<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>
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
        @media (max-width: 600px) {
            .header-main { padding: 8px 2vw; }
            .header-topbar { padding: 4px 2vw 4px 2vw; }
            .header-search { max-width: 98vw; min-width: 0; }
            .header-actions { gap: 10px; }
        }
        .privacy-card {
            background: #fafbfc;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.10);
            padding: 2.2rem 2rem 2rem 2rem;
            max-width: 800px;
            margin: 48px auto 0 auto;
            border: 1.5px solid #e3e8ee;
            transition: box-shadow 0.2s;
        }
        .privacy-title {
            font-size: 2rem;
            font-weight: 800;
            color: #2563eb;
            margin-bottom: 0.7rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            letter-spacing: 0.5px;
            text-align: left;
        }
        .privacy-icon {
            font-size: 1.7rem;
        }
        .privacy-meta {
            color: #6b7280;
            font-size: 1.05rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .privacy-section {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .privacy-heading {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            text-align: left;
        }
        .privacy-section-icon {
            font-size: 1.1rem;
        }
        .privacy-list {
            margin: 0 0 0 1.5rem;
            padding: 0;
            list-style: disc outside;
            color: #222;
            font-size: 1.02rem;
            line-height: 1.8;
        }
        .privacy-list li {
            margin-bottom: 0.3rem;
            text-align: left;
        }
        .privacy-divider {
            border-top: 1.5px solid #e3e8ee;
            margin: 1.1rem 0 1.3rem 0;
        }
        .privacy-intro, .privacy-section p {
            font-size: 1.05rem;
            color: #444;
            margin-bottom: 0.5rem;
            text-align: left;
            font-weight: 400;
            line-height: 1.8;
        }
        .privacy-link {
            color: #2563eb;
            text-decoration: underline;
            font-weight: 500;
        }
        @media (max-width: 900px) {
            .privacy-card {
                padding: 1.2rem 0.7rem;
                max-width: 98vw;
            }
        }
        @media (max-width: 700px) {
            .privacy-card {
                padding: 1.2rem 0.3rem;
            }
            .privacy-title {
                font-size: 1.2rem;
            }
            .privacy-heading {
                font-size: 1rem;
            }
            .privacy-list {
                margin-left: 1rem;
            }
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
        <form class="header-search" method="GET" action="{{ route('dashboard.customer') }}">
            <div class="header-searchbox">
                <span class="search-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" placeholder="Search yoghurt, dairy products, and more">
            </div>
            <button class="header-searchbtn" type="submit">Search</button>
        </form>
        <div class="header-actions">
            @auth
            <div class="header-action account-action" tabindex="0" style="position:relative; margin-left: 18px;"> <!-- Added margin to separate from search bar -->
                <img src="{{ Auth::user()->profile_photo_url }}" alt="Profile Photo" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #2563eb;vertical-align:middle;cursor:pointer;" />
                <div class="account-dropdown" id="accountDropdown">
                    <a href="{{ route('profile.edit') }}"><svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/></svg> Update Profile</a>
                    <a href="{{ route('customer.orders.index') }}"><svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4"/></svg> View Orders</a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><svg width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7"/><path d="M3 21V3a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v4"/></svg> Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                </div>
            </div>
            @endauth
            <a class="header-action" href="{{ route('privacy.policy') }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" font-size="12" fill="#222">?</text></svg>
                Privacy Policy
            </a>
            <a class="header-action header-cart" href="{{ route('cart.index') }}" style="position:relative;">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Cart
            </a>
        </div>
    </div>
    <div class="header-orange-bar"></div>
    <div class="container mx-auto px-4 py-8">
        <div class="privacy-card">
            <h1 class="privacy-title"><span class="privacy-icon">🔒</span> Privacy Policy</h1>
            <div class="privacy-meta">Effective Date: <strong>{{ date('F d, Y') }}</strong></div>
            <div class="privacy-section">
                <p class="privacy-intro">This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform. Please read this policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site.</p>
            </div>
            <div class="privacy-divider"></div>
            <div class="privacy-section">
                <h2 class="privacy-heading"><span class="privacy-section-icon">📋</span> 1. Information We Collect</h2>
                <ul class="privacy-list">
                    <li><strong>Personal Data:</strong> name, email, contact information, etc.</li>
                    <li><strong>Usage Data:</strong> pages visited, actions taken, etc.</li>
                    <li><strong>Cookies & Tracking:</strong> cookies and tracking technologies.</li>
                </ul>
            </div>
            <div class="privacy-divider"></div>
            <div class="privacy-section">
                <h2 class="privacy-heading"><span class="privacy-section-icon">⚙️</span> 2. How We Use Your Information</h2>
                <ul class="privacy-list">
                    <li>To provide and maintain our services</li>
                    <li>To improve user experience</li>
                    <li>To communicate with you</li>
                    <li>To comply with legal obligations</li>
                </ul>
            </div>
            <div class="privacy-divider"></div>
            <div class="privacy-section">
                <h2 class="privacy-heading"><span class="privacy-section-icon">🤝</span> 3. Sharing Your Information</h2>
                <ul class="privacy-list">
                    <li>With service providers who assist us in operating the platform</li>
                    <li>When required by law or to protect our rights</li>
                </ul>
            </div>
            <div class="privacy-divider"></div>
            <div class="privacy-section">
                <h2 class="privacy-heading"><span class="privacy-section-icon">🛡️</span> 4. Security of Your Information</h2>
                <p>We use administrative, technical, and physical security measures to help protect your personal information. However, no method of transmission over the Internet or method of electronic storage is 100% secure.</p>
            </div>
            <div class="privacy-divider"></div>
            <div class="privacy-section">
                <h2 class="privacy-heading"><span class="privacy-section-icon">🔑</span> 5. Your Privacy Rights</h2>
                <ul class="privacy-list">
                    <li>You may review, change, or terminate your account at any time.</li>
                    <li>You may opt out of certain communications.</li>
                </ul>
            </div>
            <div class="privacy-divider"></div>
            <div class="privacy-section">
                <h2 class="privacy-heading"><span class="privacy-section-icon">🔄</span> 6. Changes to This Privacy Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of any changes by updating the effective date at the top of this policy.</p>
            </div>
            <div class="privacy-divider"></div>
            <div class="privacy-section">
                <h2 class="privacy-heading"><span class="privacy-section-icon">📧</span> 7. Contact Us</h2>
                <p>If you have any questions or concerns about this Privacy Policy, please contact us at <a href="mailto:yoghurtcaramel@gmail.com" class="privacy-link">yoghurtcaramel@gmail.com</a>.</p>
            </div>
        </div>
    </div>
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