@extends('mk.layouts.app')

@section('title', 'X Trending Topics - SMADIMENT')

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

    .dashboard-container {
        padding: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }

    .page-header { margin-bottom: 32px; }
    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 8px 0;
    }
    .page-header p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    /* Filter Card */
    .filter-card {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-gray);
    }

    .filter-content {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
    }

    .date-range-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .location-select {
        padding: 10px 16px;
        background: var(--bg-gray-50);
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
        min-width: 180px;
    }

    .location-select:focus {
        border-color: var(--primary-green);
        background: var(--bg-white);
        box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
    }

    .apply-btn {
        padding: 12px 28px;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }

    .apply-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
    }

    .apply-btn svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        fill: none;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-green);
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }

    /* Table Container */
    .table-container {
        background: var(--bg-white);
        border-radius: 12px;
        border: 1px solid var(--border-gray);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    /* Ultra Clean Table */
    .status-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Poppins', sans-serif;
    }

    .status-table thead {
        background: var(--bg-white);
    }

    .status-table th {
        padding: 16px 24px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border-bottom: 2px solid var(--bg-gray-100);
        white-space: nowrap;
    }

    .status-table th.text-center { text-align: center; }
    .status-table th.text-right { text-align: right; }

    .status-table tbody tr {
        border-bottom: 1px solid var(--bg-gray-100);
        transition: background 0.15s;
    }

    .status-table tbody tr:hover {
        background: var(--bg-gray-50);
    }

    .status-table tbody tr:last-child {
        border-bottom: none;
    }

    .status-table td {
        padding: 18px 24px;
        font-size: 14px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    /* Rank Column */
    .rank-cell {
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 14px;
        width: 60px;
    }

    /* Topic Name Column */
    .topic-cell {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 14px;
    }

    /* Stats Columns */
    .appearances-cell,
    .rank-avg-cell {
        font-weight: 500;
        color: var(--text-secondary);
        font-size: 14px;
        text-align: center;
    }

    /* Action Column */
    .action-cell {
        text-align: center;
        width: 100px;
    }

    .btn-view {
        padding: 6px 14px;
        background: transparent;
        border: 1px solid var(--border-gray);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-view:hover {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
    }

    .btn-view svg {
        width: 12px;
        height: 12px;
        stroke: currentColor;
        fill: none;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        padding: 20px;
        background: var(--bg-white);
        border-top: 1px solid var(--border-gray);
    }

    .pagination-btn {
        padding: 8px 16px;
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
    }

    .pagination-btn:hover:not(:disabled) {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
    }

    .pagination-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .pagination-info {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .pagination-info strong {
        color: var(--text-primary);
        font-weight: 700;
    }

    /* Skeleton Loading */
    .skeleton-line {
        height: 16px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: var(--bg-white);
        border-radius: 16px;
        border: 1px solid var(--border-gray);
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        color: var(--text-secondary);
        margin-bottom: 16px;
        stroke: currentColor;
        fill: none;
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 8px 0;
    }

    .empty-state p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    /* ========================================
       DATE PICKER STYLES
       ======================================== */
    
    /* Date Picker Trigger Button */
    .date-picker-trigger {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        background: var(--bg-gray-50);
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
        max-width: 400px;
    }

    .date-picker-trigger:hover {
        border-color: var(--primary-green);
        background: var(--bg-white);
        box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
    }

    .date-picker-trigger svg:first-child {
        width: 18px;
        height: 18px;
        color: var(--text-secondary);
        flex-shrink: 0;
    }

    .date-picker-trigger span {
        flex: 1;
        text-align: left;
    }

    .date-picker-trigger svg:last-child {
        width: 16px;
        height: 16px;
        margin-left: auto;
        color: var(--text-secondary);
    }

    /* Date Picker Modal */
    .date-picker-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
    }

    .date-picker-modal.show {
        display: flex;
    }

    .date-picker-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        cursor: pointer;
    }

    .date-picker-container {
        position: relative;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        display: flex;
        max-width: 900px;
        width: 90%;
        max-height: 90vh;
        z-index: 10001;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Sidebar with Presets */
    .date-picker-sidebar {
        width: 180px;
        background: var(--bg-gray-50);
        border-right: 1px solid var(--border-gray);
        padding: 16px 12px;
        border-radius: 16px 0 0 16px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex-shrink: 0;
    }

    .date-preset {
        padding: 10px 16px;
        background: transparent;
        border: none;
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-primary);
        text-align: left;
        cursor: pointer;
        transition: all 0.2s;
    }

    .date-preset:hover {
        background: var(--bg-white);
        color: var(--primary-green);
    }

    .date-preset.active {
        background: var(--primary-green);
        color: white;
    }

    /* Calendar Content */
    .date-picker-content {
        flex: 1;
        padding: 24px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .date-picker-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .nav-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: var(--bg-gray-50);
        border: 1px solid var(--border-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .nav-btn:hover {
        background: var(--primary-green);
        border-color: var(--primary-green);
        color: white;
    }

    .nav-btn svg {
        width: 20px;
        height: 20px;
    }

    /* Calendars Wrapper */
    .calendars-wrapper {
        display: flex;
        gap: 24px;
        flex: 1;
        min-height: 0;
    }

    .calendar {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .calendar-month {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 16px;
        text-align: center;
    }

    .calendar-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 8px;
    }

    .weekday {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        padding: 8px 0;
    }

    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }

    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        color: var(--text-primary);
        background: transparent;
        border: none;
        padding: 0;
    }

    .calendar-day:hover:not(.disabled):not(.other-month) {
        background: var(--bg-gray-100);
    }

    .calendar-day.other-month {
        color: #cbd5e1;
        cursor: default;
    }

    .calendar-day.disabled {
        color: #e2e8f0;
        cursor: not-allowed;
    }

    .calendar-day.today {
        border: 2px solid var(--primary-green);
    }

    .calendar-day.selected {
        background: var(--primary-green);
        color: white;
    }

    .calendar-day.in-range {
        background: rgba(3, 128, 71, 0.1);
        color: var(--primary-green);
    }

    .calendar-day.range-start,
    .calendar-day.range-end {
        background: var(--primary-green);
        color: white;
    }

    /* Date Display */
    .date-picker-display {
        padding: 16px 20px;
        background: var(--bg-gray-50);
        border-radius: 12px;
        text-align: center;
        margin-bottom: 20px;
        border: 1px solid var(--border-gray);
    }

    .date-picker-display span {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Footer Buttons */
    .date-picker-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .cancel-btn,
    .apply-date-btn {
        padding: 10px 24px;
        border-radius: 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .cancel-btn {
        background: var(--bg-gray-100);
        color: var(--text-primary);
    }

    .cancel-btn:hover {
        background: var(--border-gray);
    }

    .apply-date-btn {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }

    .apply-date-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
    }

    /* ========================================
       RESPONSIVE STYLES
       ======================================== */

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 16px;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .filter-content {
            flex-direction: column;
            align-items: stretch;
        }
        .date-range-wrapper {
            flex-direction: column;
        }
        .location-select {
            width: 100%;
        }
        .apply-btn {
            width: 100%;
            justify-content: center;
        }
        
        /* Mobile Table Adjustments */
        .status-table th,
        .status-table td {
            padding: 12px 10px;
            font-size: 12px;
        }
        
        .stat-value {
            font-size: 14px;
        }

        /* Date Picker Responsive */
        .date-picker-container {
            flex-direction: column;
            max-height: 85vh;
            overflow-y: auto;
            width: 95%;
        }

        .date-picker-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid var(--border-gray);
            border-radius: 16px 16px 0 0;
            flex-direction: row;
            overflow-x: auto;
            padding: 12px 16px;
        }

        .date-preset {
            white-space: nowrap;
        }

        .date-picker-content {
            padding: 20px 16px;
        }

        .calendars-wrapper {
            flex-direction: column;
            gap: 16px;
        }

        .date-picker-header {
            flex-wrap: wrap;
        }

        .date-picker-trigger {
            max-width: 100%;
        }

        .calendar-day {
            font-size: 12px;
        }

        .weekday {
            font-size: 10px;
        }

        .cancel-btn,
        .apply-date-btn {
            flex: 1;
        }
    }
    .page-btn {
    width: 36px; height: 36px; border-radius: 10px;
    border: 1px solid var(--border-gray); background: var(--bg-white);
    color: var(--text-primary); font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Poppins', sans-serif;
}
.page-btn:hover:not(:disabled) { border-color: var(--primary-green); color: var(--primary-green); background: rgba(3,128,71,0.05); }
.page-btn.active { background: var(--primary-green); color: white; border-color: var(--primary-green); }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <h1>X Trending Topics</h1>
        <p>Real-time trending topics on X (Twitter) in {{ $location }}</p>
    </div>

    <!-- Filter Card with Date Picker -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.x.trending-topics') }}">
            <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
            <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">
            <input type="hidden" name="location" id="hiddenLocation" value="{{ $location }}">
            
            <div class="filter-content">
                <div class="filter-label">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
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
                
                <select name="location" id="locationSelect" class="location-select">
                    <option value="Indonesia" {{ $location === 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                    <option value="Worldwide" {{ $location === 'Worldwide' ? 'selected' : '' }}>Worldwide</option>
                    <option value="United States" {{ $location === 'United States' ? 'selected' : '' }}>United States</option>
                    <option value="United Kingdom" {{ $location === 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                </select>
                
                <button type="submit" class="apply-btn">
                    <svg viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Date Range Picker Modal -->
    <div class="date-picker-modal" id="datePickerModal">
        <div class="date-picker-overlay"></div>
        <div class="date-picker-container">
            <!-- Sidebar with Presets -->
            <div class="date-picker-sidebar">
                <button type="button" class="date-preset" data-preset="today">Today</button>
                <button type="button" class="date-preset" data-preset="yesterday">Yesterday</button>
                <button type="button" class="date-preset" data-preset="last7days">Last 7 Days</button>
                <button type="button" class="date-preset" data-preset="last30days">Last 30 Days</button>
                <button type="button" class="date-preset" data-preset="thismonth">This Month</button>
                <button type="button" class="date-preset" data-preset="lastmonth">Last Month</button>
                <button type="button" class="date-preset active" data-preset="custom">Custom Range</button>
            </div>
            
            <!-- Calendar Content -->
            <div class="date-picker-content">
                <!-- Navigation Header -->
                <div class="date-picker-header">
                    <button type="button" class="nav-btn" id="prevMonth">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>
                    
                    <div class="calendars-wrapper">
                        <div class="calendar" id="calendar1"></div>
                        <div class="calendar" id="calendar2"></div>
                    </div>
                    
                    <button type="button" class="nav-btn" id="nextMonth">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Selected Date Display -->
                <div class="date-picker-display">
                    <span id="selectedRangeText">{{ $startDate }} to {{ $endDate }}</span>
                </div>
                
                <!-- Footer Buttons -->
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
            <div class="stat-label">Total Periods</div>
            <div id="totalPeriods" class="stat-value">
                <div class="skeleton-line" style="width:60%;"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Unique Topics</div>
            <div id="uniqueTopics" class="stat-value">
                <div class="skeleton-line" style="width:60%;"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Topics/Period</div>
            <div id="avgTopics" class="stat-value">
                <div class="skeleton-line" style="width:60%;"></div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-wrapper">
            <!-- Loading State -->
            <table class="status-table" id="loadingTable" style="display: table;">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Topic Name</th>
                        <th class="text-center" style="width: 140px;">Appearances</th>
                        <th class="text-center" style="width: 120px;">Avg Rank</th>
                        <th class="text-center" style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" style="padding: 30px 24px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div class="skeleton-line" style="width: 30px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 280px; height: 16px;"></div>
                                <div style="flex: 1;"></div>
                                <div class="skeleton-line" style="width: 80px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 70px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 60px; height: 28px; border-radius: 6px;"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding: 30px 24px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div class="skeleton-line" style="width: 30px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 240px; height: 16px;"></div>
                                <div style="flex: 1;"></div>
                                <div class="skeleton-line" style="width: 80px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 70px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 60px; height: 28px; border-radius: 6px;"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding: 30px 24px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div class="skeleton-line" style="width: 30px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 260px; height: 16px;"></div>
                                <div style="flex: 1;"></div>
                                <div class="skeleton-line" style="width: 80px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 70px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 60px; height: 28px; border-radius: 6px;"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Actual Table (hidden initially) -->
            <table class="status-table" id="trendingTable" style="display: none;">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Topic Name</th>
                        <th class="text-center" style="width: 140px;">Appearances</th>
                        <th class="text-center" style="width: 120px;">Avg Rank</th>
                        <th class="text-center" style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="trendingTableBody">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>

            <!-- Empty State -->
            <div id="emptyState" style="display: none;">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <h3>No Trending Topics Found</h3>
                    <p>No trending topics data available for the selected date range.</p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
                <div id="paginationWrapper" class="pagination" style="display: none; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;"></div>

    </div>

</div>
@endsection

@section('scripts')
<script>
// ========================================
// DATE PICKER JAVASCRIPT
// ========================================
(function() {
  'use strict';
  
  let selectedStartDate = null;
  let selectedEndDate = null;
  let currentMonth1 = new Date();
  let currentMonth2 = new Date();
  let selectingStart = true;

  document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('hiddenStartDate');
    const endDateInput = document.getElementById('hiddenEndDate');
    
    if (startDateInput && startDateInput.value) {
      selectedStartDate = new Date(startDateInput.value);
    } else {
      selectedEndDate = new Date();
      selectedStartDate = new Date();
      selectedStartDate.setDate(selectedStartDate.getDate() - 6);
    }
    
    if (endDateInput && endDateInput.value) {
      selectedEndDate = new Date(endDateInput.value);
    }
    
    currentMonth1 = new Date(selectedStartDate);
    currentMonth2 = new Date(selectedStartDate);
    currentMonth2.setMonth(currentMonth2.getMonth() + 1);
    
    renderCalendars();
    setupEventListeners();
  });

  function setupEventListeners() {
    const trigger = document.getElementById('datePickerTrigger');
    if (trigger) {
      trigger.addEventListener('click', openDatePicker);
    }

    const overlay = document.querySelector('.date-picker-overlay');
    if (overlay) {
      overlay.addEventListener('click', closeDatePicker);
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('datePickerModal');
        if (modal && modal.classList.contains('show')) {
          closeDatePicker();
        }
      }
    });

    document.querySelectorAll('.date-preset').forEach(btn => {
      btn.addEventListener('click', handlePresetClick);
    });

    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
      prevBtn.addEventListener('click', function() {
        currentMonth1.setMonth(currentMonth1.getMonth() - 1);
        currentMonth2.setMonth(currentMonth2.getMonth() - 1);
        renderCalendars();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function() {
        currentMonth1.setMonth(currentMonth1.getMonth() + 1);
        currentMonth2.setMonth(currentMonth2.getMonth() + 1);
        renderCalendars();
      });
    }

    const applyBtn = document.getElementById('applyDatePicker');
    if (applyBtn) {
      applyBtn.addEventListener('click', applyDateSelection);
    }

    const cancelBtn = document.querySelector('.cancel-btn');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', closeDatePicker);
    }

    // Update hidden location when select changes
    const locationSelect = document.getElementById('locationSelect');
    if (locationSelect) {
      locationSelect.addEventListener('change', function() {
        document.getElementById('hiddenLocation').value = this.value;
      });
    }
  }

  function openDatePicker() {
    document.getElementById('datePickerModal').classList.add('show');
    renderCalendars();
  }

  function closeDatePicker() {
    document.getElementById('datePickerModal').classList.remove('show');
  }

  function handlePresetClick(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');

    const preset = e.target.dataset.preset;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    switch(preset) {
      case 'today':
        selectedStartDate = new Date(today);
        selectedEndDate = new Date(today);
        break;
      case 'yesterday':
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 1);
        selectedEndDate = new Date(selectedStartDate);
        break;
      case 'last7days':
        selectedEndDate = new Date(today);
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 6);
        break;
      case 'last30days':
        selectedEndDate = new Date(today);
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 29);
        break;
      case 'thismonth':
        selectedStartDate = new Date(today.getFullYear(), today.getMonth(), 1);
        selectedEndDate = new Date(today);
        break;
      case 'lastmonth':
        selectedStartDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        selectedEndDate = new Date(today.getFullYear(), today.getMonth(), 0);
        break;
    }
    
    if (preset !== 'custom') {
      currentMonth1 = new Date(selectedStartDate);
      currentMonth2 = new Date(selectedStartDate);
      currentMonth2.setMonth(currentMonth2.getMonth() + 1);
      
      updateDateDisplay();
      renderCalendars();
    }
  }

  function applyDateSelection() {
    const start = formatDate(selectedStartDate);
    const end = formatDate(selectedEndDate);
    
    document.getElementById('hiddenStartDate').value = start;
    document.getElementById('hiddenEndDate').value = end;
    
    const displayElement = document.getElementById('dateRangeDisplay');
    if (displayElement) {
      displayElement.textContent = `${start} to ${end}`;
    }
    
    closeDatePicker();
  }

  function renderCalendars() {
    renderCalendar('calendar1', currentMonth1);
    renderCalendar('calendar2', currentMonth2);
    updateDateDisplay();
  }

  function renderCalendar(elementId, month) {
    const calendar = document.getElementById(elementId);
    if (!calendar) return;

    const year = month.getFullYear();
    const monthNum = month.getMonth();
    const firstDay = new Date(year, monthNum, 1);
    const lastDay = new Date(year, monthNum + 1, 0);
    const prevLastDay = new Date(year, monthNum, 0);
    
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const weekdays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
    
    let html = `
      <div class="calendar-month">${monthNames[monthNum]} ${year}</div>
      <div class="calendar-weekdays">
        ${weekdays.map(day => `<div class="weekday">${day}</div>`).join('')}
      </div>
      <div class="calendar-days">
    `;
    
    const firstDayOfWeek = firstDay.getDay();
    for (let i = firstDayOfWeek - 1; i >= 0; i--) {
      const day = prevLastDay.getDate() - i;
      html += `<button type="button" class="calendar-day other-month" disabled>${day}</button>`;
    }
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    for (let day = 1; day <= lastDay.getDate(); day++) {
      const date = new Date(year, monthNum, day);
      date.setHours(0, 0, 0, 0);
      
      const dateStr = formatDate(date);
      let classes = 'calendar-day';
      
      if (isSameDay(date, today)) classes += ' today';
      if (date > today) classes += ' disabled';
      
      if (selectedStartDate && selectedEndDate) {
        if (isSameDay(date, selectedStartDate)) {
          classes += ' selected range-start';
        } else if (isSameDay(date, selectedEndDate)) {
          classes += ' selected range-end';
        } else if (date > selectedStartDate && date < selectedEndDate) {
          classes += ' in-range';
        }
      }
      
      const disabled = date > today ? 'disabled' : '';
      html += `<button type="button" class="calendar-day ${classes}" data-date="${dateStr}" ${disabled}>${day}</button>`;
    }
    
    const lastDayOfWeek = lastDay.getDay();
    for (let i = 1; i < 7 - lastDayOfWeek; i++) {
      html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    }
    
    html += '</div>';
    calendar.innerHTML = html;
    
    calendar.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => {
      btn.addEventListener('click', handleDateClick);
    });
  }

  function handleDateClick(e) {
    const dateStr = e.target.dataset.date;
    const date = new Date(dateStr);
    date.setHours(0, 0, 0, 0);
    
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    const customPreset = document.querySelector('[data-preset="custom"]');
    if (customPreset) customPreset.classList.add('active');
    
    if (selectingStart || date < selectedStartDate) {
      selectedStartDate = date;
      selectedEndDate = date;
      selectingStart = false;
    } else {
      if (date >= selectedStartDate) {
        selectedEndDate = date;
      } else {
        selectedEndDate = selectedStartDate;
        selectedStartDate = date;
      }
      selectingStart = true;
    }
    
    updateDateDisplay();
    renderCalendars();
  }

  function updateDateDisplay() {
    if (!selectedStartDate || !selectedEndDate) return;
    
    const start = formatDate(selectedStartDate);
    const end = formatDate(selectedEndDate);
    
    const displayElement = document.getElementById('selectedRangeText');
    if (displayElement) {
      displayElement.textContent = `${start} to ${end}`;
    }
  }

  function formatDate(date) {
    if (!date) return '';
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  function isSameDay(date1, date2) {
    if (!date1 || !date2) return false;
    
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
  }
})();

// ========================================
// TRENDING TOPICS LOGIC
// ========================================
const TrendingLoader = {
    startDate: '{{ $startDate ?? "" }}',
    endDate: '{{ $endDate ?? "" }}',
    location: '{{ $location ?? "Indonesia" }}',
    trendingData: null,
    allTopics: [],
    currentPage: 1,
    topicsPerPage: 20,

    async init() {
        console.log('🚀 TrendingLoader init started');
        console.log('📅 Date Range:', this.startDate, 'to', this.endDate);
        console.log('📍 Location:', this.location);
        
        try {
            await this.loadData();
        } catch (error) {
            console.error('❌ Failed to load trending data:', error);
            this.showError();
        }
    },

    async loadData() {
        const url = `/mk/api/x/trending-topics?start_date=${this.startDate}&end_date=${this.endDate}&location=${this.location}`;
        
        console.log('🌐 Fetching from URL:', url);
        
        try {
            const response = await fetch(url);
            console.log('📡 Response status:', response.status);
            
            const result = await response.json();
            console.log('📊 Full API response:', result);

            if (!result.success) {
                console.error('❌ API returned success=false:', result.error);
                throw new Error(result.error || 'Failed to load data');
            }

            this.trendingData = result.data;
            this.allTopics = this.trendingData.top_topics || [];
            
            console.log('💾 Data stored:', {
                totalTopics: this.allTopics.length,
                totalPeriods: this.trendingData.total_periods,
                uniqueTopics: this.trendingData.total_unique_topics
            });
            
            this.updateStats();
            this.renderTable();
        } catch (error) {
            console.error('💥 Error in loadData:', error);
            throw error;
        }
    },

    updateStats() {
        const data = this.trendingData;
        
        const totalPeriods = data.total_periods || 0;
        const uniqueTopics = data.total_unique_topics || 0;
        const avgTopics = totalPeriods > 0 ? Math.round(uniqueTopics / totalPeriods) : 0;

        console.log('📊 Stats:', { totalPeriods, uniqueTopics, avgTopics });

        document.getElementById('totalPeriods').textContent = totalPeriods;
        document.getElementById('uniqueTopics').textContent = uniqueTopics;
        document.getElementById('avgTopics').textContent = avgTopics;
    },

    renderTable() {
        const loadingTable = document.getElementById('loadingTable');
        const trendingTable = document.getElementById('trendingTable');
        const emptyState = document.getElementById('emptyState');
        if (!this.allTopics || !this.allTopics.length) {
            console.warn('⚠️ No topics to display - showing empty state');
            loadingTable.style.display = 'none';
            trendingTable.style.display = 'none';
            emptyState.style.display = 'block';
            document.getElementById('paginationWrapper').style.display = 'none';
            return;
        }

        const startIdx = (this.currentPage - 1) * this.topicsPerPage;
        const endIdx = startIdx + this.topicsPerPage;
        const currentTopics = this.allTopics.slice(startIdx, endIdx);

        const tbody = document.getElementById('trendingTableBody');
        tbody.innerHTML = currentTopics.map((topic, idx) => this.createTableRow(topic, startIdx + idx + 1)).join('');

        loadingTable.style.display = 'none';
        trendingTable.style.display = 'table';
        emptyState.style.display = 'none';

        // Update pagination
        this.updatePagination();
        
      
        
        console.log('✅ Table rendered successfully', {
            totalTopics: this.allTopics.length,
            currentPage: this.currentPage,
            showing: currentTopics.length,
            totalPages: totalPages
        });
    },

    createTableRow(topic, rank) {
        const name = this.escapeHtml(topic.name);
        const appearances = topic.appearances || 0;
        const avgRank = topic.avg_rank || 0;
        const url = topic.url || '#';

        return `
            <tr>
                <td class="rank-cell">#${rank}</td>
                
                <td class="topic-cell">${name}</td>
                
                <td class="appearances-cell">${appearances}</td>
                
                <td class="rank-avg-cell">${avgRank}</td>
                
                <td class="action-cell">
                    ${url !== '#' ? `
                    <a href="${url}" target="_blank" class="btn-view">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        View
                    </a>
                    ` : '<span style="color: var(--text-muted); font-size: 11px; font-weight: 500;">—</span>'}
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
    const totalPages = Math.ceil(this.allTopics.length / this.topicsPerPage);
    const wrapper = document.getElementById('paginationWrapper');
    const from = this.allTopics.length ? (this.currentPage - 1) * this.topicsPerPage + 1 : 0;
    const to   = Math.min(this.currentPage * this.topicsPerPage, this.allTopics.length);

    let html = `<div class="pagination-info">Showing ${from}–${to} of ${this.allTopics.length} topics</div>`;
    html += `<div style="display:flex;align-items:center;gap:6px;">`;
    html += `<button class="page-btn" onclick="TrendingLoader.changePage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''}>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
    </button>`;

    this.getPageRange(this.currentPage, totalPages).forEach(p => {
        html += p === '...'
            ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
            : `<button class="page-btn ${p === this.currentPage ? 'active' : ''}" onclick="TrendingLoader.changePage(${p})">${p}</button>`;
    });

    html += `<button class="page-btn" onclick="TrendingLoader.changePage(${this.currentPage + 1})" ${this.currentPage === totalPages ? 'disabled' : ''}>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
    </button></div>`;

    wrapper.innerHTML = html;
    wrapper.style.display = this.allTopics.length > 0 ? 'flex' : 'none';
},

changePage(p) {
    const totalPages = Math.ceil(this.allTopics.length / this.topicsPerPage);
    if (p < 1 || p > totalPages) return;
    this.currentPage = p;
    this.renderTable();
    document.querySelector('.table-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
},

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

   
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('🎬 DOM Content Loaded - Starting TrendingLoader');
    TrendingLoader.init();
});
</script>
@endsection