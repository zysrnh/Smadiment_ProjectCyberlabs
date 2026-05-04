@extends('mk.layouts.app')

@section('title', 'Net Sentiment Score - SMADIMENT')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════
   DESIGN TOKENS
══════════════════════════════════════════════════════ */
:root {
    --do-primary        : var(--bs-primary, #4361EE);
    --do-primary-rgb    : var(--bs-primary-rgb, 67,97,238);
    --do-primary-lt     : rgba(var(--do-primary-rgb,67,97,238),.10);
    --do-green          : #10B981;
    --do-green-lt       : #ECFDF5;
    --do-red            : #EF4444;
    --do-red-lt         : #FEF2F2;
    --do-slate-50       : #F8FAFC;
    --do-slate-100      : #F1F5F9;
    --do-slate-200      : #E2E8F0;
    --do-slate-300      : #CBD5E1;
    --do-slate-400      : #94A3B8;
    --do-slate-500      : #64748B;
    --do-slate-700      : #334155;
    --do-slate-800      : #1E293B;
    --do-slate-900      : #0F172A;
    --do-radius         : 8px;
    --do-radius-sm      : 5px;
    --do-shadow-sm      : 0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);
    --do-shadow-md      : 0 4px 14px rgba(15,23,42,.08);
    --do-shadow-lg      : 0 10px 30px rgba(15,23,42,.12);
    --c-pos : #0ea5e9;
    --c-neg : #EF4444;
    --c-neu : #94A3B8;
}

*, *::before, *::after { box-sizing: border-box; }

@keyframes fadeUp        { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer       { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin          { to{transform:rotate(360deg)} }
@keyframes dropIn        { from{opacity:0;transform:translateY(-6px) scale(.97)} to{opacity:1;transform:translateY(0) scale(1)} }
@keyframes overlayIn     { from{opacity:0} to{opacity:1} }
@keyframes overlayOut    { from{opacity:1} to{opacity:0} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1}    to{transform:translateX(100%);opacity:0} }
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0)} 30%{transform:scale(1.25) rotate(-10deg)} 60%{transform:scale(1.1) rotate(6deg)} }
@keyframes kpiShimmer    { 0%{left:-100%} 100%{left:150%} }

.fade-up    { animation:fadeUp .36s ease-out both; }
.fade-up-d1 { animation-delay:.04s; }
.fade-up-d2 { animation-delay:.08s; }
.fade-up-d3 { animation-delay:.12s; }

/* ══ Page Header ══ */
.do-page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:20px; }

/* ══ Media Dropdown ══ */
.nss-media-wrap { position:relative; }
.nss-media-btn {
    display:inline-flex; align-items:center; gap:6px; padding:7px 14px;
    background:var(--do-slate-800); color:#fff; border:none; border-radius:var(--do-radius-sm);
    font-size:12px; font-weight:700; cursor:pointer; transition:filter .14s;
    box-shadow:var(--do-shadow-sm); font-family:inherit;
}
.nss-media-btn:hover { filter:brightness(1.15); }
.nss-media-btn i { font-size:14px; }
.nss-media-btn .caret { width:14px; height:14px; display:inline-flex; align-items:center; justify-content:center; transition:transform .2s; }
.nss-media-btn.open .caret { transform:rotate(180deg); }
.nss-media-menu {
    display:none; position:absolute; top:calc(100% + 6px); right:0;
    background:#fff; border:1px solid var(--do-slate-200); border-radius:var(--do-radius);
    box-shadow:var(--do-shadow-lg); min-width:200px; z-index:5000; padding:5px;
    animation:dropIn .16s ease-out;
}
.nss-media-menu.show { display:block; }
.nss-media-menu-section { padding:5px 9px 4px; font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.5px; }
.nss-media-menu-item {
    display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:var(--do-radius-sm);
    background:transparent; border:none; width:100%; text-align:left; font-size:12px; font-weight:600;
    color:var(--do-slate-700); cursor:pointer; transition:background .12s; font-family:inherit;
}
.nss-media-menu-item:hover  { background:var(--do-primary-lt); color:var(--do-primary); }
.nss-media-menu-item.active { background:var(--do-primary-lt); color:var(--do-primary); font-weight:700; }
.nss-menu-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

/* ══ Base Card ══ */
.do-card { background:#fff; border:1px solid var(--do-slate-200); border-radius:var(--do-radius); overflow:hidden; box-shadow:var(--do-shadow-sm); transition:border-color .2s,box-shadow .2s; }
.do-card:hover { box-shadow:var(--do-shadow-md); border-color:var(--do-slate-300); }
.do-card-head { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--do-slate-200); background:#fff; }
.do-card-head-left { display:flex; align-items:center; gap:8px; }
.do-head-icon { width:30px; height:30px; border-radius:var(--do-radius-sm); background:var(--do-primary-lt); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:15px; color:var(--do-primary); }
.do-card-title    { font-size:13px; font-weight:700; color:var(--do-slate-900); }
.do-card-subtitle { font-size:10px; color:var(--do-slate-400); font-weight:500; margin-top:1px; }
.do-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; background:var(--do-slate-100); color:var(--do-slate-500); white-space:nowrap; }
.do-badge--pos { background:#dbeafe; color:#1e40af; }
.do-badge--neg { background:#fee2e2; color:#991b1b; }
.do-card-body { padding:16px; flex:1; }

/* ══ KPI Card Hover ══ */
.kpi-icon-bg { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0; }
.kpi-card-hover { will-change:transform,box-shadow; cursor:pointer; position:relative!important; overflow:hidden!important; transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important; }
.kpi-card-hover::before { content:''; position:absolute; top:0; bottom:0; left:-100%; width:60%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent); pointer-events:none; z-index:1; }
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025)!important; box-shadow:0 20px 40px rgba(0,0,0,.25)!important; filter:brightness(1.07)!important; }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background:rgba(255,255,255,.35)!important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important; display:inline-block!important; }
.kpi-card-hover:active { transform:translateY(-2px) scale(1.01)!important; transition-duration:.08s!important; }
.kpi-card-hover h3 { font-size:1.5rem; }

/* ══ Main Layout ══ */
.nss-main-grid { display:grid; grid-template-columns:1fr 340px; gap:14px; align-items:start; }
.nss-sidebar { display:flex; flex-direction:column; gap:14px; }

/* ══ Gauge ══ */
.nss-gauge-wrap { padding:24px 28px 0; display:flex; flex-direction:column; align-items:center; }
.nss-gauge-outer { position:relative; width:100%; max-width:440px; aspect-ratio:500/310; }
#nssGaugeSVG { width:100%; height:100%; display:block; overflow:visible; }
.nss-score-overlay { position:absolute; left:50%; top:53%; transform:translate(-50%,-50%); display:flex; flex-direction:column; align-items:center; gap:4px; pointer-events:none; z-index:2; white-space:nowrap; }
.nss-score-num { font-size:clamp(26px,5vw,46px); font-weight:800; letter-spacing:-2px; line-height:1; color:var(--do-slate-900); }
.nss-score-lbl { font-size:clamp(8px,1.2vw,10px); font-weight:700; letter-spacing:2px; color:var(--do-slate-400); text-transform:uppercase; }
.nss-gauge-legend { display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap; padding:12px 16px 14px; border-top:1px solid var(--do-slate-100); background:var(--do-slate-50); margin-top:auto; }
.nss-legend-item { display:flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:var(--do-slate-500); }
.nss-legend-dot  { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

/* ══ Distribution ══ */
.nss-dist-wrap { height:180px; }
#chNssDist { width:100%; height:100%; }

/* ══ Score Breakdown ══ */
.nss-breakdown-body { padding:0; }
.nss-brk-row { display:flex; align-items:center; justify-content:space-between; padding:9px 16px; border-bottom:1px solid var(--do-slate-50); }
.nss-brk-key { display:flex; align-items:center; gap:7px; font-size:12px; font-weight:600; color:var(--do-slate-700); }
.nss-brk-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.nss-brk-val { font-size:12px; font-weight:700; color:var(--do-slate-900); }
.nss-brk-divider { height:1px; background:var(--do-slate-200); margin:0 16px; }
.nss-brk-total-row { display:flex; align-items:center; justify-content:space-between; padding:8px 16px; background:var(--do-slate-50); }
.nss-brk-total-key { font-size:11px; font-weight:700; color:var(--do-slate-500); text-transform:uppercase; letter-spacing:.4px; }
.nss-brk-total-val { font-size:13px; font-weight:800; color:var(--do-slate-900); }
.nss-brk-nss-row { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:var(--do-primary-lt); border-top:1px solid rgba(var(--do-primary-rgb),.15); }
.nss-brk-nss-key { font-size:10px; font-weight:700; color:var(--do-primary); text-transform:uppercase; letter-spacing:.5px; }
.nss-brk-nss-val { font-size:22px; font-weight:800; letter-spacing:-1px; color:var(--do-primary); }
.nss-formula-eq { background:var(--do-slate-50); border-top:1px solid var(--do-slate-200); padding:8px 16px; font-size:10px; font-weight:600; color:var(--do-slate-400); text-align:center; letter-spacing:.2px; font-family:'Courier New',monospace; }

/* ══════════════════════════════════════════════════════
   EXPORT STYLES
══════════════════════════════════════════════════════ */
.page-export-bar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px;
    background:#fff; border:1px solid var(--do-slate-200);
    border-radius:var(--do-radius); padding:9px 14px;
    margin-bottom:20px; box-shadow:var(--do-shadow-sm);
}
.page-export-bar-left {
    display:flex; align-items:center; gap:8px;
    font-size:12px; font-weight:700; color:var(--do-slate-500);
}
.page-export-bar-left i { font-size:15px; color:#038047; }
.page-export-bar-right  { display:flex; gap:8px; }
.page-export-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:32px; height:32px; border-radius:var(--do-radius-sm);
    font-size:16px; cursor:pointer; transition:all .15s ease;
    border:1.5px solid transparent; font-family:inherit;
}
.page-export-btn-pdf { background:#fff3f3; color:#dc2626; border-color:#fca5a5; }
.page-export-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.page-export-btn-img { background:rgba(3,128,71,.10); color:#038047; border-color:rgba(3,128,71,.3); }
.page-export-btn-img:hover { background:#038047; color:#fff; border-color:#038047; }
.page-export-btn:disabled { opacity:.55; cursor:not-allowed; pointer-events:none; }
.page-export-btn .export-spinner {
    width:13px; height:13px; border:2px solid currentColor;
    border-top-color:transparent; border-radius:50%;
    animation:spin .65s linear infinite; display:none;
}
.page-export-btn.exporting .export-spinner { display:inline-block; }
.page-export-btn.exporting .export-icon    { display:none; }

.card-exp-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:var(--do-radius-sm);
    font-size:14px; cursor:pointer; flex-shrink:0;
    transition:all .14s ease; border:1px solid transparent;
    font-family:inherit; background:transparent;
}
.card-exp-btn-pdf { color:#dc2626; border-color:#fca5a5; background:#fff3f3; }
.card-exp-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.card-exp-btn-img { color:#038047; border-color:rgba(3,128,71,.3); background:rgba(3,128,71,.10); }
.card-exp-btn-img:hover { background:#038047; color:#fff; border-color:#038047; }
.card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
.card-exp-btn .export-spinner {
    width:11px; height:11px; border:2px solid currentColor;
    border-top-color:transparent; border-radius:50%;
    animation:spin .65s linear infinite; display:none;
}
.card-exp-btn.exporting .export-spinner { display:inline-block; }
.card-exp-btn.exporting .export-icon    { display:none; }

.export-toast {
    position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px);
    background:var(--do-slate-900); color:#fff; border-radius:var(--do-radius);
    padding:10px 18px; font-size:12px; font-weight:600;
    box-shadow:var(--do-shadow-lg); z-index:99999;
    opacity:0; pointer-events:none;
    transition:opacity .22s ease, transform .22s ease;
    display:flex; align-items:center; gap:8px; white-space:nowrap;
}
.export-toast.show    { opacity:1; transform:translateX(-50%) translateY(0); }
.export-toast.success { background:#065f46; }
.export-toast.error   { background:#991b1b; }

/* ══ Slide Panel ══ */
.do-panel-overlay { position:fixed; inset:0; z-index:9000; background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none; }
.do-panel-overlay.show   { display:block; animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }
.do-panel { position:fixed; top:0; right:0; bottom:0; z-index:9001; width:480px; max-width:100vw; background:#fff; display:none; flex-direction:column; border-left:1px solid var(--do-slate-200); box-shadow:-8px 0 40px rgba(15,23,42,.16); }
.do-panel.show   { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }
.do-panel-header { display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--do-slate-200); background:var(--do-slate-50); flex-shrink:0; }
.do-panel-dot   { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.do-panel-title { font-size:13px; font-weight:700; color:var(--do-slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-panel-count { background:var(--do-primary); color:#fff; border-radius:3px; padding:1px 8px; font-size:10px; font-weight:800; flex-shrink:0; }
.do-panel-close { width:28px; height:28px; border-radius:var(--do-radius-sm); border:1px solid var(--do-slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--do-slate-500); font-size:16px; transition:all .14s; flex-shrink:0; }
.do-panel-close:hover { background:var(--do-red); border-color:var(--do-red); color:#fff; }
.do-panel-actions { display:flex; align-items:center; gap:7px; padding:7px 12px; border-bottom:1px solid var(--do-slate-200); background:#fff; flex-shrink:0; }
.do-panel-meta { flex:1; font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.5px; overflow:hidden; display:flex; align-items:center; gap:5px; }
.do-panel-tabs { display:flex; background:var(--do-slate-100); border:1px solid var(--do-slate-200); border-radius:var(--do-radius-sm); padding:2px; gap:2px; }
.do-panel-tab { padding:3px 9px; border-radius:3px; border:none; background:transparent; font-size:11px; font-weight:700; cursor:pointer; transition:all .13s; color:var(--do-slate-500); font-family:inherit; }
.do-panel-tab:hover { background:#fff; }
.do-panel-tab.active                { background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08); }
.do-panel-tab.active[data-s="all"] { color:var(--do-primary); }
.do-panel-tab.neg.active { color:var(--do-red); }
.do-panel-tab.pos.active { color:#0ea5e9; }
.do-panel-tab.neu.active { color:var(--do-slate-500); }
.do-panel-list { overflow-y:auto; flex:1; padding:2px 0; min-height:0; }
.do-panel-list::-webkit-scrollbar { width:4px; }
.do-panel-list::-webkit-scrollbar-thumb { background:var(--do-slate-200); border-radius:99px; }
.do-panel-item { display:flex; gap:10px; padding:10px 14px; border-bottom:1px solid var(--do-slate-50); cursor:pointer; transition:background .1s; align-items:flex-start; }
.do-panel-item:hover { background:#f0f9ff; }
.do-panel-item:last-child { border-bottom:none; }
.do-panel-avatar { width:36px; height:36px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; color:#fff; border:1.5px solid var(--do-slate-200); overflow:hidden; }
.do-panel-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.do-panel-item-body { flex:1; min-width:0; }
.do-panel-author { font-size:12px; font-weight:700; color:var(--do-slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.do-panel-text   { font-size:11px; color:var(--do-slate-600); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
.do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--do-slate-400); flex-wrap:wrap; }
.do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.do-sent-badge--neu { background:var(--do-slate-100); color:var(--do-slate-500); }
.do-panel-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:12px; color:var(--do-slate-400); font-size:13px; font-weight:600; }
.do-panel-spinner { width:28px; height:28px; border:2.5px solid var(--do-slate-100); border-top-color:var(--do-primary); border-radius:50%; animation:spin .65s linear infinite; }

/* ══ Detail Sub-Panel ══ */
#nssDetailPanel { position:absolute; inset:0; background:#fff; z-index:5; display:none; flex-direction:column; animation:slideInRight .2s cubic-bezier(.4,0,.2,1); }
#nssDetailPanel.visible { display:flex; }
.nssdp-header { display:flex; align-items:center; gap:8px; padding:12px 14px; background:var(--do-slate-50); border-bottom:1px solid var(--do-slate-200); flex-shrink:0; }
.nssdp-back { width:28px; height:28px; border-radius:var(--do-radius-sm); border:1px solid var(--do-slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--do-slate-500); transition:all .13s; flex-shrink:0; }
.nssdp-back:hover { background:var(--do-primary-lt); color:var(--do-primary); border-color:var(--do-primary); }
.nssdp-title { font-size:13px; font-weight:700; color:var(--do-slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.nssdp-close { width:28px; height:28px; border-radius:var(--do-radius-sm); border:1px solid var(--do-slate-200); background:#fff; cursor:pointer; font-size:16px; color:var(--do-slate-500); display:flex; align-items:center; justify-content:center; transition:all .14s; }
.nssdp-close:hover { background:var(--do-red); border-color:var(--do-red); color:#fff; }
.nssdp-body { overflow-y:auto; flex:1; padding:16px; }
.nssdp-body::-webkit-scrollbar { width:4px; }
.nssdp-body::-webkit-scrollbar-thumb { background:var(--do-slate-200); border-radius:99px; }
.nssdp-avatar-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.nssdp-avatar-lg { width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700; font-size:16px; display:flex; align-items:center; justify-content:center; border:2px solid var(--do-slate-200); overflow:hidden; flex-shrink:0; }
.nssdp-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.nssdp-author-name { font-size:14px; font-weight:700; color:var(--do-slate-900); }
.nssdp-author-handle { font-size:11px; color:var(--do-slate-400); font-weight:500; }
.nssdp-sent-badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:3px; font-size:11px; font-weight:700; margin-bottom:10px; }
.nssdp-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
.nssdp-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.nssdp-sent-badge--neu { background:var(--do-slate-100); color:var(--do-slate-500); }
.nssdp-content-text { font-size:12px; color:var(--do-slate-600); line-height:1.7; margin-bottom:12px; background:var(--do-slate-50); border-radius:var(--do-radius-sm); padding:10px 12px; border:1px solid var(--do-slate-200); word-break:break-word; }
.nssdp-meta-row { display:flex; align-items:center; justify-content:space-between; font-size:11px; color:var(--do-slate-400); font-weight:500; margin-bottom:10px; }
.nssdp-stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
.nssdp-stat-box { background:var(--do-slate-50); border-radius:var(--do-radius-sm); padding:8px 10px; border:1px solid var(--do-slate-200); text-align:center; }
.nssdp-stat-val { font-size:14px; font-weight:700; color:var(--do-slate-900); }
.nssdp-stat-lbl { font-size:9px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
.nssdp-media-wrap { border-radius:var(--do-radius-sm); overflow:hidden; margin-bottom:10px; background:#000; }
.nssdp-media-img { width:100%; max-height:220px; object-fit:cover; display:block; }
.nssdp-link-btn { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 14px; background:var(--do-primary); color:#fff; border-radius:var(--do-radius-sm); font-size:12px; font-weight:700; text-decoration:none; transition:filter .14s; width:100%; margin-top:4px; }
.nssdp-link-btn:hover { filter:brightness(1.1); color:#fff; }
.nssdp-link-btn i { font-size:14px; }

/* ══ Responsive ══ */
@media(max-width:1100px) { .nss-main-grid { grid-template-columns:1fr; } }
@media(max-width:768px)  { .do-page-header { flex-direction:column; align-items:flex-start; } .do-panel { width:100vw; } }
</style>
@endsection

@section('page-title', 'Net Sentiment Score')

@section('content')
@php
  $projects  = $projects  ?? [];
  $projectId = $projectId ?? request()->get('project_id');
  $startDate = $startDate ?? request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = $endDate   ?? request()->get('end_date', now()->format('Y-m-d'));
@endphp

@include('mk.layouts.partials.filter-datepicker')

{{-- ══ Sub-header ══ --}}
<div class="do-page-header fade-up">
    <div class="nss-media-wrap" id="nssMediaWrap">
        <div class="nss-media-menu" id="nssMediaMenu">
            <div class="nss-media-menu-section">Filter Media</div>
            <button class="nss-media-menu-item active" data-m="all"       onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:var(--do-primary)"></span>All Media</button>
            <button class="nss-media-menu-item"         data-m="doc"       onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#0284c7"></span>Mass Media (News)</button>
            <button class="nss-media-menu-item"         data-m="twitter"   onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#1d9bf0"></span>X / Twitter</button>
            <button class="nss-media-menu-item"         data-m="facebook"  onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#1877f2"></span>Facebook</button>
            <button class="nss-media-menu-item"         data-m="instagram" onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#e1306c"></span>Instagram</button>
            <button class="nss-media-menu-item"         data-m="youtube"   onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#ff0000"></span>YouTube</button>
            <button class="nss-media-menu-item"         data-m="tiktok"    onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#111827"></span>TikTok</button>
        </div>
    </div>
</div>

{{-- ════ PAGE EXPORT AREA WRAPPER ════ --}}
<div id="nssExportArea">

{{-- ══ Page Export Toolbar ══ --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
        <i class="ph ph-export"></i>
        <span>Export Halaman</span>
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Gauge + Breakdown</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="nssExportPdfBtn"
                onclick="NSSExport.run('pdf', this)" title="Export sebagai PDF">
            <i class="ph ph-file-pdf export-icon"></i>
            <span class="export-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img" id="nssExportImgBtn"
                onclick="NSSExport.run('image', this)" title="Export sebagai PNG">
            <i class="ph ph-image export-icon"></i>
            <span class="export-spinner"></span>
        </button>
    </div>
</div>

{{-- ══ KPI Cards ══ --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 bg-success text-white kpi-card-hover fade-up fade-up-d1" onclick="NSSPanel.open('pos')">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
                        <h3 class="mb-0 text-white f-w-300" id="statPos">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctPos"><i class="ph ph-chart-line-up me-1"></i> Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-thumbs-up"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="col-md-6 col-xl-4">
        <div class="card h-100 bg-danger text-white kpi-card-hover fade-up fade-up-d3" onclick="NSSPanel.open('neg')">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
                        <h3 class="mb-0 text-white f-w-300" id="statNeg">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctNeg"><i class="ph ph-chart-line-up me-1"></i> Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-thumbs-down"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 bg-warning text-white kpi-card-hover fade-up fade-up-d2" onclick="NSSPanel.open('neu')">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Neutral</p>
                        <h3 class="mb-0 text-white f-w-300" id="statNeu">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctNeu"><i class="ph ph-chart-line-up me-1"></i> Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-minus-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

{{-- ══ MAIN GRID ══ --}}
<div class="nss-main-grid">

    {{-- Gauge Card --}}
    <div class="do-card fade-up">
        <div id="card-nss-gauge">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <span class="do-head-icon"><i class="ph ph-gauge"></i></span>
                    <div>
                        <div class="do-card-title">Net Sentiment Score</div>
                        <div class="do-card-subtitle">Klik pada stat card untuk lihat mentions per sentimen</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="do-badge" id="nssBadgeMain">Loading…</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf"
                                onclick="NSSExport.runCard('card-nss-gauge','gauge','pdf',this)"
                                title="Export PDF">
                            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                        </button>
                        <button class="card-exp-btn card-exp-btn-img"
                                onclick="NSSExport.runCard('card-nss-gauge','gauge','image',this)"
                                title="Export PNG">
                            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="nss-gauge-wrap">
                <div class="nss-gauge-outer">
                    <svg id="nssGaugeSVG" viewBox="0 0 500 310" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <filter id="ndlShadow" x="-60%" y="-60%" width="220%" height="220%">
                                <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="rgba(0,0,0,.18)"/>
                            </filter>
                        </defs>
                        <path d="M 60,260 A 190,190 0 0,1 440,260" fill="none" stroke="#e2e8f0" stroke-width="36" stroke-linecap="butt"/>
                        <path d="M 60,260 A 190,190 0 0,1 250,70"  fill="none" stroke="#EF4444" stroke-width="36" stroke-linecap="butt"/>
                        <path d="M 250,70 A 190,190 0 0,1 440,260" fill="none" stroke="#0ea5e9" stroke-width="36" stroke-linecap="butt"/>
                        <line x1="60"    y1="254"   x2="60"    y2="266"   stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="111.4" y1="121.4" x2="119.9" y2="129.9" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="244"   y1="70"    x2="256"   y2="70"    stroke="#fff" stroke-width="3"   stroke-linecap="round"/>
                        <line x1="380.1" y1="129.9" x2="388.6" y2="121.4" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="440"   y1="254"   x2="440"   y2="266"   stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                        <text x="32"  y="265" text-anchor="middle" font-family="inherit" font-size="11" font-weight="600" fill="#94A3B8">−100%</text>
                        <text x="88"  y="100" text-anchor="middle" font-family="inherit" font-size="11" font-weight="600" fill="#94A3B8">−50%</text>
                        <text x="250" y="36"  text-anchor="middle" font-family="inherit" font-size="12" font-weight="700" fill="#334155">0%</text>
                        <text x="412" y="100" text-anchor="middle" font-family="inherit" font-size="11" font-weight="600" fill="#94A3B8">50%</text>
                        <text x="468" y="265" text-anchor="middle" font-family="inherit" font-size="11" font-weight="600" fill="#94A3B8">100%</text>
                        <g id="nssNeedle" transform="rotate(0, 250, 260)" filter="url(#ndlShadow)">
                            <polygon points="250,260 247.5,255 250,96 252.5,255" fill="#1e293b"/>
                            <circle cx="250" cy="260" r="12" fill="#1e293b"/>
                            <circle cx="250" cy="260" r="4.5" fill="#ffffff"/>
                        </g>
                    </svg>
                    <div class="nss-score-overlay">
                        <div id="nssScoreNum" class="nss-score-num">—</div>
                        <div id="nssScoreLbl" class="nss-score-lbl">NET SENTIMENT</div>
                    </div>
                </div>
            </div>
            <div class="nss-gauge-legend">
                <div class="nss-legend-item"><span class="nss-legend-dot" style="background:var(--c-pos);"></span><span id="legPos">Positive</span></div>
                <div class="nss-legend-item"><span class="nss-legend-dot" style="background:var(--c-neu);"></span><span id="legNeu">Neutral</span></div>
                <div class="nss-legend-item"><span class="nss-legend-dot" style="background:var(--c-neg);"></span><span id="legNeg">Negative</span></div>
            </div>
        </div>{{-- /card-nss-gauge --}}
    </div>

    {{-- Sidebar --}}
    <div class="nss-sidebar">

        {{-- Distribution Card --}}
        <div class="do-card fade-up fade-up-d1">
            <div id="card-nss-dist">
                <div class="do-card-head">
                    <div class="do-card-head-left">
                        <span class="do-head-icon"><i class="ph ph-chart-bar-horizontal"></i></span>
                        <div>
                            <div class="do-card-title">Distribution</div>
                            <div class="do-card-subtitle">Persentase per sentimen</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="do-badge">3 Kategori</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf"
                                    onclick="NSSExport.runCard('card-nss-dist','dist','pdf',this)"
                                    title="Export PDF">
                                <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                            </button>
                            <button class="card-exp-btn card-exp-btn-img"
                                    onclick="NSSExport.runCard('card-nss-dist','dist','image',this)"
                                    title="Export PNG">
                                <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="do-card-body">
                    <div class="nss-dist-wrap"><div id="chNssDist"></div></div>
                </div>
            </div>{{-- /card-nss-dist --}}
        </div>

        {{-- Score Breakdown Card --}}
        <div class="do-card fade-up fade-up-d2">
            <div id="card-nss-breakdown">
                <div class="do-card-head">
                    <div class="do-card-head-left">
                        <span class="do-head-icon"><i class="ph ph-list-numbers"></i></span>
                        <div>
                            <div class="do-card-title">Score Breakdown</div>
                            <div class="do-card-subtitle">Kalkulasi NSS real-time</div>
                        </div>
                    </div>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf"
                                onclick="NSSExport.runCard('card-nss-breakdown','breakdown','pdf',this)"
                                title="Export PDF">
                            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                        </button>
                        <button class="card-exp-btn card-exp-btn-img"
                                onclick="NSSExport.runCard('card-nss-breakdown','breakdown','image',this)"
                                title="Export PNG">
                            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                        </button>
                    </div>
                </div>
                <div class="nss-breakdown-body">
                    <div class="nss-brk-row"><span class="nss-brk-key"><span class="nss-brk-dot" style="background:var(--c-pos);"></span>Positive</span><span class="nss-brk-val" id="brkPos">—</span></div>
                    <div class="nss-brk-row"><span class="nss-brk-key"><span class="nss-brk-dot" style="background:var(--c-neu);"></span>Neutral</span><span class="nss-brk-val" id="brkNeu">—</span></div>
                    <div class="nss-brk-row"><span class="nss-brk-key"><span class="nss-brk-dot" style="background:var(--c-neg);"></span>Negative</span><span class="nss-brk-val" id="brkNeg">—</span></div>
                    <div class="nss-brk-divider"></div>
                    <div class="nss-brk-total-row"><span class="nss-brk-total-key">Total Mention</span><span class="nss-brk-total-val" id="brkTot">—</span></div>
                    <div class="nss-brk-nss-row"><span class="nss-brk-nss-key">NSS Score</span><span class="nss-brk-nss-val" id="brkNSS">—</span></div>
                </div>
                <div class="nss-formula-eq">NSS = (Pos − Neg) / (Pos + Neg) × 100</div>
            </div>{{-- /card-nss-breakdown --}}
        </div>

    </div>{{-- /nss-sidebar --}}
</div>{{-- /nss-main-grid --}}

</div>{{-- /nssExportArea --}}

{{-- Export Toast --}}
<div class="export-toast" id="nssExportToast">
    <i class="ph ph-check-circle" id="nssExportToastIcon"></i>
    <span id="nssExportToastMsg">Exporting…</span>
</div>

<input type="hidden" id="nssPID" value="{{ $projectId }}">
<input type="hidden" id="nssSD"  value="{{ $startDate }}">
<input type="hidden" id="nssED"  value="{{ $endDate }}">

{{-- ══ SLIDE PANEL ══ --}}
<div class="do-panel-overlay" id="nssPanelOverlay" onclick="NSSPanel.closeByOverlay()"></div>
<div class="do-panel" id="nssSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot"    id="nssPanelDot"></div>
        <span class="do-panel-title" id="nssPanelTitle">Mentions</span>
        <button class="do-panel-close" onclick="NSSPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta">
            <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
            <span id="nssPanelMeta">—</span>
        </div>
        <div class="do-panel-tabs">
            <button class="do-panel-tab active" data-s="all" onclick="NSSPanel.filterSent('all')">Semua</button>
            <button class="do-panel-tab pos"    data-s="pos" onclick="NSSPanel.filterSent('pos')">Pos</button>
            <button class="do-panel-tab neg"    data-s="neg" onclick="NSSPanel.filterSent('neg')">Neg</button>
            <button class="do-panel-tab neu"    data-s="neu" onclick="NSSPanel.filterSent('neu')">Neu</button>
        </div>
    </div>
    <div class="do-panel-list" id="nssPanelList"></div>
    {{-- Detail Sub-Panel --}}
    <div id="nssDetailPanel">
        <div class="nssdp-header">
            <button class="nssdp-back" onclick="NSSDetail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="nssdp-title" id="nssDpTitle">Detail</span>
            <button class="nssdp-close" onclick="NSSPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="nssdp-body" id="nssDpBody"></div>
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

const NSS_PID = {{ $projectId ? (int)$projectId : 'null' }};
const NSS_SD  = '{{ $startDate }}';
const NSS_ED  = '{{ $endDate }}';

const $      = id => document.getElementById(id);
const numFmt = n  => (parseInt(n)||0).toLocaleString('id-ID');
const pct    = (v,t) => t>0?((v/t)*100).toFixed(1)+'%':'0%';
const esc    = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
function getPrimary() { return getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim()||'#4361EE'; }

/* ── Count-up ── */
function countUp(el, target, dur=900) {
    if(!el) return; el.innerHTML='';
    const s=performance.now(), ease=t=>1-Math.pow(1-t,3);
    (function tick(n){const p=Math.min((n-s)/dur,1);el.textContent=numFmt(Math.round(target*ease(p)));if(p<1)requestAnimationFrame(tick);})(performance.now());
}

/* ── ECharts ── */
const NSS_Charts={_i:{},make(id){if(this._i[id]){try{this._i[id].dispose();}catch(e){}}const d=$(id);if(!d)return null;const c=echarts.init(d,null,{renderer:'canvas'});this._i[id]=c;return c;}};
window.addEventListener('resize',()=>Object.values(NSS_Charts._i).forEach(c=>{try{if(!c.isDisposed())c.resize();}catch(e){}}));
const EC_TT={backgroundColor:'#1e293b',borderColor:'#334155',borderWidth:1,padding:[9,13],textStyle:{color:'#fff',fontFamily:'inherit',fontSize:12},extraCssText:'border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.3);'};

/* ── Gauge ── */
let _raf=null;
function renderGauge(nss){
    const val=Math.max(-100,Math.min(100,nss)),targetRot=(val/100)*90;
    const needle=$('nssNeedle'),scoreEl=$('nssScoreNum'),labelEl=$('nssScoreLbl');
    const isPos=val>5,isNeg=val<-5;
    const color=isPos?'#0ea5e9':isNeg?'#EF4444':'#94A3B8';
    const lbl=isPos?'POSITIF':isNeg?'NEGATIF':'NETRAL';
    const finalStr=(val>0?'+':'')+val.toFixed(0)+'%';
    const tf=needle.getAttribute('transform')||'rotate(0,250,260)';
    const cur=parseFloat((tf.match(/rotate\(([-\d.]+)/)||[0,0])[1]);
    if(_raf)cancelAnimationFrame(_raf);
    const dur=1200,t0=performance.now(),ease=t=>t<.5?2*t*t:-1+(4-2*t)*t;
    (function frame(now){
        const p=Math.min((now-t0)/dur,1),rot=cur+(targetRot-cur)*ease(p);
        needle.setAttribute('transform',`rotate(${rot.toFixed(3)},250,260)`);
        const lv=(rot/90)*100;
        if(scoreEl){scoreEl.textContent=(lv>0?'+':'')+lv.toFixed(0)+'%';scoreEl.style.color=color;}
        if(p<1){_raf=requestAnimationFrame(frame);}else{
            if(scoreEl){scoreEl.textContent=finalStr;scoreEl.style.color=color;}
            if(labelEl){labelEl.textContent=lbl;labelEl.style.color=isPos?'#0ea5e9':isNeg?'#dc2626':'#94A3B8';}
            _raf=null;
        }
    })(performance.now());
}

/* ── Distribution chart ── */
function renderDist(pos,neu,neg){
    const dom=$('chNssDist');if(!dom)return;
    const chart=NSS_Charts.make('chNssDist');
    const tot=pos+neu+neg||1;
    const pPct=+(pos/tot*100).toFixed(1),nuPct=+(neu/tot*100).toFixed(1),nePct=+(neg/tot*100).toFixed(1);
    chart.setOption({
        animation:true,animationDuration:900,animationEasing:'cubicInOut',backgroundColor:'transparent',
        tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'none'},formatter:params=>{const p=params.find(x=>x.value>0);return p?`<div style="font-weight:700;margin-bottom:4px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};margin-right:6px;"></span>${p.seriesName}</div><div style="display:flex;justify-content:space-between;gap:16px;"><span style="opacity:.7;">Share</span><span style="font-weight:700;">${p.value}%</span></div>`:'';}},
        grid:{left:0,right:22,top:4,bottom:0,containLabel:true},
        xAxis:{type:'value',max:100,axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:'inherit',fontSize:10,color:'#94A3B8',formatter:v=>v+'%'}},
        yAxis:{type:'category',data:['Negative','Neutral','Positive'],axisTick:{show:false},axisLine:{show:false},axisLabel:{fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#475569',margin:10}},
        series:[
            {name:'Positive',type:'bar',data:[null,null,pPct],barMaxWidth:28,barBorderRadius:[0,4,4,0],itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:'rgba(14,165,233,.1)'},{offset:1,color:'#0ea5e9'}]}},label:{show:true,position:'right',fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#0ea5e9',formatter:v=>v.value+'%'}},
            {name:'Neutral', type:'bar',data:[null,nuPct,null],barMaxWidth:28,barBorderRadius:[0,4,4,0],itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:'rgba(148,163,184,.1)'},{offset:1,color:'#94A3B8'}]}},label:{show:true,position:'right',fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#94A3B8',formatter:v=>v.value+'%'}},
            {name:'Negative',type:'bar',data:[nePct,null,null],barMaxWidth:28,barBorderRadius:[0,4,4,0],itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:'rgba(239,68,68,.1)'},{offset:1,color:'#EF4444'}]}},label:{show:true,position:'right',fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#EF4444',formatter:v=>v.value+'%'}},
        ],
    });
    chart.on('click',params=>{if(params.componentType==='series'){const m={Positive:'pos',Neutral:'neu',Negative:'neg'};NSSPanel.open(m[params.seriesName]||'pos');}});
    chart.on('mouseover',p=>{if(p.componentType==='series')chart.getDom().style.cursor='pointer';});
    chart.on('mouseout',()=>{chart.getDom().style.cursor='default';});
}

/* ── Breakdown ── */
function updateBreakdown(pos,neu,neg,nss){
    const tot=pos+neu+neg;
    $('brkPos').textContent=numFmt(pos); $('brkNeu').textContent=numFmt(neu);
    $('brkNeg').textContent=numFmt(neg); $('brkTot').textContent=numFmt(tot);
    const isPos=nss>5,isNeg=nss<-5;
    const color=isPos?'#0ea5e9':isNeg?'#EF4444':'#94A3B8';
    const nssStr=(nss>=0?'+':'')+nss.toFixed(1)+'%';
    const nssEl=$('brkNSS');
    nssEl.textContent=nssStr; nssEl.style.color=color;
    const row=nssEl.closest('.nss-brk-nss-row');
    row.style.background    =isPos?'rgba(14,165,233,.07)':isNeg?'rgba(239,68,68,.07)':'rgba(148,163,184,.07)';
    row.style.borderTopColor=isPos?'rgba(14,165,233,.2)' :isNeg?'rgba(239,68,68,.2)' :'rgba(148,163,184,.2)';
    const keyEl=row.querySelector('.nss-brk-nss-key'); if(keyEl)keyEl.style.color=color;
    const badge=$('nssBadgeMain');
    if(badge){badge.textContent=nssStr;badge.className='do-badge'+(isPos?' do-badge--pos':isNeg?' do-badge--neg':'');}
}

/* ── Data loader ── */
async function loadNSS(){
    if(!NSS_PID){
        ['statPos','statNeu','statNeg'].forEach(id=>{const el=$(id);if(el)el.textContent='—';});
        ['pctPos','pctNeu','pctNeg'].forEach(id=>{const el=$(id);if(el)el.innerHTML='<i class="ph ph-warning-circle me-1"></i>No Project';});
        const badge=$('nssBadgeMain'); if(badge) badge.textContent='No Project';
        return;
    }
    try{
        const media=document.querySelector('.nss-media-menu-item.active')?.dataset.m||'all';
        const res=await fetch(`/mk/api/sentiment/totals?project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&media=${media}`);
        if(!res.ok)throw new Error(`HTTP ${res.status}`);
        const json=await res.json(); if(json.error)throw new Error(json.error);
        const t=json.totals||{pos:0,neg:0,neu:0};
        const pos=parseInt(t.pos)||0,neg=parseInt(t.neg)||0,neu=parseInt(t.neu)||0;
        const tot=pos+neg+neu,posneg=pos+neg,nss=posneg===0?0:((pos-neg)/posneg*100);
        countUp($('statPos'),pos); countUp($('statNeu'),neu); countUp($('statNeg'),neg);
        $('pctPos').innerHTML=`<i class="ph ph-chart-line-up me-1"></i>${pct(pos,tot)}`; $('pctNeu').innerHTML=`<i class="ph ph-chart-line-up me-1"></i>${pct(neu,tot)}`; $('pctNeg').innerHTML=`<i class="ph ph-chart-line-up me-1"></i>${pct(neg,tot)}`;
        $('legPos').textContent=numFmt(pos)+' Positive'; $('legNeu').textContent=numFmt(neu)+' Neutral'; $('legNeg').textContent=numFmt(neg)+' Negative';
        updateBreakdown(pos,neu,neg,nss); renderGauge(nss); renderDist(pos,neu,neg);
    }catch(err){
        console.error('loadNSS:',err);
        ['statPos','statNeu','statNeg'].forEach(id=>{const el=$(id);if(el)el.innerHTML='<span style="font-size:12px;color:rgba(255,255,255,.8);font-weight:600;">Error</span>';});
        ['pctPos','pctNeu','pctNeg'].forEach(id=>{const el=$(id);if(el)el.innerHTML='<i class="ph ph-warning-circle me-1"></i>Gagal memuat';});
        const badge=$('nssBadgeMain'); if(badge) badge.textContent='Error';
    }
}

/* ══ Media Dropdown ══ */
const MEDIA_LABELS={all:'All Media',doc:'Mass Media (News)',twitter:'X / Twitter',facebook:'Facebook',instagram:'Instagram',youtube:'YouTube',tiktok:'TikTok'};
const NSSPage={
    toggleMenu(){const o=$('nssMediaMenu').classList.toggle('show');$('nssMediaBtn')?.classList.toggle('open',o);},
    selectMedia(el){
        document.querySelectorAll('.nss-media-menu-item').forEach(i=>i.classList.remove('active'));
        el.classList.add('active');
        const lbl=$('nssMediaLabel'); if(lbl) lbl.textContent=MEDIA_LABELS[el.dataset.m]||'All Media';
        $('nssMediaMenu').classList.remove('show'); $('nssMediaBtn')?.classList.remove('open');
        NSSPanel._cache={}; loadNSS();
    },
};
document.addEventListener('click',e=>{const w=$('nssMediaWrap');if(w&&!w.contains(e.target)){$('nssMediaMenu').classList.remove('show');$('nssMediaBtn')?.classList.remove('open');}});

/* ══ SLIDE PANEL ══ */
const NSSPanel=(()=>{
    let _cache={},_allItems=[],_filtered=[],_curSent='all';
    const SENT_MAP={
        '1':'pos','positive':'pos','positif':'pos','pos':'pos',
        '-1':'neg','2':'neg','negative':'neg','negatif':'neg','neg':'neg'
    };
    const SENT_COLORS={pos:'#0ea5e9',neg:'#EF4444',neu:'#94A3B8',all:'#4361EE'};
    const SENT_LABELS={pos:'Positive',neg:'Negative',neu:'Neutral',all:'All Mentions'};
    function _normSent(item){
        const r=String(item.sentiment_class||item.class_sentiment||item.sentiment||item.sentiment_label||item.sentiment_str||'0').toLowerCase().trim();
        return SENT_MAP[r]||'neu';
    }

    async function open(sentiment){
        _curSent=sentiment||'all';
        const color=SENT_COLORS[_curSent]||getPrimary();
        const label=SENT_LABELS[_curSent]||'Mentions';
        const media=document.querySelector('.nss-media-menu-item.active')?.dataset.m||'all';
        $('nssPanelDot').style.background=color;
        $('nssPanelTitle').textContent=label;
        $('nssPanelMeta').textContent=NSS_SD+' – '+NSS_ED;
        document.querySelectorAll('#nssSntPanel .do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s===_curSent));
        const list=$('nssPanelList');
        list.innerHTML=`<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;
        const overlay=$('nssPanelOverlay'),panel=$('nssSntPanel');
        overlay.classList.remove('hiding'); panel.classList.remove('hiding');
        overlay.classList.add('show'); panel.classList.add('show');
        try{
            const key=`${NSS_PID}_${media}_${NSS_SD}_${NSS_ED}`;
            if(!_cache[key]) _cache[key]=await _fetchAll(media);
            _allItems=_cache[key];
            _filtered=_filterBySent(_allItems,_curSent);
            _render(list,_filtered);
        }catch(err){
            list.innerHTML=`<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:13px;">Gagal memuat data<br><small>${esc(err.message)}</small></div>`;
        }
    }

    function close(){
        NSSDetail.close();
        const dpBody=$('nssDpBody');
        if(dpBody) dpBody.querySelectorAll('iframe').forEach(f=>{f.src='';f.remove();});
        const overlay=$('nssPanelOverlay'),panel=$('nssSntPanel');
        panel.classList.add('hiding'); overlay.classList.add('hiding');
        setTimeout(()=>{panel.classList.remove('show','hiding');overlay.classList.remove('show','hiding');},240);
    }
    function closeByOverlay(){close();}
    function filterSent(sent){
        _curSent=sent;
        document.querySelectorAll('#nssSntPanel .do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s===sent));
        _filtered=_filterBySent(_allItems,sent);
        _render($('nssPanelList'),_filtered);
    }
    function _filterBySent(items,sent){return sent==='all'?items:items.filter(i=>_normSent(i)===sent);}

    async function _fetchAll(media){
        const platforms=media==='all'?['doc','twitter','facebook','instagram','youtube','tiktok']:[media];
        const res=await Promise.allSettled(platforms.map(p=>_fetchOne(p)));
        return res.flatMap(r=>r.status==='fulfilled'?r.value:[]);
    }
    async function _fetchOne(platform){
        const q=`project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&rows=200&start=0`;
        if(platform==='doc'){
          const docQ=`project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&rows=500&start=0&media=doc`;
          try{
            const res=await fetch(`/mk/api/news/articles?${docQ}`);
            if(!res.ok) return [];
            const d=await res.json();
            const raw=Array.isArray(d?.data)?d.data:(Array.isArray(d)?d:[]);
            return raw.map(i=>({
                ...i,
                _platform:'doc',
                content: i.content || i.summary || i.text || '',
                title: i.title || i.subject || 'Untitled',
                url: i.url || i.link || i.post_url || i.article_url || i.source_url || i.permalink || i.news_url || i.web_url || '',
                class_sentiment: String(i.class_sentiment || i.sentiment_class || i.sentiment || '0')
            }));
          }catch(e){ return []; }
        }
        const eps={
            twitter:  `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
            facebook: `/mk/api/news/fb-top-status?${q}&sub=fblike`,
            instagram:`/mk/api/news/ig-top-status?${q}`,
            youtube:  `/mk/api/news/ytb-top-status?${q}`,
            tiktok:   `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
        };
        const url=eps[platform];if(!url)return[];
        try{
            const ctrl=new AbortController(),tid=setTimeout(()=>ctrl.abort(),20000);
            const res=await fetch(url,{signal:ctrl.signal});clearTimeout(tid);
            if(!res.ok)return[];
            const data=await res.json();
            let items=Array.isArray(data?.data?.data)?data.data.data:Array.isArray(data?.data)?data.data:(Array.isArray(data)?data:[]);
            items=items.map(i=>({...i,_platform:platform}));
            /* Twitter fallback chain */
            if(platform==='twitter' && items.length===0){
              for(const fbUrl of [`/mk/api/x/most-retweets?${q}`,`/mk/api/x/user-mentions?${q}`]){
                try{ const r2=await fetch(fbUrl); if(r2.ok){ const d2=await r2.json(); const i2=Array.isArray(d2?.data)?d2.data:Array.isArray(d2)?d2:[]; if(i2.length>0){ items=i2.map(i=>({...i,_platform:'twitter'})); break; } } }catch(e){}
              }
              if(items.length===0){
                try{ const r3=await fetch(`/mk/api/news/mentions?${q}`); const d3=await r3.json(); let all=Array.isArray(d3?.data?.data)?d3.data.data:Array.isArray(d3?.data)?d3.data:[];
                  items=all.filter(m=>{const tc=String(m.tcode||'').toLowerCase(),mt=String(m.media_type||'').toLowerCase(),u2=String(m.url||'').toLowerCase(); return tc==='twit'||tc==='rt'||mt==='twit'||mt==='twitter'||mt==='x'||u2.includes('twitter.com')||u2.includes('x.com');}).map(i=>({...i,_platform:'twitter'}));
                }catch(e){}
              }
            }
            return items;
        }catch(e){return[];}
    }

    function _render(list,items, showAll = false){
        if(!items.length){list.innerHTML=`<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>`;return;}
        const SHOW=80;
        const visibleItems = showAll ? items : items.slice(0,SHOW);
        list.innerHTML=visibleItems.map(item=>{
            const plat=item._platform||'doc';
            const meta=PLAT_META[plat]||{label:plat,color:getPrimary()};
            const sent=_normSent(item);
            const sentText={pos:'Pos',neg:'Neg',neu:'Neu'}[sent]||'Neu';
            const ao=(()=>{if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return {};}})();
            const rawName=(()=>{
                if(plat==='facebook')  return item.from_name||item.page_name||item.author_name||ao?.name||item.author_handle||null;
                if(plat==='instagram') return item.username||item.user_name||null;
                if(plat==='tiktok')    return item.author_nickname||item.nickname||ao?.nickname||null;
                if(plat==='youtube')   return item.channel_title||item.channel_name||null;
                if(plat==='twitter')   return item.name||ao?.name||ao?.scr_name||item.author_name||item.author_scr_name||null;
                return null;
            })();
            let name=(rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'').trim();
            if(!name||/^\d{5,}$/.test(name)||name.toLowerCase()==='unknown'){
              const alt=(item.author_handle||item.author_scr_name||item.screen_name||ao?.scr_name||ao?.username||item.username||item.nickname||'').trim();
              if(alt&&!/^\d{5,}$/.test(alt)) name=alt; else if(!name) name='Unknown';
            }
            const dName=name;
            const rawHandle=(item.author_handle||item.author_scr_name||item.screen_name||ao?.scr_name||item.username||item.handle||'').trim();
            const handle=rawHandle&&rawHandle.toLowerCase()!==dName.toLowerCase()?((['twitter','instagram','tiktok'].includes(plat))?(rawHandle.startsWith('@')?rawHandle:'@'+rawHandle):rawHandle):'';
            const artTitle=(plat==='doc')?(item.title||'').replace(/<[^>]*>/g,'').trim():'';
            const text=(()=>{if(plat==='doc'){const c=(item.content||'').replace(/<[^>]*>/g,'').trim(); return c?c.slice(0,150):(item.title||'').slice(0,150);} return (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,150);})();
            const av=(item.avatar_url||item.profile_image_url||item.author_image||ao?.image||item.profile_image||'').trim();
            const dt=(item.date_created||item.created_at||item.publish_date||'').split('T')[0];
            const words=dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
            const ini=(words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||'?')).toUpperCase();
            const safeIni=ini.replace(/['"]/g,'');
            const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}';">`:(plat==='doc'?`<i class="ph ph-newspaper" style="font-size:14px;color:#fff;"></i>`:ini);
            const docUrl=(plat==='doc')?(item.url||item.link||'').trim():'';
            const itemJson=esc(JSON.stringify({...item,_platform:plat}));
            /* Doc: special rendering with article title */
            if(plat==='doc'&&artTitle){
              return `<div class="do-panel-item" data-item='${itemJson}' onclick="NSSDetail.open(this)">
                <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);"><i class="ph ph-newspaper" style="font-size:14px;color:#fff;"></i></div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author" style="font-size:10px;color:#64748b;">${esc(dName)}</div>
                    <div style="font-size:12px;font-weight:700;color:#1e293b;line-height:1.3;margin:2px 0 3px;">${esc(artTitle.slice(0,100))}</div>
                    <div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div>
                    <div class="do-panel-footer">
                        <span class="do-sent-badge do-sent-badge--${sent}">${sentText}</span>
                        <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                        <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                        ${docUrl?`<a href="${esc(docUrl)}" target="_blank" rel="noopener noreferrer" style="margin-left:auto;font-size:10px;font-weight:700;color:${meta.color};display:inline-flex;align-items:center;gap:3px;text-decoration:none;" onclick="event.stopPropagation()" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'"><i class="ph ph-arrow-square-out" style="font-size:12px;"></i>Buka</a>`:''}
                        ${dt?`<span>${dt}</span>`:''}
                    </div>
                </div>
              </div>`;
            }
            return `<div class="do-panel-item" data-item='${itemJson}' onclick="NSSDetail.open(this)">
                <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author">${esc(dName)}</div>
                    ${handle?`<div style="font-size:10px;color:#94a3b8;margin-top:-1px;">${esc(handle)}</div>`:''}
                    <div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div>
                    <div class="do-panel-footer">
                        <span class="do-sent-badge do-sent-badge--${sent}">${sentText}</span>
                        <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                        <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                        ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
                    </div>
                </div>
            </div>`;
        }).join('');
        if(!showAll && items.length>SHOW) {
             const btnWrap = document.createElement('div');
             btnWrap.style.padding = '16px';
             btnWrap.style.textAlign = 'center';
             btnWrap.style.borderTop = '1px dashed rgba(0,0,0,.08)';
             btnWrap.style.background = '#f8fafc';
             btnWrap.innerHTML = `<button onclick="NSSPanel.showAll()" style="background:#038047;color:#fff;border:none;padding:8px 24px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;box-shadow:0 2px 4px rgba(3,128,71,.2);" onmouseover="this.style.background='#026136';this.style.transform='translateY(-1px)';" onmouseout="this.style.background='#038047';this.style.transform='none';">Muat Lebih Banyak</button>`;
             list.appendChild(btnWrap);
        }
    }

    function showAll() { _render($('nssPanelList'), _filtered, true); }

    return{open,close,closeByOverlay,filterSent,showAll,get _cache(){return _cache;},set _cache(v){_cache=v;}};
})();

/* ══ DETAIL PANEL ══ */
const NSSDetail = {
    open(el) {
        let item;
        try { const raw=el.getAttribute('data-item'); item=JSON.parse(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"')); } catch(e){ console.warn('NSSDetail parse error',e); return; }
        const plat=item._platform||'doc';
        const PLAT_META2={doc:{label:'Online News',color:'#0284c7'},twitter:{label:'X / Twitter',color:'#1d9bf0'},facebook:{label:'Facebook',color:'#1877f2'},instagram:{label:'Instagram',color:'#e1306c'},youtube:{label:'YouTube',color:'#ff0000'},tiktok:{label:'TikTok',color:'#111827'}};
        const meta=PLAT_META2[plat]||{label:plat,color:'#4361EE'};
        const panel=$('nssDetailPanel'),body=$('nssDpBody'),title=$('nssDpTitle');
        if(!panel) return;
        const ao=(()=>{if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return{};}})();
        const name=(item.from_name||item.page_name||item.author_nickname||item.nickname||item.channel_title||item.channel_name||item.author_name||item.username||item.user_name||item.author_scr_name||item.screen_name||ao?.name||ao?.scr_name||item.publisher||item.source_name||item.name||'Unknown').trim();
        const displayName=/^\d{8,}$/.test(name)?`User ${name.slice(-4)}`:name;
        const rawHandle=(item.author_scr_name||item.screen_name||ao?.scr_name||item.username||item.handle||'').trim();
        const handle=rawHandle&&rawHandle.toLowerCase()!==displayName.toLowerCase()?(rawHandle.startsWith('@')?rawHandle:'@'+rawHandle):'';
        const content=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
        const av=(item.avatar_url||item.profile_image_url||item.author_image||ao?.image||item.profile_image||item.thumbnail||item.picture||'').trim();
        let url = item.url || item.link || item.post_url || item.article_url || item.source_url || item.permalink || item.news_url || item.web_url || '';
        if(!url&&plat==='twitter'){const scr=rawHandle.replace(/^@/,''),subId=item.sub_id||''; if(subId) url=`https://twitter.com/${encodeURIComponent(scr)}/status/${encodeURIComponent(subId)}`; else if(scr) url=`https://twitter.com/${encodeURIComponent(scr)}`;}
        const date=item.date_created||item.created_at||item.publish_date||'';
        const sentRaw=String(item.sentiment_class||item.class_sentiment||item.sentiment||item.sentiment_label||item.sentiment_str||'0').toLowerCase().trim();
        const sent=SENT_MAP[sentRaw]||'neu';
        const sentLbl={pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];
        title.textContent=displayName;
        const words=displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
        const ini=(words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||displayName[0]||'?')).toUpperCase().replace(/['"]/, '');
        const avHtml=(av&&(av.startsWith('http://')||av.startsWith('https://')))?`<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini}'">`:ini;
        let dtFmt=''; if(date){try{dtFmt=new Date(date).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){dtFmt=date.split('T')[0];}}
        let mediaHtml='';
        if(plat==='youtube'){let ytId=''; if(url){const m=url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/); if(m) ytId=m[1];} if(!ytId&&item.video_id) ytId=item.video_id; if(ytId) mediaHtml=`<div class="nssdp-media-wrap"><iframe style="width:100%;height:210px;border:none;display:block;border-radius:8px;" src="https://www.youtube.com/embed/${ytId}?rel=0&modestbranding=1" allowfullscreen></iframe></div>`;}
        else if(plat==='tiktok'){let videoId=''; if(url){const m=url.match(/\/video\/(\d+)/); if(m) videoId=m[1];} if(!videoId&&item.id){const m=String(item.id).match(/(\d{10,})/); if(m) videoId=m[1];} if(videoId) mediaHtml=`<div class="nssdp-media-wrap"><iframe style="width:100%;height:480px;border:none;display:block;border-radius:8px;" src="https://www.tiktok.com/embed/v2/${videoId}" allow="autoplay" allowfullscreen></iframe></div>`; else{const imgUrl=item.image_url||item.thumbnail||item.media_url||item.picture||''; if(imgUrl) mediaHtml=`<div class="nssdp-media-wrap"><img class="nssdp-media-img" src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'"></div>`;}}
        else{const imgUrl=item.image_url||item.thumbnail||item.media_url||item.picture||''; if(imgUrl) mediaHtml=`<div class="nssdp-media-wrap"><img class="nssdp-media-img" src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'"></div>`;}
        const statsMap={twitter:[['Views',item.view_cnt||item.num_views||0],['Retweet',item.rt||item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0]],facebook:[['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],instagram:[['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],youtube:[['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||0]],tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],doc:[['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]]};
        const stats=statsMap[plat]||[];
        const statsHtml=stats.some(s=>parseInt(s[1])>0)?`<div class="nssdp-stats-grid">${stats.map(([l,v])=>`<div class="nssdp-stat-box"><div class="nssdp-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="nssdp-stat-lbl">${l}</div></div>`).join('')}</div>`:'';
        body.innerHTML=`
        <div class="nssdp-avatar-row">
          <div class="nssdp-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
          <div>
            <div class="nssdp-author-name">${esc(displayName)}</div>
            ${handle?`<div class="nssdp-author-handle">${esc(handle)}</div>`:''}
            <span style="background:${meta.color}22;color:${meta.color};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;margin-top:4px;">${meta.label}</span>
          </div>
        </div>
        ${dtFmt?`<div class="nssdp-meta-row"><span>${dtFmt}</span></div>`:''}
        <span class="nssdp-sent-badge nssdp-sent-badge--${sent}">${sentLbl}</span>
        ${mediaHtml}
        ${content?`<div class="nssdp-content-text">${esc(content)}</div>`:''}
        ${statsHtml}
        ${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="nssdp-link-btn"><i class="ph ph-arrow-square-out"></i>Lihat Sumber Asli</a>`:''}`;  
        panel.classList.add('visible');
    },
    close() { $('nssDetailPanel')?.classList.remove('visible'); }
};

/* ══════════════════════════════════════════════════════
   NSSExport — FIXED v6
   Fix tambahan: adjust posisi .nss-score-overlay
   saat capture agar teks +27% tidak tertutup needle
══════════════════════════════════════════════════════ */
const NSSExport = (() => {
    let _toastTimer = null;

    function _toast(msg, type = 'default', duration = 3200) {
        const t   = document.getElementById('nssExportToast');
        const m   = document.getElementById('nssExportToastMsg');
        const ico = document.getElementById('nssExportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className   = 'export-toast show ' + (type !== 'default' ? type : '');
        const icons   = { success: 'ph-check-circle', error: 'ph-x-circle', default: 'ph-spinner' };
        ico.className = 'ph ' + (icons[type] || icons.default);
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => t.classList.remove('show'), duration);
    }

    function _btnState(btns, loading) {
        [].concat(btns).forEach(b => {
            if (!b) return;
            b.disabled = loading;
            b.classList.toggle('exporting', loading);
        });
    }

    let _freezeStyle = null;

    function _freezeAnimations() {
        /* Cancel gauge needle animation */
        if (typeof _raf !== 'undefined' && _raf) {
            cancelAnimationFrame(_raf);
        }

        /* Freeze ECharts */
        Object.values(NSS_Charts._i).forEach(c => {
            try {
                if (!c.isDisposed()) {
                    c.setOption({ animation: false }, false);
                    if (c.getZr && typeof c.getZr === 'function') {
                        c.getZr().flush(true);
                    }
                    c.resize();
                }
            } catch (e) {}
        });

        /* Hanya freeze shimmer skeleton — JANGAN reset transform/opacity
           karena akan menghapus posisi final elemen .fade-up */
        _freezeStyle = document.createElement('style');
        _freezeStyle.id = '__nss_freeze__';
        _freezeStyle.textContent = `
            #nssExportArea .kpi-card-hover::before {
                display: none !important;
            }
        `;
        document.head.appendChild(_freezeStyle);
    }

    function _restoreAnimations() {
        if (_freezeStyle) {
            _freezeStyle.remove();
            _freezeStyle = null;
        }
        Object.values(NSS_Charts._i).forEach(c => {
            try {
                if (!c.isDisposed()) c.setOption({ animation: true }, false);
            } catch (e) {}
        });
    }

    /* ════════════════════════════════════════════════════
       Capture full page
       Kunci: paksa semua .fade-up ke posisi final sebelum
       capture dengan menambahkan class helper, lalu restore
    ════════════════════════════════════════════════════ */
    async function _capture() {
        const area = document.getElementById('nssExportArea');
        if (!area) throw new Error('nssExportArea tidak ditemukan');

        window.scrollTo({ top: 0, behavior: 'instant' });

        /* Paksa semua animasi fade-up ke state final */
        const animStyle = document.createElement('style');
        animStyle.id = '__nss_anim_fix__';
        animStyle.textContent = `
            #nssExportArea .fade-up,
            #nssExportArea .fade-up-d1,
            #nssExportArea .fade-up-d2,
            #nssExportArea .fade-up-d3 {
                animation: none !important;
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
            #nssExportArea [style*="animation"] {
                animation-fill-mode: both !important;
                animation-delay: 0s !important;
                animation-duration: 0.01s !important;
            }
        `;
        document.head.appendChild(animStyle);

        await new Promise(r => setTimeout(r, 200));

        _freezeAnimations();
        await new Promise(r => setTimeout(r, 300));

        const areaH = area.scrollHeight;

        const canvas = await html2canvas(area, {
            scale:           2,
            useCORS:         true,
            allowTaint:      false,
            backgroundColor: '#f1f5f9',
            logging:         false,
            removeContainer: true,
            x:               0,
            y:               0,
            scrollX:         0,
            scrollY:         0,
            windowWidth:     document.documentElement.scrollWidth,
            windowHeight:    areaH,
            width:           area.offsetWidth,
            height:          areaH,
            ignoreElements:  el =>
                el.hasAttribute('data-html2canvas-ignore') ||
                el.id === 'nssExportPdfBtn'  ||
                el.id === 'nssExportImgBtn'  ||
                el.id === 'nssExportToast'   ||
                el.id === 'nssSntPanel'      ||
                el.id === 'nssPanelOverlay',
        });

        animStyle.remove();
        _restoreAnimations();
        return canvas;
    }

    /* ════════════════════════════════════════════════════
       Capture single card
    ════════════════════════════════════════════════════ */
    async function _captureCard(areaId) {
        const area = document.getElementById(areaId);
        if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');

        const animStyle = document.createElement('style');
        animStyle.id = '__nss_anim_fix__';
        animStyle.textContent = `
            #${areaId} .fade-up,
            #${areaId} .fade-up-d1,
            #${areaId} .fade-up-d2,
            #${areaId} .fade-up-d3 {
                animation: none !important;
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
        `;
        document.head.appendChild(animStyle);

        _freezeAnimations();
        await new Promise(r => setTimeout(r, 300));

        const canvas = await html2canvas(area, {
            scale:           2,
            useCORS:         true,
            allowTaint:      false,
            backgroundColor: '#ffffff',
            logging:         false,
            removeContainer: true,
            ignoreElements:  el => el.hasAttribute('data-html2canvas-ignore'),
        });

        animStyle.remove();
        _restoreAnimations();
        return canvas;
    }

    /* ════════════════════════════════════════════════════
       PDF Helpers
    ════════════════════════════════════════════════════ */
    function _drawPdfHeader(pdf, pW, margin, label) {
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + (label || 'Net Sentiment Score'), margin, 7.5);
        const now = new Date().toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - margin, 7.5, { align: 'right' });
    }

    function _fitCanvas(pdf, canvas, margin, pW, pH, label) {
        _drawPdfHeader(pdf, pW, margin, label);
        const usableW = pW - margin * 2;
        const usableH = pH - margin * 2 - 18;
        const ratio   = Math.min(usableW / canvas.width, usableH / canvas.height);
        const dstW    = canvas.width  * ratio;
        const dstH    = canvas.height * ratio;
        pdf.addImage(
            canvas.toDataURL('image/png', 1), 'PNG',
            margin + (usableW - dstW) / 2,
            14    + (usableH - dstH) / 2,
            dstW, dstH
        );
        pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
        pdf.text('Halaman 1 / 1', pW / 2, pH - 3, { align: 'center' });
    }

    function _canvasToPdf(pdf, canvas, margin, pW, pH, label) {
        const usableW  = pW - margin * 2;
        const usableH  = pH - margin * 2 - 14;
        const ratio    = usableW / canvas.width;
        const slicePx  = Math.floor(usableH / ratio);
        const numPages = Math.ceil(canvas.height / slicePx);

        for (let page = 0; page < numPages; page++) {
            if (page > 0) pdf.addPage();
            _drawPdfHeader(pdf, pW, margin, label);
            const srcY     = page * slicePx;
            const srcSlice = Math.min(slicePx, canvas.height - srcY);
            if (srcSlice <= 0) break;
            const dstH  = srcSlice * ratio;
            const slice = document.createElement('canvas');
            slice.width  = canvas.width;
            slice.height = Math.ceil(srcSlice);
            const ctx    = slice.getContext('2d');
            ctx.fillStyle = '#f1f5f9';
            ctx.fillRect(0, 0, slice.width, slice.height);
            ctx.drawImage(canvas, 0, srcY, canvas.width, srcSlice, 0, 0, canvas.width, srcSlice);
            pdf.addImage(slice.toDataURL('image/png', 1), 'PNG', margin, 14, usableW, dstH);
            pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
            pdf.text(`Halaman ${page + 1} / ${numPages}`, pW / 2, pH - 3, { align: 'center' });
        }
        return numPages;
    }

    const _cardLabels = {
        gauge:     'Net Sentiment Score Gauge',
        dist:      'Sentiment Distribution',
        breakdown: 'Score Breakdown',
    };

    /* ════════════════════════════════════════════════════
       PUBLIC: Export per card
    ════════════════════════════════════════════════════ */
    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }
        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);
        try {
            const canvas = await _captureCard(areaId);
            const stamp  = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            const fname  = `nss_${cardKey}_${NSS_PID}_${stamp}`;
            const label  = _cardLabels[cardKey] || cardKey;
            if (type === 'image') {
                const a    = document.createElement('a');
                a.download = fname + '.png';
                a.href     = canvas.toDataURL('image/png', 1);
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const orient = canvas.width > canvas.height ? 'landscape' : 'portrait';
                const pdf    = new jsPDF({ orientation: orient, unit: 'mm', format: 'a4' });
                _fitCanvas(pdf, canvas, 10, pdf.internal.pageSize.getWidth(), pdf.internal.pageSize.getHeight(), label);
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch (err) {
            console.error('[NSSExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btn, false);
        }
    }

    /* ════════════════════════════════════════════════════
       PUBLIC: Export full page
    ════════════════════════════════════════════════════ */
    async function run(type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }
        const btnPdf = document.getElementById('nssExportPdfBtn');
        const btnImg = document.getElementById('nssExportImgBtn');
        _btnState([btnPdf, btnImg], true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);
        try {
            const canvas = await _capture();
            const stamp  = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            if (type === 'image') {
                const a    = document.createElement('a');
                a.download = `net_sentiment_score_${NSS_PID}_${stamp}.png`;
                a.href     = canvas.toDataURL('image/png', 1);
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf  = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pW   = pdf.internal.pageSize.getWidth();
                const pH   = pdf.internal.pageSize.getHeight();
                const pages = _canvasToPdf(pdf, canvas, 10, pW, pH, 'Net Sentiment Score');
                pdf.save(`net_sentiment_score_${NSS_PID}_${stamp}.pdf`);
                _toast(`PDF ${pages} halaman berhasil diunduh!`, 'success');
            }
        } catch (err) {
            console.error('[NSSExport.run]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState([btnPdf, btnImg], false);
        }
    }

    return { run, runCard };
})();

/* ══ Boot ══ */
document.addEventListener('DOMContentLoaded',()=>{
    const needle=$('nssNeedle');
    if(needle) needle.setAttribute('transform','rotate(0,250,260)');
    loadNSS();
});
</script>
@endsection