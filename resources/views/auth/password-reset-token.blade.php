<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Caramel Yogurt</title>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.token-inputs input');
            inputs.forEach((input, idx) => {
                input.addEventListener('input', function() {
                    if (this.value.length === 1 && idx < inputs.length - 1) {
                        inputs[idx + 1].focus();
                    }
                });
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value && idx > 0) {
                        inputs[idx - 1].focus();
                    }
                });
            });
        });
        function collectToken() {
            let token = '';
            document.querySelectorAll('.token-inputs input').forEach(input => {
                token += input.value;
            });
            document.getElementById('token').value = token;
        }
    </script>
</head>
<body class="email-bg">
    <div class="reset-form-container">
        <img src="{{ asset('images/apex-logo.png') }}" alt="Caramel Yogurt Logo" class="reset-logo">
        <div class="reset-title">Reset Password</div>
        @if ($errors->any())
            <div style="margin-bottom: 14px; color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; padding: 8px 12px; border-radius: 6px; font-size: 0.98rem;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('password.token.reset') }}" autocomplete="off" onsubmit="collectToken()">
            @csrf
            <div class="form-group" style="margin-bottom: 14px;">
                <label for="email" class="reset-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" style="width:100%; padding:10px; border-radius:6px; border:1.5px solid #e3e8ee;" required autofocus value="{{ old('email', request('email')) }}">
            </div>
            <div class="form-group">
                <label class="reset-label">Reset Code</label>
                <div class="token-inputs">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" maxlength="1" pattern="[A-Za-z0-9]" required oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();">
                    @endfor
                </div>
                <input type="hidden" name="token" id="token">
            </div>
            <div class="form-group" style="margin-bottom: 10px;">
                <label for="password" class="reset-label">New Password</label>
                <input type="password" name="password" id="password" class="form-control" style="width:100%; padding:10px; border-radius:6px; border:1.5px solid #e3e8ee;" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation" class="reset-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" style="width:100%; padding:10px; border-radius:6px; border:1.5px solid #e3e8ee;" required>
            </div>
            <button type="submit" class="email-button reset-btn">Reset Password</button>
        </form>
        <div class="reset-footer">&copy; {{ date('Y') }} Caramel Yogurt</div>
    </div>
</body>
</html> 