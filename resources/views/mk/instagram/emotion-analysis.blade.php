@extends('mk.layouts.app')

@section('title', 'Instagram Emotion Analysis - SMADIMENT')

@section('styles')
<style>
/* ══ DESIGN TOKENS ══ */
:root {
    --primary: #038047; --primary-rgb: 3,128,71; --primary-lt: rgba(3,128,71,.10);
    --dark: #273B4A; --white: #FFFFFF; --bg: #F1F5F8;
    --green: #038047; --green-light: #E8F5EE; --red: #EF4444; --red-light: #FEF2F2;
    --amber: #F59E0B; --amber-light: #FFFBEB; --cyan: #06B6D4; --violet: #8B5CF6;
    --orange: #F97316; --indigo: #6366F1; --blue: #3B82F6; --purple: #A855F7; --emerald: #10B981;
    --slate-50: #F8FAFC; --slate-100: #F1F5F9; --slate-200: #E2E8F0; --slate-300: #CBD5E1;
    --slate-400: #94A3B8; --slate-500: #64748B; --slate-600: #475569; --slate-700: #334155;
    --slate-800: #1E293B; --slate-900: #0F172A;
    --radius: 8px; --radius-sm: 5px;
    --shadow-sm: 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
    --shadow-lg: 0 10px 30px rgba(15,23,42,.12);
    --e-joy: #F59E0B; --e-trust: #10B981; --e-fear: #6366F1; --e-surprise: #3B82F6;
    --e-sadness: #8B5CF6; --e-disgust: #A855F7; --e-anger: #EF4444; --e-anticipation: #F97316;
}
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin { to{transform:rotate(360deg)} }
@keyframes slideInRight { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1} to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn { from{opacity:0} to{opacity:1} }
@keyframes overlayOut { from{opacity:1} to{opacity:0} }
@keyframes kpiShimmer { 0%{left:-100%} 100%{left:150%} }
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0)} 30%{transform:scale(1.25) rotate(-10deg)} 60%{transform:scale(1.1) rotate(6deg)} }

.kpi-icon-bg { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0; }
.sk-block { border-radius:4px;background:linear-gradient(90deg,var(--slate-100)25%,var(--slate-200)50%,var(--slate-100)75%);background-size:200% 100%;animation:shimmer 1.4s infinite; }
.spin-ring { width:26px;height:26px;border:2.5px solid var(--slate-100);border-top-color:var(--primary);border-radius:50%;animation:spin .65s linear infinite; }
.spinner-state { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px;gap:12px;color:var(--slate-400);font-size:12px;font-weight:600; }

.fea-tabs { display:flex;gap:2px;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px;margin-bottom:16px;overflow-x:auto;scrollbar-width:none; }
.fea-tabs::-webkit-scrollbar { display:none; }
.fea-tab-btn { flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:7px 12px;border-radius:4px;border:none;background:transparent;font-size:12px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:background .13s,color .13s;white-space:nowrap;min-width:fit-content; }
.fea-tab-btn:hover { background:#fff;color:var(--slate-800); }
.fea-tab-btn.active { background:#fff;color:var(--primary);box-shadow:0 1px 4px rgba(0,0,0,.08); }
.fea-tab-chip { display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:16px;padding:0 5px;border-radius:3px;font-size:9px;font-weight:800;background:var(--primary-lt);color:var(--primary); }
.fea-tab-btn:not(.active) .fea-tab-chip { background:var(--slate-100);color:var(--slate-400); }

.fea-toggle-group { display:flex;background:var(--slate-50);border-radius:var(--radius-sm);padding:2px;gap:2px;border:1px solid var(--slate-200); }
.fea-toggle-btn { display:flex;align-items:center;gap:4px;padding:4px 10px;border-radius:3px;border:none;background:transparent;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:background .12s,color .12s; }
.fea-toggle-btn:hover { background:#fff;color:var(--slate-800); }
.fea-toggle-btn.active { background:#fff;color:var(--primary);box-shadow:0 1px 3px rgba(0,0,0,.07); }

.fea-post-list { display:flex;flex-direction:column; }
.fea-post { display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--slate-100);transition:background .12s;cursor:pointer; }
.fea-post:last-child { border-bottom:none; }
.fea-post:hover { background:var(--slate-50); }
.fea-post-rank { width:22px;height:22px;border-radius:50%;background:var(--slate-100);border:1px solid var(--slate-200);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:var(--slate-400);flex-shrink:0;margin-top:8px; }
.fea-post-rank--1 { background:linear-gradient(135deg,#ffd700,#F59E0B);color:#7c5900;border-color:#ffd700; }
.fea-post-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af);color:#3d3d3d;border-color:#c0c0c0; }
.fea-post-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820);color:#fff;border-color:#cd7f32; }
.fea-post-av { width:36px;height:36px;border-radius:50%;flex-shrink:0;color:#fff;font-weight:700;font-size:12px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--slate-200);overflow:hidden; }
.fea-post-av img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
.fea-post-body { flex:1;min-width:0; }
.fea-post-author { font-size:12.5px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.fea-post-date { font-size:10px;color:var(--slate-400);margin-top:1px;margin-bottom:4px; }
.fea-post-text { font-size:11.5px;color:var(--slate-500);line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:6px;word-break:break-word; }
.fea-post-stats { display:flex;align-items:center;gap:4px;flex-wrap:wrap; }
.fea-metric { display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:3px;font-size:10px;font-weight:700;background:var(--slate-100);color:var(--slate-500);white-space:nowrap; }
.fea-metric--primary { background:var(--primary-lt);color:var(--primary); }
.fea-metric--amber { background:rgba(245,158,11,.1);color:#92400e; }
.fea-metric--cyan { background:rgba(6,182,212,.1);color:#164e63; }
.fea-sent { display:inline-flex;align-items:center;padding:2px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.3px; }
.fea-sent--pos { background:#d1fae5;color:#065f46; }
.fea-sent--neg { background:#fee2e2;color:#991b1b; }
.fea-sent--neu { background:var(--slate-100);color:var(--slate-500); }
.fea-emo-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 7px;border-radius:3px;font-size:9px;font-weight:800;text-transform:capitalize;border:1px solid transparent; }
.fea-emo-badge .emo-dot { width:6px;height:6px;border-radius:50%;flex-shrink:0; }
.fea-view-link { display:inline-flex;align-items:center;gap:3px;font-size:9.5px;font-weight:700;color:var(--primary);text-decoration:none;padding:2px 6px;border-radius:3px;background:var(--primary-lt);border:1px solid rgba(3,128,71,.2);transition:background .12s,color .12s;margin-left:auto; }
.fea-view-link:hover { background:var(--primary);color:#fff; }
.fea-post-thumb { width:80px;height:100px;border-radius:var(--radius-sm);flex-shrink:0;overflow:hidden;border:1.5px solid var(--slate-200);background:var(--slate-800);position:relative;align-self:center;box-shadow:var(--shadow-sm); }
.fea-post-thumb img { width:100%;height:100%;object-fit:cover;display:block;transition:transform .2s; }
.fea-post:hover .fea-post-thumb img { transform:scale(1.06); }
.fea-post-thumb-ph { width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:24px;background:linear-gradient(135deg,#273B4A,#374151); }

.fea-pagination { display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--slate-100);flex-wrap:wrap;gap:8px; }
.fea-pag-info { font-size:11px;color:var(--slate-400);font-weight:500; }
.fea-pag-controls { display:flex;align-items:center;gap:3px; }
.fea-pag-btn { min-width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;padding:0 6px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;font-size:11px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .12s;user-select:none; }
.fea-pag-btn:hover:not(:disabled):not(.is-active) { border-color:var(--primary);color:var(--primary);background:var(--primary-lt); }
.fea-pag-btn.is-active { background:var(--primary);border-color:var(--primary);color:#fff; }
.fea-pag-btn:disabled { opacity:.35;cursor:not-allowed; }

.chart-container { height:280px;position:relative; }
.chart-loading { position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;background:#fff;z-index:2;transition:opacity .3s; }
.chart-loading.hidden { opacity:0;pointer-events:none; }
.chart-loading span { font-size:11px;font-weight:600;color:var(--slate-400); }
.chart-empty { height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--slate-400);font-size:12px;font-weight:600; }
.chart-empty i { font-size:34px;color:var(--slate-300);display:block; }

.fea-rows-sel { padding:4px 9px;border:1px solid var(--slate-200);border-radius:var(--radius-sm);font-size:11px;font-weight:600;color:var(--slate-600);background:var(--slate-50);outline:none;cursor:pointer; }
.fea-rows-sel:focus { border-color:var(--primary); }

.donut-legend { display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:4px; }
.donut-leg-item { display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--slate-500);padding:3px 8px;background:var(--slate-50);border-radius:3px;border:1px solid var(--slate-200);cursor:pointer;transition:border-color .12s,background .12s,color .12s; }
.donut-leg-item:hover { border-color:var(--primary);background:var(--primary-lt);color:var(--primary); }
.donut-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }

.kpi-card-hover { will-change:transform,box-shadow;cursor:default;position:relative !important;overflow:hidden !important;transition:transform .25s cubic-bezier(.34,1.56,.64,1),box-shadow .25s ease,filter .25s ease; }
.kpi-card-hover::before { content:'';position:absolute;top:0;bottom:0;left:-100%;width:60%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);pointer-events:none;z-index:1; }
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025);box-shadow:0 20px 40px rgba(0,0,0,.25);filter:brightness(1.07); }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background:rgba(255,255,255,.35) !important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both;display:inline-block; }

/* ══ Page Export Bar ══ */
.page-export-bar { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);padding:9px 14px;margin-bottom:20px;box-shadow:var(--shadow-sm); }
.page-export-bar-left { display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--slate-600); }
.page-export-bar-left i { font-size:15px;color:var(--primary); }
.page-export-bar-right { display:flex;gap:8px; }
.page-export-btn { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);font-size:16px;cursor:pointer;transition:all .15s ease;border:1.5px solid transparent;font-family:inherit; }
.page-export-btn-pdf { background:#fff3f3;color:#dc2626;border-color:#fca5a5; }
.page-export-btn-pdf:hover { background:#dc2626;color:#fff;border-color:#dc2626; }
.page-export-btn-img { background:var(--primary-lt);color:var(--primary);border-color:rgba(3,128,71,.3); }
.page-export-btn-img:hover { background:var(--primary);color:#fff;border-color:var(--primary); }
.page-export-btn:disabled { opacity:.55;cursor:not-allowed;pointer-events:none; }
.page-export-btn .export-spinner { width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none; }
.page-export-btn.exporting .export-spinner { display:inline-block; }
.page-export-btn.exporting .export-icon { display:none; }

/* ══ Card-level Export Buttons ══ */
.card-exp-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:var(--radius-sm);font-size:14px;cursor:pointer;flex-shrink:0;transition:all .14s ease;border:1px solid transparent;font-family:inherit;background:transparent; }
.card-exp-btn-pdf { color:#dc2626;border-color:#fca5a5;background:#fff3f3; }
.card-exp-btn-pdf:hover { background:#dc2626;color:#fff;border-color:#dc2626; }
.card-exp-btn-img { color:var(--primary);border-color:rgba(3,128,71,.3);background:var(--primary-lt); }
.card-exp-btn-img:hover { background:var(--primary);color:#fff;border-color:var(--primary); }
.card-exp-btn:disabled { opacity:.45;cursor:not-allowed;pointer-events:none; }
.card-exp-btn .export-spinner { width:11px;height:11px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none; }
.card-exp-btn.exporting .export-spinner { display:inline-block; }
.card-exp-btn.exporting .export-icon { display:none; }

/* ══ Export Toast ══ */
.export-toast { position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--slate-900);color:#fff;border-radius:var(--radius);padding:10px 18px;font-size:12px;font-weight:600;box-shadow:var(--shadow-lg);z-index:99999;opacity:0;pointer-events:none;transition:opacity .22s ease,transform .22s ease;display:flex;align-items:center;gap:8px;white-space:nowrap; }
.export-toast.show { opacity:1;transform:translateX(-50%) translateY(0); }
.export-toast.success { background:#065f46; }
.export-toast.error { background:#991b1b; }

.do-panel-overlay { position:fixed;inset:0;z-index:9000;background:rgba(15,23,42,.45);backdrop-filter:blur(4px);display:none; }
.do-panel-overlay.show { display:block;animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }
.do-panel { position:fixed;top:0;right:0;bottom:0;z-index:9001;width:480px;max-width:100vw;background:#fff;display:none;flex-direction:column;border-left:1px solid var(--slate-200);box-shadow:-8px 0 40px rgba(15,23,42,.16); }
.do-panel.show { display:flex;animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }
.do-panel-header { display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid var(--slate-200);background:var(--slate-50);flex-shrink:0; }
.do-panel-dot { width:9px;height:9px;border-radius:50%;flex-shrink:0; }
.do-panel-title { font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.do-panel-close { width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);font-size:16px;transition:all .14s;flex-shrink:0; }
.do-panel-close:hover { background:var(--red);border-color:var(--red);color:#fff; }
.do-panel-actions { display:flex;align-items:center;gap:7px;padding:7px 12px;border-bottom:1px solid var(--slate-200);background:#fff;flex-shrink:0; }
.do-panel-meta { flex:1;font-size:10px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:5px; }

.do-panel-list { overflow-y:auto;flex:1;padding:2px 0;min-height:0; }
.do-panel-list::-webkit-scrollbar { width:4px; }
.do-panel-list::-webkit-scrollbar-thumb { background:var(--slate-200);border-radius:99px; }
.do-panel-item { display:flex;gap:10px;padding:10px 14px;border-bottom:1px solid var(--slate-50);cursor:pointer;transition:background .1s;align-items:flex-start; }
.do-panel-item:hover { background:#f0f9ff; }
.do-panel-item:last-child { border-bottom:none; }
.do-panel-avatar { width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#fff;border:1.5px solid var(--slate-200);overflow:hidden; }
.do-panel-avatar img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
.do-panel-item-body { flex:1;min-width:0; }
.do-panel-author { font-size:12px;font-weight:700;color:var(--slate-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.do-panel-text { font-size:11px;color:var(--slate-600);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px; }
.do-panel-footer { display:flex;align-items:center;gap:5px;font-size:10px;color:var(--slate-400);flex-wrap:wrap; }
.do-sent-badge { padding:1px 6px;border-radius:3px;font-size:9px;font-weight:800;text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe;color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2;color:#991b1b; }
.do-sent-badge--neu { background:var(--slate-100);color:var(--slate-500); }

.do-detail-panel { position:absolute;inset:0;background:#fff;z-index:5;display:none;flex-direction:column;animation:slideInRight .2s cubic-bezier(.4,0,.2,1); }
.do-detail-panel.show { display:flex; }
.do-dp2-header { display:flex;align-items:center;gap:8px;padding:12px 14px;background:var(--slate-50);border-bottom:1px solid var(--slate-200);flex-shrink:0; }
.do-dp2-back { width:28px;height:28px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);transition:all .13s;font-size:14px; }
.do-dp2-back:hover { background:var(--primary-lt);color:var(--primary);border-color:var(--primary); }
.do-dp2-title { font-size:13px;font-weight:700;color:var(--slate-900);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.do-dp2-body { overflow-y:auto;flex:1;padding:16px; }
.do-dp2-body::-webkit-scrollbar { width:4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200);border-radius:99px; }
.do-dp2-avatar-row { display:flex;align-items:center;gap:10px;margin-bottom:12px; }
.do-dp2-avatar-lg { width:46px;height:46px;border-radius:50%;color:#fff;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;border:2px solid var(--slate-200);overflow:hidden;flex-shrink:0; }
.do-dp2-avatar-lg img { width:100%;height:100%;object-fit:cover;border-radius:50%; }
.do-dp2-name { font-size:14px;font-weight:700;color:var(--slate-900); }
.do-dp2-handle { font-size:11px;color:var(--slate-400);font-weight:500; }
.do-dp2-plat-badge { display:inline-block;padding:2px 8px;border-radius:3px;font-size:10px;font-weight:700;margin-top:3px; }
.do-dp2-meta { font-size:11px;color:var(--slate-400);font-weight:500;margin-bottom:10px; }
.do-dp2-sent { display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:3px;font-size:11px;font-weight:700;margin-bottom:10px; }
.do-dp2-sent--pos { background:#dbeafe;color:#1d4ed8; }
.do-dp2-sent--neg { background:#fee2e2;color:#991b1b; }
.do-dp2-sent--neu { background:var(--slate-100);color:var(--slate-500); }
.do-dp2-content { font-size:12px;color:var(--slate-700);line-height:1.7;margin-bottom:12px;background:var(--slate-50);border-radius:var(--radius-sm);padding:10px 12px;border:1px solid var(--slate-200);word-break:break-word; }
.do-dp2-media { border-radius:var(--radius-sm);overflow:hidden;margin-bottom:10px; }
.do-dp2-media img { width:100%;display:block; }
.do-dp2-stats { display:grid;grid-template-columns:repeat(2,1fr);gap:6px;margin-bottom:10px; }
.do-dp2-stat { background:var(--slate-50);border-radius:var(--radius-sm);padding:8px 10px;border:1px solid var(--slate-200);text-align:center; }
.do-dp2-stat-val { font-size:14px;font-weight:700;color:var(--slate-900); }
.do-dp2-stat-lbl { font-size:9px;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:.4px;margin-top:1px; }
.do-dp2-link { display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 14px;background:var(--primary);color:#fff;border-radius:var(--radius-sm);font-size:12px;font-weight:700;text-decoration:none;transition:filter .14s;margin-top:4px; }
.do-dp2-link:hover { filter:brightness(1.1);color:#fff; }

.emo-dist-bar { display:flex;align-items:center;gap:10px;margin-bottom:6px; }
.emo-dist-label { font-size:11px;font-weight:600;color:var(--slate-800);width:90px;text-transform:capitalize;display:flex;align-items:center;gap:5px; }
.emo-dist-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
.emo-dist-track { flex:1;height:8px;background:var(--slate-100);border-radius:4px;overflow:hidden; }
.emo-dist-fill { height:100%;border-radius:4px;transition:width .5s ease; }
.emo-dist-count { font-size:11px;font-weight:700;color:var(--slate-700);width:32px;text-align:right; }

@media(max-width:640px) { .do-panel{width:100vw;} .fea-tabs{flex-wrap:wrap;} .fea-post-thumb{display:none;} }
</style>
@endsection

@section('page-title', 'Instagram Emotion Analysis')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->subDays(6)->format('Y-m-d'));
    $endDate   = $endDate ?? request()->get('end_date', now()->format('Y-m-d'));
    $projects  = $projects ?? [];
@endphp

<script>
    const FEA_PID = {{ $projectId ? (int)$projectId : 'null' }};
    const FEA_SD  = '{{ $startDate }}';
    const FEA_ED  = '{{ $endDate }}';
</script>

@include('mk.layouts.partials.filter-datepicker')

@if(!$projectId)
<div class="alert alert-warning d-flex align-items-center gap-2">
    <i class="ph ph-warning-circle f-20"></i>
    <span>Tidak ada project yang dipilih. Pilih project dari sidebar untuk melihat emotion analysis.</span>
</div>
@else

{{-- ════ PAGE EXPORT WRAPPER ════ --}}
<div id="pageExportArea">

{{-- KPI --}}
<div class="row g-3 mb-3">
    @php
        $kpiCards = [
            ['id'=>'kpiJoy','label'=>'Joy','icon'=>'ph-smiley','bg'=>'bg-warning','sub'=>'Emosi paling positif'],
            ['id'=>'kpiTrust','label'=>'Trust','icon'=>'ph-handshake','bg'=>'bg-success','sub'=>'Kepercayaan & rasa aman'],
            ['id'=>'kpiAnger','label'=>'Anger','icon'=>'ph-fire','bg'=>'bg-danger','sub'=>'Ekspresi amarah'],
            ['id'=>'kpiTotal','label'=>'Total','icon'=>'ph-chart-line-up','bg'=>'bg-primary','sub'=>'Total post dianalisis'],
        ];
        $delays = ['.00s','.05s','.10s','.15s'];
    @endphp
    @foreach($kpiCards as $ki => $kc)
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 {{ $kc['bg'] }} text-white kpi-card-hover"
 style="animation:fadeUp .38s ease-out {{ $delays[$ki] }} both;">
            <div class="card-body"><div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="mb-1 text-white text-opacity-75 f-12">{{ $kc['label'] }}</p>
                    <h3 class="mb-0 text-white f-w-300" id="{{ $kc['id'] }}"><div class="sk-block" style="height:28px;width:90px;background:rgba(255,255,255,.2);"></div></h3>
                    <p class="mb-0 mt-2 text-white text-opacity-75 f-12"><i class="ph {{ $kc['icon'] }} me-1"></i>{{ $kc['sub'] }}</p>
                </div>
                <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph {{ $kc['icon'] }}"></i></div></div>
            </div></div>
        </div>
    </div>
    @endforeach
</div>

{{-- ══ Page Export Toolbar ══ --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
        <i class="ph ph-export"></i>
        <span>Export Halaman</span>
        <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Charts + Post List</span>
    </div>
    <div class="page-export-bar-right">
        <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                onclick="IGExport.run('pdf', this)" title="Export halaman sebagai PDF">
            <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
        </button>
        <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                onclick="IGExport.run('image', this)" title="Export halaman sebagai PNG">
            <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
        </button>
    </div>
</div>

{{-- Donut --}}
<div class="row"><div class="col-12">
    <div class="card" style="animation:fadeUp .38s ease-out .18s both;">
        <div id="card-export-donut">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div><div><h6 class="mb-0">Distribusi Emosi — Top 5</h6><small class="text-muted">Proporsi Plutchik wheel dari semua post</small></div></div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div id="donutLegend" class="donut-legend"></div>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="IGExport.runCard('card-export-donut','donut','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="IGExport.runCard('card-export-donut','donut','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="card-body"><div style="height:420px;position:relative;">
            <div class="chart-loading" id="donutLoading"><div class="spin-ring"></div><span>Loading chart…</span></div>
            <div id="donutChart" style="width:100%;height:420px;display:none;"></div>
            <div id="donutEmpty" style="display:none;" class="chart-empty"><i class="ph ph-chart-donut"></i><span>No data</span></div>
        </div></div>
        </div>{{-- /card-export-donut --}}
    </div>
</div></div>

{{-- Radar + Bar --}}
<div class="row">
    <div class="col-xl-5"><div class="card" style="animation:fadeUp .38s ease-out .20s both;">
        <div id="card-export-radar">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-polygon f-18 text-primary"></i></div><div><h6 class="mb-0">Emotion Radar</h6><small class="text-muted">Plutchik wheel distribution</small></div></div>
            <div class="d-flex align-items-center gap-1">
                <span class="badge bg-light-secondary text-muted me-1">Radar</span>
                <div data-html2canvas-ignore="true" class="d-flex gap-1">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="IGExport.runCard('card-export-radar','radar','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="IGExport.runCard('card-export-radar','radar','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="card-body"><div class="chart-container" style="height:300px;"><div class="chart-loading" id="radarLoading"><div class="spin-ring"></div><span>Loading…</span></div><div id="radarChart" style="width:100%;height:300px;display:none;"></div></div></div>
        </div>{{-- /card-export-radar --}}
    </div></div>
    <div class="col-xl-7"><div class="card" style="animation:fadeUp .38s ease-out .22s both;">
        <div id="card-export-bar">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-bar f-18 text-primary"></i></div><div><h6 class="mb-0">Distribusi Emosi</h6><small class="text-muted">Jumlah post per emosi Plutchik</small></div></div>
            <div class="d-flex align-items-center gap-1">
                <span class="badge bg-light-primary text-primary me-1" id="barBadge">—</span>
                <div data-html2canvas-ignore="true" class="d-flex gap-1">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="IGExport.runCard('card-export-bar','bar','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="IGExport.runCard('card-export-bar','bar','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="card-body"><div class="chart-container" style="height:300px;"><div class="chart-loading" id="barLoading"><div class="spin-ring"></div><span>Loading…</span></div><div id="barChart" style="width:100%;height:300px;display:none;"></div></div></div>
        </div>{{-- /card-export-bar --}}
    </div></div>
</div>

{{-- Trends --}}
<div class="row"><div class="col-12"><div class="card" style="animation:fadeUp .38s ease-out .24s both;">
    <div id="card-export-trends">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-trend-up f-18 text-primary"></i></div><div><h6 class="mb-0">Tren Emosi Harian</h6><small class="text-muted">Aktivitas emosi per hari</small></div></div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light-primary text-primary" id="trendsBadge">—</span>
            <div class="fea-toggle-group" id="trendsTypeToggle">
                <button class="fea-toggle-btn active" data-type="line" onclick="FEAChart.setTrendsType('line')">Line</button>
                <button class="fea-toggle-btn" data-type="area" onclick="FEAChart.setTrendsType('area')">Area</button>
            </div>
            <div data-html2canvas-ignore="true" class="d-flex gap-1">
                <button class="card-exp-btn card-exp-btn-pdf" onclick="IGExport.runCard('card-export-trends','trends','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                <button class="card-exp-btn card-exp-btn-img" onclick="IGExport.runCard('card-export-trends','trends','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
        </div>
    </div>
    <div class="card-body"><div class="chart-container" style="height:300px;"><div class="chart-loading" id="trendsLoading"><div class="spin-ring"></div><span>Loading…</span></div><div id="trendsChart" style="width:100%;height:300px;display:none;"></div></div></div>
    </div>{{-- /card-export-trends --}}
</div></div></div>

{{-- Tabs + Post List --}}
<div class="d-flex align-items-center justify-content-between gap-2 mb-2" style="flex-wrap:wrap;">
    <div class="fea-tabs flex-grow-1 mb-0" style="min-width:0;">
        <button class="fea-tab-btn active" id="tab-all" onclick="FEATab.show('all',this)"><i class="ph ph-squares-four"></i>Semua <span class="fea-tab-chip" id="chip-all">—</span></button>
        <button class="fea-tab-btn" id="tab-joy" onclick="FEATab.show('joy',this)">Joy <span class="fea-tab-chip" id="chip-joy">—</span></button>
        <button class="fea-tab-btn" id="tab-trust" onclick="FEATab.show('trust',this)">Trust <span class="fea-tab-chip" id="chip-trust">—</span></button>
        <button class="fea-tab-btn" id="tab-fear" onclick="FEATab.show('fear',this)">Fear <span class="fea-tab-chip" id="chip-fear">—</span></button>
        <button class="fea-tab-btn" id="tab-surprise" onclick="FEATab.show('surprise',this)">Surprise <span class="fea-tab-chip" id="chip-surprise">—</span></button>
        <button class="fea-tab-btn" id="tab-sadness" onclick="FEATab.show('sadness',this)">Sadness <span class="fea-tab-chip" id="chip-sadness">—</span></button>
        <button class="fea-tab-btn" id="tab-disgust" onclick="FEATab.show('disgust',this)">Disgust <span class="fea-tab-chip" id="chip-disgust">—</span></button>
        <button class="fea-tab-btn" id="tab-anger" onclick="FEATab.show('anger',this)">Anger <span class="fea-tab-chip" id="chip-anger">—</span></button>
        <button class="fea-tab-btn" id="tab-anticipation" onclick="FEATab.show('anticipation',this)">Anticipation <span class="fea-tab-chip" id="chip-anticipation">—</span></button>
    </div>
</div>
<div class="card" style="animation:fadeUp .38s ease-out .26s both;">
    <div id="card-export-posts">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2"><div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-brain f-18 text-primary"></i></div><div><h6 class="mb-0">Data Postingan</h6><small class="text-muted">Klik post untuk lihat detail</small></div></div>
        <div class="d-flex align-items-center gap-2">
            <select class="fea-rows-sel" id="rowsSel" onchange="FEAData.reload()"><option value="50">Top 50</option><option value="100" selected>Top 100</option><option value="200">Top 200</option></select>
            <span class="badge bg-light-primary text-primary" id="listBadge">Loading…</span>
            <div data-html2canvas-ignore="true" class="d-flex gap-1">
                <button class="card-exp-btn card-exp-btn-pdf" onclick="IGExport.runCard('card-export-posts','posts','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                <button class="card-exp-btn card-exp-btn-img" onclick="IGExport.runCard('card-export-posts','posts','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
            </div>
        </div>
    </div>
    <div id="listEl" class="p-0"><div class="spinner-state"><div class="spin-ring"></div>Memuat data…</div></div>
    <div id="pagEl"></div>
    </div>{{-- /card-export-posts --}}
</div>

{{-- /pageExportArea --}}
</div>

{{-- ══ Export Toast ══ --}}
<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

{{-- Slide Panel --}}
<div class="do-panel-overlay" id="feaPanelOverlay" onclick="FEAPanel.close()"></div>
<div class="do-panel" id="feaSntPanel">
    <div class="do-panel-header"><div class="do-panel-dot" id="feaPanelDot" style="background:var(--primary);"></div><span class="do-panel-title" id="feaPanelTitle">Emotion Posts</span><button class="do-panel-close" onclick="FEAPanel.close()"><i class="ph ph-x"></i></button></div>
    <div class="do-panel-actions"><div class="do-panel-meta"><i class="ph ph-brain" style="font-size:11px;"></i><span id="feaPanelMeta">—</span></div></div>
    <div class="do-panel-list" id="feaPanelList"></div>
    <div class="do-detail-panel" id="feaDetailPanel">
        <div class="do-dp2-header"><button class="do-dp2-back" onclick="FEADetail.close()"><i class="ph ph-caret-left"></i></button><span class="do-dp2-title" id="feaDetailTitle">Detail</span><button class="do-panel-close" onclick="FEAPanel.close()"><i class="ph ph-x"></i></button></div>
        <div class="do-dp2-body" id="feaDetailBody"></div>
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
const EMOTIONS=['joy','trust','fear','surprise','sadness','disgust','anger','anticipation'];
const EMO_COLORS={joy:'#F59E0B',trust:'#10B981',fear:'#6366F1',surprise:'#3B82F6',sadness:'#8B5CF6',disgust:'#A855F7',anger:'#EF4444',anticipation:'#F97316'};
const EMO_COLORS_ARR=EMOTIONS.map(e=>EMO_COLORS[e]);
const DONUT_COLORS=['#038047','#273B4A','#F59E0B','#06B6D4','#EF4444','#10B981','#8B5CF6','#F97316'];
const EMO_KW={joy:['senang','bahagia','happy','joy','gembira','suka','mantap','keren','bagus','luar biasa','amazing','great','love','indah','seru','enjoy'],trust:['percaya','trust','yakin','terpercaya','aman','reliable','professional','solid','terbaik','amanah','andalan'],fear:['takut','khawatir','was-was','bahaya','danger','fear','ancaman','waspada','ngeri','serem','merinding'],surprise:['terkejut','kaget','wow','surprise','tidak menyangka','unexpected','gila','ternyata','nggak nyangka'],sadness:['sedih','sad','kecewa','duka','menangis','galau','patah hati','sorrow','grief','nelangsa','hancur'],disgust:['jijik','muak','mual','benci','tidak suka','menjijikkan','awful','jelek','buruk','payah'],anger:['marah','anger','kesal','geram','frustasi','angry','kemarahan','emosi','sebel'],anticipation:['menunggu','nantikan','cannot wait','excited','antisipasi','harapan','soon','upcoming','segera','penasaran','catat']};
const FEACfg={pid:FEA_PID,sd:FEA_SD,ed:FEA_ED,perPage:15};
const _$=id=>document.getElementById(id);
const numF=n=>parseInt(n||0).toLocaleString('id-ID');
const numK=n=>{n=parseInt(n||0);return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n);};
const esc=s=>(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const dec=s=>{if(!s)return '';try{const f=decodeURIComponent(escape(s));if(!f.includes('\uFFFD')&&f!==s)return f;}catch(e){}return s;};
const hideLd=id=>{const e=_$(id);if(e)e.classList.add('hidden');};
const Charts={};
function makeApex(id,opts){if(Charts[id]){try{Charts[id].destroy();}catch(e){}}const el=_$(id);if(!el)return null;el.style.display='block';Charts[id]=new ApexCharts(el,opts);Charts[id].render();return Charts[id];}
window.addEventListener('resize',()=>Object.values(Charts).forEach(c=>{try{c.updateOptions({});}catch(e){}}));
const ECharts={};
function makeEChart(id){if(ECharts[id]){try{ECharts[id].dispose();}catch(e){}}const el=_$(id);if(!el)return null;el.style.display='block';const c=echarts.init(el,null,{renderer:'svg'});ECharts[id]=c;window.addEventListener('resize',()=>{try{c.resize();}catch(e){}});return c;}

let allPosts=[],filteredPosts=[],currentFilter='all',currentPage=1;

function detectEmotion(post){
    const raw=(post.emotion||post.emotion_str||'').toLowerCase().trim();
    if(raw&&EMOTIONS.includes(raw))return raw;
    const n=(post.name||'').toLowerCase();
    const content=(post.content||post.caption||'').toLowerCase();
    const combined=n+' '+content;
    const sent=(post.sentiment_str||'').toLowerCase();
    for(const[emo,kws]of Object.entries(EMO_KW)){if(kws.some(k=>combined.includes(k)))return emo;}
    if(sent.includes('pos'))return 'joy';if(sent.includes('neg'))return 'anger';return 'trust';
}
function getEmoCounts(posts){const c={};EMOTIONS.forEach(e=>c[e]=0);(posts||allPosts).forEach(p=>c[p.emotion]=(c[p.emotion]||0)+1);return c;}

/* Instagram-specific helpers */
function getName(item){
    const n=(item.name||item.author?.name||item.author_scr_name||item.author_name||'').replace(/<[^>]*>/g,'').trim();
    if(n&&n.includes(':'))return n.split(':')[0].trim()||'Instagram User';
    return n||'Instagram User';
}
function getAvatar(item){return(item.avatar_url||item.profile_url||item.author?.image||'').trim();}
function getThumbnail(item){return(item.image||item.thumbnail_url||item.avatar_url||item.profile_url||'').trim();}
function getViews(item){return parseInt(item.view_cnt||item.views||item.freq||0);}
function getLikes(item){return parseInt(item.likes||item.num_likes||0);}
function getComments(item){return parseInt(item.comments||item.num_comments||0);}
function normSent(item){const r=String(item.sentiment_str||item.sentiment||'').toLowerCase();return r.includes('pos')?'pos':r.includes('neg')?'neg':'neu';}
function getColor(item){const seed=item.author_id||item.id||getName(item)||'ig';const palette=['#038047','#273B4A','#F59E0B','#06B6D4','#8b5cf6','#ec4899','#f97316','#14b8a6'];let h=0;for(let i=0;i<seed.length;i++)h=(h*31+seed.charCodeAt(i))&0xffffffff;return palette[Math.abs(h)%palette.length];}
function avHtml(item){const av=getAvatar(item);const dummy='/assets/images/user/dummy.jpg';return(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.src='${dummy}'">`:`<img src="${dummy}">`;}
function emptyHtml(msg){return `<div class="chart-empty" style="padding:40px 20px;"><i class="ph ph-folder-open"></i><span>${esc(msg)}</span></div>`;}

const FEATab={show(filter,btn){currentFilter=filter;currentPage=1;document.querySelectorAll('.fea-tab-btn').forEach(b=>b.classList.remove('active'));if(btn)btn.classList.add('active');filteredPosts=filter==='all'?[...allPosts]:allPosts.filter(p=>p.emotion===filter);FEAData.renderList();const lb=_$('listBadge');if(lb)lb.textContent=numK(filteredPosts.length)+' posts';}};

const FEAData={
    _abort:null,
    async loadAll(){
        if(!FEACfg.pid){['kpiJoy','kpiTrust','kpiAnger','kpiTotal'].forEach(id=>{const el=_$(id);if(el)el.textContent='—';});if(_$('listEl'))_$('listEl').innerHTML=emptyHtml('Pilih project terlebih dahulu');return;}
        if(this._abort)this._abort.abort();this._abort=new AbortController();
        const rows=parseInt(_$('rowsSel')?.value||'100');
        const url=`/mk/api/instagram/most-viewed-posts?project_id=${FEACfg.pid}&start_date=${FEACfg.sd}&end_date=${FEACfg.ed}&sub=postbylike&rows=${rows}`;
        try{
            const res=await fetch(url,{signal:this._abort.signal});const json=await res.json();
            if(!json.success)throw new Error(json.error||'Failed');
            allPosts=(json.data||[]).map(p=>({...p,emotion:detectEmotion(p)}));filteredPosts=[...allPosts];currentPage=1;
            this._updateKPIs();this._updateChips();this.renderList();
            requestAnimationFrame(()=>{FEAChart.renderBar();requestAnimationFrame(()=>{FEAChart.renderRadar();requestAnimationFrame(()=>{FEAChart.renderDonut();requestAnimationFrame(()=>FEAChart.renderTrends());});});});
        }catch(err){if(err.name==='AbortError')return;console.error('[FEA]',err);if(_$('listEl'))_$('listEl').innerHTML=emptyHtml('Gagal memuat: '+err.message);['barLoading','radarLoading','donutLoading','trendsLoading'].forEach(hideLd);}
    },
    reload(){allPosts=[];filteredPosts=[];currentFilter='all';currentPage=1;document.querySelectorAll('.fea-tab-btn').forEach(b=>b.classList.remove('active'));_$('tab-all')?.classList.add('active');this.loadAll();},
    _updateKPIs(){const c=getEmoCounts();const kv=_$('kpiJoy');if(kv)kv.textContent=numF(c.joy||0);const kt=_$('kpiTrust');if(kt)kt.textContent=numF(c.trust||0);const ka=_$('kpiAnger');if(ka)ka.textContent=numF(c.anger||0);const kto=_$('kpiTotal');if(kto)kto.textContent=numF(allPosts.length);},
    _updateChips(){const c=getEmoCounts();const el=_$('chip-all');if(el)el.textContent=numK(allPosts.length);EMOTIONS.forEach(e=>{const chip=_$('chip-'+e);if(chip)chip.textContent=numK(c[e]||0);});},
    renderList(){
        const listEl=_$('listEl'),pagEl=_$('pagEl');if(!listEl)return;
        if(!filteredPosts.length){listEl.innerHTML=emptyHtml('Tidak ada postingan untuk filter ini');if(pagEl)pagEl.innerHTML='';return;}
        const pp=FEACfg.perPage,total=filteredPosts.length,pages=Math.ceil(total/pp),start=(currentPage-1)*pp,page=filteredPosts.slice(start,start+pp);
        listEl.innerHTML=`<div class="fea-post-list">${page.map((p,i)=>this._postHtml(p,start+i)).join('')}</div>`;
        if(pagEl)pagEl.innerHTML=pages>1?this._pagHtml(currentPage,pages,total,start+1,Math.min(start+pp,total)):'';
        listEl.querySelectorAll('.fea-post').forEach(el=>{el.addEventListener('click',()=>{try{const item=JSON.parse(decodeURIComponent(el.dataset.item));FEAPanel.open(filteredPosts,item.emotion||'trust');FEADetail.open(item);}catch(e){console.warn(e);}});});
    },
    _postHtml(post,gi){
        const rank=gi+1,rkCls=rank<=3?'--'+rank:'',name=getName(post),color=getColor(post),thumb=getThumbnail(post),sent=normSent(post);
        const content=dec((post.content||post.caption||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()).slice(0,200);
        const dt=(post.date_created||'').split('T')[0],url=post.url||post.link||'';
        const l=getLikes(post),c=getComments(post),total=l+c;
        const emo=post.emotion||'trust',ec=EMO_COLORS[emo]||'#64748b',sentLbl={pos:'Positive',neg:'Negative',neu:'Neutral'}[sent];
        const enc=encodeURIComponent(JSON.stringify(post));
        const thumbHtml=(thumb&&thumb.startsWith('http'))?`<div class="fea-post-thumb"><img src="${esc(thumb)}" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'fea-post-thumb-ph\\'>📷</div>'"></div>`:`<div class="fea-post-thumb"><div class="fea-post-thumb-ph">📷</div></div>`;
        return `<div class="fea-post" data-item="${esc(enc)}"><div class="fea-post-rank fea-post-rank${rkCls}">${rank}</div><div class="fea-post-av" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml(post)}</div><div class="fea-post-body"><div class="fea-post-author">${esc(name)}</div>${dt?`<div class="fea-post-date">${dt}</div>`:''}${content?`<div class="fea-post-text">${esc(content)}</div>`:'<div class="fea-post-text" style="color:var(--slate-400);font-style:italic;">(Tidak ada caption)</div>'}<div class="fea-post-stats"><span class="fea-emo-badge" style="background:${ec}18;color:${ec};border-color:${ec}40;"><span class="emo-dot" style="background:${ec};"></span>${emo.charAt(0).toUpperCase()+emo.slice(1)}</span><span class="fea-metric fea-metric--amber"><i class="ph ph-heart me-1"></i>${numF(l)}</span><span class="fea-metric fea-metric--cyan"><i class="ph ph-chat-circle me-1"></i>${numF(c)}</span><span class="fea-metric" style="font-weight:800;">∑ ${numF(total)}</span><span class="fea-sent fea-sent--${sent}">${sentLbl}</span>${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="fea-view-link" onclick="event.stopPropagation()"><i class="ph ph-arrow-square-out me-1"></i>Instagram</a>`:''}</div></div>${thumbHtml}</div>`;
    },
    _pagHtml(page,pages,total,from,to){let btns='',r=2;btns+=`<button class="fea-pag-btn" ${page<=1?'disabled':''} onclick="FEAData.goPage(${page-1})"><i class="ph ph-caret-left"></i></button>`;for(let i=1;i<=pages;i++){if(i===1||i===pages||(i>=page-r&&i<=page+r))btns+=`<button class="fea-pag-btn${i===page?' is-active':''}" onclick="FEAData.goPage(${i})">${i}</button>`;else if(i===page-r-1||i===page+r+1)btns+=`<span class="fea-pag-btn" style="cursor:default;opacity:.4;">…</span>`;}btns+=`<button class="fea-pag-btn" ${page>=pages?'disabled':''} onclick="FEAData.goPage(${page+1})"><i class="ph ph-caret-right"></i></button>`;return `<div class="fea-pagination"><span class="fea-pag-info">Menampilkan ${from}–${to} dari ${total} post</span><div class="fea-pag-controls">${btns}</div></div>`;},
    goPage(p){const pages=Math.ceil(filteredPosts.length/FEACfg.perPage);if(p<1||p>pages)return;currentPage=p;this.renderList();_$('listEl')?.scrollIntoView({behavior:'smooth',block:'start'});}
};

/* Charts - identical structure to original */
const FEAChart={
    _trendsType:'line',_trendsItems:[],
    setTrendsType(t){this._trendsType=t;document.querySelectorAll('#trendsTypeToggle .fea-toggle-btn').forEach(b=>b.classList.toggle('active',b.dataset.type===t));if(this._trendsItems.length)this._doRenderTrends(this._trendsItems,t);},
    renderBar(){hideLd('barLoading');if(!allPosts.length)return;const counts=getEmoCounts(),labels=EMOTIONS.map(e=>e.charAt(0).toUpperCase()+e.slice(1)),data=EMOTIONS.map(e=>counts[e]||0),colors=EMOTIONS.map(e=>EMO_COLORS[e]),total=allPosts.length||1;const el=_$('barBadge');if(el)el.textContent=numK(total)+' posts';
        makeApex('barChart',{chart:{type:'bar',height:300,fontFamily:'inherit',background:'transparent',toolbar:{show:false},zoom:{enabled:false},events:{mounted:()=>hideLd('barLoading'),dataPointSelection:(e,ctx,cfg)=>{const emo=EMOTIONS[cfg.dataPointIndex];if(emo)FEAPanel.open(allPosts.filter(p=>p.emotion===emo),emo);},click:(_,ctx,cfg)=>{const emo=EMOTIONS[cfg.dataPointIndex];if(emo)FEAPanel.open(allPosts.filter(p=>p.emotion===emo),emo);}}},series:[{name:'Posts',data}],colors,plotOptions:{bar:{borderRadius:5,columnWidth:'58%',distributed:true,dataLabels:{position:'top'}}},dataLabels:{enabled:true,formatter:v=>numK(v),offsetY:-16,style:{fontSize:'10px',fontWeight:'800',colors:EMOTIONS.map(e=>EMO_COLORS[e])},background:{enabled:false}},xaxis:{categories:labels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'},rotate:-20}},yaxis:{labels:{formatter:v=>numK(v),style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false},axisTicks:{show:false}},grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}},padding:{top:20,right:8,bottom:0,left:4}},fill:{type:'gradient',gradient:{type:'vertical',shadeIntensity:.2,opacityFrom:1,opacityTo:.7,stops:[0,100]}},tooltip:{shared:false,intersect:true,style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:(v)=>`${numF(v)} posts (${Math.round((v/total)*100)}%)`}},legend:{show:false}});},
    renderRadar(){hideLd('radarLoading');if(!allPosts.length)return;const counts=getEmoCounts(),max=Math.max(...Object.values(counts),1);const chart=makeEChart('radarChart');if(!chart)return;
        window._feaRadarChart=chart;
        chart.setOption({animation:true,animationDuration:800,backgroundColor:'transparent',tooltip:{show:true,backgroundColor:'#1e293b',borderColor:'#334155',borderWidth:1,padding:[10,14],textStyle:{color:'#fff',fontFamily:'inherit',fontSize:12},formatter:params=>{if(!params.data)return '';const vals=params.data.value||[];return `<div style="min-width:180px;"><div style="font-weight:700;font-size:12px;margin-bottom:7px;padding-bottom:5px;border-bottom:1px solid rgba(255,255,255,.12);">Emotion Distribution</div>${EMOTIONS.map((e,i)=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${EMO_COLORS[e]};"></span><span style="font-size:12px;color:#94a3b8;">${e.charAt(0).toUpperCase()+e.slice(1)}</span></div><span style="font-size:12px;font-weight:700;">${numF(vals[i]||0)}</span></div>`).join('')}</div>`;}},radar:{indicator:EMOTIONS.map(e=>({name:e.charAt(0).toUpperCase()+e.slice(1),max})),shape:'polygon',radius:'62%',center:['50%','50%'],axisName:{fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#475569'},splitLine:{lineStyle:{color:'#e2e8f0'}},axisLine:{lineStyle:{color:'#e2e8f0'}},splitArea:{show:true,areaStyle:{color:['rgba(248,250,252,0.8)','#fff']}}},series:[{type:'radar',data:[{value:EMOTIONS.map(e=>counts[e]||0),name:'Emotion',areaStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:1,colorStops:[{offset:0,color:'rgba(3,128,71,0.2)'},{offset:1,color:'rgba(3,128,71,0.05)'}]}},lineStyle:{color:'#038047',width:2.5},symbol:'circle',symbolSize:6,itemStyle:{color:EMOTIONS.map(e=>EMO_COLORS[e]),borderColor:'#fff',borderWidth:2}}]}]});
        chart.on('click',params=>{if(!params.name)return;const emo=params.name.toLowerCase();if(EMOTIONS.includes(emo))FEAPanel.open(allPosts.filter(p=>p.emotion===emo),emo);});},
    renderDonut(){const loadEl=_$('donutLoading'),chartEl=_$('donutChart'),emptyEl=_$('donutEmpty');if(!loadEl||!chartEl)return;const counts=getEmoCounts();const sorted=EMOTIONS.map(e=>({emo:e,count:counts[e]||0})).sort((a,b)=>b.count-a.count);const top5=sorted.slice(0,5).filter(x=>x.count>0);if(!top5.length){loadEl.style.display='none';if(emptyEl)emptyEl.style.display='flex';return;}const total=top5.reduce((s,x)=>s+x.count,0);const legEl=_$('donutLegend');if(legEl)legEl.innerHTML=top5.map((x,i)=>`<div class="donut-leg-item"><span class="donut-dot" style="background:${DONUT_COLORS[i]};"></span>${x.emo.charAt(0).toUpperCase()+x.emo.slice(1)} · ${numF(x.count)}</div>`).join('');loadEl.style.display='none';if(emptyEl)emptyEl.style.display='none';if(window.__feaDonut){try{window.__feaDonut.dispose();}catch(e){}}chartEl.style.display='block';const chart=echarts.init(chartEl,null,{renderer:'canvas'});window.__feaDonut=chart;window.addEventListener('resize',()=>{try{chart.resize();}catch(e){}});
        chart.setOption({backgroundColor:'transparent',animation:true,animationDuration:1000,series:[{type:'pie',radius:['38%','62%'],center:['50%','50%'],avoidLabelOverlap:true,minAngle:8,itemStyle:{borderColor:'#fff',borderWidth:3},label:{show:true,position:'outside',alignTo:'edge',edgeDistance:20,lineHeight:18,fontSize:11,fontFamily:'inherit',color:'#334155',fontWeight:'500',formatter:p=>`{title|${p.name}}\n({val|${numF(p.value)}} posts, {pct|${p.percent.toFixed(1)}%})`,rich:{title:{fontSize:11,fontWeight:'700',color:'#1e293b',lineHeight:18},val:{fontSize:11,fontWeight:'700',color:'#038047'},pct:{fontSize:11,fontWeight:'600',color:'#64748b'}}},labelLine:{show:true,length:18,length2:24,smooth:.3,lineStyle:{width:1.5,color:'#94A3B8'}},emphasis:{scale:false,itemStyle:{borderWidth:3,borderColor:'#fff'},label:{show:true}},data:top5.map((x,i)=>({name:x.emo.charAt(0).toUpperCase()+x.emo.slice(1),value:x.count,_emo:x.emo,itemStyle:{color:DONUT_COLORS[i]}}))}],graphic:[{type:'text',left:'center',top:'46%',z:100,style:{text:numK(total),fill:'#0f172a',font:'800 28px inherit',textAlign:'center'}},{type:'text',left:'center',top:'54%',z:100,style:{text:'TOTAL POSTS',fill:'#94a3b8',font:'600 9px inherit',textAlign:'center'}}]});
        /* hover tooltip */
        let _tt = document.getElementById('feaDonutTT');
        if (!_tt) {
            _tt = document.createElement('div'); _tt.id = 'feaDonutTT';
            _tt.style.cssText = 'position:fixed;z-index:9999;pointer-events:none;background:#1e293b;color:#fff;border:1px solid #334155;border-radius:6px;padding:10px 14px;max-width:240px;font-size:12px;line-height:1.5;display:none;box-shadow:0 8px 24px rgba(0,0,0,.32);font-family:inherit;opacity:0;transform:translateY(6px) scale(.97);transition:opacity .18s ease,transform .18s ease;';
            document.body.appendChild(_tt);
        }
        let _ttTimer = null;
        chart.on('mouseover', p => {
            if (p.componentType !== 'series') return;
            const color = DONUT_COLORS[p.dataIndex]; clearTimeout(_ttTimer);
            _tt.innerHTML = `<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;"><span style="width:9px;height:9px;border-radius:50%;background:${color};display:inline-block;"></span><b>${esc(p.name)}</b></div><div style="display:flex;align-items:center;gap:8px;"><b style="font-size:13px;">${numF(p.value)} posts</b><span style="color:${color};font-weight:700;">${p.percent.toFixed(1)}%</span></div>`;
            _tt.style.display = 'block';
            requestAnimationFrame(() => { _tt.style.opacity='1'; _tt.style.transform='translateY(0) scale(1)'; });
        });
        chart.on('mouseout', () => {
            _tt.style.opacity='0'; _tt.style.transform='translateY(6px) scale(.97)';
            _ttTimer = setTimeout(() => { _tt.style.display='none'; }, 180);
        });
        chartEl.addEventListener('mousemove', e => {
            if (_tt.style.display==='none') return;
            const vw=window.innerWidth, vh=window.innerHeight, tw=_tt.offsetWidth+16, th=_tt.offsetHeight+16;
            let x=e.clientX+18, y=e.clientY-10;
            if (x+tw>vw) x=e.clientX-tw; if (y+th>vh) y=e.clientY-th;
            _tt.style.left=x+'px'; _tt.style.top=y+'px';
        });
        chart.on('click',p=>{const x=top5[p.dataIndex];if(x)FEAPanel.open(allPosts.filter(post=>post.emotion===x.emo),x.emo);});},
    renderTrends(){hideLd('trendsLoading');if(!allPosts.length)return;this._trendsItems=allPosts;const tb=_$('trendsBadge');if(tb)tb.textContent=numK(allPosts.length)+' posts';this._doRenderTrends(allPosts,this._trendsType);},
    _doRenderTrends(posts,type){const dateMap={};posts.forEach(p=>{const d=(p.date_created||'').substring(0,10);if(!d)return;if(!dateMap[d]){dateMap[d]={};EMOTIONS.forEach(e=>dateMap[d][e]=0);}dateMap[d][p.emotion]=(dateMap[d][p.emotion]||0)+1;});const dates=Object.keys(dateMap).sort();if(!dates.length)return;
        makeApex('trendsChart',{chart:{type:type==='area'?'area':'line',height:300,fontFamily:'inherit',background:'transparent',toolbar:{show:false},zoom:{enabled:false},events:{mounted:()=>hideLd('trendsLoading')}},series:EMOTIONS.map(e=>({name:e.charAt(0).toUpperCase()+e.slice(1),data:dates.map(d=>dateMap[d][e]||0)})),colors:EMO_COLORS_ARR,xaxis:{categories:dates,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'}}},yaxis:{labels:{formatter:v=>numK(v),style:{fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false},axisTicks:{show:false}},stroke:{curve:'smooth',width:2},fill:type==='area'?{type:'gradient',gradient:{opacityFrom:.35,opacityTo:.05,shadeIntensity:.1}}:{type:'solid',opacity:1},markers:{size:0,hover:{size:5}},grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}},padding:{top:10,right:8,bottom:0,left:4}},legend:{position:'bottom',horizontalAlign:'left',fontSize:'11px',fontFamily:'inherit',fontWeight:600,markers:{width:8,height:8,radius:4},itemMargin:{horizontal:12,vertical:4},offsetY:4},tooltip:{shared:true,intersect:false,style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>numF(v)+' posts'}}});}
};

const FEAPanel={
    _posts:[],_emo:null,
    open(posts,emo){this._posts=posts||[];this._emo=emo;FEADetail.close();const color=EMO_COLORS[emo]||'#038047';_$('feaPanelDot').style.background=color;_$('feaPanelTitle').textContent=emo?'Emotion: '+emo.charAt(0).toUpperCase()+emo.slice(1):'All Emotions';_$('feaPanelMeta').textContent=FEACfg.sd+' – '+FEACfg.ed;const ov=_$('feaPanelOverlay'),pn=_$('feaSntPanel');ov.classList.remove('hiding');pn.classList.remove('hiding');ov.classList.add('show');pn.classList.add('show');this._render();},
    close(){FEADetail.close();const ov=_$('feaPanelOverlay'),pn=_$('feaSntPanel');pn.classList.add('hiding');ov.classList.add('hiding');setTimeout(()=>{pn.classList.remove('show','hiding');ov.classList.remove('show','hiding');},240);},
    _render(){const list=_$('feaPanelList');if(!list)return;const posts=this._posts;if(!posts.length){list.innerHTML=emptyHtml('Tidak ada data');return;}const dummy='/assets/images/user/dummy.jpg';list.innerHTML=posts.slice(0,100).map(item=>{const name=getName(item),color2=getColor(item),av=getAvatar(item);const avH=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.src='${dummy}'">`:`<img src="${dummy}">`;const text=dec((item.content||item.caption||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()).slice(0,130);const dt=(item.date_created||'').split('T')[0],sent=normSent(item),sentLbl={pos:'Pos',neg:'Neg',neu:'Neu'}[sent];const emo=item.emotion||'trust',ec=EMO_COLORS[emo]||'#64748b';const l=getLikes(item);const enc=encodeURIComponent(JSON.stringify(item));return `<div class="do-panel-item" data-item="${esc(enc)}" onclick="FEAPanel._click(this)"><div class="do-panel-avatar" style="background:linear-gradient(135deg,${color2},${color2}99);">${avH}</div><div class="do-panel-item-body"><div class="do-panel-author">${esc(name)}</div><div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div><div class="do-panel-footer"><span style="display:inline-flex;align-items:center;gap:3px;padding:1px 6px;border-radius:3px;font-size:9px;font-weight:800;background:${ec}18;color:${ec};border:1px solid ${ec}40;">${emo.charAt(0).toUpperCase()+emo.slice(1)}</span><span class="do-sent-badge do-sent-badge--${sent}">${sentLbl}</span><span><i class="ph ph-heart" style="font-size:9px;"></i> ${numK(l)}</span>${dt?`<span style="margin-left:auto;">${dt}</span>`:''}</div></div></div>`;}).join('');},
    _click(el){try{const raw=el.dataset.item.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"');const item=JSON.parse(decodeURIComponent(raw));FEADetail.open(item);}catch(e){console.warn(e);}}
};

const FEADetail={
    open(item){const panel=_$('feaDetailPanel'),body=_$('feaDetailBody'),title=_$('feaDetailTitle');if(!panel||!body)return;const emo=item.emotion||'trust',ec=EMO_COLORS[emo]||'#038047',name=getName(item),color2=getColor(item),av=getAvatar(item),dummy='/assets/images/user/dummy.jpg';const avH=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.src='${dummy}'">`:`<img src="${dummy}">`;const handle=item.author?.scr_name||item.author_scr_name||item.author_id||'';const raw=(item.content||item.caption||'').replace(/<[^>]*>/g,'').trim();const content=raw?dec(raw):'';const url=item.url||item.link||'';const dt=item.date_created||'';const l=getLikes(item),c=getComments(item);const sent=normSent(item),sentLbl={pos:'Positif',neg:'Negatif',neu:'Netral'}[sent];let dtFmt='';if(dt){try{dtFmt=new Date(dt).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});}catch(e){dtFmt=dt.split('T')[0];}}
        const thumb=getThumbnail(item);const mediaHtml=(thumb&&thumb.startsWith('http'))?`<div class="do-dp2-media"><img src="${esc(thumb)}" style="width:100%;" onerror="this.parentElement.style.display='none'"></div>`:'';
        const counts=getEmoCounts(),maxC=Math.max(...Object.values(counts),1);const emoBars=EMOTIONS.map(e=>{const cc=counts[e]||0,w=Math.round((cc/maxC)*100),ecc=EMO_COLORS[e];return `<div class="emo-dist-bar"><div class="emo-dist-label"><span class="emo-dist-dot" style="background:${ecc};"></span>${e.charAt(0).toUpperCase()+e.slice(1)}</div><div class="emo-dist-track"><div class="emo-dist-fill" style="width:${w}%;background:${ecc};"></div></div><div class="emo-dist-count">${numK(cc)}</div></div>`;}).join('');
        title.textContent=name;body.innerHTML=`<div class="do-dp2-avatar-row"><div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${color2},${color2}99);">${avH}</div><div><div class="do-dp2-name">${esc(name)}</div>${handle?`<div class="do-dp2-handle">@${esc(handle)}</div>`:''}<span class="do-dp2-plat-badge" style="background:${ec}18;color:${ec};">${emo.charAt(0).toUpperCase()+emo.slice(1)}</span></div></div>${dtFmt?`<div class="do-dp2-meta"><i class="ph ph-calendar me-1"></i>${dtFmt}</div>`:''}<div class="do-dp2-sent do-dp2-sent--${sent}">${sentLbl}</div>${mediaHtml}${content?`<div class="do-dp2-content">${esc(content)}</div>`:''}<div class="do-dp2-stats"><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(l)}</div><div class="do-dp2-stat-lbl">Likes</div></div><div class="do-dp2-stat"><div class="do-dp2-stat-val">${numF(c)}</div><div class="do-dp2-stat-lbl">Comments</div></div></div><div style="margin-bottom:12px;"><div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--slate-400);margin-bottom:8px;">Distribusi Emosi (semua post)</div>${emoBars}</div>${url?`<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out me-1"></i>Buka di Instagram</a>`:''}`;panel.classList.add('show');},
    close(){_$('feaDetailPanel')?.classList.remove('show');}
};

/* ══════════════════════════════════════════════════════
   IGExport — FIXED
   Sama persis dengan XExport & YTExport fix:
   1. ECharts (donut + radar) pre-snapshot via getDataURL()
      sebelum html2canvas → chart selalu muncul
   2. allowTaint:true  → tidak ada tainted-canvas block
   3. _freeze / _unfreeze CSS animations
   4. onclone:
      - Sembunyikan panel overlay, detail panel, spinner
      - Ganti avatar & thumbnail cross-origin (CDN Instagram)
        → initial letter / placeholder
      - Paksa semua konten visible & transform:none
      - Replace ECharts container dengan <img> dari snapshot
   5. _sliceCanvas terpusat → tidak ada duplikasi loop
   6. _fitCanvas untuk card yang muat 1 halaman
══════════════════════════════════════════════════════ */
const IGExport = (() => {
    'use strict';
    let _toastTimer = null;

    function _toast(msg, type = 'default', duration = 3200) {
        const t = _$('exportToast'), m = _$('exportToastMsg'), ico = _$('exportToastIcon');
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

    async function _swapChartsIn(el) {
        const swaps = [];

        for (const id of ['barChart', 'trendsChart']) {
            const container = document.getElementById(id);
            if (!container || !el.contains(container) || container.style.display === 'none') continue;
            const chart = Charts[id];
            if (!chart) continue;
            try {
                const { imgURI } = await chart.dataURI();
                if (!imgURI) continue;
                const h = Math.round(container.getBoundingClientRect().height) || container.offsetHeight || 300;
                const placeholder = document.createElement('div');
                placeholder.dataset.swapFor = id;
                placeholder.style.display = 'none';
                const img = document.createElement('img');
                img.src = imgURI;
                img.style.cssText = `width:100%;height:${h}px;object-fit:contain;display:block;background:#fff;`;
                container.parentNode.insertBefore(placeholder, container);
                container.parentNode.insertBefore(img, placeholder);
                container.style.display = 'none';
                swaps.push({ container, placeholder, img });
            } catch(e) { console.warn('[IGExport] apex swap failed:', id, e); }
        }

        for (const [id, inst] of [['donutChart', window.__feaDonut], ['radarChart', window._feaRadarChart]]) {
            const container = document.getElementById(id);
            if (!container || !el.contains(container) || container.style.display === 'none') continue;
            try {
                const rect = container.getBoundingClientRect();
                const w = Math.round(rect.width)  || container.offsetWidth  || 400;
                const h = Math.round(rect.height) || container.offsetHeight || 300;
                let dataUrl = null;

                /* Canvas renderer → getDataURL() langsung */
                const canvasEl = container.querySelector('canvas');
                if (canvasEl && inst && !inst.isDisposed?.()) {
                    try {
                        dataUrl = inst.getDataURL({ type:'png', pixelRatio:2, backgroundColor:'#ffffff', excludeComponents:['toolbox'] });
                        if (dataUrl === 'data:,') dataUrl = null;
                    } catch(e) { dataUrl = null; }
                }

                /* SVG renderer → blob → canvas fallback */
                if (!dataUrl) {
                    const svgEl = container.querySelector('svg');
                    if (svgEl) {
                        const svgStr  = new XMLSerializer().serializeToString(svgEl);
                        const blob    = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
                        const blobUrl = URL.createObjectURL(blob);
                        dataUrl = await new Promise(resolve => {
                            const image  = new Image();
                            const canvas = document.createElement('canvas');
                            canvas.width = w * 2; canvas.height = h * 2;
                            const ctx = canvas.getContext('2d');
                            image.onload = () => {
                                ctx.scale(2, 2);
                                ctx.fillStyle = '#ffffff';
                                ctx.fillRect(0, 0, w, h);
                                ctx.drawImage(image, 0, 0, w, h);
                                URL.revokeObjectURL(blobUrl);
                                resolve(canvas.toDataURL('image/png'));
                            };
                            image.onerror = () => { URL.revokeObjectURL(blobUrl); resolve(null); };
                            setTimeout(() => resolve(null), 3000);
                            image.src = blobUrl;
                        });
                    }
                }

                if (!dataUrl) continue;
                const placeholder = document.createElement('div');
                placeholder.dataset.swapFor = id;
                placeholder.style.display = 'none';
                const img = document.createElement('img');
                img.src = dataUrl;
                img.style.cssText = `width:100%;height:${h}px;object-fit:contain;display:block;background:#fff;`;
                container.parentNode.insertBefore(placeholder, container);
                container.parentNode.insertBefore(img, placeholder);
                container.style.display = 'none';
                swaps.push({ container, placeholder, img });
            } catch(e) { console.warn('[IGExport] echarts swap failed:', id, e); }
        }

        return swaps;
    }

    function _swapChartsOut(swaps) {
        swaps.forEach(({ container, placeholder, img }) => {
            try { img.remove(); }         catch(e) {}
            try { placeholder.remove(); } catch(e) {}
            container.style.display = 'block';
        });
    }

    function _onClone(clonedDoc) {
        clonedDoc.querySelectorAll(
            '.do-panel-overlay,.do-panel,.do-detail-panel,' +
            '#feaPanelOverlay,#feaSntPanel,' +
            '.spin-ring,.spinner-state,.export-toast,.chart-loading,' +
            '[data-html2canvas-ignore]'
        ).forEach(el => { el.style.cssText += 'display:none!important;'; });

        clonedDoc.querySelectorAll('*').forEach(el => {
            el.style.animationPlayState = 'paused';
            el.style.animation  = 'none';
            el.style.transition = 'none';
        });

        clonedDoc.querySelectorAll(
            '.card,.card-body,.card-header,.row,[class*="col-"],' +
            '.fea-post-list,.fea-post,.kpi-card-hover,#pageExportArea'
        ).forEach(el => {
            el.style.opacity    = '1';
            el.style.transform  = 'none';
            el.style.visibility = 'visible';
        });

        clonedDoc.querySelectorAll('.fea-post-av,.do-panel-avatar,.do-dp2-avatar-lg').forEach(wrapper => {
            wrapper.querySelectorAll('img').forEach(img => { img.style.display = 'none'; });
            if (!wrapper.querySelector('.__ini')) {
                const sp = clonedDoc.createElement('span');
                sp.className = '__ini';
                sp.textContent = 'I';
                sp.style.cssText = 'font-size:13px;font-weight:700;color:#fff;line-height:1;';
                wrapper.appendChild(sp);
            }
            if (!wrapper.style.background) wrapper.style.background = 'linear-gradient(135deg,#e1306c,#f77737)';
        });

        clonedDoc.querySelectorAll('.fea-post-thumb').forEach(wrapper => {
            wrapper.querySelectorAll('img').forEach(img => { img.style.display = 'none'; });
            const ph = wrapper.querySelector('.fea-post-thumb-ph');
            if (ph) ph.style.display = 'flex';
            wrapper.style.background = 'linear-gradient(135deg,#833ab4,#fd1d1d,#fcb045)';
        });

        clonedDoc.querySelectorAll('.do-dp2-media img').forEach(img => {
            img.style.display = 'none';
        });
    }

    async function _doCapture(el, isCard) {
        el.querySelectorAll('.card,.kpi-card-hover,[class*="col-"],.fea-post')
          .forEach(e => { e.style.opacity='1'; e.style.transform='none'; e.style.visibility='visible'; });

        const swaps = await _swapChartsIn(el);

        await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));
        await new Promise(r => setTimeout(r, 200));

        let canvas;
        try {
            canvas = await html2canvas(el, {
                scale          : isCard ? 2 : 1.5,
                useCORS        : true,
                allowTaint     : false,
                backgroundColor: isCard ? '#ffffff' : '#f1f5f9',
                logging        : false,
                removeContainer: true,
                imageTimeout   : 8000,
                scrollX        : 0,
                scrollY        : -window.scrollY,
                width          : el.offsetWidth,
                height         : el.scrollHeight,
                onclone        : d => _onClone(d),
                ignoreElements : e => e.hasAttribute('data-html2canvas-ignore') ||
                                      e.tagName === 'IMG' && !e.closest('[data-swap-for]') && !e.src?.startsWith('data:'),
            });
        } finally {
            _swapChartsOut(swaps);
        }
        return canvas;
    }

    function _drawHeader(pdf, pW, pH, label, page, total) {
        pdf.setFillColor(3, 128, 71); pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255); pdf.setFontSize(9); pdf.setFont('helvetica', 'bold');
        pdf.text('SMADIMENT — ' + (label || 'Instagram Emotion Analysis'), 10, 7.5);
        const now = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
        pdf.setFontSize(7); pdf.setFont('helvetica', 'normal');
        pdf.text('Generated: ' + now, pW - 10, 7.5, { align: 'right' });
        pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
        pdf.text(`Halaman ${page} / ${total}`, pW / 2, pH - 3, { align: 'center' });
    }

    function _fitCanvas(pdf, canvas, margin, pW, pH) {
        const uw = pW - margin * 2, uh = pH - 14 - 10;
        const r  = Math.min(uw / canvas.width, uh / canvas.height);
        pdf.addImage(canvas.toDataURL('image/png'), 'PNG',
            margin + (uw - canvas.width * r) / 2,
            14    + (uh - canvas.height * r) / 2,
            canvas.width * r, canvas.height * r);
    }

    function _sliceCanvas(pdf, canvas, margin, pW, pH, label) {
        const uw = pW - margin * 2, uh = pH - 14 - 10;
        const ratio = uw / canvas.width, sliceH = uh / ratio;
        const total = Math.max(1, Math.ceil((canvas.height * ratio) / uh));
        let srcY = 0, pg = 1;
        while (srcY < canvas.height) {
            if (pg > 1) pdf.addPage();
            _drawHeader(pdf, pW, pH, label, pg, total);
            const srcSlice = Math.min(sliceH, canvas.height - srcY);
            const slice    = document.createElement('canvas');
            slice.width  = canvas.width;
            slice.height = Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcSlice, 0, 0, canvas.width, srcSlice);
            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, uw, srcSlice * ratio);
            srcY += srcSlice; pg++;
        }
    }

    function _stamp() { return new Date().toISOString().slice(0, 10).replace(/-/g, ''); }

    async function run(type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error');       return; }

        const btnPdf = _$('pageExportPdfBtn'), btnImg = _$('pageExportImgBtn');
        _btnState(btnPdf, true); _btnState(btnImg, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);

        try {
            const area   = _$('pageExportArea');
            const canvas = await _doCapture(area, false);
            const stamp  = _stamp();

            if (type === 'image') {
                const a = document.createElement('a');
                a.download = `instagram_emotion_${FEA_PID}_${stamp}.png`;
                a.href = canvas.toDataURL('image/png'); a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pW = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight(), M = 10;
                const uw = pW - M*2, uh = pH - 14 - 10;
                if ((canvas.height * (uw / canvas.width)) <= uh) {
                    _drawHeader(pdf, pW, pH, 'Instagram Emotion Analysis', 1, 1);
                    _fitCanvas(pdf, canvas, M, pW, pH);
                } else {
                    _sliceCanvas(pdf, canvas, M, pW, pH, 'Instagram Emotion Analysis');
                }
                pdf.save(`instagram_emotion_${FEA_PID}_${stamp}.pdf`);
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[IGExport.run]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btnPdf, false); _btnState(btnImg, false);
        }
    }

    const _cardLabels = {
        donut : 'Distribusi Emosi — Top 5',
        radar : 'Emotion Radar',
        bar   : 'Distribusi Emosi Bar',
        trends: 'Tren Emosi Harian',
        posts : 'Data Postingan',
    };
    function _cardFilename(k) {
        const map = { donut:'distribusi-emosi-top5', radar:'emotion-radar', bar:'distribusi-emosi-bar', trends:'tren-emosi-harian', posts:'data-postingan' };
        return `instagram_${map[k] || k}_${FEA_PID}_${_stamp()}`;
    }

    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error');       return; }
        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);
        try {
            const area = document.getElementById(areaId);
            if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');
            const canvas = await _doCapture(area, true);
            const fname  = _cardFilename(cardKey);
            const label  = _cardLabels[cardKey] || cardKey;

            if (type === 'image') {
                const a = document.createElement('a');
                a.download = fname + '.png'; a.href = canvas.toDataURL('image/png'); a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({
                    orientation: canvas.width > canvas.height * 1.2 ? 'landscape' : 'portrait',
                    unit: 'mm', format: 'a4'
                });
                const pW = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight(), M = 10;
                const uw = pW - M*2, uh = pH - 14 - 10;
                if ((canvas.height * (uw / canvas.width)) <= uh) {
                    _drawHeader(pdf, pW, pH, label, 1, 1);
                    _fitCanvas(pdf, canvas, M, pW, pH);
                } else {
                    _sliceCanvas(pdf, canvas, M, pW, pH, label);
                }
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[IGExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally { _btnState(btn, false); }
    }

    return { run, runCard };
})();
  document.addEventListener('DOMContentLoaded',()=>{FEAData.loadAll();document.addEventListener('keydown',e=>{if(e.key==='Escape')FEAPanel.close();});});
</script>
@endsection