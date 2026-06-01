{{-- resources/views/auth/employee_passwords/email.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Forgot Password | JHR Bazar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        body {
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 15px;
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
            margin-bottom: 30px;
        }
        .login-header h2 {
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 24px;
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
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
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
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(79, 70, 229, 0.4);
        }
        .brand-logo {
            font-size: 28px;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 15px;
            display: inline-block;
            text-decoration: none;
        }
        .brand-logo span { color: #f43f5e; }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <a href="/" class="brand-logo">JHR <span>BAZAR</span></a>
            <h2>Forgot Password</h2>
            <p>Send password recovery link to your employee account email.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success py-2" style="font-size: 13px; border-radius: 10px;">
                <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2" style="font-size: 13px; border-radius: 10px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('employee.password.email') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control border-start-0" placeholder="name@company.com" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn-login">Send Password Reset Link</button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('employee.login') }}" class="text-decoration-none" style="font-size: 13px; color: var(--primary); font-weight: 600;"><i class="bi bi-arrow-left me-1"></i> Back to Login</a>
        </div>
    </div>

</body>
</html>
