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

/* ══ Column Header — three-zone layout ══ */
.tt-col-header {
    padding:10px 12px;
    border-bottom:1px solid var(--slate-200);
    background:var(--slate-50);
    border-radius:var(--radius) var(--radius) 0 0;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    min-height:52px;
}

/* Centre text block */
.tt-col-header-center {
    flex:1;
    text-align:center;
    min-width:0;
}
.tt-col-ago {
    font-size:11px; font-weight:700; color:var(--primary);
    margin-bottom:2px;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.tt-col-date {
    font-size:12px; font-weight:600; color:var(--slate-700);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}

/* ══ Per-Card Export Buttons ══ */
.tt-card-export-group {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

.tt-card-export-btn {
    width: 26px;
    height: 26px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--slate-200);
    background: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    color: var(--slate-400);
    transition: all .15s ease;
    position: relative;
    flex-shrink: 0;
    padding: 0;
    line-height: 1;
}
.tt-card-export-btn:hover {
    background: var(--slate-50);
    border-color: var(--slate-300);
    color: var(--slate-700);
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(15,23,42,.10);
}
.tt-card-export-btn.btn-png:hover {
    border-color: var(--cyan);
    color: var(--cyan);
    background: var(--cyan-light);
}
.tt-card-export-btn.btn-pdf:hover {
    border-color: var(--red);
    color: var(--red);
    background: var(--red-light);
}
.tt-card-export-btn:disabled,
.tt-card-export-btn.exporting {
    opacity: .4;
    cursor: not-allowed;
    pointer-events: none;
}
.tt-card-export-btn.exporting i {
    animation: spin .6s linear infinite;
}

/* Tooltip on hover */
.tt-card-export-btn[data-tooltip] {
    position: relative;
}
.tt-card-export-btn[data-tooltip]::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: calc(100% + 5px);
    left: 50%;
    transform: translateX(-50%);
    background: var(--slate-800);
    color: #fff;
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
    padding: 3px 7px;
    border-radius: 4px;
    pointer-events: none;
    opacity: 0;
    transition: opacity .15s ease;
    z-index: 10;
}
.tt-card-export-btn[data-tooltip]:hover::after {
    opacity: 1;
}

/* Left spacer — mirrors the right export group for symmetric centering */
.tt-col-header-spacer {
    width: 60px; /* approx width of 2 icon buttons + gap */
    flex-shrink: 0;
}

/* ══ Column Body / Footer ══ */
.tt-col-body {
    flex:1; overflow-y:auto; padding:6px 0;
}
.tt-col-body::-webkit-scrollbar { width:4px; }
.tt-col-body::-webkit-scrollbar-thumb { background:var(--slate-200); border-radius:99px; }

/*
 * .snapshot-slide — used only inside the off-screen export container.
 * Each slide block holds one "page" of items stacked vertically.
 * Not visible in the live UI; html2canvas reads these styles at capture time.
 */
.snapshot-slide {
    overflow: visible;
    height: auto;
    padding: 6px 0;
}

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

/* ══ Export Overlay Toast ══ */
#ttExportToast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    background: var(--slate-800);
    color: #fff;
    border-radius: var(--radius);
    padding: 12px 18px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: var(--shadow-lg);
    opacity: 0;
    transform: translateY(12px);
    transition: opacity .22s ease, transform .22s ease;
    pointer-events: none;
}
#ttExportToast.show {
    opacity: 1;
    transform: translateY(0);
}
#ttExportToast .spin-ring {
    width: 16px; height: 16px;
    border-width: 2px;
}

/* ══ Responsive ══ */
@media(max-width:768px) {
    .tt-column { flex:0 0 240px; min-width:240px; }
    .tt-card-export-group { display: none !important; }
    .tt-col-header-spacer { display: none !important; }
}
</style>
@endsection

@section('page-title', 'Trending Topics')

@section('content')
@php
    $startDate = $startDate ?? now()->subDays(6)->format('Y-m-d');
    $endDate   = $endDate ?? now()->format('Y-m-d');
    $projects  = $projects ?? [];
    $projectId = $projectId ?? null;
@endphp

<script>
    const TT_SD = '{{ $startDate }}';
    const TT_ED = '{{ $endDate }}';
</script>

{{-- Filter --}}
<style>
    /* Hide project selector */
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
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="tt-sort-group">
                <button class="tt-sort-btn" onclick="TTSort('mention')" id="sortMention">
                    <i class="ph ph-sort-descending"></i> By Mention
                </button>
                <button class="tt-sort-btn active" onclick="TTSort('rank')" id="sortRank">
                    <i class="ph ph-list-numbers"></i> By Rank
                </button>
            </div>
            <span class="badge bg-light-primary text-primary" id="ttBadge">Loading…</span>
            {{-- Global export removed — each card now has its own export buttons --}}
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

        {{-- Columns — like Drone Emprit --}}
        <div id="ttColumns" class="tt-columns-wrapper" style="display:none;"></div>
    </div>
</div>

{{-- ══ Export Toast Notification ══ --}}
<div id="ttExportToast">
    <div class="spin-ring"></div>
    <span id="ttExportToastMsg">Menyiapkan export…</span>
</div>
@endsection

@section('scripts')
{{-- html2canvas & jsPDF via CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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

        this.sortedKeys.forEach((key, colIndex) => {
            const period = this.rawData[key];
            let items = [...(period.data || [])];

            if (this.sortBy === 'rank') {
                items.sort((a, b) => (a.rank_i || 999) - (b.rank_i || 999));
            } else {
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

            // Unique, safe DOM id for each snapshot card
            const cardId  = `snapshot-card-${colIndex}`;
            const safeKey = this.escAttr(key);

            const col = document.createElement('div');
            col.className    = 'tt-column';
            col.id           = cardId;
            col.dataset.ttKey = key;   // ← raw data key; used by export to fetch all slides

            const ago     = period.str_datetime_ago ? period.str_datetime_ago.trim() : '';
            const dateStr = period.date || key;

            col.innerHTML = `
                <!-- ══ Card Header: left spacer | center text | right export ══ -->
                <div class="tt-col-header">

                    <!-- Invisible spacer keeps center block truly centered -->
                    <div class="tt-col-header-spacer" aria-hidden="true"></div>

                    <!-- Centre: timestamp labels -->
                    <div class="tt-col-header-center">
                        <div class="tt-col-ago">${this.escHtml(ago || 'Just now')}</div>
                        <div class="tt-col-date">${this.escHtml(dateStr)}</div>
                    </div>

                    <!-- Right: per-card export buttons -->
                    <div class="tt-card-export-group">
                        <button
                            class="tt-card-export-btn btn-png"
                            data-tooltip="Export PNG"
                            onclick="TTExport.card('${cardId}', 'png', this)"
                            title="Export snapshot sebagai PNG"
                            aria-label="Export PNG">
                            <i class="ph ph-image"></i>
                        </button>
                        <button
                            class="tt-card-export-btn btn-pdf"
                            data-tooltip="Export PDF"
                            onclick="TTExport.card('${cardId}', 'pdf', this)"
                            title="Export snapshot sebagai PDF"
                            aria-label="Export PDF">
                            <i class="ph ph-file-pdf"></i>
                        </button>
                    </div>
                </div>

                <!-- ══ Card Body: topic list ══ -->
                <div class="tt-col-body">
                    ${sliced.map((item, idx) => {
                        const displayRank = page * this.PER_PAGE + idx + 1;
                        const name        = item.name || decodeURIComponent(item.query_s || '');
                        const freq        = this._topicFreq[name] || 0;
                        return this.buildItemHTML(item, displayRank, freq);
                    }).join('')}
                </div>

                <!-- ══ Pagination footer ══ -->
                ${total > this.PER_PAGE ? `
                <div class="tt-col-footer">
                    <button class="tt-col-nav" onclick="TTLoader.paginate('${safeKey}',-1)" ${page <= 0 ? 'disabled' : ''}><i class="ph ph-caret-left"></i></button>
                    <span class="tt-col-page">${page + 1}/${maxPage + 1}</span>
                    <button class="tt-col-nav" onclick="TTLoader.paginate('${safeKey}',1)" ${page >= maxPage ? 'disabled' : ''}><i class="ph ph-caret-right"></i></button>
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
        document.getElementById('kpiPeriods').textContent  = '0';
        document.getElementById('kpiTopics').textContent   = '0';
        document.getElementById('kpiTopTopic').textContent = '—';
        document.getElementById('ttBadge').textContent     = 'No data';
    },

    fmtN(n)          { return new Intl.NumberFormat('id-ID').format(n || 0); },
    truncate(s, n)   { return s && s.length > n ? s.slice(0, n) + '…' : (s || '—'); },
    escHtml(s)       {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(s || ''));
        return d.innerHTML;
    },
    // Safe for use inside onclick="..." attribute strings
    escAttr(s)       { return (s || '').replace(/'/g, "\\'").replace(/"/g, '&quot;'); },

    /**
     * Render a single .tt-item row as an HTML string.
     * Shared by the live renderer AND the export engine so styling is identical.
     *
     * @param {object} item         - raw topic object from the API
     * @param {number} displayRank  - 1-based visual rank (across all pages)
     * @param {number} freq         - how many snapshots this topic appeared in
     * @returns {string}
     */
    buildItemHTML(item, displayRank, freq = 0) {
        const rankCls = displayRank <= 3 ? ` tt-item-rank--${displayRank}` : '';
        const name    = item.name || decodeURIComponent(item.query_s || '');
        const rank    = item.rank_i || displayRank;
        const url     = item.url || `https://twitter.com/search?q=${encodeURIComponent(name)}`;
        return `<div class="tt-item" title="${this.escHtml(name)} — Rank #${rank}${freq > 1 ? ', muncul ' + freq + 'x' : ''}">
            <div class="tt-item-rank${rankCls}">${displayRank}</div>
            <a href="${this.escHtml(url)}" target="_blank" rel="noopener" class="tt-item-name">${this.escHtml(name)}</a>
            <span class="tt-item-vol">#${rank}</span>
        </div>`;
    },

    /**
     * Return all items for a given data key, sorted according to the current sortBy.
     * Used by both the renderer and the export engine.
     *
     * @param {string} key
     * @returns {Array}
     */
    getSortedItems(key) {
        const items = [...(this.rawData[key]?.data || [])];
        if (this.sortBy === 'rank') {
            items.sort((a, b) => (a.rank_i || 999) - (b.rank_i || 999));
        } else {
            items.sort((a, b) => {
                const aFreq = (this._topicFreq?.[a.name] || 0);
                const bFreq = (this._topicFreq?.[b.name] || 0);
                return bFreq - aFreq || (a.rank_i || 999) - (b.rank_i || 999);
            });
        }
        return items;
    },
};

function TTSort(by) {
    TTLoader.sortBy = by;
    TTLoader.pageState = {};
    document.getElementById('sortMention').classList.toggle('active', by === 'mention');
    document.getElementById('sortRank').classList.toggle('active', by === 'rank');
    TTLoader.render();
}

// ========================================
// EXPORT ENGINE
// ========================================
const TTExport = {

    _toast(msg, show = true) {
        const t = document.getElementById('ttExportToast');
        document.getElementById('ttExportToastMsg').textContent = msg;
        t.classList.toggle('show', show);
    },

    _lockBtn(btn, lock) {
        if (!btn) return;
        if (lock) {
            btn.classList.add('exporting');
            btn.disabled = true;
        } else {
            btn.classList.remove('exporting');
            btn.disabled = false;
        }
    },

    /**
     * Build and capture a complete snapshot export — ALL slides, not just the
     * currently visible page.
     *
     * How it works
     * ────────────
     * 1. Read `data-tt-key` from the card element to look up the full item list
     *    in TTLoader.rawData (data-first — no dependency on which DOM page is shown).
     * 2. Apply the current sort order via TTLoader.getSortedItems().
     * 3. Chunk items into pages (slides) matching TTLoader.PER_PAGE.
     * 4. Build an off-screen wrapper containing:
     *      • a branded watermark header
     *      • one .snapshot-slide block per page, each with a divider pill label
     *        and the full list of .tt-item rows for that slide
     * 5. Run html2canvas at 2× for crisp high-DPI output.
     * 6. Tear down the off-screen node in a `finally` block.
     *
     * @param {string} cardId  - DOM id of the snapshot card (e.g. "snapshot-card-0")
     * @returns {Promise<{canvas, sd, ed, dateLabel}>}
     */
    async _captureCard(cardId) {
        const card = document.getElementById(cardId);
        if (!card) throw new Error(`Card #${cardId} not found`);

        /* ── 1. Resolve the raw data key ──────────────────────────────────── */
        const key = card.dataset.ttKey;
        if (!key || !TTLoader.rawData[key]) {
            throw new Error(`No raw data found for card #${cardId} (key="${key}")`);
        }

        const period   = TTLoader.rawData[key];
        const agoText  = period.str_datetime_ago?.trim() || 'Just now';
        const dateText = period.date || key;

        const sd = new URLSearchParams(window.location.search).get('start_date') || TT_SD;
        const ed = new URLSearchParams(window.location.search).get('end_date')   || TT_ED;

        /* ── 2. Sort all items with the current sort mode ─────────────────── */
        const allItems  = TTLoader.getSortedItems(key);
        const PER_PAGE  = TTLoader.PER_PAGE;
        const totalPages = Math.max(1, Math.ceil(allItems.length / PER_PAGE));

        /* ── 3. Chunk into slide arrays ───────────────────────────────────── */
        const slides = Array.from({ length: totalPages }, (_, i) =>
            allItems.slice(i * PER_PAGE, (i + 1) * PER_PAGE)
        );

        /* ── 4. Build off-screen export wrapper ───────────────────────────── */
        const CARD_W  = 300;
        const PADDING = 20;

        const offscreen = document.createElement('div');
        offscreen.style.cssText = `
            position: fixed;
            top: -99999px;
            left: -99999px;
            width: ${CARD_W + PADDING * 2}px;
            background: #F1F5F8;
            padding: ${PADDING}px;
            box-sizing: border-box;
            font-family: inherit;
            z-index: -1;
        `;

        /* ── 4a. Watermark header ─────────────────────────────────────────── */
        const header = document.createElement('div');
        header.style.cssText = `
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #E2E8F0;
        `;
        header.innerHTML = `
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:32px;height:32px;border-radius:8px;background:#038047;
                            display:flex;align-items:center;justify-content:center;
                            color:#fff;font-size:16px;flex-shrink:0;">&#x23;</div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:#1E293B;line-height:1.2;">
                        ${TTLoader.escHtml(agoText)}
                    </div>
                    <div style="font-size:10px;color:#64748B;margin-top:1px;">
                        ${TTLoader.escHtml(dateText)}
                    </div>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:9px;color:#94A3B8;font-weight:600;line-height:1.4;">
                    SMADIMENT<br>Twitter Trending Topics
                </div>
            </div>
        `;
        offscreen.appendChild(header);

        /* ── 4b. Card shell (white rounded container) ─────────────────────── */
        const cardShell = document.createElement('div');
        cardShell.className = 'tt-column';          // inherits all .tt-column CSS
        cardShell.style.cssText = `
            width: ${CARD_W}px;
            min-width: ${CARD_W}px;
            max-height: none;
            height: auto;
            overflow: visible;
            display: flex;
            flex-direction: column;
        `;

        /* Card header row — centred, no export buttons */
        const cardHeader = document.createElement('div');
        cardHeader.className = 'tt-col-header';
        cardHeader.style.cssText = `
            justify-content: center;
            padding: 10px 14px;
        `;
        cardHeader.innerHTML = `
            <div class="tt-col-header-center">
                <div class="tt-col-ago">${TTLoader.escHtml(agoText)}</div>
                <div class="tt-col-date">${TTLoader.escHtml(dateText)}</div>
            </div>
        `;
        cardShell.appendChild(cardHeader);

        /* ── 4c. One .snapshot-slide block per page ───────────────────────── */
        slides.forEach((pageItems, slideIdx) => {
            /* Slide divider — only between slides, not before the first */
            if (slideIdx > 0) {
                const divider = document.createElement('div');
                divider.style.cssText = `
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 6px 14px;
                    background: #F8FAFC;
                    border-top: 1px solid #E2E8F0;
                    border-bottom: 1px solid #E2E8F0;
                `;
                divider.innerHTML = `
                    <span style="
                        font-size: 9px; font-weight: 800; text-transform: uppercase;
                        letter-spacing: .04em; color: #94A3B8;
                        background: #E2E8F0; border-radius: 99px;
                        padding: 2px 8px; white-space: nowrap; flex-shrink: 0;
                    ">Halaman ${slideIdx + 1} / ${totalPages}</span>
                    <div style="flex:1;height:1px;background:#E2E8F0;"></div>
                `;
                cardShell.appendChild(divider);
            }

            /* Slide body — all items for this page */
            const slideBody = document.createElement('div');
            slideBody.className = 'snapshot-slide';    // semantic hook
            slideBody.style.cssText = `
                overflow: visible;
                height: auto;
                padding: 6px 0;
            `;

            const globalOffset = slideIdx * PER_PAGE;
            slideBody.innerHTML = pageItems.map((item, idx) => {
                const displayRank = globalOffset + idx + 1;
                const name        = item.name || decodeURIComponent(item.query_s || '');
                const freq        = TTLoader._topicFreq?.[name] || 0;
                return TTLoader.buildItemHTML(item, displayRank, freq);
            }).join('');

            cardShell.appendChild(slideBody);
        });

        offscreen.appendChild(cardShell);

        /* ── 4d. Watermark footer ─────────────────────────────────────────── */
        const footer = document.createElement('div');
        footer.style.cssText = `
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 9px;
            color: #94A3B8;
            font-weight: 600;
        `;
        footer.innerHTML = `
            <span>${allItems.length} topik · ${totalPages} halaman</span>
            <span>${new Date().toLocaleString('id-ID', { dateStyle:'short', timeStyle:'short' })}</span>
        `;
        offscreen.appendChild(footer);

        document.body.appendChild(offscreen);

        /* ── 5. Capture ───────────────────────────────────────────────────── */
        try {
            const canvas = await html2canvas(offscreen, {
                scale          : 2,
                useCORS        : true,
                allowTaint     : true,
                logging        : false,
                backgroundColor: '#F1F5F8',
                width          : offscreen.offsetWidth,
                height         : offscreen.offsetHeight,
                windowHeight   : offscreen.offsetHeight,
            });
            return {
                canvas,
                sd,
                ed,
                dateLabel: dateText.replace(/[/\s:]/g, '-'),
            };
        } finally {
            /* ── 6. Tear down ─────────────────────────────────────────────── */
            document.body.removeChild(offscreen);
        }
    },

    /**
     * Per-card export entry point.
     *
     * @param {string}      cardId  - snapshot card DOM id
     * @param {'png'|'pdf'} type    - export format
     * @param {HTMLElement} btnEl   - the button that was clicked (for spinner feedback)
     */
    async card(cardId, type = 'png', btnEl = null) {
        this._lockBtn(btnEl, true);
        this._toast(`Menyiapkan ${type.toUpperCase()} — semua slide snapshot…`);

        try {
            const { canvas, sd, ed, dateLabel } = await this._captureCard(cardId);
            const filename = `trending-topic_${dateLabel}_${sd}_${ed}`;

            if (type === 'png') {
                await this._downloadPNG(canvas, filename);
            } else {
                await this._downloadPDF(canvas, filename);
            }

            this._toast(`${type.toUpperCase()} berhasil diunduh ✓`);
            setTimeout(() => this._toast('', false), 2800);

        } catch (err) {
            console.error(`[TTExport.card] ${type} error:`, err);
            this._toast(`Gagal export ${type.toUpperCase()} — coba lagi`);
            setTimeout(() => this._toast('', false), 3200);
        } finally {
            this._lockBtn(btnEl, false);
        }
    },

    // ── Download helpers ──────────────────────────────────────────────────────

    async _downloadPNG(canvas, filename) {
        const link = document.createElement('a');
        link.download = `${filename}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
    },

    async _downloadPDF(canvas, filename) {
        const PX_PER_MM = (2 * 96) / 25.4; // scale 2 × 96 dpi ÷ 25.4 mm/in ≈ 7.559
        const imgWmm    = canvas.width  / PX_PER_MM;
        const imgHmm    = canvas.height / PX_PER_MM;
        const { jsPDF } = window.jspdf;

        const doc = new jsPDF({
            orientation: imgWmm > imgHmm ? 'landscape' : 'portrait',
            unit        : 'mm',
            format      : [imgWmm, imgHmm],
            compress    : true,
        });
        doc.addImage(canvas, 'PNG', 0, 0, imgWmm, imgHmm, undefined, 'FAST');
        doc.save(`${filename}.pdf`);
    },
};

// ========================================
// INIT
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    TTLoader.init();

    // Override form submit — prevent project-based redirect
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
    // Override project change — not project-based
    const proj = document.getElementById('doProject');
    if (proj) {
        proj.addEventListener('change', e => { e.stopImmediatePropagation(); }, true);
    }
});
</script>
@endsection