<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items Removed from Cart - Caramel Yoghurt</title>
    <link href="https://fonts.googleapis.com/css?family=Segoe+UI:400,600&display=swap" rel="stylesheet">
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
    </style>
</head>
<body>
    <div class="header-orange-bar"></div>
    <div class="header-topbar">
        <div>Enjoy Caramel Yoghurt</div>
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
            {{-- Help link removed to fix RouteNotFoundException --}}
            <a class="header-action header-cart" href="{{ route('cart.index') }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Cart
            </a>
        </div>
    </div>
    <div class="header-orange-bar"></div>
    <div style="display:flex;align-items:center;justify-content:center;min-height:60vh;">
        <div class="removed-card" style="background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(37,99,235,0.10); padding:22px 20px 20px 20px; max-width:350px; width:100%; text-align:center; border:1.5px solid #f3f4f6;">
            <div class="icon" style="margin-bottom:12px;">
                <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" stroke="#b91c1c" stroke-width="2" fill="#fee2e2"/>
                    <path d="M12 8v4m0 4h.01" stroke="#b91c1c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2 style="color:#b91c1c;font-size:1.18rem;font-weight:700;margin-bottom:0.7rem;">Some items were removed from your cart</h2>
            <p style="font-size:1rem;margin-bottom:0.7rem;">The following items are out of stock and have been removed:</p>
            <ul style="list-style:none;padding:0;margin:0 0 1.1rem 0;">
                @foreach($removed as $item)
                    <li style="background:#fee2e2;color:#b91c1c;margin-bottom:6px;padding:7px 0;border-radius:6px;font-size:0.98rem;font-weight:500;">{{ $item }}</li>
                @endforeach
            </ul>
            <div class="actions" style="display:flex;gap:10px;justify-content:center;margin-top:0.7rem;">
                <a href="{{ route('checkout.index') }}" style="padding:8px 16px;border-radius:6px;font-size:0.98rem;font-weight:600;border:none;cursor:pointer;transition:background 0.18s;text-decoration:none;display:inline-block;background:linear-gradient(135deg,#2563eb 0%,#1e40af 100%);color:#fff;">Proceed with Available Items</a>
                <a href="{{ route('cart.index') }}" style="padding:8px 16px;border-radius:6px;font-size:0.98rem;font-weight:600;border:1.5px solid #d1d5db;cursor:pointer;transition:background 0.18s;text-decoration:none;display:inline-block;background:#f3f4f6;color:#222;">Back to Cart</a>
            </div>
        </div>
    </div>
    <footer class="customer-footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="/dashboard/customer">Home</a>
                <a href="/dashboard/customer">Shop</a>
                <a href="{{ route('customer.orders.index') }}">Orders</a>
                {{-- Help link removed from footer --}}
                <a href="{{ route('privacy.policy') }}">Privacy Policy</a>
                <a href="#">Terms</a>
                <a href="#">Contact</a>
            </div>
            <div class="footer-copy">&copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.</div>
        </div>
    </footer>
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
</script>
</body>
</html> 