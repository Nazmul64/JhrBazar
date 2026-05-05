<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Login | JHR Bazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #f59e0b;
            --primary-hover: #d97706;
            --bg-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        body {
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.3);
        }
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        .login-header h2 {
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .login-header p {
            color: #6b7280;
            font-size: 14px;
        }
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #374151;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1.5px solid #e5e7eb;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }
        .btn-login {
            background: var(--primary);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            color: white;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(245, 158, 11, 0.4);
        }
        .brand-logo {
            font-size: 28px;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 15px;
            display: inline-block;
        }
        .brand-logo span { color: #1f2937; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">MANAGER <span>PORTAL</span></div>
            <h2>Manager Login</h2>
            <p>Enter your credentials to access the management portal.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2" style="font-size: 13px; border-radius: 10px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('manager.login.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Manager Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="manager@jhrbazar.com" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-login">Secure Login</button>
        </form>

        <div class="text-center mt-4">
            <a href="/" class="text-decoration-none" style="font-size: 13px; color: #6b7280;"><i class="bi bi-arrow-left me-1"></i> Back to Homepage</a>
        </div>
    </div>

</body>
</html>
