<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;

// Redirect halaman utama ke chat
Route::get('/', function () {
    return redirect()->route('chat.index');
});

// Routes yang butuh login
Route::middleware(['auth'])->group(function () {

    // Halaman utama chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');

    // Buka conversation tertentu
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');

    // Kirim pesan
    Route::post('/chat/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');

    // Conversation routes
    Route::post('/conversations/private', [ConversationController::class, 'createPrivate'])->name('conversations.private');
    Route::post('/conversations/group', [ConversationController::class, 'createGroup'])->name('conversations.group');

    // Update status online/offline
    Route::post('/user/status', [ChatController::class, 'updateStatus'])->name('user.status');

    Route::post('/chat/{conversation}/typing', [ChatController::class, 'typing'])->name('chat.typing');

});

require __DIR__.'/auth.php';