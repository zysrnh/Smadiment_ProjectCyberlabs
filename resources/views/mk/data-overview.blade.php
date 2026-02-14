@extends('mk.layouts.app')

@section('title', 'Data Overview - SMADIMENT')

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
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    body {
        background: var(--bg-gray-50);
    }

    /* Page Container */
    .dashboard-container {
        padding: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* Page Header */
    .page-header {
        margin-bottom: 32px;
    }

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

    .date-input-group {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: var(--bg-gray-50);
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        transition: all 0.2s;
    }

    .date-input-group:focus-within {
        border-color: var(--primary-green);
        background: var(--bg-white);
        box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
    }

    .date-input-group svg {
        width: 18px;
        height: 18px;
        color: var(--text-secondary);
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .date-input {
        border: none;
        background: transparent;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        outline: none;
        min-width: 140px;
    }

    .date-separator {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 14px;
    }

    .do-filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .do-filter-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-primary);
        opacity: .6;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-family: 'Poppins', sans-serif;
    }

    .do-filter-input {
        padding: 10px 16px;
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        background: var(--bg-gray-50);
        outline: none;
        transition: all 0.2s;
    }

    .do-filter-input:focus {
        border-color: var(--primary-green);
        background: var(--bg-white);
        box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
    }

    .do-btn-apply,
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

    .do-btn-apply:hover,
    .apply-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
    }

    .do-btn-apply svg,
    .apply-btn svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        fill: none;
    }

    /* Grid Layouts */
    .do-row-top {
        display: grid;
        grid-template-columns: 1.15fr 1.15fr 0.85fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .do-row-mid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    /* Card Styles - Matching X Overview */
    .do-card {
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .do-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .do-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-green);
    }

    .do-card:hover::before {
        opacity: 1;
    }

    /* Card Header - Updated Icons */
    .do-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 2px solid var(--bg-gray-50);
        background: var(--bg-white);
    }

    .do-card-head-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .do-head-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(3, 128, 71, 0.1) 0%, rgba(3, 128, 71, 0.05) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .do-head-icon::after {
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

    .do-card:hover .do-head-icon::after {
        opacity: 0.5;
    }

    .do-head-icon svg {
        width: 28px;
        height: 28px;
        color: var(--primary-green);
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .do-card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        font-family: 'Poppins', sans-serif;
    }

    .do-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        background: var(--bg-gray-100);
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: .4px;
        font-family: 'Poppins', sans-serif;
    }

    /* Card Body */
    .do-card-body {
        padding: 20px 24px 24px;
        flex: 1;
    }

    .do-body-scroll {
        max-height: 185px;
        overflow-y: auto;
    }

    .do-body-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .do-body-scroll::-webkit-scrollbar-thumb {
        background: var(--border-gray);
        border-radius: 3px;
    }

    .do-body-scroll::-webkit-scrollbar-thumb:hover {
        background: var(--text-secondary);
    }

    /* Mentions Breakdown */
    .mentions-breakdown {
        display: flex;
        flex-direction: column;
        gap: 32px;
        padding: 10px 0;
    }

    .mention-item {
        text-align: center;
    }

    .mention-item-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 12px;
        font-family: 'Poppins', sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .mention-item-value {
        font-size: 42px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -1px;
        font-family: 'Poppins', sans-serif;
    }

    /* Mini Table */
    .do-tbl {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-family: 'Poppins', sans-serif;
    }

    .do-tbl thead tr {
        background: var(--bg-white);
    }

    .do-tbl th {
        padding: 10px 12px;
        font-size: 10px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: .3px;
        border-bottom: 1px solid var(--border-gray);
        text-align: left;
    }

    .do-tbl-left {
        text-align: left;
    }

    .do-tbl-right {
        text-align: right;
    }

    .do-tbl td {
        padding: 12px;
        font-size: 13px;
        color: var(--text-primary);
        border-bottom: 1px solid var(--bg-gray-100);
    }

    .do-tbl tbody tr {
        transition: all 0.2s;
        background: var(--bg-white);
    }

    .do-tbl tbody tr:hover {
        background: var(--bg-gray-50);
    }

    .do-tbl tr:last-child td {
        border-bottom: none;
    }

    .do-tbl-rank {
        font-weight: 700;
        color: var(--primary-green);
        width: 28px;
        font-size: 13px;
    }

    .do-tbl-name {
        font-weight: 600;
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .do-tbl-num {
        text-align: right;
        font-weight: 600;
        font-size: 13px;
        color: var(--text-primary);
    }

    /* Chart Controls */
    .chart-controls-inline {
        display: flex;
        gap: 4px;
        background: var(--bg-gray-100);
        padding: 4px;
        border-radius: 10px;
    }

    .chart-type-btn-small {
        padding: 8px 12px;
        border: none;
        background: transparent;
        color: var(--text-secondary);
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chart-type-btn-small svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .chart-type-btn-small:hover {
        background: var(--bg-white);
        color: var(--text-primary);
    }

    .chart-type-btn-small.active {
        background: var(--primary-green);
        color: var(--bg-white);
        box-shadow: 0 2px 4px rgba(3, 128, 71, 0.2);
    }

    /* View All Button */
    .do-view-all-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: var(--bg-white);
        color: var(--text-primary);
        border: 1px solid var(--border-gray);
        border-radius: 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }

    .do-view-all-btn:hover {
        background: var(--bg-gray-50);
        border-color: var(--primary-green);
        color: var(--primary-green);
        transform: translateY(-1px);
    }

    .do-view-all-btn svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2.5;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* Modal Styles */
    .do-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(10px);
    }

    .do-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .do-modal-content {
        background: var(--bg-white);
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        max-height: 85vh;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        animation: modalSlideIn 0.3s ease-out;
        overflow: hidden;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-20px) scale(0.95);
            opacity: 0;
        }
        to {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
    }

    .do-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 24px 28px;
        border-bottom: 2px solid var(--bg-gray-50);
        background: var(--bg-white);
    }

    .do-modal-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 6px 0;
        font-family: 'Poppins', sans-serif;
    }

    .do-modal-subtitle {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-secondary);
        margin: 0;
        font-family: 'Poppins', sans-serif;
    }

    .do-modal-close {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--bg-gray-50);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        color: var(--text-secondary);
    }

    .do-modal-close:hover {
        background: #ef4444;
        color: white;
    }

    .do-modal-close svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        stroke-width: 2.5;
        stroke-linecap: round;
        fill: none;
    }

    .do-modal-body {
        padding: 20px 28px 28px;
        max-height: calc(85vh - 120px);
        overflow-y: auto;
    }

    .do-modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .do-modal-body::-webkit-scrollbar-thumb {
        background: var(--border-gray);
        border-radius: 3px;
    }

    .do-modal-body::-webkit-scrollbar-thumb:hover {
        background: var(--text-secondary);
    }

    /* Skeleton Loading Styles */
    .do-skeleton {
        padding: 10px 0;
    }

    .skeleton-line {
        height: 28px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .skeleton-number-inline {
        height: 48px;
        width: 140px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
        display: inline-block;
        margin: 0 auto;
    }

    .skeleton-circle {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    .skeleton-map-placeholder {
        height: 420px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    .do-skeleton-chart {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 80%;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .do-card[data-loaded="true"] .do-skeleton,
    .do-card[data-loaded="true"] .do-skeleton-chart,
    .do-card[data-loaded="true"] .do-skeleton-map,
    .do-card[data-loaded="true"] .skeleton-number-inline {
        display: none;
    }

    /* Empty State */
    .do-empty {
        font-size: 14px;
        color: var(--text-secondary);
        text-align: center;
        padding: 60px 20px;
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
    }

    /* Leaflet map fix */
    #buzzMap .leaflet-container {
        height: 100%;
        font-family: 'Poppins', sans-serif;
    }

    .circle-label {
        pointer-events: none !important;
    }

    .circle-label div {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .map-legend {
        pointer-events: none;
    }

    .map-legend>div {
        pointer-events: auto;
    }

    /* Responsive */
    @media(max-width:1100px) {
        .do-row-top {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width:768px) {
        .dashboard-container {
            padding: 16px;
        }

        .do-row-top,
        .do-row-mid {
            grid-template-columns: 1fr;
        }

        .filter-content {
            flex-direction: column;
            align-items: stretch;
        }

        .date-range-wrapper {
            flex-direction: column;
        }

        .do-btn-apply,
        .apply-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Data Overview</h1>
        <p>Monitor and analyze your social media and news performance metrics</p>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <div class="filter-content">
            <div class="filter-label">
                <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; display: inline; vertical-align: middle; margin-right: 6px; stroke: currentColor; fill: none;">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Filters
            </div>

            <!-- Project Selector -->
            <div class="do-filter-group">
                <label class="do-filter-label">Project</label>
                <select class="do-filter-input" id="doProject">
                    @foreach($projects as $p)
                    <option value="{{ $p['id'] }}" {{ $p['id'] == $projectId ? 'selected' : '' }}>
                        {{ $p['name'] ?? $p['title'] ?? 'Project #' . $p['id'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="date-range-wrapper">
                <div class="date-input-group">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <input type="date" class="date-input" id="doStartDate" value="{{ $startDate }}">
                </div>

                <span class="date-separator">to</span>

                <div class="date-input-group">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <input type="date" class="date-input" id="doEndDate" value="{{ $endDate }}">
                </div>
            </div>

            <button class="apply-btn" id="doBtnApply">
                <svg viewBox="0 0 24 24">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                Apply Filter
            </button>
        </div>
    </div>

    <!-- ROW 1 — 3 Cards -->
    <div class="do-row-top">

        <!-- 1. Trending Topics News -->
        <div class="do-card" data-lazy="trending-topics">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <div class="do-head-icon">
                        <svg viewBox="0 0 24 24">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                            <polyline points="17 6 23 6 23 12" />
                        </svg>
                    </div>
                    <span class="do-card-title">Trending Topics</span>
                </div>
                <span class="do-badge">News</span>
            </div>
            <div class="do-card-body do-body-scroll">
                <div class="do-skeleton">
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                </div>
            </div>
        </div>

        <!-- 2. Top Hashtag X -->
        <div class="do-card" data-lazy="top-hashtags">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <div class="do-head-icon">
                        <svg viewBox="0 0 24 24">
                            <line x1="4" y1="9" x2="20" y2="9" />
                            <line x1="4" y1="15" x2="20" y2="15" />
                            <line x1="9" y1="4" x2="5" y2="20" />
                            <line x1="15" y1="4" x2="11" y2="20" />
                        </svg>
                    </div>
                    <span class="do-card-title">Top Hashtag</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="do-badge">X</span>
                </div>
            </div>
            <div class="do-card-body do-body-scroll">
                <div class="do-skeleton">
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                </div>
            </div>
        </div>

        <!-- 3. Mentions Card -->
        <div class="do-card" data-lazy="mention-stats">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <div class="do-head-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                    </div>
                    <span class="do-card-title">Mentions</span>
                </div>
                <span class="do-badge">All Media</span>
            </div>
            <div class="do-card-body" style="padding: 20px;">
                <div class="mentions-breakdown">
                    <div class="mention-item">
                        <div class="mention-item-label">Mass Media</div>
                        <div class="mention-item-value" style="color:#1a202c;">
                            <div class="skeleton-number-inline"></div>
                        </div>
                    </div>
                    <div class="mention-item">
                        <div class="mention-item-label">Social Media</div>
                        <div class="mention-item-value" style="color:#038047;">
                            <div class="skeleton-number-inline"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ROW 2 — Most Engaged User + Sentiment Score -->
    <div class="do-row-mid">

        <!-- Most Engaged User -->
        <div class="do-card" data-lazy="engaged-users">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <div class="do-head-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <span class="do-card-title">Most Engaged User</span>
                </div>
                <span class="do-badge">X</span>
            </div>
            <div class="do-card-body" style="padding: 15px; min-height: 300px; display: flex; align-items: center; justify-content: center; position: relative;">
                <canvas id="chartDonut" style="max-width: 100%; max-height: 270px;"></canvas>
                <div class="do-skeleton-chart">
                    <div class="skeleton-circle"></div>
                </div>
            </div>
        </div>

        <!-- Sentiment Score -->
        <div class="do-card" data-lazy="sentiment-timeline">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <div class="do-head-icon">
                        <svg viewBox="0 0 24 24">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
                        </svg>
                    </div>
                    <span class="do-card-title">Sentiment Score</span>
                </div>
                <div class="chart-controls-inline">
                    <button class="chart-type-btn-small active" data-type="line" onclick="changeSentimentChartType('line', this)">
                        <svg viewBox="0 0 24 24">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </button>
                    <button class="chart-type-btn-small" data-type="bar" onclick="changeSentimentChartType('bar', this)">
                        <svg viewBox="0 0 24 24">
                            <line x1="12" y1="20" x2="12" y2="10"/>
                            <line x1="18" y1="20" x2="18" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="16"/>
                        </svg>
                    </button>
                    <button class="chart-type-btn-small" data-type="area" onclick="changeSentimentChartType('area', this)">
                        <svg viewBox="0 0 24 24">
                            <path d="M3 3v18h18"/>
                            <path d="M7 12L12 7l5 5"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="do-card-body" style="padding: 15px 20px 20px; height: 240px; position: relative;">
                <canvas id="chartSentiment"></canvas>
                <div class="do-skeleton-chart">
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- ROW 2.5 — Sentiment by Media -->
    <div class="do-card" data-lazy="sentiment-media">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <div class="do-head-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </div>
                <span class="do-card-title">Sentiment by Media</span>
            </div>
            <span class="do-badge">Breakdown</span>
        </div>
        <div class="do-card-body" style="padding: 24px; min-height: 350px; position: relative;">
            <div style="margin-bottom: 16px;">
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Sentiment distribution across different media platforms</p>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="chartSentimentMedia"></canvas>
            </div>
            <div class="do-skeleton-chart">
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
            </div>
        </div>
    </div>

    <!-- ROW 3 — Buzzer Map -->
    <div class="do-card" data-lazy="buzzer-map">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <div class="do-head-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="10" r="3" />
                        <path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z" />
                    </svg>
                </div>
                <span class="do-card-title">Buzzer Map</span>
            </div>
            <span class="do-badge">Geographic</span>
        </div>
        <div style="padding:0;">
            <div id="buzzMap" style="width:100%; height:420px;"></div>
            <div class="do-skeleton-map">
                <div class="skeleton-map-placeholder"></div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL: All Hashtags -->
<div id="hashtagModal" class="do-modal">
    <div class="do-modal-content">
        <div class="do-modal-header">
            <div>
                <h3 class="do-modal-title">Top Hashtags</h3>
                <p class="do-modal-subtitle">Showing all trending hashtags</p>
            </div>
            <button class="do-modal-close" onclick="closeHashtagModal()">
                <svg viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="do-modal-body" id="hashtagModalBody">
            <!-- Will be populated dynamically -->
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ────────────────────────────────────────────
// 🚀 LAZY LOADING DATA MANAGER
// ────────────────────────────────────────────
const DataOverviewLazyLoader = {
    projectId: {{ $projectId ?? 'null' }},
    startDate: '{{ $startDate }}',
    endDate: '{{ $endDate }}',
    loadedSections: new Set(),
    charts: {},
    currentSentimentType: 'line',

    init() {
        console.log('🚀 Initializing Lazy Loader');
        this.setupIntersectionObserver();
        this.setupFilterButton();
    },

    setupIntersectionObserver() {
        const options = {
            root: null,
            rootMargin: '100px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    const section = card.dataset.lazy;
                    
                    if (!this.loadedSections.has(section)) {
                        this.loadedSections.add(section);
                        this.loadSection(section, card);
                        observer.unobserve(card);
                    }
                }
            });
        }, options);

        document.querySelectorAll('[data-lazy]').forEach(card => {
            observer.observe(card);
        });
    },

    async loadSection(section, card) {
        console.log(`📦 Loading section: ${section}`);
        
        try {
            switch(section) {
                case 'mention-stats':
                    await this.loadMentionStats(card);
                    break;
                case 'trending-topics':
                    await this.loadTrendingTopics(card);
                    break;
                case 'top-hashtags':
                    await this.loadTopHashtags(card);
                    break;
                case 'engaged-users':
                    await this.loadEngagedUsers(card);
                    break;
                case 'sentiment-timeline':
                    await this.loadSentimentTimeline(card);
                    break;
                case 'sentiment-media':
                    await this.loadSentimentMedia(card);
                    break;
                case 'buzzer-map':
                    await this.loadBuzzerMap(card);
                    break;
            }
            
            card.dataset.loaded = 'true';
        } catch (error) {
            console.error(`❌ Failed to load ${section}:`, error);
            this.showError(card);
        }
    },

    async loadMentionStats(card) {
        const response = await fetch(`/mk/api/mention-counts?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const data = await response.json();
        
        const values = card.querySelectorAll('.mention-item-value');
        
        const news = data.news || 0;
        const social = data.social || 0;
        
        values[0].innerHTML = news.toLocaleString();
        values[1].innerHTML = social.toLocaleString();
    },

    async loadTrendingTopics(card) {
        const response = await fetch(`/mk/api/trending-topics?limit=8`);
        const data = await response.json();
        
        const body = card.querySelector('.do-card-body');
        const topics = data.data || [];
        
        if (topics.length === 0) {
            body.innerHTML = '<div class="do-empty">No data available</div>';
            return;
        }

        let html = `
            <table class="do-tbl">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th class="do-tbl-left">Topic</th>
                        <th class="do-tbl-right">Articles</th>
                    </tr>
                </thead>
                <tbody>
        `;

        topics.slice(0, 8).forEach((topic, i) => {
            const name = topic.title || topic.name || topic.topic || 'Unknown';
            const count = topic.articles || topic.count || topic.total || 0;
            
            html += `
                <tr>
                    <td class="do-tbl-rank">${i + 1}</td>
                    <td class="do-tbl-name">${name}</td>
                    <td class="do-tbl-num">${count.toLocaleString()} docs</td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        body.innerHTML = html;
    },

    async loadTopHashtags(card) {
        const response = await fetch(`/mk/api/top-hashtags?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const data = await response.json();
        
        const body = card.querySelector('.do-card-body');
        const hashtags = data.data || [];
        
        if (hashtags.length === 0) {
            body.innerHTML = '<div class="do-empty">No data available</div>';
            return;
        }

        if (hashtags.length > 5) {
            const headerRight = card.querySelector('.do-card-head > div:last-child');
            headerRight.innerHTML += `
                <button class="do-view-all-btn" onclick="DataOverviewLazyLoader.openHashtagModal(${JSON.stringify(hashtags)})">
                    View All
                    <svg viewBox="0 0 24 24">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </button>
            `;
        }

        let html = `
            <table class="do-tbl">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th class="do-tbl-left">Hashtag</th>
                        <th class="do-tbl-right">Mention</th>
                    </tr>
                </thead>
                <tbody>
        `;

        hashtags.slice(0, 5).forEach((tag, i) => {
            let tagName = tag.hashtag || tag.name || tag.tag || 'unknown';
            const count = tag.mention || tag.size || tag.count || 0;
            
            if (!tagName.startsWith('#')) tagName = '#' + tagName;
            
            html += `
                <tr>
                    <td class="do-tbl-rank">${i + 1}</td>
                    <td class="do-tbl-name" style="color:#3b7dd8;">${tagName}</td>
                    <td class="do-tbl-num">${count.toLocaleString()}</td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        body.innerHTML = html;
    },

    async loadEngagedUsers(card) {
        const response = await fetch(`/mk/api/active-users?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const data = await response.json();
        
        const users = data.data || [];
        
        if (users.length === 0) {
            card.querySelector('canvas').parentElement.innerHTML = 
                '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--text-secondary);font-size:14px;font-weight:500;">No active user data available</div>';
            return;
        }

        const labels = users.map(u => '@' + u.username);
        const counts = users.map(u => u.count);
        const colors = ['#4BACC6', '#F2994A', '#27AE60', '#9B59B6', '#E74C3C', '#E67E22'];

        this.renderDoughnutChart(labels, counts, colors);
    },

    async loadSentimentTimeline(card) {
        const response = await fetch(`/mk/api/sentiment-timeline?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const data = await response.json();
        
        this.sentimentTimelineData = data;
        this.renderLineChart(data, this.currentSentimentType);
    },

    async loadSentimentMedia(card) {
        const response = await fetch(`/mk/api/sentiment-by-media?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const data = await response.json();
        
        console.log('📊 Sentiment Media Data:', data);
        
        const mediaData = data.data || [];
        
        if (mediaData.length === 0) {
            const canvas = card.querySelector('canvas');
            if (canvas && canvas.parentElement) {
                canvas.parentElement.innerHTML = 
                    '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--text-secondary);font-size:14px;font-weight:500;">No sentiment data available</div>';
            }
            return;
        }

        this.renderSentimentMediaChart(mediaData);
    },

    async loadBuzzerMap(card) {
        const response = await fetch(`/mk/api/geo-users?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const data = await response.json();
        
        this.renderMap(data.data || []);
    },

    renderDoughnutChart(labels, counts, colors) {
        const improvedExternalLabelPlugin = {
            id: 'improvedExternalLabelPlugin',
            afterDatasetsDraw: function(chart) {
                if (labels.length === 0) return;

                var ctx = chart.ctx;
                var meta = chart.getDatasetMeta(0);
                var centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                var centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                var outerRadius = meta.data[0] ? meta.data[0].outerRadius : 0;

                ctx.save();
                ctx.textBaseline = 'middle';

                var labelPositions = [];
                
                meta.data.forEach(function(slice, i) {
                    if (!slice || slice.circumference === 0) return;

                    var angle = (slice.startAngle + slice.endAngle) / 2;
                    var label = labels[i] || '';
                    var count = (counts[i] || 0).toLocaleString();
                    var color = colors[i % colors.length];
                    var isRight = Math.cos(angle) >= 0;

                    var edgeX = centerX + outerRadius * Math.cos(angle);
                    var edgeY = centerY + outerRadius * Math.sin(angle);

                    var extendDistance = 35;
                    var labelX = centerX + (outerRadius + extendDistance) * Math.cos(angle);
                    var labelY = centerY + (outerRadius + extendDistance) * Math.sin(angle);

                    var lineEndX = isRight ? labelX + 40 : labelX - 40;

                    labelPositions.push({
                        edgeX, edgeY, labelX, labelY, lineEndX,
                        label, count, color, isRight, angle
                    });
                });

                labelPositions.sort((a, b) => a.labelY - b.labelY);

                var minSpacing = 28;
                for (var i = 1; i < labelPositions.length; i++) {
                    var curr = labelPositions[i];
                    var prev = labelPositions[i - 1];
                    
                    if (Math.abs(curr.labelY - prev.labelY) < minSpacing) {
                        curr.labelY = prev.labelY + minSpacing;
                    }
                }

                labelPositions.forEach(function(pos) {
                    ctx.strokeStyle = pos.color;
                    ctx.lineWidth = 1.5;
                    ctx.beginPath();
                    ctx.moveTo(pos.edgeX, pos.edgeY);
                    ctx.lineTo(pos.labelX, pos.labelY);
                    ctx.lineTo(pos.lineEndX, pos.labelY);
                    ctx.stroke();

                    ctx.fillStyle = pos.color;
                    ctx.beginPath();
                    ctx.arc(pos.lineEndX, pos.labelY, 3, 0, Math.PI * 2);
                    ctx.fill();

                    var textX = pos.isRight ? pos.lineEndX + 8 : pos.lineEndX - 8;
                    ctx.textAlign = pos.isRight ? 'left' : 'right';

                    ctx.fillStyle = '#1a202c';
                    ctx.font = '700 11px Poppins, sans-serif';
                    ctx.fillText(pos.label, textX, pos.labelY - 7);

                    ctx.fillStyle = '#64748b';
                    ctx.font = '500 10px Poppins, sans-serif';
                    ctx.fillText('(' + pos.count + ' twits)', textX, pos.labelY + 7);
                });

                ctx.restore();
            }
        };

        new Chart(document.getElementById('chartDonut').getContext('2d'), {
            type: 'doughnut',
            plugins: [improvedExternalLabelPlugin],
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.35,
                cutout: '55%',
                layout: {
                    padding: { top: 25, right: 110, bottom: 25, left: 110 }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(255,255,255,0.98)',
                        titleColor: '#1a202c',
                        bodyColor: '#1a202c',
                        borderColor: '#e2e8f0',
                        borderWidth: 1.5,
                        cornerRadius: 8,
                        padding: 12,
                        titleFont: { size: 13, weight: '700', family: 'Poppins' },
                        bodyFont: { size: 12, family: 'Poppins' },
                        callbacks: {
                            label: ctx => ' ' + ctx.parsed.toLocaleString() + ' tweets'
                        }
                    }
                },
                animation: { animateRotate: true, duration: 900 }
            }
        });
    },

    renderLineChart(timeline, type = 'line') {
        const ctx = document.getElementById('chartSentiment');
        if (!ctx) return;

        if (this.charts.sentiment) {
            this.charts.sentiment.destroy();
        }

        const config = {
            type: type === 'area' ? 'line' : type,
            data: {
                labels: timeline.dates,
                datasets: [
                    {
                        label: 'Total',
                        data: timeline.values,
                        borderColor: '#5AB9EA',
                        backgroundColor: type === 'area' || type === 'line' ? 'rgba(90, 185, 234, 0.1)' : 'rgba(90, 185, 234, 0.8)',
                        borderWidth: type === 'bar' ? 2 : 2.5,
                        tension: 0.4,
                        pointRadius: type === 'line' ? timeline.dates.map((d, i) => i === timeline.dates.length - 1 ? 6 : 4) : 0,
                        pointHoverRadius: type === 'line' ? 6 : 0,
                        pointBackgroundColor: '#5AB9EA',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        fill: type === 'area' || type === 'line' ? true : false
                    },
                    {
                        label: 'Positive',
                        data: timeline.sentiment.positive,
                        borderColor: '#22C55E',
                        backgroundColor: type === 'bar' ? 'rgba(34, 197, 94, 0.8)' : 'transparent',
                        borderWidth: type === 'bar' ? 2 : 2,
                        tension: 0.4,
                        pointRadius: type === 'line' ? 3 : 0,
                        fill: false
                    },
                    {
                        label: 'Neutral',
                        data: timeline.sentiment.neutral,
                        borderColor: '#B0BEC5',
                        backgroundColor: type === 'bar' ? 'rgba(176, 190, 197, 0.8)' : 'transparent',
                        borderWidth: type === 'bar' ? 2 : 1.5,
                        tension: 0.4,
                        pointRadius: type === 'line' ? 2 : 0,
                        fill: false
                    },
                    {
                        label: 'Negative',
                        data: timeline.sentiment.negative,
                        borderColor: '#EF4444',
                        backgroundColor: type === 'bar' ? 'rgba(239, 68, 68, 0.8)' : 'transparent',
                        borderWidth: type === 'bar' ? 2 : 1.5,
                        tension: 0.4,
                        pointRadius: type === 'line' ? 2 : 0,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        align: 'start',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 10,
                            font: { size: 10, weight: '600', family: 'Poppins' },
                            color: '#64748b',
                            boxWidth: 8,
                            boxHeight: 8
                        }
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.98)',
                        titleColor: '#1a202c',
                        bodyColor: '#1a202c',
                        borderColor: '#e2e8f0',
                        borderWidth: 1.5,
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 12, weight: 'bold', family: 'Poppins' },
                        bodyFont: { size: 11, family: 'Poppins' },
                        callbacks: {
                            label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString()
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: { display: false },
                        ticks: {
                            font: { size: 10, weight: '500', family: 'Poppins' },
                            color: '#64748b',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: { display: true, color: 'rgba(0, 0, 0, 0.04)' },
                        ticks: {
                            font: { size: 10, weight: '500', family: 'Poppins' },
                            color: '#64748b',
                            callback: val => val >= 1000 ? (val / 1000) + 'k' : val,
                            maxTicksLimit: 5
                        }
                    }
                }
            }
        };

        if (type === 'bar') {
            config.data.datasets.forEach(ds => {
                ds.borderRadius = 6;
            });
        }

        this.charts.sentiment = new Chart(ctx, config);
    },

    renderSentimentMediaChart(mediaData) {
        const labels = mediaData.map(d => d.media);
        const positiveData = mediaData.map(d => d.positive);
        const negativeData = mediaData.map(d => d.negative);

        const ctx = document.getElementById('chartSentimentMedia');
        if (!ctx) {
            console.error('❌ Canvas element not found');
            return;
        }

        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Positive',
                        data: positiveData,
                        backgroundColor: '#22C55E',
                        borderColor: '#22C55E',
                        borderWidth: 0,
                        borderRadius: 8,
                        barThickness: 32
                    },
                    {
                        label: 'Negative',
                        data: negativeData,
                        backgroundColor: '#EF4444',
                        borderColor: '#EF4444',
                        borderWidth: 0,
                        borderRadius: 8,
                        barThickness: 32
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                interaction: { 
                    intersect: false, 
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 15,
                            font: { size: 12, weight: '700', family: 'Poppins' },
                            color: '#1a202c',
                            boxWidth: 10,
                            boxHeight: 10
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(26, 32, 44, 0.95)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(148, 163, 184, 0.3)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, weight: '700', family: 'Poppins' },
                        bodyFont: { size: 12, family: 'Poppins' },
                        callbacks: {
                            label: function(ctx) {
                                const item = mediaData[ctx.dataIndex];
                                const percentage = ctx.dataset.label === 'Positive' 
                                    ? item.positive_percentage 
                                    : item.negative_percentage;
                                return ` ${ctx.dataset.label}: ${ctx.parsed.x.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        beginAtZero: true,
                        grid: { 
                            color: 'rgba(148, 163, 184, 0.08)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 11, weight: '500', family: 'Poppins' },
                            color: '#64748b',
                            padding: 8,
                            callback: val => val >= 1000 ? (val / 1000) + 'k' : val
                        }
                    },
                    y: {
                        stacked: false,
                        grid: { display: false },
                        ticks: {
                            font: { size: 12, weight: '700', family: 'Poppins' },
                            color: '#1a202c',
                            padding: 10
                        }
                    }
                },
                animation: {
                    duration: 750,
                    easing: 'easeInOutQuart'
                }
            }
        });

        console.log('✅ Chart rendered successfully');
    },

    renderMap(geoData) {
        const map = L.map('buzzMap', { center: [-2.5, 118], zoom: 5 });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        if (geoData.length === 0) return;

        const maxCount = Math.max(...geoData.map(p => p.count || 0));

        geoData.forEach(p => {
            const lat = parseFloat(p.latitude || 0);
            const lng = parseFloat(p.longitude || 0);
            if (lat === 0 && lng === 0) return;

            const name = p.name || 'Unknown';
            const count = parseInt(p.count || 0);

            if (count >= 10) {
                let radius = Math.sqrt(count) * 2500;
                radius = Math.max(radius, 5000);
                radius = Math.min(radius, 50000);
                const opacity = Math.min(0.15 + (count / maxCount) * 0.45, 0.6);

                L.circle([lat, lng], {
                    radius: radius,
                    fillColor: '#ef4444',
                    color: '#ef4444',
                    weight: 1,
                    opacity: 0.3,
                    fillOpacity: opacity
                }).addTo(map);
            }

            const redPin = L.divIcon({
                className: '',
                html: '<div style="width:13px;height:13px;background:#ef4444;border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>',
                iconSize: [13, 13],
                iconAnchor: [6.5, 6.5]
            });

            L.marker([lat, lng], { icon: redPin }).addTo(map)
                .bindPopup(`
                    <div style="font-family:Poppins;text-align:center;padding:8px;">
                        <div style="font-weight:700;font-size:15px;color:#1a202c;margin-bottom:6px;">${name}</div>
                        <div style="font-size:24px;font-weight:800;color:#ef4444;margin-bottom:2px;">${count.toLocaleString()}</div>
                        <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">mentions</div>
                    </div>
                `);

            const label = count > 999 ? (count / 1000).toFixed(1) + 'k' : count;
            const fontSize = count >= 1000 ? '13px' : '11px';

            L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'circle-label',
                    html: `<div style="font-family:Poppins;font-size:${fontSize};font-weight:900;color:#fff;background:rgba(239,68,68,0.95);padding:3px 8px;border-radius:12px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.4);white-space:nowrap;">${label}</div>`,
                    iconSize: [40, 20],
                    iconAnchor: [20, 25]
                }),
                interactive: false
            }).addTo(map);
        });
    },

    openHashtagModal(hashtags) {
        const modal = document.getElementById('hashtagModal');
        const body = document.getElementById('hashtagModalBody');
        
        let html = `
            <table class="do-tbl">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th class="do-tbl-left">Hashtag</th>
                        <th class="do-tbl-right">Mention</th>
                    </tr>
                </thead>
                <tbody>
        `;

        hashtags.forEach((tag, i) => {
            let tagName = tag.hashtag || tag.name || tag.tag || 'unknown';
            const count = tag.mention || tag.size || tag.count || 0;
            
            if (!tagName.startsWith('#')) tagName = '#' + tagName;
            
            html += `
                <tr>
                    <td class="do-tbl-rank">${i + 1}</td>
                    <td class="do-tbl-name" style="color:#3b7dd8;">${tagName}</td>
                    <td class="do-tbl-num">${count.toLocaleString()}</td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        body.innerHTML = html;
        
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    },

    showError(card) {
        const body = card.querySelector('.do-card-body') || card.querySelector('.stat-value');
        if (body) {
            body.innerHTML = '<div class="do-empty">Failed to load data</div>';
        }
    },

    setupFilterButton() {
        document.getElementById('doBtnApply').addEventListener('click', () => {
            const pid = document.getElementById('doProject').value;
            const sd = document.getElementById('doStartDate').value;
            const ed = document.getElementById('doEndDate').value;
            
            if (!sd || !ed) return;
            
            const p = new URLSearchParams(window.location.search);
            p.set('project_id', pid);
            p.set('start_date', sd);
            p.set('end_date', ed);
            window.location.search = p.toString();
        });
    }
};

function changeSentimentChartType(type, button) {
    DataOverviewLazyLoader.currentSentimentType = type;
    document.querySelectorAll('.chart-type-btn-small').forEach(btn => {
        btn.classList.remove('active');
    });
    button.classList.add('active');
    
    if (DataOverviewLazyLoader.sentimentTimelineData) {
        DataOverviewLazyLoader.renderLineChart(DataOverviewLazyLoader.sentimentTimelineData, type);
    }
}

function closeHashtagModal() {
    const modal = document.getElementById('hashtagModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

window.addEventListener('click', event => {
    const modal = document.getElementById('hashtagModal');
    if (event.target === modal) closeHashtagModal();
});

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        const modal = document.getElementById('hashtagModal');
        if (modal && modal.classList.contains('active')) {
            closeHashtagModal();
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    DataOverviewLazyLoader.init();
});
</script>
@endsection