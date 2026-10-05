import React, { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import { useLocation } from 'react-router-dom';
import { useSettings } from '../context/SettingsContext';
import { X, Send, Paperclip, Headphones, MessageSquare, Phone, ArrowLeftRight, Image as ImageIcon } from 'lucide-react';

const LiveChatWidget = () => {
    const { settings } = useSettings();
    const [isOpen, setIsOpen] = useState(false);
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const [messages, setMessages] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [newMessage, setNewMessage] = useState('');
    const [imageFile, setImageFile] = useState(null);
    const [supportSettings, setSupportSettings] = useState(null);
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

    // Fetch Admin Support Settings
    useEffect(() => {
        const fetchSupportSettings = async () => {
            try {
                const response = await axios.get('/api/admin-support');
                if (response.data.success) {
                    setSupportSettings(response.data.data);
                }
            } catch (error) {
                console.error('Error fetching support settings:', error);
            }
        };
        fetchSupportSettings();
    }, []);

    // Link session to user when token is available (after login)
    useEffect(() => {
        if (!sessionId) return;
        const token = localStorage.getItem('auth_token');
        if (token) {
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

    const fetchMessages = async (sidOverride) => {
        const sid = sidOverride || sessionId;
        if (!sid) return;
        try {
            const token = localStorage.getItem('auth_token');
            const headers = token ? { Authorization: `Bearer ${token}` } : {};
            const response = await axios.get(`/api/chat/messages?session_id=${sid}${activeReceiver ? `&receiver_id=${activeReceiver.id}` : ''}`, { headers });
            if (response.data.success) {
                setMessages(response.data.data);
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    };

    const fetchUnreadCount = async () => {
        if (!sessionId) return;
        try {
            const token = localStorage.getItem('auth_token');
            const headers = token ? { Authorization: `Bearer ${token}` } : {};
            const response = await axios.get(`/api/chat/unread-count?session_id=${sessionId}${activeReceiver ? `&receiver_id=${activeReceiver.id}` : ''}`, { headers });
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
    }, [location]);

    return (
        <div className="chat-widget-container" style={{ position: 'fixed', zIndex: 10050, display: 'flex', flexDirection: 'column', alignItems: 'flex-end' }}>

            {/* --- Chat Window --- */}
            {isOpen && (
                <div className="chat-window" style={{ backgroundColor: '#fff', borderRadius: '18px', boxShadow: '0 10px 35px rgba(0,0,0,0.25)', display: 'flex', flexDirection: 'column', marginBottom: '15px', overflow: 'hidden', animation: 'slideUp 0.25s ease-out' }}>

                    {/* Header */}
                    <div style={{ backgroundColor: '#20c950', color: '#fff', padding: '14px 18px', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                            <Headphones size={22} className="text-white" />
                            <div>
                                <h6 style={{ margin: 0, fontWeight: '700', fontSize: '15px' }}>{activeReceiver ? activeReceiver.name : (settings?.website_name ? settings.website_name + ' Support' : 'Support')}</h6>
                                <small style={{ display: 'flex', alignItems: 'center', gap: '5px', fontSize: '11px', opacity: 0.9 }}>
                                    <span style={{ width: '7px', height: '7px', backgroundColor: '#fff', borderRadius: '50%', display: 'inline-block' }}></span>
                                    Online now
                                </small>
                            </div>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            {activeReceiver && (
                                <button 
                                    type="button" 
                                    onClick={() => { setActiveReceiver(null); setMessages([]); }} 
                                    title="Switch to Support" 
                                    style={{ background: 'rgba(255,255,255,0.2)', border: 'none', color: '#fff', width: '30px', height: '30px', borderRadius: '50%', display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', padding: 0 }}
                                >
                                    <ArrowLeftRight size={15} />
                                </button>
                            )}
                            <button 
                                type="button"
                                onClick={() => setIsOpen(false)} 
                                aria-label="Close Chat" 
                                title="Close"
                                style={{ 
                                    background: 'rgba(255,255,255,0.22)', 
                                    border: 'none', 
                                    color: '#fff', 
                                    width: '32px', 
                                    height: '32px', 
                                    borderRadius: '50%', 
                                    display: 'flex', 
                                    alignItems: 'center', 
                                    justifyContent: 'center', 
                                    cursor: 'pointer', 
                                    padding: 0,
                                    transition: 'all 0.2s ease'
                                }}
                                onMouseEnter={(e) => e.currentTarget.style.background = 'rgba(255,255,255,0.38)'}
                                onMouseLeave={(e) => e.currentTarget.style.background = 'rgba(255,255,255,0.22)'}
                            >
                                <X size={18} strokeWidth={2.5} />
                            </button>
                        </div>
                    </div>

                    {/* Messages Body */}
                    <div style={{ flex: 1, padding: '15px', overflowY: 'auto', backgroundColor: '#f5f7f9', display: 'flex', flexDirection: 'column', gap: '10px' }}>
                        {messages.length === 0 ? (
                            <div style={{ textAlign: 'center', color: '#888', marginTop: '30px' }}>
                                <MessageSquare size={44} strokeWidth={1.2} style={{ marginBottom: '10px', opacity: 0.6 }} />
                                <p style={{ fontSize: '14px', margin: 0, fontWeight: '500' }}>Start a conversation with us!</p>
                            </div>
                        ) : (
                            messages.map((msg, index) => (
                                <div key={index} style={{ alignSelf: msg.sender_type === 'user' ? 'flex-end' : 'flex-start', maxWidth: '80%' }}>
                                    <div style={{
                                        backgroundColor: msg.sender_type === 'user' ? '#20c950' : '#fff',
                                        color: msg.sender_type === 'user' ? '#fff' : '#333',
                                        padding: '10px 14px',
                                        borderRadius: msg.sender_type === 'user' ? '14px 14px 2px 14px' : '14px 14px 14px 2px',
                                        boxShadow: '0 2px 5px rgba(0,0,0,0.05)',
                                        wordBreak: 'break-word',
                                        fontSize: '13.5px'
                                    }}>
                                        {msg.image && (
                                            <a href={msg.image} target="_blank" rel="noreferrer">
                                                <img src={msg.image} alt="attachment" style={{ maxWidth: '100%', borderRadius: '6px', marginBottom: msg.message ? '6px' : '0' }} />
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
                    <div style={{ padding: '12px 15px', backgroundColor: '#fff', borderTop: '1px solid #eee' }}>
                        {imageFile && (
                            <div style={{ padding: '5px 10px', backgroundColor: '#f0f0f0', borderRadius: '6px', marginBottom: '8px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', fontSize: '12px' }}>
                                <span className="d-flex align-items-center gap-1"><ImageIcon size={14} /> {imageFile.name}</span>
                                <button type="button" onClick={() => { setImageFile(null); if (fileInputRef.current) fileInputRef.current.value = ''; }} style={{ background: 'none', border: 'none', color: '#ff4d4f', cursor: 'pointer', padding: 0 }}><X size={14} /></button>
                            </div>
                        )}
                        <form onSubmit={handleSendMessage} style={{ display: 'flex', gap: '8px', alignItems: 'center' }}>
                            <div style={{ position: 'relative', flex: 1 }}>
                                <input
                                    type="text"
                                    value={newMessage}
                                    onChange={(e) => setNewMessage(e.target.value)}
                                    placeholder="Type a message..."
                                    style={{ width: '100%', padding: '9px 38px 9px 14px', border: '1.5px solid #e2e8f0', borderRadius: '20px', outline: 'none', fontSize: '13.5px' }}
                                />
                                <input
                                    type="file"
                                    ref={fileInputRef}
                                    onChange={handleFileChange}
                                    accept="image/*"
                                    style={{ display: 'none' }}
                                />
                                <button type="button" onClick={() => fileInputRef.current.click()} style={{ position: 'absolute', right: '10px', top: '50%', transform: 'translateY(-50%)', background: 'none', border: 'none', color: '#888', cursor: 'pointer', padding: 0 }}>
                                    <Paperclip size={16} />
                                </button>
                            </div>
                            <button type="submit" style={{ width: '38px', height: '38px', borderRadius: '50%', backgroundColor: '#20c950', color: '#fff', border: 'none', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer', flexShrink: 0, transition: 'transform 0.2s' }}>
                                <Send size={16} />
                            </button>
                        </form>
                    </div>
                </div>
            )}

            {/* --- Options Menu --- */}
            {isMenuOpen && !isOpen && (
                <div style={{ display: 'flex', flexDirection: 'column', gap: '12px', marginBottom: '15px', alignItems: 'flex-end', animation: 'slideUp 0.25s ease-out' }}>

                    {/* Live Chat */}
                    {(supportSettings?.is_active !== false && supportSettings?.is_active !== 0) && (
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                            <div style={{ backgroundColor: '#fff', padding: '6px 14px', borderRadius: '20px', boxShadow: '0 2px 10px rgba(0,0,0,0.12)', fontSize: '13px', fontWeight: 'bold', color: '#1e293b' }}>Live Chat</div>
                            <button onClick={() => { setActiveReceiver(null); setIsOpen(true); setIsMenuOpen(false); }} style={{ width: '48px', height: '48px', borderRadius: '50%', backgroundColor: '#20c950', color: '#fff', border: 'none', boxShadow: '0 4px 15px rgba(32,201,80,0.4)', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer' }}>
                                <MessageSquare size={20} />
                            </button>
                        </div>
                    )}

                    {/* Messenger (Only if configured) */}
                    {supportSettings?.messenger_url && supportSettings.messenger_url.trim() !== '' && supportSettings.messenger_url !== 'https://m.me/yourpage' && (
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                            <div style={{ backgroundColor: '#fff', padding: '6px 14px', borderRadius: '20px', boxShadow: '0 2px 10px rgba(0,0,0,0.12)', fontSize: '13px', fontWeight: 'bold', color: '#1e293b' }}>Messenger</div>
                            <a href={supportSettings.messenger_url.startsWith('http') ? supportSettings.messenger_url : `https://m.me/${supportSettings.messenger_url.replace('@', '')}`} target="_blank" rel="noreferrer" style={{ width: '48px', height: '48px', borderRadius: '50%', backgroundColor: '#0084ff', color: '#fff', boxShadow: '0 4px 15px rgba(0,132,255,0.4)', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer', fontSize: '22px', textDecoration: 'none' }}>
                                <i className="fab fa-facebook-messenger"></i>
                            </a>
                        </div>
                    )}

                    {/* WhatsApp (Only if configured) */}
                    {supportSettings?.whatsapp_number && supportSettings.whatsapp_number.trim() !== '' && supportSettings.whatsapp_number !== '01700000000' && (
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                            <div style={{ backgroundColor: '#fff', padding: '6px 14px', borderRadius: '20px', boxShadow: '0 2px 10px rgba(0,0,0,0.12)', fontSize: '13px', fontWeight: 'bold', color: '#1e293b' }}>WhatsApp</div>
                            <a href={`https://wa.me/${supportSettings.whatsapp_number.replace(/[^0-9]/g, '')}`} target="_blank" rel="noreferrer" style={{ width: '48px', height: '48px', borderRadius: '50%', backgroundColor: '#25d366', color: '#fff', boxShadow: '0 4px 15px rgba(37,211,102,0.4)', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer', fontSize: '22px', textDecoration: 'none' }}>
                                <i className="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    )}

                    {/* Call Us (Only if phone number is configured) */}
                    {Boolean((supportSettings?.phone_number && supportSettings.phone_number !== '01700000000' ? supportSettings.phone_number : null) || (settings?.hotline_number && settings.hotline_number !== '01700000000' ? settings.hotline_number : null) || (settings?.mobile_number && settings.mobile_number !== '01700000000' ? settings.mobile_number : null)) && (
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                            <div style={{ backgroundColor: '#fff', padding: '6px 14px', borderRadius: '20px', boxShadow: '0 2px 10px rgba(0,0,0,0.12)', fontSize: '13px', fontWeight: 'bold', color: '#1e293b' }}>Call Us</div>
                            <a href={`tel:${(supportSettings?.phone_number && supportSettings.phone_number !== '01700000000' ? supportSettings.phone_number : null) || settings?.hotline_number || settings?.mobile_number}`} style={{ width: '48px', height: '48px', borderRadius: '50%', backgroundColor: '#334155', color: '#fff', boxShadow: '0 4px 15px rgba(51,65,85,0.4)', display: 'flex', justifyContent: 'center', alignItems: 'center', cursor: 'pointer', textDecoration: 'none' }}>
                                <Phone size={18} />
                            </a>
                        </div>
                    )}
                </div>
            )}

            {/* --- Main Floating Action Button --- */}
            {!isOpen && (
                <div style={{ position: 'relative' }}>
                    <button
                        onClick={() => {
                            const hasMessenger = Boolean(supportSettings?.messenger_url && supportSettings.messenger_url.trim() !== '' && supportSettings.messenger_url !== 'https://m.me/yourpage');
                            const hasWhatsApp = Boolean(supportSettings?.whatsapp_number && supportSettings.whatsapp_number.trim() !== '' && supportSettings.whatsapp_number !== '01700000000');
                            const hasPhone = Boolean((supportSettings?.phone_number && supportSettings.phone_number !== '01700000000' ? supportSettings.phone_number : null) || (settings?.hotline_number && settings.hotline_number !== '01700000000' ? settings.hotline_number : null) || (settings?.mobile_number && settings.mobile_number !== '01700000000' ? settings.mobile_number : null));
                            const hasExternal = hasMessenger || hasWhatsApp || hasPhone;

                            if (!hasExternal) {
                                // Direct toggle chat window
                                setActiveReceiver(null);
                                setIsOpen(true);
                                setIsMenuOpen(false);
                            } else {
                                setIsMenuOpen(!isMenuOpen);
                            }
                            if (unreadCount > 0) setUnreadCount(0);
                        }}
                        aria-label="Support Chat"
                        style={{
                            width: '56px',
                            height: '56px',
                            borderRadius: '50%',
                            backgroundColor: isMenuOpen ? '#334155' : '#20c950',
                            color: '#fff',
                            border: 'none',
                            boxShadow: '0 5px 20px rgba(0,0,0,0.22)',
                            display: 'flex',
                            justifyContent: 'center',
                            alignItems: 'center',
                            cursor: 'pointer',
                            fontSize: '24px',
                            transition: 'all 0.3s ease'
                        }}
                    >
                        {isMenuOpen ? <X size={24} /> : <MessageSquare size={24} />}
                    </button>

                    {/* Unread Badge */}
                    {unreadCount > 0 && !isMenuOpen && (
                        <span style={{
                            position: 'absolute',
                            top: '-4px',
                            right: '-4px',
                            backgroundColor: '#ff3b30',
                            color: '#fff',
                            borderRadius: '50%',
                            width: '22px',
                            height: '22px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            fontSize: '11px',
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
                    from { opacity: 0; transform: translateY(15px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes bounce {
                    0%, 100% { transform: translateY(0); }
                    50% { transform: translateY(-4px); }
                }
                .chat-widget-container {
                    bottom: 25px;
                    right: 25px;
                }
                .chat-window {
                    width: 360px;
                    max-width: calc(100vw - 30px);
                    height: min(520px, calc(100vh - 100px));
                    max-height: calc(100vh - 100px);
                }
                @media (max-width: 768px) {
                    .chat-widget-container {
                        bottom: 85px;
                        right: 15px;
                    }
                    .chat-window {
                        width: calc(100vw - 30px);
                        height: min(440px, calc(100vh - 160px));
                        max-height: calc(100vh - 160px);
                    }
                }
            `}</style>
        </div>
    );
};

export default LiveChatWidget;
