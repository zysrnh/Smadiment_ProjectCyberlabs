@extends('layouts.app')

@section('title', 'X Analytics - Demografi Author')

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

  .x-header-title h2 {
    font-size:22px;font-weight:700;color:var(--text-1);letter-spacing:-.4px;margin:0;
  }

  .x-header-title p { font-size:13px;color:var(--text-2);margin:2px 0 0; }

  .filter-bar { display:flex;align-items:center;gap:12px;flex-wrap:wrap; }
  .filter-bar label { font-size:12px;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:.6px; }
  .filter-bar input[type="date"] {
    padding:8px 12px;border:1px solid var(--border);border-radius:var(--radius-sm);
    font-family:'Poppins',sans-serif;font-size:13px;color:var(--text-1);
    background:var(--white);outline:none;transition:border-color .2s;
  }
  .filter-bar input[type="date"]:focus { border-color:var(--green); }
  .btn-apply {
    padding:8px 20px;background:var(--green);color:#fff;border:none;
    border-radius:var(--radius-sm);font-family:'Poppins',sans-serif;
    font-size:13px;font-weight:600;cursor:pointer;transition:background .2s,transform .15s;
  }
  .btn-apply:hover { background:var(--green-dark);transform:translateY(-1px); }

  .x-body { padding:24px 32px 40px; }

  .chart-grid-3 {
    display:grid;grid-template-columns:repeat(3,1fr);
    gap:16px;margin-bottom:16px;
  }

  .chart-grid-2 {
    display:grid;grid-template-columns:repeat(2,1fr);
    gap:16px;margin-bottom:16px;
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

  .chart-body {
    padding:20px;min-height:260px;position:relative;
    display:flex;align-items:center;justify-content:center;flex-direction:column;
  }

  .skeleton {
    background:linear-gradient(90deg,#f1f5f8 25%,#e2e8f0 50%,#f1f5f8 75%);
    background-size:200% 100%;animation:shimmer 1.4s infinite;border-radius:6px;
  }
  @keyframes shimmer { 0% { background-position:200% 0; } 100% { background-position:-200% 0; } }
  .skeleton-wrap { display:flex;flex-direction:column;gap:10px;width:100%; }
  .h200 { height:200px; }

  .error-state {
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    height:160px;color:var(--text-3);font-size:13px;gap:8px;
  }
  .error-state svg { width:28px;height:28px;stroke:var(--text-3); }

  /* ── Age Bar Chart Custom ── */
  .age-bars { width:100%;padding:8px 0; }
  .age-row {
    display:flex;align-items:center;gap:12px;margin-bottom:14px;
  }
  .age-row:last-child { margin-bottom:0; }
  .age-label { font-size:12px;font-weight:600;color:var(--text-2);width:60px;flex-shrink:0; }
  .age-bar-wrap { flex:1;height:8px;background:var(--surface);border-radius:4px;overflow:hidden; }
  .age-bar-fill { height:100%;background:var(--green);border-radius:4px;transition:width .6s cubic-bezier(.4,0,.2,1); }
  .age-count { font-size:12px;font-weight:700;color:var(--text-1);white-space:nowrap;width:50px;text-align:right; }

  /* ── Location List ── */
  .location-list { list-style:none;padding:0;margin:0;width:100%; }
  .location-item {
    display:flex;align-items:center;gap:12px;
    padding:10px 0;border-bottom:1px solid var(--surface);
  }
  .location-item:last-child { border-bottom:none; }
  .location-rank { font-size:11px;font-weight:700;color:var(--text-3);width:20px;text-align:center; }
  .location-name { font-size:13px;color:var(--text-1);flex:1;font-weight:500; }
  .location-bar-wrap { width:100px;height:4px;background:var(--surface);border-radius:2px;overflow:hidden; }
  .location-bar-fill { height:100%;background:var(--navy);border-radius:2px;transition:width .5s; }
  .location-count { font-size:12px;font-weight:700;color:var(--text-2);white-space:nowrap; }

  @media (max-width:1100px) { .chart-grid-3 { grid-template-columns:repeat(2,1fr); } }
  @media (max-width:768px) {
    .chart-grid-3,.chart-grid-2 { grid-template-columns:1fr; }
    .x-header,.x-body { padding-left:20px;padding-right:20px; }
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
      <h2>Demografi Author X</h2>
      <p>Profil dan karakteristik akun yang berkontribusi di platform X</p>
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

  {{-- Row 1: Gender + Tipe Author + Usia --}}
  <div class="chart-grid-3">

    <div class="chart-card">
      <div class="chart-head">
        <h3>Distribusi Gender</h3>
        <span>Author X</span>
      </div>
      <div class="chart-body">
        <div id="genderLoading" class="skeleton-wrap">
          <div class="skeleton h200"></div>
        </div>
        <canvas id="genderChart" style="display:none;max-width:200px;"></canvas>
        <div id="genderLegend" style="display:none;margin-top:12px;width:100%;max-width:200px;"></div>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-head">
        <h3>Tipe Akun</h3>
        <span>Author X</span>
      </div>
      <div class="chart-body">
        <div id="typeLoading" class="skeleton-wrap">
          <div class="skeleton h200"></div>
        </div>
        <canvas id="typeChart" style="display:none;max-width:200px;"></canvas>
        <div id="typeLegend" style="display:none;margin-top:12px;width:100%;max-width:200px;"></div>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-head">
        <h3>Kelompok Usia</h3>
        <span>Author X</span>
      </div>
      <div class="chart-body" style="align-items:flex-start;justify-content:flex-start;">
        <div id="ageLoading" class="skeleton-wrap">
          <div class="skeleton h200"></div>
        </div>
        <div id="ageBars" class="age-bars" style="display:none;"></div>
      </div>
    </div>

  </div>

  {{-- Row 2: Lokasi Author --}}
  <div class="chart-grid-2">

    <div class="chart-card">
      <div class="chart-head">
        <h3>Lokasi Author Teratas</h3>
        <span>Top 15 lokasi</span>
      </div>
      <div class="chart-body" style="align-items:flex-start;justify-content:flex-start;min-height:320px;">
        <div id="locationLoading" class="skeleton-wrap">
          <div class="skeleton h200"></div>
        </div>
        <ul class="location-list" id="locationList" style="display:none;"></ul>
      </div>
    </div>

    <div class="chart-card">
      <div class="chart-head">
        <h3>User Paling Aktif</h3>
        <span>X</span>
      </div>
      <div class="chart-body" style="align-items:flex-start;justify-content:flex-start;min-height:320px;padding:0;">
        <div id="activeLoading" class="skeleton-wrap" style="padding:20px;">
          <div class="skeleton h200"></div>
        </div>
        <div id="activeTable" style="display:none;width:100%;overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;">
            <thead>
              <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;border-bottom:1px solid #e2e8f0;background:#F1F5F8;">#</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;border-bottom:1px solid #e2e8f0;background:#F1F5F8;">Akun</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#94a3b8;border-bottom:1px solid #e2e8f0;background:#F1F5F8;">Post</th>
              </tr>
            </thead>
            <tbody id="activeTableBody"></tbody>
          </table>
        </div>
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
    gender   : '/mk/x/api/authors-gender',
    type     : '/mk/x/api/authors-type',
    age      : '/mk/x/api/authors-age',
    location : '/mk/x/api/top-author-location',
    active   : '/mk/x/api/active-users',
  };

  const PALETTE = ['#038047','#273B4A','#94a3b8','#2FC6F6','#f59e0b'];

  let genderChart, typeChart;

  function fmt(n) {
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
    if (el) {
      el.innerHTML = `<div class="error-state"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span>${msg}</span></div>`;
      el.style.display = 'block';
    }
  }

  function buildDonut(canvasId, legendId, loadingId, labels, values, colors) {
    document.getElementById(loadingId).style.display = 'none';
    const canvas = document.getElementById(canvasId);
    canvas.style.display = 'block';
    const legend  = document.getElementById(legendId);
    const total   = values.reduce((a, b) => a + b, 0) || 1;

    const chart = new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{ data: values, backgroundColor: colors, borderWidth: 0, hoverOffset: 5 }]
      },
      options: {
        cutout: '65%', responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#273B4A',
            titleFont: { family: 'Poppins', size: 12 },
            bodyFont: { family: 'Poppins', size: 12 },
            callbacks: { label: ctx => ' ' + ((ctx.raw / total) * 100).toFixed(1) + '% (' + fmt(ctx.raw) + ')' }
          }
        }
      }
    });

    legend.style.display = 'block';
    legend.innerHTML = labels.map((l, i) => `
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
        <span style="width:10px;height:10px;border-radius:50%;background:${colors[i]};flex-shrink:0;"></span>
        <span style="font-size:12px;color:#273B4A;flex:1;">${l}</span>
        <span style="font-size:12px;font-weight:700;color:#273B4A;">${fmt(values[i])}</span>
      </div>`).join('');

    return chart;
  }

  async function loadGender() {
    try {
      const data  = await fetchJson(`${API.gender}?${BASE_PARAMS()}`);
      const items = data.data ?? data ?? [];
      const labels = items.map(d => d.gender ?? d.label ?? d.name ?? 'Lainnya');
      const values = items.map(d => d.count ?? d.total ?? 0);
      buildDonut('genderChart', 'genderLegend', 'genderLoading', labels, values, PALETTE);
    } catch (err) {
      showError('genderLoading', 'Gagal memuat gender');
      console.error('loadGender:', err);
    }
  }

  async function loadType() {
    try {
      const data  = await fetchJson(`${API.type}?${BASE_PARAMS()}`);
      const items = data.data ?? data ?? [];
      const labels = items.map(d => d.type ?? d.label ?? d.name ?? 'Lainnya');
      const values = items.map(d => d.count ?? d.total ?? 0);
      buildDonut('typeChart', 'typeLegend', 'typeLoading', labels, values, ['#038047','#273B4A','#94a3b8','#2FC6F6']);
    } catch (err) {
      showError('typeLoading', 'Gagal memuat tipe akun');
      console.error('loadType:', err);
    }
  }

  async function loadAge() {
    try {
      const data  = await fetchJson(`${API.age}?${BASE_PARAMS()}`);
      const items = data.data ?? data ?? [];
      const max   = Math.max(...items.map(d => d.count ?? d.total ?? 0)) || 1;

      document.getElementById('ageLoading').style.display = 'none';
      const bars = document.getElementById('ageBars');
      bars.style.display = 'block';

      bars.innerHTML = items.map(d => {
        const label = d.age ?? d.label ?? d.range ?? d.name ?? '-';
        const count = d.count ?? d.total ?? 0;
        const pct   = ((count / max) * 100).toFixed(0);
        return `
          <div class="age-row">
            <span class="age-label">${label}</span>
            <div class="age-bar-wrap">
              <div class="age-bar-fill" style="width:${pct}%"></div>
            </div>
            <span class="age-count">${fmt(count)}</span>
          </div>`;
      }).join('');

    } catch (err) {
      showError('ageLoading', 'Gagal memuat usia');
      console.error('loadAge:', err);
    }
  }

  async function loadLocation() {
    try {
      const data  = await fetchJson(`${API.location}?${BASE_PARAMS()}`);
      const items = (data.data ?? data ?? []).slice(0, 15);
      const max   = items[0]?.count ?? items[0]?.total ?? 1;

      document.getElementById('locationLoading').style.display = 'none';
      const list = document.getElementById('locationList');
      list.style.display = 'block';

      list.innerHTML = items.map((loc, i) => {
        const name  = loc.location ?? loc.name ?? loc.city ?? 'Tidak diketahui';
        const count = loc.count ?? loc.total ?? 0;
        const pct   = ((count / max) * 100).toFixed(0);
        return `
          <li class="location-item">
            <span class="location-rank">${i + 1}</span>
            <span class="location-name">${name}</span>
            <div class="location-bar-wrap">
              <div class="location-bar-fill" style="width:${pct}%"></div>
            </div>
            <span class="location-count">${fmt(count)}</span>
          </li>`;
      }).join('');

    } catch (err) {
      showError('locationLoading', 'Gagal memuat lokasi');
      console.error('loadLocation:', err);
    }
  }

  async function loadActiveUsers() {
    try {
      const data  = await fetchJson(`${API.active}?${BASE_PARAMS()}`);
      const items = (data.data ?? data ?? []).slice(0, 10);

      document.getElementById('activeLoading').style.display = 'none';
      const table = document.getElementById('activeTable');
      table.style.display = 'block';

      document.getElementById('activeTableBody').innerHTML = items.map((u, i) => {
        const name  = u.name ?? u.username ?? u.screen_name ?? 'User';
        const count = u.post_freq ?? u.count ?? u.posts ?? 0;
        return `
          <tr>
            <td style="padding:12px 16px;font-size:12px;font-weight:700;color:#94a3b8;border-bottom:1px solid #e2e8f0;">${i + 1}</td>
            <td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;">
              <div style="font-size:13px;font-weight:600;color:#273B4A;">${name}</div>
              ${u.screen_name ? `<div style="font-size:11px;color:#94a3b8;">@${u.screen_name}</div>` : ''}
            </td>
            <td style="padding:12px 16px;text-align:right;font-size:13px;font-weight:700;color:#038047;border-bottom:1px solid #e2e8f0;">${fmt(count)}</td>
          </tr>`;
      }).join('');

    } catch (err) {
      showError('activeLoading', 'Gagal memuat user aktif');
      console.error('loadActiveUsers:', err);
    }
  }

  function applyFilter() {
    if (!PROJECT_ID) { alert('Pilih project terlebih dahulu.'); return; }
    loadAll();
  }

  function loadAll() {
    loadGender();
    loadType();
    loadAge();
    loadLocation();
    loadActiveUsers();
  }

  document.addEventListener('DOMContentLoaded', () => { if (PROJECT_ID) loadAll(); });
</script>
@endsection