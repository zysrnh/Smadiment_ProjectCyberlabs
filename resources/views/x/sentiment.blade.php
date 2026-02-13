@extends('layouts.app')

@section('title', 'X Analytics - Sentimen')

@section('styles')
<style>
  :root {
    --green:      #038047;
    --green-dark: #026738;
    --navy:       #273B4A;
    --white:      #FFFFFF;
    --surface:    #F1F5F8;
    --border:     #e2e8f0;
    --text-1:     #273B4A;
    --text-2:     #64748b;
    --text-3:     #94a3b8;
    --pos:        #038047;
    --neg:        #dc2626;
    --neu:        #94a3b8;
    --shadow-sm:  0 1px 3px rgba(0,0,0,.08);
    --shadow-md:  0 4px 16px rgba(0,0,0,.08);
    --radius:     12px;
    --radius-sm:  8px;
  }

  .x-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 28px 32px 0;
    flex-wrap: wrap;
    gap: 16px;
  }

  .x-header-left { display: flex; align-items: center; gap: 14px; }

  .x-icon {
    width: 44px; height: 44px;
    background: var(--navy);
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
  }

  .x-icon svg { width: 22px; height: 22px; fill: #fff; }

  .x-header-title h2 {
    font-size: 22px; font-weight: 700;
    color: var(--text-1); letter-spacing: -.4px; margin: 0;
  }

  .x-header-title p { font-size: 13px; color: var(--text-2); margin: 2px 0 0; }

  .filter-bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

  .filter-bar label {
    font-size: 12px; font-weight: 600;
    color: var(--text-2); text-transform: uppercase; letter-spacing: .6px;
  }

  .filter-bar input[type="date"] {
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: 'Poppins', sans-serif; font-size: 13px; color: var(--text-1);
    background: var(--white); outline: none; transition: border-color .2s;
  }

  .filter-bar input[type="date"]:focus { border-color: var(--green); }

  .btn-apply {
    padding: 8px 20px; background: var(--green); color: #fff;
    border: none; border-radius: var(--radius-sm);
    font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background .2s, transform .15s;
  }

  .btn-apply:hover { background: var(--green-dark); transform: translateY(-1px); }

  .x-body { padding: 24px 32px 40px; }

  /* ── Summary Metrics ── */
  .sentiment-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
  }

  .sent-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: box-shadow .2s, transform .2s;
  }

  .sent-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }

  .sent-indicator {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }

  .sent-indicator svg { width: 24px; height: 24px; }

  .sent-indicator.pos { background: rgba(3,128,71,.1); }
  .sent-indicator.neg { background: rgba(220,38,38,.1); }
  .sent-indicator.neu { background: rgba(148,163,184,.1); }

  .sent-info {}
  .sent-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: var(--text-3); margin-bottom: 4px; }
  .sent-value { font-size: 30px; font-weight: 800; letter-spacing: -1px; color: var(--text-1); line-height: 1; }
  .sent-pct   { font-size: 13px; color: var(--text-2); margin-top: 4px; }

  /* ── Charts Layout ── */
  .chart-row {
    display: grid;
    gap: 16px;
    margin-bottom: 16px;
  }

  .chart-row.cols-2 { grid-template-columns: 1fr 1fr; }
  .chart-row.cols-1 { grid-template-columns: 1fr; }

  .chart-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  .chart-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px 14px;
    border-bottom: 1px solid var(--border);
  }

  .chart-head h3 { font-size: 14px; font-weight: 700; color: var(--text-1); margin: 0; }
  .chart-head span { font-size: 11px; color: var(--text-3); font-weight: 500; }

  .chart-body {
    padding: 20px;
    min-height: 280px;
    position: relative;
  }

  /* ── Geo Sentiment Filter ── */
  .geo-filter {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
  }

  .geo-filter-btn {
    padding: 6px 14px;
    border: 1px solid var(--border);
    border-radius: 6px;
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    background: var(--white);
    color: var(--text-2);
    transition: all .2s;
  }

  .geo-filter-btn.active-pos { background: var(--pos); color: #fff; border-color: var(--pos); }
  .geo-filter-btn.active-neg { background: var(--neg); color: #fff; border-color: var(--neg); }
  .geo-filter-btn.active-neu { background: var(--neu); color: #fff; border-color: var(--neu); }
  .geo-filter-btn:hover:not(.active-pos):not(.active-neg):not(.active-neu) { border-color: var(--green); color: var(--green); }

  /* ── Table Mention ── */
  .mention-table { width: 100%; border-collapse: collapse; }
  .mention-table th {
    text-align: left; padding: 10px 14px;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; color: var(--text-3);
    border-bottom: 1px solid var(--border); background: var(--surface);
  }

  .mention-table td {
    padding: 12px 14px;
    font-size: 13px; color: var(--text-1);
    border-bottom: 1px solid var(--border);
    vertical-align: top;
  }

  .mention-table tr:last-child td { border-bottom: none; }
  .mention-table tr:hover td { background: var(--surface); }

  .badge-pos, .badge-neg, .badge-neu {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px; font-weight: 700;
  }

  .badge-pos { background: rgba(3,128,71,.12); color: var(--pos); }
  .badge-neg { background: rgba(220,38,38,.12); color: var(--neg); }
  .badge-neu { background: rgba(148,163,184,.15); color: var(--neu); }

  /* ── Skeleton ── */
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

  .skeleton-wrap { display: flex; flex-direction: column; gap: 10px; }
  .h8  { height: 8px; }
  .h12 { height: 12px; }
  .h32 { height: 32px; }
  .h200 { height: 200px; }
  .w80  { width: 80%; }
  .w60  { width: 60%; }

  .error-state {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    height: 160px; color: var(--text-3);
    font-size: 13px; gap: 8px;
  }

  .error-state svg { width: 28px; height: 28px; stroke: var(--text-3); }

  @media (max-width: 1024px) {
    .sentiment-summary { grid-template-columns: repeat(3, 1fr); }
    .chart-row.cols-2 { grid-template-columns: 1fr; }
  }

  @media (max-width: 640px) {
    .sentiment-summary { grid-template-columns: 1fr; }
    .x-header, .x-body { padding-left: 20px; padding-right: 20px; }
  }
</style>
@endsection

@section('content')

<div class="x-header">
  <div class="x-header-left">
    <div class="x-icon">
      <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
    </div>
    <div class="x-header-title">
      <h2>Analisis Sentimen X</h2>
      <p>Distribusi dan tren sentimen konten di platform X</p>
    </div>
  </div>

  <div class="filter-bar">
    <label>Dari</label>
    <input type="date" id="startDate" value="{{ $startDate }}">
    <label>Hingga</label>
    <input type="date" id="endDate" value="{{ $endDate }}">
    <button class="btn-apply" onclick="applyFilter()">Terapkan</button>
  </div>
</div>

<div class="x-body">

  {{-- Summary Cards --}}
  <div class="sentiment-summary" id="sentSummary">
    @foreach(['pos','neg','neu'] as $s)
    <div class="sent-card">
      <div class="skeleton-wrap" style="width:100%;">
        <div class="skeleton h32 w80"></div>
        <div class="skeleton h12 w60"></div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Row 1: Timeline + Donut --}}
  <div class="chart-row cols-2">

    <div class="chart-card">
      <div class="chart-head">
        <h3>Tren Sentimen per Hari</h3>
        <span id="timelineLabel">Memuat...</span>
      </div>
      <div class="chart-body">
        <div id="timelineLoading" class="skeleton-wrap">
          <div class="skeleton h200"></div>
        </div>
        <canvas id="timelineChart" style="display:none;"></canvas>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-head">
        <h3>Proporsi Sentimen</h3>
        <span id="donutLabel">Memuat...</span>
      </div>
      <div class="chart-body" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;">
        <div id="donutLoading" class="skeleton-wrap" style="width:100%;">
          <div class="skeleton h200"></div>
        </div>
        <canvas id="donutChart" style="display:none;max-width:200px;"></canvas>
        <div id="donutLegend" style="display:none;width:100%;max-width:220px;"></div>
      </div>
    </div>

  </div>

  {{-- Row 2: Geo Sentiment --}}
  <div class="chart-row cols-1">
    <div class="chart-card">
      <div class="chart-head">
        <h3>Sebaran Geografis Sentimen</h3>
        <div class="geo-filter">
          <button class="geo-filter-btn active-pos" onclick="switchGeoSentiment(1, this)">Positif</button>
          <button class="geo-filter-btn" onclick="switchGeoSentiment(-1, this)">Negatif</button>
          <button class="geo-filter-btn" onclick="switchGeoSentiment(0, this)">Netral</button>
        </div>
      </div>
      <div class="chart-body">
        <div id="geoLoading" class="skeleton-wrap">
          <div class="skeleton h200"></div>
        </div>
        <div id="geoList" style="display:none;"></div>
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
    sentiment  : '/mk/x/api/sentiment',
    timeline   : '/mk/x/api/sentiment-timeline',
    geoSentiment: '/mk/x/api/geo-sentiment',
  };

  const COLORS = { pos: '#038047', neg: '#dc2626', neu: '#94a3b8' };
  let timelineChart, donutChart;
  let currentGeoSentiment = 1;

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

  function showError(id, msg = 'Gagal memuat data') {
    const el = document.getElementById(id);
    if (!el) return;
    el.innerHTML = `
      <div class="error-state">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5">
          <circle cx="12" cy="12" r="9"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span>${msg}</span>
      </div>`;
    el.style.display = 'block';
  }

  // ── Summary + Donut ──────────────────────────────────────────
  async function loadSentiment() {
    try {
      const data  = await fetchJson(`${API.sentiment}?${BASE_PARAMS()}`);
      const pos   = data.positive ?? data.pos ?? 0;
      const neg   = data.negative ?? data.neg ?? 0;
      const neu   = data.neutral  ?? data.neu ?? 0;
      const total = pos + neg + neu || 1;

      // Summary cards
      const sentSummary = document.getElementById('sentSummary');
      sentSummary.innerHTML = [
        {
          cls: 'pos', label: 'Positif', value: pos,
          icon: `<svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="2"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/><path d="M8 13s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>`
        },
        {
          cls: 'neg', label: 'Negatif', value: neg,
          icon: `<svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>`
        },
        {
          cls: 'neu', label: 'Netral', value: neu,
          icon: `<svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>`
        },
      ].map(s => `
        <div class="sent-card">
          <div class="sent-indicator ${s.cls}">${s.icon}</div>
          <div class="sent-info">
            <div class="sent-label">${s.label}</div>
            <div class="sent-value">${fmt(s.value)}</div>
            <div class="sent-pct">${((s.value / total) * 100).toFixed(1)}% dari total</div>
          </div>
        </div>`).join('');

      // Donut
      document.getElementById('donutLoading').style.display = 'none';
      const canvas = document.getElementById('donutChart');
      canvas.style.display = 'block';
      document.getElementById('donutLabel').textContent = fmt(total) + ' total mention';

      if (donutChart) donutChart.destroy();
      donutChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
          labels  : ['Positif', 'Negatif', 'Netral'],
          datasets: [{ data: [pos, neg, neu], backgroundColor: [COLORS.pos, COLORS.neg, COLORS.neu], borderWidth: 0, hoverOffset: 6 }]
        },
        options: {
          cutout: '65%', responsive: true,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#273B4A',
              titleFont: { family: 'Poppins', size: 12 },
              bodyFont: { family: 'Poppins', size: 13 },
              callbacks: { label: ctx => ' ' + ((ctx.raw / total) * 100).toFixed(1) + '% (' + fmt(ctx.raw) + ')' }
            }
          }
        }
      });

      const legend = document.getElementById('donutLegend');
      legend.style.display = 'block';
      legend.innerHTML = [
        { label: 'Positif', v: pos, c: COLORS.pos },
        { label: 'Negatif', v: neg, c: COLORS.neg },
        { label: 'Netral',  v: neu, c: COLORS.neu },
      ].map(l => `
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
          <span style="width:10px;height:10px;border-radius:50%;background:${l.c};flex-shrink:0;"></span>
          <span style="font-size:12px;color:#273B4A;flex:1;">${l.label}</span>
          <span style="font-size:12px;font-weight:700;color:#273B4A;">${fmt(l.v)}</span>
        </div>`).join('');

    } catch (err) {
      showError('sentSummary', 'Gagal memuat data sentimen');
      console.error('loadSentiment:', err);
    }
  }

  // ── Timeline ─────────────────────────────────────────────────
  async function loadTimeline() {
    try {
      const data  = await fetchJson(`${API.timeline}?${BASE_PARAMS()}`);
      const items = data.data ?? data ?? [];

      document.getElementById('timelineLoading').style.display = 'none';
      const canvas = document.getElementById('timelineChart');
      canvas.style.display = 'block';

      const labels = items.map(d => d.date ?? d.label ?? '');
      const pos    = items.map(d => d.positive ?? d.pos ?? 0);
      const neg    = items.map(d => d.negative ?? d.neg ?? 0);
      const neu    = items.map(d => d.neutral  ?? d.neu ?? 0);

      document.getElementById('timelineLabel').textContent = items.length + ' hari';

      if (timelineChart) timelineChart.destroy();
      timelineChart = new Chart(canvas, {
        type: 'line',
        data: {
          labels,
          datasets: [
            {
              label: 'Positif', data: pos,
              borderColor: COLORS.pos, backgroundColor: 'rgba(3,128,71,.08)',
              tension: .4, fill: true, borderWidth: 2, pointRadius: 3
            },
            {
              label: 'Negatif', data: neg,
              borderColor: COLORS.neg, backgroundColor: 'rgba(220,38,38,.06)',
              tension: .4, fill: true, borderWidth: 2, pointRadius: 3
            },
            {
              label: 'Netral', data: neu,
              borderColor: COLORS.neu, backgroundColor: 'rgba(148,163,184,.06)',
              tension: .4, fill: true, borderWidth: 2, pointRadius: 3
            },
          ]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          interaction: { mode: 'index', intersect: false },
          plugins: {
            legend: {
              position: 'top',
              labels: { font: { family: 'Poppins', size: 11 }, color: '#273B4A', boxWidth: 10, padding: 16 }
            },
            tooltip: {
              backgroundColor: '#273B4A',
              titleFont: { family: 'Poppins', size: 12 },
              bodyFont: { family: 'Poppins', size: 12 },
            }
          },
          scales: {
            x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 11 }, color: '#94a3b8' } },
            y: { grid: { color: '#f1f5f8' }, ticks: { font: { family: 'Poppins', size: 11 }, color: '#94a3b8', callback: v => fmt(v) } }
          }
        }
      });

    } catch (err) {
      document.getElementById('timelineLoading').style.display = 'none';
      showError('timelineChart', 'Gagal memuat timeline');
      console.error('loadTimeline:', err);
    }
  }

  // ── Geo Sentiment ─────────────────────────────────────────────
  async function loadGeoSentiment(sentiment = 1) {
    const geoList = document.getElementById('geoList');
    const geoLoad = document.getElementById('geoLoading');

    geoLoad.style.display = 'flex';
    geoLoad.innerHTML     = '<div class="skeleton h200" style="width:100%;"></div>';
    geoList.style.display = 'none';

    try {
      const data  = await fetchJson(`${API.geoSentiment}?${BASE_PARAMS()}&sentiment=${sentiment}`);
      const items = (data.data ?? data ?? []).slice(0, 15);
      const max   = items[0]?.count ?? items[0]?.total ?? 1;

      const color = sentiment === 1 ? COLORS.pos : sentiment === -1 ? COLORS.neg : COLORS.neu;

      geoLoad.style.display = 'none';
      geoList.style.display = 'block';
      geoList.innerHTML     = `
        <div style="display:flex;flex-direction:column;gap:8px;">
          ${items.map((loc, i) => {
            const name  = loc.location ?? loc.name ?? loc.city ?? loc.province ?? 'Tidak diketahui';
            const count = loc.count ?? loc.total ?? 0;
            const pct   = ((count / max) * 100).toFixed(0);
            return `
              <div style="display:flex;align-items:center;gap:12px;">
                <span style="font-size:11px;font-weight:700;color:#94a3b8;width:20px;text-align:center;">${i + 1}</span>
                <span style="font-size:13px;color:#273B4A;width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${name}</span>
                <div style="flex:1;height:6px;background:#f1f5f8;border-radius:3px;overflow:hidden;">
                  <div style="height:100%;width:${pct}%;background:${color};border-radius:3px;transition:width .5s;"></div>
                </div>
                <span style="font-size:12px;font-weight:700;color:#273B4A;white-space:nowrap;">${fmt(count)}</span>
              </div>`;
          }).join('')}
        </div>`;

    } catch (err) {
      geoLoad.style.display = 'none';
      showError('geoList', 'Gagal memuat data geografis');
      console.error('loadGeoSentiment:', err);
    }
  }

  function switchGeoSentiment(sentiment, btn) {
    currentGeoSentiment = sentiment;

    document.querySelectorAll('.geo-filter-btn').forEach(b => {
      b.className = 'geo-filter-btn';
    });

    if (sentiment ===  1) btn.classList.add('active-pos');
    if (sentiment === -1) btn.classList.add('active-neg');
    if (sentiment ===  0) btn.classList.add('active-neu');

    loadGeoSentiment(sentiment);
  }

  function applyFilter() {
    if (!PROJECT_ID) { alert('Pilih project terlebih dahulu.'); return; }
    loadAll();
  }

  function loadAll() {
    loadSentiment();
    loadTimeline();
    loadGeoSentiment(currentGeoSentiment);
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (PROJECT_ID) loadAll();
  });
</script>
@endsection