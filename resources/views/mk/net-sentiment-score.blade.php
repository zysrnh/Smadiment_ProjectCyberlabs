@extends('mk.layouts.app')

@section('title', 'Net Sentiment Score - SMADIMENT')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --primary-green:        #038047;
    --primary-green-dark:   #026738;
    --primary-green-light:  rgba(3,128,71,0.08);
    --primary-green-border: rgba(3,128,71,0.2);

    --sent-pos:    #2FC6F6;
    --sent-neg:    #ef4444;
    --sent-neu:    #94a3b8;
    --sent-pos-bg: rgba(47,198,246,.1);
    --sent-neg-bg: rgba(239,68,68,.1);
    --sent-neu-bg: rgba(148,163,184,.1);

    --text-primary:   #1a202c;
    --text-secondary: #64748b;
    --text-muted:     #94a3b8;

    --bg-white:    #ffffff;
    --bg-body:     #f0f4f8;
    --bg-gray-50:  #f8fafc;
    --bg-gray-100: #f1f5f9;

    --border-gray:  #e2e8f0;
    --border-light: #f1f5f9;

    --shadow-sm: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1);
    --shadow-xl: 0 20px 40px -8px rgba(0,0,0,.18);

    --radius:    16px;
    --radius-sm: 12px;
    --radius-xs: 8px;
    --transition: all .2s cubic-bezier(.4,0,.2,1);
    --font: 'Poppins', -apple-system, sans-serif;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: var(--font); background: var(--bg-body); color: var(--text-primary); }

  /* ── PAGE ── */
  .nss-page { padding: 24px; max-width: 1600px; margin: 0 auto; }

  /* ── PAGE HEADER ── */
  .page-header {
    display: flex; align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
  }
  .page-header-left h1 {
    font-size: 28px; font-weight: 700;
    color: var(--text-primary); letter-spacing: -.4px;
  }
  .page-header-left p {
    font-size: 14px; color: var(--text-secondary);
    font-weight: 500; margin-top: 4px;
  }

  /* ── MEDIA DROPDOWN ── */
  .nss-media-dropdown { position: relative; }

  .nss-media-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-family: var(--font); font-size: 13px; font-weight: 600;
    cursor: pointer; transition: var(--transition);
    box-shadow: 0 4px 14px rgba(3,128,71,.25);
  }
  .nss-media-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.35); }
  .nss-media-btn svg {
    width: 14px; height: 14px; stroke: currentColor; fill: none;
    stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
    transition: transform .2s;
  }
  .nss-media-btn.open svg { transform: rotate(180deg); }

  .nss-media-menu {
    display: none; position: absolute;
    top: calc(100% + 8px); right: 0;
    background: var(--bg-white); border: 1px solid var(--border-gray);
    border-radius: var(--radius); box-shadow: var(--shadow-xl);
    min-width: 210px; z-index: 200; overflow: hidden; padding: 6px;
  }
  .nss-media-menu.open { display: block; animation: dropIn .18s ease; }

  @keyframes dropIn {
    from { opacity: 0; transform: translateY(-6px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }

  .nss-media-menu-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 12px; font-size: 13px; font-weight: 500;
    color: var(--text-secondary); cursor: pointer;
    border-radius: var(--radius-xs); transition: background .12s;
  }
  .nss-media-menu-item:hover  { background: var(--bg-gray-50); }
  .nss-media-menu-item.active {
    background: var(--primary-green-light);
    color: var(--primary-green); font-weight: 700;
  }
  .nss-menu-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

  /* ── SECTION HEADER ── */
  .snt-section-header {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 16px; margin-top: 4px;
  }
  .snt-section-icon {
    width: 36px; height: 36px; border-radius: var(--radius-sm);
    background: var(--primary-green-light);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .snt-section-icon svg {
    width: 18px; height: 18px; stroke: var(--primary-green);
    fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
  }
  .snt-section-title {
    font-size: 13px; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .8px;
  }
  .snt-section-line { flex: 1; height: 1.5px; background: var(--border-gray); border-radius: 1px; }

  /* ── STAT CARDS ── */
  .nss-stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }

  .snt-stat-card {
    background: var(--bg-white); border: 1px solid var(--border-gray);
    border-radius: var(--radius); padding: 20px 22px;
    box-shadow: var(--shadow-sm); transition: var(--transition);
    position: relative; overflow: hidden; cursor: pointer;
  }
  .snt-stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--bar-color, var(--primary-green));
    opacity: 0; transition: opacity .25s;
  }
  .snt-stat-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
    border-color: var(--primary-green-border);
  }
  .snt-stat-card:hover::before { opacity: 1; }
  .snt-stat-card--neg { --bar-color: linear-gradient(90deg,#ef4444,#dc2626); }
  .snt-stat-card--pos { --bar-color: linear-gradient(90deg,#2FC6F6,#0ea5e9); }
  .snt-stat-card--neu { --bar-color: linear-gradient(90deg,#94a3b8,#64748b); }

  .snt-stat-label {
    font-size: 11px; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px;
    display: flex; align-items: center; gap: 6px;
  }
  .snt-stat-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
  .snt-stat-value {
    font-size: 32px; font-weight: 700; color: var(--text-primary);
    letter-spacing: -1px; line-height: 1; min-height: 40px;
    display: flex; align-items: center;
  }
  .snt-stat-sub  { font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 7px; }
  .snt-stat-pct  { font-size: 13px; font-weight: 700; margin-top: 5px; }

  /* ── CARD ── */
  .snt-card {
    background: var(--bg-white); border: 1px solid var(--border-gray);
    border-radius: var(--radius); overflow: hidden;
    display: flex; flex-direction: column;
    box-shadow: var(--shadow-sm); transition: var(--transition);
    position: relative;
  }
  .snt-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--primary-green), var(--primary-green-dark));
    opacity: 0; transition: opacity .3s;
  }
  .snt-card:hover { box-shadow: var(--shadow-lg); border-color: var(--primary-green-border); }
  .snt-card:hover::before { opacity: 1; }

  .snt-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--border-gray); flex-shrink: 0;
  }
  .snt-card-head-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
  .snt-head-icon {
    width: 40px; height: 40px; border-radius: var(--radius-sm);
    background: var(--primary-green-light);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .snt-head-icon svg {
    width: 20px; height: 20px; fill: none; stroke: var(--primary-green);
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
  }
  .snt-card-title    { font-size: 15px; font-weight: 700; color: var(--text-primary); }
  .snt-card-subtitle { font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 2px; }

  .snt-badge {
    display: inline-flex; align-items: center;
    padding: 4px 12px; border-radius: 20px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    background: var(--bg-gray-100); color: var(--text-secondary);
    white-space: nowrap; flex-shrink: 0;
  }

  .snt-card-body { padding: 20px; flex: 1; }

  /* ── MAIN GRID ── */
  .nss-main-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 20px;
    align-items: start;
  }

  /* ── SIDEBAR ── */
  .nss-sidebar { display: flex; flex-direction: column; gap: 20px; }

  /* ═══════════════════════════════════════════════════════════
     GAUGE — Mathematically precise
     ViewBox: 500 × 310
     Arc center: (250, 260)   ← pivot point of needle
     Arc radius: 190
     Arc left:   (60,  260)   = (250−190, 260)
     Arc right:  (440, 260)   = (250+190, 260)
     Arc top:    (250, 70)    = (250, 260−190)
     Stroke-width: 40
     Label radius: 190 + 32 = 222  (outside arc)
  ═══════════════════════════════════════════════════════════ */
  .gauge-wrap {
    /* Wrapper inside the card body — adds symmetric horizontal padding */
    padding: 28px 32px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .gauge-outer {
    position: relative;
    width: 100%;
    max-width: 480px;
    /* aspect-ratio mirrors viewBox 500/310 */
    aspect-ratio: 500 / 310;
  }

  #gaugeSVG {
    width: 100%;
    height: 100%;
    display: block;
    overflow: visible;
  }

  /*
    Arc center in SVG coords: (250, 260)
    As % of viewBox: left = 250/500 = 50%, top = 260/310 = 83.87%

    Score overlay: sits at the optical center of the semicircle bowl,
    which is roughly the midpoint between the arc-top and center-pivot:
      optical_y = (70 + 260) / 2 = 165  →  165/310 = 53.2% of height
    We position it there and center horizontally at 50%.
  */
  .gauge-score-overlay {
    position: absolute;
    left: 50%;
    top: 53%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    pointer-events: none;
    z-index: 2;
    white-space: nowrap;
  }

  .gauge-score-num {
    font-family: 'Poppins', -apple-system, sans-serif;
    font-size: clamp(30px, 6vw, 54px);
    font-weight: 800;
    letter-spacing: -2px;
    line-height: 1;
    color: #1a202c;
  }

  .gauge-score-lbl {
    font-family: 'Poppins', -apple-system, sans-serif;
    font-size: clamp(8px, 1.3vw, 11px);
    font-weight: 700;
    letter-spacing: 2.5px;
    color: #94a3b8;
    text-transform: uppercase;
  }

  /* ── LEGEND ── */
  .gauge-legend-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 22px;
    flex-wrap: wrap;
    padding: 16px 20px 20px;
    border-top: 1px solid var(--border-light);
    background: var(--bg-gray-50);
    margin-top: auto;
  }

  .snt-legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary); }
  .snt-legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

  /* ── DISTRIBUTION CHART ── */
  .nss-dist-wrap { height: 210px; }
  #chDist { width: 100%; height: 100%; }

  /* ── SCORE BREAKDOWN ── */
  .nss-formula-card {
    background: var(--bg-white); border: 1px solid var(--border-gray);
    border-radius: var(--radius); overflow: hidden;
    box-shadow: var(--shadow-sm); transition: var(--transition);
  }
  .nss-formula-card:hover { box-shadow: var(--shadow-lg); border-color: var(--primary-green-border); }

  .nss-formula-head {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px; border-bottom: 1px solid var(--border-gray);
    background: var(--bg-gray-50);
  }
  .nss-formula-head-icon {
    width: 34px; height: 34px; border-radius: var(--radius-xs);
    background: var(--primary-green-light);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .nss-formula-head-icon svg {
    width: 17px; height: 17px; fill: none; stroke: var(--primary-green);
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
  }
  .nss-formula-head-title { font-size: 13px; font-weight: 700; color: var(--text-primary); }
  .nss-formula-head-sub   { font-size: 10px; color: var(--text-muted); font-weight: 500; }

  .nss-formula-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 11px; }
  .nss-formula-row  { display: flex; align-items: center; justify-content: space-between; font-size: 13px; }
  .nss-formula-key  { display: flex; align-items: center; gap: 8px; color: var(--text-secondary); font-weight: 500; }
  .nss-formula-dot  { width: 8px; height: 8px; border-radius: 50%; }
  .nss-formula-val  { font-size: 13px; font-weight: 700; color: var(--text-primary); }
  .nss-formula-divider { height: 1px; background: var(--border-gray); }
  .nss-formula-total     { display: flex; align-items: center; justify-content: space-between; font-size: 13px; }
  .nss-formula-total-key { font-weight: 700; color: var(--text-primary); }
  .nss-formula-total-val { font-size: 15px; font-weight: 700; color: var(--text-primary); }

  .nss-formula-nss-row {
    background: var(--primary-green-light);
    border: 1px solid var(--primary-green-border);
    border-radius: var(--radius-xs);
    padding: 12px 14px;
    display: flex; align-items: center; justify-content: space-between; margin-top: 2px;
  }
  .nss-formula-nss-label {
    font-size: 12px; font-weight: 700; color: var(--primary-green);
    text-transform: uppercase; letter-spacing: .5px;
  }
  .nss-formula-nss-val { font-size: 26px; font-weight: 800; letter-spacing: -1px; }

  .nss-formula-eq {
    background: var(--bg-gray-50); border-top: 1px solid var(--border-gray);
    padding: 10px 18px; font-size: 11px; font-weight: 600;
    color: var(--text-muted); text-align: center; letter-spacing: .2px;
    font-family: 'Courier New', monospace;
  }

  /* ── SKELETON ── */
  .snt-skel {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
    border-radius: var(--radius-xs);
  }
  @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

  /* ── EMPTY ── */
  .snt-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    height: 260px; gap: 10px;
  }
  .snt-empty svg { width: 38px; height: 38px; stroke: var(--border-gray); fill: none; stroke-width: 1.5; }
  .snt-empty-text { font-size: 13px; font-weight: 600; color: var(--text-secondary); }

  /* ── MENTION POPUP ── */
  @keyframes sntPopIn {
    from { opacity:0; transform:translateY(14px) scale(.94); }
    to   { opacity:1; transform:translateY(0) scale(1); }
  }

  #nssPopup {
    position:fixed; z-index:99999;
    background:var(--bg-white); border:1px solid var(--border-gray);
    border-radius:var(--radius); box-shadow:var(--shadow-xl);
    width:480px; height:600px;
    display:none; flex-direction:column;
    overflow:hidden; font-family:var(--font);
    animation:sntPopIn .22s cubic-bezier(.34,1.3,.64,1);
  }
  #nssPopup.visible { display:flex; }

  .nss-pop-header {
    display:flex; align-items:center; gap:8px;
    padding:12px 16px;
    background:var(--bg-gray-50); border-bottom:1px solid var(--border-gray);
    cursor:grab; flex-shrink:0;
  }
  .nss-pop-header:active { cursor:grabbing; }
  .nss-pop-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
  .nss-pop-title { font-size:13px; font-weight:700; color:var(--text-primary); flex:1; }
  .nss-pop-count {
    background:var(--primary-green); color:#fff;
    border-radius:10px; padding:1px 9px;
    font-size:11px; font-weight:800; flex-shrink:0;
  }
  .nss-pop-close {
    width:28px; height:28px; border-radius:var(--radius-xs);
    border:none; background:transparent;
    cursor:pointer; font-size:20px; line-height:1;
    color:var(--text-secondary); transition:var(--transition); flex-shrink:0;
  }
  .nss-pop-close:hover { background:#fee2e2; color:#991b1b; }

  .nss-pop-list { overflow-y:auto; flex:1; padding:4px 0; min-height:0; }
  .nss-pop-list::-webkit-scrollbar { width:5px; }
  .nss-pop-list::-webkit-scrollbar-thumb { background:var(--border-gray); border-radius:4px; }

  .nss-pop-item {
    display:flex; gap:10px; padding:10px 14px;
    border-bottom:1px solid var(--border-light);
    transition:background .1s; cursor:pointer; align-items:flex-start;
  }
  .nss-pop-item:last-child { border-bottom:none; }
  .nss-pop-item:hover { background:#f0fdf4; }

  .nss-pop-avatar {
    width:38px; height:38px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark));
    color:#fff; font-weight:700; font-size:13px;
    display:flex; align-items:center; justify-content:center;
    border:1.5px solid var(--border-gray); overflow:hidden;
  }

  .nss-pop-body { flex:1; min-width:0; }
  .nss-pop-author { font-size:12px; font-weight:700; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .nss-pop-text {
    font-size:12px; color:var(--text-secondary); line-height:1.5;
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;
    overflow:hidden; margin-bottom:5px;
  }
  .nss-pop-footer { display:flex; align-items:center; gap:6px; font-size:10px; color:var(--text-muted); }

  .nss-pop-badge {
    padding:1px 7px; border-radius:10px;
    font-size:9px; font-weight:800; text-transform:uppercase;
  }
  .nss-pop-badge--pos { background:#dbeafe; color:#1d4ed8; }
  .nss-pop-badge--neg { background:#fee2e2; color:#991b1b; }
  .nss-pop-badge--neu { background:var(--bg-gray-100); color:var(--text-secondary); }

  .nss-pop-loading {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    height:100%; gap:14px; color:var(--text-secondary); font-size:13px; font-weight:600;
  }
  .nss-pop-spinner {
    width:32px; height:32px;
    border:3px solid var(--border-gray);
    border-top-color:var(--primary-green);
    border-radius:50%;
    animation:nssSpin .7s linear infinite;
  }
  @keyframes nssSpin { to { transform:rotate(360deg); } }

  /* ── RESPONSIVE ── */
  @media (max-width: 1100px) { .nss-main-grid { grid-template-columns: 1fr; } }
  @media (max-width: 768px)  {
    .nss-stat-grid { grid-template-columns: 1fr; }
    .nss-page { padding: 16px; }
  }
</style>
@endsection

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
@endphp

<div class="nss-page">

  {{-- ══ PAGE HEADER ══ --}}
  <div class="page-header">
    <div class="page-header-left">
      <h1>Net Sentiment Score</h1>
      <p>Analisis skor sentimen bersih dari semua media yang dipantau</p>
    </div>

    <div class="nss-media-dropdown" id="mediaDdWrap">
      <button class="nss-media-btn" id="mediaBtnEl" onclick="NSSPage.toggleMenu()">
        <span id="mediaBtnLabel">All Media</span>
        <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div class="nss-media-menu" id="mediaMenu">
        <div class="nss-media-menu-item active" data-m="all"       onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#038047"></span>All Media</div>
        <div class="nss-media-menu-item"         data-m="doc"       onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#0284c7"></span>Mass Media (News)</div>
        <div class="nss-media-menu-item"         data-m="twitter"   onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#1d9bf0"></span>X / Twitter</div>
        <div class="nss-media-menu-item"         data-m="facebook"  onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#1877f2"></span>Facebook</div>
        <div class="nss-media-menu-item"         data-m="instagram" onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#e1306c"></span>Instagram</div>
        <div class="nss-media-menu-item"         data-m="youtube"   onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#ff0000"></span>YouTube</div>
        <div class="nss-media-menu-item"         data-m="tiktok"    onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#111827"></span>TikTok</div>
      </div>
    </div>
  </div>

  {{-- ══ SECTION HEADER ══ --}}
  <div class="snt-section-header">
    <div class="snt-section-icon">
      <svg viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/></svg>
    </div>
    <span class="snt-section-title">Sentiment Overview</span>
    <div class="snt-section-line"></div>
  </div>

  {{-- ══ STAT CARDS ══ --}}
  <div class="nss-stat-grid">
    <div class="snt-stat-card snt-stat-card--pos" onclick="NSSPopup.openSentiment('pos', event)">
      <div class="snt-stat-label"><span class="snt-stat-dot" style="background:#2FC6F6;"></span>Positive</div>
      <div class="snt-stat-value" id="statPos">
        <div class="snt-skel" style="height:36px;width:110px;border-radius:6px;"></div>
      </div>
      <div class="snt-stat-sub">Total mention positif</div>
      <div class="snt-stat-pct" style="color:#2FC6F6;" id="pctPos">—</div>
    </div>
    <div class="snt-stat-card snt-stat-card--neu" onclick="NSSPopup.openSentiment('neu', event)">
      <div class="snt-stat-label"><span class="snt-stat-dot" style="background:#94a3b8;"></span>Neutral</div>
      <div class="snt-stat-value" id="statNeu">
        <div class="snt-skel" style="height:36px;width:110px;border-radius:6px;"></div>
      </div>
      <div class="snt-stat-sub">Total mention netral</div>
      <div class="snt-stat-pct" style="color:#94a3b8;" id="pctNeu">—</div>
    </div>
    <div class="snt-stat-card snt-stat-card--neg" onclick="NSSPopup.openSentiment('neg', event)">
      <div class="snt-stat-label"><span class="snt-stat-dot" style="background:#ef4444;"></span>Negative</div>
      <div class="snt-stat-value" id="statNeg">
        <div class="snt-skel" style="height:36px;width:110px;border-radius:6px;"></div>
      </div>
      <div class="snt-stat-sub">Total mention negatif</div>
      <div class="snt-stat-pct" style="color:#ef4444;" id="pctNeg">—</div>
    </div>
  </div>

  {{-- ══ MAIN GRID ══ --}}
  <div class="nss-main-grid">

    {{-- ── GAUGE CARD ── --}}
    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon">
            <svg viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10"/><path d="M12 6v6l4 2"/></svg>
          </span>
          <div>
            <div class="snt-card-title">Net Sentiment Score</div>
            <div class="snt-card-subtitle">NSS = (Positive − Negative) / (Positive + Negative) × 100</div>
          </div>
        </div>
        <span class="snt-badge" id="nssBadgeMain">Loading…</span>
      </div>

      {{--
        GAUGE GEOMETRY (all values derived mathematically):
          ViewBox    : 500 × 310
          Center     : cx=250, cy=260   ← needle pivot & arc center
          Radius     : r=190
          Arc left   : (60,  260)  = (250−190, 260)
          Arc right  : (440, 260)  = (250+190, 260)
          Arc top    : (250, 70)   = (250, 260−190)
          Stroke-w   : 40
          Half-neg   : M 60,260  → 250,70   (left to top)
          Half-pos   : M 250,70  → 440,260  (top to right)

          Tick positions (on arc, perpendicular):
            −100% angle=180°: point=(60,260),  normal dir=(0,±1)  → vertical tick
            −50%  angle=135°: point=(250−190·cos45, 260−190·sin45)
                                    =(250−134.35, 260−134.35)=(115.65,125.65)
                              normal=outward=(−cos45,−sin45)=(−0.7071,−0.7071)
            0%    angle=90° : point=(250,70),   normal dir=(±1,0)  → horizontal tick
            +50%  angle=45° : point=(250+134.35, 260−134.35)=(384.35,125.65)
                              normal=(+cos45,−sin45)=(0.7071,−0.7071)
            +100% angle=0°  : point=(440,260),  normal dir=(0,±1)  → vertical tick

          Label positions (radius+28 = 218 from center):
            −100%: (250−218, 260)   = (32, 260)
            −50%:  (250−218·cos45, 260−218·sin45) = (250−154.2, 260−154.2) = (95.8, 105.8)
             0%:   (250, 260−218)   = (250, 42)
            +50%:  (250+154.2, 260−154.2) = (404.2, 105.8)
            +100%: (250+218, 260)   = (468, 260)
      --}}
      <div class="gauge-wrap">
        <div class="gauge-outer">
          <svg id="gaugeSVG" viewBox="0 0 500 310" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <filter id="ndlShadow" x="-60%" y="-60%" width="220%" height="220%">
                <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="rgba(0,0,0,0.22)"/>
              </filter>
            </defs>

            <!-- ── Track background (full semicircle) ── -->
            <path d="M 60,260 A 190,190 0 0,1 440,260"
                  fill="none" stroke="#e2e8f0" stroke-width="40" stroke-linecap="butt"/>

            <!-- ── Negative half: left → top (red) ── -->
            <path d="M 60,260 A 190,190 0 0,1 250,70"
                  fill="none" stroke="#ef4444" stroke-width="40" stroke-linecap="butt"/>

            <!-- ── Positive half: top → right (cyan) ── -->
            <path d="M 250,70 A 190,190 0 0,1 440,260"
                  fill="none" stroke="#2FC6F6" stroke-width="40" stroke-linecap="butt"/>

            <!-- ── Tick marks (white, 12px length, centered on arc, perpendicular) ──
                 Each tick: center point P, direction = outward normal unit vector n̂
                 Tick endpoints: P ± 6·n̂
            -->
            <!-- −100%: P=(60,260), n̂=(−1,0) → tick vertical (perpendicular=y-axis) -->
            <line x1="60" y1="254" x2="60" y2="266"
                  stroke="#fff" stroke-width="3" stroke-linecap="round"/>

            <!-- −50%: P=(115.65,125.65), n̂=(−0.7071,−0.7071), 6×n̂=(−4.24,−4.24)
                 endpoints: (115.65+4.24, 125.65+4.24)=(119.89,129.89)
                             (115.65−4.24, 125.65−4.24)=(111.41,121.41) -->
            <line x1="111.4" y1="121.4" x2="119.9" y2="129.9"
                  stroke="#fff" stroke-width="3" stroke-linecap="round"/>

            <!-- 0%: P=(250,70), n̂=(0,−1) → tick horizontal (perpendicular=x-axis) -->
            <line x1="244" y1="70" x2="256" y2="70"
                  stroke="#fff" stroke-width="3.5" stroke-linecap="round"/>

            <!-- +50%: P=(384.35,125.65), n̂=(0.7071,−0.7071), 6×n̂=(4.24,−4.24)
                 endpoints: (384.35−4.24, 125.65+4.24)=(380.11,129.89)
                             (384.35+4.24, 125.65−4.24)=(388.59,121.41) -->
            <line x1="380.1" y1="129.9" x2="388.6" y2="121.4"
                  stroke="#fff" stroke-width="3" stroke-linecap="round"/>

            <!-- +100%: P=(440,260), n̂=(1,0) → tick vertical -->
            <line x1="440" y1="254" x2="440" y2="266"
                  stroke="#fff" stroke-width="3" stroke-linecap="round"/>

            <!-- ── Labels (outside arc, radius=218 from center 250,260) ── -->
            <text x="32"   y="265"
                  text-anchor="middle"
                  font-family="'Poppins',sans-serif" font-size="11" font-weight="600" fill="#64748b">−100%</text>
            <text x="88"   y="100"
                  text-anchor="middle"
                  font-family="'Poppins',sans-serif" font-size="11" font-weight="600" fill="#64748b">−50%</text>
            <text x="250"  y="36"
                  text-anchor="middle"
                  font-family="'Poppins',sans-serif" font-size="12" font-weight="700" fill="#374151">0%</text>
            <text x="412"  y="100"
                  text-anchor="middle"
                  font-family="'Poppins',sans-serif" font-size="11" font-weight="600" fill="#64748b">50%</text>
            <text x="468"  y="265"
                  text-anchor="middle"
                  font-family="'Poppins',sans-serif" font-size="11" font-weight="600" fill="#64748b">100%</text>

            <!--
              ── Needle ──
              Pivot: (250, 260)  ← exact arc center
              Length: 168px (tip sits at r=168, safely inside arc band r=190, stroke±20)
              At CSS rotation 0° the needle points straight UP (−90° in SVG terms).
              We use transform="rotate(DEG, 250, 260)" from JS.
              Needle body: thin tapered polygon from pivot upward.
              Base hub: filled circle r=14 (dark), inner r=5.5 (white dot).
            -->
            <g id="needleGroup" transform="rotate(-90, 250, 260)" filter="url(#ndlShadow)">
              <!-- Needle blade: tapered from base to tip -->
              <polygon points="250,260 247,255 250,92 253,255" fill="#1a202c"/>
              <!-- Base hub outer -->
              <circle cx="250" cy="260" r="14" fill="#1a202c"/>
              <!-- Base hub inner highlight -->
              <circle cx="250" cy="260" r="5.5" fill="#ffffff"/>
            </g>
          </svg>

          <!-- Score overlay: optically centered in the semicircle bowl -->
          <div class="gauge-score-overlay">
            <div id="scoreValue" class="gauge-score-num">—</div>
            <div id="scoreLabel" class="gauge-score-lbl">NET SENTIMENT</div>
          </div>
        </div>
      </div>

      <!-- Legend row below gauge with generous breathing room -->
      <div class="gauge-legend-row">
        <div class="snt-legend-item">
          <span class="snt-legend-dot" style="background:#2FC6F6;"></span>
          <span id="legPos">Positive</span>
        </div>
        <div class="snt-legend-item">
          <span class="snt-legend-dot" style="background:#94a3b8;"></span>
          <span id="legNeu">Neutral</span>
        </div>
        <div class="snt-legend-item">
          <span class="snt-legend-dot" style="background:#ef4444;"></span>
          <span id="legNeg">Negative</span>
        </div>
      </div>
    </div>

    {{-- ── SIDEBAR ── --}}
    <div class="nss-sidebar">

      {{-- Distribution chart --}}
      <div class="snt-card">
        <div class="snt-card-head">
          <div class="snt-card-head-left">
            <span class="snt-head-icon">
              <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </span>
            <div>
              <div class="snt-card-title">Distribution</div>
              <div class="snt-card-subtitle">Persentase per sentimen</div>
            </div>
          </div>
          <span class="snt-badge">3 Kategori</span>
        </div>
        <div class="snt-card-body">
          <div class="nss-dist-wrap"><div id="chDist"></div></div>
        </div>
      </div>

      {{-- Score breakdown --}}
      <div class="nss-formula-card">
        <div class="nss-formula-head">
          <div class="nss-formula-head-icon">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </div>
          <div>
            <div class="nss-formula-head-title">Score Breakdown</div>
            <div class="nss-formula-head-sub">Kalkulasi NSS real-time</div>
          </div>
        </div>
        <div class="nss-formula-body">
          <div class="nss-formula-row">
            <span class="nss-formula-key"><span class="nss-formula-dot" style="background:#2FC6F6;"></span>Positive</span>
            <span class="nss-formula-val" id="brkPos">—</span>
          </div>
          <div class="nss-formula-row">
            <span class="nss-formula-key"><span class="nss-formula-dot" style="background:#94a3b8;"></span>Neutral</span>
            <span class="nss-formula-val" id="brkNeu">—</span>
          </div>
          <div class="nss-formula-row">
            <span class="nss-formula-key"><span class="nss-formula-dot" style="background:#ef4444;"></span>Negative</span>
            <span class="nss-formula-val" id="brkNeg">—</span>
          </div>
          <div class="nss-formula-divider"></div>
          <div class="nss-formula-total">
            <span class="nss-formula-total-key">Total</span>
            <span class="nss-formula-total-val" id="brkTot">—</span>
          </div>
          <div class="nss-formula-nss-row">
            <span class="nss-formula-nss-label">NSS Score</span>
            <span class="nss-formula-nss-val" id="brkNSS" style="color:#94a3b8;">—</span>
          </div>
        </div>
        <div class="nss-formula-eq">NSS = (Positive − Negative) / (Positive + Negative) × 100</div>
      </div>

    </div>
  </div>

  <input type="hidden" id="nssPID" value="{{ $projectId }}">
  <input type="hidden" id="nssSD"  value="{{ $startDate }}">
  <input type="hidden" id="nssED"  value="{{ $endDate }}">

</div>

{{-- ══ MENTION POPUP ══ --}}
<div id="nssPopup">
  <div class="nss-pop-header" id="nssPopHeader">
    <div class="nss-pop-dot" id="nssPopDot"></div>
    <span class="nss-pop-title" id="nssPopTitle">Mentions</span>
    <span class="nss-pop-count" id="nssPopCount">…</span>
    <button class="nss-pop-close" onclick="NSSPopup.close()">×</button>
  </div>
  <div class="nss-pop-list" id="nssPopList"></div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

const NSS_PID = {{ $projectId ? (int)$projectId : 'null' }};
const NSS_SD  = '{{ $startDate }}';
const NSS_ED  = '{{ $endDate }}';
const FONT    = "'Poppins',-apple-system,sans-serif";

const $      = id => document.getElementById(id);
const numFmt = n  => (parseInt(n) || 0).toLocaleString('id-ID');
const numK   = n  => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const pct    = (v,t) => t>0?((v/t)*100).toFixed(1)+'%':'0%';

function countUp(el, target, duration = 900) {
  if (!el) return;
  el.innerHTML = '';
  const start   = performance.now();
  const easeOut = t => 1 - Math.pow(1 - t, 3);
  (function tick(now) {
    const p = Math.min((now - start) / duration, 1);
    el.textContent = numFmt(Math.round(target * easeOut(p)));
    if (p < 1) requestAnimationFrame(tick);
  })(performance.now());
}

let _raf = null;

/*
  Needle rotation mapping:
    NSS −100% → rotate(−90°, 250, 260)  ← points left (180° in unit circle)
    NSS    0% → rotate(  0°, 250, 260)  ← points straight up
    NSS +100% → rotate(+90°, 250, 260)  ← points right (0° in unit circle)

  Formula: rotDeg = (nss / 100) * 90
  The needle SVG polygon is drawn pointing UP at rest (−90° from SVG x-axis),
  so we apply transform="rotate(rotDeg, 250, 260)" in JS.
  At rotDeg=0 → needle up (NSS=0, pointing at 0% mark).
*/
function nssToRot(nss) {
  return (Math.max(-100, Math.min(100, nss)) / 100) * 90;
}

function renderGauge(nss) {
  const val       = Math.max(-100, Math.min(100, nss));
  const targetRot = nssToRot(val);

  const needle  = $('needleGroup');
  const scoreEl = $('scoreValue');
  const labelEl = $('scoreLabel');

  const isPos     = val >  5;
  const isNeg     = val < -5;
  const color     = isPos ? '#2FC6F6' : isNeg ? '#ef4444' : '#94a3b8';
  const labelText = isPos ? 'POSITIF' : isNeg ? 'NEGATIF' : 'NETRAL';
  const finalStr  = (val > 0 ? '+' : '') + val.toFixed(0) + '%';

  /* Read current rotation from existing transform */
  const tf  = needle.getAttribute('transform') || 'rotate(-90,250,260)';
  const cur = parseFloat((tf.match(/rotate\(([-\d.]+)/) || [0, -90])[1]);

  if (_raf) cancelAnimationFrame(_raf);

  const duration = 1200;
  const t0       = performance.now();
  function ease(t) { return t < .5 ? 2*t*t : -1+(4-2*t)*t; }

  (function frame(now) {
    const p   = Math.min((now - t0) / duration, 1);
    const rot = cur + (targetRot - cur) * ease(p);

    /* Always rotate around the exact arc center (250, 260) */
    needle.setAttribute('transform', `rotate(${rot.toFixed(3)}, 250, 260)`);

    const liveNss = (rot / 90) * 100;
    if (scoreEl) {
      scoreEl.textContent = (liveNss > 0 ? '+' : '') + liveNss.toFixed(0) + '%';
      scoreEl.style.color = color;
    }

    if (p < 1) {
      _raf = requestAnimationFrame(frame);
    } else {
      if (scoreEl) { scoreEl.textContent = finalStr; scoreEl.style.color = color; }
      if (labelEl) {
        labelEl.textContent = labelText;
        labelEl.style.color = isPos ? '#0ea5e9' : isNeg ? '#dc2626' : '#94a3b8';
      }
      _raf = null;
    }
  })(performance.now());
}

let _distChart = null;
window.addEventListener('resize', () => { if (_distChart && !_distChart.isDisposed()) _distChart.resize(); });

function renderDist(pos, neu, neg) {
  const dom = $('chDist');
  if (!dom) return;
  if (!_distChart || _distChart.isDisposed()) {
    _distChart = echarts.init(dom, null, { renderer: 'canvas' });
  }
  const tot   = pos + neu + neg || 1;
  const pPct  = +(pos / tot * 100).toFixed(1);
  const nuPct = +(neu / tot * 100).toFixed(1);
  const nePct = +(neg / tot * 100).toFixed(1);

  const EC_TIP = {
    backgroundColor:'#1a202c', borderColor:'#334155', borderWidth:1,
    padding:[10,14], textStyle:{color:'#fff',fontFamily:FONT,fontSize:12},
    extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
  };

  _distChart.setOption({
    animation: true, animationDuration: 900, animationEasing: 'elasticOut',
    backgroundColor: 'transparent',
    tooltip: {
      ...EC_TIP, trigger: 'axis', axisPointer: { type: 'none' },
      formatter: params => {
        const p = params.find(x => x.value > 0);
        return p
          ? `<div style="font-weight:700;margin-bottom:4px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};margin-right:6px;vertical-align:middle;"></span>${p.seriesName}</div>
             <div style="display:flex;justify-content:space-between;gap:20px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;">${p.value}%</span></div>`
          : '';
      }
    },
    grid: { left: 0, right: 20, top: 6, bottom: 0, containLabel: true },
    xAxis: {
      type: 'value', max: 100,
      axisLine: { show: false }, axisTick: { show: false },
      splitLine: { lineStyle: { color: '#f1f5f9', type: 'dashed' } },
      axisLabel: { fontFamily: FONT, fontSize: 10, color: '#94a3b8', formatter: v => v + '%' },
    },
    yAxis: {
      type: 'category', data: ['Negative', 'Neutral', 'Positive'],
      axisTick: { show: false }, axisLine: { show: false },
      axisLabel: { fontFamily: FONT, fontSize: 12, fontWeight: '700', color: '#475569', margin: 12 },
    },
    series: [
      {
        name: 'Positive', type: 'bar', data: [null, null, pPct],
        barMaxWidth: 32, barBorderRadius: [0, 6, 6, 0],
        itemStyle: { color: { type:'linear',x:0,y:0,x2:1,y2:0, colorStops:[{offset:0,color:'rgba(47,198,246,.1)'},{offset:1,color:'#2FC6F6'}] } },
        label: { show: true, position:'right', fontFamily:FONT, fontSize:12, fontWeight:'700', color:'#2FC6F6', formatter:v=>v.value+'%' },
      },
      {
        name: 'Neutral', type: 'bar', data: [null, nuPct, null],
        barMaxWidth: 32, barBorderRadius: [0, 6, 6, 0],
        itemStyle: { color: { type:'linear',x:0,y:0,x2:1,y2:0, colorStops:[{offset:0,color:'rgba(148,163,184,.1)'},{offset:1,color:'#94a3b8'}] } },
        label: { show: true, position:'right', fontFamily:FONT, fontSize:12, fontWeight:'700', color:'#94a3b8', formatter:v=>v.value+'%' },
      },
      {
        name: 'Negative', type: 'bar', data: [nePct, null, null],
        barMaxWidth: 32, barBorderRadius: [0, 6, 6, 0],
        itemStyle: { color: { type:'linear',x:0,y:0,x2:1,y2:0, colorStops:[{offset:0,color:'rgba(239,68,68,.1)'},{offset:1,color:'#ef4444'}] } },
        label: { show: true, position:'right', fontFamily:FONT, fontSize:12, fontWeight:'700', color:'#ef4444', formatter:v=>v.value+'%' },
      },
    ],
  });

  _distChart.on('click', params => {
    if (params.componentType === 'series') {
      const sentMap = { 'Positive':'pos', 'Neutral':'neu', 'Negative':'neg' };
      const sent = sentMap[params.seriesName] || 'all';
      const rect = dom.getBoundingClientRect();
      NSSPopup.openSentiment(sent, {clientX: rect.left + rect.width/2, clientY: rect.top + rect.height/2});
    }
  });
}

function updateBreakdown(pos, neu, neg, nss) {
  const tot    = pos + neu + neg;
  $('brkPos').textContent = numFmt(pos);
  $('brkNeu').textContent = numFmt(neu);
  $('brkNeg').textContent = numFmt(neg);
  $('brkTot').textContent = numFmt(tot);

  const isPos  = nss >  5;
  const isNeg  = nss < -5;
  const color  = isPos ? '#2FC6F6' : isNeg ? '#ef4444' : '#94a3b8';
  const nssStr = (nss >= 0 ? '+' : '') + nss.toFixed(1) + '%';

  const nssEl = $('brkNSS');
  nssEl.textContent = nssStr;
  nssEl.style.color = color;

  const badge = $('nssBadgeMain');
  if (badge) {
    badge.textContent = nssStr;
    if (isPos) {
      badge.style.cssText = 'background:#dbeafe;color:#1e40af;border:none;';
    } else if (isNeg) {
      badge.style.cssText = 'background:#fee2e2;color:#991b1b;border:none;';
    } else {
      badge.style.cssText = 'background:var(--bg-gray-100);color:var(--text-secondary);border:none;';
    }
  }
}

async function loadNSS() {
  if (!NSS_PID) return;

  try {
    const media = document.querySelector('.nss-media-menu-item.active')?.dataset.m || 'all';
    const url  = `/mk/api/sentiment/totals?project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&media=${media}`;
    const res  = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const json = await res.json();
    if (json.error) throw new Error(json.error);

    const t   = json.totals || { pos:0, neg:0, neu:0 };
    const pos = parseInt(t.pos) || 0;
    const neg = parseInt(t.neg) || 0;
    const neu = parseInt(t.neu) || 0;
    const tot = pos + neg + neu;

    /* ── FIXED FORMULA: exclude neutral from denominator (matches Drone Emprit) ── */
    const posneg = pos + neg;
    const nss    = posneg === 0 ? 0 : ((pos - neg) / posneg * 100);

    countUp($('statPos'), pos);
    countUp($('statNeu'), neu);
    countUp($('statNeg'), neg);
    $('pctPos').textContent = pct(pos, tot);
    $('pctNeu').textContent = pct(neu, tot);
    $('pctNeg').textContent = pct(neg, tot);

    $('legPos').textContent = numFmt(pos) + ' Positive';
    $('legNeu').textContent = numFmt(neu) + ' Neutral';
    $('legNeg').textContent = numFmt(neg) + ' Negative';

    updateBreakdown(pos, neu, neg, nss);
    renderGauge(nss);
    renderDist(pos, neu, neg);

    NSSPopup._currentData = { pos, neg, neu, tot };

  } catch (err) {
    console.error('loadNSS:', err);
  }
}

const MEDIA_LABELS = {
  all:'All Media', doc:'Mass Media (News)', twitter:'X / Twitter',
  facebook:'Facebook', instagram:'Instagram', youtube:'YouTube', tiktok:'TikTok',
};

const NSSPage = {
  toggleMenu() {
    const open = $('mediaMenu').classList.toggle('open');
    $('mediaBtnEl').classList.toggle('open', open);
  },
  selectMedia(el) {
    document.querySelectorAll('.nss-media-menu-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    $('mediaBtnLabel').textContent = MEDIA_LABELS[el.dataset.m] || 'All Media';
    $('mediaMenu').classList.remove('open');
    $('mediaBtnEl').classList.remove('open');
    loadNSS();
  },
};

document.addEventListener('click', e => {
  const wrap = $('mediaDdWrap');
  if (wrap && !wrap.contains(e.target)) {
    $('mediaMenu').classList.remove('open');
    $('mediaBtnEl').classList.remove('open');
  }
});

/* ═══════════════════════════════════════════════════
   MENTION POPUP
═══════════════════════════════════════════════════ */
const NSSPopup = {
  _drag: false, _ox: 0, _oy: 0,
  _currentData: { pos:0, neg:0, neu:0, tot:0 },
  _cache: {},

  init() {
    const header = $('nssPopHeader');
    if (!header) return;

    header.addEventListener('mousedown', e => {
      this._drag = true;
      const r = $('nssPopup').getBoundingClientRect();
      this._ox = e.clientX - r.left;
      this._oy = e.clientY - r.top;
      document.body.style.userSelect = 'none';
    });

    document.addEventListener('mousemove', e => {
      if (!this._drag) return;
      const popup = $('nssPopup');
      const vw = window.innerWidth, vh = window.innerHeight;
      popup.style.left = Math.max(0, Math.min(e.clientX - this._ox, vw - popup.offsetWidth))  + 'px';
      popup.style.top  = Math.max(0, Math.min(e.clientY - this._oy, vh - popup.offsetHeight)) + 'px';
    });

    document.addEventListener('mouseup', () => { this._drag = false; document.body.style.userSelect = ''; });
  },

  openSentiment(sentiment, event) {
    const popup = $('nssPopup');
    let x = window.innerWidth / 2, y = window.innerHeight / 2;
    if (event) {
      x = event.clientX || event.pageX || x;
      y = event.clientY || event.pageY || y;
    }
    this.open(sentiment, x, y);
  },

  open(sentiment, x, y) {
    const popup = $('nssPopup');
    if (!popup) return;

    const sentimentMap = { pos: 'Positive', neg: 'Negative', neu: 'Neutral' };
    const colorMap = { pos: '#2FC6F6', neg: '#ef4444', neu: '#94a3b8' };

    $('nssPopDot').style.background = colorMap[sentiment] || '#038047';
    $('nssPopTitle').textContent = sentimentMap[sentiment] || 'Mentions';
    $('nssPopCount').textContent = '…';

    const list = $('nssPopList');
    list.innerHTML = `<div class="nss-pop-loading"><div class="nss-pop-spinner"></div>Memuat mentions…</div>`;

    const pw = 480, ph = 600, vw = window.innerWidth, vh = window.innerHeight;
    let left = x + 18, top = y - 40;
    if (left + pw > vw - 12) left = x - pw - 18;
    if (top + ph > vh - 12) top = vh - ph - 12;
    if (top < 8) top = 8;
    if (left < 8) left = 8;
    popup.style.left = left + 'px';
    popup.style.top = top + 'px';
    popup.classList.add('visible');

    this._fetchAndRender(sentiment, list);
  },

  async _fetchAndRender(sentiment, listEl) {
    try {
      const media = document.querySelector('.nss-media-menu-item.active')?.dataset.m || 'all';
      const cacheKey = `${NSS_PID}_${sentiment}_${media}_${NSS_SD}_${NSS_ED}`;

      if (!this._cache[cacheKey]) {
        const platforms = media === 'all'
          ? ['doc', 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok']
          : [media];

        let allItems = [];
        for (const plat of platforms) {
          const items = await this._fetchMentions(plat);
          allItems = allItems.concat(items || []);
        }
        this._cache[cacheKey] = allItems;
      }

      const items = this._cache[cacheKey];
      const filtered = sentiment === 'all' ? items : items.filter(item => this._normalizeSentiment(item) === sentiment);

      $('nssPopCount').textContent = filtered.length.toString();
      this._render(listEl, filtered, sentiment);

    } catch (err) {
      console.error('Popup fetch error:', err);
      listEl.innerHTML = `<div class="nss-pop-loading" style="color:#94a3b8;">Gagal memuat data</div>`;
      $('nssPopCount').textContent = '0';
    }
  },

  async _fetchMentions(platform) {
    const q = `project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&rows=100&start=0`;
    const endpoints = {
      doc:      `/mk/api/news/mentions?${q}`,
      twitter:  `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
      facebook: `/mk/api/news/fb-top-status?${q}&sub=fblike`,
      instagram: `/mk/api/news/ig-top-status?${q}`,
      youtube:  `/mk/api/news/ytb-top-status?${q}`,
      tiktok:   `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
    };

    const url = endpoints[platform];
    if (!url) return [];

    try {
      const ctrl = new AbortController();
      const tid = setTimeout(() => ctrl.abort(), 15000);
      const res = await fetch(url, { signal: ctrl.signal });
      clearTimeout(tid);

      if (!res.ok) return [];
      const data = await res.json();
      return Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
    } catch (err) {
      return [];
    }
  },

  _normalizeSentiment(item) {
    const raw = String(item.class_sentiment || item.sentiment || '0').toLowerCase().trim();
    if (raw === '1' || raw === 'positive' || raw === 'positif') return 'pos';
    if (raw === '-1' || raw === '2' || raw === 'negative' || raw === 'negatif') return 'neg';
    return 'neu';
  },

  _render(listEl, items, sentiment) {
    if (!items.length) {
      listEl.innerHTML = `<div class="nss-pop-loading" style="color:#94a3b8;">Tidak ada mention</div>`;
      return;
    }

    const SHOW = 50;
    listEl.innerHTML = items.slice(0, SHOW).map(item => {
      const sent = this._normalizeSentiment(item);
      const badgeClass = { pos: 'nss-pop-badge--pos', neg: 'nss-pop-badge--neg', neu: 'nss-pop-badge--neu' }[sent] || 'nss-pop-badge--neu';
      const badgeText = { pos: 'Pos', neg: 'Neg', neu: 'Neu' }[sent] || 'Neu';

      const name = (item.author_name || item.channel_name || item.publisher || item.source_name || item.name || 'Unknown').trim();
      const text = (item.content || item.caption || item.description || item.title || item.text || '').replace(/<[^>]*>/g, '').trim().slice(0, 120);
      const av = (item.avatar_url || item.profile_image_url || item.profile_image || '').trim();
      const dt = (item.date_created || item.created_at || '').split('T')[0];

      const words = name.replace(/[^a-zA-Z0-9\s]/g, '').trim().split(/\s+/).filter(Boolean);
      const ini = words.length >= 2 ? (words[0][0] + words[words.length - 1][0]).toUpperCase() : (words[0]?.[0] || 'U').toUpperCase();

      const avHtml = av && (av.startsWith('http://') || av.startsWith('https://'))
        ? `<img src="${av}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';this.parentElement.textContent='${ini}'">`
        : ini;

      return `<div class="nss-pop-item">
        <div class="nss-pop-avatar">${avHtml}</div>
        <div class="nss-pop-body">
          <div class="nss-pop-author">${name.slice(0, 30)}</div>
          <div class="nss-pop-text">${text || '(no content)'}</div>
          <div class="nss-pop-footer">
            <span class="nss-pop-badge ${badgeClass}">${badgeText}</span>
            ${dt ? `<span>${dt}</span>` : ''}
          </div>
        </div>
      </div>`;
    }).join('');

    if (items.length > SHOW) {
      listEl.insertAdjacentHTML('beforeend', `<div style="padding:9px;text-align:center;font-size:11px;color:#94a3b8;">+${items.length - SHOW} more</div>`);
    }
  },

  close() {
    const popup = $('nssPopup');
    if (popup) popup.classList.remove('visible');
  }
};

document.addEventListener('DOMContentLoaded', () => {
  /* Set needle to starting position: pointing straight up (NSS = 0) */
  const needle = $('needleGroup');
  if (needle) needle.setAttribute('transform', 'rotate(0, 250, 260)');
  NSSPopup.init();
  loadNSS();
});
</script>
@endsection