@extends('mk.layouts.app')

@section('title', 'Most Retweets - X | SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
    --text-primary: #1a202c;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --bg-white: #ffffff;
    --bg-gray-50: #f8fafc;
    --bg-gray-100: #f1f5f9;
    --border-gray: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgba(0,0,0,.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,.1);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1);
  }

  .dashboard-container {
    padding: 24px;
    background: var(--bg-gray-50);
    min-height: 100vh;
    max-width: 1600px;
    margin: 0 auto;
  }

  .page-header { margin-bottom: 32px; }
  .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
  .page-header p  { font-size: 14px; color: var(--text-secondary); margin: 0; }

  /* ── Filter Card ── */
  .filter-card {
    background: var(--bg-white); border-radius: 16px;
    padding: 20px 24px; margin-bottom: 24px;
    box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray);
  }
  .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
  .filter-label   { font-size: 14px; font-weight: 600; color: var(--text-primary); white-space: nowrap; }
  .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }

  .apply-btn {
    padding: 12px 28px;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white; border: none; border-radius: 12px;
    font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.3s;
    display: flex; align-items: center; gap: 8px;
    box-shadow: 0 4px 12px rgba(3,128,71,.2);
  }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.3); }
  .apply-btn svg  { width: 18px; height: 18px; }

  .date-picker-trigger {
    display: flex; align-items: center; gap: 12px; padding: 12px 20px;
    background: var(--bg-gray-50); border: 1px solid var(--border-gray);
    border-radius: 12px; font-family: 'Poppins', sans-serif;
    font-size: 14px; font-weight: 500; color: var(--text-primary);
    cursor: pointer; transition: all 0.2s; width: 100%; max-width: 400px;
  }
  .date-picker-trigger:hover { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(3,128,71,.1); }
  .date-picker-trigger svg:first-child { width:18px;height:18px;color:var(--text-secondary);flex-shrink:0; }
  .date-picker-trigger span            { flex:1;text-align:left; }
  .date-picker-trigger svg:last-child  { width:16px;height:16px;margin-left:auto;color:var(--text-secondary); }

  /* ── Date Picker Modal ── */
  .date-picker-modal {
    position:fixed;top:0;left:0;right:0;bottom:0;z-index:10000;
    display:none;align-items:center;justify-content:center;
    background:rgba(0,0,0,.5);backdrop-filter:blur(8px);
  }
  .date-picker-modal.show { display:flex; }
  .date-picker-overlay    { position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);cursor:pointer; }

  .date-picker-container {
    position:relative;background:#fff;border-radius:16px;
    box-shadow:0 25px 50px rgba(0,0,0,.3);display:flex;
    max-width:900px;width:90%;max-height:90vh;z-index:10001;
    animation:slideUp .3s ease-out;
  }
  @keyframes slideUp {
    from{opacity:0;transform:translateY(20px) scale(.95)}
    to  {opacity:1;transform:translateY(0)   scale(1)}
  }

  .date-picker-sidebar {
    width:180px;background:var(--bg-gray-50);border-right:1px solid var(--border-gray);
    padding:16px 12px;border-radius:16px 0 0 16px;
    display:flex;flex-direction:column;gap:4px;flex-shrink:0;
  }
  .date-preset {
    padding:10px 16px;background:transparent;border:none;border-radius:8px;
    font-family:'Poppins',sans-serif;font-size:13px;font-weight:500;
    color:var(--text-primary);text-align:left;cursor:pointer;transition:all .2s;
  }
  .date-preset:hover  { background:var(--bg-white);color:var(--primary-green); }
  .date-preset.active { background:var(--primary-green);color:white; }

  .date-picker-content  { flex:1;padding:24px;display:flex;flex-direction:column;overflow:hidden; }
  .date-picker-header   { display:flex;align-items:flex-start;gap:20px;margin-bottom:20px; }

  .nav-btn {
    width:36px;height:36px;border-radius:8px;background:var(--bg-gray-50);
    border:1px solid var(--border-gray);display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:all .2s;flex-shrink:0;
  }
  .nav-btn:hover { background:var(--primary-green);border-color:var(--primary-green);color:white; }
  .nav-btn svg   { width:20px;height:20px; }

  .calendars-wrapper { display:flex;gap:24px;flex:1;min-height:0; }
  .calendar { flex:1;display:flex;flex-direction:column;min-width:0; }
  .calendar-month   { font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:16px;text-align:center; }
  .calendar-weekdays{ display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:8px; }
  .weekday          { text-align:center;font-size:11px;font-weight:700;color:var(--text-secondary);padding:8px 0; }
  .calendar-days    { display:grid;grid-template-columns:repeat(7,1fr);gap:4px; }

  .calendar-day {
    aspect-ratio:1;display:flex;align-items:center;justify-content:center;
    font-size:13px;font-weight:500;border-radius:8px;cursor:pointer;
    transition:all .2s;color:var(--text-primary);background:transparent;border:none;padding:0;
  }
  .calendar-day:hover:not(.disabled):not(.other-month){ background:var(--bg-gray-100); }
  .calendar-day.other-month { color:#cbd5e1;cursor:default; }
  .calendar-day.disabled    { color:#e2e8f0;cursor:not-allowed; }
  .calendar-day.today       { border:2px solid var(--primary-green); }
  .calendar-day.selected,.calendar-day.range-start,.calendar-day.range-end { background:var(--primary-green);color:white; }
  .calendar-day.in-range    { background:rgba(3,128,71,.1);color:var(--primary-green); }

  .date-picker-display {
    padding:16px 20px;background:var(--bg-gray-50);border-radius:12px;
    text-align:center;margin-bottom:20px;border:1px solid var(--border-gray);
  }
  .date-picker-display span { font-size:14px;font-weight:600;color:var(--text-primary); }
  .date-picker-footer { display:flex;gap:12px;justify-content:flex-end; }

  .cancel-btn,.apply-date-btn {
    padding:10px 24px;border-radius:10px;font-family:'Poppins',sans-serif;
    font-size:14px;font-weight:600;cursor:pointer;transition:all .2s;border:none;
  }
  .cancel-btn      { background:var(--bg-gray-100);color:var(--text-primary); }
  .cancel-btn:hover{ background:var(--border-gray); }
  .apply-date-btn  { background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark));color:white;box-shadow:0 4px 12px rgba(3,128,71,.2); }
  .apply-date-btn:hover{ transform:translateY(-2px);box-shadow:0 6px 20px rgba(3,128,71,.3); }

  /* ── Stats Grid ── */
  .stats-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(280px,1fr));
    gap: 20px; margin-bottom: 24px;
  }
  .stat-card {
    background:var(--bg-white);border-radius:16px;padding:24px;
    box-shadow:var(--shadow-sm);border:1px solid var(--border-gray);
    transition:all .3s cubic-bezier(.4,0,.2,1);position:relative;overflow:hidden;
  }
  .stat-card::before {
    content:'';position:absolute;top:0;left:0;right:0;height:4px;
    background:linear-gradient(90deg,var(--primary-green),var(--primary-green-dark));
    opacity:0;transition:opacity .3s;
  }
  .stat-card:hover{ transform:translateY(-4px);box-shadow:var(--shadow-lg);border-color:var(--primary-green); }
  .stat-card:hover::before{ opacity:1; }

  .stat-header { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px; }
  .stat-icon-wrapper {
    width:56px;height:56px;border-radius:14px;
    background:linear-gradient(135deg,rgba(3,128,71,.1),rgba(3,128,71,.05));
    display:flex;align-items:center;justify-content:center;
  }
  .stat-icon  { width:28px;height:28px;color:var(--primary-green); }
  .stat-label { font-size:13px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px; }
  .stat-value { font-size:36px;font-weight:700;color:var(--text-primary);line-height:1; }
  .stat-value-wrapper { display:flex;align-items:baseline;gap:12px;margin-bottom:16px; }
  .stat-progress     { height:6px;background:var(--bg-gray-100);border-radius:10px;overflow:hidden;margin-top:8px; }
  .stat-progress-bar { height:100%;background:linear-gradient(90deg,var(--primary-green),var(--primary-green-dark));border-radius:10px;transition:width 1s ease-out; }

  /* ═══════════════════════════════════════════
     DRONE EMPRIT STYLE — VIRAL TWEETS DONUT
  ═══════════════════════════════════════════ */
  .viral-donut-section {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
    margin-bottom: 24px;
    transition: all .3s;
  }
  .viral-donut-section:hover { box-shadow: var(--shadow-md); }

  .viral-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    margin-bottom: 24px; padding-bottom: 20px;
    border-bottom: 2px solid var(--bg-gray-50);
    flex-wrap: wrap; gap: 12px;
  }
  .viral-title-group h3 { font-size:18px;font-weight:700;color:var(--text-primary);margin:0 0 6px 0; }
  .viral-subtitle       { font-size:13px;color:var(--text-secondary);font-weight:500; }

  .viral-legend {
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  }
  .viral-legend-item {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600; color: var(--text-secondary);
    padding: 4px 10px; background: var(--bg-gray-50);
    border-radius: 20px; border: 1px solid var(--border-gray);
    cursor: pointer; transition: all .2s;
  }
  .viral-legend-item:hover { border-color: var(--primary-green); background: var(--bg-white); }
  .viral-legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

  #viralDonutChart {
    width: 100%;
    height: 620px;
  }

  /* Loading skeleton */
  .loading-skeleton {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%; animation: loading 1.5s ease-in-out infinite; border-radius: 8px;
  }
  @keyframes loading { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
  .skeleton-text { height: 44px; margin-bottom: 8px; }

  /* ── Table Section ── */
  .table-section {
    background:var(--bg-white);border-radius:16px;padding:28px;
    box-shadow:var(--shadow-sm);border:1px solid var(--border-gray);margin-bottom:24px;
  }
  .table-header {
    display:flex;justify-content:space-between;align-items:center;
    margin-bottom:24px;padding-bottom:20px;border-bottom:2px solid var(--bg-gray-50);
    gap:16px;flex-wrap:wrap;
  }
  .table-title h3   { font-size:18px;font-weight:700;color:var(--text-primary);margin:0 0 6px 0; }
  .table-subtitle   { font-size:13px;color:var(--text-secondary); }
  .table-actions    { display:flex;align-items:center;gap:12px; }
  .table-search     { position:relative;width:280px; }
  .table-search input {
    width:100%;padding:10px 16px 10px 44px;border:1px solid var(--border-gray);
    border-radius:12px;font-family:'Poppins',sans-serif;font-size:14px;
    background:var(--bg-gray-50);transition:all .2s;color:var(--text-primary);
  }
  .table-search input:focus { outline:none;border-color:var(--primary-green);background:var(--bg-white);box-shadow:0 0 0 3px rgba(3,128,71,.1); }
  .table-search svg { position:absolute;left:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:var(--text-secondary); }

  .actions-dropdown { position:relative; }
  .actions-dropdown-btn {
    display:flex;align-items:center;gap:8px;padding:10px 16px;
    background:var(--bg-white);border:1px solid var(--border-gray);
    border-radius:10px;font-family:'Poppins',sans-serif;font-size:13px;font-weight:500;
    color:var(--text-primary);cursor:pointer;transition:all .2s;
  }
  .actions-dropdown-btn:hover { background:var(--bg-gray-50);border-color:var(--primary-green); }
  .actions-dropdown-btn svg   { width:16px;height:16px;color:var(--text-secondary); }
  .actions-dropdown-menu {
    position:absolute;top:calc(100% + 8px);right:0;background:var(--bg-white);
    border:1px solid var(--border-gray);border-radius:12px;
    box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:220px;padding:8px;
    z-index:1000;display:none;
  }
  .actions-dropdown-menu.show { display:block;animation:dropdownSlideIn .2s ease-out; }
  @keyframes dropdownSlideIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
  .actions-dropdown-item {
    display:flex;align-items:center;gap:12px;padding:10px 12px;
    border-radius:8px;font-size:13px;font-weight:500;color:var(--text-primary);
    cursor:pointer;transition:all .15s;text-decoration:none;
  }
  .actions-dropdown-item:hover { background:var(--bg-gray-50);color:var(--primary-green); }
  .actions-dropdown-item svg   { width:18px;height:18px;color:var(--text-secondary); }
  .actions-dropdown-item:hover svg{ color:var(--primary-green); }
  .actions-dropdown-divider { height:1px;background:var(--border-gray);margin:6px 0; }

  /* ── Data Table ── */
  .data-table { width:100%;border-collapse:separate;border-spacing:0;font-size:12px; }
  .data-table thead tr { background:var(--bg-white);border-bottom:1px solid var(--border-gray); }
  .data-table th {
    padding:10px 12px;text-align:left;font-size:10px;font-weight:700;
    color:var(--text-secondary);text-transform:uppercase;letter-spacing:.3px;
    border-bottom:1px solid var(--border-gray);white-space:nowrap;
  }
  .data-table th:first-child{ padding-left:20px; }
  .data-table th:last-child { padding-right:20px; }
  .data-table td { padding:12px;font-size:12px;color:var(--text-primary);border-bottom:1px solid #f1f5f9;vertical-align:middle; }
  .data-table td:first-child{ padding-left:20px; }
  .data-table td:last-child { padding-right:20px; }
  .data-table tbody tr      { transition:all .2s;background:var(--bg-white); }
  .data-table tbody tr:hover{ background:#fafbfc; }
  .data-table tbody tr:last-child td{ border-bottom:none; }

  .avatar-container { position:relative;display:inline-block; }
  .user-avatar-img  { width:36px;height:36px;border-radius:50%;object-fit:cover;display:block; }
  .user-avatar-fallback {
    width:36px;height:36px;border-radius:50%;
    background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark));
    display:flex;align-items:center;justify-content:center;
    color:white;font-weight:700;font-size:13px;
  }

  .username-link     { color:#ea580c;text-decoration:none;font-weight:500;transition:all .2s;cursor:pointer; }
  .username-link:hover { color:var(--primary-green);text-decoration:underline; }
  .account-name-link { color:var(--text-primary);text-decoration:none;font-weight:600;transition:all .2s; }
  .account-name-link:hover{ color:var(--primary-green);text-decoration:underline; }

  .tweet-text-cell {
    max-width:320px;font-size:12px;color:var(--text-secondary);line-height:1.5;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
    overflow:hidden;cursor:pointer;transition:color .2s;
  }
  .tweet-text-cell:hover{ color:var(--primary-green); }

  .sentiment-badge {
    padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
    display:inline-flex;align-items:center;gap:5px;white-space:nowrap;
  }
  .sentiment-dot { width:6px;height:6px;border-radius:50%;flex-shrink:0; }

  .retweet-count  { display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:700;color:var(--text-primary); }
  .retweet-count svg{ width:14px;height:14px;color:var(--primary-green); }

  .view-tweet-btn {
    display:inline-flex;align-items:center;gap:5px;padding:6px 12px;
    background:#000;color:#fff;border:none;border-radius:8px;
    font-family:'Poppins',sans-serif;font-size:11px;font-weight:600;
    cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;
  }
  .view-tweet-btn:hover { background:#1d1d1d;transform:translateY(-1px); }
  .view-tweet-btn svg   { width:11px;height:11px;fill:white; }

  /* ── Pagination ── */
  .pagination {
    display:flex;justify-content:center;align-items:center;gap:8px;
    padding:20px;background:var(--bg-white);border-top:1px solid var(--border-gray);margin-top:16px;
  }
  .page-btn {
    width:36px;height:36px;border-radius:10px;border:1px solid var(--border-gray);
    background:var(--bg-white);color:var(--text-primary);font-size:13px;font-weight:600;
    cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;
    font-family:'Poppins',sans-serif;
  }
  .page-btn:hover:not(:disabled){ border-color:var(--primary-green);color:var(--primary-green);background:rgba(3,128,71,.05); }
  .page-btn.active  { background:var(--primary-green);color:white;border-color:var(--primary-green); }
  .page-btn:disabled{ opacity:.4;cursor:not-allowed; }
  .pagination-info  { font-size:13px;color:var(--text-secondary);font-weight:500; }

  /* ── Lazy Loading ── */
  .lazy-loading-badge {
    position:absolute;top:16px;right:16px;display:inline-flex;align-items:center;gap:8px;
    padding:6px 12px;background:rgba(3,128,71,.1);color:var(--primary-green);
    border-radius:20px;font-size:12px;font-weight:600;z-index:10;
  }
  .spinner { width:14px;height:14px;border:2px solid rgba(3,128,71,.2);border-top-color:var(--primary-green);border-radius:50%;animation:spin .8s linear infinite; }
  @keyframes spin { to{transform:rotate(360deg)} }

  [data-lazy-load] { opacity:0;transform:translateY(20px);transition:opacity .6s ease-out,transform .6s ease-out; }
  [data-lazy-load].loaded{ opacity:1;transform:translateY(0); }
  .data-loaded { animation:fadeIn .4s ease-out; }
  @keyframes fadeIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }

  /* ── Alert ── */
  .alert { padding:16px 20px;border-radius:12px;margin-bottom:24px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:12px; }
  .alert-warning{ background:#fef3c7;color:#92400e;border:1px solid #fcd34d; }

  /* ── Tweet Detail Modal ── */
  .tweet-detail-modal {
    position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;
    display:none;align-items:center;justify-content:center;
    background:rgba(0,0,0,.75);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
  }
  .tweet-detail-modal.show{ display:flex; }
  .modal-overlay { position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.8); }
  .modal-content {
    position:relative;background:#fff;border-radius:16px;
    box-shadow:0 25px 50px rgba(0,0,0,.5);width:90%;max-width:560px;
    max-height:85vh;display:flex;flex-direction:column;
    animation:modalSlideIn .3s ease-out;z-index:10001;
  }
  @keyframes modalSlideIn { from{transform:translateY(-20px) scale(.95);opacity:0} to{transform:translateY(0) scale(1);opacity:1} }

  .modal-header {
    display:flex;justify-content:space-between;align-items:center;
    padding:20px 24px;border-bottom:1px solid #e2e8f0;
    background:#fff;border-radius:16px 16px 0 0;
  }
  .modal-header h3 { font-size:20px;font-weight:700;color:#1a202c;margin:0;display:flex;align-items:center;gap:10px; }
  .modal-header h3 .x-icon-sm { width:28px;height:28px;background:#000;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0; }
  .modal-header h3 .x-icon-sm svg{ width:15px;height:15px;fill:#fff; }
  .modal-close { width:36px;height:36px;border-radius:10px;background:var(--bg-gray-50);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;color:var(--text-secondary); }
  .modal-close:hover{ background:#ef4444;color:white; }
  .modal-body { padding:28px;overflow-y:auto;background:#fff;border-radius:0 0 16px 16px; }

  .modal-author-row    { display:flex;align-items:center;gap:14px;margin-bottom:18px; }
  .modal-author-avatar { width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0; }
  .modal-author-avatar-fallback {
    width:48px;height:48px;border-radius:50%;
    background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark));
    display:flex;align-items:center;justify-content:center;color:white;font-size:18px;font-weight:700;flex-shrink:0;
  }
  .modal-author-name { font-size:15px;font-weight:700;color:var(--text-primary);line-height:1.2; }
  .modal-author-scr  { font-size:13px;color:var(--text-secondary); }
  .modal-tweet-text  {
    font-size:15px;line-height:1.65;color:var(--text-primary);margin-bottom:20px;word-break:break-word;
    padding:16px;background:var(--bg-gray-50);border-radius:12px;border:1px solid var(--border-gray);
  }
  .modal-meta-row  { display:flex;align-items:center;gap:20px;margin-bottom:20px;flex-wrap:wrap; }
  .modal-meta-item { display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-secondary); }
  .modal-meta-item strong{ color:var(--text-primary);font-weight:700;font-size:14px; }
  .modal-meta-item svg  { width:15px;height:15px; }
  .modal-footer { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding-top:16px;border-top:1px solid var(--border-gray); }
  .modal-date   { font-size:12px;color:var(--text-secondary); }
  .modal-open-twitter {
    display:inline-flex;align-items:center;gap:8px;padding:9px 18px;
    background:#000;color:#fff;border-radius:10px;font-family:'Poppins',sans-serif;
    font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;
  }
  .modal-open-twitter:hover{ background:#1d1d1d;transform:translateY(-1px); }
  .modal-open-twitter svg  { width:13px;height:13px;fill:white; }

  /* ── Responsive ── */
  @media(max-width:768px){
    .dashboard-container{ padding:16px; }
    .stats-grid{ grid-template-columns:1fr; }
    .filter-content{ flex-direction:column;align-items:stretch; }
    .date-range-wrapper{ flex-direction:column; }
    .apply-btn{ width:100%;justify-content:center; }
    .date-picker-container{ flex-direction:column;max-height:85vh;overflow-y:auto;width:95%; }
    .date-picker-sidebar{ width:100%;border-right:none;border-bottom:1px solid var(--border-gray);border-radius:16px 16px 0 0;flex-direction:row;overflow-x:auto;padding:12px 16px; }
    .date-preset{ white-space:nowrap; }
    .date-picker-content{ padding:20px 16px; }
    .calendars-wrapper{ flex-direction:column;gap:16px; }
    .date-picker-trigger{ max-width:100%; }
    .cancel-btn,.apply-date-btn{ flex:1; }
    #viralDonutChart{ height:420px; }
    .data-table{ min-width:900px; }
    .stat-value{ font-size:28px; }
    .table-search{ width:100%; }
    .page-header h1{ font-size:24px; }
    .modal-content{ width:95%;max-height:90vh; }
    .modal-header,.modal-body{ padding:20px; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>Most Retweets</h1>
    <p>Top tweets with the highest retweet count on X (Twitter) in the selected date range</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view Most Retweets data.</span>
  </div>
  @else

  <!-- Date Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.most-retweets') }}">
      <input type="hidden" name="project_id"  value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hiddenEndDate"   value="{{ $endDate }}">

      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>Date Range
        </div>
        <div class="date-range-wrapper">
          <button type="button" class="date-picker-trigger" id="datePickerTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="dateRangeDisplay">{{ $startDate }} to {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <button type="submit" class="apply-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          Apply Filter
        </button>
      </div>
    </form>
  </div>

  <!-- Date Range Picker Modal -->
  <div class="date-picker-modal" id="datePickerModal">
    <div class="date-picker-overlay"></div>
    <div class="date-picker-container">
      <div class="date-picker-sidebar">
        <button type="button" class="date-preset" data-preset="today">Today</button>
        <button type="button" class="date-preset" data-preset="yesterday">Yesterday</button>
        <button type="button" class="date-preset" data-preset="last7days">Last 7 Days</button>
        <button type="button" class="date-preset" data-preset="last30days">Last 30 Days</button>
        <button type="button" class="date-preset" data-preset="thismonth">This Month</button>
        <button type="button" class="date-preset" data-preset="lastmonth">Last Month</button>
        <button type="button" class="date-preset active" data-preset="custom">Custom Range</button>
      </div>
      <div class="date-picker-content">
        <div class="date-picker-header">
          <button type="button" class="nav-btn" id="prevMonth">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="calendar1"></div>
            <div class="calendar" id="calendar2"></div>
          </div>
          <button type="button" class="nav-btn" id="nextMonth">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="date-picker-display"><span id="selectedRangeText">{{ $startDate }} to {{ $endDate }}</span></div>
        <div class="date-picker-footer">
          <button type="button" class="cancel-btn">Cancel</button>
          <button type="button" class="apply-date-btn" id="applyDatePicker">Apply</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card" data-lazy-load="retweetStats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Total Tweets</div>
      <div id="statTotalTweets" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:0%"></div></div>
    </div>

    <div class="stat-card" data-lazy-load="retweetStats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Unique Authors</div>
      <div id="statUniqueAuthors" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:0%"></div></div>
    </div>

    <div class="stat-card" data-lazy-load="retweetStats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Highest Retweets</div>
      <div id="statHighestRT" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:0%"></div></div>
    </div>
  </div>

  <!-- Viral Tweets Donut Chart -->
  <div class="viral-donut-section" data-lazy-load="viralDonut">
    <div class="viral-header">
      <div class="viral-title-group">
        <h3>Top 5 Most Retweeted Tweets</h3>
        <p class="viral-subtitle">Viral tweets ranked by retweet count — hover each segment for details</p>
      </div>
      <div class="viral-legend" id="viralLegend"></div>
    </div>

    <div id="viralDonutLoading" class="loading-skeleton" style="height:620px;border-radius:12px;"></div>
    <div id="viralDonutChart" style="display:none;"></div>

    <div id="viralDonutEmpty" style="display:none;text-align:center;padding:60px 20px;color:var(--text-secondary);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 16px;opacity:.4;display:block;">
        <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
      </svg>
      <p style="font-size:15px;font-weight:500;">No viral tweet data found for the selected date range.</p>
    </div>
  </div>

  <!-- Retweets Table -->
  <div class="table-section" data-lazy-load="retweetsTable">
    <div class="table-header">
      <div class="table-title">
        <h3>Viral Tweets Ranking</h3>
        <p class="table-subtitle">Sorted by retweet count — click tweet content to view detail</p>
      </div>
      <div class="table-actions">
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="searchInput" placeholder="Search tweets or authors..." onkeyup="filterTable()">
        </div>
        <div class="actions-dropdown">
          <button class="actions-dropdown-btn" onclick="toggleActionsDropdown(event)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
            Actions
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="actions-dropdown-menu" id="actionsDropdownMenu">
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault();exportCSV()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              Export to CSV
            </a>
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault();refreshData()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Refresh Data
            </a>
            <div class="actions-dropdown-divider"></div>
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault();printTable()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
              Print Table
            </a>
          </div>
        </div>
      </div>
    </div>

    <div id="tableLoading" class="loading-skeleton" style="height:400px;"></div>
    <div id="tableWrapper" style="display:none;overflow-x:auto;"></div>
    <div id="paginationWrapper" class="pagination" style="display:none;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;"></div>
    <div id="emptyState" style="display:none;text-align:center;padding:60px 20px;color:var(--text-secondary);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 16px;opacity:.4;display:block;">
        <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
      </svg>
      <p style="font-size:15px;font-weight:500;">No retweet data found for the selected date range.</p>
    </div>
  </div>

  @endif
</div>

<!-- Tweet Detail Modal -->
<div class="tweet-detail-modal" id="tweetDetailModal">
  <div class="modal-overlay" onclick="closeTweetModal()"></div>
  <div class="modal-content">
    <div class="modal-header">
      <h3>
        <span class="x-icon-sm"><svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></span>
        Tweet Detail
      </h3>
      <button class="modal-close" onclick="closeTweetModal()">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body" id="tweetModalBody"></div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
// ========================================
// DATE PICKER
// ========================================
(function() {
  'use strict';
  let selectedStartDate=null, selectedEndDate=null;
  let currentMonth1=new Date(), currentMonth2=new Date();
  let selectingStart=true;

  document.addEventListener('DOMContentLoaded', function() {
    const s=document.getElementById('hiddenStartDate'), e=document.getElementById('hiddenEndDate');
    if(s?.value) selectedStartDate=new Date(s.value); else { selectedEndDate=new Date(); selectedStartDate=new Date(); selectedStartDate.setDate(selectedStartDate.getDate()-6); }
    if(e?.value) selectedEndDate=new Date(e.value);
    currentMonth1=new Date(selectedStartDate);
    currentMonth2=new Date(selectedStartDate); currentMonth2.setMonth(currentMonth2.getMonth()+1);
    renderCalendars(); setupEventListeners();
  });

  function setupEventListeners(){
    document.getElementById('datePickerTrigger')?.addEventListener('click', openDatePicker);
    document.querySelector('.date-picker-overlay')?.addEventListener('click', closeDatePicker);
    document.addEventListener('keydown',e=>{ if(e.key==='Escape') document.getElementById('datePickerModal')?.classList.contains('show') && closeDatePicker(); });
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click',handlePresetClick));
    document.getElementById('prevMonth')?.addEventListener('click',()=>{ currentMonth1.setMonth(currentMonth1.getMonth()-1); currentMonth2.setMonth(currentMonth2.getMonth()-1); renderCalendars(); });
    document.getElementById('nextMonth')?.addEventListener('click',()=>{ currentMonth1.setMonth(currentMonth1.getMonth()+1); currentMonth2.setMonth(currentMonth2.getMonth()+1); renderCalendars(); });
    document.getElementById('applyDatePicker')?.addEventListener('click', applyDateSelection);
    document.querySelector('.cancel-btn')?.addEventListener('click', closeDatePicker);
  }

  function openDatePicker(){ document.getElementById('datePickerModal').classList.add('show'); renderCalendars(); }
  function closeDatePicker(){ document.getElementById('datePickerModal').classList.remove('show'); }

  function handlePresetClick(e){
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active')); e.target.classList.add('active');
    const preset=e.target.dataset.preset, today=new Date(); today.setHours(0,0,0,0);
    switch(preset){
      case 'today':      selectedStartDate=new Date(today); selectedEndDate=new Date(today); break;
      case 'yesterday':  selectedStartDate=new Date(today); selectedStartDate.setDate(today.getDate()-1); selectedEndDate=new Date(selectedStartDate); break;
      case 'last7days':  selectedEndDate=new Date(today); selectedStartDate=new Date(today); selectedStartDate.setDate(today.getDate()-6); break;
      case 'last30days': selectedEndDate=new Date(today); selectedStartDate=new Date(today); selectedStartDate.setDate(today.getDate()-29); break;
      case 'thismonth':  selectedStartDate=new Date(today.getFullYear(),today.getMonth(),1); selectedEndDate=new Date(today); break;
      case 'lastmonth':  selectedStartDate=new Date(today.getFullYear(),today.getMonth()-1,1); selectedEndDate=new Date(today.getFullYear(),today.getMonth(),0); break;
    }
    if(preset!=='custom'){ currentMonth1=new Date(selectedStartDate); currentMonth2=new Date(selectedStartDate); currentMonth2.setMonth(currentMonth2.getMonth()+1); updateDateDisplay(); renderCalendars(); }
  }

  function applyDateSelection(){
    const start=formatDate(selectedStartDate), end=formatDate(selectedEndDate);
    document.getElementById('hiddenStartDate').value=start;
    document.getElementById('hiddenEndDate').value=end;
    const disp=document.getElementById('dateRangeDisplay'); if(disp) disp.textContent=`${start} to ${end}`;
    closeDatePicker();
  }

  function renderCalendars(){ renderCalendar('calendar1',currentMonth1); renderCalendar('calendar2',currentMonth2); updateDateDisplay(); }

  function renderCalendar(elementId, month){
    const cal=document.getElementById(elementId); if(!cal) return;
    const year=month.getFullYear(), monthNum=month.getMonth();
    const firstDay=new Date(year,monthNum,1), lastDay=new Date(year,monthNum+1,0);
    const prevLastDay=new Date(year,monthNum,0);
    const monthNames=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const weekdays=['Su','Mo','Tu','We','Th','Fr','Sa'];
    let html=`<div class="calendar-month">${monthNames[monthNum]} ${year}</div><div class="calendar-weekdays">${weekdays.map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
    const today=new Date(); today.setHours(0,0,0,0);
    const firstDayOfWeek=firstDay.getDay();
    for(let i=0;i<firstDayOfWeek;i++) html+=`<button type="button" class="calendar-day other-month" disabled>${prevLastDay.getDate()-(firstDayOfWeek-1-i)}</button>`;
    for(let day=1;day<=lastDay.getDate();day++){
      const date=new Date(year,monthNum,day); date.setHours(0,0,0,0);
      const dateStr=formatDate(date); let classes='calendar-day';
      if(isSameDay(date,today)) classes+=' today';
      if(date>today) classes+=' disabled';
      if(selectedStartDate&&selectedEndDate){
        if(isSameDay(date,selectedStartDate)) classes+=' selected range-start';
        else if(isSameDay(date,selectedEndDate)) classes+=' selected range-end';
        else if(date>selectedStartDate&&date<selectedEndDate) classes+=' in-range';
      }
      html+=`<button type="button" class="${classes}" data-date="${dateStr}" ${date>today?'disabled':''}>${day}</button>`;
    }
    const lastDayOfWeek=lastDay.getDay(), rem=lastDayOfWeek===6?0:6-lastDayOfWeek;
    for(let i=1;i<=rem;i++) html+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    html+='</div>';
    cal.innerHTML=html;
    cal.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(b=>b.addEventListener('click',handleDateClick));
  }

  function handleDateClick(e){
    const date=new Date(e.target.dataset.date); date.setHours(0,0,0,0);
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
    document.querySelector('[data-preset="custom"]')?.classList.add('active');
    if(selectingStart||date<selectedStartDate){ selectedStartDate=date; selectedEndDate=date; selectingStart=false; }
    else{ if(date>=selectedStartDate) selectedEndDate=date; else{ selectedEndDate=selectedStartDate; selectedStartDate=date; } selectingStart=true; }
    updateDateDisplay(); renderCalendars();
  }

  function updateDateDisplay(){
    if(!selectedStartDate||!selectedEndDate) return;
    const el=document.getElementById('selectedRangeText');
    if(el) el.textContent=`${formatDate(selectedStartDate)} to ${formatDate(selectedEndDate)}`;
  }

  function formatDate(date){ if(!date) return ''; return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`; }
  function isSameDay(d1,d2){ return d1&&d2&&d1.getFullYear()===d2.getFullYear()&&d1.getMonth()===d2.getMonth()&&d1.getDate()===d2.getDate(); }
})();

// ========================================
// MOST RETWEETS — MAIN LOGIC
// ========================================
const projectId = '{{ $projectId ?? "" }}';
const startDate = '{{ $startDate ?? "" }}';
const endDate   = '{{ $endDate ?? "" }}';

let allData    = [];
let currentPage = 1;
const tweetsPerPage = 20;

let viralChart = null;

const SEGMENT_COLORS = [
  '#2FC6F6',
  '#f59e0b',
  '#10b981',
  '#8b5cf6',
  '#f43f5e',
];

function formatNumber(n){ if(!n&&n!==0) return '0'; return new Intl.NumberFormat('en-US').format(n); }
function formatDateStr(dateStr){ if(!dateStr) return '—'; try{ return new Date(dateStr).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'}); }catch(e){ return dateStr; } }

function getSentimentStyle(sentimentStr){
  const s=(sentimentStr||'').toLowerCase();
  if(s==='positive') return{bg:'rgba(16,185,129,.12)',color:'#059669',dot:'#10b981'};
  if(s==='negative') return{bg:'rgba(239,68,68,.12)',color:'#dc2626',dot:'#ef4444'};
  return{bg:'rgba(100,116,139,.12)',color:'#475569',dot:'#64748b'};
}

function getInitials(name){
  if(!name||name==='Unknown') return '?';
  const parts=name.trim().split(/\s+/);
  return parts.length===1?parts[0].substring(0,2).toUpperCase():(parts[0][0]+parts[parts.length-1][0]).toUpperCase();
}

function truncateText(text, maxLen){ return text&&text.length>maxLen?text.slice(0,maxLen)+'…':text||''; }

// ── Lazy Load ──
const lazyLoadConfig  = {rootMargin:'50px',threshold:.01};
const loadedComponents = new Set();

const lazyLoadObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if(entry.isIntersecting){
      const id=entry.target.dataset.lazyLoad;
      if(!loadedComponents.has(id)){
        loadedComponents.add(id);
        if(id==='retweetStats'||id==='retweetsTable') loadData();
        if(id==='viralDonut') loadViralDonut();
        lazyLoadObserver.unobserve(entry.target);
      }
    }
  });
}, lazyLoadConfig);

document.addEventListener('DOMContentLoaded', () => {
  if(projectId&&startDate&&endDate)
    document.querySelectorAll('[data-lazy-load]').forEach(el=>lazyLoadObserver.observe(el));
});

function addLoadingBadge(card){
  if(!card||card.querySelector('.lazy-loading-badge')) return;
  const badge=document.createElement('div'); badge.className='lazy-loading-badge';
  badge.innerHTML='<div class="spinner"></div><span>Loading...</span>';
  card.style.position='relative'; card.appendChild(badge);
}
function removeLoadingBadge(card){
  if(!card) return;
  const badge=card.querySelector('.lazy-loading-badge');
  if(badge){ badge.style.opacity='0'; setTimeout(()=>badge.remove(),300); }
}
function animateProgress(card, pct){
  const bar=card.querySelector('.stat-progress-bar');
  if(bar) setTimeout(()=>bar.style.width=Math.min(pct,100)+'%',100);
}

let dataLoaded=false;

async function loadData(){
  if(dataLoaded) return; dataLoaded=true;
  const statCards=document.querySelectorAll('[data-lazy-load="retweetStats"]');
  statCards.forEach(c=>addLoadingBadge(c));
  try{
    const res=await fetch(`/mk/api/x/most-retweets?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
    const result=await res.json();
    if(result.success&&result.data&&result.data.length>0){
      allData=result.data;
      const uniqueAuthors=new Set(allData.map(d=>d.author?.scr_name||d.name)).size;
      const highest=Math.max(...allData.map(d=>parseInt(d.freq||0)));
      const totalTweets=allData.length;
      document.getElementById('statTotalTweets').innerHTML=`<div class="stat-value">${formatNumber(totalTweets)}</div>`;
      document.getElementById('statUniqueAuthors').innerHTML=`<div class="stat-value">${formatNumber(uniqueAuthors)}</div>`;
      document.getElementById('statHighestRT').innerHTML=`<div class="stat-value">${formatNumber(highest)}</div>`;
      ['statTotalTweets','statUniqueAuthors','statHighestRT'].forEach(id=>document.getElementById(id).classList.add('data-loaded'));
      statCards.forEach((c,i)=>animateProgress(c,[80,65,100][i]??70));
      renderTable(); updatePagination();
      document.getElementById('tableLoading').style.display='none';
      document.getElementById('tableWrapper').style.display='block';
      updatePagination();
    } else {
      document.getElementById('tableLoading').style.display='none';
      document.getElementById('emptyState').style.display='block';
      ['statTotalTweets','statUniqueAuthors','statHighestRT'].forEach(id=>{ document.getElementById(id).innerHTML='<div class="stat-value">0</div>'; });
    }
  }catch(err){
    console.error('Error loading most retweets:', err);
    document.getElementById('tableLoading').style.display='none';
    document.getElementById('emptyState').style.display='block';
  }finally{
    statCards.forEach(c=>{ removeLoadingBadge(c); c.classList.add('loaded'); });
    document.querySelector('[data-lazy-load="retweetsTable"]')?.classList.add('loaded');
  }
}

// ════════════════════════════════════════════
// VIRAL DONUT CHART — FIXED LABELS
// ════════════════════════════════════════════
let viralDataLoaded=false;

async function loadViralDonut(){
  if(viralDataLoaded) return; viralDataLoaded=true;

  const section=document.querySelector('[data-lazy-load="viralDonut"]');
  addLoadingBadge(section);

  try{
    const res=await fetch(`/mk/api/x/most-retweets?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
    const result=await res.json();

    if(result.success&&result.data&&result.data.length>0){
      const top5=result.data
        .sort((a,b)=>parseInt(b.freq||0)-parseInt(a.freq||0))
        .slice(0,5);
      renderViralDonut(top5);
    } else {
      document.getElementById('viralDonutLoading').style.display='none';
      document.getElementById('viralDonutEmpty').style.display='block';
    }
  }catch(err){
    console.error('Viral donut error:', err);
    document.getElementById('viralDonutLoading').style.display='none';
    document.getElementById('viralDonutEmpty').style.display='block';
  }finally{
    removeLoadingBadge(section);
    section?.classList.add('loaded');
  }
}

function renderViralDonut(top5){
  const loading = document.getElementById('viralDonutLoading');
  const chartEl = document.getElementById('viralDonutChart');
  loading.style.display = 'none';
  chartEl.style.display = 'block';

  // Build legend
  const legend = document.getElementById('viralLegend');
  legend.innerHTML = top5.map((item, i) => {
    const scr  = item.author?.scr_name || item.name || '';
    const freq = parseInt(item.freq || 0);
    return `<div class="viral-legend-item" onclick="highlightSegment(${i})" id="legend-item-${i}">
      <span class="viral-legend-dot" style="background:${SEGMENT_COLORS[i]};"></span>
      @${scr} · ${formatNumber(freq)}
    </div>`;
  }).join('');

  const total = top5.reduce((s, item) => s + parseInt(item.freq || 0), 0);

  const pieData = top5.map((item, i) => {
    const scr         = item.author?.scr_name || item.name || 'Unknown';
    const freq        = parseInt(item.freq || 0);
    const fullContent = item.content || '';
    return {
      name:        scr,
      value:       freq,
      scr,
      fullContent,
      freq,
      color:       SEGMENT_COLORS[i],
      idx:         i,
      tweetLink:   item.sub_id
        ? `https://twitter.com/${scr}/status/${item.sub_id}`
        : `https://twitter.com/${scr}`,
      date:        item.date_created || '',
      sentiment:   item.sentiment_str || 'Neutral',
      itemStyle:   { color: SEGMENT_COLORS[i], borderColor:'#fff', borderWidth:3 }
    };
  });

  viralChart = echarts.init(chartEl, null, { renderer: 'canvas' });

  viralChart.setOption({
    animation:         true,
    animationDuration: 1000,
    animationEasing:   'cubicOut',
    backgroundColor:   '#ffffff',

    tooltip: {
      trigger: 'item',
      position: function(point, params, dom, rect, size) {
        const [mouseX, mouseY] = point;
        const [viewW, viewH]   = size.viewSize;
        const [tipW,  tipH]    = size.contentSize;
        const offset = 14;
        let x = mouseX + offset, y = mouseY + offset;
        if (x + tipW > viewW - 8) x = mouseX - tipW - offset;
        if (x < 8) x = 8;
        if (y + tipH > viewH - 8) y = mouseY - tipH - offset;
        if (y < 8) y = 8;
        return [x, y];
      },
      backgroundColor: '#111827',
      borderColor:     '#374151',
      borderWidth:      1,
      padding:         [14, 16],
      extraCssText: [
        'border-radius:12px',
        'box-shadow:0 8px 32px rgba(0,0,0,.45)',
        'max-width:280px',
        'word-break:break-word',
        'white-space:normal',
        'pointer-events:none',
        'z-index:9999',
      ].join(';'),
      textStyle: { color:'#f9fafb', fontFamily:"'Poppins', sans-serif", fontSize:12 },
      formatter: function(params) {
        const d   = params.data;
        const pct = total > 0 ? ((d.value / total) * 100).toFixed(1) : '0';
        const snippet = d.fullContent && d.fullContent.length > 120
          ? d.fullContent.slice(0, 120) + '…'
          : (d.fullContent || '');
        const sentLower = (d.sentiment || '').toLowerCase();
        const sentColor = sentLower==='positive'?'#10b981':sentLower==='negative'?'#ef4444':'#94a3b8';
        const sentBg    = sentLower==='positive'?'rgba(16,185,129,.2)':sentLower==='negative'?'rgba(239,68,68,.2)':'rgba(148,163,184,.15)';
        const sentLabel = sentLower==='positive'?'Positive':sentLower==='negative'?'Negative':'Neutral';
        return `
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;
                      padding-bottom:9px;border-bottom:1px solid rgba(255,255,255,.1);">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;
                         background:${d.color};flex-shrink:0;"></span>
            <span style="font-weight:700;font-size:13px;color:#fff;">@${d.scr}</span>
          </div>
          <div style="font-size:12px;color:#d1d5db;line-height:1.6;margin-bottom:12px;
                      word-wrap:break-word;">"${snippet}"</div>
          <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
            <span style="background:rgba(255,255,255,.1);border-radius:6px;
                         padding:3px 10px;font-size:11px;font-weight:700;color:#fff;">
              🔁 ${formatNumber(d.value)}
            </span>
            <span style="background:rgba(255,255,255,.1);border-radius:6px;
                         padding:3px 10px;font-size:11px;font-weight:700;color:#fff;">
              ${pct}%
            </span>
            <span style="background:${sentBg};color:${sentColor};border-radius:6px;
                         padding:3px 10px;font-size:11px;font-weight:700;">
              ${sentLabel}
            </span>
          </div>`;
      },
    },

    legend: { show: false },

    series: [{
      type:              'pie',
      radius:            ['36%', '54%'],
      center:            ['50%', '50%'],
      avoidLabelOverlap: true,
      minAngle:          8,
      padAngle:          1.5,
      itemStyle:         { borderColor:'#ffffff', borderWidth:3 },

      // ── FIXED: compact 3-line label (name + snippet + value·pct) ──
      label: {
        show:        true,
        position:    'outside',
        alignTo:     'edge',
        edgeDistance: 14,
        lineHeight:   17,
        fontFamily:  "'Poppins', sans-serif",
        fontSize:     11,
        fontWeight:  '500',
        color:       '#475569',
        formatter: function(params) {
          const d   = params.data;
          const pct = total > 0 ? ((d.value / total) * 100).toFixed(1) : '0';
          // Line 1: @username (max 30 chars)
          const nm   = ('@' + d.scr).length > 30 ? ('@' + d.scr).slice(0, 29) + '…' : '@' + d.scr;
          // Line 2: tweet snippet (max 40 chars)
          const snip = d.fullContent
            ? (d.fullContent.length > 40 ? d.fullContent.slice(0, 39) + '…' : d.fullContent)
            : '';
          const line2 = snip ? `{snip|${snip}}\n` : '';
          // Line 3: retweet count · percent
          return `{name|${nm}}\n${line2}{pct|${formatNumber(d.value)}  ·  ${pct}%}`;
        },
        rich: {
          name: { fontSize:11, fontWeight:'700', color:'#1e293b', lineHeight:17 },
          snip: { fontSize:10, fontWeight:'400', color:'#64748b', lineHeight:15 },
          pct:  { fontSize:10, fontWeight:'600', color:'#94a3b8', lineHeight:15 }
        }
      },

      labelLine: {
        show:       true,
        length:     12,
        length2:    20,
        smooth:     0.3,
        lineStyle:  { width:1.2, color:'#cbd5e1' }
      },

      emphasis: {
        scale:      true,
        scaleSize:  6,
        itemStyle:  { shadowBlur:14, shadowColor:'rgba(0,0,0,.18)' },
        label:      { fontWeight:'700', fontSize:12 }
      },

      data: pieData,
    }],

    graphic: [
      {
        type:'text', left:'center', top:'44%', z:100,
        style:{ text:formatNumber(total), fill:'#0f172a', font:"800 26px 'Poppins', sans-serif", textAlign:'center' }
      },
      {
        type:'text', left:'center', top:'52%', z:100,
        style:{ text:'TOTAL RETWEETS', fill:'#94a3b8', font:"600 9px 'Poppins', sans-serif", textAlign:'center' }
      }
    ]
  });

  viralChart.on('click', 'series', function(params) {
    const d = params.data;
    openTweetModal(d.fullContent, d.scr, d.scr, '', d.freq, d.sentiment, d.date, d.tweetLink);
  });
  viralChart.on('mouseover', () => { chartEl.style.cursor = 'pointer'; });
  viralChart.on('mouseout',  () => { chartEl.style.cursor = 'default'; });
  window.addEventListener('resize', () => { viralChart?.resize(); });
}

function highlightSegment(idx){
  if(!viralChart) return;
  viralChart.dispatchAction({ type:'highlight', seriesIndex:0, dataIndex:idx });
  viralChart.dispatchAction({ type:'showTip',  seriesIndex:0, dataIndex:idx });
}

// ── Table Render ──
function renderTable(){
  const startIdx=(currentPage-1)*tweetsPerPage;
  const endIdx=startIdx+tweetsPerPage;
  const currentData=allData.slice(startIdx,endIdx);

  let html=`<table class="data-table" id="retweetsTable">
    <thead><tr>
      <th>NO.</th><th>AVATAR</th><th>NAME</th><th>ACCOUNT NAME</th>
      <th>TWEET</th><th>SENTIMENT</th>
      <th style="text-align:center;">RETWEETS</th><th>DATE</th><th></th>
    </tr></thead><tbody>`;

  currentData.forEach((item,i)=>{
    const actualRank=startIdx+i+1;
    const authorName=item.author?.name||item.name||'Unknown';
    const authorScr=item.author?.scr_name||item.name||'';
    const avatarUrl=item.avatar_url||item.author?.image||'';
    const content=item.content||'';
    const freq=parseInt(item.freq||item.sentiment_freq||0);
    const sentStr=item.sentiment_str||'Neutral';
    const dateStr=item.date_created||'';
    const tweetId=item.sub_id||'';
    const tweetLink=tweetId?`https://twitter.com/${authorScr}/status/${tweetId}`:`https://twitter.com/${authorScr}`;
    const sStyle=getSentimentStyle(sentStr);
    const initials=getInitials(authorName);

    const hasValidAvatar=avatarUrl&&!avatarUrl.startsWith('/external')&&avatarUrl!=='/images/default-avatar.png';
    let avatarHtml=hasValidAvatar
      ?`<img src="${avatarUrl}" alt="${authorName}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
         <div class="user-avatar-fallback" style="display:none;">${initials}</div>`
      :`<img src="https://unavatar.io/twitter/${authorScr.replace('@','')}" alt="${authorName}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
         <div class="user-avatar-fallback" style="display:none;">${initials}</div>`;

    const esc=s=>s.replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;');

    html+=`<tr>
      <td><strong>${actualRank}</strong></td>
      <td><div class="avatar-container">${avatarHtml}</div></td>
      <td><a href="https://twitter.com/${authorScr}" target="_blank" class="username-link">@${authorScr}</a></td>
      <td><a href="https://twitter.com/${authorScr}" target="_blank" class="account-name-link">${authorName}</a></td>
      <td>
        <div class="tweet-text-cell" onclick="openTweetModal('${esc(content)}','${esc(authorName)}','${esc(authorScr)}','${esc(avatarUrl)}','${freq}','${esc(sentStr)}','${esc(dateStr)}','${esc(tweetLink)}')">
          ${content}
        </div>
      </td>
      <td>
        <span class="sentiment-badge" style="background:${sStyle.bg};color:${sStyle.color};">
          <span class="sentiment-dot" style="background:${sStyle.dot};"></span>${sentStr}
        </span>
      </td>
      <td style="text-align:center;">
        <div class="retweet-count">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
            <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
          </svg>
          ${formatNumber(freq)}
        </div>
      </td>
      <td style="color:var(--text-secondary);font-size:11px;white-space:nowrap;">${formatDateStr(dateStr)}</td>
      <td>
        <a href="${tweetLink}" target="_blank" class="view-tweet-btn">
          <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          View
        </a>
      </td>
    </tr>`;
  });

  html+='</tbody></table>';
  document.getElementById('tableWrapper').innerHTML=html;
}

function getPageRange(cur,total){
  if(total<=7) return Array.from({length:total},(_,i)=>i+1);
  if(cur<=4)   return [1,2,3,4,5,'...',total];
  if(cur>=total-3) return [1,'...',total-4,total-3,total-2,total-1,total];
  return [1,'...',cur-1,cur,cur+1,'...',total];
}

function updatePagination(){
  const totalPages=Math.ceil(allData.length/tweetsPerPage);
  const wrapper=document.getElementById('paginationWrapper');
  const from=allData.length?(currentPage-1)*tweetsPerPage+1:0;
  const to=Math.min(currentPage*tweetsPerPage,allData.length);

  let html=`<div class="pagination-info">Showing ${formatNumber(from)}–${formatNumber(to)} of ${formatNumber(allData.length)} tweets</div>`;
  html+=`<div style="display:flex;align-items:center;gap:6px;">`;
  html+=`<button class="page-btn" onclick="changePage(${currentPage-1})" ${currentPage===1?'disabled':''}>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
  </button>`;

  getPageRange(currentPage,totalPages).forEach(p=>{
    html+=p==='...'
      ?`<button class="page-btn" disabled style="cursor:default;">…</button>`
      :`<button class="page-btn ${p===currentPage?'active':''}" onclick="changePage(${p})">${p}</button>`;
  });

  html+=`<button class="page-btn" onclick="changePage(${currentPage+1})" ${currentPage===totalPages?'disabled':''}>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
  </button></div>`;

  wrapper.innerHTML=html;
  wrapper.style.display=allData.length>0?'flex':'none';
}

function changePage(p){
  const totalPages=Math.ceil(allData.length/tweetsPerPage);
  if(p<1||p>totalPages) return;
  currentPage=p; renderTable(); updatePagination();
  document.querySelector('.table-section').scrollIntoView({behavior:'smooth',block:'start'});
}

function filterTable(){
  const term=document.getElementById('searchInput').value.toLowerCase();
  if(!term){ currentPage=1; renderTable(); updatePagination(); return; }
  const filtered=allData.filter(item=>{
    const authorName=item.author?.name||item.name||'';
    const authorScr=item.author?.scr_name||item.name||'';
    const content=item.content||'';
    return (authorName+' '+authorScr+' '+content).toLowerCase().includes(term);
  });
  let html=`<table class="data-table"><thead><tr>
    <th>NO.</th><th>AVATAR</th><th>NAME</th><th>ACCOUNT NAME</th>
    <th>TWEET</th><th>SENTIMENT</th><th style="text-align:center;">RETWEETS</th><th>DATE</th><th></th>
  </tr></thead><tbody>`;
  filtered.forEach((item,i)=>{
    const authorName=item.author?.name||item.name||'Unknown';
    const authorScr=item.author?.scr_name||item.name||'';
    const avatarUrl=item.avatar_url||item.author?.image||'';
    const content=item.content||'';
    const freq=parseInt(item.freq??0);
    const sentStr=item.sentiment_str||'Neutral';
    const dateStr=item.date_created||'';
    const tweetId=item.sub_id||'';
    const tweetLink=tweetId?`https://twitter.com/${authorScr}/status/${tweetId}`:`https://twitter.com/${authorScr}`;
    const sStyle=getSentimentStyle(sentStr);
    const initials=getInitials(authorName);
    const hasValidAvatar=avatarUrl&&!avatarUrl.startsWith('/external')&&avatarUrl!=='/images/default-avatar.png';
    const username=authorScr.replace('@','');
    let avatarHtml=hasValidAvatar
      ?`<img src="${avatarUrl}" alt="${authorName}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
         <div class="user-avatar-fallback" style="display:none;">${initials}</div>`
      :`<img src="https://unavatar.io/twitter/${username}" alt="${authorName}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
         <div class="user-avatar-fallback" style="display:none;">${initials}</div>`;
    const esc=s=>s.replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;');
    html+=`<tr>
      <td><strong>${i+1}</strong></td>
      <td><div class="avatar-container">${avatarHtml}</div></td>
      <td><a href="https://twitter.com/${authorScr}" target="_blank" class="username-link">@${authorScr}</a></td>
      <td><a href="https://twitter.com/${authorScr}" target="_blank" class="account-name-link">${authorName}</a></td>
      <td><div class="tweet-text-cell" onclick="openTweetModal('${esc(content)}','${esc(authorName)}','${esc(authorScr)}','${esc(avatarUrl)}','${freq}','${esc(sentStr)}','${esc(dateStr)}','${esc(tweetLink)}')">${content}</div></td>
      <td><span class="sentiment-badge" style="background:${sStyle.bg};color:${sStyle.color};"><span class="sentiment-dot" style="background:${sStyle.dot};"></span>${sentStr}</span></td>
      <td style="text-align:center;"><div class="retweet-count">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        ${formatNumber(freq)}
      </div></td>
      <td style="color:var(--text-secondary);font-size:11px;white-space:nowrap;">${formatDateStr(dateStr)}</td>
      <td><a href="${tweetLink}" target="_blank" class="view-tweet-btn">
        <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        View
      </a></td>
    </tr>`;
  });
  html+='</tbody></table>';
  document.getElementById('tableWrapper').innerHTML=html;
  document.getElementById('paginationWrapper').style.display='none';
}

// ── Tweet Detail Modal ──
function openTweetModal(content, authorName, authorScr, avatarUrl, freq, sentStr, dateStr, tweetLink){
  const sStyle=getSentimentStyle(sentStr);
  const initials=getInitials(authorName);
  const hasValidAvatar=avatarUrl&&!avatarUrl.startsWith('/external')&&avatarUrl!=='/images/default-avatar.png';
  const username=authorScr.replace('@','');
  let avatarHtml=hasValidAvatar
    ?`<img src="${avatarUrl}" class="modal-author-avatar" alt="${authorName}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
       <div class="modal-author-avatar-fallback" style="display:none;">${initials}</div>`
    :`<img src="https://unavatar.io/twitter/${username}" class="modal-author-avatar" alt="${authorName}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
       <div class="modal-author-avatar-fallback" style="display:none;">${initials}</div>`;

  document.getElementById('tweetModalBody').innerHTML=`
    <div class="modal-author-row">${avatarHtml}
      <div><div class="modal-author-name">${authorName}</div><div class="modal-author-scr">@${authorScr}</div></div>
    </div>
    <div class="modal-tweet-text">${content}</div>
    <div class="modal-meta-row">
      <div class="modal-meta-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
          <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
        </svg>
        <strong>${formatNumber(freq)}</strong>&nbsp;Retweets
      </div>
      <div class="modal-meta-item">
        <span class="sentiment-badge" style="background:${sStyle.bg};color:${sStyle.color};font-size:11px;">
          <span class="sentiment-dot" style="background:${sStyle.dot};"></span>${sentStr}
        </span>
      </div>
    </div>
    <div class="modal-footer">
      <span class="modal-date">${formatDateStr(dateStr)}</span>
      <a href="${tweetLink}" target="_blank" class="modal-open-twitter">
        <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        Open on X
      </a>
    </div>`;

  const modal=document.getElementById('tweetDetailModal');
  modal.style.display='flex';
  setTimeout(()=>modal.classList.add('show'),10);
}

function closeTweetModal(){
  const modal=document.getElementById('tweetDetailModal');
  modal.classList.remove('show');
  setTimeout(()=>modal.style.display='none',300);
}
document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeTweetModal(); });

// ── Actions Dropdown ──
function toggleActionsDropdown(event){
  event.stopPropagation();
  document.getElementById('actionsDropdownMenu').classList.toggle('show');
}
document.addEventListener('click',()=>{ document.getElementById('actionsDropdownMenu')?.classList.remove('show'); });

function exportCSV(){
  document.getElementById('actionsDropdownMenu').classList.remove('show');
  if(!allData.length) return;
  let csv='Rank,Author Name,@Username,Tweet Content,Sentiment,Retweets,Date\n';
  allData.forEach((item,i)=>{
    const name=(item.author?.name||item.name||'').replace(/,/g,' ').replace(/"/g,'""');
    const scr=item.author?.scr_name||item.name||'';
    const content=(item.content||'').replace(/,/g,' ').replace(/"/g,'""').replace(/\n/g,' ');
    const sent=item.sentiment_str||'Neutral';
    const freq=item.freq||0;
    const date=item.date_created||'';
    csv+=`${i+1},"${name}","@${scr}","${content}","${sent}",${freq},"${date}"\n`;
  });
  const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a'); a.href=URL.createObjectURL(blob);
  a.download=`most_retweets_${startDate}_${endDate}.csv`; a.click();
}
function refreshData(){ document.getElementById('actionsDropdownMenu').classList.remove('show'); window.location.reload(); }
function printTable(){
  document.getElementById('actionsDropdownMenu').classList.remove('show');
  const w=window.open('','_blank');
  const tc=document.getElementById('tableWrapper').innerHTML;
  w.document.write(`<!DOCTYPE html><html><head>
    <title>Most Retweets - X</title>
    <style>body{font-family:Arial,sans-serif;padding:20px;}h1{color:#1a202c;}table{width:100%;border-collapse:collapse;}th{background:#f8fafc;padding:10px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;}td{padding:10px;font-size:12px;border-bottom:1px solid #f1f5f9;}</style>
    </head><body>
    <h1>Most Retweets — X (Twitter)</h1><p>Date Range: ${startDate} to ${endDate}</p>${tc}
  </body></html>`);
  w.document.close(); w.focus();
  setTimeout(()=>{ w.print(); w.close(); },250);
}
</script>
@endsection