@extends('mk.layouts.app')

@section('title', 'Instagram Most Engagement - SMADIMENT')

@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --primary-green:        #038047;
    --primary-green-dark:   #026738;
    --primary-green-light:  rgba(3,128,71,0.08);
    --primary-green-border: rgba(3,128,71,0.2);
    --ig:                   #e6683c;
    --ig-dark:              #c0392b;
    --ig-light:             rgba(230,104,60,.08);
    --ig-border:            rgba(230,104,60,.2);
    --green:                #10b981;
    --green-dark:           #059669;
    --green-light:          rgba(16,185,129,.08);
    --green-border:         rgba(16,185,129,.2);
    --amber:                #f59e0b;
    --amber-dark:           #d97706;
    --amber-light:          rgba(245,158,11,.08);
    --amber-border:         rgba(245,158,11,.2);
    --violet:               #8b5cf6;
    --violet-dark:          #7c3aed;
    --violet-light:         rgba(139,92,246,.08);
    --violet-border:        rgba(139,92,246,.2);
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

  /* PAGE HEADER */
  .page-header { display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px; }
  .page-header-left h1 { font-size:28px;font-weight:700;color:var(--text-primary);margin:0 0 6px;letter-spacing:-.4px; }
  .page-header-left p  { font-size:14px;color:var(--text-secondary);font-weight:500;margin:0; }
  .ms-refresh-btn { display:flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#1a202c 0%,#2d3748 100%);color:#fff;border:none;border-radius:var(--radius-sm);font-family:var(--font);font-size:13px;font-weight:600;cursor:pointer;transition:var(--transition);box-shadow:0 4px 14px rgba(0,0,0,.2); }
  .ms-refresh-btn:hover { transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,0,0,.25); }
  .ms-refresh-btn svg { width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round; }

  /* FILTER */
  .filter-card { background:var(--bg-white);border-radius:var(--radius);padding:20px 24px;margin-bottom:24px;box-shadow:var(--shadow-sm);border:1px solid var(--border-gray); }
  .filter-content { display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap; }
  .filter-group  { display:flex;flex-direction:column;gap:6px; }
  .filter-label  { font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.5px; }
  .filter-select { padding:10px 14px;border:1px solid var(--border-gray);border-radius:var(--radius-sm);font-family:var(--font);font-size:14px;font-weight:500;color:var(--text-primary);background:var(--bg-gray-50);outline:none;transition:var(--transition);min-width:200px;cursor:pointer; }
  .filter-select:focus { border-color:var(--primary-green);background:var(--bg-white);box-shadow:0 0 0 3px var(--primary-green-light); }
  .date-picker-trigger { display:flex;align-items:center;gap:10px;padding:10px 16px;background:var(--bg-gray-50);border:1px solid var(--border-gray);border-radius:var(--radius-sm);font-family:var(--font);font-size:14px;font-weight:500;color:var(--text-primary);cursor:pointer;transition:var(--transition);min-width:300px; }
  .date-picker-trigger:hover { border-color:var(--primary-green);background:var(--bg-white);box-shadow:0 0 0 3px var(--primary-green-light); }
  .date-picker-trigger svg { width:16px;height:16px;color:var(--text-secondary);flex-shrink:0; }
  .date-picker-trigger span { flex:1;text-align:left; }
  .apply-btn { display:flex;align-items:center;gap:8px;padding:10px 24px;background:linear-gradient(135deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);color:#fff;border:none;border-radius:var(--radius-sm);font-family:var(--font);font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);box-shadow:0 4px 12px rgba(3,128,71,.2);white-space:nowrap; }
  .apply-btn:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(3,128,71,.3); }
  .apply-btn svg { width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round; }

  /* DATE PICKER MODAL */
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

  /* SECTION HEADER */
  .ms-section-header { display:flex;align-items:center;gap:10px;margin-bottom:16px;margin-top:4px; }
  .ms-section-icon { width:36px;height:36px;border-radius:var(--radius-sm);background:var(--primary-green-light);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
  .ms-section-icon svg { width:18px;height:18px;stroke:var(--primary-green);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round; }
  .ms-section-title { font-size:13px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.8px; }
  .ms-section-line  { flex:1;height:1.5px;background:var(--border-gray);border-radius:1px; }

  /* STAT CARDS */
  .ms-stat-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px; }
  .ms-stat-card { background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);padding:20px 22px;box-shadow:var(--shadow-sm);transition:var(--transition);position:relative;overflow:hidden;cursor:default; }
  .ms-stat-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--stat-bar,linear-gradient(90deg,var(--primary-green),var(--primary-green-dark)));opacity:0;transition:opacity .25s; }
  .ms-stat-card:hover { box-shadow:var(--shadow-lg);border-color:var(--primary-green-border);transform:translateY(-2px); }
  .ms-stat-card:hover::before { opacity:1; }
  .ms-stat-card--ig     { --stat-bar:linear-gradient(90deg,#e6683c,#c0392b); }
  .ms-stat-card--amber  { --stat-bar:linear-gradient(90deg,#f59e0b,#d97706); }
  .ms-stat-card--violet { --stat-bar:linear-gradient(90deg,#8b5cf6,#7c3aed); }
  .ms-stat-label { font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;display:flex;align-items:center;gap:6px; }
  .ms-stat-dot   { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
  .ms-stat-value { font-size:32px;font-weight:700;color:var(--text-primary);letter-spacing:-1px;line-height:1;min-height:40px;display:flex;align-items:center; }
  .ms-stat-sub   { font-size:11px;color:var(--text-muted);font-weight:500;margin-top:7px; }

  /* TABS */
  .ms-tabs { display:flex;gap:4px;background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);padding:6px;margin-bottom:24px;box-shadow:var(--shadow-sm);flex-wrap:wrap; }
  .ms-tab-btn { flex:1;min-width:160px;display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;border-radius:var(--radius-sm);border:none;background:transparent;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition); }
  .ms-tab-btn:hover { background:var(--bg-gray-50);color:var(--text-primary); }
  .ms-tab-btn.active { background:linear-gradient(135deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);color:#fff;box-shadow:0 4px 12px rgba(3,128,71,.25); }
  .ms-tab-btn.active--ig    { background:linear-gradient(135deg,#e6683c 0%,#c0392b 100%)!important;box-shadow:0 4px 12px rgba(230,104,60,.25)!important; }
  .ms-tab-btn.active--amber { background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%)!important;box-shadow:0 4px 12px rgba(245,158,11,.25)!important; }
  .ms-tab-btn svg { width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0; }
  .ms-tab-chip { display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:20px;padding:0 6px;border-radius:10px;font-size:10px;font-weight:800;background:rgba(255,255,255,.22);color:inherit; }
  .ms-tab-btn:not(.active) .ms-tab-chip { background:var(--bg-gray-100);color:var(--text-muted); }
  .ms-tab-panel { display:none; }
  .ms-tab-panel.active { display:block; }

  /* DO CARD */
  .do-card { background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);overflow:hidden;display:flex;flex-direction:column;box-shadow:var(--shadow-sm);transition:var(--transition);position:relative; }
  .do-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);opacity:0;transition:opacity .3s; }
  .do-card--ig::before    { background:linear-gradient(90deg,#e6683c,#c0392b); }
  .do-card--amber::before { background:linear-gradient(90deg,#f59e0b,#d97706); }
  .do-card--ink::before   { background:linear-gradient(90deg,#1a202c,#334155); }
  .do-card:hover { box-shadow:var(--shadow-lg);border-color:var(--primary-green-border); }
  .do-card:hover::before { opacity:1; }
  .do-card-head { display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-gray);flex-shrink:0;gap:12px;flex-wrap:wrap; }
  .do-card-head-left { display:flex;align-items:center;gap:12px;min-width:0; }
  .do-head-icon { width:40px;height:40px;border-radius:var(--radius-sm);background:var(--primary-green-light);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
  .do-head-icon svg { width:20px;height:20px;fill:none;stroke:var(--primary-green);stroke-width:2;stroke-linecap:round;stroke-linejoin:round; }
  .do-head-icon--ig     { background:var(--ig-light)!important; }
  .do-head-icon--ig svg { stroke:#e6683c!important; }
  .do-head-icon--amber     { background:var(--amber-light)!important; }
  .do-head-icon--amber svg { stroke:#f59e0b!important; }
  .do-head-icon--ink   { background:rgba(26,32,44,.06)!important; }
  .do-head-icon--ink svg { stroke:#1a202c!important; }
  .do-card-title    { font-size:15px;font-weight:700;color:var(--text-primary);line-height:1.3; }
  .do-card-subtitle { font-size:11px;color:var(--text-muted);font-weight:500;margin-top:2px; }
  .do-badge { display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;background:var(--bg-gray-100);color:var(--text-secondary);white-space:nowrap;flex-shrink:0; }
  .do-badge--ig    { background:#fce8e1;color:#9e3318; }
  .do-badge--amber { background:#fef3c7;color:#92400e; }
  .do-card-body { padding:20px;flex:1; }
  .do-card-body--flush { padding:0; }

  /* DONUT LEGEND */
  .ms-donut-legend { display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
  .ms-donut-leg-item { display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:600;color:var(--text-secondary);padding:4px 10px;background:var(--bg-gray-100);border-radius:20px;border:1px solid var(--border-gray);cursor:pointer;transition:var(--transition); }
  .ms-donut-leg-item:hover { border-color:var(--primary-green);background:var(--primary-green-light);color:var(--primary-green); }
  .ms-donut-dot { width:9px;height:9px;border-radius:50%;flex-shrink:0; }

  /* CHART HEIGHTS */
  .ms-ch-320 { position:relative;height:320px; }
  .ms-ch-340 { position:relative;height:340px; }

  /* CONTROLS */
  .ms-toggle-group { display:flex;background:var(--bg-gray-100);border-radius:var(--radius-xs);padding:3px;gap:2px;border:1px solid var(--border-gray); }
  .ms-toggle-btn { display:flex;align-items:center;gap:5px;padding:6px 14px;border-radius:6px;border:none;background:transparent;font-family:var(--font);font-size:12px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition);white-space:nowrap; }
  .ms-toggle-btn:hover { background:var(--bg-white);color:var(--text-primary); }
  .ms-toggle-btn.active { background:var(--bg-white);color:var(--primary-green);box-shadow:0 1px 4px rgba(0,0,0,.08); }
  .ms-rows-sel { padding:6px 12px;border:1px solid var(--border-gray);border-radius:var(--radius-xs);font-family:var(--font);font-size:12px;font-weight:600;color:var(--text-secondary);background:var(--bg-gray-100);outline:none;cursor:pointer;transition:var(--transition); }
  .ms-rows-sel:focus { border-color:var(--primary-green);box-shadow:0 0 0 2px var(--primary-green-light); }
  .ms-export-btn { display:flex;align-items:center;gap:5px;padding:6px 14px;background:var(--bg-gray-100);border:1px solid var(--border-gray);border-radius:var(--radius-xs);font-family:var(--font);font-size:12px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition);white-space:nowrap; }
  .ms-export-btn:hover { background:var(--primary-green);border-color:var(--primary-green);color:#fff; }
  .ms-export-btn svg { width:12px;height:12px;stroke:currentColor;fill:none;stroke-width:2.2; }

  /* POST LIST */
  .fme-post-list { display:flex;flex-direction:column; }
  .fme-post { display:flex;align-items:flex-start;gap:14px;padding:16px 20px;border-bottom:1px solid var(--border-light);transition:background .15s;cursor:pointer; }
  .fme-post:last-child { border-bottom:none; }
  .fme-post:hover { background:#fff5f2; }
  .fme-post-rank { width:28px;height:28px;border-radius:50%;background:var(--bg-gray-100);border:1.5px solid var(--border-gray);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:var(--text-muted);flex-shrink:0;margin-top:6px; }
  .fme-post-rank--1 { background:linear-gradient(135deg,#ffd700,#f59e0b);color:#7c5900;border-color:#ffd700; }
  .fme-post-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0; }
  .fme-post-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32; }
  .fme-post-av { width:42px;height:42px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#e6683c,#c0392b);color:#fff;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;border:2px solid var(--border-gray);overflow:hidden; }
  .fme-post-av img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  .fme-post-body { flex:1;min-width:0; }
  .fme-post-author { font-size:13px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
  .fme-post-date   { font-size:10.5px;color:var(--text-muted);font-weight:400;margin-top:1px;margin-bottom:6px; }
  .fme-post-text   { font-size:12.5px;color:var(--text-secondary);line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:10px;word-break:break-word; }
  .fme-post-stats  { display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
  .fme-post-metric { display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;background:var(--bg-gray-100);border:1px solid var(--border-gray);color:var(--text-secondary);white-space:nowrap; }
  .fme-post-metric svg { width:11px;height:11px;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0; }
  .fme-post-metric--ig    { background:var(--ig-light);border-color:var(--ig-border);color:#e6683c; }
  .fme-post-metric--amber { background:var(--amber-light);border-color:var(--amber-border);color:#f59e0b; }
  .fme-post-metric--ig svg    { stroke:#e6683c; }
  .fme-post-metric--amber svg { stroke:#f59e0b; }
  .fme-sent { display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.3px; }
  .fme-sent--pos { background:#d1fae5;color:#065f46; }
  .fme-sent--neg { background:#fee2e2;color:#991b1b; }
  .fme-sent--neu { background:var(--bg-gray-100);color:var(--text-secondary); }
  .fme-view-link { display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:#e6683c;text-decoration:none;padding:3px 9px;border-radius:20px;background:var(--ig-light);border:1px solid var(--ig-border);transition:var(--transition);margin-left:auto; }
  .fme-view-link:hover { background:#e6683c;color:#fff; }
  .fme-view-link svg { width:9px;height:9px;stroke:currentColor;fill:none;stroke-width:2.5; }

  /* PAGINATION */
  .fme-pagination { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid var(--border-light);flex-wrap:wrap;gap:10px; }
  .fme-pag-info { font-size:12px;color:var(--text-muted);font-weight:500; }
  .fme-pag-controls { display:flex;align-items:center;gap:4px; }
  .fme-pag-btn { min-width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;padding:0 8px;border-radius:var(--radius-xs);border:1px solid var(--border-gray);background:var(--bg-white);font-family:var(--font);font-size:12px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition);user-select:none; }
  .fme-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary-green);color:var(--primary-green);background:var(--primary-green-light); }
  .fme-pag-btn.is-active { background:var(--primary-green);border-color:var(--primary-green);color:#fff; }
  .fme-pag-btn:disabled { opacity:.4;cursor:not-allowed; }
  .fme-pag-btn svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round; }

  /* SKELETON */
  .loading-skeleton { background:linear-gradient(90deg,var(--bg-gray-50) 25%,#e2e8f0 50%,var(--bg-gray-50) 75%);background-size:200% 100%;animation:shimmer 1.5s ease-in-out infinite;border-radius:var(--radius-xs); }
  .skel-overlay { position:absolute;inset:0;z-index:3;border-radius:inherit; }
  @keyframes shimmer { 0%{background-position:200% 0}100%{background-position:-200% 0} }
  .fme-spinner { width:34px;height:34px;border:3px solid var(--border-gray);border-top-color:var(--ig);border-radius:50%;animation:fmeSpin .7s linear infinite; }
  @keyframes fmeSpin { to{transform:rotate(360deg)} }
  .fme-spinner-state { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 20px;gap:14px;color:var(--text-secondary);font-size:13px;font-weight:500; }

  /* EMPTY */
  .do-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 20px;gap:10px; }
  .do-empty svg { width:40px;height:40px;stroke:var(--border-gray);fill:none;stroke-width:1.5; }
  .do-empty-text { font-size:13px;font-weight:600;color:var(--text-secondary); }

  .ms-mb20 { margin-bottom:20px; }

  /* POPUP */
  @keyframes msPopIn { from{opacity:0;transform:translateY(14px) scale(.94)}to{opacity:1;transform:none} }
  #msPopup { position:fixed;z-index:99999;background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);box-shadow:var(--shadow-xl);width:500px;height:620px;display:none;flex-direction:column;overflow:hidden;font-family:var(--font);animation:msPopIn .22s cubic-bezier(.34,1.3,.64,1);user-select:none; }
  #msPopup.visible { display:flex; }
  .msp-header { display:flex;align-items:center;gap:8px;padding:12px 16px;background:var(--bg-gray-50);border-bottom:1px solid var(--border-gray);cursor:grab;flex-shrink:0; }
  .msp-header:active { cursor:grabbing; }
  .msp-drag-handle { display:flex;flex-direction:column;gap:3px;margin-right:4px;flex-shrink:0;opacity:.4; }
  .msp-drag-handle span { display:block;width:18px;height:2px;background:var(--text-secondary);border-radius:1px; }
  .msp-dot   { width:10px;height:10px;border-radius:50%;flex-shrink:0; }
  .msp-title { font-size:13px;font-weight:700;color:var(--text-primary);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
  .msp-count { background:var(--ig);color:#fff;border-radius:10px;padding:1px 9px;font-size:11px;font-weight:800;flex-shrink:0; }
  .msp-close { width:28px;height:28px;border-radius:var(--radius-xs);border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:20px;line-height:1;transition:var(--transition);flex-shrink:0; }
  .msp-close:hover { background:#fee2e2;color:#991b1b; }
  .msp-actions { display:flex;align-items:center;gap:8px;padding:7px 13px;border-bottom:1px solid var(--border-gray);background:#fafbfc;flex-shrink:0; }
  .msp-meta { flex:1;font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:8px;overflow:hidden; }
  .msp-meta__label { overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
  .msp-export-btn { display:flex;align-items:center;gap:5px;padding:5px 11px;background:var(--ig);color:#fff;border:none;border-radius:var(--radius-xs);font-family:var(--font);font-size:10px;font-weight:700;cursor:pointer;transition:var(--transition);white-space:nowrap; }
  .msp-export-btn:hover { background:var(--ig-dark);transform:translateY(-1px); }
  .msp-export-btn svg { width:11px;height:11px;stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round; }
  .msp-list { overflow-y:auto;flex:1;padding:4px 0;min-height:0; }
  .msp-list::-webkit-scrollbar { width:5px; }
  .msp-list::-webkit-scrollbar-thumb { background:var(--border-gray);border-radius:4px; }
  .msp-item { display:flex;gap:10px;padding:10px 14px;border-bottom:1px solid var(--border-light);transition:background .1s;cursor:pointer;align-items:flex-start; }
  .msp-item:last-child { border-bottom:none; }
  .msp-item:hover { background:#fff5f2; }
  .msp-avatar { width:38px;height:38px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#e6683c,#c0392b);color:#fff;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--border-gray);overflow:hidden; }
  .msp-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  .msp-item-body { flex:1;min-width:0; }
  .msp-item-author { font-size:12px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
  .msp-item-text { font-size:12px;color:var(--text-secondary);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px; }
  .msp-item-footer { display:flex;align-items:center;gap:6px;font-size:10px;color:var(--text-muted);flex-wrap:wrap; }
  .msp-sent { padding:1px 7px;border-radius:10px;font-size:9px;font-weight:800;text-transform:uppercase; }
  .msp-sent--pos { background:#d1fae5;color:#065f46; }
  .msp-sent--neg { background:#fee2e2;color:#991b1b; }
  .msp-sent--neu { background:var(--bg-gray-100);color:var(--text-secondary); }

  /* DETAIL PANEL */
  @keyframes msDetailIn { from{transform:translateX(100%)}to{transform:translateX(0)} }
  #msDetailPanel { position:absolute;inset:0;background:var(--bg-white);z-index:10;display:none;flex-direction:column;animation:msDetailIn .22s cubic-bezier(.4,0,.2,1); }
  #msDetailPanel.visible { display:flex; }
  .msdp-header { display:flex;align-items:center;gap:10px;padding:12px 16px;background:var(--bg-gray-50);border-bottom:1px solid var(--border-gray);flex-shrink:0; }
  .msdp-back { width:30px;height:30px;border-radius:var(--radius-xs);border:1px solid var(--border-gray);background:var(--bg-white);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);transition:var(--transition);flex-shrink:0; }
  .msdp-back:hover { background:var(--ig-light);color:var(--ig);border-color:var(--ig-border); }
  .msdp-back svg { width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round; }
  .msdp-title { font-size:13px;font-weight:700;color:var(--text-primary);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
  .msdp-close { width:28px;height:28px;border-radius:var(--radius-xs);border:none;background:transparent;cursor:pointer;font-size:20px;color:var(--text-secondary);display:flex;align-items:center;justify-content:center;transition:var(--transition); }
  .msdp-close:hover { background:#fee2e2;color:#991b1b; }
  .msdp-body { overflow-y:auto;flex:1;padding:16px; }
  .msdp-body::-webkit-scrollbar { width:5px; }
  .msdp-body::-webkit-scrollbar-thumb { background:var(--border-gray);border-radius:4px; }
  .msdp-avatar-row { display:flex;align-items:center;gap:12px;margin-bottom:14px; }
  .msdp-avatar-lg { width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#e6683c,#c0392b);color:#fff;font-weight:700;font-size:18px;display:flex;align-items:center;justify-content:center;border:2px solid var(--border-gray);overflow:hidden;flex-shrink:0; }
  .msdp-avatar-lg img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  .msdp-author-name   { font-size:15px;font-weight:700;color:var(--text-primary); }
  .msdp-media-wrap { border-radius:10px;overflow:hidden;margin-bottom:12px;background:#000; }
  .msdp-media-img  { width:100%;max-height:400px;object-fit:contain;display:block;background:#000; }
  .msdp-content-text { font-size:13px;color:var(--text-secondary);line-height:1.7;margin-bottom:12px;background:var(--bg-gray-50);border-radius:10px;padding:12px 14px;border:1px solid var(--border-gray);word-break:break-word; }
  .msdp-meta-row { display:flex;align-items:center;justify-content:space-between;font-size:11px;color:var(--text-muted);font-weight:500;margin-bottom:12px; }
  .msdp-sent-badge { display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:12px; }
  .msdp-sent-badge--pos { background:#d1fae5;color:#065f46; }
  .msdp-sent-badge--neg { background:#fee2e2;color:#991b1b; }
  .msdp-sent-badge--neu { background:var(--bg-gray-100);color:var(--text-secondary); }
  .msdp-stats-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:12px; }
  .msdp-stat-box { background:var(--bg-gray-50);border-radius:10px;padding:10px 12px;border:1px solid var(--border-gray);text-align:center; }
  .msdp-stat-val { font-size:16px;font-weight:700;color:var(--text-primary); }
  .msdp-stat-lbl { font-size:9px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;margin-top:2px; }
  .msdp-link-btn { display:flex;align-items:center;justify-content:center;gap:8px;padding:10px 14px;background:linear-gradient(135deg,#e6683c,#c0392b);color:#fff;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;transition:var(--transition);width:100%;margin-top:4px; }
  .msdp-link-btn:hover { opacity:.9; }
  .msdp-link-btn svg { width:13px;height:13px;stroke:#fff;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round; }

  @media (max-width:768px) {
    .fme-page { padding:16px; }
    .ms-stat-grid { grid-template-columns:1fr; }
    #msPopup { width:93vw; }
    .date-picker-container { flex-direction:column;width:96%; }
    .date-picker-sidebar { width:100%;flex-direction:row;overflow-x:auto;border-right:none;border-bottom:1px solid var(--border-gray);border-radius:var(--radius) var(--radius) 0 0;flex-shrink:0; }
    .date-preset { white-space:nowrap; }
    .calendars-wrapper { flex-direction:column; }
    .ms-tab-btn { min-width:140px;font-size:12px;padding:9px 10px; }
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
        <svg viewBox="0 0 24 24" style="width:28px;height:28px;display:inline-block;vertical-align:middle;margin-right:10px;margin-top:-3px;">
          <defs>
            <linearGradient id="igGrad" x1="0%" y1="100%" x2="100%" y2="0%">
              <stop offset="0%" style="stop-color:#f09433"/>
              <stop offset="25%" style="stop-color:#e6683c"/>
              <stop offset="50%" style="stop-color:#dc2743"/>
              <stop offset="75%" style="stop-color:#cc2366"/>
              <stop offset="100%" style="stop-color:#bc1888"/>
            </linearGradient>
          </defs>
          <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="url(#igGrad)"/>
          <circle cx="12" cy="12" r="4.5" fill="none" stroke="#fff" stroke-width="1.8"/>
          <circle cx="17.5" cy="6.5" r="1.2" fill="#fff"/>
        </svg>
        Instagram Most Engagement
      </h1>
      <p>Postingan dengan Most Liked dan Most Comments</p>
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
        <button class="date-preset active" data-p="thismonth">This Month</button>
        <button class="date-preset" data-p="lastmonth">Last Month</button>
        <button class="date-preset" data-p="custom">Custom Range</button>
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

  {{-- SUMMARY STATS (no image/video breakdown) --}}
  <div class="ms-section-header">
    <div class="ms-section-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
    <span class="ms-section-title">Ringkasan Engagement</span>
    <div class="ms-section-line"></div>
  </div>
  <div class="ms-stat-grid">
    <div class="ms-stat-card ms-stat-card--ig">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#e6683c;"></span>Total Likes</div>
      <div class="ms-stat-value" id="valTotalLikes"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Akumulasi likes semua postingan</div>
    </div>
    <div class="ms-stat-card ms-stat-card--amber">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#f59e0b;"></span>Total Comments</div>
      <div class="ms-stat-value" id="valTotalCmts"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Akumulasi komentar semua postingan</div>
    </div>
    <div class="ms-stat-card ms-stat-card--violet">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#8b5cf6;"></span>Total Posts</div>
      <div class="ms-stat-value" id="valTotalPosts"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Total postingan pada periode ini</div>
    </div>
  </div>

  {{-- DONUT SECTION — 2 donuts only (like, comment) --}}
  <div class="ms-section-header">
    <div class="ms-section-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg></div>
    <span class="ms-section-title" id="donutSectionLabel">Distribusi Likes — Top 5</span>
    <div class="ms-section-line"></div>
  </div>
  <div class="do-card do-card--ig ms-mb20" id="donutMasterCard">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <div class="do-head-icon do-head-icon--ig" id="donutMasterIco">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
        </div>
        <div>
          <div class="do-card-title" id="donutMasterTitle">Top 5 Most Liked — Distribution</div>
          <div class="do-card-subtitle">Proporsi engagement per postingan — hover segmen untuk detail</div>
        </div>
      </div>
      <div id="donutMasterLegend" class="ms-donut-legend"></div>
    </div>
    <div class="do-card-body">
      @foreach(['like','comment'] as $dkey)
      <div id="donutWrap-{{ $dkey }}" style="{{ $dkey === 'like' ? '' : 'display:none;' }}">
        <div class="loading-skeleton" id="donutSkel-{{ $dkey }}" style="height:460px;border-radius:10px;"></div>
        <div id="donutChart-{{ $dkey }}" style="width:100%;height:460px;display:none;"></div>
        <div id="donutEmpty-{{ $dkey }}" style="display:none;"><div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="do-empty-text">Tidak ada data</span></div></div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- TABS — 2 tabs only --}}
  <div class="ms-tabs">
    <button class="ms-tab-btn active active--ig" id="tab-like" onclick="FMETab.show('like')">
      <svg viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/></svg>
      Most Liked
      <span class="ms-tab-chip" id="chip-like">—</span>
    </button>
    <button class="ms-tab-btn" id="tab-comment" onclick="FMETab.show('comment')">
      <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Most Comments
      <span class="ms-tab-chip" id="chip-comment">—</span>
    </button>
  </div>

  {{-- 2 PANELS --}}
  @php
    $tabDefs = [
      'like'    => ['color'=>'ig',    'metric'=>'like',    'colorHex'=>'#e6683c', 'label'=>'Most Liked'],
      'comment' => ['color'=>'amber', 'metric'=>'comment', 'colorHex'=>'#f59e0b', 'label'=>'Most Comments'],
    ];
    $metricIcons = [
      'like'    => '<path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>',
      'comment' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
    ];
  @endphp

  @foreach($tabDefs as $tabKey => $tabCfg)
  <div class="ms-tab-panel {{ $tabKey === 'like' ? 'active' : '' }}" id="panel-{{ $tabKey }}">
    <div class="do-card do-card--{{ $tabCfg['color'] }} ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--{{ $tabCfg['color'] }}">
            <svg viewBox="0 0 24 24">{!! $metricIcons[$tabCfg['metric']] !!}</svg>
          </div>
          <div>
            <div class="do-card-title">Top Posts by {{ $tabCfg['metric'] === 'like' ? 'Likes' : 'Comments' }}</div>
            <div class="do-card-subtitle">Postingan dengan paling banyak {{ $tabCfg['metric'] === 'like' ? 'likes' : 'komentar' }} — klik untuk detail</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0;">
          <select class="ms-rows-sel" id="rows-{{ $tabKey }}" onchange="FMEData.reloadTab('{{ $tabKey }}')">
            <option value="10">Top 10</option><option value="20" selected>Top 20</option><option value="50">Top 50</option>
          </select>
          <button class="ms-export-btn" onclick="FMEData.exportCsv('{{ $tabKey }}')">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>CSV
          </button>
          <span class="do-badge do-badge--{{ $tabCfg['color'] }}" id="badge-{{ $tabKey }}-full">Loading…</span>
        </div>
      </div>
      <div id="list-{{ $tabKey }}" class="do-card-body--flush">
        <div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data…</div>
      </div>
      <div id="pag-{{ $tabKey }}"></div>
    </div>

    {{-- Bar chart --}}
    <div class="do-card do-card--{{ $tabCfg['color'] }} ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--{{ $tabCfg['color'] }}">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </div>
          <div>
            <div class="do-card-title">{{ $tabCfg['metric'] === 'like' ? 'Likes' : 'Comments' }} Chart — Top 10</div>
            <div class="do-card-subtitle">Top 10 posts berdasarkan {{ $tabCfg['metric'] === 'like' ? 'likes' : 'komentar' }}</div>
          </div>
        </div>
        <span class="do-badge do-badge--{{ $tabCfg['color'] }}">Top 10</span>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-320">
          <div id="ch-{{ $tabKey }}" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="sk-{{ $tabKey }}"></div>
        </div>
      </div>
    </div>

    {{-- Engagement comparison only on like tab --}}
    @if($tabKey === 'like')
    <div class="ms-section-header">
      <div class="ms-section-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
      <span class="ms-section-title">Perbandingan Likes vs Comments — Top 10</span>
      <div class="ms-section-line"></div>
    </div>
    <div class="do-card do-card--ink ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--ink">
            <svg viewBox="0 0 24 24"><rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/></svg>
          </div>
          <div><div class="do-card-title">Like vs Comment — Top 10 Posts</div><div class="do-card-subtitle">Perbandingan engagement postingan terpopuler</div></div>
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
    @endif
  </div>
  @endforeach

</div>

{{-- POPUP --}}
<div id="msPopup">
  <div class="msp-header" id="msPopHeader">
    <div class="msp-drag-handle"><span></span><span></span><span></span></div>
    <div class="msp-dot" id="msPopDot"></div>
    <span class="msp-title" id="msPopTitle">Post Detail</span>
    <span class="msp-count" id="msPopCount">…</span>
    <button class="msp-close" onclick="FMEPopup.close()">×</button>
  </div>
  <div class="msp-actions">
    <div class="msp-meta">
      <svg viewBox="0 0 24 24" style="width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <span class="msp-meta__label" id="msPopMeta">—</span>
    </div>
    <button class="msp-export-btn" onclick="FMEPopup.exportCsv()">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>Export CSV
    </button>
  </div>
  <div class="msp-list" id="msPopList"></div>
  <div id="msDetailPanel">
    <div class="msdp-header">
      <button class="msdp-back" onclick="FMEDetail.close()">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="msdp-title" id="msDpTitle">Detail Post</span>
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

const FMECfg = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
  perPage: 10
};

// 2 tabs only — no video separation
const TAB_DEFS = {
  'like':    { sub:'postbylike',    metric:'like',    color:'ig',    colorHex:'#e6683c', label:'Most Liked' },
  'comment': { sub:'postbycomment', metric:'comment', color:'amber', colorHex:'#f59e0b', label:'Most Comments' },
};

const DONUT_TAB_META = {
  'like':    { sectionLabel:'Distribusi Likes — Top 5',    masterTitle:'Top 5 Most Liked — Distribution' },
  'comment': { sectionLabel:'Distribusi Comments — Top 5', masterTitle:'Top 5 Most Comments — Distribution' },
};

const DONUT_COLORS = ['#e6683c','#f59e0b','#10b981','#8b5cf6','#f43f5e'];

/* ── DATE PICKER ── */
const FMEDp = (() => {
  let ds=null,de=null,m1=new Date(),m2=new Date(),pickStart=true;
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
  function init(){
    const si=document.getElementById('hSD'),ei=document.getElementById('hED');
    ds=si?.value?new Date(si.value):(()=>{const d=new Date();d.setDate(1);return d;})();
    de=ei?.value?new Date(ei.value):new Date();
    m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('fmeDpTrigger').addEventListener('click',open);
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click',onPreset));
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&document.getElementById('fmeDpModal').classList.contains('show'))close();});
  }
  function open(){document.getElementById('fmeDpModal').classList.add('show');render();}
  function close(){document.getElementById('fmeDpModal').classList.remove('show');}
  function apply(){document.getElementById('hSD').value=fmt(ds);document.getElementById('hED').value=fmt(de);document.getElementById('fmeDpDisplay').textContent=fmt(ds)+' – '+fmt(de);close();}
  function nav(dir){m1.setMonth(m1.getMonth()+dir);m2.setMonth(m2.getMonth()+dir);render();}
  function onPreset(e){
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
    e.target.classList.add('active');
    const today=new Date();today.setHours(0,0,0,0);
    switch(e.target.dataset.p){
      case 'today':    ds=new Date(today);de=new Date(today);break;
      case 'yesterday':ds=new Date(today);ds.setDate(today.getDate()-1);de=new Date(ds);break;
      case 'last7':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-6);break;
      case 'last30':   de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-29);break;
      case 'thismonth':ds=new Date(today.getFullYear(),today.getMonth(),1);de=new Date(today);break;
      case 'lastmonth':ds=new Date(today.getFullYear(),today.getMonth()-1,1);de=new Date(today.getFullYear(),today.getMonth(),0);break;
    }
    if(e.target.dataset.p!=='custom'){m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);}
    updDisp();render();
  }
  function render(){renderCal('fmeDpCal1',m1);renderCal('fmeDpCal2',m2);updDisp();}
  function renderCal(id,month){
    const el=document.getElementById(id);if(!el)return;
    const y=month.getFullYear(),mn=month.getMonth();
    const first=new Date(y,mn,1),last=new Date(y,mn+1,0),prevL=new Date(y,mn,0);
    const today=new Date();today.setHours(0,0,0,0);
    let h=`<div class="calendar-month">${MN[mn]} ${y}</div><div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
    for(let i=0;i<first.getDay();i++)h+=`<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const date=new Date(y,mn,d);date.setHours(0,0,0,0);
      let cls='calendar-day';
      if(sameD(date,today))cls+=' today';
      if(date>today)cls+=' disabled';
      if(ds&&de){if(sameD(date,ds)||sameD(date,de))cls+=' selected';else if(date>ds&&date<de)cls+=' in-range';}
      h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++)h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>';el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn=>{
      btn.addEventListener('click',function(){
        const d=new Date(this.dataset.date);d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-p="custom"]').classList.add('active');
        if(pickStart||d<ds){ds=d;de=d;pickStart=false;}else{if(d>=ds)de=d;else{de=ds;ds=d;}pickStart=true;}
        updDisp();render();
      });
    });
  }
  function updDisp(){const el=document.getElementById('fmeDpRangeText');if(el&&ds&&de)el.textContent=fmt(ds)+' – '+fmt(de);}
  function fmt(d){if(!d)return '';return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
  function sameD(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
  return{init,open,close,apply,nav};
})();

/* ── UTILS ── */
const numFmt = n => parseInt(n||0).toLocaleString('id-ID');
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc    = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const emptyH = msg => `<div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="do-empty-text">${msg}</span></div>`;

/* ── CHARTS ── */
const FMECharts = {
  _i:{},
  make(id){ if(this._i[id]){try{this._i[id].dispose();}catch(e){}} const dom=document.getElementById(id);if(!dom)return null;const c=echarts.init(dom,null,{renderer:'canvas'});this._i[id]=c;return c; },
  disposeAll(){ Object.values(this._i).forEach(c=>{try{c.dispose();}catch(e){}});this._i={}; }
};
window.addEventListener('resize',()=>{Object.values(FMECharts._i).forEach(c=>{try{if(!c.isDisposed())c.resize();}catch(e){}});});
const EC_TT = {backgroundColor:'#1a202c',borderColor:'#334155',borderWidth:1,padding:[10,14],textStyle:{color:'#fff',fontFamily:"'Poppins',sans-serif",fontSize:13},extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);'};

/* ── STORE ── */
const RawStore = { postbylike:[], postbycomment:[] };
const Store    = {};
const Pag      = {};
Object.keys(TAB_DEFS).forEach(k=>{ Store[k]=[]; Pag[k]=1; });

/* ── TABS ── */
const FMETab = {
  _loaded: {},
  show(tabKey){
    const def = TAB_DEFS[tabKey];
    if(!def) return;
    Object.keys(TAB_DEFS).forEach(k=>{
      const btn=document.getElementById('tab-'+k);
      const panel=document.getElementById('panel-'+k);
      const dw=document.getElementById('donutWrap-'+k);
      const isThis=k===tabKey;
      if(btn){
        btn.classList.toggle('active',isThis);
        btn.classList.remove('active--ig','active--amber');
        if(isThis) btn.classList.add('active--'+TAB_DEFS[k].color);
      }
      if(panel) panel.classList.toggle('active',isThis);
      if(dw) dw.style.display=isThis?'block':'none';
    });
    const meta=DONUT_TAB_META[tabKey]||{};
    const sl=document.getElementById('donutSectionLabel');if(sl)sl.textContent=meta.sectionLabel||'';
    const mt=document.getElementById('donutMasterTitle');if(mt)mt.textContent=meta.masterTitle||'';
    const card=document.getElementById('donutMasterCard');
    if(card)card.className='do-card do-card--'+def.color+' ms-mb20';
    const ico=document.getElementById('donutMasterIco');
    if(ico)ico.className='do-head-icon do-head-icon--'+def.color;
    const srcLeg=document.getElementById('legendSrc-'+tabKey);
    const masterLeg=document.getElementById('donutMasterLegend');
    if(masterLeg)masterLeg.innerHTML=srcLeg?srcLeg.innerHTML:'';
    if(!this._loaded[tabKey]){
      this._loaded[tabKey]=true;
      FMEData.loadTab(tabKey);
    } else {
      requestAnimationFrame(()=>{ Object.values(FMECharts._i).forEach(c=>{try{if(c&&!c.isDisposed())c.resize();}catch(e){}});});
    }
  },
  reset(){ Object.keys(TAB_DEFS).forEach(k=>this._loaded[k]=false); }
};

/* ── DATA ── */
const FMEData = {
  async loadAll(){
    if(!FMECfg.pid){
      ['valTotalLikes','valTotalCmts','valTotalPosts'].forEach(id=>{
        const e=document.getElementById(id);if(e)e.innerHTML='<span style="font-size:14px;color:#94a3b8;">—</span>';
      });
      Object.keys(TAB_DEFS).forEach(k=>{
        const e=document.getElementById('list-'+k);if(e)e.innerHTML=emptyH('Pilih project terlebih dahulu');
        hideSk('sk-'+k); hideSk('donutSkel-'+k);
      });
      hideSk('sk-eng');
      return;
    }
    FMETab._loaded['like']=true;
    await FMEData.loadTab('like');
  },

  async _fetchRaw(sub){
    if(RawStore[sub].length) return RawStore[sub];
    const res  = await fetch(`/mk/api/instagram/most-viewed-posts?project_id=${FMECfg.pid}&start_date=${FMECfg.sd}&end_date=${FMECfg.ed}&sub=${sub}&rows=1000`);
    const json = await res.json();
    let items  = json.data||json||[];
    if(!Array.isArray(items)) items=[];
    RawStore[sub] = items;
    return items;
  },

  async loadTab(tabKey){
    const def     = TAB_DEFS[tabKey];
    const listEl  = document.getElementById('list-'+tabKey);
    const chipEl  = document.getElementById('chip-'+tabKey);
    const badgeEl = document.getElementById('badge-'+tabKey+'-full');
    if(listEl) listEl.innerHTML=`<div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data…</div>`;
    try{
      const raw = await this._fetchRaw(def.sub);
      const sorted = this._sort(raw, def.metric);
      Store[tabKey] = sorted;
      Pag[tabKey]   = 1;
      if(chipEl)  chipEl.textContent  = sorted.length;
      if(badgeEl) badgeEl.textContent = `${sorted.length} posts`;
      if(tabKey==='like') this._updateStats(raw);
      this._renderList(tabKey);
      this._renderBar(tabKey, sorted.slice(0,10));
      this._renderDonut(tabKey, sorted);
      if(tabKey==='like') this._renderEngChart(sorted);
    } catch(err){
      console.error(err);
      if(listEl) listEl.innerHTML=emptyH('Gagal memuat data: '+err.message);
      if(chipEl)  chipEl.textContent='!';
      if(badgeEl) badgeEl.textContent='Error';
      hideSk('sk-'+tabKey); hideSk('donutSkel-'+tabKey);
    }
  },

  reloadTab(tabKey){
    const def=TAB_DEFS[tabKey];
    RawStore[def.sub]=[];
    Store[tabKey]=[];Pag[tabKey]=1;
    this.loadTab(tabKey);
  },

  _sort(items, metric){
    const keys = metric==='like' ? ['num_likes','likes','like_count'] : ['num_comments','comment_count','comments'];
    return [...items].sort((a,b)=>{
      const va=keys.reduce((v,k)=>v||parseInt(a[k]||0),0);
      const vb=keys.reduce((v,k)=>v||parseInt(b[k]||0),0);
      return vb-va;
    });
  },

  _metric(item, metric){
    const keys = metric==='like' ? ['num_likes','likes','like_count','freq'] : ['num_comments','comment_count','comments'];
    return keys.reduce((v,k)=>v||parseInt(item[k]||0),0);
  },

  _updateStats(items){
    const sumLike = arr => arr.reduce((s,it)=>s+parseInt(it.num_likes||it.likes||0),0);
    const sumCmt  = arr => arr.reduce((s,it)=>s+parseInt(it.num_comments||it.comments||0),0);
    const eL=document.getElementById('valTotalLikes');if(eL)eL.textContent=numFmt(sumLike(items));
    const eC=document.getElementById('valTotalCmts'); if(eC)eC.textContent=numFmt(sumCmt(items));
    const eP=document.getElementById('valTotalPosts');if(eP)eP.textContent=numFmt(items.length);
  },

  _renderList(tabKey){
    const items=Store[tabKey], listEl=document.getElementById('list-'+tabKey), pagEl=document.getElementById('pag-'+tabKey);
    if(!listEl) return;
    if(!items.length){ listEl.innerHTML=emptyH('Tidak ada data untuk periode ini');if(pagEl)pagEl.innerHTML='';return; }
    const page=Pag[tabKey]||1, total=items.length, perPage=FMECfg.perPage, pages=Math.ceil(total/perPage), start=(page-1)*perPage;
    const def=TAB_DEFS[tabKey];
    listEl.innerHTML=`<div class="fme-post-list">${items.slice(start,start+perPage).map((item,i)=>this._postHTML(item,start+i,def)).join('')}</div>`;
    if(pagEl) pagEl.innerHTML=this._pagHTML(tabKey,page,pages,total,start+1,Math.min(start+perPage,total));
    listEl.querySelectorAll('.fme-post').forEach(el=>{
      el.addEventListener('click',()=>{
        try{
          const item=JSON.parse(decodeURIComponent(el.dataset.item));
          FMEPopup.open(items, tabKey, def.label, items.length);
          FMEDetail.open(item, def);
        }catch(e){ console.warn(e); }
      });
    });
  },

  _pagHTML(tabKey,page,pages,total,from,to){
    if(pages<=1) return '';
    let btns='';const r=2;
    btns+=`<button class="fme-pag-btn" ${page<=1?'disabled':''} onclick="FMEData.goPage('${tabKey}',${page-1})"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>`;
    for(let i=1;i<=pages;i++){
      if(i===1||i===pages||(i>=page-r&&i<=page+r))btns+=`<button class="fme-pag-btn${i===page?' is-active':''}" onclick="FMEData.goPage('${tabKey}',${i})">${i}</button>`;
      else if(i===page-r-1||i===page+r+1)btns+=`<span class="fme-pag-btn" style="cursor:default;opacity:.5;">…</span>`;
    }
    btns+=`<button class="fme-pag-btn" ${page>=pages?'disabled':''} onclick="FMEData.goPage('${tabKey}',${page+1})"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>`;
    return `<div class="fme-pagination"><span class="fme-pag-info">Menampilkan ${from}–${to} dari ${total} posts</span><div class="fme-pag-controls">${btns}</div></div>`;
  },

  goPage(tabKey,page){
    Pag[tabKey]=page; this._renderList(tabKey);
    const el=document.getElementById('list-'+tabKey);if(el)el.scrollIntoView({behavior:'smooth',block:'nearest'});
  },

  _getName(item){
    const explicit=item.author_name||item.name||item.author_scr_name||item.username||item.screen_name||'';
    if(explicit)return explicit.replace(/<[^>]*>/g,'').trim();
    const raw=(item.content||item.caption||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim();
    if(!raw)return 'Instagram Post';
    if(raw.length<=100)return raw;
    return raw.slice(0,100).replace(/\s\S*$/,'').trim()||raw.slice(0,100);
  },
  _getAvatar(item){
    const direct=(item.avatar_url||item.profile_url||item.author_image||'').trim();
    if(direct&&direct.startsWith('http'))return direct;
    const url=item.url||item.link||'';
    if(url){ try{ const domain=new URL(url).hostname.replace('www.',''); return `https://www.google.com/s2/favicons?domain=${domain}&sz=64`; }catch(e){} }
    return '';
  },
  _getInitials(name){
    if(!name||name==='Instagram Post')return 'IG';
    const words=name.replace(/[^a-zA-Z0-9\s]/g,'').split(/\s+/).filter(Boolean);
    if(words.length>=2)return(words[0][0]+words[1][0]).toUpperCase();
    return(words[0]?.[0]||'I').toUpperCase();
  },
  _getAvatarColor(item){
    const seed=item.author_id||item.id||this._getName(item)||'ig';
    const colors=['#e6683c','#10b981','#f59e0b','#8b5cf6','#ef4444','#06b6d4','#ec4899','#14b8a6','#f97316','#6366f1'];
    let hash=0;for(let i=0;i<seed.length;i++)hash=(hash*31+seed.charCodeAt(i))&0xffffffff;
    return colors[Math.abs(hash)%colors.length];
  },
  _avatarHtml(item){
    const name=this._getName(item);
    const av=this._getAvatar(item);
    const ini=this._getInitials(name);
    const safeIni=ini.replace(/['"\\]/g,'');
    if(av&&av.startsWith('http')) return `<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`;
    return ini;
  },

  _postHTML(item, globalIdx, def){
    const rank=globalIdx+1, rkCls=rank===1?'--1':rank===2?'--2':rank===3?'--3':'';
    const name=this._getName(item);
    const avColor=this._getAvatarColor(item);
    const avHtml=this._avatarHtml(item);
    const hasAuthor=!!(item.author_name||item.name||item.author_scr_name||item.username||item.screen_name||'').trim();
    const rawContent=(item.content||item.caption||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim();
    const content=rawContent.slice(0,300);
    const displayName=hasAuthor?name:(rawContent.length<=100?rawContent:(rawContent.slice(0,100).replace(/\s\S*$/,'').trim()+'…'));
    const dt=(item.date_created||'').split('T')[0];
    const url=item.url||item.link||'';
    const likes=parseInt(item.num_likes||item.likes||0);
    const cmts=parseInt(item.num_comments||item.comments||0);
    const sentRaw=String(item.sentiment_str||item.class_sentiment||item.sentiment||'').toLowerCase();
    const sent=sentRaw.includes('pos')?'pos':sentRaw.includes('neg')?'neg':'neu';
    const sentLbl=sent==='pos'?'Positive':sent==='neg'?'Negative':'Neutral';
    const lActive=def.metric==='like';
    const cActive=def.metric==='comment';
    const itemEnc=encodeURIComponent(JSON.stringify(item));

    return `<div class="fme-post" data-item="${esc(itemEnc)}" data-name="${esc(name)}">
      <div class="fme-post-rank fme-post-rank${rkCls}">${rank}</div>
      <div class="fme-post-av" style="background:linear-gradient(135deg,${avColor},${avColor}bb);">${avHtml}</div>
      <div class="fme-post-body">
        <div class="fme-post-author">${esc(displayName)}</div>
        ${dt?`<div class="fme-post-date">${dt}</div>`:''}
        ${hasAuthor&&content?`<div class="fme-post-text">${esc(content)}</div>`:!hasAuthor&&rawContent.length>100?`<div class="fme-post-text">${esc(content)}</div>`:''}
        <div class="fme-post-stats">
          <span class="fme-post-metric${lActive?' fme-post-metric--ig':''}">
            <svg viewBox="0 0 24 24" stroke="${lActive?'#e6683c':'currentColor'}"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/></svg>
            ${numFmt(likes)}
          </span>
          <span class="fme-post-metric${cActive?' fme-post-metric--amber':''}">
            <svg viewBox="0 0 24 24" stroke="${cActive?'#f59e0b':'currentColor'}"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            ${numFmt(cmts)}
          </span>
          <span class="fme-sent fme-sent--${sent}">${sentLbl}</span>
          ${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="fme-view-link" onclick="event.stopPropagation()"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Lihat</a>`:''}
        </div>
      </div>
    </div>`;
  },

  _renderBar(tabKey, items){
    hideSk('sk-'+tabKey);
    if(!items.length) return;
    const def=TAB_DEFS[tabKey];
    const color=def.colorHex;
    const labels=items.map(it=>{ const n=this._getName(it); return n.length>16?n.slice(0,15)+'…':n; });
    const values=items.map(it=>this._metric(it, def.metric));
    const chart=FMECharts.make('ch-'+tabKey);if(!chart)return;
    chart.setOption({
      animation:true,animationDuration:800,animationEasing:'elasticOut',backgroundColor:'#ffffff',
      tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'shadow',shadowStyle:{color:'rgba(230,104,60,.05)'}},
        formatter:p=>{
          const it=items[p[0]?.dataIndex];
          const name=it?this._getName(it):(p[0]?.name||'');
          const sentRaw=String(it?.sentiment_str||it?.sentiment||'').toLowerCase();
          const sent=sentRaw.includes('pos')?'Positive':sentRaw.includes('neg')?'Negative':'Neutral';
          const sc=sent==='Positive'?'#10b981':sent==='Negative'?'#ef4444':'#94a3b8';
          const sb=sent==='Positive'?'rgba(16,185,129,.2)':sent==='Negative'?'rgba(239,68,68,.2)':'rgba(148,163,184,.15)';
          const metricLbl=def.metric==='like'?'Likes':'Comments';
          return `<div style="min-width:180px;"><div style="font-weight:700;font-size:13px;margin-bottom:6px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.12);">${esc(name)}</div><div style="display:flex;align-items:center;justify-content:space-between;gap:10px;"><span style="font-size:12px;color:#94a3b8;">${metricLbl}</span><span style="font-size:13px;font-weight:700;">${numFmt(p[0].value)}</span></div><div style="margin-top:6px;"><span style="background:${sb};color:${sc};border-radius:6px;padding:2px 9px;font-size:11px;font-weight:700;">${sent}</span></div></div>`;
        }
      },
      grid:{top:14,right:18,bottom:52,left:16,containLabel:true},
      xAxis:{type:'category',data:labels,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'600',color:'#64748b',interval:0,rotate:labels.length>6?30:0}},
      yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},
      series:[{type:'bar',data:values.map(v=>({value:v,itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:color},{offset:1,color:color+'44'}]},borderRadius:[7,7,0,0]},emphasis:{itemStyle:{color,shadowBlur:12,shadowColor:color+'55'}}})),barMaxWidth:44,label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:10,color:'#64748b',formatter:p=>numK(p.value)}}]
    });
    chart.on('click',params=>{
      const item=items[params.dataIndex];if(!item)return;
      const def=TAB_DEFS[tabKey];
      FMEPopup.open(Store[tabKey],tabKey,def.label,Store[tabKey].length);
      FMEDetail.open(item,def);
    });
  },

  _renderDonut(tabKey, items){
    const skel=document.getElementById('donutSkel-'+tabKey);
    const chartEl=document.getElementById('donutChart-'+tabKey);
    const emptyEl=document.getElementById('donutEmpty-'+tabKey);
    const def=TAB_DEFS[tabKey];
    const metricLbl=def.metric==='like'?'Likes':'Comments';
    if(!items.length){ if(skel)skel.style.display='none';if(emptyEl)emptyEl.style.display='block';return; }
    const top5=items.slice(0,5), total=top5.reduce((s,it)=>s+this._metric(it,def.metric),0);
    const legendHTML=top5.map((it,i)=>{
      const n=this._getName(it); const sn=n.length>16?n.slice(0,15)+'…':n; const v=this._metric(it,def.metric);
      return `<div class="ms-donut-leg-item" onclick="FMEDonut.highlight('${tabKey}',${i})"><span class="ms-donut-dot" style="background:${DONUT_COLORS[i]};"></span>${sn} · ${numFmt(v)}</div>`;
    }).join('');
    let srcLeg=document.getElementById('legendSrc-'+tabKey);
    if(!srcLeg){ srcLeg=document.createElement('div');srcLeg.id='legendSrc-'+tabKey;srcLeg.style.display='none';document.body.appendChild(srcLeg); }
    srcLeg.innerHTML=legendHTML;
    const activeTab=Object.keys(TAB_DEFS).find(k=>document.getElementById('tab-'+k)?.classList.contains('active'))||'like';
    if(activeTab===tabKey){ const ml=document.getElementById('donutMasterLegend');if(ml)ml.innerHTML=legendHTML; }
    if(skel)skel.style.display='none'; if(chartEl)chartEl.style.display='block';
    const chart=FMECharts.make('donutChart-'+tabKey); if(!chart)return;
    const pieData=top5.map((it,i)=>{
      const name=this._getName(it); const val=this._metric(it,def.metric);
      const content=(it.content||it.caption||'').replace(/<[^>]*>/g,'').trim();
      const sentRaw=String(it.sentiment_str||it.class_sentiment||it.sentiment||'').toLowerCase();
      const sentiment=sentRaw.includes('pos')?'Positive':sentRaw.includes('neg')?'Negative':'Neutral';
      return{name,value:val,content,sentiment,url:it.url||it.link||'',color:DONUT_COLORS[i],itemStyle:{color:DONUT_COLORS[i],borderColor:'#fff',borderWidth:3}};
    });
    const sC=s=>s==='Positive'?'#10b981':s==='Negative'?'#ef4444':'#94a3b8';
    const sB=s=>s==='Positive'?'rgba(16,185,129,.2)':s==='Negative'?'rgba(239,68,68,.2)':'rgba(148,163,184,.15)';
    chart.setOption({
      animation:true,animationDuration:1000,animationEasing:'cubicOut',backgroundColor:'#ffffff',
      tooltip:{trigger:'item',backgroundColor:'#1a202c',borderColor:'#374151',borderWidth:1,padding:[14,16],extraCssText:'border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.4);max-width:280px;pointer-events:none;z-index:9999;',textStyle:{color:'#f9fafb',fontFamily:"'Poppins',sans-serif",fontSize:12},
        position:function(point,params,dom,rect,size){const[mx,my]=point,[vw,vh]=size.viewSize,[tw,th]=size.contentSize,off=14;let x=mx+off,y=my+off;if(x+tw>vw-8)x=mx-tw-off;if(x<8)x=8;if(y+th>vh-8)y=my-th-off;if(y<8)y=8;return[x,y];},
        formatter:function(p){const d=p.data,pct=total>0?((d.value/total)*100).toFixed(1):'0';const snip=d.content?(d.content.length>100?d.content.slice(0,100)+'…':d.content):'';const sc=sC(d.sentiment),sb=sB(d.sentiment);return `<div style="min-width:220px;"><div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid rgba(255,255,255,.12);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${d.color};flex-shrink:0;"></span><span style="font-weight:700;font-size:13px;color:#fff;line-height:1.3;">${esc(d.name)}</span></div>${snip?`<div style="font-size:11px;color:#94a3b8;line-height:1.6;margin-bottom:10px;font-style:italic;">"${esc(snip)}"</div>`:''}<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;"><span style="background:rgba(255,255,255,.12);border-radius:6px;padding:3px 10px;font-size:11px;font-weight:700;color:#fff;">${numFmt(d.value)} ${metricLbl}</span><span style="background:rgba(255,255,255,.12);border-radius:6px;padding:3px 10px;font-size:11px;font-weight:700;color:#fff;">${pct}%</span><span style="background:${sb};color:${sc};border-radius:6px;padding:3px 10px;font-size:11px;font-weight:700;">${d.sentiment}</span></div></div>`;}
      },
      legend:{show:false},
      series:[{type:'pie',radius:['36%','54%'],center:['50%','50%'],avoidLabelOverlap:true,minAngle:8,padAngle:1.5,itemStyle:{borderColor:'#ffffff',borderWidth:3},
        label:{show:true,position:'outside',alignTo:'edge',edgeDistance:14,lineHeight:17,fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#475569',fontWeight:'500',
          formatter:function(p){const d=p.data,pct=total>0?((d.value/total)*100).toFixed(1):'0';const nm=d.name.length>30?d.name.slice(0,29)+'…':d.name;const snip=d.content?(d.content.length>40?d.content.slice(0,39)+'…':d.content):'';const line2=snip?`{snip|${snip}}\n`:'';return `{name|${nm}}\n${line2}{pct|${numFmt(d.value)}  ·  ${pct}%}`;},
          rich:{name:{fontSize:11,fontWeight:'700',color:'#1e293b',lineHeight:17},snip:{fontSize:10,fontWeight:'400',color:'#64748b',lineHeight:15},pct:{fontSize:10,fontWeight:'600',color:'#94a3b8',lineHeight:15}}
        },
        labelLine:{show:true,length:12,length2:20,smooth:.3,lineStyle:{width:1.2,color:'#cbd5e1'}},
        emphasis:{scale:true,scaleSize:6,itemStyle:{shadowBlur:14,shadowColor:'rgba(0,0,0,.18)'},label:{fontWeight:'700',fontSize:12}},
        data:pieData
      }],
      graphic:[
        {type:'text',left:'center',top:'44%',z:100,style:{text:numFmt(total),fill:'#0f172a',font:"800 28px 'Poppins',sans-serif",textAlign:'center'}},
        {type:'text',left:'center',top:'53%',z:100,style:{text:'TOTAL '+metricLbl.toUpperCase(),fill:'#94a3b8',font:"600 9px 'Poppins',sans-serif",textAlign:'center'}}
      ]
    });
    chart.on('click',params=>{
      const item=items[params.dataIndex];if(!item)return;
      const def=TAB_DEFS[tabKey];
      FMEPopup.open(Store[tabKey],tabKey,def.label,Store[tabKey].length);
      FMEDetail.open(item,def);
    });
  },

  _renderEngChart(items){
    hideSk('sk-eng');if(!items.length)return;
    const top10=[...items].map(it=>({...it,_total:parseInt(it.num_likes||it.likes||0)+parseInt(it.num_comments||it.comments||0)}))
      .sort((a,b)=>b._total-a._total).slice(0,10);
    FMEChart._items=top10; FMEChart._render(top10,FMEChart._type);
  },

  exportCsv(tabKey){
    const items=Store[tabKey];if(!items?.length){alert('Tidak ada data untuk diekspor.');return;}
    const def=TAB_DEFS[tabKey];
    const header='index;nama;sentiment;likes;comments;tanggal;url;konten';
    const rows=items.map((it,i)=>{
      const name=this._getName(it);
      const sentRaw=String(it.sentiment_str||it.class_sentiment||it.sentiment||'').toLowerCase();
      const sent=sentRaw.includes('pos')?'Positif':sentRaw.includes('neg')?'Negatif':'Netral';
      const likes=parseInt(it.num_likes||it.likes||0);
      const cmts=parseInt(it.num_comments||it.comments||0);
      const dt=(it.date_created||'').split('T')[0], url=it.url||it.link||'';
      const content=(it.content||it.caption||'').replace(/<[^>]*>/g,'').trim().slice(0,300).replace(/;/g,',').replace(/\n/g,' ');
      return `${i};${name.replace(/;/g,',')};${sent};${likes};${cmts};${dt};${url};${content}`;
    });
    const blob=new Blob(['\uFEFF'+[header,...rows].join('\r\n')],{type:'text/csv;charset=utf-8;'});
    const a=document.createElement('a');a.href=URL.createObjectURL(blob);
    a.download=`Instagram_${def.label.replace(/\s+/g,'_')}_${FMECfg.sd}_${FMECfg.ed}.csv`;a.click();
  }
};

const FMEDonut = {
  highlight(tabKey,idx){
    const chart=FMECharts._i['donutChart-'+tabKey];if(!chart)return;
    chart.dispatchAction({type:'highlight',seriesIndex:0,dataIndex:idx});
    chart.dispatchAction({type:'showTip',seriesIndex:0,dataIndex:idx});
  }
};

const FMEChart = {
  _type:'stacked',_items:[],
  setEngType(t){
    this._type=t;
    document.querySelectorAll('#engTypeToggle .ms-toggle-btn').forEach(b=>b.classList.toggle('active',b.dataset.type===t));
    if(this._items.length)this._render(this._items,t);
  },
  _render(items,stackType){
    const chart=FMECharts.make('ch-eng');if(!chart)return;
    const labels=items.map(it=>{const n=FMEData._getName(it);return n.length>18?n.slice(0,17)+'…':n;});
    const likes=items.map(it=>parseInt(it.num_likes||it.likes||0));
    const cmts=items.map(it=>parseInt(it.num_comments||it.comments||0));
    const isStack=stackType==='stacked';
    const lOpt={show:true,position:'insideBottom',distance:15,align:'left',verticalAlign:'middle',rotate:90,formatter:p=>p.value>0?numK(p.value):'',fontSize:9,fontFamily:"'Poppins',sans-serif",fontWeight:'700',color:'#fff'};
    const lOptS={show:true,position:'inside',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:9,color:'#fff',formatter:p=>p.value>0?numK(p.value):''};
    chart.setOption({
      animation:true,animationDuration:900,animationEasing:'elasticOut',backgroundColor:'#ffffff',
      tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'shadow',shadowStyle:{color:'rgba(230,104,60,.04)'}},
        formatter:params=>{
          const idx=params[0]?.dataIndex;const it=items[idx];const name=it?FMEData._getName(it):(params[0]?.name||'');
          const total=params.reduce((s,p)=>s+(p.value||0),0);
          const rows=params.map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:7px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};flex-shrink:0;"></span><span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span></div>`).join('');
          return `<div style="min-width:200px;"><div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.12);">${esc(name)}</div>${rows}<div style="border-top:1px solid rgba(255,255,255,.12);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;"><span style="font-size:11px;color:#94a3b8;">Total</span><span style="font-size:13px;font-weight:700;">${numFmt(total)}</span></div></div>`;
        }
      },
      legend:{bottom:0,data:['Likes','Comments'],textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},icon:'circle',itemWidth:10,itemHeight:10,itemGap:20},
      grid:{top:16,right:20,bottom:52,left:16,containLabel:true},
      xAxis:{type:'category',data:labels,axisTick:{show:false},axisLine:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'600',color:'#64748b',interval:0,rotate:labels.length>7?30:0}},
      yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},
      series:[
        {name:'Likes',type:'bar',barGap:0,stack:isStack?'eng':undefined,data:likes,barMaxWidth:isStack?54:32,itemStyle:{color:'#e6683c',borderRadius:[4,4,0,0]},emphasis:{focus:'series'},label:isStack?lOptS:lOpt},
        {name:'Comments',type:'bar',barGap:0,stack:isStack?'eng':undefined,data:cmts,barMaxWidth:isStack?54:32,itemStyle:{color:'#f59e0b',borderRadius:[4,4,0,0]},emphasis:{focus:'series'},label:isStack?lOptS:lOpt}
      ]
    },true);
  }
};

/* ── POPUP ── */
const FMEPopup = {
  _drag:false,_ox:0,_oy:0,_items:[],_curTabKey:null,
  init(){
    const popup=document.getElementById('msPopup');
    const header=document.getElementById('msPopHeader');
    header.addEventListener('mousedown',e=>{this._drag=true;const r=popup.getBoundingClientRect();this._ox=e.clientX-r.left;this._oy=e.clientY-r.top;document.body.style.userSelect='none';});
    document.addEventListener('mousemove',e=>{if(!this._drag)return;const vw=window.innerWidth,vh=window.innerHeight;popup.style.left=Math.max(0,Math.min(e.clientX-this._ox,vw-popup.offsetWidth))+'px';popup.style.top=Math.max(0,Math.min(e.clientY-this._oy,vh-popup.offsetHeight))+'px';});
    document.addEventListener('mouseup',()=>{this._drag=false;document.body.style.userSelect='';});
  },
  open(items,tabKey,title,count){
    const popup=document.getElementById('msPopup');
    const def=TAB_DEFS[tabKey]||{};
    this._items=items||[];this._curTabKey=tabKey;FMEDetail.close();
    document.getElementById('msPopDot').style.background=def.colorHex||'#e6683c';
    document.getElementById('msPopTitle').textContent=title||'Post Detail';
    document.getElementById('msPopCount').textContent=numFmt(count||items.length);
    document.getElementById('msPopMeta').textContent=FMECfg.sd+' – '+FMECfg.ed;
    popup.classList.add('visible');
    const pw=500,ph=620,vw=window.innerWidth,vh=window.innerHeight;
    popup.style.left=Math.max(8,(vw-pw)/2)+'px';popup.style.top=Math.max(8,(vh-ph)/2)+'px';
    this._render(document.getElementById('msPopList'),items,tabKey);
  },
  close(){document.getElementById('msPopup')?.classList.remove('visible');FMEDetail.close();},
  exportCsv(){FMEData.exportCsv(this._curTabKey||'like');},
  _render(list,items,tabKey){
    const def=TAB_DEFS[tabKey]||{};
    if(!items?.length){list.innerHTML=`<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:14px;color:var(--text-secondary);font-size:13px;font-weight:600;">Tidak ada data</div>`;return;}
    list.innerHTML=items.slice(0,100).map(item=>{
      const name=FMEData._getName(item);
      const avColor=FMEData._getAvatarColor(item);
      const av=FMEData._getAvatar(item);
      const ini=FMEData._getInitials(name);
      const safeIni=ini.replace(/['"\\]/g,'');
      const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`:ini;
      const text=(item.content||item.caption||'').replace(/<[^>]*>/g,'').trim().slice(0,140);
      const likes=parseInt(item.num_likes||item.likes||0);
      const cmts=parseInt(item.num_comments||item.comments||0);
      const dt=(item.date_created||'').split('T')[0];
      const sentRaw=String(item.sentiment_str||item.class_sentiment||item.sentiment||'').toLowerCase();
      const sent=sentRaw.includes('pos')?'pos':sentRaw.includes('neg')?'neg':'neu';
      const sentLbl=sent==='pos'?'Pos':sent==='neg'?'Neg':'Neu';
      const eng=def.metric==='like'?`Like ${numFmt(likes)}`:`Komen ${numFmt(cmts)}`;
      const itemEnc=encodeURIComponent(JSON.stringify(item));
      return `<div class="msp-item" data-item="${esc(itemEnc)}" data-tabkey="${tabKey}" onclick="FMEPopup._onItemClick(this)">
        <div class="msp-avatar" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div>
        <div class="msp-item-body">
          <div class="msp-item-author">${esc(name)}</div>
          <div class="msp-item-text">${esc(text||'(tidak ada konten)')}</div>
          <div class="msp-item-footer">
            <span class="msp-sent msp-sent--${sent}">${sentLbl}</span>
            <span>${esc(eng)}</span>
            ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
          </div>
        </div>
      </div>`;
    }).join('');
    if(items.length>100)list.insertAdjacentHTML('beforeend',`<div style="padding:9px 14px;text-align:center;font-size:11px;font-weight:600;color:#64748b;background:var(--bg-gray-50);border-top:1px dashed var(--border-gray);">+${(items.length-100).toLocaleString()} posts lainnya</div>`);
  },
  _onItemClick(el){
    try{
      const raw=el.getAttribute('data-item');
      const item=JSON.parse(decodeURIComponent(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"')));
      const tabKey=el.dataset.tabkey||this._curTabKey;
      const def=TAB_DEFS[tabKey]||TAB_DEFS['like'];
      FMEDetail.open(item,def);
    }catch(e){console.warn('Detail parse:',e);}
  }
};

/* ── DETAIL PANEL ── */
const FMEDetail = {
  open(item, def){
    const panel=document.getElementById('msDetailPanel');
    const body=document.getElementById('msDpBody');
    const title=document.getElementById('msDpTitle');
    const color=def.colorHex||'#e6683c';
    const label=def.label||'Instagram Post';
    const name=FMEData._getName(item);
    const av=FMEData._getAvatar(item);
    const url=item.url||item.link||'';
    const content=(item.content||item.caption||'').replace(/<[^>]*>/g,'').trim();
    const date=item.date_created||'';
    const sentRaw=String(item.sentiment_str||item.class_sentiment||item.sentiment||'').toLowerCase();
    const sent=sentRaw.includes('pos')?'pos':sentRaw.includes('neg')?'neg':'neu';
    const sentLbl=sent==='pos'?'Positive':sent==='neg'?'Negative':'Neutral';
    const likes=parseInt(item.num_likes||item.likes||0);
    const cmts=parseInt(item.num_comments||item.comments||0);
    const ini=FMEData._getInitials(name);
    const safeIni=ini.replace(/['"\\]/g,'');
    const avColor=FMEData._getAvatarColor(item);
    const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`:ini;
    let dtFmt='';
    if(date){try{dtFmt=new Date(date).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){dtFmt=date.split('T')[0];}}
    const imgUrl=(item.image||item.image_url||item.thumbnail||item.media_url||item.picture||item.photo||'').trim();
    const mediaHtml=imgUrl?`<div class="msdp-media-wrap"><img class="msdp-media-img" src="${esc(imgUrl)}" loading="lazy" alt="post image" onerror="this.parentElement.style.display='none'"></div>`:'';
    title.textContent=name;
    body.innerHTML=`
      <div class="msdp-avatar-row">
        <div class="msdp-avatar-lg" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div>
        <div>
          <div class="msdp-author-name">${esc(name)}</div>
          <div style="display:flex;gap:6px;margin-top:4px;flex-wrap:wrap;">
            <span style="background:${color}22;color:${color};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;">Instagram · ${label}</span>
          </div>
        </div>
      </div>
      ${dtFmt?`<div class="msdp-meta-row"><span>${dtFmt}</span></div>`:''}
      <span class="msdp-sent-badge msdp-sent-badge--${sent}">${sentLbl}</span>
      ${mediaHtml}
      ${content?`<div class="msdp-content-text">${esc(content)}</div>`:''}
      <div class="msdp-stats-grid">
        <div class="msdp-stat-box"><div class="msdp-stat-val">${numFmt(likes)}</div><div class="msdp-stat-lbl">Likes</div></div>
        <div class="msdp-stat-box"><div class="msdp-stat-val">${numFmt(cmts)}</div><div class="msdp-stat-lbl">Comments</div></div>
      </div>
      ${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="msdp-link-btn">
        <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Lihat Post Instagram
      </a>`:''}`;
    panel.classList.add('visible');
  },
  close(){document.getElementById('msDetailPanel')?.classList.remove('visible');}
};

/* ── MAIN ── */
const FME = {
  reload(){
    FMECharts.disposeAll();
    FMETab.reset();
    Object.keys(TAB_DEFS).forEach(k=>{ Store[k]=[]; Pag[k]=1; });
    RawStore.postbylike=[];
    RawStore.postbycomment=[];
    FMEChart._items=[];
    FMEData.loadAll();
  },
  init(){ FMEDp.init(); FMEPopup.init(); FMEData.loadAll(); }
};

document.addEventListener('DOMContentLoaded',()=>FME.init());
</script>
@endsection