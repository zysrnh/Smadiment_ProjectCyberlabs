@extends('mk.layouts.app')

@section('title', 'Instagram Top Hashtags - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary: #dc2743;
    --primary-dark: #bc1888;
    --text-primary: #1a202c;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --bg-white: #ffffff;
    --bg-gray-50: #f8fafc;
    --bg-gray-100: #f1f5f9;
    --border-gray: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
    --ig-gradient: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
  }

  body { background: var(--bg-gray-50); }

  .dashboard-container { padding: 24px; max-width: 1600px; margin: 0 auto; }

  /* ── Page Header ─────────────────────────────── */
  .page-header { margin-bottom: 32px; }

  .page-header h1 {
    font-size: 28px; font-weight: 700; color: var(--text-primary);
    margin: 0 0 8px 0; display: flex; align-items: center; gap: 12px;
  }

  .ig-icon-wrapper {
    width: 40px; height: 40px; border-radius: 12px;
    background: var(--ig-gradient);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }

  .ig-icon-wrapper svg { width: 24px; height: 24px; stroke: white; fill: none; }
  .page-header p { font-size: 14px; color: var(--text-secondary); margin: 0; }

  /* ── Filter Card ─────────────────────────────── */
  .filter-card {
    background: var(--bg-white); border-radius: 16px; padding: 20px 24px;
    margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray);
  }

  .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }

  .filter-label { font-size: 14px; font-weight: 600; color: var(--text-primary); white-space: nowrap; }

  .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }

  .apply-btn {
    padding: 12px 28px; background: var(--ig-gradient);
    color: white; border: none; border-radius: 12px;
    font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;
    box-shadow: 0 4px 12px rgba(220,39,67,0.2); white-space: nowrap;
  }

  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220,39,67,0.3); }
  .apply-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; }

  /* ── Date Picker Trigger ─────────────────────── */
  .date-picker-trigger {
    display: flex; align-items: center; gap: 12px; padding: 12px 20px;
    background: var(--bg-gray-50); border: 1px solid var(--border-gray);
    border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500;
    color: var(--text-primary); cursor: pointer; transition: all 0.2s; width: 100%; max-width: 400px;
  }

  .date-picker-trigger:hover { border-color: var(--primary); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(220,39,67,0.1); }
  .date-picker-trigger svg:first-child { width: 18px; height: 18px; color: var(--text-secondary); flex-shrink: 0; }
  .date-picker-trigger span { flex: 1; text-align: left; }
  .date-picker-trigger svg:last-child { width: 16px; height: 16px; margin-left: auto; color: var(--text-secondary); }

  /* ── Stats Grid ──────────────────────────────── */
  .stats-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;
  }

  .stat-card {
    background: var(--bg-white); border: 1px solid var(--border-gray);
    border-radius: 12px; padding: 20px; box-shadow: var(--shadow-sm);
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden;
  }

  .stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--ig-gradient); opacity: 0; transition: opacity 0.3s;
  }

  .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--primary); }
  .stat-card:hover::before { opacity: 1; }

  .stat-label { font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; }
  .stat-value { font-size: 28px; font-weight: 700; color: var(--text-primary); line-height: 1.2; }
  .stat-value.accent { font-size: 18px; color: var(--primary); }

  /* ── Main Grid ───────────────────────────────── */
  .main-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; }

  /* ── Table Card ──────────────────────────────── */
  .table-card {
    background: var(--bg-white); border-radius: 16px;
    border: 1px solid var(--border-gray); box-shadow: var(--shadow-sm); overflow: hidden;
  }

  .card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 20px 24px; border-bottom: 2px solid var(--bg-gray-50);
  }

  .card-title h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0; }
  .card-subtitle { font-size: 13px; color: var(--text-secondary); }

  .table-search { position: relative; width: 240px; }

  .table-search input {
    width: 100%; padding: 9px 16px 9px 40px; border: 1px solid var(--border-gray);
    border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 13px;
    background: var(--bg-gray-50); transition: all 0.2s; outline: none;
  }

  .table-search input:focus { border-color: var(--primary); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(220,39,67,0.1); }

  .table-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-secondary); stroke: currentColor; fill: none; }

  .table-wrapper { overflow-x: auto; }

  /* ── Hashtag Table ───────────────────────────── */
  .hashtag-table { width: 100%; border-collapse: collapse; font-family: 'Poppins', sans-serif; }

  .hashtag-table th {
    padding: 14px 20px; text-align: left; font-size: 10px; font-weight: 700;
    color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.8px;
    border-bottom: 2px solid var(--bg-gray-100); white-space: nowrap;
  }

  .hashtag-table th.text-right  { text-align: right; }
  .hashtag-table th.text-center { text-align: center; }

  .hashtag-table tbody tr { border-bottom: 1px solid var(--bg-gray-100); transition: background 0.15s; }
  .hashtag-table tbody tr:hover { background: #fafbfc; }
  .hashtag-table tbody tr:last-child { border-bottom: none; }
  .hashtag-table td { padding: 14px 20px; font-size: 13px; color: var(--text-primary); vertical-align: middle; }

  .rank-badge {
    width: 28px; height: 28px; border-radius: 8px; background: var(--bg-gray-100);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; color: var(--text-secondary);
  }

  .rank-badge.gold   { background: linear-gradient(135deg,#ffd700,#ffb800); color:white; }
  .rank-badge.silver { background: linear-gradient(135deg,#c0c0c0,#a0a0a0); color:white; }
  .rank-badge.bronze { background: linear-gradient(135deg,#cd7f32,#a0522d); color:white; }

  .hashtag-pill {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
    background: rgba(220,39,67,0.08); color: var(--primary); border-radius: 20px;
    font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s;
  }

  .hashtag-pill:hover { background: var(--primary); color: white; }
  .hashtag-pill svg { width: 12px; height: 12px; stroke: currentColor; fill: none; }

  .progress-wrap { min-width: 100px; }
  .progress-bg { height: 6px; background: var(--bg-gray-100); border-radius: 10px; overflow: hidden; }
  .progress-fill { height: 100%; background: var(--ig-gradient); border-radius: 10px; transition: width 0.8s ease-out; }
  .pct-text { font-size: 11px; color: var(--text-secondary); margin-top: 3px; font-weight: 600; }

  .mention-count { font-weight: 700; color: var(--text-primary); }

  /* ── Pagination ──────────────────────────────── */
  .pagination {
    display: flex; justify-content: space-between; align-items: center;
    padding: 16px 20px; border-top: 1px solid var(--border-gray); flex-wrap: wrap; gap: 12px;
  }

  .pagination-info { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
  .pagination-info strong { color: var(--text-primary); font-weight: 700; }

  .page-btn {
    width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--border-gray);
    background: var(--bg-white); color: var(--text-primary); font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;
    font-family: 'Poppins', sans-serif;
  }

  .page-btn:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); background: rgba(220,39,67,0.05); }
  .page-btn.active { background: var(--ig-gradient); color: white; border-color: transparent; }
  .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

  /* ── Sidebar ─────────────────────────────────── */
  .sidebar { display: flex; flex-direction: column; gap: 20px; }

  .sidebar-card {
    background: var(--bg-white); border-radius: 16px;
    border: 1px solid var(--border-gray); box-shadow: var(--shadow-sm); overflow: hidden;
  }

  .sidebar-card .card-header { padding: 16px 20px; border-bottom: 1px solid var(--bg-gray-100); }

  .wordcloud-wrap {
    padding: 16px 20px 20px; min-height: 260px;
    display: flex; flex-wrap: wrap; align-content: flex-start; gap: 8px;
  }

  .wc-tag {
    display: inline-block; padding: 5px 12px; border-radius: 20px; font-weight: 600;
    cursor: pointer; transition: all 0.2s; border: none; font-family: 'Poppins', sans-serif;
    background: transparent; line-height: 1.4; text-decoration: none;
  }

  .wc-tag:hover { transform: scale(1.1) !important; opacity: 1 !important; }

  .top-list { padding: 0 20px 16px; }

  .top-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 0; border-bottom: 1px solid var(--bg-gray-100);
  }

  .top-item:last-child { border-bottom: none; }

  .top-rank {
    width: 22px; height: 22px; border-radius: 6px; background: var(--ig-gradient);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 10px; font-weight: 700; flex-shrink: 0;
  }

  .top-name { flex: 1; font-size: 12px; font-weight: 600; color: var(--primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .top-count { font-size: 11px; font-weight: 700; color: var(--text-secondary); white-space: nowrap; }

  /* ── Empty / Alert / Skeleton ────────────────── */
  .empty-state { text-align: center; padding: 80px 20px; }
  .empty-state svg { width: 56px; height: 56px; color: var(--text-muted); stroke: currentColor; fill: none; margin-bottom: 16px; }
  .empty-state h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px; }
  .empty-state p  { font-size: 14px; color: var(--text-secondary); margin: 0; }

  .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px; }
  .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

  .skeleton-line {
    height: 16px;
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 8px;
  }

  @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

  /* ── Date Picker Modal ───────────────────────── */
  .date-picker-modal {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000;
    display: none; align-items: center; justify-content: center;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(8px);
  }

  .date-picker-modal.show { display: flex; }
  .date-picker-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); cursor: pointer; }

  .date-picker-container {
    position: relative; background: #fff; border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    display: flex; max-width: 900px; width: 90%; max-height: 90vh;
    z-index: 10001; animation: slideUp 0.3s ease-out;
  }

  @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

  .date-picker-sidebar {
    width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray);
    padding: 16px 12px; border-radius: 16px 0 0 16px;
    display: flex; flex-direction: column; gap: 4px; flex-shrink: 0;
  }

  .date-preset {
    padding: 10px 16px; background: transparent; border: none; border-radius: 8px;
    font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500;
    color: var(--text-primary); text-align: left; cursor: pointer; transition: all 0.2s;
  }

  .date-preset:hover { background: var(--bg-white); color: var(--primary); }
  .date-preset.active { background: var(--ig-gradient); color: white; }

  .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; overflow: hidden; }
  .date-picker-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }

  .nav-btn {
    width: 36px; height: 36px; border-radius: 8px; background: var(--bg-gray-50);
    border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s; flex-shrink: 0;
  }

  .nav-btn:hover { background: var(--ig-gradient); border-color: transparent; color: white; }
  .nav-btn svg { width: 20px; height: 20px; }

  .calendars-wrapper { display: flex; gap: 24px; flex: 1; min-height: 0; }
  .calendar { flex: 1; display: flex; flex-direction: column; min-width: 0; }
  .calendar-month { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-align: center; }
  .calendar-weekdays { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; margin-bottom: 8px; }
  .weekday { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
  .calendar-days { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; }

  .calendar-day {
    aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 500; border-radius: 8px; cursor: pointer;
    transition: all 0.2s; color: var(--text-primary); background: transparent; border: none; padding: 0;
  }

  .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
  .calendar-day.other-month { color: #cbd5e1; cursor: default; }
  .calendar-day.disabled    { color: #e2e8f0; cursor: not-allowed; }
  .calendar-day.today       { border: 2px solid var(--primary); }
  .calendar-day.selected    { background: var(--ig-gradient); color: white; }
  .calendar-day.in-range    { background: rgba(220,39,67,0.1); color: var(--primary); }
  .calendar-day.range-start, .calendar-day.range-end { background: var(--ig-gradient); color: white; }

  .date-picker-display {
    padding: 16px 20px; background: var(--bg-gray-50); border-radius: 12px;
    text-align: center; margin-bottom: 20px; border: 1px solid var(--border-gray);
  }

  .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }

  .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }

  .cancel-btn, .apply-date-btn {
    padding: 10px 24px; border-radius: 10px; font-family: 'Poppins', sans-serif;
    font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none;
  }

  .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
  .cancel-btn:hover { background: var(--border-gray); }
  .apply-date-btn { background: var(--ig-gradient); color: white; box-shadow: 0 4px 12px rgba(220,39,67,0.2); }
  .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220,39,67,0.3); }

  /* ── Responsive ──────────────────────────────── */
  @media (max-width: 1200px) {
    .stats-grid { grid-template-columns: repeat(2,1fr); }
    .main-grid  { grid-template-columns: 1fr; }
  }

  @media (max-width: 768px) {
    .dashboard-container { padding: 16px; }
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .filter-content { flex-direction: column; align-items: stretch; }
    .date-range-wrapper { flex-direction: column; }
    .apply-btn { width: 100%; justify-content: center; }
    .date-picker-trigger { max-width: 100%; }
    .table-search { width: 100%; }
    .card-header { flex-direction: column; align-items: flex-start; gap: 12px; }
    .date-picker-container { flex-direction: column; max-height: 85vh; overflow-y: auto; width: 95%; }
    .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; padding: 12px 16px; }
    .date-preset { white-space: nowrap; }
    .date-picker-content { padding: 20px 16px; }
    .calendars-wrapper { flex-direction: column; gap: 16px; }
    .cancel-btn, .apply-date-btn { flex: 1; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>
      <div class="ig-icon-wrapper">
        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
          <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
          <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
        </svg>
      </div>
      Instagram Top Hashtags
    </h1>
    <p>Hashtag paling populer di Instagram berdasarkan jumlah mention dalam periode yang dipilih</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;flex-shrink:0;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar.</span>
  </div>
  @else

  <!-- Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.instagram.trending-topics') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hiddenEndDate"   value="{{ $endDate }}">

      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          Date Range
        </div>

        <div class="date-range-wrapper">
          <button type="button" class="date-picker-trigger" id="datePickerTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="dateRangeDisplay">{{ $startDate }} to {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
        </div>

        <button type="submit" class="apply-btn">
          <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
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
      <div class="stat-label">Total Hashtags</div>
      <div id="statTotalHashtags" class="stat-value">
        <div class="skeleton-line" style="width:60%;height:28px;"></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Mentions</div>
      <div id="statTotalMentions" class="stat-value">
        <div class="skeleton-line" style="width:60%;height:28px;"></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Top Hashtag</div>
      <div id="statTopHashtag" class="stat-value accent">
        <div class="skeleton-line" style="width:70%;height:24px;"></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Avg Mentions / Hashtag</div>
      <div id="statAvgMentions" class="stat-value">
        <div class="skeleton-line" style="width:50%;height:28px;"></div>
      </div>
    </div>
  </div>

  <!-- Main Grid: Table + Sidebar -->
  <div class="main-grid">

    <!-- Ranking Table -->
    <div class="table-card">
      <div class="card-header">
        <div class="card-title">
          <h3>Hashtag Rankings</h3>
          <p class="card-subtitle">Diurutkan berdasarkan jumlah mention terbanyak</p>
        </div>
        <div class="table-search">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="searchInput" placeholder="Cari hashtag…" oninput="HashtagApp.search(this.value)">
        </div>
      </div>

      <div class="table-wrapper">

        <!-- Skeleton -->
        <table class="hashtag-table" id="skeletonTable">
          <thead>
            <tr>
              <th style="width:60px;">#</th>
              <th>Hashtag</th>
              <th class="text-right" style="width:120px;">Mentions</th>
              <th style="width:200px;">Share</th>
            </tr>
          </thead>
          <tbody>
            @for($i = 0; $i < 8; $i++)
            <tr>
              <td><div class="skeleton-line" style="width:28px;height:28px;border-radius:8px;"></div></td>
              <td><div class="skeleton-line" style="width:{{ 90 + $i*18 }}px;height:16px;"></div></td>
              <td><div class="skeleton-line" style="width:60px;height:16px;margin-left:auto;"></div></td>
              <td><div class="skeleton-line" style="width:100%;height:8px;border-radius:10px;"></div></td>
            </tr>
            @endfor
          </tbody>
        </table>

        <!-- Real Table -->
        <table class="hashtag-table" id="hashtagTable" style="display:none;">
          <thead>
            <tr>
              <th style="width:60px;">#</th>
              <th>Hashtag</th>
              <th class="text-right" style="width:120px;">Mentions</th>
              <th style="width:200px;">Share</th>
            </tr>
          </thead>
          <tbody id="hashtagTableBody"></tbody>
        </table>

        <!-- Empty State -->
        <div id="emptyState" style="display:none;">
          <div class="empty-state">
            <svg viewBox="0 0 24 24">
              <line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/>
              <line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/>
            </svg>
            <h3>No Hashtags Found</h3>
            <p>Tidak ada data hashtag untuk periode ini. Coba ubah tanggal filter.</p>
          </div>
        </div>

      </div>

      <!-- Pagination -->
      <div id="paginationWrapper" class="pagination" style="display:none;"></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Word Cloud -->
      <div class="sidebar-card">
        <div class="card-header">
          <div class="card-title">
            <h3>Word Cloud</h3>
            <p class="card-subtitle">Visualisasi hashtag populer</p>
          </div>
        </div>
        <div class="wordcloud-wrap" id="wordcloudWrap">
          @for($i = 0; $i < 16; $i++)
          <div class="skeleton-line" style="width:{{ 38+rand(0,65) }}px;height:28px;border-radius:20px;display:inline-block;"></div>
          @endfor
        </div>
      </div>

      <!-- Top 10 Quick View -->
      <div class="sidebar-card">
        <div class="card-header">
          <div class="card-title">
            <h3>Top 10</h3>
            <p class="card-subtitle">Hashtag dengan mention terbanyak</p>
          </div>
        </div>
        <div class="top-list" id="topList">
          @for($i = 0; $i < 10; $i++)
          <div class="top-item">
            <div class="top-rank" style="background:var(--bg-gray-200);color:var(--text-muted);">{{ $i+1 }}</div>
            <div class="top-name"><div class="skeleton-line" style="width:110px;height:13px;"></div></div>
            <div class="top-count"><div class="skeleton-line" style="width:36px;height:13px;"></div></div>
          </div>
          @endfor
        </div>
      </div>

    </div>
  </div>

  @endif
</div>
@endsection

@section('scripts')
<script>
// ============================================================
// DATE PICKER (IIFE)
// ============================================================
(function () {
  'use strict';
  let s1 = null, s2 = null, mo1 = new Date(), mo2 = new Date(), pickStart = true;

  document.addEventListener('DOMContentLoaded', () => {
    const sv = document.getElementById('hiddenStartDate');
    const ev = document.getElementById('hiddenEndDate');
    s1 = sv?.value ? new Date(sv.value) : (() => { const d = new Date(); d.setDate(d.getDate()-6); return d; })();
    s2 = ev?.value ? new Date(ev.value) : new Date();
    mo1 = new Date(s1); mo2 = new Date(s1); mo2.setMonth(mo2.getMonth()+1);
    renderCals(); bind();
  });

  function bind() {
    document.getElementById('datePickerTrigger')?.addEventListener('click', open);
    document.querySelector('.date-picker-overlay')?.addEventListener('click', close);
    document.addEventListener('keydown', e => { if (e.key==='Escape') close(); });
    document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', applyPreset));
    document.getElementById('prevMonth')?.addEventListener('click', () => { mo1.setMonth(mo1.getMonth()-1); mo2.setMonth(mo2.getMonth()-1); renderCals(); });
    document.getElementById('nextMonth')?.addEventListener('click', () => { mo1.setMonth(mo1.getMonth()+1); mo2.setMonth(mo2.getMonth()+1); renderCals(); });
    document.getElementById('applyDatePicker')?.addEventListener('click', applyDates);
    document.querySelector('.cancel-btn')?.addEventListener('click', close);
  }

  function open()  { document.getElementById('datePickerModal').classList.add('show'); renderCals(); }
  function close() { document.getElementById('datePickerModal').classList.remove('show'); }

  function applyPreset(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    const today = new Date(); today.setHours(0,0,0,0);
    switch(e.target.dataset.preset) {
      case 'today':      s1=new Date(today); s2=new Date(today); break;
      case 'yesterday':  s1=new Date(today); s1.setDate(today.getDate()-1); s2=new Date(s1); break;
      case 'last7days':  s2=new Date(today); s1=new Date(today); s1.setDate(today.getDate()-6); break;
      case 'last30days': s2=new Date(today); s1=new Date(today); s1.setDate(today.getDate()-29); break;
      case 'thismonth':  s1=new Date(today.getFullYear(),today.getMonth(),1); s2=new Date(today); break;
      case 'lastmonth':  s1=new Date(today.getFullYear(),today.getMonth()-1,1); s2=new Date(today.getFullYear(),today.getMonth(),0); break;
    }
    if (e.target.dataset.preset!=='custom') { mo1=new Date(s1); mo2=new Date(s1); mo2.setMonth(mo2.getMonth()+1); updateDisp(); renderCals(); }
  }

  function applyDates() {
    document.getElementById('hiddenStartDate').value = fmt(s1);
    document.getElementById('hiddenEndDate').value   = fmt(s2);
    document.getElementById('dateRangeDisplay').textContent = `${fmt(s1)} to ${fmt(s2)}`;
    close();
  }

  function renderCals() { renderCal('calendar1',mo1); renderCal('calendar2',mo2); updateDisp(); }

  function renderCal(id, mo) {
    const el = document.getElementById(id); if(!el) return;
    const y=mo.getFullYear(), m=mo.getMonth();
    const first=new Date(y,m,1), last=new Date(y,m+1,0), prev=new Date(y,m,0);
    const MONTHS=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const today=new Date(); today.setHours(0,0,0,0);
    let html=`<div class="calendar-month">${MONTHS[m]} ${y}</div>
      <div class="calendar-weekdays">${['Su','Mo','Tu','We','Th','Fr','Sa'].map(d=>`<div class="weekday">${d}</div>`).join('')}</div>
      <div class="calendar-days">`;
    for(let i=first.getDay()-1;i>=0;i--) html+=`<button type="button" class="calendar-day other-month" disabled>${prev.getDate()-i}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const dt=new Date(y,m,d); dt.setHours(0,0,0,0);
      let cls='calendar-day';
      if(sameDay(dt,today)) cls+=' today';
      if(dt>today) cls+=' disabled';
      if(s1&&s2){
        if(sameDay(dt,s1)) cls+=' selected range-start';
        else if(sameDay(dt,s2)) cls+=' selected range-end';
        else if(dt>s1&&dt<s2) cls+=' in-range';
      }
      html+=`<button type="button" class="${cls}" data-date="${fmt(dt)}" ${dt>today?'disabled':''}>${d}</button>`;
    }
    const ld=last.getDay();
    for(let i=1;i<7-ld;i++) html+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    html+='</div>';
    el.innerHTML=html;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(b=>b.addEventListener('click',pickDate));
  }

  function pickDate(e) {
    const dt=new Date(e.target.dataset.date); dt.setHours(0,0,0,0);
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
    document.querySelector('[data-preset="custom"]')?.classList.add('active');
    if(pickStart||dt<s1){ s1=dt; s2=dt; pickStart=false; }
    else { s2=dt>=s1?dt:s1; if(dt<s1){s2=s1;s1=dt;} pickStart=true; }
    updateDisp(); renderCals();
  }

  function updateDisp() {
    const el=document.getElementById('selectedRangeText');
    if(el&&s1&&s2) el.textContent=`${fmt(s1)} to ${fmt(s2)}`;
  }

  function fmt(d)       { return d?`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`:''; }
  function sameDay(a,b) { return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
})();

// ============================================================
// HASHTAG APP
// ============================================================
const HashtagApp = {
  projectId : '{{ $projectId ?? "" }}',
  startDate : '{{ $startDate ?? "" }}',
  endDate   : '{{ $endDate ?? "" }}',
  all       : [],
  filtered  : [],
  page      : 1,
  perPage   : 20,

  async init() {
    if (!this.projectId) return;
    try   { await this.load(); }
    catch (err) {
      console.error('HashtagApp:', err);
      document.getElementById('skeletonTable').style.display = 'none';
      document.getElementById('emptyState').style.display    = 'block';
    }
  },

  async load() {
    const url    = `/mk/api/instagram/trending-topics?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`;
    const res    = await fetch(url);
    const result = await res.json();

    if (!result.success) throw new Error(result.error || 'API Error');

    const data   = result.data;
    this.all      = data.hashtags || [];
    this.filtered = [...this.all];

    // ── Stats ────────────────────────────────────────
    const fmt = n => new Intl.NumberFormat('en-US').format(n);
    document.getElementById('statTotalHashtags').textContent = fmt(data.total_hashtags || 0);
    document.getElementById('statTotalMentions').textContent = fmt(data.total_mentions || 0);
    document.getElementById('statTopHashtag').textContent    = data.top_hashtag ? '#' + data.top_hashtag.name : '—';
    const avg = data.total_hashtags > 0 ? Math.round((data.total_mentions||0) / data.total_hashtags) : 0;
    document.getElementById('statAvgMentions').textContent   = fmt(avg);

    this.renderTable();
    this.renderWordCloud();
    this.renderTopList();
  },

  // ── Table ─────────────────────────────────────────────────
  renderTable() {
    const skeleton = document.getElementById('skeletonTable');
    const table    = document.getElementById('hashtagTable');
    const empty    = document.getElementById('emptyState');
    const pagWrap  = document.getElementById('paginationWrapper');

    if (!this.filtered.length) {
      skeleton.style.display = 'none'; table.style.display = 'none';
      empty.style.display = 'block'; pagWrap.style.display = 'none';
      return;
    }

    const fmt     = n => new Intl.NumberFormat('en-US').format(n);
    const maxSize = this.filtered[0]?.size || 1;
    const start   = (this.page - 1) * this.perPage;
    const items   = this.filtered.slice(start, start + this.perPage);

    document.getElementById('hashtagTableBody').innerHTML = items.map((h, i) => {
      const rank   = start + i + 1;
      const pct    = ((h.size / maxSize) * 100).toFixed(1);
      const link   = `https://www.instagram.com/explore/tags/${encodeURIComponent(h.name)}/`;
      const rClass = rank===1?'gold':rank===2?'silver':rank===3?'bronze':'';
      return `
        <tr>
          <td><div class="rank-badge ${rClass}">${rank}</div></td>
          <td>
            <a href="${link}" target="_blank" class="hashtag-pill">
              <svg viewBox="0 0 24 24"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/></svg>
              ${this.esc(h.name)}
            </a>
          </td>
          <td style="text-align:right;"><span class="mention-count">${fmt(h.size)}</span></td>
          <td>
            <div class="progress-wrap">
              <div class="progress-bg"><div class="progress-fill" style="width:${pct}%"></div></div>
              <div class="pct-text">${pct}%</div>
            </div>
          </td>
        </tr>`;
    }).join('');

    skeleton.style.display = 'none'; table.style.display = 'table'; empty.style.display = 'none';
    this.renderPagination();
  },

  // ── Pagination ────────────────────────────────────────────
  renderPagination() {
    const total   = this.filtered.length;
    const pages   = Math.ceil(total / this.perPage);
    const wrapper = document.getElementById('paginationWrapper');
    const from    = total ? (this.page-1)*this.perPage+1 : 0;
    const to      = Math.min(this.page*this.perPage, total);

    let html = `<div class="pagination-info">Showing ${from}–${to} of <strong>${total}</strong> hashtags</div>`;
    html += `<div style="display:flex;align-items:center;gap:6px;">`;
    html += `<button class="page-btn" onclick="HashtagApp.goTo(${this.page-1})" ${this.page===1?'disabled':''}>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
    </button>`;

    this.pageRange(this.page, pages).forEach(p => {
      html += p === '...'
        ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
        : `<button class="page-btn ${p===this.page?'active':''}" onclick="HashtagApp.goTo(${p})">${p}</button>`;
    });

    html += `<button class="page-btn" onclick="HashtagApp.goTo(${this.page+1})" ${this.page===pages?'disabled':''}>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
    </button></div>`;

    wrapper.innerHTML = html;
    wrapper.style.display = total > 0 ? 'flex' : 'none';
  },

  pageRange(cur, total) {
    if (total <= 7) return Array.from({length:total}, (_,i)=>i+1);
    if (cur <= 4)   return [1,2,3,4,5,'...',total];
    if (cur >= total-3) return [1,'...',total-4,total-3,total-2,total-1,total];
    return [1,'...',cur-1,cur,cur+1,'...',total];
  },

  goTo(p) {
    const pages = Math.ceil(this.filtered.length / this.perPage);
    if (p<1||p>pages) return;
    this.page = p;
    this.renderTable();
    document.querySelector('.table-card')?.scrollIntoView({behavior:'smooth', block:'start'});
  },

  // ── Search ────────────────────────────────────────────────
  search(term) {
    this.page     = 1;
    const t       = term.toLowerCase().trim();
    this.filtered = t ? this.all.filter(h => h.name.toLowerCase().includes(t)) : [...this.all];
    this.renderTable();
  },

  // ── Word Cloud ────────────────────────────────────────────
  renderWordCloud() {
    const wrap = document.getElementById('wordcloudWrap');
    if (!this.all.length) { wrap.innerHTML = '<p style="color:var(--text-muted);font-size:13px;">No data</p>'; return; }

    const top     = this.all.slice(0, 50);
    const maxSize = top[0].size;
    const minSize = top[top.length-1].size;
    const colors  = ['#f09433','#e6683c','#dc2743','#cc2366','#bc1888','#8b5cf6','#3b82f6','#0ea5e9','#10b981'];

    wrap.innerHTML = top.map((h, i) => {
      const ratio    = maxSize>minSize ? (h.size-minSize)/(maxSize-minSize) : 1;
      const fontSize = Math.round(11 + ratio*20);
      const opacity  = (0.45 + ratio*0.55).toFixed(2);
      const link     = `https://www.instagram.com/explore/tags/${encodeURIComponent(h.name)}/`;
      return `<a href="${link}" target="_blank" class="wc-tag" style="font-size:${fontSize}px;color:${colors[i%colors.length]};opacity:${opacity};">#${this.esc(h.name)}</a>`;
    }).join('');
  },

  // ── Top 10 List ───────────────────────────────────────────
  renderTopList() {
    const fmt   = n => new Intl.NumberFormat('en-US').format(n);
    const top10 = this.all.slice(0, 10);
    document.getElementById('topList').innerHTML = top10.map((h, i) => `
      <div class="top-item">
        <div class="top-rank">${i+1}</div>
        <span class="top-name">#${this.esc(h.name)}</span>
        <span class="top-count">${fmt(h.size)}</span>
      </div>`).join('');
  },

  esc(text) { const d = document.createElement('div'); d.textContent = text; return d.innerHTML; },
};

document.addEventListener('DOMContentLoaded', () => HashtagApp.init());
</script>
@endsection