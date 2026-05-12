import React, { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import { useLocation } from 'react-router-dom';
import { useSettings } from '../context/SettingsContext';

const LiveChatWidget = () => {
    const { settings } = useSettings();
    const [isOpen, setIsOpen] = useState(false);
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [messages, setMessages] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [newMessage, setNewMessage] = useState('');
    const [imageFile, setImageFile] = useState(null);
    const [sessionId, setSessionId] = useState('');
    const [activeReceiver, setActiveReceiver] = useState(null); // null = Admin, {id, name} = Seller
    const messagesEndRef = useRef(null);
    const fileInputRef = useRef(null);
    const location = useLocation();

    // Initialize or get session ID based on active receiver
    useEffect(() => {
        const receiverKey = activeReceiver ? `chat_session_seller_${activeReceiver.id}` : 'chat_session_admin';
        let sid = localStorage.getItem(receiverKey);
        if (!sid) {
            sid = 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
            localStorage.setItem(receiverKey, sid);
        }
        setSessionId(sid);
        
        // If chat is open, fetch messages for the new session immediately
        if (isOpen) {
            setMessages([]); // Clear old messages while loading
            fetchMessages(sid);
        }
    }, [activeReceiver]);

    // Listen for custom event to open chat with seller
    useEffect(() => {
        const handleOpenChat = (e) => {
            const { sellerId, sellerName } = e.detail;
            setActiveReceiver({ id: sellerId, name: sellerName });
            setIsOpen(true);
            setIsMenuOpen(false);
        };

        window.addEventListener('openSellerChat', handleOpenChat);
        return () => window.removeEventListener('openSellerChat', handleOpenChat);
    }, []);

    // Link session to user when token is available (after login)
    useEffect(() => {
        if (!sessionId) return;
        const token = localStorage.getItem('auth_token');
        if (token) {
            // Call getMessages with auth header — this triggers session linking in backend
            axios.get(`/api/chat/messages?session_id=${sessionId}${activeReceiver ? `&receiver_id=${activeReceiver.id}` : ''}`, {
                headers: { Authorization: `Bearer ${token}` }
            }).catch(() => { });
        }
    }, [sessionId]);

    // Fetch messages periodically
    useEffect(() => {
        let interval;
        if (isOpen && sessionId) {
            fetchMessages();
            interval = setInterval(fetchMessages, 3000); // Poll every 3 seconds
            setUnreadCount(0); // Clear unread when open
        } else if (!isOpen && sessionId) {
            fetchUnreadCount();
            interval = setInterval(fetchUnreadCount, 5000); // Poll every 5 seconds for count
        }
        return () => {
            if (interval) clearInterval(interval);
        };
    }, [isOpen, sessionId, activeReceiver]);

    // Scroll to bottom when new messages arrive
    useEffect(() => {
        if (messagesEndRef.current) {
            messagesEndRef.current.scrollIntoView({ behavior: 'smooth' });
        }
    }, [messages]);

    const fetchMessages = async (forcedSid = null) => {
        try {
            const sid = forcedSid || sessionId;
            if (!sid) return;

            const token = localStorage.getItem('auth_token');
            const headers = token ? { Authorization: `Bearer ${token}` } : {};
            const url = `/api/chat/messages?session_id=${sid}${activeReceiver ? `&receiver_id=${activeReceiver.id}` : ''}`;
            const response = await axios.get(url, { headers });
            if (response.data.success) {
                setMessages(response.data.data);
                setUnreadCount(0); // Reset count since messages are marked as read
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    };

    const fetchUnreadCount = async () => {
        try {
            const url = `/api/chat/unread-count?session_id=${sessionId}${activeReceiver ? `&receiver_id=${activeReceiver.id}` : ''}`;
            const response = await axios.get(url);
            if (response.data.success) {
                setUnreadCount(response.data.count);
            }
        } catch (error) {
            console.error('Error fetching unread count:', error);
        }
    };

    const handleSendMessage = async (e) => {
        e.preventDefault();
        if (!newMessage.trim() && !imageFile) return;

        const formData = new FormData();
        formData.append('session_id', sessionId);
        if (activeReceiver) formData.append('receiver_id', activeReceiver.id);
        if (newMessage.trim()) formData.append('message', newMessage);
        if (imageFile) formData.append('image', imageFile);

        try {
            const token = localStorage.getItem('auth_token');
            const headers = {
                'Content-Type': 'multipart/form-data',
                ...(token ? { Authorization: `Bearer ${token}` } : {})
            };

            const response = await axios.post('/api/chat/send', formData, { headers });

            if (response.data.success) {
                setNewMessage('');
                setImageFile(null);
                if (fileInputRef.current) fileInputRef.current.value = '';
                fetchMessages(); // Immediately fetch to update UI
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    };

    const handleFileChange = (e) => {
        if (e.target.files && e.target.files[0]) {
            setImageFile(e.target.files[0]);
        }
    };

    // Close menu if route changes
    useEffect(() => {
        setIsMenuOpen(false);
        // Don't close chat window automatically on route change if it's active
        // unless it's a major navigation. Let's keep it open for now as per user expectation.
    }, [location]);

    return (
        <div className="chat-widget-container" style={{ position: 'fixed', zIndex: 9999, display: 'flex', flexDirection: 'column', alignItems: 'flex-end' }}>

            {/* --- Chat Window --- */}
            {isOpen && (
                <div className="chat-window" style={{ backgroundColor: '#fff', borderRadius: '15px', boxShadow: '0 5px 25px rgba(0,0,0,0.2)', display: 'flex', flexDirection: 'column', marginBottom: '20px', overflow: 'hidden', animation: 'slideUp 0.3s ease' }}>

                    {/* Header */}
                    <div style={{ backgroundColor: '#20c950', color: '#fff', padding: '15px', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                            <i className="fas fa-headset" style={{ fontSize: '24px' }}></i>
                            <div>
                                <h6 style={{ margin: 0, fontWeight: 'bold' }}>{activeReceiver ? activeReceiver.name : 'JHR Bazar Support'}</h6>
                                <small style={{ display: 'flex', alignItems: 'center', gap: '5px' }}>
                                    <span style={{ width: '8px', height: '8px', backgroundColor: '#fff', borderRadius: '50%', display: 'inline-block' }}></span>
                                    Online now
                                </small>
                            </div>
                        </div>
                        <div style={{ display: 'flex', gap: '5px' }}>
                            {activeReceiver && (
                                <button onClick={() => { setActiveReceiver(null); setMessages([]); }} title="Switch to Support" style={{ background: 'none', border: 'none', color: '#fff', fontSize: '16px', cursor: 'pointer', padding: '5px' }}>
                                    <i className="fas fa-exchange-alt"></i>
                                </button>
                            )}
                            <button onClick={() => setIsOpen(false)} style={{ background: 'none', border: 'none', color: '#fff', fontSize: '20px', cursor: 'pointer', padding: '5px' }}>
                                <i className="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    {/* Messages Body */}
                    <div style={{ flex: 1, padding: '15px', overflowY: 'auto', backgroundColor: '#f5f7f9', display: 'flex', flexDirection: 'column', gap: '10px' }}>
                        {messages.length === 0 ? (
                            <div style={{ textAlign: 'center', color: '#888', marginTop: '20px' }}>
                                <i className="far fa-comments" style={{ fontSize: '40px', marginBottom: '10px' }}></i>
                                <p>Start a conversation with us!</p>
                            </div>
                        ) : (
                            messages.map((msg, index) => (
                                <div key={index} style={{ alignSelf: msg.sender_type === 'user' ? 'flex-end' : 'flex-start', maxWidth: '80%' }}>
                                    <div style={{
                                        backgroundColor: msg.sender_type === 'user' ? '#20c950' : '#fff',
                                        color: msg.sender_type === 'user' ? '#fff' : '#333',
                                        padding: '10px 15px',
                                        borderRadius: msg.sender_type === 'user' ? '15px 15px 0 15px' : '15px 15px 15px 0',
                                        boxShadow: '0 2px 5px rgba(0,0,0,0.05)',
                                        wordBreak: 'break-word'
                                    }}>
                                        {msg.image && (
                                            <a href={msg.image} target="_blank" rel="noreferrer">
                                                <img src={msg.image} alt="attachment" style={{ maxWidth: '100%', borderRadius: '5px', marginBottom: msg.message ? '5px' : '0' }} />
                                            </a>
                                        )}
                                        {msg.message && <span>{msg.message}</span>}
                                    </div>
                                    <div style={{ fontSize: '10px', color: '#999', marginTop: '3px', textAlign: msg.sender_type === 'user' ? 'right' : 'left' }}>
                                        {new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                    </div>
                                </div>
                            ))
                        )}
                        <div ref={messagesEndRef} />
                    </div>

                    {/* Input Area */}
                    <div style={{ padding: '15px', backgroundColor: '#fff', borderTop: '1px solid #eee' }}>
                        {imageFile && (
                            <div style={{ padding: '5px 10px', backgroundColor: '#f0f0f0', borderRadius: '5px', marginBottom: '10px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', fontSize: '12px' }}>
                                <span><i className="fas fa-image me-1"></i> {imageFile.name}</span>
                                <button type="button" onClick={() => { setImageFile(null); if (fileInputRef.current) fileInputRef.current.value = ''; }} style={{ background: 'none', border: 'none', color: '#ff4d4f', cursor: 'pointer' }}><i className="fas fa-times"></i></button>
                            </div>
                        )}
                        <form onSubmit={handleSendMessage} style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                            <div style={{ position: 'relative', flex: 1 }}>
                                <input
                                    type="text"
                                    value={newMessage}
                                    onChange={(e) => setNewMessage(e.target.value)}
                                    placeholder="Type a message..."
                                    style={{ width: '100%', padding: '10px 40px 10px 15px', border: '1px solid #ddd', borderRadius: '20px', outline: 'none' }}
                                />
                                <input
                                    type="file"
                                    ref={fileInputRef}
                                    onChange={handleFileChange}
                                    accept="image/*"
                                    style={{ display: 'none' }}
                                />
                                <button type="button" onClick={() => fileInputRef.current.click()} style={{ position: 'absolute', right: '10px', top: '50%', transform: 'translateY(-50%)', background: 'none', border: 'none', color: '#888', cursor: 'pointer' }}>
                                    <i className="fas fa-paperclip"></i>
                                </button>
                            </div>
                            <button type="submit" style={{ width: '40px', height: '40px', borderRadius: '50%', backgroundColor: '#20c950', color: '#fff', border: 'none', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer', flexShrink: 0 }}>
                                <i className="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            )}

            {/* --- Options Menu --- */}
            {isMenuOpen && !isOpen && (
                <div style={{ display: 'flex', flexDirection: 'column', gap: '15px', marginBottom: '20px', alignItems: 'flex-end', animation: 'slideUp 0.3s ease' }}>

                    {/* Live Chat */}
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                        <div style={{ backgroundColor: '#fff', padding: '8px 15px', borderRadius: '20px', boxShadow: '0 2px 10px rgba(0,0,0,0.1)', fontSize: '14px', fontWeight: 'bold' }}>Live Chat</div>
                        <button onClick={() => { setActiveReceiver(null); setIsOpen(true); setIsMenuOpen(false); }} style={{ width: '50px', height: '50px', borderRadius: '50%', backgroundColor: '#20c950', color: '#fff', border: 'none', boxShadow: '0 4px 15px rgba(32,201,80,0.4)', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer', fontSize: '20px' }}>
                            <i className="far fa-comment-dots"></i>
                        </button>
                    </div>

                    {/* Messenger */}
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                        <div style={{ backgroundColor: '#fff', padding: '8px 15px', borderRadius: '20px', boxShadow: '0 2px 10px rgba(0,0,0,0.1)', fontSize: '14px', fontWeight: 'bold' }}>Messenger</div>
                        <a href="https://m.me/yourpage" target="_blank" rel="noreferrer" style={{ width: '50px', height: '50px', borderRadius: '50%', backgroundColor: '#0084ff', color: '#fff', boxShadow: '0 4px 15px rgba(0,132,255,0.4)', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer', fontSize: '24px', textDecoration: 'none' }}>
                            <i className="fab fa-facebook-messenger"></i>
                        </a>
                    </div>

                    {/* WhatsApp */}
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                        <div style={{ backgroundColor: '#fff', padding: '8px 15px', borderRadius: '20px', boxShadow: '0 2px 10px rgba(0,0,0,0.1)', fontSize: '14px', fontWeight: 'bold' }}>WhatsApp</div>
                        <a href="https://wa.me/yournumber" target="_blank" rel="noreferrer" style={{ width: '50px', height: '50px', borderRadius: '50%', backgroundColor: '#25d366', color: '#fff', boxShadow: '0 4px 15px rgba(37,211,102,0.4)', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer', fontSize: '24px', textDecoration: 'none' }}>
                            <i className="fab fa-whatsapp"></i>
                        </a>
                    </div>

                    {/* Call Us */}
                    <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                        <div style={{ backgroundColor: '#fff', padding: '8px 15px', borderRadius: '20px', boxShadow: '0 2px 10px rgba(0,0,0,0.1)', fontSize: '14px', fontWeight: 'bold' }}>Call Us</div>
                        <a href={`tel:${settings?.hotline_number || settings?.mobile_number || '01700000000'}`} style={{ width: '50px', height: '50px', borderRadius: '50%', backgroundColor: '#344050', color: '#fff', boxShadow: '0 4px 15px rgba(52,64,80,0.4)', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer', fontSize: '20px', textDecoration: 'none' }}>
                            <i className="fas fa-phone-alt"></i>
                        </a>
                    </div>
                </div>
            )}

            {/* --- Main Floating Action Button --- */}
            {!isOpen && (
                <div style={{ position: 'relative' }}>
                    <button
                        onClick={() => {
                            setIsMenuOpen(!isMenuOpen);
                            if (isMenuOpen && unreadCount > 0) setUnreadCount(0);
                        }}
                        style={{
                            width: '60px',
                            height: '60px',
                            borderRadius: '50%',
                            backgroundColor: isMenuOpen ? '#444' : '#20c950',
                            color: '#fff',
                            border: 'none',
                            boxShadow: '0 5px 20px rgba(0,0,0,0.2)',
                            display: 'flex',
                            justifyContent: 'center',
                            alignItems: 'center',
                            cursor: 'pointer',
                            fontSize: '28px',
                            transition: 'all 0.3s ease'
                        }}
                    >
                        {isMenuOpen ? <i className="fas fa-times"></i> : <i className="far fa-comment-dots"></i>}
                    </button>

                    {/* Unread Badge */}
                    {unreadCount > 0 && !isMenuOpen && (
                        <span style={{
                            position: 'absolute',
                            top: '-5px',
                            right: '-5px',
                            backgroundColor: '#ff3b30',
                            color: '#fff',
                            borderRadius: '50%',
                            width: '24px',
                            height: '24px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            fontSize: '12px',
                            fontWeight: 'bold',
                            boxShadow: '0 2px 5px rgba(0,0,0,0.2)',
                            animation: 'bounce 1s infinite'
                        }}>
                            {unreadCount}
                        </span>
                    )}
                </div>
            )}

            <style>{`
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes bounce {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-5px); }
                }
                .chat-widget-container {
                    bottom: 30px;
                    right: 30px;
                }
                .chat-window {
                    width: 350px;
                    height: 500px;
                }
                @media (max-width: 768px) {
                    .chat-widget-container {
                        bottom: 90px;
                        right: 15px;
                    }
                    .chat-window {
                        width: calc(100vw - 30px);
                        max-height: 60vh;
                        height: 400px; /* fallback */
                    }
                }
            `}</style>
        </div>
    );
};

export default LiveChatWidget;
