<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Caramel Yoghurt</title>
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
            font-size: 1.4rem;
            font-weight: bold;
            color: #222;
            letter-spacing: 2px;
            flex-shrink: 0;
        }
        .header-logo .apex-logo {
            margin-right: 10px;
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
            min-width: unset;
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
        @media (max-width: 900px) {
            .header-main { flex-direction: column; align-items: stretch; padding: 12px 8px; }
            .header-search { margin: 12px 0; width: 100%; }
            .header-actions { justify-content: flex-end; gap: 18px; }
        }
        @media (max-width: 600px) {
            .header-main { padding: 8px 2vw; }
            .header-topbar { padding: 4px 2vw 4px 2vw; }
            .header-search { max-width: 98vw; min-width: 0; }
            .header-actions { gap: 10px; }
        }
    </style>
</head>
<body>
    <div class="header-orange-bar"></div>
    <div class="header-topbar">
        <div>Sell with Caramel Yoghurt</div>
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
                <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #ff9900;vertical-align:middle;cursor:pointer;" />
                <div class="account-dropdown" id="accountDropdown">
                    <a href="{{ route('profile.edit') }}"><svg width="18" height="18" fill="none" stroke="#ff9900" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg> Update Profile</a>
                    <a href="{{ route('customer.orders.index') }}"><svg width="18" height="18" fill="none" stroke="#ff9900" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4"/></svg> View Orders</a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><svg width="18" height="18" fill="none" stroke="#ff9900" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7"/><path d="M3 21V3a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v4"/></svg> Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                </div>
            </div>
            <a class="header-action" href="#">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" font-size="12" fill="#222">?</text></svg>
                Help <svg class="dropdown" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left:2px;vertical-align:middle;"><path d="M6 8L10 12L14 8" stroke="#222" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <a class="header-action header-cart" href="{{ route('cart.index') }}" style="position:relative;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Cart
            </a>
        </div>
    </div>
    <div class="header-orange-bar"></div>
    <div class="container mx-auto py-8">
        <div class="order-details-card">
            <h1 class="order-details-title">Order Details</h1>
            <table class="order-details-table">
                <tr><th>Order #</th><td>{{ $order->order_number }}</td></tr>
                <tr><th>Date</th><td>{{ $order->order_date }}</td></tr>
                <tr><th>Status</th><td>{{ ucfirst($order->order_status) }}</td></tr>
                <tr><th>Total</th><td>{{ $order->total_amount ?? '-' }}</td></tr>
                <tr><th>Delivery Address</th><td>{{ $order->delivery_address }}</td></tr>
                <tr><th>Delivery Contact</th><td>{{ $order->delivery_contact }}</td></tr>
                <tr><th>Delivery Phone</th><td>{{ $order->delivery_phone }}</td></tr>
                <tr><th>Special Instructions</th><td>{{ $order->special_instructions ?? '-' }}</td></tr>
                <tr><th>Notes</th><td>{{ $order->notes ?? '-' }}</td></tr>
            </table>
            <h2 class="order-items-title">Order Items</h2>
            <table class="order-items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($order->orderItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->yogurtProduct->name ?? 'Product' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit_price }}</td>
                        <td>{{ $item->total_price }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <a href="{{ route('customer.orders.index') }}" class="back-link">Back to My Orders</a>
        </div>
    </div>
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
    <style>
    .order-details-card {
        background: #fff;
        max-width: 600px;
        margin: 40px auto 0 auto;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        padding: 32px 28px 28px 28px;
    }
    .order-details-title {
        font-size: 2.1rem;
        font-weight: bold;
        margin-bottom: 18px;
        color: #222;
        letter-spacing: 1px;
    }
    .order-details-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 32px;
    }
    .order-details-table th {
        text-align: left;
        padding: 8px 12px 8px 0;
        color: #555;
        font-weight: 600;
        width: 180px;
        background: none;
        border: none;
    }
    .order-details-table td {
        padding: 8px 0;
        color: #222;
        background: none;
        border: none;
    }
    .order-items-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 10px;
        margin-top: 18px;
        color: #ff9900;
    }
    .order-items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        background: #fafafa;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .order-items-table th, .order-items-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #ececec;
        text-align: left;
    }
    .order-items-table th {
        background: #ff9900;
        color: #fff;
        font-weight: 600;
    }
    .order-items-table tr:last-child td {
        border-bottom: none;
    }
    .back-link {
        display: inline-block;
        margin-top: 18px;
        color: #ff9900;
        text-decoration: none;
        font-weight: 500;
        font-size: 1.05rem;
        transition: color 0.2s;
    }
    .back-link:hover {
        color: #e67c00;
        text-decoration: underline;
    }
    @media (max-width: 700px) {
        .order-details-card { padding: 16px 4vw; }
        .order-details-title { font-size: 1.3rem; }
        .order-items-title { font-size: 1.05rem; }
    }
    </style>
</body>
</html> 