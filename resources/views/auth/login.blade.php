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
            margin: 48px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 32px rgba(0,0,0,0.10);
            padding: 2.5rem 2rem 2rem 2rem;
        }
        .auth-header, .login-header, .register-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }
        .auth-logo, .login-logo, .register-logo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .auth-title, .login-title, .register-title {
            font-size: 1.6rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 0.2rem;
            text-align: center;
        }
        .auth-subtitle, .login-subtitle, .register-subtitle {
            font-size: 1.05rem;
            color: #4CAF50;
            text-align: center;
            margin-bottom: 0.7rem;
        }
        form {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 1.2rem;
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
            font-size: 1rem;
            margin-top: 0.2rem;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #4CAF50;
            outline: none;
        }
        .status-message, .error-message {
            margin-bottom: 1rem;
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
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.85rem 0;
            font-size: 1.08rem;
            font-weight: 600;
            margin-top: 1.2rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .action-btn:hover, .auth-login-form button:hover, .auth-login-form .x-primary-button:hover {
            background: #388e3c;
        }
        .link {
            color: #2563eb;
            text-decoration: underline;
            font-size: 0.98rem;
        }
        @media (max-width: 600px) {
            .auth-container, .login-container, .register-container {
                padding: 1.2rem 0.5rem 1.5rem 0.5rem;
                max-width: 98vw;
            }
            .auth-title, .login-title, .register-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
        <div class="login-header">
            <img src="{{ asset('/images/apex-logo.png') }}" alt="Caramel Yoghurt Logo" class="login-logo">
            <div class="login-title">Caramel Supply Chain Management</div>
            <div class="login-subtitle">Sign in to your account</div>
        </div>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('login') }}" class="auth-login-form" style="width: 100%; max-width: 340px; margin: 0 auto;">
            @csrf
            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-group label">Email</label>
                <input id="email" class="form-group input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="error-message" />
            </div>
            <!-- Password -->
            <div class="form-group">
                <label for="password" class="form-group label">Password</label>
                <input id="password" class="form-group input"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
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


