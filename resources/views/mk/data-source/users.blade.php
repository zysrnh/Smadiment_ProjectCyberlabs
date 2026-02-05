@extends('mk.layouts.app')

@section('title', 'Total Users - Data Source')

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>📊 Total Users</h2>
    <p class="page-subtitle">Analyze total user count for the selected period</p>
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
    <strong>⚠️ Warning:</strong> {{ $error }}
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
        @php
          $days = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        @endphp
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
        @php
          $avgDaily = $days > 0 ? round($totalUsers / $days) : 0;
        @endphp
        <p class="stat-value">{{ number_format($avgDaily) }}</p>
        <span class="stat-label">Users per day (estimated)</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Status</h3>
        <p class="stat-value">✓</p>
        <span class="stat-label">Data fetched successfully</span>
      </div>
    </div>
  </div>

  <!-- Chart Card -->
  <div class="chart-card">
    <div class="chart-header">
      <h3>📊 Total Users Overview</h3>
      <p>Aggregate user count for the selected period</p>
    </div>
    <div class="chart-container-small">
      <canvas id="usersChart"></canvas>
    </div>
  </div>

  <!-- Data Table -->
  <div class="data-table-card">
    <div class="table-header">
      <h3>📋 Summary</h3>
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

  .chart-container-small {
    position: relative;
    height: 300px;
  }

  .table-responsive {
    overflow-x: auto;
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

  .alert {
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .alert-warning {
    background: #fef3c7;
    border: 1px solid #fbbf24;
    color: #92400e;
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
  }

  .empty-state p {
    color: var(--text-secondary);
  }
</style>
@endsection

@section('scripts')
<script>
  @if(isset($chartData) && !empty($chartData['labels']))
  // Users Chart - Simple Bar
  const ctx = document.getElementById('usersChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: @json($chartData['labels']),
      datasets: [{
        label: 'Total Users',
        data: @json($chartData['values']),
        backgroundColor: 'rgba(3, 128, 71, 0.8)',
        borderColor: '#038047',
        borderWidth: 2,
        borderRadius: 8,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
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
  });
  @endif

  // Export to CSV
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