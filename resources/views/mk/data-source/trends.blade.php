@extends('mk.layouts.app')

@section('title', 'Trends Total - Data Source')

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>📈 Trends Total</h2>
    <p class="page-subtitle">Track trending topics and keyword performance over time</p>
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
    <form method="GET" action="{{ route('mk.data-source.trends') }}" class="filter-form">
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

  @if(!empty($chartData['labels']))
  <!-- Stats Summary -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
          <polyline points="17 6 23 6 23 12"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Active Trends</h3>
        <p class="stat-value">{{ count($chartData['datasets'] ?? []) }}</p>
        <span class="stat-label">Trending topics tracked</span>
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
        @php
          $totalMentions = 0;
          foreach($chartData['datasets'] ?? [] as $dataset) {
            $totalMentions += array_sum($dataset['data'] ?? []);
          }
        @endphp
        <p class="stat-value">{{ number_format($totalMentions) }}</p>
        <span class="stat-label">Across all trends</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Date Range</h3>
        <p class="stat-value">{{ count($chartData['labels'] ?? []) }}</p>
        <span class="stat-label">Days analyzed</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <path d="M12 20V10"/>
          <path d="M18 20V4"/>
          <path d="M6 20v-4"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Top Trend</h3>
        @php
          $topTrend = 'N/A';
          $maxMentions = 0;
          foreach($chartData['datasets'] ?? [] as $dataset) {
            $sum = array_sum($dataset['data'] ?? []);
            if ($sum > $maxMentions) {
              $maxMentions = $sum;
              $topTrend = $dataset['label'] ?? 'Unknown';
            }
          }
        @endphp
        <p class="stat-value" style="font-size: 18px;">{{ Str::limit($topTrend, 15) }}</p>
        <span class="stat-label">{{ number_format($maxMentions) }} mentions</span>
      </div>
    </div>
  </div>

  <!-- Chart Card -->
  <div class="chart-card">
    <div class="chart-header">
      <div>
        <h3>📊 Trends Timeline</h3>
        <p>Dual-axis chart: High volume (left) vs Low volume (right) for better visibility</p>
      </div>
      <div class="chart-legend-toggle">
        <button onclick="toggleAllDatasets()" class="action-btn" style="font-size: 12px; padding: 8px 14px;">
          Toggle All
        </button>
      </div>
    </div>
    <div class="chart-container">
      <canvas id="trendsChart"></canvas>
    </div>
  </div>

  <!-- Trends List Table -->
  <div class="data-table-card">
    <div class="table-header">
      <h3>📋 Trends Overview</h3>
      <button onclick="exportTableToCSV('trends-data.csv')" class="action-btn">
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
            <th>Keyword/Topic</th>
            <th>Total Mentions</th>
            <th>Peak Day</th>
            <th>Peak Count</th>
            <th>Trend</th>
          </tr>
        </thead>
        <tbody>
          @forelse($trendsData as $trend)
          @php
            $keyword = $trend['keyword'] ?? $trend['topic'] ?? 'Unknown';
            $trendData = $trend['data'] ?? [];
            $totalMentions = array_sum(array_column($trendData, 'count'));
            
            // Find peak day
            $peakCount = 0;
            $peakDay = 'N/A';
            foreach ($trendData as $point) {
              $count = $point['count'] ?? 0;
              if ($count > $peakCount) {
                $peakCount = $count;
                $peakDay = $point['date'] ?? 'N/A';
              }
            }
            
            // Calculate trend direction
            $firstHalf = array_slice($trendData, 0, ceil(count($trendData) / 2));
            $secondHalf = array_slice($trendData, ceil(count($trendData) / 2));
            $avgFirst = count($firstHalf) > 0 ? array_sum(array_column($firstHalf, 'count')) / count($firstHalf) : 0;
            $avgSecond = count($secondHalf) > 0 ? array_sum(array_column($secondHalf, 'count')) / count($secondHalf) : 0;
            $trendDirection = $avgSecond > $avgFirst ? 'up' : ($avgSecond < $avgFirst ? 'down' : 'stable');
            $trendPercentage = $avgFirst > 0 ? round((($avgSecond - $avgFirst) / $avgFirst) * 100, 1) : 0;
          @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td><strong>{{ $keyword }}</strong></td>
            <td>{{ number_format($totalMentions) }}</td>
            <td>{{ $peakDay }}</td>
            <td>{{ number_format($peakCount) }}</td>
            <td>
              @if($trendDirection === 'up')
                <span class="badge badge-success">
                  ↗️ +{{ abs($trendPercentage) }}%
                </span>
              @elseif($trendDirection === 'down')
                <span class="badge badge-danger">
                  ↘️ -{{ abs($trendPercentage) }}%
                </span>
              @else
                <span class="badge badge-secondary">
                  → Stable
                </span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center">No trends data available</td>
          </tr>
          @endforelse
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
    <p>Please select a project and date range to view trends analytics.</p>
  </div>
  @endif
</div>
@endsection

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --text-primary: #0f172a;
    --text-secondary: #475569;
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

  .badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge-success {
    background: #d1fae5;
    color: #065f46;
  }

  .badge-danger {
    background: #fee2e2;
    color: #991b1b;
  }

  .badge-secondary {
    background: #e2e8f0;
    color: var(--text-muted);
  }

  .alert {
    padding: 16px 20px;
    border-radius: 10px;
    margin-bottom: 24px;
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

  .text-center {
    text-align: center;
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

  .chart-legend-toggle {
    display: flex;
    gap: 8px;
  }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  let trendsChartInstance;

  @if(!empty($chartData['labels']) && !empty($chartData['datasets']))
  const colorPalette = [
    '#038047', '#2FC6F6', '#8b5cf6', '#f59e0b', '#ef4444',
    '#10b981', '#3b82f6', '#ec4899', '#6366f1', '#14b8a6'
  ];

  const datasets = @json($chartData['datasets']).map((dataset, index) => ({
    label: dataset.label,
    data: dataset.data,
    borderColor: colorPalette[index % colorPalette.length],
    backgroundColor: colorPalette[index % colorPalette.length] + '20',
    borderWidth: 2,
    fill: false,
    tension: 0.4,
    pointRadius: 4,
    pointHoverRadius: 6,
    pointBackgroundColor: '#ffffff',
    pointBorderWidth: 2,
  }));

  // 🔥 Pisahkan datasets berdasarkan magnitude untuk dual axis
  const rawDatasets = @json($chartData['datasets']);
  
  // Hitung max value untuk setiap dataset
  const datasetsWithMax = rawDatasets.map((dataset, index) => {
    const maxValue = Math.max(...dataset.data);
    return {
      ...dataset,
      maxValue: maxValue,
      originalIndex: index
    };
  });

  // Sort by max value descending
  datasetsWithMax.sort((a, b) => b.maxValue - a.maxValue);

  // Tentukan threshold (dataset dengan value > 1000 ke Y-axis kiri, sisanya ke kanan)
  const threshold = 500;
  const leftAxisDatasets = [];
  const rightAxisDatasets = [];

  datasetsWithMax.forEach((dataset, index) => {
    const color = colorPalette[dataset.originalIndex % colorPalette.length];
    const baseDataset = {
      label: dataset.label,
      data: dataset.data,
      borderColor: color,
      backgroundColor: color + '20',
      borderWidth: 2.5,
      fill: false,
      tension: 0.4,
      pointRadius: 5,
      pointHoverRadius: 7,
      pointBackgroundColor: '#ffffff',
      pointBorderWidth: 2.5,
    };

    if (dataset.maxValue > threshold) {
      leftAxisDatasets.push({ ...baseDataset, yAxisID: 'y-left' });
    } else {
      rightAxisDatasets.push({ ...baseDataset, yAxisID: 'y-right' });
    }
  });

  const allDatasets = [...leftAxisDatasets, ...rightAxisDatasets];

  const ctx = document.getElementById('trendsChart').getContext('2d');
  trendsChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: @json($chartData['labels']),
      datasets: allDatasets
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      plugins: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            font: { family: 'Poppins', size: 12, weight: '600' },
            padding: 12,
            usePointStyle: true,
            pointStyle: 'circle',
            generateLabels: function(chart) {
              const original = Chart.defaults.plugins.legend.labels.generateLabels(chart);
              return original.map((label, i) => {
                const dataset = chart.data.datasets[i];
                const isRightAxis = dataset.yAxisID === 'y-right';
                label.text = dataset.label + (isRightAxis ? ' ⚡' : '');
                return label;
              });
            }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.95)',
          titleFont: { family: 'Poppins', size: 13, weight: '600' },
          bodyFont: { family: 'Poppins', size: 12 },
          padding: 12,
          borderColor: 'rgba(148, 163, 184, 0.3)',
          borderWidth: 1,
          displayColors: true,
          callbacks: {
            label: function(context) {
              let label = context.dataset.label || '';
              if (label) {
                label += ': ';
              }
              label += new Intl.NumberFormat().format(context.parsed.y);
              return label;
            }
          }
        }
      },
      scales: {
        'y-left': {
          type: 'linear',
          position: 'left',
          beginAtZero: true,
          grid: { 
            color: 'rgba(148, 163, 184, 0.1)',
            drawOnChartArea: true
          },
          ticks: {
            font: { family: 'Poppins', size: 11 },
            color: '#64748b',
            callback: function(value) {
              return new Intl.NumberFormat('en', { notation: 'compact' }).format(value);
            }
          },
          title: {
            display: true,
            text: 'High Volume (DOC, TWIT)',
            font: { family: 'Poppins', size: 11, weight: '600' },
            color: '#475569'
          }
        },
        'y-right': {
          type: 'linear',
          position: 'right',
          beginAtZero: true,
          grid: {
            drawOnChartArea: false
          },
          ticks: {
            font: { family: 'Poppins', size: 11 },
            color: '#64748b',
            callback: function(value) {
              return new Intl.NumberFormat('en', { notation: 'compact' }).format(value);
            }
          },
          title: {
            display: true,
            text: 'Low Volume (FB, YT, IG, TikTok) ⚡',
            font: { family: 'Poppins', size: 11, weight: '600' },
            color: '#475569'
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
  });

  function toggleAllDatasets() {
    const chart = trendsChartInstance;
    const allVisible = chart.data.datasets.every(dataset => !dataset.hidden);
    
    chart.data.datasets.forEach((dataset) => {
      dataset.hidden = allVisible;
    });
    
    chart.update();
  }
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