<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Caramel Supply Chain Management</title>
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
            padding: 0.7rem 0.9rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            margin-top: 0.2rem;
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
<body style="background: #fff;">
    <div class="auth-container">
        <div class="auth-header">
            <img src="{{ asset('/images/apex-logo.png') }}" alt="Caramel Yoghurt Logo" class="auth-logo">
            <div class="auth-title">Caramel Supply Chain Management</div>
            <div class="auth-subtitle">Reset your password</div>
        </div>
        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</body>
</html>
