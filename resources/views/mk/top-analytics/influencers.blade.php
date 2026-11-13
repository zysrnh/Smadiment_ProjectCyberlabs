@extends('mk.layouts.app')

@section('title', 'Top Influencers - SMADIMENT')

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>👑 Top Influencers</h2>
    <p class="page-subtitle">Key opinion leaders and high-impact voices during selected period</p>
  </div>
  <div class="top-actions">
    <a href="{{ route('mk.dashboard') }}" class="action-btn">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
      </svg>
      Dashboard
    </a>
  </div>
</div>

<div class="content-wrapper">
  <!-- Date Range Filter -->
  <div class="filter-card">
    <form method="GET" action="{{ route('mk.top-analytics.influencers') }}" class="filter-form">
      <input type="hidden" name="project_id" value="{{ $projectId ?? '' }}">
      
      <div class="filter-group">
        <label>Start Date</label>
        <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="form-input">
      </div>

      <div class="filter-group">
        <label>End Date</label>
        <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="form-input">
      </div>

      <button type="submit" class="action-btn primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/>
          <path d="m21 21-4.35-4.35"/>
        </svg>
        Apply Filter
      </button>
    </form>
  </div>

  <!-- Stats Summary -->
  <div class="stats-grid" id="statsContainer">
    <div class="loading-container">
      <div class="loading-spinner"></div>
      <div class="loading-text">Loading influencers data...</div>
    </div>
  </div>

  <!-- Chart Card -->
  <div class="chart-card" id="chartContainer" style="display: none;">
    <div class="chart-header">
      <div>
        <h3>📊 Top Influencers by Reach</h3>
        <p>Highest estimated reach and follower accounts</p>
      </div>
      <div class="chart-controls">
        <button class="chart-type-btn active" data-type="bar" onclick="changeChartType('bar', this)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="20" x2="12" y2="10"/>
            <line x1="18" y1="20" x2="18" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="16"/>
          </svg>
          Bar
        </button>
        <button class="chart-type-btn" data-type="line" onclick="changeChartType('line', this)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
          Line
        </button>
      </div>
    </div>
    <div class="chart-container">
      <canvas id="influencersChart"></canvas>
    </div>
  </div>

  <!-- Influencers Table -->
  <div class="data-table-card" id="tableContainer" style="display: none;">
    <div class="table-header">
      <h3>📋 Influencer Rankings</h3>
    </div>
    <div class="table-responsive">
      <table class="data-table" id="dataTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Account / Name</th>
            <th>Followers / Reach</th>
            <th>Activity / Posts</th>
            <th>Profile</th>
          </tr>
        </thead>
        <tbody id="influencersTableBody">
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --text-primary: #27384A;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --border-color: #e2e8f0;
    --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
  }

  .filter-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--card-shadow);
  }

  .filter-form {
    display: flex;
    gap: 16px;
    align-items: end;
    flex-wrap: wrap;
  }

  .filter-group {
    flex: 1;
    min-width: 200px;
  }

  .filter-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 8px;
  }

  .form-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    transition: all 0.2s;
  }

  .form-input:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    gap: 16px;
    box-shadow: var(--card-shadow);
    transition: all 0.3s;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
  }

  .stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .stat-icon svg {
    width: 28px;
    height: 28px;
  }

  .stat-content h3 {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 4px;
  }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.2;
  }

  .stat-label {
    font-size: 12px;
    color: var(--text-muted);
  }

  .chart-card, .data-table-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--card-shadow);
  }

  .chart-header, .table-header {
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
  }

  .chart-header h3, .table-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
  }

  .chart-header p {
    font-size: 13px;
    color: var(--text-secondary);
    margin: 4px 0 0 0;
  }

  .chart-controls {
    display: flex;
    gap: 8px;
    background: #FFF5F8;
    padding: 4px;
    border-radius: 10px;
  }

  .chart-type-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .chart-type-btn:hover {
    background: white;
    color: var(--text-primary);
  }

  .chart-type-btn.active {
    background: var(--primary-green);
    color: white;
    box-shadow: 0 2px 8px rgba(3, 128, 71, 0.2);
  }

  .chart-container {
    position: relative;
    height: 450px;
  }

  .table-responsive {
    overflow-x: auto;
  }

  .data-table {
    width: 100%;
    border-collapse: collapse;
  }

  .data-table thead {
    background: #FFF5F8;
  }

  .data-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 700;
    color: var(--text-primary);
    border-bottom: 2px solid var(--border-color);
  }

  .data-table td {
    padding: 14px 16px;
    font-size: 14px;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-color);
  }

  .data-table tbody tr:hover {
    background: #f8fafc;
  }

  .loading-spinner {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 4px solid var(--border-color);
    border-top-color: var(--primary-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  .loading-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
    flex-direction: column;
    gap: 16px;
    grid-column: 1 / -1;
  }

  .loading-text {
    color: var(--text-secondary);
    font-weight: 600;
  }

  .action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
  }

  .action-btn:hover {
    background: #f8fafc;
    border-color: var(--primary-green);
    color: var(--primary-green);
  }

  .action-btn.primary {
    background: var(--primary-green);
    color: white;
    border-color: var(--primary-green);
  }

  .action-btn.primary:hover {
    background: #026838;
    border-color: #026838;
  }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  const projectId = '{{ $projectId ?? '' }}';
  const startDate = '{{ $startDate }}';
  const endDate = '{{ $endDate }}';

  let influencersChart = null;
  let currentChartType = 'bar';

  const colorPalette = [
    '#038047', '#2FC6F6', '#8b5cf6', '#f59e0b', '#ef4444',
    '#10b981', '#3b82f6', '#ec4899', '#6366f1', '#14b8a6'
  ];

  async function loadInfluencersData() {
    if (!projectId) {
      document.getElementById('statsContainer').innerHTML = `
        <div class="stat-card" style="grid-column: 1 / -1;">
          <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3>No Project Selected</h3>
            <p class="stat-value" style="font-size: 16px;">Please select a project from sidebar</p>
          </div>
        </div>
      `;
      return;
    }

    try {
      const response = await fetch(
        `/mk/api/top-influencers?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`
      );
      
      const result = await response.json();

      if (result.success && result.data) {
        renderStats(result.data);
        renderChart(result.data.chartData);
        renderTable(result.data.influencers);
      } else {
        throw new Error(result.message || 'Failed to load influencers data');
      }
    } catch (error) {
      console.error('Error loading influencers data:', error);
      document.getElementById('statsContainer').innerHTML = `
        <div class="stat-card" style="grid-column: 1 / -1;">
          <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <line x1="15" y1="9" x2="9" y2="15"/>
              <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
          </div>
          <div class="stat-content">
            <h3>Error</h3>
            <p class="stat-value" style="font-size: 14px;">${error.message}</p>
          </div>
        </div>
      `;
    }
  }

  function renderStats(data) {
    const total = data.total || 0;
    const influencers = data.influencers || [];
    const topInf = influencers[0] || null;
    const totalReach = influencers.reduce((sum, inf) => sum + (parseInt(inf.followers || inf.reach || inf.influence_score) || 0), 0);

    const topInfName = topInf ? (topInf.name || topInf.username || topInf.author || 'N/A') : 'N/A';
    const topInfReach = topInf ? (parseInt(topInf.followers || topInf.reach || topInf.influence_score) || 0) : 0;

    document.getElementById('statsContainer').innerHTML = `
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #038047 0%, #026738 100%);">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Influencers</h3>
          <p class="stat-value">${total.toLocaleString()}</p>
          <span class="stat-label">Tracked key voices</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M2 12h20"/>
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Combined Reach</h3>
          <p class="stat-value">${totalReach.toLocaleString()}</p>
          <span class="stat-label">Total audience exposure</span>
        </div>
      </div>

      ${topInf ? `
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Top Voice</h3>
          <p class="stat-value" style="font-size: 18px;">${topInfName}</p>
          <span class="stat-label">${topInfReach.toLocaleString()} followers / reach</span>
        </div>
      </div>
      ` : ''}
    `;
  }

  function renderChart(chartData) {
    if (!chartData || !chartData.labels || chartData.labels.length === 0) {
      document.getElementById('chartContainer').style.display = 'none';
      return;
    }

    document.getElementById('chartContainer').style.display = 'block';
    initChart(currentChartType, chartData);
  }

  function initChart(type, chartData) {
    const ctx = document.getElementById('influencersChart').getContext('2d');
    
    if (influencersChart) {
      influencersChart.destroy();
    }

    const config = {
      type: type,
      data: {
        labels: chartData.labels,
        datasets: [{
          label: 'Reach',
          data: chartData.values,
          backgroundColor: type === 'bar' ? colorPalette[0] : colorPalette[0] + '33',
          borderColor: colorPalette[0],
          borderWidth: 2,
          borderRadius: type === 'bar' ? 8 : 0,
          fill: type === 'line' ? false : true,
          tension: 0.4,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(39, 56, 74, 0.95)',
            titleFont: { family: 'Poppins', size: 13, weight: '600' },
            bodyFont: { family: 'Poppins', size: 12 },
            padding: 12,
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: {
              font: { family: 'Poppins', size: 11 },
              color: '#64748b',
              callback: function(value) {
                return new Intl.NumberFormat('en', { notation: 'compact' }).format(value);
              }
            }
          },
          x: {
            grid: { display: false },
            ticks: {
              font: { family: 'Poppins', size: 11 },
              color: '#64748b',
              maxRotation: 45,
              minRotation: 0
            }
          }
        }
      }
    };

    influencersChart = new Chart(ctx, config);
  }

  function renderTable(influencers) {
    if (!influencers || influencers.length === 0) {
      document.getElementById('tableContainer').style.display = 'none';
      return;
    }

    document.getElementById('tableContainer').style.display = 'block';

    const tbody = document.getElementById('influencersTableBody');
    tbody.innerHTML = influencers.map((item, index) => {
      const rawName = item.name || item.username || item.author || 'Unknown';
      const cleanUser = rawName.replace('@', '').trim();
      const reach = parseInt(item.followers || item.reach || item.influence_score) || 0;
      const posts = parseInt(item.post_count || item.posts || item.count) || 1;
      const profileUrl = `https://x.com/${cleanUser}`;

      return `
        <tr>
          <td><strong>#${index + 1}</strong></td>
          <td>
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="width:32px; height:32px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-weight:700; color:#038047; font-size:12px;">
                ${cleanUser.charAt(0).toUpperCase()}
              </div>
              <strong style="color: var(--text-primary);">${rawName.startsWith('@') ? rawName : '@' + rawName}</strong>
            </div>
          </td>
          <td><strong>${reach.toLocaleString()}</strong></td>
          <td>${posts.toLocaleString()} posts</td>
          <td>
            <a href="${profileUrl}" target="_blank" rel="noopener noreferrer" style="color:var(--primary-green); font-size:12px; font-weight:600; text-decoration:none;">
              Visit Profile &rarr;
            </a>
          </td>
        </tr>
      `;
    }).join('');
  }

  function changeChartType(type, button) {
    currentChartType = type;
    document.querySelectorAll('.chart-type-btn').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    
    const chartData = {
      labels: Array.from(document.querySelectorAll('#influencersTableBody tr')).map(row => row.cells[1].textContent.trim()),
      values: Array.from(document.querySelectorAll('#influencersTableBody tr')).map(row => parseInt(row.cells[2].textContent.replace(/[^\d]/g, '')))
    };
    
    initChart(type, chartData);
  }

  document.addEventListener('DOMContentLoaded', loadInfluencersData);
</script>
@endsection
