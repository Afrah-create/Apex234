@extends('layouts.app')

@section('content')
<style>
    .chat-container {
        display: flex;
        height: 80vh;
        background: #f7f7f7;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        margin: 32px auto;
        max-width: 1100px;
    }
    .user-list {
        width: 320px;
        background: #fff;
        border-right: 1px solid #eee;
        overflow-y: auto;
    }
    .user-list .user {
        padding: 18px 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }
    .user-list .user:hover, .user-list .user.active {
        background: #e7fbe9;
    }
    .user-list .avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        margin-right: 16px;
        object-fit: cover;
        background: #eee;
    }
    .user-list .user-info {
        flex: 1;
    }
    .user-list .user-name {
        font-weight: 600;
        font-size: 1.1rem;
    }
    .user-list .last-message {
        color: #888;
        font-size: 0.95rem;
        margin-top: 2px;
    }
    .user-list .message-time {
        font-size: 0.85rem;
        color: #aaa;
        margin-left: 8px;
    }
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f4fdf7;
    }
    .chat-header {
        padding: 18px 24px;
        background: #25D366;
        color: #fff;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e0e0e0;
        min-height: 60px;
    }
    .chat-messages {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .message {
        max-width: 60%;
        padding: 12px 18px;
        border-radius: 18px;
        font-size: 1rem;
        line-height: 1.5;
        position: relative;
        word-break: break-word;
    }
    .message.sent {
        align-self: flex-end;
        background: #dcf8c6;
        color: #222;
    }
    .message.received {
        align-self: flex-start;
        background: #fff;
        color: #222;
        border: 1px solid #e0e0e0;
    }
    .message-time {
        font-size: 0.8rem;
        color: #888;
        margin-top: 4px;
        text-align: right;
    }
    .chat-input {
        padding: 16px 24px;
        background: #fff;
        border-top: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .chat-input input[type="text"] {
        flex: 1;
        border: none;
        background: #f7f7f7;
        border-radius: 20px;
        padding: 12px 18px;
        font-size: 1rem;
        outline: none;
    }
    .chat-input button {
        background: #25D366;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .chat-input button:hover {
        background: #1da851;
    }
</style>
<div class="chat-container">
    <!-- User List -->
    <div class="user-list" id="userList">
        <div style="padding: 24px; color: #888; text-align: center;" id="userListLoading">Loading users...</div>
    </div>
    <!-- Chat Main -->
    <div class="chat-main">
        <div class="chat-header" id="chatHeader">
            Select a user to start chatting
        </div>
        <div class="chat-messages" id="chatMessages">
            <div style="color: #aaa; text-align: center;">No conversation selected.</div>
        </div>
        <form class="chat-input" id="chatForm" style="display:none;">
            <input type="text" id="chatInput" placeholder="Type a message..." autocomplete="off" />
            <button type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M15.854.146a.5.5 0 0 0-.527-.116l-15 6a.5.5 0 0 0 .019.938l6.13 1.751 1.751 6.13a.5.5 0 0 0 .938.019l6-15a.5.5 0 0 0-.116-.527zM6.832 8.41l-4.917-1.404 12.02-4.808-7.103 6.212zm.758.758l6.212-7.103-4.808 12.02-1.404-4.917z"/>
                </svg>
            </button>
        </form>
    </div>
</div>
<script>
    let currentUserId = null;
    let users = [];
    let currentUser = null;
    let unreadCounts = {};
    let lastLoadedUserId = null;
    const userList = document.getElementById('userList');
    const userListLoading = document.getElementById('userListLoading');
    const chatHeader = document.getElementById('chatHeader');
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');

    // Fetch users and unread counts
    function loadUsersAndBadges() {
        Promise.all([
            fetch('/chat/recipients').then(res => res.json()),
            fetch('/chat/unread-counts').then(res => res.json())
        ]).then(([data, unread]) => {
            users = data;
            unreadCounts = unread;
            userList.innerHTML = '';
            if (!users.length) {
                userList.innerHTML = '<div style="padding: 24px; color: #888; text-align: center;">No users found.</div>';
                return;
            }
            users.forEach(user => {
                const div = document.createElement('div');
                div.className = 'user';
                div.innerHTML = `
                    <img src="${user.profile_photo_url || '/images/default-avatar.png'}" class="avatar" alt="User">
                    <div class="user-info">
                        <div class="user-name">${user.name}</div>
                        <div class="last-message" id="last-message-${user.id}"></div>
                    </div>
                    <div class="message-time" id="last-time-${user.id}"></div>
                    <span class="unread-badge" id="unread-badge-${user.id}" style="display:none;position:absolute;right:24px;top:18px;background:#e53e3e;color:#fff;border-radius:50%;padding:2px 8px;font-size:0.85em;font-weight:bold;"></span>
                `;
                div.style.position = 'relative';
                div.onclick = () => selectUser(user);
                userList.appendChild(div);
                // Show badge if unread
                if (unreadCounts[user.id]) {
                    const badge = document.getElementById('unread-badge-' + user.id);
                    badge.textContent = unreadCounts[user.id];
                    badge.style.display = '';
                }
            });
            tryAutoSelectUser();
        });
    }
    loadUsersAndBadges();

    function selectUser(user) {
        currentUserId = user.id;
        lastLoadedUserId = user.id;
        currentUser = user;
        // Highlight selected user
        Array.from(document.getElementsByClassName('user')).forEach(el => el.classList.remove('active'));
        const idx = users.findIndex(u => u.id === user.id);
        if (userList.children[idx]) userList.children[idx].classList.add('active');
        // Update header
        chatHeader.textContent = user.name;
        // Show form
        chatForm.style.display = '';
        chatInput.value = '';
        chatInput.focus();
        // Mark as read visually
        const badge = document.getElementById('unread-badge-' + user.id);
        if (badge) badge.style.display = 'none';
        // Load messages
        loadMessages();
    }

    function loadMessages() {
        if (!currentUserId) return;
        fetch(`/chat/messages?with_user_id=${currentUserId}`)
            .then(res => res.json())
            .then(messages => {
                // Only update if still viewing the same user
                if (currentUserId !== lastLoadedUserId) return;
                chatMessages.innerHTML = '';
                if (!messages.length) {
                    chatMessages.innerHTML = '<div style="color: #aaa; text-align: center;">No messages yet.</div>';
                }
                let lastMsg = null;
                messages.forEach(msg => {
                    const sent = msg.sender_id === currentUser.id ? 'received' : 'sent';
                    const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const div = document.createElement('div');
                    div.className = `message ${sent}`;
                    div.innerHTML = `${msg.message}<div class="message-time">${time}</div>`;
                    chatMessages.appendChild(div);
                    lastMsg = msg;
                });
                // Update last message and time in user list
                if (lastMsg) {
                    document.getElementById('last-message-' + currentUserId).textContent = lastMsg.message;
                    document.getElementById('last-time-' + currentUserId).textContent = new Date(lastMsg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                }
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    chatForm.onsubmit = function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message || !currentUserId) return;
        // Optimistically add the message to the view
        const now = new Date();
        const div = document.createElement('div');
        div.className = 'message sent';
        div.innerHTML = `${message}<div class="message-time">${now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>`;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        chatInput.value = '';
        chatInput.focus();
        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ receiver_id: currentUserId, message })
        })
        .then(res => res.json())
        .then(data => {
            // Always reload the full conversation from the backend after sending
            loadMessages();
            // Optionally, update the last message/time in the user list
            document.getElementById('last-message-' + currentUserId).textContent = message;
            document.getElementById('last-time-' + currentUserId).textContent = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        });
    };

    // Poll for new messages and unread counts every 10 seconds, but only update if a user is selected
    setInterval(() => {
        if (currentUserId) loadMessages();
        loadUsersAndBadges();
    }, 10000);

    // After users are loaded, if a ?user_id= param is present, auto-select that user
    function getQueryParam(name) {
        const url = new URL(window.location.href);
        return url.searchParams.get(name);
    }

    function tryAutoSelectUser() {
        const userId = getQueryParam('user_id');
        if (userId && users.length) {
            const user = users.find(u => u.id == userId);
            if (user) selectUser(user);
        }
    }
</script>
@endsection 