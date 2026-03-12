@extends('mk.layouts.app')

@section('title', 'Net Sentiment Score - SMADIMENT')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════
   DESIGN TOKENS — sama persis dengan Data Overview
══════════════════════════════════════════════════════ */
:root {
    --do-primary        : var(--bs-primary, #4361EE);
    --do-primary-rgb    : var(--bs-primary-rgb, 67,97,238);
    --do-primary-lt     : rgba(var(--do-primary-rgb,67,97,238),.10);
    --do-green          : #10B981;
    --do-green-lt       : #ECFDF5;
    --do-red            : #EF4444;
    --do-red-lt         : #FEF2F2;
    --do-slate-50       : #F8FAFC;
    --do-slate-100      : #F1F5F9;
    --do-slate-200      : #E2E8F0;
    --do-slate-300      : #CBD5E1;
    --do-slate-400      : #94A3B8;
    --do-slate-500      : #64748B;
    --do-slate-700      : #334155;
    --do-slate-800      : #1E293B;
    --do-slate-900      : #0F172A;
    --do-radius         : 8px;
    --do-radius-sm      : 5px;
    --do-shadow-sm      : 0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);
    --do-shadow-md      : 0 4px 14px rgba(15,23,42,.08);
    --do-shadow-lg      : 0 10px 30px rgba(15,23,42,.12);
    --c-pos : #0ea5e9;
    --c-neg : #EF4444;
    --c-neu : #94A3B8;
}

*, *::before, *::after { box-sizing: border-box; }

@keyframes fadeUp    { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer   { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin      { to{transform:rotate(360deg)} }
@keyframes dropIn    { from{opacity:0;transform:translateY(-6px) scale(.97)} to{opacity:1;transform:translateY(0) scale(1)} }
@keyframes overlayIn  { from{opacity:0} to{opacity:1} }
@keyframes overlayOut { from{opacity:1} to{opacity:0} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0} to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1}    to{transform:translateX(100%);opacity:0} }

.fade-up    { animation:fadeUp .36s ease-out both; }
.fade-up-d1 { animation-delay:.04s; }
.fade-up-d2 { animation-delay:.08s; }
.fade-up-d3 { animation-delay:.12s; }

/* ══ Page Header ══ */
.do-page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:20px; }

/* ══ Media Dropdown ══ */
.nss-media-wrap { position:relative; }
.nss-media-btn {
    display:inline-flex; align-items:center; gap:6px; padding:7px 14px;
    background:var(--do-slate-800); color:#fff; border:none; border-radius:var(--do-radius-sm);
    font-size:12px; font-weight:700; cursor:pointer; transition:filter .14s;
    box-shadow:var(--do-shadow-sm); font-family:inherit;
}
.nss-media-btn:hover { filter:brightness(1.15); }
.nss-media-btn i { font-size:14px; }
.nss-media-btn .caret { width:14px; height:14px; display:inline-flex; align-items:center; justify-content:center; transition:transform .2s; }
.nss-media-btn.open .caret { transform:rotate(180deg); }
.nss-media-menu {
    display:none; position:absolute; top:calc(100% + 6px); right:0;
    background:#fff; border:1px solid var(--do-slate-200); border-radius:var(--do-radius);
    box-shadow:var(--do-shadow-lg); min-width:200px; z-index:5000; padding:5px;
    animation:dropIn .16s ease-out;
}
.nss-media-menu.show { display:block; }
.nss-media-menu-section { padding:5px 9px 4px; font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.5px; }
.nss-media-menu-item {
    display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:var(--do-radius-sm);
    background:transparent; border:none; width:100%; text-align:left; font-size:12px; font-weight:600;
    color:var(--do-slate-700); cursor:pointer; transition:background .12s; font-family:inherit;
}
.nss-media-menu-item:hover  { background:var(--do-primary-lt); color:var(--do-primary); }
.nss-media-menu-item.active { background:var(--do-primary-lt); color:var(--do-primary); font-weight:700; }
.nss-menu-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

/* ══ Base Card ══ */
.do-card { background:#fff; border:1px solid var(--do-slate-200); border-radius:var(--do-radius); overflow:hidden; box-shadow:var(--do-shadow-sm); transition:border-color .2s,box-shadow .2s; }
.do-card:hover { box-shadow:var(--do-shadow-md); border-color:var(--do-slate-300); }
.do-card-head { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--do-slate-200); background:#fff; }
.do-card-head-left { display:flex; align-items:center; gap:8px; }
.do-head-icon { width:30px; height:30px; border-radius:var(--do-radius-sm); background:var(--do-primary-lt); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:15px; color:var(--do-primary); }
.do-card-title    { font-size:13px; font-weight:700; color:var(--do-slate-900); }
.do-card-subtitle { font-size:10px; color:var(--do-slate-400); font-weight:500; margin-top:1px; }
.do-badge { display:inline-flex; align-items:center; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; background:var(--do-slate-100); color:var(--do-slate-500); white-space:nowrap; }
.do-badge--pos { background:#dbeafe; color:#1e40af; }
.do-badge--neg { background:#fee2e2; color:#991b1b; }
.do-card-body { padding:16px; flex:1; }

/* ══ Stat Cards ══ */
.nss-stat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:14px; }
.nss-stat-card {
    background:#fff; border:1px solid var(--do-slate-200); border-radius:var(--do-radius); padding:14px 16px;
    box-shadow:var(--do-shadow-sm); cursor:pointer; transition:border-color .2s,box-shadow .2s,transform .15s; position:relative; overflow:hidden;
}
.nss-stat-card::after { content:''; position:absolute; top:0; left:0; right:0; height:2.5px; background:var(--accent,var(--do-primary)); opacity:0; transition:opacity .2s; }
.nss-stat-card:hover { box-shadow:var(--do-shadow-md); border-color:var(--do-slate-300); transform:translateY(-1px); }
.nss-stat-card:hover::after { opacity:1; }
.nss-stat-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
.nss-stat-label { font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.5px; display:flex; align-items:center; gap:5px; }
.nss-stat-dot   { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.nss-stat-icon  { font-size:13px; color:var(--do-slate-300); }
.nss-stat-value { font-size:26px; font-weight:800; color:var(--do-slate-900); letter-spacing:-1px; line-height:1; min-height:32px; display:flex; align-items:center; }
.nss-stat-footer { display:flex; align-items:center; justify-content:space-between; margin-top:6px; }
.nss-stat-sub { font-size:10px; color:var(--do-slate-400); font-weight:500; }
.nss-stat-pct { font-size:11px; font-weight:700; }

/* ══ Main Layout ══ */
.nss-main-grid { display:grid; grid-template-columns:1fr 340px; gap:14px; align-items:start; }
.nss-sidebar { display:flex; flex-direction:column; gap:14px; }

/* ══ Gauge ══ */
.nss-gauge-wrap { padding:24px 28px 0; display:flex; flex-direction:column; align-items:center; }
.nss-gauge-outer { position:relative; width:100%; max-width:440px; aspect-ratio:500/310; }
#nssGaugeSVG { width:100%; height:100%; display:block; overflow:visible; }
.nss-score-overlay { position:absolute; left:50%; top:53%; transform:translate(-50%,-50%); display:flex; flex-direction:column; align-items:center; gap:4px; pointer-events:none; z-index:2; white-space:nowrap; }
.nss-score-num { font-size:clamp(26px,5vw,46px); font-weight:800; letter-spacing:-2px; line-height:1; color:var(--do-slate-900); }
.nss-score-lbl { font-size:clamp(8px,1.2vw,10px); font-weight:700; letter-spacing:2px; color:var(--do-slate-400); text-transform:uppercase; }
.nss-gauge-legend { display:flex; align-items:center; justify-content:center; gap:16px; flex-wrap:wrap; padding:12px 16px 14px; border-top:1px solid var(--do-slate-100); background:var(--do-slate-50); margin-top:auto; }
.nss-legend-item { display:flex; align-items:center; gap:5px; font-size:11px; font-weight:600; color:var(--do-slate-500); }
.nss-legend-dot  { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

/* ══ Distribution ══ */
.nss-dist-wrap { height:180px; }
#chNssDist { width:100%; height:100%; }

/* ══ Score Breakdown ══ */
.nss-breakdown-body { padding:0; }
.nss-brk-row { display:flex; align-items:center; justify-content:space-between; padding:9px 16px; border-bottom:1px solid var(--do-slate-50); }
.nss-brk-key { display:flex; align-items:center; gap:7px; font-size:12px; font-weight:600; color:var(--do-slate-700); }
.nss-brk-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.nss-brk-val { font-size:12px; font-weight:700; color:var(--do-slate-900); }
.nss-brk-divider { height:1px; background:var(--do-slate-200); margin:0 16px; }
.nss-brk-total-row { display:flex; align-items:center; justify-content:space-between; padding:8px 16px; background:var(--do-slate-50); }
.nss-brk-total-key { font-size:11px; font-weight:700; color:var(--do-slate-500); text-transform:uppercase; letter-spacing:.4px; }
.nss-brk-total-val { font-size:13px; font-weight:800; color:var(--do-slate-900); }
.nss-brk-nss-row { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:var(--do-primary-lt); border-top:1px solid rgba(var(--do-primary-rgb),.15); }
.nss-brk-nss-key { font-size:10px; font-weight:700; color:var(--do-primary); text-transform:uppercase; letter-spacing:.5px; }
.nss-brk-nss-val { font-size:22px; font-weight:800; letter-spacing:-1px; color:var(--do-primary); }
.nss-formula-eq { background:var(--do-slate-50); border-top:1px solid var(--do-slate-200); padding:8px 16px; font-size:10px; font-weight:600; color:var(--do-slate-400); text-align:center; letter-spacing:.2px; font-family:'Courier New',monospace; }

/* ══ Skeleton ══ */
.sk-block { border-radius:4px; background:linear-gradient(90deg,var(--do-slate-100) 25%,var(--do-slate-200) 50%,var(--do-slate-100) 75%); background-size:200% 100%; animation:shimmer 1.4s infinite; }

/* ══ Slide Panel — IDENTICAL to Data Overview ══ */
.do-panel-overlay { position:fixed; inset:0; z-index:9000; background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none; }
.do-panel-overlay.show   { display:block; animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }

.do-panel { position:fixed; top:0; right:0; bottom:0; z-index:9001; width:480px; max-width:100vw; background:#fff; display:none; flex-direction:column; border-left:1px solid var(--do-slate-200); box-shadow:-8px 0 40px rgba(15,23,42,.16); }
.do-panel.show   { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }

.do-panel-header { display:flex; align-items:center; gap:10px; padding:14px 16px; border-bottom:1px solid var(--do-slate-200); background:var(--do-slate-50); flex-shrink:0; }
.do-panel-dot   { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.do-panel-title { font-size:13px; font-weight:700; color:var(--do-slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-panel-count { background:var(--do-primary); color:#fff; border-radius:3px; padding:1px 8px; font-size:10px; font-weight:800; flex-shrink:0; }
.do-panel-close { width:28px; height:28px; border-radius:var(--do-radius-sm); border:1px solid var(--do-slate-200); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--do-slate-500); font-size:16px; transition:all .14s; flex-shrink:0; }
.do-panel-close:hover { background:var(--do-red); border-color:var(--do-red); color:#fff; }

.do-panel-actions { display:flex; align-items:center; gap:7px; padding:7px 12px; border-bottom:1px solid var(--do-slate-200); background:#fff; flex-shrink:0; }
.do-panel-meta { flex:1; font-size:10px; font-weight:700; color:var(--do-slate-400); text-transform:uppercase; letter-spacing:.5px; overflow:hidden; display:flex; align-items:center; gap:5px; }
.do-panel-tabs { display:flex; background:var(--do-slate-100); border:1px solid var(--do-slate-200); border-radius:var(--do-radius-sm); padding:2px; gap:2px; }
.do-panel-tab { padding:3px 9px; border-radius:3px; border:none; background:transparent; font-size:11px; font-weight:700; cursor:pointer; transition:all .13s; color:var(--do-slate-500); font-family:inherit; }
.do-panel-tab:hover { background:#fff; }
.do-panel-tab.active                { background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08); }
.do-panel-tab.active[data-s="all"] { color:var(--do-primary); }
.do-panel-tab.neg.active { color:var(--do-red); }
.do-panel-tab.pos.active { color:#0ea5e9; }
.do-panel-tab.neu.active { color:var(--do-slate-500); }
.do-panel-export { display:flex; align-items:center; gap:4px; padding:4px 10px; background:var(--do-primary); color:#fff; border:none; border-radius:var(--do-radius-sm); font-size:10px; font-weight:700; cursor:pointer; transition:filter .13s; font-family:inherit; }
.do-panel-export:hover { filter:brightness(1.1); }
.do-panel-export i { font-size:12px; }

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
.do-panel-text   { font-size:11px; color:var(--do-slate-600); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
.do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--do-slate-400); flex-wrap:wrap; }

.do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.do-sent-badge--neu { background:var(--do-slate-100); color:var(--do-slate-500); }

.do-panel-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:12px; color:var(--do-slate-400); font-size:13px; font-weight:600; }
.do-panel-spinner { width:28px; height:28px; border:2.5px solid var(--do-slate-100); border-top-color:var(--do-primary); border-radius:50%; animation:spin .65s linear infinite; }

/* ══ Responsive ══ */
@media(max-width:1100px) { .nss-main-grid { grid-template-columns:1fr; } }
@media(max-width:768px)  { .nss-stat-grid { grid-template-columns:1fr; } .do-page-header { flex-direction:column; align-items:flex-start; } .do-panel { width:100vw; } }
</style>
@endsection

@section('page-title', 'Net Sentiment Score')

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
@endphp

{{-- ══ Sub-header ══ --}}
<div class="do-page-header fade-up">
   
    <div class="nss-media-wrap" id="nssMediaWrap">
         
        <div class="nss-media-menu" id="nssMediaMenu">
            <div class="nss-media-menu-section">Filter Media</div>
            <button class="nss-media-menu-item active" data-m="all"       onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:var(--do-primary)"></span>All Media</button>
            <button class="nss-media-menu-item"         data-m="doc"       onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#0284c7"></span>Mass Media (News)</button>
            <button class="nss-media-menu-item"         data-m="twitter"   onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#1d9bf0"></span>X / Twitter</button>
            <button class="nss-media-menu-item"         data-m="facebook"  onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#1877f2"></span>Facebook</button>
            <button class="nss-media-menu-item"         data-m="instagram" onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#e1306c"></span>Instagram</button>
            <button class="nss-media-menu-item"         data-m="youtube"   onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#ff0000"></span>YouTube</button>
            <button class="nss-media-menu-item"         data-m="tiktok"    onclick="NSSPage.selectMedia(this)"><span class="nss-menu-dot" style="background:#111827"></span>TikTok</button>
        </div>
    </div>
</div>

{{-- ══ STAT CARDS ══ --}}
<div class="nss-stat-grid">
    <div class="nss-stat-card fade-up fade-up-d1" style="--accent:var(--c-pos);" onclick="NSSPanel.open('pos')">
        <div class="nss-stat-top">
            <span class="nss-stat-label"><span class="nss-stat-dot" style="background:var(--c-pos);"></span>Positive</span>
            <i class="ph ph-thumbs-up nss-stat-icon" style="color:var(--c-pos);"></i>
        </div>
        <div class="nss-stat-value" id="statPos"><div class="sk-block" style="height:28px;width:100px;border-radius:4px;"></div></div>
        <div class="nss-stat-footer">
            <span class="nss-stat-sub">Total mention positif</span>
            <span class="nss-stat-pct" style="color:var(--c-pos);" id="pctPos">—</span>
        </div>
    </div>
    <div class="nss-stat-card fade-up fade-up-d2" style="--accent:var(--c-neu);" onclick="NSSPanel.open('neu')">
        <div class="nss-stat-top">
            <span class="nss-stat-label"><span class="nss-stat-dot" style="background:var(--c-neu);"></span>Neutral</span>
            <i class="ph ph-minus-circle nss-stat-icon"></i>
        </div>
        <div class="nss-stat-value" id="statNeu"><div class="sk-block" style="height:28px;width:100px;border-radius:4px;"></div></div>
        <div class="nss-stat-footer">
            <span class="nss-stat-sub">Total mention netral</span>
            <span class="nss-stat-pct" style="color:var(--c-neu);" id="pctNeu">—</span>
        </div>
    </div>
    <div class="nss-stat-card fade-up fade-up-d3" style="--accent:var(--c-neg);" onclick="NSSPanel.open('neg')">
        <div class="nss-stat-top">
            <span class="nss-stat-label"><span class="nss-stat-dot" style="background:var(--c-neg);"></span>Negative</span>
            <i class="ph ph-thumbs-down nss-stat-icon" style="color:var(--c-neg);"></i>
        </div>
        <div class="nss-stat-value" id="statNeg"><div class="sk-block" style="height:28px;width:100px;border-radius:4px;"></div></div>
        <div class="nss-stat-footer">
            <span class="nss-stat-sub">Total mention negatif</span>
            <span class="nss-stat-pct" style="color:var(--c-neg);" id="pctNeg">—</span>
        </div>
    </div>
</div>

{{-- ══ MAIN GRID ══ --}}
<div class="nss-main-grid">
    {{-- Gauge --}}
    <div class="do-card fade-up">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <span class="do-head-icon"><i class="ph ph-gauge"></i></span>
                <div>
                    <div class="do-card-title">Net Sentiment Score</div>
                    <div class="do-card-subtitle">Klik pada stat card untuk lihat mentions per sentimen</div>
                </div>
            </div>
            <span class="do-badge" id="nssBadgeMain">Loading…</span>
        </div>
        <div class="nss-gauge-wrap">
            <div class="nss-gauge-outer">
                <svg id="nssGaugeSVG" viewBox="0 0 500 310" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <filter id="ndlShadow" x="-60%" y="-60%" width="220%" height="220%">
                            <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="rgba(0,0,0,.18)"/>
                        </filter>
                    </defs>
                    <path d="M 60,260 A 190,190 0 0,1 440,260" fill="none" stroke="#e2e8f0" stroke-width="36" stroke-linecap="butt"/>
                    <path d="M 60,260 A 190,190 0 0,1 250,70"  fill="none" stroke="#EF4444" stroke-width="36" stroke-linecap="butt"/>
                    <path d="M 250,70 A 190,190 0 0,1 440,260" fill="none" stroke="#0ea5e9" stroke-width="36" stroke-linecap="butt"/>
                    <line x1="60"    y1="254"   x2="60"    y2="266"   stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="111.4" y1="121.4" x2="119.9" y2="129.9" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="244"   y1="70"    x2="256"   y2="70"    stroke="#fff" stroke-width="3"   stroke-linecap="round"/>
                    <line x1="380.1" y1="129.9" x2="388.6" y2="121.4" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="440"   y1="254"   x2="440"   y2="266"   stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                    <text x="32"  y="265" text-anchor="middle" font-family="inherit" font-size="11" font-weight="600" fill="#94A3B8">−100%</text>
                    <text x="88"  y="100" text-anchor="middle" font-family="inherit" font-size="11" font-weight="600" fill="#94A3B8">−50%</text>
                    <text x="250" y="36"  text-anchor="middle" font-family="inherit" font-size="12" font-weight="700" fill="#334155">0%</text>
                    <text x="412" y="100" text-anchor="middle" font-family="inherit" font-size="11" font-weight="600" fill="#94A3B8">50%</text>
                    <text x="468" y="265" text-anchor="middle" font-family="inherit" font-size="11" font-weight="600" fill="#94A3B8">100%</text>
                    <g id="nssNeedle" transform="rotate(0, 250, 260)" filter="url(#ndlShadow)">
                        <polygon points="250,260 247.5,255 250,96 252.5,255" fill="#1e293b"/>
                        <circle cx="250" cy="260" r="12" fill="#1e293b"/>
                        <circle cx="250" cy="260" r="4.5" fill="#ffffff"/>
                    </g>
                </svg>
                <div class="nss-score-overlay">
                    <div id="nssScoreNum" class="nss-score-num">—</div>
                    <div id="nssScoreLbl" class="nss-score-lbl">NET SENTIMENT</div>
                </div>
            </div>
        </div>
        <div class="nss-gauge-legend">
            <div class="nss-legend-item"><span class="nss-legend-dot" style="background:var(--c-pos);"></span><span id="legPos">Positive</span></div>
            <div class="nss-legend-item"><span class="nss-legend-dot" style="background:var(--c-neu);"></span><span id="legNeu">Neutral</span></div>
            <div class="nss-legend-item"><span class="nss-legend-dot" style="background:var(--c-neg);"></span><span id="legNeg">Negative</span></div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="nss-sidebar">
        {{-- Distribution --}}
        <div class="do-card fade-up fade-up-d1">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <span class="do-head-icon"><i class="ph ph-chart-bar-horizontal"></i></span>
                    <div>
                        <div class="do-card-title">Distribution</div>
                        <div class="do-card-subtitle">Persentase per sentimen</div>
                    </div>
                </div>
                <span class="do-badge">3 Kategori</span>
            </div>
            <div class="do-card-body">
                <div class="nss-dist-wrap"><div id="chNssDist"></div></div>
            </div>
        </div>

        {{-- Score Breakdown --}}
        <div class="do-card fade-up fade-up-d2">
            <div class="do-card-head">
                <div class="do-card-head-left">
                    <span class="do-head-icon"><i class="ph ph-list-numbers"></i></span>
                    <div>
                        <div class="do-card-title">Score Breakdown</div>
                        <div class="do-card-subtitle">Kalkulasi NSS real-time</div>
                    </div>
                </div>
            </div>
            <div class="nss-breakdown-body">
                <div class="nss-brk-row"><span class="nss-brk-key"><span class="nss-brk-dot" style="background:var(--c-pos);"></span>Positive</span><span class="nss-brk-val" id="brkPos">—</span></div>
                <div class="nss-brk-row"><span class="nss-brk-key"><span class="nss-brk-dot" style="background:var(--c-neu);"></span>Neutral</span><span class="nss-brk-val" id="brkNeu">—</span></div>
                <div class="nss-brk-row"><span class="nss-brk-key"><span class="nss-brk-dot" style="background:var(--c-neg);"></span>Negative</span><span class="nss-brk-val" id="brkNeg">—</span></div>
                <div class="nss-brk-divider"></div>
                <div class="nss-brk-total-row"><span class="nss-brk-total-key">Total Mention</span><span class="nss-brk-total-val" id="brkTot">—</span></div>
                <div class="nss-brk-nss-row"><span class="nss-brk-nss-key">NSS Score</span><span class="nss-brk-nss-val" id="brkNSS">—</span></div>
            </div>
            <div class="nss-formula-eq">NSS = (Pos − Neg) / (Pos + Neg) × 100</div>
        </div>
    </div>
</div>

<input type="hidden" id="nssPID" value="{{ $projectId }}">
<input type="hidden" id="nssSD"  value="{{ $startDate }}">
<input type="hidden" id="nssED"  value="{{ $endDate }}">

{{-- ══ SLIDE PANEL — identical to Data Overview ══ --}}
<div class="do-panel-overlay" id="nssPanelOverlay" onclick="NSSPanel.closeByOverlay()"></div>
<div class="do-panel" id="nssSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot"   id="nssPanelDot"></div>
        <span class="do-panel-title" id="nssPanelTitle">Mentions</span>
        <span class="do-panel-count" id="nssPanelCount">…</span>
        <button class="do-panel-close" onclick="NSSPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta">
            <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
            <span id="nssPanelMeta">—</span>
        </div>
        <div class="do-panel-tabs">
            <button class="do-panel-tab active" data-s="all" onclick="NSSPanel.filterSent('all')">Semua</button>
            <button class="do-panel-tab neg"    data-s="neg" onclick="NSSPanel.filterSent('neg')">Neg</button>
            <button class="do-panel-tab pos"    data-s="pos" onclick="NSSPanel.filterSent('pos')">Pos</button>
            <button class="do-panel-tab neu"    data-s="neu" onclick="NSSPanel.filterSent('neu')">Neu</button>
        </div>
        <button class="do-panel-export" onclick="NSSPanel.exportCsv()">
            <i class="ph ph-download-simple"></i> CSV
        </button>
    </div>
    <div class="do-panel-list" id="nssPanelList"></div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

const NSS_PID = {{ $projectId ? (int)$projectId : 'null' }};
const NSS_SD  = '{{ $startDate }}';
const NSS_ED  = '{{ $endDate }}';

const $      = id => document.getElementById(id);
const numFmt = n  => (parseInt(n)||0).toLocaleString('id-ID');
const pct    = (v,t) => t>0?((v/t)*100).toFixed(1)+'%':'0%';
const esc    = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
function getPrimary() { return getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim()||'#4361EE'; }

/* ── Count-up ── */
function countUp(el, target, dur=900) {
    if(!el) return; el.innerHTML='';
    const s=performance.now(), ease=t=>1-Math.pow(1-t,3);
    (function tick(n){const p=Math.min((n-s)/dur,1);el.textContent=numFmt(Math.round(target*ease(p)));if(p<1)requestAnimationFrame(tick);})(performance.now());
}

/* ── ECharts ── */
const NSS_Charts={_i:{},make(id){if(this._i[id]){try{this._i[id].dispose();}catch(e){}}const d=$(id);if(!d)return null;const c=echarts.init(d,null,{renderer:'canvas'});this._i[id]=c;return c;}};
window.addEventListener('resize',()=>Object.values(NSS_Charts._i).forEach(c=>{try{if(!c.isDisposed())c.resize();}catch(e){}}));
const EC_TT={backgroundColor:'#1e293b',borderColor:'#334155',borderWidth:1,padding:[9,13],textStyle:{color:'#fff',fontFamily:'inherit',fontSize:12},extraCssText:'border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.3);'};

/* ── Gauge ── */
let _raf=null;
function renderGauge(nss){
    const val=Math.max(-100,Math.min(100,nss)),targetRot=(val/100)*90;
    const needle=$('nssNeedle'),scoreEl=$('nssScoreNum'),labelEl=$('nssScoreLbl');
    const isPos=val>5,isNeg=val<-5;
    const color=isPos?'#0ea5e9':isNeg?'#EF4444':'#94A3B8';
    const lbl=isPos?'POSITIF':isNeg?'NEGATIF':'NETRAL';
    const finalStr=(val>0?'+':'')+val.toFixed(0)+'%';
    const tf=needle.getAttribute('transform')||'rotate(0,250,260)';
    const cur=parseFloat((tf.match(/rotate\(([-\d.]+)/)||[0,0])[1]);
    if(_raf)cancelAnimationFrame(_raf);
    const dur=1200,t0=performance.now(),ease=t=>t<.5?2*t*t:-1+(4-2*t)*t;
    (function frame(now){
        const p=Math.min((now-t0)/dur,1),rot=cur+(targetRot-cur)*ease(p);
        needle.setAttribute('transform',`rotate(${rot.toFixed(3)},250,260)`);
        const lv=(rot/90)*100;
        if(scoreEl){scoreEl.textContent=(lv>0?'+':'')+lv.toFixed(0)+'%';scoreEl.style.color=color;}
        if(p<1){_raf=requestAnimationFrame(frame);}else{
            if(scoreEl){scoreEl.textContent=finalStr;scoreEl.style.color=color;}
            if(labelEl){labelEl.textContent=lbl;labelEl.style.color=isPos?'#0ea5e9':isNeg?'#dc2626':'#94A3B8';}
            _raf=null;
        }
    })(performance.now());
}

/* ── Distribution chart ── */
function renderDist(pos,neu,neg){
    const dom=$('chNssDist');if(!dom)return;
    const chart=NSS_Charts.make('chNssDist');
    const tot=pos+neu+neg||1;
    const pPct=+(pos/tot*100).toFixed(1),nuPct=+(neu/tot*100).toFixed(1),nePct=+(neg/tot*100).toFixed(1);
    chart.setOption({
        animation:true,animationDuration:900,animationEasing:'cubicInOut',backgroundColor:'transparent',
        tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'none'},formatter:params=>{const p=params.find(x=>x.value>0);return p?`<div style="font-weight:700;margin-bottom:4px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:${p.color};margin-right:6px;"></span>${p.seriesName}</div><div style="display:flex;justify-content:space-between;gap:16px;"><span style="opacity:.7;">Share</span><span style="font-weight:700;">${p.value}%</span></div>`:'';}},
        grid:{left:0,right:22,top:4,bottom:0,containLabel:true},
        xAxis:{type:'value',max:100,axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:'inherit',fontSize:10,color:'#94A3B8',formatter:v=>v+'%'}},
        yAxis:{type:'category',data:['Negative','Neutral','Positive'],axisTick:{show:false},axisLine:{show:false},axisLabel:{fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#475569',margin:10}},
        series:[
            {name:'Positive',type:'bar',data:[null,null,pPct],barMaxWidth:28,barBorderRadius:[0,4,4,0],itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:'rgba(14,165,233,.1)'},{offset:1,color:'#0ea5e9'}]}},label:{show:true,position:'right',fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#0ea5e9',formatter:v=>v.value+'%'}},
            {name:'Neutral', type:'bar',data:[null,nuPct,null],barMaxWidth:28,barBorderRadius:[0,4,4,0],itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:'rgba(148,163,184,.1)'},{offset:1,color:'#94A3B8'}]}},label:{show:true,position:'right',fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#94A3B8',formatter:v=>v.value+'%'}},
            {name:'Negative',type:'bar',data:[nePct,null,null],barMaxWidth:28,barBorderRadius:[0,4,4,0],itemStyle:{color:{type:'linear',x:0,y:0,x2:1,y2:0,colorStops:[{offset:0,color:'rgba(239,68,68,.1)'},{offset:1,color:'#EF4444'}]}},label:{show:true,position:'right',fontFamily:'inherit',fontSize:11,fontWeight:'700',color:'#EF4444',formatter:v=>v.value+'%'}},
        ],
    });
    chart.on('click',params=>{if(params.componentType==='series'){const m={Positive:'pos',Neutral:'neu',Negative:'neg'};NSSPanel.open(m[params.seriesName]||'pos');}});
    chart.on('mouseover',p=>{if(p.componentType==='series')chart.getDom().style.cursor='pointer';});
    chart.on('mouseout',()=>{chart.getDom().style.cursor='default';});
}

/* ── Breakdown ── */
function updateBreakdown(pos,neu,neg,nss){
    const tot=pos+neu+neg;
    $('brkPos').textContent=numFmt(pos); $('brkNeu').textContent=numFmt(neu);
    $('brkNeg').textContent=numFmt(neg); $('brkTot').textContent=numFmt(tot);
    const isPos=nss>5,isNeg=nss<-5;
    const color=isPos?'#0ea5e9':isNeg?'#EF4444':'#94A3B8';
    const nssStr=(nss>=0?'+':'')+nss.toFixed(1)+'%';
    const nssEl=$('brkNSS');
    nssEl.textContent=nssStr; nssEl.style.color=color;
    const row=nssEl.closest('.nss-brk-nss-row');
    row.style.background    =isPos?'rgba(14,165,233,.07)':isNeg?'rgba(239,68,68,.07)':'rgba(148,163,184,.07)';
    row.style.borderTopColor=isPos?'rgba(14,165,233,.2)' :isNeg?'rgba(239,68,68,.2)' :'rgba(148,163,184,.2)';
    const keyEl=row.querySelector('.nss-brk-nss-key'); if(keyEl)keyEl.style.color=color;
    const badge=$('nssBadgeMain');
    if(badge){badge.textContent=nssStr;badge.className='do-badge'+(isPos?' do-badge--pos':isNeg?' do-badge--neg':'');}
}

/* ── Data loader ── */
async function loadNSS(){
    if(!NSS_PID)return;
    try{
        const media=document.querySelector('.nss-media-menu-item.active')?.dataset.m||'all';
        const res=await fetch(`/mk/api/sentiment/totals?project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&media=${media}`);
        if(!res.ok)throw new Error(`HTTP ${res.status}`);
        const json=await res.json(); if(json.error)throw new Error(json.error);
        const t=json.totals||{pos:0,neg:0,neu:0};
        const pos=parseInt(t.pos)||0,neg=parseInt(t.neg)||0,neu=parseInt(t.neu)||0;
        const tot=pos+neg+neu,posneg=pos+neg,nss=posneg===0?0:((pos-neg)/posneg*100);
        countUp($('statPos'),pos); countUp($('statNeu'),neu); countUp($('statNeg'),neg);
        $('pctPos').textContent=pct(pos,tot); $('pctNeu').textContent=pct(neu,tot); $('pctNeg').textContent=pct(neg,tot);
        $('legPos').textContent=numFmt(pos)+' Positive'; $('legNeu').textContent=numFmt(neu)+' Neutral'; $('legNeg').textContent=numFmt(neg)+' Negative';
        updateBreakdown(pos,neu,neg,nss); renderGauge(nss); renderDist(pos,neu,neg);
    }catch(err){console.error('loadNSS:',err);}
}

/* ══ Media Dropdown ══ */
const MEDIA_LABELS={all:'All Media',doc:'Mass Media (News)',twitter:'X / Twitter',facebook:'Facebook',instagram:'Instagram',youtube:'YouTube',tiktok:'TikTok'};
const NSSPage={
    toggleMenu(){const o=$('nssMediaMenu').classList.toggle('show');$('nssMediaBtn').classList.toggle('open',o);},
    selectMedia(el){
        document.querySelectorAll('.nss-media-menu-item').forEach(i=>i.classList.remove('active'));
        el.classList.add('active');
        $('nssMediaLabel').textContent=MEDIA_LABELS[el.dataset.m]||'All Media';
        $('nssMediaMenu').classList.remove('show'); $('nssMediaBtn').classList.remove('open');
        NSSPanel._cache={}; loadNSS();
    },
};
document.addEventListener('click',e=>{const w=$('nssMediaWrap');if(w&&!w.contains(e.target)){$('nssMediaMenu').classList.remove('show');$('nssMediaBtn').classList.remove('open');}});

/* ══════════════════════════════════════════════════════
   SLIDE PANEL — identical to Data Overview DOPanel
══════════════════════════════════════════════════════ */
const NSSPanel=(()=>{
    let _cache={},_allItems=[],_filtered=[],_curSent='all';

    const SENT_MAP={'1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg'};
    const SENT_COLORS={pos:'#0ea5e9',neg:'#EF4444',neu:'#94A3B8',all:'#4361EE'};
    const SENT_LABELS={pos:'Positive',neg:'Negative',neu:'Neutral',all:'All Mentions'};
    const PLAT_META={
        doc:      {label:'Online News', color:'#0284c7'},
        twitter:  {label:'X / Twitter', color:'#1d9bf0'},
        facebook: {label:'Facebook',    color:'#1877f2'},
        instagram:{label:'Instagram',   color:'#e1306c'},
        youtube:  {label:'YouTube',     color:'#ff0000'},
        tiktok:   {label:'TikTok',      color:'#111827'},
    };

    function _normSent(item){const r=String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();return SENT_MAP[r]||'neu';}

    async function open(sentiment){
        _curSent=sentiment||'all';
        const color=SENT_COLORS[_curSent]||getPrimary();
        const label=SENT_LABELS[_curSent]||'Mentions';
        const media=document.querySelector('.nss-media-menu-item.active')?.dataset.m||'all';

        $('nssPanelDot').style.background=color;
        $('nssPanelTitle').textContent=label;
        $('nssPanelCount').textContent='…';
        $('nssPanelMeta').textContent=NSS_SD+' – '+NSS_ED;

        document.querySelectorAll('#nssSntPanel .do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s===_curSent));

        const list=$('nssPanelList');
        list.innerHTML=`<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;

        const overlay=$('nssPanelOverlay'),panel=$('nssSntPanel');
        overlay.classList.remove('hiding'); panel.classList.remove('hiding');
        overlay.classList.add('show'); panel.classList.add('show');

        try{
            const key=`${NSS_PID}_${media}_${NSS_SD}_${NSS_ED}`;
            if(!_cache[key]) _cache[key]=await _fetchAll(media);
            _allItems=_cache[key];
            _filtered=_filterBySent(_allItems,_curSent);
            $('nssPanelCount').textContent=_filtered.length.toLocaleString();
            _render(list,_filtered);
        }catch(err){
            list.innerHTML=`<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:13px;">Gagal memuat data<br><small>${esc(err.message)}</small></div>`;
            $('nssPanelCount').textContent='0';
        }
    }

    function close(){
        const overlay=$('nssPanelOverlay'),panel=$('nssSntPanel');
        panel.classList.add('hiding'); overlay.classList.add('hiding');
        setTimeout(()=>{panel.classList.remove('show','hiding');overlay.classList.remove('show','hiding');},240);
    }

    function closeByOverlay(){close();}

    function filterSent(sent){
        _curSent=sent;
        document.querySelectorAll('#nssSntPanel .do-panel-tab').forEach(t=>t.classList.toggle('active',t.dataset.s===sent));
        _filtered=_filterBySent(_allItems,sent);
        $('nssPanelCount').textContent=_filtered.length.toLocaleString();
        _render($('nssPanelList'),_filtered);
    }

    function _filterBySent(items,sent){return sent==='all'?items:items.filter(i=>_normSent(i)===sent);}

    function exportCsv(){
        if(!_filtered.length){alert('Tidak ada data.');return;}
        const rows=_filtered.map(item=>({
            nama:(item.author_name||item.channel_name||item.publisher||item.source_name||'').trim(),
            sentimen:{pos:'Positif',neg:'Negatif',neu:'Netral'}[_normSent(item)],
            tanggal:item.date_created||'',url:item.url||item.link||'',
            konten:(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,500),
        }));
        const headers=Object.keys(rows[0]);
        const lines=[headers.join(';'),...rows.map(r=>headers.map(h=>{let v=String(r[h]||'').replace(/"/g,'""');return v.includes(';')||v.includes('"')||v.includes('\n')?`"${v}"`:v;}).join(';'))];
        const blob=new Blob(['\uFEFF'+lines.join('\n')],{type:'text/csv;charset=utf-8;'});
        const a=Object.assign(document.createElement('a'),{href:URL.createObjectURL(blob),download:`sentiment_NSS_${_curSent}_${NSS_SD}_${NSS_ED}.csv`});
        document.body.appendChild(a);a.click();document.body.removeChild(a);
    }

    async function _fetchAll(media){
        const platforms=media==='all'?['doc','twitter','facebook','instagram','youtube','tiktok']:[media];
        const res=await Promise.allSettled(platforms.map(p=>_fetchOne(p)));
        return res.flatMap(r=>r.status==='fulfilled'?r.value:[]);
    }

    async function _fetchOne(platform){
        const q=`project_id=${NSS_PID}&start_date=${NSS_SD}&end_date=${NSS_ED}&rows=200&start=0`;
        const eps={
            doc:      `/mk/api/news/mentions?${q}`,
            twitter:  `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
            facebook: `/mk/api/news/fb-top-status?${q}&sub=fblike`,
            instagram:`/mk/api/news/ig-top-status?${q}`,
            youtube:  `/mk/api/news/ytb-top-status?${q}`,
            tiktok:   `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
        };
        const url=eps[platform];if(!url)return[];
        try{
            const ctrl=new AbortController(),tid=setTimeout(()=>ctrl.abort(),20000);
            const res=await fetch(url,{signal:ctrl.signal});clearTimeout(tid);
            if(!res.ok)return[];
            const data=await res.json();
            let items=Array.isArray(data?.data)?data.data:(Array.isArray(data)?data:[]);
            if(platform==='doc') items=items.filter(m=>{const tc=String(m.tcode||'').toLowerCase(),mt=String(m.media_type||'').toLowerCase();return tc==='berita'||mt==='berita'||mt==='doc'||mt==='news'||mt==='online'||mt==='article';});
            return items.map(i=>({...i,_platform:platform}));
        }catch(e){return[];}
    }

    function _render(list,items){
        if(!items.length){list.innerHTML=`<div style="padding:50px 20px;text-align:center;color:var(--do-slate-400);font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>`;return;}
        const SHOW=80;
        list.innerHTML=items.slice(0,SHOW).map(item=>{
            const plat=item._platform||'doc';
            const meta=PLAT_META[plat]||{label:plat,color:getPrimary()};
            const sent=_normSent(item);
            const sentText={pos:'Pos',neg:'Neg',neu:'Neu'}[sent]||'Neu';
            const rawName=(()=>{
                if(plat==='facebook')  return item.from_name||item.page_name||null;
                if(plat==='instagram') return item.username||item.user_name||null;
                if(plat==='tiktok')    return item.author_nickname||item.nickname||null;
                if(plat==='youtube')   return item.channel_title||item.channel_name||null;
                if(plat==='twitter'){const ao=typeof item.author==='object'?item.author:(()=>{try{return JSON.parse(item.author||'{}');}catch(e){return{};}})();return item.name||ao?.name||ao?.scr_name||item.author_name||null;}
                return null;
            })();
            const name=(rawName||item.author_name||item.channel_name||item.publisher||item.source_name||'Unknown').trim();
            const isNum=/^\d{10,}$/.test(name),dName=isNum?`User ${name.slice(-4)}`:name;
            const text=(item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,150);
            const av=(item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||'').trim();
            const dt=(item.date_created||item.created_at||'').split('T')[0];
            const words=dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
            const ini=(words.length>=2?(words[0][0]+words[words.length-1][0]):(words[0]?.[0]||'?')).toUpperCase();
            const safeIni=ini.replace(/['"]/g,'');
            const avHtml=(av&&av.startsWith('http'))?`<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}';">`:ini;
            return `<div class="do-panel-item">
                <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author">${esc(dName)}</div>
                    <div class="do-panel-text">${esc(text||'(tidak ada konten)')}</div>
                    <div class="do-panel-footer">
                        <span class="do-sent-badge do-sent-badge--${sent}">${sentText}</span>
                        <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                        <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                        ${dt?`<span style="margin-left:auto;">${dt}</span>`:''}
                    </div>
                </div>
            </div>`;
        }).join('');
        if(items.length>SHOW) list.insertAdjacentHTML('beforeend',`<div style="padding:9px;text-align:center;font-size:11px;font-weight:600;color:var(--do-slate-400);background:var(--do-slate-50);border-top:1px dashed var(--do-slate-200);">+${(items.length-SHOW).toLocaleString()} mentions lainnya · Export CSV untuk lihat semua</div>`);
    }

    return{open,close,closeByOverlay,filterSent,exportCsv,get _cache(){return _cache;},set _cache(v){_cache=v;}};
})();

/* ══ Boot ══ */
document.addEventListener('DOMContentLoaded',()=>{
    const needle=$('nssNeedle');
    if(needle) needle.setAttribute('transform','rotate(0,250,260)');
    loadNSS();
});
</script>
@endsection