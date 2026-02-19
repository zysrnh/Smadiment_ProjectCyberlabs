@extends('mk.layouts.app')

@section('title', 'Mentions Timeline - SMADIMENT')

@section('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

  :root {
    --green: #038047;
    --green-dark: #026738;
    --blue: #3b82f6;
    --red: #ef4444;
    --amber: #f59e0b;
    --purple: #8b5cf6;
    --pink: #ec4899;
    --sky: #0ea5e9;
    --indigo: #6366f1;
    --gray-900: #111827;
    --gray-700: #374151;
    --gray-500: #6b7280;
    --gray-400: #9ca3af;
    --gray-300: #d1d5db;
    --gray-100: #f3f4f6;
    --gray-50:  #f9fafb;
    --white:    #ffffff;
    --border:   #e5e7eb;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.07);
    --shadow-md: 0 4px 12px rgba(0,0,0,.09);
    --shadow-lg: 0 10px 30px rgba(0,0,0,.12);
    --r: 12px;
    --rs: 8px;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Plus Jakarta Sans', sans-serif; }

  /* ── WRAP ── */
  .wrap {
    padding: 28px;
    background: var(--gray-50);
    min-height: 100vh;
    max-width: 1720px;
    margin: 0 auto;
  }

  /* ── PAGE HEAD ── */
  .page-head { margin-bottom: 24px; }
  .page-head h1 { font-size: 26px; font-weight: 800; color: var(--gray-900); letter-spacing: -.5px; }
  .page-head p  { font-size: 13px; color: var(--gray-500); margin-top: 4px; }

  /* ── FILTER BAR ── */
  .filter-bar {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--r);
    padding: 14px 20px;
    margin-bottom: 24px;
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    box-shadow: var(--shadow-sm);
  }
  .filter-label { font-size: 13px; font-weight: 700; color: var(--gray-700); display:flex;align-items:center;gap:6px;white-space:nowrap; }
  .date-trigger {
    display:flex;align-items:center;gap:10px;
    padding:9px 16px;
    background:var(--gray-50);border:1.5px solid var(--border);border-radius:var(--rs);
    font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:600;
    color:var(--gray-900);cursor:pointer;transition:all .2s;min-width:280px;
  }
  .date-trigger:hover { border-color:var(--green);background:var(--white); }
  .apply-btn {
    display:flex;align-items:center;gap:8px;padding:9px 22px;
    background:linear-gradient(135deg,var(--green),var(--green-dark));
    color:#fff;border:none;border-radius:var(--rs);
    font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:700;
    cursor:pointer;transition:all .2s;box-shadow:0 4px 12px rgba(3,128,71,.25);
  }
  .apply-btn:hover { transform:translateY(-1px);box-shadow:0 6px 18px rgba(3,128,71,.35); }

  /* ── DATE PICKER MODAL ── */
  .dp-modal {
    position:fixed;inset:0;z-index:9999;
    display:none;align-items:center;justify-content:center;
    background:rgba(0,0,0,.5);backdrop-filter:blur(6px);
  }
  .dp-modal.open { display:flex; }
  .dp-box {
    background:#fff;border-radius:18px;
    box-shadow:0 30px 60px rgba(0,0,0,.25);
    display:flex;max-width:860px;width:92%;max-height:90vh;overflow:auto;
    animation:popIn .25s ease-out;
  }
  @keyframes popIn { from{opacity:0;transform:scale(.94) translateY(12px)} to{opacity:1;transform:scale(1) translateY(0)} }
  .dp-sidebar {
    width:165px;background:var(--gray-50);border-right:1px solid var(--border);
    border-radius:18px 0 0 18px;padding:16px 12px;
    display:flex;flex-direction:column;gap:3px;flex-shrink:0;
  }
  .dp-preset {
    padding:8px 14px;border:none;border-radius:7px;
    font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:600;
    color:var(--gray-700);text-align:left;cursor:pointer;background:transparent;transition:all .15s;
  }
  .dp-preset:hover { background:#fff;color:var(--green); }
  .dp-preset.active { background:var(--green);color:#fff; }
  .dp-body { flex:1;padding:22px;display:flex;flex-direction:column;gap:14px; }
  .dp-nav { display:flex;align-items:flex-start;gap:14px; }
  .nav-arrow {
    width:33px;height:33px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
    border:1px solid var(--border);border-radius:7px;background:var(--gray-50);cursor:pointer;transition:all .15s;
  }
  .nav-arrow:hover { background:var(--green);border-color:var(--green);color:#fff; }
  .cals-wrap { display:flex;gap:18px;flex:1; }
  .cal { flex:1; }
  .cal-month { font-size:14px;font-weight:800;color:var(--gray-900);text-align:center;margin-bottom:12px; }
  .cal-grid { display:grid;grid-template-columns:repeat(7,1fr);gap:2px; }
  .wday { text-align:center;font-size:10px;font-weight:700;color:var(--gray-500);padding:5px 0; }
  .cday {
    aspect-ratio:1;display:flex;align-items:center;justify-content:center;
    font-size:12px;font-weight:600;border-radius:6px;cursor:pointer;border:none;
    background:transparent;transition:all .15s;color:var(--gray-900);
    font-family:'Plus Jakarta Sans',sans-serif;
  }
  .cday:hover:not(.other):not(.dis) { background:var(--gray-100); }
  .cday.other  { color:var(--gray-300);pointer-events:none; }
  .cday.dis    { color:var(--gray-300);pointer-events:none; }
  .cday.today  { border:2px solid var(--green); }
  .cday.sel    { background:var(--green);color:#fff; }
  .cday.range  { background:rgba(3,128,71,.1);color:var(--green); }
  .dp-display {
    background:var(--gray-50);border:1px solid var(--border);border-radius:9px;
    padding:11px 18px;font-size:13px;font-weight:700;color:var(--gray-900);text-align:center;
  }
  .dp-footer { display:flex;gap:10px;justify-content:flex-end; }
  .btn-cancel {
    padding:9px 20px;background:var(--gray-100);border:none;border-radius:7px;
    font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:700;
    cursor:pointer;color:var(--gray-700);
  }
  .btn-apply-dp {
    padding:9px 22px;background:linear-gradient(135deg,var(--green),var(--green-dark));
    color:#fff;border:none;border-radius:7px;
    font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:700;cursor:pointer;
  }

  /* ── STATS SECTION (DroneEmprit style) ── */
  .stats-card {
    background:var(--white);border:1px solid var(--border);border-radius:var(--r);
    padding:20px 24px;margin-bottom:24px;box-shadow:var(--shadow-sm);
  }
  .stats-card h2 {
    font-size:13px;font-weight:700;color:var(--gray-500);
    text-transform:uppercase;letter-spacing:.6px;
    margin-bottom:14px;padding-bottom:12px;border-bottom:2px solid var(--gray-100);
  }
  .stats-row {
    display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));
  }
  .stat-item {
    padding:12px 18px;border-right:1px solid var(--border);text-align:center;
  }
  .stat-item:last-child { border-right:none; }
  .stat-lbl { font-size:11px;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px; }
  .stat-num { font-size:28px;font-weight:800;color:var(--gray-900);line-height:1; }
  .stat-num.c-all    { color:var(--gray-900); }
  .stat-num.c-news   { color:var(--blue); }
  .stat-num.c-twit   { color:var(--sky); }
  .stat-num.c-fb     { color:var(--indigo); }
  .stat-num.c-ig     { color:var(--pink); }
  .stat-num.c-ytb    { color:var(--red); }
  .stat-num.c-tiktok { color:var(--gray-700); }
  .stat-num.c-pos    { color:#16a34a; }
  .stat-num.c-neg    { color:var(--red); }
  .stat-num.c-neu    { color:var(--gray-500); }
  .stat-row2 { margin-top:14px;padding-top:14px;border-top:1px solid var(--gray-100); }

  /* ── CHART ── */
  .chart-card {
    background:var(--white);border:1px solid var(--border);border-radius:var(--r);
    padding:22px 24px;margin-bottom:24px;box-shadow:var(--shadow-sm);
  }
  .chart-head {
    display:flex;justify-content:space-between;align-items:center;
    margin-bottom:18px;padding-bottom:14px;border-bottom:2px solid var(--gray-100);
    flex-wrap:wrap;gap:12px;
  }
  .chart-head h3 { font-size:15px;font-weight:800;color:var(--gray-900); }
  .chart-head p  { font-size:12px;color:var(--gray-500);margin-top:2px; }
  .legend { display:flex;flex-wrap:wrap;gap:12px;align-items:center; }
  .legend-item { display:flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--gray-700); }
  .legend-dot  { width:9px;height:9px;border-radius:50%; }
  .chart-wrap  { position:relative;height:270px; }

  /* ── TABLE SECTION ── */
  .table-section {
    background:var(--white);border:1px solid var(--border);border-radius:var(--r);
    box-shadow:var(--shadow-sm);overflow:hidden;
  }
  .table-head {
    display:flex;justify-content:space-between;align-items:center;
    padding:18px 22px;border-bottom:2px solid var(--gray-100);flex-wrap:wrap;gap:12px;
  }
  .table-head h3 { font-size:15px;font-weight:800;color:var(--gray-900); }
  .table-head p  { font-size:12px;color:var(--gray-500);margin-top:2px; }
  .search-box { position:relative; }
  .search-box input {
    width:260px;padding:8px 14px 8px 38px;
    border:1.5px solid var(--border);border-radius:20px;
    font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;
    background:var(--gray-50);color:var(--gray-900);transition:all .2s;
  }
  .search-box input:focus {
    outline:none;border-color:var(--green);background:var(--white);
    box-shadow:0 0 0 3px rgba(3,128,71,.1);
  }
  .search-box svg { position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--gray-400); }

  /* ── MEDIA TABS ── */
  .media-tabs { display:flex;gap:6px;flex-wrap:wrap;padding:14px 22px 0; }
  .media-tab {
    display:flex;align-items:center;gap:7px;padding:7px 14px;
    background:var(--white);border:1.5px solid var(--border);border-radius:20px;
    font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:700;
    color:var(--gray-700);cursor:pointer;transition:all .2s;
  }
  .media-tab:hover { border-color:var(--green);color:var(--green); }
  .media-tab.active { background:var(--green);border-color:var(--green);color:#fff;box-shadow:0 4px 12px rgba(3,128,71,.2); }
  .tab-cnt {
    padding:1px 7px;border-radius:10px;font-size:10px;font-weight:800;
    background:rgba(255,255,255,.2);
  }
  .media-tab:not(.active) .tab-cnt { background:var(--gray-100);color:var(--gray-600); }

  /* ── LOADING SPINNER in tab ── */
  .tab-spinner {
    display:inline-block;width:10px;height:10px;
    border:2px solid rgba(255,255,255,.3);border-top-color:#fff;
    border-radius:50%;animation:spin .7s linear infinite;
  }
  .media-tab:not(.active) .tab-spinner {
    border-color:rgba(3,128,71,.2);border-top-color:var(--green);
  }
  @keyframes spin { to { transform:rotate(360deg); } }

  /* ── MENTIONS TABLE ── */
  .mentions-table { width:100%;border-collapse:collapse;font-family:'Plus Jakarta Sans',sans-serif; }
  .mentions-table thead { background:var(--gray-50);border-bottom:1.5px solid var(--border); }
  .mentions-table th {
    padding:11px 14px;text-align:left;font-size:10px;font-weight:800;
    color:var(--gray-500);text-transform:uppercase;letter-spacing:.7px;white-space:nowrap;
  }
  .mentions-table th.c { text-align:center; }
  .mentions-table td {
    padding:13px 14px;border-bottom:1px solid var(--gray-100);
    font-size:13px;color:var(--gray-900);vertical-align:middle;
  }
  .mentions-table tr:hover td { background:var(--gray-50); }
  .mentions-table tr:last-child td { border-bottom:none; }

  /* cells */
  .no-cell { width:42px;text-align:center;font-weight:800;color:var(--gray-400);font-size:12px; }
  .media-cell { width:90px;text-align:center; }
  .media-badge {
    display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:10px;
    font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.3px;white-space:nowrap;
  }
  .mb-doc    { background:#dbeafe;color:#1e40af;border:1px solid #93c5fd; }
  .mb-twit   { background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc; }
  .mb-fb     { background:#e0e7ff;color:#3730a3;border:1px solid #a5b4fc; }
  .mb-ig     { background:#fce7f3;color:#be185d;border:1px solid #f9a8d4; }
  .mb-ytb    { background:#fee2e2;color:#991b1b;border:1px solid #fca5a5; }
  .mb-tiktok { background:#f3f4f6;color:#1f2937;border:1px solid #d1d5db; }
  .type-badge {
    display:inline-block;padding:2px 7px;border-radius:5px;
    font-size:10px;font-weight:700;background:var(--gray-100);color:var(--gray-600);
  }
  .content-cell { max-width:440px; }
  .mention-text {
    font-size:13px;line-height:1.55;color:var(--gray-900);
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;
  }
  .mention-meta { display:flex;align-items:center;gap:9px;font-size:11px;color:var(--gray-500);flex-wrap:wrap; }
  .view-link {
    color:var(--green);font-weight:700;text-decoration:none;
    display:inline-flex;align-items:center;gap:3px;font-size:11px;transition:all .15s;
  }
  .view-link:hover { color:var(--green-dark);text-decoration:underline; }
  .num-cell { text-align:center;min-width:64px;font-weight:700;font-size:13px;color:var(--gray-700); }
  .author-cell { min-width:170px; }
  .author-wrap { display:flex;align-items:center;gap:10px; }
  .ava {
    width:38px;height:38px;border-radius:50%;border:2px solid var(--border);flex-shrink:0;
    background:linear-gradient(135deg,var(--green),var(--green-dark));
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-weight:800;font-size:13px;overflow:hidden;
  }
  .ava img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
  .aname {
    font-weight:700;font-size:13px;color:var(--gray-900);
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px;
  }
  .ahandle { font-size:11px;color:var(--gray-500);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px; }
  .date-cell { min-width:130px; }
  .date-main { font-weight:700;font-size:12px;color:var(--gray-900); }
  .date-time { font-size:11px;color:var(--gray-500);margin-top:2px; }
  .sent-cell { text-align:center;min-width:88px; }
  .sent-badge {
    display:inline-block;padding:3px 10px;border-radius:11px;
    font-size:10px;font-weight:800;letter-spacing:.3px;
  }
  .sp { background:#d1fae5;color:#065f46;border:1px solid #6ee7b7; }
  .sn { background:#fee2e2;color:#991b1b;border:1px solid #fca5a5; }
  .su { background:#f3f4f6;color:#374151;border:1px solid #d1d5db; }

  /* ── PAGINATION ── */
  .pager {
    display:flex;justify-content:space-between;align-items:center;
    padding:14px 20px;border-top:1px solid var(--border);flex-wrap:wrap;gap:12px;
  }
  .pager-info { font-size:13px;color:var(--gray-500);font-weight:600; }
  .pager-btns { display:flex;align-items:center;gap:4px; }
  .pbtn {
    width:32px;height:32px;border-radius:7px;border:1.5px solid var(--border);
    background:var(--white);color:var(--gray-700);font-size:12px;font-weight:700;
    cursor:pointer;transition:all .15s;display:flex;align-items:center;justify-content:center;
    font-family:'Plus Jakarta Sans',sans-serif;
  }
  .pbtn:hover:not(:disabled) { border-color:var(--green);color:var(--green); }
  .pbtn.active { background:var(--green);border-color:var(--green);color:#fff; }
  .pbtn:disabled { opacity:.35;cursor:not-allowed; }

  /* ── SKELETON ── */
  .sk {
    background:linear-gradient(90deg,var(--gray-100) 25%,#e9ecef 50%,var(--gray-100) 75%);
    background-size:200% 100%;animation:shimmer 1.4s infinite;border-radius:7px;
  }
  @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

  /* ── EMPTY ── */
  .empty-state { text-align:center;padding:72px 20px;color:var(--gray-500); }
  .empty-state svg { width:48px;height:48px;margin-bottom:12px;stroke:currentColor;fill:none; }
  .empty-state h4 { font-size:16px;font-weight:800;color:var(--gray-700); }
  .empty-state p  { font-size:13px;margin-top:5px; }

  /* ── ALERT ── */
  .alert-warn {
    display:flex;align-items:center;gap:12px;
    padding:14px 18px;background:#fffbeb;border:1px solid #fcd34d;
    border-radius:var(--r);font-size:13px;font-weight:600;color:#92400e;margin-bottom:24px;
  }

  /* ── PROGRESS BAR (loading indicator per platform) ── */
  .progress-bar-wrap { height:3px;background:var(--gray-100);border-radius:0 0 var(--r) var(--r);overflow:hidden; }
  .progress-bar { height:100%;background:linear-gradient(90deg,var(--green),#34d399);width:0%;transition:width .4s ease; }

  @media(max-width:768px){
    .wrap{padding:14px;}
    .stats-row{grid-template-columns:repeat(2,1fr);}
    .stat-item{border-right:none;border-bottom:1px solid var(--border);}
    .content-cell{max-width:220px;}
    .cals-wrap{flex-direction:column;}
    .dp-sidebar{width:100%;border-right:none;border-bottom:1px solid var(--border);border-radius:18px 18px 0 0;flex-direction:row;overflow-x:auto;}
    .dp-box{flex-direction:column;}
  }
</style>
@endsection

@section('content')
<div class="wrap">

  <div class="page-head">
    <h1>Mentions Timeline</h1>
    <p>Track all media mentions across Online News, Twitter, Facebook, Instagram, YouTube &amp; TikTok</p>
  </div>

  @if(!$projectId)
  <div class="alert-warn">
    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;flex-shrink:0">
      <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    No project selected. Please select a project from the sidebar.
  </div>
  @else

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <form id="filterForm" method="GET" action="{{ route('mk.news.timeline') }}" style="display:contents">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hStart" value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hEnd"   value="{{ $endDate }}">

      <div class="filter-label">
        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none">
          <rect x="3" y="4" width="18" height="18" rx="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Date Range
      </div>

      <button type="button" class="date-trigger" id="dpTrigger">
        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;color:#9ca3af">
          <rect x="3" y="4" width="18" height="18" rx="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span id="dpDisplay">{{ $startDate }} → {{ $endDate }}</span>
        <svg viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;margin-left:auto;color:#9ca3af">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </button>

      <button type="submit" class="apply-btn">
        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none">
          <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
        Apply Filter
      </button>
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
            <svg viewBox="0 0 24 24" style="width:17px;height:17px;stroke:currentColor;fill:none"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="cals-wrap">
            <div class="cal" id="dpCal1"></div>
            <div class="cal" id="dpCal2"></div>
          </div>
          <button type="button" class="nav-arrow" id="dpNext">
            <svg viewBox="0 0 24 24" style="width:17px;height:17px;stroke:currentColor;fill:none"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="dp-display" id="dpRange">{{ $startDate }} → {{ $endDate }}</div>
        <div class="dp-footer">
          <button class="btn-cancel" id="dpCancel">Cancel</button>
          <button class="btn-apply-dp" id="dpApply">Apply</button>
        </div>
      </div>
    </div>
  </div>

  {{-- STATS (DroneEmprit style) --}}
  <div class="stats-card">
    <h2>Source: All Media Types</h2>

    {{-- Row 1: platform counts --}}
    <div class="stats-row" id="statRow1">
      @php
        $platforms = [
          ['All Media Types','all','c-all'],
          ['Online News (Ind)','doc','c-news'],
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
          <div class="sk" style="height:32px;width:70px;margin:0 auto;border-radius:5px;"></div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Row 2: relevancy + sentiment --}}
    <div class="stats-row stat-row2" id="statRow2">
      @php
        $rows2 = [
          ['All Mentions','all-m','c-all'],
          ['Relevant','rel','c-twit'],
          ['Irrelevant','irr','c-neg'],
          ['Positives','pos','c-pos'],
          ['Negatives','neg','c-neg'],
          ['Neutral','neu','c-neu'],
        ];
      @endphp
      @foreach($rows2 as $r)
      <div class="stat-item">
        <div class="stat-lbl">{{ $r[0] }}</div>
        <div class="stat-num {{ $r[2] }}" id="sn-{{ $r[1] }}">
          <div class="sk" style="height:32px;width:70px;margin:0 auto;border-radius:5px;"></div>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Progress bar: loading indicator --}}
    <div class="progress-bar-wrap" style="margin:-20px -24px -20px;margin-top:16px;">
      <div class="progress-bar" id="loadProgress"></div>
    </div>
  </div>

  {{-- CHART --}}
  <div class="chart-card">
    <div class="chart-head">
      <div>
        <h3>Mentions &amp; Trends</h3>
        <p>Daily volume per media platform</p>
      </div>
      <div class="legend" id="chartLegend">
        <div class="sk" style="height:18px;width:340px;border-radius:5px;"></div>
      </div>
    </div>
    <div class="chart-wrap">
      <div class="sk" id="chartSk" style="height:100%;"></div>
      <canvas id="volChart" style="display:none"></canvas>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="table-section">
    <div class="table-head">
      <div>
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
          ['doc',    '📰 Online News'],
          ['twit',   '𝕏 Twitter'],
          ['fb',     '📘 Facebook'],
          ['ig',     '📷 Instagram'],
          ['ytb',    '▶️ YouTube'],
          ['tiktok', '🎵 TikTok'],
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
        <div class="sk" style="height:380px;border-radius:9px;"></div>
      </div>

      <table class="mentions-table" id="mainTbl" style="display:none">
        <thead>
          <tr>
            <th class="c">No</th>
            <th>Media</th>
            <th>Type</th>
            <th>Mentions</th>
            <th class="c">#Likes</th>
            <th>Author</th>
            <th>Date</th>
            <th class="c">Sentiments</th>
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
    s = sv ? new Date(sv) : (() => { const d=new Date(); d.setDate(d.getDate()-6); return d; })();
    e = ev ? new Date(ev) : new Date();
    m1=new Date(s); m2=new Date(s); m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('dpTrigger').onclick=()=>document.getElementById('dpModal').classList.add('open');
    document.getElementById('dpOverlay').onclick=close;
    document.getElementById('dpCancel').onclick=close;
    document.getElementById('dpApply').onclick=apply;
    document.getElementById('dpPrev').onclick=()=>{m1.setMonth(m1.getMonth()-1);m2.setMonth(m2.getMonth()-1);render();};
    document.getElementById('dpNext').onclick=()=>{m1.setMonth(m1.getMonth()+1);m2.setMonth(m2.getMonth()+1);render();};
    document.querySelectorAll('.dp-preset').forEach(b=>b.onclick=preset);
    document.addEventListener('keydown',k=>{if(k.key==='Escape')close();});
  });

  function close(){document.getElementById('dpModal').classList.remove('open');}
  function fmt(d){if(!d)return'';return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');}
  function same(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}

  function apply(){
    const fs=fmt(s),fe=fmt(e);
    document.getElementById('hStart').value=fs;
    document.getElementById('hEnd').value=fe;
    document.getElementById('dpDisplay').textContent=fs+' → '+fe;
    close();
  }

  function preset(ev){
    document.querySelectorAll('.dp-preset').forEach(b=>b.classList.remove('active'));
    ev.currentTarget.classList.add('active');
    const p=ev.currentTarget.dataset.p,t=new Date();t.setHours(0,0,0,0);
    if(p==='today'){s=new Date(t);e=new Date(t);}
    else if(p==='yesterday'){s=new Date(t);s.setDate(t.getDate()-1);e=new Date(s);}
    else if(p==='7d'){e=new Date(t);s=new Date(t);s.setDate(t.getDate()-6);}
    else if(p==='30d'){e=new Date(t);s=new Date(t);s.setDate(t.getDate()-29);}
    else if(p==='thismonth'){s=new Date(t.getFullYear(),t.getMonth(),1);e=new Date(t);}
    else if(p==='lastmonth'){s=new Date(t.getFullYear(),t.getMonth()-1,1);e=new Date(t.getFullYear(),t.getMonth(),0);}
    if(p!=='custom'){m1=new Date(s);m2=new Date(s);m2.setMonth(m2.getMonth()+1);render();}
  }

  function render(){
    renderCal('dpCal1',m1);renderCal('dpCal2',m2);
    document.getElementById('dpRange').textContent=(fmt(s)||'…')+' → '+(fmt(e)||'…');
  }

  function renderCal(id,month){
    const el=document.getElementById(id);if(!el)return;
    const yr=month.getFullYear(),mo=month.getMonth();
    const first=new Date(yr,mo,1),last=new Date(yr,mo+1,0),prev=new Date(yr,mo,0);
    const today=new Date();today.setHours(0,0,0,0);
    let h=`<div class="cal-month">${MN[mo]} ${yr}</div><div class="cal-grid">`;
    WD.forEach(d=>h+=`<div class="wday">${d}</div>`);
    for(let i=0;i<first.getDay();i++)h+=`<button class="cday other">${prev.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const dt=new Date(yr,mo,d);dt.setHours(0,0,0,0);
      const ds=fmt(dt);let c='cday';
      if(same(dt,today))c+=' today';
      if(dt>today)c+=' dis';
      if(s&&e){if(same(dt,s)||same(dt,e))c+=' sel';else if(dt>s&&dt<e)c+=' range';}
      h+=`<button class="${c}" data-d="${ds}" ${dt>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++)h+=`<button class="cday other">${i}</button>`;
    h+='</div>';el.innerHTML=h;
    el.querySelectorAll('.cday:not(.other):not(.dis)').forEach(b=>b.onclick=clickDay);
  }

  function clickDay(ev){
    document.querySelectorAll('.dp-preset').forEach(b=>b.classList.remove('active'));
    document.querySelector('[data-p="custom"]').classList.add('active');
    const dt=new Date(ev.currentTarget.dataset.d);dt.setHours(0,0,0,0);
    if(picking||dt<s){s=dt;e=dt;picking=false;}
    else{if(dt>=s)e=dt;else{e=s;s=dt;}picking=true;}
    render();
  }
})();

// ═══════════════════════════════════════════════════════════════════
// CONFIG
// ═══════════════════════════════════════════════════════════════════
const PID = '{{ $projectId ?? "" }}';
const SD  = '{{ $startDate ?? "" }}';
const ED  = '{{ $endDate ?? "" }}';

const PLATFORM_CFG = {
  doc:    { label:'Online News', color:'#3b82f6',  emoji:'📰' },
  twit:   { label:'Twitter',     color:'#0ea5e9',  emoji:'𝕏'  },
  fb:     { label:'Facebook',    color:'#6366f1',  emoji:'📘' },
  ig:     { label:'Instagram',   color:'#ec4899',  emoji:'📷' },
  ytb:    { label:'YouTube',     color:'#ef4444',  emoji:'▶️' },
  tiktok: { label:'TikTok',      color:'#6b7280',  emoji:'🎵' },
};

// ═══════════════════════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════════════════════
const store = { all:[], doc:[], twit:[], fb:[], ig:[], ytb:[], tiktok:[] };
let activeTab = 'all';
let filtered  = [];
let page      = 1;
const PER     = 100;
let q         = '';
let loadedCount = 0;
const TOTAL_APIS = 5; // mentions, twitter, fb, ig, tiktok

// ═══════════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════════
const fmt   = n => new Intl.NumberFormat('en-US').format(n);
const esc   = t => { const d=document.createElement('div');d.textContent=t;return d.innerHTML; };
const strip = h => h ? h.replace(/<[^>]*>/g,'').trim() : '';

function fmtDate(str){
  if(!str)return{d:'—',t:''};
  try{
    const dt=new Date(str);
    return{
      d:dt.toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}),
      t:dt.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})
    };
  }catch(e){return{d:str,t:''};}
}

function initials(n){
  if(!n||n==='Unknown')return'?';
  const p=n.trim().split(/\s+/);
  return p.length===1?p[0].slice(0,2).toUpperCase():(p[0][0]+p[p.length-1][0]).toUpperCase();
}

function sentBadge(v){
  const s=String(v||'0');
  if(s==='1'||s.toLowerCase()==='positive'||s.toLowerCase()==='positif')
    return `<span class="sent-badge sp">Positive</span>`;
  if(s==='-1'||s.toLowerCase()==='negative'||s.toLowerCase()==='negatif')
    return `<span class="sent-badge sn">Negative</span>`;
  return `<span class="sent-badge su">Neutral</span>`;
}

function mediaBadge(platform){
  const map={
    doc:   ['mb-doc','📰 News'],
    twit:  ['mb-twit','𝕏 Twitter'],
    fb:    ['mb-fb','📘 Facebook'],
    ig:    ['mb-ig','📷 Instagram'],
    ytb:   ['mb-ytb','▶️ YouTube'],
    tiktok:['mb-tiktok','🎵 TikTok'],
  };
  const [cls,lbl]=map[platform]||['mb-doc','📌 Other'];
  return `<span class="media-badge ${cls}">${lbl}</span>`;
}

// Normalise any API response item to common schema
function norm(item, platform){
  const authorHandle = item.author?.scr_name || item.author?.username || item.author_scr_name || item.author_id || '';
  const authorName   = item.author?.name || item.author_name || authorHandle;
  const rawAvatar    = item.avatar_url || item.author?.image || item.image || '';
  const avatarUrl    = rawAvatar && rawAvatar.startsWith('http') ? rawAvatar : '';

  return {
    _platform:      platform,
    content:        strip(item.content || item.name || ''),
    author_name:    authorName,
    author_handle:  authorHandle,
    avatar_url:     avatarUrl,
    hostname:       item.hostname || (item.url ? (new URL(item.url.startsWith('http')?item.url:'https://'+item.url)).hostname : '') || '',
    url:            item.url || '',
    date_created:   item.date_created || '',
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

// Detect platform from media_type / media_type_id field
function detectPlatform(item){
  const t = String(item.media_type_id||item.media_type||item.tcode||'').toLowerCase();
  if(t==='1'||t.includes('twit')) return 'twit';
  if(t==='2'||t.includes('fb')||t.includes('facebook')) return 'fb';
  if(t==='3'||t.includes('ig')||t.includes('instagram')) return 'ig';
  if(t==='4'||t.includes('ytb')||t.includes('youtube')) return 'ytb';
  if(t==='6'||t.includes('tiktok')) return 'tiktok';
  return 'doc';
}

// ═══════════════════════════════════════════════════════════════════
// PROGRESS BAR
// ═══════════════════════════════════════════════════════════════════
function tickProgress(){
  loadedCount++;
  const pct = Math.round((loadedCount/TOTAL_APIS)*100);
  document.getElementById('loadProgress').style.width = pct+'%';
}

// ═══════════════════════════════════════════════════════════════════
// FETCH ALL APIS IN PARALLEL
// ═══════════════════════════════════════════════════════════════════
if(PID && SD && ED){
  const BASE = `/mk/api/news`;
  const XB   = `/mk/api/x`;

  async function safeGet(url){
    try{ const r=await fetch(url); return await r.json(); }
    catch(e){ console.warn('Fetch failed:', url, e.message); return null; }
  }

  async function fetchMentions(){
    const r = await safeGet(`${BASE}/mentions?project_id=${PID}&start_date=${SD}&end_date=${ED}`);
    tickProgress();
    if(!r?.success || !Array.isArray(r.data)) return [];
    return r.data.map(m => norm(m, detectPlatform(m)));
  }

  async function fetchTwitter(){
    // Use existing x/most-status endpoint
    const r = await safeGet(`${XB}/most-status?project_id=${PID}&start_date=${SD}&end_date=${ED}&media=all&mention_type=view_all`);
    tickProgress();
    if(!r?.data || !Array.isArray(r.data)) return [];
    return r.data.map(m => norm(m, 'twit'));
  }

  async function fetchFacebook(){
    const r = await safeGet(`${BASE}/fb-top-status?project_id=${PID}&start_date=${SD}&end_date=${ED}&rows=100`);
    tickProgress();
    if(!r?.success || !Array.isArray(r.data)) return [];
    return r.data.map(m => norm(m, 'fb'));
  }

  async function fetchInstagram(){
    const r = await safeGet(`${BASE}/ig-top-status?project_id=${PID}&start_date=${SD}&end_date=${ED}&rows=100`);
    tickProgress();
    if(!r?.success || !Array.isArray(r.data)) return [];
    return r.data.map(m => norm(m, 'ig'));
  }

  async function fetchTiktok(){
    const r = await safeGet(`${BASE}/tiktok-top-status?project_id=${PID}&start_date=${SD}&end_date=${ED}&rows=100`);
    tickProgress();
    if(!r?.success || !Array.isArray(r.data)) return [];
    return r.data.map(m => norm(m, 'tiktok'));
  }

  // Fire all in parallel
  Promise.all([
    fetchMentions(),
    fetchTwitter(),
    fetchFacebook(),
    fetchInstagram(),
    fetchTiktok(),
  ]).then(([mentions, twit, fb, ig, tiktok]) => {

    // Segregate mentions by platform
    const mDoc    = mentions.filter(m => m._platform === 'doc');
    const mTwit   = mentions.filter(m => m._platform === 'twit');
    const mFb     = mentions.filter(m => m._platform === 'fb');
    const mIg     = mentions.filter(m => m._platform === 'ig');
    const mYtb    = mentions.filter(m => m._platform === 'ytb');
    const mTiktok = mentions.filter(m => m._platform === 'tiktok');

    // Merge: prefer dedicated top-status APIs (more data), supplement from mentions
    store.doc    = mDoc;
    store.twit   = twit.length > 0 ? twit : mTwit;
    store.fb     = fb.length   > 0 ? fb   : mFb;
    store.ig     = ig.length   > 0 ? ig   : mIg;
    store.ytb    = mYtb; // YouTube from mentions only
    store.tiktok = tiktok.length > 0 ? tiktok : mTiktok;

    // Combine all, sort by date desc
    store.all = [
      ...store.doc,
      ...store.twit,
      ...store.fb,
      ...store.ig,
      ...store.ytb,
      ...store.tiktok,
    ].sort((a,b) => new Date(b.date_created) - new Date(a.date_created));

    // Render everything
    updateTabCounts();
    renderStats();
    renderChart();
    switchTab('all');

  }).catch(err => {
    console.error('Fatal fetch error:', err);
  });
}

// ═══════════════════════════════════════════════════════════════════
// UPDATE TAB COUNTS
// ═══════════════════════════════════════════════════════════════════
function updateTabCounts(){
  const keys = ['all','doc','twit','fb','ig','ytb','tiktok'];
  keys.forEach(k => {
    const el = document.getElementById('tc-'+k);
    if(el) el.innerHTML = fmt(k==='all' ? store.all.length : (store[k]||[]).length);
  });
}

// ═══════════════════════════════════════════════════════════════════
// RENDER STATS
// ═══════════════════════════════════════════════════════════════════
function renderStats(){
  // Platform counts
  const pMap = {all:store.all.length, doc:store.doc.length, twit:store.twit.length, fb:store.fb.length, ig:store.ig.length, ytb:store.ytb.length, tiktok:store.tiktok.length};
  Object.entries(pMap).forEach(([k,v])=>{
    const el=document.getElementById('sn-'+k);
    if(el) el.textContent=fmt(v);
  });
  document.getElementById('sn-all-m').textContent=fmt(store.all.length);

  // Sentiment
  const pos=store.all.filter(m=>['1','positive','positif'].includes(String(m.class_sentiment).toLowerCase())).length;
  const neg=store.all.filter(m=>['-1','negative','negatif'].includes(String(m.class_sentiment).toLowerCase())).length;
  const neu=store.all.length-pos-neg;
  document.getElementById('sn-pos').textContent=fmt(pos);
  document.getElementById('sn-neg').textContent=fmt(neg);
  document.getElementById('sn-neu').textContent=fmt(neu);

  // Relevancy (for news, all are relevant by default)
  document.getElementById('sn-rel').textContent=fmt(store.all.length);
  document.getElementById('sn-irr').textContent='0';
}

// ═══════════════════════════════════════════════════════════════════
// RENDER VOLUME CHART
// ═══════════════════════════════════════════════════════════════════
function renderChart(){
  const canvas=document.getElementById('volChart');
  const sk=document.getElementById('chartSk');
  if(!canvas)return;

  const start=new Date(SD), end=new Date(ED);
  const dates=[];
  for(let d=new Date(start);d<=end;d.setDate(d.getDate()+1))
    dates.push(d.toISOString().split('T')[0]);

  const platforms=['doc','twit','fb','ig','ytb','tiktok'];

  const datasets = platforms.map(p => {
    const cfg = PLATFORM_CFG[p];
    const dayMap={};
    store[p].forEach(m=>{
      if(!m.date_created)return;
      const day=(m.date_created+'').split('T')[0].split(' ')[0];
      dayMap[day]=(dayMap[day]||0)+1;
    });
    return {
      label: cfg.label,
      data: dates.map(d=>dayMap[d]||0),
      borderColor: cfg.color,
      backgroundColor: cfg.color+'18',
      borderWidth: 2.5,
      tension: .4,
      fill: false,
      pointRadius: 3,
      pointHoverRadius: 6,
      pointBorderColor: '#fff',
      pointBorderWidth: 1.5,
    };
  });

  // Legend
  document.getElementById('chartLegend').innerHTML = platforms.map(p=>`
    <div class="legend-item">
      <div class="legend-dot" style="background:${PLATFORM_CFG[p].color}"></div>
      ${PLATFORM_CFG[p].label}
    </div>
  `).join('');

  const existing=Chart.getChart(canvas);
  if(existing)existing.destroy();

  new Chart(canvas.getContext('2d'),{
    type:'line',
    data:{
      labels:dates.map(d=>{
        const dt=new Date(d);
        return dt.toLocaleDateString('en-US',{month:'short',day:'numeric'});
      }),
      datasets
    },
    options:{
      responsive:true,maintainAspectRatio:false,
      interaction:{mode:'index',intersect:false},
      plugins:{
        legend:{display:false},
        tooltip:{
          backgroundColor:'#111827',padding:14,cornerRadius:10,
          titleColor:'#fff',bodyColor:'#d1d5db',
          titleFont:{size:13,weight:'700'},bodyFont:{size:12},
          displayColors:true,boxWidth:10,boxHeight:10,
        }
      },
      scales:{
        y:{beginAtZero:true,grid:{color:'#f3f4f6'},ticks:{color:'#6b7280',font:{size:11},precision:0}},
        x:{grid:{display:false},ticks:{color:'#6b7280',font:{size:11},maxRotation:45,autoSkip:true,maxTicksLimit:12}}
      }
    }
  });

  sk.style.display='none';
  canvas.style.display='block';
}

// ═══════════════════════════════════════════════════════════════════
// TABLE
// ═══════════════════════════════════════════════════════════════════
function switchTab(tab){
  activeTab=tab;q='';page=1;
  document.getElementById('searchInput').value='';
  document.querySelectorAll('.media-tab').forEach(b=>b.classList.toggle('active',b.dataset.tab===tab));
  buildFiltered();
  renderTable();
}

function doSearch(){
  q=document.getElementById('searchInput').value.toLowerCase();
  page=1;buildFiltered();renderTable();
}

function buildFiltered(){
  const src = activeTab==='all' ? store.all : (store[activeTab]||[]);
  filtered = q
    ? src.filter(m =>
        (m.content||'').toLowerCase().includes(q) ||
        (m.author_name||'').toLowerCase().includes(q) ||
        (m.author_handle||'').toLowerCase().includes(q) ||
        (m.hostname||'').toLowerCase().includes(q))
    : [...src];
}

function renderTable(){
  const loading = document.getElementById('tblLoading');
  const table   = document.getElementById('mainTbl');
  const empty   = document.getElementById('emptyState');
  const tbody   = document.getElementById('tblBody');

  loading.style.display='none';

  if(!filtered.length){
    table.style.display='none';
    empty.style.display='block';
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

    // Avatar logic
    let avaInner=initl;
    if(item.avatar_url){
      avaInner=`<img src="${esc(item.avatar_url)}" alt="${esc(dname)}" onerror="this.parentElement.textContent='${initl}'">`;
    } else if(item._platform==='twit'&&handle){
      avaInner=`<img src="https://unavatar.io/twitter/${esc(handle)}" alt="${esc(dname)}" onerror="this.parentElement.textContent='${initl}'">`;
    } else if(item._platform==='doc'&&domain){
      avaInner=`<img src="https://logo.clearbit.com/${esc(domain)}" alt="${esc(dname)}" onerror="this.parentElement.textContent='${initl}'">`;
    } else if(item._platform==='ig'&&handle){
      avaInner=`<img src="https://unavatar.io/instagram/${esc(handle)}" alt="${esc(dname)}" onerror="this.parentElement.textContent='${initl}'">`;
    } else if(item._platform==='tiktok'&&handle){
      avaInner=`<img src="https://unavatar.io/tiktok/${esc(handle)}" alt="${esc(dname)}" onerror="this.parentElement.textContent='${initl}'">`;
    }

    const likesNum = item.num_likes || item.num_retweeted || 0;

    // Extra engagement badge
    let extra='';
    if(item.num_views>0) extra+=`<span>👁 ${fmt(item.num_views)}</span>`;
    if(item.num_comments>0) extra+=`<span>💬 ${fmt(item.num_comments)}</span>`;
    if(item.num_shares>0)   extra+=`<span>🔁 ${fmt(item.num_shares)}</span>`;
    if(item.num_followers>0) extra+=`<span>👥 ${fmt(item.num_followers)}</span>`;

    return `<tr>
      <td class="no-cell">${rank}</td>
      <td class="media-cell">${mediaBadge(item._platform)}</td>
      <td><span class="type-badge">${esc(item.mention_type||'-')}</span></td>
      <td class="content-cell">
        <div class="mention-text">${esc(item.content||'No content')}</div>
        <div class="mention-meta">
          ${item.hostname?`<span>🌐 ${esc(item.hostname)}</span>`:''}
          ${item.url&&item.url!=='#'?`<a href="${esc(item.url)}" target="_blank" rel="noopener noreferrer" class="view-link">View ↗</a>`:''}
          ${extra}
        </div>
      </td>
      <td class="num-cell">${likesNum?fmt(likesNum):'<span style="color:#d1d5db">—</span>'}</td>
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

  table.style.display='table';
  empty.style.display='none';

  const totalPages=Math.ceil(filtered.length/PER);
  const toIdx=Math.min(page*PER,filtered.length);
  document.getElementById('tblSub').textContent=`Showing ${fmt(from+1)}–${fmt(toIdx)} of ${fmt(filtered.length)} mentions`;

  renderPager(totalPages);
}

function renderPager(total){
  const wrap=document.getElementById('pager');
  if(total<=1){wrap.style.display='none';return;}
  const from=(page-1)*PER+1, to=Math.min(page*PER,filtered.length);

  function range(cur,tot){
    if(tot<=7)return Array.from({length:tot},(_,i)=>i+1);
    if(cur<=4)return[1,2,3,4,5,'…',tot];
    if(cur>=tot-3)return[1,'…',tot-4,tot-3,tot-2,tot-1,tot];
    return[1,'…',cur-1,cur,cur+1,'…',tot];
  }

  let h=`<div class="pager-info">Showing ${fmt(from)}–${fmt(to)} of ${fmt(filtered.length)}</div>`;
  h+=`<div class="pager-btns">`;
  h+=`<button class="pbtn" onclick="goPage(${page-1})" ${page===1?'disabled':''}>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="15 18 9 12 15 6"/></svg>
  </button>`;
  range(page,total).forEach(p=>{
    h+= p==='…'
      ? `<button class="pbtn" disabled style="cursor:default;font-size:14px">…</button>`
      : `<button class="pbtn ${p===page?'active':''}" onclick="goPage(${p})">${p}</button>`;
  });
  h+=`<button class="pbtn" onclick="goPage(${page+1})" ${page===total?'disabled':''}>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="9 18 15 12 9 6"/></svg>
  </button>`;
  h+=`</div>`;
  wrap.innerHTML=h;wrap.style.display='flex';
}

function goPage(p){
  const tot=Math.ceil(filtered.length/PER);
  if(p<1||p>tot)return;
  page=p;renderTable();
  document.querySelector('.table-section').scrollIntoView({behavior:'smooth',block:'start'});
}

window.switchTab=switchTab;
window.doSearch=doSearch;
window.goPage=goPage;
</script>
@endsection