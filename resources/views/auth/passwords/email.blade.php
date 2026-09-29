{{-- resources/views/auth/passwords/email.blade.php --}}
@php
    $gs = $gs ?? \App\Models\GenaralSetting::first();
    $siteName = $gs->website_name ?? 'JHR Bazar';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Forgot Password | {{ $siteName }}</title>
    <link rel="icon" href="{{ !empty($gs->favicon) ? asset($gs->favicon) : asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: {{ !empty($gs->primary_color) ? $gs->primary_color : '#4f46e5' }};
            --primary-hover: #4338ca;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
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
            max-width: 450px;
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
            margin-bottom: 28px;
        }
        .logo-wrap {
            margin-bottom: 16px;
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
            color: var(--primary);
            text-decoration: none;
            letter-spacing: -0.5px;
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
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary);
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
            line-height: 1.4;
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
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #6b7280;
            padding: 12px 14px;
        }
        .form-control {
            border-radius: 0 12px 12px 0;
            padding: 12px 16px;
            border: 1.5px solid #e5e7eb;
            border-left: none;
            font-size: 14px;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: none;
        }
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: var(--primary);
        }
        .btn-login {
            background: var(--primary);
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 700;
            font-size: 14px;
            color: white;
            width: 100%;
            margin-top: 18px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover {
            opacity: 0.92;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.45);
            color: white;
        }
        .back-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.2s;
        }
        .back-link:hover {
            gap: 9px;
            color: var(--primary-hover);
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
                <span class="role-badge"><i class="bi bi-person-fill"></i> Customer Portal</span>
            </div>
            <h2>Forgot Password?</h2>
            <p>Enter your registered email address and we'll send you a password reset link.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success py-2.5 px-3 border-0 shadow-sm mb-4" style="font-size: 13px; border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
                <i class="bi bi-check-circle-fill me-2 text-success"></i> {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2.5 px-3 border-0 shadow-sm mb-4" style="font-size: 13px; border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-2">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="yourname@example.com" required autofocus autocomplete="email">
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-send-fill"></i> Send Password Reset Link
            </button>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <a href="/login" class="back-link">
                <i class="bi bi-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

</body>
</html>
