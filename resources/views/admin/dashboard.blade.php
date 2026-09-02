@extends('admin.layouts.app')

@section('title', 'Admin Dashboard - SMADIMENT')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css" />
<style>
  * { box-sizing: border-box; }

  :root {
    --primary:     #038047;
    --primary-dk:  #026337;
    --primary-lt:  #E8F5EE;
    --dark:        #0F172A;
    --dark-blue:   #273B4A;
    --slate-800:   #1E293B;
    --slate-700:   #334155;
    --slate-600:   #475569;
    --slate-500:   #64748B;
    --slate-400:   #94A3B8;
    --slate-300:   #CBD5E1;
    --slate-200:   #E2E8F0;
    --slate-100:   #F1F5F9;
    --slate-50:    #F8FAFC;
    --white:       #FFFFFF;
    
    --pos:         #059669;
    --pos-lt:      #E8F5EE;
    --neu:         #64748B;
    --neu-lt:      #F1F5F9;
    --neg:         #DC2626;
    --neg-lt:      #FEF2F2;

    --c-news:      #2563EB;
    --c-twit:      #0284C7;
    --c-fb:        #1D4ED8;
    --c-ig:        #DB2777;
    --c-yt:        #DC2626;
    --c-tiktok:    #0F172A;

    --radius:      6px;
    --font:        'Poppins', sans-serif;
  }

  /* ══ TOPBAR ══════════════════════════════════════ */
  .adm-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
  }
  .adm-title-wrap h1 {
    font-family: var(--font);
    font-size: 22px;
    font-weight: 800;
    color: var(--white);
    margin: 0 0 4px;
    letter-spacing: -0.3px;
  }
  .adm-title-wrap p {
    font-family: var(--font);
    font-size: 13px;
    color: rgba(255, 255, 255, 0.75);
    margin: 0;
    font-weight: 500;
  }

  /* Date Filter Form */
  .adm-filter-form {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #1B2B38;
    border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 6px 10px;
    border-radius: var(--radius);
  }
  .adm-filter-form label {
    font-size: 11px;
    font-weight: 700;
    color: var(--slate-400);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-right: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  .adm-date-input {
    background: var(--dark-blue);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: var(--white);
    font-family: var(--font);
    font-size: 12px;
    font-weight: 600;
    padding: 6px 10px;
    border-radius: var(--radius);
    outline: none;
  }
  .adm-date-input:focus {
    border-color: var(--primary);
  }
  .adm-filter-btn {
    background: var(--primary);
    color: var(--white);
    border: none;
    font-family: var(--font);
    font-size: 12px;
    font-weight: 700;
    padding: 7px 14px;
    border-radius: var(--radius);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: background 0.15s ease;
  }
  .adm-filter-btn:hover {
    background: var(--primary-dk);
  }

  /* ══ KPI SUMMARY GRID ═════════════════════════════ */
  .summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
  }
  .kpi-card {
    background: var(--white);
    border: 1px solid var(--slate-200);
    border-left: 4px solid var(--primary);
    border-radius: var(--radius);
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .kpi-card.kpi-blue   { border-left-color: #2563EB; }
  .kpi-card.kpi-amber  { border-left-color: #D97706; }
  .kpi-card.kpi-slate  { border-left-color: #475569; }

  .kpi-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
  }
  .kpi-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .kpi-val {
    font-family: var(--font);
    font-size: 26px;
    font-weight: 800;
    color: var(--dark);
    line-height: 1;
    margin-bottom: 8px;
  }
  .kpi-desc {
    font-size: 11px;
    font-weight: 500;
    color: var(--slate-400);
    display: flex;
    align-items: center;
    gap: 4px;
  }

  /* ══ 2-COLUMN LAYOUT ══════════════════════════════ */
  .adm-main-layout {
    display: grid;
    grid-template-columns: 290px 1fr;
    gap: 20px;
    align-items: start;
  }

  /* ══ LEFT: PROJECT NAVIGATOR ═════════════════════ */
  .proj-sidebar {
    background: var(--white);
    border: 1px solid var(--slate-200);
    border-radius: var(--radius);
    overflow: hidden;
    position: sticky;
    top: 24px;
  }
  .proj-sidebar-hd {
    padding: 14px 16px;
    border-bottom: 1px solid var(--slate-200);
    background: var(--slate-50);
  }
  .proj-sidebar-hd-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }
  .proj-sidebar-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .proj-count-badge {
    background: var(--primary);
    color: var(--white);
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
  }
  .proj-search-input {
    width: 100%;
    background: var(--white);
    border: 1px solid var(--slate-200);
    border-radius: var(--radius);
    padding: 7px 10px;
    font-family: var(--font);
    font-size: 12px;
    color: var(--dark);
    outline: none;
  }
  .proj-search-input:focus {
    border-color: var(--primary);
  }
  .proj-nav-list {
    max-height: calc(100vh - 280px);
    overflow-y: auto;
  }
  .proj-nav-list::-webkit-scrollbar { width: 4px; }
  .proj-nav-list::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 4px; }

  .proj-nav-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-bottom: 1px solid var(--slate-100);
    cursor: pointer;
    transition: background 0.12s ease;
  }
  .proj-nav-item:last-child { border-bottom: none; }
  .proj-nav-item:hover { background: var(--slate-50); }
  .proj-nav-item.active-item {
    background: var(--primary-lt);
    border-left: 3px solid var(--primary);
  }
  .proj-nav-item.hidden-by-search { display: none !important; }

  .proj-nav-left {
    min-width: 0;
    flex: 1;
    margin-right: 10px;
  }
  .proj-nav-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 2px;
  }
  .proj-nav-meta {
    font-size: 11px;
    color: var(--slate-500);
    font-weight: 500;
  }
  .proj-nav-count {
    font-size: 11px;
    font-weight: 700;
    color: var(--primary);
    background: var(--white);
    border: 1px solid var(--slate-200);
    padding: 3px 8px;
    border-radius: 4px;
    white-space: nowrap;
  }

  /* ══ RIGHT: PROJECT CARDS ════════════════════════ */
  .cards-column {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }
  .project-card {
    background: var(--white);
    border: 1px solid var(--slate-200);
    border-radius: var(--radius);
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .project-card.highlighted {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(3, 128, 71, 0.2);
  }

  /* Card Header */
  .p-card-hd {
    padding: 12px 18px;
    background: var(--slate-50);
    border-bottom: 1px solid var(--slate-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }
  .p-card-title-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .p-card-id-badge {
    background: var(--slate-800);
    color: var(--white);
    font-size: 11px;
    font-weight: 700;
    padding: 3px 7px;
    border-radius: 4px;
    letter-spacing: 0.3px;
  }
  .p-card-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
  }
  .p-card-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  .adm-btn-action {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: var(--radius);
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all 0.12s ease;
  }
  .adm-btn-primary {
    background: var(--primary);
    color: var(--white);
  }
  .adm-btn-primary:hover {
    background: var(--primary-dk);
    color: var(--white);
  }
  .adm-btn-secondary {
    background: var(--white);
    color: var(--slate-700);
    border-color: var(--slate-200);
  }
  .adm-btn-secondary:hover {
    background: var(--slate-100);
    color: var(--dark);
    border-color: var(--slate-300);
  }

  /* Card Body */
  .p-card-bd {
    padding: 16px 18px;
  }

  /* Sentiment Breakdown Strip */
  .sent-breakdown-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 14px;
    flex-wrap: wrap;
  }
  .sent-bars-wrap {
    flex: 1;
    min-width: 240px;
  }
  .sent-progress-track {
    height: 7px;
    background: var(--slate-100);
    border-radius: 4px;
    overflow: hidden;
    display: flex;
    margin-bottom: 6px;
  }
  .sent-bar-pos { background: var(--pos); height: 100%; }
  .sent-bar-neu { background: var(--neu); height: 100%; }
  .sent-bar-neg { background: var(--neg); height: 100%; }
  
  .sent-badges-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }
  .sent-pill-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
  }
  .sent-pill-tag.pos { background: var(--pos-lt); color: var(--pos); }
  .sent-pill-tag.neu { background: var(--neu-lt); color: var(--neu); }
  .sent-pill-tag.neg { background: var(--neg-lt); color: var(--neg); }
  .sent-pill-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

  .sent-totals-pill {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
  }
  .sent-totals-num {
    font-size: 18px;
    font-weight: 800;
    color: var(--dark);
    line-height: 1;
  }
  .sent-totals-lbl {
    font-size: 10px;
    font-weight: 700;
    color: var(--slate-400);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-top: 3px;
  }

  /* Platform Badges Row */
  .platform-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
    margin-bottom: 16px;
    padding: 10px 0;
    border-top: 1px solid var(--slate-100);
    border-bottom: 1px solid var(--slate-100);
  }
  .plat-box {
    background: var(--slate-50);
    border: 1px solid var(--slate-200);
    border-radius: var(--radius);
    padding: 8px 6px;
    text-align: center;
  }
  .plat-box-hd {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  .plat-box-val {
    font-size: 13px;
    font-weight: 700;
    color: var(--dark);
  }

  /* Chart Container */
  .chart-area {
    position: relative;
    height: 210px;
    width: 100%;
  }

  /* Empty State */
  .adm-empty-state {
    background: var(--white);
    border: 1px solid var(--slate-200);
    border-radius: var(--radius);
    padding: 60px 20px;
    text-align: center;
    color: var(--slate-400);
  }
  .adm-empty-state i { font-size: 40px; margin-bottom: 8px; display: block; }
  .adm-empty-state h3 { font-size: 16px; font-weight: 700; color: var(--dark); margin-bottom: 4px; }

  /* ══ RESPONSIVE ══════════════════════════════════ */
  @media (max-width: 1200px) {
    .summary-grid { grid-template-columns: repeat(2, 1fr); }
    .platform-grid { grid-template-columns: repeat(3, 1fr); }
  }
  @media (max-width: 900px) {
    .adm-main-layout { grid-template-columns: 1fr; }
    .proj-sidebar { position: static; }
  }
  @media (max-width: 600px) {
    .summary-grid { grid-template-columns: 1fr; }
    .platform-grid { grid-template-columns: repeat(2, 1fr); }
    .adm-topbar { flex-direction: column; align-items: stretch; }
    .adm-filter-form { flex-direction: column; align-items: stretch; }
  }
</style>
@endsection

@section('content')

@php
  $start = $dateRange['start'] ?? request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $end   = $dateRange['end'] ?? request()->get('end_date', now()->format('Y-m-d'));
  
  $totalProjects = count($projects);
  $totalMentions = collect($projects)->sum(fn($p) => $p['stats']['all'] ?? 0);
  
  $totalNews = collect($projects)->sum(fn($p) => $p['stats']['news'] ?? 0);
  $totalSocial = collect($projects)->sum(fn($p) => 
    ($p['stats']['twit'] ?? 0) + 
    ($p['stats']['fb'] ?? 0) + 
    ($p['stats']['ig'] ?? 0) + 
    ($p['stats']['yt'] ?? 0) + 
    ($p['stats']['tiktok'] ?? 0)
  );

  $totalPos = collect($projects)->sum(fn($p) => collect($p['timeline']['sentiment']['positive'] ?? [])->sum());
  $totalNeu = collect($projects)->sum(fn($p) => collect($p['timeline']['sentiment']['neutral'] ?? [])->sum());
  $totalNeg = collect($projects)->sum(fn($p) => collect($p['timeline']['sentiment']['negative'] ?? [])->sum());
  $sumSentiment = $totalPos + $totalNeu + $totalNeg;
  $posPct = $sumSentiment > 0 ? round(($totalPos / $sumSentiment) * 100, 1) : 0;
  $negPct = $sumSentiment > 0 ? round(($totalNeg / $sumSentiment) * 100, 1) : 0;
@endphp

{{-- ── 1. Top Bar & Date Filter ── --}}
<div class="adm-topbar">
  <div class="adm-title-wrap">
    <h1>Admin Project Overview</h1>
    <p>Monitoring & analytics overview across all client projects</p>
  </div>

  <form method="GET" action="{{ route('admin.dashboard') }}" class="adm-filter-form">
    <label><i class="ph ph-calendar-blank"></i> Periode:</label>
    <input type="date" name="start_date" class="adm-date-input" value="{{ $start }}" required />
    <span style="color:rgba(255,255,255,0.4);font-weight:700;">–</span>
    <input type="date" name="end_date" class="adm-date-input" value="{{ $end }}" required />
    <button type="submit" class="adm-filter-btn">
      <i class="ph ph-funnel"></i> Filter
    </button>
  </form>
</div>

{{-- ── 2. KPI Summary Strip (Clean & High Contrast) ── --}}
<div class="summary-grid">
  {{-- Card 1: Total Projects --}}
  <div class="kpi-card">
    <div class="kpi-label-row">
      <span class="kpi-label"><i class="ph ph-folder-notch-open" style="font-size:14px;color:var(--primary);"></i> Total Projects</span>
    </div>
    <div class="kpi-val">{{ number_format($totalProjects) }}</div>
    <div class="kpi-desc">
      <i class="ph ph-check-circle" style="color:#059669;"></i>
      <span>Semua project terdaftar aktif</span>
    </div>
  </div>

  {{-- Card 2: Total Items / Mentions --}}
  <div class="kpi-card kpi-blue">
    <div class="kpi-label-row">
      <span class="kpi-label"><i class="ph ph-chart-bar" style="font-size:14px;color:#2563EB;"></i> Total Mentions</span>
    </div>
    <div class="kpi-val">{{ number_format($totalMentions) }}</div>
    <div class="kpi-desc">
      <i class="ph ph-clock"></i>
      <span>{{ date('d M', strtotime($start)) }} – {{ date('d M Y', strtotime($end)) }}</span>
    </div>
  </div>

  {{-- Card 3: Sentiment Quality --}}
  <div class="kpi-card kpi-amber">
    <div class="kpi-label-row">
      <span class="kpi-label"><i class="ph ph-thumbs-up" style="font-size:14px;color:#D97706;"></i> Sentiment Quality</span>
    </div>
    <div class="kpi-val" style="font-size:22px;display:flex;align-items:baseline;gap:8px;">
      <span style="color:#059669;">{{ $posPct }}% <span style="font-size:11px;font-weight:600;color:var(--slate-400);">Pos</span></span>
      <span style="color:#DC2626;">{{ $negPct }}% <span style="font-size:11px;font-weight:600;color:var(--slate-400);">Neg</span></span>
    </div>
    <div class="kpi-desc">
      <i class="ph ph-chart-pie-slice"></i>
      <span>Rasio sentimen keseluruhan</span>
    </div>
  </div>

  {{-- Card 4: Media Composition --}}
  <div class="kpi-card kpi-slate">
    <div class="kpi-label-row">
      <span class="kpi-label"><i class="ph ph-share-network" style="font-size:14px;color:#475569;"></i> Media Composition</span>
    </div>
    <div class="kpi-val" style="font-size:19px;display:flex;align-items:baseline;gap:6px;">
      <span>{{ number_format($totalSocial) }} <span style="font-size:11px;font-weight:600;color:var(--slate-400);">Social</span></span>
      <span style="color:var(--slate-300);">/</span>
      <span>{{ number_format($totalNews) }} <span style="font-size:11px;font-weight:600;color:var(--slate-400);">Mass</span></span>
    </div>
    <div class="kpi-desc">
      <i class="ph ph-globe"></i>
      <span>6 platform aktif termonitor</span>
    </div>
  </div>
</div>

{{-- ── 3. Main Two-Column View ── --}}
<div class="adm-main-layout">

  {{-- Left Column: Project Navigator --}}
  <aside class="proj-sidebar">
    <div class="proj-sidebar-hd">
      <div class="proj-sidebar-hd-row">
        <span class="proj-sidebar-title">
          <i class="ph ph-list-dashes"></i> Projects List
        </span>
        <span class="proj-count-badge" id="visibleProjCount">{{ $totalProjects }}</span>
      </div>
      <input 
        type="text" 
        id="projSearchInput" 
        class="proj-search-input" 
        placeholder="Cari nama project..." 
        autocomplete="off"
      />
    </div>

    <div class="proj-nav-list" id="projNavList">
      @forelse($projects as $project)
      <div class="proj-nav-item" data-id="{{ $project['id'] }}" data-name="{{ strtolower($project['name'] ?? $project['title'] ?? '') }}">
        <div class="proj-nav-left">
          <span class="proj-nav-name">{{ $project['name'] ?? $project['title'] ?? 'Unnamed' }}</span>
          <span class="proj-nav-meta">ID #{{ $project['id'] }}</span>
        </div>
        <span class="proj-nav-count">{{ number_format($project['stats']['all'] ?? 0) }}</span>
      </div>
      @empty
      <div style="padding:28px 16px;text-align:center;font-size:12px;color:var(--slate-400);">
        Belum ada project tersedia.
      </div>
      @endforelse
    </div>
  </aside>

  {{-- Right Column: Detailed Project Cards --}}
  <div class="cards-column">
    @forelse($projects as $project)
      @php
        $pId = $project['id'];
        $pName = $project['name'] ?? $project['title'] ?? 'Unnamed Project';
        $pStats = $project['stats'] ?? [];
        $pTotal = $pStats['all'] ?? 0;

        $pPos = collect($project['timeline']['sentiment']['positive'] ?? [])->sum();
        $pNeu = collect($project['timeline']['sentiment']['neutral'] ?? [])->sum();
        $pNeg = collect($project['timeline']['sentiment']['negative'] ?? [])->sum();
        $pSentTot = $pPos + $pNeu + $pNeg;
        
        $pPosPct = $pSentTot > 0 ? round(($pPos / $pSentTot) * 100, 1) : 0;
        $pNeuPct = $pSentTot > 0 ? round(($pNeu / $pSentTot) * 100, 1) : 0;
        $pNegPct = $pSentTot > 0 ? round(($pNeg / $pSentTot) * 100, 1) : 0;
      @endphp

      <div class="project-card" id="card-{{ $pId }}">
        {{-- Card Header --}}
        <div class="p-card-hd">
          <div class="p-card-title-wrap">
            <span class="p-card-id-badge">#{{ $pId }}</span>
            <h3 class="p-card-title">{{ $pName }}</h3>
          </div>

          <div class="p-card-actions">
            <a href="{{ route('mk.sentiment', ['project_id' => $pId, 'start_date' => $start, 'end_date' => $end]) }}" class="adm-btn-action adm-btn-primary" title="Buka Sentiment Analytics">
              <i class="ph ph-chart-donut"></i> Sentiment
            </a>
            <a href="{{ route('mk.data-overview', ['project_id' => $pId, 'start_date' => $start, 'end_date' => $end]) }}" class="adm-btn-action adm-btn-secondary" title="Buka Data Overview">
              <i class="ph ph-squares-four"></i> Overview
            </a>
            <a href="{{ route('mk.topic-map', ['project_id' => $pId, 'start_date' => $start, 'end_date' => $end]) }}" class="adm-btn-action adm-btn-secondary" title="Buka Word Cloud & Topik">
              <i class="ph ph-cloud"></i> Topics
            </a>
            <a href="{{ route('mk.media-statistic', ['project_id' => $pId, 'start_date' => $start, 'end_date' => $end]) }}" class="adm-btn-action adm-btn-secondary" title="Buka Statistik Media">
              <i class="ph ph-newspaper"></i> Media Stats
            </a>
          </div>
        </div>

        {{-- Card Body --}}
        <div class="p-card-bd">
          {{-- Row 1: Sentiment Segmented Bar & Totals --}}
          <div class="sent-breakdown-row">
            <div class="sent-bars-wrap">
              <div class="sent-progress-track">
                <div class="sent-bar-pos" style="width: {{ $pPosPct }}%;" title="Positif: {{ $pPosPct }}%"></div>
                <div class="sent-bar-neu" style="width: {{ $pNeuPct }}%;" title="Netral: {{ $pNeuPct }}%"></div>
                <div class="sent-bar-neg" style="width: {{ $pNegPct }}%;" title="Negatif: {{ $pNegPct }}%"></div>
              </div>
              <div class="sent-badges-row">
                <span class="sent-pill-tag pos">
                  <span class="sent-pill-dot"></span> Positif: {{ $pPosPct }}% ({{ number_format($pPos) }})
                </span>
                <span class="sent-pill-tag neu">
                  <span class="sent-pill-dot"></span> Netral: {{ $pNeuPct }}% ({{ number_format($pNeu) }})
                </span>
                <span class="sent-pill-tag neg">
                  <span class="sent-pill-dot"></span> Negatif: {{ $pNegPct }}% ({{ number_format($pNeg) }})
                </span>
              </div>
            </div>

            <div class="sent-totals-pill">
              <span class="sent-totals-num">{{ number_format($pTotal) }}</span>
              <span class="sent-totals-lbl">Total Mentions</span>
            </div>
          </div>

          {{-- Row 2: Platform Distribution Badges --}}
          <div class="platform-grid">
            <div class="plat-box">
              <div class="plat-box-hd" style="color:var(--c-news);"><i class="ph ph-article"></i> News</div>
              <div class="plat-box-val">{{ number_format($pStats['news'] ?? 0) }}</div>
            </div>
            <div class="plat-box">
              <div class="plat-box-hd" style="color:var(--c-twit);"><i class="ph ph-twitter-logo"></i> X / Twit</div>
              <div class="plat-box-val">{{ number_format($pStats['twit'] ?? 0) }}</div>
            </div>
            <div class="plat-box">
              <div class="plat-box-hd" style="color:var(--c-fb);"><i class="ph ph-facebook-logo"></i> Facebook</div>
              <div class="plat-box-val">{{ number_format($pStats['fb'] ?? 0) }}</div>
            </div>
            <div class="plat-box">
              <div class="plat-box-hd" style="color:var(--c-ig);"><i class="ph ph-instagram-logo"></i> Instagram</div>
              <div class="plat-box-val">{{ number_format($pStats['ig'] ?? 0) }}</div>
            </div>
            <div class="plat-box">
              <div class="plat-box-hd" style="color:var(--c-yt);"><i class="ph ph-youtube-logo"></i> YouTube</div>
              <div class="plat-box-val">{{ number_format($pStats['yt'] ?? 0) }}</div>
            </div>
            <div class="plat-box">
              <div class="plat-box-hd" style="color:var(--c-tiktok);"><i class="ph ph-tiktok-logo"></i> TikTok</div>
              <div class="plat-box-val">{{ number_format($pStats['tiktok'] ?? 0) }}</div>
            </div>
          </div>

          {{-- Row 3: Daily Timeline Chart --}}
          <div class="chart-area">
            <canvas id="chart-{{ $pId }}"></canvas>
          </div>
        </div>
      </div>
    @empty
      <div class="adm-empty-state">
        <i class="ph ph-folder-open"></i>
        <h3>Tidak Ada Project</h3>
        <p>Belum ada data project yang tersedia untuk ditampilkan.</p>
      </div>
    @endforelse
  </div>

</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // ── 1. Live Search Project Navigator ──
  const searchInput = document.getElementById('projSearchInput');
  const navItems    = document.querySelectorAll('.proj-nav-item');
  const countBadge  = document.getElementById('visibleProjCount');

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase().trim();
      let visible = 0;

      navItems.forEach(item => {
        const name = item.dataset.name || '';
        const id   = item.dataset.id || '';
        const match = name.includes(q) || id.includes(q);
        
        item.classList.toggle('hidden-by-search', !match);
        if (match) visible++;
      });

      if (countBadge) countBadge.textContent = visible;
    });
  }

  // ── 2. Click Sidebar to Scroll & Highlight Card ──
  navItems.forEach(item => {
    item.addEventListener('click', () => {
      navItems.forEach(n => n.classList.remove('active-item'));
      item.classList.add('active-item');

      const card = document.getElementById(`card-${item.dataset.id}`);
      if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        card.classList.add('highlighted');
        setTimeout(() => card.classList.remove('highlighted'), 2000);
      }
    });
  });

  // ── 3. Render Chart.js for all Projects ──
  renderAllCharts();
});

function renderAllCharts() {
  const projectsData = @json($projects);

  projectsData.forEach(project => {
    const ctx = document.getElementById(`chart-${project.id}`);
    if (!ctx) return;

    const tl = project.timeline || {};
    let labels = tl.dates || [];
    let allData, posData, neuData, negData;

    if (!labels.length) {
      labels   = ['H-6', 'H-5', 'H-4', 'H-3', 'H-2', 'Kemarin', 'Hari Ini'];
      allData  = [0, 0, 0, 0, 0, 0, 0];
      posData  = [0, 0, 0, 0, 0, 0, 0];
      neuData  = [0, 0, 0, 0, 0, 0, 0];
      negData  = [0, 0, 0, 0, 0, 0, 0];
    } else {
      allData = tl.values || [];
      if (tl.sentiment?.positive) {
        posData = tl.sentiment.positive;
        neuData = tl.sentiment.neutral;
        negData = tl.sentiment.negative;
      } else {
        posData = allData.map(v => Math.round(v * 0.40));
        neuData = allData.map(v => Math.round(v * 0.40));
        negData = allData.map(v => Math.round(v * 0.20));
      }
    }

    const ds = (label, data, color, dashed = false) => ({
      label,
      data,
      borderColor: color,
      backgroundColor: 'transparent',
      borderWidth: dashed ? 1.8 : 2.4,
      borderDash: dashed ? [4, 3] : [],
      tension: 0.35,
      pointRadius: 4,
      pointHoverRadius: 7,
      pointBackgroundColor: color,
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointHitRadius: 10,
      fill: false,
    });

    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [
          ds('Total',    allData, '#2563EB'),
          ds('Positif',  posData, '#059669'),
          ds('Netral',   neuData, '#64748B', true),
          ds('Negatif',  negData, '#DC2626', true),
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: { top: 6, right: 6, bottom: 0, left: 0 } },
        hover: {
          mode: 'nearest',
          intersect: true
        },
        plugins: {
          legend: {
            position: 'top',
            align: 'end',
            labels: {
              usePointStyle: true,
              pointStyle: 'circle',
              padding: 12,
              font: { size: 11, weight: '600', family: "'Poppins', sans-serif" },
              color: '#475569',
              boxWidth: 7,
              boxHeight: 7
            }
          },
          tooltip: {
            mode: 'nearest',
            intersect: true,
            backgroundColor: '#0F172A',
            titleColor: '#94A3B8',
            bodyColor: '#FFFFFF',
            borderColor: '#334155',
            borderWidth: 1,
            padding: 8,
            cornerRadius: 4,
            displayColors: true,
            boxWidth: 7,
            boxHeight: 7,
            boxPadding: 4,
            titleFont: { size: 10, weight: '600', family: "'Poppins', sans-serif" },
            bodyFont: { size: 12, weight: '700', family: "'Poppins', sans-serif" },
            callbacks: {
              title: (items) => items[0]?.label ? items[0].label : '',
              label: (ctx) => ` ${ctx.dataset.label}: ${Number(ctx.parsed.y).toLocaleString()} mentions`
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            border: { display: false },
            ticks: {
              font: { size: 10, weight: '600', family: "'Poppins', sans-serif" },
              color: '#94A3B8',
              padding: 6,
              maxRotation: 0,
              autoSkip: true,
              maxTicksLimit: 7
            }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(226, 232, 240, 0.7)', drawBorder: false },
            border: { display: false },
            ticks: {
              font: { size: 10, weight: '600', family: "'Poppins', sans-serif" },
              color: '#94A3B8',
              padding: 8,
              maxTicksLimit: 5,
              callback: (v) => v >= 1000 ? (v / 1000) + 'k' : v
            }
          }
        },
        interaction: {
          mode: 'nearest',
          intersect: true
        }
      }
    });
  });
}
</script>
@endsection