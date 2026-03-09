@extends('mk.layouts.app')

@section('title', 'X Most Engagement')

@section('content')
<div class="page-wrapper" id="mostEngagementPage">

    {{-- ── Header ── --}}
    <div class="page-header">
        <div class="header-left">
            <div class="page-title-wrap">
                <div class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
                        <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
                    </svg>
                </div>
                <div>
                    <h1 class="page-title">Most Engagement</h1>
                    <p class="page-subtitle">Top performing posts by engagement on X (Twitter)</p>
                </div>
            </div>
        </div>
        <div class="header-right">
            {{-- Date Picker --}}
            <div class="date-picker-wrap">
                <div class="date-presets">
                    <button class="preset-btn" data-days="7">7D</button>
                    <button class="preset-btn" data-days="14">14D</button>
                    <button class="preset-btn" data-days="30">30D</button>
                </div>
                <div class="date-inputs">
                    <input type="date" id="startDate" class="date-input" value="{{ $startDate }}">
                    <span class="date-sep">→</span>
                    <input type="date" id="endDate" class="date-input" value="{{ $endDate }}">
                </div>
                <button class="btn-apply" id="applyDate">Apply</button>
            </div>
            {{-- Project Selector --}}
            <div class="project-selector-wrap">
                <select id="projectSelect" class="project-select">
                    @forelse($projects as $project)
                        <option value="{{ $project['id'] }}" {{ isset($projectId) && $projectId == $project['id'] ? 'selected' : '' }}>
                            {{ $project['name'] ?? 'Project '.$project['id'] }}
                        </option>
                    @empty
                        <option value="">No projects</option>
                    @endforelse
                </select>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="stats-grid" id="statsGrid">
        <div class="stat-card" id="cardViews">
            <div class="stat-icon views-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </div>
            <div class="stat-info">
                <p class="stat-label">Total Views</p>
                <h3 class="stat-value" id="totalViews">—</h3>
                <p class="stat-meta" id="viewsMeta">Top post views</p>
            </div>
        </div>
        <div class="stat-card" id="cardRetweets">
            <div class="stat-icon rt-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            </div>
            <div class="stat-info">
                <p class="stat-label">Total Retweets</p>
                <h3 class="stat-value" id="totalRetweets">—</h3>
                <p class="stat-meta" id="retweetsMeta">Top post retweets</p>
            </div>
        </div>
        <div class="stat-card" id="cardLikes">
            <div class="stat-icon likes-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <div class="stat-info">
                <p class="stat-label">Total Likes</p>
                <h3 class="stat-value" id="totalLikes">—</h3>
                <p class="stat-meta" id="likesMeta">Top post likes</p>
            </div>
        </div>
        <div class="stat-card" id="cardReplies">
            <div class="stat-icon replies-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="stat-info">
                <p class="stat-label">Total Replies</p>
                <h3 class="stat-value" id="totalReplies">—</h3>
                <p class="stat-meta" id="repliesMeta">Top post replies</p>
            </div>
        </div>
    </div>

    {{-- ── Main Content ── --}}
    <div class="content-grid">

        {{-- Donut Chart --}}
        <div class="chart-card donut-card">
            <div class="card-header">
                <h3 class="card-title">Top 5 Distribution</h3>
                <div class="chart-metric-tabs">
                    <button class="metric-tab active" data-metric="views">Views</button>
                    <button class="metric-tab" data-metric="retweets">RT</button>
                    <button class="metric-tab" data-metric="likes">Likes</button>
                    <button class="metric-tab" data-metric="replies">Replies</button>
                </div>
            </div>
            <div id="donutChart" style="height:280px;"></div>
        </div>

        {{-- Bar Chart --}}
        <div class="chart-card bar-card">
            <div class="card-header">
                <h3 class="card-title" id="barChartTitle">Top 10 by Views</h3>
                <button class="btn-export" id="exportBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export CSV
                </button>
            </div>
            <div id="barChart" style="height:280px;"></div>
        </div>

    </div>

    {{-- ── Tabs + Post List ── --}}
    <div class="posts-card">
        <div class="card-header">
            <div class="tab-group" id="mainTabs">
                <button class="tab-btn active" data-tab="views">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Most Viewed
                </button>
                <button class="tab-btn" data-tab="retweets">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                    Most Retweeted
                </button>
                <button class="tab-btn" data-tab="likes">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    Most Liked
                </button>
                <button class="tab-btn" data-tab="replies">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Most Replies
                </button>
            </div>
            <div class="search-wrap">
                <input type="text" id="postSearch" placeholder="Search posts..." class="search-input">
            </div>
        </div>

        {{-- Loading / Error / Empty states --}}
        <div id="loadingState" class="state-box">
            <div class="spinner"></div>
            <p>Loading posts...</p>
        </div>
        <div id="errorState" class="state-box hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p id="errorMsg">Failed to load data.</p>
            <button class="btn-retry" id="retryBtn">Retry</button>
        </div>
        <div id="emptyState" class="state-box hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3z"/><path d="M9 9h6v6H9z"/></svg>
            <p>No posts found for this period.</p>
        </div>

        {{-- Post List --}}
        <div id="postList" class="post-list hidden"></div>

        {{-- Pagination --}}
        <div id="pagination" class="pagination hidden">
            <button class="page-btn" id="prevPage">← Prev</button>
            <span id="pageInfo" class="page-info"></span>
            <button class="page-btn" id="nextPage">Next →</button>
        </div>
    </div>

</div>

{{-- ── Post Detail Popup ── --}}
<div id="postPopup" class="popup hidden">
    <div class="popup-header">
        <h4 class="popup-title">Post Detail</h4>
        <div class="popup-actions">
            <button class="popup-drag-handle" id="popupDragHandle" title="Drag">⠿</button>
            <button class="popup-close" id="popupClose">✕</button>
        </div>
    </div>
    <div class="popup-body" id="popupBody"></div>
</div>

<style>
/* ── Variables ── */
:root {
    --primary: #1d9bf0;
    --primary-dark: #0d8de0;
    --bg: #f8fafc;
    --card-bg: #ffffff;
    --border: #e2e8f0;
    --text: #0f172a;
    --text-muted: #64748b;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --purple: #8b5cf6;
    --radius: 12px;
    --shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
}

/* ── Layout ── */
.page-wrapper { padding: 24px; max-width: 1400px; margin: 0 auto; }

/* ── Header ── */
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px; }
.page-title-wrap { display:flex; align-items:center; gap:12px; }
.page-icon { width:44px; height:44px; background:linear-gradient(135deg,#1d9bf0,#0d8de0); border-radius:10px; display:flex; align-items:center; justify-content:center; }
.page-icon svg { width:22px; height:22px; stroke:#fff; }
.page-title { font-size:22px; font-weight:700; color:var(--text); margin:0; }
.page-subtitle { font-size:13px; color:var(--text-muted); margin:2px 0 0; }
.header-right { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }

/* ── Date Picker ── */
.date-picker-wrap { display:flex; align-items:center; gap:8px; background:var(--card-bg); border:1px solid var(--border); border-radius:8px; padding:6px 10px; }
.date-presets { display:flex; gap:4px; }
.preset-btn { font-size:11px; padding:3px 8px; border:1px solid var(--border); border-radius:5px; background:transparent; cursor:pointer; color:var(--text-muted); transition:all .15s; }
.preset-btn:hover,.preset-btn.active { background:var(--primary); border-color:var(--primary); color:#fff; }
.date-inputs { display:flex; align-items:center; gap:6px; }
.date-input { border:none; outline:none; font-size:13px; color:var(--text); background:transparent; }
.date-sep { color:var(--text-muted); }
.btn-apply { background:var(--primary); color:#fff; border:none; border-radius:6px; padding:5px 12px; font-size:13px; cursor:pointer; font-weight:500; }
.btn-apply:hover { background:var(--primary-dark); }
.project-select { border:1px solid var(--border); border-radius:8px; padding:7px 12px; font-size:13px; color:var(--text); background:var(--card-bg); cursor:pointer; }

/* ── Stat Cards ── */
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-bottom:24px; }
.stat-card { background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:20px; display:flex; align-items:center; gap:16px; box-shadow:var(--shadow); transition:transform .2s,box-shadow .2s; }
.stat-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
.stat-icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-icon svg { width:22px; height:22px; }
.views-icon   { background:#dbeafe; } .views-icon svg   { stroke:#1d9bf0; }
.rt-icon      { background:#d1fae5; } .rt-icon svg      { stroke:#10b981; }
.likes-icon   { background:#fee2e2; } .likes-icon svg   { stroke:#ef4444; }
.replies-icon { background:#ede9fe; } .replies-icon svg { stroke:#8b5cf6; }
.stat-label { font-size:12px; color:var(--text-muted); margin:0 0 4px; font-weight:500; text-transform:uppercase; letter-spacing:.5px; }
.stat-value { font-size:26px; font-weight:700; color:var(--text); margin:0 0 2px; }
.stat-meta  { font-size:12px; color:var(--text-muted); margin:0; }

/* ── Content Grid ── */
.content-grid { display:grid; grid-template-columns:1fr 1.6fr; gap:20px; margin-bottom:24px; }
@media(max-width:900px){.content-grid{grid-template-columns:1fr;}}

/* ── Chart Cards ── */
.chart-card { background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:20px; box-shadow:var(--shadow); }
.card-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:8px; }
.card-title { font-size:15px; font-weight:600; color:var(--text); margin:0; }
.chart-metric-tabs { display:flex; gap:4px; }
.metric-tab { font-size:12px; padding:4px 10px; border:1px solid var(--border); border-radius:6px; background:transparent; cursor:pointer; color:var(--text-muted); transition:all .15s; }
.metric-tab.active { background:var(--primary); border-color:var(--primary); color:#fff; }
.btn-export { display:flex; align-items:center; gap:5px; font-size:12px; padding:5px 10px; border:1px solid var(--border); border-radius:6px; background:transparent; cursor:pointer; color:var(--text-muted); transition:all .15s; }
.btn-export:hover { border-color:var(--primary); color:var(--primary); }
.btn-export svg { width:14px; height:14px; }

/* ── Posts Card ── */
.posts-card { background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.posts-card .card-header { padding:16px 20px; border-bottom:1px solid var(--border); }
.tab-group { display:flex; gap:4px; flex-wrap:wrap; }
.tab-btn { display:flex; align-items:center; gap:6px; font-size:13px; padding:7px 14px; border:1px solid var(--border); border-radius:8px; background:transparent; cursor:pointer; color:var(--text-muted); transition:all .15s; font-weight:500; }
.tab-btn svg { width:14px; height:14px; }
.tab-btn.active { background:var(--primary); border-color:var(--primary); color:#fff; }
.search-wrap { flex-shrink:0; }
.search-input { border:1px solid var(--border); border-radius:8px; padding:7px 12px; font-size:13px; outline:none; width:200px; }
.search-input:focus { border-color:var(--primary); }

/* ── State Boxes ── */
.state-box { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:60px 20px; gap:12px; color:var(--text-muted); }
.state-box svg { width:40px; height:40px; opacity:.4; }
.state-box p { margin:0; font-size:14px; }
.spinner { width:36px; height:36px; border:3px solid var(--border); border-top-color:var(--primary); border-radius:50%; animation:spin .7s linear infinite; }
@keyframes spin{to{transform:rotate(360deg)}}
.btn-retry { padding:7px 18px; background:var(--primary); color:#fff; border:none; border-radius:7px; cursor:pointer; font-size:13px; }
.hidden { display:none !important; }

/* ── Post List ── */
.post-list { padding:0; }
.post-item { display:flex; gap:14px; padding:16px 20px; border-bottom:1px solid var(--border); cursor:pointer; transition:background .15s; }
.post-item:hover { background:#f8fafc; }
.post-item:last-child { border-bottom:none; }
.post-rank { font-size:13px; font-weight:700; color:var(--text-muted); min-width:28px; padding-top:2px; }
.post-avatar { width:40px; height:40px; border-radius:50%; overflow:hidden; flex-shrink:0; background:#e2e8f0; display:flex; align-items:center; justify-content:center; }
.post-avatar img { width:100%; height:100%; object-fit:cover; }
.post-avatar-fallback { font-size:16px; font-weight:700; color:#94a3b8; }
.post-body { flex:1; min-width:0; }
.post-author { display:flex; align-items:center; gap:8px; margin-bottom:4px; }
.post-name { font-size:14px; font-weight:600; color:var(--text); }
.post-handle { font-size:13px; color:var(--text-muted); }
.post-date { font-size:12px; color:var(--text-muted); margin-left:auto; }
.post-content { font-size:14px; color:var(--text); line-height:1.5; margin-bottom:10px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.post-metrics { display:flex; gap:16px; flex-wrap:wrap; }
.metric-badge { display:flex; align-items:center; gap:5px; font-size:13px; font-weight:500; }
.metric-badge svg { width:14px; height:14px; }
.metric-badge.views   { color:#1d9bf0; }
.metric-badge.rt      { color:#10b981; }
.metric-badge.likes   { color:#ef4444; }
.metric-badge.replies { color:#8b5cf6; }
.metric-badge.primary { font-size:14px; font-weight:700; }
.sentiment-pill { font-size:11px; padding:2px 8px; border-radius:20px; font-weight:500; }
.sentiment-pill.positive { background:#d1fae5; color:#065f46; }
.sentiment-pill.negative { background:#fee2e2; color:#991b1b; }
.sentiment-pill.neutral  { background:#f1f5f9; color:#475569; }

/* ── Pagination ── */
.pagination { display:flex; justify-content:center; align-items:center; gap:12px; padding:16px; border-top:1px solid var(--border); }
.page-btn { padding:7px 16px; border:1px solid var(--border); border-radius:7px; background:transparent; cursor:pointer; font-size:13px; color:var(--text-muted); transition:all .15s; }
.page-btn:hover:not(:disabled) { border-color:var(--primary); color:var(--primary); }
.page-btn:disabled { opacity:.4; cursor:default; }
.page-info { font-size:13px; color:var(--text-muted); }

/* ── Popup ── */
.popup { position:fixed; top:80px; right:24px; width:400px; max-height:80vh; background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); box-shadow:0 20px 40px rgba(0,0,0,.15); z-index:1000; display:flex; flex-direction:column; overflow:hidden; }
.popup-header { display:flex; justify-content:space-between; align-items:center; padding:14px 16px; border-bottom:1px solid var(--border); background:#f8fafc; cursor:move; }
.popup-title { font-size:14px; font-weight:600; margin:0; }
.popup-actions { display:flex; gap:6px; }
.popup-drag-handle { background:none; border:none; cursor:move; font-size:16px; color:var(--text-muted); padding:0 4px; }
.popup-close { background:none; border:none; cursor:pointer; font-size:16px; color:var(--text-muted); padding:0 4px; line-height:1; }
.popup-close:hover { color:var(--danger); }
.popup-body { padding:16px; overflow-y:auto; flex:1; }
.popup-content-text { font-size:14px; line-height:1.6; color:var(--text); margin-bottom:16px; }
.popup-metrics { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-bottom:16px; }
.popup-metric { background:#f8fafc; border-radius:8px; padding:10px 12px; text-align:center; }
.popup-metric-val { font-size:20px; font-weight:700; color:var(--text); display:block; }
.popup-metric-lbl { font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; }
.popup-author-row { display:flex; align-items:center; gap:10px; padding:12px; background:#f8fafc; border-radius:8px; }
.popup-author-img { width:40px; height:40px; border-radius:50%; object-fit:cover; background:#e2e8f0; }
.popup-author-name { font-size:14px; font-weight:600; color:var(--text); }
.popup-author-handle { font-size:12px; color:var(--text-muted); }
.popup-link { display:inline-flex; align-items:center; gap:5px; margin-top:12px; font-size:13px; color:var(--primary); text-decoration:none; }
.popup-link:hover { text-decoration:underline; }
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
(function() {
'use strict';

// ── State ──────────────────────────────────────────────────────────
const state = {
    projectId : '{{ $projectId ?? "" }}',
    startDate : '{{ $startDate }}',
    endDate   : '{{ $endDate }}',
    activeTab : 'views',
    activeMetric: 'views',
    posts     : [],       // raw all posts
    filtered  : [],       // after search
    page      : 1,
    perPage   : 15,
    loading   : false,
};

// ── DOM refs ───────────────────────────────────────────────────────
const $ = id => document.getElementById(id);
const postList  = $('postList');
const loading   = $('loadingState');
const errorDiv  = $('errorState');
const emptyDiv  = $('emptyState');
const pagination= $('pagination');

// ── Charts ─────────────────────────────────────────────────────────
let donutChart, barChart;
function initCharts() {
    donutChart = echarts.init($('donutChart'));
    barChart   = echarts.init($('barChart'));
    window.addEventListener('resize', () => { donutChart.resize(); barChart.resize(); });
}

// ── Helpers ────────────────────────────────────────────────────────
function fmt(n) {
    n = parseInt(n) || 0;
    if (n >= 1_000_000) return (n/1_000_000).toFixed(1) + 'M';
    if (n >= 1_000)     return (n/1_000).toFixed(1) + 'K';
    return n.toLocaleString();
}

function metricOf(post, metric) {
    switch(metric) {
        case 'views'   : return parseInt(post.view_cnt ?? post.views ?? post.freq ?? 0);
        case 'retweets': return parseInt(post.rt ?? post.retweets ?? post.rt_count ?? 0);
        case 'likes'   : return parseInt(post.fav_count ?? post.likes ?? post.fav ?? 0);
        case 'replies' : return parseInt(post.reply_cnt ?? post.replies ?? post.reply_count ?? 0);
    }
    return 0;
}

function sentimentClass(s) {
    s = (s||'').toLowerCase();
    if (s.includes('pos')) return 'positive';
    if (s.includes('neg')) return 'negative';
    return 'neutral';
}

function sortedByMetric(metric) {
    return [...state.posts].sort((a,b) => metricOf(b, metric) - metricOf(a, metric));
}

function avatarHtml(post, size=40) {
    const url  = post.avatar_url || (post.author && post.author.image) || '';
    const name = post.author?.scr_name || post.name || '?';
    if (url) return `<img src="${url}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" style="width:${size}px;height:${size}px;border-radius:50%;object-fit:cover;">
                     <span class="post-avatar-fallback" style="display:none">${name[0].toUpperCase()}</span>`;
    return `<span class="post-avatar-fallback">${name[0].toUpperCase()}</span>`;
}

// ── Fetch ──────────────────────────────────────────────────────────
async function fetchData() {
    if (!state.projectId) { showEmpty(); return; }
    showLoading();

    try {
        const url = `/mk/api/x/most-engagement-data?project_id=${state.projectId}&start_date=${state.startDate}&end_date=${state.endDate}`;
        const res = await fetch(url);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();

        if (!json.success) throw new Error(json.error || 'API error');

        state.posts = Array.isArray(json.data) ? json.data : [];

        if (state.posts.length === 0) { showEmpty(); return; }

        updateStats();
        renderCharts(state.activeMetric);
        renderList();
        showList();

    } catch(e) {
        showError(e.message);
    }
}

// ── Stats ──────────────────────────────────────────────────────────
function updateStats() {
    const top = sortedByMetric('views')[0] || {};
    const sumViews   = state.posts.reduce((a,p) => a + metricOf(p,'views'),   0);
    const sumRt      = state.posts.reduce((a,p) => a + metricOf(p,'retweets'),0);
    const sumLikes   = state.posts.reduce((a,p) => a + metricOf(p,'likes'),   0);
    const sumReplies = state.posts.reduce((a,p) => a + metricOf(p,'replies'), 0);

    $('totalViews').textContent   = fmt(sumViews);
    $('totalRetweets').textContent= fmt(sumRt);
    $('totalLikes').textContent   = fmt(sumLikes);
    $('totalReplies').textContent = fmt(sumReplies);

    $('viewsMeta').textContent   = `Across ${state.posts.length} posts`;
    $('retweetsMeta').textContent= `Across ${state.posts.length} posts`;
    $('likesMeta').textContent   = `Across ${state.posts.length} posts`;
    $('repliesMeta').textContent = `Across ${state.posts.length} posts`;
}

// ── Charts ─────────────────────────────────────────────────────────
function renderCharts(metric) {
    const sorted = sortedByMetric(metric);
    const top5   = sorted.slice(0, 5);
    const top10  = sorted.slice(0, 10);

    const colors = ['#1d9bf0','#10b981','#f59e0b','#8b5cf6','#ef4444',
                    '#06b6d4','#84cc16','#f97316','#ec4899','#6366f1'];

    // Donut
    donutChart.setOption({
        tooltip: { trigger:'item', formatter:p => `${p.name}<br/>${fmt(p.value)} (${p.percent}%)` },
        legend: { orient:'vertical', right:10, top:'center', textStyle:{fontSize:12} },
        series:[{
            type:'pie', radius:['45%','70%'], center:['38%','50%'],
            data: top5.map((p,i) => ({
                value: metricOf(p, metric),
                name : (p.author?.scr_name || p.name || 'Post').substring(0,20),
                itemStyle: { color: colors[i] }
            })),
            label:{ show:false },
            emphasis:{ label:{ show:true, fontSize:13, fontWeight:'bold' } }
        }]
    }, true);

    // Bar
    const labels = top10.map(p => '@' + (p.author?.scr_name || p.name || '').substring(0,15));
    const values = top10.map(p => metricOf(p, metric));
    barChart.setOption({
        tooltip: { trigger:'axis' },
        grid: { left:100, right:20, top:20, bottom:40 },
        xAxis: { type:'value', axisLabel:{ formatter: v => fmt(v) } },
        yAxis: { type:'category', data: labels.reverse(), axisLabel:{ fontSize:11 } },
        series:[{
            type:'bar', data: values.reverse(),
            itemStyle:{ color: p => colors[p.dataIndex % colors.length], borderRadius:[0,4,4,0] },
            label:{ show:true, position:'right', formatter: p => fmt(p.value), fontSize:11 }
        }]
    }, true);

    const titles = { views:'Views', retweets:'Retweets', likes:'Likes', replies:'Replies' };
    $('barChartTitle').textContent = `Top 10 by ${titles[metric]}`;
}

// ── Post List ──────────────────────────────────────────────────────
function renderList() {
    const query   = ($('postSearch').value || '').toLowerCase();
    const sorted  = sortedByMetric(state.activeTab);
    state.filtered = query
        ? sorted.filter(p => (p.content||'').toLowerCase().includes(query) ||
                              (p.name||'').toLowerCase().includes(query) ||
                              ((p.author?.scr_name)||'').toLowerCase().includes(query))
        : sorted;

    const total = state.filtered.length;
    const pages = Math.ceil(total / state.perPage);
    if (state.page > pages) state.page = 1;

    const start = (state.page - 1) * state.perPage;
    const slice = state.filtered.slice(start, start + state.perPage);

    if (slice.length === 0) { showEmpty(); return; }

    const metricIcons = {
        views  : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`,
        retweets:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>`,
        likes  : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>`,
        replies: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`
    };

    postList.innerHTML = slice.map((post, i) => {
        const rank    = start + i + 1;
        const author  = post.author?.scr_name || post.name || 'unknown';
        const name    = post.author?.name || post.name || author;
        const content = (post.content || '').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        const date    = post.date_created ? new Date(post.date_created).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '';
        const sentCls = sentimentClass(post.sentiment_str);
        const primary = metricOf(post, state.activeTab);

        return `<div class="post-item" data-idx="${start+i}" onclick="window.openPopup(${start+i})">
            <div class="post-rank">#${rank}</div>
            <div class="post-avatar">${avatarHtml(post)}</div>
            <div class="post-body">
                <div class="post-author">
                    <span class="post-name">${name}</span>
                    <span class="post-handle">@${author}</span>
                    <span class="sentiment-pill ${sentCls}">${sentCls}</span>
                    <span class="post-date">${date}</span>
                </div>
                <p class="post-content">${content}</p>
                <div class="post-metrics">
                    <span class="metric-badge ${state.activeTab} primary">${metricIcons[state.activeTab]} ${fmt(primary)}</span>
                    <span class="metric-badge views">${metricIcons.views} ${fmt(metricOf(post,'views'))}</span>
                    <span class="metric-badge rt">${metricIcons.retweets} ${fmt(metricOf(post,'retweets'))}</span>
                    <span class="metric-badge likes">${metricIcons.likes} ${fmt(metricOf(post,'likes'))}</span>
                    <span class="metric-badge replies">${metricIcons.replies} ${fmt(metricOf(post,'replies'))}</span>
                </div>
            </div>
        </div>`;
    }).join('');

    // Pagination
    $('pageInfo').textContent = `Page ${state.page} of ${pages} (${total} posts)`;
    $('prevPage').disabled = state.page <= 1;
    $('nextPage').disabled = state.page >= pages;
    pagination.classList.remove('hidden');
}

// ── Popup ──────────────────────────────────────────────────────────
window.openPopup = function(idx) {
    const post = state.filtered[idx];
    if (!post) return;
    const author = post.author?.scr_name || post.name || 'unknown';
    const name   = post.author?.name || post.name || author;
    const flw    = post.author?.flw_cnt || 0;
    const popup  = $('postPopup');
    const body   = $('popupBody');

    body.innerHTML = `
        <div class="popup-author-row">
            <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;background:#e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                ${avatarHtml(post,40)}
            </div>
            <div>
                <div class="popup-author-name">${name}</div>
                <div class="popup-author-handle">@${author} · ${parseInt(flw).toLocaleString()} followers</div>
            </div>
        </div>
        <p class="popup-content-text" style="margin-top:12px;">${(post.content||'').replace(/</g,'&lt;')}</p>
        <div class="popup-metrics">
            <div class="popup-metric"><span class="popup-metric-val" style="color:#1d9bf0">${fmt(metricOf(post,'views'))}</span><span class="popup-metric-lbl">Views</span></div>
            <div class="popup-metric"><span class="popup-metric-val" style="color:#10b981">${fmt(metricOf(post,'retweets'))}</span><span class="popup-metric-lbl">Retweets</span></div>
            <div class="popup-metric"><span class="popup-metric-val" style="color:#ef4444">${fmt(metricOf(post,'likes'))}</span><span class="popup-metric-lbl">Likes</span></div>
            <div class="popup-metric"><span class="popup-metric-val" style="color:#8b5cf6">${fmt(metricOf(post,'replies'))}</span><span class="popup-metric-lbl">Replies</span></div>
        </div>
        <div>
            <span class="sentiment-pill ${sentimentClass(post.sentiment_str)}">${sentimentClass(post.sentiment_str)}</span>
            <span style="font-size:12px;color:var(--text-muted);margin-left:8px;">${post.date_created || ''}</span>
        </div>
        ${post.sub_id ? `<a class="popup-link" href="https://twitter.com/i/web/status/${post.sub_id}" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            View on X
        </a>` : ''}
    `;
    popup.classList.remove('hidden');
};

// ── Draggable Popup ────────────────────────────────────────────────
(function() {
    const popup  = $('postPopup');
    const handle = $('popupDragHandle');
    let dragging = false, ox = 0, oy = 0;
    handle.addEventListener('mousedown', e => {
        dragging = true;
        ox = e.clientX - popup.offsetLeft;
        oy = e.clientY - popup.offsetTop;
        e.preventDefault();
    });
    document.addEventListener('mousemove', e => {
        if (!dragging) return;
        popup.style.left = (e.clientX - ox) + 'px';
        popup.style.top  = (e.clientY - oy) + 'px';
        popup.style.right = 'auto';
    });
    document.addEventListener('mouseup', () => dragging = false);
    $('popupClose').addEventListener('click', () => popup.classList.add('hidden'));
})();

// ── State helpers ──────────────────────────────────────────────────
function showLoading() { loading.classList.remove('hidden'); postList.classList.add('hidden'); errorDiv.classList.add('hidden'); emptyDiv.classList.add('hidden'); pagination.classList.add('hidden'); }
function showError(msg){ loading.classList.add('hidden');    postList.classList.add('hidden'); errorDiv.classList.remove('hidden'); emptyDiv.classList.add('hidden'); $('errorMsg').textContent = 'Error: ' + msg; }
function showEmpty()   { loading.classList.add('hidden');    postList.classList.add('hidden'); errorDiv.classList.add('hidden'); emptyDiv.classList.remove('hidden'); pagination.classList.add('hidden'); }
function showList()    { loading.classList.add('hidden');    postList.classList.remove('hidden'); errorDiv.classList.add('hidden'); emptyDiv.classList.add('hidden'); }

// ── Event Listeners ────────────────────────────────────────────────
// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        state.activeTab = this.dataset.tab;
        state.page = 1;
        renderCharts(state.activeTab);
        renderList();
        showList();
    });
});

// Metric tabs (donut)
document.querySelectorAll('.metric-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.metric-tab').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        state.activeMetric = this.dataset.metric;
        renderCharts(state.activeMetric);
    });
});

// Date presets
document.querySelectorAll('.preset-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const days = parseInt(this.dataset.days);
        const end  = new Date();
        const start= new Date(); start.setDate(start.getDate() - days + 1);
        $('endDate').value   = end.toISOString().slice(0,10);
        $('startDate').value = start.toISOString().slice(0,10);
        document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});

// Apply date
$('applyDate').addEventListener('click', () => {
    state.startDate = $('startDate').value;
    state.endDate   = $('endDate').value;
    state.page = 1;
    fetchData();
});

// Project
$('projectSelect').addEventListener('change', function() {
    state.projectId = this.value;
    state.page = 1;
    fetchData();
    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('project_id', this.value);
    window.history.pushState({}, '', url);
});

// Search
let searchTimer;
$('postSearch').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { state.page = 1; renderList(); showList(); }, 300);
});

// Pagination
$('prevPage').addEventListener('click', () => { if(state.page > 1){ state.page--; renderList(); showList(); scrollToList(); } });
$('nextPage').addEventListener('click', () => { state.page++; renderList(); showList(); scrollToList(); });

function scrollToList() { postList.scrollIntoView({ behavior:'smooth', block:'start' }); }

// Export CSV
$('exportBtn').addEventListener('click', () => {
    const sorted = sortedByMetric(state.activeTab);
    const rows   = [['Rank','Author','Handle','Content','Views','Retweets','Likes','Replies','Sentiment','Date']];
    sorted.forEach((p,i) => {
        rows.push([
            i+1,
            p.author?.name || p.name || '',
            p.author?.scr_name || p.name || '',
            '"'+(p.content||'').replace(/"/g,'""')+'"',
            metricOf(p,'views'),
            metricOf(p,'retweets'),
            metricOf(p,'likes'),
            metricOf(p,'replies'),
            p.sentiment_str || '',
            p.date_created || ''
        ]);
    });
    const csv  = rows.map(r => r.join(',')).join('\n');
    const blob = new Blob([csv], {type:'text/csv'});
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = `most-engagement-${state.startDate}-${state.endDate}.csv`;
    a.click();
});

// Retry
$('retryBtn').addEventListener('click', fetchData);

// ── Init ───────────────────────────────────────────────────────────
initCharts();
fetchData();

})();
</script>
@endpush