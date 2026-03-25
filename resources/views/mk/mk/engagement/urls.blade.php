@extends('mk.layouts.app')

@section('title', 'Engagement - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
    --primary-green-light: rgba(3,128,71,0.08);
    --primary-green-border: rgba(3,128,71,0.2);

    --text-primary:   #1a202c;
    --text-secondary: #64748b;
    --text-muted:     #94a3b8;

    --bg-white:   #ffffff;
    --bg-body:    #f0f4f8;
    --bg-gray-50: #f8fafc;
    --bg-gray-100:#f1f5f9;

    --border-gray: #e2e8f0;
    --border-light:#f1f5f9;

    --shadow-sm: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md: 0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1);
    --shadow-xl: 0 20px 40px -8px rgba(0,0,0,.18);

    --radius:    16px;
    --radius-sm: 12px;
    --radius-xs: 8px;
    --transition: all 0.2s cubic-bezier(0.4,0,0.2,1);
    --font: 'Poppins', -apple-system, sans-serif;
  }

  *, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
  body { font-family: var(--font); background: var(--bg-body); color: var(--text-primary); }

  /* ── PAGE WRAPPER ── */
  .eng-page { padding: 24px; max-width: 1600px; margin: 0 auto; }

  /* ── PAGE HEADER ── */
  .page-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
  }
  .page-header-left h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); letter-spacing: -.4px; }
  .page-header-left p  { font-size: 14px; color: var(--text-secondary); font-weight: 500; margin-top: 4px; }

  .eng-refresh-btn {
    display: flex; align-items: center; gap: 8px; padding: 10px 20px;
    background: linear-gradient(135deg,#1a202c,#2d3748);
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-family: var(--font); font-size: 13px; font-weight: 600;
    cursor: pointer; transition: var(--transition); box-shadow: 0 4px 14px rgba(0,0,0,.2);
  }
  .eng-refresh-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.25); }
  .eng-refresh-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

  /* ── FILTER CARD ── */
  .filter-card {
    background: var(--bg-white); border-radius: var(--radius);
    padding: 20px 24px; margin-bottom: 24px;
    box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray);
  }
  .filter-content { display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
  .filter-group   { display: flex; flex-direction: column; gap: 6px; }
  .filter-label   { font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .5px; }
  .filter-select  {
    padding: 10px 14px; border: 1px solid var(--border-gray);
    border-radius: var(--radius-sm); font-family: var(--font);
    font-size: 14px; font-weight: 500; color: var(--text-primary);
    background: var(--bg-gray-50); outline: none; transition: var(--transition); min-width: 200px; cursor: pointer;
  }
  .filter-select:focus { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px var(--primary-green-light); }

  .apply-btn {
    display: flex; align-items: center; gap: 8px; padding: 10px 24px;
    background: linear-gradient(135deg,var(--primary-green),var(--primary-green-dark));
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-family: var(--font); font-size: 14px; font-weight: 600;
    cursor: pointer; transition: var(--transition); box-shadow: 0 4px 12px rgba(3,128,71,.2); white-space: nowrap;
    margin-left: auto;
  }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.3); }
  .apply-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; }

  /* ── SECTION HEADER ── */
  .eng-section-header { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; margin-top: 4px; }
  .eng-section-icon {
    width: 36px; height: 36px; border-radius: var(--radius-sm);
    background: var(--primary-green-light);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .eng-section-icon svg { width: 18px; height: 18px; stroke: var(--primary-green); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .eng-section-title { font-size: 13px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .8px; }
  .eng-section-line  { flex: 1; height: 1.5px; background: var(--border-gray); border-radius: 1px; }

  /* ── STAT CARDS ── */
  .eng-stat-grid { display: grid; grid-template-columns: repeat(6,1fr); gap: 16px; margin-bottom: 24px; }

  .eng-stat-card {
    background: var(--bg-white); border: 1px solid var(--border-gray);
    border-radius: var(--radius); padding: 18px 20px;
    box-shadow: var(--shadow-sm); transition: var(--transition);
    position: relative; overflow: hidden; cursor: pointer;
  }
  .eng-stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--bar-color, linear-gradient(90deg,var(--primary-green),var(--primary-green-dark)));
    opacity: 0; transition: opacity .25s;
  }
  .eng-stat-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
  .eng-stat-card:hover::before { opacity: 1; }

  .eng-stat-card--news   { --bar-color: linear-gradient(90deg,#0284c7,#0369a1); }
  .eng-stat-card--fb     { --bar-color: linear-gradient(90deg,#1877f2,#1558b0); }
  .eng-stat-card--twit   { --bar-color: linear-gradient(90deg,#1d9bf0,#0d8bd9); }
  .eng-stat-card--yt     { --bar-color: linear-gradient(90deg,#ff0000,#cc0000); }
  .eng-stat-card--ig     { --bar-color: linear-gradient(90deg,#e1306c,#c13584); }
  .eng-stat-card--tiktok { --bar-color: linear-gradient(90deg,#2dd4bf,#14b8a6); }

  .eng-stat-label {
    font-size: 11px; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px;
    display: flex; align-items: center; gap: 6px;
  }
  .eng-stat-value {
    font-size: 22px; font-weight: 700; color: var(--text-primary);
    letter-spacing: -0.5px; line-height: 1; min-height: 30px;
    display: flex; align-items: center;
  }
  .eng-stat-sub   { font-size: 10px; color: var(--text-muted); font-weight: 500; margin-top: 6px; }
  .eng-stat-pct   {
    font-size: 11px; font-weight: 700; margin-top: 4px;
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 7px; border-radius: 10px;
    background: var(--primary-green-light); color: var(--primary-green);
  }

  /* ── CARDS ── */
  .eng-card {
    background: var(--bg-white); border: 1px solid var(--border-gray);
    border-radius: var(--radius); overflow: hidden; display: flex;
    flex-direction: column; box-shadow: var(--shadow-sm); transition: var(--transition);
    position: relative;
  }
  .eng-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg,var(--primary-green),var(--primary-green-dark));
    opacity: 0; transition: opacity .3s;
  }
  .eng-card:hover { box-shadow: var(--shadow-lg); border-color: var(--primary-green-border); }
  .eng-card:hover::before { opacity: 1; }

  .eng-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; border-bottom: 1px solid var(--border-gray); flex-shrink: 0;
  }
  .eng-card-head-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
  .eng-head-icon {
    width: 40px; height: 40px; border-radius: var(--radius-sm);
    background: var(--primary-green-light);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .eng-head-icon svg { width: 20px; height: 20px; fill: none; stroke: var(--primary-green); stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .eng-card-title    { font-size: 15px; font-weight: 700; color: var(--text-primary); }
  .eng-card-subtitle { font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 2px; }
  .eng-badge {
    display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px;
    background: var(--bg-gray-100); color: var(--text-secondary); white-space: nowrap; flex-shrink: 0;
  }
  .eng-card-body { padding: 20px; flex: 1; }

  /* ── CHART HEIGHTS ── */
  .eng-ch-220 { position: relative; height: 220px; }
  .eng-ch-280 { position: relative; height: 280px; }
  .eng-ch-320 { position: relative; height: 320px; }
  .eng-ch-360 { position: relative; height: 360px; }
  .eng-ch-400 { position: relative; height: 400px; }

  /* ── SKELETON ── */
  .eng-skel {
    background: linear-gradient(90deg,var(--bg-gray-50) 25%,#e2e8f0 50%,var(--bg-gray-50) 75%);
    background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite; border-radius: var(--radius-xs);
  }
  @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
  .eng-skel-overlay { position: absolute; inset: 0; z-index: 3; border-radius: inherit; }

  /* ── EMPTY STATE ── */
  .eng-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 44px 20px; gap: 10px; }
  .eng-empty svg { width: 40px; height: 40px; stroke: var(--border-gray); fill: none; stroke-width: 1.5; }
  .eng-empty-text { font-size: 13px; font-weight: 600; color: var(--text-secondary); }

  /* ── GRID LAYOUTS ── */
  .eng-grid-2   { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
  .eng-grid-3-2 { display: grid; grid-template-columns: 1.8fr 1fr; gap: 20px; margin-bottom: 20px; }
  .eng-mb20     { margin-bottom: 20px; }
  .eng-mt32     { margin-top: 32px; }

  /* ── LEGEND ── */
  .eng-legend { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
  .eng-legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary); }
  .eng-legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

  /* ── PLATFORM LIST ── */
  .eng-plat-list { display: flex; flex-direction: column; gap: 0; }
  .eng-plat-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 13px 0; border-bottom: 1px solid var(--border-light); transition: var(--transition);
  }
  .eng-plat-row:last-child { border-bottom: none; padding-bottom: 0; }
  .eng-plat-row-left { display: flex; align-items: center; gap: 10px; }
  .eng-plat-icon-wrap { width: 34px; height: 34px; border-radius: var(--radius-xs); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .eng-plat-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); }
  .eng-plat-count { font-size: 16px; font-weight: 800; color: var(--text-primary); letter-spacing: -.5px; }
  .eng-plat-pct   { font-size: 10px; font-weight: 600; color: var(--text-muted); margin-top: 1px; text-align: right; }

  /* ── CSV BTN ── */
  .eng-csv-btn {
    display: flex; align-items: center; gap: 5px; padding: 6px 14px;
    background: var(--bg-gray-100); border: 1px solid var(--border-gray);
    border-radius: var(--radius-xs); font-family: var(--font);
    font-size: 12px; font-weight: 600; color: var(--text-secondary);
    cursor: pointer; transition: var(--transition);
  }
  .eng-csv-btn:hover { background: var(--primary-green); border-color: var(--primary-green); color: #fff; }
  .eng-csv-btn svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 2; }

  /* ── TOTAL CARD ── */
  .eng-total-card {
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    border-radius: var(--radius); padding: 20px 24px; margin-bottom: 24px;
    color: #fff; display: flex; align-items: center; justify-content: space-between;
    gap: 16px; flex-wrap: wrap; box-shadow: 0 8px 24px rgba(3,128,71,.25);
  }
  .eng-total-card h2 { font-size: 32px; font-weight: 800; letter-spacing: -1px; }
  .eng-total-card p  { font-size: 13px; opacity: .85; margin-top: 4px; }
  .eng-total-badge {
    background: rgba(255,255,255,.2); border-radius: 12px;
    padding: 8px 18px; font-size: 13px; font-weight: 700; white-space: nowrap;
  }

  /* ══════════════════════════════════════════════════════
     X / TWITTER ENGAGEMENT STYLES
  ══════════════════════════════════════════════════════ */
  .x-divider {
    display: flex; align-items: center; gap: 16px;
    margin: 36px 0 24px; padding: 0;
  }
  .x-divider-line { flex: 1; height: 1px; background: linear-gradient(90deg, transparent, #e2e8f0, transparent); }
  .x-divider-badge {
    display: flex; align-items: center; gap: 8px; padding: 8px 20px;
    background: linear-gradient(135deg,#1d9bf0,#0d8bd9);
    border-radius: 20px; color: #fff; font-size: 12px; font-weight: 700;
    letter-spacing: .3px; box-shadow: 0 4px 14px rgba(29,155,240,.3);
  }
  .x-divider-badge svg { width: 14px; height: 14px; fill: #fff; }

  /* X Stat Row */
  .x-stat-row {
    display: grid; grid-template-columns: repeat(4,1fr);
    gap: 14px; margin-bottom: 20px;
  }
  .x-stat-box {
    background: var(--bg-white); border: 1px solid var(--border-gray);
    border-radius: var(--radius-sm); padding: 18px 20px;
    box-shadow: var(--shadow-sm); transition: var(--transition);
    position: relative; overflow: hidden;
  }
  .x-stat-box::after {
    content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg,#1d9bf0,#0d8bd9);
    opacity: 0; transition: opacity .25s;
  }
  .x-stat-box:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
  .x-stat-box:hover::after { opacity: 1; }
  .x-stat-icon {
    width: 36px; height: 36px; border-radius: var(--radius-xs);
    background: rgba(29,155,240,.1); display: flex; align-items: center; justify-content: center;
    margin-bottom: 12px;
  }
  .x-stat-icon svg { width: 18px; height: 18px; }
  .x-stat-label {
    font-size: 11px; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
  }
  .x-stat-value {
    font-size: 24px; font-weight: 800; color: #1d9bf0;
    letter-spacing: -.5px; line-height: 1; min-height: 28px;
    display: flex; align-items: center;
  }
  .x-stat-sub { font-size: 10px; color: var(--text-muted); font-weight: 500; margin-top: 5px; }

  /* X Total Banner */
  .x-total-banner {
    background: linear-gradient(135deg,#1d9bf0,#0d8bd9);
    border-radius: var(--radius); padding: 20px 28px; margin-bottom: 20px;
    color: #fff; display: flex; align-items: center; justify-content: space-between;
    gap: 16px; flex-wrap: wrap; box-shadow: 0 8px 24px rgba(29,155,240,.25);
  }
  .x-total-banner-val {
    font-size: 36px; font-weight: 800; letter-spacing: -1px; line-height: 1;
    min-height: 40px; display: flex; align-items: center;
  }
  .x-rate-box {
    text-align: right; background: rgba(255,255,255,.15);
    border-radius: var(--radius-sm); padding: 12px 20px; min-width: 160px;
  }
  .x-rate-val { font-size: 28px; font-weight: 800; letter-spacing: -.5px; }

  /* X Breakdown List */
  .x-breakdown-list { display: flex; flex-direction: column; }
  .x-breakdown-row {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 0; border-bottom: 1px solid var(--border-light);
  }
  .x-breakdown-row:last-child { border-bottom: none; padding-bottom: 0; }
  .x-breakdown-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
  .x-breakdown-name { font-size: 13px; font-weight: 600; color: var(--text-primary); width: 72px; flex-shrink: 0; }
  .x-breakdown-bar-wrap { flex: 1; height: 6px; background: var(--bg-gray-100); border-radius: 3px; overflow: hidden; }
  .x-breakdown-bar { height: 100%; border-radius: 3px; transition: width .8s cubic-bezier(.4,0,.2,1); }
  .x-breakdown-val { font-size: 14px; font-weight: 800; min-width: 70px; text-align: right; letter-spacing: -.3px; }
  .x-breakdown-pct { font-size: 10px; font-weight: 600; color: var(--text-muted); min-width: 38px; text-align: right; }

  /* X Card hover color override */
  .eng-card.x-card::before { background: linear-gradient(90deg,#1d9bf0,#0d8bd9); }
  .eng-card.x-card:hover   { border-color: rgba(29,155,240,.2); }

  @media (max-width: 1280px) {
    .eng-stat-grid { grid-template-columns: repeat(3,1fr); }
    .eng-grid-2, .eng-grid-3-2 { grid-template-columns: 1fr; }
    .x-stat-row { grid-template-columns: repeat(2,1fr); }
  }
  @media (max-width: 768px) {
    .eng-page { padding: 16px; }
    .eng-stat-grid { grid-template-columns: repeat(2,1fr); }
    .x-stat-row { grid-template-columns: repeat(2,1fr); }
  }
</style>
@endsection

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date',   now()->format('Y-m-d'));
  $media     = request()->get('media', 'all');
  $engType   = request()->get('eng_type', 'all');
  $projects  = $projects ?? [];
@endphp

<div class="eng-page">

  {{-- ── PAGE HEADER ── --}}
  <div class="page-header">
    <div class="page-header-left">
      <h1>Engagement</h1>
      <p>Total interaksi (likes, shares, comments, views, retweets) berdasarkan platform dan tipe media</p>
    </div>
    <button class="eng-refresh-btn" onclick="ENGPage.reload()">
      <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
      Refresh
    </button>
  </div>

  {{-- ── FILTER ── --}}
  <div class="filter-card">
    <form id="engFilterForm" method="GET">
      <input type="hidden" name="project_id" id="hPid" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hSD"  value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hED"  value="{{ $endDate }}">

      <div class="filter-content">
        @if(count($projects))
        <div class="filter-group">
          <label class="filter-label">Project</label>
          <select class="filter-select" onchange="document.getElementById('hPid').value=this.value">
            @foreach($projects as $p)
            <option value="{{ $p['id'] }}" {{ ($p['id'] == $projectId) ? 'selected' : '' }}>
              {{ $p['name'] ?? $p['title'] ?? 'Project #'.$p['id'] }}
            </option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="filter-group">
          <label class="filter-label">Media</label>
          <select class="filter-select" name="media" id="mediaFilter">
            <option value="all"       {{ $media==='all'       ? 'selected':'' }}>All Media</option>
            <option value="doc"       {{ $media==='doc'       ? 'selected':'' }}>Mass Media (Online News)</option>
            <option value="twitter"   {{ $media==='twitter'   ? 'selected':'' }}>X / Twitter</option>
            <option value="facebook"  {{ $media==='facebook'  ? 'selected':'' }}>Facebook</option>
            <option value="instagram" {{ $media==='instagram' ? 'selected':'' }}>Instagram</option>
            <option value="youtube"   {{ $media==='youtube'   ? 'selected':'' }}>YouTube</option>
            <option value="tiktok"    {{ $media==='tiktok'    ? 'selected':'' }}>TikTok</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">Engagement Type</label>
          <select class="filter-select" name="eng_type" id="engTypeFilter">
            <option value="all"      {{ $engType==='all'      ? 'selected':'' }}>All Interactions</option>
            <option value="likes"    {{ $engType==='likes'    ? 'selected':'' }}>Likes</option>
            <option value="shares"   {{ $engType==='shares'   ? 'selected':'' }}>Shares / Retweets</option>
            <option value="comments" {{ $engType==='comments' ? 'selected':'' }}>Comments</option>
            <option value="views"    {{ $engType==='views'    ? 'selected':'' }}>Views / Plays</option>
          </select>
        </div>

        <button type="submit" class="apply-btn">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          Filter
        </button>
      </div>
    </form>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 1 — ALL INTERACTION (All Platforms)
  ═══════════════════════════════════════════════════════ --}}
  <div class="eng-section-header">
    <div class="eng-section-icon">
      <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
    </div>
    <span class="eng-section-title">All Interaction</span>
    <div class="eng-section-line"></div>
  </div>

  {{-- Grand Total Banner --}}
  <div class="eng-total-card">
    <div>
      <div style="font-size:13px;opacity:.85;font-weight:600;margin-bottom:4px;">Total Interaction</div>
      <div id="engGrandTotal" style="font-size:36px;font-weight:800;letter-spacing:-1px;">
        <span class="eng-skel" style="display:inline-block;height:40px;width:160px;border-radius:8px;"></span>
      </div>
      <div style="font-size:12px;opacity:.75;margin-top:4px;">
        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
      </div>
    </div>
    <div style="text-align:right;">
      <div style="font-size:12px;opacity:.8;margin-bottom:8px;">By Platform</div>
      <div class="eng-total-badge" id="engGrandBadge">Loading…</div>
    </div>
  </div>

  {{-- Stat Cards per Platform --}}
  <div class="eng-stat-grid" id="engStatGrid">
    @foreach([
      ['id'=>'statNews',  'label'=>'Online News (Ind)', 'class'=>'eng-stat-card--news',   'color'=>'#0284c7'],
      ['id'=>'statFb',    'label'=>'Facebook',          'class'=>'eng-stat-card--fb',     'color'=>'#1877f2'],
      ['id'=>'statTwit',  'label'=>'Twitter / X',       'class'=>'eng-stat-card--twit',   'color'=>'#1d9bf0'],
      ['id'=>'statYt',    'label'=>'YouTube',           'class'=>'eng-stat-card--yt',     'color'=>'#ff0000'],
      ['id'=>'statIg',    'label'=>'Instagram',         'class'=>'eng-stat-card--ig',     'color'=>'#e1306c'],
      ['id'=>'statTiktok','label'=>'TikTok',            'class'=>'eng-stat-card--tiktok', 'color'=>'#2dd4bf'],
    ] as $card)
    <div class="eng-stat-card {{ $card['class'] }}" id="{{ $card['id'] }}">
      <div class="eng-stat-label">
        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $card['color'] }};flex-shrink:0;"></span>
        {{ $card['label'] }}
      </div>
      <div class="eng-stat-value">
        <div class="eng-skel" style="height:28px;width:90px;border-radius:6px;"></div>
      </div>
      <div class="eng-stat-sub">Total interaksi</div>
      <div class="eng-stat-pct" id="{{ $card['id'] }}Pct">—</div>
    </div>
    @endforeach
  </div>

  {{-- Bar Chart + Pie --}}
  <div class="eng-grid-3-2 eng-mb20">
    <div class="eng-card">
      <div class="eng-card-head">
        <div class="eng-card-head-left">
          <span class="eng-head-icon">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </span>
          <div>
            <div class="eng-card-title">Total Interaction by Media Types</div>
            <div class="eng-card-subtitle">Jumlah total interaksi per platform</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
          <button class="eng-csv-btn" onclick="ENGCsv.copyBar()">
            <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Copy CSV
          </button>
          <span class="eng-badge">Bar Chart</span>
        </div>
      </div>
      <div class="eng-card-body">
        <div class="eng-ch-360">
          <div id="chBar" style="width:100%;height:100%;"></div>
          <div class="eng-skel eng-skel-overlay" id="skBar"></div>
        </div>
      </div>
    </div>

    <div class="eng-card">
      <div class="eng-card-head">
        <div class="eng-card-head-left">
          <span class="eng-head-icon">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
          </span>
          <div>
            <div class="eng-card-title">Share of Interaction</div>
            <div class="eng-card-subtitle">Persentase per media types</div>
          </div>
        </div>
        <span class="eng-badge">SOI</span>
      </div>
      <div class="eng-card-body" style="display:flex;flex-direction:column;gap:16px;">
        <div style="position:relative;height:260px;">
          <div id="chPie" style="width:100%;height:100%;"></div>
          <div class="eng-skel" style="position:absolute;inset:0;border-radius:8px;" id="skPie"></div>
        </div>
        <div class="eng-plat-list" id="engPlatList">
          @for($i=0;$i<6;$i++)
          <div class="eng-plat-row">
            <div class="eng-plat-row-left">
              <div class="eng-skel" style="width:34px;height:34px;border-radius:8px;"></div>
              <div class="eng-skel" style="width:70px;height:14px;border-radius:4px;margin-left:4px;"></div>
            </div>
            <div style="text-align:right;">
              <div class="eng-skel" style="width:60px;height:18px;border-radius:4px;margin-bottom:3px;"></div>
              <div class="eng-skel" style="width:40px;height:11px;border-radius:3px;"></div>
            </div>
          </div>
          @endfor
        </div>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 2 — INTERACTION TREND
  ═══════════════════════════════════════════════════════ --}}
  <div class="eng-section-header">
    <div class="eng-section-icon">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <span class="eng-section-title">Interaction Trends</span>
    <div class="eng-section-line"></div>
  </div>

  <div class="eng-card eng-mb20">
    <div class="eng-card-head">
      <div class="eng-card-head-left">
        <span class="eng-head-icon">
          <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </span>
        <div>
          <div class="eng-card-title">Interaction Trend by Media Types</div>
          <div class="eng-card-subtitle">Tren harian total interaksi per platform</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
        <button class="eng-csv-btn" onclick="ENGCsv.copyTrend()">
          <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          Copy CSV
        </button>
        <span class="eng-badge" id="trendBadge">Loading…</span>
      </div>
    </div>
    <div class="eng-card-body">
      <div class="eng-ch-400">
        <div id="chTrend" style="width:100%;height:100%;"></div>
        <div class="eng-skel eng-skel-overlay" id="skTrend"></div>
      </div>
      <div class="eng-legend" style="margin-top:14px;justify-content:center;" id="trendLegend"></div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════
       SECTION 3 — MASS MEDIA vs SOCIAL MEDIA
  ═══════════════════════════════════════════════════════ --}}
  <div class="eng-section-header">
    <div class="eng-section-icon">
      <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
    </div>
    <span class="eng-section-title">Mass Media vs Social Media</span>
    <div class="eng-section-line"></div>
  </div>

  <div class="eng-grid-2 eng-mb20">
    <div class="eng-card">
      <div class="eng-card-head">
        <div class="eng-card-head-left">
          <span class="eng-head-icon">
            <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
          </span>
          <div>
            <div class="eng-card-title">Mass Media Interaction</div>
            <div class="eng-card-subtitle">Online News / Artikel</div>
          </div>
        </div>
        <span class="eng-badge">Mass</span>
      </div>
      <div class="eng-card-body">
        <div class="eng-ch-280">
          <div id="chMass" style="width:100%;height:100%;"></div>
          <div class="eng-skel eng-skel-overlay" id="skMass"></div>
        </div>
      </div>
    </div>

    <div class="eng-card">
      <div class="eng-card-head">
        <div class="eng-card-head-left">
          <span class="eng-head-icon">
            <svg viewBox="0 0 24 24"><path d="M17 2H7a5 5 0 0 0-5 5v10a5 5 0 0 0 5 5h10a5 5 0 0 0 5-5V7a5 5 0 0 0-5-5z"/><circle cx="12" cy="12" r="3"/></svg>
          </span>
          <div>
            <div class="eng-card-title">Social Media Interaction</div>
            <div class="eng-card-subtitle">Twitter · Facebook · Instagram · YouTube · TikTok</div>
          </div>
        </div>
        <span class="eng-badge">Social</span>
      </div>
      <div class="eng-card-body">
        <div class="eng-ch-280">
          <div id="chSocial" style="width:100%;height:100%;"></div>
          <div class="eng-skel eng-skel-overlay" id="skSocial"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════════════
       DIVIDER — X / TWITTER ENGAGEMENT
  ═══════════════════════════════════════════════════════════════ --}}
  <div class="x-divider">
    <div class="x-divider-line"></div>
    <div class="x-divider-badge">
      <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
      X / Twitter Engagement Detail
    </div>
    <div class="x-divider-line"></div>
  </div>

  {{-- ── X SECTION HEADER ── --}}
  <div class="eng-section-header">
    <div class="eng-section-icon" style="background:rgba(29,155,240,.1);">
      <svg viewBox="0 0 24 24" fill="#1d9bf0" stroke="none" style="width:18px;height:18px;">
        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
      </svg>
    </div>
    <span class="eng-section-title">Mentions &amp; Interaction Overview</span>
    <div class="eng-section-line"></div>
    <div id="xLoadBadge" class="eng-badge" style="background:rgba(29,155,240,.1);color:#1d9bf0;">Loading…</div>
  </div>

  {{-- ── X STAT BOXES: Posts · Views · Retweets · Favorites ── --}}
  <div class="x-stat-row">
    <div class="x-stat-box" id="xStatPosts">
      <div class="x-stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#1d9bf0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </div>
      <div class="x-stat-label">Posts</div>
      <div class="x-stat-value"><span class="eng-skel" style="display:inline-block;height:26px;width:80px;border-radius:5px;"></span></div>
      <div class="x-stat-sub">Total tweets / status</div>
    </div>
    <div class="x-stat-box" id="xStatViews">
      <div class="x-stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      </div>
      <div class="x-stat-label">Views</div>
      <div class="x-stat-value" style="color:#7c3aed;"><span class="eng-skel" style="display:inline-block;height:26px;width:80px;border-radius:5px;"></span></div>
      <div class="x-stat-sub">Total tayangan</div>
    </div>
    <div class="x-stat-box" id="xStatRetweets">
      <div class="x-stat-icon" style="background:rgba(16,185,129,.1);">
        <svg viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
      </div>
      <div class="x-stat-label">Retweets</div>
      <div class="x-stat-value" style="color:#10b981;"><span class="eng-skel" style="display:inline-block;height:26px;width:80px;border-radius:5px;"></span></div>
      <div class="x-stat-sub">Total retweet</div>
    </div>
    <div class="x-stat-box" id="xStatFavs">
      <div class="x-stat-icon" style="background:rgba(245,158,11,.1);">
        <svg viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <div class="x-stat-label">Favorites</div>
      <div class="x-stat-value" style="color:#f59e0b;"><span class="eng-skel" style="display:inline-block;height:26px;width:80px;border-radius:5px;"></span></div>
      <div class="x-stat-sub">Total likes / favorit</div>
    </div>
  </div>

  {{-- ── X TOTAL BANNER ── --}}
  <div class="x-total-banner">
    <div>
      <div style="font-size:12px;opacity:.8;font-weight:600;margin-bottom:6px;">Total X Interaction</div>
      <div class="x-total-banner-val" id="xTotalInteraction">
        <span class="eng-skel" style="display:inline-block;height:40px;width:160px;border-radius:8px;background:rgba(255,255,255,.2);"></span>
      </div>
      <div style="font-size:11px;opacity:.7;margin-top:6px;">Posts + Views + Retweets + Favorites</div>
    </div>
    <div class="x-rate-box">
      <div style="font-size:11px;opacity:.8;margin-bottom:4px;">Interaction Rate</div>
      <div class="x-rate-val" id="xInteractionRate">—</div>
      <div style="font-size:10px;opacity:.7;margin-top:4px;">Avg engagements / post</div>
    </div>
  </div>

  {{-- ── ROW 1: Mentions Breakdown + Interaction Breakdown ── --}}
  <div class="eng-grid-2">

    {{-- Mentions: Mention / Reply / Retweet --}}
    <div class="eng-card x-card">
      <div class="eng-card-head">
        <div class="eng-card-head-left">
          <span class="eng-head-icon" style="background:rgba(29,155,240,.1);">
            <svg viewBox="0 0 24 24" fill="none" stroke="#1d9bf0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <div>
            <div class="eng-card-title">Total Mentions by Post Types</div>
            <div class="eng-card-subtitle">Mention · Reply · Retweet</div>
          </div>
        </div>
        <span class="eng-badge" id="xMentionTotalBadge" style="background:rgba(29,155,240,.1);color:#1d9bf0;">—</span>
      </div>
      <div class="eng-card-body">
        <div class="eng-ch-220">
          <div id="chXMentionBar" style="width:100%;height:100%;"></div>
          <div class="eng-skel eng-skel-overlay" id="skXMentionBar"></div>
        </div>
        <div style="margin-top:16px;" class="x-breakdown-list" id="xMentionList">
          @for($i=0;$i<3;$i++)
          <div class="x-breakdown-row">
            <div class="x-breakdown-dot eng-skel"></div>
            <div class="x-breakdown-name eng-skel" style="height:13px;border-radius:4px;"></div>
            <div class="x-breakdown-bar-wrap"><div class="x-breakdown-bar eng-skel" style="width:70%;"></div></div>
            <span class="x-breakdown-val eng-skel" style="width:55px;height:16px;border-radius:4px;display:inline-block;"></span>
            <span class="x-breakdown-pct eng-skel" style="width:32px;height:11px;border-radius:3px;display:inline-block;"></span>
          </div>
          @endfor
        </div>
      </div>
    </div>

    {{-- Interaction: Posts / Views / Retweets / Favorites --}}
    <div class="eng-card x-card">
      <div class="eng-card-head">
        <div class="eng-card-head-left">
          <span class="eng-head-icon" style="background:rgba(29,155,240,.1);">
            <svg viewBox="0 0 24 24" fill="none" stroke="#1d9bf0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </span>
          <div>
            <div class="eng-card-title">Total Interaction by Activity Types</div>
            <div class="eng-card-subtitle">Posts · Views · Retweets · Favorites</div>
          </div>
        </div>
        <span class="eng-badge" style="background:rgba(29,155,240,.1);color:#1d9bf0;">Activity</span>
      </div>
      <div class="eng-card-body">
        <div class="eng-ch-220">
          <div id="chXInterBar" style="width:100%;height:100%;"></div>
          <div class="eng-skel eng-skel-overlay" id="skXInterBar"></div>
        </div>
        <div style="margin-top:16px;" class="x-breakdown-list" id="xInterList">
          @for($i=0;$i<4;$i++)
          <div class="x-breakdown-row">
            <div class="x-breakdown-dot eng-skel"></div>
            <div class="x-breakdown-name eng-skel" style="height:13px;border-radius:4px;"></div>
            <div class="x-breakdown-bar-wrap"><div class="x-breakdown-bar eng-skel" style="width:70%;"></div></div>
            <span class="x-breakdown-val eng-skel" style="width:55px;height:16px;border-radius:4px;display:inline-block;"></span>
            <span class="x-breakdown-pct eng-skel" style="width:32px;height:11px;border-radius:3px;display:inline-block;"></span>
          </div>
          @endfor
        </div>
      </div>
    </div>
  </div>

  {{-- ── ROW 2: SOV Pies ── --}}
  <div class="eng-section-header" style="margin-top:8px;">
    <div class="eng-section-icon" style="background:rgba(29,155,240,.1);">
      <svg viewBox="0 0 24 24" fill="#1d9bf0" stroke="none" style="width:16px;height:16px;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
    </div>
    <span class="eng-section-title">Share of Voice</span>
    <div class="eng-section-line"></div>
  </div>

  <div class="eng-grid-2">
    <div class="eng-card x-card">
      <div class="eng-card-head">
        <div class="eng-card-head-left">
          <span class="eng-head-icon" style="background:rgba(29,155,240,.1);">
            <svg viewBox="0 0 24 24" fill="none" stroke="#1d9bf0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
          </span>
          <div>
            <div class="eng-card-title">Share of Voice by Post Types</div>
            <div class="eng-card-subtitle">Proporsi Mention · Reply · Retweet</div>
          </div>
        </div>
        <span class="eng-badge">SOV</span>
      </div>
      <div class="eng-card-body">
        <div class="eng-ch-280">
          <div id="chXMentionPie" style="width:100%;height:100%;"></div>
          <div class="eng-skel eng-skel-overlay" id="skXMentionPie"></div>
        </div>
      </div>
    </div>

    <div class="eng-card x-card">
      <div class="eng-card-head">
        <div class="eng-card-head-left">
          <span class="eng-head-icon" style="background:rgba(29,155,240,.1);">
            <svg viewBox="0 0 24 24" fill="none" stroke="#1d9bf0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
          </span>
          <div>
            <div class="eng-card-title">Share of Voice by Twitter Activity</div>
            <div class="eng-card-subtitle">Proporsi Posts · Views · Retweets · Favorites</div>
          </div>
        </div>
        <span class="eng-badge">SOV</span>
      </div>
      <div class="eng-card-body">
        <div class="eng-ch-280">
          <div id="chXInterPie" style="width:100%;height:100%;"></div>
          <div class="eng-skel eng-skel-overlay" id="skXInterPie"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── ROW 3: Trend of X Interaction ── --}}
  <div class="eng-section-header" style="margin-top:8px;">
    <div class="eng-section-icon" style="background:rgba(29,155,240,.1);">
      <svg viewBox="0 0 24 24" fill="none" stroke="#1d9bf0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
    </div>
    <span class="eng-section-title">Trend of X Interaction</span>
    <div class="eng-section-line"></div>
  </div>

  <div class="eng-card x-card" style="margin-bottom:32px;">
    <div class="eng-card-head">
      <div class="eng-card-head-left">
        <span class="eng-head-icon" style="background:rgba(29,155,240,.1);">
          <svg viewBox="0 0 24 24" fill="none" stroke="#1d9bf0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </span>
        <div>
          <div class="eng-card-title">The Trends of Interaction</div>
          <div class="eng-card-subtitle">Tren harian total interaksi X / Twitter</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
        <button class="eng-csv-btn" onclick="XEng.csvTrend()">
          <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          Copy CSV
        </button>
        <span class="eng-badge" id="xTrendBadge" style="background:rgba(29,155,240,.1);color:#1d9bf0;">Loading…</span>
      </div>
    </div>
    <div class="eng-card-body">
      <div class="eng-ch-320">
        <div id="chXTrend" style="width:100%;height:100%;"></div>
        <div class="eng-skel eng-skel-overlay" id="skXTrend"></div>
      </div>
    </div>
  </div>

</div>{{-- /eng-page --}}
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
const ENGCfg = {
  pid:     {{ $projectId ? (int)$projectId : 'null' }},
  sd:      '{{ $startDate }}',
  ed:      '{{ $endDate }}',
  media:   '{{ $media }}',
  engType: '{{ $engType }}',
};

/* ── UTILS ── */
const numFmt = n => parseInt(n||0).toLocaleString('id-ID');
const numK   = n => {
  n = parseInt(n||0);
  if (n >= 1e9) return (n/1e9).toFixed(1)+'B';
  if (n >= 1e6) return (n/1e6).toFixed(1)+'M';
  if (n >= 1e3) return (n/1e3).toFixed(1)+'k';
  return String(n);
};
const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const emptyHtml = msg => `<div class="eng-empty">
  <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  <span class="eng-empty-text">${msg}</span></div>`;

/* ── PLATFORM META ── */
const PLAT_META = {
  doc:      { label:'Online News (Ind)', color:'#0284c7', bg:'rgba(2,132,199,.1)',   icon:'<svg viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>', statId:'statNews' },
  twitter:  { label:'Twitter / X',      color:'#1d9bf0', bg:'rgba(29,155,240,.1)',  icon:'<svg viewBox="0 0 24 24" fill="#1d9bf0" stroke="none" style="width:18px;height:18px;"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>', statId:'statTwit' },
  facebook: { label:'Facebook',         color:'#1877f2', bg:'rgba(24,119,242,.1)',  icon:'<svg viewBox="0 0 24 24" fill="none" stroke="#1877f2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>', statId:'statFb' },
  instagram:{ label:'Instagram',        color:'#e1306c', bg:'rgba(225,48,108,.1)',  icon:'<svg viewBox="0 0 24 24" fill="none" stroke="#e1306c" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>', statId:'statIg' },
  youtube:  { label:'YouTube',          color:'#ff0000', bg:'rgba(255,0,0,.08)',    icon:'<svg viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>', statId:'statYt' },
  tiktok:   { label:'TikTok',           color:'#2dd4bf', bg:'rgba(45,212,191,.1)',  icon:'<svg viewBox="0 0 24 24" fill="#111827" stroke="none" style="width:18px;height:18px;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/></svg>', statId:'statTiktok' },
};
const PLAT_ORDER = ['doc','facebook','twitter','youtube','instagram','tiktok'];

/* ── ECHARTS REGISTRY ── */
const ENGCharts = {
  _i: {},
  make(id) {
    if (this._i[id]) { try { this._i[id].dispose(); } catch(e){} }
    const dom = document.getElementById(id);
    if (!dom) return null;
    const c = echarts.init(dom, null, { renderer:'canvas' });
    this._i[id] = c;
    return c;
  },
  disposeAll() { Object.values(this._i).forEach(c=>{ try{c.dispose();}catch(e){} }); this._i={}; }
};
window.addEventListener('resize', () => {
  Object.values(ENGCharts._i).forEach(c=>{ try{ if(!c.isDisposed()) c.resize(); }catch(e){} });
});

const EC_TIP = {
  backgroundColor:'#1a202c', borderColor:'#334155', borderWidth:1,
  padding:[10,14], textStyle:{color:'#fff',fontFamily:"'Poppins',sans-serif",fontSize:12},
  extraCssText:'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);',
};

/* ══════════════════════════════════════════════════════
   DATA STORE
══════════════════════════════════════════════════════ */
const ENGData = {
  platforms:  [],
  trend:      [],
  grandTotal: 0,
};

/* ══════════════════════════════════════════════════════
   SECTION 1–3: ALL PLATFORMS ENGAGEMENT
══════════════════════════════════════════════════════ */
async function loadEngagement() {
  if (!ENGCfg.pid) {
    ['skBar','skPie','skTrend','skMass','skSocial'].forEach(hideSk);
    return;
  }

  try {
    const q   = `project_id=${ENGCfg.pid}&start_date=${ENGCfg.sd}&end_date=${ENGCfg.ed}&media=${ENGCfg.media}`;
    const res  = await fetch(`/mk/api/media-statistic/mention-by-platform?${q}`);
    const data = await res.json();
    if (data.error) throw new Error(data.error);

    const platMap = {};
    (data.platforms || []).forEach(p => { platMap[p.media] = p.count; });

    ENGData.platforms = PLAT_ORDER.map(key => ({
      key,
      label: PLAT_META[key]?.label || key,
      count: platMap[key] || 0,
      color: PLAT_META[key]?.color || '#038047',
    }));
    ENGData.grandTotal = ENGData.platforms.reduce((s,p) => s+p.count, 0);

    renderStatCards();
    renderBarChart();
    renderPieChart();
    renderPlatList();
  } catch(err) {
    console.error('loadEngagement error:', err);
    ['skBar','skPie'].forEach(hideSk);
  }

  try {
    const q    = `project_id=${ENGCfg.pid}&start_date=${ENGCfg.sd}&end_date=${ENGCfg.ed}`;
    const res   = await fetch(`/mk/api/media-statistic/trend-mentions?${q}`);
    const data  = await res.json();
    if (data.error) throw new Error(data.error);
    ENGData.trend = data.data || [];
    renderTrendChart();
    renderMassSocialCharts();
  } catch(err) {
    console.error('loadTrend error:', err);
    ['skTrend','skMass','skSocial'].forEach(hideSk);
  }
}

function renderStatCards() {
  const total = ENGData.grandTotal;
  document.getElementById('engGrandTotal').textContent = numFmt(total);
  document.getElementById('engGrandBadge').textContent = `${PLAT_ORDER.length} Platforms`;

  ENGData.platforms.forEach(p => {
    const meta = PLAT_META[p.key];
    if (!meta) return;
    const el = document.getElementById(meta.statId);
    if (!el) return;
    el.querySelector('.eng-stat-value').textContent = numFmt(p.count);
    const pctEl = document.getElementById(meta.statId+'Pct');
    if (pctEl) pctEl.textContent = total > 0 ? ((p.count/total)*100).toFixed(1)+'%' : '0%';
  });
}

function renderBarChart() {
  hideSk('skBar');
  if (!ENGData.platforms.length || ENGData.grandTotal === 0) {
    document.getElementById('chBar').innerHTML = emptyHtml('Tidak ada data engagement');
    return;
  }
  const chart = ENGCharts.make('chBar');
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip: {
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const p   = params[0];
        const pct = ENGData.grandTotal > 0 ? ((p.value/ENGData.grandTotal)*100).toFixed(1) : 0;
        return `<div style="font-weight:700;font-size:13px;margin-bottom:6px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;">
                  <span style="color:#94a3b8;">Interactions</span><span style="font-weight:700;">${numFmt(p.value)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;">
                  <span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct}%</span>
                </div>`;
      }
    },
    grid:{top:24,right:20,bottom:60,left:70},
    xAxis:{
      type:'category', data:ENGData.platforms.map(p=>p.label),
      axisLine:{show:false}, axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#374151',rotate:15,interval:0}
    },
    yAxis:{
      type:'value', axisLine:{show:false}, axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[{
      type:'bar', barMaxWidth:72,
      data: ENGData.platforms.map(p=>({
        value:p.count,
        itemStyle:{
          color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[
            {offset:0,color:p.color},{offset:1,color:p.color+'55'}
          ]},
          borderRadius:[8,8,0,0]
        }
      })),
      label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',formatter:p=>numK(p.value)}
    }]
  });
}

function renderPieChart() {
  hideSk('skPie');
  const total = ENGData.grandTotal;
  if (total === 0) { document.getElementById('chPie').innerHTML = emptyHtml('Tidak ada data'); return; }
  const chart = ENGCharts.make('chPie');
  if (!chart) return;

  chart.setOption({
    animation:true, animationDuration:800, animationEasing:'cubicOut',
    backgroundColor:'transparent',
    tooltip:{
      ...EC_TIP, trigger:'item',
      formatter: p => {
        const pct = total > 0 ? ((p.value/total)*100).toFixed(1) : '0.0';
        return `<div style="font-weight:700;font-size:13px;margin-bottom:5px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;">
                  <span style="color:#94a3b8;">Interactions</span><span style="font-weight:700;">${numFmt(p.value)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;">
                  <span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct}%</span>
                </div>`;
      }
    },
    legend:{show:false},
    series:[{
      type:'pie', radius:['40%','60%'], center:['50%','50%'],
      avoidLabelOverlap:true, minAngle:3,
      itemStyle:{borderColor:'#fff',borderWidth:3,borderRadius:5},
      label:{
        show:true, alignTo:'edge', edgeDistance:8, lineHeight:18,
        fontFamily:"'Poppins',sans-serif", fontSize:10, color:'#374151',
        formatter: p => {
          const pc = total > 0 ? (p.value/total*100) : 0;
          if (pc < 2) return '';
          return `{name|${p.name}}\n{pct|${pc.toFixed(1)}%}`;
        },
        rich:{
          name:{fontWeight:'700',fontSize:10,color:'#1a202c',lineHeight:18},
          pct:{fontWeight:'700',fontSize:9,color:'#038047',lineHeight:16,backgroundColor:'#edf7f3',borderRadius:4,padding:[1,4]},
        }
      },
      labelLine:{show:true,length:10,length2:14,smooth:.4,lineStyle:{color:'#c4cdd8',width:1.2}},
      emphasis:{scale:true,scaleSize:5,itemStyle:{shadowBlur:10,shadowColor:'rgba(0,0,0,.12)'}},
      data: ENGData.platforms.map(p=>({name:p.label,value:p.count,itemStyle:{color:p.color}}))
    }],
    graphic:[
      {type:'text',left:'center',top:'42%',z:100,style:{text:numK(total),fill:'#0f172a',font:"800 20px 'Poppins',sans-serif",textAlign:'center'}},
      {type:'text',left:'center',top:'51%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"600 9px 'Poppins',sans-serif",textAlign:'center',letterSpacing:2}},
    ]
  });
}

function renderPlatList() {
  const el    = document.getElementById('engPlatList');
  const total = ENGData.grandTotal;
  if (!el) return;
  el.innerHTML = ENGData.platforms.map(p => {
    const meta = PLAT_META[p.key];
    const pct  = total > 0 ? ((p.count/total)*100).toFixed(1) : '0.0';
    return `<div class="eng-plat-row">
      <div class="eng-plat-row-left">
        <div class="eng-plat-icon-wrap" style="background:${meta?.bg||'#f1f5f9'};">${meta?.icon||''}</div>
        <div class="eng-plat-name" style="color:${p.color};">${p.label}</div>
      </div>
      <div style="text-align:right;">
        <div class="eng-plat-count" style="color:${p.color};">${numFmt(p.count)}</div>
        <div class="eng-plat-pct">${pct}%</div>
      </div>
    </div>`;
  }).join('');
}

function renderTrendChart() {
  hideSk('skTrend');
  const trendData = ENGData.trend;
  if (!trendData.length) {
    document.getElementById('trendBadge').textContent = 'No Data';
    document.getElementById('chTrend').innerHTML = emptyHtml('Data trend tidak tersedia');
    return;
  }
  const firstPlt = trendData.find(p=>p.data?.length) || trendData[0];
  const dates    = (firstPlt?.data||[]).map(d=>d.date);
  const fmtB = d => { const dt=new Date(d+'T00:00:00'); return `${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`; };
  if (dates.length) document.getElementById('trendBadge').textContent = `${fmtB(dates[0])} – ${fmtB(dates[dates.length-1])}`;
  const xLabels = dates.map(d=>{ const dt=new Date(d+'T00:00:00'); return `${dt.getDate()}.${dt.toLocaleString('id-ID',{month:'short'})}`; });

  const legendEl = document.getElementById('trendLegend');
  if (legendEl) legendEl.innerHTML = trendData.map(p=>`<div class="eng-legend-item"><span class="eng-legend-dot" style="background:${p.color};"></span>${p.label}</div>`).join('');

  const chart = ENGCharts.make('chTrend');
  if (!chart) return;
  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'cubicInOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'line',lineStyle:{color:'#e2e8f0',type:'dashed',width:1.5}},
      formatter: params => {
        const di   = params[0]?.dataIndex??0;
        const date = dates[di]||'';
        const fullDt = date ? new Date(date+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'}) : '';
        const tot  = params.reduce((s,p)=>s+(p.value||0),0);
        const rows = params.map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
            <div style="display:flex;align-items:center;gap:6px;">
              <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};"></span>
              <span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span>
            </div>
            <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
          </div>`).join('');
        return `<div style="font-weight:700;font-size:13px;margin-bottom:4px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${fullDt||date}</div>
                ${rows}
                <div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;">
                  <span style="font-size:11px;color:#94a3b8;">Total</span><span style="font-weight:700;">${numFmt(tot)}</span>
                </div>`;
      }
    },
    legend:{show:false},
    grid:{top:28,right:20,bottom:24,left:68},
    xAxis:{type:'category',data:xLabels,boundaryGap:false,
      axisLine:{lineStyle:{color:'#e2e8f0'}},axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}
    },
    yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'solid',width:1}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series: trendData.map(p=>({
      name:p.label, type:'line', data:(p.data||[]).map(d=>d.count),
      smooth:.4, symbol:'circle', symbolSize:dates.length<=30?5:0,
      showSymbol:dates.length<=30,
      itemStyle:{color:p.color,borderColor:'#fff',borderWidth:2},
      lineStyle:{color:p.color,width:2.5},
      areaStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[
        {offset:0,color:p.color+'22'},{offset:1,color:p.color+'02'}
      ]}},
      emphasis:{focus:'series',lineStyle:{width:3.5}},
    }))
  });
}

function renderMassSocialCharts() {
  const trendData = ENGData.trend;
  if (!trendData.length) { ['skMass','skSocial'].forEach(hideSk); return; }
  const firstPlt = trendData.find(p=>p.data?.length)||trendData[0];
  const dates    = (firstPlt?.data||[]).map(d=>d.date);
  renderGroupedBars('chMass','skMass', trendData.filter(p=>p.key==='doc'),         dates,'Mass Media');
  renderGroupedBars('chSocial','skSocial', trendData.filter(p=>p.key!=='doc'),     dates,'Social Media');
}

function renderGroupedBars(domId,skelId,platforms,dates,groupLabel) {
  hideSk(skelId);
  if (!platforms.length) { document.getElementById(domId).innerHTML = emptyHtml(`Tidak ada data ${groupLabel}`); return; }
  const totals = platforms.map(p=>({ label:p.label, color:p.color, total:(p.data||[]).reduce((s,d)=>s+d.count,0) })).filter(p=>p.total>0);
  if (!totals.length) { document.getElementById(domId).innerHTML = emptyHtml(`Tidak ada data ${groupLabel}`); return; }

  const chart = ENGCharts.make(domId);
  if (!chart) return;
  chart.setOption({
    animation:true, animationDuration:900, animationEasing:'elasticOut',
    backgroundColor:'#fff',
    tooltip:{
      ...EC_TIP, trigger:'axis',
      axisPointer:{type:'shadow',shadowStyle:{color:'rgba(3,128,71,.06)'}},
      formatter: params => {
        const p   = params[0];
        const tot = totals.reduce((s,t)=>s+t.total,0);
        const pct = tot > 0 ? ((p.value/tot)*100).toFixed(1) : 0;
        return `<div style="font-weight:700;font-size:13px;margin-bottom:6px;">${p.name}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;">
                  <span style="color:#94a3b8;">Interactions</span><span style="font-weight:700;">${numFmt(p.value)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;">
                  <span style="color:#94a3b8;">Share</span><span style="font-weight:700;color:#34d399;">${pct}%</span>
                </div>`;
      }
    },
    grid:{top:24,right:20,bottom:40,left:68},
    xAxis:{type:'category',data:totals.map(p=>p.label),axisLine:{show:false},axisTick:{show:false},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b',interval:0,formatter:v=>v.length>12?v.slice(0,11)+'…':v}
    },
    yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},
      splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
      axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
    },
    series:[{
      type:'bar', barMaxWidth:64,
      data:totals.map(p=>({value:p.total,itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:p.color},{offset:1,color:p.color+'55'}]},borderRadius:[8,8,0,0]}})),
      label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',formatter:p=>numK(p.value)}
    }]
  });
}

/* ══════════════════════════════════════════════════════
   CSV EXPORT (All Platforms)
══════════════════════════════════════════════════════ */
const ENGCsv = {
  _copy(text) {
    navigator.clipboard?.writeText(text).catch(()=>{
      const ta=document.createElement('textarea'); ta.value=text;
      ta.style.cssText='position:fixed;opacity:0;'; document.body.appendChild(ta);
      ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
    });
    alert('CSV data tersalin ke clipboard!');
  },
  copyBar() {
    const tot   = ENGData.grandTotal;
    const lines = ['platform;count;percentage'];
    ENGData.platforms.forEach(p => lines.push(`${p.label};${p.count};${tot>0?(p.count/tot*100).toFixed(1):0}`));
    lines.push(`Total;${tot};100`);
    this._copy(lines.join('\n'));
  },
  copyTrend() {
    const trendData = ENGData.trend;
    if (!trendData.length) return;
    const dates   = (trendData[0]?.data||[]).map(d=>d.date);
    const headers = ['date',...trendData.map(p=>p.label)].join(';');
    const rows    = dates.map((date,i) => [date,...trendData.map(p=>(p.data?.[i]?.count||0))].join(';'));
    this._copy([headers,...rows].join('\n'));
  }
};

/* ══════════════════════════════════════════════════════
   X / TWITTER ENGAGEMENT MODULE
══════════════════════════════════════════════════════ */
const XEng = {
  data: null,
  C: {
    mention:   '#1d9bf0',
    reply:     '#7c3aed',
    retweet:   '#10b981',
    posts:     '#1d9bf0',
    views:     '#7c3aed',
    retweets:  '#10b981',
    favorites: '#f59e0b',
  },

  async load() {
    if (!ENGCfg.pid) {
      ['skXMentionBar','skXMentionPie','skXInterBar','skXInterPie','skXTrend'].forEach(hideSk);
      document.getElementById('xLoadBadge').textContent = 'No Project';
      return;
    }
    try {
      const q   = `project_id=${ENGCfg.pid}&start_date=${ENGCfg.sd}&end_date=${ENGCfg.ed}`;
      const res = await fetch(`/mk/api/media-statistic/x-interaction?${q}`);
      const d   = await res.json();
      if (d.error) throw new Error(d.error);
      this.data = d;
      this.renderAll();
    } catch(err) {
      console.error('XEng load error:', err);
      document.getElementById('xLoadBadge').textContent = 'Error';
      ['skXMentionBar','skXMentionPie','skXInterBar','skXInterPie','skXTrend'].forEach(hideSk);
    }
  },

  renderAll() {
    const d = this.data;
    if (!d) return;
    const m  = d.mentions    || {};
    const iv = d.interaction || {};
    const tr = d.trend       || [];

    document.getElementById('xLoadBadge').textContent = `Total ${numFmt(iv.total||0)}`;
    this._setStatBox('xStatPosts',    iv.posts    ||0, '#1d9bf0');
    this._setStatBox('xStatViews',    iv.views    ||0, '#7c3aed');
    this._setStatBox('xStatRetweets', iv.retweets ||0, '#10b981');
    this._setStatBox('xStatFavs',     iv.favorites||0, '#f59e0b');
    document.getElementById('xTotalInteraction').textContent = numFmt(iv.total||0);
    document.getElementById('xInteractionRate').textContent  = (iv.interaction_rate||0).toFixed(2);
    document.getElementById('xMentionTotalBadge').textContent = `Total ${numFmt(m.total||0)}`;

    this.renderMentionBar(m);
    this.renderInterBar(iv);
    this.renderMentionPie(m);
    this.renderInterPie(iv);
    this.renderTrend(tr);
    this.renderMentionList(m);
    this.renderInterList(iv);
  },

  _setStatBox(id, val, color) {
    const el = document.getElementById(id);
    if (!el) return;
    const vEl = el.querySelector('.x-stat-value');
    if (vEl) { vEl.textContent = numFmt(val); if(color) vEl.style.color = color; }
  },

  _makeBarChart(domId, skelId, items, total) {
    hideSk(skelId);
    const chart = ENGCharts.make(domId);
    if (!chart) return;
    chart.setOption({
      animation:true, animationDuration:800, animationEasing:'elasticOut',
      backgroundColor:'#fff',
      tooltip:{
        ...EC_TIP, trigger:'axis',
        axisPointer:{type:'shadow',shadowStyle:{color:'rgba(29,155,240,.06)'}},
        formatter: p => {
          const v   = p[0].value;
          const pct = total > 0 ? ((v/total)*100).toFixed(1) : 0;
          return `<b style="font-size:13px;">${p[0].name}</b><br>
                  <span style="color:#94a3b8;">Count</span> <b>${numFmt(v)}</b><br>
                  <span style="color:#94a3b8;">Share</span> <b style="color:#34d399;">${pct}%</b>`;
        }
      },
      grid:{top:10,right:10,bottom:28,left:60},
      xAxis:{type:'category',data:items.map(i=>i.name),
        axisLine:{show:false},axisTick:{show:false},
        axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'700',color:'#374151',interval:0}
      },
      yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},
        splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
        axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
      },
      series:[{
        type:'bar', barMaxWidth:60,
        data:items.map(it=>({value:it.val,itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[
          {offset:0,color:it.color},{offset:1,color:it.color+'55'}
        ]},borderRadius:[8,8,0,0]}})),
        label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:11,color:'#64748b',formatter:p=>numK(p.value)}
      }]
    });
  },

  _makePieChart(domId, skelId, data, centerLabel) {
    hideSk(skelId);
    const tot   = data.reduce((s,d)=>s+d.value,0);
    const chart = ENGCharts.make(domId);
    if (!chart) return;
    chart.setOption({
      animation:true, animationDuration:800,
      backgroundColor:'transparent',
      tooltip:{
        ...EC_TIP, trigger:'item',
        formatter: p => `<b>${p.name}</b><br>
          <span style="color:#94a3b8;">Count</span> <b>${numFmt(p.value)}</b><br>
          <span style="color:#94a3b8;">Share</span> <b style="color:#34d399;">${p.percent.toFixed(1)}%</b>`
      },
      legend:{show:true,bottom:0,left:'center',textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#64748b'}},
      series:[{
        type:'pie', radius:['38%','58%'], center:['50%','44%'],
        avoidLabelOverlap:true, minAngle:3,
        itemStyle:{borderColor:'#fff',borderWidth:3,borderRadius:5},
        label:{show:true,formatter:p=>`${p.percent.toFixed(0)}%`,fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'700'},
        labelLine:{show:true,length:8,length2:12,smooth:.4,lineStyle:{color:'#c4cdd8',width:1.2}},
        emphasis:{scale:true,scaleSize:5},
        data: data.filter(d=>d.value>0),
      }],
      graphic:[
        {type:'text',left:'center',top:'34%',z:100,style:{text:numK(tot),fill:'#0f172a',font:"800 20px 'Poppins',sans-serif",textAlign:'center'}},
        {type:'text',left:'center',top:'43%',z:100,style:{text:centerLabel,fill:'#94a3b8',font:"600 8px 'Poppins',sans-serif",textAlign:'center',letterSpacing:1.5}},
      ]
    });
  },

  renderMentionBar(m) {
    this._makeBarChart('chXMentionBar','skXMentionBar',[
      {name:'Mention', val:m.mention||0, color:this.C.mention},
      {name:'Reply',   val:m.reply  ||0, color:this.C.reply  },
      {name:'Retweet', val:m.retweet||0, color:this.C.retweet},
    ], m.total||1);
  },

  renderInterBar(iv) {
    this._makeBarChart('chXInterBar','skXInterBar',[
      {name:'Posts',     val:iv.posts    ||0, color:this.C.posts    },
      {name:'Views',     val:iv.views    ||0, color:this.C.views    },
      {name:'Retweets',  val:iv.retweets ||0, color:this.C.retweets },
      {name:'Favorites', val:iv.favorites||0, color:this.C.favorites},
    ], iv.total||1);
  },

  renderMentionPie(m) {
    this._makePieChart('chXMentionPie','skXMentionPie',[
      {name:'Mention', value:m.mention||0, itemStyle:{color:this.C.mention}},
      {name:'Reply',   value:m.reply  ||0, itemStyle:{color:this.C.reply  }},
      {name:'Retweet', value:m.retweet||0, itemStyle:{color:this.C.retweet}},
    ], 'MENTIONS');
  },

  renderInterPie(iv) {
    this._makePieChart('chXInterPie','skXInterPie',[
      {name:'Posts',     value:iv.posts    ||0, itemStyle:{color:this.C.posts    }},
      {name:'Views',     value:iv.views    ||0, itemStyle:{color:this.C.views    }},
      {name:'Retweets',  value:iv.retweets ||0, itemStyle:{color:this.C.retweets }},
      {name:'Favorites', value:iv.favorites||0, itemStyle:{color:this.C.favorites}},
    ], 'TOTAL');
  },

  renderTrend(tr) {
    hideSk('skXTrend');
    if (!tr.length) {
      document.getElementById('chXTrend').innerHTML = emptyHtml('Data trend tidak tersedia');
      document.getElementById('xTrendBadge').textContent = 'No Data';
      return;
    }
    const dates  = tr.map(d=>d.date);
    const counts = tr.map(d=>d.count);
    const fmtD = d => { const dt=new Date(d+'T00:00:00'); return `${dt.getDate()} ${dt.toLocaleString('id-ID',{month:'short'})}`; };
    document.getElementById('xTrendBadge').textContent = `${fmtD(dates[0])} – ${fmtD(dates[dates.length-1])}`;
    const xLabels = dates.map(d=>{ const dt=new Date(d+'T00:00:00'); return `${dt.getDate()}.${dt.toLocaleString('id-ID',{month:'short'})}`; });

    const chart = ENGCharts.make('chXTrend');
    if (!chart) return;
    chart.setOption({
      animation:true, animationDuration:900, animationEasing:'cubicInOut',
      backgroundColor:'#fff',
      tooltip:{
        ...EC_TIP, trigger:'axis',
        axisPointer:{type:'line',lineStyle:{color:'rgba(29,155,240,.3)',type:'dashed',width:1.5}},
        formatter: params => {
          const di   = params[0]?.dataIndex??0;
          const date = dates[di]||'';
          const fullDt = date?new Date(date+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'}):'';
          return `<div style="font-weight:700;font-size:13px;margin-bottom:6px;">${fullDt}</div>
                  <div style="display:flex;align-items:center;gap:8px;">
                    <span style="width:9px;height:9px;border-radius:50%;background:#1d9bf0;display:inline-block;"></span>
                    <span style="color:#94a3b8;">Interactions</span>
                    <b style="margin-left:auto;">${numFmt(params[0].value)}</b>
                  </div>`;
        }
      },
      grid:{top:24,right:20,bottom:28,left:68},
      xAxis:{type:'category',data:xLabels,boundaryGap:false,
        axisLine:{lineStyle:{color:'#e2e8f0'}},axisTick:{show:false},
        axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,fontWeight:'600',color:'#64748b'}
      },
      yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},
        splitLine:{lineStyle:{color:'#f1f5f9',type:'solid',width:1}},
        axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
      },
      series:[{
        name:'X Interaction', type:'line', data:counts,
        smooth:.4, symbol:'circle', symbolSize:dates.length<=14?6:0, showSymbol:dates.length<=14,
        itemStyle:{color:'#1d9bf0',borderColor:'#fff',borderWidth:2},
        lineStyle:{color:'#1d9bf0',width:3},
        areaStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[
          {offset:0,color:'rgba(29,155,240,.2)'},{offset:1,color:'rgba(29,155,240,.01)'}
        ]}},
        emphasis:{focus:'series',lineStyle:{width:4}},
        markPoint:{
          data:[{type:'max',name:'Peak'}],
          itemStyle:{color:'#1d9bf0'},
          label:{fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'700',color:'#fff'},
          symbolSize:44
        }
      }]
    });
  },

  _renderList(elId, items, total) {
    const el  = document.getElementById(elId);
    if (!el) return;
    const max = Math.max(...items.map(i=>i.val), 1);
    el.innerHTML = items.map(it => {
      const pct  = total > 0 ? ((it.val/total)*100).toFixed(1) : '0.0';
      const barW = ((it.val/max)*100).toFixed(1);
      return `<div class="x-breakdown-row">
        <span class="x-breakdown-dot" style="background:${it.color};"></span>
        <span class="x-breakdown-name">${it.label}</span>
        <div class="x-breakdown-bar-wrap"><div class="x-breakdown-bar" style="width:${barW}%;background:${it.color};"></div></div>
        <span class="x-breakdown-val" style="color:${it.color};">${numFmt(it.val)}</span>
        <span class="x-breakdown-pct">${pct}%</span>
      </div>`;
    }).join('');
  },

  renderMentionList(m) {
    this._renderList('xMentionList',[
      {label:'Mention', val:m.mention||0, color:this.C.mention},
      {label:'Reply',   val:m.reply  ||0, color:this.C.reply  },
      {label:'Retweet', val:m.retweet||0, color:this.C.retweet},
    ], m.total||1);
  },

  renderInterList(iv) {
    this._renderList('xInterList',[
      {label:'Posts',     val:iv.posts    ||0, color:this.C.posts    },
      {label:'Views',     val:iv.views    ||0, color:this.C.views    },
      {label:'Retweets',  val:iv.retweets ||0, color:this.C.retweets },
      {label:'Favorites', val:iv.favorites||0, color:this.C.favorites},
    ], iv.total||1);
  },

  csvTrend() {
    if (!this.data?.trend?.length) return;
    const lines = ['date;interactions'];
    this.data.trend.forEach(d => lines.push(`${d.date};${d.count}`));
    const text = lines.join('\n');
    navigator.clipboard?.writeText(text).catch(()=>{
      const ta=document.createElement('textarea');ta.value=text;
      ta.style.cssText='position:fixed;opacity:0;';document.body.appendChild(ta);
      ta.select();document.execCommand('copy');document.body.removeChild(ta);
    });
    alert('CSV trend X tersalin!');
  },
};

/* ══════════════════════════════════════════════════════
   PAGE CONTROLLER
══════════════════════════════════════════════════════ */
const ENGPage = {
  reload() {
    ENGCharts.disposeAll();
    ENGData.platforms  = [];
    ENGData.trend      = [];
    ENGData.grandTotal = 0;
    XEng.data          = null;
    loadEngagement();
    XEng.load();
  },
  init() {
    loadEngagement();
    XEng.load();
  }
};

document.addEventListener('DOMContentLoaded', () => ENGPage.init());
</script>
@endsection