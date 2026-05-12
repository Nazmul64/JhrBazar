@extends('admin.master')
@section('content')

<style>
    .chat-container {
        height: calc(100vh - 120px);
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin: 20px;
        border: 1px solid #f1f5f9;
    }

    /* Sidebar */
    .chat-sidebar {
        width: 350px;
        border-right: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        background: #fcfdfe;
    }
    .chat-sidebar-header {
        padding: 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    .chat-sidebar-header h5 {
        font-weight: 700;
        margin-bottom: 0;
        color: #0f172a;
    }
    .chat-list {
        flex: 1;
        overflow-y: auto;
    }
    .chat-item {
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid #f8fafc;
    }
    .chat-item:hover {
        background: #f1f5f9;
    }
    .chat-item.active {
        background: #fff1f2;
        border-left: 4px solid #e11d48;
    }
    .chat-item-img {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        background: #e2e8f0;
    }
    .chat-item-info {
        flex: 1;
        min-width: 0;
    }
    .chat-item-name {
        font-weight: 600;
        font-size: 14px;
        color: #1e293b;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .chat-item-msg {
        font-size: 12px;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .chat-item-time {
        font-size: 10px;
        color: #94a3b8;
    }

    /* Main Chat Area */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
    }
    .chat-header {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .chat-messages {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .message {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 16px;
        font-size: 14px;
        line-height: 1.5;
        position: relative;
    }
    .message-received {
        align-self: flex-start;
        background: #fff;
        color: #1e293b;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .message-sent {
        align-self: flex-end;
        background: #e11d48;
        color: #fff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2);
    }
    .message-time {
        font-size: 10px;
        margin-top: 4px;
        opacity: 0.7;
    }

    /* Input Area */
    .chat-input-area {
        padding: 20px 24px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .chat-input {
        flex: 1;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        outline: none;
        transition: all 0.2s;
    }
    .chat-input:focus {
        border-color: #e11d48;
        box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.05);
    }
    .btn-send {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: #e11d48;
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-send:hover {
        background: #be123c;
        transform: scale(1.05);
    }

    .no-chat-selected {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        gap: 16px;
    }
    .no-chat-selected i {
        font-size: 48px;
        opacity: 0.2;
    }
</style>

<div class="chat-container" id="sellerChatApp">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <h5>Messages</h5>
        </div>
        <div class="chat-list">
            @forelse($sessions as $session)
                <div class="chat-item {{ $loop->first ? '' : '' }}" onclick="loadChat({{ $session->id }}, this)">
                    <img src="{{ $session->customer->profile_image_url ?? asset('assets/admin/images/default-avatar.png') }}" class="chat-item-img">
                    <div class="chat-item-info">
                        <div class="chat-item-name">{{ $session->customer->name ?? 'Guest User' }}</div>
                        <div class="chat-item-msg">{{ $session->last_message ?? 'No messages yet' }}</div>
                    </div>
                    <div class="chat-item-time text-end">
                        {{ $session->last_message_at ? $session->last_message_at->diffForHumans(['short' => true]) : '' }}
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-muted small">No conversations found</div>
            @endforelse
        </div>
    </div>

    <!-- Main -->
    <div class="chat-main" id="chatMainContent">
        <div class="no-chat-selected">
            <i class="bi bi-chat-dots"></i>
            <p>Select a conversation to start chatting</p>
        </div>
    </div>
</div>

<script>
    let activeSessionId = null;

    function loadChat(sessionId, element) {
        // Update active state
        document.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        
        activeSessionId = sessionId;

        fetch(`/seller/messages/${sessionId}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    renderChat(data.session, data.messages);
                }
            });
    }

    function renderChat(session, messages) {
        const main = document.getElementById('chatMainContent');
        const customerName = session.customer ? session.customer.name : 'Guest User';
        const defaultAvatar = "{{ asset('assets/admin/images/default-avatar.png') }}";
        const customerImg = session.customer ? (session.customer.profile_image_url || defaultAvatar) : defaultAvatar;

        let messagesHtml = messages.map(m => {
            const isSent = m.sender_type === 'seller' || m.sender_type === 'admin';
            return `
                <div class="message ${isSent ? 'message-sent' : 'message-received'}">
                    ${m.image ? `<img src="${m.image.startsWith('http') ? m.image : '/' + m.image}" class="img-fluid rounded mb-2" style="max-height: 200px; cursor: pointer; display: block;" onclick="window.open(this.src)">` : ''}
                    ${m.message ? `<div>${m.message}</div>` : ''}
                    <div class="message-time">${new Date(m.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                </div>
            `;
        }).join('');

        main.innerHTML = `
            <div class="chat-header">
                <img src="${customerImg}" class="chat-item-img" style="width:40px; height:40px">
                <div>
                    <div class="fw-bold" style="font-size:15px">${customerName}</div>
                    <div class="text-success small" style="font-size:11px"><i class="bi bi-circle-fill" style="font-size:8px"></i> Online</div>
                </div>
            </div>
            <div class="chat-messages" id="chatMessages">
                ${messagesHtml}
            </div>
            <div id="imagePreviewContainer" style="display: none; padding: 10px 24px; background: #fff; border-top: 1px solid #f1f5f9;">
                <div class="position-relative d-inline-block">
                    <img id="imagePreview" src="" style="height: 60px; border-radius: 8px;">
                    <button onclick="clearImagePreview()" class="btn btn-danger btn-sm position-absolute" style="top: -10px; right: -10px; border-radius: 50%; padding: 2px 6px;">&times;</button>
                </div>
            </div>
            <div class="chat-input-area">
                <label for="imageUpload" class="btn btn-light m-0 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; border-radius: 12px; cursor: pointer">
                    <i class="bi bi-image" style="font-size: 20px"></i>
                    <input type="file" id="imageUpload" style="display: none" onchange="previewImage(this)" accept="image/*">
                </label>
                <input type="text" class="chat-input" id="replyInput" placeholder="Type your message..." onkeypress="handleKeyPress(event)">
                <button class="btn-send" onclick="sendReply()">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        `;
        
        scrollToBottom();
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreviewContainer').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImagePreview() {
        document.getElementById('imageUpload').value = '';
        document.getElementById('imagePreviewContainer').style.display = 'none';
    }

    function handleKeyPress(e) {
        if(e.key === 'Enter') sendReply();
    }

    function sendReply() {
        const input = document.getElementById('replyInput');
        const imageInput = document.getElementById('imageUpload');
        const message = input.value.trim();
        const hasImage = imageInput.files && imageInput.files[0];

        if(!message && !hasImage) return;
        if(!activeSessionId) return;

        const formData = new FormData();
        formData.append('message', message);
        if(hasImage) formData.append('image', imageInput.files[0]);

        // Clear inputs immediately
        input.value = '';
        clearImagePreview();

        fetch(`/seller/messages/${activeSessionId}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                const chatMessages = document.getElementById('chatMessages');
                const m = data.message;
                const msgDiv = document.createElement('div');
                msgDiv.className = 'message message-sent';
                msgDiv.innerHTML = `
                    ${m.image ? `<img src="${m.image}" class="img-fluid rounded mb-2" style="max-height: 200px; cursor: pointer; display: block;" onclick="window.open(this.src)">` : ''}
                    ${m.message ? `<div>${m.message}</div>` : ''}
                    <div class="message-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                `;
                chatMessages.appendChild(msgDiv);
                scrollToBottom();
                
                // Update sidebar last message
                const activeItem = document.querySelector('.chat-item.active .chat-item-msg');
                if(activeItem) activeItem.textContent = m.message || 'Image Attachment';
            }
        });
    }

    function scrollToBottom() {
        const chatMessages = document.getElementById('chatMessages');
        if(chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
    }
</script>

@endsection
