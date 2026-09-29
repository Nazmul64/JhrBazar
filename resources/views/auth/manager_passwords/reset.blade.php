{{-- resources/views/auth/manager_passwords/reset.blade.php --}}
@php
    $gs = $gs ?? \App\Models\GenaralSetting::first();
    $siteName = $gs->website_name ?? 'JHR Bazar';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Reset Password | {{ $siteName }}</title>
    <link rel="icon" href="{{ !empty($gs->favicon) ? asset($gs->favicon) : asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #f59e0b;
            --primary-hover: #d97706;
            --bg-gradient: linear-gradient(135deg, #78350f 0%, #451a03 100%);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo-wrap {
            margin-bottom: 14px;
            display: inline-block;
        }
        .logo-img {
            max-height: 48px;
            max-width: 200px;
            object-fit: contain;
        }
        .brand-text {
            font-size: 26px;
            font-weight: 900;
            color: #d97706;
            text-decoration: none;
        }
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(245, 158, 11, 0.15);
            color: #b45309;
            margin-bottom: 12px;
        }
        .login-header h2 {
            font-weight: 800;
            color: #111827;
            margin-bottom: 6px;
            font-size: 22px;
        }
        .login-header p {
            color: #6b7280;
            font-size: 13.5px;
        }
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #374151;
            margin-bottom: 6px;
        }
        .input-group-text {
            background-color: #f9fafb;
            border: 1.5px solid #e5e7eb;
            color: #6b7280;
            padding: 12px 14px;
        }
        .input-group-text.prefix {
            border-right: none;
            border-radius: 12px 0 0 12px;
        }
        .input-group-text.suffix {
            border-left: none;
            border-radius: 0 12px 12px 0;
            cursor: pointer;
        }
        .form-control {
            padding: 12px 14px;
            border: 1.5px solid #e5e7eb;
            font-size: 14px;
            transition: all 0.2s;
        }
        .form-control.middle {
            border-left: none;
            border-right: none;
        }
        .form-control.no-suffix {
            border-radius: 0 12px 12px 0;
            border-left: none;
        }
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #d97706;
        }
        .form-control:focus {
            box-shadow: none;
        }
        .btn-login {
            background: #d97706;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 700;
            font-size: 14px;
            color: white;
            width: 100%;
            margin-top: 18px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover {
            background: #b45309;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(217, 119, 6, 0.45);
            color: white;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="logo-wrap">
                <a href="/">
                    @if(!empty($gs->logo))
                        <img src="{{ asset($gs->logo) }}" alt="{{ $siteName }}" class="logo-img">
                    @else
                        <span class="brand-text">{{ $siteName }}</span>
                    @endif
                </a>
            </div>
            <div>
                <span class="role-badge"><i class="bi bi-briefcase-fill"></i> Manager Portal</span>
            </div>
            <h2>Set New Password</h2>
            <p>Create a new secure password for your manager account.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2.5 px-3 border-0 shadow-sm mb-4" style="font-size: 13px; border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label">Manager Email Address</label>
                <div class="input-group">
                    <span class="input-group-text prefix"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" readonly class="form-control no-suffix bg-light" required autocomplete="email">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <div class="input-group">
                    <span class="input-group-text prefix"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password" name="password" class="form-control middle" placeholder="••••••••" required autocomplete="new-password" autofocus>
                    <span class="input-group-text suffix" onclick="togglePass('password', 'eye1')"><i class="bi bi-eye" id="eye1"></i></span>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text prefix"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control middle" placeholder="••••••••" required autocomplete="new-password">
                    <span class="input-group-text suffix" onclick="togglePass('password_confirmation', 'eye2')"><i class="bi bi-eye" id="eye2"></i></span>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-check-circle-fill"></i> Reset Password
            </button>
        </form>
    </div>

    <script>
        function togglePass(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                eye.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</body>
</html>
