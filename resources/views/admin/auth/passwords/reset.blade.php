{{-- resources/views/admin/auth/passwords/reset.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Reset Password – Jhr Bazar</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet"/>

  <style>
    :root {
      --primary: #6366f1;
      --accent: #8b5cf6;
      --gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--bg-gradient);
      padding: 20px;
    }

    .login-container {
      width: 100%;
      max-width: 950px;
      min-height: 580px;
      background: #fff;
      display: flex;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Left Panel */
    .left-panel {
      flex: 1.1;
      background: var(--gradient);
      padding: 50px;
      color: #fff;
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
    }

    .left-panel::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        top: -100px;
        left: -100px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 50px;
      z-index: 1;
    }

    .brand-icon {
      width: 42px;
      height: 42px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }

    .brand-name {
      font-family: 'Sora', sans-serif;
      font-size: 24px;
      font-weight: 800;
    }

    .welcome-text h1 {
      font-family: 'Sora', sans-serif;
      font-size: 36px;
      font-weight: 800;
      margin-bottom: 15px;
      letter-spacing: -1px;
    }

    .welcome-text p {
      font-size: 14px;
      opacity: 0.85;
      line-height: 1.6;
      margin-bottom: 40px;
      max-width: 340px;
    }

    .feature-list {
      display: flex;
      flex-direction: column;
      gap: 25px;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .feature-icon {
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    .feature-info h6 {
      margin: 0;
      font-size: 14px;
      font-weight: 700;
    }

    .feature-info p {
      margin: 0;
      font-size: 12px;
      opacity: 0.75;
    }

    /* Right Panel */
    .right-panel {
      flex: 1;
      padding: 60px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-header h2 {
      font-family: 'Sora', sans-serif;
      font-size: 28px;
      font-weight: 800;
      color: #1e293b;
      margin-bottom: 8px;
    }

    .form-header p {
      font-size: 14px;
      color: #64748b;
      margin-bottom: 35px;
    }

    .form-label {
      font-size: 13px;
      font-weight: 600;
      color: #475569;
      margin-bottom: 6px;
    }

    .input-group-custom {
      position: relative;
      margin-bottom: 20px;
    }

    .input-group-custom i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 18px;
      transition: color 0.3s;
      pointer-events: none;
    }

    .input-group-custom input {
      width: 100%;
      padding: 12px 16px 12px 48px;
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s;
      color: #1e293b;
    }

    .input-group-custom input:focus {
      outline: none;
      border-color: var(--primary);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .input-group-custom input:focus + i {
      color: var(--primary);
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
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4);
    }

    .btn-submit:active { transform: translateY(0); }

    .error-msg {
        font-size: 12px;
        color: #ef4444;
        margin-top: 6px;
        font-weight: 500;
    }

    @media (max-width: 991.98px) {
      .login-container { max-width: 500px; flex-direction: column; }
      .left-panel { display: none; }
      .right-panel { padding: 40px; }
    }
  </style>
</head>
<body>

<div class="login-container">
  <!-- Left Panel -->
  <div class="left-panel">
    <div class="brand">
      <div class="brand-icon"><i class="bi bi-shield-lock-fill"></i></div>
      <div class="brand-name">Admin</div>
    </div>

    <div class="welcome-text">
      <h1>Set New Password</h1>
      <p>Set a strong and secure password for your administrator credentials to keep your portal safe.</p>
    </div>

    <div class="feature-list">
      <div class="feature-item">
        <div class="feature-icon"><i class="bi bi-key-fill"></i></div>
        <div class="feature-info">
          <h6>Strong Password</h6>
          <p>We recommend at least 8 characters</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><i class="bi bi-shield-lock"></i></div>
        <div class="feature-info">
          <h6>Secure Session</h6>
          <p>Fully encrypted authorization process</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="right-panel">
    <div class="form-header">
      <h2>Reset Password</h2>
      <p>Please enter your new password below</p>
    </div>

    <form action="{{ route('password.update') }}" method="POST">
      @csrf

      <input type="hidden" name="token" value="{{ $token }}">

      <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-group-custom">
          <i class="bi bi-envelope"></i>
          <input
            type="email"
            name="email"
            value="{{ $email ?? old('email') }}"
            required
            readonly
            autocomplete="email"
          />
          @error('email')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">New Password</label>
        <div class="input-group-custom">
          <i class="bi bi-lock"></i>
          <input
            type="password"
            name="password"
            placeholder="••••••••"
            required
            autocomplete="new-password"
            autofocus
          />
          @error('password')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <div class="input-group-custom">
          <i class="bi bi-lock-fill"></i>
          <input
            type="password"
            name="password_confirmation"
            placeholder="••••••••"
            required
            autocomplete="new-password"
          />
        </div>
      </div>

      <button type="submit" class="btn-submit">Reset Password</button>
    </form>
  </div>
</div>

</body>
</html>
