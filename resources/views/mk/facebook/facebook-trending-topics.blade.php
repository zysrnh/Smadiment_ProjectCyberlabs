@extends('mk.layouts.app')

@section('title', 'Facebook Trending Topics - SMADIMENT')

@section('styles')
<style>
    :root {
        --fb-blue: #1877F2;
        --fb-blue-dark: #1558b0;
        --fb-blue-light: #e7f0fd;
        --text-primary: #1a202c;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --bg-white: #ffffff;
        --bg-gray-50: #f8fafc;
        --bg-gray-100: #f1f5f9;
        --border-gray: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
    }

    body { background: var(--bg-gray-50); }

    .dashboard-container { padding: 24px; max-width: 1600px; margin: 0 auto; }

    .page-header { margin-bottom: 32px; display: flex; align-items: center; gap: 16px; }
    .page-header-icon {
        width: 48px; height: 48px;
        background: linear-gradient(135deg, var(--fb-blue) 0%, var(--fb-blue-dark) 100%);
        border-radius: 12px; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 12px rgba(24,119,242,0.3); flex-shrink: 0;
    }
    .page-header-icon svg { width: 26px; height: 26px; fill: white; }
    .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0; }
    .page-header p { font-size: 14px; color: var(--text-secondary); margin: 0; }

    .filter-card { background: var(--bg-white); border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); }
    .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .filter-label { font-size: 14px; font-weight: 600; color: var(--text-primary); white-space: nowrap; }
    .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }

    .apply-btn {
        padding: 12px 28px;
        background: linear-gradient(135deg, var(--fb-blue) 0%, var(--fb-blue-dark) 100%);
        color: white; border: none; border-radius: 12px;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: all 0.3s;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 12px rgba(24,119,242,0.2);
    }
    .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(24,119,242,0.35); }
    .apply-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: var(--bg-white); border: 1px solid var(--border-gray);
        border-radius: 12px; padding: 20px; box-shadow: var(--shadow-sm);
        transition: all 0.3s; position: relative; overflow: hidden;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--fb-blue) 0%, var(--fb-blue-dark) 100%);
        opacity: 0; transition: opacity 0.3s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--fb-blue); }
    .stat-card:hover::before { opacity: 1; }
    .stat-label { font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; }
    .stat-value { font-size: 28px; font-weight: 700; color: var(--text-primary); line-height: 1.2; }

    .table-container { background: var(--bg-white); border-radius: 12px; border: 1px solid var(--border-gray); box-shadow: var(--shadow-sm); overflow: hidden; }
    .table-wrapper { overflow-x: auto; }

    .status-table { width: 100%; border-collapse: collapse; font-family: 'Poppins', sans-serif; }
    .status-table thead { background: var(--bg-white); }
    .status-table th {
        padding: 16px 24px; text-align: left; font-size: 11px; font-weight: 700;
        color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.8px;
        border-bottom: 2px solid var(--bg-gray-100); white-space: nowrap;
    }
    .status-table th.text-center { text-align: center; }
    .status-table tbody tr { border-bottom: 1px solid var(--bg-gray-100); transition: background 0.15s; }
    .status-table tbody tr:hover { background: var(--bg-gray-50); }
    .status-table tbody tr:last-child { border-bottom: none; }
    .status-table td { padding: 16px 24px; font-size: 14px; color: var(--text-primary); vertical-align: middle; }

    .rank-cell { font-weight: 600; color: var(--text-secondary); width: 60px; }
    .topic-cell { display: flex; align-items: center; gap: 10px; font-weight: 600; }
    .hashtag-badge {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px;
        background: var(--fb-blue-light); color: var(--fb-blue);
        border-radius: 8px; font-size: 14px; font-weight: 700; flex-shrink: 0;
    }

    .volume-cell { min-width: 200px; }
    .volume-bar-wrapper { display: flex; align-items: center; gap: 10px; }
    .volume-bar-track { flex: 1; height: 8px; background: var(--bg-gray-100); border-radius: 4px; overflow: hidden; }
    .volume-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--fb-blue) 0%, var(--fb-blue-dark) 100%);
        border-radius: 4px; transition: width 0.8s ease;
    }
    .volume-count { font-size: 12px; font-weight: 600; color: var(--text-secondary); white-space: nowrap; min-width: 40px; text-align: right; }
    .appearances-cell { font-weight: 500; color: var(--text-secondary); font-size: 14px; text-align: center; }
    .action-cell { text-align: center; width: 100px; }

    .btn-view {
        padding: 6px 14px; background: transparent; border: 1px solid var(--border-gray);
        border-radius: 6px; font-size: 12px; font-weight: 600; color: var(--text-primary);
        cursor: pointer; transition: all 0.2s; text-decoration: none;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .btn-view:hover { background: var(--fb-blue); color: white; border-color: var(--fb-blue); }
    .btn-view svg { width: 12px; height: 12px; stroke: currentColor; fill: none; }

    .pagination {
        display: flex; justify-content: space-between; align-items: center;
        gap: 8px; padding: 20px 24px; background: var(--bg-white);
        border-top: 1px solid var(--border-gray); flex-wrap: wrap;
    }
    .page-btn {
        width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--border-gray);
        background: var(--bg-white); color: var(--text-primary); font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;
        font-family: 'Poppins', sans-serif;
    }
    .page-btn:hover:not(:disabled) { border-color: var(--fb-blue); color: var(--fb-blue); background: var(--fb-blue-light); }
    .page-btn.active { background: var(--fb-blue); color: white; border-color: var(--fb-blue); }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .pagination-info { font-size: 13px; color: var(--text-secondary); font-weight: 500; }

    .skeleton-line {
        height: 16px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 8px;
    }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    .empty-state { text-align: center; padding: 80px 20px; }
    .empty-state svg { width: 64px; height: 64px; color: var(--text-secondary); margin-bottom: 16px; stroke: currentColor; fill: none; }
    .empty-state h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
    .empty-state p { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* Date Picker */
    .date-picker-trigger {
        display: flex; align-items: center; gap: 12px; padding: 12px 20px;
        background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: 12px;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500;
        color: var(--text-primary); cursor: pointer; transition: all 0.2s;
        width: 100%; max-width: 400px;
    }
    .date-picker-trigger:hover { border-color: var(--fb-blue); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(24,119,242,0.1); }
    .date-picker-trigger svg:first-child { width: 18px; height: 18px; flex-shrink: 0; }
    .date-picker-trigger span { flex: 1; text-align: left; }
    .date-picker-trigger svg:last-child { width: 16px; height: 16px; margin-left: auto; }

    .date-picker-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); }
    .date-picker-modal.show { display: flex; }
    .date-picker-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; cursor: pointer; }
    .date-picker-container { position: relative; background: #fff; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); display: flex; max-width: 900px; width: 90%; max-height: 90vh; z-index: 10001; animation: slideUp 0.3s ease-out; }
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .date-picker-sidebar { width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray); padding: 16px 12px; border-radius: 16px 0 0 16px; display: flex; flex-direction: column; gap: 4px; flex-shrink: 0; }
    .date-preset { padding: 10px 16px; background: transparent; border: none; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500; color: var(--text-primary); text-align: left; cursor: pointer; transition: all 0.2s; }
    .date-preset:hover { background: var(--bg-white); color: var(--fb-blue); }
    .date-preset.active { background: var(--fb-blue); color: white; }
    .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; overflow: hidden; }
    .date-picker-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
    .nav-btn { width: 36px; height: 36px; border-radius: 8px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
    .nav-btn:hover { background: var(--fb-blue); border-color: var(--fb-blue); color: white; }
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
    .calendar-day.today { border: 2px solid var(--fb-blue); }
    .calendar-day.selected { background: var(--fb-blue); color: white; }
    .calendar-day.in-range { background: rgba(24,119,242,0.1); color: var(--fb-blue); }
    .calendar-day.range-start, .calendar-day.range-end { background: var(--fb-blue); color: white; }
    .date-picker-display { padding: 16px 20px; background: var(--bg-gray-50); border-radius: 12px; text-align: center; margin-bottom: 20px; border: 1px solid var(--border-gray); }
    .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }
    .cancel-btn, .apply-date-btn { padding: 10px 24px; border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
    .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
    .cancel-btn:hover { background: var(--border-gray); }
    .apply-date-btn { background: linear-gradient(135deg, var(--fb-blue) 0%, var(--fb-blue-dark) 100%); color: white; box-shadow: 0 4px 12px rgba(24,119,242,0.2); }
    .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(24,119,242,0.3); }

    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: 1fr; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-picker-trigger { max-width: 100%; }
        .date-picker-container { flex-direction: column; max-height: 85vh; overflow-y: auto; width: 95%; }
        .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; }
        .date-preset { white-space: nowrap; }
        .calendars-wrapper { flex-direction: column; gap: 16px; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <div class="page-header-icon">
            <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </div>
        <div>
            <h1>Facebook Trending Topics</h1>
            <p>Trending hashtags & topics on Facebook — filtered from project data</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.facebook.trending-topics') }}">
            <input type="hidden" name="project_id" id="hiddenProjectId" value="{{ $projectId ?? '' }}">
            <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
            <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">

            <div class="filter-content">
                <div class="filter-label">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;stroke-width:2;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Date Range
                </div>
                <div class="date-range-wrapper">
                    <button type="button" class="date-picker-trigger" id="datePickerTrigger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span id="dateRangeDisplay">{{ $startDate }} to {{ $endDate }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
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
        <div class="stat-card">
            <div class="stat-label">Total Topics</div>
            <div id="totalTopics" class="stat-value"><div class="skeleton-line" style="width:60%;height:28px;"></div></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Mentions</div>
            <div id="totalMentions" class="stat-value"><div class="skeleton-line" style="width:60%;height:28px;"></div></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Top Topic</div>
            <div id="topTopic" class="stat-value" style="font-size:18px;"><div class="skeleton-line" style="width:80%;height:22px;"></div></div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table class="status-table" id="loadingTable">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Topic / Hashtag</th>
                        <th style="min-width:200px;">Mention Volume</th>
                        <th class="text-center" style="width:120px;">Count</th>
                        <th class="text-center" style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td><div class="skeleton-line" style="width:30px;"></div></td>
                        <td><div class="skeleton-line" style="width:200px;"></div></td>
                        <td><div class="skeleton-line" style="width:100%;"></div></td>
                        <td><div class="skeleton-line" style="width:60px;margin:0 auto;"></div></td>
                        <td><div class="skeleton-line" style="width:60px;margin:0 auto;height:28px;border-radius:6px;"></div></td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <table class="status-table" id="trendingTable" style="display:none;">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Topic / Hashtag</th>
                        <th style="min-width:200px;">Mention Volume</th>
                        <th class="text-center" style="width:120px;">Count</th>
                        <th class="text-center" style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="trendingTableBody"></tbody>
            </table>

            <div id="emptyState" style="display:none;">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <h3>No Facebook Trending Topics Found</h3>
                    <p>No hashtag data available for the selected date range on Facebook.</p>
                </div>
            </div>
        </div>
        <div id="paginationWrapper" class="pagination" style="display:none;"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ---- DATE PICKER ----
(function() {
    'use strict';
    let sd = null, ed = null, m1 = new Date(), m2 = new Date(), picking = true;

    document.addEventListener('DOMContentLoaded', function() {
        const sv = document.getElementById('hiddenStartDate').value;
        const ev = document.getElementById('hiddenEndDate').value;
        sd = sv ? new Date(sv) : new Date(Date.now() - 6*86400000);
        ed = ev ? new Date(ev) : new Date();
        m1 = new Date(sd); m2 = new Date(sd); m2.setMonth(m2.getMonth()+1);
        render(); bind();
    });

    function bind() {
        document.getElementById('datePickerTrigger').onclick = open;
        document.querySelector('.date-picker-overlay').onclick = close;
        document.querySelector('.cancel-btn').onclick = close;
        document.addEventListener('keydown', e => { if(e.key==='Escape') close(); });
        document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', preset));
        document.getElementById('prevMonth').onclick = () => { m1.setMonth(m1.getMonth()-1); m2.setMonth(m2.getMonth()-1); render(); };
        document.getElementById('nextMonth').onclick = () => { m1.setMonth(m1.getMonth()+1); m2.setMonth(m2.getMonth()+1); render(); };
        document.getElementById('applyDatePicker').onclick = apply;
    }

    function open() { document.getElementById('datePickerModal').classList.add('show'); render(); }
    function close() { document.getElementById('datePickerModal').classList.remove('show'); }

    function preset(e) {
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        const p = e.target.dataset.preset, t = new Date(); t.setHours(0,0,0,0);
        if(p==='today'){ sd=new Date(t); ed=new Date(t); }
        else if(p==='yesterday'){ sd=new Date(t); sd.setDate(t.getDate()-1); ed=new Date(sd); }
        else if(p==='last7days'){ ed=new Date(t); sd=new Date(t); sd.setDate(t.getDate()-6); }
        else if(p==='last30days'){ ed=new Date(t); sd=new Date(t); sd.setDate(t.getDate()-29); }
        else if(p==='thismonth'){ sd=new Date(t.getFullYear(),t.getMonth(),1); ed=new Date(t); }
        else if(p==='lastmonth'){ sd=new Date(t.getFullYear(),t.getMonth()-1,1); ed=new Date(t.getFullYear(),t.getMonth(),0); }
        if(p!=='custom'){ m1=new Date(sd); m2=new Date(sd); m2.setMonth(m2.getMonth()+1); render(); }
    }

    function apply() {
        document.getElementById('hiddenStartDate').value = fmt(sd);
        document.getElementById('hiddenEndDate').value = fmt(ed);
        document.getElementById('dateRangeDisplay').textContent = `${fmt(sd)} to ${fmt(ed)}`;
        close();
    }

    function render() { renderCal('calendar1', m1); renderCal('calendar2', m2); document.getElementById('selectedRangeText').textContent = `${fmt(sd)} to ${fmt(ed)}`; }

    function renderCal(id, mon) {
        const el = document.getElementById(id); if(!el) return;
        const y=mon.getFullYear(), mo=mon.getMonth();
        const first=new Date(y,mo,1), last=new Date(y,mo+1,0), prev=new Date(y,mo,0);
        const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
        const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
        let h=`<div class="calendar-month">${MN[mo]} ${y}</div>
        <div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div>
        <div class="calendar-days">`;
        for(let i=first.getDay()-1;i>=0;i--) h+=`<button type="button" class="calendar-day other-month" disabled>${prev.getDate()-i}</button>`;
        const today=new Date(); today.setHours(0,0,0,0);
        for(let d=1;d<=last.getDate();d++){
            const dt=new Date(y,mo,d); dt.setHours(0,0,0,0);
            let cls='calendar-day';
            if(same(dt,today)) cls+=' today';
            if(dt>today) cls+=' disabled';
            if(sd&&ed){
                if(same(dt,sd)) cls+=' selected range-start';
                else if(same(dt,ed)) cls+=' selected range-end';
                else if(dt>sd&&dt<ed) cls+=' in-range';
            }
            h+=`<button type="button" class="${cls}" data-date="${fmt(dt)}" ${dt>today?'disabled':''}>${d}</button>`;
        }
        for(let i=1;i<7-last.getDay();i++) h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
        h+='</div>';
        el.innerHTML=h;
        el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(b=>b.addEventListener('click',dayClick));
    }

    function dayClick(e){
        const dt=new Date(e.target.dataset.date); dt.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-preset="custom"]').classList.add('active');
        if(picking||dt<sd){sd=dt;ed=dt;picking=false;}
        else{ed=dt>=sd?dt:sd;if(dt<sd){ed=sd;sd=dt;}picking=true;}
        render();
    }

    function fmt(d){if(!d)return '';return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
    function same(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
})();

// ---- FB TRENDING ----
const FBTrendingLoader = {
    projectId: '{{ $projectId ?? "" }}',
    startDate: '{{ $startDate }}',
    endDate: '{{ $endDate }}',
    allTopics: [], currentPage: 1, perPage: 20, maxVolume: 0,

    async init() {
        try { await this.load(); } catch(err) { console.error('FB Trending error:', err); this.showEmpty(); }
    },

    async load() {
        const p = new URLSearchParams({ project_id: this.projectId, start_date: this.startDate, end_date: this.endDate });
        const res = await fetch(`/mk/api/facebook/trending-topics?${p}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.error || 'API error');

        const d = json.data;
        this.allTopics = d.hashtags || [];
        this.maxVolume = this.allTopics.length ? this.allTopics[0].size : 0;

        document.getElementById('totalTopics').textContent = (d.total_hashtags||0).toLocaleString();
        document.getElementById('totalMentions').textContent = (d.total_mentions||0).toLocaleString();
        const top = d.top_hashtag;
        document.getElementById('topTopic').textContent = top ? `#${(top.hashtag||top.name||'').replace(/^#/,'')}` : '—';

        this.renderTable();
    },

    renderTable() {
        const loading = document.getElementById('loadingTable');
        const table = document.getElementById('trendingTable');
        const empty = document.getElementById('emptyState');
        if (!this.allTopics.length) {
            loading.style.display='none'; table.style.display='none';
            empty.style.display='block'; document.getElementById('paginationWrapper').style.display='none';
            return;
        }
        const start = (this.currentPage-1)*this.perPage;
        document.getElementById('trendingTableBody').innerHTML = this.allTopics.slice(start, start+this.perPage).map((t,i)=>this.row(t,start+i+1)).join('');
        loading.style.display='none'; table.style.display='table'; empty.style.display='none';
        this.renderPagination();
    },

    row(t, rank) {
        const name = this.esc(t.name||'');
        const size = t.size||0;
        const pct = this.maxVolume>0 ? Math.round(size/this.maxVolume*100) : 0;
        const url = `https://www.facebook.com/hashtag/${encodeURIComponent(name.replace(/^#/,''))}`;
        return `<tr>
            <td class="rank-cell">#${rank}</td>
            <td><div class="topic-cell"><span class="hashtag-badge">#</span><span>${name}</span></div></td>
            <td class="volume-cell"><div class="volume-bar-wrapper"><div class="volume-bar-track"><div class="volume-bar-fill" style="width:${pct}%"></div></div><span class="volume-count">${size.toLocaleString()}</span></div></td>
            <td class="appearances-cell">${size.toLocaleString()}</td>
            <td class="action-cell"><a href="${url}" target="_blank" class="btn-view"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>View</a></td>
        </tr>`;
    },

    getRange(cur, total) {
        if(total<=7) return Array.from({length:total},(_,i)=>i+1);
        if(cur<=4) return [1,2,3,4,5,'...',total];
        if(cur>=total-3) return [1,'...',total-4,total-3,total-2,total-1,total];
        return [1,'...',cur-1,cur,cur+1,'...',total];
    },

    renderPagination() {
        const total = Math.ceil(this.allTopics.length/this.perPage);
        const from = (this.currentPage-1)*this.perPage+1;
        const to = Math.min(this.currentPage*this.perPage, this.allTopics.length);
        const w = document.getElementById('paginationWrapper');
        let h = `<div class="pagination-info">Showing ${from}–${to} of ${this.allTopics.length} topics</div>
        <div style="display:flex;align-items:center;gap:6px;">
        <button class="page-btn" onclick="FBTrendingLoader.goPage(${this.currentPage-1})" ${this.currentPage===1?'disabled':''}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
        </button>`;
        this.getRange(this.currentPage, total).forEach(p=>{
            h += p==='...' ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
                : `<button class="page-btn ${p===this.currentPage?'active':''}" onclick="FBTrendingLoader.goPage(${p})">${p}</button>`;
        });
        h += `<button class="page-btn" onclick="FBTrendingLoader.goPage(${this.currentPage+1})" ${this.currentPage===total?'disabled':''}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
        </button></div>`;
        w.innerHTML=h; w.style.display='flex';
    },

    goPage(p) {
        const total = Math.ceil(this.allTopics.length/this.perPage);
        if(p<1||p>total) return;
        this.currentPage=p; this.renderTable();
        document.querySelector('.table-container').scrollIntoView({behavior:'smooth',block:'start'});
    },

    showEmpty() {
        document.getElementById('loadingTable').style.display='none';
        document.getElementById('trendingTable').style.display='none';
        document.getElementById('emptyState').style.display='block';
        document.getElementById('paginationWrapper').style.display='none';
        ['totalTopics','totalMentions'].forEach(id=>document.getElementById(id).textContent='0');
        document.getElementById('topTopic').textContent='—';
    },

    esc(t){const d=document.createElement('div');d.textContent=t;return d.innerHTML;}
};

document.addEventListener('DOMContentLoaded', ()=>FBTrendingLoader.init());
</script>
@endsection