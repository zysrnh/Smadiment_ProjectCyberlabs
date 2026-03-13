@extends('mk.layouts.app')

@section('title', 'X Geographic - SMADIMENT')

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


    /* Alert */
    .alert {
        padding: 16px 20px; border-radius: 12px; margin-bottom: 24px;
        font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px;
    }
    .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

    /* Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--bg-white); border: 1px solid var(--border-gray);
        border-radius: 16px; padding: 24px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; overflow: hidden;
        min-height: 120px; display: flex; flex-direction: column; justify-content: center;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        opacity: 0; transition: opacity 0.3s;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-green); }
    .stat-card:hover::before { opacity: 1; }
    .stat-label { font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .stat-value { font-size: 32px; font-weight: 700; color: var(--text-primary); line-height: 1.2; word-break: break-word; }
    .stat-value.stat-value-text { font-size: 20px; }

    /* do-card — identical to Data Overview */
    .do-card {
        background: var(--bg-white); border: 1px solid var(--border-gray);
        border-radius: 16px; overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; margin-bottom: 24px;
    }
    .do-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        opacity: 0; transition: opacity 0.3s;
    }
    .do-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-green); }
    .do-card:hover::before { opacity: 1; }

    .do-card-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 2px solid var(--bg-gray-50);
        background: var(--bg-white);
        flex-shrink: 0;
    }
    .do-card-head-left { display: flex; align-items: center; gap: 12px; }
    .do-head-icon {
        width: 56px; height: 56px; border-radius: 14px;
        background: linear-gradient(135deg, rgba(3,128,71,0.1) 0%, rgba(3,128,71,0.05) 100%);
        display: flex; align-items: center; justify-content: center;
        position: relative; flex-shrink: 0;
    }
    .do-head-icon::after {
        content: ''; position: absolute; inset: -4px; border-radius: 16px; padding: 4px;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor; mask-composite: exclude;
        opacity: 0; transition: opacity 0.3s;
    }
    .do-card:hover .do-head-icon::after { opacity: 0.5; }
    .do-head-icon svg { width: 28px; height: 28px; color: var(--primary-green); fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .do-card-title    { font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Poppins', sans-serif; margin: 0 0 4px 0; }
    .do-card-subtitle { font-size: 13px; color: var(--text-secondary); font-weight: 500; margin: 0; }
    .do-badge { font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 20px; background: var(--bg-gray-100); color: var(--text-secondary); text-transform: uppercase; letter-spacing: .4px; font-family: 'Poppins', sans-serif; }

    /* Skeleton */
    .do-skeleton { padding: 10px 0; }
    .skeleton-line {
        height: 28px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%; animation: shimmer 1.5s infinite;
        border-radius: 8px; margin-bottom: 10px;
    }
    .skeleton-number-inline {
        height: 48px; width: 140px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%; animation: shimmer 1.5s infinite;
        border-radius: 8px; display: inline-block; margin: 0 auto;
    }
    .skeleton-map-placeholder {
        height: 500px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%; animation: shimmer 1.5s infinite;
    }
    .do-skeleton-chart {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        display: flex; flex-direction: column; gap: 10px; width: 80%;
    }
    @keyframes shimmer {
        0%   { background-position:  200% 0; }
        100% { background-position: -200% 0; }
    }
    .do-card[data-loaded="true"] .do-skeleton,
    .do-card[data-loaded="true"] .do-skeleton-chart,
    .do-card[data-loaded="true"] .do-skeleton-map,
    .do-card[data-loaded="true"] .skeleton-number-inline { display: none; }

    /* ── Map + Location Panel Layout ── */
    .map-with-panel {
        display: flex;
        padding: 0;
    }

    /* Map takes remaining space */
    .map-with-panel .map-area {
        flex: 1;
        min-width: 0;
        position: relative;
    }

    /* Location sidebar */
    .location-panel {
        width: 220px;
        flex-shrink: 0;
        border-left: 1px solid var(--border-gray);
        display: flex;
        flex-direction: column;
        background: var(--bg-white);
    }

    .location-panel-title {
        padding: 14px 16px 10px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--bg-gray-100);
        font-family: 'Poppins', sans-serif;
    }

    .location-list {
        overflow-y: auto;
        flex: 1;
        max-height: 500px;
    }

    .location-list::-webkit-scrollbar { width: 4px; }
    .location-list::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 2px; }
    .location-list::-webkit-scrollbar-thumb:hover { background: var(--text-secondary); }

    .location-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid var(--bg-gray-50);
        transition: all 0.15s;
        font-family: 'Poppins', sans-serif;
    }

    .location-item:hover {
        background: rgba(3, 128, 71, 0.06);
    }

    .location-item.active {
        background: rgba(3, 128, 71, 0.08);
        border-left: 3px solid var(--primary-green);
        padding-left: 11px;
    }

    .location-item-rank {
        font-size: 10px;
        font-weight: 700;
        color: var(--primary-green);
        width: 18px;
        flex-shrink: 0;
    }

    .location-item-info {
        flex: 1;
        min-width: 0;
    }

    .location-item-name {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .location-item-count {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .location-item-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Location panel skeleton */
    .location-panel-skeleton { padding: 10px 14px; }
    .location-panel-skeleton .skeleton-line { height: 20px; margin-bottom: 8px; }

    /* Map skeleton inside map-area */
    .map-area .do-skeleton-map { position: absolute; inset: 0; z-index: 2; }
    .map-area .do-skeleton-map .skeleton-map-placeholder { height: 100%; }

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
    .do-tbl-num  { text-align: right; font-weight: 600; font-size: 13px; color: var(--text-primary); }
    .do-body-scroll { overflow-y: auto; }
    .do-body-scroll::-webkit-scrollbar { width: 6px; }
    .do-body-scroll::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 3px; }
    .do-empty { font-size: 14px; color: var(--text-secondary); text-align: center; padding: 60px 20px; font-weight: 500; font-family: 'Poppins', sans-serif; }

    .view-all-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--bg-white); color: var(--text-primary); border: 1px solid var(--border-gray); border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s; }
    .view-all-btn:hover { background: var(--bg-gray-50); border-color: var(--primary-green); color: var(--primary-green); }


    /* ── Chart Cards Row ── */
    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .do-card-body { padding: 20px 24px 24px; flex: 1; }

    /* Province bars */
    .prov-bar-row { margin-bottom: 12px; }
    .prov-bar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-family: 'Poppins', sans-serif; }
    .prov-bar-name  { font-size: 12px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%; }
    .prov-bar-count { font-size: 12px; font-weight: 700; color: var(--text-secondary); }
    .prov-bar-track { height: 8px; background: var(--bg-gray-100); border-radius: 99px; overflow: hidden; }
    .prov-bar-fill  { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--primary-green), var(--primary-green-dark)); transition: width 0.8s cubic-bezier(0.4,0,0.2,1); width: 0; }

    /* Country bars */
    .country-bar-row { margin-bottom: 14px; }
    .country-bar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-family: 'Poppins', sans-serif; }
    .country-bar-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .country-bar-count { font-size: 13px; font-weight: 700; color: var(--text-primary); }
    .country-bar-track { height: 10px; background: var(--bg-gray-100); border-radius: 99px; overflow: hidden; }
    .country-bar-fill  { height: 100%; border-radius: 99px; transition: width 0.9s cubic-bezier(0.4,0,0.2,1); width: 0; }
    .country-flag { font-size: 18px; line-height: 1; }

    /* Sentiment legend */
    .senti-legend { display: flex; flex-direction: column; gap: 10px; margin-top: 16px; }
    .senti-legend-item { display: flex; align-items: center; gap: 10px; font-family: 'Poppins', sans-serif; }
    .senti-legend-dot   { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .senti-legend-label { font-size: 12px; font-weight: 600; color: var(--text-primary); flex: 1; }
    .senti-legend-val   { font-size: 12px; font-weight: 700; color: var(--text-primary); }
    .senti-legend-pct   { font-size: 11px; color: var(--text-secondary); width: 38px; text-align: right; }

    @media (max-width: 1100px) { .charts-row { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 700px)  { .charts-row { grid-template-columns: 1fr; } }


    /* ── Map scroll overlay ── */
    .map-scroll-overlay {
        position: absolute;
        inset: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .map-scroll-overlay.visible { opacity: 1; }

    .map-scroll-hint {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        background: rgba(0,0,0,0.65);
        backdrop-filter: blur(6px);
        color: #fff;
        padding: 20px 32px;
        border-radius: 16px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.2px;
        pointer-events: none;
    }

    /* Leaflet */
    .circle-label { pointer-events: none !important; }
    .circle-label div { display: flex; align-items: center; justify-content: center; height: 100%; }

    /* Mobile Responsive */
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 900px) {
        .map-with-panel { flex-direction: column; }
        .location-panel { width: 100%; border-left: none; border-top: 1px solid var(--border-gray); }
        .location-list  { max-height: 200px; }
    }
    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: 1fr; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-range-wrapper { flex-direction: column; }
        .apply-btn { width: 100%; justify-content: center; }

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
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <h1>X Geographic</h1>
        <p>Monitor geographic distribution and location-based analytics for X (Twitter)</p>
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

    @include('mk.layouts.partials.filter-datepicker')

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
                    <p class="do-card-subtitle">X users by country and province</p>
                </div>
            </div>
            <span class="do-badge">All Users</span>
        </div>
        <div class="map-with-panel">
            <!-- Map -->
            <div class="map-area">
                <div id="geoMap" style="width:100%; height:500px;"></div>
                <div class="do-skeleton-map">
                    <div class="skeleton-map-placeholder"></div>
                </div>
            </div>
            <!-- Location list panel -->
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
                <div id="geoSentimentMap" style="width:100%; height:500px;"></div>
                <div class="do-skeleton-map">
                    <div class="skeleton-map-placeholder"></div>
                </div>
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

    <!-- ── 3 Chart Cards Row ── -->
    <div class="charts-row">

        <!-- Card 1: Top Countries Bar -->
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
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                </div>
                <div id="chartCountries"></div>
            </div>
        </div>

        <!-- Card 2: Top Provinces Bar -->
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
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                </div>
                <div id="chartProvinces"></div>
            </div>
        </div>

        <!-- Card 3: Sentiment Donut -->
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
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
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
        <div class="do-card-body do-body-scroll" style="max-height: unset; padding: 0 24px 24px;">
            <div class="do-skeleton" style="padding: 20px 0;">
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
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
// ========================================
// X GEOGRAPHIC LOADER
// ========================================
const XGeoLazyLoader = {
    projectId: '{{ $projectId ?? "" }}',
    startDate:  '{{ $startDate ?? "" }}',
    endDate:    '{{ $endDate   ?? "" }}',
    loadedSections: new Set(),
    _geoUserCache: null,
    _allLocations: [],

    init() {
        this.setupIntersectionObserver();
    },

    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el      = entry.target;
                const section = el.dataset.lazy;
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
                case 'stat-geo':         await this.loadStats(el);          break;
                case 'geo-user-map':     await this.loadGeoUserMap(el);     break;
                case 'geo-sentiment-map':await this.loadGeoSentimentMap(el);break;
                case 'top-locations':    await this.loadTopLocations(el);   break;
                case 'chart-countries':      await this.loadChartCountries(el);     break;
                case 'chart-provinces':      await this.loadChartProvinces(el);     break;
                case 'chart-sentiment-donut':await this.loadChartSentimentDonut(el);break;
            }
            el.dataset.loaded = 'true';
        } catch (err) {
            console.error(`❌ Failed to load ${section}:`, err);
        }
    },

    // ── Cached geo-user fetch ────────────────────────────────────────────
    async fetchGeoUser() {
        if (this._geoUserCache) return this._geoUserCache;
        const res  = await fetch(`/mk/api/x/geo-user?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const json = await res.json();
        console.log('📍 geoUser raw response:', json);
        this._geoUserCache = json;
        return json;
    },

    // ── Parse geo rows from various API shapes ───────────────────────────
    // Controller wraps as: { success: true, data: <raw MediaKernels response> }
    // MediaKernels may return:
    //   Shape A: { country: { rows: [...], total: N } }   ← most common
    //   Shape B: { rows: [...], total: N }
    //   Shape C: [...] flat array
    parseGeoRows(result) {
        if (!result || !result.success) return [];
        const d = result.data;
        if (!d) return [];

        // Shape A
        if (d.country && Array.isArray(d.country.rows)) return d.country.rows;

        // Shape B
        if (Array.isArray(d.rows)) return d.rows;

        // Shape C — flat array
        if (Array.isArray(d)) return d;

        // Shape D — object whose values are counts (e.g. { "Jakarta": 120, ... })
        if (typeof d === 'object') {
            const entries = Object.entries(d);
            if (entries.length && typeof entries[0][1] === 'number') {
                return entries.map(([name, count]) => ({ name, count }));
            }
        }

        console.warn('⚠️ Unknown geoUser response shape:', d);
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
            const detail    = topRow.detail || {};
            const provinces = Object.entries(detail).sort((a, b) => b[1] - a[1]);
            document.getElementById('topProvince').textContent = provinces.length ? provinces[0][0] : 'N/A';
        } else {
            document.getElementById('topCountry').textContent  = 'N/A';
            document.getElementById('topProvince').textContent = 'N/A';
        }

        // Mark all stat cards loaded at once
        document.querySelectorAll('[data-lazy="stat-geo"]').forEach(c => {
            c.dataset.loaded = 'true';
        });
    },

    // ── Geo User Map ─────────────────────────────────────────────────────
    async loadGeoUserMap(card) {
        const result = await this.fetchGeoUser();
        const rows   = this.parseGeoRows(result);

        const markers = this.renderMap('geoMap', rows, (p) => {
            const count = parseInt(p.count || 0);
            const name  = p.name || 'Unknown';
            return {
                color: '#038047',
                count,
                popup: `
                    <div style="font-family:Poppins;text-align:center;padding:8px;">
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
        const res    = await fetch(`/mk/api/x/geo-sentiment?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();
        console.log('📍 geoSentiment raw response:', result);

        // Use same defensive parser
        const rows = this.parseGeoRows(result);

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
                color,
                count,
                sentiment,
                popup: `
                    <div style="font-family:Poppins;text-align:center;padding:8px;">
                        <div style="font-weight:700;font-size:15px;color:#1a202c;margin-bottom:6px;">${name}</div>
                        <div style="display:inline-block;padding:4px 12px;background:${color}20;border-radius:12px;margin-bottom:8px;">
                            <span style="font-size:10px;font-weight:700;color:${color};text-transform:uppercase;">${sentiment}</span>
                        </div>
                        <div style="font-size:24px;font-weight:800;color:${color};margin-bottom:2px;">${count.toLocaleString()}</div>
                        <div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.8px;font-weight:600;">mentions</div>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:12px;border-top:1px solid #e2e8f0;padding-top:12px;">
                            <div style="text-align:center;padding:6px;background:#f0fdf4;border-radius:6px;">
                                <div style="font-size:16px;font-weight:700;color:#22c55e;">${pos}</div>
                                <div style="font-size:9px;color:#64748b;text-transform:uppercase;font-weight:600;">Positive</div>
                                <div style="font-size:8px;color:#64748b;">${((pos/safe)*100).toFixed(1)}%</div>
                            </div>
                            <div style="text-align:center;padding:6px;background:#f8fafc;border-radius:6px;">
                                <div style="font-size:16px;font-weight:700;color:#64748b;">${net}</div>
                                <div style="font-size:9px;color:#64748b;text-transform:uppercase;font-weight:600;">Neutral</div>
                                <div style="font-size:8px;color:#64748b;">${((net/safe)*100).toFixed(1)}%</div>
                            </div>
                            <div style="text-align:center;padding:6px;background:#fef2f2;border-radius:6px;">
                                <div style="font-size:16px;font-weight:700;color:#ef4444;">${neg}</div>
                                <div style="font-size:9px;color:#64748b;text-transform:uppercase;font-weight:600;">Negative</div>
                                <div style="font-size:8px;color:#64748b;">${((neg/safe)*100).toFixed(1)}%</div>
                            </div>
                        </div>
                    </div>`
            };
        });

        this.buildLocationPanel('geoSentimentList', rows, markers, null, true);
    },

    // ── SHARED renderMap — returns map instance for flyTo ────────────────
    renderMap(elementId, rows, getMarkerProps) {
        const map = L.map(elementId, { 
            center: [-2.5, 118], 
            zoom: 5,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, © <a href="https://carto.com/">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        // Ctrl+scroll to zoom overlay
        const mapEl = document.getElementById(elementId);
        const overlay = document.createElement('div');
        overlay.className = 'map-scroll-overlay';
       
        mapEl.style.position = 'relative';
        mapEl.appendChild(overlay);

        // Show overlay on scroll without ctrl
        mapEl.addEventListener('wheel', function(e) {
            if (!e.ctrlKey) {
                overlay.classList.add('visible');
                clearTimeout(overlay._hideTimer);
                overlay._hideTimer = setTimeout(() => overlay.classList.remove('visible'), 1800);
            } else {
                map.scrollWheelZoom.enable();
                overlay.classList.remove('visible');
            }
        });

        // Re-disable after ctrl+scroll stops
        map.on('zoomend', () => {
            setTimeout(() => map.scrollWheelZoom.disable(), 300);
        });

        if (!rows.length) return { map, markerRefs: [] };

        const maxCount   = Math.max(...rows.map(p => parseInt(p.count || 0)));
        const markerRefs = [];

        rows.forEach((p, i) => {
            const lat = parseFloat(p.latitude  || 0);
            const lng = parseFloat(p.longitude || 0);
            if (lat === 0 && lng === 0) {
                markerRefs.push(null); // keep index in sync
                return;
            }

            const { color, count, popup } = getMarkerProps(p);

            // Filled circle
            if (count >= 10) {
                let radius = Math.sqrt(count) * 2500;
                radius = Math.max(radius, 5000);
                radius = Math.min(radius, 50000);
                const opacity = Math.min(0.15 + (count / maxCount) * 0.45, 0.6);
                L.circle([lat, lng], { radius, fillColor: color, color, weight: 1, opacity: 0.3, fillOpacity: opacity }).addTo(map);
            }

            // Pin marker — store reference for flyTo
            const pin = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="width:13px;height:13px;background:${color};border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>`,
                    iconSize: [13, 13], iconAnchor: [6.5, 6.5]
                })
            }).addTo(map).bindPopup(popup);

            markerRefs.push({ marker: pin, lat, lng });

            // Count label
            const label    = count > 999 ? (count / 1000).toFixed(1) + 'k' : String(count);
            const fontSize = count >= 1000 ? '13px' : '11px';
            L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'circle-label',
                    html: `<div style="font-family:Poppins;font-size:${fontSize};font-weight:900;color:#fff;background:${color};padding:3px 8px;border-radius:12px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4);white-space:nowrap;">${label}</div>`,
                    iconSize: [40, 20], iconAnchor: [20, 25]
                }),
                interactive: false
            }).addTo(map);
        });

        return { map, markerRefs };
    },

    // ── Location sidebar panel builder ───────────────────────────────────
    buildLocationPanel(listId, rows, mapResult, defaultColor, useSentimentColor) {
        const listEl = document.getElementById(listId);
        if (!listEl) return;

        const { map, markerRefs } = mapResult;

        // Filter rows with valid coordinates
        const validRows = rows.filter((p, i) => {
            const lat = parseFloat(p.latitude  || 0);
            const lng = parseFloat(p.longitude || 0);
            return !(lat === 0 && lng === 0);
        });

        if (!validRows.length) {
            listEl.innerHTML = '<div class="do-empty" style="padding:24px 14px;font-size:12px;">No location data</div>';
            return;
        }

        // Sort by count desc for display
        const sorted = [...validRows].sort((a, b) => parseInt(b.count || 0) - parseInt(a.count || 0));

        let html = '';
        sorted.forEach((p, rank) => {
            const name  = p.name  || 'Unknown';
            const count = parseInt(p.count || 0);

            // Determine color
            let color = defaultColor || '#038047';
            if (useSentimentColor) {
                const pos = parseInt(p.pos || 0);
                const neg = parseInt(p.neg || 0);
                const net = parseInt(p.net || 0);
                if (pos > neg && pos > net) color = '#22c55e';
                else if (neg > pos && neg > net) color = '#ef4444';
                else color = '#64748b';
            }

            const label = count > 999 ? (count / 1000).toFixed(1) + 'k' : count;

            html += `
                <div class="location-item" data-index="${rank}" data-name="${name}">
                    <span class="location-item-rank">${rank + 1}</span>
                    <div class="location-item-info">
                        <div class="location-item-name" title="${name}">${name}</div>
                        <div class="location-item-count">${label} ${useSentimentColor ? 'mentions' : 'users'}</div>
                    </div>
                    <div class="location-item-dot" style="background:${color};"></div>
                </div>`;
        });

        listEl.innerHTML = html;

        // Click → fly to marker on map
        listEl.querySelectorAll('.location-item').forEach(item => {
            item.addEventListener('click', () => {
                const name = item.dataset.name;

                // Find matching row
                const targetRow = validRows.find(p => (p.name || 'Unknown') === name);
                if (!targetRow) return;

                const lat = parseFloat(targetRow.latitude  || 0);
                const lng = parseFloat(targetRow.longitude || 0);
                if (lat === 0 && lng === 0) return;

                // Fly to location
                map.flyTo([lat, lng], 8, { animate: true, duration: 1 });

                // Open matching marker popup
                const ref = markerRefs.find(r => r && Math.abs(r.lat - lat) < 0.001 && Math.abs(r.lng - lng) < 0.001);
                if (ref) setTimeout(() => ref.marker.openPopup(), 800);

                // Highlight active item
                listEl.querySelectorAll('.location-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });
    },

    // ── Top Locations Table ───────────────────────────────────────────────
    async loadTopLocations(card) {
        const res    = await fetch(`/mk/api/x/top-locations?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();
        console.log('📍 topLocations raw response:', result);

        let locations = [];

        // Try primary API first — filter out Unknown / garbled entries
        if (result.success && Array.isArray(result.data)) {
            locations = result.data.filter(l => {
                const n = (l.name || l.location || '').trim();
                return n && n !== 'Unknown' && !n.startsWith('\u0000');
            }).map(l => ({
                name:  l.name || l.location || 'Unknown',
                count: parseInt(l.count || l.total || 0)
            }));
        }

        // Fallback: build from geoUser country + province detail
        if (!locations.length) {
            const geo  = await this.fetchGeoUser();
            const rows = this.parseGeoRows(geo);

            rows.forEach(country => {
                // Country level
                const cName = (country.name || '').trim();
                if (cName && cName !== 'Unknown') {
                    locations.push({ name: cName, count: parseInt(country.count || 0) });
                }
                // Province level from detail
                if (country.detail && typeof country.detail === 'object') {
                    Object.entries(country.detail)
                        .filter(([k]) => k && !k.startsWith('\u0000') && k.trim())
                        .forEach(([name, val]) => {
                            const count = typeof val === 'number' ? val : parseInt(val?.count || 0);
                            if (count > 0) locations.push({ name: name.trim(), count });
                        });
                }
            });

            // Sort desc
            locations.sort((a, b) => b.count - a.count);
        }

        const tableEl = document.getElementById('topLocationsTable');

        if (!locations.length) {
            tableEl.innerHTML = '<div class="do-empty">No data available</div>';
            return;
        }

        // Store full list for modal
        this._allLocations = locations;

        // Show View All button if > 10
        if (locations.length > 10) {
            const btnWrapper = card.querySelector('.do-card-head > div:last-child');
            if (btnWrapper && !btnWrapper.querySelector('.view-all-btn')) {
                btnWrapper.innerHTML += `
                    <button class="view-all-btn" onclick="XGeoLazyLoader.openModal()">
                        View All
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;"><path d="M9 18l6-6-6-6"/></svg>
                    </button>`;
            }
        }

        tableEl.innerHTML = this._buildTable(locations.slice(0, 10), 0);
    },

    _buildTable(items, offset) {
        let html = `
            <table class="do-tbl" style="margin-top:8px;">
                <thead><tr>
                    <th style="width:40px;">#</th>
                    <th>Location</th>
                    <th style="text-align:right;">Authors</th>
                </tr></thead>
                <tbody>`;
        items.forEach((loc, i) => {
            html += `
                <tr>
                    <td class="do-tbl-rank">${offset + i + 1}</td>
                    <td class="do-tbl-name">${loc.name}</td>
                    <td class="do-tbl-num">${loc.count.toLocaleString()}</td>
                </tr>`;
        });
        html += '</tbody></table>';
        return html;
    },

    openModal() {
        // Build modal if not exists
        if (!document.getElementById('geoLocModal')) {
            const m = document.createElement('div');
            m.id = 'geoLocModal';
            m.style.cssText = 'display:none;position:fixed;z-index:9999;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(8px);align-items:center;justify-content:center;';
            m.innerHTML = `
                <div style="background:#fff;border-radius:16px;width:90%;max-width:560px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 50px rgba(0,0,0,0.4);animation:modalIn .25s ease-out;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:2px solid #f8fafc;">
                        <h3 style="font-size:18px;font-weight:700;color:#1a202c;font-family:Poppins,sans-serif;margin:0;">All Author Locations</h3>
                        <button onclick="XGeoLazyLoader.closeModal()" style="width:34px;height:34px;border-radius:8px;background:#f8fafc;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;" onmouseover="this.style.background='#ef4444';this.style.color='#fff'" onmouseout="this.style.background='#f8fafc';this.style.color='#64748b'">
                            <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;stroke-width:2.5;fill:none;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div id="geoLocModalBody" style="padding:16px 24px 24px;overflow-y:auto;flex:1;"></div>
                </div>`;
            m.addEventListener('click', e => { if (e.target === m) this.closeModal(); });
            document.body.appendChild(m);

            // Add animation keyframe once
            if (!document.getElementById('geoModalStyle')) {
                const s = document.createElement('style');
                s.id = 'geoModalStyle';
                s.textContent = '@keyframes modalIn{from{transform:translateY(-16px) scale(0.96);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}';
                document.head.appendChild(s);
            }
        }

        document.getElementById('geoLocModalBody').innerHTML = this._buildTable(this._allLocations, 0);
        const modal = document.getElementById('geoLocModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        document.addEventListener('keydown', this._escHandler = (e) => {
            if (e.key === 'Escape') this.closeModal();
        });
    },

    // ── Chart: Top Countries ────────────────────────────────────────────
    async loadChartCountries(card) {
        const result = await this.fetchGeoUser();
        const rows   = this.parseGeoRows(result);
        const el     = document.getElementById('chartCountries');
        if (!el || !rows.length) return;

        const top  = rows.slice(0, 6);
        const max  = parseInt(top[0]?.count) || 1;
        const colors = ['#038047','#059669','#0891b2','#7c3aed','#db2777','#ea580c'];

        el.innerHTML = top.map((row, i) => {
            const count  = parseInt(row.count);
            const logPct = Math.round((Math.log(count + 1) / Math.log(max + 1)) * 100);
            const pct    = Math.max(logPct, 6);
            const color  = colors[i % colors.length];
            return `
                <div class="country-bar-row">
                    <div class="country-bar-header">
                        <span class="country-bar-name">${row.name}</span>
                        <span class="country-bar-count">${count.toLocaleString()}</span>
                    </div>
                    <div class="country-bar-track">
                        <div class="country-bar-fill" data-pct="${pct}" style="background:${color};"></div>
                    </div>
                </div>`;
        }).join('');

        // Animate bars
        requestAnimationFrame(() => {
            el.querySelectorAll('.country-bar-fill').forEach(bar => {
                bar.style.width = bar.dataset.pct + '%';
            });
        });
    },

    // ── Chart: Top Provinces ─────────────────────────────────────────────
    async loadChartProvinces(card) {
        const result = await this.fetchGeoUser();
        const rows   = this.parseGeoRows(result);
        const el     = document.getElementById('chartProvinces');
        if (!el || !rows.length) return;

        // Use top country's detail
        const topCountry = rows[0];
        if (!topCountry?.detail) {
            el.innerHTML = '<div class="do-empty" style="padding:32px 0;">No province data</div>';
            return;
        }

        document.getElementById('provSubtitle').textContent = topCountry.name + ' provinces';

        const provinces = Object.entries(topCountry.detail)
            .filter(([k]) => k && !k.startsWith('\u0000') && k.trim().length > 0)
            .map(([name, count]) => ({ name, count: parseInt(count) }))
            .sort((a, b) => b.count - a.count)
            .slice(0, 8);

        const max = provinces[0]?.count || 1;

        el.innerHTML = provinces.map(p => {
            const pct = Math.round((p.count / max) * 100);
            return `
                <div class="prov-bar-row">
                    <div class="prov-bar-header">
                        <span class="prov-bar-name">${p.name}</span>
                        <span class="prov-bar-count">${p.count.toLocaleString()}</span>
                    </div>
                    <div class="prov-bar-track">
                        <div class="prov-bar-fill" data-pct="${pct}"></div>
                    </div>
                </div>`;
        }).join('');

        requestAnimationFrame(() => {
            el.querySelectorAll('.prov-bar-fill').forEach(bar => {
                bar.style.width = bar.dataset.pct + '%';
            });
        });
    },

    // ── Chart: Sentiment Donut ───────────────────────────────────────────
    async loadChartSentimentDonut(card) {
        const res    = await fetch(`/mk/api/x/geo-sentiment?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();
        const rows   = this.parseGeoRows(result);

        let totalPos = 0, totalNeg = 0, totalNet = 0;
        rows.forEach(r => {
            totalPos += parseInt(r.pos || 0);
            totalNeg += parseInt(r.neg || 0);
            totalNet += parseInt(r.net || 0);
        });

        const total = totalPos + totalNeg + totalNet || 1;
        const canvasEl = document.getElementById('chartSentimentDonut');
        const legendEl = document.getElementById('chartSentimentLegend');

        if (!canvasEl) return;

        // If no Chart.js (shouldn't happen), fallback
        const canvas = document.createElement('canvas');
        canvas.width  = 180;
        canvas.height = 180;
        canvasEl.appendChild(canvas);

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [totalPos, totalNet, totalNeg],
                    backgroundColor: ['#22c55e', '#94a3b8', '#ef4444'],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: false,
                cutout: '62%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString()} (${((ctx.parsed/total)*100).toFixed(1)}%)`
                        }
                    }
                },
                animation: { animateRotate: true, duration: 900 }
            }
        });

        // Legend
        const items = [
            { label: 'Positive', val: totalPos, color: '#22c55e' },
            { label: 'Neutral',  val: totalNet,  color: '#94a3b8' },
            { label: 'Negative', val: totalNeg, color: '#ef4444' },
        ];
        if (legendEl) {
            legendEl.innerHTML = items.map(item => `
                <div class="senti-legend-item">
                    <div class="senti-legend-dot" style="background:${item.color};"></div>
                    <span class="senti-legend-label">${item.label}</span>
                    <span class="senti-legend-val">${item.val.toLocaleString()}</span>
                    <span class="senti-legend-pct">${((item.val/total)*100).toFixed(1)}%</span>
                </div>`).join('');
        }
    },

    closeModal() {
        const modal = document.getElementById('geoLocModal');
        if (modal) modal.style.display = 'none';
        document.body.style.overflow = '';
        if (this._escHandler) document.removeEventListener('keydown', this._escHandler);
    }
};

document.addEventListener('DOMContentLoaded', () => XGeoLazyLoader.init());
</script>
@endsection