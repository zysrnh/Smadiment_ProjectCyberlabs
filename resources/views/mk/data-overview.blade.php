@extends('mk.layouts.app')

@section('title', 'Data Overview - SMADIMENT')

@section('styles')
    <style>
        :root {
            --primary: #4CAF50;
            --primary-rgb: 76, 175, 80;
            --primary-lt: rgba(76, 175, 80, .10);
            --dark: #273B4A;
            --white: #FFFFFF;
            --bg: #F1F5F8;
            --green: #4CAF50;
            --green-light: #E8F5E9;
            --red: #EF4444;
            --red-light: #FEF2F2;
            --amber: #F59E0B;
            --amber-light: #FFFBEB;
            --cyan: #06B6D4;
            --cyan-light: #ECFEFF;
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
            --c-news: #0284c7;
            --c-twitter: #1d9bf0;
            --c-facebook: #1877f2;
            --c-instagram: #e1306c;
            --c-youtube: #ff0000;
            --c-tiktok: #111827;
        }

        @-webkit-keyframes ttDotPulse {
            0%,100% { -webkit-box-shadow: 0 0 0 3px rgba(3, 128, 71, .3); box-shadow: 0 0 0 3px rgba(3, 128, 71, .3); }
            50%      { -webkit-box-shadow: 0 0 0 6px transparent; box-shadow: 0 0 0 6px transparent; }
        }
        @keyframes ttDotPulse {
            0%,100% { box-shadow: 0 0 0 3px rgba(3, 128, 71, .3); }
            50%      { box-shadow: 0 0 0 6px transparent; }
        }
        @-webkit-keyframes fadeUp {
            from { opacity: 0; -webkit-transform: translateY(12px); transform: translateY(12px); }
            to   { opacity: 1; -webkit-transform: translateY(0); transform: translateY(0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @-webkit-keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position:  200% 0; }
        }
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position:  200% 0; }
        }
        @-webkit-keyframes spin {
            to { -webkit-transform: rotate(360deg); transform: rotate(360deg); }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @-webkit-keyframes slideInRight {
            from { -webkit-transform: translateX(100%); transform: translateX(100%); opacity: 0; }
            to   { -webkit-transform: translateX(0);    transform: translateX(0);    opacity: 1; }
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }
        @-webkit-keyframes slideOutRight {
            from { -webkit-transform: translateX(0);    transform: translateX(0);    opacity: 1; }
            to   { -webkit-transform: translateX(100%); transform: translateX(100%); opacity: 0; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0);    opacity: 1; }
            to   { transform: translateX(100%); opacity: 0; }
        }
        @-webkit-keyframes overlayIn  { from { opacity: 0; } to { opacity: 1; } }
        @keyframes overlayIn  { from { opacity: 0; } to { opacity: 1; } }
        @-webkit-keyframes overlayOut { from { opacity: 1; } to { opacity: 0; } }
        @keyframes overlayOut { from { opacity: 1; } to { opacity: 0; } }
        @-webkit-keyframes kpiShimmer {
            0%   { left: -100%; }
            100% { left:  150%; }
        }
        @keyframes kpiShimmer {
            0%   { left: -100%; }
            100% { left:  150%; }
        }
        @-webkit-keyframes kpiIconBounce {
            0%,100% { -webkit-transform: scale(1) rotate(0deg); transform: scale(1) rotate(0deg); }
            30%      { -webkit-transform: scale(1.25) rotate(-10deg); transform: scale(1.25) rotate(-10deg); }
            60%      { -webkit-transform: scale(1.1)  rotate(6deg); transform: scale(1.1)  rotate(6deg); }
        }
        @keyframes kpiIconBounce {
            0%,100% { transform: scale(1) rotate(0deg); }
            30%      { transform: scale(1.25) rotate(-10deg); }
            60%      { transform: scale(1.1)  rotate(6deg); }
        }

        .kpi-icon-bg {
            width: 48px; height: 48px; border-radius: 12px;
            display: -webkit-flex; display: flex;
            -webkit-align-items: center; align-items: center;
            -webkit-justify-content: center; justify-content: center;
            background: rgba(255,255,255,.2); font-size: 24px;
            color: #fff; -webkit-flex-shrink: 0; flex-shrink: 0;
        }

        .sk-block {
            border-radius: 4px;
            background: -webkit-linear-gradient(left, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%);
            background: linear-gradient(90deg, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%);
            background-size: 200% 100%;
            -webkit-animation: shimmer 1.4s infinite;
            animation: shimmer 1.4s infinite;
        }
        .snt-skel {
            border-radius: 4px;
            background: -webkit-linear-gradient(left, var(--slate-50) 25%, #e2e8f0 50%, var(--slate-50) 75%);
            background: linear-gradient(90deg, var(--slate-50) 25%, #e2e8f0 50%, var(--slate-50) 75%);
            background-size: 200% 100%;
            -webkit-animation: shimmer 1.5s ease-in-out infinite;
            animation: shimmer 1.5s ease-in-out infinite;
        }

        .spin-ring {
            width: 26px; height: 26px;
            border: 2.5px solid var(--slate-100);
            border-top-color: var(--primary);
            border-radius: 50%;
            -webkit-animation: spin .65s linear infinite;
            animation: spin .65s linear infinite;
        }
        .spinner-state {
            display: -webkit-flex; display: flex;
            -webkit-flex-direction: column; flex-direction: column;
            -webkit-align-items: center; align-items: center;
            -webkit-justify-content: center; justify-content: center;
            padding: 48px 20px; gap: 12px;
            color: var(--slate-400); font-size: 12px; font-weight: 600;
        }

        /* ══ KPI Hover — Safari-safe (no filter+transform combo bug) ══ */
        .kpi-card-hover {
            will-change: transform;
            -webkit-transition: -webkit-transform .25s cubic-bezier(.34,1.56,.64,1), -webkit-box-shadow .25s ease !important;
            transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s ease !important;
            cursor: default;
            position: relative !important;
            overflow: hidden !important;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }
        .kpi-card-hover::before {
            content: ''; position: absolute; top: 0; bottom: 0; left: -100%;
            width: 60%;
            background: -webkit-linear-gradient(left, transparent, rgba(255,255,255,.22), transparent);
            background: linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);
            pointer-events: none; z-index: 1; -webkit-transition: none; transition: none;
        }
        .kpi-card-hover:hover {
            -webkit-transform: translateY(-6px) scale(1.025) !important;
            transform: translateY(-6px) scale(1.025) !important;
            -webkit-box-shadow: 0 20px 40px rgba(0,0,0,.25) !important;
            box-shadow: 0 20px 40px rgba(0,0,0,.25) !important;
        }
        .kpi-card-hover:hover::before {
            -webkit-animation: kpiShimmer .6s ease forwards;
            animation: kpiShimmer .6s ease forwards;
        }
        .kpi-card-hover:hover .kpi-icon-bg {
            background: rgba(255,255,255,.35) !important;
            -webkit-transition: background .2s ease !important;
            transition: background .2s ease !important;
        }
        .kpi-card-hover:hover .kpi-icon-bg i {
            -webkit-animation: kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important;
            animation: kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important;
            display: inline-block !important;
        }
        .kpi-card-hover:active {
            -webkit-transform: translateY(-2px) scale(1.01) !important;
            transform: translateY(-2px) scale(1.01) !important;
            -webkit-transition-duration: .08s !important;
            transition-duration: .08s !important;
        }

        /* ══ Chart ══ */
        .chart-container { position: relative; }
        .chart-loading {
            position: absolute; inset: 0;
            display: -webkit-flex; display: flex;
            -webkit-flex-direction: column; flex-direction: column;
            -webkit-align-items: center; align-items: center;
            -webkit-justify-content: center; justify-content: center;
            gap: 8px; background: #fff; z-index: 2;
            -webkit-transition: opacity .3s; transition: opacity .3s;
        }
        .chart-loading.hidden { opacity: 0; pointer-events: none; }
        .chart-empty {
            height: 100%;
            display: -webkit-flex; display: flex;
            -webkit-flex-direction: column; flex-direction: column;
            -webkit-align-items: center; align-items: center;
            -webkit-justify-content: center; justify-content: center;
            gap: 6px; color: var(--slate-400); font-size: 12px; font-weight: 600;
        }
        .chart-empty i { font-size: 34px; color: var(--slate-300); display: block; }

        /* ══ Export ══ */
        .page-export-bar {
            display: -webkit-flex; display: flex;
            -webkit-align-items: center; align-items: center;
            -webkit-justify-content: space-between; justify-content: space-between;
            -webkit-flex-wrap: wrap; flex-wrap: wrap;
            gap: 10px; background: #fff;
            border: 1px solid var(--slate-200); border-radius: var(--radius);
            padding: 9px 14px; margin-bottom: 20px;
            -webkit-box-shadow: var(--shadow-sm); box-shadow: var(--shadow-sm);
        }
        .page-export-bar-left {
            display: -webkit-flex; display: flex;
            -webkit-align-items: center; align-items: center;
            gap: 8px; font-size: 12px; font-weight: 700; color: var(--slate-600);
        }
        .page-export-bar-left i { font-size: 15px; color: var(--primary); }
        .page-export-bar-right { display: -webkit-flex; display: flex; gap: 8px; }

        .page-export-btn {
            display: -webkit-inline-flex; display: inline-flex;
            -webkit-align-items: center; align-items: center;
            -webkit-justify-content: center; justify-content: center;
            width: 32px; height: 32px; border-radius: var(--radius-sm);
            font-size: 16px; cursor: pointer;
            -webkit-transition: all .15s ease; transition: all .15s ease;
            border: 1.5px solid transparent; font-family: inherit;
        }
        .page-export-btn-pdf { background:#fff3f3; color:#dc2626; border-color:#fca5a5; }
        .page-export-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
        .page-export-btn-img { background:var(--primary-lt); color:var(--primary); border-color:rgba(3,128,71,.3); }
        .page-export-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
        .page-export-btn:disabled { opacity:.55; cursor:not-allowed; pointer-events:none; }
        .page-export-btn .export-spinner {
            width:13px; height:13px; border:2px solid currentColor; border-top-color:transparent;
            border-radius:50%;
            -webkit-animation:spin .65s linear infinite; animation:spin .65s linear infinite;
            display:none;
        }
        .page-export-btn.exporting .export-spinner { display:inline-block; }
        .page-export-btn.exporting .export-icon   { display:none; }

        .card-exp-btn {
            display:-webkit-inline-flex; display:inline-flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            width:28px; height:28px; border-radius:var(--radius-sm);
            font-size:14px; cursor:pointer; -webkit-flex-shrink:0; flex-shrink:0;
            -webkit-transition:all .14s ease; transition:all .14s ease;
            border:1px solid transparent; font-family:inherit; background:transparent;
        }
        .card-exp-btn-pdf { color:#dc2626; border-color:#fca5a5; background:#fff3f3; }
        .card-exp-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
        .card-exp-btn-img { color:var(--primary); border-color:rgba(3,128,71,.3); background:var(--primary-lt); }
        .card-exp-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
        .card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
        .card-exp-btn .export-spinner {
            width:11px; height:11px; border:2px solid currentColor; border-top-color:transparent;
            border-radius:50%;
            -webkit-animation:spin .65s linear infinite; animation:spin .65s linear infinite;
            display:none;
        }
        .card-exp-btn.exporting .export-spinner { display:inline-block; }
        .card-exp-btn.exporting .export-icon   { display:none; }

        .export-toast {
            position:fixed; bottom:24px; left:50%;
            -webkit-transform:translateX(-50%) translateY(20px);
            transform:translateX(-50%) translateY(20px);
            background:var(--slate-900); color:#fff; border-radius:var(--radius);
            padding:10px 18px; font-size:12px; font-weight:600;
            -webkit-box-shadow:var(--shadow-lg); box-shadow:var(--shadow-lg);
            z-index:99999; opacity:0; pointer-events:none;
            -webkit-transition:opacity .22s ease, -webkit-transform .22s ease;
            transition:opacity .22s ease, transform .22s ease;
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:8px; white-space:nowrap;
        }
        .export-toast.show  {
            opacity:1;
            -webkit-transform:translateX(-50%) translateY(0);
            transform:translateX(-50%) translateY(0);
        }
        .export-toast.success { background:#065f46; }
        .export-toast.error   { background:#991b1b; }

        /* ══ Mention Card ══ */
        .do-mention-body {
            display:-webkit-flex; display:flex;
            -webkit-align-items:stretch; align-items:stretch;
            min-height:260px;
        }
        .do-mention-chart {
            -webkit-flex:1; flex:1;
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            padding:16px 8px; min-width:0;
        }
        /* Make pie container taller so labels aren't clipped */
        #chMentionPie {
            width: 100% !important;
            max-width: 320px;
            height: 260px !important;
        }

        .do-mention-stats {
            width:175px; -webkit-flex-shrink:0; flex-shrink:0;
            border-left:1px solid var(--slate-200);
            padding:16px 14px;
            display:-webkit-flex; display:flex;
            -webkit-flex-direction:column; flex-direction:column;
            -webkit-justify-content:center; justify-content:center;
            gap:12px;
        }

        /* ══ SOV Card ══ */
        .do-sov-body {
            display:-webkit-flex; display:flex;
            -webkit-align-items:stretch; align-items:stretch;
            min-height:300px;
        }
        .do-sov-chart {
            -webkit-flex:1; flex:1;
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            padding:8px 16px; min-width:0;
        }
        #chSovPie { width:100% !important; height:300px !important; }

        .do-sov-stats {
            width:190px; -webkit-flex-shrink:0; flex-shrink:0;
            border-left:1px solid var(--slate-200);
            padding:14px 13px;
            display:-webkit-flex; display:flex;
            -webkit-flex-direction:column; flex-direction:column;
            -webkit-justify-content:flex-start; justify-content:flex-start;
            gap:0; overflow-y:auto; max-height:300px;
        }
        .do-sov-stats::-webkit-scrollbar { width:3px; }
        .do-sov-stats::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

        /* ══ Shared stat styles ══ */
        .do-mstat-label {
            font-size:10px; font-weight:700; color:var(--slate-400);
            text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px;
        }
        .do-mstat-row {
            display:-webkit-flex; display:flex;
            -webkit-flex-direction:column; flex-direction:column;
            gap:2px; cursor:pointer;
            border-radius:var(--radius-sm); padding:6px 7px; margin:0 -7px;
            -webkit-transition:background .13s; transition:background .13s;
        }
        .do-mstat-row:hover { background:var(--primary-lt); }
        .do-mstat-name {
            font-size:11px; font-weight:600; color:var(--slate-500);
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:5px;
        }
        .do-mstat-name span {
            display:inline-block; width:7px; height:7px;
            border-radius:50%; -webkit-flex-shrink:0; flex-shrink:0;
        }
        .do-mstat-val-row {
            display:-webkit-flex; display:flex;
            -webkit-align-items:baseline; align-items:baseline;
            gap:6px;
        }
        .do-mstat-val {
            font-size:17px; font-weight:800; letter-spacing:-.5px;
            color:var(--slate-900); line-height:1.1;
        }
        .do-mstat-pct { font-size:11px; font-weight:700; line-height:1.1; }
        .do-mstat-divider { height:1px; background:var(--slate-100); margin:8px 0; }
        .do-mstat-total-lbl {
            font-size:10px; font-weight:700; color:var(--slate-400);
            text-transform:uppercase; letter-spacing:.4px;
        }
        .do-mstat-total-val {
            font-size:20px; font-weight:800; letter-spacing:-1px;
            color:var(--primary); line-height:1.1;
        }

        /* ══ Map ══ */
        .do-map-wrap { display:-webkit-flex; display:flex; }
        .do-map-area { -webkit-flex:1; flex:1; min-width:0; position:relative; }
        .do-loc-panel {
            width:210px; -webkit-flex-shrink:0; flex-shrink:0;
            border-left:1px solid var(--slate-200);
            display:-webkit-flex; display:flex;
            -webkit-flex-direction:column; flex-direction:column;
        }
        .do-loc-title {
            padding:11px 14px 8px; font-size:10px; font-weight:700;
            color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px;
            border-bottom:1px solid var(--slate-100);
        }
        .do-loc-list { overflow-y:auto; -webkit-flex:1; flex:1; max-height:400px; }
        .do-loc-list::-webkit-scrollbar { width:3px; }
        .do-loc-list::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
        .do-loc-item {
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:8px; padding:9px 12px;
            cursor:pointer; border-bottom:1px solid var(--slate-50);
            -webkit-transition:all .13s; transition:all .13s;
        }
        .do-loc-item:hover { background:rgba(3,128,71,.05); }
        .do-loc-item.active {
            background:var(--primary-lt); border-left:3px solid var(--primary); padding-left:9px;
        }
        .do-loc-rank { font-size:10px; font-weight:700; color:var(--primary); width:16px; -webkit-flex-shrink:0; flex-shrink:0; }
        .do-loc-info { -webkit-flex:1; flex:1; min-width:0; }
        .do-loc-name {
            font-size:11px; font-weight:600; color:var(--slate-800);
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .do-loc-count { font-size:10px; color:var(--slate-400); font-weight:500; }
        .do-loc-dot { width:7px; height:7px; border-radius:50%; -webkit-flex-shrink:0; flex-shrink:0; background:var(--primary); }

        /* ══ Tables ══ */
        .do-tbl { width:100%; border-collapse:separate; border-spacing:0; font-size:12px; }
        .do-tbl th {
            padding:0 0 8px; text-align:left; font-size:10px; font-weight:700;
            color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px;
            border-bottom:1px solid var(--slate-200);
        }
        .do-tbl td { padding:8px 0; border-bottom:1px solid var(--slate-100); vertical-align:middle; }
        .do-tbl tbody tr:last-child td { border-bottom:none; }
        .do-tbl tbody tr:hover td { background:var(--slate-50); }
        .do-tbl-rank { font-weight:800; color:var(--primary); width:22px; font-size:11px; }
        .do-tbl-name {
            font-weight:600; white-space:nowrap; overflow:hidden;
            text-overflow:ellipsis; max-width:220px;
        }
        .do-tbl-num { text-align:right; font-weight:700; font-size:11px; color:var(--slate-500); }
        .topic-link { color:var(--slate-800); text-decoration:none; -webkit-transition:color .14s; transition:color .14s; }
        .topic-link:hover { color:var(--primary); }

        .do-view-all {
            display:-webkit-inline-flex; display:inline-flex;
            -webkit-align-items:center; align-items:center;
            gap:4px; padding:4px 10px;
            background:transparent; color:var(--primary); border:1px solid var(--primary);
            border-radius:var(--radius-sm); font-size:10px; font-weight:700;
            cursor:pointer; -webkit-transition:all .14s; transition:all .14s;
        }
        .do-view-all:hover { background:var(--primary); color:#fff; }

        .do-body-scroll { max-height:210px; overflow-y:auto; }
        .do-body-scroll::-webkit-scrollbar { width:3px; }
        .do-body-scroll::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

        .do-empty {
            display:-webkit-flex; display:flex;
            -webkit-flex-direction:column; flex-direction:column;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            padding:36px 16px; gap:7px;
        }
        .do-empty i { font-size:32px; color:var(--slate-300); }
        .do-empty-txt { font-size:12px; font-weight:600; color:var(--slate-400); }

        /* ══ Slide Panel ══ */
        .do-panel-overlay {
            position:fixed; inset:0; z-index:9000;
            background:rgba(15,23,42,.45);
            -webkit-backdrop-filter:blur(4px); backdrop-filter:blur(4px);
            display:none;
        }
        .do-panel-overlay.show   { display:block; -webkit-animation:overlayIn .22s ease-out; animation:overlayIn .22s ease-out; }
        .do-panel-overlay.hiding { -webkit-animation:overlayOut .22s ease-out forwards; animation:overlayOut .22s ease-out forwards; }

        .do-panel {
            position:fixed; top:0; right:0; bottom:0; z-index:9001;
            width:480px; max-width:100vw; background:#fff;
            display:none; -webkit-flex-direction:column; flex-direction:column;
            border-left:1px solid var(--slate-200);
            -webkit-box-shadow:-8px 0 40px rgba(15,23,42,.16);
            box-shadow:-8px 0 40px rgba(15,23,42,.16);
        }
        .do-panel.show   { display:-webkit-flex; display:flex; -webkit-animation:slideInRight .28s cubic-bezier(.4,0,.2,1); animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
        .do-panel.hiding { -webkit-animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }

        .do-panel-header {
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:10px; padding:14px 16px;
            border-bottom:1px solid var(--slate-200);
            background:var(--slate-50); -webkit-flex-shrink:0; flex-shrink:0;
        }
        .do-panel-dot   { width:9px; height:9px; border-radius:50%; -webkit-flex-shrink:0; flex-shrink:0; }
        .do-panel-title { font-size:13px; font-weight:700; color:var(--slate-900); -webkit-flex:1; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .do-panel-close {
            width:28px; height:28px; border-radius:var(--radius-sm);
            border:1px solid var(--slate-200); background:#fff; cursor:pointer;
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            color:var(--slate-500); font-size:16px;
            -webkit-transition:all .14s; transition:all .14s;
            -webkit-flex-shrink:0; flex-shrink:0;
        }
        .do-panel-close:hover { background:var(--red); border-color:var(--red); color:#fff; }
        .do-panel-actions {
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:7px; padding:7px 12px;
            border-bottom:1px solid var(--slate-200); background:#fff;
            -webkit-flex-shrink:0; flex-shrink:0;
        }
        .do-panel-meta {
            -webkit-flex:1; flex:1; font-size:10px; font-weight:700;
            color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px;
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:5px;
        }
        .do-panel-tabs {
            display:-webkit-flex; display:flex;
            background:var(--slate-100); border:1px solid var(--slate-200);
            border-radius:var(--radius-sm); padding:2px; gap:2px;
        }
        .do-panel-tab {
            padding:3px 9px; border-radius:3px; border:none; background:transparent;
            font-size:11px; font-weight:700; cursor:pointer;
            -webkit-transition:all .13s; transition:all .13s;
            color:var(--slate-500); font-family:inherit;
        }
        .do-panel-tab:hover { background:#fff; }
        .do-panel-tab.active { background:#fff; -webkit-box-shadow:0 1px 4px rgba(0,0,0,.08); box-shadow:0 1px 4px rgba(0,0,0,.08); }
        .do-panel-tab.active[data-s="all"] { color:var(--primary); }
        .do-panel-tab.neg.active { color:var(--red); }
        .do-panel-tab.pos.active { color:#0ea5e9; }
        .do-panel-tab.neu.active { color:var(--slate-500); }

        .do-panel-list { overflow-y:auto; -webkit-flex:1; flex:1; padding:2px 0; min-height:0; }
        .do-panel-list::-webkit-scrollbar { width:4px; }
        .do-panel-list::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

        .do-panel-item {
            display:-webkit-flex; display:flex;
            gap:10px; padding:10px 14px;
            border-bottom:1px solid var(--slate-50); cursor:pointer;
            -webkit-transition:background .1s; transition:background .1s;
            -webkit-align-items:flex-start; align-items:flex-start;
        }
        .do-panel-item:hover { background:#f0fff8; }
        .do-panel-item:last-child { border-bottom:none; }
        .do-panel-avatar {
            width:36px; height:36px; border-radius:50%; -webkit-flex-shrink:0; flex-shrink:0;
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            font-weight:700; font-size:12px; color:#fff;
            border:1.5px solid var(--slate-200); overflow:hidden;
        }
        .do-panel-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
        .do-panel-item-body { -webkit-flex:1; flex:1; min-width:0; }
        .do-panel-author { font-size:12px; font-weight:700; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .do-panel-handle { font-size:10px; color:var(--slate-400); font-weight:500; margin-bottom:2px; }
        .do-panel-text {
            font-size:11px; color:var(--slate-600); line-height:1.5;
            display:-webkit-box; display:box;
            -webkit-line-clamp:2; -webkit-box-orient:vertical;
            overflow:hidden; margin-bottom:4px;
            /* Safari fallback */
            max-height:3.2em;
        }
        .do-panel-footer {
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:5px; font-size:10px; color:var(--slate-400);
            -webkit-flex-wrap:wrap; flex-wrap:wrap;
        }
        .do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
        .do-sent-badge--pos { background:#d1fae5; color:#065f46; }
        .do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
        .do-sent-badge--neu { background:var(--slate-100); color:var(--slate-500); }
        .do-panel-loading {
            display:-webkit-flex; display:flex;
            -webkit-flex-direction:column; flex-direction:column;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            height:100%; gap:12px; color:var(--slate-400); font-size:13px; font-weight:600;
        }
        .do-panel-spinner {
            width:28px; height:28px; border:2.5px solid var(--slate-100);
            border-top-color:var(--primary); border-radius:50%;
            -webkit-animation:spin .65s linear infinite; animation:spin .65s linear infinite;
        }

        /* ══ Detail Panel ══ */
        .do-detail-panel {
            position:absolute; inset:0; background:#fff; z-index:5;
            display:none; -webkit-flex-direction:column; flex-direction:column;
            -webkit-animation:slideInRight .2s cubic-bezier(.4,0,.2,1);
            animation:slideInRight .2s cubic-bezier(.4,0,.2,1);
        }
        .do-detail-panel.show { display:-webkit-flex; display:flex; }
        .do-dp2-header {
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:8px; padding:12px 14px;
            background:var(--slate-50); border-bottom:1px solid var(--slate-200);
            -webkit-flex-shrink:0; flex-shrink:0;
        }
        .do-dp2-back {
            width:28px; height:28px; border-radius:var(--radius-sm);
            border:1px solid var(--slate-200); background:#fff; cursor:pointer;
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            color:var(--slate-500); -webkit-transition:all .13s; transition:all .13s; font-size:14px;
        }
        .do-dp2-back:hover { background:var(--primary-lt); color:var(--primary); border-color:var(--primary); }
        .do-dp2-title { font-size:13px; font-weight:700; color:var(--slate-900); -webkit-flex:1; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .do-dp2-body { overflow-y:auto; -webkit-flex:1; flex:1; padding:16px; }
        .do-dp2-body::-webkit-scrollbar { width:4px; }
        .do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
        .do-dp2-avatar-row {
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:10px; margin-bottom:12px;
        }
        .do-dp2-avatar-lg {
            width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700; font-size:16px;
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            border:2px solid var(--slate-200); overflow:hidden; -webkit-flex-shrink:0; flex-shrink:0;
        }
        .do-dp2-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
        .do-dp2-name   { font-size:14px; font-weight:700; color:var(--slate-900); }
        .do-dp2-handle { font-size:11px; color:var(--slate-400); font-weight:500; }
        .do-dp2-plat-badge { display:inline-block; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; margin-top:3px; }
        .do-dp2-meta {
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:space-between; justify-content:space-between;
            font-size:11px; color:var(--slate-400); font-weight:500; margin-bottom:10px;
        }
        .do-dp2-sent {
            display:-webkit-inline-flex; display:inline-flex;
            -webkit-align-items:center; align-items:center;
            gap:4px; padding:4px 10px; border-radius:3px; font-size:11px; font-weight:700; margin-bottom:10px;
        }
        .do-dp2-sent--pos { background:#d1fae5; color:#065f46; }
        .do-dp2-sent--neg { background:#fee2e2; color:#991b1b; }
        .do-dp2-sent--neu { background:var(--slate-100); color:var(--slate-500); }
        .do-dp2-content {
            font-size:12px; color:var(--slate-700); line-height:1.7; margin-bottom:12px;
            background:var(--slate-50); border-radius:var(--radius-sm);
            padding:10px 12px; border:1px solid var(--slate-200); word-break:break-word;
        }
        .do-dp2-media { border-radius:var(--radius-sm); overflow:hidden; margin-bottom:10px; background:#000; }
        .do-dp2-media img { width:100%; max-height:220px; object-fit:cover; display:block; }
        .do-dp2-media--video { background:var(--slate-900); }
        .do-dp2-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
        .do-dp2-stat { background:var(--slate-50); border-radius:var(--radius-sm); padding:8px 10px; border:1px solid var(--slate-200); text-align:center; }
        .do-dp2-stat-val { font-size:14px; font-weight:700; color:var(--slate-900); }
        .do-dp2-stat-lbl { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
        .do-dp2-link {
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            gap:6px; padding:9px 14px; background:var(--primary); color:#fff;
            border-radius:var(--radius-sm); font-size:12px; font-weight:700;
            text-decoration:none; -webkit-transition:filter .14s; transition:filter .14s; margin-top:4px;
        }
        .do-dp2-link:hover { filter:brightness(1.1); -webkit-filter:brightness(1.1); color:#fff; }
        .do-dp2-link i { font-size:13px; }

        /* ══ Platform picker ══ */
        .do-plat-picker {
            position:fixed; z-index:20000; background:#fff;
            border:1px solid var(--slate-200); border-radius:var(--radius);
            -webkit-box-shadow:var(--shadow-lg); box-shadow:var(--shadow-lg);
            padding:5px; min-width:175px; font-family:inherit; display:none;
            -webkit-animation:fadeUp .14s ease-out; animation:fadeUp .14s ease-out;
        }
        .do-plat-picker.show { display:block; }
        .do-plat-picker-head {
            padding:4px 9px 6px; font-size:10px; font-weight:700; color:var(--slate-400);
            text-transform:uppercase; letter-spacing:.5px;
            border-bottom:1px solid var(--slate-100); margin-bottom:3px;
        }
        .do-plat-btn {
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            gap:7px; padding:7px 10px;
            border-radius:var(--radius-sm); font-size:12px; font-weight:600;
            cursor:pointer; background:transparent; border:none;
            font-family:inherit; width:100%; text-align:left;
            color:var(--slate-700); -webkit-transition:background .12s; transition:background .12s;
        }
        .do-plat-btn:hover { background:var(--primary-lt); color:var(--primary); }
        .do-plat-dot { width:8px; height:8px; border-radius:50%; -webkit-flex-shrink:0; flex-shrink:0; margin-left:auto; }

        /* ══ Modals ══ */
        .do-modal-overlay {
            position:fixed; inset:0; z-index:8000;
            background:rgba(15,23,42,.55);
            -webkit-backdrop-filter:blur(4px); backdrop-filter:blur(4px);
            display:none; -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
        }
        .do-modal-overlay.show { display:-webkit-flex; display:flex; -webkit-animation:overlayIn .2s ease-out; animation:overlayIn .2s ease-out; }
        .do-modal-box {
            background:#fff; border-radius:var(--radius);
            width:90%; max-width:560px; max-height:80vh;
            -webkit-box-shadow:var(--shadow-lg); box-shadow:var(--shadow-lg);
            overflow:hidden; -webkit-animation:fadeUp .24s ease-out; animation:fadeUp .24s ease-out;
            display:-webkit-flex; display:flex; -webkit-flex-direction:column; flex-direction:column;
        }
        .do-modal-head {
            display:-webkit-flex; display:flex;
            -webkit-justify-content:space-between; justify-content:space-between;
            -webkit-align-items:center; align-items:center;
            padding:14px 20px; border-bottom:1px solid var(--slate-200);
        }
        .do-modal-head-title { font-size:15px; font-weight:700; color:var(--slate-900); margin:0; }
        .do-modal-head-close {
            width:30px; height:30px; border-radius:var(--radius-sm); background:#fff;
            border:1px solid var(--slate-200);
            display:-webkit-flex; display:flex;
            -webkit-align-items:center; align-items:center;
            -webkit-justify-content:center; justify-content:center;
            cursor:pointer; -webkit-transition:all .14s; transition:all .14s;
            font-size:16px; color:var(--slate-500);
        }
        .do-modal-head-close:hover { background:var(--red); border-color:var(--red); color:#fff; }
        .do-modal-body { padding:16px 20px 20px; overflow-y:auto; }
        .do-modal-body::-webkit-scrollbar { width:4px; }
        .do-modal-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

        /* ══ Grid layout ══ */
        .do-dashboard-grid {
            display:grid; grid-template-columns:repeat(2,1fr);
            gap:28px 24px; margin-bottom:28px;
            -webkit-align-items:stretch; align-items:stretch;
        }
        .do-dashboard-grid > .card { margin-bottom:0 !important; height:100%; }
        .do-col-full { grid-column:1 / -1; }

        @media(max-width:767px) {
            .do-dashboard-grid { grid-template-columns:1fr; }
            .do-mention-body, .do-sov-body { -webkit-flex-direction:column; flex-direction:column; }
            .do-mention-stats, .do-sov-stats {
                width:100%; border-left:none; border-top:1px solid var(--slate-200);
                -webkit-flex-direction:row; flex-direction:row;
                -webkit-flex-wrap:wrap; flex-wrap:wrap;
                gap:14px; padding:14px 16px; max-height:none;
            }
            .do-map-wrap { -webkit-flex-direction:column; flex-direction:column; }
            .do-loc-panel { width:100%; border-left:none; border-top:1px solid var(--slate-200); }
            .do-panel { width:100vw; }
        }
    </style>
@endsection

@section('page-title', 'Data Overview')

@section('content')

    @include('mk.layouts.partials.filter-datepicker')

    <div id="doPageExportArea">

        {{-- ══ KPI Cards ══ --}}
        <div class="row mb-3">
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 text-white kpi-card-hover" style="background:#06B6D4;-webkit-animation:fadeUp .38s ease-out both;animation:fadeUp .38s ease-out both;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p>
                                <h3 class="mb-0 text-white f-w-300" id="kpiTotal">—</h3>
                                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTotalSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
                            </div>
                            <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chat-dots"></i></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 text-white kpi-card-hover" style="background:#F59E0B;-webkit-animation:fadeUp .38s ease-out .10s both;animation:fadeUp .38s ease-out .10s both;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="mb-1 text-white text-opacity-75 f-12">Social Media</p>
                                <h3 class="mb-0 text-white f-w-300" id="kpiSocial">—</h3>
                                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiSocialSub"><i class="ph ph-share-network me-1"></i>Loading...</p>
                            </div>
                            <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-share-network"></i></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 text-white kpi-card-hover" style="background:#4CAF50;-webkit-animation:fadeUp .38s ease-out .05s both;animation:fadeUp .38s ease-out .05s both;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="mb-1 text-white text-opacity-75 f-12">Online News</p>
                                <h3 class="mb-0 text-white f-w-300" id="kpiNews">—</h3>
                                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiNewsSub"><i class="ph ph-newspaper me-1"></i>Loading...</p>
                            </div>
                            <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-newspaper"></i></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card h-100 text-white kpi-card-hover" style="background:#038047;-webkit-animation:fadeUp .38s ease-out .15s both;animation:fadeUp .38s ease-out .15s both;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="mb-1 text-white text-opacity-75 f-12">Platforms Active</p>
                                <h3 class="mb-0 text-white f-w-300" id="kpiPlatforms">—</h3>
                                <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiPlatformsSub"><i class="ph ph-circles-four me-1"></i>Loading...</p>
                            </div>
                            <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-circles-four"></i></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ Page Export Toolbar ══ --}}
        <div class="page-export-bar" data-html2canvas-ignore="true">
            <div class="page-export-bar-left">
                <i class="ph ph-export"></i><span>Export Halaman</span>
                <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">Trending · Mention · SOV · Sentiment · Map</span>
            </div>
            <div class="page-export-bar-right">
                <button type="button" class="page-export-btn page-export-btn-pdf" id="doPageExpPdf" onclick="DOExport.run('pdf',this)" title="Export PDF">
                    <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                </button>
                <button type="button" class="page-export-btn page-export-btn-img" id="doPageExpImg" onclick="DOExport.run('image',this)" title="Export PNG">
                    <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                </button>
            </div>
        </div>

        {{-- ══ DASHBOARD GRID ══ --}}
        <div class="do-dashboard-grid">

            {{-- Trending Topics --}}
            <div class="card" data-lazy="trending-topics" id="do-card-trending" style="-webkit-animation:fadeUp .38s ease-out .18s both;animation:fadeUp .38s ease-out .18s both;">
                <div id="card-export-trending">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-trend-up f-18 text-primary"></i></div>
                            <h6 class="mb-0">Trending Topics</h6>
                        </div>
                        <div class="d-flex align-items-center gap-2" id="trendingHead">
                            <span class="badge bg-light-secondary text-muted rounded-pill">News</span>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('card-export-trending','trending','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('card-export-trending','trending','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
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
            </div>

            {{-- Top Hashtag --}}
            <div class="card" data-lazy="top-hashtags" id="do-card-hashtag" style="-webkit-animation:fadeUp .38s ease-out .21s both;animation:fadeUp .38s ease-out .21s both;">
                <div id="card-export-hashtag">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-hash f-18 text-primary"></i></div>
                            <h6 class="mb-0">Top Hashtag</h6>
                        </div>
                        <div class="d-flex align-items-center gap-2" id="hashtagHead">
                            <span class="badge bg-light-secondary text-muted rounded-pill">X</span>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('card-export-hashtag','hashtag','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('card-export-hashtag','hashtag','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
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
            </div>

            {{-- Mention --}}
            <div class="card" data-lazy="mention-combined" id="do-card-mention" style="-webkit-animation:fadeUp .38s ease-out .24s both;animation:fadeUp .38s ease-out .24s both;">
                <div id="card-export-mention">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chat-dots f-18 text-primary"></i></div>
                            <h6 class="mb-0">Share of Voice by Media Platform</h6>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light-secondary text-muted rounded-pill">All Media</span>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('card-export-mention','mention','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('card-export-mention','mention','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                            </div>
                        </div>
                    </div>
                    <div id="mentionSkelWrap" style="padding:16px;">
                        <div class="sk-block" style="height:240px;border-radius:6px;"></div>
                    </div>
                    <div class="do-mention-body" id="mentionBody" style="display:none;">
                        <div class="do-mention-chart">
                            <div id="chMentionPie"></div>
                        </div>
                        <div class="do-mention-stats">
                            <div class="do-mstat-label">Breakdown</div>
                            <div class="do-mstat-row" id="statNewsRow">
                                <span class="do-mstat-name"><span style="background:#0284c7;"></span>Online News</span>
                                <div class="do-mstat-val-row">
                                    <span class="do-mstat-val" id="mentionNewsVal">—</span>
                                    <span class="do-mstat-pct" id="mentionNewsPct" style="color:#0284c7;"></span>
                                </div>
                            </div>
                            <div class="do-mstat-row" id="statSocialRow">
                                <span class="do-mstat-name"><span style="background:var(--primary);"></span>Social Media</span>
                                <div class="do-mstat-val-row">
                                    <span class="do-mstat-val" id="mentionSocialVal">—</span>
                                    <span class="do-mstat-pct" id="mentionSocialPct" style="color:var(--primary);"></span>
                                </div>
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

            {{-- Share of Voice --}}
            <div class="card" data-lazy="sov" id="do-card-sov" style="-webkit-animation:fadeUp .38s ease-out .27s both;animation:fadeUp .38s ease-out .27s both;">
                <div id="card-export-sov">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-microphone f-18 text-primary"></i></div>
                            <div>
                                <h6 class="mb-0">Share of Voice</h6>
                                <small class="text-muted">Distribusi per platform</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light-secondary text-muted rounded-pill">By Media</span>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('card-export-sov','sov','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('card-export-sov','sov','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                            </div>
                        </div>
                    </div>
                    <div id="sovSkel" style="padding:16px;">
                        <div class="sk-block" style="height:240px;border-radius:6px;"></div>
                    </div>
                    <div id="sovBody" style="display:none;">
                        <div id="chSovPie"></div>
                    </div>
                </div>
            </div>

            {{-- Sentiment Timeline --}}
            <div class="card do-col-full" data-lazy="sentiment-timeline" id="do-card-sentiment" style="-webkit-animation:fadeUp .38s ease-out .30s both;animation:fadeUp .38s ease-out .30s both;">
                <div id="card-export-sentiment">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-pulse f-18 text-primary"></i></div>
                            <div>
                                <h6 class="mb-0">Mention Trend</h6>
                                <small class="text-muted" id="sntSubtitle">Klik pada garis untuk lihat mentions per sentimen</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light-secondary text-muted rounded-pill">All Media</span>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('card-export-sentiment','sentiment','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('card-export-sentiment','sentiment','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="position:relative;padding-bottom:12px;">
                        <div id="skSentiment" style="display:flex;align-items:center;justify-content:center;gap:10px;padding:60px 20px;">
                            <div class="spin-ring"></div>
                            <span style="font-size:13px;font-weight:600;color:var(--slate-400);">Loading chart…</span>
                        </div>
                        <div id="chSentiment" style="display:none;"></div>
                    </div>
                </div>
            </div>

            {{-- Buzzer Map --}}
            <div class="card do-col-full" data-lazy="buzzer-map" id="do-card-map" style="-webkit-animation:fadeUp .38s ease-out .33s both;animation:fadeUp .38s ease-out .33s both;">
                <div id="card-export-map">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-map-pin f-18 text-primary"></i></div>
                            <h6 class="mb-0">Buzzer Map</h6>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light-secondary text-muted rounded-pill">Geographic</span>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="DOExport.runCard('card-export-map','map','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="DOExport.runCard('card-export-map','map','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
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
                            <div class="do-loc-body" id="buzzLocs"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /do-dashboard-grid --}}

    </div>{{-- /doPageExportArea --}}

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
            <button class="do-panel-close" style="margin-right:2px;" onclick="DOPanel.refresh()" title="Refresh"><i class="ph ph-arrows-clockwise" id="doPanelRefreshIcon"></i></button>
            <button class="do-panel-close" onclick="DOPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-panel-actions">
            <div class="do-panel-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span id="doPanelMeta">—</span></div>
            <div class="do-panel-tabs">
                <button class="do-panel-tab active" data-s="all" onclick="DOPanel.filterSent('all')">Semua</button>
                <button class="do-panel-tab pos"    data-s="pos" onclick="DOPanel.filterSent('pos')">Pos</button>
                <button class="do-panel-tab neg"    data-s="neg" onclick="DOPanel.filterSent('neg')">Neg</button>
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
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('twit','all')">X / Twitter <span class="do-plat-dot" style="background:#1d9bf0;"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('fb','all')">Facebook <span class="do-plat-dot" style="background:#1877f2;"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('instagram','all')">Instagram <span class="do-plat-dot" style="background:#e1306c;"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('youtube','all')">YouTube <span class="do-plat-dot" style="background:#ff0000;"></span></button>
        <button class="do-plat-btn" onclick="DOPanel.openPlatform('tiktok','all')">TikTok <span class="do-plat-dot" style="background:#111827;"></span></button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}" defer></script>

    <script>
    'use strict';

    const DOCfg = {
        pid: {{ $projectId ? (int)$projectId : 'null' }},
        sd:  '{{ $startDate }}',
        ed:  '{{ $endDate }}',
        primary: '#038047',
        colorMap: {
            'Mass Media':'#0284c7','Online News':'#0284c7',
            'X (Twitter)':'#1d9bf0','Facebook':'#1877f2',
            'Instagram':'#e1306c','YouTube':'#ff0000','TikTok':'#111827',
        },
        platMeta: {
            doc:       { label:'Online News',  color:'#0284c7' },
            twit:      { label:'X / Twitter',  color:'#1d9bf0' },
            fb:        { label:'Facebook',      color:'#1877f2' },
            instagram: { label:'Instagram',     color:'#e1306c' },
            youtube:   { label:'YouTube',       color:'#ff0000' },
            tiktok:    { label:'TikTok',        color:'#111827' },
            all:       { label:'All Media',     color:'#038047' },
            social:    { label:'Social Media',  color:'#038047' },
        },
        mediaKeyMap: {
            'Online News':'doc','Mass Media':'doc',
            'X (Twitter)':'twit','Facebook':'fb',
            'Instagram':'instagram','YouTube':'youtube','TikTok':'tiktok',
        },
    };

    const $      = id => document.getElementById(id);
    const numFmt = n => {
        var num = parseInt(n||0);
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };
    const numK = n => {
        n = parseInt(n || 0);
        return n.toLocaleString('id-ID');
    };
    const esc    = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const emptyHtml = m => '<div class="do-empty"><i class="ph ph-warning-circle"></i><span class="do-empty-txt">'+(m||'Tidak ada data')+'</span></div>';

    const _mediaNorm    = s => (s||'').toLowerCase().replace(/[\s()]/g,'');
    const _keyMapNorm   = Object.fromEntries(Object.entries(DOCfg.mediaKeyMap).map(function(kv){return[_mediaNorm(kv[0]),kv[1]];}));
    const _colorMapNorm = Object.fromEntries(Object.entries(DOCfg.colorMap).map(function(kv){return[_mediaNorm(kv[0]),kv[1]];}));
    const _resolveKey   = name => DOCfg.mediaKeyMap[name]||_keyMapNorm[_mediaNorm(name)]||'';
    const _resolveColor = name => DOCfg.colorMap[name]||_colorMapNorm[_mediaNorm(name)]||'';

    /* ══ ECharts registry ══ */
    const DOCharts = {
        _inst: {},
        make: function(id) {
            if (this._inst[id]) { try { this._inst[id].dispose(); } catch(e){} }
            var dom = $(id); if (!dom) return null;
            var c = echarts.init(dom, null, { renderer:'canvas' });
            this._inst[id] = c; return c;
        }
    };
    window.addEventListener('resize', function(){
        Object.keys(DOCharts._inst).forEach(function(k){
            var c = DOCharts._inst[k];
            try{ if(!c.isDisposed()) c.resize(); } catch(e){}
        });
    });

    var EC_TT = {
        backgroundColor:'#1e293b', borderColor:'#334155', borderWidth:1,
        padding:[9,13], textStyle:{color:'#fff',fontFamily:'inherit',fontSize:12},
        extraCssText:'border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.3);'
    };
    var getPrimary = function() { return DOCfg.primary; };

    /* ══ KPIs ══ */
    function _updateKPIs(news, social) {
        var total = news + social;
        var el  = function(id,val) { var e=$(id); if(e) e.textContent=numFmt(val); };
        var sub = function(id,txt) { var e=$(id); if(e) e.innerHTML=txt; };
        el('kpiTotal',total); el('kpiNews',news); el('kpiSocial',social); el('kpiPlatforms',6);
        sub('kpiTotalSub', '<i class="ph ph-chart-line-up me-1"></i>'+numFmt(total)+' total periode ini');
        sub('kpiNewsSub',  '<i class="ph ph-newspaper me-1"></i>'+(total>0?(news/total*100).toFixed(1):0)+'% dari total');
        sub('kpiSocialSub','<i class="ph ph-share-network me-1"></i>'+(total>0?(social/total*100).toFixed(1):0)+'% dari total');
        sub('kpiPlatformsSub','<i class="ph ph-circles-four me-1"></i>News + 5 social platforms');
    }

    /* ══ List Modals ══ */
    var DOListModal = {
        open: function(id) { $(id).classList.add('show'); document.body.style.overflow='hidden'; },
        close: function(id){ $(id).classList.remove('show'); document.body.style.overflow='auto'; },
        openTrending: function(topics) {
            var h='<table class="do-tbl"><thead><tr><th style="width:30px;">#</th><th>Topic</th></tr></thead><tbody>';
            topics.forEach(function(t,i){ var name=t.title||t.name||t.topic||'Unknown', url=t.reference||t.url||'#'; h+='<tr><td class="do-tbl-rank">'+(i+1)+'</td><td class="do-tbl-name">'+(url!=='#'?'<a href="'+url+'" target="_blank" class="topic-link">'+esc(name)+'</a>':esc(name))+'</td></tr>'; });
            h+='</tbody></table>'; $('trendingModalBody').innerHTML=h; this.open('doTrendingModal');
        },
        openHashtag: function(tags) {
            var h='<table class="do-tbl"><thead><tr><th style="width:30px;">#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>';
            tags.forEach(function(tag,i){ var name=tag.name||tag.hashtag||tag.tag||'?'; if(!name.startsWith('#')) name='#'+name; h+='<tr><td class="do-tbl-rank">'+(i+1)+'</td><td class="do-tbl-name" style="color:var(--primary);font-weight:700;">'+name+'</td><td class="do-tbl-num">'+parseInt(tag.size||tag.mention||tag.count||0).toLocaleString()+'</td></tr>'; });
            h+='</tbody></table>'; $('hashtagModalBody').innerHTML=h; this.open('doHashtagModal');
        }
    };
    window.addEventListener('click', function(e){ if(e.target===$('doHashtagModal')) DOListModal.close('doHashtagModal'); if(e.target===$('doTrendingModal')) DOListModal.close('doTrendingModal'); });

    /* ══════════════════════════════════════════════
       Slide Panel
    ══════════════════════════════════════════════ */
    var DOPanel = (function() {
        var PAGE_SINGLE=25, PAGE_MULTI=10;
        var SENT_NORM={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'};
        var _normSent = function(item) { return SENT_NORM[String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim()]||'neu'; };
        var _allItems=[], _filtered=[], _curSent='all', _curPlat=null, _curPlatForSent='all';
        var _curPage=0, _hasMore=false, _loadingMore=false;
        var _overrideSd=null, _overrideEd=null;

        function showPlatPicker(x,y,sent) {
            _curPlatForSent=sent||'all';
            var pp=$('doPlatPicker'); if(!pp) return;
            pp.querySelectorAll('.do-plat-btn').forEach(function(btn){ var m=(btn.getAttribute('onclick')||'').match(/openPlatform\('([^']+)'/); if(m) btn.setAttribute('onclick',"DOPanel.openPlatform('"+m[1]+"','"+_curPlatForSent+"')"); });
            var pw=180,ph=250,vw=window.innerWidth,vh=window.innerHeight;
            var left=x+10,top=y-10;
            if(left+pw>vw-8) left=x-pw-10; if(top+ph>vh-8) top=vh-ph-8; if(top<8) top=8;
            pp.style.left=left+'px'; pp.style.top=top+'px'; pp.classList.add('show');
        }
        function openPlatform(platform,sentiment){ $('doPlatPicker')&&$('doPlatPicker').classList.remove('show'); open(platform,sentiment||_curPlatForSent||'all'); }

        function open(platform, sentiment, sdOverride, edOverride) {
            sdOverride = sdOverride || null; edOverride = edOverride || null;
            _curPlat=platform; _curSent=sentiment||'all'; _curPage=0; _hasMore=false; _allItems=[]; _filtered=[];
            _overrideSd=sdOverride; _overrideEd=edOverride;
            var meta=DOCfg.platMeta[platform]||{label:platform,color:DOCfg.primary};
            DODetail.close();
            $('doPanelDot').style.background=meta.color;
            $('doPanelTitle').textContent=meta.label;
            var titleDate=sdOverride?(sdOverride===edOverride?sdOverride:sdOverride+' – '+edOverride):(DOCfg.sd+' – '+DOCfg.ed);
            $('doPanelMeta').textContent=titleDate;
            document.querySelectorAll('.do-panel-tab').forEach(function(t){t.classList.toggle('active',t.dataset.s===_curSent);});
            var ri=$('doPanelRefreshIcon');
            if(ri) ri.style.cssText='-webkit-animation:spin .7s linear infinite;animation:spin .7s linear infinite;display:inline-block;';
            var list=$('doPanelList');
            list.innerHTML='<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>';
            var overlay=$('doPanelOverlay'),panel=$('doSntPanel');
            overlay.classList.remove('hiding'); panel.classList.remove('hiding');
            overlay.classList.add('show'); panel.classList.add('show');
            _fetchPage(platform,0).then(function(result){ _allItems=result.items; _hasMore=result.hasMore; _filtered=_filterBySent(_allItems,_curSent); _render(list,_filtered,platform,meta.color); }).catch(function(err){ list.innerHTML='<div style="padding:50px 20px;text-align:center;color:var(--slate-400);font-size:13px;">Gagal memuat data<br><small>'+esc(err.message)+'</small></div>'; }).then(function(){ if(ri) ri.style.cssText=''; });
        }

        function refresh(){ if(_curPlat) open(_curPlat,_curSent,_overrideSd,_overrideEd); }

        function loadMore(){
            if(_loadingMore||!_hasMore) return; _loadingMore=true;
            var btn=document.getElementById('_doLMBtn'); if(btn){btn.textContent='Memuat…';btn.disabled=true;}
            _curPage++;
            _fetchPage(_curPlat,_curPage).then(function(result){
                _allItems=_allItems.concat(result.items); _hasMore=result.hasMore;
                var wrap=document.getElementById('_doLMWrap'); if(wrap) wrap.remove();
                var list=$('doPanelList'), meta=DOCfg.platMeta[_curPlat]||{color:DOCfg.primary};
                var newFil=_filterBySent(result.items,_curSent); _filtered=_filterBySent(_allItems,_curSent);
                if(newFil.length) list.insertAdjacentHTML('beforeend',newFil.map(function(it){return _renderItem(it,_curPlat,meta.color);}).join(''));
                if(_hasMore) list.insertAdjacentHTML('beforeend',_lmHtml());
                else list.insertAdjacentHTML('beforeend','<div style="padding:9px;text-align:center;font-size:10px;color:var(--slate-400);font-weight:600;border-top:1px dashed var(--slate-200);">✓ Semua mentions sudah dimuat</div>');
            }).catch(function(){ _curPage--; var wrap=document.getElementById('_doLMWrap'); if(wrap) wrap.remove(); $('doPanelList').insertAdjacentHTML('beforeend',_lmHtml()); }).then(function(){ _loadingMore=false; });
        }

        function close(){
            var overlay=$('doPanelOverlay'),panel=$('doSntPanel');
            panel.classList.add('hiding'); overlay.classList.add('hiding');
            setTimeout(function(){ panel.classList.remove('show','hiding'); overlay.classList.remove('show','hiding'); DODetail.close(); },240);
        }
        function closeByOverlay(){ close(); }
        function filterSent(sent){
            _curSent=sent; document.querySelectorAll('.do-panel-tab').forEach(function(t){t.classList.toggle('active',t.dataset.s===sent);});
            _filtered=_filterBySent(_allItems,sent); var meta=DOCfg.platMeta[_curPlat]||{color:DOCfg.primary};
            _render($('doPanelList'),_filtered,_curPlat,meta.color);
        }
        function _filterBySent(items,sent){ return sent==='all'?items:items.filter(function(i){return _normSent(i)===sent;}); }
        function _extractItems(d){
            if(Array.isArray(d&&d.data&&d.data.data)) return d.data.data;
            if(Array.isArray(d&&d.data)) return d.data;
            if(Array.isArray(d&&d.statuses)) return d.statuses;
            if(Array.isArray(d&&d.tweets)) return d.tweets;
            if(Array.isArray(d&&d.results)) return d.results;
            if(Array.isArray(d&&d.posts)) return d.posts;
            if(Array.isArray(d)) return d;
            if(d&&d.data&&typeof d.data==='object'&&!Array.isArray(d.data)){ var vals=Object.values(d.data); if(vals.length&&typeof vals[0]==='object') return vals; }
            return [];
        }
        function _fetchPage(platform,page){
            var isMulti=['all','social'].indexOf(platform)!==-1, size=isMulti?PAGE_MULTI:PAGE_SINGLE, start=page*size;
            if(isMulti){
                var plats = platform === 'all' ? ['doc','twit','fb','instagram','youtube','tiktok'] : ['twit','fb','instagram','youtube','tiktok'];
                return Promise.allSettled(plats.map(function(p){ return _fetchOnePage(p,start,size); })).then(function(res){
                    var all = res.reduce(function(acc,r){ return r.status==='fulfilled'?acc.concat(r.value.items):acc; },[]);
                    all.sort(function(a,b){ var da=new Date(a.date_created||a.created_at||0).getTime(), db=new Date(b.date_created||b.created_at||0).getTime(); return db-da; });
                    return { items: all, hasMore: res.some(function(r){ return r.status==='fulfilled'&&r.value.hasMore; }) };
                });
            }
            return _fetchOnePage(platform,start,size);
        }
        function _fetchOnePage(platform,start,size){
            var sd=_overrideSd||DOCfg.sd, ed=_overrideEd||DOCfg.ed;
            var fetchRows=size+1, q='project_id='+DOCfg.pid+'&start_date='+sd+'&end_date='+ed+'&rows='+fetchRows+'&start='+start;
            if(platform==='youtube'){
                var ytSubs=['postbylike','postbyview','postbydate','postbycomment',null];
                var tryYt = function(idx){
                    if(idx>=ytSubs.length) return Promise.resolve({items:[],hasMore:false});
                    var sub=ytSubs[idx];
                    var url=sub?'/mk/api/news/ytb-top-status?'+q+'&sub='+sub:'/mk/api/news/ytb-top-status?'+q;
                    return fetch(url).then(function(r){ if(!r.ok) return tryYt(idx+1); return r.json().then(function(d){ var raw=_extractItems(d); if(raw.length>0) return{items:raw.slice(0,size).map(function(i){return Object.assign({},i,{_platform:'youtube'});}),hasMore:raw.length>size}; return tryYt(idx+1); }); }).catch(function(){ return tryYt(idx+1); });
                };
                return tryYt(0);
            }
            if(platform==='instagram'){
                var igSubs=['postbylike','postbycomment','postbydate',null];
                var tryIg = function(idx){
                    if(idx>=igSubs.length) return Promise.resolve({items:[],hasMore:false});
                    var sub=igSubs[idx];
                    var url=sub?'/mk/api/news/ig-top-status?'+q+'&sub='+sub:'/mk/api/news/ig-top-status?'+q;
                    return fetch(url).then(function(r){ if(!r.ok) return tryIg(idx+1); return r.json().then(function(d){ var raw=_extractItems(d); if(raw.length>0) return{items:raw.slice(0,size).map(function(i){return Object.assign({},i,{_platform:'instagram'});}),hasMore:raw.length>size}; return tryIg(idx+1); }); }).catch(function(){ return tryIg(idx+1); });
                };
                return tryIg(0);
            }
            var eps={
                doc:    '/mk/api/news/mentions?'+q,
                twit:   '/mk/api/x/most-status?'+q+'&media=all&mention_type=view_all',
                fb:     '/mk/api/news/fb-top-status?'+q+'&sub=fblike',
                tiktok: '/mk/api/news/tiktok-top-status?'+q+'&sub=postbylike'
            };
            var url=eps[platform]; if(!url) return Promise.resolve({items:[],hasMore:false});
            var ctrl=new AbortController(), tid=setTimeout(function(){ctrl.abort();},30000);
            return fetch(url,{signal:ctrl.signal}).then(function(r){
                clearTimeout(tid);
                if(!r.ok) return {items:[],hasMore:false};
                return r.json().then(function(d){
                    var raw=_extractItems(d);
                    if(platform==='twit'&&raw.length===0){
                        return fetch('/mk/api/news/mentions?'+q+'&media_type=twit').then(function(r2){ return r2.json().then(function(d2){ raw=_extractItems(d2); return finalize(raw); }); }).catch(function(){ return finalize(raw); });
                    }
                    return finalize(raw);
                    function finalize(r){
                        if(platform==='doc'){
                            var filtered=r.filter(function(m){ var tc=String(m.tcode||'').toLowerCase(), mt=String(m.media_type||'').toLowerCase(); return !tc||!mt||tc==='berita'||mt==='berita'||mt==='doc'||mt==='news'||mt==='online'||mt==='article'||tc==='null'||mt==='null'; });
                            if(filtered.length>0) r=filtered;
                        }
                        return{items:r.slice(0,size).map(function(i){return Object.assign({},i,{_platform:platform});}),hasMore:r.length>size};
                    }
                });
            }).catch(function(){ clearTimeout(tid); return{items:[],hasMore:false}; });
        }
        function _lmHtml(){ return '<div id="_doLMWrap" style="padding:11px 14px;text-align:center;background:var(--slate-50);border-top:1px dashed var(--slate-200);"><button id="_doLMBtn" onclick="DOPanel.loadMore()" style="display:inline-flex;align-items:center;gap:5px;padding:6px 20px;background:var(--primary);color:#fff;border:none;border-radius:5px;font-size:11px;font-weight:700;cursor:pointer;font-family:inherit;-webkit-transition:filter .14s;transition:filter .14s;" onmouseover="this.style.filter=\'brightness(1.12)\'" onmouseout="this.style.filter=\'\'"><i class="ph ph-arrow-circle-down" style="font-size:13px;"></i> Muat Lebih Banyak</button></div>'; }
        function _renderItem(item,platform,accentColor){
            var plat=item._platform||platform, meta=DOCfg.platMeta[plat]||{label:plat,color:accentColor};
            var rawName=(function(){ if(plat==='fb') return item.from_name||item.page_name||null; if(plat==='instagram') return item.username||item.user_name||null; if(plat==='tiktok') return item.author_nickname||item.nickname||(item.author&&item.author.nickname)||null; if(plat==='youtube') return item.channel_title||item.channel_name||(item.snippet&&item.snippet.channelTitle)||null; if(plat==='twit'){ var ao=typeof item.author==='object'?item.author:(function(){ try{return JSON.parse(item.author||'{}');}catch(e){return{};} })(); return item.name||ao.name||ao.scr_name||item.author_name||null; } return null; })();
            var name=(rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'Unknown').trim();
            var dName=/^\d{10,}$/.test(name)?'User '+name.slice(-4):name;
            var rawH=(function(){ if(plat==='instagram') return item.username||''; if(plat==='twit'){ var ao=typeof item.author==='object'?item.author:(function(){ try{return JSON.parse(item.author||'{}');}catch(e){return{};} })(); return item.screen_name||item.author_scr_name||ao.scr_name||ao.username||''; } return item.author_scr_name||item.screen_name||item.username||''; })().trim();
            var handle=(function(){ if(!rawH) return ''; var w=['twit','instagram','tiktok'].indexOf(plat)!==-1?(rawH.startsWith('@')?rawH:'@'+rawH):rawH; return w.replace(/^@/,'').toLowerCase()===dName.toLowerCase()?'':w; })();
            var text=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,150);
            var ao=(function(){ if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return{};} })();
            var av=(item.avatar_url||item.profile_image_url||ao.image||item.author_image||item.profile_image||item.thumbnail||'').trim();
            var dt=(item.date_created||item.created_at||'').split('T')[0];
            var sent=_normSent(item), words=dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
            var ini=(words.length>=2?words[0][0]+words[words.length-1][0]:(words[0]&&words[0][0]||dName[0]||'?')).toUpperCase();
            var avHtml=(av&&av.startsWith('http'))?'<img src="'+esc(av)+'" onerror="this.style.display=\'none\';this.parentElement.textContent=\''+ini.replace(/['"]/g,'')+'\';"/>':ini;
            var enc=encodeURIComponent(JSON.stringify(item));
            var sentCls=sent==='pos'?'pos':sent==='neg'?'neg':'neu', sentLbl=sent==='pos'?'Pos':sent==='neg'?'Neg':'Neu';
            return '<div class="do-panel-item" onclick="DODetail.openEncoded(\''+enc+'\',\''+plat+'\')"><div class="do-panel-avatar" style="background:linear-gradient(135deg,'+meta.color+','+meta.color+'99);">'+avHtml+'</div><div class="do-panel-item-body"><div class="do-panel-author">'+esc(dName)+'</div>'+(handle?'<div class="do-panel-handle">'+esc(handle)+'</div>':'')+'<div class="do-panel-text">'+esc(text||'(tidak ada konten)')+'</div><div class="do-panel-footer"><span class="do-sent-badge do-sent-badge--'+sentCls+'">'+sentLbl+'</span><span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:'+meta.color+';flex-shrink:0;"></span><span style="font-size:10px;font-weight:600;color:'+meta.color+';">'+meta.label+'</span>'+(dt?'<span style="margin-left:auto;">'+dt+'</span>':'')+'</div></div></div>';
        }
        function _render(list,items,platform,accentColor){
            window._doCurrentSelection=items; window._doCurrentPlatform=platform;
            if(!items.length) list.innerHTML='<div style="padding:50px 20px;text-align:center;color:var(--slate-400);font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>';
            else list.innerHTML=items.map(function(it){ return _renderItem(it,platform,accentColor); }).join('');
            if(_hasMore) list.insertAdjacentHTML('beforeend',_lmHtml());
        }
        return{open:open,close:close,closeByOverlay:closeByOverlay,showPlatPicker:showPlatPicker,openPlatform:openPlatform,filterSent:filterSent,loadMore:loadMore,refresh:refresh};
    })();

    /* ══════════════════════════════════════════════
       Detail Panel
    ══════════════════════════════════════════════ */
    var DODetail = {
        openEncoded: function(enc,plat){ try{this.open(JSON.parse(decodeURIComponent(enc)),plat);}catch(e){} },
        open: function(item,platform){
            var panel=$('doDetailPanel'),body=$('doDetailBody'),title=$('doDetailTitle');
            if(!panel||!body) return;
            var truePlat = item._platform || platform;
            if(truePlat==='all' || truePlat==='doc' || !truePlat){
                var url = String(item.url||item.link||'').toLowerCase();
                var has=function(s){return url.indexOf(s)!==-1;};
                if(has('tiktok.com')) truePlat='tiktok';
                else if(has('youtube.com')||has('youtu.be')) truePlat='youtube';
                else if(has('instagram.com')) truePlat='instagram';
                else if(has('facebook.com')||has('fb.watch')) truePlat='fb';
                else if(has('twitter.com')||has('x.com')) truePlat='twit';
            }
            var meta=DOCfg.platMeta[truePlat]||DOCfg.platMeta[platform]||{label:truePlat==='all'?'All Media':truePlat,color:DOCfg.primary};
            var SENT_MAP={pos:'Positif',neg:'Negatif',neu:'Netral'};
            var SENT_BGS={pos:'do-dp2-sent--pos',neg:'do-dp2-sent--neg',neu:'do-dp2-sent--neu'};
            var rawS=String(item.class_sentiment||item.sentiment||'0').toLowerCase();
            var sent={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'}[rawS]||'neu';
            var name = (item.author_name || item.screen_name || item.publisher || item.source_name || item.name || item.author_nickname || item.nickname || item.username || '').trim();
            if(!name || name.toLowerCase()==='unknown'){
                name = (item.author_scr_name || (item.author&&item.author.unique_id) || (item.author&&item.author.nickname) || 'User').trim();
            }
            var handle=((truePlat==='instagram'?item.username:'')||item.author_scr_name||item.screen_name||item.username||item.unique_id||'').trim();
            var content=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
            var av=(item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||'').trim();
            var dt=item.date_created||item.created_at||'';
            title.textContent=name;
            var ini=(name[0]||'?').toUpperCase();
            var avHtml=(av&&av.startsWith('http'))?'<img src="'+esc(av)+'" onerror="this.parentElement.textContent=\''+ini+'\';">':ini;
            var dtFmt='';
            if(dt){ try{dtFmt=new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){dtFmt=dt.split('T')[0];} }
            var mediaHtml='';
            if(truePlat==='youtube'){
                var ytId=((item.url||item.link||'').match(/[?&]v=([a-zA-Z0-9_-]{11})/)||[])[1]||((item.url||'').match(/youtu\.be\/([a-zA-Z0-9_-]{11})/)||[])[1]||((item.url||'').match(/shorts\/([a-zA-Z0-9_-]{11})/)||[])[1]||item.video_id||item.youtube_id||'';
                var thumb=item.thumbnail||item.thumbnail_url||item.image_url||item.media_url||(ytId?'https://img.youtube.com/vi/'+ytId+'/hqdefault.jpg':'');
                if(ytId){ var eId='yt_'+ytId+'_'+Date.now(); mediaHtml='<div class="do-dp2-media do-dp2-media--video" id="'+eId+'" style="position:relative;cursor:pointer;border-radius:6px;overflow:hidden;background:#000;" onclick="document.getElementById(\''+eId+'\').innerHTML=\'<iframe width=\\\'100%\\\' height=\\\'280\\\' src=\\\'https://www.youtube.com/embed/'+ytId+'?autoplay=1&controls=1\\\' frameborder=\\\'0\\\' allow=\\\'accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture\\\' allowfullscreen></iframe>\'"><img src="'+esc(thumb)+'" style="width:100%;height:220px;object-fit:cover;display:block;" onerror="this.src=\'https://img.youtube.com/vi/'+ytId+'/mqdefault.jpg\'"><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.25);"><div style="width:52px;height:52px;background:#ff0000;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.4);"><i class="ph ph-play-fill" style="font-size:22px;color:#fff;margin-left:3px;"></i></div></div></div>'; }
                else if(thumb){ mediaHtml='<div class="do-dp2-media"><img src="'+esc(thumb)+'" onerror="this.parentElement.style.display=\'none\'" style="border-radius:6px;width:100%;max-height:220px;object-fit:cover;"></div>'; }
            } else if(truePlat==='tiktok'){
                var tid2=((item.url||item.link||'').match(/\/video\/(\d+)/)||[])[1]||item.video_id||item.aweme_id||'';
                var thumb2=item.thumbnail||item.cover||item.image_url||item.video_cover||item.media_url||'';
                if(tid2){ var eId2='tt_'+tid2+'_'+Date.now(); mediaHtml='<div id="'+eId2+'" style="position:relative;cursor:pointer;background:#111827;border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center;height:260px;" onclick="DODetail.loadTikTokEmbed(\''+eId2+'\',\''+tid2+'\')">'+(thumb2?'<img src="'+esc(thumb2)+'" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:.65;pointer-events:none;">':'')+'<div style="position:relative;z-index:2;width:56px;height:56px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.6);"><i class="ph ph-play-fill" style="font-size:24px;color:#111827;margin-left:3px;"></i></div><div style="position:absolute;bottom:8px;right:8px;background:#111827;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:3px;letter-spacing:.5px;">TIKTOK</div></div>'; }
                else if(thumb2){ mediaHtml='<div class="do-dp2-media"><img src="'+esc(thumb2)+'" onerror="this.parentElement.style.display=\'none\'" style="border-radius:6px;max-height:320px;object-fit:cover;width:100%;display:block;"></div>'; }
            } else if(truePlat==='instagram'){
                var igThumb=item.image_url||item.media_url||item.display_url||item.thumbnail||item.thumbnail_url||'';
                var isVid=(item.media_type||'').toLowerCase()==='video'||(item.product_type||'').toLowerCase()==='igtv'||(item.product_type||'').toLowerCase()==='reels'||String(item.item_type||'').indexOf('video')!==-1;
                if(igThumb) mediaHtml='<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;"><img src="'+esc(igThumb)+'" onerror="this.parentElement.style.display=\'none\'" style="width:100%;max-height:320px;object-fit:cover;display:block;">'+(isVid?'<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);border-radius:6px;"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:#e1306c;margin-left:3px;"></i></div></div>':'')+'</div>';
            } else if(truePlat==='fb'){
                var fbImg=item.image_url||item.thumbnail||item.media_url||item.picture||item.display_url||item.story_img||'';
                var fbVid=(item.type||'').indexOf('video')!==-1||!!item.video_id;
                if(fbImg) mediaHtml='<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;"><img src="'+esc(fbImg)+'" onerror="this.parentElement.style.display=\'none\'" style="width:100%;max-height:320px;object-fit:cover;display:block;">'+(fbVid?'<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);border-radius:6px;"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:#1877f2;margin-left:3px;"></i></div></div>':'')+'</div>';
            } else if(truePlat==='twit'){
                var twImg=item.image_url||item.media_url||item.thumbnail||item.display_url||(item.media&&item.media.media_url)||'';
                var twVid=String(item.media_type||'').toLowerCase()==='video'||String(item.type||'').toLowerCase()==='video';
                if(twImg) mediaHtml='<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;"><img src="'+esc(twImg)+'" onerror="this.parentElement.style.display=\'none\'" style="width:100%;max-height:320px;object-fit:cover;display:block;">'+(twVid?'<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);border-radius:6px;"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:#1d9bf0;margin-left:3px;"></i></div></div>':'')+'</div>';
            } else {
                var docImg=item.image_url||item.thumbnail||item.featured_image||item.banner_image||item.media_url||item.picture||'';
                if(docImg) mediaHtml='<div class="do-dp2-media" style="border-radius:6px;overflow:hidden;background:#e5e7eb;"><img src="'+esc(docImg)+'" onerror="this.parentElement.style.display=\'none\'" style="width:100%;max-height:260px;object-fit:cover;display:block;"></div>';
            }
            var statsMap={
                twit:[['Retweet',item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0],['Quote',item.num_quote||0]],
                fb:[['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]],
                instagram:[['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]],
                youtube:[['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||0]],
                tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]],
                doc:[['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]]
            };
            var stats=statsMap[truePlat]||statsMap[platform]||[];
            var statsHtml=stats.some(function(s){return parseInt(s[1])>0;})?'<div class="do-dp2-stats">'+stats.map(function(sv){ return '<div class="do-dp2-stat"><div class="do-dp2-stat-val">'+parseInt(sv[1]||0).toLocaleString()+'</div><div class="do-dp2-stat-lbl">'+sv[0]+'</div></div>'; }).join('')+'</div>':'';
            var handleDisp=handle&&!handle.replace('@','').toLowerCase().startsWith(name.toLowerCase().slice(0,4))?(handle.startsWith('@')?handle:'@'+handle):'';
            var sourceUrl=item.url||item.link||item.post_url||item.article_url||item.source_url||item.permalink||item.news_url||item.article_link||item.full_url||item.web_url||item.source||item.original_url||'';
            if(!sourceUrl){
                if(truePlat==='twit'){
                    var ao2=(function(){ if(typeof item.author==='object'&&item.author) return item.author; try{return JSON.parse(item.author||'{}');}catch(e){return{};} })();
                    var scr=(item.author_scr_name||item.screen_name||ao2.scr_name||ao2.username||'').replace(/^@/,'');
                    var sid=item.sub_id||item.tweet_id||item.id_str||item.id||'';
                    if(scr&&sid) sourceUrl='https://twitter.com/'+scr+'/status/'+sid; else if(scr) sourceUrl='https://twitter.com/'+scr;
                } else if(truePlat==='instagram'){
                    var sc=item.shortcode||item.code||item.media_id||'';
                    if(sc) sourceUrl='https://www.instagram.com/p/'+sc+'/'; else if(item.username) sourceUrl='https://www.instagram.com/'+item.username+'/';
                } else if(truePlat==='youtube'){
                    var yi=item.video_id||item.youtube_id||item.id||'';
                    if(yi) sourceUrl='https://www.youtube.com/watch?v='+yi; else if(item.channel_id) sourceUrl='https://www.youtube.com/channel/'+item.channel_id;
                } else if(truePlat==='tiktok'){
                    var ti=item.video_id||item.aweme_id||item.id||'';
                    var ni=item.author_nickname||item.nickname||item.unique_id||(item.author&&item.author.unique_id)||'';
                    if(ti&&ni) sourceUrl='https://www.tiktok.com/@'+ni+'/video/'+ti; else if(ti) sourceUrl='https://vm.tiktok.com/'+ti;
                } else if(truePlat==='fb'){
                    var pi=item.post_id||item.story_id||item.id||'';
                    var pn=item.page_name||item.from_name||item.username||'';
                    if(pn&&pi) sourceUrl='https://www.facebook.com/'+pn+'/posts/'+pi;
                }
            }
            var sourceBtnHtml = '';
            if (sourceUrl) {
                sourceBtnHtml = '<a href="'+esc(sourceUrl)+'" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out"></i> Lihat '+meta.label+' Asli</a>';
            } else {
                sourceBtnHtml = '<div style="margin-top:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:8px 12px;font-size:11px;color:#991b1b;display:flex;align-items:flex-start;gap:7px;line-height:1.4;"><i class="ph ph-warning-circle" style="font-size:15px;flex-shrink:0;"></i><span>Link artikel spesifik tidak tersedia dari sumber data.</span></div>';
            }
            body.innerHTML=
                '<div class="do-dp2-avatar-row">'+
                    '<div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,'+meta.color+','+meta.color+'99);">'+avHtml+'</div>'+
                    '<div>'+
                        '<div class="do-dp2-name">'+esc(name)+'</div>'+
                        (handleDisp?'<div class="do-dp2-handle">'+esc(handleDisp)+'</div>':'')+
                        '<span class="do-dp2-plat-badge" style="background:'+meta.color+'22;color:'+meta.color+';">'+meta.label+'</span>'+
                    '</div>'+
                '</div>'+
                (dtFmt?'<div class="do-dp2-meta"><span>'+dtFmt+'</span></div>':'')+
                '<div class="do-dp2-sent '+SENT_BGS[sent]+'">'+SENT_MAP[sent]+'</div>'+
                mediaHtml+
                (content?'<div class="do-dp2-content">'+esc(content)+'</div>':'')+
                statsHtml+
                sourceBtnHtml;
            panel.classList.add('show');
        },
        openDetail: function(idx){
            var item = (window._doCurrentSelection||[])[idx];
            if(item) this.open(item, (window._doCurrentPlatform||'all'));
        },
        close: function(){ $('doDetailPanel')&&$('doDetailPanel').classList.remove('show'); document.querySelectorAll('.do-detail-panel iframe').forEach(function(iframe){iframe.src=iframe.src;}); },
        loadTikTokEmbed: function(embedId,videoIdOrUrl){
            var el=$(embedId); if(!el) return;
            var tid='';
            if(/^\d+$/.test(videoIdOrUrl)){tid=videoIdOrUrl;}
            else{tid=((videoIdOrUrl.match(/\/video\/(\d+)/)||videoIdOrUrl.match(/\/v\/(\d+)/)||[])[1])||'';}
            if(!tid){window.open(videoIdOrUrl,'_blank');return;}
            el.style.cursor='default';el.style.minHeight='560px';el.style.height='auto';
            el.style.background='#111827';el.style.borderRadius='6px';el.style.overflow='hidden';
            el.innerHTML='<iframe src="https://www.tiktok.com/embed/v2/'+tid+'" width="100%" height="560" frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen style="display:block;border:none;border-radius:6px;background:#111827;"></iframe>';
        }
    };

    /* ══════════════════════════════════════════════
       Loader
    ══════════════════════════════════════════════ */
    var DOLoader = {
        loaded: new Set ? new Set() : {_s:[],has:function(v){return this._s.indexOf(v)!==-1;},add:function(v){this._s.push(v);}},
        _apexSentiment: null,

        init: function(){
            var self = this;
            if('IntersectionObserver' in window){
                var obs=new IntersectionObserver(function(entries){
                    entries.forEach(function(e){ if(e.isIntersecting){ var card=e.target,sec=card.dataset.lazy; if(!self.loaded.has(sec)){self.loaded.add(sec);self.load(sec);obs.unobserve(card);} } });
                },{rootMargin:'100px',threshold:.05});
                document.querySelectorAll('[data-lazy]').forEach(function(c){obs.observe(c);});
            } else {
                // Safari fallback: load all
                document.querySelectorAll('[data-lazy]').forEach(function(c){ self.load(c.dataset.lazy); });
            }
        },

        load: function(sec){
            var self = this;
            try{
                if(sec==='trending-topics') self.loadTrending();
                else if(sec==='top-hashtags') self.loadHashtags();
                else if(sec==='mention-combined') self.loadMentions();
                else if(sec==='sov') self.loadSov();
                else if(sec==='sentiment-timeline') self.loadSentLine();
                else if(sec==='buzzer-map') self.loadMap();
            } catch(err){ console.error('Error loading '+sec+':',err); }
        },

        loadTrending: function(){
            var self = this;
            fetch('/mk/api/trending-topics').then(function(r){return r.json();}).then(function(d){
                var body=$('trendingBody'), topics=d.data||[];
                if(!topics.length){body.innerHTML=emptyHtml();return;}
                if(topics.length>10) $('trendingHead').insertAdjacentHTML('beforeend','<button class="do-view-all" onclick="DOListModal.openTrending(window._doTopics)"><i class="ph ph-caret-right"></i>All</button>');
                window._doTopics=topics;
                var h='<table class="do-tbl"><thead><tr><th style="width:22px;">#</th><th>Topic</th></tr></thead><tbody>';
                topics.slice(0,10).forEach(function(t,i){ var name=t.title||t.name||t.topic||'Unknown', url=t.reference||t.url||'#'; h+='<tr><td class="do-tbl-rank">'+(i+1)+'</td><td class="do-tbl-name">'+(url!=='#'?'<a href="'+url+'" target="_blank" class="topic-link">'+esc(name)+'</a>':esc(name))+'</td></tr>'; });
                h+='</tbody></table>'; body.innerHTML=h;
            }).catch(function(e){ $('trendingBody').innerHTML=emptyHtml('Gagal memuat'); });
        },

        loadHashtags: function(){
            fetch('/mk/api/top-hashtags?project_id='+DOCfg.pid+'&start_date='+DOCfg.sd+'&end_date='+DOCfg.ed).then(function(r){return r.json();}).then(function(d){
                var body=$('hashtagBody');
                var tags=d.data&&Array.isArray(d.data.hashtags)?d.data.hashtags:(Array.isArray(d.data)?d.data:[]);
                if(!tags.length){body.innerHTML=emptyHtml();return;}
                if(tags.length>5) $('hashtagHead').insertAdjacentHTML('beforeend','<button class="do-view-all" onclick="DOListModal.openHashtag(window._doHashtags)"><i class="ph ph-caret-right"></i>All</button>');
                window._doHashtags=tags;
                var h='<table class="do-tbl"><thead><tr><th style="width:22px;">#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>';
                tags.slice(0,5).forEach(function(tag,i){ var name=tag.name||tag.hashtag||tag.tag||'?'; if(!name.startsWith('#')) name='#'+name; h+='<tr><td class="do-tbl-rank">'+(i+1)+'</td><td class="do-tbl-name" style="color:var(--primary);font-weight:700;">'+name+'</td><td class="do-tbl-num">'+parseInt(tag.size||tag.mention||tag.count||0).toLocaleString()+'</td></tr>'; });
                h+='</tbody></table>'; body.innerHTML=h;
            }).catch(function(){ $('hashtagBody').innerHTML=emptyHtml('Gagal memuat'); });
        },

        loadMentions: function(){
            fetch('/mk/api/mention-counts?project_id='+DOCfg.pid+'&start_date='+DOCfg.sd+'&end_date='+DOCfg.ed).then(function(r){return r.json();}).then(function(d){
                var social=Number(d.social||0), news=Number(d.news||0), total=social+news;
                $('mentionNewsVal').textContent=numFmt(news);
                $('mentionSocialVal').textContent=numFmt(social);
                $('mentionTotalVal').textContent=numFmt(total);
                var newsPct=total>0?(news/total*100).toFixed(1)+'%':'';
                var socialPct=total>0?(social/total*100).toFixed(1)+'%':'';
                var epNews=$('mentionNewsPct'), epSoc=$('mentionSocialPct');
                if(epNews) epNews.textContent=newsPct;
                if(epSoc)  epSoc.textContent=socialPct;
                $('mentionSkelWrap').style.display='none';
                $('mentionBody').style.display='-webkit-flex';
                $('mentionBody').style.display='flex';
                $('statNewsRow').onclick=function(){DOPanel.open('doc','all');};
                $('statSocialRow').onclick=function(e){DOPanel.showPlatPicker(e.clientX,e.clientY,'all');};
                $('statTotalRow').onclick=function(){DOPanel.open('all','all');};
                _updateKPIs(news,social);
                if(total>0){
                    var chart=DOCharts.make('chMentionPie');
                    if(chart){
                        var primary=getPrimary(), totalAll=total;
                        var labels=['Online News','Social Media'], counts=[news,social], colors=['#0284c7',primary];
                        chart.setOption({
                            animation:true,animationDuration:800,animationEasing:'cubicOut',backgroundColor:'transparent',
                            tooltip:Object.assign({},EC_TT,{trigger:'item',confine:true,formatter:function(p){
                                var pct=totalAll>0?(p.value/totalAll*100):0;
                                return '<div style="font-weight:700;font-size:13px;margin-bottom:5px;">'+p.name+'</div><div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">'+numFmt(p.value)+'</span></div><div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">'+(pct<1&&pct>0?'<1':pct.toFixed(1))+'%</span></div>';
                            }}),
                            legend:{show:false},
                            series:[{
                                type:'pie',
                                /* ══ KEY FIX: smaller radius + offset center gives room for full labels ══ */
                                radius:['36%','54%'],
                                center:['50%','52%'],
                                avoidLabelOverlap:true,
                                minAngle:6,
                                itemStyle:{borderColor:'#fff',borderWidth:3,borderRadius:5},
                                label:{
                                    show:true,
                                    fontFamily:'inherit',
                                    /* Use 'none' alignTo so ECharts places labels freely without edge-clipping */
                                    alignTo:'none',
                                    distanceToLabelLine:6,
                                    /* Full names — no shortening */
                                    formatter:function(p){
                                        var pc=totalAll>0?(p.value/totalAll*100):0;
                                        return '{name|'+p.name+'}\n{pct|'+Math.round(pc)+'%}';
                                    },
                                    rich:{
                                        name:{fontWeight:'700',fontSize:10,color:'#374151',lineHeight:17},
                                        pct:{fontWeight:'800',fontSize:11,color:primary,lineHeight:15,
                                            backgroundColor:'#f0faf5',borderRadius:4,padding:[1,5]}
                                    }
                                },
                                labelLine:{
                                    show:true,
                                    length:10,
                                    length2:14,
                                    smooth:false,
                                    lineStyle:{color:'#CBD5E1',width:1.2}
                                },
                                emphasis:{
                                    scale:true,scaleSize:6,
                                    itemStyle:{shadowBlur:14,shadowColor:'rgba(0,0,0,.18)'},
                                    label:{show:true,fontFamily:'inherit',formatter:function(p){
                                        var pc=totalAll>0?p.value/totalAll*100:0;
                                        return '{n|'+p.name+'}\n{v|'+numK(p.value)+'}\n{p|'+pc.toFixed(1)+'%}';
                                    },rich:{
                                        n:{fontSize:10,color:'#94A3B8',fontWeight:'600',lineHeight:14},
                                        v:{fontSize:15,color:'#0F172A',fontWeight:'700',lineHeight:20},
                                        p:{fontSize:10,color:primary,fontWeight:'800',lineHeight:14}
                                    }}
                                },
                                data:labels.map(function(lb,i){return{name:lb,value:counts[i],itemStyle:{color:colors[i]}};})
                            }]
                        },true);
                        chart.on('click',function(p){ if(p.name==='Online News') DOPanel.open('doc','all'); else DOPanel.showPlatPicker(window.innerWidth/2,window.innerHeight/2,'all'); });
                        chart.on('mouseover',function(p){if(p.componentType==='series') chart.getDom().style.cursor='pointer';});
                        chart.on('mouseout',function(){chart.getDom().style.cursor='default';});
                    }
                }
            }).catch(function(e){ console.error('[loadMentions]',e); });
        },

 loadSov: function(){
            fetch('/mk/api/sentiment-by-media?project_id='+DOCfg.pid+'&start_date='+DOCfg.sd+'&end_date='+DOCfg.ed).then(function(r){return r.json();}).then(function(d){
                var data=d.data||[];
                var sovSkel=$('sovSkel'), sovBody=$('sovBody');
                if(sovSkel) sovSkel.style.display='none';
                if(sovBody){ sovBody.style.display='-webkit-flex'; sovBody.style.display='flex'; }

                if(!data.length){
                    if(sovBody) sovBody.innerHTML='<div style="padding:40px;width:100%;">'+emptyHtml('Tidak ada data Share of Voice')+'</div>';
                    return;
                }

                var fallbackColors=['#0284c7','#1d9bf0','#1877f2','#e1306c','#ff0000','#111827','#7c3aed','#f59e0b'];
                var totalAll=data.reduce(function(s,m){return s+(m.total||0);},0);
                var labels=data.map(function(m){return m.media;});
                var counts=data.map(function(m){return m.total||0;});
                var colors=data.map(function(m,i){return _resolveColor(m.media)||fallbackColors[i%fallbackColors.length];});
                var primary=getPrimary();

                var dispName = function(n){
                    return n.replace('X (Twitter)','X / Twitter').replace('Mass Media','Online News');
                };

                var chart=DOCharts.make('chSovPie'); if(!chart) return;
                chart.setOption({
                    animation:true,animationDuration:800,animationEasing:'cubicOut',backgroundColor:'transparent',
                    tooltip:Object.assign({},EC_TT,{trigger:'item',confine:true,formatter:function(p){
                        var pct=totalAll>0?(p.value/totalAll*100):0;
                        return '<div style="font-weight:700;font-size:13px;margin-bottom:5px;">'+dispName(p.name)+'</div>'+
                               '<div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Mentions</span><span style="font-weight:700;">'+numFmt(p.value)+'</span></div>'+
                               '<div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">'+pct.toFixed(1)+'%</span></div>';
                    }}),
                    legend:{show:false},
                    series:[{
                        type:'pie',
                        /*
                         * Donut kecil + center geser ke kanan sedikit
                         * → sisi kiri punya ruang lebih lebar untuk label yang bergerombol
                         */
                        radius:['28%','44%'],
                        center:['52%','50%'],
                        avoidLabelOverlap:true,
                        minAngle:8,
                        padAngle:1.5,
                        itemStyle:{borderColor:'#fff',borderWidth:2,borderRadius:5},
                        label:{
                            show:true,
                            fontFamily:'inherit',
                            /*
                             * alignTo:'edge' → semua label dikumpulkan ke tepi kiri/kanan
                             * container, lalu disambung garis panjang.
                             * Hasilnya label tidak berdempet meski slicenya kecil.
                             */
                            alignTo:'edge',
                            edgeDistance:'8%',
                            distanceToLabelLine:4,
                            formatter:function(p){
                                var pc=totalAll>0?(p.value/totalAll*100):0;
                                return '{name|'+dispName(p.name)+'}\n{pct|'+Math.round(pc)+'%}';
                            },
                            rich:{
                                name:{fontWeight:'700',fontSize:10,color:'#374151',lineHeight:16},
                                pct:{fontWeight:'800',fontSize:11,color:primary,lineHeight:14,
                                    backgroundColor:'#f0faf5',borderRadius:4,padding:[1,5]}
                            }
                        },
                        labelLine:{
                            show:true,
                            length:14,
                            length2:20,
                            smooth:true,
                            lineStyle:{color:'#CBD5E1',width:1.2}
                        },
                        emphasis:{
                            scale:true,scaleSize:5,
                            itemStyle:{shadowBlur:16,shadowColor:'rgba(0,0,0,.15)'},
                            label:{
                                show:true,
                                fontFamily:'inherit',
                                alignTo:'edge',
                                edgeDistance:'8%',
                                formatter:function(p){
                                    var pc=totalAll>0?(p.value/totalAll*100):0;
                                    return '{n|'+dispName(p.name)+'}\n{v|'+numK(p.value)+'}\n{p|'+pc.toFixed(1)+'%}';
                                },
                                rich:{
                                    n:{fontSize:10,color:'#94A3B8',fontWeight:'600',lineHeight:14},
                                    v:{fontSize:15,color:'#0F172A',fontWeight:'700',lineHeight:20},
                                    p:{fontSize:10,color:primary,fontWeight:'800',lineHeight:14}
                                }
                            }
                        },
                        data:labels.map(function(lb,i){
                            return {
                                name:lb,
                                value:counts[i],
                                itemStyle:{color:colors[i]}
                            };
                        })
                    }]
                },true);
                chart.on('click',function(p){ var k=_resolveKey(p.name); if(k) DOPanel.open(k,'all'); });
                chart.on('mouseover',function(p){if(p.componentType==='series') chart.getDom().style.cursor='pointer';});
                chart.on('mouseout',function(){chart.getDom().style.cursor='default';});
            }).catch(function(e){ console.error('[loadSov]',e); });
        },

        loadSentLine: function(){
            var self = this;
            var skEl=$('skSentiment');
            fetch('/mk/api/sentiment-timeline?project_id='+DOCfg.pid+'&start_date='+DOCfg.sd+'&end_date='+DOCfg.ed).then(function(r){return r.json();}).then(function(d){
                var dates    = d.dates             || [];
                var datesEnd = d.dates_end         || dates;
                var valTotal = d.values            || [];
                var valPos   = (d.sentiment&&d.sentiment.positive) || [];
                var valNeu   = (d.sentiment&&d.sentiment.neutral)  || [];
                var valNeg   = (d.sentiment&&d.sentiment.negative) || [];
                var mainEl=$('chSentiment');
                if(!mainEl) return;
                if(!dates.length||valTotal.every(function(v){return !v;})){
                    if(skEl) skEl.style.display='none';
                    mainEl.style.display='block';
                    mainEl.innerHTML='<div class="chart-empty"><i class="ph ph-chart-line-up"></i><span>Tidak ada data sentimen untuk periode ini</span></div>';
                    return;
                }
                if(self._apexSentiment){ try{self._apexSentiment.destroy();}catch(e){} self._apexSentiment=null; }
                var n=dates.length;
                mainEl.style.width = '';
                mainEl.style.display='block';
                mainEl.innerHTML='';
                var labels=dates.map(function(dt){
                    try {
                        var d2 = new Date(dt + 'T00:00:00');
                        return d2.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' });
                    } catch (e) { return dt; }
                });
                var toNum=function(arr){return arr.map(function(v){return Number(v||0);});};
                var seriesArr=[
                    {name:'Total',    data:toNum(valTotal)},
                    {name:'Positive', data:toNum(valPos)  },
                    {name:'Negative', data:toNum(valNeg)  },
                    {name:'Neutral',  data:toNum(valNeu)  },
                ];
                var opts = {
                    chart: {
                        type: 'area',
                        height: 350,
                        animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1000 } },
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                        events: {
                            click: function(_e, _ctx, cfg) {
                                var sd = null, ed = null;
                                if (cfg && typeof cfg.dataPointIndex !== 'undefined' && cfg.dataPointIndex >= 0) {
                                    sd = dates[cfg.dataPointIndex];
                                    ed = datesEnd[cfg.dataPointIndex] || sd;
                                }
                                if (cfg && cfg.seriesIndex >= 0) {
                                    var sentMap = { Total: 'all', Positive: 'pos', Neutral: 'neu', Negative: 'neg' };
                                    DOPanel.open('all', sentMap[seriesArr[cfg.seriesIndex]&&seriesArr[cfg.seriesIndex].name] || 'all', sd, ed);
                                } else {
                                    DOPanel.open('all', 'all', sd, ed);
                                }
                            },
                            mounted: function() {
                                if (skEl) { skEl.style.display = 'none'; setTimeout(function(){ try { skEl.remove(); } catch (e) {} }, 260); }
                            },
                        },
                    },
                    series: seriesArr,
                    stroke: { curve: 'smooth', width: 2.5 },
                    grid: {
                        borderColor: '#F1F5F9',
                        strokeDashArray: 4,
                        padding: { left: 10, right: 10 }
                    },
                    markers: {
                        size: n <= 31 ? 5 : 3,
                        strokeWidth: 2,
                        strokeColors: '#fff',
                        hover: { size: 6 }
                    },
                    dataLabels: {
                        enabled: n <= 31,
                        formatter: function(v) { return v > 0 ? numK(v) : ''; },
                        offsetY: -8,
                        style: { fontSize: '9px', fontFamily: 'inherit', fontWeight: '800' },
                        background: {
                            enabled: true, foreColor: '#fff', padding: 3,
                            borderRadius: 3, borderWidth: 0, opacity: 0.9,
                            dropShadow: { enabled: true, top: 1, left: 1, blur: 2, color: '#000', opacity: 0.15 }
                        }
                    },
                    xaxis: {
                        categories: labels,
                        labels: {
                            show: true,
                            rotate: -45,
                            rotateAlways: true,
                            hideOverlappingLabels: false,
                            showDuplicates: false,
                            trim: false,
                            style: { colors: '#94A3B8', fontSize: '10px', fontFamily: 'inherit' }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: {
                        labels: { formatter: function(v) { return numK(v); }, style: { colors: '#94A3B8', fontSize: '10px', fontFamily: 'inherit' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    colors: ['#4680ff', '#10B981', '#EF4444', '#94A3B8'],
                    fill: { type: 'solid', opacity: 0.3 },
                    legend: { position: 'bottom', horizontalAlign: 'left', labels: { colors: '#94A3B8' } },
                    tooltip: {
                        shared: false,
                        intersect: true,
                        y: { formatter: function(v) { return numFmt(v) + ' mentions'; } }
                    }
                };
                self._apexSentiment=new ApexCharts(mainEl,opts);
                self._apexSentiment.render();
            }).catch(function(err){
                console.error('[loadSentLine]',err);
                if(skEl) skEl.style.display='none';
                var el=$('chSentiment');
                if(el){ el.style.display='block'; el.innerHTML='<div class="chart-empty"><i class="ph ph-warning-circle"></i><span>Gagal memuat chart: '+esc(err.message)+'</span></div>'; }
            });
        },

        loadMap: function(){
            var self = this;
            fetch('/mk/api/geo-users?project_id='+DOCfg.pid+'&start_date='+DOCfg.sd+'&end_date='+DOCfg.ed).then(function(r){return r.json();}).then(function(d){
                $('mapSkel').style.display='none';
                var rows=d.data||[], primary=getPrimary();
                var mapResult=self.renderMap('buzzMap',rows,primary);
                self.buildLocationPanel('buzzMapList',rows,mapResult);
            }).catch(function(e){ console.error('[loadMap]',e); });
        },
        renderMap: function(elId,rows,primary){
            var map=L.map(elId,{center:[-2.5,118],zoom:5,scrollWheelZoom:false});
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',{attribution:'© OpenStreetMap, © CARTO',subdomains:'abcd',maxZoom:19}).addTo(map);
            if(!rows.length) return{map:map,markerRefs:[]};
            var maxCount=Math.max.apply(null,rows.map(function(p){return parseInt(p.count||0);})), markerRefs=[];
            rows.forEach(function(p){
                var lat=parseFloat(p.latitude||0),lng=parseFloat(p.longitude||0);
                if(lat===0&&lng===0){markerRefs.push(null);return;}
                var name=p.name||'Unknown',count=parseInt(p.count||0);
                if(count>=10) L.circle([lat,lng],{radius:Math.max(5000,Math.min(Math.sqrt(count)*2500,50000)),fillColor:primary,color:primary,weight:1,opacity:.2,fillOpacity:Math.min(.15+(count/maxCount)*.4,.55)}).addTo(map);
                var pin=L.marker([lat,lng],{icon:L.divIcon({className:'',html:'<div style="width:12px;height:12px;background:'+primary+';border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>',iconSize:[12,12],iconAnchor:[6,6]})}).addTo(map).bindPopup('<div style="font-family:inherit;text-align:center;padding:6px;"><div style="font-weight:700;font-size:13px;color:#0f172a;margin-bottom:5px;">'+name+'</div><div style="font-size:20px;font-weight:800;color:'+primary+';">'+count.toLocaleString()+'</div><div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">mentions</div></div>');
                markerRefs.push({marker:pin,lat:lat,lng:lng});
                var lbl=count>999?(count/1000).toFixed(1)+'k':count;
                L.marker([lat,lng],{icon:L.divIcon({className:'',html:'<div style="font-family:inherit;font-size:10px;font-weight:800;color:#fff;background:'+primary+';padding:2px 7px;border-radius:3px;border:1.5px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);white-space:nowrap;">'+lbl+'</div>',iconSize:[36,18],iconAnchor:[18,24]}),interactive:false}).addTo(map);
            });
            return{map:map,markerRefs:markerRefs};
        },
        buildLocationPanel: function(listId,rows,mapResult){
            var listEl=$(listId); if(!listEl) return;
            var map=mapResult.map, markerRefs=mapResult.markerRefs;
            var valid=rows.filter(function(p){return!(parseFloat(p.latitude||0)===0&&parseFloat(p.longitude||0)===0);});
            if(!valid.length){listEl.innerHTML='<div class="do-empty" style="padding:20px 12px;font-size:11px;">No location data</div>';return;}
            var sorted=valid.slice().sort(function(a,b){return parseInt(b.count||0)-parseInt(a.count||0);});
            listEl.innerHTML=sorted.slice(0,8).map(function(p,rank){
                var name=p.name||'Unknown',count=parseInt(p.count||0);
                var lbl=count>999?(count/1000).toFixed(1)+'k':count;
                return '<div class="do-loc-item" data-name="'+name+'"><span class="do-loc-rank">'+(rank+1)+'</span><div class="do-loc-info"><div class="do-loc-name" title="'+name+'">'+name+'</div><div class="do-loc-count">'+lbl+' mentions</div></div><div class="do-loc-dot"></div></div>';
            }).join('');
            listEl.querySelectorAll('.do-loc-item').forEach(function(item){
                item.addEventListener('click',function(){
                    var name=item.dataset.name, target=valid.filter(function(p){return(p.name||'Unknown')===name;})[0];
                    if(!target) return;
                    var lat=parseFloat(target.latitude||0),lng=parseFloat(target.longitude||0); if(lat===0&&lng===0) return;
                    map.flyTo([lat,lng],8,{animate:true,duration:1});
                    var ref=markerRefs.filter(function(r){return r&&Math.abs(r.lat-lat)<.001&&Math.abs(r.lng-lng)<.001;})[0];
                    if(ref) setTimeout(function(){ref.marker.openPopup();},800);
                    listEl.querySelectorAll('.do-loc-item').forEach(function(i){i.classList.remove('active');}); item.classList.add('active');
                });
            });
        }
    };

    /* ══════════════════════════════════════════════
       DOExport
    ══════════════════════════════════════════════ */
    var DOExport=(function(){
        var _toastTimer=null;

        function _toast(msg,type,duration){
            type=type||'default'; duration=duration||3200;
            var t=$('doExportToast'),m=$('doExportToastMsg'),ico=$('doExportToastIcon');
            if(!t||!m) return;
            m.textContent=msg;
            t.className='export-toast show '+(type!=='default'?type:'');
            ico.className='ph '+({success:'ph-check-circle',error:'ph-x-circle',default:'ph-spinner'}[type]||'ph-spinner');
            clearTimeout(_toastTimer);
            _toastTimer=setTimeout(function(){t.classList.remove('show');},duration);
        }
        function _btnState(btn,loading){ if(!btn) return; btn.disabled=loading; btn.classList.toggle('exporting',loading); }
        function _freeze(){
            if(document.getElementById('__do_freeze')) return;
            var s=document.createElement('style'); s.id='__do_freeze';
            s.textContent='*{-webkit-animation:none!important;animation:none!important;-webkit-transition:none!important;transition:none!important;-webkit-animation-play-state:paused!important;animation-play-state:paused!important;}';
            document.head.appendChild(s);
            Object.keys(DOCharts._inst).forEach(function(k){ var c=DOCharts._inst[k]; if(c&&!c.isDisposed()){try{c.setOption({animation:false},false);}catch(e){}} });
        }
        function _unfreeze(){
            var s=document.getElementById('__do_freeze'); if(s) s.remove();
            Object.keys(DOCharts._inst).forEach(function(k){ var c=DOCharts._inst[k]; if(c&&!c.isDisposed()){try{c.setOption({animation:true},false);}catch(e){}} });
        }
        function _resizeAllCharts() {
            Object.keys(DOCharts._inst).forEach(function(k){ var c=DOCharts._inst[k]; try{ if(!c.isDisposed()) c.resize(); } catch(e){} });
        }
        function _doCapture(el,bg){
            return html2canvas(el,{
                scale: 2,
                useCORS: true,
                allowTaint: false,
                backgroundColor: bg || '#f1f5f9',
                logging: false,
                removeContainer: true,
                windowHeight: el.scrollHeight,
                height: el.scrollHeight,
                ignoreElements: function(e) { return e.hasAttribute('data-html2canvas-ignore'); }
            });
        }
        function _drawHeader(pdf,pW,pH,label,page,total){
            pdf.setFillColor(3,128,71); pdf.rect(0,0,pW,11,'F');
            pdf.setTextColor(255,255,255); pdf.setFontSize(9); pdf.setFont('helvetica','bold');
            pdf.text('SMADIMENT — Data Overview'+(label?' · '+label:''),10,7.5);
            var now=new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});
            pdf.setFontSize(7); pdf.setFont('helvetica','normal');
            pdf.text('Generated: '+now,pW-10,7.5,{align:'right'});
            pdf.setFontSize(7); pdf.setTextColor(148,163,184);
            pdf.text('Halaman '+page+' / '+total,pW/2,pH-3,{align:'center'});
        }
        function _fitCanvas(pdf,canvas,margin,pW,pH){
            var uw=pW-margin*2, uh=pH-14-10, r=Math.min(uw/canvas.width,uh/canvas.height);
            var dw=canvas.width*r, dh=canvas.height*r;
            pdf.addImage(canvas.toDataURL('image/png'),'PNG',margin+(uw-dw)/2,14,dw,dh);
        }
        function _sliceCanvas(pdf,canvas,margin,pW,pH,label){
            var uw=pW-margin*2, uh=pH-14-10, ratio=uw/canvas.width, sliceH=uh/ratio;
            var total=Math.max(1,Math.ceil((canvas.height*ratio)/uh));
            var srcY=0,page=1;
            while(srcY<canvas.height){
                if(page>1) pdf.addPage();
                _drawHeader(pdf,pW,pH,label,page,total);
                var srcSlice=Math.min(sliceH,canvas.height-srcY), dstH=srcSlice*ratio;
                var slice=document.createElement('canvas'); slice.width=canvas.width; slice.height=Math.ceil(srcSlice);
                slice.getContext('2d').drawImage(canvas,0,srcY,canvas.width,srcSlice,0,0,canvas.width,srcSlice);
                pdf.addImage(slice.toDataURL('image/png'),'PNG',margin,14,uw,dstH);
                srcY+=srcSlice; page++;
            }
        }
        var _stamp=function(){return new Date().toISOString().slice(0,10).replace(/-/g,'');};
        var _cardLabels={trending:'Trending Topics',hashtag:'Top Hashtag',mention:'Mention',sov:'Share of Voice',sentiment:'Sentiment Score',map:'Buzzer Map'};

        function run(type,btn){
            if(!window.html2canvas){_toast('html2canvas tidak tersedia','error');return;}
            if(type==='pdf'&&!(window.jspdf&&window.jspdf.jsPDF)){_toast('jsPDF tidak tersedia','error');return;}
            var btnPdf=$('doPageExpPdf'), btnImg=$('doPageExpImg');
            _btnState(btnPdf,true); _btnState(btnImg,true);
            _toast(type==='pdf'?'Menyiapkan PDF 2 halaman…':'Mengambil gambar…','default',99999);

            var restores=[];
            document.querySelectorAll('.do-body-scroll,.do-loc-list,.do-sov-stats').forEach(function(el){
                var orig={maxHeight:el.style.maxHeight,overflow:el.style.overflow,height:el.style.height};
                restores.push({el:el,orig:orig}); el.style.maxHeight='none'; el.style.overflow='visible'; el.style.height='auto';
            });
            window.scrollTo({ top:0 });

            setTimeout(function(){
                _resizeAllCharts();
                _freeze();
                requestAnimationFrame(function(){
                    requestAnimationFrame(function(){
                        setTimeout(function(){
                            var area=$('doPageExportArea'), mapCard=$('card-export-map');

                            if(type==='image'){
                                _doCapture(area,'#f1f5f9').then(function(canvasMain){
                                    _unfreeze(); restores.forEach(function(r){ Object.keys(r.orig).forEach(function(p){r.el.style[p]=r.orig[p];}); });
                                    var a=document.createElement('a'); a.download='data_overview_'+_stamp()+'.png'; a.href=canvasMain.toDataURL('image/png'); a.click();
                                    _toast('Gambar berhasil diunduh!','success');
                                }).catch(function(err){ _unfreeze(); restores.forEach(function(r){ Object.keys(r.orig).forEach(function(p){r.el.style[p]=r.orig[p];}); }); _toast('Export gagal: '+err.message,'error'); })
                                .then(function(){ _btnState(btnPdf,false); _btnState(btnImg,false); });
                                return;
                            }

                            if(mapCard) mapCard.style.visibility='hidden';
                            _doCapture(area,'#f1f5f9').then(function(canvasMain){
                                if(mapCard) mapCard.style.visibility='';
                                if(mapCard){
                                    setTimeout(function(){
                                        _doCapture(mapCard,'#ffffff').then(function(canvasMap){
                                            _finishPdf(canvasMain,canvasMap,restores,btnPdf,btnImg);
                                        }).catch(function(){ _finishPdf(canvasMain,null,restores,btnPdf,btnImg); });
                                    },200);
                                } else {
                                    _finishPdf(canvasMain,null,restores,btnPdf,btnImg);
                                }
                            }).catch(function(err){
                                _unfreeze(); if(mapCard) mapCard.style.visibility='';
                                restores.forEach(function(r){ Object.keys(r.orig).forEach(function(p){r.el.style[p]=r.orig[p];}); });
                                _toast('Export gagal: '+err.message,'error');
                                _btnState(btnPdf,false); _btnState(btnImg,false);
                            });
                        },400);
                    });
                });
            },350);
        }

        function _finishPdf(canvasMain,canvasMap,restores,btnPdf,btnImg){
            _unfreeze(); restores.forEach(function(r){ Object.keys(r.orig).forEach(function(p){r.el.style[p]=r.orig[p];}); });
            var jsPDF=window.jspdf&&window.jspdf.jsPDF;
            var pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});
            var pW1=pdf.internal.pageSize.getWidth(), pH1=pdf.internal.pageSize.getHeight();
            _drawHeader(pdf,pW1,pH1,'Overview',1,canvasMap?2:1);
            _fitCanvas(pdf,canvasMain,10,pW1,pH1);
            if(canvasMap){
                pdf.addPage([297,210]);
                var pW2=pdf.internal.pageSize.getWidth(), pH2=pdf.internal.pageSize.getHeight();
                _drawHeader(pdf,pW2,pH2,'Buzzer Map',2,2);
                _fitCanvas(pdf,canvasMap,10,pW2,pH2);
            }
            pdf.save('data_overview_'+_stamp()+'.pdf');
            _toast('PDF berhasil diunduh!','success');
            _btnState(btnPdf,false); _btnState(btnImg,false);
        }

        function runCard(areaId,cardKey,type,btn){
            if(!window.html2canvas){_toast('html2canvas tidak tersedia','error');return;}
            if(type==='pdf'&&!(window.jspdf&&window.jspdf.jsPDF)){_toast('jsPDF tidak tersedia','error');return;}
            _btnState(btn,true);
            _toast(type==='pdf'?'Menyiapkan PDF card…':'Mengambil gambar card…','default',99999);

            var area=document.getElementById(areaId);
            if(!area){ _toast('Area #'+areaId+' tidak ditemukan','error'); _btnState(btn,false); return; }
            var restores=[];
            area.querySelectorAll('.do-body-scroll,.do-loc-list,.do-sov-stats').forEach(function(el){
                var orig={maxHeight:el.style.maxHeight,overflow:el.style.overflow,height:el.style.height};
                restores.push({el:el,orig:orig}); el.style.maxHeight='none'; el.style.overflow='visible'; el.style.height='auto';
            });
            if(cardKey==='map'&&window.L){ try{Object.keys(window).filter(function(k){return window[k] instanceof L.Map;}).forEach(function(k){try{window[k].invalidateSize({animate:false});}catch(e){}});}catch(e){} }
            window.scrollTo({ top:0 });

            setTimeout(function(){
                _resizeAllCharts();
                _freeze();
                requestAnimationFrame(function(){
                    requestAnimationFrame(function(){
                        setTimeout(function(){
                            _doCapture(area,'#ffffff').then(function(canvas){
                                _unfreeze(); restores.forEach(function(r){ Object.keys(r.orig).forEach(function(p){r.el.style[p]=r.orig[p];}); });
                                var label=_cardLabels[cardKey]||cardKey, fname='data_overview_'+cardKey+'_'+_stamp();
                                if(type==='image'){
                                    var a=document.createElement('a'); a.download=fname+'.png'; a.href=canvas.toDataURL('image/png'); a.click();
                                    _toast('Gambar berhasil diunduh!','success');
                                } else {
                                    var jsPDF=window.jspdf&&window.jspdf.jsPDF;
                                    var landscape=cardKey==='map'||canvas.width>canvas.height*1.3;
                                    var pdf=new jsPDF({orientation:landscape?'landscape':'portrait',unit:'mm',format:'a4'});
                                    var pW=pdf.internal.pageSize.getWidth(), pH=pdf.internal.pageSize.getHeight();
                                    var uw=pW-10*2, uh=pH-14-10;
                                    var fitsOne=(canvas.height*(uw/canvas.width))<=uh;
                                    if(fitsOne){ _drawHeader(pdf,pW,pH,label,1,1); _fitCanvas(pdf,canvas,10,pW,pH); }
                                    else{ _sliceCanvas(pdf,canvas,10,pW,pH,label); }
                                    pdf.save(fname+'.pdf');
                                    _toast('PDF berhasil diunduh!','success');
                                }
                            }).catch(function(err){
                                _unfreeze(); restores.forEach(function(r){ Object.keys(r.orig).forEach(function(p){r.el.style[p]=r.orig[p];}); });
                                _toast('Export gagal: '+err.message,'error');
                            }).then(function(){ _btnState(btn,false); });
                        },400);
                    });
                });
            },350);
        }
        return{run:run,runCard:runCard};
    })();

    /* ══ Init ══ */
    document.addEventListener('mousedown',function(e){ var pp=$('doPlatPicker'); if(pp&&pp.classList.contains('show')&&!pp.contains(e.target)) pp.classList.remove('show'); });
    document.addEventListener('DOMContentLoaded',function(){
        var params = new URLSearchParams(window.location.search);
        var lsStart = null, lsEnd = null;
        try{ lsStart = localStorage.getItem('smadiment_g_start'); lsEnd = localStorage.getItem('smadiment_g_end'); }catch(e){}
        if (!params.get('start_date') && lsStart && lsEnd) {
            params.set('start_date', lsStart);
            params.set('end_date', lsEnd);
            window.location.search = params.toString();
            return;
        }
        DOLoader.init();
        document.addEventListener('keydown',function(e){ if(e.key==='Escape') DOPanel.close(); });
    });
    </script>
@endsection