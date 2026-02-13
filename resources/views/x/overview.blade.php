@extends('layouts.app')

@section('title', 'X Analytics - Overview')

@section('styles')
<style>
  :root {
    --green:       #038047;
    --green-dark:  #026738;
    --navy:        #273B4A;
    --white:       #FFFFFF;
    --surface:     #F1F5F8;
    --border:      #e2e8f0;
    --text-1:      #273B4A;
    --text-2:      #64748b;
    --text-3:      #94a3b8;
    --positive:    #038047;
    --negative:    #dc2626;
    --neutral:     #64748b;
    --shadow-sm:   0 1px 3px rgba(0,0,0,.08);
    --shadow-md:   0 4px 16px rgba(0,0,0,.08);
    --radius:      12px;
    --radius-sm:   8px;
  }

  /* ── Page header ── */
  .x-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 28px 32px 0;
    flex-wrap: wrap;
    gap: 16px;
  }

  .x-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .x-icon {
    width: 44px;
    height: 44px;
    background: var(--navy);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .x-icon svg {
    width: 22px;
    height: 22px;
    fill: #fff;
  }

  .x-header-title h2 {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-1);
    letter-spacing: -.4px;
    margin: 0;
  }

  .x-header-title p {
    font-size: 13px;
    color: var(--text-2);
    margin: 2px 0 0;
  }

  /* ── Date filter bar ── */
  .filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .filter-bar label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-2);
    text-transform: uppercase;
    letter-spacing: .6px;
  }

  .filter-bar input[type="date"] {
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    color: var(--text-1);
    background: var(--white);
    outline: none;
    transition: border-color .2s;
  }

  .filter-bar input[type="date"]:focus {
    border-color: var(--green);
  }

  .btn-apply {
    padding: 8px 20px;
    background: var(--green);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s, transform .15s;
  }

  .btn-apply:hover {
    background: var(--green-dark);
    transform: translateY(-1px);
  }

  /* ── Content wrapper ── */
  .x-body {
    padding: 24px 32px 40px;
  }

  /* ── Stat cards ── */
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 22px 20px;
    box-shadow: var(--shadow-sm);
    transition: box-shadow .2s, transform .2s;
    position: relative;
    overflow: hidden;
  }

  .stat-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--green);
    border-radius: var(--radius) var(--radius) 0 0;
  }

  .stat-card.navy::before { background: var(--navy); }
  .stat-card.positive::before { background: var(--positive); }
  .stat-card.negative::before { background: var(--negative); }
  .stat-card.neutral::before { background: var(--neutral); }

  .stat-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-3);
    text-transform: uppercase;
    letter-spacing: .8px;
    margin-bottom: 10px;
  }

  .stat-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--text-1);
    letter-spacing: -1px;
    line-height: 1;
  }

  .stat-sub {
    font-size: 12px;
    color: var(--text-2);
    margin-top: 6px;
  }

  /* ── Chart cards ── */
  .chart-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
  }

  .chart-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 16px;
  }

  .chart-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  .chart-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px 14px;
    border-bottom: 1px solid var(--border);
  }

  .chart-head h3 {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-1);
    margin: 0;
  }

  .chart-head span {
    font-size: 11px;
    color: var(--text-3);
    font-weight: 500;
  }

  .chart-body {
    padding: 20px;
    min-height: 260px;
    position: relative;
  }

  .chart-body.tall {
    min-height: 320px;
  }

  /* ── Loading skeleton ── */
  .skeleton-wrap {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 16px 0;
  }

  .skeleton {
    background: linear-gradient(90deg, #f1f5f8 25%, #e2e8f0 50%, #f1f5f8 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 6px;
  }

  @keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .skeleton.h8  { height: 8px; }
  .skeleton.h12 { height: 12px; }
  .skeleton.h32 { height: 32px; }
  .skeleton.h48 { height: 48px; }
  .skeleton.h200 { height: 200px; }
  .skeleton.w60  { width: 60%; }
  .skeleton.w40  { width: 40%; }
  .skeleton.w80  { width: 80%; }

  /* ── Error state ── */
  .error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 180px;
    color: var(--text-3);
    font-size: 13px;
    gap: 8px;
  }

  .error-state svg {
    width: 32px;
    height: 32px;
    stroke: var(--text-3);
  }

  /* ── Hashtag list ── */
  .hashtag-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .hashtag-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--surface);
  }

  .hashtag-item:last-child { border-bottom: none; }

  .hashtag-rank {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-3);
    width: 20px;
    text-align: center;
  }

  .hashtag-bar-wrap {
    flex: 1;
  }

  .hashtag-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--green);
    margin-bottom: 4px;
  }

  .hashtag-bar {
    height: 4px;
    background: var(--surface);
    border-radius: 2px;
    overflow: hidden;
  }

  .hashtag-bar-fill {
    height: 100%;
    background: var(--green);
    border-radius: 2px;
    transition: width .6s cubic-bezier(.4,0,.2,1);
  }

  .hashtag-count {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-2);
    white-space: nowrap;
  }

  /* ── Influencer list ── */
  .influencer-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .influencer-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--surface);
  }

  .influencer-item:last-child { border-bottom: none; }

  .influencer-avatar {
    width: 36px;
    height: 36px;
    background: var(--navy);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
  }

  .influencer-info { flex: 1; overflow: hidden; }

  .influencer-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-1);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .influencer-handle {
    font-size: 11px;
    color: var(--text-3);
  }

  .influencer-score {
    font-size: 12px;
    font-weight: 700;
    color: var(--green);
  }

  /* ── Responsive ── */
  @media (max-width: 1200px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
    .chart-grid { grid-template-columns: 1fr; }
    .chart-grid-3 { grid-template-columns: repeat(2, 1fr); }
  }

  @media (max-width: 768px) {
    .x-header, .x-body { padding-left: 20px; padding-right: 20px; }
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
    .chart-grid-3 { grid-template-columns: 1fr; }
    .filter-bar { gap: 8px; }
  }
</style>
@endsection

@section('content')

{{-- ── Page Header ── --}}
<div class="x-header">
  <div class="x-header-left">
    <div class="x-icon">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
      </svg>
    </div>
    <div class="x-header-title">
      <h2>X Analytics Overview</h2>
      <p>Ringkasan performa dan aktivitas di platform X</p>
    </div>
  </div>

  {{-- Date Filter --}}
  <div class="filter-bar">
    <label>Dari</label>
    <input type="date" id="startDate" value="{{ $startDate }}">
    <label>Hingga</label>
    <input type="date" id="endDate" value="{{ $endDate }}">
    <button class="btn-apply" onclick="applyFilter()">Terapkan</button>
  </div>
</div>

{{-- ── Body ── --}}
<div class="x-body">

  {{-- Stat Cards --}}
  <div class="stat-grid" id="statGrid">
    @for ($i = 0; $i < 4; $i++)
    <div class="stat-card">
      <div class="skeleton-wrap">
        <div class="skeleton h8 w40"></div>
        <div class="skeleton h32 w60"></div>
        <div class="skeleton h8 w80"></div>
      </div>
    </div>
    @endfor
  </div>

  {{-- Chart Row 1: Volume Timeline + Sentiment Donut --}}
  <div class="chart-grid" style="margin-bottom:16px;">

    <div class="chart-card">
      <div class="chart-head">
        <h3>Volume Mention per Hari</h3>
        <span id="volumeLabel">Memuat...</span>
      </div>
      <div class="chart-body tall">
        <div id="volumeLoading" class="skeleton-wrap">
          <div class="skeleton h200"></div>
        </div>
        <canvas id="volumeChart" style="display:none;"></canvas>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-head">
        <h3>Distribusi Sentimen</h3>
        <span id="sentimentLabel">Memuat...</span>
      </div>
      <div class="chart-body" style="display:flex;flex-direction:column;align-items:center;justify-content:center;">
        <div id="sentimentLoading" class="skeleton-wrap" style="width:100%;">
          <div class="skeleton h200"></div>
        </div>
        <canvas id="sentimentChart" style="display:none;max-width:220px;"></canvas>
        <div id="sentimentLegend" style="display:none;margin-top:16px;width:100%;"></div>
      </div>
    </div>

  </div>

  {{-- Chart Row 2: Hashtag + Influencer + Authors --}}
  <div class="chart-grid-3">

    <div class="chart-card">
      <div class="chart-head">
        <h3>Top Hashtag</h3>
        <span>X</span>
      </div>
      <div class="chart-body">
        <div id="hashtagLoading" class="skeleton-wrap">
          <div class="skeleton h12 w80"></div>
          <div class="skeleton h12 w60"></div>
          <div class="skeleton h12 w70"></div>
          <div class="skeleton h12 w50"></div>
          <div class="skeleton h12 w65"></div>
        </div>
        <ul class="hashtag-list" id="hashtagList" style="display:none;"></ul>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-head">
        <h3>Top Influencer</h3>
        <span>X</span>
      </div>
      <div class="chart-body">
        <div id="influencerLoading" class="skeleton-wrap">
          <div class="skeleton h48 w80"></div>
          <div class="skeleton h48 w80"></div>
          <div class="skeleton h48 w80"></div>
        </div>
        <ul class="influencer-list" id="influencerList" style="display:none;"></ul>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-head">
        <h3>Tipe Author</h3>
        <span>X</span>
      </div>
      <div class="chart-body" style="display:flex;align-items:center;justify-content:center;">
        <div id="authorTypeLoading" class="skeleton-wrap" style="width:100%;">
          <div class="skeleton h200"></div>
        </div>
        <canvas id="authorTypeChart" style="display:none;max-width:200px;"></canvas>
      </div>
    </div>

  </div>

</div>
@endsection

@section('scripts')
<script>
  const PROJECT_ID  = '{{ $currentProjectId }}';
  const BASE_PARAMS = () => {
    const s = document.getElementById('startDate').value;
    const e = document.getElementById('endDate').value;
    return `project_id=${PROJECT_ID}&start_date=${s}&end_date=${e}`;
  };

  const API = {
    overviewStats : '/mk/x/api/overview-stats',
    sentiment     : '/mk/x/api/sentiment',
    hashtags      : '/mk/x/api/hashtags',
    influencers   : '/mk/x/api/influencers',
    authorType    : '/mk/x/api/authors-type',
  };

  const COLORS = {
    green   : '#038047',
    navy    : '#273B4A',
    surface : '#F1F5F8',
    pos     : '#038047',
    neg     : '#dc2626',
    neu     : '#94a3b8',
  };

  let volumeChart, sentimentChart, authorTypeChart;

  // ── Helpers ──────────────────────────────────────────────────
  function fmt(n) {
    if (n === null || n === undefined) return '-';
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M';
    if (n >= 1_000)     return (n / 1_000).toFixed(1) + 'K';
    return Number(n).toLocaleString('id-ID');
  }

  async function fetchJson(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  }

  function showError(containerId, message = 'Gagal memuat data') {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = `
      <div class="error-state">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5">
          <circle cx="12" cy="12" r="9"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span>${message}</span>
      </div>`;
  }

  // ── Stat Cards ────────────────────────────────────────────────
  async function loadStats() {
    try {
      const data = await fetchJson(`${API.overviewStats}?${BASE_PARAMS()}`);
      const grid  = document.getElementById('statGrid');

      // Ambil nilai dari response
      const volData  = data.volume?.data     ?? [];
      const sentData = data.sentiment        ?? {};
      const authData = data.authors?.data    ?? [];
      const rtData   = data.retweets?.data   ?? [];

      const totalVol    = volData.reduce((s, d) => s + (d.count ?? d.total ?? 0), 0);
      const totalAuth   = authData.reduce((s, d) => s + (d.count ?? d.total ?? 0), 0);
      const totalRt     = rtData.length;
      const posCount    = sentData.positive ?? sentData.pos ?? 0;
      const negCount    = sentData.negative ?? sentData.neg ?? 0;
      const sentTotal   = (posCount + negCount + (sentData.neutral ?? sentData.neu ?? 0)) || 1;
      const posPct      = ((posCount / sentTotal) * 100).toFixed(1);

      const cards = [
        { label: 'Total Volume',    value: fmt(totalVol),  sub: 'Mention di X',          accent: '' },
        { label: 'Total Author',    value: fmt(totalAuth), sub: 'Akun unik berkontribusi', accent: 'navy' },
        { label: 'Sentimen Positif', value: posPct + '%',  sub: `${fmt(posCount)} dari ${fmt(sentTotal)}`, accent: 'positive' },
        { label: 'Top Retweet',     value: fmt(totalRt),   sub: 'Tweet teridentifikasi',  accent: '' },
      ];

      grid.innerHTML = cards.map(c => `
        <div class="stat-card ${c.accent}">
          <div class="stat-label">${c.label}</div>
          <div class="stat-value">${c.value}</div>
          <div class="stat-sub">${c.sub}</div>
        </div>`).join('');

      // Gunakan data volume untuk timeline chart
      renderVolumeChart(volData);

    } catch (err) {
      document.getElementById('statGrid').innerHTML =
        Array(4).fill(`<div class="stat-card"><div class="stat-value" style="font-size:14px;color:#94a3b8">-</div></div>`).join('');
      console.error('loadStats:', err);
    }
  }

  // ── Volume Chart ──────────────────────────────────────────────
  function renderVolumeChart(rawData) {
    document.getElementById('volumeLoading').style.display = 'none';
    const canvas = document.getElementById('volumeChart');
    canvas.style.display = 'block';

    const labels = rawData.map(d => d.date ?? d.label ?? '');
    const values = rawData.map(d => d.count ?? d.total ?? 0);
    const total  = values.reduce((a, b) => a + b, 0);
    document.getElementById('volumeLabel').textContent = fmt(total) + ' total';

    if (volumeChart) volumeChart.destroy();
    volumeChart = new Chart(canvas, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label      : 'Volume',
          data       : values,
          backgroundColor: 'rgba(3,128,71,.15)',
          borderColor    : '#038047',
          borderWidth    : 2,
          borderRadius   : 4,
          borderSkipped  : false,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#273B4A',
            titleFont : { family: 'Poppins', size: 12 },
            bodyFont  : { family: 'Poppins', size: 13 },
            callbacks : { label: ctx => ' ' + fmt(ctx.raw) + ' mention' }
          }
        },
        scales: {
          x: {
            grid  : { display: false },
            ticks : { font: { family: 'Poppins', size: 11 }, color: '#94a3b8' }
          },
          y: {
            grid  : { color: '#f1f5f8' },
            ticks : { font: { family: 'Poppins', size: 11 }, color: '#94a3b8', callback: v => fmt(v) }
          }
        }
      }
    });
  }

  // ── Sentiment Chart ───────────────────────────────────────────
  async function loadSentiment() {
    try {
      const data = await fetchJson(`${API.sentiment}?${BASE_PARAMS()}`);

      const pos  = data.positive ?? data.pos ?? 0;
      const neg  = data.negative ?? data.neg ?? 0;
      const neu  = data.neutral  ?? data.neu ?? 0;
      const total = pos + neg + neu || 1;

      document.getElementById('sentimentLoading').style.display = 'none';
      const canvas = document.getElementById('sentimentChart');
      canvas.style.display = 'block';
      document.getElementById('sentimentLabel').textContent = fmt(total) + ' total';

      if (sentimentChart) sentimentChart.destroy();
      sentimentChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
          labels  : ['Positif', 'Negatif', 'Netral'],
          datasets: [{
            data           : [pos, neg, neu],
            backgroundColor: [COLORS.pos, COLORS.neg, COLORS.neu],
            borderWidth    : 0,
            hoverOffset    : 6,
          }]
        },
        options: {
          cutout    : '68%',
          responsive: true,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#273B4A',
              titleFont : { family: 'Poppins', size: 12 },
              bodyFont  : { family: 'Poppins', size: 13 },
              callbacks : { label: ctx => ' ' + ((ctx.raw / total) * 100).toFixed(1) + '% (' + fmt(ctx.raw) + ')' }
            }
          }
        }
      });

      const legend = document.getElementById('sentimentLegend');
      legend.style.display = 'block';
      legend.innerHTML = [
        { label: 'Positif', count: pos, color: COLORS.pos },
        { label: 'Negatif', count: neg, color: COLORS.neg },
        { label: 'Netral',  count: neu, color: COLORS.neu },
      ].map(l => `
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
          <span style="width:10px;height:10px;border-radius:50%;background:${l.color};flex-shrink:0;"></span>
          <span style="font-size:12px;color:#273B4A;flex:1;">${l.label}</span>
          <span style="font-size:12px;font-weight:700;color:#273B4A;">${fmt(l.count)}</span>
        </div>`).join('');

    } catch (err) {
      document.getElementById('sentimentLoading').style.display = 'none';
      showError('sentimentLoading', 'Gagal memuat sentimen');
      console.error('loadSentiment:', err);
    }
  }

  // ── Hashtag List ─────────────────────────────────────────────
  async function loadHashtags() {
    try {
      const data  = await fetchJson(`${API.hashtags}?${BASE_PARAMS()}`);
      const items = (data.data ?? data ?? []).slice(0, 8);
      const max   = items[0]?.count ?? items[0]?.freq ?? 1;

      document.getElementById('hashtagLoading').style.display = 'none';
      const list = document.getElementById('hashtagList');
      list.style.display = 'block';

      list.innerHTML = items.map((h, i) => {
        const tag   = h.hashtag ?? h.tag ?? h.name ?? h.keyword ?? '#unknown';
        const count = h.count ?? h.freq ?? 0;
        const pct   = ((count / max) * 100).toFixed(0);
        return `
          <li class="hashtag-item">
            <span class="hashtag-rank">${i + 1}</span>
            <div class="hashtag-bar-wrap">
              <div class="hashtag-name">#${tag.replace(/^#/, '')}</div>
              <div class="hashtag-bar">
                <div class="hashtag-bar-fill" style="width:${pct}%"></div>
              </div>
            </div>
            <span class="hashtag-count">${fmt(count)}</span>
          </li>`;
      }).join('');

    } catch (err) {
      document.getElementById('hashtagLoading').style.display = 'none';
      showError('hashtagList', 'Gagal memuat hashtag');
      document.getElementById('hashtagList').style.display = 'block';
      console.error('loadHashtags:', err);
    }
  }

  // ── Influencer List ───────────────────────────────────────────
  async function loadInfluencers() {
    try {
      const data  = await fetchJson(`${API.influencers}?${BASE_PARAMS()}`);
      const items = (data.data ?? data ?? []).slice(0, 7);

      document.getElementById('influencerLoading').style.display = 'none';
      const list = document.getElementById('influencerList');
      list.style.display = 'block';

      list.innerHTML = items.map(u => {
        const name   = u.name ?? u.username ?? u.screen_name ?? 'User';
        const handle = u.screen_name ?? u.username ?? '';
        const score  = u.score ?? u.influence ?? u.followers ?? 0;
        const initials = name.slice(0, 2).toUpperCase();
        return `
          <li class="influencer-item">
            <div class="influencer-avatar">${initials}</div>
            <div class="influencer-info">
              <div class="influencer-name">${name}</div>
              <div class="influencer-handle">@${handle}</div>
            </div>
            <span class="influencer-score">${fmt(score)}</span>
          </li>`;
      }).join('');

    } catch (err) {
      document.getElementById('influencerLoading').style.display = 'none';
      showError('influencerList', 'Gagal memuat influencer');
      document.getElementById('influencerList').style.display = 'block';
      console.error('loadInfluencers:', err);
    }
  }

  // ── Author Type Chart ─────────────────────────────────────────
  async function loadAuthorType() {
    try {
      const data  = await fetchJson(`${API.authorType}?${BASE_PARAMS()}`);
      const items = data.data ?? data ?? [];

      document.getElementById('authorTypeLoading').style.display = 'none';
      const canvas = document.getElementById('authorTypeChart');
      canvas.style.display = 'block';

      if (authorTypeChart) authorTypeChart.destroy();
      authorTypeChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
          labels  : items.map(d => d.type ?? d.label ?? d.name ?? 'Lainnya'),
          datasets: [{
            data           : items.map(d => d.count ?? d.total ?? 0),
            backgroundColor: ['#038047', '#273B4A', '#94a3b8', '#2FC6F6'],
            borderWidth    : 0,
            hoverOffset    : 4,
          }]
        },
        options: {
          cutout    : '60%',
          responsive: true,
          plugins: {
            legend: {
              position : 'bottom',
              labels   : { font: { family: 'Poppins', size: 11 }, color: '#273B4A', boxWidth: 10 }
            }
          }
        }
      });

    } catch (err) {
      document.getElementById('authorTypeLoading').style.display = 'none';
      showError('authorTypeLoading', 'Gagal memuat tipe author');
      console.error('loadAuthorType:', err);
    }
  }

  // ── Filter Apply ─────────────────────────────────────────────
  function applyFilter() {
    if (!PROJECT_ID) {
      alert('Pilih project terlebih dahulu.');
      return;
    }
    loadAll();
  }

  // ── Init ─────────────────────────────────────────────────────
  function loadAll() {
    loadStats();
    loadSentiment();
    loadHashtags();
    loadInfluencers();
    loadAuthorType();
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (PROJECT_ID) loadAll();
  });
</script>
@endsection