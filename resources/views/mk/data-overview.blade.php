@extends('mk.layouts.app')

@section('title', 'Data Overview - SMADIMENT')

@section('styles')
    <style>
        :root {
            --do-primary: var(--bs-primary, #4361EE);
            --do-primary-rgb: var(--bs-primary-rgb, 67, 97, 238);
            --do-primary-lt: rgba(var(--do-primary-rgb, 67, 97, 238), .10);
            --do-green: #10B981;
            --do-red: #EF4444;
            --do-slate-50: #F8FAFC;
            --do-slate-100: #F1F5F9;
            --do-slate-200: #E2E8F0;
            --do-slate-300: #CBD5E1;
            --do-slate-400: #94A3B8;
            --do-slate-500: #64748B;
            --do-slate-700: #334155;
            --do-slate-800: #1E293B;
            --do-slate-900: #0F172A;
            --do-radius: 8px;
            --do-radius-sm: 5px;
            --do-shadow-sm: 0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);
            --do-shadow-md: 0 4px 14px rgba(15,23,42,.08);
            --do-shadow-lg: 0 10px 30px rgba(15,23,42,.12);
            --c-news: #0284c7;
            --c-twitter: #1d9bf0;
            --c-facebook: #1877f2;
            --c-instagram: #e1306c;
            --c-youtube: #ff0000;
            --c-tiktok: #111827;
        }
        *,*::before,*::after { box-sizing:border-box; }

        @keyframes fadeUp        { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        @keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
        @keyframes slideOutRight { from{transform:translateX(0);opacity:1}    to{transform:translateX(100%);opacity:0} }
        @keyframes overlayIn     { from{opacity:0} to{opacity:1} }
        @keyframes overlayOut    { from{opacity:1} to{opacity:0} }
        @keyframes shimmer       { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        @keyframes spin          { to{transform:rotate(360deg)} }
        @keyframes kpiShimmer    { 0%{left:-100%} 100%{left:150%} }

        .fade-up    { animation:fadeUp .36s ease-out both; }
        .fade-up-d1 { animation-delay:.04s; }
        .fade-up-d2 { animation-delay:.08s; }
        .fade-up-d3 { animation-delay:.12s; }

        /* Grid */
        .do-row-top { display:grid; grid-template-columns:1fr 1fr 360px; gap:14px; margin-bottom:14px; align-items:start; }
        .do-row-mid { display:grid; grid-template-columns:380px 1fr; gap:14px; margin-bottom:14px; align-items:stretch; }
        .do-mb14 { margin-bottom:14px; }

        /* Tables */
        .do-tbl { width:100%; border-collapse:separate; border-spacing:0; font-size:12px; }
        .do-tbl th { padding:0 0 8px; text-align:left; font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid var(--do-slate-200); }
        .do-tbl td { padding:8px 0; border-bottom:1px solid var(--do-slate-100); vertical-align:middle; }
        .do-tbl tbody tr:last-child td { border-bottom:none; }
        .do-tbl tbody tr:hover td { background:var(--do-slate-50); }
        .do-tbl-rank { font-weight:800; color:var(--do-primary); width:22px; font-size:11px; }
        .do-tbl-name { font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:220px; }
        .do-tbl-num  { text-align:right; font-weight:700; font-size:11px; color:var(--do-slate-500); }
        .topic-link  { color:var(--do-slate-800); text-decoration:none; transition:color .14s; }
        .topic-link:hover { color:var(--do-primary); }

        /* View-all */
        .do-view-all { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; background:transparent; color:var(--do-primary); border:1px solid var(--do-primary); border-radius:var(--do-radius-sm); font-size:10px; font-weight:700; cursor:pointer; transition:all .14s; }
        .do-view-all:hover { background:var(--do-primary); color:#fff; }

        /* Mention */
        .do-mention-body  { display:flex; align-items:stretch; min-height:240px; }
        .do-mention-chart { flex:1; display:flex; align-items:center; justify-content:center; padding:16px; min-width:0; }
        #chMentionPie     { width:100% !important; max-width:200px; height:200px !important; }
        .do-mention-stats { width:148px; flex-shrink:0; border-left:1px solid var(--do-slate-200); padding:16px 14px; display:flex; flex-direction:column; justify-content:center; gap:14px; }
        .do-mstat-label   { font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.5px; margin-bottom:2px; }
        .do-mstat-row     { display:flex; flex-direction:column; gap:2px; cursor:pointer; border-radius:var(--do-radius-sm); padding:5px 6px; margin:-5px -6px; transition:background .13s; }
        .do-mstat-row:hover { background:var(--do-primary-lt); }
        .do-mstat-name    { font-size:11px; font-weight:600; color:var(--do-slate-500); display:flex; align-items:center; gap:4px; }
        .do-mstat-name span { display:inline-block; width:7px; height:7px; border-radius:50%; flex-shrink:0; }
        .do-mstat-val     { font-size:19px; font-weight:800; letter-spacing:-.5px; color:var(--do-slate-900); line-height:1.1; }
        .do-mstat-divider { height:1px; background:var(--do-slate-100); }
        .do-mstat-total-lbl { font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.4px; }
        .do-mstat-total-val { font-size:22px; font-weight:800; letter-spacing:-1px; color:var(--do-primary); line-height:1.1; }

        /* SOV */
        .do-sov-body   { display:flex; align-items:stretch; min-height:300px; }
        .do-sov-chart  { flex:0 0 230px; display:flex; align-items:center; justify-content:center; padding:20px 14px 20px 20px; }
        #chSovPie      { width:200px !important; height:200px !important; }
        .do-sov-legend { flex:1; border-left:1px solid var(--do-slate-200); padding:16px 16px 16px 14px; display:flex; flex-direction:column; justify-content:center; overflow:hidden; }
        .do-sov-legend-title { font-size:10px; font-weight:800; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.7px; margin-bottom:8px; padding-bottom:7px; border-bottom:2px solid var(--do-slate-100); }
        .do-sov-item   { display:flex; flex-direction:column; padding:5px; border-radius:var(--do-radius-sm); transition:background .13s; cursor:pointer; gap:3px; }
        .do-sov-item:hover { background:var(--do-primary-lt); }
        .do-sov-item-row { display:flex; align-items:center; gap:6px; }
        .do-sov-dot    { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
        .do-sov-name   { flex:1; font-size:11px; font-weight:600; color:var(--do-slate-800); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .do-sov-pct    { font-size:12px; font-weight:800; flex-shrink:0; letter-spacing:-.3px; }
        .do-sov-bar-wrap { height:4px; background:var(--do-slate-100); border-radius:2px; overflow:hidden; }
        .do-sov-bar    { height:100%; border-radius:2px; transition:width .8s cubic-bezier(.4,0,.2,1); }

        /* Map */
        .do-map-wrap  { display:flex; }
        .do-map-area  { flex:1; min-width:0; position:relative; }
        .do-loc-panel { width:210px; flex-shrink:0; border-left:1px solid var(--do-slate-200); display:flex; flex-direction:column; }
        .do-loc-title { padding:11px 14px 8px; font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--do-slate-100); }
        .do-loc-list  { overflow-y:auto; flex:1; max-height:400px; }
        .do-loc-list::-webkit-scrollbar { width:3px; }
        .do-loc-list::-webkit-scrollbar-thumb { background:var(--do-slate-200); border-radius:99px; }
        .do-loc-item  { display:flex; align-items:center; gap:8px; padding:9px 12px; cursor:pointer; border-bottom:1px solid var(--do-slate-50); transition:all .13s; }
        .do-loc-item:hover { background:rgba(67,97,238,.05); }
        .do-loc-item.active { background:var(--do-primary-lt); border-left:3px solid var(--do-primary); padding-left:9px; }
        .do-loc-rank  { font-size:10px; font-weight:700; color:var(--do-primary); width:16px; flex-shrink:0; }
        .do-loc-info  { flex:1; min-width:0; }
        .do-loc-name  { font-size:11px; font-weight:600; color:var(--do-slate-800); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .do-loc-count { font-size:10px; color:var(--do-slate-400); font-weight:500; }
        .do-loc-dot   { width:7px; height:7px; border-radius:50%; flex-shrink:0; background:var(--do-primary); }

        /* Skeleton */
        .sk-block { border-radius:4px; background:linear-gradient(90deg,var(--do-slate-100) 25%,var(--do-slate-200) 50%,var(--do-slate-100) 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; }
        .sk-overlay { position:absolute; inset:0; z-index:3; border-radius:var(--do-radius-sm); }
        .do-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:36px 16px; gap:7px; }
        .do-empty i { font-size:32px; color:var(--do-slate-300); }
        .do-empty-txt { font-size:12px; font-weight:600; color:var(--do-slate-400); }
        .do-body-scroll { max-height:210px; overflow-y:auto; }
        .do-body-scroll::-webkit-scrollbar { width:3px; }
        .do-body-scroll::-webkit-scrollbar-thumb { background:var(--do-slate-200); border-radius:99px; }

        /* ══ EXPORT STYLES ══ */
        .page-export-bar {
            display:flex; align-items:center; justify-content:space-between;
            flex-wrap:wrap; gap:10px;
            background:#fff; border:1px solid var(--do-slate-200);
            border-radius:var(--do-radius); padding:9px 14px;
            margin-bottom:14px; box-shadow:var(--do-shadow-sm);
        }
        .page-export-bar-left  { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--do-slate-600); }
        .page-export-bar-left i { font-size:15px; color:var(--do-primary); }
        .page-export-bar-right { display:flex; gap:8px; }

        .page-export-btn {
            display:inline-flex; align-items:center; justify-content:center;
            width:32px; height:32px; border-radius:var(--do-radius-sm);
            font-size:16px; cursor:pointer;
            transition:all .15s ease; border:1.5px solid transparent; font-family:inherit;
        }
        .page-export-btn-pdf { background:#fff3f3; color:#dc2626; border-color:#fca5a5; }
        .page-export-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
        .page-export-btn-img { background:var(--do-primary-lt); color:var(--do-primary); border-color:rgba(67,97,238,.3); }
        .page-export-btn-img:hover { background:var(--do-primary); color:#fff; border-color:var(--do-primary); }
        .page-export-btn:disabled { opacity:.55; cursor:not-allowed; pointer-events:none; }
        .page-export-btn .export-spinner { width:13px; height:13px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
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
        .card-exp-btn-img { color:var(--do-primary); border-color:rgba(67,97,238,.3); background:var(--do-primary-lt); }
        .card-exp-btn-img:hover { background:var(--do-primary); color:#fff; border-color:var(--do-primary); }
        .card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
        .card-exp-btn .export-spinner { width:11px; height:11px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
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

        /* Slide Panel */
        .do-panel-overlay { position:fixed; inset:0; z-index:9000; background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none; }
        .do-panel-overlay.show   { display:block; animation:overlayIn .22s ease-out; }
        .do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }
        .do-panel { position:fixed; top:0; right:0; bottom:0; z-index:9001; width:480px; max-width:100vw; background:#fff; display:none; flex-direction:column; border-left:1px solid var(--do-slate-200); box-shadow:-8px 0 40px rgba(15,23,42,.16); }
        .do-panel.show   { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
        .do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }
        .do-panel-header { display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--do-slate-200); background:var(--do-slate-50); flex-shrink:0; }
        .do-panel-dot    { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
        .do-panel-title  { font-size:13px; font-weight:700; color:var(--do-slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .do-panel-count  { display:none; }
        .do-panel-close  { width:28px; height:28px; border-radius:var(--do-radius-sm); border:1px solid var(--do-slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--do-slate-500); font-size:16px; transition:all .14s; flex-shrink:0; }
        .do-panel-close:hover { background:var(--do-red); border-color:var(--do-red); color:#fff; }
        .do-panel-actions { display:flex; align-items:center; gap:7px; padding:7px 12px; border-bottom:1px solid var(--do-slate-200); background:#fff; flex-shrink:0; }
        .do-panel-meta   { flex:1; font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.5px; overflow:hidden; display:flex; align-items:center; gap:5px; }
        .do-panel-tabs   { display:flex; background:var(--do-slate-100); border:1px solid var(--do-slate-200); border-radius:var(--do-radius-sm); padding:2px; gap:2px; }
        .do-panel-tab    { padding:3px 9px; border-radius:3px; border:none; background:transparent; font-size:11px; font-weight:700; cursor:pointer; transition:all .13s; color:var(--do-slate-500); font-family:inherit; }
        .do-panel-tab:hover { background:#fff; }
        .do-panel-tab.active { background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08); }
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
        .do-panel-handle { font-size:10px; color:var(--do-slate-400); font-weight:500; margin-bottom:2px; }
        .do-panel-text   { font-size:11px; color:var(--do-slate-600); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
        .do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--do-slate-400); flex-wrap:wrap; }
        .do-sent-badge       { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
        .do-sent-badge--pos  { background:#dbeafe; color:#1d4ed8; }
        .do-sent-badge--neg  { background:#fee2e2; color:#991b1b; }
        .do-sent-badge--neu  { background:var(--do-slate-100); color:var(--do-slate-500); }
        .do-panel-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:12px; color:var(--do-slate-400); font-size:13px; font-weight:600; }
        .do-panel-spinner { width:28px; height:28px; border:2.5px solid var(--do-slate-100); border-top-color:var(--do-primary); border-radius:50%; animation:spin .65s linear infinite; }

        /* Detail sub-panel */
        .do-detail-panel { position:absolute; inset:0; background:#fff; z-index:5; display:none; flex-direction:column; animation:slideInRight .2s cubic-bezier(.4,0,.2,1); }
        .do-detail-panel.show { display:flex; }
        .do-dp2-header { display:flex; align-items:center; gap:8px; padding:12px 14px; background:var(--do-slate-50); border-bottom:1px solid var(--do-slate-200); flex-shrink:0; }
        .do-dp2-back { width:28px; height:28px; border-radius:var(--do-radius-sm); border:1px solid var(--do-slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--do-slate-500); transition:all .13s; font-size:14px; }
        .do-dp2-back:hover { background:var(--do-primary-lt); color:var(--do-primary); border-color:var(--do-primary); }
        .do-dp2-title { font-size:13px; font-weight:700; color:var(--do-slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .do-dp2-body  { overflow-y:auto; flex:1; padding:16px; }
        .do-dp2-body::-webkit-scrollbar { width:4px; }
        .do-dp2-body::-webkit-scrollbar-thumb { background:var(--do-slate-200); border-radius:99px; }
        .do-dp2-avatar-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
        .do-dp2-avatar-lg  { width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700; font-size:16px; display:flex; align-items:center; justify-content:center; border:2px solid var(--do-slate-200); overflow:hidden; flex-shrink:0; }
        .do-dp2-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
        .do-dp2-name   { font-size:14px; font-weight:700; color:var(--do-slate-900); }
        .do-dp2-handle { font-size:11px; color:var(--do-slate-400); font-weight:500; }
        .do-dp2-plat-badge { display:inline-block; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; margin-top:3px; }
        .do-dp2-meta   { display:flex; align-items:center; justify-content:space-between; font-size:11px; color:var(--do-slate-400); font-weight:500; margin-bottom:10px; }
        .do-dp2-sent   { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:3px; font-size:11px; font-weight:700; margin-bottom:10px; }
        .do-dp2-sent--pos { background:#dbeafe; color:#1d4ed8; }
        .do-dp2-sent--neg { background:#fee2e2; color:#991b1b; }
        .do-dp2-sent--neu { background:var(--do-slate-100); color:var(--do-slate-500); }
        .do-dp2-content { font-size:12px; color:var(--do-slate-700); line-height:1.7; margin-bottom:12px; background:var(--do-slate-50); border-radius:var(--do-radius-sm); padding:10px 12px; border:1px solid var(--do-slate-200); word-break:break-word; }
        .do-dp2-media   { border-radius:var(--do-radius-sm); overflow:hidden; margin-bottom:10px; background:#000; }
        .do-dp2-media img { width:100%; max-height:220px; object-fit:cover; display:block; }
        .do-dp2-media--video { background:var(--do-slate-900); }
        .do-dp2-stats  { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
        .do-dp2-stat   { background:var(--do-slate-50); border-radius:var(--do-radius-sm); padding:8px 10px; border:1px solid var(--do-slate-200); text-align:center; }
        .do-dp2-stat-val { font-size:14px; font-weight:700; color:var(--do-slate-900); }
        .do-dp2-stat-lbl { font-size:9px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
        .do-dp2-link   { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 14px; background:var(--do-primary); color:#fff; border-radius:var(--do-radius-sm); font-size:12px; font-weight:700; text-decoration:none; transition:filter .14s; margin-top:4px; }
        .do-dp2-link:hover { filter:brightness(1.1); color:#fff; }
        .do-dp2-link i { font-size:13px; }

        /* Platform picker */
        .do-plat-picker { position:fixed; z-index:20000; background:#fff; border:1px solid var(--do-slate-200); border-radius:var(--do-radius); box-shadow:var(--do-shadow-lg); padding:5px; min-width:175px; font-family:inherit; display:none; animation:fadeUp .14s ease-out; }
        .do-plat-picker.show { display:block; }
        .do-plat-picker-head { padding:4px 9px 6px; font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--do-slate-100); margin-bottom:3px; }
        .do-plat-btn { display:flex; align-items:center; gap:7px; padding:7px 10px; border-radius:var(--do-radius-sm); font-size:12px; font-weight:600; cursor:pointer; background:transparent; border:none; font-family:inherit; width:100%; text-align:left; color:var(--do-slate-700); transition:background .12s; }
        .do-plat-btn:hover { background:var(--do-primary-lt); color:var(--do-primary); }
        .do-plat-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-left:auto; }

        /* Modals */
        .do-modal-overlay { position:fixed; inset:0; z-index:8000; background:rgba(15,23,42,.55); backdrop-filter:blur(4px); display:none; align-items:center; justify-content:center; }
        .do-modal-overlay.show { display:flex; animation:overlayIn .2s ease-out; }
        .do-modal-box  { background:#fff; border-radius:var(--do-radius); width:90%; max-width:560px; max-height:80vh; box-shadow:var(--do-shadow-lg); overflow:hidden; animation:fadeUp .24s ease-out; display:flex; flex-direction:column; }
        .do-modal-head { display:flex; justify-content:space-between; align-items:center; padding:14px 20px; border-bottom:1px solid var(--do-slate-200); }
        .do-modal-head-title { font-size:15px; font-weight:700; color:var(--do-slate-900); margin:0; }
        .do-modal-head-close { width:30px; height:30px; border-radius:var(--do-radius-sm); background:#fff; border:1px solid var(--do-slate-200); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .14s; font-size:16px; color:var(--do-slate-500); }
        .do-modal-head-close:hover { background:var(--do-red); border-color:var(--do-red); color:#fff; }
        .do-modal-body { padding:16px 20px 20px; overflow-y:auto; }
        .do-modal-body::-webkit-scrollbar { width:4px; }
        .do-modal-body::-webkit-scrollbar-thumb { background:var(--do-slate-200); border-radius:99px; }

        /* Responsive */
        @media(max-width:1280px) { .do-row-top{grid-template-columns:1fr 1fr;} .do-row-top>.card:last-child{grid-column:1/-1;} .do-row-mid{grid-template-columns:320px 1fr;} }
        @media(max-width:1024px) { .do-row-mid{grid-template-columns:1fr;} .do-sov-body{flex-direction:column;} .do-sov-chart{flex:none;} .do-sov-legend{border-left:none;border-top:1px solid var(--do-slate-200);} }
        @media(max-width:900px)  { .do-row-top{grid-template-columns:1fr;} .do-mention-body{flex-direction:column;} .do-mention-stats{width:100%;border-left:none;border-top:1px solid var(--do-slate-200);flex-direction:row;flex-wrap:wrap;gap:14px;padding:14px 16px;} .do-map-wrap{flex-direction:column;} .do-loc-panel{width:100%;border-left:none;border-top:1px solid var(--do-slate-200);} .do-panel{width:100vw;} }
    </style>
@endsection

@section('page-title', 'Data Overview')

@section('content')

    {{-- Filter --}}
    @include('mk.layouts.partials.filter-datepicker')

    {{-- ════ PAGE EXPORT WRAPPER ════ --}}
    <div id="doPageExportArea">

    {{-- ══ Page Export Toolbar ══ --}}
    <div class="page-export-bar" data-html2canvas-ignore="true">
        <div class="page-export-bar-left">
            <i class="ph ph-export"></i>
            <span>Export Halaman</span>
            <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">
                Trending · Mention · SOV · Sentiment · Map
            </span>
        </div>
        <div class="page-export-bar-right">
            <button type="button" class="page-export-btn page-export-btn-pdf" id="doPageExpPdf"
                    onclick="DOExport.run('pdf',this)" title="Export halaman sebagai PDF">
                <i class="ph ph-file-pdf export-icon"></i>
                <span class="export-spinner"></span>
            </button>
            <button type="button" class="page-export-btn page-export-btn-img" id="doPageExpImg"
                    onclick="DOExport.run('image',this)" title="Export halaman sebagai PNG">
                <i class="ph ph-image export-icon"></i>
                <span class="export-spinner"></span>
            </button>
        </div>
    </div>

    {{-- ROW 1: Trending | Hashtag | Mention --}}
    <div class="do-row-top do-mb14">

        {{-- Trending Topics --}}
        <div class="card fade-up fade-up-d1" data-lazy="trending-topics" id="do-card-trending">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-trend-up f-18 text-primary"></i></div>
                    <h6 class="mb-0">Trending Topics</h6>
                </div>
                <div class="d-flex align-items-center gap-2" id="trendingHead">
                    <span class="badge bg-light-secondary text-muted rounded-pill">News</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('do-card-trending','trending','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('do-card-trending','trending','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div class="card-body do-body-scroll" id="trendingBody">
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;width:70%;"></div>
            </div>
        </div>

        {{-- Top Hashtag --}}
        <div class="card fade-up fade-up-d2" data-lazy="top-hashtags" id="do-card-hashtag">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-hash f-18 text-primary"></i></div>
                    <h6 class="mb-0">Top Hashtag</h6>
                </div>
                <div class="d-flex align-items-center gap-2" id="hashtagHead">
                    <span class="badge bg-light-secondary text-muted rounded-pill">X</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('do-card-hashtag','hashtag','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('do-card-hashtag','hashtag','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div class="card-body do-body-scroll" id="hashtagBody">
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;margin-bottom:8px;"></div>
                <div class="sk-block" style="height:22px;width:70%;"></div>
            </div>
        </div>

        {{-- Mention --}}
        <div class="card fade-up fade-up-d3" data-lazy="mention-combined" id="do-card-mention">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-chat-dots f-18 text-primary"></i></div>
                    <h6 class="mb-0">Mention</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light-secondary text-muted rounded-pill">All Media</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('do-card-mention','mention','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('do-card-mention','mention','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div id="mentionSkelWrap" style="padding:16px;">
                <div class="sk-block" style="height:200px;border-radius:6px;"></div>
            </div>
            <div class="do-mention-body" id="mentionBody" style="display:none;">
                <div class="do-mention-chart"><div id="chMentionPie"></div></div>
                <div class="do-mention-stats">
                    <div class="do-mstat-label">Breakdown</div>
                    <div class="do-mstat-row" id="statNewsRow">
                        <span class="do-mstat-name"><span style="background:var(--c-news);"></span>Online News</span>
                        <span class="do-mstat-val" id="mentionNewsVal">—</span>
                    </div>
                    <div class="do-mstat-row" id="statSocialRow">
                        <span class="do-mstat-name"><span style="background:var(--do-primary);"></span>Social Media</span>
                        <span class="do-mstat-val" id="mentionSocialVal">—</span>
                    </div>
                    <div class="do-mstat-divider"></div>
                    <div class="do-mstat-row" id="statTotalRow">
                        <span class="do-mstat-total-lbl">Total</span>
                        <span class="do-mstat-total-val" id="mentionTotalVal">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: SOV | Sentiment --}}
    <div class="do-row-mid do-mb14">

        {{-- Share of Voice --}}
        <div class="card" data-lazy="sov" id="do-card-sov">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-microphone f-18 text-primary"></i></div>
                    <div>
                        <h6 class="mb-0">Share of Voice</h6>
                        <small class="text-muted">Klik untuk lihat mentions per platform</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light-secondary text-muted rounded-pill">By Media</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('do-card-sov','sov','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('do-card-sov','sov','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div id="sovSkel" style="padding:16px;">
                <div class="sk-block" style="height:260px;border-radius:6px;"></div>
            </div>
            <div class="do-sov-body" id="sovBody" style="display:none;">
                <div class="do-sov-chart"><div id="chSovPie"></div></div>
                <div class="do-sov-legend">
                    <div class="do-sov-legend-title">Media Platforms</div>
                    <div id="sovLegendItems"></div>
                </div>
            </div>
        </div>

        {{-- Sentiment Timeline --}}
        <div class="card" data-lazy="sentiment-timeline" id="do-card-sentiment">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-pulse f-18 text-primary"></i></div>
                    <div>
                        <h6 class="mb-0">Sentiment Score</h6>
                        <small class="text-muted">Klik pada garis untuk lihat mentions per sentimen</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light-secondary text-muted rounded-pill">All Media</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('do-card-sentiment','sentiment','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('do-card-sentiment','sentiment','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div class="card-body" style="position:relative;height:400px;">
                <div id="chSentiment" style="width:100%;height:100%;"></div>
                <div class="sk-block sk-overlay" id="skSentiment"></div>
            </div>
        </div>
    </div>

    {{-- Buzzer Map --}}
    <div class="card do-mb14" data-lazy="buzzer-map" id="do-card-map">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-map-pin f-18 text-primary"></i></div>
                <h6 class="mb-0">Buzzer Map</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light-secondary text-muted rounded-pill">Geographic</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('do-card-map','map','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('do-card-map','map','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="do-map-wrap">
            <div class="do-map-area">
                <div id="buzzMap" style="width:100%;height:400px;"></div>
                <div id="mapSkel" style="position:absolute;inset:0;height:400px;">
                    <div class="sk-block" style="height:100%;border-radius:0;"></div>
                </div>
            </div>
            <div class="do-loc-panel">
                <div class="do-loc-title">Locations</div>
                <div class="do-loc-list" id="buzzMapList">
                    <div style="padding:10px 12px;">
                        <div class="sk-block" style="height:18px;margin-bottom:7px;border-radius:4px;"></div>
                        <div class="sk-block" style="height:18px;margin-bottom:7px;border-radius:4px;"></div>
                        <div class="sk-block" style="height:18px;border-radius:4px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- /doPageExportArea --}}
    </div>

    {{-- Export Toast --}}
    <div class="export-toast" id="doExportToast">
        <i class="ph ph-check-circle" id="doExportToastIcon"></i>
        <span id="doExportToastMsg">Exporting…</span>
    </div>

    {{-- Slide Panel --}}
    <div class="do-panel-overlay" id="doPanelOverlay" onclick="DOPanel.closeByOverlay()"></div>
    <div class="do-panel" id="doSntPanel">
        <div class="do-panel-header" id="doPanelHeader">
            <div class="do-panel-dot" id="doPanelDot"></div>
            <span class="do-panel-title" id="doPanelTitle">Mentions</span>
            <span class="do-panel-count" id="doPanelCount">…</span>
            <button class="do-panel-close" title="Refresh" id="doPanelRefreshBtn"
                style="margin-right:2px;" onclick="DOPanel.refresh()">
                <i class="ph ph-arrows-clockwise"></i>
            </button>
            <button class="do-panel-close" onclick="DOPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-panel-actions">
            <div class="do-panel-meta">
                <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
                <span id="doPanelMeta">—</span>
            </div>
            <div class="do-panel-tabs">
                <button class="do-panel-tab active" data-s="all" onclick="DOPanel.filterSent('all')">Semua</button>
                <button class="do-panel-tab neg"    data-s="neg" onclick="DOPanel.filterSent('neg')">Neg</button>
                <button class="do-panel-tab pos"    data-s="pos" onclick="DOPanel.filterSent('pos')">Pos</button>
                <button class="do-panel-tab neu"    data-s="neu" onclick="DOPanel.filterSent('neu')">Neu</button>
            </div>
        </div>
        <div class="do-panel-list" id="doPanelList"></div>
        <div class="do-detail-panel" id="doDetailPanel">
            <div class="do-dp2-header">
                <button class="do-dp2-back" onclick="DODetail.close()"><i class="ph ph-caret-left"></i></button>
                <span class="do-dp2-title" id="doDetailTitle">Detail</span>
                <button class="do-panel-close" onclick="DOPanel.close()"><i class="ph ph-x"></i></button>
            </div>
            <div class="do-dp2-body" id="doDetailBody"></div>
        </div>
    </div>

    {{-- Platform Picker --}}
    <div class="do-plat-picker" id="doPlatPicker">
        <div class="do-plat-picker-head">Pilih Platform</div>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('twit','all')">X / Twitter <span class="do-plat-dot" style="background:var(--c-twitter);"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('fb','all')">Facebook <span class="do-plat-dot" style="background:var(--c-facebook);"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('instagram','all')">Instagram <span class="do-plat-dot" style="background:var(--c-instagram);"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('youtube','all')">YouTube <span class="do-plat-dot" style="background:var(--c-youtube);"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('tiktok','all')">TikTok <span class="do-plat-dot" style="background:var(--c-tiktok);"></span></button>
    </div>

    {{-- Modals --}}
    <div class="do-modal-overlay" id="doHashtagModal">
        <div class="do-modal-box">
            <div class="do-modal-head">
                <h5 class="do-modal-head-title">Top Hashtags</h5>
                <button class="do-modal-head-close" onclick="DOListModal.close('doHashtagModal')"><i class="ph ph-x"></i></button>
            </div>
            <div class="do-modal-body" id="hashtagModalBody"></div>
        </div>
    </div>
    <div class="do-modal-overlay" id="doTrendingModal">
        <div class="do-modal-box">
            <div class="do-modal-head">
                <h5 class="do-modal-head-title">All Trending Topics</h5>
                <button class="do-modal-head-close" onclick="DOListModal.close('doTrendingModal')"><i class="ph ph-x"></i></button>
            </div>
            <div class="do-modal-body" id="trendingModalBody"></div>
        </div>
    </div>

@endsection

@section('scripts')
    {{-- Export deps --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"       crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
    'use strict';

    /* ══ CONFIG ══ */
    const DOCfg = {
        pid: {{ $projectId ? (int)$projectId : 'null' }},
        sd:  '{{ $startDate }}',
        ed:  '{{ $endDate }}',
        colorMap: {
            'Online News': '#0284c7',
            'X (Twitter)': '#1d9bf0',
            'Facebook':    '#1877f2',
            'Instagram':   '#e1306c',
            'YouTube':     '#ff0000',
            'TikTok':      '#111827',
        },
        platMeta: {
            doc:       { label:'Online News',  color:'#0284c7' },
            twit:      { label:'X / Twitter',  color:'#1d9bf0' },
            fb:        { label:'Facebook',     color:'#1877f2' },
            instagram: { label:'Instagram',    color:'#e1306c' },
            youtube:   { label:'YouTube',      color:'#ff0000' },
            tiktok:    { label:'TikTok',       color:'#111827' },
            all:       { label:'All Media',    color:'#4361EE' },
            social:    { label:'Social Media', color:'#4361EE' },
        },
        mediaKeyMap: {
            'Online News': 'doc',
            'X (Twitter)': 'twit',
            'Facebook':    'fb',
            'Instagram':   'instagram',
            'YouTube':     'youtube',
            'TikTok':      'tiktok',
        }
    };

    /* ── Helpers ── */
    const $      = id => document.getElementById(id);
    const numFmt = n  => parseInt(n||0).toLocaleString('id-ID');
    const numK   = n  => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
    const esc    = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const emptyHtml = m => `<div class="do-empty"><i class="ph ph-warning-circle"></i><span class="do-empty-txt">${m||'Tidak ada data'}</span></div>`;

    /* ── Case-insensitive media lookup ── */
    const _mediaNorm    = s => (s||'').toLowerCase().replace(/[\s()]/g,'');
    const _keyMapNorm   = Object.fromEntries(Object.entries(DOCfg.mediaKeyMap).map(([k,v])=>[_mediaNorm(k),v]));
    const _colorMapNorm = Object.fromEntries(Object.entries(DOCfg.colorMap).map(([k,v])=>[_mediaNorm(k),v]));
    const _resolveKey   = name => DOCfg.mediaKeyMap[name]  || _keyMapNorm[_mediaNorm(name)]  || '';
    const _resolveColor = name => DOCfg.colorMap[name]     || _colorMapNorm[_mediaNorm(name)] || '';

    /* ── ECharts ── */
    const DOCharts = {
        _inst: {},
        make(id) {
            if(this._inst[id]) { try{this._inst[id].dispose();}catch(e){} }
            const dom=$(id); if(!dom) return null;
            const c=echarts.init(dom,null,{renderer:'canvas'});
            this._inst[id]=c; return c;
        }
    };
    window.addEventListener('resize',()=>Object.values(DOCharts._inst).forEach(c=>{ try{if(!c.isDisposed())c.resize();}catch(e){} }));

    const EC_TT = {
        backgroundColor:'#1e293b',borderColor:'#334155',borderWidth:1,
        padding:[9,13],textStyle:{color:'#fff',fontFamily:'inherit',fontSize:12},
        extraCssText:'border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.3);'
    };
    function getPrimary(){ return getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim()||'#4361EE'; }

    /* ══ LIST MODALS ══ */
    const DOListModal = {
        open(id)  { $(id).classList.add('show'); document.body.style.overflow='hidden'; },
        close(id) { $(id).classList.remove('show'); document.body.style.overflow='auto'; },
        openTrending(topics) {
            let h=`<table class="do-tbl"><thead><tr><th style="width:30px;">#</th><th>Topic</th></tr></thead><tbody>`;
            topics.forEach((t,i)=>{ const name=t.title||t.name||t.topic||'Unknown',url=t.reference||t.url||'#'; h+=`<tr><td class="do-tbl-rank">${i+1}</td><td class="do-tbl-name">${url!=='#'?`<a href="${url}" target="_blank" class="topic-link">${esc(name)}</a>`:esc(name)}</td></tr>`; });
            h+='</tbody></table>'; $('trendingModalBody').innerHTML=h; this.open('doTrendingModal');
        },
        openHashtag(tags) {
            let h=`<table class="do-tbl"><thead><tr><th style="width:30px;">#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>`;
            tags.forEach((tag,i)=>{ let name=tag.name||tag.hashtag||tag.tag||'?'; if(!name.startsWith('#')) name='#'+name; h+=`<tr><td class="do-tbl-rank">${i+1}</td><td class="do-tbl-name" style="color:var(--do-primary);font-weight:700;">${name}</td><td class="do-tbl-num">${parseInt(tag.size||tag.mention||tag.count||0).toLocaleString()}</td></tr>`; });
            h+='</tbody></table>'; $('hashtagModalBody').innerHTML=h; this.open('doHashtagModal');
        }
    };
    window.addEventListener('click',e=>{ if(e.target===$('doHashtagModal')) DOListModal.close('doHashtagModal'); if(e.target===$('doTrendingModal')) DOListModal.close('doTrendingModal'); });

    /* ══ SLIDE PANEL ══ */
    const DOPanel = (()=>{
        const PAGE_SINGLE=25, PAGE_MULTI=10;
        const SENT_NORM={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'};
        function _normSent(item){ return SENT_NORM[String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim()]||'neu'; }

        let _allItems=[],_filtered=[];
        let _curSent='all',_curPlat=null,_curPlatForSent='all';
        let _curPage=0,_hasMore=false,_loadingMore=false;

        function showPlatPicker(x,y,sent){
            _curPlatForSent=sent||'all';
            const pp=$('doPlatPicker'); if(!pp) return;
            pp.querySelectorAll('.do-plat-btn').forEach(btn=>{ const m=(btn.getAttribute('onclick')||'').match(/openPlatform\('([^']+)'/); if(m) btn.setAttribute('onclick',`DOPanel.openPlatform('${m[1]}','${_curPlatForSent}')`); });
            const pw=180,ph=250,vw=window.innerWidth,vh=window.innerHeight;
            let left=x+10,top=y-10;
            if(left+pw>vw-8) left=x-pw-10; if(top+ph>vh-8) top=vh-ph-8; if(top<8) top=8;
            pp.style.left=left+'px'; pp.style.top=top+'px'; pp.classList.add('show');
        }
        function openPlatform(platform,sentiment){ $('doPlatPicker')?.classList.remove('show'); open(platform,sentiment||_curPlatForSent||'all'); }

        async function open(platform,sentiment){
            _curPlat=platform; _curSent=sentiment||'all'; _curPage=0; _hasMore=false; _allItems=[]; _filtered=[];
            const meta=DOCfg.platMeta[platform]||{label:platform,color:'#4361EE'};
            DODetail.close();
            $('doPanelDot').style.background=meta.color;
            $('doPanelTitle').textContent=meta.label;
            $('doPanelMeta').textContent=DOCfg.sd+' – '+DOCfg.ed;
            document.querySelectorAll('.do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s===_curSent));
            const refreshBtn=$('doPanelRefreshBtn');
            if(refreshBtn) refreshBtn.innerHTML='<i class="ph ph-arrows-clockwise" style="animation:spin .7s linear infinite;display:inline-block;"></i>';
            const list=$('doPanelList');
            list.innerHTML=`<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;
            const overlay=$('doPanelOverlay'),panel=$('doSntPanel');
            overlay.classList.remove('hiding'); panel.classList.remove('hiding');
            overlay.classList.add('show'); panel.classList.add('show');
            try { const result=await _fetchPage(platform,0); _allItems=result.items; _hasMore=result.hasMore; _filtered=_filterBySent(_allItems,_curSent); _render(list,_filtered,platform,meta.color); }
            catch(err){ list.innerHTML=`<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:13px;">Gagal memuat data<br><small>${esc(err.message)}</small></div>`; }
            finally { if(refreshBtn) refreshBtn.innerHTML='<i class="ph ph-arrows-clockwise"></i>'; }
        }

        async function refresh(){ if(_curPlat) open(_curPlat,_curSent); }

        async function loadMore(){
            if(_loadingMore||!_hasMore) return; _loadingMore=true;
            const btn=document.getElementById('_doLMBtn'); if(btn){btn.textContent='Memuat…';btn.disabled=true;}
            try {
                _curPage++;
                const result=await _fetchPage(_curPlat,_curPage); _allItems=[..._allItems,...result.items]; _hasMore=result.hasMore;
                document.getElementById('_doLMWrap')?.remove();
                const list=$('doPanelList'), meta=DOCfg.platMeta[_curPlat]||{color:'#4361EE'};
                const newFil=_filterBySent(result.items,_curSent); _filtered=_filterBySent(_allItems,_curSent);
                if(newFil.length) list.insertAdjacentHTML('beforeend',newFil.map(it=>_renderItem(it,_curPlat,meta.color)).join(''));
                if(_hasMore) list.insertAdjacentHTML('beforeend',_lmHtml());
                else list.insertAdjacentHTML('beforeend',`<div style="padding:9px;text-align:center;font-size:10px;color:var(--do-slate-400);font-weight:600;border-top:1px dashed var(--do-slate-200);">✓ Semua mentions sudah dimuat</div>`);
            } catch(err){ _curPage--; document.getElementById('_doLMWrap')?.remove(); $('doPanelList').insertAdjacentHTML('beforeend',_lmHtml()); }
            finally { _loadingMore=false; }
        }

        function close(){
            const overlay=$('doPanelOverlay'),panel=$('doSntPanel');
            panel.classList.add('hiding'); overlay.classList.add('hiding');
            setTimeout(()=>{ panel.classList.remove('show','hiding'); overlay.classList.remove('show','hiding'); DODetail.close(); },240);
        }
        function closeByOverlay(){ close(); }

        function filterSent(sent){
            _curSent=sent; document.querySelectorAll('.do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s===sent));
            _filtered=_filterBySent(_allItems,sent); const meta=DOCfg.platMeta[_curPlat]||{color:'#4361EE'};
            _render($('doPanelList'),_filtered,_curPlat,meta.color);
        }
        function _filterBySent(items,sent){ return sent==='all'?items:items.filter(i=>_normSent(i)===sent); }

        function _extractItems(d){
            if(Array.isArray(d?.data?.data)) return d.data.data; if(Array.isArray(d?.data)) return d.data;
            if(Array.isArray(d?.statuses)) return d.statuses; if(Array.isArray(d?.tweets)) return d.tweets;
            if(Array.isArray(d?.results)) return d.results; if(Array.isArray(d?.posts)) return d.posts;
            if(Array.isArray(d)) return d;
            if(d?.data&&typeof d.data==='object'&&!Array.isArray(d.data)){const vals=Object.values(d.data);if(vals.length&&typeof vals[0]==='object')return vals;}
            return [];
        }

        async function _fetchPage(platform,page){
            const isMulti=['all','social'].includes(platform), size=isMulti?PAGE_MULTI:PAGE_SINGLE, start=page*size;
            if(platform==='all'){ const plats=['doc','twit','fb','instagram','youtube','tiktok']; const res=await Promise.allSettled(plats.map(p=>_fetchOnePage(p,start,size))); return {items:res.flatMap(r=>r.status==='fulfilled'?r.value.items:[]),hasMore:res.some(r=>r.status==='fulfilled'&&r.value.hasMore)}; }
            if(platform==='social'){ const plats=['twit','fb','instagram','youtube','tiktok']; const res=await Promise.allSettled(plats.map(p=>_fetchOnePage(p,start,size))); return {items:res.flatMap(r=>r.status==='fulfilled'?r.value.items:[]),hasMore:res.some(r=>r.status==='fulfilled'&&r.value.hasMore)}; }
            return _fetchOnePage(platform,start,size);
        }

        async function _fetchOnePage(platform,start,size){
            const fetchRows=size+1, q=`project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}&rows=${fetchRows}&start=${start}`;
            if(platform==='youtube'){ for(const sub of ['postbylike','postbyview','postbydate','postbycomment',null]){ try{ const url=sub?`/mk/api/news/ytb-top-status?${q}&sub=${sub}`:`/mk/api/news/ytb-top-status?${q}`; const r=await fetch(url); if(!r.ok) continue; const raw=_extractItems(await r.json()); if(raw.length>0) return {items:raw.slice(0,size).map(i=>({...i,_platform:'youtube'})),hasMore:raw.length>size}; }catch(e){continue;} } return {items:[],hasMore:false}; }
            if(platform==='instagram'){ for(const sub of ['postbylike','postbycomment','postbydate',null]){ try{ const url=sub?`/mk/api/news/ig-top-status?${q}&sub=${sub}`:`/mk/api/news/ig-top-status?${q}`; const r=await fetch(url); if(!r.ok) continue; const raw=_extractItems(await r.json()); if(raw.length>0) return {items:raw.slice(0,size).map(i=>({...i,_platform:'instagram'})),hasMore:raw.length>size}; }catch(e){continue;} } return {items:[],hasMore:false}; }
            const eps={doc:`/mk/api/news/mentions?${q}`,twit:`/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,fb:`/mk/api/news/fb-top-status?${q}&sub=fblike`,tiktok:`/mk/api/news/tiktok-top-status?${q}&sub=postbylike`};
            const url=eps[platform]; if(!url) return {items:[],hasMore:false};
            const ctrl=new AbortController(), tid=setTimeout(()=>ctrl.abort(),30000);
            try {
                const r=await fetch(url,{signal:ctrl.signal}); clearTimeout(tid); if(!r.ok) return {items:[],hasMore:false};
                let raw=_extractItems(await r.json());
                if(platform==='twit'&&raw.length===0){ try{ const r2=await fetch(`/mk/api/news/mentions?${q}&media_type=twit`); raw=_extractItems(await r2.json()).filter(m=>['twit','rt'].includes(String(m.media_type||'').toLowerCase())||['twit','rt'].includes(String(m.tcode||'').toLowerCase())); }catch(e2){} }
                if(platform==='doc'){ const hasMeta=raw.some(m=>m.tcode||m.media_type); if(hasMeta){ const filtered=raw.filter(m=>{ const tc=String(m.tcode||'').toLowerCase(),mt=String(m.media_type||'').toLowerCase(); return !tc||!mt||tc==='berita'||mt==='berita'||mt==='doc'||mt==='news'||mt==='online'||mt==='article'; }); if(filtered.length>0) raw=filtered; } }
                return {items:raw.slice(0,size).map(i=>({...i,_platform:platform})),hasMore:raw.length>size};
            } catch(e){ clearTimeout(tid); return {items:[],hasMore:false}; }
        }

        function _lmHtml(){ return `<div id="_doLMWrap" style="padding:11px 14px;text-align:center;background:var(--do-slate-50);border-top:1px dashed var(--do-slate-200);"><button id="_doLMBtn" onclick="DOPanel.loadMore()" style="display:inline-flex;align-items:center;gap:5px;padding:6px 20px;background:var(--do-primary);color:#fff;border:none;border-radius:5px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;transition:filter .14s;" onmouseover="this.style.filter='brightness(1.12)'" onmouseout="this.style.filter=''"><i class="ph ph-arrow-circle-down" style="font-size:13px;"></i> Muat Lebih Banyak</button></div>`; }

        function _renderItem(item,platform,accentColor){
            const plat=item._platform||platform, meta=DOCfg.platMeta[plat]||{label:plat,color:accentColor};
            const rawName=(()=>{ if(plat==='fb') return item.from_name||item.page_name||null; if(plat==='instagram') return item.username||item.user_name||null; if(plat==='tiktok') return item.author_nickname||item.nickname||item.author?.nickname||null; if(plat==='youtube') return item.channel_title||item.channel_name||item.snippet?.channelTitle||null; if(plat==='twit'){const ao=typeof item.author==='object'?item.author:(()=>{try{return JSON.parse(item.author||'{}');}catch(e){return {};}})(); return item.name||ao?.name||ao?.scr_name||item.author_name||null;} return null; })();
            const name=(rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'Unknown').trim();
            const dName=/^\d{10,}$/.test(name)?`User ${name.slice(-4)}`:name;
            const rawH=(()=>{ if(plat==='instagram') return item.username||''; if(plat==='twit'){const ao=typeof item.author==='object'?item.author:(()=>{try{return JSON.parse(item.author||'{}');}catch(e){return {};}})(); return item.screen_name||item.author_scr_name||ao?.scr_name||ao?.username||'';} return item.author_scr_name||item.screen_name||item.username||''; })().trim();
            const handle=(()=>{ if(!rawH) return ''; const w=['twit','instagram','tiktok'].includes(plat)?(rawH.startsWith('@')?rawH:'@'+rawH):rawH; return w.replace(/^@/,'').toLowerCase()===dName.toLowerCase()?'':w; })();
            const text=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,150);
            const ao=(()=>{if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return {};}})();
            const av=(item.avatar_url||item.profile_image_url||ao?.image||item.author_image||item.profile_image||item.thumbnail||'').trim();
            const dt=(item.date_created||item.created_at||'').split('T')[0];
            const sent=_normSent(item), words=dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
            const ini=(words.length>=2?words[0][0]+words[words.length-1][0]:(words[0]?.[0]||dName[0]||'?')).toUpperCase();
            const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini.replace(/['"]/g,'')}';"/>`:ini;
            const enc=encodeURIComponent(JSON.stringify(item));
            const sentCls=sent==='pos'?'pos':sent==='neg'?'neg':'neu', sentLbl=sent==='pos'?'Pos':sent==='neg'?'Neg':'Neu';
            return `<div class="do-panel-item" onclick="DODetail.openEncoded('${enc}','${plat}')"><div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div><div class="do-panel-item-body"><div class="do-panel-author">${esc(dName)}</div>${handle?`<div class="do-panel-handle">${esc(handle)}</div>`:''}<div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div><div class="do-panel-footer"><span class="do-sent-badge do-sent-badge--${sentCls}">${sentLbl}</span><span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span><span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>${dt?`<span style="margin-left:auto;">${dt}</span>`:''}</div></div></div>`;
        }

        function _render(list,items,platform,accentColor){
            if(!items.length) list.innerHTML=`<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>`;
            else list.innerHTML=items.map(it=>_renderItem(it,platform,accentColor)).join('');
            if(_hasMore) list.insertAdjacentHTML('beforeend',_lmHtml());
        }

        return {open,close,closeByOverlay,showPlatPicker,openPlatform,filterSent,loadMore,refresh};
    })();

    /* ══ DETAIL SUB-PANEL ══ */
    const DODetail = {
        openEncoded(enc,plat){ try{this.open(JSON.parse(decodeURIComponent(enc)),plat);}catch(e){} },
        open(item,platform){
            const panel=$('doDetailPanel'),body=$('doDetailBody'),title=$('doDetailTitle'); if(!panel||!body) return;
            const meta=DOCfg.platMeta[platform]||{label:platform,color:'#4361EE'};
            const SENT_MAP={pos:'Positif',neg:'Negatif',neu:'Netral'}, SENT_BGS={pos:'do-dp2-sent--pos',neg:'do-dp2-sent--neg',neu:'do-dp2-sent--neu'};
            const rawS=String(item.class_sentiment||item.sentiment||'0').toLowerCase();
            const sent={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'}[rawS]||'neu';
            const rawName=(()=>{if(platform==='fb') return item.from_name||item.page_name||null;if(platform==='instagram') return item.username||null;if(platform==='tiktok') return item.author_nickname||item.nickname||item.author?.nickname||null;if(platform==='youtube') return item.channel_title||item.channel_name||item.snippet?.channelTitle||null;if(platform==='twit') return item.name||item.user?.name||item.author_name||null; return null;})();
            const name=(rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'Unknown').trim();
            const handle=((platform==='instagram'?item.username:'')||item.author_scr_name||item.screen_name||item.username||'').trim();
            const content=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
            const av=(item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||'').trim();
            const url=item.url||item.link||'', dt=item.date_created||item.created_at||'';
            title.textContent=name;
            const words=name.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
            const ini=(words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||name[0]||'?')).toUpperCase();
            const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.parentElement.textContent='${ini}';">`:ini;
            let dtFmt=''; if(dt){try{dtFmt=new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){dtFmt=dt.split('T')[0];}}
            let mediaHtml='';
            if(platform==='youtube'){ const ytId=(url.match(/[?&]v=([a-zA-Z0-9_-]{11})/)||url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/)||url.match(/embed\/([a-zA-Z0-9_-]{11})/)||url.match(/shorts\/([a-zA-Z0-9_-]{11})/)||[])[1]||(item.video_id||item.youtube_id||''); const thumb=item.thumbnail||item.thumbnail_url||item.image_url||item.media_url||(ytId?`https://img.youtube.com/vi/${ytId}/hqdefault.jpg`:''); if(ytId){const eId=`yt_${ytId}_${Date.now()}`; mediaHtml=`<div class="do-dp2-media do-dp2-media--video" id="${eId}" style="position:relative;cursor:pointer;border-radius:6px;overflow:hidden;background:#000;" onclick="document.getElementById('${eId}').innerHTML='<iframe width=\\\"100%\\\" height=\\\"280\\\" src=\\\"https://www.youtube.com/embed/${ytId}?autoplay=1&controls=1\\\" frameborder=\\\"0\\\" allow=\\\"accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture\\\" allowfullscreen></iframe>'"><img src="${thumb||`https://img.youtube.com/vi/${ytId}/hqdefault.jpg`}" style="width:100%;height:220px;object-fit:cover;display:block;" onerror="this.src='https://img.youtube.com/vi/${ytId}/mqdefault.jpg'"><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.25);"><div style="width:52px;height:52px;background:#ff0000;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.4);"><i class="ph ph-play-fill" style="font-size:22px;color:#fff;margin-left:3px;"></i></div></div></div>`;}else if(thumb){mediaHtml=`<div class="do-dp2-media"><img src="${esc(thumb)}" onerror="this.parentElement.style.display='none'" style="border-radius:6px;width:100%;max-height:220px;object-fit:cover;"></div>`;} }
            else if(platform==='tiktok'){ const tid=(url.match(/\/video\/(\d+)/)||url.match(/\/v\/(\d+)/)||[])[1]||(item.video_id||item.aweme_id||''); const thumb=item.thumbnail||item.cover||item.image_url||item.video_cover||item.media_url||''; if(tid){const eId=`tt_${tid}_${Date.now()}`; mediaHtml=`<div id="${eId}" style="position:relative;cursor:pointer;background:#111827;border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center;height:260px;" onclick="DODetail.loadTikTokEmbed('${eId}','${tid}')">${thumb?`<img src="${esc(thumb)}" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:.65;pointer-events:none;">`:''}<div style="position:relative;z-index:2;width:56px;height:56px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.6);"><i class="ph ph-play-fill" style="font-size:24px;color:#111827;margin-left:3px;"></i></div><div style="position:absolute;bottom:8px;right:8px;background:#111827;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:3px;letter-spacing:.5px;">TIKTOK</div></div>`;} else if(thumb){mediaHtml=`<div class="do-dp2-media"><img src="${esc(thumb)}" onerror="this.parentElement.style.display='none'" style="border-radius:6px;max-height:320px;object-fit:cover;width:100%;display:block;"></div>`;} }
            else if(platform==='instagram'){ const thumb=item.image_url||item.thumbnail||item.media_url||item.picture||item.display_url||''; const isVid=(item.media_type||'').toLowerCase()==='video'||(item.product_type||'').toLowerCase()==='igtv'||(item.product_type||'').toLowerCase()==='reels'; if(thumb) mediaHtml=`<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;"><img src="${esc(thumb)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:320px;object-fit:cover;display:block;">${isVid?`<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);border-radius:6px;"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:#e1306c;margin-left:3px;"></i></div></div>`:''}</div>`; }
            else if(platform==='fb'){ const imgUrl=item.image_url||item.thumbnail||item.media_url||item.picture||item.display_url||item.story_img||''; const isVid=(item.type||'').includes('video')||!!item.video_id; if(imgUrl) mediaHtml=`<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;"><img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:320px;object-fit:cover;display:block;">${isVid?`<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);border-radius:6px;"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:#1877f2;margin-left:3px;"></i></div></div>`:''}</div>`; }
            else if(platform==='twit'){ const imgUrl=item.image_url||item.media_url||item.thumbnail||item.display_url||item.media?.media_url||''; const isVid=String(item.media_type||'').toLowerCase()==='video'||String(item.type||'').toLowerCase()==='video'; if(imgUrl) mediaHtml=`<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;"><img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:320px;object-fit:cover;display:block;">${isVid?`<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);border-radius:6px;"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:#1d9bf0;margin-left:3px;"></i></div></div>`:''}</div>`; }
            else { const imgUrl=item.image_url||item.thumbnail||item.featured_image||item.banner_image||item.media_url||item.picture||''; if(imgUrl) mediaHtml=`<div class="do-dp2-media" style="border-radius:6px;overflow:hidden;background:#e5e7eb;"><img src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:260px;object-fit:cover;display:block;"></div>`; }
            const statsMap={twit:[['Retweet',item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0],['Quote',item.num_quote||0]],fb:[['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],instagram:[['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],youtube:[['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||0]],tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],doc:[['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]]};
            const stats=statsMap[platform]||[];
            const statsHtml=stats.some(s=>parseInt(s[1])>0)?`<div class="do-dp2-stats">${stats.map(([l,v])=>`<div class="do-dp2-stat"><div class="do-dp2-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="do-dp2-stat-lbl">${l}</div></div>`).join('')}</div>`:'';
            const handleDisp=handle&&!handle.replace('@','').toLowerCase().startsWith(name.toLowerCase().slice(0,4))?(handle.startsWith('@')?handle:'@'+handle):'';
            body.innerHTML=`<div class="do-dp2-avatar-row"><div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div><div><div class="do-dp2-name">${esc(name)}</div>${handleDisp?`<div class="do-dp2-handle">${esc(handleDisp)}</div>`:''}<span class="do-dp2-plat-badge" style="background:${meta.color}22;color:${meta.color};">${meta.label}</span></div></div>${dtFmt?`<div class="do-dp2-meta"><span>${dtFmt}</span></div>`:''}<div class="do-dp2-sent ${SENT_BGS[sent]}">${SENT_MAP[sent]}</div>${mediaHtml}${content?`<div class="do-dp2-content">${esc(content)}</div>`:''}${statsHtml}${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out"></i> Lihat ${meta.label} Asli</a>`:''}`;
            panel.classList.add('show');
        },
        close(){ $('doDetailPanel')?.classList.remove('show'); document.querySelectorAll('.do-detail-panel iframe').forEach(iframe=>{iframe.src=iframe.src;}); },
        loadTikTokEmbed(embedId,videoIdOrUrl){
            const el=$(embedId); if(!el) return;
            let tid=''; if(/^\d+$/.test(videoIdOrUrl)){tid=videoIdOrUrl;}else{tid=(videoIdOrUrl.match(/\/video\/(\d+)/)||videoIdOrUrl.match(/\/v\/(\d+)/)||[])[1]||'';}
            if(!tid){window.open(videoIdOrUrl,'_blank');return;}
            el.style.cursor='default'; el.style.minHeight='560px'; el.style.height='auto'; el.style.background='#111827'; el.style.borderRadius='6px'; el.style.overflow='hidden';
            el.innerHTML=`<iframe src="https://www.tiktok.com/embed/v2/${tid}" width="100%" height="560" frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen style="display:block;border:none;border-radius:6px;background:#111827;"></iframe>`;
        }
    };

    /* ══ LAZY LOADER ══ */
    const DOLoader = {
        loaded: new Set(),
        init(){
            const obs=new IntersectionObserver(entries=>{ entries.forEach(e=>{ if(e.isIntersecting){const card=e.target,sec=card.dataset.lazy;if(!this.loaded.has(sec)){this.loaded.add(sec);this.load(sec);obs.unobserve(card);}} }); },{rootMargin:'100px',threshold:.05});
            document.querySelectorAll('[data-lazy]').forEach(c=>obs.observe(c));
        },
        async load(sec){
            try { switch(sec){ case 'trending-topics': await this.loadTrending(); break; case 'top-hashtags': await this.loadHashtags(); break; case 'mention-combined': await this.loadMentions(); break; case 'sov': await this.loadSov(); break; case 'sentiment-timeline': await this.loadSentLine(); break; case 'buzzer-map': await this.loadMap(); break; } }
            catch(err){ console.error(`Error loading ${sec}:`,err); }
        },

        async loadTrending(){
            const r=await fetch(`/mk/api/trending-topics`); const d=await r.json();
            const body=$('trendingBody'),topics=d.data||[];
            if(!topics.length){body.innerHTML=emptyHtml();return;}
            if(topics.length>10) $('trendingHead').insertAdjacentHTML('beforeend',`<button class="do-view-all" onclick="DOListModal.openTrending(window._doTopics)"><i class="ph ph-caret-right"></i>All</button>`);
            window._doTopics=topics;
            let h=`<table class="do-tbl"><thead><tr><th style="width:22px;">#</th><th>Topic</th></tr></thead><tbody>`;
            topics.slice(0,10).forEach((t,i)=>{ const name=t.title||t.name||t.topic||'Unknown',url=t.reference||t.url||'#'; h+=`<tr><td class="do-tbl-rank">${i+1}</td><td class="do-tbl-name">${url!=='#'?`<a href="${url}" target="_blank" class="topic-link">${esc(name)}</a>`:esc(name)}</td></tr>`; });
            h+='</tbody></table>'; body.innerHTML=h;
        },

        async loadHashtags(){
            const r=await fetch(`/mk/api/top-hashtags?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`); const d=await r.json();
            const body=$('hashtagBody');
            let tags=d.data&&Array.isArray(d.data.hashtags)?d.data.hashtags:(Array.isArray(d.data)?d.data:[]);
            if(!tags.length){body.innerHTML=emptyHtml();return;}
            if(tags.length>5) $('hashtagHead').insertAdjacentHTML('beforeend',`<button class="do-view-all" onclick="DOListModal.openHashtag(window._doHashtags)"><i class="ph ph-caret-right"></i>All</button>`);
            window._doHashtags=tags;
            let h=`<table class="do-tbl"><thead><tr><th style="width:22px;">#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>`;
            tags.slice(0,5).forEach((tag,i)=>{ let name=tag.name||tag.hashtag||tag.tag||'?'; if(!name.startsWith('#')) name='#'+name; h+=`<tr><td class="do-tbl-rank">${i+1}</td><td class="do-tbl-name" style="color:var(--do-primary);font-weight:700;">${name}</td><td class="do-tbl-num">${parseInt(tag.size||tag.mention||tag.count||0).toLocaleString()}</td></tr>`; });
            h+='</tbody></table>'; body.innerHTML=h;
        },

        async loadMentions(){
            const r=await fetch(`/mk/api/mention-counts?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`); const d=await r.json();
            const social=Number(d.social||0),news=Number(d.news||0),total=social+news;
            $('mentionNewsVal').textContent=numFmt(news); $('mentionSocialVal').textContent=numFmt(social); $('mentionTotalVal').textContent=numFmt(total);
            $('mentionSkelWrap').style.display='none'; $('mentionBody').style.display='flex';
            $('statNewsRow').onclick=()=>DOPanel.open('doc','all');
            $('statSocialRow').onclick=e=>DOPanel.showPlatPicker(e.clientX,e.clientY,'all');
            $('statTotalRow').onclick=()=>DOPanel.open('all','all');
            if(total>0){
                const chart=DOCharts.make('chMentionPie'); if(chart){
                    const primary=getPrimary();
                    chart.setOption({animation:true,animationDuration:800,animationEasing:'cubicInOut',tooltip:{...EC_TT,trigger:'item',confine:true,formatter:p=>{const pct=total>0?p.value/total*100:0;const dot=`<span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};margin-right:6px;flex-shrink:0;"></span>`;return `<div style="display:flex;align-items:center;font-weight:700;margin-bottom:4px;">${dot}${p.name}</div><div style="padding-left:14px;">${numFmt(p.value)} mentions</div><div style="padding-left:14px;opacity:.7;">${pct<1&&pct>0?'<1':Math.round(pct)}% dari total</div>`;}},legend:{show:true,bottom:0,orient:'horizontal',textStyle:{fontFamily:'inherit',fontSize:10,fontWeight:'600',color:'var(--do-slate-400)'},icon:'circle',itemWidth:7,itemHeight:7,itemGap:10},series:[{type:'pie',radius:['48%','72%'],center:['50%','45%'],avoidLabelOverlap:true,itemStyle:{borderRadius:4,borderColor:'#fff',borderWidth:2},label:{show:false},emphasis:{label:{show:true,fontSize:11,fontWeight:'700',fontFamily:'inherit',formatter:p=>{const pct=total>0?p.value/total*100:0;return `{n|${p.name}}\n{v|${numK(p.value)}}\n{p|${pct<1&&pct>0?'<1%':Math.round(pct)+'%'}}`;},rich:{n:{fontSize:9,color:'var(--do-slate-400)',fontWeight:'600',lineHeight:14},v:{fontSize:13,color:'var(--do-slate-900)',fontWeight:'700',lineHeight:18},p:{fontSize:9,color:primary,fontWeight:'700',lineHeight:14}}},scale:true,scaleSize:4},data:[{name:'Online News',value:news,itemStyle:{color:'#0284c7'}},{name:'Social Media',value:social,itemStyle:{color:primary}}]}]});
                    chart.on('click',p=>{if(p.name==='Online News') DOPanel.open('doc','all'); else DOPanel.showPlatPicker(window.innerWidth/2,window.innerHeight/2,'all');});
                    chart.on('mouseover',p=>{if(p.componentType==='series') chart.getDom().style.cursor='pointer';}); chart.on('mouseout',()=>{chart.getDom().style.cursor='default';});
                }
            }
        },

        async loadSov(){
            const r=await fetch(`/mk/api/sentiment-by-media?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`); const d=await r.json();
            const data=d.data||[]; $('sovSkel').style.display='none';
            const sovBody=$('sovBody'); if(sovBody) sovBody.style.display='flex';
            if(!data.length){if(sovBody) sovBody.innerHTML=emptyHtml(); return;}
            const fallback=['#22c55e','#1d9bf0','#1877f2','#e1306c','#ff0000','#111827','#7c3aed','#f59e0b'];
            const totalAll=data.reduce((s,m)=>s+(m.total||0),0);
            const labels=data.map(m=>m.media), counts=data.map(m=>m.total||0);
            const colors=data.map((m,i)=>_resolveColor(m.media)||fallback[i%fallback.length]);
            const primary=getPrimary();
            const chart=DOCharts.make('chSovPie');
            if(chart){
                chart.setOption({animation:true,animationDuration:800,animationEasing:'cubicInOut',tooltip:{...EC_TT,trigger:'item',confine:true,formatter:p=>{const pct=totalAll>0?p.value/totalAll*100:0;return `<div style="font-weight:700;margin-bottom:3px;">${p.name}</div><div>${numFmt(p.value)} mentions</div><div style="opacity:.7;">${pct<1&&pct>0?'<1':pct.toFixed(1)}% dari total</div>`;}},legend:{show:false},series:[{type:'pie',radius:['48%','76%'],center:['50%','50%'],avoidLabelOverlap:true,itemStyle:{borderRadius:5,borderColor:'#fff',borderWidth:2.5},label:{show:false},emphasis:{label:{show:true,fontSize:12,fontWeight:'700',fontFamily:'inherit',formatter:p=>{const pct=totalAll>0?p.value/totalAll*100:0;return `{n|${p.name}}\n{v|${numK(p.value)}}\n{p|${pct<1&&pct>0?'<1%':pct.toFixed(1)+'%'}}`;},rich:{n:{fontSize:9,color:'var(--do-slate-400)',fontWeight:'600',lineHeight:14},v:{fontSize:14,color:'var(--do-slate-900)',fontWeight:'700',lineHeight:20},p:{fontSize:9,color:primary,fontWeight:'800',lineHeight:14}}},scale:true,scaleSize:4},data:labels.map((lb,i)=>({name:lb,value:counts[i],itemStyle:{color:colors[i]}}))}]});
                chart.on('click',p=>{const k=_resolveKey(p.name); if(!k) return; DOPanel.open(k,'all');});
                chart.on('mouseover',p=>{if(p.componentType==='series') chart.getDom().style.cursor='pointer';}); chart.on('mouseout',()=>{chart.getDom().style.cursor='default';});
            }
            const legendEl=$('sovLegendItems');
            if(legendEl){ legendEl.innerHTML=data.map((m,i)=>{ const pctF=totalAll>0?m.total/totalAll*100:0; const pctD=pctF===0?'0%':pctF<1?'<1%':pctF.toFixed(1)+'%'; const k=_resolveKey(m.media)||_resolveKey(m.media_key)||''; return `<div class="do-sov-item" ${k?`onclick="DOPanel.open('${k}','all')" title="Lihat mentions ${m.media}"`:''}><div class="do-sov-item-row"><span class="do-sov-dot" style="background:${colors[i]};"></span><span class="do-sov-name">${m.media}</span><span class="do-sov-pct" style="color:${colors[i]};">${pctD}</span></div><div class="do-sov-bar-wrap"><div class="do-sov-bar" style="width:${Math.max(pctF,pctF>0?2:0)}%;background:${colors[i]};"></div></div></div>`; }).join(''); }
        },

        _apexSentiment:null,
        async loadSentLine(){
            const r=await fetch(`/mk/api/sentiment-timeline?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`); const d=await r.json();
            $('skSentiment').style.display='none';
            const xLabels=(d.dates||[]).map(dt=>{try{const o=new Date(dt+'T00:00:00');return `${o.getDate()}/${o.getMonth()+1}`;}catch(e){return dt;}});
            const sentNames=['Total','Positive','Neutral','Negative'], sentMap={Total:'all',Positive:'pos',Neutral:'neu',Negative:'neg'};
            const options={chart:{type:'area',height:360,fontFamily:'inherit',background:'transparent',toolbar:{show:false},animations:{enabled:true,easing:'linear',dynamicAnimation:{speed:1000}},events:{markerClick:(e,ctx,cfg)=>{DOPanel.open('all',sentMap[sentNames[cfg.seriesIndex]]||'all');}}},series:[{name:'Total',data:d.values||[]},{name:'Positive',data:d.sentiment?.positive||[]},{name:'Neutral',data:d.sentiment?.neutral||[]},{name:'Negative',data:d.sentiment?.negative||[]}],colors:['#4680ff','#10B981','#94A3B8','#EF4444'],xaxis:{categories:xLabels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontFamily:'inherit',fontSize:'11px',fontWeight:600,colors:'#94A3B8'}}},yaxis:{labels:{formatter:v=>numK(v),style:{fontFamily:'inherit',fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false},axisTicks:{show:false}},fill:{opacity:0.3},stroke:{curve:'smooth',width:2.5},markers:{size:xLabels.length<=20?5:0,strokeWidth:2,strokeColors:'#fff',hover:{size:7}},dataLabels:{enabled:xLabels.length<=20,formatter:v=>v>0?numK(v):'',style:{fontSize:'10px',fontFamily:'inherit',fontWeight:'700'},background:{enabled:true,borderRadius:3,borderWidth:0,padding:3,opacity:0.9},offsetY:-6},grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}}},legend:{position:'bottom',horizontalAlign:'left',fontFamily:'inherit',fontSize:'11px',fontWeight:'600',labels:{colors:'#94A3B8'},markers:{width:9,height:9,radius:50},itemMargin:{horizontal:14,vertical:4}},tooltip:{shared:true,intersect:false,style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>numFmt(v)+' mentions'}}};
            if(this._apexSentiment){try{this._apexSentiment.destroy();}catch(e){}}
            const el=$('chSentiment'); if(!el) return;
            el.innerHTML=''; el.style.cursor='pointer';
            this._apexSentiment=new ApexCharts(el,options); this._apexSentiment.render();
            el.addEventListener('click',e=>{ const t=e.target; if(!t.classList.contains('apexcharts-marker')&&!t.closest('.apexcharts-data-labels')&&!t.closest('.apexcharts-datalabel')) DOPanel.open('all','all'); });
        },

        async loadMap(){
            const r=await fetch(`/mk/api/geo-users?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`); const d=await r.json();
            $('mapSkel').style.display='none';
            const rows=d.data||[], primary=getPrimary();
            const mapResult=this.renderMap('buzzMap',rows,primary);
            this.buildLocationPanel('buzzMapList',rows,mapResult);
        },
        renderMap(elId,rows,primary){
            const map=L.map(elId,{center:[-2.5,118],zoom:5,scrollWheelZoom:false});
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',{attribution:'© OpenStreetMap, © CARTO',subdomains:'abcd',maxZoom:19}).addTo(map);
            if(!rows.length) return {map,markerRefs:[]};
            const maxCount=Math.max(...rows.map(p=>parseInt(p.count||0))), markerRefs=[];
            rows.forEach(p=>{
                const lat=parseFloat(p.latitude||0),lng=parseFloat(p.longitude||0);
                if(lat===0&&lng===0){markerRefs.push(null);return;}
                const name=p.name||'Unknown',count=parseInt(p.count||0);
                if(count>=10) L.circle([lat,lng],{radius:Math.max(5000,Math.min(Math.sqrt(count)*2500,50000)),fillColor:primary,color:primary,weight:1,opacity:.2,fillOpacity:Math.min(.15+(count/maxCount)*.4,.55)}).addTo(map);
                const pin=L.marker([lat,lng],{icon:L.divIcon({className:'',html:`<div style="width:12px;height:12px;background:${primary};border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>`,iconSize:[12,12],iconAnchor:[6,6]})}).addTo(map).bindPopup(`<div style="font-family:inherit;text-align:center;padding:6px;"><div style="font-weight:700;font-size:13px;color:#0f172a;margin-bottom:5px;">${name}</div><div style="font-size:20px;font-weight:800;color:${primary};">${count.toLocaleString()}</div><div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">mentions</div></div>`);
                markerRefs.push({marker:pin,lat,lng});
                const lbl=count>999?(count/1000).toFixed(1)+'k':count;
                L.marker([lat,lng],{icon:L.divIcon({className:'',html:`<div style="font-family:inherit;font-size:10px;font-weight:800;color:#fff;background:${primary};padding:2px 7px;border-radius:3px;border:1.5px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);white-space:nowrap;">${lbl}</div>`,iconSize:[36,18],iconAnchor:[18,24]}),interactive:false}).addTo(map);
            });
            return {map,markerRefs};
        },
        buildLocationPanel(listId,rows,mapResult){
            const listEl=$(listId); if(!listEl) return;
            const {map,markerRefs}=mapResult;
            const valid=rows.filter(p=>!(parseFloat(p.latitude||0)===0&&parseFloat(p.longitude||0)===0));
            if(!valid.length){listEl.innerHTML='<div class="do-empty" style="padding:20px 12px;font-size:11px;">No location data</div>';return;}
            const sorted=[...valid].sort((a,b)=>parseInt(b.count||0)-parseInt(a.count||0));
            listEl.innerHTML=sorted.map((p,rank)=>{const name=p.name||'Unknown',count=parseInt(p.count||0);const lbl=count>999?(count/1000).toFixed(1)+'k':count;return `<div class="do-loc-item" data-name="${name}"><span class="do-loc-rank">${rank+1}</span><div class="do-loc-info"><div class="do-loc-name" title="${name}">${name}</div><div class="do-loc-count">${lbl} mentions</div></div><div class="do-loc-dot"></div></div>`;}).join('');
            listEl.querySelectorAll('.do-loc-item').forEach(item=>{
                item.addEventListener('click',()=>{
                    const name=item.dataset.name,target=valid.find(p=>(p.name||'Unknown')===name);if(!target) return;
                    const lat=parseFloat(target.latitude||0),lng=parseFloat(target.longitude||0);if(lat===0&&lng===0) return;
                    map.flyTo([lat,lng],8,{animate:true,duration:1});
                    const ref=markerRefs.find(r=>r&&Math.abs(r.lat-lat)<.001&&Math.abs(r.lng-lng)<.001);
                    if(ref) setTimeout(()=>ref.marker.openPopup(),800);
                    listEl.querySelectorAll('.do-loc-item').forEach(i=>i.classList.remove('active')); item.classList.add('active');
                });
            });
        }
    };

    /* ══ EXPORT MODULE ══ */
    const DOExport = (()=>{
        let _toastTimer=null;

        function _toast(msg,type='default',duration=3200){
            const t=$('doExportToast'),m=$('doExportToastMsg'),ico=$('doExportToastIcon'); if(!t||!m) return;
            m.textContent=msg; t.className='export-toast show '+(type!=='default'?type:'');
            const icons={success:'ph-check-circle',error:'ph-x-circle',default:'ph-spinner'};
            ico.className='ph '+(icons[type]||icons.default);
            clearTimeout(_toastTimer); _toastTimer=setTimeout(()=>t.classList.remove('show'),duration);
        }

        function _btnState(btn,loading){ if(!btn) return; btn.disabled=loading; btn.classList.toggle('exporting',loading); }

        /* ── Resize all charts before capture ── */
        function _resizeAll(){
            Object.values(DOCharts._inst).forEach(c=>{try{if(!c.isDisposed())c.resize();}catch(e){}});
            if(window._apexSentimentInst){try{window._apexSentimentInst.updateOptions({});}catch(e){}}
        }

        /* ── Full-page capture ── */
        async function _capturePage(){
            const area=$('doPageExportArea'); if(!area) throw new Error('Export area tidak ditemukan');
            window.scrollTo({top:0}); await new Promise(r=>setTimeout(r,300)); _resizeAll();
            return html2canvas(area,{scale:2,useCORS:true,allowTaint:false,backgroundColor:'#f1f5f9',logging:false,removeContainer:true,windowWidth:document.documentElement.scrollWidth,windowHeight:area.scrollHeight,height:area.scrollHeight,ignoreElements:el=>el.hasAttribute('data-html2canvas-ignore')||el.id==='doPageExpPdf'||el.id==='doPageExpImg'});
        }

        /* ── Card capture ── */
        async function _captureCard(areaId,cardKey){
            const area=document.getElementById(areaId); if(!area) throw new Error('Area #'+areaId+' tidak ditemukan');
            _resizeAll(); await new Promise(r=>setTimeout(r,220));
            return html2canvas(area,{scale:2,useCORS:true,allowTaint:false,backgroundColor:'#ffffff',logging:false,removeContainer:true,ignoreElements:el=>el.hasAttribute('data-html2canvas-ignore')});
        }

        /* ── PDF header ── */
        function _pdfHeader(pdf,pW,label){
            const primary=[67,97,238]; // #4361EE
            pdf.setFillColor(...primary); pdf.rect(0,0,pW,11,'F');
            pdf.setTextColor(255,255,255); pdf.setFontSize(9); pdf.setFont('helvetica','bold');
            pdf.text('SMADIMENT — Data Overview'+(label?(' · '+label):''),10,7.5);
            const now=new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
            pdf.setFontSize(7); pdf.setFont('helvetica','normal'); pdf.text('Generated: '+now,pW-10,7.5,{align:'right'});
        }

        /* ── Build PDF from canvas ── */
        function _buildPdf(canvas,pdfLabel,landscape=false){
            const {jsPDF}=window.jspdf;
            const imgW=canvas.width,imgH=canvas.height;
            const pdf=new jsPDF({orientation:landscape?'landscape':'portrait',unit:'mm',format:'a4'});
            const pW=pdf.internal.pageSize.getWidth(),pH=pdf.internal.pageSize.getHeight();
            const margin=10,usableW=pW-margin*2,usableH=pH-margin*2-14;
            const ratio=usableW/imgW,sliceH=usableH/ratio;
            let srcY=0,pageNum=0;
            while(srcY<imgH){
                if(pageNum>0){pdf.addPage();} _pdfHeader(pdf,pW,pdfLabel);
                const srcSlice=Math.min(sliceH,imgH-srcY),dstH=srcSlice*ratio;
                const slice=document.createElement('canvas'); slice.width=imgW; slice.height=Math.ceil(srcSlice);
                slice.getContext('2d').drawImage(canvas,0,srcY,imgW,srcSlice,0,0,imgW,srcSlice);
                pdf.addImage(slice.toDataURL('image/png'),'PNG',margin,14,usableW,dstH);
                pdf.setFontSize(7); pdf.setTextColor(148,163,184); pdf.text(`Halaman ${pageNum+1}`,pW/2,pH-3,{align:'center'});
                srcY+=srcSlice; pageNum++;
            }
            return pdf;
        }

        /* ── Filename ── */
        function _stamp(){ return new Date().toISOString().slice(0,10).replace(/-/g,''); }
        const _cardLabels={trending:'Trending Topics',hashtag:'Top Hashtag',mention:'Mention',sov:'Share of Voice',sentiment:'Sentiment Score',map:'Buzzer Map'};

        /* ── Run full-page ── */
        async function run(type,btn){
            if(!window.html2canvas){_toast('html2canvas tidak tersedia','error');return;}
            if(type==='pdf'&&!window.jspdf?.jsPDF){_toast('jsPDF tidak tersedia','error');return;}
            const btnPdf=$('doPageExpPdf'),btnImg=$('doPageExpImg');
            _btnState(btnPdf,true); _btnState(btnImg,true);
            _toast(type==='pdf'?'Menyiapkan PDF…':'Mengambil gambar…','default',99999);
            try {
                const canvas=await _capturePage();
                if(type==='pdf'){
                    const pdf=_buildPdf(canvas,'Full Page',false);
                    pdf.save(`data_overview_${_stamp()}.pdf`); _toast('PDF berhasil diunduh!','success');
                } else {
                    const link=document.createElement('a'); link.download=`data_overview_${_stamp()}.png`; link.href=canvas.toDataURL('image/png'); link.click();
                    _toast('Gambar berhasil diunduh!','success');
                }
            } catch(err){ console.error('[DOExport]',err); _toast('Export gagal: '+err.message,'error'); }
            finally { _btnState(btnPdf,false); _btnState(btnImg,false); }
        }

        /* ── Run per-card ── */
        async function runCard(areaId,cardKey,type,btn){
            if(!window.html2canvas){_toast('html2canvas tidak tersedia','error');return;}
            if(type==='pdf'&&!window.jspdf?.jsPDF){_toast('jsPDF tidak tersedia','error');return;}
            _btnState(btn,true);
            _toast(type==='pdf'?'Menyiapkan PDF card…':'Mengambil gambar card…','default',99999);
            try {
                const canvas=await _captureCard(areaId,cardKey);
                const label=_cardLabels[cardKey]||cardKey;
                const fname=`data_overview_${cardKey}_${_stamp()}`;
                if(type==='image'){
                    const link=document.createElement('a'); link.download=fname+'.png'; link.href=canvas.toDataURL('image/png'); link.click();
                    _toast('Gambar berhasil diunduh!','success');
                } else {
                    const pdf=_buildPdf(canvas,label,canvas.width>canvas.height);
                    pdf.save(fname+'.pdf'); _toast('PDF berhasil diunduh!','success');
                }
            } catch(err){ console.error('[DOExport.runCard]',err); _toast('Export gagal: '+err.message,'error'); }
            finally { _btnState(btn,false); }
        }

        return {run,runCard};
    })();

    /* ══ PLATFORM PICKER DISMISS ══ */
    document.addEventListener('mousedown',e=>{ const pp=$('doPlatPicker'); if(pp?.classList.contains('show')&&!pp.contains(e.target)) pp.classList.remove('show'); });

    /* ══ BOOT ══ */
    document.addEventListener('DOMContentLoaded',()=>{ DOLoader.init(); });
    </script>
@endsection