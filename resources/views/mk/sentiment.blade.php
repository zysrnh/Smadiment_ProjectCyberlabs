@extends('mk.layouts.app')

@section('title', 'Sentiment Analysis - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
    --primary-green-light: rgba(3,128,71,0.08);
    --primary-green-border: rgba(3,128,71,0.2);

    --sent-pos:  #2FC6F6;
    --sent-neg:  #ef4444;
    --sent-neu:  #94a3b8;
    --sent-pos-bg: rgba(47,198,246,.1);
    --sent-neg-bg: rgba(239,68,68,.1);
    --sent-neu-bg: rgba(148,163,184,.1);

    --text-primary:   #1E293B;
    --text-secondary: #64748b;
    --text-muted:     #94a3b8;

    --bg-white:   #ffffff;
    --bg-body:    #f0f4f8;
    --bg-gray-50: #f8fafc;
    --bg-gray-100:#f1f5f9;

    --border-gray: #e2e8f0;
    --border-light:#f1f5f9;

    --shadow-sm: 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
    --shadow-md: 0 4px 14px rgba(15,23,42,.08);
    --shadow-lg: 0 10px 30px rgba(15,23,42,.12);
    --shadow-xl: 0 20px 40px -8px rgba(0,0,0,.18);

    --radius:    8px;
    --radius-sm: 5px;
    --radius-xs: 4px;
    --transition:all 0.2s cubic-bezier(0.4,0,0.2,1);
    --font:'Poppins', -apple-system, sans-serif;
  }

  *, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
  body { font-family:var(--font); background:var(--bg-body); color:var(--text-primary); }

  /* ── ANIMATIONS ── */
  @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
  @keyframes fadeIn { from{opacity:0} to{opacity:1} }
  @keyframes slideInRight { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
  @keyframes slideOutRight { from{transform:translateX(0);opacity:1} to{transform:translateX(100%);opacity:0} }
  @keyframes overlayIn { from{opacity:0} to{opacity:1} }
  @keyframes overlayOut { from{opacity:1} to{opacity:0} }
  .fade-up { animation:fadeUp .36s ease-out both; }
  .fade-up-d1 { animation-delay:.04s }
  .fade-up-d2 { animation-delay:.08s }
  .fade-up-d3 { animation-delay:.12s }
  .fade-up-d4 { animation-delay:.16s }

  /* ── PAGE WRAPPER ── */
  .snt-page { padding:24px; max-width:1600px; margin:0 auto; }

  /* ── PAGE HEADER ── */
  .page-header {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px; margin-bottom:20px;
  }
  .page-header-left h1 { font-size:18px; font-weight:800; color:var(--text-primary); letter-spacing:-.3px; margin:0 0 3px; }
  .page-header-left p  { font-size:12px; color:var(--text-muted); font-weight:500; margin:0; }

  .snt-refresh-btn {
    display:inline-flex; align-items:center; gap:6px; padding:7px 14px;
    background:var(--text-primary); color:#fff; border:none;
    border-radius:var(--radius-sm); font-family:var(--font);
    font-size:12px; font-weight:700; cursor:pointer;
    transition:filter .14s, box-shadow .14s; box-shadow:var(--shadow-sm);
  }
  .snt-refresh-btn:hover { filter:brightness(1.15); }
  .snt-refresh-btn svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }

  /* ── FILTER CARD ── */
  .filter-card {
    background:#fff; border-radius:var(--radius);
    padding:14px 18px; margin-bottom:20px;
    box-shadow:var(--shadow-sm); border:1px solid var(--border-gray);
  }
  .filter-content { display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; }
  .filter-group   { display:flex; flex-direction:column; gap:5px; }
  .filter-label   { font-size:10px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.5px; }
  .filter-select  {
    padding:7px 12px; border:1px solid var(--border-gray);
    border-radius:var(--radius-sm); font-family:var(--font);
    font-size:13px; font-weight:500; color:var(--text-primary);
    background:var(--bg-gray-50); outline:none; transition:border-color .14s, box-shadow .14s; min-width:180px; cursor:pointer;
  }
  .filter-select:focus { border-color:var(--primary-green); background:#fff; box-shadow:0 0 0 3px var(--primary-green-light); }

  .apply-btn {
    display:inline-flex; align-items:center; gap:6px; padding:7px 18px;
    background:var(--primary-green); color:#fff; border:none;
    border-radius:var(--radius-sm); font-family:var(--font);
    font-size:13px; font-weight:700; cursor:pointer;
    transition:filter .14s, box-shadow .14s; white-space:nowrap; margin-left:auto;
  }
  .apply-btn:hover { filter:brightness(1.1); box-shadow:0 4px 12px var(--primary-green-light); }
  .apply-btn svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2.5; stroke-linecap:round; }

  /* ── SECTION HEADER ── */
  .snt-section-header { display:flex; align-items:center; gap:8px; margin-bottom:14px; margin-top:4px; }
  .snt-section-icon {
    width:30px; height:30px; border-radius:var(--radius-sm);
    background:var(--primary-green-light);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
  }
  .snt-section-icon svg { width:15px; height:15px; stroke:var(--primary-green); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
  .snt-section-title { font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.7px; }
  .snt-section-line  { flex:1; height:1px; background:var(--border-gray); border-radius:1px; }

  /* ── STAT CARDS (KPI style like dashboard) ── */
  .snt-stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; }

  .snt-stat-card {
    border-radius:var(--radius); padding:18px 20px;
    position:relative; overflow:hidden; border:none;
    transition:transform .18s, box-shadow .18s;
  }
  .snt-stat-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-lg) !important; }

  .snt-stat-card--neg  { background:linear-gradient(135deg,#EF4444,#B91C1C); }
  .snt-stat-card--pos  { background:linear-gradient(135deg,#0ea5e9,#0284c7); }
  .snt-stat-card--neu  { background:linear-gradient(135deg,#94a3b8,#64748b); }
  .snt-stat-card--tot  { background:linear-gradient(135deg,#7c3aed,#5b21b6); }

  .snt-stat-icon-wrap {
    width:34px; height:34px; border-radius:var(--radius-sm);
    background:rgba(255,255,255,.18);
    display:flex; align-items:center; justify-content:center;
    margin-bottom:10px;
  }
  .snt-stat-icon-wrap i { font-size:17px; color:#fff; }

  .snt-stat-label { font-size:10px; font-weight:700; color:rgba(255,255,255,.65); text-transform:uppercase; letter-spacing:.7px; margin-bottom:3px; display:flex; align-items:center; gap:6px; }
  .snt-stat-dot   { display:none; }
  .snt-stat-value { font-size:26px; font-weight:800; color:#fff; letter-spacing:-1px; line-height:1; min-height:32px; display:flex; align-items:center; margin-bottom:5px; }
  .snt-stat-sub   { font-size:11px; color:rgba(255,255,255,.60); font-weight:600; display:flex; align-items:center; gap:4px; }
  .snt-stat-pct   { font-size:12px; font-weight:700; margin-top:3px; color:rgba(255,255,255,.80); }

  .snt-stat-watermark {
    position:absolute; right:-6px; bottom:-6px;
    width:72px; height:72px;
    stroke:rgba(255,255,255,.07); fill:none; stroke-width:1.5;
    pointer-events:none;
  }

  /* ── CARDS ── */
  .snt-card {
    background:#fff; border:1px solid var(--border-gray);
    border-radius:var(--radius); overflow:hidden; display:flex;
    flex-direction:column; box-shadow:var(--shadow-sm);
    transition:border-color .2s, box-shadow .2s;
  }
  .snt-card:hover { box-shadow:var(--shadow-md); border-color:rgba(226,232,240,.8); }

  .snt-card-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 16px; border-bottom:1px solid var(--border-gray);
    background:#fff; flex-shrink:0;
  }
  .snt-card-head-left { display:flex; align-items:center; gap:8px; min-width:0; }
  .snt-head-icon {
    width:30px; height:30px; border-radius:var(--radius-sm);
    background:var(--primary-green-light);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
  }
  .snt-head-icon svg { width:15px; height:15px; fill:none; stroke:var(--primary-green); stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
  .snt-card-title    { font-size:13px; font-weight:700; color:var(--text-primary); }
  .snt-card-subtitle { font-size:10px; color:var(--text-muted); font-weight:500; margin-top:1px; }
  .snt-badge {
    display:inline-flex; align-items:center; padding:2px 8px; border-radius:3px;
    font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px;
    background:var(--bg-gray-100); color:var(--text-secondary); white-space:nowrap; flex-shrink:0;
  }
  .snt-card-body { padding:16px; flex:1; }

  /* ── LEGEND ── */
  .snt-legend { display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
  .snt-legend-item { display:flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:var(--text-secondary); }
  .snt-legend-dot  { width:7px; height:7px; border-radius:50%; flex-shrink:0; }

  /* ── GRID LAYOUTS ── */
  .snt-grid-2   { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }
  .snt-grid-3   { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:14px; }
  .snt-grid-2-3 { display:grid; grid-template-columns:1fr 1.6fr; gap:14px; margin-bottom:14px; }
  .snt-grid-3-2 { display:grid; grid-template-columns:1.6fr 1fr; gap:14px; margin-bottom:14px; }
  .snt-mb20     { margin-bottom:14px; }

  /* ── CHART HEIGHTS ── */
  .snt-ch-260 { position:relative; height:260px; }
  .snt-ch-300 { position:relative; height:300px; }
  .snt-ch-320 { position:relative; height:320px; }
  .snt-ch-340 { position:relative; height:340px; }
  .snt-ch-380 { position:relative; height:380px; }
  .snt-ch-420 { position:relative; height:420px; }

  /* ── SKELETON ── */
  .snt-skel {
    background:linear-gradient(90deg,var(--bg-gray-50) 25%,#e2e8f0 50%,var(--bg-gray-50) 75%);
    background-size:200% 100%; animation:shimmer 1.5s ease-in-out infinite; border-radius:var(--radius-xs);
  }
  @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
  .snt-skel-overlay { position:absolute; inset:0; z-index:3; border-radius:inherit; }

  /* ── EMPTY STATE ── */
  .snt-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:44px 20px; gap:10px; }
  .snt-empty svg { width:40px; height:40px; stroke:var(--border-gray); fill:none; stroke-width:1.5; }
  .snt-empty-text { font-size:13px; font-weight:600; color:var(--text-secondary); }

  /* ── CSV BTN ── */
  .snt-csv-btn {
    display:flex; align-items:center; gap:4px; padding:4px 10px;
    background:var(--bg-gray-100); border:1px solid var(--border-gray);
    border-radius:var(--radius-xs); font-family:var(--font);
    font-size:10px; font-weight:700; color:var(--text-secondary);
    cursor:pointer; transition:all .14s;
  }
  .snt-csv-btn:hover { background:var(--primary-green); border-color:var(--primary-green); color:#fff; }
  .snt-csv-btn svg { width:11px; height:11px; stroke:currentColor; fill:none; stroke-width:2; }

  /* ── PLATFORM MINI LIST ── */
  .snt-media-list { display:flex; flex-direction:column; gap:6px; }
  .snt-media-row {
    display:flex; align-items:center; gap:8px;
    padding:8px 12px; background:var(--bg-gray-50);
    border:1px solid var(--border-light); border-radius:var(--radius-sm);
    transition:background .13s, border-color .13s;
  }
  .snt-media-row:hover { border-color:var(--primary-green-border); background:#fff; box-shadow:var(--shadow-sm); }
  .snt-media-icon { width:28px; height:28px; border-radius:var(--radius-xs); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .snt-media-name { font-size:11px; font-weight:700; color:var(--text-primary); min-width:80px; }
  .snt-media-bars { flex:1; display:flex; flex-direction:column; gap:2px; }
  .snt-media-bar-row { display:flex; align-items:center; gap:5px; }
  .snt-media-bar-track { flex:1; height:4px; background:var(--bg-gray-100); border-radius:2px; overflow:hidden; }
  .snt-media-bar-fill  { height:100%; border-radius:2px; transition:width .8s cubic-bezier(.4,0,.2,1); }
  .snt-media-bar-val   { font-size:10px; font-weight:700; color:var(--text-secondary); min-width:36px; text-align:right; white-space:nowrap; }
  .snt-media-total     { font-size:11px; font-weight:700; color:var(--text-primary); min-width:48px; text-align:right; }

  @media (max-width:1280px) {
    .snt-stat-grid { grid-template-columns:repeat(2,1fr); }
    .snt-grid-2, .snt-grid-3, .snt-grid-2-3, .snt-grid-3-2 { grid-template-columns:1fr; }
  }
  @media (max-width:768px) {
    .snt-page { padding:16px; }
    .snt-stat-grid { grid-template-columns:1fr; }
    #sntPopup { width:100vw; }
    .sntp-actions { flex-wrap:wrap; }
  }

  /* ══════════════════════════════════════════════════════
     SLIDE PANEL (consistent with data-overview)
  ══════════════════════════════════════════════════════ */

  /* ── PANEL OVERLAY ── */
  .snt-panel-overlay {
    position:fixed; inset:0; z-index:9000;
    background:rgba(15,23,42,.45); backdrop-filter:blur(4px);
    display:none;
  }
  .snt-panel-overlay.show { display:block; animation:overlayIn .22s ease-out; }
  .snt-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }

  /* ── MAIN PANEL ── */
  #sntPopup {
    position:fixed; top:0; right:0; bottom:0; z-index:9001;
    width:480px; max-width:100vw;
    background:#fff; display:none; flex-direction:column;
    border-left:1px solid var(--border-gray);
    box-shadow:-8px 0 40px rgba(15,23,42,.16);
    font-family:var(--font);
  }
  #sntPopup.show { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
  #sntPopup.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }

  /* ── HEADER ── */
  .sntp-header {
    display:flex; align-items:center; gap:10px;
    padding:14px 16px;
    background:var(--bg-gray-50); border-bottom:1px solid var(--border-gray);
    flex-shrink:0;
  }

  .sntp-drag-handle { display:none; }

  .sntp-dot   { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
  .sntp-title { font-size:13px; font-weight:700; color:var(--text-primary); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

  .sntp-count {
    display:none;
  }

  .sntp-close {
    width:28px; height:28px; border-radius:var(--radius-sm);
    border:1px solid var(--border-gray); background:#fff;
    cursor:pointer; font-size:16px; line-height:1;
    color:var(--text-secondary);
    display:flex; align-items:center; justify-content:center;
    transition:all .14s; flex-shrink:0;
  }
  .sntp-close:hover { background:#EF4444; border-color:#EF4444; color:#fff; }

  /* ── ACTIONS BAR ── */
  .sntp-actions {
    display:flex; align-items:center; gap:7px;
    padding:7px 12px; border-bottom:1px solid var(--border-gray);
    background:#fff; flex-shrink:0;
  }

  .sntp-meta {
    flex:1; font-size:10px; font-weight:700;
    color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px;
    display:flex; align-items:center; gap:5px; overflow:hidden;
  }
  .sntp-meta__label { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

  /* ── SENTIMENT FILTER TABS ── */
  .sntp-sent-tabs {
    display:flex; background:var(--bg-gray-100);
    border:1px solid var(--border-gray);
    border-radius:var(--radius-sm); padding:2px; gap:2px;
  }

  .sntp-sent-tab {
    padding:3px 9px; border-radius:3px; border:none;
    background:transparent; font-family:var(--font);
    font-size:11px; font-weight:700; cursor:pointer;
    transition:all .13s; color:var(--text-secondary);
    white-space:nowrap;
  }
  .sntp-sent-tab:hover { background:#fff; }
  .sntp-sent-tab.active { background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08); }
  .sntp-sent-tab.active[data-s="all"] { color:var(--primary-green); }
  .sntp-sent-tab.neg.active           { color:#ef4444; }
  .sntp-sent-tab.pos.active           { color:#0ea5e9; }
  .sntp-sent-tab.neu.active           { color:var(--text-secondary); }

  /* ── EXPORT BTN ── */
  .sntp-export-btn {
    display:flex; align-items:center; gap:4px;
    padding:4px 10px; background:var(--primary-green);
    color:#fff; border:none; border-radius:var(--radius-sm);
    font-family:var(--font); font-size:10px; font-weight:700;
    cursor:pointer; transition:filter .13s; white-space:nowrap;
  }
  .sntp-export-btn:hover { filter:brightness(1.1); }
  .sntp-export-btn svg { width:11px; height:11px; stroke:#fff; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }

  /* ── LIST ── */
  .sntp-list { overflow-y:auto; flex:1; padding:2px 0; min-height:0; }
  .sntp-list::-webkit-scrollbar { width:4px; }
  .sntp-list::-webkit-scrollbar-thumb { background:var(--border-gray); border-radius:99px; }
  .sntp-list::-webkit-scrollbar-thumb:hover { background:var(--text-muted); }

  .sntp-item {
    display:flex; gap:10px; padding:10px 14px;
    border-bottom:1px solid var(--border-light);
    transition:background .1s; cursor:pointer; align-items:flex-start;
  }
  .sntp-item:last-child { border-bottom:none; }
  .sntp-item:hover { background:#f0f9ff; }
  .sntp-item.hidden { display:none; }

  .sntp-avatar {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    color:#fff; font-weight:700; font-size:12px;
    display:flex; align-items:center; justify-content:center;
    border:1.5px solid var(--border-gray); overflow:hidden;
  }
  .sntp-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

  .sntp-item-body { flex:1; min-width:0; }
  .sntp-item-author { font-size:12px; font-weight:700; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .sntp-item-handle { font-size:10px; color:var(--text-muted); font-weight:500; margin-bottom:2px; }
  .sntp-item-text {
    font-size:11px; color:var(--text-secondary); line-height:1.5;
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;
    overflow:hidden; margin-bottom:4px;
  }
  .sntp-item-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--text-muted); flex-wrap:wrap; }

  .sntp-sent-badge {
    padding:1px 6px; border-radius:3px;
    font-size:9px; font-weight:800; text-transform:uppercase;
  }
  .sntp-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
  .sntp-sent-badge--neg { background:#fee2e2; color:#991b1b; }
  .sntp-sent-badge--neu { background:var(--bg-gray-100); color:var(--text-secondary); }

  /* ── LOADING ── */
  .sntp-loading {
    display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    height:100%; gap:12px;
    color:var(--text-secondary); font-size:13px; font-weight:600;
  }
  .sntp-spinner {
    width:28px; height:28px;
    border:2.5px solid var(--bg-gray-100);
    border-top-color:var(--primary-green);
    border-radius:50%;
    animation:sntSpin .65s linear infinite;
  }
  @keyframes sntSpin { to { transform:rotate(360deg); } }

  /* ── DETAIL SUB-PANEL (slides within main panel) ── */
  #sntDetailPanel {
    position:absolute; inset:0; background:#fff;
    z-index:5; display:none; flex-direction:column;
    animation:slideInRight .2s cubic-bezier(.4,0,.2,1);
  }
  #sntDetailPanel.visible { display:flex; }

  .sntdp-header {
    display:flex; align-items:center; gap:8px;
    padding:12px 14px; background:var(--bg-gray-50);
    border-bottom:1px solid var(--border-gray); flex-shrink:0;
  }
  .sntdp-back {
    width:28px; height:28px; border-radius:var(--radius-sm);
    border:1px solid var(--border-gray); background:#fff;
    cursor:pointer; display:flex; align-items:center; justify-content:center;
    color:var(--text-secondary); transition:all .13s; flex-shrink:0;
  }
  .sntdp-back:hover { background:var(--primary-green-light); color:var(--primary-green); border-color:var(--primary-green-border); }
  .sntdp-back svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }

  .sntdp-title { font-size:13px; font-weight:700; color:var(--text-primary); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .sntdp-close {
    width:28px; height:28px; border-radius:var(--radius-sm);
    border:1px solid var(--border-gray); background:#fff;
    cursor:pointer; font-size:16px; color:var(--text-secondary);
    display:flex; align-items:center; justify-content:center;
    transition:all .14s;
  }
  .sntdp-close:hover { background:#EF4444; border-color:#EF4444; color:#fff; }

  .sntdp-body { overflow-y:auto; flex:1; padding:16px; }
  .sntdp-body::-webkit-scrollbar { width:4px; }
  .sntdp-body::-webkit-scrollbar-thumb { background:var(--border-gray); border-radius:99px; }

  .sntdp-avatar-row   { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
  .sntdp-avatar-lg {
    width:46px; height:46px; border-radius:50%;
    color:#fff; font-weight:700; font-size:16px;
    display:flex; align-items:center; justify-content:center;
    border:2px solid var(--border-gray); overflow:hidden; flex-shrink:0;
  }
  .sntdp-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
  .sntdp-author-name   { font-size:14px; font-weight:700; color:var(--text-primary); }
  .sntdp-author-handle { font-size:11px; color:var(--text-muted); font-weight:500; }

  .sntdp-sent-badge {
    display:inline-flex; align-items:center; gap:4px;
    padding:4px 10px; border-radius:3px;
    font-size:11px; font-weight:700; margin-bottom:10px;
  }
  .sntdp-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
  .sntdp-sent-badge--neg { background:#fee2e2; color:#991b1b; }
  .sntdp-sent-badge--neu { background:var(--bg-gray-100); color:var(--text-secondary); }

  .sntdp-content-text {
    font-size:12px; color:var(--text-secondary); line-height:1.7;
    margin-bottom:12px; background:var(--bg-gray-50);
    border-radius:var(--radius-sm); padding:10px 12px;
    border:1px solid var(--border-gray); word-break:break-word;
  }
  .sntdp-meta-row {
    display:flex; align-items:center; justify-content:space-between;
    font-size:11px; color:var(--text-muted); font-weight:500; margin-bottom:10px;
  }
  .sntdp-stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
  .sntdp-stat-box {
    background:var(--bg-gray-50); border-radius:var(--radius-sm);
    padding:8px 10px; border:1px solid var(--border-gray); text-align:center;
  }
  .sntdp-stat-val { font-size:14px; font-weight:700; color:var(--text-primary); }
  .sntdp-stat-lbl { font-size:9px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }

  .sntdp-media-wrap { border-radius:var(--radius-sm); overflow:hidden; margin-bottom:10px; background:#000; }
  .sntdp-media-img  { width:100%; max-height:220px; object-fit:cover; display:block; }

  .sntdp-link-btn {
    display:flex; align-items:center; justify-content:center; gap:6px;
    padding:9px 14px; background:var(--primary-green); color:#fff;
    border-radius:var(--radius-sm); font-size:12px; font-weight:700;
    text-decoration:none; transition:filter .14s; width:100%; margin-top:4px;
  }
  .sntdp-link-btn:hover { filter:brightness(1.1); color:#fff; }
  .sntdp-link-btn svg { width:13px; height:13px; stroke:#fff; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }

  /* ── PLATFORM PICKER ── */
  #sntPlatPicker {
    position:fixed; z-index:20000;
    background:#fff; border:1px solid var(--border-gray);
    border-radius:var(--radius); box-shadow:var(--shadow-lg);
    padding:5px; min-width:175px; font-family:var(--font);
    animation:fadeUp .14s ease-out; display:none;
  }
  #sntPlatPicker.visible { display:block; }

  .sntpp-header {
    padding:4px 9px 6px; font-size:10px; font-weight:700;
    color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px;
    border-bottom:1px solid var(--border-light); margin-bottom:3px;
  }
  .sntpp-btn {
    display:flex; align-items:center; gap:7px; padding:7px 10px;
    border-radius:var(--radius-sm); font-size:12px; font-weight:600;
    cursor:pointer; background:transparent; border:none;
    font-family:var(--font); width:100%; text-align:left;
    color:var(--text-secondary); transition:background .12s;
  }
  .sntpp-btn:hover { background:var(--primary-green-light); color:var(--primary-green); }
  .sntpp-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-left:auto; }
</style>
@endsection

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date',   now()->format('Y-m-d'));
  $media     = request()->get('media', 'all');
  $projects  = $projects ?? [];
@endphp

<div class="snt-page">

  {{-- FILTER --}}
  <div class="filter-card">
    <form id="sntFilterForm" method="GET">
      <input type="hidden" name="project_id" id="hPid" value="{{ $projectId }}">
      <input type="hidden" name="start_date"  id="hSD"  value="{{ $startDate }}">
      <input type="hidden" name="end_date"    id="hED"  value="{{ $endDate }}">

      <div class="filter-content">
        @if(count($projects))
        <div class="filter-group">
          <label class="filter-label">Project</label>
          <select class="filter-select" onchange="document.getElementById('hPid').value=this.value">
            @foreach($projects as $p)
            <option value="{{ $p['id'] }}" {{ ($p['id'] == $projectId) ? 'selected' : '' }}>
              {{ $p['name'] ?? $p['title'] ?? 'Project #' . $p['id'] }}
            </option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="filter-group">
          <label class="filter-label">Media</label>
          <select class="filter-select" name="media" id="mediaFilter">
            <option value="all"       {{ $media === 'all'       ? 'selected' : '' }}>All Media</option>
            <option value="doc"       {{ $media === 'doc'       ? 'selected' : '' }}>Mass Media (Online News)</option>
            <option value="twitter"   {{ $media === 'twitter'   ? 'selected' : '' }}>X / Twitter</option>
            <option value="facebook"  {{ $media === 'facebook'  ? 'selected' : '' }}>Facebook</option>
            <option value="instagram" {{ $media === 'instagram' ? 'selected' : '' }}>Instagram</option>
            <option value="youtube"   {{ $media === 'youtube'   ? 'selected' : '' }}>YouTube</option>
            <option value="tiktok"    {{ $media === 'tiktok'    ? 'selected' : '' }}>TikTok</option>
          </select>
        </div>

        <button type="submit" class="apply-btn">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          Filter
        </button>
      </div>
    </form>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 1 — TOTAL MENTIONS BY SENTIMENTS
  ═══════════════════════════════════════════════════════ --}}
  <div class="snt-section-header">
    <div class="snt-section-icon">
      <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
    </div>
    <span class="snt-section-title">Total Mentions by Sentiments</span>
    <div class="snt-section-line"></div>
  </div>

  {{-- Stat Cards --}}
  <div class="snt-stat-grid">
    <div class="snt-stat-card snt-stat-card--neg">
      <div class="snt-stat-icon-wrap"><svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg></div>
      <div class="snt-stat-label">Negative</div>
      <div class="snt-stat-value" id="valNeg">
        <div class="snt-skel" style="height:32px;width:90px;border-radius:6px;background:rgba(255,255,255,.18);"></div>
      </div>
      <div class="snt-stat-sub">Total mention negatif</div>
      <div class="snt-stat-pct" id="pctNeg">—</div>
      <svg class="snt-stat-watermark" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
    </div>
    <div class="snt-stat-card snt-stat-card--pos">
      <div class="snt-stat-icon-wrap"><svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg></div>
      <div class="snt-stat-label">Positive</div>
      <div class="snt-stat-value" id="valPos">
        <div class="snt-skel" style="height:32px;width:90px;border-radius:6px;background:rgba(255,255,255,.18);"></div>
      </div>
      <div class="snt-stat-sub">Total mention positif</div>
      <div class="snt-stat-pct" id="pctPos">—</div>
      <svg class="snt-stat-watermark" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
    </div>
    <div class="snt-stat-card snt-stat-card--neu">
      <div class="snt-stat-icon-wrap"><svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><circle cx="12" cy="12" r="10"/><line x1="8" y1="15" x2="16" y2="15"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg></div>
      <div class="snt-stat-label">Neutral</div>
      <div class="snt-stat-value" id="valNeu">
        <div class="snt-skel" style="height:32px;width:90px;border-radius:6px;background:rgba(255,255,255,.18);"></div>
      </div>
      <div class="snt-stat-sub">Total mention netral</div>
      <div class="snt-stat-pct" id="pctNeu">—</div>
      <svg class="snt-stat-watermark" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="8" y1="15" x2="16" y2="15"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
    </div>
    <div class="snt-stat-card snt-stat-card--tot">
      <div class="snt-stat-icon-wrap"><svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg></div>
      <div class="snt-stat-label">Total Mentions</div>
      <div class="snt-stat-value" id="valTot">
        <div class="snt-skel" style="height:32px;width:90px;border-radius:6px;background:rgba(255,255,255,.18);"></div>
      </div>
      <div class="snt-stat-sub">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
      <svg class="snt-stat-watermark" viewBox="0 0 24 24"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
    </div>
  </div>

  {{-- Bar + Doughnut Overview --}}
  <div class="snt-grid-3-2 snt-mb20">
    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          <div>
            <div class="snt-card-title">Total Mentions by Sentiments</div>
            <div class="snt-card-subtitle">Perbandingan volume Negative / Positive / Neutral</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
          <button class="snt-csv-btn" onclick="SNTCsv.copyOverview()" title="Copy CSV">
            <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Copy CSV
          </button>
          <span class="snt-badge">Overview</span>
        </div>
      </div>
      <div class="snt-card-body">
        <div class="snt-ch-300" id="chOverviewWrap">
          <div id="chOverview" style="width:100%;height:100%;"></div>
          <div class="snt-skel snt-skel-overlay" id="skOverview"></div>
        </div>
        <div class="snt-legend" style="margin-top:14px;justify-content:center;">
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>

    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
          <div>
            <div class="snt-card-title">Share of Voice by Sentiment</div>
            <div class="snt-card-subtitle">Persentase distribusi sentimen</div>
          </div>
        </div>
        <span class="snt-badge">SOV</span>
      </div>
      <div class="snt-card-body" style="display:flex;flex-direction:column;align-items:center;">
        <div style="position:relative;height:280px;width:100%;">
          <div id="chSovTotal" style="width:100%;height:100%;"></div>
          <div class="snt-skel" style="position:absolute;inset:0;border-radius:8px;" id="skSovTotal"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 2 — MEDIA PLATFORMS
  ═══════════════════════════════════════════════════════ --}}
  <div class="snt-section-header">
    <div class="snt-section-icon">
      <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
    </div>
    <span class="snt-section-title">Media Platforms</span>
    <div class="snt-section-line"></div>
  </div>

  {{-- Sentiments in Mass Media + Social Media --}}
  <div class="snt-grid-2 snt-mb20">
    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="10" y1="6" x2="16" y2="6"/><line x1="10" y1="10" x2="16" y2="10"/></svg></span>
          <div>
            <div class="snt-card-title">Sentiments in Mass Media</div>
            <div class="snt-card-subtitle">Online News / Artikel</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <button class="snt-csv-btn" onclick="SNTCsv.copyMass()" title="Copy CSV"><svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> CSV</button>
          <span class="snt-badge">Mass Media</span>
        </div>
      </div>
      <div class="snt-card-body">
        <div class="snt-ch-260" id="chMassWrap">
          <div id="chMass" style="width:100%;height:100%;"></div>
          <div class="snt-skel snt-skel-overlay" id="skMass"></div>
        </div>
        <div class="snt-legend" style="margin-top:12px;justify-content:center;">
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>

    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><path d="M17 2H7a5 5 0 0 0-5 5v10a5 5 0 0 0 5 5h10a5 5 0 0 0 5-5V7a5 5 0 0 0-5-5z"/><circle cx="12" cy="12" r="3"/><circle cx="17.5" cy="6.5" r="1.5"/></svg></span>
          <div>
            <div class="snt-card-title">Sentiments in Social Media</div>
            <div class="snt-card-subtitle">Twitter · Facebook · Instagram · YouTube · TikTok</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <button class="snt-csv-btn" onclick="SNTCsv.copySocial()" title="Copy CSV"><svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> CSV</button>
          <span class="snt-badge">Social Media</span>
        </div>
      </div>
      <div class="snt-card-body">
        <div class="snt-ch-260" id="chSocialWrap">
          <div id="chSocial" style="width:100%;height:100%;"></div>
          <div class="snt-skel snt-skel-overlay" id="skSocial"></div>
        </div>
        <div class="snt-legend" style="margin-top:12px;justify-content:center;">
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Sentiments by Media Types (horizontal %) + Sentiments by Media Platforms (grouped bars) --}}
  <div class="snt-grid-2 snt-mb20">
    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          <div>
            <div class="snt-card-title">Sentiments by Media Types</div>
            <div class="snt-card-subtitle">Persentase per platform (%)</div>
          </div>
        </div>
        <span class="snt-badge">% Share</span>
      </div>
      <div class="snt-card-body">
        <div class="snt-ch-300" id="chByTypeWrap">
          <div id="chByType" style="width:100%;height:100%;"></div>
          <div class="snt-skel snt-skel-overlay" id="skByType"></div>
        </div>
      </div>
    </div>

    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/></svg></span>
          <div>
            <div class="snt-card-title">Sentiments by Media Platforms</div>
            <div class="snt-card-subtitle">Komparasi Mass Media vs Social Media</div>
          </div>
        </div>
        <span class="snt-badge">Grouped</span>
      </div>
      <div class="snt-card-body">
        <div class="snt-ch-300" id="chByPlatWrap">
          <div id="chByPlat" style="width:100%;height:100%;"></div>
          <div class="snt-skel snt-skel-overlay" id="skByPlat"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Mass Pie + Social Pie --}}
  <div class="snt-grid-2 snt-mb20">
    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
          <div>
            <div class="snt-card-title">Mass Media</div>
            <div class="snt-card-subtitle">Share of Voice sentimen — Online News</div>
          </div>
        </div>
        <span class="snt-badge">Mass</span>
      </div>
      <div class="snt-card-body" style="display:flex;flex-direction:column;align-items:center;">
        <div style="position:relative;height:260px;width:100%;">
          <div id="chMassPie" style="width:100%;height:100%;"></div>
          <div class="snt-skel" style="position:absolute;inset:0;border-radius:8px;" id="skMassPie"></div>
        </div>
      </div>
    </div>

    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><path d="M17 2H7a5 5 0 0 0-5 5v10a5 5 0 0 0 5 5h10a5 5 0 0 0 5-5V7a5 5 0 0 0-5-5z"/><circle cx="12" cy="12" r="3"/></svg></span>
          <div>
            <div class="snt-card-title">Social Media</div>
            <div class="snt-card-subtitle">Share of Voice sentimen — Social platforms</div>
          </div>
        </div>
        <span class="snt-badge">Social</span>
      </div>
      <div class="snt-card-body" style="display:flex;flex-direction:column;align-items:center;">
        <div style="position:relative;height:260px;width:100%;">
          <div id="chSocialPie" style="width:100%;height:100%;"></div>
          <div class="snt-skel" style="position:absolute;inset:0;border-radius:8px;" id="skSocialPie"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Detail per platform (bar list) --}}
  <div class="snt-card snt-mb20">
    <div class="snt-card-head">
      <div class="snt-card-head-left">
        <span class="snt-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
        <div>
          <div class="snt-card-title">Breakdown Sentimen per Platform</div>
          <div class="snt-card-subtitle">Negative / Positive / Neutral untuk setiap media</div>
        </div>
      </div>
      <span class="snt-badge">All Platforms</span>
    </div>
    <div class="snt-card-body">
      <div id="platBreakdownList" class="snt-media-list">
        @foreach(['Mass Media','X / Twitter','Facebook','Instagram','YouTube','TikTok'] as $pl)
        <div class="snt-media-row">
          <div class="snt-media-name">{{ $pl }}</div>
          <div class="snt-media-bars">
            <div class="snt-media-bar-row"><div class="snt-skel" style="height:6px;width:100%;border-radius:3px;"></div></div>
            <div class="snt-media-bar-row"><div class="snt-skel" style="height:6px;width:100%;border-radius:3px;margin-top:4px;"></div></div>
          </div>
          <div class="snt-skel" style="height:18px;width:50px;border-radius:4px;margin-left:10px;"></div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 3 — SENTIMENT TRENDS
  ═══════════════════════════════════════════════════════ --}}
  <div class="snt-section-header">
    <div class="snt-section-icon">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <span class="snt-section-title">Sentiments Trends</span>
    <div class="snt-section-line"></div>
  </div>

  {{-- Line Trend --}}
  <div class="snt-card snt-mb20">
    <div class="snt-card-head">
      <div class="snt-card-head-left">
        <span class="snt-head-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
        <div>
          <div class="snt-card-title">Sentiment's Trends in All Media Types</div>
          <div class="snt-card-subtitle">Tren harian Negative / Positive / Neutral</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
        <button class="snt-csv-btn" onclick="SNTCsv.copyTrend()" title="Copy CSV">
          <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy CSV
        </button>
        <span class="snt-badge" id="trendBadge">Loading…</span>
      </div>
    </div>
    <div class="snt-card-body">
      <div class="snt-ch-380" id="chTrendWrap">
        <div id="chTrend" style="width:100%;height:100%;"></div>
        <div class="snt-skel snt-skel-overlay" id="skTrend"></div>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 4 — SENTIMENTS BY WEEKDAY & HOUR
  ═══════════════════════════════════════════════════════ --}}
  <div class="snt-section-header">
    <div class="snt-section-icon">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <span class="snt-section-title">Sentiments by Time</span>
    <div class="snt-section-line"></div>
  </div>

  <div class="snt-grid-2">
    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
          <div>
            <div class="snt-card-title">Sentiments by Weekday</div>
            <div class="snt-card-subtitle">Volume sentimen per hari dalam seminggu</div>
          </div>
        </div>
        <span class="snt-badge">7 Hari</span>
      </div>
      <div class="snt-card-body">
        <div class="snt-ch-320">
          <div id="chWeekday" style="width:100%;height:100%;"></div>
          <div class="snt-skel snt-skel-overlay" id="skWeekday"></div>
        </div>
        <div class="snt-legend" style="margin-top:12px;justify-content:center;">
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>

    <div class="snt-card">
      <div class="snt-card-head">
        <div class="snt-card-head-left">
          <span class="snt-head-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
          <div>
            <div class="snt-card-title">Sentiments by Hour</div>
            <div class="snt-card-subtitle">Distribusi sentimen per jam (00–23)</div>
          </div>
        </div>
        <span class="snt-badge">24 Jam</span>
      </div>
      <div class="snt-card-body">
        <div class="snt-ch-320">
          <div id="chHour" style="width:100%;height:100%;"></div>
          <div class="snt-skel snt-skel-overlay" id="skHour"></div>
        </div>
        <div class="snt-legend" style="margin-top:12px;justify-content:center;">
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="snt-legend-item"><span class="snt-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>
  </div>

</div>{{-- /snt-page --}}

{{-- ═══════════════════════════════════════════════════════
     SENTIMENT MENTION POPUP
═══════════════════════════════════════════════════════ --}}

{{-- PANEL OVERLAY --}}
<div class="snt-panel-overlay" id="sntPanelOverlay" onclick="SNTPopup.close()"></div>

{{-- MENTION PANEL (slide-in from right) --}}
<div id="sntPopup">
  <div class="sntp-header" id="sntPopHeader">
    <div class="sntp-dot" id="sntPopDot"></div>
    <span class="sntp-title" id="sntPopTitle">Mentions</span>
    <span class="sntp-count" id="sntPopCount">…</span>
    <button class="sntp-close" onclick="SNTPopup.close()">×</button>
  </div>
  <div class="sntp-actions">
    <div class="sntp-meta">
      <svg viewBox="0 0 24 24" style="width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <span class="sntp-meta__label" id="sntPopMeta">—</span>
    </div>
    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
      {{-- Sentiment filter tabs --}}
      <div class="sntp-sent-tabs" id="sntPopSentTabs">
        <button class="sntp-sent-tab active" data-s="all"   onclick="SNTPopup.filterSent('all')">Semua</button>
        <button class="sntp-sent-tab neg"    data-s="neg"   onclick="SNTPopup.filterSent('neg')">Neg</button>
        <button class="sntp-sent-tab pos"    data-s="pos"   onclick="SNTPopup.filterSent('pos')">Pos</button>
        <button class="sntp-sent-tab neu"    data-s="neu"   onclick="SNTPopup.filterSent('neu')">Neu</button>
      </div>
      <button class="sntp-export-btn" onclick="SNTPopup.exportCsv()">
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
      </button>
    </div>
  </div>
  <div class="sntp-list" id="sntPopList"></div>

  {{-- Detail Panel --}}
  <div id="sntDetailPanel">
    <div class="sntdp-header">
      <button class="sntdp-back" onclick="SNTDetail.close()">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="sntdp-title" id="sntDpTitle">Detail</span>
      <button class="sntdp-close" onclick="SNTPopup.close()">×</button>
    </div>
    <div class="sntdp-body" id="sntDpBody"></div>
  </div>
</div>

{{-- Platform Picker (untuk Social Media klik) --}}
<div id="sntPlatPicker">
  <div class="sntpp-header">Pilih Platform</div>
  <button class="sntpp-btn" onclick="SNTPopup.openPlatform('twit','all')">X / Twitter <span class="sntpp-dot" style="background:#1d9bf0;"></span></button>
  <button class="sntpp-btn" onclick="SNTPopup.openPlatform('fb','all')">Facebook <span class="sntpp-dot" style="background:#1877f2;"></span></button>
  <button class="sntpp-btn" onclick="SNTPopup.openPlatform('ig','all')">Instagram <span class="sntpp-dot" style="background:#e1306c;"></span></button>
  <button class="sntpp-btn" onclick="SNTPopup.openPlatform('yt','all')">YouTube <span class="sntpp-dot" style="background:#ff0000;"></span></button>
  <button class="sntpp-btn" onclick="SNTPopup.openPlatform('tiktok','all')">TikTok <span class="sntpp-dot" style="background:#111827;"></span></button>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
const SNTCfg = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
  media: '{{ $media }}',
  colors: { neg:'#ef4444', pos:'#2FC6F6', neu:'#94a3b8' },
};

/* ── UTILS ── */
const numFmt = n => parseInt(n||0).toLocaleString('id-ID');
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const pct    = (v,t) => t>0 ? ((v/t)*100).toFixed(1)+'%' : '0%';
const emptyHtml = msg => `<div class="snt-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="snt-empty-text">${msg}</span></div>`;

/* ── ECHARTS REGISTRY ── */
const SNTCharts = {
  _i: {},
  make(id) {
    if (this._i[id]) { try { this._i[id].dispose(); } catch(e){} }
    const dom = document.getElementById(id);
    if (!dom) return null;
    const c = echarts.init(dom, null, { renderer:'canvas' });
    this._i[id] = c;
    return c;
  },
  disposeAll() { Object.values(this._i).forEach(c=>{ try{c.dispose();}catch(e){} }); this._i={}; }
};
window.addEventListener('resize', () => {
  Object.values(SNTCharts._i).forEach(c=>{ try{ if(!c.isDisposed()) c.resize(); }catch(e){} });
});

const EC_TIP = {
  backgroundColor:'#1a202c', borderColor:'#334155', borderWidth:1,
  padding:[10,14], textStyle:{color:'#fff',fontFamily:"'Poppins',sans-serif",fontSize:12},
  extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
};

/* ═══════════════════════════════════════════════════════
   DATA STORE
═══════════════════════════════════════════════════════ */
const SNTData = {
  totals:   { neg:0, pos:0, neu:0 },
  byMedia:  [],
  trend:    [],
  weekday:  null,
  hour:     null,
};

/* ═══════════════════════════════════════════════════════
   LOAD — SENTIMENT TOTALS & PER MEDIA
═══════════════════════════════════════════════════════ */
async function loadSentiment() {
  if (!SNTCfg.pid) {
    ['skOverview','skSovTotal','skMass','skSocial','skByType','skByPlat','skMassPie','skSocialPie','skTrend','skWeekday','skHour'].forEach(hideSk);
    return;
  }

  try {
    const res  = await fetch(`/mk/api/sentiment/totals?project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}&media=${SNTCfg.media}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);

    SNTData.totals  = data.totals  || { neg:0, pos:0, neu:0 };
    SNTData.byMedia = data.by_media || [];
    SNTData.trend   = data.trend   || [];

    renderAll();
  } catch(err) {
    console.error('loadSentiment error:', err);
    ['skOverview','skSovTotal','skMass','skSocial','skByType','skByPlat','skMassPie','skSocialPie'].forEach(hideSk);
  }
}

async function loadTimeData() {
  if (!SNTCfg.pid) return;
  try {
    const res  = await fetch(`/mk/api/sentiment/by-time?project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    SNTData.weekday = data.weekday;
    SNTData.hour    = data.hour;
    renderWeekdayHour();
  } catch(err) {
    console.error('loadTimeData error:', err);
    ['skWeekday','skHour'].forEach(hideSk);
  }
}

/* ═══════════════════════════════════════════════════════
   RENDER ALL
═══════════════════════════════════════════════════════ */
function renderAll() {
  const { neg, pos, neu } = SNTData.totals;
  const tot = neg + pos + neu;

  // Stat cards
  document.getElementById('valNeg').textContent = numFmt(neg);
  document.getElementById('valPos').textContent = numFmt(pos);
  document.getElementById('valNeu').textContent = numFmt(neu);
  document.getElementById('valTot').textContent = numFmt(tot);
  document.getElementById('pctNeg').textContent = pct(neg, tot);
  document.getElementById('pctPos').textContent = pct(pos, tot);
  document.getElementById('pctNeu').textContent = pct(neu, tot);

  renderOverviewBar();
  renderSovDoughnut('chSovTotal', ['Negative','Positive','Neutral'], [neg,pos,neu], ['#ef4444','#2FC6F6','#94a3b8'], true);
  hideSk('skSovTotal');

  renderMassSocialBars();
  renderByTypePct();
  renderByPlatGrouped();
  renderMassSocialPies();
  renderPlatBreakdown();
  renderTrend();
}

/* ─── Overview stacked bar ─── */
function renderOverviewBar() {
  hideSk('skOverview');
  const { neg, pos, neu } = SNTData.totals;
  const tot = neg + pos + neu;
  if (tot === 0) { document.getElementById('chOverview').parentElement.innerHTML = emptyHtml('Tidak ada data sentimen'); return; }

  const chart = SNTCharts.make('chOverview');
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const p = params[0];
        const v = p.value;
        return `<div style="font-weight:700;font-size:13px;margin-bottom:6px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;">
                  <span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">${numFmt(v)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;">
                  <span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct(v,tot)}</span>
                </div>`;
      }
    },
    grid:{top:20,right:20,bottom:40,left:60},
    xAxis:{
      type:'category', data:['Negative','Positive','Neutral'],
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:13,fontWeight:'600',color:'#374151'}
    },
    yAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[{
      type:'bar', barMaxWidth:80,
      data:[
        {value:neg, itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:'#ef4444'},{offset:1,color:'#fca5a555'}]},borderRadius:[8,8,0,0]}},
        {value:pos, itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:'#2FC6F6'},{offset:1,color:'#7dd3fc55'}]},borderRadius:[8,8,0,0]}},
        {value:neu, itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:'#94a3b8'},{offset:1,color:'#cbd5e155'}]},borderRadius:[8,8,0,0]}},
      ],
      label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',formatter:p=>numK(p.value)}
    }]
  });
}

/* ─── SOV Doughnut ─── */
/* ================================================================
   PERUBAHAN: label slice sekarang tampilkan PERSENTASE (xx.x%)
   bukan count — konsisten dengan Drone Emprit
================================================================ */
function renderSovDoughnut(domId, labels, values, colors, ready=false) {
  const tot = values.reduce((a,b)=>a+b,0);
  const chart = SNTCharts.make(domId);
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:800, animationEasing:'cubicOut',
    backgroundColor:'transparent',
    tooltip:{
      ...EC_TIP, trigger:'item',
      formatter: p => {
        const pct2 = tot>0 ? ((p.value/tot)*100).toFixed(1) : '0.0';
        return `<div style="font-weight:700;font-size:13px;margin-bottom:5px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;">
                  <span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">${numFmt(p.value)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;">
                  <span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct2}%</span>
                </div>`;
      }
    },
    legend:{show:false},
    series:[{
      type:'pie', radius:['42%','62%'], center:['50%','50%'],
      avoidLabelOverlap:true, minAngle:5,
      itemStyle:{borderColor:'#fff',borderWidth:3,borderRadius:5},
      label:{
        show:true, alignTo:'edge', edgeDistance:10, lineHeight:18,
        fontFamily:"'Poppins',sans-serif", fontSize:11, color:'#374151',
        formatter: p => {
          const pc = tot>0?(p.value/tot*100):0;
          if(pc<3) return '';
          // ← DIUBAH: tampilkan persentase (xx.x%) bukan count
          return `{name|${p.name}}\n{pct|${pc.toFixed(1)}%}`;
        },
        rich:{
          name:{fontWeight:'700',fontSize:11,color:'#1a202c',lineHeight:18},
          // ← DIUBAH: style label pct (hijau badge)
          pct:{fontWeight:'700',fontSize:10,color:'#038047',lineHeight:16,backgroundColor:'#edf7f3',borderRadius:4,padding:[1,5]},
        }
      },
      labelLine:{show:true,length:12,length2:16,smooth:.4,lineStyle:{color:'#c4cdd8',width:1.2}},
      emphasis:{scale:true,scaleSize:5,itemStyle:{shadowBlur:10,shadowColor:'rgba(0,0,0,.12)'}},
      data: labels.map((l,i)=>({name:l,value:values[i],itemStyle:{color:colors[i]}}))
    }],
    graphic:[
      {type:'text',left:'center',top:'44%',z:100,style:{text:numK(tot),fill:'#0f172a',font:"800 24px 'Poppins',sans-serif",textAlign:'center'}},
      {type:'text',left:'center',top:'53%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"600 9px 'Poppins',sans-serif",textAlign:'center',letterSpacing:2}},
    ]
  });
}

/* ─── Mass vs Social stacked bars ─── */
function renderMassSocialBars() {
  const bm = SNTData.byMedia;

  const massPlt  = bm.filter(m => m.key === 'doc');
  const socialPlt = bm.filter(m => m.key !== 'doc');

  // ── MASS: tiap platform mass punya bar sendiri (biasanya hanya 1: Online News) ──
  hideSk('skMass');
  const massChart = SNTCharts.make('chMass');
  if (massChart) {
    const mLabels = massPlt.map(m => m.label);
    const mNeg    = massPlt.map(m => m.neg);
    const mPos    = massPlt.map(m => m.pos);
    const mNeu    = massPlt.map(m => m.neu);
    const mTot    = massPlt.map(m => m.neg+m.pos+m.neu);
    if (mLabels.length && mTot.some(v=>v>0)) {
      massChart.setOption(makeStackedBarOption(mLabels, mNeg, mPos, mNeu, mLabels, mTot));
    } else {
      document.getElementById('chMass').parentElement.innerHTML = emptyHtml('Tidak ada data Mass Media');
    }
  }

  // ── SOCIAL: tiap platform punya bar stacked sendiri ──
  hideSk('skSocial');
  const socialChart = SNTCharts.make('chSocial');
  if (socialChart) {
    const sLabels = socialPlt.map(m => m.label);
    const sNeg    = socialPlt.map(m => m.neg);
    const sPos    = socialPlt.map(m => m.pos);
    const sNeu    = socialPlt.map(m => m.neu);
    const sTot    = socialPlt.map(m => m.neg+m.pos+m.neu);
    if (sLabels.length && sTot.some(v=>v>0)) {
      socialChart.setOption(makeStackedBarOption(sLabels, sNeg, sPos, sNeu, sLabels, sTot));
    } else {
      document.getElementById('chSocial').parentElement.innerHTML = emptyHtml('Tidak ada data Social Media');
    }
  }
}

function makeStackedBarOption(xLabels, negData, posData, neuData, tooltipLabels, totals) {
  const makeData = (arr, col) => arr.map((v,i)=>({ value:v, itemStyle:{color:col} }));

  return {
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const idx = params[0]?.dataIndex ?? 0;
        const tot = totals[idx]||0;
        const rows = params.slice().reverse().map(p =>
          `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
            <div style="display:flex;align-items:center;gap:6px;">
              <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0;"></span>
              <span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span>
            </div>
            <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
          </div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${xLabels[idx]||''}</div>
                ${rows}
                <div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;gap:16px;">
                  <span style="font-size:11px;color:#94a3b8;">Total</span>
                  <span style="font-weight:700;">${numFmt(tot)}</span>
                </div>`;
      }
    },
    grid:{top:28,right:16,bottom:36,left:60},
    xAxis:{
      type:'category', data:xLabels,
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b',interval:0,
        formatter:v=>v.length>11?v.slice(0,10)+'…':v}
    },
    yAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[
      { name:'Neutral',  type:'bar', stack:'s', data:makeData(neuData,'#94a3b8'), itemStyle:{borderRadius:[0,0,0,0]} },
      { name:'Positive', type:'bar', stack:'s', data:makeData(posData,'#2FC6F6') },
      {
        name:'Negative', type:'bar', stack:'s', barMaxWidth:80,
        data:negData.map((v,i)=>({ value:v, itemStyle:{color:'#ef4444',borderRadius:[6,6,0,0]} })),
        label:{
          show:true, position:'top',
          fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',
          formatter: p => totals[p.dataIndex]>0?numK(totals[p.dataIndex]):''
        }
      },
    ]
  };
}

/* ─── By Media Type % (horizontal stacked) ─── */
function renderByTypePct() {
  hideSk('skByType');
  const bm = SNTData.byMedia;
  if (!bm.length) { document.getElementById('chByType').parentElement.innerHTML = emptyHtml('Tidak ada data'); return; }

  const chart = SNTCharts.make('chByType');
  if (!chart) return;

  const labels  = bm.map(m => m.label);
  const negPcts = bm.map(m => { const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.neg/t*100).toFixed(1)):0; });
  const posPcts = bm.map(m => { const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.pos/t*100).toFixed(1)):0; });
  const neuPcts = bm.map(m => { const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.neu/t*100).toFixed(1)):0; });

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow'},
      formatter: params => {
        const idx = params[0]?.dataIndex ?? 0;
        const m   = bm[idx]; if(!m) return '';
        const tot = m.neg+m.pos+m.neu;
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${m.label}</div>
                ${[['Negative','#ef4444',m.neg],['Positive','#2FC6F6',m.pos],['Neutral','#94a3b8',m.neu]].map(([n,c,v])=>
                  `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
                    <div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${c};"></span><span style="font-size:12px;color:#94a3b8;">${n}</span></div>
                    <div style="display:flex;gap:10px;"><span style="font-size:12px;font-weight:700;">${numFmt(v)}</span><span style="font-size:10px;color:#94a3b8;">${tot>0?(v/tot*100).toFixed(1):'0'}%</span></div>
                  </div>`).join('')}`;
      }
    },
    legend:{
      bottom:0, data:['Negative','Positive','Neutral'],
      textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},
      icon:'circle', itemWidth:10, itemHeight:10, itemGap:20,
    },
    grid:{top:12,right:16,bottom:50,left:100},
    xAxis:{
      type:'value', max:100, min:0,
      axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:v=>v+'%'}
    },
    yAxis:{
      type:'category', data:labels, inverse:false,
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#374151',margin:10}
    },
    series:[
      {name:'Negative',type:'bar',stack:'pct',data:negPcts,itemStyle:{color:'#ef4444'},barMaxWidth:30,
       emphasis:{focus:'series'},label:{show:posPcts.length<=5,position:'inside',formatter:p=>p.value>5?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:9,fontWeight:'700',color:'#fff'}},
      {name:'Positive',type:'bar',stack:'pct',data:posPcts,itemStyle:{color:'#2FC6F6'},barMaxWidth:30,
       emphasis:{focus:'series'},label:{show:posPcts.length<=5,position:'inside',formatter:p=>p.value>5?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:9,fontWeight:'700',color:'#fff'}},
      {name:'Neutral', type:'bar',stack:'pct',data:neuPcts,itemStyle:{color:'#94a3b8',borderRadius:[0,4,4,0]},barMaxWidth:30,
       emphasis:{focus:'series'}},
    ]
  });
}

/* ─── By Platform Grouped Bars (Mass vs Social %) ─── */
function renderByPlatGrouped() {
  hideSk('skByPlat');
  const bm = SNTData.byMedia;
  if (!bm.length) { document.getElementById('chByPlat').parentElement.innerHTML = emptyHtml('Tidak ada data'); return; }

  const chart = SNTCharts.make('chByPlat');
  if (!chart) return;

  const massDat  = bm.filter(m=>m.key==='doc');
  const socialDat= bm.filter(m=>m.key!=='doc');

  const groups = ['Mass Media','Social Media'];
  const mNeg   = massDat.reduce((s,m)=>s+m.neg,0);
  const mPos   = massDat.reduce((s,m)=>s+m.pos,0);
  const mNeu   = massDat.reduce((s,m)=>s+m.neu,0);
  const mTot   = mNeg+mPos+mNeu;
  const sNeg   = socialDat.reduce((s,m)=>s+m.neg,0);
  const sPos   = socialDat.reduce((s,m)=>s+m.pos,0);
  const sNeu   = socialDat.reduce((s,m)=>s+m.neu,0);
  const sTot   = sNeg+sPos+sNeu;

  const negPct = [mTot>0?(mNeg/mTot*100).toFixed(1):0, sTot>0?(sNeg/sTot*100).toFixed(1):0];
  const posPct = [mTot>0?(mPos/mTot*100).toFixed(1):0, sTot>0?(sPos/sTot*100).toFixed(1):0];
  const neuPct = [mTot>0?(mNeu/mTot*100).toFixed(1):0, sTot>0?(sNeu/sTot*100).toFixed(1):0];

  chart.setOption({
    animation:true, animationDuration:900,
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow'},
      formatter: params => {
        const idx = params[0]?.dataIndex ?? 0;
        const lbl = groups[idx];
        const neg = [mNeg,sNeg][idx], pos=[mPos,sPos][idx], neu=[mNeu,sNeu][idx], tot=[mTot,sTot][idx];
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;">${lbl}</div>
                ${[['Negative','#ef4444',neg],['Positive','#2FC6F6',pos],['Neutral','#94a3b8',neu]].map(([n,c,v])=>
                  `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
                    <div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${c};"></span><span style="font-size:12px;color:#94a3b8;">${n}</span></div>
                    <span style="font-size:12px;font-weight:700;">${numFmt(v)} <span style="color:#94a3b8;font-size:10px;">(${tot>0?(v/tot*100).toFixed(1):'0'}%)</span></span>
                  </div>`).join('')}`;
      }
    },
    legend:{
      bottom:0, data:['Negative','Positive','Neutral'],
      textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},
      icon:'circle', itemWidth:10, itemHeight:10, itemGap:20,
    },
    grid:{top:24,right:16,bottom:50,left:72},
    xAxis:{
      type:'category', data:groups,
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:13,fontWeight:'700',color:'#374151'}
    },
    yAxis:{
      type:'value', max:100, min:0,
      axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:v=>v+'%'}
    },
    series:[
      {name:'Neutral', type:'bar',stack:'s',data:neuPct.map(v=>parseFloat(v)),itemStyle:{color:'#94a3b8'},barMaxWidth:90,emphasis:{focus:'series'}},
      {name:'Positive',type:'bar',stack:'s',data:posPct.map(v=>parseFloat(v)),itemStyle:{color:'#2FC6F6'},barMaxWidth:90,emphasis:{focus:'series'},
       label:{show:true,position:'inside',formatter:p=>p.value>8?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'700',color:'#fff'}},
      {name:'Negative',type:'bar',stack:'s',data:negPct.map(v=>parseFloat(v)),itemStyle:{color:'#ef4444',borderRadius:[4,4,0,0]},barMaxWidth:90,emphasis:{focus:'series'},
       label:{show:true,position:'inside',formatter:p=>p.value>8?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'700',color:'#fff'}},
    ]
  });
}

/* ─── Mass Pie + Social Pie ─── */
function renderMassSocialPies() {
  const bm = SNTData.byMedia;

  const massDat = bm.filter(m=>m.key==='doc');
  const mN=massDat.reduce((s,m)=>s+m.neg,0), mP=massDat.reduce((s,m)=>s+m.pos,0), mNe=massDat.reduce((s,m)=>s+m.neu,0);
  hideSk('skMassPie');
  if(mN+mP+mNe>0) renderSovDoughnut('chMassPie',['Negative','Positive','Neutral'],[mN,mP,mNe],['#ef4444','#2FC6F6','#94a3b8']);
  else document.getElementById('chMassPie').parentElement.innerHTML = emptyHtml('Tidak ada data Mass Media');

  const sD = bm.filter(m=>m.key!=='doc');
  const sN=sD.reduce((s,m)=>s+m.neg,0), sP=sD.reduce((s,m)=>s+m.pos,0), sNe=sD.reduce((s,m)=>s+m.neu,0);
  hideSk('skSocialPie');
  if(sN+sP+sNe>0) renderSovDoughnut('chSocialPie',['Negative','Positive','Neutral'],[sN,sP,sNe],['#ef4444','#2FC6F6','#94a3b8']);
  else document.getElementById('chSocialPie').parentElement.innerHTML = emptyHtml('Tidak ada data Social Media');
}

/* ─── Platform Breakdown List ─── */
function renderPlatBreakdown() {
  const list = document.getElementById('platBreakdownList');
  const bm   = SNTData.byMedia;
  if (!bm.length) { list.innerHTML = emptyHtml('Tidak ada data per platform'); return; }

  const platIcons = {
    doc:       `<svg viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>`,
    twitter:   `<svg viewBox="0 0 24 24" fill="#1d9bf0" stroke="none" style="width:18px;height:18px;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`,
    facebook:  `<svg viewBox="0 0 24 24" fill="none" stroke="#1877f2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>`,
    instagram: `<svg viewBox="0 0 24 24" fill="none" stroke="#e1306c" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>`,
    youtube:   `<svg viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>`,
    tiktok:    `<svg viewBox="0 0 24 24" fill="#111827" stroke="none" style="width:18px;height:18px;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/></svg>`,
  };
  const platBg = {doc:'rgba(2,132,199,.1)',twitter:'rgba(29,155,240,.1)',facebook:'rgba(24,119,242,.1)',instagram:'rgba(225,48,108,.1)',youtube:'rgba(255,0,0,.08)',tiktok:'rgba(17,24,39,.07)'};

  list.innerHTML = bm.map(m => {
    const tot = m.neg+m.pos+m.neu;
    const np  = tot>0?((m.neg/tot)*100):0;
    const pp  = tot>0?((m.pos/tot)*100):0;
    const nep = tot>0?((m.neu/tot)*100):0;
    return `<div class="snt-media-row">
      <div class="snt-media-icon" style="background:${platBg[m.key]||'#f1f5f9'};">${platIcons[m.key]||''}</div>
      <div class="snt-media-name">${m.label}</div>
      <div class="snt-media-bars">
        <div class="snt-media-bar-row">
          <div class="snt-media-bar-track"><div class="snt-media-bar-fill" style="width:${np.toFixed(1)}%;background:#ef4444;"></div></div>
          <div class="snt-media-bar-val" style="color:#ef4444;">${numK(m.neg)}</div>
        </div>
        <div class="snt-media-bar-row">
          <div class="snt-media-bar-track"><div class="snt-media-bar-fill" style="width:${pp.toFixed(1)}%;background:#2FC6F6;"></div></div>
          <div class="snt-media-bar-val" style="color:#2FC6F6;">${numK(m.pos)}</div>
        </div>
        <div class="snt-media-bar-row">
          <div class="snt-media-bar-track"><div class="snt-media-bar-fill" style="width:${nep.toFixed(1)}%;background:#94a3b8;"></div></div>
          <div class="snt-media-bar-val" style="color:#94a3b8;">${numK(m.neu)}</div>
        </div>
      </div>
      <div class="snt-media-total">${numFmt(tot)}</div>
    </div>`;
  }).join('');
}

/* ─── Trend Line ─── */
function renderTrend() {
  hideSk('skTrend');
  const trend = SNTData.trend;
  if (!trend.length) {
    document.getElementById('trendBadge').textContent='No Data';
    document.getElementById('chTrend').parentElement.innerHTML=emptyHtml('Data trend tidak tersedia untuk periode ini');
    return;
  }

  const dates   = trend.map(d=>d.date);
  const xLabels = dates.map(d=>{ const dt=new Date(d+'T00:00:00'); return `${dt.getDate()}.${dt.toLocaleString('id-ID',{month:'short'})}`; });
  const negVals = trend.map(d=>d.neg||0);
  const posVals = trend.map(d=>d.pos||0);
  const neuVals = trend.map(d=>d.neu||0);

  const fmtB = d=>{ const dt=new Date(d+'T00:00:00'); return `${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`; };
  document.getElementById('trendBadge').textContent = `${fmtB(dates[0])} – ${fmtB(dates[dates.length-1])}`;

  const chart = SNTCharts.make('chTrend');
  if (!chart) return;

  const makeSeries = (name, data, color) => ({
    name, type:'line', data, smooth:.4,
    symbol:'circle', symbolSize:dates.length<=30?6:0, showSymbol:dates.length<=30,
    itemStyle:{color,borderColor:'#fff',borderWidth:2},
    lineStyle:{color,width:2.5},
    areaStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:color+'22'},{offset:1,color:color+'02'}]}},
    emphasis:{focus:'series',lineStyle:{width:3.5},itemStyle:{symbolSize:10,shadowBlur:10,shadowColor:color+'88'}},
    label:{show:dates.length<=14,position:'top',formatter:p=>p.value>0?numFmt(p.value):'',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:10,color:'#64748b'}
  });

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'cubicInOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'line',lineStyle:{color:'#e2e8f0',type:'dashed',width:1.5}},
      formatter: params => {
        const di   = params[0]?.dataIndex ?? 0;
        const date = dates[di]||'';
        const fullDt = date ? new Date(date+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'}) : '';
        const tot  = (params[0]?.value||0)+(params[1]?.value||0)+(params[2]?.value||0);
        const rows = params.map(p=>
          `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
            <div style="display:flex;align-items:center;gap:6px;">
              <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};"></span>
              <span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span>
            </div>
            <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
          </div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:4px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">
                  ${fullDt||date}
                </div>${rows}
                <div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;">
                  <span style="font-size:11px;color:#94a3b8;">Total</span><span style="font-weight:700;">${numFmt(tot)}</span>
                </div>`;
      }
    },
    legend:{
      bottom:0, data:['Negative','Positive','Neutral'],
      textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},
      icon:'circle', itemWidth:10, itemHeight:10, itemGap:24,
    },
    grid:{top:28,right:20,bottom:50,left:64},
    xAxis:{
      type:'category', data:xLabels, boundaryGap:false,
      axisLine:{lineStyle:{color:'#e2e8f0'}}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}
    },
    yAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'solid',width:1}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[
      makeSeries('Negative',negVals,'#ef4444'),
      makeSeries('Positive',posVals,'#2FC6F6'),
      makeSeries('Neutral', neuVals,'#94a3b8'),
    ]
  });
}

/* ─── Weekday & Hour ─── */
function renderWeekdayHour() {
  renderTimeChart('chWeekday','skWeekday', SNTData.weekday?.weekdays||[], SNTData.weekday?.neg||[], SNTData.weekday?.pos||[], SNTData.weekday?.neu||[], SNTData.weekday?.total||[], false);
  renderTimeChart('chHour','skHour', SNTData.hour?.hours||[], SNTData.hour?.neg||[], SNTData.hour?.pos||[], SNTData.hour?.neu||[], SNTData.hour?.total||[], true);
}

function renderTimeChart(domId, skelId, labels, negData, posData, neuData, totals, isHour=false) {
  hideSk(skelId);
  if (!labels.length || !totals.some(v=>v>0)) {
    document.getElementById(domId).parentElement.innerHTML = emptyHtml('Data tidak tersedia untuk periode ini');
    return;
  }

  const chart = SNTCharts.make(domId);
  if (!chart) return;

  const makeS = (name,data,color) => ({
    name, type:'bar', stack:'s',
    data: data.map(v=>({value:v,itemStyle:{color,borderRadius:[0,0,0,0]}})),
    emphasis:{focus:'series'}
  });

  chart.setOption({
    animation:true, animationDuration:800, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const idx = params[0]?.dataIndex ?? 0;
        const lbl = labels[idx]||'';
        const tot = totals[idx]||0;
        const rows = [...params].reverse().map(p=>
          `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
            <div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};"></span><span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span></div>
            <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
          </div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${isHour?'Jam ':''}${lbl}</div>
                ${rows}
                <div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;">
                  <span style="font-size:11px;color:#94a3b8;">Total</span><span style="font-weight:700;">${numFmt(tot)}</span>
                </div>`;
      }
    },
    grid:{top:24,right:16,bottom:40,left:56},
    xAxis:{
      type:'category', data:labels,
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:isHour?9:11,fontWeight:'600',color:'#64748b',
        interval:isHour?1:0, rotate:isHour?45:0}
    },
    yAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[
      makeS('Neutral', neuData,'#94a3b8'),
      makeS('Positive',posData,'#2FC6F6'),
      {
        name:'Negative', type:'bar', stack:'s', barMaxWidth:isHour?20:56,
        data:negData.map((v,i)=>({value:v,itemStyle:{color:'#ef4444',borderRadius:[4,4,0,0]}})),
        label:{
          show:true, position:'top',
          fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:isHour?9:10,color:'#64748b',
          formatter:p=>totals[p.dataIndex]>0?numK(totals[p.dataIndex]):''
        },
        emphasis:{focus:'series'}
      }
    ]
  });
}

/* ═══════════════════════════════════════════════════════
   CSV EXPORT
═══════════════════════════════════════════════════════ */
const SNTCsv = {
  _copy(text) {
    navigator.clipboard?.writeText(text).catch(()=>{
      const ta=document.createElement('textarea'); ta.value=text;
      ta.style.cssText='position:fixed;opacity:0;'; document.body.appendChild(ta);
      ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
    });
    alert('CSV data tersalin ke clipboard!');
  },
  copyOverview() {
    const {neg,pos,neu}=SNTData.totals; const tot=neg+pos+neu;
    const lines=['sentiment;count;percentage',
      `Negative;${neg};${tot>0?(neg/tot*100).toFixed(1):0}`,
      `Positive;${pos};${tot>0?(pos/tot*100).toFixed(1):0}`,
      `Neutral;${neu};${tot>0?(neu/tot*100).toFixed(1):0}`,
      `Total;${tot};100`,
    ];
    this._copy(lines.join('\n'));
  },
  copyMass() {
    const bm=SNTData.byMedia.filter(m=>m.key==='doc');
    const lines=['platform;negative;positive;neutral;total'];
    bm.forEach(m=>lines.push(`${m.label};${m.neg};${m.pos};${m.neu};${m.neg+m.pos+m.neu}`));
    this._copy(lines.join('\n'));
  },
  copySocial() {
    const bm=SNTData.byMedia.filter(m=>m.key!=='doc');
    const lines=['platform;negative;positive;neutral;total'];
    bm.forEach(m=>lines.push(`${m.label};${m.neg};${m.pos};${m.neu};${m.neg+m.pos+m.neu}`));
    this._copy(lines.join('\n'));
  },
  copyTrend() {
    const lines=['date;negative;positive;neutral;total'];
    SNTData.trend.forEach(d=>lines.push(`${d.date};${d.neg};${d.pos};${d.neu};${d.neg+d.pos+d.neu}`));
    this._copy(lines.join('\n'));
  }
};

/* ═══════════════════════════════════════════════════════
   PAGE CONTROLLER
═══════════════════════════════════════════════════════ */
const SNTPage = {
  reload() {
    SNTCharts.disposeAll();
    SNTData.trend=[];SNTData.byMedia=[];SNTData.weekday=null;SNTData.hour=null;
    loadSentiment();
    loadTimeData();
  },
  init() {
    loadSentiment();
    loadTimeData();
  }
};

document.addEventListener('DOMContentLoaded', () => SNTPage.init());

/* ══════════════════════════════════════════════════════
   SENTIMENT MENTION POPUP
══════════════════════════════════════════════════════ */

const SNTPlatMeta = {
  doc:    { label:'Online News',  color:'#0284c7' },
  twit:   { label:'X / Twitter', color:'#0ea5e9' },
  fb:     { label:'Facebook',    color:'#1877f2' },
  ig:     { label:'Instagram',   color:'#e1306c' },
  yt:     { label:'YouTube',     color:'#ff0000' },
  tiktok: { label:'TikTok',      color:'#111827' },
  neg:    { label:'Negative',    color:'#ef4444' },
  pos:    { label:'Positive',    color:'#2FC6F6' },
  neu:    { label:'Neutral',     color:'#94a3b8' },
};

/* ── ESC helper ── */
const sntEsc = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

/* ══════════════════════════════════════════════════════
   SENTIMENT POPUP
══════════════════════════════════════════════════════ */
const SNTPopup = {
  _cache:{}, _allItems:[], _curSent:'all', _curPlat:null,

  init() {
    document.addEventListener('mousedown', e => {
      const pp = document.getElementById('sntPlatPicker');
      if (pp?.classList.contains('visible') && !pp.contains(e.target)) pp.classList.remove('visible');
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') this.close();
    });
  },

  async open(platform, sentiment) {
    const popup   = document.getElementById('sntPopup');
    const overlay = document.getElementById('sntPanelOverlay');
    if (!popup) return;

    SNTDetail.close();
    this._curPlat = platform;
    this._curSent = sentiment || 'all';

    let dotColor, title;
    if (sentiment && sentiment !== 'all') {
      dotColor = SNTPlatMeta[sentiment]?.color || '#038047';
      const sentLabel = { neg:'Negative', pos:'Positive', neu:'Neutral' }[sentiment] || sentiment;
      const platLabel = platform === 'all' ? 'All Media'
        : platform === 'social' ? 'Social Media'
        : (SNTPlatMeta[platform]?.label || platform);
      title = `${sentLabel} — ${platLabel}`;
    } else {
      dotColor = platform === 'all' ? '#038047'
        : platform === 'social' ? '#038047'
        : (SNTPlatMeta[platform]?.color || '#038047');
      title = platform === 'all' ? 'All Media'
        : platform === 'social' ? 'Social Media'
        : (SNTPlatMeta[platform]?.label || platform);
    }

    document.getElementById('sntPopDot').style.background = dotColor;
    document.getElementById('sntPopTitle').textContent     = title;
    document.getElementById('sntPopMeta').textContent      = SNTCfg.sd + ' – ' + SNTCfg.ed;
    document.getElementById('sntPopCount').textContent     = '…';

    this._curSent = sentiment || 'all';
    document.querySelectorAll('.sntp-sent-tab').forEach(b => {
      b.classList.toggle('active', b.dataset.s === this._curSent);
    });

    const list = document.getElementById('sntPopList');
    list.innerHTML = `<div class="sntp-loading"><div class="sntp-spinner"></div>Memuat mentions…</div>`;

    // Slide-in panel + overlay
    popup.classList.remove('hiding');
    popup.classList.add('show');
    if (overlay) { overlay.classList.remove('hiding'); overlay.classList.add('show'); }
    document.body.style.overflow = 'hidden';

    const cacheKey = `${SNTCfg.pid}_${platform}_${SNTCfg.sd}_${SNTCfg.ed}`;
    try {
      if (!this._cache[cacheKey]) {
        this._cache[cacheKey] = await this._fetch(platform);
      }
      this._allItems = this._cache[cacheKey];
      this._renderFiltered(list);
    } catch(err) {
      list.innerHTML = `<div class="sntp-loading" style="color:#94a3b8;">
        <svg style="width:32px;height:32px;stroke:#e2e8f0;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        Gagal memuat data
      </div>`;
      document.getElementById('sntPopCount').textContent = '0';
    }
  },

  openSentiment(sentiment) {
    this.open('all', sentiment);
  },

  showPlatPicker(x, y, sentiment) {
    const pp = document.getElementById('sntPlatPicker');
    if (!pp) return;
    pp.dataset.sentiment = sentiment || 'all';
    const pw=185, ph=240, vw=window.innerWidth, vh=window.innerHeight;
    let left=x+10, top=y-10;
    if (left+pw > vw-8) left = x-pw-10;
    if (top+ph  > vh-8) top  = vh-ph-8;
    if (top < 8) top = 8;
    pp.style.left = left+'px';
    pp.style.top  = top+'px';
    pp.classList.add('visible');

    pp.querySelectorAll('.sntpp-btn').forEach(btn => {
      const plat = btn.getAttribute('onclick').match(/'([^']+)'/)?.[1];
      if (plat) {
        btn.setAttribute('onclick', `SNTPopup.openPlatform('${plat}','${sentiment||'all'}')`);
      }
    });
  },

  openPlatform(platform, sentiment) {
    const pp = document.getElementById('sntPlatPicker');
    if (pp) pp.classList.remove('visible');
    this.open(platform, sentiment||'all');
  },

  filterSent(sent) {
    this._curSent = sent;
    document.querySelectorAll('.sntp-sent-tab').forEach(b => {
      b.classList.toggle('active', b.dataset.s === sent);
    });
    const list = document.getElementById('sntPopList');
    this._renderFiltered(list);
  },

  close() {
    const popup   = document.getElementById('sntPopup');
    const overlay = document.getElementById('sntPanelOverlay');
    if (!popup || !popup.classList.contains('show')) return;
    SNTDetail.close();
    popup.classList.add('hiding');
    if (overlay) overlay.classList.add('hiding');
    setTimeout(() => {
      popup.classList.remove('show','hiding');
      if (overlay) overlay.classList.remove('show','hiding');
      document.body.style.overflow = '';
    }, 240);
  },

  exportCsv() {
    const items = this._getFiltered();
    if (!items.length) { alert('Tidak ada data untuk diekspor.'); return; }
    const platLabel = { doc:'Online_News', twit:'Twitter', fb:'Facebook', ig:'Instagram', yt:'YouTube', tiktok:'TikTok', all:'All_Media', social:'Social_Media' };
    const rows = items.map((item, idx) => {
      const name    = (item.author_name||item.channel_name||item.publisher||item.source_name||item.name||item.author_scr_name||item.screen_name||'').trim();
      const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,500);
      const sentRaw = String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
      const sent    = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif'?'Positif':sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'||sentRaw==='negatif'?'Negatif':'Netral';
      const url     = item.url||item.link||'';
      const date    = (item.date_created||item.created_at||'').split('T')[0];
      const esc2    = s => String(s||'').replace(/;/g,',').replace(/\n/g,' ');
      return `${idx};${esc2(name)};${esc2(sent)};${esc2(date)};${esc2(url)};${esc2(content)}`;
    });
    const header = 'index;nama;sentimen;tanggal;url;konten';
    const csv    = [header,...rows].join('\r\n');
    const blob   = new Blob(['\uFEFF'+csv], { type:'text/csv;charset=utf-8;' });
    const url    = URL.createObjectURL(blob);
    const a      = document.createElement('a');
    const lbl    = platLabel[this._curPlat] || this._curPlat;
    const snt    = this._curSent !== 'all' ? `_${this._curSent}` : '';
    a.href = url; a.download = `sentiment_${lbl}${snt}_${SNTCfg.sd}_${SNTCfg.ed}.csv`;
    a.click(); URL.revokeObjectURL(url);
  },

  async _fetch(platform) {
    const q = `project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}&rows=500&start=0`;

    if (platform === 'social') {
      const socials = ['twit','fb','ig','yt','tiktok'];
      const results = await Promise.allSettled(socials.map(p => this._fetchOne(p, q)));
      let merged = [];
      results.forEach(r => { if (r.status==='fulfilled') merged = merged.concat(r.value); });
      return merged;
    }

    if (platform === 'all') {
      const all = ['doc','twit','fb','ig','yt','tiktok'];
      const results = await Promise.allSettled(all.map(p => this._fetchOne(p, q)));
      let merged = [];
      results.forEach(r => { if (r.status==='fulfilled') merged = merged.concat(r.value); });
      merged.sort((a,b) => {
        const da = a.date_created||a.created_at||'';
        const db = b.date_created||b.created_at||'';
        return db.localeCompare(da);
      });
      return merged;
    }

    return this._fetchOne(platform, q);
  },

  async _fetchOne(platform, q) {
    if (platform === 'ig') {
      const subs = ['postbylike','postbycomment','postbydate',''];
      for (const sub of subs) {
        const url = `/mk/api/news/ig-top-status?${q}${sub?'&sub='+sub:''}`;
        try {
          const ctrl = new AbortController(), tid = setTimeout(()=>ctrl.abort(),15000);
          const res  = await fetch(url,{signal:ctrl.signal}); clearTimeout(tid);
          if (!res.ok) continue;
          const data  = await res.json();
          const items = Array.isArray(data.data)?data.data:(Array.isArray(data)?data:[]);
          if (items.length>0) return items;
        } catch(e){ continue; }
      }
      return [];
    }

    const eps = {
      doc:    `/mk/api/news/mentions?${q}`,
      twit:   `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
      fb:     `/mk/api/news/fb-top-status?${q}&sub=fblike`,
      yt:     `/mk/api/news/ytb-top-status?${q}`,
      tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
    };
    const url  = eps[platform]; if(!url) return [];
    const ctrl = new AbortController(), tid = setTimeout(()=>ctrl.abort(),30000);
    const res  = await fetch(url,{signal:ctrl.signal}); clearTimeout(tid);
    if (!res.ok) return [];
    const data = await res.json();
    let items  = Array.isArray(data.data)?data.data:(Array.isArray(data)?data:[]);

    if (platform==='doc') {
      items = items.filter(m => {
        const tc=String(m.tcode||'').toLowerCase();
        const mt=String(m.media_type||'').toLowerCase();
        return tc==='berita'||mt==='berita'||mt==='doc'||mt==='news'||mt==='online'||mt==='article';
      });
    }
    return items;
  },

  _normSent(item) {
    const raw = String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
    if (raw==='1'||raw==='positive'||raw==='positif') return 'pos';
    if (raw==='-1'||raw==='2'||raw==='negative'||raw==='negatif') return 'neg';
    return 'neu';
  },

  _getFiltered() {
    if (this._curSent === 'all') return this._allItems;
    return this._allItems.filter(item => this._normSent(item) === this._curSent);
  },

  _renderFiltered(list) {
    const items   = this._getFiltered();
    document.getElementById('sntPopCount').textContent = items.length.toLocaleString();

    const badge = document.getElementById('sntPopCount');
    const bColors = { neg:'#ef4444', pos:'#2FC6F6', neu:'#94a3b8', all:'var(--primary-green)' };
    badge.style.background = bColors[this._curSent] || 'var(--primary-green)';

    this._render(list, items);
  },

  _render(list, items) {
    if (!items.length) {
      list.innerHTML = `<div class="sntp-loading" style="color:#94a3b8;">
        <svg style="width:32px;height:32px;stroke:#e2e8f0;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        Tidak ada mention untuk filter ini
      </div>`;
      return;
    }

    const SHOW = 60;
    const getPlat = item => {
      const mt = String(item.media_type||item.type||item.tcode||'').toLowerCase();
      if (mt.includes('doc')||mt.includes('news')||mt.includes('berita')) return 'doc';
      if (mt.includes('twit')||mt.includes('twitter')||mt.includes('x')) return 'twit';
      if (mt.includes('fb')||mt.includes('facebook')) return 'fb';
      if (mt.includes('ig')||mt.includes('instagram')) return 'ig';
      if (mt.includes('yt')||mt.includes('youtube')) return 'yt';
      if (mt.includes('tiktok')) return 'tiktok';
      return this._curPlat || 'doc';
    };

    list.innerHTML = items.slice(0, SHOW).map(item => {
      const plat = getPlat(item);
      const meta = SNTPlatMeta[plat] || { color:'#038047' };
      const color = meta.color;

      const name = (
        item.from_name||item.page_name||item.author_nickname||item.nickname||
        item.channel_title||item.channel_name||
        item.author_name||item.username||item.user_name||
        item.author_scr_name||item.screen_name||
        item.publisher||item.source_name||item.name||'Tidak diketahui'
      ).trim();

      const isNumericId = /^\d{8,}$/.test(name);
      const displayName = isNumericId ? `User ${name.slice(-4)}` : name;

      const rawHandle = (item.author_scr_name||item.screen_name||item.username||item.handle||'').trim();
      const handle    = rawHandle && rawHandle.toLowerCase()!==displayName.toLowerCase()
        ? (['twit','ig','tiktok'].includes(plat)
            ? (rawHandle.startsWith('@')?rawHandle:'@'+rawHandle)
            : rawHandle)
        : '';

      const text = (item.content||item.caption||item.description||item.title||item.text||'')
        .replace(/<[^>]*>/g,'').trim().slice(0,155);

      const av = (item.avatar_url||item.profile_image_url||item.author_image||
                  item.profile_image||item.thumbnail||item.picture||'').trim();

      const words = displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
      const ini   = words.length>=2
        ? (words[0][0]+words[words.length-1][0]).toUpperCase()
        : (words[0]?.[0]||displayName[0]||'?').toUpperCase();
      const safeIni = ini.replace(/['"]/g,'');

      const avHtml = (av&&(av.startsWith('http://')||av.startsWith('https://')))
        ? `<img src="${sntEsc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`
        : ini;

      const sent    = this._normSent(item);
      const sentLbl = { neg:'Neg', pos:'Pos', neu:'Neu' }[sent] || 'Neu';

      const dt = (item.date_created||item.created_at||item.publish_date||'').split('T')[0];

      const eng = (() => {
        const f = n => parseInt(n)||0>0?parseInt(n).toLocaleString():null;
        const parts=[];
        if (plat==='twit') { const rt=f(item.num_retweeted||item.retweet_count),lk=f(item.num_likes||item.favorite_count); if(rt)parts.push('RT '+rt); if(lk)parts.push('Like '+lk); }
        else if(plat==='yt')     { const v=f(item.num_views||item.views),lk=f(item.num_likes||item.likes); if(v)parts.push('Views '+v); if(lk)parts.push('Like '+lk); }
        else if(plat==='tiktok') { const v=f(item.views||item.play_count),lk=f(item.likes||item.digg_count); if(v)parts.push('Play '+v); if(lk)parts.push('Like '+lk); }
        else if(plat==='ig')     { const lk=f(item.num_likes||item.likes||item.like_count),cm=f(item.num_comments||item.comment_count); if(lk)parts.push('Like '+lk); if(cm)parts.push('Komen '+cm); }
        else if(plat==='fb')     { const lk=f(item.likes||item.num_likes||item.like_count),sh=f(item.shares||item.share_count); if(lk)parts.push('Like '+lk); if(sh)parts.push('Share '+sh); }
        return parts.join(' · ');
      })();

      const platDot = `<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:${color};flex-shrink:0;"></span>`;
      const itemData = sntEsc(JSON.stringify(item));

      return `<div class="sntp-item" data-item='${itemData}' data-plat="${plat}" onclick="SNTPopup._onItemClick(this)">
        <div class="sntp-avatar" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
        <div class="sntp-item-body">
          <div class="sntp-item-author">${sntEsc(displayName)}</div>
          ${handle?`<div class="sntp-item-handle">${sntEsc(handle)}</div>`:''}
          <div class="sntp-item-text">${sntEsc(text||'(tidak ada konten)')}</div>
          <div class="sntp-item-footer">
            <span class="sntp-sent-badge sntp-sent-badge--${sent}">${sentLbl}</span>
            ${platDot}<span style="font-size:10px;">${meta.label||''}</span>
            ${eng?`<span>${sntEsc(eng)}</span>`:''}
            ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
          </div>
        </div>
      </div>`;
    }).join('');

    if (items.length > SHOW) {
      list.insertAdjacentHTML('beforeend',
        `<div style="padding:9px 14px;text-align:center;font-size:11px;font-weight:600;color:#64748b;background:var(--bg-gray-50);border-top:1px dashed var(--border-gray);">+${(items.length-SHOW).toLocaleString()} mentions lainnya</div>`);
    }
  },

  _onItemClick(el) {
    try {
      const raw  = el.getAttribute('data-item');
      const item = JSON.parse(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"'));
      const plat = el.dataset.plat || this._curPlat || 'doc';
      SNTDetail.open(item, plat);
    } catch(e) { console.warn('SNT Detail parse error:', e); }
  }
};

/* ══════════════════════════════════════════════════════
   SENTIMENT DETAIL PANEL
══════════════════════════════════════════════════════ */
const SNTDetail = {
  open(item, platform) {
    const panel = document.getElementById('sntDetailPanel');
    const body  = document.getElementById('sntDpBody');
    const title = document.getElementById('sntDpTitle');
    const meta  = SNTPlatMeta[platform] || { label: platform, color:'#038047' };

    const name = (
      item.from_name||item.page_name||item.author_nickname||item.nickname||
      item.channel_title||item.channel_name||
      item.author_name||item.username||item.user_name||
      item.author_scr_name||item.screen_name||
      item.publisher||item.source_name||item.name||'Tidak diketahui'
    ).trim();
    const isNumericId = /^\d{8,}$/.test(name);
    const displayName = isNumericId ? `User ${name.slice(-4)}` : name;

    const rawHandle = (item.author_scr_name||item.screen_name||item.username||item.handle||'').trim();
    const handle    = rawHandle && rawHandle.toLowerCase()!==displayName.toLowerCase()
      ? (rawHandle.startsWith('@')?rawHandle:'@'+rawHandle) : '';

    const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
    const av = (item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||item.picture||'').trim();
    const url  = item.url||item.link||'';
    const date = item.date_created||item.created_at||item.publish_date||'';

    const sentRaw = String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
    const sent    = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif'?'pos':sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'||sentRaw==='negatif'?'neg':'neu';
    const sentLbl = {pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];

    title.textContent = displayName;

    const words   = displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
    const ini     = words.length>=2?(words[0][0]+words[words.length-1][0]).toUpperCase():(words[0]?.[0]||displayName[0]||'?').toUpperCase();
    const safeIni = ini.replace(/['"]/g,'');
    const avHtml  = (av&&(av.startsWith('http://')||av.startsWith('https://')))
      ? `<img src="${sntEsc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`
      : ini;

    let dtFmt = '';
    if (date) {
      try { dtFmt = new Date(date).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); }
      catch(e) { dtFmt = date.split('T')[0]; }
    }

    let mediaHtml = '';
    if (platform==='yt') {
      const ytId = (url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/)||[])[1];
      if (ytId) mediaHtml = `<div class="sntdp-media-wrap"><iframe style="width:100%;height:210px;border:none;display:block;" src="https://www.youtube.com/embed/${ytId}?rel=0&modestbranding=1" allowfullscreen></iframe></div>`;
    } else {
      const imgUrl = item.image_url||item.thumbnail||item.media_url||item.picture||'';
      if (imgUrl) mediaHtml = `<div class="sntdp-media-wrap"><img class="sntdp-media-img" src="${sntEsc(imgUrl)}" onerror="this.parentElement.style.display='none'"></div>`;
    }

    const statsMap = {
      twit:  [['Retweet',item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0],['Quote',item.num_quote||0]],
      fb:    [['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],
      ig:    [['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],
      yt:    [['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||0]],
      tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],
      doc:   [['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]],
    };
    const stats     = statsMap[platform] || [];
    const statsHtml = stats.some(s=>parseInt(s[1])>0)
      ? `<div class="sntdp-stats-grid">${stats.map(([l,v])=>`<div class="sntdp-stat-box"><div class="sntdp-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="sntdp-stat-lbl">${l}</div></div>`).join('')}</div>` : '';

    body.innerHTML = `
      <div class="sntdp-avatar-row">
        <div class="sntdp-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
        <div>
          <div class="sntdp-author-name">${sntEsc(displayName)}</div>
          ${handle?`<div class="sntdp-author-handle">${sntEsc(handle)}</div>`:''}
          <span style="background:${meta.color}22;color:${meta.color};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;margin-top:4px;">${meta.label}</span>
        </div>
      </div>
      ${dtFmt?`<div class="sntdp-meta-row"><span>${dtFmt}</span></div>`:''}
      <span class="sntdp-sent-badge sntdp-sent-badge--${sent}">${sentLbl}</span>
      ${mediaHtml}
      ${content?`<div class="sntdp-content-text">${sntEsc(content)}</div>`:''}
      ${statsHtml}
      ${url?`<a href="${sntEsc(url)}" target="_blank" rel="noopener noreferrer" class="sntdp-link-btn">
        <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Lihat Sumber Asli
      </a>`:''}`;

    panel.classList.add('visible');
  },
  close() { document.getElementById('sntDetailPanel')?.classList.remove('visible'); }
};

/* ══════════════════════════════════════════════════════
   PATCH: Click handlers pada elemen yang sudah ada
══════════════════════════════════════════════════════ */
function sntAttachClickHandlers() {
  const negCard = document.getElementById('valNeg')?.closest('.snt-stat-card');
  if (negCard && !negCard._sntBound) {
    negCard._sntBound = true;
    negCard.style.cursor = 'pointer';
    negCard.title = 'Klik untuk lihat mention Negative';
    negCard.addEventListener('click', e => {
      SNTPopup.openSentiment('neg');
    });
    const sub = negCard.querySelector('.snt-stat-sub');
    if (sub && !negCard.querySelector('.snt-stat-hint')) {
      const hint = document.createElement('div');
      hint.className = 'snt-stat-hint';
      hint.innerHTML = `<svg viewBox="0 0 24 24"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg> Klik untuk lihat detail`;
      sub.after(hint);
    }
  }

  const posCard = document.getElementById('valPos')?.closest('.snt-stat-card');
  if (posCard && !posCard._sntBound) {
    posCard._sntBound = true;
    posCard.style.cursor = 'pointer';
    posCard.title = 'Klik untuk lihat mention Positive';
    posCard.addEventListener('click', e => {
      SNTPopup.openSentiment('pos');
    });
    const sub = posCard.querySelector('.snt-stat-sub');
    if (sub && !posCard.querySelector('.snt-stat-hint')) {
      const hint = document.createElement('div');
      hint.className = 'snt-stat-hint';
      hint.innerHTML = `<svg viewBox="0 0 24 24"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg> Klik untuk lihat detail`;
      sub.after(hint);
    }
  }

  const neuCard = document.getElementById('valNeu')?.closest('.snt-stat-card');
  if (neuCard && !neuCard._sntBound) {
    neuCard._sntBound = true;
    neuCard.style.cursor = 'pointer';
    neuCard.title = 'Klik untuk lihat mention Neutral';
    neuCard.addEventListener('click', e => {
      SNTPopup.openSentiment('neu');
    });
    const sub = neuCard.querySelector('.snt-stat-sub');
    if (sub && !neuCard.querySelector('.snt-stat-hint')) {
      const hint = document.createElement('div');
      hint.className = 'snt-stat-hint';
      hint.innerHTML = `<svg viewBox="0 0 24 24"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg> Klik untuk lihat detail`;
      sub.after(hint);
    }
  }

  const totCard = document.getElementById('valTot')?.closest('.snt-stat-card');
  if (totCard && !totCard._sntBound) {
    totCard._sntBound = true;
    totCard.style.cursor = 'pointer';
    totCard.title = 'Klik untuk lihat semua mention';
    totCard.addEventListener('click', e => {
      SNTPopup.open('all','all');
    });
    const sub = totCard.querySelector('.snt-stat-sub');
    if (sub && !totCard.querySelector('.snt-stat-hint')) {
      const hint = document.createElement('div');
      hint.className = 'snt-stat-hint';
      hint.innerHTML = `<svg viewBox="0 0 24 24"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg> Klik untuk lihat detail`;
      sub.after(hint);
    }
  }

  const platRows = document.querySelectorAll('#platBreakdownList .snt-media-row');
  platRows.forEach(row => {
    if (row._sntBound) return;
    row._sntBound = true;
    row.style.cursor = 'pointer';
    const nameEl = row.querySelector('.snt-media-name');
    const label  = nameEl?.textContent?.trim() || '';
    const labelMap = {
      'Mass Media':'doc', 'X / Twitter':'twit', 'Facebook':'fb',
      'Instagram':'ig',   'YouTube':'yt',        'TikTok':'tiktok',
    };
    const plat = labelMap[label] || 'doc';
    row.addEventListener('click', e => {
      SNTPopup.open(plat, 'all');
    });
  });
}

/* ── CSS hint ── */
(function injectHintStyle() {
  const s = document.createElement('style');
  s.textContent = `
    .snt-stat-hint {
      font-size:10px; color:var(--primary-green); font-weight:600;
      margin-top:8px; display:flex; align-items:center; gap:4px; opacity:.85;
    }
    .snt-stat-hint svg { width:10px; height:10px; stroke:currentColor; fill:none; stroke-width:2.5; }
  `;
  document.head.appendChild(s);
})();

/* ══════════════════════════════════════════════════════
   PATCH Chart & Render functions
══════════════════════════════════════════════════════ */
const _origRenderAll = renderAll;
window.renderAll = function() {
  _origRenderAll();
  setTimeout(sntAttachClickHandlers, 80);
};

const _origPlatBreakdown = renderPlatBreakdown;
window.renderPlatBreakdown = function() {
  _origPlatBreakdown();
  setTimeout(sntAttachClickHandlers, 80);
};

const _origOverviewBar = renderOverviewBar;
window.renderOverviewBar = function() {
  _origOverviewBar();
  const chart = SNTCharts._i['chOverview'];
  if (!chart) return;
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.name] || 'all';
    SNTPopup.open('all', sent);
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origSovDoughnut = renderSovDoughnut;
window.renderSovDoughnut = function(domId, labels, values, colors, ready=false) {
  _origSovDoughnut(domId, labels, values, colors, ready);
  const chart = SNTCharts._i[domId];
  if (!chart) return;
  chart.on('click', params => {
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.name] || 'all';
    if (domId === 'chSovTotal') {
      SNTPopup.open('all', sent);
    } else if (domId === 'chMassPie') {
      SNTPopup.open('doc', sent);
    } else if (domId === 'chSocialPie') {
      const rect = chart.getDom().getBoundingClientRect();
      SNTPopup.showPlatPicker(rect.left+rect.width/2, rect.top+rect.height/2, sent);
    }
  });
  chart.on('mouseover', () => { chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origMassSocialBars = renderMassSocialBars;
window.renderMassSocialBars = function() {
  _origMassSocialBars();
  ['chMass','chSocial'].forEach(id => {
    const chart = SNTCharts._i[id];
    if (!chart) return;
    const isMass = id === 'chMass';
    chart.on('click', params => {
      if (params.componentType !== 'series') return;
      const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
      const sent = sentMap[params.seriesName] || 'all';
      if (isMass) {
        SNTPopup.open('doc', sent);
      } else {
        const rect = chart.getDom().getBoundingClientRect();
        SNTPopup.showPlatPicker(rect.left+params.event.offsetX, rect.top+params.event.offsetY, sent);
      }
    });
    chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
    chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
  });
};

const _origByTypePct = renderByTypePct;
window.renderByTypePct = function() {
  _origByTypePct();
  const chart = SNTCharts._i['chByType'];
  if (!chart) return;
  const labelToKey = {
    'Mass Media':'doc', 'X / Twitter':'twit', 'Facebook':'fb',
    'Instagram':'ig',   'YouTube':'yt',        'TikTok':'tiktok',
  };
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const bm   = SNTData.byMedia;
    const plat = labelToKey[bm[params.dataIndex]?.label] || 'doc';
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.seriesName] || 'all';
    SNTPopup.open(plat, sent);
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origByPlatGrouped = renderByPlatGrouped;
window.renderByPlatGrouped = function() {
  _origByPlatGrouped();
  const chart = SNTCharts._i['chByPlat'];
  if (!chart) return;
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.seriesName] || 'all';
    const groups = ['Mass Media','Social Media'];
    const grp = groups[params.dataIndex];
    if (grp === 'Mass Media') {
      SNTPopup.open('doc', sent);
    } else {
      const rect = chart.getDom().getBoundingClientRect();
      SNTPopup.showPlatPicker(rect.left+rect.width/2, rect.top+params.event.offsetY, sent);
    }
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origTrend = renderTrend;
window.renderTrend = function() {
  _origTrend();
  const chart = SNTCharts._i['chTrend'];
  if (!chart) return;
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.seriesName] || 'all';
    SNTPopup.open('all', sent);
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

const _origTimeChart = renderTimeChart;
window.renderTimeChart = function(domId, skelId, labels, negData, posData, neuData, totals, isHour=false) {
  _origTimeChart(domId, skelId, labels, negData, posData, neuData, totals, isHour);
  const chart = SNTCharts._i[domId];
  if (!chart) return;
  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.seriesName] || 'all';
    SNTPopup.open('all', sent);
  });
  chart.on('mouseover', p => { if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor='default'; });
};

/* ── Patch SNTPage.init & reload ── */
const _origSNTPageInit = SNTPage.init.bind(SNTPage);
SNTPage.init = function() {
  SNTPopup.init();
  _origSNTPageInit();
};

const _origSNTPageReload = SNTPage.reload.bind(SNTPage);
SNTPage.reload = function() {
  SNTPopup._cache = {};
  _origSNTPageReload();
};
</script>
@endsection