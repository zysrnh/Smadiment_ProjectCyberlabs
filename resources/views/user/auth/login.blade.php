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
    }

    .logo-container {
      text-align: center;
      max-width: 450px;
      width: 100%;
    }

    .logo-image {
      margin-bottom: 32px;
    }

    .logo-icon {
      width: auto;
      max-width: 650px;
      margin: 0 auto 24px;
    }

    .logo-icon img {
      width: 100%;
      height: auto;
      display: block;
    }

    .logo-text h1 {
      font-size: 48px;
      font-weight: 900;
      margin-bottom: 8px;
      display: none;
    }

    .logo-text h1 .smadi {
      color: #1e3a5f;
    }

    .logo-text h1 .ment {
      color: #038047;
    }

    .logo-text p {
      font-size: 18px;
      font-weight: 600;
      color: #1e3a5f;
      text-transform: uppercase;
      letter-spacing: 2px;
      display: none;
    }

    .powered-by {
      margin-top: 40px;
      font-size: 14px;
      color: #6B7280;
      font-weight: 600;
    }

    /* Right Side - Blue/Green */
    .right-side {
      flex: 1;
      background: linear-gradient(135deg, #1e3a5f 0%, #2d5f8d 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }

    .login-box {
      background: linear-gradient(135deg, #038047 0%, #025a34 100%);
      border-radius: 24px;
      padding: 48px 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    }

    .form-group {
      margin-bottom: 20px;
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
      transition: all 0.2s;
      background: #FFFFFF;
    }

    .form-input::placeholder {
      color: #1e3a5f;
      font-weight: 600;
    }

    .form-input:focus {
      outline: none;
      box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.3);
    }

    .form-input.is-invalid {
      border: 2px solid #DC2626;
    }

    .error-message {
      display: block;
      color: #FEE2E2;
      font-size: 12px;
      font-weight: 600;
      margin-top: 6px;
      text-align: center;
    }

    .forgot-password {
      text-align: left;
      margin-bottom: 24px;
    }

    .forgot-password a {
      color: #FFFFFF;
      font-size: 13px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s;
    }

    .forgot-password a:hover {
      text-decoration: underline;
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
      transition: all 0.2s;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .btn-submit:hover {
      background: #152d47;
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .alert {
      padding: 14px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 13px;
      text-align: center;
      font-weight: 600;
    }

    .alert-success {
      background: #D1FAE5;
      color: #065F46;
    }

    .alert-error {
      background: #FEE2E2;
      color: #991B1B;
    }

    /* Responsive */
    @media (max-width: 968px) {
      body {
        flex-direction: column;
      }

      .left-side {
        padding: 20px;
      }

      .logo-icon {
        max-width: 350px;
      }

      .logo-text h1 {
        font-size: 32px;
      }

      .logo-text p {
        font-size: 14px;
      }

      .right-side {
        padding: 20px;
      }

      .login-box {
        padding: 32px 24px;
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
      <div class="logo-text">
        <h1>
          <span class="smadi">SMADI</span><span class="ment">MENT</span>
        </h1>
        <p>INTELLIGENCE</p>
      </div>
      <div class="powered-by">
        Powered by Alcomedia.id
      </div>
    </div>
  </div>

  <!-- Right Side - Login Form -->
  <div class="right-side">
    <div class="login-box">

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
      <form method="POST" action="{{ route('user.login.submit') }}">
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
            placeholder="EMAIL"
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

        <!-- Forgot Password -->
        <div class="forgot-password">
          <a href="#">Forgot Password?</a>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit">
          SUBMIT
        </button>

      </form>

    </div>
  </div>

</body>
</html>