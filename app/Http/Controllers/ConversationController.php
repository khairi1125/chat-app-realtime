<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function createPrivate(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id|different:' . Auth::id(),
    ]);

    $existing = Auth::user()->conversations()
        ->where('type', 'private')
        ->whereHas('users', function ($q) use ($request) {
            $q->where('users.id', $request->user_id);
        })
        ->first();

    if ($existing) {
        return redirect()->route('chat.show', $existing);
    }

    $conversation = Conversation::create([
        'type'       => 'private',
        'created_by' => Auth::id(),
    ]);

    $conversation->users()->attach([
        Auth::id()        => ['role' => 'admin'],
        $request->user_id => ['role' => 'member'],
    ]);

    // Broadcast ke user yang diajak chat
    $recipient = \App\Models\User::find($request->user_id);
    broadcast(new \App\Events\ConversationCreated($conversation, $recipient));

    return redirect()->route('chat.show', $conversation);
}

    public function createGroup(Request $request)
{
    $request->validate([
        'name'       => 'required|string|max:100',
        'user_ids'   => 'required|array|min:1',
        'user_ids.*' => 'exists:users,id',
    ]);

    $conversation = Conversation::create([
        'type'       => 'group',
        'name'       => $request->name,
        'created_by' => Auth::id(),
    ]);

    $members = [Auth::id() => ['role' => 'admin']];
    foreach ($request->user_ids as $userId) {
        $members[$userId] = ['role' => 'member'];
    }

    $conversation->users()->attach($members);

    // Broadcast ke semua member grup
    foreach ($request->user_ids as $userId) {
        $recipient = \App\Models\User::find($userId);
        broadcast(new \App\Events\ConversationCreated($conversation, $recipient));
    }

    return redirect()->route('chat.show', $conversation);
}
}