<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login - SMADIMENT</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --green: #038047;
      --green-dark: #025a34;
      --navy: #1e3a5f;
      --navy-dark: #152d47;
      --navy-mid: #2d5f8d;
      --white: #FFFFFF;
    }

    html, body {
      height: 100%;
      font-family: 'Poppins', sans-serif;
    }

    body {
      display: flex;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* ===================== LEFT SIDE ===================== */
    .left-side {
      flex: 1 1 50%;
      background: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(24px, 5vw, 60px);
      position: relative;
      overflow: hidden;
    }

    .left-side::before {
      content: '';
      position: absolute;
      width: min(400px, 60vw);
      height: min(400px, 60vw);
      background: radial-gradient(circle, rgba(3,128,71,0.07) 0%, transparent 70%);
      border-radius: 50%;
      top: -20%;
      left: -10%;
      animation: floatA 9s ease-in-out infinite;
      pointer-events: none;
    }

    .left-side::after {
      content: '';
      position: absolute;
      width: min(280px, 45vw);
      height: min(280px, 45vw);
      background: radial-gradient(circle, rgba(30,58,95,0.06) 0%, transparent 70%);
      border-radius: 50%;
      bottom: -15%;
      right: -5%;
      animation: floatA 7s ease-in-out infinite reverse;
      pointer-events: none;
    }

    @keyframes floatA {
      0%, 100% { transform: translate(0, 0) scale(1); }
      50%       { transform: translate(20px, 20px) scale(1.08); }
    }

    .logo-container {
      text-align: center;
      width: 100%;
      max-width: 480px;
      position: relative;
      z-index: 1;
      animation: fadeInUp 0.8s ease-out both;
    }

    .logo-icon {
      width: 100%;
      max-width: min(420px, 85%);
      margin: 0 auto clamp(12px, 2.5vw, 24px);
      animation: scaleIn 0.7s ease-out 0.2s both;
    }

    .logo-icon img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform 0.35s ease;
    }

    .logo-icon:hover img {
      transform: scale(1.04);
    }

    .admin-badge {
      display: inline-block;
      background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
      color: var(--white);
      padding: clamp(6px, 1vw, 9px) clamp(16px, 2.5vw, 26px);
      border-radius: 20px;
      font-size: clamp(10px, 1.1vw, 12px);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-top: clamp(10px, 1.5vw, 16px);
      box-shadow: 0 4px 14px rgba(3,128,71,0.25);
      animation: slideInLeft 0.6s ease-out 0.4s both;
    }

    .powered-by {
      margin-top: clamp(20px, 4vw, 40px);
      font-size: clamp(11px, 1.2vw, 14px);
      color: #9CA3AF;
      font-weight: 600;
      letter-spacing: 0.5px;
      animation: fadeIn 1s ease-out 0.6s both;
    }

    /* ===================== RIGHT SIDE ===================== */
    .right-side {
      flex: 1 1 50%;
      background: linear-gradient(145deg, var(--navy) 0%, var(--navy-mid) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(24px, 5vw, 60px);
      position: relative;
      overflow: hidden;
    }

    .shape {
      position: absolute;
      border-radius: 50%;
      pointer-events: none;
    }

    .shape-1 {
      width: min(450px, 70vw);
      height: min(450px, 70vw);
      background: var(--green);
      opacity: 0.08;
      top: -30%;
      right: -20%;
      animation: spin 22s linear infinite;
    }

    .shape-2 {
      width: min(320px, 55vw);
      height: min(320px, 55vw);
      background: var(--white);
      opacity: 0.06;
      bottom: -25%;
      left: -15%;
      animation: spin 16s linear infinite reverse;
    }

    @keyframes spin {
      from { transform: rotate(0deg); }
      to   { transform: rotate(360deg); }
    }

    /* ===================== LOGIN BOX ===================== */
    .login-box {
      background: linear-gradient(145deg, var(--green) 0%, var(--green-dark) 100%);
      border-radius: clamp(16px, 2vw, 24px);
      padding: clamp(28px, 5vw, 52px) clamp(24px, 4vw, 44px);
      width: 100%;
      max-width: 420px;
      box-shadow: 0 24px 64px rgba(0,0,0,0.45), 0 4px 16px rgba(0,0,0,0.2);
      position: relative;
      z-index: 1;
      animation: slideInRight 0.8s ease-out both;
    }

    .login-header {
      text-align: center;
      margin-bottom: clamp(20px, 3.5vw, 32px);
    }

    .login-header h2 {
      color: var(--white);
      font-size: clamp(20px, 2.5vw, 28px);
      font-weight: 800;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
    }

    .login-header p {
      color: rgba(255,255,255,0.75);
      font-size: clamp(12px, 1.3vw, 14px);
      font-weight: 500;
    }

    /* ===================== ALERTS ===================== */
    .alert {
      padding: clamp(10px, 1.5vw, 14px) clamp(14px, 2vw, 18px);
      border-radius: 10px;
      margin-bottom: clamp(14px, 2vw, 20px);
      font-size: clamp(11px, 1.2vw, 13px);
      text-align: center;
      font-weight: 600;
      animation: slideDown 0.45s ease-out both;
    }

    .alert-success {
      background: #D1FAE5;
      color: #065F46;
    }

    .alert-error {
      background: #FEE2E2;
      color: #991B1B;
    }

    .alert-warning {
      background: #FEF3C7;
      color: #92400E;
      display: flex;
      align-items: flex-start;
      gap: 10px;
      text-align: left;
    }

    .alert-warning .alert-icon {
      font-size: 17px;
      flex-shrink: 0;
      margin-top: 1px;
    }

    .alert-warning .alert-body strong {
      display: block;
      font-size: clamp(11px, 1.2vw, 13px);
      margin-bottom: 2px;
    }

    .alert-warning .alert-body span {
      font-size: clamp(10px, 1.1vw, 12px);
      font-weight: 500;
      opacity: 0.85;
    }

    /* ===================== FORM ===================== */
    .form-group {
      margin-bottom: clamp(14px, 2vw, 20px);
    }

    .form-input {
      width: 100%;
      padding: clamp(12px, 1.8vw, 16px) clamp(14px, 2vw, 20px);
      border: 2px solid transparent;
      border-radius: 10px;
      font-family: 'Poppins', sans-serif;
      font-size: clamp(12px, 1.3vw, 14px);
      font-weight: 600;
      color: var(--navy);
      text-align: center;
      letter-spacing: 1px;
      background: var(--white);
      transition: all 0.3s ease;
    }

    .form-input::placeholder {
      color: #9CA3AF;
      font-weight: 500;
    }

    .form-input:focus {
      outline: none;
      border-color: rgba(255,255,255,0.5);
      box-shadow: 0 0 0 4px rgba(255,255,255,0.2);
      transform: translateY(-2px);
    }

    .form-input.is-invalid {
      border-color: #DC2626;
      animation: shake 0.45s ease-out;
    }

    .error-message {
      display: block;
      color: #FEE2E2;
      font-size: clamp(10px, 1.1vw, 12px);
      font-weight: 600;
      margin-top: 6px;
      text-align: center;
    }

    /* Remember + Forgot */
    .remember-section {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: clamp(18px, 2.5vw, 26px);
      flex-wrap: wrap;
      gap: 8px;
    }

    .checkbox-wrapper {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .checkbox-input {
      width: 17px;
      height: 17px;
      cursor: pointer;
      accent-color: var(--navy);
      flex-shrink: 0;
    }

    .checkbox-label {
      color: var(--white);
      font-size: clamp(11px, 1.2vw, 13px);
      font-weight: 600;
      cursor: pointer;
    }

    .forgot-password a {
      color: rgba(255,255,255,0.85);
      font-size: clamp(11px, 1.2vw, 13px);
      font-weight: 600;
      text-decoration: none;
      position: relative;
      transition: color 0.2s;
    }

    .forgot-password a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--white);
      transition: width 0.3s ease;
    }

    .forgot-password a:hover {
      color: var(--white);
    }

    .forgot-password a:hover::after {
      width: 100%;
    }

    /* ===================== BUTTON ===================== */
    .btn-submit {
      width: 100%;
      padding: clamp(12px, 1.8vw, 16px);
      background: var(--navy);
      color: var(--white);
      border: none;
      border-radius: 10px;
      font-family: 'Poppins', sans-serif;
      font-size: clamp(12px, 1.3vw, 15px);
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      position: relative;
      overflow: hidden;
    }

    .btn-submit::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,0.18);
      transform: translate(-50%, -50%);
      transition: width 0.55s, height 0.55s;
    }

    .btn-submit:hover::before {
      width: 320px;
      height: 320px;
    }

    .btn-submit:hover {
      background: var(--navy-dark);
      transform: translateY(-2px);
      box-shadow: 0 10px 24px rgba(0,0,0,0.35);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    .btn-submit span {
      position: relative;
      z-index: 1;
    }

    .btn-submit.loading {
      pointer-events: none;
      opacity: 0.7;
    }

    .btn-submit.loading::after {
      content: '';
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      border: 2px solid rgba(255,255,255,0.3);
      border-top-color: var(--white);
      border-radius: 50%;
      animation: spinBtn 0.8s linear infinite;
    }

    /* ===================== KEYFRAMES ===================== */
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(28px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes scaleIn {
      from { opacity: 0; transform: scale(0.92); }
      to   { opacity: 1; transform: scale(1); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to   { opacity: 1; }
    }

    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-16px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(40px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-28px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25%       { transform: translateX(-8px); }
      75%       { transform: translateX(8px); }
    }

    @keyframes spinBtn {
      to { transform: translateY(-50%) rotate(360deg); }
    }

    /* ===================== RESPONSIVE ===================== */

    /* Tablet landscape (768–1024px) */
    @media (max-width: 1024px) {
      .logo-icon { max-width: 340px; }
    }

    /* Tablet portrait (481–767px) — stack vertically */
    @media (max-width: 767px) {
      body {
        flex-direction: column;
        overflow-y: auto;
      }

      .left-side {
        flex: none;
        padding: 32px 24px 20px;
        min-height: auto;
      }

      .logo-icon {
        max-width: 260px;
        margin-bottom: 0;
      }

      .admin-badge {
        margin-top: 10px;
      }

      .powered-by {
        margin-top: 12px;
        font-size: 12px;
      }

      .right-side {
        flex: none;
        padding: 28px 20px 40px;
        min-height: auto;
      }

      .login-box {
        max-width: 100%;
        border-radius: 18px;
        padding: 28px 24px;
      }

      .login-header h2 { font-size: 22px; }
      .login-header p  { font-size: 13px; }
    }

    /* Mobile (≤480px) */
    @media (max-width: 480px) {
      .left-side {
        padding: 24px 20px 14px;
      }

      .logo-icon { max-width: 210px; }

      .right-side {
        padding: 20px 16px 36px;
      }

      .login-box {
        padding: 24px 18px;
        border-radius: 14px;
      }

      .login-header h2 {
        font-size: 20px;
        letter-spacing: 1px;
      }

      .form-input {
        font-size: 13px;
        padding: 13px 16px;
      }

      .btn-submit {
        font-size: 12px;
        padding: 13px;
        letter-spacing: 1px;
      }

      .alert {
        font-size: 12px;
        padding: 10px 12px;
      }

      .remember-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }

    /* Very small phones (≤360px) */
    @media (max-width: 360px) {
      .logo-icon { max-width: 180px; }

      .login-box {
        padding: 20px 14px;
      }
    }
  </style>
</head>

<body>

  <!-- Left Side - Logo -->
  <div class="left-side">
    <div class="logo-container">
      <div class="logo-icon">
        <img src="{{ asset('images/SMADIMENT 2025- warna.png') }}" alt="SMADIMENT Logo">
      </div>
      <div class="admin-badge">Administrator Access</div>
      <div class="powered-by">Powered by Alcomedia.id</div>
    </div>
  </div>

  <!-- Right Side - Login Form -->
  <div class="right-side">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="login-box">

      <div class="login-header">
        <h2>Admin Login</h2>
        <p>Secure Dashboard Access</p>
      </div>

      {{-- Success Message --}}
      @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
      @endif

      {{-- Error Message --}}
      @if($errors->any())
        @if(str_contains($errors->first(), '419'))
          <div class="alert alert-warning">
            <div class="alert-icon">&#9888;</div>
            <div class="alert-body">
              <strong>Session Expired!</strong>
              <span>Halaman ini sudah kadaluarsa. Silakan refresh dan login ulang.</span>
            </div>
          </div>
        @elseif(str_contains($errors->first(), '409'))
          <div class="alert alert-warning">
            <div class="alert-icon">&#9888;</div>
            <div class="alert-body">
              <strong>Conflict Detected!</strong>
              <span>Terjadi konflik data. Silakan coba lagi.</span>
            </div>
          </div>
        @else
          <div class="alert alert-error">
            {{ $errors->first() }}
          </div>
        @endif
      @endif

      <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
        @csrf

        <div class="form-group">
          <input
            type="email"
            name="email"
            class="form-input @error('email') is-invalid @enderror"
            value="{{ old('email') }}"
            required
            autofocus
            placeholder="Email Address"
          >
          @error('email')
          <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <input
            type="password"
            name="password"
            class="form-input @error('password') is-invalid @enderror"
            required
            placeholder="Password"
          >
          @error('password')
          <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <div class="remember-section">
          <div class="checkbox-wrapper">
            <input
              type="checkbox"
              id="remember"
              name="remember"
              class="checkbox-input"
            >
            <label for="remember" class="checkbox-label">Remember me</label>
          </div>
          <div class="forgot-password">
            <a href="#">Lupa Password?</a>
          </div>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
          <span>LOGIN TO DASHBOARD</span>
        </button>

      </form>

    </div>
  </div>

  <script>
    document.getElementById('loginForm').addEventListener('submit', function () {
      const btn = document.getElementById('submitBtn');
      btn.classList.add('loading');
    });

    document.querySelectorAll('.form-input').forEach(input => {
      input.addEventListener('focus', function () {
        this.closest('.form-group').style.transform = 'translateY(-2px)';
        this.closest('.form-group').style.transition = 'transform 0.3s ease';
      });
      input.addEventListener('blur', function () {
        this.closest('.form-group').style.transform = 'translateY(0)';
      });
    });
  </script>

</body>
</html>