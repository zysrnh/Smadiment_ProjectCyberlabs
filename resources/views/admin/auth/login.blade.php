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
    :root {
      --primary-green: #038047;
      --dark-blue: #273B4A;
      --white: #FFFFFF;
      --light-gray: #F1F5F8;
      --text-dark: #273B4A;
      --error-red: #FF6B6B;
      --success-green: #00BCD4;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, var(--dark-blue) 0%, #1a2d3a 100%);
      color: var(--text-dark);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .login-container {
      width: 100%;
      max-width: 480px;
    }

    .login-card {
      background: var(--white);
      border-radius: 24px;
      padding: 48px 40px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      position: relative;
      overflow: hidden;
    }

    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: linear-gradient(90deg, var(--primary-green) 0%, #00BCD4 100%);
    }

    .logo-section {
      text-align: center;
      margin-bottom: 40px;
    }

    .logo-section h1 {
      font-size: 40px;
      font-weight: 900;
      color: var(--primary-green);
      letter-spacing: -1px;
      margin-bottom: 8px;
    }

    .logo-section p {
      font-size: 14px;
      color: var(--dark-blue);
      font-weight: 600;
    }

    .login-title {
      font-size: 24px;
      font-weight: 800;
      color: var(--dark-blue);
      margin-bottom: 8px;
      text-align: center;
    }

    .login-subtitle {
      font-size: 14px;
      color: #7A8B96;
      font-weight: 600;
      text-align: center;
      margin-bottom: 32px;
    }

    .alert {
      padding: 14px 18px;
      border-radius: 12px;
      margin-bottom: 24px;
      font-size: 13px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .alert-error {
      background: rgba(255, 107, 107, 0.1);
      color: var(--error-red);
      border: 1px solid rgba(255, 107, 107, 0.3);
    }

    .alert-success {
      background: rgba(0, 188, 212, 0.1);
      color: var(--success-green);
      border: 1px solid rgba(0, 188, 212, 0.3);
    }

    .form-group {
      margin-bottom: 24px;
    }

    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 700;
      color: var(--dark-blue);
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-input {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid var(--light-gray);
      border-radius: 12px;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      font-weight: 600;
      color: var(--dark-blue);
      transition: all 0.2s;
      background: var(--white);
    }

    .form-input:focus {
      outline: none;
      border-color: var(--primary-green);
      background: rgba(3, 128, 71, 0.02);
    }

    .form-input.error {
      border-color: var(--error-red);
    }

    .error-message {
      color: var(--error-red);
      font-size: 12px;
      font-weight: 600;
      margin-top: 6px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .remember-section {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 24px;
    }

    .checkbox-wrapper {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .checkbox-input {
      width: 20px;
      height: 20px;
      cursor: pointer;
      accent-color: var(--primary-green);
    }

    .checkbox-label {
      font-size: 13px;
      font-weight: 600;
      color: var(--dark-blue);
      cursor: pointer;
    }

    .btn-login {
      width: 100%;
      padding: 16px;
      background: var(--primary-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-family: 'Poppins', sans-serif;
      font-size: 15px;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.3s;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }

    .btn-login:hover {
      background: #025a34;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .footer-text {
      text-align: center;
      margin-top: 32px;
      padding-top: 24px;
      border-top: 2px solid var(--light-gray);
      font-size: 12px;
      color: #7A8B96;
      font-weight: 600;
    }

    /* Animations */
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-card {
      animation: slideIn 0.5s ease;
    }

    /* Responsive */
    @media (max-width: 576px) {
      .login-card {
        padding: 32px 24px;
      }

      .logo-section h1 {
        font-size: 32px;
      }

      .login-title {
        font-size: 20px;
      }
    }
  </style>
</head>

<body>

  <div class="login-container">
    <div class="login-card">
      
      <!-- Logo Section -->
      <div class="logo-section">
        <h1>SMADIMENT</h1>
        <p>Social Media Analytics Dashboard</p>
      </div>

      <!-- Login Title -->
      <h2 class="login-title">Admin Login</h2>
      <p class="login-subtitle">Enter your credentials to access the dashboard</p>

      <!-- Success Message -->
      @if(session('success'))
      <div class="alert alert-success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        {{ session('success') }}
      </div>
      @endif

      <!-- Error Message -->
      @if($errors->any())
      <div class="alert alert-error">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        @foreach($errors->all() as $error)
          {{ $error }}
        @endforeach
      </div>
      @endif

      <!-- Login Form -->
      <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <!-- Email Field -->
        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input @error('email') error @enderror" 
            value="{{ old('email') }}"
            placeholder="admin@smadiment.com"
            required 
            autofocus
          >
          @error('email')
          <div class="error-message">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            {{ $message }}
          </div>
          @enderror
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-input @error('password') error @enderror" 
            placeholder="Enter your password"
            required
          >
          @error('password')
          <div class="error-message">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            {{ $message }}
          </div>
          @enderror
        </div>

        <!-- Remember Me -->
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
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-login">
          Login to Dashboard
        </button>

      </form>

      <!-- Footer -->
      <div class="footer-text">
        © 2025 SMADIMENT. All rights reserved.
      </div>

    </div>
  </div>

</body>
</html>