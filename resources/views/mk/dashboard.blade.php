@extends('mk.layouts.app')

@section('title', 'Dashboard - SMADIMENT')

@section('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* { box-sizing: border-box; }

:root {
  --green:       #027447;
  --green-dk:    #025a35;
  --green-light: #E6F4EE;
  --dark:        #0F172A;
  --mid:         #475569;
  --muted:       #94A3B8;
  --border:      #E2E8F0;
  --bg:          #F8FAFC;
  --white:       #FFFFFF;
  --r:           12px;
  --font:        'Plus Jakarta Sans', sans-serif;
  --sh-sm:       0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
  --sh-md:       0 4px 16px rgba(15,23,42,.08), 0 2px 6px rgba(15,23,42,.04);
  --sh-lg:       0 12px 40px rgba(15,23,42,.14), 0 4px 12px rgba(15,23,42,.06);
}

/* ══ Topbar ══════════════════════════════════════ */
.mk-topbar {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 20px;
  gap: 12px;
  flex-wrap: wrap;
}
.mk-page-title {
  font-family: var(--font);
  font-size: 22px;
  font-weight: 800;
  color: var(--dark);
  margin: 0 0 2px;
  letter-spacing: -.4px;
}
.mk-page-sub {
  font-family: var(--font);
  font-size: 13px;
  color: var(--muted);
  margin: 0;
  font-weight: 500;
}
.mk-topbar-right {
  display: flex;
  align-items: center;
  gap: 10px;
}
.mk-date-pill {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 99px;
  font-family: var(--font);
  font-size: 12px;
  font-weight: 600;
  color: var(--mid);
  box-shadow: var(--sh-sm);
}
.btn-logout {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  background: #FEF2F2;
  color: #DC2626;
  border: 1px solid #FECACA;
  border-radius: 99px;
  font-family: var(--font);
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: var(--sh-sm);
}
.btn-logout:hover {
  background: #DC2626;
  color: #fff;
  border-color: #DC2626;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(220,38,38,.25);
}

/* ══ Summary Strip ═══════════════════════════════ */
.summary-strip {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 20px;
}
.sum-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: var(--sh-sm);
  transition: transform .18s, box-shadow .18s;
}
.sum-card:hover { transform: translateY(-2px); box-shadow: var(--sh-md); }
.sum-icon {
  width: 40px; height: 40px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.sum-icon--blue   { background:#EFF6FF; color:#3B82F6; }
.sum-icon--green  { background:#ECFDF5; color:#059669; }
.sum-icon--purple { background:#F5F3FF; color:#8B5CF6; }
.sum-info { display: flex; flex-direction: column; gap: 2px; }
.sum-lbl {
  font-family: var(--font); font-size: 11px; font-weight: 600;
  color: var(--muted); text-transform: uppercase; letter-spacing: .5px;
}
.sum-val {
  font-family: var(--font); font-size: 20px; font-weight: 800;
  color: var(--dark); letter-spacing: -.5px; line-height: 1;
}

/* ══ Two-column Main ═════════════════════════════ */
.mk-main {
  display: grid;
  grid-template-columns: 236px 1fr;
  gap: 16px;
  align-items: start;
}

/* ══ Left Sidebar ════════════════════════════════ */
.proj-sidebar {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  box-shadow: var(--sh-sm);
  overflow: hidden;
  position: sticky;
  top: 16px;
}
.proj-sidebar-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 13px 16px;
  border-bottom: 1px solid var(--border);
  background: var(--bg);
}
.proj-sidebar-title {
  font-family: var(--font); font-size: 11px; font-weight: 700;
  color: var(--dark); text-transform: uppercase; letter-spacing: .6px;
}
.proj-sidebar-badge {
  background: var(--green); color: #fff;
  padding: 2px 9px; border-radius: 99px;
  font-family: var(--font); font-size: 10px; font-weight: 700;
}
.proj-list { padding: 6px; }
.proj-item {
  display: flex; align-items: center; gap: 9px;
  padding: 9px 10px; border-radius: 8px;
  cursor: pointer;
  transition: background .15s, transform .15s;
  margin-bottom: 2px;
}
.proj-item:hover { background: var(--green-light); transform: translateX(2px); }
.proj-item:hover .proj-dot  { background: var(--green); transform: scale(1.3); }
.proj-item:hover .proj-arr  { opacity: 1; color: var(--green); }
.proj-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #CBD5E1; flex-shrink: 0; transition: all .2s;
}
.proj-info { flex: 1; min-width: 0; }
.proj-name {
  display: block; font-family: var(--font); font-size: 13px; font-weight: 600;
  color: var(--dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.proj-meta {
  display: block; font-family: var(--font); font-size: 10px;
  color: var(--muted); font-weight: 500; margin-top: 2px;
}
.proj-arr { opacity: 0; transition: opacity .15s; flex-shrink: 0; }

/* Empty sidebar */
.proj-empty {
  padding: 32px 16px; text-align: center;
  font-family: var(--font); font-size: 12px; color: var(--muted);
}

/* ══ Charts Column ═══════════════════════════════ */
.charts-col { display: flex; flex-direction: column; gap: 16px; }

/* ══ Chart Card ══════════════════════════════════ */
.chart-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r);
  box-shadow: var(--sh-sm);
  padding: 20px;
  transition: border-color .3s, box-shadow .3s;
}
.chart-card.hl {
  border-color: var(--green);
  box-shadow: 0 0 0 3px rgba(2,116,71,.09), var(--sh-md);
}

/* Card Header */
.card-hd {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; margin-bottom: 16px; flex-wrap: wrap;
}
.card-hd-left { display: flex; align-items: center; gap: 10px; }
.card-hd-dot {
  width: 10px; height: 10px; border-radius: 50%;
  background: var(--green); flex-shrink: 0;
  box-shadow: 0 0 0 3px rgba(2,116,71,.15);
}
.card-title {
  font-family: var(--font); font-size: 15px; font-weight: 700;
  color: var(--dark); margin: 0 0 2px; letter-spacing: -.2px;
}
.card-sub {
  font-family: var(--font); font-size: 11px;
  color: var(--muted); font-weight: 500;
}
.card-actions { display: flex; align-items: center; gap: 6px; }
.btn-primary {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 7px 14px; background: var(--green); color: #fff;
  border-radius: 8px; font-family: var(--font); font-size: 12px;
  font-weight: 600; text-decoration: none;
  box-shadow: 0 1px 4px rgba(2,116,71,.25);
  transition: background .18s, transform .18s, box-shadow .18s;
}
.btn-primary:hover { background: var(--green-dk); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(2,116,71,.3); color:#fff; }
.btn-icon {
  width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
  background: var(--bg); border: 1px solid var(--border); border-radius: 8px;
  color: var(--mid); text-decoration: none; transition: all .18s;
}
.btn-icon:hover { background: var(--green); border-color: var(--green); color: #fff; transform: translateY(-1px); }

/* Card Divider */
.card-divider { height: 1px; background: var(--border); margin-bottom: 16px; }

/* Chart Wrap */
.chart-wrap { height: 230px; position: relative; }

/* ══ Empty state ═════════════════════════════════ */
.empty-main {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 80px 20px; background: var(--white);
  border: 1px dashed var(--border); border-radius: var(--r); text-align: center; gap: 8px;
}
.empty-main h3 { font-family: var(--font); font-size: 16px; font-weight: 700; color: var(--dark); margin: 8px 0 0; }
.empty-main p  { font-family: var(--font); font-size: 13px; color: var(--muted); margin: 0; }

/* ══ Skeleton ════════════════════════════════════ */
.skeleton-card {
  height: 320px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: var(--r);
}
@keyframes shimmer {
  0%   { background-position: -200% 0; }
  100% { background-position:  200% 0; }
}

/* ══ Fade-in ═════════════════════════════════════ */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}
.fade-in { animation: fadeIn 0.4s ease-out; }

/* ══ Responsive ══════════════════════════════════ */
@media (max-width: 1024px) {
  .mk-main { grid-template-columns: 1fr; }
  .proj-sidebar { position: static; }
}
@media (max-width: 768px) {
  .summary-strip { grid-template-columns: 1fr 1fr; }
  .chart-card { padding: 14px; }
  .card-hd { flex-direction: column; align-items: flex-start; }
  .card-actions { width: 100%; }
  .btn-primary { flex: 1; justify-content: center; }
  .chart-wrap { height: 190px; }
  .mk-topbar { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 480px) {
  .summary-strip { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')

{{-- ── Top Bar ── --}}
<div class="mk-topbar">
  <div>
    <h1 class="mk-page-title">Welcome, {{ auth()->user()->name ?? 'User' }}!</h1>
    <p class="mk-page-sub">Here are your assigned projects</p>
  </div>
  <div class="mk-topbar-right">
    <div class="mk-date-pill">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      <span id="mkDateLabel"></span>
    </div>
    <form method="POST" action="{{ route('user.logout') }}" style="display:inline;">
      @csrf
      <button type="submit" class="btn-logout">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Logout
      </button>
    </form>
  </div>
</div>

{{-- ── Summary Strip ── --}}
<div class="summary-strip">
  <div class="sum-card">
    <div class="sum-icon sum-icon--blue">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
      </svg>
    </div>
    <div class="sum-info">
      <span class="sum-lbl">My Projects</span>
      <span class="sum-val">{{ count($projects) }}</span>
    </div>
  </div>
  <div class="sum-card">
    <div class="sum-icon sum-icon--green">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
      </svg>
    </div>
    <div class="sum-info">
      <span class="sum-lbl">Active Sources</span>
      <span class="sum-val">6</span>
    </div>
  </div>
  <div class="sum-card">
    <div class="sum-icon sum-icon--purple">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
    </div>
    <div class="sum-info">
      <span class="sum-lbl">Your Account</span>
      <span class="sum-val" style="font-size:14px;letter-spacing:0;">{{ auth()->user()->name ?? 'User' }}</span>
    </div>
  </div>
</div>

{{-- ── Main 2-col ── --}}
<div class="mk-main">

  {{-- Left: Project List --}}
  <aside class="proj-sidebar">
    <div class="proj-sidebar-head">
      <span class="proj-sidebar-title">Your Projects</span>
      <span class="proj-sidebar-badge">{{ count($projects) }}</span>
    </div>
    <div class="proj-list">
      @forelse($projects as $project)
      @php
        $pId    = $project['id'] ?? '-';
        $pTitle = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled';
      @endphp
      <div class="proj-item" data-id="{{ $pId }}">
        <div class="proj-dot"></div>
        <div class="proj-info">
          <span class="proj-name">{{ $pTitle }}</span>
          <span class="proj-meta">#{{ $pId }}</span>
        </div>
        <svg class="proj-arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="11" height="11">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </div>
      @empty
      <div class="proj-empty">No projects assigned</div>
      @endforelse
    </div>
  </aside>

  {{-- Right: Project Cards --}}
  <div class="charts-col">

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
        $id    = $project['id'] ?? '-';
        $title = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled Project';
        $group = $project['project_group_name'] ?? 'No Group';
        $type  = $project['project_type'] ?? 'Unknown';
        $media = $project['media_types'] ?? 'None';
      @endphp

      <div class="chart-card" id="card-{{ $id }}">

        {{-- Card Header --}}
        <div class="card-hd">
          <div class="card-hd-left">
            <div class="card-hd-dot"></div>
            <div>
              <h3 class="card-title">{{ $title }}</h3>
              <span class="card-sub">Project #{{ $id }} &middot; {{ $group }}</span>
            </div>
          </div>
          <div class="card-actions">
            <a href="{{ route('mk.data-overview') }}?project_id={{ $id }}" class="btn-primary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
              </svg>
              Data Overview
            </a>
            <a href="{{ route('mk.sentiment') }}?project_id={{ $id }}" class="btn-icon" title="Sentiment">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <circle cx="12" cy="12" r="10"/>
                <path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/>
              </svg>
            </a>
            <a href="{{ route('mk.geographic') }}?project_id={{ $id }}" class="btn-icon" title="Geographic">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
              </svg>
            </a>
          </div>
        </div>

        {{-- Meta Tags --}}
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;">
          <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#F1F5F9;border-radius:99px;font-family:var(--font);font-size:10px;font-weight:700;color:var(--mid);text-transform:uppercase;letter-spacing:.4px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="10" height="10"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            {{ $type }}
          </span>
          <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#F0FDF4;border-radius:99px;font-family:var(--font);font-size:10px;font-weight:700;color:#027447;text-transform:uppercase;letter-spacing:.4px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="10" height="10"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            {{ $media }}
          </span>
        </div>

        <div class="card-divider"></div>

        {{-- Placeholder chart area (no data without API call) --}}
        <div class="chart-wrap">
          <canvas id="chart-{{ $id }}"></canvas>
        </div>

      </div>
      @empty
      <div class="empty-main">
        <svg viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" width="56" height="56">
          <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
        </svg>
        <h3>No Projects Assigned</h3>
        <p>Contact your administrator to get access to projects.</p>
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
  document.getElementById('mkDateLabel').textContent =
    now.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });

  // ── Skeleton → show cards ───────────────────────
  setTimeout(function () {
    const skeleton = document.getElementById('skeletonCards');
    const cards    = document.getElementById('actualCards');
    if (skeleton && cards) {
      skeleton.style.display = 'none';
      cards.style.display    = 'flex';
      cards.style.flexDirection = 'column';
      cards.style.gap        = '16px';
      cards.classList.add('fade-in');
    }
    buildCharts();
  }, 300);

  // ── Sidebar scroll ──────────────────────────────
  document.querySelectorAll('.proj-item').forEach(function (el) {
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
    orange: '#F59E0B',
    gray:   '#94A3B8',
    red:    '#F87171',
    white:  '#FFFFFF',
    light:  '#94A3B8',
    border: '#E2E8F0',
    dark:   '#0F172A',
  };

  // Placeholder data (7-day demo) — replace with real API data if available
  const days = [];
  for (let i = 6; i >= 0; i--) {
    const d = new Date(); d.setDate(d.getDate() - i);
    days.push(d.getDate() + ' ' + d.toLocaleString('default', { month: 'short' }));
  }

  document.querySelectorAll('[id^="chart-"]').forEach(function (canvas) {
    const allData = days.map(() => Math.floor(Math.random() * 300 + 200));
    const posData = allData.map(v => Math.round(v * 0.42));
    const neuData = allData.map(v => Math.round(v * 0.38));
    const negData = allData.map(v => Math.round(v * 0.20));

    const ds = (label, data, color, dashed) => ({
      label, data,
      borderColor: color, backgroundColor: 'transparent',
      borderWidth: dashed ? 1.8 : 2.2,
      borderDash: dashed ? [4, 3] : [],
      tension: 0.45,
      pointRadius: 4, pointHoverRadius: 6,
      pointBackgroundColor: color, pointBorderColor: C.white, pointBorderWidth: 2,
      fill: false,
    });

    new Chart(canvas, {
      type: 'line',
      data: {
        labels: days,
        datasets: [
          ds('Mentions', allData, C.blue),
          ds('Positive', posData, C.orange),
          ds('Neutral',  neuData, C.gray,  true),
          ds('Negative', negData, C.red,   true),
        ],
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        layout: { padding: { top: 4, right: 8, bottom: 0, left: 4 } },
        plugins: {
          legend: {
            position: 'bottom', align: 'start',
            labels: {
              usePointStyle: true, pointStyle: 'circle', padding: 16,
              font: { size: 11, weight: '600', family: "'Plus Jakarta Sans',sans-serif" },
              color: C.light, boxWidth: 7, boxHeight: 7,
            },
          },
          tooltip: {
            mode: 'index', intersect: false,
            backgroundColor: '#fff', titleColor: C.dark, bodyColor: C.dark,
            borderColor: C.border, borderWidth: 1, padding: 12, cornerRadius: 8,
            titleFont: { size: 11, weight: '700', family: "'Plus Jakarta Sans',sans-serif" },
            bodyFont: { size: 11, weight: '500', family: "'Plus Jakarta Sans',sans-serif" },
            displayColors: true, boxWidth: 7, boxHeight: 7, boxPadding: 5,
            callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()}` },
          },
        },
        scales: {
          x: {
            grid: { display: false }, border: { display: false },
            ticks: { font: { size: 10, weight: '500', family: "'Plus Jakarta Sans',sans-serif" }, color: C.light, padding: 6, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 },
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(226,232,240,.8)', drawBorder: false, lineWidth: 1 },
            border: { display: false },
            ticks: { font: { size: 10, weight: '500', family: "'Plus Jakarta Sans',sans-serif" }, color: C.light, padding: 10, maxTicksLimit: 5, callback: v => v >= 1000 ? (v / 1000) + 'k' : v },
          },
        },
        interaction: { intersect: false, mode: 'index' },
      },
    });
  });
}
</script>
@endsection