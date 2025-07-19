<nav class="navbar flex items-center justify-between w-full px-4 py-2">
    <div class="navbar-logo flex items-center">
        <a href="/">
            <img src="{{ asset('images/apex-logo.png') }}" alt="Logo" />
        </a>
        <span class="navbar-title ml-2">Caramel Yogurt</span>
    </div>
    <div class="flex flex-row items-center gap-4 ml-auto">
        @auth
            <!-- Modern Notification Bell Dropdown -->
            <div class="relative">
                <button id="notificationBell" class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-600 hover:bg-yellow-500 focus:outline-none transition ease-in-out duration-150 mr-1" title="Notifications" aria-haspopup="true" aria-expanded="false">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span id="notificationDot" class="absolute top-2 right-2 block h-5 w-5 rounded-full bg-red-500 text-xs text-white flex items-center justify-center font-bold hidden">0</span>
                </button>
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-96 max-w-xs sm:max-w-md bg-white rounded-lg shadow-lg z-50 border border-gray-200" style="min-width: 320px;">
                    <div class="flex items-center justify-between p-4 border-b">
                        <span class="font-semibold text-gray-800">Notifications</span>
                        <button id="markAllReadBtn" class="text-xs text-blue-600 hover:underline focus:outline-none">Mark all as read</button>
                    </div>
                    <div id="notificationListContainer" class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                        <ul id="notificationList" class="list-none m-0 p-0"></ul>
                    </div>
                    <div class="p-2 border-t text-center">
                        <a href="{{ route('notifications.index') }}" class="text-blue-600 text-sm hover:underline">View all notifications</a>
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
                    {{-- <x-dropdown-link :href="route('help')">
                        {{ __('Help & Support') }}
                    </x-dropdown-link> --}}
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
            {{-- <x-nav-link :href="route('help')" :active="request()->routeIs('help')" class="text-white hover:text-blue-200">
                {{ __('Help & Support') }}
            </x-nav-link> --}}
        @endguest
    </div>
</nav>