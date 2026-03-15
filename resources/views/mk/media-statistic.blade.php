@extends('mk.layouts.app')

@section('title', 'Media Statistic - SMADIMENT')

@section('styles')
<style>
/* ══ Design Tokens ══ */
:root {
    --primary          : var(--bs-primary, #4361EE);
    --primary-rgb      : var(--bs-primary-rgb, 67, 97, 238);
    --primary-lt       : rgba(var(--primary-rgb, 67,97,238), .10);
    --green            : #10B981;
    --green-light      : #ECFDF5;
    --red              : #EF4444;
    --slate-50         : #F8FAFC;
    --slate-100        : #F1F5F9;
    --slate-200        : #E2E8F0;
    --slate-300        : #CBD5E1;
    --slate-400        : #94A3B8;
    --slate-500        : #64748B;
    --slate-600        : #475569;
    --slate-700        : #334155;
    --slate-800        : #1E293B;
    --slate-900        : #0F172A;
    --radius           : 8px;
    --radius-sm        : 5px;
    --shadow-sm        : 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
    --shadow-md        : 0 4px 14px rgba(15,23,42,.08);
    --shadow-lg        : 0 10px 30px rgba(15,23,42,.12);
    --font             : inherit;
    --dash-primary     : var(--primary);
    --dash-primary-rgb : var(--primary-rgb);
    --dash-primary-lt  : var(--primary-lt);
}

/* ══ Animations ══ */
@keyframes fadeUp        { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes spin          { to{transform:rotate(360deg)} }
@keyframes shimmer       { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1}    to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn     { from{opacity:0} to{opacity:1} }
@keyframes overlayOut    { from{opacity:1} to{opacity:0} }
@keyframes detailIn      { from{transform:translateX(100%)} to{transform:translateX(0)} }
@keyframes dpUp          { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
@keyframes kpiIconBounce { 0%{transform:scale(1)} 40%{transform:scale(1.25)} 60%{transform:scale(.95)} 100%{transform:scale(1)} }
@keyframes kpiShimmer    { 0%{left:-100%} 100%{left:150%} }

.fade-up    { animation:fadeUp .38s ease-out both; }
.fade-up-d1 { animation-delay:.05s }
.fade-up-d2 { animation-delay:.10s }
.fade-up-d3 { animation-delay:.15s }
.fade-up-d4 { animation-delay:.20s }

/* ══ KPI Card Icons ══ */
.kpi-icon-bg {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0;
}

/* ══ KPI Card Hover ══ */
.kpi-card-hover {
    will-change: transform, box-shadow;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1) !important,
                box-shadow .25s ease !important, filter .25s ease !important;
    position: relative !important; overflow: hidden !important;
}
.kpi-card-hover::before {
    content: ''; position: absolute; top: 0; bottom: 0; left: -100%;
    width: 60%; background: linear-gradient(90deg, transparent, rgba(255,255,255,.22), transparent);
    pointer-events: none; z-index: 1;
}
.kpi-card-hover:hover { transform: translateY(-6px) scale(1.025) !important; box-shadow: 0 20px 40px rgba(0,0,0,.25) !important; filter: brightness(1.07) !important; }
.kpi-card-hover:hover::before { animation: kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background: rgba(255,255,255,.35) !important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation: kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important; display: inline-block !important; }
.kpi-card-hover:active { transform: translateY(-2px) scale(1.01) !important; transition-duration: .08s !important; }
.kpi-card-hover.clickable { cursor: pointer; }

/* ══ KPI Stat Cards ══ */
.ms-stat-card {
    border-radius:0; padding:18px 20px; box-shadow:var(--shadow-sm);
    transition:transform .18s, box-shadow .18s; position:relative; overflow:hidden;
    color:#fff; border:none; display:flex; align-items:center; gap:14px; margin-bottom:0;
}
.ms-stat-card.clickable { cursor:pointer; }
.ms-stat-card.clickable:hover { box-shadow:var(--shadow-lg); transform:translateY(-2px); }
.ms-stat-card--blue   { background:#0284c7; }
.ms-stat-card--green  { background:#10B981; }
.ms-stat-card--purple { background:#7c3aed; }
.ms-stat-card--amber  { background:#d97706; }
.ms-stat-content { flex:1; min-width:0; }
.ms-stat-label  { font-size:11px; font-weight:600; color:rgba(255,255,255,.78); margin-bottom:8px; display:flex; align-items:center; gap:6px; }
.ms-stat-dot    { width:7px; height:7px; border-radius:50%; flex-shrink:0; background:rgba(255,255,255,.45); }
.ms-stat-value  { font-size:28px; font-weight:700; color:#fff; letter-spacing:-1px; line-height:1; min-height:36px; display:flex; align-items:center; }
.ms-stat-sub    { font-size:11px; color:rgba(255,255,255,.68); font-weight:500; margin-top:6px; }
.ms-stat-hint   { font-size:10px; color:rgba(255,255,255,.72); font-weight:600; margin-top:7px; display:flex; align-items:center; gap:4px; }
.ms-stat-hint i { font-size:11px; }

/* ══ Platform Mini Cards ══ */
.ms-plat-card {
    background:#fff; border:1px solid var(--slate-200); border-radius:var(--radius);
    padding:12px 14px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:10px;
    transition:transform .2s cubic-bezier(.4,0,.2,1), box-shadow .2s cubic-bezier(.4,0,.2,1), border-color .2s;
    cursor:pointer; user-select:none;
}
.ms-plat-card:hover { transform:translateY(-4px); box-shadow:0 8px 20px rgba(15,23,42,.12); border-color:rgba(var(--primary-rgb),.3); }
.ms-plat-card:active { transform:translateY(-1px); }
.ms-plat-icon { width:38px; height:38px; border-radius:var(--radius); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.ms-plat-name  { font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ms-plat-count { font-size:17px; font-weight:700; color:var(--slate-900); letter-spacing:-.4px; min-height:24px; display:flex; align-items:center; }

/* ══ Grid Layouts ══ */
.ms-grid-3-2 { display:grid; grid-template-columns:1.55fr 1fr; gap:16px; }
.ms-grid-2-3 { display:grid; grid-template-columns:1fr 1.55fr; gap:16px; }

/* ══ Chart Heights ══ */
.ms-ch { position:relative; }
.ms-ch-280 { height:280px; }
.ms-ch-300 { height:300px; }
.ms-ch-320 { height:320px; }
.ms-ch-340 { height:340px; }
.ms-ch > div[id] { width:100%; height:100%; }

/* ══ SOV body ══ */
.ms-sov-body { padding:16px 18px; display:flex; flex-direction:column; align-items:center; }

/* ══ Tabs ══ */
.ms-tabs { display:flex; gap:2px; background:var(--slate-100); border:1px solid var(--slate-200); border-radius:var(--radius-sm); padding:3px; margin-bottom:14px; }
.ms-tab-btn { flex:1; display:flex; align-items:center; justify-content:center; gap:7px; padding:7px 16px; border-radius:3px; border:none; background:transparent; font-family:inherit; font-size:12px; font-weight:600; color:var(--slate-500); cursor:pointer; transition:all .13s; }
.ms-tab-btn:hover { background:#fff; color:var(--slate-800); }
.ms-tab-btn.active { background:#fff; color:var(--primary); box-shadow:0 1px 4px rgba(0,0,0,.08); }
.ms-tab-btn i { font-size:14px; }
.ms-tab-panel { display:none; }
.ms-tab-panel.active { display:block; }

/* ══ Toggle Group ══ */
.ms-toggle-group { display:flex; background:var(--slate-100); border-radius:var(--radius-sm); padding:2px; gap:2px; border:1px solid var(--slate-200); }
.ms-toggle-btn { display:flex; align-items:center; gap:5px; padding:4px 10px; border-radius:3px; border:none; background:transparent; font-family:inherit; font-size:11px; font-weight:600; color:var(--slate-500); cursor:pointer; transition:all .13s; white-space:nowrap; }
.ms-toggle-btn:hover { background:#fff; color:var(--slate-800); }
.ms-toggle-btn.active { background:#fff; color:var(--primary); box-shadow:0 1px 4px rgba(0,0,0,.08); }

/* ══ CSV Button ══ */
.ms-csv-btn { display:flex; align-items:center; gap:5px; padding:4px 10px; background:var(--slate-100); border:1px solid var(--slate-200); border-radius:var(--radius-sm); font-family:inherit; font-size:11px; font-weight:600; color:var(--slate-500); cursor:pointer; transition:all .13s; white-space:nowrap; }
.ms-csv-btn:hover { background:var(--primary); border-color:var(--primary); color:#fff; }

/* ══ Skeleton ══ */
.sk-block { border-radius:4px; background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; }
.sk-overlay { position:absolute; inset:0; z-index:3; border-radius:inherit; }

/* ══ Empty State ══ */
.ms-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:44px 20px; gap:8px; }
.ms-empty i { font-size:38px; color:var(--slate-300); }
.ms-empty span { font-size:12px; font-weight:600; color:var(--slate-400); }

/* ══ CSV Modal ══ */
.ms-csv-modal { position:fixed; inset:0; z-index:99998; background:rgba(15,23,42,.5); backdrop-filter:blur(5px); display:none; align-items:center; justify-content:center; }
.ms-csv-modal.show { display:flex; }
.ms-csv-modal__box { background:#fff; border-radius:var(--radius); box-shadow:var(--shadow-lg); width:520px; max-width:92vw; overflow:hidden; animation:dpUp .22s cubic-bezier(.34,1.3,.64,1); }
.ms-csv-modal__head { display:flex; align-items:center; justify-content:space-between; padding:14px 18px; background:var(--slate-50); border-bottom:1px solid var(--slate-200); }
.ms-csv-modal__title { font-size:14px; font-weight:700; color:var(--slate-800); }
.ms-csv-modal__close { width:26px; height:26px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); font-size:16px; transition:all .14s; }
.ms-csv-modal__close:hover { background:var(--red); border-color:var(--red); color:#fff; }
.ms-csv-modal__body { padding:14px 18px; }
.ms-csv-modal__pre { background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius); padding:12px 14px; font-family:'Courier New',monospace; font-size:12px; color:var(--slate-800); line-height:1.7; max-height:260px; overflow-y:auto; white-space:pre; }
.ms-csv-modal__foot { display:flex; align-items:center; justify-content:flex-end; gap:9px; padding:12px 18px; border-top:1px solid var(--slate-200); background:var(--slate-50); }
.ms-csv-copy-btn { display:flex; align-items:center; gap:5px; padding:8px 18px; background:var(--primary); color:#fff; border:none; border-radius:var(--radius); font-family:inherit; font-size:12px; font-weight:700; cursor:pointer; transition:all .13s; }
.ms-csv-copy-btn:hover { filter:brightness(1.1); }
.ms-csv-copy-btn.copied { background:var(--green); }

/* ══ Slide Panel ══ */
.do-panel-overlay { position:fixed; inset:0; z-index:9000; background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none; }
.do-panel-overlay.show   { display:block; animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }
.do-panel { position:fixed; top:0; right:0; bottom:0; z-index:9001; width:480px; max-width:100vw; background:#fff; display:none; flex-direction:column; border-left:1px solid var(--slate-200); box-shadow:-8px 0 40px rgba(15,23,42,.16); }
.do-panel.show   { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }
.do-panel-header { display:flex; align-items:center; gap:9px; padding:14px 16px; border-bottom:1px solid var(--slate-200); background:var(--slate-50); flex-shrink:0; }
.do-panel-dot   { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.do-panel-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-panel-count { background:var(--primary); color:#fff; border-radius:10px; padding:1px 9px; font-size:11px; font-weight:800; flex-shrink:0; }
.do-panel-close { width:28px; height:28px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); font-size:16px; transition:all .14s; flex-shrink:0; }
.do-panel-close:hover { background:var(--red); border-color:var(--red); color:#fff; }
.do-panel-actions { display:flex; align-items:center; gap:7px; padding:7px 12px; border-bottom:1px solid var(--slate-200); background:#fff; flex-shrink:0; }
.do-panel-meta  { flex:1; font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px; display:flex; align-items:center; gap:5px; overflow:hidden; }
.do-panel-export { display:flex; align-items:center; gap:4px; padding:4px 10px; background:var(--primary); color:#fff; border:none; border-radius:var(--radius-sm); font-family:inherit; font-size:10px; font-weight:700; cursor:pointer; transition:filter .13s; }
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
.do-panel-handle { font-size:10px; color:var(--slate-400); font-weight:500; margin-bottom:2px; }
.do-panel-text   { font-size:11px; color:var(--slate-600); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
.do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--slate-400); flex-wrap:wrap; }
.do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.do-sent-badge--neu { background:var(--slate-100); color:var(--slate-500); }
.do-panel-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:12px; color:var(--slate-400); font-size:12px; font-weight:600; }
.do-panel-spinner { width:28px; height:28px; border:2.5px solid var(--slate-100); border-top-color:var(--primary); border-radius:50%; animation:spin .65s linear infinite; }

/* Detail sub-panel */
.do-detail-panel { position:absolute; inset:0; background:#fff; z-index:10; display:none; flex-direction:column; animation:detailIn .2s cubic-bezier(.4,0,.2,1); }
.do-detail-panel.show { display:flex; }
.do-dp2-header { display:flex; align-items:center; gap:8px; padding:12px 14px; background:var(--slate-50); border-bottom:1px solid var(--slate-200); flex-shrink:0; }
.do-dp2-back { width:28px; height:28px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); transition:all .13s; font-size:14px; }
.do-dp2-back:hover { background:var(--primary-lt); color:var(--primary); border-color:var(--primary); }
.do-dp2-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-dp2-body  { overflow-y:auto; flex:1; padding:16px; }
.do-dp2-body::-webkit-scrollbar { width:4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.do-dp2-avatar-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.do-dp2-avatar-lg  { width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700; font-size:16px; display:flex; align-items:center; justify-content:center; border:2px solid var(--slate-200); overflow:hidden; flex-shrink:0; }
.do-dp2-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.do-dp2-name   { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-handle { font-size:11px; color:var(--slate-400); font-weight:500; }
.do-dp2-plat-badge { display:inline-block; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; margin-top:3px; }
.do-dp2-meta   { font-size:11px; color:var(--slate-400); font-weight:500; margin-bottom:10px; }
.do-dp2-sent   { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:3px; font-size:11px; font-weight:700; margin-bottom:10px; }
.do-dp2-sent--pos { background:#dbeafe; color:#1d4ed8; }
.do-dp2-sent--neg { background:#fee2e2; color:#991b1b; }
.do-dp2-sent--neu { background:var(--slate-100); color:var(--slate-500); }
.do-dp2-media     { border-radius:var(--radius); overflow:hidden; margin-bottom:10px; }
.do-dp2-media img { width:100%; max-height:220px; object-fit:cover; display:block; }
.do-dp2-content   { font-size:12px; color:var(--slate-700); line-height:1.7; margin-bottom:12px; background:var(--slate-50); border-radius:var(--radius-sm); padding:10px 12px; border:1px solid var(--slate-200); word-break:break-word; }
.do-dp2-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
.do-dp2-stat  { background:var(--slate-50); border-radius:var(--radius-sm); padding:8px 10px; border:1px solid var(--slate-200); text-align:center; }
.do-dp2-stat-val { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-stat-lbl { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
.do-dp2-link { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 14px; background:var(--primary); color:#fff; border-radius:var(--radius); font-size:12px; font-weight:700; text-decoration:none; transition:filter .14s; margin-top:4px; }
.do-dp2-link:hover { filter:brightness(1.1); color:#fff; }

/* Platform picker */
.do-plat-picker { position:fixed; z-index:999999; background:#fff; border:1px solid var(--slate-200); border-radius:var(--radius); box-shadow:var(--shadow-lg); padding:5px; min-width:175px; font-family:inherit; display:none; animation:fadeUp .14s ease-out; }
.do-plat-picker.show { display:block; }
.do-plat-picker-head { padding:4px 9px 7px; font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--slate-100); margin-bottom:3px; }
.do-plat-btn { display:flex; align-items:center; gap:7px; padding:7px 10px; border-radius:var(--radius-sm); font-size:12px; font-weight:600; cursor:pointer; background:transparent; border:none; font-family:inherit; width:100%; text-align:left; color:var(--slate-700); transition:background .12s; }
.do-plat-btn:hover { background:var(--primary-lt); color:var(--primary); }
.do-plat-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-left:auto; }

/* ══ Responsive ══ */
@media(max-width:1280px) { .ms-grid-3-2,.ms-grid-2-3 { grid-template-columns:1fr; } }
@media(max-width:768px)  { .do-panel { width:100vw; } }
</style>
@endsection

@section('content')
@php
  $projectId = $projectId ?? request()->get('project_id');
  $startDate = $startDate ?? request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = $endDate ?? request()->get('end_date', now()->format('Y-m-d'));
@endphp

{{-- ══ Filter ══ --}}
@include('mk.layouts.partials.filter-datepicker')

{{-- ══ KPI Cards ══ --}}
<div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover clickable" style="background:#0284c7;animation:fadeUp .38s ease-out both;"
             onclick="MSPanel.open('doc', event.clientX, event.clientY)">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Mass Media</p>
                        <h3 class="mb-0 text-white f-w-300" id="valMass">
                            <span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-arrow-square-out me-1"></i>Klik untuk lihat detail
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-newspaper"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover clickable" style="background:#10B981;animation:fadeUp .38s ease-out .05s both;"
             onclick="MSPanel.showPlatPicker(event.clientX, event.clientY)">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Social Media</p>
                        <h3 class="mb-0 text-white f-w-300" id="valSocial">
                            <span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-arrow-square-out me-1"></i>Klik untuk lihat detail
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
        <div class="card h-100 text-white kpi-card-hover" style="background:#7c3aed;animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p>
                        <h3 class="mb-0 text-white f-w-300" id="valTotal">
                            <span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-chat-dots me-1"></i>Mass Media + Social Media
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-chat-dots"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#d97706;animation:fadeUp .38s ease-out .15s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Periode Data</p>
                        <h3 class="mb-0 text-white f-w-300" style="font-size:16px;">
                            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-calendar-blank me-1"></i>s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
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

{{-- ══ Platform Mini Cards ══ --}}
<div class="row g-2 mb-3 fade-up">
    @foreach([
        ['doc',    'pcDoc',  '#0284c7', 'rgba(2,132,199,.1)',    'ph-newspaper',    'Mass Media'],
        ['twit',   'pcTwit', '#1d9bf0', 'rgba(29,155,240,.1)',   'ph-x-logo',       'X / Twitter'],
        ['fb',     'pcFb',   '#1877f2', 'rgba(24,119,242,.1)',   'ph-facebook-logo','Facebook'],
        ['ig',     'pcIg',   '#e1306c', 'rgba(225,48,108,.1)',   'ph-instagram-logo','Instagram'],
        ['yt',     'pcYt',   '#ff0000', 'rgba(255,0,0,.07)',     'ph-youtube-logo', 'YouTube'],
        ['tiktok', 'pcTt',   '#111827', 'rgba(17,24,39,.06)',    'ph-tiktok-logo',  'TikTok'],
    ] as [$key, $elId, $color, $bg, $icon, $label])
    <div class="col-6 col-md-4 col-xl-2">
        <div class="ms-plat-card" onclick="MSPanel.open('{{ $key }}', event.clientX, event.clientY)">
            <div class="ms-plat-icon" style="background:{{ $bg }};">
                <i class="ph {{ $icon }}" style="font-size:20px;color:{{ $color }};"></i>
            </div>
            <div>
                <div class="ms-plat-name">{{ $label }}</div>
                <div class="ms-plat-count" id="{{ $elId }}">
                    <div class="sk-block" style="height:20px;width:50px;border-radius:4px;"></div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ══ Bar Chart + SOV Mass vs Social ══ --}}
<div class="ms-grid-3-2 mb-3 fade-up">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded-circle">
                    <i class="ph ph-chart-bar f-18 text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">Total Mention by Media Platform</h6>
                    <small class="text-muted">Klik bar untuk lihat detail mentions per platform</small>
                </div>
            </div>
            <span class="badge bg-light-secondary text-muted rounded-pill">All Platforms</span>
        </div>
        <div class="card-body">
            <div class="ms-ch ms-ch-300">
                <div id="chBar"></div>
                <div class="sk-block sk-overlay" id="skBar"></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded-circle">
                    <i class="ph ph-chart-donut f-18 text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">Share of Voice</h6>
                    <small class="text-muted">Mass vs Social</small>
                </div>
            </div>
            <span class="badge bg-light-secondary text-muted rounded-pill">2 Categories</span>
        </div>
        <div class="ms-sov-body">
            <div style="position:relative;height:280px;width:100%;">
                <div id="chSovMass" style="width:100%;height:100%;"></div>
                <div class="sk-block" style="position:absolute;inset:0;border-radius:6px;" id="skSovMass"></div>
            </div>
        </div>
    </div>
</div>

{{-- ══ SOV Platform + Bar Race ══ --}}
<div class="ms-grid-2-3 mb-3 fade-up">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded-circle">
                    <i class="ph ph-chart-donut f-18 text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">Share of Voice</h6>
                    <small class="text-muted">Breakdown per platform</small>
                </div>
            </div>
            <span class="badge bg-light-secondary text-muted rounded-pill">By Platform</span>
        </div>
        <div class="ms-sov-body">
            <div style="position:relative;height:340px;width:100%;">
                <div id="chSovPlat" style="width:100%;height:100%;"></div>
                <div class="sk-block" style="position:absolute;inset:0;border-radius:6px;" id="skSovPlat"></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded-circle">
                    <i class="ph ph-chart-bar-horizontal f-18 text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">Mentions per Platform</h6>
                    <small class="text-muted">Volume &amp; share — klik untuk lihat mentions</small>
                </div>
            </div>
            <span class="badge bg-light-success text-success rounded-pill">Bar Race</span>
        </div>
        <div class="card-body p-0">
            <div style="position:relative;height:320px;">
                <div id="chBarRace" style="width:100%;height:100%;"></div>
                <div class="sk-block sk-overlay" id="skBarRace"></div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Tabs ══ --}}
<div class="ms-tabs fade-up">
    <button class="ms-tab-btn active" id="tabBtnTrend" onclick="MSTab.show('trend')">
        <i class="ph ph-pulse"></i> Trend Mentions
    </button>
    <button class="ms-tab-btn" id="tabBtnPola" onclick="MSTab.show('pola')">
        <i class="ph ph-clock"></i> Pola Waktu Posting
    </button>
</div>

{{-- ══ TAB: TREND ══ --}}
<div class="ms-tab-panel active" id="panelTrend">

    {{-- Trend Mentions — ApexCharts area --}}
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded-circle">
                    <i class="ph ph-pulse f-18 text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">The Trends of Total Mentions by Media Types</h6>
                    <small class="text-muted" id="trendSubtitle">Loading…</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="ms-toggle-group" id="trendToggle">
                    <button class="ms-toggle-btn active" data-mode="daily" onclick="MSTrendToggle.set('daily')">
                        <i class="ph ph-pulse" style="font-size:11px;"></i> Harian
                    </button>
                    <button class="ms-toggle-btn" data-mode="monthly" onclick="MSTrendToggle.set('monthly')">
                        <i class="ph ph-calendar-blank" style="font-size:11px;"></i> Bulanan
                    </button>
                </div>
                <div class="ms-toggle-group" id="weekNavGroup">
                    <button class="ms-toggle-btn" id="weekNavPrev" onclick="MSTrendToggle.navWeek(1)">
                        <i class="ph ph-caret-left" style="font-size:11px;"></i>
                    </button>
                    <span id="weekNavLabel" style="padding:4px 9px;font-size:10px;font-weight:700;color:var(--slate-500);white-space:nowrap;display:flex;align-items:center;">Minggu Ini</span>
                    <button class="ms-toggle-btn" id="weekNavNext" onclick="MSTrendToggle.navWeek(-1)" disabled style="opacity:.35;cursor:not-allowed;">
                        <i class="ph ph-caret-right" style="font-size:11px;"></i>
                    </button>
                </div>
                <button class="ms-csv-btn" onclick="MSTrendToggle.copyCSV()">
                    <i class="ph ph-copy" style="font-size:11px;"></i> CSV
                </button>
                <span class="badge bg-light-primary text-primary rounded-pill" id="trendBadge">Loading…</span>
            </div>
        </div>
        <div class="card-body">
            <div class="ms-ch ms-ch-340" style="position:relative;">
                <div class="sk-block sk-overlay" id="skTrend"></div>
                <div id="chTrend" style="width:100%;height:340px;"></div>
            </div>
        </div>
    </div>

    {{-- Article Trend — ApexCharts area --}}
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded-circle">
                    <i class="ph ph-newspaper f-18 text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">The Trends of Total Articles by Media Types</h6>
                    <small class="text-muted">Artikel Online News per hari</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="ms-csv-btn" onclick="MSCsvModal.showArticleTrend()">
                    <i class="ph ph-copy" style="font-size:11px;"></i> CSV
                </button>
                <span class="badge bg-light-primary text-primary rounded-pill" id="articleTrendBadge">Loading…</span>
            </div>
        </div>
        <div class="card-body">
            <div class="ms-ch ms-ch-340" style="position:relative;">
                <div class="sk-block sk-overlay" id="skArticleTrend"></div>
                <div id="chArticleTrend" style="width:100%;height:340px;"></div>
            </div>
        </div>
    </div>

</div>

{{-- ══ TAB: POLA WAKTU ══ --}}
<div class="ms-tab-panel" id="panelPola">
    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded-circle">
                            <i class="ph ph-calendar-blank f-18 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Mentions by Weekday</h6>
                            <small class="text-muted">Total mention per hari dalam seminggu</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="ms-csv-btn" onclick="MSCsvModal.showWeekday()">
                            <i class="ph ph-copy" style="font-size:11px;"></i> CSV
                        </button>
                        <span class="badge bg-light-secondary text-muted rounded-pill">7 Hari</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="ms-ch ms-ch-280">
                        <div id="chWeekday"></div>
                        <div class="sk-block sk-overlay" id="skWeekday"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded-circle">
                            <i class="ph ph-clock f-18 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Mentions by Hour</h6>
                            <small class="text-muted">Distribusi volume mention per jam (00–23)</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="ms-csv-btn" onclick="MSCsvModal.showHour()">
                            <i class="ph ph-copy" style="font-size:11px;"></i> CSV
                        </button>
                        <span class="badge bg-light-secondary text-muted rounded-pill">24 Jam</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="ms-ch ms-ch-280">
                        <div id="chHour"></div>
                        <div class="sk-block sk-overlay" id="skHour"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Slide Panel ══ --}}
<div class="do-panel-overlay" id="msPanelOverlay" onclick="MSPanel.closeByOverlay()"></div>
<div class="do-panel" id="msSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="msPanelDot"></div>
        <span class="do-panel-title" id="msPanelTitle">Mentions</span>
        <span class="do-panel-count" id="msPanelCount">…</span>
        <button class="do-panel-close" onclick="MSPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta">
            <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
            <span id="msPanelMeta">—</span>
        </div>
        <button class="do-panel-export" onclick="MSPanel.exportCsv()">
            <i class="ph ph-download-simple"></i> Export CSV
        </button>
    </div>
    <div class="do-panel-list" id="msPanelList"></div>
    <div class="do-detail-panel" id="msDetailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="MSDetail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="msDpTitle">Detail</span>
            <button class="do-panel-close" onclick="MSPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="msDpBody"></div>
    </div>
</div>

{{-- Platform Picker ══ --}}
<div class="do-plat-picker" id="msPlatPicker">
    <div class="do-plat-picker-head">Pilih Platform</div>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('twit')">X / Twitter <span class="do-plat-dot" style="background:#1d9bf0;"></span></button>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('fb')">Facebook <span class="do-plat-dot" style="background:#1877f2;"></span></button>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('ig')">Instagram <span class="do-plat-dot" style="background:#e1306c;"></span></button>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('yt')">YouTube <span class="do-plat-dot" style="background:#ff0000;"></span></button>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('tiktok')">TikTok <span class="do-plat-dot" style="background:#111827;"></span></button>
</div>

{{-- CSV Modal ══ --}}
<div class="ms-csv-modal" id="msCsvModal">
    <div style="position:absolute;inset:0;" onclick="MSCsvModal.close()"></div>
    <div class="ms-csv-modal__box">
        <div class="ms-csv-modal__head">
            <span class="ms-csv-modal__title">CSV Data</span>
            <button class="ms-csv-modal__close" onclick="MSCsvModal.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="ms-csv-modal__body">
            <pre class="ms-csv-modal__pre" id="msCsvContent"></pre>
        </div>
        <div class="ms-csv-modal__foot">
            <button class="ms-csv-copy-btn" id="msCsvCopyBtn" onclick="MSCsvModal.copy()">
                <i class="ph ph-copy" style="font-size:12px;"></i> Copy CSV data
            </button>
            <button onclick="MSCsvModal.close()" style="padding:8px 18px;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius);font-family:inherit;font-size:12px;font-weight:600;color:var(--slate-600);cursor:pointer;">
                <i class="ph ph-x me-1"></i> Close
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

/* ══ CONFIG ══ */
const MSCfg = {
  pid : {{ $projectId ? (int)$projectId : 'null' }},
  sd  : '{{ $startDate }}',
  ed  : '{{ $endDate }}',
  platColors: {
    'Mass Media':'#0284c7','X (Twitter)':'#1d9bf0','Facebook':'#1877f2',
    'Instagram':'#e1306c','YouTube':'#ff0000','TikTok':'#111827',
    doc:'#0284c7',twit:'#1d9bf0',twitter:'#1d9bf0',
    fb:'#1877f2',facebook:'#1877f2',
    ig:'#e1306c',instagram:'#e1306c',
    yt:'#ff0000',youtube:'#ff0000',
    tiktok:'#111827',tt:'#111827',
  },
  platMeta: {
    doc    : { label:'Online News',  color:'#0284c7' },
    twit   : { label:'X / Twitter', color:'#1d9bf0' },
    fb     : { label:'Facebook',    color:'#1877f2' },
    ig     : { label:'Instagram',   color:'#e1306c' },
    yt     : { label:'YouTube',     color:'#ff0000' },
    tiktok : { label:'TikTok',      color:'#111827' },
  }
};

/* ══ UTILS ══ */
const numFmt    = n => parseInt(n||0).toLocaleString('id-ID');
const numK      = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc       = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const hideSk    = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const showSk    = id => { const e=document.getElementById(id); if(e) e.style.display=''; };
const emptyHtml = msg => `<div class="ms-empty"><i class="ph ph-warning-circle"></i><span>${msg}</span></div>`;
const labelToKey= { 'Mass Media':'doc','X (Twitter)':'twit','Facebook':'fb','Instagram':'ig','YouTube':'yt','TikTok':'tiktok' };
const Y_AXIS_IDX= { doc:1, twitter:0, facebook:1, instagram:1, youtube:1, tiktok:1 };

/* ══ ECharts registry (for bar, sov, bar-race, weekday, hour) ══ */
const MSCharts = {
  _i: {},
  make(id) {
    if(this._i[id]) { try{this._i[id].dispose();}catch(e){} }
    const dom = document.getElementById(id); if(!dom) return null;
    const c = echarts.init(dom,null,{renderer:'canvas'});
    this._i[id]=c; return c;
  },
  disposeAll() { Object.values(this._i).forEach(c=>{try{c.dispose();}catch(e){}});this._i={}; }
};

/* ══ ApexCharts instances (for trend charts) ══ */
const APX = { trend: null, article: null };
function _destroyApx(key) {
  if(APX[key]) { try{ APX[key].destroy(); }catch(e){} APX[key]=null; }
}

window.addEventListener('resize', () => {
  Object.values(MSCharts._i).forEach(c=>{try{if(!c.isDisposed())c.resize();}catch(e){}});
});

const EC_TT = {
  backgroundColor:'#1e293b', borderColor:'#334155', borderWidth:1,
  padding:[10,14], textStyle:{ color:'#fff', fontFamily:"'Poppins',sans-serif", fontSize:12 },
  extraCssText:'border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.28);'
};

/* ══ ApexCharts base options — matches realTimeOptions ══ */
function apxBase(colors, series, categories, height=340) {
  return {
    chart: {
      type: 'area',
      height: height,
      fontFamily: 'inherit',
      background: 'transparent',
      toolbar: { show: false },
      animations: {
        enabled: true,
        easing: 'linear',
        dynamicAnimation: { speed: 1000 }
      }
    },
    series,
    colors,
    xaxis: {
      categories,
      axisBorder: { show: false },
      axisTicks:  { show: false },
      labels: { style: { fontFamily: 'inherit', fontSize: '11px', fontWeight: 600, colors: '#94A3B8' } }
    },
    yaxis: {
      labels: {
        formatter: v => numK(v),
        style: { fontFamily: 'inherit', fontSize: '10px', fontWeight: 600, colors: '#94A3B8' }
      },
      axisBorder: { show: false },
      axisTicks:  { show: false }
    },
    fill:   { opacity: 0.3 },
    stroke: { curve: 'smooth', width: 2.5 },
    grid: {
      borderColor: 'rgba(226,232,240,.55)',
      strokeDashArray: 3,
      xaxis: { lines: { show: false } }
    },
    legend: {
      position: 'bottom',
      horizontalAlign: 'left',
      fontFamily: 'inherit',
      fontSize: '11px',
      fontWeight: '600',
      labels: { colors: '#94A3B8' },
      markers: { width: 9, height: 9, radius: 50 },
      itemMargin: { horizontal: 14, vertical: 4 }
    },
    tooltip: {
      shared: true,
      intersect: false,
      style: { fontFamily: 'inherit', fontSize: '12px' },
      y: { formatter: v => numFmt(v) + ' mentions' }
    },
    dataLabels: {
    enabled: true,
    formatter: v => v > 0 ? numFmt(v) : '',
    style: {
        fontSize: '10px',
        fontFamily: 'inherit',
        fontWeight: '700',
    },
    background: {
        enabled: true,
        borderRadius: 3,
        borderWidth: 0,
        padding: 3,
        opacity: 0.9,
    },
    offsetY: -6,
},
markers: {
    size: 5,
    strokeWidth: 2,
    strokeColors: '#fff',
    hover: { size: 7 }
},
  };
}

/* ══ TAB SYSTEM ══ */
const MSTab = {
  _loaded:{ trend:false, pola:false },
  show(tab) {
    document.querySelectorAll('.ms-tab-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById('tabBtn'+tab.charAt(0).toUpperCase()+tab.slice(1))?.classList.add('active');
    document.querySelectorAll('.ms-tab-panel').forEach(p=>p.classList.remove('active'));
    document.getElementById('panel'+tab.charAt(0).toUpperCase()+tab.slice(1))?.classList.add('active');
    if(tab==='trend'&&!this._loaded.trend){ this._loaded.trend=true; loadTrend(); loadArticleTrend(); }
    if(tab==='pola'){
      if(!this._loaded.pola){ this._loaded.pola=true; loadWeekHour(); }
      else setTimeout(()=>['chWeekday','chHour'].forEach(id=>{const c=MSCharts._i[id];try{if(c&&!c.isDisposed())c.resize();}catch(e){}},60));
    }
    if(tab==='trend'){
      setTimeout(()=>{
        if(APX.trend)  try{ APX.trend.updateOptions({}); }catch(e){}
        if(APX.article)try{ APX.article.updateOptions({}); }catch(e){}
      },60);
    }
  },
  reset(){ this._loaded={trend:false,pola:false}; }
};

/* ══ ECharts Doughnut ══ */
function makeEDoughnut(domId,labels,values,colors,onClickFns,subtitles){
  const total=values.reduce((a,b)=>a+b,0);
  const chart=MSCharts.make(domId);if(!chart)return null;
  const seriesData=labels.map((label,i)=>({
    name:label,value:values[i],subtitle:subtitles?subtitles[i]:'',
    itemStyle:{color:colors[i],borderColor:'#fff',borderWidth:3},
  }));
  chart.setOption({
    animation:true,animationDuration:800,animationEasing:'cubicOut',backgroundColor:'transparent',
    tooltip:{trigger:'item',backgroundColor:'#1e293b',borderColor:'#334155',borderWidth:1,padding:[10,14],
      textStyle:{color:'#fff',fontFamily:"'Poppins',sans-serif",fontSize:12},extraCssText:'border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.28);',
      formatter:params=>{const pct=total>0?((params.value/total)*100).toFixed(1):'0.0';const sub=params.data.subtitle?`<br><span style="color:#94a3b8;font-size:11px;">${params.data.subtitle}</span>`:'';
        return`<div style="font-weight:700;margin-bottom:5px;">${params.name}${sub}</div><div style="display:flex;justify-content:space-between;gap:18px;margin-top:4px;"><span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">${numFmt(params.value)}</span></div><div style="display:flex;justify-content:space-between;gap:18px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct}%</span></div>`;}
    },
    legend:{show:false},
    series:[{type:'pie',radius:['46%','64%'],center:['50%','52%'],avoidLabelOverlap:true,minAngle:5,itemStyle:{borderRadius:6},
      label:{show:true,alignTo:'edge',edgeDistance:10,lineHeight:17,fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#374151',
        formatter:params=>{const pct=total>0?(params.value/total*100):0;if(pct<2)return'';const name=params.name.length>11?params.name.slice(0,10)+'…':params.name;const sub=params.data.subtitle?(params.data.subtitle.length>11?params.data.subtitle.slice(0,10)+'…':params.data.subtitle):'';
          return sub?`{name|${name}}\n{sub|${sub}}\n{eng|${numK(params.value)}}` : `{name|${name}}\n{eng|${numK(params.value)}}`;},
        rich:{name:{fontWeight:'700',fontSize:12,color:'#1a202c',lineHeight:19},sub:{fontWeight:'400',fontSize:10.5,color:'#64748b',lineHeight:16},eng:{fontWeight:'700',fontSize:11,color:'#4361EE',lineHeight:16,backgroundColor:'#eff2fe',borderRadius:4,padding:[1,5]}},
      },
      labelLine:{show:true,length:14,length2:18,smooth:.4,minTurnAngle:130,lineStyle:{color:'#c4cdd8',width:1.2,type:'solid'},showAbove:false},
      emphasis:{scale:true,scaleSize:5,itemStyle:{shadowBlur:10,shadowColor:'rgba(0,0,0,.12)'}},
      data:seriesData,
    }],
    graphic:[
      {type:'text',left:'center',top:'47%',z:100,style:{text:numK(total),fill:'#0f172a',font:"800 24px 'Poppins',sans-serif",textAlign:'center'}},
      {type:'text',left:'center',top:'55%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"600 9px 'Poppins',sans-serif",textAlign:'center',letterSpacing:2}},
    ],
  });
  if(onClickFns){chart.on('click',params=>{const fn=onClickFns[params.dataIndex];if(typeof fn==='function'){const rect=chart.getDom().getBoundingClientRect();fn(rect.left+rect.width/2,rect.top+rect.height/2);}});}
  chart.on('mouseover',()=>{if(onClickFns)chart.getDom().style.cursor='pointer';});
  chart.on('mouseout', ()=>{chart.getDom().style.cursor='default';});
  return chart;
}

/* ══ LOAD: MENTION BY PLATFORM ══ */
async function loadMentionByPlatform(){
  if(!MSCfg.pid){ ['valMass','valSocial','valTotal'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<span style="font-size:13px;color:#94a3b8;">—</span>'}); ['skBar','skSovMass','skSovPlat','skBarRace'].forEach(hideSk); return; }
  try{
    const res=await fetch(`/mk/api/media-statistic/mention-by-platform?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`);
    const d=await res.json();
    if(d.error) throw new Error(d.error);
    document.getElementById('valMass').textContent   =numFmt(d.mass_total||0);
    document.getElementById('valSocial').textContent =numFmt(d.social_total||0);
    document.getElementById('valTotal').textContent  =numFmt(d.grand_total||0);
    const platforms=d.platforms||[];
    const pcMap={doc:'pcDoc',twit:'pcTwit',twitter:'pcTwit',fb:'pcFb',facebook:'pcFb',ig:'pcIg',instagram:'pcIg',yt:'pcYt',youtube:'pcYt',tiktok:'pcTt'};
    platforms.forEach(p=>{const key=labelToKey[p.label]||'';const elId=pcMap[key];if(elId){const e=document.getElementById(elId);if(e)e.textContent=numFmt(p.count||0);}});

    hideSk('skBar');
    if(platforms.length){
      const bLabels=platforms.map(p=>p.label),bValues=platforms.map(p=>p.count||0),bColors=platforms.map(p=>MSCfg.platColors[p.label]||'#4361EE');
      const barChart=MSCharts.make('chBar');
      if(barChart){
        barChart.setOption({animation:true,animationDuration:800,animationEasing:'elasticOut',
          tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'shadow',shadowStyle:{color:'rgba(67,97,238,.06)'}},formatter:params=>{const p=params[0];return`<div style="font-weight:700;font-size:13px;margin-bottom:4px;">${p.name}</div><div style="font-size:13px;">${numFmt(p.value)} mentions</div>`;}},
          grid:{top:14,right:14,bottom:34,left:54,containLabel:false},
          xAxis:{type:'category',data:bLabels,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#64748b',interval:0}},
          yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},
          series:[{type:'bar',data:bValues.map((v,i)=>({value:v,itemStyle:{color:bColors[i],borderRadius:[7,7,0,0]},emphasis:{itemStyle:{color:bColors[i],shadowBlur:12,shadowColor:bColors[i]+'66'}}})),barMaxWidth:52,label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:10,color:'#64748b',formatter:p=>numK(p.value)}}]
        });
        barChart.on('click',params=>{const k=labelToKey[bLabels[params.dataIndex]];if(k){const rect=barChart.getDom().getBoundingClientRect();MSPanel.open(k,rect.left+rect.width/2,rect.top+100);}});
        barChart.on('mouseover',()=>{barChart.getDom().style.cursor='pointer';});
        barChart.on('mouseout', ()=>{barChart.getDom().style.cursor='default';});
      }
    } else { document.getElementById('chBar').innerHTML=emptyHtml('Tidak ada data mention'); }

    hideSk('skSovMass');
    makeEDoughnut('chSovMass',['Mass Media','Social Media'],[d.mass_total||0,d.social_total||0],['#0284c7','#10B981'],
      [(x,y)=>MSPanel.open('doc',x,y),(x,y)=>MSPanel.showPlatPicker(x,y)],null);

    hideSk('skSovPlat');
    const nz=platforms.filter(p=>p.count>0);const pList=nz.length?nz:platforms;
    makeEDoughnut('chSovPlat',pList.map(p=>p.label),pList.map(p=>p.count||0),pList.map(p=>MSCfg.platColors[p.label]||'#4361EE'),
      pList.map(p=>{const k=labelToKey[p.label];return k?(x,y)=>MSPanel.open(k,x,y):null;}),
      pList.map(p=>{ const gt=d.grand_total||1; return((p.count||0)/gt*100).toFixed(1)+'%'; }));

    hideSk('skBarRace');
    if(platforms.length){
      const grandTotal=d.grand_total||1;
      const brData=platforms.map(p=>({label:p.label,value:p.count||0,color:MSCfg.platColors[p.label]||'#4361EE'})).sort((a,b)=>a.value-b.value);
      const brMax=Math.max(...brData.map(p=>p.value),1);
      const brChart=MSCharts.make('chBarRace');
      if(brChart){
        const buildSD=items=>items.map(item=>({value:item.value,itemStyle:{color:item.color,borderRadius:[0,9,9,0]},emphasis:{itemStyle:{shadowBlur:18,shadowColor:item.color+'55'}}}));
        brChart.setOption({animation:true,animationDuration:1400,animationDurationUpdate:1100,animationEasing:'elasticOut',animationEasingUpdate:'cubicInOut',backgroundColor:'transparent',
          tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'shadow'},formatter:params=>{const p=params[0];const item=brData.find(x=>x.label===p.name)||{};const pct=((p.value/grandTotal)*100).toFixed(1);const clr=item.color||'#4361EE';
            return`<div style="font-weight:800;font-size:13px;margin-bottom:9px;padding-bottom:7px;border-bottom:1px solid rgba(255,255,255,.12);"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${clr};margin-right:6px;vertical-align:middle;"></span>${p.name}</div><div style="display:flex;justify-content:space-between;gap:22px;margin-bottom:5px;"><span style="font-size:11px;color:#94a3b8;">Mentions</span><span style="font-size:14px;font-weight:700;">${numFmt(p.value)}</span></div><div style="display:flex;justify-content:space-between;gap:22px;"><span style="font-size:11px;color:#94a3b8;">Share of Voice</span><span style="font-size:12px;font-weight:700;color:#34d399;">${pct}%</span></div>`;}},
          grid:{top:10,right:108,bottom:10,left:14,containLabel:true},
          xAxis:{type:'value',max:brMax*1.15,axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f8fafc',type:'solid'}},axisLabel:{show:false}},
          yAxis:{type:'category',data:brData.map(p=>p.label),inverse:false,animationDuration:300,animationDurationUpdate:1100,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'700',color:'#1a202c',margin:12}},
          series:[{realtimeSort:true,type:'bar',data:buildSD(brData),barMaxWidth:40,label:{show:true,position:'right',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#1a202c',formatter:p=>{const pct=((p.value/grandTotal)*100).toFixed(1);return`{val|${numFmt(p.value)}}  {pct|${pct}%}`;},rich:{val:{fontSize:11,fontWeight:'700',color:'#1a202c',fontFamily:"'Poppins',sans-serif"},pct:{fontSize:9,fontWeight:'600',color:'#94a3b8',fontFamily:"'Poppins',sans-serif"}}}}]
        });
        setTimeout(()=>{ const sorted=[...brData].sort((a,b)=>b.value-a.value); brChart.setOption({yAxis:{data:sorted.map(p=>p.label)},series:[{data:buildSD(sorted)}]}); },1600);
        brChart.on('click',params=>{const k=labelToKey[params.name];if(k){const rect=brChart.getDom().getBoundingClientRect();MSPanel.open(k,rect.left+rect.width/2,rect.top+100);}});
        brChart.on('mouseover',()=>{brChart.getDom().style.cursor='pointer';});
        brChart.on('mouseout', ()=>{brChart.getDom().style.cursor='default';});
      }
    } else { const bd=document.getElementById('chBarRace');if(bd)bd.innerHTML=emptyHtml('Tidak ada data mention'); }

  }catch(err){
    console.error('loadMentionByPlatform:',err);
    ['valMass','valSocial','valTotal'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<span style="font-size:12px;color:#dc2626;font-weight:600;">Error</span>';});
    ['skBar','skSovMass','skSovPlat','skBarRace'].forEach(hideSk);
  }
}

/* ══════════════════════════════════════════════════════
   LOAD TREND — ApexCharts area (matches realTimeOptions)
══════════════════════════════════════════════════════ */
async function loadTrend(){
  if(!MSCfg.pid){ hideSk('skTrend'); return; }
  const fmtDate=d=>`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  let trendSD,trendED;
  if(MSTrendToggle._datePickerOverride){ trendSD=MSCfg.sd; trendED=MSCfg.ed; }
  else{
    const now=new Date(),off=MSTrendToggle._weekOffset;
    const edDate=new Date(now);edDate.setDate(now.getDate()-(7*off));
    const sdDate=new Date(now);sdDate.setDate(now.getDate()-(7*(off+1)));
    trendSD=fmtDate(sdDate); trendED=fmtDate(edDate);
  }
  const platMeta={doc:{label:'Online News',color:'#0284c7'},twitter:{label:'Twitter',color:'#1d9bf0'},facebook:{label:'Facebook',color:'#1877f2'},instagram:{label:'Instagram',color:'#e1306c'},youtube:{label:'YouTube',color:'#ff0000'},tiktok:{label:'TikTok',color:'#111827'}};
  const platOrder=['doc','twitter','facebook','instagram','youtube','tiktok'];
  try{
    const res=await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${MSCfg.pid}&start_date=${trendSD}&end_date=${trendED}`);
    const json=await res.json();if(json.error)throw new Error(json.error);
    hideSk('skTrend');
    const raw=json.data||[];
    const dSet=new Set();raw.forEach(p=>(p.data||[]).forEach(d=>dSet.add(d.date)));
    const allDates=Array.from(dSet).sort();
    MSTrendToggle.setData(raw);

    /* Monthly mode → delegate */
    if(MSTrendToggle._mode==='monthly'){ MSTrendToggle._render(raw); return; }

    const fmtB=d=>{const dt=new Date(d+'T00:00:00');return`${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`;};
    document.getElementById('trendBadge').textContent=`${fmtB(trendSD)} – ${fmtB(trendED)}`;
    const sub=document.getElementById('trendSubtitle');if(sub)sub.textContent=`${fmtB(trendSD)} – ${fmtB(trendED)}`;

    /* week nav */
    const weekNavGroup=document.getElementById('weekNavGroup'),weekNavLabel=document.getElementById('weekNavLabel'),weekNavNext=document.getElementById('weekNavNext');
    if(weekNavGroup&&!MSTrendToggle._datePickerOverride){ weekNavGroup.style.display='flex';if(weekNavLabel)weekNavLabel.textContent=MSTrendToggle._weekLabel();if(weekNavNext){const ic=MSTrendToggle._weekOffset===0;weekNavNext.disabled=ic;weekNavNext.style.opacity=ic?'.35':'1';weekNavNext.style.cursor=ic?'not-allowed':'pointer';} }
    else if(weekNavGroup) weekNavGroup.style.display='none';

    /* Build series */
    const xLabels=allDates.map(d=>{const dt=new Date(d+'T00:00:00');return`${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`;});
    const seriesArr=platOrder.map(key=>{
      const meta=platMeta[key];
      const found=raw.find(p=>p.key===key);
      const vals=allDates.map(date=>{ const pt=(found?.data||[]).find(x=>x.date===date);return pt?pt.count:0; });
      return { name:meta.label, data:vals };
    }).filter(s=>s.data.some(v=>v>0));
    const colorsArr=seriesArr.map(s=>{ const key=Object.keys(platMeta).find(k=>platMeta[k].label===s.name);return platMeta[key]?.color||'#94a3b8'; });

    _destroyApx('trend');
    const el=document.getElementById('chTrend'); if(!el) return;
    APX.trend = new ApexCharts(el, apxBase(colorsArr, seriesArr, xLabels, 340));
    APX.trend.render();

    /* click → open panel */
    el.addEventListener('click', e => {
      const seriesName=e.target.closest('[seriesName]')?.getAttribute('seriesName');
      const keyMap={'Online News':'doc','Twitter':'twit','Facebook':'fb','Instagram':'ig','YouTube':'yt','TikTok':'tiktok'};
      const k=keyMap[seriesName];
      if(k){ const rect=el.getBoundingClientRect();MSPanel.open(k,rect.left+e.offsetX,rect.top+e.offsetY); }
    });

  }catch(err){
    hideSk('skTrend');
    document.getElementById('trendBadge').textContent='Error';
    document.getElementById('chTrend').innerHTML=emptyHtml('Data trend tidak tersedia');
  }
}

/* ══════════════════════════════════════════════════════
   LOAD ARTICLE TREND — ApexCharts area (matches realTimeOptions)
══════════════════════════════════════════════════════ */
async function loadArticleTrend(){
  if(!MSCfg.pid){ hideSk('skArticleTrend'); return; }
  const fmtB=d=>{const dt=new Date(d+'T00:00:00');return`${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`;};
  try{
    const res=await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`);
    const json=await res.json();if(json.error)throw new Error(json.error);
    hideSk('skArticleTrend');
    const raw=json.data||[];
    const docData=raw.find(p=>p.key==='doc');
    if(!docData||!docData.data?.length){
      document.getElementById('articleTrendBadge').textContent='No Data';
      document.getElementById('chArticleTrend').innerHTML=emptyHtml('Data artikel tidak tersedia untuk periode ini');
      return;
    }
    document.getElementById('articleTrendBadge').textContent=`${fmtB(MSCfg.sd)} – ${fmtB(MSCfg.ed)}`;
    const dates =docData.data.map(d=>d.date);
    const values=docData.data.map(d=>d.count);
    MSCsvModal.setArticleData(dates,values);
    const xLabels=dates.map(d=>{const dt=new Date(d+'T00:00:00');return`${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`;});

    _destroyApx('article');
    const el=document.getElementById('chArticleTrend'); if(!el) return;
    const opts = apxBase(
      ['#0284c7'],
      [{ name:'Online News', data:values }],
      xLabels,
      340
    );
    /* Override tooltip for article chart */
    opts.tooltip.y = { formatter: v => numFmt(v) + ' articles' };
    APX.article = new ApexCharts(el, opts);
    APX.article.render();

    el.addEventListener('click', () => {
      const rect=el.getBoundingClientRect();
      MSPanel.open('doc', rect.left+rect.width/2, rect.top+100);
    });

  }catch(err){
    hideSk('skArticleTrend');
    document.getElementById('articleTrendBadge').textContent='Error';
    document.getElementById('chArticleTrend').innerHTML=emptyHtml('Data artikel tidak tersedia');
  }
}

/* ══ LOAD WEEKDAY & HOUR ══ */
async function loadWeekHour(){
  if(!MSCfg.pid){['skWeekday','skHour'].forEach(hideSk);return;}
  const [wdRes,hrRes]=await Promise.allSettled([
    fetch(`/mk/api/media-statistic/mentions-by-weekday?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`).then(r=>r.json()),
    fetch(`/mk/api/media-statistic/mentions-by-hour?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`).then(r=>r.json()),
  ]);
  const ltk={'Online News (Ind)':'doc','Online News':'doc','Twitter':'twit','Facebook':'fb','Instagram':'ig','YouTube':'yt','TikTok':'tiktok'};
  try{
    if(wdRes.status==='rejected')throw wdRes.reason;
    const json=wdRes.value;const wdNames=json.weekdays||['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];const wdTotal=json.total||Array(7).fill(0);const platItems=json.platforms||[];
    hideSk('skWeekday');MSCsvModal.setWeekdayData(wdNames,platItems);
    if(!wdTotal.some(v=>v>0)){ document.getElementById('chWeekday').innerHTML=emptyHtml('Data weekday tidak tersedia'); return; }
    const wdChart=MSCharts.make('chWeekday');
    if(wdChart){
      const series=platItems.map((plat,pi)=>({name:plat.label,type:'bar',stack:'total',data:plat.data.map((v,di)=>{let isTop=v>0;if(isTop){for(let si=pi+1;si<platItems.length;si++){if(platItems[si].data[di]>0){isTop=false;break;}}}return{value:v,itemStyle:{color:plat.color,borderRadius:isTop?[4,4,0,0]:[0,0,0,0]}}}),emphasis:{focus:'series'}}));
      if(series.length>0)series[series.length-1].label={show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:10,color:'#64748b',formatter:p=>wdTotal[p.dataIndex]>0?numK(wdTotal[p.dataIndex]):''};
      wdChart.setOption({animation:true,animationDuration:800,animationEasing:'elasticOut',
        tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'shadow'},formatter:params=>{const day=params[0]?.axisValue||'';const total=params.reduce((s,p)=>s+(p.value||0),0);const rows=[...params].sort((a,b)=>b.value-a.value).filter(p=>p.value>0).map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:2px 0;"><div style="display:flex;align-items:center;gap:5px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};"></span><span style="font-size:11px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:11px;font-weight:700;">${numFmt(p.value)}</span></div>`).join('');return`<div style="font-weight:700;font-size:12px;margin-bottom:7px;padding-bottom:5px;border-bottom:1px solid rgba(255,255,255,.1);">${day}</div>${rows}<div style="border-top:1px solid rgba(255,255,255,.1);margin-top:5px;padding-top:5px;display:flex;justify-content:space-between;gap:14px;"><span style="font-size:10px;color:#94a3b8;">Total</span><span style="font-size:12px;font-weight:700;">${numFmt(total)}</span></div>`;}},
        legend:{bottom:0,data:platItems.map(p=>p.label),textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},icon:'circle',itemWidth:8,itemHeight:8,itemGap:12},
        grid:{top:22,right:14,bottom:56,left:54},
        xAxis:{type:'category',data:wdNames,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}},
        yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},series});
      wdChart.on('click',params=>{ if(params.componentType!=='series')return;const k=ltk[params.seriesName];const rect=wdChart.getDom().getBoundingClientRect();if(k)MSPanel.open(k,rect.left+params.event.offsetX,rect.top+params.event.offsetY);else MSPanel.showPlatPicker(rect.left+params.event.offsetX,rect.top+params.event.offsetY); });
      wdChart.on('mouseover',params=>{if(params.componentType==='series')wdChart.getDom().style.cursor='pointer';});
      wdChart.on('mouseout', ()=>{wdChart.getDom().style.cursor='default';});
    }
  }catch(e){ hideSk('skWeekday');document.getElementById('chWeekday').innerHTML=emptyHtml('Data tidak tersedia'); }
  try{
    if(hrRes.status==='rejected')throw hrRes.reason;
    const json=hrRes.value;const hrLabels=json.hours||Array.from({length:24},(_,i)=>String(i).padStart(2,'0')+':00');const hrTotal=json.total||Array(24).fill(0);const platItems=json.platforms||[];
    hideSk('skHour');MSCsvModal.setHourData(hrLabels,platItems);
    if(!hrTotal.some(v=>v>0)){ document.getElementById('chHour').innerHTML=emptyHtml('Data per jam tidak tersedia'); return; }
    const hrChart=MSCharts.make('chHour');
    if(hrChart){
      const series=platItems.map((plat,pi)=>({name:plat.label,type:'bar',stack:'total',data:plat.data.map((v,di)=>{let isTop=v>0;if(isTop){for(let si=pi+1;si<platItems.length;si++){if(platItems[si].data[di]>0){isTop=false;break;}}}return{value:v,itemStyle:{color:plat.color,borderRadius:isTop?[3,3,0,0]:[0,0,0,0]}}}),emphasis:{focus:'series'}}));
      hrChart.setOption({animation:true,animationDuration:800,animationEasing:'elasticOut',
        tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'shadow'},formatter:params=>{const hour=params[0]?.axisValue||'';const total=params.reduce((s,p)=>s+(p.value||0),0);const rows=[...params].sort((a,b)=>b.value-a.value).filter(p=>p.value>0).map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:2px 0;"><div style="display:flex;align-items:center;gap:5px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};"></span><span style="font-size:11px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:11px;font-weight:700;">${numFmt(p.value)}</span></div>`).join('');return`<div style="font-weight:700;font-size:12px;margin-bottom:7px;padding-bottom:5px;border-bottom:1px solid rgba(255,255,255,.1);">Jam ${hour}</div>${rows}<div style="border-top:1px solid rgba(255,255,255,.1);margin-top:5px;padding-top:5px;display:flex;justify-content:space-between;gap:14px;"><span style="font-size:10px;color:#94a3b8;">Total</span><span style="font-size:12px;font-weight:700;">${numFmt(total)}</span></div>`;}},
        legend:{bottom:0,data:platItems.map(p=>p.label),textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'},icon:'circle',itemWidth:8,itemHeight:8,itemGap:12},
        grid:{top:22,right:14,bottom:56,left:54},
        xAxis:{type:'category',data:hrLabels,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'600',color:'#64748b',interval:1,rotate:45}},
        yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},series});
      hrChart.on('click',params=>{ if(params.componentType!=='series')return;const k=ltk[params.seriesName];const rect=hrChart.getDom().getBoundingClientRect();if(k)MSPanel.open(k,rect.left+params.event.offsetX,rect.top+params.event.offsetY);else MSPanel.showPlatPicker(rect.left+params.event.offsetX,rect.top+params.event.offsetY); });
      hrChart.on('mouseover',params=>{if(params.componentType==='series')hrChart.getDom().style.cursor='pointer';});
      hrChart.on('mouseout', ()=>{hrChart.getDom().style.cursor='default';});
    }
  }catch(e){ hideSk('skHour');document.getElementById('chHour').innerHTML=emptyHtml('Data tidak tersedia'); }
}

/* ══ SLIDE PANEL ══ */
const MSPanel = (() => {
  let _cache={}, _items=[], _curPlat=null;
  const SENT_MAP={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'};
  const _ns = item => SENT_MAP[String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim()] || 'neu';
  const _$  = id => document.getElementById(id);

  function showPlatPicker(x,y){
    const pp=_$('msPlatPicker');if(!pp)return;
    const pw=180,ph=250,vw=window.innerWidth,vh=window.innerHeight;
    let left=x+10,top=y-10;if(left+pw>vw-8)left=x-pw-10;if(top+ph>vh-8)top=vh-ph-8;if(top<8)top=8;
    pp.style.left=left+'px';pp.style.top=top+'px';pp.classList.add('show');
  }
  function openPlatform(platform){ _$('msPlatPicker')?.classList.remove('show'); open(platform,window.innerWidth-500,80); }

  async function open(platform,x,y){
    _curPlat=platform;const meta=MSCfg.platMeta[platform]||{label:platform,color:'#4361EE'};
    MSDetail.close();
    _$('msPanelDot').style.background=meta.color;_$('msPanelTitle').textContent=meta.label;_$('msPanelMeta').textContent=MSCfg.sd+' – '+MSCfg.ed;_$('msPanelCount').textContent='…';
    const list=_$('msPanelList');list.innerHTML=`<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;
    const overlay=_$('msPanelOverlay'),panel=_$('msSntPanel');
    overlay.classList.remove('hiding');panel.classList.remove('hiding');overlay.classList.add('show');panel.classList.add('show');
    try{
      const key=`${MSCfg.pid}_${platform}_${MSCfg.sd}_${MSCfg.ed}`;
      if(!_cache[key])_cache[key]=await _fetch(platform);
      _items=_cache[key];_$('msPanelCount').textContent=_items.length.toLocaleString();
      _render(list,_items,platform,meta.color);
    }catch(err){ list.innerHTML=`<div class="do-panel-loading" style="color:#94a3b8;"><i class="ph ph-warning-circle" style="font-size:28px;color:#e2e8f0;"></i>Gagal memuat data</div>`;_$('msPanelCount').textContent='0'; }
  }
  function close(){ const overlay=_$('msPanelOverlay'),panel=_$('msSntPanel');panel.classList.add('hiding');overlay.classList.add('hiding');setTimeout(()=>{panel.classList.remove('show','hiding');overlay.classList.remove('show','hiding');MSDetail.close();},240); }
  function closeByOverlay(){ close(); }

  function exportCsv(){
    if(!_items?.length){alert('Tidak ada data untuk diekspor.');return;}
    const lbl={doc:'Online_News',twit:'Twitter',fb:'Facebook',ig:'Instagram',yt:'YouTube',tiktok:'TikTok'};
    const rows=_items.map((item,idx)=>{
      const name=(item.author_name||item.channel_name||item.from_name||item.publisher||item.source_name||'').trim();
      const handle=(item.author_scr_name||item.screen_name||item.username||'').trim();
      const content=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,500);
      const sent={pos:'Positif',neg:'Negatif',neu:'Netral'}[_ns(item)];
      const e2=s=>String(s||'').replace(/;/g,',').replace(/\n/g,' ').replace(/\r/g,'');
      return`${idx};${e2(name)};${e2(handle)};${e2(sent)};${e2(item.date_created||item.created_at||'')};${e2(item.url||item.link||'')};${e2(content)}`;
    });
    const blob=new Blob(['\uFEFF'+'index;nama;handle;sentimen;tanggal;url;konten\r\n'+rows.join('\r\n')],{type:'text/csv;charset=utf-8;'});
    const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=`${lbl[_curPlat]||_curPlat}_${MSCfg.sd}_${MSCfg.ed}.csv`;a.click();URL.revokeObjectURL(a.href);
  }

  async function _fetch(platform){
    const q=`project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}&rows=500&start=0`;
    if(platform==='ig'){for(const sub of['postbylike','postbycomment','postbydate','']){try{const ctrl=new AbortController(),tid=setTimeout(()=>ctrl.abort(),15000);const res=await fetch(`/mk/api/news/ig-top-status?${q}${sub?'&sub='+sub:''}`,{signal:ctrl.signal});clearTimeout(tid);if(!res.ok)continue;const data=await res.json();const items=Array.isArray(data.data)?data.data:(Array.isArray(data)?data:[]);if(items.length>0)return items;}catch(e){continue;}} return[];}
    const eps={doc:`/mk/api/news/mentions?${q}`,twit:`/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,fb:`/mk/api/news/fb-top-status?${q}&sub=fblike`,yt:`/mk/api/news/ytb-top-status?${q}`,tiktok:`/mk/api/news/tiktok-top-status?${q}&sub=postbylike`};
    const url=eps[platform];if(!url)throw new Error('Platform tidak dikenali');
    const ctrl=new AbortController(),tid=setTimeout(()=>ctrl.abort(),30000);
    const res=await fetch(url,{signal:ctrl.signal});clearTimeout(tid);if(!res.ok)throw new Error('HTTP '+res.status);
    const data=await res.json();let items=Array.isArray(data.data)?data.data:(Array.isArray(data)?data:[]);
    if(platform==='doc')items=items.filter(m=>{const tc=String(m.tcode||'').toLowerCase(),mt=String(m.media_type||'').toLowerCase();return tc==='berita'||mt==='berita'||mt==='doc'||mt==='news'||mt==='online'||mt==='article';});
    return items;
  }

  function _render(list,items,platform,color){
    if(!items.length){list.innerHTML=`<div style="padding:50px 20px;text-align:center;color:#94a3b8;font-size:12px;font-weight:600;">Tidak ada mention periode ini.</div>`;return;}
    const SHOW=60;const meta=MSCfg.platMeta[platform]||{label:platform,color};
    list.innerHTML=items.slice(0,SHOW).map(item=>{
      const rawName=(()=>{if(platform==='fb')return item.from_name||item.page_name||null;if(platform==='ig')return item.username||item.user_name||null;if(platform==='tiktok')return item.author_nickname||item.nickname||item.author_name||null;if(platform==='yt')return item.channel_title||item.channel_name||item.author_name||null;return null;})();
      const name=(rawName||item.author_name||item.channel_name||item.publisher||item.source_name||item.name||item.author_scr_name||item.screen_name||item.username||'Tidak diketahui').trim();
      const dName=/^\d{8,}$/.test(name)?`User ${name.slice(-4)}`:name;
      const rawH=((platform==='ig'?item.username:'')||item.author_scr_name||item.screen_name||item.username||'').trim();
      const handle=(()=>{if(!rawH)return'';const w=['twit','ig','tiktok'].includes(platform)?(rawH.startsWith('@')?rawH:'@'+rawH):rawH;return w.replace(/^@/,'').toLowerCase()===dName.toLowerCase()?'':w;})();
      const text=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,155);
      const av=(item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||'').trim();
      const words=dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
      const ini=(words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||dName[0]||'?')).toUpperCase().replace(/['"]/g,'');
      const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini}';">`:ini;
      const sent=_ns(item);const dt=(item.date_created||item.created_at||'').split('T')[0];
      const enc=encodeURIComponent(JSON.stringify(item));
      return`<div class="do-panel-item" onclick="MSDetail.openEncoded('${enc}','${platform}')">
        <div class="do-panel-avatar" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
        <div class="do-panel-item-body">
          <div class="do-panel-author">${esc(dName)}</div>
          ${handle?`<div class="do-panel-handle">${esc(handle)}</div>`:''}
          <div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div>
          <div class="do-panel-footer">
            <span class="do-sent-badge do-sent-badge--${sent}">${sent==='pos'?'Pos':sent==='neg'?'Neg':'Neu'}</span>
            <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${color};flex-shrink:0;"></span>
            <span style="font-size:10px;font-weight:600;color:${color};">${meta.label}</span>
            ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
          </div>
        </div>
      </div>`;
    }).join('');
    if(items.length>SHOW)list.insertAdjacentHTML('beforeend',`<div style="padding:9px 14px;text-align:center;font-size:11px;font-weight:600;color:#94a3b8;background:#F8FAFC;border-top:1px dashed #E2E8F0;">+${(items.length-SHOW).toLocaleString()} mentions lainnya · Export CSV untuk semua</div>`);
  }
  return{open,close,closeByOverlay,showPlatPicker,openPlatform,exportCsv};
})();

/* ══ DETAIL SUB-PANEL ══ */
const MSDetail = {
  openEncoded(enc,plat){ try{this.open(JSON.parse(decodeURIComponent(enc)),plat);}catch(e){} },
  open(item,platform){
    const panel=document.getElementById('msDetailPanel'),body=document.getElementById('msDpBody'),title=document.getElementById('msDpTitle');
    if(!panel||!body)return;
    const meta=MSCfg.platMeta[platform]||{label:platform,color:'#4361EE'};
    const SM={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'};
    const sent=SM[String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim()]||'neu';
    const SLBL={pos:'Positive',neg:'Negative',neu:'Neutral'};const SBGS={pos:'do-dp2-sent--pos',neg:'do-dp2-sent--neg',neu:'do-dp2-sent--neu'};
    const rawName=(()=>{if(platform==='fb')return item.from_name||item.page_name||null;if(platform==='ig')return item.username||null;if(platform==='tiktok')return item.author_nickname||item.nickname||null;if(platform==='yt')return item.channel_title||item.channel_name||null;return null;})();
    const name=(rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'Unknown').trim();
    const handle=((platform==='ig'?item.username:'')||item.author_scr_name||item.screen_name||item.username||'').trim();
    const content=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
    const av=(item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||'').trim();
    const url=item.url||item.link||'';const dt=item.date_created||item.created_at||item.publish_date||'';
    title.textContent=name;
    const words=name.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
    const ini=(words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||name[0]||'?')).toUpperCase().replace(/['"]/g,'');
    const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.parentElement.textContent='${ini}';">`:ini;
    let dtFmt='';if(dt){try{dtFmt=new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){dtFmt=dt.split('T')[0];}}
    let mediaHtml='';
    if(platform==='yt'){const ytId=(url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/)||[])[1];if(ytId)mediaHtml=`<div class="do-dp2-media"><iframe style="width:100%;height:210px;border:none;display:block;" src="https://www.youtube.com/embed/${ytId}?rel=0&modestbranding=1" allowfullscreen></iframe></div>`;}
    else{const imgUrl=item.image_url||item.thumbnail||item.media_url||item.picture||'';if(imgUrl)mediaHtml=`<div class="do-dp2-media"><img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:220px;object-fit:cover;display:block;border-radius:var(--radius);"></div>`;}
    const statsMap={twit:[['Retweet',item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0],['Quote',item.num_quote||0]],fb:[['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],ig:[['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],yt:[['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0]],tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],doc:[['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]]};
    const stats=statsMap[platform]||[];
    const statsHtml=stats.some(s=>parseInt(s[1])>0)?`<div class="do-dp2-stats">${stats.map(([l,v])=>`<div class="do-dp2-stat"><div class="do-dp2-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="do-dp2-stat-lbl">${l}</div></div>`).join('')}</div>`:'';
    const handleDisp=handle&&handle.replace('@','').toLowerCase()!==name.toLowerCase().slice(0,handle.replace('@','').length)?(handle.startsWith('@')?handle:'@'+handle):'';
    body.innerHTML=`
      <div class="do-dp2-avatar-row">
        <div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
        <div><div class="do-dp2-name">${esc(name)}</div>${handleDisp?`<div class="do-dp2-handle">${esc(handleDisp)}</div>`:''}<span class="do-dp2-plat-badge" style="background:${meta.color}22;color:${meta.color};">${meta.label}</span></div>
      </div>
      ${dtFmt?`<div class="do-dp2-meta">${dtFmt}</div>`:''}
      <div class="do-dp2-sent ${SBGS[sent]}">${SLBL[sent]}</div>
      ${mediaHtml}
      ${content?`<div class="do-dp2-content">${esc(content)}</div>`:''}
      ${statsHtml}
      ${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out me-1"></i>Lihat ${meta.label} Asli</a>`:''}`;
    panel.classList.add('show');
  },
  close(){ const panel=document.getElementById('msDetailPanel');if(!panel)return;panel.classList.remove('show');panel.querySelectorAll('iframe').forEach(f=>{try{f.src=f.src;}catch(e){}});}
};

/* ══ TREND TOGGLE ══ */
const MSTrendToggle = {
  _mode:'daily', _trendData:null, _weekOffset:0, _datePickerOverride:false,
  set(mode){
    if(this._mode===mode)return;this._mode=mode;
    document.querySelectorAll('#trendToggle .ms-toggle-btn').forEach(b=>b.classList.toggle('active',b.dataset.mode===mode));
    const sub=document.getElementById('trendSubtitle');if(sub)sub.textContent=mode==='monthly'?'Total mentions per bulan':this._datePickerOverride?`${MSCfg.sd} – ${MSCfg.ed}`:'8 hari terakhir';
    const wng=document.getElementById('weekNavGroup');if(wng)wng.style.display=mode==='daily'&&!this._datePickerOverride?'flex':'none';
    if(mode==='daily'){this._weekOffset=0;this._trendData=null;}
    if(this._trendData)this._render(this._trendData);else loadTrend();
  },
  setData(rawData){ this._trendData=rawData; },
  navWeek(dir){ const next=this._weekOffset+dir;if(next<0)return;this._weekOffset=next;this._trendData=null;loadTrend(); },
  _weekLabel(){ return this._weekOffset===0?'Minggu Ini':`Week -${this._weekOffset}`; },
  copyCSV(){if(!this._trendData){alert('Data belum tersedia');return;}const lines=this._buildCSV(this._trendData,this._mode);MSCsvModal.show('Trend Mentions — '+(this._mode==='monthly'?'Bulanan':'Harian'),lines);},
  _buildCSV(raw,mode){
    const platOrder=['doc','twitter','facebook','instagram','youtube','tiktok'];
    const platMeta={doc:'Online News',twitter:'Twitter',facebook:'Facebook',instagram:'Instagram',youtube:'YouTube',tiktok:'TikTok'};
    if(mode==='monthly'){const monthMap={};raw.forEach(p=>(p.data||[]).forEach(d=>{const m=d.date.slice(0,7);if(!monthMap[m])monthMap[m]={};monthMap[m][p.key]=(monthMap[m][p.key]||0)+d.count;}));const months=Object.keys(monthMap).sort();const lines=[];months.forEach(m=>platOrder.forEach(k=>{const val=monthMap[m][k]||0;if(val>0)lines.push(`${lines.length};${platMeta[k]||k};${val};${m}`);}));return lines;}
    else{const dSet=new Set();raw.forEach(p=>(p.data||[]).forEach(d=>dSet.add(d.date)));const allDates=Array.from(dSet).sort();const lines=[];allDates.forEach(date=>raw.forEach(p=>{const pt=(p.data||[]).find(x=>x.date===date);if(pt&&pt.count>0)lines.push(`${lines.length};${platMeta[p.key]||p.key};${pt.count};${date}`);}));return lines;}
  },
  /* Monthly mode render — ApexCharts stacked bar */
  _render(raw){
    const platMetaFull={doc:{label:'Online News',color:'#0284c7'},twitter:{label:'Twitter',color:'#1d9bf0'},facebook:{label:'Facebook',color:'#1877f2'},instagram:{label:'Instagram',color:'#e1306c'},youtube:{label:'YouTube',color:'#ff0000'},tiktok:{label:'TikTok',color:'#111827'}};
    const platOrder=['doc','twitter','facebook','instagram','youtube','tiktok'];
    if(this._mode==='monthly'){
      const monthMap={};raw.forEach(p=>(p.data||[]).forEach(d=>{const m=d.date.slice(0,7);if(!monthMap[m])monthMap[m]={};monthMap[m][p.key]=(monthMap[m][p.key]||0)+d.count;}));
      const months=Object.keys(monthMap).sort();
      const xLabels=months.map(m=>{const dt=new Date(m+'-01T00:00:00');return dt.toLocaleString('id-ID',{month:'short',year:'numeric'});});
      document.getElementById('trendBadge').textContent=xLabels[0]+'…'+xLabels[xLabels.length-1];
      const sub=document.getElementById('trendSubtitle');if(sub)sub.textContent='Total mentions per bulan';

      const seriesArr=platOrder.map(key=>{
        const meta=platMetaFull[key];
        const vals=months.map(m=>monthMap[m]?.[key]||0);
        if(!vals.some(v=>v>0))return null;
        return { name:meta.label, data:vals };
      }).filter(Boolean);
      const colorsArr=seriesArr.map(s=>{ const k=Object.keys(platMetaFull).find(k=>platMetaFull[k].label===s.name);return platMetaFull[k]?.color||'#94a3b8'; });

      _destroyApx('trend');
      const el=document.getElementById('chTrend'); if(!el) return;
      /* Monthly uses stacked bar style, still ApexCharts */
      APX.trend = new ApexCharts(el, {
        chart:{ type:'bar', height:340, fontFamily:'inherit', background:'transparent', toolbar:{show:false}, stacked:true, animations:{enabled:true,easing:'linear',dynamicAnimation:{speed:1000}} },
        series: seriesArr,
        colors: colorsArr,
        plotOptions:{ bar:{ columnWidth:'60%', borderRadius:3, borderRadiusApplication:'end' } },
        fill:{ opacity:1 },
        stroke:{ show:false },
        xaxis:{ categories:xLabels, axisBorder:{show:false}, axisTicks:{show:false}, labels:{style:{fontFamily:'inherit',fontSize:'11px',fontWeight:600,colors:'#94A3B8'}} },
        yaxis:{ labels:{formatter:v=>numK(v),style:{fontFamily:'inherit',fontSize:'10px',fontWeight:600,colors:'#94A3B8'}}, axisBorder:{show:false}, axisTicks:{show:false} },
        grid:{ borderColor:'rgba(226,232,240,.55)', strokeDashArray:3, xaxis:{lines:{show:false}} },
        legend:{ position:'bottom', horizontalAlign:'left', fontFamily:'inherit', fontSize:'11px', fontWeight:'600', labels:{colors:'#94A3B8'}, markers:{width:9,height:9,radius:50}, itemMargin:{horizontal:14,vertical:4} },
        tooltip:{ shared:true, intersect:false, style:{fontFamily:'inherit',fontSize:'12px'}, y:{formatter:v=>numFmt(v)+' mentions'} },
        dataLabels:{ enabled:false },
      });
      APX.trend.render();
    } else { loadTrend(); }
  }
};

/* ══ CSV MODAL ══ */
const MSCsvModal = {
  _content:'', _articleData:null, _weekdayData:null, _hourData:null,
  show(title,lines){ this._content=lines.join('\n');document.querySelector('.ms-csv-modal__title').textContent=title||'CSV Data';document.getElementById('msCsvContent').textContent=this._content;const btn=document.getElementById('msCsvCopyBtn');btn.innerHTML='<i class="ph ph-copy" style="font-size:12px;"></i> Copy CSV data';btn.classList.remove('copied');document.getElementById('msCsvModal').classList.add('show'); },
  close(){ document.getElementById('msCsvModal').classList.remove('show'); },
  setArticleData(dates,values){ this._articleData={dates,values}; },
  showArticleTrend(){ if(!this._articleData){alert('Data belum tersedia');return;}const{dates,values}=this._articleData;const lines=dates.map((d,i)=>`${i};Online News;${values[i]};${d}`).filter((_,i)=>values[i]>0);this.show('Trend Articles — Online News',lines); },
  setWeekdayData(wdNames,platItems){ this._weekdayData={wdNames,platItems}; },
  showWeekday(){ if(!this._weekdayData){alert('Data belum tersedia');return;}const{wdNames,platItems}=this._weekdayData;const lines=[];wdNames.forEach((day,di)=>platItems.forEach(plat=>{const v=plat.data[di]||0;if(v>0)lines.push(`${lines.length};${plat.label};${v};${day}`);}));this.show('Mentions by Weekday',lines); },
  setHourData(hrLabels,platItems){ this._hourData={hrLabels,platItems}; },
  showHour(){ if(!this._hourData){alert('Data belum tersedia');return;}const{hrLabels,platItems}=this._hourData;const lines=[];hrLabels.forEach((hr,hi)=>platItems.forEach(plat=>{const v=plat.data[hi]||0;if(v>0)lines.push(`${lines.length};${plat.label};${v};${hr}`);}));this.show('Mentions by Hour',lines); },
  copy(){
    if(!this._content)return;const btn=document.getElementById('msCsvCopyBtn');
    navigator.clipboard.writeText(this._content).then(()=>{btn.innerHTML='<i class="ph ph-check" style="font-size:12px;"></i> Tersalin!';btn.classList.add('copied');setTimeout(()=>{btn.innerHTML='<i class="ph ph-copy" style="font-size:12px;"></i> Copy CSV data';btn.classList.remove('copied');},2000);}).catch(()=>{const ta=document.createElement('textarea');ta.value=this._content;ta.style.cssText='position:fixed;opacity:0;';document.body.appendChild(ta);ta.select();document.execCommand('copy');document.body.removeChild(ta);btn.innerHTML='<i class="ph ph-check" style="font-size:12px;"></i> Tersalin!';btn.classList.add('copied');setTimeout(()=>{btn.innerHTML='<i class="ph ph-copy" style="font-size:12px;"></i> Copy CSV data';btn.classList.remove('copied');},2000);});
  }
};

/* ══ INIT ══ */
const MSPage = {
  _syncDateFilter(){
    const today=new Date();today.setHours(0,0,0,0);const ed=new Date(MSCfg.ed+'T00:00:00');const sd=new Date(MSCfg.sd+'T00:00:00');const diff=Math.round((ed-sd)/86400000)+1;
    MSTrendToggle._datePickerOverride=!(ed.getTime()===today.getTime()&&diff<=8);MSTrendToggle._weekOffset=0;
  },
  reload(){
    MSCharts.disposeAll();
    _destroyApx('trend'); _destroyApx('article');
    MSTab.reset();this._syncDateFilter();
    ['skBar','skTrend','skArticleTrend','skWeekday','skHour','skBarRace'].forEach(showSk);
    loadMentionByPlatform();
    const activeTab=document.querySelector('.ms-tab-panel.active')?.id;
    if(activeTab==='panelTrend'){MSTab._loaded.trend=true;loadTrend();loadArticleTrend();}
    else if(activeTab==='panelPola'){MSTab._loaded.pola=true;loadWeekHour();}
  },
  init(){ this._syncDateFilter();loadMentionByPlatform();MSTab._loaded.trend=true;loadTrend();loadArticleTrend(); }
};

document.addEventListener('mousedown',e=>{const pp=document.getElementById('msPlatPicker');if(pp?.classList.contains('show')&&!pp.contains(e.target))pp.classList.remove('show');});
document.addEventListener('DOMContentLoaded',()=>MSPage.init());
</script>
@endsection