@extends('mk.layouts.app')

@section('title', 'X Geographic - SMADIMENT')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════
   DESIGN TOKENS — 100% konsisten dengan TikTok Overview
══════════════════════════════════════════════════════ */
:root {
    --primary        : #038047;
    --primary-rgb    : 3, 128, 71;
    --primary-lt     : rgba(3,128,71,.10);
    --dark           : #273B4A;
    --white          : #FFFFFF;
    --bg             : #F1F5F8;

    --green          : #038047;
    --green-light    : #E8F5EE;
    --red            : #EF4444;
    --red-light      : #FEF2F2;
    --amber          : #F59E0B;
    --amber-light    : #FFFBEB;
    --cyan           : #06B6D4;
    --cyan-light     : #ECFEFF;

    --slate-50       : #F8FAFC;
    --slate-100      : #F1F5F9;
    --slate-200      : #E2E8F0;
    --slate-300      : #CBD5E1;
    --slate-400      : #94A3B8;
    --slate-500      : #64748B;
    --slate-600      : #475569;
    --slate-700      : #334155;
    --slate-800      : #1E293B;
    --slate-900      : #0F172A;

    --radius         : 8px;
    --radius-sm      : 5px;
    --shadow-sm      : 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
    --shadow-md      : 0 4px 14px rgba(15,23,42,.08);
    --shadow-lg      : 0 10px 30px rgba(15,23,42,.12);
}

/* ══ Animations ══ */
@keyframes fadeUp   { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer  { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin     { to{transform:rotate(360deg)} }
@keyframes modalIn  { from{transform:translateY(-16px) scale(.96);opacity:0} to{transform:translateY(0) scale(1);opacity:1} }
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0)} 30%{transform:scale(1.25) rotate(-10deg)} 60%{transform:scale(1.1) rotate(6deg)} }
@keyframes kpiShimmer    { 0%{left:-100%} 100%{left:150%} }

/* ══ Layout ══ */
body { background: var(--bg); }
.dashboard-container { padding: 24px; max-width: 1600px; margin: 0 auto; }

/* ══ Page header ══ */
.page-header { margin-bottom: 24px; }
.page-header h1 { font-size: 22px; font-weight: 700; color: var(--slate-900); margin: 0 0 4px 0; }
.page-header p  { font-size: 13px; color: var(--slate-400); margin: 0; font-weight: 500; }

/* ══ Alert ══ */
.alert {
    padding: 14px 18px; border-radius: var(--radius); margin-bottom: 20px;
    font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;
    animation: fadeUp .3s ease-out;
}
.alert-warning { background: #fffbeb; color: #92400e; border: 1px solid #fcd34d; }
.alert svg { flex-shrink: 0; width: 18px; height: 18px; }

/* ══ KPI Icon bg (same as TikTok Overview) ══ */
.kpi-icon-bg {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.2); font-size: 24px; color: #fff; flex-shrink: 0;
}

/* ══ KPI Card Hover (identical to TikTok Overview) ══ */
.kpi-card-hover {
    will-change: transform, box-shadow; cursor: default;
    position: relative !important; overflow: hidden !important;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1) !important,
                box-shadow .25s ease !important, filter .25s ease !important;
}
.kpi-card-hover::before {
    content: ''; position: absolute; top: 0; bottom: 0; left: -100%; width: 60%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.22), transparent);
    pointer-events: none; z-index: 1;
}
.kpi-card-hover:hover {
    transform: translateY(-6px) scale(1.025) !important;
    box-shadow: 0 20px 40px rgba(0,0,0,.25) !important;
    filter: brightness(1.07) !important;
}
.kpi-card-hover:hover::before { animation: kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background: rgba(255,255,255,.35) !important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation: kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important; display: inline-block !important; }
.kpi-card-hover:active { transform: translateY(-2px) scale(1.01) !important; transition-duration: .08s !important; }
.kpi-card-hover h3 { font-size: 1.5rem; }

/* ══ Skeleton (same as TikTok Overview) ══ */
.sk-block {
    border-radius: 4px;
    background: linear-gradient(90deg, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%);
    background-size: 200% 100%; animation: shimmer 1.4s infinite;
}

/* ══ Spinner ══ */
.spin-ring {
    width: 26px; height: 26px;
    border: 2.5px solid var(--slate-100); border-top-color: var(--primary);
    border-radius: 50%; animation: spin .65s linear infinite;
}
.spinner-state {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 48px 20px; gap: 12px; color: var(--slate-400); font-size: 12px; font-weight: 600;
}

/* ══ Geo Card (uses Bootstrap .card base like TikTok Overview) ══ */
.geo-card {
    background: #fff; border: 1px solid var(--slate-200); border-radius: var(--radius);
    box-shadow: var(--shadow-sm); overflow: hidden;
    margin-bottom: 20px;
    animation: fadeUp .38s ease-out both;
}
.geo-card-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    padding: 14px 18px; border-bottom: 1px solid var(--slate-100); background: #fff;
}
.geo-card-header-left { display: flex; align-items: center; gap: 10px; }
.geo-avtar {
    width: 38px; height: 38px; border-radius: var(--radius-sm); flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: var(--primary-lt);
}
.geo-avtar i { font-size: 18px; color: var(--primary); }
.geo-avtar svg { width: 20px; height: 20px; stroke: var(--primary); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.geo-card-title    { font-size: 14px; font-weight: 700; color: var(--slate-900); margin: 0 0 2px; }
.geo-card-subtitle { font-size: 12px; color: var(--slate-400); font-weight: 500; margin: 0; }

/* ══ Badge (identical to TikTok Overview) ══ */
.geo-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 20px; height: 22px; padding: 0 8px;
    border-radius: 3px; font-size: 10px; font-weight: 800; letter-spacing: .3px;
    background: var(--primary-lt); color: var(--primary); text-transform: uppercase;
}

/* ══ Map + Location Panel Layout ══ */
.map-with-panel { display: flex; }
.map-area { flex: 1; min-width: 0; position: relative; }
.location-panel {
    width: 220px; flex-shrink: 0;
    border-left: 1px solid var(--slate-100);
    display: flex; flex-direction: column; background: #fff;
}
.location-panel-title {
    padding: 12px 14px 10px;
    font-size: 10px; font-weight: 700; color: var(--slate-400);
    text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid var(--slate-100);
}
.location-list { overflow-y: auto; flex: 1; max-height: 500px; }
.location-list::-webkit-scrollbar { width: 4px; }
.location-list::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }
.location-list::-webkit-scrollbar-thumb:hover { background: var(--slate-400); }

/* ── Location Item (same density/style as ht-item in TikTok Overview) ── */
.location-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 14px; border-bottom: 1px solid var(--slate-100);
    cursor: pointer; transition: background .12s;
}
.location-item:last-child { border-bottom: none; }
.location-item:hover { background: var(--slate-50); }
.location-item.active {
    background: var(--primary-lt);
    border-left: 3px solid var(--primary);
    padding-left: 11px;
}
.location-item-rank {
    width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 800; color: var(--slate-400);
    background: var(--slate-100); border: 1px solid var(--slate-200);
}
.location-item-rank--1 { background: linear-gradient(135deg,#ffd700,#F59E0B); color: #7c5900; border-color: #ffd700; }
.location-item-rank--2 { background: linear-gradient(135deg,#c0c0c0,#9ca3af); color: #3d3d3d; border-color: #c0c0c0; }
.location-item-rank--3 { background: linear-gradient(135deg,#cd7f32,#b06820); color: #fff; border-color: #cd7f32; }
.location-item-info { flex: 1; min-width: 0; }
.location-item-name {
    font-size: 12px; font-weight: 700; color: var(--slate-900);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.location-item-count { font-size: 11px; color: var(--slate-400); font-weight: 600; margin-top: 1px; }
.location-item-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* ── Skeleton inside map/panel ── */
.map-skeleton { position: absolute; inset: 0; z-index: 2; pointer-events: none; }
.map-skeleton-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--slate-50) 25%, var(--slate-200) 50%, var(--slate-50) 75%);
    background-size: 200% 100%; animation: shimmer 1.4s infinite;
}
.panel-skeleton { padding: 10px 14px; }
.panel-skeleton .sk-line {
    height: 18px; border-radius: 4px; margin-bottom: 8px;
    background: linear-gradient(90deg, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%);
    background-size: 200% 100%; animation: shimmer 1.4s infinite;
}
[data-loaded="true"] .map-skeleton,
[data-loaded="true"] .panel-skeleton { display: none; }

/* ══ Scroll overlay (Ctrl+scroll hint) ══ */
.map-scroll-overlay {
    position: absolute; inset: 0; z-index: 1000;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none; opacity: 0; transition: opacity .2s;
}
.map-scroll-overlay.visible { opacity: 1; }
.map-scroll-hint {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    background: rgba(15,23,42,.75); backdrop-filter: blur(6px); color: #fff;
    padding: 16px 28px; border-radius: var(--radius);
    font-size: 13px; font-weight: 700; letter-spacing: .2px; pointer-events: none;
}

/* ══ Charts row (3 cols) ══ */
.charts-row {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    gap: 20px; margin-bottom: 20px;
}

/* ══ Bar rows — Countries ══ */
.country-bar-row { margin-bottom: 12px; }
.country-bar-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 5px;
}
.country-bar-name  { font-size: 12px; font-weight: 700; color: var(--slate-800); }
.country-bar-count { font-size: 12px; font-weight: 700; color: var(--slate-500); }
.country-bar-track { height: 8px; background: var(--slate-100); border-radius: 99px; overflow: hidden; }
.country-bar-fill  {
    height: 100%; border-radius: 99px;
    transition: width .9s cubic-bezier(.4,0,.2,1); width: 0;
}

/* ══ Bar rows — Provinces ══ */
.prov-bar-row { margin-bottom: 10px; }
.prov-bar-header {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;
}
.prov-bar-name  { font-size: 11px; font-weight: 700; color: var(--slate-700); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%; }
.prov-bar-count { font-size: 11px; font-weight: 700; color: var(--slate-500); }
.prov-bar-track { height: 7px; background: var(--slate-100); border-radius: 99px; overflow: hidden; }
.prov-bar-fill  {
    height: 100%; border-radius: 99px;
    background: linear-gradient(90deg, var(--primary), rgba(3,128,71,.5));
    transition: width .8s cubic-bezier(.4,0,.2,1); width: 0;
}

/* ══ Sentiment legend ══ */
.senti-legend { display: flex; flex-direction: column; gap: 8px; margin-top: 14px; width: 100%; max-width: 260px; }
.senti-legend-item { display: flex; align-items: center; gap: 8px; }
.senti-legend-dot   { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.senti-legend-label { font-size: 12px; font-weight: 700; color: var(--slate-800); flex: 1; }
.senti-legend-val   { font-size: 12px; font-weight: 700; color: var(--slate-800); }
.senti-legend-pct   { font-size: 11px; color: var(--slate-400); width: 40px; text-align: right; font-weight: 600; }

/* ══ Table (identical to TikTok panel tables) ══ */
.geo-tbl { width: 100%; border-collapse: separate; border-spacing: 0; }
.geo-tbl thead tr { background: #fff; }
.geo-tbl th {
    padding: 9px 12px; font-size: 10px; font-weight: 700; color: var(--slate-400);
    text-transform: uppercase; letter-spacing: .4px;
    border-bottom: 1px solid var(--slate-200); text-align: left;
}
.geo-tbl td { padding: 11px 12px; font-size: 13px; color: var(--slate-800); border-bottom: 1px solid var(--slate-100); }
.geo-tbl tbody tr { transition: background .12s; background: #fff; }
.geo-tbl tbody tr:hover { background: var(--slate-50); }
.geo-tbl tr:last-child td { border-bottom: none; }
.geo-tbl-rank { font-weight: 800; color: var(--primary); width: 28px; font-size: 12px; }
.geo-tbl-name { font-weight: 700; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.geo-tbl-num  { text-align: right; font-weight: 700; font-size: 13px; color: var(--slate-800); }
.geo-empty    { font-size: 13px; color: var(--slate-400); text-align: center; padding: 48px 20px; font-weight: 600; }

/* ── View All button ── */
.view-all-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; background: #fff; color: var(--slate-600);
    border: 1px solid var(--slate-200); border-radius: var(--radius-sm);
    font-size: 11px; font-weight: 700; cursor: pointer; transition: all .12s;
}
.view-all-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-lt); }

/* ══ Modal (same as TikTok Overview modals) ══ */
.geo-modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 9000;
    background: rgba(15,23,42,.55); backdrop-filter: blur(6px);
    align-items: center; justify-content: center;
}
.geo-modal-overlay.open { display: flex; }
.geo-modal {
    background: #fff; border-radius: var(--radius); width: 90%; max-width: 540px;
    max-height: 85vh; display: flex; flex-direction: column;
    box-shadow: 0 25px 50px rgba(0,0,0,.35);
    animation: modalIn .25s ease-out;
}
.geo-modal-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 16px 20px; border-bottom: 1px solid var(--slate-100);
}
.geo-modal-header h3 { font-size: 15px; font-weight: 700; color: var(--slate-900); margin: 0; }
.geo-modal-close {
    width: 30px; height: 30px; border-radius: var(--radius-sm);
    background: var(--slate-50); border: 1px solid var(--slate-200);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: var(--slate-500); font-size: 15px; transition: all .12s;
}
.geo-modal-close:hover { background: var(--red); border-color: var(--red); color: #fff; }
.geo-modal-body { padding: 12px 20px 20px; overflow-y: auto; flex: 1; }
.geo-modal-body::-webkit-scrollbar { width: 4px; }
.geo-modal-body::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }

/* Leaflet */
.circle-label { pointer-events: none !important; }
.circle-label div { display: flex; align-items: center; justify-content: center; height: 100%; }

/* ══ Responsive ══ */
@media(max-width:1100px) { .charts-row { grid-template-columns: 1fr 1fr; } }
@media(max-width:700px)  { .charts-row { grid-template-columns: 1fr; } }
@media(max-width:900px) {
    .map-with-panel { flex-direction: column; }
    .location-panel { width: 100%; border-left: none; border-top: 1px solid var(--slate-100); }
    .location-list  { max-height: 200px; }
}
@media(max-width:768px) {
    .dashboard-container { padding: 16px; }
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
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>No project selected. Please select a project from the sidebar to view geographic data.</span>
    </div>
    @else

    @include('mk.layouts.partials.filter-datepicker')

    {{-- ══ KPI Summary Cards (same markup & classes as TikTok Overview) ══ --}}
    <div class="row mb-3">
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 bg-primary text-white kpi-card-hover fade-up fade-up-d1">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Total Countries</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiCountries">
                                <span class="sk-block" style="width:70px;height:24px;display:inline-block;"></span>
                            </h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiCountriesSub">
                                <i class="ph ph-globe me-1"></i>Loading…
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg"><i class="ph ph-globe"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 bg-success text-white kpi-card-hover fade-up fade-up-d2">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Total Users</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiUsers">
                                <span class="sk-block" style="width:70px;height:24px;display:inline-block;"></span>
                            </h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiUsersSub">
                                <i class="ph ph-users me-1"></i>Loading…
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg"><i class="ph ph-users"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 bg-warning text-white kpi-card-hover fade-up fade-up-d3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Top Country</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiTopCountry" style="font-size:1.1rem;">
                                <span class="sk-block" style="width:90px;height:24px;display:inline-block;"></span>
                            </h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopCountrySub">
                                <i class="ph ph-map-pin me-1"></i>Loading…
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg"><i class="ph ph-map-pin"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 bg-danger text-white kpi-card-hover fade-up fade-up-d4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Top Province</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiTopProvince" style="font-size:1.1rem;">
                                <span class="sk-block" style="width:90px;height:24px;display:inline-block;"></span>
                            </h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopProvinceSub">
                                <i class="ph ph-buildings me-1"></i>Loading…
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg"><i class="ph ph-buildings"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Map 1: Geographic User Distribution ══ --}}
    <div class="geo-card" data-lazy="geo-user-map" style="animation-delay:.1s;">
        <div class="geo-card-header">
            <div class="geo-card-header-left">
                <div class="geo-avtar">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/></svg>
                </div>
                <div>
                    <p class="geo-card-title">Geographic User Distribution</p>
                    <p class="geo-card-subtitle">X users by country and province</p>
                </div>
            </div>
            <span class="geo-badge">All Users</span>
        </div>
        <div class="map-with-panel">
            <div class="map-area">
                <div id="geoMap" style="width:100%;height:500px;"></div>
                <div class="map-skeleton">
                    <div class="map-skeleton-fill"></div>
                </div>
            </div>
            <div class="location-panel">
                <div class="location-panel-title">📍 Locations</div>
                <div class="location-list" id="geoUserList">
                    <div class="panel-skeleton">
                        <div class="sk-line" style="width:90%;"></div>
                        <div class="sk-line" style="width:75%;"></div>
                        <div class="sk-line" style="width:85%;"></div>
                        <div class="sk-line" style="width:70%;"></div>
                        <div class="sk-line" style="width:80%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Map 2: Sentiment by Location ══ --}}
    <div class="geo-card" data-lazy="geo-sentiment-map" style="animation-delay:.15s;">
        <div class="geo-card-header">
            <div class="geo-card-header-left">
                <div class="geo-avtar">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </div>
                <div>
                    <p class="geo-card-title">Sentiment by Location</p>
                    <p class="geo-card-subtitle">Positive, negative, and neutral sentiment distribution</p>
                </div>
            </div>
            <span class="geo-badge">Sentiment</span>
        </div>
        <div class="map-with-panel">
            <div class="map-area">
                <div id="geoSentimentMap" style="width:100%;height:500px;"></div>
                <div class="map-skeleton">
                    <div class="map-skeleton-fill"></div>
                </div>
            </div>
            <div class="location-panel">
                <div class="location-panel-title">📍 Locations</div>
                <div class="location-list" id="geoSentimentList">
                    <div class="panel-skeleton">
                        <div class="sk-line" style="width:90%;"></div>
                        <div class="sk-line" style="width:75%;"></div>
                        <div class="sk-line" style="width:85%;"></div>
                        <div class="sk-line" style="width:70%;"></div>
                        <div class="sk-line" style="width:80%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ 3 Chart Cards Row ══ --}}
    <div class="charts-row">

        {{-- Card 1: Top Countries Bar --}}
        <div class="geo-card" data-lazy="chart-countries" style="animation-delay:.2s;">
            <div class="geo-card-header">
                <div class="geo-card-header-left">
                    <div class="geo-avtar">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <div>
                        <p class="geo-card-title">Top Countries</p>
                        <p class="geo-card-subtitle">Users by country</p>
                    </div>
                </div>
                <span class="geo-badge">Users</span>
            </div>
            <div style="padding:16px 18px 18px;">
                <div class="spinner-state" id="loadingChartCountries" style="padding:28px 0;">
                    <div class="spin-ring"></div><span>Loading…</span>
                </div>
                <div id="chartCountries" style="display:none;"></div>
            </div>
        </div>

        {{-- Card 2: Top Provinces Bar --}}
        <div class="geo-card" data-lazy="chart-provinces" style="animation-delay:.24s;">
            <div class="geo-card-header">
                <div class="geo-card-header-left">
                    <div class="geo-avtar">
                        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div>
                        <p class="geo-card-title">Top Provinces</p>
                        <p class="geo-card-subtitle" id="provSubtitle">Province breakdown</p>
                    </div>
                </div>
                <span class="geo-badge">Detail</span>
            </div>
            <div style="padding:16px 18px 18px;">
                <div class="spinner-state" id="loadingChartProvinces" style="padding:28px 0;">
                    <div class="spin-ring"></div><span>Loading…</span>
                </div>
                <div id="chartProvinces" style="display:none;"></div>
            </div>
        </div>

        {{-- Card 3: Sentiment Donut --}}
        <div class="geo-card" data-lazy="chart-sentiment-donut" style="animation-delay:.28s;">
            <div class="geo-card-header">
                <div class="geo-card-header-left">
                    <div class="geo-avtar">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                    </div>
                    <div>
                        <p class="geo-card-title">Sentiment Summary</p>
                        <p class="geo-card-subtitle">Overall distribution</p>
                    </div>
                </div>
                <span class="geo-badge">Sentiment</span>
            </div>
            <div style="padding:16px 18px 18px;display:flex;flex-direction:column;align-items:center;">
                <div class="spinner-state" id="loadingChartSentiment" style="width:100%;padding:28px 0;">
                    <div class="spin-ring"></div><span>Loading…</span>
                </div>
                <div id="chartSentimentDonut" style="position:relative;width:180px;height:180px;display:none;"></div>
                <div id="chartSentimentLegend" class="senti-legend"></div>
            </div>
        </div>

    </div>

    {{-- ══ Table: Top Author Locations ══ --}}
    <div class="geo-card" data-lazy="top-locations" style="animation-delay:.32s;">
        <div class="geo-card-header">
            <div class="geo-card-header-left">
                <div class="geo-avtar">
                    <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </div>
                <div>
                    <p class="geo-card-title">Top Author Locations</p>
                    <p class="geo-card-subtitle">Ranking of locations by author count</p>
                </div>
            </div>
            <div id="topLocBtnWrap" style="display:flex;align-items:center;gap:8px;">
                <span class="geo-badge">Rankings</span>
            </div>
        </div>
        <div style="padding:0 18px 18px;">
            <div class="spinner-state" id="loadingTopLocations" style="padding:28px 0;">
                <div class="spin-ring"></div><span>Loading…</span>
            </div>
            <div id="topLocationsTable"></div>
        </div>
    </div>

    @endif
</div>

{{-- ══ Modal: All Locations ══ --}}
<div class="geo-modal-overlay" id="geoLocModal" onclick="if(event.target===this)XGeo.closeModal()">
    <div class="geo-modal">
        <div class="geo-modal-header">
            <h3>All Author Locations</h3>
            <button class="geo-modal-close" onclick="XGeo.closeModal()">
                <i class="ph ph-x"></i>
            </button>
        </div>
        <div class="geo-modal-body" id="geoLocModalBody"></div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
'use strict';

/* ══════════════════════════════════════════════════════
   X GEOGRAPHIC — refactored to match TikTok Overview
══════════════════════════════════════════════════════ */
const XGeo = {
    projectId: '{{ $projectId ?? "" }}',
    startDate : '{{ $startDate ?? "" }}',
    endDate   : '{{ $endDate   ?? "" }}',
    _loaded   : new Set(),
    _geoUserCache : null,
    _allLocations : [],

    /* utils */
    numF : n => parseInt(n||0).toLocaleString('id-ID'),
    numK : n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); },
    _$ : id => document.getElementById(id),

    init() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target, sec = el.dataset.lazy;
                if (this._loaded.has(sec)) return;
                this._loaded.add(sec); observer.unobserve(el);
                this._load(sec, el);
            });
        }, { rootMargin: '100px', threshold: 0.05 });
        document.querySelectorAll('[data-lazy]').forEach(el => observer.observe(el));
    },

    async _load(section, el) {
        try {
            switch(section) {
                case 'geo-user-map'          : await this.loadGeoUserMap(el);       break;
                case 'geo-sentiment-map'     : await this.loadGeoSentimentMap(el);  break;
                case 'top-locations'         : await this.loadTopLocations(el);     break;
                case 'chart-countries'       : await this.loadChartCountries(el);   break;
                case 'chart-provinces'       : await this.loadChartProvinces(el);   break;
                case 'chart-sentiment-donut' : await this.loadChartSentiment(el);   break;
            }
            el.dataset.loaded = 'true';
        } catch(err) { console.error(`❌ ${section}:`, err); }
    },

    /* ── Cached geo-user fetch ── */
    async fetchGeoUser() {
        if (this._geoUserCache) return this._geoUserCache;
        const r = await fetch(`/mk/api/x/geo-user?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const j = await r.json();
        console.log('📍 geoUser:', j);
        this._geoUserCache = j;

        // Load KPI stats as side effect after first fetch
        this._loadStats(j);
        return j;
    },

    /* ── Parse helpers ── */
    parseGeoRows(result) {
        if (!result?.success) return [];
        const d = result.data; if (!d) return [];
        if (d.country && Array.isArray(d.country.rows)) return d.country.rows;
        if (Array.isArray(d.rows)) return d.rows;
        if (Array.isArray(d)) return d;
        if (typeof d === 'object') {
            const e = Object.entries(d);
            if (e.length && typeof e[0][1] === 'number') return e.map(([name,count])=>({name,count}));
        }
        return [];
    },
    parseGeoTotal(result) {
        if (!result?.success || !result.data) return 0;
        const d = result.data;
        return d.country?.total || d.total || 0;
    },

    /* ── KPI Cards (auto-loaded via fetchGeoUser) ── */
    _loadStats(result) {
        const rows  = this.parseGeoRows(result);
        const total = this.parseGeoTotal(result);
        const topRow = rows[0];

        const set = (id, val) => { const e = this._$(id); if(e) e.textContent = val; };
        const sub = (id, html) => { const e = this._$(id); if(e) e.innerHTML = html; };

        set('kpiCountries',  rows.length);
        set('kpiUsers',      this.numF(total));

        if (topRow) {
            set('kpiTopCountry', topRow.name || 'N/A');
            sub('kpiCountriesSub', `<i class="ph ph-globe me-1"></i>${rows.length} countries detected`);
            sub('kpiUsersSub',    `<i class="ph ph-users me-1"></i>${this.numK(total)} total identified`);
            sub('kpiTopCountrySub', `<i class="ph ph-chart-bar me-1"></i>${this.numF(topRow.count)} users`);

            const detail = topRow.detail || {};
            const provs  = Object.entries(detail).sort((a,b) => b[1]-a[1]);
            set('kpiTopProvince', provs.length ? provs[0][0] : 'N/A');
            sub('kpiTopProvinceSub', provs.length ? `<i class="ph ph-buildings me-1"></i>${this.numF(provs[0][1])} users` : '');
        } else {
            ['kpiTopCountry','kpiTopProvince'].forEach(id => set(id, 'N/A'));
        }
    },

    /* ── Shared renderMap ── */
    renderMap(elementId, rows, getMarkerProps) {
        const map = L.map(elementId, { center: [-2.5, 118], zoom: 5, scrollWheelZoom: false });
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap contributors, © CARTO',
            subdomains: 'abcd', maxZoom: 19
        }).addTo(map);

        /* scroll hint */
        const mapEl  = document.getElementById(elementId);
        const overlay = document.createElement('div');
        overlay.className = 'map-scroll-overlay';
        overlay.innerHTML = '<div class="map-scroll-hint"><svg viewBox="0 0 24 24" style="width:22px;height:22px;stroke:#fff;fill:none;stroke-width:2;"><rect x="5" y="2" width="14" height="20" rx="7"/><line x1="12" y1="6" x2="12" y2="10"/><line x1="9" y1="20" x2="11" y2="20"/><line x1="13" y1="20" x2="15" y2="20"/></svg>Use Ctrl + Scroll to zoom</div>';
        mapEl.style.position = 'relative';
        mapEl.appendChild(overlay);

        mapEl.addEventListener('wheel', function(e) {
            if (!e.ctrlKey) {
                overlay.classList.add('visible');
                clearTimeout(overlay._t);
                overlay._t = setTimeout(() => overlay.classList.remove('visible'), 1800);
            } else {
                map.scrollWheelZoom.enable();
                overlay.classList.remove('visible');
            }
        });
        map.on('zoomend', () => setTimeout(() => map.scrollWheelZoom.disable(), 300));

        if (!rows.length) return { map, markerRefs: [] };
        const maxCount = Math.max(...rows.map(p => parseInt(p.count || 0)));
        const markerRefs = [];

        rows.forEach((p) => {
            const lat = parseFloat(p.latitude  || 0);
            const lng = parseFloat(p.longitude || 0);
            if (lat === 0 && lng === 0) { markerRefs.push(null); return; }
            const { color, count, popup } = getMarkerProps(p);

            if (count >= 10) {
                let r = Math.sqrt(count) * 2500;
                r = Math.min(Math.max(r, 5000), 50000);
                const opacity = Math.min(0.15 + (count / maxCount) * 0.45, 0.6);
                L.circle([lat, lng], { radius: r, fillColor: color, color, weight: 1, opacity: 0.3, fillOpacity: opacity }).addTo(map);
            }

            const pin = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="width:13px;height:13px;background:${color};border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>`,
                    iconSize: [13, 13], iconAnchor: [6.5, 6.5]
                })
            }).addTo(map).bindPopup(popup);
            markerRefs.push({ marker: pin, lat, lng });

            const label    = count > 999 ? (count/1000).toFixed(1)+'k' : String(count);
            const fontSize = count >= 1000 ? '13px' : '11px';
            L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'circle-label',
                    html: `<div style="font-family:inherit;font-size:${fontSize};font-weight:900;color:#fff;background:${color};padding:3px 8px;border-radius:12px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4);white-space:nowrap;">${label}</div>`,
                    iconSize: [40, 20], iconAnchor: [20, 25]
                }),
                interactive: false
            }).addTo(map);
        });

        return { map, markerRefs };
    },

    /* ── Location sidebar panel builder ── */
    buildLocationPanel(listId, rows, mapResult, defaultColor, useSentiment) {
        const listEl = this._$(listId); if (!listEl) return;
        const { map, markerRefs } = mapResult;
        const valid = rows.filter(p => !(parseFloat(p.latitude||0) === 0 && parseFloat(p.longitude||0) === 0));

        if (!valid.length) {
            listEl.innerHTML = '<div class="geo-empty" style="padding:24px 14px;font-size:12px;">No location data</div>';
            return;
        }

        const sorted = [...valid].sort((a,b) => parseInt(b.count||0) - parseInt(a.count||0));
        let html = '';
        sorted.forEach((p, rank) => {
            const name  = p.name || 'Unknown';
            const count = parseInt(p.count || 0);
            let color   = defaultColor || '#038047';
            if (useSentiment) {
                const pos = parseInt(p.pos||0), neg = parseInt(p.neg||0), net = parseInt(p.net||0);
                if (pos>neg && pos>net) color = '#22c55e';
                else if (neg>pos && neg>net) color = '#ef4444';
                else color = '#64748b';
            }
            const rkCls  = rank < 3 ? ` location-item-rank--${rank+1}` : '';
            const label  = count > 999 ? (count/1000).toFixed(1)+'k' : count;
            html += `<div class="location-item" data-rank="${rank}">
                <div class="location-item-rank${rkCls}">${rank+1}</div>
                <div class="location-item-info">
                    <div class="location-item-name" title="${name}">${name}</div>
                    <div class="location-item-count">${label} ${useSentiment ? 'mentions' : 'users'}</div>
                </div>
                <div class="location-item-dot" style="background:${color};"></div>
            </div>`;
        });
        listEl.innerHTML = html;

        listEl.querySelectorAll('.location-item').forEach((item, i) => {
            item.addEventListener('click', () => {
                const p   = sorted[i];
                const lat = parseFloat(p.latitude||0), lng = parseFloat(p.longitude||0);
                if (lat === 0 && lng === 0) return;
                map.flyTo([lat, lng], 8, { animate: true, duration: 1 });
                const ref = markerRefs.find(r => r && Math.abs(r.lat-lat)<.001 && Math.abs(r.lng-lng)<.001);
                if (ref) setTimeout(() => ref.marker.openPopup(), 800);
                listEl.querySelectorAll('.location-item').forEach(el => el.classList.remove('active'));
                item.classList.add('active');
            });
        });
    },

    /* ── Geo User Map ── */
    async loadGeoUserMap(card) {
        const result  = await this.fetchGeoUser();
        const rows    = this.parseGeoRows(result);
        const markers = this.renderMap('geoMap', rows, (p) => {
            const count = parseInt(p.count||0), name = p.name||'Unknown';
            return {
                color: '#038047', count,
                popup: `<div style="font-family:inherit;text-align:center;padding:8px;">
                    <div style="font-weight:700;font-size:15px;color:var(--slate-900);margin-bottom:6px;">${name}</div>
                    <div style="font-size:24px;font-weight:800;color:#038047;margin-bottom:2px;">${this.numF(count)}</div>
                    <div style="font-size:10px;color:var(--slate-400);text-transform:uppercase;letter-spacing:.8px;font-weight:700;">users</div>
                </div>`
            };
        });
        this.buildLocationPanel('geoUserList', rows, markers, '#038047', false);
        card.dataset.loaded = 'true';
    },

    /* ── Geo Sentiment Map ── */
    async loadGeoSentimentMap(card) {
        const r      = await fetch(`/mk/api/x/geo-sentiment?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await r.json();
        console.log('📍 geoSentiment:', result);
        const rows = this.parseGeoRows(result);

        const markers = this.renderMap('geoSentimentMap', rows, (p) => {
            const count = parseInt(p.count||0);
            const pos   = parseInt(p.pos||0), neg = parseInt(p.neg||0), net = parseInt(p.net||0);
            const name  = p.name || 'Unknown', safe = count || 1;
            let color = '#64748b', sentiment = 'Neutral';
            if (pos>neg && pos>net) { color = '#22c55e'; sentiment = 'Positive'; }
            else if (neg>pos && neg>net) { color = '#ef4444'; sentiment = 'Negative'; }

            return {
                color, count,
                popup: `<div style="font-family:inherit;text-align:center;padding:8px;min-width:200px;">
                    <div style="font-weight:700;font-size:15px;color:var(--slate-900);margin-bottom:6px;">${name}</div>
                    <div style="display:inline-block;padding:3px 10px;background:${color}20;border-radius:20px;margin-bottom:8px;">
                        <span style="font-size:10px;font-weight:800;color:${color};text-transform:uppercase;">${sentiment}</span>
                    </div>
                    <div style="font-size:24px;font-weight:800;color:${color};margin-bottom:2px;">${this.numF(count)}</div>
                    <div style="font-size:10px;color:var(--slate-400);text-transform:uppercase;letter-spacing:.8px;font-weight:700;">mentions</div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-top:10px;border-top:1px solid var(--slate-200);padding-top:10px;">
                        <div style="text-align:center;padding:5px;background:#f0fdf4;border-radius:5px;">
                            <div style="font-size:15px;font-weight:800;color:#22c55e;">${pos}</div>
                            <div style="font-size:9px;color:var(--slate-400);text-transform:uppercase;font-weight:700;">Positive</div>
                            <div style="font-size:8px;color:var(--slate-400);">${((pos/safe)*100).toFixed(1)}%</div>
                        </div>
                        <div style="text-align:center;padding:5px;background:var(--slate-50);border-radius:5px;">
                            <div style="font-size:15px;font-weight:800;color:var(--slate-500);">${net}</div>
                            <div style="font-size:9px;color:var(--slate-400);text-transform:uppercase;font-weight:700;">Neutral</div>
                            <div style="font-size:8px;color:var(--slate-400);">${((net/safe)*100).toFixed(1)}%</div>
                        </div>
                        <div style="text-align:center;padding:5px;background:#fef2f2;border-radius:5px;">
                            <div style="font-size:15px;font-weight:800;color:#ef4444;">${neg}</div>
                            <div style="font-size:9px;color:var(--slate-400);text-transform:uppercase;font-weight:700;">Negative</div>
                            <div style="font-size:8px;color:var(--slate-400);">${((neg/safe)*100).toFixed(1)}%</div>
                        </div>
                    </div>
                </div>`
            };
        });
        this.buildLocationPanel('geoSentimentList', rows, markers, null, true);
        card.dataset.loaded = 'true';
    },

    /* ── Chart: Top Countries ── */
    async loadChartCountries(card) {
        const result = await this.fetchGeoUser();
        const rows   = this.parseGeoRows(result);
        const ldEl   = this._$('loadingChartCountries');
        const el     = this._$('chartCountries');
        if (!el) return;

        if (!rows.length) {
            if(ldEl) ldEl.innerHTML = '<div class="geo-empty">No data</div>';
            return;
        }

        const colors  = ['#038047','#059669','#0891b2','#7c3aed','#db2777','#ea580c'];
        const top     = rows.slice(0, 6);
        const max     = parseInt(top[0]?.count) || 1;

        el.innerHTML = top.map((row, i) => {
            const count  = parseInt(row.count);
            const logPct = Math.round((Math.log(count+1) / Math.log(max+1)) * 100);
            const pct    = Math.max(logPct, 6);
            return `<div class="country-bar-row">
                <div class="country-bar-header">
                    <span class="country-bar-name">${row.name}</span>
                    <span class="country-bar-count">${this.numF(count)}</span>
                </div>
                <div class="country-bar-track">
                    <div class="country-bar-fill" data-pct="${pct}" style="background:${colors[i%colors.length]};"></div>
                </div>
            </div>`;
        }).join('');

        if(ldEl) ldEl.style.display = 'none';
        el.style.display = 'block';
        requestAnimationFrame(() => {
            el.querySelectorAll('.country-bar-fill').forEach(b => b.style.width = b.dataset.pct + '%');
        });
    },

    /* ── Chart: Top Provinces ── */
    async loadChartProvinces(card) {
        const result = await this.fetchGeoUser();
        const rows   = this.parseGeoRows(result);
        const ldEl   = this._$('loadingChartProvinces');
        const el     = this._$('chartProvinces');
        if (!el) return;

        const topCountry = rows[0];
        if (!topCountry?.detail) {
            if(ldEl) ldEl.innerHTML = '<div class="geo-empty">No province data</div>';
            return;
        }

        const subEl = this._$('provSubtitle');
        if (subEl) subEl.textContent = topCountry.name + ' provinces';

        const provinces = Object.entries(topCountry.detail)
            .filter(([k]) => k && !k.startsWith('\u0000') && k.trim())
            .map(([name, count]) => ({ name, count: parseInt(count) }))
            .sort((a,b) => b.count - a.count)
            .slice(0, 8);

        const max = provinces[0]?.count || 1;
        el.innerHTML = provinces.map(p => {
            const pct = Math.round((p.count / max) * 100);
            return `<div class="prov-bar-row">
                <div class="prov-bar-header">
                    <span class="prov-bar-name">${p.name}</span>
                    <span class="prov-bar-count">${this.numF(p.count)}</span>
                </div>
                <div class="prov-bar-track">
                    <div class="prov-bar-fill" data-pct="${pct}"></div>
                </div>
            </div>`;
        }).join('');

        if(ldEl) ldEl.style.display = 'none';
        el.style.display = 'block';
        requestAnimationFrame(() => {
            el.querySelectorAll('.prov-bar-fill').forEach(b => b.style.width = b.dataset.pct + '%');
        });
    },

    /* ── Chart: Sentiment Donut ── */
    async loadChartSentiment(card) {
        const r      = await fetch(`/mk/api/x/geo-sentiment?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await r.json();
        const rows   = this.parseGeoRows(result);

        let pos = 0, neg = 0, net = 0;
        rows.forEach(r => { pos += parseInt(r.pos||0); neg += parseInt(r.neg||0); net += parseInt(r.net||0); });
        const total = pos + neg + net || 1;

        const ldEl     = this._$('loadingChartSentiment');
        const canvasEl = this._$('chartSentimentDonut');
        const legendEl = this._$('chartSentimentLegend');

        if(ldEl) ldEl.style.display = 'none';
        if(canvasEl) canvasEl.style.display = 'block';

        const canvas = document.createElement('canvas');
        canvas.width = 180; canvas.height = 180;
        canvasEl.appendChild(canvas);

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Positive','Neutral','Negative'],
                datasets: [{
                    data: [pos, net, neg],
                    backgroundColor: ['#22c55e','#94a3b8','#ef4444'],
                    borderColor: '#fff', borderWidth: 3, hoverOffset: 6,
                }]
            },
            options: {
                responsive: false, cutout: '62%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString()} (${((ctx.parsed/total)*100).toFixed(1)}%)` } }
                },
                animation: { animateRotate: true, duration: 900 }
            }
        });

        if (legendEl) {
            const items = [
                { label: 'Positive', val: pos, color: '#22c55e' },
                { label: 'Neutral',  val: net, color: '#94a3b8' },
                { label: 'Negative', val: neg, color: '#ef4444' },
            ];
            legendEl.innerHTML = items.map(item => `
                <div class="senti-legend-item">
                    <div class="senti-legend-dot" style="background:${item.color};"></div>
                    <span class="senti-legend-label">${item.label}</span>
                    <span class="senti-legend-val">${this.numF(item.val)}</span>
                    <span class="senti-legend-pct">${((item.val/total)*100).toFixed(1)}%</span>
                </div>`).join('');
        }
    },

    /* ── Top Locations Table ── */
    async loadTopLocations(card) {
        const r      = await fetch(`/mk/api/x/top-locations?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await r.json();
        const ldEl   = this._$('loadingTopLocations');
        const tblEl  = this._$('topLocationsTable');

        let locs = [];
        if (result.success && Array.isArray(result.data)) {
            locs = result.data.filter(l => {
                const n = (l.name||l.location||'').trim();
                return n && n !== 'Unknown' && !n.startsWith('\u0000');
            }).map(l => ({ name: l.name||l.location||'Unknown', count: parseInt(l.count||l.total||0) }));
        }

        if (!locs.length) {
            const geo  = await this.fetchGeoUser();
            const rows = this.parseGeoRows(geo);
            rows.forEach(country => {
                const cName = (country.name||'').trim();
                if (cName && cName !== 'Unknown') locs.push({ name: cName, count: parseInt(country.count||0) });
                if (country.detail && typeof country.detail === 'object') {
                    Object.entries(country.detail)
                        .filter(([k]) => k && !k.startsWith('\u0000') && k.trim())
                        .forEach(([name, val]) => {
                            const count = typeof val === 'number' ? val : parseInt(val?.count||0);
                            if (count > 0) locs.push({ name: name.trim(), count });
                        });
                }
            });
            locs.sort((a,b) => b.count - a.count);
        }

        if(ldEl) ldEl.style.display = 'none';

        if (!locs.length) {
            if(tblEl) tblEl.innerHTML = '<div class="geo-empty">No data available</div>';
            return;
        }

        this._allLocations = locs;

        /* View All button */
        if (locs.length > 10) {
            const btnWrap = this._$('topLocBtnWrap');
            if (btnWrap && !btnWrap.querySelector('.view-all-btn')) {
                btnWrap.insertAdjacentHTML('afterbegin', `
                    <button class="view-all-btn" onclick="XGeo.openModal()">
                        View All
                        <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;"><path d="M9 18l6-6-6-6"/></svg>
                    </button>`);
            }
        }

        if(tblEl) tblEl.innerHTML = this._buildTable(locs.slice(0, 10), 0);
    },

    _buildTable(items, offset) {
        let html = `<table class="geo-tbl" style="margin-top:8px;">
            <thead><tr>
                <th style="width:40px;">#</th>
                <th>Location</th>
                <th style="text-align:right;">Authors</th>
            </tr></thead><tbody>`;
        items.forEach((loc, i) => {
            html += `<tr>
                <td class="geo-tbl-rank">${offset + i + 1}</td>
                <td class="geo-tbl-name">${loc.name}</td>
                <td class="geo-tbl-num">${this.numF(loc.count)}</td>
            </tr>`;
        });
        return html + '</tbody></table>';
    },

    openModal() {
        const modal = this._$('geoLocModal');
        const body  = this._$('geoLocModalBody');
        if (!modal || !body) return;
        body.innerHTML = this._buildTable(this._allLocations, 0);
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        this._escHandler = (e) => { if(e.key === 'Escape') this.closeModal(); };
        document.addEventListener('keydown', this._escHandler);
    },

    closeModal() {
        const modal = this._$('geoLocModal');
        if(modal) modal.classList.remove('open');
        document.body.style.overflow = '';
        if(this._escHandler) document.removeEventListener('keydown', this._escHandler);
    },
};

document.addEventListener('DOMContentLoaded', () => XGeo.init());
</script>
@endsection