@extends('layouts.guest')

@section('title', 'Messages - Belleza Rosa')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-900 to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-white">Messages</h2>
                    <div class="flex items-center space-x-4">
                        <button onclick="markAllAsRead()" class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-1 rounded-lg transition">
                            <i class="fas fa-check-double mr-1"></i> Mark All as Read
                        </button>
                        <a href="{{ route('home') }}" class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-1 rounded-lg transition">
                            <i class="fas fa-home mr-1"></i> Home
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex h-[calc(100vh-200px)]">
                <!-- Conversations List -->
                <div class="w-1/3 border-r border-gray-200 overflow-y-auto">
                    @if($conversations->count() > 0)
                        @foreach($conversations as $conversation)
                            <a href="{{ route('messages.conversation', $conversation['user']) }}"
                               class="flex items-center p-4 border-b border-gray-100 hover:bg-blue-50 transition {{ request()->route('user') && request()->route('user')->id == $conversation['user']->id ? 'bg-blue-50' : '' }}">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-xl"></i>
                                    </div>
                                    @if($conversation['unread_count'] > 0)
                                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
                                            {{ $conversation['unread_count'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-semibold text-gray-900">{{ $conversation['user']->full_name }}</h3>
                                        <span class="text-xs text-gray-500">
                                            {{ $conversation['last_message_time'] ? $conversation['last_message_time']->diffForHumans() : '' }}
                                        </span>
                                    </div>
                                    @if($conversation['last_message'])
                                        <p class="text-sm text-gray-600 truncate">
                                            {{ $conversation['last_message']->sender_id == auth()->id() ? 'You: ' : '' }}
                                            {{ $conversation['last_message']->message }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-400">No messages yet</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="p-8 text-center">
                            <i class="fas fa-comments text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No conversations yet</p>
                            <p class="text-sm text-gray-400 mt-1">Start a conversation with our staff!</p>
                        </div>
                    @endif
                </div>

                <!-- Messages Area -->
                <div class="w-2/3 flex flex-col">
                    @if(request()->route('user'))
                        @include('messages.partials.conversation')
                    @else
                        <div class="flex-1 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-comment-alt text-6xl text-gray-200 mb-4"></i>
                                <h3 class="text-xl font-semibold text-gray-400">Select a conversation</h3>
                                <p class="text-gray-500 mt-2">Choose a conversation from the list to start messaging</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Modal for Mobile/Quick Chat -->
<div id="chatModal" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">Quick Chat</h2>
                <button onclick="closeChatModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="chatModalContent">
            <!-- Content will be loaded via AJAX -->
        </div>
    </div>
</div>

<script>
// Mark all as read
function markAllAsRead() {
    fetch('{{ route("messages.markAllRead") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update notification badge
            updateNotificationBadge(0, 0);
            // Reload page to reflect changes
            location.reload();
        }
    });
}

// Open chat modal
function openChatModal(userId) {
    fetch(`/messages/quick-chat/${userId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('chatModalContent').innerHTML = html;
            document.getElementById('chatModal').classList.remove('hidden');
        });
}

function closeChatModal() {
    document.getElementById('chatModal').classList.add('hidden');
}

// Auto-refresh unread count every 30 seconds
setInterval(() => {
    fetch('{{ route("messages.unreadCount") }}')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.unread_messages, data.unread_notifications);
        });
}, 30000);

function updateNotificationBadge(messages, notifications) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        const total = messages + notifications;
        if (total > 0) {
            badge.textContent = total > 9 ? '9+' : total;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}
</script>
@endsection