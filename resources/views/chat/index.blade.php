<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

<div class="w-full max-w-6xl h-[90vh] bg-white rounded-2xl shadow-lg flex overflow-hidden">

    {{-- SIDEBAR --}}
    <div class="w-80 border-r border-gray-200 flex flex-col bg-white">

        {{-- Header Sidebar --}}
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h1 class="text-lg font-semibold text-gray-800">Chats</h1>
                <div class="flex gap-1">
                    {{-- Tombol New Group --}}
                    <button onclick="document.getElementById('modalGroup').classList.remove('hidden')"
                        class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                    </button>
                    {{-- Tombol New Private Chat --}}
                    <button onclick="document.getElementById('modalPrivate').classList.remove('hidden')"
                        class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </button>
                    {{-- Tombol Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            {{-- Search --}}
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" />
                </svg>
                <input type="text" placeholder="Cari percakapan..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-100 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 placeholder-gray-400">
            </div>
        </div>

        {{-- List Conversations --}}
        <div class="flex-1 overflow-y-auto">
            @forelse($conversations as $conv)
                @php
                    $other = $conv->type === 'private'
                        ? $conv->users->firstWhere('id', '!=', auth()->id())
                        : null;
                    $isActive = isset($conversation) && $conversation->id === $conv->id;
                    $displayName = $conv->type === 'group' ? $conv->name : ($other?->name ?? 'Unknown');
                    $initials = strtoupper(substr($displayName, 0, 1));
                    $latestMsg = $conv->latestMessage;
                @endphp
                <a href="{{ route('chat.show', $conv) }}"
    class="contact-item-link flex items-center gap-3 px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition
    {{ $isActive ? 'bg-blue-50' : '' }}">
    
    {{-- Avatar --}}
    <div class="relative flex-shrink-0">
        <div class="w-11 h-11 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-sm">
            {{ $initials }}
        </div>
        @if($conv->type === 'private' && $other?->status === 'online')
            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
        @else
            <span class="absolute bottom-0 right-0 w-3 h-3 bg-gray-400 rounded-full border-2 border-white"></span>
        @endif
    </div>

    {{-- Info --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
            <span class="font-medium text-sm text-gray-800 truncate">{{ $displayName }}</span>
            @if($conv->type === 'group')
                <span class="text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">Grup</span>
            @endif
        </div>
        <p class="preview-text text-xs text-gray-500 truncate mt-0.5">
            @if($latestMsg)
                @if($latestMsg->user_id === auth()->id()) Kamu: @endif
                {{ $latestMsg->body }}
            @else
                Belum ada pesan
            @endif
        </p>
    </div>

    {{-- Waktu --}}
    @if($latestMsg)
        <span class="preview-time text-xs text-gray-400 flex-shrink-0">
            {{ $latestMsg->created_at->format('H:i') }}
        </span>
    @endif
</a>
            @empty
                <div class="flex flex-col items-center justify-center h-full text-gray-400 p-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <p class="text-sm">Belum ada percakapan</p>
                    <p class="text-xs mt-1">Mulai chat baru dengan tombol di atas</p>
                </div>
            @endforelse
        </div>

        {{-- Info User Login --}}
        <div class="p-3 border-t border-gray-200 flex items-center gap-3 bg-gray-50">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-semibold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-green-500">Online</p>
            </div>
        </div>
    </div>

    {{-- MAIN CHAT AREA --}}
    <div class="flex-1 flex flex-col">
        @isset($conversation)
            @php
                $other = $conversation->type === 'private'
                    ? $conversation->users->firstWhere('id', '!=', auth()->id())
                    : null;
                $chatName = $conversation->type === 'group'
                    ? $conversation->name
                    : ($other?->name ?? 'Unknown');
            @endphp

            {{-- Chat Header --}}
            <div class="px-5 py-3 border-b border-gray-200 flex items-center gap-3 bg-white">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr($chatName, 0, 1)) }}
                    </div>
                    @if($conversation->type === 'private' && $other?->status === 'online')
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                    @endif
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $chatName }}</p>
                    @if($conversation->type === 'group')
                        <p class="text-xs text-gray-500">
                            {{ $conversation->users->pluck('name')->join(', ') }}
                        </p>
                    @else
                        <p class="text-xs {{ $other?->status === 'online' ? 'text-green-500' : 'text-gray-400' }}">
                            {{ $other?->status === 'online' ? 'Online' : 'Offline' }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-5 flex flex-col gap-3" id="messageContainer">
                @forelse($messages as $msg)
                    @php $isMe = $msg->user_id === auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} gap-2">
                        @if(!$isMe)
                            <div class="w-7 h-7 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0 self-end">
                                {{ strtoupper(substr($msg->sender->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="max-w-xs lg:max-w-md">
                            @if(!$isMe && $conversation->type === 'group')
                                <p class="text-xs text-blue-600 font-medium mb-1 ml-1">{{ $msg->sender->name }}</p>
                            @endif
                            <div class="{{ $isMe ? 'bg-blue-500 text-white rounded-tl-2xl' : 'bg-white text-gray-800 rounded-tr-2xl border border-gray-200' }} rounded-b-2xl px-4 py-2.5 shadow-sm">
                                <p class="text-sm leading-relaxed">{{ $msg->body }}</p>
                                <p class="text-xs mt-1 {{ $isMe ? 'text-blue-100 text-right' : 'text-gray-400 text-right' }}">
                                    {{ $msg->created_at->format('H:i') }}
                                    @if($isMe) ✓✓ @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                        <p class="text-sm">Belum ada pesan</p>
                        <p class="text-xs mt-1">Kirim pesan pertamamu!</p>
                    </div>
                @endforelse
            </div>

            {{-- Input Pesan --}}
            <div class="px-4 py-3 border-t border-gray-200 bg-white">
                {{-- Typing Indicator --}}
<div id="typingIndicator" class="hidden px-5 pb-1">
                    <div class="flex items-center gap-2">
                        <div class="flex gap-1 bg-white border border-gray-200 rounded-full px-3 py-2 shadow-sm">
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                        </div>
                        <span id="typingText" class="text-xs text-gray-400"></span>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-gray-100 rounded-full px-4 py-2">
                    <input type="text" id="messageInput" placeholder="Tulis pesan..."
                        class="flex-1 bg-transparent text-sm outline-none text-gray-800 placeholder-gray-400">
                    <button id="sendBtn"
                        class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white hover:bg-blue-600 transition flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </button>
                </div>
            </div>

        @else
            {{-- Empty State --}}
            <div class="flex-1 flex flex-col items-center justify-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                <p class="text-base font-medium">Pilih percakapan</p>
                <p class="text-sm mt-1">atau mulai chat baru</p>
            </div>
        @endisset
    </div>
</div>

{{-- MODAL NEW PRIVATE CHAT --}}
<div id="modalPrivate" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-80 shadow-xl">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Chat Baru</h2>
        <form method="POST" action="{{ route('conversations.private') }}">
            @csrf
            <select name="user_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 mb-3">
                <option value="">Pilih pengguna...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('modalPrivate').classList.add('hidden')"
                    class="flex-1 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="flex-1 py-2 rounded-lg bg-blue-500 text-white text-sm hover:bg-blue-600">Mulai Chat</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL NEW GROUP CHAT --}}
<div id="modalGroup" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-80 shadow-xl">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Buat Grup Baru</h2>
        <form method="POST" action="{{ route('conversations.group') }}">
            @csrf
            <input type="text" name="name" placeholder="Nama grup..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 mb-3">
            <p class="text-xs text-gray-500 mb-2">Pilih anggota:</p>
            <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg mb-3">
                @foreach($users as $user)
                    <label class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="rounded">
                        <span class="text-sm text-gray-700">{{ $user->name }}</span>
                    </label>
                @endforeach
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('modalGroup').classList.add('hidden')"
                    class="flex-1 py-2 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="submit"
                    class="flex-1 py-2 rounded-lg bg-blue-500 text-white text-sm hover:bg-blue-600">Buat Grup</button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript --}}
<script>
    const conversationId = {{ isset($conversation) ? $conversation->id : 'null' }};
    const currentUserId = {{ auth()->id() }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Auto scroll ke bawah
    const container = document.getElementById('messageContainer');
    if (container) container.scrollTop = container.scrollHeight;

    // Kirim pesan dengan Enter
    const input = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    function sendMessage() {
        const body = input?.value.trim();
        if (!body || !conversationId) return;

        fetch(`/chat/${conversationId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ body }),
        })
        .then(res => res.json())
        .then(data => {
            appendMessage(data.message, true);
            input.value = '';
        });
    }

    input?.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    sendBtn?.addEventListener('click', sendMessage);

    // Append pesan baru ke UI
    function appendMessage(msg, isMe) {
    if (!container) return;
    const div = document.createElement('div');
    div.className = `flex ${isMe ? 'justify-end' : 'justify-start'} gap-2`;
    div.innerHTML = `
        ${!isMe ? `<div class="w-7 h-7 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0 self-end">
            ${msg.sender.name.charAt(0).toUpperCase()}
        </div>` : ''}
        <div class="max-w-xs lg:max-w-md">
            <div class="${isMe ? 'bg-blue-500 text-white rounded-tl-2xl' : 'bg-white text-gray-800 rounded-tr-2xl border border-gray-200'} rounded-b-2xl px-4 py-2.5 shadow-sm">
                <p class="text-sm leading-relaxed">${msg.body}</p>
                <p class="text-xs mt-1 ${isMe ? 'text-blue-100 text-right' : 'text-gray-400 text-right'}">
                    ${new Date(msg.created_at).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})}
                    ${isMe ? '✓✓' : ''}
                </p>
            </div>
        </div>`;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;

    // ← Tambahkan ini: update preview pesan di sidebar
    updateSidebarPreview(msg, isMe);
}

function updateSidebarPreview(msg, isMe) {
    // Cari semua link conversation di sidebar
    const links = document.querySelectorAll('.contact-item-link');
    links.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes(`/chat/${conversationId}`)) {
            // Update preview text
            const preview = link.querySelector('.preview-text');
            if (preview) {
                preview.textContent = isMe ? `Kamu: ${msg.body}` : msg.body;
            }
            // Update waktu
            const time = link.querySelector('.preview-time');
            if (time) {
                const now = new Date(msg.created_at);
                time.textContent = now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
            }
            // Pindahkan conversation ke paling atas sidebar
            const list = link.parentElement;
            const parent = list.parentElement;
            parent.prepend(list);
        }
    });
}

    // Update status online saat halaman dibuka
    fetch('/user/status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ status: 'online' }),
    });

    // Update status offline saat halaman ditutup
    window.addEventListener('beforeunload', () => {
        navigator.sendBeacon('/user/status', JSON.stringify({ status: 'offline' }));
    });
</script>

@if(isset($conversation))
<script>
    function initEcho() {
    if (typeof window.Echo === 'undefined') {
        setTimeout(initEcho, 500);
        return;
    }

    window.Echo.join(`conversation.{{ $conversation->id }}`)
        .here(users => console.log('Online:', users))
        .joining(user => console.log(user.name, 'bergabung'))
        .leaving(user => console.log(user.name, 'keluar'))
        .listen('.message.sent', e => {
            console.log('Pesan masuk:', e);
            const msg = e.message ?? e;
            if (msg.sender.id !== currentUserId) {
                appendMessage(msg, false);
            }
        })
        // Tambahkan ini ↓
        .listen('.user.typing', e => {
            if (e.user_id !== currentUserId) {
                const indicator = document.getElementById('typingIndicator');
                const typingText = document.getElementById('typingText');
                if (e.is_typing) {
                    typingText.textContent = `${e.user_name} sedang mengetik...`;
                    indicator.classList.remove('hidden');
                    container.scrollTop = container.scrollHeight;
                } else {
                    indicator.classList.add('hidden');
                }
            }
        });
}

// Typing detection di input
let typingTimeout;
let isCurrentlyTyping = false;

function sendTypingStatus(status) {
    fetch(`/chat/${conversationId}/typing`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ is_typing: status }),
    });
}

input?.addEventListener('input', () => {
    // Hanya kirim request kalau status berubah dari false ke true
    if (!isCurrentlyTyping) {
        isCurrentlyTyping = true;
        sendTypingStatus(true);
    }

    // Reset timer setiap kali ngetik
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
        isCurrentlyTyping = false;
        sendTypingStatus(false);
    }, 1500);
});

// Langsung stop typing saat pesan dikirim
function sendMessage() {
    const body = input?.value.trim();
    if (!body || !conversationId) return;

    // Stop typing indicator dulu
    clearTimeout(typingTimeout);
    if (isCurrentlyTyping) {
        isCurrentlyTyping = false;
        sendTypingStatus(false);
    }

    fetch(`/chat/${conversationId}/messages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ body }),
    })
    .then(res => res.json())
    .then(data => {
        const msg = data.message ?? data;
        appendMessage(msg, true);
        input.value = '';
    });
}


    initEcho();

    // Listen conversation baru
if (typeof window.Echo !== 'undefined') {
    window.Echo.private(`user.${currentUserId}`)
        .listen('.conversation.created', e => {
            console.log('Conversation baru:', e);
            // Reload sidebar otomatis
            window.location.reload();
        });
} else {
    setTimeout(() => {
        window.Echo.private(`user.${currentUserId}`)
            .listen('.conversation.created', e => {
                window.location.reload();
            });
    }, 1000);
}

</script>
@endif
</body>
</html>