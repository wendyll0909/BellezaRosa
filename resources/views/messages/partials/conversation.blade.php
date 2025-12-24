@php
    $currentUser = auth()->user();
    $otherUser = $user;
@endphp

<!-- Conversation Header -->
<div class="border-b border-gray-200 p-4 bg-white">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-user text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">{{ $otherUser->full_name }}</h3>
                <p class="text-sm text-gray-500">
                    {{ $otherUser->isStaff() ? 'Staff' : ($otherUser->isAdmin() ? 'Admin' : 'Customer') }}
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            @if($appointments->count() > 0)
            <div class="relative">
                <button onclick="toggleAppointmentDropdown()" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="ml-1">Related Appointments</span>
                </button>
                <div id="appointmentDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                    <div class="p-3">
                        <h4 class="font-semibold text-gray-900 mb-2">Appointments</h4>
                        @foreach($appointments as $appointment)
                        <div class="mb-2 p-2 border border-gray-100 rounded hover:bg-gray-50">
                            <div class="text-sm font-medium">{{ $appointment->service->name }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $appointment->start_datetime->format('M j, g:i A') }}
                            </div>
                            <div class="text-xs">
                                <span class="font-medium
                                    {{ $appointment->status == 'scheduled' ? 'text-blue-600' : '' }}
                                    {{ $appointment->status == 'confirmed' ? 'text-green-600' : '' }}
                                    {{ $appointment->status == 'in_progress' ? 'text-yellow-600' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Messages Container -->
<div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
    @foreach($messages as $message)
        <div class="flex {{ $message->sender_id == $currentUser->id ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs lg:max-w-md">
                <div class="rounded-2xl px-4 py-2 {{ $message->sender_id == $currentUser->id ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200 text-gray-900' }}">
                    <p class="text-sm">{{ $message->message }}</p>
                </div>
                <div class="text-xs text-gray-500 mt-1 {{ $message->sender_id == $currentUser->id ? 'text-right' : '' }}">
                    {{ $message->created_at->format('g:i A') }}
                    @if($message->sender_id == $currentUser->id)
                        @if($message->is_read)
                            <i class="fas fa-check-double ml-1 text-blue-500"></i>
                        @else
                            <i class="fas fa-check ml-1 text-gray-400"></i>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Message Input -->
<div class="border-t border-gray-200 p-4 bg-white">
    <form id="messageForm" class="flex space-x-2">
        @csrf
        <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
        
        <div class="flex-1 relative">
            <input type="text" 
                   name="message" 
                   id="messageInput"
                   placeholder="Type your message..." 
                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                   required>
            <button type="button" onclick="quickResponse('Thank you!')" 
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600">
                <i class="fas fa-smile"></i>
            </button>
        </div>
        
        <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition font-medium">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
    
    <!-- Quick Responses -->
    <div class="mt-2 flex flex-wrap gap-2">
        <button onclick="quickResponse('Can I reschedule my appointment?')" 
                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg transition">
            Can I reschedule?
        </button>
        <button onclick="quickResponse('What are your business hours?')" 
                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg transition">
            Business hours?
        </button>
        <button onclick="quickResponse('Do you have any promotions?')" 
                class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg transition">
            Any promotions?
        </button>
    </div>
</div>

<script>
// Auto-scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

// Send message via AJAX
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    fetch('{{ route("messages.send", $otherUser) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Append new message
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.insertAdjacentHTML('beforeend', data.html);
            
            // Clear input
            messageInput.value = '';
            
            // Scroll to bottom
            scrollToBottom();
            
            // Update notification badge
            updateNotificationBadge();
        }
    });
});

// Quick response
function quickResponse(text) {
    document.getElementById('messageInput').value = text;
    document.getElementById('messageInput').focus();
}

// Toggle appointment dropdown
function toggleAppointmentDropdown() {
    const dropdown = document.getElementById('appointmentDropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('appointmentDropdown');
    if (dropdown && !dropdown.contains(event.target) && !event.target.closest('button[onclick="toggleAppointmentDropdown()"]')) {
        dropdown.classList.add('hidden');
    }
});

// Scroll to bottom on load
scrollToBottom();

// Auto-refresh messages every 10 seconds
setInterval(() => {
    fetch('{{ route("messages.refresh", $otherUser) }}')
        .then(response => response.text())
        .then(html => {
            const messagesContainer = document.getElementById('messagesContainer');
            const oldHeight = messagesContainer.scrollHeight;
            messagesContainer.innerHTML = html;
            const newHeight = messagesContainer.scrollHeight;
            
            // If user was at bottom, stay at bottom
            if (oldHeight - messagesContainer.scrollTop < 100) {
                scrollToBottom();
            }
        });
}, 10000);
</script>