@extends('mk.layouts.app')

@section('title', 'Topic Map - SMADIMENT')

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

  .dashboard-container {
    padding: 24px;
    max-width: 1600px;
    margin: 0 auto;
  }

  .page-header { 
    margin-bottom: 32px; 
  }

  .page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 8px 0;
  }

  .page-header p {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
  }

  /* Stats Grid - MATCHING X TRENDING */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(3, 128, 71, 0.1) 0%, rgba(3, 128, 71, 0.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
  }

  .stat-icon-wrapper::after {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 16px;
    padding: 4px;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s;
  }

  .stat-card:hover .stat-icon-wrapper::after { opacity: 0.5; }

  .stat-icon {
    width: 28px;
    height: 28px;
    color: var(--primary-green);
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
  }

  .stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
  }

  .stat-value-wrapper {
    display: flex;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 16px;
  }

  .stat-value {
    font-size: 36px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .stat-progress {
    height: 6px;
    background: var(--bg-gray-100);
    border-radius: 10px;
    overflow: hidden;
    margin-top: 8px;
  }

  .stat-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    border-radius: 10px;
    transition: width 1s ease-out;
    width: 0%;
  }

  /* Chart Container - MATCHING X TRENDING */
  .chart-container {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
    margin-bottom: 24px;
    transition: all 0.3s;
  }

  .chart-container:hover {
    box-shadow: var(--shadow-md);
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--bg-gray-50);
    flex-wrap: wrap;
    gap: 16px;
  }

  .section-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
  }

  .chart-switcher {
    display: flex;
    gap: 8px;
    background: var(--bg-gray-100);
    padding: 4px;
    border-radius: 12px;
  }

  .chart-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: var(--text-secondary);
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }

  .chart-btn:hover {
    color: var(--text-primary);
    background: rgba(3, 128, 71, 0.05);
  }

  .chart-btn.active {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
  }

  .chart-view {
    min-height: 500px;
    display: none;
  }

  .chart-view.active {
    display: block;
  }

  /* Word Cloud - MATCHING X TRENDING */
  .topic-cloud {
    min-height: 500px;
    position: relative;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    overflow: hidden;
  }

  .topic-cloud::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background:
        radial-gradient(circle at 20% 30%, rgba(3, 128, 71, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(47, 198, 246, 0.03) 0%, transparent 50%);
    pointer-events: none;
  }

  #wordCloudCanvas {
    width: 100% !important;
    height: 500px !important;
    cursor: pointer;
    position: relative;
    z-index: 1;
  }

  .wordcloud-hint {
    position: absolute;
    bottom: 16px;
    right: 20px;
    font-size: 11px;
    color: var(--text-muted);
    font-style: italic;
    display: none;
    align-items: center;
    gap: 5px;
    z-index: 2;
    pointer-events: none;
  }

  .wordcloud-hint svg {
    width: 13px;
    height: 13px;
    stroke: currentColor;
    fill: none;
    flex-shrink: 0;
  }

  /* Bar Chart Container */
  .bar-chart-wrapper {
    position: relative;
    height: 500px;
  }

  /* Pie Chart Container */
  .pie-chart-wrapper {
    position: relative;
    height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Topic List Container - MATCHING X TRENDING */
  .topic-list-container {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
  }

  .topic-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--bg-gray-100);
    transition: all 0.2s;
  }

  .topic-item:last-child {
    border-bottom: none;
  }

  .topic-item:hover {
    background: var(--bg-gray-50);
    padding-left: 28px;
  }

  .topic-rank {
    font-size: 14px;
    font-weight: 700;
    color: #94a3b8;
    min-width: 40px;
  }

  .topic-rank.top-3 {
    color: var(--primary-green);
    font-size: 16px;
  }

  .topic-name {
    flex: 1;
    font-weight: 600;
    color: var(--text-primary);
    margin-left: 16px;
    font-size: 14px;
  }

  .topic-count {
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    color: white;
    padding: 6px 16px;
    border-radius: 16px;
    font-size: 13px;
    font-weight: 700;
    min-width: 50px;
    text-align: center;
  }

  .view-all-btn {
    width: 100%;
    margin-top: 20px;
    padding: 14px;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
  }

  .view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
  }

  /* Modal - MATCHING X TRENDING */
  .modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.75);
    z-index: 9999;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }

  .modal-overlay.active {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    background: white;
    border-radius: 20px;
    width: 90%;
    max-width: 700px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
    animation: slideUp 0.3s ease;
  }

  .modal-header {
    padding: 24px 32px;
    border-bottom: 1px solid var(--border-gray);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-header h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
  }

  .modal-close {
    background: var(--bg-gray-50);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    color: var(--text-secondary);
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .modal-close:hover {
    background: #ef4444;
    color: white;
  }

  .modal-body {
    padding: 24px 32px;
    overflow-y: auto;
    flex: 1;
  }

  .modal-search {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    margin-bottom: 20px;
    transition: all 0.2s;
  }

  .modal-search:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Loading State - MATCHING X TRENDING */
  .loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 80px 20px;
  }

  .loading-spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--bg-gray-100);
    border-top-color: var(--primary-green);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  .loading-text {
    font-size: 14px;
    color: var(--text-secondary);
    font-weight: 500;
  }

  /* Empty State - MATCHING X TRENDING */
  .empty-state {
    text-align: center;
    padding: 80px 20px;
  }

  .empty-state svg {
    width: 64px;
    height: 64px;
    color: var(--text-secondary);
    margin-bottom: 16px;
    stroke: currentColor;
    fill: none;
  }

  .empty-state h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 8px 0;
  }

  .empty-state p {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .dashboard-container { padding: 16px; }
    .stats-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
  }

  @media (max-width: 768px) {
    .page-header h1 { font-size: 24px; }
    .chart-container, .topic-list-container { padding: 20px; }
    .section-header { flex-direction: column; align-items: stretch; }
    .chart-switcher { width: 100%; }
    .chart-btn { flex: 1; }
    #wordCloudCanvas { height: 400px !important; }
    .topic-cloud { padding: 20px; min-height: 450px; }
    .stat-value { font-size: 28px; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <div class="page-header">
    <h1>Topic Map</h1>
    <p>Visual representation of trending topics and discussion themes</p>
  </div>

  <!-- Stats Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Total Topics</div>
      <div id="totalTopics" class="stat-value-wrapper">
        <div class="stat-value">-</div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar"></div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Top Topic</div>
      <div id="topTopic" class="stat-value-wrapper">
        <div class="stat-value" style="font-size: 20px;">-</div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar"></div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Total Mentions</div>
      <div id="totalMentions" class="stat-value-wrapper">
        <div class="stat-value">-</div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar"></div>
      </div>
    </div>
  </div>

  <!-- Chart Container with Switcher -->
  <div class="chart-container">
    <div class="section-header">
      <h3 class="section-title" id="chartTitle">Topic Word Cloud</h3>
      
      <!-- Chart Type Switcher -->
      <div class="chart-switcher">
        <button class="chart-btn active" onclick="switchChart('wordcloud')" id="btnWordCloud">
          Word Cloud
        </button>
        <button class="chart-btn" onclick="switchChart('bar')" id="btnBar">
          Bar Chart
        </button>
        <button class="chart-btn" onclick="switchChart('pie')" id="btnPie">
          Pie Chart
        </button>
      </div>
    </div>

    <!-- Word Cloud View -->
    <div class="chart-view active" id="wordCloudView">
      <div class="topic-cloud" id="topicCloud">
        <div class="loading-state" id="wordCloudLoading">
          <div class="loading-spinner"></div>
          <p class="loading-text">Loading topics...</p>
        </div>
        <canvas id="wordCloudCanvas" style="display: none;"></canvas>
        <div class="wordcloud-hint" id="wordCloudHint">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          Click a word to view details
        </div>
      </div>
    </div>

    <!-- Bar Chart View -->
    <div class="chart-view" id="barChartView">
      <div class="bar-chart-wrapper">
        <canvas id="barChart"></canvas>
      </div>
    </div>

    <!-- Pie Chart View -->
    <div class="chart-view" id="pieChartView">
      <div class="pie-chart-wrapper">
        <canvas id="pieChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Topic List -->
  <div class="topic-list-container">
    <div class="section-header" style="border-bottom: none; padding-bottom: 0;">
      <h3 class="section-title">Topic Details</h3>
    </div>
    <div id="topicList">
      <div class="loading-state">
        <div class="loading-spinner"></div>
      </div>
    </div>
    <button class="view-all-btn" id="viewAllBtn" style="display: none;" onclick="openModal()">
      View All Topics
    </button>
  </div>
</div>

<!-- Modal for All Topics -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOnOverlay(event)">
  <div class="modal-content" onclick="event.stopPropagation()">
    <div class="modal-header">
      <h3>All Topics</h3>
      <button class="modal-close" onclick="closeModal()">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body">
      <input type="text" class="modal-search" id="modalSearch" placeholder="Search topics...">
      <div id="modalTopicList"></div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- WordCloud2.js Library -->
<script src="https://cdn.jsdelivr.net/npm/wordcloud@1.2.2/src/wordcloud2.min.js"></script>

<script>
  const projectId = new URLSearchParams(window.location.search).get('project_id');
  let topicsData = [];
  let barChartInstance = null;
  let pieChartInstance = null;
  let currentChart = 'wordcloud';

  // Chart.js default config
  Chart.defaults.font.family = "'Poppins', sans-serif";
  Chart.defaults.font.size = 13;

  // Auto-load on page ready
  document.addEventListener('DOMContentLoaded', function() {
    if (!projectId) {
      showEmptyState('Please select a project from sidebar');
      return;
    }
    loadTopicMap();
  });

  async function loadTopicMap() {
    const media = 'all';
    const startDate = '{{ now()->subDays(7)->format("Y-m-d") }}';
    const endDate = '{{ now()->format("Y-m-d") }}';

    try {
      const response = await fetch(`/mk/api/topic-map?project_id=${projectId}&media=${media}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();

      if (!result.success || !result.data || result.data.length === 0) {
        showEmptyState('No topics found for this period');
        return;
      }

      topicsData = result.data;
      
      updateStats(topicsData);
      renderWordCloud(topicsData);
      renderTopicList(topicsData);

    } catch (error) {
      console.error('Error loading topics:', error);
      showEmptyState('Error loading topics. Please try again.');
    }
  }

  function switchChart(type) {
    // Update active button
    document.querySelectorAll('.chart-btn').forEach(btn => btn.classList.remove('active'));
    
    // Hide all views
    document.querySelectorAll('.chart-view').forEach(view => view.classList.remove('active'));
    
    currentChart = type;
    
    if (type === 'wordcloud') {
      document.getElementById('btnWordCloud').classList.add('active');
      document.getElementById('wordCloudView').classList.add('active');
      document.getElementById('chartTitle').textContent = 'Topic Word Cloud';
      
      if (topicsData.length > 0) {
        renderWordCloud(topicsData);
      }
    } 
    else if (type === 'bar') {
      document.getElementById('btnBar').classList.add('active');
      document.getElementById('barChartView').classList.add('active');
      document.getElementById('chartTitle').textContent = 'Topic Bar Chart';
      
      if (!barChartInstance && topicsData.length > 0) {
        renderBarChart(topicsData);
      }
    } 
    else if (type === 'pie') {
      document.getElementById('btnPie').classList.add('active');
      document.getElementById('pieChartView').classList.add('active');
      document.getElementById('chartTitle').textContent = 'Topic Distribution';
      
      if (!pieChartInstance && topicsData.length > 0) {
        renderPieChart(topicsData);
      }
    }
  }

  function updateStats(topics) {
    const totalTopics = topics.length;
    const topTopic = topics[0]?.name || '-';
    const totalMentions = topics.reduce((sum, t) => sum + t.count, 0);

    document.getElementById('totalTopics').innerHTML = `<div class="stat-value">${totalTopics.toLocaleString()}</div>`;
    document.getElementById('topTopic').innerHTML = `<div class="stat-value" style="font-size: 20px;" title="${topTopic}">${topTopic}</div>`;
    document.getElementById('totalMentions').innerHTML = `<div class="stat-value">${totalMentions.toLocaleString()}</div>`;

    // Animate progress bars
    const cards = document.querySelectorAll('.stat-card');
    const pcts = [80, 100, 90];
    cards.forEach((card, i) => {
      const bar = card.querySelector('.stat-progress-bar');
      if (bar) setTimeout(() => bar.style.width = pcts[i] + '%', 100);
    });
  }

  function renderWordCloud(topics) {
    const canvas = document.getElementById('wordCloudCanvas');
    const loading = document.getElementById('wordCloudLoading');
    const hint = document.getElementById('wordCloudHint');
    
    if (!canvas) return;
    
    if (topics.length === 0) {
      loading.innerHTML = '<div class="empty-state"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><h3>No Topics Found</h3><p>No topics to display</p></div>';
      return;
    }

    loading.style.display = 'none';
    canvas.style.display = 'block';
    hint.style.display = 'flex';
    
    // Top 40 topics for better visibility
    const topTopics = topics.slice(0, 40);
    
    const maxCount = Math.max(...topTopics.map(t => t.count));
    const minCount = Math.min(...topTopics.map(t => t.count));
    
    // Prepare data
    const wordList = topTopics.map(topic => {
      const normalizedWeight = ((topic.count - minCount) / (maxCount - minCount)) * 100;
      return [topic.name, Math.max(normalizedWeight, 25)];
    });
    
    // Professional color palette
    const colors = [
      '#038047','#04995a','#06bf80','#059669','#10b981',
      '#14b8a6','#0891b2','#0284c7','#3b82f6','#6366f1',
      '#8b5cf6','#a78bfa','#f59e0b'
    ];
    
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    WordCloud(canvas, {
      list: wordList,
      gridSize: 10,
      weightFactor: function(size) {
        return size * 3;
      },
      fontFamily: "'Poppins', 'Arial', sans-serif",
      fontWeight: '700',
      color: function() {
        return colors[Math.floor(Math.random() * colors.length)];
      },
      rotateRatio: 0.4,
      rotationSteps: 2,
      minSize: 18,
      backgroundColor: 'transparent',
      drawOutOfBound: false,
      shrinkToFit: true
    });
  }

  function renderBarChart(topics) {
    const ctx = document.getElementById('barChart').getContext('2d');
    
    if (barChartInstance) {
      barChartInstance.destroy();
    }
    
    const topTopics = topics.slice(0, 20);
    
    barChartInstance = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: topTopics.map(t => t.name),
        datasets: [{
          label: 'Mentions',
          data: topTopics.map(t => t.count),
          backgroundColor: 'rgba(3, 128, 71, 0.8)',
          borderColor: '#038047',
          borderWidth: 2,
          borderRadius: 8,
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            padding: 12,
            titleFont: { size: 14, weight: 'bold', family: "'Poppins', sans-serif" },
            bodyFont: { size: 13, family: "'Poppins', sans-serif" },
            borderColor: '#038047',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                return `Mentions: ${context.parsed.x.toLocaleString()}`;
              }
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            grid: { color: 'rgba(0, 0, 0, 0.05)' },
            ticks: { font: { weight: 600, family: "'Poppins', sans-serif" } }
          },
          y: {
            grid: { display: false },
            ticks: { font: { weight: 600, size: 12, family: "'Poppins', sans-serif" } }
          }
        }
      }
    });
  }

  function renderPieChart(topics) {
    const ctx = document.getElementById('pieChart').getContext('2d');
    
    if (pieChartInstance) {
      pieChartInstance.destroy();
    }
    
    const topTopics = topics.slice(0, 10);
    const othersCount = topics.slice(10).reduce((sum, t) => sum + t.count, 0);
    
    const labels = [...topTopics.map(t => t.name)];
    const data = [...topTopics.map(t => t.count)];
    
    if (othersCount > 0) {
      labels.push('Others');
      data.push(othersCount);
    }
    
    const colors = [
      '#038047','#04995a','#2FC6F6','#8b5cf6','#f59e0b',
      '#ef4444','#10b981','#3b82f6','#ec4899','#6366f1','#94a3b8'
    ];
    
    pieChartInstance = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: colors,
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverOffset: 15
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              padding: 20,
              font: { size: 13, weight: 600, family: "'Poppins', sans-serif" },
              generateLabels: function(chart) {
                const data = chart.data;
                return data.labels.map((label, i) => {
                  const value = data.datasets[0].data[i];
                  return {
                    text: `${label} (${value.toLocaleString()})`,
                    fillStyle: data.datasets[0].backgroundColor[i],
                    hidden: false,
                    index: i
                  };
                });
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            padding: 12,
            titleFont: { size: 14, weight: 'bold', family: "'Poppins', sans-serif" },
            bodyFont: { size: 13, family: "'Poppins', sans-serif" },
            borderColor: '#038047',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const value = context.parsed;
                const percentage = ((value / total) * 100).toFixed(1);
                return `${context.label}: ${value.toLocaleString()} (${percentage}%)`;
              }
            }
          }
        }
      }
    });
  }

  function renderTopicList(topics) {
    const listContainer = document.getElementById('topicList');
    const viewAllBtn = document.getElementById('viewAllBtn');
    
    if (topics.length === 0) {
      listContainer.innerHTML = '<div class="empty-state"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><h3>No Topics Found</h3><p>No topics to display</p></div>';
      viewAllBtn.style.display = 'none';
      return;
    }
    
    // Show only top 10
    const displayTopics = topics.slice(0, 10);
    
    listContainer.innerHTML = displayTopics.map((topic, index) => {
      const rank = index + 1;
      const isTop3 = rank <= 3;
      
      return `
        <div class="topic-item">
          <span class="topic-rank ${isTop3 ? 'top-3' : ''}">#${rank}</span>
          <span class="topic-name">${topic.name}</span>
          <span class="topic-count">${topic.count.toLocaleString()}</span>
        </div>
      `;
    }).join('');
    
    // Show "View All" button if more than 10 topics
    if (topics.length > 10) {
      viewAllBtn.style.display = 'block';
    } else {
      viewAllBtn.style.display = 'none';
    }
  }

  function openModal() {
    const modal = document.getElementById('modalOverlay');
    const modalList = document.getElementById('modalTopicList');
    
    // Render all topics in modal
    modalList.innerHTML = topicsData.map((topic, index) => {
      const rank = index + 1;
      const isTop3 = rank <= 3;
      
      return `
        <div class="topic-item" data-topic-name="${topic.name.toLowerCase()}">
          <span class="topic-rank ${isTop3 ? 'top-3' : ''}">#${rank}</span>
          <span class="topic-name">${topic.name}</span>
          <span class="topic-count">${topic.count.toLocaleString()}</span>
        </div>
      `;
    }).join('');
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Focus search input
    setTimeout(() => {
      document.getElementById('modalSearch').focus();
    }, 100);
  }

  function closeModal() {
    const modal = document.getElementById('modalOverlay');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    document.getElementById('modalSearch').value = '';
  }

  function closeModalOnOverlay(event) {
    if (event.target.id === 'modalOverlay') {
      closeModal();
    }
  }

  // Search functionality
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('modalSearch');
    if (searchInput) {
      searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const items = document.querySelectorAll('#modalTopicList .topic-item');
        
        items.forEach(item => {
          const topicName = item.getAttribute('data-topic-name');
          if (topicName.includes(searchTerm)) {
            item.style.display = 'flex';
          } else {
            item.style.display = 'none';
          }
        });
      });
    }
  });

  function showEmptyState(message) {
    const cloudHtml = `
      <div class="empty-state">
        <svg viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <h3>No Topics Found</h3>
        <p>${message}</p>
      </div>
    `;
    
    document.getElementById('wordCloudLoading').innerHTML = cloudHtml;
    document.getElementById('topicList').innerHTML = cloudHtml;
    
    document.getElementById('totalTopics').innerHTML = '<div class="stat-value">0</div>';
    document.getElementById('topTopic').innerHTML = '<div class="stat-value" style="font-size: 20px;">-</div>';
    document.getElementById('totalMentions').innerHTML = '<div class="stat-value">0</div>';
    document.getElementById('viewAllBtn').style.display = 'none';
  }
</script>
@endsection