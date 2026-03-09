@extends('mk.layouts.app')

@section('title', 'TikTok Most Engagement - SMADIMENT')

@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --primary-green:        #038047;
    --primary-green-dark:   #026738;
    --primary-green-light:  rgba(3,128,71,0.08);
    --primary-green-border: rgba(3,128,71,0.2);
    --tiktok:               #EE1D52;
    --tiktok-dark:          #c4163f;
    --tiktok-light:         rgba(238,29,82,.08);
    --tiktok-border:        rgba(238,29,82,.2);
    --green:                #10b981;
    --green-dark:           #059669;
    --green-light:          rgba(16,185,129,.08);
    --green-border:         rgba(16,185,129,.2);
    --amber:                #f59e0b;
    --amber-dark:           #d97706;
    --amber-light:          rgba(245,158,11,.08);
    --amber-border:         rgba(245,158,11,.2);
    --cyan:                 #06b6d4;
    --cyan-dark:            #0891b2;
    --cyan-light:           rgba(6,182,212,.08);
    --cyan-border:          rgba(6,182,212,.2);

    --text-primary:   #1a202c;
    --text-secondary: #64748b;
    --text-muted:     #94a3b8;

    --bg-white:    #ffffff;
    --bg-body:     #f0f4f8;
    --bg-gray-50:  #f8fafc;
    --bg-gray-100: #f1f5f9;

    --border-gray:  #e2e8f0;
    --border-light: #f1f5f9;

    --shadow-xs: 0 1px 2px rgba(0,0,0,.05);
    --shadow-sm: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1);
    --shadow-xl: 0 20px 40px -8px rgba(0,0,0,.18);

    --radius:    16px;
    --radius-sm: 12px;
    --radius-xs: 8px;
    --transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
    --font: 'Poppins', -apple-system, sans-serif;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: var(--font); background: var(--bg-body); color: var(--text-primary); }

  .fme-page { padding: 24px; max-width: 1600px; margin: 0 auto; min-height: 100vh; }

  /* ── PAGE HEADER ── */
  .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 12px; }
  .page-header-left h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px; letter-spacing: -.4px; }
  .page-header-left p  { font-size: 14px; color: var(--text-secondary); font-weight: 500; margin: 0; }
  .ms-refresh-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg,#1a202c 0%,#2d3748 100%);
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-family: var(--font); font-size: 13px; font-weight: 600;
    cursor: pointer; transition: var(--transition);
    box-shadow: 0 4px 14px rgba(0,0,0,.2);
  }
  .ms-refresh-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.25); }
  .ms-refresh-btn svg { width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round; }

  /* ── FILTER CARD ── */
  .filter-card { background:var(--bg-white);border-radius:var(--radius);padding:20px 24px;margin-bottom:24px;box-shadow:var(--shadow-sm);border:1px solid var(--border-gray); }
  .filter-content { display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap; }
  .filter-group  { display:flex;flex-direction:column;gap:6px; }
  .filter-label  { font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.5px; }
  .filter-select { padding:10px 14px;border:1px solid var(--border-gray);border-radius:var(--radius-sm);font-family:var(--font);font-size:14px;font-weight:500;color:var(--text-primary);background:var(--bg-gray-50);outline:none;transition:var(--transition);min-width:200px;cursor:pointer; }
  .filter-select:focus { border-color:var(--primary-green);background:var(--bg-white);box-shadow:0 0 0 3px var(--primary-green-light); }

  .date-picker-trigger {
    display:flex;align-items:center;gap:10px;padding:10px 16px;
    background:var(--bg-gray-50);border:1px solid var(--border-gray);border-radius:var(--radius-sm);
    font-family:var(--font);font-size:14px;font-weight:500;color:var(--text-primary);
    cursor:pointer;transition:var(--transition);min-width:300px;
  }
  .date-picker-trigger:hover { border-color:var(--primary-green);background:var(--bg-white);box-shadow:0 0 0 3px var(--primary-green-light); }
  .date-picker-trigger svg { width:16px;height:16px;color:var(--text-secondary);flex-shrink:0; }
  .date-picker-trigger span { flex:1;text-align:left; }

  .apply-btn {
    display:flex;align-items:center;gap:8px;padding:10px 24px;
    background:linear-gradient(135deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);
    color:#fff;border:none;border-radius:var(--radius-sm);
    font-family:var(--font);font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);
    box-shadow:0 4px 12px rgba(3,128,71,.2);white-space:nowrap;
  }
  .apply-btn:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(3,128,71,.3); }
  .apply-btn svg { width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round; }

  /* ── DATE PICKER MODAL ── */
  .date-picker-modal { position:fixed;inset:0;z-index:10000;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.5);backdrop-filter:blur(8px); }
  .date-picker-modal.show { display:flex; }
  .date-picker-overlay { position:absolute;inset:0;cursor:pointer; }
  .date-picker-container { position:relative;z-index:1;background:#fff;border-radius:var(--radius);box-shadow:0 25px 50px rgba(0,0,0,.3);display:flex;max-width:900px;width:90%;max-height:90vh;animation:dpUp .3s ease-out; }
  @keyframes dpUp { from{opacity:0;transform:translateY(20px) scale(.95)}to{opacity:1;transform:translateY(0) scale(1)} }
  .date-picker-sidebar { width:180px;background:var(--bg-gray-50);border-right:1px solid var(--border-gray);padding:16px 12px;border-radius:var(--radius) 0 0 var(--radius);display:flex;flex-direction:column;gap:4px;flex-shrink:0; }
  .date-preset { padding:10px 16px;background:transparent;border:none;border-radius:var(--radius-xs);font-family:var(--font);font-size:13px;font-weight:500;color:var(--text-primary);text-align:left;cursor:pointer;transition:var(--transition); }
  .date-preset:hover  { background:var(--bg-white);color:var(--primary-green); }
  .date-preset.active { background:var(--primary-green);color:#fff; }
  .date-picker-content { flex:1;padding:24px;display:flex;flex-direction:column;overflow:hidden; }
  .date-picker-header { display:flex;align-items:flex-start;gap:20px;margin-bottom:20px; }
  .nav-btn { width:36px;height:36px;border-radius:var(--radius-xs);background:var(--bg-gray-50);border:1px solid var(--border-gray);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:var(--transition);flex-shrink:0; }
  .nav-btn:hover { background:var(--primary-green);border-color:var(--primary-green);color:#fff; }
  .nav-btn svg { width:20px;height:20px; }
  .calendars-wrapper { display:flex;gap:24px;flex:1; }
  .calendar { flex:1;display:flex;flex-direction:column; }
  .calendar-month { font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:16px;text-align:center; }
  .calendar-weekdays { display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:8px; }
  .weekday { text-align:center;font-size:11px;font-weight:700;color:var(--text-secondary);padding:8px 0; }
  .calendar-days { display:grid;grid-template-columns:repeat(7,1fr);gap:4px; }
  .calendar-day { aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:500;border-radius:var(--radius-xs);cursor:pointer;transition:var(--transition);color:var(--text-primary);background:transparent;border:none;padding:0;font-family:var(--font); }
  .calendar-day:hover:not(.disabled):not(.other-month) { background:var(--bg-gray-100); }
  .calendar-day.other-month { color:#cbd5e1;cursor:default; }
  .calendar-day.disabled    { color:#e2e8f0;cursor:not-allowed; }
  .calendar-day.today       { border:2px solid var(--primary-green); }
  .calendar-day.selected    { background:var(--primary-green);color:#fff; }
  .calendar-day.in-range    { background:var(--primary-green-light);color:var(--primary-green); }
  .date-picker-display { padding:16px 20px;background:var(--bg-gray-50);border-radius:var(--radius-sm);text-align:center;margin-bottom:20px;border:1px solid var(--border-gray); }
  .date-picker-display span { font-size:14px;font-weight:600;color:var(--text-primary); }
  .date-picker-footer { display:flex;gap:12px;justify-content:flex-end; }
  .cancel-btn { padding:10px 24px;border-radius:10px;font-family:var(--font);font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);border:none;background:var(--bg-gray-100);color:var(--text-primary); }
  .cancel-btn:hover { background:var(--border-gray); }
  .apply-date-btn { padding:10px 24px;border-radius:10px;font-family:var(--font);font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);border:none;background:linear-gradient(135deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);color:#fff;box-shadow:0 4px 12px rgba(3,128,71,.2); }
  .apply-date-btn:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(3,128,71,.3); }

  /* ── SECTION HEADER ── */
  .ms-section-header { display:flex;align-items:center;gap:10px;margin-bottom:16px;margin-top:4px; }
  .ms-section-icon { width:36px;height:36px;border-radius:var(--radius-sm);background:var(--primary-green-light);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
  .ms-section-icon svg { width:18px;height:18px;stroke:var(--primary-green);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round; }
  .ms-section-title { font-size:13px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px; }
  .ms-section-line  { flex:1;height:1.5px;background:var(--border-gray);border-radius:1px; }

  /* ── STAT CARDS ── */
  .ms-stat-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px; }
  .ms-stat-card { background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);padding:20px 22px;box-shadow:var(--shadow-sm);transition:var(--transition);position:relative;overflow:hidden;cursor:default; }
  .ms-stat-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--stat-bar,linear-gradient(90deg,var(--primary-green),var(--primary-green-dark)));opacity:0;transition:opacity .25s; }
  .ms-stat-card:hover { box-shadow:var(--shadow-lg);border-color:var(--primary-green-border);transform:translateY(-2px); }
  .ms-stat-card:hover::before { opacity:1; }
  .ms-stat-card--tiktok { --stat-bar:linear-gradient(90deg,#EE1D52,#c4163f); }
  .ms-stat-card--green  { --stat-bar:linear-gradient(90deg,#10b981,#059669); }
  .ms-stat-card--amber  { --stat-bar:linear-gradient(90deg,#f59e0b,#d97706); }
  .ms-stat-card--cyan   { --stat-bar:linear-gradient(90deg,#06b6d4,#0891b2); }
  .ms-stat-label { font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;display:flex;align-items:center;gap:6px; }
  .ms-stat-dot   { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
  .ms-stat-value { font-size:32px;font-weight:700;color:var(--text-primary);letter-spacing:-1px;line-height:1;min-height:40px;display:flex;align-items:center; }
  .ms-stat-sub   { font-size:11px;color:var(--text-muted);font-weight:500;margin-top:7px; }

  /* ── TABS ── */
  .ms-tabs { display:flex;gap:4px;background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);padding:6px;margin-bottom:24px;box-shadow:var(--shadow-sm); }
  .ms-tab-btn { flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 20px;border-radius:var(--radius-sm);border:none;background:transparent;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition); }
  .ms-tab-btn:hover { background:var(--bg-gray-50);color:var(--text-primary); }
  .ms-tab-btn.active { background:linear-gradient(135deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);color:#fff;box-shadow:0 4px 12px rgba(3,128,71,.25); }
  .ms-tab-btn.active--tiktok { background:linear-gradient(135deg,#EE1D52 0%,#c4163f 100%)!important;box-shadow:0 4px 12px rgba(238,29,82,.25)!important; }
  .ms-tab-btn.active--green  { background:linear-gradient(135deg,#10b981 0%,#059669 100%)!important;box-shadow:0 4px 12px rgba(16,185,129,.25)!important; }
  .ms-tab-btn.active--amber  { background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%)!important;box-shadow:0 4px 12px rgba(245,158,11,.25)!important; }
  .ms-tab-btn.active--cyan   { background:linear-gradient(135deg,#06b6d4 0%,#0891b2 100%)!important;box-shadow:0 4px 12px rgba(6,182,212,.25)!important; }
  .ms-tab-btn svg { width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0; }
  .ms-tab-chip { display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:20px;padding:0 6px;border-radius:10px;font-size:10px;font-weight:800;background:rgba(255,255,255,.22);color:inherit; }
  .ms-tab-btn:not(.active) .ms-tab-chip { background:var(--bg-gray-100);color:var(--text-muted); }
  .ms-tab-panel { display:none; }
  .ms-tab-panel.active { display:block; }

  /* ── DO CARD ── */
  .do-card { background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);overflow:hidden;display:flex;flex-direction:column;box-shadow:var(--shadow-sm);transition:var(--transition);position:relative; }
  .do-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);opacity:0;transition:opacity .3s; }
  .do-card--tiktok::before { background:linear-gradient(90deg,#EE1D52,#c4163f); }
  .do-card--green::before  { background:linear-gradient(90deg,#10b981,#059669); }
  .do-card--amber::before  { background:linear-gradient(90deg,#f59e0b,#d97706); }
  .do-card--cyan::before   { background:linear-gradient(90deg,#06b6d4,#0891b2); }
  .do-card--ink::before    { background:linear-gradient(90deg,#1a202c,#334155); }
  .do-card:hover { box-shadow:var(--shadow-lg);border-color:var(--primary-green-border); }
  .do-card:hover::before { opacity:1; }
  .do-card-head { display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-gray);flex-shrink:0;gap:12px;flex-wrap:wrap; }
  .do-card-head-left { display:flex;align-items:center;gap:12px;min-width:0; }
  .do-head-icon { width:40px;height:40px;border-radius:var(--radius-sm);background:var(--tiktok-light);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
  .do-head-icon svg { width:20px;height:20px;fill:none;stroke:var(--tiktok);stroke-width:2;stroke-linecap:round;stroke-linejoin:round; }
  .do-head-icon--tiktok { background:var(--tiktok-light)!important; }
  .do-head-icon--tiktok svg { stroke:#EE1D52!important; }
  .do-head-icon--green  { background:var(--green-light)!important; }
  .do-head-icon--green svg { stroke:#10b981!important; }
  .do-head-icon--amber  { background:var(--amber-light)!important; }
  .do-head-icon--amber svg { stroke:#f59e0b!important; }
  .do-head-icon--cyan   { background:var(--cyan-light)!important; }
  .do-head-icon--cyan svg { stroke:#06b6d4!important; }
  .do-head-icon--ink    { background:rgba(26,32,44,.06)!important; }
  .do-head-icon--ink svg { stroke:#1a202c!important; }
  .do-card-title    { font-size:15px;font-weight:700;color:var(--text-primary);line-height:1.3; }
  .do-card-subtitle { font-size:11px;color:var(--text-muted);font-weight:500;margin-top:2px; }
  .do-badge { display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;background:var(--bg-gray-100);color:var(--text-secondary);white-space:nowrap;flex-shrink:0; }
  .do-badge--tiktok { background:#ffe4ec;color:#9b1239; }
  .do-badge--green  { background:#d1fae5;color:#065f46; }
  .do-badge--amber  { background:#fef3c7;color:#92400e; }
  .do-badge--cyan   { background:#cffafe;color:#164e63; }
  .do-card-body { padding:20px;flex:1; }
  .do-card-body--flush { padding:0; }

  /* ── DONUT CARD ── */
  .ms-donut-legend { display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
  .ms-donut-leg-item { display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:600;color:var(--text-secondary);padding:4px 10px;background:var(--bg-gray-100);border-radius:20px;border:1px solid var(--border-gray);cursor:pointer;transition:var(--transition); }
  .ms-donut-leg-item:hover { border-color:var(--primary-green);background:var(--primary-green-light);color:var(--primary-green); }
  .ms-donut-dot { width:9px;height:9px;border-radius:50%;flex-shrink:0; }

  /* ── CHART HEIGHTS ── */
  .ms-ch-300 { position:relative;height:300px; }
  .ms-ch-320 { position:relative;height:320px; }
  .ms-ch-340 { position:relative;height:340px; }
  .ms-ch-460 { position:relative;height:460px; }

  /* ── CONTROLS ── */
  .ms-toggle-group { display:flex;background:var(--bg-gray-100);border-radius:var(--radius-xs);padding:3px;gap:2px;border:1px solid var(--border-gray); }
  .ms-toggle-btn { display:flex;align-items:center;gap:5px;padding:6px 14px;border-radius:6px;border:none;background:transparent;font-family:var(--font);font-size:12px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition);white-space:nowrap; }
  .ms-toggle-btn:hover { background:var(--bg-white);color:var(--text-primary); }
  .ms-toggle-btn.active { background:var(--bg-white);color:var(--primary-green);box-shadow:0 1px 4px rgba(0,0,0,.08); }

  .ms-rows-sel { padding:6px 12px;border:1px solid var(--border-gray);border-radius:var(--radius-xs);font-family:var(--font);font-size:12px;font-weight:600;color:var(--text-secondary);background:var(--bg-gray-100);outline:none;cursor:pointer;transition:var(--transition); }
  .ms-rows-sel:focus { border-color:var(--primary-green);box-shadow:0 0 0 2px var(--primary-green-light); }

  .ms-export-btn { display:flex;align-items:center;gap:5px;padding:6px 14px;background:var(--bg-gray-100);border:1px solid var(--border-gray);border-radius:var(--radius-xs);font-family:var(--font);font-size:12px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition);white-space:nowrap; }
  .ms-export-btn:hover { background:var(--primary-green);border-color:var(--primary-green);color:#fff; }
  .ms-export-btn svg { width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2.2; }

  /* ── POST LIST ── */
  .fme-post-list { display:flex;flex-direction:column; }
  .fme-post { display:flex;align-items:flex-start;gap:14px;padding:16px 20px;border-bottom:1px solid var(--border-light);transition:background .15s;cursor:pointer; }
  .fme-post:last-child { border-bottom:none; }
  .fme-post:hover { background:#fff5f8; }
  .fme-post-rank { width:28px;height:28px;border-radius:50%;background:var(--bg-gray-100);border:1.5px solid var(--border-gray);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:var(--text-muted);flex-shrink:0;margin-top:6px; }
  .fme-post-rank--1 { background:linear-gradient(135deg,#ffd700,#f59e0b);color:#7c5900;border-color:#ffd700; }
  .fme-post-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0; }
  .fme-post-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32; }
  .fme-post-av { width:46px;height:46px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#EE1D52,#c4163f);color:#fff;font-weight:700;font-size:14px;display:flex;align-items:center;justify-content:center;border:2px solid var(--border-gray);overflow:hidden; }
  .fme-post-av img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  .fme-post-body { flex:1;min-width:0; }
  .fme-post-author { font-size:13px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
  .fme-post-date   { font-size:10.5px;color:var(--text-muted);font-weight:400;margin-top:1px;margin-bottom:6px; }
  .fme-post-text   { font-size:12.5px;color:var(--text-secondary);line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:10px;word-break:break-word; }
  .fme-post-stats  { display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
  .fme-post-metric { display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;background:var(--bg-gray-100);border:1px solid var(--border-gray);color:var(--text-secondary);white-space:nowrap; }
  .fme-post-metric svg { width:11px;height:11px;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0; }
  .fme-post-metric--tiktok { background:var(--tiktok-light);border-color:var(--tiktok-border);color:#EE1D52; }
  .fme-post-metric--green  { background:var(--green-light);border-color:var(--green-border);color:#10b981; }
  .fme-post-metric--amber  { background:var(--amber-light);border-color:var(--amber-border);color:#f59e0b; }
  .fme-post-metric--cyan   { background:var(--cyan-light);border-color:var(--cyan-border);color:#06b6d4; }
  .fme-post-metric--tiktok svg { stroke:#EE1D52; }
  .fme-post-metric--green svg  { stroke:#10b981; }
  .fme-post-metric--amber svg  { stroke:#f59e0b; }
  .fme-post-metric--cyan svg   { stroke:#06b6d4; }
  .fme-sent { display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.3px; }
  .fme-sent--pos { background:#d1fae5;color:#065f46; }
  .fme-sent--neg { background:#fee2e2;color:#991b1b; }
  .fme-sent--neu { background:var(--bg-gray-100);color:var(--text-secondary); }
  .fme-view-link { display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:#EE1D52;text-decoration:none;padding:3px 9px;border-radius:20px;background:var(--tiktok-light);border:1px solid var(--tiktok-border);transition:var(--transition);margin-left:auto; }
  .fme-view-link:hover { background:#EE1D52;color:#fff; }
  .fme-view-link svg { width:9px;height:9px;stroke:currentColor;fill:none;stroke-width:2.5; }

  /* ── THUMBNAIL ── */
  .fme-post-thumb {
    width:100px; height:160px; border-radius:10px;
    flex-shrink:0; overflow:hidden;
    border:2px solid var(--border-gray);
    background:#1a202c;
    position:relative; align-self:center;
    box-shadow:var(--shadow-md);
  }
  .fme-post-thumb img {
    width:100%; height:100%; object-fit:cover; display:block;
    transition:transform .2s ease;
  }
  .fme-post:hover .fme-post-thumb img { transform:scale(1.06); }
  .fme-post-thumb-placeholder {
    width:100%; height:100%;
    display:flex; align-items:center; justify-content:center;
    font-size:28px; background:linear-gradient(135deg,#1a202c,#374151);
  }
  .fme-post-thumb-play {
    position:absolute; inset:0;
    display:flex; align-items:center; justify-content:center;
    background:rgba(0,0,0,.32);
    opacity:0; transition:opacity .2s;
    border-radius:8px;
  }
  .fme-post-thumb-play svg { width:26px;height:26px;fill:#fff;filter:drop-shadow(0 2px 6px rgba(0,0,0,.7)); }
  .fme-post:hover .fme-post-thumb-play { opacity:1; }

  /* ── PAGINATION ── */
  .fme-pagination { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid var(--border-light);flex-wrap:wrap;gap:10px; }
  .fme-pag-info { font-size:12px;color:var(--text-muted);font-weight:500; }
  .fme-pag-controls { display:flex;align-items:center;gap:4px; }
  .fme-pag-btn { min-width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;padding:0 8px;border-radius:var(--radius-xs);border:1px solid var(--border-gray);background:var(--bg-white);font-family:var(--font);font-size:12px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition);user-select:none; }
  .fme-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary-green);color:var(--primary-green);background:var(--primary-green-light); }
  .fme-pag-btn.is-active { background:var(--primary-green);border-color:var(--primary-green);color:#fff; }
  .fme-pag-btn:disabled { opacity:.4;cursor:not-allowed; }
  .fme-pag-btn svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round; }

  /* ── SKELETON ── */
  .loading-skeleton { background:linear-gradient(90deg,var(--bg-gray-50) 25%,#e2e8f0 50%,var(--bg-gray-50) 75%);background-size:200% 100%;animation:shimmer 1.5s ease-in-out infinite;border-radius:var(--radius-xs); }
  .skel-overlay { position:absolute;inset:0;z-index:3;border-radius:inherit; }
  @keyframes shimmer { 0%{background-position:200% 0}100%{background-position:-200% 0} }
  .fme-spinner { width:34px;height:34px;border:3px solid var(--border-gray);border-top-color:#EE1D52;border-radius:50%;animation:fmeSpin .7s linear infinite; }
  @keyframes fmeSpin { to{transform:rotate(360deg)} }
  .fme-spinner-state { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 20px;gap:14px;color:var(--text-secondary);font-size:13px;font-weight:500; }

  /* ── EMPTY STATE ── */
  .do-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 20px;gap:10px; }
  .do-empty svg { width:40px;height:40px;stroke:var(--border-gray);fill:none;stroke-width:1.5; }
  .do-empty-text { font-size:13px;font-weight:600;color:var(--text-secondary); }

  /* ── LAYOUT ── */
  .ms-grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px; }
  .ms-mb20 { margin-bottom:20px; }

  /* ── MENTION POPUP ── */
  @keyframes msPopIn { from{opacity:0;transform:translateY(14px) scale(.94)}to{opacity:1;transform:none} }
  #msPopup {
    position:fixed; z-index:99999;
    background:var(--bg-white);
    border:1px solid var(--border-gray);
    border-radius:20px;
    box-shadow:0 32px 80px rgba(0,0,0,.28), 0 0 0 1px rgba(0,0,0,.06);
    width:780px; height:680px; max-height:92vh;
    display:none; flex-direction:column;
    overflow:hidden;
    font-family:var(--font);
    animation:msPopIn .22s cubic-bezier(.34,1.3,.64,1);
    user-select:none;
  }
  #msPopup.visible { display:flex; }
  .msp-header { display:flex;align-items:center;gap:8px;padding:12px 16px;background:var(--bg-gray-50);border-bottom:1px solid var(--border-gray);cursor:grab;flex-shrink:0; }
  .msp-header:active { cursor:grabbing; }
  .msp-drag-handle { display:flex;flex-direction:column;gap:3px;margin-right:4px;flex-shrink:0;opacity:.4; }
  .msp-drag-handle span { display:block;width:18px;height:2px;background:var(--text-secondary);border-radius:1px; }
  .msp-dot   { width:10px;height:10px;border-radius:50%;flex-shrink:0; }
  .msp-title { font-size:13px;font-weight:700;color:var(--text-primary);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
  .msp-count { background:#EE1D52;color:#fff;border-radius:10px;padding:1px 9px;font-size:11px;font-weight:800;flex-shrink:0; }
  .msp-close { width:28px;height:28px;border-radius:var(--radius-xs);border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:20px;line-height:1;transition:var(--transition);flex-shrink:0; }
  .msp-close:hover { background:#ffe4ec;color:#9b1239; }
  .msp-actions { display:flex;align-items:center;gap:8px;padding:7px 13px;border-bottom:1px solid var(--border-gray);background:#fafbfc;flex-shrink:0; }
  .msp-meta { flex:1;font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:8px;overflow:hidden; }
  .msp-meta__label { overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
  .msp-export-btn { display:flex;align-items:center;gap:5px;padding:5px 11px;background:#EE1D52;color:#fff;border:none;border-radius:var(--radius-xs);font-family:var(--font);font-size:10px;font-weight:700;cursor:pointer;transition:var(--transition);white-space:nowrap; }
  .msp-export-btn:hover { background:#c4163f;transform:translateY(-1px); }
  .msp-export-btn svg { width:11px;height:11px;stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round; }
  .msp-list { overflow-y:auto;flex:1;padding:4px 0;min-height:0; }
  .msp-list::-webkit-scrollbar { width:5px; }
  .msp-list::-webkit-scrollbar-thumb { background:var(--border-gray);border-radius:4px; }
  .msp-item { display:flex;gap:10px;padding:10px 14px;border-bottom:1px solid var(--border-light);transition:background .1s;cursor:pointer;align-items:flex-start; }
  .msp-item:last-child { border-bottom:none; }
  .msp-item:hover { background:#fff5f8; }
  .msp-avatar { width:38px;height:38px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#EE1D52,#c4163f);color:#fff;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--border-gray);overflow:hidden; }
  .msp-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  .msp-item-body { flex:1;min-width:0; }
  .msp-item-author { font-size:12px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
  .msp-item-handle { font-size:10px;color:var(--text-muted);font-weight:500;margin-bottom:3px; }
  .msp-item-text { font-size:12px;color:var(--text-secondary);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px; }
  .msp-item-footer { display:flex;align-items:center;gap:6px;font-size:10px;color:var(--text-muted);flex-wrap:wrap; }
  .msp-sent { padding:1px 7px;border-radius:10px;font-size:9px;font-weight:800;text-transform:uppercase; }
  .msp-sent--pos { background:#d1fae5;color:#065f46; }
  .msp-sent--neg { background:#fee2e2;color:#991b1b; }
  .msp-sent--neu { background:var(--bg-gray-100);color:var(--text-secondary); }
  .msp-loading { display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:14px;color:var(--text-secondary);font-size:13px;font-weight:600; }
  .msp-spinner { width:32px;height:32px;border:3px solid var(--border-gray);border-top-color:#EE1D52;border-radius:50%;animation:msSpin .7s linear infinite; }
  @keyframes msSpin { to{transform:rotate(360deg)} }

  /* ── DETAIL PANEL ── */
  @keyframes msDetailIn {
    from { transform: translateX(100%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
  }
  #msDetailPanel {
    position: absolute; inset: 0;
    background: #ffffff; z-index: 10;
    display: none; flex-direction: column;
    animation: msDetailIn .28s cubic-bezier(.4,0,.2,1);
  }
  #msDetailPanel.visible { display: flex; }

  .msdp-header {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 20px;
    background: #fafbfc;
    border-bottom: 1px solid #e8edf2;
    flex-shrink: 0;
  }
  .msdp-back {
    width: 32px; height: 32px; border-radius: 10px;
    border: 1px solid #e2e8f0; background: #fff;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: #64748b; transition: all .18s ease; flex-shrink: 0;
  }
  .msdp-back:hover { background: #EE1D5214; color: #EE1D52; border-color: #EE1D5240; }
  .msdp-back svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }
  .msdp-title {
    font-size: 14px; font-weight: 700; color: #1a202c;
    flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }
  .msdp-close {
    width: 30px; height: 30px; border-radius: 10px;
    border: none; background: transparent; cursor: pointer;
    font-size: 20px; color: #94a3b8;
    display: flex; align-items: center; justify-content: center; transition: all .18s;
  }
  .msdp-close:hover { background: #ffe4ec; color: #9b1239; }

  .msdp-body {
    overflow-y: auto; flex: 1;
    padding: 0; scroll-behavior: smooth;
  }
  .msdp-body::-webkit-scrollbar { width: 6px; }
  .msdp-body::-webkit-scrollbar-thumb { background: #dde1e7; border-radius: 6px; }
  .msdp-body::-webkit-scrollbar-thumb:hover { background: #c5cbd5; }

  .msdp-inner { padding: 24px 26px; display: flex; flex-direction: column; gap: 22px; }

  .msdp-profile-row {
    display: flex; align-items: center; gap: 16px;
    padding: 18px 20px;
    background: linear-gradient(135deg, #fafbfc 0%, #f1f5f9 100%);
    border: 1px solid #e8edf2; border-radius: 16px;
  }
  .msdp-avatar-xl {
    width: 58px; height: 58px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 20px; color: #fff;
    border: 3px solid rgba(255,255,255,.9);
    box-shadow: 0 4px 16px rgba(0,0,0,.18);
    overflow: hidden; flex-shrink: 0;
  }
  .msdp-avatar-xl img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
  .msdp-profile-info { flex: 1; min-width: 0; }
  .msdp-author-name {
    font-size: 17px; font-weight: 800; color: #0f172a;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -.3px;
  }
  .msdp-author-handle { font-size: 12px; color: #94a3b8; font-weight: 500; margin-top: 2px; }
  .msdp-profile-badges { display: flex; align-items: center; gap: 8px; margin-top: 8px; flex-wrap: wrap; }
  .msdp-platform-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 20px;
    font-size: 11px; font-weight: 700; letter-spacing: .2px;
  }
  .msdp-date-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 20px;
    background: #f1f5f9; border: 1px solid #e2e8f0;
    font-size: 11px; font-weight: 600; color: #64748b;
  }
  .msdp-date-badge svg { width: 11px; height: 11px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }

  .msdp-sentiment-strip {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; border-radius: 12px; border: 1px solid;
  }
  .msdp-sentiment-strip--pos { background: #f0fdf4; border-color: #bbf7d0; }
  .msdp-sentiment-strip--neg { background: #fff5f5; border-color: #fecaca; }
  .msdp-sentiment-strip--neu { background: #f8fafc; border-color: #e2e8f0; }
  .msdp-sentiment-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 20px;
  }
  .msdp-sentiment-text { flex: 1; }
  .msdp-sentiment-label { font-size: 13px; font-weight: 700; }
  .msdp-sentiment-label--pos { color: #15803d; }
  .msdp-sentiment-label--neg { color: #b91c1c; }
  .msdp-sentiment-label--neu { color: #475569; }
  .msdp-sentiment-desc { font-size: 11px; font-weight: 500; color: #94a3b8; margin-top: 1px; }

  .msdp-metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
  .msdp-metric-card {
    background: #fff; border: 1.5px solid #e8edf2; border-radius: 14px;
    padding: 14px 12px 12px;
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    text-align: center; transition: all .2s ease;
    cursor: default; position: relative; overflow: hidden;
  }
  .msdp-metric-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    opacity: 0; transition: opacity .2s;
  }
  .msdp-metric-card:hover { transform: translateY(-2px); border-color: transparent; }
  .msdp-metric-card:hover::before { opacity: 1; }
  .msdp-metric-card--views::before  { background: linear-gradient(90deg,#EE1D52,#ff6b8a); }
  .msdp-metric-card--likes::before  { background: linear-gradient(90deg,#10b981,#34d399); }
  .msdp-metric-card--cmts::before   { background: linear-gradient(90deg,#f59e0b,#fbbf24); }
  .msdp-metric-card--shares::before { background: linear-gradient(90deg,#06b6d4,#22d3ee); }
  .msdp-metric-card:hover.msdp-metric-card--views  { box-shadow: 0 8px 24px rgba(238,29,82,.14); }
  .msdp-metric-card:hover.msdp-metric-card--likes  { box-shadow: 0 8px 24px rgba(16,185,129,.14); }
  .msdp-metric-card:hover.msdp-metric-card--cmts   { box-shadow: 0 8px 24px rgba(245,158,11,.14); }
  .msdp-metric-card:hover.msdp-metric-card--shares { box-shadow: 0 8px 24px rgba(6,182,212,.14); }
  .msdp-metric-icon {
    width: 34px; height: 34px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .msdp-metric-icon svg { width: 16px; height: 16px; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .msdp-metric-icon--views  { background: rgba(238,29,82,.1); }   .msdp-metric-icon--views svg  { stroke: #EE1D52; }
  .msdp-metric-icon--likes  { background: rgba(16,185,129,.1); }  .msdp-metric-icon--likes svg  { stroke: #10b981; }
  .msdp-metric-icon--cmts   { background: rgba(245,158,11,.1); }  .msdp-metric-icon--cmts svg   { stroke: #f59e0b; }
  .msdp-metric-icon--shares { background: rgba(6,182,212,.1); }   .msdp-metric-icon--shares svg { stroke: #06b6d4; }
  .msdp-metric-value { font-size: 20px; font-weight: 800; letter-spacing: -.5px; line-height: 1; }
  .msdp-metric-value--views  { color: #EE1D52; }
  .msdp-metric-value--likes  { color: #10b981; }
  .msdp-metric-value--cmts   { color: #f59e0b; }
  .msdp-metric-value--shares { color: #06b6d4; }
  .msdp-metric-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; }

  .msdp-section-heading { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
  .msdp-section-heading-icon {
    width: 26px; height: 26px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
  }
  .msdp-section-heading-icon svg { width: 13px; height: 13px; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .msdp-section-title { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .7px; color: #64748b; white-space: nowrap; }
  .msdp-section-line { flex: 1; height: 1px; background: #e8edf2; }

  .msdp-desc-box { background: #f8fafc; border: 1px solid #e8edf2; border-radius: 14px; padding: 16px 18px; }
  .msdp-desc-text {
    font-size: 13.5px; color: #374151; line-height: 1.75; word-break: break-word;
    display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;
  }
  .msdp-desc-text.expanded { display: block; -webkit-line-clamp: unset; overflow: visible; }
  .msdp-desc-toggle {
    margin-top: 10px; display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 700; color: #EE1D52;
    cursor: pointer; border: none; background: transparent;
    font-family: 'Poppins', sans-serif; padding: 0; transition: opacity .15s;
  }
  .msdp-desc-toggle:hover { opacity: .75; }
  .msdp-desc-toggle svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; transition: transform .2s; }
  .msdp-desc-toggle.expanded svg { transform: rotate(180deg); }
  .msdp-desc-empty { font-size: 13px; color: #94a3b8; font-style: italic; }

  .msdp-media-section { border-radius: 14px; overflow: hidden; border: 1px solid #e8edf2; box-shadow: 0 4px 16px rgba(0,0,0,.07); }
  .msdp-media-section iframe { width: 100%; height: 280px; border: none; display: block; }
  .msdp-media-label {
    display: flex; align-items: center; gap: 8px; padding: 9px 14px;
    background: #fafbfc; border-top: 1px solid #e8edf2;
    font-size: 11px; font-weight: 700; color: #64748b;
  }
  .msdp-media-label svg { width: 12px; height: 12px; fill: currentColor; flex-shrink: 0; }

  .msdp-cta-btn {
    display: flex; align-items: center; justify-content: center; gap: 9px;
    padding: 13px 20px;
    background: linear-gradient(135deg, #EE1D52 0%, #c4163f 100%);
    color: #fff; border-radius: 12px; font-size: 13px; font-weight: 700;
    text-decoration: none; transition: all .2s;
    box-shadow: 0 4px 16px rgba(238,29,82,.25); width: 100%;
  }
  .msdp-cta-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(238,29,82,.35); }
  .msdp-cta-btn svg { width: 14px; height: 14px; stroke: #fff; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

  /* ── DONUT TOOLTIP FIX ── */
  .echarts-tooltip-fix {
    max-width: 300px !important;
    word-break: break-word !important;
    white-space: normal !important;
    pointer-events: none !important;
  }

  /* Responsive */
  @media (max-width:860px) {
    #msPopup { width: 96vw !important; height: 88vh !important; }
    .msdp-metrics-grid { grid-template-columns: repeat(2, 1fr); }
    .msdp-inner { padding: 16px 18px; gap: 16px; }
  }
  @media (max-width:768px) {
    .fme-page { padding:16px; }
    .ms-stat-grid { grid-template-columns:1fr 1fr; }
    .ms-grid-2 { grid-template-columns:1fr; }
    .date-picker-container { flex-direction:column;width:96%; }
    .date-picker-sidebar { width:100%;flex-direction:row;overflow-x:auto;border-right:none;border-bottom:1px solid var(--border-gray);border-radius:var(--radius) var(--radius) 0 0;flex-shrink:0; }
    .date-preset { white-space:nowrap; }
    .calendars-wrapper { flex-direction:column; }
    .fme-post-thumb { width:90px; height:120px; }
  }
  @media (max-width:480px) {
    #msPopup { width: 100vw !important; height: 100vh !important; border-radius: 0 !important; }
    .msdp-section-heading-icon { display: none; }
    .msdp-metrics-grid { grid-template-columns: repeat(2, 1fr); }
    .fme-post-thumb { display: none; }
  }
</style>
@endsection

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
  $projects  = $projects ?? [];
@endphp

<div class="fme-page">

  {{-- PAGE HEADER --}}
  <div class="page-header">
    <div class="page-header-left">
      <h1>
        <svg viewBox="0 0 24 24" style="width:28px;height:28px;display:inline-block;vertical-align:middle;margin-right:10px;margin-top:-3px;fill:#EE1D52;">
          <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/>
        </svg>
        TikTok Most Engagement
      </h1>
      <p>Postingan dengan Most Views, Most Liked, Most Comments, dan Most Shares dari TikTok</p>
    </div>
    <button class="ms-refresh-btn" onclick="FME.reload()">
      <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
      Refresh
    </button>
  </div>

  {{-- FILTER CARD --}}
  <div class="filter-card">
    <form id="fmeForm" method="GET">
      <input type="hidden" name="project_id" id="hPid" value="{{ $projectId }}">
      <input type="hidden" name="start_date"  id="hSD"  value="{{ $startDate }}">
      <input type="hidden" name="end_date"    id="hED"  value="{{ $endDate }}">
      <div class="filter-content">
        @if(count($projects))
        <div class="filter-group">
          <label class="filter-label">Project</label>
          <select class="filter-select" onchange="document.getElementById('hPid').value=this.value">
            @foreach($projects as $p)
            <option value="{{ $p['id'] }}" {{ $p['id'] == $projectId ? 'selected' : '' }}>
              {{ $p['name'] ?? $p['title'] ?? 'Project #'.$p['id'] }}
            </option>
            @endforeach
          </select>
        </div>
        @endif
        <div class="filter-group">
          <label class="filter-label">Periode</label>
          <button type="button" class="date-picker-trigger" id="fmeDpTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="fmeDpDisplay">{{ $startDate }} – {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <div class="filter-group" style="margin-left:auto;">
          <label class="filter-label" style="opacity:0;pointer-events:none;">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Terapkan Filter
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- DATE PICKER MODAL --}}
  <div class="date-picker-modal" id="fmeDpModal" aria-modal="true" role="dialog">
    <div class="date-picker-overlay" onclick="FMEDp.close()"></div>
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
          <button class="nav-btn" onclick="FMEDp.nav(-1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="fmeDpCal1"></div>
            <div class="calendar" id="fmeDpCal2"></div>
          </div>
          <button class="nav-btn" onclick="FMEDp.nav(1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="date-picker-display">
          <span id="fmeDpRangeText">{{ $startDate }} – {{ $endDate }}</span>
        </div>
        <div class="date-picker-footer">
          <button class="cancel-btn" onclick="FMEDp.close()">Batal</button>
          <button class="apply-date-btn" onclick="FMEDp.apply()">Terapkan</button>
        </div>
      </div>
    </div>
  </div>

  {{-- SUMMARY STATS --}}
  <div class="ms-section-header">
    <div class="ms-section-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
    <span class="ms-section-title">Ringkasan Engagement</span>
    <div class="ms-section-line"></div>
  </div>

  <div class="ms-stat-grid">
    <div class="ms-stat-card ms-stat-card--tiktok">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#EE1D52;"></span>Total Views</div>
      <div class="ms-stat-value" id="valTotalViews"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Dari semua video</div>
    </div>
    <div class="ms-stat-card ms-stat-card--green">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#10b981;"></span>Total Likes</div>
      <div class="ms-stat-value" id="valTotalLikes"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Apresiasi konten</div>
    </div>
    <div class="ms-stat-card ms-stat-card--amber">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#f59e0b;"></span>Total Comments</div>
      <div class="ms-stat-value" id="valTotalCmts"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Interaksi komentar</div>
    </div>
    <div class="ms-stat-card ms-stat-card--cyan">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#06b6d4;"></span>Total Shares</div>
      <div class="ms-stat-value" id="valTotalShares"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Video dibagikan</div>
    </div>
  </div>

  {{-- DONUT SECTION --}}
  <div class="ms-section-header" id="donutSectionHead">
    <div class="ms-section-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg></div>
    <span class="ms-section-title" id="donutSectionLabel">Distribusi Views — Top 5</span>
    <div class="ms-section-line"></div>
  </div>
  <div class="do-card do-card--tiktok ms-mb20" id="donutMasterCard">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <div class="do-head-icon do-head-icon--tiktok" id="donutMasterIco">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
        </div>
        <div>
          <div class="do-card-title" id="donutMasterTitle">Top 5 Most Viewed — Distribution</div>
          <div class="do-card-subtitle">Proporsi engagement per video — hover segmen untuk detail</div>
        </div>
      </div>
      <div id="donutMasterLegend" class="ms-donut-legend"></div>
    </div>
    <div class="do-card-body">
      <div id="donutViewWrap">
        <div class="loading-skeleton" id="donutViewSkel" style="height:460px;border-radius:10px;"></div>
        <div id="donutViewChart" style="width:100%;height:460px;display:none;"></div>
        <div id="donutViewEmpty" style="display:none;"><div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg><span class="do-empty-text">Tidak ada data</span></div></div>
      </div>
      <div id="donutLikeWrap" style="display:none;">
        <div class="loading-skeleton" id="donutLikeSkel" style="height:460px;border-radius:10px;"></div>
        <div id="donutLikeChart" style="width:100%;height:460px;display:none;"></div>
        <div id="donutLikeEmpty" style="display:none;"><div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg><span class="do-empty-text">Tidak ada data</span></div></div>
      </div>
      <div id="donutCommentWrap" style="display:none;">
        <div class="loading-skeleton" id="donutCommentSkel" style="height:460px;border-radius:10px;"></div>
        <div id="donutCommentChart" style="width:100%;height:460px;display:none;"></div>
        <div id="donutCommentEmpty" style="display:none;"><div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg><span class="do-empty-text">Tidak ada data</span></div></div>
      </div>
      <div id="donutShareWrap" style="display:none;">
        <div class="loading-skeleton" id="donutShareSkel" style="height:460px;border-radius:10px;"></div>
        <div id="donutShareChart" style="width:100%;height:460px;display:none;"></div>
        <div id="donutShareEmpty" style="display:none;"><div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg><span class="do-empty-text">Tidak ada data</span></div></div>
      </div>
    </div>
  </div>

  {{-- TABS --}}
  <div class="ms-tabs">
    <button class="ms-tab-btn active active--tiktok" id="tab-view" onclick="FMETab.show('view')">
      <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      Most Viewed
      <span class="ms-tab-chip" id="chip-view">—</span>
    </button>
    <button class="ms-tab-btn" id="tab-like" onclick="FMETab.show('like')">
      <svg viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
      Most Liked
      <span class="ms-tab-chip" id="chip-like">—</span>
    </button>
    <button class="ms-tab-btn" id="tab-comment" onclick="FMETab.show('comment')">
      <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Most Comments
      <span class="ms-tab-chip" id="chip-comment">—</span>
    </button>
    <button class="ms-tab-btn" id="tab-share" onclick="FMETab.show('share')">
      <svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
      Most Shares
      <span class="ms-tab-chip" id="chip-share">—</span>
    </button>
  </div>

  {{-- ══ VIEW PANEL ══ --}}
  <div class="ms-tab-panel active" id="panel-view">
    <div class="do-card do-card--tiktok ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--tiktok">
            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </div>
          <div>
            <div class="do-card-title">Top Videos by Views</div>
            <div class="do-card-subtitle">Video TikTok dengan paling banyak views — klik untuk detail</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0;">
          <select class="ms-rows-sel" id="rows-view" onchange="FMEData.reloadTab('view')">
            <option value="10">Top 10</option><option value="20" selected>Top 20</option><option value="50">Top 50</option>
          </select>
          <button class="ms-export-btn" onclick="FMEData.exportCsv('view')">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>CSV
          </button>
          <span class="do-badge do-badge--tiktok" id="badge-view-full">Loading…</span>
        </div>
      </div>
      <div id="list-view" class="do-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data views…</div></div>
      <div id="pag-view"></div>
    </div>

    <div class="do-card do-card--tiktok ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--tiktok">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </div>
          <div><div class="do-card-title">Views Chart</div><div class="do-card-subtitle">Top 10 video berdasarkan views</div></div>
        </div>
        <span class="do-badge do-badge--tiktok">Top 10</span>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-320">
          <div id="ch-view" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="sk-view"></div>
        </div>
      </div>
    </div>

    <div class="ms-section-header">
      <div class="ms-section-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
      <span class="ms-section-title">Perbandingan Engagement — Top 10</span>
      <div class="ms-section-line"></div>
    </div>
    <div class="do-card do-card--ink ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--ink">
            <svg viewBox="0 0 24 24"><rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/></svg>
          </div>
          <div><div class="do-card-title">View vs Like vs Comment vs Share — Top 10 Videos</div><div class="do-card-subtitle">Perbandingan engagement keseluruhan pada video terpopuler</div></div>
        </div>
        <div class="ms-toggle-group" id="engTypeToggle">
          <button class="ms-toggle-btn active" data-type="stacked" onclick="FMEChart.setEngType('stacked')">Stacked</button>
          <button class="ms-toggle-btn" data-type="grouped" onclick="FMEChart.setEngType('grouped')">Grouped</button>
        </div>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-340">
          <div id="ch-eng" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="sk-eng"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ LIKE PANEL ══ --}}
  <div class="ms-tab-panel" id="panel-like">
    <div class="do-card do-card--green ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--green">
            <svg viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
          </div>
          <div>
            <div class="do-card-title">Top Videos by Likes</div>
            <div class="do-card-subtitle">Video TikTok dengan paling banyak likes — klik untuk detail</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0;">
          <select class="ms-rows-sel" id="rows-like" onchange="FMEData.reloadTab('like')">
            <option value="10">Top 10</option><option value="20" selected>Top 20</option><option value="50">Top 50</option>
          </select>
          <button class="ms-export-btn" onclick="FMEData.exportCsv('like')">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>CSV
          </button>
          <span class="do-badge do-badge--green" id="badge-like-full">Loading…</span>
        </div>
      </div>
      <div id="list-like" class="do-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data likes…</div></div>
      <div id="pag-like"></div>
    </div>
    <div class="do-card do-card--green ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--green">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </div>
          <div><div class="do-card-title">Likes Chart</div><div class="do-card-subtitle">Top 10 video berdasarkan likes</div></div>
        </div>
        <span class="do-badge do-badge--green">Top 10</span>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-320">
          <div id="ch-like" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="sk-like"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ COMMENT PANEL ══ --}}
  <div class="ms-tab-panel" id="panel-comment">
    <div class="do-card do-card--amber ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--amber">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <div>
            <div class="do-card-title">Top Videos by Comments</div>
            <div class="do-card-subtitle">Video TikTok dengan paling banyak komentar — klik untuk detail</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0;">
          <select class="ms-rows-sel" id="rows-comment" onchange="FMEData.reloadTab('comment')">
            <option value="10">Top 10</option><option value="20" selected>Top 20</option><option value="50">Top 50</option>
          </select>
          <button class="ms-export-btn" onclick="FMEData.exportCsv('comment')">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>CSV
          </button>
          <span class="do-badge do-badge--amber" id="badge-comment-full">Loading…</span>
        </div>
      </div>
      <div id="list-comment" class="do-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data comments…</div></div>
      <div id="pag-comment"></div>
    </div>
    <div class="do-card do-card--amber ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--amber">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </div>
          <div><div class="do-card-title">Comments Chart</div><div class="do-card-subtitle">Top 10 video berdasarkan komentar</div></div>
        </div>
        <span class="do-badge do-badge--amber">Top 10</span>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-320">
          <div id="ch-comment" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="sk-comment"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ SHARE PANEL ══ --}}
  <div class="ms-tab-panel" id="panel-share">
    <div class="do-card do-card--cyan ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--cyan">
            <svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
          </div>
          <div>
            <div class="do-card-title">Top Videos by Shares</div>
            <div class="do-card-subtitle">Video TikTok yang paling banyak dibagikan — klik untuk detail</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0;">
          <select class="ms-rows-sel" id="rows-share" onchange="FMEData.reloadTab('share')">
            <option value="10">Top 10</option><option value="20" selected>Top 20</option><option value="50">Top 50</option>
          </select>
          <button class="ms-export-btn" onclick="FMEData.exportCsv('share')">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>CSV
          </button>
          <span class="do-badge do-badge--cyan" id="badge-share-full">Loading…</span>
        </div>
      </div>
      <div id="list-share" class="do-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data shares…</div></div>
      <div id="pag-share"></div>
    </div>
    <div class="do-card do-card--cyan ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--cyan">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </div>
          <div><div class="do-card-title">Shares Chart</div><div class="do-card-subtitle">Top 10 video berdasarkan shares</div></div>
        </div>
        <span class="do-badge do-badge--cyan">Top 10</span>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-320">
          <div id="ch-share" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="sk-share"></div>
        </div>
      </div>
    </div>
  </div>

</div>{{-- /fme-page --}}

{{-- MENTION POPUP --}}
<div id="msPopup">
  <div class="msp-header" id="msPopHeader">
    <div class="msp-drag-handle"><span></span><span></span><span></span></div>
    <div class="msp-dot" id="msPopDot"></div>
    <span class="msp-title" id="msPopTitle">Video Detail</span>
    <span class="msp-count" id="msPopCount">…</span>
    <button class="msp-close" onclick="FMEPopup.close()">×</button>
  </div>
  <div class="msp-actions">
    <div class="msp-meta">
      <svg viewBox="0 0 24 24" style="width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <span class="msp-meta__label" id="msPopMeta">—</span>
    </div>
    <button class="msp-export-btn" onclick="FMEPopup.exportCsv()">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export CSV
    </button>
  </div>
  <div class="msp-list" id="msPopList"></div>
  <div id="msDetailPanel">
    <div class="msdp-header">
      <button class="msdp-back" onclick="FMEDetail.close()">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="msdp-title" id="msDpTitle">Detail Video</span>
      <button class="msdp-close" onclick="FMEPopup.close()">×</button>
    </div>
    <div class="msdp-body" id="msDpBody"></div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

/* ══ CONFIG ══ */
const FMECfg = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
  colors: { view:'#EE1D52', like:'#10b981', comment:'#f59e0b', share:'#06b6d4' },
  perPage: 10
};
const DONUT_COLORS = ['#2FC6F6','#f59e0b','#10b981','#8b5cf6','#f43f5e'];

/* ══ DATE PICKER ══ */
const FMEDp = (() => {
  let ds = null, de = null, m1 = new Date(), m2 = new Date(), pickStart = true;
  const MN = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD = ['Su','Mo','Tu','We','Th','Fr','Sa'];

  function init() {
    const si = document.getElementById('hSD'), ei = document.getElementById('hED');
    ds = si?.value ? new Date(si.value) : (() => { const d=new Date(); d.setDate(d.getDate()-6); return d; })();
    de = ei?.value ? new Date(ei.value) : new Date();
    m1 = new Date(ds); m2 = new Date(ds); m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('fmeDpTrigger').addEventListener('click', open);
    document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', onPreset));
    document.addEventListener('keydown', e => { if (e.key==='Escape' && document.getElementById('fmeDpModal').classList.contains('show')) close(); });
  }

  function open()  { document.getElementById('fmeDpModal').classList.add('show'); render(); }
  function close() { document.getElementById('fmeDpModal').classList.remove('show'); }

  function apply() {
    document.getElementById('hSD').value = fmt(ds);
    document.getElementById('hED').value = fmt(de);
    document.getElementById('fmeDpDisplay').textContent = fmt(ds)+' – '+fmt(de);
    close();
  }

  function nav(dir) { m1.setMonth(m1.getMonth()+dir); m2.setMonth(m2.getMonth()+dir); render(); }

  function onPreset(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    const today = new Date(); today.setHours(0,0,0,0);
    switch(e.target.dataset.p) {
      case 'today':     ds=new Date(today);de=new Date(today);break;
      case 'yesterday': ds=new Date(today);ds.setDate(today.getDate()-1);de=new Date(ds);break;
      case 'last7':     de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-6);break;
      case 'last30':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-29);break;
      case 'thismonth': ds=new Date(today.getFullYear(),today.getMonth(),1);de=new Date(today);break;
      case 'lastmonth': ds=new Date(today.getFullYear(),today.getMonth()-1,1);de=new Date(today.getFullYear(),today.getMonth(),0);break;
    }
    if (e.target.dataset.p!=='custom') { m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1); }
    updDisp(); render();
  }

  function render() { renderCal('fmeDpCal1',m1); renderCal('fmeDpCal2',m2); updDisp(); }

  function renderCal(id, month) {
    const el = document.getElementById(id); if (!el) return;
    const y=month.getFullYear(), mn=month.getMonth();
    const first=new Date(y,mn,1), last=new Date(y,mn+1,0), prevL=new Date(y,mn,0);
    const today=new Date(); today.setHours(0,0,0,0);
    let h=`<div class="calendar-month">${MN[mn]} ${y}</div>
      <div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div>
      <div class="calendar-days">`;
    for(let i=0;i<first.getDay();i++)
      h+=`<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++) {
      const date=new Date(y,mn,d); date.setHours(0,0,0,0);
      let cls='calendar-day';
      if(sameD(date,today)) cls+=' today';
      if(date>today) cls+=' disabled';
      if(ds&&de) {
        if(sameD(date,ds)||sameD(date,de)) cls+=' selected';
        else if(date>ds&&date<de) cls+=' in-range';
      }
      h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>';
    el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn=>{
      btn.addEventListener('click',function(){
        const d=new Date(this.dataset.date); d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-p="custom"]').classList.add('active');
        if(pickStart||d<ds){ds=d;de=d;pickStart=false;}
        else { if(d>=ds) de=d; else { de=ds;ds=d; } pickStart=true; }
        updDisp(); render();
      });
    });
  }

  function updDisp() { const el=document.getElementById('fmeDpRangeText'); if(el&&ds&&de) el.textContent=fmt(ds)+' – '+fmt(de); }
  function fmt(d) { if(!d) return ''; return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
  function sameD(a,b) { return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }

  return { init, open, close, apply, nav };
})();

/* ══ UTILS ══ */
const numFmt = n => parseInt(n||0).toLocaleString('id-ID');
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc    = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const emptyH = msg => `<div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="do-empty-text">${msg}</span></div>`;

const decodeStr = s => {
  if(!s) return '';
  try { const f=decodeURIComponent(escape(s)); if(!f.includes('\uFFFD')&&f!==s) return f; } catch(e){}
  return s;
};

/* ══ CHARTS REGISTRY ══ */
const FMECharts = {
  _i:{},
  make(id) {
    if(this._i[id]) { try{this._i[id].dispose();}catch(e){} }
    const dom=document.getElementById(id); if(!dom) return null;
    const c=echarts.init(dom,null,{renderer:'canvas'}); this._i[id]=c; return c;
  },
  disposeAll() { Object.values(this._i).forEach(c=>{try{c.dispose();}catch(e){}}); this._i={}; }
};
window.addEventListener('resize',()=>{ Object.values(FMECharts._i).forEach(c=>{try{if(!c.isDisposed())c.resize();}catch(e){}}); });

const EC_TT = {
  confine:true,
  backgroundColor:'#1a202c',borderColor:'#334155',borderWidth:1,padding:[10,14],
  textStyle:{color:'#fff',fontFamily:"'Poppins',sans-serif",fontSize:13},
  extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);'
};

/* ══ STORE & PAGINATION ══ */
const Store = { view:[], like:[], comment:[], share:[] };
const Pag   = { view:1, like:1, comment:1, share:1 };

/* ══ TAB CONFIG ══ */
const tabColorMap  = { view:'tiktok', like:'green', comment:'amber', share:'cyan' };
const tabTitleMap  = {
  view:'Top 5 Most Viewed — Distribution',
  like:'Top 5 Most Liked — Distribution',
  comment:'Top 5 Most Comments — Distribution',
  share:'Top 5 Most Shared — Distribution'
};
const tabLabelMap  = {
  view:'Distribusi Views — Top 5',
  like:'Distribusi Likes — Top 5',
  comment:'Distribusi Comments — Top 5',
  share:'Distribusi Shares — Top 5'
};

/* ══ TABS ══ */
const FMETab = {
  _loaded:{ view:false, like:false, comment:false, share:false },
  show(type) {
    ['view','like','comment','share'].forEach(t => {
      const tb=document.getElementById('tab-'+t), panel=document.getElementById('panel-'+t);
      const cap=t.charAt(0).toUpperCase()+t.slice(1);
      const wrap=document.getElementById('donut'+cap+'Wrap');
      const isThis=t===type;
      if(tb) {
        tb.classList.toggle('active',isThis);
        tb.classList.remove('active--tiktok','active--green','active--amber','active--cyan');
        if(isThis) tb.classList.add('active--'+tabColorMap[t]);
      }
      if(panel) panel.classList.toggle('active',isThis);
      if(wrap) wrap.style.display=isThis?'block':'none';
    });
    const col=tabColorMap[type];
    const card=document.getElementById('donutMasterCard');
    if(card) card.className='do-card do-card--'+col+' ms-mb20';
    const ico=document.getElementById('donutMasterIco');
    if(ico) ico.className='do-head-icon do-head-icon--'+col;
    const ttl=document.getElementById('donutMasterTitle');
    if(ttl) ttl.textContent=tabTitleMap[type];
    const lbl=document.getElementById('donutSectionLabel');
    if(lbl) lbl.textContent=tabLabelMap[type];
    const masterLeg=document.getElementById('donutMasterLegend');
    const srcLeg=document.getElementById('legendSrc-'+type);
    if(masterLeg) masterLeg.innerHTML=srcLeg?srcLeg.innerHTML:'';

    if(!this._loaded[type]) { this._loaded[type]=true; FMEData.loadTab(type); }
    else { requestAnimationFrame(()=>{ ['ch-view','ch-like','ch-comment','ch-share','ch-eng'].forEach(id=>{ const c=FMECharts._i[id]; try{if(c&&!c.isDisposed())c.resize();}catch(e){} }); }); }
  },
  reset() { this._loaded={view:false,like:false,comment:false,share:false}; }
};

/* ══ DATA ══ */
const FMEData = {
  async loadAll() {
    if(!FMECfg.pid) {
      ['valTotalViews','valTotalLikes','valTotalCmts','valTotalShares'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<span style="font-size:14px;color:#94a3b8;">—</span>';});
      ['list-view','list-like','list-comment','list-share'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML=emptyH('Pilih project terlebih dahulu');});
      ['sk-view','sk-like','sk-comment','sk-share','sk-eng','donutViewSkel','donutLikeSkel','donutCommentSkel','donutShareSkel'].forEach(hideSk);
      return;
    }
    FMETab._loaded.view=true;
    await this.loadTab('view');
  },

  async loadTab(type) {
    const subMap={ view:'postbyview', like:'postbylike', comment:'postbycomment', share:'postbyshare' };
    const rows=parseInt(document.getElementById('rows-'+type)?.value||'20');
    const listEl=document.getElementById('list-'+type);
    const chipEl=document.getElementById('chip-'+type);
    const badgeEl=document.getElementById('badge-'+type+'-full');
    if(listEl) listEl.innerHTML=`<div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data ${type}…</div>`;
    try {
const res=await fetch(`/mk/api/tiktok/most-engagement?project_id=${FMECfg.pid}&start_date=${FMECfg.sd}&end_date=${FMECfg.ed}&sub=${subMap[type]}&rows=${rows}`);
      const json=await res.json();
      let items=json.data||json||[]; if(!Array.isArray(items)) items=[];
      items=this._sort(items,type); Store[type]=items; Pag[type]=1;
      if(items.length) console.log('[FME DEBUG] item[0] keys:', Object.keys(items[0]), '\nitem[0]:', JSON.stringify(items[0]).slice(0,800));
      if(chipEl) chipEl.textContent=items.length;
      if(badgeEl) badgeEl.textContent=`${items.length} videos`;
      if(type==='view') { this._updateStats(items); this._renderEngChart(items); }
      this._renderList(type);
      this._renderBar(type,items.slice(0,10));
      this._renderDonut(type,items);
    } catch(err) {
      console.error(err);
      if(listEl) listEl.innerHTML=emptyH('Gagal memuat data: '+err.message);
      if(chipEl) chipEl.textContent='!';
      if(badgeEl) badgeEl.textContent='Error';
      const cap=type.charAt(0).toUpperCase()+type.slice(1);
      ['sk-'+type,'donut'+cap+'Skel'].forEach(hideSk);
    }
  },

  reloadTab(type) { Store[type]=[]; Pag[type]=1; this.loadTab(type); },

  _sort(items,type) {
    const keys={ view: ['view_cnt', 'views'], like:['likes','num_likes'], comment:['comments','num_comments'], share:['shares','num_shares'] };
    const ks=keys[type]||['view_cnt'];
    return [...items].sort((a,b)=>{
      const va=ks.reduce((v,k)=>v||parseInt(a[k]||0),0);
      const vb=ks.reduce((v,k)=>v||parseInt(b[k]||0),0);
      return vb-va;
    });
  },

  _metric(item,type) {
    const keys={ view:['view_cnt','views','freq'], like:['likes','num_likes'], comment:['comments','num_comments'], share:['shares','num_shares'] };
    return (keys[type]||['view_cnt']).reduce((v,k)=>v||parseInt(item[k]||0),0);
  },

  _updateStats(items) {
    let tV=0,tL=0,tC=0,tS=0;
    items.forEach(i=>{
      tV+=parseInt(i.view_cnt||i.views||i.freq||0);
      tL+=parseInt(i.likes||i.num_likes||0);
      tC+=parseInt(i.comments||i.num_comments||0);
      tS+=parseInt(i.shares||i.num_shares||0);
    });
    const eV=document.getElementById('valTotalViews'); if(eV) eV.textContent=numFmt(tV);
    const eL=document.getElementById('valTotalLikes'); if(eL) eL.textContent=numFmt(tL);
    const eC=document.getElementById('valTotalCmts'); if(eC) eC.textContent=numFmt(tC);
    const eS=document.getElementById('valTotalShares'); if(eS) eS.textContent=numFmt(tS);
  },

  _renderList(type) {
    const items=Store[type], listEl=document.getElementById('list-'+type), pagEl=document.getElementById('pag-'+type);
    if(!listEl) return;
    if(!items.length) { listEl.innerHTML=emptyH('Tidak ada data untuk periode ini'); if(pagEl) pagEl.innerHTML=''; return; }
    const page=Pag[type]||1, total=items.length, perPage=FMECfg.perPage, pages=Math.ceil(total/perPage), start=(page-1)*perPage;
    listEl.innerHTML=`<div class="fme-post-list">${items.slice(start,start+perPage).map((item,i)=>this._postHTML(item,start+i,type)).join('')}</div>`;
    if(pagEl) pagEl.innerHTML=this._pagHTML(type,page,pages,total,start+1,Math.min(start+perPage,total));
    listEl.querySelectorAll('.fme-post').forEach(el=>{
      el.addEventListener('click',()=>{
        try {
          const item=JSON.parse(decodeURIComponent(el.dataset.item));
          const lm={view:'Most Viewed Videos',like:'Most Liked Videos',comment:'Most Comments Videos',share:'Most Shared Videos'};
          FMEPopup.open(items,type,lm[type],items.length);
          FMEDetail.open(item,type);
        } catch(e){ console.warn(e); }
      });
    });
  },

  _pagHTML(type,page,pages,total,from,to) {
    if(pages<=1) return '';
    let btns=''; const r=2;
    btns+=`<button class="fme-pag-btn" ${page<=1?'disabled':''} onclick="FMEData.goPage('${type}',${page-1})"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>`;
    for(let i=1;i<=pages;i++) {
      if(i===1||i===pages||(i>=page-r&&i<=page+r)) btns+=`<button class="fme-pag-btn${i===page?' is-active':''}" onclick="FMEData.goPage('${type}',${i})">${i}</button>`;
      else if(i===page-r-1||i===page+r+1) btns+=`<span class="fme-pag-btn" style="cursor:default;opacity:.5;">…</span>`;
    }
    btns+=`<button class="fme-pag-btn" ${page>=pages?'disabled':''} onclick="FMEData.goPage('${type}',${page+1})"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>`;
    return `<div class="fme-pagination"><span class="fme-pag-info">Menampilkan ${from}–${to} dari ${total} videos</span><div class="fme-pag-controls">${btns}</div></div>`;
  },

  goPage(type,page) {
    Pag[type]=page; this._renderList(type);
    const el=document.getElementById('list-'+type); if(el) el.scrollIntoView({behavior:'smooth',block:'nearest'});
  },

  _getName(item) {
    const n=(item.name||item.author_scr_name||item.author_name||'').replace(/<[^>]*>/g,'').trim();
    if(n&&n!=='TikTok Creator') return n;
    return item.author_id?'@'+item.author_id:'TikTok Creator';
  },
  _getAvatar(item) {
    /* strictly profile/author photo — never video cover */
    return (item.avatar_url||item.author_avatar||item.profile_image||item.author_image||item.profile_picture||'').trim();
  },
  _getThumbnail(item) {
    /* use same fields as avatar - these are the fields that actually have images */
    return (item.avatar_url||item.profile_url||item.author_avatar||item.profile_image||item.author_image||item.profile_picture||item.thumbnail_url||item.cover_url||item.video_cover||item.thumbnail||item.image||item.media_url||'').trim();
  },
  _getInitials(name) {
    if(!name||name==='TikTok Creator') return 'TT';
    const w=name.replace(/[^a-zA-Z0-9\s@]/g,'').replace('@','').split(/\s+/).filter(Boolean);
    if(w.length>=2) return (w[0][0]+w[1][0]).toUpperCase();
    return (w[0]?.[0]||'T').toUpperCase();
  },
  _getAvatarColor(item) {
    const seed=item.author_id||item.id||this._getName(item)||'tt';
    const colors=['#EE1D52','#c4163f','#10b981','#f59e0b','#8b5cf6','#06b6d4','#ec4899','#14b8a6','#f97316','#6366f1'];
    let h=0; for(let i=0;i<seed.length;i++) h=(h*31+seed.charCodeAt(i))&0xffffffff;
    return colors[Math.abs(h)%colors.length];
  },
  _avatarHtml(item) {
    const name=this._getName(item), av=this._getAvatar(item), ini=this._getInitials(name);
    const safeIni=ini.replace(/['"\\]/g,'');
    if(av&&av.startsWith('http')) return `<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`;
    return ini;
  },

  /* ── UPDATED: _postHTML with thumbnail ── */
  _postHTML(item,globalIdx,type) {
    const rank=globalIdx+1, rkCls=rank===1?'--1':rank===2?'--2':rank===3?'--3':'';
    const name=this._getName(item);
    const avColor=this._getAvatarColor(item);
    const avHtml=this._avatarHtml(item);
    const content=decodeStr((item.content||item.caption||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()).slice(0,200);
    const dt=(item.date_created||'').split('T')[0];
    const url=item.url||item.link||'';
    const thumb=this._getThumbnail(item);
    const views=parseInt(item.view_cnt||item.views||item.freq||0);
    const likes=parseInt(item.likes||item.num_likes||0);
    const cmts=parseInt(item.comments||item.num_comments||0);
    const shares=parseInt(item.shares||item.num_shares||0);
    const totalEng=views+likes+cmts+shares;
    const sentRaw=String(item.sentiment_str||item.sentiment||'').toLowerCase();
    const sent=sentRaw.includes('pos')?'pos':sentRaw.includes('neg')?'neg':'neu';
    const sentLbl=sent==='pos'?'Positive':sent==='neg'?'Negative':'Neutral';
    const vV=type==='view'?' fme-post-metric--tiktok':'';
    const lV=type==='like'?' fme-post-metric--green':'';
    const cV=type==='comment'?' fme-post-metric--amber':'';
    const sV=type==='share'?' fme-post-metric--cyan':'';
    const itemEnc=encodeURIComponent(JSON.stringify(item));

    /* thumbnail html */
    let thumbHtml = '';
    if(thumb && thumb.startsWith('http')) {
      thumbHtml = `<div class="fme-post-thumb">
        <img src="${esc(thumb)}" alt="thumbnail" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'fme-post-thumb-placeholder\\'>🎵</div>'">
        <div class="fme-post-thumb-play"><svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg></div>
      </div>`;
    } else {
      thumbHtml = `<div class="fme-post-thumb"><div class="fme-post-thumb-placeholder">🎵</div></div>`;
    }

    return `<div class="fme-post" data-item="${esc(itemEnc)}" data-name="${esc(name)}">
      <div class="fme-post-rank fme-post-rank${rkCls}">${rank}</div>
      <div class="fme-post-body">
        <div class="fme-post-author">${esc(name)}</div>
        ${dt?`<div class="fme-post-date">${dt}</div>`:''}
        ${content?`<div class="fme-post-text">${esc(content)}</div>`:''}
        <div class="fme-post-stats">
          <span class="fme-post-metric${vV}"><svg viewBox="0 0 24 24" stroke="${type==='view'?'#EE1D52':'currentColor'}"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>${numFmt(views)}</span>
          <span class="fme-post-metric${lV}"><svg viewBox="0 0 24 24" stroke="${type==='like'?'#10b981':'currentColor'}"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>${numFmt(likes)}</span>
          <span class="fme-post-metric${cV}"><svg viewBox="0 0 24 24" stroke="${type==='comment'?'#f59e0b':'currentColor'}"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>${numFmt(cmts)}</span>
          <span class="fme-post-metric${sV}"><svg viewBox="0 0 24 24" stroke="${type==='share'?'#06b6d4':'currentColor'}"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>${numFmt(shares)}</span>
          <span class="fme-post-metric" style="background:rgba(100,116,139,.08);border-color:rgba(100,116,139,.2);color:#64748b;font-weight:800;">∑ ${numFmt(totalEng)}</span>
          <span class="fme-sent fme-sent--${sent}">${sentLbl}</span>
          ${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="fme-view-link" onclick="event.stopPropagation()"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Lihat</a>`:''}
        </div>
      </div>
      ${thumbHtml}
    </div>`;
  },

  _renderBar(type,items) {
    hideSk('sk-'+type);
    if(!items.length) return;
    const color=FMECfg.colors[type];
    const metricLbl={view:'Views',like:'Likes',comment:'Comments',share:'Shares'}[type];
    const labels=items.map(it=>{ const n=this._getName(it); return n.length>16?n.slice(0,15)+'…':n; });
    const values=items.map(it=>this._metric(it,type));
    const chart=FMECharts.make('ch-'+type); if(!chart) return;
    chart.setOption({
      animation:true,animationDuration:800,animationEasing:'elasticOut',backgroundColor:'#ffffff',
      tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'shadow',shadowStyle:{color:'rgba(238,29,82,.04)'}},
        formatter:p=>{
          const it=items[p[0]?.dataIndex];
          const name=it?this._getName(it):(p[0]?.name||'');
          const sentRaw=String(it?.sentiment_str||it?.sentiment||'').toLowerCase();
          const sent=sentRaw.includes('pos')?'Positive':sentRaw.includes('neg')?'Negative':'Neutral';
          const sc=sent==='Positive'?'#10b981':sent==='Negative'?'#ef4444':'#94a3b8';
          const sb=sent==='Positive'?'rgba(16,185,129,.2)':sent==='Negative'?'rgba(239,68,68,.2)':'rgba(148,163,184,.15)';
          return `<div style="min-width:200px;">
            <div style="font-weight:700;font-size:13px;margin-bottom:6px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.12);">${esc(name)}</div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
              <span style="font-size:12px;color:#94a3b8;">${metricLbl}</span>
              <span style="font-size:13px;font-weight:700;">${numFmt(p[0].value)}</span>
            </div>
            ${it?`<div style="margin-top:6px;"><span style="background:${sb};color:${sc};border-radius:6px;padding:2px 9px;font-size:11px;font-weight:700;">${sent}</span></div>`:''}
          </div>`;
        }
      },
      grid:{top:14,right:18,bottom:52,left:16,containLabel:true},
      xAxis:{type:'category',data:labels,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'600',color:'#64748b',interval:0,rotate:labels.length>6?30:0}},
      yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},
      series:[{type:'bar',data:values.map(v=>({value:v,itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:color},{offset:1,color:color+'44'}]},borderRadius:[7,7,0,0]},emphasis:{itemStyle:{color,shadowBlur:12,shadowColor:color+'55'}}})),barMaxWidth:44,label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:10,color:'#64748b',formatter:p=>numK(p.value)}}]
    });
    chart.on('click',params=>{
      const item=items[params.dataIndex]; if(!item) return;
      const lm={view:'Most Viewed Videos',like:'Most Liked Videos',comment:'Most Comments Videos',share:'Most Shared Videos'};
      FMEPopup.open(Store[type].length?Store[type]:items,type,lm[type],Store[type].length||items.length);
      FMEDetail.open(item,type);
    });
  },

  _renderDonut(type,items) {
    const cap=type.charAt(0).toUpperCase()+type.slice(1);
    const skel=document.getElementById('donut'+cap+'Skel');
    const chartEl=document.getElementById('donut'+cap+'Chart');
    const emptyEl=document.getElementById('donut'+cap+'Empty');
    const metricLbl={view:'Views',like:'Likes',comment:'Comments',share:'Shares'}[type];
    if(!items.length) { if(skel)skel.style.display='none'; if(emptyEl)emptyEl.style.display='block'; return; }
    const top5=items.slice(0,5);
    const total=top5.reduce((s,it)=>s+this._metric(it,type),0);
    /* total engagement (views+likes+cmts+shares) per item */
    const legendHTML=top5.map((it,i)=>{
      const n=this._getName(it);
      const sn=n.length>20?n.slice(0,19)+'…':n;
      return `<div class="ms-donut-leg-item" onclick="FMEDonut.highlight('${type}',${i})"><span class="ms-donut-dot" style="background:${DONUT_COLORS[i]};"></span>${sn} · ${numFmt(this._metric(it,type))}</div>`;
    }).join('');
    let srcLeg=document.getElementById('legendSrc-'+type);
    if(!srcLeg){ srcLeg=document.createElement('div'); srcLeg.id='legendSrc-'+type; srcLeg.style.display='none'; document.body.appendChild(srcLeg); }
    srcLeg.innerHTML=legendHTML;
    const activeTab=['view','like','comment','share'].find(t=>document.getElementById('tab-'+t)?.classList.contains('active'))||'view';
    const masterLeg=document.getElementById('donutMasterLegend');
    if(masterLeg&&activeTab===type) masterLeg.innerHTML=legendHTML;

    if(skel) skel.style.display='none'; if(chartEl) chartEl.style.display='block';
    const chart=FMECharts.make('donut'+cap+'Chart'); if(!chart) return;

    const _self = this;
    const pieData=top5.map((it,i)=>{
      const name=this._getName(it);
      const val=this._metric(it,type);
      const rawContent=(it.content||it.caption||'').replace(/<[^>]*>/g,'').trim();
      const content=decodeStr(rawContent);
      const sentRaw=String(it.sentiment_str||it.sentiment||'').toLowerCase();
      const sentiment=sentRaw.includes('pos')?'Positive':sentRaw.includes('neg')?'Negative':'Neutral';
      /* total engagement for this item */
      const totalItemEng = parseInt(it.view_cnt||it.views||it.freq||0)
                         + parseInt(it.likes||it.num_likes||0)
                         + parseInt(it.comments||it.num_comments||0)
                         + parseInt(it.shares||it.num_shares||0);
      return {name,value:val,content,sentiment,color:DONUT_COLORS[i],totalItemEng,
              itemStyle:{color:DONUT_COLORS[i],borderColor:'#fff',borderWidth:3}};
    });

    chart.setOption({
      animation:true,animationDuration:1000,animationEasing:'cubicOut',backgroundColor:'#ffffff',
      tooltip:{show:false},
      legend:{show:false},
      series:[{
        type:'pie',radius:['36%','54%'],center:['50%','50%'],avoidLabelOverlap:true,minAngle:8,padAngle:1.5,
        itemStyle:{borderColor:'#ffffff',borderWidth:3},
        label:{show:true,position:'outside',alignTo:'edge',edgeDistance:14,lineHeight:17,fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#475569',fontWeight:'500',
          formatter:function(p){
            const d=p.data, pct=total>0?((d.value/total)*100).toFixed(1):'0';
            const nm=d.name.length>30?d.name.slice(0,29)+'…':d.name;
            const snip=d.content?(d.content.length>40?d.content.slice(0,39)+'…':d.content):'';
            const line2=snip?`{snip|${snip}}\n`:'';
            return `{name|${nm}}\n${line2}{pct|${numFmt(d.value)}  ·  ${pct}%}`;
          },
          rich:{name:{fontSize:11,fontWeight:'700',color:'#1e293b',lineHeight:17},snip:{fontSize:10,fontWeight:'400',color:'#64748b',lineHeight:15},pct:{fontSize:10,fontWeight:'600',color:'#94a3b8',lineHeight:15}}
        },
        labelLine:{show:true,length:12,length2:20,smooth:.3,lineStyle:{width:1.2,color:'#cbd5e1'}},
        emphasis:{
          scale:true,scaleSize:8,
          itemStyle:{shadowBlur:20,shadowColor:'rgba(0,0,0,.22)'},
          label:{
            show:true,fontWeight:'800',fontSize:13,
            rich:{
              name:{fontSize:13,fontWeight:'800',color:'#0f172a',lineHeight:19},
              snip:{fontSize:11,fontWeight:'500',color:'#475569',lineHeight:16},
              pct:{fontSize:11,fontWeight:'700',color:'#1e293b',lineHeight:16}
            }
          }
        },
        data:pieData
      }],
      graphic:[
        {type:'text',left:'center',top:'44%',z:100,style:{text:numFmt(total),fill:'#0f172a',font:"800 28px 'Poppins',sans-serif",textAlign:'center'}},
        {type:'text',left:'center',top:'53%',z:100,style:{text:'TOTAL '+metricLbl.toUpperCase(),fill:'#94a3b8',font:"600 9px 'Poppins',sans-serif",textAlign:'center'}}
      ]
    });
    chart.on('mouseover',function(params){
      if(params.dataIndex===undefined) return;
      chart.dispatchAction({type:'highlight',seriesIndex:0,dataIndex:params.dataIndex});
    });
    chart.on('mouseout',function(params){
      if(params.dataIndex===undefined) return;
      chart.dispatchAction({type:'downplay',seriesIndex:0,dataIndex:params.dataIndex});
    });
    chart.on('click',params=>{
      const item=items[params.dataIndex]; if(!item) return;
      const lm={view:'Most Viewed',like:'Most Liked',comment:'Most Comments',share:'Most Shared'};
      FMEPopup.open(Store[type].length?Store[type]:items,type,lm[type],Store[type].length||items.length);
      FMEDetail.open(item,type);
    });
  },

  _renderEngChart(items) {
    hideSk('sk-eng');
    if(!items.length) return;
    const top10=[...items].map(it=>({...it,_total:parseInt(it.view_cnt||it.views||it.freq||0)+parseInt(it.likes||0)+parseInt(it.comments||0)+parseInt(it.shares||0)})).sort((a,b)=>b._total-a._total).slice(0,10);
    FMEChart._items=top10; FMEChart._render(top10,FMEChart._type);
  },

  exportCsv(type) {
    const items=Store[type]; if(!items?.length){ alert('Tidak ada data.'); return; }
    const header='index;nama;sentiment;views;likes;comments;shares;total_engagement;tanggal;url;konten';
    const rows=items.map((it,i)=>{
      const name=this._getName(it);
      const sentRaw=String(it.sentiment_str||it.sentiment||'').toLowerCase();
      const sent=sentRaw.includes('pos')?'Positif':sentRaw.includes('neg')?'Negatif':'Netral';
      const views=parseInt(it.view_cnt||it.views||it.freq||0);
      const likes=parseInt(it.likes||0);
      const cmts=parseInt(it.comments||0);
      const shares=parseInt(it.shares||0);
      const totalEng=views+likes+cmts+shares;
      const dt=(it.date_created||'').split('T')[0];
      const url=it.url||it.link||'';
      const content=(it.content||it.caption||'').replace(/<[^>]*>/g,'').trim().slice(0,300).replace(/;/g,',').replace(/\n/g,' ');
      return `${i};${name.replace(/;/g,',')};${sent};${views};${likes};${cmts};${shares};${totalEng};${dt};${url};${content}`;
    });
    const blob=new Blob(['\uFEFF'+[header,...rows].join('\r\n')],{type:'text/csv;charset=utf-8;'});
    const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download=`TikTok_Most${type.charAt(0).toUpperCase()+type.slice(1)}_${FMECfg.sd}_${FMECfg.ed}.csv`; a.click();
  }
};

const FMEDonut = {
  highlight(type,idx) {
    const cap=type.charAt(0).toUpperCase()+type.slice(1);
    const chart=FMECharts._i['donut'+cap+'Chart']; if(!chart) return;
    chart.dispatchAction({type:'highlight',seriesIndex:0,dataIndex:idx});
    chart.dispatchAction({type:'showTip',seriesIndex:0,dataIndex:idx});
  }
};

const FMEChart = {
  _type:'stacked', _items:[],
  setEngType(t) {
    this._type=t;
    document.querySelectorAll('#engTypeToggle .ms-toggle-btn').forEach(b=>b.classList.toggle('active',b.dataset.type===t));
    if(this._items.length) this._render(this._items,t);
  },
  _render(items,stackType) {
    const chart=FMECharts.make('ch-eng'); if(!chart) return;
    const labels=items.map(it=>{ const n=FMEData._getName(it); return n.length>18?n.slice(0,17)+'…':n; });
    const views  =items.map(it=>parseInt(it.view_cnt||it.views||it.freq||0));
    const likes  =items.map(it=>parseInt(it.likes||0));
    const cmts   =items.map(it=>parseInt(it.comments||0));
    const shares =items.map(it=>parseInt(it.shares||0));
    const isStack=stackType==='stacked';
    const lbl={show:true,position:isStack?'inside':'insideBottom',distance:isStack?0:15,align:isStack?'center':'left',verticalAlign:'middle',rotate:isStack?0:90,formatter:p=>p.value>0?numK(p.value):'',fontSize:9,fontFamily:"'Poppins',sans-serif",fontWeight:'700',color:'#fff'};
    chart.setOption({
      animation:true,animationDuration:900,animationEasing:'elasticOut',backgroundColor:'#ffffff',
      tooltip:{
        ...EC_TT,trigger:'axis',axisPointer:{type:'shadow',shadowStyle:{color:'rgba(238,29,82,.04)'}},
        formatter:params=>{
          const idx=params[0]?.dataIndex, it=items[idx];
          const name=it?FMEData._getName(it):(params[0]?.name||'');
          const total=params.reduce((s,p)=>s+(p.value||0),0);
          const rows=params.map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:7px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};flex-shrink:0;"></span><span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span></div>`).join('');
          return `<div style="min-width:200px;"><div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.12);">${esc(name)}</div>${rows}<div style="border-top:1px solid rgba(255,255,255,.12);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;"><span style="font-size:11px;color:#94a3b8;">Total Engagement</span><span style="font-size:13px;font-weight:700;">${numFmt(total)}</span></div></div>`;
        }
      },
      legend:{bottom:0,data:['Views','Likes','Comments','Shares'],textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},icon:'circle',itemWidth:10,itemHeight:10,itemGap:20},
      grid:{top:16,right:20,bottom:52,left:16,containLabel:true},
      xAxis:{type:'category',data:labels,axisTick:{show:false},axisLine:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'600',color:'#64748b',interval:0,rotate:labels.length>7?30:0}},
      yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},
      series:[
        {name:'Views',   type:'bar',barGap:0,stack:isStack?'eng':undefined,data:views,  barMaxWidth:isStack?54:26,itemStyle:{color:'#EE1D52',borderRadius:isStack?[0,0,0,0]:[4,4,0,0]},emphasis:{focus:'series'},label:lbl},
        {name:'Likes',   type:'bar',barGap:0,stack:isStack?'eng':undefined,data:likes,  barMaxWidth:isStack?54:26,itemStyle:{color:'#10b981',borderRadius:isStack?[0,0,0,0]:[4,4,0,0]},emphasis:{focus:'series'},label:lbl},
        {name:'Comments',type:'bar',barGap:0,stack:isStack?'eng':undefined,data:cmts,   barMaxWidth:isStack?54:26,itemStyle:{color:'#f59e0b',borderRadius:isStack?[0,0,0,0]:[4,4,0,0]},emphasis:{focus:'series'},label:lbl},
        {name:'Shares',  type:'bar',barGap:0,stack:isStack?'eng':undefined,data:shares, barMaxWidth:isStack?54:26,itemStyle:{color:'#06b6d4',borderRadius:isStack?[4,4,0,0]:[4,4,0,0]},emphasis:{focus:'series'},label:lbl}
      ]
    },true);
    chart.on('click',params=>{
      const item=items[params.dataIndex]; if(!item) return;
      const sm={'Views':'view','Likes':'like','Comments':'comment','Shares':'share'};
      const ct=sm[params.seriesName]||'view';
      const ai=Store[ct].length?Store[ct]:items;
      const lm={view:'Most Viewed Videos',like:'Most Liked Videos',comment:'Most Comments Videos',share:'Most Shared Videos'};
      FMEPopup.open(ai,ct,lm[ct],ai.length);
      FMEDetail.open(item,ct);
    });
  }
};

/* ══ POPUP ══ */
const FMEPopup = {
  _drag:false,_ox:0,_oy:0,_items:[],_curType:null,

  init() {
    const popup=document.getElementById('msPopup');
    const header=document.getElementById('msPopHeader');
    header.addEventListener('mousedown',e=>{
      this._drag=true;
      const r=popup.getBoundingClientRect();
      this._ox=e.clientX-r.left; this._oy=e.clientY-r.top;
      document.body.style.userSelect='none';
    });
    document.addEventListener('mousemove',e=>{
      if(!this._drag) return;
      const vw=window.innerWidth,vh=window.innerHeight;
      popup.style.left=Math.max(0,Math.min(e.clientX-this._ox,vw-popup.offsetWidth))+'px';
      popup.style.top=Math.max(0,Math.min(e.clientY-this._oy,vh-popup.offsetHeight))+'px';
    });
    document.addEventListener('mouseup',()=>{ this._drag=false; document.body.style.userSelect=''; });
  },

  open(items,type,title,count) {
    const popup=document.getElementById('msPopup');
    const colorMap={view:'#EE1D52',like:'#10b981',comment:'#f59e0b',share:'#06b6d4'};
    this._items=items||[]; this._curType=type;
    FMEDetail.close();
    document.getElementById('msPopDot').style.background=colorMap[type]||'#EE1D52';
    document.getElementById('msPopTitle').textContent=title||'Video Detail';
    document.getElementById('msPopCount').textContent=numFmt(count||items.length);
    document.getElementById('msPopMeta').textContent=FMECfg.sd+' – '+FMECfg.ed;
    popup.classList.add('visible');
    const pw=780,ph=680,vw=window.innerWidth,vh=window.innerHeight;
    popup.style.left=Math.max(8,(vw-pw)/2)+'px';
    popup.style.top=Math.max(8,(vh-ph)/2)+'px';
    this._render(document.getElementById('msPopList'),items,type);
  },

  close() { FMEDetail._killIframe(); FMEDetail.close(); document.getElementById('msPopup')?.classList.remove('visible'); },
  exportCsv() { FMEData.exportCsv(this._curType||'view'); },

  _render(list,items,type) {
    if(!items?.length) { list.innerHTML=`<div class="msp-loading"><div class="msp-spinner"></div>Tidak ada data</div>`; return; }
    const metricLbl={view:'Views',like:'Likes',comment:'Komentar',share:'Shares'};
    list.innerHTML=items.slice(0,100).map(item=>{
      const name=FMEData._getName(item);
      const avColor=FMEData._getAvatarColor(item);
      const av=FMEData._getAvatar(item);
      const ini=FMEData._getInitials(name);
      const safeIni=ini.replace(/['"\\]/g,'');
      const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`:ini;
      const rawText=(item.content||item.caption||'').replace(/<[^>]*>/g,'').trim();
      const text=rawText?decodeStr(rawText).slice(0,140):'';
      const metVal=FMEData._metric(item,type);
      const dt=(item.date_created||'').split('T')[0];
      const sentRaw=String(item.sentiment_str||item.sentiment||'').toLowerCase();
      const sent=sentRaw.includes('pos')?'pos':sentRaw.includes('neg')?'neg':'neu';
      const sentLbl=sent==='pos'?'Pos':sent==='neg'?'Neg':'Neu';
      const totalEng=parseInt(item.view_cnt||item.views||item.freq||0)+parseInt(item.likes||item.num_likes||0)+parseInt(item.comments||item.num_comments||0)+parseInt(item.shares||item.num_shares||0);
      const itemEnc=encodeURIComponent(JSON.stringify(item));
      return `<div class="msp-item" data-item="${esc(itemEnc)}" data-type="${type}" onclick="FMEPopup._onItemClick(this)">
        <div class="msp-avatar" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div>
        <div class="msp-item-body">
          <div class="msp-item-author">${esc(name)}</div>
          <div class="msp-item-text">${esc(text||'(tidak ada konten)')}</div>
          <div class="msp-item-footer">
            <span class="msp-sent msp-sent--${sent}">${sentLbl}</span>
            <span>${metricLbl[type]} ${numFmt(metVal)}</span>
            <span>∑ ${numFmt(totalEng)}</span>
            ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
          </div>
        </div>
      </div>`;
    }).join('');
    if(items.length>100) list.insertAdjacentHTML('beforeend',`<div style="padding:9px 14px;text-align:center;font-size:11px;font-weight:600;color:#64748b;background:var(--bg-gray-50);border-top:1px dashed var(--border-gray);">+${(items.length-100).toLocaleString()} videos lainnya</div>`);
  },

  _onItemClick(el) {
    try {
      const raw=el.getAttribute('data-item');
      const item=JSON.parse(decodeURIComponent(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"')));
      FMEDetail.open(item,el.dataset.type||this._curType);
    } catch(e){ console.warn(e); }
  }
};

/* ══ DETAIL PANEL ══ */
const FMEDetail = {
  open(item, type) {
    const panel = document.getElementById('msDetailPanel');
    const body  = document.getElementById('msDpBody');
    const title = document.getElementById('msDpTitle');

    const colorMap = { view:'#EE1D52', like:'#10b981', comment:'#f59e0b', share:'#06b6d4' };
    const labelMap = { view:'Most Viewed', like:'Most Liked', comment:'Most Comments', share:'Most Shared' };
    const color  = colorMap[type] || '#EE1D52';
    const label  = labelMap[type] || 'TikTok Video';

    const name      = FMEData._getName(item);
    const av        = FMEData._getAvatar(item);
    const avColor   = FMEData._getAvatarColor(item);
    const ini       = FMEData._getInitials(name);
    const safeIni   = ini.replace(/['"\\]/g, '');
    const handle    = item.author_scr_name || item.author_id || '';
    const rawContent= (item.content || item.caption || '').replace(/<[^>]*>/g, '').trim();
    const content   = rawContent ? decodeStr(rawContent) : '';
    const date      = item.date_created || '';
    const url       = item.url || item.link || '';

    const views  = parseInt(item.view_cnt  || item.views   || item.freq || 0);
    const likes  = parseInt(item.likes     || item.num_likes || 0);
    const cmts   = parseInt(item.comments  || item.num_comments || 0);
    const shares = parseInt(item.shares    || item.num_shares || 0);

    const sentRaw  = String(item.sentiment_str || item.sentiment || '').toLowerCase();
    const sent     = sentRaw.includes('pos') ? 'pos' : sentRaw.includes('neg') ? 'neg' : 'neu';
    const sentLbl  = { pos:'Positive', neg:'Negative', neu:'Neutral' }[sent];
    const sentDesc = { pos:'Post menunjukkan sentimen positif', neg:'Post menunjukkan sentimen negatif', neu:'Post bersifat netral' }[sent];
    const sentEmoji= { pos:'😊', neg:'😞', neu:'😐' }[sent];
    const sentBg   = { pos:'rgba(16,185,129,.12)', neg:'rgba(239,68,68,.12)', neu:'rgba(100,116,139,.1)' }[sent];

    const avHtml = (av && av.startsWith('http'))
      ? `<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`
      : ini;

    let dtFmt = '';
    if (date) {
      try {
        dtFmt = new Date(date).toLocaleDateString('id-ID', {
          weekday:'short', day:'2-digit', month:'short', year:'numeric',
          hour:'2-digit', minute:'2-digit'
        });
      } catch(e) { dtFmt = date.split('T')[0]; }
    }

    let videoId = '';
    if (url) { const m = url.match(/\/video\/(\d+)/); if (m) videoId = m[1]; }
    if (!videoId && item.id) { const m = String(item.id).match(/(\d{10,})/); if (m) videoId = m[1]; }

    const mediaHtml = videoId ? `
      <div>
        <div class="msdp-section-heading">
          <div class="msdp-section-heading-icon" style="background:rgba(238,29,82,.08);">
            <svg viewBox="0 0 24 24" style="stroke:#EE1D52;"><polygon points="5 3 19 12 5 21 5 3"/></svg>
          </div>
          <span class="msdp-section-title">Video Preview</span>
          <div class="msdp-section-line"></div>
        </div>
        <div class="msdp-media-section">
          <iframe src="https://www.tiktok.com/embed/v2/${videoId}" allow="autoplay" allowfullscreen></iframe>
          <div class="msdp-media-label">
            <svg viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/></svg>
            TikTok Video Embed
          </div>
        </div>
      </div>` : '';

    const descHtml = `
      <div>
        <div class="msdp-section-heading">
          <div class="msdp-section-heading-icon" style="background:rgba(100,116,139,.08);">
            <svg viewBox="0 0 24 24" style="stroke:#64748b;"><line x1="21" y1="6" x2="3" y2="6"/><line x1="15" y1="12" x2="3" y2="12"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
          </div>
          <span class="msdp-section-title">Post Description</span>
          <div class="msdp-section-line"></div>
        </div>
        <div class="msdp-desc-box">
          ${content
            ? `<div class="msdp-desc-text" id="msdpDescText">${esc(content)}</div>
               ${content.length > 220
                 ? `<button class="msdp-desc-toggle" id="msdpDescToggle" onclick="FMEDetail.toggleDesc()">
                      Show More
                      <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>`
                 : ''}`
            : `<span class="msdp-desc-empty">(Tidak ada deskripsi konten)</span>`
          }
        </div>
      </div>`;

    title.textContent = name;
    body.innerHTML = `
      <div class="msdp-inner">

        <!-- PROFILE ROW -->
        <div class="msdp-profile-row">
          <div class="msdp-avatar-xl" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div>
          <div class="msdp-profile-info">
            <div class="msdp-author-name">${esc(name)}</div>
            ${handle ? `<div class="msdp-author-handle">@${esc(handle)}</div>` : ''}
            <div class="msdp-profile-badges">
              <span class="msdp-platform-badge" style="background:${color}18;color:${color};border:1px solid ${color}30;">
                <svg viewBox="0 0 24 24" style="width:11px;height:11px;fill:${color};flex-shrink:0;">
                  <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/>
                </svg>
                TikTok · ${label}
              </span>
              ${dtFmt
                ? `<span class="msdp-date-badge">
                     <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                     ${dtFmt}
                   </span>`
                : ''}
            </div>
          </div>
        </div>

        <!-- SENTIMENT STRIP -->
        <div class="msdp-sentiment-strip msdp-sentiment-strip--${sent}">
          <div class="msdp-sentiment-icon" style="background:${sentBg};">
            <span>${sentEmoji}</span>
          </div>
          <div class="msdp-sentiment-text">
            <div class="msdp-sentiment-label msdp-sentiment-label--${sent}">Sentimen: ${sentLbl}</div>
            <div class="msdp-sentiment-desc">${sentDesc}</div>
          </div>
        </div>

        <!-- METRICS -->
        <div>
          <div class="msdp-section-heading">
            <div class="msdp-section-heading-icon" style="background:rgba(59,130,246,.08);">
              <svg viewBox="0 0 24 24" style="stroke:#3b82f6;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <span class="msdp-section-title">Engagement Metrics</span>
            <div class="msdp-section-line"></div>
          </div>
          <div class="msdp-metrics-grid">
            <div class="msdp-metric-card msdp-metric-card--views">
              <div class="msdp-metric-icon msdp-metric-icon--views">
                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </div>
              <div class="msdp-metric-value msdp-metric-value--views">${numFmt(views)}</div>
              <div class="msdp-metric-label">Views</div>
            </div>
            <div class="msdp-metric-card msdp-metric-card--likes">
              <div class="msdp-metric-icon msdp-metric-icon--likes">
                <svg viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
              </div>
              <div class="msdp-metric-value msdp-metric-value--likes">${numFmt(likes)}</div>
              <div class="msdp-metric-label">Likes</div>
            </div>
            <div class="msdp-metric-card msdp-metric-card--cmts">
              <div class="msdp-metric-icon msdp-metric-icon--cmts">
                <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              </div>
              <div class="msdp-metric-value msdp-metric-value--cmts">${numFmt(cmts)}</div>
              <div class="msdp-metric-label">Comments</div>
            </div>
            <div class="msdp-metric-card msdp-metric-card--shares">
              <div class="msdp-metric-icon msdp-metric-icon--shares">
                <svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
              </div>
              <div class="msdp-metric-value msdp-metric-value--shares">${numFmt(shares)}</div>
              <div class="msdp-metric-label">Shares</div>
            </div>
          </div>
        </div>

        <!-- DESCRIPTION -->
        ${descHtml}

        <!-- MEDIA -->
        ${mediaHtml}

        <!-- CTA -->
        ${url
          ? `<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="msdp-cta-btn">
               <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
               Buka Video di TikTok
             </a>`
          : ''}

      </div>`;

    panel.classList.add('visible');
  },

  toggleDesc() {
    const text = document.getElementById('msdpDescText');
    const btn  = document.getElementById('msdpDescToggle');
    if (!text || !btn) return;
    const expanded = text.classList.toggle('expanded');
    btn.classList.toggle('expanded', expanded);
    btn.innerHTML = (expanded ? 'Show Less' : 'Show More')
      + `<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>`;
  },

  _killIframe() {
    const body = document.getElementById('msDpBody');
    if (!body) return;
    body.querySelectorAll('iframe').forEach(f => { f.src = ''; f.remove(); });
  },

  close() {
    this._killIframe();
    document.getElementById('msDetailPanel')?.classList.remove('visible');
  }
};

/* ══ MAIN ══ */
const FME = {   
  reload() {
    FMECharts.disposeAll();
    FMETab.reset();
    Store.view=[]; Store.like=[]; Store.comment=[]; Store.share=[];
    FMEChart._items=[];
    FMEData.loadAll();
  },
  init() {
    FMEDp.init();
    FMEPopup.init();
    FMEData.loadAll();
  }
};

document.addEventListener('DOMContentLoaded', ()=>FME.init());
</script>
@endsection