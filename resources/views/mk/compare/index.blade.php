@extends('mk.layouts.app')

@section('title', 'Compare Projects - SMADIMENT')

@section('styles')
<style>
  :root {
    --green: #038047;
    --green-dark: #026738;
    --green-light: #e6f5ee;
    --text: #1a202c;
    --text-muted: #64748b;
    --border: #e2e8f0;
    --bg: #f8fafc;
    --white: #ffffff;
    --shadow: 0 1px 3px rgba(0,0,0,0.08);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.1);
  }

  .cmp-container {
    padding: 24px;
    background: var(--bg);
    min-height: 100vh;
    max-width: 1600px;
    margin: 0 auto;
  }

  /* ── Page Header ── */
  .cmp-header { margin-bottom: 28px; }
  .cmp-header h1 { font-size: 26px; font-weight: 700; color: var(--text); margin: 0 0 6px; }
  .cmp-header p  { font-size: 13px; color: var(--text-muted); margin: 0; }

  /* ── Config Card ── */
  .config-card {
    background: var(--white);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    margin-bottom: 24px;
  }

  .config-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 16px;
    align-items: end;
  }

  @media (max-width: 900px) { .config-grid { grid-template-columns: 1fr; } }

  .form-label {
    font-size: 12px; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: block;
  }

  /* ── Project Selector ── */
  .project-search-input {
    width: 100%; padding: 11px 16px; border: 1px solid var(--border);
    border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 13px;
    background: var(--bg); color: var(--text); outline: none; transition: all 0.2s; margin-bottom: 8px;
  }
  .project-search-input:focus {
    border-color: var(--green); background: var(--white);
    box-shadow: 0 0 0 3px rgba(3,128,71,0.1);
  }

  .project-dropdown {
    border: 1px solid var(--border); border-radius: 10px; max-height: 220px;
    overflow-y: auto; background: var(--white); box-shadow: var(--shadow-md); scrollbar-width: thin;
  }
  .project-dropdown::-webkit-scrollbar { width: 4px; }
  .project-dropdown::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

  .project-option {
    display: flex; align-items: center; gap: 10px; padding: 10px 14px;
    cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.15s;
    font-size: 13px; color: var(--text);
  }
  .project-option:last-child { border-bottom: none; }
  .project-option:hover { background: var(--bg); }
  .project-option input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--green); cursor: pointer; flex-shrink: 0; }
  .project-option-info { flex: 1; min-width: 0; }
  .project-option-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .project-option-meta { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
  .project-option-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: var(--green-light); color: var(--green); flex-shrink: 0; }

  .selected-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; min-height: 32px; }
  .selected-tag {
    display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px;
    background: var(--green); color: white; border-radius: 20px;
    font-size: 12px; font-weight: 600; animation: tagIn 0.2s ease-out;
  }
  @keyframes tagIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .selected-tag button { background: none; border: none; color: rgba(255,255,255,0.8); cursor: pointer; font-size: 14px; line-height: 1; padding: 0; display: flex; align-items: center; transition: color 0.15s; }
  .selected-tag button:hover { color: white; }

  .date-row { display: flex; align-items: center; gap: 10px; }
  .date-input {
    flex: 1; padding: 11px 14px; border: 1px solid var(--border); border-radius: 10px;
    font-family: 'Poppins', sans-serif; font-size: 13px; color: var(--text);
    outline: none; transition: all 0.2s; background: var(--bg);
  }
  .date-input:focus { border-color: var(--green); background: var(--white); box-shadow: 0 0 0 3px rgba(3,128,71,0.1); }
  .date-sep { font-size: 12px; color: var(--text-muted); font-weight: 600; white-space: nowrap; }

  .compare-btn {
    padding: 12px 28px;
    background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
    color: white; border: none; border-radius: 12px; font-family: 'Poppins', sans-serif;
    font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.25s;
    display: flex; align-items: center; gap: 8px; white-space: nowrap;
    box-shadow: 0 4px 12px rgba(3,128,71,0.25);
  }
  .compare-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,0.35); }
  .compare-btn:disabled { opacity: 0.5; cursor: not-allowed; }
  .compare-btn svg { width: 18px; height: 18px; }

  /* ── Results ── */
  #resultsSection { display: none; animation: fadeUp 0.4s ease-out; }
  @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

  .projects-row { display: grid; gap: 16px; margin-bottom: 24px; }

  .project-summary-card {
    background: var(--white); border-radius: 14px; padding: 20px;
    border: 2px solid var(--border); transition: border-color 0.2s;
    position: relative; overflow: hidden;
  }
  .project-summary-card .card-title { font-size: 15px; font-weight: 700; color: var(--text); margin: 0 0 4px; line-height: 1.3; }
  .project-summary-card .card-meta  { font-size: 11px; color: var(--text-muted); margin-bottom: 16px; }

  .project-stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
  .pstat { text-align: center; }
  .pstat-value { font-size: 22px; font-weight: 700; color: var(--text); line-height: 1; }
  .pstat-label { font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin-top: 4px; }

  .sentiment-mini { display: flex; gap: 6px; margin-top: 12px; }
  .sent-bar-wrap { flex: 1; }
  .sent-bar-label { display: flex; justify-content: space-between; font-size: 10px; font-weight: 600; margin-bottom: 4px; }
  .sent-bar { height: 5px; border-radius: 3px; background: #e2e8f0; overflow: hidden; }
  .sent-bar-fill { height: 100%; border-radius: 3px; transition: width 1s ease-out; }

  .section-card { background: var(--white); border-radius: 16px; padding: 24px; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 24px; }
  .section-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid var(--bg); }
  .section-head h3 { font-size: 16px; font-weight: 700; color: var(--text); margin: 0 0 4px; }
  .section-sub { font-size: 12px; color: var(--text-muted); }
  .chart-wrap { position: relative; height: 300px; }

  .rank-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
  .rank-table th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 2px solid var(--bg); }
  .rank-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: var(--text); }
  .rank-table tbody tr:hover { background: #fafbfc; }
  .rank-table tbody tr:last-child td { border-bottom: none; }

  .rank-num { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; background: var(--bg); color: var(--text-muted); }
  .rank-num.gold   { background: #fef3c7; color: #b45309; }
  .rank-num.silver { background: #f1f5f9; color: #475569; }
  .rank-num.bronze { background: #fef0e7; color: #c2410c; }

  .bar-cell { min-width: 120px; }
  .bar-track { height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
  .bar-fill { height: 100%; border-radius: 4px; transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1); }

  .sentiment-grid { display: grid; gap: 16px; margin-top: 4px; }
  .sentiment-project-row { display: grid; grid-template-columns: 180px 1fr; gap: 16px; align-items: center; }
  @media (max-width: 640px) { .sentiment-project-row { grid-template-columns: 1fr; } }
  .sentiment-project-name { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .sentiment-bars { display: flex; height: 28px; border-radius: 8px; overflow: hidden; }
  .sentiment-seg { display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: white; transition: width 1.2s ease-out; white-space: nowrap; overflow: hidden; }

  .chart-legend { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 12px; }
  .legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--text-muted); }
  .legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }

  .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
  .empty-state svg { width: 48px; height: 48px; opacity: 0.3; margin-bottom: 16px; }
  .empty-state h3 { font-size: 16px; font-weight: 600; margin: 0 0 8px; }
  .empty-state p  { font-size: 13px; margin: 0; }

  .loading-overlay {
    position: fixed; inset: 0; background: rgba(255,255,255,0.8);
    backdrop-filter: blur(4px); z-index: 9999; display: none;
    flex-direction: column; align-items: center; justify-content: center; gap: 16px;
  }
  .loading-overlay.show { display: flex; }
  .loading-spinner { width: 48px; height: 48px; border: 4px solid rgba(3,128,71,0.2); border-top-color: var(--green); border-radius: 50%; animation: spin 0.8s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .loading-text { font-size: 14px; font-weight: 600; color: var(--text-muted); }

  .tab-nav { display: flex; gap: 4px; background: var(--bg); border-radius: 10px; padding: 4px; }
  .tab-btn { padding: 7px 16px; border: none; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; color: var(--text-muted); background: transparent; transition: all 0.2s; }
  .tab-btn.active { background: var(--white); color: var(--green); box-shadow: var(--shadow); }

  /* Media breakdown badges */
  .media-badges { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 10px; }
  .media-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; background: var(--bg); color: var(--text-muted); border: 1px solid var(--border); }
</style>
@endsection

@section('content')
<div class="cmp-container">

  <!-- Page Header -->
  <div class="cmp-header">
    <h1>
      <svg viewBox="0 0 24 24" style="width:26px;height:26px;display:inline;vertical-align:-4px;margin-right:8px;stroke:var(--green);fill:none;stroke-width:2">
        <rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/>
      </svg>
      Compare Projects
    </h1>
    <p>Select 2–10 projects and a date range to compare volume, sentiment, and author metrics side-by-side</p>
  </div>

  <!-- Config Card -->
  <div class="config-card">
    <div class="config-grid">

      <!-- Project Selector -->
      <div>
        <label class="form-label">Select Projects (min 2)</label>
        <div class="project-selector">
          <input type="text" class="project-search-input" id="projectSearch" placeholder="🔍 Search projects…" autocomplete="off">
          <div class="project-dropdown" id="projectDropdown">
            <div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">Loading projects…</div>
          </div>
        </div>
        <div class="selected-tags" id="selectedTags">
          <span style="font-size:12px;color:var(--text-muted);align-self:center;">No projects selected</span>
        </div>
      </div>

      <!-- Date Range -->
      <div>
        <label class="form-label">Date Range</label>
        <div class="date-row">
          <input type="date" class="date-input" id="startDate" value="{{ $startDate }}">
          <span class="date-sep">to</span>
          <input type="date" class="date-input" id="endDate" value="{{ $endDate }}">
        </div>
        <div style="display:flex;gap:6px;margin-top:10px;flex-wrap:wrap;">
          @foreach([['7d','Last 7 Days'],['30d','Last 30 Days'],['1m','This Month']] as [$key,$label])
          <button onclick="applyPreset('{{ $key }}')"
            style="padding:4px 10px;font-size:11px;font-weight:600;border:1px solid var(--border);border-radius:6px;background:var(--bg);cursor:pointer;color:var(--text-muted);font-family:'Poppins',sans-serif;transition:all 0.15s"
            onmouseover="this.style.borderColor='var(--green)';this.style.color='var(--green)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">{{ $label }}</button>
          @endforeach
        </div>
      </div>

      <!-- Action -->
      <div>
        <label class="form-label" style="opacity:0">Action</label>
        <button class="compare-btn" id="compareBtn" onclick="runCompare()" disabled>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/>
          </svg>
          Compare Now
        </button>
      </div>
    </div>
  </div>

  <!-- Results -->
  <div id="resultsSection">

    <!-- Project Summary Cards -->
    <div class="projects-row" id="projectsRow"></div>

    <!-- Charts Grid -->
    <div style="display:grid;grid-template-columns:1.4fr 0.6fr;gap:24px;margin-bottom:24px;">

      <!-- Volume Trend -->
      <div class="section-card">
        <div class="section-head">
          <div>
            <h3>Volume Trend</h3>
            <div class="section-sub">Daily posting volume across selected projects</div>
          </div>
          <div class="tab-nav">
            <button class="tab-btn active" onclick="switchVolumeView('line', this)">Line</button>
            <button class="tab-btn" onclick="switchVolumeView('bar', this)">Bar</button>
          </div>
        </div>
        <div class="chart-wrap"><canvas id="volumeChart"></canvas></div>
        <div class="chart-legend" id="volumeLegend"></div>
      </div>

      <!-- Total Volume Ranking -->
      <div class="section-card">
        <div class="section-head">
          <div>
            <h3>Total Volume</h3>
            <div class="section-sub">Ranked by total posts</div>
          </div>
        </div>
        <div id="volumeRanking"></div>
      </div>
    </div>

    <!-- Media Breakdown -->
    <div class="section-card">
      <div class="section-head">
        <div>
          <h3>Volume by Media</h3>
          <div class="section-sub">Breakdown per platform per project</div>
        </div>
      </div>
      <div id="mediaBreakdown"></div>
    </div>

    <!-- Sentiment Section -->
    <div class="section-card">
      <div class="section-head">
        <div>
          <h3>Sentiment Comparison</h3>
          <div class="section-sub">Positive · Neutral · Negative breakdown per project</div>
        </div>
        <div class="tab-nav">
          <button class="tab-btn active" onclick="switchSentView('bar', this)">Bar</button>
          <button class="tab-btn" onclick="switchSentView('pie', this)">Donut</button>
        </div>
      </div>
      <div id="sentimentBars" style="display:block;">
        <div class="sentiment-grid" id="sentimentGrid"></div>
      </div>
      <div id="sentimentDonut" style="display:none;">
        <div style="display:grid;gap:16px;" id="sentimentDonutGrid"></div>
      </div>
    </div>

  </div>

  <!-- Empty State -->
  <div id="emptyState" class="empty-state">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
      <rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/>
    </svg>
    <h3>Select projects to compare</h3>
    <p>Pick at least 2 projects from the dropdown above, set a date range, and click <strong>Compare Now</strong></p>
  </div>

</div>

<div class="loading-overlay" id="loadingOverlay">
  <div class="loading-spinner"></div>
  <div class="loading-text">Fetching comparison data…</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ═══════════════════════════════════════════════
// CONSTANTS
// ═══════════════════════════════════════════════
const PALETTE = [
  '#038047','#3b82f6','#f59e0b','#ef4444',
  '#8b5cf6','#10b981','#ec4899','#0ea5e9','#f97316','#6366f1',
];
const SENT_COLORS = { pos: '#10b981', net: '#64748b', neg: '#ef4444' };
const MEDIA_LABELS = { doc:'Online News', twit:'X (Twitter)', fb:'Facebook', instagram:'Instagram', youtube:'YouTube', tiktok:'TikTok' };

// ═══════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════
let allProjects       = [];
let selectedIds       = new Set();
let compareData       = null;
let volumeChart       = null;
let currentVolumeType = 'line';

// ═══════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
  loadProjects();
  @foreach($selectedIds ?? [] as $sid)
    selectedIds.add('{{ $sid }}');
  @endforeach
  document.getElementById('projectSearch').addEventListener('input', filterDropdown);
});

// ═══════════════════════════════════════════════
// PROJECTS LOADER
// ═══════════════════════════════════════════════
async function loadProjects() {
  try {
    const res  = await fetch('/mk/api/compare/projects');
    const data = await res.json();
    allProjects = data.data || [];
    renderDropdown(allProjects);
    if (selectedIds.size > 0) { updateSelectedTags(); updateCompareBtn(); }
  } catch (e) {
    document.getElementById('projectDropdown').innerHTML =
      '<div style="padding:20px;text-align:center;color:#ef4444;font-size:13px;">Failed to load projects</div>';
  }
}

function renderDropdown(list) {
  const dd = document.getElementById('projectDropdown');
  if (!list.length) {
    dd.innerHTML = '<div style="padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">No projects found</div>';
    return;
  }
  dd.innerHTML = list.map(p => `
    <label class="project-option">
      <input type="checkbox" value="${p.id}" ${selectedIds.has(String(p.id)) ? 'checked' : ''}
             onchange="toggleProject('${p.id}', '${escHtml(p.title)}')">
      <div class="project-option-info">
        <div class="project-option-name">${escHtml(p.title)}</div>
        <div class="project-option-meta">${escHtml(p.group_name || p.project_type)}</div>
      </div>
      ${selectedIds.has(String(p.id)) ? '<span class="project-option-badge">✓</span>' : ''}
    </label>
  `).join('');
}

function filterDropdown() {
  const q = document.getElementById('projectSearch').value.toLowerCase();
  const filtered = q
    ? allProjects.filter(p => p.title.toLowerCase().includes(q) || (p.group_name||'').toLowerCase().includes(q))
    : allProjects;
  renderDropdown(filtered);
}

function toggleProject(id, title) {
  id = String(id);
  if (selectedIds.has(id)) {
    selectedIds.delete(id);
  } else {
    if (selectedIds.size >= 10) {
      alert('Maximum 10 projects can be compared at once.');
      const cb = document.querySelector(`input[value="${id}"]`);
      if (cb) cb.checked = false;
      return;
    }
    selectedIds.add(id);
  }
  updateSelectedTags(); updateCompareBtn(); filterDropdown();
}

function removeProject(id) {
  selectedIds.delete(id);
  updateSelectedTags(); updateCompareBtn(); filterDropdown();
}

function updateSelectedTags() {
  const container = document.getElementById('selectedTags');
  if (!selectedIds.size) {
    container.innerHTML = '<span style="font-size:12px;color:var(--text-muted);align-self:center;">No projects selected</span>';
    return;
  }
  container.innerHTML = [...selectedIds].map(id => {
    const p = allProjects.find(x => String(x.id) === id);
    const title = p ? truncate(p.title, 28) : 'Project #' + id;
    return `<span class="selected-tag">${escHtml(title)}<button onclick="removeProject('${id}')" title="Remove">✕</button></span>`;
  }).join('');
}

function updateCompareBtn() {
  document.getElementById('compareBtn').disabled = selectedIds.size < 2;
}

// ═══════════════════════════════════════════════
// DATE PRESETS
// ═══════════════════════════════════════════════
function applyPreset(key) {
  const today = new Date();
  let start;
  if (key === '7d')  start = new Date(today - 6 * 86400000);
  if (key === '30d') start = new Date(today - 29 * 86400000);
  if (key === '1m')  start = new Date(today.getFullYear(), today.getMonth(), 1);
  document.getElementById('startDate').value = fmtDate(start);
  document.getElementById('endDate').value   = fmtDate(today);
}

// ═══════════════════════════════════════════════
// COMPARE
// ═══════════════════════════════════════════════
async function runCompare() {
  if (selectedIds.size < 2) return;
  const startDate = document.getElementById('startDate').value;
  const endDate   = document.getElementById('endDate').value;
  if (!startDate || !endDate) { alert('Please select a date range.'); return; }

  showLoading(true);
  try {
    const ids = [...selectedIds].join(',');
    const url = `/mk/api/compare/all?project_ids=${ids}&start_date=${startDate}&end_date=${endDate}`;
    const res = await fetch(url);
    compareData = await res.json();
    if (!compareData.success) throw new Error(compareData.error || 'API error');
    renderResults(compareData);
    document.getElementById('resultsSection').style.display = 'block';
    document.getElementById('emptyState').style.display = 'none';
  } catch (e) {
    alert('Compare failed: ' + e.message);
    console.error(e);
  } finally {
    showLoading(false);
  }
}

// ═══════════════════════════════════════════════
// RENDER RESULTS
// ═══════════════════════════════════════════════
function renderResults(data) {
  const details   = data.project_details || {};
  const volume    = data.data?.volumetotal    || {};
  const sentiment = data.data?.sentimenttotal || {};
  const ids       = data.project_ids || [];

  renderProjectCards(ids, details, volume, sentiment);
  renderVolumeChart(ids, details, volume);
  renderVolumeRanking(ids, details, volume);
  renderMediaBreakdown(ids, details, volume);
  renderSentimentBars(ids, details, sentiment);
}

// ── Project Summary Cards ──────────────────────
function renderProjectCards(ids, details, volume, sentiment) {
  const row = document.getElementById('projectsRow');
  const cols = Math.min(ids.length <= 3 ? ids.length : Math.ceil(ids.length / 2), 4);
  row.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;

  row.innerHTML = ids.map((id, i) => {
    const p     = details[id] || details[String(id)] || {};
    const color = PALETTE[i % PALETTE.length];
    const vol   = extractVolumeTotal(volume, id);
    const sent  = extractSentiment(sentiment, id);
    const { pos, neg, net } = sent;
    const total = pos + neg + net || 1;

    // Media breakdown mini
    const byMedia = extractByMedia(volume, id);
    const mediaBadges = Object.entries(byMedia)
      .filter(([,v]) => v > 0)
      .sort((a,b) => b[1]-a[1])
      .map(([k,v]) => `<span class="media-badge">${MEDIA_LABELS[k]||k}: ${fmtNum(v)}</span>`)
      .join('');

    return `
      <div class="project-summary-card" style="border-color:${color}33">
        <div style="position:absolute;top:0;left:0;right:0;height:4px;background:${color};border-radius:14px 14px 0 0"></div>
        <div class="card-title">${escHtml(p.title || 'Project #' + id)}</div>
        <div class="card-meta">${escHtml(p.group_name || p.project_type || '')}</div>
        <div class="project-stats-grid">
          <div class="pstat">
            <div class="pstat-value" style="color:${color}">${fmtNum(vol)}</div>
            <div class="pstat-label">Volume</div>
          </div>
          <div class="pstat">
            <div class="pstat-value" style="color:#10b981">${((pos/total)*100).toFixed(1)}%</div>
            <div class="pstat-label">Positive</div>
          </div>
          <div class="pstat">
            <div class="pstat-value" style="color:#ef4444">${((neg/total)*100).toFixed(1)}%</div>
            <div class="pstat-label">Negative</div>
          </div>
        </div>
        <div class="sentiment-mini">
          ${['pos','net','neg'].map(k => {
            const val = k==='pos' ? pos : k==='net' ? net : neg;
            const pct = ((val/total)*100).toFixed(0);
            const lbl = k==='pos' ? 'Pos' : k==='net' ? 'Neu' : 'Neg';
            return `<div class="sent-bar-wrap">
              <div class="sent-bar-label">
                <span style="color:${SENT_COLORS[k]}">${lbl}</span>
                <span>${pct}%</span>
              </div>
              <div class="sent-bar">
                <div class="sent-bar-fill" style="width:0%;background:${SENT_COLORS[k]}" data-pct="${pct}"></div>
              </div>
            </div>`;
          }).join('')}
        </div>
        ${mediaBadges ? `<div class="media-badges">${mediaBadges}</div>` : ''}
      </div>
    `;
  }).join('');

  setTimeout(() => {
    document.querySelectorAll('.sent-bar-fill').forEach(el => { el.style.width = el.dataset.pct + '%'; });
  }, 100);
}

// ── Volume Chart ───────────────────────────────
function renderVolumeChart(ids, details, volume) {
  if (volumeChart) { volumeChart.destroy(); volumeChart = null; }
  const canvas = document.getElementById('volumeChart');
  const legend = document.getElementById('volumeLegend');

  // compare_projects doesn't return daily trend — show total as single bar
  const totals = ids.map(id => extractVolumeTotal(volume, id));
  const labels = ids.map(id => {
    const p = details[id] || details[String(id)] || {};
    return truncate(p.title || 'Project #' + id, 25);
  });

  if (totals.every(t => t === 0)) {
    canvas.parentElement.innerHTML = '<div class="empty-state" style="padding:40px"><p>No volume data available</p></div>';
    return;
  }

  volumeChart = new Chart(canvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Total Volume',
        data: totals,
        backgroundColor: ids.map((_, i) => PALETTE[i % PALETTE.length] + 'cc'),
        borderColor:     ids.map((_, i) => PALETTE[i % PALETTE.length]),
        borderWidth: 2,
        borderRadius: 6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1a202c', padding: 12, cornerRadius: 8,
          titleColor: '#fff', bodyColor: '#fff',
          titleFont: { size: 12, weight: '700', family: 'Poppins' },
          bodyFont: { size: 12, family: 'Poppins' },
          callbacks: { label: ctx => ' ' + fmtNum(ctx.raw) + ' posts' }
        }
      },
      scales: {
        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 }, callback: v => fmtNum(v) } },
        x: { grid: { display: false }, ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } } }
      }
    }
  });

  legend.innerHTML = ids.map((id, i) => {
    const p = details[id] || details[String(id)] || {};
    return `<div class="legend-item"><div class="legend-dot" style="background:${PALETTE[i%PALETTE.length]}"></div><span>${escHtml(truncate(p.title||'Project #'+id, 30))}</span></div>`;
  }).join('');
}

// ── Volume Ranking ─────────────────────────────
function renderVolumeRanking(ids, details, volume) {
  const container = document.getElementById('volumeRanking');
  const ranked = ids.map((id, i) => ({
    id, i,
    title: (details[id]||details[String(id)]||{}).title || 'Project #' + id,
    total: extractVolumeTotal(volume, id),
  })).sort((a, b) => b.total - a.total);

  const max = ranked[0]?.total || 1;
  const rankClasses = ['gold','silver','bronze'];

  container.innerHTML = `
    <div style="overflow-x:auto">
      <table class="rank-table">
        <thead><tr><th>#</th><th>Project</th><th>Total</th><th>Bar</th></tr></thead>
        <tbody>
          ${ranked.map((item, rank) => `
            <tr>
              <td><div class="rank-num ${rankClasses[rank]||''}">${rank+1}</div></td>
              <td>
                <div style="font-weight:600;font-size:13px">${escHtml(truncate(item.title,35))}</div>
                <div style="font-size:10px;color:${PALETTE[item.i%PALETTE.length]};font-weight:700;margin-top:2px">●</div>
              </td>
              <td style="font-weight:700;font-size:15px">${fmtNum(item.total)}</td>
              <td class="bar-cell">
                <div class="bar-track">
                  <div class="bar-fill" style="width:0%;background:${PALETTE[item.i%PALETTE.length]}" data-pct="${((item.total/max)*100).toFixed(0)}"></div>
                </div>
              </td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>`;

  setTimeout(() => {
    container.querySelectorAll('.bar-fill').forEach(el => { el.style.width = el.dataset.pct + '%'; });
  }, 100);
}

// ── Media Breakdown ────────────────────────────
function renderMediaBreakdown(ids, details, volume) {
  const container = document.getElementById('mediaBreakdown');
  const mediaKeys = ['doc','twit','fb','instagram','youtube','tiktok'];

  container.innerHTML = ids.map((id, i) => {
    const p      = details[id] || details[String(id)] || {};
    const color  = PALETTE[i % PALETTE.length];
    const byMedia = extractByMedia(volume, id);
    const total  = extractVolumeTotal(volume, id) || 1;

    const bars = mediaKeys.map(k => {
      const val = byMedia[k] || 0;
      if (!val) return '';
      const pct = ((val / total) * 100).toFixed(1);
      return `
        <div style="margin-bottom:8px">
          <div style="display:flex;justify-content:space-between;font-size:11px;font-weight:600;margin-bottom:3px">
            <span style="color:var(--text-muted)">${MEDIA_LABELS[k]||k}</span>
            <span style="color:var(--text)">${fmtNum(val)} <span style="color:var(--text-muted);font-weight:500">(${pct}%)</span></span>
          </div>
          <div class="bar-track" style="height:6px">
            <div class="bar-fill" style="width:0%;background:${color}" data-pct="${pct}"></div>
          </div>
        </div>`;
    }).join('');

    return `
      <div style="padding:16px;border:1px solid var(--border);border-radius:12px;border-top:3px solid ${color}">
        <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:12px">${escHtml(p.title||'Project #'+id)}</div>
        ${bars || '<p style="font-size:12px;color:var(--text-muted)">No media data</p>'}
      </div>`;
  }).join('');

  // Grid layout
  container.style.display = 'grid';
  container.style.gridTemplateColumns = `repeat(${Math.min(ids.length, 3)}, 1fr)`;
  container.style.gap = '16px';

  setTimeout(() => {
    container.querySelectorAll('.bar-fill').forEach(el => { el.style.width = el.dataset.pct + '%'; });
  }, 100);
}

// ── Sentiment Bars ─────────────────────────────
function renderSentimentBars(ids, details, sentiment) {
  const grid = document.getElementById('sentimentGrid');

  grid.innerHTML = ids.map(id => {
    const p    = details[id] || details[String(id)] || {};
    const sent = extractSentiment(sentiment, id);
    const { pos, neg, net } = sent;
    const total = pos + neg + net || 1;
    const pp  = ((pos/total)*100).toFixed(1);
    const np  = ((net/total)*100).toFixed(1);
    const ngp = ((neg/total)*100).toFixed(1);

    return `
      <div class="sentiment-project-row">
        <div class="sentiment-project-name" title="${escHtml(p.title||'Project #'+id)}">
          ${escHtml(truncate(p.title||'Project #'+id, 22))}
          <div style="font-size:10px;color:var(--text-muted);font-weight:500;margin-top:2px">${fmtNum(pos+neg+net)} total</div>
        </div>
        <div>
          <div class="sentiment-bars">
            <div class="sentiment-seg" style="width:0%;background:${SENT_COLORS.pos}" data-pct="${pp}" title="Positive: ${pp}%">${pp > 10 ? pp+'%' : ''}</div>
            <div class="sentiment-seg" style="width:0%;background:${SENT_COLORS.net}" data-pct="${np}" title="Neutral: ${np}%">${np > 10 ? np+'%' : ''}</div>
            <div class="sentiment-seg" style="width:0%;background:${SENT_COLORS.neg}" data-pct="${ngp}" title="Negative: ${ngp}%">${ngp > 10 ? ngp+'%' : ''}</div>
          </div>
          <div style="display:flex;gap:16px;margin-top:4px">
            <span style="font-size:10px;color:${SENT_COLORS.pos};font-weight:700">● Pos ${pp}%</span>
            <span style="font-size:10px;color:${SENT_COLORS.net};font-weight:700">● Neu ${np}%</span>
            <span style="font-size:10px;color:${SENT_COLORS.neg};font-weight:700">● Neg ${ngp}%</span>
          </div>
        </div>
      </div>`;
  }).join('');

  setTimeout(() => {
    document.querySelectorAll('.sentiment-seg').forEach(el => { el.style.width = el.dataset.pct + '%'; });
  }, 100);
}

// ═══════════════════════════════════════════════
// VIEW SWITCHES
// ═══════════════════════════════════════════════
function switchVolumeView(type, btn) {
  currentVolumeType = type;
  btn.closest('.tab-nav').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  if (compareData) {
    const { project_ids: ids, project_details: details, data } = compareData;
    renderVolumeChart(ids, details, data.volumetotal || {});
  }
}

function switchSentView(type, btn) {
  document.getElementById('sentimentBars').style.display  = type === 'bar' ? 'block' : 'none';
  document.getElementById('sentimentDonut').style.display = type === 'pie' ? 'block' : 'none';
  btn.closest('.tab-nav').querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

// ═══════════════════════════════════════════════
// DATA EXTRACTION — matches actual MK API structure:
//
// volumetotal response:
//   { "17143": { volume_total: { all: { total: "23231" }, bymedia: { doc:"10441", twit:"11383", ... } } } }
//
// sentimenttotal response:
//   { "17143": { sentiment_total: { pos: "13741", neg: "5791", net: "3699" } } }
// ═══════════════════════════════════════════════

function getProjectNode(data, projectId) {
  if (!data || typeof data !== 'object') return null;
  // Try string key first, then integer key
  return data[String(projectId)] ?? data[parseInt(projectId)] ?? null;
}

function extractVolumeTotal(data, projectId) {
  const node = getProjectNode(data, projectId);
  if (!node) return 0;
  // Primary: volume_total.all.total
  if (node.volume_total?.all?.total !== undefined) return int(node.volume_total.all.total);
  // Fallback: direct numeric
  if (typeof node === 'number') return node;
  return 0;
}

function extractByMedia(data, projectId) {
  const node = getProjectNode(data, projectId);
  if (!node?.volume_total?.bymedia) return {};
  const bm = node.volume_total.bymedia;
  const result = {};
  for (const [k, v] of Object.entries(bm)) result[k] = int(v);
  return result;
}

function extractSentiment(data, projectId) {
  const node = getProjectNode(data, projectId);
  if (!node) return { pos: 0, neg: 0, net: 0 };
  const s = node.sentiment_total || node;
  return {
    pos: int(s.pos ?? s.positive ?? 0),
    neg: int(s.neg ?? s.negative ?? 0),
    net: int(s.net ?? s.neutral  ?? 0),
  };
}

// ═══════════════════════════════════════════════
// UTIL
// ═══════════════════════════════════════════════
function showLoading(show) { document.getElementById('loadingOverlay').classList.toggle('show', show); }
function fmtNum(n) { return new Intl.NumberFormat('en-US').format(n || 0); }
function fmtDate(d) { return d.toISOString().split('T')[0]; }
function int(v) { return parseInt(v, 10) || 0; }
function truncate(s, n) { return s && s.length > n ? s.slice(0, n) + '…' : (s || ''); }
function escHtml(s) { return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
@endsection