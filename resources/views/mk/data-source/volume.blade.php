@extends('mk.layouts.app')

@section('title', 'Volume Total - Data Source')

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
  .page-header p  { font-size: 14px; color: var(--text-secondary); margin: 0; }

  /* Filter */
  .filter-card { background: var(--bg-white); border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); }
  .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
  .filter-label { font-size: 14px; font-weight: 600; color: var(--text-primary); white-space: nowrap; display: flex; align-items: center; gap: 6px; }
  .filter-label svg { width: 18px; height: 18px; stroke: var(--text-secondary); fill: none; }
  .filter-select { padding: 12px 16px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; color: var(--text-primary); cursor: pointer; transition: all 0.2s; min-width: 160px; outline: none; }
  .filter-select:hover, .filter-select:focus { border-color: var(--primary-green); box-shadow: 0 0 0 3px rgba(3,128,71,0.1); background: var(--bg-white); }
  .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }
  .date-picker-trigger { display: flex; align-items: center; gap: 12px; padding: 12px 20px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; color: var(--text-primary); cursor: pointer; transition: all 0.2s; max-width: 340px; width: 100%; }
  .date-picker-trigger:hover { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(3,128,71,0.1); }
  .date-picker-trigger svg { width: 18px; height: 18px; stroke: var(--text-secondary); fill: none; flex-shrink: 0; }
  .date-picker-trigger span { flex: 1; text-align: left; }
  .apply-btn { padding: 12px 28px; background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); color: white; border: none; border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(3,128,71,0.2); }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,0.3); }
  .apply-btn svg { width: 18px; height: 18px; stroke: white; fill: none; }

  /* Stats */
  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 24px; }
  .stat-card { background: var(--bg-white); border-radius: 16px; padding: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden; }
  .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--primary-green), var(--primary-green-dark)); opacity: 0; transition: opacity 0.3s; }
  .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-green); }
  .stat-card:hover::before { opacity: 1; }
  .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
  .stat-icon-wrapper { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, rgba(3,128,71,0.1), rgba(3,128,71,0.05)); display: flex; align-items: center; justify-content: center; }
  .stat-icon { width: 28px; height: 28px; stroke: var(--primary-green); fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
  .stat-label { font-size: 13px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
  .stat-value-wrapper { display: flex; align-items: baseline; gap: 10px; margin-bottom: 16px; }
  .stat-value { font-size: 36px; font-weight: 700; color: var(--text-primary); line-height: 1; }
  .stat-sub { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
  .stat-progress { height: 6px; background: var(--bg-gray-100); border-radius: 10px; overflow: hidden; }
  .stat-progress-bar { height: 100%; background: linear-gradient(90deg, var(--primary-green), var(--primary-green-dark)); border-radius: 10px; transition: width 1s ease-out; }

  /* Media Breakdown */
  .media-breakdown-card { background: var(--bg-white); border-radius: 16px; padding: 28px; margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); }
  .section-title { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0; }
  .section-subtitle { font-size: 13px; color: var(--text-secondary); margin: 0 0 20px 0; }
  .media-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; }
  .media-item { display: flex; align-items: center; gap: 12px; padding: 16px; background: var(--bg-gray-50); border-radius: 12px; border: 1px solid transparent; cursor: pointer; transition: all 0.25s; }
  .media-item:hover { background: #e0f2fe; border-color: #7dd3fc; transform: translateY(-2px); }
  .media-item.active { background: #dbeafe; border-color: #0ea5e9; box-shadow: 0 4px 12px rgba(14,165,233,0.15); }
  .media-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .media-icon svg { width: 22px; height: 22px; }
  .media-info h4 { font-size: 11px; font-weight: 700; color: var(--text-secondary); margin: 0 0 2px 0; text-transform: uppercase; letter-spacing: 0.5px; }
  .media-count { font-size: 20px; font-weight: 700; color: var(--text-primary); line-height: 1; margin: 0; }
  .media-pct { font-size: 11px; color: var(--text-secondary); }

  /* Chart */
  .chart-card { background: var(--bg-white); border-radius: 16px; padding: 28px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); margin-bottom: 24px; transition: all 0.3s; }
  .chart-card:hover { box-shadow: var(--shadow-md); }
  .chart-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid var(--bg-gray-50); flex-wrap: wrap; gap: 16px; }
  .chart-header-left h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; }
  .chart-filter-label { font-size: 13px; font-weight: 600; color: #0ea5e9; }
  .chart-subtitle { font-size: 13px; color: var(--text-secondary); }
  .chart-header-right { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
  .reset-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; color: var(--text-secondary); cursor: pointer; transition: all 0.2s; }
  .reset-btn:hover { border-color: var(--primary-green); color: var(--primary-green); }
  .reset-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }
  .chart-controls { display: flex; gap: 6px; background: var(--bg-gray-50); padding: 4px; border-radius: 10px; }
  .chart-type-btn { padding: 8px 14px; border: none; background: transparent; color: var(--text-secondary); font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; border-radius: 8px; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
  .chart-type-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }
  .chart-type-btn:hover { background: var(--bg-white); color: var(--text-primary); }
  .chart-type-btn.active { background: var(--primary-green); color: white; box-shadow: 0 2px 8px rgba(3,128,71,0.25); }
  .chart-container { position: relative; height: 360px; }

  /* Table */
  .table-section { background: var(--bg-white); border-radius: 16px; padding: 28px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); margin-bottom: 24px; overflow-x: auto; }
  .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid var(--bg-gray-50); flex-wrap: wrap; gap: 16px; }
  .table-title h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; }
  .table-subtitle { font-size: 13px; color: var(--text-secondary); }
  .export-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; color: var(--text-primary); cursor: pointer; transition: all 0.2s; box-shadow: var(--shadow-sm); }
  .export-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; }
  .export-btn:hover { border-color: var(--primary-green); color: var(--primary-green); transform: translateY(-1px); box-shadow: var(--shadow-md); }
  .data-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 800px; }
  .data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--border-gray); background: var(--bg-gray-50); white-space: nowrap; }
  .data-table td { padding: 14px 16px; font-size: 13px; color: var(--text-primary); border-bottom: 1px solid var(--bg-gray-100); vertical-align: middle; }
  .data-table tbody tr { transition: background 0.2s; }
  .data-table tbody tr:hover { background: var(--bg-gray-50); }
  .data-table tbody tr:last-child td { border-bottom: none; }
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
  .date-picker-overlay { position: absolute; top:0; left:0; right:0; bottom:0; cursor: pointer; }
  .date-picker-container { position: relative; background: #fff; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); display: flex; max-width: 900px; width: 90%; z-index: 10001; animation: slideUp 0.3s ease-out; }
  @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
  .date-picker-sidebar { width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray); padding: 16px 12px; border-radius: 16px 0 0 16px; display: flex; flex-direction: column; gap: 4px; flex-shrink: 0; }
  .date-preset { padding: 10px 16px; background: transparent; border: none; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500; color: var(--text-primary); text-align: left; cursor: pointer; transition: all 0.2s; }
  .date-preset:hover { background: var(--bg-white); color: var(--primary-green); }
  .date-preset.active { background: var(--primary-green); color: white; }
  .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; }
  .date-picker-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
  .nav-btn { width: 36px; height: 36px; border-radius: 8px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
  .nav-btn:hover { background: var(--primary-green); border-color: var(--primary-green); color: white; }
  .nav-btn svg { width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2; }
  .calendars-wrapper { display: flex; gap: 24px; flex: 1; }
  .calendar { flex: 1; }
  .calendar-month { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-align: center; }
  .calendar-weekdays { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; margin-bottom: 8px; }
  .weekday { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
  .calendar-days { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; }
  .calendar-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 500; border-radius: 8px; cursor: pointer; transition: all 0.2s; color: var(--text-primary); background: transparent; border: none; padding: 0; }
  .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
  .calendar-day.other-month { color: #cbd5e1; cursor: default; }
  .calendar-day.disabled { color: #e2e8f0; cursor: not-allowed; }
  .calendar-day.today { border: 2px solid var(--primary-green); }
  .calendar-day.selected, .calendar-day.range-start, .calendar-day.range-end { background: var(--primary-green); color: white; }
  .calendar-day.in-range { background: rgba(3,128,71,0.1); color: var(--primary-green); }
  .date-picker-display { padding: 14px 20px; background: var(--bg-gray-50); border-radius: 12px; text-align: center; margin-bottom: 20px; border: 1px solid var(--border-gray); }
  .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }
  .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }
  .cancel-btn, .apply-date-btn { padding: 10px 24px; border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
  .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
  .cancel-btn:hover { background: var(--border-gray); }
  .apply-date-btn { background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark)); color: white; box-shadow: 0 4px 12px rgba(3,128,71,0.2); }
  .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,0.3); }

  @media (max-width: 1024px) { .dashboard-container { padding: 16px; } .filter-content { flex-direction: column; align-items: stretch; } .apply-btn { justify-content: center; } }
  @media (max-width: 768px) { .date-picker-container { flex-direction: column; width: 95%; max-height: 90vh; overflow-y: auto; } .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; padding: 12px 16px; } .date-preset { white-space: nowrap; } .calendars-wrapper { flex-direction: column; } }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <div class="page-header">
    <h1>Volume Total</h1>
    <p>Track content volume and posting activity across media platforms</p>
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
    <form id="filterForm" method="GET" action="{{ route('mk.data-source.volume') }}">
      <input type="hidden" name="project_id" value="{{ $projectId ?? '' }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate ?? '' }}">
      <input type="hidden" name="end_date"   id="hiddenEndDate"   value="{{ $endDate ?? '' }}">
      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Filter
        </div>
        <select name="media" class="filter-select">
          <option value="all"       {{ ($media ?? 'all') === 'all'       ? 'selected' : '' }}>All Media</option>
          <option value="doc"       {{ ($media ?? '') === 'doc'          ? 'selected' : '' }}>News / Online</option>
          <option value="twit"      {{ ($media ?? '') === 'twit'         ? 'selected' : '' }}>Twitter</option>
          <option value="fb"        {{ ($media ?? '') === 'fb'           ? 'selected' : '' }}>Facebook</option>
          <option value="instagram" {{ ($media ?? '') === 'instagram'    ? 'selected' : '' }}>Instagram</option>
          <option value="youtube"   {{ ($media ?? '') === 'youtube'      ? 'selected' : '' }}>YouTube</option>
          <option value="tiktok"    {{ ($media ?? '') === 'tiktok'       ? 'selected' : '' }}>TikTok</option>
        </select>
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
    $days      = count($chartData['labels']);
    $avgDaily  = $days > 0 ? round($totalVolume / $days) : 0;
    $peakVol   = max($chartData['values']);
  @endphp

  <!-- Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
      </div></div>
      <div class="stat-label">Total Volume</div>
      <div class="stat-value-wrapper"><div class="stat-value">{{ number_format($totalVolume) }}</div></div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:85%;"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      </div></div>
      <div class="stat-label">Average Daily</div>
      <div class="stat-value-wrapper"><div class="stat-value">{{ number_format($avgDaily) }}</div><div class="stat-sub">posts/day</div></div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:65%;"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      </div></div>
      <div class="stat-label">Peak Volume</div>
      <div class="stat-value-wrapper"><div class="stat-value">{{ number_format($peakVol) }}</div><div class="stat-sub">highest/day</div></div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:100%;"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div></div>
      <div class="stat-label">Days Tracked</div>
      <div class="stat-value-wrapper"><div class="stat-value">{{ $days }}</div><div class="stat-sub">days</div></div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:70%;"></div></div>
    </div>
  </div>

  <!-- Media Breakdown -->
  @if(!empty($byMedia))
  @php
    $mediaConfig = [
      'doc'       => ['label' => 'News/Online', 'color' => '#3b82f6', 'icon' => 'news'],
      'twit'      => ['label' => 'Twitter',     'color' => '#1DA1F2', 'icon' => 'twitter'],
      'fb'        => ['label' => 'Facebook',    'color' => '#1877F2', 'icon' => 'facebook'],
      'instagram' => ['label' => 'Instagram',   'color' => '#E4405F', 'icon' => 'instagram'],
      'youtube'   => ['label' => 'YouTube',     'color' => '#FF0000', 'icon' => 'youtube'],
      'tiktok'    => ['label' => 'TikTok',      'color' => '#000000', 'icon' => 'tiktok'],
    ];
  @endphp
  <div class="media-breakdown-card">
    <h3 class="section-title">Volume by Media Platform</h3>
    <p class="section-subtitle">Click a platform to filter the chart below</p>
    <div class="media-grid">
      @foreach($byMedia as $key => $count)
      @php $cfg = $mediaConfig[$key] ?? ['label' => strtoupper($key), 'color' => '#6B7280', 'icon' => 'default']; @endphp
      <div class="media-item" data-media="{{ $key }}" onclick="filterChartByMedia('{{ $key }}')">
        <div class="media-icon" style="background: {{ $cfg['color'] }}18;">
          <svg viewBox="0 0 24 24" style="stroke: {{ $cfg['color'] }}; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; width:22px; height:22px;">
            @if($key === 'doc') <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            @elseif($key === 'twit') <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
            @elseif($key === 'fb') <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
            @elseif($key === 'instagram') <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
            @elseif($key === 'youtube') <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>
            @elseif($key === 'tiktok') <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
            @else <circle cx="12" cy="12" r="10"/>
            @endif
          </svg>
        </div>
        <div class="media-info">
          <h4>{{ $cfg['label'] }}</h4>
          <p class="media-count">{{ number_format($count) }}</p>
          <span class="media-pct">{{ $totalVolume > 0 ? number_format(($count/$totalVolume)*100,1) : 0 }}%</span>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <!-- Chart Card -->
  <div class="chart-card">
    <div class="chart-header">
      <div class="chart-header-left">
        <h3>Volume Trend <span class="chart-filter-label" id="chartMediaLabel"></span></h3>
        <p class="chart-subtitle">Daily posting activity over selected period</p>
      </div>
      <div class="chart-header-right">
        <button class="reset-btn" id="resetFilterBtn" onclick="resetChartFilter()" style="display:none;">
          <svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
          Show All
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
    <div class="chart-container"><canvas id="volumeChart"></canvas></div>
  </div>

  <!-- Data Table -->
  <div class="table-section">
    <div class="table-header">
      <div class="table-title">
        <h3>Detailed Data</h3>
        <p class="table-subtitle">{{ $startDate }} to {{ $endDate }}</p>
      </div>
      <button class="export-btn" onclick="exportTableToCSV('volume-data.csv')">
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
      </button>
    </div>
    @php
      $tableData = $volumeData ?? [];
      if (empty($tableData) && !empty($chartData['labels'])) {
        foreach ($chartData['labels'] as $i => $label) {
          $tableData[] = ['date' => $label, 'volume' => $chartData['values'][$i] ?? 0, 'by_media' => $chartData['by_media'][$i] ?? []];
        }
      }
    @endphp
    <table class="data-table" id="dataTable">
      <thead>
        <tr>
          <th>#</th><th>Date</th><th>Total Volume</th><th>Change</th>
          <th>News</th><th>Twitter</th><th>Facebook</th><th>Instagram</th><th>YouTube</th><th>TikTok</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tableData as $item)
        @php
          $prev   = $loop->index > 0 ? ($tableData[$loop->index-1]['volume'] ?? 0) : null;
          $cur    = $item['volume'] ?? 0;
          $change = $prev !== null && $prev > 0 ? (($cur - $prev) / $prev) * 100 : null;
        @endphp
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item['date'] ?? 'N/A' }}</td>
          <td><strong>{{ number_format($cur) }}</strong></td>
          <td>
            @if($change !== null)
              <span class="badge {{ $change >= 0 ? 'badge-up' : 'badge-down' }}">{{ $change >= 0 ? '+' : '' }}{{ number_format($change,1) }}%</span>
            @else <span class="badge badge-flat">–</span>
            @endif
          </td>
          <td>{{ number_format($item['by_media']['doc'] ?? 0) }}</td>
          <td>{{ number_format($item['by_media']['twit'] ?? 0) }}</td>
          <td>{{ number_format($item['by_media']['fb'] ?? 0) }}</td>
          <td>{{ number_format($item['by_media']['instagram'] ?? 0) }}</td>
          <td>{{ number_format($item['by_media']['youtube'] ?? 0) }}</td>
          <td>{{ number_format($item['by_media']['tiktok'] ?? 0) }}</td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;padding:40px;color:var(--text-secondary);">No data available</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @else
  <div class="empty-state">
    <div class="empty-state-icon"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div>
    <h3>No Data Available</h3>
    <p>Please select a project and date range to view volume analytics.</p>
  </div>
  @endif

</div>
@endsection

@section('scripts')
<script>
(function () {
  'use strict';
  let selectedStartDate=null,selectedEndDate=null,currentMonth1=new Date(),currentMonth2=new Date(),selectingStart=true;
  document.addEventListener('DOMContentLoaded',function(){
    const s=document.getElementById('hiddenStartDate'),e=document.getElementById('hiddenEndDate');
    if(s&&s.value)selectedStartDate=new Date(s.value); else{selectedEndDate=new Date();selectedStartDate=new Date();selectedStartDate.setDate(selectedStartDate.getDate()-6);}
    if(e&&e.value)selectedEndDate=new Date(e.value);
    currentMonth1=new Date(selectedStartDate);currentMonth2=new Date(selectedStartDate);currentMonth2.setMonth(currentMonth2.getMonth()+1);
    renderCalendars();setupListeners();
  });
  function setupListeners(){
    document.getElementById('datePickerTrigger')?.addEventListener('click',open);
    document.querySelector('.date-picker-overlay')?.addEventListener('click',close);
    document.querySelector('.cancel-btn')?.addEventListener('click',close);
    document.getElementById('applyDatePicker')?.addEventListener('click',apply);
    document.getElementById('prevMonth')?.addEventListener('click',()=>shift(-1));
    document.getElementById('nextMonth')?.addEventListener('click',()=>shift(1));
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click',preset));
    document.addEventListener('keydown',e=>{if(e.key==='Escape')close();});
  }
  function open(){document.getElementById('datePickerModal').classList.add('show');renderCalendars();}
  function close(){document.getElementById('datePickerModal').classList.remove('show');}
  function shift(n){currentMonth1.setMonth(currentMonth1.getMonth()+n);currentMonth2.setMonth(currentMonth2.getMonth()+n);renderCalendars();}
  function preset(e){
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));e.target.classList.add('active');
    const today=new Date();today.setHours(0,0,0,0);
    switch(e.target.dataset.preset){
      case 'today':selectedStartDate=new Date(today);selectedEndDate=new Date(today);break;
      case 'yesterday':selectedStartDate=new Date(today);selectedStartDate.setDate(today.getDate()-1);selectedEndDate=new Date(selectedStartDate);break;
      case 'last7days':selectedEndDate=new Date(today);selectedStartDate=new Date(today);selectedStartDate.setDate(today.getDate()-6);break;
      case 'last30days':selectedEndDate=new Date(today);selectedStartDate=new Date(today);selectedStartDate.setDate(today.getDate()-29);break;
      case 'thismonth':selectedStartDate=new Date(today.getFullYear(),today.getMonth(),1);selectedEndDate=new Date(today);break;
      case 'lastmonth':selectedStartDate=new Date(today.getFullYear(),today.getMonth()-1,1);selectedEndDate=new Date(today.getFullYear(),today.getMonth(),0);break;
    }
    if(e.target.dataset.preset!=='custom'){currentMonth1=new Date(selectedStartDate);currentMonth2=new Date(selectedStartDate);currentMonth2.setMonth(currentMonth2.getMonth()+1);renderCalendars();}
  }
  function apply(){const start=fmt(selectedStartDate),end=fmt(selectedEndDate);document.getElementById('hiddenStartDate').value=start;document.getElementById('hiddenEndDate').value=end;document.getElementById('dateRangeDisplay').textContent=`${start} – ${end}`;close();}
  function renderCalendars(){renderCal('calendar1',currentMonth1);renderCal('calendar2',currentMonth2);updateDisplay();}
  function renderCal(id,month){
    const el=document.getElementById(id);if(!el)return;
    const y=month.getFullYear(),m=month.getMonth(),first=new Date(y,m,1),last=new Date(y,m+1,0),prevLast=new Date(y,m,0);
    const MONTHS=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const today=new Date();today.setHours(0,0,0,0);
    let html=`<div class="calendar-month">${MONTHS[m]} ${y}</div><div class="calendar-weekdays">${['Su','Mo','Tu','We','Th','Fr','Sa'].map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
    for(let i=first.getDay()-1;i>=0;i--)html+=`<button type="button" class="calendar-day other-month" disabled>${prevLast.getDate()-i}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const date=new Date(y,m,d);date.setHours(0,0,0,0);const ds=fmt(date);let cls='calendar-day';
      if(same(date,today))cls+=' today';if(date>today)cls+=' disabled';
      if(selectedStartDate&&selectedEndDate){if(same(date,selectedStartDate))cls+=' selected range-start';else if(same(date,selectedEndDate))cls+=' selected range-end';else if(date>selectedStartDate&&date<selectedEndDate)cls+=' in-range';}
      html+=`<button type="button" class="${cls}" data-date="${ds}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const l6=last.getDay();for(let i=1;i<7-l6;i++)html+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    html+='</div>';el.innerHTML=html;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(b=>b.addEventListener('click',dayClick));
  }
  function dayClick(e){
    const date=new Date(e.target.dataset.date);date.setHours(0,0,0,0);
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));document.querySelector('[data-preset="custom"]')?.classList.add('active');
    if(selectingStart||date<selectedStartDate){selectedStartDate=date;selectedEndDate=date;selectingStart=false;}
    else{selectedEndDate=date>=selectedStartDate?date:selectedStartDate;if(date<selectedStartDate){selectedEndDate=selectedStartDate;selectedStartDate=date;}selectingStart=true;}
    renderCalendars();
  }
  function updateDisplay(){const t=document.getElementById('selectedRangeText');if(t&&selectedStartDate&&selectedEndDate)t.textContent=`${fmt(selectedStartDate)} – ${fmt(selectedEndDate)}`;}
  function fmt(date){if(!date)return'';return`${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;}
  function same(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
})();

@if(!empty($chartData['labels']))
let volumeChart = null;
let currentChartType = 'line';
let currentMediaFilter = null;

const fullChartData = {
  labels:  @json($chartData['labels']),
  values:  @json($chartData['values']),
  byMedia: @json($chartData['by_media'] ?? [])
};

const mediaColorMap = {
  doc: '#3b82f6', twit: '#1DA1F2', fb: '#1877F2',
  instagram: '#E4405F', youtube: '#FF0000', tiktok: '#000000'
};
const mediaLabelMap = { doc:'News/Online', twit:'Twitter', fb:'Facebook', instagram:'Instagram', youtube:'YouTube', tiktok:'TikTok' };

function filterChartByMedia(key) {
  currentMediaFilter = key;
  document.querySelectorAll('.media-item').forEach(el => el.classList.remove('active'));
  document.querySelector(`[data-media="${key}"]`)?.classList.add('active');
  document.getElementById('resetFilterBtn').style.display = 'inline-flex';
  document.getElementById('chartMediaLabel').textContent = `— ${mediaLabelMap[key] || key}`;
  initChart(currentChartType, key);
}

function resetChartFilter() {
  currentMediaFilter = null;
  document.querySelectorAll('.media-item').forEach(el => el.classList.remove('active'));
  document.getElementById('resetFilterBtn').style.display = 'none';
  document.getElementById('chartMediaLabel').textContent = '';
  initChart(currentChartType);
}

function initChart(type = 'line', filterMedia = null) {
  const ctx = document.getElementById('volumeChart').getContext('2d');
  if (volumeChart) volumeChart.destroy();

  let values;
  if (filterMedia && fullChartData.byMedia && fullChartData.byMedia.length > 0) {
    values = fullChartData.byMedia.map(m => parseInt(m[filterMedia] || 0));
  } else {
    values = fullChartData.values;
  }

  const color = filterMedia && mediaColorMap[filterMedia] ? mediaColorMap[filterMedia] : '#038047';
  const bg    = color + '18';
  const isBar = type === 'bar';

  volumeChart = new Chart(ctx, {
    type: isBar ? 'bar' : 'line',
    data: {
      labels: fullChartData.labels,
      datasets: [{
        label: filterMedia ? `${mediaLabelMap[filterMedia] || filterMedia} Volume` : 'Total Volume',
        data: values,
        borderColor: color,
        backgroundColor: isBar ? color + 'CC' : bg,
        borderWidth: isBar ? 2 : 3,
        fill: !isBar,
        tension: 0.4,
        pointRadius: isBar ? 0 : 5,
        pointHoverRadius: isBar ? 0 : 7,
        pointBackgroundColor: '#fff',
        pointBorderColor: color,
        pointBorderWidth: 2,
        borderRadius: isBar ? 8 : 0,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: true, position: 'top', labels: { font: { family:'Poppins', size:13, weight:'600' }, padding:16, usePointStyle:true, pointStyle:'circle' } },
        tooltip: { backgroundColor:'#1a202c', padding:14, titleColor:'#fff', bodyColor:'#fff', titleFont:{size:13,weight:'600',family:'Poppins'}, bodyFont:{size:12,family:'Poppins'}, displayColors:false, cornerRadius:10, callbacks:{ label: ctx=>'Volume: '+ctx.parsed.y.toLocaleString() } }
      },
      scales: {
        y: { beginAtZero:true, grid:{color:'#f1f5f9',drawBorder:false}, ticks:{font:{family:'Poppins',size:12},color:'#64748b',padding:8,callback:v=>new Intl.NumberFormat('en',{notation:'compact'}).format(v)} },
        x: { grid:{display:false,drawBorder:false}, ticks:{font:{family:'Poppins',size:12},color:'#64748b',padding:8,maxRotation:45,minRotation:0} }
      },
      animation: { duration:700, easing:'easeInOutQuart' }
    }
  });
}

function changeChartType(type, btn) {
  currentChartType = type;
  document.querySelectorAll('.chart-type-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  initChart(type, currentMediaFilter);
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