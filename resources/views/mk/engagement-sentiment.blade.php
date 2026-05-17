@extends('mk.layouts.app')

@section('title', 'Engagement Sentiment - SMADIMENT')

@section('styles')
<style>
    :root {
        --primary: #038047;
        --primary-rgb: 3, 128, 71;
        --primary-lt: rgba(3, 128, 71, .10);
        --dark: #273B4A;
        --white: #FFFFFF;
        --bg: #F1F5F8;
        --sent-pos: #2FC6F6;
        --sent-neg: #EF4444;
        --sent-neu: #94A3B8;
        --slate-50: #F8FAFC;
        --slate-100: #F1F5F9;
        --slate-200: #E2E8F0;
        --slate-300: #CBD5E1;
        --slate-400: #94A3B8;
        --slate-500: #64748B;
        --slate-600: #475569;
        --slate-700: #334155;
        --slate-800: #1E293B;
        --slate-900: #0F172A;
        --radius: 8px;
        --radius-sm: 5px;
        --shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
        --shadow-md: 0 4px 14px rgba(15, 23, 42, .08);
        --shadow-lg: 0 10px 30px rgba(15, 23, 42, .12);
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes shimmer {
        0%   { background-position: -200% 0; }
        100% { background-position:  200% 0; }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to   { transform: translateX(0);    opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0);    opacity: 1; }
        to   { transform: translateX(100%); opacity: 0; }
    }
    @keyframes overlayIn  { from { opacity: 0; } to { opacity: 1; } }
    @keyframes overlayOut { from { opacity: 1; } to { opacity: 0; } }
    @keyframes kpiShimmer {
        0%   { left: -100%; }
        100% { left:  150%; }
    }
    @keyframes kpiIconBounce {
        0%,100% { transform: scale(1) rotate(0deg); }
        30%     { transform: scale(1.25) rotate(-10deg); }
        60%     { transform: scale(1.1) rotate(6deg); }
    }

    .kpi-icon-bg {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.2); font-size: 24px;
        color: #fff; flex-shrink: 0;
    }
    .kpi-card-hover {
        will-change: transform;
        transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s ease !important;
        cursor: default; position: relative !important; overflow: hidden !important;
    }
    .kpi-card-hover::before {
        content: ''; position: absolute; top: 0; bottom: 0; left: -100%;
        width: 60%;
        background: linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);
        pointer-events: none; z-index: 1; transition: none;
    }
    .kpi-card-hover:hover { transform: translateY(-6px) scale(1.025) !important; box-shadow: 0 20px 40px rgba(0,0,0,.25) !important; }
    .kpi-card-hover:hover::before { animation: kpiShimmer .6s ease forwards; }
    .kpi-card-hover:hover .kpi-icon-bg { background: rgba(255,255,255,.35) !important; transition: background .2s ease !important; }
    .kpi-card-hover:hover .kpi-icon-bg i { animation: kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important; display: inline-block !important; }
    .kpi-card-hover:active { transform: translateY(-2px) scale(1.01) !important; transition-duration: .08s !important; }

    .sk-block {
        border-radius: 4px;
        background: linear-gradient(90deg, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
    }
    .spin-ring {
        width: 26px; height: 26px;
        border: 2.5px solid var(--slate-100);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin .65s linear infinite;
    }

    /* ── Export ── */
    .page-export-bar {
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
        gap: 10px; background: #fff;
        border: 1px solid var(--slate-200); border-radius: var(--radius);
        padding: 9px 14px; margin-bottom: 20px; box-shadow: var(--shadow-sm);
    }
    .page-export-bar-left { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: var(--slate-600); }
    .page-export-bar-left i { font-size: 15px; color: var(--primary); }
    .page-export-bar-right { display: flex; gap: 8px; }
    .page-export-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: var(--radius-sm);
        font-size: 16px; cursor: pointer; transition: all .15s ease;
        border: 1.5px solid transparent; font-family: inherit;
    }
    .page-export-btn-pdf { background:#fff3f3; color:#dc2626; border-color:#fca5a5; }
    .page-export-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
    .page-export-btn-img { background:var(--primary-lt); color:var(--primary); border-color:rgba(3,128,71,.3); }
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
    .card-exp-btn-img { color:var(--primary); border-color:rgba(3,128,71,.3); background:var(--primary-lt); }
    .card-exp-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
    .card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
    .card-exp-btn .export-spinner { width:11px; height:11px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
    .card-exp-btn.exporting .export-spinner { display:inline-block; }
    .card-exp-btn.exporting .export-icon { display:none; }
    .export-toast {
        position:fixed; bottom:24px; left:50%;
        transform:translateX(-50%) translateY(20px);
        background:var(--slate-900); color:#fff; border-radius:var(--radius);
        padding:10px 18px; font-size:12px; font-weight:600;
        box-shadow:var(--shadow-lg); z-index:99999; opacity:0; pointer-events:none;
        transition:opacity .22s ease, transform .22s ease;
        display:flex; align-items:center; gap:8px; white-space:nowrap;
    }
    .export-toast.show  { opacity:1; transform:translateX(-50%) translateY(0); }
    .export-toast.success { background:#065f46; }
    .export-toast.error   { background:#991b1b; }

    /* ── SOV Card (same as DO) ── */
    .snt-sov-body {
        display: flex; align-items: stretch; min-height: 300px;
    }
    .snt-sov-chart {
        flex: 1; display: flex; align-items: center; justify-content: center;
        padding: 8px 16px; min-width: 0;
    }
    #chSovPie { width: 100% !important; height: 300px !important; }

    .snt-sov-stats {
        width: 190px; flex-shrink: 0;
        border-left: 1px solid var(--slate-200);
        padding: 14px 13px;
        display: flex; flex-direction: column; justify-content: center;
        gap: 0;
    }

    /* ── Mention-style card ── */
    .snt-mention-body {
        display: flex; align-items: stretch; min-height: 260px;
    }
    .snt-mention-chart {
        flex: 1; display: flex; align-items: center; justify-content: center;
        padding: 16px 8px; min-width: 0;
    }
    #chMentionPie { width: 100% !important; max-width: 320px; height: 260px !important; }

    .snt-mention-stats {
        width: 175px; flex-shrink: 0;
        border-left: 1px solid var(--slate-200);
        padding: 16px 14px;
        display: flex; flex-direction: column; justify-content: center;
        gap: 12px;
    }

    /* ── Shared stat styles (identical to DO) ── */
    .do-mstat-label { font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
    .do-mstat-row {
        display: flex; flex-direction: column; gap: 2px; cursor: pointer;
        border-radius: var(--radius-sm); padding: 6px 7px; margin: 0 -7px;
        transition: background .13s;
    }
    .do-mstat-row:hover { background: var(--primary-lt); }
    .do-mstat-name { font-size:11px; font-weight:600; color:var(--slate-500); display:flex; align-items:center; gap:5px; }
    .do-mstat-name span { display:inline-block; width:7px; height:7px; border-radius:50%; flex-shrink:0; }
    .do-mstat-val-row { display:flex; align-items:baseline; gap:6px; }
    .do-mstat-val { font-size:17px; font-weight:800; letter-spacing:-.5px; color:var(--slate-900); line-height:1.1; }
    .do-mstat-pct { font-size:11px; font-weight:700; line-height:1.1; }
    .do-mstat-divider { height:1px; background:var(--slate-100); margin:8px 0; }
    .do-mstat-total-lbl { font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; }
    .do-mstat-total-val { font-size:20px; font-weight:800; letter-spacing:-1px; color:var(--primary); line-height:1.1; }

    /* ── Platform list (for breakdown) ── */
    .snt-media-list { display:flex; flex-direction:column; gap:8px; }
    .snt-media-row {
        display:flex; align-items:center; gap:10px; padding:10px 14px;
        background:var(--slate-50); border:1px solid var(--slate-100); border-radius:var(--radius);
        transition:all .13s; cursor:pointer;
    }
    .snt-media-row:hover { border-color:rgba(3,128,71,.2); background:#fff; box-shadow:var(--shadow-sm); }
    .snt-media-icon { width:32px; height:32px; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .snt-media-name { font-size:12px; font-weight:700; color:var(--slate-800); min-width:90px; }
    .snt-media-bars { flex:1; display:flex; flex-direction:column; gap:3px; }
    .snt-media-bar-row { display:flex; align-items:center; gap:6px; }
    .snt-media-bar-track { flex:1; height:6px; background:var(--slate-100); border-radius:3px; overflow:hidden; }
    .snt-media-bar-fill { height:100%; border-radius:3px; transition:width .8s cubic-bezier(.4,0,.2,1); }
    .snt-media-bar-val { font-size:10px; font-weight:700; color:var(--slate-500); min-width:38px; text-align:right; white-space:nowrap; }
    .snt-media-total { font-size:12px; font-weight:700; color:var(--slate-800); min-width:52px; text-align:right; }

    /* ── Dashboard grid ── */
    .snt-dashboard-grid {
        display: grid; grid-template-columns: repeat(2,1fr);
        gap: 28px 24px; margin-bottom: 28px; align-items: stretch;
    }
    .snt-dashboard-grid > .card { margin-bottom: 0 !important; height: 100%; }
    .snt-col-full { grid-column: 1 / -1; }

    .do-body-scroll { max-height: 210px; overflow-y: auto; }
    .do-body-scroll::-webkit-scrollbar { width: 3px; }
    .do-body-scroll::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }

    .do-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:36px 16px; gap:7px; }
    .do-empty i { font-size:32px; color:var(--slate-300); }
    .do-empty-txt { font-size:12px; font-weight:600; color:var(--slate-400); }

    /* ── Slide Panel (identical to DO) ── */
    .do-panel-overlay {
        position:fixed; inset:0; z-index:9000;
        background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none;
    }
    .do-panel-overlay.show   { display:block; animation:overlayIn .22s ease-out; }
    .do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }
    .do-panel {
        position:fixed; top:0; right:0; bottom:0; z-index:9001;
        width:480px; max-width:100vw; background:#fff;
        display:none; flex-direction:column; border-left:1px solid var(--slate-200);
        box-shadow:-8px 0 40px rgba(15,23,42,.16);
    }
    .do-panel.show   { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
    .do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }
    .do-panel-header {
        display:flex; align-items:center; gap:10px; padding:14px 16px;
        border-bottom:1px solid var(--slate-200); background:var(--slate-50); flex-shrink:0;
    }
    .do-panel-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
    .do-panel-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .do-panel-close {
        width:28px; height:28px; border-radius:var(--radius-sm);
        border:1px solid var(--slate-200); background:#fff; cursor:pointer;
        display:flex; align-items:center; justify-content:center;
        color:var(--slate-500); font-size:16px; transition:all .14s; flex-shrink:0;
    }
    .do-panel-close:hover { background:#EF4444; border-color:#EF4444; color:#fff; }
    .do-panel-actions {
        display:flex; align-items:center; gap:7px; padding:7px 12px;
        border-bottom:1px solid var(--slate-200); background:#fff; flex-shrink:0;
    }
    .do-panel-meta {
        flex:1; font-size:10px; font-weight:700; color:var(--slate-400);
        text-transform:uppercase; letter-spacing:.5px;
        display:flex; align-items:center; gap:5px;
    }
    .do-panel-tabs {
        display:flex; background:var(--slate-100); border:1px solid var(--slate-200);
        border-radius:var(--radius-sm); padding:2px; gap:2px;
    }
    .do-panel-tab {
        padding:3px 9px; border-radius:3px; border:none; background:transparent;
        font-size:11px; font-weight:700; cursor:pointer; transition:all .13s;
        color:var(--slate-500); font-family:inherit;
    }
    .do-panel-tab:hover { background:#fff; }
    .do-panel-tab.active { background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08); }
    .do-panel-tab.active[data-s="all"] { color:var(--primary); }
    .do-panel-tab.neg.active { color:#EF4444; }
    .do-panel-tab.pos.active { color:#2FC6F6; }
    .do-panel-tab.neu.active { color:var(--slate-500); }
    .do-panel-list { overflow-y:auto; flex:1; padding:2px 0; min-height:0; }
    .do-panel-list::-webkit-scrollbar { width:4px; }
    .do-panel-list::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
    .do-panel-item {
        display:flex; gap:10px; padding:10px 14px;
        border-bottom:1px solid var(--slate-50); cursor:pointer; transition:background .1s;
        align-items:flex-start;
    }
    .do-panel-item:hover { background:#f0fff8; }
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
    .do-panel-handle { font-size:10px; color:var(--slate-400); font-weight:500; margin-bottom:2px; }
    .do-panel-text { font-size:11px; color:var(--slate-600); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
    .do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--slate-400); flex-wrap:wrap; }
    .do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
    .do-sent-badge--pos { background:#d1fae5; color:#065f46; }
    .do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
    .do-sent-badge--neu { background:var(--slate-100); color:var(--slate-500); }
    .do-panel-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:12px; color:var(--slate-400); font-size:13px; font-weight:600; }
    .do-panel-spinner { width:28px; height:28px; border:2.5px solid var(--slate-100); border-top-color:var(--primary); border-radius:50%; animation:spin .65s linear infinite; }

    /* ── Detail Panel ── */
    .do-detail-panel {
        position:absolute; inset:0; background:#fff; z-index:5;
        display:none; flex-direction:column;
        animation:slideInRight .2s cubic-bezier(.4,0,.2,1);
    }
    .do-detail-panel.show { display:flex; }
    .do-dp2-header { display:flex; align-items:center; gap:8px; padding:12px 14px; background:var(--slate-50); border-bottom:1px solid var(--slate-200); flex-shrink:0; }
    .do-dp2-back { width:28px; height:28px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); transition:all .13s; font-size:14px; }
    .do-dp2-back:hover { background:var(--primary-lt); color:var(--primary); border-color:var(--primary); }
    .do-dp2-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .do-dp2-body { overflow-y:auto; flex:1; padding:16px; }
    .do-dp2-body::-webkit-scrollbar { width:4px; }
    .do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
    .do-dp2-avatar-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
    .do-dp2-avatar-lg { width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700; font-size:16px; display:flex; align-items:center; justify-content:center; border:2px solid var(--slate-200); overflow:hidden; flex-shrink:0; }
    .do-dp2-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
    .do-dp2-name { font-size:14px; font-weight:700; color:var(--slate-900); }
    .do-dp2-handle { font-size:11px; color:var(--slate-400); font-weight:500; }
    .do-dp2-plat-badge { display:inline-block; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; margin-top:3px; }
    .do-dp2-meta { display:flex; align-items:center; justify-content:space-between; font-size:11px; color:var(--slate-400); font-weight:500; margin-bottom:10px; }
    .do-dp2-sent { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:3px; font-size:11px; font-weight:700; margin-bottom:10px; }
    .do-dp2-sent--pos { background:#d1fae5; color:#065f46; }
    .do-dp2-sent--neg { background:#fee2e2; color:#991b1b; }
    .do-dp2-sent--neu { background:var(--slate-100); color:var(--slate-500); }
    .do-dp2-content { font-size:12px; color:var(--slate-700); line-height:1.7; margin-bottom:12px; background:var(--slate-50); border-radius:var(--radius-sm); padding:10px 12px; border:1px solid var(--slate-200); word-break:break-word; }
    .do-dp2-media { border-radius:var(--radius-sm); overflow:hidden; margin-bottom:10px; background:#000; }
    .do-dp2-media img { width:100%; max-height:220px; object-fit:cover; display:block; }
    .do-dp2-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
    .do-dp2-stat { background:var(--slate-50); border-radius:var(--radius-sm); padding:8px 10px; border:1px solid var(--slate-200); text-align:center; }
    .do-dp2-stat-val { font-size:14px; font-weight:700; color:var(--slate-900); }
    .do-dp2-stat-lbl { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
    .do-dp2-link { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 14px; background:var(--primary); color:#fff; border-radius:var(--radius-sm); font-size:12px; font-weight:700; text-decoration:none; transition:filter .14s; margin-top:4px; }
    .do-dp2-link:hover { filter:brightness(1.1); color:#fff; }
    .do-dp2-link i { font-size:13px; }

    /* ── Platform picker ── */
    .do-plat-picker {
        position:fixed; z-index:20000; background:#fff;
        border:1px solid var(--slate-200); border-radius:var(--radius);
        box-shadow:var(--shadow-lg); padding:5px; min-width:175px; font-family:inherit; display:none;
        animation:fadeUp .14s ease-out;
    }
    .do-plat-picker.show { display:block; }
    .do-plat-picker-head { padding:4px 9px 6px; font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--slate-100); margin-bottom:3px; }
    .do-plat-btn { display:flex; align-items:center; gap:7px; padding:7px 10px; border-radius:var(--radius-sm); font-size:12px; font-weight:600; cursor:pointer; background:transparent; border:none; font-family:inherit; width:100%; text-align:left; color:var(--slate-700); transition:background .12s; }
    .do-plat-btn:hover { background:var(--primary-lt); color:var(--primary); }
    .do-plat-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-left:auto; }

    /* ── Trend line chart container ── */
    #chTrend { width:100% !important; }

    @media(max-width:767px) {
        .snt-dashboard-grid { grid-template-columns:1fr; }
        .snt-sov-body, .snt-mention-body { flex-direction:column; }
        .snt-sov-stats, .snt-mention-stats { width:100%; border-left:none; border-top:1px solid var(--slate-200); flex-direction:row; flex-wrap:wrap; gap:14px; padding:14px 16px; }
        .do-panel { width:100vw; }
    }
</style>
@endsection

@section('page-title', 'Engagement Sentiment')

@section('content')

@include('mk.layouts.partials.filter-datepicker')

@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
@endphp

<div id="sntPageExportArea">

    {{-- ══ KPI Cards ══ --}}
    <div class="row mb-3">
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 text-white kpi-card-hover" style="background:#06B6D4;animation:fadeUp .38s ease-out both;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiNeg">—</h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNegSub"><i class="ph ph-arrow-down me-1"></i>Loading...</p>
                        </div>
                        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-thumbs-down"></i></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 text-white kpi-card-hover" style="background:#F59E0B;animation:fadeUp .38s ease-out .05s both;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiPos">—</h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPosSub"><i class="ph ph-arrow-up me-1"></i>Loading...</p>
                        </div>
                        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-thumbs-up"></i></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 text-white kpi-card-hover" style="background:#4CAF50;animation:fadeUp .38s ease-out .10s both;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Neutral</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiNeu">—</h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNeuSub"><i class="ph ph-minus-circle me-1"></i>Loading...</p>
                        </div>
                        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-minus-circle"></i></div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 text-white kpi-card-hover" style="background:#038047;animation:fadeUp .38s ease-out .15s both;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Total Interactions</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiTot">—</h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTotSub"><i class="ph ph-chart-bar me-1"></i>Loading...</p>
                        </div>
                        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-activity"></i></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Page Export Toolbar ══ --}}
    <div class="page-export-bar" data-html2canvas-ignore="true">
        <div class="page-export-bar-left">
            <i class="ph ph-export"></i><span>Export Halaman</span>
            <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">Overview · Trend · Platforms · Time</span>
        </div>
        <div class="page-export-bar-right">
            <button type="button" class="page-export-btn page-export-btn-pdf" id="sntPageExpPdf" onclick="SNTExport.run('pdf',this)" title="Export PDF">
                <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
            </button>
            <button type="button" class="page-export-btn page-export-btn-img" id="sntPageExpImg" onclick="SNTExport.run('image',this)" title="Export PNG">
                <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
            </button>
        </div>
    </div>

    {{-- ══ DASHBOARD GRID ══ --}}
    <div class="snt-dashboard-grid">

        {{-- Share of Voice Sentiment ── same as DO SOV card --}}
        <div class="card" data-lazy="sov-sentiment" id="snt-card-sov" style="animation:fadeUp .38s ease-out .18s both;">
            <div id="card-export-sov">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-microphone f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Share of Voice by Sentiment</h6>
                            <small class="text-muted">Distribusi sentimen keseluruhan</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-secondary text-muted rounded-pill">All Media</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-sov','sov','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-sov','sov','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div id="sovSkel" style="padding:16px;"><div class="sk-block" style="height:240px;border-radius:6px;"></div></div>
                <div id="sovBody" style="display:none;">
                    <div class="snt-sov-body">
                        <div class="snt-sov-chart"><div id="chSovPie"></div></div>
                        <div class="snt-sov-stats">
                            <div class="do-mstat-label">Distribusi</div>
                            <div class="do-mstat-row" id="statNegRow" style="cursor:pointer;">
                                <span class="do-mstat-name"><span style="background:#EF4444;"></span>Negative</span>
                                <div class="do-mstat-val-row">
                                    <span class="do-mstat-val" id="sovNegVal">—</span>
                                    <span class="do-mstat-pct" id="sovNegPct" style="color:#EF4444;"></span>
                                </div>
                            </div>
                            <div class="do-mstat-row" id="statPosRow" style="cursor:pointer;">
                                <span class="do-mstat-name"><span style="background:#2FC6F6;"></span>Positive</span>
                                <div class="do-mstat-val-row">
                                    <span class="do-mstat-val" id="sovPosVal">—</span>
                                    <span class="do-mstat-pct" id="sovPosPct" style="color:#2FC6F6;"></span>
                                </div>
                            </div>
                            <div class="do-mstat-row" id="statNeuRow" style="cursor:pointer;">
                                <span class="do-mstat-name"><span style="background:#94A3B8;"></span>Neutral</span>
                                <div class="do-mstat-val-row">
                                    <span class="do-mstat-val" id="sovNeuVal">—</span>
                                    <span class="do-mstat-pct" id="sovNeuPct" style="color:#94A3B8;"></span>
                                </div>
                            </div>
                            <div class="do-mstat-divider"></div>
                            <div class="do-mstat-row" id="statTotRow" style="cursor:pointer;">
                                <span class="do-mstat-total-lbl">Total</span>
                                <span class="do-mstat-total-val" id="sovTotVal">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sentiments by Media Type ── same layout as DO Mention card --}}
        <div class="card" data-lazy="sentiment-by-media" id="snt-card-media" style="animation:fadeUp .38s ease-out .21s both;">
            <div id="card-export-media">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-bar f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Sentiments by Media Types</h6>
                            <small class="text-muted">Mass Media vs Social Media</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-secondary text-muted rounded-pill">% Share</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-media','media','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-media','media','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div id="mediaSkel" style="padding:16px;"><div class="sk-block" style="height:240px;border-radius:6px;"></div></div>
                <div id="mediaBody" style="display:none;">
                    <div class="snt-mention-body">
                        <div class="snt-mention-chart"><div id="chMentionPie"></div></div>
                        <div class="snt-mention-stats">
                            <div class="do-mstat-label">Breakdown</div>
                            <div class="do-mstat-row" id="statMassRow">
                                <span class="do-mstat-name"><span style="background:#0284c7;"></span>Mass Media</span>
                                <div class="do-mstat-val-row">
                                    <span class="do-mstat-val" id="mediaMassVal">—</span>
                                    <span class="do-mstat-pct" id="mediaMassPct" style="color:#0284c7;"></span>
                                </div>
                            </div>
                            <div class="do-mstat-row" id="statSocialRow">
                                <span class="do-mstat-name"><span style="background:var(--primary);"></span>Social Media</span>
                                <div class="do-mstat-val-row">
                                    <span class="do-mstat-val" id="mediaSocialVal">—</span>
                                    <span class="do-mstat-pct" id="mediaSocialPct" style="color:var(--primary);"></span>
                                </div>
                            </div>
                            <div class="do-mstat-divider"></div>
                            <div class="do-mstat-row" id="statMediaTotRow">
                                <span class="do-mstat-total-lbl">Total</span>
                                <span class="do-mstat-total-val" id="mediaTotVal">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sentiment Trend ── full width like DO sentiment-timeline --}}
        <div class="card snt-col-full" data-lazy="sentiment-trend" id="snt-card-trend" style="animation:fadeUp .38s ease-out .24s both;">
            <div id="card-export-trend">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-pulse f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Sentiment Trend</h6>
                            <small class="text-muted">Tren harian Negative / Positive / Neutral — klik untuk detail</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-secondary text-muted rounded-pill" id="trendBadge">All Media</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-trend','trend','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-trend','trend','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div class="card-body" style="position:relative;padding-bottom:12px;">
                    <div id="skTrend" style="display:flex;align-items:center;justify-content:center;gap:10px;padding:60px 20px;">
                        <div class="spin-ring"></div>
                        <span style="font-size:13px;font-weight:600;color:var(--slate-400);">Loading chart…</span>
                    </div>
                    <div id="chTrend" style="display:none;height:350px;"></div>
                </div>
            </div>
        </div>

        {{-- Breakdown per Platform ── full width --}}
        <div class="card snt-col-full" data-lazy="platform-breakdown" id="snt-card-breakdown" style="animation:fadeUp .38s ease-out .27s both;">
            <div id="card-export-breakdown">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-grid-four f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Breakdown Interaksi per Platform</h6>
                            <small class="text-muted">Negative / Positive / Neutral per media — klik untuk detail</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-secondary text-muted rounded-pill">All Platforms</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-breakdown','breakdown','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-breakdown','breakdown','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="platBreakdownList" class="snt-media-list">
                        @foreach(['Mass Media','X / Twitter','Facebook','Instagram','YouTube','TikTok'] as $pl)
                        <div class="snt-media-row">
                            <div class="snt-media-name">{{ $pl }}</div>
                            <div class="snt-media-bars">
                                <div class="snt-media-bar-row"><div class="sk-block" style="height:6px;width:100%;border-radius:3px;"></div></div>
                                <div class="snt-media-bar-row"><div class="sk-block" style="height:6px;width:100%;border-radius:3px;margin-top:4px;"></div></div>
                            </div>
                            <div class="sk-block" style="height:18px;width:50px;border-radius:4px;margin-left:10px;"></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Sentiments by Weekday --}}
        <div class="card" data-lazy="sentiment-weekday" id="snt-card-weekday" style="animation:fadeUp .38s ease-out .30s both;">
            <div id="card-export-weekday">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-calendar f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Sentiments by Weekday</h6>
                            <small class="text-muted">Volume sentimen per hari dalam seminggu</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-secondary text-muted rounded-pill">7 Hari</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-weekday','weekday','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-weekday','weekday','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="skWeekday" style="display:flex;align-items:center;justify-content:center;gap:10px;padding:60px 20px;">
                        <div class="spin-ring"></div>
                        <span style="font-size:13px;font-weight:600;color:var(--slate-400);">Loading chart…</span>
                    </div>
                    <div id="chWeekday" style="display:none;height:320px;"></div>
                </div>
            </div>
        </div>

        {{-- Sentiments by Hour --}}
        <div class="card" data-lazy="sentiment-hour" id="snt-card-hour" style="animation:fadeUp .38s ease-out .33s both;">
            <div id="card-export-hour">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-clock f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Sentiments by Hour</h6>
                            <small class="text-muted">Distribusi sentimen per jam (00–23)</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-secondary text-muted rounded-pill">24 Jam</span>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="SNTExport.runCard('card-export-hour','hour','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="SNTExport.runCard('card-export-hour','hour','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="skHour" style="display:flex;align-items:center;justify-content:center;gap:10px;padding:60px 20px;">
                        <div class="spin-ring"></div>
                        <span style="font-size:13px;font-weight:600;color:var(--slate-400);">Loading chart…</span>
                    </div>
                    <div id="chHour" style="display:none;height:320px;"></div>
                </div>
            </div>
        </div>

    </div>{{-- /snt-dashboard-grid --}}

</div>{{-- /sntPageExportArea --}}

<div class="export-toast" id="sntExportToast">
    <i class="ph ph-check-circle" id="sntExportToastIcon"></i>
    <span id="sntExportToastMsg">Exporting…</span>
</div>

{{-- Slide Panel --}}
<div class="do-panel-overlay" id="sntPanelOverlay" onclick="SNTPanel.closeByOverlay()"></div>
<div class="do-panel" id="sntSlidePanel">
    <div class="do-panel-header" id="sntPanelHeader">
        <div class="do-panel-dot" id="sntPanelDot"></div>
        <span class="do-panel-title" id="sntPanelTitle">Mentions</span>
        <button class="do-panel-close" style="margin-right:2px;" onclick="SNTPanel.refresh()" title="Refresh"><i class="ph ph-arrows-clockwise" id="sntPanelRefreshIcon"></i></button>
        <button class="do-panel-close" onclick="SNTPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span id="sntPanelMeta">—</span></div>
        <div class="do-panel-tabs">
            <button class="do-panel-tab active" data-s="all" onclick="SNTPanel.filterSent('all')">Semua</button>
            <button class="do-panel-tab pos" data-s="pos" onclick="SNTPanel.filterSent('pos')">Pos</button>
            <button class="do-panel-tab neg" data-s="neg" onclick="SNTPanel.filterSent('neg')">Neg</button>
            <button class="do-panel-tab neu" data-s="neu" onclick="SNTPanel.filterSent('neu')">Neu</button>
        </div>
    </div>
    <div class="do-panel-list" id="sntPanelList"></div>
    <div class="do-detail-panel" id="sntDetailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="SNTDetail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="sntDetailTitle">Detail</span>
            <button class="do-panel-close" onclick="SNTPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="sntDetailBody"></div>
    </div>
</div>

{{-- Platform Picker --}}
<div class="do-plat-picker" id="sntPlatPicker">
    <div class="do-plat-picker-head">Pilih Platform</div>
    <button class="do-plat-btn" onclick="SNTPanel.openPlatform('twit','all')">X / Twitter <span class="do-plat-dot" style="background:#1d9bf0;"></span></button>
    <button class="do-plat-btn" onclick="SNTPanel.openPlatform('fb','all')">Facebook <span class="do-plat-dot" style="background:#1877f2;"></span></button>
    <button class="do-plat-btn" onclick="SNTPanel.openPlatform('instagram','all')">Instagram <span class="do-plat-dot" style="background:#e1306c;"></span></button>
    <button class="do-plat-btn" onclick="SNTPanel.openPlatform('youtube','all')">YouTube <span class="do-plat-dot" style="background:#ff0000;"></span></button>
    <button class="do-plat-btn" onclick="SNTPanel.openPlatform('tiktok','all')">TikTok <span class="do-plat-dot" style="background:#111827;"></span></button>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}" defer></script>
<script>
'use strict';

/* ══ CONFIG ══ */
const SNTCfg = {
    pid: {{ $projectId ? (int)$projectId : 'null' }},
    sd:  '{{ $startDate }}',
    ed:  '{{ $endDate }}',
    primary: '#038047',
    platMeta: {
        doc:       { label:'Online News',  color:'#0284c7' },
        twit:      { label:'X / Twitter',  color:'#1d9bf0' },
        twitter:   { label:'X / Twitter',  color:'#1d9bf0' },
        fb:        { label:'Facebook',      color:'#1877f2' },
        facebook:  { label:'Facebook',      color:'#1877f2' },
        instagram: { label:'Instagram',     color:'#e1306c' },
        youtube:   { label:'YouTube',       color:'#ff0000' },
        tiktok:    { label:'TikTok',        color:'#111827' },
        all:       { label:'All Media',     color:'#038047' },
        social:    { label:'Social Media',  color:'#038047' },
    }
};

const $s     = id => document.getElementById(id);
const numFmt = n  => parseInt(n||0).toLocaleString('id-ID');
const numK   = n  => { n=parseInt(n||0); return n>=1e9?(n/1e9).toFixed(1)+'B':n>=1e6?(n/1e6).toFixed(1)+'M':n>=1e3?(n/1e3).toFixed(1)+'k':String(n); };
const pct    = (v,t) => t>0?((v/t)*100).toFixed(1)+'%':'0%';
const esc    = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const emptyHtml = m => '<div class="do-empty"><i class="ph ph-warning-circle"></i><span class="do-empty-txt">'+(m||'Tidak ada data')+'</span></div>';

/* ══ ECharts ══ */
const SNTCharts = {
    _i: {},
    make(id) {
        if (this._i[id]) { try { this._i[id].dispose(); } catch(e){} }
        const dom = $s(id); if (!dom) return null;
        const c = echarts.init(dom, null, { renderer:'canvas' });
        this._i[id] = c; return c;
    },
    disposeAll() { Object.values(this._i).forEach(c=>{ try{c.dispose();}catch(e){} }); this._i={}; }
};
window.addEventListener('resize', () => {
    Object.values(SNTCharts._i).forEach(c=>{ try{ if(!c.isDisposed()) c.resize(); }catch(e){} });
});

const EC_TT = {
    backgroundColor:'#1e293b', borderColor:'#334155', borderWidth:1,
    padding:[9,13], textStyle:{color:'#fff',fontFamily:'inherit',fontSize:12},
    extraCssText:'border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.3);'
};

/* ══ DATA STORE ══ */
const SNTData = {
    totals:  { neg:0, pos:0, neu:0 },
    byMedia: [],
    trend:   [],
    weekday: null,
    hour:    null,
};

/* ══ LOADER ══ */
const SNTLoader = {
    loaded: new Set(),
    _apexTrend: null,

    init() {
        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        const sec = e.target.dataset.lazy;
                        if (!this.loaded.has(sec)) { this.loaded.add(sec); this.load(sec); obs.unobserve(e.target); }
                    }
                });
            }, { rootMargin:'100px', threshold:.05 });
            document.querySelectorAll('[data-lazy]').forEach(c => obs.observe(c));
        } else {
            document.querySelectorAll('[data-lazy]').forEach(c => this.load(c.dataset.lazy));
        }
    },

    load(sec) {
        try {
            if (sec === 'sov-sentiment')    this.loadSov();
            if (sec === 'sentiment-by-media') this.loadByMedia();
            if (sec === 'sentiment-trend')  this.loadTrend();
            if (sec === 'platform-breakdown') this.loadBreakdown();
            if (sec === 'sentiment-weekday') this.loadWeekday();
            if (sec === 'sentiment-hour')   this.loadHour();
        } catch(err) { console.error('Error loading '+sec+':', err); }
    },

    async _fetchTotals() {
        if (!SNTCfg.pid) return;
        try {
            const r = await fetch(`/mk/api/sentiment/interaction-totals?project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}`);
            const d = await r.json();
            if (d.error) throw new Error(d.error);
            SNTData.totals  = d.totals  || { neg:0, pos:0, neu:0 };
            SNTData.byMedia = d.by_media || [];
            SNTData.trend   = d.trend   || [];
            this._updateKPIs();
        } catch(e) { console.error('[fetchTotals]', e); }
    },

    _updateKPIs() {
        const { neg, pos, neu } = SNTData.totals;
        const tot = neg + pos + neu;
        $s('kpiNeg').textContent = numK(neg);
        $s('kpiPos').textContent = numK(pos);
        $s('kpiNeu').textContent = numK(neu);
        $s('kpiTot').textContent = numK(tot);
        $s('kpiNegSub').innerHTML = `<i class="ph ph-arrow-down me-1"></i>${pct(neg,tot)} dari total`;
        $s('kpiPosSub').innerHTML = `<i class="ph ph-arrow-up me-1"></i>${pct(pos,tot)} dari total`;
        $s('kpiNeuSub').innerHTML = `<i class="ph ph-minus-circle me-1"></i>${pct(neu,tot)} dari total`;
        $s('kpiTotSub').innerHTML = `<i class="ph ph-chart-bar me-1"></i>${numFmt(tot)} total periode ini`;
    },

    async loadSov() {
        await this._fetchTotals();
        const { neg, pos, neu } = SNTData.totals;
        const tot = neg + pos + neu;
        $s('sovSkel').style.display = 'none';
        $s('sovBody').style.display = 'block';
        $s('sovNegVal').textContent = numK(neg);
        $s('sovPosVal').textContent = numK(pos);
        $s('sovNeuVal').textContent = numK(neu);
        $s('sovTotVal').textContent = numK(tot);
        $s('sovNegPct').textContent = pct(neg, tot);
        $s('sovPosPct').textContent = pct(pos, tot);
        $s('sovNeuPct').textContent = pct(neu, tot);

        $s('statNegRow').onclick = () => SNTPanel.open('all','neg', window.innerWidth/2, window.innerHeight/2);
        $s('statPosRow').onclick = () => SNTPanel.open('all','pos', window.innerWidth/2, window.innerHeight/2);
        $s('statNeuRow').onclick = () => SNTPanel.open('all','neu', window.innerWidth/2, window.innerHeight/2);
        $s('statTotRow').onclick = () => SNTPanel.open('all','all', window.innerWidth/2, window.innerHeight/2);

        if (!tot) return;
        const chart = SNTCharts.make('chSovPie'); if (!chart) return;
        chart.setOption({
            animation:true, animationDuration:800, animationEasing:'cubicOut', backgroundColor:'transparent',
            tooltip:{ ...EC_TT, trigger:'item', formatter(p) {
                const pc = tot > 0 ? (p.value/tot*100) : 0;
                return `<div style="font-weight:700;font-size:13px;margin-bottom:5px;">${p.name}</div>
                        <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Interactions</span><span style="font-weight:700;">${numFmt(p.value)}</span></div>
                        <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pc.toFixed(1)}%</span></div>`;
            }},
            legend:{ show:false },
            series:[{
                type:'pie', radius:['32%','48%'], center:['50%','50%'],
                avoidLabelOverlap:true, minAngle:6,
                itemStyle:{ borderColor:'#fff', borderWidth:3, borderRadius:5 },
                label:{
                    show:true, alignTo:'edge', edgeDistance:'8%', fontFamily:'inherit',
                    formatter(p) {
                        const pc = tot>0?(p.value/tot*100):0;
                        return `{name|${p.name}}\n{pct|${Math.round(pc)}%}`;
                    },
                    rich:{
                        name:{ fontWeight:'700', fontSize:10, color:'#374151', lineHeight:16 },
                        pct:{ fontWeight:'800', fontSize:11, color:'#038047', lineHeight:14, backgroundColor:'#f0faf5', borderRadius:4, padding:[1,5] }
                    }
                },
                labelLine:{ show:true, length:14, length2:18, smooth:.4, lineStyle:{ color:'#CBD5E1', width:1.2 } },
                emphasis:{ scale:true, scaleSize:5, itemStyle:{ shadowBlur:14, shadowColor:'rgba(0,0,0,.15)' } },
                data:[
                    { name:'Negative', value:neg, itemStyle:{ color:'#EF4444' } },
                    { name:'Positive', value:pos, itemStyle:{ color:'#2FC6F6' } },
                    { name:'Neutral',  value:neu, itemStyle:{ color:'#94A3B8' } },
                ]
            }]
        });
        chart.on('click', p => {
            const sentMap = { Negative:'neg', Positive:'pos', Neutral:'neu' };
            SNTPanel.open('all', sentMap[p.name]||'all', window.innerWidth/2, window.innerHeight/2);
        });
        chart.on('mouseover', p=>{ if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
        chart.on('mouseout',  ()=>{ chart.getDom().style.cursor='default'; });
    },

    async loadByMedia() {
        if (!SNTData.byMedia.length) await this._fetchTotals();
        const bm   = SNTData.byMedia;
        const massD   = bm.filter(m => m.key==='doc');
        const socialD = bm.filter(m => m.key!=='doc');
        const massN   = massD.reduce((s,m)=>s+m.neg,0);
        const massP   = massD.reduce((s,m)=>s+m.pos,0);
        const massNe  = massD.reduce((s,m)=>s+m.neu,0);
        const massT   = massN+massP+massNe;
        const socN    = socialD.reduce((s,m)=>s+m.neg,0);
        const socP    = socialD.reduce((s,m)=>s+m.pos,0);
        const socNe   = socialD.reduce((s,m)=>s+m.neu,0);
        const socT    = socN+socP+socNe;
        const tot     = massT + socT;

        $s('mediaSkel').style.display = 'none';
        $s('mediaBody').style.display = 'block';
        $s('mediaMassVal').textContent  = numK(massT);
        $s('mediaSocialVal').textContent = numK(socT);
        $s('mediaTotVal').textContent   = numK(tot);
        $s('mediaMassPct').textContent  = pct(massT, tot);
        $s('mediaSocialPct').textContent = pct(socT, tot);
        $s('statMassRow').onclick   = () => SNTPanel.open('doc','all', window.innerWidth/2, window.innerHeight/2);
        $s('statSocialRow').onclick = (e) => SNTPanel.showPlatPicker(e.clientX, e.clientY, 'all');
        $s('statMediaTotRow').onclick = () => SNTPanel.open('all','all', window.innerWidth/2, window.innerHeight/2);

        if (!tot) return;
        const chart = SNTCharts.make('chMentionPie'); if (!chart) return;
        const totalAll = tot;
        chart.setOption({
            animation:true, animationDuration:800, animationEasing:'cubicOut', backgroundColor:'transparent',
            tooltip:{ ...EC_TT, trigger:'item', formatter(p) {
                const pc = totalAll>0?(p.value/totalAll*100):0;
                return `<div style="font-weight:700;font-size:13px;margin-bottom:5px;">${p.name}</div>
                        <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Total</span><span style="font-weight:700;">${numFmt(p.value)}</span></div>
                        <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pc.toFixed(1)}%</span></div>`;
            }},
            legend:{ show:false },
            series:[{
                type:'pie', radius:['32%','48%'], center:['50%','50%'],
                avoidLabelOverlap:true, minAngle:6,
                itemStyle:{ borderColor:'#fff', borderWidth:3, borderRadius:5 },
                label:{
                    show:true, alignTo:'none', distanceToLabelLine:6, fontFamily:'inherit',
                    formatter(p) { const pc=totalAll>0?(p.value/totalAll*100):0; return `{name|${p.name}}\n{pct|${Math.round(pc)}%}`; },
                    rich:{
                        name:{ fontWeight:'700', fontSize:10, color:'#374151', lineHeight:17 },
                        pct:{ fontWeight:'800', fontSize:11, color:'#038047', lineHeight:15, backgroundColor:'#f0faf5', borderRadius:4, padding:[1,5] }
                    }
                },
                labelLine:{ show:true, length:10, length2:14, smooth:false, lineStyle:{ color:'#CBD5E1', width:1.2 } },
                emphasis:{ scale:true, scaleSize:6, itemStyle:{ shadowBlur:14, shadowColor:'rgba(0,0,0,.18)' } },
                data:[
                    { name:'Mass Media',   value:massT, itemStyle:{ color:'#0284c7' } },
                    { name:'Social Media', value:socT,  itemStyle:{ color:'#038047' } },
                ]
            }]
        });
        chart.on('click', p => {
            if (p.name === 'Mass Media') SNTPanel.open('doc','all', window.innerWidth/2, window.innerHeight/2);
            else SNTPanel.showPlatPicker(window.innerWidth/2, window.innerHeight/2, 'all');
        });
        chart.on('mouseover', p=>{ if(p.componentType==='series') chart.getDom().style.cursor='pointer'; });
        chart.on('mouseout',  ()=>{ chart.getDom().style.cursor='default'; });
    },

    async loadTrend() {
        if (!SNTData.trend.length) await this._fetchTotals();
        const trend = SNTData.trend;
        const skEl  = $s('skTrend');
        const mainEl = $s('chTrend');
        if (!mainEl) return;
        if (!trend.length || trend.every(d => !(d.neg||d.pos||d.neu))) {
            if (skEl) skEl.style.display = 'none';
            mainEl.style.display = 'block';
            mainEl.innerHTML = emptyHtml('Data trend tidak tersedia');
            return;
        }

        const dates  = trend.map(d => d.date);
        const xLabels = dates.map(d => { const dt = new Date(d+'T00:00:00'); return `${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`; });
        if (this._apexTrend) { try { this._apexTrend.destroy(); } catch(e){} this._apexTrend = null; }
        mainEl.style.display = 'block';
        mainEl.innerHTML = '';

        const toNum = arr => arr.map(v => Number(v||0));
        const opts = {
            chart:{
                type:'area', height:350,
                animations:{ enabled:true, easing:'linear', dynamicAnimation:{ speed:1000 } },
                toolbar:{ show:false },
                fontFamily:'inherit',
                events:{
                    click: (_e,_ctx,cfg) => {
                        let sd=null, ed=null;
                        if (cfg && typeof cfg.dataPointIndex !== 'undefined' && cfg.dataPointIndex>=0) { sd=dates[cfg.dataPointIndex]; ed=sd; }
                        const sentMap = { 0:'all', 1:'pos', 2:'neg', 3:'neu' };
                        SNTPanel.open('all', sentMap[cfg.seriesIndex]||'all', window.innerWidth/2, window.innerHeight/2);
                    },
                    mounted: () => { if (skEl) { skEl.style.display='none'; setTimeout(()=>{ try{skEl.remove();}catch(e){} }, 260); } }
                }
            },
            series:[
                { name:'Positive', data:toNum(trend.map(d=>d.pos||0)) },
                { name:'Negative', data:toNum(trend.map(d=>d.neg||0)) },
                { name:'Neutral',  data:toNum(trend.map(d=>d.neu||0)) },
            ],
            stroke:{ curve:'smooth', width:2.5 },
            grid:{ borderColor:'#F1F5F9', strokeDashArray:4, padding:{ left:10, right:10 } },
            markers:{ size: dates.length<=31?5:3, strokeWidth:2, strokeColors:'#fff', hover:{ size:6 } },
            dataLabels:{
                enabled: dates.length<=31,
                formatter: v => v > 0 ? numK(v) : '',
                offsetY:-8,
                style:{ fontSize:'9px', fontFamily:'inherit', fontWeight:'800' },
                background:{ enabled:true, foreColor:'#fff', padding:3, borderRadius:3, borderWidth:0, opacity:.9 }
            },
            xaxis:{
                categories: xLabels,
                labels:{ rotate:-45, rotateAlways:true, hideOverlappingLabels:false, style:{ colors:'#94A3B8', fontSize:'10px', fontFamily:'inherit' } },
                axisBorder:{ show:false }, axisTicks:{ show:false }
            },
            yaxis:{
                labels:{ formatter: v => numK(v), style:{ colors:'#94A3B8', fontSize:'10px', fontFamily:'inherit' } },
                axisBorder:{ show:false }, axisTicks:{ show:false }
            },
            colors:['#2FC6F6','#EF4444','#94A3B8'],
            fill:{ type:'solid', opacity:.3 },
            legend:{ position:'bottom', horizontalAlign:'left', labels:{ colors:'#94A3B8' } },
            tooltip:{ shared:false, intersect:true, y:{ formatter: v => numFmt(v)+' mentions' } }
        };
        this._apexTrend = new ApexCharts(mainEl, opts);
        this._apexTrend.render();

        const d0 = dates[0] ? new Date(dates[0]+'T00:00:00').toLocaleDateString('id-ID',{day:'numeric',month:'short'}) : '';
        const dn = dates[dates.length-1] ? new Date(dates[dates.length-1]+'T00:00:00').toLocaleDateString('id-ID',{day:'numeric',month:'short'}) : '';
        if (d0 && dn) $s('trendBadge').textContent = `${d0} – ${dn}`;
    },

    async loadBreakdown() {
        if (!SNTData.byMedia.length) await this._fetchTotals();
        const bm   = SNTData.byMedia;
        const list = $s('platBreakdownList');
        if (!bm.length) { list.innerHTML = emptyHtml('Tidak ada data per platform'); return; }

        const platIcons = {
            doc:       `<svg viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>`,
            twit:      `<svg viewBox="0 0 24 24" fill="#1d9bf0" stroke="none" style="width:18px;height:18px;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`,
            twitter:   `<svg viewBox="0 0 24 24" fill="#1d9bf0" stroke="none" style="width:18px;height:18px;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>`,
            fb:        `<svg viewBox="0 0 24 24" fill="none" stroke="#1877f2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>`,
            facebook:  `<svg viewBox="0 0 24 24" fill="none" stroke="#1877f2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>`,
            instagram: `<svg viewBox="0 0 24 24" fill="none" stroke="#e1306c" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".5" fill="#e1306c"/></svg>`,
            youtube:   `<svg viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>`,
            tiktok:    `<svg viewBox="0 0 24 24" fill="#111827" stroke="none" style="width:18px;height:18px;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.77 0 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0 0 12.68 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/></svg>`,
        };
        const platBg = { doc:'rgba(2,132,199,.1)', twit:'rgba(29,155,240,.1)', twitter:'rgba(29,155,240,.1)', fb:'rgba(24,119,242,.1)', facebook:'rgba(24,119,242,.1)', instagram:'rgba(225,48,108,.1)', youtube:'rgba(255,0,0,.08)', tiktok:'rgba(17,24,39,.07)' };
        const platKey = { doc:'doc', twit:'twit', twitter:'twitter', fb:'fb', facebook:'facebook', instagram:'instagram', youtube:'youtube', tiktok:'tiktok' };

        list.innerHTML = bm.map(m => {
            const tot = m.neg + m.pos + m.neu;
            const np  = tot > 0 ? (m.neg/tot*100) : 0;
            const pp  = tot > 0 ? (m.pos/tot*100) : 0;
            const nep = tot > 0 ? (m.neu/tot*100) : 0;
            const pk  = platKey[m.key] || m.key;
            return `<div class="snt-media-row" data-plat="${pk}">
                <div class="snt-media-icon" style="background:${platBg[m.key]||'#f1f5f9'};">${platIcons[m.key]||''}</div>
                <div class="snt-media-name">${m.label}</div>
                <div class="snt-media-bars">
                    <div class="snt-media-bar-row"><div class="snt-media-bar-track"><div class="snt-media-bar-fill" style="width:${np.toFixed(1)}%;background:#EF4444;"></div></div><div class="snt-media-bar-val" style="color:#EF4444;">${numK(m.neg)}</div></div>
                    <div class="snt-media-bar-row"><div class="snt-media-bar-track"><div class="snt-media-bar-fill" style="width:${pp.toFixed(1)}%;background:#2FC6F6;"></div></div><div class="snt-media-bar-val" style="color:#2FC6F6;">${numK(m.pos)}</div></div>
                    <div class="snt-media-bar-row"><div class="snt-media-bar-track"><div class="snt-media-bar-fill" style="width:${nep.toFixed(1)}%;background:#94A3B8;"></div></div><div class="snt-media-bar-val" style="color:#94A3B8;">${numK(m.neu)}</div></div>
                </div>
                <div class="snt-media-total">${numFmt(tot)}</div>
            </div>`;
        }).join('');

        list.querySelectorAll('.snt-media-row').forEach(row => {
            row.addEventListener('click', () => {
                const plat = row.dataset.plat || 'doc';
                const r = row.getBoundingClientRect();
                if (['twit','fb','instagram','youtube','tiktok'].includes(plat)) SNTPanel.open(plat, 'all', r.left+r.width/2, r.top+r.height/2);
                else SNTPanel.open('doc', 'all', r.left+r.width/2, r.top+r.height/2);
            });
        });
    },

    async _fetchTimeData() {
        if (!SNTCfg.pid) return;
        try {
            const r = await fetch(`/mk/api/sentiment/by-time?project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}`);
            const d = await r.json();
            if (d.error) throw new Error(d.error);
            SNTData.weekday = d.weekday;
            SNTData.hour    = d.hour;
        } catch(e) { console.error('[fetchTimeData]', e); }
    },

    async loadWeekday() {
        if (!SNTData.weekday) await this._fetchTimeData();
        const skEl  = $s('skWeekday');
        const mainEl = $s('chWeekday');
        const wd    = SNTData.weekday;
        if (!wd || !wd.weekdays || !wd.weekdays.length) {
            if (skEl) skEl.style.display='none'; if(mainEl){ mainEl.style.display='block'; mainEl.innerHTML=emptyHtml('Data tidak tersedia'); } return;
        }
        this._renderTimeChart('chWeekday','skWeekday', wd.weekdays, wd.neg||[], wd.pos||[], wd.neu||[], wd.total||[], false);
    },

    async loadHour() {
        if (!SNTData.hour) await this._fetchTimeData();
        const hr = SNTData.hour;
        if (!hr || !hr.hours || !hr.hours.length) {
            $s('skHour').style.display='none'; const e=$s('chHour'); if(e){ e.style.display='block'; e.innerHTML=emptyHtml('Data tidak tersedia'); } return;
        }
        this._renderTimeChart('chHour','skHour', hr.hours, hr.neg||[], hr.pos||[], hr.neu||[], hr.total||[], true);
    },

    _renderTimeChart(domId, skelId, labels, negD, posD, neuD, totals, isHour) {
        const skEl  = $s(skelId);
        const mainEl = $s(domId);
        if (!mainEl) return;
        if (!labels.length || !totals.some(v=>v>0)) {
            if(skEl) skEl.style.display='none'; mainEl.style.display='block'; mainEl.innerHTML=emptyHtml('Data tidak tersedia'); return;
        }
        if(skEl) skEl.style.display='none';
        mainEl.style.display='block';
        mainEl.innerHTML='';

        const opts = {
            chart:{ type:'bar', height:320, stacked:true, toolbar:{ show:false }, fontFamily:'inherit', events:{ click:(_e,_ctx,cfg)=>{ const sentMap={0:'all',1:'pos',2:'neg'}; SNTPanel.open('all', sentMap[cfg.seriesIndex]||'all', window.innerWidth/2, window.innerHeight/2); } } },
            plotOptions:{ bar:{ borderRadius: isHour?2:4, columnWidth: isHour?'70%':'55%' } },
            series:[
                { name:'Positive', data: posD.map(v=>Number(v||0)) },
                { name:'Negative', data: negD.map(v=>Number(v||0)) },
                { name:'Neutral',  data: neuD.map(v=>Number(v||0)) },
            ],
            colors:['#2FC6F6','#EF4444','#94A3B8'],
            xaxis:{
                categories: labels,
                labels:{ rotate: isHour?-45:0, rotateAlways: isHour, style:{ colors:'#94A3B8', fontSize: isHour?'9px':'11px', fontFamily:'inherit' } },
                axisBorder:{ show:false }, axisTicks:{ show:false }
            },
            yaxis:{ labels:{ formatter: v=>numK(v), style:{ colors:'#94A3B8', fontSize:'10px', fontFamily:'inherit' } }, axisBorder:{ show:false }, axisTicks:{ show:false } },
            grid:{ borderColor:'#F1F5F9', strokeDashArray:4 },
            legend:{ position:'bottom', horizontalAlign:'left', labels:{ colors:'#94A3B8' } },
            dataLabels:{ enabled:false },
            tooltip:{ y:{ formatter: v=>numFmt(v)+' mentions' } }
        };
        const chart = new ApexCharts(mainEl, opts);
        chart.render();
    }
};

/* ══ PANEL (identical pattern to DO) ══ */
const SNTPanel = (function() {
    const SENT_NORM = { '1':'pos','positive':'pos','positif':'pos','pos':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg','neg':'neg' };
    const normSent  = item => SENT_NORM[String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim()] || 'neu';
    let _allItems=[], _filtered=[], _curSent='all', _curPlat=null;

    function showPlatPicker(x, y, sent) {
        const pp = $s('sntPlatPicker'); if (!pp) return;
        pp.querySelectorAll('.do-plat-btn').forEach(btn => {
            const m = (btn.getAttribute('onclick')||'').match(/openPlatform\('([^']+)'/);
            if (m) btn.setAttribute('onclick', `SNTPanel.openPlatform('${m[1]}','${sent||'all'}')`);
        });
        const pw=180, ph=250, vw=window.innerWidth, vh=window.innerHeight;
        let left=x+10, top=y-10;
        if (left+pw>vw-8) left=x-pw-10; if (top+ph>vh-8) top=vh-ph-8; if (top<8) top=8;
        pp.style.left=left+'px'; pp.style.top=top+'px'; pp.classList.add('show');
    }
    function openPlatform(platform, sentiment) {
        $s('sntPlatPicker')&&$s('sntPlatPicker').classList.remove('show');
        open(platform, sentiment||'all');
    }

    async function open(platform, sentiment, x, y) {
        _curPlat=platform; _curSent=sentiment||'all'; _allItems=[]; _filtered=[];
        const meta = SNTCfg.platMeta[platform] || { label:platform, color:SNTCfg.primary };
        SNTDetail.close();
        $s('sntPanelDot').style.background  = meta.color;
        $s('sntPanelTitle').textContent     = meta.label;
        $s('sntPanelMeta').textContent      = SNTCfg.sd + ' – ' + SNTCfg.ed;
        document.querySelectorAll('#sntSlidePanel .do-panel-tab').forEach(t => t.classList.toggle('active', t.dataset.s===_curSent));
        const ri = $s('sntPanelRefreshIcon');
        if (ri) ri.style.cssText = 'animation:spin .7s linear infinite;display:inline-block;';
        const list = $s('sntPanelList');
        list.innerHTML = '<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>';
        const overlay = $s('sntPanelOverlay'), panel = $s('sntSlidePanel');
        overlay.classList.remove('hiding'); panel.classList.remove('hiding');
        overlay.classList.add('show'); panel.classList.add('show');
        try {
            _allItems = await _fetch(platform);
            _filtered = _filterBySent(_allItems, _curSent);
            _render(list, _filtered, platform, meta.color);
        } catch(err) {
            list.innerHTML = '<div style="padding:50px 20px;text-align:center;color:#94A3B8;font-size:13px;">Gagal memuat data</div>';
        } finally {
            if (ri) ri.style.cssText = '';
        }
    }

    function refresh() { if (_curPlat) open(_curPlat, _curSent); }

    function close() {
        const overlay=$s('sntPanelOverlay'), panel=$s('sntSlidePanel');
        panel.classList.add('hiding'); overlay.classList.add('hiding');
        setTimeout(()=>{ panel.classList.remove('show','hiding'); overlay.classList.remove('show','hiding'); SNTDetail.close(); }, 240);
    }
    function closeByOverlay() { close(); }

    function filterSent(sent) {
        _curSent = sent;
        document.querySelectorAll('#sntSlidePanel .do-panel-tab').forEach(t => t.classList.toggle('active', t.dataset.s===sent));
        _filtered = _filterBySent(_allItems, sent);
        const meta = SNTCfg.platMeta[_curPlat] || { color: SNTCfg.primary };
        _render($s('sntPanelList'), _filtered, _curPlat, meta.color);
    }

    function _filterBySent(items, sent) { return sent==='all' ? items : items.filter(i => normSent(i)===sent); }

    function _extractItems(d) {
        if (Array.isArray(d&&d.data&&d.data.data)) return d.data.data;
        if (Array.isArray(d&&d.data)) return d.data;
        if (Array.isArray(d&&d.statuses)) return d.statuses;
        if (Array.isArray(d&&d.results)) return d.results;
        if (Array.isArray(d)) return d;
        return [];
    }

    async function _fetch(platform) {
        const q = `project_id=${SNTCfg.pid}&start_date=${SNTCfg.sd}&end_date=${SNTCfg.ed}&rows=60&start=0`;
        if (platform === 'all' || platform === 'social') {
            const plats = platform==='social' ? ['twit','fb','instagram','youtube','tiktok'] : ['doc','twit','fb','instagram','youtube','tiktok'];
            const results = await Promise.allSettled(plats.map(p => _fetchOne(p, q)));
            let merged = [];
            results.forEach(r => { if (r.status==='fulfilled') merged = merged.concat(r.value); });
            merged.sort((a,b) => (b.date_created||b.created_at||'').localeCompare(a.date_created||a.created_at||''));
            return merged;
        }
        return _fetchOne(platform, q);
    }

    async function _fetchOne(platform, q) {
        const eps = {
            doc:       `/mk/api/news/mentions?${q}`,
            twit:      `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
            fb:        `/mk/api/news/fb-top-status?${q}&sub=fblike`,
            instagram: `/mk/api/news/ig-top-status?${q}`,
            youtube:   `/mk/api/news/ytb-top-status?${q}`,
            tiktok:    `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
        };
        const url = eps[platform]; if (!url) return [];
        const ctrl = new AbortController(); const tid = setTimeout(()=>ctrl.abort(), 30000);
        try {
            const r = await fetch(url, { signal: ctrl.signal }); clearTimeout(tid);
            if (!r.ok) return [];
            const d = await r.json();
            return _extractItems(d).map(i => Object.assign({}, i, { _platform: platform }));
        } catch(e) { clearTimeout(tid); return []; }
    }

    function _renderItem(item, platform, accentColor) {
        const plat = item._platform || platform;
        const meta = SNTCfg.platMeta[plat] || { label:plat, color:accentColor };
        const name = (item.from_name||item.page_name||item.author_nickname||item.channel_title||item.author_name||item.username||item.author_scr_name||item.screen_name||item.publisher||item.source_name||'').trim() || 'Unknown';
        const text = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,150);
        const av   = (item.avatar_url||item.profile_image_url||item.author_image||item.thumbnail||'').trim();
        const dt   = (item.date_created||item.created_at||'').split('T')[0];
        const sent = normSent(item);
        const sentLbl = { pos:'Pos', neg:'Neg', neu:'Neu' }[sent];
        const words = name.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
        const ini   = (words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||'?')).toUpperCase();
        const avHtml = (av&&av.startsWith('http')) ? `<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini}'">` : ini;
        const enc = encodeURIComponent(JSON.stringify(item));
        return `<div class="do-panel-item" onclick="SNTDetail.openEncoded('${enc}','${plat}')">
            <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
            <div class="do-panel-item-body">
                <div class="do-panel-author">${esc(name)}</div>
                <div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div>
                <div class="do-panel-footer">
                    <span class="do-sent-badge do-sent-badge--${sent}">${sentLbl}</span>
                    <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                    <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                    ${dt ? `<span style="margin-left:auto;">${dt}</span>` : ''}
                </div>
            </div>
        </div>`;
    }

    function _render(list, items, platform, accentColor) {
        if (!items.length) { list.innerHTML='<div style="padding:50px 20px;text-align:center;color:#94A3B8;font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>'; return; }
        list.innerHTML = items.slice(0,60).map(i => _renderItem(i, platform, accentColor)).join('');
        if (items.length>60) list.insertAdjacentHTML('beforeend', `<div style="padding:9px;text-align:center;font-size:10px;color:#94A3B8;font-weight:600;border-top:1px dashed #E2E8F0;">+${(items.length-60).toLocaleString()} mentions lainnya</div>`);
    }

    return { open, close, closeByOverlay, showPlatPicker, openPlatform, filterSent, refresh };
})();

/* ══ DETAIL ══ */
const SNTDetail = {
    openEncoded(enc, plat) { try { this.open(JSON.parse(decodeURIComponent(enc)), plat); } catch(e){} },
    open(item, platform) {
        const panel = $s('sntDetailPanel'), body = $s('sntDetailBody'), title = $s('sntDetailTitle');
        if (!panel||!body) return;
        const truePlat = item._platform || platform;
        const meta = SNTCfg.platMeta[truePlat] || { label:truePlat, color:'#038047' };
        const SNORM = { '1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg' };
        const sent = SNORM[String(item.class_sentiment||item.sentiment||'0').toLowerCase()] || 'neu';
        const SLBL = { pos:'Positif', neg:'Negatif', neu:'Netral' };
        const SBGS = { pos:'do-dp2-sent--pos', neg:'do-dp2-sent--neg', neu:'do-dp2-sent--neu' };
        const name = (item.from_name||item.page_name||item.author_nickname||item.channel_title||item.author_name||item.username||item.author_scr_name||item.screen_name||item.publisher||item.source_name||'Unknown').trim();
        const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
        const av  = (item.avatar_url||item.profile_image_url||item.author_image||item.thumbnail||'').trim();
        const url = item.url||item.link||'';
        const dt  = item.date_created||item.created_at||'';
        let dtFmt = '';
        if (dt) { try { dtFmt = new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); } catch(e){ dtFmt=dt.split('T')[0]; } }
        const words = name.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
        const ini   = (words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||'?')).toUpperCase();
        const avHtml = (av&&av.startsWith('http')) ? `<img src="${esc(av)}" onerror="this.parentElement.textContent='${ini}'">` : ini;
        const statsMap = {
            twit:[['Retweet',item.num_retweeted||0],['Like',item.num_likes||0],['Quote',item.num_quote||0]],
            fb:  [['Like',item.likes||item.num_likes||0],['Share',item.shares||0],['Comment',item.num_comments||0]],
            instagram:[['Like',item.num_likes||0],['Comment',item.num_comments||0],['View',item.num_views||0]],
            youtube:  [['View',item.num_views||0],['Like',item.num_likes||0],['Comment',item.num_comments||0]],
            tiktok:   [['Play',item.views||0],['Like',item.likes||0],['Share',item.shares||0]],
            doc:      [['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]],
        };
        const stats = statsMap[truePlat]||[];
        const statsHtml = stats.some(s=>parseInt(s[1])>0) ? `<div class="do-dp2-stats">${stats.map(([l,v])=>`<div class="do-dp2-stat"><div class="do-dp2-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="do-dp2-stat-lbl">${l}</div></div>`).join('')}</div>` : '';
        title.textContent = name;
        body.innerHTML = `
            <div class="do-dp2-avatar-row">
                <div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                <div><div class="do-dp2-name">${esc(name)}</div><span class="do-dp2-plat-badge" style="background:${meta.color}22;color:${meta.color};">${meta.label}</span></div>
            </div>
            ${dtFmt ? `<div class="do-dp2-meta"><span>${dtFmt}</span></div>` : ''}
            <div class="do-dp2-sent ${SBGS[sent]}">${SLBL[sent]}</div>
            ${content ? `<div class="do-dp2-content">${esc(content)}</div>` : ''}
            ${statsHtml}
            ${url ? `<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out"></i> Lihat Sumber Asli</a>` : ''}`;
        panel.classList.add('show');
    },
    close() { $s('sntDetailPanel')&&$s('sntDetailPanel').classList.remove('show'); }
};

/* ══ EXPORT (identical to DO) ══ */
const SNTExport = (function() {
    let _timer = null;
    function _toast(msg, type, dur) {
        const t=$s('sntExportToast'), m=$s('sntExportToastMsg'), ico=$s('sntExportToastIcon');
        if (!t||!m) return;
        m.textContent = msg; dur = dur||3200;
        t.className = 'export-toast show ' + (type!=='default'?type:'');
        ico.className = 'ph ' + ({ success:'ph-check-circle', error:'ph-x-circle', default:'ph-spinner' }[type]||'ph-spinner');
        clearTimeout(_timer); _timer = setTimeout(()=>t.classList.remove('show'), dur);
    }
    function _btnState(btn, loading) { if(!btn) return; btn.disabled=loading; btn.classList.toggle('exporting',loading); }
    function _freeze() {
        if (document.getElementById('__snt_freeze')) return;
        const s=document.createElement('style'); s.id='__snt_freeze';
        s.textContent='*{animation:none!important;transition:none!important;}';
        document.head.appendChild(s);
    }
    function _unfreeze() { const s=document.getElementById('__snt_freeze'); if(s) s.remove(); }
    function _resizeAllCharts() { Object.values(SNTCharts._i).forEach(c=>{ try{ if(!c.isDisposed()) c.resize(); }catch(e){} }); }
    function _capture(el, bg) {
        return html2canvas(el, { scale:2, useCORS:true, allowTaint:false, backgroundColor:bg||'#f1f5f9', logging:false, removeContainer:true, windowHeight:el.scrollHeight, height:el.scrollHeight, ignoreElements: e=>e.hasAttribute('data-html2canvas-ignore') });
    }
    function _drawHeader(pdf, pW, pH, label, page, total) {
        pdf.setFillColor(3,128,71); pdf.rect(0,0,pW,11,'F');
        pdf.setTextColor(255,255,255); pdf.setFontSize(9); pdf.setFont('helvetica','bold');
        pdf.text('SMADIMENT — Engagement Sentiment'+(label?' · '+label:''), 10, 7.5);
        const now = new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
        pdf.setFontSize(7); pdf.setFont('helvetica','normal'); pdf.text('Generated: '+now, pW-10, 7.5, { align:'right' });
        pdf.setFontSize(7); pdf.setTextColor(148,163,184); pdf.text('Halaman '+page+' / '+total, pW/2, pH-3, { align:'center' });
    }
    function _fitCanvas(pdf, canvas, margin, pW, pH) {
        const uw=pW-margin*2, uh=pH-14-10, r=Math.min(uw/canvas.width, uh/canvas.height), dw=canvas.width*r, dh=canvas.height*r;
        pdf.addImage(canvas.toDataURL('image/png'),'PNG', margin+(uw-dw)/2, 14, dw, dh);
    }
    const _stamp = () => new Date().toISOString().slice(0,10).replace(/-/g,'');
    const _labels = { sov:'Share of Voice', media:'Sentiments by Media', trend:'Sentiment Trend', breakdown:'Platform Breakdown', weekday:'By Weekday', hour:'By Hour' };

    function run(type, btn) {
        if (!window.html2canvas) { _toast('html2canvas tidak tersedia','error'); return; }
        const btnPdf=$s('sntPageExpPdf'), btnImg=$s('sntPageExpImg');
        _btnState(btnPdf,true); _btnState(btnImg,true);
        _toast(type==='pdf'?'Menyiapkan PDF…':'Mengambil gambar…','default',99999);
        window.scrollTo({ top:0 });
        setTimeout(()=>{
            _resizeAllCharts(); _freeze();
            requestAnimationFrame(()=>{ requestAnimationFrame(()=>{
                setTimeout(()=>{
                    _capture($s('sntPageExportArea'),'#f1f5f9').then(canvas=>{
                        _unfreeze();
                        if (type==='image') {
                            const a=document.createElement('a'); a.download='engagement_sentiment_'+_stamp()+'.png'; a.href=canvas.toDataURL('image/png'); a.click();
                            _toast('Gambar berhasil diunduh!','success');
                        } else {
                            const jsPDF=window.jspdf&&window.jspdf.jsPDF;
                            const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
                            const pW=pdf.internal.pageSize.getWidth(), pH=pdf.internal.pageSize.getHeight();
                            _drawHeader(pdf,pW,pH,'Overview',1,1); _fitCanvas(pdf,canvas,10,pW,pH);
                            pdf.save('engagement_sentiment_'+_stamp()+'.pdf');
                            _toast('PDF berhasil diunduh!','success');
                        }
                    }).catch(err=>{ _unfreeze(); _toast('Export gagal: '+err.message,'error'); })
                    .finally(()=>{ _btnState(btnPdf,false); _btnState(btnImg,false); });
                },400);
            }); });
        },350);
    }

    function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas) { _toast('html2canvas tidak tersedia','error'); return; }
        _btnState(btn,true); _toast(type==='pdf'?'Menyiapkan PDF card…':'Mengambil gambar card…','default',99999);
        const area = document.getElementById(areaId);
        if (!area) { _toast('Area tidak ditemukan','error'); _btnState(btn,false); return; }
        window.scrollTo({ top:0 });
        setTimeout(()=>{
            _resizeAllCharts(); _freeze();
            requestAnimationFrame(()=>{ requestAnimationFrame(()=>{
                setTimeout(()=>{
                    _capture(area,'#ffffff').then(canvas=>{
                        _unfreeze();
                        const label=_labels[cardKey]||cardKey, fname='snt_'+cardKey+'_'+_stamp();
                        if (type==='image') {
                            const a=document.createElement('a'); a.download=fname+'.png'; a.href=canvas.toDataURL('image/png'); a.click();
                            _toast('Gambar berhasil diunduh!','success');
                        } else {
                            const jsPDF=window.jspdf&&window.jspdf.jsPDF;
                            const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
                            const pW=pdf.internal.pageSize.getWidth(), pH=pdf.internal.pageSize.getHeight();
                            _drawHeader(pdf,pW,pH,label,1,1); _fitCanvas(pdf,canvas,10,pW,pH);
                            pdf.save(fname+'.pdf'); _toast('PDF berhasil diunduh!','success');
                        }
                    }).catch(err=>{ _unfreeze(); _toast('Export gagal: '+err.message,'error'); })
                    .finally(()=>{ _btnState(btn,false); });
                },400);
            }); });
        },350);
    }
    return { run, runCard };
})();

/* ══ INIT ══ */
document.addEventListener('mousedown', e => {
    const pp = $s('sntPlatPicker');
    if (pp && pp.classList.contains('show') && !pp.contains(e.target)) pp.classList.remove('show');
});
document.addEventListener('DOMContentLoaded', () => {
    SNTLoader.init();
    document.addEventListener('keydown', e => { if (e.key==='Escape') SNTPanel.close(); });
});
</script>
@endsection