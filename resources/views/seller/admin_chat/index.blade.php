@extends('admin.master')
@section('title', 'Chat with Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-size: 20px;">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Admin Support</h5>
                            <small class="text-success"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Online</small>
                        </div>
                    </div>
                </div>

                <div class="card-body" id="chat-messages" style="height: 65vh; overflow-y: auto; background-color: #f0f2f5; padding: 20px;">
                    <!-- Messages will be loaded here via AJAX -->
                </div>

                <div class="card-footer bg-white border-top p-3">
                    <form id="reply-form" class="d-flex align-items-center gap-2" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="position-relative">
                            <input type="file" id="reply-image" name="image" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-light rounded-circle border shadow-sm" onclick="document.getElementById('reply-image').click()" style="width: 45px; height: 45px;">
                                <i class="bi bi-image text-primary"></i>
                            </button>
                            <span id="image-name-preview" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.6rem;">
                                1
                            </span>
                        </div>

                        <input type="text" id="reply-message" class="form-control rounded-pill px-4 border-0 bg-light" placeholder="Describe your issue or ask a question..." style="height: 45px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                        
                        <button type="submit" class="btn btn-primary rounded-circle shadow-sm d-flex justify-content-center align-items-center" style="width: 45px; height: 45px; flex-shrink: 0; background: linear-gradient(135deg, #e7567c 0%, #c93f65 100%); border: none;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #chat-messages::-webkit-scrollbar { width: 6px; }
    #chat-messages::-webkit-scrollbar-thumb { background: #ccd0d5; border-radius: 10px; }
    
    .chat-bubble {
        max-width: 80%;
        padding: 10px 16px;
        border-radius: 18px;
        margin-bottom: 4px;
        font-size: 14px;
        line-height: 1.4;
        position: relative;
    }
    
    .chat-bubble.admin {
        background-color: #ffffff;
        color: #1c1e21;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .chat-bubble.user {
        background: linear-gradient(135deg, #e7567c 0%, #c93f65 100%);
        color: #ffffff;
        border-bottom-right-radius: 4px;
    }

    .chat-message-container {
        margin-bottom: 12px;
        display: flex;
        flex-direction: column;
    }
    
    .chat-time {
        font-size: 11px;
        color: #65676b;
        margin-top: 2px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chat-messages');
        const replyForm = document.getElementById('reply-form');
        const sessionId = '{{ $session->id }}';
        
        let lastMessageCount = 0;
        const notificationSound = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

        function loadMessages(isPolling = false) {
            fetch(`/seller/admin-chat/${sessionId}/messages`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if(isPolling && data.data.length === lastMessageCount) return;

                        chatMessages.innerHTML = '';
                        
                        if(isPolling && data.data.length > lastMessageCount) {
                            const lastMsg = data.data[data.data.length - 1];
                            if(lastMsg && lastMsg.sender_type === 'admin') {
                                notificationSound.play().catch(e => {});
                            }
                        }
                        lastMessageCount = data.data.length;

                        data.data.forEach(msg => {
                            const isMe = msg.sender_type === 'user';
                            
                            let html = `
                                <div class="chat-message-container ${isMe ? 'align-items-end' : 'align-items-start'}">
                                    <div class="chat-bubble ${isMe ? 'user' : 'admin'}">
                            `;
                            
                            if (msg.image) {
                                html += `<div class="mb-2"><a href="${msg.image}" target="_blank"><img src="${msg.image}" class="img-fluid rounded" style="max-height: 250px;"></a></div>`;
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
                        
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                });
        }

        loadMessages();
        setInterval(() => loadMessages(true), 3000);

        const replyImageInput = document.getElementById('reply-image');
        const imageNamePreview = document.getElementById('image-name-preview');
        
        replyImageInput.addEventListener('change', function() {
            imageNamePreview.style.display = (this.files && this.files.length > 0) ? 'block' : 'none';
        });

        replyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageInput = document.getElementById('reply-message');
            const message = messageInput.value.trim();
            const imageFile = replyImageInput.files[0];
            
            if(!message && !imageFile) return;
            
            messageInput.disabled = true;
            const formData = new FormData();
            formData.append('message', message);
            if(imageFile) formData.append('image', imageFile);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch(`/seller/admin-chat/${sessionId}/reply`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if(data.success) {
                    messageInput.value = '';
                    replyImageInput.value = '';
                    imageNamePreview.style.display = 'none';
                    loadMessages();
                }
            })
            .finally(() => {
                messageInput.disabled = false;
                messageInput.focus();
            });
        });
    });
</script>
@endsection
