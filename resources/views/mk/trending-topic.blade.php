@extends('mk.layouts.app')

@section('title', 'Trending Topics - SMADIMENT')

@section('styles')
<style>
/* ══════════════════════════════════════════════════════
   DESIGN TOKENS — mirrored from tiktok-most-engagement
══════════════════════════════════════════════════════ */
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

/* ══ Animations ══ */
@keyframes fadeUp   { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
@keyframes shimmer  { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin     { to{transform:rotate(360deg)} }
@keyframes kpiIconBounce {
    0%,100% { transform:scale(1) rotate(0deg); }
    30%      { transform:scale(1.25) rotate(-10deg); }
    60%      { transform:scale(1.1) rotate(6deg); }
}
@keyframes kpiShimmer {
    0%   { left:-100%; }
    100% { left:150%; }
}

/* ══ KPI Icon bg ══ */
.kpi-icon-bg {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.2); font-size:24px; color:#fff; flex-shrink:0;
}

/* ══ Skeleton ══ */
.sk-block {
    border-radius:4px;
    background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);
    background-size:200% 100%; animation:shimmer 1.4s infinite;
}

/* ══ Spinner ══ */
.spin-ring {
    width:26px; height:26px;
    border:2.5px solid var(--slate-100); border-top-color:var(--primary);
    border-radius:50%; animation:spin .65s linear infinite;
}
.spinner-state {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:48px 20px; gap:12px; color:var(--slate-400); font-size:12px; font-weight:600;
}

/* ══ Time Columns Grid — Drone Emprit style ══ */
.tt-columns-wrapper {
    display:flex; gap:16px; overflow-x:auto;
    padding-bottom:8px; margin-bottom:16px;
}
.tt-columns-wrapper::-webkit-scrollbar { height:6px; }
.tt-columns-wrapper::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

.tt-column {
    flex:0 0 300px; min-width:300px;
    background:#fff; border:1px solid var(--slate-200);
    border-radius:var(--radius); box-shadow:var(--shadow-sm);
    display:flex; flex-direction:column; max-height:700px;
}

.tt-col-header {
    padding:14px 16px; border-bottom:1px solid var(--slate-200);
    background:var(--slate-50); border-radius:var(--radius) var(--radius) 0 0;
    text-align:center;
}
.tt-col-ago {
    font-size:12px; font-weight:600; color:var(--primary);
    margin-bottom:3px;
}
.tt-col-date {
    font-size:13px; font-weight:700; color:var(--slate-800);
}

.tt-col-body {
    flex:1; overflow-y:auto; padding:6px 0;
}
.tt-col-body::-webkit-scrollbar { width:4px; }
.tt-col-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

.tt-item {
    display:flex; align-items:center; gap:10px;
    padding:9px 16px; cursor:pointer;
    transition:background .12s;
    border-bottom:1px solid var(--slate-100);
}
.tt-item:last-child { border-bottom:none; }
.tt-item:hover { background:var(--slate-50); }

.tt-item-rank {
    width:24px; height:24px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:10px; font-weight:800; flex-shrink:0;
    background:var(--slate-100); color:var(--slate-400);
    border:1px solid var(--slate-200);
}
.tt-item-rank--1 { background:linear-gradient(135deg,#ffd700,#F59E0B); color:#7c5900; border-color:#ffd700; }
.tt-item-rank--2 { background:linear-gradient(135deg,#c0c0c0,#9ca3af); color:#3d3d3d; border-color:#c0c0c0; }
.tt-item-rank--3 { background:linear-gradient(135deg,#cd7f32,#b06820); color:#fff; border-color:#cd7f32; }

.tt-item-name {
    flex:1; min-width:0;
    font-size:13px; font-weight:600; color:var(--primary);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    text-decoration:none;
}
.tt-item-name:hover { text-decoration:underline; }

.tt-item-vol {
    font-size:11px; font-weight:700; color:var(--slate-400);
    white-space:nowrap; flex-shrink:0;
}

.tt-col-footer {
    padding:8px 14px; border-top:1px solid var(--slate-200);
    display:flex; align-items:center; justify-content:center; gap:6px;
}
.tt-col-nav {
    width:24px; height:24px; border-radius:var(--radius-sm);
    border:1px solid var(--slate-200); background:#fff;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; font-size:12px; color:var(--slate-500);
    transition:all .12s;
}
.tt-col-nav:hover { border-color:var(--primary); color:var(--primary); background:var(--primary-lt); }
.tt-col-nav:disabled { opacity:.3; cursor:not-allowed; }
.tt-col-page {
    font-size:10px; font-weight:700; color:var(--slate-400);
}

/* ══ Sort Options ══ */
.tt-sort-group {
    display:flex; background:var(--slate-50); border-radius:var(--radius-sm);
    padding:2px; gap:2px; border:1px solid var(--slate-200);
}
.tt-sort-btn {
    display:flex; align-items:center; gap:4px;
    padding:5px 12px; border-radius:3px; border:none; background:transparent;
    font-size:11px; font-weight:600; color:var(--slate-500);
    cursor:pointer; transition:background .12s, color .12s;
}
.tt-sort-btn:hover  { background:#fff; color:var(--slate-800); }
.tt-sort-btn.active { background:#fff; color:var(--primary); box-shadow:0 1px 3px rgba(0,0,0,.07); }

/* ══ Empty / Error ══ */
.tt-empty {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    padding:60px 20px; gap:10px; color:var(--slate-400);
}
.tt-empty i { font-size:40px; color:var(--slate-300); }
.tt-empty h6 { font-size:14px; font-weight:700; color:var(--slate-500); margin:0; }
.tt-empty p  { font-size:12px; margin:0; }

/* ══ KPI Card Hover Animations ══ */
.kpi-card-hover {
    will-change: transform, box-shadow;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1) !important,
                box-shadow .25s ease !important,
                filter .25s ease !important;
    cursor: default;
    position: relative !important;
    overflow: hidden !important;
}
.kpi-card-hover::before {
    content: '';
    position: absolute;
    top: 0; bottom: 0;
    left: -100%;
    width: 60%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.22), transparent);
    pointer-events: none;
    z-index: 1;
    transition: none;
}
.kpi-card-hover:hover {
    transform: translateY(-6px) scale(1.025) !important;
    box-shadow: 0 20px 40px rgba(0,0,0,.25) !important;
    filter: brightness(1.07) !important;
}
.kpi-card-hover:hover::before {
    animation: kpiShimmer .6s ease forwards;
}
.kpi-card-hover:hover .kpi-icon-bg {
    background: rgba(255,255,255,.35) !important;
    transition: background .2s ease !important;
}
.kpi-card-hover:hover .kpi-icon-bg i {
    animation: kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both !important;
    display: inline-block !important;
}
.kpi-card-hover:active {
    transform: translateY(-2px) scale(1.01) !important;
    transition-duration: .08s !important;
}

/* ══ Responsive ══ */
@media(max-width:768px) {
    .tt-column { flex:0 0 240px; min-width:240px; }
}
</style>
@endsection

@section('page-title', 'Trending Topics')

@section('content')
@php
    $startDate = $startDate ?? now()->subDays(6)->format('Y-m-d');
    $endDate   = $endDate ?? now()->format('Y-m-d');
    // filter-datepicker needs $projects, $projectId — set dummies for this page since it's not project-based
    $projects  = $projects ?? [];
    $projectId = $projectId ?? null;
@endphp

<script>
    const TT_SD = '{{ $startDate }}';
    const TT_ED = '{{ $endDate }}';
</script>

{{-- Filter --}}
<style>
    /* Hide project selector — Trending Topics doesn't use projects */
    .tt-page .do-filter-card .do-filter-group:first-child { display: none !important; }
    .tt-page .do-filter-card .do-filter-row { justify-content: flex-start; }
</style>
<div class="tt-page">
@include('mk.layouts.partials.filter-datepicker')
</div>

{{-- ══ KPI Cards ══ --}}
<div class="row">
    <div class="col-md-6 col-xl-4">
        <div class="card bg-primary text-white kpi-card-hover" style="animation:fadeUp .38s ease-out both;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-1 text-white text-opacity-75 f-12">Total Periods</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiPeriods">
                            <div class="sk-block" style="height:28px;width:90px;border-radius:4px;background:rgba(255,255,255,.2);"></div>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-clock me-1"></i>Snapshot waktu
                        </p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="kpi-icon-bg"><i class="ph ph-clock"></i></div>
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
                        <p class="mb-1 text-white text-opacity-75 f-12">Unique Topics</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiTopics">
                            <div class="sk-block" style="height:28px;width:90px;border-radius:4px;background:rgba(255,255,255,.2);"></div>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-hash me-1"></i>Trending unik
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
                        <p class="mb-1 text-white text-opacity-75 f-12">Top Topic</p>
                        <h3 class="mb-0 text-white f-w-300" id="kpiTopTopic" style="font-size:16px;">
                            <div class="sk-block" style="height:28px;width:90px;border-radius:4px;background:rgba(255,255,255,.2);"></div>
                        </h3>
                        <p class="mb-0 mt-2 text-white text-opacity-75 f-12">
                            <i class="ph ph-trophy me-1"></i>Paling banyak muncul
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

{{-- ══ Sort & Controls ══ --}}
<div class="card mb-3" style="animation:fadeUp .38s ease-out .18s both;">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="avtar avtar-xs bg-light-primary rounded">
                <i class="ph ph-trend-up f-18 text-primary"></i>
            </div>
            <div>
                <h6 class="mb-0">Twitter Trending Topics</h6>
                <small class="text-muted">Data dari Twitter/X Indonesia — klik topik untuk buka di Twitter</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="tt-sort-group">
                <button class="tt-sort-btn" onclick="TTSort('mention')" id="sortMention">
                    <i class="ph ph-sort-descending"></i> By Mention
                </button>
                <button class="tt-sort-btn active" onclick="TTSort('rank')" id="sortRank">
                    <i class="ph ph-list-numbers"></i> By Rank
                </button>
            </div>
            <span class="badge bg-light-primary text-primary" id="ttBadge">Loading…</span>
        </div>
    </div>
    <div class="card-body" style="padding:16px;">
        {{-- Loading --}}
        <div id="ttLoading" class="spinner-state">
            <div class="spin-ring"></div>
            <span>Memuat trending topics…</span>
        </div>

        {{-- Empty --}}
        <div id="ttEmpty" style="display:none;">
            <div class="tt-empty">
                <i class="ph ph-trend-up"></i>
                <h6>Tidak ada data</h6>
                <p>Pilih rentang tanggal lain atau coba lagi nanti</p>
            </div>
        </div>

        {{-- Columns —  like Drone Emprit --}}
        <div id="ttColumns" class="tt-columns-wrapper" style="display:none;"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ========================================
// TRENDING TOPICS LOADER
// ========================================
const TTLoader = {
    rawData: {},
    sortedKeys: [],
    sortBy: 'rank',
    PER_PAGE: 20,
    pageState: {},

    async init() {
        await this.loadData();
    },

    async loadData() {
        const sd = new URLSearchParams(window.location.search).get('start_date') || TT_SD;
        const ed = new URLSearchParams(window.location.search).get('end_date')   || TT_ED;

        document.getElementById('ttLoading').style.display = '';
        document.getElementById('ttEmpty').style.display = 'none';
        document.getElementById('ttColumns').style.display = 'none';

        try {
            const res  = await fetch(`/mk/api/trending-topics-twitter?start_date=${encodeURIComponent(sd)}&end_date=${encodeURIComponent(ed)}`);
            const json = await res.json();

            if (!json.success || !json.data || Object.keys(json.data).length === 0) {
                this.showEmpty();
                return;
            }

            this.rawData = json.data;
            this.sortedKeys = Object.keys(this.rawData).sort((a, b) => new Date(b) - new Date(a));
            this.updateKPIs();
            this.render();
        } catch (err) {
            console.error('TT load error:', err);
            this.showEmpty();
        }
    },

    updateKPIs() {
        const keys   = this.sortedKeys;
        const allTopics = new Set();
        const topicCount = {};

        keys.forEach(k => {
            const items = this.rawData[k]?.data || [];
            items.forEach(item => {
                const name = item.name || '';
                allTopics.add(name);
                topicCount[name] = (topicCount[name] || 0) + 1;
            });
        });

        let topTopic = '—';
        let topCount = 0;
        for (const [name, cnt] of Object.entries(topicCount)) {
            if (cnt > topCount) { topCount = cnt; topTopic = name; }
        }

        document.getElementById('kpiPeriods').textContent  = this.fmtN(keys.length);
        document.getElementById('kpiTopics').textContent   = this.fmtN(allTopics.size);
        document.getElementById('kpiTopTopic').textContent = this.truncate(topTopic, 25);
        document.getElementById('ttBadge').textContent     = `${keys.length} snapshots`;
    },

    render() {
        document.getElementById('ttLoading').style.display = 'none';
        document.getElementById('ttEmpty').style.display = 'none';
        const wrap = document.getElementById('ttColumns');
        wrap.style.display = '';
        wrap.innerHTML = '';

        // Build topic frequency map for "By Mention" sort
        this._topicFreq = {};
        this.sortedKeys.forEach(k => {
            (this.rawData[k]?.data || []).forEach(item => {
                const name = item.name || '';
                this._topicFreq[name] = (this._topicFreq[name] || 0) + 1;
            });
        });

        this.sortedKeys.forEach(key => {
            const period = this.rawData[key];
            let items = [...(period.data || [])];

            if (this.sortBy === 'rank') {
                items.sort((a, b) => (a.rank_i || 999) - (b.rank_i || 999));
            } else {
                // By Mention — sort by frequency across periods (most appeared first)
                items.sort((a, b) => {
                    const aFreq = this._topicFreq[a.name] || 0;
                    const bFreq = this._topicFreq[b.name] || 0;
                    return bFreq - aFreq || (a.rank_i || 999) - (b.rank_i || 999);
                });
            }

            this.pageState[key] = this.pageState[key] || 0;
            const page    = this.pageState[key];
            const total   = items.length;
            const maxPage = Math.max(0, Math.ceil(total / this.PER_PAGE) - 1);
            const sliced  = items.slice(page * this.PER_PAGE, (page + 1) * this.PER_PAGE);

            const col = document.createElement('div');
            col.className = 'tt-column';

            const ago = period.str_datetime_ago ? period.str_datetime_ago.trim() : '';
            const dateStr = period.date || key;

            col.innerHTML = `
                <div class="tt-col-header">
                    <div class="tt-col-ago">${this.escHtml(ago || 'Just now')}</div>
                    <div class="tt-col-date">${this.escHtml(dateStr)}</div>
                </div>
                <div class="tt-col-body">
                    ${sliced.map((item, idx) => {
                        const rank = item.rank_i || (page * this.PER_PAGE + idx + 1);
                        const displayRank = page * this.PER_PAGE + idx + 1;
                        const rankCls = displayRank <= 3 ? ` tt-item-rank--${displayRank}` : '';
                        const name = item.name || decodeURIComponent(item.query_s || '');
                        const freq = this._topicFreq[name] || 0;
                        const url  = item.url || `https://twitter.com/search?q=${encodeURIComponent(name)}`;

                        return `<div class="tt-item" title="${this.escHtml(name)} — Rank #${rank}${freq > 1 ? ', muncul ' + freq + 'x' : ''}">
                            <div class="tt-item-rank${rankCls}">${displayRank}</div>
                            <a href="${this.escHtml(url)}" target="_blank" rel="noopener" class="tt-item-name">${this.escHtml(name)}</a>
                            <span class="tt-item-vol">#${rank}</span>
                        </div>`;
                    }).join('')}
                </div>
                ${total > this.PER_PAGE ? `
                <div class="tt-col-footer">
                    <button class="tt-col-nav" onclick="TTLoader.paginate('${this.escHtml(key)}',-1)" ${page <= 0 ? 'disabled' : ''}><i class="ph ph-caret-left"></i></button>
                    <span class="tt-col-page">${page+1}/${maxPage+1}</span>
                    <button class="tt-col-nav" onclick="TTLoader.paginate('${this.escHtml(key)}',1)" ${page >= maxPage ? 'disabled' : ''}><i class="ph ph-caret-right"></i></button>
                </div>` : ''}
            `;

            wrap.appendChild(col);
        });
    },

    paginate(key, dir) {
        const total   = (this.rawData[key]?.data || []).length;
        const maxPage = Math.max(0, Math.ceil(total / this.PER_PAGE) - 1);
        const cur     = this.pageState[key] || 0;
        const next    = Math.max(0, Math.min(maxPage, cur + dir));
        if (next !== cur) {
            this.pageState[key] = next;
            this.render();
        }
    },

    showEmpty() {
        document.getElementById('ttLoading').style.display = 'none';
        document.getElementById('ttColumns').style.display = 'none';
        document.getElementById('ttEmpty').style.display = '';
        document.getElementById('kpiPeriods').textContent = '0';
        document.getElementById('kpiTopics').textContent  = '0';
        document.getElementById('kpiTopTopic').textContent = '—';
        document.getElementById('ttBadge').textContent     = 'No data';
    },

    fmtN(n) { return new Intl.NumberFormat('id-ID').format(n || 0); },

    truncate(s, n) { return s && s.length > n ? s.slice(0, n) + '…' : (s || '—'); },

    escHtml(s) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(s || ''));
        return d.innerHTML;
    }
};

function TTSort(by) {
    TTLoader.sortBy = by;
    TTLoader.pageState = {};
    document.getElementById('sortMention').classList.toggle('active', by === 'mention');
    document.getElementById('sortRank').classList.toggle('active', by === 'rank');
    TTLoader.render();
}

// Override DPicker form submit for this page — prevent project-based redirect
document.addEventListener('DOMContentLoaded', () => {
    TTLoader.init();

    // Override form submit to just reload with date params
    const form = document.getElementById('doFilterForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const sd = document.getElementById('hiddenStartDate')?.value || TT_SD;
            const ed = document.getElementById('hiddenEndDate')?.value   || TT_ED;
            window.location.href = `/mk/trending-topic?start_date=${sd}&end_date=${ed}`;
        }, true);
    }
    // Override project change — do nothing (not project-based)
    const proj = document.getElementById('doProject');
    if (proj) {
        proj.addEventListener('change', e => { e.stopImmediatePropagation(); }, true);
    }
});
</script>
@endsection
