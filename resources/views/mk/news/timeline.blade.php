@extends('mk.layouts.app')

@section('title', 'Mentions Timeline - SMADIMENT')

@section('styles')
<style>
:root {
    --primary        : #038047;
    --primary-rgb    : 3, 128, 71;
    --primary-lt     : rgba(3,128,71,.10);
    --dark           : #273B4A;
    --white          : #FFFFFF;
    --bg             : #F1F5F8;
    --green          : #038047;
    --red            : #EF4444;
    --amber          : #F59E0B;
    --cyan           : #06B6D4;
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
}
@keyframes fadeUp   { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer  { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin     { to{transform:rotate(360deg)} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1}    to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn  { from{opacity:0} to{opacity:1} }
@keyframes overlayOut { from{opacity:1} to{opacity:0} }

.kpi-icon-bg {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0;
}
.sk-block {
    border-radius:4px;
    background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);
    background-size:200% 100%; animation:shimmer 1.4s infinite;
}
.spin-ring {
    width:26px; height:26px;
    border:2.5px solid var(--slate-100); border-top-color:var(--primary);
    border-radius:50%; animation:spin .65s linear infinite;
}
.spinner-state {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:48px 20px; gap:12px; color:var(--slate-400); font-size:12px; font-weight:600;
}
.mt-tabs {
    display:flex; gap:2px;
    background:var(--slate-100); border:1px solid var(--slate-200);
    border-radius:var(--radius-sm); padding:2px; margin-bottom:16px;
}
.mt-tab-btn {
    flex:1; display:flex; align-items:center; justify-content:center; gap:6px;
    padding:7px 14px; border-radius:4px; border:none; background:transparent;
    font-size:12px; font-weight:600; color:var(--slate-500);
    cursor:pointer; transition:background .13s, color .13s; white-space:nowrap;
}
.mt-tab-btn:hover { background:#fff; color:var(--slate-800); }
.mt-tab-btn.active { background:#fff; color:var(--primary); box-shadow:0 1px 4px rgba(0,0,0,.08); }
.mt-tab-chip {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:20px; height:16px; padding:0 5px;
    border-radius:3px; font-size:9px; font-weight:800;
    background:var(--primary-lt); color:var(--primary);
}
.mt-tab-btn:not(.active) .mt-tab-chip { background:var(--slate-100); color:var(--slate-400); }
.mt-post-list { display:flex; flex-direction:column; }
.mt-post {
    display:flex; align-items:flex-start; gap:12px;
    padding:12px 16px; border-bottom:1px solid var(--slate-100);
    transition:background .12s; cursor:pointer;
}
.mt-post:last-child { border-bottom:none; }
.mt-post:hover { background:var(--slate-50); }
.mt-post-rank {
    width:22px; height:22px; border-radius:50%;
    background:var(--slate-100); border:1px solid var(--slate-200);
    display:flex; align-items:center; justify-content:center;
    font-size:9px; font-weight:800; color:var(--slate-400);
    flex-shrink:0; margin-top:8px;
}
.mt-post-rank--1 { background:linear-gradient(135deg,#ffd700,#F59E0B); color:#7c5900; border-color:#ffd700; }
.mt-post-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af); color:#3d3d3d; border-color:#c0c0c0; }
.mt-post-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820); color:#fff;    border-color:#cd7f32; }
.mt-post-av {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    color:#fff; font-weight:700; font-size:12px;
    display:flex; align-items:center; justify-content:center;
    border:1.5px solid var(--slate-200); overflow:hidden;
}
.mt-post-av img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.mt-post-body { flex:1; min-width:0; }
.mt-post-author { font-size:12.5px; font-weight:700; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.mt-post-handle { font-size:10px; color:var(--slate-400); font-weight:500; }
.mt-post-date   { font-size:10px; color:var(--slate-400); margin-top:1px; margin-bottom:4px; }
.mt-post-text   { font-size:11.5px; color:var(--slate-500); line-height:1.55; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:6px; word-break:break-word; }
.mt-post-stats  { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
.mt-metric {
    display:inline-flex; align-items:center; gap:3px;
    padding:2px 6px; border-radius:3px;
    font-size:10px; font-weight:700;
    background:var(--slate-100); color:var(--slate-500);
    white-space:nowrap;
}
.mt-metric--primary { background:var(--primary-lt); color:var(--primary); }
.mt-sent { display:inline-flex; align-items:center; padding:2px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.3px; }
.mt-sent--pos { background:#d1fae5; color:#065f46; }
.mt-sent--neg { background:#fee2e2; color:#991b1b; }
.mt-sent--neu { background:var(--slate-100); color:var(--slate-500); }
.mt-plat-badge {
    display:inline-flex; align-items:center; gap:3px;
    padding:2px 6px; border-radius:3px; font-size:9px; font-weight:800;
    white-space:nowrap;
}
.mt-view-link {
    display:inline-flex; align-items:center; gap:3px;
    font-size:9.5px; font-weight:700; color:var(--primary);
    text-decoration:none; padding:2px 6px; border-radius:3px;
    background:var(--primary-lt); border:1px solid rgba(3,128,71,.2);
    transition:background .12s, color .12s; margin-left:auto;
}
.mt-view-link:hover { background:var(--primary); color:#fff; }
.mt-pagination {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 16px; border-top:1px solid var(--slate-100); flex-wrap:wrap; gap:8px;
}
.mt-pag-info { font-size:11px; color:var(--slate-400); font-weight:500; }
.mt-pag-controls { display:flex; align-items:center; gap:3px; }
.mt-pag-btn {
    min-width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;
    padding:0 6px; border-radius:var(--radius-sm); border:1px solid var(--slate-200);
    background:#fff; font-size:11px; font-weight:600; color:var(--slate-500);
    cursor:pointer; transition:all .12s; user-select:none;
}
.mt-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary); color:var(--primary); background:var(--primary-lt); }
.mt-pag-btn.is-active { background:var(--primary); border-color:var(--primary); color:#fff; }
.mt-pag-btn:disabled { opacity:.35; cursor:not-allowed; }
.chart-container { height:280px; position:relative; }
.chart-loading {
    position:absolute; inset:0;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:8px; background:#fff; z-index:2; transition:opacity .3s;
}
.chart-loading.hidden { opacity:0; pointer-events:none; }
.chart-empty {
    height:100%; display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    gap:6px; color:var(--slate-400); font-size:12px; font-weight:600;
}
.chart-empty i { font-size:34px; color:var(--slate-300); display:block; }
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
.do-panel-header { display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--slate-200); background:var(--slate-50); flex-shrink:0; }
.do-panel-dot   { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.do-panel-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-panel-close { width:28px; height:28px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); font-size:16px; transition:all .14s; flex-shrink:0; }
.do-panel-close:hover { background:var(--red); border-color:var(--red); color:#fff; }
.do-panel-actions { display:flex; align-items:center; gap:7px; padding:7px 12px; border-bottom:1px solid var(--slate-200); background:#fff; flex-shrink:0; }
.do-panel-meta { flex:1; font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px; display:flex; align-items:center; gap:5px; }
.do-panel-export { display:flex; align-items:center; gap:4px; padding:4px 10px; background:var(--primary); color:#fff; border:none; border-radius:var(--radius-sm); font-size:10px; font-weight:700; cursor:pointer; transition:filter .13s; }
.do-panel-export:hover { filter:brightness(1.1); }
.do-panel-list { overflow-y:auto; flex:1; padding:2px 0; min-height:0; }
.do-panel-list::-webkit-scrollbar { width:4px; }
.do-panel-list::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.do-panel-item { display:flex; gap:10px; padding:10px 14px; border-bottom:1px solid var(--slate-50); cursor:pointer; transition:background .1s; align-items:flex-start; }
.do-panel-item:hover { background:#f0f9ff; }
.do-panel-item:last-child { border-bottom:none; }
.do-panel-avatar { width:36px; height:36px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; color:#fff; border:1.5px solid var(--slate-200); overflow:hidden; }
.do-panel-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.do-panel-item-body { flex:1; min-width:0; }
.do-panel-author { font-size:12px; font-weight:700; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.do-panel-text   { font-size:11px; color:var(--slate-600); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
.do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--slate-400); flex-wrap:wrap; }
.do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.do-sent-badge--neu { background:var(--slate-100); color:var(--slate-500); }
.do-detail-panel { position:absolute; inset:0; background:#fff; z-index:5; display:none; flex-direction:column; animation:slideInRight .2s cubic-bezier(.4,0,.2,1); }
.do-detail-panel.show { display:flex; }
.do-dp2-header { display:flex; align-items:center; gap:8px; padding:12px 14px; background:var(--slate-50); border-bottom:1px solid var(--slate-200); flex-shrink:0; }
.do-dp2-back { width:28px; height:28px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); transition:all .13s; font-size:14px; }
.do-dp2-back:hover { background:var(--primary-lt); color:var(--primary); border-color:var(--primary); }
.do-dp2-title  { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-dp2-body   { overflow-y:auto; flex:1; padding:16px; }
.do-dp2-body::-webkit-scrollbar { width:4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.do-dp2-avatar-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.do-dp2-avatar-lg  { width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700; font-size:16px; display:flex; align-items:center; justify-content:center; border:2px solid var(--slate-200); overflow:hidden; flex-shrink:0; }
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
.do-dp2-media      { border-radius:var(--radius-sm); overflow:hidden; margin-bottom:10px; }
.do-dp2-media iframe { width:100%; height:480px; border:none; display:block; }
.do-dp2-stats      { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
.do-dp2-stat       { background:var(--slate-50); border-radius:var(--radius-sm); padding:8px 10px; border:1px solid var(--slate-200); text-align:center; }
.do-dp2-stat-val   { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-stat-lbl   { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
.do-dp2-link { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 14px; background:var(--primary); color:#fff; border-radius:var(--radius-sm); font-size:12px; font-weight:700; text-decoration:none; transition:filter .14s; margin-top:4px; }
.do-dp2-link:hover { filter:brightness(1.1); color:#fff; }
.mt-rows-sel { padding:4px 9px; border:1px solid var(--slate-200); border-radius:var(--radius-sm); font-size:11px; font-weight:600; color:var(--slate-600); background:var(--slate-50); outline:none; cursor:pointer; }
@media(max-width:640px) {
    .do-panel { width:100vw; }
    .mt-tabs { flex-wrap:wrap; }
    .mt-tab-btn { flex:unset; min-width:calc(50% - 4px); }
}
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0)} 30%{transform:scale(1.25) rotate(-10deg)} 60%{transform:scale(1.1) rotate(6deg)} }
@keyframes kpiShimmer { 0%{left:-100%} 100%{left:150%} }
.kpi-card-hover { will-change:transform,box-shadow; transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important; cursor:default; position:relative!important; overflow:hidden!important; }
.kpi-card-hover::before { content:''; position:absolute; top:0; bottom:0; left:-100%; width:60%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent); pointer-events:none; z-index:1; transition:none; }
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025)!important; box-shadow:0 20px 40px rgba(0,0,0,.25)!important; filter:brightness(1.07)!important; }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background:rgba(255,255,255,.35)!important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important; display:inline-block!important; }
</style>
@endsection

@section('page-title', 'Mentions Timeline')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate   = $endDate ?? request()->get('end_date', now()->format('Y-m-d'));
    $projects  = $projects ?? [];
@endphp

<script>
    const MT_PID = {{ $projectId ? (int)$projectId : 'null' }};
    const MT_SD  = '{{ $startDate }}';
    const MT_ED  = '{{ $endDate }}';
</script>

@include('mk.layouts.partials.filter-datepicker')

<div class="row mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#4680ff;animation:fadeUp .38s ease-out both;">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiTotal"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTotalSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-newspaper"></i></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#10B981;animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiPos"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPosSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#94A3B8;animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Neutral</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiNeu"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNeuSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-minus-circle"></i></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#EF4444;animation:fadeUp .38s ease-out .15s both;">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiNeg"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNegSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div></div>
            </div></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-line f-18 text-primary"></i></div>
                    <div><h6 class="mb-0">Mentions Trend</h6><small class="text-muted">Daily volume per platform</small></div>
                </div>
                <span class="badge bg-light-primary text-primary" id="trendBadge">Loading...</span>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:340px;">
                    <div class="chart-loading" id="trendLoading"><div class="spin-ring"></div><span>Loading chart...</span></div>
                    <div id="trendChart" style="width:100%;height:340px;display:none;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-success rounded"><i class="ph ph-chart-bar f-18 text-success"></i></div>
                    <div><h6 class="mb-0">Platform Distribution</h6><small class="text-muted">Total mentions per platform</small></div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:340px;">
                    <div class="chart-loading" id="barLoading"><div class="spin-ring"></div><span>Loading chart...</span></div>
                    <div id="barChart" style="width:100%;height:340px;display:none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-tabs">
    <button class="mt-tab-btn active" id="tab-all" onclick="MTTab.show('all')"><i class="ph ph-globe"></i> All <span class="mt-tab-chip" id="chip-all">-</span></button>
    <button class="mt-tab-btn" id="tab-doc" onclick="MTTab.show('doc')"><i class="ph ph-newspaper"></i> News <span class="mt-tab-chip" id="chip-doc">-</span></button>
    <button class="mt-tab-btn" id="tab-twit" onclick="MTTab.show('twit')"><i class="ph ph-x-logo"></i> Twitter <span class="mt-tab-chip" id="chip-twit">-</span></button>
    <button class="mt-tab-btn" id="tab-fb" onclick="MTTab.show('fb')"><i class="ph ph-facebook-logo"></i> Facebook <span class="mt-tab-chip" id="chip-fb">-</span></button>
    <button class="mt-tab-btn" id="tab-ig" onclick="MTTab.show('ig')"><i class="ph ph-instagram-logo"></i> Instagram <span class="mt-tab-chip" id="chip-ig">-</span></button>
    <button class="mt-tab-btn" id="tab-ytb" onclick="MTTab.show('ytb')"><i class="ph ph-youtube-logo"></i> YouTube <span class="mt-tab-chip" id="chip-ytb">-</span></button>
    <button class="mt-tab-btn" id="tab-tiktok" onclick="MTTab.show('tiktok')"><i class="ph ph-tiktok-logo"></i> TikTok <span class="mt-tab-chip" id="chip-tiktok">-</span></button>
</div>

<div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-list-dashes f-18 text-primary"></i></div>
            <div><h6 class="mb-0">Mentions List</h6><small class="text-muted">Klik mention untuk lihat detail</small></div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="Object.keys(_mtCache).forEach(function(k){delete _mtCache[k];}); MTData.reload();" title="Refresh data"><i class="ph ph-arrows-clockwise me-1"></i>Refresh</button>
            <button class="btn btn-outline-secondary btn-sm" onclick="MTData.exportCsv()" title="Export CSV"><i class="ph ph-download-simple me-1"></i>CSV</button>
            <span class="badge bg-light-primary text-primary" id="listBadge">Loading...</span>
        </div>
    </div>
    <div id="listContainer" class="p-0"><div class="spinner-state"><div class="spin-ring"></div>Memuat data...</div></div>
    <div id="pagContainer"></div>
</div>

<div class="do-panel-overlay" id="mtPanelOverlay" onclick="MTPanel.close()"></div>
<div class="do-panel" id="mtPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="mtPanelDot" style="background:var(--primary);"></div>
        <span class="do-panel-title" id="mtPanelTitle">Mentions</span>
        <button class="do-panel-close" onclick="MTPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span id="mtPanelMeta">-</span></div>
        <button class="do-panel-export" onclick="MTPanel.exportCsv()"><i class="ph ph-download-simple"></i> CSV</button>
    </div>
    <div class="do-panel-list" id="mtPanelList"></div>
    <div class="do-detail-panel" id="mtDetailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="MTDetail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="mtDetailTitle">Detail</span>
            <button class="do-panel-close" onclick="MTPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="mtDetailBody"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

const MTCfg = { pid:MT_PID, sd:MT_SD, ed:MT_ED, perPage:20 };

const PLAT = {
    doc:   {label:'Online News',color:'#3b82f6',icon:'ph-newspaper'},
    twit:  {label:'Twitter',    color:'#0ea5e9',icon:'ph-x-logo'},
    fb:    {label:'Facebook',   color:'#6366f1',icon:'ph-facebook-logo'},
    ig:    {label:'Instagram',  color:'#ec4899',icon:'ph-instagram-logo'},
    ytb:   {label:'YouTube',    color:'#ef4444',icon:'ph-youtube-logo'},
    tiktok:{label:'TikTok',     color:'#6b7280',icon:'ph-tiktok-logo'},
};
const PLAT_KEYS = Object.keys(PLAT);

const _$   = id => document.getElementById(id);
const numF = n  => parseInt(n||0).toLocaleString('id-ID');
const numK = n  => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc  = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');

const Store = { all:[], doc:[], twit:[], fb:[], ig:[], ytb:[], tiktok:[] };
let _activeTab = 'all', _page = 1, _trendChart = null, _barChart = null;

function _normSent(item) {
    const code = String(item.class_sentiment_code||'').toLowerCase().trim();
    if(code==='pos'||code==='positive'||code==='positif') return 'pos';
    if(code==='neg'||code==='negative'||code==='negatif') return 'neg';
    if(code) return 'neu';
    const raw = String(item.class_sentiment||item.sentiment||item.sentiment_str||'0').toLowerCase().trim();
    if(raw==='1'||raw==='positive'||raw==='positif') return 'pos';
    if(raw==='-1'||raw==='2'||raw==='negative'||raw==='negatif') return 'neg';
    return 'neu';
}
function _detectPlatform(item) {
    if(item._platform) return item._platform;
    const tc=String(item.tcode||'').toLowerCase(),mt=String(item.media_type||'').toLowerCase();
    const mid=String(item.media_type_id||'');
    if(tc==='berita'||mt==='doc'||mt==='news'||mt==='berita'||mt==='online'||mt==='article'||mid==='1') return 'doc';
    if(tc==='rt'||tc==='mention'||tc==='reply'||tc==='tweet'||mt==='twit'||mt==='twitter'||mt==='x'||mid==='5') return 'twit';
    if(tc.startsWith('fb')||mt==='fb'||mt==='facebook'||mid==='4') return 'fb';
    if(tc.startsWith('ig')||tc==='image'||mt==='ig'||mt==='instagram'||mid==='16') return 'ig';
    if(tc==='youtube'||mt==='ytb'||mt==='youtube'||mid==='13') return 'ytb';
    if(tc==='video'||mt==='tiktok'||mt==='tt'||mid==='20') return 'tiktok';
    const url=String(item.url||'').toLowerCase();
    if(url.includes('tiktok.com')) return 'tiktok';
    if(url.includes('instagram.com')) return 'ig';
    if(url.includes('facebook.com')||url.includes('fb.com')) return 'fb';
    if(url.includes('youtube.com')||url.includes('youtu.be')) return 'ytb';
    if(url.includes('twitter.com')||url.includes('x.com')) return 'twit';
    return 'doc';
}
function _normItem(item, platform) {
    let ao = {};
    if(typeof item.author==='object'&&item.author) { ao=item.author; }
    else if(typeof item.author==='string'&&item.author.startsWith('{')) { try{ao=JSON.parse(item.author);}catch(e){} }
    let cj = {};
    if(typeof item.contentJson==='string'&&item.contentJson.startsWith('{')) { try{cj=JSON.parse(item.contentJson);}catch(e){} }
    else if(typeof item.contentJson==='object'&&item.contentJson) { cj=item.contentJson; }
    const name = (ao.name||item.author_name||item.name||item.from_name||item.page_name||item.channel_title||item.author_nickname||item.publisher||item.source_name||'').replace(/<[^>]*>/g,'').trim() || 'Unknown';
    const handle = (item.author_scr_name||item.screen_name||ao.scr_name||item.username||'').trim();
    const av = (item.avatar_url||item.profile_image_url||item.author_image||ao.image||(cj.user&&cj.user.image?cj.user.image:'')||item.profile_image||'').trim();
    const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
    let url = item.url||item.link||'';
    if(!url && platform==='twit') {
        const scr = (handle||ao.scr_name||'').replace(/^@/,'');
        let tweetId = item.sub_id || '';
        if(!tweetId && item.docid) { const m=String(item.docid).match(/^tw-(.+)$/); if(m) tweetId=m[1]; }
        if(!tweetId && cj.rt_status && cj.rt_status.id) tweetId=String(cj.rt_status.id);
        url = (scr && tweetId) ? 'https://twitter.com/'+scr+'/status/'+tweetId : (scr?'https://twitter.com/'+scr:'');
    }
    const sent = _normSent(item);
    const nL=parseInt(item.num_likes||item.likes||item.favorite_count||item.like_count||0);
    const nC=parseInt(item.num_comments||item.comments||item.comment_count||item.reply_count||0);
    const nS=parseInt(item.num_shares||item.shares||item.share_count||0);
    const nV=parseInt(item.view_cnt||item.num_views||item.views||item.play_count||item.freq||0);
    const nR=parseInt(item.rt||item.num_retweeted||item.retweet_count||0);
    return { _platform:platform, name:name, handle:handle, avatar:av, content:content, url:url, sentiment:sent, date:item.date_created||item.created_at||'', likes:nL, comments:nC, shares:nS, views:nV, retweets:nR, _raw:item };
}

const MTTab = {
    show(type) {
        _activeTab = type; _page = 1;
        PLAT_KEYS.concat(['all']).forEach(t => {
            const el = _$('tab-'+t);
            if(el) el.classList.toggle('active', t===type);
        });
        MTData._renderList();
    }
};

const _mtCache = {};

async function _mtFetchOne(platform, pid, sd, ed) {
    const cKey = pid+'_'+platform+'_'+sd+'_'+ed;
    if(_mtCache[cKey]) return _mtCache[cKey];

    const q = 'project_id='+pid+'&start_date='+sd+'&end_date='+ed+'&rows=500&start=0';

    // Instagram: try multiple sub values until data found
    if(platform === 'ig') {
        for(const sub of ['postbylike','postbycomment','postbydate','']) {
            try {
                const r = await fetch('/mk/api/news/ig-top-status?'+q+(sub?'&sub='+sub:''));
                const d = await r.json();
                const items = Array.isArray(d&&d.data)?d.data:(Array.isArray(d)?d:[]);
                if(items.length>0) { _mtCache[cKey]=items.map(function(i){i._platform=platform;return i;}); return _mtCache[cKey]; }
            } catch(e) { continue; }
        }
        return [];
    }

    const eps = {
        doc:    '/mk/api/news/mentions?'+q,
        twit:   '/mk/api/x/most-status?'+q+'&media=all&mention_type=view_all',
        fb:     '/mk/api/news/fb-top-status?'+q+'&sub=fblike',
        ytb:    '/mk/api/news/ytb-top-status?'+q,
        tiktok: '/mk/api/news/tiktok-top-status?'+q+'&sub=postbylike'
    };
    const url = eps[platform]; if(!url) return [];

    const ctrl = new AbortController(), tid = setTimeout(function(){ctrl.abort();}, 30000);
    try {
        const r = await fetch(url, {signal:ctrl.signal}); clearTimeout(tid);
        if(!r.ok) return [];
        const d = await r.json();
        let items = [];
        if(Array.isArray(d&&d.data&&d.data.data)) items = d.data.data;
        else if(Array.isArray(d&&d.data)) items = d.data;
        else if(Array.isArray(d&&d.statuses)) items = d.statuses;
        else if(Array.isArray(d&&d.results)) items = d.results;
        else if(Array.isArray(d&&d.posts)) items = d.posts;
        else if(Array.isArray(d)) items = d;

        // doc: filter to news items only
        if(platform === 'doc') items = items.filter(function(m){
            const tc = String(m.tcode||'').toLowerCase(), mt = String(m.media_type||'').toLowerCase();
            return tc==='berita'||mt==='berita'||mt==='doc'||mt==='news'||mt==='online'||mt==='article';
        });

        items = items.map(function(i){i._platform=platform;return i;});
        _mtCache[cKey] = items;
        return items;
    } catch(e) { clearTimeout(tid); return []; }
}

const MTData = {
    async loadAll() {
        if(!MTCfg.pid) { _$('listContainer').innerHTML='<div class="chart-empty" style="padding:40px"><i class="ph ph-folder-open"></i><span>Pilih project terlebih dahulu</span></div>'; return; }

        const results = await Promise.allSettled(
            PLAT_KEYS.map(function(k){ return _mtFetchOne(k, MTCfg.pid, MTCfg.sd, MTCfg.ed); })
        );

        PLAT_KEYS.forEach(function(k, i) {
            var raw = results[i].status==='fulfilled' ? results[i].value : [];
            Store[k] = raw.map(function(it){ return _normItem(it, k); });
            MTData._updateChip(k, Store[k].length);
        });

        Store.all = PLAT_KEYS.reduce(function(acc,k){ return acc.concat(Store[k]); },[]).sort(function(a,b){ return (b.date||'').localeCompare(a.date||''); });
        this._updateChip('all', Store.all.length);

        this._updateKPIs();
        this._renderTrend();
        this._renderBar();
        this._renderList();
        _$('listBadge').textContent = Store.all.length+' mentions';
    },

    reload() { this.loadAll(); },

    _updateChip(key, count) {
        var el = _$('chip-'+key); if(el) el.textContent = count;
    },

    _updateKPIs() {
        var all = Store.all;
        var pos = all.filter(function(m){return m.sentiment==='pos';}).length;
        var neg = all.filter(function(m){return m.sentiment==='neg';}).length;
        var neu = all.length - pos - neg;
        var pct = function(v){ return all.length>0 ? ((v/all.length)*100).toFixed(1) : '0.0'; };
        var platCount = PLAT_KEYS.map(function(k){return Store[k].length;}).filter(function(v){return v>0;}).length;
        _$('kpiTotal').textContent = numF(all.length);
        _$('kpiTotalSub').innerHTML = '<i class="ph ph-chart-line-up me-1"></i>'+platCount+' platforms';
        _$('kpiPos').textContent = numF(pos);
        _$('kpiPosSub').innerHTML = '<i class="ph ph-chart-line-up me-1"></i>'+pct(pos)+'% of total';
        _$('kpiNeu').textContent = numF(neu);
        _$('kpiNeuSub').innerHTML = '<i class="ph ph-chart-line-up me-1"></i>'+pct(neu)+'% of total';
        _$('kpiNeg').textContent = numF(neg);
        _$('kpiNegSub').innerHTML = '<i class="ph ph-chart-line-up me-1"></i>'+pct(neg)+'% of total';
    },

    _renderTrend() {
        var el = _$('trendChart'), ld = _$('trendLoading');
        if(!el) return;
        var start=new Date(MTCfg.sd), end=new Date(MTCfg.ed), dates=[];
        for(var d=new Date(start); d<=end; d.setDate(d.getDate()+1)) dates.push(d.toISOString().split('T')[0]);
        if(!dates.length || !Store.all.length) { if(ld) ld.classList.add('hidden'); return; }

        var xLabels = dates.map(function(d){ var dt=new Date(d+'T00:00:00'); return dt.getDate()+'/'+(dt.getMonth()+1); });
        var datasets = {};
        PLAT_KEYS.forEach(function(k){ datasets[k]=new Array(dates.length).fill(0); });
        Store.all.forEach(function(m) {
            var day = (m.date||'').split('T')[0].split(' ')[0];
            var idx = dates.indexOf(day);
            if(idx>=0 && datasets[m._platform]) datasets[m._platform][idx]++;
        });

        if(_trendChart) { try{_trendChart.dispose();}catch(e){} }
        el.style.display = 'block';
        _trendChart = echarts.init(el, null, {renderer:'canvas'});
        window.addEventListener('resize', function(){ try{_trendChart.resize();}catch(e){} });

        var series = PLAT_KEYS.map(function(k) {
            var hasData = datasets[k].some(function(v){return v>0;});
            return {
                name: PLAT[k].label, type:'line', data:datasets[k], smooth:.4,
                symbol:'circle', symbolSize:0, showSymbol:false,
                itemStyle:{color:PLAT[k].color},
                lineStyle:{color:PLAT[k].color, width:hasData?2:1, opacity:hasData?1:.12},
                areaStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:PLAT[k].color+'58'},{offset:1,color:PLAT[k].color+'08'}]}},
                emphasis:{focus:'series',lineStyle:{width:3},itemStyle:{borderColor:'#fff',borderWidth:2,shadowBlur:8,shadowColor:PLAT[k].color+'66'}}
            };
        });

        _trendChart.setOption({
            animation:true, animationDuration:600, animationEasing:'cubicOut', backgroundColor:'transparent',
            tooltip:{ trigger:'axis', backgroundColor:'rgba(15,23,42,.92)', borderColor:'transparent', borderRadius:8, padding:[10,14],
                textStyle:{color:'#e2e8f0',fontFamily:'inherit',fontSize:12},
                axisPointer:{lineStyle:{color:'#cbd5e1',type:'dashed'}},
                formatter: function(params) {
                    if(!params||!params.length) return '';
                    var total=0;
                    var rows=params.map(function(p){
                        total+=p.value||0;
                        return '<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:2px 0;">'
                            +'<span style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:'+p.color+';"></span>'+esc(p.seriesName)+'</span>'
                            +'<span style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:18px;padding:0 6px;border-radius:4px;background:rgba(255,255,255,.15);font-size:11px;font-weight:800;">'+numF(p.value||0)+'</span>'
                            +'</div>';
                    }).join('');
                    return '<div style="font-size:12px;font-weight:700;margin-bottom:6px;color:#f8fafc;">'+params[0].axisValue+'</div>'
                        +rows
                        +'<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:4px 0 0;margin-top:4px;border-top:1px solid rgba(255,255,255,.15);font-weight:800;color:#f8fafc;">'
                        +'<span>Total</span>'
                        +'<span style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:18px;padding:0 6px;border-radius:4px;background:rgba(99,179,237,.25);font-size:11px;font-weight:800;">'+numF(total)+'</span>'
                        +'</div>';
                }
            },
            legend:{ bottom:0, left:'center', data:PLAT_KEYS.map(function(k){return PLAT[k].label;}), textStyle:{fontFamily:'inherit',fontSize:11,fontWeight:600,color:'#64748b'}, icon:'circle', itemWidth:8, itemHeight:8, itemGap:16 },
            grid:{top:20,right:16,bottom:40,left:46},
            xAxis:{ type:'category', data:xLabels, boundaryGap:false, axisLine:{show:false}, axisTick:{show:false}, axisLabel:{fontSize:10,fontWeight:600,color:'#94a3b8'} },
            yAxis:{ type:'value', axisLine:{show:false}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}}, axisLabel:{fontSize:10,color:'#94a3b8',formatter:numK} },
            series:series
        });

        _trendChart.on('click', function(p) {
            if(p.componentType!=='series') return;
            var platKey = PLAT_KEYS.find(function(k){return PLAT[k].label===p.seriesName;});
            if(!platKey) return;
            var clickedDate = dates[p.dataIndex]||'';
            var items = (Store[platKey]||[]).filter(function(m){
                if(!clickedDate) return true;
                var day = (m.date||'').split('T')[0].split(' ')[0];
                return day === clickedDate;
            });
            if(!items.length) items = Store[platKey]||[];
            if(items.length) {
                MTPanel.open(items, items[0], clickedDate);
            } else {
                MTTab.show(platKey);
            }
        });
        _trendChart.on('mouseover', function(p){ if(p.componentType==='series') el.style.cursor='pointer'; });
        _trendChart.on('mouseout', function(){ el.style.cursor='default'; });

        if(ld) ld.classList.add('hidden');
        requestAnimationFrame(function(){ _trendChart.resize(); });
        var fmtB = function(d){ var dt=new Date(d+'T00:00:00'); return dt.getDate()+' '+dt.toLocaleString('id-ID',{month:'short'}); };
        _$('trendBadge').textContent = fmtB(dates[0])+' - '+fmtB(dates[dates.length-1]);
    },

    _renderBar() {
        var el = _$('barChart'), ld = _$('barLoading');
        if(!el) return;
        var platData = PLAT_KEYS.map(function(k){ return {name:PLAT[k].label, value:Store[k].length, color:PLAT[k].color}; })
            .sort(function(a,b){return b.value-a.value;});
        var total = platData.reduce(function(s,d){return s+d.value;},0);
        if(!total) { if(ld) ld.classList.add('hidden'); return; }

        if(_barChart) { try{_barChart.dispose();}catch(e){} }
        el.style.display='block';
        _barChart = echarts.init(el, null, {renderer:'canvas'});
        window.addEventListener('resize', function(){ try{_barChart.resize();}catch(e){} });

        _barChart.setOption({
            animation:true, animationDuration:500, animationEasing:'cubicOut', backgroundColor:'transparent',
            tooltip:{ trigger:'item', backgroundColor:'rgba(15,23,42,.92)', borderColor:'transparent', borderRadius:8, padding:[8,12],
                textStyle:{color:'#e2e8f0',fontFamily:'inherit',fontSize:12},
                formatter:function(p){return '<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:'+p.color+';margin-right:6px;"></span>'+esc(p.name)+' <b style="margin-left:8px;">'+numF(p.value)+'</b> <span style="color:#94a3b8;font-size:10px;">('+((p.value/total)*100).toFixed(1)+'%)</span>';}
            },
            grid:{top:10,right:16,bottom:28,left:80},
            xAxis:{type:'value', axisLine:{show:false}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}}, axisLabel:{fontSize:10,color:'#94a3b8',formatter:numK}},
            yAxis:{type:'category', data:platData.map(function(d){return d.name;}).reverse(), axisLine:{show:false}, axisTick:{show:false}, axisLabel:{fontSize:11,fontWeight:600,color:'#64748b'}},
            series:[{
                type:'bar', data:platData.map(function(d){return {value:d.value,itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:d.color},{offset:1,color:d.color+'99'}]}}};}).reverse(),
                barWidth:18, borderRadius:[0,6,6,0],
                label:{show:true,position:'right',formatter:function(p){return numF(p.value);},fontSize:10,fontWeight:800,color:'#64748b'}
            }],
            graphic:[{type:'text',left:'center',bottom:4,z:100,style:{text:'Total: '+numF(total),fill:'#94a3b8',font:'600 10px inherit',textAlign:'center'}}]
        });

        _barChart.on('click',function(p){
            var platKey=PLAT_KEYS.find(function(k){return PLAT[k].label===p.name;});
            if(!platKey) return;
            var items = Store[platKey]||[];
            if(items.length) {
                MTPanel.open(items, items[0]);
            } else {
                MTTab.show(platKey);
            }
        });
        _barChart.on('mouseover',function(){el.style.cursor='pointer';});
        _barChart.on('mouseout',function(){el.style.cursor='default';});

        if(ld) ld.classList.add('hidden');
        requestAnimationFrame(function(){_barChart.resize();});
    },

    _getItems() {
        return _activeTab==='all' ? Store.all : (Store[_activeTab]||[]);
    },

    _renderList() {
        var items = this._getItems();
        var listEl = _$('listContainer'), pagEl = _$('pagContainer');
        if(!items.length) { listEl.innerHTML = '<div class="chart-empty" style="padding:40px"><i class="ph ph-folder-open"></i><span>Tidak ada data untuk periode ini</span></div>'; if(pagEl) pagEl.innerHTML=''; return; }
        var pp = MTCfg.perPage, total = items.length, pages = Math.ceil(total/pp);
        if(_page>pages) _page=pages;
        var start=(_page-1)*pp;
        var html = '<div class="mt-post-list">';
        for(var i=start; i<Math.min(start+pp, total); i++) html += this._postHtml(items[i], i);
        html += '</div>';
        listEl.innerHTML = html;
        if(pagEl) pagEl.innerHTML = this._pagHtml(_page,pages,total,start+1,Math.min(start+pp,total));

        listEl.querySelectorAll('.mt-post').forEach(function(el) {
            el.addEventListener('click', function() {
                try {
                    var item = JSON.parse(decodeURIComponent(el.dataset.item));
                    MTPanel.open(items, item);
                    MTDetail.open(item);
                } catch(e){ console.warn(e); }
            });
        });
    },

    _postHtml(item, gi) {
        var rank=gi+1, rkCls=rank<=3?'--'+rank:'';
        var plat = PLAT[item._platform]||PLAT.doc;
        var dummy = '/assets/images/user/dummy.jpg';
        var avHtml = (item.avatar&&item.avatar.indexOf('http')===0)
            ? '<img src="'+esc(item.avatar)+'" onerror="this.src=\''+dummy+'\'">'
            : '<img src="'+dummy+'">';
        var dt = (item.date||'').split('T')[0];
        var sentLbl = {pos:'Positive',neg:'Negative',neu:'Neutral'}[item.sentiment];
        var enc = encodeURIComponent(JSON.stringify(item));
        var parts = [];
        if(item.views>0)    parts.push('<span class="mt-metric"><i class="ph ph-eye me-1"></i>'+numF(item.views)+'</span>');
        if(item.likes>0)    parts.push('<span class="mt-metric"><i class="ph ph-thumbs-up me-1"></i>'+numF(item.likes)+'</span>');
        if(item.retweets>0) parts.push('<span class="mt-metric"><i class="ph ph-repeat me-1"></i>'+numF(item.retweets)+'</span>');
        if(item.comments>0) parts.push('<span class="mt-metric"><i class="ph ph-chat-circle me-1"></i>'+numF(item.comments)+'</span>');
        if(item.shares>0)   parts.push('<span class="mt-metric"><i class="ph ph-share-network me-1"></i>'+numF(item.shares)+'</span>');

        return '<div class="mt-post" data-item="'+esc(enc)+'">'
            +'<div class="mt-post-rank mt-post-rank'+rkCls+'">'+rank+'</div>'
            +'<div class="mt-post-av" style="background:linear-gradient(135deg,'+plat.color+','+plat.color+'99);">'+avHtml+'</div>'
            +'<div class="mt-post-body">'
            +'<div class="mt-post-author">'+esc(item.name)+'</div>'
            +(item.handle?'<div class="mt-post-handle">@'+esc(item.handle.replace(/^@/,''))+'</div>':'')
            +(dt?'<div class="mt-post-date">'+dt+'</div>':'')
            +(item.content?'<div class="mt-post-text">'+esc(item.content.slice(0,200))+'</div>':'')
            +'<div class="mt-post-stats">'
            +'<span class="mt-plat-badge" style="background:'+plat.color+'15;color:'+plat.color+';border:1px solid '+plat.color+'30;">'+plat.label+'</span>'
            +parts.join('')
            +'<span class="mt-sent mt-sent--'+item.sentiment+'">'+sentLbl+'</span>'
            +(item.url?'<a href="'+esc(item.url)+'" target="_blank" rel="noopener" class="mt-view-link" onclick="event.stopPropagation()"><i class="ph ph-arrow-square-out me-1"></i>Lihat</a>':'')
            +'</div></div></div>';
    },

    _pagHtml(page,pages,total,from,to) {
        if(pages<=1) return '';
        var btns='', r=2;
        btns+='<button class="mt-pag-btn" '+(page<=1?'disabled':'')+' onclick="MTData.goPage('+(page-1)+')"><i class="ph ph-caret-left"></i></button>';
        for(var i=1;i<=pages;i++){
            if(i===1||i===pages||(i>=page-r&&i<=page+r)) btns+='<button class="mt-pag-btn'+(i===page?' is-active':'')+'" onclick="MTData.goPage('+i+')">'+i+'</button>';
            else if(i===page-r-1||i===page+r+1) btns+='<span class="mt-pag-btn" style="cursor:default;opacity:.4;">...</span>';
        }
        btns+='<button class="mt-pag-btn" '+(page>=pages?'disabled':'')+' onclick="MTData.goPage('+(page+1)+')"><i class="ph ph-caret-right"></i></button>';
        return '<div class="mt-pagination"><span class="mt-pag-info">Menampilkan '+from+'-'+to+' dari '+total+' mentions</span><div class="mt-pag-controls">'+btns+'</div></div>';
    },

    goPage(p) {
        var items=this._getItems(), pages=Math.ceil(items.length/MTCfg.perPage);
        if(p<1||p>pages) return;
        _page=p; this._renderList();
        var el=_$('listContainer'); if(el) el.scrollIntoView({behavior:'smooth',block:'nearest'});
    },

    exportCsv() {
        var items = this._getItems();
        if(!items.length) { alert('Tidak ada data.'); return; }
        var hdr='index;platform;nama;handle;sentimen;views;likes;retweets;comments;shares;tanggal;url;konten';
        var rows=items.map(function(it,i){
            var sentLbl={pos:'Positif',neg:'Negatif',neu:'Netral'}[it.sentiment];
            var e=function(s){return String(s||'').replace(/;/g,',').replace(/\n/g,' ');};
            return i+';'+it._platform+';'+e(it.name)+';'+e(it.handle)+';'+sentLbl+';'+it.views+';'+it.likes+';'+it.retweets+';'+it.comments+';'+it.shares+';'+(it.date||'').split('T')[0]+';'+e(it.url)+';'+e((it.content||'').slice(0,300));
        });
        var blob=new Blob(['\uFEFF'+[hdr].concat(rows).join('\r\n')],{type:'text/csv;charset=utf-8;'});
        var a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='Mentions_'+_activeTab+'_'+MTCfg.sd+'_'+MTCfg.ed+'.csv';
        a.click();
    }
};

const MTPanel = {
    _items:[], _current:null,
    open(items, current, dateLabel) {
        this._items=items; this._current=current;
        MTDetail.close();
        var plat = PLAT[current._platform]||PLAT.doc;
        _$('mtPanelDot').style.background = plat.color;
        _$('mtPanelTitle').textContent = plat.label+' Mentions';
        var metaText = MTCfg.sd+' - '+MTCfg.ed;
        if(dateLabel) {
            var dl=new Date(dateLabel+'T00:00:00');
            metaText = dl.getDate()+'/'+(dl.getMonth()+1)+'/'+dl.getFullYear()+' • '+items.length+' mentions';
        }
        _$('mtPanelMeta').textContent = metaText;
        var ov=_$('mtPanelOverlay'), pn=_$('mtPanel');
        ov.classList.remove('hiding'); pn.classList.remove('hiding');
        ov.classList.add('show'); pn.classList.add('show');
        this._render(items);
    },
    close() {
        MTDetail.killIframe(); MTDetail.close();
        var ov=_$('mtPanelOverlay'), pn=_$('mtPanel');
        pn.classList.add('hiding'); ov.classList.add('hiding');
        setTimeout(function(){ pn.classList.remove('show','hiding'); ov.classList.remove('show','hiding'); },240);
    },
    exportCsv() { MTData.exportCsv(); },
    _render(items) {
        var list=_$('mtPanelList'); if(!list) return;
        if(!items||!items.length){ list.innerHTML='<div class="do-panel-loading"><div class="do-panel-spinner"></div>Tidak ada data</div>'; return; }
        var dummy='/assets/images/user/dummy.jpg';
        list.innerHTML=items.slice(0,100).map(function(item){
            var plat=PLAT[item._platform]||PLAT.doc;
            var avHtml=(item.avatar&&item.avatar.indexOf('http')===0)?'<img src="'+esc(item.avatar)+'" onerror="this.src=\''+dummy+'\'">':'<img src="'+dummy+'">';
            var sentLbl={pos:'Pos',neg:'Neg',neu:'Neu'}[item.sentiment];
            var dt=(item.date||'').split('T')[0];
            var enc=encodeURIComponent(JSON.stringify(item));
            return '<div class="do-panel-item" data-item="'+esc(enc)+'" onclick="MTPanel._click(this)">'
                +'<div class="do-panel-avatar" style="background:linear-gradient(135deg,'+plat.color+','+plat.color+'99);">'+avHtml+'</div>'
                +'<div class="do-panel-item-body">'
                +'<div class="do-panel-author">'+esc(item.name)+'</div>'
                +'<div class="do-panel-text">'+esc((item.content||'').slice(0,130)||'(tidak ada konten)')+'</div>'
                +'<div class="do-panel-footer">'
                +'<span class="do-sent-badge do-sent-badge--'+item.sentiment+'">'+sentLbl+'</span>'
                +'<span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:'+plat.color+';flex-shrink:0;"></span>'
                +'<span style="font-size:10px;font-weight:600;color:'+plat.color+';">'+plat.label+'</span>'
                +(dt?'<span style="margin-left:auto;">'+dt+'</span>':'')
                +'</div></div></div>';
        }).join('');
        if(items.length>100) list.insertAdjacentHTML('beforeend','<div style="padding:8px;text-align:center;font-size:10px;font-weight:600;color:#94A3B8;background:var(--slate-50);border-top:1px dashed var(--slate-200);">+'+(items.length-100).toLocaleString()+' lainnya</div>');
    },
    _click(el) {
        try {
            var raw = el.dataset.item.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"');
            var item=JSON.parse(decodeURIComponent(raw));
            MTDetail.open(item);
        } catch(e){ console.warn(e); }
    }
};

const MTDetail = {
    open(item) {
        var panel=_$('mtDetailPanel'), body=_$('mtDetailBody'), title=_$('mtDetailTitle');
        if(!panel||!body) return;
        var plat=PLAT[item._platform]||PLAT.doc;
        var dummy='/assets/images/user/dummy.jpg';
        var avHtml=(item.avatar&&item.avatar.indexOf('http')===0)?'<img src="'+esc(item.avatar)+'" onerror="this.src=\''+dummy+'\'">':'<img src="'+dummy+'">';
        var sentLbl={pos:'Positif',neg:'Negatif',neu:'Netral'}[item.sentiment];
        var dtFmt=''; if(item.date){ try{ dtFmt=new Date(item.date).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); }catch(e){ dtFmt=(item.date||'').split('T')[0]; } }

        var mediaHtml='';
        var url=item.url||'';
        if(item._platform==='ytb') {
            var m=url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/);
            if(m) mediaHtml='<div class="do-dp2-media"><iframe src="https://www.youtube.com/embed/'+m[1]+'?rel=0&modestbranding=1" allowfullscreen></iframe></div>';
        } else if(item._platform==='tiktok') {
            var vid=''; if(url){ var m2=url.match(/\/video\/(\d+)/); if(m2) vid=m2[1]; }
            if(!vid&&item._raw&&item._raw.id){ var m3=String(item._raw.id).match(/(\d{10,})/); if(m3) vid=m3[1]; }
            if(vid) mediaHtml='<div class="do-dp2-media"><iframe src="https://www.tiktok.com/embed/v2/'+vid+'" allow="autoplay" allowfullscreen></iframe></div>';
        }

        var stats=[];
        if(item.views>0)    stats.push({l:'Views',v:numF(item.views)});
        if(item.likes>0)    stats.push({l:'Likes',v:numF(item.likes)});
        if(item.retweets>0) stats.push({l:'Retweets',v:numF(item.retweets)});
        if(item.comments>0) stats.push({l:'Comments',v:numF(item.comments)});
        if(item.shares>0)   stats.push({l:'Shares',v:numF(item.shares)});
        var cols = Math.min(stats.length,4);
        var statsHtml = stats.length ? '<div class="do-dp2-stats" style="grid-template-columns:repeat('+cols+',1fr);">'+stats.map(function(s){return '<div class="do-dp2-stat"><div class="do-dp2-stat-val">'+s.v+'</div><div class="do-dp2-stat-lbl">'+s.l+'</div></div>';}).join('')+'</div>' : '';

        title.textContent=item.name;
        body.innerHTML=''
            +'<div class="do-dp2-avatar-row">'
            +'<div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,'+plat.color+','+plat.color+'99);">'+avHtml+'</div>'
            +'<div>'
            +'<div class="do-dp2-name">'+esc(item.name)+'</div>'
            +(item.handle?'<div class="do-dp2-handle">@'+esc(item.handle.replace(/^@/,''))+'</div>':'')
            +'<span class="do-dp2-plat-badge" style="background:'+plat.color+'18;color:'+plat.color+';">'+plat.label+'</span>'
            +'</div></div>'
            +(dtFmt?'<div class="do-dp2-meta">'+dtFmt+'</div>':'')
            +'<div class="do-dp2-sent do-dp2-sent--'+item.sentiment+'">'+sentLbl+'</div>'
            +mediaHtml
            +(item.content?'<div class="do-dp2-content">'+esc(item.content)+'</div>':'')
            +statsHtml
            +(url?'<a href="'+esc(url)+'" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out me-1"></i>Lihat Sumber Asli</a>':'');
        panel.classList.add('show');
    },
    close() { var p=_$('mtDetailPanel'); if(p) p.classList.remove('show'); },
    killIframe() { var b=_$('mtDetailBody'); if(b){ var frames=b.querySelectorAll('iframe'); for(var i=0;i<frames.length;i++){frames[i].src='';frames[i].remove();} } }
};

document.addEventListener('DOMContentLoaded', function() {
    MTData.loadAll();
    document.addEventListener('keydown', function(e) { if(e.key==='Escape') MTPanel.close(); });
});
</script>
@endsection