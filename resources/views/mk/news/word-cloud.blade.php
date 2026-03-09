@extends('mk.layouts.app')

@section('title', 'News Word Cloud - SMADIMENT')

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

    /* Stats Grid - always 4 columns */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-green);
    }

    .stat-card:hover::before { opacity: 1; }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .stat-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(3, 128, 71, 0.1) 0%, rgba(3, 128, 71, 0.05) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .stat-icon-wrapper::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 16px;
        padding: 4px;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stat-card:hover .stat-icon-wrapper::after { opacity: 0.5; }

    .stat-icon {
        width: 28px;
        height: 28px;
        color: var(--primary-green);
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value-wrapper {
        display: flex;
        align-items: baseline;
        gap: 12px;
        margin-bottom: 16px;
    }

    .stat-value {
        font-size: 36px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-progress {
        height: 6px;
        background: var(--bg-gray-100);
        border-radius: 10px;
        overflow: hidden;
        margin-top: 8px;
    }

    .stat-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        border-radius: 10px;
        transition: width 1s ease-out;
        width: 0%;
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
        width: 18px; height: 18px;
        color: var(--text-secondary);
        flex-shrink: 0;
    }

    .date-picker-trigger span { flex: 1; text-align: left; }

    .date-picker-trigger svg:last-child {
        width: 16px; height: 16px;
        margin-left: auto;
        color: var(--text-secondary);
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
        width: 18px; height: 18px;
        stroke: currentColor; fill: none;
    }

    /* Date Picker Modal */
    .date-picker-modal {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
    }

    .date-picker-modal.show { display: flex; }

    .date-picker-overlay-inner {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
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
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

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

    .date-preset:hover { background: var(--bg-white); color: var(--primary-green); }
    .date-preset.active { background: var(--primary-green); color: white; }

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
        width: 36px; height: 36px;
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

    .nav-btn svg { width: 20px; height: 20px; }

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

    .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
    .calendar-day.other-month { color: #cbd5e1; cursor: default; }
    .calendar-day.disabled { color: #e2e8f0; cursor: not-allowed; }
    .calendar-day.today { border: 2px solid var(--primary-green); }
    .calendar-day.selected { background: var(--primary-green); color: white; }
    .calendar-day.in-range { background: rgba(3, 128, 71, 0.1); color: var(--primary-green); }
    .calendar-day.range-start, .calendar-day.range-end { background: var(--primary-green); color: white; }

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

    .date-picker-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .cancel-btn, .apply-date-btn {
        padding: 10px 24px;
        border-radius: 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
    .cancel-btn:hover { background: var(--border-gray); }

    .apply-date-btn {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }

    .apply-date-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
    }

    /* Sentiment Filter */
    .sentiment-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .sentiment-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        background: var(--bg-white);
        border: 2px solid var(--border-gray);
        border-radius: 50px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
    }

    .sentiment-btn:hover {
        border-color: var(--primary-green);
        color: var(--text-primary);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .sentiment-btn.active {
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        transform: translateY(-1px);
    }

    .sentiment-btn[data-sentiment="2"].active  { background: linear-gradient(135deg, #038047, #026738); }
    .sentiment-btn[data-sentiment="0"].active  { background: linear-gradient(135deg, #10b981, #059669); }
    .sentiment-btn[data-sentiment="1"].active  { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .sentiment-btn[data-sentiment="-1"].active { background: linear-gradient(135deg, #ef4444, #dc2626); }

    .sentiment-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Word Cloud */
    .wordcloud-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        border: 1px solid var(--border-gray);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        min-height: 800px;
        height: 800px;
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
        width: 13px; height: 13px;
        stroke: currentColor; fill: none;
        flex-shrink: 0;
    }

    #wordCloudChart {
        width: 100% !important;
        height: 100% !important;
        position: absolute;
        top: 0; left: 0;
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
        width: 48px; height: 48px;
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
        width: 64px; height: 64px;
        color: var(--text-secondary);
        margin-bottom: 16px;
        stroke: currentColor; fill: none;
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

    /* Responsive */
    @media (max-width: 1024px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
    }

    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-range-wrapper { flex-direction: column; }
        .apply-btn { width: 100%; justify-content: center; }
        .sentiment-filters { width: 100%; }
        .sentiment-btn { flex: 1; justify-content: center; }
        #wordCloudChart { height: 100% !important; }
        .wordcloud-container { min-height: 600px; height: 600px; }
        .stat-value { font-size: 28px; }

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
        .calendars-wrapper { flex-direction: column; gap: 16px; }
        .date-picker-header { flex-wrap: wrap; }
        .date-picker-trigger { max-width: 100%; }
        .calendar-day { font-size: 12px; }
        .weekday { font-size: 10px; }
        .cancel-btn, .apply-date-btn { flex: 1; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <h1>News Word Cloud</h1>
        <p>Visual representation of trending words in news mentions</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon-wrapper">
                    <svg class="stat-icon" viewBox="0 0 24 24">
                        <path d="M4 7h16M4 12h16M4 17h10"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Total Words</div>
            <div id="totalWords" class="stat-value-wrapper">
                <div class="stat-value">-</div>
            </div>
            <div class="stat-progress"><div class="stat-progress-bar"></div></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon-wrapper">
                    <svg class="stat-icon" viewBox="0 0 24 24">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Top Word</div>
            <div id="topWord" class="stat-value-wrapper">
                <div class="stat-value" style="font-size: 20px;">-</div>
            </div>
            <div class="stat-progress"><div class="stat-progress-bar"></div></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon-wrapper">
                    <svg class="stat-icon" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Total Mentions</div>
            <div id="totalMentions" class="stat-value-wrapper">
                <div class="stat-value">-</div>
            </div>
            <div class="stat-progress"><div class="stat-progress-bar"></div></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon-wrapper">
                    <svg class="stat-icon" viewBox="0 0 24 24">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Avg per Word</div>
            <div id="avgMentions" class="stat-value-wrapper">
                <div class="stat-value">-</div>
            </div>
            <div class="stat-progress"><div class="stat-progress-bar"></div></div>
        </div>
    </div>

    <!-- Filters: Date + Sentiment in one card -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.news.word-cloud') }}">
            <input type="hidden" name="project_id" value="{{ $projectId }}">
            <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
            <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">

            <div class="filter-content">
                <div class="filter-label" style="display:flex;align-items:center;gap:6px;">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;flex-shrink:0;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Date
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

                <div style="width:1px;height:32px;background:var(--border-gray);flex-shrink:0;"></div>

                <div class="filter-label" style="display:flex;align-items:center;gap:6px;">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;flex-shrink:0;">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                        <line x1="9" y1="9" x2="9.01" y2="9"/>
                        <line x1="15" y1="9" x2="15.01" y2="9"/>
                    </svg>
                    Sentiment
                </div>
                <div class="sentiment-filters">
                    <button type="button" class="sentiment-btn active" data-sentiment="2">
                        <span class="sentiment-dot" style="background:linear-gradient(135deg,#038047,#2FC6F6);"></span>
                        All
                    </button>
                    <button type="button" class="sentiment-btn" data-sentiment="0">
                        <span class="sentiment-dot" style="background:#10b981;"></span>
                        Positive
                    </button>
                    <button type="button" class="sentiment-btn" data-sentiment="1">
                        <span class="sentiment-dot" style="background:#f59e0b;"></span>
                        Neutral
                    </button>
                    <button type="button" class="sentiment-btn" data-sentiment="-1">
                        <span class="sentiment-dot" style="background:#ef4444;"></span>
                        Negative
                    </button>
                </div>

                <button type="submit" class="apply-btn" style="margin-left:auto;">
                    <svg viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    Apply
                </button>
            </div>
        </form>
    </div>

    <!-- Date Picker Modal -->
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

    <!-- Word Cloud Container -->
    <div class="wordcloud-container">
        <div id="loadingState" class="loading-state">
            <div class="loading-spinner"></div>
            <div class="loading-text" id="loadingText">Loading news word cloud data...</div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 8px;" id="loadingProgress"></div>
        </div>

        <div id="wordCloudChart" style="display: none;"></div>

        <div class="wordcloud-hint" id="wordCloudHint">
            <svg viewBox="0 0 24 24">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
            Click a word to search on Google News
        </div>

        <div id="emptyState" class="empty-state" style="display: none;">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <h3>No News Data Found</h3>
            <p>No news mentions available for the selected date range.</p>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts-wordcloud@2.1.0/dist/echarts-wordcloud.min.js"></script>

<script>
// ─── Date Picker ──────────────────────────────────────────────────────────────
(function () {
    'use strict';

    let selectedStartDate = null;
    let selectedEndDate   = null;
    let currentMonth1     = new Date();
    let currentMonth2     = new Date();
    let selectingStart    = true;

    document.addEventListener('DOMContentLoaded', function () {
        const startInput = document.getElementById('hiddenStartDate');
        const endInput   = document.getElementById('hiddenEndDate');

        selectedStartDate = startInput?.value
            ? new Date(startInput.value)
            : (() => { const d = new Date(); d.setDate(d.getDate() - 29); return d; })();

        selectedEndDate = endInput?.value ? new Date(endInput.value) : new Date();

        currentMonth1 = new Date(selectedStartDate);
        currentMonth2 = new Date(selectedStartDate);
        currentMonth2.setMonth(currentMonth2.getMonth() + 1);

        renderCalendars();
        setupListeners();
    });

    function setupListeners() {
        document.getElementById('datePickerTrigger')?.addEventListener('click', openPicker);
        document.querySelector('.date-picker-overlay-inner')?.addEventListener('click', closePicker);

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && document.getElementById('datePickerModal')?.classList.contains('show')) {
                closePicker();
            }
        });

        document.querySelectorAll('.date-preset').forEach(btn => btn.addEventListener('click', handlePreset));

        document.getElementById('prevMonth')?.addEventListener('click', () => {
            currentMonth1.setMonth(currentMonth1.getMonth() - 1);
            currentMonth2.setMonth(currentMonth2.getMonth() - 1);
            renderCalendars();
        });

        document.getElementById('nextMonth')?.addEventListener('click', () => {
            currentMonth1.setMonth(currentMonth1.getMonth() + 1);
            currentMonth2.setMonth(currentMonth2.getMonth() + 1);
            renderCalendars();
        });

        document.getElementById('applyDatePicker')?.addEventListener('click', applySelection);
        document.querySelector('.cancel-btn')?.addEventListener('click', closePicker);
    }

    function openPicker()  { document.getElementById('datePickerModal').classList.add('show'); renderCalendars(); }
    function closePicker() { document.getElementById('datePickerModal').classList.remove('show'); }

    function handlePreset(e) {
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');

        const preset = e.target.dataset.preset;
        const today  = new Date(); today.setHours(0, 0, 0, 0);

        const presets = {
            today:      [new Date(today), new Date(today)],
            yesterday:  [new Date(today.setDate(today.getDate() - 1)), new Date(today)],
            last7days:  [new Date(new Date().setDate(new Date().getDate() - 6)), new Date()],
            last30days: [new Date(new Date().setDate(new Date().getDate() - 29)), new Date()],
            thismonth:  [new Date(new Date().getFullYear(), new Date().getMonth(), 1), new Date()],
            lastmonth:  [
                new Date(new Date().getFullYear(), new Date().getMonth() - 1, 1),
                new Date(new Date().getFullYear(), new Date().getMonth(), 0),
            ],
        };

        if (presets[preset]) {
            [selectedStartDate, selectedEndDate] = presets[preset];
            currentMonth1 = new Date(selectedStartDate);
            currentMonth2 = new Date(selectedStartDate);
            currentMonth2.setMonth(currentMonth2.getMonth() + 1);
            updateDisplay();
            renderCalendars();
        }
    }

    function applySelection() {
        const start = fmt(selectedStartDate);
        const end   = fmt(selectedEndDate);
        document.getElementById('hiddenStartDate').value = start;
        document.getElementById('hiddenEndDate').value   = end;
        const el = document.getElementById('dateRangeDisplay');
        if (el) el.textContent = `${start} to ${end}`;
        closePicker();
    }

    function renderCalendars() {
        renderCal('calendar1', currentMonth1);
        renderCal('calendar2', currentMonth2);
        updateDisplay();
    }

    function renderCal(id, month) {
        const el = document.getElementById(id);
        if (!el) return;

        const y = month.getFullYear(), m = month.getMonth();
        const first = new Date(y, m, 1);
        const last  = new Date(y, m + 1, 0);
        const prevLast = new Date(y, m, 0);
        const today = new Date(); today.setHours(0, 0, 0, 0);

        const months  = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const wdays   = ['Su','Mo','Tu','We','Th','Fr','Sa'];

        let html = `<div class="calendar-month">${months[m]} ${y}</div>
        <div class="calendar-weekdays">${wdays.map(d => `<div class="weekday">${d}</div>`).join('')}</div>
        <div class="calendar-days">`;

        const fdw = first.getDay();
        for (let i = 0; i < fdw; i++) {
            html += `<button type="button" class="calendar-day other-month" disabled>${prevLast.getDate() - (fdw - 1 - i)}</button>`;
        }

        for (let day = 1; day <= last.getDate(); day++) {
            const date = new Date(y, m, day); date.setHours(0, 0, 0, 0);
            let cls = 'calendar-day';
            if (sameDay(date, today))              cls += ' today';
            if (date > today)                       cls += ' disabled';
            if (selectedStartDate && selectedEndDate) {
                if (sameDay(date, selectedStartDate))  cls += ' selected range-start';
                else if (sameDay(date, selectedEndDate)) cls += ' selected range-end';
                else if (date > selectedStartDate && date < selectedEndDate) cls += ' in-range';
            }
            const dis = date > today ? 'disabled' : '';
            html += `<button type="button" class="${cls}" data-date="${fmt(date)}" ${dis}>${day}</button>`;
        }

        const ldw = last.getDay();
        const rem = ldw === 6 ? 0 : 6 - ldw;
        for (let i = 1; i <= rem; i++) {
            html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
        }

        html += '</div>';
        el.innerHTML = html;

        el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => {
            btn.addEventListener('click', handleDayClick);
        });
    }

    function handleDayClick(e) {
        const date = new Date(e.target.dataset.date); date.setHours(0, 0, 0, 0);
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        document.querySelector('[data-preset="custom"]')?.classList.add('active');

        if (selectingStart || date < selectedStartDate) {
            selectedStartDate = date;
            selectedEndDate   = date;
            selectingStart    = false;
        } else {
            selectedEndDate = date >= selectedStartDate ? date : selectedStartDate;
            if (date < selectedStartDate) { selectedEndDate = selectedStartDate; selectedStartDate = date; }
            selectingStart = true;
        }

        updateDisplay();
        renderCalendars();
    }

    function updateDisplay() {
        if (!selectedStartDate || !selectedEndDate) return;
        const el = document.getElementById('selectedRangeText');
        if (el) el.textContent = `${fmt(selectedStartDate)} to ${fmt(selectedEndDate)}`;
    }

    function fmt(d) {
        if (!d) return '';
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }

    function sameDay(a, b) {
        return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
    }
})();

// ─── Word Cloud Generator ─────────────────────────────────────────────────────
const NewsWordCloudGenerator = {
    projectId: '{{ $projectId ?? "" }}',
    startDate: '{{ $startDate ?? "" }}',
    endDate:   '{{ $endDate ?? "" }}',
    chart:     null,

    // Warna multicolor seperti DroneEmprit
    colors: [
        '#e74c3c', '#e67e22', '#f1c40f', '#2ecc71', '#1abc9c',
        '#3498db', '#9b59b6', '#e91e63', '#ff5722', '#009688',
        '#673ab7', '#2196f3', '#4caf50', '#ff9800', '#f44336',
        '#00bcd4', '#8bc34a', '#ffc107', '#03a9f4', '#cddc39',
        '#ff4081', '#7c4dff', '#00e5ff', '#76ff03', '#ffea00',
    ],

    currentSentiment: '2',

    async init() {
        if (typeof echarts === 'undefined') {
            this.showError('ECharts library failed to load');
            return;
        }
        // Sentiment filter buttons
        document.querySelectorAll('.sentiment-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                document.querySelectorAll('.sentiment-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.currentSentiment = btn.dataset.sentiment;
                this.loadData();
            });
        });
        try {
            await this.loadData();
        } catch (error) {
            console.error('Word cloud error:', error);
            this.showError('Failed to load data');
        }
    },

    async loadData() {
        const loadingState   = document.getElementById('loadingState');
        const chartDiv       = document.getElementById('wordCloudChart');
        const emptyState     = document.getElementById('emptyState');
        const hintEl         = document.getElementById('wordCloudHint');
        const loadingText    = document.getElementById('loadingText');
        const loadingProgress = document.getElementById('loadingProgress');

        loadingState.style.display  = 'flex';
        chartDiv.style.display      = 'none';
        emptyState.style.display    = 'none';
        hintEl.style.display        = 'none';
        loadingText.textContent     = 'Fetching news data...';
        loadingProgress.textContent = '';

        const url = `/mk/api/news/word-cloud?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}&sentiment=${this.currentSentiment}`;
        const t0  = Date.now();

        const response = await fetch(url);
        loadingText.textContent = 'Building word cloud...';

        const result = await response.json();
        if (!result.success) throw new Error(result.error || 'API error');

        const phrases  = result.data?.data?.phrases || {};
        const loadTime = ((Date.now() - t0) / 1000).toFixed(1);
        loadingProgress.textContent = `${Object.keys(phrases).length} words loaded in ${loadTime}s`;

        await new Promise(r => setTimeout(r, 150));
        this.generateWordCloud(phrases);
    },

    generateWordCloud(phrases) {
        const loadingState = document.getElementById('loadingState');
        const chartDiv     = document.getElementById('wordCloudChart');
        const emptyState   = document.getElementById('emptyState');
        const hintEl       = document.getElementById('wordCloudHint');

        if (!phrases || Object.keys(phrases).length === 0) {
            loadingState.style.display = 'none';
            emptyState.style.display   = 'block';
            this.updateStats({ totalWords: 0, topWord: '-', totalMentions: 0, avgMentions: 0 });
            return;
        }

        const entries  = Object.entries(phrases).sort((a, b) => b[1] - a[1]);
        const maxCount = entries[0][1];
        const minCount = entries[entries.length - 1][1];

        const wordData = entries.slice(0, 150).map(([word, count]) => {
            const norm    = (count - minCount) / (maxCount - minCount || 1);
            // Scaling logaritmik supaya distribusi lebih merata seperti DroneEmprit
            const scaled  = Math.log1p(norm * 9) / Math.log1p(9) * 65 + 13;
            return { name: word, value: scaled, originalCount: count };
        });

        const totalWords    = entries.length;
        const topWord       = entries[0][0];
        const totalMentions = entries.reduce((s, [, c]) => s + c, 0);
        const avgMentions   = Math.round(totalMentions / totalWords);
        this.updateStats({ totalWords, topWord, totalMentions, avgMentions });

        loadingState.style.display = 'none';
        chartDiv.style.display     = 'block';
        hintEl.style.display       = 'flex';

        if (this.chart) this.chart.dispose();
        this.chart = echarts.init(chartDiv, null, {
            renderer: 'canvas',
            devicePixelRatio: window.devicePixelRatio || 1,
        });

        const sentimentColors = {
            '2':  ['#e74c3c','#e67e22','#f1c40f','#2ecc71','#1abc9c','#3498db','#9b59b6','#e91e63','#ff5722','#009688','#673ab7','#2196f3','#4caf50','#ff9800','#f44336','#00bcd4','#8bc34a','#ffc107','#03a9f4','#cddc39','#ff4081','#7c4dff','#00e5ff','#76ff03','#ffea00'],
            '0':  ['#10b981','#059669','#34d399','#6ee7b7','#047857','#0d9488','#14b8a6','#5eead4','#2dd4bf','#86efac','#22c55e','#16a34a','#15803d','#166534','#4ade80'],
            '1':  ['#f59e0b','#d97706','#fbbf24','#b45309','#fcd34d','#f97316','#ea580c','#fb923c','#fdba74','#fed7aa','#fde68a','#fef08a','#fef9c3','#fde047','#eab308'],
            '-1': ['#ef4444','#dc2626','#b91c1c','#f87171','#fca5a5','#f43f5e','#e11d48','#fb7185','#be123c','#9f1239','#ff6b6b','#ee5a24','#e55039','#c0392b','#922b21'],
        };
        const colors = sentimentColors[this.currentSentiment] || sentimentColors['2'];
        let colorIdx = 0;

        const option = {
            tooltip: {
                show: true,
                trigger: 'item',
                backgroundColor: '#fff',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                padding: 14,
                shadowBlur: 16,
                shadowColor: 'rgba(0,0,0,0.12)',
                textStyle: { fontFamily: 'Poppins, sans-serif', color: '#1a202c', fontSize: 13 },
                formatter: p => `
                    <div style="font-family:Poppins,sans-serif;min-width:160px;text-align:center;">
                        <div style="font-weight:700;font-size:15px;margin-bottom:8px;">${p.name}</div>
                        <div style="background:#f1f5f9;border-radius:10px;padding:6px 12px;margin-bottom:8px;">
                            <span style="font-weight:700;color:#038047;">${p.data.originalCount} mentions</span>
                        </div>
                        <div style="font-size:11px;color:#94a3b8;">Click to search Google News</div>
                    </div>`,
            },
            series: [{
                type: 'wordCloud',
                shape: 'circle',
                left: 'center',
                top: 'center',
                width: '96%',
                height: '96%',
                sizeRange: [13, 78],
                rotationRange: [-75, 75],
                rotationStep: 15,
                gridSize: 3,
                drawOutOfBound: false,
                layoutAnimation: true,
                textStyle: {
                    fontFamily: 'Poppins, Arial, sans-serif',
                    fontWeight: 'bold',
                    // Setiap kata dapat warna berbeda secara sequential, seperti DroneEmprit
                    color: () => colors[colorIdx++ % colors.length],
                },
                emphasis: {
                    focus: 'self',
                    textStyle: {
                        textShadowBlur: 8,
                        textShadowColor: 'rgba(0,0,0,0.25)',
                    },
                },
                data: wordData,
            }],
        };

        setTimeout(() => {
            this.chart.setOption(option, true);

            this.chart.on('click', p => {
                if (p?.name) {
                    window.open(`https://news.google.com/search?q=${encodeURIComponent(p.name)}`, '_blank', 'noopener,noreferrer');
                }
            });

            this.chart.on('mouseover', () => { chartDiv.style.cursor = 'pointer'; });
            this.chart.on('mouseout',  () => { chartDiv.style.cursor = 'default'; });
        }, 10);

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => this.chart?.resize(), 250);
        });
    },

    updateStats({ totalWords, topWord, totalMentions, avgMentions }) {
        document.getElementById('totalWords').innerHTML    = `<div class="stat-value">${totalWords.toLocaleString()}</div>`;
        document.getElementById('topWord').innerHTML       = `<div class="stat-value" style="font-size:20px;" title="${topWord}">${topWord}</div>`;
        document.getElementById('totalMentions').innerHTML = `<div class="stat-value">${totalMentions.toLocaleString()}</div>`;
        document.getElementById('avgMentions').innerHTML   = `<div class="stat-value">${avgMentions.toLocaleString()}</div>`;

        const pcts = [80, 100, 95, 85];
        document.querySelectorAll('.stat-card').forEach((card, i) => {
            const bar = card.querySelector('.stat-progress-bar');
            if (bar) setTimeout(() => bar.style.width = pcts[i] + '%', 100);
        });
    },

    showError(msg = 'Failed to load data') {
        document.getElementById('loadingState').style.display = 'none';
        const el = document.getElementById('emptyState');
        el.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" style="color:#ef4444;width:64px;height:64px;stroke:currentColor;fill:none;margin-bottom:16px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <h3>Failed to Load Data</h3>
                <p>${msg}. Please try again later.</p>
            </div>`;
        el.style.display = 'block';
        this.updateStats({ totalWords: 0, topWord: '-', totalMentions: 0, avgMentions: 0 });
    },
};

document.addEventListener('DOMContentLoaded', () => NewsWordCloudGenerator.init());
</script>
@endsection