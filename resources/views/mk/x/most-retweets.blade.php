@extends('mk.layouts.app')

@section('title', 'X Most Retweets - SMADIMENT')

@section('styles')
<style>
:root{--primary:#038047;--primary-rgb:3,128,71;--primary-lt:rgba(3,128,71,.10);--dark:#273B4A;--white:#FFFFFF;--bg:#F1F5F8;--green:#038047;--green-light:#E8F5EE;--red:#EF4444;--red-light:#FEF2F2;--amber:#F59E0B;--amber-light:#FFFBEB;--cyan:#06B6D4;--cyan-light:#ECFEFF;--slate-50:#F8FAFC;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-300:#CBD5E1;--slate-400:#94A3B8;--slate-500:#64748B;--slate-600:#475569;--slate-700:#334155;--slate-800:#1E293B;--slate-900:#0F172A;--radius:8px;--radius-sm:5px;--shadow-sm:0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);--shadow-md:0 4px 14px rgba(15,23,42,.08);--shadow-lg:0 10px 30px rgba(15,23,42,.12)}

@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes slideInRight{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}
@keyframes slideOutRight{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}
@keyframes overlayIn{from{opacity:0}to{opacity:1}}
@keyframes overlayOut{from{opacity:1}to{opacity:0}}
@keyframes kpiIconBounce{0%,100%{transform:scale(1) rotate(0)}30%{transform:scale(1.25) rotate(-10deg)}60%{transform:scale(1.1) rotate(6deg)}}
@keyframes kpiShimmer{0%{left:-100%}100%{left:150%}}

.kpi-icon-bg{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0}
.sk-block{border-radius:4px;background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
.spin-ring{width:26px;height:26px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
.spinner-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;gap:12px;color:var(--slate-400);font-size:12px;font-weight:600}

.kpi-card-hover{will-change:transform,box-shadow;cursor:default;position:relative!important;overflow:hidden!important;transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important}
.kpi-card-hover::before{content:'';position:absolute;top:0;bottom:0;left:-100%;width:60%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);pointer-events:none;z-index:1}
.kpi-card-hover:hover{transform:translateY(-6px) scale(1.025)!important;box-shadow:0 20px 40px rgba(0,0,0,.25)!important;filter:brightness(1.07)!important}
.kpi-card-hover:hover::before{animation:kpiShimmer .6s ease forwards}
.kpi-card-hover:hover .kpi-icon-bg{background:rgba(255,255,255,.35)!important}
.kpi-card-hover:hover .kpi-icon-bg i{animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important;display:inline-block!important}
.kpi-card-hover:active{transform:translateY(-2px) scale(1.01)!important;transition-duration:.08s!important}

.chart-container{position:relative}
.chart-loading{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;background:#fff;z-index:2;transition:opacity .3s}
.chart-loading.hidden{opacity:0;pointer-events:none}
.chart-loading span{font-size:11px;font-weight:600;color:var(--slate-400)}
.chart-empty{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600}
.chart-empty i{font-size:34px;color:var(--slate-300);display:block}

.tme-post-list{display:flex;flex-direction:column}
.tme-post{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--slate-100);transition:background .12s;cursor:pointer}
.tme-post:last-child{border-bottom:none}
.tme-post:hover{background:var(--slate-50)}
.tme-post-rank{width:22px;height:22px;border-radius:50%;background:var(--slate-100);border:1px solid var(--slate-200);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:var(--slate-400);flex-shrink:0;margin-top:8px}
.tme-post-rank--1{background:linear-gradient(135deg,#ffd700,#F59E0B);color:#7c5900;border-color:#ffd700}
.tme-post-rank--2{background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0}
.tme-post-rank--3{background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32}
.tme-post-av{width:36px;height:36px;border-radius:50%;flex-shrink:0;color:#fff;font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--slate-200);overflow:hidden}
.tme-post-av img{width:100%;height:100%;object-fit:cover;border-radius:50%}
.tme-post-body{flex:1;min-width:0}
.tme-post-author{font-size:12.5px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tme-post-date{font-size:10px;color:var(--slate-400);margin-top:1px;margin-bottom:4px}
.tme-post-text{font-size:11.5px;color:var(--slate-500);line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:6px;word-break:break-word}
.tme-post-stats{display:flex;align-items:center;gap:4px;flex-wrap:wrap}
.tme-metric{display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:3px;font-size:10px;font-weight:700;background:var(--slate-100);color:var(--slate-500);white-space:nowrap}
.tme-metric--primary{background:var(--primary-lt);color:var(--primary)}
.tme-metric--amber{background:rgba(245,158,11,.1);color:#92400e}
.tme-metric--cyan{background:rgba(6,182,212,.1);color:#164e63}
.tme-metric--red{background:rgba(239,68,68,.1);color:#991b1b}
.tme-sent{display:inline-flex;align-items:center;padding:2px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.3px}
.tme-sent--pos{background:#d1fae5;color:#065f46}
.tme-sent--neg{background:#fee2e2;color:#991b1b}
.tme-sent--neu{background:var(--slate-100);color:var(--slate-500)}
.tme-view-link{display:inline-flex;align-items:center;gap:3px;font-size:9.5px;font-weight:700;color:var(--primary);text-decoration:none;padding:2px 6px;border-radius:3px;background:var(--primary-lt);border:1px solid rgba(3,128,71,.2);transition:background .12s,color .12s;margin-left:auto}
.tme-view-link:hover{background:var(--primary);color:#fff}

.tme-pagination{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--slate-100);flex-wrap:wrap;gap:8px}
.tme-pag-info{font-size:11px;color:var(--slate-400);font-weight:500}
.tme-pag-controls{display:flex;align-items:center;gap:3px}
.tme-pag-btn{min-width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;padding:0 6px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .12s;user-select:none}
.tme-pag-btn:hover:not(:disabled):not(.is-active){border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}
.tme-pag-btn.is-active{background:var(--primary);border-color:var(--primary);color:#fff}
.tme-pag-btn:disabled{opacity:.35;cursor:not-allowed}
.tme-rows-sel{padding:4px 9px;border:1px solid var(--slate-200);border-radius:var(--radius-sm);font-size:11px;font-weight:600;color:var(--slate-600);background:var(--slate-50);outline:none;cursor:pointer;transition:border-color .14s}
.tme-rows-sel:focus{border-color:var(--primary)}

.donut-legend{display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:10px}
.donut-leg-item{display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--slate-500);padding:3px 8px;background:var(--slate-50);border-radius:3px;border:1px solid var(--slate-200);cursor:pointer;transition:border-color .12s,background .12s,color .12s}
.donut-leg-item:hover{border-color:var(--primary);background:var(--primary-lt);color:var(--primary)}
.donut-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}

/* ══ Slide Panel ══ */
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
.do-panel-text{font-size:11px;color:var(--slate-600);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px}
.do-panel-footer{display:flex;align-items:center;gap:5px;font-size:10px;color:var(--slate-400);flex-wrap:wrap}
.do-sent-badge{padding:1px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase}
.do-sent-badge--pos{background:#dbeafe;color:#1d4ed8}
.do-sent-badge--neg{background:#fee2e2;color:#991b1b}
.do-sent-badge--neu{background:var(--slate-100);color:var(--slate-500)}
.do-panel-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:12px;color:var(--slate-400);font-size:13px;font-weight:600}
.do-panel-spinner{width:28px;height:28px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}

/* Detail sub-panel */
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
.do-dp2-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:10px}
.do-dp2-stat{background:var(--slate-50);border-radius:var(--radius-sm);padding:8px 10px;border:1px solid var(--slate-200);text-align:center}
.do-dp2-stat-val{font-size:14px;font-weight:700;color:var(--slate-900)}
.do-dp2-stat-lbl{font-size:9px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.4px;margin-top:1px}
.do-dp2-link{display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:var(--primary);color:#fff;border-radius:var(--radius-sm);font-size:12px;font-weight:700;text-decoration:none;transition:filter .14s;margin-top:4px}
.do-dp2-link:hover{filter:brightness(1.1);color:#fff}

/* ══ Export Styles ══ */
.page-export-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);padding:9px 14px;margin-bottom:20px;box-shadow:var(--shadow-sm)}
.page-export-bar-left{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--slate-600)}
.page-export-bar-left i{font-size:15px;color:var(--primary)}
.page-export-bar-right{display:flex;gap:8px}
.page-export-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);font-size:16px;cursor:pointer;transition:all .15s ease;border:1.5px solid transparent;font-family:inherit}
.page-export-btn-pdf{background:#fff3f3;color:#dc2626;border-color:#fca5a5}
.page-export-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.page-export-btn-img{background:var(--primary-lt);color:var(--primary);border-color:rgba(3,128,71,.3)}
.page-export-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.page-export-btn:disabled{opacity:.55;cursor:not-allowed;pointer-events:none}
.page-export-btn .export-spinner{width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
.page-export-btn.exporting .export-spinner{display:inline-block}
.page-export-btn.exporting .export-icon{display:none}
.card-exp-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:var(--radius-sm);font-size:14px;cursor:pointer;flex-shrink:0;transition:all .14s ease;border:1px solid transparent;font-family:inherit;background:transparent}
.card-exp-btn-pdf{color:#dc2626;border-color:#fca5a5;background:#fff3f3}
.card-exp-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.card-exp-btn-img{color:var(--primary);border-color:rgba(3,128,71,.3);background:var(--primary-lt)}
.card-exp-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.card-exp-btn:disabled{opacity:.45;cursor:not-allowed;pointer-events:none}
.card-exp-btn .export-spinner{width:11px;height:11px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
.card-exp-btn.exporting .export-spinner{display:inline-block}
.card-exp-btn.exporting .export-icon{display:none}
.export-toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--slate-900);color:#fff;border-radius:var(--radius);padding:10px 18px;font-size:12px;font-weight:600;box-shadow:var(--shadow-lg);z-index:99999;opacity:0;pointer-events:none;transition:opacity .22s ease,transform .22s ease;display:flex;align-items:center;gap:8px;white-space:nowrap}
.export-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.export-toast.success{background:#065f46}
.export-toast.error{background:#991b1b}

.kpi-card-hover h3{font-size:1.5rem}
@media(max-width:640px){.do-panel{width:100vw}}
</style>
@endsection

@section('page-title', 'X Most Retweets')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate   = $endDate ?? request()->get('end_date', now()->format('Y-m-d'));
    $projects  = $projects ?? [];
@endphp
<script>
    const OV_PID = {{ $projectId ? (int)$projectId : 'null' }};
    const OV_SD  = '{{ $startDate }}';
    const OV_ED  = '{{ $endDate }}';
</script>

@include('mk.layouts.partials.filter-datepicker')

{{-- ════ PAGE EXPORT AREA WRAPPER ════ --}}
<div id="pageExportArea">

{{-- ══ Page Export Toolbar ══ --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
        <i class="ph ph-export"></i>
        <span>Export Halaman</span>
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Donut + Tweet List</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                onclick="XExport.run('pdf', this)" title="Export halaman sebagai PDF">
            <i class="ph ph-file-pdf export-icon"></i>
            <span class="export-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                onclick="XExport.run('image', this)" title="Export halaman sebagai PNG">
            <i class="ph ph-image export-icon"></i>
            <span class="export-spinner"></span>
        </button>
    </div>
</div>

{{-- KPI --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-primary text-white kpi-card-hover fade-up fade-up-d1">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Tweets</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiTotal">
                            <span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTotalSub">
                            <i class="ph ph-file-text me-1"></i>Loading…
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-file-text"></i></div>
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
                        <p class="mb-1 text-white text-opacity-75 f-12">Unique Authors</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiAuthors">
                            <span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiAuthorsSub">
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
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Retweets</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiRt">
                            <span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiRtSub">
                            <i class="ph ph-repeat me-1"></i>Loading…
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-repeat"></i></div>
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
                        <p class="mb-1 text-white text-opacity-75 f-12">Highest Retweet</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiHighest">
                            <span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiHighestSub">
                            <i class="ph ph-trophy me-1"></i>Loading…
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-trophy"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Donut Chart Card --}}
<div class="row">
    <div class="col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-donut">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded">
                            <i class="ph ph-chart-donut f-18 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Top 5 Most Retweeted</h6>
                            <small class="text-muted">Klik untuk lihat detail</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div id="donutLegend" class="donut-legend"></div>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf"
                                    onclick="XExport.runCard('card-donut','donut','pdf',this)"
                                    title="Export PDF">
                                <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                            </button>
                            <button class="card-exp-btn card-exp-btn-img"
                                    onclick="XExport.runCard('card-donut','donut','image',this)"
                                    title="Export PNG">
                                <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height:340px;">
                        <div class="chart-loading" id="loadingDonut">
                            <div class="spin-ring"></div>
                            <span>Loading…</span>
                        </div>
                        <div id="donutChart" style="width:100%;height:340px;display:none;"></div>
                        <div id="donutEmpty" style="display:none;" class="chart-empty">
                            <i class="ph ph-chart-donut"></i><span>No data</span>
                        </div>
                    </div>
                </div>
            </div>{{-- /card-donut --}}
        </div>
    </div>
</div>

{{-- Tweet List Card --}}
<div class="row">
    <div class="col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
            <div id="card-tweetlist">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded">
                            <i class="ph ph-repeat f-18 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Ranked Tweets by Retweets</h6>
                            <small class="text-muted">Klik tweet untuk lihat detail</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-primary text-primary" id="badgeTotal">Loading…</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf"
                                    onclick="XExport.runCard('card-tweetlist','tweetlist','pdf',this)"
                                    title="Export PDF">
                                <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                            </button>
                            <button class="card-exp-btn card-exp-btn-img"
                                    onclick="XExport.runCard('card-tweetlist','tweetlist','image',this)"
                                    title="Export PNG">
                                <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="tweetList" class="p-0">
                    <div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div>
                </div>
                <div id="pagArea"></div>
            </div>{{-- /card-tweetlist --}}
        </div>
    </div>
</div>

</div>{{-- /pageExportArea --}}

{{-- Export Toast --}}
<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

{{-- Slide Panel --}}
<div class="do-panel-overlay" id="panelOverlay" onclick="Panel.close()"></div>
<div class="do-panel" id="sntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" style="background:var(--primary);"></div>
        <span class="do-panel-title" id="panelTitle">X Tweets</span>
        <button class="do-panel-close" onclick="Panel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta">
            <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
            <span id="panelMeta">—</span>
        </div>
    </div>
    <div class="do-panel-list" id="panelList"></div>
    <div class="do-detail-panel" id="detailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="Detail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="detailTitle">Detail</span>
            <button class="do-panel-close" onclick="Panel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="detailBody"></div>
    </div>
</div>

@endsection

@section('scripts')
{{-- Export dependencies --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>

<script>
'use strict';

const CFG = { pid: OV_PID, sd: OV_SD, ed: OV_ED };
const DONUT_COLORS = ['#038047','#273B4A','#F59E0B','#06B6D4','#EF4444'];

/* ══ UTILS ══ */
const _$    = id => document.getElementById(id);
const numF  = n  => parseInt(n || 0).toLocaleString('id-ID');
const numK  = n  => { n = parseInt(n || 0); return n >= 1e6 ? (n/1e6).toFixed(1)+'M' : n >= 1000 ? (n/1000).toFixed(1)+'k' : String(n); };
const esc   = s  => (s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const dec   = s  => { if (!s) return ''; try { const f = decodeURIComponent(escape(s)); if (!f.includes('\uFFFD') && f !== s) return f; } catch(e) {} return s; };

let Store = [], curPage = 1;
const PP = 10;

const _getName  = it => it.author?.name  || it.name || 'X User';
const _getScr   = it => it.author?.scr_name || it.name || '';
const _getAvatar = it => (it.avatar_url || it.author?.image || '').replace(/_normal\./g, '.').trim();
const _getColor = it => {
    const s = it.sub_id || it.id || _getName(it);
    const p = ['#038047','#273B4A','#F59E0B','#06B6D4','#8b5cf6','#ec4899','#f97316','#14b8a6'];
    let h = 0;
    for (let i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) & 0xffffffff;
    return p[Math.abs(h) % p.length];
};
const _avHtml = it => {
    const av = _getAvatar(it), d = '/assets/images/user/dummy.jpg';
    return av ? `<img src="${esc(av)}" onerror="this.src='${d}'">` : `<img src="${d}">`;
};
const _normSent = it => {
    const r = String(it.sentiment_str || it.sentiment || '').toLowerCase();
    return r.includes('pos') ? 'pos' : r.includes('neg') ? 'neg' : 'neu';
};
const _tweetUrl = it => {
    const sc = _getScr(it), sid = it.sub_id || it.id || '';
    if (sc && sid) return `https://twitter.com/${encodeURIComponent(sc)}/status/${encodeURIComponent(sid)}`;
    return it.url || it.link || '';
};

/* ══ LOAD DATA ══ */
async function loadData() {
    if (!CFG.pid) return;
    try {
        const r = await fetch(`/mk/api/x/most-retweets?project_id=${CFG.pid}&start_date=${CFG.sd}&end_date=${CFG.ed}`);
        const j = await r.json();
        let items = j.data || j || [];
        if (!Array.isArray(items)) items = [];
        Store = items.sort((a, b) => parseInt(b.freq || 0) - parseInt(a.freq || 0));
        curPage = 1;
        updateKpi();
        renderList();
        renderDonut();
    } catch(e) {
        console.error(e);
        _$('tweetList').innerHTML = `<div class="chart-empty" style="padding:40px;"><i class="ph ph-warning"></i><span>Gagal memuat: ${esc(e.message)}</span></div>`;
        _$('loadingDonut').style.display = 'none';
    }
}

/* ══ KPI ══ */
function updateKpi() {
    const n = Store.length;
    const authors = new Set(Store.map(i => _getScr(i) || _getName(i)));
    let totalRt = 0, maxRt = 0;
    Store.forEach(i => { const rt = parseInt(i.freq || 0); totalRt += rt; if (rt > maxRt) maxRt = rt; });
    const el = (id, v) => { const e = _$(id); if (e) e.textContent = numF(v); };
    el('kpiTotal', n);
    _$('kpiTotalSub').innerHTML = `<i class="ph ph-file-text me-1"></i>${n} tweets collected`;
    el('kpiAuthors', authors.size);
    _$('kpiAuthorsSub').innerHTML = `<i class="ph ph-users me-1"></i>${authors.size} unique authors`;
    el('kpiRt', totalRt);
    _$('kpiRtSub').innerHTML = `<i class="ph ph-repeat me-1"></i>Avg ${numF(n ? Math.round(totalRt / n) : 0)} / tweet`;
    el('kpiHighest', maxRt);
    _$('kpiHighestSub').innerHTML = `<i class="ph ph-trophy me-1"></i>Most retweeted tweet`;
    _$('badgeTotal').textContent = n + ' tweets';
}

/* ══ TWEET LIST ══ */
function renderList() {
    const ls = _$('tweetList'), pg = _$('pagArea');
    if (!ls) return;
    if (!Store.length) {
        ls.innerHTML = `<div class="chart-empty" style="padding:40px;"><i class="ph ph-folder-open"></i><span>Tidak ada data</span></div>`;
        if (pg) pg.innerHTML = '';
        return;
    }
    const total = Store.length, pages = Math.ceil(total / PP), start = (curPage - 1) * PP;
    ls.innerHTML = `<div class="tme-post-list">${Store.slice(start, start + PP).map((it, i) => postHtml(it, start + i)).join('')}</div>`;
    if (pg) pg.innerHTML = pagHtml(curPage, pages, total, start + 1, Math.min(start + PP, total));
    ls.querySelectorAll('.tme-post').forEach(el => {
        el.addEventListener('click', () => {
            try {
                const it = JSON.parse(decodeURIComponent(el.dataset.item));
                Panel.open(Store, 'X — Most Retweets');
                Detail.open(it);
            } catch(e) { console.warn(e); }
        });
    });
}

function postHtml(item, gi) {
    const rank = gi + 1, rkC = rank <= 3 ? '--' + rank : '';
    const name = _getName(item), scr = _getScr(item), color = _getColor(item);
    const avH = _avHtml(item), sent = _normSent(item);
    const content = dec((item.content || '').replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim()).slice(0, 200);
    const dt = (item.date_created || '').split('T')[0], url = _tweetUrl(item);
    const rt = parseInt(item.freq || 0);
    const sL = { pos:'Positive', neg:'Negative', neu:'Neutral' }[sent];
    const enc = encodeURIComponent(JSON.stringify(item));
    return `<div class="tme-post" data-item="${esc(enc)}">
        <div class="tme-post-rank tme-post-rank${rkC}">${rank}</div>
        <div class="tme-post-av" style="background:linear-gradient(135deg,${color},${color}99);">${avH}</div>
        <div class="tme-post-body">
            <div class="tme-post-author">${esc(name)}${scr ? ` <span style="color:var(--slate-400);font-weight:500;">@${esc(scr)}</span>` : ''}</div>
            ${dt ? `<div class="tme-post-date">${dt}</div>` : ''}
            ${content ? `<div class="tme-post-text">${esc(content)}</div>` : ''}
            <div class="tme-post-stats">
                <span class="tme-metric tme-metric--primary"><i class="ph ph-repeat me-1"></i>${numF(rt)} RT</span>
                <span class="tme-sent tme-sent--${sent}">${sL}</span>
                ${url ? `<a href="${esc(url)}" target="_blank" rel="noopener" class="tme-view-link" onclick="event.stopPropagation()"><i class="ph ph-arrow-square-out me-1"></i>Lihat</a>` : ''}
            </div>
        </div>
    </div>`;
}

function pagHtml(page, pages, total, from, to) {
    if (pages <= 1) return '';
    let b = '', r = 2;
    b += `<button class="tme-pag-btn" ${page <= 1 ? 'disabled' : ''} onclick="goPage(${page - 1})"><i class="ph ph-caret-left"></i></button>`;
    for (let i = 1; i <= pages; i++) {
        if (i === 1 || i === pages || (i >= page - r && i <= page + r))
            b += `<button class="tme-pag-btn${i === page ? ' is-active' : ''}" onclick="goPage(${i})">${i}</button>`;
        else if (i === page - r - 1 || i === page + r + 1)
            b += `<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`;
    }
    b += `<button class="tme-pag-btn" ${page >= pages ? 'disabled' : ''} onclick="goPage(${page + 1})"><i class="ph ph-caret-right"></i></button>`;
    return `<div class="tme-pagination">
        <span class="tme-pag-info">Menampilkan ${from}–${to} dari ${total} tweets</span>
        <div class="tme-pag-controls">${b}</div>
    </div>`;
}

function goPage(p) {
    curPage = p;
    renderList();
    _$('tweetList')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* ══ DONUT CHART ══ */
function renderDonut() {
    const ld = _$('loadingDonut'), ch = _$('donutChart'), em = _$('donutEmpty'), lg = _$('donutLegend');
    if (!Store.length) {
        if (ld) ld.style.display = 'none';
        if (em) em.style.display = 'flex';
        return;
    }
    const top5  = Store.slice(0, 5);
    const total = top5.reduce((s, d) => s + parseInt(d.freq || 0), 0);
    if (lg) lg.innerHTML = top5.map((d, i) => {
        const n = _getName(d), sn = n.length > 20 ? n.slice(0, 19) + '…' : n;
        return `<div class="donut-leg-item"><span class="donut-dot" style="background:${DONUT_COLORS[i]};"></span>${sn} · ${numF(parseInt(d.freq || 0))}</div>`;
    }).join('');
    if (ld) ld.style.display = 'none';
    ch.style.display = 'block';
    if (typeof echarts === 'undefined') {
        ch.innerHTML = '<div class="chart-empty"><i class="ph ph-chart-donut"></i><span>ECharts not loaded</span></div>';
        return;
    }
    const chart = echarts.init(ch, null, { renderer: 'canvas' });
    window.addEventListener('resize', () => { try { chart.resize(); } catch(e) {} });
    const pd = top5.map((d, i) => ({
        name: _getName(d),
        value: parseInt(d.freq || 0),
        itemStyle: { color: DONUT_COLORS[i] }
    }));
    chart.setOption({
        backgroundColor: 'transparent',
        animation: true,
        animationDuration: 1000,
        animationEasing: 'cubicOut',
        tooltip: { show: false },
        series: [{
            type: 'pie',
            radius: ['38%', '62%'],
            center: ['50%', '50%'],
            avoidLabelOverlap: true,
            selectedMode: false,
            minAngle: 8,
            itemStyle: { borderColor: '#fff', borderWidth: 3 },
            label: {
                show: true,
                position: 'outside',
                alignTo: 'edge',
                edgeDistance: 15,
                lineHeight: 16,
                fontSize: 11,
                fontFamily: 'inherit',
                color: '#334155',
                fontWeight: '500',
                formatter: p => `{title|${p.name}}\n{val|${numF(p.value)}} RT ({pct|${p.percent.toFixed(1)}%})`,
                rich: {
                    title: { fontSize: 11, fontWeight: '700', color: '#1e293b', lineHeight: 16 },
                    val:   { fontSize: 11, fontWeight: '700', color: '#038047' },
                    pct:   { fontSize: 11, fontWeight: '600', color: '#64748b' }
                }
            },
            labelLine: { show: true, length: 15, length2: 20, smooth: 0.3, lineStyle: { width: 1.5, color: '#94A3B8' } },
            emphasis: { scale: false, itemStyle: { shadowBlur: 0, borderWidth: 3, borderColor: '#fff' }, label: { show: true } },
            data: pd
        }],
        graphic: [
            { type: 'text', left: 'center', top: '46%', z: 100, style: { text: numK(total), fill: '#0f172a', font: "800 24px inherit", textAlign: 'center' } },
            { type: 'text', left: 'center', top: '55%', z: 100, style: { text: 'TOTAL RT',  fill: '#94a3b8', font: "600 9px inherit",  textAlign: 'center' } }
        ]
    });
    chart.on('click', p => {
        const d = top5[p.dataIndex];
        if (d) { Panel.open(Store, 'X — Most Retweets'); Detail.open(d); }
    });

    /* Custom tooltip */
    let _tt = document.getElementById('rtCustomTT');
    if (!_tt) {
        _tt = document.createElement('div');
        _tt.id = 'rtCustomTT';
        _tt.style.cssText = 'position:fixed;z-index:9999;pointer-events:none;background:#1e293b;color:#fff;border:1px solid #334155;border-radius:6px;padding:10px 14px;max-width:280px;font-size:12px;line-height:1.5;display:none;box-shadow:0 8px 24px rgba(0,0,0,.32);font-family:inherit;opacity:0;transform:translateY(6px) scale(.97);transition:opacity .18s ease,transform .18s ease;';
        document.body.appendChild(_tt);
    }
    let _tm = null;
    chart.on('mouseover', p => {
        if (p.componentType !== 'series') return;
        const d = top5[p.dataIndex], cl = DONUT_COLORS[p.dataIndex];
        const tx = dec((d.content || '').replace(/<[^>]*>/g, '').trim()).slice(0, 120);
        clearTimeout(_tm);
        _tt.innerHTML = `<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;"><span style="width:9px;height:9px;border-radius:50%;background:${cl};flex-shrink:0;display:inline-block;"></span><b style="font-size:12.5px;">${esc(p.name)}</b></div>${tx ? `<div style="font-size:11px;color:#94a3b8;margin-bottom:6px;">${esc(tx)}</div>` : ''}<div style="display:flex;align-items:center;gap:8px;"><b style="font-size:13px;">${numF(p.value)} RT</b><span style="color:${cl};font-weight:700;">${p.percent.toFixed(1)}%</span></div>`;
        _tt.style.display = 'block';
        requestAnimationFrame(() => { _tt.style.opacity = '1'; _tt.style.transform = 'translateY(0) scale(1)'; });
    });
    chart.on('mouseout', () => {
        _tt.style.opacity = '0'; _tt.style.transform = 'translateY(6px) scale(.97)';
        _tm = setTimeout(() => { _tt.style.display = 'none'; }, 180);
    });
    ch.addEventListener('mousemove', e => {
        if (_tt.style.display === 'none') return;
        const vw = window.innerWidth, vh = window.innerHeight, tw = _tt.offsetWidth + 16, th = _tt.offsetHeight + 16;
        let x = e.clientX + 18, y = e.clientY - 10;
        if (x + tw > vw) x = e.clientX - tw;
        if (y + th > vh) y = e.clientY - th;
        _tt.style.left = x + 'px'; _tt.style.top = y + 'px';
    });
}

/* ══ SLIDE PANEL ══ */
const Panel = {
    _items: [],
    open(items, title) {
        this._items = items || [];
        Detail.close();
        _$('panelTitle').textContent = title || 'X Tweets';
        _$('panelMeta').textContent  = CFG.sd + ' – ' + CFG.ed;
        const ov = _$('panelOverlay'), pn = _$('sntPanel');
        ov.classList.remove('hiding'); pn.classList.remove('hiding');
        ov.classList.add('show'); pn.classList.add('show');
        this._render(items);
    },
    close() {
        Detail.close();
        const ov = _$('panelOverlay'), pn = _$('sntPanel');
        pn.classList.add('hiding'); ov.classList.add('hiding');
        setTimeout(() => { pn.classList.remove('show','hiding'); ov.classList.remove('show','hiding'); }, 240);
    },
    _render(items) {
        const list = _$('panelList');
        if (!list) return;
        if (!items?.length) { list.innerHTML = '<div class="do-panel-loading"><span>Tidak ada data</span></div>'; return; }
        const d = '/assets/images/user/dummy.jpg';
        list.innerHTML = items.slice(0, 100).map(item => {
            const nm = _getName(item), cl = _getColor(item), aH = _avHtml(item);
            const tx = (item.content || '').replace(/<[^>]*>/g, '').trim();
            const rt = parseInt(item.freq || 0), dt = (item.date_created || '').split('T')[0];
            const sn = _normSent(item), sl = { pos:'Pos', neg:'Neg', neu:'Neu' }[sn];
            const enc = encodeURIComponent(JSON.stringify(item));
            return `<div class="do-panel-item" data-item="${esc(enc)}" onclick="Panel._click(this)">
                <div class="do-panel-avatar" style="background:linear-gradient(135deg,${cl},${cl}99);">${aH}</div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author">${esc(nm)}</div>
                    <div class="do-panel-text">${esc(dec(tx).slice(0, 130) || '(no content)')}</div>
                    <div class="do-panel-footer">
                        <span class="do-sent-badge do-sent-badge--${sn}">${sl}</span>
                        <span>${numF(rt)} RT</span>
                        ${dt ? `<span style="margin-left:auto;">${dt}</span>` : ''}
                    </div>
                </div>
            </div>`;
        }).join('');
    },
    _click(el) {
        try {
            const item = JSON.parse(decodeURIComponent(el.dataset.item.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"')));
            Detail.open(item);
        } catch(e) { console.warn(e); }
    }
};

/* ══ DETAIL PANEL ══ */
const Detail = {
    open(item) {
        const panel = _$('detailPanel'), body = _$('detailBody'), title = _$('detailTitle');
        if (!panel || !body) return;
        const name = _getName(item), scr = _getScr(item), ac = _getColor(item);
        const aH   = _avHtml(item);
        const raw  = (item.content || '').replace(/<[^>]*>/g, '').trim();
        const ct   = raw ? dec(raw) : '';
        const url  = _tweetUrl(item), dt = item.date_created || '';
        const rt   = parseInt(item.freq || 0), sf = parseInt(item.sentiment_freq || 0);
        const sn   = _normSent(item), sl = { pos:'Positif', neg:'Negatif', neu:'Netral' }[sn];
        let dtF = '';
        if (dt) try { dtF = new Date(dt).toLocaleDateString('id-ID', { weekday:'long', day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit' }); } catch(e) { dtF = dt.split('T')[0]; }
        title.textContent = name;
        body.innerHTML = `
            <div class="do-dp2-avatar-row">
                <div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${ac},${ac}99);">${aH}</div>
                <div>
                    <div class="do-dp2-name">${esc(name)}</div>
                    ${scr ? `<div class="do-dp2-handle">@${esc(scr)}</div>` : ''}
                    <span class="do-dp2-plat-badge" style="background:#03804718;color:#038047;">X / Twitter</span>
                </div>
            </div>
            ${dtF ? `<div class="do-dp2-meta">${dtF}</div>` : ''}
            <div class="do-dp2-sent do-dp2-sent--${sn}">${sl}</div>
            ${ct ? `<div class="do-dp2-content">${esc(ct)}</div>` : ''}
            <div class="do-dp2-stats">
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(rt)}</div><div class="do-dp2-stat-lbl">Retweets</div></div>
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(sf)}</div><div class="do-dp2-stat-lbl">Sentiment</div></div>
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${({pos:'😊',neg:'😡',neu:'😐'})[sn]}</div><div class="do-dp2-stat-lbl">Mood</div></div>
            </div>
            ${url ? `<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out me-1"></i>Buka di X</a>` : ''}`;
        panel.classList.add('show');
    },
    close() { _$('detailPanel')?.classList.remove('show'); }
};

/* ══════════════════════════════════════════════════════
   EXPORT MODULE — X Most Retweets v2 (sync dengan X Overview)
   Fix: ECharts pre-snapshot via getInstanceByDom, _onClone, freeze
   Note: halaman ini tidak punya tabs, PDF = single capture saja
══════════════════════════════════════════════════════ */
const XExport = (() => {
    let _toastTimer = null;

    function _toast(msg, type = 'default', duration = 3200) {
        const t = _$('exportToast'), m = _$('exportToastMsg'), ico = _$('exportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className   = 'export-toast show ' + (type !== 'default' ? type : '');
        const icons   = { success: 'ph-check-circle', error: 'ph-x-circle', default: 'ph-spinner' };
        ico.className = 'ph ' + (icons[type] || icons.default);
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => t.classList.remove('show'), duration);
    }

    function _btnState(btn, loading) {
        if (!btn) return;
        btn.disabled = loading;
        btn.classList.toggle('exporting', loading);
    }
    function _freeze() {
        if(document.getElementById('__s_freeze')) return;
        const s = document.createElement('style'); s.id = '__s_freeze';
        s.textContent = '*,*::before,*::after{animation:none!important;transition:none!important;animation-play-state:paused!important;}';
        document.head.appendChild(s);
    }
    function _unfreeze() { document.getElementById('__s_freeze')?.remove(); }
     

    /* ── Snapshot ECharts donut → dataURL ── */
    async function _getDonutSnapshot() {
        const donutEl = _$('donutChart');
        if (!donutEl || typeof echarts === 'undefined') return null;
        try {
            const inst = echarts.getInstanceByDom(donutEl);
            if (!inst || inst.isDisposed()) return null;
            inst.setOption({ animation: false });
            inst.resize();
            await new Promise(r => setTimeout(r, 600));
            return inst.getDataURL({ type: 'png', pixelRatio: 2, backgroundColor: '#ffffff' });
        } catch(e) {
            console.warn('[XExport] donut snapshot failed', e);
            return null;
        }
    }

    /* ── onclone: replace ECharts canvas dengan img snapshot ── */
    function _makeOnClone(donutSnapshot) {
        return (clonedDoc) => {
            /* Inject freeze style */
            const s = clonedDoc.createElement('style');
            s.textContent = `
                *, *::before, *::after { animation: none !important; transition: none !important; }
                .fade-up, .fade-up-d1, .fade-up-d2, .fade-up-d3,
                .fade-up-d4 { opacity: 1 !important; transform: none !important; }
                [data-html2canvas-ignore] { display: none !important; }
                .do-panel-overlay, .do-panel, #panelOverlay, #sntPanel { display: none !important; }
                .chart-loading { display: none !important; }
                .sk-block { animation: none !important; background: #e2e8f0 !important; }
            `;
            clonedDoc.head.appendChild(s);

            /* Sembunyikan elemen non-export */
            clonedDoc.querySelectorAll(
                '.do-panel-overlay,.do-panel,.export-toast,.chart-loading,.spin-ring'
            ).forEach(el => { el.style.display = 'none'; el.style.visibility = 'hidden'; });

            /* Force semua card/row visible */
            clonedDoc.querySelectorAll(
                '.card,.tme-post,[class*="col-"],.row,#pageExportArea,.kpi-card-hover'
            ).forEach(el => {
                el.style.opacity = '1';
                el.style.transform = 'none';
                el.style.visibility = 'visible';
                el.style.animation = 'none';
                el.style.transition = 'none';
            });

            /* KUNCI: Ganti ECharts donut canvas dengan img */
            const donutDiv = clonedDoc.getElementById('donutChart');
            if (donutDiv) {
                donutDiv.innerHTML = '';
                donutDiv.style.cssText = 'display:block!important;width:100%;height:340px;';
                if (donutSnapshot) {
                    const img = clonedDoc.createElement('img');
                    img.src = donutSnapshot;
                    img.style.cssText = 'width:100%;height:100%;object-fit:contain;display:block;';
                    donutDiv.appendChild(img);
                }
            }
        };
    }

    /* ── Core capture ── */
    async function _capture(areaId, bgColor) {
        const area = document.getElementById(areaId);
        if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');

        /* 1. Scroll ke atas */
        window.scrollTo({ top: 0 });

        /* 2. Snapshot donut SEBELUM freeze */
        const donutSnapshot = await _getDonutSnapshot();

        /* 3. Force visible semua fade-up di live DOM */
        area.querySelectorAll('.fade-up,.fade-up-d1,.fade-up-d2,.fade-up-d3,.fade-up-d4,.tme-post,.kpi-card-hover').forEach(e => {
            e.style.opacity = '1';
            e.style.transform = 'none';
            e.style.visibility = 'visible';
        });

        /* 4. Tunggu repaint */
        await new Promise(r => setTimeout(r, 400));

        let canvas;
        try {
            canvas = await html2canvas(area, {
                scale:           2,
                useCORS:         true,
                allowTaint:      false,
                backgroundColor: bgColor || '#f1f5f9',
                logging:         false,
                removeContainer: true,
                scrollX:         0,
                scrollY:         0,
                width:           area.offsetWidth,
                height:          area.scrollHeight,
                onclone:         _makeOnClone(donutSnapshot),
            });
        } finally {
            /* Restore ECharts animation */
            const donutEl = _$('donutChart');
            if (donutEl && typeof echarts !== 'undefined') {
                try {
                    const inst = echarts.getInstanceByDom(donutEl);
                    if (inst && !inst.isDisposed()) inst.setOption({ animation: true });
                } catch(e) {}
            }
        }
        return canvas;
    }

    function _drawPdfHeader(pdf, pW, margin, label) {
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + (label || 'X Most Retweets'), margin, 7.5);
        const now = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - margin, 7.5, { align: 'right' });
    }

    function _paginateCanvasToPdf(pdf, canvas, pW, pH, margin, headerH) {
        const imgW = canvas.width, imgH = canvas.height;
        const usableW = pW - margin * 2;
        const usableH = pH - margin * 2 - headerH;
        const ratio   = usableW / imgW;
        const sliceH  = usableH / ratio;
        let srcY = 0, pageNum = 0;
        while (srcY < imgH) {
            if (pageNum > 0) { pdf.addPage(); _drawPdfHeader(pdf, pW, margin, ''); }
            const srcSlice = Math.min(sliceH, imgH - srcY);
            const dstH     = srcSlice * ratio;
            const slice    = document.createElement('canvas');
            slice.width = imgW; slice.height = Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(canvas, 0, srcY, imgW, srcSlice, 0, 0, imgW, srcSlice);
            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, headerH + 3, usableW, dstH);
            pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
            pdf.text('Halaman ' + (pageNum + 1), pW / 2, pH - 3, { align: 'center' });
            srcY += srcSlice; pageNum++;
        }
    }

    const _cardLabels = {
        donut:     'Top 5 Most Retweeted',
        tweetlist: 'Ranked Tweets by Retweets',
    };

    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia',       'error'); return; }

        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);

        try {
            const canvas = await _capture(areaId, '#ffffff');
            const stamp  = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            const fname  = `x_${cardKey}_${CFG.pid}_${stamp}`;

            if (type === 'image') {
                const link = document.createElement('a');
                link.download = fname + '.png';
                link.href     = canvas.toDataURL('image/png');
                link.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const landscape = canvas.width > canvas.height;
                const pdf  = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit: 'mm', format: 'a4' });
                const pW   = pdf.internal.pageSize.getWidth();
                const pH   = pdf.internal.pageSize.getHeight();
                _drawPdfHeader(pdf, pW, 10, _cardLabels[cardKey] || cardKey);
                _paginateCanvasToPdf(pdf, canvas, pW, pH, 10, 11);
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[XExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btn, false);
        }
    }

    async function run(type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia',       'error'); return; }

        const btnPdf = _$('pageExportPdfBtn');
        const btnImg = _$('pageExportImgBtn');
        _btnState(btnPdf, true); _btnState(btnImg, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);

        try {
            const canvas = await _capture('pageExportArea', '#f1f5f9');
            const stamp  = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            if (type === 'image') {
                const link = document.createElement('a');
                link.download = `x_most_retweets_${CFG.pid}_${stamp}.png`;
                link.href     = canvas.toDataURL('image/png');
                link.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf  = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pW   = pdf.internal.pageSize.getWidth();
                const pH   = pdf.internal.pageSize.getHeight();
                _drawPdfHeader(pdf, pW, 10, 'X Most Retweets');
                _paginateCanvasToPdf(pdf, canvas, pW, pH, 10, 11);
                pdf.save(`x_most_retweets_${CFG.pid}_${stamp}.pdf`);
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[XExport]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btnPdf, false); _btnState(btnImg, false);
        }
    }

    return { run, runCard };
})();

/* ══ INIT ══ */
document.addEventListener('DOMContentLoaded', () => {
    loadData();
    document.addEventListener('keydown', e => { if (e.key === 'Escape') Panel.close(); });
});
</script>
@endsection