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
.ms-grid-1-1 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.ms-grid-11-9 { display:grid; grid-template-columns:1.2fr 1fr; gap:16px; }
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
.do-panel-actions { display:flex; align-items:center; gap:7px; padding:7px 12px; border-bottom:1px solid var(--slate-200); background:#fff; flex-shrink:0; flex-wrap:wrap; }
.do-panel-meta  { flex:1; font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px; display:flex; align-items:center; gap:5px; overflow:hidden; min-width:100px; }
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
.do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--slate-400); }
.do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.do-sent-badge--neu { background:var(--slate-100); color:var(--slate-500); }
.do-panel-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:12px; color:var(--slate-400); font-size:12px; font-weight:600; }
.do-panel-spinner { width:28px; height:28px; border:2.5px solid var(--slate-100); border-top-color:var(--primary); border-radius:50%; animation:spin .65s linear infinite; }
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

/* ══ Direct Link Button ══ */
.do-panel-link-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:22px; height:22px; border-radius:4px; flex-shrink:0;
    background:rgba(var(--primary-rgb),.08); color:var(--primary);
    border:1px solid rgba(var(--primary-rgb),.2);
    transition:background .13s, color .13s, border-color .13s;
    text-decoration:none;
}
.do-panel-link-btn:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.do-panel-link-btn i { font-size:12px; pointer-events:none; }

/* ══ Platform Picker ══ */
.do-plat-picker { position:fixed; z-index:999999; background:#fff; border:1px solid var(--slate-200); border-radius:var(--radius); box-shadow:var(--shadow-lg); padding:5px; min-width:175px; font-family:inherit; display:none; animation:fadeUp .14s ease-out; }
.do-plat-picker.show { display:block; }
.do-plat-picker-head { padding:4px 9px 7px; font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--slate-100); margin-bottom:3px; }
.do-plat-btn { display:flex; align-items:center; gap:7px; padding:7px 10px; border-radius:var(--radius-sm); font-size:12px; font-weight:600; cursor:pointer; background:transparent; border:none; font-family:inherit; width:100%; text-align:left; color:var(--slate-700); transition:background .12s; }
.do-plat-btn:hover { background:var(--primary-lt); color:var(--primary); }
.do-plat-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-left:auto; }

/* ══ Sentiment Tabs (MSPanel) ══ */
.sntp-sent-tabs { display:flex; background:var(--slate-100); border:1px solid var(--slate-200); border-radius:var(--radius-sm); padding:2px; gap:2px; }
.sntp-sent-tab  { padding:3px 9px; border-radius:3px; border:none; background:transparent; font-size:11px; font-weight:700; cursor:pointer; transition:all .13s; color:var(--slate-500); white-space:nowrap; font-family:inherit; }
.sntp-sent-tab:hover { background:#fff; }
.sntp-sent-tab.active { background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08); }
.sntp-sent-tab.active[data-s="all"] { color:var(--primary); }
.sntp-sent-tab.neg.active { color:#ef4444; }
.sntp-sent-tab.pos.active { color:#0ea5e9; }
.sntp-sent-tab.neu.active { color:var(--slate-500); }

/* ══ EXPORT STYLES ══ */
.page-export-bar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px;
    background:#fff; border:1px solid var(--slate-200);
    border-radius:var(--radius); padding:9px 14px;
    margin-bottom:16px; box-shadow:var(--shadow-sm);
}
.page-export-bar-left { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--slate-600); }
.page-export-bar-left i { font-size:15px; color:var(--primary); }
.page-export-bar-right { display:flex; gap:8px; }
.page-export-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:32px; height:32px; border-radius:var(--radius-sm);
    font-size:16px; cursor:pointer;
    transition:all .15s ease; border:1.5px solid transparent; font-family:inherit;
}
.page-export-btn-pdf { background:#fff3f3; color:#dc2626; border-color:#fca5a5; }
.page-export-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.page-export-btn-img { background:var(--primary-lt); color:var(--primary); border-color:rgba(67,97,238,.3); }
.page-export-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.page-export-btn:disabled { opacity:.55; cursor:not-allowed; pointer-events:none; }
.page-export-btn .export-spinner { width:13px; height:13px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
.page-export-btn.exporting .export-spinner { display:inline-block; }
.page-export-btn.exporting .export-icon { display:none; }

.card-exp-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:var(--radius-sm);
    font-size:14px; cursor:pointer; flex-shrink:0;
    transition:all .14s ease; border:1px solid transparent; font-family:inherit; background:transparent;
}
.card-exp-btn-pdf { color:#dc2626; border-color:#fca5a5; background:#fff3f3; }
.card-exp-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.card-exp-btn-img { color:var(--primary); border-color:rgba(67,97,238,.3); background:var(--primary-lt); }
.card-exp-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
.card-exp-btn .export-spinner { width:11px; height:11px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
.card-exp-btn.exporting .export-spinner { display:inline-block; }
.card-exp-btn.exporting .export-icon { display:none; }

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

/* ══ Responsive ══ */
@media(max-width:1280px) { .ms-grid-1-1,.ms-grid-11-9,.ms-grid-3-2,.ms-grid-2-3 { grid-template-columns:1fr; } }
@media(max-width:768px)  { .do-panel { width:100vw; } }

/* ══ Apexcharts click hint ══ */
.apx-click-hint {
    display:flex; align-items:center; gap:5px;
    font-size:10px; font-weight:600; color:var(--slate-400);
    margin-top:4px;
}
.apx-click-hint i { font-size:11px; }
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

{{-- ════ PAGE EXPORT WRAPPER ════ --}}
<div id="pageExportArea">

{{-- ══ KPI Cards ══ --}}
<div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-success text-white kpi-card-hover clickable" style="animation:fadeUp .38s ease-out both;"
             onclick="MSPanel.openSentiment('pos')">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
                        <h3 class="mb-0 text-white f-w-300" id="valPos">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctPos">
                            <i class="ph ph-chart-line-up me-1"></i>Loading…
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-danger text-white kpi-card-hover clickable" style="animation:fadeUp .38s ease-out .10s both;"
             onclick="MSPanel.openSentiment('neg')">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
                        <h3 class="mb-0 text-white f-w-300" id="valNeg">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctNeg">
                            <i class="ph ph-chart-line-up me-1"></i>Loading…
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-warning text-white kpi-card-hover clickable" style="animation:fadeUp .38s ease-out .05s both;"
             onclick="MSPanel.openSentiment('neu')">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Neutral</p>
                        <h3 class="mb-0 text-white f-w-300" id="valNeu">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="pctNeu">
                            <i class="ph ph-chart-line-up me-1"></i>Loading…
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-smiley-meh"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-primary text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .15s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p>
                        <h3 class="mb-0 text-white f-w-300" id="valTotal">—</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-calendar-blank me-1"></i>{{ \Carbon\Carbon::parse($startDate)->format('d M') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div>
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
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Charts + Trends</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                onclick="MSExport.run('pdf', this)" title="Export halaman sebagai PDF">
            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                onclick="MSExport.run('image', this)" title="Export halaman sebagai PNG">
            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
        </button>
    </div>
</div>

{{-- ══ Platform Mini Cards ══ --}}
<div class="row g-2 mb-3 fade-up" id="card-export-platforms">
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
    <div class="card" id="card-export-bar">
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
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light-secondary text-muted rounded-pill">All Platforms</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="MSExport.runCard('card-export-bar','bar','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="MSExport.runCard('card-export-bar','bar','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="ms-ch ms-ch-300">
                <div id="chBar"></div>
                <div class="sk-block sk-overlay" id="skBar"></div>
            </div>
        </div>
    </div>

    <div class="card" id="card-export-sov-mass">
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
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light-secondary text-muted rounded-pill">2 Categories</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="MSExport.runCard('card-export-sov-mass','sov-mass','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="MSExport.runCard('card-export-sov-mass','sov-mass','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="card-body" id="sovBodyMass" style="display:none; padding:16px 18px; flex-direction:column; align-items:center;">
             <div class="ms-ch ms-ch-280" style="width:100%; max-width:400px; margin:0 auto;">
                <div id="chSovMass"></div>
                <div class="sk-block sk-overlay" id="skSovMass"></div>
            </div>
        </div>
    </div>
</div>

{{-- ══ SOV Platform + Bar Race ══ --}}
<div class="ms-grid-11-9 mb-3 fade-up">
    <div class="card" id="card-export-sov-plat">
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
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light-secondary text-muted rounded-pill">By Platform</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="MSExport.runCard('card-export-sov-plat','sov-plat','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="MSExport.runCard('card-export-sov-plat','sov-plat','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="card-body" id="sovBodyPlat" style="display:none; padding:16px 18px; flex-direction:column; align-items:center;">
            <div class="ms-ch" style="height:340px; width:100%; max-width:500px; margin:0 auto;">
                <div id="chSovPlat"></div>
                <div class="sk-block sk-overlay" id="skSovPlat"></div>
            </div>
        </div>
    </div>

    <div class="card" id="card-export-bar-race">
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
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light-success text-success rounded-pill">Bar Race</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="MSExport.runCard('card-export-bar-race','bar-race','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="MSExport.runCard('card-export-bar-race','bar-race','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
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
<div class="ms-tabs fade-up" data-html2canvas-ignore="true">
    <button class="ms-tab-btn active" id="tabBtnTrend" onclick="MSTab.show('trend')">
        <i class="ph ph-pulse"></i> Trend Mentions
    </button>
    <button class="ms-tab-btn" id="tabBtnPola" onclick="MSTab.show('pola')">
        <i class="ph ph-clock"></i> Pola Waktu Posting
    </button>
</div>

{{-- ══ TAB: TREND ══ --}}
<div class="ms-tab-panel active" id="panelTrend">

    <div class="card mb-3" id="card-export-trend">
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
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="MSExport.runCard('card-export-trend','trend','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="MSExport.runCard('card-export-trend','trend','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="ms-ch ms-ch-340" style="position:relative;">
                <div class="sk-block sk-overlay" id="skTrend"></div>
                <div id="chTrend" style="width:100%;height:340px;"></div>
            </div>
            <div class="apx-click-hint" data-html2canvas-ignore="true">
                <i class="ph ph-cursor-click"></i>
             </div>
        </div>
    </div>

    <div class="card mb-3" id="card-export-article-trend">
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
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="MSExport.runCard('card-export-article-trend','article-trend','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="MSExport.runCard('card-export-article-trend','article-trend','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="ms-ch ms-ch-340" style="position:relative;">
                <div class="sk-block sk-overlay" id="skArticleTrend"></div>
                <div id="chArticleTrend" style="width:100%;height:340px;"></div>
            </div>
            <div class="apx-click-hint" data-html2canvas-ignore="true">
                <i class="ph ph-cursor-click"></i>
             </div>
        </div>
    </div>

</div>

{{-- ══ TAB: POLA WAKTU ══ --}}
<div class="ms-tab-panel" id="panelPola">
    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card" id="card-export-weekday">
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
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="MSExport.runCard('card-export-weekday','weekday','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="MSExport.runCard('card-export-weekday','weekday','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
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
            <div class="card" id="card-export-hour">
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
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="MSExport.runCard('card-export-hour','hour','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="MSExport.runCard('card-export-hour','hour','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
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

{{-- /pageExportArea --}}
</div>

{{-- ══ Export Toast ══ --}}
<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

{{-- ══ Slide Panel ══ --}}
<div class="do-panel-overlay" id="msPanelOverlay" onclick="MSPanel.closeByOverlay()"></div>
<div class="do-panel" id="msSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="msPanelDot"></div>
        <span class="do-panel-title" id="msPanelTitle">Mentions</span>
        <button class="do-panel-close" onclick="MSPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta">
            <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
            <span id="msPanelMeta">—</span>
        </div>
        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
            <div class="sntp-sent-tabs" id="msPanelSentTabs">
                <button class="sntp-sent-tab active" data-s="all" onclick="MSPanel.filterSent('all')">Semua</button>
                <button class="sntp-sent-tab pos"    data-s="pos" onclick="MSPanel.filterSent('pos')">Pos</button>
                <button class="sntp-sent-tab neg"    data-s="neg" onclick="MSPanel.filterSent('neg')">Neg</button>
                <button class="sntp-sent-tab neu"    data-s="neu" onclick="MSPanel.filterSent('neu')">Neu</button>
            </div>
        </div>
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

{{-- ══ Platform Picker ══ --}}
<div class="do-plat-picker" id="msPlatPicker">
    <div class="do-plat-picker-head">Pilih Platform</div>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('twit')">X / Twitter <span class="do-plat-dot" style="background:#1d9bf0;"></span></button>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('fb')">Facebook <span class="do-plat-dot" style="background:#1877f2;"></span></button>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('ig')">Instagram <span class="do-plat-dot" style="background:#e1306c;"></span></button>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('yt')">YouTube <span class="do-plat-dot" style="background:#ff0000;"></span></button>
    <button class="do-plat-btn" onclick="MSPanel.openPlatform('tiktok')">TikTok <span class="do-plat-dot" style="background:#111827;"></span></button>
</div>

{{-- ══ CSV Modal ══ --}}
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

{{-- ══ Media Viewer Modal ══ --}}
<div class="video-modal-overlay" id="vidViewModal" onclick="closeVidModal()" style="display:none;position:fixed;inset:0;background:rgba(15,20,25,0.85);backdrop-filter:blur(8px);z-index:99999;align-items:center;justify-content:center;padding:20px;opacity:0;transition:opacity .2s ease;">
    <div class="video-modal-content" style="position:relative;width:100%;max-width:800px;background:#000;border-radius:12px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.4);transform:scale(0.95);transition:transform .2s ease;" onclick="event.stopPropagation()">
        <button onclick="closeVidModal()" style="position:absolute;top:12px;right:12px;z-index:10;background:rgba(0,0,0,.5);color:#fff;border:none;width:36px;height:36px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;"><i class="ph ph-x" style="font-size:18px;"></i></button>
        <div id="vidViewWrap" style="width:100%;aspect-ratio:16/9;background:#000;display:flex;align-items:center;justify-content:center;"></div>
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

/* ══ ECharts registry ══ */
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

/* ══ ApexCharts instances ══ */
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

function apxBase(colors, series, categories, height=340, onPointClick) {
  const opts = {
    chart: {
      type:'area', height,
      fontFamily:'inherit', background:'transparent',
      toolbar:{show:false},
      animations:{enabled:true,easing:'linear',dynamicAnimation:{speed:1000}},
      events: {
        click: function(event, chartContext, config) {
          if(typeof onPointClick !== 'function') return;
          let platformKey = null;
          if(config.seriesIndex !== undefined && config.seriesIndex >= 0) {
            platformKey = config.seriesIndex;
          } else {
            const globals = chartContext?.w?.globals;
            if(globals && globals.seriesXvalues) { platformKey = 0; }
          }
          if(platformKey !== null) { onPointClick(platformKey, event.clientX, event.clientY); }
        },
        legendClick: function(chartContext, seriesIndex) {
          if(typeof onPointClick !== 'function') return;
          onPointClick(seriesIndex, window.innerWidth - 520, 200);
        },
        mouseMove: function(event, chartContext, config) {
          const el = chartContext?.el; if(el) el.style.cursor = 'pointer';
        },
        mouseLeave: function(event, chartContext) {
          const el = chartContext?.el; if(el) el.style.cursor = 'default';
        }
      }
    },
    series, colors,
    xaxis: {
      categories,
      axisBorder:{show:false}, axisTicks:{show:false},
      labels:{style:{fontFamily:'inherit',fontSize:'11px',fontWeight:600,colors:'#94A3B8'}}
    },
    yaxis: {
      labels:{formatter:v=>numK(v),style:{fontFamily:'inherit',fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},
      axisBorder:{show:false}, axisTicks:{show:false}
    },
    fill:{ opacity:0.3 },
    stroke:{ curve:'smooth', width:2.5 },
    grid:{ borderColor:'rgba(226,232,240,.55)', strokeDashArray:3, xaxis:{lines:{show:false}} },
    legend:{
      position:'bottom', horizontalAlign:'left', fontFamily:'inherit',
      fontSize:'11px', fontWeight:'600', labels:{colors:'#94A3B8'},
      markers:{width:9,height:9,radius:50},
      itemMargin:{horizontal:14,vertical:4},
      onItemClick:{ toggleDataSeries:true },
      onItemHover:{ highlightDataSeries:true }
    },
    tooltip:{
      shared:false, intersect:true,
      style:{fontFamily:'inherit',fontSize:'12px'},
      y:{formatter:v=>numFmt(v)+' mentions'}
    },
    dataLabels:{
      enabled:true,
      formatter:v=>v>0?numFmt(v):'',
      style:{fontSize:'10px',fontFamily:'inherit',fontWeight:'700'},
      background:{enabled:true,borderRadius:3,borderWidth:0,padding:3,opacity:0.9},
      offsetY:-6
    },
    markers:{ size:5, strokeWidth:2, strokeColors:'#fff', hover:{size:7}, discrete:[] },
    states:{
      hover:{ filter:{ type:'lighten', value:0.1 } },
      active:{ filter:{ type:'darken', value:0.35 } }
    }
  };
  return opts;
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
  const seriesData=labels.map((label,i)=>({name:label,value:values[i],subtitle:subtitles?subtitles[i]:'',itemStyle:{color:colors[i],borderColor:'#fff',borderWidth:3,borderRadius:5}}));
  chart.setOption({animation:true,animationDuration:800,animationEasing:'cubicOut',backgroundColor:'transparent',
    tooltip:{trigger:'item',backgroundColor:'#1e293b',borderColor:'#334155',borderWidth:1,padding:[9,13],textStyle:{color:'#fff',fontFamily:'inherit',fontSize:12},extraCssText:'border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
      formatter:params=>{const pct=total>0?((params.value/total)*100):0;const sub=params.data.subtitle?`<br><span style="color:#94a3b8;font-size:11px;">${params.data.subtitle}</span>`:'';const displayName=params.name.replace('Mass Media','Online News').replace('X (Twitter)','X');return`<div style="font-weight:700;font-size:13px;margin-bottom:5px;">${displayName}${sub}</div><div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">${numFmt(params.value)}</span></div><div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct<1&&pct>0?'<1':pct.toFixed(1)}%</span></div>`;}},
    legend:{show:false},
    series:[{type:'pie',radius:['40%','60%'],center:['50%','50%'],avoidLabelOverlap:true,minAngle:15,padAngle:2,
      itemStyle:{borderRadius:5},
      label:{show:true,alignTo:'labelLine',fontFamily:'inherit',fontSize:11,color:'#374151',distanceToLabelLine:5,
        formatter:params=>{const pc=total>0?(params.value/total)*100:0;const rawName=String(params.name).split('\n')[0].replace(/ \d+(\.\d+)?%$/, '').replace('X (Twitter)','X').replace('Mass Media','Online News');const name=rawName.length>10?rawName.slice(0,9)+'…':rawName;return `{name|${name}}\n{pct|${Math.round(pc)}%}`;},
        rich:{name:{fontWeight:'700',fontSize:10,color:'#1a202c',lineHeight:16},pct:{fontWeight:'800',fontSize:10,color:'#038047',lineHeight:14,backgroundColor:'#edf7f3',borderRadius:4,padding:[2,4]}}},
      labelLine:{show:true,length:12,length2:16,smooth:0.4,lineStyle:{color:'#94a3b8',width:1.4}},
      labelLayout:{hideOverlap:false,moveOverlap:'shiftY'},
      emphasis:{scale:true,scaleSize:5,itemStyle:{shadowBlur:12,shadowColor:'rgba(0,0,0,.2)'}},data:seriesData}],
    graphic:[
      {type:'text',left:'center',top:'47%',z:100,style:{text:numK(total),fill:'#0f172a',font:"800 20px inherit",textAlign:'center'}},
      {type:'text',left:'center',top:'55%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"700 9px inherit",textAlign:'center',letterSpacing:2}},
    ]});
  if(onClickFns){chart.on('click',params=>{const fn=onClickFns[params.dataIndex];if(typeof fn==='function'){const rect=chart.getDom().getBoundingClientRect();fn(rect.left+rect.width/2,rect.top+rect.height/2);}});}
  chart.on('mouseover',()=>{if(onClickFns)chart.getDom().style.cursor='pointer';});
  chart.on('mouseout', ()=>{chart.getDom().style.cursor='default';});
  return chart;
}

/* ══ LOAD MENTION BY PLATFORM ══ */
async function loadMentionByPlatform(){
  if(!MSCfg.pid){ ['valPos','valNeu','valNeg','valTotal'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<span style="font-size:13px;color:#94a3b8;">—</span>'}); ['pctPos','pctNeu','pctNeg'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<i class="ph ph-warning-circle me-1"></i>No Project';}); ['skBar','skSovMass','skSovPlat','skBarRace'].forEach(hideSk); return; }
  try{
    const [resPlat, resSent] = await Promise.all([
      fetch(`/mk/api/media-statistic/mention-by-platform?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`),
      fetch(`/mk/api/sentiment/totals?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`)
    ]);
    const d=await resPlat.json();
    const s=await resSent.json();

    if(d.error) throw new Error(d.error);
    
    /* Update Sentiment KPIs */
    const sent = s.totals || {pos:0, neu:0, neg:0};
    const totalSent = (sent.pos||0) + (sent.neu||0) + (sent.neg||0) || 1;
    document.getElementById('valPos').textContent = numFmt(sent.pos);
    document.getElementById('valNeu').textContent = numFmt(sent.neu);
    document.getElementById('valNeg').textContent = numFmt(sent.neg);
    document.getElementById('valTotal').textContent = numFmt(totalSent);
    
    document.getElementById('pctPos').innerHTML = `<i class="ph ph-trend-up me-1"></i>${(sent.pos/totalSent*100).toFixed(1)}% Share`;
    document.getElementById('pctNeu').innerHTML = `<i class="ph ph-minus me-1"></i>${(sent.neu/totalSent*100).toFixed(1)}% Share`;
    document.getElementById('pctNeg').innerHTML = `<i class="ph ph-trend-down me-1"></i>${(sent.neg/totalSent*100).toFixed(1)}% Share`;

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
          series:[{type:'bar',data:bValues.map((v,i)=>({value:v,itemStyle:{color:bColors[i],borderRadius:[7,7,0,0]},emphasis:{itemStyle:{color:bColors[i],shadowBlur:12,shadowColor:bColors[i]+'66'}}})),barMaxWidth:52,label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:10,color:'#64748b',formatter:p=>numK(p.value)}}]});
        barChart.on('click',params=>{const k=labelToKey[bLabels[params.dataIndex]];if(k){const rect=barChart.getDom().getBoundingClientRect();MSPanel.open(k,rect.left+rect.width/2,rect.top+100);}});
        barChart.on('mouseover',()=>{barChart.getDom().style.cursor='pointer';});
        barChart.on('mouseout', ()=>{barChart.getDom().style.cursor='default';});
      }
    } else { document.getElementById('chBar').innerHTML=emptyHtml('Tidak ada data mention'); }
    hideSk('skSovMass');
    const sovBodyMass = document.getElementById('sovBodyMass');
    if(sovBodyMass) sovBodyMass.style.display = 'flex';
    makeEDoughnut('chSovMass',['Mass Media','Social Media'],[d.mass_total||0,d.social_total||0],['#0284c7','#10B981'],[(x,y)=>MSPanel.open('doc',x,y),(x,y)=>MSPanel.showPlatPicker(x,y)],null);

    hideSk('skSovPlat');
    const sovBodyPlat = document.getElementById('sovBodyPlat');
    if(sovBodyPlat) sovBodyPlat.style.display = 'flex';
    const nz=platforms.filter(p=>p.count>0);const pList=nz.length?nz:platforms;
    const totalPlat = d.grand_total||1;
    makeEDoughnut('chSovPlat',pList.map(p=>p.label),pList.map(p=>p.count||0),pList.map(p=>MSCfg.platColors[p.label]||'#4361EE'),pList.map(p=>{const k=labelToKey[p.label];return k?(x,y)=>MSPanel.open(k,x,y):null;}),pList.map(p=>{return((p.count||0)/totalPlat*100).toFixed(1)+'%';}));
    hideSk('skBarRace');
    if(platforms.length){
      const grandTotal=d.grand_total||1;
      const brData=platforms.map(p=>({label:p.label,value:p.count||0,color:MSCfg.platColors[p.label]||'#4361EE'})).sort((a,b)=>a.value-b.value);
      const brMax=Math.max(...brData.map(p=>p.value),1);
      const brChart=MSCharts.make('chBarRace');
      if(brChart){
        const buildSD=items=>items.map(item=>({value:item.value,itemStyle:{color:item.color,borderRadius:[0,9,9,0]},emphasis:{itemStyle:{shadowBlur:18,shadowColor:item.color+'55'}}}));
        brChart.setOption({animation:true,animationDuration:1400,animationDurationUpdate:1100,animationEasing:'elasticOut',animationEasingUpdate:'cubicInOut',backgroundColor:'transparent',
          tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'shadow'},formatter:params=>{const p=params[0];const item=brData.find(x=>x.label===p.name)||{};const pct=((p.value/grandTotal)*100).toFixed(1);const clr=item.color||'#4361EE';return`<div style="font-weight:800;font-size:13px;margin-bottom:9px;padding-bottom:7px;border-bottom:1px solid rgba(255,255,255,.12);"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${clr};margin-right:6px;vertical-align:middle;"></span>${p.name}</div><div style="display:flex;justify-content:space-between;gap:22px;margin-bottom:5px;"><span style="font-size:11px;color:#94a3b8;">Mentions</span><span style="font-size:14px;font-weight:700;">${numFmt(p.value)}</span></div><div style="display:flex;justify-content:space-between;gap:22px;"><span style="font-size:11px;color:#94a3b8;">Share of Voice</span><span style="font-size:12px;font-weight:700;color:#34d399;">${pct}%</span></div>`;}},
          grid:{top:10,right:108,bottom:10,left:14,containLabel:true},
          xAxis:{type:'value',max:brMax*1.15,axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f8fafc',type:'solid'}},axisLabel:{show:false}},
          yAxis:{type:'category',data:brData.map(p=>p.label),inverse:false,animationDuration:300,animationDurationUpdate:1100,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'700',color:'#1a202c',margin:12}},
          series:[{realtimeSort:true,type:'bar',data:buildSD(brData),barMaxWidth:40,label:{show:true,position:'right',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#1a202c',formatter:p=>{const pct=((p.value/grandTotal)*100).toFixed(1);return`{val|${numFmt(p.value)}}  {pct|${pct}%}`;},rich:{val:{fontSize:11,fontWeight:'700',color:'#1a202c',fontFamily:"'Poppins',sans-serif"},pct:{fontSize:9,fontWeight:'600',color:'#94a3b8',fontFamily:"'Poppins',sans-serif"}}}}]});
        setTimeout(()=>{ const sorted=[...brData].sort((a,b)=>b.value-a.value); brChart.setOption({yAxis:{data:sorted.map(p=>p.label)},series:[{data:buildSD(sorted)}]}); },1600);
        brChart.on('click',params=>{const k=labelToKey[params.name];if(k){const rect=brChart.getDom().getBoundingClientRect();MSPanel.open(k,rect.left+rect.width/2,rect.top+100);}});
        brChart.on('mouseover',()=>{brChart.getDom().style.cursor='pointer';});
        brChart.on('mouseout', ()=>{brChart.getDom().style.cursor='default';});
      }
    } else { const bd=document.getElementById('chBarRace');if(bd)bd.innerHTML=emptyHtml('Tidak ada data mention'); }
  }catch(err){
    console.error('loadMentionByPlatform:',err);
    ['valPos','valNeu','valNeg','valTotal'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<span style="font-size:12px;color:#dc2626;font-weight:600;">Error</span>';});
    ['pctPos','pctNeu','pctNeg'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<i class="ph ph-warning-circle me-1"></i>Gagal memuat';});
    ['skBar','skSovMass','skSovPlat','skBarRace'].forEach(hideSk);
  }
}

/* ══ LOAD TREND ══ */
async function loadTrend(){
  if(!MSCfg.pid){ hideSk('skTrend'); return; }
  const fmtDate=d=>`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  let trendSD,trendED;
  if(MSTrendToggle._datePickerOverride){ trendSD=MSCfg.sd; trendED=MSCfg.ed; }
  else{ const now=new Date(),off=MSTrendToggle._weekOffset;const edDate=new Date(now);edDate.setDate(now.getDate()-(7*off));const sdDate=new Date(now);sdDate.setDate(now.getDate()-(7*(off+1)));trendSD=fmtDate(sdDate);trendED=fmtDate(edDate); }
  const platMeta={doc:{label:'Online News',color:'#0284c7'},twitter:{label:'Twitter',color:'#1d9bf0'},facebook:{label:'Facebook',color:'#1877f2'},instagram:{label:'Instagram',color:'#e1306c'},youtube:{label:'YouTube',color:'#ff0000'},tiktok:{label:'TikTok',color:'#111827'}};
  const platOrder=['doc','twitter','facebook','instagram','youtube','tiktok'];
  const keyMap={'Online News':'doc','Twitter':'twit','Facebook':'fb','Instagram':'ig','YouTube':'yt','TikTok':'tiktok'};
  try{
    const res=await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${MSCfg.pid}&start_date=${trendSD}&end_date=${trendED}`);
    const json=await res.json();if(json.error)throw new Error(json.error);
    hideSk('skTrend');
    const raw=json.data||[];
    const dSet=new Set();raw.forEach(p=>(p.data||[]).forEach(d=>dSet.add(d.date)));
    const allDates=Array.from(dSet).sort();
    MSTrendToggle.setData(raw);
    if(MSTrendToggle._mode==='monthly'){ MSTrendToggle._render(raw); return; }
    const fmtB=d=>{const dt=new Date(d+'T00:00:00');return`${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`;};
    document.getElementById('trendBadge').textContent=`${fmtB(trendSD)} – ${fmtB(trendED)}`;
    const sub=document.getElementById('trendSubtitle');if(sub)sub.textContent=`${fmtB(trendSD)} – ${fmtB(trendED)}`;
    const weekNavGroup=document.getElementById('weekNavGroup'),weekNavLabel=document.getElementById('weekNavLabel'),weekNavNext=document.getElementById('weekNavNext');
    if(weekNavGroup&&!MSTrendToggle._datePickerOverride){ weekNavGroup.style.display='flex';if(weekNavLabel)weekNavLabel.textContent=MSTrendToggle._weekLabel();if(weekNavNext){const ic=MSTrendToggle._weekOffset===0;weekNavNext.disabled=ic;weekNavNext.style.opacity=ic?'.35':'1';weekNavNext.style.cursor=ic?'not-allowed':'pointer';} }
    else if(weekNavGroup) weekNavGroup.style.display='none';
    const xLabels=allDates.map(d=>{const dt=new Date(d+'T00:00:00');return`${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`;});
    const seriesArr=platOrder.map(key=>{const meta=platMeta[key];const found=raw.find(p=>p.key===key);const vals=allDates.map(date=>{const pt=(found?.data||[]).find(x=>x.date===date);return pt?pt.count:0;});return{name:meta.label,data:vals};}).filter(s=>s.data.some(v=>v>0));
    const colorsArr=seriesArr.map(s=>{const key=Object.keys(platMeta).find(k=>platMeta[k].label===s.name);return platMeta[key]?.color||'#94a3b8';});
    _destroyApx('trend');
    const el=document.getElementById('chTrend');if(!el)return;
    const onPointClick = (seriesIndex, cx, cy) => {
      const sName = seriesArr[seriesIndex]?.name;
      const k = keyMap[sName];
      if(k) MSPanel.open(k, cx, cy);
    };
    const trendOpts = apxBase(colorsArr, seriesArr, xLabels, 340, onPointClick);
    APX.trend = new ApexCharts(el, trendOpts);
    APX.trend.render();
  }catch(err){ hideSk('skTrend');document.getElementById('trendBadge').textContent='Error';document.getElementById('chTrend').innerHTML=emptyHtml('Data trend tidak tersedia'); }
}

/* ══ LOAD ARTICLE TREND ══ */
async function loadArticleTrend(){
  if(!MSCfg.pid){ hideSk('skArticleTrend'); return; }
  const fmtB=d=>{const dt=new Date(d+'T00:00:00');return`${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`;};
  try{
    const res=await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`);
    const json=await res.json();if(json.error)throw new Error(json.error);
    hideSk('skArticleTrend');
    const raw=json.data||[];const docData=raw.find(p=>p.key==='doc');
    if(!docData||!docData.data?.length){ document.getElementById('articleTrendBadge').textContent='No Data';document.getElementById('chArticleTrend').innerHTML=emptyHtml('Data artikel tidak tersedia untuk periode ini');return; }
    document.getElementById('articleTrendBadge').textContent=`${fmtB(MSCfg.sd)} – ${fmtB(MSCfg.ed)}`;
    const dates=docData.data.map(d=>d.date),values=docData.data.map(d=>d.count);
    MSCsvModal.setArticleData(dates,values);
    const xLabels=dates.map(d=>{const dt=new Date(d+'T00:00:00');return`${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`;});
    _destroyApx('article');
    const el=document.getElementById('chArticleTrend');if(!el)return;
    const onPointClick = (_sIdx, cx, cy) => { MSPanel.open('doc', cx, cy); };
    const opts = apxBase(['#0284c7'], [{name:'Online News', data:values}], xLabels, 340, onPointClick);
    opts.tooltip.y = { formatter: v => numFmt(v)+' articles' };
    APX.article = new ApexCharts(el, opts);
    APX.article.render();
  }catch(err){ hideSk('skArticleTrend');document.getElementById('articleTrendBadge').textContent='Error';document.getElementById('chArticleTrend').innerHTML=emptyHtml('Data artikel tidak tersedia'); }
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
      wdChart.on('click',params=>{if(params.componentType!=='series')return;const k=ltk[params.seriesName];const rect=wdChart.getDom().getBoundingClientRect();if(k)MSPanel.open(k,rect.left+params.event.offsetX,rect.top+params.event.offsetY);else MSPanel.showPlatPicker(rect.left+params.event.offsetX,rect.top+params.event.offsetY);});
      wdChart.on('mouseover',params=>{if(params.componentType==='series')wdChart.getDom().style.cursor='pointer';});
      wdChart.on('mouseout',()=>{wdChart.getDom().style.cursor='default';});
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
      hrChart.on('click',params=>{if(params.componentType!=='series')return;const k=ltk[params.seriesName];const rect=hrChart.getDom().getBoundingClientRect();if(k)MSPanel.open(k,rect.left+params.event.offsetX,rect.top+params.event.offsetY);else MSPanel.showPlatPicker(rect.left+params.event.offsetX,rect.top+params.event.offsetY);});
      hrChart.on('mouseover',params=>{if(params.componentType==='series')hrChart.getDom().style.cursor='pointer';});
      hrChart.on('mouseout',()=>{hrChart.getDom().style.cursor='default';});
    }
  }catch(e){ hideSk('skHour');document.getElementById('chHour').innerHTML=emptyHtml('Data tidak tersedia'); }
}

/* ══════════════════════════════════════════════════════
   SLIDE PANEL — FIXED: openSentiment + filterSent
══════════════════════════════════════════════════════ */
const MSPanel = (() => {
  let _cache = {}, _allItems = [], _curPlat = null, _curSent = 'all';

  const SENT_MAP = {
    '1':'pos','positive':'pos','positif':'pos',
    '-1':'neg','2':'neg','negative':'neg','negatif':'neg'
  };
  const _ns  = item => SENT_MAP[String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim()] || 'neu';
  const _$   = id   => document.getElementById(id);

  /* ── Sync active tab button ── */
  function _syncTabs(sent) {
    document.querySelectorAll('#msPanelSentTabs .sntp-sent-tab')
      .forEach(b => b.classList.toggle('active', b.dataset.s === sent));
  }

  /* ── Filter + re-render sesuai sentimen aktif ── */
  function filterSent(sent) {
    _curSent = sent;
    _syncTabs(sent);
    const list = _$('msPanelList');
    if (!list) return;
    const meta    = MSCfg.platMeta[_curPlat] || { label: _curPlat || 'All', color: '#4361EE' };
    const items   = _curSent === 'all' ? _allItems : _allItems.filter(i => _ns(i) === _curSent);
    _render(list, items, _curPlat, meta.color, false, true);
  }

  /* ── Buka panel dari klik KPI Sentiment ── */
  async function openSentiment(type) {
    const sentKey    = type; // 'pos' | 'neg' | 'neu'
    const sentColors = { pos:'#10B981', neg:'#EF4444', neu:'#F59E0B' };
    const sentLabels = { pos:'Positive Mentions', neg:'Negative Mentions', neu:'Neutral Mentions' };

    _curPlat = 'all';
    _curSent = sentKey;

    _$('msPanelDot').style.background = sentColors[sentKey] || '#4361EE';
    _$('msPanelTitle').textContent     = sentLabels[sentKey] || 'Mentions';
    _$('msPanelMeta').textContent      = MSCfg.sd + ' – ' + MSCfg.ed;
    _syncTabs(sentKey);

    const list    = _$('msPanelList');
    const overlay = _$('msPanelOverlay'), panel = _$('msSntPanel');
    list.innerHTML = `<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;
    overlay.classList.remove('hiding'); panel.classList.remove('hiding');
    overlay.classList.add('show');      panel.classList.add('show');
    document.body.style.overflow = 'hidden';

    try {
      const cacheKey = `${MSCfg.pid}_all_${MSCfg.sd}_${MSCfg.ed}`;
      if (!_cache[cacheKey]) _cache[cacheKey] = await _fetchAll();
      _allItems = _cache[cacheKey];
      const filtered = _allItems.filter(i => _ns(i) === sentKey);
      _render(list, filtered, 'all', sentColors[sentKey] || '#4361EE', false, true);
    } catch(err) {
      list.innerHTML = `<div class="do-panel-loading" style="color:#94a3b8;"><i class="ph ph-warning-circle" style="font-size:28px;"></i>Gagal memuat data</div>`;
    }
  }

  function showPlatPicker(x, y) {
    const pp = _$('msPlatPicker'); if (!pp) return;
    const pw=180, ph=250, vw=window.innerWidth, vh=window.innerHeight;
    let left=x+10, top=y-10;
    if (left+pw > vw-8) left = x-pw-10;
    if (top+ph  > vh-8) top  = vh-ph-8;
    if (top < 8) top = 8;
    pp.style.left = left+'px'; pp.style.top = top+'px';
    pp.classList.add('show');
  }

  function openPlatform(platform) {
    _$('msPlatPicker')?.classList.remove('show');
    open(platform, window.innerWidth - 500, 80);
  }

  async function open(platform, x, y) {
    _curPlat = platform;
    _curSent = 'all';
    _syncTabs('all');
    MSDetail.close();

    const meta = MSCfg.platMeta[platform] || { label: platform, color: '#4361EE' };
    _$('msPanelDot').style.background = meta.color;
    _$('msPanelTitle').textContent    = meta.label;
    _$('msPanelMeta').textContent     = MSCfg.sd + ' – ' + MSCfg.ed;

    const list    = _$('msPanelList');
    const overlay = _$('msPanelOverlay'), panel = _$('msSntPanel');
    list.innerHTML = `<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;
    overlay.classList.remove('hiding'); panel.classList.remove('hiding');
    overlay.classList.add('show');      panel.classList.add('show');
    document.body.style.overflow = 'hidden';

    try {
      const key = `${MSCfg.pid}_${platform}_${MSCfg.sd}_${MSCfg.ed}`;
      if (!_cache[key]) _cache[key] = await _fetch(platform);
      _allItems = _cache[key];
      _render(list, _allItems, platform, meta.color);
    } catch(err) {
      list.innerHTML = `<div class="do-panel-loading" style="color:#94a3b8;"><i class="ph ph-warning-circle" style="font-size:28px;"></i>Gagal memuat data</div>`;
    }
  }

  function close() {
    const overlay = _$('msPanelOverlay'), panel = _$('msSntPanel');
    panel.classList.add('hiding'); overlay.classList.add('hiding');
    document.body.style.overflow = '';
    setTimeout(() => {
      panel.classList.remove('show','hiding');
      overlay.classList.remove('show','hiding');
      MSDetail.close();
    }, 240);
  }

  function closeByOverlay() { close(); }

  /* ── Fetch semua platform sekaligus ── */
  async function _fetchAll() {
    const platforms = ['doc','twit','fb','ig','yt','tiktok'];
    const results   = await Promise.allSettled(platforms.map(p => _fetch(p)));
    let merged = [];
    results.forEach((r, i) => {
      if (r.status === 'fulfilled') {
        r.value.forEach(item => { if (!item._type) item._type = platforms[i]; });
        merged = merged.concat(r.value);
      }
    });
    merged.sort((a, b) =>
      (b.date_created||b.created_at||'').localeCompare(a.date_created||a.created_at||'')
    );
    return merged;
  }

  async function _fetch(platform) {
    const q = `project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}&rows=500&start=0`;

    if (platform === 'ig') {
      for (const sub of ['postbylike','postbycomment','postbydate','']) {
        try {
          const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 15000);
          const res = await fetch(`/mk/api/news/ig-top-status?${q}${sub?'&sub='+sub:''}`, { signal: ctrl.signal });
          clearTimeout(tid); if (!res.ok) continue;
          const d = await res.json();
          let items = [];
          if (Array.isArray(d?.data?.data))   items = d.data.data;
          else if (Array.isArray(d?.data))     items = d.data;
          else if (Array.isArray(d?.statuses)) items = d.statuses;
          else if (Array.isArray(d))           items = d;
          if (items.length > 0) return items;
        } catch(e) { continue; }
      }
      return [];
    }

    if (platform === 'yt') {
      for (const sub of ['postbylike','postbyview','postbydate','postbycomment','']) {
        try {
          const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 15000);
          const res = await fetch(`/mk/api/news/ytb-top-status?${q}${sub?'&sub='+sub:''}`, { signal: ctrl.signal });
          clearTimeout(tid); if (!res.ok) continue;
          const d = await res.json();
          let items = [];
          if (Array.isArray(d?.data?.data))    items = d.data.data;
          else if (Array.isArray(d?.data))      items = d.data;
          else if (Array.isArray(d?.statuses))  items = d.statuses;
          else if (Array.isArray(d?.results))   items = d.results;
          else if (Array.isArray(d?.posts))     items = d.posts;
          else if (Array.isArray(d))            items = d;
          else if (d?.data && typeof d.data === 'object' && !Array.isArray(d.data)) {
            const vals = Object.values(d.data);
            if (vals.length && typeof vals[0] === 'object') items = vals;
          }
          if (items.length > 0) return items;
        } catch(e) { continue; }
      }
      return [];
    }

    const eps = {
      doc    : `/mk/api/news/mentions?${q}`,
      twit   : `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
      fb     : `/mk/api/news/fb-top-status?${q}&sub=fblike`,
      tiktok : `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
    };
    const url = eps[platform]; if (!url) throw new Error('Platform tidak dikenali');
    const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 30000);
    const res = await fetch(url, { signal: ctrl.signal }); clearTimeout(tid);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const d = await res.json();

    let items = [];
    if (Array.isArray(d?.data?.data))    items = d.data.data;
    else if (Array.isArray(d?.data))     items = d.data;
    else if (Array.isArray(d?.statuses)) items = d.statuses;
    else if (Array.isArray(d?.results))  items = d.results;
    else if (Array.isArray(d?.posts))    items = d.posts;
    else if (Array.isArray(d))           items = d;
    else if (d?.data && typeof d.data === 'object' && !Array.isArray(d.data)) {
      const vals = Object.values(d.data);
      if (vals.length && typeof vals[0] === 'object') items = vals;
    }

    if (platform === 'doc') items = items.filter(m => {
      const tc = String(m.tcode||'').toLowerCase(), mt = String(m.media_type||'').toLowerCase();
      return tc === 'berita' || mt === 'berita' || mt === 'doc' || mt === 'news' || mt === 'online' || mt === 'article';
    });

    return items;
  }

  function _render(list, items, platform, color, showAll=false, skipScroll=false) {
    if (!skipScroll) list.scrollTop = 0;
    if (!items.length) {
      list.innerHTML = `<div style="padding:50px 20px;text-align:center;color:#94a3b8;font-size:12px;font-weight:600;">Tidak ada mention${_curSent !== 'all' ? ' untuk filter ini' : ' periode ini'}.</div>`;
      return;
    }

    /* Untuk panel "all" (dari KPI), tiap item punya _type */
    const getPlat = item => item._type || platform;

    const SHOW = 60;
    const visibleItems = showAll ? items : items.slice(0, SHOW);

    list.innerHTML = visibleItems.map(item => {
      const plat      = getPlat(item);
      const meta      = MSCfg.platMeta[plat] || MSCfg.platMeta[platform] || { label: platform, color };
      const itemColor = meta.color;

      const rawName = (()=>{
        if (plat==='fb')     return item.from_name || item.page_name || null;
        if (plat==='ig')     return item.username || item.user_name || null;
        if (plat==='tiktok') return item.author_nickname || item.nickname || item.author_name || null;
        if (plat==='yt')     return item.channel_title || item.channel_name || item.author_name || null;
        return null;
      })();
      const name  = (rawName || item.author_name || item.channel_name || item.publisher ||
                     item.source_name || item.name || item.author_scr_name ||
                     item.screen_name || item.username || 'Tidak diketahui').trim();
      const dName = /^\d{8,}$/.test(name) ? `User ${name.slice(-4)}` : name;

      const rawH  = ((plat==='ig'?item.username:'')||item.author_scr_name||item.screen_name||item.username||'').trim();
      const handle = (()=>{
        if (!rawH) return '';
        const w = ['twit','ig','tiktok'].includes(plat) ? (rawH.startsWith('@')?rawH:'@'+rawH) : rawH;
        return w.replace(/^@/,'').toLowerCase() === dName.toLowerCase() ? '' : w;
      })();

      const text = (item.content||item.caption||item.description||item.title||item.text||'')
                    .replace(/<[^>]*>/g,'').trim().slice(0,155);
      const url  = (item.url||item.link||'').trim();
      const av   = (item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||'').trim();

      const words = dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
      const ini   = (words.length>=2 ? (words[0][0]+words[words.length-1][0]) : (words[0]?.[0]||dName[0]||'?'))
                     .toUpperCase().replace(/['"]/g,'');
      const avHtml = (av && av.startsWith('http'))
        ? `<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini}';">`
        : ini;

      const sent = _ns(item);
      const dt   = (item.date_created||item.created_at||'').split('T')[0];
      const enc  = encodeURIComponent(JSON.stringify(item));

      const linkBtn = url
        ? `<a href="${esc(url)}" target="_blank" rel="noopener noreferrer"
               onclick="event.stopPropagation()" title="Buka di tab baru"
               class="do-panel-link-btn">
             <i class="ph ph-arrow-square-out"></i>
           </a>`
        : '';

      return `<div class="do-panel-item" onclick="MSDetail.openEncoded('${enc}','${plat}')">
        <div class="do-panel-avatar" style="background:linear-gradient(135deg,${itemColor},${itemColor}99);">${avHtml}</div>
        <div class="do-panel-item-body">
          <div class="do-panel-author">${esc(dName)}</div>
          ${handle ? `<div class="do-panel-handle">${esc(handle)}</div>` : ''}
          <div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div>
          <div class="do-panel-footer" style="justify-content:space-between;flex-wrap:nowrap;">
            <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;min-width:0;">
              <span class="do-sent-badge do-sent-badge--${sent}">${sent==='pos'?'Pos':sent==='neg'?'Neg':'Neu'}</span>
              <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${itemColor};flex-shrink:0;"></span>
              <span style="font-size:10px;font-weight:600;color:${itemColor};">${meta.label}</span>
              ${dt ? `<span style="font-size:10px;color:var(--slate-400);">${dt}</span>` : ''}
            </div>
            ${linkBtn}
          </div>
        </div>
      </div>`;
    }).join('');

    if (!showAll && items.length > SHOW) {
      list.insertAdjacentHTML('beforeend', `
        <div style="padding:16px;text-align:center;background:#f8fafc;border-top:1px dashed #e2e8f0;">
          <button onclick="MSPanel.showMore()"
            style="background:#038047;color:#fff;border:none;padding:8px 24px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;box-shadow:0 2px 4px rgba(3,128,71,.2);"
            onmouseover="this.style.background='#026136';this.style.transform='translateY(-1px)';"
            onmouseout="this.style.background='#038047';this.style.transform='none';">
            Muat Lebih Banyak
          </button>
        </div>`);
    }
  }

  function showMore() {
    const list = _$('msPanelList');
    if (!list || !_allItems.length) return;
    const meta  = MSCfg.platMeta[_curPlat] || { label: _curPlat || 'All', color: '#4361EE' };
    const items = _curSent === 'all' ? _allItems : _allItems.filter(i => _ns(i) === _curSent);
    _render(list, items, _curPlat, meta.color, true, true);
  }

  return { open, close, closeByOverlay, showPlatPicker, openPlatform, openSentiment, filterSent, showMore };
})();

/* ══ DETAIL SUB-PANEL ══ */
const MSDetail = {
  openEncoded(enc,plat){ try{this.open(JSON.parse(decodeURIComponent(enc)),plat);}catch(e){} },
  open(item,platform){
    const panel=document.getElementById('msDetailPanel'),body=document.getElementById('msDpBody'),title=document.getElementById('msDpTitle');if(!panel||!body)return;
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
    if(platform==='yt'){
        let ytId=''; if(url){ const m=url.match(/(?:v=|youtu\.be\/|embed\/|shorts\/|\/vi\/)([a-zA-Z0-9_-]{11})/); if(m) ytId=m[1]; }
        if(!ytId && item.video_id) ytId = item.video_id;
        if(!ytId && item.yt_id) ytId = item.yt_id;
        if(!ytId && item.id){ const m=String(item.id).match(/(?:yt-|youtube-)?([A-Za-z0-9_-]{11})$/); if(m) ytId=m[1]; }
        const imgUrl = item.thumbnail || item.image_url || item.picture || (ytId ? `https://img.youtube.com/vi/${ytId}/hqdefault.jpg` : '');
        if(ytId){
            mediaHtml=`<div class="do-dp2-media" style="cursor:pointer;position:relative;background:#000;aspect-ratio:16/9;border-radius:var(--radius);overflow:hidden;" onclick="openVidModal('yt', '${ytId}')">
                <img src="${esc(imgUrl)}" style="width:100%;height:100%;object-fit:cover;opacity:0.6;" onerror="this.src='https://img.youtube.com/vi/${ytId}/hqdefault.jpg'">
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;"><i class="ph ph-youtube-logo" style="font-size:48px;color:#ff0000;filter:drop-shadow(0 0 10px rgba(0,0,0,.5));"></i></div>
            </div>`;
        } else if(imgUrl) {
            mediaHtml=`<div class="do-dp2-media"><img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:220px;object-fit:cover;display:block;border-radius:var(--radius);"></div>`;
        }
    }
    else if(platform==='tiktok'){
        let videoId=''; if(url){ const m=url.match(/\/video\/(\d+)/); if(m) videoId=m[1]; } if(!videoId&&item.id){ const m=String(item.id).match(/(\d{10,})/); if(m) videoId=m[1]; }
        const imgUrl=item.image_url||item.thumbnail||item.media_url||item.picture||'';
        if(videoId) {
            mediaHtml=`<div class="do-dp2-media" style="cursor:pointer;position:relative;background:#111827;aspect-ratio:9/16;max-width:280px;margin:0 auto;border-radius:var(--radius);overflow:hidden;" onclick="openVidModal('tiktok', '${videoId}')">
                ${imgUrl ? `<img src="${esc(imgUrl)}" style="width:100%;height:100%;object-fit:cover;opacity:0.5;" onerror="this.style.display='none'">` : ''}
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#fff;"><i class="ph ph-tiktok-logo" style="font-size:42px;color:#fff;filter:drop-shadow(0 2px 8px rgba(0,0,0,.4));margin-bottom:8px;"></i><span style="font-size:12px;font-weight:700;background:rgba(255,255,255,.2);padding:4px 10px;border-radius:20px;backdrop-filter:blur(4px);">Play Video</span></div>
            </div>`;
        } else if(imgUrl) {
            mediaHtml=`<div class="do-dp2-media"><img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:220px;object-fit:cover;display:block;border-radius:var(--radius);"></div>`;
        }
    }
    else {
        const imgUrl=item.image_url||item.thumbnail||item.media_url||item.picture||'';
        if(imgUrl) mediaHtml=`<div class="do-dp2-media"><img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:220px;object-fit:cover;display:block;border-radius:var(--radius);"></div>`;
    }
    const statsMap={twit:[['Retweet',item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0],['Quote',item.num_quote||0]],fb:[['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],ig:[['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],yt:[['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0]],tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],doc:[['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]]};
    const stats=statsMap[platform]||[];
    const statsHtml=stats.some(s=>parseInt(s[1])>0)?`<div class="do-dp2-stats">${stats.map(([l,v])=>`<div class="do-dp2-stat"><div class="do-dp2-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="do-dp2-stat-lbl">${l}</div></div>`).join('')}</div>`:'';
    const handleDisp=handle&&handle.replace('@','').toLowerCase()!==name.toLowerCase().slice(0,handle.replace('@','').length)?(handle.startsWith('@')?handle:'@'+handle):'';
    
    let sourceUrl=item.url||item.link||item.post_url||item.article_url||item.source_url||item.permalink||item.news_url||item.article_link||item.full_url||item.web_url||item.source||item.original_url||'';
    
    if(!sourceUrl){
        if(platform==='twit'){ 
            const ao=(()=>{ if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return{};} })(); 
            const scr=(item.author_scr_name||item.screen_name||ao?.scr_name||ao?.username||'').replace(/^@/,''); 
            const sid=item.sub_id||item.tweet_id||item.id_str||item.id||''; 
            if(scr&&sid) sourceUrl=`https://twitter.com/${scr}/status/${sid}`; else if(scr) sourceUrl=`https://twitter.com/${scr}`; 
        }
        else if(platform==='ig'){ 
            const sc=item.shortcode||item.code||item.media_id||''; 
            if(sc) sourceUrl=`https://www.instagram.com/p/${sc}/`; else if(item.username) sourceUrl=`https://www.instagram.com/${item.username}/`; 
        }
        else if(platform==='yt'){ 
            const yi=item.video_id||item.youtube_id||item.id||''; 
            if(yi) sourceUrl=`https://www.youtube.com/watch?v=${yi}`; else if(item.channel_id) sourceUrl=`https://www.youtube.com/channel/${item.channel_id}`; 
        }
        else if(platform==='tiktok'){ 
            const ti=item.video_id||item.aweme_id||item.id||''; 
            const ni=item.author_nickname||item.nickname||item.unique_id||item.author?.unique_id||''; 
            if(ti&&ni) sourceUrl=`https://www.tiktok.com/@${ni}/video/${ti}`; else if(ti) sourceUrl=`https://vm.tiktok.com/${ti}`; 
        }
        else if(platform==='fb'){ 
            const pi=item.post_id||item.story_id||item.id||''; 
            const pn=item.page_name||item.from_name||item.username||''; 
            if(pn&&pi) sourceUrl=`https://www.facebook.com/${pn}/posts/${pi}`; 
        }
    }

    let sourceBtnHtml = '';
    if (sourceUrl) {
        sourceBtnHtml = `<a href="${esc(sourceUrl)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out"></i> Lihat ${meta.label} Asli</a>`;
    } else {
        sourceBtnHtml = `
            <div style="margin-top:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px 12px;font-size:11px;color:#991b1b;display:flex;align-items:flex-start;gap:7px;line-height:1.4;">
                <i class="ph ph-warning-circle" style="font-size:15px;flex-shrink:0;"></i>
                <span>Link artikel spesifik tidak tersedia dari sumber data.</span>
            </div>`;
    }

    body.innerHTML=`<div class="do-dp2-avatar-row"><div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div><div><div class="do-dp2-name">${esc(name)}</div>${handleDisp?`<div class="do-dp2-handle">${esc(handleDisp)}</div>`:''}<span class="do-dp2-plat-badge" style="background:${meta.color}22;color:${meta.color};">${meta.label}</span></div></div>${dtFmt?`<div class="do-dp2-meta">${dtFmt}</div>`:''}<div class="do-dp2-sent ${SBGS[sent]}">${SLBL[sent]}</div>${mediaHtml}${content?`<div class="do-dp2-content">${esc(content)}</div>`:''}${statsHtml}${sourceBtnHtml}`;
    panel.classList.add('show');
  },
  close(){ const panel=document.getElementById('msDetailPanel');if(!panel)return;panel.classList.remove('show');panel.querySelectorAll('iframe').forEach(f=>{try{f.src=f.src;}catch(e){}});}
};

/* ══ TREND TOGGLE ══ */
const MSTrendToggle = {
  _mode:'daily', _trendData:null, _weekOffset:0, _datePickerOverride:false,
  set(mode){ if(this._mode===mode)return;this._mode=mode;document.querySelectorAll('#trendToggle .ms-toggle-btn').forEach(b=>b.classList.toggle('active',b.dataset.mode===mode));const sub=document.getElementById('trendSubtitle');if(sub)sub.textContent=mode==='monthly'?'Total mentions per bulan':this._datePickerOverride?`${MSCfg.sd} – ${MSCfg.ed}`:'8 hari terakhir';const wng=document.getElementById('weekNavGroup');if(wng)wng.style.display=mode==='daily'&&!this._datePickerOverride?'flex':'none';if(mode==='daily'){this._weekOffset=0;this._trendData=null;}if(this._trendData)this._render(this._trendData);else loadTrend(); },
  setData(rawData){ this._trendData=rawData; },
  navWeek(dir){ const next=this._weekOffset+dir;if(next<0)return;this._weekOffset=next;this._trendData=null;loadTrend(); },
  _weekLabel(){ return this._weekOffset===0?'Minggu Ini':`Week -${this._weekOffset}`; },
  copyCSV(){if(!this._trendData){alert('Data belum tersedia');return;}const lines=this._buildCSV(this._trendData,this._mode);MSCsvModal.show('Trend Mentions — '+(this._mode==='monthly'?'Bulanan':'Harian'),lines);},
  _buildCSV(raw,mode){
    const platOrder=['doc','twitter','facebook','instagram','youtube','tiktok'];const platMeta={doc:'Online News',twitter:'Twitter',facebook:'Facebook',instagram:'Instagram',youtube:'YouTube',tiktok:'TikTok'};
    if(mode==='monthly'){const monthMap={};raw.forEach(p=>(p.data||[]).forEach(d=>{const m=d.date.slice(0,7);if(!monthMap[m])monthMap[m]={};monthMap[m][p.key]=(monthMap[m][p.key]||0)+d.count;}));const months=Object.keys(monthMap).sort();const lines=[];months.forEach(m=>platOrder.forEach(k=>{const val=monthMap[m][k]||0;if(val>0)lines.push(`${lines.length};${platMeta[k]||k};${val};${m}`);}));return lines;}
    else{const dSet=new Set();raw.forEach(p=>(p.data||[]).forEach(d=>dSet.add(d.date)));const allDates=Array.from(dSet).sort();const lines=[];allDates.forEach(date=>raw.forEach(p=>{const pt=(p.data||[]).find(x=>x.date===date);if(pt&&pt.count>0)lines.push(`${lines.length};${platMeta[p.key]||p.key};${pt.count};${date}`);}));return lines;}
  },
  _render(raw){
    const platMetaFull={doc:{label:'Online News',color:'#0284c7'},twitter:{label:'Twitter',color:'#1d9bf0'},facebook:{label:'Facebook',color:'#1877f2'},instagram:{label:'Instagram',color:'#e1306c'},youtube:{label:'YouTube',color:'#ff0000'},tiktok:{label:'TikTok',color:'#111827'}};
    const platOrder=['doc','twitter','facebook','instagram','youtube','tiktok'];
    const keyMap={'Online News':'doc','Twitter':'twit','Facebook':'fb','Instagram':'ig','YouTube':'yt','TikTok':'tiktok'};
    if(this._mode==='monthly'){
      const monthMap={};raw.forEach(p=>(p.data||[]).forEach(d=>{const m=d.date.slice(0,7);if(!monthMap[m])monthMap[m]={};monthMap[m][p.key]=(monthMap[m][p.key]||0)+d.count;}));
      const months=Object.keys(monthMap).sort();const xLabels=months.map(m=>{const dt=new Date(m+'-01T00:00:00');return dt.toLocaleString('id-ID',{month:'short',year:'numeric'});});
      document.getElementById('trendBadge').textContent=xLabels[0]+'…'+xLabels[xLabels.length-1];const sub=document.getElementById('trendSubtitle');if(sub)sub.textContent='Total mentions per bulan';
      const seriesArr=platOrder.map(key=>{const meta=platMetaFull[key];const vals=months.map(m=>monthMap[m]?.[key]||0);if(!vals.some(v=>v>0))return null;return{name:meta.label,data:vals};}).filter(Boolean);
      const colorsArr=seriesArr.map(s=>{const k=Object.keys(platMetaFull).find(k=>platMetaFull[k].label===s.name);return platMetaFull[k]?.color||'#94a3b8';});
      _destroyApx('trend');const el=document.getElementById('chTrend');if(!el)return;
      const onPointClick = (seriesIndex, cx, cy) => {
        const sName = seriesArr[seriesIndex]?.name;
        const k = keyMap[sName];
        if(k) MSPanel.open(k, cx, cy);
      };
      APX.trend=new ApexCharts(el,{
        chart:{
          type:'bar', height:340, fontFamily:'inherit', background:'transparent',
          toolbar:{show:false}, stacked:true,
          animations:{enabled:true,easing:'linear',dynamicAnimation:{speed:1000}},
          events:{
            click: function(event, chartContext, config) {
              if(config.seriesIndex >= 0 && typeof onPointClick === 'function') {
                onPointClick(config.seriesIndex, event.clientX, event.clientY);
              }
            },
            legendClick: function(chartContext, seriesIndex) {
              if(typeof onPointClick === 'function') { onPointClick(seriesIndex, window.innerWidth - 520, 200); }
            },
            mouseMove: function(event, chartContext) {
              if(chartContext?.el) chartContext.el.style.cursor = 'pointer';
            },
            mouseLeave: function(event, chartContext) {
              if(chartContext?.el) chartContext.el.style.cursor = 'default';
            }
          }
        },
        series:seriesArr, colors:colorsArr,
        plotOptions:{bar:{columnWidth:'60%',borderRadius:3,borderRadiusApplication:'end'}},
        fill:{opacity:1}, stroke:{show:false},
        xaxis:{categories:xLabels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontFamily:'inherit',fontSize:'11px',fontWeight:600,colors:'#94A3B8'}}},
        yaxis:{labels:{formatter:v=>numK(v),style:{fontFamily:'inherit',fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false},axisTicks:{show:false}},
        grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}}},
        legend:{position:'bottom',horizontalAlign:'left',fontFamily:'inherit',fontSize:'11px',fontWeight:'600',labels:{colors:'#94A3B8'},markers:{width:9,height:9,radius:50},itemMargin:{horizontal:14,vertical:4}},
        tooltip:{shared:true,intersect:false,style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>numFmt(v)+' mentions'}},
        dataLabels:{enabled:false},
        states:{hover:{filter:{type:'lighten',value:0.1}},active:{filter:{type:'darken',value:0.35}}}
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
    navigator.clipboard.writeText(this._content).then(()=>{btn.innerHTML='<i class="ph ph-copy" style="font-size:12px;"></i> Tersalin!';btn.classList.add('copied');setTimeout(()=>{btn.innerHTML='<i class="ph ph-copy" style="font-size:12px;"></i> Copy CSV data';btn.classList.remove('copied');},2000);}).catch(()=>{const ta=document.createElement('textarea');ta.value=this._content;ta.style.cssText='position:fixed;opacity:0;';document.body.appendChild(ta);ta.select();document.execCommand('copy');document.body.removeChild(ta);btn.innerHTML='<i class="ph ph-check" style="font-size:12px;"></i> Tersalin!';btn.classList.add('copied');setTimeout(()=>{btn.innerHTML='<i class="ph ph-copy" style="font-size:12px;"></i> Copy CSV data';btn.classList.remove('copied');},2000);});
  }
};

/* ══════════════════════════════════════════════════════
   EXPORT MODULE
══════════════════════════════════════════════════════ */
const MSExport = (() => {
    let _toastTimer = null;
    const PID = '{{ $projectId ?? "0" }}';
 
    function _toast(msg, type='default', duration=3200) {
        const t=document.getElementById('exportToast'), m=document.getElementById('exportToastMsg'), ico=document.getElementById('exportToastIcon');
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

    function _resizeAllCharts() {
        Object.values(MSCharts._i).forEach(c=>{try{if(!c.isDisposed())c.resize();}catch(e){}});
        if(APX.trend)  try{APX.trend.updateOptions({});}catch(e){}
        if(APX.article)try{APX.article.updateOptions({});}catch(e){}
    }
 
    function _drawPdfHeader(pdf, pW, margin, label) {
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica','bold');
        pdf.text('SMADIMENT — '+(label||'Media Statistic'), margin, 7.5);
        const now = new Date().toLocaleDateString('id-ID',{ day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
        pdf.setFontSize(7); pdf.setFont('helvetica','normal');
        pdf.text('Generated: '+now, pW-margin, 7.5, { align:'right' });
    }
 
    function _splitCanvas(pdf, canvas, margin, pW, pH, label, numPages) {
        const usableW  = pW - margin * 2;
        const sliceH   = Math.ceil(canvas.height / numPages);
        for (let page = 0; page < numPages; page++) {
            if (page > 0) pdf.addPage();
            _drawPdfHeader(pdf, pW, margin, label);
            const srcY     = page * sliceH;
            const srcSlice = Math.min(sliceH, canvas.height - srcY);
            if (srcSlice <= 0) break;
            const ratio = usableW / canvas.width;
            const dstH  = srcSlice * ratio;
            const slice = document.createElement('canvas');
            slice.width  = canvas.width;
            slice.height = Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcSlice, 0, 0, canvas.width, srcSlice);
            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, usableW, dstH);
            pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
            pdf.text(`Halaman ${page + 1} / ${numPages}`, pW / 2, pH - 3, { align:'center' });
        }
    }
 
    function _fitCanvas(pdf, canvas, margin, pW, pH, label) {
        _drawPdfHeader(pdf, pW, margin, label);
        const usableW = pW - margin * 2;
        const usableH = pH - margin * 2 - 18;
        const ratio   = Math.min(usableW / canvas.width, usableH / canvas.height);
        const dstW    = canvas.width  * ratio;
        const dstH    = canvas.height * ratio;
        const x       = margin + (usableW - dstW) / 2;
        const y       = 14 + (usableH - dstH) / 2;
        pdf.addImage(canvas.toDataURL('image/png'), 'PNG', x, y, dstW, dstH);
        pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
        pdf.text('Halaman 1 / 1', pW / 2, pH - 3, { align:'center' });
    }
 
    async function _captureArea(areaEl) {
        window.scrollTo({ top:0 });
        await new Promise(r => setTimeout(r, 350));
        _resizeAllCharts();
        _freeze(); await new Promise(r=>setTimeout(r,400));
        try {
            return await html2canvas(areaEl, {
                scale:           2,
                useCORS:         true,
                allowTaint:      false,
                backgroundColor: '#f1f5f9',
                logging:         false,
                removeContainer: true,
                windowHeight:    areaEl.scrollHeight,
                height:          areaEl.scrollHeight,
                ignoreElements:  el => el.hasAttribute('data-html2canvas-ignore'),
            });
        } finally { _unfreeze(); }
    }
 
    async function _captureCard(areaId) {
        const area = document.getElementById(areaId);
        if(!area) throw new Error('Area #'+areaId+' tidak ditemukan');
        _resizeAllCharts();
        await new Promise(r => setTimeout(r, 220));
        _freeze(); await new Promise(r=>setTimeout(r,400));
        try {
            return await html2canvas(area, {
                scale:           2,
                useCORS:         true,
                allowTaint:      false,
                backgroundColor: '#ffffff',
                logging:         false,
                removeContainer: true,
                ignoreElements:  el => el.hasAttribute('data-html2canvas-ignore'),
            });
        } finally { _unfreeze(); }
    }
 
    const _cardLabels = {
        'bar'           : 'Total Mention by Media Platform',
        'sov-mass'      : 'Share of Voice — Mass vs Social',
        'sov-plat'      : 'Share of Voice by Platform',
        'bar-race'      : 'Mentions per Platform',
        'trend'         : 'Trend Mentions',
        'article-trend' : 'Trend Articles — Online News',
        'weekday'       : 'Mentions by Weekday',
        'hour'          : 'Mentions by Hour',
    };
 
    function _filename(cardKey) {
        const slug = {
            'bar':'mention-by-platform', 'sov-mass':'sov-mass-social', 'sov-plat':'sov-by-platform',
            'bar-race':'bar-race', 'trend':'trend-mentions', 'article-trend':'trend-articles',
            'weekday':'mentions-weekday', 'hour':'mentions-hour',
        };
        const stamp = new Date().toISOString().slice(0,10).replace(/-/g,'');
        return `media_stat_${slug[cardKey]||cardKey}_${PID}_${stamp}`;
    }
 
    async function runCard(areaId, cardKey, type, btn) {
        if(!window.html2canvas)           { _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia','error'); return; }
        _btnState(btn, true);
        _toast(type==='pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);
        try {
            const canvas = await _captureCard(areaId);
            const fname  = _filename(cardKey);
            const label  = _cardLabels[cardKey] || cardKey;
            if (type === 'image') {
                const link = document.createElement('a');
                link.download = fname+'.png'; link.href = canvas.toDataURL('image/png'); link.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const landscape = canvas.width > canvas.height;
                const pdf = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit:'mm', format:'a4' });
                const pW  = pdf.internal.pageSize.getWidth();
                const pH  = pdf.internal.pageSize.getHeight();
                _fitCanvas(pdf, canvas, 10, pW, pH, label);
                pdf.save(fname+'.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[MSExport.runCard]', err);
            _toast('Export gagal: '+err.message, 'error');
        } finally {
            _btnState(btn, false);
        }
    }
 
    async function run(type, btn) {
        if(!window.html2canvas)           { _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia','error'); return; }
        const btnPdf = document.getElementById('pageExportPdfBtn');
        const btnImg = document.getElementById('pageExportImgBtn');
        _btnState(btnPdf, true); _btnState(btnImg, true);
        _toast(type==='pdf' ? 'Menyiapkan PDF 2 halaman…' : 'Mengambil gambar halaman…', 'default', 99999);
        try {
            const area   = document.getElementById('pageExportArea');
            const canvas = await _captureArea(area);
            const stamp  = new Date().toISOString().slice(0,10).replace(/-/g,'');
            if (type === 'image') {
                const link = document.createElement('a');
                link.download = `media_statistic_${PID}_${stamp}.png`;
                link.href = canvas.toDataURL('image/png'); link.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
                const pW  = pdf.internal.pageSize.getWidth();
                const pH  = pdf.internal.pageSize.getHeight();
                _splitCanvas(pdf, canvas, 10, pW, pH, 'Media Statistic', 2);
                pdf.save(`media_statistic_${PID}_${stamp}.pdf`);
                _toast('PDF 2 halaman berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[MSExport]', err);
            _toast('Export gagal: '+err.message, 'error');
        } finally {
            _btnState(btnPdf, false); _btnState(btnImg, false);
        }
    }
 
    return { run, runCard };
})();

/* ══ INIT ══ */
const MSPage = {
  _syncDateFilter(){
    const today=new Date();today.setHours(0,0,0,0);const ed=new Date(MSCfg.ed+'T00:00:00');const sd=new Date(MSCfg.sd+'T00:00:00');const diff=Math.round((ed-sd)/86400000)+1;
    MSTrendToggle._datePickerOverride=!(ed.getTime()===today.getTime()&&diff<=8);MSTrendToggle._weekOffset=0;
  },
  reload(){
    MSCharts.disposeAll();_destroyApx('trend');_destroyApx('article');MSTab.reset();this._syncDateFilter();
    ['skBar','skTrend','skArticleTrend','skWeekday','skHour','skBarRace'].forEach(showSk);
    loadMentionByPlatform();
    const activeTab=document.querySelector('.ms-tab-panel.active')?.id;
    if(activeTab==='panelTrend'){MSTab._loaded.trend=true;loadTrend();loadArticleTrend();}
    else if(activeTab==='panelPola'){MSTab._loaded.pola=true;loadWeekHour();}
  },
  init(){ this._syncDateFilter();loadMentionByPlatform();MSTab._loaded.trend=true;loadTrend();loadArticleTrend(); }
};

document.addEventListener('mousedown',e=>{const pp=document.getElementById('msPlatPicker');if(pp?.classList.contains('show')&&!pp.contains(e.target))pp.classList.remove('show');});
document.addEventListener('keydown', e=>{ if(e.key==='Escape'){ const vm=document.getElementById('vidViewModal'); if(vm&&vm.style.display!=='none'){closeVidModal();return;} MSPanel.close(); } });
document.addEventListener('DOMContentLoaded',()=>MSPage.init());

/* ══ MEDIA VIEWER MODAL ══ */
function openVidModal(type, id) {
    const m = document.getElementById('vidViewModal');
    const w = document.getElementById('vidViewWrap');
    if(!m || !w || !id) return;
    let url = '';
    if(type === 'yt') {
        url = `https://www.youtube.com/embed/${id}?autoplay=1&rel=0`;
        w.style.aspectRatio = '16/9';
        w.style.maxWidth = '100%';
    } else if(type === 'tiktok') {
        url = `https://www.tiktok.com/embed/v2/${id}`;
        w.style.aspectRatio = '9/16';
        w.style.maxWidth = '400px';
        w.style.margin = '0 auto';
    }
    w.innerHTML = `<iframe src="${url}" style="width:100%;height:100%;border:none;border-radius:12px;display:block;" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>`;
    m.style.display = 'flex';
    requestAnimationFrame(() => {
        m.style.opacity = '1';
        m.querySelector('.video-modal-content').style.transform = 'scale(1)';
    });
}
function closeVidModal() {
    const m = document.getElementById('vidViewModal');
    const w = document.getElementById('vidViewWrap');
    if(!m) return;
    m.style.opacity = '0';
    m.querySelector('.video-modal-content').style.transform = 'scale(0.95)';
    setTimeout(() => { m.style.display = 'none'; if(w) w.innerHTML = ''; }, 200);
}
</script>
@endsection