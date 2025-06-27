<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password - Caramel Yogurt</title>
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
        <p class="email-body">We received a request to reset your password for your Caramel Yogurt account. Click the button below to set a new password and regain access to your dashboard.</p>
        <div class="email-button-container">
            <a href="{{ $resetUrl }}" class="email-button">Reset Password</a>
        </div>
        <p class="email-expiry">If you did not request a password reset, please ignore this email. This password reset link will expire in 60 minutes for your security.</p>
        <hr class="email-hr">
        <p class="email-footer">&copy; {{ date('Y') }} Caramel Yogurt. All rights reserved.<br>Empowering Dairy Supply Chains.</p>
    </div>
</body>
</html> 