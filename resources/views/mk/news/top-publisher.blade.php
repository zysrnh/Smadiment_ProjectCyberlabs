@extends('mk.layouts.app')

@section('title', 'Top News Publishers - SMADIMENT')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════
   DESIGN TOKENS — identical to TikTok Most Engagement
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
@keyframes fadeUp        { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer       { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin          { to{transform:rotate(360deg)} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1}    to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn     { from{opacity:0} to{opacity:1} }
@keyframes overlayOut    { from{opacity:1} to{opacity:0} }

/* ══ KPI Icon bg ══ */
.kpi-icon-bg {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0;
}

/* ══ Skeleton ══ */
.sk-block {
    border-radius:4px;
    background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);
    background-size:200% 100%; animation:shimmer 1.4s infinite;
}

/* ══ Spinner ══ */
.spin-ring {
    width:26px; height:26px;
    border:2.5px solid var(--slate-100); border-top-color:var(--primary);
    border-radius:50%; animation:spin .65s linear infinite;
}
.spinner-state {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:48px 20px; gap:12px; color:var(--slate-400); font-size:12px; font-weight:600;
}

/* ══ Chart helpers ══ */
.chart-container { position:relative; }
.chart-loading {
    position:absolute; inset:0;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:8px; background:#fff; z-index:2; transition:opacity .3s;
}
.chart-loading.hidden { opacity:0; pointer-events:none; }
.chart-loading span { font-size:11px; font-weight:600; color:var(--slate-400); }
.chart-empty {
    height:100%; display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    gap:6px; color:var(--slate-400); font-size:12px; font-weight:600;
}
.chart-empty i { font-size:34px; color:var(--slate-300); display:block; }

/* ══ Publisher list items ══ */
.tp-pub-list { display:flex; flex-direction:column; }
.tp-pub-item {
    display:flex; align-items:center; gap:12px;
    padding:10px 16px; border-bottom:1px solid var(--slate-100);
    transition:background .12s; cursor:pointer;
}
.tp-pub-item:last-child { border-bottom:none; }
.tp-pub-item:hover { background:var(--slate-50); }

.tp-pub-rank {
    width:22px; height:22px; border-radius:50%;
    background:var(--slate-100); border:1px solid var(--slate-200);
    display:flex; align-items:center; justify-content:center;
    font-size:9px; font-weight:800; color:var(--slate-400); flex-shrink:0;
}
.tp-pub-rank--1 { background:linear-gradient(135deg,#ffd700,#F59E0B); color:#7c5900; border-color:#ffd700; }
.tp-pub-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af); color:#3d3d3d; border-color:#c0c0c0; }
.tp-pub-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820); color:#fff;    border-color:#cd7f32; }

.tp-pub-av {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-weight:700; font-size:12px; color:#fff;
    border:1.5px solid var(--slate-200); overflow:hidden; background:#EF4444;
}
.tp-pub-av img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

.tp-pub-body { flex:1; min-width:0; }
.tp-pub-name { font-size:12.5px; font-weight:700; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tp-pub-stats { display:flex; align-items:center; gap:4px; flex-wrap:wrap; margin-top:3px; }

.tp-metric {
    display:inline-flex; align-items:center; gap:3px;
    padding:2px 6px; border-radius:3px;
    font-size:10px; font-weight:700;
    background:var(--slate-100); color:var(--slate-500); white-space:nowrap;
}
.tp-metric--red     { background:rgba(239,68,68,.10); color:#991b1b; }
.tp-metric--primary { background:var(--primary-lt);  color:var(--primary); }

.tp-pub-bar-wrap { display:flex; align-items:center; gap:8px; width:140px; flex-shrink:0; }
.tp-pub-bar-track { flex:1; height:5px; background:var(--slate-100); border-radius:3px; overflow:hidden; }
.tp-pub-bar-fill  { height:100%; border-radius:3px; background:#EF4444; transition:width .4s ease; }
.tp-pub-bar-val   { font-size:10px; font-weight:800; color:var(--slate-500); white-space:nowrap; min-width:34px; text-align:right; }

/* ══ Search ══ */
.tp-search {
    padding:5px 12px; border:1px solid var(--slate-200);
    border-radius:var(--radius-sm); font-size:12px; font-weight:600;
    background:var(--slate-50); outline:none; width:200px;
    transition:border-color .15s; color:var(--slate-700);
}
.tp-search:focus { border-color:var(--primary); }

/* ══ Rows select ══ */
.tp-rows-sel {
    padding:4px 9px; border:1px solid var(--slate-200); border-radius:var(--radius-sm);
    font-size:11px; font-weight:600; color:var(--slate-600);
    background:var(--slate-50); outline:none; cursor:pointer;
}
.tp-rows-sel:focus { border-color:var(--primary); }

/* ══ Donut legend ══ */
.donut-legend { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-top:10px; }
.donut-leg-item {
    display:flex; align-items:center; gap:5px;
    font-size:11px; font-weight:600; color:var(--slate-500);
    padding:3px 8px; background:var(--slate-50);
    border-radius:3px; border:1px solid var(--slate-200);
}
.donut-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

/* ══ Pagination ══ */
.tp-pagination {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 16px; border-top:1px solid var(--slate-100); flex-wrap:wrap; gap:8px;
}
.tp-pag-info { font-size:11px; color:var(--slate-400); font-weight:500; }
.tp-pag-controls { display:flex; align-items:center; gap:3px; }
.tp-pag-btn {
    min-width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;
    padding:0 6px; border-radius:var(--radius-sm); border:1px solid var(--slate-200);
    background:#fff; font-size:11px; font-weight:600; color:var(--slate-500);
    cursor:pointer; transition:all .12s; user-select:none;
}
.tp-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary); color:var(--primary); background:var(--primary-lt); }
.tp-pag-btn.is-active { background:var(--primary); border-color:var(--primary); color:#fff; }
.tp-pag-btn:disabled { opacity:.35; cursor:not-allowed; }

/* ══════════════════════════════════════
   SLIDE PANEL — 1-to-1 from TikTok page
══════════════════════════════════════ */
.do-panel-overlay {
    position:fixed; inset:0; z-index:9000;
    background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none;
}
.do-panel-overlay.show   { display:block; animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }

.do-panel {
    position:fixed; top:0; right:0; bottom:0; z-index:9001;
    width:480px; max-width:100vw; background:#fff;
    display:none; flex-direction:column;
    border-left:1px solid var(--slate-200);
    box-shadow:-8px 0 40px rgba(15,23,42,.16);
}
.do-panel.show   { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }

.do-panel-header {
    display:flex; align-items:center; gap:10px;
    padding:14px 16px; border-bottom:1px solid var(--slate-200);
    background:var(--slate-50); flex-shrink:0;
}
.do-panel-dot   { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.do-panel-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-panel-close {
    width:28px; height:28px; border-radius:var(--radius-sm);
    border:1px solid var(--slate-200); background:#fff; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    color:var(--slate-500); font-size:16px; transition:all .14s; flex-shrink:0;
}
.do-panel-close:hover { background:var(--red); border-color:var(--red); color:#fff; }

.do-panel-actions {
    display:flex; align-items:center; gap:7px; padding:7px 12px;
    border-bottom:1px solid var(--slate-200); background:#fff; flex-shrink:0;
}
.do-panel-meta {
    flex:1; font-size:10px; font-weight:700; color:var(--slate-400);
    text-transform:uppercase; letter-spacing:.5px;
    display:flex; align-items:center; gap:5px;
}
.do-panel-export {
    display:flex; align-items:center; gap:4px; padding:4px 10px;
    background:var(--primary); color:#fff; border:none;
    border-radius:var(--radius-sm); font-size:10px; font-weight:700;
    cursor:pointer; transition:filter .13s;
}
.do-panel-export:hover { filter:brightness(1.1); }

.do-panel-list { overflow-y:auto; flex:1; padding:2px 0; min-height:0; }
.do-panel-list::-webkit-scrollbar { width:4px; }
.do-panel-list::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

.do-panel-item {
    display:flex; gap:10px; padding:10px 14px;
    border-bottom:1px solid var(--slate-50); cursor:pointer;
    transition:background .1s; align-items:flex-start;
}
.do-panel-item:hover { background:#f0f9ff; }
.do-panel-item:last-child { border-bottom:none; }

.do-panel-avatar {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-weight:700; font-size:12px; color:#fff;
    border:1.5px solid var(--slate-200); overflow:hidden;
}
.do-panel-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.do-panel-item-body { flex:1; min-width:0; }
.do-panel-author { font-size:12px; font-weight:700; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.do-panel-text   { font-size:11px; color:var(--slate-600); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
.do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--slate-400); flex-wrap:wrap; }

.do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.do-sent-badge--neu { background:var(--slate-100); color:var(--slate-500); }

.do-panel-loading {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    height:100%; gap:12px; color:var(--slate-400); font-size:13px; font-weight:600;
}
.do-panel-spinner {
    width:28px; height:28px; border:2.5px solid var(--slate-100);
    border-top-color:var(--primary); border-radius:50%; animation:spin .65s linear infinite;
}

/* Detail sub-panel — same as TikTok */
.do-detail-panel {
    position:absolute; inset:0; background:#fff; z-index:5;
    display:none; flex-direction:column;
    animation:slideInRight .2s cubic-bezier(.4,0,.2,1);
}
.do-detail-panel.show { display:flex; }

.do-dp2-header {
    display:flex; align-items:center; gap:8px; padding:12px 14px;
    background:var(--slate-50); border-bottom:1px solid var(--slate-200); flex-shrink:0;
}
.do-dp2-back {
    width:28px; height:28px; border-radius:var(--radius-sm);
    border:1px solid var(--slate-200); background:#fff; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    color:var(--slate-500); transition:all .13s;
}
.do-dp2-back:hover { background:var(--primary-lt); color:var(--primary); border-color:var(--primary); }
.do-dp2-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-dp2-body  { overflow-y:auto; flex:1; padding:16px; }
.do-dp2-body::-webkit-scrollbar { width:4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

.do-dp2-avatar-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.do-dp2-avatar-lg  {
    width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700;
    font-size:16px; display:flex; align-items:center; justify-content:center;
    border:2px solid var(--slate-200); overflow:hidden; flex-shrink:0; background:#EF4444;
}
.do-dp2-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.do-dp2-name       { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-handle     { font-size:11px; color:var(--slate-400); font-weight:500; }
.do-dp2-plat-badge { display:inline-block; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; margin-top:3px; }
.do-dp2-meta       { font-size:11px; color:var(--slate-400); font-weight:500; margin-bottom:10px; }
.do-dp2-sent       { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:3px; font-size:11px; font-weight:700; margin-bottom:10px; }
.do-dp2-sent--pos  { background:#dbeafe; color:#1d4ed8; }
.do-dp2-sent--neg  { background:#fee2e2; color:#991b1b; }
.do-dp2-sent--neu  { background:var(--slate-100); color:var(--slate-500); }
.do-dp2-content    { font-size:12px; color:var(--slate-700); line-height:1.7; margin-bottom:12px; background:var(--slate-50); border-radius:var(--radius-sm); padding:10px 12px; border:1px solid var(--slate-200); word-break:break-word; }
.do-dp2-stats      { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
.do-dp2-stat       { background:var(--slate-50); border-radius:var(--radius-sm); padding:8px 10px; border:1px solid var(--slate-200); text-align:center; }
.do-dp2-stat-val   { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-stat-lbl   { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
.do-dp2-link {
    display:flex; align-items:center; justify-content:center; gap:6px;
    padding:9px 14px; background:var(--primary); color:#fff;
    border-radius:var(--radius-sm); font-size:12px; font-weight:700;
    text-decoration:none; transition:filter .14s; margin-top:4px;
}
.do-dp2-link:hover { filter:brightness(1.1); color:#fff; }

/* ══ KPI Card Hover — identical to TikTok ══ */
@keyframes kpiIconBounce {
    0%,100% { transform:scale(1) rotate(0deg); }
    30%      { transform:scale(1.25) rotate(-10deg); }
    60%      { transform:scale(1.1) rotate(6deg); }
}
@keyframes kpiShimmer {
    0%   { left:-100%; }
    100% { left:150%; }
}
.kpi-card-hover {
    will-change:transform,box-shadow;
    transition:transform .25s cubic-bezier(.34,1.56,.64,1) !important,
               box-shadow .25s ease !important, filter .25s ease !important;
    cursor:default; position:relative !important; overflow:hidden !important;
}
.kpi-card-hover::before {
    content:''; position:absolute; top:0; bottom:0; left:-100%;
    width:60%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);
    pointer-events:none; z-index:1;
}
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025) !important; box-shadow:0 20px 40px rgba(0,0,0,.25) !important; filter:brightness(1.07) !important; }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background:rgba(255,255,255,.35) !important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important; display:inline-block !important; }
.kpi-card-hover:active { transform:translateY(-2px) scale(1.01) !important; transition-duration:.08s !important; }

@media(max-width:640px) {
    .do-panel { width:100vw; }
    .tp-pub-bar-wrap { display:none; }
    .tp-search { width:140px; }
}
</style>
@endsection

@section('page-title', 'Top News Publishers')

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->subDays(7)->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
  $newsType  = request()->get('news_type', 'article');
@endphp

<script>
    const TpCfg = {
        pid      : {{ $projectId ? (int)$projectId : 'null' }},
        sd       : '{{ $startDate }}',
        ed       : '{{ $endDate }}',
        newsType : '{{ $newsType }}',
    };
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
</script>

{{-- ══ Shared filter datepicker (same as TikTok page) ══ --}}
@include('mk.layouts.partials.filter-datepicker')

@if(!$projectId)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
    <i class="ph ph-warning-circle f-18"></i>
    <div>No project selected. Please select a project from the sidebar.</div>
</div>
@else

{{-- ══ KPI Cards ══ --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 text-white kpi-card-hover" style="background:#4680ff;animation:fadeUp .38s ease-out both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Articles</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiArticles">
                            <span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiArticlesSub">
                            <i class="ph ph-chart-line-up me-1"></i>Loading…
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-newspaper"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 text-white kpi-card-hover" style="background:#10B981;animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Publishers</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiPublishers">
                            <span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPublishersSub">
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
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 text-white kpi-card-hover" style="background:linear-gradient(135deg,#7c3aed,#a78bfa);animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Period</p>
                        <h3 class="mb-0 text-white f-w-300" style="font-size:16px;letter-spacing:0;padding-top:4px;">
                            {{ \Carbon\Carbon::parse($startDate)->format('d M') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-calendar me-1"></i>Selected date range
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-calendar-blank"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Charts Row ══ --}}
<div class="row mb-3">

    {{-- Publisher List Card (left / larger) --}}
    <div class="col-xl-7 mb-3 mb-xl-0">
        <div class="card h-100" style="animation:fadeUp .38s ease-out .15s both;">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-danger rounded">
                        <i class="ph ph-chart-bar f-18 text-danger"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Total articles by publisher</h6>
                        <small class="text-muted">Click a publisher to view its articles</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <input type="text" class="tp-search" id="tpSearch"
                        placeholder="🔍 Search publisher…"
                        oninput="TpData.filter(this.value)">
                    <select class="tp-rows-sel" id="tpRowsSel" onchange="TpData.changeRows()">
                        <option value="10">Top 10</option>
                        <option value="20" selected>Top 20</option>
                        <option value="50">Top 50</option>
                        <option value="100">Top 100</option>
                    </select>
                    <button class="btn btn-outline-secondary btn-sm" onclick="TpData.exportCsv()" title="Export CSV">
                        <i class="ph ph-download-simple me-1"></i>CSV
                    </button>
                    <span class="badge bg-light-danger text-danger" id="badgePublishers">Loading…</span>
                </div>
            </div>
            <div id="tpPubList" class="p-0">
                <div class="spinner-state"><div class="spin-ring"></div>Loading publishers…</div>
            </div>
            <div id="tpPubPag"></div>
        </div>
    </div>

    {{-- Donut Chart (right / smaller) --}}
    <div class="col-xl-5">
        <div class="card h-100" style="animation:fadeUp .38s ease-out .20s both;">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded">
                        <i class="ph ph-chart-donut f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Publisher's shares</h6>
                        <small class="text-muted">Top 9 + others</small>
                    </div>
                </div>
                <span class="badge bg-light-secondary text-muted">Top 9</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:460px;">
                    <div class="chart-loading" id="donutLoading">
                        <div class="spin-ring"></div><span>Loading chart…</span>
                    </div>
                    <div id="donutChart" style="width:100%;height:460px;display:none;"></div>
                </div>
                <div id="donutLegend" class="donut-legend"></div>
            </div>
        </div>
    </div>

</div>

{{-- ══ Horizontal Bar Chart Card ══ --}}
<div class="card mb-3" style="animation:fadeUp .38s ease-out .25s both;">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-danger rounded">
                <i class="ph ph-chart-bar-horizontal f-18 text-danger"></i>
            </div>
            <div>
                <h6 class="mb-0">Article Volume Chart</h6>
                <small class="text-muted">Top 20 publishers — click a bar to view articles</small>
            </div>
        </div>
        <span class="badge bg-light-danger text-danger">Top 20</span>
    </div>
    <div class="card-body">
        <div class="chart-container">
            <div class="chart-loading" id="barLoading">
                <div class="spin-ring"></div><span>Loading…</span>
            </div>
            <div id="barChart" style="width:100%;height:620px;display:none;"></div>
        </div>
    </div>
</div>

@endif

{{-- ══ Slide Panel (drawer) — same structure as TikTok page ══ --}}
<div class="do-panel-overlay" id="tpPanelOverlay" onclick="TpPanel.close()"></div>
<div class="do-panel" id="tpSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="tpPanelDot" style="background:#EF4444;"></div>
        <span class="do-panel-title" id="tpPanelTitle">Articles</span>
        <button class="do-panel-close" onclick="TpPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta">
            <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
            <span id="tpPanelMeta">—</span>
        </div>
        <button class="do-panel-export" onclick="TpPanel.exportCsv()">
            <i class="ph ph-download-simple"></i> CSV
        </button>
    </div>
    <div class="do-panel-list" id="tpPanelList"></div>

    {{-- Article detail sub-panel --}}
    <div class="do-detail-panel" id="tpDetailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="TpDetail.close()">
                <i class="ph ph-caret-left"></i>
            </button>
            <span class="do-dp2-title" id="tpDetailTitle">Article Detail</span>
            <button class="do-panel-close" onclick="TpPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="tpDetailBody"></div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

/* ══ UTILS ══ */
const _$   = id => document.getElementById(id);
const numF = n  => parseInt(n||0).toLocaleString('id-ID');
const numK = n  => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc  = s  => String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
const hideLd = id => { const e=_$(id); if(e){ e.style.transition='opacity .3s'; e.style.opacity='0'; setTimeout(()=>e.classList.add('hidden'),300); } };
const shortDomain = d => (d||'').replace(/^www\./,'');

const DONUT_COLORS = ['#2563eb','#e02020','#f97316','#a21caf','#0891b2','#15803d','#b45309','#0f766e','#7c3aed','#be185d'];
const RED = '#EF4444';

/* ══ STATE ══ */
let _allData  = [];
let _filtered = [];
let _pubPage  = 1;
const PUB_PER_PAGE = 20;
const _artCache = {};
let _panelItems = [];
let _panelDomain = null;

/* ══ ECharts instances ══ */
const EC = {};
window.addEventListener('resize', () => Object.values(EC).forEach(c=>{ try{ c.resize(); }catch(e){} }));

/* ══════════════════════════════════════
   MAIN DATA LOAD
══════════════════════════════════════ */
async function loadData() {
    if (!TpCfg.pid) return;
    try {
        const res    = await fetch(`/mk/api/news/top-publisher?project_id=${TpCfg.pid}&start_date=${TpCfg.sd}&end_date=${TpCfg.ed}&news_type=${TpCfg.newsType}`);
        const result = await res.json();
        if (!result.success || !result.data) return;

        _allData  = result.data;
        _filtered = _allData;

        const totalArt = result.meta?.total_articles  || _allData.reduce((s,p)=>s+(p.count||0),0);
        const totalPub = result.meta?.total_publishers || _allData.length;

        /* KPIs */
        const ka = _$('kpiArticles');   if(ka) ka.textContent = numF(totalArt);
        const kp = _$('kpiPublishers'); if(kp) kp.textContent = numF(totalPub);
        const ks = _$('kpiArticlesSub');
        const kps= _$('kpiPublishersSub');
        if(ks)  ks.innerHTML  = `<i class="ph ph-newspaper me-1"></i>Unique published articles`;
        if(kps) kps.innerHTML = `<i class="ph ph-globe me-1"></i>Active news sites`;
        const b = _$('badgePublishers'); if(b) b.textContent = `${totalPub} publishers`;

        renderAll(_filtered);
    } catch(err) {
        console.error('[TpData] load error:', err);
    }
}

function renderAll(data) {
    _pubPage = 1;
    renderPubList(data);
    renderBarChart(data.slice(0, 20));
    renderDonut(data);
}

/* ══════════════════════════════════════
   PUBLISHER LIST
══════════════════════════════════════ */
function renderPubList(data) {
    const listEl = _$('tpPubList'), pagEl = _$('tpPubPag');
    if (!listEl) return;

    if (!data.length) {
        listEl.innerHTML = `<div class="chart-empty" style="padding:48px 20px;"><i class="ph ph-folder-open"></i><span>No publisher data</span></div>`;
        if(pagEl) pagEl.innerHTML = '';
        return;
    }

    const pp = PUB_PER_PAGE, total = data.length, pages = Math.ceil(total/pp);
    const start = (_pubPage-1)*pp;
    const slice = data.slice(start, start+pp);
    const maxCount = data[0]?.count || 1;

    listEl.innerHTML = `<div class="tp-pub-list">${slice.map((p,i) => {
        const rank = start+i+1, rkCls = rank<=3 ? '--'+rank : '';
        const domain = shortDomain(p.domain||'');
        const color  = DONUT_COLORS[(start+i) % DONUT_COLORS.length];
        const fav    = `https://www.google.com/s2/favicons?sz=64&domain=${domain}`;
        const ini    = (domain[0]||'N').toUpperCase();
        const pct    = Math.max(4, Math.round((p.count/maxCount)*100));
        const enc    = esc(encodeURIComponent(JSON.stringify({domain:p.domain,count:p.count,mentions:p.mentions})));
        return `<div class="tp-pub-item" data-enc="${enc}" data-domain="${esc(p.domain)}">
            <div class="tp-pub-rank tp-pub-rank${rkCls}">${rank}</div>
            <div class="tp-pub-av">
                <img src="${fav}"
                    onerror="this.style.display='none';this.parentElement.style.background='${color}';this.parentElement.insertAdjacentHTML('beforeend','${ini}');">
            </div>
            <div class="tp-pub-body">
                <div class="tp-pub-name">${esc(domain)}</div>
                <div class="tp-pub-stats">
                    <span class="tp-metric tp-metric--red">
                        <i class="ph ph-newspaper"></i>${numF(p.count||0)} articles
                    </span>
                    ${p.mentions ? `<span class="tp-metric"><i class="ph ph-at"></i>${numF(p.mentions)} mentions</span>` : ''}
                </div>
            </div>
            <div class="tp-pub-bar-wrap">
                <div class="tp-pub-bar-track">
                    <div class="tp-pub-bar-fill" style="width:${pct}%;"></div>
                </div>
                <span class="tp-pub-bar-val">${numK(p.count||0)}</span>
            </div>
        </div>`;
    }).join('')}</div>`;

    /* Pagination */
    if(pagEl) pagEl.innerHTML = _pubPagHtml(_pubPage, pages, total, start+1, Math.min(start+pp, total));

    /* Click handlers */
    listEl.querySelectorAll('.tp-pub-item').forEach(el => {
        el.addEventListener('click', () => TpPanel.open(el.dataset.domain));
    });
}

function _pubPagHtml(page, pages, total, from, to) {
    if(pages <= 1) return '';
    let btns = '', r = 2;
    btns += `<button class="tp-pag-btn" ${page<=1?'disabled':''} onclick="TpPub.goPage(${page-1})"><i class="ph ph-caret-left"></i></button>`;
    for(let i=1;i<=pages;i++){
        if(i===1||i===pages||(i>=page-r&&i<=page+r))
            btns += `<button class="tp-pag-btn${i===page?' is-active':''}" onclick="TpPub.goPage(${i})">${i}</button>`;
        else if(i===page-r-1||i===page+r+1)
            btns += `<span class="tp-pag-btn" style="cursor:default;opacity:.4;">…</span>`;
    }
    btns += `<button class="tp-pag-btn" ${page>=pages?'disabled':''} onclick="TpPub.goPage(${page+1})"><i class="ph ph-caret-right"></i></button>`;
    return `<div class="tp-pagination">
        <span class="tp-pag-info">Showing ${from}–${to} of ${total} publishers</span>
        <div class="tp-pag-controls">${btns}</div>
    </div>`;
}

const TpPub = {
    goPage(page) {
        _pubPage = page;
        renderPubList(_filtered);
        _$('tpPubList')?.scrollIntoView({behavior:'smooth', block:'nearest'});
    }
};

/* ══════════════════════════════════════
   BAR CHART (ECharts horizontal)
══════════════════════════════════════ */
function renderBarChart(items) {
    const barEl = _$('barChart');
    if (!barEl || !items.length) { hideLd('barLoading'); return; }

    const domains = items.map(p => shortDomain(p.domain)).reverse();
    const values  = items.map(p => p.count||0).reverse();

    if(EC.bar) EC.bar.dispose();
    EC.bar = echarts.init(barEl, null, {renderer:'canvas'});

    EC.bar.setOption({
        animation:true, animationDuration:700, animationEasing:'cubicOut',
        backgroundColor:'transparent',
        tooltip:{
            trigger:'axis',
            backgroundColor:'#1e293b', borderColor:'#334155', borderWidth:1,
            padding:[10,14],
            textStyle:{color:'#f8fafc', fontFamily:'inherit', fontSize:12},
            extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
            axisPointer:{type:'shadow', shadowStyle:{color:'rgba(239,68,68,.05)'}},
            formatter: p => `<b style="font-size:13px;">${p[0].name}</b><br>${numF(p[0].value)} articles`,
        },
        grid:{top:8, right:72, bottom:8, left:8, containLabel:true},
        xAxis:{
            type:'value', axisLine:{show:false}, axisTick:{show:false},
            splitLine:{lineStyle:{color:'#f1f5f9', type:'dashed'}},
            axisLabel:{fontFamily:'inherit', fontSize:10, color:'#94a3b8', formatter:numK},
            max: v => Math.ceil(v.max * 1.18),
        },
        yAxis:{
            type:'category', data:domains, axisLine:{show:false}, axisTick:{show:false},
            axisLabel:{
                fontFamily:'inherit', fontSize:11, fontWeight:'700', color:'#475569',
                width:180, overflow:'truncate', ellipsis:'…',
            },
        },
        series:[{
            type:'bar',
            data: values.map((v,i)=>{
                const ratio = i / Math.max(values.length-1,1);
                return {
                    value:v,
                    itemStyle:{
                        color: new echarts.graphic.LinearGradient(0,0,1,0,[
                            {offset:0, color: DONUT_COLORS[i % DONUT_COLORS.length]},
                            {offset:1, color: DONUT_COLORS[(i+1) % DONUT_COLORS.length] + 'AA'}
                        ]),
                        borderRadius:[0,5,5,0]
                    }
                }
            }),
            barMaxWidth:20,
            label:{
                show:true, position:'right',
                fontFamily:'inherit', fontWeight:'700', fontSize:11, color:'#475569',
                formatter: p => numK(p.value),
            },
        }]
    });

    barEl.style.display = 'block';
    hideLd('barLoading');

    EC.bar.on('click', params => {
        if(params.componentType !== 'series') return;
        const domain = items[items.length-1-params.dataIndex]?.domain;
        if(domain) TpPanel.open(domain);
    });
    EC.bar.on('mouseover', ()=>{ EC.bar.getDom().style.cursor='pointer'; });
    EC.bar.on('mouseout',  ()=>{ EC.bar.getDom().style.cursor='default'; });
}

/* ══════════════════════════════════════
   DONUT CHART (ECharts) — same style as TikTok
══════════════════════════════════════ */
function renderDonut(data) {
    const chartEl=_$('donutChart'), loadEl=_$('donutLoading'), legEl=_$('donutLegend');
    if (!chartEl || !data.length) { if(loadEl) loadEl.classList.add('hidden'); return; }

    const top9  = data.slice(0,9);
    const rest  = data.slice(9);
    const total = data.reduce((s,p)=>s+(p.count||0), 0);

    const pieData = top9.map((p,i)=>({
        name      : shortDomain(p.domain),
        value     : p.count||0,
        domain    : p.domain,
        isOthers  : false,
        itemStyle : {color:DONUT_COLORS[i], borderColor:'#fff', borderWidth:3},
    }));
    if(rest.length) {
        pieData.push({
            name      : `Others (${rest.length})`,
            value     : rest.reduce((s,p)=>s+(p.count||0),0),
            domain    : null,
            isOthers  : true,
            restData  : rest,
            itemStyle : {color:'#94a3b8', borderColor:'#fff', borderWidth:3},
        });
    }

    /* Legend */
    if(legEl) legEl.innerHTML = pieData.map(d=>{
        const sn = d.name.length>24 ? d.name.slice(0,23)+'…' : d.name;
        return `<div class="donut-leg-item">
            <span class="donut-dot" style="background:${d.itemStyle.color};"></span>${esc(sn)}
        </div>`;
    }).join('');

    if(EC.donut) EC.donut.dispose();
    EC.donut = echarts.init(chartEl, null, {renderer:'canvas'});
    chartEl.style.display = 'block';
    if(loadEl) loadEl.classList.add('hidden');

    EC.donut.setOption({
        animation:true, animationDuration:900, animationEasing:'cubicOut',
        backgroundColor:'transparent',
        tooltip:{show:false},
        series:[{
            type:'pie',
            radius:['38%','64%'],
            center:['50%','46%'],
            avoidLabelOverlap:true,
            minAngle:3,
            itemStyle:{borderColor:'#fff', borderWidth:3},
            label:{
                show:true,
                position:'outside',
                lineHeight:16,
                fontSize:10,
                fontFamily:'inherit',
                color:'#475569',
                fontWeight:'600',
                distanceToLabelLine: 4,
                formatter: p => {
                    /* hide label for tiny slices — they clutter */
                    if (p.percent < 2.5) return '';
                    const n   = p.name.length > 16 ? p.name.slice(0,15)+'…' : p.name;
                    const pct = p.percent.toFixed(1);
                    return `{nm|${n}}\n{pc|${pct}%}`;
                },
                rich:{
                    nm:{ fontSize:10, fontWeight:'700', color:'#1e293b', lineHeight:15 },
                    pc:{ fontSize:10, fontWeight:'600', color:'#EF4444',  lineHeight:14 },
                }
            },
            labelLine:{
                show:true,
                showAbove:false,
                length:8,
                length2:12,
                smooth:0.4,
                minTurnAngle:80,
                lineStyle:{width:1.2, color:'#cbd5e1'},
            },
            labelLayout:{
                hideOverlap: true,
            },
            emphasis:{
                scale:true, scaleSize:6,
                itemStyle:{shadowBlur:12, shadowColor:'rgba(0,0,0,.18)', borderWidth:3, borderColor:'#fff'},
                label:{show:true, fontSize:11, fontWeight:'800'},
            },
            data: pieData,
        }],
        graphic:[
            {type:'text',left:'center',top:'41%',z:100,style:{text:numK(total),fill:'#0f172a',font:"800 26px inherit",textAlign:'center'}},
            {type:'text',left:'center',top:'49%',z:100,style:{text:'TOTAL ARTICLES',fill:'#94a3b8',font:"600 9px inherit",textAlign:'center'}},
        ]
    });

    /* Custom tooltip — same pattern as TikTok */
    let _ttEl = document.getElementById('tpDonutTT');
    if(!_ttEl){
        _ttEl = document.createElement('div');
        _ttEl.id = 'tpDonutTT';
        _ttEl.style.cssText = `position:fixed;z-index:9999;pointer-events:none;
            background:#1e293b;color:#fff;border:1px solid #334155;border-radius:6px;
            padding:10px 14px;max-width:260px;font-size:12px;line-height:1.5;display:none;
            box-shadow:0 8px 24px rgba(0,0,0,.32);font-family:inherit;
            opacity:0;transform:translateY(6px) scale(.97);
            transition:opacity .18s ease,transform .18s ease;`;
        document.body.appendChild(_ttEl);
    }
    let _ttTimer = null;

    EC.donut.on('mouseover', p => {
        if(p.componentType !== 'series') return;
        const d = pieData[p.dataIndex], color = d.itemStyle.color;
        clearTimeout(_ttTimer);
        _ttEl.innerHTML = `
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;">
                <span style="width:9px;height:9px;border-radius:50%;background:${color};flex-shrink:0;display:inline-block;"></span>
                <b style="font-size:12.5px;">${esc(p.name)}</b>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <b style="font-size:13px;color:${color};">${numF(p.value)} articles</b>
                <span style="color:#94a3b8;">${p.percent.toFixed(1)}%</span>
            </div>`;
        _ttEl.style.display='block';
        requestAnimationFrame(()=>{ _ttEl.style.opacity='1'; _ttEl.style.transform='translateY(0) scale(1)'; });
    });
    EC.donut.on('mouseout', ()=>{
        _ttEl.style.opacity='0'; _ttEl.style.transform='translateY(6px) scale(.97)';
        _ttTimer=setTimeout(()=>{ _ttEl.style.display='none'; }, 180);
    });
    chartEl.addEventListener('mousemove', e=>{
        if(_ttEl.style.display==='none') return;
        const vw=window.innerWidth, vh=window.innerHeight, tw=_ttEl.offsetWidth+16, th=_ttEl.offsetHeight+16;
        let x=e.clientX+18, y=e.clientY-10;
        if(x+tw>vw) x=e.clientX-tw; if(y+th>vh) y=e.clientY-th;
        _ttEl.style.left=x+'px'; _ttEl.style.top=y+'px';
    });

    EC.donut.on('click', p=>{
        const d=pieData[p.dataIndex];
        if(d?.domain) TpPanel.open(d.domain);
    });
    EC.donut.on('mouseover', ()=>{ EC.donut.getDom().style.cursor='pointer'; });
    EC.donut.on('mouseout',  ()=>{ EC.donut.getDom().style.cursor='default'; });
}

/* ══════════════════════════════════════
   FILTER + EXPORT
══════════════════════════════════════ */
const TpData = {
    filter(term) {
        _filtered = term.trim()
            ? _allData.filter(p=>(p.domain||'').toLowerCase().includes(term.toLowerCase()))
            : _allData;
        _pubPage = 1;
        const rows = parseInt(_$('tpRowsSel')?.value||'20');
        renderPubList(_filtered);
        renderBarChart(_filtered.slice(0, rows));
        renderDonut(_filtered);
        const b=_$('badgePublishers'); if(b) b.textContent=`${_filtered.length} publishers`;
    },
    changeRows() {
        const rows = parseInt(_$('tpRowsSel')?.value||'20');
        renderBarChart(_filtered.slice(0, rows));
    },
    exportCsv() {
        if(!_filtered.length){ alert('No data to export.'); return; }
        const rows=['index;domain;articles;mentions',..._filtered.map((p,i)=>`${i+1};${p.domain||''};${p.count||0};${p.mentions||0}`)];
        const blob=new Blob(['\uFEFF'+rows.join('\r\n')],{type:'text/csv;charset=utf-8;'});
        const a=Object.assign(document.createElement('a'),{href:URL.createObjectURL(blob),download:`TopPublishers_${TpCfg.sd}_${TpCfg.ed}.csv`});
        a.click();
    }
};

/* ══════════════════════════════════════
   ARTICLE FETCH (batched, same logic)
══════════════════════════════════════ */
async function fetchArticles(domain) {
    const cleanDomain = domain.replace(/^www\./,'').toLowerCase();
    const parts = cleanDomain.split('.');
    const baseDomain = parts.slice(-2).join('.');
    const isBase = (cleanDomain === baseDomain);

    const getHost = a => {
        const raw=(a.publisher||a.source_name||a.hostname||'').replace(/^www\./,'').toLowerCase().trim();
        if(raw) return raw;
        try { return new URL((a.url||a.link||'').startsWith('http')?(a.url||a.link||''):'https://'+(a.url||a.link||'')).hostname.replace(/^www\./,'').toLowerCase(); }
        catch { return ''; }
    };

    const BATCH=500; let allItems=[], start=0, maxBatches=6;
    while(maxBatches-->0){
        let batch=[];
        try {
            const res=await fetch(`/mk/api/news/mentions?project_id=${TpCfg.pid}&start_date=${TpCfg.sd}&end_date=${TpCfg.ed}&rows=${BATCH}&start=${start}`);
            if(!res.ok) break;
            const data=await res.json();
            batch=Array.isArray(data.data)?data.data:(Array.isArray(data)?data:[]);
        } catch(e){ break; }
        if(!batch.length) break;

        /* Filter to online news only */
        batch=batch.filter(m=>{
            const mt  =String(m.media_type   ||'').toLowerCase();
            const mtid=String(m.media_type_id||'').toLowerCase();
            const id  =String(m.id||'');
            const url =String(m.url||m.link||'').toLowerCase();
            if(['twit','twitter','fb','facebook','ig','instagram','yt','youtube','tiktok'].includes(mt)) return false;
            if(['2','3','4','5','6'].includes(mtid)) return false;
            if(/^(tw|fb|in|yt)-/.test(id)) return false;
            if(/twitter\.com|x\.com|facebook\.com|instagram\.com|youtube\.com|tiktok\.com/.test(url)) return false;
            return true;
        });

        allItems=allItems.concat(batch);
        start+=BATCH;
        const exact=allItems.filter(a=>getHost(a)===cleanDomain);
        if(exact.length>=100) break;
        if(batch.length<BATCH) break;
    }

    const exact=allItems.filter(a=>getHost(a)===cleanDomain);
    if(exact.length) return exact;
    if(isBase){
        const sub=allItems.filter(a=>{ const h=getHost(a); return h===baseDomain||h.endsWith('.'+baseDomain); });
        if(sub.length) return sub;
    }
    return allItems.filter(a=>(a.url||a.link||'').toLowerCase().includes(baseDomain));
}

/* ══════════════════════════════════════
   SLIDE PANEL — same structure as TikTok
══════════════════════════════════════ */
const TpPanel = {
    async open(domain) {
        _panelDomain = domain;
        TpDetail.close();

        _$('tpPanelDot').style.background   = RED;
        _$('tpPanelTitle').textContent       = shortDomain(domain);
        _$('tpPanelMeta').textContent        = TpCfg.sd + ' – ' + TpCfg.ed;

        const ov=_$('tpPanelOverlay'), pn=_$('tpSntPanel');
        ov.classList.remove('hiding'); pn.classList.remove('hiding');
        ov.classList.add('show');      pn.classList.add('show');

        const list=_$('tpPanelList');
        list.innerHTML='<div class="do-panel-loading"><div class="do-panel-spinner"></div>Loading articles…</div>';

        try {
            const key=`${TpCfg.pid}_${domain}_${TpCfg.sd}_${TpCfg.ed}`;
            if(!_artCache[key]) _artCache[key]=await fetchArticles(domain);
            _panelItems=_artCache[key]||[];
            this._render(list, _panelItems, domain);
        } catch(err){
            list.innerHTML=`<div class="do-panel-loading" style="color:var(--slate-400);">
                <i class="ph ph-warning-circle" style="font-size:28px;"></i>
                Failed to load: ${esc(err.message)}
            </div>`;
        }
    },

    close() {
        TpDetail.close();
        const ov=_$('tpPanelOverlay'), pn=_$('tpSntPanel');
        pn.classList.add('hiding'); ov.classList.add('hiding');
        setTimeout(()=>{ pn.classList.remove('show','hiding'); ov.classList.remove('show','hiding'); }, 240);
    },

    exportCsv() {
        if(!_panelItems?.length){ alert('No data to export.'); return; }
        const hdr='index;publisher;title;sentiment;views;shares;comments;date;url';
        const rows=_panelItems.map((a,i)=>{
            const sentRaw=String(a.class_sentiment||a.sentiment||'0').toLowerCase();
            const sent=sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif'?'Positif':sentRaw==='-1'||sentRaw==='negative'||sentRaw==='negatif'?'Negatif':'Netral';
            const e2=s=>String(s||'').replace(/;/g,',').replace(/\n/g,' ').replace(/\r/g,'');
            return `${i};${e2(a.publisher||_panelDomain)};${e2(a.title||'')};${sent};${parseInt(a.num_views||0)||0};${parseInt(a.num_share||0)||0};${parseInt(a.num_comments||0)||0};${e2((a.date_created||'').split('T')[0])};${e2(a.url||'')}`;
        });
        const blob=new Blob(['\uFEFF'+[hdr,...rows].join('\r\n')],{type:'text/csv;charset=utf-8;'});
        const a=Object.assign(document.createElement('a'),{href:URL.createObjectURL(blob),download:`articles_${shortDomain(_panelDomain)}_${TpCfg.sd}_${TpCfg.ed}.csv`});
        a.click();
    },

    _render(list, items, domain) {
        if(!items.length){
            list.innerHTML=`<div class="do-panel-loading" style="color:var(--slate-400);">
                <i class="ph ph-folder-open" style="font-size:32px;"></i>
                <span>No articles found for <b>${esc(shortDomain(domain))}</b></span>
                <small style="font-size:10px;">Domain not found in API results for this period</small>
            </div>`;
            return;
        }

        const SHOW=60, fav=`https://www.google.com/s2/favicons?sz=64&domain=${shortDomain(domain)}`;
        list.innerHTML=items.slice(0,SHOW).map(a=>{
            const pub =(a.publisher||a.source_name||a.hostname||domain).replace(/^www\./,'').trim();
            const title=(a.title||'').trim();
            const text =(a.content||a.description||a.summary||'').replace(/<[^>]*>/g,'').trim().slice(0,130);
            const dt   =(a.date_created||a.publish_date||'').split('T')[0];
            const url  =a.url||a.link||'';
            const views=parseInt(a.num_views||a.views||0)||0;
            const sentRaw=String(a.class_sentiment||a.sentiment||'0').toLowerCase();
            const sent=sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif'?'pos':sentRaw==='-1'||sentRaw==='negative'||sentRaw==='negatif'?'neg':'neu';
            const sentLbl={pos:'Pos',neg:'Neg',neu:'Neu'}[sent];
            const enc=esc(encodeURIComponent(JSON.stringify(a)));
            const ini=(shortDomain(domain)[0]||'N').toUpperCase();
            return `<div class="do-panel-item" data-item="${enc}" data-domain="${esc(domain)}" onclick="TpPanel._click(this)">
                <div class="do-panel-avatar" style="background:#EF4444;">
                    <img src="${fav}" onerror="this.style.display='none';this.parentElement.textContent='${ini}';">
                </div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author">${esc(pub)}</div>
                    <div class="do-panel-text">${esc(title||text||'(no title)')}</div>
                    <div class="do-panel-footer">
                        <span class="do-sent-badge do-sent-badge--${sent}">${sentLbl}</span>
                        ${views>0?`<span>Views ${numF(views)}</span>`:''}
                        ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
                        ${url?`<a href="${esc(url)}" target="_blank" rel="noopener"
                            style="color:var(--primary);font-weight:700;font-size:10px;text-decoration:none;"
                            onclick="event.stopPropagation()">Open ↗</a>`:''}
                    </div>
                </div>
            </div>`;
        }).join('');

        if(items.length>SHOW)
            list.insertAdjacentHTML('beforeend',`<div style="padding:9px;text-align:center;font-size:11px;font-weight:600;color:var(--slate-400);background:var(--slate-50);border-top:1px dashed var(--slate-200);">+${(items.length-SHOW).toLocaleString()} more articles</div>`);
    },

    _click(el){
        try {
            const raw  = el.dataset.item.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"');
            const item = JSON.parse(decodeURIComponent(raw));
            TpDetail.open(item, el.dataset.domain||_panelDomain);
        } catch(e){ console.warn('[TpPanel._click]', e); }
    }
};

/* ══════════════════════════════════════
   ARTICLE DETAIL SUB-PANEL — same as TikTok
══════════════════════════════════════ */
const TpDetail = {
    open(item, domain) {
        const panel=_$('tpDetailPanel'), body=_$('tpDetailBody'), title=_$('tpDetailTitle');
        if(!panel||!body) return;

        const pub      =(item.publisher||item.source_name||item.hostname||domain).trim();
        const artTitle =(item.title||'Article').trim();
        const content  =(item.content||item.description||item.summary||'').replace(/<[^>]*>/g,'').trim();
        const url      = item.url||item.link||'';
        const date     = item.date_created||item.publish_date||'';
        const fav      = `https://www.google.com/s2/favicons?sz=64&domain=${shortDomain(domain)}`;
        const ini      = (shortDomain(domain)[0]||'N').toUpperCase();
        const views    = parseInt(item.num_views||item.views||0)||0;
        const share    = parseInt(item.num_share||item.shares||0)||0;
        const comm     = parseInt(item.num_comments||0)||0;
        const imgUrl   = item.image_url||item.thumbnail||item.media_url||'';

        let dtFmt='';
        if(date){ try{ dtFmt=new Date(date).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); }catch(e){ dtFmt=date.split('T')[0]; } }

        const sentRaw=String(item.class_sentiment||item.sentiment||'0').toLowerCase();
        const sent=sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif'?'pos':sentRaw==='-1'||sentRaw==='negative'||sentRaw==='negatif'?'neg':'neu';
        const sentLbl={pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];

        title.textContent = artTitle;
        body.innerHTML = `
            <div class="do-dp2-avatar-row">
                <div class="do-dp2-avatar-lg">
                    <img src="${fav}" onerror="this.style.display='none';this.parentElement.textContent='${ini}';">
                </div>
                <div>
                    <div class="do-dp2-name">${esc(pub)}</div>
                    <div class="do-dp2-handle">${esc(shortDomain(domain))}</div>
                    <span class="do-dp2-plat-badge" style="background:rgba(239,68,68,.1);color:#EF4444;">Online News</span>
                </div>
            </div>
            ${dtFmt?`<div class="do-dp2-meta">${dtFmt}</div>`:''}
            <div class="do-dp2-sent do-dp2-sent--${sent}">${sentLbl}</div>
            ${imgUrl?`<div style="border-radius:var(--radius-sm);overflow:hidden;margin-bottom:10px;">
                <img style="width:100%;max-height:200px;object-fit:cover;display:block;" src="${esc(imgUrl)}"
                    onerror="this.parentElement.style.display='none'">
            </div>`:''}
            <div style="font-size:14px;font-weight:700;color:var(--slate-900);line-height:1.45;margin-bottom:10px;">${esc(artTitle)}</div>
            ${content?`<div class="do-dp2-content">${esc(content)}</div>`:''}
            ${(views>0||share>0||comm>0)?`
            <div class="do-dp2-stats">
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(views)}</div><div class="do-dp2-stat-lbl">Views</div></div>
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(share)}</div><div class="do-dp2-stat-lbl">Shares</div></div>
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(comm)}</div><div class="do-dp2-stat-lbl">Comments</div></div>
            </div>`:''}
            ${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link">
                <i class="ph ph-arrow-square-out me-1"></i>Open Original Article
            </a>`:''}`;

        panel.classList.add('show');
    },
    close() { _$('tpDetailPanel')?.classList.remove('show'); }
};

/* ══ INIT ══ */
document.addEventListener('DOMContentLoaded', () => {
    if(TpCfg.pid) loadData();
    document.addEventListener('keydown', e => { if(e.key==='Escape') TpPanel.close(); });
});
</script>
@endsection