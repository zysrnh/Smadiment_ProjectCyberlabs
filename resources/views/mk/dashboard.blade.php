@extends('mk.layouts.app')

@section('title', 'Dashboard - SMADIMENT')

@section('styles')
<style>
/* ══ Summary Cards ═══════════════════════════════ */
.smad-stat-card {
    border: none;
    border-radius: 12px;
    transition: transform .18s, box-shadow .18s;
    box-shadow: 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
}
.smad-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(15,23,42,.10);
}
.smad-stat-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 20px;
}
.smad-stat-lbl {
    font-size: 11px; font-weight: 600;
    color: #94A3B8; text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: 2px;
}
.smad-stat-val {
    font-size: 22px; font-weight: 800;
    color: #0F172A; letter-spacing: -.5px; line-height: 1;
}

/* ══ Project Sidebar ═════════════════════════════ */
.proj-sidebar-card {
    border-radius: 12px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 1px 3px rgba(15,23,42,.06);
    position: sticky;
    top: 16px;
}
.proj-sidebar-card .card-header {
    background: #F8FAFC;
    border-bottom: 1px solid #E2E8F0;
    border-radius: 12px 12px 0 0;
    padding: 12px 16px;
    display: flex; align-items: center; justify-content: space-between;
}
.proj-list-item {
    display: flex; align-items: center; gap: 9px;
    padding: 9px 10px; border-radius: 8px;
    cursor: pointer;
    transition: background .15s, transform .15s;
    margin-bottom: 2px;
}
.proj-list-item:hover {
    background: #E6F4EE;
    transform: translateX(2px);
}
.proj-list-item:hover .proj-dot { background: #027447; transform: scale(1.3); }
.proj-list-item:hover .proj-arr { opacity: 1; color: #027447; }
.proj-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #CBD5E1; flex-shrink: 0; transition: all .2s;
}
.proj-arr { opacity: 0; transition: opacity .15s; flex-shrink: 0; }
.proj-name {
    font-size: 13px; font-weight: 600; color: #0F172A;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    display: block;
}
.proj-meta {
    font-size: 10px; color: #94A3B8; font-weight: 500; margin-top: 1px;
    display: block;
}

/* ══ Project Chart Cards ═════════════════════════ */
.proj-chart-card {
    border-radius: 12px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 1px 3px rgba(15,23,42,.06);
    transition: border-color .3s, box-shadow .3s;
    margin-bottom: 16px;
}
.proj-chart-card.hl {
    border-color: #027447;
    box-shadow: 0 0 0 3px rgba(2,116,71,.09), 0 4px 16px rgba(15,23,42,.08);
}
.proj-chart-card .card-header {
    background: #fff;
    border-bottom: 1px solid #E2E8F0;
    border-radius: 12px 12px 0 0;
    padding: 16px 20px 12px;
}

/* Card dot indicator */
.card-dot-green {
    width: 10px; height: 10px; border-radius: 50%;
    background: #027447; flex-shrink: 0;
    box-shadow: 0 0 0 3px rgba(2,116,71,.15);
    display: inline-block;
}

/* Mention stats */
.mention-chip {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600; color: #475569;
}
.mention-chip-dot {
    width: 8px; height: 8px; border-radius: 50%;
}
.mention-chip-val {
    font-size: 13px; font-weight: 800; color: #0F172A;
}

/* Meta tags */
.meta-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; border-radius: 99px;
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
}
.meta-badge-blue  { background: #EFF6FF; color: #3B82F6; }
.meta-badge-slate { background: #F1F5F9; color: #475569; }
.meta-badge-green { background: #F0FDF4; color: #027447; }

/* Chart area */
.chart-wrap {
    height: 230px; position: relative; padding: 0 4px;
}
.chart-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; height: 100%; gap: 8px;
    color: #94A3B8; font-size: 12px; font-weight: 500;
}

/* Skeleton */
.skeleton-card {
    height: 380px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 12px;
    margin-bottom: 16px;
}
@keyframes shimmer {
    0%   { background-position: -200% 0; }
    100% { background-position:  200% 0; }
}

/* Fade in */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.fade-in { animation: fadeIn 0.4s ease-out; }

/* Logout btn override */
.btn-logout-smad {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    background: #FEF2F2; color: #DC2626;
    border: 1px solid #FECACA;
    border-radius: 99px;
    font-size: 12px; font-weight: 700;
    transition: all .2s;
}
.btn-logout-smad:hover {
    background: #DC2626; color: #fff; border-color: #DC2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220,38,38,.25);
}

/* Empty state */
.empty-state-card {
    border: 2px dashed #E2E8F0;
    border-radius: 12px;
    padding: 80px 20px;
    text-align: center;
    background: #fff;
}
</style>
@endsection

@section('content')

{{-- Pass PHP data ke JS --}}
@php
  $jsTimelines  = collect($projects)->mapWithKeys(fn($p) => [
    (string)($p['id'] ?? '') => $p['timeline'] ?? ['dates'=>[],'values'=>[],'sentiment'=>['positive'=>[],'neutral'=>[],'negative'=>[]]]
  ]);
  $jsSentiments = collect($projects)->mapWithKeys(fn($p) => [
    (string)($p['id'] ?? '') => $p['sentiment_summary'] ?? ['positive'=>0,'neutral'=>0,'negative'=>0]
  ]);
  $jsTotals = collect($projects)->mapWithKeys(fn($p) => [
    (string)($p['id'] ?? '') => $p['total_mentions'] ?? 0
  ]);
  $totalMentionsAll = collect($projects)->sum('total_mentions');
  $totalPositiveAll = collect($projects)->sum(fn($p) => $p['sentiment_summary']['positive'] ?? 0);
  $totalNegativeAll = collect($projects)->sum(fn($p) => $p['sentiment_summary']['negative'] ?? 0);
@endphp

<script>
  const PROJECT_TIMELINES  = {!! json_encode($jsTimelines) !!};
  const PROJECT_SENTIMENTS = {!! json_encode($jsSentiments) !!};
  const PROJECT_TOTALS     = {!! json_encode($jsTotals) !!};
  const DASHBOARD_DATE_RANGE = {
    start: '{{ $startDate }}',
    end:   '{{ $endDate }}'
  };
</script>

{{-- ── Top Bar ── --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Welcome, {{ auth()->user()->name ?? 'User' }}!</h4>
        <p class="mb-0 text-muted" style="font-size:13px;">
            Showing data for
            <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong>
            –
            <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        {{-- Date pill --}}
        <span class="badge bg-light text-secondary border d-flex align-items-center gap-1 px-3 py-2" style="font-size:12px;font-weight:600;border-radius:99px;">
            <i class="ph ph-calendar-blank"></i>
            <span id="mkDateLabel"></span>
        </span>
        {{-- Logout --}}
        <form method="POST" action="{{ route('user.logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn-logout-smad border-0">
                <i class="ph ph-sign-out"></i>
                Logout
            </button>
        </form>
    </div>
</div>

{{-- ── Summary Strip ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card smad-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="smad-stat-icon" style="background:#EFF6FF;color:#3B82F6;">
                    <i class="ph ph-folder-open"></i>
                </div>
                <div>
                    <div class="smad-stat-lbl">My Projects</div>
                    <div class="smad-stat-val">{{ count($projects) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card smad-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="smad-stat-icon" style="background:#ECFDF5;color:#059669;">
                    <i class="ph ph-activity"></i>
                </div>
                <div>
                    <div class="smad-stat-lbl">Total Mentions</div>
                    <div class="smad-stat-val">{{ number_format($totalMentionsAll) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card smad-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="smad-stat-icon" style="background:#ECFDF5;color:#059669;">
                    <i class="ph ph-smiley"></i>
                </div>
                <div>
                    <div class="smad-stat-lbl">Positive</div>
                    <div class="smad-stat-val" style="color:#059669;">{{ number_format($totalPositiveAll) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card smad-stat-card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="smad-stat-icon" style="background:#FEF2F2;color:#DC2626;">
                    <i class="ph ph-smiley-sad"></i>
                </div>
                <div>
                    <div class="smad-stat-lbl">Negative</div>
                    <div class="smad-stat-val" style="color:#DC2626;">{{ number_format($totalNegativeAll) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Main 2-col ── --}}
<div class="row g-3 align-items-start">

    {{-- Left: Project List --}}
    <div class="col-12 col-lg-3">
        <div class="card proj-sidebar-card">
            <div class="card-header">
                <span style="font-size:11px;font-weight:700;color:#0F172A;text-transform:uppercase;letter-spacing:.6px;">
                    Your Projects
                </span>
                <span class="badge" style="background:#027447;color:#fff;font-size:10px;">{{ count($projects) }}</span>
            </div>
            <div class="card-body p-2">
                @forelse($projects as $project)
                @php
                    $pId    = $project['id'] ?? '-';
                    $pTitle = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled';
                    $pTotal = $project['total_mentions'] ?? 0;
                @endphp
                <div class="proj-list-item" data-id="{{ $pId }}">
                    <div class="proj-dot"></div>
                    <div class="flex-grow-1 min-width-0" style="min-width:0;">
                        <span class="proj-name">{{ $pTitle }}</span>
                        <span class="proj-meta">#{{ $pId }} · {{ number_format($pTotal) }} mentions</span>
                    </div>
                    <i class="ph ph-caret-right proj-arr" style="font-size:11px;"></i>
                </div>
                @empty
                <p class="text-center text-muted py-4 mb-0" style="font-size:12px;">No projects assigned</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Charts Column --}}
    <div class="col-12 col-lg-9">

        {{-- Skeleton --}}
        <div id="skeletonCards">
            @for($i = 0; $i < min(3, max(1, count($projects))); $i++)
            <div class="skeleton-card"></div>
            @endfor
        </div>

        {{-- Actual Cards --}}
        <div id="actualCards" style="display:none;">
            @forelse($projects as $project)
            @php
                $id     = $project['id'] ?? '-';
                $title  = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled Project';
                $group  = $project['project_group_name'] ?? 'No Group';
                $type   = $project['project_type'] ?? 'Unknown';
                $media  = $project['media_types'] ?? 'None';
                $total  = $project['total_mentions'] ?? 0;
                $sent   = $project['sentiment_summary'] ?? ['positive'=>0,'neutral'=>0,'negative'=>0];
                $pos    = $sent['positive'] ?? 0;
                $neu    = $sent['neutral']  ?? 0;
                $neg    = $sent['negative'] ?? 0;
            @endphp

            <div class="card proj-chart-card" id="card-{{ $id }}">

                {{-- Card Header --}}
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="card-dot-green"></span>
                            <div>
                                <h6 class="mb-0 fw-bold" style="color:#0F172A;letter-spacing:-.2px;">{{ $title }}</h6>
                                <span class="text-muted" style="font-size:11px;">Project #{{ $id }} &middot; {{ $group }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <a href="{{ route('mk.data-overview') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                               class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1" style="background:#027447;border-color:#027447;font-size:12px;">
                                <i class="ph ph-chart-bar"></i>
                                Data Overview
                            </a>
                            <a href="{{ route('mk.sentiment') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                               class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Sentiment" style="width:32px;height:32px;padding:0;justify-content:center;">
                                <i class="ph ph-smiley"></i>
                            </a>
                            <a href="{{ route('mk.geographic') }}?project_id={{ $id }}&start_date={{ $startDate }}&end_date={{ $endDate }}"
                               class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" title="Geographic" style="width:32px;height:32px;padding:0;justify-content:center;">
                                <i class="ph ph-globe-hemisphere-west"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Meta Tags --}}
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <span class="meta-badge meta-badge-blue">
                            <i class="ph ph-calendar-blank" style="font-size:10px;"></i>
                            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </span>
                        <span class="meta-badge meta-badge-slate">
                            <i class="ph ph-squares-four" style="font-size:10px;"></i>
                            {{ $type }}
                        </span>
                        <span class="meta-badge meta-badge-green">
                            <i class="ph ph-globe" style="font-size:10px;"></i>
                            {{ $media }}
                        </span>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Mention Stats Row --}}
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <div class="mention-chip">
                            <span class="mention-chip-dot" style="background:#5AB9EA;"></span>
                            <span>Total</span>
                            <span class="mention-chip-val">{{ number_format($total) }}</span>
                        </div>
                        <div class="mention-chip">
                            <span class="mention-chip-dot" style="background:#10B981;"></span>
                            <span>Positive</span>
                            <span class="mention-chip-val" style="color:#059669;">{{ number_format($pos) }}</span>
                        </div>
                        <div class="mention-chip">
                            <span class="mention-chip-dot" style="background:#94A3B8;"></span>
                            <span>Neutral</span>
                            <span class="mention-chip-val">{{ number_format($neu) }}</span>
                        </div>
                        <div class="mention-chip">
                            <span class="mention-chip-dot" style="background:#F87171;"></span>
                            <span>Negative</span>
                            <span class="mention-chip-val" style="color:#DC2626;">{{ number_format($neg) }}</span>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- Chart --}}
                    <div class="chart-wrap" id="wrap-{{ $id }}">
                        <canvas id="chart-{{ $id }}"></canvas>
                    </div>

                </div>
            </div>
            @empty
            <div class="empty-state-card">
                <i class="ph ph-folder-open" style="font-size:56px;color:#CBD5E1;"></i>
                <h5 class="mt-3 mb-1">No Projects Assigned</h5>
                <p class="text-muted mb-0" style="font-size:13px;">Contact your administrator to get access to projects.</p>
            </div>
            @endforelse
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Date label ──────────────────────────────────
    const now = new Date();
    const el = document.getElementById('mkDateLabel');
    if (el) el.textContent = now.toLocaleDateString('en-US', {
        weekday: 'short', day: 'numeric', month: 'short', year: 'numeric'
    });

    // ── Skeleton → show cards ───────────────────────
    setTimeout(function () {
        const skeleton = document.getElementById('skeletonCards');
        const cards    = document.getElementById('actualCards');
        if (skeleton && cards) {
            skeleton.style.display = 'none';
            cards.style.display    = 'block';
            cards.classList.add('fade-in');
        }
        buildCharts();
    }, 300);

    // ── Sidebar scroll ──────────────────────────────
    document.querySelectorAll('.proj-list-item').forEach(function (el) {
        el.addEventListener('click', function () {
            const card = document.getElementById('card-' + el.dataset.id);
            if (!card) return;
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
            card.classList.add('hl');
            setTimeout(() => card.classList.remove('hl'), 2200);
        });
    });

});

// ── Charts ──────────────────────────────────────────
function buildCharts() {
    if (typeof Chart === 'undefined') return;

    const C = {
        blue:   '#5AB9EA',
        green:  '#10B981',
        gray:   '#94A3B8',
        red:    '#F87171',
        white:  '#FFFFFF',
        light:  '#94A3B8',
        border: '#E2E8F0',
        dark:   '#0F172A',
    };

    document.querySelectorAll('[id^="chart-"]').forEach(function (canvas) {
        const projectId = canvas.id.replace('chart-', '');
        const tl        = PROJECT_TIMELINES[projectId] || null;
        const wrapEl    = document.getElementById('wrap-' + projectId);

        if (!tl || !tl.dates || tl.dates.length === 0) {
            if (wrapEl) {
                wrapEl.innerHTML = `
                    <div class="chart-empty">
                        <i class="ph ph-chart-line" style="font-size:36px;color:#CBD5E1;"></i>
                        <span>No mention data for this date range</span>
                    </div>`;
            }
            return;
        }

        const labels  = tl.dates;
        const allData = tl.values;
        const posData = tl.sentiment.positive;
        const neuData = tl.sentiment.neutral;
        const negData = tl.sentiment.negative;

        const ds = (label, data, color, dashed) => ({
            label,
            data,
            borderColor:          color,
            backgroundColor:      'transparent',
            borderWidth:          dashed ? 1.8 : 2.2,
            borderDash:           dashed ? [4, 3] : [],
            tension:              0.42,
            pointRadius:          labels.length <= 14 ? 4 : 2,
            pointHoverRadius:     6,
            pointBackgroundColor: color,
            pointBorderColor:     C.white,
            pointBorderWidth:     2,
            fill:                 false,
        });

        new Chart(canvas, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    ds('Mentions', allData, C.blue),
                    ds('Positive', posData, C.green),
                    ds('Neutral',  neuData, C.gray, true),
                    ds('Negative', negData, C.red,  true),
                ],
            },
            options: {
                responsive:          true,
                maintainAspectRatio: false,
                layout: { padding: { top: 4, right: 8, bottom: 0, left: 4 } },
                plugins: {
                    legend: {
                        position: 'bottom',
                        align:    'start',
                        labels: {
                            usePointStyle: true,
                            pointStyle:    'circle',
                            padding:       16,
                            font: { size: 11, weight: '600' },
                            color:    C.light,
                            boxWidth:  7,
                            boxHeight: 7,
                        },
                    },
                    tooltip: {
                        mode:            'index',
                        intersect:       false,
                        backgroundColor: '#fff',
                        titleColor:      C.dark,
                        bodyColor:       C.dark,
                        borderColor:     C.border,
                        borderWidth:     1,
                        padding:         12,
                        cornerRadius:    8,
                        titleFont: { size: 11, weight: '700' },
                        bodyFont:  { size: 11, weight: '500' },
                        displayColors: true,
                        boxWidth:  7,
                        boxHeight: 7,
                        boxPadding: 5,
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid:   { display: false },
                        border: { display: false },
                        ticks: {
                            font:          { size: 10, weight: '500' },
                            color:         C.light,
                            padding:       6,
                            maxRotation:   0,
                            autoSkip:      true,
                            maxTicksLimit: labels.length > 30 ? 10 : 8,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226,232,240,.8)', lineWidth: 1 },
                        border: { display: false },
                        ticks: {
                            font:          { size: 10, weight: '500' },
                            color:         C.light,
                            padding:       10,
                            maxTicksLimit: 5,
                            callback: v => v >= 1000 ? (v / 1000).toFixed(1) + 'k' : v,
                        },
                    },
                },
                interaction: { intersect: false, mode: 'index' },
            },
        });
    });
}
</script>
@endsection