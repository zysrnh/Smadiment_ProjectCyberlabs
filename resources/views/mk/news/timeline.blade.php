@extends('mk.layouts.app')

@section('title', 'Mentions Timeline - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
    --text-primary: #1a202c;
    --text-secondary: #64748b;
    --bg-white: #ffffff;
    --bg-gray-50: #f8fafc;
    --bg-gray-100: #f1f5f9;
    --border-gray: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgba(0,0,0,.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,.1);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1);
    --blue:   #3b82f6;
    --sky:    #0ea5e9;
    --indigo: #6366f1;
    --pink:   #ec4899;
    --red:    #ef4444;
    --amber:  #f59e0b;
    --r:      16px;
    --rs:     12px;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  /* ── WRAP ── */
  .dashboard-container {
    padding: 24px;
    background: var(--bg-gray-50);
    min-height: 100vh;
  }

  /* ── PAGE HEADER ── */
  .page-header { margin-bottom: 28px; }
  .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; }
  .page-header p  { font-size: 14px; color: var(--text-secondary); font-weight: 500; margin: 0; }

  /* ── FILTER CARD ── */
  .filter-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: var(--r);
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
  }
  .filter-content {
    display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap;
  }
  .filter-group { display: flex; flex-direction: column; gap: 6px; }
  .filter-label {
    font-size: 11px; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .5px;
  }
  .date-trigger {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px;
    background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: var(--rs);
    font-size: 14px; font-weight: 500; color: var(--text-primary);
    cursor: pointer; transition: all .2s; min-width: 300px;
  }
  .date-trigger:hover { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(3,128,71,.1); }
  .date-trigger span { flex: 1; text-align: left; }
  .apply-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 24px;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    color: #fff; border: none; border-radius: var(--rs);
    font-size: 14px; font-weight: 600; cursor: pointer; transition: all .3s;
    box-shadow: 0 4px 12px rgba(3,128,71,.2); white-space: nowrap;
  }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.3); }
  .apply-btn svg { fill: none; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; }

  /* ── DATE PICKER MODAL ── */
  .dp-modal {
    position: fixed; inset: 0; z-index: 9999;
    display: none; align-items: center; justify-content: center;
    background: rgba(0,0,0,.5); backdrop-filter: blur(8px);
  }
  .dp-modal.open { display: flex; }
  .dp-box {
    background: #fff; border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0,0,0,.3);
    display: flex; max-width: 900px; width: 92%; max-height: 90vh; overflow: auto;
    animation: dpUp .25s ease-out;
  }
  @keyframes dpUp { from{opacity:0;transform:translateY(20px) scale(.95)} to{opacity:1;transform:translateY(0) scale(1)} }
  .dp-sidebar {
    width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray);
    border-radius: 16px 0 0 16px; padding: 16px 12px;
    display: flex; flex-direction: column; gap: 4px; flex-shrink: 0;
  }
  .dp-preset {
    padding: 10px 16px; background: transparent; border: none; border-radius: 8px;
    font-size: 13px; font-weight: 500; color: var(--text-primary);
    text-align: left; cursor: pointer; transition: all .2s;
  }
  .dp-preset:hover  { background: #fff; color: var(--primary-green); }
  .dp-preset.active { background: var(--primary-green); color: #fff; }
  .dp-body { flex: 1; padding: 24px; display: flex; flex-direction: column; gap: 14px; }
  .dp-nav  { display: flex; align-items: flex-start; gap: 14px; }
  .nav-arrow {
    width: 36px; height: 36px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--border-gray); border-radius: 8px; background: var(--bg-gray-50);
    cursor: pointer; transition: all .2s;
  }
  .nav-arrow:hover { background: var(--primary-green); border-color: var(--primary-green); color: #fff; }
  .cals-wrap { display: flex; gap: 24px; flex: 1; }
  .cal { flex: 1; }
  .cal-month { font-size: 16px; font-weight: 700; color: var(--text-primary); text-align: center; margin-bottom: 16px; }
  .cal-grid  { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; }
  .wday { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
  .cday {
    aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 500; border-radius: 8px;
    cursor: pointer; border: none; background: transparent;
    transition: all .15s; color: var(--text-primary);
  }
  .cday:hover:not(.other):not(.dis) { background: var(--bg-gray-100); }
  .cday.other  { color: #cbd5e1; pointer-events: none; }
  .cday.dis    { color: #e2e8f0; pointer-events: none; }
  .cday.today  { border: 2px solid var(--primary-green); }
  .cday.sel    { background: var(--primary-green); color: #fff; }
  .cday.range  { background: rgba(3,128,71,.1); color: var(--primary-green); }
  .dp-display {
    padding: 16px 20px; background: var(--bg-gray-50); border: 1px solid var(--border-gray);
    border-radius: var(--rs); text-align: center;
    font-size: 14px; font-weight: 600; color: var(--text-primary);
  }
  .dp-footer { display: flex; gap: 12px; justify-content: flex-end; }
  .btn-cancel {
    padding: 10px 24px; background: var(--bg-gray-100); border: none; border-radius: 10px;
    font-size: 14px; font-weight: 600; cursor: pointer; color: var(--text-primary);
  }
  .btn-apply-dp {
    padding: 10px 24px;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    color: #fff; border: none; border-radius: 10px;
    font-size: 14px; font-weight: 600; cursor: pointer;
  }

  /* ── DO-CARD (shared base) ── */
  .do-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: var(--r); overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all .3s cubic-bezier(.4,0,.2,1);
    position: relative; margin-bottom: 20px;
  }
  .do-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--primary-green), var(--primary-green-dark));
    opacity: 0; transition: opacity .3s;
  }
  .do-card:hover { box-shadow: var(--shadow-lg); border-color: rgba(3,128,71,.25); }
  .do-card:hover::before { opacity: 1; }

  .do-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--border-gray);
    flex-wrap: wrap; gap: 12px;
  }
  .do-card-head-left { display: flex; align-items: center; gap: 12px; }
  .do-head-icon {
    width: 40px; height: 40px; border-radius: 12px;
    background: linear-gradient(135deg, rgba(3,128,71,.1), rgba(3,128,71,.05));
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .do-head-icon svg { width: 20px; height: 20px; fill: none; stroke: var(--primary-green); stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .do-card-title { font-size: 16px; font-weight: 700; color: var(--text-primary); }
  .do-badge {
    display: inline-flex; align-items: center;
    padding: 4px 12px; border-radius: 20px;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px;
    background: var(--bg-gray-100); color: var(--text-secondary);
  }

  /* ── STATS CARD ── */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  }
  .stat-item {
    padding: 20px 22px; border-right: 1px solid var(--border-gray); text-align: center;
  }
  .stat-item:last-child { border-right: none; }
  .stat-lbl {
    font-size: 10px; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px;
  }
  .stat-num {
    font-size: 30px; font-weight: 700; line-height: 1; color: var(--text-primary);
  }
  .stat-num.c-news   { color: var(--blue);   }
  .stat-num.c-twit   { color: var(--sky);    }
  .stat-num.c-fb     { color: var(--indigo); }
  .stat-num.c-ig     { color: var(--pink);   }
  .stat-num.c-ytb    { color: var(--red);    }
  .stat-num.c-tiktok { color: #374151; }
  .stat-num.c-pos    { color: #16a34a; }
  .stat-num.c-neg    { color: var(--red); }
  .stat-num.c-neu    { color: var(--text-secondary); }
  .stats-divider { height: 1px; background: var(--bg-gray-100); margin: 0 20px; }

  /* ── CHART CARD ── */
  .chart-controls {
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
  }
  .chart-toggle {
    display: flex; background: var(--bg-gray-100); border-radius: 10px; padding: 3px; gap: 2px;
  }
  .ct-btn {
    padding: 5px 14px; border: none; border-radius: 8px;
    font-size: 11px; font-weight: 700; cursor: pointer; transition: all .2s;
    color: var(--text-secondary); background: transparent;
  }
  .ct-btn.active {
    background: var(--bg-white); color: var(--text-primary);
    box-shadow: 0 1px 3px rgba(0,0,0,.1);
  }
  .legend { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
  .legend-item {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600; color: var(--text-secondary);
    cursor: pointer; transition: opacity .2s; user-select: none;
  }
  .legend-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
  .legend-cnt {
    font-size: 10px; font-weight: 800; padding: 1px 7px; border-radius: 10px;
  }
  .chart-wrap { position: relative; height: 300px; }

  /* ── TABLE ── */
  .table-head {
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 22px; border-bottom: 1px solid var(--border-gray);
    flex-wrap: wrap; gap: 12px;
  }
  .table-head-info h3 { font-size: 16px; font-weight: 700; color: var(--text-primary); }
  .table-head-info p  { font-size: 12px; color: var(--text-secondary); margin-top: 3px; font-weight: 500; }
  .search-box { position: relative; }
  .search-box input {
    width: 260px; padding: 9px 14px 9px 38px;
    border: 1px solid var(--border-gray); border-radius: 20px;
    font-size: 13px; font-weight: 500; background: var(--bg-gray-50); color: var(--text-primary);
    transition: all .2s;
  }
  .search-box input:focus {
    outline: none; border-color: var(--primary-green); background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3,128,71,.1);
  }
  .search-box svg {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    width: 15px; height: 15px; color: var(--text-secondary);
  }

  /* ── MEDIA TABS ── */
  .media-tabs {
    display: flex; gap: 6px; flex-wrap: wrap;
    padding: 14px 22px 0; border-bottom: 1px solid var(--border-gray);
    padding-bottom: 0;
  }
  .media-tab {
    display: flex; align-items: center; gap: 6px; padding: 8px 16px;
    background: transparent; border: none; border-bottom: 2px solid transparent;
    font-size: 13px; font-weight: 600; color: var(--text-secondary);
    cursor: pointer; transition: all .2s; margin-bottom: -1px;
  }
  .media-tab:hover { color: var(--primary-green); }
  .media-tab.active {
    color: var(--primary-green);
    border-bottom-color: var(--primary-green);
  }
  .tab-cnt {
    padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 800;
    background: var(--bg-gray-100); color: var(--text-secondary);
  }
  .media-tab.active .tab-cnt { background: rgba(3,128,71,.1); color: var(--primary-green); }

  /* ── TAB SPINNER ── */
  .tab-spinner {
    display: inline-block; width: 10px; height: 10px;
    border: 2px solid var(--border-gray); border-top-color: var(--primary-green);
    border-radius: 50%; animation: spin .7s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ── MENTIONS TABLE ── */
  .mentions-table { width: 100%; border-collapse: collapse; }
  .mentions-table thead { background: var(--bg-gray-50); border-bottom: 1.5px solid var(--border-gray); }
  .mentions-table th {
    padding: 11px 14px; text-align: left;
    font-size: 10px; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .7px; white-space: nowrap;
  }
  .mentions-table th.c { text-align: center; }
  .mentions-table td {
    padding: 13px 14px; border-bottom: 1px solid var(--bg-gray-100);
    font-size: 13px; color: var(--text-primary); vertical-align: middle;
  }
  .mentions-table tr:hover td { background: var(--bg-gray-50); }
  .mentions-table tr:last-child td { border-bottom: none; }

  /* cells */
  .no-cell    { width: 42px; text-align: center; font-weight: 700; color: var(--text-secondary); font-size: 12px; }
  .media-cell { width: 90px; text-align: center; }
  .media-badge {
    display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 10px;
    font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; white-space: nowrap;
  }
  .mb-doc    { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
  .mb-twit   { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
  .mb-fb     { background: #e0e7ff; color: #3730a3; border: 1px solid #a5b4fc; }
  .mb-ig     { background: #fce7f3; color: #be185d; border: 1px solid #f9a8d4; }
  .mb-ytb    { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
  .mb-tiktok { background: var(--bg-gray-100); color: #374151; border: 1px solid var(--border-gray); }
  .type-badge {
    display: inline-block; padding: 2px 8px; border-radius: 6px;
    font-size: 10px; font-weight: 700; background: var(--bg-gray-100); color: var(--text-secondary);
  }
  .content-cell { max-width: 440px; }
  .mention-text {
    font-size: 13px; line-height: 1.55; color: var(--text-primary);
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 5px;
  }
  .mention-meta { display: flex; align-items: center; gap: 8px; font-size: 11px; color: var(--text-secondary); flex-wrap: wrap; }
  .view-link {
    color: var(--primary-green); font-weight: 700; text-decoration: none;
    display: inline-flex; align-items: center; gap: 3px; font-size: 11px; transition: all .15s;
  }
  .view-link:hover { color: var(--primary-green-dark); text-decoration: underline; }
  .num-cell { text-align: center; min-width: 64px; font-weight: 700; font-size: 13px; color: var(--text-secondary); }
  .author-cell { min-width: 170px; }
  .author-wrap { display: flex; align-items: center; gap: 10px; }
  .ava {
    width: 38px; height: 38px; border-radius: 50%; border: 2px solid var(--border-gray); flex-shrink: 0;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 13px; overflow: hidden;
  }
  .ava img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
  .aname   { font-weight: 700; font-size: 13px; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px; }
  .ahandle { font-size: 11px; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px; }
  .date-cell { min-width: 130px; }
  .date-main { font-weight: 600; font-size: 12px; color: var(--text-primary); }
  .date-time { font-size: 11px; color: var(--text-secondary); margin-top: 2px; }
  .sent-cell { text-align: center; min-width: 88px; }
  .sent-badge {
    display: inline-block; padding: 3px 10px; border-radius: 20px;
    font-size: 10px; font-weight: 800; letter-spacing: .3px;
  }
  .sp { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
  .sn { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
  .su { background: var(--bg-gray-100); color: #374151; border: 1px solid var(--border-gray); }

  /* ── PAGINATION ── */
  .pager {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 20px; border-top: 1px solid var(--border-gray); flex-wrap: wrap; gap: 12px;
  }
  .pager-info { font-size: 13px; color: var(--text-secondary); font-weight: 600; }
  .pager-btns { display: flex; align-items: center; gap: 4px; }
  .pbtn {
    width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-gray);
    background: var(--bg-white); color: var(--text-secondary); font-size: 12px; font-weight: 700;
    cursor: pointer; transition: all .15s; display: flex; align-items: center; justify-content: center;
  }
  .pbtn:hover:not(:disabled) { border-color: var(--primary-green); color: var(--primary-green); }
  .pbtn.active { background: var(--primary-green); border-color: var(--primary-green); color: #fff; }
  .pbtn:disabled { opacity: .35; cursor: not-allowed; }

  /* ── SKELETON ── */
  .sk {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite; border-radius: 8px;
  }
  @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

  /* ── EMPTY STATE ── */
  .empty-state { text-align: center; padding: 72px 20px; color: var(--text-secondary); }
  .empty-state svg { width: 44px; height: 44px; margin-bottom: 12px; stroke: currentColor; fill: none; stroke-width: 1.5; }
  .empty-state h4 { font-size: 16px; font-weight: 700; color: var(--text-primary); }
  .empty-state p  { font-size: 13px; margin-top: 5px; }

  /* ── ALERT ── */
  .alert-warn {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px; background: #fffbeb; border: 1px solid #fcd34d;
    border-radius: var(--r); font-size: 13px; font-weight: 600; color: #92400e; margin-bottom: 24px;
  }

  /* ── PROGRESS BAR ── */
  .progress-bar-wrap { height: 3px; background: var(--bg-gray-100); overflow: hidden; }
  .progress-bar { height: 100%; background: linear-gradient(90deg, var(--primary-green), #34d399); width: 0%; transition: width .4s ease; }

  @media(max-width:768px){
    .dashboard-container{ padding: 16px; }
    .stats-grid{ grid-template-columns: repeat(2,1fr); }
    .stat-item{ border-right: none; border-bottom: 1px solid var(--border-gray); }
    .content-cell{ max-width: 200px; }
    .cals-wrap{ flex-direction: column; }
    .dp-box{ flex-direction: column; }
    .dp-sidebar{ width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <div class="page-header">
    <h1>Mentions Timeline</h1>
    <p>Track all media mentions across Online News, Twitter, Facebook, Instagram, YouTube &amp; TikTok</p>
  </div>

  @if(!$projectId)
  <div class="alert-warn">
    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;flex-shrink:0;stroke-width:2">
      <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    No project selected. Please select a project from the sidebar.
  </div>
  @else

  {{-- FILTER CARD --}}
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.news.timeline') }}" style="display:contents">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hStart" value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hEnd"   value="{{ $endDate }}">
      <div class="filter-content">
        <div class="filter-group">
          <label class="filter-label">Date Range</label>
          <button type="button" class="date-trigger" id="dpTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0;color:var(--text-secondary)">
              <rect x="3" y="4" width="18" height="18" rx="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="dpDisplay">{{ $startDate }} &rarr; {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:var(--text-secondary)">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
        </div>
        <div class="filter-group">
          <label class="filter-label" style="opacity:0;pointer-events:none;">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24" style="width:15px;height:15px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Apply Filter
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- DATE PICKER MODAL --}}
  <div class="dp-modal" id="dpModal">
    <div style="position:absolute;inset:0;cursor:pointer" id="dpOverlay"></div>
    <div class="dp-box">
      <div class="dp-sidebar">
        <button class="dp-preset" data-p="today">Today</button>
        <button class="dp-preset" data-p="yesterday">Yesterday</button>
        <button class="dp-preset" data-p="7d">Last 7 Days</button>
        <button class="dp-preset" data-p="30d">Last 30 Days</button>
        <button class="dp-preset" data-p="thismonth">This Month</button>
        <button class="dp-preset" data-p="lastmonth">Last Month</button>
        <button class="dp-preset active" data-p="custom">Custom Range</button>
      </div>
      <div class="dp-body">
        <div class="dp-nav">
          <button type="button" class="nav-arrow" id="dpPrev">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:17px;height:17px"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="cals-wrap">
            <div class="cal" id="dpCal1"></div>
            <div class="cal" id="dpCal2"></div>
          </div>
          <button type="button" class="nav-arrow" id="dpNext">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:17px;height:17px"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="dp-display" id="dpRange">{{ $startDate }} &rarr; {{ $endDate }}</div>
        <div class="dp-footer">
          <button class="btn-cancel" id="dpCancel">Cancel</button>
          <button class="btn-apply-dp" id="dpApply">Apply</button>
        </div>
      </div>
    </div>
  </div>

  {{-- STATS CARD --}}
  <div class="do-card">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <span class="do-head-icon">
          <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </span>
        <span class="do-card-title">Overview</span>
      </div>
      <span class="do-badge">All Media Types</span>
    </div>

    {{-- Row 1: Platform counts --}}
    <div class="stats-grid">
      @php
        $platforms = [
          ['All Media','all',''],
          ['Online News','doc','c-news'],
          ['Twitter','twit','c-twit'],
          ['Facebook','fb','c-fb'],
          ['Instagram','ig','c-ig'],
          ['YouTube','ytb','c-ytb'],
          ['TikTok','tiktok','c-tiktok'],
        ];
      @endphp
      @foreach($platforms as $p)
      <div class="stat-item">
        <div class="stat-lbl">{{ $p[0] }}</div>
        <div class="stat-num {{ $p[2] }}" id="sn-{{ $p[1] }}">
          <div class="sk" style="height:32px;width:64px;margin:0 auto;"></div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="stats-divider"></div>

    {{-- Row 2: Sentiment --}}
    <div class="stats-grid">
      @php
        $rows2 = [
          ['All Mentions','all-m',''],
          ['Relevant','rel','c-twit'],
          ['Irrelevant','irr','c-neg'],
          ['Positive','pos','c-pos'],
          ['Negative','neg','c-neg'],
          ['Neutral','neu','c-neu'],
        ];
      @endphp
      @foreach($rows2 as $r)
      <div class="stat-item">
        <div class="stat-lbl">{{ $r[0] }}</div>
        <div class="stat-num {{ $r[2] }}" id="sn-{{ $r[1] }}">
          <div class="sk" style="height:32px;width:64px;margin:0 auto;"></div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="progress-bar-wrap">
      <div class="progress-bar" id="loadProgress"></div>
    </div>
  </div>

  {{-- CHART CARD --}}
  <div class="do-card">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <span class="do-head-icon">
          <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        </span>
        <div>
          <div class="do-card-title">Mentions Trend</div>
          <div style="font-size:12px;color:var(--text-secondary);margin-top:2px;font-weight:500;">Daily volume per media platform — click legend to toggle</div>
        </div>
      </div>
      <div class="chart-controls">
        <div class="chart-toggle">
          <button id="chartBtnLine" class="ct-btn active" onclick="setChartMode('line')">Line</button>
          <button id="chartBtnBar"  class="ct-btn"        onclick="setChartMode('bar')">Bar</button>
          <button id="chartBtnLog"  class="ct-btn"        onclick="setChartMode('log')" title="Logarithmic scale">Log</button>
        </div>
        <div class="legend" id="chartLegend">
          <div class="sk" style="height:18px;width:360px;"></div>
        </div>
      </div>
    </div>
    <div style="padding:20px;">
      <div class="chart-wrap">
        <div class="sk" id="chartSk" style="height:100%;"></div>
        <canvas id="volChart" style="display:none"></canvas>
      </div>
    </div>
  </div>

  {{-- TABLE CARD --}}
  <div class="do-card" style="overflow:visible;">
    <div class="table-head">
      <div class="table-head-info">
        <h3>Mentions Timeline</h3>
        <p id="tblSub">Loading data from all platforms…</p>
      </div>
      <div class="search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="searchInput" placeholder="Search mentions…" oninput="doSearch()">
      </div>
    </div>

    {{-- TABS --}}
    <div class="media-tabs" id="mediaTabs">
      @php
        $tabs = [
          ['all',    'All'],
          ['doc',    'Online News'],
          ['twit',   'Twitter'],
          ['fb',     'Facebook'],
          ['ig',     'Instagram'],
          ['ytb',    'YouTube'],
          ['tiktok', 'TikTok'],
        ];
      @endphp
      @foreach($tabs as $tab)
      <button class="media-tab {{ $tab[0]==='all'?'active':'' }}" data-tab="{{ $tab[0] }}" onclick="switchTab('{{ $tab[0] }}')">
        {{ $tab[1] }}
        <span class="tab-cnt" id="tc-{{ $tab[0] }}">
          <span class="tab-spinner"></span>
        </span>
      </button>
      @endforeach
    </div>

    <div style="overflow-x:auto">
      <div id="tblLoading" style="padding:20px">
        <div class="sk" style="height:380px;"></div>
      </div>

      <table class="mentions-table" id="mainTbl" style="display:none">
        <thead>
          <tr>
            <th class="c">No</th>
            <th>Media</th>
            <th>Type</th>
            <th>Content</th>
            <th class="c">Engagement</th>
            <th>Author</th>
            <th>Date</th>
            <th class="c">Sentiment</th>
          </tr>
        </thead>
        <tbody id="tblBody"></tbody>
      </table>

      <div id="emptyState" style="display:none">
        <div class="empty-state">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <h4>No Mentions Found</h4>
          <p>No data available for the selected filters.</p>
        </div>
      </div>
    </div>

    <div class="pager" id="pager" style="display:none"></div>
  </div>

  @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ═══════════════════════════════════════════════════════════════════
// DATE PICKER
// ═══════════════════════════════════════════════════════════════════
(function(){
  let s=null,e=null,m1=new Date(),m2=new Date(),picking=true;
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];

  document.addEventListener('DOMContentLoaded',()=>{
    const sv=document.getElementById('hStart').value;
    const ev=document.getElementById('hEnd').value;
    s = sv ? new Date(sv) : (()=>{ const d=new Date(); d.setDate(d.getDate()-6); return d; })();
    e = ev ? new Date(ev) : new Date();
    m1=new Date(s); m2=new Date(s); m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('dpTrigger').onclick = () => document.getElementById('dpModal').classList.add('open');
    document.getElementById('dpOverlay').onclick  = close;
    document.getElementById('dpCancel').onclick   = close;
    document.getElementById('dpApply').onclick    = apply;
    document.getElementById('dpPrev').onclick     = () => { m1.setMonth(m1.getMonth()-1); m2.setMonth(m2.getMonth()-1); render(); };
    document.getElementById('dpNext').onclick     = () => { m1.setMonth(m1.getMonth()+1); m2.setMonth(m2.getMonth()+1); render(); };
    document.querySelectorAll('.dp-preset').forEach(b => b.onclick = preset);
    document.addEventListener('keydown', k => { if(k.key==='Escape') close(); });
  });

  function close(){ document.getElementById('dpModal').classList.remove('open'); }
  function fmt(d){ if(!d)return''; return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
  function same(a,b){ return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }

  function apply(){
    const fs=fmt(s), fe=fmt(e);
    document.getElementById('hStart').value = fs;
    document.getElementById('hEnd').value   = fe;
    document.getElementById('dpDisplay').textContent = fs+' → '+fe;
    close();
  }

  function preset(ev){
    document.querySelectorAll('.dp-preset').forEach(b=>b.classList.remove('active'));
    ev.currentTarget.classList.add('active');
    const p=ev.currentTarget.dataset.p, t=new Date(); t.setHours(0,0,0,0);
    if(p==='today'){ s=new Date(t); e=new Date(t); }
    else if(p==='yesterday'){ s=new Date(t); s.setDate(t.getDate()-1); e=new Date(s); }
    else if(p==='7d'){ e=new Date(t); s=new Date(t); s.setDate(t.getDate()-6); }
    else if(p==='30d'){ e=new Date(t); s=new Date(t); s.setDate(t.getDate()-29); }
    else if(p==='thismonth'){ s=new Date(t.getFullYear(),t.getMonth(),1); e=new Date(t); }
    else if(p==='lastmonth'){ s=new Date(t.getFullYear(),t.getMonth()-1,1); e=new Date(t.getFullYear(),t.getMonth(),0); }
    if(p!=='custom'){ m1=new Date(s); m2=new Date(s); m2.setMonth(m2.getMonth()+1); render(); }
  }

  function render(){ renderCal('dpCal1',m1); renderCal('dpCal2',m2); document.getElementById('dpRange').textContent=(fmt(s)||'…')+' → '+(fmt(e)||'…'); }

  function renderCal(id,month){
    const el=document.getElementById(id); if(!el)return;
    const yr=month.getFullYear(), mo=month.getMonth();
    const first=new Date(yr,mo,1), last=new Date(yr,mo+1,0), prev=new Date(yr,mo,0);
    const today=new Date(); today.setHours(0,0,0,0);
    let h=`<div class="cal-month">${MN[mo]} ${yr}</div><div class="cal-grid">`;
    WD.forEach(d => h+=`<div class="wday">${d}</div>`);
    for(let i=0;i<first.getDay();i++) h+=`<button class="cday other">${prev.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const dt=new Date(yr,mo,d); dt.setHours(0,0,0,0);
      const ds=fmt(dt); let c='cday';
      if(same(dt,today)) c+=' today';
      if(dt>today) c+=' dis';
      if(s&&e){ if(same(dt,s)||same(dt,e)) c+=' sel'; else if(dt>s&&dt<e) c+=' range'; }
      h+=`<button class="${c}" data-d="${ds}" ${dt>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h+=`<button class="cday other">${i}</button>`;
    h+='</div>'; el.innerHTML=h;
    el.querySelectorAll('.cday:not(.other):not(.dis)').forEach(b=>b.onclick=clickDay);
  }

  function clickDay(ev){
    document.querySelectorAll('.dp-preset').forEach(b=>b.classList.remove('active'));
    document.querySelector('[data-p="custom"]').classList.add('active');
    const dt=new Date(ev.currentTarget.dataset.d); dt.setHours(0,0,0,0);
    if(picking||dt<s){ s=dt; e=dt; picking=false; }
    else{ if(dt>=s) e=dt; else{ e=s; s=dt; } picking=true; }
    render();
  }
})();

// ═══════════════════════════════════════════════════════════════════
// CONFIG & STATE
// ═══════════════════════════════════════════════════════════════════
const PID = '{{ $projectId ?? "" }}';
const SD  = '{{ $startDate ?? "" }}';
const ED  = '{{ $endDate ?? "" }}';

const PLATFORM_CFG = {
  doc:    { label:'Online News', color:'#3b82f6' },
  twit:   { label:'Twitter',     color:'#0ea5e9' },
  fb:     { label:'Facebook',    color:'#6366f1' },
  ig:     { label:'Instagram',   color:'#ec4899' },
  ytb:    { label:'YouTube',     color:'#ef4444' },
  tiktok: { label:'TikTok',      color:'#6b7280' },
};

const store = { all:[], doc:[], twit:[], fb:[], ig:[], ytb:[], tiktok:[] };
let activeTab='all', filtered=[], page=1;
const PER=100;
let q='', loadedCount=0;
const TOTAL_APIS=5;

// ═══════════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════════
const fmt   = n => new Intl.NumberFormat('en-US').format(n);
const esc   = t => { const d=document.createElement('div'); d.textContent=t; return d.innerHTML; };
const strip = h => h ? h.replace(/<[^>]*>/g,'').trim() : '';

function fmtDate(str){
  if(!str) return{d:'—',t:''};
  try{
    const dt=new Date(str);
    return{
      d:dt.toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}),
      t:dt.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})
    };
  }catch(e){ return{d:str,t:''}; }
}

function initials(n){
  if(!n||n==='Unknown') return '?';
  const p=n.trim().split(/\s+/);
  return p.length===1 ? p[0].slice(0,2).toUpperCase() : (p[0][0]+p[p.length-1][0]).toUpperCase();
}

function sentBadge(v){
  const s=String(v||'0');
  if(s==='1'||s.toLowerCase()==='positive'||s.toLowerCase()==='positif') return `<span class="sent-badge sp">Positive</span>`;
  if(s==='-1'||s.toLowerCase()==='negative'||s.toLowerCase()==='negatif') return `<span class="sent-badge sn">Negative</span>`;
  return `<span class="sent-badge su">Neutral</span>`;
}

function mediaBadge(platform){
  const map={
    doc:   ['mb-doc','News'],
    twit:  ['mb-twit','Twitter'],
    fb:    ['mb-fb','Facebook'],
    ig:    ['mb-ig','Instagram'],
    ytb:   ['mb-ytb','YouTube'],
    tiktok:['mb-tiktok','TikTok'],
  };
  const [cls,lbl]=map[platform]||['mb-doc','Other'];
  return `<span class="media-badge ${cls}">${lbl}</span>`;
}

function norm(item, platform){
  const authorHandle = item.author?.scr_name||item.author?.username||item.author_scr_name||item.author_id||'';
  const authorName   = item.author?.name||item.author_name||authorHandle;
  const rawAvatar    = item.avatar_url||item.author?.image||item.image||'';
  const avatarUrl    = rawAvatar&&rawAvatar.startsWith('http') ? rawAvatar : '';
  return {
    _platform:      platform,
    content:        strip(item.content||item.name||''),
    author_name:    authorName,
    author_handle:  authorHandle,
    avatar_url:     avatarUrl,
    hostname:       item.hostname||(item.url?(new URL(item.url.startsWith('http')?item.url:'https://'+item.url)).hostname:'')||'',
    url:            item.url||'',
    date_created:   item.date_created||'',
    num_likes:      parseInt(item.num_likes||item.likes||item.freq||item.rt||0,10),
    num_comments:   parseInt(item.num_comments||item.comments||0,10),
    num_shares:     parseInt(item.num_shares||item.shares||0,10),
    num_views:      parseInt(item.view_cnt||item.views||item.num_views||0,10),
    num_retweeted:  parseInt(item.rt||item.num_retweeted||0,10),
    num_followers:  parseInt(item.num_followers||item.followers||item.author?.flw_cnt||0,10),
    class_sentiment:String(item.class_sentiment||item.sentiment||item.sentiment_id||'0'),
    mention_type:   item.mention_type||item.tcode||'post',
  };
}

function detectPlatform(item){
  const mt=String(item.media_type_id||item.media_type||item.tcode||'').toLowerCase();
  const id=String(item.id||item.docid||'').toLowerCase();
  const url=String(item.url||'').toLowerCase();
  if(mt==='1'||mt.includes('twit')) return 'twit';
  if(mt==='2'||mt.includes('fb')||mt.includes('facebook')) return 'fb';
  if(mt==='3'||mt.includes('ig')||mt.includes('instagram')) return 'ig';
  if(mt==='4'||mt.includes('ytb')||mt.includes('youtube')) return 'ytb';
  if(mt==='6'||mt.includes('tiktok')) return 'tiktok';
  if(id.startsWith('in-')) return 'ig';
  if(id.startsWith('fb-')) return 'fb';
  if(id.startsWith('yt-')) return 'ytb';
  if(id.startsWith('twit-')||id.startsWith('tw-')) return 'twit';
  if(id.startsWith('tiktok-')||id.startsWith('tt-')) return 'tiktok';
  if(url.includes('instagram.com')) return 'ig';
  if(url.includes('facebook.com')||url.includes('fb.com')) return 'fb';
  if(url.includes('youtube.com')||url.includes('youtu.be')) return 'ytb';
  if(url.includes('tiktok.com')) return 'tiktok';
  if(url.includes('twitter.com')||url.includes('x.com')) return 'twit';
  return 'doc';
}

function tickProgress(){
  loadedCount++;
  document.getElementById('loadProgress').style.width = Math.round((loadedCount/TOTAL_APIS)*100)+'%';
}

// ═══════════════════════════════════════════════════════════════════
// FETCH
// ═══════════════════════════════════════════════════════════════════
if(PID && SD && ED){
  const BASE='/mk/api/news', XB='/mk/api/x';
  async function safeGet(url){ try{ const r=await fetch(url); return await r.json(); }catch(e){ return null; } }
  async function fetchMentions(){ const r=await safeGet(`${BASE}/mentions?project_id=${PID}&start_date=${SD}&end_date=${ED}`); tickProgress(); if(!r?.success||!Array.isArray(r.data)) return []; return r.data.map(m=>norm(m,detectPlatform(m))); }
  async function fetchTwitter(){ const r=await safeGet(`${XB}/most-status?project_id=${PID}&start_date=${SD}&end_date=${ED}&media=all&mention_type=view_all`); tickProgress(); if(!r?.data||!Array.isArray(r.data)) return []; return r.data.map(m=>norm(m,'twit')); }
  async function fetchFacebook(){ const r=await safeGet(`${BASE}/fb-top-status?project_id=${PID}&start_date=${SD}&end_date=${ED}&rows=100`); tickProgress(); if(!r?.success||!Array.isArray(r.data)) return []; return r.data.map(m=>norm(m,'fb')); }
  async function fetchInstagram(){ const r=await safeGet(`${BASE}/ig-top-status?project_id=${PID}&start_date=${SD}&end_date=${ED}&rows=100`); tickProgress(); if(!r?.success||!Array.isArray(r.data)) return []; return r.data.map(m=>norm(m,'ig')); }
  async function fetchTiktok(){ const r=await safeGet(`${BASE}/tiktok-top-status?project_id=${PID}&start_date=${SD}&end_date=${ED}&rows=100`); tickProgress(); if(!r?.success||!Array.isArray(r.data)) return []; return r.data.map(m=>norm(m,'tiktok')); }

  Promise.all([fetchMentions(),fetchTwitter(),fetchFacebook(),fetchInstagram(),fetchTiktok()])
  .then(([mentions,twit,fb,ig,tiktok])=>{
    const mDoc=mentions.filter(m=>m._platform==='doc');
    const mTwit=mentions.filter(m=>m._platform==='twit');
    const mFb=mentions.filter(m=>m._platform==='fb');
    const mIg=mentions.filter(m=>m._platform==='ig');
    const mYtb=mentions.filter(m=>m._platform==='ytb');
    const mTiktok=mentions.filter(m=>m._platform==='tiktok');
    store.doc=mDoc;
    store.twit=twit.length>0?twit:mTwit;
    store.fb=fb.length>0?fb:mFb;
    store.ig=ig.length>0?ig:mIg;
    store.ytb=mYtb;
    store.tiktok=tiktok.length>0?tiktok:mTiktok;
    store.all=[...store.doc,...store.twit,...store.fb,...store.ig,...store.ytb,...store.tiktok]
      .sort((a,b)=>new Date(b.date_created)-new Date(a.date_created));
    updateTabCounts();
    renderStats();
    renderChart();
    switchTab('all');
  }).catch(err=>console.error('Fatal fetch error:',err));
}

// ═══════════════════════════════════════════════════════════════════
// TABS
// ═══════════════════════════════════════════════════════════════════
function updateTabCounts(){
  ['all','doc','twit','fb','ig','ytb','tiktok'].forEach(k=>{
    const el=document.getElementById('tc-'+k);
    if(el) el.innerHTML=fmt(k==='all'?store.all.length:(store[k]||[]).length);
  });
}

// ═══════════════════════════════════════════════════════════════════
// STATS
// ═══════════════════════════════════════════════════════════════════
function renderStats(){
  const pMap={all:store.all.length,doc:store.doc.length,twit:store.twit.length,fb:store.fb.length,ig:store.ig.length,ytb:store.ytb.length,tiktok:store.tiktok.length};
  Object.entries(pMap).forEach(([k,v])=>{ const el=document.getElementById('sn-'+k); if(el) el.textContent=fmt(v); });
  document.getElementById('sn-all-m').textContent=fmt(store.all.length);
  const pos=store.all.filter(m=>['1','positive','positif'].includes(String(m.class_sentiment).toLowerCase())).length;
  const neg=store.all.filter(m=>['-1','negative','negatif'].includes(String(m.class_sentiment).toLowerCase())).length;
  document.getElementById('sn-pos').textContent=fmt(pos);
  document.getElementById('sn-neg').textContent=fmt(neg);
  document.getElementById('sn-neu').textContent=fmt(store.all.length-pos-neg);
  document.getElementById('sn-rel').textContent=fmt(store.all.length);
  document.getElementById('sn-irr').textContent='0';
}

// ═══════════════════════════════════════════════════════════════════
// CHART
// ═══════════════════════════════════════════════════════════════════
let chartMode='line', chartInstance=null;
const hiddenPlatforms=new Set();

function setChartMode(mode){
  chartMode=mode;
  ['line','bar','log'].forEach(m=>{
    const btn=document.getElementById('chartBtn'+m.charAt(0).toUpperCase()+m.slice(1));
    if(!btn) return;
    btn.classList.toggle('active', m===mode);
  });
  renderChart();
}

function renderChart(){
  const canvas=document.getElementById('volChart');
  const sk=document.getElementById('chartSk');
  if(!canvas) return;
  const start=new Date(SD), end=new Date(ED);
  const dates=[];
  for(let d=new Date(start);d<=end;d.setDate(d.getDate()+1)) dates.push(d.toISOString().split('T')[0]);
  const platforms=['doc','twit','fb','ig','ytb','tiktok'];
  const isLog=chartMode==='log', isBar=chartMode==='bar';
  const datasets=platforms.map(p=>{
    const cfg=PLATFORM_CFG[p];
    const dayMap={};
    (store[p]||[]).forEach(m=>{ if(!m.date_created) return; const day=(m.date_created+'').split('T')[0].split(' ')[0]; dayMap[day]=(dayMap[day]||0)+1; });
    const data=dates.map(d=>dayMap[d]||0);
    return{
      label:cfg.label, data:isLog?data.map(v=>v>0?v:0.1):data,
      borderColor:cfg.color, backgroundColor:isBar?cfg.color+'cc':cfg.color+'18',
      borderWidth:isBar?0:2.5, tension:isBar?0:0.4, fill:false,
      pointRadius:isBar?0:(data.length>30?2:4), pointHoverRadius:6,
      pointBorderColor:'#fff', pointBorderWidth:1.5,
      hidden:hiddenPlatforms.has(p), _platform:p,
    };
  });

  document.getElementById('chartLegend').innerHTML=platforms.map(p=>{
    const cfg=PLATFORM_CFG[p], total=(store[p]||[]).length, off=hiddenPlatforms.has(p);
    return `<div class="legend-item" onclick="togglePlatform('${p}')" style="opacity:${off?.4:1}">
      <div class="legend-dot" style="background:${cfg.color}"></div>
      <span>${cfg.label}</span>
      <span class="legend-cnt" style="color:${cfg.color};background:${cfg.color}18">${fmt(total)}</span>
    </div>`;
  }).join('');

  if(chartInstance) chartInstance.destroy();
  const yConfig=isLog
    ?{ type:'logarithmic',beginAtZero:false,min:0.1,grid:{color:'rgba(0,0,0,.04)'},ticks:{color:'#94a3b8',font:{size:11},callback:v=>v>=1?fmt(Math.round(v)):''} }
    :{ beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{color:'#94a3b8',font:{size:11},precision:0} };

  chartInstance=new Chart(canvas.getContext('2d'),{
    type:isBar?'bar':'line', data:{ labels:dates.map(d=>{ const dt=new Date(d+'T00:00:00'); return dt.toLocaleDateString('en-US',{month:'short',day:'numeric'}); }), datasets },
    options:{
      responsive:true, maintainAspectRatio:false,
      interaction:{mode:'index',intersect:false},
      plugins:{
        legend:{display:false},
        tooltip:{
          backgroundColor:'#1a202c', padding:14, cornerRadius:10,
          titleColor:'#fff', bodyColor:'#d1d5db',
          titleFont:{size:13,weight:'700'}, bodyFont:{size:12},
          displayColors:true, boxWidth:10, boxHeight:10,
          callbacks:{label:ctx=>` ${ctx.dataset.label}: ${fmt(Math.round(ctx.parsed.y))}`}
        }
      },
      scales:{
        y:yConfig,
        x:{
          stacked:isBar, grid:{display:false},
          ticks:{color:'#94a3b8',font:{size:11},maxRotation:45,autoSkip:true,maxTicksLimit:14}
        }
      }
    }
  });
  sk.style.display='none';
  canvas.style.display='block';
}

function togglePlatform(p){
  if(hiddenPlatforms.has(p)) hiddenPlatforms.delete(p);
  else hiddenPlatforms.add(p);
  renderChart();
}

// ═══════════════════════════════════════════════════════════════════
// TABLE
// ═══════════════════════════════════════════════════════════════════
function switchTab(tab){
  activeTab=tab; q=''; page=1;
  document.getElementById('searchInput').value='';
  document.querySelectorAll('.media-tab').forEach(b=>b.classList.toggle('active',b.dataset.tab===tab));
  buildFiltered(); renderTable();
}

function doSearch(){
  q=document.getElementById('searchInput').value.toLowerCase();
  page=1; buildFiltered(); renderTable();
}

function buildFiltered(){
  const src=activeTab==='all'?store.all:(store[activeTab]||[]);
  filtered=q ? src.filter(m=>(m.content||'').toLowerCase().includes(q)||(m.author_name||'').toLowerCase().includes(q)||(m.author_handle||'').toLowerCase().includes(q)||(m.hostname||'').toLowerCase().includes(q)) : [...src];
}

function renderTable(){
  const loading=document.getElementById('tblLoading');
  const table=document.getElementById('mainTbl');
  const empty=document.getElementById('emptyState');
  const tbody=document.getElementById('tblBody');
  loading.style.display='none';
  if(!filtered.length){
    table.style.display='none'; empty.style.display='block';
    document.getElementById('pager').style.display='none';
    document.getElementById('tblSub').textContent='No mentions found';
    return;
  }
  const from=(page-1)*PER;
  const slice=filtered.slice(from,from+PER);
  tbody.innerHTML=slice.map((item,i)=>{
    const rank=from+i+1;
    const dt=fmtDate(item.date_created);
    const initl=initials(item.author_name||item.author_handle);
    const handle=item.author_handle||item.hostname.replace('www.','').split('.')[0]||'';
    const dname=item.author_name||handle||'Unknown';
    const domain=item.hostname.replace('www.','');
    let avaInner=initl;
    if(item.avatar_url&&item.avatar_url.startsWith('http')){ avaInner=`<img src="${esc(item.avatar_url)}" alt="${esc(dname)}" onerror="this.parentElement.innerHTML='${initl}'">`; }
    else if(item._platform==='twit'&&handle){ avaInner=`<img src="https://unavatar.io/twitter/${esc(handle)}" alt="${esc(dname)}" onerror="this.parentElement.innerHTML='${initl}'">`; }
    else if(item._platform==='ig'&&handle){ avaInner=`<img src="https://unavatar.io/instagram/${esc((item.author_handle||handle).toLowerCase())}" alt="${esc(dname)}" onerror="this.parentElement.innerHTML='${initl}'">`; }
    else if(item._platform==='tiktok'&&handle){ avaInner=`<img src="https://unavatar.io/tiktok/${esc((item.author_handle||handle).toLowerCase())}" alt="${esc(dname)}" onerror="this.parentElement.innerHTML='${initl}'">`; }
    else if(item._platform==='doc'&&domain){ avaInner=`<img src="https://logo.clearbit.com/${esc(domain)}" alt="${esc(dname)}" onerror="this.parentElement.innerHTML='${initl}'">`; }
    else if(item._platform==='ytb'&&handle){ avaInner=`<img src="https://unavatar.io/youtube/${esc(handle)}" alt="${esc(dname)}" onerror="this.parentElement.innerHTML='${initl}'">`; }
    const likesNum=item.num_likes||item.num_retweeted||0;
    let engagements='';
    if(item.num_views>0) engagements+=`<div style="font-size:10px;color:var(--text-secondary);font-weight:600">${fmt(item.num_views)} views</div>`;
    if(item.num_comments>0) engagements+=`<div style="font-size:10px;color:var(--text-secondary);font-weight:600">${fmt(item.num_comments)} comments</div>`;
    if(item.num_shares>0) engagements+=`<div style="font-size:10px;color:var(--text-secondary);font-weight:600">${fmt(item.num_shares)} shares</div>`;
    if(item.num_followers>0) engagements+=`<div style="font-size:10px;color:var(--text-secondary);font-weight:600">${fmt(item.num_followers)} followers</div>`;
    return `<tr>
      <td class="no-cell">${rank}</td>
      <td class="media-cell">${mediaBadge(item._platform)}</td>
      <td><span class="type-badge">${esc(item.mention_type||'-')}</span></td>
      <td class="content-cell">
        <div class="mention-text">${esc(item.content||'No content')}</div>
        <div class="mention-meta">
          ${item.hostname?`<span>${esc(item.hostname)}</span>`:''}
          ${item.url&&item.url!=='#'?`<a href="${esc(item.url)}" target="_blank" rel="noopener noreferrer" class="view-link">View source</a>`:''}
        </div>
      </td>
      <td class="num-cell" style="vertical-align:middle;">
        <div style="font-weight:700;font-size:14px;color:var(--text-primary)">${likesNum?fmt(likesNum):'<span style="color:var(--border-gray)">—</span>'}</div>
        ${engagements}
      </td>
      <td class="author-cell">
        <div class="author-wrap">
          <div class="ava">${avaInner}</div>
          <div>
            <div class="aname" title="${esc(dname)}">${esc(dname)}</div>
            <div class="ahandle">${esc(handle)}</div>
          </div>
        </div>
      </td>
      <td class="date-cell">
        <div class="date-main">${dt.d}</div>
        <div class="date-time">${dt.t}</div>
      </td>
      <td class="sent-cell">${sentBadge(item.class_sentiment)}</td>
    </tr>`;
  }).join('');
  table.style.display='table'; empty.style.display='none';
  const totalPages=Math.ceil(filtered.length/PER);
  const toIdx=Math.min(page*PER,filtered.length);
  document.getElementById('tblSub').textContent=`Showing ${fmt(from+1)}–${fmt(toIdx)} of ${fmt(filtered.length)} mentions`;
  renderPager(totalPages);
}

function renderPager(total){
  const wrap=document.getElementById('pager');
  if(total<=1){ wrap.style.display='none'; return; }
  const from=(page-1)*PER+1, to=Math.min(page*PER,filtered.length);
  function range(cur,tot){
    if(tot<=7) return Array.from({length:tot},(_,i)=>i+1);
    if(cur<=4) return[1,2,3,4,5,'…',tot];
    if(cur>=tot-3) return[1,'…',tot-4,tot-3,tot-2,tot-1,tot];
    return[1,'…',cur-1,cur,cur+1,'…',tot];
  }
  let h=`<div class="pager-info">Showing ${fmt(from)}–${fmt(to)} of ${fmt(filtered.length)}</div><div class="pager-btns">`;
  h+=`<button class="pbtn" onclick="goPage(${page-1})" ${page===1?'disabled':''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="15 18 9 12 15 6"/></svg></button>`;
  range(page,total).forEach(p=>{
    h+=p==='…'?`<button class="pbtn" disabled style="cursor:default;font-size:14px">…</button>`:`<button class="pbtn ${p===page?'active':''}" onclick="goPage(${p})">${p}</button>`;
  });
  h+=`<button class="pbtn" onclick="goPage(${page+1})" ${page===total?'disabled':''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="9 18 15 12 9 6"/></svg></button></div>`;
  wrap.innerHTML=h; wrap.style.display='flex';
}

function goPage(p){
  const tot=Math.ceil(filtered.length/PER);
  if(p<1||p>tot) return;
  page=p; renderTable();
  document.querySelector('.do-card:last-child').scrollIntoView({behavior:'smooth',block:'start'});
}

window.switchTab=switchTab;
window.doSearch=doSearch;
window.goPage=goPage;
window.setChartMode=setChartMode;
window.togglePlatform=togglePlatform;
</script>
@endsection