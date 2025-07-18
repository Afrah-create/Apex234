<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Caramel Supply Chain Management</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        body {
            background: #f7f8fa;
        }
        .auth-container, .login-container, .register-container {
            max-width: 420px;
            margin: 24px auto; /* reduced from 48px */
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 32px rgba(0,0,0,0.10);
            padding: 1.5rem 1.2rem 1.2rem 1.2rem; /* reduced padding */
        }
        .auth-header, .login-header, .register-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.2rem; /* reduced from 2rem */
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
            margin-bottom: 0.4rem; /* reduced from 0.7rem */
        }
        form {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 0.7rem; /* reduced from 1.2rem */
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
            box-sizing: border-box; /* ensure no overflow */
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #2563eb;
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
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.65rem 0; /* reduced from 0.85rem */
            font-size: 1.02rem; /* slightly reduced */
            font-weight: 600;
            margin-top: 0.7rem; /* reduced from 1.2rem */
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
<body style="background: #fff;">
    <div class="register-container">
        <div class="register-header">
            <img src="{{ asset('/images/apex-logo.png') }}" alt="Caramel Yoghurt Logo" class="register-logo">
            <div class="register-title">Caramel Supply Chain Management</div>
            <div class="register-subtitle">Create your account</div>
        </div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Full name" />
                <x-input-error :messages="$errors->get('name')" class="error-message" />
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="e.g. user@example.com" />
                <x-input-error :messages="$errors->get('email')" class="error-message" />
            </div>
            <div class="form-group">
                <label for="role">Register as</label>
                <div style="font-size: 0.97rem; color: #555; margin-bottom: 0.2rem;">Please select your account type to continue registration.</div>
                <select id="role" name="role" required>
                    <option value="retailer" {{ old('role') == 'retailer' ? 'selected' : '' }}>Retailer</option>
                    <option value="supplier" {{ old('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                    <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="error-message" />
            </div>
            <div id="vendor-fields" style="display: none;">
                <div class="form-group">
                    <label for="business_name">Business Name</label>
                    <input id="business_name" type="text" name="business_name" value="{{ old('business_name') }}" placeholder="Business legal name" />
                    <x-input-error :messages="$errors->get('business_name')" class="error-message" />
                </div>
                <div class="form-group">
                    <label for="business_address">Business Address</label>
                    <textarea id="business_address" name="business_address" rows="3" placeholder="Street, City, State, ZIP">{{ old('business_address') }}</textarea>
                    <x-input-error :messages="$errors->get('business_address')" class="error-message" />
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="e.g. +1 555 123 4567" />
                    <x-input-error :messages="$errors->get('phone_number')" class="error-message" />
                </div>
                <div class="form-group">
                    <label for="tax_id">Tax ID (Optional)</label>
                    <input id="tax_id" type="text" name="tax_id" value="{{ old('tax_id') }}" placeholder="Optional: e.g. 12-3456789" />
                    <x-input-error :messages="$errors->get('tax_id')" class="error-message" />
                </div>
                <div class="form-group">
                    <label for="business_license">Business License (Optional)</label>
                    <input id="business_license" type="text" name="business_license" value="{{ old('business_license') }}" placeholder="Optional: License number" />
                    <x-input-error :messages="$errors->get('business_license')" class="error-message" />
                </div>
                <div class="form-group">
                    <label for="description">Business Description (Optional)</label>
                    <textarea id="description" name="description" rows="3" placeholder="Optional: Brief business description">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="error-message" />
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters" />
                <x-input-error :messages="$errors->get('password')" class="error-message" />
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter your password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="error-message" />
            </div>
            <div class="form-group" style="display: flex; flex-direction: column; align-items: stretch; gap: 0.5rem; margin-top: 1rem; width: 100%;">
                <button type="submit" class="action-btn" style="margin-top: 0;">Register</button>
                <a href="{{ route('login') }}" class="link" style="text-align: center;">Already registered?</a>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const vendorFields = document.getElementById('vendor-fields');
            function toggleVendorFields() {
                if (roleSelect.value === 'vendor') {
                    vendorFields.style.display = 'block';
                } else {
                    vendorFields.style.display = 'none';
                }
            }
            // Initial check
            toggleVendorFields();
            // Listen for changes
            roleSelect.addEventListener('change', toggleVendorFields);
        });
    </script>
</body>
</html>
