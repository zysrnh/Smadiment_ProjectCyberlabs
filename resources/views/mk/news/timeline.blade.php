@extends('mk.layouts.app')

@section('title', 'Mentions Timeline - SMADIMENT')

@section('styles')
<style>
:root{--primary:#038047;--primary-rgb:3,128,71;--primary-lt:rgba(3,128,71,.10);--dark:#273B4A;--white:#FFFFFF;--bg:#F1F5F8;--green:#10B981;--green-lt:#ECFDF5;--red:#EF4444;--red-lt:#FEF2F2;--slate-50:#F8FAFC;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-300:#CBD5E1;--slate-400:#94A3B8;--slate-500:#64748B;--slate-600:#475569;--slate-700:#334155;--slate-800:#1E293B;--slate-900:#0F172A;--radius:8px;--radius-sm:5px;--shadow-sm:0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);--shadow-md:0 4px 14px rgba(15,23,42,.08);--shadow-lg:0 10px 30px rgba(15,23,42,.12)}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes slideInRight{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}
@keyframes slideOutRight{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}
@keyframes overlayIn{from{opacity:0}to{opacity:1}}
@keyframes overlayOut{from{opacity:1}to{opacity:0}}
@keyframes kpiIconBounce{0%,100%{transform:scale(1) rotate(0)}30%{transform:scale(1.25) rotate(-10deg)}60%{transform:scale(1.1) rotate(6deg)}}
@keyframes kpiShimmer{0%{left:-100%}100%{left:150%}}
@keyframes pulseP{0%,100%{box-shadow:0 0 0 3px rgba(255,255,255,.2)}50%{box-shadow:0 0 0 6px transparent}}

/* ══════════════════════════════════════════
   EXPORT MODE — hanya matikan animasi & transform
   TIDAK override opacity agar Bootstrap text-opacity tetap jalan
══════════════════════════════════════════ */
body.is-exporting *,
body.is-exporting *::before,
body.is-exporting *::after {
    animation: none !important;
    animation-delay: 0s !important;
    animation-duration: 0s !important;
    transition: none !important;
}
/* Paksa elemen dengan animasi fadeUp agar langsung tampil penuh */
body.is-exporting .card[style*="animation"],
body.is-exporting .kpi-card-hover {
    opacity: 1 !important;
    transform: none !important;
    filter: none !important;
}
/* Hilangkan shimmer overlay KPI */
body.is-exporting .kpi-card-hover::before {
    display: none !important;
}
/* Shimmer skeleton tampil sebagai block diam */
body.is-exporting .sk-block {
    background: var(--slate-200) !important;
}
/* Spinner tombol export tetap tersembunyi */
body.is-exporting .exp-spinner {
    display: none !important;
}
body.is-exporting .exp-icon {
    display: inline-block !important;
}

/* KPI */
.kpi-icon-bg{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0}
.sk-block{border-radius:4px;background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
.spin-ring{width:26px;height:26px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
.spinner-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;gap:12px;color:var(--slate-400);font-size:12px;font-weight:600}
.kpi-card-hover{will-change:transform,box-shadow;transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important;cursor:default;position:relative!important;overflow:hidden!important}
.kpi-card-hover::before{content:'';position:absolute;top:0;bottom:0;left:-100%;width:60%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);pointer-events:none;z-index:1;transition:none}
.kpi-card-hover:hover{transform:translateY(-6px) scale(1.025)!important;box-shadow:0 20px 40px rgba(0,0,0,.25)!important;filter:brightness(1.07)!important}
.kpi-card-hover:hover::before{animation:kpiShimmer .6s ease forwards}
.kpi-card-hover:hover .kpi-icon-bg{background:rgba(255,255,255,.35)!important}
.kpi-card-hover:hover .kpi-icon-bg i{animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important;display:inline-block!important}
/* Dot Number Badge */
.kpi-dot-num{position:absolute;top:10px;right:10px;min-width:22px;height:20px;border-radius:10px;background:rgba(255,255,255,.22);color:#fff;font-size:9px;font-weight:800;display:flex;align-items:center;justify-content:center;padding:0 6px;letter-spacing:.3px;border:1px solid rgba(255,255,255,.28);backdrop-filter:blur(4px);z-index:2;gap:4px}
.kpi-pulse-dot{width:7px;height:7px;border-radius:50%;background:rgba(255,255,255,.9);flex-shrink:0;animation:pulseP 2.5s infinite}
/* Tabs */
.mt-tabs{display:flex;gap:2px;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px;margin-bottom:16px}
.mt-tab-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:7px 14px;border-radius:4px;border:none;background:transparent;font-size:12px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:background .13s,color .13s;white-space:nowrap}
.mt-tab-btn:hover{background:#fff;color:var(--slate-800)}
.mt-tab-btn.active{background:#fff;color:var(--primary);box-shadow:0 1px 4px rgba(0,0,0,.08)}
.mt-tab-chip{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:16px;padding:0 5px;border-radius:3px;font-size:9px;font-weight:800;background:var(--primary-lt);color:var(--primary)}
.mt-tab-btn:not(.active) .mt-tab-chip{background:var(--slate-100);color:var(--slate-400)}
/* Post List */
.mt-post-list{display:flex;flex-direction:column}
.mt-post{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--slate-100);transition:background .12s;cursor:pointer}
.mt-post:last-child{border-bottom:none}
.mt-post:hover{background:var(--slate-50)}
.mt-post-rank{width:22px;height:22px;border-radius:50%;background:var(--slate-100);border:1px solid var(--slate-200);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:var(--slate-400);flex-shrink:0;margin-top:8px}
.mt-post-rank--1{background:linear-gradient(135deg,#ffd700,#F59E0B);color:#7c5900;border-color:#ffd700}
.mt-post-rank--2{background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0}
.mt-post-rank--3{background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32}
.mt-post-av{width:36px;height:36px;border-radius:50%;flex-shrink:0;color:#fff;font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--slate-200);overflow:hidden}
.mt-post-av img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.mt-post-body{flex:1;min-width:0}
.mt-post-author{font-size:12.5px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mt-post-handle{font-size:10px;color:var(--slate-400);font-weight:500}
.mt-post-date{font-size:10px;color:var(--slate-400);margin-top:1px;margin-bottom:4px}
.mt-post-text{font-size:11.5px;color:var(--slate-500);line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:6px;word-break:break-word}
.mt-post-stats{display:flex;align-items:center;gap:4px;flex-wrap:wrap}
.mt-metric{display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:3px;font-size:10px;font-weight:700;background:var(--slate-100);color:var(--slate-500);white-space:nowrap}
.mt-sent{display:inline-flex;align-items:center;padding:2px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.3px}
.mt-sent--pos{background:#d1fae5;color:#065f46}
.mt-sent--neg{background:#fee2e2;color:#991b1b}
.mt-sent--neu{background:var(--slate-100);color:var(--slate-500)}
.mt-plat-badge{display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:3px;font-size:9px;font-weight:800;white-space:nowrap}
.mt-view-link{display:inline-flex;align-items:center;gap:3px;font-size:9.5px;font-weight:700;color:var(--primary);text-decoration:none;padding:2px 6px;border-radius:3px;background:var(--primary-lt);border:1px solid rgba(3,128,71,.2);transition:background .12s,color .12s;margin-left:auto}
.mt-view-link:hover{background:var(--primary);color:#fff}
/* Pagination */
.mt-pagination{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--slate-100);flex-wrap:wrap;gap:8px}
.mt-pag-info{font-size:11px;color:var(--slate-400);font-weight:500}
.mt-pag-controls{display:flex;align-items:center;gap:3px}
.mt-pag-btn{min-width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;padding:0 6px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .12s;user-select:none}
.mt-pag-btn:hover:not(:disabled):not(.is-active){border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}
.mt-pag-btn.is-active{background:var(--primary);border-color:var(--primary);color:#fff}
.mt-pag-btn:disabled{opacity:.35;cursor:not-allowed}
/* Chart */
.chart-container{height:280px;position:relative}
.chart-loading{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;background:#fff;z-index:2;transition:opacity .3s}
.chart-loading.hidden{opacity:0;pointer-events:none}
.chart-empty{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600}
.chart-empty i{font-size:34px;color:var(--slate-300);display:block}
/* Slide Panel */
.do-panel-overlay{position:fixed;inset:0;z-index:9000;background:rgba(15,23,42,.45);backdrop-filter:blur(4px);display:none}
.do-panel-overlay.show{display:block;animation:overlayIn .22s ease-out}
.do-panel-overlay.hiding{animation:overlayOut .22s ease-out forwards}
.do-panel{position:fixed;top:0;right:0;bottom:0;z-index:9001;width:480px;max-width:100vw;background:#fff;display:none;flex-direction:column;border-left:1px solid var(--slate-200);box-shadow:-8px 0 40px rgba(15,23,42,.16)}
.do-panel.show{display:flex;animation:slideInRight .28s cubic-bezier(.4,0,.2,1)}
.do-panel.hiding{animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards}
.do-panel-header{display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid var(--slate-200);background:var(--slate-50);flex-shrink:0}
.do-panel-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}
.do-panel-title{font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.do-panel-close{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);font-size:16px;transition:all .14s;flex-shrink:0}
.do-panel-close:hover{background:var(--red);border-color:var(--red);color:#fff}
.do-panel-actions{display:flex;align-items:center;gap:7px;padding:7px 12px;border-bottom:1px solid var(--slate-200);background:#fff;flex-shrink:0}
.do-panel-meta{flex:1;font-size:10px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:5px}
.do-panel-tabs{display:flex;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px;gap:2px}
.do-panel-tab{padding:3px 9px;border-radius:3px;border:none;background:transparent;font-size:11px;font-weight:700;cursor:pointer;transition:all .13s;color:var(--slate-500);font-family:inherit}
.do-panel-tab:hover{background:#fff}
.do-panel-tab.active{background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.do-panel-tab.active[data-s="all"]{color:var(--primary)}
.do-panel-tab.neg.active{color:#EF4444}
.do-panel-tab.pos.active{color:#10B981}
.do-panel-tab.neu.active{color:var(--slate-500)}
.do-panel-list{overflow-y:auto;flex:1;padding:2px 0;min-height:0}
.do-panel-list::-webkit-scrollbar{width:4px}
.do-panel-list::-webkit-scrollbar-thumb{background:var(--slate-200);border-radius:99px}
.do-panel-item{display:flex;gap:10px;padding:10px 14px;border-bottom:1px solid var(--slate-50);cursor:pointer;transition:background .1s;align-items:flex-start}
.do-panel-item:hover{background:#f0f9ff}
.do-panel-item:last-child{border-bottom:none}
.do-panel-avatar{width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#fff;border:1.5px solid var(--slate-200);overflow:hidden}
.do-panel-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.do-panel-item-body{flex:1;min-width:0}
.do-panel-author{font-size:12px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.do-panel-handle{font-size:10px;color:var(--slate-400);font-weight:500;margin-bottom:2px}
.do-panel-text{font-size:11px;color:var(--slate-600);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px}
.do-panel-footer{display:flex;align-items:center;gap:5px;font-size:10px;color:var(--slate-400);flex-wrap:wrap}
.do-sent-badge{padding:1px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase}
.do-sent-badge--pos{background:#dbeafe;color:#1d4ed8}
.do-sent-badge--neg{background:#fee2e2;color:#991b1b}
.do-sent-badge--neu{background:var(--slate-100);color:var(--slate-500)}
.do-panel-loading{display:flex;flex-direction:column;align-items:center;justify-content:height:100%;gap:12px;color:var(--slate-400);font-size:13px;font-weight:600}
.do-panel-spinner{width:28px;height:28px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
/* Detail Sub-panel */
.do-detail-panel{position:absolute;inset:0;background:#fff;z-index:5;display:none;flex-direction:column;animation:slideInRight .2s cubic-bezier(.4,0,.2,1)}
.do-detail-panel.show{display:flex}
.do-dp2-header{display:flex;align-items:center;gap:8px;padding:12px 14px;background:var(--slate-50);border-bottom:1px solid var(--slate-200);flex-shrink:0}
.do-dp2-back{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);transition:all .13s;font-size:14px}
.do-dp2-back:hover{background:var(--primary-lt);color:var(--primary);border-color:var(--primary)}
.do-dp2-title{font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.do-dp2-body{overflow-y:auto;flex:1;padding:16px}
.do-dp2-body::-webkit-scrollbar{width:4px}
.do-dp2-body::-webkit-scrollbar-thumb{background:var(--slate-200);border-radius:99px}
.do-dp2-avatar-row{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.do-dp2-avatar-lg{width:46px;height:46px;border-radius:50%;color:#fff;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;border:2px solid var(--slate-200);overflow:hidden;flex-shrink:0}
.do-dp2-avatar-lg img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.do-dp2-name{font-size:14px;font-weight:700;color:var(--slate-900)}
.do-dp2-handle{font-size:11px;color:var(--slate-400);font-weight:500}
.do-dp2-plat-badge{display:inline-block;padding:2px 8px;border-radius:3px;font-size:10px;font-weight:700;margin-top:3px}
.do-dp2-meta{font-size:11px;color:var(--slate-400);font-weight:500;margin-bottom:10px}
.do-dp2-sent{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:3px;font-size:11px;font-weight:700;margin-bottom:10px}
.do-dp2-sent--pos{background:#dbeafe;color:#1d4ed8}
.do-dp2-sent--neg{background:#fee2e2;color:#991b1b}
.do-dp2-sent--neu{background:var(--slate-100);color:var(--slate-500)}
.do-dp2-content{font-size:12px;color:var(--slate-700);line-height:1.7;margin-bottom:12px;background:var(--slate-50);border-radius:var(--radius-sm);padding:10px 12px;border:1px solid var(--slate-200);word-break:break-word}
.do-dp2-media{border-radius:var(--radius-sm);overflow:hidden;margin-bottom:10px}
.do-dp2-media img{width:100%;max-height:220px;object-fit:cover;display:block}
.do-dp2-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:10px}
.do-dp2-stat{background:var(--slate-50);border-radius:var(--radius-sm);padding:8px 10px;border:1px solid var(--slate-200);text-align:center}
.do-dp2-stat-val{font-size:14px;font-weight:700;color:var(--slate-900)}
.do-dp2-stat-lbl{font-size:9px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.4px;margin-top:1px}
.do-dp2-link{display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:var(--primary);color:#fff;border-radius:var(--radius-sm);font-size:12px;font-weight:700;text-decoration:none;transition:filter .14s;margin-top:4px}
.do-dp2-link:hover{filter:brightness(1.1);color:#fff}
/* Export */
.page-export-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);padding:9px 14px;margin-bottom:20px;box-shadow:var(--shadow-sm)}
.page-export-bar-left{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--slate-600)}
.page-export-bar-left i{font-size:15px;color:var(--primary)}
.page-export-bar-right{display:flex;gap:8px}
.page-export-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);font-size:16px;cursor:pointer;transition:all .15s ease;border:1.5px solid transparent;font-family:inherit;background:transparent;padding:0;line-height:1}
.page-export-btn-pdf{background:#fff3f3;color:#dc2626;border-color:#fca5a5}
.page-export-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.page-export-btn-img{background:var(--primary-lt);color:var(--primary);border-color:rgba(3,128,71,.3)}
.page-export-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.page-export-btn-csv{background:#f0fdf4;color:#15803d;border-color:#86efac}
.page-export-btn-csv:hover{background:#15803d;color:#fff;border-color:#15803d}
.page-export-btn:disabled{opacity:.55;cursor:not-allowed;pointer-events:none}
.page-export-btn .exp-spinner{width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
.page-export-btn.exporting .exp-spinner{display:inline-block}
.page-export-btn.exporting .exp-icon{display:none}
.card-exp-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:var(--radius-sm);font-size:14px;cursor:pointer;flex-shrink:0;transition:all .14s ease;border:1px solid transparent;font-family:inherit;background:transparent;padding:0;line-height:1}
.card-exp-btn-pdf{color:#dc2626;border-color:#fca5a5;background:#fff3f3}
.card-exp-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.card-exp-btn-img{color:var(--primary);border-color:rgba(3,128,71,.3);background:var(--primary-lt)}
.card-exp-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.card-exp-btn:disabled{opacity:.45;cursor:not-allowed;pointer-events:none}
.card-exp-btn .exp-spinner{width:11px;height:11px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
.card-exp-btn.exporting .exp-spinner{display:inline-block}
.card-exp-btn.exporting .exp-icon{display:none}
.export-toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--slate-900);color:#fff;border-radius:var(--radius);padding:10px 18px;font-size:12px;font-weight:600;box-shadow:var(--shadow-lg);z-index:99999;opacity:0;pointer-events:none;transition:opacity .22s ease,transform .22s ease;display:flex;align-items:center;gap:8px;white-space:nowrap}
.export-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.export-toast.success{background:#065f46}
.export-toast.error{background:#991b1b}
@media(max-width:640px){.do-panel{width:100vw}.mt-tabs{flex-wrap:wrap}.mt-tab-btn{flex:unset;min-width:calc(50% - 4px)}}
</style>
@endsection
@section('page-title', 'Mentions Timeline')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate   = $endDate   ?? request()->get('end_date', now()->format('Y-m-d'));
    $projects  = $projects  ?? [];
@endphp
<script>
    const MT_PID = {{ $projectId ? (int)$projectId : 'null' }};
    const MT_SD  = '{{ $startDate }}';
    const MT_ED  = '{{ $endDate }}';
</script>

@include('mk.layouts.partials.filter-datepicker')

<div id="pageExportArea">

{{-- KPI Cards with dot number badge --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#4680ff;animation:fadeUp .38s ease-out both;">
            <div class="card-body">
                <span class="kpi-dot-num"><span class="kpi-pulse-dot"></span><span id="dotTotalVal">–</span></span>
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiTotal">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTotalSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-newspaper"></i></div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#10B981;animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body">
                <span class="kpi-dot-num"><span class="kpi-pulse-dot"></span><span id="dotPosVal">–</span></span>
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiPos">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPosSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#94A3B8;animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body">
                <span class="kpi-dot-num"><span class="kpi-pulse-dot"></span><span id="dotNeuVal">–</span></span>
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Neutral</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiNeu">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNeuSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-minus-circle"></i></div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#EF4444;animation:fadeUp .38s ease-out .15s both;">
            <div class="card-body">
                <span class="kpi-dot-num"><span class="kpi-pulse-dot"></span><span id="dotNegVal">–</span></span>
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiNeg">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNegSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Page Export Toolbar --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
        <i class="ph ph-export"></i><span>Export Halaman</span>
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Charts + Mentions List</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn" onclick="MTExport.run('pdf',this)" title="Export PDF"><i class="ph ph-file-pdf exp-icon"></i><span class="exp-spinner"></span></button>
        <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn" onclick="MTExport.run('image',this)" title="Export PNG"><i class="ph ph-image exp-icon"></i><span class="exp-spinner"></span></button>
        <button type="button" class="page-export-btn page-export-btn-csv" id="pageExportCsvBtn" onclick="MTExport.run('csv',this)" title="Export CSV"><i class="ph ph-file-csv exp-icon"></i><span class="exp-spinner"></span></button>
    </div>
</div>

{{-- Charts Row --}}
<div class="row">
    <div class="col-lg-7 col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-export-trend">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-line f-18 text-primary"></i></div>
                        <div><h6 class="mb-0">Mentions Trend</h6><small class="text-muted">Daily volume per platform</small></div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-primary text-primary" id="trendBadge">Loading...</span>
                        <div data-html2canvas-ignore="true" class="d-flex gap-1">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="MTExport.runCard('card-export-trend','trend','pdf',this)"><i class="ph ph-file-pdf exp-icon"></i><span class="exp-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="MTExport.runCard('card-export-trend','trend','image',this)"><i class="ph ph-image exp-icon"></i><span class="exp-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height:340px;">
                        <div class="chart-loading" id="trendLoading"><div class="spin-ring"></div><span>Loading chart...</span></div>
                        <div id="trendChart" style="width:100%;height:340px;display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
            <div id="card-export-dist">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-success rounded"><i class="ph ph-chart-bar f-18 text-success"></i></div>
                        <div><h6 class="mb-0">Platform Distribution</h6><small class="text-muted">Total mentions per platform</small></div>
                    </div>
                    <div data-html2canvas-ignore="true" class="d-flex gap-1">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="MTExport.runCard('card-export-dist','distribution','pdf',this)"><i class="ph ph-file-pdf exp-icon"></i><span class="exp-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="MTExport.runCard('card-export-dist','distribution','image',this)"><i class="ph ph-image exp-icon"></i><span class="exp-spinner"></span></button>
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
</div>

{{-- Platform Tabs --}}
<div class="mt-tabs">
    <button class="mt-tab-btn active" id="tab-all"    onclick="MTTab.show('all')"><i class="ph ph-globe"></i> All</button>
    <button class="mt-tab-btn"        id="tab-doc"    onclick="MTTab.show('doc')"><i class="ph ph-newspaper"></i> News</button>
    <button class="mt-tab-btn"        id="tab-twit"   onclick="MTTab.show('twit')"><i class="ph ph-x-logo"></i> Twitter</button>
    <button class="mt-tab-btn"        id="tab-fb"     onclick="MTTab.show('fb')"><i class="ph ph-facebook-logo"></i> Facebook</button>
    <button class="mt-tab-btn"        id="tab-ig"     onclick="MTTab.show('ig')"><i class="ph ph-instagram-logo"></i> Instagram</button>
    <button class="mt-tab-btn"        id="tab-ytb"    onclick="MTTab.show('ytb')"><i class="ph ph-youtube-logo"></i> YouTube</button>
    <button class="mt-tab-btn"        id="tab-tiktok" onclick="MTTab.show('tiktok')"><i class="ph ph-tiktok-logo"></i> TikTok</button>
</div>

{{-- Mentions List --}}
<div class="card mb-3" style="animation:fadeUp .38s ease-out .26s both;">
    <div id="card-export-list">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-list-dashes f-18 text-primary"></i></div>
                <div><h6 class="mb-0">Mentions List</h6><small class="text-muted">Klik mention untuk lihat detail</small></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-primary btn-sm" onclick="Object.keys(_mtCache).forEach(k=>{delete _mtCache[k];}); MTData.reload();"><i class="ph ph-arrows-clockwise me-1"></i>Refresh</button>
                <span class="badge bg-light-primary text-primary" id="listBadge">Loading...</span>
                <div data-html2canvas-ignore="true" class="d-flex gap-1">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="MTExport.runCard('card-export-list','list','pdf',this)"><i class="ph ph-file-pdf exp-icon"></i><span class="exp-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="MTExport.runCard('card-export-list','list','image',this)"><i class="ph ph-image exp-icon"></i><span class="exp-spinner"></span></button>
                    <button class="card-exp-btn" style="color:#15803d;border-color:#86efac;background:#f0fdf4;" onclick="MTExport.runCsvCard(this)"><i class="ph ph-file-csv exp-icon"></i><span class="exp-spinner"></span></button>
                </div>
            </div>
        </div>
        <div id="listContainer" class="p-0"><div class="spinner-state"><div class="spin-ring"></div>Memuat data...</div></div>
        <div id="pagContainer"></div>
    </div>
</div>

</div>{{-- /pageExportArea --}}

{{-- Slide Panel (Dashboard-style with sentiment tabs) --}}
<div class="do-panel-overlay" id="mtPanelOverlay" onclick="MTPanelNew.close()"></div>
<div class="do-panel" id="mtPanelNew">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="mtPanelDot" style="background:var(--primary);"></div>
        <span class="do-panel-title" id="mtPanelTitle">Mentions</span>
        <button class="do-panel-close" onclick="MTPanelNew.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span id="mtPanelMeta">–</span></div>
        <div class="do-panel-tabs">
            <button class="do-panel-tab active" data-s="all" onclick="MTPanelNew.filterSent('all')">Semua</button>
            <button class="do-panel-tab neg"    data-s="neg" onclick="MTPanelNew.filterSent('neg')">Neg</button>
            <button class="do-panel-tab pos"    data-s="pos" onclick="MTPanelNew.filterSent('pos')">Pos</button>
            <button class="do-panel-tab neu"    data-s="neu" onclick="MTPanelNew.filterSent('neu')">Neu</button>
        </div>
    </div>
    <div class="do-panel-list" id="mtPanelList"></div>
    {{-- Detail sub-panel --}}
    <div class="do-detail-panel" id="mtDetailPanelNew">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="MTDetailNew.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="mtDetailTitleNew">Detail</span>
            <button class="do-panel-close" onclick="MTPanelNew.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="mtDetailBodyNew"></div>
    </div>
</div>

{{-- Export Toast --}}
<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

@endsection
@section('scripts')
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
'use strict';

/* ── Config & Globals ── */
const MTCfg = { pid: MT_PID, sd: MT_SD, ed: MT_ED, perPage: 20 };
const PLAT = {
    doc:    { label:'Online News', color:'#3b82f6' },
    twit:   { label:'Twitter',     color:'#0ea5e9' },
    fb:     { label:'Facebook',    color:'#6366f1' },
    ig:     { label:'Instagram',   color:'#ec4899' },
    ytb:    { label:'YouTube',     color:'#ef4444' },
    tiktok: { label:'TikTok',      color:'#6b7280' },
};
const PLAT_KEYS = Object.keys(PLAT);
const _$   = id => document.getElementById(id);
const numF = n  => parseInt(n||0).toLocaleString('id-ID');
const numK = n  => parseInt(n||0).toLocaleString('id-ID');
const esc  = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
const Store = { all:[], doc:[], twit:[], fb:[], ig:[], ytb:[], tiktok:[] };
let _activeTab='all', _page=1, _trendChart=null, _barChart=null, _trendRaw=[];

/* ── Normalise sentiment ── */
function _normSent(item) {
    const MAP={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'};
    const code=String(item.class_sentiment_code||'').toLowerCase().trim();
    if(code==='pos'||code==='positive'||code==='positif') return 'pos';
    if(code==='neg'||code==='negative'||code==='negatif') return 'neg';
    if(code) return 'neu';
    return MAP[String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim()]||'neu';
}

/* ── Normalise item ── */
function _normItem(item, platform) {
    let cj={};
    try {
        if(item.content_json) {
            cj = (typeof item.content_json === 'string') ? JSON.parse(item.content_json) : item.content_json;
        }
    } catch(e){cj={};}
    const ao=item.author_object ? (typeof item.author_object==='string'?JSON.parse(item.author_object):item.author_object) : (cj.user||{});
    const name=(ao.name||item.author_name||item.name||item.from_name||item.page_name||item.channel_title||item.author_nickname||item.publisher||item.source_name||'').replace(/<[^>]*>/g,'').trim()||'Unknown';
    const handle=(item.author_scr_name||item.screen_name||ao.scr_name||item.username||'').trim();
    const av=(item.avatar_url||item.profile_image_url||item.author_image||ao.image||(cj.user&&cj.user.image?cj.user.image:'')||item.profile_image||'').trim();
    const content=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
    let url=item.url||item.link||'';
    if(!url&&platform==='twit'){const scr=(handle||ao.scr_name||'').replace(/^@/,'');let tid=item.sub_id||'';if(!tid&&item.docid){const m=String(item.docid).match(/^tw-(.+)$/);if(m)tid=m[1];}if(!tid&&cj.rt_status&&cj.rt_status.id)tid=String(cj.rt_status.id);url=(scr&&tid)?'https://twitter.com/'+scr+'/status/'+tid:(scr?'https://twitter.com/'+scr:'');}
    return {_platform:platform,_raw:item,name,handle,avatar:av,content,url,sentiment:_normSent(item),date:item.date_created||item.created_at||'',
        likes:parseInt(item.num_likes||item.likes||item.favorite_count||item.like_count||0),
        comments:parseInt(item.num_comments||item.comments||item.comment_count||item.reply_count||0),
        shares:parseInt(item.num_shares||item.shares||item.share_count||0),
        views:parseInt(item.view_cnt||item.num_views||item.views||item.play_count||item.freq||0),
        retweets:parseInt(item.rt||item.num_retweeted||item.retweet_count||0)};
}

/* ── Tab switcher ── */
const MTTab={show(type){_activeTab=type;_page=1;PLAT_KEYS.concat(['all']).forEach(t=>{const el=_$('tab-'+t);if(el)el.classList.toggle('active',t===type);});MTData._renderList();}};

/* ── Fetch cache ── */
const _mtCache={};
async function _mtFetchOne(platform,pid,sd,ed){
    const cleanSd = String(sd).trim().replace(/\s+/g,'-'), cleanEd = String(ed).trim().replace(/\s+/g,'-');
    const cKey=pid+'_'+platform+'_'+cleanSd+'_'+cleanEd;
    if(_mtCache[cKey]) return _mtCache[cKey];
    const rws = 500;
    const q='project_id='+pid+'&start_date='+cleanSd+'&end_date='+cleanEd+'&rows='+rws+'&start=0';
    if(platform==='ig'){for(const sub of['postbylike','postbycomment','postbydate','']){try{const r=await fetch('/mk/api/news/ig-top-status?'+q+(sub?'&sub='+sub:''));const d=await r.json();const items=Array.isArray(d&&d.data)?d.data:(Array.isArray(d)?d:[]);if(items.length>0){_mtCache[cKey]=items.map(i=>{i._platform=platform;return i;});return _mtCache[cKey];}}catch(e){continue;}}return[];}
    const eps={doc:'/mk/api/news/mentions?'+q,twit:'/mk/api/news/mentions?'+q+'&media_type=twit',fb:'/mk/api/news/fb-top-status?'+q+'&sub=fblike',ytb:'/mk/api/news/ytb-top-status?'+q,tiktok:'/mk/api/news/tiktok-top-status?'+q+'&sub=postbylike'};
    const url=eps[platform];if(!url)return[];
    const ctrl=new AbortController(),tid=setTimeout(()=>ctrl.abort(),30000);
    try{const r=await fetch(url,{signal:ctrl.signal});clearTimeout(tid);if(!r.ok)return[];const d=await r.json();
    let items=[];if(Array.isArray(d?.data?.data))items=d.data.data;else if(Array.isArray(d?.data))items=d.data;else if(Array.isArray(d?.statuses))items=d.statuses;else if(Array.isArray(d?.results))items=d.results;else if(Array.isArray(d?.posts))items=d.posts;else if(Array.isArray(d))items=d;
    items=items.map(i=>{i._platform=platform;return i;});
    _mtCache[cKey]=items;return items;}catch(e){clearTimeout(tid);return[];}
}

/* ════ DATA MODULE ════ */
const MTData={
    async loadAll(){
        if(!MTCfg.pid){_$('listContainer').innerHTML='<div class="chart-empty" style="padding:40px"><i class="ph ph-folder-open"></i><span>Pilih project terlebih dahulu</span></div>';return;}
        
        const ctn = _$('listContainer'), ld = _$('listLoading');
        if(ctn) ctn.innerHTML = '<div style="padding:100px; text-align:center;"><div class="loading-spinner mb-2"></div><div class="text-slate-400 text-sm">Menyiapkan data timeline...</div></div>';
        if(ld) ld.classList.remove('hidden');

        // 1. Fetch Trend Data for Charts & KPIs
        try {
            const cleanSd = String(MTCfg.sd).trim().replace(/\s+/g,'-'), cleanEd = String(MTCfg.ed).trim().replace(/\s+/g,'-');
            const tRes = await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${MTCfg.pid}&start_date=${cleanSd}&end_date=${cleanEd}`);
            const tJson = await tRes.json();
            _trendRaw = tJson.data || [];
            this._updateKPIsFromTrend(_trendRaw);
            this._renderTrend();
            this._renderBar();
        } catch(e) { console.warn("Trend fetch failed", e); }

        // 2. Fetch Mentions for List (Original Logic)
        const results=await Promise.allSettled(PLAT_KEYS.map(k=>_mtFetchOne(k,MTCfg.pid,MTCfg.sd,MTCfg.ed)));
        PLAT_KEYS.forEach((k,i)=>{
            const raw=results[i].status==='fulfilled'?results[i].value:[];
            Store[k]=raw.map(it=>_normItem(it,k));
        });
        Store.all=PLAT_KEYS.reduce((acc,k)=>acc.concat(Store[k]),[]).sort((a,b)=>(b.date||'').localeCompare(a.date||''));
        
        this._renderList();
        if(_$('listBadge')) _$('listBadge').textContent='';
    },
    _updateKPIsFromTrend(raw){
        let total = 0;
        const platTotals = {};
        raw.forEach(p => {
            let key = p.key;
            if(key==='twitter') key='twit';
            if(key==='facebook') key='fb';
            if(key==='instagram') key='ig';
            if(key==='youtube') key='ytb';
            
            let pSum = 0;
            (p.data || []).forEach(d => pSum += (d.count || 0));
            platTotals[key] = pSum;
            total += pSum;
        });
        
        _$('kpiTotal').textContent = numF(total);
        _$('dotTotalVal').textContent = numK(total);
        
        Object.keys(platTotals).forEach(k => this._updateChip(k, platTotals[k]));
        this._updateChip('all', total);
    },
    reload(){this.loadAll();},
    _updateChip(key,count){},
    _updateKPIs(){
        const all=Store.all,pos=all.filter(m=>m.sentiment==='pos').length,neg=all.filter(m=>m.sentiment==='neg').length,neu=all.length-pos-neg;
        const pct=v=>all.length>0?((v/all.length)*100).toFixed(1):'0.0';
        const platCount=PLAT_KEYS.map(k=>Store[k].length).filter(v=>v>0).length;
        _$('kpiTotal').textContent=numF(all.length);_$('kpiTotalSub').innerHTML='<i class="ph ph-chart-line-up me-1"></i>'+platCount+' platforms';
        _$('kpiPos').textContent=numF(pos);_$('kpiPosSub').innerHTML='<i class="ph ph-chart-line-up me-1"></i>'+pct(pos)+'% of total';
        _$('kpiNeu').textContent=numF(neu);_$('kpiNeuSub').innerHTML='<i class="ph ph-chart-line-up me-1"></i>'+pct(neu)+'% of total';
        _$('kpiNeg').textContent=numF(neg);_$('kpiNegSub').innerHTML='<i class="ph ph-chart-line-up me-1"></i>'+pct(neg)+'% of total';
        /* dot badges */
        _$('dotTotalVal').textContent=numK(all.length);_$('dotPosVal').textContent=numK(pos);_$('dotNeuVal').textContent=numK(neu);_$('dotNegVal').textContent=numK(neg);
    },
    _renderTrend(){
        const el=_$('trendChart'),ld=_$('trendLoading');if(!el)return;
        const parseDate = s => {
            if(!s) return null;
            const clean = String(s).trim().split('T')[0].split(' ')[0].replace(/\//g,'-');
            if(/^\d{4}-\d{2}-\d{2}$/.test(clean)) return clean;
            try {
                const d = new Date(s.replace(/-/g,'/'));
                if(!isNaN(d.getTime())) return d.toISOString().split('T')[0];
            } catch(e) {}
            return null;
        };
        const startISO = parseDate(MTCfg.sd), endISO = parseDate(MTCfg.ed);
        if(!startISO || !endISO){ if(ld)ld.classList.add('hidden'); return; }
        
        const dates = [];
        let curr = new Date(startISO + 'T00:00:00');
        const stop = new Date(endISO + 'T00:00:00');
        while(curr <= stop) {
            const y = curr.getFullYear(), m = String(curr.getMonth()+1).padStart(2,'0'), d = String(curr.getDate()).padStart(2,'0');
            dates.push(`${y}-${m}-${d}`);
            curr.setDate(curr.getDate() + 1);
        }

        const hasTrendData = (_trendRaw && _trendRaw.length > 0);
        if(!dates.length || (!hasTrendData && !Store.all.length)){ 
            if(ld)ld.classList.add('hidden'); 
            return; 
        }

        const xLabels=dates.map(ds=>{
            const [y,m,d] = ds.split('-');
            return parseInt(d)+'/'+parseInt(m);
        });
        const datasets={}; PLAT_KEYS.forEach(k=>{datasets[k]=new Array(dates.length).fill(0);});
        
        if(hasTrendData) {
            _trendRaw.forEach(p => {
                let key = p.key;
                if(key==='twitter') key='twit';
                if(key==='facebook') key='fb';
                if(key==='instagram') key='ig';
                if(key==='youtube') key='ytb';
                
                if(datasets[key]) {
                    (p.data || []).forEach(pt => {
                        const idx = dates.indexOf(pt.date);
                        if(idx >= 0) datasets[key][idx] = pt.count || 0;
                    });
                }
            });
        } else {
            Store.all.forEach(m => {
                const day = parseDate(m.date);
                const idx = dates.indexOf(day);
                if(idx >= 0 && datasets[m._platform]) datasets[m._platform][idx]++;
            });
        }

        if(_trendChart){try{_trendChart.destroy();}catch(e){}_trendChart=null;}
        el.style.display='block';if(ld)ld.classList.add('hidden');

        const seriesArr=PLAT_KEYS.map(k=>({name:PLAT[k].label,data:datasets[k]})).filter(s=>s.data.some(v=>v>0));
        const colorsArr=seriesArr.map(s=>{const k=PLAT_KEYS.find(k=>PLAT[k].label===s.name);return k?PLAT[k].color:'#94a3b8';});
        _trendChart=new ApexCharts(el,{
            chart:{type:'area',height:340,fontFamily:'inherit',background:'transparent',toolbar:{show:false},
                events:{click:(e,ctx,cfg)=>{
                    if(e&&e.target&&e.target.closest&&e.target.closest('.apexcharts-legend')) return;
                    if(cfg&&cfg.seriesIndex>=0&&seriesArr[cfg.seriesIndex]){const s=seriesArr[cfg.seriesIndex];const pk=PLAT_KEYS.find(k=>PLAT[k].label===s.name);if(pk&&Store[pk].length)MTPanelNew.open(Store[pk],pk);}else if(Store.all.length)MTPanelNew.open(Store.all,'all');}}},
            series:seriesArr,colors:colorsArr,
            xaxis:{categories:xLabels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontFamily:'inherit',fontSize:'11px',fontWeight:600,colors:'#94A3B8'}}},
            yaxis:{labels:{formatter:v=>numK(v),style:{fontFamily:'inherit',fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false},axisTicks:{show:false}},
            fill:{opacity:0.3},stroke:{curve:'smooth',width:2.5},
            markers:{size:dates.length<=32?3:0,strokeWidth:1,strokeColors:'#fff',hover:{size:5}},
            dataLabels:{enabled:true,formatter:v=>v>10?numF(v):'',style:{fontSize:'9px',fontFamily:'inherit',fontWeight:'700'},background:{enabled:true,borderRadius:3,borderWidth:0,padding:3,opacity:0.9},offsetY:-5},
            grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}}},
            legend:{position:'bottom',horizontalAlign:'left',fontFamily:'inherit',fontSize:'11px',fontWeight:'600',labels:{colors:'#94A3B8'},markers:{width:9,height:9,radius:50},itemMargin:{horizontal:14,vertical:4},onItemClick:{toggleDataSeries:true}},
            tooltip:{shared:false,intersect:true,style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>numF(v)+' mentions'}},
        });
        _trendChart.render();
        const fmtB=ds=>{
            const [y,m,d] = ds.split('-');
            const dt = new Date(y, m-1, d);
            return parseInt(d)+' '+dt.toLocaleString('id-ID',{month:'short'});
        };
        _$('trendBadge').textContent=fmtB(dates[0])+' - '+fmtB(dates[dates.length-1]);
    },
    _renderBar(){
        const el=_$('barChart'),ld=_$('barLoading');if(!el)return;
        
        let platData = [];
        if(_trendRaw && _trendRaw.length > 0) {
            platData = _trendRaw.map(p => {
                let key = p.key;
                if(key==='twitter') key='twit';
                if(key==='facebook') key='fb';
                if(key==='instagram') key='ig';
                if(key==='youtube') key='ytb';
                
                let sum = 0; (p.data || []).forEach(d => sum += (d.count || 0));
                return { name: PLAT[key]?.label || p.label, value: sum, color: PLAT[key]?.color || p.color, key: key };
            }).sort((a,b)=>b.value-a.value);
        } else {
            platData = PLAT_KEYS.map(k=>({name:PLAT[k].label,value:Store[k].length,color:PLAT[k].color,key:k})).sort((a,b)=>b.value-a.value);
        }

        const total=platData.reduce((s,d)=>s+d.value,0);if(!total){if(ld)ld.classList.add('hidden');return;}
        if(_barChart){try{_barChart.dispose();}catch(e){}}
        el.style.display='block';_barChart=echarts.init(el,null,{renderer:'canvas'});
        window.addEventListener('resize',()=>{try{_barChart.resize();}catch(e){}});
        _barChart.setOption({animation:true,animationDuration:500,animationEasing:'cubicOut',backgroundColor:'transparent',
            tooltip:{trigger:'item',backgroundColor:'rgba(15,23,42,.92)',borderColor:'transparent',borderRadius:8,padding:[8,12],textStyle:{color:'#e2e8f0',fontFamily:'inherit',fontSize:12},formatter:p=>'<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:'+p.color+';margin-right:6px;"></span>'+esc(p.name)+' <b style="margin-left:8px;">'+numF(p.value)+'</b> <span style="color:#94a3b8;font-size:10px;">('+((p.value/total)*100).toFixed(1)+'%)</span>'},
            grid:{top:10,right:30,bottom:28,left:80},
            xAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontSize:10,color:'#94a3b8',formatter:numF}},
            yAxis:{type:'category',data:platData.map(d=>d.name).reverse(),axisLine:{show:false},axisTick:{show:false},axisLabel:{fontSize:11,fontWeight:600,color:'#64748b'}},
            series:[{type:'bar',data:platData.map(d=>({value:d.value,itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:d.color},{offset:1,color:d.color+'99'}]}}})).reverse(),barWidth:18,borderRadius:[0,6,6,0],label:{show:true,position:'right',formatter:p=>numF(p.value),fontSize:10,fontWeight:700,color:'#fff',backgroundColor:'inherit',padding:[3,6],borderRadius:4,distance:6}}],
            graphic:[]
        });
        _barChart.on('click',p=>{const pk=PLAT_KEYS.find(k=>PLAT[k].label===p.name);if(pk&&Store[pk].length)MTPanelNew.open(Store[pk],pk);});
        if(ld)ld.classList.add('hidden');requestAnimationFrame(()=>{_barChart.resize();});
    },
    _getItems(){return _activeTab==='all'?Store.all:(Store[_activeTab]||[]);},
    _renderList(){
        const items=this._getItems(),listEl=_$('listContainer'),pagEl=_$('pagContainer');
        if(!items.length){listEl.innerHTML='<div class="chart-empty" style="padding:40px"><i class="ph ph-folder-open"></i><span>Tidak ada data untuk periode ini</span></div>';if(pagEl)pagEl.innerHTML='';return;}
        const pp=MTCfg.perPage,total=items.length,pages=Math.ceil(total/pp);
        if(_page>pages)_page=pages;const start=(_page-1)*pp;
        let html='<div class="mt-post-list">';
        for(let i=start;i<Math.min(start+pp,total);i++)html+=this._postHtml(items[i],i);
        html+='</div>';listEl.innerHTML=html;
        if(pagEl)pagEl.innerHTML=this._pagHtml(_page,pages,total,start+1,Math.min(start+pp,total));
        listEl.querySelectorAll('.mt-post').forEach(el=>{
            el.addEventListener('click',()=>{
                try{const item=JSON.parse(decodeURIComponent(el.dataset.item));MTPanelNew.open(items,item._platform);MTDetailNew.open(item);}catch(e){console.warn(e);}
            });
        });
    },
    _postHtml(item,gi){
        const rank=gi+1,rkCls=rank<=3?'--'+rank:'',plat=PLAT[item._platform]||PLAT.doc;
        const dummy='/assets/images/user/dummy.jpg';
        const avHtml=(item.avatar&&item.avatar.indexOf('http')===0)?'<img src="'+esc(item.avatar)+'" onerror="this.src=\''+dummy+'\'">':'<img src="'+dummy+'">';
        const dt=(item.date||'').split('T')[0],sentLbl={pos:'Positive',neg:'Negative',neu:'Neutral'}[item.sentiment];
        const enc=encodeURIComponent(JSON.stringify(item)),parts=[];
        if(item.views>0)parts.push('<span class="mt-metric"><i class="ph ph-eye me-1"></i>'+numF(item.views)+'</span>');
        if(item.likes>0)parts.push('<span class="mt-metric"><i class="ph ph-thumbs-up me-1"></i>'+numF(item.likes)+'</span>');
        if(item.retweets>0)parts.push('<span class="mt-metric"><i class="ph ph-repeat me-1"></i>'+numF(item.retweets)+'</span>');
        if(item.comments>0)parts.push('<span class="mt-metric"><i class="ph ph-chat-circle me-1"></i>'+numF(item.comments)+'</span>');
        if(item.shares>0)parts.push('<span class="mt-metric"><i class="ph ph-share-network me-1"></i>'+numF(item.shares)+'</span>');
        return '<div class="mt-post" data-item="'+esc(enc)+'">'
            +'<div class="mt-post-rank mt-post-rank'+rkCls+'">'+rank+'</div>'
            +'<div class="mt-post-av" style="background:linear-gradient(135deg,'+plat.color+','+plat.color+'99);">'+avHtml+'</div>'
            +'<div class="mt-post-body"><div class="mt-post-author">'+esc(item.name)+'</div>'
            +(item.handle?'<div class="mt-post-handle">@'+esc(item.handle.replace(/^@/,''))+'</div>':'')
            +(dt?'<div class="mt-post-date">'+dt+'</div>':'')
            +(item.content?'<div class="mt-post-text">'+esc(item.content.slice(0,200))+'</div>':'')
            +'<div class="mt-post-stats"><span class="mt-plat-badge" style="background:'+plat.color+'15;color:'+plat.color+';border:1px solid '+plat.color+'30;">'+plat.label+'</span>'
            +parts.join('')+'<span class="mt-sent mt-sent--'+item.sentiment+'">'+sentLbl+'</span>'
            +(item.url?'<a href="'+esc(item.url)+'" target="_blank" rel="noopener" class="mt-view-link" onclick="event.stopPropagation()"><i class="ph ph-arrow-square-out me-1"></i>Lihat</a>':'')
            +'</div></div></div>';
    },
    _pagHtml(page,pages,total,from,to){
        if(pages<=1)return'';let btns='',r=2;
        btns+='<button class="mt-pag-btn" '+(page<=1?'disabled':'')+' onclick="MTData.goPage('+(page-1)+')"><i class="ph ph-caret-left"></i></button>';
        for(let i=1;i<=pages;i++){if(i===1||i===pages||(i>=page-r&&i<=page+r))btns+='<button class="mt-pag-btn'+(i===page?' is-active':'')+'" onclick="MTData.goPage('+i+')">'+i+'</button>';else if(i===page-r-1||i===page+r+1)btns+='<span class="mt-pag-btn" style="cursor:default;opacity:.4;">...</span>';}
        btns+='<button class="mt-pag-btn" '+(page>=pages?'disabled':'')+' onclick="MTData.goPage('+(page+1)+')"><i class="ph ph-caret-right"></i></button>';
        return'<div class="mt-pagination"><span class="mt-pag-info">Menampilkan '+from+'-'+to+' dari '+total+' mentions</span><div class="mt-pag-controls">'+btns+'</div></div>';
    },
    goPage(p){const items=this._getItems(),pages=Math.ceil(items.length/MTCfg.perPage);if(p<1||p>pages)return;_page=p;this._renderList();const el=_$('listContainer');if(el)el.scrollIntoView({behavior:'smooth',block:'nearest'});}
};

/* ════════════════════════════════════════
   SLIDE PANEL — Dashboard style
════════════════════════════════════════ */
const MTPanelNew=(() => {
    let _allItems=[],_filtered=[],_curSent='all';

    function open(items,platform){
        _allItems=items;_filtered=items;_curSent='all';
        const plat=PLAT[platform]||{label:'All Media',color:'#038047'};
        _$('mtPanelDot').style.background=plat.color;
        _$('mtPanelTitle').textContent=plat.label+' Mentions';
        _$('mtPanelMeta').textContent=MTCfg.sd+' – '+MTCfg.ed;
        document.querySelectorAll('#mtPanelNew .do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s==='all'));
        MTDetailNew.close();
        const ov=_$('mtPanelOverlay'),pn=_$('mtPanelNew');
        ov.classList.remove('hiding');pn.classList.remove('hiding');
        ov.classList.add('show');pn.classList.add('show');
        _render();
    }

    function close(){
        MTDetailNew.killIframe();MTDetailNew.close();
        const ov=_$('mtPanelOverlay'),pn=_$('mtPanelNew');
        pn.classList.add('hiding');ov.classList.add('hiding');
        setTimeout(()=>{pn.classList.remove('show','hiding');ov.classList.remove('show','hiding');},240);
    }

    let _renderedCount = 0;
    
    function filterSent(sent){
        _curSent=sent;
        document.querySelectorAll('#mtPanelNew .do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s===sent));
        _filtered=sent==='all'?_allItems:_allItems.filter(m=>m.sentiment===sent);
        _render();
    }

    function _render(){
        const list=_$('mtPanelList');if(!list)return;
        list.innerHTML=''; list.scrollTop=0; _renderedCount=0;
        loadMore(list);
    }
    
    function loadMore(list = _$('mtPanelList')){
        const btn = _$('mtPopLoadMoreBtn'); if(btn) btn.remove();
        if(!list)return;
        if(!_filtered.length){list.innerHTML='<div style="padding:50px 20px;text-align:center;color:#94A3B8;font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>';return;}
        const dummy='/assets/images/user/dummy.jpg';
        const limit=10, start=_renderedCount||0, chunk=_filtered.slice(start, start+limit);
        
        const html=chunk.map(item=>{
            const plat=PLAT[item._platform]||PLAT.doc;
            const avHtml=(item.avatar&&item.avatar.indexOf('http')===0)?'<img src="'+esc(item.avatar)+'" onerror="this.src=\''+dummy+'\'">':'<img src="'+dummy+'">';
            const sentLbl={pos:'Pos',neg:'Neg',neu:'Neu'}[item.sentiment];
            const dt=(item.date||'').split('T')[0];
            const enc=encodeURIComponent(JSON.stringify(item));
            return '<div class="do-panel-item" onclick="MTDetailNew.openEnc(\''+esc(enc)+'\')">'
                +'<div class="do-panel-avatar" style="background:linear-gradient(135deg,'+plat.color+','+plat.color+'99);">'+avHtml+'</div>'
                +'<div class="do-panel-item-body"><div class="do-panel-author">'+esc(item.name)+'</div>'
                +(item.handle?'<div class="do-panel-handle">@'+esc(item.handle.replace(/^@/,''))+'</div>':'')
                +'<div class="do-panel-text">'+esc((item.content||'').slice(0,130)||'(tidak ada konten)')+'</div>'
                +'<div class="do-panel-footer"><span class="do-sent-badge do-sent-badge--'+item.sentiment+'">'+sentLbl+'</span>'
                +'<span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:'+plat.color+';flex-shrink:0;"></span>'
                +'<span style="font-size:10px;font-weight:600;color:'+plat.color+';">'+plat.label+'</span>'
                +(dt?'<span style="margin-left:auto;">'+dt+'</span>':'')
                +'</div></div></div>';
        }).join('');
        
        list.insertAdjacentHTML('beforeend', html);
        _renderedCount = start + chunk.length;

        if (_filtered.length > _renderedCount) {
             const left = _filtered.length - _renderedCount;
             const next = Math.min(limit, left);
             list.insertAdjacentHTML('beforeend', `<div id="mtPopLoadMoreBtn" style="padding:10px;"><button style="width:100%;height:32px;display:flex;align-items:center;justify-content:center;gap:6px;background:#f8fafc;color:#475569;border:1px dashed #cbd5e1;border-radius:6px;font-weight:600;cursor:pointer;" onclick="MTPanelNew.loadMore()">Muat ${next} lagi <span style="font-weight:400;opacity:0.8;">(${left.toLocaleString()} tersisa)</span> <i class="ph ph-caret-down"></i></button></div>`);
        }
    }

    return{open,close,filterSent,loadMore};
})();

/* ════════════════════════════════════════
   DETAIL SUB-PANEL — Dashboard style
════════════════════════════════════════ */
const MTDetailNew={
    openEnc(enc){try{this.open(JSON.parse(decodeURIComponent(enc)));}catch(e){console.warn(e);}},
    open(item){
        const panel=_$('mtDetailPanelNew'),body=_$('mtDetailBodyNew'),title=_$('mtDetailTitleNew');
        if(!panel||!body)return;
        const plat=PLAT[item._platform]||PLAT.doc;
        const dummy='/assets/images/user/dummy.jpg';
        const avHtml=(item.avatar&&item.avatar.indexOf('http')===0)?'<img src="'+esc(item.avatar)+'" onerror="this.src=\''+dummy+'\'">':'<img src="'+dummy+'">';
        const sentLbl={pos:'Positif',neg:'Negatif',neu:'Netral'}[item.sentiment];
        const sentCls={pos:'do-dp2-sent--pos',neg:'do-dp2-sent--neg',neu:'do-dp2-sent--neu'}[item.sentiment];
        let dtFmt='';if(item.date){try{dtFmt=new Date(item.date).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){dtFmt=(item.date||'').split('T')[0];}}
        /* Media embed */
        let mediaHtml='';const url=item.url||'';
        if(item._platform==='ytb'){
            let vid='';if(url){const m=url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/);if(m)vid=m[1];}
            if(!vid && item._raw && item._raw.video_id) vid=item._raw.video_id; 
            if(!vid && item._raw && item._raw.id) {const strId=String(item._raw.id); if(strId.length===11) vid=strId;}
            if(vid){const eid='yt_'+Date.now();mediaHtml='<div id="'+eid+'" style="position:relative;cursor:pointer;border-radius:6px;overflow:hidden;background:#000;margin-bottom:10px;" onclick="MTDetailNew._playYT(\''+eid+'\',\''+vid+'\')"><img src="https://img.youtube.com/vi/'+vid+'/hqdefault.jpg" style="width:100%;height:200px;object-fit:cover;display:block;" onerror="this.src=\'https://img.youtube.com/vi/'+vid+'/mqdefault.jpg\'"><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);"><div style="width:52px;height:52px;background:#ff0000;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:22px;color:#fff;margin-left:3px;"></i></div></div></div>';}
        }
        else if(item._platform==='tiktok'){let vid='';if(url){const m2=url.match(/\/video\/(\d+)/);if(m2)vid=m2[1];}if(!vid&&item._raw&&item._raw.id){const m3=String(item._raw.id).match(/(\d{10,})/);if(m3)vid=m3[1];}if(vid){const eid='tt_'+Date.now();mediaHtml='<div id="'+eid+'" style="position:relative;cursor:pointer;background:#111827;border-radius:6px;overflow:hidden;height:240px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;" onclick="MTDetailNew._playTT(\''+eid+'\',\''+vid+'\')"><div style="z-index:2;width:56px;height:56px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:24px;color:#111827;margin-left:3px;"></i></div><div style="position:absolute;bottom:8px;right:8px;background:#111827;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:3px;">TIKTOK</div></div>';}}
        /* Stats */
        const sm={twit:[['Retweet',item.retweets],['Like',item.likes],['Comment',item.comments]],fb:[['Like',item.likes],['Share',item.shares],['Comment',item.comments]],ig:[['Like',item.likes],['Comment',item.comments],['View',item.views]],ytb:[['View',item.views],['Like',item.likes],['Comment',item.comments]],tiktok:[['Play',item.views],['Like',item.likes],['Share',item.shares]],doc:[['View',item.views],['Share',item.shares],['Comment',item.comments]]};
        const stats=sm[item._platform]||[];
        const statsHtml=stats.some(s=>parseInt(s[1])>0)?'<div class="do-dp2-stats">'+stats.map(([l,v])=>'<div class="do-dp2-stat"><div class="do-dp2-stat-val">'+parseInt(v||0).toLocaleString('id-ID')+'</div><div class="do-dp2-stat-lbl">'+l+'</div></div>').join('')+'</div>':'';
        title.textContent=item.name;
        body.innerHTML='<div class="do-dp2-avatar-row"><div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,'+plat.color+','+plat.color+'99);">'+avHtml+'</div>'
            +'<div><div class="do-dp2-name">'+esc(item.name)+'</div>'+(item.handle?'<div class="do-dp2-handle">@'+esc(item.handle.replace(/^@/,''))+'</div>':'')+'<span class="do-dp2-plat-badge" style="background:'+plat.color+'18;color:'+plat.color+';">'+plat.label+'</span></div></div>'
            +(dtFmt?'<div class="do-dp2-meta">'+dtFmt+'</div>':'')+'<div class="do-dp2-sent '+sentCls+'">'+sentLbl+'</div>'
            +mediaHtml+(item.content?'<div class="do-dp2-content">'+esc(item.content)+'</div>':'')+statsHtml
            +(url?'<a href="'+esc(url)+'" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out me-1"></i>Lihat Sumber Asli</a>':'');
        panel.classList.add('show');
    },
    close(){const p=_$('mtDetailPanelNew');if(p)p.classList.remove('show');},
    killIframe(){const b=_$('mtDetailBodyNew');if(b)b.querySelectorAll('iframe').forEach(f=>{f.src='';f.remove();});},
    _playYT(eid,vid){const el=_$(eid);if(!el)return;el.innerHTML='<iframe width="100%" height="260" src="https://www.youtube.com/embed/'+vid+'?autoplay=1&controls=1" frameborder="0" allowfullscreen style="display:block;border-radius:6px;"></iframe>';el.style.cursor='default';},
    _playTT(eid,vid){const el=_$(eid);if(!el)return;el.style.cssText='min-height:560px;height:auto;background:#111827;border-radius:6px;overflow:hidden;margin-bottom:10px;';el.innerHTML='<iframe src="https://www.tiktok.com/embed/v2/'+vid+'" width="100%" height="560" frameborder="0" allow="autoplay" allowfullscreen style="display:block;border:none;"></iframe>';}
};

/* ════════════════════════════════════════
   EXPORT MODULE
════════════════════════════════════════ */
const MTExport=(() => {
    let _timer=null;
    function _toast(msg,type='default',dur=3200){const t=_$('exportToast'),m=_$('exportToastMsg'),ico=_$('exportToastIcon');if(!t||!m)return;m.textContent=msg;t.className='export-toast show'+(type!=='default'?' '+type:'');const ic={success:'ph-check-circle',error:'ph-x-circle',default:'ph-spinner'};if(ico)ico.className='ph '+(ic[type]||ic.default);clearTimeout(_timer);_timer=setTimeout(()=>t.classList.remove('show'),dur);}
    function _btnState(btn,on){if(!btn)return;btn.disabled=on;btn.classList.toggle('exporting',on);}
    function _disableAll(on){['pageExportPdfBtn','pageExportImgBtn','pageExportCsvBtn'].forEach(id=>{const b=_$(id);if(b){b.disabled=on;b.classList.toggle('exporting',on);}});}

    /* ── Freeze / Unfreeze semua animasi CSS (Safari-safe) ── */
    function _freeze() {
        if (document.getElementById('__mt_freeze')) return;
        const s = document.createElement('style');
        s.id = '__mt_freeze';
        s.textContent = `
            *, *::before, *::after {
                animation-play-state: paused !important;
                animation-duration: 0s !important;
                animation-delay: 0s !important;
                transition-duration: 0s !important;
                transition-delay: 0s !important;
            }
            .mt-post, .card, [class*="col-"], .kpi-card-hover {
                opacity: 1 !important;
                transform: none !important;
                animation: none !important;
            }
        `;
        document.head.appendChild(s);
    }
    function _unfreeze() { document.getElementById('__mt_freeze')?.remove(); }

    /* onclone helper bersama */
    function _onClone(clonedDoc) {
        const s = clonedDoc.createElement('style');
        s.textContent = `
            *, *::before, *::after { animation: none !important; transition: none !important; }
            [data-html2canvas-ignore] { display: none !important; }
            .mt-post, .card, [class*="col-"], .kpi-card-hover {
                opacity: 1 !important; transform: none !important; visibility: visible !important;
            }
            .do-panel-overlay, .do-panel, .do-detail-panel, .export-toast {
                display: none !important;
            }
        `;
        clonedDoc.head.appendChild(s);
        /* Sembunyikan avatar cross-origin */
        clonedDoc.querySelectorAll('.mt-post-av, .do-panel-avatar, .do-dp2-avatar-lg').forEach(wrapper => {
            wrapper.querySelectorAll('img').forEach(img => { img.style.display = 'none'; });
            if (!wrapper.querySelector('.__ini')) {
                const sp = clonedDoc.createElement('span');
                sp.className  = '__ini';
                sp.textContent = (wrapper.textContent || 'M').trim()[0].toUpperCase();
                sp.style.cssText = 'font-size:12px;font-weight:700;color:#fff;line-height:1;';
                wrapper.appendChild(sp);
            }
        });
    }

    async function _capturePage(){
        const area=_$('pageExportArea');if(!area)throw new Error('pageExportArea tidak ditemukan');
        window.scrollTo({top:0});
        await new Promise(r=>setTimeout(r,300));
        if(_trendChart){try{await _trendChart.updateOptions({});}catch(e){}}
        if(_barChart){try{_barChart.resize();}catch(e){}}
        _freeze();
        await new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r)));
        await new Promise(r=>setTimeout(r,400));
        try {
            return await html2canvas(area,{
                scale:2,useCORS:true,allowTaint:false,
                backgroundColor:'#f1f5f8',logging:false,removeContainer:true,
                windowWidth:document.documentElement.scrollWidth,
                windowHeight:area.scrollHeight,height:area.scrollHeight,
                onclone: d => _onClone(d),
                ignoreElements:el=>el.hasAttribute('data-html2canvas-ignore')||['pageExportPdfBtn','pageExportImgBtn','pageExportCsvBtn'].includes(el.id)
            });
        } finally {
            _unfreeze();
        }
    }

    async function _captureCard(areaId){
        const area=document.getElementById(areaId);if(!area)throw new Error('Area #'+areaId+' tidak ditemukan');
        if(_barChart&&areaId==='card-export-dist'){try{_barChart.resize();}catch(e){}}
        _freeze();
        await new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r)));
        await new Promise(r=>setTimeout(r,350));
        try {
            return await html2canvas(area,{
                scale:2,useCORS:true,allowTaint:false,
                backgroundColor:'#ffffff',logging:false,removeContainer:true,
                onclone: d => _onClone(d),
                ignoreElements:el=>el.hasAttribute('data-html2canvas-ignore')
            });
        } finally {
            _unfreeze();
        }
    }

    function _pdfHeader(pdf,title){const pW=pdf.internal.pageSize.getWidth();pdf.setFillColor(3,128,71);pdf.rect(0,0,pW,11,'F');pdf.setTextColor(255,255,255);pdf.setFontSize(9);pdf.setFont('helvetica','bold');pdf.text('SMADIMENT — '+title,10,7.5);const now=new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});pdf.setFontSize(7);pdf.setFont('helvetica','normal');pdf.text('Generated: '+now,pW-10,7.5,{align:'right'});}
    async function _paginatePdf(pdf,canvas,title){const pW=pdf.internal.pageSize.getWidth(),pH=pdf.internal.pageSize.getHeight(),margin=10,usableW=pW-margin*2,usableH=pH-margin*2-14,ratio=usableW/canvas.width,sliceH=usableH/ratio;let srcY=0,pg=0;while(srcY<canvas.height){if(pg>0){pdf.addPage();_pdfHeader(pdf,title);}const srcSlice=Math.min(sliceH,canvas.height-srcY),dstH=srcSlice*ratio,slice=document.createElement('canvas');slice.width=canvas.width;slice.height=Math.ceil(srcSlice);slice.getContext('2d').drawImage(canvas,0,srcY,canvas.width,srcSlice,0,0,canvas.width,srcSlice);pdf.addImage(slice.toDataURL('image/png'),'PNG',margin,14,usableW,dstH);pdf.setFontSize(7);pdf.setTextColor(148,163,184);pdf.text('Halaman '+(pg+1),pW/2,pH-3,{align:'center'});srcY+=srcSlice;pg++;}}
    function _stamp(){return new Date().toISOString().slice(0,10).replace(/-/g,'');}
    function _toCsv(items){const headers=['No','Platform','Author','Handle','Date','Sentiment','Content','Views','Likes','Retweets','Comments','Shares','URL'];const rows=items.map((m,i)=>[i+1,(PLAT[m._platform]||PLAT.doc).label,m.name,m.handle,(m.date||'').split('T')[0],{pos:'Positive',neg:'Negative',neu:'Neutral'}[m.sentiment]||'',m.content.replace(/"/g,'""'),m.views,m.likes,m.retweets,m.comments,m.shares,m.url]);return'\uFEFF'+[headers,...rows].map(r=>r.map(v=>'"'+String(v||'').replace(/"/g,'""')+'"').join(',')).join('\n');}
    function _dlCsv(content,filename){const blob=new Blob([content],{type:'text/csv;charset=utf-8;'});const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download=filename;a.click();setTimeout(()=>URL.revokeObjectURL(url),2000);}
    async function run(type,btn){
        if(type==='csv'){_btnState(btn,true);_toast('Menyiapkan CSV…','default',99999);try{const items=Store.all;if(!items.length){_toast('Tidak ada data','error');return;}_dlCsv(_toCsv(items),'mentions_all_'+MTCfg.pid+'_'+_stamp()+'.csv');_toast('CSV berhasil diunduh! ('+items.length+' baris)','success');}catch(err){_toast('Export gagal: '+err.message,'error');}finally{_btnState(btn,false);}return;}
        if(!window.html2canvas){_toast('html2canvas tidak tersedia','error');return;}if(type==='pdf'&&!window.jspdf?.jsPDF){_toast('jsPDF tidak tersedia','error');return;}
        _disableAll(true);_toast(type==='pdf'?'Menyiapkan PDF…':'Mengambil gambar…','default',99999);
        try{const canvas=await _capturePage(),stamp=_stamp();if(type==='image'){const a=document.createElement('a');a.download='mentions_timeline_'+MTCfg.pid+'_'+stamp+'.png';a.href=canvas.toDataURL('image/png');a.click();_toast('Gambar berhasil diunduh!','success');}else{const{jsPDF}=window.jspdf;const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});_pdfHeader(pdf,'Mentions Timeline');await _paginatePdf(pdf,canvas,'Mentions Timeline');pdf.save('mentions_timeline_'+MTCfg.pid+'_'+stamp+'.pdf');_toast('PDF berhasil diunduh!','success');}}catch(err){_toast('Export gagal: '+err.message,'error');}finally{_disableAll(false);}
    }
    async function runCard(areaId,cardKey,type,btn){
        if(!window.html2canvas){_toast('html2canvas tidak tersedia','error');return;}if(type==='pdf'&&!window.jspdf?.jsPDF){_toast('jsPDF tidak tersedia','error');return;}
        _btnState(btn,true);_toast(type==='pdf'?'Menyiapkan PDF…':'Mengambil gambar…','default',99999);
        const titles={trend:'Mentions Trend',distribution:'Platform Distribution',list:'Mentions List'};
        try{const canvas=await _captureCard(areaId),title=titles[cardKey]||'Mentions',fname='mentions_'+cardKey+'_'+MTCfg.pid+'_'+_stamp();if(type==='image'){const a=document.createElement('a');a.download=fname+'.png';a.href=canvas.toDataURL('image/png');a.click();_toast('Gambar berhasil diunduh!','success');}else{const{jsPDF}=window.jspdf;const landscape=canvas.width>canvas.height;const pdf=new jsPDF({orientation:landscape?'landscape':'portrait',unit:'mm',format:'a4'});_pdfHeader(pdf,title);await _paginatePdf(pdf,canvas,title);pdf.save(fname+'.pdf');_toast('PDF berhasil diunduh!','success');}}catch(err){_toast('Export gagal: '+err.message,'error');}finally{_btnState(btn,false);}
    }
    function runCsvCard(btn){_btnState(btn,true);_toast('Menyiapkan CSV…','default',99999);try{const items=_activeTab==='all'?Store.all:(Store[_activeTab]||[]);if(!items.length){_toast('Tidak ada data','error');return;}_dlCsv(_toCsv(items),'mentions_'+_activeTab+'_'+MTCfg.pid+'_'+_stamp()+'.csv');_toast('CSV berhasil diunduh! ('+items.length+' baris)','success');}catch(err){_toast('Export gagal: '+err.message,'error');}finally{_btnState(btn,false);}
    }
    return{run,runCard,runCsvCard};
})();

/* ── Init ── */
document.addEventListener('DOMContentLoaded',()=>{
    MTData.loadAll();
    document.addEventListener('keydown',e=>{if(e.key==='Escape')MTPanelNew.close();});
});
</script>
@endsection