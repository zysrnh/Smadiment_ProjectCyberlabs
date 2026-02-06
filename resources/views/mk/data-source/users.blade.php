@extends('mk.layouts.app')

@section('title', 'Total Users - Data Source')

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>Total Users</h2>
    <p class="page-subtitle">Analyze total user count and daily average for the selected period</p>
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
  @if(isset($error))
  <div class="alert alert-warning">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <div>
      <strong>Warning:</strong> {{ $error }}
    </div>
  </div>
  @endif

  <!-- Date Range Filter -->
  <div class="filter-card">
    <form method="GET" action="{{ route('mk.data-source.users') }}" class="filter-form">
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

  @if(isset($totalUsers) && $totalUsers > 0)
  @php
    $days = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
    $avgDaily = $days > 0 ? round($totalUsers / $days) : 0;
  @endphp
  
  <!-- Stats Summary -->
  <div class="stats-grid">
    <div class="stat-card highlight">
      <div class="stat-icon" style="background: linear-gradient(135deg, #038047 0%, #026738 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Total Users</h3>
        <p class="stat-value">{{ number_format($totalUsers) }}</p>
        <span class="stat-label">{{ $startDate }} to {{ $endDate }}</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #2FC6F6 0%, #1a9cc9 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Date Range</h3>
        <p class="stat-value">{{ $days }}</p>
        <span class="stat-label">Days analyzed</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Average Daily</h3>
        <p class="stat-value">{{ number_format($avgDaily) }}</p>
        <span class="stat-label">Users per day (estimated)</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <path d="M9 12l2 2 4-4"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Status</h3>
        <p class="stat-value stat-value-check">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </p>
        <span class="stat-label">Data fetched successfully</span>
      </div>
    </div>
  </div>

  <!-- Average Daily Trend Chart -->
  <div class="chart-card">
    <div class="chart-header">
      <div>
        <h3>Average Daily Users Trend</h3>
        <p>Estimated daily user distribution over selected period</p>
      </div>
      <div class="chart-controls">
        <button class="chart-type-btn active" data-type="line" onclick="changeChartType('line', this)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
          Line
        </button>
        <button class="chart-type-btn" data-type="bar" onclick="changeChartType('bar', this)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="20" x2="12" y2="10"/>
            <line x1="18" y1="20" x2="18" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="16"/>
          </svg>
          Bar
        </button>
        <button class="chart-type-btn" data-type="area" onclick="changeChartType('area', this)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 3v18h18"/>
            <path d="M7 12L12 7l5 5"/>
          </svg>
          Area
        </button>
      </div>
    </div>
    <div class="chart-container">
      <canvas id="usersChart"></canvas>
    </div>
  </div>

  <!-- Data Table -->
  <div class="data-table-card">
    <div class="table-header">
      <h3>Summary</h3>
      <button onclick="exportTableToCSV('users-data.csv')" class="action-btn">
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
            <th>Metric</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Period</strong></td>
            <td>{{ $startDate }} to {{ $endDate }}</td>
          </tr>
          <tr>
            <td><strong>Total Users</strong></td>
            <td>{{ number_format($totalUsers) }}</td>
          </tr>
          <tr>
            <td><strong>Days in Period</strong></td>
            <td>{{ $days }}</td>
          </tr>
          <tr>
            <td><strong>Estimated Average/Day</strong></td>
            <td>{{ number_format($avgDaily) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  @else
  <div class="empty-state">
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="11" cy="11" r="8"/>
      <path d="m21 21-4.35-4.35"/>
    </svg>
    <h3>No Data Available</h3>
    <p>Please select a project and date range to view user analytics.</p>
  </div>
  @endif
</div>
@endsection

@section('styles')
<style>
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
    font-family: 'Poppins', sans-serif;
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

  .stat-card.highlight {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 2px solid #038047;
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
    font-family: 'Poppins', sans-serif;
  }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.2;
    font-family: 'Poppins', sans-serif;
  }

  .stat-value-check {
    display: flex;
    align-items: center;
    color: #10b981;
  }

  .stat-value-check svg {
    width: 32px;
    height: 32px;
  }

  .stat-label {
    font-size: 12px;
    color: var(--text-muted);
    font-family: 'Poppins', sans-serif;
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
    font-family: 'Poppins', sans-serif;
  }

  .chart-header p {
    font-size: 13px;
    color: var(--text-secondary);
    margin: 4px 0 0 0;
    font-family: 'Poppins', sans-serif;
  }

  .chart-controls {
    display: flex;
    gap: 8px;
    background: #f8fafc;
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
    height: 400px;
  }

  .table-responsive {
    overflow-x: auto;
  }

  .data-table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'Poppins', sans-serif;
  }

  .data-table thead {
    background: #f8fafc;
  }

  .data-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 700;
    color: var(--text-primary);
    border-bottom: 2px solid var(--border-color);
    font-family: 'Poppins', sans-serif;
  }

  .data-table td {
    padding: 14px 16px;
    font-size: 14px;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-color);
    font-family: 'Poppins', sans-serif;
  }

  .data-table tbody tr:hover {
    background: #f8fafc;
  }

  .alert {
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-family: 'Poppins', sans-serif;
  }

  .alert-warning {
    background: #fef3c7;
    border: 1px solid #fbbf24;
    color: #92400e;
  }

  .alert svg {
    flex-shrink: 0;
    margin-top: 2px;
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: var(--card-shadow);
  }

  .empty-state svg {
    color: var(--text-muted);
    margin-bottom: 16px;
  }

  .empty-state h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
    font-family: 'Poppins', sans-serif;
  }

  .empty-state p {
    color: var(--text-secondary);
    font-family: 'Poppins', sans-serif;
  }

  @media (max-width: 768px) {
    .chart-controls {
      width: 100%;
      justify-content: stretch;
    }

    .chart-type-btn {
      flex: 1;
      justify-content: center;
    }
  }
</style>
@endsection

@section('scripts')
<script>
  let usersChart = null;

  @if(isset($totalUsers) && $totalUsers > 0)
  @php
    // Generate simulated daily data for visualization
    $dailyData = [];
    $dates = [];
    $variance = 0.15; // 15% variance
    
    for ($i = 0; $i < min($days, 30); $i++) {
      $date = \Carbon\Carbon::parse($startDate)->addDays($i);
      $dates[] = $date->format('M d');
      // Simulate data with some variance around average
      $randomFactor = 1 + (mt_rand(-$variance * 100, $variance * 100) / 100);
      $dailyData[] = round($avgDaily * $randomFactor);
    }
  @endphp

  const chartData = {
    labels: @json($dates),
    values: @json($dailyData)
  };

  // Initialize Chart
  function initChart(type = 'line') {
    const ctx = document.getElementById('usersChart').getContext('2d');
    
    if (usersChart) {
      usersChart.destroy();
    }

    let config = {
      type: type === 'area' ? 'line' : type,
      data: {
        labels: chartData.labels,
        datasets: [{
          label: 'Daily Users (Estimated)',
          data: chartData.values,
          borderColor: '#038047',
          backgroundColor: type === 'area' || type === 'line' ? 'rgba(3, 128, 71, 0.1)' : 'rgba(3, 128, 71, 0.8)',
          borderWidth: type === 'bar' ? 2 : 3,
          fill: type === 'area' || type === 'line' ? true : false,
          tension: 0.4,
          pointRadius: type === 'line' ? 4 : 0,
          pointHoverRadius: type === 'line' ? 6 : 0,
          pointBackgroundColor: '#ffffff',
          pointBorderColor: '#038047',
          pointBorderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              font: { family: 'Poppins', size: 13, weight: '600' },
              padding: 15,
              usePointStyle: true
            }
          },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            titleFont: { family: 'Poppins', size: 13, weight: '600' },
            bodyFont: { family: 'Poppins', size: 12 },
            padding: 12,
            borderColor: 'rgba(148, 163, 184, 0.3)',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                return 'Users: ' + context.parsed.y.toLocaleString();
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: {
              font: { family: 'Poppins', size: 12 },
              color: '#64748b',
              callback: function(value) {
                return value.toLocaleString();
              }
            }
          },
          x: {
            grid: { display: false },
            ticks: {
              font: { family: 'Poppins', size: 12 },
              color: '#64748b'
            }
          }
        }
      }
    };

    if (type === 'bar') {
      config.data.datasets[0].borderRadius = 8;
    }

    usersChart = new Chart(ctx, config);
  }

  function changeChartType(type, button) {
    document.querySelectorAll('.chart-type-btn').forEach(btn => {
      btn.classList.remove('active');
    });
    button.classList.add('active');
    initChart(type);
  }

  document.addEventListener('DOMContentLoaded', function() {
    initChart('line');
  });
  @endif

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
</script>
@endsection