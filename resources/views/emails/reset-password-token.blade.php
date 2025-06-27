<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Password Reset Code - Caramel Yogurt</title>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="email-bg">
    <div class="email-container">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="https://YOUR_DOMAIN/images/apex-logo.png" alt="Caramel Yogurt Logo" class="email-logo">
            <h2 class="email-heading">Caramel Yogurt</h2>
            <div class="email-subheading">Supply Chain Management Platform</div>
        </div>
        <p class="email-greeting">Hello{{ isset($user->name) ? ' ' . $user->name : '' }},</p>
        <p class="email-body">
            Use the following code to reset your password. Enter this code in the password reset form on our website.
        </p>
        <div class="email-button-container">
            <span style="display:inline-block; font-size:2rem; font-weight:700; letter-spacing:4px; color:#3182ce; background:#f1f5f9; padding:12px 32px; border-radius:8px; border:1.5px solid #e3e8ee;">
                {{ $token }}
            </span>
        </div>
        <p class="email-expiry">This code will expire in 60 minutes. If you did not request a password reset, please ignore this email.</p>
        <hr class="email-hr">
        <p class="email-footer">&copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.<br>Empowering Dairy Supply Chains.</p>
    </div>
</body>
</html> 