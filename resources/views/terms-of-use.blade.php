<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Use - Caramel Yoghurt</title>
    <link rel="stylesheet" href="/css/customer.css">
    <style>
        .terms-container {
            max-width: 900px;
            margin: 48px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 32px rgba(37,99,235,0.07);
            padding: 32px 32px 32px 32px;
        }
        .terms-container h1 {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #2563eb;
        }
        .terms-container h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 0.7rem;
            color: #2563eb;
        }
        .terms-container ul {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .terms-container p, .terms-container li {
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
    <div class="terms-container">
        <h1>Terms of Use</h1>
        <p>Effective Date: {{ date('F d, Y') }}</p>
        <p class="mt-4">By accessing or using our platform, you agree to be bound by these Terms of Use. If you do not agree to these terms, please do not use our services.</p>
        <h2>1. Use of the Platform</h2>
        <ul>
            <li>You must be at least 18 years old or have legal parental or guardian consent to use this platform.</li>
            <li>You agree to provide accurate and complete information when registering and using the platform.</li>
            <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
        </ul>
        <h2>2. Prohibited Activities</h2>
        <ul>
            <li>Using the platform for any unlawful purpose.</li>
            <li>Attempting to gain unauthorized access to other accounts or systems.</li>
            <li>Transmitting any viruses, malware, or harmful code.</li>
        </ul>
        <h2>3. Intellectual Property</h2>
        <p>All content, trademarks, and data on this platform are the property of the company or its licensors. You may not use, reproduce, or distribute any content without permission.</p>
        <h2>4. Termination</h2>
        <p>We reserve the right to suspend or terminate your access to the platform at our discretion, without notice, for conduct that we believe violates these Terms or is harmful to other users or the platform.</p>
        <h2>5. Limitation of Liability</h2>
        <p>To the fullest extent permitted by law, we disclaim all warranties, express or implied, and will not be liable for any damages arising from your use of the platform.</p>
        <h2>6. Changes to These Terms</h2>
        <p>We may update these Terms of Use from time to time. Continued use of the platform after changes constitutes acceptance of the new terms.</p>
        <h2>7. Contact Us</h2>
        <p>If you have any questions about these Terms, please contact us at support@example.com.</p>
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