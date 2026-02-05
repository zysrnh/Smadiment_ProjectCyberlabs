@extends('mk.layouts.app')

@section('title', 'Topic Map - SMADIMENT')

@section('styles')
<style>
  /* Import Poppins Font */
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

  * {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
  }

  .stat-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
  }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
  }

  .chart-container {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: var(--card-shadow);
    margin-bottom: 24px;
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
  }

  .section-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
  }

  .chart-switcher {
    display: flex;
    gap: 8px;
    background: #f1f5f9;
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
    font-size: 14px;
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

  /* Word Cloud Styles */
  .topic-cloud {
    min-height: 500px;
    position: relative;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  #wordCloudCanvas {
    width: 100% !important;
    height: 500px !important;
    cursor: default;
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

  .topic-list-container {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: var(--card-shadow);
  }

  .topic-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    transition: all 0.2s;
  }

  .topic-item:last-child {
    border-bottom: none;
  }

  .topic-item:hover {
    background: #f8fafc;
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
  }

  .view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(3, 128, 71, 0.3);
  }

  /* Modal Styles */
  .modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    backdrop-filter: blur(4px);
    animation: fadeIn 0.3s ease;
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
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
  }

  .modal-header {
    padding: 24px 32px;
    border-bottom: 1px solid #e2e8f0;
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
    background: #f1f5f9;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    color: #64748b;
    transition: all 0.2s;
  }

  .modal-close:hover {
    background: #e2e8f0;
    color: #334155;
  }

  .modal-body {
    padding: 24px 32px;
    overflow-y: auto;
    flex: 1;
  }

  .modal-search {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
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

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
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

  .loading-state {
    text-align: center;
    padding: 80px 20px;
    color: var(--text-muted);
  }

  .loading-state .spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #e2e8f0;
    border-top-color: var(--primary-green);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 20px;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-secondary);
  }

  .empty-state svg {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    opacity: 0.3;
  }
</style>
@endsection

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>Topic Map</h2>
    <p class="page-subtitle">Visualisasi topik yang sedang trending</p>
  </div>
</div>

<div class="content-wrapper">
  <!-- Stats Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-label">Total Topics</div>
      <div class="stat-value" id="totalTopics">-</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Top Topic</div>
      <div class="stat-value" id="topTopic" style="font-size: 18px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">-</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Mentions</div>
      <div class="stat-value" id="totalMentions">-</div>
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
        <canvas id="wordCloudCanvas"></canvas>
        <div class="loading-state" id="wordCloudLoading">
          <div class="spinner"></div>
          <p style="font-size: 16px; font-weight: 600;">Loading topics...</p>
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
    <div class="section-header">
      <h3 class="section-title">Topic Details</h3>
    </div>
    <div id="topicList">
      <div class="loading-state">
        <div class="spinner"></div>
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
      <button class="modal-close" onclick="closeModal()">&times;</button>
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
      document.getElementById('chartTitle').textContent = 'Topic Pie Chart';
      
      if (!pieChartInstance && topicsData.length > 0) {
        renderPieChart(topicsData);
      }
    }
  }

  function updateStats(topics) {
    const totalTopics = topics.length;
    const topTopic = topics[0]?.name || '-';
    const totalMentions = topics.reduce((sum, t) => sum + t.count, 0);

    document.getElementById('totalTopics').textContent = totalTopics.toLocaleString();
    document.getElementById('topTopic').textContent = topTopic;
    document.getElementById('topTopic').title = topTopic;
    document.getElementById('totalMentions').textContent = totalMentions.toLocaleString();
  }

  function renderWordCloud(topics) {
    const canvas = document.getElementById('wordCloudCanvas');
    const loading = document.getElementById('wordCloudLoading');
    
    if (!canvas) return;
    
    if (topics.length === 0) {
      loading.innerHTML = '<div class="empty-state">No topics to display</div>';
      return;
    }

    loading.style.display = 'none';
    
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
      '#038047',
      '#04995a',
      '#06bf80',
      '#059669',
      '#10b981',
      '#14b8a6',
      '#0891b2',
      '#0284c7',
      '#3b82f6',
      '#6366f1'
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
          backgroundColor: topTopics.map((_, i) => {
            const opacity = 1 - (i * 0.03);
            return `rgba(3, 128, 71, ${opacity})`;
          }),
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
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            padding: 12,
            titleFont: {
              size: 14,
              weight: 'bold',
              family: "'Poppins', sans-serif"
            },
            bodyFont: {
              size: 13,
              family: "'Poppins', sans-serif"
            },
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
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              font: {
                weight: 600,
                family: "'Poppins', sans-serif"
              }
            }
          },
          y: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                weight: 600,
                size: 12,
                family: "'Poppins', sans-serif"
              }
            }
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
      '#038047', '#04995a', '#2FC6F6', '#8b5cf6', '#f59e0b',
      '#ef4444', '#10b981', '#3b82f6', '#ec4899', '#6366f1',
      '#94a3b8'
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
              font: {
                size: 13,
                weight: 600,
                family: "'Poppins', sans-serif"
              },
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
            titleFont: {
              size: 14,
              weight: 'bold',
              family: "'Poppins', sans-serif"
            },
            bodyFont: {
              size: 13,
              family: "'Poppins', sans-serif"
            },
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
      listContainer.innerHTML = '<div class="empty-state">No topics to display</div>';
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
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <p style="font-size: 16px; font-weight: 600;">${message}</p>
      </div>
    `;
    
    document.getElementById('wordCloudLoading').innerHTML = cloudHtml;
    document.getElementById('topicList').innerHTML = cloudHtml;
    
    document.getElementById('totalTopics').textContent = '0';
    document.getElementById('topTopic').textContent = '-';
    document.getElementById('totalMentions').textContent = '0';
    document.getElementById('viewAllBtn').style.display = 'none';
  }
</script>
@endsection
