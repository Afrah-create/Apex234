<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            color: #ff9900;
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
            border-bottom: 3px solid #ff9900;
        }
        .header-logo {
            display: flex;
            align-items: center;
            font-size: 2.2rem;
            font-weight: bold;
            color: #222;
            letter-spacing: 2px;
        }
        .header-logo .star {
            color: #ff9900;
            margin-left: 2px;
            font-size: 1.5rem;
        }
        .header-search {
            flex: 1;
            display: flex;
            align-items: center;
            margin: 0 32px;
            max-width: 600px;
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
            background: #ff9900;
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
            background: #e67c00;
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
            background: #ff9900;
            width: 100%;
        }
        .carousel-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            height: 340px;
        }
        .carousel-slide {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.10);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: translateX(100%);
            transition: transform 0.7s cubic-bezier(0.77,0,0.175,1), opacity 0.5s;
            z-index: 1;
            background-size: cover;
            background-position: center;
            color: #fff;
        }
        .carousel-slide .carousel-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.55);
            border-radius: 20px;
            z-index: 1;
        }
        .carousel-slide .carousel-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .carousel-slide.active {
            opacity: 1;
            transform: translateX(0);
            z-index: 2;
        }
        .carousel-slide.slide-out-left {
            opacity: 0;
            transform: translateX(-100%);
            z-index: 1;
        }
        .carousel-slide.slide-in-right {
            opacity: 1;
            transform: translateX(0);
            z-index: 2;
        }
        .carousel-icon {
            font-size: 4.2rem;
            margin-bottom: 28px;
            font-weight: bold;
            text-shadow: 0 2px 8px rgba(0,0,0,0.45);
        }
        .carousel-title {
            font-size: 2.1rem;
            font-weight: 900;
            margin-bottom: 14px;
            color: #fff;
            text-align: center;
            text-shadow: 0 2px 8px rgba(0,0,0,0.45);
        }
        .carousel-desc {
            color: #fff;
            font-size: 1.18rem;
            text-align: center;
            max-width: 85%;
            font-weight: 500;
            text-shadow: 0 2px 8px rgba(0,0,0,0.45);
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
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #d1d5db;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .carousel-indicator.active {
            background: #ff9900;
        }
        @media (max-width: 900px) {
            .header-main { flex-direction: column; align-items: stretch; padding: 12px 8px; }
            .header-search { margin: 12px 0; }
        }
        @media (max-width: 600px) {
            .header-main { padding: 8px 2vw; }
            .header-topbar { padding: 4px 2vw 4px 2vw; }
            .header-search { max-width: 98vw; }
        }
        .products-section {
            max-width: 1200px;
            margin: 48px auto 32px auto;
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
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px 24px;
        }
        .product-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 18px 18px 22px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.2s;
        }
        .product-card:hover {
            box-shadow: 0 6px 24px rgba(255,153,0,0.13);
        }
        .product-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            background: #f5f5f5;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-name {
            font-size: 1.15rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
            text-align: center;
        }
        .product-price {
            color: #ff9900;
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .product-desc {
            color: #555;
            font-size: 0.98rem;
            text-align: center;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="header-orange-bar"></div>
    <div class="header-topbar">
        <div><span style="color:#ff9900; font-size:1.1em;">&#127849;</span> Sell with Caramel Yoghurt</div>
        <div class="caramel-mini">
            <span style="color:#222; font-weight:bold;">CARAMEL</span>
            <span style="color:#ff9900; font-size:1.1em;">&#127849;</span>
            <span style="color:#bfa76a; font-size:1em; font-weight:normal;">FRESH</span>
            <span style="color:#bfa76a; font-size:1em; font-weight:normal;">DAIRY</span>
        </div>
    </div>
    <div class="header-main">
        <div class="header-logo">
            CARAMEL YOGHURT <span class="star">&#127849;</span>
        </div>
        <form class="header-search" onsubmit="return false;">
            <div class="header-searchbox">
                <span class="search-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" placeholder="Search yoghurt, dairy products, and more">
            </div>
            <button class="header-searchbtn" type="submit">Search</button>
        </form>
        <div class="header-actions">
            <div class="header-action account-action" tabindex="0" style="position:relative;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 8-4 8-4s8 0 8 4"/></svg>
                Account <span class="dropdown">&#9662;</span>
                <div class="account-dropdown" id="accountDropdown">
                    <a href="{{ route('profile.edit') }}"><svg width="18" height="18" fill="none" stroke="#ff9900" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg> Update Profile</a>
                    <a href="{{ route('customer.orders.index') }}"><svg width="18" height="18" fill="none" stroke="#ff9900" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4"/></svg> View Orders</a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><svg width="18" height="18" fill="none" stroke="#ff9900" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7"/><path d="M3 21V3a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v4"/></svg> Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                </div>
            </div>
            <a class="header-action" href="#">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" font-size="12" fill="#222">?</text></svg>
                Help <span class="dropdown">&#9662;</span>
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
                    <span style="position:absolute;top:-8px;right:-12px;background:#ff9900;color:#fff;font-size:0.98rem;font-weight:600;padding:2px 8px;border-radius:16px;min-width:24px;text-align:center;box-shadow:0 2px 8px rgba(255,153,0,0.13);">{{ $cartCount }}</span>
                @endif
            </a>
        </div>
    </div>
    <div class="header-orange-bar"></div>
    
    <div class="carousel-container" id="retailer-carousel">
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/fresh-milk.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-title">Dashboard Home</div>
                <div class="carousel-desc">Overview of your store's performance and quick links.</div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/production.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-title">View Stock</div>
                <div class="carousel-desc">Check current inventory and stock levels.</div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/quality.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-title">Sales Reports</div>
                <div class="carousel-desc">View sales analytics and download reports.</div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/transportation.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-title">Shop Products</div>
                <div class="carousel-desc">Browse and add products to your cart.</div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/retail.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-title">Deliveries</div>
                <div class="carousel-desc">Track and manage your deliveries.</div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/production.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-title">View Transactions</div>
                <div class="carousel-desc">Review your transaction history.</div>
            </div>
        </div>
        <div class="carousel-slide" style="background-image: url('{{ asset('images/carousel/fresh-milk.jpg') }}');">
            <div class="carousel-overlay"></div>
            <div class="carousel-content">
                <div class="carousel-title">Chat</div>
                <div class="carousel-desc">Chat with admin or vendor for support.</div>
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
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">Add to Cart</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @if(session('success'))
        <div id="cart-toast" style="position:fixed;top:32px;right:32px;z-index:9999;min-width:220px;max-width:320px;background:#fff;border-left:6px solid #38c172;box-shadow:0 4px 24px rgba(0,0,0,0.13);padding:18px 28px 18px 18px;border-radius:8px;font-size:1.08rem;color:#222;display:flex;align-items:center;gap:12px;">
            <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12" fill="#38c172"/><path d="M8 12.5l3 3 5-5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
            <a href="#">Help</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms</a>
            <a href="#">Contact</a>
        </div>
        <div class="footer-copy">&copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.</div>
    </div>
</footer>
<style>
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
        color: #ff9900;
        text-decoration: none;
        font-size: 1.08rem;
        font-weight: 500;
        transition: color 0.2s;
    }
    .footer-links a:hover {
        color: #fff;
        text-decoration: underline;
    }
    .footer-copy {
        color: #bbb;
        font-size: 0.98rem;
        text-align: center;
    }
</style>
    <script>
        const slides = document.querySelectorAll('#retailer-carousel .carousel-slide');
        const indicators = document.querySelectorAll('#retailer-carousel .carousel-indicator');
        let current = 0;
        let prev = 0;
        function showSlide(idx) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active', 'slide-in-right', 'slide-out-left');
                slide.style.zIndex = 1;
            });
            if (slides[prev]) slides[prev].classList.add('slide-out-left');
            if (slides[idx]) {
                slides[idx].classList.add('slide-in-right', 'active');
                slides[idx].style.zIndex = 2;
            }
            indicators.forEach((ind, i) => {
                ind.classList.toggle('active', i === idx);
            });
            setTimeout(() => {
                slides.forEach((slide, i) => {
                    if (i !== idx) {
                        slide.classList.remove('active', 'slide-in-right', 'slide-out-left');
                        slide.style.zIndex = 1;
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