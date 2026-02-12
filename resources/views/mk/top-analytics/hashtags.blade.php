@extends('mk.layouts.app')

@section('title', 'Top Hashtags - SMADIMENT')

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>🏷️ Top Hashtags</h2>
    <p class="page-subtitle">Most popular hashtags across social media platforms</p>
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
    <form method="GET" action="{{ route('mk.top-analytics.hashtags') }}" class="filter-form">
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
      <div class="loading-text">Loading hashtags data...</div>
    </div>
  </div>

  <!-- Chart Card -->
  <div class="chart-card" id="chartContainer" style="display: none;">
    <div class="chart-header">
      <div>
        <h3>📊 Top 10 Hashtags</h3>
        <p>Most mentioned hashtags during selected period</p>
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
      <canvas id="hashtagsChart"></canvas>
    </div>
  </div>

  <!-- Hashtags Table -->
  <div class="data-table-card" id="tableContainer" style="display: none;">
    <div class="table-header">
      <h3>📋 All Hashtags</h3>
      <button onclick="exportTableToCSV('hashtags-data.csv')" class="action-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Export CSV
      </button>
    </div>
    <div class="table-responsive">
      <table class="data-table" id="dataTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Hashtag</th>
            <th>Mentions</th>
            <th>Trend</th>
          </tr>
        </thead>
        <tbody id="hashtagsTableBody">
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
  const media = '{{ $media ?? 'all' }}';

  let hashtagsChart = null;
  let currentChartType = 'bar';

  const colorPalette = [
    '#038047', '#2FC6F6', '#8b5cf6', '#f59e0b', '#ef4444',
    '#10b981', '#3b82f6', '#ec4899', '#6366f1', '#14b8a6'
  ];

  async function loadHashtagsData() {
    if (!projectId) {
      document.getElementById('statsContainer').innerHTML = `
        <div class="stat-card" style="grid-column: 1 / -1;">
          <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
              <line x1="12" y1="9" x2="12" y2="13"/>
              <line x1="12" y1="17" x2="12.01" y2="17"/>
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
        `/mk/api/top-hashtags?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}&media=${media}`
      );
      
      const result = await response.json();
      console.log('API Response:', result);

      if (result.success && result.data) {
        renderStats(result.data);
        renderChart(result.data.chartData);
        renderTable(result.data.hashtags);
      } else {
        throw new Error(result.message || 'Failed to load data');
      }
    } catch (error) {
      console.error('Error loading hashtags data:', error);
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
    const hashtags = data.hashtags || [];
    const topHashtag = hashtags[0] || null;
    const totalMentions = hashtags.reduce((sum, h) => sum + (parseInt(h.size) || 0), 0);

    document.getElementById('statsContainer').innerHTML = `
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Hashtags</h3>
          <p class="stat-value">${total.toLocaleString()}</p>
          <span class="stat-label">Unique hashtags tracked</span>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Total Mentions</h3>
          <p class="stat-value">${totalMentions.toLocaleString()}</p>
          <span class="stat-label">Across all hashtags</span>
        </div>
      </div>

      ${topHashtag ? `
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
        <div class="stat-content">
          <h3>Top Hashtag</h3>
          <p class="stat-value" style="font-size: 18px;">#${topHashtag.name || 'N/A'}</p>
          <span class="stat-label">${(topHashtag.size || 0).toLocaleString()} mentions</span>
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
    const ctx = document.getElementById('hashtagsChart').getContext('2d');
    
    if (hashtagsChart) {
      hashtagsChart.destroy();
    }

    const config = {
      type: type,
      data: {
        labels: chartData.labels,
        datasets: [{
          label: 'Mentions',
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

    hashtagsChart = new Chart(ctx, config);
  }

  function renderTable(hashtags) {
    if (!hashtags || hashtags.length === 0) {
      document.getElementById('tableContainer').style.display = 'none';
      return;
    }

    document.getElementById('tableContainer').style.display = 'block';

    const tbody = document.getElementById('hashtagsTableBody');
    tbody.innerHTML = hashtags.map((item, index) => `
      <tr>
        <td><strong>#${index + 1}</strong></td>
        <td><strong style="color: var(--primary-green);">#${item.name || 'Unknown'}</strong></td>
        <td><strong>${(item.size || 0).toLocaleString()}</strong></td>
        <td>📈 Trending</td>
      </tr>
    `).join('');
  }

  function changeChartType(type, button) {
    currentChartType = type;
    document.querySelectorAll('.chart-type-btn').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    
    const chartData = {
      labels: Array.from(document.querySelectorAll('#hashtagsTableBody tr')).map(row => row.cells[1].textContent.trim()),
      values: Array.from(document.querySelectorAll('#hashtagsTableBody tr')).map(row => parseInt(row.cells[2].textContent.replace(/[^\d]/g, '')))
    };
    
    initChart(type, chartData);
  }

  function exportTableToCSV(filename) {
    const table = document.getElementById('dataTable');
    let csv = [];
    
    for (let row of table.rows) {
      let rowData = [];
      for (let cell of row.cells) {
        rowData.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
      }
      csv.push(rowData.join(','));
    }
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
  }

  document.addEventListener('DOMContentLoaded', loadHashtagsData);
</script>
@endsection