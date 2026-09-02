@extends('mk.layouts.app')

@section('title', 'TikTok Top Hashtags - SMADIMENT')

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
    --accent-blue:          #2FC6F6;

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
  .page-header { display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px; }
  .page-header-left h1 { font-size:28px;font-weight:700;color:var(--text-primary);margin:0 0 6px;letter-spacing:-.4px; }
  .page-header-left p  { font-size:14px;color:var(--text-secondary);font-weight:500;margin:0; }

  .tiktok-badge {
    display:inline-flex;align-items:center;gap:8px;
    background:linear-gradient(135deg,#010101 0%,#2d2d2d 100%);
    border-radius:100px;padding:8px 16px;
    font-size:12px;font-weight:600;color:#fff;
    white-space:nowrap;flex-shrink:0;
    box-shadow:0 4px 14px rgba(0,0,0,.25);
  }
  .tiktok-badge .dot {
    width:8px;height:8px;border-radius:50%;
    background:linear-gradient(135deg,#69C9D0,#EE1D52);
    flex-shrink:0;
  }

  /* ── FILTER CARD ── */
  .filter-card { background:var(--bg-white);border-radius:var(--radius);padding:20px 24px;margin-bottom:24px;box-shadow:var(--shadow-sm);border:1px solid var(--border-gray); }
  .filter-content { display:flex;align-items:center;gap:16px;flex-wrap:wrap; }
  .filter-label { font-size:14px;font-weight:600;color:var(--text-primary);white-space:nowrap;display:flex;align-items:center;gap:8px; }
  .filter-label svg { width:16px;height:16px;stroke:var(--primary-green);fill:none;flex-shrink:0; }
  .date-range-wrapper { display:flex;align-items:center;gap:12px;flex:1; }

  .date-picker-trigger {
    display:flex;align-items:center;gap:12px;padding:11px 18px;
    background:var(--bg-gray-50);border:1px solid var(--border-gray);
    border-radius:var(--radius-sm);font-family:var(--font);font-size:14px;font-weight:500;
    color:var(--text-primary);cursor:pointer;transition:var(--transition);width:100%;max-width:420px;
  }
  .date-picker-trigger:hover { border-color:var(--primary-green);background:var(--bg-white);box-shadow:0 0 0 3px rgba(3,128,71,.1); }
  .date-picker-trigger svg:first-child { width:17px;height:17px;stroke:var(--text-secondary);fill:none;flex-shrink:0; }
  .date-picker-trigger span { flex:1;text-align:left; }
  .date-picker-trigger svg:last-child { width:15px;height:15px;margin-left:auto;stroke:var(--text-secondary);fill:none; }

  .apply-btn {
    padding:11px 26px;
    background:linear-gradient(135deg,var(--primary-green) 0%,var(--primary-green-dark) 100%);
    color:#fff;border:none;border-radius:var(--radius-sm);font-family:var(--font);
    font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);
    display:flex;align-items:center;gap:8px;
    box-shadow:0 4px 12px rgba(3,128,71,.25);white-space:nowrap;
  }
  .apply-btn:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(3,128,71,.35); }
  .apply-btn svg { width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2.5; }

  /* ── STATS GRID ── */
  .stats-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px; }

  .stat-card {
    background:var(--bg-white);border:1px solid var(--border-gray);border-radius:var(--radius);
    padding:22px 24px;transition:var(--transition);position:relative;overflow:hidden;
  }
  .stat-card::after {
    content:'';position:absolute;bottom:0;left:0;right:0;height:3px;
    background:linear-gradient(90deg,var(--primary-green),var(--accent-blue));
    transform:scaleX(0);transform-origin:left;transition:transform .3s;
  }
  .stat-card:hover { border-color:var(--primary-green-border);transform:translateY(-2px);box-shadow:var(--shadow-md); }
  .stat-card:hover::after { transform:scaleX(1); }

  .stat-icon {
    width:42px;height:42px;border-radius:var(--radius-sm);
    background:var(--primary-green-light);display:flex;align-items:center;justify-content:center;
    margin-bottom:14px;
  }
  .stat-icon svg { width:20px;height:20px;stroke:var(--primary-green);fill:none;stroke-width:2; }
  .stat-label { font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px; }
  .stat-value { font-size:30px;font-weight:800;color:var(--text-primary);line-height:1.1; }
  .stat-value.small { font-size:20px;word-break:break-all;color:var(--primary-green); }

  /* ── TABLE CARD ── */
  .table-card {
    background:var(--bg-white);border-radius:var(--radius);
    border:1px solid var(--border-gray);box-shadow:var(--shadow-sm);overflow:hidden;
  }
  .table-card-header {
    display:flex;align-items:center;justify-content:space-between;
    padding:20px 24px;border-bottom:1px solid var(--border-gray);
  }
  .table-card-header-left {}
  .table-card-title { font-size:16px;font-weight:700;color:var(--text-primary); }
  .table-card-sub   { font-size:12px;color:var(--text-muted);margin-top:3px; }

  .table-wrapper { overflow-x:auto; }

  .ht-table { width:100%;border-collapse:collapse;font-family:var(--font); }
  .ht-table thead { background:var(--bg-gray-50); }
  .ht-table th {
    padding:13px 24px;text-align:left;font-size:10px;font-weight:700;
    color:var(--text-muted);text-transform:uppercase;letter-spacing:1.2px;
    border-bottom:1px solid var(--border-gray);white-space:nowrap;
  }
  .ht-table th.tc { text-align:center; }
  .ht-table tbody tr { border-bottom:1px solid var(--border-light);transition:background .15s; }
  .ht-table tbody tr:hover { background:var(--bg-gray-50); }
  .ht-table tbody tr:last-child { border-bottom:none; }
  .ht-table td { padding:15px 24px;font-size:14px;color:var(--text-primary);vertical-align:middle; }

  /* ── RANK BADGE ── */
  .ht-rank {
    width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;
    font-size:13px;font-weight:800;background:var(--bg-gray-100);color:var(--text-muted);
    flex-shrink:0;
  }
  .ht-rank.gold   { background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;box-shadow:0 2px 8px rgba(245,158,11,.4); }
  .ht-rank.silver { background:linear-gradient(135deg,#94a3b8,#64748b);color:#fff;box-shadow:0 2px 8px rgba(100,116,139,.3); }
  .ht-rank.bronze { background:linear-gradient(135deg,#cd7c4a,#a0522d);color:#fff;box-shadow:0 2px 8px rgba(160,82,45,.3); }

  /* ── HASHTAG CELL ── */
  .ht-tag-wrap { display:flex;align-items:center;gap:12px; }
  .ht-tag-icon {
    width:36px;height:36px;border-radius:var(--radius-xs);flex-shrink:0;
    background:linear-gradient(135deg,var(--tiktok-light),rgba(238,29,82,.04));
    border:1px solid var(--tiktok-border);
    display:flex;align-items:center;justify-content:center;
    font-size:15px;font-weight:900;color:var(--tiktok);
  }
  .ht-tag-name { font-size:15px;font-weight:700;color:var(--text-primary); }
  .ht-tag-name span { color:var(--tiktok); }

  /* ── BAR ── */
  .ht-bar-wrap { display:flex;align-items:center;gap:12px; }
  .ht-bar-track { flex:1;height:7px;background:var(--bg-gray-100);border-radius:4px;overflow:hidden; }
  .ht-bar-fill {
    height:100%;border-radius:4px;
    background:linear-gradient(90deg,var(--primary-green),var(--accent-blue));
    transition:width 1.2s cubic-bezier(0.4,0,0.2,1);
  }
  .ht-bar-count { font-size:12px;font-weight:600;color:var(--text-muted);white-space:nowrap;min-width:54px;text-align:right; }

  /* ── COUNT ── */
  .ht-count { text-align:center;font-weight:800;font-size:15px;color:var(--text-primary); }

  /* ── ACTION ── */
  .ht-action { text-align:center; }
  .btn-view {
    padding:7px 14px;background:transparent;border:1px solid var(--border-gray);
    border-radius:var(--radius-xs);font-size:12px;font-weight:600;color:var(--text-secondary);
    cursor:pointer;transition:var(--transition);text-decoration:none;
    display:inline-flex;align-items:center;gap:5px;font-family:var(--font);
  }
  .btn-view:hover { background:var(--tiktok);color:#fff;border-color:var(--tiktok); }
  .btn-view svg { width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2; }

  /* ── PAGINATION ── */
  .ht-pagination {
    display:flex;justify-content:space-between;align-items:center;
    gap:8px;padding:18px 24px;background:var(--bg-gray-50);
    border-top:1px solid var(--border-gray);flex-wrap:wrap;
  }
  .ht-pag-info { font-size:13px;color:var(--text-muted);font-weight:500; }
  .page-btn {
    width:34px;height:34px;border-radius:var(--radius-xs);border:1px solid var(--border-gray);
    background:var(--bg-white);color:var(--text-secondary);font-size:13px;font-weight:600;
    cursor:pointer;transition:var(--transition);display:flex;align-items:center;justify-content:center;
    font-family:var(--font);
  }
  .page-btn:hover:not(:disabled) { border-color:var(--primary-green);color:var(--primary-green);background:var(--primary-green-light); }
  .page-btn.active { background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark));color:#fff;border-color:transparent;box-shadow:0 2px 8px rgba(3,128,71,.3); }
  .page-btn:disabled { opacity:.35;cursor:not-allowed; }

  /* ── SKELETON ── */
  .skeleton-line {
    background:linear-gradient(90deg,var(--bg-gray-100) 25%,var(--bg-gray-50) 50%,var(--bg-gray-100) 75%);
    background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:6px;
  }
  @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

  /* ── CHART CARD ── */
  .chart-card {
    background:var(--bg-white);border-radius:var(--radius);
    border:1px solid var(--border-gray);box-shadow:var(--shadow-sm);overflow:hidden;
  }
  .chart-card-header {
    display:flex;align-items:center;justify-content:space-between;
    padding:20px 24px;border-bottom:1px solid var(--border-gray);flex-wrap:wrap;gap:12px;
  }
  .chart-card-title { font-size:16px;font-weight:700;color:var(--text-primary); }
  .chart-card-sub   { font-size:12px;color:var(--text-muted);margin-top:3px; }
  .chart-controls { display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
  .top-select {
    padding:7px 12px;border:1px solid var(--border-gray);border-radius:var(--radius-xs);
    font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);
    background:var(--bg-gray-50);cursor:pointer;transition:var(--transition);outline:none;
  }
  .top-select:hover,.top-select:focus { border-color:var(--primary-green);color:var(--primary-green); }
  .chart-type-toggle { display:flex;align-items:center;background:var(--bg-gray-100);border-radius:var(--radius-xs);padding:3px; }
  .ctt-btn {
    display:flex;align-items:center;gap:6px;padding:6px 14px;border:none;
    border-radius:6px;font-family:var(--font);font-size:12px;font-weight:600;
    color:var(--text-muted);background:transparent;cursor:pointer;transition:var(--transition);
  }
  .ctt-btn.active { background:var(--bg-white);color:var(--text-primary);box-shadow:var(--shadow-xs); }
  .ctt-btn svg { width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2; }
  .chart-body { padding:24px; }
  #htChart { width:100%;height:520px; }
  .chart-sk { display:flex;align-items:flex-end;gap:8px;height:520px;padding:0 10px 40px; }
  .chart-sk-bar { flex:1;border-radius:6px 6px 0 0;animation:shimmer 1.5s infinite;
    background:linear-gradient(90deg,var(--bg-gray-100) 25%,var(--bg-gray-50) 50%,var(--bg-gray-100) 75%);
    background-size:200% 100%;
  }

  /* ── EMPTY STATE ── */
  .empty-state { text-align:center;padding:80px 20px; }
  .empty-state svg { width:56px;height:56px;stroke:var(--text-muted);fill:none;margin-bottom:16px; }
  .empty-state h3  { font-size:18px;font-weight:700;color:var(--text-primary);margin:0 0 8px; }
  .empty-state p   { font-size:14px;color:var(--text-secondary);margin:0; }

  /* ── ALERT ── */
  .alert { padding:16px 20px;border-radius:var(--radius-sm);margin-bottom:24px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:12px; }
  .alert-warning { background:#fef3c7;color:#92400e;border:1px solid #fcd34d; }

  /* ── DATE PICKER MODAL ── */
  .date-picker-modal {
    position:fixed;top:0;left:0;right:0;bottom:0;z-index:10000;
    display:none;align-items:center;justify-content:center;
    background:rgba(0,0,0,.5);backdrop-filter:blur(8px);
  }
  .date-picker-modal.show { display:flex; }
  .date-picker-overlay { position:absolute;top:0;left:0;right:0;bottom:0;cursor:pointer; }
  .date-picker-container {
    position:relative;background:#fff;border-radius:var(--radius);
    box-shadow:0 25px 50px rgba(0,0,0,.3);display:flex;
    max-width:900px;width:90%;max-height:90vh;z-index:10001;
    animation:slideUpModal .3s ease-out;
  }
  @keyframes slideUpModal { from{opacity:0;transform:translateY(20px) scale(.95)} to{opacity:1;transform:translateY(0) scale(1)} }

  .date-picker-sidebar {
    width:180px;background:var(--bg-gray-50);border-right:1px solid var(--border-gray);
    padding:16px 12px;border-radius:var(--radius) 0 0 var(--radius);
    display:flex;flex-direction:column;gap:4px;flex-shrink:0;
  }
  .date-preset {
    padding:10px 16px;background:transparent;border:none;border-radius:var(--radius-xs);
    font-family:var(--font);font-size:13px;font-weight:500;
    color:var(--text-primary);text-align:left;cursor:pointer;transition:var(--transition);
  }
  .date-preset:hover  { background:var(--bg-white);color:var(--primary-green); }
  .date-preset.active { background:var(--primary-green);color:#fff; }

  .date-picker-content { flex:1;padding:24px;display:flex;flex-direction:column;overflow:hidden; }
  .date-picker-header  { display:flex;align-items:flex-start;gap:20px;margin-bottom:20px; }
  .nav-btn {
    width:36px;height:36px;border-radius:var(--radius-xs);background:var(--bg-gray-50);
    border:1px solid var(--border-gray);display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:var(--transition);flex-shrink:0;
  }
  .nav-btn:hover { background:var(--primary-green);border-color:var(--primary-green);color:#fff; }
  .nav-btn svg { width:20px;height:20px;stroke:currentColor;fill:none; }

  .calendars-wrapper { display:flex;gap:24px;flex:1;min-height:0; }
  .calendar          { flex:1;display:flex;flex-direction:column;min-width:0; }
  .calendar-month    { font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:16px;text-align:center; }
  .calendar-weekdays { display:grid;grid-template-columns:repeat(7,1fr);gap:4px;margin-bottom:8px; }
  .weekday           { text-align:center;font-size:11px;font-weight:700;color:var(--text-secondary);padding:8px 0; }
  .calendar-days     { display:grid;grid-template-columns:repeat(7,1fr);gap:4px; }
  .calendar-day {
    aspect-ratio:1;display:flex;align-items:center;justify-content:center;
    font-size:13px;font-weight:500;border-radius:var(--radius-xs);cursor:pointer;
    transition:var(--transition);color:var(--text-primary);background:transparent;border:none;padding:0;
  }
  .calendar-day:hover:not(.disabled):not(.other-month) { background:var(--bg-gray-100); }
  .calendar-day.other-month { color:#cbd5e1;cursor:default; }
  .calendar-day.disabled    { color:#e2e8f0;cursor:not-allowed; }
  .calendar-day.today       { border:2px solid var(--primary-green); }
  .calendar-day.in-range    { background:var(--primary-green-light);color:var(--primary-green); }
  .calendar-day.range-start,.calendar-day.range-end,.calendar-day.selected {
    background:var(--primary-green)!important;color:#fff!important;border:none!important;border-radius:var(--radius-xs)!important;
  }
  .date-picker-display {
    padding:14px 20px;background:var(--bg-gray-50);border-radius:var(--radius-sm);
    text-align:center;margin-bottom:20px;border:1px solid var(--border-gray);
  }
  .date-picker-display span { font-size:14px;font-weight:600;color:var(--text-primary); }
  .date-picker-footer { display:flex;gap:12px;justify-content:flex-end; }
  .cancel-btn {
    padding:10px 24px;border-radius:10px;font-family:var(--font);
    font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);border:none;
    background:var(--bg-gray-100);color:var(--text-primary);
  }
  .cancel-btn:hover { background:var(--border-gray); }
  .apply-date-btn {
    padding:10px 24px;border-radius:10px;font-family:var(--font);
    font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);border:none;
    background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark));
    color:#fff;box-shadow:0 4px 12px rgba(3,128,71,.25);
  }
  .apply-date-btn:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(3,128,71,.35); }

  /* ── RESPONSIVE ── */
  @media (max-width:1200px) { .stats-grid { grid-template-columns:repeat(2,1fr); } }
  @media (max-width:768px) {
    .fme-page { padding:16px; }
    .stats-grid { grid-template-columns:1fr; }
    .filter-content { flex-direction:column;align-items:stretch; }
    .date-range-wrapper { flex-direction:column; }
    .apply-btn { width:100%;justify-content:center; }
    .ht-table th,.ht-table td { padding:12px; font-size:12px; }
    .date-picker-container { flex-direction:column;max-height:85vh;overflow-y:auto;width:95%; }
    .date-picker-sidebar { width:100%;border-right:none;border-bottom:1px solid var(--border-gray);border-radius:var(--radius) var(--radius) 0 0;flex-direction:row;overflow-x:auto;padding:12px 16px; }
    .date-preset { white-space:nowrap; }
    .calendars-wrapper { flex-direction:column;gap:16px; }
    .date-picker-trigger { max-width:100%; }
    .cancel-btn,.apply-date-btn { flex:1; }
    .page-header { flex-direction:column;align-items:flex-start;gap:10px; }
    .ht-tag-icon { display:none; }
  }
</style>
@endsection

@section('content')
<div class="fme-page">

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-left">
      <h1>TikTok Top Hashtags</h1>
      <p>Hashtag paling populer di TikTok berdasarkan jumlah mention dalam periode yang dipilih</p>
    </div>
    <div class="tiktok-badge">
      <span class="dot"></span>
      TikTok Analytics
    </div>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;flex-shrink:0;stroke-width:2;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar.</span>
  </div>
  @else

  <!-- Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.tiktok.trending-topics') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hiddenEndDate"   value="{{ $endDate }}">
      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          Date Range
        </div>
        <div class="date-range-wrapper">
          <button type="button" class="date-picker-trigger" id="datePickerTrigger">
            <svg viewBox="0 0 24 24" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="dateRangeDisplay">{{ $startDate }} to {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <button type="submit" class="apply-btn">
          <svg viewBox="0 0 24 24" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          Apply Filter
        </button>
      </div>
    </form>
  </div>

  <!-- Date Picker Modal -->
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
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="calendar1"></div>
            <div class="calendar" id="calendar2"></div>
          </div>
          <button type="button" class="nav-btn" id="nextMonth">
            <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="date-picker-display">
          <span id="selectedRangeText">{{ $startDate }} to {{ $endDate }}</span>
        </div>
        <div class="date-picker-footer">
          <button type="button" class="cancel-btn">Cancel</button>
          <button type="button" class="apply-date-btn" id="applyDatePicker">Apply</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
      </div>
      <div class="stat-label">Total Hashtags</div>
      <div id="statTotalHashtags" class="stat-value">
        <div class="skeleton-line" style="width:55%;height:28px;"></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      </div>
      <div class="stat-label">Total Mentions</div>
      <div id="statTotalMentions" class="stat-value">
        <div class="skeleton-line" style="width:55%;height:28px;"></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <div class="stat-label">Top Hashtag</div>
      <div id="statTopHashtag" class="stat-value small">
        <div class="skeleton-line" style="width:65%;height:22px;"></div>
      </div>
    </div>
  </div>

  <!-- Chart Card -->
  <div class="chart-card">
    <div class="chart-card-header">
      <div>
        <div class="chart-card-title">Trending Hashtags</div>
        <div class="chart-card-sub">Diurutkan berdasarkan jumlah mention terbanyak</div>
      </div>
      <div class="chart-controls">
        <select class="top-select" id="topSelect" onchange="HashtagApp.setTop(this.value)">
          <option value="10">Top 10</option>
          <option value="20" selected>Top 20</option>
          <option value="30">Top 30</option>
          <option value="50">Top 50</option>
        </select>
        <div class="chart-type-toggle">
          <button class="ctt-btn active" id="btnBar" onclick="HashtagApp.setType('bar')">
            <svg viewBox="0 0 24 24"><rect x="3" y="12" width="4" height="9"/><rect x="10" y="7" width="4" height="14"/><rect x="17" y="3" width="4" height="18"/></svg>
            Bar
          </button>
          <button class="ctt-btn" id="btnDonut" onclick="HashtagApp.setType('donut')">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/></svg>
            Donut
          </button>
        </div>
      </div>
    </div>

    <div class="chart-body">
      <!-- Skeleton -->
      <div class="chart-sk" id="chartSkeleton">
        @for($i=0;$i<20;$i++)
        <div class="chart-sk-bar" style="height:{{ rand(20,95) }}%;opacity:{{ 1 - $i*0.03 }};"></div>
        @endfor
      </div>
      <!-- Chart -->
      <div id="htChart" style="display:none;"></div>
      <!-- Empty -->
      <div id="emptyState" style="display:none;">
        <div class="empty-state">
          <svg viewBox="0 0 24 24" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          <h3>No Hashtags Found</h3>
          <p>Tidak ada data hashtag TikTok untuk periode ini. Coba ubah tanggal filter.</p>
        </div>
      </div>
    </div>
  </div>

  @endif
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
// ── DATE PICKER ───────────────────────────────────────────────────────────────
(function () {
  'use strict';
  let sd, ed, m1, m2, picking = true;

  document.addEventListener('DOMContentLoaded', () => {
    const sv = document.getElementById('hiddenStartDate');
    const ev = document.getElementById('hiddenEndDate');
    sd = sv?.value ? new Date(sv.value+'T00:00:00') : (() => { const d=new Date(); d.setDate(d.getDate()-6); d.setHours(0,0,0,0); return d; })();
    ed = ev?.value ? new Date(ev.value+'T00:00:00') : (() => { const d=new Date(); d.setHours(0,0,0,0); return d; })();
    m1 = new Date(sd.getFullYear(), sd.getMonth(), 1);
    m2 = new Date(sd.getFullYear(), sd.getMonth()+1, 1);
    render(); bind();
  });

  function bind() {
    document.getElementById('datePickerTrigger').onclick   = openPicker;
    document.querySelector('.date-picker-overlay').onclick = closePicker;
    document.querySelector('.cancel-btn').onclick          = closePicker;
    document.addEventListener('keydown', e => { if(e.key==='Escape') closePicker(); });
    document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', applyPreset));
    document.getElementById('prevMonth').onclick = () => { m1.setMonth(m1.getMonth()-1); m2.setMonth(m2.getMonth()-1); render(); };
    document.getElementById('nextMonth').onclick = () => { m1.setMonth(m1.getMonth()+1); m2.setMonth(m2.getMonth()+1); render(); };
    document.getElementById('applyDatePicker').onclick = applyPicker;
  }

  function openPicker()  { document.getElementById('datePickerModal').classList.add('show'); render(); }
  function closePicker() { document.getElementById('datePickerModal').classList.remove('show'); }

  function applyPreset(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    const p=e.target.dataset.preset, t=new Date(); t.setHours(0,0,0,0);
    if      (p==='today')      { sd=new Date(t); ed=new Date(t); }
    else if (p==='yesterday')  { sd=new Date(t); sd.setDate(t.getDate()-1); ed=new Date(sd); }
    else if (p==='last7days')  { ed=new Date(t); sd=new Date(t); sd.setDate(t.getDate()-6); }
    else if (p==='last30days') { ed=new Date(t); sd=new Date(t); sd.setDate(t.getDate()-29); }
    else if (p==='thismonth')  { sd=new Date(t.getFullYear(),t.getMonth(),1); ed=new Date(t); }
    else if (p==='lastmonth')  { sd=new Date(t.getFullYear(),t.getMonth()-1,1); ed=new Date(t.getFullYear(),t.getMonth(),0); }
    if(p!=='custom') { m1=new Date(sd.getFullYear(),sd.getMonth(),1); m2=new Date(sd.getFullYear(),sd.getMonth()+1,1); render(); }
  }

  function applyPicker() {
    document.getElementById('hiddenStartDate').value        = fmt(sd);
    document.getElementById('hiddenEndDate').value          = fmt(ed);
    document.getElementById('dateRangeDisplay').textContent = fmt(sd)+' to '+fmt(ed);
    closePicker();
  }

  function render() {
    renderCal('calendar1',m1); renderCal('calendar2',m2);
    document.getElementById('selectedRangeText').textContent = fmt(sd)+' to '+fmt(ed);
  }

  function renderCal(id,mon) {
    const el=document.getElementById(id); if(!el) return;
    const y=mon.getFullYear(), mo=mon.getMonth();
    const first=new Date(y,mo,1), last=new Date(y,mo+1,0), prev=new Date(y,mo,0);
    const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
    let h='<div class="calendar-month">'+MN[mo]+' '+y+'</div>'+
      '<div class="calendar-weekdays">'+WD.map(d=>'<div class="weekday">'+d+'</div>').join('')+'</div>'+
      '<div class="calendar-days">';
    for(let i=first.getDay()-1;i>=0;i--)
      h+='<button type="button" class="calendar-day other-month" disabled>'+(prev.getDate()-i)+'</button>';
    const today=new Date(); today.setHours(0,0,0,0);
    for(let d=1;d<=last.getDate();d++){
      const dt=new Date(y,mo,d); dt.setHours(0,0,0,0);
      let cls='calendar-day';
      if(same(dt,today)) cls+=' today';
      if(dt>today)       cls+=' disabled';
      if(sd&&ed){
        if(same(dt,sd))       cls+=' range-start selected';
        else if(same(dt,ed))  cls+=' range-end selected';
        else if(dt>sd&&dt<ed) cls+=' in-range';
      }
      h+='<button type="button" class="'+cls+'" data-date="'+fmt(dt)+'" '+(dt>today?'disabled':'')+'>'+d+'</button>';
    }
    for(let i=1;i<7-last.getDay();i++)
      h+='<button type="button" class="calendar-day other-month" disabled>'+i+'</button>';
    h+='</div>';
    el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(b=>b.addEventListener('click',dayClick));
  }

  function dayClick(e) {
    const dt=new Date(e.target.dataset.date+'T00:00:00');
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
    document.querySelector('[data-preset="custom"]').classList.add('active');
    if(picking||dt<sd){sd=dt;ed=dt;picking=false;}
    else{ed=dt>=sd?dt:sd;if(dt<sd){ed=sd;sd=dt;}picking=true;}
    render();
  }

  function fmt(d) { if(!d)return''; return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
  function same(a,b) { return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
})();

// ── HASHTAG APP ───────────────────────────────────────────────────────────────
const HashtagApp = {
  projectId   : '{{ $projectId ?? "" }}',
  startDate   : '{{ $startDate ?? "" }}',
  endDate     : '{{ $endDate ?? "" }}',
  allHashtags : [],
  chartType   : 'bar',   // 'bar' | 'donut'
  topN        : 20,
  chart       : null,

  COLORS: ['#EE1D52','#2FC6F6','#10b981','#f59e0b','#8b5cf6','#06b6d4','#f43f5e','#ec4899',
           '#14b8a6','#f97316','#6366f1','#84cc16','#ef4444','#3b82f6','#a855f7',
           '#22c55e','#fb923c','#38bdf8','#e879f9','#4ade80'],

  numFmt(n) { return new Intl.NumberFormat('id-ID').format(n); },

  async init() {
    if(!this.projectId) return;
    try { await this.loadData(); }
    catch(err) { console.error('HashtagApp error:', err); this.showError(); }
  },

  async loadData() {
    const url    = '/mk/api/tiktok/trending-topics?project_id='+this.projectId+'&start_date='+this.startDate+'&end_date='+this.endDate;
    const res    = await fetch(url);
    const result = await res.json();
    if(!result.success) throw new Error(result.error||'API Error');

    const data       = result.data;
    this.allHashtags = data.hashtags||[];

    document.getElementById('statTotalHashtags').textContent = this.numFmt(data.total_hashtags||0);
    document.getElementById('statTotalMentions').textContent = this.numFmt(data.total_mentions||0);
    const top = data.top_hashtag;
    document.getElementById('statTopHashtag').textContent = top ? '#'+(top.name||'') : '—';

    this.renderChart();
  },

  getSlice() {
    return this.allHashtags.slice(0, this.topN);
  },

  renderChart() {
    document.getElementById('chartSkeleton').style.display = 'none';
    document.getElementById('emptyState').style.display    = 'none';

    if(!this.allHashtags.length) {
      document.getElementById('htChart').style.display = 'none';
      document.getElementById('emptyState').style.display = 'block';
      return;
    }

    const el = document.getElementById('htChart');
    el.style.display = 'block';

    if(!this.chart) {
      this.chart = echarts.init(el, null, {renderer:'canvas'});
      window.addEventListener('resize', () => this.chart && this.chart.resize());
    }

    if(this.chartType === 'bar') this._renderBar();
    else                          this._renderDonut();
  },

  _renderBar() {
    const data   = this.getSlice();
    const names  = data.map(h => '#'+h.name);
    const values = data.map(h => h.size||0);
    const total  = values.reduce((a,b)=>a+b,0);

    // gradient bar colors cycling through COLORS
    const barColors = data.map((_,i) => ({
      type:'linear', x:0, y:0, x2:0, y2:1,
      colorStops:[
        {offset:0, color: this.COLORS[i % this.COLORS.length]},
        {offset:1, color: this.COLORS[i % this.COLORS.length]+'88'}
      ]
    }));

    const opt = {
      backgroundColor: '#ffffff',
      animation: true,
      animationDuration: 900,
      animationEasing: 'cubicOut',
      grid: { top:20, right:20, bottom:120, left:20, containLabel:true },
      tooltip: {
        trigger:'axis',
        axisPointer:{ type:'shadow', shadowStyle:{ color:'rgba(238,29,82,.04)' } },
        backgroundColor:'#1a202c',
        borderColor:'#374151',
        borderWidth:1,
        padding:[12,16],
        textStyle:{ color:'#f9fafb', fontFamily:"'Poppins',sans-serif", fontSize:12 },
        formatter: function(p) {
          const val = p[0].value;
          const pct = total>0 ? ((val/total)*100).toFixed(1) : '0';
          return '<div style="font-family:Poppins,sans-serif;">'
            + '<div style="font-weight:700;margin-bottom:6px;color:#fff;">'+p[0].name+'</div>'
            + '<div style="display:flex;justify-content:space-between;gap:24px;">'
            + '<span style="color:#94a3b8;">Mentions</span>'
            + '<span style="font-weight:700;color:#fff;">'+new Intl.NumberFormat('id-ID').format(val)+'</span>'
            + '</div>'
            + '<div style="display:flex;justify-content:space-between;gap:24px;margin-top:4px;">'
            + '<span style="color:#94a3b8;">Proporsi</span>'
            + '<span style="font-weight:700;color:#fff;">'+pct+'%</span>'
            + '</div>'
            + '</div>';
        }
      },
      xAxis: {
        type:'category', data:names,
        axisLabel:{
          rotate:35, interval:0,
          fontFamily:"'Poppins',sans-serif", fontSize:11, fontWeight:600, color:'#475569',
          formatter: v => v.length>12 ? v.slice(0,11)+'…' : v
        },
        axisLine:{ lineStyle:{ color:'#e2e8f0' } },
        axisTick:{ show:false }
      },
      yAxis: {
        type:'value',
        axisLabel:{ fontFamily:"'Poppins',sans-serif", fontSize:11, color:'#94a3b8',
          formatter: v => v>=1000000 ? (v/1000000).toFixed(1)+'M' : v>=1000 ? (v/1000).toFixed(0)+'K' : v },
        splitLine:{ lineStyle:{ color:'#f1f5f9', type:'dashed' } },
        axisLine:{ show:false }, axisTick:{ show:false }
      },
      series:[{
        type:'bar', data: values.map((v,i) => ({ value:v, itemStyle:{ color:barColors[i], borderRadius:[6,6,0,0] } })),
        barMaxWidth: 40,
        emphasis:{ itemStyle:{ shadowBlur:12, shadowColor:'rgba(238,29,82,.25)' } },
        label:{
          show: data.length <= 15,
          position:'top',
          fontFamily:"'Poppins',sans-serif", fontSize:10, fontWeight:700, color:'#64748b',
          formatter: p => p.value>=1000000 ? (p.value/1000000).toFixed(1)+'M'
                        : p.value>=1000    ? (p.value/1000).toFixed(0)+'K'
                        : p.value
        }
      }]
    };

    this.chart.setOption(opt, true);
  },

  _renderDonut() {
    const data  = this.getSlice();
    const total = data.reduce((s,h)=>s+(h.size||0), 0);
    const self  = this;

    const pieData = data.map((h,i) => ({
      name: '#'+h.name,
      value: h.size||0,
      itemStyle:{ color: this.COLORS[i % this.COLORS.length] }
    }));

    const opt = {
      backgroundColor: '#ffffff',
      animation: true,
      animationDuration: 1000,
      animationEasing: 'cubicOut',
      tooltip: {
        trigger: 'item',
        confine: true,
        appendToBody: true,
        backgroundColor: '#1a202c',
        borderColor: '#374151',
        borderWidth: 1,
        padding: 0,
        extraCssText: 'border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,.4);pointer-events:none;',
        formatter: function(p) {
          var pct = total>0 ? ((p.value/total)*100).toFixed(1) : '0';
          var html = '<div style="width:220px;padding:12px 16px;font-family:Poppins,sans-serif;">';
          html += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">';
          html += '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:'+p.color+';flex-shrink:0;"></span>';
          html += '<span style="font-size:13px;font-weight:700;color:#fff;">'+p.name+'</span>';
          html += '</div>';
          html += '<div style="display:flex;justify-content:space-between;margin-bottom:4px;">';
          html += '<span style="font-size:11px;color:#94a3b8;">Mentions</span>';
          html += '<span style="font-size:13px;font-weight:700;color:#fff;">'+new Intl.NumberFormat('id-ID').format(p.value)+'</span>';
          html += '</div>';
          html += '<div style="display:flex;justify-content:space-between;">';
          html += '<span style="font-size:11px;color:#94a3b8;">Proporsi</span>';
          html += '<span style="font-size:13px;font-weight:700;color:#fff;">'+pct+'%</span>';
          html += '</div>';
          html += '</div>';
          return html;
        }
      },
      legend: { show:false },
      grid: { containLabel: true },
      series:[{
        type:'pie',
        radius:['42%','65%'],
        center:['50%','50%'],
        avoidLabelOverlap: true,
        minAngle: 4,
        padAngle: 1.5,
        itemStyle:{ borderColor:'#ffffff', borderWidth:3 },
        label:{ show:false },
        labelLine:{ show:false },
        emphasis:{
          scale:true, scaleSize:6,
          itemStyle:{ shadowBlur:16, shadowColor:'rgba(0,0,0,.2)' }
        },
        data: pieData
      }],
      graphic:[
        { type:'text', left:'center', top:'44%', z:100, style:{ text:this.numFmt(total), fill:'#0f172a', font:"800 28px 'Poppins',sans-serif", textAlign:'center' } },
        { type:'text', left:'center', top:'54%', z:100, style:{ text:'TOTAL MENTIONS', fill:'#94a3b8', font:"600 9px 'Poppins',sans-serif", textAlign:'center' } }
      ]
    };

    this.chart.setOption(opt, true);

    // render legend list below chart
    this._renderDonutLegend(data, total);

    // hover highlight
    this.chart.off('mouseover'); this.chart.off('mouseout');
    this.chart.on('mouseover', p => { if(p.dataIndex!==undefined) this.chart.dispatchAction({type:'highlight',seriesIndex:0,dataIndex:p.dataIndex}); });
    this.chart.on('mouseout',  p => { if(p.dataIndex!==undefined) this.chart.dispatchAction({type:'downplay', seriesIndex:0,dataIndex:p.dataIndex}); });
  },

  _renderDonutLegend(data, total) {
    let el = document.getElementById('donutLegend');
    if(!el) {
      el = document.createElement('div');
      el.id = 'donutLegend';
      document.getElementById('htChart').insertAdjacentElement('afterend', el);
    }
    el.style.cssText = 'display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:6px 16px;padding:0 24px 20px;';
    el.innerHTML = data.map((h,i) => {
      const pct = total>0 ? ((h.size||0)/total*100).toFixed(1) : '0';
      const color = this.COLORS[i % this.COLORS.length];
      return '<div style="display:flex;align-items:center;gap:8px;padding:6px 10px;border-radius:8px;cursor:pointer;transition:background .15s;" '
        + 'onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'transparent\'">'
        + '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:'+color+';flex-shrink:0;"></span>'
        + '<span style="font-size:12px;font-weight:700;color:#1e293b;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">#'+h.name+'</span>'
        + '<span style="font-size:11px;font-weight:600;color:#94a3b8;white-space:nowrap;">'+this.numFmt(h.size||0)+'</span>'
        + '<span style="font-size:11px;font-weight:700;color:'+color+';white-space:nowrap;min-width:38px;text-align:right;">'+pct+'%</span>'
        + '</div>';
    }).join('');
  },

  setType(type) {
    this.chartType = type;
    document.getElementById('btnBar').classList.toggle('active',   type==='bar');
    document.getElementById('btnDonut').classList.toggle('active', type==='donut');
    // clear legend when switching to bar
    const lg = document.getElementById('donutLegend');
    if(lg && type==='bar') lg.innerHTML = '';
    // resize chart height for donut
    const el = document.getElementById('htChart');
    el.style.height = type==='donut' ? '460px' : '520px';
    if(this.chart) this.chart.resize();
    this.renderChart();
  },

  setTop(n) {
    this.topN = parseInt(n);
    this.renderChart();
  },

  showError() {
    document.getElementById('chartSkeleton').style.display = 'none';
    document.getElementById('htChart').style.display       = 'none';
    document.getElementById('emptyState').style.display    = 'block';
  }
};

document.addEventListener('DOMContentLoaded', () => HashtagApp.init());
</script>
@endsection