@extends('mk.layouts.app')

@section('title', 'Location Map - SMADIMENT')

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

/* Safari-safe: hindari animation pada container chart */
@keyframes fadeUp        { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer       { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin          { to{transform:rotate(360deg)} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1} to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn     { from{opacity:0} to{opacity:1} }
@keyframes overlayOut    { from{opacity:1} to{opacity:0} }
@keyframes kpiIconBounce { 0%,100%{transform:scale(1) rotate(0)} 30%{transform:scale(1.25) rotate(-10deg)} 60%{transform:scale(1.1) rotate(6deg)} }
@keyframes kpiShimmer    { 0%{left:-100%} 100%{left:150%} }
@keyframes modalIn       { from{transform:translateY(-16px) scale(.96);opacity:0} to{transform:translateY(0) scale(1);opacity:1} }

body { background: var(--bg); }
.dashboard-container { padding: 24px; max-width: 1600px; margin: 0 auto; }
.page-header { margin-bottom: 24px; }
.page-header h1 { font-size: 22px; font-weight: 700; color: var(--slate-900); margin: 0 0 4px 0; }
.page-header p  { font-size: 13px; color: var(--slate-400); margin: 0; font-weight: 500; }

.alert { padding: 14px 18px; border-radius: var(--radius); margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
.alert-warning { background: #fffbeb; color: #92400e; border: 1px solid #fcd34d; }
.alert svg { flex-shrink: 0; width: 18px; height: 18px; }

.kpi-icon-bg { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.2); font-size: 24px; color: #fff; flex-shrink: 0; }
.kpi-card-hover { will-change: transform; cursor: default; position: relative !important; overflow: hidden !important; transition: transform .25s cubic-bezier(.34,1.56,.64,1) !important, box-shadow .25s ease !important; }
.kpi-card-hover::before { content: ''; position: absolute; top: 0; bottom: 0; left: -100%; width: 60%; background: linear-gradient(90deg, transparent, rgba(255,255,255,.22), transparent); pointer-events: none; z-index: 1; }
.kpi-card-hover:hover { transform: translateY(-6px) scale(1.025) !important; box-shadow: 0 20px 40px rgba(0,0,0,.25) !important; }
.kpi-card-hover:hover::before { animation: kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background: rgba(255,255,255,.35) !important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation: kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important; display: inline-block !important; }
.kpi-card-hover h3 { font-size: 1.5rem; }

.sk-block { border-radius: 4px; background: linear-gradient(90deg, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
.spin-ring { width: 26px; height: 26px; border: 2.5px solid var(--slate-100); border-top-color: var(--primary); border-radius: 50%; animation: spin .65s linear infinite; }
.spinner-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 20px; gap: 12px; color: var(--slate-400); font-size: 12px; font-weight: 600; }

.geo-card { background: #fff; border: 1px solid var(--slate-200); border-radius: var(--radius); box-shadow: var(--shadow-sm); overflow: hidden; margin-bottom: 20px; }
.geo-card-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--slate-100); background: #fff; }
.geo-card-header-left { display: flex; align-items: center; gap: 10px; }
.geo-avtar { width: 38px; height: 38px; border-radius: var(--radius-sm); flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--primary-lt); }
.geo-avtar i   { font-size: 18px; color: var(--primary); }
.geo-avtar svg { width: 20px; height: 20px; stroke: var(--primary); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.geo-card-title    { font-size: 14px; font-weight: 700; color: var(--slate-900); margin: 0 0 2px; }
.geo-card-subtitle { font-size: 12px; color: var(--slate-400); font-weight: 500; margin: 0; }
.geo-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 22px; padding: 0 8px; border-radius: 3px; font-size: 10px; font-weight: 800; letter-spacing: .3px; background: var(--primary-lt); color: var(--primary); text-transform: uppercase; }

.donut-legend { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 10px; }
.donut-leg-item { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: var(--slate-500); padding: 3px 8px; background: var(--slate-50); border-radius: 3px; border: 1px solid var(--slate-200); cursor: pointer; transition: border-color .12s, background .12s, color .12s; }
.donut-leg-item:hover { border-color: var(--primary); background: var(--primary-lt); color: var(--primary); }
.donut-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* Safari-safe chart container: pakai visibility:hidden/visible bukan display:none/block untuk canvas */
.chart-container { position: relative; }
.chart-loading { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; background: #fff; z-index: 2; transition: opacity .3s; }
.chart-loading.hidden { opacity: 0; pointer-events: none; }
.chart-empty { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; color: var(--slate-400); font-size: 12px; font-weight: 600; }
.chart-empty i { font-size: 34px; color: var(--slate-300); display: block; }

/* Safari-safe: jangan sembunyikan canvas pakai display:none, gunakan visibility atau opacity */
.geo-chart-wrap {
    position: relative;
    width: 100%;
}
/* Canvas/echarts container selalu ada di DOM, cukup opacity untuk hide */
.geo-chart-inner {
    width: 100%;
    height: 100%;
    /* Safari: min-height harus explicit agar canvas punya dimensi */
}

.do-panel-overlay { position: fixed; inset: 0; z-index: 9000; background: rgba(15,23,42,.45); -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px); display: none; }
.do-panel-overlay.show   { display: block; animation: overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation: overlayOut .22s ease-out forwards; }
.do-panel { position: fixed; top: 0; right: 0; bottom: 0; z-index: 9001; width: 480px; max-width: 100vw; background: #fff; display: none; flex-direction: column; border-left: 1px solid var(--slate-200); box-shadow: -8px 0 40px rgba(15,23,42,.16); }
.do-panel.show   { display: flex; animation: slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation: slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }
.do-panel-header  { display: flex; align-items: center; gap: 10px; padding: 14px 16px; border-bottom: 1px solid var(--slate-200); background: var(--slate-50); flex-shrink: 0; }
.do-panel-dot     { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.do-panel-title   { font-size: 13px; font-weight: 700; color: var(--slate-900); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.do-panel-close   { width: 28px; height: 28px; border-radius: var(--radius-sm); border: 1px solid var(--slate-200); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--slate-500); font-size: 16px; transition: all .14s; flex-shrink: 0; }
.do-panel-close:hover { background: var(--red); border-color: var(--red); color: #fff; }
.do-panel-actions { display: flex; align-items: center; gap: 7px; padding: 7px 12px; border-bottom: 1px solid var(--slate-200); background: #fff; flex-shrink: 0; }
.do-panel-meta    { flex: 1; font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .5px; display: flex; align-items: center; gap: 5px; }
.do-panel-list    { overflow-y: auto; flex: 1; padding: 2px 0; min-height: 0; }
.do-panel-list::-webkit-scrollbar       { width: 4px; }
.do-panel-list::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }
.do-panel-item    { display: flex; gap: 10px; padding: 10px 14px; border-bottom: 1px solid var(--slate-50); cursor: pointer; transition: background .1s; align-items: flex-start; }
.do-panel-item:hover      { background: #f0f9ff; }
.do-panel-item:last-child { border-bottom: none; }
.do-panel-avatar  { width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; color: #fff; border: 1.5px solid var(--slate-200); overflow: hidden; }
.do-panel-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.do-panel-item-body { flex: 1; min-width: 0; }
.do-panel-author  { font-size: 12px; font-weight: 700; color: var(--slate-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.do-panel-text    { font-size: 11px; color: var(--slate-600); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 4px; }
.do-panel-footer  { display: flex; align-items: center; gap: 5px; font-size: 10px; color: var(--slate-400); flex-wrap: wrap; }
.do-sent-badge        { padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
.do-sent-badge--pos   { background: #dbeafe; color: #1d4ed8; }
.do-sent-badge--neg   { background: #fee2e2; color: #991b1b; }
.do-sent-badge--neu   { background: var(--slate-100); color: var(--slate-500); }
.do-detail-panel  { position: absolute; inset: 0; background: #fff; z-index: 5; display: none; flex-direction: column; animation: slideInRight .2s cubic-bezier(.4,0,.2,1); }
.do-detail-panel.show { display: flex; }
.do-dp2-header    { display: flex; align-items: center; gap: 8px; padding: 12px 14px; background: var(--slate-50); border-bottom: 1px solid var(--slate-200); flex-shrink: 0; }
.do-dp2-back      { width: 28px; height: 28px; border-radius: var(--radius-sm); border: 1px solid var(--slate-200); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--slate-500); transition: all .13s; font-size: 14px; }
.do-dp2-back:hover { background: var(--primary-lt); color: var(--primary); border-color: var(--primary); }
.do-dp2-title     { font-size: 13px; font-weight: 700; color: var(--slate-900); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.do-dp2-body      { overflow-y: auto; flex: 1; padding: 16px; }
.do-dp2-body::-webkit-scrollbar       { width: 4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }
.do-dp2-avatar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.do-dp2-avatar-lg  { width: 46px; height: 46px; border-radius: 50%; color: #fff; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; border: 2px solid var(--slate-200); overflow: hidden; flex-shrink: 0; }
.do-dp2-name       { font-size: 14px; font-weight: 700; color: var(--slate-900); }
.do-dp2-handle     { font-size: 11px; color: var(--slate-400); font-weight: 500; }
.do-dp2-plat-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 700; margin-top: 3px; }
.do-dp2-sent       { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 3px; font-size: 11px; font-weight: 700; margin-bottom: 10px; }
.do-dp2-sent--pos  { background: #dbeafe; color: #1d4ed8; }
.do-dp2-sent--neg  { background: #fee2e2; color: #991b1b; }
.do-dp2-sent--neu  { background: var(--slate-100); color: var(--slate-500); }
.do-dp2-stats      { display: grid; grid-template-columns: repeat(3,1fr); gap: 6px; margin-bottom: 10px; }
.do-dp2-stat       { background: var(--slate-50); border-radius: var(--radius-sm); padding: 8px 10px; border: 1px solid var(--slate-200); text-align: center; }
.do-dp2-stat-val   { font-size: 14px; font-weight: 700; color: var(--slate-900); }
.do-dp2-stat-lbl   { font-size: 9px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .4px; margin-top: 1px; }
.do-dp2-link { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 14px; background: var(--primary); color: #fff; border-radius: var(--radius-sm); font-size: 12px; font-weight: 700; text-decoration: none; transition: filter .14s; margin-top: 4px; }
.do-dp2-link:hover { filter: brightness(1.1); color: #fff; }

.map-with-panel { display: flex; }
.map-area       { flex: 1; min-width: 0; position: relative; }
.location-panel { width: 220px; flex-shrink: 0; border-left: 1px solid var(--slate-100); display: flex; flex-direction: column; background: #fff; }
.location-panel-title { padding: 12px 14px 10px; font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid var(--slate-100); }
.location-list  { overflow-y: auto; flex: 1; max-height: 500px; }
.location-list::-webkit-scrollbar       { width: 4px; }
.location-list::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }
.location-item  { display: flex; align-items: center; gap: 10px; padding: 9px 14px; border-bottom: 1px solid var(--slate-100); cursor: pointer; transition: background .12s; }
.location-item:last-child { border-bottom: none; }
.location-item:hover  { background: var(--slate-50); }
.location-item.active { background: var(--primary-lt); border-left: 3px solid var(--primary); padding-left: 11px; }
.location-item-rank   { width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: var(--slate-400); background: var(--slate-100); border: 1px solid var(--slate-200); }
.location-item-rank--1 { background: linear-gradient(135deg,#ffd700,#F59E0B); color: #7c5900; border-color: #ffd700; }
.location-item-rank--2 { background: linear-gradient(135deg,#c0c0c0,#9ca3af); color: #3d3d3d; border-color: #c0c0c0; }
.location-item-rank--3 { background: linear-gradient(135deg,#cd7f32,#b06820); color: #fff; border-color: #cd7f32; }
.location-item-info  { flex: 1; min-width: 0; }
.location-item-name  { font-size: 12px; font-weight: 700; color: var(--slate-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.location-item-count { font-size: 11px; color: var(--slate-400); font-weight: 600; margin-top: 1px; }
.location-item-dot   { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* Loading overlay pakai opacity agar canvas tetap ada di DOM (Safari safe) */
.map-loading-overlay {
    position: absolute; inset: 0; z-index: 10;
    background: #f8fafc;
    display: flex; align-items: center; justify-content: center;
    flex-direction: column; gap: 10px;
    transition: opacity .4s ease;
    pointer-events: none;
}
.map-loading-overlay.hidden { opacity: 0; pointer-events: none; }
.map-loading-overlay.gone   { display: none; }
.panel-skeleton      { padding: 10px 14px; }
.panel-skeleton .sk-line { height: 18px; border-radius: 4px; margin-bottom: 8px; background: linear-gradient(90deg, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }

.map-scroll-overlay  { position: absolute; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; pointer-events: none; opacity: 0; transition: opacity .2s; }
.map-scroll-overlay.visible { opacity: 1; }
.map-scroll-hint     { display: flex; flex-direction: column; align-items: center; gap: 8px; background: rgba(15,23,42,.75); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); color: #fff; padding: 16px 28px; border-radius: var(--radius); font-size: 13px; font-weight: 700; pointer-events: none; }

.charts-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.country-bar-row    { margin-bottom: 12px; }
.country-bar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
.country-bar-name   { font-size: 12px; font-weight: 700; color: var(--slate-800); }
.country-bar-count  { font-size: 12px; font-weight: 700; color: var(--slate-500); }
.country-bar-track  { height: 8px; background: var(--slate-100); border-radius: 99px; overflow: hidden; }
.country-bar-fill   { height: 100%; border-radius: 99px; transition: width .9s cubic-bezier(.4,0,.2,1); width: 0; }
.prov-bar-row    { margin-bottom: 10px; }
.prov-bar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.prov-bar-name   { font-size: 11px; font-weight: 700; color: var(--slate-700); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%; }
.prov-bar-count  { font-size: 11px; font-weight: 700; color: var(--slate-500); }
.prov-bar-track  { height: 7px; background: var(--slate-100); border-radius: 99px; overflow: hidden; }
.prov-bar-fill   { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--primary), rgba(3,128,71,.5)); transition: width .8s cubic-bezier(.4,0,.2,1); width: 0; }

.geo-tbl { width: 100%; border-collapse: separate; border-spacing: 0; }
.geo-tbl th { padding: 9px 12px; font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid var(--slate-200); text-align: left; }
.geo-tbl td { padding: 11px 12px; font-size: 13px; color: var(--slate-800); border-bottom: 1px solid var(--slate-100); }
.geo-tbl tbody tr { transition: background .12s; background: #fff; }
.geo-tbl tbody tr:hover { background: var(--slate-50); }
.geo-tbl tr:last-child td { border-bottom: none; }
.geo-tbl-rank { font-weight: 800; color: var(--primary); width: 28px; font-size: 12px; }
.geo-tbl-name { font-weight: 700; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.geo-tbl-num  { text-align: right; font-weight: 700; font-size: 13px; color: var(--slate-800); }
.geo-empty    { font-size: 13px; color: var(--slate-400); text-align: center; padding: 48px 20px; font-weight: 600; }
.view-all-btn { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; background: #fff; color: var(--slate-600); border: 1px solid var(--slate-200); border-radius: var(--radius-sm); font-size: 11px; font-weight: 700; cursor: pointer; transition: all .12s; }
.view-all-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-lt); }

.geo-modal-overlay { display: none; position: fixed; inset: 0; z-index: 9000; background: rgba(15,23,42,.55); -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px); align-items: center; justify-content: center; }
.geo-modal-overlay.open { display: flex; }
.geo-modal         { background: #fff; border-radius: var(--radius); width: 90%; max-width: 540px; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,.35); animation: modalIn .25s ease-out; }
.geo-modal-header  { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--slate-100); }
.geo-modal-header h3 { font-size: 15px; font-weight: 700; color: var(--slate-900); margin: 0; }
.geo-modal-close   { width: 30px; height: 30px; border-radius: var(--radius-sm); background: var(--slate-50); border: 1px solid var(--slate-200); cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--slate-500); transition: all .12s; }
.geo-modal-close:hover { background: var(--red); border-color: var(--red); color: #fff; }
.geo-modal-body    { padding: 12px 20px 20px; overflow-y: auto; flex: 1; }
.geo-modal-body::-webkit-scrollbar       { width: 4px; }
.geo-modal-body::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 99px; }

/* ══ EXPORT STYLES ══ */
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
.export-toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px); background:var(--slate-900); color:#fff; border-radius:var(--radius); padding:10px 18px; font-size:12px; font-weight:600; box-shadow:var(--shadow-lg); z-index:99999; opacity:0; pointer-events:none; transition:opacity .22s ease, transform .22s ease; display:flex; align-items:center; gap:8px; white-space:nowrap; }
.export-toast.show    { opacity:1; transform:translateX(-50%) translateY(0); }
.export-toast.success { background:#065f46; }
.export-toast.error   { background:#991b1b; }

@media(max-width:1100px) { .charts-row { grid-template-columns: 1fr 1fr; } }
@media(max-width:700px)  { .charts-row { grid-template-columns: 1fr; } }
@media(max-width:900px)  { .map-with-panel { flex-direction: column; } .location-panel { width: 100%; border-left: none; border-top: 1px solid var(--slate-100); } .location-list { max-height: 200px; } }
@media(max-width:768px)  { .dashboard-container { padding: 16px; } }
@media(max-width:640px)  { .do-panel { width: 100vw; } }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    @if(!$projectId)
    <div class="alert alert-warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>No project selected. Please select a project from the sidebar to view geographic data.</span>
    </div>
    @else

    @include('mk.layouts.partials.filter-datepicker')

    <div id="pageExportArea">

    {{-- ══ Page Export Toolbar ══ --}}
    <div class="page-export-bar" data-html2canvas-ignore="true">
        <div class="page-export-bar-left">
            <i class="ph ph-export"></i>
            <span>Export Halaman</span>
            <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">PDF 3 Hal · PNG</span>
        </div>
        <div class="page-export-bar-right">
            <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
                    onclick="XGeoExport.run('pdf', this)" title="Export PDF 3 halaman">
                <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
            </button>
            <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
                    onclick="XGeoExport.run('image', this)" title="Export PNG">
                <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
            </button>
        </div>
    </div>

    {{-- ════ HALAMAN 1 EXPORT ════ --}}
    <div id="exportPage1">

    {{-- ══ KPI Cards ══ --}}
    <div class="row mb-3">
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 bg-primary text-white kpi-card-hover" style="animation:fadeUp .38s ease-out both;">
                <div class="card-body"><div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Countries</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiCountries"><span class="sk-block" style="width:70px;height:24px;display:inline-block;"></span></h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiCountriesSub"><i class="ph ph-globe me-1"></i>Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-globe"></i></div></div>
                </div></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 bg-success text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .05s both;">
                <div class="card-body"><div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Users</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiUsers"><span class="sk-block" style="width:70px;height:24px;display:inline-block;"></span></h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiUsersSub"><i class="ph ph-users me-1"></i>Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-users"></i></div></div>
                </div></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 bg-warning text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .10s both;">
                <div class="card-body"><div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Top Country</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiTopCountry" style="font-size:1.1rem;"><span class="sk-block" style="width:90px;height:24px;display:inline-block;"></span></h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopCountrySub"><i class="ph ph-map-pin me-1"></i>Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-map-pin"></i></div></div>
                </div></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 bg-danger text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .15s both;">
                <div class="card-body"><div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Top Province</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiTopProvince" style="font-size:1.1rem;"><span class="sk-block" style="width:90px;height:24px;display:inline-block;"></span></h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12" id="kpiTopProvinceSub"><i class="ph ph-buildings me-1"></i>Loading…</p>
                    </div>
                    <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-buildings"></i></div></div>
                </div></div>
            </div>
        </div>
    </div>

    {{-- ══ ECharts Donut ══ --}}
    {{--
        SAFARI FIX #1: Container donut SELALU ada di DOM dengan height/width explicit.
        Jangan pakai display:none pada #geoDonutChart.
        Gunakan loading overlay di atasnya (opacity), bukan hide canvas-nya.
    --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div id="card-export-donut">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avtar avtar-xs bg-light-primary rounded"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
                        <div>
                            <h6 class="mb-0">Distribusi Sentiment — Top Locations</h6>
                            <small class="text-muted">Proporsi sentiment per lokasi — klik untuk lihat detail</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div id="geoDonutLegend" class="donut-legend"></div>
                        <div class="d-flex gap-1" data-html2canvas-ignore="true">
                            <button class="card-exp-btn card-exp-btn-pdf" onclick="XGeoExport.runCard('card-export-donut','donut','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                            <button class="card-exp-btn card-exp-btn-img" onclick="XGeoExport.runCard('card-export-donut','donut','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Safari Fix: wrapper relative, canvas selalu ada, overlay di atas --}}
                    <div style="position:relative; width:100%; height:480px;">
                        <div id="geoDonutChart" style="width:100%;height:480px;"></div>
                        <div class="map-loading-overlay" id="geoDonutLoading">
                            <div class="spin-ring"></div>
                            <span style="font-size:11px;font-weight:600;color:var(--slate-400);">Loading chart…</span>
                        </div>
                        <div id="geoDonutEmpty" style="display:none;position:absolute;inset:0;" class="chart-empty">
                            <i class="ph ph-chart-donut"></i><span>No data</span>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Buzzer Map ══ --}}
    <div class="geo-card" data-lazy="geo-buzzer-map" id="card-export-map-buzzer">
        <div class="geo-card-header">
            <div class="geo-card-header-left">
                <div class="geo-avtar"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke="none" fill="none" stroke-width="2"/><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/></svg></div>
                <div><p class="geo-card-title">Buzzer Map</p><p class="geo-card-subtitle">Location Map by Buzzer</p></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="geo-badge">Location Map</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="XGeoExport.runCard('card-export-map-buzzer','map-buzzer','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="XGeoExport.runCard('card-export-map-buzzer','map-buzzer','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="map-with-panel">
            <div class="map-area" style="position:relative;">
                {{-- Safari Fix: div map selalu ada, ukuran explicit --}}
                <div id="geoBuzzerMap" style="width:100%;height:500px;"></div>
                <div class="map-loading-overlay" id="geoBuzzerMapLoading">
                    <div class="spin-ring"></div>
                    <span style="font-size:11px;font-weight:600;color:var(--slate-400);">Loading map…</span>
                </div>
            </div>
            <div class="location-panel">
                <div class="location-panel-title">📍 Locations</div>
                <div class="location-list" id="geoBuzzerList">
                    <div class="panel-skeleton">
                        <div class="sk-line" style="width:90%;"></div><div class="sk-line" style="width:75%;"></div>
                        <div class="sk-line" style="width:85%;"></div><div class="sk-line" style="width:70%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>{{-- /exportPage1 --}}

    {{-- ════ HALAMAN 2 EXPORT ════ --}}
    <div id="exportPage2">

    <div class="geo-card" data-lazy="geo-user-map" id="card-export-map-user">
        <div class="geo-card-header">
            <div class="geo-card-header-left">
                <div class="geo-avtar"><svg viewBox="0 0 24 24"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/></svg></div>
                <div><p class="geo-card-title">Geographic User Distribution</p><p class="geo-card-subtitle">X users by country and province</p></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="geo-badge">All Users</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="XGeoExport.runCard('card-export-map-user','map-user','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="XGeoExport.runCard('card-export-map-user','map-user','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="map-with-panel">
            <div class="map-area" style="position:relative;">
                <div id="geoMap" style="width:100%;height:500px;"></div>
                <div class="map-loading-overlay" id="geoMapLoading">
                    <div class="spin-ring"></div>
                    <span style="font-size:11px;font-weight:600;color:var(--slate-400);">Loading map…</span>
                </div>
            </div>
            <div class="location-panel">
                <div class="location-panel-title">📍 Locations</div>
                <div class="location-list" id="geoUserList">
                    <div class="panel-skeleton">
                        <div class="sk-line" style="width:90%;"></div><div class="sk-line" style="width:75%;"></div>
                        <div class="sk-line" style="width:85%;"></div><div class="sk-line" style="width:70%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="geo-card" data-lazy="geo-sentiment-map" id="card-export-map-sentiment">
        <div class="geo-card-header">
            <div class="geo-card-header-left">
                <div class="geo-avtar"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg></div>
                <div><p class="geo-card-title">Sentiment by Location</p><p class="geo-card-subtitle">Positive, negative, and neutral sentiment distribution</p></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="geo-badge">Sentiment</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="XGeoExport.runCard('card-export-map-sentiment','map-sentiment','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="XGeoExport.runCard('card-export-map-sentiment','map-sentiment','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div class="map-with-panel">
            <div class="map-area" style="position:relative;">
                <div id="geoSentimentMap" style="width:100%;height:500px;"></div>
                <div class="map-loading-overlay" id="geoSentimentMapLoading">
                    <div class="spin-ring"></div>
                    <span style="font-size:11px;font-weight:600;color:var(--slate-400);">Loading map…</span>
                </div>
            </div>
            <div class="location-panel">
                <div class="location-panel-title">📍 Locations</div>
                <div class="location-list" id="geoSentimentList">
                    <div class="panel-skeleton">
                        <div class="sk-line" style="width:90%;"></div><div class="sk-line" style="width:75%;"></div>
                        <div class="sk-line" style="width:85%;"></div><div class="sk-line" style="width:70%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>{{-- /exportPage2 --}}

    {{-- ════ HALAMAN 3 EXPORT ════ --}}
    <div id="exportPage3">

    <div class="charts-row">
        <div class="geo-card" data-lazy="chart-countries" id="card-export-chart-countries">
            <div class="geo-card-header">
                <div class="geo-card-header-left">
                    <div class="geo-avtar"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
                    <div><p class="geo-card-title">Top Countries</p><p class="geo-card-subtitle">Users by country</p></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="geo-badge">Users</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="XGeoExport.runCard('card-export-chart-countries','chart-countries','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="XGeoExport.runCard('card-export-chart-countries','chart-countries','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div style="padding:16px 18px 18px;">
                <div class="spinner-state" id="loadingChartCountries" style="padding:28px 0;"><div class="spin-ring"></div><span>Loading…</span></div>
                <div id="chartCountries" style="display:none;"></div>
            </div>
        </div>
        <div class="geo-card" data-lazy="chart-provinces" id="card-export-chart-provinces">
            <div class="geo-card-header">
                <div class="geo-card-header-left">
                    <div class="geo-avtar"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                    <div><p class="geo-card-title">Top Provinces</p><p class="geo-card-subtitle" id="provSubtitle">Province breakdown</p></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="geo-badge">Detail</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="XGeoExport.runCard('card-export-chart-provinces','chart-provinces','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="XGeoExport.runCard('card-export-chart-provinces','chart-provinces','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div style="padding:16px 18px 18px;">
                <div class="spinner-state" id="loadingChartProvinces" style="padding:28px 0;"><div class="spin-ring"></div><span>Loading…</span></div>
                <div id="chartProvinces" style="display:none;"></div>
            </div>
        </div>
        <div class="geo-card" data-lazy="chart-sentiment-donut" id="card-export-chart-sentiment">
            <div class="geo-card-header">
                <div class="geo-card-header-left">
                    <div class="geo-avtar"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg></div>
                    <div><p class="geo-card-title">Sentiment Summary</p><p class="geo-card-subtitle">Overall distribution</p></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="geo-badge">Sentiment</span>
                    <div class="d-flex gap-1" data-html2canvas-ignore="true">
                        <button class="card-exp-btn card-exp-btn-pdf" onclick="XGeoExport.runCard('card-export-chart-sentiment','chart-sentiment','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                        <button class="card-exp-btn card-exp-btn-img" onclick="XGeoExport.runCard('card-export-chart-sentiment','chart-sentiment','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                    </div>
                </div>
            </div>
            <div style="padding:16px 18px 18px;display:flex;flex-direction:column;align-items:center;">
                <div class="spinner-state" id="loadingChartSentiment" style="width:100%;padding:28px 0;"><div class="spin-ring"></div><span>Loading…</span></div>
                {{-- Safari Fix: pakai wrapper div dengan explicit size, canvas append ke sini --}}
                <div id="chartSentimentDonut" style="position:relative;width:180px;height:180px;display:none;"></div>
                <div id="chartSentimentLegend" style="width:100%;max-width:260px;margin-top:14px;display:flex;flex-direction:column;gap:8px;"></div>
            </div>
        </div>
    </div>

    <div class="geo-card" data-lazy="top-locations" id="card-export-top-locations">
        <div class="geo-card-header">
            <div class="geo-card-header-left">
                <div class="geo-avtar"><svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></div>
                <div><p class="geo-card-title">Top Author Locations</p><p class="geo-card-subtitle">Ranking of locations by author count</p></div>
            </div>
            <div id="topLocBtnWrap" style="display:flex;align-items:center;gap:8px;">
                <span class="geo-badge">Rankings</span>
                <div class="d-flex gap-1" data-html2canvas-ignore="true">
                    <button class="card-exp-btn card-exp-btn-pdf" onclick="XGeoExport.runCard('card-export-top-locations','top-locations','pdf',this)"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
                    <button class="card-exp-btn card-exp-btn-img" onclick="XGeoExport.runCard('card-export-top-locations','top-locations','image',this)"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
                </div>
            </div>
        </div>
        <div style="padding:0 18px 18px;">
            <div class="spinner-state" id="loadingTopLocations" style="padding:28px 0;"><div class="spin-ring"></div><span>Loading…</span></div>
            <div id="topLocationsTable"></div>
        </div>
    </div>

    </div>{{-- /exportPage3 --}}

    </div>{{-- /pageExportArea --}}
    @endif
</div>

{{-- ══ Export Toast ══ --}}
<div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
</div>

{{-- ══ Modal ══ --}}
<div class="geo-modal-overlay" id="geoLocModal" onclick="if(event.target===this)XGeo.closeModal()">
    <div class="geo-modal">
        <div class="geo-modal-header">
            <h3>All Author Locations</h3>
            <button class="geo-modal-close" onclick="XGeo.closeModal()"><i class="ph ph-x"></i></button>
        </div>
        <div class="geo-modal-body" id="geoLocModalBody"></div>
    </div>
</div>

{{-- ══ Slide Panel ══ --}}
<div class="do-panel-overlay" id="geoPanelOverlay" onclick="GeoPanel.close()"></div>
<div class="do-panel" id="geoSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="geoPanelDot" style="background:var(--primary);"></div>
        <span class="do-panel-title" id="geoPanelTitle">X Geographic</span>
        <button class="do-panel-close" onclick="GeoPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta"><i class="ph ph-magnifying-glass" style="font-size:11px;"></i><span id="geoPanelMeta">—</span></div>
    </div>
    <div class="do-panel-list" id="geoPanelList"></div>
    <div class="do-detail-panel" id="geoDetailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="GeoDetail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="geoDetailTitle">Detail</span>
            <button class="do-panel-close" onclick="GeoPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="geoDetailBody"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>

<script>
'use strict';

// Province/location name normalization map
function normalizeProvinceName(name) {
    if (!name) return '';
    const map = {
        'DKI JAKARTA': 'DKI Jakarta',
        'JAKARTA': 'DKI Jakarta',
        'JAWA BARAT': 'Jawa Barat',
        'JABAR': 'Jawa Barat',
        'JAWA TENGAH': 'Jawa Tengah',
        'JATENG': 'Jawa Tengah',
        'JAWA TIMUR': 'Jawa Timur',
        'JATIM': 'Jawa Timur',
        'BANTEN': 'Banten',
        'DI YOGYAKARTA': 'DI Yogyakarta',
        'YOGYAKARTA': 'DI Yogyakarta',
        'BALI': 'Bali',
        'SUMATERA UTARA': 'Sumatera Utara',
        'SUMUT': 'Sumatera Utara',
        'SUMATERA BARAT': 'Sumatera Barat',
        'SUMBAR': 'Sumatera Barat',
        'SUMATERA SELATAN': 'Sumatera Selatan',
        'SUMSEL': 'Sumatera Selatan',
        'RIAU': 'Riau',
        'KEPULAUAN RIAU': 'Kepulauan Riau',
        'KEPRI': 'Kepulauan Riau',
        'JAMBI': 'Jambi',
        'BENGKULU': 'Bengkulu',
        'LAMPUNG': 'Lampung',
        'BANGKA BELITUNG': 'Bangka Belitung',
        'BABEL': 'Bangka Belitung',
        'KALIMANTAN BARAT': 'Kalimantan Barat',
        'KALBAR': 'Kalimantan Barat',
        'KALIMANTAN TENGAH': 'Kalimantan Tengah',
        'KALTENG': 'Kalimantan Tengah',
        'KALIMANTAN SELATAN': 'Kalimantan Selatan',
        'KALSEL': 'Kalimantan Selatan',
        'KALIMANTAN TIMUR': 'Kalimantan Timur',
        'KALTIM': 'Kalimantan Timur',
        'KALIMANTAN UTARA': 'Kalimantan Utara',
        'KALTARA': 'Kalimantan Utara',
        'SULAWESI UTARA': 'Sulawesi Utara',
        'SULUT': 'Sulawesi Utara',
        'SULAWESI TENGAH': 'Sulawesi Tengah',
        'SULTENG': 'Sulawesi Tengah',
        'SULAWESI SELATAN': 'Sulawesi Selatan',
        'SULSEl': 'Sulawesi Selatan',
        'SULAWESI TENGGARA': 'Sulawesi Tenggara',
        'SULTRA': 'Sulawesi Tenggara',
        'GORONTALO': 'Gorontalo',
        'SULAWESI BARAT': 'Sulawesi Barat',
        'SULBAR': 'Sulawesi Barat',
        'MALUKU': 'Maluku',
        'MALUKU UTARA': 'Maluku Utara',
        'PAPUA': 'Papua',
        'PAPUA BARAT': 'Papua Barat',
        'PAPUA TENGAH': 'Papua Tengah',
        'PAPUA PEGUNUNGAN': 'Papua Pegunungan',
        'PAPUA SELATAN': 'Papua Selatan',
        'PAPUA BARAT DAYA': 'Papua Barat Daya',
    };
    let key = String(name).trim().toUpperCase();
    return map[key] || name;
}
/* ══════════════════════════════════════════════════════
   SAFARI-SAFE HELPERS
   Masalah utama Safari dengan chart:
   1. echarts.init() / Chart.js pada container display:none → dimensi 0×0
   2. Leaflet.invalidateSize() perlu dipanggil setelah container visible
   3. requestAnimationFrame tidak cukup — Safari butuh setTimeout minimal 1 frame
   4. IntersectionObserver callback bisa terlambat di Safari
   Solusi: gunakan _safeInit() yang memastikan container sudah visible & berukuran
══════════════════════════════════════════════════════ */

/**
 * Safari-safe: tunggu sampai element benar-benar punya dimensi > 0
 * sebelum inisialisasi chart. Retry max 20x tiap 80ms.
 */
function _waitForSize(el, cb, maxTry) {
    maxTry = maxTry || 20;
    var tries = 0;
    function check() {
        var w = el.offsetWidth, h = el.offsetHeight;
        if (w > 0 && h > 0) { cb(); return; }
        tries++;
        if (tries >= maxTry) { cb(); return; } // fallback tetap jalankan
        setTimeout(check, 80);
    }
    check();
}

/**
 * Sembunyikan loading overlay dengan fade, lalu hapus dari flow
 */
function _hideOverlay(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    setTimeout(function() { el.classList.add('gone'); }, 420);
}

const DONUT_COLORS = ['#038047','#273B4A','#F59E0B','#06B6D4','#EF4444'];
const _$   = id => document.getElementById(id);
const numF = n  => parseInt(n||0).toLocaleString('id-ID');
const numK = n  => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc  = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

window._leafletMaps = {};

/* ══════════════════════════════════════════════════════
   XGeo — main data & render
══════════════════════════════════════════════════════ */
const XGeo = {
    projectId : '{{ $projectId ?? "" }}',
    startDate : '{{ $startDate ?? "" }}',
    endDate   : '{{ $endDate   ?? "" }}',
    _loaded   : new Set(),
    _geoUserCache   : null,
    _sentimentCache : null,
    _allLocations   : [],

    init() {
        /* ── Safari Fix: pakai requestAnimationFrame + setTimeout combo ── */
        /* Pastikan DOM fully painted sebelum observe */
        var self = this;
        window.addEventListener('load', function() {
            requestAnimationFrame(function() {
                setTimeout(function() { self._initObserver(); }, 100);
            });
        });
        /* Fallback: kalau load sudah terlambat, langsung init */
        if (document.readyState === 'complete') {
            requestAnimationFrame(function() {
                setTimeout(function() { self._initObserver(); }, 100);
            });
        }
        self._loadDonut();
    },

    _initObserver() {
        var self = this;
        /* ── Safari Fix: IntersectionObserver dengan threshold rendah ── */
        var obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(e) {
                if (!e.isIntersecting) return;
                var el  = e.target;
                var sec = el.dataset.lazy;
                if (!sec || self._loaded.has(sec)) return;
                self._loaded.add(sec);
                obs.unobserve(el);
                /* Safari Fix: delay kecil agar browser selesai layout */
                setTimeout(function() { self._load(sec, el); }, 60);
            });
        }, { rootMargin: '200px', threshold: 0 });

        document.querySelectorAll('[data-lazy]').forEach(function(el) {
            obs.observe(el);
        });
    },

    async _load(sec, el) {
        try {
            switch(sec) {
                case 'geo-buzzer-map'        : await this.loadGeoBuzzerMap(el);         break;
                case 'geo-user-map'          : await this.loadGeoUserMap(el);           break;
                case 'geo-sentiment-map'     : await this.loadGeoSentimentMap(el);      break;
                case 'top-locations'         : await this.loadTopLocations(el);         break;
                case 'chart-countries'       : await this.loadChartCountries(el);       break;
                case 'chart-provinces'       : await this.loadChartProvinces(el);       break;
                case 'chart-sentiment-donut' : await this.loadChartSentimentSmall(el);  break;
            }
            el.dataset.loaded = 'true';
        } catch(err) { console.error('❌ ' + sec + ':', err); }
    },

    async fetchGeoUser() {
        if (this._geoUserCache) return this._geoUserCache;
        var j = await (await fetch('/mk/api/x/geo-user?project_id=' + this.projectId + '&start_date=' + this.startDate + '&end_date=' + this.endDate)).json();
        this._geoUserCache = j;
        this._loadStats(j);
        return j;
    },
    async fetchGeoSentiment() {
        if (this._sentimentCache) return this._sentimentCache;
        var j = await (await fetch('/mk/api/x/geo-sentiment?project_id=' + this.projectId + '&start_date=' + this.startDate + '&end_date=' + this.endDate)).json();
        this._sentimentCache = j;
        return j;
    },

    parseGeoRows(result) {
        if (!result?.success) return [];
        var d = result.data; if (!d) return [];
        if (d.country && Array.isArray(d.country.rows)) return d.country.rows;
        if (Array.isArray(d.rows)) return d.rows;
        if (Array.isArray(d)) return d;
        if (typeof d === 'object') {
            var e = Object.entries(d);
            if (e.length && typeof e[0][1] === 'number') return e.map(([name,count]) => ({name,count}));
        }
        return [];
    },
    parseGeoTotal(r) {
        if (!r?.success || !r.data) return 0;
        var d = r.data;
        return d.country?.total || d.total || 0;
    },

    _loadStats(result) {
        var rows = this.parseGeoRows(result), total = this.parseGeoTotal(result), top = rows[0];
        var set = (id,v) => { var e = _$(id); if(e) e.textContent = v; };
        var sub = (id,h) => { var e = _$(id); if(e) e.innerHTML = h; };
        set('kpiCountries', rows.length); set('kpiUsers', numF(total));
        sub('kpiCountriesSub', '<i class="ph ph-globe me-1"></i>' + rows.length + ' countries detected');
        sub('kpiUsersSub', '<i class="ph ph-users me-1"></i>' + numK(total) + ' total identified');
        if (top) {
                set('kpiTopCountry', normalizeProvinceName(top.name) || 'N/A');
                sub('kpiTopCountrySub', '<i class="ph ph-chart-bar me-1"></i>' + numF(top.count) + ' users');
                var provs = Object.entries(top.detail || {}).sort((a,b) => b[1]-a[1]);
                set('kpiTopProvince', provs.length ? normalizeProvinceName(provs[0][0]) : 'N/A');
                if (provs.length) sub('kpiTopProvinceSub', '<i class="ph ph-buildings me-1"></i>' + numF(provs[0][1]) + ' users');
        } else { set('kpiTopCountry','N/A'); set('kpiTopProvince','N/A'); }
    },

    async _loadDonut() {
        var result = await this.fetchGeoSentiment();
        var rows   = this.parseGeoRows(result);
        var loadEl = _$('geoDonutLoading'), emptyEl = _$('geoDonutEmpty'), legEl = _$('geoDonutLegend');
        var chartEl = _$('geoDonutChart');

        if (!rows.length) {
            _hideOverlay('geoDonutLoading');
            if (emptyEl) emptyEl.style.display = 'flex';
            return;
        }

        var top5 = [...rows].sort((a,b) => parseInt(b.count||0) - parseInt(a.count||0)).slice(0,5);
        var total = top5.reduce((s,r) => s + parseInt(r.count||0), 0);

        if (legEl) legEl.innerHTML = top5.map((r,i) => {
            var n = r.name || 'Unknown', sn = n.length > 22 ? n.slice(0,21)+'…' : n;
            return '<div class="donut-leg-item"><span class="donut-dot" style="background:' + DONUT_COLORS[i] + ';"></span>' + esc(sn) + ' · ' + numF(r.count) + '</div>';
        }).join('');

        if (typeof echarts === 'undefined') {
            _hideOverlay('geoDonutLoading');
            return;
        }

        /* ── Safari Fix: pastikan container punya ukuran sebelum init echarts ── */
        _waitForSize(chartEl, () => {
            _hideOverlay('geoDonutLoading');

            if (window.__geoDonutChart) {
                try { window.__geoDonutChart.dispose(); } catch(e) {}
            }

            var chart = echarts.init(chartEl, null, { renderer: 'canvas' });
            window.__geoDonutChart = chart;

            /* Safari Fix: resize listener dengan debounce */
            var _resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(_resizeTimer);
                _resizeTimer = setTimeout(function() {
                    try { if (!chart.isDisposed()) chart.resize(); } catch(e) {}
                }, 150);
            });

            var pieData = top5.map((row,i) => ({
                name: row.name || 'Unknown',
                value: parseInt(row.count || 0),
                _pos: parseInt(row.pos || 0),
                _neg: parseInt(row.neg || 0),
                _net: parseInt(row.net || 0),
                itemStyle: { color: DONUT_COLORS[i] },
            }));

            chart.setOption({
                backgroundColor: 'transparent',
                tooltip: { show: false },
                animation: true,
                animationDuration: 1000,
                animationEasing: 'cubicOut',
                animationDelay: idx => idx * 80,
                series: [{
                    type: 'pie',
                    radius: ['38%','62%'],
                    center: ['50%','50%'],
                    avoidLabelOverlap: true,
                    selectedMode: false,
                    minAngle: 8,
                    itemStyle: { borderColor: '#fff', borderWidth: 3 },
                    label: {
                        show: true,
                        position: 'outside',
                        alignTo: 'edge',
                        edgeDistance: 20,
                        lineHeight: 18,
                        fontSize: 11,
                        fontFamily: 'inherit',
                        color: '#334155',
                        fontWeight: '500',
                        formatter: function(p) {
                            var row = top5[p.dataIndex];
                            var pos = parseInt(row.pos||0), neg = parseInt(row.neg||0), net = parseInt(row.net||0);
                            return '{title|' + p.name + '}\n{pos|+' + numK(pos) + '}  {neg|-' + numK(neg) + '}  {neu|~' + numK(net) + '}\n({val|' + numF(p.value) + '} users, {pct|' + p.percent.toFixed(1) + '%})';
                        },
                        rich: {
                            title: { fontSize:11, fontWeight:'700', color:'#1e293b', lineHeight:18 },
                            val:   { fontSize:11, fontWeight:'700', color:'#038047' },
                            pct:   { fontSize:11, fontWeight:'600', color:'#64748b' },
                            pos:   { fontSize:10, fontWeight:'700', color:'#22c55e' },
                            neg:   { fontSize:10, fontWeight:'700', color:'#ef4444' },
                            neu:   { fontSize:10, fontWeight:'600', color:'#94a3b8' },
                        }
                    },
                    labelLine: { show:true, length:18, length2:24, smooth:0.3, lineStyle:{ width:1.5, color:'#94A3B8' } },
                    emphasis: {
                        scale: false,
                        itemStyle: { shadowBlur:0, shadowColor:'transparent', borderWidth:3, borderColor:'#fff', opacity:1 },
                        labelLine: { lineStyle: { width:2.5, color:'#273B4A' } },
                        label: { show:true }
                    },
                    select: { disabled: true },
                    data: pieData,
                }],
                graphic: [
                    { type:'text', left:'center', top:'46%', z:100, style:{ text:numK(total), fill:'#0f172a', font:"800 28px inherit", textAlign:'center' } },
                    { type:'text', left:'center', top:'54%', z:100, style:{ text:'TOTAL USERS', fill:'#94a3b8', font:"600 9px inherit", textAlign:'center' } },
                ]
            });

            chart.on('click', function(p) {
                var row = top5[p.dataIndex];
                if (row) GeoPanel.openLocation(row);
            });

            /* Custom tooltip */
            var _ttEl = document.getElementById('geoCustomTT');
            if (!_ttEl) {
                _ttEl = document.createElement('div');
                _ttEl.id = 'geoCustomTT';
                _ttEl.style.cssText = 'position:fixed;z-index:9999;pointer-events:none;background:#1e293b;color:#fff;border:1px solid #334155;border-radius:6px;padding:10px 14px;max-width:280px;font-size:12px;line-height:1.5;display:none;box-shadow:0 8px 24px rgba(0,0,0,.32);font-family:inherit;opacity:0;transform:translateY(6px) scale(.97);transition:opacity .18s ease,transform .18s ease;';
                document.body.appendChild(_ttEl);
            }
            var _ttTimer = null;

            chart.on('mouseover', function(p) {
                if (p.componentType !== 'series') return;
                var row = top5[p.dataIndex], color = DONUT_COLORS[p.dataIndex];
                var pos = parseInt(row.pos||0), neg = parseInt(row.neg||0), net = parseInt(row.net||0);
                clearTimeout(_ttTimer);
                _ttEl.innerHTML = '<div style="display:flex;align-items:center;gap:6px;margin-bottom:5px;"><span style="width:9px;height:9px;border-radius:50%;background:' + color + ';flex-shrink:0;display:inline-block;"></span><b style="font-size:12.5px;">' + esc(p.name) + '</b></div><div style="display:grid;grid-template-columns:repeat(3,1fr);gap:5px;margin-bottom:7px;"><div style="text-align:center;background:#f0fdf4;border-radius:4px;padding:4px;"><div style="font-weight:800;color:#22c55e;">' + numF(pos) + '</div><div style="font-size:9px;color:#64748b;text-transform:uppercase;">Pos</div></div><div style="text-align:center;background:#f8fafc;border-radius:4px;padding:4px;"><div style="font-weight:800;color:#94a3b8;">' + numF(net) + '</div><div style="font-size:9px;color:#64748b;text-transform:uppercase;">Neu</div></div><div style="text-align:center;background:#fef2f2;border-radius:4px;padding:4px;"><div style="font-weight:800;color:#ef4444;">' + numF(neg) + '</div><div style="font-size:9px;color:#64748b;text-transform:uppercase;">Neg</div></div></div><div style="display:flex;align-items:center;gap:8px;"><b style="font-size:13px;">' + numF(p.value) + ' users</b><span style="color:' + color + ';font-weight:700;">' + p.percent.toFixed(1) + '%</span></div>';
                _ttEl.style.display = 'block';
                requestAnimationFrame(function() {
                    _ttEl.style.opacity = '1';
                    _ttEl.style.transform = 'translateY(0) scale(1)';
                });
            });

            chart.on('mouseout', function() {
                _ttEl.style.opacity = '0';
                _ttEl.style.transform = 'translateY(6px) scale(.97)';
                _ttTimer = setTimeout(function() { _ttEl.style.display = 'none'; }, 180);
            });

            chartEl.addEventListener('mousemove', function(e) {
                if (_ttEl.style.display === 'none') return;
                var vw = window.innerWidth, vh = window.innerHeight;
                var tw = _ttEl.offsetWidth + 16, th = _ttEl.offsetHeight + 16;
                var x = e.clientX + 18, y = e.clientY - 10;
                if (x + tw > vw) x = e.clientX - tw;
                if (y + th > vh) y = e.clientY - th;
                _ttEl.style.left = x + 'px';
                _ttEl.style.top  = y + 'px';
            });
        });
    },

    /* ── Leaflet Map renderer ── */
    renderMap(elementId, rows, getMarkerProps) {
        var mapEl = document.getElementById(elementId);
        if (!mapEl) return { map: null, markerRefs: [] };

        /* ── Safari Fix: invalidate existing map jika ada ── */
        if (window._leafletMaps[elementId]) {
            try { window._leafletMaps[elementId].remove(); } catch(e) {}
            delete window._leafletMaps[elementId];
        }

        var map = L.map(elementId, {
            center: [-2.5, 118], zoom: 5,
            scrollWheelZoom: false,
            preferCanvas: true,
            /* Safari Fix: zoomAnimation false mencegah blank tile di Safari */
            zoomAnimation: true,
            fadeAnimation: true,
        });
        window._leafletMaps[elementId] = map;

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap, © CARTO',
            subdomains: 'abcd',
            maxZoom: 19,
            crossOrigin: true,
            /* Safari Fix: detectRetina untuk HiDPI display di macOS/iOS */
            detectRetina: true,
        }).addTo(map);

        /* Scroll overlay */
        var overlay = document.createElement('div');
        overlay.className = 'map-scroll-overlay';
        overlay.innerHTML = '<div class="map-scroll-hint"><svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#fff;fill:none;stroke-width:2;"><rect x="5" y="2" width="14" height="20" rx="7"/><line x1="12" y1="6" x2="12" y2="10"/></svg>Use Ctrl + Scroll to zoom</div>';
        mapEl.style.position = 'relative';
        mapEl.appendChild(overlay);

        mapEl.addEventListener('wheel', function(e) {
            if (!e.ctrlKey) {
                overlay.classList.add('visible');
                clearTimeout(overlay._t);
                overlay._t = setTimeout(function() { overlay.classList.remove('visible'); }, 1800);
            } else {
                map.scrollWheelZoom.enable();
                overlay.classList.remove('visible');
            }
        });
        map.on('zoomend', function() {
            setTimeout(function() { map.scrollWheelZoom.disable(); }, 300);
        });

        /* ── Safari Fix: invalidateSize setelah map fully visible ── */
        setTimeout(function() {
            try { map.invalidateSize({ animate: false }); } catch(e) {}
        }, 300);

        if (!rows.length) return { map: map, markerRefs: [] };

        var maxCount = Math.max.apply(null, rows.map(function(p) { return parseInt(p.count||0); }));
        var markerRefs = [];

        rows.forEach(function(p) {
            var lat = parseFloat(p.latitude || 0), lng = parseFloat(p.longitude || 0);
            if (lat === 0 && lng === 0) { markerRefs.push(null); return; }
            var props = getMarkerProps(p);
            var color = props.color, count = props.count, popup = props.popup;

            if (count >= 10) {
                var r = Math.min(Math.max(Math.sqrt(count) * 2500, 5000), 50000);
                L.circle([lat, lng], {
                    radius: r,
                    fillColor: color, color: color,
                    weight: 1, opacity: .3,
                    fillOpacity: Math.min(.15 + (count / maxCount) * .45, .6)
                }).addTo(map);
            }

            var pin = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: '',
                    html: '<div style="width:12px;height:12px;background:' + color + ';border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>',
                    iconSize: [12, 12],
                    iconAnchor: [6, 6]
                })
            }).addTo(map).bindPopup(popup);
            markerRefs.push({ marker: pin, lat: lat, lng: lng });

            var label = count > 999 ? (count/1000).toFixed(1)+'k' : String(count);
            L.marker([lat, lng], {
                icon: L.divIcon({
                    className: '',
                    html: '<div style="font-family:inherit;font-size:10px;font-weight:800;color:#fff;background:' + color + ';padding:2px 7px;border-radius:3px;border:1.5px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);white-space:nowrap;">' + label + '</div>',
                    iconSize: [36, 18],
                    iconAnchor: [18, 24]
                }),
                interactive: false
            }).addTo(map);
        });

        return { map: map, markerRefs: markerRefs };
    },

    buildLocationPanel(listId, rows, mapResult, defaultColor, useSentiment) {
        var listEl = _$(listId);
        if (!listEl) return;
        var map = mapResult.map, markerRefs = mapResult.markerRefs;
        var valid = rows.filter(function(p) {
            return !(parseFloat(p.latitude||0) === 0 && parseFloat(p.longitude||0) === 0);
        });
        if (!valid.length) {
            listEl.innerHTML = '<div style="padding:24px 14px;font-size:12px;color:var(--slate-400);text-align:center;font-weight:600;">No location data</div>';
            return;
        }
        var sorted = [...valid].sort((a,b) => parseInt(b.count||0) - parseInt(a.count||0));
        listEl.innerHTML = sorted.slice(0,8).map(function(p, rank) {
            var name = p.name || 'Unknown', count = parseInt(p.count || 0);
            var color = defaultColor || '#038047';
            if (useSentiment) {
                var pos = parseInt(p.pos||0), neg = parseInt(p.neg||0), net = parseInt(p.net||0);
                if (pos > neg && pos > net) color = '#22c55e';
                else if (neg > pos && neg > net) color = '#ef4444';
                else color = '#64748b';
            }
            var rkCls = rank < 3 ? ' location-item-rank--' + (rank+1) : '';
            var lbl   = count > 999 ? (count/1000).toFixed(1)+'k' : count;
            return '<div class="location-item" data-rank="' + rank + '">' +
                '<div class="location-item-rank' + rkCls + '">' + (rank+1) + '</div>' +
                '<div class="location-item-info">' +
                '<div class="location-item-name" title="' + esc(name) + '">' + esc(name) + '</div>' +
                '<div class="location-item-count">' + lbl + ' ' + (useSentiment ? 'mentions' : 'users') + '</div>' +
                '</div>' +
                '<div class="location-item-dot" style="background:' + color + ';"></div>' +
                '</div>';
        }).join('');

        listEl.querySelectorAll('.location-item').forEach(function(item, i) {
            item.addEventListener('click', function() {
                var p = sorted[i];
                var lat = parseFloat(p.latitude||0), lng = parseFloat(p.longitude||0);
                if (lat === 0 && lng === 0) return;
                if (map) map.flyTo([lat, lng], 8, { animate: true, duration: 1 });
                var ref = markerRefs.find(function(r) {
                    return r && Math.abs(r.lat - lat) < .001 && Math.abs(r.lng - lng) < .001;
                });
                if (ref) setTimeout(function() { ref.marker.openPopup(); }, 800);
                listEl.querySelectorAll('.location-item').forEach(function(el) { el.classList.remove('active'); });
                item.classList.add('active');
            });
        });
    },

    async loadGeoBuzzerMap(card) {
        try {
            var r      = await fetch('/mk/api/geo-users?project_id=' + this.projectId + '&start_date=' + this.startDate + '&end_date=' + this.endDate);
            var result = await r.json();
            var rows   = this.parseGeoRows(result);
            var mapEl  = _$('geoBuzzerMap');
            if (!mapEl) return;

            /* Safari Fix: tunggu element visible dan berukuran */
            var self = this;
            _waitForSize(mapEl, function() {
                var markers = self.renderMap('geoBuzzerMap', rows, function(p) {
                    return {
                        color: '#038047',
                        count: parseInt(p.count || 0),
                        popup: '<div style="font-family:inherit;text-align:center;padding:8px;min-width:140px;"><div style="font-weight:700;font-size:15px;color:#0f172a;margin-bottom:6px;">' + esc(p.name||'Unknown') + '</div><div style="font-size:24px;font-weight:800;color:#038047;">' + numF(p.count) + '</div><div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;font-weight:700;">buzzers</div></div>'
                    };
                });
                self.buildLocationPanel('geoBuzzerList', rows, markers, '#038047', false);
                _hideOverlay('geoBuzzerMapLoading');
                card.dataset.loaded = 'true';
            });
        } catch(e) { console.error('Buzzer map err:', e); _hideOverlay('geoBuzzerMapLoading'); }
    },

    async loadGeoUserMap(card) {
        var result = await this.fetchGeoUser();
        var rows   = this.parseGeoRows(result);
        var mapEl  = _$('geoMap');
        if (!mapEl) return;
        var self = this;
        _waitForSize(mapEl, function() {
            var markers = self.renderMap('geoMap', rows, function(p) {
                return {
                    color: '#038047',
                    count: parseInt(p.count || 0),
                    popup: '<div style="font-family:inherit;text-align:center;padding:8px;min-width:140px;"><div style="font-weight:700;font-size:15px;color:#0f172a;margin-bottom:6px;">' + esc(p.name||'Unknown') + '</div><div style="font-size:24px;font-weight:800;color:#038047;">' + numF(p.count) + '</div><div style="font-size:10px;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;font-weight:700;">users</div></div>'
                };
            });
            self.buildLocationPanel('geoUserList', rows, markers, '#038047', false);
            _hideOverlay('geoMapLoading');
            card.dataset.loaded = 'true';
        });
    },

    async loadGeoSentimentMap(card) {
        var result = await this.fetchGeoSentiment();
        var rows   = this.parseGeoRows(result);
        var mapEl  = _$('geoSentimentMap');
        if (!mapEl) return;
        var self = this;
        _waitForSize(mapEl, function() {
            var markers = self.renderMap('geoSentimentMap', rows, function(p) {
                var count = parseInt(p.count||0), pos = parseInt(p.pos||0), neg = parseInt(p.neg||0), net = parseInt(p.net||0), safe = count || 1;
                var color = '#64748b', sentiment = 'Neutral';
                if (pos > neg && pos > net) { color = '#22c55e'; sentiment = 'Positive'; }
                else if (neg > pos && neg > net) { color = '#ef4444'; sentiment = 'Negative'; }
                return {
                    color: color, count: count,
                    popup: '<div style="font-family:inherit;text-align:center;padding:8px;min-width:200px;"><div style="font-weight:700;font-size:15px;color:#0f172a;margin-bottom:6px;">' + esc(p.name||'Unknown') + '</div><div style="display:inline-block;padding:3px 10px;background:' + color + '20;border-radius:20px;margin-bottom:8px;"><span style="font-size:10px;font-weight:800;color:' + color + ';text-transform:uppercase;">' + sentiment + '</span></div><div style="font-size:24px;font-weight:800;color:' + color + ';">' + numF(count) + '</div><div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-top:10px;border-top:1px solid #e2e8f0;padding-top:10px;"><div style="text-align:center;background:#f0fdf4;border-radius:5px;padding:5px;"><div style="font-size:14px;font-weight:800;color:#22c55e;">' + pos + '</div><div style="font-size:9px;color:#94a3b8;text-transform:uppercase;font-weight:700;">Pos</div><div style="font-size:8px;color:#94a3b8;">' + ((pos/safe)*100).toFixed(1) + '%</div></div><div style="text-align:center;background:#f8fafc;border-radius:5px;padding:5px;"><div style="font-size:14px;font-weight:800;color:#94a3b8;">' + net + '</div><div style="font-size:9px;color:#94a3b8;text-transform:uppercase;font-weight:700;">Neu</div><div style="font-size:8px;color:#94a3b8;">' + ((net/safe)*100).toFixed(1) + '%</div></div><div style="text-align:center;background:#fef2f2;border-radius:5px;padding:5px;"><div style="font-size:14px;font-weight:800;color:#ef4444;">' + neg + '</div><div style="font-size:9px;color:#94a3b8;text-transform:uppercase;font-weight:700;">Neg</div><div style="font-size:8px;color:#94a3b8;">' + ((neg/safe)*100).toFixed(1) + '%</div></div></div></div>'
                };
            });
            self.buildLocationPanel('geoSentimentList', rows, markers, null, true);
            _hideOverlay('geoSentimentMapLoading');
            card.dataset.loaded = 'true';
        });
    },

    async loadChartCountries(card) {
        var result = await this.fetchGeoUser(), rows = this.parseGeoRows(result);
        var ldEl = _$('loadingChartCountries'), el = _$('chartCountries');
        if (!el) return;
        if (!rows.length) { if(ldEl) ldEl.innerHTML = '<div style="padding:20px;text-align:center;color:var(--slate-400);font-size:12px;">No data</div>'; return; }
        var colors = ['#038047','#059669','#0891b2','#7c3aed','#db2777','#ea580c'];
        var top = rows.slice(0,6), max = parseInt(top[0]?.count) || 1;
        el.innerHTML = top.map(function(row, i) {
            var count = parseInt(row.count), pct = Math.max(Math.round((Math.log(count+1)/Math.log(max+1))*100), 6);
            return '<div class="country-bar-row"><div class="country-bar-header"><span class="country-bar-name">' + esc(row.name) + '</span><span class="country-bar-count">' + numF(count) + '</span></div><div class="country-bar-track"><div class="country-bar-fill" data-pct="' + pct + '" style="background:' + colors[i % colors.length] + ';"></div></div></div>';
        }).join('');
        if (ldEl) ldEl.style.display = 'none';
        el.style.display = 'block';
        requestAnimationFrame(function() {
            el.querySelectorAll('.country-bar-fill').forEach(function(b) { b.style.width = b.dataset.pct + '%'; });
        });
    },

    async loadChartProvinces(card) {
        var result = await this.fetchGeoUser(), rows = this.parseGeoRows(result);
        var ldEl = _$('loadingChartProvinces'), el = _$('chartProvinces');
        if (!el) return;
        var top = rows[0];
        if (!top?.detail) { if(ldEl) ldEl.innerHTML = '<div style="padding:20px;text-align:center;color:var(--slate-400);font-size:12px;">No province data</div>'; return; }
        var subEl = _$('provSubtitle');
            if (subEl) subEl.textContent = normalizeProvinceName(top.name) + ' provinces';
            var provinces = Object.entries(top.detail)
                .filter(([k]) => k && !k.startsWith('\u0000') && k.trim())
                .map(([name, count]) => ({ name: normalizeProvinceName(name), count: parseInt(count) }))
                .sort((a,b) => b.count - a.count)
                .slice(0,8);
        var max = provinces[0]?.count || 1;
        el.innerHTML = provinces.map(function(p) {
            var pct = Math.round((p.count / max) * 100);
                return '<div class="prov-bar-row"><div class="prov-bar-header"><span class="prov-bar-name">' + esc(p.name) + '</span><span class="prov-bar-count">' + numF(p.count) + '</span></div><div class="prov-bar-track"><div class="prov-bar-fill" data-pct="' + pct + '"></div></div></div>';
        }).join('');
        if (ldEl) ldEl.style.display = 'none';
        el.style.display = 'block';
        requestAnimationFrame(function() {
            el.querySelectorAll('.prov-bar-fill').forEach(function(b) { b.style.width = b.dataset.pct + '%'; });
        });
    },

    async loadChartSentimentSmall(card) {
        var result = await this.fetchGeoSentiment(), rows = this.parseGeoRows(result);
        var pos = 0, neg = 0, net = 0;
        rows.forEach(function(r) { pos += parseInt(r.pos||0); neg += parseInt(r.neg||0); net += parseInt(r.net||0); });
        var total = pos + neg + net || 1;
        var ldEl = _$('loadingChartSentiment'), canvasEl = _$('chartSentimentDonut'), legendEl = _$('chartSentimentLegend');
        if (ldEl) ldEl.style.display = 'none';
        if (canvasEl) canvasEl.style.display = 'block';

        /* Safari Fix: create canvas element dan append ke container, bukan inject langsung */
        _waitForSize(canvasEl, function() {
            var canvas = document.createElement('canvas');
            canvas.width  = 180;
            canvas.height = 180;
            canvas.style.cssText = 'width:180px;height:180px;display:block;';
            canvasEl.innerHTML = '';
            canvasEl.appendChild(canvas);

            /* Safari Fix: requestAnimationFrame sebelum getContext */
            requestAnimationFrame(function() {
                var ctx = canvas.getContext('2d');
                if (!ctx) return;
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Positive','Neutral','Negative'],
                        datasets: [{
                            data: [pos, net, neg],
                            backgroundColor: ['#22c55e','#94a3b8','#ef4444'],
                            borderColor: '#fff',
                            borderWidth: 3,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: false,
                        cutout: '62%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx2) {
                                        return ' ' + ctx2.label + ': ' + ctx2.parsed.toLocaleString() + ' (' + ((ctx2.parsed/total)*100).toFixed(1) + '%)';
                                    }
                                }
                            }
                        },
                        animation: { duration: 800 }
                    }
                });
            });
        });

        if (legendEl) legendEl.innerHTML = [
            { label:'Positive', val:pos, color:'#22c55e' },
            { label:'Neutral',  val:net, color:'#94a3b8' },
            { label:'Negative', val:neg, color:'#ef4444' },
        ].map(function(item) {
            return '<div style="display:flex;align-items:center;gap:8px;"><div style="width:9px;height:9px;border-radius:50%;background:' + item.color + ';flex-shrink:0;"></div><span style="font-size:12px;font-weight:700;color:#1e293b;flex:1;">' + item.label + '</span><span style="font-size:12px;font-weight:700;color:#1e293b;">' + numF(item.val) + '</span><span style="font-size:11px;color:#94a3b8;width:40px;text-align:right;font-weight:600;">' + ((item.val/total)*100).toFixed(1) + '%</span></div>';
        }).join('');
    },

    async loadTopLocations(card) {
        var ldEl = _$('loadingTopLocations'), tblEl = _$('topLocationsTable');
        var locs = [];
        try {
            var r = await fetch('/mk/api/x/top-locations?project_id=' + this.projectId + '&start_date=' + this.startDate + '&end_date=' + this.endDate);
            if (r.ok) {
                var result = await r.json();
                if (result.success && Array.isArray(result.data)) {
                    locs = result.data.filter(function(l) {
                        var n = (l.name || l.location || '').trim();
                        return n && n !== 'Unknown' && !n.startsWith('\u0000');
                    }).map(function(l) {
                            return { name: normalizeProvinceName(l.name || l.location || 'Unknown'), count: parseInt(l.count || l.total || 0) };
                    });
                }
            }
        } catch(e) { console.warn('top-loc api missing', e); }

        if (!locs.length) {
            var geo  = await this.fetchGeoUser(), rows = this.parseGeoRows(geo);
            rows.forEach(function(country) {
                var cName = (country.name || '').trim();
                var hasProvinces = country.detail && typeof country.detail === 'object' && Object.keys(country.detail).length > 0;
                if (hasProvinces) {
                    Object.entries(country.detail)
                        .filter(([k]) => k && !k.startsWith('\u0000') && k.trim())
                            .forEach(function([name, val]) {
                                var count = typeof val === 'number' ? val : parseInt(val?.count || 0);
                                if (count > 0) locs.push({ name: normalizeProvinceName(name.trim()), count });
                            });
                } else if (cName && cName !== 'Unknown') {
                        locs.push({ name: normalizeProvinceName(cName), count: parseInt(country.count || 0) });
                }
            });
            locs.sort((a,b) => b.count - a.count);
        }

        if (ldEl) ldEl.style.display = 'none';
        if (!locs.length) {
            if (tblEl) tblEl.innerHTML = '<div style="padding:20px;text-align:center;color:var(--slate-400);font-size:12px;">No data available</div>';
            return;
        }
        this._allLocations = locs;
        if (locs.length > 10) {
            var btnWrap = _$('topLocBtnWrap');
            if (btnWrap && !btnWrap.querySelector('.view-all-btn')) {
                btnWrap.insertAdjacentHTML('afterbegin', '<button class="view-all-btn" onclick="XGeo.openModal()">View All<svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;margin-left:4px;"><path d="M9 18l6-6-6-6"/></svg></button>');
            }
        }
        if (tblEl) tblEl.innerHTML = this._buildTable(locs.slice(0,10), 0);
    },

    _buildTable(items, offset) {
            return '<table class="geo-tbl" style="margin-top:8px;"><thead><tr><th style="width:40px;">#</th><th>Location</th><th style="text-align:right;">Authors</th></tr></thead><tbody>' +
                items.map(function(loc, i) {
                    return '<tr><td class="geo-tbl-rank">' + (offset+i+1) + '</td><td class="geo-tbl-name">' + esc(normalizeProvinceName(loc.name)) + '</td><td class="geo-tbl-num">' + numF(loc.count) + '</td></tr>';
                }).join('') +
                '</tbody></table>';
    },

    openModal() {
        var modal = _$('geoLocModal'), body = _$('geoLocModalBody');
        if (!modal || !body) return;
        body.innerHTML = this._buildTable(this._allLocations, 0);
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        var self = this;
        this._escHandler = function(e) { if (e.key === 'Escape') self.closeModal(); };
        document.addEventListener('keydown', this._escHandler);
    },
    closeModal() {
        var modal = _$('geoLocModal');
        if (modal) modal.classList.remove('open');
        document.body.style.overflow = '';
        if (this._escHandler) document.removeEventListener('keydown', this._escHandler);
    },
};

/* ══════════════════════════════════════════════════════
   GeoPanel
══════════════════════════════════════════════════════ */
const GeoPanel = {
    open(rows, title, dotColor) {
        GeoDetail.close();
        _$('geoPanelDot').style.background    = dotColor || '#038047';
        _$('geoPanelTitle').textContent        = title || 'X Geographic';
        _$('geoPanelMeta').textContent         = XGeo.startDate + ' – ' + XGeo.endDate;
        var ov = _$('geoPanelOverlay'), pn = _$('geoSntPanel');
        ov.classList.remove('hiding'); pn.classList.remove('hiding');
        ov.classList.add('show'); pn.classList.add('show');
        this._render(rows);
    },
    openLocation(row) {
        var pos = parseInt(row.pos||0), neg = parseInt(row.neg||0), net = parseInt(row.net||0);
        var color = '#64748b';
        if (pos > neg && pos > net) color = '#22c55e';
        else if (neg > pos && neg > net) color = '#ef4444';
        this.open([row], 'X — ' + (row.name || 'Unknown'), color);
        GeoDetail.openLocation(row, color);
    },
    close() {
        GeoDetail.close();
        var ov = _$('geoPanelOverlay'), pn = _$('geoSntPanel');
        pn.classList.add('hiding'); ov.classList.add('hiding');
        setTimeout(function() {
            pn.classList.remove('show','hiding');
            ov.classList.remove('show','hiding');
        }, 240);
    },
    _render(rows) {
        var list = _$('geoPanelList');
        if (!list) return;
        if (!rows?.length) {
            list.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:12px;color:#94A3B8;font-size:13px;font-weight:600;">No data</div>';
            return;
        }
        list.innerHTML = rows.slice(0,100).map(function(row) {
            var name = row.name || 'Unknown', count = parseInt(row.count||0);
            var pos = parseInt(row.pos||0), neg = parseInt(row.neg||0), net = parseInt(row.net||0);
            var sent = pos>neg&&pos>net ? 'pos' : neg>pos&&neg>net ? 'neg' : 'neu';
            var color = '#64748b';
            if (sent === 'pos') color = '#22c55e';
            else if (sent === 'neg') color = '#ef4444';
            var sentLbl = { pos:'Pos', neg:'Neg', neu:'Neu' }[sent];
            var enc = encodeURIComponent(JSON.stringify(row));
            return '<div class="do-panel-item" data-item="' + esc(enc) + '" onclick="GeoPanel._click(this)">' +
                '<div class="do-panel-avatar" style="background:linear-gradient(135deg,' + color + ',' + color + '99);"><svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:#fff;fill:none;stroke-width:2;"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/></svg></div>' +
                '<div class="do-panel-item-body">' +
                '<div class="do-panel-author">' + esc(name) + '</div>' +
                '<div class="do-panel-text">' + numF(pos) + ' pos · ' + numF(net) + ' neu · ' + numF(neg) + ' neg</div>' +
                '<div class="do-panel-footer"><span class="do-sent-badge do-sent-badge--' + sent + '">' + sentLbl + '</span><span>' + numF(count) + ' users</span><span style="margin-left:auto;">X (Twitter)</span></div>' +
                '</div></div>';
        }).join('');
    },
    _click(el) {
        try {
            var raw = el.dataset.item.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"');
            var row = JSON.parse(decodeURIComponent(raw));
            var color = '#64748b';
            var pos = parseInt(row.pos||0), neg = parseInt(row.neg||0), net = parseInt(row.net||0);
            if (pos > neg && pos > net) color = '#22c55e';
            else if (neg > pos && neg > net) color = '#ef4444';
            GeoDetail.openLocation(row, color);
        } catch(e) { console.warn(e); }
    }
};

/* ══════════════════════════════════════════════════════
   GeoDetail
══════════════════════════════════════════════════════ */
const GeoDetail = {
    openLocation(row, color) {
        var panel = _$('geoDetailPanel'), body = _$('geoDetailBody'), title = _$('geoDetailTitle');
        if (!panel || !body) return;
        var name = row.name || 'Unknown', count = parseInt(row.count||0);
        var pos = parseInt(row.pos||0), neg = parseInt(row.neg||0), net = parseInt(row.net||0), safe = count || 1;
        var sent = pos>neg&&pos>net ? 'pos' : neg>pos&&neg>net ? 'neg' : 'neu';
        var sentLbl = { pos:'Positive', neg:'Negative', neu:'Neutral' }[sent];
        if (title) title.textContent = name;
        body.innerHTML =
            '<div class="do-dp2-avatar-row"><div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,' + color + ',' + color + '99);"><svg viewBox="0 0 24 24" style="width:24px;height:24px;stroke:#fff;fill:none;stroke-width:2;"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/></svg></div>' +
            '<div><div class="do-dp2-name">' + esc(name) + '</div><div class="do-dp2-handle">Geographic Region</div><span class="do-dp2-plat-badge" style="background:' + color + '18;color:' + color + ';">X (Twitter)</span></div></div>' +
            '<div class="do-dp2-sent do-dp2-sent--' + sent + '">' + sentLbl + ' dominant</div>' +
            '<div class="do-dp2-stats">' +
            '<div class="do-dp2-stat"><div class="do-dp2-stat-val">' + numF(count) + '</div><div class="do-dp2-stat-lbl">Total Users</div></div>' +
            '<div class="do-dp2-stat"><div class="do-dp2-stat-val" style="color:#22c55e;">' + numF(pos) + '</div><div class="do-dp2-stat-lbl">Positive</div></div>' +
            '<div class="do-dp2-stat"><div class="do-dp2-stat-val" style="color:#ef4444;">' + numF(neg) + '</div><div class="do-dp2-stat-lbl">Negative</div></div>' +
            '</div>' +
            '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:12px;">' +
            '<div style="background:#f0fdf4;border-radius:6px;padding:8px;text-align:center;border:1px solid #bbf7d0;"><div style="font-size:13px;font-weight:800;color:#22c55e;">' + ((pos/safe)*100).toFixed(1) + '%</div><div style="font-size:9px;color:#94a3b8;text-transform:uppercase;font-weight:700;margin-top:2px;">Positive</div></div>' +
            '<div style="background:#f8fafc;border-radius:6px;padding:8px;text-align:center;border:1px solid #e2e8f0;"><div style="font-size:13px;font-weight:800;color:#94a3b8;">' + ((net/safe)*100).toFixed(1) + '%</div><div style="font-size:9px;color:#94a3b8;text-transform:uppercase;font-weight:700;margin-top:2px;">Neutral</div></div>' +
            '<div style="background:#fef2f2;border-radius:6px;padding:8px;text-align:center;border:1px solid #fecaca;"><div style="font-size:13px;font-weight:800;color:#ef4444;">' + ((neg/safe)*100).toFixed(1) + '%</div><div style="font-size:9px;color:#94a3b8;text-transform:uppercase;font-weight:700;margin-top:2px;">Negative</div></div>' +
            '</div>' +
            '<div style="font-size:11px;color:#94a3b8;font-weight:600;text-align:center;padding:8px;background:#f8fafc;border-radius:6px;border:1px solid #e2e8f0;"><i class="ph ph-map-pin me-1"></i>' + esc(name) + ' · ' + numF(count) + ' total mentions tracked</div>';
        panel.classList.add('show');
    },
    close() { var p = _$('geoDetailPanel'); if(p) p.classList.remove('show'); },
};

/* ══════════════════════════════════════════════════════
   XGeoExport
══════════════════════════════════════════════════════ */
const XGeoExport = (() => {
    'use strict';

    let _toastTimer = null;
    const PID = '{{ $projectId ?? "0" }}';

    function _toast(msg, type, duration) {
        type = type || 'default'; duration = duration || 3200;
        var t = _$('exportToast'), m = _$('exportToastMsg'), ico = _$('exportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className   = 'export-toast show ' + (type !== 'default' ? type : '');
        var icons = { success:'ph-check-circle', error:'ph-x-circle', default:'ph-spinner' };
        ico.className = 'ph ' + (icons[type] || icons.default);
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(function() { t.classList.remove('show'); }, duration);
    }

    function _btnState(btn, loading) {
        if (!btn) return;
        btn.disabled = loading;
        btn.classList.toggle('exporting', loading);
    }

    /* ── Tunggu ECharts selesai render ── */
    function _waitEChartsFinished(instance, ms) {
        ms = ms || 2500;
        return new Promise(function(resolve) {
            var done = false;
            var finish = function() { if (!done) { done = true; resolve(); } };
            var t = setTimeout(finish, ms);
            try { instance.on('finished', function() { clearTimeout(t); finish(); }); }
            catch(e) { clearTimeout(t); finish(); }
        });
    }

    /* ── Snapshot ECharts donut ── */
    async function _getDonutSnapshot() {
        var inst = window.__geoDonutChart;
        if (!inst || inst.isDisposed?.()) return null;
        try {
            inst.setOption({ animation: false }, { silent: true });
            await _waitEChartsFinished(inst, 1500);
            await new Promise(function(r) { requestAnimationFrame(function() { requestAnimationFrame(r); }); });
            var dataUrl = inst.getDataURL({
                type: 'png',
                pixelRatio: 2,
                backgroundColor: '#ffffff',
                excludeComponents: ['toolbox'],
            });
            return (dataUrl && dataUrl !== 'data:,') ? dataUrl : null;
        } catch(e) {
            console.warn('[XGeoExport] donut snapshot failed:', e);
            return null;
        } finally {
            try { inst.setOption({ animation: true }, { silent: true }); } catch(e) {}
        }
    }

    /* ── Snapshot Leaflet maps sebagai PNG via canvas ── */
    async function _getLeafletSnapshot(mapId) {
        var mapEl = document.getElementById(mapId);
        if (!mapEl) return null;
        try {
            /* Invalidate size dulu */
            var lmap = window._leafletMaps[mapId];
            if (lmap) {
                lmap.invalidateSize({ animate: false });
                await new Promise(function(r) { setTimeout(r, 200); });
            }
            var canvas = await html2canvas(mapEl, {
                scale: 2,
                useCORS: true,
                allowTaint: false,
                backgroundColor: '#e8f0e8',
                logging: false,
                removeContainer: true,
                imageTimeout: 8000,
                ignoreElements: function(e) {
                    return e.classList.contains('map-scroll-overlay') ||
                           e.classList.contains('map-loading-overlay');
                },
            });
            return canvas.toDataURL('image/png');
        } catch(e) {
            console.warn('[XGeoExport] leaflet snapshot failed:', mapId, e);
            return null;
        }
    }

    /* ── onclone: cleanup + inject semua snapshots ── */
    function _makeOnClone(snaps) {
        return function(clonedDoc) {
            /* Freeze semua animasi */
            var s = clonedDoc.createElement('style');
            s.textContent = [
                '*, *::before, *::after {',
                '  animation: none !important;',
                '  transition: none !important;',
                '  animation-play-state: paused !important;',
                '}',
                '[data-html2canvas-ignore] { display: none !important; }',
                '.sk-block, .sk-line { animation: none !important; background: #e2e8f0 !important; }',
                '.kpi-card-hover { transform: none !important; filter: none !important; opacity: 1 !important; }',
                '.kpi-card-hover::before { display: none !important; }',
                '.map-loading-overlay, .chart-loading, .spinner-state { display: none !important; }',
                '.export-toast, .page-export-bar { display: none !important; }',
                '.map-scroll-overlay { display: none !important; }',
                '.location-list { max-height: none !important; overflow: visible !important; }',
            ].join('\n');
            clonedDoc.head.appendChild(s);

            /* Sembunyikan panel dan overlay */
            clonedDoc.querySelectorAll(
                '#geoPanelOverlay,#geoSntPanel,.do-panel-overlay,.do-panel,' +
                '#geoLocModal,.geo-modal-overlay,#geoCustomTT,' +
                '.map-scroll-overlay,.map-loading-overlay,' +
                '.export-toast,[data-html2canvas-ignore],.page-export-bar'
            ).forEach(function(el) {
                el.style.cssText += 'display:none!important;visibility:hidden!important;opacity:0!important;height:0!important;overflow:hidden!important;';
            });

            /* Force visible semua card */
            clonedDoc.querySelectorAll(
                '.card,.card-body,.card-header,.row,[class*="col-"],' +
                '.geo-card,.map-with-panel,.map-area,.location-panel,' +
                '.charts-row,#pageExportArea,#exportPage1,#exportPage2,#exportPage3'
            ).forEach(function(el) {
                el.style.opacity    = '1';
                el.style.transform  = 'none';
                el.style.visibility = 'visible';
                el.style.filter     = 'none';
            });

            /* Sembunyikan loading state */
            ['geoDonutLoading','loadingChartCountries','loadingChartProvinces',
             'loadingChartSentiment','loadingTopLocations'].forEach(function(id) {
                var el = clonedDoc.getElementById(id);
                if (el) el.style.display = 'none';
            });

            /* Force visible charts */
            ['chartCountries','chartProvinces','chartSentimentDonut',
             'chartSentimentLegend','topLocationsTable'].forEach(function(id) {
                var el = clonedDoc.getElementById(id);
                if (el) { el.style.display = 'block'; el.style.opacity = '1'; }
            });

            /* ★ Inject semua snapshots ── kunci Safari ── */
            Object.entries(snaps || {}).forEach(function(entry) {
                var id = entry[0], dataUrl = entry[1];
                if (!dataUrl) return;
                var container = clonedDoc.getElementById(id);
                if (!container) return;
                var orig = document.getElementById(id);
                var h = orig ? Math.max(orig.scrollHeight, orig.offsetHeight, 240) : 300;
                container.innerHTML = '';
                var img = clonedDoc.createElement('img');
                img.src = dataUrl;
                img.style.cssText = 'width:100%;height:' + h + 'px;display:block;object-fit:contain;background:#fff;border-radius:4px;';
                container.appendChild(img);
                container.style.cssText += 'display:block!important;opacity:1!important;visibility:visible!important;overflow:visible!important;';
                /* Fix parent container */
                var parent = container.parentElement;
                if (parent) {
                    parent.style.height    = 'auto';
                    parent.style.minHeight = h + 'px';
                    parent.style.overflow  = 'visible';
                }
            });
        };
    }

    /* ── Core capture: snapshot dulu, baru html2canvas ── */
    async function _doCapture(el, bg, snaps) {
        window.scrollTo(0, 0);
        await new Promise(function(r) { setTimeout(r, 80); });

        /* Force visible */
        el.querySelectorAll('.card,.kpi-card-hover,.geo-card,[class*="col-"]')
          .forEach(function(e) {
              e.style.opacity    = '1';
              e.style.transform  = 'none';
              e.style.visibility = 'visible';
          });

        /* Decode semua snapshot dulu sebelum html2canvas */
        var allUrls = Object.values(snaps || {}).filter(Boolean);
        await Promise.allSettled(allUrls.map(function(src) {
            return new Promise(function(resolve) {
                var img = new Image();
                img.onload = img.onerror = resolve;
                setTimeout(resolve, 5000);
                img.src = src;
            });
        }));

        await new Promise(function(r) { requestAnimationFrame(function() { requestAnimationFrame(r); }); });
        await new Promise(function(r) { setTimeout(r, 200); });

        return html2canvas(el, {
            scale          : 2,
            useCORS        : true,
            allowTaint     : false,
            backgroundColor: bg || '#f1f5f9',
            logging        : false,
            removeContainer: true,
            imageTimeout   : 12000,
            scrollX        : 0,
            scrollY        : 0,
            width          : el.offsetWidth,
            height         : el.scrollHeight,
            onclone        : _makeOnClone(snaps),
            ignoreElements : function(e) {
                return e.hasAttribute('data-html2canvas-ignore') ||
                    e.classList.contains('map-scroll-overlay') ||
                    e.classList.contains('map-loading-overlay') ||
                    ['geoCustomTT','exportToast','geoSntPanel',
                     'geoPanelOverlay','geoLocModal'].includes(e.id) ||
                    /* Skip cross-origin img yang bukan data: / blob: */
                    (e.tagName === 'IMG' && e.src &&
                     !e.src.startsWith('data:') && !e.src.startsWith('blob:'));
            },
        });
    }

    /* ── Kumpulkan semua snapshots sebelum capture ── */
    async function _gatherSnapshots() {
        var snaps = {};

        _toast('Menyiapkan snapshot charts…', 'default', 99999);

        /* ECharts donut */
        var donutSnap = await _getDonutSnapshot();
        if (donutSnap) snaps['geoDonutChart'] = donutSnap;

        /* Leaflet maps */
        var mapIds = ['geoBuzzerMap', 'geoMap', 'geoSentimentMap'];
        for (var i = 0; i < mapIds.length; i++) {
            var id   = mapIds[i];
                var snap = await _getLeafletSnapshot(id);
                // Always use snapshot for Safari to avoid 'gepeng' map in export
                if (snap && (navigator.userAgent.includes('Safari') && !navigator.userAgent.includes('Chrome'))) {
                    snaps[id] = snap;
                } else if (snap && !snaps[id]) {
                    snaps[id] = snap;
                }
        }

        return snaps;
    }

    function _stamp() { return new Date().toISOString().slice(0,10).replace(/-/g,''); }

    function _drawHeader(pdf, pW, pH, label, page, total) {
        pdf.setFillColor(3, 128, 71);
        pdf.rect(0, 0, pW, 11, 'F');
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(9); pdf.setFont('helvetica','bold');
        pdf.text('SMADIMENT — Location Map' + (label ? '  ·  ' + label : ''), 10, 7.5);
        var now = new Date().toLocaleDateString('id-ID', {
            day:'2-digit', month:'short', year:'numeric',
            hour:'2-digit', minute:'2-digit'
        });
        pdf.setFontSize(7); pdf.setFont('helvetica','normal');
        pdf.text('Generated: ' + now, pW - 10, 7.5, { align: 'right' });
        pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
        pdf.text('Halaman ' + page + ' / ' + total, pW / 2, pH - 3, { align: 'center' });
    }

    function _addCanvasAsPage(pdf, canvas, margin, pW, pH, label, page, total) {
        _drawHeader(pdf, pW, pH, label, page, total);
        var usableW = pW - margin * 2;
        var usableH = pH - 14 - 8;
        var ratio   = Math.min(usableW / canvas.width, usableH / canvas.height);
        var dw = canvas.width  * ratio;
        var dh = canvas.height * ratio;
        pdf.addImage(
            canvas.toDataURL('image/png'), 'PNG',
            margin + (usableW - dw) / 2, 14,
            dw, dh
        );
    }

    function _paginate(pdf, canvas, margin, pW, pH, label) {
        var uw = pW - margin * 2, uh = pH - 14 - 8;
        var ratio  = uw / canvas.width, sliceH = uh / ratio;
        var total  = Math.max(1, Math.ceil((canvas.height * ratio) / uh));
        var srcY = 0, pg = 1;
        while (srcY < canvas.height) {
            if (pg > 1) pdf.addPage();
            _drawHeader(pdf, pW, pH, label, pg, total);
            var srcSlice = Math.min(sliceH, canvas.height - srcY);
            var slice    = document.createElement('canvas');
            slice.width  = canvas.width;
            slice.height = Math.ceil(srcSlice);
            slice.getContext('2d').drawImage(
                canvas, 0, srcY, canvas.width, srcSlice,
                0, 0,           canvas.width, srcSlice
            );
            pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, uw, srcSlice * ratio);
            srcY += srcSlice; pg++;
        }
        return total;
    }

    async function _ensureAllLoaded() {
        var pending = Array.from(document.querySelectorAll('[data-lazy]'))
            .filter(function(el) { return !XGeo._loaded.has(el.dataset.lazy); });
        for (var i = 0; i < pending.length; i++) {
            var lazy = pending[i], sec = lazy.dataset.lazy;
            XGeo._loaded.add(sec);
            try { await XGeo._load(sec, lazy); } catch(e) { console.warn('lazy:', sec, e); }
            await new Promise(function(r) { setTimeout(r, 100); });
        }
        /* Invalidate semua Leaflet setelah load */
        Object.values(window._leafletMaps || {}).forEach(function(m) {
            try { m.invalidateSize({ animate: false }); } catch(e) {}
        });
        await new Promise(function(r) { setTimeout(r, 400); });
    }

    const _cardLabels = {
        'donut'          : 'Distribusi Sentiment — Top Locations',
        'map-buzzer'     : 'Buzzer Map',
        'map-user'       : 'Geographic User Distribution',
        'map-sentiment'  : 'Sentiment by Location',
        'chart-countries': 'Top Countries',
        'chart-provinces': 'Top Provinces',
        'chart-sentiment': 'Sentiment Summary',
        'top-locations'  : 'Top Author Locations',
    };

    /* ── Per-card export ── */
    async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia','error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia','error');       return; }

        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);

        try {
            var area = document.getElementById(areaId);
            if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');

            /* Snapshot hanya chart yang relevan di dalam card ini */
            var snaps = {};

            /* Donut */
            if (area.contains(document.getElementById('geoDonutChart'))) {
                var ds = await _getDonutSnapshot();
                if (ds) snaps['geoDonutChart'] = ds;
            }

            /* Leaflet maps yang ada di dalam card */
            var mapIds = ['geoBuzzerMap', 'geoMap', 'geoSentimentMap'];
            for (var i = 0; i < mapIds.length; i++) {
                var mapEl = document.getElementById(mapIds[i]);
                if (mapEl && area.contains(mapEl)) {
                    var ms = await _getLeafletSnapshot(mapIds[i]);
                    if (ms) snaps[mapIds[i]] = ms;
                }
            }

            var canvas = await _doCapture(area, '#ffffff', snaps);
            var fname  = 'location_map_' + cardKey + '_' + PID + '_' + _stamp();
            var label  = _cardLabels[cardKey] || cardKey;

            if (type === 'image') {
                var a = document.createElement('a');
                a.download = fname + '.png';
                a.href     = canvas.toDataURL('image/png');
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
            } else {
                var { jsPDF } = window.jspdf;
                var landscape = canvas.width > canvas.height * 1.2;
                var pdf = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit:'mm', format:'a4' });
                var pW  = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight();
                var M   = 10, uw = pW - M * 2, uh = pH - 14 - 8;
                var fitsOne = (canvas.height * (uw / canvas.width)) <= uh;
                if (fitsOne) { _addCanvasAsPage(pdf, canvas, M, pW, pH, label, 1, 1); }
                else         { _paginate(pdf, canvas, M, pW, pH, label); }
                pdf.save(fname + '.pdf');
                _toast('PDF berhasil diunduh!', 'success');
            }
        } catch(err) {
            console.error('[XGeoExport.runCard]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally { _btnState(btn, false); }
    }

    /* ── Export seluruh halaman (3-page PDF) ── */
    async function run(type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia','error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia','error');       return; }

        var btnPdf = _$('pageExportPdfBtn'), btnImg = _$('pageExportImgBtn');
        _btnState(btnPdf, true); _btnState(btnImg, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF 3 halaman…' : 'Mengambil gambar…', 'default', 99999);

        try {
            window.scrollTo({ top: 0 });

            /* Pastikan semua section sudah di-load */
            await _ensureAllLoaded();

            /* Kumpulkan semua snapshots */
            var snaps = await _gatherSnapshots();
            var stamp = _stamp();

            if (type === 'image') {
                var canvas = await _doCapture(_$('pageExportArea'), '#f1f5f9', snaps);
                var a = document.createElement('a');
                a.download = 'location_map_' + PID + '_' + stamp + '.png';
                a.href     = canvas.toDataURL('image/png');
                a.click();
                _toast('Gambar berhasil diunduh!', 'success');
                return;
            }

            /* PDF: 3 halaman */
            var pg1El = _$('exportPage1'), pg2El = _$('exportPage2'), pg3El = _$('exportPage3');
            if (!pg1El || !pg2El || !pg3El) throw new Error('Wrapper exportPage tidak ditemukan.');

            _toast('Menangkap Halaman 1…', 'default', 99999);
            var canvas1 = await _doCapture(pg1El, '#f1f5f9', snaps);

            _toast('Menangkap Halaman 2…', 'default', 99999);
            var canvas2 = await _doCapture(pg2El, '#f1f5f9', snaps);

            _toast('Menangkap Halaman 3…', 'default', 99999);
            var canvas3 = await _doCapture(pg3El, '#f1f5f9', snaps);

            var { jsPDF } = window.jspdf;
            var pdf = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
            var pW  = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight();
            _addCanvasAsPage(pdf, canvas1, 10, pW, pH, 'KPI, Donut & Buzzer Map', 1, 3);
            pdf.addPage();
            _addCanvasAsPage(pdf, canvas2, 10, pW, pH, 'Users & Sentiment Maps', 2, 3);
            pdf.addPage();
            _addCanvasAsPage(pdf, canvas3, 10, pW, pH, 'Charts & Top Locations', 3, 3);
            pdf.save('location_map_' + PID + '_' + stamp + '.pdf');
            _toast('PDF berhasil diunduh!', 'success');

        } catch(err) {
            console.error('[XGeoExport.run]', err);
            _toast('Export gagal: ' + err.message, 'error');
        } finally {
            _btnState(btnPdf, false); _btnState(btnImg, false);
        }
    }

    return { run, runCard };
})();
/* ══ INIT ══ */
document.addEventListener('DOMContentLoaded', function() {
    XGeo.init();
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') GeoPanel.close(); });
});
</script>
@endsection