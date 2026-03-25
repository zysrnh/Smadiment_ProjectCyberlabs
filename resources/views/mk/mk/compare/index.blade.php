@extends('mk.layouts.app')

@section('title', 'Compare Projects - SMADIMENT')

@section('styles')
    <style>
        /* ══ Design Tokens ══ */
        :root {
            --dash-primary: var(--bs-primary, #4361EE);
            --dash-primary-rgb: var(--bs-primary-rgb, 67, 97, 238);
            --dash-primary-lt: rgba(var(--dash-primary-rgb, 67, 97, 238), .10);
            --green: #10B981;
            --green-light: #ECFDF5;
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
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px) } to { opacity: 1; transform: translateY(0) } }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0 } to { transform: translateX(0); opacity: 1 } }
        @keyframes slideOutRight { from { transform: translateX(0); opacity: 1 } to { transform: translateX(100%); opacity: 0 } }
        @keyframes overlayIn { from { opacity: 0 } to { opacity: 1 } }
        @keyframes overlayOut { from { opacity: 1 } to { opacity: 0 } }
        @keyframes spin { to { transform: rotate(360deg) } }
        @keyframes shimmer { 0% { background-position: -200% 0 } 100% { background-position: 200% 0 } }
        @keyframes tagIn { from { transform: scale(.8); opacity: 0 } to { transform: scale(1); opacity: 1 } }
        @keyframes pulseP { 0%, 100% { box-shadow: 0 0 0 3px var(--dash-primary-lt) } 50% { box-shadow: 0 0 0 6px transparent } }
        @keyframes kpiShimmer { 0% { left: -100% } 100% { left: 150% } }

        .fade-up { animation: fadeUp .38s ease-out both; }
        .fade-up-d1 { animation-delay: .05s }
        .fade-up-d2 { animation-delay: .10s }

        /* ══ Project Selector ══ */
        .cmp-search-input { width: 100%; padding: 9px 14px; border: 1px solid var(--slate-200); border-radius: var(--radius-sm); font-family: inherit; font-size: 13px; background: var(--slate-50); color: var(--slate-800); outline: none; transition: all .2s; margin-bottom: 7px; }
        .cmp-search-input:focus { border-color: var(--dash-primary); background: #fff; box-shadow: 0 0 0 3px var(--dash-primary-lt); }
        .cmp-dropdown { border: 1px solid var(--slate-200); border-radius: var(--radius-sm); max-height: 200px; overflow-y: auto; background: #fff; box-shadow: var(--shadow-md); }
        .cmp-dropdown::-webkit-scrollbar { width: 4px; }
        .cmp-dropdown::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }
        .cmp-proj-opt { display: flex; align-items: center; gap: 10px; padding: 9px 13px; cursor: pointer; border-bottom: 1px solid var(--slate-50); transition: background .15s; font-size: 13px; color: var(--slate-800); }
        .cmp-proj-opt:last-child { border-bottom: none; }
        .cmp-proj-opt:hover { background: var(--slate-50); }
        .cmp-proj-opt input[type="checkbox"] { width: 15px; height: 15px; accent-color: var(--dash-primary); cursor: pointer; flex-shrink: 0; }
        .cmp-proj-opt-info { flex: 1; min-width: 0; }
        .cmp-proj-opt-name { font-weight: 600; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cmp-proj-opt-meta { font-size: 11px; color: var(--slate-400); margin-top: 2px; }
        .cmp-proj-opt-badge { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 20px; background: var(--dash-primary-lt); color: var(--dash-primary); flex-shrink: 0; }
        .cmp-tags { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 9px; min-height: 30px; }
        .cmp-tag { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; background: var(--dash-primary); color: #fff; border-radius: 20px; font-size: 12px; font-weight: 600; animation: tagIn .2s ease-out; }
        .cmp-tag button { background: none; border: none; color: rgba(255, 255, 255, .75); cursor: pointer; font-size: 14px; line-height: 1; padding: 0; display: flex; align-items: center; transition: color .15s; }
        .cmp-tag button:hover { color: #fff; }

        /* ══ Chart ══ */
        .chart-container { position: relative; }
        .chart-loading { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; background: #fff; z-index: 2; transition: opacity .3s; }
        .chart-loading.hidden { opacity: 0; pointer-events: none; }
        .spin-ring { width: 26px; height: 26px; border: 2.5px solid var(--slate-100); border-top-color: var(--dash-primary); border-radius: 50%; animation: spin .65s linear infinite; }
        .chart-loading span { font-size: 11px; font-weight: 600; color: var(--slate-400); }
        .chart-empty { min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; color: var(--slate-400); font-size: 12px; font-weight: 600; }
        .chart-empty i { font-size: 34px; color: var(--slate-300); display: block; }
        .sk-block { border-radius: 4px; background: linear-gradient(90deg, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }

        /* ══ Loading overlay ══ */
        .cmp-loading-overlay { position: fixed; inset: 0; background: rgba(255, 255, 255, .85); backdrop-filter: blur(4px); z-index: 9999; display: none; flex-direction: column; align-items: center; justify-content: center; gap: 16px; }
        .cmp-loading-overlay.show { display: flex; }
        .cmp-spin-lg { width: 44px; height: 44px; border: 3px solid var(--dash-primary-lt); border-top-color: var(--dash-primary); border-radius: 50%; animation: spin .8s linear infinite; }

        /* ══ Sentiment mini bars ══ */
        .sent-mini { display: flex; flex-direction: column; gap: 5px; margin-top: 12px; }
        .sent-mini-row { display: grid; grid-template-columns: 32px 1fr 36px; align-items: center; gap: 6px; font-size: 10px; font-weight: 700; }
        .sent-mini-track { height: 5px; border-radius: 3px; background: var(--slate-100); overflow: hidden; }
        .sent-mini-fill { height: 100%; border-radius: 3px; transition: width 1s ease-out; }

        /* ══ Ranking table ══ */
        .cmp-rank-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
        .cmp-rank-table th { padding: 9px 13px; text-align: left; font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .4px; border-bottom: 2px solid var(--slate-100); }
        .cmp-rank-table td { padding: 11px 13px; border-bottom: 1px solid var(--slate-50); vertical-align: middle; }
        .cmp-rank-table tbody tr:last-child td { border-bottom: none; }
        .cmp-rank-table tbody tr { cursor: pointer; transition: background .15s; }
        .cmp-rank-table tbody tr:hover td { background: var(--slate-50); }
        .rank-num { width: 26px; height: 26px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; background: var(--slate-100); color: var(--slate-500); }
        .rank-num.gold { background: #fef3c7; color: #b45309; }
        .rank-num.silver { background: #f1f5f9; color: #475569; }
        .rank-num.bronze { background: #fef0e7; color: #c2410c; }
        .bar-track { height: 7px; background: var(--slate-100); border-radius: 4px; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 4px; transition: width 1.2s cubic-bezier(.4, 0, .2, 1); }

        /* ══ Media table ══ */
        .cmp-media-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
        .cmp-media-table th { padding: 9px 13px; text-align: right; font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .4px; border-bottom: 2px solid var(--slate-100); white-space: nowrap; }
        .cmp-media-table th:first-child, .cmp-media-table th:nth-child(2) { text-align: left; }
        .cmp-media-table td { padding: 10px 13px; border-bottom: 1px solid var(--slate-50); text-align: right; color: var(--slate-700); }
        .cmp-media-table td:first-child { text-align: left; }
        .cmp-media-table td:nth-child(2) { text-align: left; font-weight: 700; }
        .cmp-media-table tbody tr { transition: background .15s; cursor: pointer; }
        .cmp-media-table tbody tr:last-child td { border-bottom: none; }
        .cmp-media-table tbody tr:hover td { background: var(--slate-50); }

        /* ══ Sentiment stacked ══ */
        .sent-stack-row { display: grid; grid-template-columns: 170px 1fr; gap: 14px; align-items: center; }
        @media(max-width:640px) { .sent-stack-row { grid-template-columns: 1fr; } }
        .sent-stack-bars { display: flex; height: 26px; border-radius: var(--radius-sm); overflow: hidden; }
        .sent-seg { display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: #fff; transition: width 1.2s ease-out; white-space: nowrap; overflow: hidden; }

        /* ══ Tab nav ══ */
        .cmp-tab-nav { display: flex; gap: 2px; background: var(--slate-100); border: 1px solid var(--slate-200); border-radius: var(--radius-sm); padding: 3px; }
        .cmp-tab-btn { padding: 5px 13px; border: none; border-radius: 3px; font-family: inherit; font-size: 11px; font-weight: 700; cursor: pointer; color: var(--slate-500); background: transparent; transition: all .15s; }
        .cmp-tab-btn.active { background: #fff; color: var(--dash-primary); box-shadow: 0 1px 4px rgba(0, 0, 0, .08); }

        /* ══ Slide Panel ══ */
        .do-panel-overlay { position: fixed; inset: 0; z-index: 9000; background: rgba(15, 23, 42, .45); backdrop-filter: blur(4px); display: none; }
        .do-panel-overlay.show { display: block; animation: overlayIn .22s ease-out; }
        .do-panel-overlay.hiding { animation: overlayOut .22s ease-out forwards; }
        .do-panel { position: fixed; top: 0; right: 0; bottom: 0; z-index: 9001; width: 480px; max-width: 100vw; background: #fff; display: none; flex-direction: column; border-left: 1px solid var(--slate-200); box-shadow: -8px 0 40px rgba(15, 23, 42, .16); }
        .do-panel.show { display: flex; animation: slideInRight .28s cubic-bezier(.4, 0, .2, 1); }
        .do-panel.hiding { animation: slideOutRight .24s cubic-bezier(.4, 0, .2, 1) forwards; }
        .do-panel-header { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--slate-200); background: var(--slate-50); flex-shrink: 0; }
        .do-panel-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
        .do-panel-title { font-size: 13px; font-weight: 700; color: var(--slate-900); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .do-panel-close { width: 28px; height: 28px; border-radius: var(--radius-sm); border: 1px solid var(--slate-200); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--slate-500); font-size: 16px; transition: all .14s; flex-shrink: 0; }
        .do-panel-close:hover { background: var(--red); border-color: var(--red); color: #fff; }
        .do-panel-actions { display: flex; align-items: center; gap: 7px; padding: 7px 12px; border-bottom: 1px solid var(--slate-200); background: #fff; flex-shrink: 0; }
        .do-panel-meta { flex: 1; font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .5px; display: flex; align-items: center; gap: 5px; }
        .do-panel-tabs { display: flex; background: var(--slate-100); border: 1px solid var(--slate-200); border-radius: var(--radius-sm); padding: 2px; gap: 2px; }
        .do-panel-tab { padding: 3px 9px; border-radius: 3px; border: none; background: transparent; font-size: 11px; font-weight: 700; cursor: pointer; transition: all .13s; color: var(--slate-500); font-family: inherit; }
        .do-panel-tab:hover { background: #fff; }
        .do-panel-tab.active { background: #fff; box-shadow: 0 1px 4px rgba(0, 0, 0, .08); }
        .do-panel-tab.active[data-s="all"] { color: var(--dash-primary); }
        .do-panel-tab.neg.active { color: #EF4444; }
        .do-panel-tab.pos.active { color: #10B981; }
        .do-panel-tab.neu.active { color: var(--slate-500); }
        .do-panel-list { overflow-y: auto; flex: 1; padding: 2px 0; min-height: 0; }
        .do-panel-list::-webkit-scrollbar { width: 4px; }
        .do-panel-list::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }
        .do-panel-item { display: flex; gap: 10px; padding: 10px 14px; border-bottom: 1px solid var(--slate-50); cursor: pointer; transition: background .1s; align-items: flex-start; }
        .do-panel-item:hover { background: #f0f9ff; }
        .do-panel-item:last-child { border-bottom: none; }
        .do-panel-avatar { width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; color: #fff; border: 1.5px solid var(--slate-200); overflow: hidden; }
        .do-panel-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .do-panel-item-body { flex: 1; min-width: 0; }
        .do-panel-author { font-size: 12px; font-weight: 700; color: var(--slate-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .do-panel-handle { font-size: 10px; color: var(--slate-400); font-weight: 500; margin-bottom: 2px; }
        .do-panel-text { font-size: 11px; color: var(--slate-600); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 4px; }
        .do-panel-footer { display: flex; align-items: center; gap: 5px; font-size: 10px; color: var(--slate-400); flex-wrap: wrap; }
        .do-sent-badge { padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
        .do-sent-badge--pos { background: #dbeafe; color: #1d4ed8; }
        .do-sent-badge--neg { background: #fee2e2; color: #991b1b; }
        .do-sent-badge--neu { background: var(--slate-100); color: var(--slate-500); }
        .do-panel-loading { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 12px; color: var(--slate-400); font-size: 13px; font-weight: 600; }
        .do-panel-spinner { width: 28px; height: 28px; border: 2.5px solid var(--slate-100); border-top-color: var(--dash-primary); border-radius: 50%; animation: spin .65s linear infinite; }
        .do-detail-panel { position: absolute; inset: 0; background: #fff; z-index: 5; display: none; flex-direction: column; animation: slideInRight .2s cubic-bezier(.4, 0, .2, 1); }
        .do-detail-panel.show { display: flex; }
        .do-dp2-header { display: flex; align-items: center; gap: 8px; padding: 12px 14px; background: var(--slate-50); border-bottom: 1px solid var(--slate-200); flex-shrink: 0; }
        .do-dp2-back { width: 28px; height: 28px; border-radius: var(--radius-sm); border: 1px solid var(--slate-200); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--slate-500); transition: all .13s; font-size: 14px; }
        .do-dp2-back:hover { background: var(--dash-primary-lt); color: var(--dash-primary); border-color: var(--dash-primary); }
        .do-dp2-title { font-size: 13px; font-weight: 700; color: var(--slate-900); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .do-dp2-body { overflow-y: auto; flex: 1; padding: 16px; }
        .do-dp2-body::-webkit-scrollbar { width: 4px; }
        .do-dp2-body::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }
        .do-dp2-avatar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
        .do-dp2-avatar-lg { width: 46px; height: 46px; border-radius: 50%; color: #fff; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; border: 2px solid var(--slate-200); overflow: hidden; flex-shrink: 0; }
        .do-dp2-avatar-lg img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .do-dp2-name { font-size: 14px; font-weight: 700; color: var(--slate-900); }
        .do-dp2-handle { font-size: 11px; color: var(--slate-400); font-weight: 500; }
        .do-dp2-plat-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 700; margin-top: 3px; }
        .do-dp2-meta { font-size: 11px; color: var(--slate-400); font-weight: 500; margin-bottom: 10px; }
        .do-dp2-sent { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 3px; font-size: 11px; font-weight: 700; margin-bottom: 10px; }
        .do-dp2-sent--pos { background: #dbeafe; color: #1d4ed8; }
        .do-dp2-sent--neg { background: #fee2e2; color: #991b1b; }
        .do-dp2-sent--neu { background: var(--slate-100); color: var(--slate-500); }
        .do-dp2-content { font-size: 12px; color: var(--slate-700); line-height: 1.7; margin-bottom: 12px; background: var(--slate-50); border-radius: var(--radius-sm); padding: 10px 12px; border: 1px solid var(--slate-200); word-break: break-word; }
        .do-dp2-media { border-radius: var(--radius-sm); overflow: hidden; margin-bottom: 10px; }
        .do-dp2-media img { width: 100%; max-height: 220px; object-fit: cover; display: block; }
        .do-dp2-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-bottom: 10px; }
        .do-dp2-stat { background: var(--slate-50); border-radius: var(--radius-sm); padding: 8px 10px; border: 1px solid var(--slate-200); text-align: center; }
        .do-dp2-stat-val { font-size: 14px; font-weight: 700; color: var(--slate-900); }
        .do-dp2-stat-lbl { font-size: 9px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .4px; margin-top: 1px; }
        .do-dp2-link { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 14px; background: var(--dash-primary); color: #fff; border-radius: var(--radius-sm); font-size: 12px; font-weight: 700; text-decoration: none; transition: filter .14s; margin-top: 4px; }
        .do-dp2-link:hover { filter: brightness(1.1); color: #fff; }

        /* ── Platform picker ── */
        .do-plat-picker { position: fixed; z-index: 20000; background: #fff; border: 1px solid var(--slate-200); border-radius: var(--radius); box-shadow: var(--shadow-lg); padding: 5px; min-width: 175px; font-family: inherit; display: none; animation: fadeUp .14s ease-out; }
        .do-plat-picker.show { display: block; }
        .do-plat-picker-head { padding: 4px 9px 6px; font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid var(--slate-100); margin-bottom: 3px; }
        .do-plat-btn { display: flex; align-items: center; gap: 7px; padding: 7px 10px; border-radius: var(--radius-sm); font-size: 12px; font-weight: 600; cursor: pointer; background: transparent; border: none; font-family: inherit; width: 100%; text-align: left; color: var(--slate-700); transition: background .12s; }
        .do-plat-btn:hover { background: var(--dash-primary-lt); color: var(--dash-primary); }
        .do-plat-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-left: auto; }

        /* ══════════════════════════════════════════════════════
           EXPORT STYLES — identik dengan TikTok Most Engagement
        ══════════════════════════════════════════════════════ */
        .page-export-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; background: #fff; border: 1px solid var(--slate-200); border-radius: var(--radius); padding: 9px 14px; margin-bottom: 20px; box-shadow: var(--shadow-sm); }
        .page-export-bar-left { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; color: var(--slate-600); }
        .page-export-bar-left i { font-size: 15px; color: #038047; }
        .page-export-bar-right { display: flex; gap: 8px; }

        .page-export-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: var(--radius-sm); font-size: 16px; cursor: pointer; transition: all .15s ease; border: 1.5px solid transparent; font-family: inherit; }
        .page-export-btn-pdf { background: #fff3f3; color: #dc2626; border-color: #fca5a5; }
        .page-export-btn-pdf:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
        .page-export-btn-img { background: rgba(3,128,71,.1); color: #038047; border-color: rgba(3,128,71,.3); }
        .page-export-btn-img:hover { background: #038047; color: #fff; border-color: #038047; }
        .page-export-btn:disabled { opacity: .55; cursor: not-allowed; pointer-events: none; }
        .page-export-btn .export-spinner { width: 13px; height: 13px; border: 2px solid currentColor; border-top-color: transparent; border-radius: 50%; animation: spin .65s linear infinite; display: none; }
        .page-export-btn.exporting .export-spinner { display: inline-block; }
        .page-export-btn.exporting .export-icon { display: none; }

        .card-exp-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: var(--radius-sm); font-size: 14px; cursor: pointer; flex-shrink: 0; transition: all .14s ease; border: 1px solid transparent; font-family: inherit; background: transparent; }
        .card-exp-btn-pdf { color: #dc2626; border-color: #fca5a5; background: #fff3f3; }
        .card-exp-btn-pdf:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
        .card-exp-btn-img { color: #038047; border-color: rgba(3,128,71,.3); background: rgba(3,128,71,.1); }
        .card-exp-btn-img:hover { background: #038047; color: #fff; border-color: #038047; }
        .card-exp-btn:disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }
        .card-exp-btn .export-spinner { width: 11px; height: 11px; border: 2px solid currentColor; border-top-color: transparent; border-radius: 50%; animation: spin .65s linear infinite; display: none; }
        .card-exp-btn.exporting .export-spinner { display: inline-block; }
        .card-exp-btn.exporting .export-icon { display: none; }

        .export-toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(20px); background: var(--slate-900); color: #fff; border-radius: var(--radius); padding: 10px 18px; font-size: 12px; font-weight: 600; box-shadow: var(--shadow-lg); z-index: 99999; opacity: 0; pointer-events: none; transition: opacity .22s ease, transform .22s ease; display: flex; align-items: center; gap: 8px; white-space: nowrap; }
        .export-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
        .export-toast.success { background: #065f46; }
        .export-toast.error { background: #991b1b; }

        .cmp-page .do-filter-card .do-filter-group:first-child { display: none !important; }

        @media(max-width:640px) { .do-panel { width: 100vw; } }
    </style>
@endsection

@section('page-title', 'Compare Projects')

@section('content')

    <script>
        const CMP_PALETTE = ['#4361EE', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#10b981', '#ec4899', '#0ea5e9', '#f97316', '#6366f1'];
        const CMP_SENT_COLORS = { pos: '#10b981', net: '#64748b', neg: '#ef4444' };
        const CMP_MEDIA_LABELS = { doc: 'Online News', twit: 'Twitter', fb: 'Facebook', instagram: 'Instagram', youtube: 'YouTube', tiktok: 'TikTok' };
        const CMP_MEDIA_COLORS = { doc: '#0284c7', twit: '#1d9bf0', fb: '#1877f2', instagram: '#e1306c', youtube: '#ff0000', tiktok: '#111827' };
        const CMP_PLAT_META = {
            doc: { label: 'Online News', color: '#0284c7' },
            twit: { label: 'X / Twitter', color: '#1d9bf0' },
            fb: { label: 'Facebook', color: '#1877f2' },
            instagram: { label: 'Instagram', color: '#e1306c' },
            youtube: { label: 'YouTube', color: '#ff0000' },
            tiktok: { label: 'TikTok', color: '#111827' },
            all: { label: 'All Media', color: '#4361EE' },
        };
    </script>

    {{-- ══ Filter ══ --}}
    <div class="cmp-page">
        @include('mk.layouts.partials.filter-datepicker')
    </div>

    {{-- ══ Config Card ══ --}}
    <div class="card mb-3 fade-up">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-chart-bar f-18 text-primary"></i></div>
                <div><h6 class="mb-0">Compare Projects</h6><small class="text-muted">Pilih minimal 2 project untuk dibandingkan</small></div>
            </div>
            <span class="badge bg-light-secondary text-muted rounded-pill" id="cmpSelectedCount">0 selected</span>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-end-0"><i class="ph ph-magnifying-glass text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" id="cmpProjectSearch" placeholder="Search projects…" autocomplete="off">
                </div>
            </div>
            <div class="cmp-dropdown mb-3" id="cmpProjectDropdown">
                <div style="padding:16px;text-align:center;color:var(--slate-400);font-size:13px;">
                    <div class="spin-ring mx-auto mb-2" style="position:relative;"></div>Loading projects…
                </div>
            </div>
            <div class="cmp-tags mb-3" id="cmpSelectedTags">
                <span style="font-size:12px;color:var(--slate-400);align-self:center;">No projects selected</span>
            </div>
            <div class="d-flex justify-content-end border-top pt-3">
                <button class="btn btn-primary fw-bold px-4" id="cmpCompareBtn" onclick="cmpRunCompare()" disabled>
                    <i class="ph ph-chart-bar me-1"></i>Compare Now
                </button>
            </div>
        </div>
    </div>

    {{-- ══ Empty State ══ --}}
    <div id="cmpEmptyState">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ph ph-chart-bar d-block mb-3" style="font-size:48px;color:var(--slate-300);"></i>
                <h5 class="text-muted">Select projects to compare</h5>
                <p class="text-muted mb-0 f-12">Pick at least 2 projects, set a date range, and click <strong>Compare Now</strong></p>
            </div>
        </div>
    </div>

    {{-- ══ Results ══ --}}
    <div id="cmpResultsSection" style="display:none;">

        {{-- ════ PAGE EXPORT WRAPPER ════ --}}
        <div id="pageExportArea">

        {{-- Page Export Toolbar --}}
        <div class="page-export-bar" data-html2canvas-ignore="true">
            <div class="page-export-bar-left">
                <i class="ph ph-export"></i>
                <span>Export Hasil Perbandingan</span>
                <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">Cards + Charts + Tables</span>
            </div>
            <div class="page-export-bar-right">
                <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                        onclick="CmpExport.run('pdf', this)" title="Export sebagai PDF">
                    <i class="ph ph-file-pdf export-icon"></i>
                    <span class="export-spinner"></span>
                </button>
                <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                        onclick="CmpExport.run('image', this)" title="Export sebagai PNG">
                    <i class="ph ph-image export-icon"></i>
                    <span class="export-spinner"></span>
                </button>
            </div>
        </div>

        {{-- Project Summary Cards --}}
        <div class="row g-3 mb-3" id="cmpProjectsRow"></div>

        {{-- Volume Trend + Ranking ══ --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-8">
                <div class="card h-100">
                    <div id="card-export-volume">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-chart-line f-18 text-primary"></i></div>
                            <div><h6 class="mb-0">Volume Trend</h6><small class="text-muted">Klik bar/titik untuk lihat mentions</small></div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="cmp-tab-nav" id="cmpVolTabNav">
                                <button class="cmp-tab-btn active" onclick="cmpSwitchVolume('bar',this)">Bar</button>
                                <button class="cmp-tab-btn" onclick="cmpSwitchVolume('line',this)">Line</button>
                            </div>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="CmpExport.runCard('card-export-volume','volume','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="CmpExport.runCard('card-export-volume','volume','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="min-height:300px;">
                            <div class="chart-loading" id="cmpVolLoading"><div class="spin-ring"></div><span>Loading…</span></div>
                            <div id="cmpVolumeChart" style="min-height:300px;cursor:pointer;"></div>
                        </div>
                        <div id="cmpVolumeLegend" class="d-flex flex-wrap gap-3 mt-2"></div>
                    </div>
                    </div>{{-- /card-export-volume --}}
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card h-100">
                    <div id="card-export-ranking">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-ranking f-18 text-primary"></i></div>
                            <h6 class="mb-0">Total Volume</h6>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-muted">Klik baris untuk detail platform</small>
                            <div class="d-flex gap-1" data-html2canvas-ignore="true">
                                <button class="card-exp-btn card-exp-btn-pdf" onclick="CmpExport.runCard('card-export-ranking','ranking','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                                <button class="card-exp-btn card-exp-btn-img" onclick="CmpExport.runCard('card-export-ranking','ranking','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" id="cmpVolumeRanking"></div>
                    </div>{{-- /card-export-ranking --}}
                </div>
            </div>
        </div>

        {{-- Volume by Media ══ --}}
        <div class="card mb-3">
            <div id="card-export-media">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-table f-18 text-primary"></i></div>
                    <div><h6 class="mb-0">Volume by Media Types</h6><small class="text-muted">Klik sel untuk lihat mentions platform tersebut</small></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light-primary text-primary rounded-pill" id="cmpMediaDateBadge"></span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="CmpExport.runCard('card-export-media','media','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="CmpExport.runCard('card-export-media','media','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div style="overflow-x:auto;" id="cmpMediaTableWrap"></div>
            </div>
            <div class="card-body border-top">
                <p class="text-muted mb-2" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Volume Distribution by Platform</p>
                <div class="chart-container" style="min-height:260px;">
                    <div class="chart-loading" id="cmpMediaLoading"><div class="spin-ring"></div><span>Loading…</span></div>
                    <div id="cmpMediaChart" style="min-height:260px;cursor:pointer;"></div>
                </div>
            </div>
            </div>{{-- /card-export-media --}}
        </div>

        {{-- Share of Voice ══ --}}
        <div class="card mb-3">
            <div id="card-export-sov">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-pie-chart f-18 text-primary"></i></div>
                    <div><h6 class="mb-0">Share of Voice</h6><small class="text-muted">% share per project per media — klik slice untuk lihat mentions</small></div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div id="cmpSovTabNav" class="cmp-tab-nav" style="flex-wrap:wrap;gap:2px;"></div>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="CmpExport.runCard('card-export-sov','sov','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="CmpExport.runCard('card-export-sov','sov','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div class="card-body"><div id="cmpSovGrid"></div></div>
            </div>{{-- /card-export-sov --}}
        </div>

        {{-- Sentiment Comparison ══ --}}
        <div class="card mb-3">
            <div id="card-export-sentiment">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-smiley f-18 text-primary"></i></div>
                    <div><h6 class="mb-0">Sentiment Comparison</h6><small class="text-muted">Klik bar atau label untuk lihat mentions per sentimen</small></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="cmp-tab-nav">
                        <button class="cmp-tab-btn active" onclick="cmpSwitchSent('bar',this)">Bar</button>
                        <button class="cmp-tab-btn" onclick="cmpSwitchSent('donut',this)">Donut</button>
                    </div>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="CmpExport.runCard('card-export-sentiment','sentiment','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="CmpExport.runCard('card-export-sentiment','sentiment','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="cmpSentBars"><div id="cmpSentGrid" style="display:flex;flex-direction:column;gap:14px;"></div></div>
                <div id="cmpSentDonut" style="display:none;"></div>
            </div>
            </div>{{-- /card-export-sentiment --}}
        </div>

        {{-- /pageExportArea --}}
        </div>

    </div>

    {{-- Export Toast --}}
    <div class="export-toast" id="exportToast">
        <i class="ph ph-check-circle" id="exportToastIcon"></i>
        <span id="exportToastMsg">Exporting…</span>
    </div>

    {{-- ══ Loading Overlay ══ --}}
    <div class="cmp-loading-overlay" id="cmpLoadingOverlay">
        <div class="cmp-spin-lg"></div>
        <div style="font-size:13px;font-weight:600;color:var(--slate-500);">Fetching comparison data…</div>
    </div>

    {{-- ══ Slide Panel ══ --}}
    <div class="do-panel-overlay" id="cmpPanelOverlay" onclick="CmpPanel.close()"></div>
    <div class="do-panel" id="cmpSntPanel">
        <div class="do-panel-header">
            <div class="do-panel-dot" id="cmpPanelDot"></div>
            <span class="do-panel-title" id="cmpPanelTitle">Mentions</span>
            <button class="do-panel-close" onclick="CmpPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-panel-actions">
            <div class="do-panel-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span id="cmpPanelMeta">—</span></div>
            <div class="do-panel-tabs">
                <button class="do-panel-tab active" data-s="all" onclick="CmpPanel.filterSent('all')">Semua</button>
                <button class="do-panel-tab neg" data-s="neg" onclick="CmpPanel.filterSent('neg')">Neg</button>
                <button class="do-panel-tab pos" data-s="pos" onclick="CmpPanel.filterSent('pos')">Pos</button>
                <button class="do-panel-tab neu" data-s="neu" onclick="CmpPanel.filterSent('neu')">Neu</button>
            </div>
        </div>
        <div class="do-panel-list" id="cmpPanelList"></div>
        <div class="do-detail-panel" id="cmpDetailPanel">
            <div class="do-dp2-header">
                <button class="do-dp2-back" onclick="CmpDetail.close()"><i class="ph ph-caret-left"></i></button>
                <span class="do-dp2-title" id="cmpDetailTitle">Detail</span>
                <button class="do-panel-close" onclick="CmpPanel.close()"><i class="ph ph-x"></i></button>
            </div>
            <div class="do-dp2-body" id="cmpDetailBody"></div>
        </div>
    </div>

    {{-- Platform Picker ══ --}}
    <div class="do-plat-picker" id="cmpPlatPicker">
        <div class="do-plat-picker-head">Pilih Platform</div>
        <button class="do-plat-btn" onclick="CmpPanel.openPlatform('doc','all')">Online News <span class="do-plat-dot" style="background:#0284c7;"></span></button>
        <button class="do-plat-btn" onclick="CmpPanel.openPlatform('twit','all')">X / Twitter <span class="do-plat-dot" style="background:#1d9bf0;"></span></button>
        <button class="do-plat-btn" onclick="CmpPanel.openPlatform('fb','all')">Facebook <span class="do-plat-dot" style="background:#1877f2;"></span></button>
        <button class="do-plat-btn" onclick="CmpPanel.openPlatform('instagram','all')">Instagram<span class="do-plat-dot" style="background:#e1306c;"></span></button>
        <button class="do-plat-btn" onclick="CmpPanel.openPlatform('youtube','all')">YouTube <span class="do-plat-dot" style="background:#ff0000;"></span></button>
        <button class="do-plat-btn" onclick="CmpPanel.openPlatform('tiktok','all')">TikTok <span class="do-plat-dot" style="background:#111827;"></span></button>
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        'use strict';

        /* ══ GLOBALS ══ */
        let _cmpAllProjects = [];
        let _cmpSelectedIds = new Set();
        let _cmpData = null;
        let _cmpStartDate = '{{ $startDate }}';
        let _cmpEndDate = '{{ $endDate }}';
        let _cmpVolType = 'bar';
        let _cmpPickerPid = null;

        let _apexVol = null;
        let _apexMedia = null;
        const _apexSov = {};
        const _apexSent = {};

        const _$c = id => document.getElementById(id);
        const _esc = s => (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        const _trunc = (s, n) => s && s.length > n ? s.slice(0, n) + '…' : (s || '');
        const _fmtN = n => new Intl.NumberFormat('id-ID').format(n || 0);
        const _int = v => parseInt(v, 10) || 0;
        const numK = n => { n = _int(n); return n >= 1e6 ? (n / 1e6).toFixed(1) + 'M' : n >= 1000 ? (n / 1000).toFixed(1) + 'k' : String(n); };

        /* ══ INIT ══ */
        document.addEventListener('DOMContentLoaded', () => {
            cmpLoadProjects();
            @foreach($selectedIds ?? [] as $sid)
                _cmpSelectedIds.add('{{ $sid }}');
            @endforeach
            _$c('cmpProjectSearch').addEventListener('input', cmpFilterDropdown);
            document.addEventListener('mousedown', e => {
                const pp = _$c('cmpPlatPicker');
                if (pp?.classList.contains('show') && !pp.contains(e.target)) pp.classList.remove('show');
            });
        });

        /* ══ PROJECT LOADER ══ */
        async function cmpLoadProjects() {
            try {
                const res = await fetch('/mk/api/compare/projects');
                const data = await res.json();
                _cmpAllProjects = data.data || [];
                cmpRenderDropdown(_cmpAllProjects);
                if (_cmpSelectedIds.size > 0) { cmpUpdateTags(); cmpUpdateBtn(); }
            } catch (e) {
                _$c('cmpProjectDropdown').innerHTML = '<div style="padding:16px;text-align:center;color:#ef4444;font-size:13px;">Failed to load projects</div>';
            }
        }

        function cmpRenderDropdown(list) {
            const dd = _$c('cmpProjectDropdown');
            if (!list.length) { dd.innerHTML = '<div style="padding:16px;text-align:center;color:var(--slate-400);font-size:13px;">No projects found</div>'; return; }
            dd.innerHTML = list.map(p => `
                <label class="cmp-proj-opt">
                    <input type="checkbox" value="${p.id}" ${_cmpSelectedIds.has(String(p.id)) ? 'checked' : ''} onchange="cmpToggleProject('${p.id}')">
                    <div class="cmp-proj-opt-info">
                        <div class="cmp-proj-opt-name">${_esc(p.title)}</div>
                        <div class="cmp-proj-opt-meta">${_esc(p.group_name || p.project_type || '')}</div>
                    </div>
                    ${_cmpSelectedIds.has(String(p.id)) ? '<span class="cmp-proj-opt-badge">✓</span>' : ''}
                </label>`).join('');
        }

        function cmpFilterDropdown() {
            const q = _$c('cmpProjectSearch').value.toLowerCase();
            cmpRenderDropdown(q ? _cmpAllProjects.filter(p => p.title.toLowerCase().includes(q) || (p.group_name || '').toLowerCase().includes(q)) : _cmpAllProjects);
        }

        function cmpToggleProject(id) {
            id = String(id);
            if (_cmpSelectedIds.has(id)) { _cmpSelectedIds.delete(id); }
            else {
                if (_cmpSelectedIds.size >= 10) { alert('Maximum 10 projects.'); const cb = document.querySelector(`input[value="${id}"]`); if (cb) cb.checked = false; return; }
                _cmpSelectedIds.add(id);
            }
            cmpUpdateTags(); cmpUpdateBtn(); cmpFilterDropdown();
        }

        function cmpRemoveProject(id) { _cmpSelectedIds.delete(id); cmpUpdateTags(); cmpUpdateBtn(); cmpFilterDropdown(); }

        function cmpUpdateTags() {
            const c = _$c('cmpSelectedTags'), count = _$c('cmpSelectedCount');
            if (count) count.textContent = _cmpSelectedIds.size + ' selected';
            if (!_cmpSelectedIds.size) { c.innerHTML = '<span style="font-size:12px;color:var(--slate-400);align-self:center;">No projects selected</span>'; return; }
            c.innerHTML = [..._cmpSelectedIds].map(id => {
                const p = _cmpAllProjects.find(x => String(x.id) === id);
                const t = p ? _trunc(p.title, 28) : 'Project #' + id;
                return `<span class="cmp-tag">${_esc(t)}<button onclick="cmpRemoveProject('${id}')" title="Remove">✕</button></span>`;
            }).join('');
        }

        function cmpUpdateBtn() { _$c('cmpCompareBtn').disabled = _cmpSelectedIds.size < 2; }

        /* ══ RUN COMPARE ══ */
        async function cmpRunCompare() {
            if (_cmpSelectedIds.size < 2) return;
            const si = _$c('hiddenStartDate'), ei = _$c('hiddenEndDate');
            if (si) _cmpStartDate = si.value;
            if (ei) _cmpEndDate = ei.value;
            if (!_cmpStartDate || !_cmpEndDate) { alert('Please select a date range.'); return; }
            CmpPanel._cache = {};
            _$c('cmpLoadingOverlay').classList.add('show');
            try {
                const ids = [..._cmpSelectedIds].join(',');
                const res = await fetch(`/mk/api/compare/all?project_ids=${ids}&start_date=${_cmpStartDate}&end_date=${_cmpEndDate}`);
                _cmpData = await res.json();
                if (!_cmpData.success) throw new Error(_cmpData.error || 'API error');
                _cmpRenderAll(_cmpData);
                _$c('cmpResultsSection').style.display = 'block';
                _$c('cmpEmptyState').style.display = 'none';
            } catch (e) { alert('Compare failed: ' + e.message); }
            finally { _$c('cmpLoadingOverlay').classList.remove('show'); }
        }

        /* ══ DATA HELPERS ══ */
        function _cmpNode(data, pid) {
            if (!data || typeof data !== 'object') return null;
            return data[String(pid)] ?? data[parseInt(pid)] ?? null;
        }
        function _cmpVolTotal(volume, pid) {
            const n = _cmpNode(volume, pid); if (!n) return 0;
            if (n.volume_total?.all?.total !== undefined) return _int(n.volume_total.all.total);
            if (typeof n === 'number') return n; return 0;
        }
        function _cmpByMedia(volume, pid) {
            const n = _cmpNode(volume, pid); if (!n?.volume_total?.bymedia) return {};
            const r = {}; for (const [k, v] of Object.entries(n.volume_total.bymedia)) r[k] = _int(v); return r;
        }
        function _cmpSent(sentiment, pid) {
            const n = _cmpNode(sentiment, pid); if (!n) return { pos: 0, neg: 0, net: 0 };
            const s = n.sentiment_total || n;
            return { pos: _int(s.pos ?? s.positive ?? 0), neg: _int(s.neg ?? s.negative ?? 0), net: _int(s.net ?? s.neutral ?? 0) };
        }

        /* ══ RENDER ALL ══ */
        function _cmpRenderAll(data) {
            const details = data.project_details || {}, volume = data.data?.volumetotal || {}, sentiment = data.data?.sentimenttotal || {}, ids = data.project_ids || [];
            _$c('cmpMediaDateBadge').textContent = `${_cmpStartDate} → ${_cmpEndDate}`;
            _cmpRenderCards(ids, details, volume, sentiment);
            _cmpRenderVolChart(ids, details, volume);
            _cmpRenderRanking(ids, details, volume);
            _cmpRenderMediaTable(ids, details, volume);
            _cmpRenderMediaChart(ids, details, volume);
            _cmpRenderSovPies(ids, details, volume);
            _cmpRenderSentBars(ids, details, sentiment);
        }

        /* ── Project Summary Cards ── */
        function _cmpRenderCards(ids, details, volume, sentiment) {
            const row = _$c('cmpProjectsRow');
            row.innerHTML = ids.map((id, i) => {
                const p = details[id] || details[String(id)] || {}, color = CMP_PALETTE[i % CMP_PALETTE.length];
                const vol = _cmpVolTotal(volume, id), s = _cmpSent(sentiment, id), total = s.pos + s.neg + s.net || 1;
                const bm = _cmpByMedia(volume, id);
                const badges = Object.entries(bm).filter(([, v]) => v > 0).sort((a, b) => b[1] - a[1]).map(([k, v]) => `<span class="badge bg-light-secondary text-muted" style="font-size:10px;font-weight:600;">${CMP_MEDIA_LABELS[k] || k}: ${_fmtN(v)}</span>`).join('');
                return `<div class="col-xl-${Math.max(3, Math.floor(12 / ids.length))} col-md-6 col-sm-6">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:9px;height:9px;border-radius:50%;background:${color};flex-shrink:0;box-shadow:0 0 0 3px ${color}22;animation:pulseP 2.5s infinite;"></div>
                                <div>
                                    <h6 class="mb-0 f-14" title="${_esc(p.title || 'Project #' + id)}">${_esc(_trunc(p.title || 'Project #' + id, 30))}</h6>
                                    <small class="text-muted">${_esc(p.group_name || p.project_type || '—')}</small>
                                </div>
                            </div>
                            <span class="badge bg-light-secondary text-muted rounded-pill" style="font-size:10px;font-weight:700;">${numK(vol)} mentions</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                <div class="col-4 text-center"><div class="p-2 rounded-2" style="background:${color}14;"><h6 class="mb-0 fw-bold cursor-pointer" style="color:${color};font-size:16px;" onclick="CmpPanel.open('all','all','${id}')">${numK(vol)}</h6><small class="text-muted text-uppercase fw-semibold" style="font-size:9px;letter-spacing:.4px;">Volume</small></div></div>
                                <div class="col-4 text-center"><div class="p-2 rounded-2" style="background:#ECFDF5;"><h6 class="mb-0 fw-bold text-success cursor-pointer" style="font-size:16px;" onclick="CmpPanel.open('all','pos','${id}')">${((s.pos / total) * 100).toFixed(1)}%</h6><small class="text-muted text-uppercase fw-semibold" style="font-size:9px;letter-spacing:.4px;">Positive</small></div></div>
                                <div class="col-4 text-center"><div class="p-2 rounded-2" style="background:#FEF2F2;"><h6 class="mb-0 fw-bold text-danger cursor-pointer" style="font-size:16px;" onclick="CmpPanel.open('all','neg','${id}')">${((s.neg / total) * 100).toFixed(1)}%</h6><small class="text-muted text-uppercase fw-semibold" style="font-size:9px;letter-spacing:.4px;">Negative</small></div></div>
                            </div>
                            <div class="sent-mini mb-3">${[['pos', 'Pos', s.pos], ['net', 'Neu', s.net], ['neg', 'Neg', s.neg]].map(([k, lbl, val]) => { const pct = ((val / total) * 100).toFixed(0); return `<div class="sent-mini-row"><span style="color:${CMP_SENT_COLORS[k]};font-size:10px;font-weight:700;">${lbl}</span><div class="sent-mini-track"><div class="sent-mini-fill" style="width:0%;background:${CMP_SENT_COLORS[k]}" data-pct="${pct}"></div></div><span class="text-muted text-end" style="font-size:10px;font-weight:700;">${pct}%</span></div>`; }).join('')}</div>
                            ${badges ? `<div class="d-flex flex-wrap gap-1 pt-2 border-top">${badges}</div>` : ''}
                        </div>
                    </div>
                </div>`;
            }).join('');
            setTimeout(() => document.querySelectorAll('.sent-mini-fill').forEach(el => { el.style.width = el.dataset.pct + '%'; }), 150);
        }

        /* ── Volume Chart ── */
        function _cmpRenderVolChart(ids, details, volume) {
            if (_apexVol) { try { _apexVol.destroy(); } catch (e) { } } _apexVol = null;
            const el = _$c('cmpVolumeChart'), ld = _$c('cmpVolLoading');
            const cats = ids.map(id => { const p = details[id] || details[String(id)] || {}; return _trunc(p.title || 'Project #' + id, 22); });
            const totals = ids.map(id => _cmpVolTotal(volume, id));
            const colors = ids.map((_, i) => CMP_PALETTE[i % CMP_PALETTE.length]);
            _apexVol = new ApexCharts(el, {
                chart: { type: _cmpVolType === 'line' ? 'area' : 'bar', height: 300, fontFamily: 'inherit', background: 'transparent', toolbar: { show: false }, animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1000 } }, events: { dataPointSelection: (_, ctx, cfg) => { CmpPanel.open('all', 'all', ids[cfg.dataPointIndex]); }, markerClick: (_, ctx, cfg) => { CmpPanel.open('all', 'all', ids[cfg.dataPointIndex]); } } },
                series: [{ name: 'Total Volume', data: totals }], colors,
                plotOptions: { bar: { borderRadius: 4, distributed: true, columnWidth: '55%', borderRadiusApplication: 'end' } },
                stroke: { curve: 'smooth', width: _cmpVolType === 'line' ? 2.5 : 0 },
                fill: { type: _cmpVolType === 'line' ? 'gradient' : 'solid', gradient: { type: 'vertical', shadeIntensity: 0, opacityFrom: .35, opacityTo: .05, stops: [0, 80, 100] }, opacity: .9 },
                dataLabels: { enabled: totals.length <= 8, formatter: v => numK(v), style: { fontFamily: 'inherit', fontWeight: '700', fontSize: '11px' }, background: { enabled: false } },
                legend: { show: false },
                xaxis: { categories: cats, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontFamily: 'inherit', fontSize: '11px', fontWeight: 600, colors: '#94A3B8' } } },
                yaxis: { labels: { formatter: v => numK(v), style: { fontFamily: 'inherit', fontSize: '10px', fontWeight: 600, colors: '#94A3B8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                grid: { borderColor: 'rgba(226,232,240,.55)', strokeDashArray: 3, xaxis: { lines: { show: false } } },
                tooltip: { shared: false, y: { formatter: v => _fmtN(v) + ' posts' }, style: { fontFamily: 'inherit', fontSize: '12px' } },
            });
            _apexVol.render().then(() => { if (ld) ld.classList.add('hidden'); });
            const leg = _$c('cmpVolumeLegend');
            leg.innerHTML = ids.map((id, i) => { const p = details[id] || details[String(id)] || {}; return `<span class="d-flex align-items-center gap-1 cursor-pointer" style="font-size:11px;font-weight:600;color:var(--slate-500);" onclick="CmpPanel.open('all','all','${id}')"><span style="width:9px;height:9px;border-radius:3px;background:${CMP_PALETTE[i % CMP_PALETTE.length]};display:inline-block;flex-shrink:0;"></span>${_esc(_trunc(p.title || 'Project #' + id, 30))}</span>`; }).join('');
        }

        /* ── Volume Ranking ── */
        function _cmpRenderRanking(ids, details, volume) {
            const c = _$c('cmpVolumeRanking');
            const ranked = ids.map((id, i) => ({ id, i, title: (details[id] || details[String(id)] || {}).title || 'Project #' + id, total: _cmpVolTotal(volume, id) })).sort((a, b) => b.total - a.total);
            const max = ranked[0]?.total || 1;
            const rk = ['gold', 'silver', 'bronze'];
            c.innerHTML = `<div style="overflow-x:auto"><table class="cmp-rank-table">
                <thead><tr><th>#</th><th>Project</th><th>Total</th><th style="min-width:90px;">Bar</th></tr></thead>
                <tbody>${ranked.map((item, rank) => { const pct = ((item.total / max) * 100).toFixed(0), color = CMP_PALETTE[item.i % CMP_PALETTE.length]; return `<tr onclick="cmpShowPlatPicker(event,'${item.id}','${item.title.replace(/'/g, "\\'")}')"><td><div class="rank-num ${rk[rank] || ''}">${rank + 1}</div></td><td><div class="fw-bold f-13 text-truncate" style="max-width:140px;">${_esc(item.title)}</div><div style="width:8px;height:8px;border-radius:50%;background:${color};margin-top:3px;display:inline-block;"></div></td><td class="fw-bold f-14">${_fmtN(item.total)}</td><td><div class="bar-track"><div class="bar-fill" style="width:0%;background:${color}" data-pct="${pct}"></div></div></td></tr>`; }).join('')}</tbody>
            </table></div>
            <div class="p-2 text-center" style="font-size:11px;color:var(--slate-400);border-top:1px solid var(--slate-50);"><i class="ph ph-cursor-click me-1"></i>Click row to see mentions per platform</div>`;
            setTimeout(() => c.querySelectorAll('.bar-fill').forEach(el => { el.style.width = el.dataset.pct + '%'; }), 150);
        }

        /* ── Media Table ── */
        function _cmpRenderMediaTable(ids, details, volume) {
            const wrap = _$c('cmpMediaTableWrap'), MK = ['doc', 'twit', 'fb', 'instagram', 'youtube', 'tiktok'];
            const rows = ids.map(id => { const p = details[id] || details[String(id)] || {}; return { id, title: p.title || 'Project #' + id, bm: _cmpByMedia(volume, id), total: _cmpVolTotal(volume, id) }; });
            wrap.innerHTML = `<table class="cmp-media-table"><thead><tr><th style="width:30px;">No</th><th>Project</th>${MK.map(k => `<th>${CMP_MEDIA_LABELS[k]}</th>`).join('')}<th>Total</th></tr></thead><tbody>${rows.map((item, i) => { const color = CMP_PALETTE[i % CMP_PALETTE.length]; return `<tr onclick="cmpShowPlatPicker(event,'${item.id}','${item.title.replace(/'/g, "\\'")}')"><td class="text-muted fw-bold">${i + 1}</td><td><div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:50%;background:${color};flex-shrink:0;"></div><span style="color:${color};font-weight:700;">${_esc(_trunc(item.title, 32))}</span></div></td>${MK.map(k => { const v = item.bm[k] || 0; return `<td onclick="event.stopPropagation();CmpPanel.open('${k}','all','${item.id}')" style="cursor:pointer;color:${v > 0 ? 'var(--slate-700)' : 'var(--slate-300)'};" onmouseover="this.style.background='${CMP_MEDIA_COLORS[k]}18'" onmouseout="this.style.background=''">${_fmtN(v)}</td>`; }).join('')}<td class="fw-bold">${_fmtN(item.total)}</td></tr>`; }).join('')}</tbody></table>`;
        }

        /* ── Media Stacked Bar ── */
        function _cmpRenderMediaChart(ids, details, volume) {
            if (_apexMedia) { try { _apexMedia.destroy(); } catch (e) { } } _apexMedia = null;
            const el = _$c('cmpMediaChart'), ld = _$c('cmpMediaLoading'), MK = ['doc', 'twit', 'fb', 'instagram', 'youtube', 'tiktok'];
            const cats = ids.map(id => { const p = details[id] || details[String(id)] || {}; return _trunc(p.title || 'Project #' + id, 22); });
            const series = MK.map(k => ({ name: CMP_MEDIA_LABELS[k], data: ids.map(id => _cmpByMedia(volume, id)[k] || 0) })).filter(s => s.data.some(v => v > 0));
            if (!series.length) { if (ld) ld.classList.add('hidden'); return; }
            const colors = series.map(s => CMP_MEDIA_COLORS[MK.find(k => CMP_MEDIA_LABELS[k] === s.name)]);
            _apexMedia = new ApexCharts(el, {
                chart: { type: 'bar', height: 260, fontFamily: 'inherit', background: 'transparent', toolbar: { show: false }, stacked: true, animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1000 } }, events: { dataPointSelection: (_, ctx, cfg) => { const platKey = MK.find(k => CMP_MEDIA_LABELS[k] === series[cfg.seriesIndex]?.name) || 'all'; CmpPanel.open(platKey, 'all', ids[cfg.dataPointIndex]); } } },
                series, colors,
                plotOptions: { bar: { borderRadius: 3, columnWidth: '55%', borderRadiusApplication: 'end' } },
                dataLabels: { enabled: false },
                xaxis: { categories: cats, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontFamily: 'inherit', fontSize: '11px', fontWeight: 600, colors: '#94A3B8' } } },
                yaxis: { labels: { formatter: v => numK(v), style: { fontFamily: 'inherit', fontSize: '10px', fontWeight: 600, colors: '#94A3B8' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                grid: { borderColor: 'rgba(226,232,240,.55)', strokeDashArray: 3, xaxis: { lines: { show: false } } },
                legend: { position: 'bottom', horizontalAlign: 'left', fontFamily: 'inherit', fontSize: '11px', fontWeight: 600, labels: { colors: '#94A3B8' }, markers: { width: 9, height: 9, radius: 3 }, itemMargin: { horizontal: 12, vertical: 4 } },
                tooltip: { shared: true, intersect: false, y: { formatter: v => _fmtN(v) + ' posts' }, style: { fontFamily: 'inherit', fontSize: '12px' } },
                fill: { opacity: .9 },
            });
            _apexMedia.render().then(() => { if (ld) ld.classList.add('hidden'); });
        }

        /* ── SOV Pies ── */
        function _cmpRenderSovPies(ids, details, volume) {
            Object.values(_apexSov).forEach(c => { try { c.destroy(); } catch (e) { } });
            Object.keys(_apexSov).forEach(k => delete _apexSov[k]);
            const MK = ['doc', 'twit', 'fb', 'instagram', 'youtube', 'tiktok'];
            const labels = ids.map(id => { const p = details[id] || details[String(id)] || {}; return _trunc(p.title || 'Project #' + id, 22); });
            const colors = ids.map((_, i) => CMP_PALETTE[i % CMP_PALETTE.length]);
            const allKeys = [null, ...MK];
            const datasets = allKeys.map(k => ({ key: k, label: k ? (CMP_MEDIA_LABELS[k] || k) : 'All Media', vals: ids.map(id => k ? (_cmpByMedia(volume, id)[k] || 0) : _cmpVolTotal(volume, id)) })).filter(d => d.vals.some(v => v > 0));
            if (!datasets.length) { _$c('cmpSovGrid').innerHTML = '<div class="chart-empty"><i class="ph ph-pie-chart"></i><span>No data</span></div>'; _$c('cmpSovTabNav').innerHTML = ''; return; }
            const tabNav = _$c('cmpSovTabNav');
            tabNav.innerHTML = datasets.map((d, i) => `<button class="cmp-tab-btn${i === 0 ? ' active' : ''}" onclick="_cmpSovShowTab(${i}, this)" data-sov-idx="${i}">${d.label}</button>`).join('');
            const grid = _$c('cmpSovGrid');
            grid.innerHTML = datasets.map((d, i) => `<div id="cmpSovPanel_${i}" style="${i === 0 ? '' : 'display:none;'}"><div id="cmpSovChart_${i}" style="min-height:280px;"></div></div>`).join('');
            _cmpSovRenderOne(0, datasets, ids, labels, colors);
        }

        function _cmpSovShowTab(idx, btn) {
            btn.closest('.cmp-tab-nav').querySelectorAll('.cmp-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('[id^="cmpSovPanel_"]').forEach((el, i) => { el.style.display = i === idx ? '' : 'none'; });
            if (!_apexSov[idx]) {
                const datasets = window._cmpSovDatasets, ids = window._cmpSovIds, labels = window._cmpSovLabels, colors = window._cmpSovColors;
                if (datasets) _cmpSovRenderOne(idx, datasets, ids, labels, colors);
            } else { try { _apexSov[idx].updateOptions({}); } catch (e) { } }
        }

        function _cmpSovRenderOne(idx, datasets, ids, labels, colors) {
            window._cmpSovDatasets = datasets; window._cmpSovIds = ids; window._cmpSovLabels = labels; window._cmpSovColors = colors;
            const d = datasets[idx], el = _$c(`cmpSovChart_${idx}`);
            if (!el || !d) return;
            const total = d.vals.reduce((a, b) => a + b, 0) || 1;
            const pcts = d.vals.map(v => parseFloat(((v / total) * 100).toFixed(1)));
            const ap = new ApexCharts(el, {
                chart: { type: 'pie', height: 300, fontFamily: 'inherit', background: 'transparent', toolbar: { show: false }, animations: { enabled: true, easing: 'linear', speed: 800 }, events: { dataPointSelection: (_, ctx, cfg) => { CmpPanel.open(d.key || 'all', 'all', ids[cfg.dataPointIndex]); } } },
                series: pcts, labels, colors,
                dataLabels: { enabled: false },
                legend: { position: 'bottom', fontFamily: 'inherit', fontSize: '11px', fontWeight: 600, labels: { colors: '#94A3B8' }, markers: { width: 9, height: 9, radius: 50 }, itemMargin: { horizontal: 10, vertical: 4 }, formatter: (seriesName, opts) => `${seriesName} — ${_fmtN(d.vals[opts.seriesIndex])}` },
                stroke: { width: 2, colors: ['#fff'] },
                tooltip: { y: { formatter: (v, opts) => `${_fmtN(d.vals[opts.seriesIndex])} (${v}%)` }, style: { fontFamily: 'inherit', fontSize: '12px' } },
                plotOptions: { pie: { dataLabels: { offset: 25, minAngleToShowLabel: 5 } } },
            });
            ap.render();
            _apexSov[idx] = ap;
        }

        /* ── Sentiment Stacked Bars ── */
        function _cmpRenderSentBars(ids, details, sentiment) {
            const grid = _$c('cmpSentGrid');
            grid.innerHTML = ids.map((id, i) => {
                const p = details[id] || details[String(id)] || {}, s = _cmpSent(sentiment, id), total = s.pos + s.neg + s.net || 1;
                const pp = ((s.pos / total) * 100).toFixed(1), np = ((s.net / total) * 100).toFixed(1), ng = ((s.neg / total) * 100).toFixed(1);
                return `<div class="sent-stack-row"><div><div class="fw-bold f-12 text-truncate" style="color:var(--slate-700);" title="${_esc(p.title || 'Project #' + id)}">${_esc(_trunc(p.title || 'Project #' + id, 22))}</div><div style="font-size:10px;color:var(--slate-400);margin-top:2px;">${_fmtN(s.pos + s.neg + s.net)} total</div></div><div><div class="sent-stack-bars cursor-pointer" onclick="CmpPanel.open('all','all','${id}')"><div class="sent-seg" style="width:0%;background:#10b981" data-pct="${pp}" title="Pos: ${pp}%">${pp > 10 ? pp + '%' : ''}</div><div class="sent-seg" style="width:0%;background:#64748b" data-pct="${np}" title="Neu: ${np}%">${np > 10 ? np + '%' : ''}</div><div class="sent-seg" style="width:0%;background:#ef4444" data-pct="${ng}" title="Neg: ${ng}%">${ng > 10 ? ng + '%' : ''}</div></div><div class="d-flex gap-3 mt-1"><span onclick="CmpPanel.open('all','pos','${id}')" class="cursor-pointer" style="font-size:10px;color:#10b981;font-weight:700;">● Pos ${pp}%</span><span onclick="CmpPanel.open('all','neu','${id}')" class="cursor-pointer" style="font-size:10px;color:#64748b;font-weight:700;">● Neu ${np}%</span><span onclick="CmpPanel.open('all','neg','${id}')" class="cursor-pointer" style="font-size:10px;color:#ef4444;font-weight:700;">● Neg ${ng}%</span></div></div></div>`;
            }).join('');
            setTimeout(() => document.querySelectorAll('.sent-seg').forEach(el => { el.style.width = el.dataset.pct + '%'; }), 150);
        }

        /* ── Sentiment Donut ── */
        function _cmpRenderSentDonut(ids, details, sentiment) {
            const grid = _$c('cmpSentDonut');
            Object.values(_apexSent).forEach(c => { try { c.destroy(); } catch (e) { } });
            const cols = Math.min(ids.length, 3);
            grid.style.cssText = `display:grid;grid-template-columns:repeat(${cols},1fr);gap:20px;`;
            grid.innerHTML = ids.map((_, i) => `<div id="cmpSentDnt_${i}" style="min-height:220px;"></div>`).join('');
            ids.forEach((id, i) => {
                const p = details[id] || details[String(id)] || {}, s = _cmpSent(sentiment, id), el = _$c(`cmpSentDnt_${i}`); if (!el) return;
                const ap = new ApexCharts(el, {
                    chart: { type: 'donut', height: 220, fontFamily: 'inherit', background: 'transparent', toolbar: { show: false }, animations: { enabled: true, speed: 600 }, events: { dataPointSelection: (_, ctx, cfg) => { const sm = ['pos', 'neu', 'neg']; CmpPanel.open('all', sm[cfg.dataPointIndex] || 'all', id); } } },
                    series: [s.pos, s.net, s.neg], labels: ['Positive', 'Neutral', 'Negative'], colors: ['#10b981', '#64748b', '#ef4444'],
                    title: { text: _trunc(p.title || 'Project #' + id, 22), align: 'center', style: { fontFamily: 'inherit', fontSize: '12px', fontWeight: '700', color: 'var(--slate-700)' } },
                    dataLabels: { enabled: true, formatter: v => v.toFixed(0) + '%', style: { fontFamily: 'inherit', fontSize: '11px', fontWeight: '700' }, dropShadow: { enabled: false } },
                    legend: { position: 'bottom', fontFamily: 'inherit', fontSize: '11px', fontWeight: 600, labels: { colors: '#94A3B8' } },
                    stroke: { width: 2, colors: ['#fff'] },
                    plotOptions: { pie: { donut: { size: '55%' } } },
                    tooltip: { y: { formatter: v => _fmtN(v) }, style: { fontFamily: 'inherit', fontSize: '12px' } },
                });
                ap.render();
                _apexSent[i] = ap;
            });
        }

        /* ── View switches ── */
        function cmpSwitchVolume(type, btn) {
            _cmpVolType = type;
            btn.closest('.cmp-tab-nav').querySelectorAll('.cmp-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (_cmpData) { const { project_ids: ids, project_details: details, data } = _cmpData; _cmpRenderVolChart(ids, details, data.volumetotal || {}); }
        }
        function cmpSwitchSent(type, btn) {
            btn.closest('.cmp-tab-nav').querySelectorAll('.cmp-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            _$c('cmpSentBars').style.display = type === 'bar' ? 'block' : 'none';
            _$c('cmpSentDonut').style.display = type === 'donut' ? 'block' : 'none';
            if (type === 'donut' && _cmpData) { const { project_ids: ids, project_details: details, data } = _cmpData; _cmpRenderSentDonut(ids, details, data.sentimenttotal || {}); }
        }

        /* ── Platform Picker ── */
        function cmpShowPlatPicker(event, projectId, projectTitle) {
            event.stopPropagation();
            _cmpPickerPid = String(projectId);
            const pp = _$c('cmpPlatPicker');
            const pw = 175, ph = 270, vw = window.innerWidth, vh = window.innerHeight;
            let left = event.clientX + 10, top = event.clientY - 10;
            if (left + pw > vw - 8) left = event.clientX - pw - 10;
            if (top + ph > vh - 8) top = vh - ph - 8; if (top < 8) top = 8;
            pp.style.left = left + 'px'; pp.style.top = top + 'px';
            pp.classList.add('show');
        }

        /* ══ CmpPanel ══ */
        const CmpPanel = (() => {
            let _cache = {}, _allItems = [], _filtered = [], _curSent = 'all', _curPlat = null, _curPid = null;
            const SENT_MAP = { '1': 'pos', 'positive': 'pos', 'positif': 'pos', '-1': 'neg', '2': 'neg', 'negative': 'neg', 'negatif': 'neg' };
            const _ns = item => SENT_MAP[String(item.class_sentiment || item.sentiment || '0').toLowerCase().trim()] || 'neu';

            function openPlatform(platform, sentiment) {
                _$c('cmpPlatPicker')?.classList.remove('show');
                open(platform, sentiment || 'all', _cmpPickerPid || _curPid);
            }

            async function open(platform, sentiment, projectId) {
                _curPlat = platform; _curSent = sentiment || 'all';
                if (projectId) _curPid = String(projectId);
                const meta = CMP_PLAT_META[platform] || { label: platform, color: '#4361EE' };
                CmpDetail.close();
                _$c('cmpPanelDot').style.background = meta.color;
                _$c('cmpPanelTitle').textContent = meta.label + (platform === 'all' ? ' — All Platforms' : '');
                _$c('cmpPanelMeta').textContent = _cmpStartDate + ' – ' + _cmpEndDate;
                document.querySelectorAll('#cmpSntPanel .do-panel-tab').forEach(t => t.classList.toggle('active', t.dataset.s === _curSent));
                const list = _$c('cmpPanelList');
                list.innerHTML = `<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;
                const overlay = _$c('cmpPanelOverlay'), panel = _$c('cmpSntPanel');
                overlay.classList.remove('hiding'); panel.classList.remove('hiding');
                overlay.classList.add('show'); panel.classList.add('show');
                try {
                    const key = `${_curPid}_${platform}_${_cmpStartDate}_${_cmpEndDate}`;
                    if (!_cache[key]) _cache[key] = await _fetchAll(platform, _curPid);
                    _allItems = _cache[key];
                    _filtered = _curSent === 'all' ? _allItems : _allItems.filter(i => _ns(i) === _curSent);
                    _render(list, _filtered, platform, meta.color);
                } catch (err) {
                    list.innerHTML = `<div style="padding:50px 20px;text-align:center;color:#94A3B8;font-size:13px;">Gagal memuat data<br><small>${_esc(err.message)}</small></div>`;
                }
            }

            function close() {
                const overlay = _$c('cmpPanelOverlay'), panel = _$c('cmpSntPanel');
                panel.classList.add('hiding'); overlay.classList.add('hiding');
                setTimeout(() => { panel.classList.remove('show', 'hiding'); overlay.classList.remove('show', 'hiding'); CmpDetail.close(); }, 240);
            }

            function filterSent(sent) {
                _curSent = sent;
                document.querySelectorAll('#cmpSntPanel .do-panel-tab').forEach(t => t.classList.toggle('active', t.dataset.s === sent));
                _filtered = sent === 'all' ? _allItems : _allItems.filter(i => _ns(i) === sent);
                _render(_$c('cmpPanelList'), _filtered, _curPlat, (CMP_PLAT_META[_curPlat] || { color: '#4361EE' }).color);
            }

            async function _fetchAll(platform, pid) {
                if (platform === 'all') {
                    const res = await Promise.allSettled(['doc', 'twit', 'fb', 'instagram', 'youtube', 'tiktok'].map(p => _fetchOne(p, pid)));
                    return res.flatMap(r => r.status === 'fulfilled' ? r.value : []);
                }
                return _fetchOne(platform, pid);
            }

            async function _fetchOne(platform, pid) {
                const q = `project_id=${pid}&start_date=${_cmpStartDate}&end_date=${_cmpEndDate}&rows=500&start=0`;
                if (platform === 'instagram') {
                    for (const sub of ['postbylike', 'postbycomment', 'postbydate', '']) {
                        try { const r = await fetch(`/mk/api/news/ig-top-status?${q}${sub ? '&sub=' + sub : ''}`); const d = await r.json(); const items = Array.isArray(d.data) ? d.data : (Array.isArray(d) ? d : []); if (items.length > 0) return items.map(i => ({ ...i, _platform: platform })); } catch (e) { continue; }
                    }
                    return [];
                }
                const eps = { doc: `/mk/api/news/mentions?${q}`, twit: `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`, fb: `/mk/api/news/fb-top-status?${q}&sub=fblike`, youtube: `/mk/api/news/ytb-top-status?${q}`, tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike` };
                const url = eps[platform]; if (!url) return [];
                const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 30000);
                try {
                    const r = await fetch(url, { signal: ctrl.signal }); clearTimeout(tid);
                    if (!r.ok) return [];
                    const d = await r.json();
                    let items = [];
                    if (Array.isArray(d?.data?.data)) items = d.data.data;
                    else if (Array.isArray(d?.data)) items = d.data;
                    else if (Array.isArray(d?.statuses)) items = d.statuses;
                    else if (Array.isArray(d?.results)) items = d.results;
                    else if (Array.isArray(d?.posts)) items = d.posts;
                    else if (Array.isArray(d)) items = d;
                    if (platform === 'doc') items = items.filter(m => { const tc = String(m.tcode || '').toLowerCase(), mt = String(m.media_type || '').toLowerCase(); return tc === 'berita' || mt === 'berita' || mt === 'doc' || mt === 'news' || mt === 'online' || mt === 'article'; });
                    return items.map(i => ({ ...i, _platform: platform }));
                } catch (e) { clearTimeout(tid); return []; }
            }

            function _render(list, items, platform, accentColor) {
                if (!items.length) { list.innerHTML = `<div style="padding:50px 20px;text-align:center;color:#94A3B8;font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>`; return; }
                const SHOW = 60;
                list.innerHTML = items.slice(0, SHOW).map(item => {
                    const plat = item._platform || platform, meta = CMP_PLAT_META[plat] || { label: plat, color: accentColor };
                    const rawName = (() => { if (plat === 'fb') return item.from_name || item.page_name || null; if (plat === 'instagram') return item.username || item.user_name || null; if (plat === 'tiktok') return item.author_nickname || item.nickname || item.author?.nickname || null; if (plat === 'youtube') return item.channel_title || item.channel_name || item.snippet?.channelTitle || null; if (plat === 'twit') { const ao = typeof item.author === 'object' ? item.author : (() => { try { return JSON.parse(item.author || '{}'); } catch (e) { return {}; } })(); return item.name || ao?.name || ao?.scr_name || item.author_name || null; } return null; })();
                    const name = (rawName || item.author_name || item.channel_name || item.publisher || item.source_name || 'Unknown').trim();
                    const dName = /^\d{10,}$/.test(name) ? `User ${name.slice(-4)}` : name;
                    const rawH = (() => { if (plat === 'instagram') return item.username || ''; if (plat === 'twit') { const ao = typeof item.author === 'object' ? item.author : (() => { try { return JSON.parse(item.author || '{}'); } catch (e) { return {}; } })(); return item.screen_name || item.author_scr_name || ao?.scr_name || ao?.username || ''; } return item.author_scr_name || item.screen_name || item.username || ''; })().trim();
                    const handle = (() => { if (!rawH) return ''; const w = ['twit', 'instagram', 'tiktok'].includes(plat) ? (rawH.startsWith('@') ? rawH : '@' + rawH) : rawH; return w.replace(/^@/, '').toLowerCase() === dName.toLowerCase() ? '' : w; })();
                    const text = (item.content || item.caption || item.description || item.title || item.text || '').replace(/<[^>]*>/g, '').trim().slice(0, 150);
                    const ao = (() => { if (typeof item.author === 'object' && item.author) return item.author; try { return JSON.parse(item.author || '{}'); } catch (e) { return {}; } })();
                    const av = (item.avatar_url || item.profile_image_url || ao?.image || item.author_image || item.profile_image || item.thumbnail || '').trim();
                    const dt = (item.date_created || item.created_at || '').split('T')[0];
                    const sent = _ns(item);
                    const words = dName.replace(/[^a-zA-Z0-9\s]/g, '').trim().split(/\s+/).filter(Boolean);
                    const ini = (words.length >= 2 ? (words[0][0] + words[words.length - 1][0]) : (words[0]?.[0] || dName[0] || '?')).toUpperCase().replace(/['"]/g, '');
                    const avHtml = (av && av.startsWith('http')) ? `<img src="${_esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini}';">` : ini;
                    const enc = encodeURIComponent(JSON.stringify(item));
                    return `<div class="do-panel-item" onclick="CmpDetail.openEncoded('${enc}','${plat}')"><div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div><div class="do-panel-item-body"><div class="do-panel-author">${_esc(dName)}</div>${handle ? `<div class="do-panel-handle">${_esc(handle)}</div>` : ''}<div class="do-panel-text">${_esc(text || '(tidak ada konten)')}</div><div class="do-panel-footer"><span class="do-sent-badge do-sent-badge--${sent}">${sent === 'pos' ? 'Pos' : sent === 'neg' ? 'Neg' : 'Neu'}</span><span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span><span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>${dt ? `<span style="margin-left:auto;">${dt}</span>` : ''}</div></div></div>`;
                }).join('');
                if (items.length > SHOW) list.insertAdjacentHTML('beforeend', `<div style="padding:9px;text-align:center;font-size:11px;font-weight:600;color:#94A3B8;background:#F8FAFC;border-top:1px dashed #E2E8F0;">+${(items.length - SHOW).toLocaleString()} lainnya</div>`);
            }

            return { open, close, filterSent, openPlatform, get _cache() { return _cache; }, set _cache(v) { _cache = v; } };
        })();

        /* ══ CmpDetail ══ */
        const CmpDetail = {
            openEncoded(enc, plat) { try { this.open(JSON.parse(decodeURIComponent(enc)), plat); } catch (e) { } },
            open(item, platform) {
                const panel = _$c('cmpDetailPanel'), body = _$c('cmpDetailBody'), title = _$c('cmpDetailTitle');
                if (!panel || !body) return;
                const meta = CMP_PLAT_META[platform] || { label: platform, color: '#4361EE' };
                const SM2 = { '1': 'pos', 'positive': 'pos', 'positif': 'pos', '-1': 'neg', '2': 'neg', 'negative': 'neg', 'negatif': 'neg' };
                const sent = SM2[String(item.class_sentiment || item.sentiment || '0').toLowerCase().trim()] || 'neu';
                const SLBL = { pos: 'Positif', neg: 'Negatif', neu: 'Netral' }, SBGS = { pos: 'do-dp2-sent--pos', neg: 'do-dp2-sent--neg', neu: 'do-dp2-sent--neu' };
                const rawName = (() => { if (platform === 'fb') return item.from_name || item.page_name || null; if (platform === 'instagram') return item.username || null; if (platform === 'tiktok') return item.author_nickname || item.nickname || item.author?.nickname || null; if (platform === 'youtube') return item.channel_title || item.channel_name || item.snippet?.channelTitle || null; if (platform === 'twit') { const ao = typeof item.author === 'object' ? item.author : (() => { try { return JSON.parse(item.author || '{}'); } catch (e) { return {}; } })(); return item.name || ao?.name || ao?.scr_name || item.author_name || null; } return null; })();
                const name = (rawName || item.author_name || item.channel_name || item.publisher || item.source_name || 'Unknown').trim();
                const handle = ((platform === 'instagram' ? item.username : '') || item.author_scr_name || item.screen_name || item.username || '').trim();
                const content = (item.content || item.caption || item.description || item.title || item.text || '').replace(/<[^>]*>/g, '').trim();
                const av = (item.avatar_url || item.profile_image_url || item.author_image || item.profile_image || item.thumbnail || '').trim();
                const url = item.url || item.link || '', dt = item.date_created || item.created_at || '';
                title.textContent = name;
                const words = name.replace(/[^a-zA-Z0-9\s]/g, '').trim().split(/\s+/).filter(Boolean);
                const ini = (words.length >= 2 ? (words[0][0] + words[words.length - 1][0]) : (words[0]?.[0] || name[0] || '?')).toUpperCase().replace(/['"]/g, '');
                const avHtml = (av && av.startsWith('http')) ? `<img src="${_esc(av)}" onerror="this.parentElement.textContent='${ini}';">` : ini;
                let dtFmt = ''; if (dt) { try { dtFmt = new Date(dt).toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }); } catch (e) { dtFmt = dt.split('T')[0]; } }
                let mediaHtml = '';
                if (platform === 'youtube') { const ytId = (url.match(/[?&]v=([a-zA-Z0-9_-]{11})/) || url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/) || url.match(/shorts\/([a-zA-Z0-9_-]{11})/) || [])[1] || (item.video_id || item.youtube_id || ''); const thumb = item.thumbnail || item.thumbnail_url || item.image_url || (ytId ? `https://img.youtube.com/vi/${ytId}/hqdefault.jpg` : ''); if (ytId) { const eid = `yt_${ytId}_${Date.now()}`; mediaHtml = `<div id="${eid}" style="position:relative;cursor:pointer;border-radius:6px;overflow:hidden;background:#000;margin-bottom:10px;" onclick="document.getElementById('${eid}').innerHTML='<iframe width=\\"100%\\" height=\\"260\\" src=\\"https://www.youtube.com/embed/${ytId}?autoplay=1&controls=1\\" frameborder=\\"0\\" allowfullscreen></iframe>'"><img src="${thumb || `https://img.youtube.com/vi/${ytId}/hqdefault.jpg`}" style="width:100%;height:200px;object-fit:cover;display:block;" onerror="this.src='https://img.youtube.com/vi/${ytId}/mqdefault.jpg'"><div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.25);"><div style="width:52px;height:52px;background:#ff0000;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:22px;color:#fff;margin-left:3px;"></i></div></div></div>`; } else if (thumb) { mediaHtml = `<div class="do-dp2-media"><img src="${_esc(thumb)}" onerror="this.parentElement.style.display='none'" style="border-radius:6px;"></div>`; } }
                else if (platform === 'tiktok') { const tid = (url.match(/\/video\/(\d+)/) || url.match(/\/v\/(\d+)/) || [])[1] || (item.video_id || item.aweme_id || ''); const thumb = item.thumbnail || item.cover || item.image_url || item.video_cover || ''; if (tid) { const eid = `tt_${tid}_${Date.now()}`; mediaHtml = `<div id="${eid}" style="position:relative;cursor:pointer;background:#111827;border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center;height:240px;margin-bottom:10px;" onclick="CmpDetail.loadTikTok('${eid}','${tid}')">${thumb ? `<img src="${_esc(thumb)}" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:.65;pointer-events:none;">` : ''}<div style="position:relative;z-index:2;width:56px;height:56px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:24px;color:#111827;margin-left:3px;"></i></div></div>`; } else if (thumb) { mediaHtml = `<div class="do-dp2-media"><img src="${_esc(thumb)}" onerror="this.parentElement.style.display='none'" style="max-height:280px;object-fit:cover;width:100%;display:block;border-radius:6px;"></div>`; } }
                else { const thumb = item.image_url || item.thumbnail || item.media_url || item.picture || item.display_url || item.featured_image || ''; const isVideo = (item.media_type || item.type || '').toLowerCase().includes('video'); if (thumb) { mediaHtml = `<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;margin-bottom:10px;"><img src="${_esc(thumb)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:280px;object-fit:cover;display:block;">${isVideo ? `<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);"><div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;"><i class="ph ph-play-fill" style="font-size:20px;color:${meta.color};margin-left:3px;"></i></div></div>` : ''}</div>`; } }
                const statsMap = { twit: [['Retweet', item.num_retweeted || item.retweet_count || 0], ['Like', item.num_likes || item.favorite_count || 0], ['Quote', item.num_quote || 0]], fb: [['Like', item.likes || item.num_likes || 0], ['Share', item.shares || item.share_count || 0], ['Comment', item.num_comments || 0]], instagram: [['Like', item.num_likes || item.likes || 0], ['Comment', item.num_comments || item.comment_count || 0], ['View', item.num_views || item.views || 0]], youtube: [['View', item.num_views || item.views || 0], ['Like', item.num_likes || item.likes || 0], ['Comment', item.num_comments || 0]], tiktok: [['Play', item.views || item.play_count || 0], ['Like', item.likes || item.digg_count || 0], ['Share', item.shares || item.share_count || 0]], doc: [['Read', item.num_views || 0], ['Share', item.num_share || 0], ['Comment', item.num_comments || 0]] };
                const stats = statsMap[platform] || [];
                const statsHtml = stats.some(s => parseInt(s[1]) > 0) ? `<div class="do-dp2-stats">${stats.map(([l, v]) => `<div class="do-dp2-stat"><div class="do-dp2-stat-val">${parseInt(v || 0).toLocaleString()}</div><div class="do-dp2-stat-lbl">${l}</div></div>`).join('')}</div>` : '';
                const handleDisp = handle && !handle.replace('@', '').toLowerCase().startsWith(name.toLowerCase().slice(0, 4)) ? (handle.startsWith('@') ? handle : '@' + handle) : '';
                body.innerHTML = `<div class="do-dp2-avatar-row"><div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div><div><div class="do-dp2-name">${_esc(name)}</div>${handleDisp ? `<div class="do-dp2-handle">${_esc(handleDisp)}</div>` : ''}<span class="do-dp2-plat-badge" style="background:${meta.color}22;color:${meta.color};">${meta.label}</span></div></div>${dtFmt ? `<div class="do-dp2-meta">${dtFmt}</div>` : ''}<div class="do-dp2-sent ${SBGS[sent]}">${SLBL[sent]}</div>${mediaHtml}${content ? `<div class="do-dp2-content">${_esc(content)}</div>` : ''}${statsHtml}${url ? `<a href="${_esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out"></i> Lihat ${meta.label} Asli</a>` : ''}`;
                panel.classList.add('show');
            },
            close() { const p = _$c('cmpDetailPanel'); if (!p) return; p.classList.remove('show'); p.querySelectorAll('iframe').forEach(f => { try { f.src = f.src; } catch (e) { } }); },
            loadTikTok(eid, tid) { const el = _$c(eid); if (!el) return; el.style.cssText = 'cursor:default;min-height:560px;height:auto;background:#111827;border-radius:6px;overflow:hidden;margin-bottom:10px;'; el.innerHTML = `<iframe src="https://www.tiktok.com/embed/v2/${tid}" width="100%" height="560" frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen style="display:block;border:none;border-radius:6px;background:#111827;"></iframe>`; },
        };

        window.CmpPanel = CmpPanel; window.CmpDetail = CmpDetail; window.cmpShowPlatPicker = cmpShowPlatPicker;

        /* ══════════════════════════════════════════════════════
           EXPORT MODULE — identik dengan TikTok Most Engagement
        ══════════════════════════════════════════════════════ */
        const CmpExport = (() => {
    let _toastTimer = null;

    function _toast(msg, type = 'default', duration = 3200) {
        const t = _$c('exportToast'), m = _$c('exportToastMsg'), ico = _$c('exportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className = 'export-toast show ' + (type !== 'default' ? type : '');
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

    function _resizeAllCharts() {
        [_apexVol, _apexMedia].forEach(c => { if (c) { try { c.updateOptions({}); } catch (e) { } } });
        Object.values(_apexSov).forEach(c => { try { c.updateOptions({}); } catch (e) { } });
        Object.values(_apexSent).forEach(c => { try { c.updateOptions({}); } catch (e) { } });
    }

    function _drawPdfHeader(pdf, pW, margin, label) {
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + (label || 'Compare Projects'), margin, 7.5);
        const now = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - margin, 7.5, { align: 'right' });
    }

    /* ── Split canvas menjadi tepat `numPages` halaman ── */
    function _splitCanvas(pdf, canvas, margin, pW, pH, label, numPages) {
        const usableW = pW - margin * 2;
        const sliceH  = Math.ceil(canvas.height / numPages);

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
            slice.getContext('2d').drawImage(
                canvas,
                0, srcY, canvas.width, srcSlice,
                0, 0,    canvas.width, srcSlice
            );

            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, usableW, dstH);
            pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
            pdf.text(`Halaman ${page + 1} / ${numPages}`, pW / 2, pH - 3, { align: 'center' });
        }
    }

    /* ── Fit canvas ke 1 halaman (tidak terpotong) ── */
    function _fitCanvas(pdf, canvas, margin, pW, pH, label) {
        _drawPdfHeader(pdf, pW, margin, label);

        const usableW = pW - margin * 2;
        const usableH = pH - margin * 2 - 18;
        const ratio   = Math.min(usableW / canvas.width, usableH / canvas.height);
        const dstW    = canvas.width  * ratio;
        const dstH    = canvas.height * ratio;
        const x       = margin + (usableW - dstW) / 2;
        const y       = 14   + (usableH - dstH) / 2;

        pdf.addImage(canvas.toDataURL('image/png'), 'PNG', x, y, dstW, dstH);
        pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
        pdf.text('Halaman 1 / 1', pW / 2, pH - 3, { align: 'center' });
    }

    async function _captureArea() {
        const area = _$c('pageExportArea');
        if (!area) throw new Error('pageExportArea tidak ditemukan');
        window.scrollTo({ top: 0 });
        await new Promise(r => setTimeout(r, 400));
        _resizeAllCharts();
        await new Promise(r => setTimeout(r, 200));
        return html2canvas(area, {
            scale: 2, useCORS: true, allowTaint: false,
            backgroundColor: '#f1f5f9', logging: false, removeContainer: true,
            windowWidth: document.documentElement.scrollWidth,
            windowHeight: area.scrollHeight, height: area.scrollHeight,
            ignoreElements: el => el.hasAttribute('data-html2canvas-ignore'),
        });
    }

    async function _captureCard(areaId, cardKey) {
        const area = document.getElementById(areaId);
        if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');
        const chartMap = {
            'volume'    : [_apexVol],
            'media'     : [_apexMedia],
            'sov'       : Object.values(_apexSov),
            'sentiment' : Object.values(_apexSent),
        };
        (chartMap[cardKey] || []).forEach(c => { if (c) { try { c.updateOptions({}); } catch (e) { } } });
        await new Promise(r => setTimeout(r, 280));
        return html2canvas(area, {
            scale: 2, useCORS: true, allowTaint: false,
            backgroundColor: '#ffffff', logging: false, removeContainer: true,
            ignoreElements: el => el.hasAttribute('data-html2canvas-ignore'),
        });
    }

    const _cardLabels = {
        'volume'    : 'Volume Trend',
        'ranking'   : 'Total Volume Ranking',
        'media'     : 'Volume by Media Types',
        'sov'       : 'Share of Voice',
        'sentiment' : 'Sentiment Comparison',
    };

    function _cardFilename(cardKey) {
        const slugs = {
            'volume'    : 'volume-trend',
            'ranking'   : 'volume-ranking',
            'media'     : 'volume-by-media',
            'sov'       : 'share-of-voice',
            'sentiment' : 'sentiment-comparison',
        };
        const stamp = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        return `compare_${slugs[cardKey] || cardKey}_${stamp}`;
    }

    /* ── Export per-card: 1 halaman, fit to page ── */
    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)           { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }
        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);
        try {
            const canvas = await _captureCard(areaId, cardKey);
            const fname  = _cardFilename(cardKey);
            const label  = _cardLabels[cardKey] || cardKey;
            if (type === 'image') {
                const link = document.createElement('a');
                link.download = fname + '.png'; link.href = canvas.toDataURL('image/png'); link.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const landscape = canvas.width > canvas.height;
                const pdf = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit: 'mm', format: 'a4' });
                const pW  = pdf.internal.pageSize.getWidth();
                const pH  = pdf.internal.pageSize.getHeight();
                /* fit ke 1 halaman, tidak terpotong */
                _fitCanvas(pdf, canvas, 10, pW, pH, label);
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch (err) {
            console.error('[CmpExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btn, false);
        }
    }

    /* ── Export full page: tepat 2 halaman ── */
    async function run(type, btn) {
        if (!window.html2canvas)           { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }
        const btnPdf = _$c('pageExportPdfBtn'), btnImg = _$c('pageExportImgBtn');
        _btnState(btnPdf, true); _btnState(btnImg, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF 2 halaman…' : 'Mengambil gambar halaman…', 'default', 99999);
        try {
            const canvas = await _captureArea();
            const stamp  = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            if (type === 'image') {
                const link = document.createElement('a');
                link.download = `compare_projects_${stamp}.png`;
                link.href = canvas.toDataURL('image/png'); link.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pW  = pdf.internal.pageSize.getWidth();
                const pH  = pdf.internal.pageSize.getHeight();
                /* split tepat 2 halaman */
                _splitCanvas(pdf, canvas, 10, pW, pH, 'Compare Projects', 2);
                pdf.save(`compare_projects_${stamp}.pdf`);
                _toast('PDF 2 halaman berhasil diunduh!', 'success');
            }
        } catch (err) {
            console.error('[CmpExport]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btnPdf, false); _btnState(btnImg, false);
        }
    }

    return { run, runCard };
})();

        window.CmpExport = CmpExport;
    </script>

    {{-- Override DPicker for Compare ══ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.DPicker) {
                DPicker.apply = function () {
                    const si = document.getElementById('hiddenStartDate'), ei = document.getElementById('hiddenEndDate');
                    const dpModal = document.getElementById('doDpModal'), dpDisp = document.getElementById('doDateDisplay');
                    if (si && ei) { _cmpStartDate = si.value; _cmpEndDate = ei.value; if (dpDisp) dpDisp.textContent = si.value + ' – ' + ei.value; }
                    if (dpModal) dpModal.classList.remove('show');
                };
            }
            const form = document.getElementById('doFilterForm');
            if (form) form.addEventListener('submit', e => { e.preventDefault(); e.stopImmediatePropagation(); }, true);
            const proj = document.getElementById('doProject');
            if (proj) proj.addEventListener('change', e => { e.stopImmediatePropagation(); }, true);
        });
    </script>
@endsection