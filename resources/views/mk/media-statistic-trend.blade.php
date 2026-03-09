@extends('mk.layouts.app')

@section('title', 'Trend Mentions — SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
    --primary-green-light: rgba(3,128,71,0.08);
    --primary-green-border: rgba(3,128,71,0.2);
    --blue:           #0284c7;
    --fb-color:       #1877f2;
    --ig-color:       #e1306c;
    --yt-color:       #ff0000;
    --tt-color:       #111827;
    --twitter-color:  #1d9bf0;
    --text-primary:   #1a202c;
    --text-secondary: #64748b;
    --text-muted:     #94a3b8;
    --bg-white:       #ffffff;
    --bg-body:        #f0f4f8;
    --bg-gray-50:     #f8fafc;
    --bg-gray-100:    #f1f5f9;
    --border-gray:    #e2e8f0;
    --border-light:   #f1f5f9;
    --shadow-xs:      0 1px 2px rgba(0,0,0,.05);
    --shadow-sm:      0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:      0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg:      0 10px 15px -3px rgba(0,0,0,.1);
    --shadow-xl:      0 20px 40px -8px rgba(0,0,0,.18);
    --radius:         16px;
    --radius-sm:      12px;
    --radius-xs:      8px;
    --transition:     all 0.2s cubic-bezier(0.4,0,0.2,1);
    --font:           'Poppins', -apple-system, sans-serif;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: var(--font); background: var(--bg-body); color: var(--text-primary); }

  .tr-page {
    padding: 24px;
    max-width: 1600px;
    margin: 0 auto;
    min-height: 100vh;
    background: var(--bg-body);
  }

  /* PAGE HEADER */
  .page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
  }
  .page-header-left h1 {
    font-size: 28px; font-weight: 700; color: var(--text-primary);
    margin: 0 0 6px 0; letter-spacing: -0.4px;
  }
  .page-header-left p { font-size: 14px; color: var(--text-secondary); font-weight: 500; margin: 0; }

  .tr-back-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 20px;
    background: var(--bg-white);
    color: var(--text-secondary);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius-sm);
    font-family: var(--font); font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
  }
  .tr-back-btn:hover {
    border-color: var(--primary-green); color: var(--primary-green);
    transform: translateY(-2px); box-shadow: var(--shadow-md);
  }
  .tr-back-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

  /* FILTER CARD */
  .filter-card {
    background: var(--bg-white); border-radius: var(--radius);
    padding: 20px 24px; margin-bottom: 24px;
    box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray);
  }
  .filter-content { display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
  .filter-group { display: flex; flex-direction: column; gap: 6px; }
  .filter-label { font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .5px; }
  .filter-select {
    padding: 10px 14px; border: 1px solid var(--border-gray); border-radius: var(--radius-sm);
    font-family: var(--font); font-size: 14px; font-weight: 500; color: var(--text-primary);
    background: var(--bg-gray-50); outline: none; transition: var(--transition); min-width: 200px; cursor: pointer;
  }
  .filter-select:focus { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px var(--primary-green-light); }
  .date-picker-trigger {
    display: flex; align-items: center; gap: 10px; padding: 10px 16px;
    background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: var(--radius-sm);
    font-family: var(--font); font-size: 14px; font-weight: 500; color: var(--text-primary);
    cursor: pointer; transition: var(--transition); min-width: 300px;
  }
  .date-picker-trigger:hover { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px var(--primary-green-light); }
  .date-picker-trigger svg { width: 16px; height: 16px; color: var(--text-secondary); flex-shrink: 0; }
  .date-picker-trigger span { flex: 1; text-align: left; }
  .apply-btn {
    display: flex; align-items: center; gap: 8px; padding: 10px 24px;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-family: var(--font); font-size: 14px; font-weight: 600;
    cursor: pointer; transition: var(--transition);
    box-shadow: 0 4px 12px rgba(3,128,71,.2); white-space: nowrap;
  }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.3); }
  .apply-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; }

  /* DATE PICKER MODAL */
  .date-picker-modal { position: fixed; inset: 0; z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,.5); backdrop-filter: blur(8px); }
  .date-picker-modal.show { display: flex; }
  .date-picker-overlay { position: absolute; inset: 0; cursor: pointer; }
  .date-picker-container { position: relative; z-index: 1; background: #fff; border-radius: var(--radius); box-shadow: 0 25px 50px rgba(0,0,0,.3); display: flex; max-width: 900px; width: 90%; max-height: 90vh; animation: dpUp .3s ease-out; }
  @keyframes dpUp { from { opacity: 0; transform: translateY(20px) scale(.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
  .date-picker-sidebar { width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray); padding: 16px 12px; border-radius: var(--radius) 0 0 var(--radius); display: flex; flex-direction: column; gap: 4px; flex-shrink: 0; }
  .date-preset { padding: 10px 16px; background: transparent; border: none; border-radius: var(--radius-xs); font-family: var(--font); font-size: 13px; font-weight: 500; color: var(--text-primary); text-align: left; cursor: pointer; transition: var(--transition); }
  .date-preset:hover  { background: var(--bg-white); color: var(--primary-green); }
  .date-preset.active { background: var(--primary-green); color: #fff; }
  .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; overflow: hidden; }
  .date-picker-header { display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px; }
  .nav-btn { width: 36px; height: 36px; border-radius: var(--radius-xs); background: var(--bg-gray-50); border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: var(--transition); flex-shrink: 0; }
  .nav-btn:hover { background: var(--primary-green); border-color: var(--primary-green); color: #fff; }
  .nav-btn svg { width: 20px; height: 20px; }
  .calendars-wrapper { display: flex; gap: 24px; flex: 1; }
  .calendar { flex: 1; display: flex; flex-direction: column; }
  .calendar-month { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-align: center; }
  .calendar-weekdays { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; margin-bottom: 8px; }
  .weekday { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
  .calendar-days { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; }
  .calendar-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 500; border-radius: var(--radius-xs); cursor: pointer; transition: var(--transition); color: var(--text-primary); background: transparent; border: none; padding: 0; font-family: var(--font); }
  .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
  .calendar-day.other-month { color: #cbd5e1; cursor: default; }
  .calendar-day.disabled    { color: #e2e8f0; cursor: not-allowed; }
  .calendar-day.today       { border: 2px solid var(--primary-green); }
  .calendar-day.selected    { background: var(--primary-green); color: #fff; }
  .calendar-day.in-range    { background: var(--primary-green-light); color: var(--primary-green); }
  .date-picker-display { padding: 16px 20px; background: var(--bg-gray-50); border-radius: var(--radius-sm); text-align: center; margin-bottom: 20px; border: 1px solid var(--border-gray); }
  .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }
  .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }
  .cancel-btn, .apply-date-btn { padding: 10px 24px; border-radius: 10px; font-family: var(--font); font-size: 14px; font-weight: 600; cursor: pointer; transition: var(--transition); border: none; }
  .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
  .cancel-btn:hover { background: var(--border-gray); }
  .apply-date-btn { background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); color: #fff; box-shadow: 0 4px 12px rgba(3,128,71,.2); }
  .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.3); }

  /* SECTION HEADER */
  .ms-section-header { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; margin-top: 4px; }
  .ms-section-icon { width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--primary-green-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .ms-section-icon svg { width: 18px; height: 18px; stroke: var(--primary-green); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .ms-section-title { font-size: 13px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .8px; }
  .ms-section-line { flex: 1; height: 1.5px; background: var(--border-gray); border-radius: 1px; }

  /* DO-CARD */
  .do-card { background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: var(--radius); overflow: hidden; display: flex; flex-direction: column; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; }
  .do-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); opacity: 0; transition: opacity .3s; }
  .do-card:hover { box-shadow: var(--shadow-lg); border-color: var(--primary-green-border); }
  .do-card:hover::before { opacity: 1; }
  .do-card-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border-gray); flex-shrink: 0; }
  .do-card-head-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
  .do-head-icon { width: 40px; height: 40px; border-radius: var(--radius-sm); background: linear-gradient(135deg, var(--primary-green-light) 0%, rgba(3,128,71,.05) 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .do-head-icon svg { width: 20px; height: 20px; fill: none; stroke: var(--primary-green); stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .do-card-title { font-size: 16px; font-weight: 700; color: var(--text-primary); line-height: 1.3; }
  .do-card-subtitle { font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 2px; }
  .do-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; background: var(--bg-gray-100); color: var(--text-secondary); white-space: nowrap; flex-shrink: 0; }
  .do-card-body { padding: 20px; flex: 1; }

  /* TAB SYSTEM */
  .ms-tabs { display: flex; gap: 4px; background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: var(--radius); padding: 6px; margin-bottom: 24px; box-shadow: var(--shadow-sm); }
  .ms-tab-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 20px; border-radius: var(--radius-sm); border: none; background: transparent; font-family: var(--font); font-size: 13px; font-weight: 600; color: var(--text-secondary); cursor: pointer; transition: var(--transition); }
  .ms-tab-btn:hover { background: var(--bg-gray-50); color: var(--text-primary); }
  .ms-tab-btn.active { background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); color: #fff; box-shadow: 0 4px 12px rgba(3,128,71,.25); }
  .ms-tab-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
  .ms-tab-panel { display: none; }
  .ms-tab-panel.active { display: block; }

  /* CHART HEIGHT HELPERS */
  .ms-ch-280 { position: relative; height: 280px; }
  .ms-ch-340 { position: relative; height: 340px; }

  /* GRID */
  .ms-grid-2        { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.ms-pola-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 24px;
  margin-bottom: 20px;
  align-items: start; /* ← THE CRITICAL FIX: top-aligns both cards */
}
.ms-pola-grid > .do-card {
  display: flex;
  flex-direction: column;
}
.ms-pola-grid > .do-card .do-card-body {
  flex: 1;
  padding: 20px;
}
.ms-pola-grid > .do-card .ms-ch-280 {
  position: relative;
  height: 280px; /* explicit height required for ECharts to render */
  width: 100%;
}

@media (max-width: 1024px) {
  .ms-pola-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 768px) {
  .ms-pola-grid { grid-template-columns: 1fr; }
}
  .ms-mb20 { margin-bottom: 20px; }

  /* SKELETON */
  .loading-skeleton { background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite; border-radius: var(--radius-xs); }
  @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
  .skel-overlay { position: absolute; inset: 0; z-index: 3; border-radius: inherit; }

  /* EMPTY STATE */
  .do-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 44px 20px; gap: 10px; }
  .do-empty svg { width: 40px; height: 40px; stroke: var(--border-gray); fill: none; stroke-width: 1.5; }
  .do-empty-text { font-size: 13px; font-weight: 600; color: var(--text-secondary); }

  /* SUMMARY CARDS */
  .tr-sum-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; margin-bottom: 22px; }
  .tr-sum-card { background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: var(--radius-sm); padding: 16px 18px; box-shadow: var(--shadow-sm); transition: var(--transition); overflow: hidden; position: relative; }
  .tr-sum-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--pc, var(--primary-green)); transform: scaleX(0); transform-origin: left; transition: transform .3s ease; }
  .tr-sum-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
  .tr-sum-card:hover::after { transform: scaleX(1); }
  .tr-sum-label { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; display: flex; align-items: center; gap: 6px; margin-bottom: 10px; }
  .tr-sum-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--pc); flex-shrink: 0; }
  .tr-sum-val { font-size: 24px; font-weight: 800; letter-spacing: -.5px; color: var(--text-primary); line-height: 1; margin-bottom: 4px; min-height: 28px; }
  .tr-sum-val.skel { width: 90px; height: 28px; border-radius: 6px; background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite; }
  .tr-sum-sub { font-size: 11px; color: var(--text-muted); font-weight: 500; }

  /* CSV BUTTON */
  .tr-csv-btn { display: flex; align-items: center; gap: 6px; padding: 7px 16px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: var(--radius-xs); font-family: var(--font); font-size: 12px; font-weight: 700; color: var(--text-secondary); cursor: pointer; transition: var(--transition); }
  .tr-csv-btn:hover { border-color: var(--primary-green); color: var(--primary-green); }
  .tr-csv-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2.5; }

  /* PLATFORM PILLS */
  .tr-pills { display: flex; gap: 7px; flex-wrap: wrap; padding: 12px 20px; border-bottom: 1px solid var(--border-light); background: var(--bg-gray-50); }
  .tr-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 20px; font-family: var(--font); font-size: 12px; font-weight: 600; cursor: pointer; transition: var(--transition); border: 1.5px solid var(--border-gray); background: var(--bg-white); color: var(--text-secondary); }
  .tr-pill:hover { border-color: var(--pc); color: var(--pc); }
  .tr-pill.on { background: var(--pc); border-color: var(--pc); color: #fff; }
  .tr-pill-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--pc); flex-shrink: 0; }
  .tr-pill.on .tr-pill-dot { background: rgba(255,255,255,.7); }

  /* META BAR */
  .tr-meta { display: none; align-items: center; gap: 18px; flex-wrap: wrap; padding: 10px 22px; background: #f0fdf4; border-top: 1px solid #bbf7d0; font-size: 11.5px; font-weight: 600; color: #065f46; }
  .tr-meta.show { display: flex; }

  @media (max-width: 1280px) {
    .tr-sum-grid { grid-template-columns: repeat(3, 1fr); }
    .ms-grid-2   { grid-template-columns: 1fr; }
  }
  @media (max-width: 768px) {
    .tr-page { padding: 16px; }
    .tr-sum-grid { grid-template-columns: repeat(2, 1fr); }
    .ms-tab-btn { font-size: 12px; padding: 10px 12px; }
    .date-picker-container { flex-direction: column; width: 96%; }
    .date-picker-sidebar { width: 100%; flex-direction: row; overflow-x: auto; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: var(--radius) var(--radius) 0 0; flex-shrink: 0; }
    .calendars-wrapper { flex-direction: column; }
  }
</style>
@endsection

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
  $projects  = $projects ?? [];

  $platDefs = [
    ['key' => 'doc',       'label' => 'Online News', 'color' => '#038047'],
    ['key' => 'twitter',   'label' => 'Twitter',     'color' => '#1d9bf0'],
    ['key' => 'facebook',  'label' => 'Facebook',    'color' => '#1877f2'],
    ['key' => 'instagram', 'label' => 'Instagram',   'color' => '#e1306c'],
    ['key' => 'youtube',   'label' => 'YouTube',     'color' => '#ff0000'],
    ['key' => 'tiktok',    'label' => 'TikTok',      'color' => '#2dd4bf'],
  ];
@endphp

<div class="tr-page">

  {{-- PAGE HEADER --}}
  <div class="page-header">
    <div class="page-header-left">
      <h1>Analitik Lanjutan</h1>
      <p>Trend mentions harian per platform & pola waktu posting</p>
    </div>
    <a href="{{ route('mk.media-statistic') }}{{ $projectId ? '?project_id='.$projectId.'&start_date='.$startDate.'&end_date='.$endDate : '' }}"
       class="tr-back-btn">
      <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      Kembali ke Media Statistic
    </a>
  </div>

  {{-- FILTER CARD --}}
  <div class="filter-card">
    <form id="trFilterForm" method="GET" action="{{ route('mk.media-statistic.trend') }}">
      <input type="hidden" name="project_id" id="hPid" value="{{ $projectId }}">
      <input type="hidden" name="start_date"  id="hSD"  value="{{ $startDate }}">
      <input type="hidden" name="end_date"    id="hED"  value="{{ $endDate }}">

      <div class="filter-content">
        @if(count($projects))
        <div class="filter-group">
          <label class="filter-label">Project</label>
          <select class="filter-select" id="trProjSel"
            onchange="document.getElementById('hPid').value=this.value">
            @foreach($projects as $p)
            <option value="{{ $p['id'] }}" {{ ($p['id'] == $projectId) ? 'selected' : '' }}>
              {{ $p['name'] ?? $p['title'] ?? 'Project #' . $p['id'] }}
            </option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="filter-group">
          <label class="filter-label">Periode</label>
          <button type="button" class="date-picker-trigger" id="trDpTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="trDpDisplay">{{ $startDate }} – {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
        </div>

        <div class="filter-group" style="margin-left:auto;">
          <label class="filter-label" style="opacity:0;pointer-events:none;">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Filter
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- DATE PICKER MODAL --}}
  <div class="date-picker-modal" id="trDpModal" aria-modal="true" role="dialog">
    <div class="date-picker-overlay" onclick="TrDp.close()"></div>
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
          <button class="nav-btn" onclick="TrDp.nav(-1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="15 18 9 12 15 6"/>
            </svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="trDpCal1"></div>
            <div class="calendar" id="trDpCal2"></div>
          </div>
          <button class="nav-btn" onclick="TrDp.nav(1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </button>
        </div>
        <div class="date-picker-display">
          <span id="trDpRangeText">{{ $startDate }} – {{ $endDate }}</span>
        </div>
        <div class="date-picker-footer">
          <button class="cancel-btn" onclick="TrDp.close()">Batal</button>
          <button class="apply-date-btn" onclick="TrDp.apply()">Terapkan</button>
        </div>
      </div>
    </div>
  </div>

  {{-- SECTION HEADER --}}
  <div class="ms-section-header">
    <div class="ms-section-icon">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <span class="ms-section-title">Analitik Lanjutan</span>
    <div class="ms-section-line"></div>
  </div>

  {{-- SUMMARY CARDS --}}
  <div class="tr-sum-grid">
    @foreach($platDefs as $pd)
    <div class="tr-sum-card" id="sum_{{ $pd['key'] }}" style="--pc:{{ $pd['color'] }}">
      <div class="tr-sum-label">
        <span class="tr-sum-dot"></span>
        {{ $pd['label'] }}
      </div>
      <div class="tr-sum-val skel" id="sumV_{{ $pd['key'] }}"></div>
      <div class="tr-sum-sub" id="sumS_{{ $pd['key'] }}">Memuat…</div>
    </div>
    @endforeach
  </div>

  {{-- TAB BUTTONS --}}
  <div class="ms-tabs">
    <button class="ms-tab-btn active" id="tabBtnTrend" onclick="TrTab.show('trend')">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Trend Mentions
    </button>
    <button class="ms-tab-btn" id="tabBtnPola" onclick="TrTab.show('pola')">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Pola Waktu Posting
    </button>
  </div>

  {{-- TAB PANEL: TREND MENTIONS --}}
  <div class="ms-tab-panel active" id="panelTrend">
    <div class="do-card ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </span>
          <div>
            <div class="do-card-title">The Trends of Total Mentions by Media Types</div>
            <div class="do-card-subtitle" id="trendSubtitle">The trends of total mentions by media types</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
          <span class="do-badge" id="trendBadge">Loading…</span>
          <button class="tr-csv-btn" onclick="TrPage.exportCsv()">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
          </button>
        </div>
      </div>

      {{-- Platform Pills --}}
      <div class="tr-pills">
        <button class="tr-pill on" data-k="all" style="--pc:var(--primary-green)" onclick="TrPage.toggle('all',this)">
          <span class="tr-pill-dot" style="background:var(--primary-green)"></span>All
        </button>
        @foreach($platDefs as $pd)
        <button class="tr-pill on" data-k="{{ $pd['key'] }}" style="--pc:{{ $pd['color'] }}" onclick="TrPage.toggle('{{ $pd['key'] }}',this)">
          <span class="tr-pill-dot" style="background:{{ $pd['color'] }}"></span>{{ $pd['label'] }}
        </button>
        @endforeach
      </div>

      <div class="do-card-body">
        <div class="ms-ch-340" id="chTrendWrap">
          <div id="chTrend" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="skTrend"></div>
        </div>
      </div>

      {{-- Meta Bar --}}
      <div class="tr-meta" id="trMeta">
        <span id="metaTotal"></span>
        <span id="metaRange"></span>
      </div>
    </div>
  </div>

  {{-- TAB PANEL: POLA WAKTU --}}
<div class="ms-tab-panel" id="panelPola">
    <div class="ms-pola-grid">
      <div class="do-card">
        <div class="do-card-head">
          <div class="do-card-head-left">
            <span class="do-head-icon">
              <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <div>
              <div class="do-card-title">Mentions by Weekday</div>
              <div class="do-card-subtitle">Total mention per hari dalam seminggu</div>
            </div>
          </div>
          <span class="do-badge">7 Hari</span>
        </div>
        <div class="do-card-body">
          <div class="ms-ch-280">
            <div id="chWeekday" style="width:100%;height:100%;"></div>
            <div class="loading-skeleton skel-overlay" id="skWeekday"></div>
          </div>
        </div>
      </div>

    <div class="do-card">
        <div class="do-card-head">
          <div class="do-card-head-left">
            <span class="do-head-icon">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <div>
              <div class="do-card-title">Mentions by Hour</div>
              <div class="do-card-subtitle">Distribusi volume mention per jam (00–23)</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            <button class="ms-csv-btn" onclick="MSCsvModal.showHour()" title="Copy CSV Data">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
              CSV
            </button>
            <span class="do-badge">24 Jam</span>
          </div>
        </div>{{-- ← THIS closing tag was missing --}}
        <div class="do-card-body">
          <div class="ms-ch-280">
            <div id="chHour" style="width:100%;height:100%;"></div>
            <div class="loading-skeleton skel-overlay" id="skHour"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>{{-- /tr-page --}}
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

const TrCfg = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
  plats: @json($platDefs),
};

const numFmt = n => parseInt(n||0).toLocaleString('id-ID');

// Dual Y-axis: Twitter on left (0), all others on right (1)
const Y_AXIS_IDX = { doc: 1, twitter: 0, facebook: 1, instagram: 1, youtube: 1, tiktok: 1 };
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const emptyHtml = msg => `<div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="do-empty-text">${msg}</span></div>`;

/* ── ECHARTS REGISTRY ── */
const TrCharts = {
  _i: {},
  make(id) {
    if (this._i[id]) { try { this._i[id].dispose(); } catch(e){} }
    const dom = document.getElementById(id);
    if (!dom) return null;
    const c = echarts.init(dom, null, { renderer: 'canvas' });
    this._i[id] = c;
    return c;
  },
};
window.addEventListener('resize', () => {
  Object.values(TrCharts._i).forEach(c => { try { if (!c.isDisposed()) c.resize(); } catch(e){} });
});

const EC_TIP = {
  backgroundColor:'#1a202c', borderColor:'#e2e8f0', borderWidth:1,
  padding:[10,14], textStyle:{ color:'#fff', fontFamily:"'Poppins',sans-serif", fontSize:13 },
  extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);'
};

/* ── DATE PICKER ── */
const TrDp = (() => {
  const MN = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD = ['Su','Mo','Tu','We','Th','Fr','Sa'];
  let ds=null, de=null, m1=new Date(), m2=new Date(), pickStart=true;
  function fmt(d) { return d ? `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}` : ''; }
  function sameD(a,b) { return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
  function init() {
    ds = new Date(TrCfg.sd); de = new Date(TrCfg.ed);
    m1 = new Date(ds); m2 = new Date(ds); m2.setMonth(m2.getMonth()+1);
    document.getElementById('trDpTrigger').addEventListener('click', open);
    document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', onPreset));
  }
  function open()  { document.getElementById('trDpModal').classList.add('show'); render(); }
  function close() { document.getElementById('trDpModal').classList.remove('show'); }
  function apply() {
    document.getElementById('hSD').value = fmt(ds);
    document.getElementById('hED').value = fmt(de);
    document.getElementById('trDpDisplay').textContent = fmt(ds) + ' – ' + fmt(de);
    close();
  }
  function nav(dir) { m1.setMonth(m1.getMonth()+dir); m2.setMonth(m2.getMonth()+dir); render(); }
  function onPreset(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    const today = new Date(); today.setHours(0,0,0,0);
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
  function render() { renderCal('trDpCal1',m1); renderCal('trDpCal2',m2); updDisp(); }
  function renderCal(id, month) {
    const el=document.getElementById(id); if(!el) return;
    const y=month.getFullYear(), mn=month.getMonth();
    const first=new Date(y,mn,1), last=new Date(y,mn+1,0), prevL=new Date(y,mn,0);
    const today=new Date(); today.setHours(0,0,0,0);
    let h=`<div class="calendar-month">${MN[mn]} ${y}</div>
      <div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div>
      <div class="calendar-days">`;
    for(let i=0;i<first.getDay();i++) h+=`<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++) {
      const date=new Date(y,mn,d); date.setHours(0,0,0,0);
      let cls='calendar-day';
      if(sameD(date,today)) cls+=' today';
      if(date>today) cls+=' disabled';
      if(ds&&de){ if(sameD(date,ds)||sameD(date,de)) cls+=' selected'; else if(date>ds&&date<de) cls+=' in-range'; }
      h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>';
    el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn=>{
      btn.addEventListener('click',function(){
        const d=new Date(this.dataset.date); d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-p="custom"]').classList.add('active');
        if(pickStart||d<ds){ds=d;de=d;pickStart=false;}
        else{if(d>=ds)de=d;else{de=ds;ds=d;}pickStart=true;}
        updDisp(); render();
      });
    });
  }
  function updDisp() { const el=document.getElementById('trDpRangeText'); if(el&&ds&&de) el.textContent=fmt(ds)+' – '+fmt(de); }
  return { init, open, close, apply, nav };
})();

/* ── TAB SYSTEM ── */
const TrTab = {
  _loaded: { trend: false, pola: false },
  show(tab) {
    document.querySelectorAll('.ms-tab-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById('tabBtn'+tab.charAt(0).toUpperCase()+tab.slice(1))?.classList.add('active');
    document.querySelectorAll('.ms-tab-panel').forEach(p=>p.classList.remove('active'));
    document.getElementById('panel'+tab.charAt(0).toUpperCase()+tab.slice(1))?.classList.add('active');
    if(tab==='trend'&&!this._loaded.trend){ this._loaded.trend=true; loadTrend(); }
    if(tab==='pola'&&!this._loaded.pola){ this._loaded.pola=true; loadWeekHour(); setTimeout(()=>{ Object.values(TrCharts._i).forEach(c=>{ try{if(!c.isDisposed())c.resize();}catch(e){} }); },50); }
  }
};

/* ── TREND DATA ── */
let _trendRaw   = [];
let _trendDates = [];
let _trendActive = new Set(TrCfg.plats.map(p=>p.key));

// ── Hitung 8 hari terakhir dari TODAY (persis seperti Drone Emprit) ──
function _getTrendDateRange() {
  const today  = new Date(); today.setHours(0,0,0,0);
  const sd     = new Date(today); sd.setDate(today.getDate() - 7); // 7 hari lalu
  const fmt    = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  return { sd: fmt(sd), ed: fmt(today) };
}

async function loadTrend() {
  if (!MSCfg.pid) { hideSk('skTrend'); return; }

  // Anchor ke TODAY — 7 hari ke belakang s/d hari ini (identik dengan trend.blade)
  const today  = new Date(); today.setHours(0,0,0,0);
  const sdDate = new Date(today); sdDate.setDate(today.getDate() - 7);
  const fmt    = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  const trendSD = fmt(sdDate);
  const trendED = fmt(today);

  const platMeta = {
    doc:       { label: 'Online News (Ind)', color: '#038047' },
    twitter:   { label: 'Twitter',           color: '#1d9bf0' },
    facebook:  { label: 'Facebook',          color: '#1877f2' },
    instagram: { label: 'Instagram',         color: '#e1306c' },
    youtube:   { label: 'YouTube',           color: '#ff0000' },
    tiktok:    { label: 'TikTok',            color: '#2dd4bf' },
  };
  const platOrder = ['doc', 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok'];

  try {
    const res  = await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${MSCfg.pid}&start_date=${trendSD}&end_date=${trendED}`);
    const json = await res.json();
    if (json.error) throw new Error(json.error);

    hideSk('skTrend');

    const raw  = json.data || [];
    const meta = json.meta || {};

    // Collect semua tanggal dari response
    const dSet = new Set();
    raw.forEach(p => (p.data || []).forEach(d => dSet.add(d.date)));
    const allDates = Array.from(dSet).sort();

    // Map ke urutan platform konsisten
    const trendRaw = platOrder.map(key => {
      const found = raw.find(p => p.key === key);
      return found || { key, label: platMeta[key]?.label || key, color: platMeta[key]?.color || '#94a3b8', data: [] };
    });

    // Badge — format "21 Feb – 28 Feb"
    const fmtB = d => {
      const dt = new Date(d + 'T00:00:00');
      return `${dt.getDate()} ${dt.toLocaleString('id-ID', { month: 'short' })}`;
    };
    document.getElementById('trendBadge').textContent = `${fmtB(trendSD)} – ${fmtB(trendED)}`;

    const trendChart = MSCharts.make('chTrend');
    if (!trendChart) return;

    const skipInterval = allDates.length > 21 ? Math.floor(allDates.length / 14) : 0;

const series = trendRaw.map(p => {
      const vals    = allDates.map(d => { const pt = p.data.find(x => x.date === d); return pt ? pt.count : 0; });
      const hasData = vals.some(v => v > 0);
      return {
        name: p.label, type: 'line', yAxisIndex: Y_AXIS_IDX[p.key] ?? 1, data: vals, smooth: 0.4,
        symbol: 'circle', symbolSize: hasData && allDates.length <= 30 ? 6 : 0, showSymbol: allDates.length <= 30,
        itemStyle: { color: p.color, borderColor: '#fff', borderWidth: 2 },
        lineStyle:  { color: p.color, width: hasData ? 2.5 : 1, opacity: hasData ? 1 : 0.15 },
        label: {
          show: hasData && allDates.length <= 14, position: 'top',
          formatter: params => params.value > 0 ? numFmt(params.value) : '',
          fontFamily: "'Poppins', sans-serif", fontWeight: '700', fontSize: 10, color: '#64748b',
        },
        emphasis: {
          focus: 'series', lineStyle: { width: 3.5 },
          itemStyle: { symbolSize: 10, borderColor: '#fff', borderWidth: 2.5, shadowBlur: 10, shadowColor: p.color + '88' }
        },
      };
    });

    const xLabels = allDates.map(d => {
      const dt = new Date(d + 'T00:00:00');
      return `${dt.getDate()}. ${dt.toLocaleString('id-ID', { month: 'short' })}`;
    });

    trendChart.setOption({
      animation: true, animationDuration: 900, animationEasing: 'cubicInOut',
      backgroundColor: '#ffffff',
      tooltip: {
        backgroundColor: '#ffffff', borderColor: '#e2e8f0', borderWidth: 1, padding: [12, 16],
        textStyle: { color: '#1a202c', fontFamily: "'Poppins', sans-serif", fontSize: 12 },
        extraCssText: 'border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.12);',
        trigger: 'axis', axisPointer: { type: 'line', lineStyle: { color: '#e2e8f0', type: 'dashed', width: 1.5 } },
        formatter: params => {
          const idx    = params[0]?.dataIndex ?? 0;
          const fullDt = new Date(allDates[idx] + 'T00:00:00')
            .toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
          const sorted = [...params].sort((a, b) => b.value - a.value);
          const rows   = sorted.filter(p => p.value > 0).map(p =>
            `<div style="display:flex;align-items:center;justify-content:space-between;gap:20px;padding:2px 0;">
               <div style="display:flex;align-items:center;gap:7px;">
                 <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0;"></span>
                 <span style="font-size:12px;color:#64748b;">${p.seriesName}</span>
               </div>
               <span style="font-size:12px;font-weight:700;color:#1a202c;">${numFmt(p.value)}</span>
             </div>`
          ).join('');
          const total = params.reduce((s, p) => s + (p.value || 0), 0);
         return `<div style="font-weight:700;font-size:12px;color:#1a202c;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #f1f5f9;">${fullDt}</div>
                  ${rows || '<div style="color:#94a3b8;font-size:12px;">Tidak ada data</div>'}
                  <div style="border-top:1px solid #f1f5f9;margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:11px;color:#94a3b8;">Total</span>
                    <span style="font-size:12px;font-weight:700;color:#1a202c;">${numFmt(total)}</span>
                  </div>
                  <div style="margin-top:5px;font-size:10px;color:#cbd5e1;display:flex;gap:10px;">
                    <span style="color:#1d9bf066;">▌ Left axis: Twitter</span>
                    <span style="color:#94a3b8;">▌ Right axis: Others</span>
                  </div>`;
        }
      },
      legend: {
        bottom: 0, type: 'scroll',
        data: trendRaw.map(p => p.label),
        textStyle: { fontFamily: "'Poppins', sans-serif", fontSize: 11, fontWeight: '600', color: '#64748b' },
        icon: 'circle', itemWidth: 10, itemHeight: 10, itemGap: 20,
      },
      grid: { top: 32, right: 72, bottom: 50, left: 64 },
      xAxis: {
        type: 'category', data: xLabels, boundaryGap: false,
        axisLine: { lineStyle: { color: '#e2e8f0' } }, axisTick: { show: false },
        axisLabel: { fontFamily: "'Poppins', sans-serif", fontSize: 11, fontWeight: '600', color: '#64748b', interval: skipInterval }
      },
      yAxis: [
        {
          // LEFT — Twitter scale
          type: 'value', position: 'left',
          name: 'Twitter', nameGap: 8,
          nameTextStyle: { color: '#1d9bf066', fontSize: 10, fontWeight: '700', fontFamily: "'Poppins', sans-serif", align: 'right' },
          axisLine: { show: true, lineStyle: { color: '#1d9bf018', width: 1 } },
          axisTick: { show: false },
          splitLine: { show: false },
          axisLabel: { fontFamily: "'Poppins', sans-serif", fontSize: 10, color: '#1d9bf0bb', formatter: numK },
        },
        {
          // RIGHT — All others scale
          type: 'value', position: 'right',
          name: 'Others', nameGap: 8,
          nameTextStyle: { color: '#94a3b8', fontSize: 10, fontWeight: '700', fontFamily: "'Poppins', sans-serif", align: 'left' },
          axisLine: { show: true, lineStyle: { color: '#e2e8f0', width: 1 } },
          axisTick: { show: false },
          splitLine: { lineStyle: { color: '#f1f5f9', type: 'solid', width: 1 } },
          axisLabel: { fontFamily: "'Poppins', sans-serif", fontSize: 10, color: '#94a3b8', formatter: numK },
        },
      ],
      series,
    }, true);

    MSPage._trendChart = trendChart;

  } catch (err) {
    hideSk('skTrend');
    console.warn('loadTrend error:', err);
    document.getElementById('trendBadge').textContent = 'Error';
    document.getElementById('chTrend').parentElement.innerHTML =
      emptyHtml('Data trend tidak tersedia');
  }
}

function _renderTrendChart() {
  const chart = TrCharts.make('chTrend');
  if (!chart) return;

  // Auto-skip label kalau terlalu banyak hari (>14 hari labelnya padat)
  const skipInterval = _trendDates.length > 21 ? Math.floor(_trendDates.length / 14) : 0;

  const series = _trendRaw.filter(p=>_trendActive.has(p.key)).map(p=>{
    const vals    = _trendDates.map(d=>{ const pt=p.data.find(x=>x.date===d); return pt?pt.count:0; });
    const hasData = vals.some(v=>v>0);
    return {
      name: p.label, type:'line', yAxisIndex: Y_AXIS_IDX[p.key] ?? 1, data:vals, smooth:0.4,
      symbol:'circle', symbolSize: hasData && _trendDates.length <= 30 ? 6 : 0, showSymbol: _trendDates.length <= 30,
      itemStyle:{ color:p.color, borderColor:'#fff', borderWidth:2 },
      lineStyle:{ color:p.color, width:hasData?2.5:1, opacity:hasData?1:0.15 },
      label:{ show: hasData && _trendDates.length <= 14, position:'top', formatter:params=>params.value>0?numFmt(params.value):'', fontFamily:"'Poppins',sans-serif", fontWeight:'700', fontSize:10, color:'#64748b' },
      emphasis:{ focus:'series', lineStyle:{width:3.5}, itemStyle:{symbolSize:10,borderColor:'#fff',borderWidth:2.5,shadowBlur:10,shadowColor:p.color+'88'} },
    };
  });

  const lbls = _trendDates.map(d=>{ const dt=new Date(d+'T00:00:00'); return `${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`; });

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'cubicInOut',
    backgroundColor:'#ffffff',
    tooltip:{
      backgroundColor:'#ffffff', borderColor:'#e2e8f0', borderWidth:1, padding:[12,16],
      textStyle:{color:'#1a202c',fontFamily:"'Poppins',sans-serif",fontSize:12},
      extraCssText:'border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.12);',
      trigger:'axis', axisPointer:{type:'line',lineStyle:{color:'#e2e8f0',type:'dashed',width:1.5}},
      formatter:params=>{
        const idx=params[0]?.dataIndex??0;
        const fullDt=new Date(_trendDates[idx]+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
        const sorted=[...params].sort((a,b)=>b.value-a.value);
        const rows=sorted.filter(p=>p.value>0).map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:20px;padding:2px 0"><div style="display:flex;align-items:center;gap:7px"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0"></span><span style="font-size:12px;color:#64748b">${p.seriesName}</span></div><span style="font-size:12px;font-weight:700;color:#1a202c">${numFmt(p.value)}</span></div>`).join('');
        const total=params.reduce((s,p)=>s+(p.value||0),0);
        return `<div style="font-weight:700;font-size:12px;color:#1a202c;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #f1f5f9">${fullDt}</div>${rows||'<div style="color:#94a3b8;font-size:12px">Tidak ada data</div>'}<div style="border-top:1px solid #f1f5f9;margin-top:6px;padding-top:6px;display:flex;justify-content:space-between"><span style="font-size:11px;color:#94a3b8">Total</span><span style="font-size:12px;font-weight:700;color:#1a202c">${numFmt(total)}</span></div>`;
      }
    },
    legend:{ bottom:0, type:'scroll', data:_trendRaw.filter(p=>_trendActive.has(p.key)).map(p=>p.label), textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}, icon:'circle', itemWidth:10, itemHeight:10, itemGap:20 },
  grid:{ top:32, right:72, bottom:50, left:64 },
    xAxis:{ type:'category', data:lbls, boundaryGap:false, axisLine:{lineStyle:{color:'#e2e8f0'}}, axisTick:{show:false}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b', interval: skipInterval} },
    yAxis:[
      { type:'value', position:'left', name:'Twitter', nameGap:8, nameTextStyle:{color:'#1d9bf066',fontSize:10,fontWeight:'700',fontFamily:"'Poppins',sans-serif",align:'right'}, axisLine:{show:true,lineStyle:{color:'#1d9bf018',width:1}}, axisTick:{show:false}, splitLine:{show:false}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#1d9bf0bb',formatter:numK} },
      { type:'value', position:'right', name:'Others', nameGap:8, nameTextStyle:{color:'#94a3b8',fontSize:10,fontWeight:'700',fontFamily:"'Poppins',sans-serif",align:'left'}, axisLine:{show:true,lineStyle:{color:'#e2e8f0',width:1}}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'solid',width:1}}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK} },
    ],
    series,
  }, true);
}

/* ── PLATFORM PILL TOGGLE ── */
const TrPage = {
  toggle(key, btn) {
    const allKeys = TrCfg.plats.map(p=>p.key);
    if(key==='all'){
      const allOn=_trendActive.size===allKeys.length;
      if(allOn){ _trendActive.clear(); document.querySelectorAll('.tr-pill[data-k]:not([data-k="all"])').forEach(b=>b.classList.remove('on')); btn.classList.add('on'); }
      else { allKeys.forEach(k=>_trendActive.add(k)); document.querySelectorAll('.tr-pill').forEach(b=>b.classList.add('on')); }
    } else {
      if(_trendActive.has(key)){ _trendActive.delete(key); btn.classList.remove('on'); }
      else { _trendActive.add(key); btn.classList.add('on'); }
      const allPill=document.querySelector('.tr-pill[data-k="all"]');
      if(allPill){ if(_trendActive.size===allKeys.length) allPill.classList.add('on'); else allPill.classList.remove('on'); }
    }
    if(_trendRaw.length) _renderTrendChart();
  },

  exportCsv() {
    if(!_trendRaw.length||!_trendDates.length){ alert('Belum ada data untuk diekspor.'); return; }
    const headers=['Tanggal',..._trendRaw.map(p=>p.label),'Total'];
    const rows=_trendDates.map(date=>{
      const vals=_trendRaw.map(p=>{ const d=p.data.find(x=>x.date===date); return d?d.count:0; });
      return [date,...vals,vals.reduce((s,v)=>s+v,0)];
    });
    const csv=[headers,...rows].map(r=>r.join(',')).join('\r\n');
    const blob=new Blob(['\uFEFF'+csv],{type:'text/csv;charset=utf-8;'});
    const url=URL.createObjectURL(blob), a=document.createElement('a');
    a.href=url; a.download=`trend_mentions_${TrCfg.pid}_${TrCfg.sd}_${TrCfg.ed}.csv`;
    a.click(); URL.revokeObjectURL(url);
  }
};

/* ── WEEKDAY & HOUR ── */
async function loadWeekHour() {
  if(!TrCfg.pid){ ['skWeekday','skHour'].forEach(hideSk); return; }
  try {
    const res  = await fetch(`/mk/api/media-statistic/mentions-by-weekday?project_id=${TrCfg.pid}&start_date=${TrCfg.sd}&end_date=${TrCfg.ed}`);
    const json = await res.json();
    hideSk('skWeekday');
    const wdNames=json.weekdays||['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
    const wdTotal=json.total||Array(7).fill(0);
    const platItems=json.platforms||[];
    const hasData=wdTotal.some(v=>v>0);

    if(!hasData){
      document.getElementById('chWeekday').parentElement.innerHTML=emptyHtml('Data weekday tidak tersedia untuk periode ini');
    } else {
      const wdChart=TrCharts.make('chWeekday');
      if(wdChart){
        const series=platItems.map((plat,pi)=>({
          name:plat.label, type:'bar', stack:'total',
          data:plat.data.map((v,di)=>{
            let isTop=v>0;
            if(isTop) for(let si=pi+1;si<platItems.length;si++) if(platItems[si].data[di]>0){isTop=false;break;}
            return { value:v, itemStyle:{ color:plat.color, borderRadius:isTop?[5,5,0,0]:[0,0,0,0] } };
          }),
          emphasis:{focus:'series'}, label:{show:false},
        }));
        if(series.length>0) series[series.length-1].label={show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',formatter:p=>wdTotal[p.dataIndex]>0?numK(wdTotal[p.dataIndex]):''};
        wdChart.setOption({
          animation:true, animationDuration:800, animationEasing:'elasticOut',
          tooltip:{ ...EC_TIP, trigger:'axis', axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
            formatter:params=>{
              const day=params[0]?.axisValue||'';
              const total=params.reduce((s,p)=>s+(p.value||0),0);
              const rows=[...params].sort((a,b)=>b.value-a.value).filter(p=>p.value>0).map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0"><div style="display:flex;align-items:center;gap:6px"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0"></span><span style="font-size:12px;color:#94a3b8">${p.seriesName}</span></div><span style="font-size:12px;font-weight:700">${numFmt(p.value)}</span></div>`).join('');
              return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1)">${day}</div>${rows||'<div style="color:#64748b;font-size:12px">Tidak ada data</div>'}<div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;gap:16px"><span style="font-size:11px;color:#94a3b8">Total</span><span style="font-size:13px;font-weight:700">${numFmt(total)}</span></div>`;
            }
          },
          legend:{ bottom:0, data:platItems.map(p=>p.label), textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}, icon:'circle', itemWidth:9, itemHeight:9, itemGap:14 },
          grid:{top:24,right:16,bottom:60,left:56},
          xAxis:{ type:'category', data:wdNames, axisLine:{show:false}, axisTick:{show:false}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:12,fontWeight:'600',color:'#64748b'} },
          yAxis:{ type:'value', axisLine:{show:false}, axisTick:{show:false}, splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}}, axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK} },
          series,
        });
      }
    }

    hideSk('skHour');
    const hourChart=TrCharts.make('chHour');
    if(hourChart){
      hourChart.setOption({
        animation:false,
        graphic:[{type:'text',left:'center',top:'middle',style:{text:'Data per jam tidak tersedia dari API',font:"600 12px 'Poppins',sans-serif",fill:'#94a3b8'}}],
        grid:{top:16,right:16,bottom:36,left:56},
        xAxis:{type:'category',data:Array.from({length:24},(_,i)=>String(i).padStart(2,'0')+':00'),axisLine:{show:false},axisTick:{show:false},axisLabel:{color:'#e2e8f0',fontSize:10,interval:3}},
        yAxis:{type:'value',splitLine:{lineStyle:{color:'#f8fafc'}},axisLabel:{color:'#e2e8f0'}},
        series:[{type:'bar',data:Array(24).fill(0),itemStyle:{color:'#f1f5f9',borderRadius:[4,4,0,0]},barMaxWidth:20}]
      });
    }
  } catch(err) {
    ['skWeekday','skHour'].forEach(hideSk);
    console.warn('loadWeekHour error:', err);
    document.getElementById('chWeekday').parentElement.innerHTML=emptyHtml('Data tidak tersedia');
    document.getElementById('chHour').parentElement.innerHTML=emptyHtml('Data tidak tersedia');
  }
}

/* ── INIT ── */
document.addEventListener('DOMContentLoaded', () => {
  TrDp.init();
  // Load trend tab langsung (default active)
  TrTab._loaded.trend = true;
  loadTrend();
});
</script>
@endsection