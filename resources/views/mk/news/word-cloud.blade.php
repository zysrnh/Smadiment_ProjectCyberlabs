@extends('mk.layouts.app')

@section('title', 'News Word Cloud - SMADIMENT')

@section('styles')
<style>
    :root {
        --primary-green: #038047;
        --primary-green-dark: #026738;
        --accent-blue: #2FC6F6;
        --text-primary: #1a202c;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --bg-white: #ffffff;
        --bg-gray-50: #f8fafc;
        --bg-gray-100: #f1f5f9;
        --border-gray: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    body { background: var(--bg-gray-50); }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-green);
    }
    .stat-card:hover::before { opacity: 1; }
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    .stat-icon-wrapper {
        width: 56px; height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(3,128,71,.1) 0%, rgba(3,128,71,.05) 100%);
        display: flex; align-items: center; justify-content: center;
    }
    .stat-icon {
        width: 28px; height: 28px;
        color: var(--primary-green);
        stroke: currentColor; fill: none; stroke-width: 2;
    }
    .stat-label {
        font-size: 13px; font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase; letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .stat-value-wrapper {
        display: flex; align-items: baseline; gap: 12px; margin-bottom: 16px;
    }
    .stat-value {
        font-size: 36px; font-weight: 700;
        color: var(--text-primary); line-height: 1;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .stat-progress {
        height: 6px; background: var(--bg-gray-100);
        border-radius: 10px; overflow: hidden; margin-top: 8px;
    }
    .stat-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        border-radius: 10px; transition: width 1s ease-out; width: 0%;
    }

    /* Filter Card (sentiment) */
    .filter-card {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-gray);
    }
    .filter-content {
        display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    }
    .filter-label {
        font-size: 14px; font-weight: 600;
        color: var(--text-primary); white-space: nowrap;
    }

    /* Sentiment */
    .sentiment-filters {
        display: flex; gap: 8px; flex-wrap: wrap;
    }
    .sentiment-btn {
        padding: 10px 20px;
        background: var(--bg-white);
        border: 2px solid var(--border-gray);
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px; font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer; transition: all 0.3s;
        display: flex; align-items: center; gap: 8px;
    }
    .sentiment-btn:hover {
        border-color: var(--primary-green);
        color: var(--text-primary);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .sentiment-btn.active {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        border-color: var(--primary-green);
        color: white;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }
    .sentiment-dot {
        width: 10px; height: 10px; border-radius: 50%;
        display: inline-block; flex-shrink: 0;
    }

    /* Word Cloud Container */
    .wordcloud-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        border: 1px solid var(--border-gray);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        padding: 48px;
        min-height: 700px;
        display: flex; align-items: center; justify-content: center;
        position: relative;
    }
    .wordcloud-container::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(3,128,71,.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(47,198,246,.03) 0%, transparent 50%);
        pointer-events: none;
    }
    .wordcloud-hint {
        position: absolute; bottom: 16px; right: 20px;
        font-size: 11px; color: var(--text-muted); font-style: italic;
        display: none; align-items: center; gap: 5px; z-index: 2; pointer-events: none;
    }
    .wordcloud-hint svg {
        width: 13px; height: 13px; stroke: currentColor; fill: none; flex-shrink: 0;
    }
    #wordCloudChart {
        width: 100% !important; height: 700px !important;
        position: relative; z-index: 1; cursor: pointer;
    }

    /* Loading / Empty */
    .loading-state {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; gap: 16px; padding: 80px 20px;
    }
    .loading-spinner {
        width: 48px; height: 48px;
        border: 4px solid var(--bg-gray-100);
        border-top-color: var(--primary-green);
        border-radius: 50%; animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .loading-text { font-size: 14px; color: var(--text-secondary); font-weight: 500; }
    .empty-state { text-align: center; padding: 80px 20px; }
    .empty-state svg {
        width: 64px; height: 64px; color: var(--text-secondary);
        margin-bottom: 16px; stroke: currentColor; fill: none;
    }
    .empty-state h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
    .empty-state p { font-size: 14px; color: var(--text-secondary); margin: 0; }

    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
    }
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .sentiment-filters { width: 100%; }
        .sentiment-btn { flex: 1; justify-content: center; }
        #wordCloudChart { height: 500px !important; }
        .wordcloud-container { padding: 24px; min-height: 550px; }
        .stat-value { font-size: 28px; }
    }
</style>
@endsection

@section('page-title', 'News Word Cloud')

@section('content')
@php
    $projectId = $projectId ?? request()->get('project_id');
    $startDate = $startDate ?? request()->get('start_date', now()->subDays(29)->format('Y-m-d'));
    $endDate   = $endDate ?? request()->get('end_date', now()->format('Y-m-d'));
    $projects  = $projects ?? [];
@endphp

{{-- Filter --}}
@include('mk.layouts.partials.filter-datepicker')

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-wrapper">
                <svg class="stat-icon" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
            </div>
        </div>
        <div class="stat-label">Total Words</div>
        <div id="totalWords" class="stat-value-wrapper"><div class="stat-value">-</div></div>
        <div class="stat-progress"><div class="stat-progress-bar"></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-wrapper">
                <svg class="stat-icon" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
        </div>
        <div class="stat-label">Top Word</div>
        <div id="topWord" class="stat-value-wrapper"><div class="stat-value" style="font-size:20px;">-</div></div>
        <div class="stat-progress"><div class="stat-progress-bar"></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-wrapper">
                <svg class="stat-icon" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
        </div>
        <div class="stat-label">Total Mentions</div>
        <div id="totalMentions" class="stat-value-wrapper"><div class="stat-value">-</div></div>
        <div class="stat-progress"><div class="stat-progress-bar"></div></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon-wrapper">
                <svg class="stat-icon" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
        </div>
        <div class="stat-label">Avg per Word</div>
        <div id="avgMentions" class="stat-value-wrapper"><div class="stat-value">-</div></div>
        <div class="stat-progress"><div class="stat-progress-bar"></div></div>
    </div>
</div>

<!-- Sentiment Filter Card -->
<div class="filter-card">
    <div class="filter-content">
        <div class="filter-label">
            <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
                <circle cx="12" cy="12" r="10"/>
                <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                <line x1="9"  y1="9"  x2="9.01"  y2="9"/>
                <line x1="15" y1="9"  x2="15.01" y2="9"/>
            </svg>
            Sentiment Filter
        </div>
        <div class="sentiment-filters">
            <button type="button" class="sentiment-btn active" data-sentiment="2">
                <span class="sentiment-dot" style="background: linear-gradient(135deg, #038047, #2FC6F6);"></span>
                All
            </button>
            <button type="button" class="sentiment-btn" data-sentiment="0">
                <span class="sentiment-dot" style="background: #10b981;"></span>
                Positive
            </button>
            <button type="button" class="sentiment-btn" data-sentiment="1">
                <span class="sentiment-dot" style="background: #f59e0b;"></span>
                Neutral
            </button>
            <button type="button" class="sentiment-btn" data-sentiment="-1">
                <span class="sentiment-dot" style="background: #ef4444;"></span>
                Negative
            </button>
        </div>
    </div>
</div>

<!-- Word Cloud Container -->
<div class="wordcloud-container">
    <div id="loadingState" class="loading-state">
        <div class="loading-spinner"></div>
        <div class="loading-text" id="loadingText">Loading news word cloud data...</div>
        <div style="font-size: 12px; color: var(--text-muted); margin-top: 8px;" id="loadingProgress"></div>
    </div>
    <div id="wordCloudChart" style="display: none;"></div>
    <div class="wordcloud-hint" id="wordCloudHint">
        <svg viewBox="0 0 24 24">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            <polyline points="15 3 21 3 21 9"/>
            <line x1="10" y1="14" x2="21" y2="3"/>
        </svg>
        Click a word to search on Google News
    </div>
    <div id="emptyState" class="empty-state" style="display: none;">
        <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8"  x2="12"    y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <h3>No News Data Found</h3>
        <p>No news mentions available for the selected date range.</p>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts-wordcloud@2.1.0/dist/echarts-wordcloud.min.js"></script>

<script>
const NewsWordCloudGenerator = {
    projectId: '{{ $projectId ?? "" }}',
    startDate: '{{ $startDate ?? "" }}',
    endDate:   '{{ $endDate ?? "" }}',
    chart:     null,

    colors: [
        '#e74c3c', '#e67e22', '#f1c40f', '#2ecc71', '#1abc9c',
        '#3498db', '#9b59b6', '#e91e63', '#ff5722', '#009688',
        '#673ab7', '#2196f3', '#4caf50', '#ff9800', '#f44336',
        '#00bcd4', '#8bc34a', '#ffc107', '#03a9f4', '#cddc39',
        '#ff4081', '#7c4dff', '#00e5ff', '#76ff03', '#ffea00',
    ],

    currentSentiment: '2',

    async init() {
        if (typeof echarts === 'undefined') {
            this.showError('ECharts library failed to load');
            return;
        }
        document.querySelectorAll('.sentiment-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                document.querySelectorAll('.sentiment-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.currentSentiment = btn.dataset.sentiment;
                this.loadData();
            });
        });
        try {
            await this.loadData();
        } catch (error) {
            console.error('Word cloud error:', error);
            this.showError('Failed to load data');
        }
    },

    async loadData() {
        const loadingState    = document.getElementById('loadingState');
        const chartDiv        = document.getElementById('wordCloudChart');
        const emptyState      = document.getElementById('emptyState');
        const hintEl          = document.getElementById('wordCloudHint');
        const loadingText     = document.getElementById('loadingText');
        const loadingProgress = document.getElementById('loadingProgress');

        loadingState.style.display  = 'flex';
        chartDiv.style.display      = 'none';
        emptyState.style.display    = 'none';
        hintEl.style.display        = 'none';
        loadingText.textContent     = 'Fetching news data...';
        loadingProgress.textContent = '';

        if (!this.projectId) {
            this.showError('No project selected. Please select a project first.');
            return;
        }

        const url = `/mk/api/news/word-cloud?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}&sentiment=${this.currentSentiment}`;
        const t0  = Date.now();

        const response = await fetch(url);
        loadingText.textContent = 'Building word cloud...';

        const result = await response.json();
        if (!result.success) throw new Error(result.error || 'API error');

        const phrases   = result.data?.data?.phrases || {};
        const loadTime  = ((Date.now() - t0) / 1000).toFixed(1);
        loadingProgress.textContent = `${Object.keys(phrases).length} words loaded in ${loadTime}s`;

        await new Promise(r => setTimeout(r, 150));
        this.generateWordCloud(phrases);
    },

    generateWordCloud(phrases) {
        const loadingState = document.getElementById('loadingState');
        const chartDiv     = document.getElementById('wordCloudChart');
        const emptyState   = document.getElementById('emptyState');
        const hintEl       = document.getElementById('wordCloudHint');

        if (!phrases || Object.keys(phrases).length === 0) {
            loadingState.style.display = 'none';
            emptyState.style.display   = 'block';
            this.updateStats({ totalWords: 0, topWord: '-', totalMentions: 0, avgMentions: 0 });
            return;
        }

        const entries  = Object.entries(phrases).sort((a, b) => b[1] - a[1]);
        const maxCount = entries[0][1];
        const minCount = entries[entries.length - 1][1];

        const wordData = entries.slice(0, 150).map(([word, count]) => {
            const norm   = (count - minCount) / (maxCount - minCount || 1);
            const scaled = Math.log1p(norm * 9) / Math.log1p(9) * 65 + 13;
            return { name: word, value: scaled, originalCount: count };
        });

        const totalWords    = entries.length;
        const topWord       = entries[0][0];
        const totalMentions = entries.reduce((s, [, c]) => s + c, 0);
        const avgMentions   = Math.round(totalMentions / totalWords);
        this.updateStats({ totalWords, topWord, totalMentions, avgMentions });

        loadingState.style.display = 'none';
        chartDiv.style.display     = 'block';
        hintEl.style.display       = 'flex';

        if (this.chart) this.chart.dispose();
        this.chart = echarts.init(chartDiv, null, {
            renderer: 'canvas',
            devicePixelRatio: window.devicePixelRatio || 1,
        });

        const sentimentColors = {
            '2':  this.colors,
            '0':  ['#10b981','#059669','#34d399','#6ee7b7','#047857','#0d9488','#14b8a6','#5eead4','#2dd4bf','#86efac','#22c55e','#16a34a','#15803d','#166534','#4ade80'],
            '1':  ['#f59e0b','#d97706','#fbbf24','#b45309','#fcd34d','#f97316','#ea580c','#fb923c','#fdba74','#fed7aa','#fde68a','#fef08a','#fef9c3','#fde047','#eab308'],
            '-1': ['#ef4444','#dc2626','#b91c1c','#f87171','#fca5a5','#f43f5e','#e11d48','#fb7185','#be123c','#9f1239','#ff6b6b','#ee5a24','#e55039','#c0392b','#922b21'],
        };
        const colors = sentimentColors[this.currentSentiment] || sentimentColors['2'];
        let colorIdx = 0;

        const option = {
            tooltip: {
                show: true,
                trigger: 'item',
                backgroundColor: '#fff',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                padding: 14,
                shadowBlur: 16,
                shadowColor: 'rgba(0,0,0,0.12)',
                textStyle: { fontFamily: 'Poppins, sans-serif', color: '#1a202c', fontSize: 13 },
                formatter: p => `
                    <div style="font-family:Poppins,sans-serif;min-width:160px;text-align:center;">
                        <div style="font-weight:700;font-size:15px;margin-bottom:8px;">${p.name}</div>
                        <div style="background:#f1f5f9;border-radius:10px;padding:6px 12px;margin-bottom:8px;">
                            <span style="font-weight:700;color:#038047;">${p.data.originalCount} mentions</span>
                        </div>
                        <div style="font-size:11px;color:#94a3b8;">Click to search Google News</div>
                    </div>`,
            },
            series: [{
                type: 'wordCloud',
                shape: 'circle',
                left: 'center',
                top: 'center',
                width: '96%',
                height: '96%',
                sizeRange: [13, 78],
                rotationRange: [-75, 75],
                rotationStep: 15,
                gridSize: 3,
                drawOutOfBound: false,
                layoutAnimation: true,
                textStyle: {
                    fontFamily: 'Poppins, Arial, sans-serif',
                    fontWeight: 'bold',
                    color: () => colors[colorIdx++ % colors.length],
                },
                emphasis: {
                    focus: 'self',
                    textStyle: {
                        textShadowBlur: 8,
                        textShadowColor: 'rgba(0,0,0,0.25)',
                    },
                },
                data: wordData,
            }],
        };

        setTimeout(() => {
            this.chart.setOption(option, true);
            this.chart.on('click', p => {
                if (p?.name) {
                    window.open(`https://news.google.com/search?q=${encodeURIComponent(p.name)}`, '_blank', 'noopener,noreferrer');
                }
            });
            this.chart.on('mouseover', () => { chartDiv.style.cursor = 'pointer'; });
            this.chart.on('mouseout',  () => { chartDiv.style.cursor = 'default'; });
        }, 10);

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => this.chart?.resize(), 250);
        });
    },

    updateStats({ totalWords, topWord, totalMentions, avgMentions }) {
        document.getElementById('totalWords').innerHTML    = `<div class="stat-value">${totalWords.toLocaleString()}</div>`;
        document.getElementById('topWord').innerHTML       = `<div class="stat-value" style="font-size:20px;" title="${topWord}">${topWord}</div>`;
        document.getElementById('totalMentions').innerHTML = `<div class="stat-value">${totalMentions.toLocaleString()}</div>`;
        document.getElementById('avgMentions').innerHTML   = `<div class="stat-value">${avgMentions.toLocaleString()}</div>`;

        const pcts = [80, 100, 95, 85];
        document.querySelectorAll('.stat-card').forEach((card, i) => {
            const bar = card.querySelector('.stat-progress-bar');
            if (bar) setTimeout(() => bar.style.width = pcts[i] + '%', 100);
        });
    },

    showError(msg = 'Failed to load data') {
        document.getElementById('loadingState').style.display = 'none';
        const el = document.getElementById('emptyState');
        el.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" style="color:#ef4444;width:64px;height:64px;stroke:currentColor;fill:none;margin-bottom:16px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <h3>Failed to Load Data</h3>
                <p>${msg}. Please try again later.</p>
            </div>`;
        el.style.display = 'block';
        this.updateStats({ totalWords: 0, topWord: '-', totalMentions: 0, avgMentions: 0 });
    },
};

document.addEventListener('DOMContentLoaded', () => NewsWordCloudGenerator.init());
</script>
@endsection
