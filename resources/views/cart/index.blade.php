<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cart - Caramel Yoghurt</title>
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
        /* Cart Card Centering and Padding */
        .cart-empty-outer {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 170px);
            background: transparent;
            padding-top: 48px;
        }
        .cart-empty-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            border: 1.5px solid #f2f2f2;
            padding: 20px 40px 20px 40px;
            max-width: 700px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .cart-empty-title {
            font-size: 2rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        .cart-empty-desc {
            color: #555;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 1.13rem;
        }
        .cart-empty-btn {
            background: #2563eb;
            color: #fff;
            font-size: 1.18rem;
            font-weight: 500;
            padding: 14px 38px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(37,99,235,0.13);
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .cart-empty-btn:hover {
            background: #1e40af;
        }
        @media (max-width: 600px) {
            .cart-empty-card {
                padding: 32px 8vw 28px 8vw;
            }
        }
        .cart-table {
            width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
        }
        .cart-table th, .cart-table td {
            padding: 14px 12px;
            text-align: left;
        }
        .cart-table th {
            background: #fafafa;
            color: #222;
            font-size: 1.08rem;
            font-weight: 600;
            border-bottom: 2px solid #f2f2f2;
        }
        .cart-table tbody tr {
            border-bottom: 1px solid #f2f2f2;
            transition: background 0.18s;
        }
        .cart-table tbody tr:nth-child(even) {
            background: #fcfcfc;
        }
        .cart-table tbody tr:hover {
            background: #e6f0ff;
        }
        .cart-table img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
            background: #f5f5f5;
        }
        .cart-table .cart-action-btn {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 6px 16px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.18s;
        }
        .cart-table .cart-action-btn:hover {
            background: #1e40af;
        }
        .cart-table input[type='number'] {
            width: 56px;
            padding: 4px 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            margin-right: 6px;
        }
        
        /* Checkout Buttons */
        .checkout-btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 16px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(37,99,235,0.3);
            position: relative;
            overflow: hidden;
        }
        .checkout-btn-primary:hover {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37,99,235,0.4);
            color: #fff;
            text-decoration: none;
        }
        .checkout-btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(37,99,235,0.3);
        }
        
        .checkout-btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 16px 32px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
            position: relative;
            overflow: hidden;
        }
        .checkout-btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
            color: #fff;
            text-decoration: none;
        }
        .checkout-btn-secondary:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(108, 117, 125, 0.3);
        }
        .cart-total-row td {
            font-size: 1.15rem;
            font-weight: 600;
            color: #222;
            text-align: right;
            padding-top: 18px;
            border: none;
        }
        @media (max-width: 700px) {
            .cart-table th, .cart-table td { padding: 8px 4px; font-size: 0.98rem; }
            .cart-table img { width: 40px; height: 40px; }
        }
        
        @media (max-width: 768px) {
            .checkout-btn-primary, .checkout-btn-secondary {
                padding: 14px 24px;
                font-size: 1rem;
                width: 100%;
                justify-content: center;
                margin-bottom: 12px;
            }
            .mt-8.flex.justify-end.gap-6 {
                flex-direction: column;
                gap: 12px;
            }
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
                @php $user = Auth::user(); @endphp
                <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #2563eb;vertical-align:middle;cursor:pointer;" />
                <div class="account-dropdown" id="accountDropdown">
                    <a href="{{ route('profile.edit') }}"><svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg> Update Profile</a>
                    <a href="{{ route('customer.orders.index') }}"><svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4"/></svg> View Orders</a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7"/><path d="M3 21V3a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v4"/></svg> Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                </div>
            </div>
            <a class="header-action header-cart" href="{{ route('cart.index') }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Cart
            </a>
        </div>
    </div>
    <div class="header-orange-bar"></div>

    <div class="container mx-auto py-8">
        @if(session('success'))
            <div class="mb-4 text-green-700 bg-green-100 p-2 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div id="cart-error-toast" class="fixed top-6 right-6 z-50 flex items-center gap-3 p-4 rounded-lg bg-red-50 border border-red-200 shadow-lg animate-fade-in-up" style="min-width: 280px; max-width: 90vw;">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-red-500">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="#fee2e2"/>
                    <path d="M12 8v4m0 4h.01" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-red-700 font-semibold text-base">{{ session('error') }}</span>
            </div>
            <script>
                setTimeout(function() {
                    var toast = document.getElementById('cart-error-toast');
                    if (toast) toast.style.display = 'none';
                }, 3500);
            </script>
        @endif
        @if($cartItems->count())
        <div class="overflow-x-auto">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                    <tr>
                        <td>
                            @if($item['product']->image_path)
                                <img src="{{ asset('storage/' . $item['product']->image_path) }}" alt="{{ $item['product']->product_name }}">
                            @else
                                <div style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;background:#f5f5f5;border-radius:8px;font-size:1.5rem;color:#bbb;">🥛</div>
                            @endif
                        </td>
                        <td>{{ $item['product']->product_name }}</td>
                        <td>{{ $item['product']->selling_price }}</td>
                        <td>
                            <form action="{{ route('cart.update', $item['product']) }}" method="POST" style="display:flex;align-items:center;">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1">
                                <button type="submit" class="cart-action-btn">Update</button>
                            </form>
                        </td>
                        <td>{{ $item['subtotal'] }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $item['product']) }}" method="POST">
                                @csrf
                                <button type="submit" class="cart-action-btn" style="background:#e3342f;">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 text-right">
            <span class="text-xl font-semibold">Total: {{ $total }}</span>
        </div>
        <div class="mt-8 flex justify-end gap-6">
            <a href="/dashboard/customer" class="checkout-btn-secondary">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Continue Shopping
            </a>
            @if(Auth::check() && Auth::user()->retailer)
                <a href="{{ route('retailer.checkout') }}" class="checkout-btn-primary">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    Proceed to Checkout
                </a>
            @else
                <a href="{{ route('cart.proceedToCheckout') }}" class="checkout-btn-primary">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                    Proceed to Checkout
                </a>
            @endif
        </div>
        @else
            <div class="cart-empty-outer">
                <div class="cart-empty-card">
                    <div class="mb-6">
                        <svg width="110" height="110" fill="none" viewBox="0 0 110 110">
                            <circle cx="55" cy="55" r="55" fill="#fafafa"/>
                            <g>
                                <rect x="33" y="40" width="44" height="28" rx="7" fill="#2563eb"/>
                                <circle cx="44" cy="75" r="7" fill="#2563eb"/>
                                <circle cx="66" cy="75" r="7" fill="#2563eb"/>
                                <polygon points="38,40 72,40 77,60 33,60" fill="#fff" opacity=".18"/>
                                <circle cx="55" cy="54" r="8" fill="#fff" opacity=".18"/>
                            </g>
                        </svg>
                    </div>
                    <div class="cart-empty-title">Your cart is empty!</div>
                    <div class="cart-empty-desc">Browse our categories and discover our best deals!</div>
                    <a href="/dashboard/customer" class="cart-empty-btn" onclick="window.location.href='/dashboard/customer'; return false;">Start Shopping</a>
                </div>
            </div>
        @endif
    </div>
    <footer class="customer-footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="/dashboard/customer">Home</a>
                <a href="/dashboard/customer">Shop</a>
                <a href="{{ route('customer.orders.index') }}">Orders</a>
                <a href="{{ route('privacy.policy') }}">Privacy Policy</a>
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
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.5s cubic-bezier(0.4,0,0.2,1);
        }
    </style>
    <script>
// Account dropdown logic
const accountAction = document.querySelector('.account-action');
const accountDropdown = document.getElementById('accountDropdown');
if (accountAction && accountDropdown) {
    accountAction.addEventListener('click', function(e) {
        e.stopPropagation();
        accountDropdown.classList.toggle('show');
    });
    document.addEventListener('click', function(e) {
        if (!accountAction.contains(e.target)) {
            accountDropdown.classList.remove('show');
        }
    });
}
 // Chat badge logic: fetch unread count from backend
(function(){
    function updateChatBadge() {
        fetch('/chat/unread-total')
            .then(res => res.json())
            .then(data => {
                var badge = document.getElementById('chat-badge');
                if (badge) {
                    var count = data.unread_count || 0;
                    if (count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.style.display = '';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
    }
    setInterval(updateChatBadge, 5000);
    document.addEventListener('DOMContentLoaded', updateChatBadge);
})();
</script>
>>>>>>> 0d7f5e4d662b3687a3240b12eed621dd0f0f28fc
</body>
</html> 