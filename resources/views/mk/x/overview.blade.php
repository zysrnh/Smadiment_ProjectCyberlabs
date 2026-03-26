@extends('mk.layouts.app')

@section('title', 'X Overview - SMADIMENT')

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
.fade-up{animation:fadeUp .38s ease-out both}
.fade-up-d1{animation-delay:.05s}
.fade-up-d2{animation-delay:.10s}
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
.chart-container{position:relative}.chart-loading{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;background:#fff;z-index:2;transition:opacity .3s}.chart-loading.hidden{opacity:0;pointer-events:none}.chart-loading span{font-size:11px;font-weight:600;color:var(--slate-400)}.chart-empty{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600}.chart-empty i{font-size:34px;color:var(--slate-300);display:block}

/* ── Tabs ── */
.tme-tabs{display:flex;gap:2px;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px;margin-bottom:16px}
.tme-tab-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:7px 14px;border-radius:4px;border:none;background:transparent;font-size:12px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:background .13s,color .13s;white-space:nowrap}
.tme-tab-btn:hover{background:#fff;color:var(--slate-800)}
.tme-tab-btn.active{background:#fff;color:var(--primary);box-shadow:0 1px 4px rgba(0,0,0,.08)}
.tme-tab-chip{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:16px;padding:0 5px;border-radius:3px;font-size:9px;font-weight:800;background:var(--primary-lt);color:var(--primary)}
.tme-tab-btn:not(.active) .tme-tab-chip{background:var(--slate-100);color:var(--slate-400)}
.tme-tab-panel{display:none}.tme-tab-panel.active{display:block}

/* ── Hashtag list ── */
.ht-list{display:flex;flex-direction:column}
.ht-item{display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid var(--slate-100);cursor:pointer;transition:background .12s}
.ht-item:last-child{border-bottom:none}.ht-item:hover{background:var(--slate-50)}
.ht-rank{width:24px;height:24px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:var(--slate-400);background:var(--slate-100);border:1px solid var(--slate-200)}
.ht-rank--1{background:linear-gradient(135deg,#ffd700,#F59E0B);color:#7c5900;border-color:#ffd700}
.ht-rank--2{background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0}
.ht-rank--3{background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32}
.ht-name{flex:1;min-width:0;font-size:13px;font-weight:700;color:var(--primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ht-bar-wrap{flex:0 0 100px;height:6px;background:var(--slate-100);border-radius:99px;overflow:hidden}
.ht-bar-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--primary),rgba(3,128,71,.5));transition:width .4s cubic-bezier(.4,0,.2,1)}
.ht-count{font-size:11px;font-weight:700;color:var(--slate-500);white-space:nowrap;flex-shrink:0;min-width:36px;text-align:right}

/* ── Post list ── */
.tme-post-list{display:flex;flex-direction:column}
.tme-post{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--slate-100);transition:background .12s;cursor:pointer}
.tme-post:last-child{border-bottom:none}.tme-post:hover{background:var(--slate-50)}
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
.tme-sent{display:inline-flex;align-items:center;padding:2px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.3px}
.tme-sent--pos{background:#d1fae5;color:#065f46}.tme-sent--neg{background:#fee2e2;color:#991b1b}.tme-sent--neu{background:var(--slate-100);color:var(--slate-500)}
.tme-view-link{display:inline-flex;align-items:center;gap:3px;font-size:9.5px;font-weight:700;color:var(--primary);text-decoration:none;padding:2px 6px;border-radius:3px;background:var(--primary-lt);border:1px solid rgba(3,128,71,.2);transition:background .12s,color .12s;margin-left:auto}
.tme-view-link:hover{background:var(--primary);color:#fff}
.tme-post-thumb{width:80px;height:80px;border-radius:var(--radius-sm);flex-shrink:0;overflow:hidden;border:1.5px solid var(--slate-200);background:var(--slate-800);position:relative;align-self:center;box-shadow:var(--shadow-sm)}
.tme-post-thumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .2s}
.tme-post:hover .tme-post-thumb img{transform:scale(1.06)}
.tme-post-thumb-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:24px;background:linear-gradient(135deg,#273B4A,#374151)}

/* ── Pagination ── */
.tme-pagination{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--slate-100);flex-wrap:wrap;gap:8px}
.tme-pag-info{font-size:11px;color:var(--slate-400);font-weight:500}
.tme-pag-controls{display:flex;align-items:center;gap:3px}
.tme-pag-btn{min-width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;padding:0 6px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .12s;user-select:none}
.tme-pag-btn:hover:not(:disabled):not(.is-active){border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}
.tme-pag-btn.is-active{background:var(--primary);border-color:var(--primary);color:#fff}
.tme-pag-btn:disabled{opacity:.35;cursor:not-allowed}
.tme-rows-sel{padding:4px 9px;border:1px solid var(--slate-200);border-radius:var(--radius-sm);font-size:11px;font-weight:600;color:var(--slate-600);background:var(--slate-50);outline:none;cursor:pointer;transition:border-color .14s}
.tme-rows-sel:focus{border-color:var(--primary)}

/* ══ Donut + Breakdown List (stacked) ══ */
.donut-stack-wrap{display:flex;flex-direction:column;gap:0}
.donut-chart-area{width:100%;display:flex;align-items:center;justify-content:center;padding:8px 16px 0}
.donut-breakdown-list{border-top:1px solid var(--slate-100);margin-top:8px}
.donut-breakdown-item{display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--slate-100);cursor:pointer;transition:background .12s}
.donut-breakdown-item:last-child{border-bottom:none}
.donut-breakdown-item:hover{background:var(--primary-lt)}
.donut-breakdown-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.donut-breakdown-rank{font-size:10px;font-weight:800;color:var(--slate-400);width:16px;flex-shrink:0;text-align:center}
.donut-breakdown-name{flex:1;min-width:0;font-size:12px;font-weight:700;color:var(--slate-800);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.donut-breakdown-bar-wrap{flex:0 0 80px;height:5px;background:var(--slate-100);border-radius:99px;overflow:hidden}
.donut-breakdown-bar-fill{height:100%;border-radius:99px;transition:width .4s cubic-bezier(.4,0,.2,1)}
.donut-breakdown-val{font-size:11px;font-weight:700;color:var(--slate-500);white-space:nowrap;min-width:48px;text-align:right}
.donut-breakdown-pct{font-size:10px;font-weight:700;min-width:36px;text-align:right}

/* ── Slide Panel ── */
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
.do-sent-badge--pos{background:#dbeafe;color:#1d4ed8}.do-sent-badge--neg{background:#fee2e2;color:#991b1b}.do-sent-badge--neu{background:var(--slate-100);color:var(--slate-500)}
.do-panel-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:12px;color:var(--slate-400);font-size:13px;font-weight:600}
.do-panel-spinner{width:28px;height:28px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite}
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
.do-dp2-sent--pos{background:#dbeafe;color:#1d4ed8}.do-dp2-sent--neg{background:#fee2e2;color:#991b1b}.do-dp2-sent--neu{background:var(--slate-100);color:var(--slate-500)}
.do-dp2-content{font-size:12px;color:var(--slate-700);line-height:1.7;margin-bottom:12px;background:var(--slate-50);border-radius:var(--radius-sm);padding:10px 12px;border:1px solid var(--slate-200);word-break:break-word}
.do-dp2-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:6px;margin-bottom:10px}
.do-dp2-stat{background:var(--slate-50);border-radius:var(--radius-sm);padding:8px 10px;border:1px solid var(--slate-200);text-align:center}
.do-dp2-stat-val{font-size:14px;font-weight:700;color:var(--slate-900)}
.do-dp2-stat-lbl{font-size:9px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.4px;margin-top:1px}
.do-dp2-link{display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:var(--primary);color:#fff;border-radius:var(--radius-sm);font-size:12px;font-weight:700;text-decoration:none;transition:filter .14s;margin-top:4px}
.do-dp2-link:hover{filter:brightness(1.1);color:#fff}

/* ══ Export ══ */
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

@media(max-width:640px){.do-panel{width:100vw}.tme-tabs{flex-wrap:wrap}.tme-tab-btn{flex:unset;min-width:calc(50% - 4px)}.tme-post-thumb{display:none}}
</style>
@endsection

@section('page-title', 'X Overview')

@section('content')
@php
    $projectId  = $projectId  ?? request()->get('project_id');
    $startDate  = $startDate  ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate    = $endDate    ?? request()->get('end_date',   now()->format('Y-m-d'));
    $projects   = $projects   ?? [];
@endphp
<script>
    const OV_PID = {{ $projectId ? (int)$projectId : 'null' }};
    const OV_SD  = '{{ $startDate }}';
    const OV_ED  = '{{ $endDate }}';
</script>

@include('mk.layouts.partials.filter-datepicker')

<div id="pageExportArea">

    {{-- ══ KPI Row ══ --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card h-100 text-white kpi-card-hover fade-up fade-up-d1"
                 style="background:linear-gradient(135deg,#038047,#05a85e);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Total Views</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiViews">
                                <span class="sk-block" style="width:100px;height:28px;display:inline-block;"></span>
                            </h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiViewsSub">
                                <i class="ph ph-eye me-1"></i>Loading…
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg"><i class="ph ph-eye"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 text-white kpi-card-hover fade-up fade-up-d2"
                 style="background:linear-gradient(135deg,#06B6D4,#0891b2);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Total Retweets</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiRt">
                                <span class="sk-block" style="width:100px;height:28px;display:inline-block;"></span>
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
    </div>

    {{-- ══ Page Export Toolbar ══ --}}
    <div class="page-export-bar" data-html2canvas-ignore="true">
        <div class="page-export-bar-left">
            <i class="ph ph-export"></i>
            <span>Export Halaman</span>
            <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Charts + Post List (semua tab)</span>
        </div>
        <div class="page-export-bar-right">
            <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                    onclick="OVExport.run('pdf', this)" title="Export PDF">
                <i class="ph ph-file-pdf export-icon"></i>
                <span class="export-spinner"></span>
            </button>
            <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                    onclick="OVExport.run('image', this)" title="Export PNG">
                <i class="ph ph-image export-icon"></i>
                <span class="export-spinner"></span>
            </button>
        </div>
    </div>

    {{-- ══ Tabs ══ --}}
    <div class="tme-tabs" id="ovTabsBar">
        <button class="tme-tab-btn active" id="tab-hashtag" onclick="OVTab.show('hashtag')">
            <i class="ph ph-hash"></i> Top Topics
            <span class="tme-tab-chip" id="chip-hashtag">—</span>
        </button>
        <button class="tme-tab-btn" id="tab-view" onclick="OVTab.show('view')">
            <i class="ph ph-eye"></i> Most Viewed
            <span class="tme-tab-chip" id="chip-view">—</span>
        </button>
        <button class="tme-tab-btn" id="tab-retweet" onclick="OVTab.show('retweet')">
            <i class="ph ph-repeat"></i> Most Retweeted
            <span class="tme-tab-chip" id="chip-retweet">—</span>
        </button>
    </div>

    {{-- ══ Panel: Top Topics (Hashtag) ══ --}}
    <div class="tme-tab-panel active" id="panel-hashtag">

        {{-- Hashtag List Card --}}
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-export-hashtag-list">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-hash f-18 text-primary"></i></div>
                        <div><h6 class="mb-0">Top Topics / Hashtags</h6><small class="text-muted">Klik untuk lihat tweet terkait</small></div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-primary text-primary" id="badgeHashtag">Loading…</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-hashtag-list','hashtag-list','pdf',this)" title="PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-hashtag-list','hashtag-list','image',this)" title="PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div id="hashtagLoading" class="spinner-state"><div class="spin-ring"></div><span>Memuat topics…</span></div>
                <div id="hashtagContent" style="display:none;">
                    <div id="hashtagList" class="ht-list"></div>
                    <div id="pag-hashtag"></div>
                </div>
                <div id="hashtagEmpty" style="display:none;" class="chart-empty" style="padding:40px 0;"><i class="ph ph-hash"></i><span>Tidak ada data topics</span></div>
            </div>
        </div>

        {{-- Donut + Breakdown (stacked dalam 1 card) --}}
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
            <div id="card-export-hashtag-donut">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
                        <div><h6 class="mb-0">Distribusi — Top 5 Topics</h6><small class="text-muted">Proporsi penggunaan topic teratas</small></div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-hashtag-donut','hashtag-donut','pdf',this)" title="PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-hashtag-donut','hashtag-donut','image',this)" title="PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="donut-stack-wrap">
                        {{-- Donut chart di atas --}}
                        <div class="donut-chart-area">
                            <div class="chart-container" style="width:100%;max-width:420px;">
                                <div class="chart-loading" id="loadingDonutHashtag"><div class="spin-ring"></div><span>Loading…</span></div>
                                <div id="donutHashtagChart" style="width:100%;height:320px;display:none;"></div>
                            </div>
                        </div>
                        {{-- Breakdown list di bawah --}}
                        <div class="donut-breakdown-list" id="donutHashtagBreakdown" style="display:none;"></div>
                    </div>
                </div>
                <div style="height:8px;"></div>
            </div>
        </div>

    </div>

    {{-- ══ Panel: Most Viewed & Most Retweeted ══ --}}
    @foreach(['view' => 'Most Viewed', 'retweet' => 'Most Retweeted'] as $tp => $tpLabel)
    @php
        $tpIcons = ['view' => 'ph-eye', 'retweet' => 'ph-repeat'];
        $tpIcon  = $tpIcons[$tp];
    @endphp
    <div class="tme-tab-panel" id="panel-{{ $tp }}">

        {{-- Donut + Breakdown stacked card --}}
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-export-donut-{{ $tp }}">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
                        <div><h6 class="mb-0">Distribusi {{ $tpLabel }} — Top 5</h6><small class="text-muted">Proporsi engagement per tweet — klik untuk detail</small></div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-donut-{{ $tp }}','donut-{{ $tp }}','pdf',this)" title="PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-donut-{{ $tp }}','donut-{{ $tp }}','image',this)" title="PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div class="donut-stack-wrap">
                        <div class="donut-chart-area">
                            <div class="chart-container" style="width:100%;max-width:420px;">
                                <div class="chart-loading" id="loadingDonut-{{ $tp }}"><div class="spin-ring"></div><span>Loading chart…</span></div>
                                <div id="donutChart-{{ $tp }}" style="width:100%;height:320px;display:none;"></div>
                                <div id="donutEmpty-{{ $tp }}" style="display:none;" class="chart-empty"><i class="ph ph-chart-donut"></i><span>No data</span></div>
                            </div>
                        </div>
                        <div class="donut-breakdown-list" id="donutBreakdown-{{ $tp }}" style="display:none;"></div>
                    </div>
                </div>
                <div style="height:8px;"></div>
            </div>
        </div>

        {{-- Post List Card --}}
        <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
            <div id="card-export-list-{{ $tp }}">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph {{ $tpIcon }} f-18 text-primary"></i></div>
                        <div><h6 class="mb-0">Top Tweets by {{ $tpLabel }}</h6><small class="text-muted">Klik tweet untuk lihat detail</small></div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <select class="tme-rows-sel" id="rows-{{ $tp }}" onchange="OVData.reloadAll()">
                            <option value="10">Top 10</option>
                            <option value="20">Top 20</option>
                            <option value="50">Top 50</option>
                            <option value="100" selected>Top 100</option>
                        </select>
                        <span class="badge bg-light-primary text-primary" id="badge-{{ $tp }}">Loading…</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-list-{{ $tp }}','list-{{ $tp }}','pdf',this)" title="PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-list-{{ $tp }}','list-{{ $tp }}','image',this)" title="PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div id="list-{{ $tp }}" class="p-0"><div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div></div>
                <div id="pag-{{ $tp }}"></div>
            </div>
        </div>

    </div>
    @endforeach

</div>{{-- /pageExportArea --}}

{{-- Export Toast --}}
<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

{{-- Slide Panel --}}
<div class="do-panel-overlay" id="ovPanelOverlay" onclick="OVPanel.close()"></div>
<div class="do-panel" id="ovSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="ovPanelDot" style="background:var(--primary);"></div>
        <span class="do-panel-title" id="ovPanelTitle">X Tweets</span>
        <button class="do-panel-close" onclick="OVPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span id="ovPanelMeta">—</span></div>
    </div>
    <div class="do-panel-list" id="ovPanelList"></div>
    <div class="do-detail-panel" id="ovDetailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="OVDetail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="ovDetailTitle">Detail</span>
            <button class="do-panel-close" onclick="OVPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="ovDetailBody"></div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

const PLAT     = 'X';
const API_BASE = '/mk/api/x';
const OVCfg    = {
    pid    : OV_PID,
    sd     : OV_SD,
    ed     : OV_ED,
    primary: '#038047',
    colors : { view:'#038047', retweet:'#06B6D4', hashtag:'#038047' },
    perPage: 10,
};
const DONUT_COLORS = ['#038047','#273B4A','#F59E0B','#06B6D4','#EF4444'];
const TAB_TYPES    = ['hashtag', 'view', 'retweet'];

const _$  = id => document.getElementById(id);
const numF = n => parseInt(n||0).toLocaleString('id-ID');
const numK = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc  = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const dec  = s => { if(!s)return ''; try{const f=decodeURIComponent(escape(s));if(!f.includes('\uFFFD')&&f!==s)return f}catch(e){}return s; };
const hideLd = id => { const e=_$(id); if(e&&e.classList.contains('chart-loading'))e.classList.add('hidden'); };

const Store  = { view:[], retweet:[], hashtag:[] };
const Pag    = { view:1,  retweet:1,  hashtag:1  };
let allPostsRaw = [];
let _engFetched = false;

window.addEventListener('resize',()=>{
    ['__ec_donutHashtagChart','__ec_donutChart_view','__ec_donutChart_retweet'].forEach(k=>{
        try{ if(window[k]&&!window[k].isDisposed()) window[k].resize(); }catch(e){}
    });
});

/* ════════════════════════════
   TABS
════════════════════════════ */
const OVTab = {
    _loaded: { hashtag:false, view:false, retweet:false },
    show(t) {
        TAB_TYPES.forEach(x => {
            _$('tab-'+x)?.classList.toggle('active', x===t);
            _$('panel-'+x)?.classList.toggle('active', x===t);
        });
        if (!this._loaded[t]) { this._loaded[t]=true; OVData.loadTab(t); }
        else if (t!=='hashtag' && Store[t].length) OVData._renderDonut(t, Store[t]);
        if (t!=='hashtag') setTimeout(()=>{ _$('ovTabsBar')?.scrollIntoView({behavior:'smooth',block:'nearest'}); },80);
    },
    reset() { this._loaded = { hashtag:false, view:false, retweet:false }; }
};

/* ════════════════════════════
   DATA
════════════════════════════ */
const OVData = {
    async loadTab(t) {
        if (!OVCfg.pid) { this._noProject(t); return; }
        if (t==='hashtag') { await this.loadHashtags(); return; }
        await this._ensureEngagement();
        this._sortAndDisplay(t);
    },

    async loadHashtags() {
        const ld=_$('hashtagLoading'), ct=_$('hashtagContent'), em=_$('hashtagEmpty'), bd=_$('badgeHashtag');
        try {
            const r = await fetch(`${API_BASE}/trending-topics?start_date=${OVCfg.sd}&end_date=${OVCfg.ed}&location=Indonesia`);
            const j = await r.json();
            const topics = (j.data?.top_topics||[]).map(t=>({ name:t.name, size:t.total_volume||t.appearances||0, url:t.url||'' }));
            if (topics.length) {
                Store.hashtag=topics; Pag.hashtag=1;
                if(bd) bd.textContent=topics.length+' topics';
                const ch=_$('chip-hashtag'); if(ch) ch.textContent=topics.length;
                this._renderHashtagList(topics);
                this._renderDonutHashtag(topics);
            } else {
                if(ld) ld.style.display='none';
                if(em) em.style.display='flex';
                if(bd) bd.textContent='0';
            }
        } catch(e) {
            console.error('[OV]',e);
            if(ld) ld.style.display='none';
            if(em) em.style.display='flex';
            if(bd) bd.textContent='Error';
        }
    },

    _renderHashtagList(hashtags) {
        const ld=_$('hashtagLoading'), ct=_$('hashtagContent'), list=_$('hashtagList'), pg=_$('pag-hashtag');
        if(!list) return;
        const all=hashtags.slice(0,50); Store.hashtag=all;
        const page=Pag.hashtag||1, pp=OVCfg.perPage, total=all.length, pages=Math.ceil(total/pp),
              start=(page-1)*pp, items=all.slice(start,start+pp), mx=all[0]?.size||1;
        list.innerHTML='';
        items.forEach((h,i)=>{
            const rk=start+i+1, rc=rk<=3?` ht-rank--${rk}`:'', pct=Math.round((h.size/mx)*100);
            const el=document.createElement('div'); el.className='ht-item';
            el.innerHTML=`<div class="ht-rank${rc}">${rk}</div><div class="ht-name">${esc(h.name)}</div><div class="ht-bar-wrap"><div class="ht-bar-fill" style="width:${pct}%;"></div></div><div class="ht-count">${numF(h.size)}</div>`;
            el.onclick=()=>this._openHashtagPanel(h);
            list.appendChild(el);
        });
        if(pg) {
            if(pages<=1){ pg.innerHTML=''; }
            else {
                const fr=start+1, to=Math.min(start+pp,total); let b='',r=2;
                b+=`<button class="tme-pag-btn" ${page<=1?'disabled':''} onclick="OVData.goHashtagPage(${page-1})"><i class="ph ph-caret-left"></i></button>`;
                for(let i=1;i<=pages;i++){if(i===1||i===pages||(i>=page-r&&i<=page+r))b+=`<button class="tme-pag-btn${i===page?' is-active':''}" onclick="OVData.goHashtagPage(${i})">${i}</button>`;else if(i===page-r-1||i===page+r+1)b+=`<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`}
                b+=`<button class="tme-pag-btn" ${page>=pages?'disabled':''} onclick="OVData.goHashtagPage(${page+1})"><i class="ph ph-caret-right"></i></button>`;
                pg.innerHTML=`<div class="tme-pagination"><span class="tme-pag-info">Menampilkan ${fr}–${to} dari ${total} topics</span><div class="tme-pag-controls">${b}</div></div>`;
            }
        }
        if(ld) ld.style.display='none';
        if(ct) ct.style.display='block';
    },
    goHashtagPage(p){ Pag.hashtag=p; this._renderHashtagList(Store.hashtag); _$('hashtagList')?.scrollIntoView({behavior:'smooth',block:'nearest'}); },

    _renderDonutHashtag(h) {
        const ld=_$('loadingDonutHashtag'), ch=_$('donutHashtagChart'), bd=_$('donutHashtagBreakdown');
        if(!ld||!ch||!h.length){ hideLd('loadingDonutHashtag'); return; }
        const top5=h.slice(0,5), total=top5.reduce((s,x)=>s+x.size,0);
        this._renderDonutEcharts(
            ch, ld, top5, total, 'mentions',
            x=>x.name, x=>x.size,
            x=>this._openHashtagPanel(x),
            'Topic', null, '__ec_donutHashtagChart'
        );
        this._renderBreakdownList(bd, top5, total, x=>x.name, x=>x.size, (x)=>this._openHashtagPanel(x));
    },

    async _ensureEngagement() {
        if(_engFetched) return;
        const rows=parseInt(_$('rows-view')?.value||'100');
        ['view','retweet'].forEach(t=>{const ls=_$('list-'+t);if(ls)ls.innerHTML=`<div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div>`});
        try {
            const r=await fetch(`${API_BASE}/most-engagement?project_id=${OVCfg.pid}&start_date=${OVCfg.sd}&end_date=${OVCfg.ed}&rows=${rows}`);
            const j=await r.json();
            let items=j.data||j||[]; if(!Array.isArray(items)) items=[];
            allPostsRaw=items; _engFetched=true;
            this._updateKpi(items); this._distributeItems(items);
        } catch(e) {
            console.error('[OV]',e);
            ['view','retweet'].forEach(t=>{
                const ls=_$('list-'+t);
                if(ls) ls.innerHTML=`<div class="chart-empty" style="padding:40px;"><i class="ph ph-warning"></i><span>Gagal memuat: ${esc(e.message)}</span></div>`;
                hideLd('loadingDonut-'+t);
            });
        }
    },

    _distributeItems(items) {
        const sortKey={view:'view_cnt',retweet:'rt'};
        ['view','retweet'].forEach(t=>{
            const k=sortKey[t];
            Store[t]=[...items].sort((a,b)=>parseInt(b[k]||0)-parseInt(a[k]||0));
            Pag[t]=1;
            const ch=_$('chip-'+t); if(ch) ch.textContent=Store[t].length;
            const bd=_$('badge-'+t); if(bd) bd.textContent=Store[t].length+' tweets';
        });
    },
    _sortAndDisplay(t){ this._renderList(t); this._renderDonut(t, Store[t]); },

    _updateKpi(items) {
        let tv=0,tr=0; items.forEach(i=>{tv+=parseInt(i.view_cnt||0);tr+=parseInt(i.rt||0)});
        const n=items.length, av=v=>n?Math.round(v/n):0;
        const el=(id,v)=>{const e=_$(id);if(e)e.textContent=numF(v)};
        el('kpiViews',tv);
        const vs=_$('kpiViewsSub'); if(vs) vs.innerHTML=`<i class="ph ph-chart-line-up me-1"></i>Avg ${numF(av(tv))} / tweet · ${n} tweets`;
        el('kpiRt',tr);
        const rs=_$('kpiRtSub'); if(rs) rs.innerHTML=`<i class="ph ph-chart-line-up me-1"></i>Avg ${numF(av(tr))} / tweet · ${n} tweets`;
    },

    reloadAll() {
        _engFetched=false; Store.view=[]; Store.retweet=[]; Pag.view=1; Pag.retweet=1;
        this._ensureEngagement().then(()=>{
            ['view','retweet'].forEach(t=>{if(OVTab._loaded[t]) this._sortAndDisplay(t)});
        });
    },

    _noProject(t){ const ls=_$('list-'+t);if(ls)ls.innerHTML=`<div class="chart-empty" style="padding:40px;"><i class="ph ph-folder-open"></i><span>Pilih project terlebih dahulu</span></div>`;hideLd('loadingDonut-'+t); },
    _metric(item,type){ const k={view:'view_cnt',retweet:'rt'};return parseInt(item[k[type]]||0); },
    _getName(item){ return item.author?.name||item.name||'X User'; },
    _getScr(item){ return item.author?.scr_name||item.name||''; },
    _getAvatar(item){ return (item.avatar_url||item.author?.image||'').replace(/_normal\./g,'.').trim(); },
    _getColor(item){
        const seed=item.sub_id||item.id||this._getName(item);
        const pal=['#038047','#273B4A','#F59E0B','#06B6D4','#8b5cf6','#ec4899','#f97316','#14b8a6'];
        let h=0; for(let i=0;i<seed.length;i++) h=(h*31+seed.charCodeAt(i))&0xffffffff;
        return pal[Math.abs(h)%pal.length];
    },
    _avHtml(item){ const av=this._getAvatar(item),d='/assets/images/user/dummy.jpg'; return(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.src='${d}'">`:`<img src="${d}">`; },
    _normSent(item){ const r=String(item.sentiment_str||item.sentiment||'').toLowerCase(); return r.includes('pos')?'pos':r.includes('neg')?'neg':'neu'; },
    _tweetUrl(item){ const scr=this._getScr(item),sid=item.sub_id||item.id||''; if(scr&&sid)return `https://twitter.com/${encodeURIComponent(scr)}/status/${encodeURIComponent(sid)}`; return item.url||item.link||''; },

    _renderList(type) {
        const items=Store[type], ls=_$('list-'+type), pg=_$('pag-'+type);
        if(!ls) return;
        if(!items.length){
            ls.innerHTML=`<div class="chart-empty" style="padding:40px;"><i class="ph ph-folder-open"></i><span>Tidak ada data untuk periode ini</span></div>`;
            if(pg) pg.innerHTML=''; return;
        }
        const page=Pag[type]||1, total=items.length, pp=OVCfg.perPage, pages=Math.ceil(total/pp), start=(page-1)*pp;
        ls.innerHTML=`<div class="tme-post-list">${items.slice(start,start+pp).map((it,i)=>this._postHtml(it,start+i,type)).join('')}</div>`;
        if(pg) pg.innerHTML=this._pagHtml(type,page,pages,total,start+1,Math.min(start+pp,total));
        ls.querySelectorAll('.tme-post').forEach(el=>{
            el.addEventListener('click',()=>{
                try{
                    const it=JSON.parse(decodeURIComponent(el.dataset.item));
                    const lm={view:'Most Viewed',retweet:'Most Retweeted'};
                    OVPanel.open(items,type,PLAT+' — '+lm[type]);
                    OVDetail.open(it,type);
                }catch(e){console.warn(e)}
            });
        });
    },
    _pagHtml(type,page,pages,total,from,to){
        if(pages<=1) return '';
        let b='',r=2;
        b+=`<button class="tme-pag-btn" ${page<=1?'disabled':''} onclick="OVData.goPage('${type}',${page-1})"><i class="ph ph-caret-left"></i></button>`;
        for(let i=1;i<=pages;i++){if(i===1||i===pages||(i>=page-r&&i<=page+r))b+=`<button class="tme-pag-btn${i===page?' is-active':''}" onclick="OVData.goPage('${type}',${i})">${i}</button>`;else if(i===page-r-1||i===page+r+1)b+=`<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`}
        b+=`<button class="tme-pag-btn" ${page>=pages?'disabled':''} onclick="OVData.goPage('${type}',${page+1})"><i class="ph ph-caret-right"></i></button>`;
        return `<div class="tme-pagination"><span class="tme-pag-info">Menampilkan ${from}–${to} dari ${total} tweets</span><div class="tme-pag-controls">${b}</div></div>`;
    },
    goPage(t,p){ Pag[t]=p; this._renderList(t); _$('list-'+t)?.scrollIntoView({behavior:'smooth',block:'nearest'}); },

    _postHtml(item,gi,type){
        const rank=gi+1,rkC=rank<=3?'--'+rank:'',name=this._getName(item),scr=this._getScr(item),color=this._getColor(item),avH=this._avHtml(item),sent=this._normSent(item);
        const content=dec((item.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()).slice(0,200);
        const dt=(item.date_created||'').split('T')[0], url=this._tweetUrl(item);
        const v=parseInt(item.view_cnt||0), rt=parseInt(item.rt||0);
        const sL={pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];
        const enc=encodeURIComponent(JSON.stringify(item));
        const vC=type==='view'?' tme-metric--primary':'', rtC=type==='retweet'?' tme-metric--primary':'';
        const av=this._getAvatar(item);
        const thH=(av&&av.startsWith('http'))?`<div class="tme-post-thumb"><img src="${esc(av)}" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'tme-post-thumb-ph\\'>🐦</div>'"></div>`:`<div class="tme-post-thumb"><div class="tme-post-thumb-ph">🐦</div></div>`;
        return `<div class="tme-post" data-item="${esc(enc)}"><div class="tme-post-rank tme-post-rank${rkC}">${rank}</div><div class="tme-post-av" style="background:linear-gradient(135deg,${color},${color}99);">${avH}</div><div class="tme-post-body"><div class="tme-post-author">${esc(name)}${scr?` <span style="color:var(--slate-400);font-weight:500;">@${esc(scr)}</span>`:''}</div>${dt?`<div class="tme-post-date">${dt}</div>`:''}${content?`<div class="tme-post-text">${esc(content)}</div>`:''}<div class="tme-post-stats"><span class="tme-metric${vC}"><i class="ph ph-eye me-1"></i>${numF(v)}</span><span class="tme-metric${rtC}"><i class="ph ph-repeat me-1"></i>${numF(rt)}</span><span class="tme-sent tme-sent--${sent}">${sL}</span>${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="tme-view-link" onclick="event.stopPropagation()"><i class="ph ph-arrow-square-out me-1"></i>Lihat</a>`:''}</div></div>${thH}</div>`;
    },

    _renderDonut(type, items){
        const ld=_$('loadingDonut-'+type), ch=_$('donutChart-'+type), em=_$('donutEmpty-'+type), bd=_$('donutBreakdown-'+type);
        if(!ld||!ch) return;
        if(!items.length){ ld.style.display='none'; if(em) em.style.display='flex'; return; }
        const top5=items.slice(0,5), ml={view:'Views',retweet:'Retweets'}[type];
        const total=top5.reduce((s,it)=>s+this._metric(it,type),0);
        this._renderDonutEcharts(
            ch, ld, top5, total, ml,
            it=>this._getName(it),
            it=>this._metric(it,type),
            it=>{ OVPanel.open(Store[type],type,PLAT); OVDetail.open(it,type); },
            ml,
            it=>dec((it.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()),
            '__ec_donutChart_'+type
        );
        this._renderBreakdownList(bd, top5, total, it=>this._getName(it), it=>this._metric(it,type), (it)=>{ OVPanel.open(Store[type],type,PLAT); OVDetail.open(it,type); });
    },

    /* ════════════════════════════════════
       Breakdown list di bawah donut
    ════════════════════════════════════ */
    _renderBreakdownList(el, data, total, getNameFn, getValFn, onClickFn) {
        if(!el || !data.length) return;
        const maxVal = Math.max(...data.map(d=>getValFn(d)), 1);
        el.innerHTML = data.map((d,i)=>{
            const val=getValFn(d), pct=total>0?((val/total)*100).toFixed(1):0, barW=Math.round((val/maxVal)*100);
            const color=DONUT_COLORS[i], name=getNameFn(d), shortName=name.length>30?name.slice(0,29)+'…':name;
            const rank=i+1;
            return `<div class="donut-breakdown-item" onclick="void(0)" data-idx="${i}">
                <div class="donut-breakdown-rank">${rank}</div>
                <div class="donut-breakdown-dot" style="background:${color};"></div>
                <div class="donut-breakdown-name" title="${esc(name)}">${esc(shortName)}</div>
                <div class="donut-breakdown-bar-wrap">
                    <div class="donut-breakdown-bar-fill" style="width:${barW}%;background:${color};"></div>
                </div>
                <div class="donut-breakdown-val">${numF(val)}</div>
                <div class="donut-breakdown-pct" style="color:${color};">${pct}%</div>
            </div>`;
        }).join('');
        el.querySelectorAll('.donut-breakdown-item').forEach((row,i)=>{
            row.addEventListener('click',()=>{ if(onClickFn) onClickFn(data[i]); });
        });
        el.style.display='block';
    },

    /* ════════════════════════════════════
       ECharts donut (tanpa custom legend lagi,
       legend sudah ada di breakdown list)
    ════════════════════════════════════ */
    _renderDonutEcharts(chartEl, loadEl, data, total, metricLabel, getNameFn, getValFn, onClickFn, titleLabel, getDescFn, windowKey){
        if(!chartEl||!data.length){ if(loadEl) loadEl.style.display='none'; return; }
        if(loadEl) loadEl.style.display='none';
        chartEl.style.display='block';
        const ek=windowKey||('__ec_'+chartEl.id);
        if(window[ek]){ try{window[ek].dispose()}catch(e){} }
        if(typeof echarts==='undefined'){ chartEl.innerHTML='<div class="chart-empty"><i class="ph ph-chart-donut"></i><span>ECharts not loaded</span></div>'; return; }
        const chart=echarts.init(chartEl, null, {renderer:'canvas'});
        window[ek]=chart;
        window.addEventListener('resize',()=>{ try{chart.resize()}catch(e){} });
        const pd=data.map((d,i)=>({
            name:getNameFn(d), value:getValFn(d),
            _desc:getDescFn?getDescFn(d):'',
            itemStyle:{color:DONUT_COLORS[i]}
        }));
        chart.setOption({
            backgroundColor:'transparent',
            animation:true,animationDuration:1000,animationEasing:'cubicOut',animationDelay:idx=>idx*80,
            tooltip:{show:false},
            series:[{
                type:'pie',radius:['40%','65%'],center:['50%','50%'],
                avoidLabelOverlap:true,selectedMode:false,minAngle:8,
                itemStyle:{borderColor:'#fff',borderWidth:3},
                label:{
                    show:true,position:'outside',alignTo:'edge',edgeDistance:16,
                    lineHeight:16,fontSize:11,fontFamily:'inherit',color:'#334155',fontWeight:'600',
                    formatter:p=>{
                        const d=data[p.dataIndex];
                        const nm=getNameFn(d);
                        const short=nm.length>18?nm.slice(0,17)+'…':nm;
                        return `{title|${short}}\n{pct|${p.percent.toFixed(1)}%}`;
                    },
                    rich:{
                        title:{fontSize:11,fontWeight:'700',color:'#1e293b',lineHeight:16},
                        pct:{fontSize:10,fontWeight:'700',color:'#038047',lineHeight:14}
                    }
                },
                labelLine:{show:true,length:12,length2:16,smooth:0.3,lineStyle:{width:1.5,color:'#94A3B8'}},
                emphasis:{
                    scale:true,scaleSize:5,
                    itemStyle:{shadowBlur:12,shadowColor:'rgba(0,0,0,.15)',borderWidth:3,borderColor:'#fff'},
                    label:{show:true}
                },
                data:pd
            }],
            graphic:[
                {type:'text',left:'center',top:'44%',z:100,style:{text:numK(total),fill:'#0f172a',font:"800 26px inherit",textAlign:'center'}},
                {type:'text',left:'center',top:'53%',z:100,style:{text:'TOTAL '+(titleLabel||metricLabel).toUpperCase(),fill:'#94a3b8',font:"600 9px inherit",textAlign:'center'}}
            ]
        });
        chart.on('click',p=>{ const d=data[p.dataIndex]; if(d&&onClickFn) onClickFn(d); });

        /* Custom tooltip on hover */
        let _tt=document.getElementById('ovCustomTT');
        if(!_tt){
            _tt=document.createElement('div'); _tt.id='ovCustomTT';
            _tt.style.cssText='position:fixed;z-index:9999;pointer-events:none;background:#1e293b;color:#fff;border:1px solid #334155;border-radius:6px;padding:10px 14px;max-width:280px;font-size:12px;line-height:1.5;display:none;box-shadow:0 8px 24px rgba(0,0,0,.32);font-family:inherit;opacity:0;transform:translateY(6px) scale(.97);transition:opacity .18s ease,transform .18s ease;';
            document.body.appendChild(_tt);
        }
        let _tm=null;
        chart.on('mouseover',p=>{
            if(p.componentType!=='series') return;
            const d=data[p.dataIndex], cl=DONUT_COLORS[p.dataIndex], desc=(getDescFn?getDescFn(d):'').slice(0,160);
            clearTimeout(_tm);
            _tt.innerHTML=`<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;"><span style="width:9px;height:9px;border-radius:50%;background:${cl};flex-shrink:0;display:inline-block;"></span><b style="font-size:12.5px;">${esc(p.name)}</b></div>${desc?`<div style="font-size:11px;color:#94a3b8;margin-bottom:6px;">${esc(desc)}</div>`:''}<div style="display:flex;align-items:center;gap:8px;"><b style="font-size:13px;">${numF(p.value)} ${metricLabel}</b><span style="color:${cl};font-weight:700;">${p.percent.toFixed(1)}%</span></div>`;
            _tt.style.display='block';
            requestAnimationFrame(()=>{ _tt.style.opacity='1'; _tt.style.transform='translateY(0) scale(1)'; });
        });
        chart.on('mouseout',()=>{
            _tt.style.opacity='0'; _tt.style.transform='translateY(6px) scale(.97)';
            _tm=setTimeout(()=>{ _tt.style.display='none'; },180);
        });
        chartEl.addEventListener('mousemove',e=>{
            if(_tt.style.display==='none') return;
            const vw=window.innerWidth, vh=window.innerHeight, tw=_tt.offsetWidth+16, th=_tt.offsetHeight+16;
            let x=e.clientX+18, y=e.clientY-10;
            if(x+tw>vw) x=e.clientX-tw; if(y+th>vh) y=e.clientY-th;
            _tt.style.left=x+'px'; _tt.style.top=y+'px';
        });
    },

    _openHashtagPanel(h){
        const tag=h.name.toLowerCase();
        const filtered=allPostsRaw.filter(p=>(p.content||'').toLowerCase().includes(tag));
        OVPanel.open(filtered.length?filtered:allPostsRaw.slice(0,20),'view',`${PLAT} — ${h.name} (${h.size} mentions)`);
    },
};

/* ════════════════════════════
   PANEL
════════════════════════════ */
const OVPanel = {
    _items:[], _type:null,
    open(items, type, title){
        this._items=items||[]; this._type=type; OVDetail.close();
        _$('ovPanelDot').style.background=OVCfg.colors[type]||OVCfg.primary;
        _$('ovPanelTitle').textContent=title||PLAT+' Tweets';
        _$('ovPanelMeta').textContent=OVCfg.sd+' – '+OVCfg.ed;
        const ov=_$('ovPanelOverlay'), pn=_$('ovSntPanel');
        ov.classList.remove('hiding'); pn.classList.remove('hiding');
        ov.classList.add('show'); pn.classList.add('show');
        this._render(items,type);
    },
    close(){
        OVDetail.close();
        const ov=_$('ovPanelOverlay'), pn=_$('ovSntPanel');
        pn.classList.add('hiding'); ov.classList.add('hiding');
        setTimeout(()=>{ pn.classList.remove('show','hiding'); ov.classList.remove('show','hiding'); },240);
    },
    _render(items,type){
        const list=_$('ovPanelList'); if(!list) return;
        if(!items?.length){ list.innerHTML='<div class="do-panel-loading"><div class="do-panel-spinner"></div>Tidak ada data</div>'; return; }
        const ml={view:'Views',retweet:'Retweets'}[type]||'Views';
        list.innerHTML=items.slice(0,100).map(item=>{
            const nm=OVData._getName(item), av=OVData._getAvatar(item), cl=OVData._getColor(item),
                  d='/assets/images/user/dummy.jpg',
                  aH=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.src='${d}'">`:`<img src="${d}">`,
                  tx=(item.content||'').replace(/<[^>]*>/g,'').trim(),
                  mv=OVData._metric(item,type), dt=(item.date_created||'').split('T')[0],
                  sn=OVData._normSent(item), sl={pos:'Pos',neg:'Neg',neu:'Neu'}[sn],
                  v=parseInt(item.view_cnt||0), rt=parseInt(item.rt||0), total=v+rt,
                  enc=encodeURIComponent(JSON.stringify(item));
            return `<div class="do-panel-item" data-item="${esc(enc)}" data-type="${type}" onclick="OVPanel._click(this)"><div class="do-panel-avatar" style="background:linear-gradient(135deg,${cl},${cl}99);">${aH}</div><div class="do-panel-item-body"><div class="do-panel-author">${esc(nm)}</div><div class="do-panel-text">${esc(dec(tx).slice(0,130)||'(tidak ada konten)')}</div><div class="do-panel-footer"><span class="do-sent-badge do-sent-badge--${sn}">${sl}</span><span>${ml} ${numF(mv)}</span><span>∑ ${numF(total)}</span>${dt?`<span style="margin-left:auto;">${dt}</span>`:''}</div></div></div>`;
        }).join('');
        if(items.length>100) list.insertAdjacentHTML('beforeend',`<div style="padding:8px;text-align:center;font-size:10px;font-weight:600;color:#94A3B8;background:var(--slate-50);border-top:1px dashed var(--slate-200);">+${(items.length-100).toLocaleString()} lainnya</div>`);
    },
    _click(el){
        try{
            const item=JSON.parse(decodeURIComponent(el.dataset.item.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"')));
            OVDetail.open(item, el.dataset.type||this._type);
        }catch(e){console.warn(e)}
    }
};

/* ════════════════════════════
   DETAIL
════════════════════════════ */
const OVDetail = {
    open(item,type){
        const panel=_$('ovDetailPanel'), body=_$('ovDetailBody'), title=_$('ovDetailTitle');
        if(!panel||!body) return;
        const color=OVCfg.colors[type]||OVCfg.primary, name=OVData._getName(item), scr=OVData._getScr(item),
              av=OVData._getAvatar(item), ac=OVData._getColor(item), d='/assets/images/user/dummy.jpg',
              aH=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.src='${d}'">`:`<img src="${d}">`;
        const raw=(item.content||'').replace(/<[^>]*>/g,'').trim(), ct=raw?dec(raw):'',
              url=OVData._tweetUrl(item), dt=item.date_created||'';
        const v=parseInt(item.view_cnt||0), rt=parseInt(item.rt||0);
        const sn=OVData._normSent(item), sl={pos:'Positif',neg:'Negatif',neu:'Netral'}[sn];
        let dtF=''; if(dt) try{dtF=new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'})}catch(e){dtF=dt.split('T')[0]}
        const flw=item.author?.flw_cnt;
        title.textContent=name;
        body.innerHTML=`<div class="do-dp2-avatar-row"><div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${ac},${ac}99);">${aH}</div><div><div class="do-dp2-name">${esc(name)}</div>${scr?`<div class="do-dp2-handle">@${esc(scr)}</div>`:''}<span class="do-dp2-plat-badge" style="background:${color}18;color:${color};">X / Twitter</span>${flw?`<div style="font-size:10px;color:var(--slate-400);margin-top:3px;"><i class="ph ph-users me-1"></i>${numF(flw)} followers</div>`:''}</div></div>${dtF?`<div class="do-dp2-meta">${dtF}</div>`:''}<div class="do-dp2-sent do-dp2-sent--${sn}">${sl}</div>${ct?`<div class="do-dp2-content">${esc(ct)}</div>`:''}<div class="do-dp2-stats"><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(v)}</div><div class="do-dp2-stat-lbl">Views</div></div><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(rt)}</div><div class="do-dp2-stat-lbl">Retweets</div></div></div>${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out me-1"></i>Buka di X</a>`:''}`;
        panel.classList.add('show');
    },
    close(){ _$('ovDetailPanel')?.classList.remove('show'); }
};

/* ════════════════════════════════════════════════════════
   OVExport — v4 FIXED
   ✓ Semua 3 tab ter-capture (hashtag, view, retweet)
   ✓ Tidak ada duplikasi konten
   ✓ Tidak ada konten terpotong (full scrollHeight)
   ✓ Semua animasi dimatikan sebelum capture di dokumen asli
════════════════════════════════════════════════════════ */
const OVExport = (() => {
    'use strict';
    let _toastTimer  = null;
    let _ecSnapshots = {};

    function _toast(msg, type='default', duration=3200) {
        const t=_$('exportToast'), m=_$('exportToastMsg'), ico=_$('exportToastIcon');
        if(!t||!m) return;
        m.textContent=msg; t.className='export-toast show '+(type!=='default'?type:'');
        ico.className='ph '+({success:'ph-check-circle',error:'ph-x-circle',default:'ph-spinner'}[type]||'ph-spinner');
        clearTimeout(_toastTimer); _toastTimer=setTimeout(()=>t.classList.remove('show'),duration);
    }
    function _btnState(btn,loading){ if(!btn)return; btn.disabled=loading; btn.classList.toggle('exporting',loading); }
    function _freeze() {
        if(document.getElementById('__s_freeze')) return;
        const s = document.createElement('style'); s.id = '__s_freeze';
        s.textContent = '*,*::before,*::after{animation:none!important;transition:none!important;animation-play-state:paused!important;}';
        document.head.appendChild(s);
    }
    function _unfreeze() { document.getElementById('__s_freeze')?.remove(); }
     

    /* ── Matikan SEMUA animasi di dokumen asli (live DOM) ── */
    function _killAnimations(){
        if(document.getElementById('__ov_kill_anim')) return;
        const s=document.createElement('style'); s.id='__ov_kill_anim';
        s.textContent=[
            '*,*::before,*::after{',
            '  animation:none!important;',
            '  animation-duration:0.001ms!important;',
            '  animation-delay:0ms!important;',
            '  animation-iteration-count:1!important;',
            '  animation-play-state:paused!important;',
            '  transition:none!important;',
            '  transition-duration:0.001ms!important;',
            '}',
            /* Paksa elemen dengan fadeUp animation langsung visible */
            '.fade-up,.fade-up-d1,.fade-up-d2,',
            '[style*="animation"],[style*="fadeUp"]{',
            '  opacity:1!important;transform:none!important;',
            '}',
            /* Paksa semua card visible */
            '.card{opacity:1!important;transform:none!important;}',
            /* Paksa tab aktif full visible */
            '.tme-tab-panel.active{display:block!important;opacity:1!important;visibility:visible!important;}',
            /* Hentikan shimmer skeleton */
            '.sk-block{animation:none!important;}',
        ].join('');
        document.head.appendChild(s);
    }
    function _restoreAnimations(){ document.getElementById('__ov_kill_anim')?.remove(); }

    /* ── ECharts snapshot map ── */
    const EC_ID_MAP = {
        '__ec_donutHashtagChart' : 'donutHashtagChart',
        '__ec_donutChart_view'   : 'donutChart-view',
        '__ec_donutChart_retweet': 'donutChart-retweet',
    };
    /* Hanya snapshot chart tab tertentu (hindari cross-tab contamination) */
    const TAB_EC_MAP = {
        hashtag : ['__ec_donutHashtagChart'],
        view    : ['__ec_donutChart_view'],
        retweet : ['__ec_donutChart_retweet'],
    };

    function _snapshotAll(){
        _ecSnapshots={};
        Object.entries(EC_ID_MAP).forEach(([wk,containerId])=>{
            const inst=window[wk]; if(!inst||inst.isDisposed?.()) return;
            try{ _ecSnapshots[containerId]=inst.getDataURL({type:'png',pixelRatio:window.devicePixelRatio||2,backgroundColor:'#ffffff'}); }
            catch(e){}
        });
    }
    function _snapshotTab(tabKey){
        _ecSnapshots={};
        (TAB_EC_MAP[tabKey]||[]).forEach(wk=>{
            const containerId=EC_ID_MAP[wk]; if(!containerId) return;
            const inst=window[wk]; if(!inst||inst.isDisposed?.()) return;
            try{ _ecSnapshots[containerId]=inst.getDataURL({type:'png',pixelRatio:window.devicePixelRatio||2,backgroundColor:'#ffffff'}); }
            catch(e){}
        });
    }

    /* ── onClone: bersihkan cloned doc untuk capture bersih ── */
    function _onClone(clonedDoc){
        /* 1. Sembunyikan elemen yang tidak perlu di output */
        clonedDoc.querySelectorAll([
            '.do-panel-overlay','.do-panel','.do-detail-panel','#ovCustomTT',
            '.spin-ring','.spinner-state','.export-toast',
            '.chart-loading:not(.hidden)',
            '[data-html2canvas-ignore]',
        ].join(',')).forEach(el=>{
            el.style.cssText+='display:none!important;visibility:hidden!important;opacity:0!important;height:0!important;min-height:0!important;overflow:hidden!important;';
        });

        /* 2. Sembunyikan tab panel yang TIDAK aktif */
        clonedDoc.querySelectorAll('.tme-tab-panel:not(.active)').forEach(el=>{
            el.style.cssText+='display:none!important;height:0!important;min-height:0!important;overflow:hidden!important;';
        });

        /* 3. Matikan SEMUA animasi di cloned doc */
        const freezeStyle=clonedDoc.createElement('style');
        freezeStyle.textContent=[
            '*,*::before,*::after{',
            '  animation:none!important;',
            '  animation-duration:0.001ms!important;',
            '  animation-play-state:paused!important;',
            '  transition:none!important;',
            '}',
            '.fade-up,.fade-up-d1,.fade-up-d2,',
            '[style*="fadeUp"],[style*="animation"]{',
            '  opacity:1!important;transform:none!important;',
            '}',
        ].join('');
        clonedDoc.head.appendChild(freezeStyle);

        /* 4. Ganti avatar cross-origin dengan initial letter */
        clonedDoc.querySelectorAll('.tme-post-av,.do-panel-avatar,.do-dp2-avatar-lg').forEach(wrapper=>{
            wrapper.querySelectorAll('img').forEach(img=>{ img.style.display='none'; });
            if(!wrapper.querySelector('.__ini')){
                const initial=((wrapper.textContent||'').trim()[0]||'X').toUpperCase();
                const sp=clonedDoc.createElement('span'); sp.className='__ini';
                sp.textContent=initial;
                sp.style.cssText='font-size:13px;font-weight:700;color:#fff;line-height:1;';
                wrapper.appendChild(sp);
            }
            if(!wrapper.style.background) wrapper.style.background='linear-gradient(135deg,#038047,#05a85e)';
        });
        clonedDoc.querySelectorAll('.tme-post-thumb').forEach(wrapper=>{
            wrapper.querySelectorAll('img').forEach(img=>{ img.style.display='none'; });
            wrapper.style.background='linear-gradient(135deg,#273B4A,#374151)';
        });

        /* 5. Paksa semua elemen konten visible & opacity 1 */
        clonedDoc.querySelectorAll([
            '.card','.card-body','.card-header','.row','[class*="col-"]',
            '.tme-tab-panel.active','.ht-list','.ht-item',
            '.tme-post-list','.tme-post','.donut-stack-wrap','.donut-breakdown-list',
            '.donut-breakdown-item','#pageExportArea','#ovTabsBar',
            '.kpi-card-hover','.kpi-icon-bg',
        ].join(',')).forEach(el=>{
            el.style.opacity='1';
            el.style.transform='none';
            el.style.visibility='visible';
            el.style.animationPlayState='paused';
        });

        /* 6. ★ Inject ECharts snapshot sebagai <img> ★ */
        Object.entries(_ecSnapshots).forEach(([containerId,dataUrl])=>{
            const container=clonedDoc.getElementById(containerId); if(!container) return;
            container.innerHTML='';
            const img=clonedDoc.createElement('img');
            img.src=dataUrl;
            img.style.cssText='width:100%;height:100%;display:block;object-fit:contain;';
            container.appendChild(img);
            container.style.cssText+='display:block!important;opacity:1!important;visibility:visible!important;';
        });
    }

    /* ── _doCapture: capture elemen dengan animasi mati total ── */
    async function _doCapture(el, isCard, skipSnapshot=false){
        if(!skipSnapshot) _snapshotAll();

        /* Matikan animasi di live DOM SEBELUM ukur height */
        _killAnimations();

        /* Tunggu browser reflow setelah animasi dimatikan */
        await new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r)));
        await new Promise(r=>setTimeout(r,250));

        /* Ukur full height SETELAH animasi berhenti, agar tidak terpotong */
        const captureH = Math.max(
            el.scrollHeight,
            el.offsetHeight,
            el.getBoundingClientRect().height
        );

        let canvas;
        try{
            canvas=await html2canvas(el,{
                scale:2,
                useCORS:true,
                allowTaint:true,
                backgroundColor:isCard?'#ffffff':'#f1f5f9',
                logging:false,
                removeContainer:true,
                imageTimeout:8000,
                onclone:d=>_onClone(d),
                ignoreElements:e=>e.hasAttribute('data-html2canvas-ignore'),
                x:0,
                y:0,
                width:el.offsetWidth,
                height:captureH,
                scrollX:0,
                scrollY:0,
            });
        }finally{
            _restoreAnimations();
        }
        return canvas;
    }

    /* ── PDF helpers ── */
    function _drawHeader(pdf,pW,pH,label,page,total){
        pdf.setFillColor(3,128,71); pdf.rect(0,0,pW,11,'F');
        pdf.setTextColor(255,255,255); pdf.setFontSize(9); pdf.setFont('helvetica','bold');
        pdf.text('SMADIMENT — '+(label||'X Overview'),10,7.5);
        const now=new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
        pdf.setFontSize(7); pdf.setFont('helvetica','normal'); pdf.text('Generated: '+now,pW-10,7.5,{align:'right'});
        pdf.setFontSize(7); pdf.setTextColor(148,163,184); pdf.text(`Halaman ${page} / ${total}`,pW/2,pH-3,{align:'center'});
    }
    function _addCanvas(pdf,canvas,margin,pW,pH){
        const uw=pW-margin*2, uh=pH-14-10;
        const ratio=Math.min(uw/canvas.width, uh/canvas.height);
        const dw=canvas.width*ratio, dh=canvas.height*ratio;
        pdf.addImage(canvas.toDataURL('image/png'),'PNG',margin+(uw-dw)/2,14+(uh-dh)/2,dw,dh);
    }
    function _sliceIntoPages(pdf,canvas,margin,pW,pH,label,startPageNum,totalPages){
        const uw=pW-margin*2, uh=pH-14-10;
        const ratio=uw/canvas.width;
        const sliceH=uh/ratio;
        let srcY=0, pg=startPageNum, firstInTab=true;
        while(srcY<canvas.height){
            if(!firstInTab) pdf.addPage(); firstInTab=false;
            _drawHeader(pdf,pW,pH,label,pg,totalPages);
            const srcSlice=Math.min(sliceH, canvas.height-srcY);
            const dstH=srcSlice*ratio;
            const slice=document.createElement('canvas');
            slice.width=canvas.width; slice.height=Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(canvas,0,srcY,canvas.width,srcSlice,0,0,canvas.width,srcSlice);
            pdf.addImage(slice.toDataURL('image/png'),'PNG',margin,14,uw,dstH);
            srcY+=srcSlice; pg++;
        }
        return pg; /* return next page number */
    }

    /* ══════════════════════════════════════════════
       run() — export semua 3 tab ke PDF / image
    ══════════════════════════════════════════════ */
    async function run(type,btn){
        if(!window.html2canvas){ _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf'&&!window.jspdf?.jsPDF){ _toast('jsPDF tidak tersedia','error'); return; }

        const btnPdf=_$('pageExportPdfBtn'), btnImg=_$('pageExportImgBtn');
        _btnState(btnPdf,true); _btnState(btnImg,true);
        _toast(type==='pdf'?'Menyiapkan PDF semua tab…':'Mengambil gambar tab aktif…','default',99999);

        const originalTab=TAB_TYPES.find(t=>_$('tab-'+t)?.classList.contains('active'))||'hashtag';
        const area=_$('pageExportArea');
        const stamp=new Date().toISOString().slice(0,10).replace(/-/g,'');

        try{
            /* ── IMAGE: capture tab aktif saja ── */
            if(type==='image'){
                const canvas=await _doCapture(area,false);
                const link=document.createElement('a');
                link.download=`x_overview_${OV_PID}_${stamp}.png`;
                link.href=canvas.toDataURL('image/png');
                link.click();
                _toast('Gambar berhasil diunduh!','success');
                return;
            }

            /* ── PDF: capture semua 3 tab secara berurutan ── */
            const TAB_ORDER=[
                {key:'hashtag', label:'Top Topics'},
                {key:'view',    label:'Most Viewed'},
                {key:'retweet', label:'Most Retweeted'},
            ];

            /* Step 1 — pastikan semua tab sudah di-load datanya */
            _toast('Memuat semua tab…','default',99999);
            for(const {key} of TAB_ORDER){
                if(!OVTab._loaded[key]){
                    OVTab._loaded[key]=true;
                    await OVData.loadTab(key);
                    await new Promise(r=>setTimeout(r,600));
                }
            }

            /* Step 2 — capture tiap tab satu per satu */
            const canvases=[];
            for(let i=0;i<TAB_ORDER.length;i++){
                const {key,label}=TAB_ORDER[i];
                _toast(`Mengambil tab ${i+1}/3: ${label}…`,'default',99999);

                /* Aktifkan tab tanpa animasi scroll */
                TAB_TYPES.forEach(x=>{
                    _$('tab-'+x)?.classList.toggle('active',x===key);
                    _$('panel-'+x)?.classList.toggle('active',x===key);
                });

                /* Resize chart tab ini agar dimensi benar */
                (TAB_EC_MAP[key]||[]).forEach(wk=>{
                    try{ if(window[wk]&&!window[wk].isDisposed()) window[wk].resize(); }catch(e){}
                });

                /* Tunggu render selesai */
                await new Promise(r=>setTimeout(r,800));

                /* Snapshot HANYA chart tab aktif */
                _snapshotTab(key);

                /* Capture dengan animasi mati */
                const canvas=await _doCapture(area, false, true); /* skipSnapshot=true */
                canvases.push({label:`X Overview — ${label}`, canvas});
            }

            /* Step 3 — hitung total halaman */
            const {jsPDF}=window.jspdf;
            const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
            const pW=pdf.internal.pageSize.getWidth(), pH=pdf.internal.pageSize.getHeight(), M=10;
            const usableW=pW-M*2, usableH=pH-14-10;

            let totalPages=0;
            canvases.forEach(({canvas})=>{
                const ratio=usableW/canvas.width;
                totalPages+=Math.max(1, Math.ceil((canvas.height*ratio)/usableH));
            });

            /* Step 4 — build PDF dengan slicing */
            let pageNum=1, firstPage=true;
            canvases.forEach(({label,canvas})=>{
                if(!firstPage) pdf.addPage(); firstPage=false;
                pageNum=_sliceIntoPages(pdf,canvas,M,pW,pH,label,pageNum,totalPages);
            });

            pdf.save(`x_overview_${OV_PID}_${stamp}.pdf`);
            _toast(`PDF ${totalPages} halaman berhasil diunduh!`,'success');

        }catch(err){
            console.error('[OVExport.run]',err);
            _restoreAnimations();
            _toast('Export gagal: '+err.message,'error');
        }finally{
            /* Kembalikan ke tab semula */
            TAB_TYPES.forEach(x=>{
                _$('tab-'+x)?.classList.toggle('active',x===originalTab);
                _$('panel-'+x)?.classList.toggle('active',x===originalTab);
            });
            _btnState(btnPdf,false); _btnState(btnImg,false);
        }
    }

    /* ══════════════════════════════════════════════
       runCard() — export satu card saja
    ══════════════════════════════════════════════ */
    const _cardLabels={
        'hashtag-list':'Top Topics / Hashtags','hashtag-donut':'Distribusi Top Topics',
        'donut-view':'Distribusi Most Viewed','donut-retweet':'Distribusi Most Retweeted',
        'list-view':'Top Tweets by Views','list-retweet':'Top Tweets by Retweets'
    };
    function _cardFilename(k){
        const map={'hashtag-list':'top-topics-list','hashtag-donut':'top-topics-donut','donut-view':'distribusi-most-viewed','donut-retweet':'distribusi-most-retweeted','list-view':'top-tweets-by-view','list-retweet':'top-tweets-by-retweet'};
        return `x_overview_${map[k]||k}_${OV_PID}_${new Date().toISOString().slice(0,10).replace(/-/g,'')}`;
    }

    async function runCard(areaId,cardKey,type,btn){
        if(!window.html2canvas){ _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf'&&!window.jspdf?.jsPDF){ _toast('jsPDF tidak tersedia','error'); return; }
        _btnState(btn,true); _toast(type==='pdf'?'Menyiapkan PDF…':'Mengambil gambar…','default',99999);
        try{
            const area=document.getElementById(areaId);
            if(!area) throw new Error('Area #'+areaId+' tidak ditemukan');
            const canvas=await _doCapture(area, true);
            const fname=_cardFilename(cardKey), label=_cardLabels[cardKey]||cardKey;
            if(type==='image'){
                const link=document.createElement('a'); link.download=fname+'.png'; link.href=canvas.toDataURL('image/png'); link.click();
                _toast('Gambar berhasil diunduh!','success');
            }else{
                const {jsPDF}=window.jspdf;
                const landscape=canvas.width>canvas.height*1.2;
                const pdf=new jsPDF({orientation:landscape?'landscape':'portrait',unit:'mm',format:'a4'});
                const pW=pdf.internal.pageSize.getWidth(), pH=pdf.internal.pageSize.getHeight(), M=10;
                const uw=pW-M*2, uh=pH-14-10;
                const fitsOne=(canvas.height*(uw/canvas.width))<=uh;
                if(fitsOne){
                    _drawHeader(pdf,pW,pH,label,1,1); _addCanvas(pdf,canvas,M,pW,pH);
                }else{
                    const ratio=uw/canvas.width, sliceH=uh/ratio;
                    const total=Math.max(1,Math.ceil((canvas.height*ratio)/uh));
                    _sliceIntoPages(pdf,canvas,M,pW,pH,label,1,total);
                }
                pdf.save(fname+'.pdf'); _toast('PDF berhasil diunduh!','success');
            }
        }catch(err){
            console.error('[OVExport.runCard]',err); _toast('Export gagal: '+err.message,'error');
        }finally{ _btnState(btn,false); }
    }

    return {run,runCard};
})();

/* ── INIT ── */
document.addEventListener('DOMContentLoaded',()=>{
    if(!OVCfg.pid) return;
    OVTab._loaded.hashtag=true;
    OVData.loadHashtags();
    OVData._ensureEngagement().then(()=>{
        OVTab._loaded.view=true;
        OVData._sortAndDisplay('view');
    });
    document.addEventListener('keydown',e=>{ if(e.key==='Escape') OVPanel.close(); });
});
</script>
@endsection