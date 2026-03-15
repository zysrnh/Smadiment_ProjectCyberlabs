@extends('mk.layouts.app')

@section('title', 'TikTok Overview - SMADIMENT')

@section('styles')
<style>
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

@keyframes fadeUp        { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer       { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin          { to{transform:rotate(360deg)} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1} to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn     { from{opacity:0} to{opacity:1} }
@keyframes overlayOut    { from{opacity:1} to{opacity:0} }
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0)} 30%{transform:scale(1.25) rotate(-10deg)} 60%{transform:scale(1.1) rotate(6deg)} }
@keyframes kpiShimmer    { 0%{left:-100%} 100%{left:150%} }
@keyframes ttDotPulse    { 0%,100%{box-shadow:0 0 0 3px rgba(3,128,71,.3)} 50%{box-shadow:0 0 0 6px transparent} }

.kpi-icon-bg { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0; }
.sk-block { border-radius:4px; background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; }
.spin-ring { width:26px; height:26px; border:2.5px solid var(--slate-100); border-top-color:var(--primary); border-radius:50%; animation:spin .65s linear infinite; }
.spinner-state { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:48px 20px; gap:12px; color:var(--slate-400); font-size:12px; font-weight:600; }

.kpi-card-hover { will-change:transform,box-shadow; cursor:default; position:relative!important; overflow:hidden!important; transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important; }
.kpi-card-hover::before { content:''; position:absolute; top:0; bottom:0; left:-100%; width:60%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent); pointer-events:none; z-index:1; }
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025)!important; box-shadow:0 20px 40px rgba(0,0,0,.25)!important; filter:brightness(1.07)!important; }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background:rgba(255,255,255,.35)!important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important; display:inline-block!important; }
.kpi-card-hover:active { transform:translateY(-2px) scale(1.01)!important; transition-duration:.08s!important; }

.chart-container { position:relative; }
.chart-loading { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; background:#fff; z-index:2; transition:opacity .3s; }
.chart-loading.hidden { opacity:0; pointer-events:none; }
.chart-loading span { font-size:11px; font-weight:600; color:var(--slate-400); }
.chart-empty { height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; color:var(--slate-400); font-size:12px; font-weight:600; }
.chart-empty i { font-size:34px; color:var(--slate-300); display:block; }

.tme-toggle-group { display:flex; background:var(--slate-50); border-radius:var(--radius-sm); padding:2px; gap:2px; border:1px solid var(--slate-200); }
.tme-toggle-btn { display:flex; align-items:center; gap:4px; padding:4px 10px; border-radius:3px; border:none; background:transparent; font-size:11px; font-weight:600; color:var(--slate-500); cursor:pointer; transition:background .12s,color .12s; }
.tme-toggle-btn:hover  { background:#fff; color:var(--slate-800); }
.tme-toggle-btn.active { background:#fff; color:var(--primary); box-shadow:0 1px 3px rgba(0,0,0,.07); }

.tme-tabs { display:flex; gap:2px; background:var(--slate-100); border:1px solid var(--slate-200); border-radius:var(--radius-sm); padding:2px; margin-bottom:16px; }
.tme-tab-btn { flex:1; display:flex; align-items:center; justify-content:center; gap:6px; padding:7px 14px; border-radius:4px; border:none; background:transparent; font-size:12px; font-weight:600; color:var(--slate-500); cursor:pointer; transition:background .13s,color .13s; white-space:nowrap; }
.tme-tab-btn:hover { background:#fff; color:var(--slate-800); }
.tme-tab-btn.active { background:#fff; color:var(--primary); box-shadow:0 1px 4px rgba(0,0,0,.08); }
.tme-tab-chip { display:inline-flex; align-items:center; justify-content:center; min-width:20px; height:16px; padding:0 5px; border-radius:3px; font-size:9px; font-weight:800; background:var(--primary-lt); color:var(--primary); }
.tme-tab-btn:not(.active) .tme-tab-chip { background:var(--slate-100); color:var(--slate-400); }
.tme-tab-panel { display:none; }
.tme-tab-panel.active { display:block; }

.ht-list { display:flex; flex-direction:column; }
.ht-item { display:flex; align-items:center; gap:12px; padding:10px 16px; border-bottom:1px solid var(--slate-100); cursor:pointer; transition:background .12s; }
.ht-item:last-child { border-bottom:none; }
.ht-item:hover { background:var(--slate-50); }
.ht-rank { width:24px; height:24px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; color:var(--slate-400); background:var(--slate-100); border:1px solid var(--slate-200); }
.ht-rank--1 { background:linear-gradient(135deg,#ffd700,#F59E0B); color:#7c5900; border-color:#ffd700; }
.ht-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af); color:#3d3d3d; border-color:#c0c0c0; }
.ht-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820); color:#fff; border-color:#cd7f32; }
.ht-name { flex:1; min-width:0; font-size:13px; font-weight:700; color:var(--primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ht-bar-wrap { flex:0 0 100px; height:6px; background:var(--slate-100); border-radius:99px; overflow:hidden; }
.ht-bar-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,var(--primary),rgba(3,128,71,.5)); transition:width .4s cubic-bezier(.4,0,.2,1); }
.ht-count { font-size:11px; font-weight:700; color:var(--slate-500); white-space:nowrap; flex-shrink:0; min-width:36px; text-align:right; }

.tme-post-list { display:flex; flex-direction:column; }
.tme-post { display:flex; align-items:flex-start; gap:12px; padding:12px 16px; border-bottom:1px solid var(--slate-100); transition:background .12s; cursor:pointer; }
.tme-post:last-child { border-bottom:none; }
.tme-post:hover { background:var(--slate-50); }
.tme-post-rank { width:22px; height:22px; border-radius:50%; background:var(--slate-100); border:1px solid var(--slate-200); display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:800; color:var(--slate-400); flex-shrink:0; margin-top:8px; }
.tme-post-rank--1 { background:linear-gradient(135deg,#ffd700,#F59E0B); color:#7c5900; border-color:#ffd700; }
.tme-post-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af); color:#3d3d3d; border-color:#c0c0c0; }
.tme-post-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820); color:#fff; border-color:#cd7f32; }
.tme-post-av { width:36px; height:36px; border-radius:50%; flex-shrink:0; color:#fff; font-weight:700; font-size:12px; display:flex; align-items:center; justify-content:center; border:1.5px solid var(--slate-200); overflow:hidden; }
.tme-post-av img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.tme-post-body { flex:1; min-width:0; }
.tme-post-author { font-size:12.5px; font-weight:700; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tme-post-date   { font-size:10px; color:var(--slate-400); margin-top:1px; margin-bottom:4px; }
.tme-post-text   { font-size:11.5px; color:var(--slate-500); line-height:1.55; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:6px; word-break:break-word; }
.tme-post-stats  { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
.tme-metric { display:inline-flex; align-items:center; gap:3px; padding:2px 6px; border-radius:3px; font-size:10px; font-weight:700; background:var(--slate-100); color:var(--slate-500); white-space:nowrap; }
.tme-metric--primary { background:var(--primary-lt); color:var(--primary); }
.tme-metric--amber   { background:rgba(245,158,11,.1); color:#92400e; }
.tme-metric--cyan    { background:rgba(6,182,212,.1);  color:#164e63; }
.tme-metric--red     { background:rgba(239,68,68,.1);  color:#991b1b; }
.tme-sent { display:inline-flex; align-items:center; padding:2px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.3px; }
.tme-sent--pos { background:#d1fae5; color:#065f46; }
.tme-sent--neg { background:#fee2e2; color:#991b1b; }
.tme-sent--neu { background:var(--slate-100); color:var(--slate-500); }
.tme-view-link { display:inline-flex; align-items:center; gap:3px; font-size:9.5px; font-weight:700; color:var(--primary); text-decoration:none; padding:2px 6px; border-radius:3px; background:var(--primary-lt); border:1px solid rgba(3,128,71,.2); transition:background .12s,color .12s; margin-left:auto; }
.tme-view-link:hover { background:var(--primary); color:#fff; }
.tme-post-thumb { width:80px; height:120px; border-radius:var(--radius-sm); flex-shrink:0; overflow:hidden; border:1.5px solid var(--slate-200); background:var(--slate-800); position:relative; align-self:center; box-shadow:var(--shadow-sm); }
.tme-post-thumb img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .2s; }
.tme-post:hover .tme-post-thumb img { transform:scale(1.06); }
.tme-post-thumb-ph { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:24px; background:linear-gradient(135deg,#273B4A,#374151); }
.tme-post-thumb-play { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.28); opacity:0; transition:opacity .2s; border-radius:inherit; }
.tme-post-thumb-play i { font-size:20px; color:#fff; }
.tme-post:hover .tme-post-thumb-play { opacity:1; }

.tme-pagination { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; border-top:1px solid var(--slate-100); flex-wrap:wrap; gap:8px; }
.tme-pag-info { font-size:11px; color:var(--slate-400); font-weight:500; }
.tme-pag-controls { display:flex; align-items:center; gap:3px; }
.tme-pag-btn { min-width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; padding:0 6px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; font-size:11px; font-weight:600; color:var(--slate-500); cursor:pointer; transition:all .12s; user-select:none; }
.tme-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary); color:var(--primary); background:var(--primary-lt); }
.tme-pag-btn.is-active { background:var(--primary); border-color:var(--primary); color:#fff; }
.tme-pag-btn:disabled { opacity:.35; cursor:not-allowed; }

.tme-rows-sel { padding:4px 9px; border:1px solid var(--slate-200); border-radius:var(--radius-sm); font-size:11px; font-weight:600; color:var(--slate-600); background:var(--slate-50); outline:none; cursor:pointer; transition:border-color .14s; }
.tme-rows-sel:focus { border-color:var(--primary); }

.donut-legend { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-top:10px; }
.donut-leg-item { display:flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:var(--slate-500); padding:3px 8px; background:var(--slate-50); border-radius:3px; border:1px solid var(--slate-200); cursor:pointer; transition:border-color .12s,background .12s,color .12s; }
.donut-leg-item:hover { border-color:var(--primary); background:var(--primary-lt); color:var(--primary); }
.donut-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

.do-panel-overlay { position:fixed; inset:0; z-index:9000; background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none; }
.do-panel-overlay.show   { display:block; animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }
.do-panel { position:fixed; top:0; right:0; bottom:0; z-index:9001; width:480px; max-width:100vw; background:#fff; display:none; flex-direction:column; border-left:1px solid var(--slate-200); box-shadow:-8px 0 40px rgba(15,23,42,.16); }
.do-panel.show   { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }
.do-panel-header { display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--slate-200); background:var(--slate-50); flex-shrink:0; }
.do-panel-dot   { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.do-panel-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-panel-close { width:28px; height:28px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); font-size:16px; transition:all .14s; flex-shrink:0; }
.do-panel-close:hover { background:var(--red); border-color:var(--red); color:#fff; }
.do-panel-actions { display:flex; align-items:center; gap:7px; padding:7px 12px; border-bottom:1px solid var(--slate-200); background:#fff; flex-shrink:0; }
.do-panel-meta { flex:1; font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px; display:flex; align-items:center; gap:5px; }
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
.do-panel-text   { font-size:11px; color:var(--slate-600); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
.do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--slate-400); flex-wrap:wrap; }
.do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.do-sent-badge--neu { background:var(--slate-100); color:var(--slate-500); }
.do-panel-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:12px; color:var(--slate-400); font-size:13px; font-weight:600; }
.do-panel-spinner { width:28px; height:28px; border:2.5px solid var(--slate-100); border-top-color:var(--primary); border-radius:50%; animation:spin .65s linear infinite; }

.do-detail-panel { position:absolute; inset:0; background:#fff; z-index:5; display:none; flex-direction:column; animation:slideInRight .2s cubic-bezier(.4,0,.2,1); }
.do-detail-panel.show { display:flex; }
.do-dp2-header { display:flex; align-items:center; gap:8px; padding:12px 14px; background:var(--slate-50); border-bottom:1px solid var(--slate-200); flex-shrink:0; }
.do-dp2-back { width:28px; height:28px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); transition:all .13s; font-size:14px; }
.do-dp2-back:hover { background:var(--primary-lt); color:var(--primary); border-color:var(--primary); }
.do-dp2-title  { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-dp2-body   { overflow-y:auto; flex:1; padding:16px; }
.do-dp2-body::-webkit-scrollbar { width:4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.do-dp2-avatar-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.do-dp2-avatar-lg  { width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700; font-size:16px; display:flex; align-items:center; justify-content:center; border:2px solid var(--slate-200); overflow:hidden; flex-shrink:0; }
.do-dp2-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.do-dp2-name       { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-handle     { font-size:11px; color:var(--slate-400); font-weight:500; }
.do-dp2-plat-badge { display:inline-block; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; margin-top:3px; }
.do-dp2-meta       { font-size:11px; color:var(--slate-400); font-weight:500; margin-bottom:10px; }
.do-dp2-sent       { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:3px; font-size:11px; font-weight:700; margin-bottom:10px; }
.do-dp2-sent--pos  { background:#dbeafe; color:#1d4ed8; }
.do-dp2-sent--neg  { background:#fee2e2; color:#991b1b; }
.do-dp2-sent--neu  { background:var(--slate-100); color:var(--slate-500); }
.do-dp2-content    { font-size:12px; color:var(--slate-700); line-height:1.7; margin-bottom:12px; background:var(--slate-50); border-radius:var(--radius-sm); padding:10px 12px; border:1px solid var(--slate-200); word-break:break-word; }
.do-dp2-media      { border-radius:var(--radius-sm); overflow:hidden; margin-bottom:10px; }
.do-dp2-media iframe { width:100%; height:480px; border:none; display:block; }
.do-dp2-stats      { display:grid; grid-template-columns:repeat(4,1fr); gap:6px; margin-bottom:10px; }
.do-dp2-stat       { background:var(--slate-50); border-radius:var(--radius-sm); padding:8px 10px; border:1px solid var(--slate-200); text-align:center; }
.do-dp2-stat-val   { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-stat-lbl   { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
.do-dp2-link { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 14px; background:var(--primary); color:#fff; border-radius:var(--radius-sm); font-size:12px; font-weight:700; text-decoration:none; transition:filter .14s; margin-top:4px; }
.do-dp2-link:hover { filter:brightness(1.1); color:#fff; }

.chart-clickable-hint { position:absolute; bottom:8px; right:10px; z-index:3; display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:3px; background:rgba(3,128,71,.1); color:var(--primary); font-size:10px; font-weight:700; pointer-events:none; opacity:.7; }
.kpi-card-hover h3 { font-size:1.5rem; }

/* ══════════════════════════════════════════════════════
   EXPORT STYLES
══════════════════════════════════════════════════════ */
.page-export-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; background:#fff; border:1px solid var(--slate-200); border-radius:var(--radius); padding:9px 14px; margin-bottom:20px; box-shadow:var(--shadow-sm); }
.page-export-bar-left { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--slate-600); }
.page-export-bar-left i { font-size:15px; color:var(--primary); }
.page-export-bar-right { display:flex; gap:8px; }

.page-export-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:var(--radius-sm); font-size:16px; cursor:pointer; transition:all .15s ease; border:1.5px solid transparent; font-family:inherit; }
.page-export-btn-pdf { background:#fff3f3; color:#dc2626; border-color:#fca5a5; }
.page-export-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.page-export-btn-img { background:var(--primary-lt); color:var(--primary); border-color:rgba(3,128,71,.3); }
.page-export-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.page-export-btn:disabled { opacity:.55; cursor:not-allowed; pointer-events:none; }
.page-export-btn .export-spinner { width:13px; height:13px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
.page-export-btn.exporting .export-spinner { display:inline-block; }
.page-export-btn.exporting .export-icon { display:none; }

.card-exp-btn { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:var(--radius-sm); font-size:14px; cursor:pointer; flex-shrink:0; transition:all .14s ease; border:1px solid transparent; font-family:inherit; background:transparent; }
.card-exp-btn-pdf { color:#dc2626; border-color:#fca5a5; background:#fff3f3; }
.card-exp-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.card-exp-btn-img { color:var(--primary); border-color:rgba(3,128,71,.3); background:var(--primary-lt); }
.card-exp-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
.card-exp-btn .export-spinner { width:11px; height:11px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
.card-exp-btn.exporting .export-spinner { display:inline-block; }
.card-exp-btn.exporting .export-icon { display:none; }

.export-toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px); background:var(--slate-900); color:#fff; border-radius:var(--radius); padding:10px 18px; font-size:12px; font-weight:600; box-shadow:var(--shadow-lg); z-index:99999; opacity:0; pointer-events:none; transition:opacity .22s ease,transform .22s ease; display:flex; align-items:center; gap:8px; white-space:nowrap; }
.export-toast.show    { opacity:1; transform:translateX(-50%) translateY(0); }
.export-toast.success { background:#065f46; }
.export-toast.error   { background:#991b1b; }

@media(max-width:640px) {
    .do-panel { width:100vw; }
    .tme-tabs { flex-wrap:wrap; }
    .tme-tab-btn { flex:unset; min-width:calc(50% - 4px); }
    .tme-post-thumb { display:none; }
}
</style>
@endsection

@section('page-title', 'TikTok Overview')

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

{{-- ════ PAGE EXPORT WRAPPER ════ --}}
<div id="pageExportArea">

{{-- ══ KPI Cards ══ --}}
<div class="row mb-3">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-primary text-white kpi-card-hover fade-up fade-up-d1">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Total Views</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiViews"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiViewsSub"><i class="ph ph-chart-line-up me-1"></i>Loading…</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-eye"></i></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-success text-white kpi-card-hover fade-up fade-up-d2">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Total Likes</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiLikes"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiLikesSub"><i class="ph ph-heart me-1"></i>Loading…</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-thumbs-up"></i></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-warning text-white kpi-card-hover fade-up fade-up-d3">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Total Comments</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiComments"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiCommentsSub"><i class="ph ph-chat-circle me-1"></i>Loading…</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chat-circle"></i></div></div>
            </div></div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 bg-danger text-white kpi-card-hover fade-up fade-up-d4">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">Total Shares</p>
                    <h3 class="mb-0 text-white f-w-300" id="kpiShares"><span class="sk-block" style="width:80px;height:24px;display:inline-block;"></span></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiSharesSub"><i class="ph ph-share-network me-1"></i>Loading…</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-share-network"></i></div></div>
            </div></div>
        </div>
    </div>
</div>

{{-- ══ Page Export Toolbar ══ --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
        <i class="ph ph-export"></i>
        <span>Export Halaman</span>
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Tab Aktif</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                onclick="OVExport.run('pdf',this)" title="Export halaman sebagai PDF">
            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                onclick="OVExport.run('image',this)" title="Export halaman sebagai PNG">
            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
        </button>
    </div>
</div>

{{-- ══ TABS ══ --}}
<div class="tme-tabs" id="ovTabsBar">
    <button class="tme-tab-btn active" id="tab-hashtag" onclick="OVTab.show('hashtag')">
        <i class="ph ph-hash"></i> Top Hashtags <span class="tme-tab-chip" id="chip-hashtag">—</span>
    </button>
    <button class="tme-tab-btn" id="tab-view"    onclick="OVTab.show('view')">
        <i class="ph ph-eye"></i> Most Viewed <span class="tme-tab-chip" id="chip-view">—</span>
    </button>
    <button class="tme-tab-btn" id="tab-like"    onclick="OVTab.show('like')">
        <i class="ph ph-thumbs-up"></i> Most Liked <span class="tme-tab-chip" id="chip-like">—</span>
    </button>
    <button class="tme-tab-btn" id="tab-comment" onclick="OVTab.show('comment')">
        <i class="ph ph-chat-circle"></i> Most Comments <span class="tme-tab-chip" id="chip-comment">—</span>
    </button>
    <button class="tme-tab-btn" id="tab-share"   onclick="OVTab.show('share')">
        <i class="ph ph-share-network"></i> Most Shares <span class="tme-tab-chip" id="chip-share">—</span>
    </button>
</div>

{{-- ══ TAB PANEL: Hashtag ══ --}}
<div class="tme-tab-panel active" id="panel-hashtag">
    <div class="row">

        {{-- Ranked List --}}
        <div class="col-lg-7 col-12">
            <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
                <div id="card-export-hashtag-list">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-hash f-18 text-primary"></i></div>
                            <div><h6 class="mb-0">Top Hashtags</h6><small class="text-muted">Klik untuk lihat video terkait</small></div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light-primary text-primary" id="badgeHashtag">Loading…</span>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-hashtag-list','hashtag-list','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-hashtag-list','hashtag-list','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                            </div>
                        </div>
                    </div>
                    <div id="hashtagLoading" class="spinner-state"><div class="spin-ring"></div><span>Memuat hashtag…</span></div>
                    <div id="hashtagContent" style="display:none;">
                        <div id="hashtagList" class="ht-list"></div>
                        <div id="pag-hashtag"></div>
                    </div>
                    <div id="hashtagEmpty" style="display:none;" class="chart-empty" style="padding:40px 0;"><i class="ph ph-hash"></i><span>Tidak ada data hashtag</span></div>
                </div>{{-- /card-export-hashtag-list --}}
            </div>
        </div>

        {{-- Donut Top 5 --}}
        <div class="col-lg-5 col-12">
            <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
                <div id="card-export-hashtag-donut">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
                            <div><h6 class="mb-0">Distribusi — Top 5</h6><small class="text-muted">Proporsi penggunaan hashtag teratas</small></div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div id="donutHashtagLegend" class="donut-legend"></div>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-hashtag-donut','hashtag-donut','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-hashtag-donut','hashtag-donut','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height:380px;">
                            <div class="chart-loading" id="loadingDonutHashtag"><div class="spin-ring"></div><span>Loading…</span></div>
                            <div id="donutHashtagChart" style="width:100%;height:380px;display:none;"></div>
                        </div>
                    </div>
                </div>{{-- /card-export-hashtag-donut --}}
            </div>
        </div>

    </div>
</div>

{{-- ══ TAB PANELS: Engagement ══ --}}
@foreach(['view','like','comment','share'] as $tp)
@php
    $panelLabels = ['view'=>'Most Viewed','like'=>'Most Liked','comment'=>'Most Comments','share'=>'Most Shares'];
    $panelIcons  = ['view'=>'ph-eye','like'=>'ph-thumbs-up','comment'=>'ph-chat-circle','share'=>'ph-share-network'];
@endphp
<div class="tme-tab-panel" id="panel-{{ $tp }}">

    {{-- Donut --}}
    <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
        <div id="card-export-donut-{{ $tp }}">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
                    <div>
                        <h6 class="mb-0" id="donutTitle-{{ $tp }}">Distribusi {{ $panelLabels[$tp] }} — Top 5</h6>
                        <small class="text-muted">Proporsi engagement per video — klik untuk detail</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div id="donutLegend-{{ $tp }}" class="donut-legend"></div>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-donut-{{ $tp }}','donut-{{ $tp }}','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-donut-{{ $tp }}','donut-{{ $tp }}','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:420px;">
                    <div class="chart-loading" id="loadingDonut-{{ $tp }}"><div class="spin-ring"></div><span>Loading chart…</span></div>
                    <div id="donutChart-{{ $tp }}" style="width:100%;height:420px;display:none;"></div>
                    <div id="donutEmpty-{{ $tp }}" style="display:none;" class="chart-empty"><i class="ph ph-chart-donut"></i><span>No data</span></div>
                </div>
            </div>
        </div>{{-- /card-export-donut --}}
    </div>

    {{-- Post List --}}
    <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
        <div id="card-export-list-{{ $tp }}">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph {{ $panelIcons[$tp] }} f-18 text-primary"></i></div>
                    <div><h6 class="mb-0">Top Videos by {{ $panelLabels[$tp] }}</h6><small class="text-muted">Klik video untuk lihat detail</small></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select class="tme-rows-sel" id="rows-{{ $tp }}" onchange="OVData.reloadTab('{{ $tp }}')">
                        <option value="10">Top 10</option>
                        <option value="20">Top 20</option>
                        <option value="50">Top 50</option>
                        <option value="100" selected>Top 100</option>
                    </select>
                    <span class="badge bg-light-primary text-primary" id="badge-{{ $tp }}">Loading…</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-list-{{ $tp }}','list-{{ $tp }}','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-list-{{ $tp }}','list-{{ $tp }}','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div id="list-{{ $tp }}" class="p-0"><div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div></div>
            <div id="pag-{{ $tp }}"></div>
        </div>{{-- /card-export-list --}}
    </div>

    {{-- Bar Chart --}}
    <div class="card mb-3" style="display:none;">
        <div id="card-export-bar-{{ $tp }}">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-bar f-18 text-primary"></i></div>
                    <div><h6 class="mb-0">{{ $panelLabels[$tp] }} Chart</h6><small class="text-muted">Top 10 — klik bar untuk detail</small></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light-secondary text-muted">Top 10</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-bar-{{ $tp }}','bar-{{ $tp }}','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-bar-{{ $tp }}','bar-{{ $tp }}','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:280px;" id="wrapBar-{{ $tp }}">
                    <div class="chart-loading" id="loadingBar-{{ $tp }}"><div class="spin-ring"></div><span>Loading…</span></div>
                    <div id="bar-{{ $tp }}" style="width:100%;height:280px;display:none;"></div>
                    <span class="chart-clickable-hint"><i class="ph ph-cursor-click me-1"></i>Klik bar untuk detail</span>
                </div>
            </div>
        </div>{{-- /card-export-bar --}}
    </div>

    {{-- Engagement Comparison (view tab only) --}}
    @if($tp === 'view')
    <div class="card mb-3" style="display:none;">
        <div id="card-export-eng">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-secondary rounded"><i class="ph ph-chart-bar-horizontal f-18 text-muted"></i></div>
                    <div><h6 class="mb-0">View vs Like vs Comment vs Share — Top 10</h6><small class="text-muted">Perbandingan engagement keseluruhan</small></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="tme-toggle-group" id="engTypeToggle">
                        <button class="tme-toggle-btn active" data-type="stacked" onclick="OVChart.setEngType('stacked')">Stacked</button>
                        <button class="tme-toggle-btn" data-type="grouped"  onclick="OVChart.setEngType('grouped')">Grouped</button>
                    </div>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="OVExport.runCard('card-export-eng','eng','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="OVExport.runCard('card-export-eng','eng','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:280px;" id="wrapEng">
                    <div class="chart-loading" id="loadingEng"><div class="spin-ring"></div><span>Loading…</span></div>
                    <div id="barEng" style="width:100%;height:280px;display:none;"></div>
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
<div class="do-panel-overlay" id="ovPanelOverlay" onclick="OVPanel.close()"></div>
<div class="do-panel" id="ovSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="ovPanelDot" style="background:var(--primary);"></div>
        <span class="do-panel-title" id="ovPanelTitle">TikTok Videos</span>
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
{{-- ══ Export dependencies ══ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

const OVCfg = {
    pid    : OV_PID,
    sd     : OV_SD,
    ed     : OV_ED,
    primary: '#038047',
    colors : { view:'#038047', like:'#10B981', comment:'#F59E0B', share:'#06B6D4', hashtag:'#038047' },
    perPage: 10,
};
const DONUT_COLORS = ['#038047','#273B4A','#F59E0B','#06B6D4','#EF4444'];

const _$   = id => document.getElementById(id);
const numF = n  => parseInt(n||0).toLocaleString('id-ID');
const numK = n  => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc  = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const dec  = s  => { if(!s) return ''; try{ const f=decodeURIComponent(escape(s)); if(!f.includes('\uFFFD')&&f!==s)return f; }catch(e){} return s; };
const hideLd = id => { const e=_$(id); if(e&&e.classList.contains('chart-loading')) e.classList.add('hidden'); };

const Store   = { view:[], like:[], comment:[], share:[], hashtag:[] };
const Pag     = { view:1, like:1, comment:1, share:1, hashtag:1 };
let allPostsRaw = [];

const Charts = {};
function makeChart(id, opts) {
    if(Charts[id]) { try{ Charts[id].destroy(); }catch(e){} }
    const el=_$(id); if(!el) return null;
    Charts[id]=new ApexCharts(el,opts); Charts[id].render();
    el.style.display='block'; return Charts[id];
}
window.addEventListener('resize',()=>Object.values(Charts).forEach(c=>{ try{ c.updateOptions({}); }catch(e){} }));

/* ══ TABS ══ */
const OVTab = {
    _loaded:{ hashtag:false, view:false, like:false, comment:false, share:false },
    show(type) {
        ['hashtag','view','like','comment','share'].forEach(t=>{
            _$('tab-'+t)?.classList.toggle('active',t===type);
            _$('panel-'+t)?.classList.toggle('active',t===type);
        });
        if(!this._loaded[type]){ this._loaded[type]=true; OVData.loadTab(type); }
        else if(type!=='hashtag'&&Store[type].length){ OVData._renderDonut(type,Store[type]); }
        if(type!=='hashtag') setTimeout(()=>{ _$('ovTabsBar')?.scrollIntoView({behavior:'smooth',block:'nearest'}); },80);
    },
    reset(){ this._loaded={hashtag:false,view:false,like:false,comment:false,share:false}; }
};

/* ══ DATA ══ */
const OVData = {
    async loadTab(type) {
        if(!OVCfg.pid){ this._noProject(type); return; }
        if(type==='hashtag'){ await this.loadHashtags(); return; }
        await this.loadEngagement(type);
    },

    async loadHashtags() {
        const loadEl=_$('hashtagLoading'), contentEl=_$('hashtagContent'), emptyEl=_$('hashtagEmpty'), badge=_$('badgeHashtag');
        try {
            const r=await fetch(`/mk/api/tiktok/trending-topics?project_id=${OVCfg.pid}&start_date=${OVCfg.sd}&end_date=${OVCfg.ed}`);
            const j=await r.json();
            if(j.success&&j.data?.hashtags?.length){
                Store.hashtag=j.data.hashtags; Pag.hashtag=1;
                if(badge) badge.textContent=j.data.total_hashtags+' hashtags';
                const chip=_$('chip-hashtag'); if(chip) chip.textContent=j.data.total_hashtags;
                this._renderHashtagCloud(j.data.hashtags);
                this._renderDonutHashtag(j.data.hashtags);
            } else {
                if(loadEl) loadEl.style.display='none'; if(emptyEl) emptyEl.style.display='flex'; if(badge) badge.textContent='0';
            }
        } catch(e) { console.error('[OV hashtag]',e); if(loadEl) loadEl.style.display='none'; if(emptyEl) emptyEl.style.display='flex'; if(badge) badge.textContent='Error'; }
    },

    _renderHashtagCloud(hashtags) {
        const loadEl=_$('hashtagLoading'), contentEl=_$('hashtagContent'), list=_$('hashtagList'), pagEl=_$('pag-hashtag');
        if(!list) return;
        const all=hashtags.slice(0,50); Store.hashtag=all;
        const page=Pag.hashtag||1, pp=OVCfg.perPage, total=all.length;
        const pages=Math.ceil(total/pp), start=(page-1)*pp, pageItems=all.slice(start,start+pp);
        const maxSize=all[0]?.size||1;
        list.innerHTML='';
        pageItems.forEach((h,i)=>{
            const rank=start+i+1, rkCls=rank<=3?` ht-rank--${rank}`:'';
            const pct=Math.round((h.size/maxSize)*100);
            const el=document.createElement('div'); el.className='ht-item';
            el.innerHTML=`<div class="ht-rank${rkCls}">${rank}</div><div class="ht-name">#${esc(h.name)}</div><div class="ht-bar-wrap"><div class="ht-bar-fill" style="width:${pct}%;"></div></div><div class="ht-count">${numF(h.size)}</div>`;
            el.onclick=()=>this._openHashtagPanel(h); list.appendChild(el);
        });
        if(pagEl){
            if(pages<=1){ pagEl.innerHTML=''; } else {
                const from=start+1, to=Math.min(start+pp,total); let btns='', r=2;
                btns+=`<button class="tme-pag-btn" ${page<=1?'disabled':''} onclick="OVData.goHashtagPage(${page-1})"><i class="ph ph-caret-left"></i></button>`;
                for(let i=1;i<=pages;i++){
                    if(i===1||i===pages||(i>=page-r&&i<=page+r)) btns+=`<button class="tme-pag-btn${i===page?' is-active':''}" onclick="OVData.goHashtagPage(${i})">${i}</button>`;
                    else if(i===page-r-1||i===page+r+1) btns+=`<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`;
                }
                btns+=`<button class="tme-pag-btn" ${page>=pages?'disabled':''} onclick="OVData.goHashtagPage(${page+1})"><i class="ph ph-caret-right"></i></button>`;
                pagEl.innerHTML=`<div class="tme-pagination"><span class="tme-pag-info">Menampilkan ${from}–${to} dari ${total} hashtags</span><div class="tme-pag-controls">${btns}</div></div>`;
            }
        }
        if(loadEl) loadEl.style.display='none'; if(contentEl) contentEl.style.display='block';
    },

    goHashtagPage(page){ Pag.hashtag=page; this._renderHashtagCloud(Store.hashtag); const el=_$('hashtagList'); if(el) el.scrollIntoView({behavior:'smooth',block:'nearest'}); },

    _renderDonutHashtag(hashtags) {
        const loadEl=_$('loadingDonutHashtag'), chartEl=_$('donutHashtagChart');
        if(!loadEl||!chartEl||!hashtags.length){ hideLd('loadingDonutHashtag'); return; }
        this._renderDonutEcharts(chartEl,loadEl,_$('donutHashtagLegend'),hashtags.slice(0,5),'mentions',h=>h.name,h=>h.size,(h)=>this._openHashtagPanel(h),'Hashtag');
    },

    async loadEngagement(type) {
        const subMap={view:'postbyview',like:'postbylike',comment:'postbycomment',share:'postbyshare'};
        const rows=parseInt(_$('rows-'+type)?.value||'100');
        const listEl=_$('list-'+type);
        if(listEl) listEl.innerHTML=`<div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div>`;
        try {
            const r=await fetch(`/mk/api/tiktok/most-engagement?project_id=${OVCfg.pid}&start_date=${OVCfg.sd}&end_date=${OVCfg.ed}&sub=${subMap[type]}&rows=${rows}`);
            const json=await r.json();
            let items=json.data||json||[]; if(!Array.isArray(items)) items=[];
            Store[type]=items; Pag[type]=1;
            const chip=_$('chip-'+type); if(chip) chip.textContent=items.length;
            const badge=_$('badge-'+type); if(badge) badge.textContent=items.length+' videos';
            if(type==='view'&&!allPostsRaw.length) allPostsRaw=items;
            if(type==='view'){ OVChart._renderEng(items); this._updateKpi(items); }
            this._renderList(type);
            this._renderBar(type,items.slice(0,10));
            this._renderDonut(type,items);
        } catch(err) {
            console.error('[OV engagement]',err);
            if(listEl) listEl.innerHTML=`<div class="chart-empty" style="padding:40px;"><i class="ph ph-warning"></i><span>Gagal memuat: ${esc(err.message)}</span></div>`;
            hideLd('loadingBar-'+type); hideLd('loadingDonut-'+type);
        }
    },

    _updateKpi(items) {
        let tv=0,tl=0,tc=0,ts=0;
        items.forEach(i=>{ tv+=parseInt(i.view_cnt||i.views||i.freq||0); tl+=parseInt(i.likes||i.num_likes||0); tc+=parseInt(i.comments||i.num_comments||0); ts+=parseInt(i.shares||i.num_shares||0); });
        const n=items.length, avg=v=>n?Math.round(v/n):0;
        const el=(id,v)=>{ const e=_$(id); if(e) e.textContent=numF(v); };
        const sub=(id,v)=>{ const e=_$(id); if(e) e.innerHTML=`<i class="ph ph-chart-line-up me-1"></i>Avg ${numF(avg(v))} / video &middot; ${n} videos`; };
        el('kpiViews',tv); sub('kpiViewsSub',tv); el('kpiLikes',tl); sub('kpiLikesSub',tl); el('kpiComments',tc); sub('kpiCommentsSub',tc); el('kpiShares',ts); sub('kpiSharesSub',ts);
    },

    reloadTab(type){ Store[type]=[]; Pag[type]=1; this.loadEngagement(type); },
    _noProject(type){ const el=_$('list-'+type); if(el) el.innerHTML=`<div class="chart-empty" style="padding:40px;"><i class="ph ph-folder-open"></i><span>Pilih project terlebih dahulu</span></div>`; hideLd('loadingBar-'+type); hideLd('loadingDonut-'+type); },

    _metric(item,type){ const keys={view:['view_cnt','views','freq'],like:['likes','num_likes'],comment:['comments','num_comments'],share:['shares','num_shares']}; return (keys[type]||['view_cnt']).reduce((v,k)=>v||parseInt(item[k]||0),0); },
    _getName(item){ const n=(item.name||item.author_scr_name||item.author_name||'').replace(/<[^>]*>/g,'').trim(); if(n&&n!=='TikTok Creator')return n; return item.author_id?'@'+item.author_id:'TikTok Creator'; },
    _getAvatar(item){ return (item.avatar_url||item.author_avatar||item.profile_image||item.author_image||'').trim(); },
    _getThumb(item) { return (item.avatar_url||item.profile_url||item.thumbnail_url||item.cover_url||item.video_cover||item.thumbnail||item.image||'').trim(); },
    _getColor(item){ const seed=item.author_id||item.id||this._getName(item)||'tt'; const palette=['#038047','#273B4A','#F59E0B','#06B6D4','#8b5cf6','#ec4899','#f97316','#14b8a6']; let h=0; for(let i=0;i<seed.length;i++) h=(h*31+seed.charCodeAt(i))&0xffffffff; return palette[Math.abs(h)%palette.length]; },
    _avHtml(item){ const av=this._getAvatar(item), dummy='/assets/images/user/dummy.jpg'; return (av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.src='${dummy}'">`:`<img src="${dummy}">`; },
    _normSent(item){ const r=String(item.sentiment_str||item.sentiment||'').toLowerCase(); return r.includes('pos')?'pos':r.includes('neg')?'neg':'neu'; },

    _renderList(type) {
        const items=Store[type], listEl=_$('list-'+type), pagEl=_$('pag-'+type);
        if(!listEl) return;
        if(!items.length){ listEl.innerHTML=`<div class="chart-empty" style="padding:40px;"><i class="ph ph-folder-open"></i><span>Tidak ada data untuk periode ini</span></div>`; if(pagEl)pagEl.innerHTML=''; return; }
        const page=Pag[type]||1, total=items.length, pp=OVCfg.perPage;
        const pages=Math.ceil(total/pp), start=(page-1)*pp;
        listEl.innerHTML=`<div class="tme-post-list">${items.slice(start,start+pp).map((item,i)=>this._postHtml(item,start+i,type)).join('')}</div>`;
        if(pagEl) pagEl.innerHTML=this._pagHtml(type,page,pages,total,start+1,Math.min(start+pp,total));
        listEl.querySelectorAll('.tme-post').forEach(el=>{
            el.addEventListener('click',()=>{
                try { const item=JSON.parse(decodeURIComponent(el.dataset.item)); const lm={view:'Most Viewed',like:'Most Liked',comment:'Most Comments',share:'Most Shares'}; OVPanel.open(items,type,'TikTok — '+lm[type]); OVDetail.open(item,type); } catch(e){ console.warn(e); }
            });
        });
    },

    _pagHtml(type,page,pages,total,from,to) {
        if(pages<=1) return '';
        let btns='', r=2;
        btns+=`<button class="tme-pag-btn" ${page<=1?'disabled':''} onclick="OVData.goPage('${type}',${page-1})"><i class="ph ph-caret-left"></i></button>`;
        for(let i=1;i<=pages;i++){ if(i===1||i===pages||(i>=page-r&&i<=page+r)) btns+=`<button class="tme-pag-btn${i===page?' is-active':''}" onclick="OVData.goPage('${type}',${i})">${i}</button>`; else if(i===page-r-1||i===page+r+1) btns+=`<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`; }
        btns+=`<button class="tme-pag-btn" ${page>=pages?'disabled':''} onclick="OVData.goPage('${type}',${page+1})"><i class="ph ph-caret-right"></i></button>`;
        return `<div class="tme-pagination"><span class="tme-pag-info">Menampilkan ${from}–${to} dari ${total} videos</span><div class="tme-pag-controls">${btns}</div></div>`;
    },

    goPage(type,page){ Pag[type]=page; this._renderList(type); const el=_$('list-'+type); if(el) el.scrollIntoView({behavior:'smooth',block:'nearest'}); },

    _postHtml(item,gi,type) {
        const rank=gi+1, rkCls=rank<=3?'--'+rank:'';
        const name=this._getName(item), color=this._getColor(item), avHtml=this._avHtml(item), sent=this._normSent(item);
        const content=dec((item.content||item.caption||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()).slice(0,200);
        const dt=(item.date_created||'').split('T')[0], url=item.url||item.link||'';
        const thumb=this._getThumb(item);
        const v=parseInt(item.view_cnt||item.views||item.freq||0), l=parseInt(item.likes||item.num_likes||0), c=parseInt(item.comments||item.num_comments||0), s=parseInt(item.shares||item.num_shares||0);
        const total=v+l+c+s, sentLbl={pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];
        const enc=encodeURIComponent(JSON.stringify(item));
        const vCls=type==='view'?' tme-metric--primary':'', lCls=type==='like'?' tme-metric--primary':'', cCls=type==='comment'?' tme-metric--amber':'', sCls=type==='share'?' tme-metric--cyan':'';
        const thumbHtml=(thumb&&thumb.startsWith('http'))?`<div class="tme-post-thumb"><img src="${esc(thumb)}" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'tme-post-thumb-ph\\'>🎵</div>'"><div class="tme-post-thumb-play"><i class="ph ph-play-fill"></i></div></div>`:`<div class="tme-post-thumb"><div class="tme-post-thumb-ph">🎵</div></div>`;
        return `<div class="tme-post" data-item="${esc(enc)}">
            <div class="tme-post-rank tme-post-rank${rkCls}">${rank}</div>
            <div class="tme-post-av" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
            <div class="tme-post-body">
                <div class="tme-post-author">${esc(name)}</div>
                ${dt?`<div class="tme-post-date">${dt}</div>`:''}
                ${content?`<div class="tme-post-text">${esc(content)}</div>`:''}
                <div class="tme-post-stats">
                    <span class="tme-metric${vCls}"><i class="ph ph-eye me-1"></i>${numF(v)}</span>
                    <span class="tme-metric${lCls}"><i class="ph ph-thumbs-up me-1"></i>${numF(l)}</span>
                    <span class="tme-metric${cCls}"><i class="ph ph-chat-circle me-1"></i>${numF(c)}</span>
                    <span class="tme-metric${sCls}"><i class="ph ph-share-network me-1"></i>${numF(s)}</span>
                    <span class="tme-metric" style="font-weight:800;">∑ ${numF(total)}</span>
                    <span class="tme-sent tme-sent--${sent}">${sentLbl}</span>
                    ${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="tme-view-link" onclick="event.stopPropagation()"><i class="ph ph-arrow-square-out me-1"></i>Lihat</a>`:''}
                </div>
            </div>
            ${thumbHtml}
        </div>`;
    },

    _renderBar(type, items) {
        const barEl=_$('bar-'+type), loadEl=_$('loadingBar-'+type);
        if(!barEl||!items.length||typeof ApexCharts==='undefined'){ hideLd('loadingBar-'+type); return; }
        const color=OVCfg.colors[type];
        const labels=items.map(it=>{ const n=this._getName(it); return n.length>14?n.slice(0,13)+'…':n; });
        const values=items.map(it=>this._metric(it,type));
        const opts={
            chart:{ type:'bar',height:280,fontFamily:'inherit',background:'transparent',toolbar:{show:false},zoom:{enabled:false},
                events:{ mounted:()=>hideLd('loadingBar-'+type), click:(_,ctx,cfg)=>{ const item=items[cfg.dataPointIndex]; if(item){ OVPanel.open(Store[type],type,'TikTok'); OVDetail.open(item,type); } } }
            },
            series:[{ name:{view:'Views',like:'Likes',comment:'Comments',share:'Shares'}[type],data:values }],
            colors:[color],
            plotOptions:{ bar:{ borderRadius:5,columnWidth:'58%',dataLabels:{position:'top'} } },
            dataLabels:{ enabled:true,formatter:v=>numK(v),offsetY:-16,style:{fontSize:'10px',fontWeight:'800',colors:[color]},background:{enabled:false} },
            xaxis:{ categories:labels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'},rotate:labels.length>6?-28:0,hideOverlappingLabels:true} },
            yaxis:{ labels:{formatter:v=>numK(v),style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false},axisTicks:{show:false} },
            grid:{ borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}},padding:{top:20,right:8,bottom:0,left:4} },
            fill:{ type:'gradient',gradient:{type:'vertical',shadeIntensity:0.2,opacityFrom:1,opacityTo:.7,stops:[0,100]} },
            tooltip:{ shared:false,intersect:true,style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>numF(v)} },
        };
        barEl.style.display='block'; makeChart('bar-'+type, opts);
    },

    _renderDonut(type, items) {
        const loadEl=_$('loadingDonut-'+type), chartEl=_$('donutChart-'+type), emptyEl=_$('donutEmpty-'+type), legEl=_$('donutLegend-'+type);
        if(!loadEl||!chartEl) return;
        if(!items.length){ loadEl.style.display='none'; if(emptyEl) emptyEl.style.display='flex'; return; }
        const top5=items.slice(0,5);
        const metLbl={view:'Views',like:'Likes',comment:'Comments',share:'Shares'}[type];
        this._renderDonutEcharts(chartEl,loadEl,legEl,top5,metLbl,it=>this._getName(it),it=>this._metric(it,type),(it)=>{ OVPanel.open(Store[type],type,'TikTok'); OVDetail.open(it,type); },metLbl,it=>dec((it.content||it.caption||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()));
    },

    _renderDonutEcharts(chartEl, loadEl, legEl, data, metricLabel, getNameFn, getValFn, onClickFn, titleLabel, getDescFn) {
        if(!chartEl||!data.length){ if(loadEl) loadEl.style.display='none'; return; }
        const total=data.reduce((s,d)=>s+getValFn(d),0);
        if(legEl) legEl.innerHTML=data.map((d,i)=>{ const n=getNameFn(d), sn=n.length>22?n.slice(0,21)+'…':n; return `<div class="donut-leg-item"><span class="donut-dot" style="background:${DONUT_COLORS[i]};"></span>${sn} · ${numF(getValFn(d))}</div>`; }).join('');
        if(loadEl) loadEl.style.display='none';
        chartEl.style.display='block';
        const ecKey='__echart_'+chartEl.id;
        if(window[ecKey]){ try{ window[ecKey].dispose(); }catch(e){} }
        if(typeof echarts==='undefined'){ chartEl.innerHTML='<div class="chart-empty"><i class="ph ph-chart-donut"></i><span>ECharts not loaded</span></div>'; return; }
        const chart=echarts.init(chartEl,null,{renderer:'canvas'});
        window[ecKey]=chart;
        window.addEventListener('resize',()=>{ try{ chart.resize(); }catch(e){} });
        const pieData=data.map((d,i)=>({ name:getNameFn(d), value:getValFn(d), _desc:getDescFn?getDescFn(d):'', itemStyle:{color:DONUT_COLORS[i]} }));
        chart.setOption({
            backgroundColor:'transparent', animation:true, animationDuration:1000, animationEasing:'cubicOut', animationDelay:idx=>idx*80,
            tooltip:{show:false},
            series:[{
                type:'pie', radius:['38%','62%'], center:['50%','50%'],
                avoidLabelOverlap:true, selectedMode:false, minAngle:8,
                itemStyle:{borderColor:'#fff',borderWidth:3},
                label:{
                    show:true, position:'outside', alignTo:'edge', edgeDistance:20, lineHeight:18, fontSize:11, fontFamily:'inherit', color:'#334155', fontWeight:'500',
                    formatter:p=>{
                        const d=data[p.dataIndex]; const desc=getDescFn?getDescFn(d).slice(0,80):'';
                        const words=desc?desc.split(' '):[]; const lines=[]; let cur='';
                        words.forEach(w=>{ if((cur+' '+w).trim().length>40){lines.push(cur.trim());cur=w;}else{cur=(cur+' '+w).trim();} });
                        if(cur) lines.push(cur); const body=lines.join('\n');
                        return `{title|${p.name}}\n${body?body+'\n':''}({val|${numF(p.value)}} ${metricLabel}, {pct|${p.percent.toFixed(1)}%})`;
                    },
                    rich:{ title:{fontSize:11,fontWeight:'700',color:'#1e293b',lineHeight:18}, val:{fontSize:11,fontWeight:'700',color:'#038047'}, pct:{fontSize:11,fontWeight:'600',color:'#64748b'} }
                },
                labelLine:{show:true,length:18,length2:24,smooth:0.3,lineStyle:{width:1.5,color:'#94A3B8'}},
                emphasis:{ scale:false, itemStyle:{shadowBlur:0,shadowColor:'transparent',borderWidth:3,borderColor:'#fff',opacity:1}, labelLine:{lineStyle:{width:2.5,color:'#273B4A'}}, label:{show:true} },
                select:{disabled:true},
                data:pieData,
            }],
            graphic:[
                {type:'text',left:'center',top:'46%',z:100,style:{text:numK(total),fill:'#0f172a',font:"800 28px inherit",textAlign:'center'}},
                {type:'text',left:'center',top:'54%',z:100,style:{text:'TOTAL '+titleLabel.toUpperCase(),fill:'#94a3b8',font:"600 9px inherit",textAlign:'center'}}
            ]
        });
        chart.on('click',p=>{ const d=data[p.dataIndex]; if(d&&onClickFn) onClickFn(d); });

        let _ttEl=document.getElementById('ovCustomTT');
        if(!_ttEl){ _ttEl=document.createElement('div'); _ttEl.id='ovCustomTT'; _ttEl.style.cssText=`position:fixed;z-index:9999;pointer-events:none;background:#1e293b;color:#fff;border:1px solid #334155;border-radius:6px;padding:10px 14px;max-width:280px;font-size:12px;line-height:1.5;display:none;box-shadow:0 8px 24px rgba(0,0,0,.32);font-family:inherit;opacity:0;transform:translateY(6px) scale(.97);transition:opacity .18s ease,transform .18s ease;`; document.body.appendChild(_ttEl); }
        let _ttTimer=null;
        chart.on('mouseover',p=>{
            if(p.componentType!=='series') return;
            const d=data[p.dataIndex], color=DONUT_COLORS[p.dataIndex], desc=(getDescFn?getDescFn(d):'').slice(0,160);
            clearTimeout(_ttTimer);
            _ttEl.innerHTML=`<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;"><span style="width:9px;height:9px;border-radius:50%;background:${color};flex-shrink:0;display:inline-block;"></span><b style="font-size:12.5px;">${esc(p.name)}</b></div>${desc?`<div style="font-size:11px;color:#94a3b8;margin-bottom:6px;">${esc(desc)}</div>`:''}<div style="display:flex;align-items:center;gap:8px;"><b style="font-size:13px;">${numF(p.value)} ${metricLabel}</b><span style="color:${color};font-weight:700;">${p.percent.toFixed(1)}%</span></div>`;
            _ttEl.style.display='block'; requestAnimationFrame(()=>{ _ttEl.style.opacity='1'; _ttEl.style.transform='translateY(0) scale(1)'; });
        });
        chart.on('mouseout',()=>{ _ttEl.style.opacity='0'; _ttEl.style.transform='translateY(6px) scale(.97)'; _ttTimer=setTimeout(()=>{ _ttEl.style.display='none'; },180); });
        chartEl.addEventListener('mousemove',e=>{ if(_ttEl.style.display==='none') return; const vw=window.innerWidth,vh=window.innerHeight,tw=_ttEl.offsetWidth+16,th=_ttEl.offsetHeight+16; let x=e.clientX+18,y=e.clientY-10; if(x+tw>vw)x=e.clientX-tw; if(y+th>vh)y=e.clientY-th; _ttEl.style.left=x+'px'; _ttEl.style.top=y+'px'; });
    },

    _openHashtagPanel(h) {
        const tag='#'+h.name.toLowerCase();
        const filtered=allPostsRaw.filter(p=>{ const txt=(p.content||p.caption||'').toLowerCase(); return txt.includes(tag); });
        const items=filtered.length?filtered:allPostsRaw.slice(0,20);
        OVPanel.open(items,'view',`TikTok — #${h.name} (${h.size} mentions)`);
    },
};

/* ══ Engagement chart ══ */
const OVChart = {
    _type:'stacked', _items:[],
    setEngType(t){ this._type=t; document.querySelectorAll('#engTypeToggle .tme-toggle-btn').forEach(b=>b.classList.toggle('active',b.dataset.type===t)); if(this._items.length) this._render(this._items,t); },
    _renderEng(items){ hideLd('loadingEng'); if(!items.length) return; const top10=[...items].map(it=>({...it,_total:parseInt(it.view_cnt||it.views||it.freq||0)+parseInt(it.likes||0)+parseInt(it.comments||0)+parseInt(it.shares||0)})).sort((a,b)=>b._total-a._total).slice(0,10); this._items=top10; this._render(top10,this._type); },
    _render(items, stackType) {
        const barEl=_$('barEng'); if(!barEl) return; barEl.style.display='block';
        const isStack=stackType==='stacked';
        const labels=items.map(it=>{ const n=OVData._getName(it); return n.length>14?n.slice(0,13)+'…':n; });
        const seriesData=[{name:'Views',data:items.map(it=>parseInt(it.view_cnt||it.views||it.freq||0)),color:'#038047'},{name:'Likes',data:items.map(it=>parseInt(it.likes||0)),color:'#10B981'},{name:'Comments',data:items.map(it=>parseInt(it.comments||0)),color:'#F59E0B'},{name:'Shares',data:items.map(it=>parseInt(it.shares||0)),color:'#06B6D4'}];
        const opts={ chart:{type:'bar',height:280,fontFamily:'inherit',background:'transparent',toolbar:{show:false},zoom:{enabled:false},stacked:isStack,events:{click:(_,ctx,cfg)=>{ const item=items[cfg.dataPointIndex]; if(item){ OVPanel.open(Store.view,'view','TikTok'); OVDetail.open(item,'view'); } }}}, series:seriesData.map(s=>({name:s.name,data:s.data})), colors:seriesData.map(s=>s.color), plotOptions:{bar:{borderRadius:isStack?2:4,columnWidth:isStack?'60%':'75%',dataLabels:{position:isStack?'center':'top'}}}, dataLabels:{enabled:isStack,formatter:v=>v>0?numK(v):'',style:{fontSize:'9px',fontWeight:'700',colors:['#fff']},background:{enabled:false}}, xaxis:{categories:labels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'},rotate:labels.length>7?-28:0}}, yaxis:{labels:{formatter:v=>numK(v),style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false},axisTicks:{show:false}}, grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}},padding:{top:10,right:8,bottom:0,left:4}}, legend:{position:'bottom',horizontalAlign:'left',fontSize:'11px',fontFamily:'inherit',fontWeight:600,markers:{width:8,height:8,radius:4},itemMargin:{horizontal:12,vertical:4},offsetY:4}, tooltip:{shared:true,intersect:false,style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>numF(v)}} };
        makeChart('barEng',opts);
    }
};

/* ══ PANEL ══ */
const OVPanel = {
    _items:[], _type:null,
    open(items,type,title){
        this._items=items||[]; this._type=type; OVDetail.close();
        _$('ovPanelDot').style.background=OVCfg.colors[type]||OVCfg.primary;
        _$('ovPanelTitle').textContent=title||'TikTok Videos';
        _$('ovPanelMeta').textContent=OVCfg.sd+' – '+OVCfg.ed;
        const ov=_$('ovPanelOverlay'),pn=_$('ovSntPanel');
        ov.classList.remove('hiding'); pn.classList.remove('hiding'); ov.classList.add('show'); pn.classList.add('show');
        this._render(items,type);
    },
    close(){
        OVDetail.killIframe(); OVDetail.close();
        const ov=_$('ovPanelOverlay'),pn=_$('ovSntPanel'); pn.classList.add('hiding'); ov.classList.add('hiding');
        setTimeout(()=>{ pn.classList.remove('show','hiding'); ov.classList.remove('show','hiding'); },240);
    },
    _render(items,type){
        const list=_$('ovPanelList'); if(!list) return;
        if(!items?.length){ list.innerHTML='<div class="do-panel-loading"><div class="do-panel-spinner"></div>Tidak ada data</div>'; return; }
        const metLbl={view:'Views',like:'Likes',comment:'Komentar',share:'Shares'}[type]||'Views';
        list.innerHTML=items.slice(0,100).map(item=>{
            const name=OVData._getName(item), av=OVData._getAvatar(item), color2=OVData._getColor(item), dummy='/assets/images/user/dummy.jpg';
            const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.src='${dummy}'">`:`<img src="${dummy}">`;
            const text=(item.content||item.caption||'').replace(/<[^>]*>/g,'').trim();
            const metVal=OVData._metric(item,type), dt=(item.date_created||'').split('T')[0], sent=OVData._normSent(item), sentLbl={pos:'Pos',neg:'Neg',neu:'Neu'}[sent];
            const total=parseInt(item.view_cnt||item.views||item.freq||0)+parseInt(item.likes||0)+parseInt(item.comments||0)+parseInt(item.shares||0);
            const enc=encodeURIComponent(JSON.stringify(item));
            return `<div class="do-panel-item" data-item="${esc(enc)}" data-type="${type}" onclick="OVPanel._click(this)">
                <div class="do-panel-avatar" style="background:linear-gradient(135deg,${color2},${color2}99);">${avHtml}</div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author">${esc(name)}</div>
                    <div class="do-panel-text">${esc(dec(text).slice(0,130)||'(tidak ada konten)')}</div>
                    <div class="do-panel-footer"><span class="do-sent-badge do-sent-badge--${sent}">${sentLbl}</span><span>${metLbl} ${numF(metVal)}</span><span>∑ ${numF(total)}</span>${dt?`<span style="margin-left:auto;">${dt}</span>`:''}</div>
                </div>
            </div>`;
        }).join('');
        if(items.length>100) list.insertAdjacentHTML('beforeend',`<div style="padding:8px;text-align:center;font-size:10px;font-weight:600;color:#94A3B8;background:var(--slate-50);border-top:1px dashed var(--slate-200);">+${(items.length-100).toLocaleString()} lainnya</div>`);
    },
    _click(el){ try{ const item=JSON.parse(decodeURIComponent(el.dataset.item.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"'))); OVDetail.open(item,el.dataset.type||this._type); }catch(e){ console.warn(e); } }
};

/* ══ DETAIL ══ */
const OVDetail = {
    open(item,type){
        const panel=_$('ovDetailPanel'),body=_$('ovDetailBody'),title=_$('ovDetailTitle'); if(!panel||!body) return;
        const color=OVCfg.colors[type]||OVCfg.primary, name=OVData._getName(item), av=OVData._getAvatar(item), avColor=OVData._getColor(item), dummy='/assets/images/user/dummy.jpg';
        const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.src='${dummy}'">`:`<img src="${dummy}">`;
        const handle=item.author_scr_name||item.author_id||'', rawContent=(item.content||item.caption||'').replace(/<[^>]*>/g,'').trim(), content=rawContent?dec(rawContent):'';
        const url=item.url||item.link||'', dt=item.date_created||'';
        const v=parseInt(item.view_cnt||item.views||item.freq||0), l=parseInt(item.likes||item.num_likes||0), c=parseInt(item.comments||item.num_comments||0), s=parseInt(item.shares||item.num_shares||0);
        const sent=OVData._normSent(item), sentLbl={pos:'Positif',neg:'Negatif',neu:'Netral'}[sent];
        let dtFmt=''; if(dt){ try{ dtFmt=new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); }catch(e){ dtFmt=dt.split('T')[0]; } }
        let videoId=''; if(url){ const m=url.match(/\/video\/(\d+)/); if(m) videoId=m[1]; } if(!videoId&&item.id){ const m=String(item.id).match(/(\d{10,})/); if(m) videoId=m[1]; }
        const mediaHtml=videoId?`<div class="do-dp2-media"><iframe id="ov_iframe_${videoId}" src="https://www.tiktok.com/embed/v2/${videoId}" allow="autoplay" allowfullscreen></iframe></div>`:'';
        title.textContent=name;
        body.innerHTML=`<div class="do-dp2-avatar-row"><div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div><div><div class="do-dp2-name">${esc(name)}</div>${handle?`<div class="do-dp2-handle">@${esc(handle)}</div>`:''}<span class="do-dp2-plat-badge" style="background:${color}18;color:${color};">TikTok</span></div></div>${dtFmt?`<div class="do-dp2-meta">${dtFmt}</div>`:''}<div class="do-dp2-sent do-dp2-sent--${sent}">${sentLbl}</div>${mediaHtml}${content?`<div class="do-dp2-content">${esc(content)}</div>`:''}<div class="do-dp2-stats"><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(v)}</div><div class="do-dp2-stat-lbl">Views</div></div><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(l)}</div><div class="do-dp2-stat-lbl">Likes</div></div><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(c)}</div><div class="do-dp2-stat-lbl">Comments</div></div><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(s)}</div><div class="do-dp2-stat-lbl">Shares</div></div></div>${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out me-1"></i>Buka di TikTok</a>`:''}`;
        panel.classList.add('show');
    },
    close(){ _$('ovDetailPanel')?.classList.remove('show'); },
    killIframe(){ const b=_$('ovDetailBody'); if(b) b.querySelectorAll('iframe').forEach(f=>{ f.src=''; f.remove(); }); }
};

/* ══════════════════════════════════════════════════════
   EXPORT MODULE — OVExport
   Full-page: KPI + visible tab (hashtag/engagement)
   Per-card:  setiap card individual
══════════════════════════════════════════════════════ */
const OVExport = (() => {
    let _toastTimer = null;

    function _toast(msg, type='default', duration=3200) {
        const t=_$('exportToast'), m=_$('exportToastMsg'), ico=_$('exportToastIcon');
        if(!t||!m) return;
        m.textContent=msg; t.className='export-toast show '+(type!=='default'?type:'');
        const icons={success:'ph-check-circle',error:'ph-x-circle',default:'ph-spinner'};
        ico.className='ph '+(icons[type]||icons.default);
        clearTimeout(_toastTimer); _toastTimer=setTimeout(()=>t.classList.remove('show'),duration);
    }

    function _btnState(btn, loading) { if(!btn) return; btn.disabled=loading; btn.classList.toggle('exporting',loading); }

    /* ── Resize semua ECharts canvas aktif ── */
    function _syncCharts() {
        /* All echarts instances tracked via window.__echart_{id} */
        Object.keys(window).filter(k=>k.startsWith('__echart_')).forEach(k=>{ try{ window[k].resize(); }catch(e){} });
        Object.values(Charts).forEach(c=>{ try{ c.updateOptions({}); }catch(e){} });
    }

    /* ── Full-page capture ── */
    async function _capture() {
        const area=_$('pageExportArea'); if(!area) throw new Error('pageExportArea tidak ditemukan');
        window.scrollTo({top:0}); await new Promise(r=>setTimeout(r,300));
        _syncCharts();
        return html2canvas(area, {
            scale:2, useCORS:true, allowTaint:false, backgroundColor:'#f1f5f9',
            logging:false, removeContainer:true,
            windowWidth:document.documentElement.scrollWidth, windowHeight:area.scrollHeight, height:area.scrollHeight,
            ignoreElements:el=>el.hasAttribute('data-html2canvas-ignore')||el.id==='pageExportPdfBtn'||el.id==='pageExportImgBtn',
        });
    }

    /* ── PDF header ── */
    function _drawHeader(doc, pW, margin, title) {
        doc.setFillColor(3,128,71); doc.rect(0,0,pW,11,'F');
        doc.setTextColor(255,255,255); doc.setFontSize(9); doc.setFont('helvetica','bold');
        doc.text('SMADIMENT — '+title, margin, 7.5);
        const now=new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
        doc.setFontSize(7); doc.setFont('helvetica','normal'); doc.text('Generated: '+now, pW-margin, 7.5,{align:'right'});
    }

    /* ── Paginate canvas into PDF pages ── */
    function _paginate(canvas, pdf, label) {
        const pW=pdf.internal.pageSize.getWidth(), pH=pdf.internal.pageSize.getHeight();
        const margin=10, usableW=pW-margin*2, usableH=pH-margin*2-14;
        const ratio=usableW/canvas.width, sliceH=usableH/ratio;
        let srcY=0, pageNum=0;
        while(srcY<canvas.height){
            if(pageNum>0){ pdf.addPage(); _drawHeader(pdf,pW,margin,label); }
            const srcSlice=Math.min(sliceH,canvas.height-srcY), dstH=srcSlice*ratio;
            const slice=document.createElement('canvas'); slice.width=canvas.width; slice.height=Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(canvas,0,srcY,canvas.width,srcSlice,0,0,canvas.width,srcSlice);
            pdf.addImage(slice.toDataURL('image/png'),'PNG',margin,14,usableW,dstH);
            pdf.setFontSize(7); pdf.setTextColor(148,163,184); pdf.text(`Halaman ${pageNum+1}`,pW/2,pH-3,{align:'center'});
            srcY+=srcSlice; pageNum++;
        }
    }

    async function _exportPdf() {
        const canvas=await _capture();
        const {jsPDF}=window.jspdf;
        const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
        _drawHeader(pdf,pdf.internal.pageSize.getWidth(),10,'TikTok Overview');
        _paginate(canvas,pdf,'TikTok Overview');
        const stamp=new Date().toISOString().slice(0,10).replace(/-/g,'');
        pdf.save(`tiktok_overview_${OVCfg.pid}_${stamp}.pdf`);
    }

    async function _exportImage() {
        const canvas=await _capture();
        const stamp=new Date().toISOString().slice(0,10).replace(/-/g,'');
        const link=document.createElement('a'); link.download=`tiktok_overview_${OVCfg.pid}_${stamp}.png`; link.href=canvas.toDataURL('image/png'); link.click();
    }

    /* ── Per-card capture ── */
    async function _captureCard(areaId, cardKey) {
        const area=document.getElementById(areaId); if(!area) throw new Error('Area #'+areaId+' tidak ditemukan');
        /* resize specific ECharts canvas for this card */
        const ecId=_ecIdForCard(cardKey);
        if(ecId&&window['__echart_'+ecId]){ try{ window['__echart_'+ecId].resize(); }catch(e){} }
        /* resize ApexCharts if relevant */
        const apexId=_apexIdForCard(cardKey);
        if(apexId&&Charts[apexId]){ try{ Charts[apexId].updateOptions({}); }catch(e){} }
        await new Promise(r=>setTimeout(r,220));
        return html2canvas(area,{scale:2,useCORS:true,allowTaint:false,backgroundColor:'#ffffff',logging:false,removeContainer:true,ignoreElements:el=>el.hasAttribute('data-html2canvas-ignore')});
    }

    function _ecIdForCard(k) {
        const map={'hashtag-donut':'donutHashtagChart','donut-view':'donutChart-view','donut-like':'donutChart-like','donut-comment':'donutChart-comment','donut-share':'donutChart-share'};
        return map[k]||null;
    }
    function _apexIdForCard(k) {
        const map={'bar-view':'bar-view','bar-like':'bar-like','bar-comment':'bar-comment','bar-share':'bar-share','eng':'barEng'};
        return map[k]||null;
    }

    const _cardMeta = {
        'hashtag-list'   : { label:'Top Hashtags',                     file:'top-hashtags' },
        'hashtag-donut'  : { label:'Distribusi Hashtag — Top 5',       file:'hashtag-donut' },
        'donut-view'     : { label:'Distribusi Most Viewed — Top 5',   file:'donut-most-viewed' },
        'donut-like'     : { label:'Distribusi Most Liked — Top 5',    file:'donut-most-liked' },
        'donut-comment'  : { label:'Distribusi Most Comments — Top 5', file:'donut-most-comments' },
        'donut-share'    : { label:'Distribusi Most Shares — Top 5',   file:'donut-most-shares' },
        'list-view'      : { label:'Top Videos by Views',              file:'list-most-viewed' },
        'list-like'      : { label:'Top Videos by Likes',              file:'list-most-liked' },
        'list-comment'   : { label:'Top Videos by Comments',           file:'list-most-comments' },
        'list-share'     : { label:'Top Videos by Shares',             file:'list-most-shares' },
        'bar-view'       : { label:'Chart Most Viewed',                file:'chart-most-viewed' },
        'bar-like'       : { label:'Chart Most Liked',                 file:'chart-most-liked' },
        'bar-comment'    : { label:'Chart Most Comments',              file:'chart-most-comments' },
        'bar-share'      : { label:'Chart Most Shares',                file:'chart-most-shares' },
        'eng'            : { label:'Engagement Comparison',            file:'engagement-comparison' },
    };
    const _cardLabel    = k => (_cardMeta[k]||{label:k}).label;
    const _cardFilename = k => { const f=(_cardMeta[k]||{file:k}).file; const stamp=new Date().toISOString().slice(0,10).replace(/-/g,''); return `tiktok_overview_${f}_${OVCfg.pid}_${stamp}`; };

    async function runCard(areaId, cardKey, type, btn) {
        if(!window.html2canvas)               { _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf'&&!window.jspdf?.jsPDF){ _toast('jsPDF tidak tersedia','error'); return; }
        _btnState(btn,true); _toast(type==='pdf'?'Menyiapkan PDF card…':'Mengambil gambar card…','default',99999);
        try {
            const canvas=await _captureCard(areaId,cardKey), fname=_cardFilename(cardKey);
            if(type==='image'){
                const link=document.createElement('a'); link.download=fname+'.png'; link.href=canvas.toDataURL('image/png'); link.click();
                _toast('Gambar berhasil diunduh!','success');
            } else {
                const {jsPDF}=window.jspdf, isLandscape=canvas.width>canvas.height;
                const pdf=new jsPDF({orientation:isLandscape?'landscape':'portrait',unit:'mm',format:'a4'});
                _drawHeader(pdf,pdf.internal.pageSize.getWidth(),10,_cardLabel(cardKey));
                _paginate(canvas,pdf,_cardLabel(cardKey));
                pdf.save(fname+'.pdf'); _toast('PDF berhasil diunduh!','success');
            }
        } catch(err){ console.error('[OVExport.runCard]',err); _toast('Export gagal: '+err.message,'error'); }
        finally { _btnState(btn,false); }
    }

    async function run(type, btn) {
        if(!window.html2canvas)               { _toast('html2canvas tidak tersedia','error'); return; }
        if(type==='pdf'&&!window.jspdf?.jsPDF){ _toast('jsPDF tidak tersedia','error'); return; }
        const btnPdf=_$('pageExportPdfBtn'), btnImg=_$('pageExportImgBtn');
        _btnState(btnPdf,true); _btnState(btnImg,true);
        _toast(type==='pdf'?'Menyiapkan PDF…':'Mengambil gambar…','default',99999);
        try {
            if(type==='pdf'){ await _exportPdf();   _toast('PDF berhasil diunduh!',   'success'); }
            else            { await _exportImage(); _toast('Gambar berhasil diunduh!','success'); }
        } catch(err){ console.error('[OVExport]',err); _toast('Export gagal: '+err.message,'error'); }
        finally { _btnState(btnPdf,false); _btnState(btnImg,false); }
    }

    return { run, runCard };
})();

/* ══ INIT ══ */
document.addEventListener('DOMContentLoaded', () => {
    if(!OVCfg.pid) return;
    OVTab._loaded.hashtag=true; OVData.loadHashtags();
    OVData.loadEngagement('view').then(()=>{ OVTab._loaded.view=true; });
    document.addEventListener('keydown',e=>{ if(e.key==='Escape') OVPanel.close(); });
});
</script>
@endsection