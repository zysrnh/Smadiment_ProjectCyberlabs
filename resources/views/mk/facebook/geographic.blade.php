@extends('mk.layouts.app')

@section('title', 'Facebook Geographic - SMADIMENT')

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
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    body { background: var(--bg-gray-50); }

    .dashboard-container {
        padding: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }

    .page-header { margin-bottom: 32px; }
    .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
    .page-header p  { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* Filter */
    .filter-card {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-gray);
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
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }
    .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3); }
    .apply-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; }

    /* Date Picker Trigger */
    .date-picker-trigger {
        display: flex; align-items: center; gap: 12px; padding: 12px 20px;
        background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: 12px;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500;
        color: var(--text-primary); cursor: pointer; transition: all 0.2s;
        width: 100%; max-width: 400px;
    }
    .date-picker-trigger:hover { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1); }
    .date-picker-trigger svg:first-child { width: 18px; height: 18px; color: var(--text-secondary); flex-shrink: 0; }
    .date-picker-trigger span { flex: 1; text-align: left; }
    .date-picker-trigger svg:last-child { width: 16px; height: 16px; margin-left: auto; color: var(--text-secondary); }

    /* Date Picker Modal */
    .date-picker-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); }
    .date-picker-modal.show { display: flex; }
    .date-picker-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); cursor: pointer; }
    .date-picker-container { position: relative; background: #fff; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); display: flex; max-width: 900px; width: 90%; max-height: 90vh; z-index: 10001; animation: slideUp 0.3s ease-out; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

    .date-picker-sidebar { width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray); padding: 16px 12px; border-radius: 16px 0 0 16px; display: flex; flex-direction: column; gap: 4px; flex-shrink: 0; }
    .date-preset { padding: 10px 16px; background: transparent; border: none; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500; color: var(--text-primary); text-align: left; cursor: pointer; transition: all 0.2s; }
    .date-preset:hover { background: var(--bg-white); color: var(--primary-green); }
    .date-preset.active { background: var(--primary-green); color: white; }

    .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; overflow: hidden; }
    .date-picker-header { display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px; }
    .nav-btn { width: 36px; height: 36px; border-radius: 8px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
    .nav-btn:hover { background: var(--primary-green); border-color: var(--primary-green); color: white; }
    .nav-btn svg { width: 20px; height: 20px; }

    .calendars-wrapper { display: flex; gap: 24px; flex: 1; min-height: 0; }
    .calendar { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .calendar-month { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-align: center; }
    .calendar-weekdays { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 8px; }
    .weekday { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
    .calendar-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
    .calendar-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 500; border-radius: 8px; cursor: pointer; transition: all 0.2s; color: var(--text-primary); background: transparent; border: none; padding: 0; }
    .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
    .calendar-day.other-month { color: #cbd5e1; cursor: default; }
    .calendar-day.disabled { color: #e2e8f0; cursor: not-allowed; }
    .calendar-day.today { border: 2px solid var(--primary-green); }
    .calendar-day.selected { background: var(--primary-green); color: white; }
    .calendar-day.in-range { background: rgba(3, 128, 71, 0.1); color: var(--primary-green); }
    .calendar-day.range-start, .calendar-day.range-end { background: var(--primary-green); color: white; }

    .date-picker-display { padding: 16px 20px; background: var(--bg-gray-50); border-radius: 12px; text-align: center; margin-bottom: 20px; border: 1px solid var(--border-gray); }
    .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }
    .cancel-btn, .apply-date-btn { padding: 10px 24px; border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
    .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
    .cancel-btn:hover { background: var(--border-gray); }
    .apply-date-btn { background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); color: white; box-shadow: 0 4px 12px rgba(3,128,71,0.2); }
    .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,0.3); }

    /* Alert */
    .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px; }
    .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

    /* Stats */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card { background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 16px; padding: 24px; box-shadow: var(--shadow-sm); transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden; min-height: 120px; display: flex; flex-direction: column; justify-content: center; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); opacity: 0; transition: opacity 0.3s; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-green); }
    .stat-card:hover::before { opacity: 1; }
    .stat-label { font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .stat-value { font-size: 32px; font-weight: 700; color: var(--text-primary); line-height: 1.2; word-break: break-word; }
    .stat-value.stat-value-text { font-size: 20px; }

    /* Cards */
    .do-card { background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; box-shadow: var(--shadow-sm); transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; margin-bottom: 24px; }
    .do-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); opacity: 0; transition: opacity 0.3s; }
    .do-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-green); }
    .do-card:hover::before { opacity: 1; }
    .do-card-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 2px solid var(--bg-gray-50); background: var(--bg-white); flex-shrink: 0; }
    .do-card-head-left { display: flex; align-items: center; gap: 12px; }
    .do-head-icon { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, rgba(3,128,71,0.1) 0%, rgba(3,128,71,0.05) 100%); display: flex; align-items: center; justify-content: center; position: relative; flex-shrink: 0; }
    .do-head-icon svg { width: 28px; height: 28px; color: var(--primary-green); fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .do-card-title { font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Poppins', sans-serif; margin: 0 0 4px 0; }
    .do-card-subtitle { font-size: 13px; color: var(--text-secondary); font-weight: 500; margin: 0; }
    .do-badge { font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 20px; background: var(--bg-gray-100); color: var(--text-secondary); text-transform: uppercase; letter-spacing: .4px; font-family: 'Poppins', sans-serif; }

    /* Skeleton */
    .skeleton-line { height: 28px; background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 8px; margin-bottom: 10px; }
    .skeleton-number-inline { height: 48px; width: 140px; background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 8px; display: inline-block; margin: 0 auto; }
    .skeleton-map-placeholder { height: 500px; background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
    .do-skeleton { padding: 10px 0; }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    .do-card[data-loaded="true"] .do-skeleton,
    .do-card[data-loaded="true"] .do-skeleton-map,
    .do-card[data-loaded="true"] .skeleton-number-inline { display: none; }

    /* Map layout */
    .map-with-panel { display: flex; padding: 0; }
    .map-with-panel .map-area { flex: 1; min-width: 0; position: relative; }
    .location-panel { width: 220px; flex-shrink: 0; border-left: 1px solid var(--border-gray); display: flex; flex-direction: column; background: var(--bg-white); }
    .location-panel-title { padding: 14px 16px 10px; font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--bg-gray-100); font-family: 'Poppins', sans-serif; }
    .location-list { overflow-y: auto; flex: 1; max-height: 500px; }
    .location-list::-webkit-scrollbar { width: 4px; }
    .location-list::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 2px; }
    .location-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; cursor: pointer; border-bottom: 1px solid var(--bg-gray-50); transition: all 0.15s; font-family: 'Poppins', sans-serif; }
    .location-item:hover { background: rgba(3,128,71,0.06); }
    .location-item.active { background: rgba(3,128,71,0.08); border-left: 3px solid var(--primary-green); padding-left: 11px; }
    .location-item-rank { font-size: 10px; font-weight: 700; color: var(--primary-green); width: 18px; flex-shrink: 0; }
    .location-item-info { flex: 1; min-width: 0; }
    .location-item-name { font-size: 12px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .location-item-count { font-size: 11px; color: var(--text-secondary); font-weight: 500; }
    .location-item-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .location-panel-skeleton { padding: 10px 14px; }
    .location-panel-skeleton .skeleton-line { height: 20px; margin-bottom: 8px; }
    .map-area .do-skeleton-map { position: absolute; inset: 0; z-index: 2; }
    .map-area .do-skeleton-map .skeleton-map-placeholder { height: 100%; }

    /* Charts row */
    .charts-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    .do-card-body { padding: 20px 24px 24px; flex: 1; }

    /* Province bars */
    .prov-bar-row { margin-bottom: 12px; }
    .prov-bar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-family: 'Poppins', sans-serif; }
    .prov-bar-name { font-size: 12px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%; }
    .prov-bar-count { font-size: 12px; font-weight: 700; color: var(--text-secondary); }
    .prov-bar-track { height: 8px; background: var(--bg-gray-100); border-radius: 99px; overflow: hidden; }
    .prov-bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--primary-green), var(--primary-green-dark)); transition: width 0.8s cubic-bezier(0.4,0,0.2,1); width: 0; }

    /* Country bars */
    .country-bar-row { margin-bottom: 14px; }
    .country-bar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-family: 'Poppins', sans-serif; }
    .country-bar-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .country-bar-count { font-size: 13px; font-weight: 700; color: var(--text-primary); }
    .country-bar-track { height: 10px; background: var(--bg-gray-100); border-radius: 99px; overflow: hidden; }
    .country-bar-fill { height: 100%; border-radius: 99px; transition: width 0.9s cubic-bezier(0.4,0,0.2,1); width: 0; }

    /* Sentiment legend */
    .senti-legend { display: flex; flex-direction: column; gap: 10px; margin-top: 16px; }
    .senti-legend-item { display: flex; align-items: center; gap: 10px; font-family: 'Poppins', sans-serif; }
    .senti-legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .senti-legend-label { font-size: 12px; font-weight: 600; color: var(--text-primary); flex: 1; }
    .senti-legend-val { font-size: 12px; font-weight: 700; color: var(--text-primary); }
    .senti-legend-pct { font-size: 11px; color: var(--text-secondary); width: 38px; text-align: right; }

    /* Table */
    .do-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-family: 'Poppins', sans-serif; }
    .do-tbl thead tr { background: var(--bg-white); }
    .do-tbl th { padding: 10px 12px; font-size: 10px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .3px; border-bottom: 1px solid var(--border-gray); text-align: left; }
    .do-tbl td { padding: 12px; font-size: 13px; color: var(--text-primary); border-bottom: 1px solid var(--bg-gray-100); }
    .do-tbl tbody tr { transition: all 0.2s; background: var(--bg-white); }
    .do-tbl tbody tr:hover { background: var(--bg-gray-50); }
    .do-tbl tr:last-child td { border-bottom: none; }
    .do-tbl-rank { font-weight: 700; color: var(--primary-green); width: 28px; font-size: 13px; }
    .do-tbl-name { font-weight: 600; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .do-tbl-num { text-align: right; font-weight: 600; font-size: 13px; color: var(--text-primary); }
    .do-body-scroll { overflow-y: auto; }
    .do-empty { font-size: 14px; color: var(--text-secondary); text-align: center; padding: 60px 20px; font-weight: 500; font-family: 'Poppins', sans-serif; }
    .view-all-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--bg-white); color: var(--text-primary); border: 1px solid var(--border-gray); border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s; }
    .view-all-btn:hover { background: var(--bg-gray-50); border-color: var(--primary-green); color: var(--primary-green); }

    /* Map scroll overlay */
    .map-scroll-overlay { position: absolute; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; pointer-events: none; opacity: 0; transition: opacity 0.2s; }
    .map-scroll-overlay.visible { opacity: 1; }
    .map-scroll-hint { display: flex; flex-direction: column; align-items: center; gap: 10px; background: rgba(0,0,0,0.65); backdrop-filter: blur(6px); color: #fff; padding: 20px 32px; border-radius: 16px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; letter-spacing: 0.2px; pointer-events: none; }
    .circle-label { pointer-events: none !important; }
    .circle-label div { display: flex; align-items: center; justify-content: center; height: 100%; }

    /* Responsive */
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 1100px) { .charts-row { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 900px) { .map-with-panel { flex-direction: column; } .location-panel { width: 100%; border-left: none; border-top: 1px solid var(--border-gray); } .location-list { max-height: 200px; } }
    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: 1fr; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .apply-btn { width: 100%; justify-content: center; }
        .date-picker-container { flex-direction: column; max-height: 85vh; overflow-y: auto; width: 95%; }
        .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; padding: 12px 16px; }
        .date-preset { white-space: nowrap; }
        .calendars-wrapper { flex-direction: column; gap: 16px; }
        .cancel-btn, .apply-date-btn { flex: 1; }
    }
    @media (max-width: 700px) { .charts-row { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <h1>Facebook Geographic</h1>
        <p>Monitor geographic distribution and location-based analytics for Facebook</p>
    </div>

    @if(!$projectId)
    <div class="alert alert-warning">
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>No project selected. Please select a project from the sidebar to view geographic data.</span>
    </div>
    @else

    <!-- Filter -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.facebook.geographic') }}">
            <input type="hidden" name="project_id"  value="{{ $projectId }}">
            <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
            <input type="hidden" name="end_date"   id="hiddenEndDate"   value="{{ $endDate }}">

            <div class="filter-content">
                <div class="filter-label">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Date Range
                </div>
                <div class="date-range-wrapper">
                    <button type="button" class="date-picker-trigger" id="datePickerTrigger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
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

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card" data-lazy="stat-geo">
            <div class="stat-label">Total Countries</div>
            <div id="totalCountries" class="stat-value"><div class="skeleton-number-inline"></div></div>
        </div>
        <div class="stat-card" data-lazy="stat-geo">
            <div class="stat-label">Total Users</div>
            <div id="totalUsers" class="stat-value"><div class="skeleton-number-inline"></div></div>
        </div>
        <div class="stat-card" data-lazy="stat-geo">
            <div class="stat-label">Top Country</div>
            <div id="topCountry" class="stat-value stat-value-text"><div class="skeleton-number-inline"></div></div>
        </div>
        <div class="stat-card" data-lazy="stat-geo">
            <div class="stat-label">Top Province</div>
            <div id="topProvince" class="stat-value stat-value-text"><div class="skeleton-number-inline"></div></div>
        </div>
    </div>

    <!-- Map 1: Geographic User Distribution -->
    <div class="do-card" data-lazy="geo-user-map">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <div class="do-head-icon">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/></svg>
                </div>
                <div>
                    <p class="do-card-title">Geographic User Distribution</p>
                    <p class="do-card-subtitle">Facebook users by country and province</p>
                </div>
            </div>
            <span class="do-badge">Facebook Users</span>
        </div>
        <div class="map-with-panel">
            <div class="map-area">
                <div id="geoMap" style="width:100%;height:500px;"></div>
                <div class="do-skeleton-map"><div class="skeleton-map-placeholder"></div></div>
            </div>
            <div class="location-panel" id="geoUserPanel">
                <div class="location-panel-title">📍 Locations</div>
                <div class="location-list" id="geoUserList">
                    <div class="location-panel-skeleton">
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map 2: Sentiment by Location -->
    <div class="do-card" data-lazy="geo-sentiment-map">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <div class="do-head-icon">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </div>
                <div>
                    <p class="do-card-title">Sentiment by Location</p>
                    <p class="do-card-subtitle">Positive, negative, and neutral sentiment distribution</p>
                </div>
            </div>
            <span class="do-badge">Sentiment</span>
        </div>
        <div class="map-with-panel">
            <div class="map-area">
                <div id="geoSentimentMap" style="width:100%;height:500px;"></div>
                <div class="do-skeleton-map"><div class="skeleton-map-placeholder"></div></div>
            </div>
            <div class="location-panel" id="geoSentimentPanel">
                <div class="location-panel-title">📍 Locations</div>
                <div class="location-list" id="geoSentimentList">
                    <div class="location-panel-skeleton">
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 Chart Cards -->
    <div class="charts-row">
        <div class="do-card" data-lazy="chart-countries">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <div class="do-head-icon">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <div>
                        <p class="do-card-title">Top Countries</p>
                        <p class="do-card-subtitle">Users by country</p>
                    </div>
                </div>
                <span class="do-badge">Users</span>
            </div>
            <div class="do-card-body">
                <div class="do-skeleton">
                    <div class="skeleton-line"></div><div class="skeleton-line"></div>
                    <div class="skeleton-line"></div><div class="skeleton-line"></div>
                </div>
                <div id="chartCountries"></div>
            </div>
        </div>

        <div class="do-card" data-lazy="chart-provinces">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <div class="do-head-icon">
                        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div>
                        <p class="do-card-title">Top Provinces</p>
                        <p class="do-card-subtitle" id="provSubtitle">Province breakdown</p>
                    </div>
                </div>
                <span class="do-badge">Detail</span>
            </div>
            <div class="do-card-body">
                <div class="do-skeleton">
                    <div class="skeleton-line"></div><div class="skeleton-line"></div>
                    <div class="skeleton-line"></div><div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                </div>
                <div id="chartProvinces"></div>
            </div>
        </div>

        <div class="do-card" data-lazy="chart-sentiment-donut">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <div class="do-head-icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                    </div>
                    <div>
                        <p class="do-card-title">Sentiment Summary</p>
                        <p class="do-card-subtitle">Overall distribution</p>
                    </div>
                </div>
                <span class="do-badge">Sentiment</span>
            </div>
            <div class="do-card-body" style="display:flex;flex-direction:column;align-items:center;">
                <div class="do-skeleton" style="width:100%;">
                    <div class="skeleton-line"></div><div class="skeleton-line"></div><div class="skeleton-line"></div>
                </div>
                <div id="chartSentimentDonut" style="position:relative;width:180px;height:180px;"></div>
                <div id="chartSentimentLegend" class="senti-legend" style="width:100%;max-width:260px;"></div>
            </div>
        </div>
    </div>

    <!-- Table: Top Author Locations -->
    <div class="do-card" data-lazy="top-locations">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <div class="do-head-icon">
                    <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </div>
                <div>
                    <p class="do-card-title">Top Author Locations</p>
                    <p class="do-card-subtitle">Ranking of locations by author count</p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="do-badge">Rankings</span>
            </div>
        </div>
        <div class="do-card-body do-body-scroll" style="max-height:unset;padding:0 24px 24px;">
            <div class="do-skeleton" style="padding:20px 0;">
                <div class="skeleton-line"></div><div class="skeleton-line"></div>
                <div class="skeleton-line"></div><div class="skeleton-line"></div>
            </div>
            <div id="topLocationsTable"></div>
        </div>
    </div>

    @endif
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ── Date Picker ──────────────────────────────────────────────────────────────
(function () {
    'use strict';
    let selectedStartDate = null, selectedEndDate = null;
    let currentMonth1 = new Date(), currentMonth2 = new Date();
    let selectingStart = true;

    document.addEventListener('DOMContentLoaded', function () {
        const s = document.getElementById('hiddenStartDate');
        const e = document.getElementById('hiddenEndDate');
        selectedStartDate = s && s.value ? new Date(s.value) : (() => { const d = new Date(); d.setDate(d.getDate() - 6); return d; })();
        selectedEndDate   = e && e.value ? new Date(e.value) : new Date();
        currentMonth1 = new Date(selectedStartDate);
        currentMonth2 = new Date(selectedStartDate);
        currentMonth2.setMonth(currentMonth2.getMonth() + 1);
        renderCalendars();
        setupEventListeners();
    });

    function setupEventListeners() {
        document.getElementById('datePickerTrigger')?.addEventListener('click', openDatePicker);
        document.querySelector('.date-picker-overlay')?.addEventListener('click', closeDatePicker);
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && document.getElementById('datePickerModal')?.classList.contains('show')) closeDatePicker(); });
        document.querySelectorAll('.date-preset').forEach(btn => btn.addEventListener('click', handlePresetClick));
        document.getElementById('prevMonth')?.addEventListener('click', () => { currentMonth1.setMonth(currentMonth1.getMonth()-1); currentMonth2.setMonth(currentMonth2.getMonth()-1); renderCalendars(); });
        document.getElementById('nextMonth')?.addEventListener('click', () => { currentMonth1.setMonth(currentMonth1.getMonth()+1); currentMonth2.setMonth(currentMonth2.getMonth()+1); renderCalendars(); });
        document.getElementById('applyDatePicker')?.addEventListener('click', applyDateSelection);
        document.querySelector('.cancel-btn')?.addEventListener('click', closeDatePicker);
    }

    function openDatePicker()  { document.getElementById('datePickerModal').classList.add('show'); renderCalendars(); }
    function closeDatePicker() { document.getElementById('datePickerModal').classList.remove('show'); }

    function handlePresetClick(e) {
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        const today = new Date(); today.setHours(0,0,0,0);
        switch(e.target.dataset.preset) {
            case 'today':      selectedStartDate = new Date(today); selectedEndDate = new Date(today); break;
            case 'yesterday':  selectedStartDate = new Date(today); selectedStartDate.setDate(today.getDate()-1); selectedEndDate = new Date(selectedStartDate); break;
            case 'last7days':  selectedEndDate = new Date(today); selectedStartDate = new Date(today); selectedStartDate.setDate(today.getDate()-6); break;
            case 'last30days': selectedEndDate = new Date(today); selectedStartDate = new Date(today); selectedStartDate.setDate(today.getDate()-29); break;
            case 'thismonth':  selectedStartDate = new Date(today.getFullYear(), today.getMonth(), 1); selectedEndDate = new Date(today); break;
            case 'lastmonth':  selectedStartDate = new Date(today.getFullYear(), today.getMonth()-1, 1); selectedEndDate = new Date(today.getFullYear(), today.getMonth(), 0); break;
        }
        if (e.target.dataset.preset !== 'custom') {
            currentMonth1 = new Date(selectedStartDate);
            currentMonth2 = new Date(selectedStartDate);
            currentMonth2.setMonth(currentMonth2.getMonth()+1);
            updateDateDisplay(); renderCalendars();
        }
    }

    function applyDateSelection() {
        const start = formatDate(selectedStartDate), end = formatDate(selectedEndDate);
        document.getElementById('hiddenStartDate').value = start;
        document.getElementById('hiddenEndDate').value   = end;
        const d = document.getElementById('dateRangeDisplay');
        if (d) d.textContent = `${start} to ${end}`;
        closeDatePicker();
    }

    function renderCalendars() { renderCalendar('calendar1', currentMonth1); renderCalendar('calendar2', currentMonth2); updateDateDisplay(); }

    function renderCalendar(id, month) {
        const calendar = document.getElementById(id); if (!calendar) return;
        const year = month.getFullYear(), monthNum = month.getMonth();
        const firstDay = new Date(year, monthNum, 1), lastDay = new Date(year, monthNum+1, 0);
        const prevLastDay = new Date(year, monthNum, 0);
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const weekdays   = ['Su','Mo','Tu','We','Th','Fr','Sa'];
        const today = new Date(); today.setHours(0,0,0,0);
        let html = `<div class="calendar-month">${monthNames[monthNum]} ${year}</div><div class="calendar-weekdays">${weekdays.map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
        for (let i = 0; i < firstDay.getDay(); i++) html += `<button type="button" class="calendar-day other-month" disabled>${prevLastDay.getDate()-(firstDay.getDay()-1-i)}</button>`;
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const date = new Date(year, monthNum, day); date.setHours(0,0,0,0);
            const dateStr = formatDate(date);
            let classes = 'calendar-day';
            if (isSameDay(date, today)) classes += ' today';
            if (date > today) classes += ' disabled';
            if (selectedStartDate && selectedEndDate) {
                if (isSameDay(date, selectedStartDate)) classes += ' selected range-start';
                else if (isSameDay(date, selectedEndDate)) classes += ' selected range-end';
                else if (date > selectedStartDate && date < selectedEndDate) classes += ' in-range';
            }
            html += `<button type="button" class="${classes}" data-date="${dateStr}" ${date > today ? 'disabled' : ''}>${day}</button>`;
        }
        const rem = lastDay.getDay() === 6 ? 0 : 6 - lastDay.getDay();
        for (let i = 1; i <= rem; i++) html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
        html += '</div>';
        calendar.innerHTML = html;
        calendar.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => btn.addEventListener('click', handleDateClick));
    }

    function handleDateClick(e) {
        const date = new Date(e.target.dataset.date); date.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        document.querySelector('[data-preset="custom"]')?.classList.add('active');
        if (selectingStart || date < selectedStartDate) { selectedStartDate = date; selectedEndDate = date; selectingStart = false; }
        else { selectedEndDate = date >= selectedStartDate ? date : selectedStartDate; if (date < selectedStartDate) { selectedEndDate = selectedStartDate; selectedStartDate = date; } selectingStart = true; }
        updateDateDisplay(); renderCalendars();
    }

    function updateDateDisplay() {
        if (!selectedStartDate || !selectedEndDate) return;
        const el = document.getElementById('selectedRangeText');
        if (el) el.textContent = `${formatDate(selectedStartDate)} to ${formatDate(selectedEndDate)}`;
    }

    function formatDate(date) {
        if (!date) return '';
        return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
    }

    function isSameDay(a, b) {
        return a && b && a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();
    }
})();

// ── Facebook Geographic Loader ───────────────────────────────────────────────
const FBGeoLoader = {
    projectId: '{{ $projectId ?? "" }}',
    startDate:  '{{ $startDate ?? "" }}',
    endDate:    '{{ $endDate   ?? "" }}',
    loadedSections: new Set(),
    _geoUserCache: null,
    _allLocations: [],

    init() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target, section = el.dataset.lazy;
                if (this.loadedSections.has(section)) return;
                this.loadedSections.add(section);
                observer.unobserve(el);
                this.loadSection(section, el);
            });
        }, { root: null, rootMargin: '100px', threshold: 0.1 });

        document.querySelectorAll('[data-lazy]').forEach(el => observer.observe(el));
    },

    async loadSection(section, el) {
        try {
            switch (section) {
                case 'stat-geo':              await this.loadStats(el);              break;
                case 'geo-user-map':          await this.loadGeoUserMap(el);         break;
                case 'geo-sentiment-map':     await this.loadGeoSentimentMap(el);    break;
                case 'top-locations':         await this.loadTopLocations(el);       break;
                case 'chart-countries':       await this.loadChartCountries(el);     break;
                case 'chart-provinces':       await this.loadChartProvinces(el);     break;
                case 'chart-sentiment-donut': await this.loadChartSentimentDonut(el);break;
            }
            el.dataset.loaded = 'true';
        } catch (err) {
            console.error(`❌ Failed to load ${section}:`, err);
        }
    },

    // ── fetch helpers ────────────────────────────────────────────────────
    async fetchGeoUser() {
        if (this._geoUserCache) return this._geoUserCache;
        const res  = await fetch(`/mk/api/facebook/geo-user?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const json = await res.json();
        console.log('📍 FB geoUser raw:', json);
        this._geoUserCache = json;
        return json;
    },

    parseGeoRows(result) {
        if (!result || !result.success) return [];
        const d = result.data;
        if (!d) return [];
        if (d.country && Array.isArray(d.country.rows)) return d.country.rows;
        if (Array.isArray(d.rows)) return d.rows;
        if (Array.isArray(d)) return d;
        if (typeof d === 'object') {
            const entries = Object.entries(d);
            if (entries.length && typeof entries[0][1] === 'number')
                return entries.map(([name, count]) => ({ name, count }));
        }
        return [];
    },

    parseGeoTotal(result) {
        if (!result || !result.success || !result.data) return 0;
        const d = result.data;
        if (d.country && d.country.total) return d.country.total;
        if (d.total) return d.total;
        return 0;
    },

    // ── Stats ────────────────────────────────────────────────────────────
    async loadStats(el) {
        const result = await this.fetchGeoUser();
        const rows   = this.parseGeoRows(result);
        const total  = this.parseGeoTotal(result);

        document.getElementById('totalCountries').textContent = rows.length;
        document.getElementById('totalUsers').textContent     = total.toLocaleString();

        const topRow = rows[0];
        if (topRow) {
            document.getElementById('topCountry').textContent = topRow.name || 'N/A';
            const provinces = Object.entries(topRow.detail || {}).sort((a, b) => b[1] - a[1]);
            document.getElementById('topProvince').textContent = provinces.length ? provinces[0][0] : 'N/A';
        } else {
            document.getElementById('topCountry').textContent  = 'N/A';
            document.getElementById('topProvince').textContent = 'N/A';
        }

        document.querySelectorAll('[data-lazy="stat-geo"]').forEach(c => c.dataset.loaded = 'true');
    },

    // ── Geo User Map ─────────────────────────────────────────────────────
    async loadGeoUserMap(card) {
        const result  = await this.fetchGeoUser();
        const rows    = this.parseGeoRows(result);
        const markers = this.renderMap('geoMap', rows, (p) => {
            const count = parseInt(p.count || 0), name = p.name || 'Unknown';
            return {
                color: '#038047', count,
                popup: `<div style="font-family:Poppins;text-align:center;padding:8px;">
                    <div style="font-weight:700;font-size:15px;color:#1a202c;margin-bottom:6px;">${name}</div>
                    <div style="font-size:24px;font-weight:800;color:#038047;margin-bottom:2px;">${count.toLocaleString()}</div>
                    <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.8px;font-weight:600;">users</div>
                </div>`
            };
        });
        this.buildLocationPanel('geoUserList', rows, markers, '#038047');
    },

    // ── Geo Sentiment Map ─────────────────────────────────────────────────
    async loadGeoSentimentMap(card) {
        const res    = await fetch(`/mk/api/facebook/geo-sentiment?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();
        console.log('📍 FB geoSentiment raw:', result);
        const rows    = this.parseGeoRows(result);
        const markers = this.renderMap('geoSentimentMap', rows, (location) => {
            const count = parseInt(location.count || 0);
            const pos   = parseInt(location.pos   || 0);
            const neg   = parseInt(location.neg   || 0);
            const net   = parseInt(location.net   || 0);
            const name  = location.name || 'Unknown';
            const safe  = count || 1;
            let color = '#64748b', sentiment = 'Neutral';
            if (pos > neg && pos > net) { color = '#22c55e'; sentiment = 'Positive'; }
            else if (neg > pos && neg > net) { color = '#ef4444'; sentiment = 'Negative'; }
            return {
                color, count, sentiment,
                popup: `<div style="font-family:Poppins;text-align:center;padding:8px;">
                    <div style="font-weight:700;font-size:15px;color:#1a202c;margin-bottom:6px;">${name}</div>
                    <div style="display:inline-block;padding:4px 12px;background:${color}20;border-radius:12px;margin-bottom:8px;">
                        <span style="font-size:10px;font-weight:700;color:${color};text-transform:uppercase;">${sentiment}</span>
                    </div>
                    <div style="font-size:24px;font-weight:800;color:${color};margin-bottom:2px;">${count.toLocaleString()}</div>
                    <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.8px;font-weight:600;">mentions</div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:12px;border-top:1px solid #e2e8f0;padding-top:12px;">
                        <div style="text-align:center;padding:6px;background:#f0fdf4;border-radius:6px;"><div style="font-size:16px;font-weight:700;color:#22c55e;">${pos}</div><div style="font-size:9px;color:#64748b;text-transform:uppercase;font-weight:600;">Positive</div><div style="font-size:8px;color:#64748b;">${((pos/safe)*100).toFixed(1)}%</div></div>
                        <div style="text-align:center;padding:6px;background:#f8fafc;border-radius:6px;"><div style="font-size:16px;font-weight:700;color:#64748b;">${net}</div><div style="font-size:9px;color:#64748b;text-transform:uppercase;font-weight:600;">Neutral</div><div style="font-size:8px;color:#64748b;">${((net/safe)*100).toFixed(1)}%</div></div>
                        <div style="text-align:center;padding:6px;background:#fef2f2;border-radius:6px;"><div style="font-size:16px;font-weight:700;color:#ef4444;">${neg}</div><div style="font-size:9px;color:#64748b;text-transform:uppercase;font-weight:600;">Negative</div><div style="font-size:8px;color:#64748b;">${((neg/safe)*100).toFixed(1)}%</div></div>
                    </div>
                </div>`
            };
        });
        this.buildLocationPanel('geoSentimentList', rows, markers, null, true);
    },

    // ── Shared renderMap ──────────────────────────────────────────────────
    renderMap(elementId, rows, getMarkerProps) {
        const map = L.map(elementId, { center: [-2.5, 118], zoom: 5, scrollWheelZoom: false });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
            crossOrigin: true
        }).addTo(map);

        const mapEl  = document.getElementById(elementId);
        const overlay = document.createElement('div');
        overlay.className = 'map-scroll-overlay';
        mapEl.style.position = 'relative';
        mapEl.appendChild(overlay);

        mapEl.addEventListener('wheel', function (e) {
            if (!e.ctrlKey) { overlay.classList.add('visible'); clearTimeout(overlay._hideTimer); overlay._hideTimer = setTimeout(() => overlay.classList.remove('visible'), 1800); }
            else { map.scrollWheelZoom.enable(); overlay.classList.remove('visible'); }
        });
        map.on('zoomend', () => setTimeout(() => map.scrollWheelZoom.disable(), 300));

        if (!rows.length) return { map, markerRefs: [] };

        const maxCount   = Math.max(...rows.map(p => parseInt(p.count || 0)));
        const markerRefs = [];

        rows.forEach(p => {
            const lat = parseFloat(p.latitude  || 0);
            const lng = parseFloat(p.longitude || 0);
            if (lat === 0 && lng === 0) { markerRefs.push(null); return; }

            const { color, count, popup } = getMarkerProps(p);

            if (count >= 10) {
                let radius = Math.sqrt(count) * 2500;
                radius = Math.max(radius, 5000); radius = Math.min(radius, 50000);
                const opacity = Math.min(0.15 + (count / maxCount) * 0.45, 0.6);
                L.circle([lat, lng], { radius, fillColor: color, color, weight: 1, opacity: 0.3, fillOpacity: opacity }).addTo(map);
            }

            const pin = L.marker([lat, lng], {
                icon: L.divIcon({ className: '', html: `<div style="width:13px;height:13px;background:${color};border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>`, iconSize: [13,13], iconAnchor: [6.5,6.5] })
            }).addTo(map).bindPopup(popup);

            markerRefs.push({ marker: pin, lat, lng });

            const label    = count > 999 ? (count/1000).toFixed(1)+'k' : String(count);
            const fontSize = count >= 1000 ? '13px' : '11px';
            L.marker([lat, lng], {
                icon: L.divIcon({ className: 'circle-label', html: `<div style="font-family:Poppins;font-size:${fontSize};font-weight:900;color:#fff;background:${color};padding:3px 8px;border-radius:12px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4);white-space:nowrap;">${label}</div>`, iconSize: [40,20], iconAnchor: [20,25] }),
                interactive: false
            }).addTo(map);
        });

        return { map, markerRefs };
    },

    // ── Location Panel ────────────────────────────────────────────────────
    buildLocationPanel(listId, rows, mapResult, defaultColor, useSentimentColor) {
        const listEl = document.getElementById(listId);
        if (!listEl) return;
        const { map, markerRefs } = mapResult;
        const validRows = rows.filter(p => !(parseFloat(p.latitude||0) === 0 && parseFloat(p.longitude||0) === 0));

        if (!validRows.length) { listEl.innerHTML = '<div class="do-empty" style="padding:24px 14px;font-size:12px;">No location data</div>'; return; }

        const sorted = [...validRows].sort((a, b) => parseInt(b.count||0) - parseInt(a.count||0));
        let html = '';
        sorted.forEach((p, rank) => {
            const name = p.name || 'Unknown', count = parseInt(p.count || 0);
            let color = defaultColor || '#038047';
            if (useSentimentColor) {
                const pos = parseInt(p.pos||0), neg = parseInt(p.neg||0), net = parseInt(p.net||0);
                if (pos > neg && pos > net) color = '#22c55e';
                else if (neg > pos && neg > net) color = '#ef4444';
                else color = '#64748b';
            }
            const label = count > 999 ? (count/1000).toFixed(1)+'k' : count;
            html += `<div class="location-item" data-index="${rank}" data-name="${name}"><span class="location-item-rank">${rank+1}</span><div class="location-item-info"><div class="location-item-name" title="${name}">${name}</div><div class="location-item-count">${label} ${useSentimentColor?'mentions':'users'}</div></div><div class="location-item-dot" style="background:${color};"></div></div>`;
        });
        listEl.innerHTML = html;

        listEl.querySelectorAll('.location-item').forEach(item => {
            item.addEventListener('click', () => {
                const name = item.dataset.name;
                const targetRow = validRows.find(p => (p.name||'Unknown') === name);
                if (!targetRow) return;
                const lat = parseFloat(targetRow.latitude||0), lng = parseFloat(targetRow.longitude||0);
                if (lat===0 && lng===0) return;
                map.flyTo([lat, lng], 8, { animate: true, duration: 1 });
                const ref = markerRefs.find(r => r && Math.abs(r.lat-lat) < 0.001 && Math.abs(r.lng-lng) < 0.001);
                if (ref) setTimeout(() => ref.marker.openPopup(), 800);
                listEl.querySelectorAll('.location-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });
    },

    // ── Top Locations Table ───────────────────────────────────────────────
    async loadTopLocations(card) {
        const res    = await fetch(`/mk/api/facebook/top-locations?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();
        console.log('📍 FB topLocations raw:', result);
        let locations = [];

        if (result.success && Array.isArray(result.data)) {
            locations = result.data
                .filter(l => { const n = (l.name || l.location || '').trim(); return n && n !== 'Unknown' && !n.startsWith('\u0000'); })
                .map(l => ({ name: l.name || l.location || 'Unknown', count: parseInt(l.count || l.total || 0) }));
        }

        if (!locations.length) {
            const geo  = await this.fetchGeoUser();
            const rows = this.parseGeoRows(geo);
            rows.forEach(country => {
                const cName = (country.name || '').trim();
                if (cName && cName !== 'Unknown') locations.push({ name: cName, count: parseInt(country.count || 0) });
                if (country.detail && typeof country.detail === 'object') {
                    Object.entries(country.detail)
                        .filter(([k]) => k && !k.startsWith('\u0000') && k.trim())
                        .forEach(([name, val]) => {
                            const count = typeof val === 'number' ? val : parseInt(val?.count || 0);
                            if (count > 0) locations.push({ name: name.trim(), count });
                        });
                }
            });
            locations.sort((a, b) => b.count - a.count);
        }

        const tableEl = document.getElementById('topLocationsTable');
        if (!locations.length) { tableEl.innerHTML = '<div class="do-empty">No data available</div>'; return; }

        this._allLocations = locations;

        if (locations.length > 10) {
            const btnWrapper = card.querySelector('.do-card-head > div:last-child');
            if (btnWrapper && !btnWrapper.querySelector('.view-all-btn')) {
                btnWrapper.innerHTML += `<button class="view-all-btn" onclick="FBGeoLoader.openModal()">View All <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;"><path d="M9 18l6-6-6-6"/></svg></button>`;
            }
        }

        tableEl.innerHTML = this._buildTable(locations.slice(0, 10), 0);
    },

    _buildTable(items, offset) {
        let html = `<table class="do-tbl" style="margin-top:8px;"><thead><tr><th style="width:40px;">#</th><th>Location</th><th style="text-align:right;">Authors</th></tr></thead><tbody>`;
        items.forEach((loc, i) => {
            html += `<tr><td class="do-tbl-rank">${offset+i+1}</td><td class="do-tbl-name">${loc.name}</td><td class="do-tbl-num">${loc.count.toLocaleString()}</td></tr>`;
        });
        html += '</tbody></table>';
        return html;
    },

    // ── Chart: Top Countries ──────────────────────────────────────────────
    async loadChartCountries(card) {
        const result = await this.fetchGeoUser();
        const rows   = this.parseGeoRows(result);
        const el     = document.getElementById('chartCountries');
        if (!el || !rows.length) return;
        const top    = rows.slice(0, 6);
        const max    = parseInt(top[0]?.count) || 1;
        const colors = ['#038047','#059669','#0891b2','#7c3aed','#db2777','#ea580c'];
        el.innerHTML = top.map((row, i) => {
            const count  = parseInt(row.count);
            const logPct = Math.round((Math.log(count+1)/Math.log(max+1))*100);
            const pct    = Math.max(logPct, 6);
            const color  = colors[i % colors.length];
            return `<div class="country-bar-row"><div class="country-bar-header"><span class="country-bar-name">${row.name}</span><span class="country-bar-count">${count.toLocaleString()}</span></div><div class="country-bar-track"><div class="country-bar-fill" data-pct="${pct}" style="background:${color};"></div></div></div>`;
        }).join('');
        requestAnimationFrame(() => el.querySelectorAll('.country-bar-fill').forEach(bar => bar.style.width = bar.dataset.pct + '%'));
    },

    // ── Chart: Top Provinces ──────────────────────────────────────────────
    async loadChartProvinces(card) {
        const result = await this.fetchGeoUser();
        const rows   = this.parseGeoRows(result);
        const el     = document.getElementById('chartProvinces');
        if (!el || !rows.length) return;
        const topCountry = rows[0];
        if (!topCountry?.detail) { el.innerHTML = '<div class="do-empty" style="padding:32px 0;">No province data</div>'; return; }
        document.getElementById('provSubtitle').textContent = topCountry.name + ' provinces';
        const provinces = Object.entries(topCountry.detail)
            .filter(([k]) => k && !k.startsWith('\u0000') && k.trim().length > 0)
            .map(([name, count]) => ({ name, count: parseInt(count) }))
            .sort((a, b) => b.count - a.count).slice(0, 8);
        const max = provinces[0]?.count || 1;
        el.innerHTML = provinces.map(p => {
            const pct = Math.round((p.count/max)*100);
            return `<div class="prov-bar-row"><div class="prov-bar-header"><span class="prov-bar-name">${p.name}</span><span class="prov-bar-count">${p.count.toLocaleString()}</span></div><div class="prov-bar-track"><div class="prov-bar-fill" data-pct="${pct}"></div></div></div>`;
        }).join('');
        requestAnimationFrame(() => el.querySelectorAll('.prov-bar-fill').forEach(bar => bar.style.width = bar.dataset.pct + '%'));
    },

    // ── Chart: Sentiment Donut ────────────────────────────────────────────
    async loadChartSentimentDonut(card) {
        const res    = await fetch(`/mk/api/facebook/geo-sentiment?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();
        const rows   = this.parseGeoRows(result);
        let totalPos = 0, totalNeg = 0, totalNet = 0;
        rows.forEach(r => { totalPos += parseInt(r.pos||0); totalNeg += parseInt(r.neg||0); totalNet += parseInt(r.net||0); });
        const total    = totalPos + totalNeg + totalNet || 1;
        const canvasEl = document.getElementById('chartSentimentDonut');
        const legendEl = document.getElementById('chartSentimentLegend');
        if (!canvasEl) return;
        const canvas = document.createElement('canvas'); canvas.width = 180; canvas.height = 180;
        canvasEl.appendChild(canvas);
        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: { labels: ['Positive','Neutral','Negative'], datasets: [{ data: [totalPos, totalNet, totalNeg], backgroundColor: ['#22c55e','#94a3b8','#ef4444'], borderColor: '#fff', borderWidth: 3, hoverOffset: 6 }] },
            options: { responsive: false, cutout: '62%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString()} (${((ctx.parsed/total)*100).toFixed(1)}%)` } } }, animation: { animateRotate: true, duration: 900 } }
        });
        if (legendEl) {
            legendEl.innerHTML = [
                { label: 'Positive', val: totalPos, color: '#22c55e' },
                { label: 'Neutral',  val: totalNet,  color: '#94a3b8' },
                { label: 'Negative', val: totalNeg, color: '#ef4444' },
            ].map(item => `<div class="senti-legend-item"><div class="senti-legend-dot" style="background:${item.color};"></div><span class="senti-legend-label">${item.label}</span><span class="senti-legend-val">${item.val.toLocaleString()}</span><span class="senti-legend-pct">${((item.val/total)*100).toFixed(1)}%</span></div>`).join('');
        }
    },

    openModal() {
        if (!document.getElementById('geoLocModal')) {
            const m = document.createElement('div');
            m.id = 'geoLocModal';
            m.style.cssText = 'display:none;position:fixed;z-index:9999;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(8px);align-items:center;justify-content:center;';
            m.innerHTML = `<div style="background:#fff;border-radius:16px;width:90%;max-width:560px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 50px rgba(0,0,0,0.4);animation:modalIn .25s ease-out;"><div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:2px solid #f8fafc;"><h3 style="font-size:18px;font-weight:700;color:#1a202c;font-family:Poppins,sans-serif;margin:0;">All Author Locations</h3><button onclick="FBGeoLoader.closeModal()" style="width:34px;height:34px;border-radius:8px;background:#f8fafc;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;" onmouseover="this.style.background='#ef4444';this.style.color='#fff'" onmouseout="this.style.background='#f8fafc';this.style.color='#64748b'"><svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;stroke-width:2.5;fill:none;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div><div id="geoLocModalBody" style="padding:16px 24px 24px;overflow-y:auto;flex:1;"></div></div>`;
            m.addEventListener('click', e => { if (e.target === m) this.closeModal(); });
            document.body.appendChild(m);
            if (!document.getElementById('geoModalStyle')) {
                const s = document.createElement('style'); s.id = 'geoModalStyle';
                s.textContent = '@keyframes modalIn{from{transform:translateY(-16px) scale(0.96);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}';
                document.head.appendChild(s);
            }
        }
        document.getElementById('geoLocModalBody').innerHTML = this._buildTable(this._allLocations, 0);
        document.getElementById('geoLocModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', this._escHandler = e => { if (e.key === 'Escape') this.closeModal(); });
    },

    closeModal() {
        const modal = document.getElementById('geoLocModal');
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = '';
        if (this._escHandler) document.removeEventListener('keydown', this._escHandler);
    }
};

document.addEventListener('DOMContentLoaded', () => FBGeoLoader.init());
</script>
@endsection