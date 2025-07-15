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
                    <div id="notificationMessages" class="p-4 max-h-64 overflow-y-auto text-sm">
                        @if(isset($lowStockNotifications) && $lowStockNotifications->count())
                            <div class="mb-2 font-bold text-red-600">Low Stock Alerts</div>
                            @foreach($lowStockNotifications as $notif)
                                <div class="mb-1">
                                    <span class="font-semibold">{{ ucfirst($notif['type']) }}:</span>
                                    {{ $notif['name'] }} ({{ $notif['quantity'] }} {{ $notif['unit'] }})
                                </div>
                            @endforeach
                        @else
                            <div class="text-gray-500 text-center py-4">No notifications available.</div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Chat Icon Button (link to /chat) -->
            <a href="/chat" class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-500 hover:bg-blue-600 focus:outline-none transition ease-in-out duration-150 mr-1" title="Chat">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2 21l1.65-4.95A8.001 8.001 0 1 1 12 20a7.96 7.96 0 0 1-4.95-1.65L2 21zm6-9a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm4 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm4 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                </svg>
                <span id="chatNotificationDot" class="absolute top-2 right-2 block h-5 w-5 rounded-full bg-red-500 text-xs text-white flex items-center justify-center font-bold hidden"></span>
            </a>
            <!-- Cart Icon Button (only for customers and retailers) -->
            @php $role = auth()->user()->getPrimaryRoleName(); @endphp
            @if($role === 'customer')
                <a href="{{ route('cart.index') }}" class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-orange-500 hover:bg-orange-600 focus:outline-none transition ease-in-out duration-150 mr-1" title="Cart">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="#FFA500" stroke-width="2" fill="#FFA500"/>
                        <rect x="7" y="9" width="10" height="6" rx="2" fill="#fff"/>
                        <circle cx="10" cy="16" r="1.5" fill="#fff"/>
                        <circle cx="14" cy="16" r="1.5" fill="#fff"/>
                    </svg>
                </a>
            @endif
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
                    <x-dropdown-link :href="route('help')">
                        {{ __('Help & Support') }}
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
            <x-nav-link :href="route('help')" :active="request()->routeIs('help')" class="text-white hover:text-blue-200">
                {{ __('Help & Support') }}
            </x-nav-link>
        @endguest
    </div>
</nav>

<!-- Your existing JavaScript and styles below this line -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bellBtn = document.getElementById('notificationBellBtn');
    const dropdown = document.getElementById('notificationDropdown');
    const notificationDot = document.getElementById('notificationDot');
    const chatNotificationDot = document.getElementById('chatNotificationDot');
    const notificationMessages = document.getElementById('notificationMessages');

    // Fetch unread chat count and update badges
    function updateChatBadges() {
        fetch('/chat/unread-grouped')
            .then(res => res.json())
            .then(data => {
                let totalUnread = 0;
                if (Array.isArray(data)) {
                    data.forEach(item => {
                        totalUnread += item.unread_count;
                    });
                }
                // Update chat icon badge
                if (totalUnread > 0) {
                    chatNotificationDot.textContent = totalUnread;
                    chatNotificationDot.classList.remove('hidden');
                } else {
                    chatNotificationDot.classList.add('hidden');
                }
                // Update notification bell badge
                if (totalUnread > 0) {
                    notificationDot.textContent = totalUnread;
                    notificationDot.classList.remove('hidden');
                } else {
                    notificationDot.classList.add('hidden');
                }
            });
    }
    updateChatBadges();
    setInterval(updateChatBadges, 15000); // Poll every 15s

    if (bellBtn && dropdown) {
        bellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
            if (!dropdown.classList.contains('hidden')) {
                // Load unread chat messages grouped by sender
                fetch('/chat/unread-grouped')
                    .then(res => res.json())
                    .then(unreadChats => {
                        if (!Array.isArray(unreadChats) || unreadChats.length === 0) {
                            notificationMessages.innerHTML = '<div class="text-gray-500 text-center py-4">No unread chat messages.</div>';
                            return;
                        }
                        notificationMessages.innerHTML = '';
                        unreadChats.forEach(chat => {
                            const senderId = chat.sender_id;
                            const senderName = chat.sender_name || 'User #' + senderId;
                            const senderAvatar = chat.sender_avatar || '/images/default-avatar.png';
                            const message = chat.latest_message || 'You have a new message.';
                            const time = chat.latest_message_time ? `<div class='text-xs text-gray-500'>${chat.latest_message_time}</div>` : '';
                            const div = document.createElement('div');
                            div.className = 'mb-3 border-b pb-2 flex flex-col gap-1';
                            div.innerHTML = `
                                <div class=\"flex items-center gap-2\">
                                    <img src=\"${senderAvatar}\" alt=\"Profile\" class=\"w-8 h-8 rounded-full object-cover border border-gray-300\" />
                                    <span class=\"font-semibold text-blue-700\">${senderName}</span>
                                    <span class=\"ml-auto bg-red-500 text-white rounded-full px-2 text-xs font-bold\">${chat.unread_count}</span>
                                </div>
                                <div class=\"text-gray-700\">${message}</div>
                                ${time}
                                <button class=\"reply-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mt-1 w-max\" data-user-id=\"${senderId}\">Reply</button>
                            `;
                            notificationMessages.appendChild(div);
                        });
                        // Add click handler to each reply button
                        document.querySelectorAll('.reply-btn').forEach(btn => {
                            btn.addEventListener('click', function(ev) {
                                ev.preventDefault();
                                const userId = this.getAttribute('data-user-id');
                                window.location.href = `/chat?user_id=${userId}`;
                            });
                        });
                    });
            }
        });
        // Hide dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }
});
</script>