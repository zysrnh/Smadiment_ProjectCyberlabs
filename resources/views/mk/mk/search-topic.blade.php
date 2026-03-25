@extends('mk.layouts.app')

@section('title', 'Search Topic - SMADIMENT')

@section('styles')
<style>
/* ══ Design Tokens ══ */
:root {
    --primary     : #038047;
    --primary-rgb : 3, 128, 71;
    --primary-lt  : rgba(3,128,71,.10);
    --dark        : #273B4A;
    --slate-50    : #F8FAFC;
    --slate-100   : #F1F5F9;
    --slate-200   : #E2E8F0;
    --slate-300   : #CBD5E1;
    --slate-400   : #94A3B8;
    --slate-500   : #64748B;
    --slate-600   : #475569;
    --slate-700   : #334155;
    --slate-800   : #1E293B;
    --slate-900   : #0F172A;
    --radius      : 8px;
    --radius-sm   : 5px;
    --shadow-sm   : 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
    --shadow-md   : 0 4px 14px rgba(15,23,42,.08);
}

@keyframes fadeUp   { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer  { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin     { to{transform:rotate(360deg)} }
@keyframes kpiIconBounce {
    0%,100% { transform:scale(1) rotate(0deg); }
    30%      { transform:scale(1.25) rotate(-10deg); }
    60%      { transform:scale(1.1) rotate(6deg); }
}
@keyframes kpiShimmer { 0% { left:-100%; } 100% { left:150%; } }

.kpi-icon-bg {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0;
}

.kpi-card-hover {
    will-change: transform, box-shadow;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1) !important, box-shadow .25s ease !important, filter .25s ease !important;
    cursor: default; position: relative !important; overflow: hidden !important;
}
.kpi-card-hover::before {
    content:''; position:absolute; top:0; bottom:0; left:-100%; width:60%;
    background:linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);
    pointer-events:none; z-index:1; transition:none;
}
.kpi-card-hover:hover { transform:translateY(-6px) scale(1.025)!important; box-shadow:0 20px 40px rgba(0,0,0,.25)!important; filter:brightness(1.07)!important; }
.kpi-card-hover:hover::before { animation:kpiShimmer .6s ease forwards; }
.kpi-card-hover:hover .kpi-icon-bg { background:rgba(255,255,255,.35)!important; }
.kpi-card-hover:hover .kpi-icon-bg i { animation:kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both!important; display:inline-block!important; }

.sk-block {
    border-radius:4px;
    background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);
    background-size:200% 100%; animation:shimmer 1.4s infinite;
}
.spin-ring {
    width:26px; height:26px; border:2.5px solid var(--slate-100); border-top-color:var(--primary);
    border-radius:50%; animation:spin .65s linear infinite;
}
.spinner-state {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:48px 20px; gap:12px; color:var(--slate-400); font-size:12px; font-weight:600;
}

/* ══ Search Box ══ */
.st-search-wrap {
    display:flex; gap:10px; align-items:center; margin-bottom:20px;
}
.st-search-input {
    flex:1; max-width:500px; padding:10px 16px; border:2px solid var(--slate-200);
    border-radius:var(--radius); font-size:14px; color:var(--slate-800);
    background:#fff; transition:border-color .15s;
}
.st-search-input:focus { outline:none; border-color:var(--primary); }
.st-search-input::placeholder { color:var(--slate-400); }
.st-search-btn {
    padding:10px 24px; border:none; border-radius:var(--radius);
    background:var(--primary); color:#fff; font-size:14px; font-weight:700;
    cursor:pointer; transition:background .15s;
}
.st-search-btn:hover { background:#026838; }

/* ══ Filter Name ══ */
.st-filter-wrap {
    display:flex; gap:10px; align-items:center; margin-bottom:16px;
}
.st-filter-input {
    padding:7px 12px; border:1px solid var(--slate-200); border-radius:var(--radius-sm);
    font-size:12px; color:var(--slate-700); background:#fff; width:240px;
}
.st-filter-input:focus { outline:none; border-color:var(--primary); }
.st-filter-input::placeholder { color:var(--slate-400); }

/* ══ Chart ══ */
.st-chart-wrap {
    position:relative; width:100%; height:500px;
}

/* ══ Legend List ══ */
.st-legend {
    display:flex; flex-wrap:wrap; gap:8px; padding:12px 0 0;
}
.st-legend-item {
    display:inline-flex; align-items:center; gap:5px; padding:4px 10px;
    border-radius:var(--radius-sm); font-size:11px; font-weight:600;
    color:var(--slate-600); background:var(--slate-50); border:1px solid var(--slate-200);
    cursor:pointer; transition:all .12s;
}
.st-legend-item:hover { border-color:var(--primary); color:var(--primary); }
.st-legend-item.hidden { opacity:.35; text-decoration:line-through; }
.st-legend-dot {
    width:10px; height:10px; border-radius:50%; flex-shrink:0;
}

/* ══ Empty ══ */
.st-empty {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:60px 20px; gap:10px; color:var(--slate-400);
}
.st-empty i { font-size:40px; color:var(--slate-300); }
.st-empty h6 { font-size:14px; font-weight:700; color:var(--slate-500); margin:0; }
.st-empty p  { font-size:12px; margin:0; }
</style>
@endsection

@section('page-title', 'Search Topic')

@section('content')
@php
    $startDate = $startDate ?? now()->subDays(6)->format('Y-m-d');
    $endDate   = $endDate ?? now()->format('Y-m-d');
    $keyword   = $keyword ?? '';
    $projects  = $projects ?? [];
    $projectId = $projectId ?? null;
@endphp

<script>
    const ST_SD = '{{ $startDate }}';
    const ST_ED = '{{ $endDate }}';
    const ST_KW = '{{ addslashes($keyword) }}';
</script>

{{-- Filter (date only, project hidden) --}}
<style>
    .st-page .do-filter-card .do-filter-group:first-child { display: none !important; }
    .st-page .do-filter-card .do-filter-row { justify-content: flex-start; }
</style>
<div class="st-page">
@include('mk.layouts.partials.filter-datepicker')
</div>

{{-- ══ KPI Cards ══ --}}
<div class="row">
    <div class="col-md-6 col-xl-4">
        <div class="card bg-primary text-white kpi-card-hover" style="animation:fadeUp .38s ease-out both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Search Keyword</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiKeyword" style="font-size:16px;">
                            {{ $keyword ?: '—' }}
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-magnifying-glass me-1"></i>Topic yang dicari
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-magnifying-glass"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card bg-success text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .05s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Matched Topics</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiMatched">
                            <div class="sk-block" style="height:28px;width:90px;background:rgba(255,255,255,.2);"></div>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-hash me-1"></i>Topic yang cocok
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-hash"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card bg-warning text-white kpi-card-hover" style="animation:fadeUp .38s ease-out .10s both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Best Rank</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiBestRank">
                            <div class="sk-block" style="height:28px;width:90px;background:rgba(255,255,255,.2);"></div>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-trophy me-1"></i>Posisi tertinggi
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-trophy"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Search + Chart Card ══ --}}
<div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded">
                <i class="ph ph-chart-line-up f-18 text-primary"></i>
            </div>
            <div>
                <h6 class="mb-0">TOPICS</h6>
                <small class="text-muted">Position in Top 50 — cari keyword lalu lihat pergerakan ranking</small>
            </div>
        </div>
        <span class="badge bg-light-primary text-primary" id="stBadge">Enter a keyword</span>
    </div>
    <div class="card-body" style="padding:20px;">
        {{-- Search box --}}
        <div class="st-search-wrap">
            <input type="text" class="st-search-input" id="stKeyword"
                   placeholder="Topics : ketik keyword lalu tekan Search …"
                   value="{{ $keyword }}">
            <button class="st-search-btn" id="stSearchBtn">
                <i class="ph ph-magnifying-glass me-1"></i> Search
            </button>
        </div>

        {{-- Filter name --}}
        <div class="st-filter-wrap" id="stFilterWrap" style="display:none;">
            <input type="text" class="st-filter-input" id="stFilterName"
                   placeholder="Search name …">
        </div>

        {{-- Loading --}}
        <div id="stLoading" style="display:none;" class="spinner-state">
            <div class="spin-ring"></div>
            <span>Memuat data trending…</span>
        </div>

        {{-- Empty --}}
        <div id="stEmpty" style="display:none;">
            <div class="st-empty">
                <i class="ph ph-magnifying-glass"></i>
                <h6>Ketik keyword untuk mulai</h6>
                <p>Masukkan nama topik lalu tekan Search untuk melihat pergerakan ranking</p>
            </div>
        </div>

        {{-- No match --}}
        <div id="stNoMatch" style="display:none;">
            <div class="st-empty">
                <i class="ph ph-x-circle"></i>
                <h6>Tidak ditemukan</h6>
                <p>Keyword tidak cocok dengan trending topic manapun dalam periode ini</p>
            </div>
        </div>

        {{-- Chart --}}
        <div id="stChartContainer" style="display:none;">
            <div class="st-chart-wrap">
                <canvas id="stChart"></canvas>
            </div>
            <div class="st-legend" id="stLegend"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const STLoader = {
    rawData: null,
    chart: null,
    matchedTopics: {},
    hiddenTopics: new Set(),
    COLORS: [
        '#E74C3C','#E67E22','#F1C40F','#2ECC71','#1ABC9C','#3498DB','#9B59B6',
        '#E91E63','#00BCD4','#8BC34A','#FF9800','#795548','#607D8B','#673AB7',
        '#009688','#FF5722','#CDDC39','#03A9F4','#4CAF50','#FFC107'
    ],

    async init() {
        this.bindEvents();
        // Auto-search if keyword is in URL
        if (ST_KW) {
            document.getElementById('stKeyword').value = ST_KW;
            await this.search(ST_KW);
        }
    },

    bindEvents() {
        const input = document.getElementById('stKeyword');
        const btn   = document.getElementById('stSearchBtn');
        btn.addEventListener('click', () => this.search(input.value));
        input.addEventListener('keydown', e => { if (e.key === 'Enter') this.search(input.value); });

        document.getElementById('stFilterName').addEventListener('input', e => {
            this.filterLegend(e.target.value);
        });
    },

    async search(keyword) {
        keyword = (keyword || '').trim();
        if (!keyword) return;

        this.showState('loading');
        document.getElementById('kpiKeyword').textContent = keyword;

        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('keyword', keyword);
        window.history.replaceState({}, '', url);

        try {
            // Fetch trending data
            const sd = new URLSearchParams(window.location.search).get('start_date') || ST_SD;
            const ed = new URLSearchParams(window.location.search).get('end_date')   || ST_ED;
            const res  = await fetch(`/mk/api/trending-topics-twitter?start_date=${encodeURIComponent(sd)}&end_date=${encodeURIComponent(ed)}`);
            const json = await res.json();

            if (!json.success || !json.data || Object.keys(json.data).length === 0) {
                this.showState('empty');
                return;
            }

            this.rawData = json.data;
            this.processAndRender(keyword);
        } catch (err) {
            console.error('ST error:', err);
            this.showState('empty');
        }
    },

    processAndRender(keyword) {
        const kw = keyword.toLowerCase();

        // Sort period keys chronologically
        const keys = Object.keys(this.rawData).sort((a, b) => new Date(a) - new Date(b));

        // Find all topic names matching the keyword
        this.matchedTopics = {};
        keys.forEach(k => {
            const items = this.rawData[k]?.data || [];
            items.forEach(item => {
                const name = item.name || '';
                if (name.toLowerCase().includes(kw)) {
                    if (!this.matchedTopics[name]) {
                        this.matchedTopics[name] = {};
                    }
                    this.matchedTopics[name][k] = item.rank_i || 50;
                }
            });
        });

        const topicNames = Object.keys(this.matchedTopics);

        if (topicNames.length === 0) {
            this.showState('nomatch');
            this.updateKPIs(0, null);
            return;
        }

        this.hiddenTopics.clear();
        this.showState('chart');
        this.renderChart(keys, topicNames);
        this.renderLegend(topicNames);

        // KPIs
        let bestRank = 999;
        topicNames.forEach(name => {
            Object.values(this.matchedTopics[name]).forEach(r => {
                if (r < bestRank) bestRank = r;
            });
        });
        this.updateKPIs(topicNames.length, bestRank);
    },

    renderChart(keys, topicNames) {
        // Build time labels
        const labels = keys.map(k => {
            const period = this.rawData[k];
            const ago = period?.str_datetime_ago?.trim() || '';
            const dt  = period?.date || k;
            return ago || dt;
        });

        // Build datasets
        const datasets = topicNames.map((name, idx) => {
            const color = this.COLORS[idx % this.COLORS.length];
            const data  = keys.map(k => {
                const rank = this.matchedTopics[name]?.[k];
                return rank != null ? rank : null;
            });
            return {
                label: name,
                data: data,
                borderColor: color,
                backgroundColor: color + '22',
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: color,
                tension: 0.3,
                spanGaps: false,
            };
        });

        // Destroy old chart
        if (this.chart) { this.chart.destroy(); this.chart = null; }

        const ctx = document.getElementById('stChart').getContext('2d');
        this.chart = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(30,41,59,.92)',
                        titleFont: { size: 11, weight: '600' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: ctx => {
                                const val = ctx.parsed.y;
                                return val != null ? `${ctx.dataset.label} | rank ${val}` : null;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        reverse: true,
                        min: 1,
                        max: 50,
                        title: {
                            display: true,
                            text: 'Position in Top 50',
                            font: { size: 12, weight: '600' },
                            color: '#64748B',
                        },
                        ticks: {
                            stepSize: 5,
                            font: { size: 11 },
                            color: '#94A3B8',
                        },
                        grid: { color: 'rgba(226,232,240,.5)' },
                    },
                    x: {
                        ticks: {
                            font: { size: 10 },
                            color: '#94A3B8',
                            maxRotation: 45,
                            autoSkip: true,
                            maxTicksLimit: 20,
                        },
                        grid: { display: false },
                    }
                }
            }
        });
    },

    renderLegend(topicNames) {
        const wrap = document.getElementById('stLegend');
        wrap.innerHTML = '';
        topicNames.forEach((name, idx) => {
            const color = this.COLORS[idx % this.COLORS.length];
            const el = document.createElement('div');
            el.className = 'st-legend-item';
            el.innerHTML = `<span class="st-legend-dot" style="background:${this.escHtml(color)}"></span>${this.escHtml(name)}`;
            el.addEventListener('click', () => this.toggleTopic(name, idx, el));
            wrap.appendChild(el);
        });
        document.getElementById('stFilterWrap').style.display = '';
    },

    toggleTopic(name, idx, el) {
        if (this.hiddenTopics.has(name)) {
            this.hiddenTopics.delete(name);
            el.classList.remove('hidden');
            this.chart.data.datasets[idx].hidden = false;
        } else {
            this.hiddenTopics.add(name);
            el.classList.add('hidden');
            this.chart.data.datasets[idx].hidden = true;
        }
        this.chart.update();
    },

    filterLegend(query) {
        const q = query.toLowerCase();
        document.querySelectorAll('.st-legend-item').forEach(el => {
            const name = el.textContent.toLowerCase();
            el.style.display = name.includes(q) ? '' : 'none';
        });
    },

    updateKPIs(matched, bestRank) {
        document.getElementById('kpiMatched').textContent = matched;
        document.getElementById('kpiBestRank').textContent = bestRank && bestRank < 999 ? `#${bestRank}` : '—';
        document.getElementById('stBadge').textContent = matched > 0 ? `${matched} topics` : 'No match';
    },

    showState(state) {
        document.getElementById('stLoading').style.display        = state === 'loading' ? '' : 'none';
        document.getElementById('stEmpty').style.display           = state === 'empty'   ? '' : 'none';
        document.getElementById('stNoMatch').style.display         = state === 'nomatch' ? '' : 'none';
        document.getElementById('stChartContainer').style.display  = state === 'chart'   ? '' : 'none';
    },

    escHtml(s) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(s || ''));
        return d.innerHTML;
    }
};

// Override DPicker form submit for this page
document.addEventListener('DOMContentLoaded', () => {
    STLoader.init();

    const form = document.getElementById('doFilterForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const sd = document.getElementById('hiddenStartDate')?.value || ST_SD;
            const ed = document.getElementById('hiddenEndDate')?.value   || ST_ED;
            const kw = document.getElementById('stKeyword')?.value || '';
            window.location.href = `/mk/search-topic?start_date=${sd}&end_date=${ed}&keyword=${encodeURIComponent(kw)}`;
        }, true);
    }
    const proj = document.getElementById('doProject');
    if (proj) {
        proj.addEventListener('change', e => { e.stopImmediatePropagation(); }, true);
    }
});
</script>
@endsection
