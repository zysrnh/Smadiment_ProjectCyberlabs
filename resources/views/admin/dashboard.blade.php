@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css" />
<style>
* { box-sizing: border-box; }

:root {
  --green:       #038047;
  --green-dk:    #025a35;
  --green-light: #E8F5E9;
  --dark:        #1E293B;
  --mid:         #475569;
  --muted:       #94A3B8;
  --border:      #E2E8F0;
  --bg:          #F8FAFC;
  --white:       #FFFFFF;
  --r:           8px;
  --font:        'Poppins', sans-serif;
  --sh-sm:       0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
  --sh-md:       0 4px 14px rgba(15, 23, 42, .08);
  --sh-lg:       0 10px 30px rgba(15, 23, 42, .12);

  --c-news:  #3B82F6;
  --c-twit:  #1DA1F2;
  --c-fb:    #1877F2;
  --c-ig:    #E1306C;
  --c-yt:    #FF0000;
  --c-tt:    #111827;
}

/* ══ Topbar ══════════════════════════════════════ */
.adm-topbar {
  display: flex; align-items: flex-end; justify-content: space-between;
  margin-bottom: 24px; gap: 12px; flex-wrap: wrap;
}
.adm-page-title {
  font-family: var(--font); font-size: 24px; font-weight: 700;
  color: #fff; margin: 0 0 4px;
}
.adm-page-sub {
  font-family: var(--font); font-size: 14px; color: rgba(255,255,255,0.7);
  margin: 0; font-weight: 500;
}
.adm-date-pill {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 16px; background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2); border-radius: 8px;
  font-family: var(--font); font-size: 13px; font-weight: 600; color: #fff;
}

/* ══ Summary Strip ═══════════════════════════════ */
.summary-strip {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;
}
.sum-card {
  border-radius: 12px; padding: 20px; display: flex; align-items: center;
  justify-content: space-between; box-shadow: var(--sh-md);
  transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s ease, filter .25s ease;
  cursor: pointer; position: relative; overflow: hidden; color: #fff;
}
.sum-card::before {
  content: ''; position: absolute; top: 0; bottom: 0; left: -100%;
  width: 60%; background: linear-gradient(90deg,transparent,rgba(255,255,255,.22),transparent);
  pointer-events: none; z-index: 1;
}
.sum-card:hover {
  transform: translateY(-6px) scale(1.025); box-shadow: 0 20px 40px rgba(0,0,0,.25); filter: brightness(1.07);
}
.sum-card:hover::before { animation: kpiShimmer .6s ease forwards; }
@keyframes kpiShimmer { 100% { left: 150%; } }

.sum-info { display: flex; flex-direction: column; z-index: 2; flex: 1; }
.sum-lbl { font-family: var(--font); font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.75); margin-bottom: 4px; }
.sum-val { font-family: var(--font); font-size: 28px; font-weight: 600; color: #fff; line-height: 1; margin: 4px 0; }
.sum-sub { font-family: var(--font); font-size: 12px; color: rgba(255,255,255,0.75); margin-top: 8px; display: flex; align-items: center; gap: 4px; }

.sum-icon {
  width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
  background: rgba(255,255,255,.2); flex-shrink: 0; z-index: 2; font-size: 24px; color: #fff; transition: background .2s ease;
}
.sum-card:hover .sum-icon { background: rgba(255,255,255,.35); }
.sum-card:hover .sum-icon i { animation: kpiIconBounce .5s cubic-bezier(.34,1.56,.64,1) both; }
@keyframes kpiIconBounce { 0% { transform: scale(1); } 50% { transform: scale(1.15); } 100% { transform: scale(1); } }

/* ══ Two-column Main ═════════════════════════════ */
.adm-main { display: grid; grid-template-columns: 280px 1fr; gap: 24px; align-items: start; }

/* ══ Left Sidebar ════════════════════════════════ */
.proj-sidebar {
  background: var(--white); border: 1px solid var(--border); border-radius: 12px;
  box-shadow: var(--sh-sm); overflow: hidden; position: sticky; top: 24px;
}
.proj-sidebar-head {
  display: flex; align-items: center; justify-content: space-between; padding: 16px 20px;
  border-bottom: 1px solid var(--border); background: var(--white);
}
.proj-sidebar-title {
  font-family: var(--font); font-size: 15px; font-weight: 600; color: var(--dark);
  display: flex; align-items: center; gap: 8px;
}
.proj-sidebar-title i { color: var(--muted); font-size: 18px; }
.proj-sidebar-badge {
  background: var(--green); color: #fff; padding: 4px 10px; border-radius: 99px;
  font-family: var(--font); font-size: 12px; font-weight: 600;
}
.proj-list { padding: 0; max-height: 600px; overflow-y: auto; }
.proj-list::-webkit-scrollbar { width: 4px; }
.proj-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
.proj-item {
  display: flex; align-items: center; gap: 12px; padding: 16px 20px;
  border-bottom: 1px solid var(--bg); cursor: pointer; transition: all .15s ease;
}
.proj-item:last-child { border-bottom: none; }
.proj-item:hover { background: var(--bg); }
.proj-item.hl { background: var(--green-light); border-left: 3px solid var(--green); }
.proj-dot {
  width: 8px; height: 8px; border-radius: 50%; background: var(--green);
  flex-shrink: 0; box-shadow: 0 0 0 3px var(--green-light); animation: pulseP 2.5s infinite;
}
@keyframes pulseP { 0%,100% { box-shadow: 0 0 0 3px var(--green-light) } 50% { box-shadow: 0 0 0 6px transparent } }
.proj-info { flex: 1; min-width: 0; }
.proj-name {
  display: block; font-family: var(--font); font-size: 14px; font-weight: 500;
  color: var(--dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.proj-meta {
  display: block; font-family: var(--font); font-size: 12px; color: var(--muted); margin-top: 4px;
}
.proj-arr { color: var(--muted); transition: color .15s; flex-shrink: 0; font-size: 16px; }
.proj-item:hover .proj-arr { color: var(--green); }

/* ══ Charts Column ═══════════════════════════════ */
.charts-col { display: flex; flex-direction: column; gap: 24px; }

/* ══ Chart Card ══════════════════════════════════ */
.chart-card {
  background: var(--white); border: 1px solid var(--border); border-radius: 12px;
  box-shadow: var(--sh-sm); padding: 0; transition: border-color .3s, box-shadow .3s; overflow: hidden;
}
.chart-card.hl { border-color: var(--green); box-shadow: 0 0 0 3px rgba(3,128,71,.09), var(--sh-md); }

.card-hd {
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
  padding: 16px 20px; border-bottom: 1px solid var(--border); background: var(--white); flex-wrap: wrap;
}
.card-hd-left { display: flex; align-items: center; gap: 12px; }
.card-hd-dot {
  width: 32px; height: 32px; border-radius: 50%; background: var(--green-light);
  color: var(--green); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
}
.card-title { font-family: var(--font); font-size: 15px; font-weight: 600; color: var(--dark); margin: 0 0 2px; }
.card-sub { font-family: var(--font); font-size: 12px; color: var(--muted); font-weight: 500; }
.card-actions { display: flex; align-items: center; gap: 6px; }
.btn-primary {
  display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
  background: var(--green); color: #fff; border-radius: 6px; font-family: var(--font);
  font-size: 12px; font-weight: 600; text-decoration: none; box-shadow: var(--sh-sm);
  transition: all .18s; border: 1px solid var(--green);
}
.btn-primary:hover { filter: brightness(0.9); transform: translateY(-1px); color:#fff; }
.btn-primary i { font-size: 16px; }
.btn-icon {
  width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
  background: var(--white); border: 1px solid var(--border); border-radius: 6px;
  color: var(--mid); text-decoration: none; transition: all .18s; font-size: 16px;
}
.btn-icon:hover { background: var(--bg); border-color: var(--muted); color: var(--dark); transform: translateY(-1px); }

.card-bd { padding: 20px; }

/* ══ STATS PILLS ══════════════════════════════════ */
.stats-row {
  display: flex; align-items: center; gap: 8px; margin-bottom: 20px; overflow-x: auto; scrollbar-width: none;
}
.stats-row::-webkit-scrollbar { display: none; }
.stat-pill {
  display: flex; align-items: center; gap: 8px; padding: 8px 12px;
  background: var(--white); border: 1px solid var(--border); border-radius: 8px;
  flex-shrink: 0;
}
.stat-pill.all-pill { border-color: var(--green); background: var(--green-light); }
.stat-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.stat-info { display: flex; flex-direction: column; gap: 2px; }
.stat-val { font-family: var(--font); font-size: 14px; font-weight: 600; color: var(--dark); line-height: 1; }
.stat-lbl { font-family: var(--font); font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; }

.card-divider { height: 1px; background: var(--border); margin-bottom: 20px; }
.chart-wrap { height: 260px; position: relative; }

.empty-main {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 80px 20px; background: var(--white); border: 1px dashed var(--border);
  border-radius: var(--r); text-align: center; gap: 12px;
}
.empty-main i { font-size: 48px; color: var(--muted); }
.empty-main h3 { font-family: var(--font); font-size: 18px; font-weight: 600; color: var(--dark); margin: 0; }
.empty-main p  { font-family: var(--font); font-size: 14px; color: var(--muted); margin: 0; }

/* ══ Responsive ══════════════════════════════════ */
@media (max-width: 1200px) { .summary-strip { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 1024px) { .adm-main { grid-template-columns: 1fr; } .proj-sidebar { position: static; } }
@media (max-width: 640px) {
  .summary-strip { grid-template-columns: repeat(2,1fr); gap: 12px; }
  .sum-card { padding: 16px; flex-direction: column; align-items: flex-start; gap: 12px; }
  .sum-icon { position: absolute; top: 16px; right: 16px; width: 40px; height: 40px; font-size: 20px; }
  .sum-val { font-size: 24px; }
  .chart-card { padding: 0; }
  .card-hd { flex-direction: column; align-items: flex-start; }
  .card-actions { width: 100%; justify-content: flex-start; }
  .chart-wrap { height: 220px; }
  .adm-topbar { flex-direction: column; align-items: flex-start; gap: 16px; }
}
</style>
@endsection

@section('content')

{{-- ── Top Bar ── --}}
<div class="adm-topbar">
  <div>
    <h1 class="adm-page-title">Dashboard</h1>
    <p class="adm-page-sub">Monitor & manage all projects</p>
  </div>
  <div class="adm-date-pill">
    <i class="ph ph-calendar-blank"></i>
    <span id="admDateLabel"></span>
  </div>
</div>

{{-- ── Summary Strip ── --}}
<div class="summary-strip">
  <div class="sum-card" style="background:#06B6D4;">
    <div class="sum-info">
      <span class="sum-lbl">Total Projects</span>
      <span class="sum-val">{{ count($projects) }}</span>
      <span class="sum-sub"><i class="ph ph-folder"></i>All active projects</span>
    </div>
    <div class="sum-icon"><i class="ph ph-list-bullets"></i></div>
  </div>
  <div class="sum-card" style="background:#4CAF50;">
    <div class="sum-info">
      <span class="sum-lbl">Total Items</span>
      <span class="sum-val">{{ number_format(collect($projects)->sum(fn($p) => $p['stats']['all'] ?? 0)) }}</span>
      <span class="sum-sub"><i class="ph ph-activity"></i>Across all items</span>
    </div>
    <div class="sum-icon"><i class="ph ph-chart-bar"></i></div>
  </div>
  <div class="sum-card" style="background:#F59E0B;">
    <div class="sum-info">
      <span class="sum-lbl">Active Sources</span>
      <span class="sum-val">6</span>
      <span class="sum-sub"><i class="ph ph-broadcast"></i>Monitored platforms</span>
    </div>
    <div class="sum-icon"><i class="ph ph-share-network"></i></div>
  </div>
  <div class="sum-card" style="background:#038047;">
    <div class="sum-info">
      <span class="sum-lbl">Social Posts</span>
      <span class="sum-val">{{ number_format(collect($projects)->sum(fn($p) => ($p['stats']['twit'] ?? 0) + ($p['stats']['fb'] ?? 0) + ($p['stats']['ig'] ?? 0) + ($p['stats']['yt'] ?? 0) + ($p['stats']['tiktok'] ?? 0))) }}</span>
      <span class="sum-sub"><i class="ph ph-users"></i>From social media</span>
    </div>
    <div class="sum-icon"><i class="ph ph-activity"></i></div>
  </div>
</div>

{{-- ── Main 2-col ── --}}
<div class="adm-main">

  {{-- Left: Project List --}}
  <aside class="proj-sidebar">
    <div class="proj-sidebar-head">
      <span class="proj-sidebar-title"><i class="ph ph-list-bullets"></i>Projects</span>
      <span class="proj-sidebar-badge">{{ count($projects) }}</span>
    </div>
    <div class="proj-list">
      @forelse($projects as $project)
      <div class="proj-item" data-id="{{ $project['id'] }}">
        <div class="proj-dot"></div>
        <div class="proj-info">
          <span class="proj-name">{{ $project['name'] ?? $project['title'] ?? 'Unnamed' }}</span>
          <span class="proj-meta">#{{ $project['id'] }} · {{ number_format($project['stats']['all'] ?? 0) }} items</span>
        </div>
        <i class="ph ph-caret-right proj-arr"></i>
      </div>
      @empty
      <div style="padding:32px 16px;text-align:center;font-family:var(--font);font-size:13px;color:var(--muted);">
        No projects available
      </div>
      @endforelse
    </div>
  </aside>

  {{-- Right: Chart Cards --}}
  <div class="charts-col">
    @forelse($projects as $project)
    <div class="chart-card" id="card-{{ $project['id'] }}">

      {{-- Card Header --}}
      <div class="card-hd">
        <div class="card-hd-left">
          <div class="card-hd-dot"><i class="ph ph-pulse"></i></div>
          <div>
            <h3 class="card-title">{{ $project['name'] ?? $project['title'] ?? 'Unnamed Project' }}</h3>
            <span class="card-sub">Project #{{ $project['id'] }}</span>
          </div>
        </div>
        <div class="card-actions">
          <a href="{{ route('mk.sentiment', ['project_id' => $project['id']]) }}" class="btn-primary">
            <i class="ph ph-chart-bar"></i> Analytics
          </a>
          <a href="{{ route('mk.geographic', ['project_id' => $project['id']]) }}" class="btn-icon" title="Geographic">
            <i class="ph ph-map-pin"></i>
          </a>
          <a href="{{ route('mk.publisher', ['project_id' => $project['id']]) }}" class="btn-icon" title="Publisher">
            <i class="ph ph-users"></i>
          </a>
        </div>
      </div>

      <div class="card-bd">
        {{-- Stat Pills (display only) --}}
        @php
          $stats = [
            ['key'=>'all',    'lbl'=>'All',       'color'=>'#038047'],
            ['key'=>'news',   'lbl'=>'News',      'color'=>'#3B82F6'],
            ['key'=>'twit',   'lbl'=>'Twitter',   'color'=>'#1DA1F2'],
            ['key'=>'fb',     'lbl'=>'Facebook',  'color'=>'#1877F2'],
            ['key'=>'ig',     'lbl'=>'Instagram', 'color'=>'#E1306C'],
            ['key'=>'yt',     'lbl'=>'YouTube',   'color'=>'#FF0000'],
            ['key'=>'tiktok', 'lbl'=>'TikTok',    'color'=>'#111827'],
          ];
        @endphp

        <div class="stats-row">
          @foreach($stats as $st)
          <div class="stat-pill {{ $st['key'] === 'all' ? 'all-pill' : '' }}">
            <span class="stat-dot" style="background:{{ $st['color'] }}"></span>
            <div class="stat-info">
              <span class="stat-val">{{ number_format($project['stats'][$st['key']] ?? 0) }}</span>
              <span class="stat-lbl">{{ $st['lbl'] }}</span>
            </div>
          </div>
          @endforeach
        </div>

        <div class="card-divider"></div>

        {{-- Chart --}}
        <div class="chart-wrap">
          <canvas id="chart-{{ $project['id'] }}"></canvas>
        </div>
      </div>

    </div>
    @empty
    <div class="empty-main">
      <i class="ph ph-folder-open"></i>
      <h3>No Projects Found</h3>
      <p>There are no projects available at this moment.</p>
    </div>
    @endforelse
  </div>

</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Date label
  const now = new Date();
  document.getElementById('admDateLabel').textContent =
    now.toLocaleDateString('en-US', { weekday:'short', day:'numeric', month:'short', year:'numeric' });

  // Sidebar scroll to card
  document.querySelectorAll('.proj-item').forEach(el => {
    el.addEventListener('click', () => {
      const card = document.getElementById(`card-${el.dataset.id}`);
      if (!card) return;
      card.scrollIntoView({ behavior:'smooth', block:'start' });
      card.classList.add('hl');
      setTimeout(() => card.classList.remove('hl'), 2200);
    });
  });

  buildCharts();
});

function buildCharts() {
  const C = { blue:'#5AB9EA', orange:'#F59E0B', gray:'#94A3B8', red:'#F87171', white:'#FFFFFF', light:'#94A3B8', border:'#E2E8F0', dark:'#0F172A' };
  const projects = @json($projects);

  projects.forEach(project => {
    const ctx = document.getElementById(`chart-${project.id}`);
    if (!ctx) return;

    const tl = project.timeline || {};
    let labels = tl.dates || [];
    let allData, posData, neuData, negData;

    if (!labels.length) {
      labels   = ['20 Feb','21 Feb','22 Feb','23 Feb','24 Feb','25 Feb','26 Feb'];
      allData  = [520, 480, 610, 590, 720, 680, 800];
      posData  = [210, 200, 260, 250, 300, 280, 340];
      neuData  = [200, 185, 230, 220, 270, 255, 300];
      negData  = [110, 95,  120, 120, 150, 145, 160];
    } else {
      allData = tl.values || [];
      if (tl.sentiment?.positive) {
        posData = tl.sentiment.positive;
        neuData = tl.sentiment.neutral;
        negData = tl.sentiment.negative;
      } else {
        posData = allData.map(v => Math.round(v * 0.42));
        neuData = allData.map(v => Math.round(v * 0.38));
        negData = allData.map(v => Math.round(v * 0.20));
      }
    }

    const ds = (label, data, color, dashed = false) => ({
      label, data,
      borderColor: color, backgroundColor: 'transparent',
      borderWidth: dashed ? 1.8 : 2.2,
      borderDash: dashed ? [4,3] : [],
      tension: 0.45,
      pointRadius: 4, pointHoverRadius: 6,
      pointBackgroundColor: color, pointBorderColor: C.white, pointBorderWidth: 2,
      fill: false,
    });

    new Chart(ctx, {
      type: 'line',
      data: { labels, datasets: [
        ds('New',      allData, C.blue),
        ds('Positive', posData, C.orange),
        ds('Neutral',  neuData, C.gray,  true),
        ds('Negative', negData, C.red,   true),
      ]},
      options: {
        responsive: true, maintainAspectRatio: false,
        layout: { padding: { top:4, right:8, bottom:0, left:4 } },
        plugins: {
          legend: {
            position:'bottom', align:'start',
            labels: { usePointStyle:true, pointStyle:'circle', padding:16,
              font:{ size:11, weight:'600', family:"'Poppins',sans-serif" },
              color:C.light, boxWidth:7, boxHeight:7 }
          },
          tooltip: {
            mode:'index', intersect:false,
            backgroundColor:'#fff', titleColor:C.dark, bodyColor:C.dark,
            borderColor:C.border, borderWidth:1, padding:12, cornerRadius:8,
            titleFont:{ size:11,weight:'700',family:"'Poppins',sans-serif" },
            bodyFont:{ size:11,weight:'500',family:"'Poppins',sans-serif" },
            displayColors:true, boxWidth:7, boxHeight:7, boxPadding:5,
            callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()}` }
          },
        },
        scales: {
          x: {
            grid:{ display:false }, border:{ display:false },
            ticks:{ font:{size:10,weight:'500',family:"'Poppins',sans-serif"}, color:C.light, padding:6, maxRotation:0, autoSkip:true, maxTicksLimit:8 }
          },
          y: {
            beginAtZero:true,
            grid:{ color:'rgba(226,232,240,.8)', drawBorder:false, lineWidth:1 },
            border:{ display:false },
            ticks:{ font:{size:10,weight:'500',family:"'Poppins',sans-serif"}, color:C.light, padding:10, maxTicksLimit:5, callback:v=>v>=1000?(v/1000)+'k':v }
          },
        },
        interaction:{ intersect:false, mode:'index' },
      },
    });
  });
}
</script>
@endsection