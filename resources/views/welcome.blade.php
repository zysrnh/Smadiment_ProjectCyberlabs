<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SMADIMENT — Analytic Platform</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,700&display=swap" rel="stylesheet">
  <style>
    :root {
      --navy:       #0d1f35;
      --navy-mid:   #1e3a5f;
      --green:      #038047;
      --green-dark: #025a34;
      --gold:       #c9a84c;
      --white:      #ffffff;
      --muted:      rgba(255,255,255,0.45);
      --border:     rgba(255,255,255,0.08);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--navy);
      color: var(--white);
      overflow-x: hidden;
    }

    /* ── NAV ── */
    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      height: 68px;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 48px;
      transition: background 0.3s, border-color 0.3s;
      border-bottom: 1px solid transparent;
    }
    nav.scrolled {
      background: rgba(13,31,53,0.9);
      backdrop-filter: blur(16px);
      border-bottom-color: var(--border);
    }
    .nav-logo img { height: 36px; }
    .nav-right { display: flex; align-items: center; gap: 32px; }
    .nav-right a {
      font-size: 13px; font-weight: 500;
      color: var(--muted); text-decoration: none;
      transition: color 0.2s;
    }
    .nav-right a:hover { color: var(--white); }
    .nav-btn {
      padding: 9px 20px;
      background: var(--green);
      color: var(--white) !important;
      border-radius: 5px;
      font-size: 12px !important; font-weight: 600 !important;
      letter-spacing: 0.5px;
    }
    .nav-btn:hover { background: var(--green-dark) !important; }

    /* ── HERO ── */
    .hero {
      min-height: 100vh;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      text-align: center;
      padding: 130px 24px 90px;
      position: relative; overflow: hidden;
    }

    /* subtle radial bg */
    .hero::before {
      content: '';
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse 70% 60% at 50% 10%, rgba(3,128,71,0.13) 0%, transparent 70%),
        radial-gradient(ellipse 50% 50% at 80% 80%, rgba(30,58,95,0.3) 0%, transparent 70%);
      pointer-events: none;
    }

    /* grid */
    .hero::after {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
      background-size: 56px 56px;
      mask-image: radial-gradient(ellipse 75% 75% at 50% 50%, black 20%, transparent 100%);
      pointer-events: none;
    }

    .hero-inner { position: relative; z-index: 2; max-width: 680px; }

    .hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 6px 16px;
      border: 1px solid rgba(201,168,76,0.25);
      border-radius: 100px;
      background: rgba(201,168,76,0.07);
      margin-bottom: 28px;
      animation: up 0.7s ease both;
    }
    .hero-badge-dot {
      width: 6px; height: 6px; border-radius: 50%;
      background: var(--gold);
      animation: blink 2s ease-in-out infinite;
    }
    .hero-badge span {
      font-size: 10.5px; font-weight: 600;
      letter-spacing: 2.5px; text-transform: uppercase;
      color: var(--gold);
    }

    .hero-title {
      font-size: clamp(40px, 6vw, 76px);
      font-weight: 800; line-height: 1.05;
      letter-spacing: -1.5px;
      margin-bottom: 20px;
      animation: up 0.7s ease 0.08s both;
    }
    .hero-title em { font-style: italic; color: var(--green); }

    .hero-sub {
      font-size: 15px; font-weight: 300;
      color: var(--muted); line-height: 1.8;
      max-width: 500px; margin: 0 auto 44px;
      animation: up 0.7s ease 0.16s both;
    }

    .hero-cta {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 15px 32px;
      background: var(--green); color: var(--white);
      text-decoration: none; border-radius: 6px;
      font-size: 13px; font-weight: 600; letter-spacing: 0.5px;
      transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
      animation: up 0.7s ease 0.24s both;
    }
    .hero-cta:hover {
      background: var(--green-dark);
      transform: translateY(-2px);
      box-shadow: 0 12px 32px rgba(3,128,71,0.4);
    }
    .hero-cta svg { transition: transform 0.2s; }
    .hero-cta:hover svg { transform: translateX(3px); }

    /* Hero illustration */
    .hero-visual {
      position: relative; z-index: 2;
      margin-top: 64px;
      animation: up 0.7s ease 0.32s both;
    }

    /* ── SECTION COMMON ── */
    section { padding: 100px 24px; }
    .wrap { max-width: 1100px; margin: 0 auto; }

    .sec-label {
      display: inline-flex; align-items: center; gap: 10px;
      margin-bottom: 14px;
    }
    .sec-label-line { width: 24px; height: 1px; background: var(--green); }
    .sec-label span {
      font-size: 10px; font-weight: 600;
      letter-spacing: 3.5px; text-transform: uppercase;
      color: var(--green);
    }
    .sec-title {
      font-size: clamp(28px, 3.5vw, 44px);
      font-weight: 700; line-height: 1.15;
      letter-spacing: -0.5px; margin-bottom: 14px;
    }
    .sec-title em { font-style: italic; color: var(--green); }
    .sec-desc {
      font-size: 14px; font-weight: 300;
      color: var(--muted); line-height: 1.75;
      max-width: 480px;
    }

    /* ── FEATURES ── */
    .feat-head {
      display: flex; justify-content: space-between;
      align-items: flex-end; gap: 40px;
      margin-bottom: 56px; flex-wrap: wrap;
    }
    .feat-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1px;
      background: var(--border);
      border: 1px solid var(--border);
      border-radius: 14px; overflow: hidden;
    }
    .fc {
      background: rgba(255,255,255,0.025);
      padding: 36px 30px;
      position: relative; overflow: hidden;
      transition: background 0.25s;
    }
    .fc::before {
      content: '';
      position: absolute; top: 0; left: 0; right: 0; height: 2px;
      background: linear-gradient(to right, var(--green), transparent);
      opacity: 0; transition: opacity 0.25s;
    }
    .fc:hover { background: rgba(255,255,255,0.042); }
    .fc:hover::before { opacity: 1; }

    .fc-icon {
      width: 44px; height: 44px; margin-bottom: 20px;
      display: flex; align-items: center; justify-content: center;
      background: rgba(3,128,71,0.1);
      border: 1px solid rgba(3,128,71,0.18);
      border-radius: 10px;
    }
    .fc-icon svg { width: 22px; height: 22px; }

    .fc-num {
      position: absolute; top: 24px; right: 24px;
      font-size: 11px; font-weight: 700;
      letter-spacing: 1px; color: rgba(255,255,255,0.08);
    }
    .fc-title {
      font-size: 15px; font-weight: 600;
      color: var(--white); margin-bottom: 10px;
    }
    .fc-desc {
      font-size: 13px; font-weight: 300;
      color: var(--muted); line-height: 1.7;
    }

    /* ── HOW ── */
    .how { background: linear-gradient(180deg, var(--navy) 0%, #091825 100%); }

    .how-grid {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 80px; align-items: center;
    }

    .steps { display: flex; flex-direction: column; }

    .step {
      display: flex; gap: 20px;
      padding: 24px 0;
      border-bottom: 1px solid var(--border);
      position: relative;
    }
    .step:last-child { border-bottom: none; }

    .step-connector {
      position: absolute; left: 18px; top: 52px; bottom: -24px;
      width: 1px;
      background: linear-gradient(to bottom, rgba(3,128,71,0.4), transparent);
    }
    .step:last-child .step-connector { display: none; }

    .step-n {
      flex-shrink: 0; width: 38px; height: 38px;
      border-radius: 50%;
      border: 1px solid rgba(3,128,71,0.35);
      background: rgba(3,128,71,0.08);
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 700; color: var(--green);
      margin-top: 1px;
    }
    .step-title { font-size: 15px; font-weight: 600; margin-bottom: 6px; }
    .step-desc { font-size: 13px; font-weight: 300; color: var(--muted); line-height: 1.7; }

    /* Mockup */
    .mockup {
      border: 1px solid var(--border);
      border-radius: 14px; overflow: hidden;
      background: rgba(255,255,255,0.025);
    }
    .mock-bar {
      background: rgba(255,255,255,0.035);
      border-bottom: 1px solid var(--border);
      padding: 12px 18px;
      display: flex; align-items: center; gap: 7px;
    }
    .dot { width: 9px; height: 9px; border-radius: 50%; }
    .dr { background: #ff5f56; } .dy { background: #ffbd2e; } .dg { background: #27c93f; }
    .mock-label { font-size: 10.5px; color: var(--muted); margin-left: 6px; letter-spacing: 0.5px; }

    .mock-body { padding: 20px; display: flex; flex-direction: column; gap: 10px; }

    .mock-row { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 8px; }
    .mock-card {
      background: rgba(255,255,255,0.035);
      border: 1px solid var(--border);
      border-radius: 8px; padding: 12px 14px;
    }
    .mk-lbl { font-size: 8.5px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: var(--muted); margin-bottom: 5px; }
    .mk-val { font-size: 20px; font-weight: 700; }
    .mk-val.g { color: var(--green); }
    .mk-val.o { color: var(--gold); }

    .mock-chart {
      background: rgba(255,255,255,0.035);
      border: 1px solid var(--border);
      border-radius: 8px; padding: 14px;
      height: 96px; display: flex; align-items: flex-end; gap: 5px;
    }
    .bar {
      flex: 1; border-radius: 3px 3px 0 0;
      background: linear-gradient(to top, var(--green), rgba(3,128,71,0.25));
    }

    .mock-sent {
      background: rgba(255,255,255,0.035);
      border: 1px solid var(--border);
      border-radius: 8px; padding: 14px;
    }
    .sent-lbl { font-size: 8.5px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; color: var(--muted); margin-bottom: 10px; }
    .sent-track { display: flex; height: 5px; border-radius: 10px; overflow: hidden; gap: 2px; margin-bottom: 9px; }
    .seg { border-radius: 10px; }
    .seg-p { background: var(--green); }
    .seg-n { background: var(--gold); }
    .seg-e { background: #e05c5c; }
    .sent-row { display: flex; gap: 14px; }
    .sent-item { display: flex; align-items: center; gap: 5px; font-size: 10px; color: var(--muted); }
    .sent-dot { width: 5px; height: 5px; border-radius: 50%; }

    /* ── STATS ── */
    .stats-strip {
      padding: 60px 24px;
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      background: rgba(255,255,255,0.015);
    }
    .stats-grid {
      display: grid; grid-template-columns: repeat(4, 1fr);
      gap: 1px; background: var(--border);
      border: 1px solid var(--border); border-radius: 12px; overflow: hidden;
    }
    .stat {
      background: rgba(255,255,255,0.018);
      padding: 36px 28px; text-align: center;
      transition: background 0.2s;
    }
    .stat:hover { background: rgba(255,255,255,0.04); }
    .stat-n {
      font-size: 40px; font-weight: 800;
      letter-spacing: -1.5px; line-height: 1;
      margin-bottom: 6px;
    }
    .stat-n span { color: var(--green); }
    .stat-d { font-size: 11.5px; font-weight: 300; color: var(--muted); line-height: 1.5; }

    /* ── WHY ── */
    .why-grid {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 60px; align-items: center;
    }
    .why-cards { display: flex; flex-direction: column; gap: 12px; }
    .wc {
      display: flex; align-items: flex-start; gap: 18px;
      padding: 22px 24px;
      border: 1px solid var(--border); border-radius: 10px;
      background: rgba(255,255,255,0.025);
      transition: border-color 0.2s, background 0.2s;
    }
    .wc:hover {
      border-color: rgba(3,128,71,0.28);
      background: rgba(3,128,71,0.04);
    }
    .wc-icon {
      flex-shrink: 0; width: 38px; height: 38px;
      display: flex; align-items: center; justify-content: center;
      background: rgba(3,128,71,0.1); border: 1px solid rgba(3,128,71,0.15);
      border-radius: 8px; margin-top: 1px;
    }
    .wc-icon svg { width: 18px; height: 18px; }
    .wc-title { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
    .wc-desc { font-size: 12.5px; font-weight: 300; color: var(--muted); line-height: 1.65; }

    /* ── CTA ── */
    .cta-sec {
      text-align: center; padding: 120px 24px;
      position: relative; overflow: hidden;
    }
    .cta-sec::before {
      content: '';
      position: absolute; inset: 0;
      background: radial-gradient(ellipse 60% 70% at 50% 50%, rgba(3,128,71,0.1) 0%, transparent 65%);
      pointer-events: none;
    }
    .cta-inner { position: relative; z-index: 2; max-width: 600px; margin: 0 auto; }
    .cta-line {
      width: 48px; height: 2px;
      background: linear-gradient(to right, var(--gold), transparent);
      margin: 0 auto 28px;
    }
    .cta-title {
      font-size: clamp(30px, 4vw, 52px); font-weight: 800;
      line-height: 1.1; letter-spacing: -1px; margin-bottom: 16px;
    }
    .cta-title em { font-style: italic; color: var(--green); }
    .cta-desc { font-size: 14px; font-weight: 300; color: var(--muted); line-height: 1.75; margin-bottom: 40px; }

    /* ── FOOTER ── */
    footer {
      border-top: 1px solid var(--border);
      padding: 48px 24px 32px;
      background: rgba(0,0,0,0.2);
    }
    .foot-inner { max-width: 1100px; margin: 0 auto; }
    .foot-top {
      display: flex; justify-content: space-between; align-items: center;
      padding-bottom: 32px; border-bottom: 1px solid var(--border);
      flex-wrap: wrap; gap: 24px;
    }
    .foot-top img { height: 32px; opacity: 0.7; }
    .foot-links { display: flex; gap: 28px; list-style: none; flex-wrap: wrap; }
    .foot-links a { font-size: 12px; font-weight: 400; color: var(--muted); text-decoration: none; transition: color 0.2s; }
    .foot-links a:hover { color: var(--white); }
    .foot-bottom {
      display: flex; justify-content: space-between; align-items: center;
      padding-top: 24px; flex-wrap: wrap; gap: 12px;
    }
    .foot-copy { font-size: 11.5px; color: rgba(255,255,255,0.18); }
    .foot-powered { font-size: 11px; font-weight: 500; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,0.15); }
    .foot-powered b { color: var(--gold); font-weight: 600; opacity: 0.7; }

    /* ── ANIM ── */
    @keyframes up {
      from { opacity: 0; transform: translateY(22px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes blink {
      0%,100% { opacity: 1; } 50% { opacity: 0.4; }
    }
    .reveal { opacity: 0; transform: translateY(26px); transition: opacity 0.65s ease, transform 0.65s ease; }
    .reveal.on { opacity: 1; transform: translateY(0); }

    /* ── RESPONSIVE ── */
    @media (max-width: 960px) {
      .feat-grid { grid-template-columns: 1fr 1fr; }
      .how-grid, .why-grid { grid-template-columns: 1fr; gap: 48px; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 640px) {
      nav { padding: 0 20px; }
      .nav-right a:not(.nav-btn) { display: none; }
      section { padding: 72px 20px; }
      .feat-grid { grid-template-columns: 1fr; }
      .mock-row { grid-template-columns: 1fr; }
      .foot-top { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav id="nav">
  <div class="nav-logo">
    <img src="{{ asset('images/SMADIMENT 2025-Putih.png') }}" alt="SMADIMENT">
  </div>
  <div class="nav-right">
    <a href="#fitur">Fitur</a>
    <a href="#cara-kerja">Cara Kerja</a>
    <a href="{{ route('user.login') }}" class="nav-btn">Masuk</a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <div class="hero-badge">
      <div class="hero-badge-dot"></div>
      <span>Smadiment Analytic Platform</span>
    </div>

    <h1 class="hero-title">
      Pantau. Analisis.<br><em>Ambil Keputusan.</em>
    </h1>

    <p class="hero-sub">
      Platform analitik media sosial berbasis AI untuk memantau tren secara real-time, mengukur sentimen publik, dan menghasilkan insight strategis yang presisi.
    </p>

    <a href="{{ route('user.login') }}" class="hero-cta">
      Mulai Sekarang
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
  </div>

  <!-- Simple hero SVG illustration -->
  <div class="hero-visual">
    <svg width="680" height="220" viewBox="0 0 680 220" fill="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Card background -->
      <rect x="1" y="1" width="678" height="218" rx="13" fill="rgba(255,255,255,0.02)" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>
      <!-- Top bar -->
      <rect x="1" y="1" width="678" height="40" rx="13" fill="rgba(255,255,255,0.03)"/>
      <rect x="1" y="27" width="678" height="14" fill="rgba(255,255,255,0.03)"/>
      <!-- Dots -->
      <circle cx="22" cy="21" r="5" fill="#ff5f56"/>
      <circle cx="38" cy="21" r="5" fill="#ffbd2e"/>
      <circle cx="54" cy="21" r="5" fill="#27c93f"/>
      <!-- Title text placeholder -->
      <rect x="72" y="16" width="120" height="10" rx="3" fill="rgba(255,255,255,0.1)"/>

      <!-- Stat cards -->
      <!-- Card 1 -->
      <rect x="20" y="56" width="180" height="70" rx="8" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>
      <rect x="32" y="68" width="60" height="7" rx="3" fill="rgba(255,255,255,0.12)"/>
      <rect x="32" y="82" width="100" height="14" rx="4" fill="rgba(255,255,255,0.2)"/>
      <rect x="32" y="102" width="44" height="6" rx="3" fill="rgba(255,255,255,0.07)"/>

      <!-- Card 2 -->
      <rect x="212" y="56" width="110" height="70" rx="8" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>
      <rect x="224" y="68" width="50" height="7" rx="3" fill="rgba(255,255,255,0.12)"/>
      <rect x="224" y="82" width="70" height="14" rx="4" fill="#038047" fill-opacity="0.6"/>
      <rect x="224" y="102" width="44" height="6" rx="3" fill="rgba(255,255,255,0.07)"/>

      <!-- Card 3 -->
      <rect x="334" y="56" width="110" height="70" rx="8" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>
      <rect x="346" y="68" width="50" height="7" rx="3" fill="rgba(255,255,255,0.12)"/>
      <rect x="346" y="82" width="70" height="14" rx="4" fill="#c9a84c" fill-opacity="0.5"/>
      <rect x="346" y="102" width="44" height="6" rx="3" fill="rgba(255,255,255,0.07)"/>

      <!-- Bar chart -->
      <rect x="456" y="56" width="204" height="70" rx="8" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>
      <!-- Bars -->
      <rect x="468" y="96" width="14" height="20" rx="2" fill="#038047" fill-opacity="0.4"/>
      <rect x="487" y="88" width="14" height="28" rx="2" fill="#038047" fill-opacity="0.5"/>
      <rect x="506" y="80" width="14" height="36" rx="2" fill="#038047" fill-opacity="0.65"/>
      <rect x="525" y="72" width="14" height="44" rx="2" fill="#038047" fill-opacity="0.8"/>
      <rect x="544" y="78" width="14" height="38" rx="2" fill="#038047" fill-opacity="0.7"/>
      <rect x="563" y="68" width="14" height="48" rx="2" fill="#038047"/>
      <rect x="582" y="74" width="14" height="42" rx="2" fill="#038047" fill-opacity="0.75"/>
      <rect x="601" y="84" width="14" height="32" rx="2" fill="#038047" fill-opacity="0.55"/>
      <rect x="620" y="90" width="14" height="26" rx="2" fill="#038047" fill-opacity="0.45"/>
      <!-- X axis line -->
      <line x1="468" y1="116" x2="644" y2="116" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>

      <!-- Sentiment bar -->
      <rect x="20" y="142" width="640" height="58" rx="8" fill="rgba(255,255,255,0.025)" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>
      <rect x="32" y="153" width="60" height="6" rx="3" fill="rgba(255,255,255,0.12)"/>
      <!-- sentiment bar track -->
      <rect x="32" y="167" width="614" height="5" rx="10" fill="rgba(255,255,255,0.06)"/>
      <rect x="32" y="167" width="418" height="5" rx="10" fill="#038047" fill-opacity="0.75"/>
      <rect x="452" y="167" width="129" height="5" rx="10" fill="#c9a84c" fill-opacity="0.6"/>
      <rect x="583" y="167" width="63" height="5" rx="10" fill="#e05c5c" fill-opacity="0.6"/>
      <!-- legend dots + labels -->
      <circle cx="38" cy="184" r="4" fill="#038047" fill-opacity="0.8"/>
      <rect x="47" y="180" width="50" height="7" rx="3" fill="rgba(255,255,255,0.12)"/>
      <circle cx="120" cy="184" r="4" fill="#c9a84c" fill-opacity="0.7"/>
      <rect x="129" y="180" width="42" height="7" rx="3" fill="rgba(255,255,255,0.1)"/>
      <circle cx="194" cy="184" r="4" fill="#e05c5c" fill-opacity="0.7"/>
      <rect x="203" y="180" width="38" height="7" rx="3" fill="rgba(255,255,255,0.1)"/>
    </svg>
  </div>
</section>

<!-- FEATURES -->
<section id="fitur">
  <div class="wrap">
    <div class="feat-head">
      <div>
        <div class="sec-label reveal">
          <div class="sec-label-line"></div>
          <span>Fitur Unggulan</span>
        </div>
        <h2 class="sec-title reveal">Semua yang Anda butuhkan,<br><em>satu platform</em></h2>
      </div>
      <p class="sec-desc reveal">Dirancang untuk tim analis dan komunikator yang membutuhkan data media sosial yang akurat dan siap digunakan.</p>
    </div>

    <div class="feat-grid reveal">
      <div class="fc">
        <div class="fc-num">01</div>
        <div class="fc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"/>
            <path d="M2 12h3M19 12h3M12 2v3M12 19v3"/>
            <path d="M4.9 4.9l2.1 2.1M16.9 16.9l2.1 2.1M4.9 19.1l2.1-2.1M16.9 7.1l2.1-2.1"/>
          </svg>
        </div>
        <div class="fc-title">Monitoring Real-Time</div>
        <p class="fc-desc">Pantau percakapan di berbagai platform media sosial secara langsung — Twitter, Instagram, Facebook, YouTube, dan lainnya.</p>
      </div>

      <div class="fc">
        <div class="fc-num">02</div>
        <div class="fc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 2v20M2 12h20"/>
          </svg>
        </div>
        <div class="fc-title">Plutchik Emotion Analysis</div>
        <p class="fc-desc">AI kami menganalisis spektrum emosi manusia berdasarkan Plutchik Wheel (Joy, Trust, Fear, Surprise, Sadness, Disgust, Anger, Anticipation).</p>
      </div>

      <div class="fc">
        <div class="fc-num">03</div>
        <div class="fc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <path d="M3 9h18M9 21V9"/>
            <path d="M14 14l2 2 4-4"/>
          </svg>
        </div>
        <div class="fc-title">Visualisasi SOV & Radar</div>
        <p class="fc-desc">Visualisasi data komprehensif mulai dari Share of Voice (SOV), Emotion Radar, hingga distribusi tren harian yang interaktif.</p>
      </div>

      <div class="fc">
        <div class="fc-num">04</div>
        <div class="fc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 3h18v18H3zM3 9h18M9 21V9"/>
          </svg>
        </div>
        <div class="fc-title">Data Overview</div>
        <p class="fc-desc">Ringkasan performa project yang komprehensif, mencakup total mention, jangkauan media, hingga ringkasan statistik harian.</p>
      </div>

      <div class="fc">
        <div class="fc-num">05</div>
        <div class="fc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
          </svg>
        </div>
        <div class="fc-title">Export PDF & PNG</div>
        <p class="fc-desc">Generate laporan instan untuk setiap widget atau seluruh halaman dalam format PDF/PNG dengan toolbar export yang profesional.</p>
      </div>

      <div class="fc">
        <div class="fc-num">06</div>
        <div class="fc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div class="fc-title">Author Analytic</div>
        <p class="fc-desc">Identifikasi influencer utama dan user paling aktif yang membicarakan topik Anda di berbagai platform media sosial.</p>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how" id="cara-kerja">
  <div class="wrap">
    <div class="how-grid">
      <div>
        <div class="sec-label reveal">
          <div class="sec-label-line"></div>
          <span>Cara Kerja</span>
        </div>
        <h2 class="sec-title reveal">Dari data mentah<br>ke <em>insight strategis</em></h2>
        <p class="sec-desc reveal" style="margin-bottom: 40px;">Proses analitik berjalan otomatis, sehingga Anda fokus pada keputusan bukan pengumpulan data.</p>

        <div class="steps">
          <div class="step reveal">
            <div class="step-connector"></div>
            <div class="step-n">1</div>
            <div>
              <div class="step-title">Project & Boolean Query</div>
              <p class="step-desc">Tentukan parameter monitoring melalui Boolean Query yang presisi untuk memfilter topik atau isu tertentu.</p>
            </div>
          </div>
          <div class="step reveal">
            <div class="step-connector"></div>
            <div class="step-n">2</div>
            <div>
              <div class="step-title">SMADIMENT Multi-Crawler</div>
              <p class="step-desc">Crawler kami mengumpulkan data dari Twitter, FB, IG, YT, TikTok, dan Media Online secara sinkron dan otomatis.</p>
            </div>
          </div>
          <div class="step reveal">
            <div class="step-connector"></div>
            <div class="step-n">3</div>
            <div>
              <div class="step-title">NLP & Emotion Engine</div>
              <p class="step-desc">Setiap konten diproses melalui Natural Language Processing (NLP) untuk klasifikasi sentimen dan 8 kategori emosi.</p>
            </div>
          </div>
          <div class="step reveal">
            <div class="step-n">4</div>
            <div>
              <div class="step-title">Analytic Output & Export</div>
              <p class="step-desc">Dapatkan hasil analitik di dashboard interaktif dan unduh dalam format laporan yang siap dipresentasikan.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="reveal">
        <div class="mockup">
          <div class="mock-bar">
            <div class="dot dr"></div>
            <div class="dot dy"></div>
            <div class="dot dg"></div>
            <span class="mock-label">SMADIMENT Dashboard</span>
          </div>
          <div class="mock-body">
            <div class="mock-row">
              <div class="mock-card">
                <div class="mk-lbl">Total Mention</div>
                <div class="mk-val">24,891</div>
              </div>
              <div class="mock-card">
                <div class="mk-lbl">Positif</div>
                <div class="mk-val g">68%</div>
              </div>
              <div class="mock-card">
                <div class="mk-lbl">Reach</div>
                <div class="mk-val o">2.4M</div>
              </div>
            </div>
            <div class="mock-chart">
              <div class="bar" style="height:38%"></div>
              <div class="bar" style="height:60%"></div>
              <div class="bar" style="height:48%"></div>
              <div class="bar" style="height:78%"></div>
              <div class="bar" style="height:58%"></div>
              <div class="bar" style="height:92%"></div>
              <div class="bar" style="height:68%"></div>
              <div class="bar" style="height:52%"></div>
              <div class="bar" style="height:74%"></div>
              <div class="bar" style="height:84%"></div>
              <div class="bar" style="height:60%"></div>
              <div class="bar" style="height:96%"></div>
            </div>
            <div class="mock-sent">
              <div class="sent-lbl">Distribusi Sentimen</div>
              <div class="sent-track">
                <div class="seg seg-p" style="flex:68"></div>
                <div class="seg seg-n" style="flex:21"></div>
                <div class="seg seg-e" style="flex:11"></div>
              </div>
              <div class="sent-row">
                <div class="sent-item">
                  <div class="sent-dot" style="background:var(--green)"></div>
                  Positif 68%
                </div>
                <div class="sent-item">
                  <div class="sent-dot" style="background:var(--gold)"></div>
                  Netral 21%
                </div>
                <div class="sent-item">
                  <div class="sent-dot" style="background:#e05c5c"></div>
                  Negatif 11%
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- WHY -->
<section id="mengapa">
  <div class="wrap">
    <div class="why-grid">
      <div>
        <div class="sec-label reveal">
          <div class="sec-label-line"></div>
          <span>Mengapa SMADIMENT</span>
        </div>
        <h2 class="sec-title reveal">Platform yang<br><em>dirancang untuk hasil</em></h2>
        <p class="sec-desc reveal">Dibangun dengan teknologi terkini dan pemahaman mendalam tentang kebutuhan tim komunikasi di Indonesia.</p>
      </div>

      <div class="why-cards">
        <div class="wc reveal">
          <div class="wc-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
            </svg>
          </div>
          <div>
            <div class="wc-title">Optimasi Bahasa Indonesia</div>
            <p class="wc-desc">Model NLP dilatih khusus pada konten bahasa Indonesia, termasuk bahasa gaul, slang, dan dialek regional.</p>
          </div>
        </div>
        <div class="wc reveal">
          <div class="wc-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </div>
          <div>
            <div class="wc-title">Keamanan Data Terjamin</div>
            <p class="wc-desc">Enkripsi end-to-end dan infrastruktur aman memastikan data Anda terlindungi sepenuhnya.</p>
          </div>
        </div>
        <div class="wc reveal">
          <div class="wc-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
            </svg>
          </div>
          <div>
            <div class="wc-title">Skalabel & Andal</div>
            <p class="wc-desc">Arsitektur cloud scalable menangani lonjakan data saat momen viral tanpa gangguan performa.</p>
          </div>
        </div>
        <div class="wc reveal">
          <div class="wc-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
              <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
          </div>
          <div>
            <div class="wc-title">Insight yang Actionable</div>
            <p class="wc-desc">Bukan sekadar data — platform kami menyajikan rekomendasi yang bisa langsung diimplementasikan.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-sec">
  <div class="cta-inner">
    <div class="cta-line reveal"></div>
    <h2 class="cta-title reveal">Siap memulai<br><em>analitik yang lebih cerdas?</em></h2>
    <p class="cta-desc reveal">Mulai pantau media sosial Anda hari ini dan dapatkan insight untuk pengambilan keputusan yang lebih tepat.</p>
    <a href="{{ route('user.login') }}" class="hero-cta reveal" style="display:inline-flex;">
      Masuk ke Platform
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="foot-inner">
    <div class="foot-top">
      <img src="{{ asset('images/SMADIMENT 2025- warna.png') }}" alt="SMADIMENT">
      <ul class="foot-links">
        <li><a href="#fitur">Fitur</a></li>
        <li><a href="#cara-kerja">Cara Kerja</a></li>
        <li><a href="#mengapa">Mengapa SMADIMENT</a></li>
        <li><a href="{{ route('user.login') }}">Masuk</a></li>
      </ul>
    </div>
    <div class="foot-bottom">
      <p class="foot-copy">&copy; {{ date('Y') }} SMADIMENT. Seluruh hak cipta dilindungi.</p>
      <p class="foot-powered">Powered by <b>Alcomedia.id</b></p>
    </div>
  </div>
</footer>

<script>
  // Navbar scroll
  const nav = document.getElementById('nav');
  window.addEventListener('scroll', () => nav.classList.toggle('scrolled', scrollY > 30));

  // Scroll reveal
  const els = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const siblings = [...e.target.parentElement.querySelectorAll('.reveal:not(.on)')];
      const i = siblings.indexOf(e.target);
      setTimeout(() => e.target.classList.add('on'), i * 70);
      io.unobserve(e.target);
    });
  }, { threshold: 0.1 });
  els.forEach(el => io.observe(el));
</script>
</body>
</html>