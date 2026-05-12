@extends('admin.master')
@section('title', 'Conversations')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Chat List Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 text-primary"><i class="bi bi-chat-left-dots"></i> Conversations</h5>
                </div>
                <div class="card-body p-0" style="max-height: 70vh; overflow-y: auto;">
                    <div class="list-group list-group-flush" id="chat-list">
                        @forelse($sessions as $session)
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center p-3 chat-session-item {{ !$session->is_read_by_admin ? 'fw-bold bg-light' : '' }}" data-session-id="{{ $session->id }}">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-size: 20px; overflow: hidden;">
                                    @if($session->user && $session->user->profile_image)
                                        <img src="{{ asset('uploads/profile_images/' . $session->user->profile_image) }}" alt="User Image" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <i class="bi bi-person"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 text-truncate">
                                            @if($session->user)
                                                {{ $session->user->name }}
                                            @else
                                                Guest User
                                            @endif
                                        </h6>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($session->last_message_at)->shortRelativeDiffForHumans() }}</small>
                                    </div>
                                </div>
                                @if(!$session->is_read_by_admin)
                                    <span class="badge bg-danger rounded-pill ms-2">New</span>
                                @endif
                            </a>
                        @empty
                            <div class="text-center p-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No conversations found.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Window Area -->
        <div class="col-md-8">
            <div class="card shadow-sm h-100" id="chat-window-container" style="display: none;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" id="chat-header-title">Select a conversation</h5>
                </div>
                <div class="card-body" id="chat-messages" style="height: 60vh; overflow-y: auto; background-color: #f8f9fa;">
                    <!-- Messages will be loaded here via AJAX -->
                </div>
                <div class="card-footer bg-white border-top p-3">
                    <form id="reply-form" class="d-flex align-items-center gap-2" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="current_session_id" name="session_id">
                        
                        <!-- Image Upload Button -->
                        <div class="position-relative">
                            <input type="file" id="reply-image" name="image" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-light rounded-circle shadow-sm border" onclick="document.getElementById('reply-image').click()" style="width: 45px; height: 45px;">
                                <i class="bi bi-paperclip fs-5 text-secondary"></i>
                            </button>
                            <span id="image-name-preview" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" style="display: none; font-size: 0.6rem;">
                                1
                            </span>
                        </div>

                        <input type="text" id="reply-message" class="form-control rounded-pill px-4 shadow-sm border-0 bg-light" placeholder="Type a message..." style="height: 45px;">
                        
                        <button type="submit" class="btn btn-primary rounded-circle shadow-sm d-flex justify-content-center align-items-center" style="width: 45px; height: 45px; flex-shrink: 0; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border: none;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm h-100 d-flex justify-content-center align-items-center text-muted" id="chat-placeholder">
                <div class="text-center p-5">
                    <div class="rounded-circle bg-light d-inline-flex justify-content-center align-items-center mb-4" style="width: 100px; height: 100px;">
                        <i class="bi bi-chat-dots fs-1 text-primary"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Select a conversation</h4>
                    <p class="text-muted">Click on any user on the left to start chatting.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .chat-bubble {
        max-width: 75%;
        padding: 12px 18px;
        border-radius: 20px;
        margin-bottom: 5px;
        word-break: break-word;
    }
    .chat-bubble.admin {
        background-color: #e3f2fd;
        border-bottom-right-radius: 5px;
    }
    .chat-bubble.user {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-bottom-left-radius: 5px;
    }
    .chat-message-container {
        margin-bottom: 15px;
    }
    .chat-time {
        font-size: 11px;
        color: #6c757d;
        margin-top: 3px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatItems = document.querySelectorAll('.chat-session-item');
        const chatWindowContainer = document.getElementById('chat-window-container');
        const chatPlaceholder = document.getElementById('chat-placeholder');
        const chatMessages = document.getElementById('chat-messages');
        const replyForm = document.getElementById('reply-form');
        const currentSessionInput = document.getElementById('current_session_id');
        const chatHeaderTitle = document.getElementById('chat-header-title');
        
        let pollingInterval = null;
        let lastMessageCount = 0;
        let activeSessionId = null;
        
        // Notification Sound
        const notificationSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

        // ── Auto-refresh session list every 10 seconds ──
        function refreshSessionList() {
            fetch('/admin/chat/sessions')
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    const chatList = document.getElementById('chat-list');
                    chatList.innerHTML = '';

                    data.data.forEach(session => {
                        const isActive = String(session.id) === String(activeSessionId);
                        const isUnread = !session.is_read_by_admin;
                        
                        const avatarHtml = session.profile_image
                            ? `<img src="${session.profile_image}" style="width:100%;height:100%;object-fit:cover;" alt="">`
                            : `<i class="bi bi-person"></i>`;

                        const badgeHtml = isUnread && !isActive
                            ? `<span class="badge bg-danger rounded-pill ms-2">New</span>` : '';

                        const li = document.createElement('a');
                        li.href = '#';
                        li.className = `list-group-item list-group-item-action d-flex align-items-center p-3 chat-session-item${isActive ? ' active text-white' : (isUnread ? ' fw-bold bg-light' : '')}`;
                        li.dataset.sessionId = session.id;
                        li.innerHTML = `
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;font-size:20px;overflow:hidden;">
                                ${avatarHtml}
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 text-truncate">${session.name}</h6>
                                    <small class="text-muted">${session.time_ago}</small>
                                </div>
                            </div>
                            ${badgeHtml}
                        `;
                        li.addEventListener('click', handleSessionClick);
                        chatList.appendChild(li);
                        
                        // If this is the active session, update the header too
                        if (isActive && session.name !== 'Guest User') {
                            chatHeaderTitle.innerText = `Chatting with: ${session.name}`;
                        }
                    });
                });
        }
        setInterval(refreshSessionList, 10000); // Refresh every 10 seconds
        
        // Image preview logic
        const replyImageInput = document.getElementById('reply-image');
        const imageNamePreview = document.getElementById('image-name-preview');
        
        replyImageInput.addEventListener('change', function() {
            if(this.files && this.files.length > 0) {
                imageNamePreview.style.display = 'block';
                // Remove required from text if image is selected
                document.getElementById('reply-message').removeAttribute('required');
            } else {
                imageNamePreview.style.display = 'none';
                document.getElementById('reply-message').setAttribute('required', 'required');
            }
        });

        function loadMessages(sessionId, isPolling = false) {
            fetch(`/admin/chat/${sessionId}/messages`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        chatMessages.innerHTML = '';
                        
                        // Play sound if new messages arrived
                        if(isPolling && data.data.length > lastMessageCount) {
                            // Check if the last message is from a user
                            const lastMsg = data.data[data.data.length - 1];
                            if(lastMsg && lastMsg.sender_type === 'user') {
                                notificationSound.play().catch(e => console.log('Audio play blocked'));
                            }
                        }
                        lastMessageCount = data.data.length;

                        data.data.forEach(msg => {
                            const isUser = msg.sender_type === 'user';
                            
                            let html = `
                                <div class="chat-message-container d-flex flex-column ${isUser ? 'align-items-start' : 'align-items-end'}">
                                    <div class="chat-bubble ${isUser ? 'user shadow-sm' : 'admin text-end'}">
                            `;
                            
                            if (msg.image) {
                                html += `<div class="mb-2"><a href="${msg.image}" target="_blank"><img src="${msg.image}" class="img-fluid rounded" style="max-height: 200px;"></a></div>`;
                            }
                            
                            if (msg.message) {
                                html += `<div>${msg.message}</div>`;
                            }
                            
                            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                            
                            html += `
                                    </div>
                                    <div class="chat-time px-2">${time}</div>
                                </div>
                            `;
                            chatMessages.innerHTML += html;
                        });
                        
                        // Scroll to bottom
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                })
                .catch(error => console.error('Error loading messages:', error));
        }

        function handleSessionClick(e) {
            e.preventDefault();

            // Update UI active state
            document.querySelectorAll('.chat-session-item').forEach(i => i.classList.remove('active', 'text-white'));
            this.classList.add('active', 'text-white');

            // Remove new badge
            const badge = this.querySelector('.badge');
            if(badge) badge.remove();
            this.classList.remove('fw-bold', 'bg-light');

            const sessionId = this.dataset.sessionId;
            const userName = this.querySelector('h6').innerText;

            activeSessionId = sessionId; // track which is open
            currentSessionInput.value = sessionId;
            chatHeaderTitle.innerText = `Chatting with: ${userName}`;

            chatPlaceholder.classList.remove('d-flex');
            chatPlaceholder.classList.add('d-none');

            chatWindowContainer.style.display = 'flex';
            chatWindowContainer.style.flexDirection = 'column';

            loadMessages(sessionId);

            // Setup polling
            if(pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(() => loadMessages(sessionId, true), 3000);
        }

        // Bind click to initial server-rendered items
        document.querySelectorAll('.chat-session-item').forEach(item => {
            item.addEventListener('click', handleSessionClick);
        });

        replyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const sessionId = currentSessionInput.value;
            const messageInput = document.getElementById('reply-message');
            const message = messageInput.value.trim();
            const imageFile = replyImageInput.files[0];
            const token = document.querySelector('input[name="_token"]').value;
            
            if(!message && !imageFile) return;
            
            // Optimistic UI update
            messageInput.disabled = true;
            
            const formData = new FormData();
            formData.append('message', message);
            if(imageFile) {
                formData.append('image', imageFile);
            }
            
            fetch(`/admin/chat/${sessionId}/reply`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    messageInput.value = '';
                    replyImageInput.value = '';
                    imageNamePreview.style.display = 'none';
                    document.getElementById('reply-message').setAttribute('required', 'required');
                    loadMessages(sessionId);
                }
            })
            .catch(error => console.error('Error sending reply:', error))
            .finally(() => {
                messageInput.disabled = false;
                messageInput.focus();
            });
        });
    });
</script>
@endsection
