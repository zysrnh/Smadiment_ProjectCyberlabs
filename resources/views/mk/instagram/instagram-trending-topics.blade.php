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
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --ig-gradient: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
    }

    body { background: var(--bg-gray-50); }

    .dashboard-container { padding: 24px; max-width: 1600px; margin: 0 auto; }

    .page-header { margin-bottom: 32px; }
    .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
    .page-header p  { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* Filter Card */
    .filter-card {
        background: var(--bg-white); border-radius: 16px; padding: 20px 24px;
        margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray);
    }

    .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .filter-label   { font-size: 14px; font-weight: 600; color: var(--text-primary); white-space: nowrap; }
    .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }

    .apply-btn {
        padding: 12px 28px; background: var(--ig-gradient); color: white;
        border: none; border-radius: 12px; font-family: 'Poppins', sans-serif;
        font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 12px rgba(220,39,67,0.2);
    }
    .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220,39,67,0.3); }
    .apply-btn svg  { width: 18px; height: 18px; stroke: currentColor; fill: none; }

    /* Stats Grid */
    .stats-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 12px;
        padding: 20px; box-shadow: var(--shadow-sm);
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

    /* Table Container */
    .table-container {
        background: var(--bg-white); border-radius: 12px;
        border: 1px solid var(--border-gray); box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .table-wrapper { overflow-x: auto; }

    /* Table */
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
    .status-table td { padding: 18px 24px; font-size: 14px; color: var(--text-primary); vertical-align: middle; }

    .rank-cell    { font-weight: 600; color: var(--text-secondary); font-size: 14px; width: 60px; }
    .topic-cell   { font-weight: 600; color: var(--primary); font-size: 14px; }
    .mentions-cell, .share-cell { font-weight: 500; color: var(--text-secondary); font-size: 14px; text-align: center; }
    .action-cell  { text-align: center; width: 100px; }

    .btn-view {
        padding: 6px 14px; background: transparent; border: 1px solid var(--border-gray);
        border-radius: 6px; font-size: 12px; font-weight: 600; color: var(--text-primary);
        cursor: pointer; transition: all 0.2s; text-decoration: none;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .btn-view:hover { background: var(--primary); color: white; border-color: var(--primary); }
    .btn-view svg  { width: 12px; height: 12px; stroke: currentColor; fill: none; }

    /* Pagination */
    .pagination {
        display: flex; justify-content: space-between; align-items: center;
        padding: 20px; background: var(--bg-white);
        border-top: 1px solid var(--border-gray); flex-wrap: wrap; gap: 12px;
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
    .page-btn.active  { background: var(--ig-gradient); color: white; border-color: transparent; }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* Skeleton */
    .skeleton-line {
        height: 16px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 8px;
    }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    /* Empty State */
    .empty-state { text-align: center; padding: 80px 20px; }
    .empty-state svg { width: 64px; height: 64px; color: var(--text-secondary); margin-bottom: 16px; stroke: currentColor; fill: none; }
    .empty-state h3  { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px; }
    .empty-state p   { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* Alert */
    .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px; }
    .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

    /* Date Picker Trigger */
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

    /* Date Picker Modal */
    .date-picker-modal {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000;
        display: none; align-items: center; justify-content: center;
        background: rgba(0,0,0,0.5); backdrop-filter: blur(8px);
    }
    .date-picker-modal.show { display: flex; }
    .date-picker-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; cursor: pointer; }

    .date-picker-container {
        position: relative; background: #fff; border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.3); display: flex;
        max-width: 900px; width: 90%; max-height: 90vh; z-index: 10001;
        animation: slideUp 0.3s ease-out;
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
    .date-picker-header  { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }

    .nav-btn {
        width: 36px; height: 36px; border-radius: 8px; background: var(--bg-gray-50);
        border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; flex-shrink: 0;
    }
    .nav-btn:hover { background: var(--ig-gradient); border-color: transparent; }
    .nav-btn svg { width: 20px; height: 20px; }

    .calendars-wrapper { display: flex; gap: 24px; flex: 1; min-height: 0; }
    .calendar { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .calendar-month    { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-align: center; }
    .calendar-weekdays { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; margin-bottom: 8px; }
    .weekday           { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
    .calendar-days     { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; }

    .calendar-day {
        aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 500; border-radius: 8px; cursor: pointer; transition: all 0.2s;
        color: var(--text-primary); background: transparent; border: none; padding: 0;
    }
    .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
    .calendar-day.other-month { color: #cbd5e1; cursor: default; }
    .calendar-day.disabled    { color: #e2e8f0; cursor: not-allowed; }
    .calendar-day.today       { border: 2px solid var(--primary); }
    .calendar-day.selected    { background: var(--ig-gradient); color: white; }
    .calendar-day.in-range    { background: rgba(220,39,67,0.1); color: var(--primary); }
    .calendar-day.range-start,
    .calendar-day.range-end   { background: var(--ig-gradient); color: white; }

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

    /* Responsive */
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: 1fr; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-range-wrapper { flex-direction: column; }
        .apply-btn { width: 100%; justify-content: center; }
        .status-table th, .status-table td { padding: 12px 10px; font-size: 12px; }
        .stat-value { font-size: 20px; }
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

    <div class="page-header">
        <h1>Instagram Top Hashtags</h1>
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
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8"  y1="2" x2="8"  y2="6"/>
                        <line x1="3"  y1="10" x2="21" y2="10"/>
                    </svg>
                    Date Range
                </div>

                <div class="date-range-wrapper">
                    <button type="button" class="date-picker-trigger" id="datePickerTrigger">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8"  y1="2" x2="8"  y2="6"/>
                            <line x1="3"  y1="10" x2="21" y2="10"/>
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
                <div class="skeleton-line" style="width:60%;"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Mentions</div>
            <div id="statTotalMentions" class="stat-value">
                <div class="skeleton-line" style="width:60%;"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Top Hashtag</div>
            <div id="statTopHashtag" class="stat-value">
                <div class="skeleton-line" style="width:70%;"></div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-wrapper">

            <!-- Loading Skeleton -->
            <table class="status-table" id="loadingTable" style="display: table;">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Hashtag</th>
                        <th class="text-center" style="width:140px;">Mentions</th>
                        <th class="text-center" style="width:120px;">Share %</th>
                        <th class="text-center" style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 3; $i++)
                    <tr>
                        <td colspan="5" style="padding: 30px 24px;">
                            <div style="display:flex; align-items:center; gap:20px;">
                                <div class="skeleton-line" style="width:30px;height:16px;margin-bottom:0;"></div>
                                <div class="skeleton-line" style="width:{{ 200+$i*60 }}px;height:16px;margin-bottom:0;"></div>
                                <div style="flex:1;"></div>
                                <div class="skeleton-line" style="width:80px;height:16px;margin-bottom:0;"></div>
                                <div class="skeleton-line" style="width:60px;height:16px;margin-bottom:0;"></div>
                                <div class="skeleton-line" style="width:60px;height:28px;border-radius:6px;margin-bottom:0;"></div>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Real Table -->
            <table class="status-table" id="hashtagTable" style="display: none;">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Hashtag</th>
                        <th class="text-center" style="width:140px;">Mentions</th>
                        <th class="text-center" style="width:120px;">Share %</th>
                        <th class="text-center" style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="hashtagTableBody"></tbody>
            </table>

            <!-- Empty State -->
            <div id="emptyState" style="display: none;">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8"   x2="12"    y2="12"/>
                        <line x1="12" y1="16"  x2="12.01" y2="16"/>
                    </svg>
                    <h3>No Hashtags Found</h3>
                    <p>Tidak ada data hashtag untuk periode ini. Coba ubah tanggal filter.</p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div id="paginationWrapper" class="pagination" style="display: none;"></div>
    </div>

    @endif
</div>
@endsection

@section('scripts')
<script>
// ============================================================
// DATE PICKER
// ============================================================
(function() {
    'use strict';

    let s1 = null, s2 = null, mo1 = new Date(), mo2 = new Date(), pickStart = true;

    document.addEventListener('DOMContentLoaded', function() {
        const sv = document.getElementById('hiddenStartDate');
        const ev = document.getElementById('hiddenEndDate');

        s1 = (sv && sv.value) ? new Date(sv.value) : (() => { const d = new Date(); d.setDate(d.getDate()-6); return d; })();
        s2 = (ev && ev.value) ? new Date(ev.value) : new Date();

        mo1 = new Date(s1);
        mo2 = new Date(s1);
        mo2.setMonth(mo2.getMonth() + 1);

        renderCals();
        bindEvents();
    });

    function bindEvents() {
        document.getElementById('datePickerTrigger')?.addEventListener('click', open);
        document.querySelector('.date-picker-overlay')?.addEventListener('click', close);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
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
            case 'today':      s1 = new Date(today); s2 = new Date(today); break;
            case 'yesterday':  s1 = new Date(today); s1.setDate(today.getDate()-1); s2 = new Date(s1); break;
            case 'last7days':  s2 = new Date(today); s1 = new Date(today); s1.setDate(today.getDate()-6); break;
            case 'last30days': s2 = new Date(today); s1 = new Date(today); s1.setDate(today.getDate()-29); break;
            case 'thismonth':  s1 = new Date(today.getFullYear(), today.getMonth(), 1); s2 = new Date(today); break;
            case 'lastmonth':  s1 = new Date(today.getFullYear(), today.getMonth()-1, 1); s2 = new Date(today.getFullYear(), today.getMonth(), 0); break;
        }
        if (e.target.dataset.preset !== 'custom') {
            mo1 = new Date(s1); mo2 = new Date(s1); mo2.setMonth(mo2.getMonth()+1);
            updateDisp(); renderCals();
        }
    }

    function applyDates() {
        document.getElementById('hiddenStartDate').value = fmt(s1);
        document.getElementById('hiddenEndDate').value   = fmt(s2);
        document.getElementById('dateRangeDisplay').textContent = `${fmt(s1)} to ${fmt(s2)}`;
        close();
    }

    function renderCals() { renderCal('calendar1', mo1); renderCal('calendar2', mo2); updateDisp(); }

    function renderCal(id, mo) {
        const el = document.getElementById(id); if (!el) return;
        const y = mo.getFullYear(), m = mo.getMonth();
        const first = new Date(y,m,1), last = new Date(y,m+1,0), prev = new Date(y,m,0);
        const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const today  = new Date(); today.setHours(0,0,0,0);

        let html = `<div class="calendar-month">${MONTHS[m]} ${y}</div>
            <div class="calendar-weekdays">${['Su','Mo','Tu','We','Th','Fr','Sa'].map(d=>`<div class="weekday">${d}</div>`).join('')}</div>
            <div class="calendar-days">`;

        for (let i = first.getDay()-1; i >= 0; i--)
            html += `<button type="button" class="calendar-day other-month" disabled>${prev.getDate()-i}</button>`;

        for (let d = 1; d <= last.getDate(); d++) {
            const dt = new Date(y,m,d); dt.setHours(0,0,0,0);
            let cls = 'calendar-day';
            if (sameDay(dt, today)) cls += ' today';
            if (dt > today) cls += ' disabled';
            if (s1 && s2) {
                if (sameDay(dt, s1)) cls += ' selected range-start';
                else if (sameDay(dt, s2)) cls += ' selected range-end';
                else if (dt > s1 && dt < s2) cls += ' in-range';
            }
            html += `<button type="button" class="${cls}" data-date="${fmt(dt)}" ${dt>today?'disabled':''}>${d}</button>`;
        }

        const ld = last.getDay();
        for (let i = 1; i < 7-ld; i++)
            html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;

        html += '</div>';
        el.innerHTML = html;
        el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(b => b.addEventListener('click', pickDate));
    }

    function pickDate(e) {
        const dt = new Date(e.target.dataset.date); dt.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        document.querySelector('[data-preset="custom"]')?.classList.add('active');
        if (pickStart || dt < s1) { s1 = dt; s2 = dt; pickStart = false; }
        else { s2 = dt >= s1 ? dt : s1; if (dt < s1) { s2 = s1; s1 = dt; } pickStart = true; }
        updateDisp(); renderCals();
    }

    function updateDisp() {
        const el = document.getElementById('selectedRangeText');
        if (el && s1 && s2) el.textContent = `${fmt(s1)} to ${fmt(s2)}`;
    }

    function fmt(d)        { return d ? `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}` : ''; }
    function sameDay(a, b) { return a && b && a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }
})();

// ============================================================
// HASHTAG APP  — struktur 100% sama dengan X TrendingLoader
// ============================================================
const HashtagApp = {
    projectId   : '{{ $projectId ?? "" }}',
    startDate   : '{{ $startDate ?? "" }}',
    endDate     : '{{ $endDate ?? "" }}',
    allHashtags : [],
    currentPage : 1,
    perPage     : 20,

    async init() {
        if (!this.projectId) return;
        try {
            await this.loadData();
        } catch (err) {
            console.error('HashtagApp error:', err);
            this.showError();
        }
    },

    async loadData() {
        const url    = `/mk/api/instagram/trending-topics?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`;
        const res    = await fetch(url);
        const result = await res.json();

        if (!result.success) throw new Error(result.error || 'API Error');

        const data       = result.data;
        this.allHashtags = data.hashtags || [];

        // Update Stats
        const fmt = n => new Intl.NumberFormat('en-US').format(n);
        document.getElementById('statTotalHashtags').textContent = fmt(data.total_hashtags || 0);
        document.getElementById('statTotalMentions').textContent = fmt(data.total_mentions || 0);
        document.getElementById('statTopHashtag').textContent    = data.top_hashtag ? '#' + data.top_hashtag.name : '—';

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

        const maxMentions = this.allHashtags[0]?.size || 1;
        const startIdx    = (this.currentPage - 1) * this.perPage;
        const pageItems   = this.allHashtags.slice(startIdx, startIdx + this.perPage);

        document.getElementById('hashtagTableBody').innerHTML = pageItems.map((h, i) =>
            this.createRow(h, startIdx + i + 1, maxMentions)
        ).join('');

        loadingTable.style.display = 'none';
        hashtagTable.style.display = 'table';
        emptyState.style.display   = 'none';

        this.updatePagination();
    },

    createRow(h, rank, maxMentions) {
        const name = this.esc(h.name);
        const fmt  = n => new Intl.NumberFormat('en-US').format(n);
        const pct  = ((h.size / maxMentions) * 100).toFixed(1);
        const link = `https://www.instagram.com/explore/tags/${encodeURIComponent(h.name)}/`;

        return `
            <tr>
                <td class="rank-cell">#${rank}</td>
                <td class="topic-cell">#${name}</td>
                <td class="mentions-cell">${fmt(h.size)}</td>
                <td class="share-cell">${pct}%</td>
                <td class="action-cell">
                    <a href="${link}" target="_blank" class="btn-view">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        View
                    </a>
                </td>
            </tr>`;
    },

    getPageRange(cur, total) {
        if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
        if (cur <= 4)   return [1, 2, 3, 4, 5, '...', total];
        if (cur >= total - 3) return [1, '...', total-4, total-3, total-2, total-1, total];
        return [1, '...', cur-1, cur, cur+1, '...', total];
    },

    updatePagination() {
        const totalPages = Math.ceil(this.allHashtags.length / this.perPage);
        const wrapper    = document.getElementById('paginationWrapper');
        const from       = this.allHashtags.length ? (this.currentPage - 1) * this.perPage + 1 : 0;
        const to         = Math.min(this.currentPage * this.perPage, this.allHashtags.length);

        let html = `<div class="pagination-info">Showing ${from}–${to} of <strong>${this.allHashtags.length}</strong> hashtags</div>`;
        html += `<div style="display:flex;align-items:center;gap:6px;">`;
        html += `<button class="page-btn" onclick="HashtagApp.changePage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
        </button>`;

        this.getPageRange(this.currentPage, totalPages).forEach(p => {
            html += p === '...'
                ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
                : `<button class="page-btn ${p === this.currentPage ? 'active' : ''}" onclick="HashtagApp.changePage(${p})">${p}</button>`;
        });

        html += `<button class="page-btn" onclick="HashtagApp.changePage(${this.currentPage + 1})" ${this.currentPage === totalPages ? 'disabled' : ''}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
        </button></div>`;

        wrapper.innerHTML = html;
        wrapper.style.display = this.allHashtags.length > 0 ? 'flex' : 'none';
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

    esc(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }
};

document.addEventListener('DOMContentLoaded', () => HashtagApp.init());
</script>
@endsection