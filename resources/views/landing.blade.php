<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SMADIMENT Intelligence</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --green:       #038047;
      --green-dark:  #025a34;
      --green-light: #05b865;
      --navy:        #1e3a5f;
      --navy-dark:   #152d47;
      --navy-mid:    #2d5f8d;
      --white:       #FFFFFF;
    }

    html, body {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      -webkit-font-smoothing: antialiased;
    }

    body {
      display: flex;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* ══════════════════════════════════════
       LEFT SIDE — white, logo + tagline
    ══════════════════════════════════════ */
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

    /* subtle radial glow identical to login page */
    .left-side::before {
      content: '';
      position: absolute;
      width: min(400px, 60vw);
      height: min(400px, 60vw);
      background: radial-gradient(circle, rgba(3,128,71,0.07) 0%, transparent 70%);
      border-radius: 50%;
      top: -20%; left: -10%;
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
      bottom: -15%; right: -5%;
      animation: floatA 7s ease-in-out infinite reverse;
      pointer-events: none;
    }

    .left-content {
      text-align: center;
      width: 100%;
      max-width: 480px;
      position: relative;
      z-index: 1;
      animation: fadeInUp 0.8s ease-out both;
    }

    .logo-wrap {
      width: 100%;
      max-width: min(420px, 85%);
      margin: 0 auto clamp(16px, 3vw, 28px);
      animation: scaleIn 0.7s ease-out 0.2s both;
    }

    .logo-wrap img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform 0.35s ease;
    }

    .logo-wrap:hover img {
      transform: scale(1.03);
    }

    .tagline {
      font-size: clamp(12px, 1.3vw, 15px);
      font-weight: 600;
      color: var(--navy);
      letter-spacing: 0.3px;
      margin-bottom: 6px;
      animation: fadeIn 1s ease-out 0.5s both;
    }

    .tagline em {
      font-style: italic;
      color: var(--green);
    }

    .powered-by {
      margin-top: clamp(32px, 5vw, 56px);
      font-size: clamp(11px, 1.1vw, 13px);
      color: #9CA3AF;
      font-weight: 600;
      letter-spacing: 0.5px;
      animation: fadeIn 1s ease-out 0.7s both;
    }

    /* ══════════════════════════════════════
       RIGHT SIDE — navy, headline + CTA
    ══════════════════════════════════════ */
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

    /* same decorative shapes as login page */
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
      top: -30%; right: -20%;
      animation: spin 22s linear infinite;
    }

    .shape-2 {
      width: min(320px, 55vw);
      height: min(320px, 55vw);
      background: var(--white);
      opacity: 0.05;
      bottom: -25%; left: -15%;
      animation: spin 16s linear infinite reverse;
    }

    /* ── Content box (no border-radius card, just padded block) ── */
    .right-content {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 460px;
      animation: slideInRight 0.8s ease-out both;
    }

    /* eyebrow badge */
    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: clamp(10px, 1.1vw, 12px);
      font-weight: 700;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--green-light);
      margin-bottom: clamp(18px, 2.5vw, 26px);
    }

    .eyebrow-dot {
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--green-light);
      flex-shrink: 0;
    }

    /* headline */
    .headline {
      font-size: clamp(28px, 3.8vw, 50px);
      font-weight: 900;
      line-height: 1.12;
      letter-spacing: -0.5px;
      color: var(--white);
      margin-bottom: clamp(16px, 2.5vw, 24px);
    }

    .headline .plain {
      display: block;
    }

    .headline .highlight {
      display: block;
      background: linear-gradient(90deg, var(--green-light), #7fffc4);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* subtext */
    .subtext {
      font-size: clamp(13px, 1.4vw, 16px);
      font-weight: 400;
      color: rgba(255, 255, 255, 0.68);
      line-height: 1.8;
      margin-bottom: clamp(28px, 4vw, 44px);
    }

    /* divider rule */
    .rule {
      width: 48px;
      height: 3px;
      background: linear-gradient(90deg, var(--green-light), rgba(5,184,101,0.2));
      border-radius: 2px;
      margin-bottom: clamp(20px, 3vw, 32px);
    }

    /* CTA button */
    .btn-cta {
      display: inline-block;
      background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
      color: var(--white);
      text-decoration: none;
      font-family: 'Poppins', sans-serif;
      font-size: clamp(13px, 1.4vw, 15px);
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: clamp(14px, 1.8vw, 18px) clamp(36px, 5vw, 56px);
      border-radius: 10px;
      border: none;
      cursor: pointer;
      transition: all 0.28s ease;
      box-shadow:
        0 6px 20px rgba(3, 128, 71, 0.4),
        0 2px 6px rgba(3, 128, 71, 0.25);
      position: relative;
      overflow: hidden;
    }

    .btn-cta::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, var(--green-light) 0%, var(--green) 100%);
      opacity: 0;
      transition: opacity 0.28s ease;
    }

    .btn-cta:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 32px rgba(3,128,71,0.48), 0 3px 8px rgba(3,128,71,0.25);
    }

    .btn-cta:hover::before { opacity: 1; }
    .btn-cta:active { transform: translateY(0); }

    .btn-cta span {
      position: relative;
      z-index: 1;
    }

    /* login note below button */
    .login-note {
      margin-top: clamp(14px, 2vw, 20px);
      font-size: clamp(11px, 1.1vw, 13px);
      font-weight: 500;
      color: rgba(255,255,255,0.45);
    }

    /* ══════════════════════════════════════
       KEYFRAMES
    ══════════════════════════════════════ */
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
    @keyframes slideInRight {
      from { opacity: 0; transform: translateX(40px); }
      to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes floatA {
      0%, 100% { transform: translate(0, 0) scale(1); }
      50%       { transform: translate(20px, 20px) scale(1.08); }
    }
    @keyframes spin {
      from { transform: rotate(0deg); }
      to   { transform: rotate(360deg); }
    }

    /* ══════════════════════════════════════
       RESPONSIVE — tablet (stack vertical)
    ══════════════════════════════════════ */
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

      .logo-wrap {
        max-width: 260px;
        margin-bottom: 0;
      }

      .powered-by { margin-top: 14px; }

      .right-side {
        flex: none;
        padding: 40px 28px 56px;
        min-height: auto;
      }

      .right-content {
        max-width: 100%;
      }
    }

    /* ══════════════════════════════════════
       RESPONSIVE — mobile
    ══════════════════════════════════════ */
    @media (max-width: 480px) {
      .left-side { padding: 24px 20px 16px; }
      .logo-wrap  { max-width: 210px; }
      .right-side { padding: 32px 20px 48px; }

      .headline {
        font-size: clamp(24px, 7vw, 34px);
        letter-spacing: -0.3px;
      }

      .btn-cta {
        width: 100%;
        text-align: center;
        padding: 15px 24px;
      }
    }

    @media (max-width: 360px) {
      .logo-wrap  { max-width: 180px; }
    }
  </style>
</head>
<body>

  <!-- ══ LEFT ══ -->
  <div class="left-side">
    <div class="left-content">

      <div class="logo-wrap">
        <img
          src="{{ asset('images/SMADIMENT 2025- warna.png') }}"
          alt="SMADIMENT Intelligence"
        >
      </div>

      <p class="tagline">
        <em>Understanding People, Empowering Decisions</em>
      </p>

      <p class="powered-by">Powered by Alcomedia.id</p>

    </div>
  </div>

  <!-- ══ RIGHT ══ -->
  <div class="right-side">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="right-content">

      <div class="eyebrow">
        <span class="eyebrow-dot"></span>
        Social Media Intelligence
      </div>

      <h1 class="headline">
        <span class="plain">Pantau, Analisis &amp;</span>
        <span class="highlight">Kuasai Narasi Digital</span>
      </h1>

      <div class="rule"></div>

      <p class="subtext">
        Platform analitik berbasis AI untuk memantau tren, sentimen,
        dan engagement secara real-time — dari X, Instagram, Facebook,
        YouTube, TikTok, hingga News Media.
      </p>

      <a href="/user/login" class="btn-cta">
        <span>Mulai Sekarang</span>
      </a>

      <p class="login-note">Sudah punya akun? Klik tombol di atas untuk login.</p>

    </div>
  </div>

</body>
</html>