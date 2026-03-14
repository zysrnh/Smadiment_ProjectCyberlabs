@extends('mk.layouts.app')

@section('title', 'Dashboard - SMADIMENT')

@section('styles')
<style>
/* ══ Design Tokens ══ */
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

/* ══ Animations ══ */
@keyframes fadeUp        { from{opacity:0;transform:translateY(12px)}  to{opacity:1;transform:translateY(0)} }
@keyframes fadeIn        { from{opacity:0}                             to{opacity:1} }
@keyframes slideInRight  { from{transform:translateX(100%);opacity:0}  to{transform:translateX(0);opacity:1} }
@keyframes slideOutRight { from{transform:translateX(0);opacity:1}     to{transform:translateX(100%);opacity:0} }
@keyframes overlayIn     { from{opacity:0} to{opacity:1} }
@keyframes overlayOut    { from{opacity:1} to{opacity:0} }
@keyframes spin          { to{transform:rotate(360deg)} }
@keyframes shimmer       { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes pulseP        { 0%,100%{box-shadow:0 0 0 3px var(--dash-primary-lt)} 50%{box-shadow:0 0 0 6px transparent} }

.fade-up    { animation:fadeUp .38s ease-out both; }
.fade-up-d1 { animation-delay:.05s }
.fade-up-d2 { animation-delay:.10s }
.fade-up-d3 { animation-delay:.15s }
.fade-up-d4 { animation-delay:.20s }

/* ══ KPI Card Icons ══ */
.kpi-icon-bg {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0;
}

/* ══ Project Sidebar ══ */
.proj-sidebar-card { position:sticky; top:80px; }
.proj-list-scroll  { max-height:600px; overflow-y:auto; }
.proj-list-scroll::-webkit-scrollbar { width:4px; }
.proj-list-scroll::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

.proj-item { transition:all .15s ease; cursor:pointer; }
.proj-item:hover { background:var(--slate-50) !important; }
.proj-item.active-sidebar {
    background:var(--dash-primary-lt) !important;
    border-left:3px solid var(--dash-primary) !important;
}
.proj-item .proj-status-dot {
    width:8px; height:8px; border-radius:50%;
    background:var(--dash-primary); flex-shrink:0;
    box-shadow:0 0 0 3px var(--dash-primary-lt);
    animation:pulseP 2.5s infinite;
}

/* ══ Lazy Card Transitions ══ */
.lazy-card {
    opacity:0; transform:translateY(14px);
    transition:opacity .4s ease, transform .4s ease, border-color .2s, box-shadow .2s;
}
.lazy-card.card-visible { opacity:1; transform:translateY(0); }
.lazy-card.highlighted  {
    border-color:var(--dash-primary) !important;
    box-shadow:0 0 0 3px var(--dash-primary-lt), var(--shadow-md) !important;
}

/* ══ Stat Chips ══ */
.stat-chip.clickable {
    cursor:pointer; transition:transform .13s, box-shadow .13s; user-select:none;
}
.stat-chip.clickable:hover  { transform:translateY(-2px); box-shadow:var(--shadow-sm); }
.stat-chip.clickable:active { transform:translateY(0); }

/* ══ Chart Area ══ */
.chart-container { height:280px; position:relative; }
.chart-loading {
    position:absolute; inset:0;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:8px; background:#fff; z-index:2; transition:opacity .3s;
}
.chart-loading.hidden { opacity:0; pointer-events:none; }
.spin-ring {
    width:26px; height:26px;
    border:2.5px solid var(--slate-100); border-top-color:var(--dash-primary);
    border-radius:50%; animation:spin .65s linear infinite;
}
.chart-loading span { font-size:11px; font-weight:600; color:var(--slate-400); }
.chart-empty {
    height:100%; display:flex; flex-direction:column;
    align-items:center; justify-content:center;
    gap:6px; color:var(--slate-400); font-size:12px; font-weight:600;
}
.chart-empty i { font-size:34px; color:var(--slate-300); display:block; }

/* ══ Skeleton ══ */
.sk-block {
    border-radius:4px;
    background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);
    background-size:200% 100%; animation:shimmer 1.4s infinite;
}
.lazy-sentinel { height:1px; }

/* ══ Scroll-to-top ══ */
.scroll-top-btn {
    position:fixed; bottom:20px; right:70px;
    width:36px; height:36px; background:var(--dash-primary); border-radius:var(--radius);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:15px; box-shadow:var(--shadow-md);
    cursor:pointer; opacity:0; pointer-events:none;
    transition:opacity .2s, transform .2s; z-index:998; border:none;
}
.scroll-top-btn.visible { opacity:1; pointer-events:all; }
.scroll-top-btn:hover   { transform:translateY(-2px); }

/* ══════════════════════════════════════════════════════
   SLIDE PANEL  (ported 1-to-1 from data-overview)
══════════════════════════════════════════════════════ */
.do-panel-overlay {
    position:fixed; inset:0; z-index:9000;
    background:rgba(15,23,42,.45); backdrop-filter:blur(4px); display:none;
}
.do-panel-overlay.show   { display:block; animation:overlayIn .22s ease-out; }
.do-panel-overlay.hiding { animation:overlayOut .22s ease-out forwards; }

.do-panel {
    position:fixed; top:0; right:0; bottom:0; z-index:9001;
    width:480px; max-width:100vw; background:#fff;
    display:none; flex-direction:column;
    border-left:1px solid var(--slate-200);
    box-shadow:-8px 0 40px rgba(15,23,42,.16);
}
.do-panel.show   { display:flex; animation:slideInRight .28s cubic-bezier(.4,0,.2,1); }
.do-panel.hiding { animation:slideOutRight .24s cubic-bezier(.4,0,.2,1) forwards; }

.do-panel-header {
    display:flex; align-items:center; gap:10px;
    padding:14px 16px; border-bottom:1px solid var(--slate-200);
    background:var(--slate-50); flex-shrink:0;
}
.do-panel-dot   { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
.do-panel-title { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-panel-close {
    width:28px; height:28px; border-radius:var(--radius-sm);
    border:1px solid var(--slate-200); background:#fff; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    color:var(--slate-500); font-size:16px; transition:all .14s; flex-shrink:0;
}
.do-panel-close:hover { background:var(--red); border-color:var(--red); color:#fff; }

.do-panel-actions {
    display:flex; align-items:center; gap:7px; padding:7px 12px;
    border-bottom:1px solid var(--slate-200); background:#fff; flex-shrink:0;
}
.do-panel-meta {
    flex:1; font-size:10px; font-weight:700; color:var(--slate-400);
    text-transform:uppercase; letter-spacing:.5px;
    display:flex; align-items:center; gap:5px;
}
.do-panel-tabs {
    display:flex; background:var(--slate-100); border:1px solid var(--slate-200);
    border-radius:var(--radius-sm); padding:2px; gap:2px;
}
.do-panel-tab {
    padding:3px 9px; border-radius:3px; border:none; background:transparent;
    font-size:11px; font-weight:700; cursor:pointer; transition:all .13s;
    color:var(--slate-500); font-family:inherit;
}
.do-panel-tab:hover  { background:#fff; }
.do-panel-tab.active { background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08); }
.do-panel-tab.active[data-s="all"] { color:var(--dash-primary); }
.do-panel-tab.neg.active { color:#EF4444; }
.do-panel-tab.pos.active { color:#10B981; }
.do-panel-tab.neu.active { color:var(--slate-500); }

.do-panel-export {
    display:flex; align-items:center; gap:4px; padding:4px 10px;
    background:var(--dash-primary); color:#fff; border:none;
    border-radius:var(--radius-sm); font-size:10px; font-weight:700;
    cursor:pointer; transition:filter .13s; font-family:inherit;
}
.do-panel-export:hover { filter:brightness(1.1); }
.do-panel-export i { font-size:12px; }

.do-panel-list { overflow-y:auto; flex:1; padding:2px 0; min-height:0; }
.do-panel-list::-webkit-scrollbar { width:4px; }
.do-panel-list::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

.do-panel-item {
    display:flex; gap:10px; padding:10px 14px;
    border-bottom:1px solid var(--slate-50); cursor:pointer;
    transition:background .1s; align-items:flex-start;
}
.do-panel-item:hover { background:#f0f9ff; }
.do-panel-item:last-child { border-bottom:none; }

.do-panel-avatar {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-weight:700; font-size:12px; color:#fff;
    border:1.5px solid var(--slate-200); overflow:hidden;
}
.do-panel-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

.do-panel-item-body { flex:1; min-width:0; }
.do-panel-author { font-size:12px; font-weight:700; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.do-panel-handle { font-size:10px; color:var(--slate-400); font-weight:500; margin-bottom:2px; }
.do-panel-text   { font-size:11px; color:var(--slate-600); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
.do-panel-footer { display:flex; align-items:center; gap:5px; font-size:10px; color:var(--slate-400); flex-wrap:wrap; }

.do-sent-badge { padding:1px 6px; border-radius:3px; font-size:9px; font-weight:800; text-transform:uppercase; }
.do-sent-badge--pos { background:#dbeafe; color:#1d4ed8; }
.do-sent-badge--neg { background:#fee2e2; color:#991b1b; }
.do-sent-badge--neu { background:var(--slate-100); color:var(--slate-500); }

.do-panel-loading {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    height:100%; gap:12px; color:var(--slate-400); font-size:13px; font-weight:600;
}
.do-panel-spinner {
    width:28px; height:28px; border:2.5px solid var(--slate-100);
    border-top-color:var(--dash-primary); border-radius:50%;
    animation:spin .65s linear infinite;
}

/* ── Detail sub-panel ── */
.do-detail-panel {
    position:absolute; inset:0; background:#fff; z-index:5;
    display:none; flex-direction:column;
    animation:slideInRight .2s cubic-bezier(.4,0,.2,1);
}
.do-detail-panel.show { display:flex; }

.do-dp2-header {
    display:flex; align-items:center; gap:8px; padding:12px 14px;
    background:var(--slate-50); border-bottom:1px solid var(--slate-200); flex-shrink:0;
}
.do-dp2-back {
    width:28px; height:28px; border-radius:var(--radius-sm);
    border:1px solid var(--slate-200); background:#fff; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    color:var(--slate-500); transition:all .13s; font-size:14px;
}
.do-dp2-back:hover { background:var(--dash-primary-lt); color:var(--dash-primary); border-color:var(--dash-primary); }
.do-dp2-title  { font-size:13px; font-weight:700; color:var(--slate-900); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.do-dp2-body   { overflow-y:auto; flex:1; padding:16px; }
.do-dp2-body::-webkit-scrollbar { width:4px; }
.do-dp2-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }
.do-dp2-avatar-row { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.do-dp2-avatar-lg  {
    width:46px; height:46px; border-radius:50%; color:#fff; font-weight:700;
    font-size:16px; display:flex; align-items:center; justify-content:center;
    border:2px solid var(--slate-200); overflow:hidden; flex-shrink:0;
}
.do-dp2-avatar-lg img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.do-dp2-name       { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-handle     { font-size:11px; color:var(--slate-400); font-weight:500; }
.do-dp2-plat-badge { display:inline-block; padding:2px 8px; border-radius:3px; font-size:10px; font-weight:700; margin-top:3px; }
.do-dp2-meta       { font-size:11px; color:var(--slate-400); font-weight:500; margin-bottom:10px; }
.do-dp2-sent       { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:3px; font-size:11px; font-weight:700; margin-bottom:10px; }
.do-dp2-sent--pos  { background:#dbeafe; color:#1d4ed8; }
.do-dp2-sent--neg  { background:#fee2e2; color:#991b1b; }
.do-dp2-sent--neu  { background:var(--slate-100); color:var(--slate-500); }
.do-dp2-content    { font-size:12px; color:var(--slate-700); line-height:1.7; margin-bottom:12px; background:var(--slate-50); border-radius:var(--radius-sm); padding:10px 12px; border:1px solid var(--slate-200); word-break:break-word; }
.do-dp2-media      { border-radius:var(--radius-sm); overflow:hidden; margin-bottom:10px; }
.do-dp2-media img  { width:100%; max-height:220px; object-fit:cover; display:block; }
.do-dp2-stats      { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:10px; }
.do-dp2-stat       { background:var(--slate-50); border-radius:var(--radius-sm); padding:8px 10px; border:1px solid var(--slate-200); text-align:center; }
.do-dp2-stat-val   { font-size:14px; font-weight:700; color:var(--slate-900); }
.do-dp2-stat-lbl   { font-size:9px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.4px; margin-top:1px; }
.do-dp2-link {
    display:flex; align-items:center; justify-content:center; gap:6px;
    padding:9px 14px; background:var(--dash-primary); color:#fff;
    border-radius:var(--radius-sm); font-size:12px; font-weight:700;
    text-decoration:none; transition:filter .14s; margin-top:4px;
}
.do-dp2-link:hover { filter:brightness(1.1); color:#fff; }
.do-dp2-link i { font-size:13px; }

/* ── Platform picker ── */
.do-plat-picker {
    position:fixed; z-index:20000; background:#fff;
    border:1px solid var(--slate-200); border-radius:var(--radius);
    box-shadow:var(--shadow-lg); padding:5px; min-width:175px;
    font-family:inherit; display:none; animation:fadeUp .14s ease-out;
}
.do-plat-picker.show { display:block; }
.do-plat-picker-head { padding:4px 9px 6px; font-size:10px; font-weight:700; color:var(--slate-400); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--slate-100); margin-bottom:3px; }
.do-plat-btn {
    display:flex; align-items:center; gap:7px; padding:7px 10px;
    border-radius:var(--radius-sm); font-size:12px; font-weight:600;
    cursor:pointer; background:transparent; border:none;
    font-family:inherit; width:100%; text-align:left; color:var(--slate-700); transition:background .12s;
}
.do-plat-btn:hover  { background:var(--dash-primary-lt); color:var(--dash-primary); }
.do-plat-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-left:auto; }

@media(max-width:640px) { .do-panel { width:100vw; } }
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

{{-- ══ Welcome Card ══ --}}
<div class="row">
    <div class="col-12">
        <div class="card fade-up">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avtar avtar-s rounded" style="width:42px;height:42px;background:var(--dash-primary);color:#fff;font-weight:800;font-size:15px;">{{ $initials }}</div>
                        <div>
                            <h6 class="mb-1">Welcome, {{ auth()->user()->name ?? 'User' }}!</h6>
                            <p class="mb-0 text-muted f-12">
                                <i class="ph ph-calendar-blank me-1"></i>
                                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                                &ndash;
                                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-secondary text-muted rounded-pill px-3 py-2">
                            <i class="ph ph-clock me-1"></i><span id="mkDateLabel"></span>
                        </span>
                        <form method="POST" action="{{ route('user.logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-light-danger btn-sm">
                                <i class="ph ph-sign-out me-1"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ KPI Cards ══ --}}
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card bg-primary text-white fade-up fade-up-d1">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">My Projects</p>
                        <h3 class="mb-0 text-white f-w-300">{{ $projectCount }}</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-circle-dashed me-1"></i>Active monitoring
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-folder-open"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card bg-success text-white fade-up fade-up-d2">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p>
                        <h3 class="mb-0 text-white f-w-300">{{ number_format($totalMentions) }}</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-chart-line-up me-1"></i>Across all projects
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-activity"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card bg-warning text-white fade-up fade-up-d3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
                        <h3 class="mb-0 text-white f-w-300">{{ number_format($totalPositive) }}</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-trend-up me-1"></i>
                            @if($totalMentions > 0){{ round($totalPositive/$totalMentions*100,1) }}% of total
                            @else No data @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card bg-danger text-white fade-up fade-up-d4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
                        <h3 class="mb-0 text-white f-w-300">{{ number_format($totalNegative) }}</h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-trend-down me-1"></i>
                            @if($totalMentions > 0){{ round($totalNegative/$totalMentions*100,1) }}% of total
                            @else No data @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- ══ Main Layout ══ --}}
<div class="row">

    {{-- Project Sidebar --}}
    <div class="col-xl-4 col-md-5">
        <div class="card proj-sidebar-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="ph ph-list-bullets me-2 text-muted"></i>Projects</h5>
                <span class="badge bg-primary rounded-pill">{{ $projectCount }}</span>
            </div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="ph ph-magnifying-glass text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="sidebarSearch" placeholder="Search project...">
                    </div>
                </div>
                <div class="proj-list-scroll" id="projList">
                    @forelse($projects as $project)
                    @php
                        $pId    = $project['id'] ?? '-';
                        $pTitle = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled';
                        $pTotal = $project['total_mentions'] ?? 0;
                        $pGroup = $project['project_group_name'] ?? '';
                    @endphp
                    <div class="proj-item d-flex align-items-center p-3 border-bottom"
                         data-id="{{ $pId }}" data-name="{{ strtolower($pTitle) }}">
                        <div class="flex-shrink-0">
                            <div class="proj-status-dot"></div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 f-14">{{ $pTitle }}</h6>
                            <p class="mb-0 text-muted f-12">{{ $pGroup ? $pGroup.' · ' : '' }}{{ number_format($pTotal) }} mentions</p>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="ph ph-caret-right text-muted f-16"></i>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted small py-4 mb-0">No projects assigned</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Area --}}
    <div class="col-xl-8 col-md-7">
        <div id="chartsArea">

            {{-- Skeleton --}}
            <div id="skeletonWrap">
                @for($i = 0; $i < min(2, max(1, $projectCount)); $i++)
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex gap-3 align-items-center">
                            <div class="sk-block" style="width:8px;height:8px;border-radius:50%;flex-shrink:0;"></div>
                            <div class="sk-block" style="width:160px;height:13px;"></div>
                            <div class="ms-auto sk-block" style="width:86px;height:28px;border-radius:5px;"></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="sk-block mb-2" style="width:220px;height:10px;"></div>
                        <div class="sk-block mb-4" style="width:150px;height:10px;"></div>
                        <div class="sk-block" style="width:100%;height:200px;border-radius:5px;"></div>
                    </div>
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

                <div class="card lazy-card mb-3"
                     id="proj-card-{{ $id }}"
                     data-project-id="{{ $id }}"
                     data-loaded="false">

                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-xs bg-light-success rounded-circle">
                                    <i class="ph ph-pulse f-18 text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $title }}</h6>
                                    <small class="text-muted">#{{ $id }} &middot; {{ $group }}</small>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('mk.data-overview') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                                   class="btn btn-primary btn-sm">
                                    <i class="ph ph-chart-bar me-1"></i>Overview
                                </a>
                                <a href="{{ route('mk.sentiment') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                                   class="btn btn-outline-secondary btn-sm" title="Sentiment">
                                    <i class="ph ph-smiley"></i>
                                </a>
                                <a href="{{ route('mk.geographic') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                                   class="btn btn-outline-secondary btn-sm" title="Geographic">
                                    <i class="ph ph-globe-hemisphere-west"></i>
                                </a>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <span class="badge bg-light-primary text-primary">
                                <i class="ph ph-calendar-blank me-1"></i>
                                {{ \Carbon\Carbon::parse($startDate)->format('d M') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            </span>
                            <span class="badge bg-light-secondary"><i class="ph ph-tag me-1"></i>{{ $type }}</span>
                            <span class="badge bg-light-success text-success"><i class="ph ph-globe me-1"></i>{{ $media }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Stats Row --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-lg-3">
                                <div class="stat-chip clickable p-2 rounded-2 text-center"
                                     style="background:var(--dash-primary-lt);"
                                     onclick="DashPanel.open('all','all','{{ $id }}')">
                                    <small class="text-muted d-block mb-1 f-10 fw-semibold text-uppercase">Total</small>
                                    <h6 class="mb-0" style="color:var(--dash-primary);">{{ number_format($total) }}</h6>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="stat-chip clickable p-2 rounded-2 text-center"
                                     style="background:var(--green-light);"
                                     onclick="DashPanel.open('all','pos','{{ $id }}')">
                                    <small class="text-muted d-block mb-1 f-10 fw-semibold text-uppercase">Positive</small>
                                    <h6 class="mb-0 text-success">{{ number_format($pos) }}</h6>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="stat-chip clickable p-2 rounded-2 text-center"
                                     style="background:var(--slate-50);"
                                     onclick="DashPanel.open('all','neu','{{ $id }}')">
                                    <small class="text-muted d-block mb-1 f-10 fw-semibold text-uppercase">Neutral</small>
                                    <h6 class="mb-0 text-muted">{{ number_format($neu) }}</h6>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="stat-chip clickable p-2 rounded-2 text-center"
                                     style="background:var(--red-light);"
                                     onclick="DashPanel.open('all','neg','{{ $id }}')">
                                    <small class="text-muted d-block mb-1 f-10 fw-semibold text-uppercase">Negative</small>
                                    <h6 class="mb-0 text-danger">{{ number_format($neg) }}</h6>
                                </div>
                            </div>
                        </div>

                        {{-- Chart Header --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted fw-semibold text-uppercase" style="letter-spacing:.5px;font-size:11px;">
                                Mention Trend
                            </small>
                            <small class="text-muted f-12">
                                {{ \Carbon\Carbon::parse($startDate)->format('d M') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            </small>
                        </div>

                        {{-- Chart --}}
                        <div class="chart-container" id="chart-wrap-{{ $id }}">
                            <div class="chart-loading" id="chart-loading-{{ $id }}">
                                <div class="spin-ring"></div>
                                <span>Loading chart...</span>
                            </div>
                            <div id="chart-{{ $id }}" style="width:100%;height:280px;display:none;cursor:pointer;"></div>
                        </div>
                    </div>
                </div>

                <div class="lazy-sentinel" data-target="proj-card-{{ $id }}" id="sentinel-{{ $id }}"></div>

                @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ph ph-folder-open d-block mb-3" style="font-size:48px;color:var(--slate-300);"></i>
                        <h5 class="text-muted">No Projects Yet</h5>
                        <p class="text-muted mb-0 f-12">Contact your administrator to get project access.</p>
                    </div>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

<button class="scroll-top-btn" id="scrollTopBtn" onclick="window.scrollTo({top:0,behavior:'smooth'})">
    <i class="ph ph-arrow-up"></i>
</button>

{{-- ══ Slide Panel (mentions drawer) — same structure as data-overview ══ --}}
<div class="do-panel-overlay" id="dashPanelOverlay" onclick="DashPanel.closeByOverlay()"></div>
<div class="do-panel" id="dashSntPanel">
    <div class="do-panel-header">
        <div class="do-panel-dot" id="dashPanelDot"></div>
        <span class="do-panel-title" id="dashPanelTitle">Mentions</span>
        <button class="do-panel-close" onclick="DashPanel.close()"><i class="ph ph-x"></i></button>
    </div>
    <div class="do-panel-actions">
        <div class="do-panel-meta">
            <i class="ph ph-magnifying-glass" style="font-size:11px;"></i>
            <span id="dashPanelMeta">&mdash;</span>
        </div>
        <div class="do-panel-tabs">
            <button class="do-panel-tab active" data-s="all" onclick="DashPanel.filterSent('all')">Semua</button>
            <button class="do-panel-tab neg"    data-s="neg" onclick="DashPanel.filterSent('neg')">Neg</button>
            <button class="do-panel-tab pos"    data-s="pos" onclick="DashPanel.filterSent('pos')">Pos</button>
            <button class="do-panel-tab neu"    data-s="neu" onclick="DashPanel.filterSent('neu')">Neu</button>
        </div>
        <button class="do-panel-export" onclick="DashPanel.exportCsv()">
            <i class="ph ph-download-simple"></i> CSV
        </button>
    </div>
    <div class="do-panel-list" id="dashPanelList"></div>

    {{-- Detail sub-panel --}}
    <div class="do-detail-panel" id="dashDetailPanel">
        <div class="do-dp2-header">
            <button class="do-dp2-back" onclick="DashDetail.close()"><i class="ph ph-caret-left"></i></button>
            <span class="do-dp2-title" id="dashDetailTitle">Detail</span>
            <button class="do-panel-close" onclick="DashPanel.close()"><i class="ph ph-x"></i></button>
        </div>
        <div class="do-dp2-body" id="dashDetailBody"></div>
    </div>
</div>

{{-- Platform Picker --}}
<div class="do-plat-picker" id="dashPlatPicker">
    <div class="do-plat-picker-head">Pilih Platform</div>
    <button class="do-plat-btn" onclick="DashPanel.openPlatform('twit','all')">X / Twitter   <span class="do-plat-dot" style="background:#1d9bf0;"></span></button>
    <button class="do-plat-btn" onclick="DashPanel.openPlatform('fb','all')">Facebook       <span class="do-plat-dot" style="background:#1877f2;"></span></button>
    <button class="do-plat-btn" onclick="DashPanel.openPlatform('instagram','all')">Instagram <span class="do-plat-dot" style="background:#e1306c;"></span></button>
    <button class="do-plat-btn" onclick="DashPanel.openPlatform('youtube','all')">YouTube    <span class="do-plat-dot" style="background:#ff0000;"></span></button>
    <button class="do-plat-btn" onclick="DashPanel.openPlatform('tiktok','all')">TikTok      <span class="do-plat-dot" style="background:#111827;"></span></button>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script>
/* ══════════════════════════════════════════════════════
   GLOBALS / CONFIG
══════════════════════════════════════════════════════ */
const DashCfg = {
    sd: START_DATE,
    ed: END_DATE,
    platMeta: {
        doc       : { label:'Online News',  color:'#0284c7' },
        twit      : { label:'X / Twitter',  color:'#1d9bf0' },
        fb        : { label:'Facebook',     color:'#1877f2' },
        instagram : { label:'Instagram',    color:'#e1306c' },
        youtube   : { label:'YouTube',      color:'#ff0000' },
        tiktok    : { label:'TikTok',       color:'#111827' },
        all       : { label:'All Media',    color:'#4361EE' },
        social    : { label:'Social Media', color:'#4361EE' },
    },
};

const _$  = id => document.getElementById(id);
const _es = s  => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const numK = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };

function getPrimary(){
    return getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#4361EE';
}

/* ══════════════════════════════════════════════════════
   DOM READY
══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {

    // Date label
    const el = _$('mkDateLabel');
    if (el) el.textContent = new Date().toLocaleDateString('en-US', {
        weekday:'short', day:'numeric', month:'short', year:'numeric'
    });

    // Scroll-to-top
    const scrollBtn = _$('scrollTopBtn');
    window.addEventListener('scroll', () => scrollBtn.classList.toggle('visible', window.scrollY > 300), { passive:true });

    // Sidebar search
    const searchInput = _$('sidebarSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.proj-item').forEach(item => {
                item.style.display = (!q || item.dataset.name.includes(q)) ? '' : 'none';
            });
        });
    }

    // Sidebar click → scroll + highlight card
    document.querySelectorAll('.proj-item').forEach(function (item) {
        item.addEventListener('click', function () {
            const card = _$('proj-card-' + item.dataset.id);
            document.querySelectorAll('.proj-item').forEach(el => el.classList.remove('active-sidebar'));
            item.classList.add('active-sidebar');
            if (!card) return;
            card.scrollIntoView({ behavior:'smooth', block:'start' });
            card.classList.add('highlighted');
            setTimeout(() => card.classList.remove('highlighted'), 2000);
        });
    });

    // Swap skeleton → cards
    const skel  = _$('skeletonWrap');
    const cards = _$('actualCards');
    if (skel)  skel.style.display = 'none';
    if (cards) cards.style.display = 'block';

    initLazyCharts();
});

/* ══════════════════════════════════════════════════════
   LAZY CHART INIT
══════════════════════════════════════════════════════ */
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
            const card     = _$('proj-card-' + sentinel.dataset.target.replace('proj-card-',''));
            // re-use target attr which already holds the full id
            const cardEl   = document.getElementById(sentinel.dataset.target);
            if (!cardEl || cardEl.dataset.loaded === 'true') { obs.unobserve(sentinel); return; }
            cardEl.dataset.loaded = 'true';
            cardEl.classList.add('card-visible');
            obs.unobserve(sentinel);
            setTimeout(() => fetchAndRenderChart(cardEl.dataset.projectId), 120);
        });
    }, { rootMargin:'150px 0px', threshold:0.01 });

    document.querySelectorAll('.lazy-sentinel').forEach(s => obs.observe(s));
}

/* ══════════════════════════════════════════════════════
   FETCH CHART DATA
══════════════════════════════════════════════════════ */
function fetchAndRenderChart(projectId) {
    if (PROJECT_TIMELINES[String(projectId)]) {
        renderProjectChart(projectId);
        return;
    }

    const loadEl = _$('chart-loading-' + projectId);
    if (loadEl) { const sp = loadEl.querySelector('span'); if(sp) sp.textContent = 'Fetching data...'; }

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
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(json => {
        PROJECT_TIMELINES[String(projectId)] = json.timeline || {};
        renderProjectChart(projectId);
    })
    .catch(err => {
        console.error('[Chart] Fetch failed for project', projectId, err);
        const wrapEl = _$('chart-wrap-' + projectId);
        if (loadEl)  loadEl.remove();
        if (wrapEl)  wrapEl.innerHTML =
            '<div class="chart-empty"><i class="ph ph-wifi-slash"></i><span>Failed to load chart data</span></div>';
    });
}

/* ══════════════════════════════════════════════════════
   RENDER CHART
   - Basic area, smooth spline (like image 2)
   - Floating num-badge data labels on Total + Positive
     (clean text, no box bg — like image 1 label style)
══════════════════════════════════════════════════════ */
function renderProjectChart(projectId) {
    const chartEl = _$('chart-' + projectId);
    const wrapEl  = _$('chart-wrap-' + projectId);
    const loadEl  = _$('chart-loading-' + projectId);
    const tl      = PROJECT_TIMELINES[String(projectId)] || null;

    if (!chartEl || !wrapEl || typeof ApexCharts === 'undefined') return;

    if (!tl || !tl.dates || tl.dates.length === 0) {
        if (loadEl) loadEl.remove();
        wrapEl.innerHTML = '<div class="chart-empty"><i class="ph ph-chart-line"></i><span>No data for selected range</span></div>';
        return;
    }

    chartEl.style.display = 'block';

    const primary = getPrimary();
    const C = {
        primary,
        green : '#10B981',
        gray  : '#94A3B8',
        red   : '#EF4444',
    };

    /* x-axis short labels */
    const labels = tl.dates.map(dt => {
        try {
            const d = new Date(dt + 'T00:00:00');
            return `${d.getDate()}/${d.getMonth() + 1}`;
        } catch(e) { return dt; }
    });

    /* skip data-labels when there are too many points (gets crowded) */
    const showLabels = labels.length <= 20;

    const options = {
        chart: {
            type: 'area',
            height: 280,
            animations: {
                enabled: true,
                easing: 'linear',
                dynamicAnimation: { speed: 1000 }
            },
            toolbar: { show: false },
            events: {
                mounted: function () {
                    if (loadEl) {
                        loadEl.classList.add('hidden');
                        setTimeout(() => { try { loadEl.remove(); } catch(e){} }, 260);
                    }
                },
            },
        },
        series: [
            { name: 'Total',    data: tl.values                    || [] },
            { name: 'Positive', data: tl.sentiment?.positive       || [] },
            { name: 'Neutral',  data: tl.sentiment?.neutral        || [] },
            { name: 'Negative', data: tl.sentiment?.negative       || [] },
        ],
        xaxis: {
            categories: labels,
        },
        colors: ['#4680ff', '#10B981', '#94A3B8', '#EF4444'],
        fill: { opacity: 0.3 },
        stroke: { curve: 'smooth' },
    };

    /* destroy previous instance */
    const prevKey = '__apexInst_' + projectId;
    if (window[prevKey]) { try { window[prevKey].destroy(); } catch(e){} }

    chartEl.innerHTML = '';
    const inst = new ApexCharts(chartEl, options);
    window[prevKey] = inst;
    inst.render();

    /* whole-chart area click → open mentions panel */
    chartEl.addEventListener('click', e => {
        const t = e.target;
        const skip = t.classList.contains('apexcharts-marker')
            || t.closest('.apexcharts-datalabels')
            || t.closest('.apexcharts-legend');
        if (!skip) DashPanel.open('all', 'all', projectId);
    });
}

/* ══════════════════════════════════════════════════════
   DASH PANEL — slide-in drawer (full data-overview parity)
══════════════════════════════════════════════════════ */
const DashPanel = (() => {
    let _cache = {}, _allItems = [], _filtered = [], _curSent = 'all',
        _curPlat = null, _curPid = null, _curPlatForSent = 'all';

    const SENT_MAP = {
        '1':'pos','positive':'pos','positif':'pos',
        '-1':'neg','2':'neg','negative':'neg','negatif':'neg',
    };
    const _normSent = item => SENT_MAP[String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim()] || 'neu';

    /* ── Platform picker ── */
    function showPlatPicker(x, y, sent, pid) {
        _curPlatForSent = sent || 'all';
        if (pid) _curPid = pid;
        const pp = _$('dashPlatPicker');
        if (!pp) return;
        pp.querySelectorAll('.do-plat-btn').forEach(btn => {
            const m  = btn.getAttribute('onclick') || '';
            const pm = m.match(/openPlatform\('([^']+)'/);
            if (pm) btn.setAttribute('onclick', `DashPanel.openPlatform('${pm[1]}','${_curPlatForSent}')`);
        });
        const pw=180,ph=250,vw=window.innerWidth,vh=window.innerHeight;
        let left=x+10, top=y-10;
        if (left+pw>vw-8) left=x-pw-10;
        if (top+ph>vh-8)  top=vh-ph-8;
        if (top<8) top=8;
        pp.style.left = left+'px'; pp.style.top = top+'px';
        pp.classList.add('show');
    }

    function openPlatform(platform, sentiment) {
        _$('dashPlatPicker')?.classList.remove('show');
        open(platform, sentiment || _curPlatForSent || 'all', _curPid);
    }

    /* ── Open panel ── */
    async function open(platform, sentiment, projectId) {
        _curPlat = platform;
        _curSent = sentiment || 'all';
        if (projectId) _curPid = projectId;

        const meta = DashCfg.platMeta[platform] || { label: platform, color: '#4361EE' };

        DashDetail.close();

        _$('dashPanelDot').style.background = meta.color;
        _$('dashPanelTitle').textContent    = meta.label + (platform === 'all' ? ' — All Platforms' : '');
        _$('dashPanelMeta').textContent     = DashCfg.sd + ' – ' + DashCfg.ed;

        document.querySelectorAll('#dashSntPanel .do-panel-tab').forEach(t =>
            t.classList.toggle('active', t.dataset.s === _curSent)
        );

        const list = _$('dashPanelList');
        list.innerHTML = `<div class="do-panel-loading"><div class="do-panel-spinner"></div><span>Memuat mentions…</span></div>`;

        const overlay = _$('dashPanelOverlay'), panel = _$('dashSntPanel');
        overlay.classList.remove('hiding'); panel.classList.remove('hiding');
        overlay.classList.add('show');     panel.classList.add('show');

        try {
            const key = `${_curPid}_${platform}_${DashCfg.sd}_${DashCfg.ed}`;
            if (!_cache[key]) _cache[key] = await _fetchAll(platform, _curPid);
            _allItems = _cache[key];
            _filtered = _filterBySent(_allItems, _curSent);
            _render(list, _filtered, platform, meta.color);
        } catch (err) {
            list.innerHTML = `<div style="padding:50px 20px;text-align:center;color:#94A3B8;font-size:13px;">Gagal memuat data<br><small>${_es(err.message)}</small></div>`;
        }
    }

    /* ── Close ── */
    function close() {
        const overlay = _$('dashPanelOverlay'), panel = _$('dashSntPanel');
        panel.classList.add('hiding'); overlay.classList.add('hiding');
        setTimeout(() => {
            panel.classList.remove('show','hiding');
            overlay.classList.remove('show','hiding');
            DashDetail.close();
        }, 240);
    }
    function closeByOverlay() { close(); }

    /* ── Filter by sentiment ── */
    function filterSent(sent) {
        _curSent = sent;
        document.querySelectorAll('#dashSntPanel .do-panel-tab').forEach(t =>
            t.classList.toggle('active', t.dataset.s === sent)
        );
        _filtered = _filterBySent(_allItems, sent);
        const meta = DashCfg.platMeta[_curPlat] || { color:'#4361EE' };
        _render(_$('dashPanelList'), _filtered, _curPlat, meta.color);
    }

    function _filterBySent(items, sent) {
        return sent === 'all' ? items : items.filter(i => _normSent(i) === sent);
    }

    /* ── Export CSV ── */
    function exportCsv() {
        if (!_filtered.length) { alert('Tidak ada data.'); return; }
        const rows = _filtered.map(item => ({
            nama    : (item.author_name||item.channel_name||item.from_name||item.publisher||item.source_name||'').trim(),
            sentimen: { pos:'Positif', neg:'Negatif', neu:'Netral' }[_normSent(item)],
            tanggal : item.date_created || '',
            url     : item.url || item.link || '',
            konten  : (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,500),
        }));
        const meta = DashCfg.platMeta[_curPlat] || { label: _curPlat || 'all' };
        const fn   = `mentions_${meta.label.replace(/\s+/g,'_')}_${_curSent}_${DashCfg.sd}_${DashCfg.ed}`;
        const headers = Object.keys(rows[0]);
        const lines   = [
            headers.join(';'),
            ...rows.map(r => headers.map(h => {
                let v = String(r[h]||'').replace(/"/g,'""');
                return v.includes(';')||v.includes('"')||v.includes('\n') ? `"${v}"` : v;
            }).join(';'))
        ];
        const blob = new Blob(['\uFEFF' + lines.join('\n')], { type:'text/csv;charset=utf-8;' });
        const a    = Object.assign(document.createElement('a'), {
            href    : URL.createObjectURL(blob),
            download: fn + '.csv',
        });
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
    }

    /* ── Fetch all platforms ── */
    async function _fetchAll(platform, pid) {
        if (platform === 'all') {
            const all = ['doc','twit','fb','instagram','youtube','tiktok'];
            const res = await Promise.allSettled(all.map(p => _fetchOne(p, pid)));
            return res.flatMap(r => r.status === 'fulfilled' ? r.value : []);
        }
        if (platform === 'social') {
            const s = ['twit','fb','instagram','youtube','tiktok'];
            const res = await Promise.allSettled(s.map(p => _fetchOne(p, pid)));
            return res.flatMap(r => r.status === 'fulfilled' ? r.value : []);
        }
        return _fetchOne(platform, pid);
    }

    async function _fetchOne(platform, pid) {
        const q = `project_id=${pid}&start_date=${DashCfg.sd}&end_date=${DashCfg.ed}&rows=500&start=0`;

        if (platform === 'instagram') {
            for (const sub of ['postbylike','postbydate']) {
                const ic = new AbortController(), it = setTimeout(() => ic.abort(), 12000);
                try {
                    const r = await fetch(`/mk/api/news/ig-top-status?${q}${sub ? '&sub='+sub : ''}`, { signal: ic.signal });
                    clearTimeout(it);
                    const d = await r.json();
                    const items = Array.isArray(d.data) ? d.data : (Array.isArray(d) ? d : []);
                    if (items.length > 0) return items.map(i => ({ ...i, _platform: platform }));
                } catch(e) { clearTimeout(it); continue; }
            }
            return [];
        }

        const eps = {
            doc    : `/mk/api/news/mentions?${q}`,
            twit   : `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
            fb     : `/mk/api/news/fb-top-status?${q}&sub=fblike`,
            youtube: `/mk/api/news/ytb-top-status?${q}`,
            tiktok : `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
        };
        const twitFallback = `/mk/api/news/mentions?${q}&media_type=twit`;
        const url = eps[platform]; if (!url) return [];

        const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 15000);
        try {
            const r = await fetch(url, { signal: ctrl.signal }); clearTimeout(tid);
            if (!r.ok) return [];
            const d = await r.json();

            let items = [];
            if (Array.isArray(d?.data?.data))   items = d.data.data;
            else if (Array.isArray(d?.data))     items = d.data;
            else if (Array.isArray(d?.statuses)) items = d.statuses;
            else if (Array.isArray(d?.tweets))   items = d.tweets;
            else if (Array.isArray(d?.results))  items = d.results;
            else if (Array.isArray(d?.posts))    items = d.posts;
            else if (Array.isArray(d))           items = d;
            else if (d?.data && typeof d.data === 'object' && !Array.isArray(d.data)) {
                const vals = Object.values(d.data);
                if (vals.length && typeof vals[0] === 'object') items = vals;
            }

            // Twitter fallback
            if (platform === 'twit' && items.length === 0) {
                try {
                    const r2 = await fetch(twitFallback);
                    const d2 = await r2.json();
                    let fb = Array.isArray(d2?.data?.data) ? d2.data.data
                           : Array.isArray(d2?.data)       ? d2.data
                           : Array.isArray(d2)             ? d2 : [];
                    items = fb.filter(m => {
                        const tc = String(m.tcode||'').toLowerCase();
                        const mt = String(m.media_type||'').toLowerCase();
                        return tc === 'twit' || tc === 'rt' || mt === 'twit';
                    });
                } catch(e2) {}
            }

            if (platform === 'doc') items = items.filter(m => {
                const tc = String(m.tcode||'').toLowerCase();
                const mt = String(m.media_type||'').toLowerCase();
                return tc==='berita'||mt==='berita'||mt==='doc'||mt==='news'||mt==='online'||mt==='article';
            });

            return items.map(i => ({ ...i, _platform: platform }));
        } catch(e) { clearTimeout(tid); return []; }
    }

    /* ── Render list ── */
    function _render(list, items, platform, accentColor) {
        if (!items.length) {
            list.innerHTML = `<div style="padding:50px 20px;text-align:center;color:#94A3B8;font-size:12px;font-weight:600;">Tidak ada mentions untuk filter ini.</div>`;
            return;
        }
        const SHOW = 60;
        list.innerHTML = items.slice(0, SHOW).map(item => {
            const plat = item._platform || platform;
            const meta = DashCfg.platMeta[plat] || { label: plat, color: accentColor };

            const rawName = (() => {
                if (plat === 'fb')        return item.from_name || item.page_name || null;
                if (plat === 'instagram') return item.username  || item.user_name || null;
                if (plat === 'tiktok')    return item.author_nickname || item.nickname || item.author?.nickname || null;
                if (plat === 'youtube')   return item.channel_title   || item.channel_name || item.snippet?.channelTitle || null;
                if (plat === 'twit') {
                    const ao = typeof item.author === 'object' ? item.author
                             : (() => { try { return JSON.parse(item.author||'{}'); } catch(e){ return {}; } })();
                    return item.name || ao?.name || ao?.scr_name || item.author_name || null;
                }
                return null;
            })();

            const name  = (rawName || item.author_name || item.channel_name || item.publisher || item.source_name || 'Unknown').trim();
            const isNum = /^\d{10,}$/.test(name);
            const dName = isNum ? `User ${name.slice(-4)}` : name;

            const rawH = (() => {
                if (plat === 'instagram') return item.username || '';
                if (plat === 'twit') {
                    const ao = typeof item.author === 'object' ? item.author
                             : (() => { try { return JSON.parse(item.author||'{}'); } catch(e){ return {}; } })();
                    return item.screen_name || item.author_scr_name || ao?.scr_name || ao?.username || '';
                }
                return item.author_scr_name || item.screen_name || item.username || '';
            })().trim();

            const handle = (() => {
                if (!rawH) return '';
                const w = ['twit','instagram','tiktok'].includes(plat)
                        ? (rawH.startsWith('@') ? rawH : '@' + rawH) : rawH;
                return w.replace(/^@/,'').toLowerCase() === dName.toLowerCase() ? '' : w;
            })();

            const text = (item.content||item.caption||item.description||item.title||item.text||'')
                .replace(/<[^>]*>/g,'').trim().slice(0,150);

            const ao  = (() => { if (typeof item.author==='object'&&item.author) return item.author; try { return JSON.parse(item.author||'{}'); } catch(e){ return {}; } })();
            const av  = (item.avatar_url||item.profile_image_url||ao?.image||item.author_image||item.profile_image||item.thumbnail||'').trim();
            const dt  = (item.date_created||item.created_at||'').split('T')[0];
            const sent= _normSent(item);
            const sentLbl = sent==='pos'?'Pos':sent==='neg'?'Neg':'Neu';

            const words  = dName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
            const ini    = (words.length>=2 ? (words[0][0]+words[words.length-1][0]) : (words[0]?.[0]||dName[0]||'?')).toUpperCase().replace(/['"]/g,'');
            const avHtml = (av && av.startsWith('http'))
                ? `<img src="${_es(av)}" onerror="this.style.display='none';this.parentElement.textContent='${ini}';">`
                : ini;
            const sentBadge = `do-sent-badge--${sent}`;
            const enc = encodeURIComponent(JSON.stringify(item));

            return `<div class="do-panel-item" onclick="DashDetail.openEncoded('${enc}','${plat}')">
                <div class="do-panel-avatar" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                <div class="do-panel-item-body">
                    <div class="do-panel-author">${_es(dName)}</div>
                    ${handle ? `<div class="do-panel-handle">${_es(handle)}</div>` : ''}
                    <div class="do-panel-text">${_es(text||'(tidak ada konten)')}</div>
                    <div class="do-panel-footer">
                        <span class="do-sent-badge ${sentBadge}">${sentLbl}</span>
                        <span style="display:inline-block;width:5px;height:5px;border-radius:50%;background:${meta.color};flex-shrink:0;"></span>
                        <span style="font-size:10px;font-weight:600;color:${meta.color};">${meta.label}</span>
                        ${dt ? `<span style="margin-left:auto;">${dt}</span>` : ''}
                    </div>
                </div>
            </div>`;
        }).join('');

        if (items.length > SHOW) {
            list.insertAdjacentHTML('beforeend',
                `<div style="padding:9px;text-align:center;font-size:11px;font-weight:600;color:#94A3B8;background:#F8FAFC;border-top:1px dashed #E2E8F0;">` +
                `+${(items.length - SHOW).toLocaleString()} lainnya · Export CSV untuk lihat semua</div>`
            );
        }
    }

    return { open, close, closeByOverlay, showPlatPicker, openPlatform, filterSent, exportCsv };
})();

/* ══════════════════════════════════════════════════════
   DASH DETAIL — sub-panel (full data-overview parity)
══════════════════════════════════════════════════════ */
const DashDetail = {
    openEncoded(enc, plat) {
        try { this.open(JSON.parse(decodeURIComponent(enc)), plat); } catch(e) {}
    },

    open(item, platform) {
        const panel = _$('dashDetailPanel'), body = _$('dashDetailBody'), title = _$('dashDetailTitle');
        if (!panel || !body) return;

        const meta = DashCfg.platMeta[platform] || { label: platform, color: '#4361EE' };
        const SM2  = { '1':'pos','positive':'pos','positif':'pos','-1':'neg','2':'neg','negative':'neg','negatif':'neg' };
        const raw  = String(item.class_sentiment||item.sentiment||'0').toLowerCase();
        const sent = SM2[raw] || 'neu';
        const SLBL = { pos:'Positif', neg:'Negatif', neu:'Netral' };
        const SBGS = { pos:'do-dp2-sent--pos', neg:'do-dp2-sent--neg', neu:'do-dp2-sent--neu' };

        const rawName = (() => {
            if (platform==='fb')        return item.from_name || item.page_name || null;
            if (platform==='instagram') return item.username || null;
            if (platform==='tiktok')    return item.author_nickname || item.nickname || item.author?.nickname || null;
            if (platform==='youtube')   return item.channel_title   || item.channel_name || item.snippet?.channelTitle || null;
            if (platform==='twit') {
                const ao = typeof item.author==='object' ? item.author
                         : (() => { try { return JSON.parse(item.author||'{}'); } catch(e){ return {}; } })();
                return item.name || ao?.name || ao?.scr_name || item.author_name || null;
            }
            return null;
        })();

        const name    = (rawName || item.author_name || item.channel_name || item.publisher || item.source_name || 'Unknown').trim();
        const handle  = ((platform==='instagram' ? item.username : '') || item.author_scr_name || item.screen_name || item.username || '').trim();
        const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
        const av      = (item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||'').trim();
        const url     = item.url || item.link || '';
        const dt      = item.date_created || item.created_at || '';

        title.textContent = name;

        const words  = name.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
        const ini    = (words.length>=2 ? (words[0][0]+words[words.length-1][0]) : (words[0]?.[0]||name[0]||'?')).toUpperCase().replace(/['"]/g,'');
        const avHtml = (av && av.startsWith('http'))
            ? `<img src="${_es(av)}" onerror="this.parentElement.textContent='${ini}';">`
            : ini;

        let dtFmt = '';
        if (dt) {
            try {
                dtFmt = new Date(dt).toLocaleDateString('id-ID', {
                    weekday:'long', day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit'
                });
            } catch(e) { dtFmt = dt.split('T')[0]; }
        }

        /* ── Media embed per platform ── */
        let mediaHtml = '';
        if (platform === 'youtube') {
            const ytId = (url.match(/[?&]v=([a-zA-Z0-9_-]{11})/) || url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/) || url.match(/shorts\/([a-zA-Z0-9_-]{11})/) || [])[1]
                       || (item.video_id || item.youtube_id || '');
            const thumb = item.thumbnail || item.thumbnail_url || item.image_url || (ytId ? `https://img.youtube.com/vi/${ytId}/hqdefault.jpg` : '');
            if (ytId) {
                const eid = `yt_${ytId}_${Date.now()}`;
                mediaHtml = `<div id="${eid}" style="position:relative;cursor:pointer;border-radius:6px;overflow:hidden;background:#000;margin-bottom:10px;" onclick="document.getElementById('${eid}').innerHTML='<iframe width=\\\"100%\\\" height=\\\"260\\\" src=\\\"https://www.youtube.com/embed/${ytId}?autoplay=1&controls=1\\\" frameborder=\\\"0\\\" allowfullscreen></iframe>'">
                    <img src="${thumb || `https://img.youtube.com/vi/${ytId}/hqdefault.jpg`}" style="width:100%;height:200px;object-fit:cover;display:block;" onerror="this.src='https://img.youtube.com/vi/${ytId}/mqdefault.jpg'">
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.25);">
                        <div style="width:52px;height:52px;background:#ff0000;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.4);">
                            <i class="ph ph-play-fill" style="font-size:22px;color:#fff;margin-left:3px;"></i>
                        </div>
                    </div>
                </div>`;
            } else if (thumb) {
                mediaHtml = `<div class="do-dp2-media"><img src="${_es(thumb)}" onerror="this.parentElement.style.display='none'" style="border-radius:6px;"></div>`;
            }
        } else if (platform === 'tiktok') {
            const tid = (url.match(/\/video\/(\d+)/) || url.match(/\/v\/(\d+)/) || [])[1] || (item.video_id || item.aweme_id || '');
            const thumb = item.thumbnail || item.cover || item.image_url || item.video_cover || '';
            if (tid) {
                const eid = `tt_${tid}_${Date.now()}`;
                mediaHtml = `<div id="${eid}" style="position:relative;cursor:pointer;background:#111827;border-radius:6px;overflow:hidden;display:flex;align-items:center;justify-content:center;height:240px;margin-bottom:10px;" onclick="DashDetail.loadTikTok('${eid}','${tid}')">
                    ${thumb ? `<img src="${_es(thumb)}" style="position:absolute;width:100%;height:100%;object-fit:cover;opacity:.65;pointer-events:none;">` : ''}
                    <div style="position:relative;z-index:2;width:56px;height:56px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(0,0,0,.6);">
                        <i class="ph ph-play-fill" style="font-size:24px;color:#111827;margin-left:3px;"></i>
                    </div>
                    <div style="position:absolute;bottom:8px;right:8px;background:#111827;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:3px;letter-spacing:.5px;">TIKTOK</div>
                </div>`;
            } else if (thumb) {
                mediaHtml = `<div class="do-dp2-media"><img src="${_es(thumb)}" onerror="this.parentElement.style.display='none'" style="max-height:280px;object-fit:cover;width:100%;display:block;border-radius:6px;"></div>`;
            }
        } else {
            const thumb = item.image_url || item.thumbnail || item.media_url || item.picture || item.display_url || item.featured_image || '';
            const isVideo = (item.media_type || item.type || '').toLowerCase().includes('video');
            if (thumb) {
                mediaHtml = `<div class="do-dp2-media" style="position:relative;border-radius:6px;overflow:hidden;background:#f3f4f6;margin-bottom:10px;">
                    <img src="${_es(thumb)}" onerror="this.parentElement.style.display='none'" style="width:100%;max-height:280px;object-fit:cover;display:block;">
                    ${isVideo ? `<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.3);">
                        <div style="width:44px;height:44px;background:rgba(255,255,255,.9);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="ph ph-play-fill" style="font-size:20px;color:${meta.color};margin-left:3px;"></i>
                        </div></div>` : ''}
                </div>`;
            }
        }

        const statsMap = {
            twit     : [['Retweet', item.num_retweeted||item.retweet_count||0], ['Like', item.num_likes||item.favorite_count||0], ['Quote', item.num_quote||0]],
            fb       : [['Like', item.likes||item.num_likes||0], ['Share', item.shares||item.share_count||0], ['Comment', item.num_comments||0]],
            instagram: [['Like', item.num_likes||item.likes||0], ['Comment', item.num_comments||item.comment_count||0], ['View', item.num_views||item.views||0]],
            youtube  : [['View', item.num_views||item.views||0], ['Like', item.num_likes||item.likes||0], ['Comment', item.num_comments||0]],
            tiktok   : [['Play', item.views||item.play_count||0], ['Like', item.likes||item.digg_count||0], ['Share', item.shares||item.share_count||0]],
            doc      : [['Read', item.num_views||0], ['Share', item.num_share||0], ['Comment', item.num_comments||0]],
        };
        const stats = statsMap[platform] || [];
        const statsHtml = stats.some(s => parseInt(s[1]) > 0)
            ? `<div class="do-dp2-stats">${stats.map(([l,v]) =>
                `<div class="do-dp2-stat"><div class="do-dp2-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="do-dp2-stat-lbl">${l}</div></div>`
              ).join('')}</div>` : '';

        const handleDisp = handle && !handle.replace('@','').toLowerCase().startsWith(name.toLowerCase().slice(0,4))
            ? (handle.startsWith('@') ? handle : '@' + handle) : '';

        body.innerHTML = `
            <div class="do-dp2-avatar-row">
                <div class="do-dp2-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
                <div>
                    <div class="do-dp2-name">${_es(name)}</div>
                    ${handleDisp ? `<div class="do-dp2-handle">${_es(handleDisp)}</div>` : ''}
                    <span class="do-dp2-plat-badge" style="background:${meta.color}22;color:${meta.color};">${meta.label}</span>
                </div>
            </div>
            ${dtFmt ? `<div class="do-dp2-meta">${dtFmt}</div>` : ''}
            <div class="do-dp2-sent ${SBGS[sent]}">${SLBL[sent]}</div>
            ${mediaHtml}
            ${content ? `<div class="do-dp2-content">${_es(content)}</div>` : ''}
            ${statsHtml}
            ${url ? `<a href="${_es(url)}" target="_blank" rel="noopener noreferrer" class="do-dp2-link"><i class="ph ph-arrow-square-out"></i> Lihat ${meta.label} Asli</a>` : ''}`;

        panel.classList.add('show');
    },

    close() {
        const panel = _$('dashDetailPanel');
        if (!panel) return;
        panel.classList.remove('show');
        panel.querySelectorAll('iframe').forEach(f => { try { f.src = f.src; } catch(e){} });
    },

    loadTikTok(eid, tid) {
        const el = _$(eid); if (!el) return;
        el.style.cssText = 'cursor:default;min-height:560px;height:auto;background:#111827;border-radius:6px;overflow:hidden;margin-bottom:10px;';
        el.innerHTML = `<iframe src="https://www.tiktok.com/embed/v2/${tid}" width="100%" height="560"
            frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture"
            allowfullscreen style="display:block;border:none;border-radius:6px;background:#111827;"></iframe>`;
    },
};

/* ── Platform picker dismiss on outside click ── */
document.addEventListener('mousedown', e => {
    const pp = _$('dashPlatPicker');
    if (pp?.classList.contains('show') && !pp.contains(e.target)) pp.classList.remove('show');
});
</script>
@endsection