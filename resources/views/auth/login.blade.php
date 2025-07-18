<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Caramel Supply Chain Management</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        body {
            background: #f7f8fa;
        }
        .auth-container, .login-container, .register-container {
            max-width: 420px;
            margin: 30px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 32px rgba(0,0,0,0.10);
            padding: 0.9rem 0.3rem 0.5rem 0.9rem; /* reduced left and right padding for tighter fit */
        }
        .auth-header, .login-header, .register-header {
            margin-bottom: 0.7rem; /* reduce space below header */
        }
        .auth-logo, .login-logo, .register-logo {
            width: 60px; /* reduced from 72px */
            height: 60px;
            border-radius: 50%;
            margin-bottom: 0.5rem; /* reduced from 0.75rem */
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .auth-title, .login-title, .register-title {
            font-size: 1.3rem; /* reduced from 1.6rem */
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 0.15rem; /* reduced from 0.2rem */
            text-align: center;
        }
        .auth-subtitle, .login-subtitle, .register-subtitle {
            font-size: 0.98rem; /* reduced from 1.05rem */
            color: #2563eb;
            text-align: center;
            margin-bottom: 0.7rem; /* reduced from 0.7rem */
        }
        form {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 0.4rem; /* reduce space between fields */
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 0.4rem;
            display: block;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.45rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.4rem;
            font-size: 16px !important;
            margin-top: 0.2rem;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #2563eb;
            outline: none;
            font-size: 16px !important;
        }
        input:-webkit-autofill {
            font-size: 16px !important;
        }
        .status-message, .error-message {
            margin-bottom: 0.5rem;
            font-size: 0.98rem;
        }
        .status-message {
            color: #2563eb;
        }
        .error-message {
            color: #e53e3e;
        }
        .action-btn, .auth-login-form button, .auth-login-form .x-primary-button {
            width: 100%;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.5rem 0; /* reduce button height */
            font-size: 1.02rem; /* slightly reduced */
            font-weight: 600;
            margin-top: 0.4rem; /* reduce space above button */
            cursor: pointer;
            transition: background 0.2s;
        }
        .action-btn:hover, .auth-login-form button:hover, .auth-login-form .x-primary-button:hover {
            background: #1746a2;
        }
        .link {
            color: #2563eb;
            text-decoration: underline;
            font-size: 0.98rem;
        }
        .flex.items-center.justify-end.mt-4 {
            margin-top: 0.3rem;
        }
        .login-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            margin-bottom: 0.3rem;
        }
        .login-subtitle {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 0.2rem;
            margin-bottom: 0.5rem;
            font-size: 0.98rem;
            color: #2563eb;
        }
        .login-logo {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-bottom: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .login-title {
            font-size: 1.15rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 0;
            text-align: left;
        }
        .login-container {
            min-height: unset;
        }
        .auth-login-form {
            margin: 0;
            padding: 0;
        }
        @media (max-width: 600px) {
            .auth-container, .login-container, .register-container {
                padding: 0.7rem 0.2rem 1rem 0.2rem; /* reduced for mobile */
                max-width: 99vw;
            }
            .auth-title, .login-title, .register-title {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <div class="login-header">
            <img src="{{ asset('/images/apex-logo.png') }}" alt="Caramel Yoghurt Logo" class="login-logo">
            <div class="login-title">Welcome Back To Caramel Yoghurt</div>
        </div>
        <div class="login-subtitle">Sign in to your account</div>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('login') }}" class="auth-login-form" style="width: 100%; max-width: 340px; margin: 0 auto;" autocomplete="off">
            @csrf
            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-group label">Email</label>
                <input id="email" class="form-group input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="e.g. user@example.com" />
                <x-input-error :messages="$errors->get('email')" class="error-message" />
            </div>
            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-group label">Password</label>
                <input id="password" class="form-group input"
                                type="password"
                                name="password"
                                required autocomplete="current-password" placeholder="Enter your password" />
                <x-input-error :messages="$errors->get('password')" class="error-message" />
            </div>
            <!-- Remember Me -->
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <input id="remember_me" type="checkbox" name="remember" style="width: 16px; height: 16px; margin: 0;">
                <label for="remember_me" style="margin: 0; font-weight: 400; font-size: 1rem; cursor: pointer;">{{ __('Remember me') }}</label>
            </div>
            <div class="form-group">
                <button type="submit" class="action-btn">
                    {{ __('Log in') }}
                </button>
            </div>
            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</body>
</html>


