@extends('mk.layouts.app')

@section('title', 'X Top Influencers - SMADIMENT')

@section('styles')
<style>
:root{--primary:#038047;--primary-rgb:3,128,71;--primary-lt:rgba(3,128,71,.10);--dark:#273B4A;--white:#FFFFFF;--bg:#F1F5F8;--green:#038047;--slate-50:#F8FAFC;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-300:#CBD5E1;--slate-400:#94A3B8;--slate-500:#64748B;--slate-600:#475569;--slate-700:#334155;--slate-800:#1E293B;--slate-900:#0F172A;--radius:8px;--radius-sm:5px;--shadow-sm:0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);--shadow-md:0 4px 14px rgba(15,23,42,.08);--shadow-lg:0 10px 30px rgba(15,23,42,.12)}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}@keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}@keyframes spin{to{transform:rotate(360deg)}}@keyframes slideInRight{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}@keyframes slideOutRight{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}@keyframes overlayIn{from{opacity:0}to{opacity:1}}@keyframes overlayOut{from{opacity:1}to{opacity:0}}@keyframes kpiIconBounce{0%,100%{transform:scale(1) rotate(0)}30%{transform:scale(1.25) rotate(-10deg)}60%{transform:scale(1.1) rotate(6deg)}}@keyframes kpiShimmer{0%{left:-100%}100%{left:150%}}
.kpi-icon-bg{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0}
.sk-block{border-radius:4px;background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
.spin-ring{width:26px;height:26px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
.spinner-state{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;gap:12px;color:var(--slate-400);font-size:12px;font-weight:600}
.kpi-card-hover{will-change:transform,box-shadow;cursor:default;position:relative!important;overflow:hidden!important;transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important}.kpi-card-hover::before{content:'';position:absolute;top:0;bottom:0;left:-100%;width:60%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);pointer-events:none;z-index:1}.kpi-card-hover:hover{transform:translateY(-6px) scale(1.025)!important;box-shadow:0 20px 40px rgba(0,0,0,.25)!important;filter:brightness(1.07)!important}.kpi-card-hover:hover::before{animation:kpiShimmer .6s ease forwards}.kpi-card-hover:hover .kpi-icon-bg{background:rgba(255,255,255,.35)!important}.kpi-card-hover:hover .kpi-icon-bg i{animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important;display:inline-block!important}.kpi-card-hover:active{transform:translateY(-2px) scale(1.01)!important;transition-duration:.08s!important}
.chart-empty{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600}.chart-empty i{font-size:34px;color:var(--slate-300);display:block}
.sent-tabs{display:flex;gap:2px;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px}.sent-tab{flex:0 0 auto;display:flex;align-items:center;justify-content:center;gap:5px;padding:6px 14px;border-radius:4px;border:none;background:transparent;font-size:12px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:background .13s,color .13s;white-space:nowrap}.sent-tab:hover{background:#fff;color:var(--slate-800)}.sent-tab.active{background:#fff;color:var(--primary);box-shadow:0 1px 4px rgba(0,0,0,.08)}
.donut-legend{display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:10px}.donut-leg-item{display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--slate-500);padding:3px 8px;background:var(--slate-50);border-radius:3px;border:1px solid var(--slate-200);cursor:pointer;transition:border-color .12s,background .12s,color .12s}.donut-leg-item:hover{border-color:var(--primary);background:var(--primary-lt);color:var(--primary)}.donut-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.ht-list{display:flex;flex-direction:column}.ht-item{display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid var(--slate-100);cursor:pointer;transition:background .12s}.ht-item:last-child{border-bottom:none}.ht-item:hover{background:var(--slate-50)}.ht-rank{width:24px;height:24px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:var(--slate-400);background:var(--slate-100);border:1px solid var(--slate-200)}.ht-rank--1{background:linear-gradient(135deg,#ffd700,#F59E0B);color:#7c5900;border-color:#ffd700}.ht-rank--2{background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0}.ht-rank--3{background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32}
.ht-av{width:32px;height:32px;border-radius:50%;flex-shrink:0;overflow:hidden;border:1.5px solid var(--slate-200)}.ht-av img{width:100%;height:100%;object-fit:cover;border-radius:50%}.ht-info{flex:1;min-width:0}.ht-name{font-size:13px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ht-handle{font-size:10.5px;color:var(--slate-400);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ht-bar-wrap{flex:0 0 60px;height:6px;background:var(--slate-100);border-radius:99px;overflow:hidden}.ht-bar-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--primary),rgba(3,128,71,.5));transition:width .4s cubic-bezier(.4,0,.2,1)}.ht-count{font-size:11px;font-weight:700;color:var(--slate-500);white-space:nowrap;flex-shrink:0;min-width:36px;text-align:right}
.tme-pagination{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--slate-100);flex-wrap:wrap;gap:8px}.tme-pag-info{font-size:11px;color:var(--slate-400);font-weight:500}.tme-pag-controls{display:flex;align-items:center;gap:3px}.tme-pag-btn{min-width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;padding:0 6px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .12s;user-select:none}.tme-pag-btn:hover:not(:disabled):not(.is-active){border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}.tme-pag-btn.is-active{background:var(--primary);border-color:var(--primary);color:#fff}.tme-pag-btn:disabled{opacity:.35;cursor:not-allowed}
.do-panel-overlay{position:fixed;inset:0;z-index:9000;background:rgba(15,23,42,.45);backdrop-filter:blur(4px);display:none}.do-panel-overlay.show{display:block;animation:overlayIn .22s ease-out}.do-panel-overlay.hiding{animation:overlayOut .22s ease-out forwards}.do-panel{position:fixed;top:0;right:0;bottom:0;z-index:9001;width:520px;max-width:100vw;background:#fff;display:none;flex-direction:column;border-left:1px solid var(--slate-200);box-shadow:-8px 0 40px rgba(15,23,42,.16)}.do-panel.show{display:flex;animation:slideInRight .28s cubic-bezier(.4,0,.2,1)}.do-panel.hiding{animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards}
.do-panel-header{display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid var(--slate-200);background:var(--slate-50);flex-shrink:0}.do-panel-title{font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.do-panel-close{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);font-size:16px;transition:all .14s;flex-shrink:0}.do-panel-close:hover{background:#ef4444;border-color:#ef4444;color:#fff}
.up-banner{height:80px;background:linear-gradient(135deg,#0d3d2e,#038047,#06b6d4);position:relative;flex-shrink:0}.up-banner::after{content:'';position:absolute;inset:0;background:repeating-linear-gradient(-55deg,transparent,transparent 28px,rgba(255,255,255,.03) 28px,rgba(255,255,255,.03) 29px)}
.up-profile{padding:0 20px 16px;margin-top:-28px;position:relative;z-index:2;border-bottom:1px solid var(--slate-200)}.up-av{width:56px;height:56px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,.15);display:block;background:var(--slate-100)}.up-av-fb{width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:var(--primary);background:var(--primary-lt);border:3px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,.15)}
.up-name{font-size:16px;font-weight:700;color:var(--slate-900);margin-top:8px;display:flex;align-items:center;gap:5px}.up-handle{font-size:12px;color:var(--slate-400);margin-top:2px}.up-handle a{color:var(--slate-400);text-decoration:none}.up-handle a:hover{color:#1d9bf0;text-decoration:underline}
.up-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:0;margin-top:12px;border:1px solid var(--slate-200);border-radius:var(--radius);overflow:hidden}.up-stat{padding:10px 8px;text-align:center;border-right:1px solid var(--slate-200)}.up-stat:last-child{border-right:none}.up-stat-val{font-size:16px;font-weight:700;color:var(--slate-900)}.up-stat-lbl{font-size:9px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.4px;margin-top:1px}
.up-total{display:flex;align-items:center;justify-content:space-between;margin-top:10px;padding:10px 14px;background:var(--primary-lt);border-radius:var(--radius);border:1px solid rgba(3,128,71,.15)}.up-total-lbl{font-size:12px;font-weight:600;color:var(--primary);display:flex;align-items:center;gap:5px}.up-total-val{font-size:18px;font-weight:800;color:var(--primary)}
.up-mentions-head{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--slate-200);background:var(--slate-50);position:sticky;top:0;z-index:3}.up-mentions-head h6{font-size:13px;font-weight:700;color:var(--slate-900);margin:0}.up-mention-cnt{font-size:11px;font-weight:600;color:var(--slate-400);background:var(--slate-100);padding:3px 9px;border-radius:99px}
.up-body{overflow-y:auto;flex:1;min-height:0}.up-body::-webkit-scrollbar{width:4px}.up-body::-webkit-scrollbar-thumb{background:var(--slate-200);border-radius:99px}
.up-tweet{padding:12px 16px;border-bottom:1px solid var(--slate-100);cursor:pointer;transition:background .12s}.up-tweet:hover{background:var(--slate-50)}.up-tweet:last-child{border-bottom:none}
.up-tw-head{display:flex;align-items:center;gap:8px;margin-bottom:6px}.up-tw-author{font-size:12px;font-weight:700;color:var(--slate-900)}.up-tw-time{font-size:10px;color:var(--slate-400);margin-left:auto}
.up-tw-text{font-size:12px;color:var(--slate-600);line-height:1.6;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word}
.up-tw-foot{display:flex;align-items:center;gap:10px;font-size:10px;color:var(--slate-400)}.up-tw-metric{display:flex;align-items:center;gap:3px}.up-tw-sent{padding:2px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase}.up-tw-sent--pos{background:#d1fae5;color:#065f46}.up-tw-sent--neg{background:#fee2e2;color:#991b1b}.up-tw-sent--neu{background:var(--slate-100);color:var(--slate-500)}
.up-tw-link{display:inline-flex;align-items:center;gap:3px;font-size:9.5px;font-weight:700;color:var(--primary);text-decoration:none;padding:2px 6px;border-radius:3px;background:var(--primary-lt);border:1px solid rgba(3,128,71,.2);transition:background .12s,color .12s;margin-left:auto}.up-tw-link:hover{background:var(--primary);color:#fff}
.up-pag{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--slate-200);background:var(--slate-50);flex-wrap:wrap;gap:6px;flex-shrink:0}.up-pag-info{font-size:10px;color:var(--slate-400)}.up-pag-btns{display:flex;gap:3px}.up-pag-btn{min-width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;padding:0 5px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;font-size:10px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .12s}.up-pag-btn:hover:not(:disabled):not(.is-active){border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}.up-pag-btn.is-active{background:var(--primary);border-color:var(--primary);color:#fff}.up-pag-btn:disabled{opacity:.35;cursor:not-allowed}
.up-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;gap:12px;color:var(--slate-400);font-size:12px}
.up-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;padding:40px 20px;gap:8px;color:var(--slate-400)}.up-empty i{font-size:32px;color:var(--slate-300)}
.do-detail-panel{position:absolute;inset:0;background:#fff;z-index:5;display:none;flex-direction:column;animation:slideInRight .2s cubic-bezier(.4,0,.2,1)}.do-detail-panel.show{display:flex}.do-dp2-header{display:flex;align-items:center;gap:8px;padding:12px 14px;background:var(--slate-50);border-bottom:1px solid var(--slate-200);flex-shrink:0}.do-dp2-back{width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);transition:all .13s;font-size:14px}.do-dp2-back:hover{background:var(--primary-lt);color:var(--primary);border-color:var(--primary)}.do-dp2-title{font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.do-dp2-body{overflow-y:auto;flex:1;padding:16px}.do-dp2-body::-webkit-scrollbar{width:4px}.do-dp2-body::-webkit-scrollbar-thumb{background:var(--slate-200);border-radius:99px}
.do-dp2-meta{font-size:11px;color:var(--slate-400);font-weight:500;margin-bottom:10px}.do-dp2-sent{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:3px;font-size:11px;font-weight:700;margin-bottom:10px}.do-dp2-sent--pos{background:#d1fae5;color:#065f46}.do-dp2-sent--neg{background:#fee2e2;color:#991b1b}.do-dp2-sent--neu{background:var(--slate-100);color:var(--slate-500)}.do-dp2-content{font-size:12px;color:var(--slate-700);line-height:1.7;margin-bottom:12px;background:var(--slate-50);border-radius:var(--radius-sm);padding:10px 12px;border:1px solid var(--slate-200);word-break:break-word}.do-dp2-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:10px}.do-dp2-stat{background:var(--slate-50);border-radius:var(--radius-sm);padding:8px 10px;border:1px solid var(--slate-200);text-align:center}.do-dp2-stat-val{font-size:14px;font-weight:700;color:var(--slate-900)}.do-dp2-stat-lbl{font-size:9px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.4px;margin-top:1px}.do-dp2-link{display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:var(--primary);color:#fff;border-radius:var(--radius-sm);font-size:12px;font-weight:700;text-decoration:none;transition:filter .14s;margin-top:4px}.do-dp2-link:hover{filter:brightness(1.1);color:#fff}
.kpi-card-hover h3{font-size:1.5rem}
@media(max-width:640px){.do-panel{width:100vw}}

/* ══════════════════════════════════════════════════════
   EXPORT STYLES — identik dengan TikTok Most Engagement
══════════════════════════════════════════════════════ */

/* Page Export Bar */
.page-export-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);padding:9px 14px;margin-bottom:20px;box-shadow:var(--shadow-sm)}
.page-export-bar-left{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--slate-600)}
.page-export-bar-left i{font-size:15px;color:var(--primary)}
.page-export-bar-right{display:flex;gap:8px}

/* Page-level export buttons */
.page-export-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);font-size:16px;cursor:pointer;transition:all .15s ease;border:1.5px solid transparent;font-family:inherit}
.page-export-btn-pdf{background:#fff3f3;color:#dc2626;border-color:#fca5a5}
.page-export-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.page-export-btn-img{background:var(--primary-lt);color:var(--primary);border-color:rgba(3,128,71,.3)}
.page-export-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.page-export-btn:disabled{opacity:.55;cursor:not-allowed;pointer-events:none}
.page-export-btn .export-spinner{width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
.page-export-btn.exporting .export-spinner{display:inline-block}
.page-export-btn.exporting .export-icon{display:none}

/* Card-level export buttons */
.card-exp-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:var(--radius-sm);font-size:14px;cursor:pointer;flex-shrink:0;transition:all .14s ease;border:1px solid transparent;font-family:inherit;background:transparent}
.card-exp-btn-pdf{color:#dc2626;border-color:#fca5a5;background:#fff3f3}
.card-exp-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
.card-exp-btn-img{color:var(--primary);border-color:rgba(3,128,71,.3);background:var(--primary-lt)}
.card-exp-btn-img:hover{background:var(--primary);color:#fff;border-color:var(--primary)}
.card-exp-btn:disabled{opacity:.45;cursor:not-allowed;pointer-events:none}
.card-exp-btn .export-spinner{width:11px;height:11px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
.card-exp-btn.exporting .export-spinner{display:inline-block}
.card-exp-btn.exporting .export-icon{display:none}

/* Export Toast */
.export-toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--slate-900);color:#fff;border-radius:var(--radius);padding:10px 18px;font-size:12px;font-weight:600;box-shadow:var(--shadow-lg);z-index:99999;opacity:0;pointer-events:none;transition:opacity .22s ease,transform .22s ease;display:flex;align-items:center;gap:8px;white-space:nowrap}
.export-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.export-toast.success{background:#065f46}
.export-toast.error{background:#991b1b}
</style>
@endsection

@section('page-title', 'X Top Influencers')

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

{{-- Tab --}}
<div class="sent-tabs mb-3" id="subTabs">
    <button class="sent-tab active" data-s="rt"><i class="ph ph-chat-circle-dots me-1"></i> By Collected Mentions</button>
    <button class="sent-tab" data-s="rt_all"><i class="ph ph-repeat me-1"></i> By Total Retweets</button>
</div>

{{-- ════ PAGE EXPORT WRAPPER ════ --}}
<div id="pageExportArea">

{{-- KPI --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-primary text-white kpi-card-hover fade-up fade-up-d1">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Total Influencers</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiTotal"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTotalSub"><i class="ph ph-users me-1"></i>Loading…</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-users"></i></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-success text-white kpi-card-hover fade-up fade-up-d2">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12" id="kpiEngLabel">Total Engagements</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiEng"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiEngSub"><i class="ph ph-chart-bar me-1"></i>Loading…</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-warning text-white kpi-card-hover fade-up fade-up-d3">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Top Account</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiTopAcc" style="font-size:1rem;"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopAccSub"><i class="ph ph-crown me-1"></i>Loading…</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-crown"></i></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-danger text-white kpi-card-hover fade-up fade-up-d4">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Avg Followers</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiAvgFol"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiAvgFolSub"><i class="ph ph-trend-up me-1"></i>Loading…</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-trend-up"></i></div></div>
            </div></div>
        </div>
    </div>
</div>

{{-- ══ Page Export Toolbar ══ --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
        <i class="ph ph-export"></i>
        <span>Export Halaman</span>
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">
            KPI + Donut + Influencer List
        </span>
    </div>
    <div class="page-export-bar-right">
        <button type="button"
                class="page-export-btn page-export-btn-pdf"
                id="pageExportPdfBtn"
                onclick="XIExport.run('pdf', this)"
                title="Export halaman sebagai PDF">
            <i class="ph ph-file-pdf export-icon"></i>
            <span class="export-spinner"></span>
        </button>
        <button type="button"
                class="page-export-btn page-export-btn-img"
                id="pageExportImgBtn"
                onclick="XIExport.run('image', this)"
                title="Export halaman sebagai PNG">
            <i class="ph ph-image export-icon"></i>
            <span class="export-spinner"></span>
        </button>
    </div>
</div>

{{-- Donut --}}
<div class="row">
    <div class="col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-export-donut">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Top 5 Engagement Share</h6>
                            <small class="text-muted">Klik untuk lihat detail</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div id="donutLegend" class="donut-legend"></div>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf"
                                    onclick="XIExport.runCard('card-export-donut','donut','pdf',this)"
                                    title="Export PDF">
                                <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                            </button>
                            <button class="card-exp-btn card-exp-btn-img"
                                    onclick="XIExport.runCard('card-export-donut','donut','image',this)"
                                    title="Export PNG">
                                <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height:340px;">
                        <div class="chart-loading" id="loadingDonut" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:8px;">
                            <div class="spin-ring"></div><span style="font-size:11px;font-weight:600;color:var(--slate-400)">Loading…</span>
                        </div>
                        <div id="donutChart" style="width:100%;height:340px;display:none;"></div>
                        <div id="donutEmpty" style="display:none;" class="chart-empty"><i class="ph ph-chart-donut"></i><span>No data</span></div>
                    </div>
                </div>
            </div>{{-- /card-export-donut --}}
        </div>
    </div>
</div>

{{-- Influencer List --}}
<div class="row">
    <div class="col-12">
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
            <div id="card-export-list">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-star f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Influencer Ranking</h6>
                            <small class="text-muted">Klik user untuk lihat profil</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-primary text-primary" id="badgeTotal">Loading…</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf"
                                    onclick="XIExport.runCard('card-export-list','list','pdf',this)"
                                    title="Export PDF">
                                <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                            </button>
                            <button class="card-exp-btn card-exp-btn-img"
                                    onclick="XIExport.runCard('card-export-list','list','image',this)"
                                    title="Export PNG">
                                <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="userList" class="p-0"><div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div></div>
                <div id="pagArea"></div>
            </div>{{-- /card-export-list --}}
        </div>
    </div>
</div>

{{-- /pageExportArea --}}
</div>

{{-- ══ Export Toast ══ --}}
<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

{{-- Slide Panel --}}
<div class="do-panel-overlay" id="panelOverlay" onclick="Panel.close()"></div>
<div class="do-panel" id="sntPanel">
    <div class="do-panel-header">
        <span class="do-panel-title" id="panelTitle">Influencer Profile</span>
        <button class="do-panel-close" onclick="Panel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="up-body" id="panelBody"></div>
    <div class="do-detail-panel" id="detailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="Detail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="detailTitle">Tweet Detail</span>
            <button class="do-panel-close" onclick="Panel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="detailBody"></div>
    </div>
</div>

@endsection

@section('scripts')
{{-- ══ Export dependencies ══ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<script>
'use strict';
const CFG = { pid: OV_PID, sd: OV_SD, ed: OV_ED };
const DONUT_COLORS = ['#038047','#273B4A','#F59E0B','#06B6D4','#EF4444'];
const _$  = id => document.getElementById(id);
const numF = n => parseInt(n||0).toLocaleString('id-ID');
const numK = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc  = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const totalEng = u => curSub==='rt_all'
    ? parseInt(u.retweets||u.total||0)
    : parseInt(u.total||(parseInt(u.retweets||0)+parseInt(u.replies||0))||0);
const _getInit = n => { if(!n||n==='Unknown')return'?'; const p=n.trim().split(/\s+/); return p.length===1?p[0].substring(0,2).toUpperCase():(p[0][0]+p[p.length-1][0]).toUpperCase(); };
const _avUrl   = u => { const a=u.profile_image||u.profile_image_url||''; if(a)return a; const h=u.screen_name||u.username||''; return h?`https://unavatar.io/twitter/${h}`:''; };

let Store=[], curPage=1, curSub='rt';
const PP = 15;
let donutInst = null;

const TAB = {
    rt    : { engLabel:'Total RT + Reply', engSub:'RT + Reply count',    col:'RT + Reply' },
    rt_all: { engLabel:'Total Retweets',   engSub:'Total retweet count', col:'Total Retweets' },
};

/* ══ LOAD ══ */
async function loadData() {
    _$('userList').innerHTML = '<div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div>';
    _$('pagArea').innerHTML  = '';
    _$('loadingDonut').style.display = 'flex';
    _$('donutChart').style.display   = 'none';
    _$('donutEmpty').style.display   = 'none';
    try {
        const r    = await fetch(`/mk/api/x/top-influencers?project_id=${CFG.pid}&start_date=${CFG.sd}&end_date=${CFG.ed}&sub=${curSub}`);
        const j    = await r.json();
        const rows = j.data || [];
        if(!rows.length){ showEmpty(); return; }
        Store    = rows.sort((a,b)=>totalEng(b)-totalEng(a));
        curPage  = 1;
        updateKpi();
        renderDonut();
        renderList();
    } catch(e) { console.error(e); showEmpty(true); }
}

function showEmpty(isError) {
    _$('loadingDonut').style.display = 'none';
    _$('donutEmpty').style.display   = 'flex';
    _$('donutChart').style.display   = 'none';
    const msg = isError
        ? '<div class="spinner-state" style="padding:40px;"><i class="ph ph-warning" style="font-size:32px;color:var(--slate-300);"></i><span>Gagal memuat data. Server mungkin lambat.</span><button class="btn btn-sm btn-outline-primary mt-2" onclick="loadData()"><i class="ph ph-arrow-clockwise me-1"></i>Coba Lagi</button></div>'
        : '<div class="spinner-state" style="padding:40px;"><i class="ph ph-users" style="font-size:32px;color:var(--slate-300);"></i><span>Tidak ada data influencer</span></div>';
    _$('userList').innerHTML = msg;
    _$('pagArea').innerHTML  = '';
    ['kpiTotal','kpiEng','kpiAvgFol'].forEach(id=>{ const e=_$(id); if(e)e.textContent='0'; });
    _$('kpiTopAcc').textContent = '–';
    _$('badgeTotal').textContent = '0';
}

function updateKpi() {
    const cfg=TAB[curSub], n=Store.length, tot=Store.reduce((s,u)=>s+totalEng(u),0);
    _$('kpiTotal').textContent   = numF(n);
    _$('kpiTotalSub').innerHTML  = `<i class="ph ph-users me-1"></i>${n} accounts tracked`;
    _$('kpiEngLabel').textContent= cfg.engLabel;
    _$('kpiEng').textContent     = numF(tot);
    _$('kpiEngSub').innerHTML    = `<i class="ph ph-chart-bar me-1"></i>${cfg.engSub}`;
    if(n){
        const top=Store[0];
        _$('kpiTopAcc').textContent   = top.name||('@'+(top.screen_name||''));
        _$('kpiTopAccSub').innerHTML  = `<i class="ph ph-crown me-1"></i>${numF(totalEng(top))} engagements`;
        const avgF = Math.round(Store.reduce((s,u)=>s+parseInt(u.followers_count||u.author_followers_count||0),0)/n);
        _$('kpiAvgFol').textContent  = numK(avgF);
        _$('kpiAvgFolSub').innerHTML = `<i class="ph ph-trend-up me-1"></i>Per influencer`;
    }
    _$('badgeTotal').textContent = n+' influencers';
}

/* ══ DONUT ══ */
function renderDonut() {
    const ld=_$('loadingDonut'), ch=_$('donutChart'), em=_$('donutEmpty'), lg=_$('donutLegend');
    const top5 = Store.slice(0,5);
    if(!top5.length){ ld.style.display='none'; em.style.display='flex'; return; }
    ld.style.display='none'; ch.style.display='block';
    const data  = top5.map((u,i)=>({
        name: u.name||('@'+(u.screen_name||'')),
        value: totalEng(u),
        uname: u.screen_name||u.username||'',
        itemStyle: { color:DONUT_COLORS[i], borderColor:'#fff', borderWidth:3 }
    }));
    const total = data.reduce((s,d)=>s+d.value,0);
    if(donutInst){ try{ donutInst.dispose(); }catch(e){} }
    donutInst = echarts.init(ch, null, { renderer:'canvas' });
    donutInst.setOption({
        backgroundColor: 'transparent',
        animation: true, animationDuration:700,
        tooltip:{
            trigger:'item', backgroundColor:'#1e293b', borderColor:'#334155', borderWidth:1,
            padding:[10,14], textStyle:{color:'#f8fafc',fontSize:12},
            extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.2);',
            formatter: p => `<div style="font-weight:700;margin-bottom:4px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:16px"><span style="color:#94a3b8">Engagement</span><span style="font-weight:700">${numF(p.value)}</span></div>
                <div style="display:flex;justify-content:space-between;gap:16px;margin-top:2px"><span style="color:#94a3b8">Share</span><span style="font-weight:700;color:#038047">${total?(p.value/total*100).toFixed(1):'0'}%</span></div>`
        },
        series:[{
            type:'pie', radius:['46%','68%'], center:['50%','50%'],
            avoidLabelOverlap:true, minAngle:8,
            label:{
                show:true, position:'outside', fontFamily:'inherit', fontSize:11, color:'#475569',
               formatter: p => { const n=p.name.length>12?p.name.slice(0,11)+'…':p.name; const pct=total?(p.value/total*100).toFixed(1):'0'; return `{n|${n}}\n{v|${numK(p.value)}} {pct|${pct}%}`; },
rich:{
    n:{fontWeight:700,fontSize:11.5,color:'#1e293b',lineHeight:18},
    v:{fontWeight:700,fontSize:10.5,color:'#038047',lineHeight:16,backgroundColor:'rgba(3,128,71,.08)',borderRadius:3,padding:[1,4]},
    pct:{fontWeight:600,fontSize:10,color:'#64748b',lineHeight:16}
},
            },
            labelLine:{show:true,length:14,length2:18,smooth:.3,lineStyle:{color:'#c4cdd8',width:1.2}},
            emphasis:{scale:true,scaleSize:4,itemStyle:{shadowBlur:8,shadowColor:'rgba(0,0,0,.1)'}},
            data
        }],
        graphic:[
            {type:'text',left:'center',top:'46%',z:100,style:{text:numK(total),fill:'#0f172a',font:"700 24px inherit",textAlign:'center'}},
            {type:'text',left:'center',top:'54%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"600 9px inherit",textAlign:'center'}}
        ]
    });
    donutInst.on('click', p => {
        if(p.componentType==='series'){
            const u = top5.find(x=>(x.name||('@'+(x.screen_name||'')))===p.name);
            if(u) Panel.open(u);
        }
    });
    window.addEventListener('resize', ()=>{ try{ donutInst.resize(); }catch(e){} });
    lg.innerHTML = top5.map((u,i)=>`<span class="donut-leg-item" onclick="Panel.open(Store[${i}])"><span class="donut-dot" style="background:${DONUT_COLORS[i]}"></span>${esc((u.name||u.screen_name||'').substring(0,15))}</span>`).join('');
}

/* ══ USER LIST ══ */
function renderList() {
    const el=_$('userList'), pg=_$('pagArea');
    if(!Store.length){
        el.innerHTML='<div class="spinner-state" style="padding:40px;"><i class="ph ph-users" style="font-size:32px;color:var(--slate-300);"></i><span>Tidak ada data</span></div>';
        pg.innerHTML=''; return;
    }
    const total=Store.length, pages=Math.ceil(total/PP), start=(curPage-1)*PP;
    const items=Store.slice(start,start+PP), mx=Store[0]?totalEng(Store[0]):1;
    let h='<div class="ht-list">';
    items.forEach((u,i)=>{
        const rk=start+i+1, rc=rk<=3?` ht-rank--${rk}`:'';
        const pct = Math.round((totalEng(u)/mx)*100);
        const name=u.name||u.screen_name||'Unknown', uname=u.screen_name||u.username||'';
        const src=_avUrl(u), init=_getInit(name);
        const avH = src
            ? `<img src="${esc(src)}" onerror="this.src='/assets/images/user/dummy.jpg'" alt="">`
            : `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--primary-lt);color:var(--primary);font-weight:700;font-size:11px;">${esc(init)}</div>`;
        h+=`<div class="ht-item" onclick="Panel.open(Store[${start+i}])">
            <div class="ht-rank${rc}">${rk}</div>
            <div class="ht-av">${avH}</div>
            <div class="ht-info">
                <div class="ht-name">${esc(name)}</div>
                <div class="ht-handle">@${esc(uname)} · <span style="color:var(--slate-500)">${numF(u.followers_count||0)} followers</span></div>
            </div>
            <div class="ht-bar-wrap"><div class="ht-bar-fill" style="width:${pct}%;"></div></div>
            <div class="ht-count">${numF(totalEng(u))}</div>
        </div>`;
    });
    h+='</div>';
    el.innerHTML = h;
    if(pages<=1){ pg.innerHTML=''; return; }
    const fr=start+1, to=Math.min(start+PP,total);
    let b='', r=2;
    b+=`<button class="tme-pag-btn" ${curPage<=1?'disabled':''} onclick="goPage(${curPage-1})"><i class="ph ph-caret-left"></i></button>`;
    for(let i=1;i<=pages;i++){
        if(i===1||i===pages||(i>=curPage-r&&i<=curPage+r))
            b+=`<button class="tme-pag-btn${i===curPage?' is-active':''}" onclick="goPage(${i})">${i}</button>`;
        else if(i===curPage-r-1||i===curPage+r+1)
            b+=`<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`;
    }
    b+=`<button class="tme-pag-btn" ${curPage>=pages?'disabled':''} onclick="goPage(${curPage+1})"><i class="ph ph-caret-right"></i></button>`;
    pg.innerHTML=`<div class="tme-pagination"><span class="tme-pag-info">${fr}–${to} dari ${total}</span><div class="tme-pag-controls">${b}</div></div>`;
}
function goPage(p){ curPage=p; renderList(); _$('userList')?.scrollIntoView({behavior:'smooth',block:'nearest'}); }

/* ═══════════════════════════════════════════════════════════
   SLIDE PANEL
═══════════════════════════════════════════════════════════ */
const Panel = (() => {
    let _u=null, _mentions=[], _hasMore=false, _apiStart=0, _pg=1;
    const _PP=10;
    let _abort=null;

    function open(user) {
        _u=user; _mentions=[]; _hasMore=false; _apiStart=0; _pg=1;
        if(_abort) try{ _abort.abort(); }catch(e){}
        _abort = new AbortController();
        const name = user.name||user.screen_name||'Influencer';
        _$('panelTitle').textContent = name+' — Profile';
        _$('panelBody').innerHTML    = _profileHTML(user)+'<div id="upMentions"><div class="up-loading"><div class="spin-ring"></div><span>Memuat tweets…</span></div></div>';
        _$('panelOverlay').classList.remove('hiding'); _$('panelOverlay').classList.add('show');
        _$('sntPanel').classList.remove('hiding');     _$('sntPanel').classList.add('show');
        _$('detailPanel').classList.remove('show');
        _fetchMentions();
    }
    function close() {
        if(_abort) try{ _abort.abort(); }catch(e){}
        _abort=null;
        _$('panelOverlay').classList.add('hiding'); _$('sntPanel').classList.add('hiding');
        setTimeout(()=>{ _$('panelOverlay').classList.remove('show','hiding'); _$('sntPanel').classList.remove('show','hiding'); _$('panelBody').innerHTML=''; },260);
    }
    async function _fetchMentions() {
        try {
            const uname = _u.screen_name||_u.username||'';
            const url   = `/mk/api/x/user-detailed-mentions?project_id=${CFG.pid}&username=${encodeURIComponent(uname)}&start_date=${CFG.sd}&end_date=${CFG.ed}&api_start=${_apiStart}&stat_mentions=${_u.mentions||0}&stat_replies=${_u.replies||0}&stat_retweets=${_u.retweets||0}`;
            const r     = await fetch(url,{signal:_abort?.signal});
            if(!r.ok) throw new Error('HTTP '+r.status);
            const j = await r.json();
            if(!j.success){ _renderMentions(); return; }
            _mentions    = [..._mentions,...(j.data?.mentions||[])];
            _hasMore     = j.data?.has_more||false;
            _apiStart    = j.data?.next_api_start||0;
            _renderMentions();
        } catch(e) { if(e.name!=='AbortError'){ console.error(e); _renderMentions(); } }
    }
    function _profileHTML(u) {
        const name=u.name||u.screen_name||'Unknown', uname=u.screen_name||u.username||'', src=_avUrl(u), init=_getInit(name);
        const fol=parseInt(u.followers_count||u.author_followers_count||0), rt=parseInt(u.retweets||0), rep=parseInt(u.replies||0), tot=parseInt(u.total||0)||rt+rep;
        const avH=src
            ? `<img src="${esc(src)}" class="up-av" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" alt="${esc(name)}"><div class="up-av-fb" style="display:none;">${esc(init)}</div>`
            : `<div class="up-av-fb">${esc(init)}</div>`;
        return `<div class="up-banner"></div>
        <div class="up-profile">
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:10px">
                ${avH}
                <a href="https://twitter.com/${esc(uname)}" target="_blank" rel="noopener" style="font-size:11px;font-weight:700;color:#fff;background:#0f1419;padding:6px 14px;border-radius:99px;text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:4px">
                    <i class="ph ph-arrow-square-out" style="font-size:12px"></i>View on X
                </a>
            </div>
            <div class="up-name">${esc(name)}</div>
            <div class="up-handle"><a href="https://twitter.com/${esc(uname)}" target="_blank" rel="noopener">@${esc(uname)}</a> · <strong>${numF(fol)}</strong> Followers</div>
            <div class="up-stats">
                <div class="up-stat"><div class="up-stat-val">${numF(rep)}</div><div class="up-stat-lbl">Replies</div></div>
                <div class="up-stat"><div class="up-stat-val">${numF(fol)}</div><div class="up-stat-lbl">Followers</div></div>
                <div class="up-stat"><div class="up-stat-val">${numF(rt)}</div><div class="up-stat-lbl">Retweets</div></div>
            </div>
            <div class="up-total">
                <span class="up-total-lbl"><i class="ph ph-chart-bar"></i> Total Engagement</span>
                <span class="up-total-val">${numF(tot)}</span>
            </div>
        </div>`;
    }
    function _renderMentions() {
        const el=_$('upMentions'); if(!el) return;
        if(!_mentions.length&&!_hasMore){
            el.innerHTML='<div class="up-empty"><i class="ph ph-chat-circle-dots"></i><span style="font-size:12px">No mentions found</span></div>'; return;
        }
        const total=_mentions.length, pages=Math.ceil(total/_PP), si=(_pg-1)*_PP, ei=Math.min(si+_PP,total), page=_mentions.slice(si,ei);
        let h=`<div class="up-mentions-head"><h6><i class="ph ph-chat-circle-dots me-1"></i>Tweets & Mentions</h6><span class="up-mention-cnt">${_hasMore?total+' loaded · more':total+' found'}</span></div>`;
        page.forEach(m=>{ h+=_tweetCard(m); });
        if(total>_PP||_hasMore){
            const isLast=_pg>=pages, canLoad=isLast&&_hasMore;
            h+=`<div class="up-pag"><span class="up-pag-info">Page ${_pg}/${pages}${_hasMore?'+':''} · ${si+1}–${ei} of ${total}${_hasMore?'+':''}</span><div class="up-pag-btns">`;
            h+=`<button class="up-pag-btn" ${_pg<=1?'disabled':''} onclick="Panel.goPage(${_pg-1})"><i class="ph ph-caret-left"></i></button>`;
            for(let p=Math.max(1,_pg-2);p<=Math.min(pages,_pg+2);p++)
                h+=`<button class="up-pag-btn${p===_pg?' is-active':''}" onclick="Panel.goPage(${p})">${p}</button>`;
            if(canLoad)
                h+=`<button class="up-pag-btn" id="upLoadMore" onclick="Panel.loadMore()" style="padding:0 8px;background:var(--primary-lt);border-color:rgba(3,128,71,.2);color:var(--primary)">More <i class="ph ph-caret-right"></i></button>`;
            else
                h+=`<button class="up-pag-btn" ${_pg>=pages?'disabled':''} onclick="Panel.goPage(${_pg+1})"><i class="ph ph-caret-right"></i></button>`;
            h+=`</div></div>`;
        }
        el.innerHTML = h;
    }
    function _tweetCard(m) {
        const sent=(m.sentiment||'neutral').toLowerCase(), sentCls=sent.includes('pos')?'pos':sent.includes('neg')?'neg':'neu';
        const sentLbl=sent.includes('pos')?'Positive':sent.includes('neg')?'Negative':'Neutral';
        const ts=m.timestamp||m.created_at||''; let dtStr='';
        if(ts){ const d=new Date(ts); if(!isNaN(d)) dtStr=d.toLocaleString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit',timeZone:'Asia/Jakarta'})+' WIB'; }
        const likes=parseInt(m.likes||m.num_likes||0), rts=parseInt(m.retweets||m.num_shares||0), reps=parseInt(m.replies||m.num_comments||0);
        const text=_linkify(m.text||''), tUrl=(m.url&&m.url!=='#')?m.url:'';
        const mJson=esc(JSON.stringify(m).replace(/'/g,'&#39;'));
        const viewLink=tUrl?`<a href="${esc(tUrl)}" target="_blank" rel="noopener" class="up-tw-link" onclick="event.stopPropagation()"><i class="ph ph-arrow-square-out"></i>View</a>`:'';
        return `<div class="up-tweet" onclick="Detail.open(JSON.parse(this.dataset.m))" data-m='${mJson}'>
            <div class="up-tw-head"><span class="up-tw-author">${esc(m.author_name||m.author||_u?.screen_name||'')}</span><span class="up-tw-time">${dtStr}</span></div>
            <div class="up-tw-text">${text}</div>
            <div class="up-tw-foot">
                <span class="up-tw-sent up-tw-sent--${sentCls}">${sentLbl}</span>
                <span class="up-tw-metric"><i class="ph ph-heart"></i>${numF(likes)}</span>
                <span class="up-tw-metric"><i class="ph ph-repeat"></i>${numF(rts)}</span>
                <span class="up-tw-metric"><i class="ph ph-chat-circle"></i>${numF(reps)}</span>
                ${viewLink}
            </div>
        </div>`;
    }
    function goPage(p){ const tp=Math.ceil(_mentions.length/_PP); if(p<1||p>tp)return; _pg=p; _renderMentions(); }
    async function loadMore() {
        const btn=_$('upLoadMore');
        if(btn){ btn.disabled=true; btn.innerHTML='<div class="spin-ring" style="width:14px;height:14px;border-width:2px"></div>'; }
        try {
            const uname=_u?.screen_name||_u?.username||'';
            const url=`/mk/api/x/user-detailed-mentions?project_id=${CFG.pid}&username=${encodeURIComponent(uname)}&start_date=${CFG.sd}&end_date=${CFG.ed}&api_start=${_apiStart}&stat_mentions=${_u?.mentions||0}&stat_replies=${_u?.replies||0}&stat_retweets=${_u?.retweets||0}`;
            const r=await fetch(url,{signal:_abort?.signal}); const j=await r.json();
            if(j.success&&j.data){ _mentions=[..._mentions,...(j.data.mentions||[])]; _hasMore=j.data.has_more||false; _apiStart=j.data.next_api_start||0; const tp=Math.ceil(_mentions.length/_PP); _pg=Math.min(_pg+1,tp); }
            _renderMentions();
        } catch(e){ if(btn){ btn.disabled=false; btn.innerHTML='More <i class="ph ph-caret-right"></i>'; } }
    }
    document.addEventListener('keydown', e=>{ if(e.key==='Escape'&&_$('sntPanel')?.classList.contains('show')) close(); });
    return { open, close, goPage, loadMore };
})();

/* ═══════════════════════════════════════════════════════════
   DETAIL PANEL
═══════════════════════════════════════════════════════════ */
const Detail = (() => {
    function open(m) {
        const sent=(m.sentiment||'neutral').toLowerCase(), sentCls=sent.includes('pos')?'pos':sent.includes('neg')?'neg':'neu';
        const sentLbl=sent.includes('pos')?'Positive':sent.includes('neg')?'Negative':'Neutral';
        const ts=m.timestamp||m.created_at||''; let dtStr='';
        if(ts){ const d=new Date(ts); if(!isNaN(d)) dtStr=d.toLocaleString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit',timeZone:'Asia/Jakarta'})+' WIB'; }
        const likes=parseInt(m.likes||m.num_likes||0), rts=parseInt(m.retweets||m.num_shares||0), reps=parseInt(m.replies||m.num_comments||0);
        const text=_linkify(m.text||'');
        const handle=m.author||'';
        const tUrl=(m.url&&m.url!=='#')?m.url:`https://twitter.com/${encodeURIComponent(handle)}`;
        _$('detailTitle').textContent = 'Tweet Detail';
        _$('detailBody').innerHTML = `
            <div class="do-dp2-meta">${dtStr?`<i class="ph ph-clock me-1"></i>${dtStr}`:''}</div>
            <div class="do-dp2-sent do-dp2-sent--${sentCls}"><i class="ph ph-circle-fill" style="font-size:6px"></i> ${sentLbl}</div>
            <div class="do-dp2-content">${text||'<em style="color:var(--slate-400)">No content</em>'}</div>
            <div class="do-dp2-stats">
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(likes)}</div><div class="do-dp2-stat-lbl">Likes</div></div>
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(rts)}</div><div class="do-dp2-stat-lbl">Retweets</div></div>
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(reps)}</div><div class="do-dp2-stat-lbl">Replies</div></div>
            </div>
            <a href="${esc(tUrl)}" target="_blank" rel="noopener" class="do-dp2-link"><i class="ph ph-arrow-square-out"></i>Open on X</a>`;
        _$('detailPanel').classList.add('show');
    }
    function close(){ _$('detailPanel').classList.remove('show'); }
    return { open, close };
})();

function _linkify(raw) {
    if(!raw) return '<em style="color:var(--slate-400)">No content</em>';
    let t=raw.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    t=t.replace(/(https?:\/\/[^\s<>"'\u0000-\u001F]+)/g, u=>{ const h=u.replace(/&amp;/g,'&'); return `<a href="${h}" target="_blank" rel="noopener" style="color:var(--primary);word-break:break-all">${u}</a>`; });
    t=t.replace(/(?<![\/\w])@([A-Za-z0-9_]{1,50})/g,'<a href="https://twitter.com/$1" target="_blank" rel="noopener" style="color:#1d9bf0">@$1</a>');
    t=t.replace(/(?<!\w)#([A-Za-z0-9_\u00C0-\u024F]+)/g,'<a href="https://twitter.com/hashtag/$1" target="_blank" rel="noopener" style="color:#1d9bf0">#$1</a>');
    return t;
}

const XIExport = (() => {
    let _toastTimer = null;

    function _toast(msg, type='default', duration=3200) {
        const t=_$('exportToast'), m=_$('exportToastMsg'), ico=_$('exportToastIcon');
        if(!t||!m) return;
        m.textContent = msg;
        t.className   = 'export-toast show '+(type!=='default'?type:'');
        const icons   = { success:'ph-check-circle', error:'ph-x-circle', default:'ph-spinner' };
        ico.className = 'ph '+(icons[type]||icons.default);
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(()=>t.classList.remove('show'), duration);
    }

    function _btnState(btn, loading) {
        if(!btn) return;
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
     

    function _getDonutSnapshot() {
        try {
            if(!donutInst || donutInst.isDisposed()) return null;
            const ch = _$('donutChart');
            if(!ch || ch.style.display === 'none') return null;
            return donutInst.getDataURL({ type:'png', pixelRatio:2, backgroundColor:'#ffffff' });
        } catch(e) { return null; }
    }

    function _makeOnClone(donutSnapshot) {
        return (clonedDoc) => {
            const s = clonedDoc.createElement('style');
            s.textContent = `
                *, *::before, *::after { animation:none!important; transition:none!important; }
                .fade-up,.fade-up-d1,.fade-up-d2,.fade-up-d3,.fade-up-d4 { opacity:1!important; transform:none!important; }
                [data-html2canvas-ignore] { display:none!important; }
                .do-panel-overlay,.do-panel,#panelOverlay,#sntPanel { display:none!important; }
                .chart-loading,.spinner-state,.spin-ring { display:none!important; }
                .sk-block { animation:none!important; background:#e2e8f0!important; }
                .kpi-card-hover { transform:none!important; filter:none!important; }
                .sent-tabs { display:none!important; }
            `;
            clonedDoc.head.appendChild(s);

            clonedDoc.querySelectorAll('.do-panel-overlay,.do-panel,.chart-loading,.spinner-state,.export-toast,.sent-tabs')
                .forEach(el => { el.style.display = 'none'; });

            clonedDoc.querySelectorAll('.card,.kpi-card-hover,.ht-item,.ht-list,[class*="col-"],.row,#pageExportArea')
                .forEach(el => {
                    el.style.opacity    = '1';
                    el.style.transform  = 'none';
                    el.style.visibility = 'visible';
                    el.style.animation  = 'none';
                });

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

    async function _capture(areaId, bgColor) {
        const area = document.getElementById(areaId);
        if(!area) throw new Error('Area #' + areaId + ' tidak ditemukan');

        window.scrollTo({ top: 0 });
        const donutSnapshot = _getDonutSnapshot();

        area.querySelectorAll('.fade-up,.fade-up-d1,.fade-up-d2,.fade-up-d3,.fade-up-d4,.kpi-card-hover,.ht-item,.card,[class*="col-"]')
            .forEach(e => {
                e.style.opacity    = '1';
                e.style.transform  = 'none';
                e.style.visibility = 'visible';
            });

        await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));

        _freeze(); await new Promise(r=>setTimeout(r,400));
        let capturePromise;
        try { capturePromise = await html2canvas(area, {

            scale:           2,
            useCORS:         true,
            allowTaint:      true,
            backgroundColor: bgColor || '#f1f5f9',
            logging:         false,
            removeContainer: true,
            scrollX:         0,
            scrollY:         0,
            width:           area.offsetWidth,
            height:          area.scrollHeight,
            onclone:         _makeOnClone(donutSnapshot),
        
        }); } finally { _unfreeze(); }

        const timeout = new Promise((_,reject) =>
            setTimeout(() => reject(new Error('Capture timeout — cek console')), 15000)
        );

        return Promise.race([capturePromise, timeout]);
    }

    function _drawHeader(pdf, label) {
        const pW = pdf.internal.pageSize.getWidth();
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + (label || 'X Top Influencers'), 10, 7.5);
        const now = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - 10, 7.5, { align: 'right' });
    }

    function _paginatePdf(pdf, canvas, label) {
        const pW = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight();
        const margin = 10, usableW = pW - margin*2, usableH = pH - margin*2 - 14;
        const ratio = usableW / canvas.width, sliceH = usableH / ratio;
        let srcY = 0, pg = 0;
        while (srcY < canvas.height) {
            if (pg > 0) { pdf.addPage(); _drawHeader(pdf, label); }
            const srcSlice = Math.min(sliceH, canvas.height - srcY);
            const slice    = document.createElement('canvas');
            slice.width    = canvas.width; slice.height = Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcSlice, 0, 0, canvas.width, srcSlice);
            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, usableW, srcSlice * ratio);
            pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
            pdf.text(`Halaman ${pg + 1}`, pW / 2, pH - 3, { align: 'center' });
            srcY += srcSlice; pg++;
        }
    }

    const _cardMeta = {
        donut : { label:'Top 5 Engagement Share', file:'top5-engagement-share' },
        list  : { label:'Influencer Ranking',     file:'influencer-ranking'    },
    };
    const _stamp = () => new Date().toISOString().slice(0, 10).replace(/-/g, '');

    async function runCard(areaId, cardKey, type, btn) {
        if(!window.html2canvas)                    { _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf' && !window.jspdf?.jsPDF)   { _toast('jsPDF tidak tersedia','error'); return; }

        _btnState(btn, true);
        _toast(type==='pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);

        try {
            const canvas = await _capture(areaId, '#ffffff');
            const meta   = _cardMeta[cardKey] || { label: cardKey, file: cardKey };
            const fname  = `x_influencers_${meta.file}_${CFG.pid}_${_stamp()}`;

            if (type === 'image') {
                const a = document.createElement('a');
                a.download = fname + '.png'; a.href = canvas.toDataURL('image/png'); a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation: canvas.width > canvas.height ? 'landscape' : 'portrait', unit:'mm', format:'a4' });
                _drawHeader(pdf, meta.label);
                _paginatePdf(pdf, canvas, meta.label);
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[XIExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btn, false);
        }
    }

    async function run(type, btn) {
        if(!window.html2canvas)                    { _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf' && !window.jspdf?.jsPDF)   { _toast('jsPDF tidak tersedia','error'); return; }

        const btnPdf = _$('pageExportPdfBtn'), btnImg = _$('pageExportImgBtn');
        _btnState(btnPdf, true); _btnState(btnImg, true);
        _toast(type==='pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);

        try {
            const canvas = await _capture('pageExportArea', '#f1f5f9');
            const stamp  = _stamp();
            if (type === 'image') {
                const a = document.createElement('a');
                a.download = `x_influencers_${CFG.pid}_${stamp}.png`;
                a.href = canvas.toDataURL('image/png'); a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
                _drawHeader(pdf, 'X Top Influencers');
                _paginatePdf(pdf, canvas, 'X Top Influencers');
                pdf.save(`x_influencers_${CFG.pid}_${stamp}.pdf`);
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[XIExport]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btnPdf, false); _btnState(btnImg, false);
        }
    }

    return { run, runCard };
})();

/* ══ INIT ══ */
document.addEventListener('DOMContentLoaded', () => {
    if(CFG.pid) loadData();
    document.querySelectorAll('#subTabs .sent-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#subTabs .sent-tab').forEach(b=>b.classList.remove('active'));
            btn.classList.add('active');
            curSub = btn.dataset.s;
            Store  = [];
            loadData();
        });
    });
});
</script>
@endsection