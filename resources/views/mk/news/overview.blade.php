@extends('mk.layouts.app')
@section('title', 'News Overview - SMADIMENT')

@section('styles')
<style>
:root{--nv-primary:#4361EE;--nv-green:#10B981;--nv-red:#EF4444;--nv-slate:#94A3B8;--nv-dark:#0F172A;--radius:8px}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes spin{to{transform:rotate(360deg)}}
.fade-up{animation:fadeUp .38s ease-out both}
.fade-up-d1{animation-delay:.05s}.fade-up-d2{animation-delay:.1s}.fade-up-d3{animation-delay:.15s}.fade-up-d4{animation-delay:.2s}
.kpi-icon-bg{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.2);font-size:24px;color:#fff;flex-shrink:0}
.nv-spinner{width:28px;height:28px;border:3px solid #e2e8f0;border-top-color:var(--nv-primary);border-radius:50%;animation:spin .7s linear infinite}
.nv-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;padding:50px 20px;color:var(--nv-slate);font-size:12px;font-weight:600}
.nv-empty{display:flex;flex-direction:column;align-items:center;gap:8px;padding:50px;color:var(--nv-slate);font-size:12px;text-align:center}
.nv-empty i{font-size:36px;color:#cbd5e1}
.pub-row{display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid #f1f5f9;transition:background .12s;cursor:pointer;text-decoration:none}
.pub-row:last-child{border-bottom:none}
.pub-row:hover{background:#f0f5ff;padding-left:20px}
.pub-rank{font-size:11px;font-weight:800;color:var(--nv-slate);min-width:24px}
.pub-rank.top{color:var(--nv-primary)}
.pub-name{flex:1;font-size:13px;font-weight:600;color:var(--nv-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pub-row:hover .pub-name{color:var(--nv-primary)}
.pub-count{background:rgba(67,97,238,.1);color:var(--nv-primary);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.pub-ext{font-size:12px;color:#cbd5e1;transition:color .12s;margin-left:4px}
.pub-row:hover .pub-ext{color:var(--nv-primary)}
.article-item{display:block;padding:14px 16px;border-bottom:1px solid #f1f5f9;transition:background .12s,padding-left .12s;cursor:pointer;text-decoration:none;color:inherit}
.article-item:hover{background:#f8fafc;padding-left:20px;color:inherit;text-decoration:none}
.article-item:last-child{border-bottom:none}
.article-title{font-size:13px;font-weight:700;color:var(--nv-dark);margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.5}
.article-item:hover .article-title{color:var(--nv-primary)}
.article-meta{font-size:11px;color:var(--nv-slate);display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.article-meta i{font-size:13px}
.sent-badge{padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;display:inline-flex;align-items:center;gap:3px}
.sent-pos{background:#dcfce7;color:#15803d}
.sent-neg{background:#fef2f2;color:#dc2626}
.sent-neu{background:#f1f5f9;color:#64748b}

/* ══ EXPORT STYLES ══ */
.page-export-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:9px 14px; margin-bottom:20px; box-shadow:0 1px 2px rgba(0,0,0,0.05); }
.page-export-bar-left { display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:#475569; }
.page-export-bar-left svg { width:16px; height:16px; stroke:#4361EE; fill:none; stroke-width:2.5; }
.page-export-bar-right { display:flex; gap:8px; }
.page-export-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:6px; font-size:16px; cursor:pointer; transition:all .15s ease; border:1.5px solid transparent; font-family:inherit; }
.page-export-btn-pdf { background:#fff3f3; color:#dc2626; border-color:#fca5a5; }
.page-export-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.page-export-btn-img { background:rgba(67,97,238,.08); color:#4361EE; border-color:rgba(67,97,238,.2); }
.page-export-btn-img:hover { background:#4361EE; color:#fff; border-color:#4361EE; }
.page-export-btn:disabled { opacity:.55; cursor:not-allowed; pointer-events:none; }
.page-export-btn .export-spinner { width:13px; height:13px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
.page-export-btn.exporting .export-spinner { display:inline-block; }
.page-export-btn.exporting .export-icon { display:none; }
.card-exp-btn { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:6px; font-size:14px; cursor:pointer; flex-shrink:0; transition:all .14s ease; border:1px solid transparent; font-family:inherit; background:transparent; }
.card-exp-btn-pdf { color:#dc2626; border-color:#fca5a5; background:#fff3f3; }
.card-exp-btn-pdf:hover { background:#dc2626; color:#fff; border-color:#dc2626; }
.card-exp-btn-img { color:#4361EE; border-color:rgba(67,97,238,.2); background:rgba(67,97,238,.08); }
.card-exp-btn-img:hover { background:#4361EE; color:#fff; border-color:#4361EE; }
.card-exp-btn:disabled { opacity:.45; cursor:not-allowed; pointer-events:none; }
.card-exp-btn .export-spinner { width:11px; height:11px; border:2px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .65s linear infinite; display:none; }
.card-exp-btn.exporting .export-spinner { display:inline-block; }
.card-exp-btn.exporting .export-icon { display:none; }
.export-toast { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px); background:#0F172A; color:#fff; border-radius:6px; padding:10px 18px; font-size:12px; font-weight:600; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1),0 4px 6px -2px rgba(0,0,0,0.05); z-index:99999; opacity:0; pointer-events:none; transition:opacity .22s ease, transform .22s ease; display:flex; align-items:center; gap:8px; white-space:nowrap; }
.export-toast.show    { opacity:1; transform:translateX(-50%) translateY(0); }
.export-toast.success { background:#10B981; }
.export-toast.error   { background:#dc2626; }
</style>
@endsection

@section('page-title', 'News Overview')

@section('content')
@include('mk.layouts.partials.filter-datepicker')

{{-- ════ PAGE EXPORT WRAPPER ════ --}}
<div id="pageExportArea">

{{-- ══ Page Export Toolbar ══ --}}
<div class="page-export-bar" data-html2canvas-ignore="true">
  <div class="page-export-bar-left">
    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
    <span style="font-weight:700;">Export Halaman</span>
    <span class="badge bg-light-secondary text-muted ms-1" style="font-size:10px;">PDF 2 Hal · PNG</span>
  </div>
  <div class="page-export-bar-right">
    <button type="button" class="page-export-btn page-export-btn-pdf" id="pageExportPdfBtn"
            onclick="NVExport.run('pdf', this)" title="Export PDF 2 halaman">
      <i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span>
    </button>
    <button type="button" class="page-export-btn page-export-btn-img" id="pageExportImgBtn"
            onclick="NVExport.run('image', this)" title="Export PNG">
      <i class="ph ph-image export-icon"></i><span class="export-spinner"></span>
    </button>
  </div>
</div>

{{-- ════ HALAMAN 1 EXPORT ════ --}}
<div id="exportPage1">

{{-- KPI Cards - warna sama dengan Mention page --}}
<div class="row mb-3">
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 text-white fade-up fade-up-d1" style="background:#06B6D4">
      <div class="card-body py-3"><div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <p class="mb-1 text-white text-opacity-75 f-12">Positive</p>
          <h3 class="mb-0 text-white f-w-300" id="kpiPos">—</h3>
          <p class="mb-0 mt-1 text-white text-opacity-75 f-12" id="kpiPosSub"><i class="ph ph-chart-line-up me-1"></i>Loading...</p>
        </div>
        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley"></i></div></div>
      </div></div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 text-white fade-up fade-up-d2" style="background:#F59E0B">
      <div class="card-body py-3"><div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <p class="mb-1 text-white text-opacity-75 f-12">Negative</p>
          <h3 class="mb-0 text-white f-w-300" id="kpiNeg">—</h3>
          <p class="mb-0 mt-1 text-white text-opacity-75 f-12" id="kpiNegSub"><i class="ph ph-smiley-sad me-1"></i>Loading...</p>
        </div>
        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-sad"></i></div></div>
      </div></div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 text-white fade-up fade-up-d3" style="background:#4CAF50">
      <div class="card-body py-3"><div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <p class="mb-1 text-white text-opacity-75 f-12">Neutral</p>
          <h3 class="mb-0 text-white f-w-300" id="kpiNeu">—</h3>
          <p class="mb-0 mt-1 text-white text-opacity-75 f-12" id="kpiNeuSub"><i class="ph ph-smiley-meh me-1"></i>Loading...</p>
        </div>
        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-smiley-meh"></i></div></div>
      </div></div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card h-100 text-white fade-up fade-up-d4" style="background:#038047">
      <div class="card-body py-3"><div class="d-flex align-items-center">
        <div class="flex-grow-1">
          <p class="mb-1 text-white text-opacity-75 f-12">Total News</p>
          <h3 class="mb-0 text-white f-w-300" id="kpiTotal">—</h3>
          <p class="mb-0 mt-1 text-white text-opacity-75 f-12"><i class="ph ph-newspaper me-1"></i>Online News Mentions</p>
        </div>
        <div class="flex-shrink-0 ms-3"><div class="kpi-icon-bg"><i class="ph ph-chart-bar"></i></div></div>
      </div></div>
    </div>
  </div>
</div>

{{-- Trend Chart --}}
<div class="card mb-3 fade-up fade-up-d2" id="card-export-trend">
  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-chart-line f-18 text-primary"></i></div>
      <div><h6 class="mb-0">Mention Trend</h6><small class="text-muted">Tren mention berita online harian</small></div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-light-primary text-primary rounded-pill" id="trendBadge">Loading…</span>
      <div class="d-flex gap-1" data-html2canvas-ignore="true">
        <button class="card-exp-btn card-exp-btn-pdf" onclick="NVExport.runCard('card-export-trend','trend','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
        <button class="card-exp-btn card-exp-btn-img" onclick="NVExport.runCard('card-export-trend','trend','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
      </div>
    </div>
  </div>
  <div class="card-body p-3">
    <div class="nv-loading" id="trendLoading"><div class="nv-spinner"></div><span>Memuat trend…</span></div>
    <div id="trendChart" style="display:none;min-height:320px"></div>
  </div>
</div>

<div class="row mb-3">
  {{-- Sentiment Donut --}}
  <div class="col-lg-12 fade-up fade-up-d3">
    <div class="card h-100" id="card-export-donut">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-chart-donut f-18 text-primary"></i></div>
          <div><h6 class="mb-0">Sentiment Distribution</h6><small class="text-muted">Proporsi sentimen berita</small></div>
        </div>
        <div class="d-flex gap-1" data-html2canvas-ignore="true">
          <button class="card-exp-btn card-exp-btn-pdf" onclick="NVExport.runCard('card-export-donut','donut','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
          <button class="card-exp-btn card-exp-btn-img" onclick="NVExport.runCard('card-export-donut','donut','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="nv-loading" id="donutLoading"><div class="nv-spinner"></div><span>Memuat…</span></div>
        <div id="donutChart" style="display:none;min-height:300px"></div>
        <div class="d-flex justify-content-center gap-4 mt-2" id="donutLegend" style="display:none!important">
          <span style="font-size:12px;font-weight:600;color:#64748b;display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#10B981;display:inline-block"></span>Positive</span>
          <span style="font-size:12px;font-weight:600;color:#64748b;display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#EF4444;display:inline-block"></span>Negative</span>
          <span style="font-size:12px;font-weight:600;color:#64748b;display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#94A3B8;display:inline-block"></span>Neutral</span>
        </div>
      </div>
    </div>
  </div>
</div>

</div>{{-- /exportPage1 --}}

{{-- ════ HALAMAN 2 EXPORT ════ --}}
<div id="exportPage2">

<div class="row mb-3">
  {{-- Top Publishers --}}
  <div class="col-lg-12 fade-up fade-up-d4">
    <div class="card h-100" id="card-export-pub">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-buildings f-18 text-primary"></i></div>
          <div><h6 class="mb-0">Top Publishers</h6><small class="text-muted">Klik untuk buka website</small></div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-light-primary text-primary rounded-pill" id="pubBadge">Loading…</span>
          <div class="d-flex gap-1" data-html2canvas-ignore="true">
            <button class="card-exp-btn card-exp-btn-pdf" onclick="NVExport.runCard('card-export-pub','publishers','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
            <button class="card-exp-btn card-exp-btn-img" onclick="NVExport.runCard('card-export-pub','publishers','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
          </div>
        </div>
      </div>
      <div class="card-body p-0" id="pubList" style="max-height:380px;overflow-y:auto">
        <div class="nv-loading"><div class="nv-spinner"></div><span>Memuat…</span></div>
      </div>
    </div>
  </div>
</div>

{{-- Recent Articles --}}
<div class="card mb-3 fade-up fade-up-d4" id="card-export-art">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div class="avtar avtar-xs bg-light-primary rounded-circle"><i class="ph ph-article f-18 text-primary"></i></div>
      <div><h6 class="mb-0">Recent Articles</h6><small class="text-muted">Artikel berita terbaru</small></div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-light-primary text-primary rounded-pill" id="artBadge">Loading…</span>
      <div class="d-flex gap-1" data-html2canvas-ignore="true">
        <button class="card-exp-btn card-exp-btn-pdf" onclick="NVExport.runCard('card-export-art','articles','pdf',this)" title="Export PDF"><i class="ph ph-file-pdf export-icon"></i><span class="export-spinner"></span></button>
        <button class="card-exp-btn card-exp-btn-img" onclick="NVExport.runCard('card-export-art','articles','image',this)" title="Export PNG"><i class="ph ph-image export-icon"></i><span class="export-spinner"></span></button>
      </div>
    </div>
  </div>
  <div class="card-body p-0" id="artList">
    <div class="nv-loading"><div class="nv-spinner"></div><span>Memuat artikel…</span></div>
  </div>
</div>

</div>{{-- /exportPage2 --}}

{{-- /pageExportArea --}}
</div>

{{-- Export Toast --}}
<div class="export-toast" id="exportToast">
  <i class="ph ph-check-circle" id="exportToastIcon"></i>
  <span id="exportToastMsg">Exporting…</span>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
'use strict';

/* ══ SAFARI DETECTION ══ */
const _isSafari = (function () {
  const ua = navigator.userAgent;
  return /^((?!chrome|android).)*safari/i.test(ua);
})();
const NV = (() => {
  const _p = new URLSearchParams(location.search);
  const pid = _p.get('project_id') || '{{ $projectId }}';
  const sd  = _p.get('start_date') || '{{ $startDate }}';
  const ed  = _p.get('end_date')   || '{{ $endDate }}';
  const $ = id => document.getElementById(id);
  const nF = n => parseInt(n||0).toLocaleString('id-ID');
  const nK = n => { n=parseInt(n||0); if(n>=1e6)return(n/1e6).toFixed(1)+'M'; if(n>=1e3)return(n/1e3).toFixed(1)+'k'; return n.toString(); };
  let _apex = null, _eChart = null, _trendTotal = 0;

  async function init() {
    if (!pid) { $('trendLoading').innerHTML='<div class="nv-empty"><i class="ph ph-folder-open"></i><span>Pilih project terlebih dahulu</span></div>'; return; }
    await Promise.all([_loadTrend(), _loadArticles(), _loadPublishers()]);
  }

  async function _loadTrend() {
    try {
      const r = await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${pid}&start_date=${sd}&end_date=${ed}`);
      const j = await r.json();
      const raw = j.data || [];
      // Hitung total dari trend API
      let total = 0;
      raw.forEach(p => { (p.data||[]).forEach(pt => { total += pt.count || 0; }); });
      _trendTotal = total;
      if ($('kpiTotal') && _trendTotal > 0) $('kpiTotal').textContent = nF(_trendTotal);
      _renderTrend(raw);
    } catch(e) { console.warn('Trend failed', e); $('trendLoading').innerHTML='<div class="nv-empty"><i class="ph ph-warning"></i><span>Gagal memuat trend</span></div>'; }
  }

  async function _loadArticles() {
    try {
      const r = await fetch(`/mk/api/news/articles?project_id=${pid}&start_date=${sd}&end_date=${ed}&media=doc&rows=100`);
      const j = await r.json();
      const arts = j.data || [];
      _updateKPIs(arts);
      _renderDonut(arts);
      _renderArticleList(arts);
    } catch(e) { console.warn('Articles failed', e); }
  }

  async function _loadPublishers() {
    try {
      const r = await fetch(`/mk/api/news/top-publisher?project_id=${pid}&start_date=${sd}&end_date=${ed}`);
      const j = await r.json();
      _renderPubs(j.data || []);
    } catch(e) { $('pubList').innerHTML='<div class="nv-empty"><i class="ph ph-warning"></i><span>Gagal memuat</span></div>'; }
  }

  function _getSent(a) {
    const s = (a.sentiment || a.sentiment_class || '').toLowerCase();
    if (s.includes('positif') || s.includes('positive') || s === 'pos') return 'pos';
    if (s.includes('negatif') || s.includes('negative') || s === 'neg') return 'neg';
    return 'neu';
  }

  function _updateKPIs(arts) {
    const pos = arts.filter(a => _getSent(a)==='pos').length;
    const neg = arts.filter(a => _getSent(a)==='neg').length;
    const neu = arts.length - pos - neg;
    const pct = v => arts.length > 0 ? ((v/arts.length)*100).toFixed(1) : '0.0';
    // Scale dengan trendTotal
    const realTotal = _trendTotal > 0 ? _trendTotal : arts.length;
    const posReal = arts.length > 0 ? Math.round((pos/arts.length)*realTotal) : 0;
    const negReal = arts.length > 0 ? Math.round((neg/arts.length)*realTotal) : 0;
    const neuReal = arts.length > 0 ? Math.round((neu/arts.length)*realTotal) : 0;
    if (_trendTotal <= 0) $('kpiTotal').textContent = nF(arts.length);
    $('kpiPos').textContent = nF(posReal);
    $('kpiNeg').textContent = nF(negReal);
    $('kpiNeu').textContent = nF(neuReal);
    $('kpiPosSub').innerHTML = '<i class="ph ph-chart-line-up me-1"></i>' + pct(pos) + '% of total';
    $('kpiNegSub').innerHTML = '<i class="ph ph-smiley-sad me-1"></i>' + pct(neg) + '% of total';
    $('kpiNeuSub').innerHTML = '<i class="ph ph-smiley-meh me-1"></i>' + pct(neu) + '% of total';
  }

  function _renderTrend(raw) {
    const curr = new Date(sd), end = new Date(ed), dates = [];
    while (curr <= end) { const y=curr.getFullYear(),m=String(curr.getMonth()+1).padStart(2,'0'),d=String(curr.getDate()).padStart(2,'0'); dates.push(`${y}-${m}-${d}`); curr.setDate(curr.getDate()+1); }
    if (!dates.length) { $('trendLoading').style.display='none'; return; }
    const xLabels = dates.map(ds => { const[,m,d]=ds.split('-'); return parseInt(d)+'/'+parseInt(m); });
    const totVals = new Array(dates.length).fill(0);
    if (raw.length) {
      const docEntry = raw.find(p => ['doc','online_news','news'].includes((p.key||'').toLowerCase()));
      if (docEntry) { (docEntry.data||[]).forEach(pt => { const i=dates.indexOf(pt.date); if(i>=0)totVals[i]=pt.count||0; }); }
      else { raw.forEach(p => { (p.data||[]).forEach(pt => { const i=dates.indexOf(pt.date); if(i>=0)totVals[i]+=pt.count||0; }); }); }
    }
    const el = $('trendChart'); if (!el) return;
    if (_apex) { try{_apex.destroy();}catch(e){} }
    el.style.display = 'block';
    _apex = new ApexCharts(el, {
      chart:{type:'area',height:320,fontFamily:'inherit',background:'transparent',toolbar:{show:false},animations:{enabled:true,easing:'linear',dynamicAnimation:{speed:800}}},
      series:[{name:'Mentions',data:totVals}],
      colors:['#4361EE'], fill:{opacity:0.2}, stroke:{curve:'smooth',width:2.5},
      xaxis:{categories:xLabels,axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontFamily:'inherit',fontSize:'11px',fontWeight:600,colors:'#94A3B8'}}},
      yaxis:{labels:{formatter:v=>nK(v),style:{fontFamily:'inherit',fontSize:'10px',fontWeight:600,colors:'#94A3B8'}},axisBorder:{show:false}},
      markers:{size:4,strokeWidth:2,strokeColors:'#fff',hover:{size:6}},
      dataLabels:{enabled:true,formatter:v=>v>0?nK(v):'',style:{fontSize:'10px',fontFamily:'inherit',fontWeight:'800'},background:{enabled:true,borderRadius:3,borderWidth:0,padding:3,opacity:.9},offsetY:-8},
      grid:{borderColor:'rgba(226,232,240,.55)',strokeDashArray:3,xaxis:{lines:{show:false}},padding:{top:10,right:10,left:10,bottom:0}},
      legend:{show:false},
      tooltip:{style:{fontFamily:'inherit',fontSize:'12px'},y:{formatter:v=>nF(v)+' mentions'}},
    });
    _apex.render();
    const fmtB=ds=>{const[y,m,d]=ds.split('-');const dt=new Date(parseInt(y),parseInt(m)-1,parseInt(d));return parseInt(d)+' '+dt.toLocaleString('id-ID',{month:'short'});};
    $('trendBadge').textContent=fmtB(dates[0])+' - '+fmtB(dates[dates.length-1]);
    $('trendLoading').style.display='none';
  }

  function _renderDonut(arts) {
    const pos=arts.filter(a=>_getSent(a)==='pos').length, neg=arts.filter(a=>_getSent(a)==='neg').length, neu=arts.length-pos-neg, tot=pos+neg+neu;
    const el=$('donutChart');
    if(!el||!tot){$('donutLoading').innerHTML='<div class="nv-empty"><i class="ph ph-chart-donut"></i><span>Tidak ada data</span></div>';return;}
    el.style.display='block'; $('donutLoading').style.display='none'; $('donutLegend').style.cssText='display:flex!important';
    if(_eChart)_eChart.dispose();
    _eChart=echarts.init(el,null,{renderer:'svg'});
    const displayTotal = _trendTotal > 0 ? _trendTotal : tot;
    _eChart.setOption({
      animation:true,animationDuration:800,backgroundColor:'transparent',
      tooltip:{trigger:'item',backgroundColor:'#fff',borderColor:'#e2e8f0',borderWidth:1,padding:12,textStyle:{color:'#0f172a',fontSize:12,fontFamily:'inherit'},
        formatter:p=>{const pc=tot>0?((p.value/tot)*100).toFixed(1):'0';return `<b>${p.name}</b><br/>Mentions: ${nF(p.value)} (${pc}%)`;}
      },
      legend:{show:false},
      series:[{
        type:'pie',radius:['36%','54%'],center:['50%','45%'],
        avoidLabelOverlap:true,minAngle:8,
        itemStyle:{borderColor:'#fff',borderWidth:3,borderRadius:5},
        label:{show:true,alignTo:'edge',edgeDistance:12,lineHeight:20,fontFamily:"'Poppins',sans-serif",fontSize:11,
          formatter:p=>{const pc=tot>0?(p.value/tot*100):0;if(pc<2)return'';return`{name|${p.name}}\n{pct|${pc.toFixed(1)}%}`;},
          rich:{name:{fontWeight:'700',fontSize:11,color:'#1a202c',lineHeight:20},pct:{fontWeight:'700',fontSize:10,color:'#038047',lineHeight:17,backgroundColor:'#edf7f3',borderRadius:4,padding:[2,6]}}
        },
        labelLine:{show:true,length:16,length2:20,smooth:.3,lineStyle:{color:'#c4cdd8',width:1.2}},
        emphasis:{scale:true,scaleSize:5},
        data:[
          {name:'Positive',value:pos,itemStyle:{color:'#10B981'}},
          {name:'Negative',value:neg,itemStyle:{color:'#EF4444'}},
          {name:'Neutral',value:neu,itemStyle:{color:'#94A3B8'}},
        ].filter(d=>d.value>0)
      }],
      graphic:[
        {type:'text',left:'center',top:'38%',z:100,style:{text:nK(displayTotal),fill:'#0f172a',font:"800 17px 'Poppins',sans-serif",textAlign:'center'}},
        {type:'text',left:'center',top:'48%',z:100,style:{text:'TOTAL',fill:'#94a3b8',font:"600 8px 'Poppins',sans-serif",textAlign:'center'}},
      ]
    });
    window.addEventListener('resize',()=>{if(_eChart)try{_eChart.resize();}catch(e){}});
  }

  function _renderPubs(pubs) {
    const el=$('pubList');
    if(!pubs.length){el.innerHTML='<div class="nv-empty"><i class="ph ph-buildings"></i><span>Tidak ada data publisher</span></div>';$('pubBadge').textContent='0';return;}
    const top=pubs.slice(0,10);
    el.innerHTML=top.map((p,i)=>{
      const rank=i+1;
      const domain=p.domain||'unknown';
      const url='https://'+domain.replace(/^www\./,'');
      return `<a href="${url}" target="_blank" rel="noopener" class="pub-row">
        <span class="pub-rank${rank<=3?' top':''}">#${rank}</span>
        <span class="pub-name">${domain}</span>
        <span class="pub-count">${nF(p.count)} articles</span>
        <i class="ph ph-arrow-square-out pub-ext"></i>
      </a>`;
    }).join('');
    $('pubBadge').textContent=pubs.length+' publishers';
  }

  function _renderArticleList(arts) {
    const el=$('artList');
    if(!arts.length){el.innerHTML='<div class="nv-empty"><i class="ph ph-article"></i><span>Tidak ada artikel</span></div>';$('artBadge').textContent='0';return;}
    const top=arts.slice(0,20);
    el.innerHTML=top.map(a=>{
      const sent=_getSent(a);
      const sentLabel=sent==='pos'?'Positive':sent==='neg'?'Negative':'Neutral';
      const sentClass=sent==='pos'?'sent-pos':sent==='neg'?'sent-neg':'sent-neu';
      const sentIcon=sent==='pos'?'ph-smiley':sent==='neg'?'ph-smiley-sad':'ph-smiley-meh';
      const date=a.date_created?new Date(a.date_created).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}):'';
      const url=a.url||a.link||'#';
      const publisher=a.publisher||a.hostname||'Unknown';
      return `<a href="${url}" target="_blank" rel="noopener" class="article-item">
        <div class="article-title">${a.title||'Untitled'}</div>
        <div class="article-meta">
          <span><i class="ph ph-globe me-1"></i>${publisher}</span>
          <span><i class="ph ph-calendar-blank me-1"></i>${date}</span>
          <span class="sent-badge ${sentClass}"><i class="ph ${sentIcon}"></i> ${sentLabel}</span>
        </div>
      </a>`;
    }).join('');
    $('artBadge').textContent=arts.length+' articles';
  }

  document.addEventListener('DOMContentLoaded',init);
  return{init, getApex() { return _apex; }, getEChart() { return _eChart; }, getProjectId() { return pid; } };
})();

/* ══════════════════════════════════════════════════════
   NVExport — PDF & IMAGE EXPORT ENGINE (Standardized for Chrome & Safari)
══════════════════════════════════════════════════════ */
const NVExport = (() => {
    function _stamp() {
        const d = new Date();
        return `${d.getFullYear()}${String(d.getMonth()+1).padStart(2,'0')}${String(d.getDate()).padStart(2,'0')}_${String(d.getHours()).padStart(2,'0')}${String(d.getMinutes()).padStart(2,'0')}`;
    }

    function _toast(msg, type='success', duration=3500) {
        const t = document.getElementById('exportToast');
        if (!t) return;
        const msgEl = document.getElementById('exportToastMsg');
        const iconEl = document.getElementById('exportToastIcon');
        if (msgEl) msgEl.textContent = msg;

        t.className = 'export-toast show ' + type;
        if (iconEl) {
            iconEl.className = type === 'success' ? 'ph ph-check-circle' :
                               type === 'error' ? 'ph ph-x-circle' : 'ph ph-spinner spin';
        }
        
        clearTimeout(t._tm);
        if (duration < 99999) {
            t._tm = setTimeout(() => { t.classList.remove('show'); }, duration);
        }
    }

    function _btnState(btn, active) {
        const btns = Array.isArray(btn) ? btn : [btn];
        btns.forEach(b => {
            if (!b) return;
            if (active) {
                b.classList.add('exporting');
                b.setAttribute('disabled', 'true');
            } else {
                b.classList.remove('exporting');
                b.removeAttribute('disabled');
            }
        });
    }

    async function _getEChartSnapshot() {
        const chart = NV.getEChart();
        if (!chart || chart.isDisposed()) return null;
        try {
            chart.setOption({ animation: false });
            const dataUrl = chart.getDataURL({ type: 'png', pixelRatio: 2, backgroundColor: '#ffffff' });
            chart.setOption({ animation: true });
            return dataUrl;
        } catch(e) {
            console.warn('[EChartSnapshot] Gagal ambil snapshot donutChart', e);
            return null;
        }
    }

    async function _getApexSnapshot() {
        const chart = NV.getApex();
        if (!chart) return null;
        try {
            chart.updateOptions({
                fill: { opacity: 1, type: 'solid' },
                chart: { animations: { enabled: false } }
            }, false, false, false);
            await new Promise(r => setTimeout(r, 400));
            const result = await chart.dataURI({ scale: 2 });
            return result?.imgURI || null;
        } catch(e) {
            console.warn('[ApexSnapshot] Gagal ambil snapshot trendChart', e);
            return null;
        } finally {
            try {
                chart.updateOptions({
                    fill: { opacity: 0.2, type: 'solid' },
                    chart: { animations: { enabled: true } }
                }, false, false, false);
            } catch(e) {}
        }
    }

    async function _doCapture(element, bg, ecSnap, apexSnap) {
        const replacements = [];
        
        // Replace ECharts donutChart if present in element
        const donutDom = document.getElementById('donutChart');
        if (donutDom && element.contains(donutDom) && ecSnap) {
            const canvas = donutDom.querySelector('canvas');
            if (canvas) {
                const img = document.createElement('img');
                img.src = ecSnap;
                img.style.cssText = `position:absolute;top:0;left:0;width:100%;height:100%;z-index:9999;background:${bg};`;
                canvas.parentElement.appendChild(img);
                replacements.push({ parent: canvas.parentElement, img });
            }
        }

        // Replace ApexCharts trendChart if present in element
        const trendDom = document.getElementById('trendChart');
        if (trendDom && element.contains(trendDom) && apexSnap) {
            const chartWrap = trendDom.querySelector('.apexcharts-canvas');
            if (chartWrap) {
                const img = document.createElement('img');
                img.src = apexSnap;
                img.style.cssText = `position:absolute;top:0;left:0;width:100%;height:100%;z-index:9999;background:${bg};object-fit:contain;`;
                chartWrap.parentElement.appendChild(img);
                replacements.push({ parent: chartWrap.parentElement, img });
            }
        }

        const opt = {
            backgroundColor: bg,
            scale: 2,
            useCORS: true,
            allowTaint: true,
            logging: false,
            windowWidth: 1200,
            ignoreElements: el => el.hasAttribute('data-html2canvas-ignore') || el.classList.contains('page-export-bar')
        };

        try {
            return await html2canvas(element, opt);
        } finally {
            replacements.forEach(r => { r.parent.removeChild(r.img); });
        }
    }

    function _addCanvasAsPage(pdf, canvas, margin, pW, pH, label, curPg, totPg) {
        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        const uw = pW - margin * 2;
        const uh = pH - 14 - 8;
        const cw = canvas.width;
        const ch = canvas.height;
        const imgH = ch * (uw / cw);

        pdf.setFillColor('#f8fafc');
        pdf.rect(0, 0, pW, pH, 'F');

        // Header
        pdf.setFont("Helvetica", "bold");
        pdf.setFontSize(8);
        pdf.setTextColor('#64748b');
        pdf.text('SMADIMENT — ONLINE NEWS OVERVIEW REPORT', margin, 10);
        pdf.setFont("Helvetica", "normal");
        pdf.text(new Date().toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}), pW - margin, 10, {align:'right'});
        
        pdf.setDrawColor('#cbd5e1');
        pdf.setLineWidth(0.2);
        pdf.line(margin, 12, pW - margin, 12);

        // Content
        const xOffset = margin;
        const yOffset = 14 + (uh - imgH) / 2;
        pdf.addImage(imgData, 'JPEG', xOffset, yOffset, uw, imgH);

        // Footer
        pdf.line(margin, pH - 8, pW - margin, pH - 8);
        pdf.setFontSize(7);
        pdf.setTextColor('#94a3b8');
        pdf.text(`Section: ${label}`, margin, pH - 5);
        pdf.text(`Halaman ${curPg} dari ${totPg}`, pW - margin, pH - 5, {align:'right'});
    }

    function _paginate(pdf, canvas, margin, pW, pH, label) {
        const uw = pW - margin * 2;
        const uh = pH - 14 - 8;
        const cw = canvas.width;
        const ch = canvas.height;
        const pageHInCanvas = cw * (uh / uw);
        let remH = ch;
        let curY = 0;
        let pg = 1;

        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = cw;
        const tempCtx = tempCanvas.getContext('2d');

        while (remH > 0) {
            const hToDraw = Math.min(remH, pageHInCanvas);
            tempCanvas.height = hToDraw;
            tempCtx.fillStyle = '#ffffff';
            tempCtx.fillRect(0, 0, cw, hToDraw);
            tempCtx.drawImage(canvas, 0, curY, cw, hToDraw, 0, 0, cw, hToDraw);

            if (pg > 1) pdf.addPage();
            _addCanvasAsPage(pdf, tempCanvas, margin, pW, pH, `${label} (Part ${pg})`, pg, 'Multi');

            curY += hToDraw;
            remH -= hToDraw;
            pg++;
        }
    }

    const _cardLabels = {
        trend: 'Mention Trend Chart',
        donut: 'Sentiment Distribution',
        publishers: 'Top Publishers',
        articles: 'Recent Articles List'
    };

    return {
        async runCard(areaId, cardKey, type, btn) {
            if (!window.html2canvas) { _toast('html2canvas tidak tersedia', 'error'); return; }
            if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

            _btnState(btn, true);
            _toast(type === 'pdf' ? 'Menyiapkan PDF card…' : 'Mengambil gambar card…', 'default', 99999);

            try {
                const area = document.getElementById(areaId);
                if (!area) throw new Error('Area #' + areaId + ' tidak ditemukan');

                const ecSnap = cardKey === 'donut' ? await _getEChartSnapshot() : null;
                const apexSnap = cardKey === 'trend' ? await _getApexSnapshot() : null;

                const canvas = await _doCapture(area, '#ffffff', ecSnap, apexSnap);
                const fname = `news_overview_${cardKey}_${NV.getProjectId()}_${_stamp()}`;
                const label = _cardLabels[cardKey] || cardKey;

                if (type === 'image') {
                    const a = document.createElement('a');
                    a.download = fname + '.png';
                    a.href = canvas.toDataURL('image/png');
                    a.click();
                    _toast('Gambar berhasil diunduh!', 'success');
                } else {
                    const { jsPDF } = window.jspdf;
                    const landscape = canvas.width > canvas.height * 1.2;
                    const pdf = new jsPDF({ orientation: landscape ? 'landscape' : 'portrait', unit:'mm', format:'a4' });
                    const pW = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight();
                    const M = 10, uw = pW - M * 2, uh = pH - 14 - 8;
                    const fitsOne = (canvas.height * (uw / canvas.width)) <= uh;

                    if (fitsOne) {
                        _addCanvasAsPage(pdf, canvas, M, pW, pH, label, 1, 1);
                    } else {
                        _paginate(pdf, canvas, M, pW, pH, label);
                    }
                    pdf.save(fname + '.pdf');
                    _toast('PDF berhasil diunduh!', 'success');
                }
            } catch(err) {
                console.error('[NVExport.runCard]', err);
                _toast('Export gagal: ' + err.message, 'error');
            } finally { _btnState(btn, false); }
        },

        async run(type, btn) {
            if (!window.html2canvas) { _toast('html2canvas tidak tersedia', 'error'); return; }
            if (type === 'pdf' && !window.jspdf?.jsPDF) { _toast('jsPDF tidak tersedia', 'error'); return; }

            const btnPdf = document.getElementById('pageExportPdfBtn');
            const btnImg = document.getElementById('pageExportImgBtn');
            _btnState([btnPdf, btnImg], true);
            _toast(type === 'pdf' ? 'Menyiapkan PDF 2 halaman…' : 'Mengambil gambar halaman…', 'default', 99999);

            try {
                window.scrollTo({ top: 0 });
                const stamp = _stamp();

                _toast('Menyiapkan snapshot charts…', 'default', 99999);
                const ecSnap = await _getEChartSnapshot();
                const apexSnap = await _getApexSnapshot();

                if (type === 'image') {
                    const area = document.getElementById('pageExportArea');
                    if (!area) throw new Error('pageExportArea tidak ditemukan');
                    const canvas = await _doCapture(area, '#f1f5f9', ecSnap, apexSnap);
                    const a = document.createElement('a');
                    a.download = `news_overview_${NV.getProjectId()}_${stamp}.png`;
                    a.href = canvas.toDataURL('image/png');
                    a.click();
                    _toast('Gambar berhasil diunduh!', 'success');
                    return;
                }

                const pg1El = document.getElementById('exportPage1');
                const pg2El = document.getElementById('exportPage2');
                if (!pg1El || !pg2El) throw new Error('Wrapper #exportPage1 / #exportPage2 tidak ditemukan.');

                _toast('Menangkap Halaman 1…', 'default', 99999);
                const canvas1 = await _doCapture(pg1El, '#f1f5f9', ecSnap, apexSnap);

                _toast('Menangkap Halaman 2…', 'default', 99999);
                const canvas2 = await _doCapture(pg2El, '#f1f5f9', ecSnap, apexSnap);

                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
                const pW = pdf.internal.pageSize.getWidth(), pH = pdf.internal.pageSize.getHeight();
                
                _addCanvasAsPage(pdf, canvas1, 10, pW, pH, 'News KPIs & Trend Distribution', 1, 2);
                pdf.addPage();
                _addCanvasAsPage(pdf, canvas2, 10, pW, pH, 'Publishers & Recent Articles', 2, 2);
                
                pdf.save(`news_overview_${NV.getProjectId()}_${stamp}.pdf`);
                _toast('PDF 2 halaman berhasil diunduh!', 'success');
            } catch(err) {
                console.error('[NVExport.run]', err);
                _toast('Export gagal: ' + err.message, 'error');
            } finally {
                _btnState([btnPdf, btnImg], false);
            }
        }
    };
})();
</script>
@endsection
