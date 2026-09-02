@extends('mk.layouts.app')

@section('title', 'Word Cloud - SMADIMENT')

@section('styles')
  <style>
    /* ══ Design Tokens ══ */
    :root {
      --tm-primary: var(--bs-primary, #4361EE);
      --tm-primary-rgb: var(--bs-primary-rgb, 67, 97, 238);
      --tm-primary-lt: rgba(var(--tm-primary-rgb, 67, 97, 238), .10);
      --tm-green: #10B981;
      --tm-red: #EF4444;
      --slate-50: #F8FAFC;
      --slate-100: #F1F5F9;
      --slate-200: #E2E8F0;
      --slate-300: #CBD5E1;
      --slate-400: #94A3B8;
      --slate-500: #64748B;
      --slate-600: #475569;
      --slate-700: #334155;
      --slate-800: #1E293B;
      --slate-900: #0F172A;
      --radius: 8px;
      --radius-sm: 5px;
      --shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
      --shadow-md: 0 4px 14px rgba(15, 23, 42, .08);
      --shadow-lg: 0 10px 30px rgba(15, 23, 42, .12);
    }

    /* ══ Animations ══ */
    @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    @keyframes spin { to{transform:rotate(360deg)} }
    @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
    @keyframes slideUp { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }

    .fade-up { animation: fadeUp .38s ease-out both; }
    .fade-up-d1 { animation-delay: .05s }
    .fade-up-d2 { animation-delay: .10s }
    .fade-up-d3 { animation-delay: .15s }

    /* ══ KPI Cards ══ */
    .kpi-icon-bg { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0; }

    /* ══ Chart Container ══ */
    .tm-chart-wrap { position:relative;min-height:500px; }
    .wordcloud-container { min-height:700px;height:700px;position:relative;background:linear-gradient(135deg,#ffffff 0%,#f8fafc 100%);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;overflow:hidden; }
    #wordCloudChart { width:100%!important;height:100%!important;cursor:pointer;position:absolute;top:0;left:0;z-index:1; }
    .wordcloud-hint { position:absolute;bottom:12px;right:16px;z-index:2;display:none;align-items:center;gap:5px;font-size:11px;color:var(--slate-400);pointer-events:none; }
    .wordcloud-hint i { font-size:13px; }
    .bar-chart-wrapper { position:relative;height:500px; }
    .pie-chart-wrapper { position:relative;height:500px;display:flex;align-items:center;justify-content:center; }

    /* ══ Chart switcher tabs ══ */
    .tm-switcher { display:flex;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:var(--radius-sm);padding:2px;gap:2px; }
    .tm-switch-btn { padding:5px 14px;border-radius:3px;border:none;background:transparent;font-size:12px;font-weight:600;color:var(--slate-500);cursor:pointer;transition:all .13s;font-family:inherit; }
    .tm-switch-btn:hover { background:#fff;color:var(--slate-800); }
    .tm-switch-btn.active { background:#fff;color:var(--tm-primary);box-shadow:0 1px 4px rgba(0,0,0,.08); }
    .chart-view { display:none; }
    .chart-view.active { display:block; }

    /* ══ Topic list ══ */
    .topic-item { display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--slate-100);transition:background .12s,padding-left .12s;cursor:default; }
    .topic-item:last-child { border-bottom:none; }
    .topic-item:hover { background:var(--slate-50);padding-left:22px; }
    .topic-rank { font-size:12px;font-weight:800;color:var(--slate-400);min-width:28px;flex-shrink:0; }
    .topic-rank.top-3 { color:var(--tm-primary);font-size:13px; }
    .topic-name { flex:1;font-size:13px;font-weight:600;color:var(--slate-800); }
    .topic-count { background:var(--tm-primary-lt);color:var(--tm-primary);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;white-space:nowrap; }

    /* ══ Skeleton / Spinner ══ */
    .sk-block { border-radius:4px;background:linear-gradient(90deg,var(--slate-100) 25%,var(--slate-200) 50%,var(--slate-100) 75%);background-size:200% 100%;animation:shimmer 1.4s infinite; }
    .tm-spinner { width:32px;height:32px;border:3px solid var(--slate-100);border-top-color:var(--tm-primary);border-radius:50%;animation:spin .7s linear infinite; }
    .tm-loading { display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;padding:60px 20px;color:var(--slate-400);font-size:12px;font-weight:600; }
    .tm-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:60px 20px;color:var(--slate-400);font-size:12px;font-weight:600;text-align:center; }
    .tm-empty i { font-size:40px;color:var(--slate-300);display:block; }

    /* ══ Modal ══ */
    .tm-modal-overlay { display:none;position:fixed;inset:0;z-index:9000;background:rgba(15,23,42,.55);backdrop-filter:blur(6px);align-items:center;justify-content:center; }
    .tm-modal-overlay.active { display:flex; }
    .tm-modal-box { background:#fff;border-radius:var(--radius);width:90%;max-width:600px;max-height:80vh;display:flex;flex-direction:column;box-shadow:var(--shadow-lg);animation:slideUp .28s cubic-bezier(.4,0,.2,1); }
    .tm-modal-head { display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid var(--slate-200);background:var(--slate-50); }
    .tm-modal-head h5 { font-size:14px;font-weight:700;color:var(--slate-900);margin:0; }
    .tm-modal-close { width:28px;height:28px;border-radius:var(--radius-sm);background:#fff;border:1px solid var(--slate-200);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--slate-500);font-size:16px;transition:all .14s; }
    .tm-modal-close:hover { background:var(--tm-red);border-color:var(--tm-red);color:#fff; }
    .tm-modal-body { padding:16px 20px 20px;overflow-y:auto;flex:1; }
    .tm-modal-body::-webkit-scrollbar { width:4px; }
    .tm-modal-body::-webkit-scrollbar-thumb { background:var(--slate-200);border-radius:99px; }
    .tm-modal-search { width:100%;padding:8px 12px;margin-bottom:14px;border:1px solid var(--slate-200);border-radius:var(--radius-sm);font-size:12px;font-family:inherit;outline:none;transition:border-color .14s; }
    .tm-modal-search:focus { border-color:var(--tm-primary);box-shadow:0 0 0 3px var(--tm-primary-lt); }

    /* ════════════════════════════════════════
       EXPORT STYLES
    ════════════════════════════════════════ */
    .page-export-bar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius);padding:9px 14px;margin-bottom:20px;box-shadow:var(--shadow-sm)}
    .page-export-bar-left{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--slate-600)}
    .page-export-bar-left i{font-size:15px;color:var(--tm-primary)}
    .page-export-bar-right{display:flex;gap:8px}
    .page-export-btn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);font-size:16px;cursor:pointer;transition:all .15s ease;border:1.5px solid transparent;font-family:inherit}
    .page-export-btn-pdf{background:#fff3f3;color:#dc2626;border-color:#fca5a5}
    .page-export-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
    .page-export-btn-img{background:var(--tm-primary-lt);color:var(--tm-primary);border-color:rgba(67,97,238,.3)}
    .page-export-btn-img:hover{background:var(--tm-primary);color:#fff;border-color:var(--tm-primary)}
    .page-export-btn:disabled{opacity:.55;cursor:not-allowed;pointer-events:none}
    .page-export-btn .export-spinner{width:13px;height:13px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
    .page-export-btn.exporting .export-spinner{display:inline-block}
    .page-export-btn.exporting .export-icon{display:none}
    .card-exp-btn{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:var(--radius-sm);font-size:14px;cursor:pointer;flex-shrink:0;transition:all .14s ease;border:1px solid transparent;font-family:inherit;background:transparent}
    .card-exp-btn-pdf{color:#dc2626;border-color:#fca5a5;background:#fff3f3}
    .card-exp-btn-pdf:hover{background:#dc2626;color:#fff;border-color:#dc2626}
    .card-exp-btn-img{color:var(--tm-primary);border-color:rgba(67,97,238,.3);background:var(--tm-primary-lt)}
    .card-exp-btn-img:hover{background:var(--tm-primary);color:#fff;border-color:var(--tm-primary)}
    .card-exp-btn:disabled{opacity:.45;cursor:not-allowed;pointer-events:none}
    .card-exp-btn .export-spinner{width:11px;height:11px;border:2px solid currentColor;border-top-color:transparent;border-radius:50%;animation:spin .65s linear infinite;display:none}
    .card-exp-btn.exporting .export-spinner{display:inline-block}
    .card-exp-btn.exporting .export-icon{display:none}
    .export-toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--slate-900);color:#fff;border-radius:var(--radius);padding:10px 18px;font-size:12px;font-weight:600;box-shadow:var(--shadow-lg);z-index:99999;opacity:0;pointer-events:none;transition:opacity .22s ease,transform .22s ease;display:flex;align-items:center;gap:8px;white-space:nowrap}
    .export-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
    .export-toast.success{background:#065f46}
    .export-toast.error{background:#991b1b}

    @media(max-width:768px) {
      .wordcloud-container { min-height:480px;height:480px; }
    }
  </style>
@endsection

@section('page-title', 'Word Cloud')

@section('content')

  {{-- ══ Filter ══ --}}
  @include('mk.layouts.partials.filter-datepicker')

  {{-- ════ PAGE EXPORT WRAPPER ════ --}}
  <div id="pageExportArea">

  {{-- ══ KPI Cards ══ --}}
  <div class="row g-3 mb-3">
    <div class="col-md-4">
<div class="card h-100 text-white kpi-card-hover" style="background:#06B6D4;animation:fadeUp .38s ease-out both;">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <p class="mb-1 text-white text-opacity-75 f-12">Total Topics</p>
              <h3 class="mb-0 text-white f-w-300" id="statTotalTopics">—</h3>
              <p class="mb-0 mt-2 text-white text-opacity-75 f-12"><i class="ph ph-hash me-1"></i>Unique topics</p>
            </div>
            <div class="flex-shrink-0 ms-3">
              <div class="kpi-icon-bg"><i class="ph ph-hash"></i></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
<div class="card h-100 text-white kpi-card-hover" style="background:#F59E0B;animation:fadeUp .38s ease-out .05s both;">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <p class="mb-1 text-white text-opacity-75 f-12">Top Topic</p>
              <h3 class="mb-0 text-white f-w-300" id="statTopTopic" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:190px;">—</h3>
              <p class="mb-0 mt-2 text-white text-opacity-75 f-12"><i class="ph ph-trend-up me-1"></i>Most mentioned</p>
            </div>
            <div class="flex-shrink-0 ms-3">
              <div class="kpi-icon-bg"><i class="ph ph-star"></i></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
<div class="card h-100 text-white kpi-card-hover" style="background:#4CAF50;animation:fadeUp .38s ease-out .10s both;">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-grow-1">
              <p class="mb-1 text-white text-opacity-75 f-12">Total Mentions</p>
              <h3 class="mb-0 text-white f-w-300" id="statTotalMentions">—</h3>
              <p class="mb-0 mt-2 text-white text-opacity-75 f-12"><i class="ph ph-chat-dots me-1"></i>Across all topics</p>
            </div>
            <div class="flex-shrink-0 ms-3">
              <div class="kpi-icon-bg"><i class="ph ph-activity"></i></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ Page Export Toolbar ══ --}}
  <div class="page-export-bar" data-html2canvas-ignore="true">
    <div class="page-export-bar-left">
      <i class="ph ph-export"></i>
      <span>Export Halaman</span>
      <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">KPI + Chart + Topic List</span>
    </div>
    <div class="page-export-bar-right">
      <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
              onclick="WCExport.run('pdf', this)" title="Export halaman sebagai PDF">
        <i class="ph ph-file-pdf export-icon"></i>
        <span class="export-spinner"></span>
      </button>
      <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
              onclick="WCExport.run('image', this)" title="Export halaman sebagai PNG">
        <i class="ph ph-image export-icon"></i>
        <span class="export-spinner"></span>
      </button>
    </div>
  </div>

  {{-- ══ Chart Card ══ --}}
  <div class="card mb-3">
    <div id="card-export-chart">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-cloud f-18 text-primary"></i></div>
        <div>
          <h6 class="mb-0" id="chartTitle">Topic Word Cloud</h6>
          <small class="text-muted">Visualisasi topik berdasarkan frekuensi mention</small>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="tm-switcher">
          <button class="tm-switch-btn active" onclick="TMApp.switchChart('wordcloud')" id="btnWordCloud">
            <i class="ph ph-cloud me-1"></i>Word Cloud
          </button>
          <button class="tm-switch-btn" onclick="TMApp.switchChart('bar')" id="btnBar">
            <i class="ph ph-chart-bar me-1"></i>Bar Chart
          </button>
          <button class="tm-switch-btn" onclick="TMApp.switchChart('pie')" id="btnPie">
            <i class="ph ph-chart-donut me-1"></i>Pie Chart
          </button>
        </div>
        {{-- Card export buttons --}}
        <div class="d-flex gap-1" data-html2canvas-ignore="true">
          <button class="card-exp-btn card-exp-btn-pdf"
                  onclick="WCExport.runCard('card-export-chart','chart','pdf',this)"
                  title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
          <button class="card-exp-btn card-exp-btn-img"
                  onclick="WCExport.runCard('card-export-chart','chart','image',this)"
                  title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
        </div>
      </div>
    </div>
    <div class="card-body p-3">

      {{-- Word Cloud --}}
      <div class="chart-view active" id="wordCloudView">
        <div class="wordcloud-container">
          <div class="tm-loading" id="wordCloudLoading"><div class="tm-spinner"></div><span>Loading topics…</span></div>
          <div id="wordCloudChart" style="display:none;"></div>
          <div class="wordcloud-hint" id="wordCloudHint"><i class="ph ph-cursor-click"></i> Hover to view details</div>
        </div>
      </div>

      {{-- Bar Chart --}}
      <div class="chart-view" id="barChartView">
        <div class="bar-chart-wrapper"><canvas id="barChart"></canvas></div>
      </div>

      {{-- Pie Chart --}}
      <div class="chart-view" id="pieChartView">
        <div class="pie-chart-wrapper"><canvas id="pieChart"></canvas></div>
      </div>

    </div>
    </div>{{-- /card-export-chart --}}
  </div>

  {{-- ══ Topic List Card ══ --}}
  <div class="card mb-3">
    <div id="card-export-topiclist">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-list-numbers f-18 text-primary"></i></div>
        <div>
          <h6 class="mb-0">Topic Details</h6>
          <small class="text-muted">Daftar topik beserta jumlah mention</small>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light-primary text-primary rounded-pill" id="topicCountBadge">Loading…</span>
        {{-- Card export buttons --}}
        <div class="d-flex gap-1" data-html2canvas-ignore="true">
          <button class="card-exp-btn card-exp-btn-pdf"
                  onclick="WCExport.runCard('card-export-topiclist','topiclist','pdf',this)"
                  title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
          <button class="card-exp-btn card-exp-btn-img"
                  onclick="WCExport.runCard('card-export-topiclist','topiclist','image',this)"
                  title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
        </div>
      </div>
    </div>
    <div class="card-body p-0" id="topicList">
      <div class="tm-loading"><div class="tm-spinner"></div><span>Memuat data…</span></div>
    </div>
   <div class="card-footer bg-transparent border-top" id="viewAllWrap" style="display:none;" data-html2canvas-ignore="true">
    <button class="btn btn-primary btn-sm w-100" onclick="TMApp.openModal()">
        <i class="ph ph-list me-1"></i>View All Topics
    </button>
</div>
    </div>{{-- /card-export-topiclist --}}
  </div>

  {{-- /pageExportArea --}}
  </div>

  {{-- Export Toast --}}
  <div class="export-toast" id="exportToast">
    <i class="ph ph-check-circle" id="exportToastIcon"></i>
    <span id="exportToastMsg">Exporting…</span>
  </div>

  {{-- ══ Modal ══ --}}
  <div class="tm-modal-overlay" id="tmModalOverlay" onclick="TMApp.closeModalOnOverlay(event)">
    <div class="tm-modal-box" onclick="event.stopPropagation()">
      <div class="tm-modal-head">
        <h5>All Topics</h5>
        <button class="tm-modal-close" onclick="TMApp.closeModal()"><i class="ph ph-x"></i></button>
      </div>
      <div class="tm-modal-body">
        <input type="text" class="tm-modal-search" id="modalSearch" placeholder="Search topics…">
        <div id="modalTopicList"></div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
          crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"
          crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/echarts-wordcloud@2.1.0/dist/echarts-wordcloud.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <script>
    'use strict';

    /* ══════════════════════════════════════════════════
       TMApp — tidak berubah
    ══════════════════════════════════════════════════ */
    const TMApp = (() => {
      const _urlP = new URLSearchParams(window.location.search);
      const projectId = _urlP.get('project_id');
      const startDate = _urlP.get('start_date') || '{{ $startDate }}';
      const endDate   = _urlP.get('end_date')   || '{{ $endDate }}';

      let topicsData    = [];
      let barChartInst  = null;
      let pieChartInst  = null;
      let wordCloudInst = null;
      let currentChart  = 'wordcloud';

      const $ = id => document.getElementById(id);
      const nF = n => parseInt(n || 0).toLocaleString('id-ID');
      function getPrimary() {
        return getComputedStyle(document.documentElement).getPropertyValue('--bs-primary').trim() || '#4361EE';
      }

      function init() {
        if (!projectId) { _showEmpty('Pilih project dari sidebar terlebih dahulu.'); return; }
        _load();
        const search = $('modalSearch');
        if (search) search.addEventListener('input', e => {
          const q = e.target.value.toLowerCase();
          document.querySelectorAll('#modalTopicList .topic-item').forEach(el => {
            el.style.display = (el.dataset.name || '').includes(q) ? '' : 'none';
          });
        });
      }

      async function _load() {
        try {
          const r = await fetch(`/mk/api/topic-map?project_id=${projectId}&media=all&start_date=${startDate}&end_date=${endDate}`);
          const j = await r.json();
          if (!j.success || !j.data?.length) { _showEmpty('Tidak ada topik untuk periode ini.'); return; }
          topicsData = j.data;
          _updateStats(topicsData);
          _renderWordCloud(topicsData);
          _renderList(topicsData);
        } catch (e) {
          console.error(e);
          _showEmpty('Gagal memuat data. Silakan coba lagi.');
        }
      }

      function _updateStats(topics) {
        $('statTotalTopics').textContent   = nF(topics.length);
        $('statTopTopic').textContent      = topics[0]?.name || '-';
        $('statTotalMentions').textContent = nF(topics.reduce((s, t) => s + t.count, 0));
        $('topicCountBadge').textContent   = topics.length + ' topics';
      }

      function switchChart(type) {
        document.querySelectorAll('.tm-switch-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.chart-view').forEach(v => v.classList.remove('active'));
        currentChart = type;
        const titleMap = { wordcloud:'Topic Word Cloud', bar:'Topic Bar Chart', pie:'Topic Distribution' };
        $('chartTitle').textContent = titleMap[type];
        if (type === 'wordcloud') {
          $('btnWordCloud').classList.add('active');
          $('wordCloudView').classList.add('active');
          if (topicsData.length) _renderWordCloud(topicsData);
        } else if (type === 'bar') {
          $('btnBar').classList.add('active');
          $('barChartView').classList.add('active');
          if (!barChartInst && topicsData.length) _renderBar(topicsData);
        } else if (type === 'pie') {
          $('btnPie').classList.add('active');
          $('pieChartView').classList.add('active');
          if (!pieChartInst && topicsData.length) _renderPie(topicsData);
        }
      }

      function _renderWordCloud(topics) {
        const chartDiv  = $('wordCloudChart');
        const loadingEl = $('wordCloudLoading');
        const hintEl    = $('wordCloudHint');
        if (!chartDiv || typeof echarts === 'undefined') return;
        if (!topics.length) { _showEmpty('Tidak ada topik.'); return; }
        loadingEl.style.display = 'none';
        chartDiv.style.display  = 'block';
        hintEl.style.display    = 'flex';
        const top      = topics.slice(0, 60);
        const maxCount = Math.max(...top.map(t => t.count));
        const minCount = Math.min(...top.map(t => t.count));
        const primary  = getPrimary();
        const colors   = [primary,'#10B981','#2FC6F6','#8b5cf6','#f59e0b','#ef4444','#06b6d4','#a78bfa','#34d399','#fbbf24'];
        const wordData = top.map(t => ({
          name  : t.name.replace(/^#/, ''),
          value : Math.pow((t.count - minCount) / (maxCount - minCount || 1), 0.5) * 1200 + 200,
          _topic: t,
        }));
        if (wordCloudInst) wordCloudInst.dispose();
        wordCloudInst = echarts.init(chartDiv, null, { renderer:'canvas', devicePixelRatio: window.devicePixelRatio || 1 });
        wordCloudInst.setOption({
          tooltip: {
            show:true, trigger:'item',
            backgroundColor:'#fff', borderColor:'#E2E8F0', borderWidth:1, padding:14,
            textStyle:{ color:'#0F172A', fontSize:12, fontFamily:'inherit' },
            shadowBlur:20, shadowColor:'rgba(0,0,0,.1)', shadowOffsetY:4,
            formatter: p => `<div style="font-family:inherit;min-width:160px;">
                              <div style="font-weight:700;font-size:14px;color:#0F172A;margin-bottom:6px;text-align:center;">${p.name}</div>
                              <div style="font-size:12px;color:#64748B;text-align:center;"><strong>${nF(p.data._topic.count)}</strong> mentions</div>
                            </div>`,
          },
          series:[{
            type:'wordCloud', shape:'circle', keepAspect:false, left:'center', top:'center',
            width:'98%', height:'98%', right:null, bottom:null,
            sizeRange:[24,120], rotationRange:[-45,45], rotationStep:45, gridSize:8,
            drawOutOfBound:false, layoutAnimation:true,
            textStyle:{ fontFamily:'Poppins, Inter, sans-serif', fontWeight:'bold', color:()=>colors[Math.floor(Math.random()*colors.length)] },
            emphasis:{ focus:'self', textStyle:{ textShadowBlur:10, textShadowColor:'rgba(0,0,0,0.35)' } },
            data: wordData,
          }],
        }, true);
        // Enable click on word cloud word
        wordCloudInst.off('click');
        wordCloudInst.on('click', function(params) {
          if (params && params.name) {
            const topic = encodeURIComponent(params.name);
            window.location.href = `/mk/topic-detail?topic=${topic}`;
          }
        });
        let rtimer;
        window.addEventListener('resize', () => {
          clearTimeout(rtimer);
          rtimer = setTimeout(() => { if (wordCloudInst) wordCloudInst.resize(); }, 250);
        });
      }

      function _renderBar(topics) {
        const ctx = $('barChart')?.getContext('2d');
        if (!ctx) return;
        if (barChartInst) barChartInst.destroy();
        const top     = topics.slice(0, 20);
        const primary = getPrimary();
        barChartInst = new Chart(ctx, {
          type:'bar',
          data:{ labels:top.map(t=>t.name), datasets:[{ label:'Mentions', data:top.map(t=>t.count), backgroundColor:primary+'CC', borderColor:primary, borderWidth:2, borderRadius:6 }] },
          options:{
            indexAxis:'y', responsive:true, maintainAspectRatio:false,
            plugins:{
              legend:{ display:false },
              tooltip:{ backgroundColor:'rgba(15,23,42,.95)', padding:12, titleFont:{size:13,weight:'bold',family:'inherit'}, bodyFont:{size:12,family:'inherit'}, borderColor:primary, borderWidth:1, callbacks:{ label:c=>`Mentions: ${nF(c.parsed.x)}` } }
            },
            scales:{
              x:{ beginAtZero:true, grid:{color:'rgba(226,232,240,.6)'}, ticks:{font:{weight:'600',family:'inherit',size:11}} },
              y:{ grid:{display:false}, ticks:{font:{weight:'600',size:12,family:'inherit'}} }
            }
          }
        });
      }

      function _renderPie(topics) {
        const ctx = $('pieChart')?.getContext('2d');
        if (!ctx) return;
        if (pieChartInst) pieChartInst.destroy();
        const top    = topics.slice(0, 10);
        const others = topics.slice(10).reduce((s,t)=>s+t.count, 0);
        const labels = [...top.map(t=>t.name)];
        const data   = [...top.map(t=>t.count)];
        if (others > 0) { labels.push('Others'); data.push(others); }
        const colors = ['#4361EE','#10B981','#2FC6F6','#8b5cf6','#f59e0b','#ef4444','#06b6d4','#3b82f6','#ec4899','#6366f1','#94a3b8'];
        pieChartInst = new Chart(ctx, {
          type:'doughnut',
          data:{ labels, datasets:[{ data, backgroundColor:colors, borderColor:'#fff', borderWidth:3, hoverOffset:14 }] },
          options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{
              legend:{ position:'right', labels:{ padding:18, font:{size:12,weight:'600',family:'inherit'}, generateLabels:c=>c.data.labels.map((l,i)=>({ text:`${l} (${nF(c.data.datasets[0].data[i])})`, fillStyle:c.data.datasets[0].backgroundColor[i], hidden:false, index:i })) } },
              tooltip:{ backgroundColor:'rgba(15,23,42,.95)', padding:12, titleFont:{size:13,weight:'bold',family:'inherit'}, bodyFont:{size:12,family:'inherit'}, borderColor:'#4361EE', borderWidth:1, callbacks:{ label:c=>{ const tot=c.dataset.data.reduce((a,b)=>a+b,0); return `${c.label}: ${nF(c.parsed)} (${((c.parsed/tot)*100).toFixed(1)}%)`; } } }
            }
          }
        });
      }

      function _renderList(topics) {
        const listEl      = $('topicList');
        const viewAllWrap = $('viewAllWrap');
        if (!listEl) return;
        if (!topics.length) {
          listEl.innerHTML = `<div class="tm-empty"><i class="ph ph-folder-open"></i><span>Tidak ada topik</span></div>`;
          viewAllWrap.style.display = 'none';
          return;
        }
        listEl.innerHTML = topics.slice(0, 10).map((t, i) => {
          const rank = i + 1;
          const topicUrl = `/mk/topic-detail?topic=${encodeURIComponent(t.name)}`;
          return `<div class="topic-item" style="cursor:pointer" onclick="window.location.href='${topicUrl}'"><span class="topic-rank${rank<=3?' top-3':''}">#${rank}</span><span class="topic-name">${t.name}</span><span class="topic-count">${nF(t.count)}</span></div>`;
        }).join('');
        viewAllWrap.style.display = topics.length > 10 ? '' : 'none';
      }

      function openModal() {
        const overlay = $('tmModalOverlay'), listEl = $('modalTopicList');
        listEl.innerHTML = topicsData.map((t, i) => {
          const rank = i + 1;
          const topicUrl = `/mk/topic-detail?topic=${encodeURIComponent(t.name)}`;
          return `<div class="topic-item" data-name="${t.name.toLowerCase()}" style="cursor:pointer" onclick="window.location.href='${topicUrl}'"><span class="topic-rank${rank<=3?' top-3':''}">#${rank}</span><span class="topic-name">${t.name}</span><span class="topic-count">${nF(t.count)}</span></div>`;
        }).join('');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        setTimeout(() => $('modalSearch')?.focus(), 100);
      }
      function closeModal() {
        $('tmModalOverlay').classList.remove('active');
        document.body.style.overflow = '';
        if ($('modalSearch')) $('modalSearch').value = '';
      }
      function closeModalOnOverlay(e) { if (e.target.id === 'tmModalOverlay') closeModal(); }

      function _showEmpty(msg) {
        const html = `<div class="tm-empty"><i class="ph ph-warning-circle"></i><span>${msg}</span></div>`;
        $('wordCloudLoading').innerHTML = html;
        $('topicList').innerHTML        = html;
        $('statTotalTopics').textContent   = '0';
        $('statTopTopic').textContent      = '—';
        $('statTotalMentions').textContent = '0';
        $('topicCountBadge').textContent   = '0 topics';
        $('viewAllWrap').style.display     = 'none';
      }

      function getInsts() { return { wordCloudInst, barChartInst, pieChartInst, currentChart }; }

      document.addEventListener('DOMContentLoaded', init);
      return { switchChart, openModal, closeModal, closeModalOnOverlay, getInsts, nF };
    })();

    /* ════════════════════════════════════════════════════════
       WCExport — Safari-compatible
       
       Pendekatan: ganti elemen di LIVE DOM (bukan onclone).
       Safari tidak bisa render canvas via html2canvas.
       Solusi: replace canvas/echarts-div → <img> di DOM nyata,
       jalankan html2canvas, lalu restore elemen asli.
    ════════════════════════════════════════════════════════ */
    const WCExport = (() => {
      const _urlP     = new URLSearchParams(window.location.search);
      const _pid      = _urlP.get('project_id') || '';
      let _toastTimer = null;

      /* ── Toast ── */
      function _toast(msg, type = 'default', duration = 3200) {
        const t   = document.getElementById('exportToast');
        const m   = document.getElementById('exportToastMsg');
        const ico = document.getElementById('exportToastIcon');
        if (!t || !m) return;
        m.textContent = msg;
        t.className   = 'export-toast show ' + (type !== 'default' ? type : '');
        ico.className = 'ph ' + ({ success:'ph-check-circle', error:'ph-x-circle', default:'ph-spinner' }[type] || 'ph-spinner');
        clearTimeout(_toastTimer);
        _toastTimer = setTimeout(() => t.classList.remove('show'), duration);
      }

      function _btnState(btn, loading) {
        if (!btn) return;
        btn.disabled = loading;
        btn.classList.toggle('exporting', loading);
      }

      /* ── Freeze CSS animations ── */
      let _freezeStyle = null;
      function _freeze() {
        if (_freezeStyle) return;
        _freezeStyle = document.createElement('style');
        _freezeStyle.id = '__wc_freeze__';
        _freezeStyle.textContent = `
          *, *::before, *::after {
            animation-play-state: paused !important;
            animation-duration: 0s !important;
            animation-delay: 0s !important;
            transition-duration: 0s !important;
            transition-delay: 0s !important;
          }
          .fade-up,.fade-up-d1,.fade-up-d2,.fade-up-d3 {
            opacity:1!important; transform:none!important; animation:none!important;
          }
          .sk-block { animation:none!important; background:#e2e8f0!important; }
        `;
        document.head.appendChild(_freezeStyle);
      }
      function _unfreeze() {
        if (_freezeStyle) { _freezeStyle.remove(); _freezeStyle = null; }
      }

      /* ─────────────────────────────────────────────────────
         _getWCSnapshot()
         Ambil dataURL dari ECharts word cloud.
         Harus dipanggil SEBELUM freeze/replace DOM.
      ───────────────────────────────────────────────────── */
      async function _getWCSnapshot() {
        const { wordCloudInst } = TMApp.getInsts();
        if (!wordCloudInst || wordCloudInst.isDisposed()) return null;
        try {
          wordCloudInst.setOption({ animation: false });
          wordCloudInst.resize();
          /* Tunggu word cloud selesai layout */
          await new Promise(r => setTimeout(r, 900));
          return wordCloudInst.getDataURL({ type:'png', pixelRatio:2, backgroundColor:'#ffffff' });
        } catch(e) {
          console.warn('[WCExport] WC snapshot failed:', e);
          return null;
        }
      }

      /* ─────────────────────────────────────────────────────
         _replaceCanvasesWithImages()
         Ganti canvas/div chart di LIVE DOM dengan <img>.
         Kembalikan array {node, parent, nextSibling}
         supaya bisa di-restore setelah capture.
      ───────────────────────────────────────────────────── */
      function _replaceCanvasesWithImages(wcSnapshot, currentChart) {
        const replacements = [];

        function _replace(originalEl, dataUrl, styleOverride) {
          if (!originalEl || !originalEl.parentNode) return;
          const img = document.createElement('img');
          img.src = dataUrl;
          img.style.cssText = styleOverride || 'width:100%;height:100%;display:block;object-fit:contain;';
          const nextSib = originalEl.nextSibling;
          const parent  = originalEl.parentNode;
          parent.replaceChild(img, originalEl);
          replacements.push({ original: originalEl, replacement: img, parent, nextSib });
        }

        /* 1. Word cloud (ECharts) */
        if (currentChart === 'wordcloud') {
          const wcDiv = document.getElementById('wordCloudChart');
          if (wcDiv && wcSnapshot) {
            _replace(wcDiv, wcSnapshot, 'width:100%;height:100%;position:absolute;top:0;left:0;display:block;object-fit:contain;');
          }
        }

        /* 2. Bar chart (Chart.js canvas) */
        if (currentChart === 'bar') {
          const barCanvas = document.getElementById('barChart');
          if (barCanvas) {
            try {
              const dataUrl = barCanvas.toDataURL('image/png');
              _replace(barCanvas, dataUrl, 'width:100%;height:100%;display:block;');
            } catch(e) { console.warn('[WCExport] bar canvas toDataURL failed:', e); }
          }
        }

        /* 3. Pie chart (Chart.js canvas) */
        if (currentChart === 'pie') {
          const pieCanvas = document.getElementById('pieChart');
          if (pieCanvas) {
            try {
              const dataUrl = pieCanvas.toDataURL('image/png');
              _replace(pieCanvas, dataUrl, 'width:100%;height:100%;display:block;');
            } catch(e) { console.warn('[WCExport] pie canvas toDataURL failed:', e); }
          }
        }

        return replacements;
      }

      /* ─────────────────────────────────────────────────────
         _restoreElements()
         Kembalikan elemen asli ke DOM.
      ───────────────────────────────────────────────────── */
      function _restoreElements(replacements) {
        for (const { original, replacement, parent, nextSib } of replacements) {
          try {
            if (replacement.parentNode === parent) {
              parent.replaceChild(original, replacement);
            } else if (nextSib && nextSib.parentNode === parent) {
              parent.insertBefore(original, nextSib);
            } else {
              parent.appendChild(original);
            }
          } catch(e) {
            console.warn('[WCExport] restore failed:', e);
          }
        }
      }

      /* ─────────────────────────────────────────────────────
         _hideIgnoredElements()
         Sembunyikan elemen data-html2canvas-ignore & UI noise
         di live DOM, return restore function.
      ───────────────────────────────────────────────────── */
      function _hideUIElements() {
        const selectors = [
          '[data-html2canvas-ignore]',
          '.wordcloud-hint',
          '#wordCloudLoading',
          '.tm-loading',
          '.tm-spinner',
          '.export-toast',
          '.tm-modal-overlay',
          '#viewAllWrap',
        ];
        const hidden = [];
        selectors.forEach(sel => {
          document.querySelectorAll(sel).forEach(el => {
            if (el.style.display !== 'none') {
              hidden.push({ el, prev: el.style.display });
              el.style.display = 'none';
            }
          });
        });
        return () => hidden.forEach(({ el, prev }) => { el.style.display = prev; });
      }

      /* ─────────────────────────────────────────────────────
         _capture() — inti export, Safari-compatible
      ───────────────────────────────────────────────────── */
      async function _capture(areaId, bgColor) {
        const area = document.getElementById(areaId);
        if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');

        const { currentChart } = TMApp.getInsts();

        /* 1. Scroll ke atas */
        window.scrollTo({ top: 0 });
        await new Promise(r => setTimeout(r, 200));

        /* 2. Ambil snapshot word cloud (async, sebelum modifikasi DOM) */
        const wcSnapshot = (currentChart === 'wordcloud') ? await _getWCSnapshot() : null;

        /* 3. Ganti canvas/div di live DOM dengan <img> */
        const domReplacements = _replaceCanvasesWithImages(wcSnapshot, currentChart);

        /* 4. Sembunyikan elemen UI yang tidak perlu */
        const restoreUI = _hideUIElements();

        /* 5. Freeze animasi */
        _freeze();

        /* 6. Tunggu browser repaint (Safari butuh lebih lama) */
        await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)));
        await new Promise(r => setTimeout(r, 400));

        let canvas;
        try {
          canvas = await html2canvas(area, {
            scale           : 2,
            useCORS         : true,
            allowTaint      : false,
            backgroundColor : bgColor || '#f1f5f9',
            logging         : false,
            removeContainer : true,
            /* TIDAK pakai onclone — itu yang bikin blank di Safari */
            /* windowHeight & height dari scrollHeight area */
            windowHeight    : area.scrollHeight,
            height          : area.scrollHeight,
          });
        } finally {
          /* 7. Restore semua perubahan DOM */
          _restoreElements(domReplacements);
          restoreUI();
          _unfreeze();

          /* 8. Restore ECharts animation */
          const { wordCloudInst } = TMApp.getInsts();
          if (wordCloudInst && !wordCloudInst.isDisposed()) {
            try { wordCloudInst.setOption({ animation: true }); } catch(e) {}
          }
        }

        return canvas;
      }

      /* ── PDF header ── */
      function _drawHeader(doc, pW, margin, subtitle) {
        doc.setFillColor(3, 128, 71);
        doc.rect(0, 0, pW, 11, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(9); doc.setFont('helvetica', 'bold');
        doc.text('SMADIMENT — Word Cloud' + (subtitle ? ' · ' + subtitle : ''), margin, 7.5);
        const now = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
        doc.setFontSize(7); doc.setFont('helvetica', 'normal');
        doc.text('Generated: ' + now, pW - margin, 7.5, { align: 'right' });
      }

      /* ── Fit canvas ke satu halaman ── */
      function _fitCanvas(pdf, canvas, margin) {
        const pW = pdf.internal.pageSize.getWidth();
        const pH = pdf.internal.pageSize.getHeight();
        const uw = pW - margin * 2;
        const uh = pH - 14 - 10;
        const r  = Math.min(uw / canvas.width, uh / canvas.height);
        pdf.addImage(canvas.toDataURL('image/png'), 'PNG',
          margin + (uw - canvas.width * r) / 2, 14, canvas.width * r, canvas.height * r);
      }

      /* ── Paginasi canvas (untuk konten panjang) ── */
      async function _paginateCanvas(pdf, canvas, margin) {
        const pW = pdf.internal.pageSize.getWidth();
        const pH = pdf.internal.pageSize.getHeight();
        const uw = pW - margin * 2;
        const uh = pH - 14 - 10;
        const ratio = uw / canvas.width;
        const sliceH = uh / ratio;
        let srcY = 0, pageNum = 0;
        while (srcY < canvas.height) {
          if (pageNum > 0) { pdf.addPage(); _drawHeader(pdf, pW, margin); }
          const srcSlice = Math.min(sliceH, canvas.height - srcY);
          const dstH = srcSlice * ratio;
          const slice = document.createElement('canvas');
          slice.width = canvas.width;
          slice.height = Math.ceil(srcSlice);
          slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcSlice, 0, 0, canvas.width, srcSlice);
          pdf.addImage(slice.toDataURL('image/png'), 'PNG', margin, 14, uw, dstH);
          pdf.setFontSize(7); pdf.setTextColor(148, 163, 184);
          pdf.text(`Halaman ${pageNum + 1}`, pW / 2, pH - 3, { align: 'center' });
          srcY += srcSlice; pageNum++;
        }
      }

      const _stamp = () => new Date().toISOString().slice(0, 10).replace(/-/g, '');

      /* ════════════
         Export full page
      ════════════ */
      async function run(type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

        const btnPdf = document.getElementById('pageExportPdfBtn');
        const btnImg = document.getElementById('pageExportImgBtn');
        _btnState(btnPdf, true); _btnState(btnImg, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF…' : 'Mengambil gambar…', 'default', 99999);

        try {
          const canvas = await _capture('pageExportArea', '#f1f5f9');
          const stamp  = _stamp();
          if (type === 'image') {
            const a = document.createElement('a');
            a.download = `wordcloud_${_pid}_${stamp}.png`;
            a.href = canvas.toDataURL('image/png');
            a.click();
            _toast('Gambar berhasil diunduh!', 'success');
          } else {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
            const pW  = pdf.internal.pageSize.getWidth();
            const pH  = pdf.internal.pageSize.getHeight();
            const uw  = pW - 20;
            const uh  = pH - 14 - 10;
            _drawHeader(pdf, pW, 10, 'Full Page');
            if ((canvas.height * (uw / canvas.width)) <= uh) {
              _fitCanvas(pdf, canvas, 10);
            } else {
              await _paginateCanvas(pdf, canvas, 10);
            }
            pdf.save(`wordcloud_${_pid}_${stamp}.pdf`);
            _toast('PDF berhasil diunduh!', 'success');
          }
        } catch(err) {
          console.error('[WCExport.run]', err);
          _toast('Export gagal: ' + err.message, 'error');
        } finally {
          _btnState(btnPdf, false); _btnState(btnImg, false);
        }
      }

      /* ════════════
         Export single card
      ════════════ */
      async function runCard(areaId, cardKey, type, btn) {
        if (!window.html2canvas)                    { _toast('html2canvas tidak tersedia', 'error'); return; }
        if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

        _btnState(btn, true);
        _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);

        const stamp      = _stamp();
        const cardLabels = { chart:'Topic Visualization', topiclist:'Topic Details' };
        const cardFiles  = { chart:'topic-chart', topiclist:'topic-list' };
        const label      = cardLabels[cardKey] || cardKey;
        const fname      = `wordcloud_${cardFiles[cardKey] || cardKey}_${_pid}_${stamp}`;

        try {
          const canvas = await _capture(areaId, '#ffffff');
          if (type === 'image') {
            const a = document.createElement('a');
            a.download = fname + '.png';
            a.href = canvas.toDataURL('image/png');
            a.click();
            _toast('Gambar berhasil diunduh!', 'success');
          } else {
            const { jsPDF } = window.jspdf;
            const landscape = canvas.width > canvas.height * 1.3;
            const pdf = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit:'mm', format:'a4' });
            const pW  = pdf.internal.pageSize.getWidth();
            const pH  = pdf.internal.pageSize.getHeight();
            const uw  = pW - 20;
            const uh  = pH - 14 - 10;
            if ((canvas.height * (uw / canvas.width)) <= uh) {
              _fitCanvas(pdf, canvas, 10);
            } else {
              await _paginateCanvas(pdf, canvas, 10);
            }
            pdf.save(fname + '.pdf');
            _toast('PDF berhasil diunduh!', 'success');
          }
        } catch(err) {
          console.error('[WCExport.runCard]', err);
          _toast('Export gagal: ' + err.message, 'error');
        } finally {
          _btnState(btn, false);
        }
      }

      return { run, runCard };
    })();
  </script>
@endsection