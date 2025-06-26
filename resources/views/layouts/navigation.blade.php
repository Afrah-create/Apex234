<nav class="navbar">
    <div class="navbar-logo">
        <a href="/">
            <img src="{{ asset('images/apex-logo.png') }}" alt="Logo" />
        </a>
        <span class="navbar-title">Caramel Yogurt</span>
    </div>
    <div class="navbar-menu sm:hidden">
        <button @click="$store.sidebarOpen.open = true" aria-label="Open sidebar">
            <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
    <div class="hidden sm:flex items-center gap-4">
        @auth
            <!-- Notification Bell -->
            <button class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-800 hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150 mr-1" title="Notifications">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <!-- Notification dot (optional, for unread notifications) -->
                <!-- <span class="absolute top-2 right-2 block h-2 w-2 rounded-full bg-red-500"></span> -->
            </button>
                   
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-800 hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150 text-white font-bold text-lg uppercase" title="User menu">
                            {{ collect(explode(' ', auth()->user()->name))->map(fn($part) => strtoupper(substr($part,0,1)))->join('') }}
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
