@extends('mk.layouts.app')

@section('title', 'TikTok Top Hashtags - SMADIMENT')

@section('styles')
<style>
    :root {
        --primary-green: #038047;
        --primary-green-dark: #026738;
        --accent-blue: #2FC6F6;
        --text-primary: #1a202c;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --bg-white: #ffffff;
        --bg-gray-50: #f8fafc;
        --bg-gray-100: #f1f5f9;
        --border-gray: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    body { background: var(--bg-gray-50); }

    .dashboard-container { padding: 24px; max-width: 1600px; margin: 0 auto; }

    /* PAGE HEADER */
    .page-header { margin-bottom: 32px; display: flex; align-items: flex-end; gap: 16px; }
    .page-header-text h1 {
        font-size: 28px; font-weight: 700; color: var(--text-primary);
        margin: 0 0 6px 0;
    }
    .page-header-text p { font-size: 14px; color: var(--text-secondary); margin: 0; }
    .tiktok-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(135deg, #010101 0%, #2d2d2d 100%);
        border-radius: 100px; padding: 6px 14px;
        font-size: 12px; font-weight: 600; color: #ffffff;
        white-space: nowrap; flex-shrink: 0; margin-bottom: 4px;
    }
    .tiktok-badge .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: linear-gradient(135deg, #69C9D0, #EE1D52);
    }

    /* FILTER CARD */
    .filter-card {
        background: var(--bg-white); border-radius: 16px; padding: 20px 24px;
        margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray);
    }
    .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .filter-label {
        font-size: 14px; font-weight: 600; color: var(--text-primary);
        white-space: nowrap; display: flex; align-items: center; gap: 8px;
    }
    .filter-label svg { width: 16px; height: 16px; stroke: var(--primary-green); fill: none; flex-shrink: 0; }
    .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }

    .date-picker-trigger {
        display: flex; align-items: center; gap: 12px; padding: 12px 20px;
        background: var(--bg-gray-50); border: 1px solid var(--border-gray);
        border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500;
        color: var(--text-primary); cursor: pointer; transition: all 0.2s; width: 100%; max-width: 400px;
    }
    .date-picker-trigger:hover {
        border-color: var(--primary-green); background: var(--bg-white);
        box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
    }
    .date-picker-trigger svg:first-child { width: 18px; height: 18px; stroke: var(--text-secondary); fill: none; flex-shrink: 0; }
    .date-picker-trigger span { flex: 1; text-align: left; }
    .date-picker-trigger svg:last-child { width: 16px; height: 16px; margin-left: auto; stroke: var(--text-secondary); fill: none; }

    .apply-btn {
        padding: 12px 28px;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        color: white; border: none; border-radius: 12px; font-family: 'Poppins', sans-serif;
        font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2); white-space: nowrap;
    }
    .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3); }
    .apply-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; }

    /* STATS GRID */
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }

    .stat-card {
        background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 16px;
        padding: 22px 24px; transition: all 0.3s; position: relative; overflow: hidden;
    }
    .stat-card::after {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--primary-green), var(--accent-blue));
        transform: scaleX(0); transform-origin: left; transition: transform 0.3s;
    }
    .stat-card:hover { border-color: #c8e6d4; transform: translateY(-2px); box-shadow: var(--shadow-md); }
    .stat-card:hover::after { transform: scaleX(1); }

    .stat-icon {
        width: 40px; height: 40px; border-radius: 12px;
        background: rgba(3, 128, 71, 0.08); display: flex; align-items: center; justify-content: center;
        margin-bottom: 16px;
    }
    .stat-icon svg { width: 20px; height: 20px; stroke: var(--primary-green); fill: none; }
    .stat-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
    .stat-value { font-size: 30px; font-weight: 800; color: var(--text-primary); line-height: 1.1; }
    .stat-value.small { font-size: 18px; word-break: break-all; color: var(--primary-green); }

    /* TABLE CONTAINER */
    .table-container {
        background: var(--bg-white); border-radius: 16px;
        border: 1px solid var(--border-gray); box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .table-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px 24px; border-bottom: 1px solid var(--border-gray);
    }
    .table-header-title { font-size: 16px; font-weight: 700; color: var(--text-primary); }
    .table-header-sub   { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
    .table-wrapper { overflow-x: auto; }

    .status-table { width: 100%; border-collapse: collapse; font-family: 'Poppins', sans-serif; }
    .status-table thead { background: var(--bg-gray-50); }
    .status-table th {
        padding: 14px 24px; text-align: left; font-size: 10px; font-weight: 700;
        color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.2px;
        border-bottom: 1px solid var(--border-gray); white-space: nowrap;
    }
    .status-table th.text-center { text-align: center; }

    .status-table tbody tr { border-bottom: 1px solid var(--bg-gray-50); transition: background 0.15s; }
    .status-table tbody tr:hover { background: var(--bg-gray-50); }
    .status-table tbody tr:last-child { border-bottom: none; }
    .status-table td { padding: 16px 24px; font-size: 14px; color: var(--text-primary); vertical-align: middle; }

    .rank-cell { font-weight: 800; color: var(--text-muted); font-size: 14px; width: 60px; }
    .rank-top  { color: var(--primary-green) !important; }

    .hashtag-cell { display: flex; align-items: center; gap: 12px; }
    .hashtag-text { font-weight: 700; font-size: 15px; color: var(--text-primary); }
    .hashtag-text span { color: var(--primary-green); }

    .bar-cell { min-width: 200px; }
    .bar-wrap  { display: flex; align-items: center; gap: 10px; }
    .bar-track { flex: 1; height: 6px; background: var(--bg-gray-100); border-radius: 4px; overflow: hidden; }
    .bar-fill  {
        height: 100%; border-radius: 4px;
        background: linear-gradient(90deg, var(--primary-green), var(--accent-blue));
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .bar-count { font-size: 12px; font-weight: 600; color: var(--text-muted); white-space: nowrap; min-width: 44px; text-align: right; }

    .count-cell { text-align: center; font-weight: 700; font-size: 15px; color: var(--text-primary); }
    .action-cell { text-align: center; width: 100px; }

    .btn-view {
        padding: 7px 14px; background: transparent; border: 1px solid var(--border-gray);
        border-radius: 8px; font-size: 12px; font-weight: 600; color: var(--text-secondary);
        cursor: pointer; transition: all 0.2s; text-decoration: none;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-view:hover { background: var(--primary-green); color: white; border-color: var(--primary-green); }
    .btn-view svg { width: 11px; height: 11px; stroke: currentColor; fill: none; }

    /* PAGINATION */
    .pagination {
        display: flex; justify-content: space-between; align-items: center;
        gap: 8px; padding: 20px 24px; background: var(--bg-gray-50);
        border-top: 1px solid var(--border-gray); flex-wrap: wrap;
    }
    .pagination-info { font-size: 13px; color: var(--text-muted); font-weight: 500; }
    .page-btn {
        width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--border-gray);
        background: var(--bg-white); color: var(--text-secondary); font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;
    }
    .page-btn:hover:not(:disabled) { border-color: var(--primary-green); color: var(--primary-green); background: rgba(3, 128, 71, 0.06); }
    .page-btn.active { background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); color: white; border-color: transparent; box-shadow: 0 2px 8px rgba(3, 128, 71, 0.3); }
    .page-btn:disabled { opacity: 0.35; cursor: not-allowed; }

    /* SKELETON */
    .skeleton-line {
        height: 14px;
        background: linear-gradient(90deg, var(--bg-gray-100) 25%, var(--bg-gray-50) 50%, var(--bg-gray-100) 75%);
        background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 6px; margin-bottom: 10px;
    }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    /* EMPTY STATE */
    .empty-state { text-align: center; padding: 80px 20px; }
    .empty-state svg { width: 64px; height: 64px; stroke: var(--text-muted); fill: none; margin-bottom: 16px; }
    .empty-state h3  { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px; }
    .empty-state p   { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* ALERT */
    .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px; }
    .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

    /* DATE PICKER MODAL */
    .date-picker-modal {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000;
        display: none; align-items: center; justify-content: center;
        background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(8px);
    }
    .date-picker-modal.show { display: flex; }
    .date-picker-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; cursor: pointer; }

    .date-picker-container {
        position: relative; background: #ffffff; border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3); display: flex;
        max-width: 900px; width: 90%; max-height: 90vh; z-index: 10001;
        animation: slideUpModal 0.3s ease-out;
    }
    @keyframes slideUpModal { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

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
    .date-preset:hover  { background: var(--bg-white); color: var(--primary-green); }
    .date-preset.active { background: var(--primary-green); color: white; }

    .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; overflow: hidden; }
    .date-picker-header  { display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px; }

    .nav-btn {
        width: 36px; height: 36px; border-radius: 8px; background: var(--bg-gray-50);
        border: 1px solid var(--border-gray); display: flex; align-items: center;
        justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0;
    }
    .nav-btn:hover { background: var(--primary-green); border-color: var(--primary-green); color: white; }
    .nav-btn svg { width: 20px; height: 20px; stroke: currentColor; fill: none; }

    .calendars-wrapper { display: flex; gap: 24px; flex: 1; min-height: 0; }
    .calendar          { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .calendar-month    { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-align: center; }
    .calendar-weekdays { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 8px; }
    .weekday           { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
    .calendar-days     { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }

    .calendar-day {
        aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 500; border-radius: 8px; cursor: pointer;
        transition: all 0.2s; color: var(--text-primary); background: transparent; border: none; padding: 0;
    }
    .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
    .calendar-day.other-month { color: #cbd5e1; cursor: default; }
    .calendar-day.disabled    { color: #e2e8f0; cursor: not-allowed; }
    .calendar-day.today       { border: 2px solid var(--primary-green); }
    .calendar-day.in-range    { background: rgba(3, 128, 71, 0.1); color: var(--primary-green); }
    .calendar-day.range-start,
    .calendar-day.range-end,
    .calendar-day.selected    { background: var(--primary-green) !important; color: white !important; border: none !important; border-radius: 8px !important; }

    .date-picker-display {
        padding: 16px 20px; background: var(--bg-gray-50); border-radius: 12px;
        text-align: center; margin-bottom: 20px; border: 1px solid var(--border-gray);
    }
    .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }

    .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }
    .cancel-btn {
        padding: 10px 24px; border-radius: 10px; font-family: 'Poppins', sans-serif;
        font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none;
        background: var(--bg-gray-100); color: var(--text-primary);
    }
    .cancel-btn:hover { background: var(--border-gray); }
    .apply-date-btn {
        padding: 10px 24px; border-radius: 10px; font-family: 'Poppins', sans-serif;
        font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        color: white; box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }
    .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3); }

    /* RESPONSIVE */
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: 1fr; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-range-wrapper { flex-direction: column; }
        .apply-btn { width: 100%; justify-content: center; }
        .status-table th, .status-table td { padding: 12px; font-size: 12px; }
        .date-picker-container { flex-direction: column; max-height: 85vh; overflow-y: auto; width: 95%; }
        .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; padding: 12px 16px; }
        .date-preset { white-space: nowrap; }
        .calendars-wrapper { flex-direction: column; gap: 16px; }
        .date-picker-trigger { max-width: 100%; }
        .cancel-btn, .apply-date-btn { flex: 1; }
        .page-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <div class="page-header-text">
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
                    <svg viewBox="0 0 24 24" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
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
                <div class="skeleton-line" style="width:55%;"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="stat-label">Total Mentions</div>
            <div id="statTotalMentions" class="stat-value">
                <div class="skeleton-line" style="width:55%;"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div class="stat-label">Top Hashtag</div>
            <div id="statTopHashtag" class="stat-value small">
                <div class="skeleton-line" style="width:65%;"></div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-header">
            <div>
                <div class="table-header-title">Trending Hashtags</div>
                <div class="table-header-sub">Diurutkan berdasarkan jumlah mention terbanyak</div>
            </div>
        </div>
        <div class="table-wrapper">

            <!-- Loading Skeleton -->
            <table class="status-table" id="loadingTable">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Hashtag</th>
                        <th class="text-center" style="min-width:220px;">Mention Volume</th>
                        <th class="text-center" style="width:120px;">Count</th>
                        <th class="text-center" style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td colspan="5" style="padding:24px;">
                            <div style="display:flex;align-items:center;gap:20px;">
                                <div class="skeleton-line" style="width:24px;height:14px;margin:0;flex-shrink:0;"></div>
                                <div class="skeleton-line" style="width:{{ 120+$i*50 }}px;height:14px;margin:0;"></div>
                                <div style="flex:1;"></div>
                                <div class="skeleton-line" style="width:120px;height:8px;border-radius:4px;margin:0;"></div>
                                <div class="skeleton-line" style="width:50px;height:14px;margin:0;"></div>
                                <div class="skeleton-line" style="width:60px;height:30px;border-radius:8px;margin:0;flex-shrink:0;"></div>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Real Table -->
            <table class="status-table" id="hashtagTable" style="display:none;">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Hashtag</th>
                        <th class="text-center" style="min-width:220px;">Mention Volume</th>
                        <th class="text-center" style="width:120px;">Count</th>
                        <th class="text-center" style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="hashtagTableBody"></tbody>
            </table>

            <!-- Empty State -->
            <div id="emptyState" style="display:none;">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <h3>No Hashtags Found</h3>
                    <p>Tidak ada data hashtag TikTok untuk periode ini. Coba ubah tanggal filter.</p>
                </div>
            </div>
        </div>

        <div id="paginationWrapper" class="pagination" style="display:none;"></div>
    </div>

    @endif
</div>
@endsection

@section('scripts')
<script>
// ── DATE PICKER ───────────────────────────────────────────────────────────────
(function () {
    'use strict';
    let sd, ed, m1, m2, picking = true;

    document.addEventListener('DOMContentLoaded', () => {
        const sv = document.getElementById('hiddenStartDate');
        const ev = document.getElementById('hiddenEndDate');
        sd = sv && sv.value ? new Date(sv.value + 'T00:00:00') : (() => { const d = new Date(); d.setDate(d.getDate()-6); d.setHours(0,0,0,0); return d; })();
        ed = ev && ev.value ? new Date(ev.value + 'T00:00:00') : (() => { const d = new Date(); d.setHours(0,0,0,0); return d; })();
        m1 = new Date(sd.getFullYear(), sd.getMonth(), 1);
        m2 = new Date(sd.getFullYear(), sd.getMonth()+1, 1);
        render(); bind();
    });

    function bind() {
        document.getElementById('datePickerTrigger').onclick  = openPicker;
        document.querySelector('.date-picker-overlay').onclick = closePicker;
        document.querySelector('.cancel-btn').onclick          = closePicker;
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closePicker(); });
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
        const p = e.target.dataset.preset;
        const t = new Date(); t.setHours(0,0,0,0);
        if      (p==='today')      { sd=new Date(t); ed=new Date(t); }
        else if (p==='yesterday')  { sd=new Date(t); sd.setDate(t.getDate()-1); ed=new Date(sd); }
        else if (p==='last7days')  { ed=new Date(t); sd=new Date(t); sd.setDate(t.getDate()-6); }
        else if (p==='last30days') { ed=new Date(t); sd=new Date(t); sd.setDate(t.getDate()-29); }
        else if (p==='thismonth')  { sd=new Date(t.getFullYear(),t.getMonth(),1); ed=new Date(t); }
        else if (p==='lastmonth')  { sd=new Date(t.getFullYear(),t.getMonth()-1,1); ed=new Date(t.getFullYear(),t.getMonth(),0); }
        if (p!=='custom') { m1=new Date(sd.getFullYear(),sd.getMonth(),1); m2=new Date(sd.getFullYear(),sd.getMonth()+1,1); render(); }
    }

    function applyPicker() {
        document.getElementById('hiddenStartDate').value        = fmt(sd);
        document.getElementById('hiddenEndDate').value          = fmt(ed);
        document.getElementById('dateRangeDisplay').textContent = fmt(sd) + ' to ' + fmt(ed);
        closePicker();
    }

    function render() {
        renderCal('calendar1', m1); renderCal('calendar2', m2);
        document.getElementById('selectedRangeText').textContent = fmt(sd) + ' to ' + fmt(ed);
    }

    function renderCal(id, mon) {
        const el = document.getElementById(id); if (!el) return;
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

    function fmt(d) { if(!d)return ''; return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
    function same(a,b) { return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
})();

// ── HASHTAG APP ───────────────────────────────────────────────────────────────
const HashtagApp = {
    projectId   : '{{ $projectId ?? "" }}',
    startDate   : '{{ $startDate ?? "" }}',
    endDate     : '{{ $endDate ?? "" }}',
    allHashtags : [],
    currentPage : 1,
    perPage     : 20,
    maxVolume   : 0,

    async init() {
        if (!this.projectId) return;
        try { await this.loadData(); }
        catch (err) { console.error('HashtagApp error:', err); this.showError(); }
    },

    async loadData() {
        const url    = '/mk/api/tiktok/trending-topics?project_id=' + this.projectId + '&start_date=' + this.startDate + '&end_date=' + this.endDate;
        const res    = await fetch(url);
        const result = await res.json();
        if (!result.success) throw new Error(result.error || 'API Error');

        const data       = result.data;
        this.allHashtags = data.hashtags || [];
        this.maxVolume   = this.allHashtags.length ? this.allHashtags[0].size : 0;

        const fmt = n => new Intl.NumberFormat('id-ID').format(n);
        document.getElementById('statTotalHashtags').textContent = fmt(data.total_hashtags || 0);
        document.getElementById('statTotalMentions').textContent = fmt(data.total_mentions  || 0);
        const top = data.top_hashtag;
        document.getElementById('statTopHashtag').textContent = top ? '#' + (top.name || '') : '—';

        this.renderTable();
    },

    renderTable() {
        const loadingTable = document.getElementById('loadingTable');
        const hashtagTable = document.getElementById('hashtagTable');
        const emptyState   = document.getElementById('emptyState');

        if (!this.allHashtags || !this.allHashtags.length) {
            loadingTable.style.display = 'none';
            hashtagTable.style.display = 'none';
            emptyState.style.display   = 'block';
            document.getElementById('paginationWrapper').style.display = 'none';
            return;
        }

        const start     = (this.currentPage - 1) * this.perPage;
        const pageItems = this.allHashtags.slice(start, start + this.perPage);

        document.getElementById('hashtagTableBody').innerHTML =
            pageItems.map((h, i) => this.createRow(h, start + i + 1)).join('');

        loadingTable.style.display = 'none';
        hashtagTable.style.display = 'table';
        emptyState.style.display   = 'none';
        this.updatePagination();
    },

    createRow(h, rank) {
        const name  = this.esc(h.name || '');
        const size  = h.size || 0;
        const pct   = this.maxVolume > 0 ? Math.round(size / this.maxVolume * 100) : 0;
        const fmt   = n => new Intl.NumberFormat('id-ID').format(n);
        const link  = 'https://www.tiktok.com/tag/' + encodeURIComponent(name);
        const isTop = rank <= 3;

        return '<tr>' +
            '<td class="rank-cell' + (isTop ? ' rank-top' : '') + '">#' + rank + '</td>' +
            '<td>' +
                '<div class="hashtag-cell">' +
                    '<div class="hashtag-text"><span>#</span>' + name + '</div>' +
                '</div>' +
            '</td>' +
            '<td class="bar-cell">' +
                '<div class="bar-wrap">' +
                    '<div class="bar-track"><div class="bar-fill" style="width:' + pct + '%"></div></div>' +
                    '<span class="bar-count">' + fmt(size) + '</span>' +
                '</div>' +
            '</td>' +
            '<td class="count-cell">' + fmt(size) + '</td>' +
            '<td class="action-cell">' +
                '<a href="' + link + '" target="_blank" rel="noopener" class="btn-view">' +
                    '<svg viewBox="0 0 24 24" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>' +
                    'View' +
                '</a>' +
            '</td>' +
        '</tr>';
    },

    getPageRange(cur, total) {
        if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
        if (cur <= 4)         return [1, 2, 3, 4, 5, '...', total];
        if (cur >= total - 3) return [1, '...', total-4, total-3, total-2, total-1, total];
        return [1, '...', cur-1, cur, cur+1, '...', total];
    },

    updatePagination() {
        const totalPages = Math.ceil(this.allHashtags.length / this.perPage);
        const wrapper    = document.getElementById('paginationWrapper');
        const from       = this.allHashtags.length ? (this.currentPage - 1) * this.perPage + 1 : 0;
        const to         = Math.min(this.currentPage * this.perPage, this.allHashtags.length);

        let html = '<div class="pagination-info">Showing ' + from + '–' + to + ' of ' + this.allHashtags.length + ' hashtags</div>';
        html += '<div style="display:flex;align-items:center;gap:6px;">';
        html += '<button class="page-btn" onclick="HashtagApp.changePage(' + (this.currentPage-1) + ')" ' + (this.currentPage===1?'disabled':'') + '>' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg></button>';

        this.getPageRange(this.currentPage, totalPages).forEach(p => {
            html += p === '...'
                ? '<button class="page-btn" disabled style="cursor:default;">…</button>'
                : '<button class="page-btn ' + (p === this.currentPage ? 'active' : '') + '" onclick="HashtagApp.changePage(' + p + ')">' + p + '</button>';
        });

        html += '<button class="page-btn" onclick="HashtagApp.changePage(' + (this.currentPage+1) + ')" ' + (this.currentPage===totalPages?'disabled':'') + '>' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg></button></div>';

        wrapper.innerHTML       = html;
        wrapper.style.display   = this.allHashtags.length > 0 ? 'flex' : 'none';
    },

    changePage(p) {
        const totalPages = Math.ceil(this.allHashtags.length / this.perPage);
        if (p < 1 || p > totalPages) return;
        this.currentPage = p;
        this.renderTable();
        document.querySelector('.table-container')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    showError() {
        document.getElementById('loadingTable').style.display = 'none';
        document.getElementById('emptyState').style.display   = 'block';
    },

    esc(text) { const d = document.createElement('div'); d.textContent = text; return d.innerHTML; }
};

document.addEventListener('DOMContentLoaded', () => HashtagApp.init());
</script>
@endsection