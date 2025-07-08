<nav class="navbar flex items-center justify-between w-full px-4 py-2">
    <div class="navbar-logo flex items-center">
        <a href="/">
            <img src="{{ asset('images/apex-logo.png') }}" alt="Logo" />
        </a>
        <span class="navbar-title ml-2">Caramel Yogurt</span>
    </div>
    <div class="flex flex-row items-center gap-4 ml-auto">
        @auth
            <!-- Notification Bell with Dropdown for unread messages (read-only) -->
            <div class="relative">
                <button id="notificationBellBtn" class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-800 hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150 mr-1" title="Notifications">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span id="notificationDot" class="absolute top-2 right-2 block h-5 w-5 rounded-full bg-red-500 text-xs text-white flex items-center justify-center font-bold hidden"></span>
                </button>
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 border border-gray-200" style="min-width: 280px;">
                    <div class="p-4 border-b font-semibold">Unread Messages</div>
                    <div id="notificationMessages" class="p-4 max-h-64 overflow-y-auto text-sm"></div>
                </div>
            </div>
            <!-- Chat Icon Button (link to /chat) -->
            <a href="/chat" class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-500 hover:bg-blue-600 focus:outline-none transition ease-in-out duration-150 mr-1" title="Chat">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2 21l1.65-4.95A8.001 8.001 0 1 1 12 20a7.96 7.96 0 0 1-4.95-1.65L2 21zm6-9a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm4 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm4 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                </svg>
                <span id="chatNotificationDot" class="absolute top-2 right-2 block h-5 w-5 rounded-full bg-red-500 text-xs text-white flex items-center justify-center font-bold hidden"></span>
            </a>
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="w-10 h-10 rounded-full bg-blue-800 hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150 overflow-hidden p-0 border-2 border-white" title="User menu">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile Photo" class="w-full h-full object-cover rounded-full block" />
                        @else
                            <span class="w-full h-full flex items-center justify-center text-white font-bold text-lg uppercase">
                                {{ collect(explode(' ', auth()->user()->name))->map(fn($part) => strtoupper(substr($part,0,1)))->join('') }}
                            </span>
                        @endif
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        @endauth
        @guest
            <x-nav-link :href="route('login')" :active="request()->routeIs('login')" class="text-white hover:text-blue-200">
                {{ __('Login') }}
            </x-nav-link>
            <x-nav-link :href="route('register')" :active="request()->routeIs('register')" class="text-white hover:text-blue-200">
                {{ __('Register') }}
            </x-nav-link>
        @endguest
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatIconBtn = document.getElementById('chatIconBtn');
        const chatDropdown = document.getElementById('chatDropdown');
        const chatRecipient = document.getElementById('chatRecipient');
        const chatMessages = document.getElementById('chatMessages');
        const chatMessageInput = document.getElementById('chatMessageInput');
        const chatSendBtn = document.getElementById('chatSendBtn');
        const chatNotificationDot = document.getElementById('chatNotificationDot');
        const notificationBellBtn = document.getElementById('notificationBellBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationMessages = document.getElementById('notificationMessages');
        const notificationDot = document.getElementById('notificationDot');
        let currentRecipientId = '';
        let unreadCount = 0;
        let unreadChatCount = 0;
        let unreadFromUsers = {};
        let pollingInterval = null;

        // Toggle dropdown
        chatIconBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            chatDropdown.classList.toggle('hidden');
            if (!chatDropdown.classList.contains('hidden')) {
                loadRecipients();
                if (chatRecipient.value) loadMessages(chatRecipient.value);
            }
        });
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!chatDropdown.contains(e.target) && e.target !== chatIconBtn) {
                chatDropdown.classList.add('hidden');
            }
        });
        // Load recipients and show dot for users with unread messages
        function loadRecipients() {
            fetch('/chat/recipients')
                .then(res => res.json())
                .then(users => {
                    console.log('Recipients:', users); // Debug log
                    if (!Array.isArray(users) || users.length === 0) {
                        console.error('No users received or users is not an array:', users);
                        chatRecipient.innerHTML = '<option value="">No users found</option>';
                        return;
                    }
                    // Fetch unread messages per user
                    fetch('/chat/messages?with_user_id=all')
                        .then(res2 => res2.json())
                        .then(messages => {
                            unreadFromUsers = {};
                            messages.forEach(msg => {
                                if (!msg.is_read && msg.receiver_id == {{ auth()->id() }}) {
                                    unreadFromUsers[msg.sender_id] = true;
                                }
                            });
                            chatRecipient.innerHTML = '<option value="">Select user...</option>';
                            users.forEach(user => {
                                fetch(`/api/user/${user.id}`)
                                    .then(r => r.json())
                                    .then(userInfo => {
                                        const photoUrl = userInfo.profile_photo_url || '/images/default-avatar.png';
                                        let dot = unreadFromUsers[user.id] ? ' <span style="color:red;font-size:1.2em;">•</span>' : '';
                                        chatRecipient.innerHTML += `<option value="${user.id}">
                                            <span style='display:flex;align-items:center;'>
                                                <img src='${photoUrl}' style='width:24px;height:24px;border-radius:50%;object-fit:cover;margin-right:8px;'>
                                                ${user.name} (${user.email})${dot}
                                            </span>
                                        </option>`;
                                    });
                            });
                        })
                        .catch(err => {
                            console.error('Error fetching messages:', err);
                        });
                })
                .catch(err => {
                    console.error('Error fetching recipients:', err);
                    chatRecipient.innerHTML = '<option value="">Error loading users</option>';
                });
        }
        // On recipient change, load messages
        chatRecipient.addEventListener('change', function() {
            currentRecipientId = this.value;
            chatMessageInput.value = '';
            chatMessages.innerHTML = '';
            if (currentRecipientId) {
                loadMessages(currentRecipientId);
            }
        });
        // Load messages
        function loadMessages(withUserId) {
            fetch(`/chat/messages?with_user_id=${withUserId}`)
                .then(res => res.json())
                .then(messages => {
                    chatMessages.innerHTML = '';
                    messages.forEach(msg => {
                        const align = msg.sender_id == {{ auth()->id() }} ? 'text-right' : 'text-left';
                        const bg = msg.sender_id == {{ auth()->id() }} ? 'bg-blue-100' : 'bg-gray-200';
                        chatMessages.innerHTML += `<div class="mb-2 ${align}"><span class="inline-block px-2 py-1 rounded ${bg}">${msg.message}</span></div>`;
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
        }
        // Send message
        chatSendBtn.addEventListener('click', function() {
            sendMessage();
        });
        chatMessageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') sendMessage();
        });
        function sendMessage() {
            const message = chatMessageInput.value.trim();
            if (!currentRecipientId || !message) return;
            // Optimistically display the message immediately
            const align = 'text-right';
            const bg = 'bg-blue-100';
            const sentLabel = '<span class="ml-2 text-xs text-green-600">Sent</span>';
            chatMessages.innerHTML += `<div class="mb-2 ${align}"><span class="inline-block px-2 py-1 rounded ${bg}">${message}${sentLabel}</span></div>`;
            chatMessages.scrollTop = chatMessages.scrollHeight;
            chatMessageInput.value = '';
            fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ receiver_id: currentRecipientId, message })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Optionally reload messages to ensure sync
                    loadMessages(currentRecipientId);
                }
            });
        }
        // Update pollUnread to also update notification and chat badge with count
        function pollUnread() {
            fetch('/chat/unread-count')
                .then(res => res.json())
                .then(data => {
                    unreadCount = data.unread_count;
                    // Show badge with count or hide if zero
                    if (unreadCount > 0) {
                        chatNotificationDot.classList.remove('hidden');
                        notificationDot.classList.remove('hidden');
                        chatNotificationDot.textContent = unreadCount;
                        notificationDot.textContent = unreadCount;
                    } else {
                        chatNotificationDot.classList.add('hidden');
                        notificationDot.classList.add('hidden');
                        chatNotificationDot.textContent = '';
                        notificationDot.textContent = '';
                    }
                });
        }
        pollUnread();
        pollingInterval = setInterval(pollUnread, 5000);

        // --- Notification Bell Dropdown Logic ---
        function updateNotificationBadge() {
            fetch('/chat/unread-counts')
                .then(res => res.json())
                .then(unreadCounts => {
                    let totalUnread = 0;
                    for (const senderId in unreadCounts) {
                        totalUnread += unreadCounts[senderId];
                    }
                    const notificationDot = document.getElementById('notificationDot');
                    if (totalUnread > 0) {
                        notificationDot.classList.remove('hidden');
                        notificationDot.textContent = totalUnread;
                    } else {
                        notificationDot.classList.add('hidden');
                        notificationDot.textContent = '';
                    }
                });
        }
        updateNotificationBadge();
        setInterval(updateNotificationBadge, 10000);

        notificationBellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
            if (!notificationDropdown.classList.contains('hidden')) {
                // Load unread senders
                fetch('/chat/unread-counts')
                    .then(res => res.json())
                    .then(unreadCounts => {
                        const senderIds = Object.keys(unreadCounts);
                        if (senderIds.length === 0) {
                            notificationMessages.innerHTML = '<div class="text-gray-500 text-center py-4">No unread messages.</div>';
                            return;
                        }
                        // Fetch sender info for each senderId
                        Promise.all(senderIds.map(id => fetch(`/api/user/${id}`).then(r => r.json())))
                            .then(users => {
                                notificationMessages.innerHTML = '';
                                users.forEach((user, idx) => {
                                    const senderId = senderIds[idx];
                                    const photoUrl = user.profile_photo_url || '/images/default-avatar.png';
                                    const div = document.createElement('div');
                                    div.className = 'mb-3 border-b pb-2 flex items-center gap-2';
                                    div.innerHTML = `
                                        <a href="/chat?user_id=${senderId}" class="flex-1 flex items-center gap-2 text-blue-700 font-semibold hover:underline notification-chat-link" data-user-id="${senderId}">
                                            <img src="${photoUrl}" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-300" />
                                            <span>${user.name || 'User #' + senderId}</span>
                                            <span class="ml-2 text-xs text-red-600 font-bold">Unread message</span>
                                        </a>
                                    `;
                                    notificationMessages.appendChild(div);
                                });
                                // Add click handler to each link to go to chat and select user
                                document.querySelectorAll('.notification-chat-link').forEach(link => {
                                    link.addEventListener('click', function(ev) {
                                        ev.preventDefault();
                                        window.location.href = this.href;
                                    });
                                });
                            });
                    });
            }
        });
        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target) && e.target !== notificationBellBtn) {
                notificationDropdown.classList.add('hidden');
            }
        });
        // Load unread messages for notification dropdown
        function loadUnreadMessages() {
            fetch('/chat/messages?with_user_id=all')
                .then(res => res.json())
                .then(messages => {
                    if (!Array.isArray(messages) || messages.length === 0) {
                        notificationMessages.innerHTML = '<div class="text-gray-500">No unread messages.</div>';
                        return;
                    }
                    notificationMessages.innerHTML = '';
                    messages.forEach(msg => {
                        if (!msg.is_read && msg.receiver_id == {{ auth()->id() }}) {
                            notificationMessages.innerHTML += `<div class="mb-3 border-b pb-2"><div class="font-semibold text-blue-700">${msg.sender?.name || 'User #' + msg.sender_id}</div><div class="text-gray-700">${msg.message}</div></div>`;
                        }
                    });
                });
            // Mark all as read (optional: you can implement a separate endpoint for this)
        }
        // --- End Notification Bell Dropdown Logic ---

        // If you have a chat icon dropdown, update its logic as follows:
        function updateChatDropdown() {
            fetch('/chat/unread-counts')
                .then(res => res.json())
                .then(unreadCounts => {
                    const senderIds = Object.keys(unreadCounts);
                    const chatDropdown = document.getElementById('chatDropdown');
                    const chatMessages = document.getElementById('chatMessages');
                    if (!chatDropdown || !chatMessages) return;
                    if (senderIds.length === 0) {
                        chatMessages.innerHTML = '<div class="text-gray-500">No unread messages.</div>';
                        return;
                    }
                    Promise.all(senderIds.map(id => fetch(`/api/user/${id}`).then(r => r.json())))
                        .then(users => {
                            chatMessages.innerHTML = '';
                            users.forEach((user, idx) => {
                                const senderId = senderIds[idx];
                                const photoUrl = user.profile_photo_url || '/images/default-avatar.png';
                                const div = document.createElement('div');
                                div.className = 'mb-3 border-b pb-2 flex items-center gap-2';
                                div.innerHTML = `
                                    <a href="/chat?user_id=${senderId}" class="flex-1 flex items-center gap-2 text-blue-700 font-semibold hover:underline notification-chat-link" data-user-id="${senderId}">
                                        <img src="${photoUrl}" alt="Profile" class="w-8 h-8 rounded-full object-cover border border-gray-300" />
                                        <span>${user.name || 'User #' + senderId}</span>
                                        <span class="ml-2 text-xs text-red-600 font-bold">Unread: ${unreadCounts[senderId]}</span>
                                    </a>
                                `;
                                chatMessages.appendChild(div);
                            });
                            document.querySelectorAll('.notification-chat-link').forEach(link => {
                                link.addEventListener('click', function(ev) {
                                    ev.preventDefault();
                                    window.location.href = this.href;
                                });
                            });
                        });
                });
        }
        // Call updateChatDropdown when the chat icon is clicked (if you have a dropdown)
        if (chatIconBtn) {
            chatIconBtn.addEventListener('click', function(e) {
                updateChatDropdown();
            });
        }

        // Add logic to update the chat icon badge with unread count
        function updateChatIconBadge() {
            fetch('/chat/unread-counts')
                .then(res => res.json())
                .then(unreadCounts => {
                    let totalUnread = 0;
                    for (const senderId in unreadCounts) {
                        totalUnread += unreadCounts[senderId];
                    }
                    const chatNotificationDot = document.getElementById('chatNotificationDot');
                    if (totalUnread > 0) {
                        chatNotificationDot.classList.remove('hidden');
                        chatNotificationDot.textContent = totalUnread;
                    } else {
                        chatNotificationDot.classList.add('hidden');
                        chatNotificationDot.textContent = '';
                    }
                });
        }
        updateChatIconBadge();
        setInterval(updateChatIconBadge, 10000);
    });
</script>
<style>
    /* Chat Dropdown Styles */
    #chatDropdown { box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
    #chatDropdown select, #chatDropdown input { font-size: 0.95rem; }
    #chatMessages::-webkit-scrollbar { width: 6px; background: #f1f1f1; }
    #chatMessages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    /* Notification/Chat badge styles */
    #notificationDot, #chatNotificationDot { min-width: 1.25em; min-height: 1.25em; line-height: 1.25em; text-align: center; font-size: 0.85em; }
</style>
