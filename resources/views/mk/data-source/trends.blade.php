@extends('mk.layouts.app')

@section('title', 'Trends Total - Data Source')

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
    --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
  }

  .dashboard-container { padding: 24px; background: var(--bg-gray-50); min-height: 100vh; max-width: 1600px; margin: 0 auto; }
  .page-header { margin-bottom: 32px; }
  .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
  .page-header p { font-size: 14px; color: var(--text-secondary); margin: 0; }

  /* Filter */
  .filter-card { background: var(--bg-white); border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); }
  .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
  .filter-label { font-size: 14px; font-weight: 600; color: var(--text-primary); white-space: nowrap; display: flex; align-items: center; gap: 6px; }
  .filter-label svg { width: 18px; height: 18px; stroke: var(--text-secondary); fill: none; }
  .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }
  .date-picker-trigger { display: flex; align-items: center; gap: 12px; padding: 12px 20px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; color: var(--text-primary); cursor: pointer; transition: all 0.2s; max-width: 340px; width: 100%; }
  .date-picker-trigger:hover { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(3,128,71,0.1); }
  .date-picker-trigger svg { width: 18px; height: 18px; stroke: var(--text-secondary); fill: none; flex-shrink: 0; }
  .date-picker-trigger span { flex: 1; text-align: left; }
  .apply-btn { padding: 12px 28px; background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark)); color: white; border: none; border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(3,128,71,0.2); }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,0.3); }
  .apply-btn svg { width: 18px; height: 18px; stroke: white; fill: none; }

  /* Stats */
  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 24px; }
  .stat-card { background: var(--bg-white); border-radius: 16px; padding: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden; }
  .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background: linear-gradient(90deg, var(--primary-green), var(--primary-green-dark)); opacity:0; transition: opacity 0.3s; }
  .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-green); }
  .stat-card:hover::before { opacity: 1; }
  .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
  .stat-icon-wrapper { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, rgba(3,128,71,0.1), rgba(3,128,71,0.05)); display: flex; align-items: center; justify-content: center; }
  .stat-icon { width: 28px; height: 28px; stroke: var(--primary-green); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
  .stat-label { font-size: 13px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
  .stat-value-wrapper { display: flex; align-items: baseline; gap: 10px; margin-bottom: 16px; }
  .stat-value { font-size: 36px; font-weight: 700; color: var(--text-primary); line-height: 1; }
  .stat-value-sm { font-size: 20px; }
  .stat-sub { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
  .stat-progress { height: 6px; background: var(--bg-gray-100); border-radius: 10px; overflow: hidden; }
  .stat-progress-bar { height: 100%; background: linear-gradient(90deg, var(--primary-green), var(--primary-green-dark)); border-radius: 10px; transition: width 1s ease-out; }

  /* Chart */
  .chart-card { background: var(--bg-white); border-radius: 16px; padding: 28px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); margin-bottom: 24px; transition: all 0.3s; }
  .chart-card:hover { box-shadow: var(--shadow-md); }
  .chart-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid var(--bg-gray-50); flex-wrap: wrap; gap: 16px; }
  .chart-title-group h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; }
  .chart-subtitle { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
  .chart-header-right { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
  .toggle-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; color: var(--text-secondary); cursor: pointer; transition: all 0.2s; }
  .toggle-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }
  .toggle-btn:hover { border-color: var(--primary-green); color: var(--primary-green); }
  .chart-controls { display: flex; gap: 6px; background: var(--bg-gray-50); padding: 4px; border-radius: 10px; }
  .chart-type-btn { padding: 8px 14px; border: none; background: transparent; color: var(--text-secondary); font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; border-radius: 8px; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
  .chart-type-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }
  .chart-type-btn:hover { background: var(--bg-white); color: var(--text-primary); }
  .chart-type-btn.active { background: var(--primary-green); color: white; box-shadow: 0 2px 8px rgba(3,128,71,0.25); }
  .chart-container { position: relative; height: 400px; }

  /* Table */
  .table-section { background: var(--bg-white); border-radius: 16px; padding: 28px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); margin-bottom: 24px; overflow-x: auto; }
  .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid var(--bg-gray-50); flex-wrap: wrap; gap: 16px; }
  .table-title h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; }
  .table-subtitle { font-size: 13px; color: var(--text-secondary); }
  .export-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; color: var(--text-primary); cursor: pointer; transition: all 0.2s; box-shadow: var(--shadow-sm); }
  .export-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; }
  .export-btn:hover { border-color: var(--primary-green); color: var(--primary-green); transform: translateY(-1px); box-shadow: var(--shadow-md); }
  .data-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 700px; }
  .data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--border-gray); background: var(--bg-gray-50); white-space: nowrap; }
  .data-table td { padding: 14px 16px; font-size: 13px; color: var(--text-primary); border-bottom: 1px solid var(--bg-gray-100); vertical-align: middle; }
  .data-table tbody tr { transition: background 0.2s; }
  .data-table tbody tr:hover { background: var(--bg-gray-50); }
  .data-table tbody tr:last-child td { border-bottom: none; }
  .keyword-cell { font-weight: 600; color: var(--text-primary); }
  .badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
  .badge-up   { background: #d1fae5; color: #065f46; }
  .badge-down { background: #fee2e2; color: #991b1b; }
  .badge-flat { background: #e2e8f0; color: #64748b; }

  /* Alert */
  .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px; }
  .alert svg { width: 20px; height: 20px; flex-shrink: 0; stroke: currentColor; fill: none; }
  .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

  /* Empty */
  .empty-state { text-align: center; padding: 80px 20px; background: var(--bg-white); border-radius: 16px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); }
  .empty-state-icon { width: 72px; height: 72px; margin: 0 auto 20px; background: var(--bg-gray-100); border-radius: 20px; display: flex; align-items: center; justify-content: center; }
  .empty-state-icon svg { width: 36px; height: 36px; stroke: var(--text-secondary); fill: none; stroke-width: 1.5; }
  .empty-state h3 { font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
  .empty-state p { color: var(--text-secondary); font-size: 14px; }

  /* Date Picker */
  .date-picker-modal { position: fixed; top:0; left:0; right:0; bottom:0; z-index:10000; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,0.5); backdrop-filter:blur(8px); }
  .date-picker-modal.show { display: flex; }
  .date-picker-overlay { position:absolute; top:0; left:0; right:0; bottom:0; cursor:pointer; }
  .date-picker-container { position:relative; background:#fff; border-radius:16px; box-shadow:0 25px 50px rgba(0,0,0,0.3); display:flex; max-width:900px; width:90%; z-index:10001; animation:slideUp 0.3s ease-out; }
  @keyframes slideUp { from{transform:translateY(20px);opacity:0;}to{transform:translateY(0);opacity:1;} }
  .date-picker-sidebar { width:180px; background:var(--bg-gray-50); border-right:1px solid var(--border-gray); padding:16px 12px; border-radius:16px 0 0 16px; display:flex; flex-direction:column; gap:4px; flex-shrink:0; }
  .date-preset { padding:10px 16px; background:transparent; border:none; border-radius:8px; font-family:'Poppins',sans-serif; font-size:13px; font-weight:500; color:var(--text-primary); text-align:left; cursor:pointer; transition:all 0.2s; }
  .date-preset:hover { background:var(--bg-white); color:var(--primary-green); }
  .date-preset.active { background:var(--primary-green); color:white; }
  .date-picker-content { flex:1; padding:24px; display:flex; flex-direction:column; }
  .date-picker-header { display:flex; align-items:center; gap:20px; margin-bottom:20px; }
  .nav-btn { width:36px; height:36px; border-radius:8px; background:var(--bg-gray-50); border:1px solid var(--border-gray); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.2s; flex-shrink:0; }
  .nav-btn:hover { background:var(--primary-green); border-color:var(--primary-green); color:white; }
  .nav-btn svg { width:20px; height:20px; stroke:currentColor; fill:none; stroke-width:2; }
  .calendars-wrapper { display:flex; gap:24px; flex:1; }
  .calendar { flex:1; }
  .calendar-month { font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:16px; text-align:center; }
  .calendar-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:8px; }
  .weekday { text-align:center; font-size:11px; font-weight:700; color:var(--text-secondary); padding:8px 0; }
  .calendar-days { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
  .calendar-day { aspect-ratio:1; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:500; border-radius:8px; cursor:pointer; transition:all 0.2s; color:var(--text-primary); background:transparent; border:none; padding:0; }
  .calendar-day:hover:not(.disabled):not(.other-month) { background:var(--bg-gray-100); }
  .calendar-day.other-month { color:#cbd5e1; cursor:default; }
  .calendar-day.disabled { color:#e2e8f0; cursor:not-allowed; }
  .calendar-day.today { border:2px solid var(--primary-green); }
  .calendar-day.selected,.calendar-day.range-start,.calendar-day.range-end { background:var(--primary-green); color:white; }
  .calendar-day.in-range { background:rgba(3,128,71,0.1); color:var(--primary-green); }
  .date-picker-display { padding:14px 20px; background:var(--bg-gray-50); border-radius:12px; text-align:center; margin-bottom:20px; border:1px solid var(--border-gray); }
  .date-picker-display span { font-size:14px; font-weight:600; color:var(--text-primary); }
  .date-picker-footer { display:flex; gap:12px; justify-content:flex-end; }
  .cancel-btn,.apply-date-btn { padding:10px 24px; border-radius:10px; font-family:'Poppins',sans-serif; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; border:none; }
  .cancel-btn { background:var(--bg-gray-100); color:var(--text-primary); }
  .cancel-btn:hover { background:var(--border-gray); }
  .apply-date-btn { background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark)); color:white; box-shadow:0 4px 12px rgba(3,128,71,0.2); }
  .apply-date-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(3,128,71,0.3); }

  @media (max-width:1024px) { .dashboard-container{padding:16px;} .filter-content{flex-direction:column;align-items:stretch;} .apply-btn{justify-content:center;} }
  @media (max-width:768px) { .date-picker-container{flex-direction:column;width:95%;max-height:90vh;overflow-y:auto;} .date-picker-sidebar{width:100%;border-right:none;border-bottom:1px solid var(--border-gray);border-radius:16px 16px 0 0;flex-direction:row;overflow-x:auto;padding:12px 16px;} .date-preset{white-space:nowrap;} .calendars-wrapper{flex-direction:column;} }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <div class="page-header">
    <h1>Trends Total</h1>
    <p>Track trending topics and keyword performance over time</p>
  </div>

  @if(isset($error))
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span><strong>Warning:</strong> {{ $error }}</span>
  </div>
  @endif

  <!-- Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.data-source.trends') }}">
      <input type="hidden" name="project_id" value="{{ $projectId ?? '' }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate ?? '' }}">
      <input type="hidden" name="end_date"   id="hiddenEndDate"   value="{{ $endDate ?? '' }}">
      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
          Date Range
        </div>
        <div class="date-range-wrapper">
          <button type="button" class="date-picker-trigger" id="datePickerTrigger">
            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="dateRangeDisplay">{{ $startDate ?? '' }} – {{ $endDate ?? '' }}</span>
            <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <button type="submit" class="apply-btn">
          <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
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
          <button type="button" class="nav-btn" id="prevMonth"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
          <div class="calendars-wrapper"><div class="calendar" id="calendar1"></div><div class="calendar" id="calendar2"></div></div>
          <button type="button" class="nav-btn" id="nextMonth"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
        <div class="date-picker-display"><span id="selectedRangeText">{{ $startDate ?? '' }} – {{ $endDate ?? '' }}</span></div>
        <div class="date-picker-footer">
          <button type="button" class="cancel-btn">Cancel</button>
          <button type="button" class="apply-date-btn" id="applyDatePicker">Apply</button>
        </div>
      </div>
    </div>
  </div>

  @if(!empty($chartData['labels']))
  @php
    $totalMentions = 0;
    $topTrend = 'N/A'; $maxMentions = 0;
    foreach($chartData['datasets'] ?? [] as $ds) {
      $sum = array_sum($ds['data'] ?? []);
      $totalMentions += $sum;
      if ($sum > $maxMentions) { $maxMentions = $sum; $topTrend = $ds['label'] ?? 'Unknown'; }
    }
  @endphp

  <!-- Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      </div></div>
      <div class="stat-label">Active Trends</div>
      <div class="stat-value-wrapper"><div class="stat-value">{{ count($chartData['datasets'] ?? []) }}</div><div class="stat-sub">topics</div></div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:75%;"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
      </div></div>
      <div class="stat-label">Total Mentions</div>
      <div class="stat-value-wrapper"><div class="stat-value">{{ number_format($totalMentions) }}</div></div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:85%;"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div></div>
      <div class="stat-label">Days Analyzed</div>
      <div class="stat-value-wrapper"><div class="stat-value">{{ count($chartData['labels'] ?? []) }}</div><div class="stat-sub">days</div></div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:65%;"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
      </div></div>
      <div class="stat-label">Top Trend</div>
      <div class="stat-value-wrapper"><div class="stat-value stat-value-sm">{{ Str::limit($topTrend, 16) }}</div></div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:100%;"></div></div>
    </div>
  </div>

  <!-- Chart Card -->
  <div class="chart-card">
    <div class="chart-header">
      <div class="chart-title-group">
        <h3>Trends Timeline</h3>
        <p class="chart-subtitle">Dual-axis: High volume (left) vs Low volume (right) for better readability</p>
      </div>
      <div class="chart-header-right">
        <button class="toggle-btn" onclick="toggleAllDatasets()">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
          Toggle All
        </button>
        <div class="chart-controls">
          <button class="chart-type-btn active" onclick="changeChartType('line',this)">
            <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg> Line
          </button>
          <button class="chart-type-btn" onclick="changeChartType('bar',this)">
            <svg viewBox="0 0 24 24"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg> Bar
          </button>
          <button class="chart-type-btn" onclick="changeChartType('area',this)">
            <svg viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M7 12L12 7l5 5"/></svg> Area
          </button>
        </div>
      </div>
    </div>
    <div class="chart-container"><canvas id="trendsChart"></canvas></div>
  </div>

  <!-- Table -->
  <div class="table-section">
    <div class="table-header">
      <div class="table-title">
        <h3>Trends Overview</h3>
        <p class="table-subtitle">{{ $startDate }} to {{ $endDate }}</p>
      </div>
      <button class="export-btn" onclick="exportTableToCSV('trends-data.csv')">
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
      </button>
    </div>
    <table class="data-table" id="dataTable">
      <thead>
        <tr><th>#</th><th>Keyword / Topic</th><th>Total Mentions</th><th>Peak Day</th><th>Peak Count</th><th>Trend</th></tr>
      </thead>
      <tbody>
        @forelse($trendsData as $trend)
        @php
          $keyword  = $trend['keyword'] ?? $trend['topic'] ?? 'Unknown';
          $tData    = $trend['data'] ?? [];
          $total    = array_sum(array_column($tData, 'count'));
          $peak = 0; $peakDay = 'N/A';
          foreach ($tData as $pt) { if (($pt['count'] ?? 0) > $peak) { $peak = $pt['count']; $peakDay = $pt['date'] ?? 'N/A'; } }
          $half1 = array_slice($tData, 0, (int)ceil(count($tData)/2));
          $half2 = array_slice($tData, (int)ceil(count($tData)/2));
          $avg1  = count($half1) > 0 ? array_sum(array_column($half1,'count'))/count($half1) : 0;
          $avg2  = count($half2) > 0 ? array_sum(array_column($half2,'count'))/count($half2) : 0;
          $dir   = $avg2 > $avg1 ? 'up' : ($avg2 < $avg1 ? 'down' : 'flat');
          $pct   = $avg1 > 0 ? round((($avg2-$avg1)/$avg1)*100,1) : 0;
        @endphp
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td class="keyword-cell">{{ $keyword }}</td>
          <td>{{ number_format($total) }}</td>
          <td>{{ $peakDay }}</td>
          <td>{{ number_format($peak) }}</td>
          <td>
            @if($dir === 'up') <span class="badge badge-up">+{{ abs($pct) }}%</span>
            @elseif($dir === 'down') <span class="badge badge-down">-{{ abs($pct) }}%</span>
            @else <span class="badge badge-flat">Stable</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-secondary);">No trends data available</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @else
  <div class="empty-state">
    <div class="empty-state-icon"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
    <h3>No Data Available</h3>
    <p>Please select a project and date range to view trends analytics.</p>
  </div>
  @endif

</div>
@endsection

@section('scripts')
<script>
(function(){
  'use strict';
  let s=null,e=null,m1=new Date(),m2=new Date(),sel=true;
  document.addEventListener('DOMContentLoaded',function(){
    const si=document.getElementById('hiddenStartDate'),ei=document.getElementById('hiddenEndDate');
    if(si&&si.value)s=new Date(si.value);else{e=new Date();s=new Date();s.setDate(s.getDate()-6);}
    if(ei&&ei.value)e=new Date(ei.value);
    m1=new Date(s);m2=new Date(s);m2.setMonth(m2.getMonth()+1);
    rc();setup();
  });
  function setup(){
    document.getElementById('datePickerTrigger')?.addEventListener('click',op);
    document.querySelector('.date-picker-overlay')?.addEventListener('click',cl);
    document.querySelector('.cancel-btn')?.addEventListener('click',cl);
    document.getElementById('applyDatePicker')?.addEventListener('click',ap);
    document.getElementById('prevMonth')?.addEventListener('click',()=>sh(-1));
    document.getElementById('nextMonth')?.addEventListener('click',()=>sh(1));
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click',pr));
    document.addEventListener('keydown',ev=>{if(ev.key==='Escape')cl();});
  }
  function op(){document.getElementById('datePickerModal').classList.add('show');rc();}
  function cl(){document.getElementById('datePickerModal').classList.remove('show');}
  function sh(n){m1.setMonth(m1.getMonth()+n);m2.setMonth(m2.getMonth()+n);rc();}
  function pr(ev){
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));ev.target.classList.add('active');
    const t=new Date();t.setHours(0,0,0,0);
    switch(ev.target.dataset.preset){
      case'today':s=new Date(t);e=new Date(t);break;
      case'yesterday':s=new Date(t);s.setDate(t.getDate()-1);e=new Date(s);break;
      case'last7days':e=new Date(t);s=new Date(t);s.setDate(t.getDate()-6);break;
      case'last30days':e=new Date(t);s=new Date(t);s.setDate(t.getDate()-29);break;
      case'thismonth':s=new Date(t.getFullYear(),t.getMonth(),1);e=new Date(t);break;
      case'lastmonth':s=new Date(t.getFullYear(),t.getMonth()-1,1);e=new Date(t.getFullYear(),t.getMonth(),0);break;
    }
    if(ev.target.dataset.preset!=='custom'){m1=new Date(s);m2=new Date(s);m2.setMonth(m2.getMonth()+1);rc();}
  }
  function ap(){const st=fmt(s),en=fmt(e);document.getElementById('hiddenStartDate').value=st;document.getElementById('hiddenEndDate').value=en;document.getElementById('dateRangeDisplay').textContent=`${st} – ${en}`;cl();}
  function rc(){rCal('calendar1',m1);rCal('calendar2',m2);ud();}
  function rCal(id,mo){
    const el=document.getElementById(id);if(!el)return;
    const y=mo.getFullYear(),mn=mo.getMonth(),fi=new Date(y,mn,1),la=new Date(y,mn+1,0),pv=new Date(y,mn,0);
    const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const td=new Date();td.setHours(0,0,0,0);
    let h=`<div class="calendar-month">${MN[mn]} ${y}</div><div class="calendar-weekdays">${['Su','Mo','Tu','We','Th','Fr','Sa'].map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
    for(let i=fi.getDay()-1;i>=0;i--)h+=`<button type="button" class="calendar-day other-month" disabled>${pv.getDate()-i}</button>`;
    for(let d=1;d<=la.getDate();d++){
      const dt=new Date(y,mn,d);dt.setHours(0,0,0,0);const ds=fmt(dt);let cl2='calendar-day';
      if(sm(dt,td))cl2+=' today';if(dt>td)cl2+=' disabled';
      if(s&&e){if(sm(dt,s))cl2+=' selected range-start';else if(sm(dt,e))cl2+=' selected range-end';else if(dt>s&&dt<e)cl2+=' in-range';}
      h+=`<button type="button" class="${cl2}" data-date="${ds}" ${dt>td?'disabled':''}>${d}</button>`;
    }
    const l6=la.getDay();for(let i=1;i<7-l6;i++)h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>';el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(b=>b.addEventListener('click',dc));
  }
  function dc(ev){
    const dt=new Date(ev.target.dataset.date);dt.setHours(0,0,0,0);
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));document.querySelector('[data-preset="custom"]')?.classList.add('active');
    if(sel||dt<s){s=dt;e=dt;sel=false;}else{e=dt>=s?dt:s;if(dt<s){e=s;s=dt;}sel=true;}
    rc();
  }
  function ud(){const t=document.getElementById('selectedRangeText');if(t&&s&&e)t.textContent=`${fmt(s)} – ${fmt(e)}`;}
  function fmt(d){if(!d)return'';return`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
  function sm(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
})();

@if(!empty($chartData['labels']) && !empty($chartData['datasets']))
let trendsChart = null;
let currentChartType = 'line';
const colorPalette = ['#038047','#2FC6F6','#8b5cf6','#f59e0b','#ef4444','#10b981','#3b82f6','#ec4899','#6366f1','#14b8a6'];
const rawDatasets  = @json($chartData['datasets']);
const chartLabels  = @json($chartData['labels']);
const threshold    = 500;

const datasetsWithMax = rawDatasets.map((ds, i) => ({
  ...ds, maxValue: Math.max(...ds.data), originalIndex: i
})).sort((a, b) => b.maxValue - a.maxValue);

function buildDatasets(type) {
  return datasetsWithMax.map((ds, i) => {
    const color   = colorPalette[ds.originalIndex % colorPalette.length];
    const isRight = ds.maxValue <= threshold;
    const isBar   = type === 'bar';
    return {
      label: ds.label,
      data: ds.data,
      borderColor: color,
      backgroundColor: isBar ? color + 'CC' : color + '18',
      borderWidth: 2.5,
      fill: type === 'area',
      tension: 0.4,
      pointRadius: 4,
      pointHoverRadius: 6,
      pointBackgroundColor: '#fff',
      pointBorderColor: color,
      pointBorderWidth: 2,
      borderRadius: isBar ? 6 : 0,
      yAxisID: isRight ? 'y-right' : 'y-left',
    };
  });
}

function initChart(type = 'line') {
  const ctx = document.getElementById('trendsChart').getContext('2d');
  if (trendsChart) trendsChart.destroy();

  trendsChart = new Chart(ctx, {
    type: type === 'area' ? 'line' : type,
    data: { labels: chartLabels, datasets: buildDatasets(type) },
    options: {
      responsive: true, maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          display: true, position: 'top',
          labels: { font:{family:'Poppins',size:12,weight:'600'}, padding:14, usePointStyle:true, pointStyle:'circle',
            generateLabels: chart => Chart.defaults.plugins.legend.labels.generateLabels(chart).map((l,i)=>{
              const isRight = chart.data.datasets[i]?.yAxisID === 'y-right';
              l.text = chart.data.datasets[i]?.label + (isRight ? ' ●' : '');
              return l;
            })
          }
        },
        tooltip: {
          backgroundColor:'#1a202c', padding:14, titleColor:'#fff', bodyColor:'#fff',
          titleFont:{size:13,weight:'600',family:'Poppins'}, bodyFont:{size:12,family:'Poppins'},
          displayColors:true, cornerRadius:10,
          callbacks:{ label: ctx=>`${ctx.dataset.label}: ${new Intl.NumberFormat().format(ctx.parsed.y)}` }
        }
      },
      scales: {
        'y-left':  { type:'linear', position:'left',  beginAtZero:true, grid:{color:'#f1f5f9',drawBorder:false}, ticks:{font:{family:'Poppins',size:11},color:'#64748b',callback:v=>new Intl.NumberFormat('en',{notation:'compact'}).format(v)}, title:{display:true,text:'High Volume',font:{family:'Poppins',size:11,weight:'600'},color:'#475569'} },
        'y-right': { type:'linear', position:'right', beginAtZero:true, grid:{drawOnChartArea:false}, ticks:{font:{family:'Poppins',size:11},color:'#64748b',callback:v=>new Intl.NumberFormat('en',{notation:'compact'}).format(v)}, title:{display:true,text:'Low Volume',font:{family:'Poppins',size:11,weight:'600'},color:'#475569'} },
        x: { grid:{display:false,drawBorder:false}, ticks:{font:{family:'Poppins',size:11},color:'#64748b',maxRotation:45,minRotation:0} }
      },
      animation: { duration:700, easing:'easeInOutQuart' }
    }
  });
}

function changeChartType(type, btn) {
  currentChartType = type;
  document.querySelectorAll('.chart-type-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  initChart(type);
}

function toggleAllDatasets() {
  if (!trendsChart) return;
  const allVisible = trendsChart.data.datasets.every(ds => !ds.hidden);
  trendsChart.data.datasets.forEach(ds => ds.hidden = allVisible);
  trendsChart.update();
}

document.addEventListener('DOMContentLoaded', () => initChart('line'));
@endif

function exportTableToCSV(filename) {
  const table = document.getElementById('dataTable');
  let csv = [];
  for (let row of table.rows) {
    let cells = [];
    for (let cell of row.cells) cells.push('"'+cell.textContent.trim().replace(/"/g,'""')+'"');
    csv.push(cells.join(','));
  }
  const blob = new Blob([csv.join('\n')],{type:'text/csv'});
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');a.href=url;a.download=filename;a.click();URL.revokeObjectURL(url);
}
</script>
@endsection