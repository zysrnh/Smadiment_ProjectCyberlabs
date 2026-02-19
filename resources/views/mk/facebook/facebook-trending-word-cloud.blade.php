@extends('mk.layouts.app')

@section('title', 'Facebook Trending Word Cloud - SMADIMENT')

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

    /* Date Picker Trigger */
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

    .date-picker-overlay-inner {
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
        animation: slideUpModal 0.3s ease-out;
    }

    @keyframes slideUpModal {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
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
        align-items: flex-start;
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

    .sentiment-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sentiment-btn {
        padding: 10px 20px;
        background: var(--bg-white);
        border: 2px solid var(--border-gray);
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sentiment-btn:hover {
        border-color: var(--primary-green);
        color: var(--text-primary);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .sentiment-btn.active {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        border-color: var(--primary-green);
        color: white;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }

    .sentiment-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .wordcloud-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        border: 1px solid var(--border-gray);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        padding: 48px;
        min-height: 700px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .wordcloud-container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(3, 128, 71, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(47, 198, 246, 0.03) 0%, transparent 50%);
        pointer-events: none;
    }

    .wordcloud-hint {
        position: absolute;
        bottom: 16px;
        right: 20px;
        font-size: 11px;
        color: var(--text-muted);
        font-style: italic;
        display: none;
        align-items: center;
        gap: 5px;
        z-index: 2;
        pointer-events: none;
    }

    .wordcloud-hint svg {
        width: 13px;
        height: 13px;
        stroke: currentColor;
        fill: none;
        flex-shrink: 0;
    }

    #wordCloudChart {
        width: 100% !important;
        height: 700px !important;
        position: relative;
        z-index: 1;
        cursor: pointer;
    }

    .loading-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 80px 20px;
    }

    .loading-spinner {
        width: 48px;
        height: 48px;
        border: 4px solid var(--bg-gray-100);
        border-top-color: var(--primary-green);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .loading-text {
        font-size: 14px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
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

    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-range-wrapper { flex-direction: column; }
        .apply-btn { width: 100%; justify-content: center; }
        .sentiment-filters { width: 100%; }
        .sentiment-btn { flex: 1; justify-content: center; }
        #wordCloudChart { height: 500px !important; }
        .wordcloud-container { padding: 24px; min-height: 550px; }

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

        .date-preset { white-space: nowrap; }

        .date-picker-content { padding: 20px 16px; }

        .calendars-wrapper {
            flex-direction: column;
            gap: 16px;
        }

        .date-picker-header { flex-wrap: wrap; }

        .date-picker-trigger { max-width: 100%; }

        .calendar-day { font-size: 12px; }
        .weekday { font-size: 10px; }

        .cancel-btn,
        .apply-date-btn { flex: 1; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <h1>Facebook Trending Word Cloud</h1>
        <p>Visual representation of trending hashtags &amp; topics on Facebook</p>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.facebook.trending-word-cloud') }}">
            <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
            <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">

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
        <div class="date-picker-overlay-inner"></div>
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

    <!-- Sentiment Filter Card -->
    <div class="filter-card">
        <div class="filter-content">
            <div class="filter-label">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                    <line x1="9" y1="9" x2="9.01" y2="9"/>
                    <line x1="15" y1="9" x2="15.01" y2="9"/>
                </svg>
                Sentiment Filter
            </div>
            <div class="sentiment-filters">
                <button type="button" class="sentiment-btn active" data-sentiment="all">
                    <span class="sentiment-dot" style="background: linear-gradient(135deg, #038047, #2FC6F6);"></span>
                    All Topics
                </button>
                <button type="button" class="sentiment-btn" data-sentiment="positive">
                    <span class="sentiment-dot" style="background: #10b981;"></span>
                    Positive
                </button>
                <button type="button" class="sentiment-btn" data-sentiment="neutral">
                    <span class="sentiment-dot" style="background: #f59e0b;"></span>
                    Neutral
                </button>
                <button type="button" class="sentiment-btn" data-sentiment="negative">
                    <span class="sentiment-dot" style="background: #ef4444;"></span>
                    Negative
                </button>
            </div>
        </div>
    </div>

    <!-- Word Cloud Container -->
    <div class="wordcloud-container">

        <div id="loadingState" class="loading-state">
            <div class="loading-spinner"></div>
            <div class="loading-text" id="loadingText">Loading Facebook trending topics data...</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 8px;" id="loadingProgress"></div>
        </div>

        <div id="wordCloudChart" style="display: none;"></div>

        <div class="wordcloud-hint" id="wordCloudHint">
            <svg viewBox="0 0 24 24">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
            Click a word to search on Facebook
        </div>

        <div id="emptyState" class="empty-state" style="display: none;">
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts-wordcloud@2.1.0/dist/echarts-wordcloud.min.js"></script>

<script>
// ========================================
// DATE PICKER JAVASCRIPT
// ========================================
(function() {
    'use strict';

    let selectedStartDate = null;
    let selectedEndDate   = null;
    let currentMonth1     = new Date();
    let currentMonth2     = new Date();
    let selectingStart    = true;

    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('hiddenStartDate');
        const endDateInput   = document.getElementById('hiddenEndDate');

        if (startDateInput && startDateInput.value) {
            selectedStartDate = new Date(startDateInput.value);
        } else {
            selectedStartDate = new Date();
            selectedStartDate.setDate(selectedStartDate.getDate() - 6);
        }

        if (endDateInput && endDateInput.value) {
            selectedEndDate = new Date(endDateInput.value);
        } else {
            selectedEndDate = new Date();
        }

        currentMonth1 = new Date(selectedStartDate);
        currentMonth2 = new Date(selectedStartDate);
        currentMonth2.setMonth(currentMonth2.getMonth() + 1);

        renderCalendars();
        setupEventListeners();
    });

    function setupEventListeners() {
        const trigger = document.getElementById('datePickerTrigger');
        if (trigger) trigger.addEventListener('click', openDatePicker);

        const overlay = document.querySelector('.date-picker-overlay-inner');
        if (overlay) overlay.addEventListener('click', closeDatePicker);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('datePickerModal');
                if (modal && modal.classList.contains('show')) closeDatePicker();
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
        if (applyBtn) applyBtn.addEventListener('click', applyDateSelection);

        const cancelBtn = document.querySelector('.cancel-btn');
        if (cancelBtn) cancelBtn.addEventListener('click', closeDatePicker);
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
        const today  = new Date();
        today.setHours(0, 0, 0, 0);

        switch (preset) {
            case 'today':
                selectedStartDate = new Date(today);
                selectedEndDate   = new Date(today);
                break;
            case 'yesterday':
                selectedStartDate = new Date(today);
                selectedStartDate.setDate(today.getDate() - 1);
                selectedEndDate = new Date(selectedStartDate);
                break;
            case 'last7days':
                selectedEndDate   = new Date(today);
                selectedStartDate = new Date(today);
                selectedStartDate.setDate(today.getDate() - 6);
                break;
            case 'last30days':
                selectedEndDate   = new Date(today);
                selectedStartDate = new Date(today);
                selectedStartDate.setDate(today.getDate() - 29);
                break;
            case 'thismonth':
                selectedStartDate = new Date(today.getFullYear(), today.getMonth(), 1);
                selectedEndDate   = new Date(today);
                break;
            case 'lastmonth':
                selectedStartDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                selectedEndDate   = new Date(today.getFullYear(), today.getMonth(), 0);
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
        const end   = formatDate(selectedEndDate);

        document.getElementById('hiddenStartDate').value = start;
        document.getElementById('hiddenEndDate').value   = end;

        const displayEl = document.getElementById('dateRangeDisplay');
        if (displayEl) displayEl.textContent = `${start} to ${end}`;

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

        const year     = month.getFullYear();
        const monthNum = month.getMonth();
        const firstDay = new Date(year, monthNum, 1);
        const lastDay  = new Date(year, monthNum + 1, 0);
        const prevLastDay = new Date(year, monthNum, 0);

        const monthNames = ['January','February','March','April','May','June',
                            'July','August','September','October','November','December'];
        const weekdays   = ['Su','Mo','Tu','We','Th','Fr','Sa'];

        let html = `
            <div class="calendar-month">${monthNames[monthNum]} ${year}</div>
            <div class="calendar-weekdays">
                ${weekdays.map(d => `<div class="weekday">${d}</div>`).join('')}
            </div>
            <div class="calendar-days">
        `;

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const firstDayOfWeek = firstDay.getDay();
        for (let i = 0; i < firstDayOfWeek; i++) {
            const d = prevLastDay.getDate() - (firstDayOfWeek - 1 - i);
            html += `<button type="button" class="calendar-day other-month" disabled>${d}</button>`;
        }

        for (let day = 1; day <= lastDay.getDate(); day++) {
            const date = new Date(year, monthNum, day);
            date.setHours(0, 0, 0, 0);

            const dateStr = formatDate(date);
            let classes = 'calendar-day';

            if (isSameDay(date, today)) classes += ' today';
            if (date > today)           classes += ' disabled';

            if (selectedStartDate && selectedEndDate) {
                if (isSameDay(date, selectedStartDate))                          classes += ' selected range-start';
                else if (isSameDay(date, selectedEndDate))                       classes += ' selected range-end';
                else if (date > selectedStartDate && date < selectedEndDate)     classes += ' in-range';
            }

            const disabled = date > today ? 'disabled' : '';
            html += `<button type="button" class="${classes}" data-date="${dateStr}" ${disabled}>${day}</button>`;
        }

        const lastDayOfWeek  = lastDay.getDay();
        const remainingCells = lastDayOfWeek === 6 ? 0 : 6 - lastDayOfWeek;
        for (let i = 1; i <= remainingCells; i++) {
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
        const date    = new Date(dateStr);
        date.setHours(0, 0, 0, 0);

        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        const customPreset = document.querySelector('[data-preset="custom"]');
        if (customPreset) customPreset.classList.add('active');

        if (selectingStart || date < selectedStartDate) {
            selectedStartDate = date;
            selectedEndDate   = date;
            selectingStart    = false;
        } else {
            if (date >= selectedStartDate) {
                selectedEndDate = date;
            } else {
                selectedEndDate   = selectedStartDate;
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
        const end   = formatDate(selectedEndDate);

        const el = document.getElementById('selectedRangeText');
        if (el) el.textContent = `${start} to ${end}`;
    }

    function formatDate(date) {
        if (!date) return '';
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function isSameDay(d1, d2) {
        if (!d1 || !d2) return false;
        return d1.getFullYear() === d2.getFullYear() &&
               d1.getMonth()    === d2.getMonth()    &&
               d1.getDate()     === d2.getDate();
    }
})();

// ========================================
// FACEBOOK WORD CLOUD GENERATOR
// ========================================
const WordCloudGenerator = {
    startDate: '{{ $startDate ?? "" }}',
    endDate:   '{{ $endDate ?? "" }}',
    projectId: '{{ $projectId ?? "" }}',
    trendingData:     null,
    currentSentiment: 'all',
    chart: null,

    NEGATIVE_KEYWORDS: [
        'bad','worst','hate','hated','sad','fail','failed','failure',
        'lose','lost','loss','angry','anger','terrible','awful',
        'poor','dead','death','die','died','dies','kill','killed','killing',
        'corrupt','corruption','crime','criminal','fraud','scam',
        'lie','lies','liar','cheat','cheating','cheater',
        'abuse','abuser','abusive','terror','terrorist','terrorism',
        'attack','attacked','war','riot','rioting','scandal',
        'boycott','crisis','disaster','catastrophe',
        'wrong','broken','hurt','pain','suffer','suffering',
        'injustice','unfair','illegal','violence','violent',
        'racist','racism','bully','bullying','harassment','harass',
        'threat','threaten','danger','dangerous','emergency',
        'victim','bankrupt','poverty','hunger','famine',
        'shooting','murdered','murder','arrested','arrest',
        'fired','layoff','layoffs','resign','resignation',
        'buruk','terburuk','benci','sedih','gagal','kalah',
        'marah','parah','miskin','mati','maut','bunuh','tewas',
        'korupsi','korup','kejahatan','kriminal','penipuan','tipu',
        'bohong','curang','kekerasan','serang','perang','rusuh',
        'skandal','boikot','krisis','bencana','musibah',
        'salah','rusak','sakit','derita','menderita',
        'ilegal','rasis','rasisme','ancaman','bahaya','berbahaya',
        'darurat','korban','bangkrut','kemiskinan','kelaparan',
        'tersangka','terdakwa','penjara','ditangkap','tangkap',
        'pecat','dipecat','mengundurkan','narkoba','narkotika',
        'meninggal','wafat','terbunuh','dibunuh','penembakan',
        'kebakaran','banjir','gempa','longsor','kecelakaan',
    ],

    NEGATIVE_PHRASES: [
        'tidak adil','tidak beres','tidak bisa','tidak mampu',
        'tidak aman','unjuk rasa','demo besar','huru hara',
        'kasus korupsi','dugaan korupsi','terseret kasus',
        'ditangkap polisi','diciduk polisi','dicokok polisi',
    ],

    POSITIVE_KEYWORDS: [
        'win','won','winner','best','good','great','love',
        'happy','success','successful','amazing','excellent',
        'awesome','celebrate','celebration','proud','pride',
        'champion','victory','achieve','achievement',
        'congratulations','congrats','hope','inspire','inspiration',
        'wonderful','beautiful','brilliant','fantastic','superb',
        'legend','hero','heroic','progress','growth','improve',
        'menang','juara','terbaik','baik','bagus','cinta',
        'senang','sukses','berhasil','hebat','keren',
        'rayakan','bangga','kemenangan','prestasi','selamat',
        'harapan','inspirasi','positif','indah','cemerlang',
        'fantastis','legenda','pahlawan','kemajuan','merdeka',
        'damai','harmonis','aman','sejahtera',
    ],

    getSentimentFromTopic(topicName) {
        const lower  = topicName.toLowerCase().replace(/^#/, '').trim();
        const tokens = lower.split(/[^a-z0-9]+/).filter(t => t.length > 0);

        for (const kw of this.NEGATIVE_KEYWORDS) {
            if (tokens.includes(kw)) return 'negative';
        }
        for (const phrase of this.NEGATIVE_PHRASES) {
            if (lower.includes(phrase)) return 'negative';
        }
        for (const kw of this.POSITIVE_KEYWORDS) {
            if (tokens.includes(kw)) return 'positive';
        }

        return 'neutral';
    },

    getSentimentColor() {
        const colorSchemes = {
            positive: ['#10b981','#059669','#34d399','#6ee7b7','#047857'],
            negative: ['#ef4444','#dc2626','#b91c1c','#f87171','#c53030'],
            neutral:  ['#f59e0b','#d97706','#fbbf24','#b45309','#fcd34d'],
            all:      ['#038047','#04995a','#2FC6F6','#06b6d4','#8b5cf6',
                       '#a78bfa','#f59e0b','#fbbf24','#10b981','#34d399',
                       '#ef4444','#f87171'],
        };
        return colorSchemes[this.currentSentiment] || colorSchemes.all;
    },

    openFacebookSearch(topicName) {
        const query = encodeURIComponent(topicName);
        window.open(
            `https://www.facebook.com/search/posts?q=${query}`,
            '_blank',
            'noopener,noreferrer'
        );
    },

    async init() {
        if (typeof echarts === 'undefined') {
            this.showError('ECharts library failed to load');
            return;
        }
        this.initSentimentFilters();
        try {
            await this.loadData();
        } catch (error) {
            console.error('Failed to load Facebook trending data:', error);
            this.showError('Failed to load data');
        }
    },

    initSentimentFilters() {
        const buttons = document.querySelectorAll('.sentiment-btn');
        buttons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.currentSentiment = btn.dataset.sentiment;
                this.generateWordCloud();
            });
        });
    },

    async loadData() {
        const loadingText     = document.getElementById('loadingText');
        const loadingProgress = document.getElementById('loadingProgress');

        if (!this.projectId) {
            this.showError('No project selected. Please select a project first.');
            return;
        }

        const url = `/mk/api/facebook/trending-topics?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`;

        loadingText.textContent = 'Fetching data from server...';

        const startTime = Date.now();
        const response  = await fetch(url);

        loadingText.textContent = 'Processing Facebook trending hashtags...';

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.error || 'Failed to load data');
        }

        const rawData  = result.data || {};
        const hashtags = rawData.hashtags || [];

        const topTopics = hashtags.map(h => ({
            name:         h.hashtag || h.name || '',
            total_volume: h.size    || 0,
            appearances:  1,
            avg_rank:     0,
            url:          '',
            sentiment:    this.getSentimentFromTopic(h.name || h.hashtag || ''),
        }));

        topTopics.sort((a, b) => b.total_volume - a.total_volume);

        this.trendingData = { top_topics: topTopics };

        const loadTime = ((Date.now() - startTime) / 1000).toFixed(1);

        loadingText.textContent     = 'Generating word cloud...';
        loadingProgress.textContent = `Loaded ${topTopics.length} hashtags in ${loadTime}s`;

        await new Promise(resolve => setTimeout(resolve, 200));
        this.generateWordCloud();
    },

    generateWordCloud() {
        const loadingState = document.getElementById('loadingState');
        const chartDiv     = document.getElementById('wordCloudChart');
        const emptyState   = document.getElementById('emptyState');
        const hintEl       = document.getElementById('wordCloudHint');

        let topics = (this.trendingData && this.trendingData.top_topics) ? [...this.trendingData.top_topics] : [];

        if (this.currentSentiment !== 'all') {
            topics = topics.filter(t => this.getSentimentFromTopic(t.name) === this.currentSentiment);
        }

        if (!topics.length) {
            loadingState.style.display = 'none';
            chartDiv.style.display     = 'none';
            hintEl.style.display       = 'none';
            emptyState.style.display   = 'block';

            const label = this.currentSentiment === 'all'
                ? ''
                : this.currentSentiment.charAt(0).toUpperCase() + this.currentSentiment.slice(1) + ' ';

            emptyState.innerHTML = `
                <svg viewBox="0 0 24 24" style="width:64px;height:64px;stroke:currentColor;fill:none;margin-bottom:16px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <h3>No ${label}Topics Found</h3>
                <p>Try selecting a different sentiment filter or date range.</p>
            `;
            return;
        }

        if (topics.length > 100) topics = topics.slice(0, 100);

        const wordData = topics.map(topic => ({
            name:          topic.name.replace(/^#/, ''),
            value:         topic.total_volume || (topic.appearances * 100) || 100,
            originalTopic: topic,
        }));

        loadingState.style.display = 'none';
        chartDiv.style.display     = 'block';
        emptyState.style.display   = 'none';
        hintEl.style.display       = 'flex';

        if (this.chart) this.chart.dispose();

        this.chart = echarts.init(chartDiv, null, {
            renderer: 'canvas',
            devicePixelRatio: window.devicePixelRatio || 1,
        });

        const colors = this.getSentimentColor();

        const option = {
            tooltip: {
                show: true,
                trigger: 'item',
                backgroundColor: '#ffffff',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                textStyle: { color: '#1a202c', fontSize: 13, fontFamily: 'Poppins, sans-serif' },
                padding: 16,
                shadowBlur: 20,
                shadowColor: 'rgba(0,0,0,0.15)',
                shadowOffsetY: 4,
                formatter: (params) => {
                    const topic     = params.data.originalTopic;
                    const sentiment = this.getSentimentFromTopic(topic.name);
                    const sentimentColors = {
                        positive: '#22c55e',
                        negative: '#ef4444',
                        neutral:  '#94a3b8',
                    };
                    const color = sentimentColors[sentiment];
                    const label = sentiment.charAt(0).toUpperCase() + sentiment.slice(1);
                    const volume = (topic.total_volume || 0).toLocaleString();
                    return `
                        <div style="font-family:Poppins,sans-serif;min-width:200px;">
                            <div style="font-weight:700;font-size:15px;color:#1a202c;margin-bottom:8px;text-align:center;">
                                #${params.name}
                            </div>
                            <div style="display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:8px;">
                                <span style="font-size:12px;color:#64748b;">Mentions:</span>
                                <span style="font-size:13px;font-weight:700;color:#1a202c;">${volume}</span>
                            </div>
                            <div style="padding:4px 12px;background:${color}20;border-radius:12px;margin-bottom:10px;text-align:center;">
                                <span style="font-size:10px;font-weight:700;color:${color};text-transform:uppercase;">
                                    ${label}
                                </span>
                            </div>
                            <div style="font-size:11px;color:#94a3b8;text-align:center;">
                                Click to search on Facebook
                            </div>
                        </div>
                    `;
                },
            },
            series: [{
                type: 'wordCloud',
                shape: 'circle',
                keepAspect: false,
                left: 'center',
                top: 'center',
                width: '90%',
                height: '90%',
                right: null,
                bottom: null,
                sizeRange: [16, 70],
                rotationRange: [-45, 45],
                rotationStep: 45,
                gridSize: 12,
                drawOutOfBound: false,
                layoutAnimation: true,
                textStyle: {
                    fontFamily: 'Poppins, Inter, sans-serif',
                    fontWeight: 'bold',
                    color: () => colors[Math.floor(Math.random() * colors.length)],
                },
                emphasis: {
                    focus: 'self',
                    textStyle: {
                        textShadowBlur: 10,
                        textShadowColor: 'rgba(0,0,0,0.35)',
                    },
                },
                data: wordData,
            }],
        };

        setTimeout(() => {
            this.chart.setOption(option, true);

            this.chart.on('click', (params) => {
                if (params && params.data && params.data.originalTopic) {
                    this.openFacebookSearch(params.data.originalTopic.name);
                }
            });
        }, 10);

        let resizeTimer;
        const handleResize = () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => { if (this.chart) this.chart.resize(); }, 250);
        };
        window.removeEventListener('resize', handleResize);
        window.addEventListener('resize', handleResize);
    },

    showError(message = 'Failed to load data') {
        document.getElementById('loadingState').style.display = 'none';
        const emptyState = document.getElementById('emptyState');
        emptyState.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" style="color:#ef4444;width:64px;height:64px;stroke:currentColor;fill:none;margin-bottom:16px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <h3>Failed to Load Data</h3>
                <p>${message}. Please try again later.</p>
            </div>`;
        emptyState.style.display = 'block';
    },
};

document.addEventListener('DOMContentLoaded', () => { WordCloudGenerator.init(); });
</script>
@endsection