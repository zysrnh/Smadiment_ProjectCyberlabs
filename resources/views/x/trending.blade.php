    @extends('layouts.app')

@section('title', 'X Analytics - Trending Topics')

@section('styles')
<style>
  :root {
    --green:      #038047; --green-dark: #026738;
    --navy:       #273B4A; --white:      #FFFFFF;
    --surface:    #F1F5F8; --border:     #e2e8f0;
    --text-1:     #273B4A; --text-2:     #64748b; --text-3:     #94a3b8;
    --shadow-sm:  0 1px 3px rgba(0,0,0,.08); --shadow-md: 0 4px 16px rgba(0,0,0,.08);
    --radius:     12px; --radius-sm: 8px;
  }

  .x-header {
    display:flex;align-items:center;justify-content:space-between;
    padding:28px 32px 0;flex-wrap:wrap;gap:16px;
  }
  .x-header-left { display:flex;align-items:center;gap:14px; }
  .x-icon {
    width:44px;height:44px;background:var(--navy);
    border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;
  }
  .x-icon svg { width:22px;height:22px;fill:#fff; }
  .x-header-title h2 { font-size:22px;font-weight:700;color:var(--text-1);letter-spacing:-.4px;margin:0; }
  .x-header-title p { font-size:13px;color:var(--text-2);margin:2px 0 0; }

  .filter-bar { display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
  .filter-bar label { font-size:12px;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:.6px; }
  .filter-bar input[type="date"],.filter-bar input[type="text"] {
    padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
    font-family:'Poppins',sans-serif;font-size:13px;color:var(--text-1);
    background:var(--white);outline:none;transition:border-color .2s;
  }
  .filter-bar input[type="date"]:focus,
  .filter-bar input[type="text"]:focus { border-color:var(--green); }
  .btn-apply {
    padding:8px 20px;background:var(--green);color:#fff;border:none;
    border-radius:var(--radius-sm);font-family:'Poppins',sans-serif;
    font-size:13px;font-weight:600;cursor:pointer;transition:background .2s,transform .15s;
  }
  .btn-apply:hover { background:var(--green-dark);transform:translateY(-1px); }

  .x-body { padding:24px 32px 40px; }

  /* ── Notice: no project needed ── */
  .info-banner {
    display:flex;align-items:center;gap:10px;
    padding:10px 16px;background:rgba(3,128,71,.07);
    border:1px solid rgba(3,128,71,.15);border-radius:var(--radius-sm);
    font-size:12px;color:#026738;font-weight:500;
    margin-bottom:20px;
  }

  .info-banner svg { width:16px;height:16px;stroke:#038047;fill:none;flex-shrink:0; }

  /* ── Location pills ── */
  .location-pills {
    display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;
  }

  .location-pill {
    padding:6px 14px;border:1px solid var(--border);border-radius:20px;
    font-family:'Poppins',sans-serif;font-size:12px;font-weight:600;
    cursor:pointer;background:var(--white);color:var(--text-2);
    transition:all .2s;white-space:nowrap;
  }

  .location-pill:hover { border-color:var(--green);color:var(--green); }
  .location-pill.active { background:var(--green);color:#fff;border-color:var(--green); }

  /* ── Layout ── */
  .trend-layout {
    display:grid;grid-template-columns:1fr 1fr;gap:16px;
  }

  .chart-card {
    background:var(--white);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow-sm);overflow:hidden;
  }

  .chart-head {
    display:flex;align-items:center;justify-content:space-between;
    padding:18px 20px 14px;border-bottom:1px solid var(--border);
  }
  .chart-head h3 { font-size:14px;font-weight:700;color:var(--text-1);margin:0; }
  .chart-head span { font-size:11px;color:var(--text-3);font-weight:500; }

  /* ── Trending list ── */
  .trend-list { list-style:none;padding:16px;margin:0;display:flex;flex-direction:column;gap:4px; }

  .trend-item {
    display:flex;align-items:center;gap:12px;
    padding:12px 10px;border-radius:var(--radius-sm);
    transition:background .15s;cursor:default;
    border-bottom:1px solid var(--surface);
  }

  .trend-item:last-child { border-bottom:none; }
  .trend-item:hover { background:var(--surface); }

  .trend-rank {
    font-size:14px;font-weight:800;color:var(--text-3);
    width:28px;text-align:center;flex-shrink:0;
  }

  .trend-rank.top { color:var(--green); }

  .trend-info { flex:1;overflow:hidden; }

  .trend-name {
    font-size:14px;font-weight:700;color:var(--text-1);
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  }

  .trend-volume {
    font-size:11px;color:var(--text-3);margin-top:2px;
  }

  .trend-badge {
    display:inline-flex;align-items:center;gap:4px;
    padding:3px 8px;border-radius:4px;
    font-size:10px;font-weight:700;flex-shrink:0;
  }

  .trend-badge.rising { background:rgba(3,128,71,.1);color:var(--green); }
  .trend-badge.hot    { background:rgba(239,68,68,.1);color:#ef4444; }

  /* ── Chart card ── */
  .chart-body { padding:20px;min-height:300px;position:relative; }

  .skeleton {
    background:linear-gradient(90deg,#f1f5f8 25%,#e2e8f0 50%,#f1f5f8 75%);
    background-size:200% 100%;animation:shimmer 1.4s infinite;border-radius:6px;
  }
  @keyframes shimmer { 0% { background-position:200% 0; } 100% { background-position:-200% 0; } }
  .skeleton-wrap { display:flex;flex-direction:column;gap:10px; }

  .error-state {
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    height:200px;color:var(--text-3);font-size:13px;gap:8px;
  }
  .error-state svg { width:28px;height:28px;stroke:var(--text-3); }

  @media (max-width:900px) { .trend-layout { grid-template-columns:1fr; } }
  @media (max-width:768px) { .x-header,.x-body { padding-left:20px;padding-right:20px; } }
</style>
@endsection

@section('content')

<div class="x-header">
  <div class="x-header-left">
    <div class="x-icon">
      <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
    </div>
    <div class="x-header-title">
      <h2>Trending Topics X</h2>
      <p>Topik yang sedang viral dan ramai dibicarakan di platform X</p>
    </div>
  </div>

  <div class="filter-bar">
    <label>Dari</label>
    <input type="date" id="startDate" value="{{ $startDate }}">
    <label>Hingga</label>
    <input type="date" id="endDate" value="{{ $endDate }}">
    <label>Lokasi</label>
    <input type="text" id="locationInput" placeholder="Indonesia" style="width:130px;">
    <button class="btn-apply" onclick="applyFilter()">Terapkan</button>
  </div>
</div>

<div class="x-body">

  <div class="info-banner">
    <svg viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Data trending topics bersifat global per platform X dan tidak memerlukan filter project.
  </div>

  {{-- Location Presets --}}
  <div class="location-pills">
    <button class="location-pill active" onclick="setLocation('Indonesia', this)">Indonesia</button>
    <button class="location-pill" onclick="setLocation('Jakarta', this)">Jakarta</button>
    <button class="location-pill" onclick="setLocation('Worldwide', this)">Worldwide</button>
    <button class="location-pill" onclick="setLocation('Bandung', this)">Bandung</button>
    <button class="location-pill" onclick="setLocation('Surabaya', this)">Surabaya</button>
  </div>

  <div class="trend-layout">

    {{-- Trending List --}}
    <div class="chart-card">
      <div class="chart-head">
        <h3>Topik Trending</h3>
        <span id="trendCount">Memuat...</span>
      </div>
      <div id="trendListWrap">
        <ul class="trend-list">
          @for($i = 0; $i < 8; $i++)
          <li>
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;">
              <div class="skeleton" style="width:28px;height:14px;border-radius:4px;"></div>
              <div style="flex:1;display:flex;flex-direction:column;gap:5px;">
                <div class="skeleton" style="height:12px;width:60%;"></div>
                <div class="skeleton" style="height:8px;width:35%;"></div>
              </div>
            </div>
          </li>
          @endfor
        </ul>
      </div>
    </div>

    {{-- Volume Bar Chart --}}
    <div class="chart-card">
      <div class="chart-head">
        <h3>Volume Tweet per Topik</h3>
        <span id="chartSubLabel">Top 10</span>
      </div>
      <div class="chart-body">
        <div id="chartLoading" class="skeleton-wrap">
          <div class="skeleton" style="height:300px;"></div>
        </div>
        <canvas id="trendChart" style="display:none;"></canvas>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
  const API_TRENDING = '/mk/x/api/trending-topics';

  let trendChart;
  let currentLocation = 'Indonesia';

  function fmt(n) {
    if (!n && n !== 0) return '-';
    if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M';
    if (n >= 1_000)     return (n / 1_000).toFixed(1) + 'K';
    return Number(n).toLocaleString('id-ID');
  }

  async function fetchJson(url) {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  }

  function buildQuery() {
    const s = document.getElementById('startDate').value;
    const e = document.getElementById('endDate').value;
    const loc = document.getElementById('locationInput').value || currentLocation;
    return `start_date=${s}&end_date=${e}&location=${encodeURIComponent(loc)}`;
  }

  function setLocation(loc, btn) {
    currentLocation = loc;
    document.getElementById('locationInput').value = loc;
    document.querySelectorAll('.location-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    loadTrending();
  }

  async function loadTrending() {
    const wrap = document.getElementById('trendListWrap');

    // Reset skeleton
    wrap.innerHTML = `<ul class="trend-list">${
      Array(8).fill(`<li><div style="display:flex;align-items:center;gap:12px;padding:10px 0;">
        <div class="skeleton" style="width:28px;height:14px;border-radius:4px;"></div>
        <div style="flex:1;display:flex;flex-direction:column;gap:5px;">
          <div class="skeleton" style="height:12px;width:60%;"></div>
          <div class="skeleton" style="height:8px;width:35%;"></div>
        </div>
      </div></li>`).join('')
    }</ul>`;

    document.getElementById('chartLoading').style.display = 'flex';
    const canvas = document.getElementById('trendChart');
    canvas.style.display = 'none';

    try {
      const data  = await fetchJson(`${API_TRENDING}?${buildQuery()}`);
      const raw   = data.data ?? data ?? [];
      const items = Array.isArray(raw) ? raw : Object.entries(raw).map(([k, v]) => ({ topic: k, ...(typeof v === 'object' ? v : { count: v }) }));
      const top   = items.slice(0, 20);

      document.getElementById('trendCount').textContent = top.length + ' topik';

      if (!top.length) {
        wrap.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;height:200px;color:#94a3b8;font-size:13px;">Tidak ada data trending untuk periode ini</div>`;
        document.getElementById('chartLoading').style.display = 'none';
        return;
      }

      // List
      wrap.innerHTML = `<ul class="trend-list">${
        top.map((t, i) => {
          const name  = t.topic ?? t.name ?? t.keyword ?? t.hashtag ?? Object.keys(t)[0] ?? 'Topik';
          const count = t.count ?? t.volume ?? t.tweet_count ?? 0;
          const isTop = i < 3;
          return `
            <li class="trend-item">
              <span class="trend-rank ${isTop ? 'top' : ''}">${i + 1}</span>
              <div class="trend-info">
                <div class="trend-name">${name}</div>
                ${count ? `<div class="trend-volume">${fmt(count)} tweet</div>` : ''}
              </div>
              ${isTop ? `<span class="trend-badge hot">Trending</span>` : ''}
            </li>`;
        }).join('')
      }</ul>`;

      // Bar Chart
      const top10  = top.slice(0, 10);
      const labels = top10.map(t => t.topic ?? t.name ?? t.keyword ?? '-');
      const values = top10.map(t => t.count ?? t.volume ?? t.tweet_count ?? 0);

      document.getElementById('chartLoading').style.display = 'none';
      canvas.style.display = 'block';

      if (trendChart) trendChart.destroy();
      trendChart = new Chart(canvas, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Volume',
            data: values,
            backgroundColor: labels.map((_, i) => i < 3 ? '#038047' : 'rgba(39,59,74,.15)'),
            borderColor   : labels.map((_, i) => i < 3 ? '#026738' : '#273B4A'),
            borderWidth   : 1,
            borderRadius  : 4,
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#273B4A',
              titleFont: { family: 'Poppins', size: 12 },
              bodyFont: { family: 'Poppins', size: 12 },
              callbacks: { label: ctx => ' ' + fmt(ctx.raw) + ' tweet' }
            }
          },
          scales: {
            x: { grid: { color: '#f1f5f8' }, ticks: { font: { family: 'Poppins', size: 11 }, color: '#94a3b8', callback: v => fmt(v) } },
            y: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 11 }, color: '#273B4A' } }
          }
        }
      });

    } catch (err) {
      wrap.innerHTML = `<div class="error-state"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span>Gagal memuat trending topics</span></div>`;
      document.getElementById('chartLoading').style.display = 'none';
      console.error('loadTrending:', err);
    }
  }

  function applyFilter() { loadTrending(); }

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('locationInput').value = currentLocation;
    loadTrending();
  });
</script>
@endsection