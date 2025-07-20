<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            background: linear-gradient(to right, #f5f5f5, #fff);
            min-height: 100vh;
        }
        .header-topbar {
            background: #f5f5f5;
            border-bottom: 1px solid #ececec;
            font-size: 0.95rem;
            color: #2563eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 32px 4px 16px;
        }
        .header-topbar .caramel-mini {
            font-weight: bold;
            color: #222;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-main {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 32px 18px 32px;
            border-bottom: 3px solid #2563eb;
        }
        .header-logo {
            display: flex;
            align-items: center;
            font-size: 1.4rem;
            font-weight: bold;
            color: #222;
            letter-spacing: 2px;
            flex-shrink: 0;
        }
        .header-logo .apex-logo {
            margin-left: 10px;
            width: 36px;
            height: 36px;
            object-fit: contain;
        }
        .header-search {
            flex: 0 1 320px;
            display: flex;
            align-items: center;
            margin: 0 18px;
            max-width: 320px;
        }
        .header-searchbox {
            flex: 1;
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
            background: #fff;
            height: 44px;
        }
        .header-searchbox input {
            border: none;
            outline: none;
            font-size: 1.1rem;
            padding: 0 12px;
            flex: 1;
            background: transparent;
        }
        .header-searchbox .search-icon {
            margin-left: 10px;
            color: #888;
            font-size: 1.2rem;
        }
        .header-searchbtn {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 0 4px 4px 0;
            font-size: 1.1rem;
            font-weight: 500;
            padding: 0 28px;
            height: 44px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .header-searchbtn:hover {
            background: #1e40af;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 28px;
            position: relative;
        }
        .account-dropdown {
            position: absolute;
            top: 38px;
            right: 0;
            background: #fff;
            border: 1px solid #ececec;
            border-radius: 6px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            min-width: 180px;
            z-index: 100;
            display: none;
            flex-direction: column;
            padding: 8px 0;
        }
        .account-dropdown.show {
            display: flex;
        }
        .account-dropdown a {
            color: #222;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .account-dropdown a:hover {
            background: #f5f5f5;
        }
        .header-action {
            display: flex;
            align-items: center;
            color: #222;
            font-size: 1.08rem;
            cursor: pointer;
            gap: 6px;
            text-decoration: none;
        }
        .header-action svg {
            width: 22px;
            height: 22px;
            vertical-align: middle;
        }
        .header-action .dropdown {
            font-size: 1.1rem;
            margin-left: 2px;
        }
        .header-cart {
            font-weight: 500;
        }
        .header-orange-bar {
            height: 6px;
            background: #2563eb;
            width: 100%;
        }
        /* Carousel */
        .carousel-container {
            width: 100%;
            max-width: 900px;
            margin: 32px auto 0 auto;
            position: relative;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 32px rgba(37,99,235,0.07);
            min-height: 240px;
            height: 240px;
        }
        .carousel-slide {
            width: 100%;
            height: 240px;
            background-size: cover;
            background-position: center;
            position: absolute;
            top: 0; left: 0;
            opacity: 0;
            z-index: 1;
            display: flex;
            align-items: center; /* Center vertically */
            justify-content: center; /* Center horizontally */
            transform: translateX(100%);
            transition: transform 0.7s cubic-bezier(.77,0,.18,1), opacity 0.3s;
        }
        .carousel-slide.active {
            opacity: 1;
            z-index: 2;
            transform: translateX(0);
            transition: transform 0.7s cubic-bezier(.77,0,.18,1), opacity 0.3s;
        }
        .carousel-slide.slide-out-left {
            opacity: 0;
            z-index: 1;
            transform: translateX(-100%);
            transition: transform 0.7s cubic-bezier(.77,0,.18,1), opacity 0.3s;
        }
        .carousel-slide.slide-in-right {
            opacity: 1;
            z-index: 2;
            transform: translateX(0);
            transition: transform 0.7s cubic-bezier(.77,0,.18,1), opacity 0.3s;
        }
        .carousel-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(120deg, rgba(255,255,255,0.05) 0%, rgba(37,99,235,0.13) 100%);
            z-index: 1;
        }
        .carousel-content {
            position: relative;
            z-index: 2;
            color: #222;
            background: none;
            min-width: unset;
            max-width: 90%;
            margin: 0;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .carousel-text-bg {
            background: rgba(255,255,255,0.55);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border-radius: 18px;
            padding: 18px 32px 14px 32px;
            display: inline-block;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            max-width: 90vw;
        }
        .carousel-title {
            font-size: 1.7rem;
            font-weight: bold;
            margin-bottom: 8px;
            color: #222;
            text-shadow: 0 2px 8px rgba(255,255,255,0.7);
        }
        .carousel-desc {
            font-size: 1.08rem;
            color: #444;
            text-shadow: 0 2px 8px rgba(255,255,255,0.7);
        }
        .carousel-indicators {
            position: absolute;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }
        .carousel-indicator {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #2563eb;
            opacity: 0.6;
            cursor: pointer;
            transition: opacity 0.2s, background 0.2s;
        }
        .carousel-indicator.active {
            background: #2563eb;
            opacity: 1;
        }
        /* Products */
        .products-section {
            max-width: 1200px;
            margin: 48px auto 0 auto;
            padding: 0 16px;
        }
        .products-title {
            font-size: 2rem;
            font-weight: bold;
            color: #222;
            margin-bottom: 24px;
            text-align: left;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 32px;
        }
        .product-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 24px 18px 18px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.18s, transform 0.18s;
            position: relative;
        }
        .product-card:hover {
            box-shadow: 0 8px 32px rgba(37,99,235,0.13);
            transform: translateY(-4px) scale(1.02);
        }
        .product-image {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 16px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }
        .product-name {
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #222;
            text-align: center;
        }
        .product-price {
            color: #2563eb;
            font-size: 1.08rem;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .product-desc {
            color: #666;
            font-size: 0.98rem;
            margin-bottom: 12px;
            text-align: center;
        }
        .product-card form {
            margin-top: 8px;
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .product-card input[type="number"] {
            width: 48px;
            padding: 4px 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        .product-card button {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 6px 16px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.18s;
        }
        .product-card button:hover {
            background: #1e40af;
        }
        /* Toast */
        #cart-toast {
            position: fixed;
            top: 32px;
            right: 32px;
            z-index: 9999;
            min-width: 220px;
            max-width: 320px;
            background: #fff;
            border-left: 6px solid #2563eb;
            box-shadow: 0 4px 24px rgba(0,0,0,0.13);
            padding: 18px 28px 18px 18px;
            border-radius: 8px;
            font-size: 1.08rem;
            color: #222;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        /* Footer */
        .customer-footer {
            background: #222;
            color: #fff;
            padding: 32px 0 18px 0;
            margin-top: 48px;
        }
        .footer-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            margin-bottom: 16px;
            justify-content: center;
        }
        .footer-links a {
            color: #fff;
            text-decoration: none;
            font-size: 1.08rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: #2563eb;
            text-decoration: underline;
        }
        .footer-copy {
            color: #bbb;
            font-size: 0.98rem;
            text-align: center;
        }
        @media (max-width: 600px) {
            .header-main { padding: 8px 2vw; }
            .header-topbar { padding: 4px 2vw 4px 2vw; }
            .header-search { max-width: 98vw; min-width: 0; }
            .header-actions { gap: 10px; }
            .carousel-content { margin: 0 0 8px 2px; padding: 14px 8px 18px 8px; }
            .carousel-slide, .carousel-container { min-height: 140px; height: 140px; }
        }
    </style>
</head>
<body>
    <div class="header-orange-bar"></div>
    <div class="header-topbar" style="position: relative;">
        <div>Enjoy Caramel Yoghurt</div>
        <!-- Notification Bell -->
        @include('components.notification-bell')
        <div class="caramel-mini">
            <span style="color:#222; font-weight:bold;">CARAMEL</span>
            <span style="color:#bfa76a; font-size:1em; font-weight:normal;">FRESH</span>
            <span style="color:#bfa76a; font-size:1em; font-weight:normal;">DAIRY</span>
        </div>
    </div>
    <div class="header-main">
        <div class="header-logo">
            <img src="{{ asset('images/apex-logo.png') }}" alt="Apex Logo" class="apex-logo" />CARAMEL YOGHURT
        </div>
        <form class="header-search" method="GET" action="{{ route('dashboard.customer') }}">
            <div class="header-searchbox">
                <span class="search-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" placeholder="Search yoghurt, dairy products, and more" value="{{ request('search', $search ?? '') }}">
            </div>
            <button class="header-searchbtn" type="submit">Search</button>
        </form>
        <div class="header-actions">
            <div class="header-action account-action" tabindex="0" style="position:relative; margin-left: 18px;"> <!-- Added margin to separate from search bar -->
                @php $user = Auth::user(); @endphp
                <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #2563eb;vertical-align:middle;cursor:pointer;" />
                <div class="account-dropdown" id="accountDropdown">
                    <a href="{{ route('profile.edit') }}"><svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg> Update Profile</a>
                    <a href="{{ route('customer.orders.index') }}"><svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4"/></svg> View Orders</a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7"/><path d="M3 21V3a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v4"/></svg> Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                </div>
            </div>
            <a class="header-action" href="{{ route('privacy.policy') }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" font-size="12" fill="#222">?</text></svg>
                Privacy Policy
            </a>
            <a class="header-action header-cart" href="{{ route('cart.index') }}" style="position:relative;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Cart
                @php
                    use Illuminate\Support\Facades\Auth;
                    if(Auth::check()) {
                        $cartCount = \App\Models\CartItem::where('user_id', Auth::id())->sum('quantity');
                    } else {
                        $cart = session('cart', []);
                        $cartCount = array_sum(array_column($cart, 'quantity'));
                    }
                @endphp
                @if($cartCount > 0)
                    <span style="position:absolute;top:-8px;right:-12px;background:#2563eb;color:#fff;font-size:0.98rem;font-weight:600;padding:2px 8px;border-radius:16px;min-width:24px;text-align:center;box-shadow:0 2px 8px rgba(37,99,235,0.13);">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
    </div>
    <div class="header-orange-bar"></div>
    
    <div class="carousel-container" id="retailer-carousel">
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/fresh-milk.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-text-bg">
                    <div class="carousel-title">Dashboard Home</div>
                    <div class="carousel-desc">Overview of your store's performance and quick links.</div>
                </div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/production.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-text-bg">
                    <div class="carousel-title">View Stock</div>
                    <div class="carousel-desc">Check current inventory and stock levels.</div>
                </div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/quality.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-text-bg">
                    <div class="carousel-title">Sales Reports</div>
                    <div class="carousel-desc">View sales analytics and download reports.</div>
                </div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/transportation.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-text-bg">
                    <div class="carousel-title">Shop Products</div>
                    <div class="carousel-desc">Browse and add products to your cart.</div>
                </div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/retail.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-text-bg">
                    <div class="carousel-title">Deliveries</div>
                    <div class="carousel-desc">Track and manage your deliveries.</div>
                </div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/production.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-text-bg">
                    <div class="carousel-title">View Transactions</div>
                    <div class="carousel-desc">Review your transaction history.</div>
                </div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/fresh-milk.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-text-bg">
                    <div class="carousel-title">Chat</div>
                    <div class="carousel-desc">Chat with admin or vendor for support.</div>
                </div>
            </div>
        </div>
        <div class="carousel-indicators">
            <button class="carousel-indicator" data-slide="0"></button>
            <button class="carousel-indicator" data-slide="1"></button>
            <button class="carousel-indicator" data-slide="2"></button>
            <button class="carousel-indicator" data-slide="3"></button>
            <button class="carousel-indicator" data-slide="4"></button>
            <button class="carousel-indicator" data-slide="5"></button>
            <button class="carousel-indicator" data-slide="6"></button>
        </div>
    </div>
    <!-- Products Section -->
    <div class="products-section">
        <div class="products-title">Our Products</div>
        <div class="products-grid">
            @foreach($products as $product)
                <div class="product-card">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->product_name }}" class="product-image">
                    @else
                        <div class="product-image" style="color:#bbb; font-size:2.5rem;">🧴</div>
                    @endif
                    <div class="product-name">{{ $product->product_name }}</div>
                    <div class="product-price">{{ $product->selling_price }}</div>
                    <div class="product-desc">{{ Str::limit($product->description, 90) }}</div>
                    @if(Auth::check())
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-2">
                            @csrf
                            <input type="number" name="quantity" value="1" min="1" class="w-16 p-1 border rounded mr-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">Add to Cart</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @if(session('success'))
        <div id="cart-toast" style="position:fixed;top:32px;right:32px;z-index:9999;min-width:220px;max-width:320px;background:#fff;border-left:6px solid #2563eb;box-shadow:0 4px 24px rgba(0,0,0,0.13);padding:18px 28px 18px 18px;border-radius:8px;font-size:1.08rem;color:#222;display:flex;align-items:center;gap:12px;">
            <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12" fill="#2563eb"/><path d="M8 12.5l3 3 5-5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.style.display='none'" style="background:none;border:none;font-size:1.3rem;line-height:1;color:#888;cursor:pointer;margin-left:auto;">&times;</button>
        </div>
        <script>
            setTimeout(function(){
                var toast = document.getElementById('cart-toast');
                if(toast) toast.style.display = 'none';
            }, 3500);
        </script>
    @endif
    <footer class="customer-footer">
    <div class="footer-container">
        <div class="footer-links">
            <a href="/dashboard/customer">Home</a>
            <a href="/dashboard/customer">Shop</a>
            <a href="{{ route('customer.orders.index') }}">Orders</a>
            <a href="{{ route('privacy.policy') }}">Privacy Policy</a>
            <a href="#">Contact</a>
        </div>
        <div class="footer-copy">&copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.</div>
            </div>
    </footer>
    
    <!-- Laravel Echo and Pusher for Real-time Notifications -->
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Initialize Laravel Echo for real-time notifications
        window.Laravel = {!! json_encode([
            'userId' => auth()->check() ? auth()->user()->id : null,
        ]) !!};
    </script>
    
    <script>
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
        const slides = document.querySelectorAll('#retailer-carousel .carousel-slide');
        const indicators = document.querySelectorAll('#retailer-carousel .carousel-indicator');
        let current = 0;
        let prev = 0;
        function showSlide(idx) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active', 'slide-in-right', 'slide-out-left');
                slide.style.zIndex = 1;
                // Reset all slides to off-screen right except the current and previous
                if (i !== idx && i !== prev) {
                    slide.style.transform = 'translateX(100%)';
                    slide.style.opacity = 0;
                }
            });
            if (slides[prev] && prev !== idx) {
                slides[prev].classList.add('slide-out-left');
                slides[prev].style.zIndex = 1;
            }
            if (slides[idx]) {
                slides[idx].classList.add('slide-in-right', 'active');
                slides[idx].style.zIndex = 2;
                slides[idx].style.opacity = 1;
                slides[idx].style.transform = 'translateX(0)';
            }
            indicators.forEach((ind, i) => {
                ind.classList.toggle('active', i === idx);
            });
            setTimeout(() => {
                slides.forEach((slide, i) => {
                    if (i !== idx) {
                        slide.classList.remove('active', 'slide-in-right', 'slide-out-left');
                        slide.style.zIndex = 1;
                        slide.style.opacity = 0;
                        slide.style.transform = 'translateX(100%)';
                    }
                });
            }, 700);
            prev = idx;
        }
        function nextSlide() {
            let next = (current + 1) % slides.length;
            showSlide(next);
            current = next;
        }
        indicators.forEach((btn, i) => {
            btn.addEventListener('click', () => {
                showSlide(i);
                current = i;
            });
        });
        // Initialize all slides off-screen except the first
        slides.forEach((slide, i) => {
            if (i !== 0) {
                slide.style.transform = 'translateX(100%)';
                slide.style.opacity = 0;
            } else {
                slide.classList.add('active');
                slide.style.transform = 'translateX(0)';
                slide.style.opacity = 1;
            }
        });
        showSlide(current);
        setInterval(nextSlide, 4000);

    // Account dropdown logic
    const accountAction = document.querySelector('.account-action');
    const accountDropdown = document.getElementById('accountDropdown');
    accountAction.addEventListener('click', function(e) {
        e.stopPropagation();
        accountDropdown.classList.toggle('show');
    });
    document.addEventListener('click', function(e) {
        if (!accountAction.contains(e.target)) {
            accountDropdown.classList.remove('show');
        }
    });
    accountAction.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') accountDropdown.classList.remove('show');
    });
    </script>
</body>
</html> 