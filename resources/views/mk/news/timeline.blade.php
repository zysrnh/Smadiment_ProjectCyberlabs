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
  .date-picker-modal {
    position: fixed; top:0; left:0; right:0; bottom:0;
    z-index: 10000; display: none;
    align-items: center; justify-content: center;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(8px);
  }
  .date-picker-modal.show { display: flex; }
  .date-picker-overlay { position:absolute; top:0; left:0; right:0; bottom:0; cursor:pointer; }
  .date-picker-container {
    position: relative; background: #ffffff; border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    display: flex; max-width: 900px; width: 90%; max-height: 90vh;
    z-index: 10001; animation: dpUp 0.3s ease-out;
  }
  @keyframes dpUp {
    from { opacity:0; transform:translateY(20px) scale(0.95); }
    to   { opacity:1; transform:translateY(0) scale(1); }
  }
  .date-picker-sidebar {
    width: 180px; background: var(--bg-gray-50);
    border-right: 1px solid var(--border-gray);
    padding: 16px 12px; border-radius: 16px 0 0 16px;
    display: flex; flex-direction: column; gap: 4px; flex-shrink: 0;
  }
  .date-preset {
    padding: 10px 16px; background: transparent; border: none;
    border-radius: 8px; font-size: 13px; font-weight: 500; color: var(--text-primary);
    text-align: left; cursor: pointer; transition: all 0.2s;
  }
  .date-preset:hover  { background: var(--bg-white); color: var(--primary-green); }
  .date-preset.active { background: var(--primary-green); color: white; }
  .date-picker-content { flex:1; padding:24px; display:flex; flex-direction:column; overflow:hidden; }
  .date-picker-header  { display:flex; align-items:flex-start; gap:20px; margin-bottom:20px; }
  .nav-btn {
    width:36px; height:36px; border-radius:8px;
    background:var(--bg-gray-50); border:1px solid var(--border-gray);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all 0.2s; flex-shrink:0;
  }
  .nav-btn:hover { background:var(--primary-green); border-color:var(--primary-green); color:white; }
  .nav-btn svg { width:20px; height:20px; }
  .calendars-wrapper { display:flex; gap:24px; flex:1; min-height:0; }
  .calendar { flex:1; display:flex; flex-direction:column; min-width:0; }
  .calendar-month { font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:16px; text-align:center; }
  .calendar-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:8px; }
  .weekday { text-align:center; font-size:11px; font-weight:700; color:var(--text-secondary); padding:8px 0; }
  .calendar-days { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
  .calendar-day {
    aspect-ratio:1; display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:500; border-radius:8px; cursor:pointer;
    transition:all 0.2s; color:var(--text-primary); background:transparent; border:none; padding:0;
  }
  .calendar-day:hover:not(.disabled):not(.other-month) { background:var(--bg-gray-100); }
  .calendar-day.other-month { color:#cbd5e1; cursor:default; }
  .calendar-day.disabled    { color:#e2e8f0; cursor:not-allowed; }
  .calendar-day.today       { border:2px solid var(--primary-green); }
  .calendar-day.selected    { background:var(--primary-green); color:white; }
  .calendar-day.in-range    { background:rgba(3,128,71,0.1); color:var(--primary-green); }
  .calendar-day.range-start,
  .calendar-day.range-end   { background:var(--primary-green); color:white; }
  .date-picker-display {
    padding:16px 20px; background:var(--bg-gray-50); border-radius:12px;
    text-align:center; margin-bottom:20px; border:1px solid var(--border-gray);
  }
  .date-picker-display span { font-size:14px; font-weight:600; color:var(--text-primary); }
  .date-picker-footer { display:flex; gap:12px; justify-content:flex-end; }
  .cancel-btn, .apply-date-btn {
    padding:10px 24px; border-radius:10px;
    font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; border:none;
  }
  .cancel-btn { background:var(--bg-gray-100); color:var(--text-primary); }
  .cancel-btn:hover { background:var(--border-gray); }
  .apply-date-btn {
    background:linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    color:white; box-shadow:0 4px 12px rgba(3,128,71,0.2);
  }
  .apply-date-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(3,128,71,0.3); }

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

  /* ── OVERVIEW STATS (donut layout) ── */
  .overview-body {
    display: grid;
    grid-template-columns: 1fr 1px 1fr;
    min-height: 240px;
  }
  .overview-panel { padding: 24px 28px; display: flex; flex-direction: column; justify-content: center; }
  .overview-divider { background: var(--border-gray); }
  .overview-title {
    font-size: 11px; font-weight: 700; color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: .6px; margin-bottom: 18px;
    display: flex; align-items: center; gap: 8px;
  }
  .overview-title-dot { width: 8px; height: 8px; border-radius: 50%; }
  .donut-row { display: flex; align-items: center; gap: 20px; }
  .donut-canvas-wrap { flex-shrink: 0; position: relative; width: 150px; height: 150px; }
  .donut-center-text {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
    text-align: center; pointer-events: none;
  }
  .donut-center-num  { font-size: 19px; font-weight: 800; color: var(--text-primary); line-height: 1; }
  .donut-center-lbl  { font-size: 9px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .5px; margin-top: 3px; }
  .donut-legend { display: flex; flex-direction: column; gap: 7px; flex: 1; }
  .dl-item { display: flex; align-items: center; gap: 8px; }
  .dl-dot  { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
  .dl-name { font-size: 12px; font-weight: 600; color: var(--text-secondary); flex: 1; }
  .dl-val  { font-size: 13px; font-weight: 800; color: var(--text-primary); }
  .dl-pct  { font-size: 10px; font-weight: 600; color: var(--text-secondary); min-width: 34px; text-align: right; }
  .progress-bar-wrap { height: 3px; background: var(--bg-gray-100); overflow: hidden; }
  .progress-bar { height: 100%; background: linear-gradient(90deg, var(--primary-green), #34d399); width: 0%; transition: width .4s ease; }
  @media(max-width:768px){
    .overview-body { grid-template-columns: 1fr; }
    .overview-divider { height: 1px; width: auto; }
    .donut-canvas-wrap { width: 120px; height: 120px; }
  }

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
    .content-cell{ max-width: 200px; }
    .filter-content{ flex-direction: column; align-items: stretch; }
    .date-trigger{ min-width: auto; }
    .apply-btn{ width: 100%; justify-content: center; }
    .date-picker-container{ flex-direction: column; max-height: 85vh; overflow-y: auto; width: 95%; }
    .date-picker-sidebar{ width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; padding: 12px 16px; }
    .date-preset{ white-space: nowrap; }
    .calendars-wrapper{ flex-direction: column; gap: 16px; }
    .cancel-btn, .apply-date-btn{ flex: 1; }
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
  <div class="date-picker-modal" id="datePickerModal">
    <div class="date-picker-overlay" id="dpOverlay"></div>
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
          <button type="button" class="nav-btn" id="dpPrev">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="dpCal1"></div>
            <div class="calendar" id="dpCal2"></div>
          </div>
          <button type="button" class="nav-btn" id="dpNext">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="date-picker-display">
          <span id="dpRangeText">{{ $startDate }} to {{ $endDate }}</span>
        </div>
        <div class="date-picker-footer">
          <button type="button" class="cancel-btn" id="dpCancel">Cancel</button>
          <button type="button" class="apply-date-btn" id="dpApply">Apply</button>
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

    <div class="overview-body">

      {{-- LEFT: Platform Donut --}}
      <div class="overview-panel">
        <div class="overview-title">
          <div class="overview-title-dot" style="background:var(--primary-green)"></div>
          Mentions by Platform
        </div>
        <div class="donut-row">
          <div class="donut-canvas-wrap">
            <div class="sk" id="skPlatform" style="width:150px;height:150px;border-radius:50%;"></div>
            <canvas id="donutPlatform" width="150" height="150" style="display:none"></canvas>
            <div class="donut-center-text">
              <div class="donut-center-num" id="donutTotalPlatform">—</div>
              <div class="donut-center-lbl">Total</div>
            </div>
          </div>
          <div class="donut-legend" id="legendPlatform">
            @foreach([['doc','#3b82f6'],['twit','#0ea5e9'],['fb','#6366f1'],['ig','#ec4899'],['ytb','#ef4444'],['tiktok','#6b7280']] as $p)
            <div class="dl-item">
              <div class="dl-dot" style="background:{{ $p[1] }}"></div>
              <div class="dl-name" id="dlname-{{ $p[0] }}">{{ ucfirst($p[0]) }}</div>
              <div class="dl-val" id="dlval-{{ $p[0] }}"><div class="sk" style="height:14px;width:40px;"></div></div>
              <div class="dl-pct" id="dlpct-{{ $p[0] }}"></div>
            </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="overview-divider"></div>

      {{-- RIGHT: Sentiment Donut --}}
      <div class="overview-panel">
        <div class="overview-title">
          <div class="overview-title-dot" style="background:#f59e0b"></div>
          Sentiment Distribution
        </div>
        <div class="donut-row">
          <div class="donut-canvas-wrap">
            <div class="sk" id="skSentiment" style="width:150px;height:150px;border-radius:50%;"></div>
            <canvas id="donutSentiment" width="150" height="150" style="display:none"></canvas>
            <div class="donut-center-text">
              <div class="donut-center-num" id="donutTotalSent">—</div>
              <div class="donut-center-lbl">Total</div>
            </div>
          </div>
          <div class="donut-legend" id="legendSentiment">
            @foreach([['pos','Positive','#16a34a'],['neg','Negative','#ef4444'],['neu','Neutral','#94a3b8']] as $s)
            <div class="dl-item">
              <div class="dl-dot" style="background:{{ $s[2] }}"></div>
              <div class="dl-name">{{ $s[1] }}</div>
              <div class="dl-val" id="dlval-{{ $s[0] }}"><div class="sk" style="height:14px;width:40px;"></div></div>
              <div class="dl-pct" id="dlpct-{{ $s[0] }}"></div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
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
// DATE PICKER  (same logic as Data Overview — proven working)
// ═══════════════════════════════════════════════════════════════════
(function () {
  'use strict';
  let ds = null, de = null;
  let m1 = new Date(), m2 = new Date();
  let pickStart = true;

  document.addEventListener('DOMContentLoaded', function () {
    const si = document.getElementById('hStart');
    const ei = document.getElementById('hEnd');
    ds = si && si.value ? new Date(si.value) : (() => { const d=new Date(); d.setDate(d.getDate()-6); return d; })();
    de = ei && ei.value ? new Date(ei.value) : new Date();
    m1 = new Date(ds); m2 = new Date(ds); m2.setMonth(m2.getMonth()+1);
    renderCals();

    const trigger = document.getElementById('dpTrigger');
    if (trigger) trigger.addEventListener('click', open);
    const overlay = document.getElementById('dpOverlay');
    if (overlay) overlay.addEventListener('click', close);
    document.addEventListener('keydown', e => { if (e.key==='Escape') close(); });
    document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', preset));
    document.getElementById('dpPrev').addEventListener('click', () => { m1.setMonth(m1.getMonth()-1); m2.setMonth(m2.getMonth()-1); renderCals(); });
    document.getElementById('dpNext').addEventListener('click', () => { m1.setMonth(m1.getMonth()+1); m2.setMonth(m2.getMonth()+1); renderCals(); });
    document.getElementById('dpApply').addEventListener('click', apply);
    document.getElementById('dpCancel').addEventListener('click', close);
  });

  function open()  { document.getElementById('datePickerModal').classList.add('show'); renderCals(); }
  function close() { document.getElementById('datePickerModal').classList.remove('show'); }

  function preset(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    const today = new Date(); today.setHours(0,0,0,0);
    switch(e.target.dataset.preset) {
      case 'today':      ds=new Date(today); de=new Date(today); break;
      case 'yesterday':  ds=new Date(today); ds.setDate(today.getDate()-1); de=new Date(ds); break;
      case 'last7days':  de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-6); break;
      case 'last30days': de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-29); break;
      case 'thismonth':  ds=new Date(today.getFullYear(),today.getMonth(),1); de=new Date(today); break;
      case 'lastmonth':  ds=new Date(today.getFullYear(),today.getMonth()-1,1); de=new Date(today.getFullYear(),today.getMonth(),0); break;
    }
    if (e.target.dataset.preset !== 'custom') {
      m1=new Date(ds); m2=new Date(ds); m2.setMonth(m2.getMonth()+1);
      updateDisp(); renderCals();
    }
  }

  function apply() {
    document.getElementById('hStart').value = fmt(ds);
    document.getElementById('hEnd').value   = fmt(de);
    document.getElementById('dpDisplay').textContent = `${fmt(ds)} → ${fmt(de)}`;
    close();
  }

  function renderCals() { renderCal('dpCal1', m1); renderCal('dpCal2', m2); updateDisp(); }

  function renderCal(id, month) {
    const el = document.getElementById(id); if (!el) return;
    const y=month.getFullYear(), mn=month.getMonth();
    const first=new Date(y,mn,1), last=new Date(y,mn+1,0), prevL=new Date(y,mn,0);
    const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
    const today=new Date(); today.setHours(0,0,0,0);
    let h=`<div class="calendar-month">${MN[mn]} ${y}</div>
      <div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div>
      <div class="calendar-days">`;
    for(let i=0;i<first.getDay();i++)
      h+=`<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const date=new Date(y,mn,d); date.setHours(0,0,0,0);
      let cls='calendar-day';
      if(sameDay(date,today)) cls+=' today';
      if(date>today) cls+=' disabled';
      if(ds&&de){
        if(sameDay(date,ds)) cls+=' selected range-start';
        else if(sameDay(date,de)) cls+=' selected range-end';
        else if(date>ds&&date<de) cls+=' in-range';
      }
      h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>'; el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn=>{
      btn.addEventListener('click', function(){
        const d=new Date(this.dataset.date); d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-preset="custom"]').classList.add('active');
        if(pickStart || d<ds){ ds=d; de=d; pickStart=false; }
        else { if(d>=ds) de=d; else { de=ds; ds=d; } pickStart=true; }
        updateDisp(); renderCals();
      });
    });
  }

  function updateDisp(){
    const el=document.getElementById('dpRangeText');
    if(el&&ds&&de) el.textContent=`${fmt(ds)} to ${fmt(de)}`;
    const disp=document.getElementById('dpDisplay');
    if(disp&&ds&&de) disp.textContent=`${fmt(ds)} → ${fmt(de)}`;
  }
  function fmt(d){ if(!d)return''; return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
  function sameDay(a,b){ return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
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
// STATS — renders two donut charts
// ═══════════════════════════════════════════════════════════════════
const PLAT_CFG_DONUT = [
  { key:'doc',    label:'Online News', color:'#3b82f6' },
  { key:'twit',   label:'Twitter',     color:'#0ea5e9' },
  { key:'fb',     label:'Facebook',    color:'#6366f1' },
  { key:'ig',     label:'Instagram',   color:'#ec4899' },
  { key:'ytb',    label:'YouTube',     color:'#ef4444' },
  { key:'tiktok', label:'TikTok',      color:'#6b7280' },
];

function renderStats(){
  const fmtN = n => new Intl.NumberFormat('en-US').format(n);

  // ── Platform donut ──
  const platVals = PLAT_CFG_DONUT.map(p => (store[p.key]||[]).length);
  const platTotal = platVals.reduce((a,b)=>a+b,0);

  document.getElementById('donutTotalPlatform').textContent = fmtN(platTotal);
  PLAT_CFG_DONUT.forEach((p,i) => {
    const v = platVals[i];
    const pct = platTotal > 0 ? ((v/platTotal)*100).toFixed(1) : '0.0';
    const nEl = document.getElementById('dlval-'+p.key);
    const pEl = document.getElementById('dlpct-'+p.key);
    const nameEl = document.getElementById('dlname-'+p.key);
    if(nEl)    nEl.textContent    = fmtN(v);
    if(pEl)    pEl.textContent    = pct+'%';
    if(nameEl) nameEl.textContent = p.label;
  });

  document.getElementById('skPlatform').style.display = 'none';
  const cvP = document.getElementById('donutPlatform');
  cvP.style.display = 'block';
  new Chart(cvP.getContext('2d'), {
    type: 'doughnut',
    data: {
      labels: PLAT_CFG_DONUT.map(p=>p.label),
      datasets: [{ data: platVals, backgroundColor: PLAT_CFG_DONUT.map(p=>p.color), borderColor:'#fff', borderWidth:3, hoverOffset:8 }]
    },
    options: {
      responsive: false, cutout:'68%',
      plugins: {
        legend: { display:false },
        tooltip: {
          backgroundColor:'#1a202c', padding:12, cornerRadius:10,
          titleColor:'#fff', bodyColor:'#d1d5db',
          titleFont:{size:12,weight:'700'}, bodyFont:{size:11},
          callbacks:{label:ctx=>{const t=platVals.reduce((a,b)=>a+b,0); return ` ${ctx.label}: ${fmtN(ctx.parsed)} (${t>0?((ctx.parsed/t)*100).toFixed(1):0}%)`;}}
        }
      }
    }
  });

  // ── Sentiment donut ──
  const pos = store.all.filter(m=>['1','positive','positif'].includes(String(m.class_sentiment).toLowerCase())).length;
  const neg = store.all.filter(m=>['-1','negative','negatif'].includes(String(m.class_sentiment).toLowerCase())).length;
  const neu = store.all.length - pos - neg;
  const sentTotal = store.all.length;

  document.getElementById('donutTotalSent').textContent = fmtN(sentTotal);
  [['pos',pos,'#16a34a'],['neg',neg,'#ef4444'],['neu',neu,'#94a3b8']].forEach(([k,v,c]) => {
    const pct = sentTotal > 0 ? ((v/sentTotal)*100).toFixed(1) : '0.0';
    const vEl = document.getElementById('dlval-'+k);
    const pEl = document.getElementById('dlpct-'+k);
    if(vEl) vEl.textContent = fmtN(v);
    if(pEl) pEl.textContent = pct+'%';
  });

  document.getElementById('skSentiment').style.display = 'none';
  const cvS = document.getElementById('donutSentiment');
  cvS.style.display = 'block';
  new Chart(cvS.getContext('2d'), {
    type: 'doughnut',
    data: {
      labels: ['Positive','Negative','Neutral'],
      datasets: [{ data:[pos,neg,neu], backgroundColor:['#16a34a','#ef4444','#94a3b8'], borderColor:'#fff', borderWidth:3, hoverOffset:8 }]
    },
    options: {
      responsive: false, cutout:'68%',
      plugins: {
        legend: { display:false },
        tooltip: {
          backgroundColor:'#1a202c', padding:12, cornerRadius:10,
          titleColor:'#fff', bodyColor:'#d1d5db',
          titleFont:{size:12,weight:'700'}, bodyFont:{size:11},
          callbacks:{label:ctx=>{return ` ${ctx.label}: ${fmtN(ctx.parsed)} (${sentTotal>0?((ctx.parsed/sentTotal)*100).toFixed(1):0}%)`;}}
        }
      }
    }
  });
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