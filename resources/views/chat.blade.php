@extends('layouts.app')

@section('content')
<style>
    .chat-container {
        display: flex;
        height: 70vh;
        min-height: 400px;
        max-height: 80vh;
        background: #f7f7f7;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        margin: 8px auto 0 auto;
        max-width: 900px;
        border: 1.5px solid #fff;
    }
    body {
        background: #fff;
    }
    .user-list {
        width: 340px;
        background: #fff;
        border-right: 1px solid #eee;
        overflow-y: auto;
        position: relative;
        min-width: 220px;
        max-width: 420px;
        margin-left: 38px;
        padding-left: 32px;
    }
    .user-list .user {
        padding: 8px 8px 8px 12px;
        margin-left: 0;
    }
    .user-list .user-name {
        font-weight: 500;
        font-size: 1.08rem;
        line-height: 1.25;
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
        max-width: 100%;
        display: block;
        word-break: break-word;
    }
    .chat-error-banner {
        background: #fee2e2;
        color: #b91c1c;
        font-size: 1em;
        text-align: center;
        padding: 4px 0 3px 0;
        border-radius: 6px;
        margin-bottom: 6px;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    .user-list .search-bar {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 2;
        padding: 16px 24px 8px 24px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }
    .search-input-wrapper {
        position: relative;
        width: 100%;
    }
    .search-input-wrapper input[type="text"] {
        width: 100%;
        padding: 10px 38px 10px 14px;
        border-radius: 8px;
        border: 2px solid #22c55e;
        font-size: 1em;
        outline: none;
        transition: border 0.2s;
    }
    .search-input-wrapper input[type="text"]:focus {
        border: 2.5px solid #16a34a;
        box-shadow: 0 0 0 2px #bbf7d0;
    }
    .search-icon-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: #22c55e;
        border: 2px solid #22c55e;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        padding: 2px 8px;
        display: flex;
        align-items: center;
        font-size: 1.2em;
        transition: background 0.2s, border 0.2s;
    }
    .search-icon-btn:hover {
        background: #16a34a;
        border-color: #16a34a;
    }
    .search-icon-btn svg {
        color: #fff;
    }
    .user-list .user {
        padding: 8px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
        position: relative;
    }
    .user-list .user:hover, .user-list .user.active {
        background: #e0f2fe !important;
    }
    .user-list .user.active .user-name {
        background: #e0f2fe;
        border-radius: 6px;
        padding: 2px 4px;
    }
    .user-list .avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        margin-right: 8px;
        object-fit: cover;
        background: #eee;
    }
    .user-list .user-info {
        flex: 1;
    }
    .user-list .user-name {
        font-weight: 500;
        font-size: 0.95rem;
        line-height: 1.1;
    }
    .user-list .last-message {
        color: #888;
        font-size: 0.85rem;
        margin-top: 1px;
    }
    .user-list .message-time {
        font-size: 0.75rem;
        color: #aaa;
        margin-left: 4px;
    }
    .unread-badge {
        display: none;
        position: absolute;
        right: 24px;
        top: 18px;
        background: #e53e3e;
        color: #fff;
        border-radius: 50%;
        padding: 2px 8px;
        font-size: 0.85em;
        font-weight: bold;
        z-index: 3;
    }
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f4fdf7;
        min-width: 0;
        max-width: 100%;
    }
    .chat-header {
        padding: 6px 16px;
        background: #25D366;
        color: #fff;
        font-weight: 500;
        font-size: 0.98rem;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e0e0e0;
        min-height: 32px;
        height: 38px;
    }
    .chat-messages {
        flex: 1;
        padding: 10px 10px 10px 10px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 0;
        max-height: 100%;
    }
    .chat-loaded-banner {
        background: #e0f2fe;
        color: #2563eb;
        font-size: 0.95em;
        text-align: center;
        padding: 3px 0 2px 0;
        border-radius: 6px;
        margin-bottom: 6px;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    .message {
         max-width: 80%;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 1.13rem;
        line-height: 1.18;
        position: relative;
        word-break: break-word;
        margin-bottom: 2px;
    }
    .message-time {
        font-size: 0.75rem;
        color: #888;
        margin-top: 2px;
        text-align: right;
        display: block;
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
        min-height: 44px;
        max-height: 120px;
        resize: none;
        overflow-y: auto;
        box-sizing: border-box;
        transition: min-height 0.2s;
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
        <div class="search-bar">
            <div class="search-input-wrapper">
                <input type="text" id="userSearchInput" placeholder="Search users..." />
                <button class="search-icon-btn" id="searchIconBtn" title="Search">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </button>
            </div>
        </div>
        <div style="padding: 24px; color: #888; text-align: center;" id="userListLoading">Loading users...</div>
    </div>
    <!-- Chat Main -->
    <div class="chat-main" id="chatMain">
        <div class="chat-header" id="chatHeader" style="position:relative;">
            <span>Select a user to start chatting</span>
            <div style="margin-left:auto;display:flex;align-items:center;gap:8px;position:relative;">
                <button id="bgOptionsBtn" title="Chat options" style="background:none;border:none;cursor:pointer;padding:4px 8px;font-size:1.8em;line-height:1;">
                    &#8942;
                </button>
                <div id="bgOptionsMenu" style="display:none;position:absolute;right:0;top:40px;background:#fff;border:1px solid #eee;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);padding:12px 0;z-index:10;min-width:200px;max-width:240px;">
                    <div style="font-weight:600;font-size:1em;margin-bottom:8px;text-align:center;">Chat Background</div>
                    <ul style="list-style:none;padding:0;margin:0;">
                        <li><a href="#" id="bgColorText" style="display:block;padding:10px 24px;color:#222;text-decoration:none;cursor:pointer;transition:background 0.2s;">Change Background Color</a></li>
                        <li><a href="#" id="bgImageText" style="display:block;padding:10px 24px;color:#222;text-decoration:none;cursor:pointer;transition:background 0.2s;">Change Background Image</a></li>
                        <li><a href="#" id="bgRemoveText" style="display:block;padding:10px 24px;color:#e53e3e;text-decoration:none;cursor:pointer;transition:background 0.2s;font-weight:500;">Remove Background</a></li>
                    </ul>
                    <input type="color" id="bgColorPicker" style="display:none;">
                    <input type="file" id="bgImagePicker" accept="image/*" style="display:none;">
                </div>
            </div>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div style="color: #aaa; text-align: center;">No conversation selected.</div>
        </div>
        <form class="chat-input" id="chatForm" style="display:none;">
            <input type="text" id="chatInput" placeholder="Type a message..." autocomplete="off" oninput="autoExpandInput(this)" />
            <input type="file" id="chatFileInput" style="display:none;" />
            <button type="button" id="attachFileBtn" title="Attach file" style="background:#eee;color:#222;border:none;border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;cursor:pointer;margin-right:4px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 13.5a3.5 3.5 0 0 0 7 0V3a2.5 2.5 0 0 1 5 0v10a4.5 4.5 0 0 1-9 0V4.5a.5.5 0 0 1 1 0V13.5a3.5 3.5 0 0 0 7 0V3a1.5 1.5 0 0 0-3 0v10.5a.5.5 0 0 1-1 0V3a2.5 2.5 0 0 1 5 0v10.5a5.5 5.5 0 0 1-11 0V4.5a1.5 1.5 0 0 1 3 0V13.5a2.5 2.5 0 0 0 5 0V3a.5.5 0 0 1 1 0v10.5a3.5 3.5 0 0 1-7 0V4.5a.5.5 0 0 1 1 0V13.5z"/></svg>
            </button>
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
    const chatFileInput = document.getElementById('chatFileInput');
    const attachFileBtn = document.getElementById('attachFileBtn');
    const userSearchInput = document.getElementById('userSearchInput');

    // Fetch users and unread counts
    function loadUsersAndBadges() {
        Promise.all([
            fetch('/chat/recipients').then(res => res.json()),
            fetch('/chat/unread-counts').then(res => res.json())
        ]).then(([data, unread]) => {
            users = data;
            unreadCounts = unread;
            // Only clear user list items, not the search bar
            let searchBar = document.querySelector('.user-list .search-bar');
            if (!searchBar) {
                searchBar = document.createElement('div');
                searchBar.className = 'search-bar';
                searchBar.innerHTML = `
                    <div class="search-input-wrapper">
                        <input type="text" id="userSearchInput" placeholder="Search users..." />
                        <button class="search-icon-btn" id="searchIconBtn" title="Search">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                        </button>
                    </div>`;
            userList.innerHTML = '';
                userList.appendChild(searchBar);
                // Attach handlers
                window.userSearchInput = searchBar.querySelector('#userSearchInput');
                window.userSearchInput.addEventListener('input', userSearchInputHandler);
                searchBar.querySelector('#searchIconBtn').addEventListener('click', function(e) {
                    e.preventDefault();
                    userSearchInputHandler();
                    window.userSearchInput.focus();
                });
            } else {
                // Only clear user list items, not the search bar
                Array.from(userList.children).forEach((child, idx) => { if (idx > 0) child.remove(); });
            }
            // Preserve search value
            const searchValue = window.userSearchInput.value;
            if (!users.length) {
                const noUserDiv = document.createElement('div');
                noUserDiv.style = 'padding: 24px; color: #888; text-align: center;';
                noUserDiv.textContent = 'No users found.';
                userList.appendChild(noUserDiv);
                return;
            }
            users.forEach(user => {
                const div = document.createElement('div');
                div.className = 'user';
                div.innerHTML = `
                    <img src="${user.profile_photo_url || '/images/default-avatar.png'}" class="avatar" alt="User">
                    <div class="user-info" style="display:flex;align-items:center;">
                        <span class="user-name">${user.name}</span>
                        <span class="unread-badge" id="unread-badge-${user.id}" style="margin-left:8px;position:relative;top:-2px;min-width:14px;height:14px;padding:0 4px;background:#e3342f;color:#fff;font-size:0.72em;font-weight:bold;border-radius:7px;line-height:14px;text-align:center;z-index:2;display:none;"></span>
                    </div>
                `;
                div.style.position = 'relative';
                div.onclick = () => selectUser(user);
                userList.appendChild(div);
                // Always update badge for this user
                const badge = div.querySelector('.unread-badge');
                if (unreadCounts[user.id] && unreadCounts[user.id] > 0) {
                    badge.textContent = unreadCounts[user.id];
                    badge.style.display = '';
                } else {
                    badge.textContent = '';
                    badge.style.display = 'none';
                }
            });
            // After rendering, highlight the active user if any
            Array.from(userList.getElementsByClassName('user')).forEach(div => {
                const userName = div.querySelector('.user-name').textContent;
                if (users.find(u => u.id === currentUserId && u.name === userName)) {
                    div.classList.add('active');
                } else {
                    div.classList.remove('active');
                }
            });
            // Restore search value and re-filter
            window.userSearchInput.value = searchValue;
            userSearchInputHandler();
            tryAutoSelectUser();
        });
    }

    // Handler for search input (by name or email)
    function userSearchInputHandler() {
        const search = userSearchInput.value.toLowerCase();
        let anyVisible = false;
        Array.from(userList.getElementsByClassName('user')).forEach(div => {
            const name = div.querySelector('.user-name').textContent.toLowerCase();
            const userObj = users.find(u => u.name === div.querySelector('.user-name').textContent);
            const email = userObj && userObj.email ? userObj.email.toLowerCase() : '';
            const match = (name.includes(search) || email.includes(search));
            div.style.display = match ? '' : 'none';
            if (match) anyVisible = true;
        });
        // Remove any previous 'user does not exist' message
        let noUserMsg = document.getElementById('noUserMsg');
        if (noUserMsg) noUserMsg.remove();
        if (!anyVisible) {
            noUserMsg = document.createElement('div');
            noUserMsg.id = 'noUserMsg';
            noUserMsg.style = 'padding: 24px; color: #888; text-align: center;';
            noUserMsg.textContent = 'User does not exist.';
            userList.appendChild(noUserMsg);
        }
    }

    // Attach search handler to both input and icon button
    document.addEventListener('DOMContentLoaded', function() {
        // Reset chat icon badge in header when chat page is opened
        var chatBadge = document.getElementById('chat-badge');
        if (chatBadge) {
            chatBadge.style.display = 'none';
        }
        // Optionally, you can also POST to backend to mark all as seen for the icon
        fetch('/chat/mark-all-seen', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } });
        const searchIconBtn = document.getElementById('searchIconBtn');
        userSearchInput.addEventListener('input', userSearchInputHandler);
        searchIconBtn.addEventListener('click', function(e) {
            e.preventDefault();
            userSearchInputHandler();
            userSearchInput.focus();
        });
    });

    function selectUser(user) {
        currentUserId = user.id;
        lastLoadedUserId = user.id;
        currentUser = user;
        // Highlight selected user
        Array.from(document.getElementsByClassName('user')).forEach(el => el.classList.remove('active'));
        const idx = users.findIndex(u => u.id === user.id);
        if (userList.children[idx + 1]) userList.children[idx + 1].classList.add('active'); // +1 for search bar
        // Update header
        chatHeader.textContent = user.name;
        // Show form
        chatForm.style.display = '';
        chatInput.value = '';
        chatInput.focus();
        // Mark as read in backend, then hide badge only after confirmation
        const badge = document.getElementById('unread-badge-' + user.id);
        if (badge && badge.style.display !== 'none') {
            fetch(`/chat/mark-read?user_id=${user.id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
                .then(() => { badge.style.display = 'none'; });
        }
        // Load messages
        loadMessages();
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
        .then(res => {
            if (!res.ok) throw new Error('Failed to send message');
            return res.json();
        })
        .then(data => {
            // Always reload the full conversation from the backend after sending
            loadMessages();
            // Do NOT update the last message/time in the user list here
        })
        .catch(err => {
            const errBanner = document.createElement('div');
            errBanner.className = 'chat-error-banner';
            errBanner.textContent = err.message;
            chatMessages.insertBefore(errBanner, chatMessages.firstChild);
        });
    };

    attachFileBtn.addEventListener('click', function(e) {
        chatFileInput.click();
    });

    chatFileInput.addEventListener('change', function(e) {
        const file = chatFileInput.files[0];
        if (!file || !currentUserId) return;
        const formData = new FormData();
        formData.append('receiver_id', currentUserId);
        formData.append('file', file);
        // Optionally, add a message
        formData.append('message', chatInput.value || '');
        fetch('/chat/send-file', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            chatInput.value = '';
            chatFileInput.value = '';
            loadMessages();
        });
    });

    // Auto-expand chat input for long messages
    function autoExpandInput(input) {
        input.style.height = 'auto';
        input.style.height = (input.scrollHeight) + 'px';
    }
    document.addEventListener('DOMContentLoaded', function() {
        var chatInput = document.getElementById('chatInput');
        if (chatInput) {
            autoExpandInput(chatInput);
        }
    });
    // Robust polling for unread badges
    function updateUserListBadges() {
        fetch('/chat/unread-counts')
            .then(res => res.json())
            .then(data => {
                // Debug: log unread counts
                console.log('Unread counts:', data);
                Object.keys(data).forEach(function(senderId) {
                    var badge = document.getElementById('unread-badge-' + senderId);
                    if (badge) {
                        badge.textContent = data[senderId];
                        badge.style.display = data[senderId] > 0 ? '' : 'none';
                    }
                });
            });
    }
    // Immediately load users and badges on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadUsersAndBadges();
        updateUserListBadges();
    });
    setInterval(() => {
        if (currentUserId) loadMessages();
        loadUsersAndBadges();
        updateUserListBadges();
    }, 2000);

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

    // Filter users in the list as you type (by name or email)
    // This function is now handled by userSearchInputHandler

    // Background logic
    const chatMain = document.getElementById('chatMain');
    const bgColorPicker = document.getElementById('bgColorPicker');
    const bgImagePicker = document.getElementById('bgImagePicker');

    function applyChatBackground(bg) {
        if (!bg) {
            chatMain.style.background = '#f4fdf7';
            chatMain.style.backgroundImage = '';
            chatMain.style.backgroundSize = '';
            chatMain.style.backgroundRepeat = '';
            chatMain.style.backgroundPosition = '';
            return;
        }
        if (bg.startsWith('#')) {
            chatMain.style.background = bg;
            chatMain.style.backgroundImage = '';
            chatMain.style.backgroundSize = '';
            chatMain.style.backgroundRepeat = '';
            chatMain.style.backgroundPosition = '';
        } else if (bg.startsWith('data:image') || bg.match(/^https?:\/\//)) {
            chatMain.style.background = '';
            chatMain.style.backgroundImage = `url('${bg}')`;
            chatMain.style.backgroundSize = 'cover';
            chatMain.style.backgroundRepeat = 'no-repeat';
            chatMain.style.backgroundPosition = 'center';
        } else {
            chatMain.style.background = '#f4fdf7';
            chatMain.style.backgroundImage = '';
            chatMain.style.backgroundSize = '';
            chatMain.style.backgroundRepeat = '';
            chatMain.style.backgroundPosition = '';
        }
    }

    // Simple text menu logic
    const bgColorText = document.getElementById('bgColorText');
    const bgImageText = document.getElementById('bgImageText');
    const bgRemoveText = document.getElementById('bgRemoveText');

    bgColorText.addEventListener('click', function(e) {
        e.preventDefault();
        bgColorPicker.click();
    });
    bgImageText.addEventListener('click', function(e) {
        e.preventDefault();
        bgImagePicker.click();
    });
    bgRemoveText.addEventListener('click', function(e) {
        e.preventDefault();
        applyChatBackground('');
        fetch('/chat/background', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ chat_background: '' })
        });
    });

    function loadChatBackground() {
        fetch('/chat/background')
            .then(res => res.json())
            .then(data => {
                applyChatBackground(data.chat_background);
                if (data.chat_background && data.chat_background.startsWith('#')) {
                    bgColorPicker.value = data.chat_background;
                    // bgColorPreview.style.background = data.chat_background; // Removed preview
                    // bgImagePreview.style.backgroundImage = ''; // Removed preview
                } else if (data.chat_background && (data.chat_background.startsWith('data:image') || data.chat_background.match(/^https?:\/\//))) {
                    // bgImagePreview.style.backgroundImage = `url('${data.chat_background}')`; // Removed preview
                    // bgColorPreview.style.background = '#f4fdf7'; // Removed preview
                } else {
                    // bgColorPreview.style.background = '#f4fdf7'; // Removed preview
                    // bgImagePreview.style.backgroundImage = ''; // Removed preview
                }
            });
    }

    loadChatBackground();

    // Background options menu logic
    const bgOptionsBtn = document.getElementById('bgOptionsBtn');
    const bgOptionsMenu = document.getElementById('bgOptionsMenu');
    bgOptionsBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        bgOptionsMenu.style.display = bgOptionsMenu.style.display === 'none' ? 'block' : 'none';
    });
    document.addEventListener('click', function(e) {
        if (!bgOptionsMenu.contains(e.target) && e.target !== bgOptionsBtn) {
            bgOptionsMenu.style.display = 'none';
        }
    });

    bgColorPicker.addEventListener('input', function(e) {
        const color = e.target.value;
        applyChatBackground(color);
        fetch('/chat/background', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ chat_background: color })
        });
    });

    bgImagePicker.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            const dataUrl = ev.target.result;
            applyChatBackground(dataUrl);
            fetch('/chat/background', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ chat_background: dataUrl })
            });
        };
        reader.readAsDataURL(file);
    });

    // Update message rendering to display file messages
    function loadMessages() {
        if (!currentUserId) return;
        fetch(`/chat/messages?with_user_id=${currentUserId}`)
            .then(res => {
                if (!res.ok) throw new Error('Failed to load messages');
                return res.json();
            })
            .then(messages => {
                if (currentUserId !== lastLoadedUserId) return;
                chatMessages.innerHTML = '';
                // Show visible banner for loaded messages
                const banner = document.createElement('div');
                banner.className = 'chat-loaded-banner';
                banner.textContent = `Loaded ${messages.length} message${messages.length === 1 ? '' : 's'}`;
                chatMessages.appendChild(banner);
                if (!messages.length) {
                    chatMessages.innerHTML += '<div style="color: #aaa; text-align: center;">No messages yet.</div>';
                }
                messages.forEach(msg => {
                    const sent = msg.sender_id === currentUser.id ? 'received' : 'sent';
                    const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const div = document.createElement('div');
                    div.className = `message ${sent}`;
                    let content = '';
                    if (msg.file_path) {
                        const fileUrl = `/chat/file/${msg.id}`;
                        if (msg.file_type && msg.file_type.startsWith('image/')) {
                            content += `<a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="${msg.original_name || 'Image'}" style="max-width:180px;max-height:180px;border-radius:8px;display:block;margin-bottom:6px;"></a>`;
                        } else {
                            content += `<a href="${fileUrl}" target="_blank" style="display:inline-block;margin-bottom:6px;"><span style="font-size:1.2em;">📎</span> ${msg.original_name || 'Download file'}</a><br/>`;
                        }
                    }
                    if (msg.message) {
                        content += msg.message;
                    }
                    content += `<div class="message-time">${time}</div>`;
                    div.innerHTML = content;
                    chatMessages.appendChild(div);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            })
            .catch(err => {
                chatMessages.innerHTML = `<div class='chat-error-banner'>${err.message}</div>`;
            });
    }
</script>
@endsection 