@extends('mk.layouts.app')

@section('title', 'Most Engagement - X | SMADIMENT')

@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --primary-green:        #038047;
    --primary-green-dark:   #026738;
    --primary-green-light:  rgba(3,128,71,0.08);
    --primary-green-border: rgba(3,128,71,0.2);
    --blue:                 #1d9bf0;
    --blue-dark:            #0d8de0;
    --blue-light:           rgba(29,155,240,.08);
    --blue-border:          rgba(29,155,240,.2);
    --green:                #10b981;
    --green-dark:           #059669;
    --green-light:          rgba(16,185,129,.08);
    --green-border:         rgba(16,185,129,.2);
    --red:                  #ef4444;
    --red-dark:             #dc2626;
    --red-light:            rgba(239,68,68,.08);
    --red-border:           rgba(239,68,68,.2);
    --purple:               #8b5cf6;
    --purple-dark:          #7c3aed;
    --purple-light:         rgba(139,92,246,.08);
    --purple-border:        rgba(139,92,246,.2);

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
  .ms-stat-card--blue   { --stat-bar:linear-gradient(90deg,#1d9bf0,#0d8de0); }
  .ms-stat-card--green  { --stat-bar:linear-gradient(90deg,#10b981,#059669); }
  .ms-stat-card--red    { --stat-bar:linear-gradient(90deg,#ef4444,#dc2626); }
  .ms-stat-card--purple { --stat-bar:linear-gradient(90deg,#8b5cf6,#7c3aed); }
  .ms-stat-label { font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;display:flex;align-items:center;gap:6px; }
  .ms-stat-dot   { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
  .ms-stat-value { font-size:32px;font-weight:700;color:var(--text-primary);letter-spacing:-1px;line-height:1;min-height:40px;display:flex;align-items:center; }
  .ms-stat-sub   { font-size:11px;color:var(--text-muted);font-weight:500;margin-top:7px; }

  /* ── TABS ── */
  .ms-tabs { display:flex;gap:4px;background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);padding:6px;margin-bottom:24px;box-shadow:var(--shadow-sm); }
  .ms-tab-btn { flex:1;display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 20px;border-radius:var(--radius-sm);border:none;background:transparent;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;transition:var(--transition); }
  .ms-tab-btn:hover { background:var(--bg-gray-50);color:var(--text-primary); }
  .ms-tab-btn.active { background:linear-gradient(135deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);color:#fff;box-shadow:0 4px 12px rgba(3,128,71,.25); }
  .ms-tab-btn.active--blue   { background:linear-gradient(135deg,#1d9bf0 0%,#0d8de0 100%)!important;box-shadow:0 4px 12px rgba(29,155,240,.25)!important; }
  .ms-tab-btn.active--green  { background:linear-gradient(135deg,#10b981 0%,#059669 100%)!important;box-shadow:0 4px 12px rgba(16,185,129,.25)!important; }
  .ms-tab-btn.active--red    { background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%)!important;box-shadow:0 4px 12px rgba(239,68,68,.25)!important; }
  .ms-tab-btn.active--purple { background:linear-gradient(135deg,#8b5cf6 0%,#7c3aed 100%)!important;box-shadow:0 4px 12px rgba(139,92,246,.25)!important; }
  .ms-tab-btn svg { width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;flex-shrink:0; }
  .ms-tab-chip { display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:20px;padding:0 6px;border-radius:10px;font-size:10px;font-weight:800;background:rgba(255,255,255,.22);color:inherit; }
  .ms-tab-btn:not(.active) .ms-tab-chip { background:var(--bg-gray-100);color:var(--text-muted); }
  .ms-tab-panel { display:none; }
  .ms-tab-panel.active { display:block; }

  /* ── DO CARD ── */
  .do-card { background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);overflow:hidden;display:flex;flex-direction:column;box-shadow:var(--shadow-sm);transition:var(--transition);position:relative; }
  .do-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);opacity:0;transition:opacity .3s; }
  .do-card--blue::before   { background:linear-gradient(90deg,#1d9bf0,#0d8de0); }
  .do-card--green::before  { background:linear-gradient(90deg,#10b981,#059669); }
  .do-card--red::before    { background:linear-gradient(90deg,#ef4444,#dc2626); }
  .do-card--purple::before { background:linear-gradient(90deg,#8b5cf6,#7c3aed); }
  .do-card--ink::before    { background:linear-gradient(90deg,#1a202c,#334155); }
  .do-card:hover { box-shadow:var(--shadow-lg);border-color:var(--primary-green-border); }
  .do-card:hover::before { opacity:1; }
  .do-card-head { display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-gray);flex-shrink:0;gap:12px;flex-wrap:wrap; }
  .do-card-head-left { display:flex;align-items:center;gap:12px;min-width:0; }
  .do-head-icon { width:40px;height:40px;border-radius:var(--radius-sm);background:linear-gradient(135deg,var(--primary-green-light) 0%,rgba(3,128,71,.05) 100%);display:flex;align-items:center;justify-content:center;flex-shrink:0; }
  .do-head-icon svg { width:20px;height:20px;fill:none;stroke:var(--primary-green);stroke-width:2;stroke-linecap:round;stroke-linejoin:round; }
  .do-head-icon--blue   { background:var(--blue-light)!important; }
  .do-head-icon--blue svg { stroke:#1d9bf0!important; }
  .do-head-icon--green  { background:var(--green-light)!important; }
  .do-head-icon--green svg { stroke:#10b981!important; }
  .do-head-icon--red    { background:var(--red-light)!important; }
  .do-head-icon--red svg { stroke:#ef4444!important; }
  .do-head-icon--purple { background:var(--purple-light)!important; }
  .do-head-icon--purple svg { stroke:#8b5cf6!important; }
  .do-head-icon--ink    { background:rgba(26,32,44,.06)!important; }
  .do-head-icon--ink svg { stroke:#1a202c!important; }
  .do-card-title    { font-size:15px;font-weight:700;color:var(--text-primary);line-height:1.3; }
  .do-card-subtitle { font-size:11px;color:var(--text-muted);font-weight:500;margin-top:2px; }
  .do-badge { display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;background:var(--bg-gray-100);color:var(--text-secondary);white-space:nowrap;flex-shrink:0; }
  .do-badge--blue   { background:#dbeafe;color:#1e40af; }
  .do-badge--green  { background:#d1fae5;color:#065f46; }
  .do-badge--red    { background:#fee2e2;color:#991b1b; }
  .do-badge--purple { background:#ede9fe;color:#5b21b6; }
  .do-card-body { padding:20px;flex:1; }
  .do-card-body--flush { padding:0; }

  /* ── DONUT CARD ── */
  .ms-donut-legend { display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
  .ms-donut-leg-item { display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:600;color:var(--text-secondary);padding:4px 10px;background:var(--bg-gray-100);border-radius:20px;border:1px solid var(--border-gray);cursor:pointer;transition:var(--transition); }
  .ms-donut-leg-item:hover { border-color:var(--primary-green);background:var(--primary-green-light);color:var(--primary-green); }
  .ms-donut-dot { width:9px;height:9px;border-radius:50%;flex-shrink:0; }

  .ms-ch-460 { position:relative;height:460px; }
  .ms-ch-320 { position:relative;height:320px; }
  .ms-ch-340 { position:relative;height:340px; }

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
  .fme-post:hover { background:var(--bg-gray-50); }
  .fme-post-rank { width:28px;height:28px;border-radius:50%;background:var(--bg-gray-100);border:1.5px solid var(--border-gray);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:var(--text-muted);flex-shrink:0;margin-top:6px; }
  .fme-post-rank--1 { background:linear-gradient(135deg,#ffd700,#f59e0b);color:#7c5900;border-color:#ffd700; }
  .fme-post-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0; }
  .fme-post-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32; }
  .fme-post-av { width:42px;height:42px;border-radius:50%;flex-shrink:0;background:linear-gradient(135deg,#1a202c,#334155);color:#fff;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;border:2px solid var(--border-gray);overflow:hidden; }
  .fme-post-av img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  .fme-post-body { flex:1;min-width:0; }
  .fme-post-author { font-size:13px;font-weight:700;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
  .fme-post-handle { font-size:10.5px;color:var(--text-muted);font-weight:400; }
  .fme-post-date   { font-size:10.5px;color:var(--text-muted);font-weight:400;margin-top:1px;margin-bottom:6px; }
  .fme-post-text   { font-size:12.5px;color:var(--text-secondary);line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:10px;word-break:break-word; }
  .fme-post-stats  { display:flex;align-items:center;gap:8px;flex-wrap:wrap; }
  .fme-post-metric { display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;background:var(--bg-gray-100);border:1px solid var(--border-gray);color:var(--text-secondary);white-space:nowrap; }
  .fme-post-metric svg { width:11px;height:11px;fill:none;stroke-width:2;stroke-linecap:round;flex-shrink:0; }
  .fme-post-metric--blue   { background:var(--blue-light);border-color:var(--blue-border);color:#1d9bf0; }
  .fme-post-metric--blue svg   { stroke:#1d9bf0; }
  .fme-post-metric--green  { background:var(--green-light);border-color:var(--green-border);color:#10b981; }
  .fme-post-metric--green svg  { stroke:#10b981; }
  .fme-post-metric--red    { background:var(--red-light);border-color:var(--red-border);color:#ef4444; }
  .fme-post-metric--red svg    { stroke:#ef4444; }
  .fme-post-metric--purple { background:var(--purple-light);border-color:var(--purple-border);color:#8b5cf6; }
  .fme-post-metric--purple svg { stroke:#8b5cf6; }
  .fme-sent { display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.3px; }
  .fme-sent--pos { background:#d1fae5;color:#065f46; }
  .fme-sent--neg { background:#fee2e2;color:#991b1b; }
  .fme-sent--neu { background:var(--bg-gray-100);color:var(--text-secondary); }
  .fme-view-link { display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:#1a202c;text-decoration:none;padding:3px 9px;border-radius:20px;background:rgba(26,32,44,.06);border:1px solid rgba(26,32,44,.15);transition:var(--transition);margin-left:auto; }
  .fme-view-link:hover { background:#1a202c;color:#fff; }
  .fme-view-link svg { width:9px;height:9px;stroke:currentColor;fill:none;stroke-width:2.5; }

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
  .fme-spinner { width:34px;height:34px;border:3px solid var(--border-gray);border-top-color:var(--primary-green);border-radius:50%;animation:fmeSpin .7s linear infinite; }
  @keyframes fmeSpin { to{transform:rotate(360deg)} }
  .fme-spinner-state { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 20px;gap:14px;color:var(--text-secondary);font-size:13px;font-weight:500; }

  /* ── EMPTY STATE ── */
  .do-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 20px;gap:10px; }
  .do-empty svg { width:40px;height:40px;stroke:var(--border-gray);fill:none;stroke-width:1.5; }
  .do-empty-text { font-size:13px;font-weight:600;color:var(--text-secondary); }

  /* ── LAYOUT ── */
  .ms-grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px; }
  .ms-mb20 { margin-bottom:20px; }

  /* ── TWEET DETAIL MODAL ── */
  .tweet-detail-modal { position:fixed;inset:0;z-index:10000;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.75);backdrop-filter:blur(10px); }
  .tweet-detail-modal.show { display:flex; }
  .tweet-modal-overlay { position:absolute;inset:0; }
  .tweet-modal-content { position:relative;z-index:1;background:#fff;border-radius:var(--radius);box-shadow:0 25px 50px rgba(0,0,0,.5);width:90%;max-width:560px;max-height:85vh;display:flex;flex-direction:column;animation:dpUp .3s ease-out; }
  .tweet-modal-header { display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:1px solid var(--border-gray);background:var(--bg-gray-50);border-radius:var(--radius) var(--radius) 0 0; }
  .tweet-modal-header h3 { font-size:16px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:10px; }
  .tweet-modal-header h3 .x-ico { width:28px;height:28px;background:#000;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0; }
  .tweet-modal-header h3 .x-ico svg { width:15px;height:15px;fill:#fff; }
  .tweet-modal-close { width:32px;height:32px;border-radius:var(--radius-xs);border:none;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);transition:var(--transition); }
  .tweet-modal-close:hover { background:#fee2e2;color:#dc2626; }
  .tweet-modal-body { padding:24px;overflow-y:auto;flex:1; }
  .tdm-author-row { display:flex;align-items:center;gap:14px;margin-bottom:16px; }
  .tdm-avatar { width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#1a202c,#334155);color:#fff;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid var(--border-gray);overflow:hidden; }
  .tdm-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  .tdm-author-name { font-size:15px;font-weight:700;color:var(--text-primary);line-height:1.2; }
  .tdm-author-scr  { font-size:12px;color:var(--text-muted); }
  .tdm-tweet-text { font-size:14px;line-height:1.65;color:var(--text-primary);margin-bottom:16px;padding:14px;background:var(--bg-gray-50);border-radius:var(--radius-sm);border:1px solid var(--border-gray);word-break:break-word; }
  .tdm-metrics { display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:16px; }
  .tdm-metric-box { text-align:center;padding:10px 8px;background:var(--bg-gray-50);border-radius:var(--radius-sm);border:1px solid var(--border-gray); }
  .tdm-metric-val { font-size:18px;font-weight:700;color:var(--text-primary); }
  .tdm-metric-lbl { font-size:9px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;margin-top:2px; }
  .tdm-footer { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding-top:14px;border-top:1px solid var(--border-gray); }
  .tdm-date { font-size:12px;color:var(--text-muted); }
  .tdm-open-x { display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:#000;color:#fff;border-radius:10px;font-family:var(--font);font-size:13px;font-weight:600;text-decoration:none;transition:var(--transition); }
  .tdm-open-x:hover { background:#1d1d1d;transform:translateY(-1px); }
  .tdm-open-x svg { width:13px;height:13px;fill:#fff; }

  @media (max-width:1200px) { .ms-stat-grid { grid-template-columns:repeat(2,1fr); } }
  @media (max-width:768px) {
    .fme-page { padding:16px; }
    .ms-stat-grid { grid-template-columns:1fr; }
    .ms-grid-2 { grid-template-columns:1fr; }
    .date-picker-container { flex-direction:column;width:96%; }
    .date-picker-sidebar { width:100%;flex-direction:row;overflow-x:auto;border-right:none;border-bottom:1px solid var(--border-gray);border-radius:var(--radius) var(--radius) 0 0;flex-shrink:0; }
    .date-preset { white-space:nowrap; }
    .calendars-wrapper { flex-direction:column; }
    .tdm-metrics { grid-template-columns:repeat(2,1fr); }
  }
</style>
@endsection

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
  $projects  = $projects ?? [];
@endphp

<div class="fme-page">

  {{-- PAGE HEADER --}}
  <div class="page-header">
    <div class="page-header-left">
      <h1>
        <svg viewBox="0 0 24 24" style="width:26px;height:26px;fill:#000;display:inline-block;vertical-align:middle;margin-right:10px;margin-top:-3px;">
          <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
        X Most Engagement
      </h1>
      <p>Postingan dengan Most Views, Most Retweets, Most Likes dan Most Replies dari X (Twitter)</p>
    </div>
    <button class="ms-refresh-btn" onclick="XME.reload()">
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
          <label class="filter-label" style="opacity:0;pointer-events:none;">&#8206;</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Terapkan Filter
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- DATE PICKER MODAL --}}
  <div class="date-picker-modal" id="fmeDpModal">
    <div class="date-picker-overlay" onclick="XMEDp.close()"></div>
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
          <button class="nav-btn" onclick="XMEDp.nav(-1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
          <div class="calendars-wrapper">
            <div class="calendar" id="fmeDpCal1"></div>
            <div class="calendar" id="fmeDpCal2"></div>
          </div>
          <button class="nav-btn" onclick="XMEDp.nav(1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
        <div class="date-picker-display"><span id="fmeDpRangeText">{{ $startDate }} – {{ $endDate }}</span></div>
        <div class="date-picker-footer">
          <button class="cancel-btn" onclick="XMEDp.close()">Batal</button>
          <button class="apply-date-btn" onclick="XMEDp.apply()">Terapkan</button>
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
    <div class="ms-stat-card ms-stat-card--blue">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#1d9bf0;"></span>Total Views</div>
      <div class="ms-stat-value" id="valViews"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Dari semua tweet</div>
    </div>
    <div class="ms-stat-card ms-stat-card--green">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#10b981;"></span>Total Retweets</div>
      <div class="ms-stat-value" id="valRT"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Konten yang dibagikan</div>
    </div>
    <div class="ms-stat-card ms-stat-card--red">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#ef4444;"></span>Total Likes</div>
      <div class="ms-stat-value" id="valLikes"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Apresiasi konten</div>
    </div>
    <div class="ms-stat-card ms-stat-card--purple">
      <div class="ms-stat-label"><span class="ms-stat-dot" style="background:#8b5cf6;"></span>Total Replies</div>
      <div class="ms-stat-value" id="valReplies"><div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div></div>
      <div class="ms-stat-sub">Interaksi balasan</div>
    </div>
  </div>

  {{-- DONUT SECTION --}}
  <div class="ms-section-header" id="donutSectionHead">
    <div class="ms-section-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg></div>
    <span class="ms-section-title" id="donutSectionLabel">Distribusi Views — Top 5</span>
    <div class="ms-section-line"></div>
  </div>
  <div class="do-card do-card--blue ms-mb20" id="donutMasterCard">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <div class="do-head-icon do-head-icon--blue" id="donutMasterIco">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
        </div>
        <div>
          <div class="do-card-title" id="donutMasterTitle">Top 5 Most Viewed — Distribution</div>
          <div class="do-card-subtitle">Proporsi engagement per tweet — hover segmen untuk detail</div>
        </div>
      </div>
      <div id="donutMasterLegend" class="ms-donut-legend"></div>
    </div>
    <div class="do-card-body">
      <div id="donutWrap">
        <div class="loading-skeleton" id="donutSkel" style="height:400px;border-radius:10px;"></div>
        <div id="donutChart" style="height:400px;display:none;"></div>
        <div id="donutEmpty" style="display:none;"><div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="do-empty-text">Tidak ada data</span></div></div>
      </div>
    </div>
  </div>

  {{-- TABS --}}
  <div class="ms-tabs">
    <button class="ms-tab-btn active active--blue" id="tab-view" onclick="XMETab.show('view')">
      <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      Most Viewed
      <span class="ms-tab-chip" id="chip-view">—</span>
    </button>
    <button class="ms-tab-btn" id="tab-rt" onclick="XMETab.show('rt')">
      <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
      Most Retweeted
      <span class="ms-tab-chip" id="chip-rt">—</span>
    </button>
    <button class="ms-tab-btn" id="tab-like" onclick="XMETab.show('like')">
      <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      Most Liked
      <span class="ms-tab-chip" id="chip-like">—</span>
    </button>
    <button class="ms-tab-btn" id="tab-reply" onclick="XMETab.show('reply')">
      <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Most Replies
      <span class="ms-tab-chip" id="chip-reply">—</span>
    </button>
  </div>

  {{-- ═══ VIEW PANEL ═══ --}}
  <div class="ms-tab-panel active" id="panel-view">
    <div class="do-card do-card--blue ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--blue"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
          <div><div class="do-card-title">Top Tweets by Views</div><div class="do-card-subtitle">Tweet X dengan paling banyak views — klik untuk detail</div></div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0;">
          <button class="ms-export-btn" onclick="XMEData.exportCsv('view')"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>CSV</button>
          <span class="do-badge do-badge--blue" id="badge-view">Loading…</span>
        </div>
      </div>
      <div id="list-view" class="do-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data views…</div></div>
      <div id="pag-view"></div>
    </div>
    <div class="do-card do-card--blue ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--blue"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
          <div><div class="do-card-title">Views Chart</div><div class="do-card-subtitle">Top 10 tweet berdasarkan views</div></div>
        </div>
        <span class="do-badge do-badge--blue">Top 10</span>
      </div>
      <div class="do-card-body"><div class="ms-ch-320"><div id="barView"></div><div class="loading-skeleton skel-overlay" id="sk-view"></div></div></div>
    </div>
    {{-- Engagement Comparison --}}
    <div class="ms-section-header">
      <div class="ms-section-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
      <span class="ms-section-title">Perbandingan Engagement — Top 10</span>
      <div class="ms-section-line"></div>
    </div>
    <div class="do-card do-card--ink ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--ink"><svg viewBox="0 0 24 24"><rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/></svg></div>
          <div><div class="do-card-title">View vs RT vs Like vs Reply — Top 10 Tweets</div><div class="do-card-subtitle">Perbandingan engagement keseluruhan pada tweet terpopuler</div></div>
        </div>
      </div>
      <div class="do-card-body"><div class="ms-ch-340"><div id="barEngagement"></div><div class="loading-skeleton skel-overlay" id="sk-eng"></div></div></div>
    </div>
  </div>

  {{-- ═══ RT PANEL ═══ --}}
  <div class="ms-tab-panel" id="panel-rt">
    <div class="do-card do-card--green ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--green"><svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg></div>
          <div><div class="do-card-title">Top Tweets by Retweets</div><div class="do-card-subtitle">Tweet X dengan paling banyak retweets — klik untuk detail</div></div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0;">
          <button class="ms-export-btn" onclick="XMEData.exportCsv('rt')"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>CSV</button>
          <span class="do-badge do-badge--green" id="badge-rt">Loading…</span>
        </div>
      </div>
      <div id="list-rt" class="do-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data retweets…</div></div>
      <div id="pag-rt"></div>
    </div>
    <div class="do-card do-card--green ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left"><div class="do-head-icon do-head-icon--green"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><div><div class="do-card-title">Retweets Chart</div><div class="do-card-subtitle">Top 10 tweet berdasarkan retweets</div></div></div>
        <span class="do-badge do-badge--green">Top 10</span>
      </div>
      <div class="do-card-body"><div class="ms-ch-320"><div id="barRT"></div><div class="loading-skeleton skel-overlay" id="sk-rt"></div></div></div>
    </div>
  </div>

  {{-- ═══ LIKE PANEL ═══ --}}
  <div class="ms-tab-panel" id="panel-like">
    <div class="do-card do-card--red ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--red"><svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></div>
          <div><div class="do-card-title">Top Tweets by Likes</div><div class="do-card-subtitle">Tweet X dengan paling banyak likes — klik untuk detail</div></div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0;">
          <button class="ms-export-btn" onclick="XMEData.exportCsv('like')"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>CSV</button>
          <span class="do-badge do-badge--red" id="badge-like">Loading…</span>
        </div>
      </div>
      <div id="list-like" class="do-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data likes…</div></div>
      <div id="pag-like"></div>
    </div>
    <div class="do-card do-card--red ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left"><div class="do-head-icon do-head-icon--red"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><div><div class="do-card-title">Likes Chart</div><div class="do-card-subtitle">Top 10 tweet berdasarkan likes</div></div></div>
        <span class="do-badge do-badge--red">Top 10</span>
      </div>
      <div class="do-card-body"><div class="ms-ch-320"><div id="barLike"></div><div class="loading-skeleton skel-overlay" id="sk-like"></div></div></div>
    </div>
  </div>

  {{-- ═══ REPLY PANEL ═══ --}}
  <div class="ms-tab-panel" id="panel-reply">
    <div class="do-card do-card--purple ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <div class="do-head-icon do-head-icon--purple"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
          <div><div class="do-card-title">Top Tweets by Replies</div><div class="do-card-subtitle">Tweet X dengan paling banyak replies — klik untuk detail</div></div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;flex-shrink:0;">
          <button class="ms-export-btn" onclick="XMEData.exportCsv('reply')"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>CSV</button>
          <span class="do-badge do-badge--purple" id="badge-reply">Loading…</span>
        </div>
      </div>
      <div id="list-reply" class="do-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data replies…</div></div>
      <div id="pag-reply"></div>
    </div>
    <div class="do-card do-card--purple ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left"><div class="do-head-icon do-head-icon--purple"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><div><div class="do-card-title">Replies Chart</div><div class="do-card-subtitle">Top 10 tweet berdasarkan replies</div></div></div>
        <span class="do-badge do-badge--purple">Top 10</span>
      </div>
      <div class="do-card-body"><div class="ms-ch-320"><div id="barReply"></div><div class="loading-skeleton skel-overlay" id="sk-reply"></div></div></div>
    </div>
  </div>

</div>{{-- /fme-page --}}

{{-- TWEET DETAIL MODAL --}}
<div class="tweet-detail-modal" id="tweetModal">
  <div class="tweet-modal-overlay" onclick="XMEModal.close()"></div>
  <div class="tweet-modal-content">
    <div class="tweet-modal-header">
      <h3><span class="x-ico"><svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></span>Tweet Detail</h3>
      <button class="tweet-modal-close" onclick="XMEModal.close()"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="tweet-modal-body" id="tweetModalBody"></div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script>
'use strict';

/* ═══════════════════════════════════════════════════
   CONFIG
═══════════════════════════════════════════════════ */
const CFG = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
  colors: { view:'#1d9bf0', rt:'#10b981', like:'#ef4444', reply:'#8b5cf6' },
  perPage: 10
};
const DONUT_COLORS = ['#2FC6F6','#f59e0b','#10b981','#8b5cf6','#f43f5e'];
const TABS = ['view','rt','like','reply'];

/* ═══════════════════════════════════════════════════
   DATE PICKER
═══════════════════════════════════════════════════ */
const XMEDp = (() => {
  let ds=null, de=null, m1=new Date(), m2=new Date(), pickStart=true;
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];

  function init(){
    const si=document.getElementById('hSD'), ei=document.getElementById('hED');
    ds=si?.value?new Date(si.value):( ()=>{const d=new Date();d.setDate(d.getDate()-6);return d;} )();
    de=ei?.value?new Date(ei.value):new Date();
    m1=new Date(ds); m2=new Date(ds); m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('fmeDpTrigger')?.addEventListener('click', open);
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click',onPreset));
    document.addEventListener('keydown',e=>{ if(e.key==='Escape'&&document.getElementById('fmeDpModal').classList.contains('show')) close(); });
  }
  function open()  { document.getElementById('fmeDpModal').classList.add('show'); render(); }
  function close() { document.getElementById('fmeDpModal').classList.remove('show'); }
  function apply() {
    document.getElementById('hSD').value=fmt(ds);
    document.getElementById('hED').value=fmt(de);
    document.getElementById('fmeDpDisplay').textContent=fmt(ds)+' – '+fmt(de);
    close(); document.getElementById('fmeForm').submit();
  }
  function nav(dir){m1.setMonth(m1.getMonth()+dir);m2.setMonth(m2.getMonth()+dir);render();}
  function onPreset(e){
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active')); e.target.classList.add('active');
    const today=new Date();today.setHours(0,0,0,0);
    switch(e.target.dataset.p){
      case'today':ds=new Date(today);de=new Date(today);break;
      case'yesterday':ds=new Date(today);ds.setDate(today.getDate()-1);de=new Date(ds);break;
      case'last7':de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-6);break;
      case'last30':de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-29);break;
      case'thismonth':ds=new Date(today.getFullYear(),today.getMonth(),1);de=new Date(today);break;
      case'lastmonth':ds=new Date(today.getFullYear(),today.getMonth()-1,1);de=new Date(today.getFullYear(),today.getMonth(),0);break;
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
    for(let i=0;i<first.getDay();i++) h+=`<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const date=new Date(y,mn,d);date.setHours(0,0,0,0); let cls='calendar-day';
      if(sameD(date,today))cls+=' today'; if(ds&&de){if(sameD(date,ds)||sameD(date,de))cls+=' selected';else if(date>ds&&date<de)cls+=' in-range';}
      h+=`<button type="button" class="${cls}" data-date="${fmt(date)}">${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();for(let i=1;i<=rem;i++)h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>'; el.innerHTML=h;
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
  function fmt(d){if(!d)return'';return`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
  function sameD(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
  return{init,open,close,apply,nav};
})();

/* ═══════════════════════════════════════════════════
   UTILS
═══════════════════════════════════════════════════ */
const numFmt=n=>parseInt(n||0).toLocaleString('id-ID');
const numK=n=>{n=parseInt(n||0);return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n);};
const esc=s=>(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const hideSk=id=>{const e=document.getElementById(id);if(e)e.style.display='none';};
const emptyH=msg=>`<div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="do-empty-text">${msg}</span></div>`;

/* ═══════════════════════════════════════════════════
   APEX CHARTS REGISTRY
═══════════════════════════════════════════════════ */
const Charts={};

/* ═══════════════════════════════════════════════════
   STORE & PAGINATION
═══════════════════════════════════════════════════ */
const Store={view:[],rt:[],like:[],reply:[]};
const Pag={view:1,rt:1,like:1,reply:1};

/* ═══════════════════════════════════════════════════
   TABS
═══════════════════════════════════════════════════ */
const tabColorMap={view:'blue',rt:'green',like:'red',reply:'purple'};
const tabTitleMap={view:'Top 5 Most Viewed — Distribution',rt:'Top 5 Most Retweeted — Distribution',like:'Top 5 Most Liked — Distribution',reply:'Top 5 Most Replies — Distribution'};
const tabLabelMap={view:'Distribusi Views — Top 5',rt:'Distribusi Retweets — Top 5',like:'Distribusi Likes — Top 5',reply:'Distribusi Replies — Top 5'};

const XMETab = {
  _loaded:{view:false,rt:false,like:false,reply:false},
  show(type){
    TABS.forEach(t=>{
      const tb=document.getElementById('tab-'+t),panel=document.getElementById('panel-'+t);
      const isThis=t===type;
      if(tb){tb.classList.toggle('active',isThis);tb.classList.remove('active--blue','active--green','active--red','active--purple');if(isThis)tb.classList.add('active--'+tabColorMap[t]);}
      if(panel)panel.classList.toggle('active',isThis);
    });
    const col=tabColorMap[type];
    const card=document.getElementById('donutMasterCard');if(card)card.className='do-card do-card--'+col+' ms-mb20';
    const ico=document.getElementById('donutMasterIco');if(ico)ico.className='do-head-icon do-head-icon--'+col;
    const ttl=document.getElementById('donutMasterTitle');if(ttl)ttl.textContent=tabTitleMap[type];
    const lbl=document.getElementById('donutSectionLabel');if(lbl)lbl.textContent=tabLabelMap[type];
    if(!this._loaded[type]){this._loaded[type]=true;XMEData.loadTab(type);}
    XMEData.renderDonut(type);
  },
  reset(){this._loaded={view:false,rt:false,like:false,reply:false};}
};

/* ═══════════════════════════════════════════════════
   DATA
═══════════════════════════════════════════════════ */
const XMEData = {
  async loadAll(){
    if(!CFG.pid){
      ['valViews','valRT','valLikes','valReplies'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<span style="font-size:14px;color:#94a3b8;">—</span>';});
      TABS.forEach(t=>{const e=document.getElementById('list-'+t);if(e)e.innerHTML=emptyH('Pilih project terlebih dahulu');});
      ['sk-view','sk-rt','sk-like','sk-reply','sk-eng','donutSkel'].forEach(hideSk);
      return;
    }
    try{
      const res=await fetch(`/mk/api/x/most-engagement?project_id=${CFG.pid}&start_date=${CFG.sd}&end_date=${CFG.ed}`);
      const json=await res.json();
      let items=json.data||[];if(!Array.isArray(items))items=[];

      // Put same data into all stores; sorting handles per-tab ordering
      Store.view=[...items]; Store.rt=[...items]; Store.like=[...items]; Store.reply=[...items];
      TABS.forEach(t=>{
        const chip=document.getElementById('chip-'+t);if(chip)chip.textContent=items.length;
        const badge=document.getElementById('badge-'+t);if(badge)badge.textContent=`${items.length} tweets`;
      });
      this._updateStats(items);
      XMETab._loaded.view=true;
      this._renderList('view');
      this._renderBar('view',this._sortItems(items,'view').slice(0,10));
      this._renderEngChart(items);
      this.renderDonut('view');
    }catch(err){
      console.error(err);
      document.getElementById('list-view').innerHTML=emptyH('Gagal memuat data: '+err.message);
      ['sk-view','sk-rt','sk-like','sk-reply','sk-eng','donutSkel'].forEach(hideSk);
    }
  },

  loadTab(type){
    const items=Store[type];
    if(!items.length){const el=document.getElementById('list-'+type);if(el)el.innerHTML=emptyH('Tidak ada data');hideSk('sk-'+type);return;}
    Pag[type]=1;
    this._renderList(type);
    this._renderBar(type,this._sortItems(items,type).slice(0,10));
    this.renderDonut(type);
  },

  _sortItems(items,type){
    const keyMap={view:'view_cnt',rt:'rt',like:'fav_count',reply:'reply_cnt'};
    const key=keyMap[type]||'view_cnt';
    return[...items].sort((a,b)=>parseInt(b[key]||0)-parseInt(a[key]||0));
  },

  _metric(item,type){
    const keyMap={view:'view_cnt',rt:'rt',like:'fav_count',reply:'reply_cnt'};
    return parseInt(item[keyMap[type]]||0);
  },

  _updateStats(items){
    let tV=0,tR=0,tL=0,tRp=0;
    items.forEach(i=>{tV+=parseInt(i.view_cnt||0);tR+=parseInt(i.rt||0);tL+=parseInt(i.fav_count||0);tRp+=parseInt(i.reply_cnt||0);});
    const eV=document.getElementById('valViews');if(eV)eV.textContent=numFmt(tV);
    const eR=document.getElementById('valRT');if(eR)eR.textContent=numFmt(tR);
    const eL=document.getElementById('valLikes');if(eL)eL.textContent=numFmt(tL);
    const eRp=document.getElementById('valReplies');if(eRp)eRp.textContent=numFmt(tRp);
  },

  _getName(item){return item.author?.name||item.name||'Unknown';},
  _getScr(item){return item.author?.scr_name||item.name||'';},
  _getAvatar(item){return(item.avatar_url||item.author?.image||'').trim();},
  _getInitials(name){
    if(!name||name==='Unknown')return'?';
    const w=name.trim().split(/\s+/).filter(Boolean);
    return w.length>=2?(w[0][0]+w[1][0]).toUpperCase():(w[0]?w[0].substring(0,2).toUpperCase():'?');
  },

  _renderList(type){
    const sorted=this._sortItems(Store[type],type);
    const listEl=document.getElementById('list-'+type),pagEl=document.getElementById('pag-'+type);
    if(!listEl)return; if(!sorted.length){listEl.innerHTML=emptyH('Tidak ada data');if(pagEl)pagEl.innerHTML='';return;}
    const page=Pag[type]||1,total=sorted.length,perPage=CFG.perPage,pages=Math.ceil(total/perPage),start=(page-1)*perPage;
    listEl.innerHTML=`<div class="fme-post-list">${sorted.slice(start,start+perPage).map((item,i)=>this._postHTML(item,start+i,type)).join('')}</div>`;
    if(pagEl)pagEl.innerHTML=this._pagHTML(type,page,pages,total,start+1,Math.min(start+perPage,total));
    listEl.querySelectorAll('.fme-post').forEach(el=>{
      el.addEventListener('click',()=>{
        try{const item=JSON.parse(decodeURIComponent(el.dataset.item));XMEModal.open(item);}catch(e){console.warn(e);}
      });
    });
  },

  _postHTML(item,globalIdx,type){
    const rank=globalIdx+1,rkCls=rank===1?'--1':rank===2?'--2':rank===3?'--3':'';
    const name=this._getName(item),scr=this._getScr(item),av=this._getAvatar(item),ini=this._getInitials(name);
    const content=(item.content||'').replace(/<[^>]*>/g,'').trim();
    const dt=(item.date_created||'').split('T')[0];
    const subId=item.sub_id||'';
    const tweetUrl=subId?`https://twitter.com/${scr}/status/${subId}`:`https://twitter.com/${scr}`;
    const views=parseInt(item.view_cnt||0),rt=parseInt(item.rt||0),likes=parseInt(item.fav_count||0),replies=parseInt(item.reply_cnt||0);
    const sentRaw=String(item.sentiment_str||'').toLowerCase();
    const sent=sentRaw.includes('pos')?'pos':sentRaw.includes('neg')?'neg':'neu';
    const sentLbl=sent==='pos'?'Positive':sent==='neg'?'Negative':'Neutral';
    const hl={view:'--blue',rt:'--green',like:'--red',reply:'--purple'};
    const vH=type==='view'?` fme-post-metric${hl.view}`:'';
    const rH=type==='rt'?` fme-post-metric${hl.rt}`:'';
    const lH=type==='like'?` fme-post-metric${hl.like}`:'';
    const rpH=type==='reply'?` fme-post-metric${hl.reply}`:'';
    const avHtml=av&&av.startsWith('http')?`<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${esc(ini)}'">`:(ini);
    const itemEnc=encodeURIComponent(JSON.stringify(item));
    return`<div class="fme-post" data-item="${esc(itemEnc)}">
      <div class="fme-post-rank fme-post-rank${rkCls}">${rank}</div>
      <div class="fme-post-av">${avHtml}</div>
      <div class="fme-post-body">
        <div class="fme-post-author">${esc(name)}</div>
        <div class="fme-post-handle">@${esc(scr)}</div>
        ${dt?`<div class="fme-post-date">${dt}</div>`:''}
        ${content?`<div class="fme-post-text">${esc(content)}</div>`:''}
        <div class="fme-post-stats">
          <span class="fme-post-metric${vH}"><svg viewBox="0 0 24 24" stroke="${type==='view'?'#1d9bf0':'currentColor'}"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>${numFmt(views)}</span>
          <span class="fme-post-metric${rH}"><svg viewBox="0 0 24 24" stroke="${type==='rt'?'#10b981':'currentColor'}"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>${numFmt(rt)}</span>
          <span class="fme-post-metric${lH}"><svg viewBox="0 0 24 24" stroke="${type==='like'?'#ef4444':'currentColor'}"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>${numFmt(likes)}</span>
          <span class="fme-post-metric${rpH}"><svg viewBox="0 0 24 24" stroke="${type==='reply'?'#8b5cf6':'currentColor'}"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>${numFmt(replies)}</span>
          <span class="fme-sent fme-sent--${sent}">${sentLbl}</span>
          <a href="${esc(tweetUrl)}" target="_blank" rel="noopener" class="fme-view-link" onclick="event.stopPropagation()"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Lihat</a>
        </div>
      </div>
    </div>`;
  },

  _pagHTML(type,page,pages,total,from,to){
    if(pages<=1)return'';
    const r=2;let btns='';
    btns+=`<button class="fme-pag-btn" ${page<=1?'disabled':''} onclick="XMEData.goPage('${type}',${page-1})"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>`;
    for(let i=1;i<=pages;i++){if(i===1||i===pages||(i>=page-r&&i<=page+r))btns+=`<button class="fme-pag-btn${i===page?' is-active':''}" onclick="XMEData.goPage('${type}',${i})">${i}</button>`;else if(i===page-r-1||i===page+r+1)btns+=`<span class="fme-pag-btn" style="cursor:default;opacity:.5;">…</span>`;}
    btns+=`<button class="fme-pag-btn" ${page>=pages?'disabled':''} onclick="XMEData.goPage('${type}',${page+1})"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>`;
    return`<div class="fme-pagination"><span class="fme-pag-info">Menampilkan ${from}–${to} dari ${total} tweets</span><div class="fme-pag-controls">${btns}</div></div>`;
  },

  goPage(type,page){Pag[type]=page;this._renderList(type);const el=document.getElementById('list-'+type);if(el)el.scrollIntoView({behavior:'smooth',block:'nearest'});},

  // ── BAR CHART (ApexCharts) ──
  _renderBar(type,items){
    hideSk('sk-'+type);
    if(!items.length)return;
    const color=CFG.colors[type];
    const labels=items.map(it=>{const n=this._getName(it);return n.length>16?n.slice(0,15)+'…':n;});
    const values=items.map(it=>this._metric(it,type));
    const metricLbl={view:'Views',rt:'Retweets',like:'Likes',reply:'Replies'}[type];
    const elId='bar'+type.charAt(0).toUpperCase()+type.slice(1).replace('rt','RT').replace('view','View').replace('like','Like').replace('reply','Reply');
    const realId={view:'barView',rt:'barRT',like:'barLike',reply:'barReply'}[type];

    if(Charts[realId]){try{Charts[realId].destroy();}catch(e){}}
    const el=document.getElementById(realId);if(!el)return;
    el.innerHTML='';

    Charts[realId]=new ApexCharts(el,{
      chart:{type:'bar',height:300,fontFamily:"'Poppins',sans-serif",toolbar:{show:false},animations:{enabled:true,speed:600}},
      series:[{name:metricLbl,data:values}],
      colors:[color],
      plotOptions:{bar:{borderRadius:8,columnWidth:'55%',distributed:false,dataLabels:{position:'top'}}},
      dataLabels:{enabled:true,formatter:v=>numK(v),offsetY:-20,style:{fontSize:'10px',fontWeight:'700',colors:['#64748b']}},
      xaxis:{categories:labels,labels:{style:{fontFamily:"'Poppins',sans-serif",fontSize:'10px',fontWeight:'600',colors:'#64748b'},rotate:-30,rotateAlways:labels.length>6},axisBorder:{show:false},axisTicks:{show:false}},
      yaxis:{labels:{style:{fontFamily:"'Poppins',sans-serif",fontSize:'10px',colors:'#94a3b8'},formatter:numK}},
      grid:{borderColor:'#f1f5f9',strokeDashArray:4},
      tooltip:{y:{formatter:v=>numFmt(v)+' '+metricLbl},style:{fontFamily:"'Poppins',sans-serif",fontSize:'12px'}},
      fill:{type:'gradient',gradient:{shade:'light',type:'vertical',shadeIntensity:0.25,gradientToColors:[color+'44'],inverseColors:false,opacityFrom:1,opacityTo:0.85}}
    });
    Charts[realId].render();
  },

  // ── ENGAGEMENT COMPARISON (ApexCharts) ──
  _renderEngChart(items){
    hideSk('sk-eng');
    if(!items.length)return;
    const top10=[...items].map(it=>({...it,_total:parseInt(it.view_cnt||0)+parseInt(it.rt||0)+parseInt(it.fav_count||0)+parseInt(it.reply_cnt||0)})).sort((a,b)=>b._total-a._total).slice(0,10);
    const labels=top10.map(it=>{const n=this._getName(it);return n.length>14?n.slice(0,13)+'…':n;});

    if(Charts.barEngagement){try{Charts.barEngagement.destroy();}catch(e){}}
    const el=document.getElementById('barEngagement');if(!el)return;
    el.innerHTML='';

    Charts.barEngagement=new ApexCharts(el,{
      chart:{type:'bar',height:320,stacked:true,fontFamily:"'Poppins',sans-serif",toolbar:{show:false},animations:{enabled:true,speed:600}},
      series:[
        {name:'Views',data:top10.map(i=>parseInt(i.view_cnt||0))},
        {name:'Retweets',data:top10.map(i=>parseInt(i.rt||0))},
        {name:'Likes',data:top10.map(i=>parseInt(i.fav_count||0))},
        {name:'Replies',data:top10.map(i=>parseInt(i.reply_cnt||0))}
      ],
      colors:['#1d9bf0','#10b981','#ef4444','#8b5cf6'],
      plotOptions:{bar:{borderRadius:4,columnWidth:'50%'}},
      dataLabels:{enabled:false},
      xaxis:{categories:labels,labels:{style:{fontFamily:"'Poppins',sans-serif",fontSize:'10px',fontWeight:'600',colors:'#64748b'},rotate:-30,rotateAlways:true},axisBorder:{show:false},axisTicks:{show:false}},
      yaxis:{labels:{style:{fontFamily:"'Poppins',sans-serif",fontSize:'10px',colors:'#94a3b8'},formatter:numK}},
      grid:{borderColor:'#f1f5f9',strokeDashArray:4},
      legend:{position:'top',fontFamily:"'Poppins',sans-serif",fontSize:'11px',fontWeight:600,labels:{colors:'#64748b'}},
      tooltip:{shared:true,intersect:false,y:{formatter:v=>numFmt(v)},style:{fontFamily:"'Poppins',sans-serif",fontSize:'12px'}}
    });
    Charts.barEngagement.render();
  },

  // ── DONUT CHART (ApexCharts) ──
  renderDonut(type){
    const items=this._sortItems(Store[type],type);
    const skel=document.getElementById('donutSkel');
    const chartEl=document.getElementById('donutChart');
    const emptyEl=document.getElementById('donutEmpty');
    const metricLbl={view:'Views',rt:'Retweets',like:'Likes',reply:'Replies'}[type];
    if(!items.length){if(skel)skel.style.display='none';if(emptyEl)emptyEl.style.display='block';return;}
    if(skel)skel.style.display='none';if(chartEl)chartEl.style.display='block';if(emptyEl)emptyEl.style.display='none';

    const top5=items.slice(0,5);
    const legendHTML=top5.map((it,i)=>{
      const n=this._getName(it);const sn=n.length>20?n.slice(0,19)+'…':n;const v=this._metric(it,type);
      return`<div class="ms-donut-leg-item"><span class="ms-donut-dot" style="background:${DONUT_COLORS[i]};"></span>${sn} · ${numFmt(v)}</div>`;
    }).join('');
    const masterLeg=document.getElementById('donutMasterLegend');if(masterLeg)masterLeg.innerHTML=legendHTML;

    if(Charts.donut){try{Charts.donut.destroy();}catch(e){}}
    chartEl.innerHTML='';

    Charts.donut=new ApexCharts(chartEl,{
      chart:{type:'donut',height:400,fontFamily:"'Poppins',sans-serif",animations:{enabled:true,speed:800,easing:'easeInOutQuad'},events:{
        dataPointSelection:function(e,ctx,cfg){
          const item=top5[cfg.dataPointIndex];if(item)XMEModal.open(item);
        }
      }},
      series:top5.map(it=>this._metric(it,type)),
      labels:top5.map(it=>{const n=this._getName(it);return n.length>25?n.slice(0,24)+'…':n;}),
      colors:DONUT_COLORS,
      plotOptions:{pie:{donut:{size:'55%',labels:{show:true,total:{show:true,label:'TOTAL '+metricLbl.toUpperCase(),fontSize:'10px',fontWeight:700,color:'#94a3b8',formatter:w=>numFmt(w.globals.seriesTotals.reduce((a,b)=>a+b,0))},value:{fontSize:'28px',fontWeight:800,color:'#0f172a',offsetY:4}},background:''}},expandOnClick:false},
      dataLabels:{enabled:true,formatter:(val,opts)=>val<2?'':val.toFixed(0)+'%',style:{fontFamily:"'Poppins',sans-serif",fontSize:'12px',fontWeight:'700'},dropShadow:{enabled:false}},
      legend:{position:'bottom',fontFamily:"'Poppins',sans-serif",fontSize:'12px',fontWeight:600,labels:{colors:'#64748b'},itemMargin:{horizontal:10,vertical:4}},
      stroke:{width:3,colors:['#fff']},
      tooltip:{y:{formatter:(v,opts)=>{const idx=opts.seriesIndex;const name=top5[idx]?this._getName(top5[idx]):'';const total=top5.reduce((s,it)=>s+this._metric(it,type),0);const pct=total>0?(v/total*100).toFixed(1):0;return`${numFmt(v)} ${metricLbl} (${pct}%)`;},title:{formatter:s=>s}},style:{fontFamily:"'Poppins',sans-serif",fontSize:'12px'}}
    });
    Charts.donut.render();
  },

  // ── EXPORT CSV ──
  exportCsv(type){
    const items=this._sortItems(Store[type],type);if(!items.length){alert('Tidak ada data.');return;}
    const metricLbl={view:'views',rt:'retweets',like:'likes',reply:'replies'}[type];
    const header='rank;author;username;sentiment;views;retweets;likes;replies;date;content';
    const rows=items.map((it,i)=>{
      const name=this._getName(it),scr=this._getScr(it);
      const sentRaw=String(it.sentiment_str||'').toLowerCase();
      const sent=sentRaw.includes('pos')?'Positif':sentRaw.includes('neg')?'Negatif':'Netral';
      return[i+1,`"${(name).replace(/"/g,'""')}"`,'@'+scr,sent,parseInt(it.view_cnt||0),parseInt(it.rt||0),parseInt(it.fav_count||0),parseInt(it.reply_cnt||0),(it.date_created||'').split('T')[0],`"${(it.content||'').replace(/"/g,'""').replace(/\n/g,' ')}"`].join(';');
    });
    const csv=header+'\n'+rows.join('\n');
    const blob=new Blob([csv],{type:'text/csv;charset=utf-8'});
    const a=document.createElement('a');a.href=URL.createObjectURL(blob);
    a.download=`x-most-${metricLbl}-${CFG.sd}-to-${CFG.ed}.csv`;a.click();URL.revokeObjectURL(a.href);
  }
};

/* ═══════════════════════════════════════════════════
   TWEET DETAIL MODAL
═══════════════════════════════════════════════════ */
const XMEModal = {
  open(item){
    const name=XMEData._getName(item),scr=XMEData._getScr(item),av=XMEData._getAvatar(item),ini=XMEData._getInitials(name);
    const content=(item.content||'').replace(/<[^>]*>/g,'').trim();
    const views=parseInt(item.view_cnt||0),rt=parseInt(item.rt||0),likes=parseInt(item.fav_count||0),replies=parseInt(item.reply_cnt||0);
    const sentRaw=String(item.sentiment_str||'').toLowerCase();
    const sent=sentRaw.includes('pos')?'pos':sentRaw.includes('neg')?'neg':'neu';
    const sentLbl=sent==='pos'?'Positive':sent==='neg'?'Negative':'Neutral';
    const sentCls=sent==='pos'?'background:#d1fae5;color:#065f46':sent==='neg'?'background:#fee2e2;color:#991b1b':'background:#f1f5f9;color:#64748b';
    const dt=(item.date_created||'').split('T')[0];
    const subId=item.sub_id||'';
    const tweetUrl=subId?`https://twitter.com/${scr}/status/${subId}`:`https://twitter.com/${scr}`;
    const avHtml=av&&av.startsWith('http')?`<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${esc(ini)}'">`:(ini);

    document.getElementById('tweetModalBody').innerHTML=`
      <div class="tdm-author-row">
        <div class="tdm-avatar">${avHtml}</div>
        <div><div class="tdm-author-name">${esc(name)}</div><div class="tdm-author-scr">@${esc(scr)}</div></div>
      </div>
      <div class="tdm-tweet-text">${esc(content)}</div>
      <div class="tdm-metrics">
        <div class="tdm-metric-box"><div class="tdm-metric-val" style="color:#1d9bf0;">${numFmt(views)}</div><div class="tdm-metric-lbl">Views</div></div>
        <div class="tdm-metric-box"><div class="tdm-metric-val" style="color:#10b981;">${numFmt(rt)}</div><div class="tdm-metric-lbl">Retweets</div></div>
        <div class="tdm-metric-box"><div class="tdm-metric-val" style="color:#ef4444;">${numFmt(likes)}</div><div class="tdm-metric-lbl">Likes</div></div>
        <div class="tdm-metric-box"><div class="tdm-metric-val" style="color:#8b5cf6;">${numFmt(replies)}</div><div class="tdm-metric-lbl">Replies</div></div>
      </div>
      <div style="margin-bottom:14px;"><span style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;${sentCls}">${sentLbl}</span></div>
      <div class="tdm-footer">
        <div class="tdm-date">${dt}</div>
        <a href="${esc(tweetUrl)}" target="_blank" rel="noopener" class="tdm-open-x">
          <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          Open on X
        </a>
      </div>`;
    document.getElementById('tweetModal').classList.add('show');
  },
  close(){document.getElementById('tweetModal').classList.remove('show');}
};
document.addEventListener('keydown',e=>{if(e.key==='Escape')XMEModal.close();});

/* ═══════════════════════════════════════════════════
   MAIN INIT
═══════════════════════════════════════════════════ */
const XME = {
  init(){XMEDp.init();XMEData.loadAll();},
  reload(){
    Store.view=[];Store.rt=[];Store.like=[];Store.reply=[];
    Pag.view=1;Pag.rt=1;Pag.like=1;Pag.reply=1;
    XMETab.reset();
    TABS.forEach(t=>{
      const el=document.getElementById('list-'+t);if(el)el.innerHTML=`<div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat ulang…</div>`;
      const pag=document.getElementById('pag-'+t);if(pag)pag.innerHTML='';
    });
    ['valViews','valRT','valLikes','valReplies'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div>';});
    document.getElementById('donutChart').style.display='none';document.getElementById('donutSkel').style.display='block';
    XMEData.loadAll();
  }
};
document.addEventListener('DOMContentLoaded',()=>XME.init());
</script>
@endsection
