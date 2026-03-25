@extends('mk.layouts.app')

@section('title', 'Interaction Sentiment - SMADIMENT')

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

    --text-primary:   #1a202c;
    --text-secondary: #64748b;
    --text-muted:     #94a3b8;

    --bg-white:   #ffffff;
    --bg-body:    #f0f4f8;
    --bg-gray-50: #f8fafc;
    --bg-gray-100:#f1f5f9;

    --border-gray: #e2e8f0;
    --border-light:#f1f5f9;

    --shadow-sm: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1);
    --shadow-xl: 0 20px 40px -8px rgba(0,0,0,.18);

    --radius:    16px;
    --radius-sm: 12px;
    --radius-xs: 8px;
    --transition:all 0.2s cubic-bezier(0.4,0,0.2,1);
    --font:'Poppins', -apple-system, sans-serif;
  }

  *, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
  body { font-family:var(--font); background:var(--bg-body); color:var(--text-primary); }

  /* ── PAGE WRAPPER ── */
  .int-page { padding:24px; max-width:1600px; margin:0 auto; }

  /* ── PAGE HEADER ── */
  .page-header {
    display:flex; align-items:flex-start; justify-content:space-between;
    margin-bottom:28px; flex-wrap:wrap; gap:12px;
  }
  .page-header-left h1 { font-size:28px; font-weight:700; color:var(--text-primary); letter-spacing:-.4px; }
  .page-header-left p  { font-size:14px; color:var(--text-secondary); font-weight:500; margin-top:4px; }

  .int-refresh-btn {
    display:flex; align-items:center; gap:8px; padding:10px 20px;
    background:linear-gradient(135deg,#1a202c,#2d3748);
    color:#fff; border:none; border-radius:var(--radius-sm);
    font-family:var(--font); font-size:13px; font-weight:600;
    cursor:pointer; transition:var(--transition); box-shadow:0 4px 14px rgba(0,0,0,.2);
  }
  .int-refresh-btn:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,.25); }
  .int-refresh-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }

  /* ── SECTION HEADER ── */
  .int-section-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; margin-top:4px; }
  .int-section-icon {
    width:36px; height:36px; border-radius:var(--radius-sm);
    background:var(--primary-green-light);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
  }
  .int-section-icon svg { width:18px; height:18px; stroke:var(--primary-green); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
  .int-section-title { font-size:13px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.8px; }
  .int-section-line  { flex:1; height:1.5px; background:var(--border-gray); border-radius:1px; }

  /* ── STAT CARDS ── */
  .int-stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }

  .int-stat-card {
    background:var(--bg-white); border:1px solid var(--border-gray);
    border-radius:var(--radius); padding:20px 22px;
    box-shadow:var(--shadow-sm); transition:var(--transition);
    position:relative; overflow:hidden;
  }
  .int-stat-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:var(--bar-color, linear-gradient(90deg,var(--primary-green),var(--primary-green-dark)));
    opacity:0; transition:opacity .25s;
  }
  .int-stat-card:hover { box-shadow:var(--shadow-lg); transform:translateY(-2px); }
  .int-stat-card:hover::before { opacity:1; }

  .int-stat-card--neg  { --bar-color: linear-gradient(90deg,#ef4444,#dc2626); }
  .int-stat-card--pos  { --bar-color: linear-gradient(90deg,#2FC6F6,#0ea5e9); }
  .int-stat-card--neu  { --bar-color: linear-gradient(90deg,#94a3b8,#64748b); }
  .int-stat-card--tot  { --bar-color: linear-gradient(90deg,#038047,#026738); }

  .int-stat-label { font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px; display:flex; align-items:center; gap:6px; }
  .int-stat-dot   { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
  .int-stat-value { font-size:32px; font-weight:700; color:var(--text-primary); letter-spacing:-1px; line-height:1; min-height:40px; display:flex; align-items:center; }
  .int-stat-sub   { font-size:11px; color:var(--text-muted); font-weight:500; margin-top:7px; }
  .int-stat-pct   { font-size:13px; font-weight:700; margin-top:5px; }
  .int-stat-hint  {
    font-size:10px; color:var(--primary-green); font-weight:600;
    margin-top:8px; display:flex; align-items:center; gap:4px; opacity:.85;
  }
  .int-stat-hint svg { width:10px; height:10px; stroke:currentColor; fill:none; stroke-width:2.5; }

  /* ── CARDS ── */
  .int-card {
    background:var(--bg-white); border:1px solid var(--border-gray);
    border-radius:var(--radius); overflow:hidden; display:flex;
    flex-direction:column; box-shadow:var(--shadow-sm); transition:var(--transition);
    position:relative;
  }
  .int-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:linear-gradient(90deg,var(--primary-green),var(--primary-green-dark)); opacity:0; transition:opacity .3s;
  }
  .int-card:hover { box-shadow:var(--shadow-lg); border-color:var(--primary-green-border); }
  .int-card:hover::before { opacity:1; }

  .int-card-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:16px 20px; border-bottom:1px solid var(--border-gray); flex-shrink:0;
  }
  .int-card-head-left { display:flex; align-items:center; gap:12px; min-width:0; }
  .int-head-icon {
    width:40px; height:40px; border-radius:var(--radius-sm);
    background:var(--primary-green-light);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
  }
  .int-head-icon svg { width:20px; height:20px; fill:none; stroke:var(--primary-green); stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
  .int-card-title    { font-size:15px; font-weight:700; color:var(--text-primary); }
  .int-card-subtitle { font-size:11px; color:var(--text-muted); font-weight:500; margin-top:2px; }
  .int-badge {
    display:inline-flex; align-items:center; padding:4px 12px; border-radius:20px;
    font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px;
    background:var(--bg-gray-100); color:var(--text-secondary); white-space:nowrap; flex-shrink:0;
  }
  .int-card-body { padding:20px; flex:1; }

  /* ── LEGEND ── */
  .int-legend { display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
  .int-legend-item { display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:var(--text-secondary); }
  .int-legend-dot  { width:10px; height:10px; border-radius:50%; flex-shrink:0; }

  /* ── GRID LAYOUTS ── */
  .int-grid-2   { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
  .int-grid-3   { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:20px; }
  .int-grid-3-2 { display:grid; grid-template-columns:1.6fr 1fr; gap:20px; margin-bottom:20px; }
  .int-mb20     { margin-bottom:20px; }

  /* ── CHART HEIGHTS ── */
  .int-ch-260 { position:relative; height:260px; }
  .int-ch-300 { position:relative; height:300px; }
  .int-ch-320 { position:relative; height:320px; }
  .int-ch-380 { position:relative; height:380px; }

  /* ── SKELETON ── */
  .int-skel {
    background:linear-gradient(90deg,var(--bg-gray-50) 25%,#e2e8f0 50%,var(--bg-gray-50) 75%);
    background-size:200% 100%; animation:shimmer 1.5s ease-in-out infinite; border-radius:var(--radius-xs);
  }
  @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
  .int-skel-overlay { position:absolute; inset:0; z-index:3; border-radius:inherit; }

  /* ── EMPTY STATE ── */
  .int-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:44px 20px; gap:10px; }
  .int-empty svg { width:40px; height:40px; stroke:var(--border-gray); fill:none; stroke-width:1.5; }
  .int-empty-text { font-size:13px; font-weight:600; color:var(--text-secondary); }

  /* ── CSV BTN ── */
  .int-csv-btn {
    display:flex; align-items:center; gap:5px; padding:6px 14px;
    background:var(--bg-gray-100); border:1px solid var(--border-gray);
    border-radius:var(--radius-xs); font-family:var(--font);
    font-size:12px; font-weight:600; color:var(--text-secondary);
    cursor:pointer; transition:var(--transition);
  }
  .int-csv-btn:hover { background:var(--primary-green); border-color:var(--primary-green); color:#fff; }
  .int-csv-btn svg { width:12px; height:12px; stroke:currentColor; fill:none; stroke-width:2; }

  /* ── INTERACTION BOX METRICS ── */
  .int-metrics-box { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
  .int-metric-item {
    background:var(--bg-gray-50); border:1px solid var(--border-light);
    border-radius:var(--radius-sm); padding:14px 16px; transition:var(--transition);
  }
  .int-metric-item:hover { border-color:var(--primary-green-border); background:var(--bg-white); box-shadow:var(--shadow-sm); }
  .int-metric-label { font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
  .int-metric-value { font-size:22px; font-weight:800; color:var(--text-primary); letter-spacing:-.5px; }
  .int-metric-sub   { font-size:10px; color:var(--text-muted); margin-top:3px; }
  .int-metric-item.full { grid-column:1/-1; background:linear-gradient(135deg,rgba(3,128,71,.06),rgba(3,128,71,.02)); border-color:rgba(3,128,71,.15); }
  .int-metric-item.full .int-metric-value { color:var(--primary-green); font-size:28px; }

  /* ── PLATFORM LIST (same as snt-media-list) ── */
  .int-media-list { display:flex; flex-direction:column; gap:8px; }
  .int-media-row {
    display:flex; align-items:center; gap:10px;
    padding:10px 14px; background:var(--bg-gray-50);
    border:1px solid var(--border-light); border-radius:var(--radius-sm);
    transition:var(--transition);
  }
  .int-media-row:hover { border-color:var(--primary-green-border); background:var(--bg-white); box-shadow:var(--shadow-sm); }
  .int-media-icon  { width:32px; height:32px; border-radius:var(--radius-xs); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .int-media-name  { font-size:12px; font-weight:700; color:var(--text-primary); min-width:90px; }
  .int-media-bars  { flex:1; display:flex; flex-direction:column; gap:3px; }
  .int-media-bar-row   { display:flex; align-items:center; gap:6px; }
  .int-media-bar-track { flex:1; height:6px; background:var(--bg-gray-100); border-radius:3px; overflow:hidden; }
  .int-media-bar-fill  { height:100%; border-radius:3px; transition:width .8s cubic-bezier(.4,0,.2,1); }
  .int-media-bar-val   { font-size:10px; font-weight:700; color:var(--text-secondary); min-width:38px; text-align:right; white-space:nowrap; }
  .int-media-total     { font-size:12px; font-weight:700; color:var(--text-primary); min-width:52px; text-align:right; }

  @media (max-width:1280px) {
    .int-stat-grid { grid-template-columns:repeat(2,1fr); }
    .int-grid-2, .int-grid-3, .int-grid-3-2 { grid-template-columns:1fr; }
  }
  @media (max-width:768px) {
    .int-page { padding:16px; }
    .int-stat-grid { grid-template-columns:1fr; }
  }

  /* ══════════════════════════════════════════════════════
     INTERACTION MENTION POPUP (same pattern as SNT)
  ══════════════════════════════════════════════════════ */
  @keyframes intPopIn {
    from { opacity:0; transform:translateY(14px) scale(.94); }
    to   { opacity:1; transform:translateY(0) scale(1); }
  }

  #intPopup {
    position:fixed; z-index:99999;
    background:var(--bg-white); border:1px solid var(--border-gray);
    border-radius:var(--radius); box-shadow:var(--shadow-xl);
    width:480px; height:600px;
    display:none; flex-direction:column;
    overflow:hidden; font-family:var(--font);
    animation:intPopIn .22s cubic-bezier(.34,1.3,.64,1);
    user-select:none;
  }
  #intPopup.visible { display:flex; }

  .intp-header {
    display:flex; align-items:center; gap:8px; padding:12px 16px;
    background:var(--bg-gray-50); border-bottom:1px solid var(--border-gray);
    cursor:grab; flex-shrink:0;
  }
  .intp-header:active { cursor:grabbing; }
  .intp-drag-handle { display:flex; flex-direction:column; gap:3px; margin-right:4px; flex-shrink:0; opacity:.4; }
  .intp-drag-handle span { display:block; width:18px; height:2px; background:var(--text-secondary); border-radius:1px; }
  .intp-dot   { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
  .intp-title { font-size:13px; font-weight:700; color:var(--text-primary); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .intp-count {
    background:var(--primary-green); color:#fff; border-radius:10px;
    padding:1px 9px; font-size:11px; font-weight:800; flex-shrink:0;
  }
  .intp-close {
    width:28px; height:28px; border-radius:var(--radius-xs); border:none; background:transparent;
    cursor:pointer; font-size:20px; line-height:1; color:var(--text-secondary);
    display:flex; align-items:center; justify-content:center; transition:var(--transition); flex-shrink:0;
  }
  .intp-close:hover { background:#fee2e2; color:#991b1b; }

  .intp-actions {
    display:flex; align-items:center; gap:8px; padding:7px 13px;
    border-bottom:1px solid var(--border-gray); background:#fafbfc; flex-shrink:0;
  }
  .intp-meta {
    flex:1; font-size:10px; font-weight:700; color:var(--text-muted);
    text-transform:uppercase; letter-spacing:.5px;
    display:flex; align-items:center; gap:6px; overflow:hidden;
  }
  .intp-meta__label { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

  .intp-sent-tabs {
    display:flex; background:var(--bg-gray-100); border:1px solid var(--border-gray);
    border-radius:var(--radius-xs); padding:2px; gap:2px;
  }
  .intp-sent-tab {
    padding:4px 10px; border-radius:5px; border:none; background:transparent;
    font-family:var(--font); font-size:11px; font-weight:700; cursor:pointer;
    transition:var(--transition); color:var(--text-secondary); white-space:nowrap;
  }
  .intp-sent-tab:hover { background:var(--bg-white); }
  .intp-sent-tab.active { background:var(--bg-white); box-shadow:0 1px 4px rgba(0,0,0,.08); }
  .intp-sent-tab.active[data-s="all"] { color:var(--primary-green); }
  .intp-sent-tab.neg.active           { color:#ef4444; }
  .intp-sent-tab.pos.active           { color:#2FC6F6; }
  .intp-sent-tab.neu.active           { color:#94a3b8; }

  .intp-list { overflow-y:auto; flex:1; padding:4px 0; min-height:0; }
  .intp-list::-webkit-scrollbar { width:5px; }
  .intp-list::-webkit-scrollbar-thumb { background:var(--border-gray); border-radius:4px; }
  .intp-list::-webkit-scrollbar-thumb:hover { background:var(--text-muted); }

  .intp-item {
    display:flex; gap:10px; padding:10px 14px; border-bottom:1px solid var(--border-light);
    transition:background .1s; cursor:pointer; align-items:flex-start;
  }
  .intp-item:last-child { border-bottom:none; }
  .intp-item:hover { background:#f0fdf4; }

  .intp-avatar {
    width:38px; height:38px; border-radius:50%; flex-shrink:0;
    background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark));
    color:#fff; font-weight:700; font-size:13px;
    display:flex; align-items:center; justify-content:center;
    border:1.5px solid var(--border-gray); overflow:hidden;
  }
  .intp-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

  .intp-item-body   { flex:1; min-width:0; }
  .intp-item-author { font-size:12px; font-weight:700; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .intp-item-handle { font-size:10px; color:var(--text-muted); font-weight:500; margin-bottom:3px; }
  .intp-item-text   {
    font-size:12px; color:var(--text-secondary); line-height:1.5;
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;
    overflow:hidden; margin-bottom:5px;
  }
  .intp-item-footer { display:flex; align-items:center; gap:6px; font-size:10px; color:var(--text-muted); flex-wrap:wrap; }

  .intp-sent-badge { padding:1px 7px; border-radius:10px; font-size:9px; font-weight:800; text-transform:uppercase; }
  .intp-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
  .intp-sent-badge--neg { background:#fee2e2; color:#991b1b; }
  .intp-sent-badge--neu { background:var(--bg-gray-100); color:var(--text-secondary); }

  .intp-loading {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    height:100%; gap:14px; color:var(--text-secondary); font-size:13px; font-weight:600;
  }
  .intp-spinner {
    width:32px; height:32px; border:3px solid var(--border-gray);
    border-top-color:var(--primary-green); border-radius:50%;
    animation:intSpin .7s linear infinite;
  }
  @keyframes intSpin { to { transform:rotate(360deg); } }

  /* ── DETAIL PANEL ── */
  @keyframes intDetailIn {
    from { transform:translateX(100%); }
    to   { transform:translateX(0); }
  }
  #intDetailPanel {
    position:absolute; inset:0; background:var(--bg-white);
    z-index:10; display:none; flex-direction:column;
    animation:intDetailIn .22s cubic-bezier(.4,0,.2,1);
  }
  #intDetailPanel.visible { display:flex; }

  .intdp-header {
    display:flex; align-items:center; gap:10px; padding:12px 16px;
    background:var(--bg-gray-50); border-bottom:1px solid var(--border-gray); flex-shrink:0;
  }
  .intdp-back {
    width:30px; height:30px; border-radius:var(--radius-xs); border:1px solid var(--border-gray);
    background:var(--bg-white); cursor:pointer; display:flex; align-items:center; justify-content:center;
    color:var(--text-secondary); transition:var(--transition); flex-shrink:0;
  }
  .intdp-back:hover { background:var(--primary-green-light); color:var(--primary-green); border-color:var(--primary-green-border); }
  .intdp-back svg { width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }
  .intdp-title { font-size:13px; font-weight:700; color:var(--text-primary); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .intdp-close {
    width:28px; height:28px; border-radius:var(--radius-xs); border:none; background:transparent;
    cursor:pointer; font-size:20px; color:var(--text-secondary);
    display:flex; align-items:center; justify-content:center; transition:var(--transition);
  }
  .intdp-close:hover { background:#fee2e2; color:#991b1b; }

  .intdp-body { overflow-y:auto; flex:1; padding:16px; }
  .intdp-body::-webkit-scrollbar { width:5px; }
  .intdp-body::-webkit-scrollbar-thumb { background:var(--border-gray); border-radius:4px; }

  .intdp-avatar-row { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
  .intdp-avatar-lg {
    width:52px; height:52px; border-radius:50%;
    background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark));
    color:#fff; font-weight:700; font-size:18px;
    display:flex; align-items:center; justify-content:center;
    border:2px solid var(--border-gray); overflow:hidden; flex-shrink:0;
  }
  .intdp-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
  .intdp-author-name   { font-size:15px; font-weight:700; color:var(--text-primary); }
  .intdp-author-handle { font-size:11px; color:var(--text-muted); font-weight:500; }

  .intdp-sent-badge { display:inline-flex; align-items:center; gap:5px; padding:5px 14px; border-radius:20px; font-size:12px; font-weight:700; margin-bottom:12px; }
  .intdp-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
  .intdp-sent-badge--neg { background:#fee2e2; color:#991b1b; }
  .intdp-sent-badge--neu { background:var(--bg-gray-100); color:var(--text-secondary); }

  .intdp-content-text {
    font-size:13px; color:var(--text-secondary); line-height:1.7; margin-bottom:12px;
    background:var(--bg-gray-50); border-radius:10px; padding:12px 14px;
    border:1px solid var(--border-gray); word-break:break-word;
  }
  .intdp-meta-row { display:flex; align-items:center; justify-content:space-between; font-size:11px; color:var(--text-muted); font-weight:500; margin-bottom:12px; }
  .intdp-stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:12px; }
  .intdp-stat-box { background:var(--bg-gray-50); border-radius:10px; padding:10px 12px; border:1px solid var(--border-gray); text-align:center; }
  .intdp-stat-val { font-size:16px; font-weight:700; color:var(--text-primary); }
  .intdp-stat-lbl { font-size:9px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.4px; margin-top:2px; }

  .intdp-media-wrap { border-radius:10px; overflow:hidden; margin-bottom:12px; background:#000; }
  .intdp-media-img  { width:100%; max-height:240px; object-fit:cover; display:block; }

  .intdp-link-btn {
    display:flex; align-items:center; justify-content:center; gap:8px;
    padding:10px 14px; background:var(--primary-green); color:#fff;
    border-radius:10px; font-size:12px; font-weight:700;
    text-decoration:none; transition:var(--transition); width:100%; margin-top:4px;
  }
  .intdp-link-btn:hover { background:var(--primary-green-dark); }
  .intdp-link-btn svg { width:13px; height:13px; stroke:#fff; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }

  /* ── PLATFORM PICKER ── */
  @keyframes intPlatIn {
    from { opacity:0; transform:scale(.9) translateY(8px); }
    to   { opacity:1; transform:none; }
  }
  #intPlatPicker {
    position:fixed; z-index:999999; background:var(--bg-white); border:1px solid var(--border-gray);
    border-radius:var(--radius-sm); box-shadow:var(--shadow-lg);
    padding:6px; min-width:180px; font-family:var(--font);
    animation:intPlatIn .15s ease-out; display:none;
  }
  #intPlatPicker.visible { display:block; }
  .intpp-header { padding:5px 10px 8px; font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--border-light); margin-bottom:4px; }
  .intpp-btn {
    display:flex; align-items:center; gap:8px; padding:8px 11px; border-radius:var(--radius-xs);
    font-size:12px; font-weight:600; cursor:pointer; background:transparent; border:none;
    font-family:var(--font); width:100%; text-align:left; color:var(--text-secondary); transition:var(--transition);
  }
  .intpp-btn:hover { background:var(--primary-green-light); color:var(--primary-green); }
  .intpp-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; margin-left:auto; }
</style>
@endsection

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date',   now()->format('Y-m-d'));
  $projects  = $projects ?? [];
@endphp

<div class="int-page">

  {{-- PAGE HEADER --}}
  <div class="page-header">
    <div class="page-header-left">
      <h1>Interaction Sentiment</h1>
      <p>Analisis sentimen berdasarkan interaksi — reach, engagement, dan distribusi per platform</p>
    </div>
    <button class="int-refresh-btn" onclick="INTPage.reload()">
      <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
      Refresh
    </button>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 1 — TOTAL INTERACTIONS BY SENTIMENTS
  ═══════════════════════════════════════════════════════ --}}
  <div class="int-section-header">
    <div class="int-section-icon">
      <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
    </div>
    <span class="int-section-title">Total Interactions by Sentiments</span>
    <div class="int-section-line"></div>
  </div>

  {{-- Stat Cards --}}
  <div class="int-stat-grid">
    <div class="int-stat-card int-stat-card--neg">
      <div class="int-stat-label"><span class="int-stat-dot" style="background:#ef4444;"></span>Negative</div>
      <div class="int-stat-value" id="valNeg"><div class="int-skel" style="height:36px;width:110px;border-radius:6px;"></div></div>
      <div class="int-stat-sub">Total interaksi negatif</div>
      <div class="int-stat-pct" style="color:#ef4444;" id="pctNeg">—</div>
    </div>
    <div class="int-stat-card int-stat-card--pos">
      <div class="int-stat-label"><span class="int-stat-dot" style="background:var(--sent-pos);"></span>Positive</div>
      <div class="int-stat-value" id="valPos"><div class="int-skel" style="height:36px;width:110px;border-radius:6px;"></div></div>
      <div class="int-stat-sub">Total interaksi positif</div>
      <div class="int-stat-pct" style="color:var(--sent-pos);" id="pctPos">—</div>
    </div>
    <div class="int-stat-card int-stat-card--neu">
      <div class="int-stat-label"><span class="int-stat-dot" style="background:var(--sent-neu);"></span>Neutral</div>
      <div class="int-stat-value" id="valNeu"><div class="int-skel" style="height:36px;width:110px;border-radius:6px;"></div></div>
      <div class="int-stat-sub">Total interaksi netral</div>
      <div class="int-stat-pct" style="color:var(--sent-neu);" id="pctNeu">—</div>
    </div>
    <div class="int-stat-card int-stat-card--tot">
      <div class="int-stat-label">Total Interactions</div>
      <div class="int-stat-value" id="valTot"><div class="int-skel" style="height:36px;width:110px;border-radius:6px;"></div></div>
      <div class="int-stat-sub">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
    </div>
  </div>

  {{-- Bar (3/5) + Doughnut (2/5) --}}
  <div class="int-grid-3-2 int-mb20">
    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          <div>
            <div class="int-card-title">Total Interactions by Sentiments</div>
            <div class="int-card-subtitle">Perbandingan volume Negative / Positive / Neutral</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
          <button class="int-csv-btn" onclick="INTCsv.copyOverview()">
            <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy CSV
          </button>
          <span class="int-badge">Overview</span>
        </div>
      </div>
      <div class="int-card-body">
        <div class="int-ch-300">
          <div id="chOverview" style="width:100%;height:100%;"></div>
          <div class="int-skel int-skel-overlay" id="skOverview"></div>
        </div>
        <div class="int-legend" style="margin-top:14px;justify-content:center;">
          <div class="int-legend-item"><span class="int-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>

    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
          <div>
            <div class="int-card-title">Share of Voice by Sentiment</div>
            <div class="int-card-subtitle">Persentase distribusi sentimen</div>
          </div>
        </div>
        <span class="int-badge">SOV</span>
      </div>
      <div class="int-card-body" style="display:flex;flex-direction:column;align-items:center;">
        <div style="position:relative;height:280px;width:100%;">
          <div id="chSovTotal" style="width:100%;height:100%;"></div>
          <div class="int-skel" style="position:absolute;inset:0;border-radius:8px;" id="skSovTotal"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 2 — MEDIA PLATFORMS
  ═══════════════════════════════════════════════════════ --}}
  <div class="int-section-header">
    <div class="int-section-icon">
      <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
    </div>
    <span class="int-section-title">Media Platforms</span>
    <div class="int-section-line"></div>
  </div>

  <div class="int-grid-2 int-mb20">
    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="10" y1="6" x2="16" y2="6"/><line x1="10" y1="10" x2="16" y2="10"/></svg></span>
          <div>
            <div class="int-card-title">Sentiments in Mass Media</div>
            <div class="int-card-subtitle">Online News / Artikel</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <button class="int-csv-btn" onclick="INTCsv.copyMass()"><svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> CSV</button>
          <span class="int-badge">Mass Media</span>
        </div>
      </div>
      <div class="int-card-body">
        <div class="int-ch-260">
          <div id="chMass" style="width:100%;height:100%;"></div>
          <div class="int-skel int-skel-overlay" id="skMass"></div>
        </div>
        <div class="int-legend" style="margin-top:12px;justify-content:center;">
          <div class="int-legend-item"><span class="int-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>

    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><path d="M17 2H7a5 5 0 0 0-5 5v10a5 5 0 0 0 5 5h10a5 5 0 0 0 5-5V7a5 5 0 0 0-5-5z"/><circle cx="12" cy="12" r="3"/><circle cx="17.5" cy="6.5" r="1.5"/></svg></span>
          <div>
            <div class="int-card-title">Sentiments in Social Media</div>
            <div class="int-card-subtitle">Twitter · Facebook · Instagram · YouTube · TikTok</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
          <button class="int-csv-btn" onclick="INTCsv.copySocial()"><svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> CSV</button>
          <span class="int-badge">Social Media</span>
        </div>
      </div>
      <div class="int-card-body">
        <div class="int-ch-260">
          <div id="chSocial" style="width:100%;height:100%;"></div>
          <div class="int-skel int-skel-overlay" id="skSocial"></div>
        </div>
        <div class="int-legend" style="margin-top:12px;justify-content:center;">
          <div class="int-legend-item"><span class="int-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>
  </div>

  <div class="int-grid-2 int-mb20">
    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          <div>
            <div class="int-card-title">Sentiments by Media Types</div>
            <div class="int-card-subtitle">Persentase sentimen per platform (%)</div>
          </div>
        </div>
        <span class="int-badge">% Share</span>
      </div>
      <div class="int-card-body">
        <div class="int-ch-300">
          <div id="chByType" style="width:100%;height:100%;"></div>
          <div class="int-skel int-skel-overlay" id="skByType"></div>
        </div>
      </div>
    </div>

    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/></svg></span>
          <div>
            <div class="int-card-title">Sentiments by Media Platforms</div>
            <div class="int-card-subtitle">Komparasi Mass Media vs Social Media</div>
          </div>
        </div>
        <span class="int-badge">Grouped</span>
      </div>
      <div class="int-card-body">
        <div class="int-ch-300">
          <div id="chByPlat" style="width:100%;height:100%;"></div>
          <div class="int-skel int-skel-overlay" id="skByPlat"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="int-grid-2 int-mb20">
    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></span>
          <div>
            <div class="int-card-title">Mass Media</div>
            <div class="int-card-subtitle">Share of Voice sentimen — Online News</div>
          </div>
        </div>
        <span class="int-badge">Mass</span>
      </div>
      <div class="int-card-body" style="display:flex;flex-direction:column;align-items:center;">
        <div style="position:relative;height:260px;width:100%;">
          <div id="chMassPie" style="width:100%;height:100%;"></div>
          <div class="int-skel" style="position:absolute;inset:0;border-radius:8px;" id="skMassPie"></div>
        </div>
      </div>
    </div>

    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><path d="M17 2H7a5 5 0 0 0-5 5v10a5 5 0 0 0 5 5h10a5 5 0 0 0 5-5V7a5 5 0 0 0-5-5z"/><circle cx="12" cy="12" r="3"/></svg></span>
          <div>
            <div class="int-card-title">Social Media</div>
            <div class="int-card-subtitle">Share of Voice sentimen — Social platforms</div>
          </div>
        </div>
        <span class="int-badge">Social</span>
      </div>
      <div class="int-card-body" style="display:flex;flex-direction:column;align-items:center;">
        <div style="position:relative;height:260px;width:100%;">
          <div id="chSocialPie" style="width:100%;height:100%;"></div>
          <div class="int-skel" style="position:absolute;inset:0;border-radius:8px;" id="skSocialPie"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Interaction Breakdown per Platform --}}
  <div class="int-card int-mb20">
    <div class="int-card-head">
      <div class="int-card-head-left">
        <span class="int-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
        <div>
          <div class="int-card-title">Breakdown Interaksi per Platform</div>
          <div class="int-card-subtitle">Negative / Positive / Neutral per media — klik untuk lihat detail</div>
        </div>
      </div>
      <span class="int-badge">All Platforms</span>
    </div>
    <div class="int-card-body">
      <div id="platBreakdownList" class="int-media-list">
        @foreach(['Mass Media','X / Twitter','Facebook','Instagram','YouTube','TikTok'] as $pl)
        <div class="int-media-row">
          <div class="int-media-name">{{ $pl }}</div>
          <div class="int-media-bars">
            <div class="int-media-bar-row"><div class="int-skel" style="height:6px;width:100%;border-radius:3px;"></div></div>
            <div class="int-media-bar-row"><div class="int-skel" style="height:6px;width:100%;border-radius:3px;margin-top:4px;"></div></div>
          </div>
          <div class="int-skel" style="height:18px;width:50px;border-radius:4px;margin-left:10px;"></div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 3 — SENTIMENT TRENDS
  ═══════════════════════════════════════════════════════ --}}
  <div class="int-section-header">
    <div class="int-section-icon">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <span class="int-section-title">Sentiments Trends</span>
    <div class="int-section-line"></div>
  </div>

  <div class="int-card int-mb20">
    <div class="int-card-head">
      <div class="int-card-head-left">
        <span class="int-head-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
        <div>
          <div class="int-card-title">Sentiment's Trends in All Media Types</div>
          <div class="int-card-subtitle">Tren harian Negative / Positive / Neutral</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
        <button class="int-csv-btn" onclick="INTCsv.copyTrend()">
          <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy CSV
        </button>
        <span class="int-badge" id="trendBadge">Loading…</span>
      </div>
    </div>
    <div class="int-card-body">
      <div class="int-ch-380">
        <div id="chTrend" style="width:100%;height:100%;"></div>
        <div class="int-skel int-skel-overlay" id="skTrend"></div>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 4 — SENTIMENTS BY WEEKDAY & HOUR
  ═══════════════════════════════════════════════════════ --}}
  <div class="int-section-header">
    <div class="int-section-icon">
      <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
    <span class="int-section-title">Sentiments by Time</span>
    <div class="int-section-line"></div>
  </div>

  <div class="int-grid-2">
    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
          <div>
            <div class="int-card-title">Sentiments by Weekday</div>
            <div class="int-card-subtitle">Volume sentimen per hari dalam seminggu</div>
          </div>
        </div>
        <span class="int-badge">7 Hari</span>
      </div>
      <div class="int-card-body">
        <div class="int-ch-320">
          <div id="chWeekday" style="width:100%;height:100%;"></div>
          <div class="int-skel int-skel-overlay" id="skWeekday"></div>
        </div>
        <div class="int-legend" style="margin-top:12px;justify-content:center;">
          <div class="int-legend-item"><span class="int-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>

    <div class="int-card">
      <div class="int-card-head">
        <div class="int-card-head-left">
          <span class="int-head-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
          <div>
            <div class="int-card-title">Sentiments by Hour</div>
            <div class="int-card-subtitle">Distribusi sentimen per jam (00–23)</div>
          </div>
        </div>
        <span class="int-badge">24 Jam</span>
      </div>
      <div class="int-card-body">
        <div class="int-ch-320">
          <div id="chHour" style="width:100%;height:100%;"></div>
          <div class="int-skel int-skel-overlay" id="skHour"></div>
        </div>
        <div class="int-legend" style="margin-top:12px;justify-content:center;">
          <div class="int-legend-item"><span class="int-legend-dot" style="background:#ef4444;"></span>Negative</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-pos);"></span>Positive</div>
          <div class="int-legend-item"><span class="int-legend-dot" style="background:var(--sent-neu);"></span>Neutral</div>
        </div>
      </div>
    </div>
  </div>

</div>{{-- /int-page --}}

{{-- ═══════════════════════════════════════════════════════
     INTERACTION MENTION POPUP
═══════════════════════════════════════════════════════ --}}
<div id="intPopup">
  <div class="intp-header" id="intPopHeader">
    <div class="intp-drag-handle"><span></span><span></span><span></span></div>
    <div class="intp-dot" id="intPopDot"></div>
    <span class="intp-title" id="intPopTitle">Interactions</span>
    <span class="intp-count" id="intPopCount">…</span>
    <button class="intp-close" onclick="INTPopup.close()">×</button>
  </div>
  <div class="intp-actions">
    <div class="intp-meta">
      <svg viewBox="0 0 24 24" style="width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <span class="intp-meta__label" id="intPopMeta">—</span>
    </div>
    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
      <div class="intp-sent-tabs" id="intPopSentTabs">
        <button class="intp-sent-tab active" data-s="all" onclick="INTPopup.filterSent('all')">Semua</button>
        <button class="intp-sent-tab neg"    data-s="neg" onclick="INTPopup.filterSent('neg')">Neg</button>
        <button class="intp-sent-tab pos"    data-s="pos" onclick="INTPopup.filterSent('pos')">Pos</button>
        <button class="intp-sent-tab neu"    data-s="neu" onclick="INTPopup.filterSent('neu')">Neu</button>
      </div>
    </div>
  </div>
  <div class="intp-list" id="intPopList"></div>

  <div id="intDetailPanel">
    <div class="intdp-header">
      <button class="intdp-back" onclick="INTDetail.close()">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="intdp-title" id="intDpTitle">Detail</span>
      <button class="intdp-close" onclick="INTPopup.close()">×</button>
    </div>
    <div class="intdp-body" id="intDpBody"></div>
  </div>
</div>

<div id="intPlatPicker">
  <div class="intpp-header">Pilih Platform</div>
  <button class="intpp-btn" onclick="INTPopup.openPlatform('twit','all')">X / Twitter <span class="intpp-dot" style="background:#1d9bf0;"></span></button>
  <button class="intpp-btn" onclick="INTPopup.openPlatform('fb','all')">Facebook <span class="intpp-dot" style="background:#1877f2;"></span></button>
  <button class="intpp-btn" onclick="INTPopup.openPlatform('ig','all')">Instagram <span class="intpp-dot" style="background:#e1306c;"></span></button>
  <button class="intpp-btn" onclick="INTPopup.openPlatform('yt','all')">YouTube <span class="intpp-dot" style="background:#ff0000;"></span></button>
  <button class="intpp-btn" onclick="INTPopup.openPlatform('tiktok','all')">TikTok <span class="intpp-dot" style="background:#111827;"></span></button>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
const INTCfg = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
};

/* ── UTILS ── */
const numFmt = n => parseInt(n||0).toLocaleString('id-ID');
const numK   = n => { n=parseInt(n||0); return n>=1e9?(n/1e9).toFixed(1)+'B':n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const pct    = (v,t) => t>0 ? ((v/t)*100).toFixed(1)+'%' : '0%';
const emptyHtml = msg => `<div class="int-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="int-empty-text">${msg}</span></div>`;

/* ── ECHARTS REGISTRY ── */
const INTCharts = {
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
  Object.values(INTCharts._i).forEach(c=>{ try{ if(!c.isDisposed()) c.resize(); }catch(e){} });
});

const EC_TIP = {
  backgroundColor:'#1a202c', borderColor:'#334155', borderWidth:1,
  padding:[10,14], textStyle:{color:'#fff',fontFamily:"'Poppins',sans-serif",fontSize:12},
  extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
};

/* ══════════════════════════════════════════════════════
   DATA STORE
══════════════════════════════════════════════════════ */
const INTData = {
  totals:  { neg:0, pos:0, neu:0 },
  byMedia: [],
  trend:   [],
  weekday: null,
  hour:    null,
};

/* ══════════════════════════════════════════════════════
   LOAD
══════════════════════════════════════════════════════ */
async function loadSentiment() {
  if (!INTCfg.pid) {
    ['skOverview','skSovTotal','skMass','skSocial','skByType','skByPlat','skMassPie','skSocialPie','skTrend','skWeekday','skHour'].forEach(hideSk);
    return;
  }
  try {
    const res  = await fetch(`/mk/api/sentiment/totals?project_id=${INTCfg.pid}&start_date=${INTCfg.sd}&end_date=${INTCfg.ed}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    INTData.totals  = data.totals  || { neg:0, pos:0, neu:0 };
    INTData.byMedia = data.by_media || [];
    INTData.trend   = data.trend   || [];
    renderAll();
  } catch(err) {
    console.error('loadSentiment error:', err);
    ['skOverview','skSovTotal','skMass','skSocial','skByType','skByPlat','skMassPie','skSocialPie'].forEach(hideSk);
  }
}

async function loadTimeData() {
  if (!INTCfg.pid) return;
  try {
    const res  = await fetch(`/mk/api/sentiment/by-time?project_id=${INTCfg.pid}&start_date=${INTCfg.sd}&end_date=${INTCfg.ed}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    INTData.weekday = data.weekday;
    INTData.hour    = data.hour;
    renderWeekdayHour();
  } catch(err) {
    console.error('loadTimeData error:', err);
    ['skWeekday','skHour'].forEach(hideSk);
  }
}

/* ══════════════════════════════════════════════════════
   RENDER ALL
══════════════════════════════════════════════════════ */
function renderAll() {
  const { neg, pos, neu } = INTData.totals;
  const tot = neg + pos + neu;

  document.getElementById('valNeg').textContent = numFmt(neg);
  document.getElementById('valPos').textContent = numFmt(pos);
  document.getElementById('valNeu').textContent = numFmt(neu);
  document.getElementById('valTot').textContent = numFmt(tot);
  document.getElementById('pctNeg').textContent = pct(neg, tot);
  document.getElementById('pctPos').textContent = pct(pos, tot);
  document.getElementById('pctNeu').textContent = pct(neu, tot);

  renderOverviewBar();
  renderSovDoughnut('chSovTotal', ['Negative','Positive','Neutral'], [neg,pos,neu], ['#ef4444','#2FC6F6','#94a3b8']);
  hideSk('skSovTotal');

  renderMassSocialBars();
  renderByTypePct();
  renderByPlatGrouped();
  renderMassSocialPies();
  renderPlatBreakdown();
  renderTrend();

  setTimeout(intAttachClickHandlers, 80);
}

/* ─── Overview Bar ─── */
function renderOverviewBar() {
  hideSk('skOverview');
  const { neg, pos, neu } = INTData.totals;
  const tot = neg + pos + neu;
  if (tot === 0) { document.getElementById('chOverview').parentElement.innerHTML = emptyHtml('Tidak ada data sentimen'); return; }

  const chart = INTCharts.make('chOverview');
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const p=params[0], v=p.value;
        return `<div style="font-weight:700;font-size:13px;margin-bottom:6px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;"><span style="color:#94a3b8;">Interactions</span><span style="font-weight:700;">${numFmt(v)}</span></div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct(v,tot)}</span></div>`;
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
        {value:neg,itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:'#ef4444'},{offset:1,color:'#fca5a555'}]},borderRadius:[8,8,0,0]}},
        {value:pos,itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:'#2FC6F6'},{offset:1,color:'#7dd3fc55'}]},borderRadius:[8,8,0,0]}},
        {value:neu,itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:'#94a3b8'},{offset:1,color:'#cbd5e155'}]},borderRadius:[8,8,0,0]}},
      ],
      label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',formatter:p=>numK(p.value)}
    }]
  });

  chart.on('click', params => {
    if (params.componentType !== 'series') return;
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.name] || 'all';
    const rect = chart.getDom().getBoundingClientRect();
    INTPopup.open('all', sent, rect.left+rect.width/2, rect.top+params.event.offsetY);
  });
  chart.on('mouseover', p=>{ if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  ()=>{ chart.getDom().style.cursor='default'; });
}

/* ─── SOV Doughnut ─── */
function renderSovDoughnut(domId, labels, values, colors) {
  const tot = values.reduce((a,b)=>a+b,0);
  const chart = INTCharts.make(domId);
  if (!chart) return;
  chart.setOption({
    animation:true, animationDuration:800, animationEasing:'cubicOut',
    backgroundColor:'transparent',
    tooltip:{
      ...EC_TIP, trigger:'item',
      formatter: p => {
        const pc = tot>0?((p.value/tot)*100).toFixed(1):'0.0';
        return `<div style="font-weight:700;font-size:13px;margin-bottom:5px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Interactions</span><span style="font-weight:700;">${numFmt(p.value)}</span></div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pc}%</span></div>`;
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
          return `{name|${p.name}}\n{pct|${pc.toFixed(1)}%}`;
        },
        rich:{
          name:{fontWeight:'700',fontSize:11,color:'#1a202c',lineHeight:18},
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

  chart.on('click', params => {
    const sentMap = { 'Negative':'neg', 'Positive':'pos', 'Neutral':'neu' };
    const sent = sentMap[params.name] || 'all';
    const rect = chart.getDom().getBoundingClientRect();
    if (domId === 'chSovTotal') INTPopup.open('all', sent, rect.left+rect.width/2, rect.top+rect.height/2);
    else if (domId === 'chMassPie') INTPopup.open('doc', sent, rect.left+rect.width/2, rect.top+rect.height/2);
    else if (domId === 'chSocialPie') INTPopup.showPlatPicker(rect.left+rect.width/2, rect.top+rect.height/2, sent);
  });
  chart.on('mouseover', ()=>{ chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  ()=>{ chart.getDom().style.cursor='default'; });
}

/* ─── Mass & Social Stacked Bars ─── */
function renderMassSocialBars() {
  const bm = INTData.byMedia;
  const massPlt  = bm.filter(m=>m.key==='doc');
  const socialPlt= bm.filter(m=>m.key!=='doc');

  hideSk('skMass');
  const massChart = INTCharts.make('chMass');
  if (massChart) {
    const lb=massPlt.map(m=>m.label), ng=massPlt.map(m=>m.neg), ps=massPlt.map(m=>m.pos), ne=massPlt.map(m=>m.neu), tt=massPlt.map(m=>m.neg+m.pos+m.neu);
    if (lb.length && tt.some(v=>v>0)) {
      massChart.setOption(makeStackedBarOption(lb, ng, ps, ne, tt));
      massChart.on('click', params=>{
        if(params.componentType!=='series') return;
        const sentMap={'Negative':'neg','Positive':'pos','Neutral':'neu'};
        const rect=massChart.getDom().getBoundingClientRect();
        INTPopup.open('doc', sentMap[params.seriesName]||'all', rect.left+rect.width/2, rect.top+params.event.offsetY);
      });
      massChart.on('mouseover', p=>{ if(p.componentType==='series') massChart.getDom().style.cursor='pointer'; });
      massChart.on('mouseout',  ()=>{ massChart.getDom().style.cursor='default'; });
    } else { document.getElementById('chMass').parentElement.innerHTML = emptyHtml('Tidak ada data Mass Media'); }
  }

  hideSk('skSocial');
  const socialChart = INTCharts.make('chSocial');
  if (socialChart) {
    const lb=socialPlt.map(m=>m.label), ng=socialPlt.map(m=>m.neg), ps=socialPlt.map(m=>m.pos), ne=socialPlt.map(m=>m.neu), tt=socialPlt.map(m=>m.neg+m.pos+m.neu);
    if (lb.length && tt.some(v=>v>0)) {
      socialChart.setOption(makeStackedBarOption(lb, ng, ps, ne, tt));
      socialChart.on('click', params=>{
        if(params.componentType!=='series') return;
        const sentMap={'Negative':'neg','Positive':'pos','Neutral':'neu'};
        const rect=socialChart.getDom().getBoundingClientRect();
        INTPopup.showPlatPicker(rect.left+params.event.offsetX, rect.top+params.event.offsetY, sentMap[params.seriesName]||'all');
      });
      socialChart.on('mouseover', p=>{ if(p.componentType==='series') socialChart.getDom().style.cursor='pointer'; });
      socialChart.on('mouseout',  ()=>{ socialChart.getDom().style.cursor='default'; });
    } else { document.getElementById('chSocial').parentElement.innerHTML = emptyHtml('Tidak ada data Social Media'); }
  }
}

function makeStackedBarOption(xLabels, negData, posData, neuData, totals) {
  return {
    animation:true, animationDuration:900, animationEasing:'elasticOut', backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const idx=params[0]?.dataIndex??0, tot=totals[idx]||0;
        const rows=[...params].reverse().map(p=>
          `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0;"></span><span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span></div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${xLabels[idx]||''}</div>${rows}<div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;gap:16px;"><span style="font-size:11px;color:#94a3b8;">Total</span><span style="font-weight:700;">${numFmt(tot)}</span></div>`;
      }
    },
    grid:{top:28,right:16,bottom:36,left:60},
    xAxis:{type:'category',data:xLabels,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b',interval:0,formatter:v=>v.length>11?v.slice(0,10)+'…':v}},
    yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}},
    series:[
      {name:'Neutral', type:'bar',stack:'s',data:neuData.map(v=>({value:v,itemStyle:{color:'#94a3b8'}}))},
      {name:'Positive',type:'bar',stack:'s',data:posData.map(v=>({value:v,itemStyle:{color:'#2FC6F6'}}))},
      {name:'Negative',type:'bar',stack:'s',barMaxWidth:80,
       data:negData.map(v=>({value:v,itemStyle:{color:'#ef4444',borderRadius:[6,6,0,0]}})),
       label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',formatter:p=>totals[p.dataIndex]>0?numK(totals[p.dataIndex]):''}}
    ]
  };
}

/* ─── By Media Type % ─── */
function renderByTypePct() {
  hideSk('skByType');
  const bm = INTData.byMedia;
  if (!bm.length) { document.getElementById('chByType').parentElement.innerHTML = emptyHtml('Tidak ada data'); return; }
  const chart = INTCharts.make('chByType');
  if (!chart) return;
  const labels  = bm.map(m=>m.label);
  const negPcts = bm.map(m=>{ const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.neg/t*100).toFixed(1)):0; });
  const posPcts = bm.map(m=>{ const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.pos/t*100).toFixed(1)):0; });
  const neuPcts = bm.map(m=>{ const t=m.neg+m.pos+m.neu; return t>0?parseFloat((m.neu/t*100).toFixed(1)):0; });
  const labelToKey = {'Mass Media':'doc','X / Twitter':'twit','Facebook':'fb','Instagram':'ig','YouTube':'yt','TikTok':'tiktok'};
  chart.setOption({
    animation:true,animationDuration:900,backgroundColor:'#fff',
    tooltip:{...EC_TIP,trigger:'axis',axisPointer:{type:'shadow'},
      formatter:params=>{const idx=params[0]?.dataIndex??0,m=bm[idx];if(!m)return'';const tot=m.neg+m.pos+m.neu;
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${m.label}</div>${[['Negative','#ef4444',m.neg],['Positive','#2FC6F6',m.pos],['Neutral','#94a3b8',m.neu]].map(([n,c,v])=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${c};"></span><span style="font-size:12px;color:#94a3b8;">${n}</span></div><div style="display:flex;gap:10px;"><span style="font-size:12px;font-weight:700;">${numFmt(v)}</span><span style="font-size:10px;color:#94a3b8;">${tot>0?(v/tot*100).toFixed(1):'0'}%</span></div></div>`).join('')}`;
      }
    },
    legend:{bottom:0,data:['Negative','Positive','Neutral'],textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},icon:'circle',itemWidth:10,itemHeight:10,itemGap:20},
    grid:{top:12,right:16,bottom:50,left:100},
    xAxis:{type:'value',max:100,min:0,axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:v=>v+'%'}},
    yAxis:{type:'category',data:labels,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#374151',margin:10}},
    series:[
      {name:'Negative',type:'bar',stack:'pct',data:negPcts,itemStyle:{color:'#ef4444'},barMaxWidth:30},
      {name:'Positive',type:'bar',stack:'pct',data:posPcts,itemStyle:{color:'#2FC6F6'},barMaxWidth:30},
      {name:'Neutral', type:'bar',stack:'pct',data:neuPcts,itemStyle:{color:'#94a3b8',borderRadius:[0,4,4,0]},barMaxWidth:30},
    ]
  });
  chart.on('click', params=>{
    if(params.componentType!=='series') return;
    const sentMap={'Negative':'neg','Positive':'pos','Neutral':'neu'};
    const plat=labelToKey[bm[params.dataIndex]?.label]||'doc';
    const rect=chart.getDom().getBoundingClientRect();
    INTPopup.open(plat, sentMap[params.seriesName]||'all', rect.left+rect.width/2, rect.top+params.event.offsetY);
  });
  chart.on('mouseover', p=>{ if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  ()=>{ chart.getDom().style.cursor='default'; });
}

/* ─── By Platform Grouped ─── */
function renderByPlatGrouped() {
  hideSk('skByPlat');
  const bm = INTData.byMedia;
  if (!bm.length) { document.getElementById('chByPlat').parentElement.innerHTML = emptyHtml('Tidak ada data'); return; }
  const chart = INTCharts.make('chByPlat');
  if (!chart) return;
  const mD=bm.filter(m=>m.key==='doc'), sD=bm.filter(m=>m.key!=='doc');
  const mN=mD.reduce((s,m)=>s+m.neg,0),mP=mD.reduce((s,m)=>s+m.pos,0),mNe=mD.reduce((s,m)=>s+m.neu,0),mT=mN+mP+mNe;
  const sN=sD.reduce((s,m)=>s+m.neg,0),sP=sD.reduce((s,m)=>s+m.pos,0),sNe=sD.reduce((s,m)=>s+m.neu,0),sT=sN+sP+sNe;
  const negPct=[mT>0?(mN/mT*100).toFixed(1):0,sT>0?(sN/sT*100).toFixed(1):0];
  const posPct=[mT>0?(mP/mT*100).toFixed(1):0,sT>0?(sP/sT*100).toFixed(1):0];
  const neuPct=[mT>0?(mNe/mT*100).toFixed(1):0,sT>0?(sNe/sT*100).toFixed(1):0];
  chart.setOption({
    animation:true,animationDuration:900,backgroundColor:'#fff',
    tooltip:{...EC_TIP,trigger:'axis',axisPointer:{type:'shadow'},
      formatter:params=>{const idx=params[0]?.dataIndex??0;const lbl=['Mass Media','Social Media'][idx];const neg=[mN,sN][idx],pos=[mP,sP][idx],neu=[mNe,sNe][idx],tot=[mT,sT][idx];
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;">${lbl}</div>${[['Negative','#ef4444',neg],['Positive','#2FC6F6',pos],['Neutral','#94a3b8',neu]].map(([n,c,v])=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${c};"></span><span style="font-size:12px;color:#94a3b8;">${n}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(v)} <span style="color:#94a3b8;font-size:10px;">(${tot>0?(v/tot*100).toFixed(1):'0'}%)</span></span></div>`).join('')}`;
      }
    },
    legend:{bottom:0,data:['Negative','Positive','Neutral'],textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},icon:'circle',itemWidth:10,itemHeight:10,itemGap:20},
    grid:{top:24,right:16,bottom:50,left:72},
    xAxis:{type:'category',data:['Mass Media','Social Media'],axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:13,fontWeight:'700',color:'#374151'}},
    yAxis:{type:'value',max:100,min:0,axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:v=>v+'%'}},
    series:[
      {name:'Neutral', type:'bar',stack:'s',data:neuPct.map(v=>parseFloat(v)),itemStyle:{color:'#94a3b8'},barMaxWidth:90},
      {name:'Positive',type:'bar',stack:'s',data:posPct.map(v=>parseFloat(v)),itemStyle:{color:'#2FC6F6'},barMaxWidth:90,label:{show:true,position:'inside',formatter:p=>p.value>8?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'700',color:'#fff'}},
      {name:'Negative',type:'bar',stack:'s',data:negPct.map(v=>parseFloat(v)),itemStyle:{color:'#ef4444',borderRadius:[4,4,0,0]},barMaxWidth:90,label:{show:true,position:'inside',formatter:p=>p.value>8?p.value+'%':'',fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'700',color:'#fff'}},
    ]
  });
  chart.on('click', params=>{
    if(params.componentType!=='series') return;
    const sentMap={'Negative':'neg','Positive':'pos','Neutral':'neu'};
    const rect=chart.getDom().getBoundingClientRect();
    const grp=['Mass Media','Social Media'][params.dataIndex];
    if(grp==='Mass Media') INTPopup.open('doc', sentMap[params.seriesName]||'all', rect.left+rect.width/2, rect.top+params.event.offsetY);
    else INTPopup.showPlatPicker(rect.left+rect.width/2, rect.top+params.event.offsetY, sentMap[params.seriesName]||'all');
  });
  chart.on('mouseover', p=>{ if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  ()=>{ chart.getDom().style.cursor='default'; });
}

/* ─── Mass & Social Pies ─── */
function renderMassSocialPies() {
  const bm = INTData.byMedia;
  const mD=bm.filter(m=>m.key==='doc');
  const mN=mD.reduce((s,m)=>s+m.neg,0),mP=mD.reduce((s,m)=>s+m.pos,0),mNe=mD.reduce((s,m)=>s+m.neu,0);
  hideSk('skMassPie');
  if(mN+mP+mNe>0) renderSovDoughnut('chMassPie',['Negative','Positive','Neutral'],[mN,mP,mNe],['#ef4444','#2FC6F6','#94a3b8']);
  else document.getElementById('chMassPie').parentElement.innerHTML = emptyHtml('Tidak ada data Mass Media');
  const sD=bm.filter(m=>m.key!=='doc');
  const sN=sD.reduce((s,m)=>s+m.neg,0),sP=sD.reduce((s,m)=>s+m.pos,0),sNe=sD.reduce((s,m)=>s+m.neu,0);
  hideSk('skSocialPie');
  if(sN+sP+sNe>0) renderSovDoughnut('chSocialPie',['Negative','Positive','Neutral'],[sN,sP,sNe],['#ef4444','#2FC6F6','#94a3b8']);
  else document.getElementById('chSocialPie').parentElement.innerHTML = emptyHtml('Tidak ada data Social Media');
}

/* ─── Platform Breakdown ─── */
function renderPlatBreakdown() {
  const list = document.getElementById('platBreakdownList');
  const bm   = INTData.byMedia;
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
    const tot=m.neg+m.pos+m.neu;
    const np=tot>0?((m.neg/tot)*100):0, pp=tot>0?((m.pos/tot)*100):0, nep=tot>0?((m.neu/tot)*100):0;
    return `<div class="int-media-row" style="cursor:pointer;" data-plat="${m.key}">
      <div class="int-media-icon" style="background:${platBg[m.key]||'#f1f5f9'};">${platIcons[m.key]||''}</div>
      <div class="int-media-name">${m.label}</div>
      <div class="int-media-bars">
        <div class="int-media-bar-row"><div class="int-media-bar-track"><div class="int-media-bar-fill" style="width:${np.toFixed(1)}%;background:#ef4444;"></div></div><div class="int-media-bar-val" style="color:#ef4444;">${numK(m.neg)}</div></div>
        <div class="int-media-bar-row"><div class="int-media-bar-track"><div class="int-media-bar-fill" style="width:${pp.toFixed(1)}%;background:#2FC6F6;"></div></div><div class="int-media-bar-val" style="color:#2FC6F6;">${numK(m.pos)}</div></div>
        <div class="int-media-bar-row"><div class="int-media-bar-track"><div class="int-media-bar-fill" style="width:${nep.toFixed(1)}%;background:#94a3b8;"></div></div><div class="int-media-bar-val" style="color:#94a3b8;">${numK(m.neu)}</div></div>
      </div>
      <div class="int-media-total">${numFmt(tot)}</div>
    </div>`;
  }).join('');

  setTimeout(intAttachClickHandlers, 50);
}

/* ─── Trend ─── */
function renderTrend() {
  hideSk('skTrend');
  const trend = INTData.trend;
  if (!trend.length) {
    document.getElementById('trendBadge').textContent='No Data';
    document.getElementById('chTrend').parentElement.innerHTML=emptyHtml('Data trend tidak tersedia');
    return;
  }
  const dates=trend.map(d=>d.date);
  const xLabels=dates.map(d=>{ const dt=new Date(d+'T00:00:00'); return `${dt.getDate()}.${dt.toLocaleString('id-ID',{month:'short'})}`; });
  const fmtB=d=>{ const dt=new Date(d+'T00:00:00'); return `${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`; };
  document.getElementById('trendBadge').textContent = `${fmtB(dates[0])} – ${fmtB(dates[dates.length-1])}`;
  const chart = INTCharts.make('chTrend');
  if (!chart) return;
  const makeSeries=(name,data,color)=>({
    name, type:'line', data, smooth:.4,
    symbol:'circle', symbolSize:dates.length<=30?6:0, showSymbol:dates.length<=30,
    itemStyle:{color,borderColor:'#fff',borderWidth:2},
    lineStyle:{color,width:2.5},
    areaStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:color+'22'},{offset:1,color:color+'02'}]}},
    emphasis:{focus:'series',lineStyle:{width:3.5}},
    label:{show:dates.length<=14,position:'top',formatter:p=>p.value>0?numFmt(p.value):'',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:10,color:'#64748b'}
  });
  chart.setOption({
    animation:true,animationDuration:900,animationEasing:'cubicInOut',backgroundColor:'#fff',
    tooltip:{...EC_TIP,trigger:'axis',axisPointer:{type:'line',lineStyle:{color:'#e2e8f0',type:'dashed',width:1.5}},
      formatter:params=>{const di=params[0]?.dataIndex??0,date=dates[di]||'',tot=(params[0]?.value||0)+(params[1]?.value||0)+(params[2]?.value||0);
        const fullDt=date?new Date(date+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'}):'';
        const rows=params.map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};"></span><span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span></div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:4px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${fullDt||date}</div>${rows}<div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;"><span style="font-size:11px;color:#94a3b8;">Total</span><span style="font-weight:700;">${numFmt(tot)}</span></div>`;
      }
    },
    legend:{bottom:0,data:['Negative','Positive','Neutral'],textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},icon:'circle',itemWidth:10,itemHeight:10,itemGap:24},
    grid:{top:28,right:20,bottom:50,left:64},
    xAxis:{type:'category',data:xLabels,boundaryGap:false,axisLine:{lineStyle:{color:'#e2e8f0'}},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}},
    yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'solid',width:1}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}},
    series:[makeSeries('Negative',trend.map(d=>d.neg||0),'#ef4444'),makeSeries('Positive',trend.map(d=>d.pos||0),'#2FC6F6'),makeSeries('Neutral',trend.map(d=>d.neu||0),'#94a3b8')]
  });
  chart.on('click', params=>{
    if(params.componentType!=='series') return;
    const sentMap={'Negative':'neg','Positive':'pos','Neutral':'neu'};
    const rect=chart.getDom().getBoundingClientRect();
    INTPopup.open('all', sentMap[params.seriesName]||'all', rect.left+params.event.offsetX, rect.top+params.event.offsetY);
  });
  chart.on('mouseover', p=>{ if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  ()=>{ chart.getDom().style.cursor='default'; });
}

/* ─── Weekday & Hour ─── */
function renderWeekdayHour() {
  renderTimeChart('chWeekday','skWeekday',INTData.weekday?.weekdays||[],INTData.weekday?.neg||[],INTData.weekday?.pos||[],INTData.weekday?.neu||[],INTData.weekday?.total||[],false);
  renderTimeChart('chHour','skHour',INTData.hour?.hours||[],INTData.hour?.neg||[],INTData.hour?.pos||[],INTData.hour?.neu||[],INTData.hour?.total||[],true);
}

function renderTimeChart(domId, skelId, labels, negData, posData, neuData, totals, isHour=false) {
  hideSk(skelId);
  if (!labels.length||!totals.some(v=>v>0)) { document.getElementById(domId).parentElement.innerHTML=emptyHtml('Data tidak tersedia'); return; }
  const chart = INTCharts.make(domId);
  if (!chart) return;
  const makeS=(name,data,color)=>({name,type:'bar',stack:'s',data:data.map(v=>({value:v,itemStyle:{color}})),emphasis:{focus:'series'}});
  chart.setOption({
    animation:true,animationDuration:800,animationEasing:'elasticOut',backgroundColor:'#fff',
    tooltip:{...EC_TIP,trigger:'axis',axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter:params=>{const idx=params[0]?.dataIndex??0,lbl=labels[idx]||'',tot=totals[idx]||0;
        const rows=[...params].reverse().map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};"></span><span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span></div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${isHour?'Jam ':''}${lbl}</div>${rows}<div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;"><span style="font-size:11px;color:#94a3b8;">Total</span><span style="font-weight:700;">${numFmt(tot)}</span></div>`;
      }
    },
    grid:{top:24,right:16,bottom:40,left:56},
    xAxis:{type:'category',data:labels,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:isHour?9:11,fontWeight:'600',color:'#64748b',interval:isHour?1:0,rotate:isHour?45:0}},
    yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}},
    series:[
      makeS('Neutral', neuData,'#94a3b8'),
      makeS('Positive',posData,'#2FC6F6'),
      {name:'Negative',type:'bar',stack:'s',barMaxWidth:isHour?20:56,
       data:negData.map(v=>({value:v,itemStyle:{color:'#ef4444',borderRadius:[4,4,0,0]}})),
       label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:isHour?9:10,color:'#64748b',formatter:p=>totals[p.dataIndex]>0?numK(totals[p.dataIndex]):''},
       emphasis:{focus:'series'}}
    ]
  });
  chart.on('click', params=>{
    if(params.componentType!=='series') return;
    const sentMap={'Negative':'neg','Positive':'pos','Neutral':'neu'};
    const rect=chart.getDom().getBoundingClientRect();
    INTPopup.open('all', sentMap[params.seriesName]||'all', rect.left+params.event.offsetX, rect.top+params.event.offsetY);
  });
  chart.on('mouseover', p=>{ if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
  chart.on('mouseout',  ()=>{ chart.getDom().style.cursor='default'; });
}

/* ══════════════════════════════════════════════════════
   CSV EXPORT
══════════════════════════════════════════════════════ */
const INTCsv = {
  _copy(text) {
    navigator.clipboard?.writeText(text).catch(()=>{
      const ta=document.createElement('textarea'); ta.value=text;
      ta.style.cssText='position:fixed;opacity:0;'; document.body.appendChild(ta);
      ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
    });
    alert('CSV data tersalin ke clipboard!');
  },
  copyOverview() {
    const {neg,pos,neu}=INTData.totals; const tot=neg+pos+neu;
    this._copy(['sentiment;count;percentage',`Negative;${neg};${tot>0?(neg/tot*100).toFixed(1):0}`,`Positive;${pos};${tot>0?(pos/tot*100).toFixed(1):0}`,`Neutral;${neu};${tot>0?(neu/tot*100).toFixed(1):0}`,`Total;${tot};100`].join('\n'));
  },
  copyMass() {
    const bm=INTData.byMedia.filter(m=>m.key==='doc');
    this._copy(['platform;negative;positive;neutral;total',...bm.map(m=>`${m.label};${m.neg};${m.pos};${m.neu};${m.neg+m.pos+m.neu}`)].join('\n'));
  },
  copySocial() {
    const bm=INTData.byMedia.filter(m=>m.key!=='doc');
    this._copy(['platform;negative;positive;neutral;total',...bm.map(m=>`${m.label};${m.neg};${m.pos};${m.neu};${m.neg+m.pos+m.neu}`)].join('\n'));
  },
  copyTrend() {
    this._copy(['date;negative;positive;neutral;total',...INTData.trend.map(d=>`${d.date};${d.neg};${d.pos};${d.neu};${d.neg+d.pos+d.neu}`)].join('\n'));
  }
};

/* ══════════════════════════════════════════════════════
   CLICK HANDLERS — Stat cards + Platform rows
══════════════════════════════════════════════════════ */
function intAttachClickHandlers() {
  const cardMap = [
    ['valNeg','neg'],['valPos','pos'],['valNeu','neu'],['valTot','all']
  ];
  cardMap.forEach(([elId, sent]) => {
    const card = document.getElementById(elId)?.closest('.int-stat-card');
    if (!card || card._intBound) return;
    card._intBound = true;
    card.style.cursor = 'pointer';
    card.addEventListener('click', () => {
      const r = card.getBoundingClientRect();
      if (sent==='all') INTPopup.open('all','all', r.left+r.width/2, r.top+r.height/2);
      else INTPopup.openSentiment(sent, r.left+r.width/2, r.top+r.height/2);
    });
    const sub = card.querySelector('.int-stat-sub');
    if (sub && !card.querySelector('.int-stat-hint')) {
      const hint=document.createElement('div'); hint.className='int-stat-hint';
      hint.innerHTML=`<svg viewBox="0 0 24 24"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg> Klik untuk lihat detail`;
      sub.after(hint);
    }
  });

  const labelToPlat = {'Mass Media':'doc','X / Twitter':'twit','Facebook':'fb','Instagram':'ig','YouTube':'yt','TikTok':'tiktok'};
  document.querySelectorAll('#platBreakdownList .int-media-row').forEach(row => {
    if (row._intBound) return;
    row._intBound = true;
    const lbl = row.querySelector('.int-media-name')?.textContent?.trim()||'';
    const plat = row.dataset.plat || labelToPlat[lbl] || 'doc';
    row.addEventListener('click', e => {
      const r = row.getBoundingClientRect();
      INTPopup.open(plat, 'all', r.left+r.width/2, r.top+r.height/2);
    });
  });
}

/* ══════════════════════════════════════════════════════
   POPUP
══════════════════════════════════════════════════════ */
const INTPlatMeta = {
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

const intEsc = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

const INTPopup = {
  _drag:false, _ox:0, _oy:0,
  _cache:{}, _allItems:[], _curSent:'all', _curPlat:null,

  init() {
    const popup=document.getElementById('intPopup'), header=document.getElementById('intPopHeader');
    if (!popup||!header) return;
    header.addEventListener('mousedown', e => {
      this._drag=true; const r=popup.getBoundingClientRect();
      this._ox=e.clientX-r.left; this._oy=e.clientY-r.top;
      document.body.style.userSelect='none';
    });
    document.addEventListener('mousemove', e => {
      if (!this._drag) return;
      const vw=window.innerWidth,vh=window.innerHeight;
      popup.style.left=Math.max(0,Math.min(e.clientX-this._ox,vw-popup.offsetWidth))+'px';
      popup.style.top =Math.max(0,Math.min(e.clientY-this._oy,vh-popup.offsetHeight))+'px';
    });
    document.addEventListener('mouseup', ()=>{ this._drag=false; document.body.style.userSelect=''; });
    document.addEventListener('mousedown', e=>{
      const pp=document.getElementById('intPlatPicker');
      if (pp?.classList.contains('visible')&&!pp.contains(e.target)) pp.classList.remove('visible');
    });
  },

  _pos(popup,x,y) {
    const pw=480,ph=600,vw=window.innerWidth,vh=window.innerHeight;
    let left=x+18,top=y-40;
    if (left+pw>vw-12) left=x-pw-18;
    if (top+ph>vh-12)  top=vh-ph-12;
    if (top<8) top=8; if (left<8) left=8;
    popup.style.left=left+'px'; popup.style.top=top+'px';
  },

  async open(platform, sentiment, x, y) {
    const popup=document.getElementById('intPopup'); if(!popup) return;
    INTDetail.close();
    this._curPlat=platform; this._curSent=sentiment||'all';

    let dotColor, title;
    if (sentiment&&sentiment!=='all') {
      dotColor=INTPlatMeta[sentiment]?.color||'#038047';
      const sentLabel={neg:'Negative',pos:'Positive',neu:'Neutral'}[sentiment]||sentiment;
      const platLabel=platform==='all'?'All Media':platform==='social'?'Social Media':(INTPlatMeta[platform]?.label||platform);
      title=`${sentLabel} — ${platLabel}`;
    } else {
      dotColor=platform==='all'?'#038047':platform==='social'?'#038047':(INTPlatMeta[platform]?.color||'#038047');
      title=platform==='all'?'All Media':platform==='social'?'Social Media':(INTPlatMeta[platform]?.label||platform);
    }
    document.getElementById('intPopDot').style.background=dotColor;
    document.getElementById('intPopTitle').textContent=title;
    document.getElementById('intPopMeta').textContent=INTCfg.sd+' – '+INTCfg.ed;
    document.getElementById('intPopCount').textContent='…';

    document.querySelectorAll('.intp-sent-tab').forEach(b=>b.classList.toggle('active',b.dataset.s===this._curSent));

    const list=document.getElementById('intPopList');
    list.innerHTML=`<div class="intp-loading"><div class="intp-spinner"></div>Memuat data…</div>`;
    popup.classList.add('visible');
    this._pos(popup,x,y);

    const cacheKey=`${INTCfg.pid}_${platform}_${INTCfg.sd}_${INTCfg.ed}`;
    try {
      if (!this._cache[cacheKey]) this._cache[cacheKey]=await this._fetch(platform);
      this._allItems=this._cache[cacheKey];
      this._renderFiltered(list);
    } catch(err) {
      list.innerHTML=`<div class="intp-loading" style="color:#94a3b8;"><svg style="width:32px;height:32px;stroke:#e2e8f0;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>Gagal memuat data</div>`;
      document.getElementById('intPopCount').textContent='0';
    }
  },

  openSentiment(sentiment,x,y) { this.open('all',sentiment,x,y); },

  showPlatPicker(x,y,sentiment) {
    const pp=document.getElementById('intPlatPicker'); if(!pp) return;
    pp.dataset.sentiment=sentiment||'all';
    const pw=185,ph=240,vw=window.innerWidth,vh=window.innerHeight;
    let left=x+10,top=y-10;
    if (left+pw>vw-8) left=x-pw-10; if (top+ph>vh-8) top=vh-ph-8; if (top<8) top=8;
    pp.style.left=left+'px'; pp.style.top=top+'px';
    pp.classList.add('visible');
    pp.querySelectorAll('.intpp-btn').forEach(btn=>{
      const plat=btn.getAttribute('onclick').match(/'([^']+)'/)?.[1];
      if (plat) btn.setAttribute('onclick',`INTPopup.openPlatform('${plat}','${sentiment||'all'}')`);
    });
  },

  openPlatform(platform,sentiment) {
    const pp=document.getElementById('intPlatPicker');
    const x=pp?parseFloat(pp.style.left)+90:window.innerWidth/2;
    const y=pp?parseFloat(pp.style.top)+20:window.innerHeight/2;
    if (pp) pp.classList.remove('visible');
    this.open(platform,sentiment||'all',x,y);
  },

  filterSent(sent) {
    this._curSent=sent;
    document.querySelectorAll('.intp-sent-tab').forEach(b=>b.classList.toggle('active',b.dataset.s===sent));
    this._renderFiltered(document.getElementById('intPopList'));
  },

  close() {
    document.getElementById('intPopup')?.classList.remove('visible');
    INTDetail.close();
  },

  async _fetch(platform) {
    const q=`project_id=${INTCfg.pid}&start_date=${INTCfg.sd}&end_date=${INTCfg.ed}&rows=500&start=0`;
    if (platform==='all'||platform==='social') {
      const plats=platform==='social'?['twit','fb','ig','yt','tiktok']:['doc','twit','fb','ig','yt','tiktok'];
      const results=await Promise.allSettled(plats.map(p=>this._fetchOne(p,q)));
      let merged=[]; results.forEach(r=>{ if(r.status==='fulfilled') merged=merged.concat(r.value); });
      merged.sort((a,b)=>(b.date_created||b.created_at||'').localeCompare(a.date_created||a.created_at||''));
      return merged;
    }
    return this._fetchOne(platform,q);
  },

  async _fetchOne(platform,q) {
    const eps={
      doc:    `/mk/api/news/mentions?${q}`,
      twit:   `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
      fb:     `/mk/api/news/fb-top-status?${q}&sub=fblike`,
      ig:     `/mk/api/news/ig-top-status?${q}`,
      yt:     `/mk/api/news/ytb-top-status?${q}`,
      tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
    };
    const url=eps[platform]; if(!url) return [];
    const ctrl=new AbortController(), tid=setTimeout(()=>ctrl.abort(),30000);
    const res=await fetch(url,{signal:ctrl.signal}); clearTimeout(tid);
    if (!res.ok) return [];
    const data=await res.json();
    return Array.isArray(data.data)?data.data:(Array.isArray(data)?data:[]);
  },

  _normSent(item) {
    const raw=String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
    if (raw==='1'||raw==='positive'||raw==='positif') return 'pos';
    if (raw==='-1'||raw==='2'||raw==='negative'||raw==='negatif') return 'neg';
    return 'neu';
  },

  _getFiltered() {
    if (this._curSent==='all') return this._allItems;
    return this._allItems.filter(item=>this._normSent(item)===this._curSent);
  },

  _renderFiltered(list) {
    const items=this._getFiltered();
    document.getElementById('intPopCount').textContent=items.length.toLocaleString();
    const badge=document.getElementById('intPopCount');
    const bColors={neg:'#ef4444',pos:'#2FC6F6',neu:'#94a3b8',all:'var(--primary-green)'};
    badge.style.background=bColors[this._curSent]||'var(--primary-green)';
    this._render(list,items);
  },

  _render(list, items) {
    if (!items.length) {
      list.innerHTML=`<div class="intp-loading" style="color:#94a3b8;"><svg style="width:32px;height:32px;stroke:#e2e8f0;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>Tidak ada mention untuk filter ini</div>`;
      return;
    }
    const SHOW=60;
    const getPlat=item=>{
      const mt=String(item.media_type||item.type||item.tcode||'').toLowerCase();
      if(mt.includes('doc')||mt.includes('news')||mt.includes('berita')) return 'doc';
      if(mt.includes('twit')||mt.includes('twitter')||mt.includes('x')) return 'twit';
      if(mt.includes('fb')||mt.includes('facebook')) return 'fb';
      if(mt.includes('ig')||mt.includes('instagram')) return 'ig';
      if(mt.includes('yt')||mt.includes('youtube')) return 'yt';
      if(mt.includes('tiktok')) return 'tiktok';
      return this._curPlat||'doc';
    };
    list.innerHTML=items.slice(0,SHOW).map(item=>{
      const plat=getPlat(item), meta=INTPlatMeta[plat]||{color:'#038047'};
      const name=(item.from_name||item.page_name||item.author_nickname||item.channel_title||item.channel_name||item.author_name||item.username||item.author_scr_name||item.screen_name||item.publisher||item.source_name||item.name||'Tidak diketahui').trim();
      const isNumericId=/^\d{8,}$/.test(name), displayName=isNumericId?`User ${name.slice(-4)}`:name;
      const rawHandle=(item.author_scr_name||item.screen_name||item.username||item.handle||'').trim();
      const handle=rawHandle&&rawHandle.toLowerCase()!==displayName.toLowerCase()?(['twit','ig','tiktok'].includes(plat)?(rawHandle.startsWith('@')?rawHandle:'@'+rawHandle):rawHandle):'';
      const text=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,155);
      const av=(item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||item.picture||'').trim();
      const words=displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
      const ini=words.length>=2?(words[0][0]+words[words.length-1][0]).toUpperCase():(words[0]?.[0]||displayName[0]||'?').toUpperCase();
      const avHtml=(av&&(av.startsWith('http://')||av.startsWith('https://')))?`<img src="${intEsc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini.replace(/['"]/g,'')}'">`:ini;
      const sent=this._normSent(item), sentLbl={neg:'Neg',pos:'Pos',neu:'Neu'}[sent]||'Neu';
      const dt=(item.date_created||item.created_at||item.publish_date||'').split('T')[0];
      const itemData=intEsc(JSON.stringify(item));
      return `<div class="intp-item" data-item='${itemData}' data-plat="${plat}" onclick="INTPopup._onItemClick(this)">
        <div class="intp-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
        <div class="intp-item-body">
          <div class="intp-item-author">${intEsc(displayName)}</div>
          ${handle?`<div class="intp-item-handle">${intEsc(handle)}</div>`:''}
          <div class="intp-item-text">${intEsc(text||'(tidak ada konten)')}</div>
          <div class="intp-item-footer">
            <span class="intp-sent-badge intp-sent-badge--${sent}">${sentLbl}</span>
            <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
            <span style="font-size:10px;">${meta.label||''}</span>
            ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
          </div>
        </div>
      </div>`;
    }).join('');
    if (items.length>SHOW) {
      list.insertAdjacentHTML('beforeend',`<div style="padding:9px 14px;text-align:center;font-size:11px;font-weight:600;color:#64748b;background:var(--bg-gray-50);border-top:1px dashed var(--border-gray);">+${(items.length-SHOW).toLocaleString()} mentions lainnya</div>`);
    }
  },

  _onItemClick(el) {
    try {
      const raw=el.getAttribute('data-item');
      const item=JSON.parse(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"'));
      INTDetail.open(item, el.dataset.plat||this._curPlat||'doc');
    } catch(e){ console.warn('INT Detail parse error:',e); }
  }
};

/* ══════════════════════════════════════════════════════
   DETAIL PANEL
══════════════════════════════════════════════════════ */
const INTDetail = {
  open(item, platform) {
    const panel=document.getElementById('intDetailPanel'), body=document.getElementById('intDpBody');
    if (!panel||!body) return;
    const meta=INTPlatMeta[platform]||{label:platform,color:'#038047'};
    const name=(item.from_name||item.page_name||item.author_nickname||item.channel_title||item.channel_name||item.author_name||item.username||item.author_scr_name||item.screen_name||item.publisher||item.source_name||item.name||'Tidak diketahui').trim();
    const isNumericId=/^\d{8,}$/.test(name), displayName=isNumericId?`User ${name.slice(-4)}`:name;
    const rawHandle=(item.author_scr_name||item.screen_name||item.username||item.handle||'').trim();
    const handle=rawHandle&&rawHandle.toLowerCase()!==displayName.toLowerCase()?(rawHandle.startsWith('@')?rawHandle:'@'+rawHandle):'';
    const content=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
    const av=(item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||item.picture||'').trim();
    const url=item.url||item.link||'';
    const date=item.date_created||item.created_at||item.publish_date||'';
    const sentRaw=String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
    const sent=sentRaw==='1'||sentRaw==='positive'?'pos':sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'?'neg':'neu';
    const sentLbl={pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];
    document.getElementById('intDpTitle').textContent=displayName;
    const words=displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
    const ini=words.length>=2?(words[0][0]+words[words.length-1][0]).toUpperCase():(words[0]?.[0]||displayName[0]||'?').toUpperCase();
    const avHtml=(av&&(av.startsWith('http://')||av.startsWith('https://')))?`<img src="${intEsc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini.replace(/['"]/g,'')}'">`  :ini;
    let dtFmt='';
    if (date) { try { dtFmt=new Date(date).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); } catch(e){ dtFmt=date.split('T')[0]; } }
    let mediaHtml='';
    if (platform==='yt') {
      const ytId=(url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/)||[])[1];
      if (ytId) mediaHtml=`<div class="intdp-media-wrap"><iframe style="width:100%;height:210px;border:none;display:block;" src="https://www.youtube.com/embed/${ytId}?rel=0&modestbranding=1" allowfullscreen></iframe></div>`;
    } else {
      const imgUrl=item.image_url||item.thumbnail||item.media_url||item.picture||'';
      if (imgUrl) mediaHtml=`<div class="intdp-media-wrap"><img class="intdp-media-img" src="${intEsc(imgUrl)}" onerror="this.parentElement.style.display='none'"></div>`;
    }
    const statsMap={
      twit:[['Retweet',item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0],['Quote',item.num_quote||0]],
      fb:  [['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],
      ig:  [['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],
      yt:  [['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||0]],
      tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],
      doc: [['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]],
    };
    const stats=statsMap[platform]||[];
    const statsHtml=stats.some(s=>parseInt(s[1])>0)?`<div class="intdp-stats-grid">${stats.map(([l,v])=>`<div class="intdp-stat-box"><div class="intdp-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="intdp-stat-lbl">${l}</div></div>`).join('')}</div>`:'';
    body.innerHTML=`
      <div class="intdp-avatar-row">
        <div class="intdp-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
        <div>
          <div class="intdp-author-name">${intEsc(displayName)}</div>
          ${handle?`<div class="intdp-author-handle">${intEsc(handle)}</div>`:''}
          <span style="background:${meta.color}22;color:${meta.color};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;margin-top:4px;">${meta.label}</span>
        </div>
      </div>
      ${dtFmt?`<div class="intdp-meta-row"><span>${dtFmt}</span></div>`:''}
      <span class="intdp-sent-badge intdp-sent-badge--${sent}">${sentLbl}</span>
      ${mediaHtml}
      ${content?`<div class="intdp-content-text">${intEsc(content)}</div>`:''}
      ${statsHtml}
      ${url?`<a href="${intEsc(url)}" target="_blank" rel="noopener noreferrer" class="intdp-link-btn"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Lihat Sumber Asli</a>`:''}`;
    panel.classList.add('visible');
  },
  close() { document.getElementById('intDetailPanel')?.classList.remove('visible'); }
};

/* ══════════════════════════════════════════════════════
   PAGE CONTROLLER
══════════════════════════════════════════════════════ */
const INTPage = {
  reload() {
    INTCharts.disposeAll();
    INTPopup._cache={};
    INTData.trend=[]; INTData.byMedia=[]; INTData.weekday=null; INTData.hour=null;
    loadSentiment();
    loadTimeData();
  },
  init() {
    INTPopup.init();
    loadSentiment();
    loadTimeData();
  } 
};

document.addEventListener('DOMContentLoaded', () => INTPage.init());
</script>
@endsection