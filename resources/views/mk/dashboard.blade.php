@extends('mk.layouts.app')

@section('title', 'Dashboard - SMADIMENT')

@section('styles')
<style>
/* ══ Use Bootstrap/Mantis primary vars so custom theme works ══ */
:root {
    --dash-primary     : var(--bs-primary, #4361EE);
    --dash-primary-rgb : var(--bs-primary-rgb, 67, 97, 238);
    --dash-primary-lt  : rgba(var(--dash-primary-rgb, 67,97,238), .10);
    --green            : #10B981;
    --green-light      : #ECFDF5;
    --red              : #EF4444;
    --red-light        : #FEF2F2;
    --slate-50         : #F8FAFC;
    --slate-100        : #F1F5F9;
    --slate-200        : #E2E8F0;
    --slate-300        : #CBD5E1;
    --slate-400        : #94A3B8;
    --slate-500        : #64748B;
    --slate-600        : #475569;
    --slate-700        : #334155;
    --slate-800        : #1E293B;
    --slate-900        : #0F172A;
    --radius           : 8px;
    --radius-sm        : 5px;
    --shadow-sm        : 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
    --shadow-md        : 0 4px 14px rgba(15,23,42,.08);
    --shadow-lg        : 0 10px 30px rgba(15,23,42,.12);
}

/* ══ Animations ══════════════════════════════════════ */
@keyframes fadeUp {
    from { opacity:0; transform:translateY(12px); }
    to   { opacity:1; transform:translateY(0); }
}
@keyframes fadeIn {
    from { opacity:0; }
    to   { opacity:1; }
}
@keyframes slideRight {
    from { opacity:0; transform:translateX(-12px); }
    to   { opacity:1; transform:translateX(0); }
}
.fade-up    { animation: fadeUp .38s ease-out both; }
.fade-up-d1 { animation-delay:.05s }
.fade-up-d2 { animation-delay:.10s }
.fade-up-d3 { animation-delay:.15s }
.fade-up-d4 { animation-delay:.20s }
.fade-in    { animation: fadeIn .3s ease-out both; }

/* ══ Welcome Bar ═════════════════════════════════════ */
.welcome-bar {
    display: flex; align-items: center;
    justify-content: space-between; flex-wrap: wrap; gap: 10px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    margin-bottom: 20px;
}
.welcome-left { display:flex; align-items:center; gap:12px; }
.welcome-avatar {
    width:40px; height:40px; border-radius:var(--radius-sm);
    background: var(--dash-primary);
    background: linear-gradient(135deg, var(--dash-primary), var(--bs-primary-darker, #1a2fa0));
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:15px; font-weight:800;
    flex-shrink:0; letter-spacing:-.5px;
}
.welcome-name  { font-size:14px; font-weight:700; color:var(--slate-900); margin:0 0 2px; line-height:1; }
.welcome-range { font-size:11px; color:var(--slate-400); font-weight:500; display:flex; align-items:center; gap:4px; }
.welcome-right { display:flex; align-items:center; gap:8px; }
.date-chip {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 10px; background:var(--slate-50);
    border:1px solid var(--slate-200); border-radius:var(--radius-sm);
    font-size:11px; font-weight:600; color:var(--slate-600);
}
.btn-logout {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 12px; background:var(--red-light); color:var(--red);
    border:1px solid #FECACA; border-radius:var(--radius-sm);
    font-size:12px; font-weight:700; cursor:pointer; transition:all .15s;
}
.btn-logout:hover { background:var(--red); color:#fff; border-color:var(--red); }

/* ══ KPI Cards ═══════════════════════════════════════ */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 14px;
    margin-bottom: 20px;
}
@media (max-width:1100px) { .kpi-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width:560px)  { .kpi-grid { grid-template-columns: 1fr; } }

.kpi-card {
    border-radius: var(--radius);
    padding: 18px 20px;
    position: relative; overflow: hidden;
    border: none;
    transition: transform .18s, box-shadow .18s;
}
.kpi-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-lg) !important; }

/* Theme-aware KPI colors — use bs-primary so custom theme works */
.kpi-card.kpi-primary {
    background: linear-gradient(135deg, var(--bs-primary, #4361EE) 0%, var(--bs-primary-text, #2540c4) 100%);
}
.kpi-card.kpi-teal  { background: linear-gradient(135deg,#06B6D4,#0E7490); }
.kpi-card.kpi-green { background: linear-gradient(135deg,#10B981,#059669); }
.kpi-card.kpi-red   { background: linear-gradient(135deg,#EF4444,#B91C1C); }

.kpi-icon-wrap {
    width:34px; height:34px; border-radius:var(--radius-sm);
    background:rgba(255,255,255,.18);
    display:flex; align-items:center; justify-content:center;
    margin-bottom:10px; font-size:17px; color:#fff;
}
.kpi-label    { font-size:10px; font-weight:700; color:rgba(255,255,255,.65); text-transform:uppercase; letter-spacing:.7px; margin-bottom:3px; }
.kpi-value    { font-size:26px; font-weight:800; color:#fff; line-height:1; letter-spacing:-1px; margin-bottom:5px; }
.kpi-sub      { font-size:11px; color:rgba(255,255,255,.60); font-weight:600; display:flex; align-items:center; gap:4px; }
.kpi-watermark{ position:absolute; right:-6px; bottom:-6px; font-size:72px; color:rgba(255,255,255,.07); pointer-events:none; line-height:1; }

/* ══ Main Layout ═════════════════════════════════════ */
.dash-layout {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 16px;
    align-items: start;
}
@media (max-width:992px) { .dash-layout { grid-template-columns:1fr; } }

/* ── Sidebar ── */
.proj-sidebar {
    background:#fff; border-radius:var(--radius);
    border:1px solid var(--slate-200);
    overflow:hidden; position:sticky; top:80px; /* clear header */
    box-shadow:var(--shadow-sm);
    animation: slideRight .4s ease-out both;
}
.proj-sidebar-head {
    padding:10px 14px; border-bottom:1px solid var(--slate-100);
    display:flex; align-items:center; justify-content:space-between;
    background:var(--slate-50);
}
.proj-sidebar-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:var(--slate-600); }
.proj-count-pill    { background:var(--dash-primary); color:#fff; font-size:10px; font-weight:700; padding:1px 7px; border-radius:3px; }

.sidebar-search { padding:7px 8px; border-bottom:1px solid var(--slate-100); }
.sidebar-search input {
    width:100%; padding:5px 10px 5px 28px;
    border:1px solid var(--slate-200); border-radius:var(--radius-sm);
    font-size:12px; color:var(--slate-700);
    background:var(--slate-50) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 8px center;
    outline:none; transition:border-color .15s;
}
.sidebar-search input:focus { border-color:var(--dash-primary); background-color:#fff; }

.proj-list { padding:5px; max-height:560px; overflow-y:auto; }
.proj-list::-webkit-scrollbar { width:3px; }
.proj-list::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

.proj-item {
    display:flex; align-items:center; gap:9px;
    padding:8px 9px; border-radius:var(--radius-sm);
    cursor:pointer; transition:background .14s, transform .14s; margin-bottom:1px;
}
.proj-item:hover { background:var(--dash-primary-lt); transform:translateX(2px); }
.proj-item:hover .proj-item-dot  { background:var(--dash-primary); }
.proj-item:hover .proj-item-name { color:var(--dash-primary); }
.proj-item:hover .proj-item-arrow{ opacity:1; }
.proj-item.active-sidebar   { background:var(--dash-primary-lt); }
.proj-item.active-sidebar .proj-item-dot { background:var(--dash-primary); }

.proj-item-dot  { width:6px; height:6px; border-radius:50%; background:var(--slate-300); flex-shrink:0; transition:background .14s; }
.proj-item-text { flex:1; min-width:0; }
.proj-item-name { font-size:12px; font-weight:700; color:var(--slate-800); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; transition:color .14s; }
.proj-item-meta { font-size:10px; color:var(--slate-400); font-weight:500; display:block; margin-top:1px; }
.proj-item-arrow{ opacity:0; font-size:10px; color:var(--dash-primary); transition:opacity .14s; flex-shrink:0; }

/* ── Charts Area ── */
.charts-area { display:flex; flex-direction:column; gap:16px; }

/* ══ Project Card ════════════════════════════════════ */
.proj-card {
    background:#fff; border-radius:var(--radius);
    border:1px solid var(--slate-200);
    overflow:hidden; box-shadow:var(--shadow-sm);
    opacity:0; transform:translateY(14px);
    transition:opacity .4s ease, transform .4s ease, border-color .2s, box-shadow .2s;
}
.proj-card.card-visible { opacity:1; transform:translateY(0); }
.proj-card.highlighted  { border-color:var(--dash-primary); box-shadow:0 0 0 3px var(--dash-primary-lt), var(--shadow-md); }

.proj-card-header { padding:14px 18px 12px; border-bottom:1px solid var(--slate-200); background:#fff; }
.proj-card-title-row { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
.proj-card-left { display:flex; align-items:center; gap:8px; }

.proj-status-dot {
    width:8px; height:8px; border-radius:50%;
    background:var(--dash-primary); flex-shrink:0;
    box-shadow:0 0 0 3px var(--dash-primary-lt);
    animation:pulseP 2.5s infinite;
}
@keyframes pulseP {
    0%,100% { box-shadow:0 0 0 3px var(--dash-primary-lt); }
    50%      { box-shadow:0 0 0 6px transparent; }
}
.proj-card-name { font-size:13.5px; font-weight:700; color:var(--slate-900); letter-spacing:-.2px; margin:0 0 1px; }
.proj-card-sub  { font-size:11px; color:var(--slate-400); font-weight:500; }
.proj-card-actions { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }

.btn-primary-sm {
    display:inline-flex; align-items:center; gap:4px;
    padding:6px 13px; background:var(--dash-primary);
    border:none; border-radius:var(--radius-sm);
    color:#fff; font-size:11.5px; font-weight:600; text-decoration:none;
    transition:filter .14s, box-shadow .14s;
}
.btn-primary-sm:hover { filter:brightness(1.12); color:#fff; box-shadow:0 3px 10px var(--dash-primary-lt); }

.btn-icon-sm {
    width:30px; height:30px;
    display:inline-flex; align-items:center; justify-content:center;
    border:1px solid var(--slate-200); border-radius:var(--radius-sm);
    background:#fff; color:var(--slate-500); text-decoration:none; font-size:14px;
    transition:all .14s; flex-shrink:0;
}
.btn-icon-sm:hover { background:var(--dash-primary); border-color:var(--dash-primary); color:#fff; }

.meta-row { display:flex; flex-wrap:wrap; gap:5px; }
.meta-pill {
    display:inline-flex; align-items:center; gap:3px;
    padding:2px 8px; border-radius:3px;
    font-size:10px; font-weight:700; letter-spacing:.2px;
}
.meta-pill.date  { background:var(--dash-primary-lt); color:var(--dash-primary); }
.meta-pill.type  { background:var(--slate-100); color:var(--slate-500); }
.meta-pill.media { background:var(--green-light); color:#059669; }

.proj-card-body { padding:14px 18px 18px; }

.stats-row {
    display:flex; flex-wrap:wrap; gap:16px;
    padding-bottom:12px; margin-bottom:12px;
    border-bottom:1px solid var(--slate-100);
    align-items:center;
}
.stat-chip { display:flex; align-items:center; gap:5px; font-size:11.5px; font-weight:600; color:var(--slate-500); }
.stat-chip-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.stat-chip strong { font-size:13px; font-weight:800; color:var(--slate-800); }

.chart-subheader { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
.chart-subheader-label { font-size:11px; font-weight:700; color:var(--slate-500); text-transform:uppercase; letter-spacing:.5px; }
.chart-subheader-right { font-size:10px; color:var(--slate-400); font-weight:500; }

.chart-container { height:220px; position:relative; }
.chart-loading {
    position:absolute; inset:0;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:8px; background:#fff; z-index:2; transition:opacity .3s;
}
.chart-loading.hidden { opacity:0; pointer-events:none; }
.spin-ring {
    width:26px; height:26px;
    border:2.5px solid var(--slate-100);
    border-top-color:var(--dash-primary);
    border-radius:50%; animation:spin .65s linear infinite;
}
@keyframes spin { to { transform:rotate(360deg); } }
.chart-loading span { font-size:11px; font-weight:600; color:var(--slate-400); }

.chart-empty { height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; color:var(--slate-400); font-size:12px; font-weight:600; }
.chart-empty i { font-size:34px; color:var(--slate-300); display:block; }

/* ══ Skeleton ════════════════════════════════════════ */
.skeleton-wrap { display:flex; flex-direction:column; gap:16px; }
.skeleton-card { background:#fff; border-radius:var(--radius); border:1px solid var(--slate-200); padding:16px; }
.sk-block {
    border-radius:4px;
    background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);
    background-size:200% 100%; animation:shimmer 1.4s infinite;
}
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }

/* ══ Misc ════════════════════════════════════════════ */
.empty-state { text-align:center; padding:70px 24px; background:#fff; border-radius:var(--radius); border:2px dashed var(--slate-200); }
.empty-state i  { font-size:48px; color:var(--slate-200); display:block; margin-bottom:10px; }
.empty-state h5 { font-size:15px; font-weight:700; color:var(--slate-500); margin-bottom:5px; }
.empty-state p  { font-size:12px; color:var(--slate-400); margin:0; }

.lazy-sentinel { height:1px; }

.scroll-top-btn {
    position:fixed; bottom:20px; right:70px; /* beri jarak dari customizer btn */
    width:34px; height:34px;
    background:var(--dash-primary); border-radius:var(--radius-sm);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:15px; box-shadow:var(--shadow-md);
    cursor:pointer; opacity:0; pointer-events:none;
    transition:opacity .2s, transform .2s; z-index:998; border:none;
}
.scroll-top-btn.visible { opacity:1; pointer-events:all; }
.scroll-top-btn:hover { transform:translateY(-2px); }

/* ══ Header gap fix ══════════════════════════════════ */
/* Tambah padding-top ke pc-content supaya content tidak nempel header */
.pc-content { padding-top: 20px !important; }
</style>
@endsection

@section('page-title', 'Dashboard')

@section('content')

@php
    $totalMentions = collect($projects)->sum('total_mentions');
    $totalPositive = collect($projects)->sum(fn($p) => $p['sentiment_summary']['positive'] ?? 0);
    $totalNeutral  = collect($projects)->sum(fn($p) => $p['sentiment_summary']['neutral']  ?? 0);
    $totalNegative = collect($projects)->sum(fn($p) => $p['sentiment_summary']['negative'] ?? 0);
    $projectCount  = count($projects);
    $initials      = strtoupper(substr(auth()->user()->name ?? 'U', 0, 2));
@endphp

<script>
    const CHART_DATA_URL = '{{ url("/mk/dashboard/chart-data") }}';
    const START_DATE     = '{{ $startDate }}';
    const END_DATE       = '{{ $endDate }}';
    const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const PROJECT_TIMELINES = {};
</script>

{{-- ══ Welcome Bar ══ --}}
<div class="welcome-bar fade-up">
    <div class="welcome-left">
        <div class="welcome-avatar">{{ $initials }}</div>
        <div>
            <p class="welcome-name">Welcome, {{ auth()->user()->name ?? 'User' }}!</p>
            <div class="welcome-range">
                <i class="ph ph-calendar-blank"></i>
                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                &ndash;
                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>
        </div>
    </div>
    <div class="welcome-right">
        <div class="date-chip">
            <i class="ph ph-clock"></i>
            <span id="mkDateLabel"></span>
        </div>
        <form method="POST" action="{{ route('user.logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="ph ph-sign-out"></i> Logout
            </button>
        </form>
    </div>
</div>

{{-- ══ KPI Cards ══ --}}
<div class="kpi-grid">
    <div class="kpi-card kpi-primary fade-up fade-up-d1">
        <div class="kpi-icon-wrap"><i class="ph ph-folder-open"></i></div>
        <div class="kpi-label">My Projects</div>
        <div class="kpi-value">{{ $projectCount }}</div>
        <div class="kpi-sub"><i class="ph ph-circle-dashed"></i> Active monitoring</div>
        <i class="ph ph-folder-open kpi-watermark"></i>
    </div>
    <div class="kpi-card kpi-teal fade-up fade-up-d2">
        <div class="kpi-icon-wrap"><i class="ph ph-activity"></i></div>
        <div class="kpi-label">Total Mentions</div>
        <div class="kpi-value">{{ number_format($totalMentions) }}</div>
        <div class="kpi-sub"><i class="ph ph-chart-line-up"></i> Across all projects</div>
        <i class="ph ph-activity kpi-watermark"></i>
    </div>
    <div class="kpi-card kpi-green fade-up fade-up-d3">
        <div class="kpi-icon-wrap"><i class="ph ph-smiley"></i></div>
        <div class="kpi-label">Positive</div>
        <div class="kpi-value">{{ number_format($totalPositive) }}</div>
        <div class="kpi-sub">
            <i class="ph ph-trend-up"></i>
            @if($totalMentions > 0){{ round($totalPositive/$totalMentions*100,1) }}% of total
            @else No data @endif
        </div>
        <i class="ph ph-smiley kpi-watermark"></i>
    </div>
    <div class="kpi-card kpi-red fade-up fade-up-d4">
        <div class="kpi-icon-wrap"><i class="ph ph-smiley-sad"></i></div>
        <div class="kpi-label">Negative</div>
        <div class="kpi-value">{{ number_format($totalNegative) }}</div>
        <div class="kpi-sub">
            <i class="ph ph-trend-down"></i>
            @if($totalMentions > 0){{ round($totalNegative/$totalMentions*100,1) }}% of total
            @else No data @endif
        </div>
        <i class="ph ph-smiley-sad kpi-watermark"></i>
    </div>
</div>

{{-- ══ Main Layout ══ --}}
<div class="dash-layout">

    {{-- Sidebar --}}
    <div>
        <div class="proj-sidebar">
            <div class="proj-sidebar-head">
                <span class="proj-sidebar-title">Projects</span>
                <span class="proj-count-pill">{{ $projectCount }}</span>
            </div>
            <div class="sidebar-search">
                <input type="text" id="sidebarSearch" placeholder="Search project...">
            </div>
            <div class="proj-list" id="projList">
                @forelse($projects as $project)
                @php
                    $pId    = $project['id'] ?? '-';
                    $pTitle = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled';
                    $pTotal = $project['total_mentions'] ?? 0;
                    $pGroup = $project['project_group_name'] ?? '';
                @endphp
                <div class="proj-item" data-id="{{ $pId }}" data-name="{{ strtolower($pTitle) }}">
                    <div class="proj-item-dot"></div>
                    <div class="proj-item-text">
                        <span class="proj-item-name">{{ $pTitle }}</span>
                        <span class="proj-item-meta">{{ $pGroup ? $pGroup.' · ' : '' }}{{ number_format($pTotal) }} mentions</span>
                    </div>
                    <i class="ph ph-caret-right proj-item-arrow"></i>
                </div>
                @empty
                <p class="text-center text-muted small py-4 mb-0">No projects assigned</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Charts Area --}}
    <div class="charts-area" id="chartsArea">

        {{-- Skeleton --}}
        <div id="skeletonWrap" class="skeleton-wrap">
            @for($i = 0; $i < min(2, max(1, $projectCount)); $i++)
            <div class="skeleton-card">
                <div class="d-flex gap-3 mb-3 align-items-center" style="padding-bottom:10px;border-bottom:1px solid var(--slate-100);">
                    <div class="sk-block" style="width:8px;height:8px;border-radius:50%;flex-shrink:0;"></div>
                    <div class="sk-block" style="width:160px;height:13px;"></div>
                    <div class="ms-auto sk-block" style="width:86px;height:28px;border-radius:5px;"></div>
                </div>
                <div class="sk-block mb-2" style="width:220px;height:10px;"></div>
                <div class="sk-block mb-4" style="width:150px;height:10px;"></div>
                <div class="sk-block" style="width:100%;height:180px;border-radius:5px;"></div>
            </div>
            @endfor
        </div>

        {{-- Actual Cards --}}
        <div id="actualCards" style="display:none;">
            @forelse($projects as $project)
            @php
                $id    = $project['id'] ?? '-';
                $title = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled Project';
                $group = $project['project_group_name'] ?? '-';
                $type  = $project['project_type'] ?? 'Unknown';
                $media = $project['media_types'] ?? 'All Media';
                $total = $project['total_mentions'] ?? 0;
                $sent  = $project['sentiment_summary'] ?? ['positive'=>0,'neutral'=>0,'negative'=>0];
                $pos   = $sent['positive'] ?? 0;
                $neu   = $sent['neutral']  ?? 0;
                $neg   = $sent['negative'] ?? 0;
            @endphp

            <div class="proj-card lazy-card"
                 id="proj-card-{{ $id }}"
                 data-project-id="{{ $id }}"
                 data-loaded="false">

                <div class="proj-card-header">
                    <div class="proj-card-title-row">
                        <div class="proj-card-left">
                            <div class="proj-status-dot"></div>
                            <div>
                                <h6 class="proj-card-name">{{ $title }}</h6>
                                <span class="proj-card-sub">#{{ $id }} &middot; {{ $group }}</span>
                            </div>
                        </div>
                        <div class="proj-card-actions">
                            <a href="{{ route('mk.data-overview') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                               class="btn-primary-sm">
                                <i class="ph ph-chart-bar"></i> Overview
                            </a>
                            <a href="{{ route('mk.sentiment') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                               class="btn-icon-sm" title="Sentiment">
                                <i class="ph ph-smiley"></i>
                            </a>
                            <a href="{{ route('mk.geographic') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                               class="btn-icon-sm" title="Geographic">
                                <i class="ph ph-globe-hemisphere-west"></i>
                            </a>
                        </div>
                    </div>
                    <div class="meta-row">
                        <span class="meta-pill date">
                            <i class="ph ph-calendar-blank"></i>
                            {{ \Carbon\Carbon::parse($startDate)->format('d M') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </span>
                        <span class="meta-pill type"><i class="ph ph-tag"></i> {{ $type }}</span>
                        <span class="meta-pill media"><i class="ph ph-globe"></i> {{ $media }}</span>
                    </div>
                </div>

                <div class="proj-card-body">
                    <div class="stats-row">
                        <div class="stat-chip">
                            <span class="stat-chip-dot" style="background:var(--dash-primary);box-shadow:0 0 0 2px var(--dash-primary-lt);"></span>
                            <span>Total</span><strong>{{ number_format($total) }}</strong>
                        </div>
                        <div class="stat-chip">
                            <span class="stat-chip-dot" style="background:#10B981;box-shadow:0 0 0 2px rgba(16,185,129,.14);"></span>
                            <span>Positive</span><strong style="color:#059669;">{{ number_format($pos) }}</strong>
                        </div>
                        <div class="stat-chip">
                            <span class="stat-chip-dot" style="background:#94A3B8;box-shadow:0 0 0 2px rgba(148,163,184,.14);"></span>
                            <span>Neutral</span><strong>{{ number_format($neu) }}</strong>
                        </div>
                        <div class="stat-chip">
                            <span class="stat-chip-dot" style="background:#EF4444;box-shadow:0 0 0 2px rgba(239,68,68,.14);"></span>
                            <span>Negative</span><strong style="color:#DC2626;">{{ number_format($neg) }}</strong>
                        </div>
                    </div>

                    <div class="chart-subheader">
                        <span class="chart-subheader-label">Mention Trend</span>
                        <span class="chart-subheader-right">
                            {{ \Carbon\Carbon::parse($startDate)->format('d M') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </span>
                    </div>

                    <div class="chart-container" id="chart-wrap-{{ $id }}">
                        <div class="chart-loading" id="chart-loading-{{ $id }}">
                            <div class="spin-ring"></div>
                            <span>Loading chart...</span>
                        </div>
                        <canvas id="chart-{{ $id }}" style="display:none;"></canvas>
                    </div>
                </div>
            </div>

            <div class="lazy-sentinel" data-target="proj-card-{{ $id }}" id="sentinel-{{ $id }}"></div>

            @empty
            <div class="empty-state">
                <i class="ph ph-folder-open"></i>
                <h5>No Projects Yet</h5>
                <p>Contact your administrator to get project access.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<button class="scroll-top-btn" id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})">
    <i class="ph ph-arrow-up"></i>
</button>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Date label
    const el = document.getElementById('mkDateLabel');
    if (el) el.textContent = new Date().toLocaleDateString('en-US', {
        weekday:'short', day:'numeric', month:'short', year:'numeric'
    });

    // Scroll-to-top
    const scrollBtn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', () => scrollBtn.classList.toggle('visible', window.scrollY > 300), { passive:true });

    // Sidebar search
    const searchInput = document.getElementById('sidebarSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.proj-item').forEach(item => {
                item.style.display = (!q || item.dataset.name.includes(q)) ? '' : 'none';
            });
        });
    }

    // Sidebar click
    document.querySelectorAll('.proj-item').forEach(function (item) {
        item.addEventListener('click', function () {
            const card = document.getElementById('proj-card-' + item.dataset.id);
            document.querySelectorAll('.proj-item').forEach(el => el.classList.remove('active-sidebar'));
            item.classList.add('active-sidebar');
            if (!card) return;
            card.scrollIntoView({ behavior:'smooth', block:'start' });
            card.classList.add('highlighted');
            setTimeout(() => card.classList.remove('highlighted'), 2000);
        });
    });

    // Swap skeleton → cards (instant)
    const skel  = document.getElementById('skeletonWrap');
    const cards = document.getElementById('actualCards');
    if (skel)  skel.style.display = 'none';
    if (cards) cards.style.display = 'block';

    initLazyCharts();
});

/* ── Lazy Chart Init ── */
function initLazyCharts() {
    if (typeof IntersectionObserver === 'undefined') {
        document.querySelectorAll('.lazy-card').forEach(function (c, i) {
            c.classList.add('card-visible');
            setTimeout(() => fetchAndRenderChart(c.dataset.projectId), i * 400);
        });
        return;
    }

    const obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            const sentinel = entry.target;
            const card     = document.getElementById(sentinel.dataset.target);
            if (!card || card.dataset.loaded === 'true') { obs.unobserve(sentinel); return; }
            card.dataset.loaded = 'true';
            card.classList.add('card-visible');
            obs.unobserve(sentinel);
            setTimeout(() => fetchAndRenderChart(card.dataset.projectId), 120);
        });
    }, { rootMargin:'150px 0px', threshold:0.01 });

    document.querySelectorAll('.lazy-sentinel').forEach(s => obs.observe(s));
}

/* ── AJAX fetch per project ── */
function fetchAndRenderChart(projectId) {
    if (PROJECT_TIMELINES[String(projectId)]) {
        renderProjectChart(projectId);
        return;
    }

    const loadEl = document.getElementById('chart-loading-' + projectId);
    if (loadEl) loadEl.querySelector('span').textContent = 'Fetching data...';

    const url = CHART_DATA_URL
        + '?project_id=' + encodeURIComponent(projectId)
        + '&start_date=' + encodeURIComponent(START_DATE)
        + '&end_date='   + encodeURIComponent(END_DATE);

    fetch(url, {
        method  : 'GET',
        headers : {
            'Accept'          : 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN'    : CSRF_TOKEN,
        },
        credentials: 'same-origin',
    })
    .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status + ' — ' + res.statusText);
        return res.json();
    })
    .then(function (json) {
        PROJECT_TIMELINES[String(projectId)] = json.timeline || {};
        renderProjectChart(projectId);
    })
    .catch(function (err) {
        console.error('[Chart] Fetch failed for project', projectId, err);
        const wrapEl = document.getElementById('chart-wrap-' + projectId);
        if (loadEl)  loadEl.remove();
        if (wrapEl)  wrapEl.innerHTML =
            '<div class="chart-empty"><i class="ph ph-wifi-slash"></i><span>Failed to load chart data</span></div>';
    });
}

/* ── Render chart from cache ── */
function renderProjectChart(projectId) {
    const canvas = document.getElementById('chart-' + projectId);
    const wrapEl = document.getElementById('chart-wrap-' + projectId);
    const loadEl = document.getElementById('chart-loading-' + projectId);
    const tl     = PROJECT_TIMELINES[String(projectId)] || null;

    if (!canvas || !wrapEl || typeof Chart === 'undefined') return;

    if (!tl || !tl.dates || tl.dates.length === 0) {
        if (loadEl) loadEl.remove();
        wrapEl.innerHTML = '<div class="chart-empty"><i class="ph ph-chart-line"></i><span>No data for selected range</span></div>';
        return;
    }

    canvas.style.display = 'block';

    // Read actual primary color from CSS variable (respects custom theme)
    const style   = getComputedStyle(document.documentElement);
    const primary = style.getPropertyValue('--bs-primary').trim() || '#4361EE';

    const C = { primary, green:'#10B981', gray:'#94A3B8', red:'#EF4444', white:'#fff', muted:'#94A3B8', border:'#E2E8F0', dark:'#0F172A' };
    const labels = tl.dates;

    const mkDs = (label, data, color, dashed) => ({
        label, data,
        borderColor:color, backgroundColor:'transparent',
        borderWidth:dashed ? 1.6 : 2, borderDash:dashed ? [5,3] : [],
        tension:0.42,
        pointRadius: labels.length <= 14 ? 3 : 2, pointHoverRadius:6,
        pointBackgroundColor:color, pointBorderColor:C.white, pointBorderWidth:2,
        fill:false,
    });

    new Chart(canvas, {
        type:'line',
        data:{
            labels,
            datasets:[
                mkDs('Mentions', tl.values,             C.primary),
                mkDs('Positive', tl.sentiment.positive, C.green),
                mkDs('Neutral',  tl.sentiment.neutral,  C.gray, true),
                mkDs('Negative', tl.sentiment.negative, C.red,  true),
            ],
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            animation:{
                duration:520, easing:'easeOutQuart',
                onComplete() {
                    if (loadEl) { loadEl.classList.add('hidden'); setTimeout(() => loadEl.remove(), 260); }
                },
            },
            layout:{ padding:{ top:4, right:6, bottom:0, left:4 } },
            plugins:{
                legend:{
                    position:'bottom', align:'start',
                    labels:{ usePointStyle:true, pointStyle:'circle', padding:16, boxWidth:7, boxHeight:7, font:{ size:11, weight:'600' }, color:C.muted },
                },
                tooltip:{
                    mode:'index', intersect:false,
                    backgroundColor:C.white, titleColor:C.dark, bodyColor:C.dark,
                    borderColor:C.border, borderWidth:1, padding:10, cornerRadius:5,
                    callbacks:{ label: ctx => '  ' + ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString() },
                },
            },
            scales:{
                x:{ grid:{ display:false }, border:{ display:false },
                    ticks:{ font:{ size:10, weight:'600' }, color:C.muted, padding:6, maxRotation:0, autoSkip:true, maxTicksLimit: labels.length > 30 ? 10 : 8 } },
                y:{ beginAtZero:true,
                    grid:{ color:'rgba(226,232,240,.5)', lineWidth:1 }, border:{ display:false },
                    ticks:{ font:{ size:10, weight:'600' }, color:C.muted, padding:8, maxTicksLimit:5,
                            callback: v => v >= 1000 ? (v/1000).toFixed(1)+'k' : v } },
            },
            interaction:{ intersect:false, mode:'index' },
        },
    });
}
</script>
@endsection