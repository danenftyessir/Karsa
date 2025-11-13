{{-- resources/views/student/chatbot/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Chat dengan KARA - ' . $conversation->title)

@push('styles')
<style>
    .chat-container {
        height: calc(100vh - 200px);
        max-height: 800px;
    }

    .messages-container {
        height: calc(100% - 80px);
        overflow-y: auto;
        scroll-behavior: smooth;
    }

    .message-bubble {
        max-width: 80%;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user-message {
        background: #2d2d2d;
        color: white;
        border-radius: 1rem 1rem 0.25rem 1rem;
    }

    .assistant-message {
        background: #f7f7f8;
        color: #1f2937;
        border-radius: 1rem 1rem 1rem 0.25rem;
    }

    .typing-indicator {
        display: none;
    }

    .typing-indicator.active {
        display: flex;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #9ca3af;
        animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
        }
        30% {
            transform: translateY(-10px);
        }
    }

    .sidebar-conversation {
        transition: all 0.2s ease;
    }

    .sidebar-conversation:hover {
        background: #f9fafb;
    }

    .sidebar-conversation.active {
        background: #f3f4f6;
        border-left: 3px solid #374151;
    }

    .source-badge {
        transition: all 0.2s ease;
    }

    .source-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Sidebar slide */
    .sidebar-panel {
        position: fixed;
        left: -100%;
        top: 0;
        height: 100vh;
        width: 320px;
        background: white;
        z-index: 1000;
        transition: left 0.3s ease;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar-panel.open {
        left: 0;
    }

    .sidebar-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .sidebar-overlay.open {
        opacity: 1;
        pointer-events: auto;
    }

    /* Custom scrollbar */
    .messages-container::-webkit-scrollbar {
        width: 6px;
    }

    .messages-container::-webkit-scrollbar-track {
        background: #f9fafb;
        border-radius: 10px;
    }

    .messages-container::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }

    .messages-container::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen py-8" style="background-color: #ffffff; background-image: radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%), radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%), radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%), radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%), radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);">
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar Panel -->
    <div class="sidebar-panel" id="sidebarPanel">
        <div class="h-full flex flex-col">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Percakapan</h3>
                <button onclick="toggleSidebar()" class="p-1 hover:bg-gray-200 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex-1 divide-y divide-gray-200 overflow-y-auto">
                @foreach($conversations as $conv)
                <a href="{{ route('student.chatbot.show', $conv->id) }}"
                   class="sidebar-conversation block p-3 {{ $conv->id == $conversation->id ? 'active' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ Str::limit($conv->title, 30) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $conv->message_count }} pesan
                            </p>
                        </div>
                        @if($conv->id == $conversation->id)
                        <svg class="w-4 h-4 text-gray-700 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            <div class="p-3 border-t border-gray-200">
                <a href="{{ route('student.chatbot.create') }}" class="block w-full text-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors">
                    + Percakapan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 chat-container">
            <!-- Chat Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- Hamburger Menu Button -->
                        <button onclick="toggleSidebar()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ $conversation->title }}</h2>
                            <p class="text-sm text-gray-500">KARA - Karsa Artificial Response Assistant</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="archiveConversation({{ $conversation->id }})" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                        </button>
                        <button onclick="deleteConversation({{ $conversation->id }})" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="messages-container p-6 bg-white" id="messagesContainer">
                @forelse($conversation->messages as $message)
                <div class="mb-4 flex {{ $message->isUser() ? 'justify-end' : 'justify-start' }}">
                    <div class="message-bubble {{ $message->isUser() ? 'user-message' : 'assistant-message' }} px-4 py-3 shadow-sm">
                        <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>

                        @if($message->isAssistant() && $message->documentReferences->count() > 0)
                        <div class="mt-3 pt-3 border-t {{ $message->isUser() ? 'border-white/20' : 'border-gray-300' }}">
                            <p class="text-xs {{ $message->isUser() ? 'text-white/80' : 'text-gray-500' }} font-semibold mb-2">Sumber:</p>
                            <div class="space-y-1">
                                @foreach($message->documentReferences->take(3) as $ref)
                                <div class="source-badge text-xs">
                                    <a href="{{ route('student.repository.show', $ref->document->id) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 hover:underline {{ $message->isUser() ? 'text-white/90 hover:text-white' : 'text-gray-700 hover:text-gray-900' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $ref->document->title }}
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <p class="text-xs {{ $message->isUser() ? 'text-white/70' : 'text-gray-500' }} mt-2">
                            {{ $message->created_at->format('H:i') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Mulai Percakapan dengan KARA</h3>
                    <p class="text-gray-600">Tanyakan apapun tentang proyek KKN dan dokumentasi Karsa</p>
                </div>
                @endforelse

                <!-- Typing Indicator -->
                <div class="typing-indicator mb-4 flex justify-start" id="typingIndicator">
                    <div class="assistant-message px-4 py-3 shadow-sm flex items-center space-x-2">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="px-6 py-4 border-t border-gray-200 bg-white">
                <form id="chatForm" class="flex items-start space-x-3">
                    @csrf
                    <div class="flex-1">
                        <textarea
                            id="messageInput"
                            name="message"
                            rows="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:border-transparent resize-none"
                            placeholder="Ketik pesan Anda di sini..."
                            maxlength="5000"
                            required
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            <span id="charCount">0</span>/5000 karakter
                        </p>
                    </div>
                    <button
                        type="submit"
                        id="sendButton"
                        class="px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                    >
                        <span>Kirim</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebarPanel');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
}

document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('messageInput');
    const charCount = document.getElementById('charCount');
    const chatForm = document.getElementById('chatForm');
    const messagesContainer = document.getElementById('messagesContainer');
    const typingIndicator = document.getElementById('typingIndicator');
    const sendButton = document.getElementById('sendButton');

    // Character counter
    messageInput.addEventListener('input', function() {
        charCount.textContent = this.value.length;
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 150) + 'px';
    });

    // Handle Enter key (Shift+Enter for new line)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    // Scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Initial scroll
    scrollToBottom();

    // Handle form submission
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        // Disable input
        messageInput.disabled = true;
        sendButton.disabled = true;

        // Add user message to UI
        addMessageToUI('user', message);
        messageInput.value = '';
        charCount.textContent = '0';
        messageInput.style.height = 'auto';

        // Show typing indicator
        typingIndicator.classList.add('active');
        scrollToBottom();

        try {
            // Send message to API
            const response = await fetch('{{ route("student.chatbot.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    conversation_id: {{ $conversation->id }},
                    message: message
                })
            });

            const data = await response.json();

            // Hide typing indicator
            typingIndicator.classList.remove('active');

            if (data.success) {
                // Add assistant response
                addMessageToUI('assistant', data.message, data.sources);
            } else {
                // Show error message
                addMessageToUI('assistant', data.message || 'Maaf, terjadi kesalahan. Silakan coba lagi.');
            }

        } catch (error) {
            console.error('Error:', error);
            typingIndicator.classList.remove('active');
            addMessageToUI('assistant', 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi.');
        } finally {
            // Re-enable input
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        }
    });

    // Add message to UI
    function addMessageToUI(role, content, sources = []) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-4 flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;

        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = `message-bubble ${role === 'user' ? 'user-message' : 'assistant-message'} px-4 py-3 shadow-sm`;

        const contentP = document.createElement('p');
        contentP.className = 'text-sm whitespace-pre-wrap';
        contentP.textContent = content;
        bubbleDiv.appendChild(contentP);

        // Add sources if assistant message
        if (role === 'assistant' && sources && sources.length > 0) {
            const sourcesDiv = document.createElement('div');
            sourcesDiv.className = 'mt-3 pt-3 border-t border-gray-300';
            sourcesDiv.innerHTML = '<p class="text-xs text-gray-500 font-semibold mb-2">Sumber:</p>';

            const sourcesList = document.createElement('div');
            sourcesList.className = 'space-y-1';
            sources.forEach(source => {
                const sourceItem = document.createElement('div');
                sourceItem.className = 'source-badge text-xs';

                const link = document.createElement('a');
                link.href = `/student/repository/${source.id}`;
                link.target = '_blank';
                link.className = 'inline-flex items-center gap-1 hover:underline text-gray-700 hover:text-gray-900';

                const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                icon.setAttribute('class', 'w-3 h-3');
                icon.setAttribute('fill', 'none');
                icon.setAttribute('stroke', 'currentColor');
                icon.setAttribute('viewBox', '0 0 24 24');
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('stroke-linecap', 'round');
                path.setAttribute('stroke-linejoin', 'round');
                path.setAttribute('stroke-width', '2');
                path.setAttribute('d', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z');
                icon.appendChild(path);

                link.appendChild(icon);
                link.appendChild(document.createTextNode(source.title));
                sourceItem.appendChild(link);
                sourcesList.appendChild(sourceItem);
            });
            sourcesDiv.appendChild(sourcesList);
            bubbleDiv.appendChild(sourcesDiv);
        }

        // Add timestamp
        const timeP = document.createElement('p');
        timeP.className = `text-xs ${role === 'user' ? 'text-white/70' : 'text-gray-500'} mt-2`;
        timeP.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        bubbleDiv.appendChild(timeP);

        messageDiv.appendChild(bubbleDiv);
        messagesContainer.insertBefore(messageDiv, typingIndicator);
        scrollToBottom();
    }
});

// Archive conversation
function archiveConversation(id) {
    if (!confirm('Arsipkan percakapan ini?')) return;

    fetch(`/student/chatbot/${id}/archive`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("student.chatbot.index") }}';
        } else {
            alert(data.message || 'Gagal mengarsipkan percakapan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

// Delete conversation
function deleteConversation(id) {
    if (!confirm('Hapus percakapan ini? Tindakan ini tidak dapat dibatalkan.')) return;

    fetch(`/student/chatbot/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("student.chatbot.index") }}';
        } else {
            alert(data.message || 'Gagal menghapus percakapan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}
</script>
@endpush
