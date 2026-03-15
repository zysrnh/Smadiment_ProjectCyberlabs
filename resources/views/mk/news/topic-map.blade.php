@extends('mk.layouts.app')

@section('title', 'News Topic Map - SMADIMENT')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════
   DESIGN TOKENS
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
@keyframes fadeUp        { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer       { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin          { to{transform:rotate(360deg)} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1}    to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn     { from{opacity:0} to{opacity:1} }
@keyframes overlayOut    { from{opacity:1} to{opacity:0} }
@keyframes ntmFI         { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:none} }
@keyframes kpiShimmer    { 0%{left:-100%} 100%{left:150%} }
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0deg)} 30%{transform:scale(1.25) rotate(-10deg)} 60%{transform:scale(1.1) rotate(6deg)} }

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
    padding:56px 20px; gap:12px; color:var(--slate-400); font-size:12px; font-weight:600;
}

/* ══ KPI Cards ══ */
.kpi-icon-bg {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0;
}
.kpi-card-hover {
    will-change:transform,box-shadow;
    transition:transform .25s cubic-bezier(.34,1.56,.64,1) !important,
               box-shadow .25s ease !important, filter .25s ease !important;
    cursor:default; position:relative !important; overflow:hidden !important;
}
.kpi-card-hover::before {
    content:''; position:absolute; top:0; bottom:0; left:-100%; width:60%;
    background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);
    pointer-events:none; z-index:1; transition:none;
}
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025) !important; box-shadow:0 20px 40px rgba(0,0,0,.25) !important; filter:brightness(1.07) !important; }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background:rgba(255,255,255,.35) !important; transition:background .2s ease !important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important; display:inline-block !important; }
.kpi-card-hover:active { transform:translateY(-2px) scale(1.01) !important; transition-duration:.08s !important; }

/* ══ Tabs ══ */
.ntm-tabs {
    display:flex; gap:2px;
    background:var(--slate-100); border:1px solid var(--slate-200);
    border-radius:var(--radius-sm); padding:2px; margin-bottom:16px;
    width:fit-content;
}
.ntm-tab-btn {
    display:flex; align-items:center; justify-content:center; gap:6px;
    padding:7px 16px; border-radius:4px; border:none; background:transparent;
    font-size:12px; font-weight:600; color:var(--slate-500);
    cursor:pointer; transition:background .13s, color .13s; white-space:nowrap;
}
.ntm-tab-btn:hover { background:#fff; color:var(--slate-800); }
.ntm-tab-btn.active { background:#fff; color:var(--primary); box-shadow:0 1px 4px rgba(0,0,0,.08); }
.ntm-tab-panel { display:none; }
.ntm-tab-panel.active { display:block; }

/* ══ Toggle group ══ */
.ntm-toggle-group {
    display:flex; background:var(--slate-50); border-radius:var(--radius-sm);
    padding:2px; gap:2px; border:1px solid var(--slate-200);
}
.ntm-toggle-btn {
    display:flex; align-items:center; gap:4px;
    padding:4px 10px; border-radius:3px; border:none; background:transparent;
    font-size:11px; font-weight:600; color:var(--slate-500);
    cursor:pointer; transition:background .12s, color .12s;
}
.ntm-toggle-btn:hover  { background:#fff; color:var(--slate-800); }
.ntm-toggle-btn.active { background:#fff; color:var(--primary); box-shadow:0 1px 3px rgba(0,0,0,.07); }

/* ══ Refresh btn ══ */
.ntm-rlbtn {
    width:30px; height:30px; border-radius:var(--radius-sm);
    border:1px solid var(--slate-200); background:#fff;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all .18s; color:var(--slate-400);
}
.ntm-rlbtn:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-lt); }
.ntm-rlbtn svg { width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2.5; }
.ntm-rlbtn.spin svg { animation:spin .7s linear infinite; }

/* ══ Headline toggle ══ */
.ntm-hl-tog {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 10px; border:1px solid var(--slate-200); border-radius:3px;
    font-size:11px; font-weight:600; color:var(--primary); background:#fff;
    cursor:pointer; transition:all .12s;
}
.ntm-hl-tog:hover { background:var(--primary-lt); border-color:var(--primary); }
.ntm-hl-tog.off { color:var(--slate-500); background:var(--slate-50); border-color:var(--slate-200); }

/* ══ Treemap ══ */
#ntmTreemap { position:relative; width:100%; overflow:hidden; }
.ntm-tile {
    position:absolute; overflow:hidden; cursor:pointer;
    box-sizing:border-box; transition:filter .15s, transform .15s;
    border:1px solid rgba(255,255,255,.08);
}
.ntm-tile:hover { filter:brightness(1.18); z-index:5; transform:scale(1.003); }
.ntm-tile-in { padding:7px 9px; height:100%; display:flex; flex-direction:column; gap:2px; }
.ntm-tile-cat {
    font-size:9px; font-weight:800; text-transform:uppercase;
    letter-spacing:.6px; opacity:.75; white-space:nowrap;
    overflow:hidden; text-overflow:ellipsis; flex-shrink:0; color:rgba(255,255,255,.8);
}
.ntm-tile-hl {
    font-weight:700; line-height:1.25; flex:1;
    overflow:hidden; display:-webkit-box; -webkit-box-orient:vertical; color:#fff;
}

/* Dark palette */
.c0 {background:#7f1d1d} .c1 {background:#273B4A} .c2 {background:#92400e}
.c3 {background:#064e3b} .c4 {background:#1e3a5f} .c5 {background:#5b21b6}
.c6 {background:#9d174d} .c7 {background:#1e40af} .c8 {background:#374151}
.c9 {background:#4d7c0f} .c10{background:#0e7490} .c11{background:#6d28d9}
.c12{background:#b45309} .c13{background:#166534} .c14{background:#991b1b}
.c15{background:#1d4ed8} .c16{background:#7e22ce} .c17{background:#a16207}
.c18{background:#065f46} .c19{background:#0f172a}

/* ══ List view ══ */
.ntm-li {
    display:flex; align-items:center; gap:12px;
    padding:11px 16px; border-bottom:1px solid var(--slate-100);
    cursor:pointer; transition:background .12s;
}
.ntm-li:last-child { border-bottom:none; }
.ntm-li:hover { background:var(--slate-50); }
.ntm-li-rnk {
    width:26px; height:26px; border-radius:50%;
    background:var(--slate-100); border:1px solid var(--slate-200);
    display:flex; align-items:center; justify-content:center;
    font-size:9px; font-weight:800; color:var(--slate-400); flex-shrink:0;
}
.ntm-li-rnk.g { background:linear-gradient(135deg,#ffd700,#F59E0B); color:#7c5900; border-color:#ffd700; }
.ntm-li-rnk.s { background:linear-gradient(135deg,#c0c0c0,#9ca3af); color:#3d3d3d; border-color:#c0c0c0; }
.ntm-li-rnk.b { background:linear-gradient(135deg,#cd7f32,#b06820); color:#fff;    border-color:#cd7f32; }
.ntm-li-dot  { width:10px; height:10px; border-radius:3px; flex-shrink:0; }
.ntm-li-info { flex:1; min-width:0; }
.ntm-li-topic { font-size:13px; font-weight:700; color:var(--slate-900); margin-bottom:2px; }
.ntm-li-bar  { height:4px; background:var(--slate-100); border-radius:10px; overflow:hidden; width:120px; flex-shrink:0; }
.ntm-li-fill { height:100%; background:linear-gradient(90deg,var(--primary),#04995a); border-radius:10px; transition:width .8s cubic-bezier(.4,0,.2,1); }
.ntm-li-cnt  { font-size:14px; font-weight:700; color:var(--primary); text-align:right; min-width:36px; }
.ntm-li-lbl  { font-size:9px; color:var(--slate-400); text-align:right; font-weight:600; }

/* ══ Bubble ══ */
.ntm-bub-card-inner { height:580px; }
.ntm-bub-card-inner svg { width:100%; height:100%; }

/* ══ Empty ══ */
.ntm-empty-state {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:64px 24px; gap:12px; color:var(--slate-400);
}
.ntm-empty-state svg { width:48px; height:48px; stroke:var(--slate-300); fill:none; stroke-width:1.5; }
.ntm-empty-state h4  { font-size:14px; font-weight:700; color:var(--slate-500); margin:0; }
.ntm-empty-state p   { font-size:12px; color:var(--slate-400); margin:0; text-align:center; }

/* ══ Slide Panel ══ */
.do-panel-overlay {
    position:fixed; inset:0; z-index:9000;
    background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none;
}
.do-panel-overlay.show   { display:block; animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }
.do-panel {
    position:fixed; top:0; right:0; bottom:0; z-index:9001;
    width:560px; max-width:100vw; background:#fff;
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

/* Panel 3-col layout */
.ntm-panel-body { flex:1; display:flex; overflow:hidden; min-height:0; }
.ntm-p-pub {
    width:190px; flex-shrink:0; border-right:1px solid var(--slate-200);
    overflow-y:auto; display:flex; flex-direction:column;
}
.ntm-p-pub::-webkit-scrollbar { width:3px; }
.ntm-p-pub::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.ntm-p-pub-hdr {
    padding:8px 12px; font-size:10px; font-weight:700; color:var(--slate-400);
    text-transform:uppercase; letter-spacing:.5px;
    border-bottom:1px solid var(--slate-200); background:var(--slate-50);
    position:sticky; top:0; z-index:1; flex-shrink:0;
}
.ntm-pub-row {
    display:flex; align-items:center; padding:8px 12px;
    border-bottom:1px solid var(--slate-50); cursor:pointer;
    transition:background .1s; gap:7px;
}
.ntm-pub-row:hover { background:#f0fdf4; }
.ntm-pub-row.act  { background:#dcfce7; border-left:3px solid var(--primary); padding-left:9px; }
.ntm-pub-row.act .ntm-pub-name { color:var(--primary); font-weight:700; }
.ntm-pub-name { font-size:11px; font-weight:500; color:var(--slate-700); flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.ntm-pub-docs { font-size:9px; font-weight:700; color:#fff; background:var(--primary); padding:1px 6px; border-radius:9px; flex-shrink:0; }
.ntm-pub-arr  { color:var(--slate-300); font-size:12px; flex-shrink:0; }
.ntm-pub-row.act .ntm-pub-arr { color:var(--primary); }
.ntm-p-kw {
    width:220px; flex-shrink:0; border-right:1px solid var(--slate-200);
    overflow-y:auto; display:flex; flex-direction:column;
}
.ntm-p-kw::-webkit-scrollbar { width:3px; }
.ntm-p-kw::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.ntm-p-kw-hdr {
    padding:8px 12px; font-size:10px; font-weight:700; color:var(--slate-400);
    text-transform:uppercase; letter-spacing:.5px;
    border-bottom:1px solid var(--slate-200); background:var(--slate-50);
    position:sticky; top:0; z-index:1; flex-shrink:0;
}
.ntm-kw-tbl { width:100%; border-collapse:collapse; }
.ntm-kw-tbl thead th {
    padding:7px 10px; font-size:10px; font-weight:700; color:var(--slate-400);
    text-align:left; border-bottom:2px solid var(--slate-200);
    background:var(--slate-50); position:sticky; top:0;
}
.ntm-kw-tbl thead th:last-child { text-align:right; }
.ntm-kw-tbl tbody tr:hover { background:var(--slate-50); }
.ntm-kw-tbl tbody tr { border-bottom:1px solid var(--slate-50); }
.ntm-kw-no { width:28px; padding:6px 4px 6px 10px; font-size:10px; color:var(--slate-400); font-weight:600; }
.ntm-kw-w  { padding:6px 6px; font-size:11px; font-weight:500; color:var(--slate-700); }
.ntm-kw-f  { padding:6px 10px 6px 4px; font-size:11px; font-weight:700; color:var(--slate-800); text-align:right; }
.ntm-p-art { flex:1; overflow-y:auto; }
.ntm-p-art::-webkit-scrollbar { width:3px; }
.ntm-p-art::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.ntm-p-art-hdr {
    padding:8px 14px; font-size:10px; font-weight:700; color:var(--slate-400);
    text-transform:uppercase; letter-spacing:.5px;
    border-bottom:1px solid var(--slate-200); background:var(--slate-50);
    position:sticky; top:0; z-index:1;
}
.ntm-ac {
    padding:12px 14px; border-bottom:1px solid var(--slate-50);
    transition:background .1s; display:flex; gap:10px;
}
.ntm-ac:hover { background:var(--slate-50); }
.ntm-ac-body  { flex:1; min-width:0; }
.ntm-ac-src   { font-size:10px; font-weight:800; color:var(--primary); text-transform:uppercase; margin-bottom:2px; }
.ntm-ac-date  { font-size:10px; color:var(--slate-400); margin-bottom:3px; }
.ntm-ac-title {
    font-size:12px; font-weight:700; color:var(--amber);
    text-decoration:none; line-height:1.4; display:block; margin-bottom:4px;
}
.ntm-ac-title:hover { color:#92400e; text-decoration:underline; }
.ntm-ac-snippet { font-size:11px; color:var(--slate-400); line-height:1.55; }
.ntm-ac-thumb {
    width:64px; height:48px; border-radius:var(--radius-sm);
    object-fit:cover; flex-shrink:0; background:var(--slate-100);
}

/* ══ Tooltip ══ */
.ntm-tip {
    position:fixed; background:var(--slate-800); color:#fff;
    padding:7px 12px; border-radius:var(--radius-sm);
    font-size:11px; font-weight:500; pointer-events:none;
    opacity:0; transition:opacity .12s; z-index:99999;
    max-width:260px; white-space:normal;
    box-shadow:0 6px 20px rgba(0,0,0,.25);
}
.ntm-tip.on { opacity:1; }
.ntm-tip b  { color:#4ade80; }

/* ══ Metric chips ══ */
.ntm-metric { display:inline-flex; align-items:center; gap:3px; padding:2px 6px; border-radius:3px; font-size:10px; font-weight:700; background:var(--slate-100); color:var(--slate-500); }
.ntm-metric--primary { background:var(--primary-lt); color:var(--primary); }

/* ══ Source badge ══ */
.ntm-src-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:2px 9px; border-radius:3px;
    font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px;
    background:var(--primary-lt); color:var(--primary);
    border:1px solid rgba(3,128,71,.2);
}

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
.page-export-bar-left {
    display:flex; align-items:center; gap:8px;
    font-size:12px; font-weight:700; color:var(--slate-600);
}
.page-export-bar-left i { font-size:15px; color:var(--primary); }
.page-export-bar-right  { display:flex; gap:8px; }
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
.page-export-btn .export-spinner {
    width:13px; height:13px; border:2px solid currentColor;
    border-top-color:transparent; border-radius:50%;
    animation:spin .65s linear infinite; display:none;
}
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
.card-exp-btn .export-spinner {
    width:11px; height:11px; border:2px solid currentColor;
    border-top-color:transparent; border-radius:50%;
    animation:spin .65s linear infinite; display:none;
}
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

/* ══ Date Picker Modal ══ */
.ntm-dp-modal {
    position:fixed; inset:0; z-index:10000;
    display:none; align-items:center; justify-content:center;
    background:rgba(15,23,42,.5); backdrop-filter:blur(8px);
}
.ntm-dp-modal.show { display:flex; }
.ntm-dp-overlay   { position:absolute; inset:0; cursor:pointer; }
.ntm-dp-container {
    position:relative; z-index:1;
    background:#fff; border-radius:12px;
    box-shadow:0 25px 50px rgba(0,0,0,.3);
    display:flex; max-width:860px; width:90%; max-height:90vh;
    animation:fadeUp .3s ease-out;
}
.ntm-dp-sidebar {
    width:160px; background:var(--slate-50);
    border-right:1px solid var(--slate-200);
    padding:12px 8px; border-radius:12px 0 0 12px;
    display:flex; flex-direction:column; gap:2px; flex-shrink:0;
}
.ntm-dp-preset {
    padding:8px 12px; background:transparent; border:none;
    border-radius:var(--radius-sm); font-family:inherit;
    font-size:12px; font-weight:500; color:var(--slate-600);
    text-align:left; cursor:pointer; transition:all .14s;
}
.ntm-dp-preset:hover  { background:#fff; color:var(--primary); }
.ntm-dp-preset.active { background:var(--primary); color:#fff; }
.ntm-dp-content {
    flex:1; padding:18px 22px;
    display:flex; flex-direction:column; overflow:hidden;
}
.ntm-dp-cals-wrap { display:flex; align-items:flex-start; gap:6px; flex:1; }
.ntm-dp-cals { display:flex; gap:20px; flex:1; }
.ntm-dp-cal  { flex:1; }
.ntm-dp-nav {
    width:30px; height:30px; border-radius:var(--radius-sm);
    background:var(--slate-50); border:1px solid var(--slate-200);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all .14s; flex-shrink:0; margin-top:32px;
}
.ntm-dp-nav:hover { background:var(--primary); border-color:var(--primary); color:#fff; }
.ntm-dp-nav svg  { width:16px; height:16px; }
.ntm-cal-month   { font-size:13px; font-weight:700; color:var(--slate-800); margin-bottom:12px; text-align:center; }
.ntm-cal-wdays   { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; margin-bottom:4px; }
.ntm-cal-wday    { text-align:center; font-size:9px; font-weight:700; color:var(--slate-400); padding:5px 0; }
.ntm-cal-days    { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; }
.ntm-cal-day {
    aspect-ratio:1; display:flex; align-items:center; justify-content:center;
    font-size:11px; font-weight:500; border-radius:var(--radius-sm);
    cursor:pointer; transition:all .12s; color:var(--slate-700);
    background:transparent; border:none; padding:0; font-family:inherit;
}
.ntm-cal-day:hover:not(.oth):not(.dis) { background:var(--slate-100); }
.ntm-cal-day.oth { color:var(--slate-300); cursor:default; }
.ntm-cal-day.dis { color:var(--slate-200); cursor:not-allowed; }
.ntm-cal-day.tod { border:2px solid var(--primary); }
.ntm-cal-day.sel { background:var(--primary); color:#fff; }
.ntm-cal-day.inr { background:var(--primary-lt); color:var(--primary); }
.ntm-dp-display {
    padding:10px 14px; background:var(--slate-50); border-radius:var(--radius-sm);
    text-align:center; margin:14px 0; border:1px solid var(--slate-200);
}
.ntm-dp-display span { font-size:13px; font-weight:600; color:var(--slate-800); }
.ntm-dp-footer { display:flex; gap:8px; justify-content:flex-end; }
.ntm-dp-cancel {
    padding:8px 20px; border-radius:var(--radius-sm); font-family:inherit;
    font-size:12px; font-weight:600; cursor:pointer; transition:all .14s;
    background:var(--slate-100); color:var(--slate-700); border:none;
}
.ntm-dp-cancel:hover { background:var(--slate-200); }
.ntm-dp-apply {
    padding:8px 20px; border-radius:var(--radius-sm); font-family:inherit;
    font-size:12px; font-weight:600; cursor:pointer; transition:all .14s;
    background:linear-gradient(135deg,var(--primary),#026738);
    color:#fff; border:none; box-shadow:0 4px 12px rgba(3,128,71,.2);
}
.ntm-dp-apply:hover { filter:brightness(1.1); }

@media(max-width:768px) {
    .do-panel { width:100vw; }
    .ntm-dp-container { flex-direction:column; width:96%; }
    .ntm-dp-sidebar { width:100%; flex-direction:row; overflow-x:auto; border-right:none; border-bottom:1px solid var(--slate-200); border-radius:12px 12px 0 0; }
    .ntm-dp-preset { white-space:nowrap; }
    .ntm-dp-cals { flex-direction:column; }
    .ntm-p-pub, .ntm-p-kw { display:none; }
    .ntm-li-bar { display:none; }
}
</style>
@endsection

@section('page-title', 'News Topic Map')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate   = $endDate   ?? request()->get('end_date',   now()->format('Y-m-d'));
@endphp

<script>
    const NTM_PID = {{ $projectId ? (int)$projectId : 'null' }};
    const NTM_SD  = '{{ $startDate }}';
    const NTM_ED  = '{{ $endDate }}';
</script>

{{-- Filter datepicker --}}
@include('mk.layouts.partials.filter-datepicker')

@if(!$projectId)
<div class="alert alert-warning" style="border-radius:var(--radius);font-size:13px;">
    <i class="ph ph-warning me-2"></i>No project selected. Please choose a project from the sidebar.
</div>
@else

{{-- ════ PAGE EXPORT AREA WRAPPER ════ --}}
<div id="ntmExportArea">

{{-- ══ KPI Cards ══ --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#4680ff;animation:fadeUp .38s ease-out both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Topics</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiTopics">
                            <span class="sk-block" style="width:70px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopicsSub">
                            <i class="ph ph-chart-line-up me-1"></i>Loading…
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-squares-four"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#10B981;animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Articles</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiArts">
                            <span class="sk-block" style="width:70px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiArtsSub">
                            <i class="ph ph-newspaper me-1"></i>Loading…
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
        <div class="card h-100 text-white kpi-card-hover" style="background:#F59E0B;animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Top Topic</p>
                        <h3 class="mb-0 text-white f-w-300 f-16" id="kpiTop" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <span class="sk-block" style="width:100px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopSub">
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
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 text-white kpi-card-hover" style="background:#8B5CF6;animation:fadeUp .38s ease-out .15s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Publishers Found</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiPubs">
                            <span class="sk-block" style="width:70px;height:24px;display:inline-block;"></span>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPubsSub">
                            <i class="ph ph-globe me-1"></i>Loading…
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-globe"></i></div>
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
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Active Tab</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="ntmExportPdfBtn"
                onclick="NTMExport.run('pdf', this)" title="Export sebagai PDF">
            <i class="ph ph-file-pdf export-icon"></i>
            <span class="export-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img" id="ntmExportImgBtn"
                onclick="NTMExport.run('image', this)" title="Export sebagai PNG">
            <i class="ph ph-image export-icon"></i>
            <span class="export-spinner"></span>
        </button>
    </div>
</div>

{{-- ══ Tabs ══ --}}
<div class="ntm-tabs" data-html2canvas-ignore="true">
    <button class="ntm-tab-btn active" id="tab-map"    onclick="NTMTab.show('map')">
        <i class="ph ph-squares-four"></i> Topic Map
    </button>
    <button class="ntm-tab-btn" id="tab-list" onclick="NTMTab.show('list')">
        <i class="ph ph-list-numbers"></i> Ranking List
    </button>
    <button class="ntm-tab-btn" id="tab-bubble" onclick="NTMTab.show('bubble')">
        <i class="ph ph-circles-three"></i> Bubble View
    </button>
</div>

{{-- ══ TAB: Map ══ --}}
<div class="ntm-tab-panel active" id="panel-map">
    <div class="card" style="animation:fadeUp .38s ease-out .18s both;">
        <div id="card-ntm-map">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded">
                        <i class="ph ph-squares-four f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">News Topic Map</h6>
                        <small class="text-muted" id="ntmMapMeta">Visual map of the most-discussed topics</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="ntm-src-badge"><i class="ph ph-newspaper me-1"></i>Online News (Ind)</span>
                    <button class="ntm-hl-tog" id="ntmHlTog" onclick="toggleHL()" title="Show/Hide Headline">
                        <i class="ph ph-eye" style="font-size:11px;"></i>
                        Headline: <span id="ntmHlLabel">On</span>
                    </button>
                    <input type="checkbox" id="ntmHlChk" checked style="display:none;" onchange="ntmRender()">
                    <button class="ntm-rlbtn" id="ntmRl" onclick="ntmLoad()" title="Refresh" data-html2canvas-ignore="true">
                        <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    </button>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf"
                                onclick="NTMExport.runCard('card-ntm-map','topic-map','pdf',this)"
                                title="Export PDF">
                            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                        </button>
                        <button class="card-exp-btn card-exp-btn-img"
                                onclick="NTMExport.runCard('card-ntm-map','topic-map','image',this)"
                                title="Export PNG">
                            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="ntmLoading" class="spinner-state"><div class="spin-ring"></div>Loading topic map…</div>
                <div id="ntmMapWrap" style="display:none;">
                    <div id="ntmTreemap" style="position:relative;width:100%;overflow:hidden;"></div>
                </div>
                <div id="ntmEmpty" class="ntm-empty-state" style="display:none;">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <h4>No topic data found</h4>
                    <p>Try a wider date range or check your project settings.</p>
                </div>
            </div>
        </div>{{-- /card-ntm-map --}}
    </div>
</div>

{{-- ══ TAB: List ══ --}}
<div class="ntm-tab-panel" id="panel-list">
    <div class="card" style="animation:fadeUp .38s ease-out .18s both;">
        <div id="card-ntm-list">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded">
                        <i class="ph ph-list-numbers f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Topic Ranking</h6>
                        <small class="text-muted">Sorted by article count</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light-primary text-primary" id="badgeList">—</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf"
                                onclick="NTMExport.runCard('card-ntm-list','topic-list','pdf',this)"
                                title="Export PDF">
                            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                        </button>
                        <button class="card-exp-btn card-exp-btn-img"
                                onclick="NTMExport.runCard('card-ntm-list','topic-list','image',this)"
                                title="Export PNG">
                            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div id="ntmListInner" class="p-0">
                <div class="spinner-state"><div class="spin-ring"></div>Loading…</div>
            </div>
        </div>{{-- /card-ntm-list --}}
    </div>
</div>

{{-- ══ TAB: Bubble ══ --}}
<div class="ntm-tab-panel" id="panel-bubble">
    <div class="card" style="animation:fadeUp .38s ease-out .18s both;">
        <div id="card-ntm-bubble">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded">
                        <i class="ph ph-circles-three f-18 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Bubble View</h6>
                        <small class="text-muted">Size = topic weight</small>
                    </div>
                </div>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf"
                            onclick="NTMExport.runCard('card-ntm-bubble','topic-bubble','pdf',this)"
                            title="Export PDF">
                        <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                    </button>
                    <button class="card-exp-btn card-exp-btn-img"
                            onclick="NTMExport.runCard('card-ntm-bubble','topic-bubble','image',this)"
                            title="Export PNG">
                        <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="ntm-bub-card-inner" id="ntmBubWrap" style="display:none;">
                    <svg id="ntmBubSvg"></svg>
                </div>
                <div class="spinner-state" id="ntmBubLoading"><div class="spin-ring"></div>Loading…</div>
            </div>
        </div>{{-- /card-ntm-bubble --}}
    </div>
</div>

</div>{{-- /ntmExportArea --}}

{{-- Export Toast --}}
<div class="export-toast" id="ntmExportToast">
    <i class="ph ph-check-circle" id="ntmExportToastIcon"></i>
    <span id="ntmExportToastMsg">Exporting…</span>
</div>

{{-- ══ Slide Panel ══ --}}
<div class="do-panel-overlay" id="ntmPanelOverlay" onclick="NTMPanel.close()"></div>
<div class="do-panel" id="ntmDetailPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="ntmPanelDot" style="background:var(--primary);"></div>
        <span class="do-panel-title" id="ntmPanelTitle">Topic Detail</span>
        <button class="do-panel-close" onclick="NTMPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta">
            <i class="ph ph-calendar" style="font-size:11px;"></i>
            <span id="ntmPanelMeta">{{ $startDate }} – {{ $endDate }}</span>
        </div>
        <span class="badge bg-light-primary text-primary" id="ntmPanelBadge">0 articles</span>
    </div>
    <div class="ntm-panel-body">
        <div class="ntm-p-pub">
            <div class="ntm-p-pub-hdr">Publishers</div>
            <div id="ntmPubList"></div>
        </div>
        <div class="ntm-p-kw">
            <div class="ntm-p-kw-hdr" id="ntmKwTitle">Keywords — All</div>
            <table class="ntm-kw-tbl">
                <thead><tr>
                    <th style="width:28px;">No</th>
                    <th>Keyword</th>
                    <th>Freq</th>
                </tr></thead>
                <tbody id="ntmKwBody"></tbody>
            </table>
        </div>
        <div class="ntm-p-art">
            <div class="ntm-p-art-hdr" id="ntmArtTitle">Articles</div>
            <div id="ntmArtList"></div>
        </div>
    </div>
</div>

{{-- Date Picker Modal --}}
<div class="ntm-dp-modal" id="ntmDpModal">
    <div class="ntm-dp-overlay" onclick="NTMDp.close()"></div>
    <div class="ntm-dp-container">
        <div class="ntm-dp-sidebar">
            <button class="ntm-dp-preset" data-p="today">Today</button>
            <button class="ntm-dp-preset" data-p="yesterday">Yesterday</button>
            <button class="ntm-dp-preset" data-p="last7">Last 7 Days</button>
            <button class="ntm-dp-preset" data-p="last14">Last 14 Days</button>
            <button class="ntm-dp-preset" data-p="last30">Last 30 Days</button>
            <button class="ntm-dp-preset" data-p="last60">Last 60 Days</button>
            <button class="ntm-dp-preset" data-p="last90">Last 90 Days</button>
            <button class="ntm-dp-preset" data-p="thismonth">This Month</button>
            <button class="ntm-dp-preset" data-p="lastmonth">Last Month</button>
            <button class="ntm-dp-preset active" data-p="custom">Custom Range</button>
        </div>
        <div class="ntm-dp-content">
            <div class="ntm-dp-cals-wrap">
                <button class="ntm-dp-nav" onclick="NTMDp.nav(-1)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <div class="ntm-dp-cals">
                    <div class="ntm-dp-cal" id="ntmDpCal1"></div>
                    <div class="ntm-dp-cal" id="ntmDpCal2"></div>
                </div>
                <button class="ntm-dp-nav" onclick="NTMDp.nav(1)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
            <div class="ntm-dp-display">
                <span id="ntmDpRangeText">{{ $startDate }} – {{ $endDate }}</span>
            </div>
            <div class="ntm-dp-footer">
                <button class="ntm-dp-cancel" onclick="NTMDp.close()">Batal</button>
                <button class="ntm-dp-apply"  onclick="NTMDp.apply()">Terapkan</button>
            </div>
        </div>
    </div>
</div>

@endif

<div class="ntm-tip" id="ntmTip"></div>
@endsection

@section('scripts')
{{-- Export dependencies --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>

<script>
(function(){
'use strict';

/* ══ Config ══ */
const CFG = {
    pid:      NTM_PID,
    s:        NTM_SD,
    e:        NTM_ED,
    topicApi: '/mk/api/topic-map',
    artApi:   '/mk/api/news/articles',
    pubApi:   '/mk/api/news/top-publisher',
};

/* ══ Palette ══ */
const PAL = [
    '#7f1d1d','#273B4A','#92400e','#064e3b','#1e3a5f',
    '#5b21b6','#9d174d','#1e40af','#374151','#4d7c0f',
    '#0e7490','#6d28d9','#b45309','#166534','#991b1b',
    '#1d4ed8','#7e22ce','#a16207','#065f46','#0f172a',
];

/* ══ State ══ */
let D  = { raw:[], fil:[], arts:[], allPubs:[], busy:false };
let DS = { topic:null, idx:0, arts:[], pubArts:{}, activePub:null };
window.D  = D;
window.DS = DS;

const $ = id => document.getElementById(id);
const tip = $('ntmTip');

const esc   = s => String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const trunc = (s,n) => s&&s.length>n?s.slice(0,n-1)+'…':s;
const numF  = n => parseInt(n||0).toLocaleString('id-ID');

function showTip(e,h){ tip.innerHTML=h; tip.classList.add('on'); moveTip(e); }
function moveTip(e){ tip.style.left=(e.clientX+14)+'px'; tip.style.top=(e.clientY-50)+'px'; }
function hideTip(){ tip.classList.remove('on'); }

/* ══ Tab system ══ */
const NTMTab = {
    _rendered: { map:false, list:false, bubble:false },
    show(tab) {
        ['map','list','bubble'].forEach(t => {
            $('tab-'+t)?.classList.toggle('active', t===tab);
            $('panel-'+t)?.classList.toggle('active', t===tab);
        });
        if(tab==='map'    && D.raw.length && !this._rendered.map)    { this._rendered.map=true;    doMap(D.fil); }
        if(tab==='list'   && D.raw.length)                           { renderList(D.fil); }
        if(tab==='bubble' && D.raw.length && !this._rendered.bubble) { this._rendered.bubble=true; doBub(D.fil); }
    },
    reset() { this._rendered={map:false,list:false,bubble:false}; }
};
window.NTMTab = NTMTab;

/* ══ KPI update ══ */
function updateKPIs() {
    const total    = D.raw.reduce((s,d)=>s+d.count,0);
    const top      = D.raw[0]||{};
    const pubCount = Object.keys(D.arts.reduce((m,a)=>{ const p=a.publisher||extractHostname(a.url||'')||'Unknown'; m[p]=1; return m; },{})).length;
    const el  = (id,v) => { const e=$(id); if(e) e.textContent=v; };
    const sub = (id,v) => { const e=$(id); if(e) e.innerHTML=v; };
    el('kpiTopics', D.raw.length);
    sub('kpiTopicsSub', `<i class="ph ph-chart-bar me-1"></i>Sum weight ${numF(total)}`);
    el('kpiArts', D.arts.length);
    sub('kpiArtsSub', `<i class="ph ph-newspaper me-1"></i>${NTM_SD} – ${NTM_ED}`);
    el('kpiTop', top.topic||'—');
    sub('kpiTopSub', top.count ? `<i class="ph ph-trophy me-1"></i>${numF(top.count)} articles / mentions` : '');
    el('kpiPubs', pubCount||D.allPubs.length);
    sub('kpiPubsSub', `<i class="ph ph-globe me-1"></i>Unique online publishers`);
    const bl=$('badgeList'); if(bl) bl.textContent=D.raw.length+' topics';
}

/* ══ Headline scoring with dedup ══ */
function findHeadlineUnique(topic, usedSet) {
    const q=topic.toLowerCase(), words=q.split(/\s+/).filter(w=>w.length>2);
    let best=null, bestScore=0;
    D.arts.forEach(a => {
        const titleRaw=a.title||'', title=titleRaw.toLowerCase();
        const content=(a.content||a.description||'').toLowerCase().slice(0,400);
        if(usedSet.has(titleRaw)) return;
        let score=0;
        if(title.includes(q)) score+=100;
        if(words.length>1&&words.every(w=>title.includes(w))) score+=60;
        words.forEach(w=>{ if(title.includes(w)) score+=15; });
        if(content.includes(q)) score+=20;
        words.forEach(w=>{ if(content.includes(w)) score+=3; });
        if(score>bestScore||(score===bestScore&&best&&titleRaw.length<best.title.length)){ bestScore=score; best=a; }
    });
    if(bestScore>=15&&best){ usedSet.add(best.title||''); return best.title||''; }
    return '';
}

/* ══ Fetch articles ══ */
async function fetchAllArticles() {
    const batchSize=500; let all=[], start=0, hasMore=true;
    while(hasMore) {
        try {
            const res=await fetch(`${CFG.artApi}?project_id=${CFG.pid}&media=doc&start_date=${CFG.s}&end_date=${CFG.e}&rows=${batchSize}&start=${start}`);
            const json=await res.json();
            const batch=json.data||(Array.isArray(json)?json:[]);
            if(!batch.length){ hasMore=false; break; }
            all=all.concat(batch);
            if(batch.length<batchSize) hasMore=false; else start+=batchSize;
            if(all.length>=5000) hasMore=false;
        } catch(e){ hasMore=false; }
    }
    return all;
}

function extractHostname(url) {
    try{ return new URL(url).hostname.replace('www.',''); }catch(e){ return ''; }
}

/* ══ LOAD ══ */
window.ntmLoad = async function() {
    if(D.busy||!CFG.pid) return; D.busy=true;
    const rl=$('ntmRl'); rl?.classList.add('spin');
    NTMTab.reset();
    $('ntmLoading').style.display='flex'; $('ntmMapWrap').style.display='none'; $('ntmEmpty').style.display='none';
    $('ntmListInner').innerHTML='<div class="spinner-state"><div class="spin-ring"></div>Loading…</div>';
    $('ntmBubLoading').style.display='flex'; $('ntmBubWrap').style.display='none';

    try {
        const [topicRes, pubRes, allArts] = await Promise.all([
            fetch(`${CFG.topicApi}?project_id=${CFG.pid}&media=doc&start_date=${CFG.s}&end_date=${CFG.e}`),
            fetch(`${CFG.pubApi}?project_id=${CFG.pid}&start_date=${CFG.s}&end_date=${CFG.e}&news_type=article`).catch(()=>null),
            fetchAllArticles(),
        ]);
        const topicJson = await topicRes.json();
        const pubJson   = pubRes ? await pubRes.json().catch(()=>null) : null;
        D.arts = allArts;

        if(pubJson?.success&&Array.isArray(pubJson.data)&&pubJson.data.length) {
            D.allPubs = pubJson.data;
        } else {
            const pm={};
            D.arts.forEach(a=>{ const p=a.publisher||extractHostname(a.url||'')||'Unknown'; pm[p]=(pm[p]||0)+1; });
            D.allPubs=Object.entries(pm).map(([domain,count],i)=>({rank:i+1,domain,count})).sort((a,b)=>b.count-a.count);
        }

        let raw={};
        if(topicJson.data&&!Array.isArray(topicJson.data)&&typeof topicJson.data==='object') raw=topicJson.data;
        else if(Array.isArray(topicJson.data)) topicJson.data.forEach(it=>{ raw[it.name||it.topic||'']={num_docs:it.weight||it.num_docs||1}; });
        else if(Array.isArray(topicJson)) topicJson.forEach(it=>{ raw[it.name||it.topic||'']={num_docs:it.weight||it.num_docs||1}; });
        else if(typeof topicJson==='object'&&!topicJson.success&&!topicJson.error) raw=topicJson;

        const usedHeadlines=new Set();
        const ranked=Object.entries(raw)
            .map(([topic,val])=>({ topic, count:typeof val==='object'?(val.num_docs||val.count||1):Number(val) }))
            .filter(d=>d.topic&&d.count>0)
            .sort((a,b)=>b.count-a.count);

        D.raw=ranked.map((d,i)=>({
            ...d,
            headline: findHeadlineUnique(d.topic, usedHeadlines),
            color: PAL[i%PAL.length],
        }));
        D.fil=[...D.raw];

        if(!D.raw.length){ $('ntmLoading').style.display='none'; $('ntmEmpty').style.display='flex'; return; }

        updateKPIs();
        ntmRender();
        if(D.raw[0]) buildDetail(D.raw[0], 0);

    } catch(err) {
        console.error('[NTM]', err);
        $('ntmLoading').style.display='none'; $('ntmEmpty').style.display='flex';
    } finally {
        D.busy=false; rl?.classList.remove('spin');
    }
};

window.ntmRender = function() {
    D.fil=[...D.raw];
    $('ntmLoading').style.display='none';
    $('ntmMapWrap').style.display='block';
    $('ntmEmpty').style.display='none';
    $('ntmMapMeta').textContent=`${D.raw.length} topics · ${CFG.s} to ${CFG.e}`;
    doMap(D.fil);
};

window.toggleHL = function() {
    const c=$('ntmHlChk'); if(!c) return;
    c.checked=!c.checked;
    const tog=$('ntmHlTog'), lbl=$('ntmHlLabel');
    tog?.classList.toggle('off',!c.checked);
    if(lbl) lbl.textContent=c.checked?'On':'Off';
    const i=tog?.querySelector('i');
    if(i){ i.className=c.checked?'ph ph-eye':'ph ph-eye-slash'; i.style.fontSize='11px'; }
    ntmRender();
};

/* ══ Treemap ══ */
function doMap(data) {
    const showHL=$('ntmHlChk')?.checked!==false;
    const wrap=$('ntmTreemap'); wrap.innerHTML='';
    const W=wrap.offsetWidth||900;
    const H=Math.min(680,Math.max(520,window.innerHeight*.7));
    wrap.style.height=H+'px';

    const root=d3.hierarchy({children:data}).sum(d=>d.count||1);
    d3.treemap().tile(d3.treemapBinary).size([W,H]).padding(2).round(true)(root);

    root.leaves().forEach((node,i)=>{
        const d=node.data, tW=node.x1-node.x0, tH=node.y1-node.y0;
        if(tW<40||tH<28) return;
        const tile=document.createElement('div');
        tile.className='ntm-tile ntm-fi';
        tile.style.cssText=`left:${node.x0}px;top:${node.y0}px;width:${tW}px;height:${tH}px;`+
            `animation-delay:${i*7}ms;background:${d.color};`;

        const catFs=Math.max(7,Math.min(10,tW/18));
        const hlFs =Math.max(10,Math.min(26,tW/8));
        const lines=Math.max(1,Math.floor((tH-catFs*2-14)/(hlFs*1.3)));

        let hlHtml='';
        if(tH>48){
            if(showHL&&d.headline){
                hlHtml=`<div class="ntm-tile-hl" style="font-size:${hlFs}px;-webkit-line-clamp:${lines};">${esc(d.headline)}</div>`;
            } else {
                const tf=Math.max(11,Math.min(24,tW/9));
                hlHtml=`<div class="ntm-tile-hl" style="font-size:${tf}px;-webkit-line-clamp:${lines};font-weight:800;">${esc(d.topic)}</div>`;
            }
        }

        tile.innerHTML=`<div class="ntm-tile-in">
            <div class="ntm-tile-cat" style="font-size:${catFs}px;">${esc(d.topic)}</div>
            ${hlHtml}
        </div>`;

        tile.addEventListener('mouseenter', e=>showTip(e,`<b>${esc(d.topic)}</b><br><small>${numF(d.count)} articles</small>`));
        tile.addEventListener('mousemove', moveTip);
        tile.addEventListener('mouseleave', hideTip);
        tile.addEventListener('click', ()=>{ hideTip(); openDetail(d,i); });
        wrap.appendChild(tile);
    });
}

/* ══ List view ══ */
function renderList(data) {
    const max=data[0]?.count||1;
    let h='';
    data.forEach((d,i)=>{
        const pct=Math.round(d.count/max*100);
        const rc=i===0?'g':i===1?'s':i===2?'b':'';
        h+=`<div class="ntm-li ntm-fi" style="animation-delay:${i*10}ms;" onclick="openDetail(D.fil[${i}],${i})">
            <div class="ntm-li-rnk ${rc}">${i+1}</div>
            <div class="ntm-li-dot" style="background:${d.color};border-radius:3px;"></div>
            <div class="ntm-li-info">
                <div class="ntm-li-topic">${esc(d.topic)}</div>
                <div class="ntm-li-bar"><div class="ntm-li-fill" style="width:0%" data-p="${pct}"></div></div>
            </div>
            <div>
                <div class="ntm-li-cnt">${numF(d.count)}</div>
                <div class="ntm-li-lbl">articles</div>
            </div>
        </div>`;
    });
    $('ntmListInner').innerHTML=h;
    requestAnimationFrame(()=>requestAnimationFrame(()=>{
        $('ntmListInner').querySelectorAll('.ntm-li-fill').forEach((b,i)=>{
            setTimeout(()=>{ b.style.width=b.dataset.p+'%'; },i*10+50);
        });
    }));
}

/* ══ Bubble view ══ */
function doBub(data) {
    $('ntmBubLoading').style.display='none';
    $('ntmBubWrap').style.display='block';
    const svgEl=$('ntmBubSvg'); svgEl.innerHTML='';
    const W=svgEl.parentElement.clientWidth||860, H=580;
    svgEl.setAttribute('viewBox',`0 0 ${W} ${H}`);
    const pack=d3.pack().size([W,H]).padding(5);
    const root=d3.hierarchy({children:data}).sum(d=>d.count||1).sort((a,b)=>b.value-a.value);
    pack(root);
    const svg=d3.select(svgEl), defs=svg.append('defs');

    function lighten(hex,a){ const n=parseInt(hex.replace('#',''),16); return `rgb(${Math.min(255,((n>>16)&0xff)+Math.round(255*a))},${Math.min(255,((n>>8)&0xff)+Math.round(255*a))},${Math.min(255,(n&0xff)+Math.round(255*a))})`; }

    root.leaves().forEach((node,i)=>{
        const gid=`bg${i}`, base=PAL[i%PAL.length];
        const gr=defs.append('radialGradient').attr('id',gid).attr('cx','35%').attr('cy','35%');
        gr.append('stop').attr('offset','0%').attr('stop-color',lighten(base,.28));
        gr.append('stop').attr('offset','100%').attr('stop-color',base);
        node._gid=gid; node._base=base;
    });

    const nodes=svg.selectAll('g').data(root.leaves()).enter().append('g')
        .attr('transform',d=>`translate(${d.x},${d.y})`).style('cursor','pointer');

    nodes.append('circle').attr('r',0)
        .attr('fill',d=>`url(#${d._gid})`).attr('stroke',d=>d._base).attr('stroke-width',1.5).attr('stroke-opacity',.4)
        .transition().duration(500).delay((_,i)=>i*12).ease(d3.easeBounceOut).attr('r',d=>d.r);

    nodes.filter(d=>d.r>20).append('text')
        .attr('text-anchor','middle').attr('dy',d=>d.r>36?'-0.3em':'0.35em')
        .attr('font-family','inherit').attr('font-weight','700').attr('fill','#fff')
        .attr('font-size',d=>Math.max(9,Math.min(14,d.r*.32))+'px')
        .text(d=>trunc(d.data.topic,Math.floor(d.r/5)))
        .style('opacity',0).transition().duration(300).delay((_,i)=>i*12+350).style('opacity',1);

    nodes.filter(d=>d.r>38).append('text')
        .attr('text-anchor','middle').attr('dy','1.1em')
        .attr('font-family','inherit').attr('font-weight','500').attr('fill','rgba(255,255,255,.7)')
        .attr('font-size',d=>Math.max(8,Math.min(11,d.r*.21))+'px')
        .text(d=>numF(d.data.count)+' articles')
        .style('opacity',0).transition().duration(300).delay((_,i)=>i*12+400).style('opacity',1);

    nodes
        .on('mouseenter',(e,d)=>{ showTip(e,`<b>${d.data.topic}</b><br><small>${numF(d.data.count)} articles</small>`); d3.select(e.currentTarget).select('circle').transition().duration(120).attr('stroke-opacity',.9).attr('stroke-width',2.5); })
        .on('mousemove',moveTip)
        .on('mouseleave',e=>{ hideTip(); d3.select(e.currentTarget).select('circle').transition().duration(120).attr('stroke-opacity',.4).attr('stroke-width',1.5); })
        .on('click',(e,d)=>openDetail(d.data, D.raw.indexOf(d.data)));
}

/* ══ Detail ══ */
function buildDetail(d, idx) {
    DS.topic=d; DS.idx=idx; DS.activePub=null;
    const q=d.topic.toLowerCase();
    DS.arts=D.arts.filter(a=>((a.title||'')+(a.content||'')+(a.description||'')).toLowerCase().includes(q));
    DS.pubArts={};
    DS.arts.forEach(a=>{ const p=a.publisher||extractHostname(a.url||'')||'Unknown'; if(!DS.pubArts[p]) DS.pubArts[p]=[]; DS.pubArts[p].push(a); });
    if(!Object.keys(DS.pubArts).length&&D.allPubs.length) D.allPubs.forEach(p=>{ DS.pubArts[p.domain]=[]; });
    renderPubPanel(); renderKwAll(); renderArts(DS.arts,'All Publishers');
}

function openDetail(d, idx) {
    if(!d) return;
    buildDetail(d, idx);
    $('ntmPanelDot').style.background=PAL[idx%PAL.length];
    $('ntmPanelTitle').textContent=d.topic;
    $('ntmPanelMeta').textContent=`${CFG.s} – ${CFG.e}`;
    $('ntmPanelBadge').textContent=DS.arts.length>0?`${DS.arts.length} articles`:`${d.count} mentions`;
    NTMPanel.open();
}
window.openDetail=openDetail;

/* ══ Panel ══ */
const NTMPanel = {
    open() {
        const ov=$('ntmPanelOverlay'), pn=$('ntmDetailPanel');
        ov.classList.remove('hiding'); pn.classList.remove('hiding');
        ov.classList.add('show'); pn.classList.add('show');
    },
    close() {
        const ov=$('ntmPanelOverlay'), pn=$('ntmDetailPanel');
        pn.classList.add('hiding'); ov.classList.add('hiding');
        setTimeout(()=>{ pn.classList.remove('show','hiding'); ov.classList.remove('show','hiding'); },240);
    }
};
window.NTMPanel=NTMPanel;

function renderPubPanel() {
    let pubs=Object.entries(DS.pubArts).filter(([,a])=>a.length>0).map(([domain,a])=>({domain,count:a.length})).sort((a,b)=>b.count-a.count);
    if(!pubs.length) pubs=D.allPubs.slice(0,30).map(p=>({domain:p.domain,count:p.count,isGlobal:true}));
    let h='';
    pubs.forEach(p=>{
        const isAct=DS.activePub===p.domain;
        h+=`<div class="ntm-pub-row${isAct?' act':''}" onclick="selectPub(${JSON.stringify(p.domain)})">
            <span class="ntm-pub-name" title="${esc(p.domain)}">${esc(p.domain)}</span>
            <span class="ntm-pub-docs">${p.count}</span>
            <span class="ntm-pub-arr">›</span>
        </div>`;
    });
    if(!h) h='<div style="padding:20px 12px;font-size:11px;color:var(--slate-400);text-align:center;">No publishers</div>';
    $('ntmPubList').innerHTML=h;
}

window.selectPub=function(domain){
    DS.activePub=domain;
    document.querySelectorAll('#ntmPubList .ntm-pub-row').forEach(el=>{
        el.classList.toggle('act', el.querySelector('.ntm-pub-name')?.getAttribute('title')===domain);
    });
    const arts=DS.pubArts[domain]||[];
    renderKwForPub(arts, domain);
    renderArts(arts, domain);
};

function renderKwAll() {
    $('ntmKwTitle').textContent='Keywords — All';
    if(!DS.arts.length){
        let h=''; D.raw.forEach((kw,i)=>{
            const ia=DS.topic&&kw.topic===DS.topic.topic;
            h+=`<tr style="${ia?'background:#f0fdf4;':''}"><td class="ntm-kw-no">${i+1}</td><td class="ntm-kw-w" style="${ia?'color:var(--primary);font-weight:700;':''}">${esc(kw.topic)}${ia?' ◀':''}</td><td class="ntm-kw-f" style="${ia?'color:var(--primary);':''}">${numF(kw.count)}</td></tr>`;
        });
        $('ntmKwBody').innerHTML=h||'<tr><td colspan="3" style="padding:20px;text-align:center;color:var(--slate-400);font-size:11px;">No keywords</td></tr>';
        return;
    }
    const kwFreq={};
    D.raw.forEach(t=>{
        const q=t.topic.toLowerCase(); let cnt=0;
        DS.arts.forEach(a=>{ const txt=((a.title||'')+(a.content||'')+(a.description||'')).toLowerCase(); let pos=0,found; while((found=txt.indexOf(q,pos))!==-1){cnt++;pos=found+q.length;} });
        if(cnt>0) kwFreq[t.topic]={freq:cnt,api:t.count};
    });
    const sorted=Object.entries(kwFreq).sort((a,b)=>b[1].freq-a[1].freq);
    let h='';
    sorted.forEach(([kw,val],i)=>{
        const ia=DS.topic&&kw===DS.topic.topic;
        h+=`<tr style="${ia?'background:#f0fdf4;':''}"><td class="ntm-kw-no">${i+1}</td><td class="ntm-kw-w" style="${ia?'color:var(--primary);font-weight:700;':''}">${esc(kw)}${ia?' ◀':''}</td><td class="ntm-kw-f" style="${ia?'color:var(--primary);':''}">${val.freq}</td></tr>`;
    });
    $('ntmKwBody').innerHTML=h||'<tr><td colspan="3" style="padding:20px;text-align:center;color:var(--slate-400);font-size:11px;">No keywords found</td></tr>';
    setTimeout(()=>{ $('ntmKwBody')?.querySelector('tr[style*="#f0fdf4"]')?.scrollIntoView({block:'center',behavior:'smooth'}); },100);
}

function renderKwForPub(arts, domain) {
    $('ntmKwTitle').textContent=`KW — ${domain.length>22?domain.slice(0,21)+'…':domain}`;
    if(!arts.length){ $('ntmKwBody').innerHTML='<tr><td colspan="3" style="padding:16px;text-align:center;color:var(--slate-400);font-size:11px;">No articles from this publisher</td></tr>'; return; }
    const kwFreq={};
    D.raw.forEach(t=>{ const q=t.topic.toLowerCase(); let cnt=0; arts.forEach(a=>{ const txt=((a.title||'')+(a.content||'')+(a.description||'')).toLowerCase(); let pos=0,found; while((found=txt.indexOf(q,pos))!==-1){cnt++;pos=found+q.length;} }); if(cnt>0) kwFreq[t.topic]=cnt; });
    const sorted=Object.entries(kwFreq).sort((a,b)=>b[1]-a[1]);
    let h=''; sorted.forEach(([kw,cnt],i)=>{ const ia=DS.topic&&kw===DS.topic.topic; h+=`<tr style="${ia?'background:#f0fdf4;':''}"><td class="ntm-kw-no">${i+1}</td><td class="ntm-kw-w" style="${ia?'color:var(--primary);font-weight:700;':''}">${esc(kw)}${ia?' ◀':''}</td><td class="ntm-kw-f" style="${ia?'color:var(--primary);':''}">${cnt}</td></tr>`; });
    $('ntmKwBody').innerHTML=h||'<tr><td colspan="3" style="padding:16px;text-align:center;color:var(--slate-400);font-size:11px;">No match</td></tr>';
}

function renderArts(arts, pubLabel) {
    $('ntmArtTitle').textContent=`Articles — ${pubLabel} (${arts.length})`;
    if(!arts.length){
        $('ntmArtList').innerHTML=`<div class="ntm-empty-state" style="padding:32px;"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><h4>No articles found</h4><p>No matching articles from this publisher</p></div>`;
        return;
    }
    let h=`<div style="padding:6px 14px;font-size:10px;color:var(--slate-400);border-bottom:1px solid var(--slate-50);">${arts.length} artikel</div>`;
    arts.forEach((a,i)=>{
        const src=a.publisher||extractHostname(a.url||'')||'Unknown';
        const url=a.url||a.link||'#', date=(a.date_created||'').split('T')[0];
        const snippet=(a.content||a.description||a.summary||'').replace(/<[^>]+>/g,'').trim().slice(0,200);
        const img=a.image||a.thumbnail||a.image_url||a.urlToImage||'';
        h+=`<div class="ntm-ac ntm-fi" style="animation-delay:${Math.min(i,25)*12}ms;">
            <div class="ntm-ac-body">
                <div class="ntm-ac-src">${esc(src.toUpperCase())}</div>
                ${date?`<div class="ntm-ac-date">${date}</div>`:''}
                <a class="ntm-ac-title" href="${esc(url)}" target="_blank" rel="noopener">${esc(a.title||'Untitled')}</a>
                ${snippet?`<div class="ntm-ac-snippet">…${esc(snippet)}${snippet.length>=200?'…':''}</div>`:''}
            </div>
            ${img?`<img class="ntm-ac-thumb" src="${esc(img)}" alt="" loading="lazy" onerror="this.style.display='none'">`:''}
        </div>`;
    });
    $('ntmArtList').innerHTML=h;
}

/* ══ Date Picker ══ */
const NTMDp=(()=>{
    let ds=null,de=null,m1=new Date(),m2=new Date(),pickStart=true;
    const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
    function init(){
        ds=CFG.s?new Date(CFG.s):(()=>{const d=new Date();d.setDate(d.getDate()-6);return d;})();
        de=CFG.e?new Date(CFG.e):new Date();
        m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);
        render();
        $('ntmDateBtn')?.addEventListener('click',open);
        document.querySelectorAll('.ntm-dp-preset').forEach(b=>b.addEventListener('click',onPreset));
        document.addEventListener('keydown',e=>{if(e.key==='Escape'&&$('ntmDpModal')?.classList.contains('show'))close();});
    }
    function open(){ $('ntmDpModal')?.classList.add('show'); render(); }
    function close(){ $('ntmDpModal')?.classList.remove('show'); }
    function apply(){
        CFG.s=fmt(ds); CFG.e=fmt(de);
        if($('ntmDateSpan')) $('ntmDateSpan').textContent=`${CFG.s} – ${CFG.e}`;
        close(); ntmLoad();
    }
    function nav(dir){ m1.setMonth(m1.getMonth()+dir); m2.setMonth(m2.getMonth()+dir); render(); }
    function onPreset(e){
        document.querySelectorAll('.ntm-dp-preset').forEach(b=>b.classList.remove('active'));
        e.target.classList.add('active');
        const today=new Date(); today.setHours(0,0,0,0);
        switch(e.target.dataset.p){
            case 'today':     ds=new Date(today);de=new Date(today);break;
            case 'yesterday': ds=new Date(today);ds.setDate(today.getDate()-1);de=new Date(ds);break;
            case 'last7':     de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-6);break;
            case 'last14':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-13);break;
            case 'last30':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-29);break;
            case 'last60':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-59);break;
            case 'last90':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-89);break;
            case 'thismonth': ds=new Date(today.getFullYear(),today.getMonth(),1);de=new Date(today);break;
            case 'lastmonth': ds=new Date(today.getFullYear(),today.getMonth()-1,1);de=new Date(today.getFullYear(),today.getMonth(),0);break;
        }
        if(e.target.dataset.p!=='custom'){m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);}
        updDisp(); render();
    }
    function render(){ renderCal('ntmDpCal1',m1); renderCal('ntmDpCal2',m2); updDisp(); }
    function renderCal(id,month){
        const el=$(id); if(!el) return;
        const y=month.getFullYear(),mn=month.getMonth(),first=new Date(y,mn,1),last=new Date(y,mn+1,0),prevL=new Date(y,mn,0);
        const today=new Date(); today.setHours(0,0,0,0);
        let h=`<div class="ntm-cal-month">${MN[mn]} ${y}</div><div class="ntm-cal-wdays">${WD.map(d=>`<div class="ntm-cal-wday">${d}</div>`).join('')}</div><div class="ntm-cal-days">`;
        for(let i=0;i<first.getDay();i++) h+=`<button type="button" class="ntm-cal-day oth" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
        for(let d=1;d<=last.getDate();d++){
            const date=new Date(y,mn,d); date.setHours(0,0,0,0);
            let cls='ntm-cal-day';
            if(sameD(date,today)) cls+=' tod';
            if(date>today) cls+=' dis';
            if(ds&&de){ if(sameD(date,ds)||sameD(date,de)) cls+=' sel'; else if(date>ds&&date<de) cls+=' inr'; }
            h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
        }
        const rem=last.getDay()===6?0:6-last.getDay();
        for(let i=1;i<=rem;i++) h+=`<button type="button" class="ntm-cal-day oth" disabled>${i}</button>`;
        h+='</div>'; el.innerHTML=h;
        el.querySelectorAll('.ntm-cal-day:not(.oth):not(.dis)').forEach(btn=>{
            btn.addEventListener('click',function(){
                const d=new Date(this.dataset.date); d.setHours(0,0,0,0);
                document.querySelectorAll('.ntm-dp-preset').forEach(b=>b.classList.remove('active'));
                document.querySelector('[data-p="custom"]').classList.add('active');
                if(pickStart||d<ds){ds=d;de=d;pickStart=false;}else{if(d>=ds)de=d;else{de=ds;ds=d;}pickStart=true;}
                updDisp(); render();
            });
        });
    }
    function updDisp(){ const el=$('ntmDpRangeText'); if(el&&ds&&de) el.textContent=fmt(ds)+' – '+fmt(de); }
    function fmt(d){ if(!d)return''; return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
    function sameD(a,b){ return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
    return {init,open,close,apply,nav};
})();
window.NTMDp=NTMDp;

/* ══ Resize ══ */
let rTimer;
window.addEventListener('resize',()=>{
    clearTimeout(rTimer);
    rTimer=setTimeout(()=>{ if(D.raw.length){ if($('panel-map').classList.contains('active')) doMap(D.fil); if($('panel-bubble').classList.contains('active')) doBub(D.fil); } },300);
});

/* ══ Boot ══ */
document.addEventListener('DOMContentLoaded',()=>{
    const urlP=new URLSearchParams(window.location.search);
    if(urlP.get('start_date')) CFG.s=urlP.get('start_date');
    if(urlP.get('end_date'))   CFG.e=urlP.get('end_date');
    if($('ntmDateSpan')) $('ntmDateSpan').textContent=`${CFG.s} – ${CFG.e}`;
    NTMDp.init();
    document.addEventListener('keydown',e=>{ if(e.key==='Escape') NTMPanel.close(); });
    if(CFG.pid) ntmLoad();
});

})();

/* ══════════════════════════════════════════════════════
   EXPORT MODULE — News Topic Map
══════════════════════════════════════════════════════ */
const NTMExport = (() => {
    let _toastTimer = null;

    function _toast(msg, type = 'default', duration = 3200) {
        const t = document.getElementById('ntmExportToast');
        const m = document.getElementById('ntmExportToastMsg');
        const ico = document.getElementById('ntmExportToastIcon');
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

    /* Detect active tab card id */
    function _activeCardId() {
        if (document.getElementById('panel-map')?.classList.contains('active'))    return 'card-ntm-map';
        if (document.getElementById('panel-list')?.classList.contains('active'))   return 'card-ntm-list';
        if (document.getElementById('panel-bubble')?.classList.contains('active')) return 'card-ntm-bubble';
        return 'card-ntm-map';
    }

    /* Full-page capture: KPI rows + active tab card */
    async function _capture() {
        const area = document.getElementById('ntmExportArea');
        if (!area) throw new Error('ntmExportArea tidak ditemukan');
        window.scrollTo({ top: 0 });
        await new Promise(r => setTimeout(r, 400));
        return html2canvas(area, {
            scale:           2,
            useCORS:         true,
            allowTaint:      false,
            backgroundColor: '#f1f5f9',
            logging:         false,
            removeContainer: true,
            windowWidth:     document.documentElement.scrollWidth,
            windowHeight:    area.scrollHeight,
            height:          area.scrollHeight,
            ignoreElements:  el =>
                el.hasAttribute('data-html2canvas-ignore') ||
                el.id === 'ntmExportPdfBtn' ||
                el.id === 'ntmExportImgBtn',
        });
    }

    /* Per-card capture */
    async function _captureCard(areaId) {
        const area = document.getElementById(areaId);
        if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');
        await new Promise(r => setTimeout(r, 280));
        return html2canvas(area, {
            scale:           2,
            useCORS:         true,
            allowTaint:      false,
            backgroundColor: '#ffffff',
            logging:         false,
            removeContainer: true,
            ignoreElements:  el => el.hasAttribute('data-html2canvas-ignore'),
        });
    }

    function _drawPdfHeader(pdf, pW, margin, label) {
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + (label || 'News Topic Map'), margin, 7.5);
        const now = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - margin, 7.5, { align: 'right' });
    }

    function _paginateCanvasToPdf(pdf, canvas, pW, pH, margin, headerH) {
        const imgW    = canvas.width, imgH = canvas.height;
        const usableW = pW - margin * 2;
        const usableH = pH - margin * 2 - headerH;
        const ratio   = usableW / imgW;
        const sliceH  = usableH / ratio;
        let srcY = 0, pageNum = 0;
        while (srcY < imgH) {
            if (pageNum > 0) pdf.addPage();
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

    async function _exportPdf() {
        const canvas = await _capture();
        const { jsPDF } = window.jspdf;
        const pdf    = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
        const pW     = pdf.internal.pageSize.getWidth();
        const pH     = pdf.internal.pageSize.getHeight();
        const margin = 10;
        _drawPdfHeader(pdf, pW, margin, 'News Topic Map');
        _paginateCanvasToPdf(pdf, canvas, pW, pH, margin, 11);
        const stamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        pdf.save(`news_topic_map_${NTM_PID}_${stamp}.pdf`);
    }

    async function _exportImage() {
        const canvas = await _capture();
        const stamp  = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        const link   = document.createElement('a');
        link.download = `news_topic_map_${NTM_PID}_${stamp}.png`;
        link.href     = canvas.toDataURL('image/png');
        link.click();
    }

    const _cardLabels = {
        'topic-map':    'News Topic Map',
        'topic-list':   'Topic Ranking List',
        'topic-bubble': 'Topic Bubble View',
    };

    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);

        try {
            const canvas = await _captureCard(areaId);
            const stamp  = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            const fname  = `ntm_${cardKey}_${NTM_PID}_${stamp}`;

            if (type === 'image') {
                const link    = document.createElement('a');
                link.download = fname + '.png';
                link.href     = canvas.toDataURL('image/png');
                link.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const imgW = canvas.width, imgH = canvas.height;
                const landscape = imgW > imgH;
                const pdf  = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit: 'mm', format: 'a4' });
                const pW   = pdf.internal.pageSize.getWidth();
                const pH   = pdf.internal.pageSize.getHeight();
                const margin = 10;
                _drawPdfHeader(pdf, pW, margin, _cardLabels[cardKey] || cardKey);
                _paginateCanvasToPdf(pdf, canvas, pW, pH, margin, 11);
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[NTMExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btn, false);
        }
    }

    async function run(type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

        const btnPdf = document.getElementById('ntmExportPdfBtn');
        const btnImg = document.getElementById('ntmExportImgBtn');
        _btnState(btnPdf, true); _btnState(btnImg, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);

        try {
            if (type === 'pdf') { await _exportPdf();   _toast('PDF berhasil diunduh!',    'success'); }
            else                { await _exportImage(); _toast('Gambar berhasil diunduh!', 'success'); }
        } catch(err) {
            console.error('[NTMExport]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btnPdf, false); _btnState(btnImg, false);
        }
    }

    return { run, runCard };
})();
</script>
@endsection