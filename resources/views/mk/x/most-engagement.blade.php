@extends('mk.layouts.app')

@section('title', 'X Most Engagement - SMADIMENT')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════
   DESIGN TOKENS — mirrored from TikTok Most Engagement
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
@keyframes xDotPulse {
    0%,100% { box-shadow:0 0 0 3px var(--c,rgba(3,128,71,.3)); }
    50%      { box-shadow:0 0 0 6px transparent; }
}
@keyframes fadeUp   { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer  { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin     { to{transform:rotate(360deg)} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1}    to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn  { from{opacity:0} to{opacity:1} }
@keyframes overlayOut { from{opacity:1} to{opacity:0} }
@keyframes kpiShimmer { 0%{left:-100%} 100%{left:150%} }
@keyframes kpiIconBounce {
    0%,100% { transform:scale(1) rotate(0deg); }
    30%     { transform:scale(1.25) rotate(-10deg); }
    60%     { transform:scale(1.1) rotate(6deg); }
}

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

/* ══ Tabs ══ */
.xmv-tabs {
    display:flex; gap:2px;
    background:var(--slate-100); border:1px solid var(--slate-200);
    border-radius:var(--radius-sm); padding:2px; margin-bottom:16px;
}
.xmv-tab-btn {
    flex:1; display:flex; align-items:center; justify-content:center; gap:6px;
    padding:7px 14px; border-radius:4px; border:none; background:transparent;
    font-size:12px; font-weight:600; color:var(--slate-500);
    cursor:pointer; transition:background .13s, color .13s; white-space:nowrap;
}
.xmv-tab-btn:hover { background:#fff; color:var(--slate-800); }
.xmv-tab-btn.active {
    background:#fff; color:var(--primary);
    box-shadow:0 1px 4px rgba(0,0,0,.08);
}
.xmv-tab-chip {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:20px; height:16px; padding:0 5px;
    border-radius:3px; font-size:9px; font-weight:800;
    background:var(--primary-lt); color:var(--primary);
}
.xmv-tab-btn:not(.active) .xmv-tab-chip { background:var(--slate-100); color:var(--slate-400); }
.xmv-tab-panel { display:none; }
.xmv-tab-panel.active { display:block; }

/* ══ Toggle group ══ */
.xmv-toggle-group {
    display:flex; background:var(--slate-50); border-radius:var(--radius-sm);
    padding:2px; gap:2px; border:1px solid var(--slate-200);
}
.xmv-toggle-btn {
    display:flex; align-items:center; gap:4px;
    padding:4px 10px; border-radius:3px; border:none; background:transparent;
    font-size:11px; font-weight:600; color:var(--slate-500);
    cursor:pointer; transition:background .12s, color .12s;
}
.xmv-toggle-btn:hover  { background:#fff; color:var(--slate-800); }
.xmv-toggle-btn.active { background:#fff; color:var(--primary); box-shadow:0 1px 3px rgba(0,0,0,.07); }

/* ══ Post list ══ */
.xmv-post-list { display:flex; flex-direction:column; }
.xmv-post {
    display:flex; align-items:flex-start; gap:12px;
    padding:12px 16px; border-bottom:1px solid var(--slate-100);
    transition:background .12s; cursor:pointer;
}
.xmv-post:last-child { border-bottom:none; }
.xmv-post:hover { background:var(--slate-50); }

.xmv-post-rank {
    width:22px; height:22px; border-radius:50%;
    background:var(--slate-100); border:1px solid var(--slate-200);
    display:flex; align-items:center; justify-content:center;
    font-size:9px; font-weight:800; color:var(--slate-400);
    flex-shrink:0; margin-top:8px;
}
.xmv-post-rank--1 { background:linear-gradient(135deg,#ffd700,#F59E0B); color:#7c5900; border-color:#ffd700; }
.xmv-post-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af); color:#3d3d3d; border-color:#c0c0c0; }
.xmv-post-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820); color:#fff;    border-color:#cd7f32; }

.xmv-post-av {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    color:#fff; font-weight:700; font-size:12px;
    display:flex; align-items:center; justify-content:center;
    border:1.5px solid var(--slate-200); overflow:hidden;
}
.xmv-post-av img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

.xmv-post-body { flex:1; min-width:0; }
.xmv-post-author { font-size:12.5px; font-weight:700; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.xmv-post-handle { font-size:10px; color:var(--slate-400); margin-top:1px; }
.xmv-post-date   { font-size:10px; color:var(--slate-400); margin-bottom:4px; }
.xmv-post-text   { font-size:11.5px; color:var(--slate-500); line-height:1.55; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:6px; word-break:break-word; }
.xmv-post-stats  { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }

.xmv-metric {
    display:inline-flex; align-items:center; gap:3px;
    padding:2px 6px; border-radius:3px;
    font-size:10px; font-weight:700;
    background:var(--slate-100); color:var(--slate-500);
    white-space:nowrap;
}
.xmv-metric--primary { background:var(--primary-lt); color:var(--primary); }
.xmv-metric--cyan    { background:rgba(6,182,212,.1);  color:#164e63; }
.xmv-metric--amber   { background:rgba(245,158,11,.1); color:#92400e; }
.xmv-metric--red     { background:rgba(239,68,68,.1);  color:#991b1b; }

.xmv-sent { display:inline-flex; align-items:center; padding:2px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.3px; }
.xmv-sent--pos { background:#d1fae5; color:#065f46; }
.xmv-sent--neg { background:#fee2e2; color:#991b1b; }
.xmv-sent--neu { background:var(--slate-100); color:var(--slate-500); }

.xmv-view-link {
    display:inline-flex; align-items:center; gap:3px;
    font-size:9.5px; font-weight:700; color:var(--primary);
    text-decoration:none; padding:2px 6px; border-radius:3px;
    background:var(--primary-lt); border:1px solid rgba(3,128,71,.2);
    transition:background .12s, color .12s; margin-left:auto;
}
.xmv-view-link:hover { background:var(--primary); color:#fff; }

/* ══ Pagination ══ */
.xmv-pagination {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 16px; border-top:1px solid var(--slate-100); flex-wrap:wrap; gap:8px;
}
.xmv-pag-info { font-size:11px; color:var(--slate-400); font-weight:500; }
.xmv-pag-controls { display:flex; align-items:center; gap:3px; }
.xmv-pag-btn {
    min-width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;
    padding:0 6px; border-radius:var(--radius-sm); border:1px solid var(--slate-200);
    background:#fff; font-size:11px; font-weight:600; color:var(--slate-500);
    cursor:pointer; transition:all .12s; user-select:none;
}
.xmv-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary); color:var(--primary); background:var(--primary-lt); }
.xmv-pag-btn.is-active { background:var(--primary); border-color:var(--primary); color:#fff; }
.xmv-pag-btn:disabled { opacity:.35; cursor:not-allowed; }

/* ══ Chart container ══ */
.chart-container { height:280px; position:relative; }
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

/* ══ Donut legend ══ */
.donut-legend { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-top:10px; }
.donut-leg-item {
    display:flex; align-items:center; gap:5px;
    font-size:11px; font-weight:600; color:var(--slate-500);
    padding:3px 8px; background:var(--slate-50);
    border-radius:3px; border:1px solid var(--slate-200);
    cursor:pointer; transition:border-color .12s, background .12s, color .12s;
}
.donut-leg-item:hover { border-color:var(--primary); background:var(--primary-lt); color:var(--primary); }
.donut-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

/* ══ Rows select ══ */
.xmv-rows-sel {
    padding:4px 9px;
    border:1px solid var(--slate-200); border-radius:var(--radius-sm);
    font-size:11px; font-weight:600; color:var(--slate-600);
    background:var(--slate-50); outline:none; cursor:pointer;
    transition:border-color .14s;
}
.xmv-rows-sel:focus { border-color:var(--primary); }

/* ══ Slide Panel ══ */
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

/* Detail sub-panel */
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
    color:var(--slate-500); transition:all .13s; font-size:14px;
}
.do-dp2-back:hover { background:var(--primary-lt); color:var(--primary); border-color:var(--primary); }
.do-dp2-title  { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-dp2-body   { overflow-y:auto; flex:1; padding:16px; }
.do-dp2-body::-webkit-scrollbar { width:4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

.do-dp2-avatar-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.do-dp2-avatar-lg  {
    width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700;
    font-size:16px; display:flex; align-items:center; justify-content:center;
    border:2px solid var(--slate-200); overflow:hidden; flex-shrink:0;
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
.do-dp2-content    { font-size:12px; color:var(--slate-700); line-height:1.7; margin-bottom:12px; background:var(--slate-50); border-radius:var(--radius-sm); padding:10px 12px; border:1px solid var(--slate-200); word-break:break-word; white-space:pre-wrap; }
.do-dp2-stats      { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
.do-dp2-stat       { background:var(--slate-50); border-radius:var(--radius-sm); padding:8px 10px; border:1px solid var(--slate-200); text-align:center; }
.do-dp2-stat-val   { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-stat-val.views    { color:var(--primary); }
.do-dp2-stat-val.retweets { color:var(--cyan); }
.do-dp2-stat-lbl   { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
.do-dp2-link {
    display:flex; align-items:center; justify-content:center; gap:6px;
    padding:9px 14px; background:var(--primary); color:#fff;
    border-radius:var(--radius-sm); font-size:12px; font-weight:700;
    text-decoration:none; transition:filter .14s; margin-top:4px;
}
.do-dp2-link:hover { filter:brightness(1.1); color:#fff; }

/* ══ KPI Card Hover ══ */
.kpi-card-hover {
    will-change: transform, box-shadow;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1) !important,
                box-shadow .25s ease !important,
                filter .25s ease !important;
    cursor: default; position: relative !important; overflow: hidden !important;
}
.kpi-card-hover::before {
    content: ''; position: absolute; top: 0; bottom: 0; left: -100%;
    width: 60%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.22), transparent);
    pointer-events: none; z-index: 1; transition: none;
}
.kpi-card-hover:hover {
    transform: translateY(-6px) scale(1.025) !important;
    box-shadow: 0 20px 40px rgba(0,0,0,.25) !important;
    filter: brightness(1.07) !important;
}
.kpi-card-hover:hover::before { animation: kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background: rgba(255,255,255,.35) !important; transition: background .2s ease !important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation: kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important; display: inline-block !important; }
.kpi-card-hover:active { transform: translateY(-2px) scale(1.01) !important; transition-duration: .08s !important; }

/* ══ Alert ══ */
.alert-warning-custom {
    padding:14px 18px; border-radius:var(--radius);
    background:#fef3c7; color:#92400e; border:1px solid #fcd34d;
    font-size:13px; font-weight:500; display:flex; align-items:center; gap:10px;
    margin-bottom:20px;
}

/* ══════════════════════════════════════════════════════
   EXPORT STYLES
══════════════════════════════════════════════════════ */
@keyframes spin { to{transform:rotate(360deg)} }

.page-export-bar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px;
    background:#fff; border:1px solid var(--slate-200);
    border-radius:var(--radius); padding:9px 14px;
    margin-bottom:20px; box-shadow:var(--shadow-sm);
}
.page-export-bar-left {
    display:flex; align-items:center; gap:8px;
    font-size:12px; font-weight:700; color:var(--slate-600);
}
.page-export-bar-left i { font-size:15px; color:var(--primary); }
.page-export-bar-right  { display:flex; gap:8px; }

.page-export-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:32px; height:32px; border-radius:var(--radius-sm);
    font-size:16px; cursor:pointer;
    transition:all .15s ease; border:1.5px solid transparent; font-family:inherit;
}
.page-export-btn-pdf { background:#fff3f3; color:#dc2626; border-color:#fca5a5; }
.page-export-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.page-export-btn-img { background:var(--primary-lt); color:var(--primary); border-color:rgba(3,128,71,.3); }
.page-export-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.page-export-btn:disabled { opacity:.55; cursor:not-allowed; pointer-events:none; }
.page-export-btn .export-spinner { width:13px; height:13px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
.page-export-btn.exporting .export-spinner { display:inline-block; }
.page-export-btn.exporting .export-icon    { display:none; }

.card-exp-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:var(--radius-sm);
    font-size:14px; cursor:pointer; flex-shrink:0;
    transition:all .14s ease; border:1px solid transparent; font-family:inherit; background:transparent;
}
.card-exp-btn-pdf { color:#dc2626; border-color:#fca5a5; background:#fff3f3; }
.card-exp-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.card-exp-btn-img { color:var(--primary); border-color:rgba(3,128,71,.3); background:var(--primary-lt); }
.card-exp-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
.card-exp-btn .export-spinner { width:11px; height:11px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
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

@media(max-width:640px) {
    .do-panel { width:100vw; }
    .xmv-tabs { flex-wrap:wrap; }
    .xmv-tab-btn { flex:unset; min-width:calc(50% - 4px); }
}
</style>
@endsection

@section('page-title', 'X Most Engagement Posts')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate   = $endDate   ?? request()->get('end_date',   now()->format('Y-m-d'));
    $projects  = $projects  ?? [];
@endphp

<script>
    const XMV_PID = {{ $projectId ? (int)$projectId : 'null' }};
    const XMV_SD  = '{{ $startDate }}';
    const XMV_ED  = '{{ $endDate }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
</script>

{{-- Filter --}}
@include('mk.layouts.partials.filter-datepicker')

@if(!$projectId)
<div class="alert-warning-custom">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <span>No project selected. Please select a project from the sidebar to view most viewed posts.</span>
</div>
@else

{{-- ════ PAGE EXPORT WRAPPER ════ --}}
<div id="pageExportArea">

{{-- ══ KPI Cards ══ --}}
<div class="row mb-3">
    @php
        $kpiCards = [
            ['id' => 'kpiPosts',   'label' => 'Total Posts',      'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>', 'bg' => '#EF4444', 'subId' => 'kpiPostsSub'],
            ['id' => 'kpiViews',   'label' => 'Total Views',      'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>', 'bg' => '#1D9BF0', 'subId' => 'kpiViewsSub'],
            ['id' => 'kpiRetweets','label' => 'Total Retweets',   'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>', 'bg' => '#10B981', 'subId' => 'kpiRetweetsSub'],
            ['id' => 'kpiAvg',     'label' => 'Avg. Views / Post', 'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', 'bg' => '#273B4A', 'subId' => 'kpiAvgSub'],
        ];
        $delays = ['.00s','.05s','.10s','.15s'];
    @endphp
    @foreach($kpiCards as $ki => $kc)
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 text-white kpi-card-hover"
                 style="background:{{ $kc['bg'] }};animation:fadeUp .38s ease-out {{ $delays[$ki] }} both;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">{{ $kc['label'] }}</p>
                            <h3 class="mb-0 text-white f-w-300" id="{{ $kc['id'] }}">
                                <span class="sk-block" style="height:28px;width:90px;border-radius:4px;background:rgba(255,255,255,.2);"></span>
                            </h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="{{ $kc['subId'] }}">
                                {!! str_replace('width="24" height="24"', 'width="12" height="12" style="vertical-align:text-bottom;margin-right:4px;"', $kc['icon']) !!}—
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg">{!! $kc['icon'] !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ══ Page Export Toolbar ══ --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
        <i class="ph ph-export"></i>
        <span>Export Halaman</span>
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">
            KPI + Charts + Post List
        </span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                onclick="XMVExport.run('pdf',this)" title="Export halaman sebagai PDF">
            <i class="ph ph-file-pdf export-icon"></i>
            <span class="export-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                onclick="XMVExport.run('image',this)" title="Export halaman sebagai PNG">
            <i class="ph ph-image export-icon"></i>
            <span class="export-spinner"></span>
        </button>
    </div>
</div>

{{-- ══ Donut Distribution Card ══ --}}
<div class="row">
    <div class="col-12">
        <div class="card" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-export-donut">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded">
                        <i class="ph ph-chart-donut f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0" id="donutTitle">Distribusi Views — Top 5</h6>
                        <small class="text-muted">Proporsi views per post teratas</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div id="donutLegend" class="donut-legend"></div>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf"
                                onclick="XMVExport.runCard('card-export-donut','donut','pdf',this)" title="Export PDF">
                            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                        </button>
                        <button class="card-exp-btn card-exp-btn-img"
                                onclick="XMVExport.runCard('card-export-donut','donut','image',this)" title="Export PNG">
                            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:480px;" id="donutWrap">
                    <div class="chart-loading" id="donutLoading">
                        <div class="spin-ring"></div>
                        <span>Loading chart…</span>
                    </div>
                    <div id="donutChart" style="width:100%;height:480px;display:none;"></div>
                    <div id="donutEmpty" style="display:none;" class="chart-empty">
                        <i class="ph ph-chart-donut"></i><span>No data</span>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ TABS ══ --}}
<div class="xmv-tabs">
    <button class="xmv-tab-btn active" id="tab-view"      onclick="XMVTab.show('view')">
        <i class="ph ph-eye"></i> Most Viewed <span class="xmv-tab-chip" id="chip-view">—</span>
    </button>
    <button class="xmv-tab-btn" id="tab-retweet"   onclick="XMVTab.show('retweet')">
        <i class="ph ph-repeat"></i> Most Retweeted <span class="xmv-tab-chip" id="chip-retweet">—</span>
    </button>
    <button class="xmv-tab-btn" id="tab-follower"  onclick="XMVTab.show('follower')">
        <i class="ph ph-users"></i> Top Followers <span class="xmv-tab-chip" id="chip-follower">—</span>
    </button>
    <button class="xmv-tab-btn" id="tab-sentiment" onclick="XMVTab.show('sentiment')">
        <i class="ph ph-smiley"></i> Sentiment <span class="xmv-tab-chip" id="chip-sentiment">—</span>
    </button>
</div>

{{-- ══ TAB PANELS ══ --}}
@php
$panelCfg = [
    'view'      => ['label' => 'Most Viewed',    'icon' => 'ph-eye',      'metric' => 'Views'],
    'retweet'   => ['label' => 'Most Retweeted', 'icon' => 'ph-repeat',   'metric' => 'Retweets'],
    'follower'  => ['label' => 'Top Followers',  'icon' => 'ph-users',    'metric' => 'Followers'],
    'sentiment' => ['label' => 'Sentiment',      'icon' => 'ph-smiley',   'metric' => 'Posts'],
];
@endphp

@foreach($panelCfg as $tp => $cfg)
<div class="xmv-tab-panel {{ $tp === 'view' ? 'active' : '' }}" id="panel-{{ $tp }}">

    {{-- Post List Card --}}
    <div class="card mb-3" style="animation:fadeUp .38s ease-out .2s both;">
        <div id="card-export-list-{{ $tp }}">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded">
                    <i class="ph {{ $cfg['icon'] }} f-18" style="color:var(--primary);"></i>
                </div>
                <div>
                    <h6 class="mb-0">Top Posts — {{ $cfg['label'] }}</h6>
                    <small class="text-muted">Klik post untuk lihat detail</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select class="xmv-rows-sel" id="rows-{{ $tp }}" onchange="XMVData.reloadTab('{{ $tp }}')">
                    <option value="10">Top 10</option>
                    <option value="20">Top 20</option>
                    <option value="50">Top 50</option>
                    <option value="100" selected>Top 100</option>
                </select>
                <span class="badge bg-light-primary text-primary" id="badge-{{ $tp }}">Loading…</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf"
                            onclick="XMVExport.runCard('card-export-list-{{ $tp }}','list-{{ $tp }}','pdf',this)" title="Export PDF">
                        <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                    </button>
                    <button class="card-exp-btn card-exp-btn-img"
                            onclick="XMVExport.runCard('card-export-list-{{ $tp }}','list-{{ $tp }}','image',this)" title="Export PNG">
                        <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                    </button>
                </div>
            </div>
        </div>
        <div id="list-{{ $tp }}" class="p-0"><div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div></div>
        <div id="pag-{{ $tp }}"></div>
        </div>
    </div>

    {{-- Bar Chart Card --}}
    <div class="card mb-3" style="animation:fadeUp .38s ease-out .25s both;">
        <div id="card-export-bar-{{ $tp }}">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded">
                    <i class="ph ph-chart-bar f-18 text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0">{{ $cfg['label'] }} Chart</h6>
                    <small class="text-muted">Top 10 posts</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light-secondary text-muted">Top 10</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf"
                            onclick="XMVExport.runCard('card-export-bar-{{ $tp }}','bar-{{ $tp }}','pdf',this)" title="Export PDF">
                        <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                    </button>
                    <button class="card-exp-btn card-exp-btn-img"
                            onclick="XMVExport.runCard('card-export-bar-{{ $tp }}','bar-{{ $tp }}','image',this)" title="Export PNG">
                        <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container" id="wrap-bar-{{ $tp }}">
                <div class="chart-loading" id="loading-bar-{{ $tp }}">
                    <div class="spin-ring"></div><span>Loading…</span>
                </div>
                <div id="bar-{{ $tp }}" style="width:100%;height:280px;display:none;"></div>
            </div>
        </div>
        </div>
    </div>

    {{-- Engagement comparison only on view tab --}}
    @if($tp === 'view')
    <div class="card mb-3" style="animation:fadeUp .38s ease-out .30s both;">
        <div id="card-export-eng">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-secondary rounded">
                    <i class="ph ph-chart-bar-horizontal f-18 text-muted"></i>
                </div>
                <div>
                    <h6 class="mb-0">View vs Retweet vs Followers — Top 10</h6>
                    <small class="text-muted">Perbandingan engagement keseluruhan</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="xmv-toggle-group" id="engTypeToggle">
                    <button class="xmv-toggle-btn active" data-type="stacked" onclick="XMVChart.setEngType('stacked')">Stacked</button>
                    <button class="xmv-toggle-btn" data-type="grouped"  onclick="XMVChart.setEngType('grouped')">Grouped</button>
                </div>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf"
                            onclick="XMVExport.runCard('card-export-eng','eng','pdf',this)" title="Export PDF">
                        <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                    </button>
                    <button class="card-exp-btn card-exp-btn-img"
                            onclick="XMVExport.runCard('card-export-eng','eng','image',this)" title="Export PNG">
                        <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container" id="wrap-eng">
                <div class="chart-loading" id="loading-eng">
                    <div class="spin-ring"></div><span>Loading…</span>
                </div>
                <div id="bar-eng" style="width:100%;height:280px;display:none;"></div>
            </div>
        </div>
        </div>
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
<div class="do-panel-overlay" id="xmvPanelOverlay" onclick="XMVPanel.close()"></div>
<div class="do-panel" id="xmvSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="xmvPanelDot" style="background:var(--primary);"></div>
        <span class="do-panel-title" id="xmvPanelTitle">X Posts</span>
        <button class="do-panel-close" onclick="XMVPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta">
            <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
            <span id="xmvPanelMeta">—</span>
        </div>
    </div>
    <div class="do-panel-list" id="xmvPanelList"></div>

    <div class="do-detail-panel" id="xmvDetailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="XMVDetail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="xmvDetailTitle">Detail</span>
            <button class="do-panel-close" onclick="XMVPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="xmvDetailBody"></div>
    </div>
</div>

@endif
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

/* ══ CONFIG ══ */
const XMVCfg = {
    pid : XMV_PID,
    sd  : XMV_SD,
    ed  : XMV_ED,
    primary : '#038047',
    colors  : { view:'#038047', retweet:'#06B6D4', follower:'#F59E0B', sentiment:'#8b5cf6' },
    perPage : 10,
};
const DONUT_COLORS = ['#038047','#273B4A','#F59E0B','#06B6D4','#EF4444'];

/* ══ UTILS ══ */
const _$   = id => document.getElementById(id);
const numF = n  => parseInt(n||0).toLocaleString('id-ID');
const numK = n  => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc  = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const hideLd = id => { const e=_$(id); if(e&&e.classList.contains('chart-loading')) e.classList.add('hidden'); };

/* ══ STORE & PAGINATION ══ */
const Store = { view:[], retweet:[], follower:[], sentiment:[] };
const Pag   = { view:1, retweet:1, follower:1, sentiment:1 };

/* ══ ApexCharts instances ══ */
const Charts = {};
function makeChart(id, opts) {
    if(Charts[id]) { try{ Charts[id].destroy(); }catch(e){} }
    const el = _$(id); if(!el) return null;
    Charts[id] = new ApexCharts(el, opts);
    Charts[id].render();
    el.style.display = 'block';
    return Charts[id];
}
window.addEventListener('resize', () => Object.values(Charts).forEach(c=>{ try{ c.updateOptions({}); }catch(e){} }));

/* ══ TABS ══ */
const XMVTab = {
    _loaded:{ view:false, retweet:false, follower:false, sentiment:false },
    show(type) {
        ['view','retweet','follower','sentiment'].forEach(t => {
            _$('tab-'+t)?.classList.toggle('active', t===type);
            _$('panel-'+t)?.classList.toggle('active', t===type);
        });
        const lblMap = { view:'Distribusi Views — Top 5', retweet:'Distribusi Retweets — Top 5', follower:'Distribusi Followers — Top 5', sentiment:'Distribusi Sentiment' };
        const dtitle = _$('donutTitle'); if(dtitle) dtitle.textContent = lblMap[type] || '';
        if(!this._loaded[type]) { this._loaded[type]=true; XMVData.loadTab(type); }
        else { if(Store[type].length) XMVData._renderDonut(type, Store[type]); }
    },
    reset() { this._loaded = {view:false,retweet:false,follower:false,sentiment:false}; }
};

/* ══ DATA ══ */
const XMVData = {
    async loadAll() {
        if(!XMVCfg.pid) {
            ['kpiPosts','kpiViews','kpiRetweets','kpiAvg'].forEach(id => { const e=_$(id); if(e) e.textContent='—'; });
            ['list-view','list-retweet','list-follower','list-sentiment'].forEach(id => { const e=_$(id); if(e) e.innerHTML=this._emptyHtml('Pilih project terlebih dahulu'); });
            return;
        }
        XMVTab._loaded.view = true;
        await this.loadTab('view');
    },

    async loadTab(type) {
        const rows   = parseInt(_$('rows-'+type)?.value || '100');
        const listEl = _$('list-'+type);
        if(listEl) listEl.innerHTML = `<div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div>`;
        try {
            const res  = await fetch(`/mk/api/x/most-status?project_id=${XMVCfg.pid}&start_date=${XMVCfg.sd}&end_date=${XMVCfg.ed}`);
            const json = await res.json();
            let items  = json.data || json || []; if(!Array.isArray(items)) items=[];

            /* Sort by tab type */
            const sorted = this._sortByType(items, type).slice(0, rows);
            Store[type]  = sorted; Pag[type] = 1;

            const chip  = _$('chip-'+type);  if(chip)  chip.textContent  = sorted.length;
            const badge = _$('badge-'+type); if(badge) badge.textContent = `${sorted.length} posts`;

            if(type === 'view') { this._updateKPIs(items); this._renderEngChart(sorted); }
            this._renderList(type);
            this._renderBar(type, sorted.slice(0,10));
            this._renderDonut(type, sorted);
        } catch(err) {
            console.error('[XMV]', err);
            if(listEl) listEl.innerHTML = this._emptyHtml('Gagal memuat: ' + err.message);
            const chip = _$('chip-'+type); if(chip) chip.textContent = '!';
            hideLd('loading-bar-'+type);
        }
    },

    reloadTab(type) { Store[type]=[]; Pag[type]=1; this.loadTab(type); },

    _sortByType(items, type) {
        const copy = [...items];
        switch(type) {
            case 'view':      return copy.sort((a,b) => (b.view_cnt||0)-(a.view_cnt||0));
            case 'retweet':   return copy.sort((a,b) => (b.rt||0)-(a.rt||0));
            case 'follower':  return copy.sort((a,b) => (b.author?.flw_cnt||0)-(a.author?.flw_cnt||0));
            case 'sentiment': return copy.sort((a,b) => (b.sentiment_prec||0)-(a.sentiment_prec||0));
            default:          return copy;
        }
    },

    _metric(item, type) {
        switch(type) {
            case 'view':      return parseInt(item.view_cnt||0);
            case 'retweet':   return parseInt(item.rt||0);
            case 'follower':  return parseInt(item.author?.flw_cnt||0);
            case 'sentiment': return parseFloat(item.sentiment_prec||0);
            default:          return parseInt(item.view_cnt||0);
        }
    },

    _updateKPIs(items) {
        const n = items.length;
        const totalViews    = items.reduce((s,i)=>s+parseInt(i.view_cnt||0),0);
        const totalRetweets = items.reduce((s,i)=>s+parseInt(i.rt||0),0);
        const avgViews      = n ? Math.round(totalViews/n) : 0;

        const el  = (id,val) => { const e=_$(id); if(e) e.textContent=numF(val); };
        const sub = (id,txt) => { const e=_$(id); if(e) e.innerHTML=`<i class="ph ph-chart-line-up me-1"></i>${txt}`; };

        el('kpiPosts',    n);
        el('kpiViews',    totalViews);
        el('kpiRetweets', totalRetweets);
        el('kpiAvg',      avgViews);

        sub('kpiPostsSub',    `${n} posts dalam periode ini`);
        sub('kpiViewsSub',    `Avg ${numF(avgViews)} views / post`);
        sub('kpiRetweetsSub', `Total retweet keseluruhan`);
        sub('kpiAvgSub',      `Berdasarkan ${n} posts`);
    },

    _getName(item) {
        return (item.author?.name || item.name || '').trim() || 'Unknown User';
    },
    _getHandle(item) {
        return item.author?.scr_name || item.name || 'unknown';
    },
    _getAvatar(item) {
        const av = item.avatar_url || '';
        if(av && !av.startsWith('/external') && av !== '/images/default-avatar.png') return av;
        return '';
    },
    _getInitials(name) {
        if(!name||name==='Unknown User') return '?';
        const p=name.trim().split(/\s+/);
        if(p.length>=2) return (p[0][0]+p[p.length-1][0]).toUpperCase();
        return p[0].substring(0,2).toUpperCase();
    },
    _getColor(item) {
        const seed = item.author?.scr_name || item.name || 'x';
        const palette=['#038047','#273B4A','#F59E0B','#06B6D4','#8b5cf6','#ec4899','#f97316','#14b8a6'];
        let h=0; for(let i=0;i<seed.length;i++) h=(h*31+seed.charCodeAt(i))&0xffffffff;
        return palette[Math.abs(h)%palette.length];
    },
    _avHtml(item) {
        const av   = this._getAvatar(item);
        const name = this._getName(item);
        const handle = this._getHandle(item).replace('@','');
        const dummy = `https://unavatar.io/twitter/${handle}`;
        if(av && av.startsWith('http')) {
            return `<img src="${esc(av)}" onerror="this.src='${dummy}'">`;
        }
        return `<img src="${dummy}" onerror="this.parentElement.textContent='${this._getInitials(name)}'">`;
    },
    _normSent(item) {
        const r = String(item.sentiment_str||'').toLowerCase();
        return r.includes('pos')?'pos':r.includes('neg')?'neg':'neu';
    },
    _emptyHtml(msg) {
        return `<div class="chart-empty" style="padding:40px 20px;"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2" style="opacity:.5;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg><span>${esc(msg)}</span></div>`;
    },
    _formatDate(ds) {
        if(!ds) return '';
        try{ return new Date(ds).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}); }
        catch(e){ return ds.split('T')[0]; }
    },

    _renderList(type) {
        const items=Store[type], listEl=_$('list-'+type), pagEl=_$('pag-'+type);
        if(!listEl) return;
        if(!items.length) { listEl.innerHTML=this._emptyHtml('Tidak ada data untuk periode ini'); if(pagEl) pagEl.innerHTML=''; return; }
        const page=Pag[type]||1, total=items.length, pp=XMVCfg.perPage;
        const pages=Math.ceil(total/pp), start=(page-1)*pp;
        listEl.innerHTML = `<div class="xmv-post-list">${items.slice(start,start+pp).map((item,i)=>this._postHtml(item,start+i,type)).join('')}</div>`;
        if(pagEl) pagEl.innerHTML = this._pagHtml(type,page,pages,total,start+1,Math.min(start+pp,total));
        listEl.querySelectorAll('.xmv-post').forEach(el => {
            el.addEventListener('click', () => {
                try {
                    const item = JSON.parse(decodeURIComponent(el.dataset.item));
                    const lm = {view:'Most Viewed',retweet:'Most Retweeted',follower:'Top Followers',sentiment:'Sentiment'};
                    XMVPanel.open(items, type, 'X (Twitter) — '+lm[type]);
                    XMVDetail.open(item, type);
                } catch(e){ console.warn(e); }
            });
        });
    },

    _pagHtml(type,page,pages,total,from,to) {
        if(pages<=1) return '';
        let btns='', r=2;
        btns += `<button class="xmv-pag-btn" ${page<=1?'disabled':''} onclick="XMVData.goPage('${type}',${page-1})"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg></button>`;
        for(let i=1;i<=pages;i++){
            if(i===1||i===pages||(i>=page-r&&i<=page+r)) btns+=`<button class="xmv-pag-btn${i===page?' is-active':''}" onclick="XMVData.goPage('${type}',${i})">${i}</button>`;
            else if(i===page-r-1||i===page+r+1) btns+=`<span class="xmv-pag-btn" style="cursor:default;opacity:.4;">…</span>`;
        }
        btns += `<button class="xmv-pag-btn" ${page>=pages?'disabled':''} onclick="XMVData.goPage('${type}',${page+1})"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button>`;
        return `<div class="xmv-pagination"><span class="xmv-pag-info">Menampilkan ${from}–${to} dari ${total} posts</span><div class="xmv-pag-controls">${btns}</div></div>`;
    },

    goPage(type,page) {
        Pag[type]=page; this._renderList(type);
        const el=_$('list-'+type); if(el) el.scrollIntoView({behavior:'smooth',block:'nearest'});
    },

    _postHtml(item,gi,type) {
        const rank=gi+1, rkCls=rank<=3?'--'+rank:'';
        const name=this._getName(item), handle=this._getHandle(item), color=this._getColor(item);
        const avHtml=this._avHtml(item), sent=this._normSent(item);
        const content=(item.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim().slice(0,200);
        const dt=this._formatDate(item.date_created);
        const url=item.sub_id?`https://twitter.com/i/web/status/${item.sub_id}`:'';
        const v=parseInt(item.view_cnt||0), rt=parseInt(item.rt||0), flw=parseInt(item.author?.flw_cnt||0);
        const total=v+rt;
        const sentLbl={pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];
        const enc=encodeURIComponent(JSON.stringify(item));

        const vCls  = type==='view'     ? ' xmv-metric--primary' : '';
        const rtCls = type==='retweet'  ? ' xmv-metric--primary' : ' xmv-metric--cyan';
        const fCls  = type==='follower' ? ' xmv-metric--amber'   : '';

        return `<div class="xmv-post" data-item="${esc(enc)}">
            <div class="xmv-post-rank xmv-post-rank${rkCls}">${rank}</div>
            <div class="xmv-post-av" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
            <div class="xmv-post-body">
                <div class="xmv-post-author">${esc(name)}</div>
                <div class="xmv-post-handle">@${esc(handle)}</div>
                ${dt?`<div class="xmv-post-date">${dt}</div>`:''}
                ${content?`<div class="xmv-post-text">${esc(content)}</div>`:''}
                <div class="xmv-post-stats">
                    <span class="xmv-metric${vCls}"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>${numF(v)}</span>
                    <span class="xmv-metric${rtCls}"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>${numF(rt)}</span>
                    <span class="xmv-metric${fCls}"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>${numK(flw)}</span>
                    <span class="xmv-sent xmv-sent--${sent}">${sentLbl}</span>
                    ${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="xmv-view-link" onclick="event.stopPropagation()"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>View</a>`:''}
                </div>
            </div>
        </div>`;
    },

    _renderBar(type, items) {
        const barEl=_$('bar-'+type), loadEl=_$('loading-bar-'+type);
        if(!barEl||!items.length||typeof ApexCharts==='undefined') { hideLd('loading-bar-'+type); return; }
        const color  = XMVCfg.colors[type];
        const labels = items.map(it=>{ const n=this._getName(it); return n.length>14?n.slice(0,13)+'…':n; });
        const values = items.map(it=>this._metric(it,type));
        const metLbl = {view:'Views',retweet:'Retweets',follower:'Followers',sentiment:'Sentiment%'}[type];
        const opts = {
            chart: { type:'bar', height:280, fontFamily:'inherit', background:'transparent', toolbar:{show:false}, zoom:{enabled:false},
                events: { mounted:()=>hideLd('loading-bar-'+type), click:(_,ctx,cfg)=>{ const item=items[cfg.dataPointIndex]; if(item){ XMVPanel.open(Store[type],type,'X (Twitter)'); XMVDetail.open(item,type); } } }
            },
            series: [{ name:metLbl, data:values }],
            colors: [color],
            plotOptions: { bar:{ borderRadius:5, columnWidth:'58%', dataLabels:{position:'top'} } },
            dataLabels: { enabled:true, formatter:v=>numK(v), offsetY:-16, style:{ fontSize:'10px', fontWeight:'800', colors:[color] }, background:{enabled:false} },
            xaxis: { categories:labels, axisBorder:{show:false}, axisTicks:{show:false}, labels:{ style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'}, rotate:labels.length>6?-28:0, hideOverlappingLabels:true } },
            yaxis: { labels:{ formatter:v=>numK(v), style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'} }, axisBorder:{show:false}, axisTicks:{show:false} },
            grid: { borderColor:'rgba(226,232,240,.55)', strokeDashArray:3, xaxis:{lines:{show:false}}, padding:{top:20,right:8,bottom:0,left:4} },
            fill: { type:'gradient', gradient:{ type:'vertical', shadeIntensity:0.2, opacityFrom:1, opacityTo:.7, stops:[0,100] } },
            tooltip: { shared:false, intersect:true, style:{fontFamily:'inherit',fontSize:'12px'}, y:{formatter:v=>numF(v)} },
        };
        barEl.style.display = 'block';
        makeChart('bar-'+type, opts);
    },

    _renderDonut(type, items) {
        const loadEl=_$('donutLoading'), chartEl=_$('donutChart'), emptyEl=_$('donutEmpty');
        if(!loadEl||!chartEl) return;
        if(!items.length) { loadEl.style.display='none'; if(emptyEl) emptyEl.style.display='flex'; return; }
        const top5   = items.slice(0,5);
        const metLbl = {view:'Views',retweet:'Retweets',follower:'Followers',sentiment:'Post'}[type];
        const total  = top5.reduce((s,it)=>s+this._metric(it,type),0);

        const legEl = _$('donutLegend');
        if(legEl) legEl.innerHTML = top5.map((it,i)=>{
            const n=this._getName(it), sn=n.length>22?n.slice(0,21)+'…':n;
            return `<div class="donut-leg-item"><span class="donut-dot" style="background:${DONUT_COLORS[i]};"></span>${sn} · ${numF(this._metric(it,type))}</div>`;
        }).join('');

        loadEl.style.display  = 'none';
        chartEl.style.display = 'block';
        if(emptyEl) emptyEl.style.display = 'none';

        if(window.__xmvDonutChart) { try{ window.__xmvDonutChart.dispose(); }catch(e){} }
        if(typeof echarts === 'undefined') { chartEl.innerHTML='<div class="chart-empty"><i class="ph ph-chart-donut"></i><span>ECharts not loaded</span></div>'; return; }

const chart = echarts.init(chartEl, null, {renderer:'canvas'});
      window.__xmvDonutChart = chart;
        window.addEventListener('resize', ()=>{ try{ chart.resize(); }catch(e){} });

        const pieData = top5.map((it,i)=>{
            const name  = this._getName(it);
            const val   = this._metric(it,type);
            const pct   = total>0?((val/total)*100).toFixed(1):'0';
            const content = (it.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim();
            return { name, value:val, _content:content, _pct:pct, itemStyle:{color:DONUT_COLORS[i]} };
        });

        const option = {
            backgroundColor: 'transparent',
            tooltip: { show:false },
            animation: true, animationDuration:1000, animationEasing:'cubicOut', animationDelay:idx=>idx*80,
            series: [{
                type: 'pie', radius: ['38%','62%'], center:['50%','50%'],
                avoidLabelOverlap: true, selectedMode:false, minAngle:8,
                itemStyle: { borderColor:'#fff', borderWidth:3 },
                label: {
                    show:true, position:'outside', alignTo:'edge', edgeDistance:20,
                    lineHeight:18, fontSize:11, fontFamily:'inherit',
                    color:'#334155', fontWeight:'500',
                    formatter: p => {
                        const it    = top5[p.dataIndex];
                        const content = (it.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim();
                        const words = content?content.slice(0,120).split(' '):[];
                        const lines=[]; let cur='';
                        words.forEach(w=>{ if((cur+' '+w).trim().length>42){lines.push(cur.trim());cur=w;}else{cur=(cur+' '+w).trim();} });
                        if(cur) lines.push(cur);
                        const body=lines.join('\n');
                        return `{title|${p.name}}\n${body?body+'\n':''}({val|${numF(p.value)}} ${metLbl}, {pct|${p.percent.toFixed(1)}%})`;
                    },
                    rich: { title:{fontSize:11,fontWeight:'700',color:'#1e293b',lineHeight:18}, val:{fontSize:11,fontWeight:'700',color:'#038047'}, pct:{fontSize:11,fontWeight:'600',color:'#64748b'} }
                },
                labelLine: { show:true, length:18, length2:24, smooth:0.3, lineStyle:{width:1.5,color:'#94A3B8'} },
                emphasis: { scale:false, itemStyle:{shadowBlur:0,shadowColor:'transparent',borderWidth:3,borderColor:'#fff',opacity:1}, labelLine:{lineStyle:{width:2.5,color:'#273B4A'}}, label:{show:true} },
                select: { disabled:true },
                data: pieData,
            }],
            graphic: [
                { type:'text', left:'center', top:'46%', z:100, style:{ text:numK(total), fill:'#0f172a', font:"800 28px inherit", textAlign:'center' } },
                { type:'text', left:'center', top:'54%', z:100, style:{ text:'TOTAL '+metLbl.toUpperCase(), fill:'#94a3b8', font:"600 9px inherit", textAlign:'center' } }
            ]
        };
        chart.setOption(option);
        chart.on('click', p => {
            const item=top5[p.dataIndex]; if(item){ XMVPanel.open(Store[type].length?Store[type]:top5, type, 'X (Twitter)'); XMVDetail.open(item,type); }
        });

        /* Custom tooltip */
        let _ttEl=document.getElementById('xmvDonutTT');
        if(!_ttEl){ _ttEl=document.createElement('div'); _ttEl.id='xmvDonutTT'; _ttEl.style.cssText=`position:fixed;z-index:9999;pointer-events:none;background:#1e293b;color:#fff;border:1px solid #334155;border-radius:6px;padding:10px 14px;max-width:280px;font-size:12px;line-height:1.5;display:none;box-shadow:0 8px 24px rgba(0,0,0,.32);font-family:inherit;opacity:0;transform:translateY(6px) scale(.97);transition:opacity .18s ease,transform .18s ease;`; document.body.appendChild(_ttEl); }
        let _ttTimer=null;

        chart.on('mouseover', p => {
            if(p.componentType!=='series') return;
            const it    = top5[p.dataIndex], color=DONUT_COLORS[p.dataIndex];
            const content=(it.content||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim().slice(0,160);
            clearTimeout(_ttTimer);
            _ttEl.innerHTML=`<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;"><span style="width:9px;height:9px;border-radius:50%;background:${color};flex-shrink:0;display:inline-block;"></span><b style="font-size:12.5px;">${esc(p.name)}</b></div>${content?`<div style="font-size:11px;color:#94a3b8;margin-bottom:6px;">${esc(content)}</div>`:''}<div style="display:flex;align-items:center;gap:8px;"><b style="font-size:13px;">${numF(p.value)} ${metLbl}</b><span style="color:${color};font-weight:700;">${p.percent.toFixed(1)}%</span></div>`;
            _ttEl.style.display='block';
            requestAnimationFrame(()=>{ _ttEl.style.opacity='1'; _ttEl.style.transform='translateY(0) scale(1)'; });
        });
        chart.on('mouseout', ()=>{ _ttEl.style.opacity='0'; _ttEl.style.transform='translateY(6px) scale(.97)'; _ttTimer=setTimeout(()=>{ _ttEl.style.display='none'; },180); });
        chartEl.addEventListener('mousemove', e=>{ if(_ttEl.style.display==='none') return; const vw=window.innerWidth,vh=window.innerHeight,tw=_ttEl.offsetWidth+16,th=_ttEl.offsetHeight+16; let x=e.clientX+18,y=e.clientY-10; if(x+tw>vw)x=e.clientX-tw; if(y+th>vh)y=e.clientY-th; _ttEl.style.left=x+'px'; _ttEl.style.top=y+'px'; });
    },

    _renderEngChart(items) {
        hideLd('loading-eng');
        if(!items.length) return;
        const top10=[...items].slice(0,10);
        XMVChart._items=top10; XMVChart._render(top10, XMVChart._type);
    }
};

/* ══ Engagement stacked/grouped chart ══ */
const XMVChart = {
    _type:'stacked', _items:[],
    setEngType(t) {
        this._type=t;
        document.querySelectorAll('#engTypeToggle .xmv-toggle-btn').forEach(b=>b.classList.toggle('active',b.dataset.type===t));
        if(this._items.length) this._render(this._items,t);
    },
    _render(items,stackType) {
        const barEl=_$('bar-eng'); if(!barEl) return;
        barEl.style.display='block';
        const labels=items.map(it=>{ const n=XMVData._getName(it); return n.length>14?n.slice(0,13)+'…':n; });
        const isStack=stackType==='stacked';
        const seriesData=[
            { name:'Views',     data:items.map(it=>parseInt(it.view_cnt||0)),         color:'#038047' },
            { name:'Retweets',  data:items.map(it=>parseInt(it.rt||0)),               color:'#06B6D4' },
            { name:'Followers', data:items.map(it=>parseInt(it.author?.flw_cnt||0)),  color:'#F59E0B' },
        ];
        const opts={
            chart:{ type:'bar', height:280, fontFamily:'inherit', background:'transparent', toolbar:{show:false}, zoom:{enabled:false}, stacked:isStack },
            series:seriesData.map(s=>({name:s.name,data:s.data})), colors:seriesData.map(s=>s.color),
            plotOptions:{ bar:{ borderRadius:isStack?2:4, columnWidth:isStack?'60%':'75%', dataLabels:{position:isStack?'center':'top'} } },
            dataLabels:{ enabled:isStack, formatter:v=>v>0?numK(v):'', style:{fontSize:'9px',fontWeight:'700',colors:['#fff']}, background:{enabled:false} },
            xaxis:{ categories:labels, axisBorder:{show:false}, axisTicks:{show:false}, labels:{ style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'}, rotate:labels.length>7?-28:0 } },
            yaxis:{ labels:{ formatter:v=>numK(v), style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'} }, axisBorder:{show:false}, axisTicks:{show:false} },
            grid:{ borderColor:'rgba(226,232,240,.55)', strokeDashArray:3, xaxis:{lines:{show:false}}, padding:{top:10,right:8,bottom:0,left:4} },
            legend:{ position:'bottom', horizontalAlign:'left', fontSize:'11px', fontFamily:'inherit', fontWeight:600, markers:{width:8,height:8,radius:4}, itemMargin:{horizontal:12,vertical:4}, offsetY:4 },
            tooltip:{ shared:true, intersect:false, style:{fontFamily:'inherit',fontSize:'12px'}, y:{formatter:v=>numF(v)} },
        };
        makeChart('bar-eng', opts);
    }
};

/* ══ PANEL DRAWER ══ */
const XMVPanel = {
    _items:[], _type:null,
    open(items,type,title) {
        this._items=items||[]; this._type=type;
        XMVDetail.close();
        _$('xmvPanelDot').style.background=XMVCfg.colors[type]||XMVCfg.primary;
        _$('xmvPanelTitle').textContent=title||'X Posts';
        _$('xmvPanelMeta').textContent=XMVCfg.sd+' – '+XMVCfg.ed;
        const ov=_$('xmvPanelOverlay'), pn=_$('xmvSntPanel');
        ov.classList.remove('hiding'); pn.classList.remove('hiding');
        ov.classList.add('show'); pn.classList.add('show');
        this._render(items,type);
    },
    close() {
        XMVDetail.close();
        const ov=_$('xmvPanelOverlay'), pn=_$('xmvSntPanel');
        pn.classList.add('hiding'); ov.classList.add('hiding');
        setTimeout(()=>{ pn.classList.remove('show','hiding'); ov.classList.remove('show','hiding'); },240);
    },
    _render(items,type) {
        const list=_$('xmvPanelList'); if(!list) return;
        if(!items?.length){ list.innerHTML='<div style="padding:40px;text-align:center;color:#94A3B8;font-size:12px;">Tidak ada data</div>'; return; }
        const color=XMVCfg.colors[type]||XMVCfg.primary;
        const metLbl={view:'Views',retweet:'Retweets',follower:'Followers',sentiment:'Sentiment'}[type];
        list.innerHTML=items.slice(0,100).map(item=>{
            const name=XMVData._getName(item), handle=XMVData._getHandle(item);
            const color2=XMVData._getColor(item);
            const avHtml=XMVData._avHtml(item);
            const text=(item.content||'').replace(/<[^>]*>/g,'').trim();
            const metVal=XMVData._metric(item,type);
            const dt=XMVData._formatDate(item.date_created);
            const sent=XMVData._normSent(item), sentLbl={pos:'Pos',neg:'Neg',neu:'Neu'}[sent];
            const enc=encodeURIComponent(JSON.stringify(item));
            return `<div class="do-panel-item" data-item="${esc(enc)}" data-type="${type}" onclick="XMVPanel._click(this)">
                <div class="do-panel-avatar" style="background:linear-gradient(135deg,${color2},${color2}99);">${avHtml}</div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author">${esc(name)} <span style="font-weight:400;color:var(--slate-400);">@${esc(handle)}</span></div>
                    <div class="do-panel-text">${esc(text.slice(0,130)||'(tidak ada konten)')}</div>
                    <div class="do-panel-footer">
                        <span class="do-sent-badge do-sent-badge--${sent}">${sentLbl}</span>
                        <span>${metLbl} ${numF(metVal)}</span>
                        ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
                    </div>
                </div>
            </div>`;
        }).join('');
        if(items.length>100) list.insertAdjacentHTML('beforeend',`<div style="padding:8px;text-align:center;font-size:10px;font-weight:600;color:#94A3B8;background:var(--slate-50);border-top:1px dashed var(--slate-200);">+${(items.length-100).toLocaleString()} lainnya</div>`);
    },
    _click(el) {
        try {
            const item=JSON.parse(decodeURIComponent(el.dataset.item.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"')));
            XMVDetail.open(item, el.dataset.type||this._type);
        } catch(e){ console.warn(e); }
    }
};

/* ══ DETAIL SUB-PANEL ══ */
const XMVDetail = {
    open(item, type) {
        const panel=_$('xmvDetailPanel'), body=_$('xmvDetailBody'), title=_$('xmvDetailTitle');
        if(!panel||!body) return;
        const color   = XMVCfg.colors[type]||XMVCfg.primary;
        const name    = XMVData._getName(item);
        const handle  = XMVData._getHandle(item);
        const avColor = XMVData._getColor(item);
        const avHtml  = XMVData._avHtml(item);
        const content = (item.content||'').replace(/<[^>]*>/g,'').trim();
        const url     = item.sub_id?`https://twitter.com/i/web/status/${item.sub_id}`:'';
        const dt      = item.date_created||'';
        let dtFmt=''; if(dt){ try{ dtFmt=new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); }catch(e){ dtFmt=dt.split('T')[0]; } }
        const v   = parseInt(item.view_cnt||0);
        const rt  = parseInt(item.rt||0);
        const flw = parseInt(item.author?.flw_cnt||0);
        const sent    = XMVData._normSent(item);
        const sentLbl = {pos:'Positif',neg:'Negatif',neu:'Netral'}[sent];

        title.textContent = name;
        body.innerHTML=`
            <div class="do-dp2-avatar-row">
                <div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div>
                <div>
                    <div class="do-dp2-name">${esc(name)}</div>
                    <div class="do-dp2-handle">@${esc(handle)}</div>
                    <span class="do-dp2-plat-badge" style="background:${color}18;color:${color};">X (Twitter)</span>
                </div>
            </div>
            ${dtFmt?`<div class="do-dp2-meta">${dtFmt}</div>`:''}
            <div class="do-dp2-sent do-dp2-sent--${sent}">${sentLbl}</div>
            ${content?`<div class="do-dp2-content">${esc(content)}</div>`:''}
            <div class="do-dp2-stats">
                <div class="do-dp2-stat"><div class="do-dp2-stat-val views">${numF(v)}</div><div class="do-dp2-stat-lbl">Views</div></div>
                <div class="do-dp2-stat"><div class="do-dp2-stat-val retweets">${numF(rt)}</div><div class="do-dp2-stat-lbl">Retweets</div></div>
                <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numK(flw)}</div><div class="do-dp2-stat-lbl">Followers</div></div>
            </div>
            ${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out me-1"></i>Buka di X (Twitter)</a>`:''}`;
        panel.classList.add('show');
    },
    close() { _$('xmvDetailPanel')?.classList.remove('show'); }
};

/* ══════════════════════════════════════════════════════
   EXPORT MODULE — X Most Viewed Posts v2 (sync dengan X Overview)
   Fix: ECharts pre-snapshot, _onClone, freeze, PDF all-tabs
   Note: ECharts instance disimpan di window.__xmvDonutChart
══════════════════════════════════════════════════════ */
const XMVExport = (() => {
    'use strict';
    let _toastTimer = null;

    function _toast(msg, type='default', duration=3200) {
        const t=_$('exportToast'), m=_$('exportToastMsg'), ico=_$('exportToastIcon');
        if(!t||!m) return;
        m.textContent=msg; t.className='export-toast show '+(type!=='default'?type:'');
        ico.className='ph '+({success:'ph-check-circle',error:'ph-x-circle',default:'ph-spinner'}[type]||'ph-spinner');
        clearTimeout(_toastTimer); _toastTimer=setTimeout(()=>t.classList.remove('show'),duration);
    }
    function _btnState(btn,loading){ if(!btn)return; btn.disabled=loading; btn.classList.toggle('exporting',loading); }

    function _freeze(){
        if(document.getElementById('__xmv_freeze')) return;
        const s=document.createElement('style'); s.id='__xmv_freeze';
        s.textContent='*,*::before,*::after{animation:none!important;transition:none!important;animation-play-state:paused!important;}.fade-up,.kpi-card-hover{opacity:1!important;transform:none!important;}.sk-block{animation:none!important;}';
        document.head.appendChild(s);
    }
    function _unfreeze(){ document.getElementById('__xmv_freeze')?.remove(); }

    function _resizeCharts(){
        try{ if(window.__xmvDonutChart&&!window.__xmvDonutChart.isDisposed()) window.__xmvDonutChart.resize(); }catch(e){}
        Object.values(Charts).forEach(c=>{ try{ c.updateOptions({}); }catch(e){} });
    }

    function _drawHeader(pdf,pW,pH,label,page,total){
        pdf.setFillColor(3,128,71); pdf.rect(0,0,pW,11,'F');
        pdf.setTextColor(255,255,255); pdf.setFontSize(9); pdf.setFont('helvetica','bold');
        pdf.text('SMADIMENT — '+(label||'X Most Engagement'),10,7.5);
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
        const ratio=uw/canvas.width, sliceH=uh/ratio;
        let srcY=0, pg=startPageNum, first=true;
        while(srcY<canvas.height){
            if(!first) pdf.addPage(); first=false;
            _drawHeader(pdf,pW,pH,label,pg,totalPages);
            const srcSlice=Math.min(sliceH,canvas.height-srcY), dstH=srcSlice*ratio;
            const slice=document.createElement('canvas');
            slice.width=canvas.width; slice.height=Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(canvas,0,srcY,canvas.width,srcSlice,0,0,canvas.width,srcSlice);
            pdf.addImage(slice.toDataURL('image/png'),'PNG',margin,14,uw,dstH);
            srcY+=srcSlice; pg++;
        }
        return pg;
    }

    // ── Core capture — sama persis dengan Media Statistic yang works ──
    async function _captureArea(el, isCard){
        _resizeCharts();
        await new Promise(r=>setTimeout(r,300));
        _freeze();
        await new Promise(r=>setTimeout(r,400));
        try{
            return await html2canvas(el,{
                scale:2,
                useCORS:true,
                allowTaint:false,
                backgroundColor: isCard ? '#ffffff' : '#f1f5f9',
                logging:false,
                removeContainer:true,
                windowHeight: el.scrollHeight,
                height: el.scrollHeight,
                ignoreElements: e=>e.hasAttribute('data-html2canvas-ignore'),
            });
        }finally{
            _unfreeze();
        }
    }

    // ── run(): export full page ──
    async function run(type,btn){
        if(!window.html2canvas){ _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf'&&!window.jspdf?.jsPDF){ _toast('jsPDF tidak tersedia','error'); return; }

        const btnPdf=_$('pageExportPdfBtn'), btnImg=_$('pageExportImgBtn');
        _btnState(btnPdf,true); _btnState(btnImg,true);
        _toast(type==='pdf'?'Menyiapkan PDF…':'Mengambil gambar…','default',99999);

        const TAB_KEYS=['view','retweet','follower','sentiment'];
        const originalTab=TAB_KEYS.find(t=>_$('tab-'+t)?.classList.contains('active'))||'view';
        const area=_$('pageExportArea');
        const stamp=new Date().toISOString().slice(0,10).replace(/-/g,'');

        try{
            if(type==='image'){
                const canvas=await _captureArea(area,false);
                const link=document.createElement('a');
                link.download=`x_most_engagement_${XMVCfg.pid}_${stamp}.png`;
                link.href=canvas.toDataURL('image/png'); link.click();
                _toast('Gambar berhasil diunduh!','success');
                return;
            }

            const TAB_ORDER=[
                {key:'view',      label:'Most Viewed'},
                {key:'retweet',   label:'Most Retweeted'},
                {key:'follower',  label:'Top Followers'},
                {key:'sentiment', label:'Sentiment'},
            ];

            // Load semua tab yang belum loaded
            _toast('Memuat semua tab…','default',99999);
            for(const {key} of TAB_ORDER){
                if(!XMVTab._loaded[key]){
                    XMVTab._loaded[key]=true;
                    await XMVData.loadTab(key);
                    await new Promise(r=>setTimeout(r,800));
                }
            }

            // Capture tiap tab
            const canvases=[];
            for(let i=0;i<TAB_ORDER.length;i++){
                const {key,label}=TAB_ORDER[i];
                _toast(`Mengambil tab ${i+1}/4: ${label}…`,'default',99999);

                // Aktifkan tab
                TAB_KEYS.forEach(x=>{
                    _$('tab-'+x)?.classList.toggle('active',x===key);
                    _$('panel-'+x)?.classList.toggle('active',x===key);
                });

                // Resize chart
                try{ if(window.__xmvDonutChart&&!window.__xmvDonutChart.isDisposed()) window.__xmvDonutChart.resize(); }catch(e){}

                await new Promise(r=>setTimeout(r,600));
                const canvas=await _captureArea(area,false);
                canvases.push({label:`X Most Engagement — ${label}`, canvas});
            }

            // Build PDF
            const {jsPDF}=window.jspdf;
            const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
            const pW=pdf.internal.pageSize.getWidth(), pH=pdf.internal.pageSize.getHeight(), M=10;
            const usableW=pW-M*2, usableH=pH-14-10;

            let totalPages=0;
            canvases.forEach(({canvas})=>{
                const ratio=usableW/canvas.width;
                totalPages+=Math.max(1,Math.ceil((canvas.height*ratio)/usableH));
            });

            let pageNum=1, firstPage=true;
            canvases.forEach(({label,canvas})=>{
                if(!firstPage) pdf.addPage(); firstPage=false;
                pageNum=_sliceIntoPages(pdf,canvas,M,pW,pH,label,pageNum,totalPages);
            });

            pdf.save(`x_most_engagement_${XMVCfg.pid}_${stamp}.pdf`);
            _toast(`PDF ${totalPages} halaman berhasil diunduh!`,'success');

        }catch(err){
            console.error('[XMVExport.run]',err);
            _unfreeze();
            _toast('Export gagal: '+err.message,'error');
        }finally{
            TAB_KEYS.forEach(x=>{
                _$('tab-'+x)?.classList.toggle('active',x===originalTab);
                _$('panel-'+x)?.classList.toggle('active',x===originalTab);
            });
            _btnState(btnPdf,false); _btnState(btnImg,false);
        }
    }

    // ── runCard() ──
    const _cardLabels={
        'donut'          : 'Distribusi Views — Top 5',
        'eng'            : 'Engagement Comparison',
        'list-view'      : 'Top Posts by Views',
        'list-retweet'   : 'Top Posts by Retweets',
        'list-follower'  : 'Top Posts by Followers',
        'list-sentiment' : 'Top Posts by Sentiment',
        'bar-view'       : 'Chart Most Viewed',
        'bar-retweet'    : 'Chart Most Retweeted',
        'bar-follower'   : 'Chart Top Followers',
        'bar-sentiment'  : 'Chart Sentiment',
    };
    function _cardFilename(k){
        const map={
            'donut':'distribusi-views-top5','eng':'engagement-comparison',
            'list-view':'top-posts-view','list-retweet':'top-posts-retweet',
            'list-follower':'top-posts-follower','list-sentiment':'top-posts-sentiment',
            'bar-view':'chart-most-viewed','bar-retweet':'chart-most-retweet',
            'bar-follower':'chart-top-follower','bar-sentiment':'chart-sentiment',
        };
        return `x_most_engagement_${map[k]||k}_${XMVCfg.pid}_${new Date().toISOString().slice(0,10).replace(/-/g,'')}`;
    }

    async function runCard(areaId,cardKey,type,btn){
        if(!window.html2canvas){ _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf'&&!window.jspdf?.jsPDF){ _toast('jsPDF tidak tersedia','error'); return; }
        _btnState(btn,true); _toast(type==='pdf'?'Menyiapkan PDF…':'Mengambil gambar…','default',99999);
        try{
            const area=document.getElementById(areaId);
            if(!area) throw new Error('Area #'+areaId+' tidak ditemukan');
            _resizeCharts();
            await new Promise(r=>setTimeout(r,220));
            const canvas=await _captureArea(area,true);
            const fname=_cardFilename(cardKey), label=_cardLabels[cardKey]||cardKey;
            if(type==='image'){
                const link=document.createElement('a');
                link.download=fname+'.png'; link.href=canvas.toDataURL('image/png'); link.click();
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
            console.error('[XMVExport.runCard]',err); _toast('Export gagal: '+err.message,'error');
        }finally{ _btnState(btn,false); }
    }

    return {run,runCard};
})();
/* ══ INIT ══ */
document.addEventListener('DOMContentLoaded', () => {
    XMVData.loadAll();
    document.addEventListener('keydown', e=>{ if(e.key==='Escape') XMVPanel.close(); });
});
</script>
@endsection