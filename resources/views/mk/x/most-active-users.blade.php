@extends('mk.layouts.app')

@section('title', 'Most Active Users - X Analytics')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/most-active-users.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════════
   DESIGN TOKENS — selaraskan dengan Top Influencers
═══════════════════════════════════════════════════════════ */
:root {
  --primary:        #038047;
  --primary-rgb:    3,128,71;
  --primary-lt:     rgba(3,128,71,.10);
  --dark:           #273B4A;
  --white:          #FFFFFF;
  --bg:             #F1F5F8;
  --slate-50:       #F8FAFC;
  --slate-100:      #F1F5F9;
  --slate-200:      #E2E8F0;
  --slate-300:      #CBD5E1;
  --slate-400:      #94A3B8;
  --slate-500:      #64748B;
  --slate-600:      #475569;
  --slate-700:      #334155;
  --slate-800:      #1E293B;
  --slate-900:      #0F172A;
  --radius:         8px;
  --radius-sm:      5px;
  --shadow-sm:      0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);
  --shadow-md:      0 4px 14px rgba(15,23,42,.08);
  --shadow-lg:      0 10px 30px rgba(15,23,42,.12);

  /* legacy compat */
  --color-brand:        #038047;
  --color-brand-dark:   #026035;
  --color-brand-light:  rgba(3,128,71,.10);
  --color-brand-mid:    rgba(3,128,71,.25);
  --color-x:            #0f1419;
  --color-x-blue:       #1d9bf0;
  --color-x-green:      #00ba7c;
  --color-x-pink:       #f91880;
  --color-pos:          #16a34a;
  --color-pos-bg:       #dcfce7;
  --color-neg:          #dc2626;
  --color-neg-bg:       #fee2e2;
  --color-neu:          #4b5563;
  --color-neu-bg:       #f3f4f6;
  --text-primary:       #0f1419;
  --text-secondary:     #4b5563;
  --text-muted:         #94a3b8;
  --text-label:         #64748b;
  --border:             1px solid #e5e9ef;
  --border-strong:      1px solid #d1d9e0;
  --bg-card:            #ffffff;
  --bg-muted:           #f8fafc;
  --bg-subtle:          #f1f5f9;
  --bg-hover:           #f7fbf9;
  --font-sans:          'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  --font-mono:          'DM Mono', monospace;
}

/* ═══════════════════════════════════════════════════════════
   ANIMATIONS
═══════════════════════════════════════════════════════════ */
@keyframes fadeUp   { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer  { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin     { to{transform:rotate(360deg)} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1} to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn     { from{opacity:0} to{opacity:1} }
@keyframes overlayOut    { from{opacity:1} to{opacity:0} }
@keyframes kpiShimmer    { 0%{left:-100%} 100%{left:150%} }
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0)} 30%{transform:scale(1.25) rotate(-10deg)} 60%{transform:scale(1.1) rotate(6deg)} }

/* ═══════════════════════════════════════════════════════════
   SHARED COMPONENTS
═══════════════════════════════════════════════════════════ */
.sk-block { border-radius:4px; background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; }
.spin-ring { width:26px; height:26px; border:2.5px solid var(--slate-100); border-top-color:var(--primary); border-radius:50%; animation:spin .65s linear infinite; }
.spinner-state { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:48px 20px; gap:12px; color:var(--slate-400); font-size:12px; font-weight:600; }
.chart-empty { height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; color:var(--slate-400); font-size:12px; font-weight:600; }
.chart-empty i { font-size:34px; color:var(--slate-300); display:block; }

/* ─── KPI Cards ─── */
.kpi-icon-bg { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0; }
.kpi-card-hover { will-change:transform,box-shadow; cursor:default; position:relative!important; overflow:hidden!important; transition:transform .25s cubic-bezier(.34,1.56,.64,1)!important,box-shadow .25s ease!important,filter .25s ease!important; }
.kpi-card-hover::before { content:''; position:absolute; top:0; bottom:0; left:-100%; width:60%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent); pointer-events:none; z-index:1; }
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025)!important; box-shadow:0 20px 40px rgba(0,0,0,.25)!important; filter:brightness(1.07)!important; }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important; display:inline-block!important; }
.kpi-card-hover:active { transform:translateY(-2px) scale(1.01)!important; transition-duration:.08s!important; }
.kpi-card-hover h3 { font-size:1.5rem; }

/* ─── Donut Legend ─── */
.donut-legend { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-top:10px; }
.donut-leg-item { display:flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:var(--slate-500); padding:3px 8px; background:var(--slate-50); border-radius:3px; border:1px solid var(--slate-200); cursor:pointer; transition:border-color .12s,background .12s,color .12s; }
.donut-leg-item:hover { border-color:var(--primary); background:var(--primary-lt); color:var(--primary); }
.donut-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

/* ─── Ranked List (ht-list) ─── */
.ht-list { display:flex; flex-direction:column; }
.ht-item { display:flex; align-items:center; gap:12px; padding:10px 16px; border-bottom:1px solid var(--slate-100); cursor:pointer; transition:background .12s; }
.ht-item:last-child { border-bottom:none; }
.ht-item:hover { background:var(--slate-50); }
.ht-rank { width:24px; height:24px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:800; color:var(--slate-400); background:var(--slate-100); border:1px solid var(--slate-200); }
.ht-rank--1 { background:linear-gradient(135deg,#ffd700,#F59E0B); color:#7c5900; border-color:#ffd700; }
.ht-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af); color:#3d3d3d; border-color:#c0c0c0; }
.ht-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820); color:#fff; border-color:#cd7f32; }
.ht-av { width:32px; height:32px; border-radius:50%; flex-shrink:0; overflow:hidden; border:1.5px solid var(--slate-200); }
.ht-av img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.ht-info { flex:1; min-width:0; }
.ht-name { font-size:13px; font-weight:700; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ht-handle { font-size:10.5px; color:var(--slate-400); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.ht-bar-wrap { flex:0 0 60px; height:6px; background:var(--slate-100); border-radius:99px; overflow:hidden; }
.ht-bar-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,var(--primary),rgba(3,128,71,.5)); transition:width .4s cubic-bezier(.4,0,.2,1); }
.ht-count { font-size:11px; font-weight:700; color:var(--slate-500); white-space:nowrap; flex-shrink:0; min-width:36px; text-align:right; }

/* ─── Pagination ─── */
.tme-pagination { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; border-top:1px solid var(--slate-100); flex-wrap:wrap; gap:8px; }
.tme-pag-info { font-size:11px; color:var(--slate-400); font-weight:500; }
.tme-pag-controls { display:flex; align-items:center; gap:3px; }
.tme-pag-btn { min-width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; padding:0 6px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; font-size:11px; font-weight:600; color:var(--slate-500); cursor:pointer; transition:all .12s; user-select:none; font-family:inherit; }
.tme-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary); color:var(--primary); background:var(--primary-lt); }
.tme-pag-btn.is-active { background:var(--primary); border-color:var(--primary); color:#fff; }
.tme-pag-btn:disabled { opacity:.35; cursor:not-allowed; }

/* ─── Page Export Bar ─── */
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

/* ─── Card Export Buttons ─── */
.card-exp-btn { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:var(--radius-sm); font-size:14px; cursor:pointer; flex-shrink:0; transition:all .14s ease; border:1px solid transparent; font-family:inherit; background:transparent; }
.card-exp-btn-pdf { color:#dc2626; border-color:#fca5a5; background:#fff3f3; }
.card-exp-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.card-exp-btn-img { color:var(--primary); border-color:rgba(3,128,71,.3); background:var(--primary-lt); }
.card-exp-btn-img:hover { background:var(--primary); color:#fff; border-color:var(--primary); }
.card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
.card-exp-btn .export-spinner { width:11px; height:11px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
.card-exp-btn.exporting .export-spinner { display:inline-block; }
.card-exp-btn.exporting .export-icon { display:none; }

/* ─── Export Toast ─── */
.export-toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px); background:var(--slate-900); color:#fff; border-radius:var(--radius); padding:10px 18px; font-size:12px; font-weight:600; box-shadow:var(--shadow-lg); z-index:99999; opacity:0; pointer-events:none; transition:opacity .22s ease,transform .22s ease; display:flex; align-items:center; gap:8px; white-space:nowrap; }
.export-toast.show { opacity:1; transform:translateX(-50%) translateY(0); }
.export-toast.success { background:#065f46; }
.export-toast.error { background:#991b1b; }

/* ─── Slide Panel ─── */
.do-panel-overlay { position:fixed; inset:0; z-index:9000; background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none; }
.do-panel-overlay.show { display:block; animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }
.do-panel { position:fixed; top:0; right:0; bottom:0; z-index:9001; width:520px; max-width:100vw; background:#fff; display:none; flex-direction:column; border-left:1px solid var(--slate-200); box-shadow:-8px 0 40px rgba(15,23,42,.16); }
.do-panel.show { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }
.do-panel-header { display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--slate-200); background:var(--slate-50); flex-shrink:0; }
.do-panel-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-panel-close { width:28px; height:28px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); font-size:16px; transition:all .14s; flex-shrink:0; }
.do-panel-close:hover { background:#ef4444; border-color:#ef4444; color:#fff; }

/* ─── User Profile inside Panel ─── */
.up-banner { height:80px; background:linear-gradient(135deg,#0d3d2e,#038047,#06b6d4); position:relative; flex-shrink:0; }
.up-banner::after { content:''; position:absolute; inset:0; background:repeating-linear-gradient(-55deg,transparent,transparent 28px,rgba(255,255,255,.03) 28px,rgba(255,255,255,.03) 29px); }
.up-profile { padding:0 20px 16px; margin-top:-28px; position:relative; z-index:2; border-bottom:1px solid var(--slate-200); }
.up-av { width:56px; height:56px; border-radius:50%; object-fit:cover; border:3px solid #fff; box-shadow:0 2px 10px rgba(0,0,0,.15); display:block; background:var(--slate-100); }
.up-av-fb { width:56px; height:56px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:800; color:var(--primary); background:var(--primary-lt); border:3px solid #fff; box-shadow:0 2px 10px rgba(0,0,0,.15); }
.up-name { font-size:16px; font-weight:700; color:var(--slate-900); margin-top:8px; display:flex; align-items:center; gap:5px; }
.up-handle { font-size:12px; color:var(--slate-400); margin-top:2px; }
.up-handle a { color:var(--slate-400); text-decoration:none; }
.up-handle a:hover { color:#1d9bf0; text-decoration:underline; }
.up-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:0; margin-top:12px; border:1px solid var(--slate-200); border-radius:var(--radius); overflow:hidden; }
.up-stat { padding:10px 8px; text-align:center; border-right:1px solid var(--slate-200); }
.up-stat:last-child { border-right:none; }
.up-stat-val { font-size:16px; font-weight:700; color:var(--slate-900); }
.up-stat-lbl { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
.up-total { display:flex; align-items:center; justify-content:space-between; margin-top:10px; padding:10px 14px; background:var(--primary-lt); border-radius:var(--radius); border:1px solid rgba(3,128,71,.15); }
.up-total-lbl { font-size:12px; font-weight:600; color:var(--primary); display:flex; align-items:center; gap:5px; }
.up-total-val { font-size:18px; font-weight:800; color:var(--primary); }

/* ─── Mentions inside Panel ─── */
.up-mentions-head { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--slate-200); background:var(--slate-50); position:sticky; top:0; z-index:3; }
.up-mentions-head h6 { font-size:13px; font-weight:700; color:var(--slate-900); margin:0; }
.up-mention-cnt { font-size:11px; font-weight:600; color:var(--slate-400); background:var(--slate-100); padding:3px 9px; border-radius:99px; }
.up-body { overflow-y:auto; flex:1; min-height:0; }
.up-body::-webkit-scrollbar { width:4px; }
.up-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.up-tweet { padding:12px 16px; border-bottom:1px solid var(--slate-100); cursor:pointer; transition:background .12s; }
.up-tweet:hover { background:var(--slate-50); }
.up-tweet:last-child { border-bottom:none; }
.up-tw-head { display:flex; align-items:center; gap:8px; margin-bottom:6px; }
.up-tw-author { font-size:12px; font-weight:700; color:var(--slate-900); }
.up-tw-time { font-size:10px; color:var(--slate-400); margin-left:auto; }
.up-tw-text { font-size:12px; color:var(--slate-600); line-height:1.6; margin-bottom:6px; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; word-break:break-word; }
.up-tw-foot { display:flex; align-items:center; gap:10px; font-size:10px; color:var(--slate-400); }
.up-tw-metric { display:flex; align-items:center; gap:3px; }
.up-tw-sent { padding:2px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
.up-tw-sent--pos { background:#d1fae5; color:#065f46; }
.up-tw-sent--neg { background:#fee2e2; color:#991b1b; }
.up-tw-sent--neu { background:var(--slate-100); color:var(--slate-500); }
.up-tw-link { display:inline-flex; align-items:center; gap:3px; font-size:9.5px; font-weight:700; color:var(--primary); text-decoration:none; padding:2px 6px; border-radius:3px; background:var(--primary-lt); border:1px solid rgba(3,128,71,.2); transition:background .12s,color .12s; margin-left:auto; }
.up-tw-link:hover { background:var(--primary); color:#fff; }
.up-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; gap:12px; color:var(--slate-400); font-size:12px; padding:40px 20px; }
.up-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; flex:1; padding:40px 20px; gap:8px; color:var(--slate-400); }
.up-empty i { font-size:32px; color:var(--slate-300); }
.up-pag { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; border-top:1px solid var(--slate-200); background:var(--slate-50); flex-wrap:wrap; gap:6px; flex-shrink:0; }
.up-pag-info { font-size:10px; color:var(--slate-400); }
.up-pag-btns { display:flex; gap:3px; }
.up-pag-btn { min-width:26px; height:26px; display:inline-flex; align-items:center; justify-content:center; padding:0 5px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; font-size:10px; font-weight:600; color:var(--slate-500); cursor:pointer; transition:all .12s; font-family:inherit; }
.up-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary); color:var(--primary); background:var(--primary-lt); }
.up-pag-btn.is-active { background:var(--primary); border-color:var(--primary); color:#fff; }
.up-pag-btn:disabled { opacity:.35; cursor:not-allowed; }

/* ─── Tweet Detail Sub-panel ─── */
.do-detail-panel { position:absolute; inset:0; background:#fff; z-index:5; display:none; flex-direction:column; animation:slideInRight .2s cubic-bezier(.4,0,.2,1); }
.do-detail-panel.show { display:flex; }
.do-dp2-header { display:flex; align-items:center; gap:8px; padding:12px 14px; background:var(--slate-50); border-bottom:1px solid var(--slate-200); flex-shrink:0; }
.do-dp2-back { width:28px; height:28px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--slate-500); transition:all .13s; font-size:14px; }
.do-dp2-back:hover { background:var(--primary-lt); color:var(--primary); border-color:var(--primary); }
.do-dp2-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-dp2-body { overflow-y:auto; flex:1; padding:16px; }
.do-dp2-body::-webkit-scrollbar { width:4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.do-dp2-meta { font-size:11px; color:var(--slate-400); font-weight:500; margin-bottom:10px; }
.do-dp2-sent { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:3px; font-size:11px; font-weight:700; margin-bottom:10px; }
.do-dp2-sent--pos { background:#d1fae5; color:#065f46; }
.do-dp2-sent--neg { background:#fee2e2; color:#991b1b; }
.do-dp2-sent--neu { background:var(--slate-100); color:var(--slate-500); }
.do-dp2-content { font-size:12px; color:var(--slate-700); line-height:1.7; margin-bottom:12px; background:var(--slate-50); border-radius:var(--radius-sm); padding:10px 12px; border:1px solid var(--slate-200); word-break:break-word; }
.do-dp2-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
.do-dp2-stat { background:var(--slate-50); border-radius:var(--radius-sm); padding:8px 10px; border:1px solid var(--slate-200); text-align:center; }
.do-dp2-stat-val { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-stat-lbl { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
.do-dp2-link { display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 14px; background:var(--primary); color:#fff; border-radius:var(--radius-sm); font-size:12px; font-weight:700; text-decoration:none; transition:filter .14s; margin-top:4px; }
.do-dp2-link:hover { filter:brightness(1.1); color:#fff; }

/* ─── Table section ─── */
.table-section { background:var(--bg-card); border:1px solid var(--slate-200); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:24px; }
.table-header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 20px; border-bottom:1px solid var(--slate-100); flex-wrap:wrap; }
.table-title h3 { font-size:14px; font-weight:800; color:var(--slate-900); margin:0 0 3px; letter-spacing:-.3px; }
.table-subtitle { font-size:11.5px; color:var(--slate-400); margin:0; }
.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table thead tr { background:var(--slate-50); border-bottom:1px solid var(--slate-100); }
.data-table th { padding:10px 14px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--slate-500); text-align:left; white-space:nowrap; }
.data-table td { padding:12px 14px; border-bottom:1px solid var(--slate-100); vertical-align:middle; color:var(--slate-900); }
.data-table tbody tr { cursor:pointer; transition:background .12s; }
.data-table tbody tr:hover { background:var(--slate-50); }
.data-table tbody tr:last-child td { border-bottom:none; }
.user-avatar-img,.user-avatar-fallback { width:32px; height:32px; border-radius:50%; display:block; object-fit:cover; border:1.5px solid var(--slate-200); }
.user-avatar-fallback { display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; color:var(--primary); background:var(--primary-lt); border-color:rgba(3,128,71,.25); }
.pagination { padding:12px 20px; border-top:1px solid var(--slate-100); background:var(--slate-50); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.pagination-info { font-size:11px; color:var(--slate-400); font-weight:500; }
.page-btn { min-width:28px; height:28px; padding:0 6px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; color:var(--slate-500); font-size:11px; font-weight:600; font-family:inherit; cursor:pointer; transition:all .12s; display:inline-flex; align-items:center; justify-content:center; }
.page-btn:hover:not(:disabled) { background:var(--primary-lt); border-color:rgba(3,128,71,.3); color:var(--primary); }
.page-btn.active { background:var(--primary); border-color:var(--primary); color:#fff; }
.page-btn:disabled { opacity:.4; cursor:not-allowed; }

/* ─── Search & Actions ─── */
.table-search { position:relative; }
.table-search svg { position:absolute; left:9px; top:50%; transform:translateY(-50%); width:14px; height:14px; color:var(--slate-400); }
.table-search input { padding:6px 10px 6px 30px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); font-size:12px; font-family:inherit; color:var(--slate-700); background:#fff; outline:none; transition:border-color .12s; width:180px; }
.table-search input:focus { border-color:var(--primary); }
.table-actions { display:flex; align-items:center; gap:8px; }
.actions-dropdown { position:relative; }
.actions-dropdown-btn { display:flex; align-items:center; gap:5px; padding:6px 12px; border-radius:var(--radius-sm); border:1px solid var(--slate-200); background:#fff; font-size:12px; font-weight:600; font-family:inherit; color:var(--slate-600); cursor:pointer; transition:all .12s; }
.actions-dropdown-btn:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-lt); }
.actions-dropdown-menu { position:absolute; right:0; top:calc(100% + 4px); background:#fff; border:1px solid var(--slate-200); border-radius:var(--radius); box-shadow:var(--shadow-md); min-width:160px; z-index:100; display:none; }
.actions-dropdown-menu.show { display:block; }
.actions-dropdown-item { display:flex; align-items:center; gap:8px; padding:9px 14px; font-size:12px; color:var(--slate-600); text-decoration:none; cursor:pointer; transition:background .1s; }
.actions-dropdown-item:hover { background:var(--slate-50); color:var(--slate-900); }
.actions-dropdown-item svg { width:14px; height:14px; }
.actions-dropdown-divider { height:1px; background:var(--slate-100); margin:4px 0; }

/* ─── Page header & filter ─── */
.page-header { display:flex; align-items:center; justify-content:space-between; gap:20px; margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid var(--slate-200); flex-wrap:wrap; }
.page-header-left h1 { font-size:20px; font-weight:800; color:var(--slate-900); letter-spacing:-.4px; margin:0 0 4px; }
.page-header-left p { font-size:13px; color:var(--slate-500); margin:0; }
.last-updated { display:flex; align-items:center; gap:7px; font-size:12px; color:var(--slate-400); white-space:nowrap; flex-shrink:0; background:var(--slate-50); border:1px solid var(--slate-200); border-radius:20px; padding:6px 14px; }
.last-updated svg { width:13px; height:13px; flex-shrink:0; color:var(--primary); }
.last-updated strong { color:var(--slate-600); font-weight:600; }
.filter-card { background:#fff; border:1px solid var(--slate-200); border-radius:var(--radius); box-shadow:var(--shadow-sm); padding:14px 18px; margin-bottom:24px; }
.filter-content { display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; }
.filter-group { display:flex; flex-direction:column; gap:5px; }
.filter-label { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--slate-500); }
.apply-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; border-radius:var(--radius-sm); border:none; background:var(--primary); color:#fff; font-size:12px; font-weight:700; font-family:inherit; cursor:pointer; transition:filter .13s; }
.apply-btn:hover { filter:brightness(1.1); }
.apply-btn svg { width:14px; height:14px; fill:none; stroke:currentColor; stroke-width:2; }

/* misc */
.username-link,.account-name-link { color:var(--slate-700); text-decoration:none; font-weight:600; }
.username-link:hover,.account-name-link:hover { color:var(--primary); text-decoration:underline; }
.view-profile-btn { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; color:var(--primary); text-decoration:none; padding:4px 9px; border-radius:var(--radius-sm); background:var(--primary-lt); border:1px solid rgba(3,128,71,.2); transition:all .13s; }
.view-profile-btn:hover { background:var(--primary); color:#fff; }
.activity-stat { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:600; color:var(--slate-600); }
.activity-stat svg { width:13px; height:13px; flex-shrink:0; opacity:.6; }

/* fade-up anim helpers */
.fade-up         { animation:fadeUp .36s ease-out both; }
.fade-up-d1      { animation-delay:.05s; }
.fade-up-d2      { animation-delay:.10s; }
.fade-up-d3      { animation-delay:.15s; }
.fade-up-d4      { animation-delay:.20s; }

@media(max-width:640px) { .do-panel{width:100vw;} .page-header{flex-direction:column;align-items:flex-start;} }
</style>
@endsection


@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
  $projects  = $projects ?? [];
@endphp

<div class="dashboard-container" style="font-family:var(--font-sans);">

 
  @if(!$projectId)
  <div class="alert alert-warning d-flex align-items-center gap-2" style="font-size:13px;">
    <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    No project selected. Please select a project from the sidebar.
  </div>
  @else

  {{-- Filter Card --}}
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.most-active-users') }}">
      <input type="hidden" name="project_id" id="hPid" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hSD"  value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hED"  value="{{ $endDate }}">
      <div class="filter-content">
        @if(count($projects))
        <div class="filter-group">
          <label class="filter-label">Project</label>
          <select class="filter-select" id="msProjSel" onchange="document.getElementById('hPid').value=this.value">
            @foreach($projects as $p)
            <option value="{{ $p['id'] }}" {{ ($p['id']==$projectId)?'selected':'' }}>
              {{ $p['name'] ?? $p['title'] ?? 'Project #'.$p['id'] }}
            </option>
            @endforeach
          </select>
        </div>
        @endif
        <div class="filter-group">
          <label class="filter-label">Date Range</label>
          <button type="button" class="date-picker-trigger" id="msDpTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="msDpDisplay">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <div class="filter-group" style="margin-left:auto;">
          <label class="filter-label" style="opacity:0">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Apply
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- Date Picker Modal --}}
  <div class="date-picker-modal" id="msDpModal" aria-modal="true" role="dialog">
    <div class="date-picker-overlay" onclick="MSDp.close()"></div>
    <div class="date-picker-container">
      <div class="date-picker-sidebar">
        <button class="date-preset" data-p="today">Today</button>
        <button class="date-preset" data-p="yesterday">Yesterday</button>
        <button class="date-preset" data-p="last7">Last 7 Days</button>
        <button class="date-preset" data-p="last30">Last 30 Days</button>
        <button class="date-preset" data-p="thismonth">This Month</button>
        <button class="date-preset" data-p="lastmonth">Last Month</button>
        <button class="date-preset active" data-p="custom">Custom Range</button>
      </div>
      <div class="date-picker-content">
        <div class="date-picker-header">
          <button class="nav-btn" onclick="MSDp.nav(-1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
          <div class="calendars-wrapper">
            <div class="calendar" id="msDpCal1"></div>
            <div class="calendar" id="msDpCal2"></div>
          </div>
          <button class="nav-btn" onclick="MSDp.nav(1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
        <div class="date-picker-display"><span id="msDpRangeText">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span></div>
        <div class="date-picker-footer">
          <button class="cancel-btn" onclick="MSDp.close()">Cancel</button>
          <button class="apply-date-btn" onclick="MSDp.apply()">Apply</button>
        </div>
      </div>
    </div>
  </div>

  {{-- ════ PAGE EXPORT WRAPPER ════ --}}
  <div id="pageExportArea">

  {{-- ── KPI Cards ── --}}
  <div class="row mb-3">
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 bg-primary text-white kpi-card-hover fade-up fade-up-d1">
        <div class="card-body"><div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <p class="mb-1 text-white text-opacity-75 f-12">Total Users</p>
            <h3 class="mb-0 text-white f-w-300" id="kpiTotal"><span class="sk-block" style="width:70px;height:22px;display:inline-block;"></span></h3>
            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTotalSub"><i class="ph ph-users me-1"></i>Loading…</p>
          </div>
          <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-users"></i></div></div>
        </div></div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 bg-success text-white kpi-card-hover fade-up fade-up-d2">
        <div class="card-body"><div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <p class="mb-1 text-white text-opacity-75 f-12">Total Interactions</p>
            <h3 class="mb-0 text-white f-w-300" id="kpiEng"><span class="sk-block" style="width:70px;height:22px;display:inline-block;"></span></h3>
            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiEngSub"><i class="ph ph-chart-bar me-1"></i>Loading…</p>
          </div>
          <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div></div>
        </div></div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 bg-warning text-white kpi-card-hover fade-up fade-up-d3">
        <div class="card-body"><div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <p class="mb-1 text-white text-opacity-75 f-12">Top Account</p>
            <h3 class="mb-0 text-white f-w-300" id="kpiTopAcc" style="font-size:1rem;"><span class="sk-block" style="width:70px;height:22px;display:inline-block;"></span></h3>
            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopAccSub"><i class="ph ph-crown me-1"></i>Loading…</p>
          </div>
          <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-crown"></i></div></div>
        </div></div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="card h-100 bg-danger text-white kpi-card-hover fade-up fade-up-d4">
        <div class="card-body"><div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <p class="mb-1 text-white text-opacity-75 f-12">Avg Followers</p>
            <h3 class="mb-0 text-white f-w-300" id="kpiAvgFol"><span class="sk-block" style="width:70px;height:22px;display:inline-block;"></span></h3>
            <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiAvgFolSub"><i class="ph ph-trend-up me-1"></i>Loading…</p>
          </div>
          <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-trend-up"></i></div></div>
        </div></div>
      </div>
    </div>
  </div>

  {{-- ── Page Export Toolbar ── --}}
  <div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
      <i class="ph ph-export"></i>
      <span>Export Page</span>
      <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Donut + User List</span>
    </div>
    <div class="page-export-bar-right">
      <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
              onclick="MAUExport.run('pdf', this)" title="Export as PDF">
        <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
      </button>
      <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
              onclick="MAUExport.run('image', this)" title="Export as PNG">
        <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
      </button>
    </div>
  </div>

  {{-- ── Donut Chart ── --}}
  <div class="row">
    <div class="col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
        <div id="card-export-donut">
          <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
              <div>
                <h6 class="mb-0">Top 5 Engagement Share</h6>
                <small class="text-muted">Click to view user detail</small>
              </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <div id="donutLegend" class="donut-legend"></div>
              <div class="d-flex gap-1" data-html2canvas-ignore="true">
                <button class="card-exp-btn card-exp-btn-pdf"
                        onclick="MAUExport.runCard('card-export-donut','donut','pdf',this)" title="Export PDF">
                  <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                </button>
                <button class="card-exp-btn card-exp-btn-img"
                        onclick="MAUExport.runCard('card-export-donut','donut','image',this)" title="Export PNG">
                  <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-container" style="height:340px;">
              <div id="loadingDonut" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:8px;">
                <div class="spin-ring"></div><span style="font-size:11px;font-weight:600;color:var(--slate-400)">Loading…</span>
              </div>
              <div id="donutChart" style="width:100%;height:340px;display:none;"></div>
              <div id="donutEmpty" style="display:none;" class="chart-empty"><i class="ph ph-chart-donut"></i><span>No data available</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Ranked List ── --}}
  <div class="row">
    <div class="col-12">
      <div class="card mb-3" style="animation:fadeUp .38s ease-out .22s both;">
        <div id="card-export-list">
          <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-star f-18 text-primary"></i></div>
              <div>
                <h6 class="mb-0">Top Contributors</h6>
                <small class="text-muted">Ranked by total interactions</small>
              </div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-light-primary text-primary" id="badgeTotal">Loading…</span>
              <div class="d-flex gap-1" data-html2canvas-ignore="true">
                <button class="card-exp-btn card-exp-btn-pdf"
                        onclick="MAUExport.runCard('card-export-list','list','pdf',this)" title="Export PDF">
                  <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
                </button>
                <button class="card-exp-btn card-exp-btn-img"
                        onclick="MAUExport.runCard('card-export-list','list','image',this)" title="Export PNG">
                  <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
                </button>
              </div>
            </div>
          </div>
          <div id="userList" class="p-0"><div class="spinner-state"><div class="spin-ring"></div>Loading users…</div></div>
          <div id="listPagArea"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Data Table ── --}}
  <div class="table-section" style="animation:fadeUp .38s ease-out .26s both;">
    <div class="table-header">
      <div class="table-title">
        <h3>Active Users Ranking</h3>
        <p class="table-subtitle">Sorted by total activity — click user to view detailed analytics</p>
      </div>
      <div class="table-actions">
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="searchInput" placeholder="Search users…" onkeyup="filterTable()">
        </div>
        <div class="actions-dropdown">
          <button class="actions-dropdown-btn" onclick="toggleActionsDropdown(event)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
            Actions
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="actions-dropdown-menu" id="actionsDropdownMenu">
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault();exportCSV()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>Export to CSV
            </a>
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault();refreshData()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>Refresh Data
            </a>
            <div class="actions-dropdown-divider"></div>
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault();printTable()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>Print Table
            </a>
          </div>
        </div>
      </div>
    </div>
    <div id="tableLoading" class="sk-block" style="height:360px;border-radius:0;"></div>
    <div id="tableWrapper" style="display:none;overflow-x:auto;"></div>
    <div id="paginationWrapper" class="pagination" style="display:none;"></div>
    <div id="emptyState" style="display:none;text-align:center;padding:60px 20px;color:var(--slate-400);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:44px;height:44px;margin:0 auto 14px;opacity:.3;display:block;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      <p style="font-size:14px;font-weight:500;margin:0;">No user data found for the selected date range.</p>
    </div>
  </div>

  </div>{{-- /pageExportArea --}}
  @endif
</div>

{{-- Export Toast --}}
<div class="export-toast" id="exportToast">
  <i class="ph ph-check-circle" id="exportToastIcon"></i>
  <span id="exportToastMsg">Exporting…</span>
</div>

{{-- ════ SLIDE PANEL ════ --}}
<div class="do-panel-overlay" id="panelOverlay" onclick="Panel.close()"></div>
<div class="do-panel" id="sntPanel">
  <div class="do-panel-header">
    <span class="do-panel-title" id="panelTitle">User Profile</span>
    <button class="do-panel-close" onclick="Panel.close()"><i class="ph ph-x"></i></button>
  </div>
  <div class="up-body" id="panelBody"></div>
  {{-- Tweet detail sub-panel --}}
  <div class="do-detail-panel" id="detailPanel">
    <div class="do-dp2-header">
      <button class="do-dp2-back" onclick="Detail.close()"><i class="ph ph-caret-left"></i></button>
      <span class="do-dp2-title" id="detailTitle">Tweet Detail</span>
      <button class="do-panel-close" onclick="Panel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-dp2-body" id="detailBody"></div>
  </div>
</div>

@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
const MSCfg = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
};

const DONUT_COLORS = ['#038047','#273B4A','#F59E0B','#06B6D4','#EF4444'];
let allData    = [];
let curPage    = 1;
let donutInst  = null;
const PP_LIST  = 10;  // ranked list items per page
const PP_TABLE = 20;  // table items per page
let listPage   = 1;
let tablePage  = 1;

/* ══════════════════════════════════════════════════════
   UTILS
══════════════════════════════════════════════════════ */
const $  = id => document.getElementById(id);
const numF  = n => parseInt(n||0).toLocaleString('id-ID');
const numK  = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const eng   = u => parseInt(u.engagement || (parseInt(u.mentions||0)+parseInt(u.replies||0)+parseInt(u.retweets||0)) || u.posts || u.y || 0);
const esc   = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const escA  = s => (s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;');
const escH  = s => (s||'').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
const getInit = n => { if(!n||n==='Unknown')return'?'; const p=n.trim().split(/\s+/); return p.length===1?p[0].substring(0,2).toUpperCase():(p[0][0]+p[p.length-1][0]).toUpperCase(); };
const avUrl   = u => { const a=u.profile_image_url||u.profile_image||u.avatar||''; if(a&&!a.startsWith('/external'))return a; const h=u.username||u.screen_name||''; return h?`https://unavatar.io/twitter/${h}`:''; };

/* ══════════════════════════════════════════════════════
   DATE PICKER
══════════════════════════════════════════════════════ */
const MSDp = (() => {
  let ds=null,de=null,m1=new Date(),m2=new Date(),pickStart=true;
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];
  function init(){
    const si=$('hSD'),ei=$('hED');
    ds=si?.value?new Date(si.value):(()=>{const d=new Date();d.setDate(d.getDate()-6);return d;})();
    de=ei?.value?new Date(ei.value):new Date();
    m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);render();
    document.getElementById('msDpTrigger').addEventListener('click',open);
    document.querySelectorAll('.date-preset').forEach(b=>b.addEventListener('click',onPreset));
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&$('msDpModal')?.classList.contains('show'))close();});
  }
  function open(){const m=$('msDpModal');m.style.display='flex';requestAnimationFrame(()=>m.classList.add('show'));render();}
  function close(){const m=$('msDpModal');m.classList.remove('show');setTimeout(()=>{m.style.display='none';},250);}
  function apply(){
    $('hSD').value=fmt(ds);$('hED').value=fmt(de);
    $('msDpDisplay').textContent=_d(ds)+' – '+_d(de);
    close();document.getElementById('filterForm').submit();
  }
  function nav(dir){m1.setMonth(m1.getMonth()+dir);m2.setMonth(m2.getMonth()+dir);render();}
  function onPreset(e){
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));e.target.classList.add('active');
    const t=new Date();t.setHours(0,0,0,0);
    switch(e.target.dataset.p){
      case 'today':     ds=new Date(t);de=new Date(t);break;
      case 'yesterday': ds=new Date(t);ds.setDate(t.getDate()-1);de=new Date(ds);break;
      case 'last7':     de=new Date(t);ds=new Date(t);ds.setDate(t.getDate()-6);break;
      case 'last30':    de=new Date(t);ds=new Date(t);ds.setDate(t.getDate()-29);break;
      case 'thismonth': ds=new Date(t.getFullYear(),t.getMonth(),1);de=new Date(t);break;
      case 'lastmonth': ds=new Date(t.getFullYear(),t.getMonth()-1,1);de=new Date(t.getFullYear(),t.getMonth(),0);break;
    }
    if(e.target.dataset.p!=='custom'){m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);}
    upd();render();
  }
  function render(){_cal('msDpCal1',m1);_cal('msDpCal2',m2);upd();}
  function _cal(id,month){
    const el=$(id);if(!el)return;
    const y=month.getFullYear(),mn=month.getMonth();
    const first=new Date(y,mn,1),last=new Date(y,mn+1,0),prevL=new Date(y,mn,0);
    const today=new Date();today.setHours(0,0,0,0);
    let h=`<div class="calendar-month">${MN[mn]} ${y}</div><div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
    for(let i=0;i<first.getDay();i++) h+=`<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const date=new Date(y,mn,d);date.setHours(0,0,0,0);
      let cls='calendar-day';
      if(sD(date,today))cls+=' today';if(date>today)cls+=' disabled';
      if(ds&&de){if(sD(date,ds)||sD(date,de))cls+=' selected';else if(date>ds&&date<de)cls+=' in-range';}
      h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>';el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn=>{
      btn.addEventListener('click',function(){
        const d=new Date(this.dataset.date);d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-p="custom"]').classList.add('active');
        if(pickStart||d<ds){ds=d;de=d;pickStart=false;}else{if(d>=ds)de=d;else{de=ds;ds=d;}pickStart=true;}
        upd();render();
      });
    });
  }
  function upd(){const el=$('msDpRangeText');if(el&&ds&&de)el.textContent=_d(ds)+' – '+_d(de);}
  function fmt(d){return`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;}
  function sD(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
  function _d(d){const M=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];return`${M[d.getMonth()]} ${String(d.getDate()).padStart(2,'0')}, ${d.getFullYear()}`;}
  return {init,open,close,apply,nav};
})();

/* ══════════════════════════════════════════════════════
   DATA LOAD
══════════════════════════════════════════════════════ */
let dataLoaded = false;

async function loadData() {
  if (dataLoaded) return;
  dataLoaded = true;
  try {
    const res    = await fetch(`/mk/api/x/most-active-users?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`);
    const result = await res.json();
    let rows = null;
    if      (Array.isArray(result?.data?.data))  rows = result.data.data;
    else if (Array.isArray(result?.data?.users)) rows = result.data.users;
    else if (Array.isArray(result?.data?.items)) rows = result.data.items;
    else if (Array.isArray(result?.data))        rows = result.data;
    else if (Array.isArray(result?.users))       rows = result.users;
    else if (Array.isArray(result?.items))       rows = result.items;
    else if (Array.isArray(result))              rows = result;
    else if (result?.data && typeof result.data === 'object') {
      const vals = Object.values(result.data);
      if (vals.length > 0 && typeof vals[0] === 'object') rows = vals;
    }
    if (rows && rows.length > 0) {
      allData = rows.sort((a,b) => eng(b)-eng(a));
      updateKpi();
      renderDonut();
      renderRankedList();
      renderTable();
      updatePagination();
      $('tableLoading').style.display      = 'none';
      $('tableWrapper').style.display      = 'block';
      $('paginationWrapper').style.display = 'flex';
    } else {
      _showEmpty();
    }
  } catch(err) {
    console.error('loadData:', err);
    _showEmpty(true);
  }
}

function _showEmpty(isError) {
  $('loadingDonut').style.display = 'none';
  $('donutEmpty').style.display   = 'flex';
  $('userList').innerHTML = isError
    ? '<div class="spinner-state" style="padding:40px;"><i class="ph ph-warning" style="font-size:32px;color:var(--slate-300);"></i><span>Failed to load data.</span></div>'
    : '<div class="spinner-state" style="padding:40px;"><i class="ph ph-users" style="font-size:32px;color:var(--slate-300);"></i><span>No user data found.</span></div>';
  $('tableLoading').style.display = 'none';
  $('emptyState').style.display   = 'block';
  ['kpiTotal','kpiEng','kpiAvgFol'].forEach(id=>{ const e=$(id); if(e)e.textContent='0'; });
  $('kpiTopAcc').textContent = '–';
  $('badgeTotal').textContent = '0 users';
}

/* ══════════════════════════════════════════════════════
   KPI CARDS
══════════════════════════════════════════════════════ */
function updateKpi() {
  const n   = allData.length;
  const tot = allData.reduce((s,u) => s+eng(u), 0);
  $('kpiTotal').textContent    = numF(n);
  $('kpiTotalSub').innerHTML   = `<i class="ph ph-users me-1"></i>${n} accounts tracked`;
  $('kpiEng').textContent      = numF(tot);
  $('kpiEngSub').innerHTML     = `<i class="ph ph-chart-bar me-1"></i>mentions + replies + retweets`;
  if (n) {
    const top = allData[0];
    $('kpiTopAcc').textContent   = top.name || ('@'+(top.username||''));
    $('kpiTopAccSub').innerHTML  = `<i class="ph ph-crown me-1"></i>${numF(eng(top))} interactions`;
    const avgF = Math.round(allData.reduce((s,u)=>s+parseInt(u.followers||u.followers_count||0),0)/n);
    $('kpiAvgFol').textContent  = numK(avgF);
    $('kpiAvgFolSub').innerHTML = `<i class="ph ph-trend-up me-1"></i>per user`;
  }
  $('badgeTotal').textContent = n + ' users';
}

/* ══════════════════════════════════════════════════════
   DONUT CHART
══════════════════════════════════════════════════════ */
function renderDonut() {
  const ld=$('loadingDonut'), ch=$('donutChart'), em=$('donutEmpty'), lg=$('donutLegend');
  const top5 = allData.slice(0,5);
  if (!top5.length) { ld.style.display='none'; em.style.display='flex'; return; }
  ld.style.display='none'; ch.style.display='block';
  const data  = top5.map((u,i) => ({
    name:  u.name || ('@'+(u.username||'')),
    value: eng(u), uname: u.username||'',
    itemStyle: { color:DONUT_COLORS[i], borderColor:'#fff', borderWidth:3 },
  }));
  const total = data.reduce((s,d)=>s+d.value, 0);
  if (donutInst) { try { donutInst.dispose(); } catch(e){} }
  donutInst = echarts.init(ch, null, { renderer:'canvas' });
  donutInst.setOption({
    backgroundColor: 'transparent',
    animation: true, animationDuration:700,
    tooltip:{
      trigger:'item', backgroundColor:'#1e293b', borderColor:'#334155', borderWidth:1,
      padding:[10,14], textStyle:{color:'#f8fafc',fontSize:12},
      extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.2);',
      formatter: p => `<div style="font-weight:700;margin-bottom:5px;font-size:13px;">${p.name}<br><span style="color:#94a3b8;font-weight:400;font-size:11px;">@${p.data.uname}</span></div>
          <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;"><span style="color:#94a3b8;">Engagements</span><span style="font-weight:700;">${numF(p.value)}</span></div>
          <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;"><span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#038047;">${total?(p.value/total*100).toFixed(1):'0'}%</span></div>`,
    },
    series:[{
      type:'pie', radius:['46%','64%'], center:['50%','52%'],
      avoidLabelOverlap:true, minAngle:8,
      label:{
        show:true, position:'outside', fontFamily:"'DM Sans',sans-serif", fontSize:11.5, color:'#475569',
        formatter: p => {
          const n = p.name.length>13?p.name.slice(0,12)+'…':p.name;
          const pct = total?(p.value/total*100).toFixed(1):'0';
          return `{n|${n}}\n{v|${numK(p.value)}} {pct|${pct}%}`;
        },
        rich:{
          n:  { fontWeight:700, fontSize:12, color:'#1e293b', lineHeight:20 },
          v:  { fontWeight:700, fontSize:11, color:'#038047', lineHeight:17, backgroundColor:'rgba(3,128,71,.08)', borderRadius:3, padding:[1,4] },
          pct:{ fontWeight:600, fontSize:10, color:'#64748b', lineHeight:17 },
        },
      },
      labelLine:{ show:true, length:14, length2:18, smooth:.3, lineStyle:{color:'#c4cdd8',width:1.2} },
      emphasis:{ scale:true, scaleSize:4, itemStyle:{shadowBlur:8,shadowColor:'rgba(0,0,0,.1)'} },
      data,
    }],
    graphic:[
      { type:'text', left:'center', top:'47%', z:100, style:{ text:numK(total), fill:'#0f172a', font:"700 24px 'DM Sans',sans-serif", textAlign:'center' } },
      { type:'text', left:'center', top:'55%', z:100, style:{ text:'TOTAL', fill:'#94a3b8', font:"600 9px 'DM Sans',sans-serif", textAlign:'center', letterSpacing:2 } },
    ],
  });
  donutInst.on('click', p => {
    if (p.componentType === 'series') {
      const u = top5.find(x=>(x.name||('@'+(x.username||'')))===p.name);
      if (u) Panel.open(u);
    }
  });
  window.addEventListener('resize', ()=>{ try{ donutInst.resize(); }catch(e){} });
  lg.innerHTML = top5.map((u,i)=>`<span class="donut-leg-item" onclick="Panel.open(allData[${i}])"><span class="donut-dot" style="background:${DONUT_COLORS[i]}"></span>${esc((u.name||u.username||'').substring(0,15))}</span>`).join('');
}

/* ══════════════════════════════════════════════════════
   RANKED LIST (ht-list style)
══════════════════════════════════════════════════════ */
function renderRankedList() {
  const el=$('userList'), pg=$('listPagArea');
  if (!allData.length) { el.innerHTML='<div class="spinner-state" style="padding:40px;"><i class="ph ph-users" style="font-size:32px;color:var(--slate-300);"></i><span>No data</span></div>'; pg.innerHTML=''; return; }
  const total=allData.length, pages=Math.ceil(total/PP_LIST), start=(listPage-1)*PP_LIST;
  const items=allData.slice(start, start+PP_LIST), mx=eng(allData[0])||1;
  let h='<div class="ht-list">';
  items.forEach((u,i) => {
    const rk=start+i+1, rc=rk<=3?` ht-rank--${rk}`:'';
    const pct=Math.round((eng(u)/mx)*100);
    const name=u.name||u.username||'Unknown', uname=u.username||'';
    const src=avUrl(u), init=getInit(name);
    const avH=src
      ? `<img src="${esc(src)}" onerror="this.src=''" alt="">`
      : `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--primary-lt);color:var(--primary);font-weight:700;font-size:11px;">${esc(init)}</div>`;
    h+=`<div class="ht-item" onclick="Panel.open(allData[${start+i}])">
      <div class="ht-rank${rc}">${rk}</div>
      <div class="ht-av">${avH}</div>
      <div class="ht-info">
        <div class="ht-name">${esc(name)}</div>
        <div class="ht-handle">@${esc(uname)} · <span style="color:var(--slate-600);font-weight:600;">${numF(u.followers||0)} followers</span></div>
      </div>
      <div class="ht-bar-wrap"><div class="ht-bar-fill" style="width:${pct}%;"></div></div>
      <div class="ht-count">${numF(eng(u))}</div>
    </div>`;
  });
  h+='</div>';
  el.innerHTML = h;
  if (pages<=1) { pg.innerHTML=''; return; }
  const fr=start+1, to=Math.min(start+PP_LIST,total);
  let b='';
  b+=`<button class="tme-pag-btn" ${listPage<=1?'disabled':''} onclick="goListPage(${listPage-1})"><i class="ph ph-caret-left"></i></button>`;
  for(let i=1;i<=pages;i++){
    if(i===1||i===pages||(i>=listPage-2&&i<=listPage+2))
      b+=`<button class="tme-pag-btn${i===listPage?' is-active':''}" onclick="goListPage(${i})">${i}</button>`;
    else if(i===listPage-3||i===listPage+3)
      b+=`<span class="tme-pag-btn" style="cursor:default;opacity:.4;">…</span>`;
  }
  b+=`<button class="tme-pag-btn" ${listPage>=pages?'disabled':''} onclick="goListPage(${listPage+1})"><i class="ph ph-caret-right"></i></button>`;
  pg.innerHTML=`<div class="tme-pagination"><span class="tme-pag-info">${fr}–${to} of ${total}</span><div class="tme-pag-controls">${b}</div></div>`;
}
function goListPage(p) {
  const pages=Math.ceil(allData.length/PP_LIST);
  if(p<1||p>pages) return;
  listPage=p; renderRankedList();
  $('userList')?.scrollIntoView({behavior:'smooth',block:'nearest'});
}

/* ══════════════════════════════════════════════════════
   TABLE
══════════════════════════════════════════════════════ */
function renderTable() {
  const si=(tablePage-1)*PP_TABLE;
  const cd=allData.slice(si, si+PP_TABLE);
  let html=`<table class="data-table"><thead><tr>
    <th>RANK</th><th>AVATAR</th><th>USERNAME</th><th>NAME</th>
    <th>FOLLOWERS</th><th>MENTIONS</th><th>REPLIES</th><th>RETWEETS</th>
    <th style="text-align:center;">TOTAL</th><th></th>
  </tr></thead><tbody>`;
  cd.forEach((u,i) => {
    const rank=si+i+1, name=u.name||u.username||'Unknown', uname=u.username||'';
    const src=avUrl(u), init=getInit(name);
    const ujson=escA(JSON.stringify(u));
    const sM=parseInt(u.mentions||0), sR=parseInt(u.replies||0), sRt=parseInt(u.retweets||0);
    const avH=`<img src="${esc(src)}" alt="${esc(name)}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"><div class="user-avatar-fallback" style="display:none;">${esc(init)}</div>`;
    html+=`<tr onclick="Panel.open(allData[${si+i}])">
      <td><strong>${rank}</strong></td>
      <td>${avH}</td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="username-link" onclick="event.stopPropagation();">@${uname}</a></td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="account-name-link" onclick="event.stopPropagation();">${esc(name)}</a></td>
      <td class="activity-stat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>${numF(u.followers||0)}</td>
      <td style="color:var(--slate-600);font-weight:600;">${numF(u.mentions||0)}</td>
      <td style="color:var(--slate-600);font-weight:600;">${numF(u.replies||0)}</td>
      <td style="color:var(--slate-600);font-weight:600;">${numF(u.retweets||0)}</td>
      <td style="text-align:center;"><span class="activity-stat"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>${numF(eng(u))}</span></td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="view-profile-btn" onclick="event.stopPropagation();">View</a></td>
    </tr>`;
  });
  html+='</tbody></table>';
  $('tableWrapper').innerHTML=html;
}
function getPageRange(cur,total){
  if(total<=7) return Array.from({length:total},(_,i)=>i+1);
  if(cur<=4) return [1,2,3,4,5,'...',total];
  if(cur>=total-3) return [1,'...',total-4,total-3,total-2,total-1,total];
  return [1,'...',cur-1,cur,cur+1,'...',total];
}
function updatePagination(){
  const tp=Math.ceil(allData.length/PP_TABLE), w=$('paginationWrapper');
  const from=allData.length?(tablePage-1)*PP_TABLE+1:0, to=Math.min(tablePage*PP_TABLE,allData.length);
  let html=`<div class="pagination-info">Showing ${numF(from)}–${numF(to)} of ${numF(allData.length)} users</div><div style="display:flex;align-items:center;gap:4px;">`;
  html+=`<button class="page-btn" onclick="changePage(${tablePage-1})" ${tablePage===1?'disabled':''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg></button>`;
  getPageRange(tablePage,tp).forEach(p=>{
    html+=p==='...'?`<button class="page-btn" disabled style="cursor:default;">…</button>`:`<button class="page-btn${p===tablePage?' active':''}" onclick="changePage(${p})">${p}</button>`;
  });
  html+=`<button class="page-btn" onclick="changePage(${tablePage+1})" ${tablePage===tp?'disabled':''}><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="9 18 15 12 9 6"/></svg></button></div>`;
  w.innerHTML=html; w.style.display=allData.length>0?'flex':'none';
}
function changePage(p){
  const tp=Math.ceil(allData.length/PP_TABLE);
  if(p<1||p>tp)return;
  tablePage=p; renderTable(); updatePagination();
  document.querySelector('.table-section')?.scrollIntoView({behavior:'smooth',block:'start'});
}
function filterTable(){
  const term=$('searchInput').value.toLowerCase();
  if(!term){tablePage=1;renderTable();updatePagination();return;}
  const filtered=allData.filter(u=>((u.name||'')+' '+(u.username||'')).toLowerCase().includes(term));
  let html=`<table class="data-table"><thead><tr><th>RANK</th><th>AVATAR</th><th>USERNAME</th><th>NAME</th><th>FOLLOWERS</th><th>MENTIONS</th><th>REPLIES</th><th>RETWEETS</th><th>TOTAL</th><th></th></tr></thead><tbody>`;
  filtered.forEach((u,i)=>{
    const name=u.name||u.username||'Unknown', uname=u.username||'';
    const src=avUrl(u), init=getInit(name);
    const avH=`<img src="${esc(src)}" alt="${esc(name)}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"><div class="user-avatar-fallback" style="display:none;">${esc(init)}</div>`;
    html+=`<tr onclick="Panel.open(allData[${allData.indexOf(u)}])">
      <td><strong>${i+1}</strong></td><td>${avH}</td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="username-link" onclick="event.stopPropagation();">@${uname}</a></td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="account-name-link" onclick="event.stopPropagation();">${esc(name)}</a></td>
      <td>${numF(u.followers||0)}</td>
      <td style="color:var(--slate-600);font-weight:600;">${numF(u.mentions||0)}</td>
      <td style="color:var(--slate-600);font-weight:600;">${numF(u.replies||0)}</td>
      <td style="color:var(--slate-600);font-weight:600;">${numF(u.retweets||0)}</td>
      <td style="text-align:center;">${numF(eng(u))}</td>
      <td><a href="https://twitter.com/${uname}" target="_blank" class="view-profile-btn" onclick="event.stopPropagation();">View</a></td>
    </tr>`;
  });
  html+='</tbody></table>';
  $('tableWrapper').innerHTML=html;
  $('paginationWrapper').style.display='none';
}

/* ══════════════════════════════════════════════════════
   SLIDE PANEL
══════════════════════════════════════════════════════ */
const Panel = (() => {
  let _u=null, _mentions=[], _hasMore=false, _apiStart=0, _pg=1;
  const _PP=10;
  let _abort=null;

  function open(user) {
    _u=user; _mentions=[]; _hasMore=false; _apiStart=0; _pg=1;
    if(_abort) try{_abort.abort();}catch(e){}
    _abort = new AbortController();
    const name = user.name||user.username||'User';
    $('panelTitle').textContent = name+' — Profile';
    $('panelBody').innerHTML    = _profileHTML(user)+'<div id="upMentions"><div class="up-loading"><div class="spin-ring"></div><span>Loading tweets…</span></div></div>';
    $('panelOverlay').classList.remove('hiding'); $('panelOverlay').classList.add('show');
    $('sntPanel').classList.remove('hiding');     $('sntPanel').classList.add('show');
    $('detailPanel').classList.remove('show');
    _fetchMentions();
  }

  function close() {
    if(_abort) try{_abort.abort();}catch(e){}
    _abort=null;
    $('panelOverlay').classList.add('hiding'); $('sntPanel').classList.add('hiding');
    setTimeout(()=>{ $('panelOverlay').classList.remove('show','hiding'); $('sntPanel').classList.remove('show','hiding'); $('panelBody').innerHTML=''; }, 260);
  }

  async function _fetchMentions() {
    try {
      const uname=_u.username||_u.screen_name||'';
      const url=`/mk/api/x/user-detailed-mentions?project_id=${MSCfg.pid}&username=${encodeURIComponent(uname)}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}&api_start=${_apiStart}&stat_mentions=${_u.mentions||0}&stat_replies=${_u.replies||0}&stat_retweets=${_u.retweets||0}`;
      const r=await fetch(url,{signal:_abort?.signal});
      if(!r.ok) throw new Error('HTTP '+r.status);
      const j=await r.json();
      if(!j.success){_renderMentions();return;}
      _mentions = [..._mentions,...(j.data?.mentions||[])];
      _hasMore  = j.data?.has_more||false;
      _apiStart = j.data?.next_api_start||0;
      _renderMentions();
    } catch(e) { if(e.name!=='AbortError'){console.error(e);_renderMentions();} }
  }

  function _profileHTML(u) {
    const name=u.name||u.username||'Unknown', uname=u.username||u.screen_name||'', src=avUrl(u), init=getInit(name);
    const fol=parseInt(u.followers||u.followers_count||0);
    const sM=parseInt(u.mentions||0), sR=parseInt(u.replies||0), sRt=parseInt(u.retweets||0), tot=eng(u);
    const avH=src
      ? `<img src="${esc(src)}" class="up-av" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" alt="${esc(name)}"><div class="up-av-fb" style="display:none;">${esc(init)}</div>`
      : `<div class="up-av-fb">${esc(init)}</div>`;
    return `<div class="up-banner"></div>
    <div class="up-profile">
      <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:10px">
        ${avH}
        <a href="https://twitter.com/${esc(uname)}" target="_blank" rel="noopener" style="font-size:11px;font-weight:700;color:#fff;background:#0f1419;padding:6px 14px;border-radius:99px;text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:4px">
          <i class="ph ph-arrow-square-out" style="font-size:12px"></i>View on X
        </a>
      </div>
      <div class="up-name">${esc(name)}</div>
      <div class="up-handle"><a href="https://twitter.com/${esc(uname)}" target="_blank" rel="noopener">@${esc(uname)}</a> · <strong>${numF(fol)}</strong> Followers</div>
      <div class="up-stats">
        <div class="up-stat"><div class="up-stat-val">${numF(sM)}</div><div class="up-stat-lbl">Mentions</div></div>
        <div class="up-stat"><div class="up-stat-val">${numF(sR)}</div><div class="up-stat-lbl">Replies</div></div>
        <div class="up-stat"><div class="up-stat-val">${numF(sRt)}</div><div class="up-stat-lbl">Retweets</div></div>
      </div>
      <div class="up-total">
        <span class="up-total-lbl"><i class="ph ph-chart-bar"></i> Total Engagement</span>
        <span class="up-total-val">${numF(tot)}</span>
      </div>
    </div>`;
  }

  function _renderMentions() {
    const el=$('upMentions'); if(!el) return;
    if (!_mentions.length && !_hasMore) {
      el.innerHTML='<div class="up-empty"><i class="ph ph-chat-circle-dots"></i><span style="font-size:12px">No mentions found</span></div>'; return;
    }
    const total=_mentions.length, pages=Math.ceil(total/_PP), si=(_pg-1)*_PP, ei=Math.min(si+_PP,total), page=_mentions.slice(si,ei);
    let h=`<div class="up-mentions-head"><h6><i class="ph ph-chat-circle-dots me-1"></i>Tweets & Mentions</h6><span class="up-mention-cnt">${_hasMore?total+' loaded · more':total+' found'}</span></div>`;
    page.forEach(m=>{ h+=_tweetCard(m); });
    if (total>_PP || _hasMore) {
      const isLast=_pg>=pages, canLoad=isLast&&_hasMore;
      h+=`<div class="up-pag"><span class="up-pag-info">Page ${_pg}/${pages}${_hasMore?'+':''} · ${si+1}–${ei} of ${total}${_hasMore?'+':''}</span><div class="up-pag-btns">`;
      h+=`<button class="up-pag-btn" ${_pg<=1?'disabled':''} onclick="Panel.goPage(${_pg-1})"><i class="ph ph-caret-left"></i></button>`;
      for(let p=Math.max(1,_pg-2);p<=Math.min(pages,_pg+2);p++)
        h+=`<button class="up-pag-btn${p===_pg?' is-active':''}" onclick="Panel.goPage(${p})">${p}</button>`;
      if(canLoad)
        h+=`<button class="up-pag-btn" id="upLoadMore" onclick="Panel.loadMore()" style="padding:0 8px;background:var(--primary-lt);border-color:rgba(3,128,71,.2);color:var(--primary)">More <i class="ph ph-caret-right"></i></button>`;
      else
        h+=`<button class="up-pag-btn" ${_pg>=pages?'disabled':''} onclick="Panel.goPage(${_pg+1})"><i class="ph ph-caret-right"></i></button>`;
      h+=`</div></div>`;
    }
    el.innerHTML=h;
  }

  function _tweetCard(m) {
    const sent=(m.sentiment||'neutral').toLowerCase(), sentCls=sent.includes('pos')?'pos':sent.includes('neg')?'neg':'neu';
    const sentLbl=sent.includes('pos')?'Positive':sent.includes('neg')?'Negative':'Neutral';
    const ts=m.timestamp||m.created_at||''; let dtStr='';
    if(ts){const d=new Date(ts);if(!isNaN(d))dtStr=d.toLocaleString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit',timeZone:'Asia/Jakarta'})+' WIB';}
    const likes=parseInt(m.likes||m.num_likes||0), rts=parseInt(m.retweets||m.num_shares||0), reps=parseInt(m.replies||m.num_comments||0);
    const text=_linkify(m.text||''), tUrl=(m.url&&m.url!=='#')?m.url:'';
    const mJson=esc(JSON.stringify(m).replace(/'/g,'&#39;'));
    const viewLink=tUrl?`<a href="${esc(tUrl)}" target="_blank" rel="noopener" class="up-tw-link" onclick="event.stopPropagation()"><i class="ph ph-arrow-square-out"></i>View</a>`:'';
    return `<div class="up-tweet" onclick="Detail.open(JSON.parse(this.dataset.m))" data-m='${mJson}'>
      <div class="up-tw-head"><span class="up-tw-author">${esc(m.author_name||m.author||_u?.username||'')}</span><span class="up-tw-time">${dtStr}</span></div>
      <div class="up-tw-text">${text}</div>
      <div class="up-tw-foot">
        <span class="up-tw-sent up-tw-sent--${sentCls}">${sentLbl}</span>
        <span class="up-tw-metric"><i class="ph ph-heart"></i>${numF(likes)}</span>
        <span class="up-tw-metric"><i class="ph ph-repeat"></i>${numF(rts)}</span>
        <span class="up-tw-metric"><i class="ph ph-chat-circle"></i>${numF(reps)}</span>
        ${viewLink}
      </div>
    </div>`;
  }

  function goPage(p){const tp=Math.ceil(_mentions.length/_PP);if(p<1||p>tp)return;_pg=p;_renderMentions();}

  async function loadMore(){
    const btn=$('upLoadMore');
    if(btn){btn.disabled=true;btn.innerHTML='<div class="spin-ring" style="width:14px;height:14px;border-width:2px"></div>';}
    try{
      const uname=_u?.username||_u?.screen_name||'';
      const url=`/mk/api/x/user-detailed-mentions?project_id=${MSCfg.pid}&username=${encodeURIComponent(uname)}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}&api_start=${_apiStart}&stat_mentions=${_u?.mentions||0}&stat_replies=${_u?.replies||0}&stat_retweets=${_u?.retweets||0}`;
      const r=await fetch(url,{signal:_abort?.signal});const j=await r.json();
      if(j.success&&j.data){_mentions=[..._mentions,...(j.data.mentions||[])];_hasMore=j.data.has_more||false;_apiStart=j.data.next_api_start||0;const tp=Math.ceil(_mentions.length/_PP);_pg=Math.min(_pg+1,tp);}
      _renderMentions();
    }catch(e){if(btn){btn.disabled=false;btn.innerHTML='More <i class="ph ph-caret-right"></i>';}}
  }

  document.addEventListener('keydown', e=>{ if(e.key==='Escape'&&$('sntPanel')?.classList.contains('show')) close(); });
  return { open, close, goPage, loadMore };
})();

/* ══════════════════════════════════════════════════════
   DETAIL SUB-PANEL
══════════════════════════════════════════════════════ */
const Detail = (() => {
  function open(m) {
    const sent=(m.sentiment||'neutral').toLowerCase(), sentCls=sent.includes('pos')?'pos':sent.includes('neg')?'neg':'neu';
    const sentLbl=sent.includes('pos')?'Positive':sent.includes('neg')?'Negative':'Neutral';
    const ts=m.timestamp||m.created_at||''; let dtStr='';
    if(ts){const d=new Date(ts);if(!isNaN(d))dtStr=d.toLocaleString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit',timeZone:'Asia/Jakarta'})+' WIB';}
    const likes=parseInt(m.likes||m.num_likes||0), rts=parseInt(m.retweets||m.num_shares||0), reps=parseInt(m.replies||m.num_comments||0);
    const text=_linkify(m.text||''), handle=m.author||'';
    const tUrl=(m.url&&m.url!=='#')?m.url:`https://twitter.com/${encodeURIComponent(handle)}`;
    $('detailTitle').textContent='Tweet Detail';
    $('detailBody').innerHTML=`
      <div class="do-dp2-meta">${dtStr?`<i class="ph ph-clock me-1"></i>${dtStr}`:''}</div>
      <div class="do-dp2-sent do-dp2-sent--${sentCls}"><i class="ph ph-circle-fill" style="font-size:6px"></i> ${sentLbl}</div>
      <div class="do-dp2-content">${text||'<em style="color:var(--slate-400)">No content</em>'}</div>
      <div class="do-dp2-stats">
        <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(likes)}</div><div class="do-dp2-stat-lbl">Likes</div></div>
        <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(rts)}</div><div class="do-dp2-stat-lbl">Retweets</div></div>
        <div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(reps)}</div><div class="do-dp2-stat-lbl">Replies</div></div>
      </div>
      <a href="${esc(tUrl)}" target="_blank" rel="noopener" class="do-dp2-link"><i class="ph ph-arrow-square-out"></i>Open on X</a>`;
    $('detailPanel').classList.add('show');
  }
  function close(){ $('detailPanel').classList.remove('show'); }
  return { open, close };
})();

/* ══════════════════════════════════════════════════════
   LINKIFY
══════════════════════════════════════════════════════ */
function _linkify(raw){
  if(!raw)return'<em style="color:var(--slate-400)">No content</em>';
  let t=raw.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  t=t.replace(/(https?:\/\/[^\s<>"'\u0000-\u001F]+)/g,u=>{const h=u.replace(/&amp;/g,'&');return`<a href="${h}" target="_blank" rel="noopener" style="color:var(--primary);word-break:break-all">${u}</a>`;});
  t=t.replace(/(?<![\/\w])@([A-Za-z0-9_]{1,50})/g,'<a href="https://twitter.com/$1" target="_blank" rel="noopener" style="color:#1d9bf0">@$1</a>');
  t=t.replace(/(?<!\w)#([A-Za-z0-9_\u00C0-\u024F\u0400-\u04FF]+)/g,'<a href="https://twitter.com/hashtag/$1" target="_blank" rel="noopener" style="color:#1d9bf0">#$1</a>');
  return t;
}

/* ══════════════════════════════════════════════════════
   ACTIONS DROPDOWN
══════════════════════════════════════════════════════ */
function toggleActionsDropdown(e){ e.stopPropagation(); $('actionsDropdownMenu')?.classList.toggle('show'); }
document.addEventListener('click', ()=>$('actionsDropdownMenu')?.classList.remove('show'));
function exportCSV(){
  $('actionsDropdownMenu')?.classList.remove('show');
  if(!allData.length)return;
  let csv='Rank,Username,Name,Followers,Mentions,Replies,Retweets,Total\n';
  allData.forEach((u,i)=>{
    const n=(u.name||'').replace(/,/g,' ').replace(/"/g,'""');
    csv+=`${i+1},"@${u.username||''}","${n}",${u.followers||0},${u.mentions||0},${u.replies||0},${u.retweets||0},${eng(u)}\n`;
  });
  const a=document.createElement('a');
  a.href=URL.createObjectURL(new Blob([csv],{type:'text/csv;charset=utf-8;'}));
  a.download=`most_active_users_${MSCfg.sd}_${MSCfg.ed}.csv`;a.click();
}
function refreshData(){ $('actionsDropdownMenu')?.classList.remove('show'); window.location.reload(); }
function printTable(){
  $('actionsDropdownMenu')?.classList.remove('show');
  const content=$('tableWrapper').innerHTML;
  const w=window.open('','_blank');
  w.document.write(`<!DOCTYPE html><html><head><title>Most Active Users</title><style>body{font-family:Arial,sans-serif;padding:20px;}table{width:100%;border-collapse:collapse;}th{background:#f8fafc;padding:10px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;}td{padding:10px;font-size:12px;border-bottom:1px solid #f1f5f9;}</style></head><body><h1>Most Active Users — X</h1><p style="color:#64748b;font-size:13px;">${MSCfg.sd} to ${MSCfg.ed}</p>${content}</body></html>`);
  w.document.close();w.focus();setTimeout(()=>{w.print();w.close();},250);
}

/* ══════════════════════════════════════════════════════
   EXPORT
══════════════════════════════════════════════════════ */
const MAUExport = (() => {
  let _timer=null;
  function _toast(msg,type='default',dur=3200){
    const t=$('exportToast'),m=$('exportToastMsg'),ico=$('exportToastIcon');
    if(!t||!m)return;
    m.textContent=msg; t.className='export-toast show '+(type!=='default'?type:'');
    const icons={success:'ph-check-circle',error:'ph-x-circle',default:'ph-spinner'};
    ico.className='ph '+(icons[type]||icons.default);
    clearTimeout(_timer);_timer=setTimeout(()=>t.classList.remove('show'),dur);
  }
  function _btnState(btn,loading){ if(!btn)return; btn.disabled=loading; btn.classList.toggle('exporting',loading); }
    function _freeze() {
        if(document.getElementById('__s_freeze')) return;
        const s = document.createElement('style'); s.id = '__s_freeze';
        s.textContent = '*,*::before,*::after{animation:none!important;transition:none!important;animation-play-state:paused!important;}';
        document.head.appendChild(s);
    }
    function _unfreeze() { document.getElementById('__s_freeze')?.remove(); }
     
  function _donutSnap(){
    try{ if(!donutInst||donutInst.isDisposed())return null; const ch=$('donutChart'); if(!ch||ch.style.display==='none')return null; return donutInst.getDataURL({type:'png',pixelRatio:2,backgroundColor:'#ffffff'}); }catch(e){return null;}
  }
  function _makeOnClone(snap){ return clonedDoc=>{
    const s=clonedDoc.createElement('style');
    s.textContent=`*,*::before,*::after{animation:none!important;transition:none!important;}[data-html2canvas-ignore]{display:none!important;}.do-panel-overlay,.do-panel,#panelOverlay,#sntPanel{display:none!important;}.sk-block{animation:none!important;background:#e2e8f0!important;}.kpi-card-hover{transform:none!important;filter:none!important;}.fade-up,.fade-up-d1,.fade-up-d2,.fade-up-d3,.fade-up-d4{opacity:1!important;transform:none!important;}.spin-ring,.spinner-state{display:none!important;}`;
    clonedDoc.head.appendChild(s);
    clonedDoc.querySelectorAll('.do-panel-overlay,.do-panel,.export-toast').forEach(el=>{el.style.display='none';});
    clonedDoc.querySelectorAll('.card,.kpi-card-hover,.ht-item,.ht-list,[class*="col-"],.row,#pageExportArea').forEach(el=>{el.style.opacity='1';el.style.transform='none';el.style.visibility='visible';el.style.animation='none';});
    const dd=clonedDoc.getElementById('donutChart');
    if(dd){dd.innerHTML='';dd.style.cssText='display:block!important;width:100%;height:340px;';if(snap){const img=clonedDoc.createElement('img');img.src=snap;img.style.cssText='width:100%;height:100%;object-fit:contain;display:block;';dd.appendChild(img);}}
  };}
  async function _capture(areaId,bgColor){
    const area=document.getElementById(areaId);if(!area)throw new Error('Area #'+areaId+' not found');
    window.scrollTo({top:0});const snap=_donutSnap();
    area.querySelectorAll('.fade-up,.fade-up-d1,.fade-up-d2,.fade-up-d3,.fade-up-d4,.kpi-card-hover,.ht-item,.card,[class*="col-"]').forEach(e=>{e.style.opacity='1';e.style.transform='none';e.style.visibility='visible';});
    await new Promise(r=>requestAnimationFrame(()=>requestAnimationFrame(r)));
    const captureP=html2canvas(area,{scale:2,useCORS:true,allowTaint:true,backgroundColor:bgColor||'#f1f5f9',logging:false,removeContainer:true,scrollX:0,scrollY:0,width:area.offsetWidth,height:area.scrollHeight,onclone:_makeOnClone(snap)});
    const timeout=new Promise((_,rej)=>setTimeout(()=>rej(new Error('Capture timeout')),15000));
    return Promise.race([captureP,timeout]);
  }
  function _drawHeader(pdf,label){
    const pW=pdf.internal.pageSize.getWidth();pdf.setFillColor(3,128,71);pdf.rect(0,0,pW,11,'F');pdf.setTextColor(255,255,255);pdf.setFontSize(9);pdf.setFont('helvetica','bold');pdf.text('X Analytics — '+(label||'Most Active Users'),10,7.5);const now=new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'});pdf.setFontSize(7);pdf.setFont('helvetica','normal');pdf.text('Generated: '+now,pW-10,7.5,{align:'right'});
  }
  function _paginate(pdf,canvas,label){
    const pW=pdf.internal.pageSize.getWidth(),pH=pdf.internal.pageSize.getHeight();const margin=10,usableW=pW-margin*2,usableH=pH-margin*2-14;const ratio=usableW/canvas.width,sliceH=usableH/ratio;let srcY=0,pg=0;
    while(srcY<canvas.height){if(pg>0){pdf.addPage();_drawHeader(pdf,label);}const srcSlice=Math.min(sliceH,canvas.height-srcY);const slice=document.createElement('canvas');slice.width=canvas.width;slice.height=Math.ceil(srcSlice);slice.getContext('2d').drawImage(canvas,0,srcY,canvas.width,srcSlice,0,0,canvas.width,srcSlice);pdf.addImage(slice.toDataURL('image/png'),'PNG',margin,14,usableW,srcSlice*ratio);pdf.setFontSize(7);pdf.setTextColor(148,163,184);pdf.text(`Page ${pg+1}`,pW/2,pH-3,{align:'center'});srcY+=srcSlice;pg++;}
  }
  const _cardMeta={donut:{label:'Top 5 Engagement Share',file:'engagement-share'},list:{label:'Top Contributors',file:'top-contributors'}};
  const _stamp=()=>new Date().toISOString().slice(0,10).replace(/-/g,'');
  async function runCard(areaId,cardKey,type,btn){
    if(!window.html2canvas){_toast('html2canvas not available','error');return;}
    if(type==='pdf'&&!window.jspdf?.jsPDF){_toast('jsPDF not available','error');return;}
    _btnState(btn,true);_toast(type==='pdf'?'Preparing PDF…':'Capturing image…','default',99999);
    try{
      const canvas=await _capture(areaId,'#ffffff');const meta=_cardMeta[cardKey]||{label:cardKey,file:cardKey};const fname=`x_active_${meta.file}_${MSCfg.pid}_${_stamp()}`;
      if(type==='image'){const a=document.createElement('a');a.download=fname+'.png';a.href=canvas.toDataURL('image/png');a.click();_toast('Image downloaded!','success');}
      else{const{jsPDF}=window.jspdf;const pdf=new jsPDF({orientation:canvas.width>canvas.height?'landscape':'portrait',unit:'mm',format:'a4'});_drawHeader(pdf,meta.label);_paginate(pdf,canvas,meta.label);pdf.save(fname+'.pdf');_toast('PDF downloaded!','success');}
    }catch(err){console.error('[MAUExport.runCard]',err);_toast('Export failed: '+err.message,'error');}
    finally{_btnState(btn,false);}
  }
  async function run(type,btn){
    if(!window.html2canvas){_toast('html2canvas not available','error');return;}
    if(type==='pdf'&&!window.jspdf?.jsPDF){_toast('jsPDF not available','error');return;}
    const btnPdf=$('pageExportPdfBtn'),btnImg=$('pageExportImgBtn');
    _btnState(btnPdf,true);_btnState(btnImg,true);_toast(type==='pdf'?'Preparing PDF…':'Capturing image…','default',99999);
    try{
      const canvas=await _capture('pageExportArea','#f1f5f9');const stamp=_stamp();
      if(type==='image'){const a=document.createElement('a');a.download=`x_active_users_${MSCfg.pid}_${stamp}.png`;a.href=canvas.toDataURL('image/png');a.click();_toast('Image downloaded!','success');}
      else{const{jsPDF}=window.jspdf;const pdf=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'});_drawHeader(pdf,'Most Active Users');_paginate(pdf,canvas,'Most Active Users');pdf.save(`x_active_users_${MSCfg.pid}_${stamp}.pdf`);_toast('PDF downloaded!','success');}
    }catch(err){console.error('[MAUExport]',err);_toast('Export failed: '+err.message,'error');}
    finally{_btnState(btnPdf,false);_btnState(btnImg,false);}
  }
  return{run,runCard};
})();

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  if (MSCfg.pid) loadData();
  MSDp.init();
});
</script>
@endsection