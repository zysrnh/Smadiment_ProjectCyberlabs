@extends('mk.layouts.app')

@section('title', 'Total Authors - Data Source')

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>✍️ Total Authors</h2>
    <p class="page-subtitle">Analyze author distribution and activity across media platforms</p>
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
    <form method="GET" action="{{ route('mk.data-source.authors') }}" class="filter-form">
      <input type="hidden" name="project_id" value="{{ $projectId ?? '' }}">
      
      <div class="filter-group">
        <label>Media Type</label>
        <select name="media" class="form-input">
          <option value="all" {{ ($media ?? 'all') === 'all' ? 'selected' : '' }}>All Media</option>
          <option value="fb" {{ ($media ?? '') === 'fb' ? 'selected' : '' }}>Facebook</option>
          <option value="twit" {{ ($media ?? '') === 'twit' ? 'selected' : '' }}>Twitter</option>
          <option value="instagram" {{ ($media ?? '') === 'instagram' ? 'selected' : '' }}>Instagram</option>
          <option value="youtube" {{ ($media ?? '') === 'youtube' ? 'selected' : '' }}>YouTube</option>
        </select>
      </div>

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

  @if(isset($totalAll) && $totalAll > 0)
  <!-- Stats Summary -->
  <div class="stats-grid">
    <div class="stat-card highlight">
      <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Total Authors</h3>
        <p class="stat-value">{{ number_format($totalAll) }}</p>
        <span class="stat-label">{{ $startDate }} to {{ $endDate }}</span>
      </div>
    </div>

    @if(!empty($byMedia))
      @foreach($byMedia as $mediaName => $count)
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, 
          {{ $mediaName === 'fb' ? '#1877F2, #166FE5' : '' }}
          {{ $mediaName === 'twit' ? '#1DA1F2, #0C85D0' : '' }}
          {{ $mediaName === 'instagram' ? '#E4405F, #C13584' : '' }}
          {{ $mediaName === 'youtube' ? '#FF0000, #CC0000' : '' }}
          {{ !in_array($mediaName, ['fb', 'twit', 'instagram', 'youtube']) ? '#6B7280, #4B5563' : '' }});">
          <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            @if($mediaName === 'fb')
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
            @elseif($mediaName === 'twit')
              <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
            @elseif($mediaName === 'instagram')
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
            @elseif($mediaName === 'youtube')
              <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/>
              <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>
            @else
              <circle cx="12" cy="12" r="10"/>
            @endif
          </svg>
        </div>
        <div class="stat-content">
          <h3>{{ strtoupper($mediaName) }} Authors</h3>
          <p class="stat-value">{{ number_format($count) }}</p>
          <span class="stat-label">{{ number_format(($count / $totalAll) * 100, 1) }}% of total</span>
        </div>
      </div>
      @endforeach
    @endif
  </div>

  <!-- Chart Card -->
  <div class="chart-card">
    <div class="chart-header">
      <h3>📊 Authors by Media Platform</h3>
      <p>Distribution of authors across different social media platforms</p>
    </div>
    <div class="chart-container">
      <canvas id="authorsChart"></canvas>
    </div>
  </div>

  <!-- Data Table -->
  <div class="data-table-card">
    <div class="table-header">
      <h3>📋 Summary</h3>
      <button onclick="exportTableToCSV('authors-data.csv')" class="action-btn">
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
            <th>Media Platform</th>
            <th>Total Authors</th>
            <th>Percentage</th>
          </tr>
        </thead>
        <tbody>
          <tr class="total-row">
            <td><strong>TOTAL (All Media)</strong></td>
            <td><strong>{{ number_format($totalAll) }}</strong></td>
            <td><strong>100%</strong></td>
          </tr>
          @if(!empty($byMedia))
            @foreach($byMedia as $mediaName => $count)
            <tr>
              <td>{{ strtoupper($mediaName) }}</td>
              <td>{{ number_format($count) }}</td>
              <td>
                <span class="badge badge-primary">
                  {{ number_format(($count / $totalAll) * 100, 1) }}%
                </span>
              </td>
            </tr>
            @endforeach
          @endif
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
    <p>Please select a project and date range to view authors analytics.</p>
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
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
    border: 2px solid #8b5cf6;
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

  .total-row {
    background: #f0f9ff !important;
    font-weight: 600;
  }

  .badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge-primary {
    background: #dbeafe;
    color: #1e40af;
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
  // Authors Distribution Chart
  const ctx = document.getElementById('authorsChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: @json($chartData['labels']),
      datasets: [{
        label: 'Authors Count',
        data: @json($chartData['values']),
        backgroundColor: [
          'rgba(24, 119, 242, 0.8)',   // Facebook Blue
          'rgba(29, 161, 242, 0.8)',   // Twitter Blue
          'rgba(228, 64, 95, 0.8)',    // Instagram Pink
          'rgba(255, 0, 0, 0.8)',      // YouTube Red
          'rgba(107, 114, 128, 0.8)',  // Other Gray
        ],
        borderColor: [
          '#1877F2',
          '#1DA1F2',
          '#E4405F',
          '#FF0000',
          '#6B7280',
        ],
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
              return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
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