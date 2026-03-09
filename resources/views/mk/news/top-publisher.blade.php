@extends('mk.layouts.app')

@section('title', 'Top News Publishers - SMADIMENT')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
  --green:        #038047;
  --green-dark:   #026738;
  --green-dim:    rgba(3,128,71,.08);
  --green-border: rgba(3,128,71,.2);
  --red:          #e02020;
  --red-dim:      rgba(224,32,32,.08);
  --blue:         #2563eb;
  --text-hi:      #0f172a;
  --text-md:      #475569;
  --text-lo:      #94a3b8;
  --bg:           #f1f5f9;
  --surface:      #ffffff;
  --border:       #e2e8f0;
  --radius:       14px;
  --radius-sm:    10px;
  --shadow:       0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
  --shadow-md:    0 4px 16px rgba(0,0,0,.09);
  --font:         'Plus Jakarta Sans', sans-serif;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: var(--font); background: var(--bg); color: var(--text-hi); }

.tp-page {
  padding: 24px;
  max-width: 1600px;
  margin: 0 auto;
  min-height: 100vh;
}

.tp-header { margin-bottom: 22px; }
.tp-header h1 { font-size: 24px; font-weight: 800; color: var(--text-hi); letter-spacing: -.4px; margin-bottom: 4px; }
.tp-header p  { font-size: 13px; color: var(--text-md); font-weight: 500; }

/* ── FILTER BAR ── */
.tp-filter {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 14px 20px; margin-bottom: 22px;
  box-shadow: var(--shadow); display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
}
.tp-date-pill {
  display: flex; align-items: center; gap: 8px; padding: 8px 14px;
  background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm);
  font-size: 13px; font-weight: 600; color: var(--text-hi); cursor: pointer;
  transition: border-color .2s, box-shadow .2s; white-space: nowrap;
}
.tp-date-pill:hover { border-color: var(--green); box-shadow: 0 0 0 3px var(--green-dim); }
.tp-date-pill svg { width: 15px; height: 15px; stroke: var(--text-md); fill: none; flex-shrink: 0; }
.tp-filter-div { width: 1px; height: 28px; background: var(--border); flex-shrink: 0; }
.tp-filter-btn-group { display: flex; gap: 6px; flex-wrap: wrap; }
.tp-filter-btn {
  display: flex; align-items: center; gap: 6px; padding: 7px 14px;
  background: var(--bg); border: 1px solid var(--border); border-radius: 20px;
  font-family: var(--font); font-size: 12px; font-weight: 700; color: var(--text-md);
  cursor: pointer; transition: all .2s; white-space: nowrap;
}
.tp-filter-btn:hover { background: var(--surface); border-color: var(--green); color: var(--green); }
.tp-filter-btn.active { background: var(--green); border-color: var(--green); color: #fff; }
.tp-filter-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; flex-shrink: 0; }
.tp-apply {
  margin-left: auto; display: flex; align-items: center; gap: 7px;
  padding: 8px 18px; background: linear-gradient(135deg, var(--green), var(--green-dark));
  color: #fff; border: none; border-radius: var(--radius-sm);
  font-family: var(--font); font-size: 13px; font-weight: 700;
  cursor: pointer; transition: box-shadow .2s, transform .2s;
  box-shadow: 0 4px 12px rgba(3,128,71,.25);
}
.tp-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(3,128,71,.35); }
.tp-apply svg { width: 14px; height: 14px; stroke: #fff; fill: none; }

/* ── TOTALS ── */
.tp-totals { display: flex; gap: 12px; margin-bottom: 22px; flex-wrap: wrap; }
.tp-total-pill {
  background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm);
  padding: 14px 20px; box-shadow: var(--shadow); flex: 1; min-width: 160px;
  position: relative; overflow: hidden; transition: box-shadow .2s;
}
.tp-total-pill:hover { box-shadow: var(--shadow-md); }
.tp-total-pill::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
.tp-total-pill--articles::before  { background: linear-gradient(90deg, var(--blue), #60a5fa); }
.tp-total-pill--publishers::before{ background: linear-gradient(90deg, var(--green), #34d399); }
.tp-total-pill--period::before    { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
.tp-total-label { font-size: 10px; font-weight: 800; color: var(--text-lo); text-transform: uppercase; letter-spacing: .7px; margin-bottom: 8px; }
.tp-total-value { font-size: 28px; font-weight: 800; color: var(--text-hi); letter-spacing: -1px; line-height: 1; }
.tp-total-value--articles   { color: var(--blue); }
.tp-total-value--publishers { color: var(--green); }
.tp-total-sub { font-size: 11px; color: var(--text-lo); font-weight: 600; margin-top: 6px; }

/* ── TAB BAR ── */
.tp-tab-bar {
  background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
  padding: 12px 16px; margin-bottom: 20px;
  display: flex; align-items: center; gap: 10px; flex-wrap: wrap; box-shadow: var(--shadow);
}
.tp-tab-sep { flex: 1; }
.tp-csv-btn {
  display: flex; align-items: center; gap: 6px; padding: 7px 14px;
  background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm);
  font-family: var(--font); font-size: 12px; font-weight: 600; color: var(--text-md);
  cursor: pointer; transition: all .2s;
}
.tp-csv-btn:hover { background: var(--green); color: #fff; border-color: var(--green); }
.tp-csv-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; }

/* ── CHART GRID ── */
.tp-chart-row1 {
  display: grid;
  grid-template-columns: 3fr 2fr;
  gap: 18px;
  margin-bottom: 18px;
}

.tp-card {
  background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
  box-shadow: var(--shadow); overflow: hidden; display: flex; flex-direction: column;
}
.tp-card-head {
  padding: 14px 18px; border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
  flex-shrink: 0; gap: 12px;
}
.tp-card-title { font-size: 14px; font-weight: 800; color: var(--text-hi); letter-spacing: -.2px; }
.tp-card-body  { flex: 1; padding: 14px 8px 14px 4px; position: relative; }
.tp-ch         { width: 100%; }
.tp-ch--bar    { height: 560px; }
.tp-ch--donut  { height: 560px; }

/* Legend dot */
.tp-legend-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

/* Skeleton */
.tp-skel {
  position: absolute; inset: 0;
  background: linear-gradient(90deg, #f8fafc 25%, #e2e8f0 50%, #f8fafc 75%);
  background-size: 200% 100%; animation: shimmer 1.4s ease-in-out infinite;
  border-radius: 8px; z-index: 3;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* ── DATE PICKER MODAL ── */
.tp-dp-modal {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,.45); backdrop-filter: blur(6px);
  display: none; align-items: center; justify-content: center;
}
.tp-dp-modal.show { display: flex; }
.tp-dp-overlay { position: absolute; inset: 0; cursor: pointer; }
.tp-dp-box {
  position: relative; z-index: 1;
  background: #fff; border-radius: var(--radius);
  box-shadow: 0 24px 60px rgba(0,0,0,.25);
  display: flex; max-width: 880px; width: 92%;
  animation: popIn .25s cubic-bezier(.34,1.3,.64,1);
}
@keyframes popIn { from{opacity:0;transform:scale(.94) translateY(12px)} to{opacity:1;transform:none} }
.tp-dp-sidebar {
  width: 168px; background: var(--bg); border-right: 1px solid var(--border);
  padding: 14px 10px; border-radius: var(--radius) 0 0 var(--radius);
  display: flex; flex-direction: column; gap: 3px;
}
.tp-dp-preset {
  padding: 9px 14px; border: none; border-radius: 8px;
  font-family: var(--font); font-size: 12px; font-weight: 600;
  color: var(--text-hi); text-align: left; cursor: pointer;
  background: transparent; transition: all .15s;
}
.tp-dp-preset:hover { background: #fff; color: var(--green); }
.tp-dp-preset.active { background: var(--green); color: #fff; }
.tp-dp-content { flex: 1; padding: 20px; display: flex; flex-direction: column; }
.tp-dp-nav { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 18px; }
.tp-dp-nav-btn {
  width: 34px; height: 34px; border-radius: 8px;
  background: var(--bg); border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all .15s; flex-shrink: 0;
}
.tp-dp-nav-btn:hover { background: var(--green); border-color: var(--green); color: #fff; }
.tp-dp-nav-btn svg { width: 18px; height: 18px; }
.tp-dp-cals { display: flex; gap: 20px; flex: 1; }
.tp-dp-cal { flex: 1; }
.tp-dp-month-title { font-size: 15px; font-weight: 800; text-align: center; margin-bottom: 14px; color: var(--text-hi); }
.tp-dp-weekdays { display: grid; grid-template-columns: repeat(7,1fr); gap: 3px; margin-bottom: 6px; }
.tp-dp-wd { text-align: center; font-size: 10px; font-weight: 800; color: var(--text-lo); padding: 6px 0; }
.tp-dp-days { display: grid; grid-template-columns: repeat(7,1fr); gap: 3px; }
.tp-dp-day {
  aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 600; border-radius: 7px;
  cursor: pointer; transition: all .12s; color: var(--text-hi);
  background: transparent; border: none; font-family: var(--font);
}
.tp-dp-day:hover:not(.other):not(.dis) { background: var(--bg); }
.tp-dp-day.other { color: #cbd5e1; cursor: default; }
.tp-dp-day.dis   { color: #e2e8f0; cursor: not-allowed; }
.tp-dp-day.today { border: 2px solid var(--green); }
.tp-dp-day.sel   { background: var(--green); color: #fff; }
.tp-dp-day.rng   { background: var(--green-dim); color: var(--green); }
.tp-dp-display {
  background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm);
  padding: 13px 16px; text-align: center; margin-bottom: 16px;
  font-size: 13px; font-weight: 700; color: var(--text-hi);
}
.tp-dp-footer { display: flex; gap: 10px; justify-content: flex-end; }
.tp-dp-cancel {
  padding: 9px 20px; background: var(--bg); border: 1px solid var(--border);
  border-radius: 8px; font-family: var(--font); font-size: 13px; font-weight: 700;
  color: var(--text-md); cursor: pointer; transition: background .15s;
}
.tp-dp-cancel:hover { background: var(--border); }
.tp-dp-apply {
  padding: 9px 20px; background: linear-gradient(135deg, var(--green), var(--green-dark));
  color: #fff; border: none; border-radius: 8px;
  font-family: var(--font); font-size: 13px; font-weight: 700;
  cursor: pointer; transition: box-shadow .2s; box-shadow: 0 3px 10px rgba(3,128,71,.25);
}
.tp-dp-apply:hover { box-shadow: 0 6px 18px rgba(3,128,71,.35); }

/* ════════════════════════════════════════
   ARTICLE POPUP
════════════════════════════════════════ */
@keyframes tpPopIn {
  from { opacity:0; transform:translateY(14px) scale(.94); }
  to   { opacity:1; transform:translateY(0) scale(1); }
}

#tpPopup {
  position: fixed; z-index: 99999;
  background: #fff; border: 1px solid var(--border); border-radius: var(--radius);
  box-shadow: 0 20px 60px rgba(0,0,0,.22);
  width: 480px; height: 600px;
  display: none; flex-direction: column; overflow: hidden;
  font-family: var(--font);
  animation: tpPopIn .22s cubic-bezier(.34,1.3,.64,1);
  user-select: none;
}
#tpPopup.visible { display: flex; }

.tpp-header {
  display: flex; align-items: center; gap: 8px; padding: 12px 16px;
  background: var(--bg); border-bottom: 1px solid var(--border);
  cursor: grab; flex-shrink: 0;
}
.tpp-header:active { cursor: grabbing; }
.tpp-drag { display:flex; flex-direction:column; gap:3px; margin-right:4px; opacity:.4; flex-shrink:0; }
.tpp-drag span { display:block; width:18px; height:2px; background:var(--text-md); border-radius:1px; }
.tpp-dot   { width:10px; height:10px; border-radius:50%; flex-shrink:0; background:var(--red); }
.tpp-title { font-size:13px; font-weight:700; color:var(--text-hi); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.tpp-count { background:var(--green); color:#fff; border-radius:10px; padding:1px 9px; font-size:11px; font-weight:800; flex-shrink:0; }
.tpp-close {
  width:28px; height:28px; border-radius:8px; border:none;
  background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center;
  color:var(--text-md); font-size:20px; line-height:1; transition:all .15s; flex-shrink:0;
}
.tpp-close:hover { background:#fee2e2; color:#991b1b; }

.tpp-actions {
  display:flex; align-items:center; gap:8px; padding:7px 13px;
  border-bottom:1px solid var(--border); background:#fafbfc; flex-shrink:0;
}
.tpp-meta {
  flex:1; font-size:10px; font-weight:700; color:var(--text-lo);
  text-transform:uppercase; letter-spacing:.5px;
  display:flex; align-items:center; gap:8px; overflow:hidden;
}
.tpp-meta svg { width:11px; height:11px; stroke:currentColor; fill:none; stroke-width:2; flex-shrink:0; }
.tpp-meta__label { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.tpp-export-btn {
  display:flex; align-items:center; gap:5px; padding:5px 11px;
  background:var(--green); color:#fff; border:none; border-radius:8px;
  font-family:var(--font); font-size:10px; font-weight:700;
  cursor:pointer; transition:all .15s; white-space:nowrap;
}
.tpp-export-btn:hover { background:var(--green-dark); transform:translateY(-1px); }
.tpp-export-btn svg { width:11px; height:11px; stroke:#fff; fill:none; stroke-width:2.5; }

.tpp-list { overflow-y:auto; flex:1; padding:4px 0; min-height:0; }
.tpp-list::-webkit-scrollbar { width:5px; }
.tpp-list::-webkit-scrollbar-thumb { background:var(--border); border-radius:4px; }
.tpp-list::-webkit-scrollbar-thumb:hover { background:var(--text-lo); }

.tpp-item {
  display:flex; gap:10px; padding:10px 14px; border-bottom:1px solid #f1f5f9;
  transition:background .1s; cursor:pointer; align-items:flex-start;
}
.tpp-item:last-child { border-bottom:none; }
.tpp-item:hover { background:#f0fdf4; }
.tpp-avatar {
  width:38px; height:38px; border-radius:50%; flex-shrink:0;
  background:linear-gradient(135deg,var(--red),#f87171);
  color:#fff; font-weight:700; font-size:13px;
  display:flex; align-items:center; justify-content:center;
  border:1.5px solid var(--border); overflow:hidden;
}
.tpp-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.tpp-item-body { flex:1; min-width:0; }
.tpp-item-pub  { font-size:12px; font-weight:700; color:var(--text-hi); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.tpp-item-url  { font-size:10px; color:var(--text-lo); font-weight:500; margin-bottom:3px; }
.tpp-item-text {
  font-size:12px; color:var(--text-md); line-height:1.5;
  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;
  overflow:hidden; margin-bottom:5px;
}
.tpp-item-foot { display:flex; align-items:center; gap:6px; font-size:10px; color:var(--text-lo); flex-wrap:wrap; }
.tpp-sent { padding:1px 7px; border-radius:10px; font-size:9px; font-weight:800; text-transform:uppercase; }
.tpp-sent--pos { background:#d1fae5; color:#065f46; }
.tpp-sent--neg { background:#fee2e2; color:#991b1b; }
.tpp-sent--neu { background:var(--bg); color:var(--text-md); }

.tpp-loading {
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  height:100%; gap:14px; color:var(--text-md); font-size:13px; font-weight:600;
}
.tpp-spinner {
  width:32px; height:32px; border:3px solid var(--border); border-top-color:var(--green);
  border-radius:50%; animation:tpSpin .7s linear infinite;
}
@keyframes tpSpin { to { transform:rotate(360deg); } }

/* Detail Panel */
@keyframes tpDetailIn { from{transform:translateX(100%)} to{transform:translateX(0)} }
#tpDetailPanel {
  position:absolute; inset:0; background:#fff; z-index:10;
  display:none; flex-direction:column;
  animation:tpDetailIn .22s cubic-bezier(.4,0,.2,1);
}
#tpDetailPanel.visible { display:flex; }
.tpdp-header {
  display:flex; align-items:center; gap:10px; padding:12px 16px;
  background:var(--bg); border-bottom:1px solid var(--border); flex-shrink:0;
}
.tpdp-back {
  width:30px; height:30px; border-radius:8px; border:1px solid var(--border);
  background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;
  color:var(--text-md); transition:all .15s; flex-shrink:0;
}
.tpdp-back:hover { background:var(--green-dim); color:var(--green); border-color:var(--green-border); }
.tpdp-back svg { width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:2.5; }
.tpdp-title { font-size:13px; font-weight:700; color:var(--text-hi); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.tpdp-close { width:28px; height:28px; border-radius:8px; border:none; background:transparent; cursor:pointer; font-size:20px; color:var(--text-md); display:flex; align-items:center; justify-content:center; transition:all .15s; }
.tpdp-close:hover { background:#fee2e2; color:#991b1b; }
.tpdp-body { overflow-y:auto; flex:1; padding:16px; }
.tpdp-body::-webkit-scrollbar { width:5px; }
.tpdp-body::-webkit-scrollbar-thumb { background:var(--border); border-radius:4px; }
.tpdp-avatar-row { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
.tpdp-avatar-lg {
  width:52px; height:52px; border-radius:50%;
  background:linear-gradient(135deg,var(--red),#f87171);
  color:#fff; font-weight:700; font-size:18px;
  display:flex; align-items:center; justify-content:center;
  border:2px solid var(--border); overflow:hidden; flex-shrink:0;
}
.tpdp-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.tpdp-pub-name { font-size:15px; font-weight:700; color:var(--text-hi); }
.tpdp-pub-url  { font-size:11px; color:var(--text-lo); font-weight:500; }
.tpdp-ttl { font-size:14px; font-weight:800; color:var(--text-hi); line-height:1.45; margin-bottom:10px; }
.tpdp-content {
  font-size:12px; color:var(--text-md); line-height:1.7; margin-bottom:12px;
  background:var(--bg); border-radius:10px; padding:12px 14px;
  border:1px solid var(--border); word-break:break-word;
  display:-webkit-box; -webkit-line-clamp:6; -webkit-box-orient:vertical; overflow:hidden;
}
.tpdp-meta-row { display:flex; align-items:center; justify-content:space-between; font-size:11px; color:var(--text-lo); font-weight:500; margin-bottom:12px; }
.tpdp-sent-badge { display:inline-flex; align-items:center; gap:5px; padding:5px 14px; border-radius:20px; font-size:12px; font-weight:700; margin-bottom:12px; }
.tpdp-sent-badge--pos { background:#d1fae5; color:#065f46; }
.tpdp-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.tpdp-sent-badge--neu { background:var(--bg); color:var(--text-md); }
.tpdp-stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:12px; }
.tpdp-stat-box { background:var(--bg); border-radius:10px; padding:10px 12px; border:1px solid var(--border); text-align:center; }
.tpdp-stat-val { font-size:16px; font-weight:700; color:var(--text-hi); }
.tpdp-stat-lbl { font-size:9px; font-weight:700; color:var(--text-lo); text-transform:uppercase; letter-spacing:.4px; margin-top:2px; }
.tpdp-link-btn {
  display:flex; align-items:center; justify-content:center; gap:8px;
  padding:10px 14px; background:var(--green); color:#fff;
  border-radius:10px; font-size:12px; font-weight:700;
  text-decoration:none; transition:all .15s; width:100%; margin-top:4px;
}
.tpdp-link-btn:hover { background:var(--green-dark); }
.tpdp-link-btn svg { width:13px; height:13px; stroke:#fff; fill:none; stroke-width:2.5; }

@media (max-width: 900px) { .tp-chart-row1 { grid-template-columns: 1fr; } }
@media (max-width: 700px) {
  .tp-page { padding: 14px; }
  .tp-dp-box { flex-direction: column; }
  .tp-dp-sidebar { width: 100%; flex-direction: row; overflow-x: auto; border-right: none; border-bottom: 1px solid var(--border); border-radius: var(--radius) var(--radius) 0 0; }
  .tp-dp-cals { flex-direction: column; }
}
</style>
@endsection

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->subDays(7)->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
  $newsType  = request()->get('news_type', 'article');
@endphp

<div class="tp-page">

  <div class="tp-header">
    <h1>Sites</h1>
    <p>Distribution by Online News — Top publishers by article volume and total mentions</p>
  </div>

  <div class="tp-filter">
    <form id="tpForm" method="GET" action="{{ route('mk.news.top-publishers') }}" style="display:contents;">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hSD" value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hED" value="{{ $endDate }}">
      <input type="hidden" name="news_type"  id="hNT" value="{{ $newsType }}">

      <button type="button" class="tp-date-pill" id="tpDpTrigger">
        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span id="tpDpLabel">{{ $startDate }} to {{ $endDate }}</span>
      </button>

      <div class="tp-filter-div"></div>

      <div class="tp-filter-btn-group">
        <button type="button" class="tp-filter-btn active" id="btnNewsType" onclick="tpToggleType(this)">
          <svg viewBox="0 0 24 24" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
          Online News (Ind)
          <span style="width:0;height:0;border-left:4px solid transparent;border-right:4px solid transparent;border-top:5px solid currentColor;margin-left:3px;"></span>
        </button>
        <button type="button" class="tp-filter-btn">Filter by Page Rank <span style="width:0;height:0;border-left:4px solid transparent;border-right:4px solid transparent;border-top:5px solid currentColor;margin-left:3px;"></span></button>
        <button type="button" class="tp-filter-btn">Filter Tier <span style="width:0;height:0;border-left:4px solid transparent;border-right:4px solid transparent;border-top:5px solid currentColor;margin-left:3px;"></span></button>
      </div>

      <button type="submit" class="tp-apply">
        <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Apply
      </button>
    </form>
  </div>

  @if(!$projectId)
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:var(--radius);padding:16px 20px;font-size:13px;font-weight:600;color:#92400e;margin-bottom:20px;">
      ⚠️ No project selected. Please select a project from the sidebar.
    </div>
  @else

  <div class="tp-totals">
    <div class="tp-total-pill tp-total-pill--articles">
      <div class="tp-total-label">Total Articles</div>
      <div class="tp-total-value tp-total-value--articles" id="valArticles">—</div>
      <div class="tp-total-sub">unique published articles</div>
    </div>
    <div class="tp-total-pill tp-total-pill--publishers">
      <div class="tp-total-label">Total Publishers</div>
      <div class="tp-total-value tp-total-value--publishers" id="valPublishers">—</div>
      <div class="tp-total-sub">active news sites</div>
    </div>
    <div class="tp-total-pill tp-total-pill--period">
      <div class="tp-total-label">Period</div>
      <div class="tp-total-value" style="color:#7c3aed;font-size:16px;letter-spacing:0;padding-top:4px;">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
      <div class="tp-total-sub">selected date range</div>
    </div>
  </div>

  <div class="tp-tab-bar">
    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:var(--text-md);">
        <span class="tp-legend-dot" style="background:var(--red);"></span> Articles
      </div>
    </div>
    <div class="tp-tab-sep"></div>
    <input type="text" id="tpSearch" placeholder="🔍  Search publisher…"
      style="padding:7px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);font-family:var(--font);font-size:12px;font-weight:600;background:var(--bg);outline:none;width:220px;transition:border-color .2s;"
      oninput="tpFilter(this.value)"
      onfocus="this.style.borderColor='var(--green)'"
      onblur="this.style.borderColor='var(--border)'">
    <button class="tp-csv-btn" onclick="tpExportCSV()">
      <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
      Copy CSV data
    </button>
  </div>

  <div class="tp-chart-row1">

    {{-- Bar Chart --}}
    <div class="tp-card">
      <div class="tp-card-head">
        <span class="tp-card-title">Total articles by publisher</span>
        <span style="display:flex;align-items:center;gap:5px;font-size:11px;font-weight:700;color:var(--text-lo);background:var(--bg);padding:3px 10px;border-radius:20px;border:1px solid var(--border);">
          <span class="tp-legend-dot" style="background:var(--red);"></span>Total Articles
        </span>
      </div>
      <div class="tp-card-body">
        <div class="tp-ch tp-ch--bar" id="chArticles"></div>
        <div class="tp-skel" id="skArticles"></div>
      </div>
    </div>

    {{-- Donut Chart --}}
    <div class="tp-card">
      <div class="tp-card-head">
        <span class="tp-card-title">Publisher's shares</span>
        <span style="display:flex;align-items:center;gap:5px;font-size:11px;font-weight:700;color:var(--text-lo);background:var(--bg);padding:3px 10px;border-radius:20px;border:1px solid var(--border);">
          Top 9 + others
        </span>
      </div>
      <div class="tp-card-body" style="padding:10px;">
        <div class="tp-ch tp-ch--donut" id="chDonut"></div>
        <div class="tp-skel" id="skDonut"></div>
      </div>
    </div>

  </div>

  @endif

</div>

{{-- DATE PICKER MODAL --}}
<div class="tp-dp-modal" id="tpDpModal">
  <div class="tp-dp-overlay" onclick="TpDp.close()"></div>
  <div class="tp-dp-box">
    <div class="tp-dp-sidebar">
      <button class="tp-dp-preset" data-p="today">Today</button>
      <button class="tp-dp-preset" data-p="yesterday">Yesterday</button>
      <button class="tp-dp-preset" data-p="last7">Last 7 Days</button>
      <button class="tp-dp-preset" data-p="last30">Last 30 Days</button>
      <button class="tp-dp-preset" data-p="thismonth">This Month</button>
      <button class="tp-dp-preset" data-p="lastmonth">Last Month</button>
      <button class="tp-dp-preset active" data-p="custom">Custom Range</button>
    </div>
    <div class="tp-dp-content">
      <div class="tp-dp-nav">
        <button class="tp-dp-nav-btn" onclick="TpDp.nav(-1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
        <div class="tp-dp-cals">
          <div class="tp-dp-cal" id="tpCal1"></div>
          <div class="tp-dp-cal" id="tpCal2"></div>
        </div>
        <button class="tp-dp-nav-btn" onclick="TpDp.nav(1)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
      </div>
      <div class="tp-dp-display" id="tpDpRange">{{ $startDate }} – {{ $endDate }}</div>
      <div class="tp-dp-footer">
        <button class="tp-dp-cancel" onclick="TpDp.close()">Cancel</button>
        <button class="tp-dp-apply" onclick="TpDp.apply()">Apply</button>
      </div>
    </div>
  </div>
</div>

{{-- ARTICLE POPUP --}}
<div id="tpPopup">
  <div class="tpp-header" id="tppHeader">
    <div class="tpp-drag"><span></span><span></span><span></span></div>
    <div class="tpp-dot" id="tppDot"></div>
    <span class="tpp-title" id="tppTitle">Articles</span>
    <span class="tpp-count" id="tppCount">…</span>
    <button class="tpp-close" onclick="TpPopup.close()">×</button>
  </div>
  <div class="tpp-actions">
    <div class="tpp-meta">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <span class="tpp-meta__label" id="tppMeta">—</span>
    </div>
    <button class="tpp-export-btn" onclick="TpPopup.exportCsv()">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export CSV
    </button>
  </div>
  <div class="tpp-list" id="tppList"></div>
  <div id="tpDetailPanel">
    <div class="tpdp-header">
      <button class="tpdp-back" onclick="TpDetail.close()">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="tpdp-title" id="tpdpTitle">Detail Artikel</span>
      <button class="tpdp-close" onclick="TpPopup.close()">×</button>
    </div>
    <div class="tpdp-body" id="tpdpBody"></div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

const TpCfg = {
  pid:      {{ $projectId ? (int)$projectId : 'null' }},
  sd:       '{{ $startDate }}',
  ed:       '{{ $endDate }}',
  newsType: '{{ $newsType }}',
};

const numFmt = n => parseInt(n||0).toLocaleString('en-US');
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };

const Charts = {};
window.addEventListener('resize', () => Object.values(Charts).forEach(c => { try{ c.resize(); }catch(e){} }));

const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };

const RED  = '#e02020';
const BLUE = '#2563eb';
const PIE_COLORS = ['#2563eb','#e02020','#f97316','#a21caf','#0891b2','#15803d','#b45309','#0f766e','#7c3aed','#be185d','#94a3b8','#64748b'];

const TT = {
  backgroundColor: '#1e293b', borderColor: '#334155', borderWidth: 1,
  padding: [10, 14],
  textStyle: { color: '#f8fafc', fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: 12 },
  extraCssText: 'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
};

let _allData  = [];
let _filtered = [];

function hostnameOf(url) {
  try {
    const h = new URL(url.startsWith('http') ? url : 'https://' + url).hostname;
    return h.replace(/^www\./, '');
  } catch { return url.replace(/^www\./, '').split('/')[0]; }
}

/* ════════════════════════════════════════
   MAIN DATA LOAD
════════════════════════════════════════ */
async function loadData() {
  if (!TpCfg.pid) return;
  try {
    const res    = await fetch(`/mk/api/news/top-publisher?project_id=${TpCfg.pid}&start_date=${TpCfg.sd}&end_date=${TpCfg.ed}&news_type=${TpCfg.newsType}`);
    const result = await res.json();
    if (!result.success || !result.data) return;

    _allData  = result.data;
    _filtered = _allData;

    const totalArt = result.meta?.total_articles  || _allData.reduce((s,p) => s+(p.count||0), 0);
    const totalPub = result.meta?.total_publishers || _allData.length;

    document.getElementById('valArticles').textContent   = numFmt(totalArt);
    document.getElementById('valPublishers').textContent = numFmt(totalPub);

    renderCharts(_filtered);
  } catch(err) {
    console.error('Load error:', err);
  }
}

/* ════════════════════════════════════════
   RENDER CHARTS
════════════════════════════════════════ */
function renderCharts(data) {
  renderArticlesBar(data.slice(0, 20));
  renderDonut(data);
}

/* ── Bar Chart ── */
function renderArticlesBar(items) {
  hideSk('skArticles');
  const domains = items.map(p => shortDomain(p.domain)).reverse();
  const values  = items.map(p => p.count || 0).reverse();

  if (Charts.art) Charts.art.dispose();
  const dom = document.getElementById('chArticles');
  if (!dom) return;
  Charts.art = echarts.init(dom, null, { renderer: 'canvas' });

  Charts.art.setOption({
    animation: true, animationDuration: 700, animationEasing: 'cubicOut',
    backgroundColor: '#fff',
    tooltip: {
      ...TT, trigger: 'axis',
      axisPointer: { type: 'shadow', shadowStyle: { color: 'rgba(37,99,235,.05)' } },
      formatter: p => `<b style="font-size:13px;">${p[0].name}</b><br>${numFmt(p[0].value)} articles`,
    },
    grid: { top: 10, right: 70, bottom: 10, left: 10, containLabel: true },
    xAxis: { type: 'value', axisLine:{show:false}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}}, axisLabel:{fontFamily:"'Plus Jakarta Sans',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK} },
    yAxis: { type: 'category', data: domains, axisLine:{show:false}, axisTick:{show:false}, axisLabel:{fontFamily:"'Plus Jakarta Sans',sans-serif",fontSize:11,fontWeight:'700',color:'#475569'} },
    series: [{
      type: 'bar',
      data: values.map((v,i) => ({
        value: v,
        itemStyle: { color: RED, borderRadius: [0, 5, 5, 0], opacity: i === values.length-1 ? 1 : 0.8 }
      })),
      barMaxWidth: 22,
      label: { show: true, position: 'right', fontFamily:"'Plus Jakarta Sans',sans-serif", fontWeight:'700', fontSize:11, color:'#475569', formatter: p => numFmt(p.value) },
    }]
  });

  Charts.art.on('click', params => {
    if (params.componentType !== 'series') return;
    const domain = items[items.length - 1 - params.dataIndex]?.domain;
    if (!domain) return;
    TpPopup.open(domain, params.event.event.clientX, params.event.event.clientY);
  });
  Charts.art.on('mouseover', () => { Charts.art.getDom().style.cursor = 'pointer'; });
  Charts.art.on('mouseout',  () => { Charts.art.getDom().style.cursor = 'default'; });
}

/* ── Donut Chart ── */
function renderDonut(data) {
  hideSk('skDonut');

  const top9  = data.slice(0, 9);
  const rest  = data.slice(9);   // "others"
  const total = data.reduce((s,p) => s+(p.count||0), 0);

  const items = top9.map((p,i) => ({
    name:   shortDomain(p.domain),
    value:  p.count || 0,
    color:  PIE_COLORS[i],
    domain: p.domain,
    isOthers: false,
  }));

  if (rest.length) {
    const othersVal = rest.reduce((s,p) => s+(p.count||0), 0);
    items.push({
      name:     `others (${rest.length})`,
      value:    othersVal,
      color:    '#94a3b8',
      domain:   null,
      isOthers: true,
      restData: rest,
    });
  }

  if (Charts.donut) Charts.donut.dispose();
  const dom = document.getElementById('chDonut');
  if (!dom) return;
  Charts.donut = echarts.init(dom, null, { renderer: 'canvas' });

  Charts.donut.setOption({
    animation: true, animationDuration: 900, animationEasing: 'cubicOut',
    backgroundColor: '#fff',
    tooltip: {
      ...TT, trigger: 'item',
      formatter: p => {
        const pct = total > 0 ? ((p.value/total)*100).toFixed(1) : '0';
        const hint = p.data?.isOthers ? '<br><small style="opacity:.7">Klik untuk lihat semua</small>' : '';
        return `<b>${p.name}</b><br>${numFmt(p.value)} articles (${pct}%)${hint}`;
      }
    },
    legend: {
      bottom: 0, type: 'scroll',
      data: items.map(d => d.name),
      textStyle: { fontFamily:"'Plus Jakarta Sans',sans-serif", fontSize:11, fontWeight:'700', color:'#475569' },
      icon: 'circle', itemWidth:9, itemHeight:9, itemGap:12,
    },
    graphic: [{
      type: 'text', left: 'center', top: '38%',
      style: { text: numK(total), textAlign: 'center', fill: '#0f172a', fontSize: 22, fontWeight: '800', fontFamily: "'Plus Jakarta Sans', sans-serif" }
    },{
      type: 'text', left: 'center', top: '46%',
      style: { text: 'articles', textAlign: 'center', fill: '#94a3b8', fontSize: 11, fontWeight: '700', fontFamily: "'Plus Jakarta Sans', sans-serif" }
    }],
    series: [{
      type: 'pie',
      radius: ['42%', '72%'],
      center: ['50%', '44%'],
      avoidLabelOverlap: true,
      minAngle: 2,
      itemStyle: { borderColor: '#fff', borderWidth: 3 },
      label: {
        show: true,
        formatter: p => {
          const pct = total > 0 ? Math.round((p.value/total)*100) : 0;
          return pct >= 3 ? `${pct}%` : '';
        },
        fontFamily:"'Plus Jakarta Sans',sans-serif", fontWeight:'800', fontSize:11, color:'#fff',
        position: 'inside',
      },
      labelLine: { show: false },
      emphasis: {
        scale: true, scaleSize: 8,
        itemStyle: { shadowBlur: 20, shadowColor: 'rgba(0,0,0,.25)' },
        label: { show: true, fontSize: 13, fontWeight: '800', color: '#fff' }
      },
      data: items.map((d,i) => ({
        name:      d.name,
        value:     d.value,
        domain:    d.domain,
        isOthers:  d.isOthers,
        restData:  d.restData || null,
        itemStyle: { color: d.color || PIE_COLORS[i % PIE_COLORS.length] },
      })),
    }]
  });

  /* Click donut slice */
  Charts.donut.on('click', params => {
    const rect = Charts.donut.getDom().getBoundingClientRect();
    const cx   = rect.left + rect.width / 2;
    const cy   = rect.top  + rect.height / 3;

    if (params.data?.isOthers) {
      /* ── "others" → open mini-list of all remaining publishers ── */
      OthersPopup.open(params.data.restData || [], cx, cy);
      return;
    }
    if (params.data?.domain) {
      TpPopup.open(params.data.domain, cx, cy);
    }
  });
  Charts.donut.on('mouseover', () => { Charts.donut.getDom().style.cursor = 'pointer'; });
  Charts.donut.on('mouseout',  () => { Charts.donut.getDom().style.cursor = 'default'; });
}

/* ════════════════════════════════════════
   ARTICLE POPUP
   FIX: fetch ALL mentions & filter loosely
════════════════════════════════════════ */
const TpPopup = {
  _drag: false, _ox: 0, _oy: 0,
  _cache: {}, _items: [], _curDomain: null,

  init() {
    const popup  = document.getElementById('tpPopup');
    const header = document.getElementById('tppHeader');
    header.addEventListener('mousedown', e => {
      this._drag = true;
      const r = popup.getBoundingClientRect();
      this._ox = e.clientX - r.left; this._oy = e.clientY - r.top;
      document.body.style.userSelect = 'none';
    });
    document.addEventListener('mousemove', e => {
      if (!this._drag) return;
      const vw = window.innerWidth, vh = window.innerHeight;
      popup.style.left = Math.max(0, Math.min(e.clientX - this._ox, vw - popup.offsetWidth)) + 'px';
      popup.style.top  = Math.max(0, Math.min(e.clientY - this._oy, vh - popup.offsetHeight)) + 'px';
    });
    document.addEventListener('mouseup', () => { this._drag = false; document.body.style.userSelect = ''; });
  },

  _pos(popup, x, y) {
    const pw = 480, ph = 600, vw = window.innerWidth, vh = window.innerHeight;
    let left = x + 18, top = y - 40;
    if (left + pw > vw - 12) left = x - pw - 18;
    if (top  + ph > vh - 12) top  = vh - ph - 12;
    if (top < 8) top = 8; if (left < 8) left = 8;
    popup.style.left = left + 'px'; popup.style.top = top + 'px';
  },

  async open(domain, x, y) {
    const popup = document.getElementById('tpPopup');
    this._curDomain = domain;
    TpDetail.close();

    document.getElementById('tppDot').style.background = '#e02020';
    document.getElementById('tppTitle').textContent    = domain;
    document.getElementById('tppMeta').textContent     = TpCfg.sd + ' – ' + TpCfg.ed;
    document.getElementById('tppCount').textContent    = '…';

    const list = document.getElementById('tppList');
    list.innerHTML = '<div class="tpp-loading"><div class="tpp-spinner"></div><span id="tppLoadMsg">Memuat articles…</span></div>';
    popup.classList.add('visible');
    this._pos(popup, x, y);

    const key = `${TpCfg.pid}_${domain}_${TpCfg.sd}_${TpCfg.ed}`;
    try {
      if (!this._cache[key]) {
        // Show live progress updates during batch fetch
        const msgEl = document.getElementById('tppLoadMsg');
        const progressCb = (fetched, matched) => {
          if (msgEl) msgEl.textContent = `Mencari articles… (${fetched} diperiksa, ${matched} cocok)`;
        };
        this._cache[key] = await this._fetch(domain, progressCb);
      }
      this._items = this._cache[key];
      document.getElementById('tppCount').textContent = this._items.length.toLocaleString();
      this._render(list, this._items);
    } catch(err) {
      console.error('Popup fetch error:', err);
      list.innerHTML = `<div class="tpp-loading" style="color:#94a3b8;">
        <svg style="width:32px;height:32px;stroke:#e2e8f0;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        Gagal memuat articles<br><small style="font-size:10px;margin-top:4px;display:block;">${err.message}</small></div>`;
      document.getElementById('tppCount').textContent = '0';
    }
  },

  close() {
    document.getElementById('tpPopup')?.classList.remove('visible');
    TpDetail.close();
  },

  /* ── FETCH: articles for a given domain ── */
  async _fetch(domain, progressCb) {
    const cleanDomain = domain.replace(/^www\./, '').toLowerCase();
    const parts       = cleanDomain.split('.');
    const baseDomain  = parts.slice(-2).join('.');  // "jambi.antaranews.com" → "antaranews.com"
    const isBase      = (cleanDomain === baseDomain);

    /* helper: get normalized hostname of an item */
    const getHost = a => {
      const raw = (a.publisher || a.source_name || a.hostname || '').replace(/^www\./,'').toLowerCase().trim();
      if (raw) return raw;
      const url = a.url || a.link || '';
      try { return new URL(url.startsWith('http') ? url : 'https://'+url).hostname.replace(/^www\./,'').toLowerCase(); }
      catch { return ''; }
    };

    /* ── Fetch in batches until we get enough domain matches ── */
    const BATCH = 500;
    let allItems   = [];
    let start      = 0;
    let maxBatches = 6;   // max 6 × 500 = 3000 items fetched

    while (maxBatches-- > 0) {
      const q    = `project_id=${TpCfg.pid}&start_date=${TpCfg.sd}&end_date=${TpCfg.ed}&rows=${BATCH}&start=${start}`;
      const ctrl = new AbortController();
      const tid  = setTimeout(() => ctrl.abort(), 30000);

      let batch = [];
      try {
        const res = await fetch(`/mk/api/news/mentions?${q}`, { signal: ctrl.signal });
        clearTimeout(tid);
        if (!res.ok) break;
        const data = await res.json();
        batch = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
      } catch(e) { clearTimeout(tid); break; }

      if (!batch.length) break;   // no more data

      /* filter: online news / doc ONLY — sama seperti cara MK membedakan media */
      batch = batch.filter(m => {
        const tc  = String(m.tcode        || '').toLowerCase();
        const mt  = String(m.media_type   || '').toLowerCase();
        const mtid= String(m.media_type_id|| '').toLowerCase();
        const id  = String(m.id || m.docid || '');
        const url = String(m.url || m.link || '').toLowerCase();

        // Positif: flag sebagai doc/online news
        if (tc   === 'berita')       return true;
        if (mt   === 'berita')       return true;
        if (mt   === 'doc')          return true;
        if (mt   === 'news')         return true;
        if (mt   === 'online')       return true;
        if (mt   === 'article')      return true;
        if (mt   === 'onlinenews')   return true;
        if (mtid === '1')            return true;   // MK: 1 = online news
        if (id.startsWith('doc-'))   return true;
        if (id.startsWith('nw-'))    return true;
        if (id.startsWith('news-'))  return true;

        // Negatif: jelas bukan online news
        if (mt === 'twit' || mt === 'twitter') return false;
        if (mt === 'fb'   || mt === 'facebook')return false;
        if (mt === 'ig'   || mt === 'instagram')return false;
        if (mt === 'yt'   || mt === 'youtube') return false;
        if (mt === 'tiktok')                   return false;
        if (mtid === '2' || mtid === '3' || mtid === '4' || mtid === '5' || mtid === '6') return false;
        if (id.startsWith('tw-') || id.startsWith('fb-') || id.startsWith('in-') || id.startsWith('yt-')) return false;
        if (url.includes('twitter.com') || url.includes('x.com') || url.includes('facebook.com') ||
            url.includes('instagram.com') || url.includes('youtube.com') || url.includes('tiktok.com')) return false;

        // Default: anggap doc jika tidak ada info media_type sama sekali
        return !mt || mt === '';
      });

      allItems = allItems.concat(batch);
      start   += BATCH;

      /* report progress */
      const exactSoFar = allItems.filter(a => getHost(a) === cleanDomain);
      if (progressCb) progressCb(allItems.length, exactSoFar.length);

      /* stop early if we have plenty */
      if (exactSoFar.length >= 100) break;

      if (batch.length < BATCH) break;   // last page
    }

    /* ── Step 1: EXACT match ── */
    const exactMatches = allItems.filter(a => getHost(a) === cleanDomain);
    if (exactMatches.length > 0) {
      exactMatches.forEach(a => a._matchType = 'exact');
      return exactMatches;
    }

    /* ── Step 2: subdomain fallback (only when clicking base domain) ── */
    if (isBase) {
      const subMatches = allItems.filter(a => {
        const h = getHost(a);
        return h === baseDomain || h.endsWith('.' + baseDomain);
      });
      if (subMatches.length > 0) {
        subMatches.forEach(a => a._matchType = 'subdomain');
        return subMatches;
      }
    }

    /* ── Step 3: URL keyword fallback ── */
    const urlMatches = allItems.filter(a =>
      (a.url || a.link || '').toLowerCase().includes(baseDomain)
    );
    urlMatches.forEach(a => a._matchType = 'url');
    return urlMatches;
  },

  exportCsv() {
    if (!this._items?.length) { alert('Tidak ada data untuk diekspor.'); return; }
    const rows = this._items.map((a, i) => {
      const e2 = s => String(s||'').replace(/;/g,',').replace(/\n/g,' ').replace(/\r/g,'');
      const sentRaw = String(a.class_sentiment||a.sentiment||'0').toLowerCase();
      const sent = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif' ? 'Positif'
                 : sentRaw==='-1'||sentRaw==='negative'||sentRaw==='negatif' ? 'Negatif' : 'Netral';
      return `${i};${e2(a.publisher||this._curDomain)};${e2(a.title||'')};${sent};${parseInt(a.num_views||a.views||0)||0};${parseInt(a.num_share||a.shares||0)||0};${parseInt(a.num_comments||0)||0};${e2((a.date_created||'').split('T')[0])};${e2(a.url||'')}`;
    });
    const csv  = ['index;publisher;judul;sentimen;views;share;komentar;tanggal;url', ...rows].join('\r\n');
    const blob = new Blob(['\uFEFF'+csv], { type:'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = `articles_${this._curDomain}_${TpCfg.sd}_${TpCfg.ed}.csv`;
    a.click(); URL.revokeObjectURL(url);
  },

  _render(list, items) {
    if (!items.length) {
      list.innerHTML = `<div class="tpp-loading" style="color:#94a3b8;gap:8px;">
        <svg style="width:28px;height:28px;stroke:#cbd5e1;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        <span>Tidak ada artikel untuk <b>${esc(this._curDomain)}</b></span>
        <small style="font-size:10px;color:#cbd5e1;">Domain tidak ditemukan di hasil API periode ini</small>
      </div>`;
      return;
    }

    const SHOW      = 60;
    const domain    = this._curDomain;
    const fav       = `https://www.google.com/s2/favicons?sz=64&domain=${domain}`;
    const isSubdom  = items[0]?._matchType === 'subdomain';

    // Show info banner if results are from subdomains
    let bannerHtml = '';
    if (isSubdom) {
      const uniqueSubs = [...new Set(items.map(a => {
        const raw = (a.publisher || a.source_name || a.hostname || '').replace(/^www\./,'').toLowerCase().trim();
        return raw || domain;
      }))].slice(0,5);
      bannerHtml = `<div style="padding:8px 14px;background:#fffbeb;border-bottom:1px solid #fde68a;font-size:11px;font-weight:600;color:#92400e;display:flex;align-items:center;gap:6px;">
        <svg style="width:12px;height:12px;stroke:currentColor;fill:none;flex-shrink:0;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Menampilkan artikel dari seluruh subdomain ${esc(domain)} (${uniqueSubs.map(s=>`<b>${esc(s)}</b>`).join(', ')}${uniqueSubs.length < items.length ? ', …' : ''})
      </div>`;
    }

    list.insertAdjacentHTML('afterbegin', bannerHtml);

    list.innerHTML = bannerHtml + items.slice(0, SHOW).map(a => {
      // Show actual subdomain as publisher, not the clicked domain
      const pub     = (a.publisher || a.source_name || a.hostname || domain).replace(/^www\./,'').trim();
      const ini     = (pub[0]||'N').toUpperCase();
      const favHost = pub.includes('.') ? pub : domain;
      const title   = (a.title || '').trim();
      const content = (a.content || a.description || a.summary || '').replace(/<[^>]*>/g,'').trim().slice(0,140);
      const text    = content || title.slice(0,140);
      const sentRaw = String(a.class_sentiment||a.sentiment||'0').toLowerCase();
      const sent    = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif' ? 'pos'
                    : sentRaw==='-1'||sentRaw==='negative'||sentRaw==='negatif' ? 'neg' : 'neu';
      const sentLbl = sent==='pos' ? 'Pos' : sent==='neg' ? 'Neg' : 'Neu';
      const dt      = (a.date_created || a.publish_date || '').split('T')[0];
      const views   = parseInt(a.num_views||a.views||0)||0;
      const share   = parseInt(a.num_share||a.shares||0)||0;
      const comm    = parseInt(a.num_comments||0)||0;
      const engParts= [];
      if (views>0) engParts.push('Views '+views.toLocaleString('id-ID'));
      if (share>0) engParts.push('Share '+share.toLocaleString('id-ID'));
      if (comm >0) engParts.push('Komentar '+comm.toLocaleString('id-ID'));
      const eng  = engParts.join(' · ');
      const url  = a.url || a.link || '';
      const safeItem = esc(JSON.stringify(a));

      return `<div class="tpp-item" data-item='${safeItem}' onclick="TpPopup._onItemClick(this)">
        <div class="tpp-avatar">
          <img src="https://www.google.com/s2/favicons?sz=64&domain=${favHost}" onerror="this.style.display='none';this.parentElement.textContent='${ini}';">
        </div>
        <div class="tpp-item-body">
          <div class="tpp-item-pub">${esc(pub)}</div>
          <div class="tpp-item-url">${esc(domain)}</div>
          <div class="tpp-item-text">${esc(title || '(no title)')}</div>
          <div class="tpp-item-foot">
            <span class="tpp-sent tpp-sent--${sent}">${sentLbl}</span>
            ${eng  ? `<span>${esc(eng)}</span>` : ''}
            ${dt   ? `<span style="margin-left:auto;">${dt}</span>` : ''}
            ${url  ? `<a href="${esc(url)}" target="_blank" rel="noopener" style="color:var(--green);font-weight:700;font-size:10px;text-decoration:none;" onclick="event.stopPropagation()">Buka ↗</a>` : ''}
          </div>
        </div>
      </div>`;
    }).join('');

    if (items.length > SHOW) {
      list.insertAdjacentHTML('beforeend',
        `<div style="padding:9px 14px;text-align:center;font-size:11px;font-weight:600;color:var(--text-lo);background:var(--bg);border-top:1px dashed var(--border);">+${(items.length-SHOW).toLocaleString()} artikel lainnya</div>`);
    }
  },

  _onItemClick(el) {
    try {
      const raw  = el.getAttribute('data-item');
      const item = JSON.parse(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"'));
      TpDetail.open(item, this._curDomain);
    } catch(e) { console.warn('Detail parse error:', e); }
  }
};

/* ════════════════════════════════════════
   ARTICLE DETAIL PANEL
════════════════════════════════════════ */
const TpDetail = {
  open(item, domain) {
    const panel = document.getElementById('tpDetailPanel');
    const body  = document.getElementById('tpdpBody');
    const title = document.getElementById('tpdpTitle');

    const pub      = (item.publisher || item.source_name || item.hostname || domain).trim();
    const artTitle = (item.title || 'Article').trim();
    const content  = (item.content || item.description || item.summary || '').replace(/<[^>]*>/g,'').trim();
    const url      = item.url || item.link || '';
    const date     = item.date_created || item.publish_date || '';
    const fav      = `https://www.google.com/s2/favicons?sz=64&domain=${domain}`;
    const ini      = (domain[0]||'N').toUpperCase();

    title.textContent = artTitle;

    let dtFmt = '';
    if (date) {
      try { dtFmt = new Date(date).toLocaleDateString('id-ID', { weekday:'long', day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit' }); }
      catch(e) { dtFmt = date.split('T')[0]; }
    }

    const sentRaw = String(item.class_sentiment||item.sentiment||'0').toLowerCase();
    const sent    = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif' ? 'pos'
                  : sentRaw==='-1'||sentRaw==='negative'||sentRaw==='negatif' ? 'neg' : 'neu';
    const sentLbl = sent==='pos' ? 'Positive' : sent==='neg' ? 'Negative' : 'Neutral';

    const views = parseInt(item.num_views||item.views||0)||0;
    const share = parseInt(item.num_share||item.shares||0)||0;
    const comm  = parseInt(item.num_comments||0)||0;
    const statsHtml = (views > 0 || share > 0 || comm > 0)
      ? `<div class="tpdp-stats-grid">
           <div class="tpdp-stat-box"><div class="tpdp-stat-val">${views.toLocaleString('id-ID')}</div><div class="tpdp-stat-lbl">Views</div></div>
           <div class="tpdp-stat-box"><div class="tpdp-stat-val">${share.toLocaleString('id-ID')}</div><div class="tpdp-stat-lbl">Share</div></div>
           <div class="tpdp-stat-box"><div class="tpdp-stat-val">${comm.toLocaleString('id-ID')}</div><div class="tpdp-stat-lbl">Komentar</div></div>
         </div>` : '';

    const imgUrl  = item.image_url || item.thumbnail || item.media_url || '';
    const imgHtml = imgUrl ? `<div style="border-radius:10px;overflow:hidden;margin-bottom:12px;background:#000;"><img style="width:100%;max-height:200px;object-fit:cover;display:block;" src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'"></div>` : '';

    body.innerHTML = `
      <div class="tpdp-avatar-row">
        <div class="tpdp-avatar-lg">
          <img src="${fav}" onerror="this.style.display='none';this.parentElement.textContent='${ini}';">
        </div>
        <div>
          <div class="tpdp-pub-name">${esc(pub)}</div>
          <div class="tpdp-pub-url">${esc(domain)}</div>
          <span style="background:rgba(224,32,32,.1);color:#e02020;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;margin-top:4px;">Online News</span>
        </div>
      </div>
      ${dtFmt ? `<div class="tpdp-meta-row"><span>${dtFmt}</span></div>` : ''}
      <span class="tpdp-sent-badge tpdp-sent-badge--${sent}">${sentLbl}</span>
      ${imgHtml}
      <div class="tpdp-ttl">${esc(artTitle)}</div>
      ${content ? `<div class="tpdp-content">${esc(content)}</div>` : ''}
      ${statsHtml}
      ${url ? `<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="tpdp-link-btn">
        <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Buka Artikel Asli
      </a>` : ''}
    `;
    panel.classList.add('visible');
  },
  close() { document.getElementById('tpDetailPanel')?.classList.remove('visible'); }
};

/* ════════════════════════════════════════
   FILTER SEARCH
════════════════════════════════════════ */
function tpFilter(term) {
  _filtered = term.trim()
    ? _allData.filter(p => (p.domain||'').toLowerCase().includes(term.toLowerCase()))
    : _allData;
  renderCharts(_filtered);
}

/* ════════════════════════════════════════
   EXPORT CSV
════════════════════════════════════════ */
function tpExportCSV() {
  if (!_filtered.length) { alert('No data to export'); return; }
  const rows = ['index;domain;articles;mentions', ..._filtered.map((p,i) => `${i+1};${p.domain||''};${p.count||0};${p.mentions||0}`)];
  const csv  = rows.join('\r\n');
  navigator.clipboard.writeText(csv).then(() => alert('CSV copied!')).catch(() => {
    const ta = document.createElement('textarea'); ta.value = csv;
    ta.style.cssText = 'position:fixed;opacity:0;'; document.body.appendChild(ta);
    ta.select(); document.execCommand('copy'); document.body.removeChild(ta); alert('CSV copied!');
  });
}

/* ════════════════════════════════════════
   MISC UTILS
════════════════════════════════════════ */
function shortDomain(d) { return (d||'').replace(/^www\./, ''); }
function tpToggleType(btn) {
  document.querySelectorAll('.tp-filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}
function esc(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

/* ════════════════════════════════════════
   DATE PICKER
════════════════════════════════════════ */
const TpDp = (() => {
  let ds=null, de=null, m1=new Date(), m2=new Date(), pickStart=true;
  const MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD=['Su','Mo','Tu','We','Th','Fr','Sa'];

  function init() {
    const si=document.getElementById('hSD'), ei=document.getElementById('hED');
    ds = si?.value ? new Date(si.value) : (() => { const d=new Date(); d.setDate(d.getDate()-6); return d; })();
    de = ei?.value ? new Date(ei.value) : new Date();
    m1=new Date(ds); m2=new Date(ds); m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('tpDpTrigger')?.addEventListener('click', open);
    document.querySelectorAll('.tp-dp-preset').forEach(b => b.addEventListener('click', onPreset));
    document.addEventListener('keydown', e => { if(e.key==='Escape') close(); });
  }

  function open()  { document.getElementById('tpDpModal').classList.add('show'); render(); }
  function close() { document.getElementById('tpDpModal').classList.remove('show'); }

  function apply() {
    const s=fmt(ds), e=fmt(de);
    document.getElementById('hSD').value = s;
    document.getElementById('hED').value = e;
    document.getElementById('tpDpLabel').textContent = s + ' to ' + e;
    close();
  }

  function nav(dir) { m1.setMonth(m1.getMonth()+dir); m2.setMonth(m2.getMonth()+dir); render(); }

  function onPreset(e) {
    document.querySelectorAll('.tp-dp-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    const today=new Date(); today.setHours(0,0,0,0);
    switch(e.target.dataset.p) {
      case 'today':     ds=new Date(today); de=new Date(today); break;
      case 'yesterday': ds=new Date(today); ds.setDate(today.getDate()-1); de=new Date(ds); break;
      case 'last7':     de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-6); break;
      case 'last30':    de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-29); break;
      case 'thismonth': ds=new Date(today.getFullYear(),today.getMonth(),1); de=new Date(today); break;
      case 'lastmonth': ds=new Date(today.getFullYear(),today.getMonth()-1,1); de=new Date(today.getFullYear(),today.getMonth(),0); break;
    }
    if(e.target.dataset.p!=='custom') { m1=new Date(ds); m2=new Date(ds); m2.setMonth(m2.getMonth()+1); }
    updDisp(); render();
  }

  function render() { renderCal('tpCal1',m1); renderCal('tpCal2',m2); updDisp(); }

  function renderCal(id,month) {
    const el=document.getElementById(id); if(!el) return;
    const y=month.getFullYear(), mn=month.getMonth();
    const first=new Date(y,mn,1), last=new Date(y,mn+1,0), prevL=new Date(y,mn,0);
    const today=new Date(); today.setHours(0,0,0,0);
    let h=`<div class="tp-dp-month-title">${MN[mn]} ${y}</div>
      <div class="tp-dp-weekdays">${WD.map(d=>`<div class="tp-dp-wd">${d}</div>`).join('')}</div>
      <div class="tp-dp-days">`;
    for(let i=0;i<first.getDay();i++)
      h+=`<button class="tp-dp-day other" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++) {
      const date=new Date(y,mn,d); date.setHours(0,0,0,0);
      let cls='tp-dp-day';
      if(sameD(date,today)) cls+=' today';
      if(date>today) cls+=' dis';
      if(ds&&de) {
        if(sameD(date,ds)||sameD(date,de)) cls+=' sel';
        else if(date>ds&&date<de) cls+=' rng';
      }
      h+=`<button class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h+=`<button class="tp-dp-day other" disabled>${i}</button>`;
    h+='</div>';
    el.innerHTML=h;
    el.querySelectorAll('.tp-dp-day:not(.other):not(.dis)').forEach(btn => {
      btn.addEventListener('click', function() {
        const d=new Date(this.dataset.date); d.setHours(0,0,0,0);
        document.querySelectorAll('.tp-dp-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-p="custom"]')?.classList.add('active');
        if(pickStart||d<ds) { ds=d; de=d; pickStart=false; }
        else { if(d>=ds) de=d; else { de=ds; ds=d; } pickStart=true; }
        updDisp(); render();
      });
    });
  }

  function updDisp() {
    const el=document.getElementById('tpDpRange');
    if(el&&ds&&de) el.textContent=fmt(ds)+' – '+fmt(de);
  }
  function fmt(d) { return d?`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`:''; }
  function sameD(a,b) { return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }

  return { init, open, close, apply, nav };
})();

/* ════════════════════════════════════════
   INIT
════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  TpDp.init();
  TpPopup.init();
  if (TpCfg.pid) loadData();
});
</script>
@endsection