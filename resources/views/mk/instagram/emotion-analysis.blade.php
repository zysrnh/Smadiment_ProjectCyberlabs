@extends('mk.layouts.app')

@section('title', 'Instagram Emotion Analysis - SMADIMENT')

@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --primary-green:        #038047;
    --primary-green-dark:   #026738;
    --primary-green-light:  rgba(3,128,71,0.08);
    --primary-green-border: rgba(3,128,71,0.2);

    /* Instagram gradient brand colors */
    --ig:             #E1306C;
    --ig-dark:        #c13584;
    --ig-gradient:    linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    --ig-light:       rgba(225,48,108,0.08);
    --ig-border:      rgba(225,48,108,0.2);

    --e-joy:          #F59E0B;
    --e-trust:        #10B981;
    --e-fear:         #6366F1;
    --e-surprise:     #3B82F6;
    --e-sadness:      #8B5CF6;
    --e-disgust:      #A855F7;
    --e-anger:        #EF4444;
    --e-anticipation: #F97316;

    --text-primary:   #1a202c;
    --text-secondary: #64748b;
    --text-muted:     #94a3b8;
    --bg-white:       #ffffff;
    --bg-body:        #f0f4f8;
    --bg-gray-50:     #f8fafc;
    --bg-gray-100:    #f1f5f9;
    --border-gray:    #e2e8f0;
    --border-light:   #f1f5f9;
    --shadow-sm:      0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
    --shadow-lg:      0 10px 15px -3px rgba(0,0,0,.1);
    --radius:         16px;
    --radius-sm:      12px;
    --radius-xs:      8px;
    --transition:     all 0.2s cubic-bezier(0.4,0,0.2,1);
    --font:           'Poppins', -apple-system, sans-serif;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: var(--font); background: var(--bg-body); color: var(--text-primary); }
  .fme-page { padding: 24px; max-width: 1600px; margin: 0 auto; min-height: 100vh; }

  /* ── PAGE HEADER ── */
  .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:12px; }
  .page-header-left h1 { font-size:28px; font-weight:700; color:var(--text-primary); margin:0 0 6px; letter-spacing:-.4px; }
  .page-header-left p  { font-size:14px; color:var(--text-secondary); font-weight:500; margin:0; }
  .ms-refresh-btn { display:flex; align-items:center; gap:8px; padding:10px 20px; background:linear-gradient(135deg,#1a202c 0%,#2d3748 100%); color:#fff; border:none; border-radius:var(--radius-sm); font-family:var(--font); font-size:13px; font-weight:600; cursor:pointer; transition:var(--transition); box-shadow:0 4px 14px rgba(0,0,0,.2); }
  .ms-refresh-btn:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,.25); }
  .ms-refresh-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; }

  /* ── FILTER CARD ── */
  .filter-card { background:var(--bg-white); border-radius:var(--radius); padding:20px 24px; margin-bottom:24px; box-shadow:var(--shadow-sm); border:1px solid var(--border-gray); }
  .filter-content { display:flex; align-items:flex-end; gap:16px; flex-wrap:wrap; }
  .filter-group { display:flex; flex-direction:column; gap:6px; }
  .filter-label { font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.5px; }
  .date-picker-trigger { display:flex; align-items:center; gap:10px; padding:10px 16px; background:var(--bg-gray-50); border:1px solid var(--border-gray); border-radius:var(--radius-sm); font-family:var(--font); font-size:14px; font-weight:500; color:var(--text-primary); cursor:pointer; transition:var(--transition); min-width:300px; }
  .date-picker-trigger:hover { border-color:var(--ig); background:var(--bg-white); box-shadow:0 0 0 3px var(--ig-light); }
  .date-picker-trigger svg { width:16px; height:16px; color:var(--text-secondary); flex-shrink:0; }
  .date-picker-trigger span { flex:1; text-align:left; }
  .apply-btn { display:flex; align-items:center; gap:8px; padding:10px 24px; background:var(--ig-gradient); color:#fff; border:none; border-radius:var(--radius-sm); font-family:var(--font); font-size:14px; font-weight:600; cursor:pointer; transition:var(--transition); box-shadow:0 4px 12px rgba(225,48,108,.25); white-space:nowrap; }
  .apply-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(225,48,108,.35); }
  .apply-btn svg { width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:2.5; stroke-linecap:round; }

  /* ── DATE PICKER MODAL ── */
  .date-picker-modal { position:fixed; inset:0; z-index:10000; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,.5); backdrop-filter:blur(8px); }
  .date-picker-modal.show { display:flex; }
  .date-picker-overlay { position:absolute; inset:0; cursor:pointer; }
  .date-picker-container { position:relative; z-index:1; background:#fff; border-radius:var(--radius); box-shadow:0 25px 50px rgba(0,0,0,.3); display:flex; max-width:900px; width:90%; max-height:90vh; animation:dpUp .3s ease-out; }
  @keyframes dpUp { from{opacity:0;transform:translateY(20px) scale(.95)}to{opacity:1;transform:translateY(0) scale(1)} }
  .date-picker-sidebar { width:180px; background:var(--bg-gray-50); border-right:1px solid var(--border-gray); padding:16px 12px; border-radius:var(--radius) 0 0 var(--radius); display:flex; flex-direction:column; gap:4px; flex-shrink:0; }
  .date-preset { padding:10px 16px; background:transparent; border:none; border-radius:var(--radius-xs); font-family:var(--font); font-size:13px; font-weight:500; color:var(--text-primary); text-align:left; cursor:pointer; transition:var(--transition); }
  .date-preset:hover { background:var(--bg-white); color:var(--ig); }
  .date-preset.active { background:var(--ig); color:#fff; }
  .date-picker-content { flex:1; padding:24px; display:flex; flex-direction:column; overflow:hidden; }
  .date-picker-header { display:flex; align-items:flex-start; gap:20px; margin-bottom:20px; }
  .nav-btn { width:36px; height:36px; border-radius:var(--radius-xs); background:var(--bg-gray-50); border:1px solid var(--border-gray); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:var(--transition); flex-shrink:0; }
  .nav-btn:hover { background:var(--ig); border-color:var(--ig); color:#fff; }
  .nav-btn svg { width:20px; height:20px; }
  .calendars-wrapper { display:flex; gap:24px; flex:1; }
  .calendar { flex:1; display:flex; flex-direction:column; }
  .calendar-month { font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:16px; text-align:center; }
  .calendar-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:8px; }
  .weekday { text-align:center; font-size:11px; font-weight:700; color:var(--text-secondary); padding:8px 0; }
  .calendar-days { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
  .calendar-day { aspect-ratio:1; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:500; border-radius:var(--radius-xs); cursor:pointer; transition:var(--transition); color:var(--text-primary); background:transparent; border:none; padding:0; font-family:var(--font); }
  .calendar-day:hover:not(.disabled):not(.other-month) { background:var(--bg-gray-100); }
  .calendar-day.other-month { color:#cbd5e1; cursor:default; }
  .calendar-day.disabled { color:#e2e8f0; cursor:not-allowed; }
  .calendar-day.today { border:2px solid var(--ig); }
  .calendar-day.selected { background:var(--ig); color:#fff; }
  .calendar-day.in-range { background:var(--ig-light); color:var(--ig); }
  .date-picker-display { padding:16px 20px; background:var(--bg-gray-50); border-radius:var(--radius-sm); text-align:center; margin-bottom:20px; border:1px solid var(--border-gray); }
  .date-picker-display span { font-size:14px; font-weight:600; color:var(--text-primary); }
  .date-picker-footer { display:flex; gap:12px; justify-content:flex-end; }
  .cancel-btn { padding:10px 24px; border-radius:10px; font-family:var(--font); font-size:14px; font-weight:600; cursor:pointer; transition:var(--transition); border:none; background:var(--bg-gray-100); color:var(--text-primary); }
  .cancel-btn:hover { background:var(--border-gray); }
  .apply-date-btn { padding:10px 24px; border-radius:10px; font-family:var(--font); font-size:14px; font-weight:600; cursor:pointer; transition:var(--transition); border:none; background:var(--ig-gradient); color:#fff; box-shadow:0 4px 12px rgba(225,48,108,.25); }
  .apply-date-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(225,48,108,.35); }

  /* ── SECTION HEADER ── */
  .ms-section-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; margin-top:4px; }
  .ms-section-icon { width:36px; height:36px; border-radius:var(--radius-sm); background:var(--ig-light); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .ms-section-icon svg { width:18px; height:18px; stroke:var(--ig); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
  .ms-section-title { font-size:13px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.8px; }
  .ms-section-line { flex:1; height:1.5px; background:var(--border-gray); border-radius:1px; }

  /* ── STAT CARDS ── */
  .ms-stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px; }
  .ms-stat-card { background:var(--bg-white); border:1px solid var(--border-gray); border-radius:var(--radius); padding:20px 22px; box-shadow:var(--shadow-sm); transition:var(--transition); position:relative; overflow:hidden; cursor:default; }
  .ms-stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:var(--stat-bar,var(--ig)); opacity:0; transition:opacity .25s; }
  .ms-stat-card:hover { box-shadow:var(--shadow-lg); border-color:var(--ig-border); transform:translateY(-2px); }
  .ms-stat-card:hover::before { opacity:1; }
  .ms-stat-card--joy          { --stat-bar:linear-gradient(90deg,#F59E0B,#D97706); }
  .ms-stat-card--trust        { --stat-bar:linear-gradient(90deg,#10B981,#059669); }
  .ms-stat-card--anticipation { --stat-bar:linear-gradient(90deg,#F97316,#EA580C); }
  .ms-stat-card--surprise     { --stat-bar:linear-gradient(90deg,#3B82F6,#2563EB); }
  .ms-stat-label { font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px; display:flex; align-items:center; gap:6px; }
  .ms-stat-dot   { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
  .ms-stat-value { font-size:32px; font-weight:700; color:var(--text-primary); letter-spacing:-1px; line-height:1; min-height:40px; display:flex; align-items:center; }
  .ms-stat-sub   { font-size:11px; color:var(--text-muted); font-weight:500; margin-top:7px; }

  /* ── CARD ── */
  .do-card { background:var(--bg-white); border:1px solid var(--border-gray); border-radius:var(--radius); overflow:hidden; display:flex; flex-direction:column; box-shadow:var(--shadow-sm); transition:var(--transition); position:relative; }
  .do-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; opacity:0; transition:opacity .3s; }
  .do-card--ig::before  { background:var(--ig-gradient); }
  .do-card--ink::before { background:linear-gradient(90deg,#1a202c,#334155); }
  .do-card:hover { box-shadow:var(--shadow-lg); }
  .do-card:hover::before { opacity:1; }
  .do-card-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-gray); flex-shrink:0; gap:12px; flex-wrap:wrap; }
  .do-card-head-left { display:flex; align-items:center; gap:12px; min-width:0; }
  .do-head-icon { width:40px; height:40px; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .do-head-icon--ig  { background:var(--ig-light); }
  .do-head-icon--ig svg { stroke:var(--ig); }
  .do-head-icon--ink { background:rgba(26,32,44,.06); }
  .do-head-icon--ink svg { stroke:#1a202c; }
  .do-head-icon svg { width:20px; height:20px; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
  .do-card-title    { font-size:15px; font-weight:700; color:var(--text-primary); line-height:1.3; }
  .do-card-subtitle { font-size:11px; color:var(--text-muted); font-weight:500; margin-top:2px; }
  .do-badge { display:inline-flex; align-items:center; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; background:var(--bg-gray-100); color:var(--text-secondary); white-space:nowrap; flex-shrink:0; }
  .do-badge--ig { background:rgba(225,48,108,.1); color:#9b1239; }
  .do-card-body { padding:20px; flex:1; }

  /* ── GRID ── */
  .ms-grid-2 { display:grid; grid-template-columns:1fr 380px; gap:20px; margin-bottom:20px; }
  .ms-mb20 { margin-bottom:20px; }
  .ms-ch-360 { position:relative; height:360px; }
  .ms-ch-340 { position:relative; height:340px; }

  /* ── TABS ── */
  .ms-tabs { display:flex; gap:4px; background:var(--bg-white); border:1px solid var(--border-gray); border-radius:var(--radius); padding:6px; box-shadow:var(--shadow-sm); overflow-x:auto; scrollbar-width:none; flex-wrap:nowrap; }
  .ms-tabs::-webkit-scrollbar { display:none; }
  .ms-tab-btn { display:flex; align-items:center; justify-content:center; gap:7px; padding:9px 18px; border-radius:var(--radius-sm); border:none; background:transparent; font-family:var(--font); font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; transition:var(--transition); white-space:nowrap; }
  .ms-tab-btn:hover { background:var(--bg-gray-50); color:var(--text-primary); }
  .ms-tab-btn.active { background:var(--ig-gradient); color:#fff; box-shadow:0 4px 12px rgba(225,48,108,.25); }
  .ms-tab-chip { display:inline-flex; align-items:center; justify-content:center; min-width:22px; height:20px; padding:0 6px; border-radius:10px; font-size:10px; font-weight:800; background:rgba(255,255,255,.22); color:inherit; }
  .ms-tab-btn:not(.active) .ms-tab-chip { background:var(--bg-gray-100); color:var(--text-muted); }

  /* ── EXPORT BTN ── */
  .ms-export-btn { display:flex; align-items:center; gap:5px; padding:7px 14px; background:var(--bg-gray-100); border:1px solid var(--border-gray); border-radius:var(--radius-xs); font-family:var(--font); font-size:12px; font-weight:600; color:var(--text-secondary); cursor:pointer; transition:var(--transition); white-space:nowrap; }
  .ms-export-btn:hover { background:var(--ig); border-color:var(--ig); color:#fff; }
  .ms-export-btn svg { width:12px; height:12px; stroke:currentColor; fill:none; stroke-width:2.2; }

  /* ── POST LIST ── */
  .fme-post-list { display:flex; flex-direction:column; }
  .fme-post { display:flex; align-items:flex-start; gap:14px; padding:16px 20px; border-bottom:1px solid var(--border-light); transition:background .15s; cursor:pointer; }
  .fme-post:last-child { border-bottom:none; }
  .fme-post:hover { background:#fff5f8; }
  .fme-post-rank { width:28px; height:28px; border-radius:50%; background:var(--bg-gray-100); border:1.5px solid var(--border-gray); display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; color:var(--text-muted); flex-shrink:0; margin-top:6px; }
  .fme-post-rank--1 { background:linear-gradient(135deg,#ffd700,#f59e0b); color:#7c5900; border-color:#ffd700; }
  .fme-post-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af); color:#3d3d3d; border-color:#c0c0c0; }
  .fme-post-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820); color:#fff; border-color:#cd7f32; }
  .fme-post-av { width:46px; height:46px; border-radius:50%; flex-shrink:0; background:var(--ig-gradient); color:#fff; font-weight:700; font-size:14px; display:flex; align-items:center; justify-content:center; border:2px solid var(--border-gray); overflow:hidden; }
  .fme-post-av img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
  .fme-post-body { flex:1; min-width:0; }
  .fme-post-author { font-size:13px; font-weight:700; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .fme-post-date   { font-size:10.5px; color:var(--text-muted); font-weight:400; margin-top:1px; margin-bottom:6px; }
  .fme-post-text   { font-size:12.5px; color:var(--text-secondary); line-height:1.6; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:10px; word-break:break-word; }
  .fme-post-stats  { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
  .fme-post-metric { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; background:var(--bg-gray-100); border:1px solid var(--border-gray); color:var(--text-secondary); white-space:nowrap; }
  .fme-post-metric svg { width:11px; height:11px; fill:none; stroke-width:2; stroke-linecap:round; flex-shrink:0; }
  .fme-post-metric--ig    { background:var(--ig-light); border-color:var(--ig-border); color:var(--ig); }
  .fme-post-metric--ig svg { stroke:var(--ig); }
  .fme-post-metric--green { background:rgba(16,185,129,.08); border-color:rgba(16,185,129,.2); color:#10b981; }
  .fme-post-metric--green svg { stroke:#10b981; }
  .fme-post-metric--amber { background:rgba(245,158,11,.08); border-color:rgba(245,158,11,.2); color:#f59e0b; }
  .fme-post-metric--amber svg { stroke:#f59e0b; }
  .fme-view-link { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:700; color:var(--ig); text-decoration:none; padding:3px 9px; border-radius:20px; background:var(--ig-light); border:1px solid var(--ig-border); transition:var(--transition); margin-left:auto; }
  .fme-view-link:hover { background:var(--ig); color:#fff; }
  .fme-view-link svg { width:9px; height:9px; stroke:currentColor; fill:none; stroke-width:2.5; }

  /* ── THUMBNAIL (portrait untuk Instagram) ── */
  .fme-post-thumb { width:80px; height:100px; border-radius:10px; flex-shrink:0; overflow:hidden; border:2px solid var(--border-gray); background:#1a202c; position:relative; align-self:center; box-shadow:0 4px 12px rgba(0,0,0,.08); }
  .fme-post-thumb img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .2s ease; }
  .fme-post:hover .fme-post-thumb img { transform:scale(1.06); }
  .fme-post-thumb-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:24px; background:linear-gradient(135deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }

  /* ── TYPE BADGE (image / video / reel) ── */
  .type-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; }
  .type-image { background:rgba(99,102,241,.1); color:#4338ca; border:1px solid rgba(99,102,241,.2); }
  .type-video { background:rgba(239,68,68,.1); color:#b91c1c; border:1px solid rgba(239,68,68,.2); }

  /* ── EMOTION BADGE ── */
  .emo-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; text-transform:capitalize; letter-spacing:.2px; white-space:nowrap; }
  .emo-badge .emo-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
  .emo-joy          { background:rgba(245,158,11,.1);  color:#92400e; border:1px solid rgba(245,158,11,.3); }
  .emo-trust        { background:rgba(16,185,129,.1);  color:#065f46; border:1px solid rgba(16,185,129,.3); }
  .emo-fear         { background:rgba(99,102,241,.1);  color:#3730a3; border:1px solid rgba(99,102,241,.3); }
  .emo-surprise     { background:rgba(59,130,246,.1);  color:#1e40af; border:1px solid rgba(59,130,246,.3); }
  .emo-sadness      { background:rgba(139,92,246,.1);  color:#5b21b6; border:1px solid rgba(139,92,246,.3); }
  .emo-disgust      { background:rgba(168,85,247,.1);  color:#6b21a8; border:1px solid rgba(168,85,247,.3); }
  .emo-anger        { background:rgba(239,68,68,.1);   color:#991b1b; border:1px solid rgba(239,68,68,.3); }
  .emo-anticipation { background:rgba(249,115,22,.1);  color:#9a3412; border:1px solid rgba(249,115,22,.3); }

  /* ── SENTIMENT BADGE ── */
  .sent-badge { display:inline-flex; align-items:center; justify-content:center; padding:3px 9px; border-radius:20px; font-size:9.5px; font-weight:800; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
  .sent-positive { background:linear-gradient(135deg,#d1fae5,#a7f3d0); color:#065f46; border:1px solid #6ee7b7; }
  .sent-negative { background:linear-gradient(135deg,#fee2e2,#fecaca); color:#991b1b; border:1px solid #fca5a5; }
  .sent-neutral  { background:var(--bg-gray-100); color:var(--text-secondary); border:1px solid var(--border-gray); }

  /* ── PAGINATION ── */
  .fme-pagination { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-top:1px solid var(--border-light); flex-wrap:wrap; gap:10px; }
  .fme-pag-info { font-size:12px; color:var(--text-muted); font-weight:500; }
  .fme-pag-controls { display:flex; align-items:center; gap:4px; }
  .fme-pag-btn { min-width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; padding:0 8px; border-radius:var(--radius-xs); border:1px solid var(--border-gray); background:var(--bg-white); font-family:var(--font); font-size:12px; font-weight:600; color:var(--text-secondary); cursor:pointer; transition:var(--transition); user-select:none; }
  .fme-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--ig); color:var(--ig); background:var(--ig-light); }
  .fme-pag-btn.is-active { background:var(--ig); border-color:var(--ig); color:#fff; }
  .fme-pag-btn:disabled { opacity:.4; cursor:not-allowed; }
  .fme-pag-btn svg { width:13px; height:13px; stroke:currentColor; fill:none; stroke-width:2.2; }

  /* ── SKELETON ── */
  .loading-skeleton { background:linear-gradient(90deg,var(--bg-gray-50) 25%,#e2e8f0 50%,var(--bg-gray-50) 75%); background-size:200% 100%; animation:shimmer 1.5s ease-in-out infinite; border-radius:var(--radius-xs); }
  @keyframes shimmer { 0%{background-position:200% 0}100%{background-position:-200% 0} }
  .skel-overlay { position:absolute; inset:0; z-index:3; border-radius:inherit; }

  /* ── EMPTY ── */
  .do-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:56px 20px; gap:10px; }
  .do-empty svg { width:40px; height:40px; stroke:var(--border-gray); fill:none; stroke-width:1.5; }
  .do-empty-text { font-size:13px; font-weight:600; color:var(--text-secondary); }

  /* ── ALERT ── */
  .alert { padding:16px 20px; border-radius:var(--radius-sm); margin-bottom:24px; font-size:14px; font-weight:500; display:flex; align-items:center; gap:12px; }
  .alert-warning { background:#fef3c7; color:#92400e; border:1px solid #fcd34d; }
  .alert svg { width:20px; height:20px; stroke:currentColor; fill:none; flex-shrink:0; }

  /* ── MODAL ── */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.75); z-index:9999; align-items:center; justify-content:center; }
  .modal-overlay.active { display:flex; }
  @keyframes slideUp { from{opacity:0;transform:translateY(20px) scale(.95)}to{opacity:1;transform:none} }
  .modal-content { background:#fff; border-radius:var(--radius); width:90%; max-width:780px; max-height:90vh; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,.4); animation:slideUp .25s cubic-bezier(.4,0,.2,1); display:flex; flex-direction:column; }
  .modal-header { padding:18px 24px; border-bottom:1px solid var(--border-gray); display:flex; justify-content:space-between; align-items:center; flex-shrink:0; background:var(--ig-gradient); }
  .modal-header h3 { font-size:16px; font-weight:700; color:#fff; margin:0; }
  .modal-close { width:34px; height:34px; border-radius:var(--radius-xs); background:rgba(255,255,255,.2); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff; transition:var(--transition); }
  .modal-close:hover { background:rgba(255,255,255,.35); }
  .modal-close svg { width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:2.5; }
  .modal-body { padding:24px; overflow-y:auto; flex:1; }
  .modal-body::-webkit-scrollbar { width:5px; }
  .modal-body::-webkit-scrollbar-thumb { background:var(--border-gray); border-radius:3px; }
  .modal-stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:18px; }
  .modal-stat-box { text-align:center; padding:14px 8px; background:var(--bg-gray-50); border-radius:var(--radius-sm); border:1px solid var(--border-gray); }
  .modal-stat-value { font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:3px; }
  .modal-stat-value.v-likes { color:var(--ig); }
  .modal-stat-value.v-cmts  { color:#f59e0b; }
  .modal-stat-label { font-size:10px; color:var(--text-secondary); font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
  .modal-emo-bar { display:flex; align-items:center; gap:10px; margin-bottom:7px; }
  .modal-emo-label { font-size:11.5px; font-weight:600; color:var(--text-primary); width:90px; text-transform:capitalize; display:flex; align-items:center; gap:5px; }
  .modal-emo-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
  .modal-emo-track { flex:1; height:9px; background:var(--bg-gray-100); border-radius:5px; overflow:hidden; }
  .modal-emo-fill  { height:100%; border-radius:5px; transition:width .6s ease; }
  .modal-emo-count { font-size:11px; font-weight:700; color:var(--text-primary); width:28px; text-align:right; }
  .modal-actions { display:flex; gap:10px; margin-top:18px; }
  .modal-btn { flex:1; padding:12px 18px; border-radius:var(--radius-sm); font-family:var(--font); font-size:13px; font-weight:700; cursor:pointer; transition:var(--transition); display:flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; border:none; }
  .modal-btn.primary   { background:var(--ig-gradient); color:#fff; box-shadow:0 4px 14px rgba(225,48,108,.25); }
  .modal-btn.primary:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(225,48,108,.35); }
  .modal-btn.secondary { background:var(--bg-gray-100); color:var(--text-primary); border:1px solid var(--border-gray); }
  .modal-btn.secondary:hover { background:var(--border-gray); }
  .modal-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
  .msdp-section-heading { display:flex; align-items:center; gap:8px; margin-bottom:10px; }
  .msdp-section-heading-icon { width:26px; height:26px; border-radius:8px; display:flex; align-items:center; justify-content:center; }
  .msdp-section-title { font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.7px; color:#64748b; white-space:nowrap; }
  .msdp-section-line { flex:1; height:1px; background:#e8edf2; }

  @media (max-width:1024px) { .ms-grid-2 { grid-template-columns:1fr; } }
  @media (max-width:768px) {
    .fme-page { padding:16px; }
    .ms-stat-grid { grid-template-columns:1fr 1fr; }
    .filter-content { flex-direction:column; align-items:stretch; }
    .apply-btn { width:100%; justify-content:center; }
    .date-picker-container { flex-direction:column; width:96%; }
    .date-picker-sidebar { width:100%; flex-direction:row; overflow-x:auto; border-right:none; border-bottom:1px solid var(--border-gray); border-radius:var(--radius) var(--radius) 0 0; }
    .date-preset { white-space:nowrap; }
    .calendars-wrapper { flex-direction:column; }
    .fme-post-thumb { display:none; }
  }
</style>
@endsection

@section('content')
<div class="fme-page">

  {{-- PAGE HEADER --}}
  <div class="page-header">
    <div class="page-header-left">
      <h1>
        <svg viewBox="0 0 24 24" style="width:26px;height:26px;display:inline-block;vertical-align:middle;margin-right:10px;margin-top:-3px;fill:none;stroke:#E1306C;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
          <circle cx="12" cy="12" r="10"/><path d="M8 12l2 2 4-4"/>
        </svg>
        Instagram Emotion Analysis
      </h1>
      <p>Analisis pola emosi pada postingan Instagram berdasarkan Plutchik Wheel — joy, trust, fear, surprise, sadness, disgust, anger, anticipation</p>
    </div>
    <button class="ms-refresh-btn" onclick="EmoApp.reload()">
      <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
      Refresh
    </button>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <span>Tidak ada project yang dipilih. Pilih project dari sidebar untuk melihat emotion analysis.</span>
  </div>
  @else

  {{-- FILTER --}}
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.instagram.emotion-analysis') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hSD" value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hED" value="{{ $endDate }}">
      <div class="filter-content">
        <div class="filter-group">
          <label class="filter-label">Periode</label>
          <button type="button" class="date-picker-trigger" id="dpTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="dpDisplay">{{ $startDate }} – {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <div class="filter-group" style="margin-left:auto;">
          <label class="filter-label" style="opacity:0;pointer-events:none;">x</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Terapkan Filter
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- DATE PICKER MODAL --}}
  <div class="date-picker-modal" id="dpModal">
    <div class="date-picker-overlay" onclick="EmoDP.close()"></div>
    <div class="date-picker-container">
      <div class="date-picker-sidebar">
        <button class="date-preset" data-p="today">Today</button>
        <button class="date-preset" data-p="yesterday">Yesterday</button>
        <button class="date-preset" data-p="last7">Last 7 Days</button>
        <button class="date-preset" data-p="last30">Last 30 Days</button>
        <button class="date-preset" data-p="thismonth">This Month</button>
        <button class="date-preset" data-p="lastmonth">Last Month</button>
        <button class="date-preset active" data-p="custom">Custom Range</button>
      </div>
      <div class="date-picker-content">
        <div class="date-picker-header">
          <button class="nav-btn" onclick="EmoDP.nav(-1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="dpCal1"></div>
            <div class="calendar" id="dpCal2"></div>
          </div>
          <button class="nav-btn" onclick="EmoDP.nav(1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="date-picker-display">
          <span id="dpRangeText">{{ $startDate }} – {{ $endDate }}</span>
        </div>
        <div class="date-picker-footer">
          <button class="cancel-btn" onclick="EmoDP.close()">Batal</button>
          <button class="apply-date-btn" onclick="EmoDP.apply()">Terapkan</button>
        </div>
      </div>
    </div>
  </div>

  {{-- STAT CARDS --}}
  <div class="ms-section-header">
    <div class="ms-section-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
    <span class="ms-section-title">Ringkasan Emosi Teratas</span>
    <div class="ms-section-line"></div>
  </div>
  <div class="ms-stat-grid">
    <div class="ms-stat-card ms-stat-card--joy">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#F59E0B;"></span>Joy</div>
      <div class="ms-stat-value" id="statJoy"><div class="loading-skeleton" style="height:36px;width:100px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub" id="statJoyPct">— dari total post</div>
    </div>
    <div class="ms-stat-card ms-stat-card--trust">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#10B981;"></span>Trust</div>
      <div class="ms-stat-value" id="statTrust"><div class="loading-skeleton" style="height:36px;width:100px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub" id="statTrustPct">— dari total post</div>
    </div>
    <div class="ms-stat-card ms-stat-card--anticipation">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#F97316;"></span>Anticipation</div>
      <div class="ms-stat-value" id="statAnticipation"><div class="loading-skeleton" style="height:36px;width:100px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub" id="statAnticipationPct">— dari total post</div>
    </div>
    <div class="ms-stat-card ms-stat-card--surprise">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#3B82F6;"></span>Surprise</div>
      <div class="ms-stat-value" id="statSurprise"><div class="loading-skeleton" style="height:36px;width:100px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub" id="statSurprisePct">— dari total post</div>
    </div>
  </div>

  {{-- CHARTS ROW --}}
  <div class="ms-grid-2 ms-mb20">
    <div class="do-card do-card--ig">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--ig">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </div>
          <div>
            <div class="do-card-title">Distribusi Emosi</div>
            <div class="do-card-subtitle" id="barSubtitle">Jumlah post per emosi</div>
          </div>
        </div>
        <span class="do-badge do-badge--ig" id="barBadge">—</span>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-360">
          <div id="chartBar" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="skBar"></div>
        </div>
      </div>
    </div>
    <div class="do-card do-card--ink">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--ink">
            <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div>
            <div class="do-card-title">Emotion Radar</div>
            <div class="do-card-subtitle">Plutchik wheel distribution</div>
          </div>
        </div>
        <span class="do-badge">Radar</span>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-360">
          <div id="chartRadar" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="skRadar"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- TRENDS --}}
  <div class="do-card do-card--ig ms-mb20">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <div class="do-head-icon do-head-icon--ig">
          <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div>
          <div class="do-card-title">Tren Emosi Harian</div>
          <div class="do-card-subtitle">Aktivitas emosi per hari pada periode yang dipilih</div>
        </div>
      </div>
      <span class="do-badge do-badge--ig">Trends</span>
    </div>
    <div class="do-card-body">
      <div class="ms-ch-340">
        <div id="chartTrends" style="width:100%;height:100%;"></div>
        <div class="loading-skeleton skel-overlay" id="skTrends"></div>
      </div>
    </div>
  </div>

  {{-- TABLE SECTION --}}
  <div class="ms-section-header">
    <div class="ms-section-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg></div>
    <span class="ms-section-title">Data Postingan</span>
    <div class="ms-section-line"></div>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
    <div class="ms-tabs" style="margin-bottom:0;flex:1;min-width:0;">
      <button class="ms-tab-btn active" id="tabAll" onclick="EmoApp.switchFilter('all',this)">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Semua <span class="ms-tab-chip" id="chipAll">—</span>
      </button>
      <button class="ms-tab-btn" id="tabJoy"          onclick="EmoApp.switchFilter('joy',this)">Joy <span class="ms-tab-chip" id="chipJoy">—</span></button>
      <button class="ms-tab-btn" id="tabTrust"        onclick="EmoApp.switchFilter('trust',this)">Trust <span class="ms-tab-chip" id="chipTrust">—</span></button>
      <button class="ms-tab-btn" id="tabFear"         onclick="EmoApp.switchFilter('fear',this)">Fear <span class="ms-tab-chip" id="chipFear">—</span></button>
      <button class="ms-tab-btn" id="tabSurprise"     onclick="EmoApp.switchFilter('surprise',this)">Surprise <span class="ms-tab-chip" id="chipSurprise">—</span></button>
      <button class="ms-tab-btn" id="tabSadness"      onclick="EmoApp.switchFilter('sadness',this)">Sadness <span class="ms-tab-chip" id="chipSadness">—</span></button>
      <button class="ms-tab-btn" id="tabDisgust"      onclick="EmoApp.switchFilter('disgust',this)">Disgust <span class="ms-tab-chip" id="chipDisgust">—</span></button>
      <button class="ms-tab-btn" id="tabAnger"        onclick="EmoApp.switchFilter('anger',this)">Anger <span class="ms-tab-chip" id="chipAnger">—</span></button>
      <button class="ms-tab-btn" id="tabAnticipation" onclick="EmoApp.switchFilter('anticipation',this)">Anticipation <span class="ms-tab-chip" id="chipAnticipation">—</span></button>
    </div>
    <button class="ms-export-btn" onclick="EmoApp.exportCSV()">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export CSV
    </button>
  </div>

  <div class="do-card" id="tableCard">
    {{-- Skeleton --}}
    <div id="listSkel">
      @for($i=0;$i<5;$i++)
      <div style="display:flex;gap:14px;padding:16px 20px;border-bottom:1px solid #f1f5f9;align-items:flex-start;">
        <div class="loading-skeleton" style="width:28px;height:28px;border-radius:50%;flex-shrink:0;margin-top:6px;"></div>
        <div class="loading-skeleton" style="width:46px;height:46px;border-radius:50%;flex-shrink:0;"></div>
        <div style="flex:1;">
          <div class="loading-skeleton" style="height:13px;width:140px;margin-bottom:6px;"></div>
          <div class="loading-skeleton" style="height:10px;width:80px;margin-bottom:8px;"></div>
          <div class="loading-skeleton" style="height:12px;width:90%;margin-bottom:5px;"></div>
          <div class="loading-skeleton" style="height:12px;width:70%;margin-bottom:10px;"></div>
          <div style="display:flex;gap:6px;">
            <div class="loading-skeleton" style="height:22px;width:70px;border-radius:20px;"></div>
            <div class="loading-skeleton" style="height:22px;width:70px;border-radius:20px;"></div>
            <div class="loading-skeleton" style="height:22px;width:70px;border-radius:20px;"></div>
          </div>
        </div>
        <div class="loading-skeleton" style="width:80px;height:100px;border-radius:10px;flex-shrink:0;"></div>
      </div>
      @endfor
    </div>

    <div id="listReal" class="fme-post-list" style="display:none;"></div>

    <div id="listEmpty" style="display:none;">
      <div class="do-empty">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span class="do-empty-text">Tidak ada postingan untuk periode ini</span>
      </div>
    </div>

    <div id="pagWrapper" style="display:none;"></div>
  </div>

  {{-- POST DETAIL MODAL --}}
  <div class="modal-overlay" id="postModal" onclick="if(event.target===this)EmoApp.closeModal()">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle">Detail Post</h3>
        <button class="modal-close" onclick="EmoApp.closeModal()">
          <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <div class="modal-body" id="modalBody"></div>
    </div>
  </div>

  @endif
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

const EmoCfg = {
  pid:     {{ $projectId ? (int)$projectId : 'null' }},
  sd:      '{{ $startDate ?? "" }}',
  ed:      '{{ $endDate ?? "" }}',
  perPage: 20,
};

const EMOTIONS   = ['joy','trust','fear','surprise','sadness','disgust','anger','anticipation'];
const EMO_COLORS = { joy:'#F59E0B', trust:'#10B981', fear:'#6366F1', surprise:'#3B82F6', sadness:'#8B5CF6', disgust:'#A855F7', anger:'#EF4444', anticipation:'#F97316' };

/* ── Keyword-based emotion detection (same logic as TikTok) ── */
const EMO_KW = {
  joy:          ['senang','bahagia','happy','joy','gembira','suka','mantap','keren','bagus','luar biasa','amazing','great','love','indah','seru','enjoy','cantik','gorgeous','beautiful'],
  trust:        ['percaya','trust','yakin','terpercaya','aman','reliable','professional','solid','terbaik','amanah','andalan'],
  fear:         ['takut','khawatir','was-was','bahaya','danger','fear','ancaman','waspada','ngeri','serem','merinding'],
  surprise:     ['terkejut','kaget','wow','surprise','tidak menyangka','unexpected','gila','ternyata','nggak nyangka'],
  sadness:      ['sedih','sad','kecewa','duka','menangis','galau','patah hati','sorrow','grief','nelangsa','hancur'],
  disgust:      ['jijik','muak','mual','benci','tidak suka','menjijikkan','awful','jelek','buruk','payah'],
  anger:        ['marah','anger','kesal','geram','frustasi','angry','kemarahan','emosi','sebel'],
  anticipation: ['menunggu','nantikan','cannot wait','excited','antisipasi','harapan','soon','upcoming','segera','penasaran','catat'],
};

/* ── Utils ── */
const numFmt = n => parseInt(n||0).toLocaleString('id-ID');
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc    = s => { const d=document.createElement('div'); d.textContent=String(s||''); return d.innerHTML; };
const decodeStr = s => {
  if(!s) return '';
  try { const f=decodeURIComponent(escape(s)); if(!f.includes('\uFFFD')&&f!==s) return f; } catch(e){}
  return s;
};

/* ── Date Picker ── */
const EmoDP = (() => {
  let ds=null, de=null, m1=new Date(), m2=new Date(), pickStart=true;
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
  function init() {
    const si=document.getElementById('hSD'), ei=document.getElementById('hED');
    ds=si?.value?new Date(si.value):(() => { const d=new Date();d.setDate(d.getDate()-6);return d; })();
    de=ei?.value?new Date(ei.value):new Date();
    m1=new Date(ds); m2=new Date(ds); m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('dpTrigger')?.addEventListener('click', open);
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click', onPreset));
    document.addEventListener('keydown', e=>{ if(e.key==='Escape'&&document.getElementById('dpModal').classList.contains('show')) close(); });
  }
  function open()  { document.getElementById('dpModal').classList.add('show'); render(); }
  function close() { document.getElementById('dpModal').classList.remove('show'); }
  function apply() {
    document.getElementById('hSD').value=fmt(ds); document.getElementById('hED').value=fmt(de);
    document.getElementById('dpDisplay').textContent=fmt(ds)+' – '+fmt(de); close();
  }
  function nav(dir) { m1.setMonth(m1.getMonth()+dir); m2.setMonth(m2.getMonth()+dir); render(); }
  function onPreset(e) {
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
    e.target.classList.add('active');
    const today=new Date(); today.setHours(0,0,0,0);
    switch(e.target.dataset.p) {
      case 'today':     ds=new Date(today);de=new Date(today);break;
      case 'yesterday': ds=new Date(today);ds.setDate(today.getDate()-1);de=new Date(ds);break;
      case 'last7':     de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-6);break;
      case 'last30':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-29);break;
      case 'thismonth': ds=new Date(today.getFullYear(),today.getMonth(),1);de=new Date(today);break;
      case 'lastmonth': ds=new Date(today.getFullYear(),today.getMonth()-1,1);de=new Date(today.getFullYear(),today.getMonth(),0);break;
    }
    if(e.target.dataset.p!=='custom'){ m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1); }
    updDisp(); render();
  }
  function render() { renderCal('dpCal1',m1); renderCal('dpCal2',m2); updDisp(); }
  function renderCal(id, month) {
    const el=document.getElementById(id); if(!el) return;
    const y=month.getFullYear(), mn=month.getMonth();
    const first=new Date(y,mn,1), last=new Date(y,mn+1,0), prevL=new Date(y,mn,0);
    const today=new Date(); today.setHours(0,0,0,0);
    let h=`<div class="calendar-month">${MN[mn]} ${y}</div><div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
    for(let i=0;i<first.getDay();i++) h+=`<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++) {
      const date=new Date(y,mn,d); date.setHours(0,0,0,0);
      let cls='calendar-day';
      if(sameD(date,today)) cls+=' today';
      if(date>today) cls+=' disabled';
      if(ds&&de){ if(sameD(date,ds)||sameD(date,de)) cls+=' selected'; else if(date>ds&&date<de) cls+=' in-range'; }
      h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>'; el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn=>{
      btn.addEventListener('click', function() {
        const d=new Date(this.dataset.date); d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-p="custom"]').classList.add('active');
        if(pickStart||d<ds){ ds=d;de=d;pickStart=false; }
        else { if(d>=ds) de=d; else { de=ds;ds=d; } pickStart=true; }
        updDisp(); render();
      });
    });
  }
  function updDisp() { const el=document.getElementById('dpRangeText'); if(el&&ds&&de) el.textContent=fmt(ds)+' – '+fmt(de); }
  function fmt(d) { if(!d) return ''; return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
  function sameD(a,b) { return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
  return { init, open, close, apply, nav };
})();

/* ── ECharts helpers ── */
const EmoCharts = {
  _i: {},
  make(id) {
    if(this._i[id]) { try{this._i[id].dispose();}catch(e){} }
    const dom=document.getElementById(id); if(!dom) return null;
    const c=echarts.init(dom,null,{renderer:'canvas'}); this._i[id]=c; return c;
  },
  disposeAll() { Object.values(this._i).forEach(c=>{ try{c.dispose();}catch(e){} }); this._i={}; }
};
window.addEventListener('resize',()=>{ Object.values(EmoCharts._i).forEach(c=>{ try{if(!c.isDisposed())c.resize();}catch(e){} }); });
const EC_TT = { confine:true, backgroundColor:'#1a202c', borderColor:'#334155', borderWidth:1, padding:[10,14], textStyle:{color:'#fff',fontFamily:"'Poppins',sans-serif",fontSize:13}, extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);' };

/* ── State ── */
let allPosts=[], filteredPosts=[], currentFilter='all', currentPage=1;

/* ── Emotion detection ── */
function detectEmotion(post) {
  const raw=(post.emotion||post.emotion_str||'').toLowerCase().trim();
  if(raw && EMOTIONS.includes(raw)) return raw;
  const content=((post.content||post.caption||post.name||'').replace(/<[^>]*>/g,'')).toLowerCase();
  const sent=(post.sentiment_str||'').toLowerCase();
  for(const [emo,kws] of Object.entries(EMO_KW)) {
    if(kws.some(k=>content.includes(k))) return emo;
  }
  if(sent.includes('pos')) return 'joy';
  if(sent.includes('neg')) return 'anger';
  return 'trust';
}
function getEmotionCounts(posts) {
  const c={}; EMOTIONS.forEach(e=>c[e]=0);
  (posts||allPosts).forEach(p=>c[p.emotion]=(c[p.emotion]||0)+1);
  return c;
}

/* ── Field helpers specific to Instagram ── */
function getName(item) {
  const n=(item.name||item.author?.name||item.author_scr_name||item.author_name||'').replace(/<[^>]*>/g,'').trim();
  // Instagram names often come as "Username: caption" — parse out just username
  if(n && n.includes(':')) return n.split(':')[0].trim() || 'Instagram User';
  return n || 'Instagram User';
}
function getAvatar(item)     { return (item.avatar_url||item.profile_url||item.author?.image||'').trim(); }
function getThumbnail(item)  { return (item.image||item.thumbnail_url||item.avatar_url||item.profile_url||'').trim(); }
function getInitials(name) {
  if(!name||name==='Instagram User') return 'IG';
  const w=name.replace(/[^a-zA-Z0-9\s]/g,'').split(/\s+/).filter(Boolean);
  if(w.length>=2) return (w[0][0]+w[w.length-1][0]).toUpperCase();
  return (w[0]?.[0]||'I').toUpperCase();
}
function getAvatarColor(item) {
  const seed=item.author_id||item.id||getName(item)||'ig';
  const palette=['#E1306C','#c13584','#fd1d1d','#fcb045','#405de6','#5851db','#833ab4','#e6683c','#dc2743','#cc2366'];
  let h=0; for(let i=0;i<seed.length;i++) h=(h*31+seed.charCodeAt(i))&0xffffffff;
  return palette[Math.abs(h)%palette.length];
}
function getLikes(item)    { return parseInt(item.likes||item.num_likes||0); }
function getComments(item) { return parseInt(item.comments||item.num_comments||0); }
function getMentionType(item) {
  const mt=(item.mention_type||'').toLowerCase();
  if(mt==='video') return 'video';
  const url=item.url||item.link||'';
  if(url.includes('/reel/')||url.includes('/tv/')) return 'video';
  return 'image';
}
function getSent(item) {
  const raw=String(item.sentiment_str||item.sentiment||'').toLowerCase();
  if(raw.includes('pos')) return 'pos';
  if(raw.includes('neg')) return 'neg';
  return 'neu';
}

/* ── MAIN APP ── */
const EmoApp = {
  _abort: null,

  async init() {
    if(!EmoCfg.pid) return;
    try { await this.loadData(); }
    catch(e) { if(e.name!=='AbortError') { console.error(e); this.showError(); } }
  },

  async loadData() {
    if(this._abort) this._abort.abort();
    this._abort = new AbortController();

    // Show skeletons
    document.getElementById('listSkel').style.display='block';
    document.getElementById('listReal').style.display='none';
    document.getElementById('listEmpty').style.display='none';
    document.getElementById('pagWrapper').style.display='none';
    ['skBar','skRadar','skTrends'].forEach(id=>{ const el=document.getElementById(id); if(el) el.style.display='block'; });
    ['statJoy','statTrust','statAnticipation','statSurprise'].forEach(id=>{
      const el=document.getElementById(id);
      if(el) el.innerHTML='<div class="loading-skeleton" style="height:36px;width:100px;border-radius:6px;"></div>';
    });

    // Use the existing mostViewedPostsData endpoint (sub=postbylike, rows=500)
    const url=`/mk/api/instagram/most-viewed-posts?project_id=${EmoCfg.pid}&start_date=${EmoCfg.sd}&end_date=${EmoCfg.ed}&sub=postbylike&rows=500`;
    const res=await fetch(url,{signal:this._abort.signal});
    const json=await res.json();
    if(!json.success) throw new Error(json.error||'Failed to load');

    allPosts=(json.data||[]).map(p=>({...p, emotion:detectEmotion(p)}));

    this.applyFilter();
    this.updateStats();
    this.updateChips();
    this.renderList();

    requestAnimationFrame(()=>{
      this.renderBarChart();
      requestAnimationFrame(()=>{
        this.renderRadarChart();
        requestAnimationFrame(()=>{ this.renderTrendsChart(); });
      });
    });
  },

  reload() {
    EmoCharts.disposeAll();
    allPosts=[]; filteredPosts=[];
    currentFilter='all'; currentPage=1;
    document.querySelectorAll('.ms-tab-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById('tabAll')?.classList.add('active');
    this.init();
  },

  applyFilter() {
    filteredPosts=currentFilter==='all'?[...allPosts]:allPosts.filter(p=>p.emotion===currentFilter);
    currentPage=1;
  },

  switchFilter(filter,btn) {
    currentFilter=filter;
    document.querySelectorAll('.ms-tab-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    this.applyFilter();
    this.renderList();
  },

  updateChips() {
    const counts=getEmotionCounts();
    document.getElementById('chipAll').textContent=numK(allPosts.length);
    EMOTIONS.forEach(e=>{
      const el=document.getElementById('chip'+e.charAt(0).toUpperCase()+e.slice(1));
      if(el) el.textContent=numK(counts[e]||0);
    });
  },

  updateStats() {
    const total=allPosts.length||1, counts=getEmotionCounts();
    [{id:'statJoy',pct:'statJoyPct',emo:'joy'},{id:'statTrust',pct:'statTrustPct',emo:'trust'},
     {id:'statAnticipation',pct:'statAnticipationPct',emo:'anticipation'},{id:'statSurprise',pct:'statSurprisePct',emo:'surprise'}]
    .forEach(({id,pct,emo})=>{
      const v=counts[emo]||0, p=Math.round((v/total)*100);
      const el=document.getElementById(id); if(el) el.textContent=numFmt(v);
      const ep=document.getElementById(pct); if(ep) ep.textContent=`${p}% dari total post`;
    });
  },

  /* ── RENDER LIST ── */
  renderList() {
    const skelEl  = document.getElementById('listSkel');
    const realEl  = document.getElementById('listReal');
    const emptyEl = document.getElementById('listEmpty');
    const pagEl   = document.getElementById('pagWrapper');

    if(!filteredPosts.length) {
      if(skelEl)  skelEl.style.display='none';
      if(realEl)  realEl.style.display='none';
      if(emptyEl) emptyEl.style.display='block';
      if(pagEl)   pagEl.style.display='none';
      return;
    }

    const start=(currentPage-1)*EmoCfg.perPage;
    const page=filteredPosts.slice(start, start+EmoCfg.perPage);
    realEl.innerHTML=page.map((p,i)=>this.postHTML(p, start+i+1)).join('');

    if(skelEl)  skelEl.style.display='none';
    if(realEl)  realEl.style.display='block';
    if(emptyEl) emptyEl.style.display='none';
    this.updatePagination();

    realEl.querySelectorAll('.fme-post').forEach((el,i)=>{
      el.addEventListener('click', ()=>this.openModal(page[i]));
    });
  },

  /* ── POST CARD HTML ── */
  postHTML(post, rank) {
    const rkCls = rank===1?'--1':rank===2?'--2':rank===3?'--3':'';
    const name  = getName(post);
    const av    = getAvatar(post);
    const thumb = getThumbnail(post);
    const color = getAvatarColor(post);
    const ini   = getInitials(name);
    const safeIni = ini.replace(/['"\\]/g,'');

    const avHtml = (av&&av.startsWith('http'))
      ? `<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`
      : ini;

    // Thumbnail — Instagram portrait square/portrait
    const thumbHtml = (thumb&&thumb.startsWith('http'))
      ? `<div class="fme-post-thumb">
           <img src="${esc(thumb)}" alt="thumb" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'fme-post-thumb-placeholder\\'>📸</div>'">
         </div>`
      : `<div class="fme-post-thumb"><div class="fme-post-thumb-placeholder">📸</div></div>`;

    const handle  = post.author?.scr_name||post.author_scr_name||'';
    const rawText = (post.content||post.caption||'').replace(/<[^>]*>/g,'').trim();
    // If content starts with "Name: caption", strip the author prefix
    const content = decodeStr(
      rawText.includes(':')&&rawText.indexOf(':')<40
        ? rawText.slice(rawText.indexOf(':')+1).trim()
        : rawText
    ).slice(0,200);

    const dt     = (post.date_created||'').split('T')[0];
    const url    = post.url||post.link||'';
    const likes  = getLikes(post);
    const cmts   = getComments(post);
    const mtype  = getMentionType(post);

    const emo  = post.emotion||'trust';
    const ec   = EMO_COLORS[emo]||'#64748b';
    const sent = getSent(post);
    const sentLbl = {pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];

    return `<div class="fme-post">
      <div class="fme-post-rank fme-post-rank${rkCls}">${rank}</div>
      <div class="fme-post-av" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
      <div class="fme-post-body">
        <div class="fme-post-author">${esc(name)}${handle&&handle!==name?` <span style="font-weight:400;color:var(--text-muted);font-size:11px;">@${esc(handle)}</span>`:''}</div>
        ${dt?`<div class="fme-post-date">${dt}</div>`:''}
        ${content?`<div class="fme-post-text">${esc(content)}</div>`:'<div class="fme-post-text" style="color:var(--text-muted);font-style:italic;">(Tidak ada caption)</div>'}
        <div class="fme-post-stats">
          <span class="emo-badge emo-${emo}"><span class="emo-dot" style="background:${ec};"></span>${emo.charAt(0).toUpperCase()+emo.slice(1)}</span>
          <span class="type-badge type-${mtype}">
            ${mtype==='video'?'<svg viewBox="0 0 24 24" style="width:10px;height:10px;fill:currentColor;flex-shrink:0;margin-right:2px;"><polygon points="5 3 19 12 5 21 5 3"/></svg>':'<svg viewBox="0 0 24 24" style="width:10px;height:10px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0;margin-right:2px;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>'}
            ${mtype}
          </span>
          <span class="fme-post-metric fme-post-metric--ig">
            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            ${numFmt(likes)}
          </span>
          <span class="fme-post-metric fme-post-metric--amber">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            ${numFmt(cmts)}
          </span>
          <span class="sent-badge sent-${sent==='pos'?'positive':sent==='neg'?'negative':'neutral'}">${sentLbl}</span>
          ${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="fme-view-link" onclick="event.stopPropagation()">
            <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Instagram</a>`:''}
        </div>
      </div>
      ${thumbHtml}
    </div>`;
  },

  /* ── MODAL ── */
  openModal(post) {
    if(!post) return;
    const name    = getName(post);
    const av      = getAvatar(post);
    const color   = getAvatarColor(post);
    const ini     = getInitials(name);
    const safeIni = ini.replace(/['"\\]/g,'');
    const handle  = post.author?.scr_name||post.author_scr_name||'';
    const rawText = (post.content||post.caption||'').replace(/<[^>]*>/g,'').trim();
    const content = decodeStr(
      rawText.includes(':')&&rawText.indexOf(':')<40
        ? rawText.slice(rawText.indexOf(':')+1).trim()
        : rawText
    );
    const url    = post.url||post.link||'';
    const likes  = getLikes(post);
    const cmts   = getComments(post);
    const mtype  = getMentionType(post);
    const emo    = post.emotion||'trust';
    const ec     = EMO_COLORS[emo]||'#64748b';
    const sent   = getSent(post);
    const sentLbl = {pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];
    const sentDesc= {pos:'Post menunjukkan sentimen positif',neg:'Post menunjukkan sentimen negatif',neu:'Post bersifat netral'}[sent];
    const sentEmoji={pos:'😊',neg:'😞',neu:'😐'}[sent];
    const sentBg  = {pos:'rgba(16,185,129,.12)',neg:'rgba(239,68,68,.12)',neu:'rgba(100,116,139,.1)'}[sent];

    const avHtml = (av&&av.startsWith('http'))
      ? `<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`
      : ini;

    let dtFmt='';
    if(post.date_created){ try{ dtFmt=new Date(post.date_created).toLocaleDateString('id-ID',{weekday:'short',day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}); }catch(e){ dtFmt=post.date_created.split('T')[0]; } }

    /* Emotion distribution bars */
    const counts=getEmotionCounts(), maxC=Math.max(...Object.values(counts),1);
    const emoBars=EMOTIONS.map(e=>{
      const c=counts[e]||0, w=Math.round((c/maxC)*100), ecc=EMO_COLORS[e];
      return `<div class="modal-emo-bar">
        <div class="modal-emo-label"><span class="modal-emo-dot" style="background:${ecc};"></span>${e.charAt(0).toUpperCase()+e.slice(1)}</div>
        <div class="modal-emo-track"><div class="modal-emo-fill" style="width:${w}%;background:${ecc};"></div></div>
        <div class="modal-emo-count">${numK(c)}</div>
      </div>`;
    }).join('');

    // Instagram thumbnail preview
    const thumb = getThumbnail(post);
    const mediaHtml = (thumb&&thumb.startsWith('http')) ? `
      <div>
        <div class="msdp-section-heading">
          <div class="msdp-section-heading-icon" style="background:rgba(225,48,108,.08);">
            <svg viewBox="0 0 24 24" style="stroke:#E1306C;stroke-width:2;fill:none;stroke-linecap:round;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          </div>
          <span class="msdp-section-title">Preview</span>
          <div class="msdp-section-line"></div>
        </div>
        <div style="text-align:center;">
          <img src="${esc(thumb)}" alt="post" style="max-height:300px;max-width:100%;border-radius:12px;border:1px solid #e8edf2;box-shadow:0 4px 16px rgba(0,0,0,.08);object-fit:cover;" onerror="this.parentElement.style.display='none'">
        </div>
      </div>` : '';

    document.getElementById('modalTitle').textContent=name;
    document.getElementById('modalBody').innerHTML=`
      <div style="display:flex;flex-direction:column;gap:16px;">

        <!-- PROFILE -->
        <div style="display:flex;align-items:center;gap:14px;padding:16px 18px;background:linear-gradient(135deg,#fafbfc,#f1f5f9);border:1px solid #e8edf2;border-radius:14px;">
          <div style="width:56px;height:56px;border-radius:50%;font-size:20px;font-weight:800;background:linear-gradient(135deg,${color},${color}99);border:3px solid rgba(255,255,255,.9);box-shadow:0 4px 16px rgba(0,0,0,.18);display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;overflow:hidden;">${avHtml}</div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:16px;font-weight:800;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${esc(name)}</div>
            ${handle?`<div style="font-size:12px;color:#94a3b8;margin-top:2px;">@${esc(handle)}</div>`:''}
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;align-items:center;">
              <span class="emo-badge emo-${emo}"><span class="emo-dot" style="background:${ec};"></span>${emo.charAt(0).toUpperCase()+emo.slice(1)}</span>
              <span class="type-badge type-${mtype}">${mtype}</span>
              <span class="sent-badge sent-${sent==='pos'?'positive':sent==='neg'?'negative':'neutral'}">${sentLbl}</span>
              ${dtFmt?`<span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;background:#f1f5f9;border:1px solid #e2e8f0;font-size:11px;font-weight:600;color:#64748b;">${dtFmt}</span>`:''}
            </div>
          </div>
        </div>

        <!-- SENTIMENT -->
        <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:12px;border:1px solid;${sent==='pos'?'background:#f0fdf4;border-color:#bbf7d0;':sent==='neg'?'background:#fff5f5;border-color:#fecaca;':'background:#f8fafc;border-color:#e2e8f0;'}">
          <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;background:${sentBg};">${sentEmoji}</div>
          <div>
            <div style="font-size:13px;font-weight:700;${sent==='pos'?'color:#15803d;':sent==='neg'?'color:#b91c1c;':'color:#475569;'}">Sentimen: ${sentLbl}</div>
            <div style="font-size:11px;color:#94a3b8;font-weight:500;margin-top:1px;">${sentDesc}</div>
          </div>
        </div>

        <!-- METRICS -->
        <div class="modal-stats-grid">
          <div class="modal-stat-box"><div class="modal-stat-value v-likes">${numFmt(likes)}</div><div class="modal-stat-label">Likes</div></div>
          <div class="modal-stat-box"><div class="modal-stat-value v-cmts">${numFmt(cmts)}</div><div class="modal-stat-label">Comments</div></div>
          <div class="modal-stat-box"><div class="modal-stat-value" style="color:#6366f1;">${numFmt(likes+cmts)}</div><div class="modal-stat-label">Engagement</div></div>
        </div>

        <!-- CAPTION -->
        <div>
          <div class="msdp-section-heading">
            <div class="msdp-section-heading-icon" style="background:rgba(100,116,139,.08);">
              <svg viewBox="0 0 24 24" style="stroke:#64748b;stroke-width:2;fill:none;stroke-linecap:round;"><line x1="21" y1="6" x2="3" y2="6"/><line x1="15" y1="12" x2="3" y2="12"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
            </div>
            <span class="msdp-section-title">Caption</span>
            <div class="msdp-section-line"></div>
          </div>
          <div style="background:#f8fafc;border:1px solid #e8edf2;border-radius:12px;padding:14px 16px;font-size:13.5px;color:#374151;line-height:1.75;word-break:break-word;white-space:pre-wrap;">${content?esc(content):'<span style="color:#94a3b8;font-style:italic;">(Tidak ada caption)</span>'}</div>
        </div>

        <!-- MEDIA PREVIEW -->
        ${mediaHtml}

        <!-- EMOTION DIST -->
        <div>
          <div class="msdp-section-heading">
            <div class="msdp-section-heading-icon" style="background:rgba(225,48,108,.08);">
              <svg viewBox="0 0 24 24" style="stroke:#E1306C;stroke-width:2;fill:none;stroke-linecap:round;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <span class="msdp-section-title">Distribusi Emosi (semua post)</span>
            <div class="msdp-section-line"></div>
          </div>
          ${emoBars}
        </div>

        <!-- ACTIONS -->
        <div class="modal-actions">
          ${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="modal-btn primary">
            <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Buka di Instagram</a>`:''}
          <button onclick="EmoApp.closeModal()" class="modal-btn secondary">Tutup</button>
        </div>

      </div>`;

    document.getElementById('postModal').classList.add('active');
    document.body.style.overflow='hidden';
  },

  closeModal() {
    document.getElementById('postModal').classList.remove('active');
    document.body.style.overflow='';
  },

  /* ── PAGINATION ── */
  updatePagination() {
    const total=filteredPosts.length, pages=Math.ceil(total/EmoCfg.perPage);
    const pagEl=document.getElementById('pagWrapper');
    if(pages<=1){ pagEl.style.display='none'; return; }
    const from=total?(currentPage-1)*EmoCfg.perPage+1:0, to=Math.min(currentPage*EmoCfg.perPage,total);
    let btns='';
    btns+=`<button class="fme-pag-btn" ${currentPage<=1?'disabled':''} onclick="EmoApp.goPage(${currentPage-1})"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>`;
    const r=2;
    for(let i=1;i<=pages;i++) {
      if(i===1||i===pages||(i>=currentPage-r&&i<=currentPage+r)) btns+=`<button class="fme-pag-btn${i===currentPage?' is-active':''}" onclick="EmoApp.goPage(${i})">${i}</button>`;
      else if(i===currentPage-r-1||i===currentPage+r+1) btns+=`<span class="fme-pag-btn" style="cursor:default;opacity:.5;">…</span>`;
    }
    btns+=`<button class="fme-pag-btn" ${currentPage>=pages?'disabled':''} onclick="EmoApp.goPage(${currentPage+1})"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>`;
    pagEl.innerHTML=`<div class="fme-pagination"><span class="fme-pag-info">Menampilkan ${numFmt(from)}–${numFmt(to)} dari ${numFmt(total)} post</span><div class="fme-pag-controls">${btns}</div></div>`;
    pagEl.style.display='block';
  },

  goPage(p) {
    const pages=Math.ceil(filteredPosts.length/EmoCfg.perPage);
    if(p<1||p>pages) return;
    currentPage=p; this.renderList();
    document.getElementById('tableCard')?.scrollIntoView({behavior:'smooth',block:'start'});
  },

  /* ── EXPORT ── */
  exportCSV() {
    if(!filteredPosts.length){ alert('Tidak ada data untuk diekspor.'); return; }
    const header='index;creator;handle;emosi;tipe;sentimen;likes;comments;tanggal;url;caption';
    const rows=filteredPosts.map((p,i)=>{
      const name=getName(p), handle=p.author?.scr_name||p.author_scr_name||'';
      const sent={pos:'Positif',neg:'Negatif',neu:'Netral'}[getSent(p)];
      const rawText=(p.content||p.caption||'').replace(/<[^>]*>/g,'').trim();
      const caption=rawText.includes(':')&&rawText.indexOf(':')<40?rawText.slice(rawText.indexOf(':')+1).trim():rawText;
      return [i+1,`"${name.replace(/"/g,'""')}"`,`"${handle.replace(/"/g,'""')}"`,p.emotion||'',getMentionType(p),sent,
        getLikes(p), getComments(p),
        (p.date_created||'').split('T')[0], p.url||p.link||'',
        `"${caption.replace(/"/g,'""').replace(/\n/g,' ').slice(0,300)}"`
      ].join(';');
    });
    const blob=new Blob(['\uFEFF'+[header,...rows].join('\r\n')],{type:'text/csv;charset=utf-8;'});
    const a=document.createElement('a'); a.href=URL.createObjectURL(blob);
    a.download=`Instagram_EmotionAnalysis_${EmoCfg.sd}_${EmoCfg.ed}.csv`; a.click();
  },

  /* ── BAR CHART ── */
  renderBarChart() {
    const skEl=document.getElementById('skBar'); if(skEl) skEl.style.display='none';
    if(!allPosts.length) return;
    const counts=getEmotionCounts(), total=allPosts.length||1;
    const labels=EMOTIONS.map(e=>e.charAt(0).toUpperCase()+e.slice(1));
    const data=EMOTIONS.map(e=>counts[e]||0);
    const colors=EMOTIONS.map(e=>EMO_COLORS[e]);
    const chart=EmoCharts.make('chartBar'); if(!chart) return;
    chart.setOption({
      animation:true, animationDuration:600, backgroundColor:'#ffffff',
      tooltip:{ ...EC_TT, trigger:'axis', axisPointer:{type:'shadow',shadowStyle:{color:'rgba(225,48,108,.04)'}},
        formatter:params=>{ const idx=params[0]?.dataIndex, cnt=params[0]?.value||0, pct=Math.round((cnt/total)*100);
          return `<div style="min-width:170px;"><div style="font-weight:700;margin-bottom:6px;padding-bottom:5px;border-bottom:1px solid rgba(255,255,255,.12);">${labels[idx]||''}</div>
            <div style="display:flex;justify-content:space-between;gap:14px;"><span style="color:#94a3b8;font-size:12px;">Posts</span><span style="font-weight:700;">${numFmt(cnt)}</span></div>
            <div style="display:flex;justify-content:space-between;gap:14px;margin-top:3px;"><span style="color:#94a3b8;font-size:12px;">Proporsi</span><span style="font-weight:700;">${pct}%</span></div></div>`; }
      },
      grid:{top:14,right:18,bottom:52,left:16,containLabel:true},
      xAxis:{type:'category',data:labels,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b',interval:0,rotate:labels.length>6?30:0}},
      yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},
      series:[{ type:'bar', data:data.map((v,i)=>({value:v,itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:colors[i]},{offset:1,color:colors[i]+'44'}]},borderRadius:[6,6,0,0]}})), barMaxWidth:44,
        label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:10,color:'#64748b',formatter:p=>numK(p.value)} }]
    });
    chart.on('click',params=>{ const emo=EMOTIONS[params.dataIndex]; if(!emo) return; const btn=document.getElementById('tab'+emo.charAt(0).toUpperCase()+emo.slice(1)); if(btn) this.switchFilter(emo,btn); });
    const badge=document.getElementById('barBadge'); if(badge) badge.textContent=`${numK(total)} posts`;
    const sub=document.getElementById('barSubtitle'); if(sub) sub.textContent=`${numFmt(total)} post dianalisis`;
  },

  /* ── RADAR CHART ── */
  renderRadarChart() {
    const skEl=document.getElementById('skRadar'); if(skEl) skEl.style.display='none';
    if(!allPosts.length) return;
    const counts=getEmotionCounts(), max=Math.max(...Object.values(counts),1);
    const chart=EmoCharts.make('chartRadar'); if(!chart) return;
    chart.setOption({
      animation:true, animationDuration:800, backgroundColor:'#ffffff',
      tooltip:{ ...EC_TT, trigger:'item', formatter:params=>{ if(!params.data) return ''; const vals=params.data.value||[];
        const rows=EMOTIONS.map((e,i)=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:7px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${EMO_COLORS[e]};flex-shrink:0;"></span><span style="font-size:12px;color:#94a3b8;">${e.charAt(0).toUpperCase()+e.slice(1)}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(vals[i]||0)}</span></div>`).join('');
        return `<div style="min-width:180px;"><div style="font-weight:700;font-size:12px;margin-bottom:7px;padding-bottom:5px;border-bottom:1px solid rgba(255,255,255,.12);">Emotion Distribution</div>${rows}</div>`; }
      },
      radar:{ indicator:EMOTIONS.map(e=>({name:e.charAt(0).toUpperCase()+e.slice(1),max})), shape:'polygon', radius:'62%', center:['50%','50%'],
        axisName:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'700',color:'#475569'}, splitLine:{lineStyle:{color:'#e2e8f0'}}, axisLine:{lineStyle:{color:'#e2e8f0'}}, splitArea:{show:true,areaStyle:{color:['rgba(248,250,252,0.8)','#fff']}} },
      series:[{ type:'radar', data:[{ value:EMOTIONS.map(e=>counts[e]||0), name:'Emotion',
        areaStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:1,colorStops:[{offset:0,color:'rgba(225,48,108,0.18)'},{offset:1,color:'rgba(193,53,132,0.08)'}]}},
        lineStyle:{color:'#E1306C',width:2.5}, symbol:'circle', symbolSize:6, itemStyle:{color:EMOTIONS.map(e=>EMO_COLORS[e]),borderColor:'#fff',borderWidth:2} }] }]
    });
  },

  /* ── TRENDS CHART ── */
  renderTrendsChart() {
    const skEl=document.getElementById('skTrends'); if(skEl) skEl.style.display='none';
    if(!allPosts.length) return;
    const dateMap={};
    allPosts.forEach(p=>{ const d=(p.date_created||'').substring(0,10); if(!d) return; if(!dateMap[d]){dateMap[d]={};EMOTIONS.forEach(e=>dateMap[d][e]=0);} dateMap[d][p.emotion]=(dateMap[d][p.emotion]||0)+1; });
    const dates=Object.keys(dateMap).sort(); if(!dates.length) return;
    const chart=EmoCharts.make('chartTrends'); if(!chart) return;
    chart.setOption({
      animation:true, animationDuration:700, backgroundColor:'#ffffff',
      tooltip:{ ...EC_TT, trigger:'axis', axisPointer:{type:'cross',crossStyle:{color:'#999'}},
        formatter:params=>{ if(!params.length) return ''; const rows=params.filter(p=>p.value>0).map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:7px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};flex-shrink:0;"></span><span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span></div>`).join('');
          return `<div style="min-width:180px;"><div style="font-size:11px;color:#94a3b8;margin-bottom:5px;padding-bottom:5px;border-bottom:1px solid rgba(255,255,255,.12);">${params[0].axisValue}</div>${rows||'<span style="color:#94a3b8;font-size:11px;">No data</span>'}</div>`; }
      },
      legend:{bottom:0,data:EMOTIONS.map(e=>e.charAt(0).toUpperCase()+e.slice(1)),textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},icon:'circle',itemWidth:10,itemHeight:10,itemGap:14},
      grid:{top:16,right:20,bottom:60,left:16,containLabel:true},
      xAxis:{type:'category',data:dates,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8'}},
      yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},
      series:EMOTIONS.map(e=>({ name:e.charAt(0).toUpperCase()+e.slice(1), type:'line', data:dates.map(d=>dateMap[d][e]||0),
        lineStyle:{color:EMO_COLORS[e],width:2}, itemStyle:{color:EMO_COLORS[e]},
        areaStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:EMO_COLORS[e]+'28'},{offset:1,color:EMO_COLORS[e]+'05'}]}},
        smooth:0.4, symbol:'circle', symbolSize:4, showSymbol:false, emphasis:{focus:'series',showSymbol:true,symbolSize:7} }))
    });
  },

  showError() {
    document.getElementById('listSkel').style.display='none';
    const el=document.getElementById('listEmpty');
    el.innerHTML=`<div class="do-empty"><svg viewBox="0 0 24 24" style="stroke:#ef4444;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><span class="do-empty-text">Gagal memuat data. Silakan coba lagi.</span></div>`;
    el.style.display='block';
    ['skBar','skRadar','skTrends'].forEach(id=>{ const el=document.getElementById(id); if(el) el.style.display='none'; });
  }
};

document.addEventListener('DOMContentLoaded',()=>{
  EmoDP.init();
  EmoApp.init();
  document.addEventListener('keydown',e=>{ if(e.key==='Escape') EmoApp.closeModal(); });
});
</script>
@endsection