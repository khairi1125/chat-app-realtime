<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Halaman utama chat — load semua conversations user
    public function index()
{
    $conversations = Auth::user()
        ->conversations()
        ->with(['latestMessage.sender', 'users'])
        ->get()
        ->map(function ($conv) {
            // Hitung unread messages
            $lastRead = $conv->pivot->last_read_at;
            $conv->unread_count = $conv->messages()
                ->where('user_id', '!=', Auth::id())
                ->when($lastRead, fn($q) => $q->where('created_at', '>', $lastRead))
                ->count();
            return $conv;
        })
        ->sortByDesc(fn($conv) => $conv->latestMessage?->created_at)
        ->values();

    $users = \App\Models\User::where('id', '!=', Auth::id())->get();

    return view('chat.index', compact('conversations', 'users'));
}

    // Buka conversation & load messages
    public function show(Conversation $conversation)
{
    abort_if(
        !$conversation->users->contains(Auth::id()),
        403,
        'Kamu bukan anggota conversation ini.'
    );

    $messages = $conversation->messages()
        ->with('sender')
        ->orderBy('created_at', 'asc')
        ->get();

    $conversations = Auth::user()
        ->conversations()
        ->with(['latestMessage.sender', 'users'])
        ->get()
        ->map(function ($conv) {
            $lastRead = $conv->pivot->last_read_at;
            $conv->unread_count = $conv->messages()
                ->where('user_id', '!=', Auth::id())
                ->when($lastRead, fn($q) => $q->where('created_at', '>', $lastRead))
                ->count();
            return $conv;
        })
        ->sortByDesc(fn($conv) => $conv->latestMessage?->created_at)
        ->values();

    $users = \App\Models\User::where('id', '!=', Auth::id())->get();

    // Update last_read_at
    $conversation->users()->updateExistingPivot(Auth::id(), [
        'last_read_at' => now(),
    ]);

    return view('chat.index', compact('conversations', 'conversation', 'messages', 'users'));
}

    // Kirim pesan baru
    public function sendMessage(Request $request, Conversation $conversation)
{
    $request->validate([
        'body' => 'required|string|max:5000',
    ]);

    abort_if(!$conversation->users->contains(Auth::id()), 403);

    $message = Message::create([
        'conversation_id' => $conversation->id,
        'user_id'         => Auth::id(),
        'body'            => $request->body,
        'type'            => 'text',
    ]);

    $message->load('sender');

    // Broadcast pesan ke semua anggota via Reverb
    broadcast(new MessageSent($message))->toOthers();

    // Broadcast notifikasi ke semua member selain pengirim
    // Broadcast notifikasi ke semua member selain pengirim
    $conversation->users
        ->where('id', '!=', Auth::id())
        ->each(function ($user) use ($message) {
            broadcast(new \App\Events\MessageReceived($message, $user));
        });

    return response()->json(['message' => $message]);
}

    // Update status online/offline user
    public function updateStatus(Request $request)
{
    $request->validate([
        'status' => 'required|in:online,offline',
    ]);

    Auth::user()->update([
        'status'       => $request->status,
        'last_seen_at' => now(),
    ]);

    broadcast(new \App\Events\UserStatusChanged(Auth::user(), $request->status));

    return response()->json(['success' => true]);
}

    // Handle user typing event
    public function typing(Request $request, Conversation $conversation)
{
    $request->validate([
        'is_typing' => 'required|boolean',
    ]);

    broadcast(new \App\Events\UserTyping(
        conversationId: $conversation->id,
        userId: Auth::id(),
        userName: Auth::user()->name,
        isTyping: $request->is_typing,
    ))->toOthers();

    return response()->json(['success' => true]);
}
}