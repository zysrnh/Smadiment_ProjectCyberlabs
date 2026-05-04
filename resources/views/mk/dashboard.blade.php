@extends('mk.layouts.app')

@section('title', 'Dashboard - SMADIMENT')

@section('styles')
    <style>
        /* ══ Design Tokens ══ */
        :root {
            --dash-primary: var(--bs-primary, #4361EE);
            --dash-primary-rgb: var(--bs-primary-rgb, 67, 97, 238);
            --dash-primary-lt: rgba(var(--dash-primary-rgb, 67, 97, 238), .10);
            --green: #4CAF50;
            --green-light: #E8F5E9;
            --red: #EF4444;
            --red-light: #FEF2F2;
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

        /* ══ Animations ══ */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px) }
            to   { opacity: 1; transform: translateY(0) }
        }
        @keyframes fadeIn {
            from { opacity: 0 }
            to   { opacity: 1 }
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0 }
            to   { transform: translateX(0);    opacity: 1 }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0);    opacity: 1 }
            to   { transform: translateX(100%); opacity: 0 }
        }
        @keyframes overlayIn  { from { opacity: 0 } to { opacity: 1 } }
        @keyframes overlayOut { from { opacity: 1 } to { opacity: 0 } }
        @keyframes spin    { to { transform: rotate(360deg) } }
        @keyframes shimmer {
            0%   { background-position: -200% 0 }
            100% { background-position:  200% 0 }
        }
        @keyframes pulseP {
            0%,100% { box-shadow: 0 0 0 3px var(--dash-primary-lt) }
            50%      { box-shadow: 0 0 0 6px transparent }
        }

        .fade-up    { animation: fadeUp .38s ease-out both; }
        .fade-up-d1 { animation-delay: .05s }
        .fade-up-d2 { animation-delay: .10s }
        .fade-up-d3 { animation-delay: .15s }
        .fade-up-d4 { animation-delay: .20s }

        /* ══ KPI Card Icons ══ */
        .kpi-icon-bg {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.2); font-size: 24px;
            color: #fff; flex-shrink: 0;
        }

        /* ══ Project Sidebar ══ */
        .proj-sidebar-card { position: sticky; top: 80px; }
        .proj-list-scroll  { max-height: 600px; overflow-y: auto; }
        .proj-list-scroll::-webkit-scrollbar       { width: 4px; }
        .proj-list-scroll::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }

        .proj-item { transition: all .15s ease; cursor: pointer; }
        .proj-item:hover { background: var(--slate-50) !important; }
        .proj-item.active-sidebar {
            background: var(--dash-primary-lt) !important;
            border-left: 3px solid var(--dash-primary) !important;
        }
        .proj-item .proj-status-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--dash-primary); flex-shrink: 0;
            box-shadow: 0 0 0 3px var(--dash-primary-lt);
            animation: pulseP 2.5s infinite;
        }

        /* ══ Lazy Card Transitions ══ */
        .lazy-card {
            opacity: 0; transform: translateY(14px);
            transition: opacity .4s ease, transform .4s ease, border-color .2s, box-shadow .2s;
        }
        .lazy-card.card-visible { opacity: 1; transform: translateY(0); }
        .lazy-card.highlighted {
            border-color: var(--dash-primary) !important;
            box-shadow: 0 0 0 3px var(--dash-primary-lt), var(--shadow-md) !important;
        }

        /* ══ Stat Chips ══ */
        .stat-chip.clickable {
            cursor: pointer; transition: transform .13s, box-shadow .13s; user-select: none;
        }
        .stat-chip.clickable:hover  { transform: translateY(-2px); box-shadow: var(--shadow-sm); }
        .stat-chip.clickable:active { transform: translateY(0); }

        /* ══ KPI Hover ══ */
        .kpi-card-hover {
            will-change: transform, box-shadow;
            transition: transform .25s cubic-bezier(.34,1.56,.64,1) !important,
                        box-shadow .25s ease !important, filter .25s ease !important;
            cursor: pointer; position: relative !important; overflow: hidden !important;
        }
        .kpi-card-hover::before {
            content: ''; position: absolute; top: 0; bottom: 0; left: -100%;
            width: 60%; background: linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);
            pointer-events: none; z-index: 1; transition: none;
        }
        @keyframes kpiShimmer { 100% { left: 150%; } }
        @keyframes kpiIconBounce {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.15); }
            100% { transform: scale(1); }
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

        /* ══ Chart Area ══ */
        .chart-container { height: 280px; position: relative; }
        .chart-loading {
            position: absolute; inset: 0; display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 8px;
            background: #fff; z-index: 2; transition: opacity .3s;
        }
        .chart-loading.hidden { opacity: 0; pointer-events: none; }
        .spin-ring {
            width: 26px; height: 26px; border: 2.5px solid var(--slate-100);
            border-top-color: var(--dash-primary); border-radius: 50%;
            animation: spin .65s linear infinite;
        }
        .chart-loading span { font-size: 11px; font-weight: 600; color: var(--slate-400); }
        .chart-empty {
            height: 100%; display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 6px;
            color: var(--slate-400); font-size: 12px; font-weight: 600;
        }
        .chart-empty i { font-size: 34px; color: var(--slate-300); display: block; }

        /* ══ Skeleton ══ */
        .sk-block {
            border-radius: 4px;
            background: linear-gradient(90deg, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%);
            background-size: 200% 100%; animation: shimmer 1.4s infinite;
        }
        .lazy-sentinel { height: 1px; }

        /* ══ Scroll-to-top ══ */
        .scroll-top-btn {
            position: fixed; bottom: 20px; right: 70px;
            width: 36px; height: 36px; background: var(--dash-primary);
            border-radius: var(--radius); display: flex; align-items: center;
            justify-content: center; color: #fff; font-size: 15px;
            box-shadow: var(--shadow-md); cursor: pointer;
            opacity: 0; pointer-events: none;
            transition: opacity .2s, transform .2s; z-index: 998; border: none;
        }
        .scroll-top-btn.visible { opacity: 1; pointer-events: all; }
        .scroll-top-btn:hover   { transform: translateY(-2px); }

        /* ══ Page Export Toolbar ══ */
        .page-export-bar {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px;
            background: #fff; border: 1px solid var(--slate-200);
            border-radius: var(--radius); padding: 9px 14px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }
        .page-export-bar-left {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; font-weight: 700; color: var(--slate-600);
        }
        .page-export-bar-left i { font-size: 15px; color: var(--dash-primary); }
        .page-export-bar-right  { display: flex; gap: 8px; flex-wrap: wrap; }

        .page-export-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px;
            border-radius: var(--radius-sm);
            font-size: 16px; cursor: pointer;
            transition: all .15s ease;
            border: 1.5px solid transparent; font-family: inherit;
        }
        .page-export-btn-pdf {
            background: #fff3f3; color: #dc2626;
            border-color: #fca5a5;
        }
        .page-export-btn-pdf:hover {
            background: #dc2626; color: #fff; border-color: #dc2626;
        }
        .page-export-btn-img {
            background: var(--dash-primary-lt); color: var(--dash-primary);
            border-color: rgba(var(--dash-primary-rgb), .3);
        }
        .page-export-btn-img:hover {
            background: var(--dash-primary); color: #fff;
            border-color: var(--dash-primary);
        }
        .page-export-btn:disabled {
            opacity: .55; cursor: not-allowed; pointer-events: none;
        }
        .page-export-btn .export-spinner {
            width: 13px; height: 13px;
            border: 2px solid currentColor; border-top-color: transparent;
            border-radius: 50%; animation: spin .65s linear infinite;
            display: none;
        }
        .page-export-btn.exporting .export-spinner { display: inline-block; }
        .page-export-btn.exporting .export-icon    { display: none; }

        /* ══ Export Buttons (per-card) ══ */
        .export-btn {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 12px; font-weight: 600; white-space: nowrap;
            transition: all .15s ease;
        }
        .export-btn:disabled {
            opacity: .65; cursor: not-allowed; pointer-events: none;
        }
        .export-btn .export-spinner {
            width: 13px; height: 13px;
            border: 2px solid currentColor; border-top-color: transparent;
            border-radius: 50%; animation: spin .65s linear infinite;
            display: none;
        }
        .export-btn.exporting .export-spinner { display: inline-block; }
        .export-btn.exporting .export-icon    { display: none; }

        /* Export toast notification */
        .export-toast {
            position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(20px);
            background: var(--slate-900); color: #fff; border-radius: var(--radius);
            padding: 10px 18px; font-size: 12px; font-weight: 600;
            box-shadow: var(--shadow-lg); z-index: 99999;
            opacity: 0; pointer-events: none;
            transition: opacity .22s ease, transform .22s ease;
            display: flex; align-items: center; gap: 8px;
            white-space: nowrap;
        }
        .export-toast.show {
            opacity: 1; pointer-events: none; transform: translateX(-50%) translateY(0);
        }
        .export-toast.success { background: #065f46; }
        .export-toast.error   { background: #991b1b; }

        /* ══════════════════════════════════════════════════════
           SLIDE PANEL
        ══════════════════════════════════════════════════════ */
        .do-panel-overlay {
            position: fixed; inset: 0; z-index: 9000;
            background: rgba(15,23,42,.45); backdrop-filter: blur(4px); display: none;
        }
        .do-panel-overlay.show    { display: block; animation: overlayIn .22s ease-out; }
        .do-panel-overlay.hiding  { animation: overlayOut .22s ease-out forwards; }

        .do-panel {
            position: fixed; top: 0; right: 0; bottom: 0; z-index: 9001;
            width: 480px; max-width: 100vw; background: #fff;
            display: none; flex-direction: column;
            border-left: 1px solid var(--slate-200);
            box-shadow: -8px 0 40px rgba(15,23,42,.16);
        }
        .do-panel.show   { display: flex; animation: slideInRight .28s cubic-bezier(.4,0,.2,1); }
        .do-panel.hiding { animation: slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }

        .do-panel-header {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 16px; border-bottom: 1px solid var(--slate-200);
            background: var(--slate-50); flex-shrink: 0;
        }
        .do-panel-dot   { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
        .do-panel-title {
            font-size: 13px; font-weight: 700; color: var(--slate-900);
            flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .do-panel-close {
            width: 28px; height: 28px; border-radius: var(--radius-sm);
            border: 1px solid var(--slate-200); background: #fff; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--slate-500); font-size: 16px; transition: all .14s; flex-shrink: 0;
        }
        .do-panel-close:hover { background: var(--red); border-color: var(--red); color: #fff; }

        .do-panel-actions {
            display: flex; align-items: center; gap: 7px; padding: 7px 12px;
            border-bottom: 1px solid var(--slate-200); background: #fff; flex-shrink: 0;
        }
        .do-panel-meta {
            flex: 1; font-size: 10px; font-weight: 700; color: var(--slate-400);
            text-transform: uppercase; letter-spacing: .5px;
            display: flex; align-items: center; gap: 5px;
        }
        .do-panel-tabs {
            display: flex; background: var(--slate-100); border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm); padding: 2px; gap: 2px;
        }
        .do-panel-tab {
            padding: 3px 9px; border-radius: 3px; border: none; background: transparent;
            font-size: 11px; font-weight: 700; cursor: pointer; transition: all .13s;
            color: var(--slate-500); font-family: inherit;
        }
        .do-panel-tab:hover { background: #fff; }
        .do-panel-tab.active { background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .do-panel-tab.active[data-s="all"] { color: var(--dash-primary); }
        .do-panel-tab.neg.active { color: #EF4444; }
        .do-panel-tab.pos.active { color: #10B981; }
        .do-panel-tab.neu.active { color: var(--slate-500); }

        .do-panel-list { overflow-y: auto; flex: 1; padding: 2px 0; min-height: 0; }
        .do-panel-list::-webkit-scrollbar       { width: 4px; }
        .do-panel-list::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }

        .do-panel-item {
            display: flex; gap: 10px; padding: 10px 14px;
            border-bottom: 1px solid var(--slate-50); cursor: pointer;
            transition: background .1s; align-items: flex-start;
        }
        .do-panel-item:hover         { background: #f0f9ff; }
        .do-panel-item:last-child     { border-bottom: none; }

        .do-panel-avatar {
            width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; color: #fff;
            border: 1.5px solid var(--slate-200); overflow: hidden;
        }
        .do-panel-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

        .do-panel-item-body { flex: 1; min-width: 0; }
        .do-panel-author {
            font-size: 12px; font-weight: 700; color: var(--slate-900);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .do-panel-handle  { font-size: 10px; color: var(--slate-400); font-weight: 500; margin-bottom: 2px; }
        .do-panel-text {
            font-size: 11px; color: var(--slate-600); line-height: 1.5;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 4px;
        }
        .do-panel-footer {
            display: flex; align-items: center; gap: 5px;
            font-size: 10px; color: var(--slate-400); flex-wrap: wrap;
        }

        .do-sent-badge { padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
        .do-sent-badge--pos { background: #dbeafe; color: #1d4ed8; }
        .do-sent-badge--neg { background: #fee2e2; color: #991b1b; }
        .do-sent-badge--neu { background: var(--slate-100); color: var(--slate-500); }

        .do-panel-loading {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; height: 100%; gap: 12px;
            color: var(--slate-400); font-size: 13px; font-weight: 600;
        }
        .do-panel-spinner {
            width: 28px; height: 28px; border: 2.5px solid var(--slate-100);
            border-top-color: var(--dash-primary); border-radius: 50%;
            animation: spin .65s linear infinite;
        }

        /* ── Detail sub-panel ── */
        .do-detail-panel {
            position: absolute; inset: 0; background: #fff; z-index: 5;
            display: none; flex-direction: column;
            animation: slideInRight .2s cubic-bezier(.4,0,.2,1);
        }
        .do-detail-panel.show { display: flex; }

        .do-dp2-header {
            display: flex; align-items: center; gap: 8px; padding: 12px 14px;
            background: var(--slate-50); border-bottom: 1px solid var(--slate-200); flex-shrink: 0;
        }
        .do-dp2-back {
            width: 28px; height: 28px; border-radius: var(--radius-sm);
            border: 1px solid var(--slate-200); background: #fff; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--slate-500); transition: all .13s; font-size: 14px;
        }
        .do-dp2-back:hover { background: var(--dash-primary-lt); color: var(--dash-primary); border-color: var(--dash-primary); }
        .do-dp2-title {
            font-size: 13px; font-weight: 700; color: var(--slate-900);
            flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }

        .do-dp2-body { overflow-y: auto; flex: 1; padding: 16px; }
        .do-dp2-body::-webkit-scrollbar       { width: 4px; }
        .do-dp2-body::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }

        .do-dp2-avatar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .do-dp2-avatar-lg {
            width: 46px; height: 46px; border-radius: 50%; color: #fff; font-weight: 700; font-size: 16px;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--slate-200); overflow: hidden; flex-shrink: 0;
        }
        .do-dp2-avatar-lg img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .do-dp2-name    { font-size: 14px; font-weight: 700; color: var(--slate-900); }
        .do-dp2-handle  { font-size: 11px; color: var(--slate-400); font-weight: 500; }
        .do-dp2-plat-badge {
            display: inline-block; padding: 2px 8px; border-radius: 3px;
            font-size: 10px; font-weight: 700; margin-top: 3px;
        }
        .do-dp2-meta    { font-size: 11px; color: var(--slate-400); font-weight: 500; margin-bottom: 10px; }
        .do-dp2-sent {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 3px; font-size: 11px; font-weight: 700; margin-bottom: 10px;
        }
        .do-dp2-sent--pos { background: #dbeafe; color: #1d4ed8; }
        .do-dp2-sent--neg { background: #fee2e2; color: #991b1b; }
        .do-dp2-sent--neu { background: var(--slate-100); color: var(--slate-500); }

        .do-dp2-content {
            font-size: 12px; color: var(--slate-700); line-height: 1.7; margin-bottom: 12px;
            background: var(--slate-50); border-radius: var(--radius-sm);
            padding: 10px 12px; border: 1px solid var(--slate-200); word-break: break-word;
        }
        .do-dp2-media { border-radius: var(--radius-sm); overflow: hidden; margin-bottom: 10px; }
        .do-dp2-media img { width: 100%; max-height: 220px; object-fit: cover; display: block; }

        .do-dp2-stats {
            display: grid; grid-template-columns: repeat(3,1fr); gap: 6px; margin-bottom: 10px;
        }
        .do-dp2-stat {
            background: var(--slate-50); border-radius: var(--radius-sm); padding: 8px 10px;
            border: 1px solid var(--slate-200); text-align: center;
        }
        .do-dp2-stat-val { font-size: 14px; font-weight: 700; color: var(--slate-900); }
        .do-dp2-stat-lbl {
            font-size: 9px; font-weight: 700; color: var(--slate-400);
            text-transform: uppercase; letter-spacing: .4px; margin-top: 1px;
        }

        /* ── Detail action buttons ── */
        .do-dp2-actions { display: flex; flex-direction: column; gap: 7px; margin-top: 4px; }
        .do-dp2-link {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 14px; background: var(--dash-primary); color: #fff;
            border-radius: var(--radius-sm); font-size: 12px; font-weight: 700;
            text-decoration: none; transition: filter .14s;
        }
        .do-dp2-link:hover { filter: brightness(1.1); color: #fff; }
        .do-dp2-link i    { font-size: 13px; }
        .do-dp2-link-sec {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 8px 14px; background: transparent; color: var(--dash-primary);
            border: 1.5px solid var(--dash-primary);
            border-radius: var(--radius-sm); font-size: 12px; font-weight: 700;
            text-decoration: none; transition: background .14s, color .14s;
        }
        .do-dp2-link-sec:hover { background: var(--dash-primary-lt); color: var(--dash-primary); }
        .do-dp2-link-sec i { font-size: 13px; }
        .do-dp2-link-news {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 14px; background: #0284c7; color: #fff;
            border-radius: var(--radius-sm); font-size: 12px; font-weight: 700;
            text-decoration: none; transition: filter .14s;
        }
        .do-dp2-link-news:hover { filter: brightness(1.1); color: #fff; }
        .do-dp2-link-news i { font-size: 13px; }

        /* ── Platform picker ── */
        .do-plat-picker {
            position: fixed; z-index: 20000; background: #fff;
            border: 1px solid var(--slate-200); border-radius: var(--radius);
            box-shadow: var(--shadow-lg); padding: 5px; min-width: 175px;
            font-family: inherit; display: none; animation: fadeUp .14s ease-out;
        }
        .do-plat-picker.show { display: block; }
        .do-plat-picker-head {
            padding: 4px 9px 6px; font-size: 10px; font-weight: 700;
            color: var(--slate-400); text-transform: uppercase; letter-spacing: .5px;
            border-bottom: 1px solid var(--slate-100); margin-bottom: 3px;
        }
        .do-plat-btn {
            display: flex; align-items: center; gap: 7px; padding: 7px 10px;
            border-radius: var(--radius-sm); font-size: 12px; font-weight: 600;
            cursor: pointer; background: transparent; border: none;
            font-family: inherit; width: 100%; text-align: left;
            color: var(--slate-700); transition: background .12s;
        }
        .do-plat-btn:hover { background: var(--dash-primary-lt); color: var(--dash-primary); }
        .do-plat-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-left: auto; }

        @media(max-width:640px) {
            .do-panel { width: 100vw; }
        }
    </style>
@endsection

@section('page-title', 'Dashboard')

@section('content')

    @php
        $totalMentions = collect($projects)->sum('total_mentions');
        $totalPositive = collect($projects)->sum(fn($p) => $p['sentiment_summary']['positive'] ?? 0);
        $totalNeutral  = collect($projects)->sum(fn($p) => $p['sentiment_summary']['neutral']  ?? 0);
        $totalNegative = collect($projects)->sum(fn($p) => $p['sentiment_summary']['negative'] ?? 0);
        $projectCount  = count($projects);
        $initials      = strtoupper(substr(auth()->user()->name ?? 'U', 0, 2));
    @endphp

    <script>
        const CHART_DATA_URL  = '{{ url("/mk/dashboard/chart-data") }}';
        const START_DATE      = '{{ $startDate }}';
        const END_DATE        = '{{ $endDate }}';
        const CSRF_TOKEN      = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const PROJECT_TIMELINES = {};
        const DATA_OVERVIEW_URL = '{{ route("mk.data-overview") }}';
        const KPI_DATA = {
            projects : {{ $projectCount }},
            mentions : {{ $totalMentions }},
            positive : {{ $totalPositive }},
            neutral  : {{ $totalNeutral }},
            negative : {{ $totalNegative }},
            posPct   : '{{ $totalMentions > 0 ? round($totalPositive / $totalMentions * 100, 1) : 0 }}% of total',
            negPct   : '{{ $totalMentions > 0 ? round($totalNegative / $totalMentions * 100, 1) : 0 }}% of total',
            neuPct   : '{{ $totalMentions > 0 ? round($totalNeutral / $totalMentions * 100, 1) : 0 }}% of total',
            hasData  : {{ $totalMentions > 0 ? 'true' : 'false' }},
            allPids  : @json(collect($projects)->pluck('id'))
        };
    </script>

    {{-- ════ PAGE EXPORT WRAPPER ════ --}}
    <div id="pageExportArea">

    {{-- ══ KPI Cards ══ --}}
    <div class="row g-3 mb-3">
        {{-- 1. Positive --}}
        <div class="col-md-6 col-xl-3">
            <div class="card text-white fade-up fade-up-d1 kpi-card-hover" style="background:#06B6D4;" onclick="DashPanel.open('all','pos','ALL_PROJECTS')">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiPositive" style="transition:opacity .3s ease;">—</h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPositiveSub">
                                <i class="ph ph-smiley me-1"></i>0% of total
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                {{-- 3. Negative --}}
        <div class="col-md-6 col-xl-3">
            <div class="card text-white fade-up fade-up-d3 kpi-card-hover" style="background:#F59E0B;" onclick="DashPanel.open('all','neg','ALL_PROJECTS')">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiNegative" style="transition:opacity .3s ease;">—</h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNegativeSub">
                                <i class="ph ph-smiley-sad me-1"></i>0% of total
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- 2. Neutral --}}
        <div class="col-md-6 col-xl-3">
            <div class="card text-white fade-up fade-up-d2 kpi-card-hover" style="background:#4CAF50;" onclick="DashPanel.open('all','neu','ALL_PROJECTS')">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Neutral</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiNeutral" style="transition:opacity .3s ease;">—</h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNeutralSub">
                                <i class="ph ph-smiley-meh me-1"></i>0% of total
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg"><i class="ph ph-smiley-meh"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- 4. Total Mentions --}}
        <div class="col-md-6 col-xl-3">
            <div class="card text-white fade-up fade-up-d4 kpi-card-hover" style="background:#038047;" onclick="DashPanel.open('all','all','ALL_PROJECTS')">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p>
                            <h3 class="mb-0 text-white f-w-300" id="kpiMentions" style="transition:opacity .3s ease;">—</h3>
                            <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                                <i class="ph ph-activity me-1"></i>Across all projects
                            </p>
                        </div>
                        <div class="flex-shrink-0 ms-3">
                            <div class="kpi-icon-bg"><i class="ph ph-activity"></i></div>
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
            <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">
                KPI + Semua Project
            </span>
        </div>
        <div class="page-export-bar-right">
            <button type="button"
                    class="page-export-btn page-export-btn-pdf"
                    id="pageExportPdfBtn"
                    onclick="DashExport.runPage('pdf', this)"
                    title="Export halaman sebagai PDF">
                <i class="ph ph-file-pdf export-icon" style="font-size:16px;"></i>
                <span class="export-spinner"></span>
            </button>
            <button type="button"
                    class="page-export-btn page-export-btn-img"
                    id="pageExportImgBtn"
                    onclick="DashExport.runPage('image', this)"
                    title="Export halaman sebagai PNG">
                <i class="ph ph-image export-icon" style="font-size:16px;"></i>
                <span class="export-spinner"></span>
            </button>
        </div>
    </div>

    {{-- ══ Main Layout ══ --}}
    <div class="row">

        {{-- ── Project Sidebar ── --}}
        <div class="col-xl-4 col-md-5">
            <div class="card proj-sidebar-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ph ph-list-bullets me-2 text-muted"></i>Projects</h5>
                    <span class="badge bg-primary rounded-pill">{{ $projectCount }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="ph ph-magnifying-glass text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0"
                                   id="sidebarSearch" placeholder="Search project...">
                        </div>
                    </div>
                    <div class="proj-list-scroll" id="projList">
                        @forelse($projects as $project)
                            @php
                                $pId    = $project['id'] ?? '-';
                                $pTitle = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled';
                                $pTotal = $project['total_mentions'] ?? 0;
                                $pGroup = $project['project_group_name'] ?? '';
                            @endphp
                            <div class="proj-item d-flex align-items-center p-3 border-bottom"
                                 data-id="{{ $pId }}" data-name="{{ strtolower($pTitle) }}">
                                <div class="flex-shrink-0">
                                    <div class="proj-status-dot"></div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0 f-14">{{ $pTitle }}</h6>
                                    <p class="mb-0 text-muted f-12">
                                        {{ $pGroup ? $pGroup . ' · ' : '' }}{{ number_format($pTotal) }} mentions
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="ph ph-caret-right text-muted f-16"></i>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted small py-4 mb-0">No projects assigned</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Charts Area ── --}}
        <div class="col-xl-8 col-md-7">
            <div id="chartsArea">

                {{-- Skeleton --}}
                <div id="skeletonWrap">
                    @for($i = 0; $i < min(2, max(1, $projectCount)); $i++)
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="sk-block" style="width:8px;height:8px;border-radius:50%;flex-shrink:0;"></div>
                                    <div class="sk-block" style="width:160px;height:13px;"></div>
                                    <div class="ms-auto sk-block" style="width:86px;height:28px;border-radius:5px;"></div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="sk-block mb-2" style="width:220px;height:10px;"></div>
                                <div class="sk-block mb-4" style="width:150px;height:10px;"></div>
                                <div class="sk-block" style="width:100%;height:200px;border-radius:5px;"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Actual Cards --}}
                <div id="actualCards" style="display:none;">
                    @forelse($projects as $project)
                        @php
                            $id    = $project['id'] ?? '-';
                            $title = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled Project';
                            $group = $project['project_group_name'] ?? '-';
                            $type  = $project['project_type'] ?? 'Unknown';
                            $media = $project['media_types'] ?? 'All Media';
                            $total = $project['total_mentions'] ?? 0;
                            $sent  = $project['sentiment_summary'] ?? ['positive' => 0, 'neutral' => 0, 'negative' => 0];
                            $pos   = $sent['positive'] ?? 0;
                            $neu   = $sent['neutral']  ?? 0;
                            $neg   = $sent['negative'] ?? 0;
                        @endphp

                        <div class="card lazy-card mb-3"
                             id="proj-card-{{ $id }}"
                             data-project-id="{{ $id }}"
                             data-loaded="false">

                            <div id="dashboardExportArea-{{ $id }}"
                                 style="background:#fff;border-radius:8px;overflow:hidden;">

                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avtar avtar-xs bg-light-success rounded-circle">
                                                <i class="ph ph-pulse f-18 text-success"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $title }}</h6>
                                                <small class="text-muted">#{{ $id }} &middot; {{ $group }}</small>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-1 flex-wrap" data-html2canvas-ignore="true">
                                            <a href="{{ route('mk.data-overview') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                                               class="btn btn-primary btn-sm">
                                                <i class="ph ph-chart-bar me-1"></i>Overview
                                            </a>
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm export-btn"
                                                    id="btn-pdf-{{ $id }}"
                                                    onclick="DashExport.run('{{ $id }}', 'pdf', this)"
                                                    title="Export as PDF">
                                                <i class="ph ph-file-pdf export-icon"></i>
                                                <span class="export-spinner"></span>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-sm export-btn"
                                                    id="btn-img-{{ $id }}"
                                                    onclick="DashExport.run('{{ $id }}', 'image', this)"
                                                    title="Export as PNG">
                                                <i class="ph ph-image export-icon"></i>
                                                <span class="export-spinner"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 mt-3 flex-wrap">
                                        <span class="badge bg-light-primary text-primary">
                                            <i class="ph ph-calendar-blank me-1"></i>
                                            {{ \Carbon\Carbon::parse($startDate)->format('d M') }}
                                            &ndash;
                                            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                                        </span>
                                        <span class="badge bg-light-secondary">
                                            <i class="ph ph-tag me-1"></i>{{ $type }}
                                        </span>
                                        <span class="badge bg-light-success text-success">
                                            <i class="ph ph-globe me-1"></i>{{ $media }}
                                        </span>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row g-2 mb-3">
                                        <div class="col-6 col-lg-3">
                                            <div class="stat-chip clickable p-2 rounded-2 text-center"
                                                 style="background:#ebf0ff;"
                                                 onclick="DashPanel.open('all','all','{{ $id }}')">
                                                <small class="text-muted d-block mb-1 f-10 fw-semibold text-uppercase">Total</small>
                                                <h6 class="mb-0" style="color:#4680ff;">{{ number_format($total) }}</h6>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="stat-chip clickable p-2 rounded-2 text-center"
                                                 style="background:#e6f8f1;"
                                                 onclick="DashPanel.open('all','pos','{{ $id }}')">
                                                <small class="text-muted d-block mb-1 f-10 fw-semibold text-uppercase">Positive</small>
                                                <h6 class="mb-0" style="color:#10B981;">{{ number_format($pos) }}</h6>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="stat-chip clickable p-2 rounded-2 text-center"
                                                 style="background:#fee2e2;"
                                                 onclick="DashPanel.open('all','neg','{{ $id }}')">
                                                <small class="text-muted d-block mb-1 f-10 fw-semibold text-uppercase">Negative</small>
                                                <h6 class="mb-0" style="color:#EF4444;">{{ number_format($neg) }}</h6>
                                            </div>
                                        </div>
                                        <div class="col-6 col-lg-3">
                                            <div class="stat-chip clickable p-2 rounded-2 text-center"
                                                 style="background:var(--slate-50);"
                                                 onclick="DashPanel.open('all','neu','{{ $id }}')">
                                                <small class="text-muted d-block mb-1 f-10 fw-semibold text-uppercase">Neutral</small>
                                                <h6 class="mb-0" style="color:#94A3B8;">{{ number_format($neu) }}</h6>
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted fw-semibold text-uppercase"
                                               style="letter-spacing:.5px;font-size:11px;">
                                            Mention Trend
                                        </small>
                                        <small class="text-muted f-12">
                                            {{ \Carbon\Carbon::parse($startDate)->format('d M') }} &ndash;
                                            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                                        </small>
                                    </div>

                                    <div class="chart-container" id="chart-wrap-{{ $id }}">
                                        <div class="chart-loading" id="chart-loading-{{ $id }}">
                                            <div class="spin-ring"></div>
                                            <span>Loading chart...</span>
                                        </div>
                                        <div id="chart-scroll-{{ $id }}" style="overflow-x:auto;overflow-y:hidden;width:100%;-webkit-overflow-scrolling:touch;">
                                            <div id="chart-{{ $id }}" style="width:100%;height:280px;display:none;cursor:pointer;"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="lazy-sentinel"
                             data-target="proj-card-{{ $id }}"
                             id="sentinel-{{ $id }}"></div>

                    @empty
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="ph ph-folder-open d-block mb-3"
                                   style="font-size:48px;color:var(--slate-300);"></i>
                                <h5 class="text-muted">No Projects Yet</h5>
                                <p class="text-muted mb-0 f-12">
                                    Contact your administrator to get project access.
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>
                {{-- /actualCards --}}

            </div>
        </div>
    </div>
    {{-- /pageExportArea --}}
    </div>

    {{-- Scroll-to-top --}}
    <button class="scroll-top-btn" id="scrollTopBtn"
            onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <i class="ph ph-arrow-up"></i>
    </button>

    {{-- Export Toast --}}
    <div class="export-toast" id="exportToast">
        <i class="ph ph-check-circle" id="exportToastIcon"></i>
        <span id="exportToastMsg">Exporting…</span>
    </div>

    {{-- Slide Panel --}}
    <div class="do-panel-overlay" id="dashPanelOverlay" onclick="DashPanel.closeByOverlay()"></div>
    <div class="do-panel" id="dashSntPanel">
        <div class="do-panel-header">
            <div class="do-panel-dot" id="dashPanelDot"></div>
            <span class="do-panel-title" id="dashPanelTitle">Mentions</span>
            <button class="do-panel-close" onclick="DashPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-panel-actions">
            <div class="do-panel-meta">
                <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
                <span id="dashPanelMeta">&mdash;</span>
            </div>
            <div class="do-panel-tabs">
                <button class="do-panel-tab active" data-s="all" onclick="DashPanel.filterSent('all')">Semua</button>
                 <button class="do-panel-tab pos"    data-s="pos" onclick="DashPanel.filterSent('pos')">Pos</button>
                 <button class="do-panel-tab neg"    data-s="neg" onclick="DashPanel.filterSent('neg')">Neg</button>
                 <button class="do-panel-tab neu"    data-s="neu" onclick="DashPanel.filterSent('neu')">Neu</button>
               
              
            </div>
        </div>
        <div class="do-panel-list" id="dashPanelList"></div>

        <div class="do-detail-panel" id="dashDetailPanel">
            <div class="do-dp2-header">
                <button class="do-dp2-back" onclick="DashDetail.close()">
                    <i class="ph ph-caret-left"></i>
                </button>
                <span class="do-dp2-title" id="dashDetailTitle">Detail</span>
                <button class="do-panel-close" onclick="DashPanel.close()">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            <div class="do-dp2-body" id="dashDetailBody"></div>
        </div>
    </div>

    {{-- Platform Picker --}}
    <div class="do-plat-picker" id="dashPlatPicker">
        <div class="do-plat-picker-head">Pilih Platform</div>
        <button class="do-plat-btn" onclick="DashPanel.openPlatform('twit','all')">
            X / Twitter <span class="do-plat-dot" style="background:#1d9bf0;"></span>
        </button>
        <button class="do-plat-btn" onclick="DashPanel.openPlatform('fb','all')">
            Facebook <span class="do-plat-dot" style="background:#1877f2;"></span>
        </button>
        <button class="do-plat-btn" onclick="DashPanel.openPlatform('instagram','all')">
            Instagram <span class="do-plat-dot" style="background:#e1306c;"></span>
        </button>
        <button class="do-plat-btn" onclick="DashPanel.openPlatform('youtube','all')">
            YouTube <span class="do-plat-dot" style="background:#ff0000;"></span>
        </button>
        <button class="do-plat-btn" onclick="DashPanel.openPlatform('tiktok','all')">
            TikTok <span class="do-plat-dot" style="background:#111827;"></span>
        </button>
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>

    <script>
    /* ════════════════════════════════════════════════════════
       GLOBALS / CONFIG
    ════════════════════════════════════════════════════════ */
    const DashCfg = {
        sd: START_DATE,
        ed: END_DATE,
        platMeta: {
            doc:       { label: 'Online News',   color: '#0284c7' },
            twit:      { label: 'X / Twitter',   color: '#1d9bf0' },
            fb:        { label: 'Facebook',       color: '#1877f2' },
            instagram: { label: 'Instagram',      color: '#e1306c' },
            youtube:   { label: 'YouTube',        color: '#ff0000' },
            tiktok:    { label: 'TikTok',         color: '#111827' },
            all:       { label: 'All Media',      color: '#4361EE' },
            social:    { label: 'Social Media',   color: '#4361EE' },
        },
    };

    const _$ = id => document.getElementById(id);
    const _es = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const numK = n => { n = parseInt(n||0); return n.toLocaleString('id-ID'); };
    function getPrimary() {
        return getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#4361EE';
    }

    /* ════════════════════════════════════════════════════════
       DOM READY
    ════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', function () {

        // ── AUTO-SYNC WITH GLOBAL DATEPICKER ──
        const params = new URLSearchParams(window.location.search);
        const lsStart = localStorage.getItem('smadiment_g_start');
        const lsEnd   = localStorage.getItem('smadiment_g_end');
        if (!params.get('start_date') && lsStart && lsEnd) {
            params.set('start_date', lsStart);
            params.set('end_date', lsEnd);
            window.location.search = params.toString();
            return;
        }

        // ── Date label ──
        const el = _$('mkDateLabel');
        if (el) el.textContent = new Date().toLocaleDateString('en-US', {
            weekday:'short', day:'numeric', month:'short', year:'numeric'
        });

        // ── KPI Animated Fill ──
        const fmt = n => parseInt(n || 0).toLocaleString('id-ID');
        const kpiEls = [_$('kpiMentions'), _$('kpiPositive'), _$('kpiNeutral'), _$('kpiNegative')];
        kpiEls.forEach(el => { if (el) el.style.opacity = '0'; });

        setTimeout(() => {
            const kpiMentions = _$('kpiMentions');
            const kpiPositive = _$('kpiPositive');
            const kpiNeutral  = _$('kpiNeutral');
            const kpiNegative = _$('kpiNegative');
            const kpiPosSub   = _$('kpiPositiveSub');
            const kpiNeuSub   = _$('kpiNeutralSub');
            const kpiNegSub   = _$('kpiNegativeSub');

            if (kpiMentions) { kpiMentions.textContent = fmt(KPI_DATA.mentions); kpiMentions.style.opacity = '1'; }
            if (kpiPositive) { kpiPositive.textContent = fmt(KPI_DATA.positive); kpiPositive.style.opacity = '1'; }
            if (kpiNeutral)  { kpiNeutral.textContent  = fmt(KPI_DATA.neutral);  kpiNeutral.style.opacity  = '1'; }
            if (kpiNegative) { kpiNegative.textContent = fmt(KPI_DATA.negative); kpiNegative.style.opacity = '1'; }

            if (kpiPosSub) kpiPosSub.innerHTML =
                `<i class="ph ph-trend-up me-1"></i>${KPI_DATA.hasData ? KPI_DATA.posPct : 'No data'}`;
            if (kpiNeuSub) kpiNeuSub.innerHTML =
                `<i class="ph ph-minus me-1"></i>${KPI_DATA.hasData ? KPI_DATA.neuPct : 'No data'}`;
            if (kpiNegSub) kpiNegSub.innerHTML =
                `<i class="ph ph-trend-down me-1"></i>${KPI_DATA.hasData ? KPI_DATA.negPct : 'No data'}`;
        }, 400);

        // ── Scroll-to-top ──
        const scrollBtn = _$('scrollTopBtn');
        window.addEventListener('scroll', () =>
            scrollBtn.classList.toggle('visible', window.scrollY > 300),
        { passive: true });

        // ── Sidebar search ──
        const searchInput = _$('sidebarSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();
                document.querySelectorAll('.proj-item').forEach(item => {
                    item.style.display = (!q || item.dataset.name.includes(q)) ? '' : 'none';
                });
            });
        }

        // ── Sidebar click → scroll + highlight card ──
        document.querySelectorAll('.proj-item').forEach(function (item) {
            item.addEventListener('click', function () {
                const card = _$('proj-card-' + item.dataset.id);
                document.querySelectorAll('.proj-item').forEach(el => el.classList.remove('active-sidebar'));
                item.classList.add('active-sidebar');
                if (!card) return;
                card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                card.classList.add('highlighted');
                setTimeout(() => card.classList.remove('highlighted'), 2000);
            });
        });

        // ── Swap skeleton → cards ──
        const skel  = _$('skeletonWrap');
        const cards = _$('actualCards');
        if (skel)  skel.style.display  = 'none';
        if (cards) cards.style.display = 'block';

        // ── Init lazy charts ──
        initLazyCharts();
    });

    /* ════════════════════════════════════════════════════════
       LAZY CHART INIT
    ════════════════════════════════════════════════════════ */
    function initLazyCharts() {
        if (typeof IntersectionObserver === 'undefined') {
            document.querySelectorAll('.lazy-card').forEach(function (c, i) {
                c.classList.add('card-visible');
                setTimeout(() => fetchAndRenderChart(c.dataset.projectId), i * 400);
            });
            return;
        }

        const obs = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                const sentinel = entry.target;
                const cardEl = document.getElementById(sentinel.dataset.target);
                if (!cardEl || cardEl.dataset.loaded === 'true') { obs.unobserve(sentinel); return; }
                cardEl.dataset.loaded = 'true';
                cardEl.classList.add('card-visible');
                obs.unobserve(sentinel);
                setTimeout(() => fetchAndRenderChart(cardEl.dataset.projectId), 120);
            });
        }, { rootMargin: '150px 0px', threshold: 0.01 });

        document.querySelectorAll('.lazy-sentinel').forEach(s => obs.observe(s));
    }

    /* ════════════════════════════════════════════════════════
       FETCH CHART DATA
    ════════════════════════════════════════════════════════ */
    function fetchAndRenderChart(projectId) {
        if (PROJECT_TIMELINES[String(projectId)]) {
            renderProjectChart(projectId);
            return;
        }

        const loadEl = _$('chart-loading-' + projectId);
        if (loadEl) { const sp = loadEl.querySelector('span'); if (sp) sp.textContent = 'Fetching data...'; }

        const url = CHART_DATA_URL
            + '?project_id='  + encodeURIComponent(projectId)
            + '&start_date='  + encodeURIComponent(START_DATE)
            + '&end_date='    + encodeURIComponent(END_DATE);

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            credentials: 'same-origin',
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(json => {
            PROJECT_TIMELINES[String(projectId)] = json.timeline || {};
            renderProjectChart(projectId);
        })
        .catch(err => {
            console.error('[Chart] Fetch failed for project', projectId, err);
            const wrapEl = _$('chart-wrap-' + projectId);
            if (loadEl) loadEl.remove();
            if (wrapEl) wrapEl.innerHTML =
                '<div class="chart-empty"><i class="ph ph-wifi-slash"></i><span>Failed to load chart data</span></div>';
        });
    }

    /* ════════════════════════════════════════════════════════
       RENDER CHART
    ════════════════════════════════════════════════════════ */
    function renderProjectChart(projectId) {
        const chartEl  = _$('chart-' + projectId);
        const wrapEl   = _$('chart-wrap-' + projectId);
        const loadEl   = _$('chart-loading-' + projectId);
        const tl       = PROJECT_TIMELINES[String(projectId)] || null;

        if (!chartEl || !wrapEl || typeof ApexCharts === 'undefined') return;

        if (!tl || !tl.dates || tl.dates.length === 0) {
            if (loadEl) loadEl.remove();
            wrapEl.innerHTML = '<div class="chart-empty"><i class="ph ph-chart-line"></i><span>No data for selected range</span></div>';
            return;
        }

        chartEl.style.display = 'block';

        const totalPoints      = tl.dates.length;
        const SCROLL_THRESHOLD = 31;

        const labels = tl.dates.map(dt => {
            try {
                const datePart = String(dt || '').split(/[T\s]/)[0];
                const parts    = datePart.split('-');
                if (parts.length !== 3) return dt;
                const year  = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10);
                const day   = parseInt(parts[2], 10);
                if (isNaN(year) || isNaN(month) || isNaN(day)) return dt;
                const d = new Date(year, month - 1, day);
                return totalPoints > SCROLL_THRESHOLD
                    ? d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
                    : `${d.getDate()}/${d.getMonth() + 1}`;
            } catch (e) { return dt; }
        });

        const containerW  = chartEl.parentElement?.offsetWidth || 600;
        const chartWidth  = totalPoints > SCROLL_THRESHOLD
            ? Math.max(totalPoints * 42, containerW)
            : containerW;

        const scrollWrap = _$('chart-scroll-' + projectId);
        if (scrollWrap) {
            if (totalPoints > SCROLL_THRESHOLD) {
                scrollWrap.style.overflowX = 'auto';
                if (!scrollWrap.dataset.hinted) {
                    scrollWrap.dataset.hinted = '1';
                    const hint = document.createElement('div');
                    hint.style.cssText = 'text-align:right;font-size:10px;color:#94A3B8;font-weight:600;padding:2px 4px 4px;';
                    hint.innerHTML = '<i class="ph ph-arrows-left-right" style="font-size:11px;vertical-align:middle;"></i> Geser untuk lihat semua';
                    scrollWrap.parentElement?.insertBefore(hint, scrollWrap);
                }
            } else {
                scrollWrap.style.overflowX = 'hidden';
            }
        }

        chartEl.style.width    = chartWidth + 'px';
        chartEl.style.minWidth = chartWidth + 'px';

        const options = {
            chart: {
                type:       'area',
                height:     280,
                width:      chartWidth,
                fontFamily: 'inherit',
                background: 'transparent',
                toolbar:    { show: false },
                animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1000 } },
                events: {
                    click: (_e, _ctx, cfg) => {
                        let sdStr = null, edStr = null;
                        if (cfg && typeof cfg.dataPointIndex !== 'undefined' && cfg.dataPointIndex >= 0) {
                            if (tl.dates_start && tl.dates_start[cfg.dataPointIndex]) sdStr = tl.dates_start[cfg.dataPointIndex];
                            if (tl.dates_end   && tl.dates_end[cfg.dataPointIndex])   edStr = tl.dates_end[cfg.dataPointIndex];
                        }
                        if (cfg && cfg.seriesIndex >= 0) {
                            const mapping = {0:'all', 1:'pos', 2:'neu', 3:'neg'};
                            DashPanel.open('all', mapping[cfg.seriesIndex] || 'all', projectId, sdStr, edStr);
                        } else {
                            DashPanel.open('all', 'all', projectId, sdStr, edStr);
                        }
                    },
                    mounted: function () {
                        if (loadEl) {
                            loadEl.classList.add('hidden');
                            setTimeout(() => { try { loadEl.remove(); } catch (e) {} }, 260);
                        }
                    },
                },
            },
            series: [
                { name: 'Total',    data: tl.values || [] },
                { name: 'Positive', data: tl.sentiment?.positive || [] },
                { name: 'Negative', data: tl.sentiment?.negative || [] },
                { name: 'Neutral',  data: tl.sentiment?.neutral  || [] },
            ],
            colors: ['#4680ff', '#10B981', '#EF4444', '#94A3B8'],
          markers: {
    size: totalPoints <= 31 ? 6 : 3,   // ← dari 14 ke 31, dan fallback 3 bukan 0
    strokeWidth: 2,
    strokeColors: '#fff',
    hover: { size: 8 }
},
dataLabels: {
    enabled: totalPoints <= 31,         // ← dari 14 ke 31
    formatter: v => v > 0 ? numK(v) : '',
    offsetY: -8,
    style: { fontSize: '9px', fontFamily: 'inherit', fontWeight: '800' },
    background: {
        enabled: true, foreColor: '#fff', padding: 3,
        borderRadius: 3, borderWidth: 0, opacity: 0.9,
        dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: 0.15 }  // ← tambah ini
    }
},
            xaxis: {
                categories: labels,
                type:       'category',
                axisBorder: { show: false },
                axisTicks:  { show: false },
                tickPlacement: 'on',
                labels: {
                    rotate:                -45,
                    rotateAlways:          totalPoints > 12,
                    hideOverlappingLabels: false,
                    showDuplicates:        false,
                    trim:                  false,
                    style: { fontFamily: 'inherit', fontSize: '10px', fontWeight: 600, colors: '#94A3B8' }
                }
            },
            yaxis: {
                labels: {
                    formatter: v => numK(v),
                    style: { fontFamily:'inherit', fontSize:'10px', fontWeight:600, colors:'#94A3B8' }
                },
                axisBorder: { show: false },
                axisTicks:  { show: false }
            },
            fill:    { opacity: 0.3 },
            stroke:  { curve: 'smooth', width: 2.5 },
            grid: {
                borderColor:     'rgba(226,232,240,.55)',
                strokeDashArray: 3,
                xaxis: { lines: { show: false } }
            },
            legend: {
                position:        'bottom',
                horizontalAlign: 'left',
                fontFamily:      'inherit',
                fontSize:        '11px',
                fontWeight:      '600',
                labels:          { colors: '#94A3B8' },
                markers:         { width:9, height:9, radius:50 },
                itemMargin:      { horizontal:14, vertical:4 }
            },
            tooltip: {
                shared:    false,
                intersect: true,
                style:     { fontFamily:'inherit', fontSize:'12px' },
                y:         { formatter: v => v ? v.toLocaleString('id-ID') + ' mentions' : '0 mentions' }
            },
        };

        const prevKey = '__apexInst_' + projectId;
        if (window[prevKey]) { try { window[prevKey].destroy(); } catch (e) {} }

        chartEl.innerHTML = '';
        const inst = new ApexCharts(chartEl, options);
        window[prevKey] = inst;
        inst.render();

        if (totalPoints <= SCROLL_THRESHOLD) {
            window.addEventListener('resize', () => {
                const i = window['__apexInst_' + projectId];
                if (i) {
                    const w = chartEl.parentElement?.offsetWidth || 600;
                    try { i.updateOptions({ chart: { width: w } }, false, false); } catch(e) {}
                }
            });
        }

        chartEl.addEventListener('click', e => {
            const t    = e.target;
            const skip = t.classList.contains('apexcharts-marker')
                      || t.closest('.apexcharts-datalabels')
                      || t.closest('.apexcharts-legend');
            if (!skip) DashPanel.open('all', 'all', projectId);
        });
    }

    /* ════════════════════════════════════════════════════════
       DASH EXPORT MODULE
    ════════════════════════════════════════════════════════ */
    const DashExport = (() => {
        let _toastTimer = null;

        function _toast(msg, type = 'default', duration = 3200) {
            const t = _$('exportToast'), m = _$('exportToastMsg'), ico = _$('exportToastIcon');
            if (!t || !m) return;
            m.textContent = msg;
            t.className   = 'export-toast show ' + (type !== 'default' ? type : '');
            const icons = { success: 'ph-check-circle', error: 'ph-x-circle', default: 'ph-spinner' };
            ico.className = 'ph ' + (icons[type] || icons.default);
            clearTimeout(_toastTimer);
            _toastTimer = setTimeout(() => t.classList.remove('show'), duration);
        }

        function _btnState(btn, loading) {
            if (!btn) return;
            btn.disabled = loading;
            btn.classList.toggle('exporting', loading);
        }

        let _freezeStyle = null;
        function _freeze() {
            if (_freezeStyle) return;
            _freezeStyle = document.createElement('style');
            _freezeStyle.id = '__dash_freeze__';
            _freezeStyle.textContent = `
                #pageExportArea .lazy-card,
                #pageExportArea .fade-up,
                #pageExportArea [class*="fade-up"] {
                    animation: none !important; opacity: 1 !important; transform: none !important;
                }
                #pageExportArea .sk-block { animation: none !important; background: #e2e8f0 !important; }
                #pageExportArea .proj-status-dot { animation: none !important; box-shadow: 0 0 0 3px rgba(67,97,238,.15) !important; }
                #pageExportArea .spin-ring { animation: none !important; }
                #pageExportArea [data-html2canvas-ignore],
                #pageExportArea .page-export-bar,
                #pageExportArea .scroll-top-btn,
                #pageExportArea .chart-loading.hidden { display: none !important; }
            `;
            document.head.appendChild(_freezeStyle);
        }
        function _unfreeze() {
            if (_freezeStyle) { _freezeStyle.remove(); _freezeStyle = null; }
        }

        function _onClone(clonedDoc) {
            clonedDoc.querySelectorAll(
                '#dashPanelOverlay, #dashSntPanel, .do-panel-overlay, .do-panel,' +
                '#dashPlatPicker, .do-plat-picker,' +
                '.export-toast, .scroll-top-btn,' +
                '[data-html2canvas-ignore], .page-export-bar'
            ).forEach(el => {
                el.style.cssText += 'display:none!important;visibility:hidden!important;';
            });
            clonedDoc.querySelectorAll('*').forEach(el => {
                el.style.animationPlayState = 'paused';
                el.style.animation  = 'none';
                el.style.transition = 'none';
            });
            clonedDoc.querySelectorAll(
                '.lazy-card, .card, .card-body, .card-header,' +
                '.row, [class*="col-"], #pageExportArea, #actualCards'
            ).forEach(el => {
                el.style.opacity    = '1';
                el.style.transform  = 'none';
                el.style.visibility = 'visible';
            });
            clonedDoc.querySelectorAll('[id^="chart-"]').forEach(el => {
                el.style.display    = 'block';
                el.style.opacity    = '1';
                el.style.visibility = 'visible';
            });
            clonedDoc.querySelectorAll('.chart-loading').forEach(el => { el.style.display = 'none'; });
            clonedDoc.querySelectorAll('#skeletonWrap').forEach(el => { el.style.display = 'none'; });
            const ac = clonedDoc.getElementById('actualCards');
            if (ac) ac.style.display = 'block';
        }

        async function _captureEl(el, bg) {
            return html2canvas(el, {
                scale: 2, useCORS: true, allowTaint: false,
                backgroundColor: bg || '#ffffff', logging: false, removeContainer: true,
                onclone: d => _onClone(d),
                ignoreElements: e => e.hasAttribute('data-html2canvas-ignore'),
                x: 0, y: 0, width: el.offsetWidth, height: el.offsetHeight || el.scrollHeight,
            });
        }

        async function _captureArea(el, bg) {
            return html2canvas(el, {
                scale: 2, useCORS: true, allowTaint: false,
                backgroundColor: bg || '#f1f5f9', logging: false, removeContainer: true,
                onclone: d => _onClone(d),
                ignoreElements: e =>
                    e.hasAttribute('data-html2canvas-ignore') ||
                    e.id === 'pageExportPdfBtn' || e.id === 'pageExportImgBtn',
                windowWidth: document.documentElement.scrollWidth,
                windowHeight: el.scrollHeight, height: el.scrollHeight,
            });
        }

        function _filename(projectId) {
            const card  = _$('proj-card-' + projectId);
            const title = card?.querySelector('h6')?.textContent?.trim() || ('project-' + projectId);
            const safe  = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 48);
            const d     = new Date();
            const stamp = `${d.getFullYear()}${String(d.getMonth()+1).padStart(2,'0')}${String(d.getDate()).padStart(2,'0')}`;
            return `${safe}_${stamp}`;
        }

        function _buildPdf(canvas, headerColor, headerText, landscape) {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit: 'mm', format: 'a4' });
            const pW  = pdf.internal.pageSize.getWidth();
            const pH  = pdf.internal.pageSize.getHeight();
            const margin  = 12;
            const usableW = pW - margin * 2;
            const usableH = pH - 14 - 6;
            const ratio   = usableW / canvas.width;
            const sliceH  = usableH / ratio;
            const total   = Math.max(1, Math.ceil((canvas.height * ratio) / usableH));

            const _hdr = (page) => {
                const [r, g, b] = headerColor;
                pdf.setFillColor(r, g, b);
                pdf.rect(0, 0, pW, 11, 'F');
                pdf.setTextColor(255, 255, 255);
                pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
                pdf.text(headerText, margin, 7.5);
                const now = new Date().toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });
                pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
                pdf.text('Generated: ' + now, pW - margin, 7.5, { align: 'right' });
                pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
                pdf.text(`Halaman ${page} / ${total}`, pW / 2, pH - 3, { align: 'center' });
            };

            let srcY = 0, page = 1;
            while (srcY < canvas.height) {
                if (page > 1) pdf.addPage();
                _hdr(page);
                const srcSlice = Math.min(sliceH, canvas.height - srcY);
                const dstH     = srcSlice * ratio;
                const slice    = document.createElement('canvas');
                slice.width  = canvas.width;
                slice.height = Math.ceil(srcSlice);
                slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcSlice, 0, 0, canvas.width, srcSlice);
                pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, usableW, dstH);
                srcY += srcSlice; page++;
            }
            return pdf;
        }

        async function run(projectId, type, btn) {
            if (!window.html2canvas)                    { _toast('html2canvas not loaded', 'error'); return; }
            if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF not loaded', 'error'); return; }

            _btnState(btn, true);
            _toast(type === 'pdf' ? 'Generating PDF…' : 'Capturing image…', 'default', 99999);

            try {
                const area = _$('dashboardExportArea-' + projectId);
                if (!area) throw new Error('Export area not found for project ' + projectId);

                _freeze();
                await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));
                await new Promise(r => setTimeout(r, 300));

                let canvas;
                try   { canvas = await _captureEl(area, '#ffffff'); }
                finally { _unfreeze(); }

                const fname = _filename(projectId);

                if (type === 'image') {
                    const link    = document.createElement('a');
                    link.download = fname + '.png';
                    link.href     = canvas.toDataURL('image/png');
                    link.click();
                    _toast('Image downloaded!', 'success');
                } else {
                    const landscape = canvas.width > canvas.height * 1.3;
                    const pdf = _buildPdf(canvas, [67, 97, 238], 'SMADIMENT — Dashboard Export', landscape);
                    pdf.save(fname + '.pdf');
                    _toast('PDF downloaded!', 'success');
                }
            } catch (err) {
                console.error('[DashExport]', err);
                _unfreeze();
                _toast('Export failed: ' + err.message, 'error');
            } finally {
                _btnState(btn, false);
            }
        }

        async function runPage(type, btn) {
            if (!window.html2canvas)                    { _toast('html2canvas not loaded', 'error'); return; }
            if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF not loaded', 'error'); return; }

            const btnPdf = _$('pageExportPdfBtn'), btnImg = _$('pageExportImgBtn');
            _btnState(btnPdf, true); _btnState(btnImg, true);
            _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar halaman…', 'default', 99999);

            try {
                const area = _$('pageExportArea');
                if (!area) throw new Error('pageExportArea not found');

                window.scrollTo({ top: 0 });
                _freeze();
                await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));
                await new Promise(r => setTimeout(r, 400));

                let canvas;
                try   { canvas = await _captureArea(area, '#f1f5f9'); }
                finally { _unfreeze(); }

                const stamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');

                if (type === 'image') {
                    const link    = document.createElement('a');
                    link.download = `smadiment_dashboard_${stamp}.png`;
                    link.href     = canvas.toDataURL('image/png');
                    link.click();
                    _toast('Gambar halaman berhasil diunduh!', 'success');
                } else {
                    const pdf = _buildPdf(canvas, [67, 97, 238], 'SMADIMENT — Dashboard Full Export', false);
                    pdf.save(`smadiment_dashboard_${stamp}.pdf`);
                    _toast('PDF halaman berhasil diunduh!', 'success');
                }
            } catch (err) {
                console.error('[DashExport.runPage]', err);
                _unfreeze();
                _toast('Export gagal: ' + err.message, 'error');
            } finally {
                _btnState(btnPdf, false); _btnState(btnImg, false);
            }
        }

        return { run, runPage };
    })();

    /* ════════════════════════════════════════════════════════
       DASH PANEL
    ════════════════════════════════════════════════════════ */
    const DashPanel = (() => {
        let _cache = {}, _allItems = [], _filtered = [];
        let _curPlat = 'all', _curSent = 'all', _curPid = null, _curPlatForSent = 'all';
        let _overrideSd = null, _overrideEd = null;

        const SENT_MAP = {
            '1': 'pos', 'positive': 'pos', 'positif': 'pos',
            '-1': 'neg', '2': 'neg', 'negative': 'neg', 'negatif': 'neg',
        };
        const _normSent = item =>
            SENT_MAP[String(item.class_sentiment || item.sentiment || '0').toLowerCase().trim()] || 'neu';

        function showPlatPicker(x, y, sent, pid) {
            _curPlatForSent = sent || 'all';
            if (pid) _curPid = pid;
            const pp = _$('dashPlatPicker');
            if (!pp) return;
            pp.querySelectorAll('.do-plat-btn').forEach(btn => {
                const m  = btn.getAttribute('onclick') || '';
                const pm = m.match(/openPlatform\('([^']+)'/);
                if (pm) btn.setAttribute('onclick', `DashPanel.openPlatform('${pm[1]}','${_curPlatForSent}')`);
            });
            const pw=180, ph=250, vw=window.innerWidth, vh=window.innerHeight;
            let left=x+10, top=y-10;
            if (left+pw>vw-8) left=x-pw-10;
            if (top+ph>vh-8)  top=vh-ph-8;
            if (top<8) top=8;
            pp.style.left=left+'px'; pp.style.top=top+'px'; pp.classList.add('show');
        }

        function openPlatform(platform, sentiment) {
            _$('dashPlatPicker')?.classList.remove('show');
            open(platform, sentiment || _curPlatForSent || 'all', _curPid, _overrideSd, _overrideEd);
        }

        async function open(platform, sentiment, projectId, sdOverride = null, edOverride = null) {
            _curPlat = platform; _curSent = sentiment || 'all';
            if (projectId) _curPid = projectId;
            _overrideSd = sdOverride; _overrideEd = edOverride;

            const meta = DashCfg.platMeta[platform] || { label: platform, color: '#4361EE' };
            DashDetail.close();
            _$('dashPanelDot').style.background  = meta.color;
            _$('dashPanelTitle').textContent      = meta.label + (platform==='all' ? ' — All Platforms' : '');

            const titleDate = sdOverride
                ? (sdOverride === edOverride ? sdOverride : sdOverride + ' – ' + edOverride)
                : (DashCfg.sd + ' – ' + DashCfg.ed);
            _$('dashPanelMeta').textContent = titleDate;

            document.querySelectorAll('#dashSntPanel .do-panel-tab').forEach(t =>
                t.classList.toggle('active', t.dataset.s === _curSent)
            );
            const list = _$('dashPanelList');
            list.innerHTML = `<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;
            const overlay = _$('dashPanelOverlay'), panel = _$('dashSntPanel');
            overlay.classList.remove('hiding'); panel.classList.remove('hiding');
            overlay.classList.add('show'); panel.classList.add('show');
            try {
                const sdStr = _overrideSd || DashCfg.sd;
                const edStr = _overrideEd || DashCfg.ed;
                const key = `${_curPid}_${platform}_${sdStr}_${edStr}`;
                if (!_cache[key]) _cache[key] = await _fetchAll(platform, _curPid, sdStr, edStr);
                _allItems = _cache[key];
                _filtered = _filterBySent(_allItems, _curSent);
                _render(list, _filtered, platform, meta.color);
            } catch (err) {
                list.innerHTML = `<div style="padding:50px 20px;text-align:center;color:#94A3B8;font-size:13px;">Gagal memuat data<br><small>${_es(err.message)}</small></div>`;
            }
        }

        function close() {
            const overlay = _$('dashPanelOverlay'), panel = _$('dashSntPanel');
            panel.classList.add('hiding'); overlay.classList.add('hiding');
            setTimeout(() => {
                panel.classList.remove('show','hiding');
                overlay.classList.remove('show','hiding');
                DashDetail.close();
            }, 240);
        }
        function closeByOverlay() { close(); }

        function filterSent(sent) {
            _curSent = sent;
            document.querySelectorAll('#dashSntPanel .do-panel-tab').forEach(t =>
                t.classList.toggle('active', t.dataset.s === sent)
            );
            _filtered = _filterBySent(_allItems, sent);
            const meta = DashCfg.platMeta[_curPlat] || { color: '#4361EE' };
            _render(_$('dashPanelList'), _filtered, _curPlat, meta.color);
        }

        function _filterBySent(items, sent) {
            return sent === 'all' ? items : items.filter(i => _normSent(i) === sent);
        }

        async function _fetchAll(platform, pid, sd, ed) {
            if (pid === 'ALL_PROJECTS') {
                const pids = KPI_DATA.allPids || [];
                const res = await Promise.allSettled(pids.map(id => _fetchAll(platform, id, sd, ed)));
                const items = res.flatMap(r => r.status==='fulfilled' ? r.value : []);
                items.sort((a,b)=>new Date(b.date_created||b.created_at||0)-new Date(a.date_created||a.created_at||0));
                return items;
            }

            if (platform === 'all') {
                const all = ['doc','twit','fb','instagram','youtube','tiktok'];
                const res = await Promise.allSettled(all.map(p => _fetchOne(p, pid, sd, ed)));
                const items = res.flatMap(r => r.status==='fulfilled' ? r.value : []);
                items.sort((a,b)=>new Date(b.date_created||b.created_at||0)-new Date(a.date_created||a.created_at||0));
                return items;
            }
            if (platform === 'social') {
                const s = ['twit','fb','instagram','youtube','tiktok'];
                const res = await Promise.allSettled(s.map(p => _fetchOne(p, pid, sd, ed)));
                const items = res.flatMap(r => r.status==='fulfilled' ? r.value : []);
                items.sort((a,b)=>new Date(b.date_created||b.created_at||0)-new Date(a.date_created||a.created_at||0));
                return items;
            }
            return _fetchOne(platform, pid, sd, ed);
        }

        async function _fetchOne(platform, pid, sd, ed) {
            const q = `project_id=${pid}&start_date=${sd}&end_date=${ed}&rows=500&start=0`;
            if (platform === 'instagram') {
                for (const sub of ['postbylike','postbydate']) {
                    const ic = new AbortController(), it = setTimeout(() => ic.abort(), 12000);
                    try {
                        const r = await fetch(`/mk/api/news/ig-top-status?${q}${sub ? '&sub='+sub : ''}`, { signal: ic.signal });
                        clearTimeout(it);
                        const d = await r.json();
                        const items = Array.isArray(d.data) ? d.data : (Array.isArray(d) ? d : []);
                        if (items.length > 0) return items.map(i => ({ ...i, _platform: platform }));
                    } catch (e) { clearTimeout(it); continue; }
                }
                return [];
            }

            /* ── Online News: use articles API (has proper URLs) ── */
            if (platform === 'doc') {
                const docQ = `project_id=${pid}&start_date=${sd}&end_date=${ed}&rows=50&start=0&media=doc`;
                const artUrl = `/mk/api/news/articles?${docQ}`;
                const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 25000);
                try {
                    const r = await fetch(artUrl, { signal: ctrl.signal }); clearTimeout(tid);
                    if (!r.ok) return [];
                    const d = await r.json();
                    let items = Array.isArray(d?.data) ? d.data : (Array.isArray(d) ? d : []);
                    return items.map(i => ({
                        ...i,
                        _platform: 'doc',
                        /* Normalise fields so panel renderer picks them up correctly */
                        content:         i.content  || i.summary || '',
                        title:           i.title    || 'Untitled',
                        publisher:       i.publisher || i.name || '',
                        source_name:     i.publisher || i.name || '',
                        date_created:    i.date_created || '',
                        url:             i.url || '',
                        class_sentiment: String(i.class_sentiment ?? i.sentiment_class ?? i.sentiment ?? '0'),
                    }));
                } catch (e) { clearTimeout(tid); return []; }
            }

            const eps = {
                twit:    `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
                fb:      `/mk/api/news/fb-top-status?${q}&sub=fblike`,
                youtube: `/mk/api/news/ytb-top-status?${q}`,
                tiktok:  `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
            };
            const twitFallback = `/mk/api/news/mentions?${q}`;
            const url = eps[platform]; if (!url) return [];
            const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 20000);
            try {
                const r = await fetch(url, { signal: ctrl.signal }); clearTimeout(tid);
                if (!r.ok) return [];
                const d = await r.json();
                let items = [];
                if      (Array.isArray(d?.data?.data))  items = d.data.data;
                else if (Array.isArray(d?.data))         items = d.data;
                else if (Array.isArray(d?.statuses))     items = d.statuses;
                else if (Array.isArray(d?.tweets))       items = d.tweets;
                else if (Array.isArray(d?.results))      items = d.results;
                else if (Array.isArray(d?.posts))         items = d.posts;
                else if (Array.isArray(d))               items = d;
                else if (d?.data && typeof d.data==='object' && !Array.isArray(d.data)) {
                    const vals = Object.values(d.data);
                    if (vals.length && typeof vals[0]==='object') items = vals;
                }
                if (platform==='twit' && items.length===0) {
                    /* Fallback 1: try most-retweets endpoint */
                    try {
                        const r1b = await fetch(`/mk/api/x/most-retweets?${q}`);
                        if (r1b.ok) {
                            const d1b = await r1b.json();
                            if (Array.isArray(d1b?.data)) items = d1b.data;
                            else if (Array.isArray(d1b)) items = d1b;
                        }
                    } catch(e1b) {}
                }
                if (platform==='twit' && items.length===0) {
                    /* Fallback 2: try user-mentions endpoint */
                    try {
                        const r1c = await fetch(`/mk/api/x/user-mentions?${q}`);
                        if (r1c.ok) {
                            const d1c = await r1c.json();
                            if (Array.isArray(d1c?.data)) items = d1c.data;
                            else if (Array.isArray(d1c)) items = d1c;
                        }
                    } catch(e1c) {}
                }
                if (platform==='twit' && items.length===0) {
                    /* Fallback 3: try mentions API with Twitter filtering */
                    try {
                        const r2 = await fetch(twitFallback);
                        const d2 = await r2.json();
                        let fb = Array.isArray(d2?.data?.data) ? d2.data.data : Array.isArray(d2?.data) ? d2.data : Array.isArray(d2) ? d2 : [];
                        items = fb.filter(m => {
                            const tc=String(m.tcode||'').toLowerCase(), mt=String(m.media_type||'').toLowerCase();
                            const id=String(m.id||m.docid||'').toLowerCase(), url2=String(m.url||'').toLowerCase();
                            return tc==='twit'||tc==='rt'||mt==='twit'||mt==='twitter'||mt==='x'
                                ||id.startsWith('tw-')||url2.includes('twitter.com')||url2.includes('x.com');
                        });
                    } catch (e2) {}
                }
                return items.map(i => ({ ...i, _platform: platform }));
            } catch (e) { clearTimeout(tid); return []; }
        }

        function _render(list, items, platform, accentColor) {
            if (!items.length) {
                list.innerHTML = `<div style="padding:50px 20px;text-align:center;color:#94A3B8;font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>`;
                return;
            }

            const PAGE = 10;
            let _page = 0;

            function _renderItems(arr) {
                return arr.map(item => {
                    const plat = item._platform || platform;
                    const meta = DashCfg.platMeta[plat] || { label: plat, color: accentColor };
                    const ao0  = (() => { if (typeof item.author==='object'&&item.author) return item.author; try { return JSON.parse(item.author||'{}'); } catch(e){ return {}; }})();
                    const rawName = (() => {
                        if (plat==='fb')        return item.from_name||item.page_name||item.author_name||ao0?.name||item.author_handle||null;
                        if (plat==='instagram') return item.username||item.user_name||null;
                        if (plat==='tiktok')    return item.author_nickname||item.nickname||ao0?.nickname||null;
                        if (plat==='youtube')   return item.channel_title||item.channel_name||item.snippet?.channelTitle||null;
                        if (plat==='twit')      return item.name||ao0?.name||ao0?.scr_name||item.author_name||item.author_scr_name||null;
                        return null;
                    })();
                    let name  = (rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'').trim();
                    /* If name is numeric ID, try to use handle/screen_name instead */
                    if (!name || /^\d{5,}$/.test(name) || name.toLowerCase()==='unknown') {
                        const altName = (item.author_handle||item.author_scr_name||item.screen_name||ao0?.scr_name||ao0?.username||item.username||item.nickname||ao0?.name||'').trim();
                        if (altName && !/^\d{5,}$/.test(altName)) {
                            name = altName;
                        } else {
                            const lbls = { fb:'Facebook User', twit:'Twitter User', youtube:'YouTube User', instagram:'Instagram User', tiktok:'TikTok User' };
                            name = lbls[plat] || 'Unknown User';
                        }
                    }
                    const dName = name;
                    const rawH = (() => {
                        if (plat==='instagram') return item.username||'';
                        if (plat==='twit') {
                            return item.screen_name||item.author_scr_name||ao0?.scr_name||ao0?.username||'';
                        }
                        return item.author_scr_name||item.screen_name||item.username||'';
                    })().trim();
                    const handle = (() => {
                        if (!rawH) return '';
                        const w = ['twit','instagram','tiktok'].includes(plat) ? (rawH.startsWith('@') ? rawH : '@'+rawH) : rawH;
                        return w.replace(/^@/,'').toLowerCase()===dName.toLowerCase() ? '' : w;
                    })();
                    const text  = (() => {
                        if (plat === 'doc') {
                            /* For articles: show content snippet, fallback to title */
                            const c = (item.content||'').replace(/<[^>]*>/g,'').trim();
                            return c ? c.slice(0,150) : (item.title||'').slice(0,150);
                        }
                        return (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,150);
                    })();
                    const artTitle = (plat === 'doc') ? (item.title||'').replace(/<[^>]*>/g,'').trim() : '';
                    const av    = (item.avatar_url||item.profile_image_url||ao0?.image||item.author_image||item.profile_image||item.thumbnail||'').trim();
                    const dt    = (item.date_created||item.created_at||'').split('T')[0];
                    const sent  = _normSent(item);
                    const sentLbl = sent==='pos'?'Pos':sent==='neg'?'Neg':'Neu';
                    const words = dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
                    const ini   = (words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||dName[0]||'?')).toUpperCase().replace(/['"]/g,'');
                    const avHtml = (av&&av.startsWith('http')) ? `<img src="${_es(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini}';">` : ini;
                    const sentBadge = `do-sent-badge--${sent}`;
                    const enc = encodeURIComponent(JSON.stringify(item));

                    /* For doc (Online News) items: show article title prominently */
                    if (plat === 'doc' && artTitle) {
                        const docUrl = (item.url||'').trim();
                        return `<div class="do-panel-item" onclick="DashDetail.openEncoded('${enc}','${plat}')">
                            <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);"><i class="ph ph-newspaper" style="font-size:16px;color:#fff;"></i></div>
                            <div class="do-panel-item-body">
                                <div class="do-panel-author" style="font-size:11px;color:#64748b;font-weight:600;">${_es(dName)}</div>
                                <div style="font-size:12px;font-weight:700;color:#1e293b;line-height:1.35;margin:3px 0 4px;">${_es(artTitle.slice(0,100))}</div>
                                <div class="do-panel-text" style="font-size:11px;">${_es(text||'(tidak ada konten)')}</div>
                                <div class="do-panel-footer">
                                    <span class="do-sent-badge ${sentBadge}">${sentLbl}</span>
                                    <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                                    <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                                    ${docUrl ? `<a href="${_es(docUrl)}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation();" style="margin-left:auto;font-size:10px;font-weight:700;color:${meta.color};display:inline-flex;align-items:center;gap:3px;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'"><i class="ph ph-arrow-square-out" style="font-size:12px;"></i>Buka</a>` : ''}
                                    ${dt ? `<span>${dt}</span>` : ''}
                                </div>
                            </div>
                        </div>`;
                    }

                    return `<div class="do-panel-item" onclick="DashDetail.openEncoded('${enc}','${plat}')">
                        <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                        <div class="do-panel-item-body">
                            <div class="do-panel-author">${_es(dName)}</div>
                            ${handle ? `<div class="do-panel-handle">${_es(handle)}</div>` : ''}
                            <div class="do-panel-text">${_es(text||'(tidak ada konten)')}</div>
                            <div class="do-panel-footer">
                                <span class="do-sent-badge ${sentBadge}">${sentLbl}</span>
                                <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                                <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                                ${dt ? `<span style="margin-left:auto;">${dt}</span>` : ''}
                            </div>
                        </div>
                    </div>`;
                }).join('');
            }

            function _renderLoadMore() {
                const shown     = (_page + 1) * PAGE;
                const remaining = items.length - shown;
                if (remaining <= 0) {
                    return `<div style="padding:9px;text-align:center;font-size:10px;color:#94A3B8;font-weight:600;border-top:1px dashed #E2E8F0;">
                        ✓ Semua ${items.length.toLocaleString()} mentions sudah dimuat
                    </div>`;
                }
                return `<div id="_dashLMWrap" style="padding:11px 14px;text-align:center;background:#F8FAFC;border-top:1px dashed #E2E8F0;">
                    <button id="_dashLMBtn" onclick="window.__dashLoadMore()"
                        style="display:inline-flex;align-items:center;gap:5px;padding:6px 20px;
                        background:var(--dash-primary);color:#fff;border:none;border-radius:5px;
                        font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;"
                        onmouseover="this.style.filter='brightness(1.12)'"
                        onmouseout="this.style.filter=''">
                        <i class="ph ph-arrow-circle-down" style="font-size:13px;"></i>
                        Muat lagi
                    </button>
                </div>`;
            }

            list.innerHTML = _renderItems(items.slice(0, PAGE)) + _renderLoadMore();

            window.__dashLoadMore = function() {
                const btn = document.getElementById('_dashLMBtn');
                if (btn) { btn.textContent = 'Memuat…'; btn.disabled = true; }
                setTimeout(() => {
                    _page++;
                    const batch = items.slice(_page * PAGE, (_page + 1) * PAGE);
                    document.getElementById('_doLMWrap')?.remove();
                    document.getElementById('_dashLMWrap')?.remove();
                    list.insertAdjacentHTML('beforeend', _renderItems(batch) + _renderLoadMore());
                }, 80);
            };
        }

        return { open, close, closeByOverlay, showPlatPicker, openPlatform, filterSent };
    })();

    /* ════════════════════════════════════════════════════════
       DASH DETAIL
    ════════════════════════════════════════════════════════ */
    const DashDetail = {
        openEncoded(enc, plat) {
            try { this.open(JSON.parse(decodeURIComponent(enc)), plat); } catch (e) {}
        },

        open(item, platform) {
            const panel = _$('dashDetailPanel'), body = _$('dashDetailBody'), title = _$('dashDetailTitle');
            if (!panel || !body) return;

            let truePlat = item._platform || platform;
            if (truePlat === 'all' || truePlat === 'doc' || !truePlat) {
                const url = String(item.url || item.link || '').toLowerCase();
                const has = (s) => url.includes(s);
                if (has('tiktok.com'))                            truePlat = 'tiktok';
                else if (has('youtube.com') || has('youtu.be'))   truePlat = 'youtube';
                else if (has('instagram.com'))                    truePlat = 'instagram';
                else if (has('facebook.com') || has('fb.watch'))  truePlat = 'fb';
                else if (has('twitter.com') || has('x.com'))      truePlat = 'twit';
            }
            platform = truePlat;

            const meta = DashCfg.platMeta[platform] || { label: platform==='all'?'All Media':platform, color: '#4361EE' };
            const SM2  = { '1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg' };
            const raw  = String(item.class_sentiment||item.sentiment||'0').toLowerCase();
            const sent = SM2[raw] || 'neu';
            const SLBL = { pos:'Positif', neg:'Negatif', neu:'Netral' };
            const SBGS = { pos:'do-dp2-sent--pos', neg:'do-dp2-sent--neg', neu:'do-dp2-sent--neu' };

            const ao2  = (() => { if (typeof item.author==='object'&&item.author) return item.author; try { return JSON.parse(item.author||'{}'); } catch(e){ return {}; }})();
            const rawName = (() => {
                if (platform==='fb')        return item.from_name||item.page_name||item.author_name||ao2?.name||item.author_handle||null;
                if (platform==='instagram') return item.username||null;
                if (platform==='tiktok')    return item.author_nickname||item.nickname||ao2?.nickname||null;
                if (platform==='youtube')   return item.channel_title||item.channel_name||item.snippet?.channelTitle||null;
                if (platform==='twit')      return item.name||ao2?.name||ao2?.scr_name||item.author_name||item.author_scr_name||null;
                return null;
            })();
            let name    = (rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'').trim();
            /* If name is numeric ID, try to use handle/screen_name instead */
            if (!name || /^\d{5,}$/.test(name) || name.toLowerCase()==='unknown') {
                const altName = (item.author_handle||item.author_scr_name||item.screen_name||ao2?.scr_name||ao2?.username||item.username||item.nickname||ao2?.name||'').trim();
                if (altName && !/^\d{5,}$/.test(altName)) {
                    name = altName;
                } else {
                    const lbls = { fb:'Facebook User', twit:'Twitter User', youtube:'YouTube User', instagram:'Instagram User', tiktok:'TikTok User' };
                    name = lbls[platform] || 'Unknown User';
                }
            }
            const handle  = ((platform==='instagram' ? item.username : '')||item.author_handle||item.author_scr_name||item.screen_name||ao2?.scr_name||item.username||'').trim();
            const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
            const av      = (item.avatar_url||item.profile_image_url||ao2?.image||item.author_image||item.profile_image||item.thumbnail||'').trim();
            const dt      = item.date_created||item.created_at||'';

            title.textContent = name;

            const words  = name.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
            const ini    = (words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||name[0]||'?')).toUpperCase().replace(/['"]/g,'');
            const avHtml = (av&&av.startsWith('http'))
                ? `<img src="${_es(av)}" onerror="this.parentElement.textContent='${ini}';">`
                : ini;

            let dtFmt = '';
            if (dt) {
                try {
                    const normalized = String(dt).trim().replace(' ', 'T');
                    const parts = normalized.split(/[T\-\:\.Z]/);
                    const year  = parseInt(parts[0], 10);
                    const month = parseInt(parts[1], 10) - 1;
                    const day   = parseInt(parts[2], 10);
                    const hour  = parseInt(parts[3] || '0', 10);
                    const min   = parseInt(parts[4] || '0', 10);
                    if (!isNaN(year) && !isNaN(month) && !isNaN(day)) {
                        const d = new Date(year, month, day, hour, min);
                        dtFmt = d.toLocaleDateString('id-ID', {
                            weekday: 'long', day: '2-digit', month: 'long',
                            year: 'numeric', hour: '2-digit', minute: '2-digit'
                        });
                    } else { dtFmt = dt.split(/[T\s]/)[0]; }
                } catch (e) { dtFmt = dt.split(/[T\s]/)[0]; }
            }

            /* Media embed & YouTube ID detection */
            let ytId = '';
            if (platform === 'youtube') {
                const url = item.url || item.link || item.permalink || item.original_url || '';
                ytId = ((url).match(/[?&]v=([a-zA-Z0-9_-]{11})/)||
                        (url).match(/youtu\.be\/([a-zA-Z0-9_-]{11})/)||
                        (url).match(/shorts\/([a-zA-Z0-9_-]{11})/)||
                        (url).match(/embed\/([a-zA-Z0-9_-]{11})/)||[])[1];
                
                if (!ytId) {
                    const flds = ['video_id','youtube_id','id_str','post_id','docid','id','sub_id'];
                    for(let f of flds) {
                        let v = item[f]; if(!v) continue;
                        let s = String(v).replace(/^(yt[-_])/i, '');
                        if(s.length===11) { ytId=s; break; }
                    }
                }
                if (!ytId && item.snippet) ytId = item.snippet.videoId || item.snippet.resourceId?.videoId;

                const thumb = item.thumbnail||item.thumbnail_url||item.image_url||item.cover||item.picture||(ytId ? `https://img.youtube.com/vi/${ytId}/hqdefault.jpg` : '');
                
                if (ytId) {
                    const eid = `yt_${ytId}_${Date.now()}`;
                    mediaHtml = `<div id="${eid}" class="do-dp2-media" style="position:relative;cursor:pointer;background:#000;height:220px;"
                        onclick="document.getElementById('${eid}').innerHTML='<iframe width=\\'100%\\' height=\\'220\\' src=\\'https://www.youtube.com/embed/${ytId}?autoplay=1&controls=1\\' frameborder=\\'0\\' allowfullscreen style=\\'border-radius:6px;\\'></iframe>'; document.getElementById('${eid}').style.height='auto';">
                        <img src="${thumb||`https://img.youtube.com/vi/${ytId}/hqdefault.jpg`}" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.src='https://img.youtube.com/vi/${ytId}/mqdefault.jpg'">
                        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.15);">
                            <div style="width:52px;height:36px;background:#ff0000;border-radius:8px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,.3);">
                                <i class="ph ph-play-fill" style="font-size:20px;color:#fff;margin-left:2px;"></i>
                            </div>
                        </div>
                    </div>`;
                } else if (thumb) {
                    mediaHtml = `<div class="do-dp2-media"><img src="${_es(thumb)}" onerror="this.parentElement.style.display='none'"></div>`;
                }
            } else if (platform === 'tiktok') {
                const tid   = ((item.url||'').match(/\/video\/(\d+)/)||(item.url||'').match(/\/v\/(\d+)/)||[])[1]||(item.video_id||item.aweme_id||'');
                const thumb = item.thumbnail||item.cover||item.image_url||item.video_cover||'';
                if (tid) {
                    const eid = `tt_${tid}_${Date.now()}`;
                    mediaHtml = `<div id="${eid}" style="position:relative;cursor:pointer;background:#111827;border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center;height:240px;margin-bottom:10px;"
                        onclick="DashDetail.loadTikTok('${eid}','${tid}')">
                        ${thumb ? `<img src="${_es(thumb)}" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:.65;pointer-events:none;">` : ''}
                        <div style="position:relative;z-index:2;width:56px;height:56px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.6);">
                            <i class="ph ph-play-fill" style="font-size:24px;color:#111827;margin-left:3px;"></i>
                        </div>
                        <div style="position:absolute;bottom:8px;right:8px;background:#111827;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:3px;letter-spacing:.5px;">TIKTOK</div>
                    </div>`;
                } else if (thumb) {
                    mediaHtml = `<div class="do-dp2-media"><img src="${_es(thumb)}" onerror="this.parentElement.style.display='none'" style="max-height:280px;object-fit:cover;width:100%;display:block;border-radius:6px;"></div>`;
                }
            } else {
                const thumb   = item.image_url||item.thumbnail||item.media_url||item.picture||item.display_url||item.featured_image||'';
                const isVideo = (item.media_type||item.type||'').toLowerCase().includes('video');
                if (thumb) {
                    mediaHtml = `<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;margin-bottom:10px;">
                        <img src="${_es(thumb)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:280px;object-fit:cover;display:block;">
                        ${isVideo ? `<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);">
                            <div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <i class="ph ph-play-fill" style="font-size:20px;color:${meta.color};margin-left:3px;"></i>
                            </div></div>` : ''}
                    </div>`;
                }
            }

            /* Stats */
            const statsMap = {
                twit:      [['Retweet',item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0],['Quote',item.num_quote||0]],
                fb:        [['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],
                instagram: [['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],
                youtube:   [['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||0]],
                tiktok:    [['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],
                doc:       [['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]],
            };
            const stats     = statsMap[platform] || [];
            const statsHtml = stats.some(s => parseInt(s[1]) > 0)
                ? `<div class="do-dp2-stats">${stats.map(([l,v]) =>
                    `<div class="do-dp2-stat"><div class="do-dp2-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="do-dp2-stat-lbl">${l}</div></div>`
                  ).join('')}</div>` : '';

            const handleDisp = handle && !handle.replace('@','').toLowerCase().startsWith(name.toLowerCase().slice(0,4))
                ? (handle.startsWith('@') ? handle : '@'+handle) : '';

            /* Source URL */
            let sourceUrl = '', isDirectArticleUrl = false;

            if (platform === 'doc') {
                const _cleanUrl = v => (v && typeof v === 'string'
                    && v !== 'null' && v !== 'undefined' && v.trim() !== ''
                    && v.startsWith('http')) ? v.trim() : '';
                sourceUrl = _cleanUrl(item.url)||_cleanUrl(item.link)||_cleanUrl(item.article_url)
                    ||_cleanUrl(item.source_url)||_cleanUrl(item.news_url)||_cleanUrl(item.permalink)
                    ||_cleanUrl(item.web_url)||_cleanUrl(item.full_url)||_cleanUrl(item.original_url)
                    ||_cleanUrl(item.reference)||_cleanUrl(item.href)||_cleanUrl(item.canonical_url)
                    ||_cleanUrl(item.post_url)||'';
                isDirectArticleUrl = !!sourceUrl;
            } else if (platform === 'twit') {
                const cj = (() => { try { return JSON.parse(item.contentJson||'{}'); } catch(e){ return {}; }})();
                sourceUrl = cj?.url || item.url || '';
                if (!sourceUrl || sourceUrl.includes('pbs.twimg.com') || sourceUrl.includes('t.co')) {
                    const ao  = typeof item.author==='object' ? item.author : (() => { try { return JSON.parse(item.author||'{}'); } catch(e){ return {}; }})();
                    const scr = (item.author_scr_name||item.screen_name||ao?.scr_name||ao?.username||'').replace(/^@/,'');
                    const sid = item.post_id_s||item.sub_id||item.tweet_id||item.id_str||item.docid?.replace('tw-','')||'';
                    if (scr && sid) sourceUrl = `https://twitter.com/${scr}/status/${sid}`;
                    else if (scr)   sourceUrl = `https://twitter.com/${scr}`;
                }
            } else if (platform === 'instagram') {
                const shortcode = item.shortcode||item.code||item.media_id||'';
                if (shortcode)          sourceUrl = `https://www.instagram.com/p/${shortcode}/`;
                else if (item.username) sourceUrl = `https://www.instagram.com/${item.username}/`;
            } else if (platform === 'youtube') {
                if (ytId)                sourceUrl = `https://www.youtube.com/watch?v=${ytId}`;
                else if (item.channel_id) sourceUrl = `https://www.youtube.com/channel/${item.channel_id}`;
            } else if (platform === 'tiktok') {
                const tid  = item.video_id||item.aweme_id||item.id||'';
                const nick = item.author_nickname||item.nickname||item.unique_id||item.author?.unique_id||'';
                if (tid && nick) sourceUrl = `https://www.tiktok.com/@${nick}/video/${tid}`;
                else if (tid)    sourceUrl = `https://vm.tiktok.com/${tid}`;
            } else if (platform === 'fb') {
                /* First check if the normalized data already has a URL */
                const fbUrl = item.url||item.link||item.post_url||'';
                if (fbUrl && fbUrl.startsWith('http') && (fbUrl.includes('facebook.com') || fbUrl.includes('fb.com') || fbUrl.includes('fb.watch'))) {
                    sourceUrl = fbUrl;
                } else {
                    const pname = item.page_name||item.from_name||item.author_name||item.author_handle||ao2?.username||'';
                    const pid2  = item.post_id_s||item.post_id||item.story_id||item.id||(item.docid||'').replace(/^fb-/,'')||'';
                    const fbProfUrl = ao2?.profile_url || '';
                    if (fbProfUrl)         sourceUrl = fbProfUrl;
                    else if (pname&&pid2)  sourceUrl = `https://www.facebook.com/${pname}/posts/${pid2}`;
                    else if (pname)        sourceUrl = `https://www.facebook.com/${pname}`;
                }
            }

            /* Action buttons */
            let actionBtns = '';
            if (platform === 'doc') {
                if (isDirectArticleUrl && sourceUrl) {
                    actionBtns = `<div class="do-dp2-actions">
                        <a href="${_es(sourceUrl)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link-news">
                            <i class="ph ph-newspaper"></i> Baca Artikel Asli
                        </a>
                    </div>`;
                } else {
                    actionBtns = `<div class="do-dp2-actions">
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:9px 12px;font-size:11px;color:#991b1b;display:flex;align-items:flex-start;gap:7px;line-height:1.4;">
                            <i class="ph ph-warning-circle" style="font-size:14px;flex-shrink:0;margin-top:1px;"></i>
                            <span>Link artikel spesifik tidak tersedia dari sumber data.</span>
                        </div>
                    </div>`;
                }
            } else if (sourceUrl) {
                actionBtns = `<div class="do-dp2-actions">
                    <a href="${_es(sourceUrl)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link">
                        <i class="ph ph-arrow-square-out"></i> Lihat ${_es(meta.label)} Asli
                    </a>
                </div>`;
            } else {
                actionBtns = `<div class="do-dp2-actions"></div>`;
            }

            /* Build detail body — special layout for doc (Online News) */
            const artTitle = (platform === 'doc') ? (item.title||'').replace(/<[^>]*>/g,'').trim() : '';

            if (platform === 'doc') {
                body.innerHTML = `
                <div class="do-dp2-avatar-row">
                    <div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);"><i class="ph ph-newspaper" style="font-size:22px;color:#fff;"></i></div>
                    <div>
                        <div class="do-dp2-name">${_es(name)}</div>
                        <span class="do-dp2-plat-badge" style="background:${meta.color}22;color:${meta.color};">${meta.label}</span>
                    </div>
                </div>
                ${dtFmt ? `<div class="do-dp2-meta">${dtFmt}</div>` : ''}
                <div class="do-dp2-sent ${SBGS[sent]}">${SLBL[sent]}</div>
                ${artTitle ? `<div style="font-size:15px;font-weight:700;color:#1e293b;line-height:1.4;margin:8px 0 12px;padding:0 2px;">${_es(artTitle)}</div>` : ''}
                ${mediaHtml}
                ${content ? `<div class="do-dp2-content">${_es(content)}</div>` : ''}
                ${statsHtml}
                ${actionBtns}`;
            } else {
                body.innerHTML = `
                <div class="do-dp2-avatar-row">
                    <div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                    <div>
                        <div class="do-dp2-name">${_es(name)}</div>
                        ${handleDisp ? `<div class="do-dp2-handle">${_es(handleDisp)}</div>` : ''}
                        <span class="do-dp2-plat-badge" style="background:${meta.color}22;color:${meta.color};">${meta.label}</span>
                    </div>
                </div>
                ${dtFmt ? `<div class="do-dp2-meta">${dtFmt}</div>` : ''}
                <div class="do-dp2-sent ${SBGS[sent]}">${SLBL[sent]}</div>
                ${mediaHtml}
                ${content ? `<div class="do-dp2-content">${_es(content)}</div>` : ''}
                ${statsHtml}
                ${actionBtns}`;
            }

            panel.classList.add('show');
        },

        close() {
            const panel = _$('dashDetailPanel');
            if (!panel) return;
            panel.classList.remove('show');
            panel.querySelectorAll('iframe').forEach(f => { try { f.src = f.src; } catch (e) {} });
        },

        loadTikTok(eid, tid) {
            const el = _$(eid); if (!el) return;
            el.style.cssText = 'cursor:default;min-height:560px;height:auto;background:#111827;border-radius:6px;overflow:hidden;margin-bottom:10px;';
            el.innerHTML = `<iframe src="https://www.tiktok.com/embed/v2/${tid}" width="100%" height="560"
                frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture"
                allowfullscreen style="display:block;border:none;border-radius:6px;background:#111827;"></iframe>`;
        },
    };

    /* ════════════════════════════════════════════════════════
       Track current project ID
    ════════════════════════════════════════════════════════ */
    const _origPanelOpen = DashPanel.open.bind(DashPanel);
    DashPanel.open = function(platform, sentiment, projectId, sdOverride, edOverride) {
        if (projectId) window.__dashCurrentPid = projectId;
        return _origPanelOpen(platform, sentiment, projectId, sdOverride, edOverride);
    };

    /* Platform picker — dismiss on outside click */
    document.addEventListener('mousedown', e => {
        const pp = _$('dashPlatPicker');
        if (pp?.classList.contains('show') && !pp.contains(e.target)) pp.classList.remove('show');
    });
    </script>
@endsection