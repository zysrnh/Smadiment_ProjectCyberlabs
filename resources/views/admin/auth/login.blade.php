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
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      overflow: hidden;
      display: flex;
    }

    /* Left Side - White */
    .left-side {
      flex: 1;
      background: #FFFFFF;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
      position: relative;
      overflow: hidden;
    }

    /* Animated background particles */
    .left-side::before {
      content: '';
      position: absolute;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(3, 128, 71, 0.05) 0%, transparent 70%);
      border-radius: 50%;
      top: -100px;
      left: -100px;
      animation: float 8s ease-in-out infinite;
    }

    .left-side::after {
      content: '';
      position: absolute;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(30, 58, 95, 0.05) 0%, transparent 70%);
      border-radius: 50%;
      bottom: -50px;
      right: -50px;
      animation: float 6s ease-in-out infinite reverse;
    }

    @keyframes float {
      0%, 100% {
        transform: translate(0, 0) scale(1);
      }
      50% {
        transform: translate(30px, 30px) scale(1.1);
      }
    }

    .logo-container {
      text-align: center;
      max-width: 450px;
      width: 100%;
      position: relative;
      z-index: 1;
      animation: fadeInUp 0.8s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .logo-image {
      margin-bottom: 32px;
    }

    .logo-icon {
      width: auto;
      max-width: 650px;
      margin: 0 auto 24px;
      animation: scaleIn 0.6s ease-out 0.2s both;
    }

    @keyframes scaleIn {
      from {
        opacity: 0;
        transform: scale(0.9);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .logo-icon img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform 0.3s ease;
    }

    .logo-icon:hover img {
      transform: scale(1.05);
    }

    .admin-badge {
      display: inline-block;
      background: linear-gradient(135deg, #038047 0%, #025a34 100%);
      color: #FFFFFF;
      padding: 8px 24px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-top: 16px;
      animation: slideInLeft 0.6s ease-out 0.4s both;
      box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }

    @keyframes slideInLeft {
      from {
        opacity: 0;
        transform: translateX(-30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .powered-by {
      margin-top: 40px;
      font-size: 14px;
      color: #6B7280;
      font-weight: 600;
      animation: fadeIn 1s ease-out 0.6s both;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    /* Right Side - Blue/Green */
    .right-side {
      flex: 1;
      background: linear-gradient(135deg, #1e3a5f 0%, #2d5f8d 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
      position: relative;
      overflow: hidden;
    }

    /* Animated background shapes */
    .shape {
      position: absolute;
      border-radius: 50%;
      opacity: 0.1;
    }

    .shape-1 {
      width: 400px;
      height: 400px;
      background: #038047;
      top: -200px;
      right: -200px;
      animation: rotate 20s linear infinite;
    }

    .shape-2 {
      width: 300px;
      height: 300px;
      background: #FFFFFF;
      bottom: -150px;
      left: -150px;
      animation: rotate 15s linear infinite reverse;
    }

    @keyframes rotate {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }

    .login-box {
      background: linear-gradient(135deg, #038047 0%, #025a34 100%);
      border-radius: 24px;
      padding: 48px 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
      position: relative;
      z-index: 1;
      animation: slideInRight 0.8s ease-out;
    }

    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(50px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .login-header {
      text-align: center;
      margin-bottom: 32px;
    }

    .login-header h2 {
      color: #FFFFFF;
      font-size: 28px;
      font-weight: 800;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .login-header p {
      color: rgba(255, 255, 255, 0.8);
      font-size: 14px;
      font-weight: 600;
    }

    .form-group {
      margin-bottom: 20px;
      animation: fadeInUp 0.6s ease-out both;
    }

    .form-group:nth-child(1) {
      animation-delay: 0.2s;
    }

    .form-group:nth-child(2) {
      animation-delay: 0.3s;
    }

    .form-input {
      width: 100%;
      padding: 16px 20px;
      border: none;
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      font-weight: 600;
      color: #1e3a5f;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      background: #FFFFFF;
    }

    .form-input::placeholder {
      color: #1e3a5f;
      font-weight: 600;
    }

    .form-input:focus {
      outline: none;
      box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.3);
      transform: translateY(-2px);
    }

    .form-input.is-invalid {
      border: 2px solid #DC2626;
      animation: shake 0.5s;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-10px); }
      75% { transform: translateX(10px); }
    }

    .error-message {
      display: block;
      color: #FEE2E2;
      font-size: 12px;
      font-weight: 600;
      margin-top: 6px;
      text-align: center;
      animation: fadeIn 0.3s ease-out;
    }

    .remember-section {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      animation: fadeInUp 0.6s ease-out 0.4s both;
    }

    .checkbox-wrapper {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .checkbox-input {
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: #1e3a5f;
    }

    .checkbox-label {
      color: #FFFFFF;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
    }

    .forgot-password a {
      color: #FFFFFF;
      font-size: 13px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s;
      position: relative;
    }

    .forgot-password a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: #FFFFFF;
      transition: width 0.3s ease;
    }

    .forgot-password a:hover::after {
      width: 100%;
    }

    .btn-submit {
      width: 100%;
      padding: 16px;
      background: #1e3a5f;
      color: #FFFFFF;
      border: none;
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.6s ease-out 0.5s both;
    }

    .btn-submit::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .btn-submit:hover::before {
      width: 300px;
      height: 300px;
    }

    .btn-submit:hover {
      background: #152d47;
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    .btn-submit span {
      position: relative;
      z-index: 1;
    }

    .alert {
      padding: 14px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 13px;
      text-align: center;
      font-weight: 600;
      animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .alert-success {
      background: #D1FAE5;
      color: #065F46;
    }

    .alert-error {
      background: #FEE2E2;
      color: #991B1B;
    }

    /* Loading animation for submit button */
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
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top-color: #FFFFFF;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: translateY(-50%) rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 968px) {
      body {
        flex-direction: column;
      }

      .left-side, .right-side {
        padding: 20px;
      }

      .logo-icon {
        max-width: 350px;
      }

      .login-box {
        padding: 32px 24px;
      }

      .login-header h2 {
        font-size: 24px;
      }
    }
  </style>
</head>

<body>

  <!-- Left Side - Logo -->
  <div class="left-side">
    <div class="logo-container">
      <div class="logo-image">
        <div class="logo-icon">
          <img src="{{ asset('images/SMADIMENT 2025 _ Logo-03.png') }}" alt="SMADIMENT Logo">
        </div>
      </div>
      <div class="admin-badge">
        Administrator Access
      </div>
      <div class="powered-by">
        Powered by Alcomedia.id
      </div>
    </div>
  </div>

  <!-- Right Side - Login Form -->
  <div class="right-side">
    <!-- Animated background shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="login-box">
      
      <!-- Login Header -->
      <div class="login-header">
        <h2>Admin Login</h2>
        <p>Secure Dashboard Access</p>
      </div>

      <!-- Success Message -->
      @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
      @endif

      <!-- Error Message -->
      @if($errors->any())
      <div class="alert alert-error">
        {{ $errors->first() }}
      </div>
      @endif

      <!-- Login Form -->
      <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
        @csrf

        <!-- Email -->
        <div class="form-group">
          <input 
            type="email" 
            name="email" 
            class="form-input @error('email') is-invalid @enderror" 
            value="{{ old('email') }}" 
            required 
            autofocus
            placeholder="EMAIL ADDRESS"
          >
          @error('email')
          <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
          <input 
            type="password" 
            name="password" 
            class="form-input @error('password') is-invalid @enderror" 
            required
            placeholder="PASSWORD"
          >
          @error('password')
          <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
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
            <a href="#">Forgot Password?</a>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit" id="submitBtn">
          <span>LOGIN TO DASHBOARD</span>
        </button>

      </form>

    </div>
  </div>

  <script>
    // Add loading animation on form submit
    document.getElementById('loginForm').addEventListener('submit', function() {
      const btn = document.getElementById('submitBtn');
      btn.classList.add('loading');
    });

    // Add input focus animations
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'translateY(-2px)';
        this.parentElement.style.transition = 'transform 0.3s ease';
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'translateY(0)';
      });
    });
  </script>

</body>
</html>