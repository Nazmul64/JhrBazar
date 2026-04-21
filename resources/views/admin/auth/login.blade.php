<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>JhrBazar – Admin Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      background: #fde8ec;
      overflow-x: hidden;
    }

    .page-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: stretch;
    }

    /* ═══════════════════════════
       LEFT IMAGE PANEL
    ═══════════════════════════ */
    .image-panel {
      flex: 1;
      position: relative;
      overflow: hidden;
      min-height: 100vh;
    }

    .image-panel img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    /* Dark gradient overlay on the image */
    .image-panel::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(
        135deg,
        rgba(184, 0, 46, 0.55) 0%,
        rgba(10, 10, 30, 0.45) 100%
      );
    }

    /* Text overlay on image */
    .image-overlay-text {
      position: absolute;
      bottom: 48px;
      left: 44px;
      right: 44px;
      z-index: 2;
      color: #fff;
    }

    .image-overlay-text h1 {
      font-size: 2rem;
      font-weight: 800;
      line-height: 1.2;
      margin-bottom: 10px;
      text-shadow: 0 2px 12px rgba(0,0,0,.3);
    }

    .image-overlay-text p {
      font-size: .88rem;
      opacity: .85;
      line-height: 1.6;
      max-width: 360px;
    }

    /* Small brand badge on image */
    .image-brand-badge {
      position: absolute;
      top: 36px;
      left: 44px;
      z-index: 2;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .image-brand-badge .badge-icon {
      width: 42px; height: 42px;
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(8px);
      border: 1.5px solid rgba(255,255,255,0.35);
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem; font-weight: 800;
      color: #fff; font-style: italic;
    }

    .image-brand-badge .badge-label {
      font-size: .8rem;
      font-weight: 600;
      color: #fff;
      opacity: .9;
    }

    /* ═══════════════════════════
       RIGHT LOGIN CARD
    ═══════════════════════════ */
    .login-card {
      width: 440px;
      background: #fff;
      box-shadow: -6px 0 50px rgba(0,0,0,.10);
      padding: 28px 44px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 100vh;
      flex-shrink: 0;
    }

    .top-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;
    }
    .powered-text { font-size: .7rem; color: #aaa; }
    .powered-text span { color: #e8194b; font-weight: 600; }
    .version-badge { font-size: .7rem; color: #e8194b; font-weight: 600; }

    .brand-area {
      display: flex; align-items: center; gap: 12px;
      margin-bottom: 10px;
    }
    .brand-icon {
      width: 50px; height: 50px;
      background: linear-gradient(145deg, #e8194b, #b8002e);
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 6px 16px rgba(232,25,75,.3);
      position: relative; flex-shrink: 0;
    }
    .brand-icon::before {
      content: '';
      position: absolute;
      top: 7px; left: 50%;
      transform: translateX(-50%);
      width: 16px; height: 6px;
      border: 2.5px solid rgba(255,255,255,.65);
      border-bottom: none;
      border-radius: 7px 7px 0 0;
    }
    .brand-icon .r { font-size: 1.25rem; font-weight: 800; color: #fff; margin-top: 5px; font-style: italic; }
    .brand-name-text h2 { font-size: 1.45rem; font-weight: 700; color: #1a1a1a; line-height: 1.15; }
    .brand-name-text h2 span { color: #e8194b; }

    .welcome-line { font-size: .84rem; color: #555; margin-bottom: 3px; }
    .welcome-line span { color: #e8194b; font-weight: 600; }
    .login-heading { font-size: 1.45rem; font-weight: 700; color: #1a1a1a; margin-bottom: 20px; }

    hr.sep { border-color: #eee; margin-bottom: 22px; }

    .form-label { font-size: .8rem; font-weight: 500; color: #333; margin-bottom: 6px; }

    .form-control {
      font-family: 'Poppins', sans-serif !important;
      font-size: .85rem !important;
      padding: 11px 14px !important;
      border-radius: 9px !important;
      border: 1.5px solid #dde !important;
      background: #fafafa !important;
      transition: border-color .2s, background .2s !important;
    }
    .form-control:focus {
      border-color: #e8194b !important;
      background: #fff !important;
      box-shadow: 0 0 0 3px rgba(232,25,75,.1) !important;
    }

    .input-group .form-control { border-right: none !important; border-radius: 9px 0 0 9px !important; }
    .input-group .btn-eye {
      border: 1.5px solid #dde;
      border-left: none;
      border-radius: 0 9px 9px 0 !important;
      background: #fafafa;
      color: #aaa;
      padding: 0 13px;
      transition: color .2s;
    }
    .input-group .btn-eye:hover { color: #e8194b; }
    .input-group:focus-within .btn-eye {
      border-color: #e8194b;
      background: #fff;
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #e8194b, #b8002e);
      color: #fff;
      border: none;
      border-radius: 9px;
      font-family: 'Poppins', sans-serif;
      font-size: .95rem;
      font-weight: 600;
      letter-spacing: .4px;
      box-shadow: 0 6px 18px rgba(232,25,75,.35);
      transition: transform .15s, box-shadow .15s;
      margin-top: 8px;
    }
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 26px rgba(232,25,75,.4);
      color: #fff;
    }
    .btn-login:active { transform: translateY(0); }

    .demo-note { font-size: .72rem; color: #999; line-height: 1.65; margin-top: 16px; }

    @media (max-width: 768px) {
      .image-panel { display: none; }
      .login-card { width: 100%; box-shadow: none; }
    }
  </style>
</head>
<body>

  <div class="page-wrapper">

    <!-- Left: Full image panel -->
    <div class="image-panel">
      <!-- High quality ecommerce/shopping image from Unsplash -->
      <img
        src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200&auto=format&fit=crop&q=80"
        alt="Online Shopping"
      />

      <!-- Text overlay bottom-left -->
      <div class="image-overlay-text">
        <h1>Manage Your Store<br>Like a Pro</h1>
        <p>One powerful dashboard for orders, products, customers, and analytics — all in one place.</p>
      </div>
    </div>

    <!-- Right: Login card -->
    <div class="login-card">
      <p class="welcome-line mb-1">Welcome to <span>JhrBazar</span></p>
      <h3 class="login-heading">Login To Admin</h3>
      <hr class="sep" />

      <div class="mb-3">
        <label class="form-label">Enter Address</label>
        <input type="email" class="form-control" value="root@readyecommerce.com" />
      </div>

      <div class="mb-1">
        <label class="form-label">Password</label>
        <div class="input-group">
          <input type="password" class="form-control" id="passwordInput" value="password123" />
          <button class="btn btn-eye" type="button" onclick="togglePassword()">
            <i class="bi bi-eye-slash" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button class="btn btn-login">Login</button>

      <p class="demo-note">
        This is a public demo environment for the ReadyEcommerce software.
        The accounts below are demo accounts for testing purposes only and do
        not contain real user data.
      </p>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword() {
      const input = document.getElementById('passwordInput');
      const icon  = document.getElementById('eyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye';
      } else {
        input.type = 'password';
        icon.className = 'bi bi-eye-slash';
      }
    }
  </script>
</body>
</html>
