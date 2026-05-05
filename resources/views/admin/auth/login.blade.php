{{-- resources/views/admin/auth/login.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login – Jhr Bazar</title>

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
      margin-bottom: 8px;
    }

    .input-group-custom {
      position: relative;
      margin-bottom: 24px;
    }

    .input-group-custom i:not(.eye-toggle) {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 18px;
      transition: color 0.3s;
      pointer-events: none; /* Allow clicking through the icon to the input */
    }

    .input-group-custom input {
      width: 100%;
      padding: 14px 16px 14px 48px;
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

    .eye-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        cursor: pointer;
        transition: color 0.3s;
    }

    .eye-toggle:hover { color: var(--primary); }

    .form-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 30px;
    }

    .remember-me {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      font-weight: 500;
      color: #64748b;
      cursor: pointer;
    }

    .remember-me input {
      width: 16px;
      height: 16px;
      border-radius: 4px;
      border: 1.5px solid #cbd5e1;
      cursor: pointer;
    }

    .forgot-link {
      font-size: 13px;
      font-weight: 600;
      color: var(--primary);
      text-decoration: none;
    }

    .forgot-link:hover { text-decoration: underline; }

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
      <h1>Welcome Back!</h1>
      <p>Sign in to access your admin dashboard and manage your platform with powerful tools and insights.</p>
    </div>

    <div class="feature-list">
      <div class="feature-item">
        <div class="feature-icon"><i class="bi bi-bar-chart-fill"></i></div>
        <div class="feature-info">
          <h6>Analytics Dashboard</h6>
          <p>Real-time insights and comprehensive reports</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
        <div class="feature-info">
          <h6>User Management</h6>
          <p>Complete control over platform users</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><i class="bi bi-gear-fill"></i></div>
        <div class="feature-info">
          <h6>System Settings</h6>
          <p>Configure and customize your platform</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Panel -->
  <div class="right-panel">
    <div class="form-header">
      <h2>Admin Login</h2>
      <p>Enter your credentials to access the dashboard</p>
    </div>

    <form action="{{ route('admin.login.submit') }}" method="POST">
      @csrf

      <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-group-custom">
          <i class="bi bi-envelope"></i>
          <input
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="admin@example.com"
            required
            autocomplete="email"
          />
          @error('email')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-group-custom">
          <i class="bi bi-lock"></i>
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Enter your password"
            required
            autocomplete="current-password"
          />
          <i class="bi bi-eye eye-toggle" id="togglePassword"></i>
          @error('password')
            <div class="error-msg">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="form-options">
        <label class="remember-me">
          <input type="checkbox" name="remember">
          Remember me
        </label>
        <a href="#" class="forgot-link">Forgot Password?</a>
      </div>

      <button type="submit" class="btn-submit">Sign In to Dashboard</button>
    </form>
  </div>
</div>

<script>
  const togglePassword = document.querySelector('#togglePassword');
  const password = document.querySelector('#password');

  togglePassword.addEventListener('click', function (e) {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    this.classList.toggle('bi-eye');
    this.classList.toggle('bi-eye-slash');
  });
</script>

</body>
</html>
