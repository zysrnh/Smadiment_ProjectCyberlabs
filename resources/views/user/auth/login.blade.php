<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Login - SMADIMENT</title>

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
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #273B4A 0%, #1a2732 100%);
      color: var(--text-dark);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-container {
      width: 100%;
      max-width: 450px;
    }

    .login-card {
      background: var(--white);
      border-radius: 24px;
      padding: 48px 40px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .logo-section {
      text-align: center;
      margin-bottom: 40px;
    }

    .logo-section h1 {
      font-size: 36px;
      font-weight: 900;
      color: var(--primary-green);
      letter-spacing: -1px;
      margin-bottom: 8px;
    }

    .logo-section p {
      font-size: 14px;
      color: #6B7280;
      font-weight: 600;
    }

    .welcome-text {
      text-align: center;
      margin-bottom: 32px;
    }

    .welcome-text h2 {
      font-size: 24px;
      font-weight: 800;
      color: var(--dark-blue);
      margin-bottom: 8px;
    }

    .welcome-text p {
      font-size: 14px;
      color: #6B7280;
    }

    .alert {
      padding: 14px 16px;
      border-radius: 10px;
      margin-bottom: 24px;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .alert-success {
      background: #D1FAE5;
      border: 2px solid #10B981;
      color: #065F46;
      font-weight: 600;
    }

    .alert-error {
      background: #FEE2E2;
      border: 2px solid #EF4444;
      color: #991B1B;
      font-weight: 600;
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
    }

    .form-input {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid var(--light-gray);
      border-radius: 12px;
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      font-weight: 500;
      color: var(--dark-blue);
      transition: all 0.2s;
    }

    .form-input:focus {
      outline: none;
      border-color: var(--primary-green);
      box-shadow: 0 0 0 4px rgba(3, 128, 71, 0.1);
    }

    .form-input.is-invalid {
      border-color: #EF4444;
    }

    .error-message {
      display: block;
      color: #DC2626;
      font-size: 12px;
      font-weight: 600;
      margin-top: 6px;
    }

    .remember-me {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 24px;
    }

    .remember-me input[type="checkbox"] {
      width: 18px;
      height: 18px;
      cursor: pointer;
    }

    .remember-me label {
      font-size: 13px;
      font-weight: 600;
      color: var(--dark-blue);
      cursor: pointer;
    }

    .btn-login {
      width: 100%;
      padding: 14px;
      background: var(--primary-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-family: 'Poppins', sans-serif;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-login:hover {
      background: #025a34;
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(3, 128, 71, 0.3);
    }

    .divider {
      text-align: center;
      margin: 32px 0;
      position: relative;
    }

    .divider::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      width: 100%;
      height: 1px;
      background: var(--light-gray);
    }

    .divider span {
      position: relative;
      background: var(--white);
      padding: 0 16px;
      font-size: 12px;
      font-weight: 600;
      color: #9CA3AF;
    }

    .admin-link {
      text-align: center;
    }

    .admin-link a {
      color: var(--primary-green);
      font-size: 13px;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.2s;
    }

    .admin-link a:hover {
      color: #025a34;
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .login-card {
        padding: 32px 24px;
      }

      .logo-section h1 {
        font-size: 28px;
      }

      .welcome-text h2 {
        font-size: 20px;
      }
    }
  </style>
</head>

<body>

  <div class="login-container">
    <div class="login-card">
      
      <!-- Logo -->
      <div class="logo-section">
        <h1>SMADIMENT</h1>
        <p>Social Media Analytics Dashboard</p>
      </div>

      <!-- Welcome Text -->
      <div class="welcome-text">
        <h2>User Login</h2>
        <p>Access your analytics dashboard</p>
      </div>

      <!-- Success Message -->
      @if(session('success'))
      <div class="alert alert-success">
        <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        {{ session('success') }}
      </div>
      @endif

      <!-- Login Form -->
      <form method="POST" action="{{ route('user.login.submit') }}">
        @csrf

        <!-- Email -->
        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input @error('email') is-invalid @enderror" 
            value="{{ old('email') }}" 
            required 
            autofocus
            placeholder="your.email@example.com"
          >
          @error('email')
          <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-input @error('password') is-invalid @enderror" 
            required
            placeholder="Enter your password"
          >
          @error('password')
          <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <!-- Remember Me -->
        <div class="remember-me">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Remember me</label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-login">
          <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <polyline points="10 17 15 12 10 7"/>
            <line x1="15" y1="12" x2="3" y2="12"/>
          </svg>
          Sign In
        </button>
      </form>

      <!-- Divider -->
      <div class="divider">
        <span>OR</span>
      </div>

      <!-- Admin Link -->
      <div class="admin-link">
        <a href="{{ route('admin.login') }}">
          <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; display: inline; vertical-align: middle;">
            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
            <polyline points="2 17 12 22 22 17"/>
            <polyline points="2 12 12 17 22 12"/>
          </svg>
          Admin Login
        </a>
      </div>

    </div>
  </div>

</body>
</html>