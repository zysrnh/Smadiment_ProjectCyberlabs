@extends('mk.layouts.app')

@section('title', 'Instagram Most Engagement - SMADIMENT')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════
   DESIGN TOKENS — mirrored from dashboard
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

@keyframes ttDotPulse { 0%,100%{ box-shadow:0 0 0 3px var(--c,rgba(3,128,71,.3)); } 50%{ box-shadow:0 0 0 6px transparent; } }
@keyframes fadeUp   { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer  { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin     { to{transform:rotate(360deg)} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1}    to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn  { from{opacity:0} to{opacity:1} }
@keyframes overlayOut { from{opacity:1} to{opacity:0} }
@keyframes kpiShimmer { 0%{left:-100%} 100%{left:150%} }
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0deg);} 30%{transform:scale(1.25) rotate(-10deg);} 60%{transform:scale(1.1) rotate(6deg);} }

.kpi-icon-bg { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0; }
.sk-block { border-radius:4px;background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite; }
.spin-ring { width:26px;height:26px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite; }
.spinner-state { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;gap:12px;color:var(--slate-400);font-size:12px;font-weight:600; }

.tme-tabs { display:flex;gap:2px;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px;margin-bottom:16px; }
.tme-tab-btn { flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:7px 14px;border-radius:4px;border:none;background:transparent;font-size:12px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:background .13s,color .13s;white-space:nowrap; }
.tme-tab-btn:hover { background:#fff;color:var(--slate-800); }
.tme-tab-btn.active { background:#fff;color:var(--primary);box-shadow:0 1px 4px rgba(0,0,0,.08); }
.tme-tab-chip { display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:16px;padding:0 5px;border-radius:3px;font-size:9px;font-weight:800;background:var(--primary-lt);color:var(--primary); }
.tme-tab-btn:not(.active) .tme-tab-chip { background:var(--slate-100);color:var(--slate-400); }
.tme-tab-panel { display:none; }
.tme-tab-panel.active { display:block; }

.tme-toggle-group { display:flex;background:var(--slate-50);border-radius:var(--radius-sm);padding:2px;gap:2px;border:1px solid var(--slate-200); }
.tme-toggle-btn { display:flex;align-items:center;gap:4px;padding:4px 10px;border-radius:3px;border:none;background:transparent;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:background .12s,color .12s; }
.tme-toggle-btn:hover { background:#fff;color:var(--slate-800); }
.tme-toggle-btn.active { background:#fff;color:var(--primary);box-shadow:0 1px 3px rgba(0,0,0,.07); }

.tme-post-list { display:flex;flex-direction:column; }
.tme-post { display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--slate-100);transition:background .12s;cursor:pointer; }
.tme-post:last-child { border-bottom:none; }
.tme-post:hover { background:var(--slate-50); }
.tme-post-rank { width:22px;height:22px;border-radius:50%;background:var(--slate-100);border:1px solid var(--slate-200);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:var(--slate-400);flex-shrink:0;margin-top:8px; }
.tme-post-rank--1 { background:linear-gradient(135deg,#ffd700,#F59E0B);color:#7c5900;border-color:#ffd700; }
.tme-post-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0; }
.tme-post-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32; }
.tme-post-av { width:36px;height:36px;border-radius:50%;flex-shrink:0;color:#fff;font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--slate-200);overflow:hidden; }
.tme-post-av img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
.tme-post-body { flex:1;min-width:0; }
.tme-post-author { font-size:12.5px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.tme-post-date { font-size:10px;color:var(--slate-400);margin-top:1px;margin-bottom:4px; }
.tme-post-text { font-size:11.5px;color:var(--slate-500);line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:6px;word-break:break-word; }
.tme-post-stats { display:flex;align-items:center;gap:4px;flex-wrap:wrap; }
.tme-metric { display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:3px;font-size:10px;font-weight:700;background:var(--slate-100);color:var(--slate-500);white-space:nowrap; }
.tme-metric--primary { background:var(--primary-lt);color:var(--primary); }
.tme-metric--ig { background:rgba(230,104,60,.1);color:#c2410c; }
.tme-metric--amber { background:rgba(245,158,11,.1);color:#92400e; }
.tme-sent { display:inline-flex;align-items:center;padding:2px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.3px; }
.tme-sent--pos { background:#d1fae5;color:#065f46; }
.tme-sent--neg { background:#fee2e2;color:#991b1b; }
.tme-sent--neu { background:var(--slate-100);color:var(--slate-500); }
.tme-view-link { display:inline-flex;align-items:center;gap:3px;font-size:9.5px;font-weight:700;color:var(--primary);text-decoration:none;padding:2px 6px;border-radius:3px;background:var(--primary-lt);border:1px solid rgba(3,128,71,.2);transition:background .12s,color .12s;margin-left:auto; }
.tme-view-link:hover { background:var(--primary);color:#fff; }
.tme-type-badge { display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.3px;background:var(--slate-100);color:var(--slate-500); }
.tme-type-badge--video { background:#dbeafe;color:#1d4ed8; }
.tme-type-badge--image { background:#fce7f3;color:#9d174d; }

.tme-pagination { display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--slate-100);flex-wrap:wrap;gap:8px; }
.tme-pag-info { font-size:11px;color:var(--slate-400);font-weight:500; }
.tme-pag-controls { display:flex;align-items:center;gap:3px; }
.tme-pag-btn { min-width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;padding:0 6px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .12s;user-select:none; }
.tme-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary);color:var(--primary);background:var(--primary-lt); }
.tme-pag-btn.is-active { background:var(--primary);border-color:var(--primary);color:#fff; }
.tme-pag-btn:disabled { opacity:.35;cursor:not-allowed; }

.chart-container { height:280px;position:relative; }
.chart-loading { position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;background:#fff;z-index:2;transition:opacity .3s; }
.chart-loading.hidden { opacity:0;pointer-events:none; }
.chart-loading span { font-size:11px;font-weight:600;color:var(--slate-400); }
.chart-empty { height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600; }
.chart-empty i { font-size:34px;color:var(--slate-300);display:block; }

.do-panel-overlay { position:fixed;inset:0;z-index:9000;background:rgba(15,23,42,.45);backdrop-filter:blur(4px);display:none; }
.do-panel-overlay.show { display:block;animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }
.do-panel { position:fixed;top:0;right:0;bottom:0;z-index:9001;width:480px;max-width:100vw;background:#fff;display:none;flex-direction:column;border-left:1px solid var(--slate-200);box-shadow:-8px 0 40px rgba(15,23,42,.16); }
.do-panel.show { display:flex;animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }
.do-panel-header { display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid var(--slate-200);background:var(--slate-50);flex-shrink:0; }
.do-panel-dot { width:9px;height:9px;border-radius:50%;flex-shrink:0; }
.do-panel-title { font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.do-panel-close { width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);font-size:16px;transition:all .14s;flex-shrink:0; }
.do-panel-close:hover { background:var(--red);border-color:var(--red);color:#fff; }
.do-panel-actions { display:flex;align-items:center;gap:7px;padding:7px 12px;border-bottom:1px solid var(--slate-200);background:#fff;flex-shrink:0; }
.do-panel-meta { flex:1;font-size:10px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:5px; }
.do-panel-list { overflow-y:auto;flex:1;padding:2px 0;min-height:0; }
.do-panel-list::-webkit-scrollbar { width:4px; }
.do-panel-list::-webkit-scrollbar-thumb { background:var(--slate-200);border-radius:99px; }
.do-panel-item { display:flex;gap:10px;padding:10px 14px;border-bottom:1px solid var(--slate-50);cursor:pointer;transition:background .1s;align-items:flex-start; }
.do-panel-item:hover { background:#f0f9ff; }
.do-panel-item:last-child { border-bottom:none; }
.do-panel-avatar { width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#fff;border:1.5px solid var(--slate-200);overflow:hidden; }
.do-panel-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
.do-panel-item-body { flex:1;min-width:0; }
.do-panel-author { font-size:12px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.do-panel-text { font-size:11px;color:var(--slate-600);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px; }
.do-panel-footer { display:flex;align-items:center;gap:5px;font-size:10px;color:var(--slate-400);flex-wrap:wrap; }
.do-sent-badge { padding:1px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe;color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2;color:#991b1b; }
.do-sent-badge--neu { background:var(--slate-100);color:var(--slate-500); }
.do-panel-loading { display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:12px;color:var(--slate-400);font-size:13px;font-weight:600; }
.do-panel-spinner { width:28px;height:28px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite; }

.do-detail-panel { position:absolute;inset:0;background:#fff;z-index:5;display:none;flex-direction:column;animation:slideInRight .2s cubic-bezier(.4,0,.2,1); }
.do-detail-panel.show { display:flex; }
.do-dp2-header { display:flex;align-items:center;gap:8px;padding:12px 14px;background:var(--slate-50);border-bottom:1px solid var(--slate-200);flex-shrink:0; }
.do-dp2-back { width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);transition:all .13s;font-size:14px; }
.do-dp2-back:hover { background:var(--primary-lt);color:var(--primary);border-color:var(--primary); }
.do-dp2-title { font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.do-dp2-body { overflow-y:auto;flex:1;padding:16px; }
.do-dp2-body::-webkit-scrollbar { width:4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200);border-radius:99px; }
.do-dp2-avatar-row { display:flex;align-items:center;gap:10px;margin-bottom:12px; }
.do-dp2-avatar-lg { width:46px;height:46px;border-radius:50%;color:#fff;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;border:2px solid var(--slate-200);overflow:hidden;flex-shrink:0; }
.do-dp2-avatar-lg img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
.do-dp2-name { font-size:14px;font-weight:700;color:var(--slate-900); }
.do-dp2-plat-badge { display:inline-block;padding:2px 8px;border-radius:3px;font-size:10px;font-weight:700;margin-top:3px; }
.do-dp2-meta { font-size:11px;color:var(--slate-400);font-weight:500;margin-bottom:10px; }
.do-dp2-sent { display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:3px;font-size:11px;font-weight:700;margin-bottom:10px; }
.do-dp2-sent--pos { background:#dbeafe;color:#1d4ed8; }
.do-dp2-sent--neg { background:#fee2e2;color:#991b1b; }
.do-dp2-sent--neu { background:var(--slate-100);color:var(--slate-500); }
.do-dp2-content { font-size:12px;color:var(--slate-700);line-height:1.7;margin-bottom:12px;background:var(--slate-50);border-radius:var(--radius-sm);padding:10px 12px;border:1px solid var(--slate-200);word-break:break-word; }
.do-dp2-stats { display:grid;grid-template-columns:repeat(2,1fr);gap:6px;margin-bottom:10px; }
.do-dp2-stat { background:var(--slate-50);border-radius:var(--radius-sm);padding:8px 10px;border:1px solid var(--slate-200);text-align:center; }
.do-dp2-stat-val { font-size:14px;font-weight:700;color:var(--slate-900); }
.do-dp2-stat-lbl { font-size:9px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.4px;margin-top:1px; }
.do-dp2-img-wrap { border-radius:var(--radius-sm);overflow:hidden;margin-bottom:12px;border:1px solid var(--slate-200);max-height:300px; }
.do-dp2-img-wrap img { width:100%;display:block;object-fit:cover; }
.do-dp2-link { display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:#e6683c;color:#fff;border-radius:var(--radius-sm);font-size:12px;font-weight:700;text-decoration:none;transition:filter .14s;margin-top:4px; }
.do-dp2-link:hover { filter:brightness(1.1);color:#fff; }

.tme-rows-sel { padding:4px 9px;border:1px solid var(--slate-200);border-radius:var(--radius-sm);font-size:11px;font-weight:600;color:var(--slate-600);background:var(--slate-50);outline:none;cursor:pointer;transition:border-color .14s; }
.tme-rows-sel:focus { border-color:var(--primary); }

.donut-legend { display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:10px; }
.donut-leg-item { display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--slate-500);padding:3px 8px;background:var(--slate-50);border-radius:3px;border:1px solid var(--slate-200);cursor:pointer;transition:border-color .12s,background .12s,color .12s; }
.donut-leg-item:hover { border-color:var(--primary);background:var(--primary-lt);color:var(--primary); }
.donut-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }

.kpi-card-hover { will-change:transform,box-shadow;cursor:default;position:relative!important;overflow:hidden!important;transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important; }
.kpi-card-hover::before { content:'';position:absolute;top:0;bottom:0;left:-100%;width:60%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);pointer-events:none;z-index:1;transition:none; }
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025)!important;box-shadow:0 20px 40px rgba(0,0,0,.25)!important;filter:brightness(1.07)!important; }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background:rgba(255,255,255,.35)!important;transition:background .2s ease!important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important;display:inline-block!important; }
.kpi-card-hover:active { transform:translateY(-2px) scale(1.01)!important;transition-duration:.08s!important; }

/* ══════════════════════════════════════════════════════
   EXPORT STYLES
══════════════════════════════════════════════════════ */
.page-export-bar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px;
    background:#fff; border:1px solid var(--slate-200);
    border-radius:var(--radius); padding:9px 14px;
    margin-bottom:20px; box-shadow:var(--shadow-sm);
}
.page-export-bar-left { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--slate-600); }
.page-export-bar-left i { font-size:15px; color:var(--primary); }
.page-export-bar-right { display:flex; gap:8px; }

.page-export-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:32px; height:32px; border-radius:var(--radius-sm);
    font-size:16px; cursor:pointer; transition:all .15s ease;
    border:1.5px solid transparent; font-family:inherit;
}
.page-export-btn-pdf { background:#fff3f3; color:#dc2626; border-color:#fca5a5; }
.page-export-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.page-export-btn-img { background:var(--primary-lt); color:var(--primary); border-color:rgba(3,128,71,.3); }
.page-export-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.page-export-btn:disabled { opacity:.55; cursor:not-allowed; pointer-events:none; }
.page-export-btn .export-spinner { width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none; }
.page-export-btn.exporting .export-spinner { display:inline-block; }
.page-export-btn.exporting .export-icon    { display:none; }

.card-exp-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:var(--radius-sm);
    font-size:14px; cursor:pointer; flex-shrink:0;
    transition:all .14s ease; border:1px solid transparent;
    font-family:inherit; background:transparent;
}
.card-exp-btn-pdf { color:#dc2626; border-color:#fca5a5; background:#fff3f3; }
.card-exp-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.card-exp-btn-img { color:var(--primary); border-color:rgba(3,128,71,.3); background:var(--primary-lt); }
.card-exp-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
.card-exp-btn .export-spinner { width:11px;height:11px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none; }
.card-exp-btn.exporting .export-spinner { display:inline-block; }
.card-exp-btn.exporting .export-icon    { display:none; }

.export-toast {
    position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px);
    background:var(--slate-900); color:#fff; border-radius:var(--radius);
    padding:10px 18px; font-size:12px; font-weight:600;
    box-shadow:var(--shadow-lg); z-index:99999;
    opacity:0; pointer-events:none;
    transition:opacity .22s ease, transform .22s ease;
    display:flex; align-items:center; gap:8px; white-space:nowrap;
}
.export-toast.show    { opacity:1; transform:translateX(-50%) translateY(0); }
.export-toast.success { background:#065f46; }
.export-toast.error   { background:#991b1b; }

@media(max-width:640px) { .do-panel{width:100vw;} .tme-tabs{flex-wrap:wrap;} .tme-tab-btn{flex:unset;min-width:calc(50% - 4px);} }
</style>
@endsection

@section('page-title', 'Instagram Most Engagement')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $endDate ?? request()->get('end_date', now()->format('Y-m-d'));
    $projects  = $projects ?? [];
@endphp

<script>
    const IME_PID = {{ $projectId ? (int)$projectId : 'null' }};
    const IME_SD  = '{{ $startDate }}';
    const IME_ED  = '{{ $endDate }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
</script>

@include('mk.layouts.partials.filter-datepicker')

{{-- ════ PAGE EXPORT WRAPPER ════ --}}
<div id="pageExportArea">

{{-- ══ KPI Cards ══ --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 text-white kpi-card-hover" style="background:#e6683c;animation:fadeUp .38s ease-out both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Likes</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiLikes">�</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiLikesSub"><i class="ph ph-chart-line-up me-1"></i>Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-heart"></i></div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 bg-warning text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Comments</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiCmts">�</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiCmtsSub"><i class="ph ph-chart-line-up me-1"></i>Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chat-circle"></i></div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 bg-primary text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Posts</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiPosts">�</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPostsSub"><i class="ph ph-chart-line-up me-1"></i>Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-images"></i></div></div>
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
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Charts + Post List</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                onclick="IMEExport.run('pdf',this)" title="Export halaman sebagai PDF">
            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                onclick="IMEExport.run('image',this)" title="Export halaman sebagai PNG">
            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
        </button>
    </div>
</div>

{{-- ══ Donut Distribution ══ --}}
<div class="row">
    <div class="col-12">
        <div class="card" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-export-donut">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
                    <div>
                        <h6 class="mb-0" id="donutTitle">Distribusi Likes — Top 5</h6>
                        <small class="text-muted">Proporsi engagement per post</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div id="donutLegend" class="donut-legend"></div>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf"
                                onclick="IMEExport.runCard('card-export-donut','donut','pdf',this)" title="Export PDF">
                            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                        </button>
                        <button class="card-exp-btn card-exp-btn-img"
                                onclick="IMEExport.runCard('card-export-donut','donut','image',this)" title="Export PNG">
                            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:480px;" id="donutWrap">
                    <div class="chart-loading" id="donutLoading"><div class="spin-ring"></div><span>Loading chart…</span></div>
                    <div id="donutChart" style="width:100%;height:480px;display:none;"></div>
                    <div id="donutEmpty" style="display:none;" class="chart-empty"><i class="ph ph-chart-donut"></i><span>No data</span></div>
                </div>
            </div>
            </div>{{-- /card-export-donut --}}
        </div>
    </div>
</div>

{{-- ══ TABS ══ --}}
<div class="tme-tabs">
    <button class="tme-tab-btn active" id="tab-like" onclick="IMETab.show('like')"><i class="ph ph-heart"></i> Most Liked <span class="tme-tab-chip" id="chip-like">—</span></button>
    <button class="tme-tab-btn" id="tab-comment" onclick="IMETab.show('comment')"><i class="ph ph-chat-circle"></i> Most Comments <span class="tme-tab-chip" id="chip-comment">—</span></button>
</div>

@foreach(['like','comment'] as $tp)
@php
    $panelLabels = ['like'=>'Most Liked','comment'=>'Most Comments'];
    $panelIcons  = ['like'=>'ph-heart','comment'=>'ph-chat-circle'];
@endphp
<div class="tme-tab-panel {{ $tp === 'like' ? 'active' : '' }}" id="panel-{{ $tp }}">

    {{-- Post List Card --}}
    <div class="card mb-3" style="animation:fadeUp .38s ease-out .2s both;">
        <div id="card-export-list-{{ $tp }}">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph {{ $panelIcons[$tp] }} f-18 text-primary"></i></div>
                <div><h6 class="mb-0">Top Posts by {{ $panelLabels[$tp] }}</h6><small class="text-muted">Klik post untuk lihat detail</small></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select class="tme-rows-sel" id="rows-{{ $tp }}" onchange="IMEData.reloadTab('{{ $tp }}')">
                    <option value="10">Top 10</option><option value="20">Top 20</option><option value="50">Top 50</option><option value="100" selected>Top 100</option>
                </select>
                <span class="badge bg-light-primary text-primary" id="badge-{{ $tp }}">Loading…</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf"
                            onclick="IMEExport.runCard('card-export-list-{{ $tp }}','list-{{ $tp }}','pdf',this)" title="Export PDF">
                        <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                    </button>
                    <button class="card-exp-btn card-exp-btn-img"
                            onclick="IMEExport.runCard('card-export-list-{{ $tp }}','list-{{ $tp }}','image',this)" title="Export PNG">
                        <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                    </button>
                </div>
            </div>
        </div>
        <div id="list-{{ $tp }}" class="p-0"><div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div></div>
        <div id="pag-{{ $tp }}"></div>
        </div>{{-- /card-export-list --}}
    </div>

    {{-- Bar Chart Card --}}
    <div class="card mb-3" style="animation:fadeUp .38s ease-out .25s both;">
        <div id="card-export-bar-{{ $tp }}">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-bar f-18 text-primary"></i></div>
                <div><h6 class="mb-0">{{ $panelLabels[$tp] }} Chart</h6><small class="text-muted">Top 10 posts</small></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light-secondary text-muted">Top 10</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf"
                            onclick="IMEExport.runCard('card-export-bar-{{ $tp }}','bar-{{ $tp }}','pdf',this)" title="Export PDF">
                        <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                    </button>
                    <button class="card-exp-btn card-exp-btn-img"
                            onclick="IMEExport.runCard('card-export-bar-{{ $tp }}','bar-{{ $tp }}','image',this)" title="Export PNG">
                        <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container" id="wrap-bar-{{ $tp }}">
                <div class="chart-loading" id="loading-bar-{{ $tp }}"><div class="spin-ring"></div><span>Loading…</span></div>
                <div id="bar-{{ $tp }}" style="width:100%;height:280px;display:none;"></div>
            </div>
        </div>
        </div>{{-- /card-export-bar --}}
    </div>

    {{-- Engagement comparison — like tab only --}}
    @if($tp === 'like')
    <div class="card mb-3" style="animation:fadeUp .38s ease-out .30s both;">
        <div id="card-export-eng">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-secondary rounded"><i class="ph ph-chart-bar-horizontal f-18 text-muted"></i></div>
                <div><h6 class="mb-0">Like vs Comment — Top 10</h6><small class="text-muted">Perbandingan engagement keseluruhan</small></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="tme-toggle-group" id="engTypeToggle">
                    <button class="tme-toggle-btn active" data-type="stacked" onclick="IMEChart.setEngType('stacked')">Stacked</button>
                    <button class="tme-toggle-btn" data-type="grouped" onclick="IMEChart.setEngType('grouped')">Grouped</button>
                </div>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf"
                            onclick="IMEExport.runCard('card-export-eng','eng','pdf',this)" title="Export PDF">
                        <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                    </button>
                    <button class="card-exp-btn card-exp-btn-img"
                            onclick="IMEExport.runCard('card-export-eng','eng','image',this)" title="Export PNG">
                        <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container" id="wrap-eng">
                <div class="chart-loading" id="loading-eng"><div class="spin-ring"></div><span>Loading…</span></div>
                <div id="bar-eng" style="width:100%;height:280px;display:none;"></div>
            </div>
        </div>
        </div>{{-- /card-export-eng --}}
    </div>
    @endif

</div>
@endforeach

{{-- /pageExportArea --}}
</div>

{{-- ══ Export Toast ══ --}}
<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

{{-- ══ Slide Panel ══ --}}
<div class="do-panel-overlay" id="imePanelOverlay" onclick="IMEPanel.close()"></div>
<div class="do-panel" id="imeSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="imePanelDot" style="background:#e6683c;"></div>
        <span class="do-panel-title" id="imePanelTitle">Instagram Posts</span>
        <button class="do-panel-close" onclick="IMEPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span id="imePanelMeta">—</span></div>
    </div>
    <div class="do-panel-list" id="imePanelList"></div>
    <div class="do-detail-panel" id="imeDetailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="IMEDetail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="imeDetailTitle">Detail</span>
            <button class="do-panel-close" onclick="IMEPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="imeDetailBody"></div>
    </div>
</div>

@endsection

@section('scripts')
{{-- ══ Export dependencies ══ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';
const IMECfg={pid:IME_PID,sd:IME_SD,ed:IME_ED,colors:{like:'#e6683c',comment:'#F59E0B'},perPage:10};
const DONUT_COLORS=['#038047','#273B4A','#F59E0B','#06B6D4','#EF4444'];
const _$=id=>document.getElementById(id);
const numF=n=>parseInt(n||0).toLocaleString('id-ID');
const numK=n=>{n=parseInt(n||0);return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n);};
const esc=s=>(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const dec=s=>{if(!s)return '';try{const f=decodeURIComponent(escape(s));if(!f.includes('\uFFFD')&&f!==s)return f;}catch(e){}return s;};
const hideLd=id=>{const e=_$(id);if(e&&e.classList.contains('chart-loading'))e.classList.add('hidden');};

const Store={like:[],comment:[]};
const Pag={like:1,comment:1};
const Charts={};
function makeChart(id,opts){if(Charts[id]){try{Charts[id].destroy();}catch(e){}}const el=_$(id);if(!el)return null;Charts[id]=new ApexCharts(el,opts);Charts[id].render();el.style.display='block';return Charts[id];}
window.addEventListener('resize',()=>Object.values(Charts).forEach(c=>{try{c.updateOptions({});}catch(e){}}));

const IMETab={
    _loaded:{like:false,comment:false},
    show(type){
        ['like','comment'].forEach(t=>{_$('tab-'+t)?.classList.toggle('active',t===type);_$('panel-'+t)?.classList.toggle('active',t===type);});
        const lblMap={like:'Distribusi Likes — Top 5',comment:'Distribusi Comments — Top 5'};
        const dtitle=_$('donutTitle');if(dtitle)dtitle.textContent=lblMap[type]||'';
        if(!this._loaded[type]){this._loaded[type]=true;IMEData.loadTab(type);}
        else{if(Store[type].length)IMEData._renderDonut(type,Store[type]);}
    },
    reset(){this._loaded={like:false,comment:false};}
};

const IMEData={
    async loadAll(){
        if(!IMECfg.pid){['kpiLikes','kpiCmts','kpiPosts'].forEach(id=>{const e=_$(id);if(e)e.textContent='—';});['list-like','list-comment'].forEach(id=>{const e=_$(id);if(e)e.innerHTML=this._emptyHtml('Pilih project terlebih dahulu');});return;}
        IMETab._loaded.like=true;await this.loadTab('like');
    },
    async loadTab(type){
        const subMap={like:'postbylike',comment:'postbycomment'};
        const rows=parseInt(_$('rows-'+type)?.value||'100');
        const listEl=_$('list-'+type);
        if(listEl)listEl.innerHTML=`<div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div>`;
        try{
            const res=await fetch(`/mk/api/instagram/most-viewed-posts?project_id=${IMECfg.pid}&start_date=${IMECfg.sd}&end_date=${IMECfg.ed}&sub=${subMap[type]}&rows=${rows}`);
            const json=await res.json();
            let items=json.data||json||[];if(!Array.isArray(items))items=[];
            Store[type]=items;Pag[type]=1;
            const chip=_$('chip-'+type);if(chip)chip.textContent=items.length;
            const badge=_$('badge-'+type);if(badge)badge.textContent=`${items.length} posts`;
            if(type==='like'){this._updateKPIs(items);this._renderEngChart(items);}
            this._renderList(type);this._renderBar(type,items.slice(0,10));this._renderDonut(type,items);
        }catch(err){
            console.error('[IME]',err);if(listEl)listEl.innerHTML=this._emptyHtml('Gagal memuat: '+err.message);
            const chip=_$('chip-'+type);if(chip)chip.textContent='!';hideLd('loading-bar-'+type);
        }
    },
    reloadTab(type){Store[type]=[];Pag[type]=1;this.loadTab(type);},
    _metric(item,type){const keys={like:['likes','num_likes'],comment:['comments','num_comments']};return(keys[type]||['likes']).reduce((v,k)=>v||parseInt(item[k]||0),0);},
    _updateKPIs(items){
        let l=0,c=0;items.forEach(i=>{l+=parseInt(i.likes||i.num_likes||0);c+=parseInt(i.comments||i.num_comments||0);});
        const n=items.length,avg=val=>n?Math.round(val/n):0;
        const el=(id,val)=>{const e=_$(id);if(e)e.textContent=numF(val);};
        const sub=(id,val)=>{const e=_$(id);if(e)e.innerHTML=`<i class="ph ph-chart-line-up me-1"></i>Avg ${numF(avg(val))} / post &middot; ${n} posts`;};
        el('kpiLikes',l);sub('kpiLikesSub',l);el('kpiCmts',c);sub('kpiCmtsSub',c);el('kpiPosts',n);
        const ep=_$('kpiPostsSub');if(ep)ep.innerHTML=`<i class="ph ph-chart-line-up me-1"></i>Avg ${numF(avg(l+c))} engagement / post`;
    },
    _getName(item){const n=(item.name||item.author_scr_name||'').replace(/<[^>]*>/g,'').trim();if(n&&n!=='Unknown')return n;return'Instagram Post';},
    _getAvatar(item){return(item.avatar_url||item.image||'').trim();},
    _getColor(item){const seed=item.id||this._getName(item)||'ig';const palette=['#e6683c','#273B4A','#F59E0B','#06B6D4','#8b5cf6','#ec4899','#f97316','#14b8a6'];let h=0;for(let i=0;i<seed.length;i++)h=(h*31+seed.charCodeAt(i))&0xffffffff;return palette[Math.abs(h)%palette.length];},
    _avHtml(item){const av=this._getAvatar(item),dummy='/assets/images/user/dummy.jpg';if(av&&av.startsWith('http'))return`<img src="${esc(av)}" onerror="this.src='${dummy}'">`; return`<img src="${dummy}">`;},
    _normSent(item){const r=String(item.sentiment_str||item.sentiment||'').toLowerCase();return r.includes('pos')?'pos':r.includes('neg')?'neg':'neu';},
    _getType(item){const t=String(item.mention_type||'').toLowerCase();return t.includes('video')?'video':t.includes('image')?'image':'';},
    _emptyHtml(msg){return`<div class="chart-empty" style="padding:40px 20px;"><i class="ph ph-folder-open"></i><span>${esc(msg)}</span></div>`;},
    _renderList(type){
        const items=Store[type],listEl=_$('list-'+type),pagEl=_$('pag-'+type);if(!listEl)return;
        if(!items.length){listEl.innerHTML=this._emptyHtml('Tidak ada data untuk periode ini');if(pagEl)pagEl.innerHTML='';return;}
        const page=Pag[type]||1,total=items.length,pp=IMECfg.perPage,pages=Math.ceil(total/pp),start=(page-1)*pp;
        listEl.innerHTML=`<div class="tme-post-list">${items.slice(start,start+pp).map((item,i)=>this._postHtml(item,start+i,type)).join('')}</div>`;
        if(pagEl)pagEl.innerHTML=this._pagHtml(type,page,pages,total,start+1,Math.min(start+pp,total));
        listEl.querySelectorAll('.tme-post').forEach(el=>{el.addEventListener('click',()=>{try{const item=JSON.parse(decodeURIComponent(el.dataset.item));const lm={like:'Most Liked',comment:'Most Comments'};IMEPanel.open(items,type,'Instagram — '+lm[type]);IMEDetail.open(item,type);}catch(e){console.warn(e);}});});
    },
    _pagHtml(type,page,pages,total,from,to){
        if(pages<=1)return '';let btns='',r=2;
        btns+=`<button class="tme-pag-btn" ${page<=1?'disabled':''} onclick="IMEData.goPage('${type}',${page-1})"><i class="ph ph-caret-left"></i></button>`;
        for(let i=1;i<=pages;i++){if(i===1||i===pages||(i>=page-r&&i<=page+r))btns+=`<button class="tme-pag-btn${i===page?' is-active':''}" onclick="IMEData.goPage('${type}',${i})">${i}</button>`;else if(i===page-r-1||i===page+r+1)btns+=`<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`;}
        btns+=`<button class="tme-pag-btn" ${page>=pages?'disabled':''} onclick="IMEData.goPage('${type}',${page+1})"><i class="ph ph-caret-right"></i></button>`;
        return`<div class="tme-pagination"><span class="tme-pag-info">Menampilkan ${from}–${to} dari ${total} posts</span><div class="tme-pag-controls">${btns}</div></div>`;
    },
    goPage(type,page){Pag[type]=page;this._renderList(type);const el=_$('list-'+type);if(el)el.scrollIntoView({behavior:'smooth',block:'nearest'});},
    _postHtml(item,gi,type){
        const rank=gi+1,rkCls=rank<=3?'--'+rank:'';
        const name=this._getName(item),color=this._getColor(item),avHtml=this._avHtml(item),sent=this._normSent(item),mType=this._getType(item);
        const content=dec((item.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()).slice(0,200);
        const dt=(item.date_created||'').split('T')[0],url=item.url||'';
        const l=parseInt(item.likes||item.num_likes||0),c=parseInt(item.comments||item.num_comments||0),total=l+c;
        const sentLbl={pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];
        const enc=encodeURIComponent(JSON.stringify(item));
        const lCls=type==='like'?' tme-metric--ig':'',cCls=type==='comment'?' tme-metric--amber':'';
        const typeBadge=mType?`<span class="tme-type-badge tme-type-badge--${mType}"><i class="ph ph-${mType==='video'?'video-camera':'image'} me-1"></i>${mType}</span>`:'';
        return`<div class="tme-post" data-item="${esc(enc)}">
            <div class="tme-post-rank tme-post-rank${rkCls}">${rank}</div>
            <div class="tme-post-av" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
            <div class="tme-post-body">
                <div class="tme-post-author">${esc(name)}</div>
                ${dt?`<div class="tme-post-date">${dt}</div>`:''}
                ${content?`<div class="tme-post-text">${esc(content)}</div>`:''}
                <div class="tme-post-stats">
                    <span class="tme-metric${lCls}"><i class="ph ph-heart me-1"></i>${numF(l)}</span>
                    <span class="tme-metric${cCls}"><i class="ph ph-chat-circle me-1"></i>${numF(c)}</span>
                    <span class="tme-metric" style="font-weight:800;">∑ ${numF(total)}</span>
                    ${typeBadge}
                    <span class="tme-sent tme-sent--${sent}">${sentLbl}</span>
                    ${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="tme-view-link" onclick="event.stopPropagation()"><i class="ph ph-arrow-square-out me-1"></i>Lihat</a>`:''}
                </div>
            </div>
        </div>`;
    },
    _renderBar(type,items){
        const barEl=_$('bar-'+type),loadEl=_$('loading-bar-'+type);if(!barEl||!items.length||typeof ApexCharts==='undefined'){hideLd('loading-bar-'+type);return;}
        const color=IMECfg.colors[type];const labels=items.map(it=>{const n=this._getName(it);return n.length>14?n.slice(0,13)+'…':n;});const values=items.map(it=>this._metric(it,type));
        barEl.style.display='block';makeChart('bar-'+type,{
            chart:{type:'bar',height:280,fontFamily:'inherit',background:'transparent',toolbar:{show:false},zoom:{enabled:false},events:{mounted:()=>hideLd('loading-bar-'+type),click:(_,ctx,cfg)=>{const item=items[cfg.dataPointIndex];if(item){IMEPanel.open(Store[type],type,'Instagram');IMEDetail.open(item,type);}}}},
            series:[{name:{like:'Likes',comment:'Comments'}[type],data:values}],colors:[color],
            plotOptions:{bar:{borderRadius:5,columnWidth:'58%',dataLabels:{position:'top'}}},
            dataLabels:{enabled:true,formatter:v=>numK(v),offsetY:-16,style:{fontSize:'10px',fontWeight:'800',colors:[color]},background:{enabled:false}},
            xaxis:{categories:labels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'},rotate:labels.length>6?-28:0,hideOverlappingLabels:true}},
            yaxis:{labels:{formatter:v=>numK(v),style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false},axisTicks:{show:false}},
            grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}},padding:{top:20,right:8,bottom:0,left:4}},
            fill:{type:'gradient',gradient:{type:'vertical',shadeIntensity:0.2,opacityFrom:1,opacityTo:.7,stops:[0,100]}},
            tooltip:{shared:false,intersect:true,style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>numF(v)}},
        });
    },
    _renderDonut(type,items){
        const loadEl=_$('donutLoading'),chartEl=_$('donutChart'),emptyEl=_$('donutEmpty');if(!loadEl||!chartEl)return;
        if(!items.length){loadEl.style.display='none';if(emptyEl)emptyEl.style.display='flex';return;}
        const top5=items.slice(0,5),total=top5.reduce((s,it)=>s+this._metric(it,type),0);
        const metLbl={like:'Likes',comment:'Comments'}[type];
        const legEl=_$('donutLegend');if(legEl)legEl.innerHTML=top5.map((it,i)=>{const n=this._getName(it),sn=n.length>22?n.slice(0,21)+'…':n;return`<div class="donut-leg-item"><span class="donut-dot" style="background:${DONUT_COLORS[i]};"></span>${sn} · ${numF(this._metric(it,type))}</div>`;}).join('');
        loadEl.style.display='none';chartEl.style.display='block';if(emptyEl)emptyEl.style.display='none';
        if(window.__donutEChart){try{window.__donutEChart.dispose();}catch(e){}}
        if(typeof echarts==='undefined'){chartEl.innerHTML='<div class="chart-empty"><i class="ph ph-chart-donut"></i><span>ECharts not loaded</span></div>';return;}
        const chart=echarts.init(chartEl,null,{renderer:'canvas'});window.__donutEChart=chart;window.addEventListener('resize',()=>{try{chart.resize();}catch(e){}});
        const pieData=top5.map((it,i)=>{const name=this._getName(it),val=this._metric(it,type);const content=dec((it.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim());return{name,value:val,_content:content,itemStyle:{color:DONUT_COLORS[i]}};});
        chart.setOption({
            backgroundColor:'transparent',tooltip:{show:false},animation:true,animationDuration:1000,animationEasing:'cubicOut',animationDelay:idx=>idx*80,
            series:[{type:'pie',radius:['38%','62%'],center:['50%','50%'],avoidLabelOverlap:true,selectedMode:false,minAngle:8,itemStyle:{borderColor:'#fff',borderWidth:3},
                label:{show:true,position:'outside',alignTo:'edge',edgeDistance:20,lineHeight:18,fontSize:11,fontFamily:'inherit',color:'#334155',fontWeight:'500',
                    formatter:p=>{const it=top5[p.dataIndex];const content=dec((it.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim());const words=content?content.slice(0,120).split(' '):[];const lines=[];let cur='';words.forEach(w=>{if((cur+' '+w).trim().length>42){lines.push(cur.trim());cur=w;}else{cur=(cur+' '+w).trim();}});if(cur)lines.push(cur);const body=lines.join('\n');return`{title|${p.name}}\n${body?body+'\n':''}({val|${numF(p.value)}} ${metLbl}, {pct|${p.percent.toFixed(1)}%})`;},
                    rich:{title:{fontSize:11,fontWeight:'700',color:'#1e293b',lineHeight:18},val:{fontSize:11,fontWeight:'700',color:'#038047'},pct:{fontSize:11,fontWeight:'600',color:'#64748b'}}
                },labelLine:{show:true,length:18,length2:24,smooth:0.3,lineStyle:{width:1.5,color:'#94A3B8'}},emphasis:{scale:false,itemStyle:{borderWidth:3,borderColor:'#fff'},label:{show:true}},select:{disabled:true},data:pieData,
            }],
            graphic:[{type:'text',left:'center',top:'46%',z:100,style:{text:numK(total),fill:'#0f172a',font:"800 28px inherit",textAlign:'center'}},{type:'text',left:'center',top:'54%',z:100,style:{text:'TOTAL '+metLbl.toUpperCase(),fill:'#94a3b8',font:"600 9px inherit",textAlign:'center'}}]
        });
        chart.on('click',p=>{const item=top5[p.dataIndex];if(item){IMEPanel.open(Store[type].length?Store[type]:top5,type,'Instagram');IMEDetail.open(item,type);}});
        let _ttEl=document.getElementById('donutCustomTT');if(!_ttEl){_ttEl=document.createElement('div');_ttEl.id='donutCustomTT';_ttEl.style.cssText=`position:fixed;z-index:9999;pointer-events:none;background:#1e293b;color:#fff;border:1px solid #334155;border-radius:6px;padding:10px 14px;max-width:280px;font-size:12px;line-height:1.5;display:none;box-shadow:0 8px 24px rgba(0,0,0,.32);font-family:inherit;opacity:0;transform:translateY(6px) scale(.97);transition:opacity .18s ease,transform .18s ease;`;document.body.appendChild(_ttEl);}
        let _ttTimer=null;
        chart.on('mouseover',p=>{if(p.componentType!=='series')return;const it=top5[p.dataIndex];const clr=DONUT_COLORS[p.dataIndex];const content=dec((it.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()).slice(0,160);clearTimeout(_ttTimer);_ttEl.innerHTML=`<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;"><span style="width:9px;height:9px;border-radius:50%;background:${clr};flex-shrink:0;display:inline-block;box-shadow:0 0 0 3px ${clr}33;animation:ttDotPulse 1.4s ease infinite;"></span><b style="font-size:12.5px;">${esc(p.name)}</b></div>${content?`<div style="font-size:11px;color:#94a3b8;margin-bottom:6px;">${esc(content)}</div>`:''}<div style="display:flex;align-items:center;gap:8px;"><b style="font-size:13px;">${numF(p.value)} ${metLbl}</b><span style="color:${clr};font-weight:700;">${p.percent.toFixed(1)}%</span></div>`;_ttEl.style.display='block';requestAnimationFrame(()=>{_ttEl.style.opacity='1';_ttEl.style.transform='translateY(0) scale(1)';});});
        chart.on('mouseout',()=>{_ttEl.style.opacity='0';_ttEl.style.transform='translateY(6px) scale(.97)';_ttTimer=setTimeout(()=>{_ttEl.style.display='none';},180);});
        chartEl.addEventListener('mousemove',e=>{if(_ttEl.style.display==='none')return;const vw=window.innerWidth,vh=window.innerHeight,tw=_ttEl.offsetWidth+16,th=_ttEl.offsetHeight+16;let x=e.clientX+18,y=e.clientY-10;if(x+tw>vw)x=e.clientX-tw;if(y+th>vh)y=e.clientY-th;_ttEl.style.left=x+'px';_ttEl.style.top=y+'px';});
    },
    _renderEngChart(items){hideLd('loading-eng');if(!items.length)return;const top10=[...items].map(it=>({...it,_total:parseInt(it.likes||0)+parseInt(it.comments||0)})).sort((a,b)=>b._total-a._total).slice(0,10);IMEChart._items=top10;IMEChart._render(top10,IMEChart._type);}
};

const IMEChart={
    _type:'stacked',_items:[],
    setEngType(t){this._type=t;document.querySelectorAll('#engTypeToggle .tme-toggle-btn').forEach(b=>b.classList.toggle('active',b.dataset.type===t));if(this._items.length)this._render(this._items,t);},
    _render(items,stackType){
        const barEl=_$('bar-eng');if(!barEl)return;barEl.style.display='block';
        const labels=items.map(it=>{const n=IMEData._getName(it);return n.length>14?n.slice(0,13)+'…':n;});const isStack=stackType==='stacked';
        makeChart('bar-eng',{
            chart:{type:'bar',height:280,fontFamily:'inherit',background:'transparent',toolbar:{show:false},zoom:{enabled:false},stacked:isStack},
            series:[{name:'Likes',data:items.map(it=>parseInt(it.likes||0))},{name:'Comments',data:items.map(it=>parseInt(it.comments||0))}],
            colors:['#e6683c','#F59E0B'],
            plotOptions:{bar:{borderRadius:isStack?2:4,columnWidth:isStack?'60%':'75%',dataLabels:{position:isStack?'center':'top'}}},
            dataLabels:{enabled:isStack,formatter:v=>v>0?numK(v):'',style:{fontSize:'9px',fontWeight:'700',colors:['#fff']},background:{enabled:false}},
            xaxis:{categories:labels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'},rotate:labels.length>7?-28:0}},
            yaxis:{labels:{formatter:v=>numK(v),style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false},axisTicks:{show:false}},
            grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}},padding:{top:10,right:8,bottom:0,left:4}},
            legend:{position:'bottom',horizontalAlign:'left',fontSize:'11px',fontFamily:'inherit',fontWeight:600,markers:{width:8,height:8,radius:4},itemMargin:{horizontal:12,vertical:4},offsetY:4},
            tooltip:{shared:true,intersect:false,style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>numF(v)}},
        });
    }
};

const IMEPanel={
    _items:[],_type:null,
    open(items,type,title){this._items=items||[];this._type=type;IMEDetail.close();_$('imePanelDot').style.background=IMECfg.colors[type]||'#e6683c';_$('imePanelTitle').textContent=title||'Instagram Posts';_$('imePanelMeta').textContent=IMECfg.sd+' – '+IMECfg.ed;const ov=_$('imePanelOverlay'),pn=_$('imeSntPanel');ov.classList.remove('hiding');pn.classList.remove('hiding');ov.classList.add('show');pn.classList.add('show');this._render(items,type);},
    close(){IMEDetail.close();const ov=_$('imePanelOverlay'),pn=_$('imeSntPanel');pn.classList.add('hiding');ov.classList.add('hiding');setTimeout(()=>{pn.classList.remove('show','hiding');ov.classList.remove('show','hiding');},240);},
    _render(items,type){
        const list=_$('imePanelList');if(!list)return;if(!items?.length){list.innerHTML='<div class="do-panel-loading"><div class="do-panel-spinner"></div>Tidak ada data</div>';return;}
        const metLbl={like:'Likes',comment:'Comments'}[type];
        list.innerHTML=items.slice(0,100).map(item=>{const name=IMEData._getName(item),color2=IMEData._getColor(item),avHtml=IMEData._avHtml(item);const text=(item.content||'').replace(/<[^>]*>/g,'').trim();const metVal=IMEData._metric(item,type);const dt=(item.date_created||'').split('T')[0];const sent=IMEData._normSent(item),sentLbl={pos:'Pos',neg:'Neg',neu:'Neu'}[sent];const total=parseInt(item.likes||0)+parseInt(item.comments||0);const enc=encodeURIComponent(JSON.stringify(item));return`<div class="do-panel-item" data-item="${esc(enc)}" data-type="${type}" onclick="IMEPanel._click(this)"><div class="do-panel-avatar" style="background:linear-gradient(135deg,${color2},${color2}99);">${avHtml}</div><div class="do-panel-item-body"><div class="do-panel-author">${esc(name)}</div><div class="do-panel-text">${esc(dec(text).slice(0,130)||'(tidak ada konten)')}</div><div class="do-panel-footer"><span class="do-sent-badge do-sent-badge--${sent}">${sentLbl}</span><span>${metLbl} ${numF(metVal)}</span><span>∑ ${numF(total)}</span>${dt?`<span style="margin-left:auto;">${dt}</span>`:''}</div></div></div>`;}).join('');
        if(items.length>100)list.insertAdjacentHTML('beforeend',`<div style="padding:8px;text-align:center;font-size:10px;font-weight:600;color:#94A3B8;background:var(--slate-50);border-top:1px dashed var(--slate-200);">+${(items.length-100).toLocaleString()} lainnya</div>`);
    },
    _click(el){try{const item=JSON.parse(decodeURIComponent(el.dataset.item.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"')));IMEDetail.open(item,el.dataset.type||this._type);}catch(e){console.warn(e);}}
};

const IMEDetail={
    open(item,type){
        const panel=_$('imeDetailPanel'),body=_$('imeDetailBody'),title=_$('imeDetailTitle');if(!panel||!body)return;
        const color=IMECfg.colors[type]||'#e6683c',name=IMEData._getName(item),avColor=IMEData._getColor(item),avHtml=IMEData._avHtml(item);
        const rawContent=(item.content||'').replace(/<[^>]*>/g,'').trim(),content=rawContent?dec(rawContent):'';
        const url=item.url||'',dt=item.date_created||'',imgUrl=(item.image||'').trim();
        const l=parseInt(item.likes||item.num_likes||0),c=parseInt(item.comments||item.num_comments||0);
        const sent=IMEData._normSent(item),sentLbl={pos:'Positif',neg:'Negatif',neu:'Netral'}[sent];
        const mType=IMEData._getType(item);
        let dtFmt='';if(dt){try{dtFmt=new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){dtFmt=dt.split('T')[0];}}
        title.textContent=name;
        const typeBadge=mType?`<span style="display:inline-block;padding:2px 8px;border-radius:3px;font-size:10px;font-weight:700;margin-left:6px;background:${mType==='video'?'#dbeafe':'#fce7f3'};color:${mType==='video'?'#1d4ed8':'#9d174d'};">${mType}</span>`:'';
        body.innerHTML=`<div class="do-dp2-avatar-row"><div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div><div><div class="do-dp2-name">${esc(name)}${typeBadge}</div><span class="do-dp2-plat-badge" style="background:#e6683c18;color:#e6683c;">Instagram</span></div></div>${dtFmt?`<div class="do-dp2-meta"><i class="ph ph-calendar me-1"></i>${dtFmt}</div>`:''}<div class="do-dp2-sent do-dp2-sent--${sent}"><i class="ph ph-smiley me-1"></i>${sentLbl}</div>${imgUrl&&imgUrl.startsWith('http')?`<div class="do-dp2-img-wrap"><img src="${esc(imgUrl)}" alt="Post image" onerror="this.parentElement.style.display='none'"></div>`:''} ${content?`<div class="do-dp2-content">${esc(content)}</div>`:''}<div class="do-dp2-stats"><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(l)}</div><div class="do-dp2-stat-lbl">Likes</div></div><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(c)}</div><div class="do-dp2-stat-lbl">Comments</div></div></div>${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-instagram-logo me-1"></i>Lihat Post Instagram</a>`:''}`;
        panel.classList.add('show');
    },
    close(){_$('imeDetailPanel')?.classList.remove('show');}
};
/* ══════════════════════════════════════════════════════
   EXPORT MODULE — Instagram Most Engagement v2 (sync dengan X Overview)
   Fix: ECharts pre-snapshot, _onClone, freeze, PDF all-tabs
   Note: ECharts instance disimpan di window.__donutEChart
══════════════════════════════════════════════════════ */
const IMEExport = (() => {
    'use strict';
    let _toastTimer  = null;
    let _ecSnapshots = {};

    /* ── Toast ── */
    function _toast(msg, type = 'default', duration = 3200) {
        const t   = _$('exportToast'),
              m   = _$('exportToastMsg'),
              ico = _$('exportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className   = 'export-toast show ' + (type !== 'default' ? type : '');
        ico.className = 'ph ' + ({ success: 'ph-check-circle', error: 'ph-x-circle', default: 'ph-spinner' }[type] || 'ph-spinner');
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => t.classList.remove('show'), duration);
    }

    /* ── Button state ── */
    function _btnState(btn, loading) {
        if (!btn) return;
        btn.disabled = loading;
        btn.classList.toggle('exporting', loading);
    }

    /* ── Freeze / unfreeze animations ── */
    function _freeze() {
        if (document.getElementById('__ime_freeze')) return;
        const s = document.createElement('style');
        s.id = '__ime_freeze';
        s.textContent = '*{animation:none!important;transition:none!important;animation-play-state:paused!important;}';
        document.head.appendChild(s);
    }
    function _unfreeze() { document.getElementById('__ime_freeze')?.remove(); }

    /* ── Pre-snapshot ECharts donut (stored di window.__donutEChart) ── */
   /* ── Pre-snapshot ECharts: ambil dataURL sebelum html2canvas ── */
function _preSnapshot() {
    _ecSnapshots = {};
    const inst = window.__donutEChart;
    if (!inst || inst.isDisposed?.()) return;

    // Coba via ECharts API dulu
    try {
        const url = inst.getDataURL({ type:'png', pixelRatio:2, backgroundColor:'#ffffff' });
        if (url && url !== 'data:,' && url.length > 100) {
            _ecSnapshots['donutChart'] = url;
            return;
        }
    } catch(e) { console.warn('[IMEExport] getDataURL gagal:', e); }

    // Fallback: copy langsung dari DOM canvas (Safari-safe)
    const container = document.getElementById('donutChart');
    if (!container) return;
    const echartsCanvas = container.querySelector('canvas');
    if (!echartsCanvas) return;
    try {
        const off = document.createElement('canvas');
        off.width  = echartsCanvas.width;
        off.height = echartsCanvas.height;
        const ctx  = off.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, off.width, off.height);
        ctx.drawImage(echartsCanvas, 0, 0);
        const url = off.toDataURL('image/png');
        if (url && url !== 'data:,' && url.length > 100)
            _ecSnapshots['donutChart'] = url;
    } catch(e2) { console.warn('[IMEExport] fallback canvas gagal:', e2); }
}

/* ── Swap ECharts canvas → <img> di DOM asli (Safari-safe) ── */
function _swapChartsIn(el) {
    const swaps = [];
    const container = document.getElementById('donutChart');
    if (!container || !el.contains(container)) return swaps;
    if (container.style.display === 'none') return swaps;
    if (!_ecSnapshots['donutChart']) return swaps;

    const h = container.offsetHeight || 480;
    const w = container.offsetWidth  || 600;

    const placeholder = document.createElement('div');
    placeholder.dataset.swapFor = 'donutChart';

    const img = document.createElement('img');
    img.src = _ecSnapshots['donutChart'];
    img.style.cssText = `width:${w}px;height:${h}px;object-fit:contain;display:block;background:#fff;`;

    container.parentNode.insertBefore(placeholder, container);
    container.parentNode.insertBefore(img, placeholder);
    container.style.display = 'none';

    swaps.push({ container, placeholder, img });
    return swaps;
}

function _swapChartsOut(swaps) {
    swaps.forEach(({ container, placeholder, img }) => {
        try { img.remove(); }         catch(e) {}
        try { placeholder.remove(); } catch(e) {}
        container.style.display = 'block';
    });
}

/* ── onclone: bersihkan DOM clone ── */
function _onClone(clonedDoc) {
    clonedDoc.querySelectorAll(
        '.do-panel-overlay,.do-panel,.do-detail-panel,' +
        '#donutCustomTT,#imePanelOverlay,#imeSntPanel,' +
        '.spin-ring,.spinner-state,.export-toast,.chart-loading,' +
        '[data-html2canvas-ignore]'
    ).forEach(el => {
        el.style.cssText += 'display:none!important;visibility:hidden!important;' +
                            'opacity:0!important;height:0!important;overflow:hidden!important;';
    });

    clonedDoc.querySelectorAll('.tme-tab-panel:not(.active)').forEach(el => {
        el.style.cssText += 'display:none!important;height:0!important;overflow:hidden!important;';
    });

    clonedDoc.querySelectorAll('.tme-post-av,.do-panel-avatar,.do-dp2-avatar-lg').forEach(wrapper => {
        wrapper.querySelectorAll('img').forEach(img => { img.style.display = 'none'; });
        if (!wrapper.querySelector('.__ini')) {
            const sp = clonedDoc.createElement('span');
            sp.className  = '__ini';
            sp.textContent = 'I';
            sp.style.cssText = 'font-size:13px;font-weight:700;color:#fff;line-height:1;';
            wrapper.appendChild(sp);
        }
        if (!wrapper.style.background)
            wrapper.style.background = 'linear-gradient(135deg,#e6683c,#cc2366)';
    });

    clonedDoc.querySelectorAll('.do-dp2-img-wrap img').forEach(img => {
        img.style.display = 'none';
    });

    clonedDoc.querySelectorAll('*').forEach(el => {
        el.style.animationPlayState = 'paused';
        el.style.animation          = 'none';
        el.style.transition         = 'none';
    });

    clonedDoc.querySelectorAll(
        '.card,.card-body,.card-header,.row,[class*="col-"],' +
        '.tme-tab-panel.active,.tme-post-list,.tme-post,' +
        '#pageExportArea'
    ).forEach(el => {
        el.style.opacity    = '1';
        el.style.transform  = 'none';
        el.style.visibility = 'visible';
    });
}

/* ── Core capture: swap dulu di DOM asli, baru html2canvas ── */
async function _doCapture(el, isCard) {
    _preSnapshot();

    // Freeze visual
    el.querySelectorAll('.card,.kpi-card-hover,[class*="col-"],.tme-post')
      .forEach(e => { e.style.opacity='1'; e.style.transform='none'; e.style.visibility='visible'; });

    // Swap ECharts canvas → img di DOM asli
    const swaps = _swapChartsIn(el);

    // Safari butuh extra frame
    await new Promise(r => setTimeout(r, 300));
    await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));

    let canvas;
    try {
        canvas = await html2canvas(el, {
            scale          : 2,
            useCORS        : true,
            allowTaint     : true,
            backgroundColor: isCard ? '#ffffff' : '#f1f5f9',
            logging        : false,
            removeContainer: true,
            imageTimeout   : 0,
            onclone        : d => _onClone(d),
            ignoreElements : e => e.hasAttribute('data-html2canvas-ignore'),
            x              : 0,
            y              : 0,
            width          : el.offsetWidth,
            height         : el.scrollHeight,
        });
    } finally {
        _swapChartsOut(swaps);
    }
    return canvas;
}
    /* ── PDF header ── */
    function _drawHeader(pdf, pW, pH, label, page, total) {
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + (label || 'Instagram Most Engagement'), 10, 7.5);
        const now = new Date().toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit',
        });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - 10, 7.5, { align: 'right' });
        pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
        pdf.text(`Halaman ${page} / ${total}`, pW / 2, pH - 3, { align: 'center' });
    }

    /* ── Tambahkan canvas ke halaman PDF (fit-in-page) ── */
    function _addCanvas(pdf, canvas, margin, pW, pH) {
        const uw = pW - margin * 2, uh = pH - 14 - 10;
        const ratio = Math.min(uw / canvas.width, uh / canvas.height);
        const dw = canvas.width * ratio, dh = canvas.height * ratio;
        pdf.addImage(
            canvas.toDataURL('image/png'), 'PNG',
            margin + (uw - dw) / 2,
            14   + (uh - dh) / 2,
            dw, dh
        );
    }

    /* ── Paginate canvas panjang ── */
    function _paginate(pdf, canvas, margin, pW, pH, labelFn) {
        const uw = pW - margin * 2, uh = pH - 14 - 10;
        const ratio  = uw / canvas.width, sliceH = uh / ratio;
        const total  = Math.max(1, Math.ceil((canvas.height * ratio) / uh));
        let srcY = 0, pg = 1;
        while (srcY < canvas.height) {
            if (pg > 1) pdf.addPage();
            _drawHeader(pdf, pW, pH, labelFn(), pg, total);
            const srcSlice = Math.min(sliceH, canvas.height - srcY), dstH = srcSlice * ratio;
            const slice    = document.createElement('canvas');
            slice.width    = canvas.width;
            slice.height   = Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(
                canvas, 0, srcY, canvas.width, srcSlice,
                0, 0,           canvas.width, srcSlice
            );
            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, uw, dstH);
            srcY += srcSlice; pg++;
        }
        return total;
    }

    /* ════════════════════════════════════════════
       run — Export seluruh halaman (semua 2 tab)
    ════════════════════════════════════════════ */
    async function run(type, btn) {
        if (!window.html2canvas)       { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

        const btnPdf = _$('pageExportPdfBtn'), btnImg = _$('pageExportImgBtn');
        _btnState(btnPdf, true); _btnState(btnImg, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF semua tab…' : 'Mengambil gambar…', 'default', 99999);

        const originalTab = ['like','comment']
            .find(t => _$('tab-' + t)?.classList.contains('active')) || 'like';
        const area  = _$('pageExportArea');
        const stamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');

        try {
            /* ── Image: capture tab aktif saja ── */
            if (type === 'image') {
                const canvas = await _doCapture(area, false);
                const link   = document.createElement('a');
                link.download = `instagram_engagement_${IME_PID}_${stamp}.png`;
                link.href     = canvas.toDataURL('image/png');
                link.click();
                _toast('Gambar berhasil diunduh!', 'success');
                return;
            }

            /* ── PDF: capture semua 2 tab ── */
            const { jsPDF }  = window.jspdf;
            const TAB_ORDER  = [
                { key: 'like',    label: 'Most Liked'    },
                { key: 'comment', label: 'Most Comments' },
            ];

            const canvases = [];
            for (let i = 0; i < TAB_ORDER.length; i++) {
                const { key, label } = TAB_ORDER[i];
                _toast(`Mengambil tab ${i + 1}/2: ${label}…`, 'default', 99999);
                IMETab.show(key);
                await new Promise(r => setTimeout(r, 900));
                canvases.push({ label, canvas: await _doCapture(area, false) });
            }

            /* Hitung total halaman */
            const pdf   = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
            const pW    = pdf.internal.pageSize.getWidth();
            const pH    = pdf.internal.pageSize.getHeight();
            const M     = 10, uw = pW - M * 2, uh = pH - 14 - 10;
            let totalPg = 0;
            canvases.forEach(({ canvas }) => {
                const ratio = uw / canvas.width;
                totalPg += Math.max(1, Math.ceil((canvas.height * ratio) / uh));
            });

            /* Build PDF */
            let pageNum = 1, firstPage = true;
            canvases.forEach(({ label, canvas }) => {
                const ratio   = uw / canvas.width, sliceH = uh / ratio;
                let srcY = 0;
                while (srcY < canvas.height) {
                    if (!firstPage) pdf.addPage();
                    firstPage = false;
                    _drawHeader(pdf, pW, pH, `Instagram Most Engagement — ${label}`, pageNum, totalPg);
                    const srcSlice = Math.min(sliceH, canvas.height - srcY), dstH = srcSlice * ratio;
                    const slice    = document.createElement('canvas');
                    slice.width    = canvas.width;
                    slice.height   = Math.ceil(srcSlice);
                    slice.getContext('2d').drawImage(
                        canvas, 0, srcY, canvas.width, srcSlice,
                        0, 0,           canvas.width, srcSlice
                    );
                    pdf.addImage(slice.toDataURL('image/png'), 'PNG', M, 14, uw, dstH);
                    srcY += srcSlice; pageNum++;
                }
            });

            pdf.save(`instagram_engagement_${IME_PID}_${stamp}.pdf`);
            _toast(`PDF ${totalPg} halaman berhasil diunduh!`, 'success');

        } catch (err) {
            console.error('[IMEExport.run]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            IMETab.show(originalTab);
            _btnState(btnPdf, false); _btnState(btnImg, false);
        }
    }

    /* ════════════════════════════════════════════
       runCard — Export per-card
    ════════════════════════════════════════════ */
    const _cardLabels = {
        'donut'        : 'Distribusi Likes — Top 5',
        'eng'          : 'Engagement Comparison',
        'list-like'    : 'Top Posts by Likes',
        'list-comment' : 'Top Posts by Comments',
        'bar-like'     : 'Chart Most Liked',
        'bar-comment'  : 'Chart Most Comments',
    };

    function _cardFilename(k) {
        const map = {
            'donut'        : 'distribusi-likes-top5',
            'eng'          : 'engagement-comparison',
            'list-like'    : 'top-posts-by-like',
            'list-comment' : 'top-posts-by-comment',
            'bar-like'     : 'chart-most-liked',
            'bar-comment'  : 'chart-most-comments',
        };
        const stamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        return `instagram_engagement_${map[k] || k}_${IME_PID}_${stamp}`;
    }

    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)       { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);

        try {
            const area = document.getElementById(areaId);
            if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');

            const canvas = await _doCapture(area, true);
            const fname  = _cardFilename(cardKey);
            const label  = _cardLabels[cardKey] || cardKey;

            if (type === 'image') {
                const link    = document.createElement('a');
                link.download = fname + '.png';
                link.href     = canvas.toDataURL('image/png');
                link.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF }  = window.jspdf;
                const landscape  = canvas.width > canvas.height * 1.2;
                const pdf        = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit: 'mm', format: 'a4' });
                const pW = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight();
                const M  = 10, uw = pW - M * 2, uh = pH - 14 - 10;
                const fitsOne = (canvas.height * (uw / canvas.width)) <= uh;

                if (fitsOne) {
                    _drawHeader(pdf, pW, pH, label, 1, 1);
                    _addCanvas(pdf, canvas, M, pW, pH);
                } else {
                    _paginate(pdf, canvas, M, pW, pH, () => label);
                }
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch (err) {
            console.error('[IMEExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btn, false);
        }
    }

    return { run, runCard };
})();

document.addEventListener('DOMContentLoaded',()=>{IMEData.loadAll();document.addEventListener('keydown',e=>{if(e.key==='Escape')IMEPanel.close();});});
</script>
@endsection
