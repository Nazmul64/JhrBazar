@php
  $gs = $gs ?? \App\Models\GenaralSetting::first();
  $siteName = $gs->website_name ?? 'JHR Bazar';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Reset Password – {{ $siteName }}</title>
  <link rel="icon" href="{{ !empty($gs->favicon) ? asset($gs->favicon) : asset('favicon.ico') }}"/>

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
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        top: -100px;
        right: -100px;
    }

    .left-panel::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        bottom: -50px;
        left: -50px;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: auto;
      z-index: 1;
    }

    .brand-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
    }

    .brand-name {
      font-family: 'Sora', sans-serif;
      font-size: 20px;
      font-weight: 700;
      letter-spacing: -0.5px;
    }

    .welcome-text {
      margin: 40px 0;
      z-index: 1;
    }

    .welcome-text h1 {
      font-family: 'Sora', sans-serif;
      font-size: 32px;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 12px;
    }

    .welcome-text p {
      font-size: 15px;
      opacity: 0.85;
      line-height: 1.6;
    }

    .feature-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
      z-index: 1;
    }

    .feature-item {
      display: flex;
      align-items: center;
      gap: 12px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 12px 16px;
      border-radius: 12px;
      border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .feature-icon {
      font-size: 18px;
      color: #a5b4fc;
    }

    .feature-info h6 {
      font-size: 13px;
      font-weight: 600;
      margin: 0;
    }

    .feature-info p {
      font-size: 11px;
      opacity: 0.75;
      margin: 0;
    }

    /* Right Panel */
    .right-panel {
      flex: 1.2;
      padding: 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .form-header {
      margin-bottom: 30px;
    }

    .form-header h2 {
      font-family: 'Sora', sans-serif;
      font-size: 24px;
      font-weight: 700;
      color: #1e293b;
      margin-bottom: 6px;
    }

    .form-header p {
      font-size: 14px;
      color: #64748b;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      font-size: 13px;
      font-weight: 600;
      color: #334155;
      margin-bottom: 6px;
      display: block;
    }

    .input-group-custom {
      position: relative;
    }

    .input-group-custom i.icon-prefix {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 16px;
      color: #94a3b8;
      transition: color 0.3s;
    }

    .input-group-custom .icon-suffix {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 16px;
      color: #94a3b8;
      cursor: pointer;
    }

    .input-group-custom input {
      width: 100%;
      padding: 12px 42px 12px 44px;
      border: 1.5px solid #e2e8f0;
      border-radius: 12px;
      font-size: 14px;
      color: #1e293b;
      background: #f8fafc;
      transition: all 0.3s;
    }

    .input-group-custom input:focus {
      outline: none;
      border-color: var(--primary);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .btn-submit {
      width: 100%;
      padding: 13px;
      border: none;
      border-radius: 12px;
      background: var(--gradient);
      color: #fff;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
      transition: all 0.3s;
      margin-top: 10px;
    }

    .btn-submit:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(99, 102, 241, 0.45);
    }

    .error-msg {
      color: #ef4444;
      font-size: 12px;
      margin-top: 4px;
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
      <div class="brand-name">Admin Portal</div>
    </div>

    <div class="welcome-text">
      <h1>Set New Password</h1>
      <p>Create a strong and secure password for your administrator account.</p>
    </div>

    <div class="feature-list">
      <div class="feature-item">
        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
        <div class="feature-info">
          <h6>Secure Update</h6>
          <p>Instant password encryption & verification</p>
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

      <div class="form-group">
        <label class="form-label">Admin Email Address</label>
        <div class="input-group-custom">
          <i class="bi bi-envelope icon-prefix"></i>
          <input
            type="email"
            name="email"
            value="{{ $email ?? old('email') }}"
            required
            readonly
            autocomplete="email"
          />
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">New Password</label>
        <div class="input-group-custom">
          <i class="bi bi-lock icon-prefix"></i>
          <input
            type="password"
            id="admin_pass"
            name="password"
            placeholder="••••••••"
            required
            autocomplete="new-password"
            autofocus
          />
          <i class="bi bi-eye icon-suffix" id="admin_eye1" onclick="toggleAdminPass('admin_pass', 'admin_eye1')"></i>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <div class="input-group-custom">
          <i class="bi bi-lock-fill icon-prefix"></i>
          <input
            type="password"
            id="admin_pass_confirm"
            name="password_confirmation"
            placeholder="••••••••"
            required
            autocomplete="new-password"
          />
          <i class="bi bi-eye icon-suffix" id="admin_eye2" onclick="toggleAdminPass('admin_pass_confirm', 'admin_eye2')"></i>
        </div>
      </div>

      <button type="submit" class="btn-submit">Reset Password</button>
    </form>
  </div>
</div>

<script>
  function toggleAdminPass(inputId, eyeId) {
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
