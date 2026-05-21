<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Registration – Jhr Bazar</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet"/>

  <style>
    :root {
      --primary: #6366f1;
      --accent: #8b5cf6;
      --gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--bg-gradient);
      padding: 20px;
    }

    .register-card {
      width: 100%;
      max-width: 500px;
      background: #fff;
      border-radius: 24px;
      padding: 40px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      text-align: center;
      margin-bottom: 30px;
      background: none;
      border: none;
    }

    .card-header h2 {
      font-family: 'Sora', sans-serif;
      font-weight: 800;
      color: #1e293b;
      margin-bottom: 8px;
    }

    .card-header p {
      font-size: 14px;
      color: #64748b;
    }

    .form-label {
      font-size: 13px;
      font-weight: 600;
      color: #475569;
    }

    .form-control {
      padding: 12px 16px;
      border-radius: 12px;
      border: 1.5px solid #e2e8f0;
      font-size: 14px;
      margin-bottom: 15px;
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .btn-submit {
      width: 100%;
      padding: 14px;
      background: var(--gradient);
      border: none;
      border-radius: 12px;
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      margin-top: 10px;
      transition: all 0.3s;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
    }

    .login-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #64748b;
    }

    .login-link a {
      color: var(--primary);
      font-weight: 700;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="register-card">
  <div class="card-header">
    <h2>Join as Customer</h2>
    <p>Create your account and start shopping today.</p>
  </div>

  <form action="{{ route('register.customer.submit') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control" placeholder="John Doe" required value="{{ old('name') }}">
      @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Email Address</label>
      <input type="email" name="email" class="form-control" placeholder="john@example.com" required value="{{ old('email') }}">
      @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Phone Number</label>
      <input type="text" name="phone" class="form-control" placeholder="+880..." required value="{{ old('phone') }}">
      @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required>
      @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Confirm Password</label>
      <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
    </div>

    <button type="submit" class="btn-submit">Create Account</button>
  </form>

  <div class="login-link">
    Already have an account? <a href="{{ route('login') }}">Sign In</a>
  </div>
</div>

</body>
</html>
