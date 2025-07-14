<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Caramel Yoghurt</title>
    <link rel="stylesheet" href="/css/customer.css">
    <style>
        .policy-container {
            max-width: 900px;
            margin: 48px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(37,99,235,0.07);
            padding: 32px 32px 32px 32px;
        }
        .policy-container h1 {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #2563eb;
        }
        .policy-container h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 0.7rem;
            color: #2563eb;
        }
        .policy-container ul {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .policy-container p, .policy-container li {
            color: #222;
            font-size: 1.08rem;
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
        <div class="header-actions">
            <a class="header-action" href="/dashboard/customer">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" font-size="12" fill="#222">?</text></svg>
                Help
            </a>
        </div>
    </div>
    <div class="header-orange-bar"></div>
    <div class="policy-container">
        <h1>Privacy Policy</h1>
        <p>Effective Date: {{ date('F d, Y') }}</p>
        <p class="mt-4">This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform. Please read this policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site.</p>
        <h2>1. Information We Collect</h2>
        <ul>
            <li>Personal Data (name, email, contact information, etc.)</li>
            <li>Usage Data (pages visited, actions taken, etc.)</li>
            <li>Cookies and Tracking Technologies</li>
        </ul>
        <h2>2. How We Use Your Information</h2>
        <ul>
            <li>To provide and maintain our services</li>
            <li>To improve user experience</li>
            <li>To communicate with you</li>
            <li>To comply with legal obligations</li>
        </ul>
        <h2>3. Sharing Your Information</h2>
        <ul>
            <li>With service providers who assist us in operating the platform</li>
            <li>When required by law or to protect our rights</li>
        </ul>
        <h2>4. Security of Your Information</h2>
        <p>We use administrative, technical, and physical security measures to help protect your personal information. However, no method of transmission over the Internet or method of electronic storage is 100% secure.</p>
        <h2>5. Your Privacy Rights</h2>
        <ul>
            <li>You may review, change, or terminate your account at any time.</li>
            <li>You may opt out of certain communications.</li>
        </ul>
        <h2>6. Changes to This Privacy Policy</h2>
        <p>We may update this Privacy Policy from time to time. We will notify you of any changes by updating the effective date at the top of this policy.</p>
        <h2>7. Contact Us</h2>
        <p>If you have any questions or concerns about this Privacy Policy, please contact us at support@example.com.</p>
    </div>
    <footer class="customer-footer">
        <div class="footer-container">
            <div class="footer-links">
                <a href="/dashboard/customer">Home</a>
                <a href="/dashboard/customer">Shop</a>
                <a href="{{ route('customer.orders.index') }}">Orders</a>
                <a href="{{ route('help.index') }}">Help</a>
                <a href="{{ route('privacy.policy') }}">Privacy Policy</a>
                <a href="{{ route('terms.use') }}">Terms</a>
                <a href="#">Contact</a>
            </div>
            <div class="footer-copy">&copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.</div>
        </div>
    </footer>
</body>
</html> 