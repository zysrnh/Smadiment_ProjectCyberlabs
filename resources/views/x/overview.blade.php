@extends('layouts.mk')

@section('title', 'X Overview - SMADIMENT')

@section('styles')
<style>
  /* Stats Cards */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e2e8f0;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
  }

  .stat-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #038047 0%, #026738 100%);
  }

  .stat-icon svg {
    width: 24px;
    height: 24px;
    stroke: #ffffff;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .stat-label {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #27384A;
    margin-bottom: 8px;
  }

  .stat-change {
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 6px;
  }

  .stat-change.positive {
    color: #038047;
    background: rgba(3, 128, 71, 0.1);
  }

  .stat-change.negative {
    color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
  }

  /* Chart Containers */
  .charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
  }

  .chart-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
  }

  .chart-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f8fafc;
  }

  .chart-title {
    font-size: 18px;
    font-weight: 700;
    color: #27384A;
    margin-bottom: 4px;
  }

  .chart-subtitle {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
  }

  .chart-container {
    position: relative;
    height: 320px;
  }

  /* Loading States */
  .loading-skeleton {
    background: linear-gradient(90deg, #f8fafc 25%, #e2e8f0 50%, #f8fafc 75%);
    background-size: 200% 100%;
    animation: loading 1.5s ease-in-out infinite;
    border-radius: 8px;
  }

  @keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .skeleton-stat {
    height: 120px;
  }

  .skeleton-chart {
    height: 320px;
  }

  /* Date Range Filter */
  .filter-section {
    background: #ffffff;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
  }

  .filter-grid {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 16px;
    align-items: center;
  }

  .filter-label {
    font-size: 14px;
    font-weight: 600;
    color: #27384A;
  }

  .date-inputs {
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .date-input {
    padding: 10px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: #27384A;
    transition: all 0.2s;
  }

  .date-input:focus {
    outline: none;
    border-color: #038047;
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  .filter-btn {
    padding: 10px 24px;
    background: linear-gradient(135deg, #038047 0%, #026738 100%);
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }

  .filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 128, 71, 0.3);
  }

  /* Table Styles */
  .table-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    margin-bottom: 24px;
  }

  .data-table {
    width: 100%;
    border-collapse: collapse;
  }

  .data-table thead {
    background: #f8fafc;
  }

  .data-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e2e8f0;
  }

  .data-table td {
    padding: 16px;
    font-size: 14px;
    color: #27384A;
    border-bottom: 1px solid #f1f5f8;
  }

  .data-table tbody tr:hover {
    background: #f8fafc;
  }

  .rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
  }

  .rank-1 {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #ffffff;
  }

  .rank-2 {
    background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    color: #ffffff;
  }

  .rank-3 {
    background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%);
    color: #ffffff;
  }

  .rank-other {
    background: #f1f5f8;
    color: #64748b;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }

    .charts-grid {
      grid-template-columns: 1fr;
    }

    .filter-grid {
      grid-template-columns: 1fr;
    }

    .date-inputs {
      flex-direction: column;
      width: 100%;
    }

    .date-input {
      width: 100%;
    }
  }
</style>
@endsection

@section('content')
<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>X Overview</h2>
    <p class="page-subtitle">Monitor and analyze X (Twitter) social media metrics</p>
  </div>
</div>

<!-- Content Wrapper -->
<div class="content-wrapper">
  
  <!-- Date Range Filter -->
  <div class="filter-section">
    <form id="filterForm" method="GET" action="{{ route('mk.x.overview') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      
      <div class="filter-grid">
        <div class="filter-label">Date Range</div>
        
        <div class="date-inputs">
          <input type="date" 
                 name="start_date" 
                 id="startDate"
                 class="date-input" 
                 value="{{ $startDate }}"
                 max="{{ date('Y-m-d') }}">
          
          <span style="color: #64748b; font-weight: 600;">to</span>
          
          <input type="date" 
                 name="end_date" 
                 id="endDate"
                 class="date-input" 
                 value="{{ $endDate }}"
                 max="{{ date('Y-m-d') }}">
        </div>
        
        <button type="submit" class="filter-btn">Apply Filter</button>
      </div>
    </form>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid">
    <!-- Total Users -->
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div class="stat-label">Total Users</div>
      </div>
      <div id="totalUsersValue" class="stat-value">
        <div class="loading-skeleton" style="width: 120px; height: 40px;"></div>
      </div>
      <div id="totalUsersChange"></div>
    </div>

    <!-- Total Authors -->
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <div class="stat-label">Total Authors</div>
      </div>
      <div id="totalAuthorsValue" class="stat-value">
        <div class="loading-skeleton" style="width: 120px; height: 40px;"></div>
      </div>
      <div id="totalAuthorsChange"></div>
    </div>

    <!-- Volume Total -->
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
        </div>
        <div class="stat-label">Volume Total</div>
      </div>
      <div id="volumeTotalValue" class="stat-value">
        <div class="loading-skeleton" style="width: 120px; height: 40px;"></div>
      </div>
      <div id="volumeTotalChange"></div>
    </div>

    <!-- Sentiment Score -->
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
            <line x1="9" y1="9" x2="9.01" y2="9"/>
            <line x1="15" y1="9" x2="15.01" y2="9"/>
          </svg>
        </div>
        <div class="stat-label">Sentiment Score</div>
      </div>
      <div id="sentimentValue" class="stat-value">
        <div class="loading-skeleton" style="width: 120px; height: 40px;"></div>
      </div>
      <div id="sentimentChange"></div>
    </div>
  </div>

  <!-- Charts Grid -->
  <div class="charts-grid">
    <!-- Volume Trend Chart -->
    <div class="chart-card">
      <div class="chart-header">
        <div class="chart-title">Volume Trend</div>
        <div class="chart-subtitle">Daily posting volume over time</div>
      </div>
      <div class="chart-container">
        <div id="volumeTrendLoading" class="loading-skeleton skeleton-chart"></div>
        <canvas id="volumeTrendChart" style="display: none;"></canvas>
      </div>
    </div>

    <!-- Sentiment Distribution -->
    <div class="chart-card">
      <div class="chart-header">
        <div class="chart-title">Sentiment Distribution</div>
        <div class="chart-subtitle">Breakdown of positive, neutral, and negative sentiments</div>
      </div>
      <div class="chart-container">
        <div id="sentimentLoading" class="loading-skeleton skeleton-chart"></div>
        <canvas id="sentimentChart" style="display: none;"></canvas>
      </div>
    </div>
  </div>

  <!-- Top Hashtags Table -->
  <div class="table-card">
    <div class="chart-header">
      <div class="chart-title">Top Hashtags</div>
      <div class="chart-subtitle">Most frequently used hashtags</div>
    </div>
    <div id="hashtagsLoading" class="loading-skeleton" style="height: 300px;"></div>
    <div id="hashtagsTable" style="display: none;"></div>
  </div>

  <!-- Most Active Users Table -->
  <div class="table-card">
    <div class="chart-header">
      <div class="chart-title">Most Active Users</div>
      <div class="chart-subtitle">Users with highest posting frequency</div>
    </div>
    <div id="activeUsersLoading" class="loading-skeleton" style="height: 300px;"></div>
    <div id="activeUsersTable" style="display: none;"></div>
  </div>

</div>
@endsection

@section('scripts')
<script>
  const projectId = '{{ $projectId }}';
  const startDate = '{{ $startDate }}';
  const endDate = '{{ $endDate }}';

  // Utility: Format number with thousand separators
  function formatNumber(num) {
    return new Intl.NumberFormat('en-US').format(num);
  }

  // Utility: Calculate percentage change
  function calculateChange(current, previous) {
    if (!previous || previous === 0) return null;
    return ((current - previous) / previous * 100).toFixed(1);
  }

  // Load all data on page load
  document.addEventListener('DOMContentLoaded', function() {
    loadTotalUsers();
    loadTotalAuthors();
    loadVolumeTotal();
    loadSentimentTotal();
    loadTopHashtags();
    loadMostActiveUsers();
  });

  // API: Total Users
  async function loadTotalUsers() {
    try {
      const response = await fetch(`/mk/api/x/total-users?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      if (result.success && result.data) {
        const total = result.data.data?.total || result.data.total || 0;
        document.getElementById('totalUsersValue').innerHTML = formatNumber(total);
      } else {
        document.getElementById('totalUsersValue').innerHTML = '0';
      }
    } catch (error) {
      console.error('Error loading total users:', error);
      document.getElementById('totalUsersValue').innerHTML = 'Error';
    }
  }

  // API: Total Authors
  async function loadTotalAuthors() {
    try {
      const response = await fetch(`/mk/api/x/total-authors?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      if (result.success && result.data) {
        const total = result.data.data?.total || result.data.total || 0;
        document.getElementById('totalAuthorsValue').innerHTML = formatNumber(total);
      } else {
        document.getElementById('totalAuthorsValue').innerHTML = '0';
      }
    } catch (error) {
      console.error('Error loading total authors:', error);
      document.getElementById('totalAuthorsValue').innerHTML = 'Error';
    }
  }

  // API: Volume Total with Chart
  async function loadVolumeTotal() {
    try {
      const response = await fetch(`/mk/api/x/volume-total?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      if (result.success && result.data) {
        // Update stat card
        const total = result.data.data?.total || result.data.total || 0;
        document.getElementById('volumeTotalValue').innerHTML = formatNumber(total);

        // Render chart
        const chartData = result.data.data?.chart || result.data.chart || [];
        renderVolumeTrendChart(chartData);
      } else {
        document.getElementById('volumeTotalValue').innerHTML = '0';
      }
    } catch (error) {
      console.error('Error loading volume total:', error);
      document.getElementById('volumeTotalValue').innerHTML = 'Error';
    }
  }

  // API: Sentiment Total
  async function loadSentimentTotal() {
    try {
      const response = await fetch(`/mk/api/x/sentiment-total?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      if (result.success && result.data) {
        const positive = result.data.positive || 0;
        const neutral = result.data.neutral || 0;
        const negative = result.data.negative || 0;
        const total = positive + neutral + negative;
        
        // Calculate sentiment score (weighted average)
        const score = total > 0 ? ((positive * 100 + neutral * 50 + negative * 0) / total).toFixed(1) : 0;
        
        document.getElementById('sentimentValue').innerHTML = score + '%';
        
        // Render sentiment chart
        renderSentimentChart({ positive, neutral, negative });
      } else {
        document.getElementById('sentimentValue').innerHTML = '0%';
      }
    } catch (error) {
      console.error('Error loading sentiment:', error);
      document.getElementById('sentimentValue').innerHTML = 'Error';
    }
  }

  // API: Top Hashtags
  async function loadTopHashtags() {
    try {
      const response = await fetch(`/mk/api/x/top-hashtags?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      const container = document.getElementById('hashtagsTable');
      const loading = document.getElementById('hashtagsLoading');
      
      if (result.success && result.data && result.data.data) {
        const hashtags = result.data.data.slice(0, 10); // Top 10
        
        let html = '<table class="data-table"><thead><tr>';
        html += '<th>Rank</th><th>Hashtag</th><th>Frequency</th></tr></thead><tbody>';
        
        hashtags.forEach((item, index) => {
          const rankClass = index === 0 ? 'rank-1' : index === 1 ? 'rank-2' : index === 2 ? 'rank-3' : 'rank-other';
          html += `<tr>
            <td><span class="rank-badge ${rankClass}">${index + 1}</span></td>
            <td><strong>#${item.hashtag || item.name}</strong></td>
            <td>${formatNumber(item.freq || item.count || 0)}</td>
          </tr>`;
        });
        
        html += '</tbody></table>';
        container.innerHTML = html;
        
        loading.style.display = 'none';
        container.style.display = 'block';
      } else {
        container.innerHTML = '<p style="text-align: center; color: #64748b; padding: 40px;">No hashtag data available</p>';
        loading.style.display = 'none';
        container.style.display = 'block';
      }
    } catch (error) {
      console.error('Error loading hashtags:', error);
      document.getElementById('hashtagsTable').innerHTML = '<p style="text-align: center; color: #ef4444;">Error loading data</p>';
      document.getElementById('hashtagsLoading').style.display = 'none';
      document.getElementById('hashtagsTable').style.display = 'block';
    }
  }

  // API: Most Active Users
  async function loadMostActiveUsers() {
    try {
      const response = await fetch(`/mk/api/x/most-active-users?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      const container = document.getElementById('activeUsersTable');
      const loading = document.getElementById('activeUsersLoading');
      
      if (result.success && result.data && result.data.data) {
        const users = result.data.data.slice(0, 10); // Top 10
        
        let html = '<table class="data-table"><thead><tr>';
        html += '<th>Rank</th><th>Username</th><th>Posts</th></tr></thead><tbody>';
        
        users.forEach((item, index) => {
          const rankClass = index === 0 ? 'rank-1' : index === 1 ? 'rank-2' : index === 2 ? 'rank-3' : 'rank-other';
          html += `<tr>
            <td><span class="rank-badge ${rankClass}">${index + 1}</span></td>
            <td><strong>@${item.username || item.author || item.name}</strong></td>
            <td>${formatNumber(item.posts || item.count || 0)}</td>
          </tr>`;
        });
        
        html += '</tbody></table>';
        container.innerHTML = html;
        
        loading.style.display = 'none';
        container.style.display = 'block';
      } else {
        container.innerHTML = '<p style="text-align: center; color: #64748b; padding: 40px;">No user data available</p>';
        loading.style.display = 'none';
        container.style.display = 'block';
      }
    } catch (error) {
      console.error('Error loading active users:', error);
      document.getElementById('activeUsersTable').innerHTML = '<p style="text-align: center; color: #ef4444;">Error loading data</p>';
      document.getElementById('activeUsersLoading').style.display = 'none';
      document.getElementById('activeUsersTable').style.display = 'block';
    }
  }

  // Render Volume Trend Chart
  function renderVolumeTrendChart(data) {
    const canvas = document.getElementById('volumeTrendChart');
    const loading = document.getElementById('volumeTrendLoading');
    
    if (!data || data.length === 0) {
      loading.innerHTML = '<p style="text-align: center; color: #64748b; padding: 40px;">No volume data available</p>';
      return;
    }

    const ctx = canvas.getContext('2d');
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map(d => d.date),
        datasets: [{
          label: 'Volume',
          data: data.map(d => d.count || d.value || 0),
          borderColor: '#038047',
          backgroundColor: 'rgba(3, 128, 71, 0.1)',
          borderWidth: 3,
          tension: 0.4,
          fill: true,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: '#038047',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#27384A',
            padding: 12,
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#e2e8f0',
            borderWidth: 1
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f1f5f8' },
            ticks: { color: '#64748b', font: { family: 'Poppins', size: 12 } }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#64748b', font: { family: 'Poppins', size: 12 } }
          }
        }
      }
    });

    loading.style.display = 'none';
    canvas.style.display = 'block';
  }

  // Render Sentiment Chart
  function renderSentimentChart(sentiment) {
    const canvas = document.getElementById('sentimentChart');
    const loading = document.getElementById('sentimentLoading');
    
    const ctx = canvas.getContext('2d');
    
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Positive', 'Neutral', 'Negative'],
        datasets: [{
          data: [sentiment.positive, sentiment.neutral, sentiment.negative],
          backgroundColor: ['#038047', '#64748b', '#ef4444'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#27384A',
              font: { family: 'Poppins', size: 13, weight: '600' },
              padding: 16,
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            backgroundColor: '#27384A',
            padding: 12,
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((context.parsed / total) * 100).toFixed(1);
                return `${context.label}: ${formatNumber(context.parsed)} (${percentage}%)`;
              }
            }
          }
        }
      }
    });

    loading.style.display = 'none';
    canvas.style.display = 'block';
  }
</script>
@endsection