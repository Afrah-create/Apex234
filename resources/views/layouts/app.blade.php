<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Caramel Yogurt - Supply Chain Management Dashboard">
        <meta name="keywords" content="Yogurt, Supply Chain, Dashboard, Caramel Yogurt">
        <meta name="author" content="Caramel Yogurt Team">

        <title>{{ config('app.name', 'Caramel Yogurt') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        @yield('styles')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <!-- Navigation Bar -->
        @include('layouts.navigation')
        @auth
            @include('components.sidebar')
            <div class="sidebar-overlay"></div>
        @endauth

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow flex items-center justify-between">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Main Content -->
        <main>
            @yield('content')
            <!-- Floating Chat Bot Widget -->
            <div id="chatbot-widget" style="position: fixed; bottom: 32px; right: 32px; z-index: 9999;">
                <button id="chatbot-toggle" style="background: #2563eb; color: #fff; border: none; border-radius: 50%; width: 56px; height: 56px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); font-size: 2rem; cursor: pointer;">
                    💬
                </button>
                <div id="chatbot-window" style="display: none; width: 340px; max-width: 90vw; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.18); position: absolute; bottom: 70px; right: 0;">
                    <div style="background: #2563eb; color: #fff; padding: 1rem; border-radius: 12px 12px 0 0; font-weight: bold;">Help & Support Bot <span id="chatbot-close" style="float:right; cursor:pointer; font-weight:normal;">&times;</span></div>
                    <div style="padding: 1rem; max-height: 320px; overflow-y: auto;">
                        <div id="chatbot-messages" style="min-height: 120px;">
                            <div style="margin-bottom: 1rem; color: #2563eb;">Hi! How can I help you today? Choose a question below:</div>
                        </div>
                        <ul id="chatbot-questions" style="list-style: none; padding: 0; margin: 0;">
                            @guest
                                <li><button class="chatbot-q" data-a="You can register as a retailer, supplier, or vendor from the registration page.">How to register?</button></li>
                                <li><button class="chatbot-q" data-a="Click 'Forgot Password?' on the login page and follow the instructions.">How to reset my password?</button></li>
                                <li><button class="chatbot-q" data-a="You can browse the FAQ on the Help & Support page.">Where to find help?</button></li>
                                <li><button class="chatbot-q" data-a="Contact yoghurtcaramel@gmail.com for assistance.">How to contact support?</button></li>
                                <li><button class="chatbot-q" data-a="You need to register and log in to access your dashboard.">How to access my dashboard?</button></li>
                            @else
                                @php $role = auth()->user()->getPrimaryRoleName(); @endphp
                                @if($role === 'admin')
                                    <li><button class="chatbot-q" data-a="Go to the admin dashboard to manage users.">How to manage users?</button></li>
                                    <li><button class="chatbot-q" data-a="You can view all reports in the Reports section.">Where to view reports?</button></li>
                                    <li><button class="chatbot-q" data-a="Go to the Workforce tab to assign employees to vendors.">How to assign employees to vendors?</button></li>
                                    <li><button class="chatbot-q" data-a="Use the 'Add New User' button in User Management.">How do I add a new user?</button></li>
                                    <li><button class="chatbot-q" data-a="You can delete or edit users from the User Management table.">How to edit or delete a user?</button></li>
                                @elseif($role === 'vendor')
                                    <li><button class="chatbot-q" data-a="Go to your dashboard to see your assigned employees.">How to see my employees?</button></li>
                                    <li><button class="chatbot-q" data-a="Go to the Orders section to view and manage orders.">How to manage orders?</button></li>
                                    <li><button class="chatbot-q" data-a="You can update your business info from your profile page.">How to update my business information?</button></li>
                                    <li><button class="chatbot-q" data-a="Contact yoghurtcaramel@gmail.com if you need help with vendor features.">How to get vendor support?</button></li>
                                    <li><button class="chatbot-q" data-a="You can view your dashboard for business analytics and stats.">Where to see my business stats?</button></li>
                                @elseif($role === 'supplier')
                                    <li><button class="chatbot-q" data-a="Go to your dashboard to add new raw materials.">How to add raw materials?</button></li>
                                    <li><button class="chatbot-q" data-a="You can view your deliveries in the Deliveries section.">How to view my deliveries?</button></li>
                                    <li><button class="chatbot-q" data-a="Update your company info from your profile page.">How to update my company information?</button></li>
                                    <li><button class="chatbot-q" data-a="Contact yoghurtcaramel@gmail.com for supplier-related help.">How to get supplier support?</button></li>
                                    <li><button class="chatbot-q" data-a="You can view payment history in your dashboard.">How do I view payment history?</button></li>
                                @elseif($role === 'retailer')
                                    <li><button class="chatbot-q" data-a="Go to your dashboard to place new orders.">How to place an order?</button></li>
                                    <li><button class="chatbot-q" data-a="You can view your order history in the Orders section.">How to view my order history?</button></li>
                                    <li><button class="chatbot-q" data-a="Update your store info from your profile page.">How to update my store information?</button></li>
                                    <li><button class="chatbot-q" data-a="Contact yoghurtcaramel@gmail.com for retailer support.">How to get retailer support?</button></li>
                                    <li><button class="chatbot-q" data-a="You can view your dashboard for sales analytics.">Where to see my sales stats?</button></li>
                                @elseif($role === 'employee')
                                    <li><button class="chatbot-q" data-a="Check your assigned tasks in your dashboard.">How to see my tasks?</button></li>
                                    <li><button class="chatbot-q" data-a="You can update your profile from the profile page.">How to update my profile?</button></li>
                                    <li><button class="chatbot-q" data-a="Contact your assigned vendor or yoghurtcaramel@gmail.com for help.">How to get help as an employee?</button></li>
                                    <li><button class="chatbot-q" data-a="You can view your work schedule in your dashboard.">How to view my work schedule?</button></li>
                                    <li><button class="chatbot-q" data-a="You can report issues to your vendor or via the Help & Support page.">How to report an issue?</button></li>
                                @endif
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
            <script>
                window.Laravel = {!! json_encode([
                    'userId' => auth()->check() ? auth()->user()->id : null,
                    'userRole' => auth()->check() ? auth()->user()->getPrimaryRoleName() : null,
                ]) !!};
                const chatbotToggle = document.getElementById('chatbot-toggle');
                const chatbotWindow = document.getElementById('chatbot-window');
                const chatbotClose = document.getElementById('chatbot-close');
                const chatbotQuestions = document.querySelectorAll('.chatbot-q');
                const chatbotMessages = document.getElementById('chatbot-messages');
                const chatbotQuestionsList = document.getElementById('chatbot-questions');
                chatbotToggle.addEventListener('click', () => {
                    chatbotWindow.style.display = chatbotWindow.style.display === 'none' ? 'block' : 'none';
                });
                chatbotClose.addEventListener('click', () => {
                    chatbotWindow.style.display = 'none';
                });
                chatbotQuestions.forEach(btn => {
                    btn.style.display = 'block';
                    btn.style.width = '100%';
                    btn.style.textAlign = 'left';
                    btn.style.background = '#f3f4f6';
                    btn.style.border = 'none';
                    btn.style.padding = '0.5rem 0.75rem';
                    btn.style.marginBottom = '0.5rem';
                    btn.style.borderRadius = '6px';
                    btn.style.cursor = 'pointer';
                    btn.addEventListener('click', function() {
                        const question = this.textContent;
                        const answer = this.getAttribute('data-a');
                        // Show user message
                        chatbotMessages.innerHTML += `<div style='display:flex;justify-content:flex-end;'><div style='background:#2563eb;color:#fff;padding:8px 14px;border-radius:16px 16px 2px 16px;max-width:80%;margin:0.5rem 0 0.5rem 0.5rem;'>${question}</div></div>`;
                        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
                        // Simulate bot typing...
                        setTimeout(() => {
                            chatbotMessages.innerHTML += `<div style='display:flex;justify-content:flex-start;'><div style='background:#f3f4f6;color:#222;padding:8px 14px;border-radius:16px 16px 16px 2px;max-width:80%;margin:0 0 1rem 0.5rem;'><b>Bot:</b> ${answer}</div></div>`;
                            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
                        }, 700);
                    });
                });
            </script>
        </main>

        <!-- Footer -->
        <footer class="bg-blue-900 text-white mt-8">
            <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row justify-between items-center">
                
                <div class="text-sm mb-2 md:mb-0">
                    &copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('privacy.policy') }}" class="hover:underline hover:text-blue-200">Privacy Policy</a>
                    <a href="{{ route('terms.use') }}" class="hover:underline hover:text-blue-200">Terms of Service</a>
                    <a href="{{ route('contact') }}" class="hover:underline hover:text-blue-200">Contact</a>
                </div>
            </div>
        </footer>

        <!-- Custom JavaScript -->
        <script src="{{ asset('js/carousel.js') }}"></script>
        @yield('scripts')
        @include('components.confirm-modal')
    </body>
</html>
