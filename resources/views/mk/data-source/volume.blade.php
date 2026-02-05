@extends('mk.layouts.app')

@section('title', 'Volume Total - Data Source')

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>📊 Volume Total</h2>
    <p class="page-subtitle">Track content volume and posting activity across media platforms</p>
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
    <form method="GET" action="{{ route('mk.data-source.volume') }}" class="filter-form">
      <input type="hidden" name="project_id" value="{{ $projectId ?? '' }}">
      
      <div class="filter-group">
        <label>Media Type</label>
        <select name="media" class="form-input">
          <option value="all" {{ ($media ?? 'all') === 'all' ? 'selected' : '' }}>All Media</option>
          <option value="doc" {{ ($media ?? '') === 'doc' ? 'selected' : '' }}>News/Online</option>
          <option value="twit" {{ ($media ?? '') === 'twit' ? 'selected' : '' }}>Twitter</option>
          <option value="fb" {{ ($media ?? '') === 'fb' ? 'selected' : '' }}>Facebook</option>
          <option value="instagram" {{ ($media ?? '') === 'instagram' ? 'selected' : '' }}>Instagram</option>
          <option value="youtube" {{ ($media ?? '') === 'youtube' ? 'selected' : '' }}>YouTube</option>
          <option value="tiktok" {{ ($media ?? '') === 'tiktok' ? 'selected' : '' }}>TikTok</option>
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

  @if(!empty($chartData['labels']))
  <!-- Stats Summary -->
  <div class="stats-grid">
    <div class="stat-card highlight">
      <div class="stat-icon" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Total Volume</h3>
        <p class="stat-value">{{ number_format($totalVolume) }}</p>
        <span class="stat-label">{{ $startDate }} to {{ $endDate }}</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Average Daily</h3>
        @php
          $days = count($chartData['labels']);
          $avgDaily = $days > 0 ? round($totalVolume / $days) : 0;
        @endphp
        <p class="stat-value">{{ number_format($avgDaily) }}</p>
        <span class="stat-label">Posts per day</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
          <polyline points="17 6 23 6 23 12"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Peak Volume</h3>
        <p class="stat-value">{{ number_format(max($chartData['values'])) }}</p>
        <span class="stat-label">Highest daily posts</span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
      </div>
      <div class="stat-content">
        <h3>Days Tracked</h3>
        <p class="stat-value">{{ count($chartData['labels']) }}</p>
        <span class="stat-label">Data points collected</span>
      </div>
    </div>
  </div>

  <!-- Media Breakdown -->
  @if(!empty($byMedia))
  <div class="media-breakdown-card">
    <h3>📱 Volume by Media Platform</h3>
    <div class="media-grid">
      @foreach($byMedia as $mediaName => $count)
      <div class="media-item">
        <div class="media-icon" style="background: 
          {{ $mediaName === 'doc' ? 'linear-gradient(135deg, #6B7280, #4B5563)' : '' }}
          {{ $mediaName === 'twit' ? 'linear-gradient(135deg, #1DA1F2, #0C85D0)' : '' }}
          {{ $mediaName === 'fb' ? 'linear-gradient(135deg, #1877F2, #166FE5)' : '' }}
          {{ $mediaName === 'instagram' ? 'linear-gradient(135deg, #E4405F, #C13584)' : '' }}
          {{ $mediaName === 'youtube' ? 'linear-gradient(135deg, #FF0000, #CC0000)' : '' }}
          {{ $mediaName === 'tiktok' ? 'linear-gradient(135deg, #000000, #EE1D52)' : '' }}
          {{ !in_array($mediaName, ['doc', 'twit', 'fb', 'instagram', 'youtube', 'tiktok']) ? 'linear-gradient(135deg, #6B7280, #4B5563)' : '' }}">
          <svg viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="2">
            @if($mediaName === 'doc')
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
            @elseif($mediaName === 'twit')
              <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
            @elseif($mediaName === 'fb')
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
            @elseif($mediaName === 'instagram')
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
            @elseif($mediaName === 'youtube')
              <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/>
            @elseif($mediaName === 'tiktok')
              <path d="M9 12a4 4 0 1 0 4 4V6a5 5 0 0 0 5 5"/>
            @else
              <circle cx="12" cy="12" r="10"/>
            @endif
          </svg>
        </div>
        <div class="media-info">
          <h4>{{ strtoupper($mediaName === 'doc' ? 'News/Online' : $mediaName) }}</h4>
          <p class="media-count">{{ number_format($count) }}</p>
          <span class="media-percentage">{{ number_format(($count / max($totalVolume, 1)) * 100, 1) }}%</span>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <!-- Chart Card -->
  <div class="chart-card">
    <div class="chart-header">
      <div>
        <h3>📈 Volume Trend</h3>
        <p>Daily posting activity over selected period</p>
      </div>
      <div class="chart-controls">
        <button id="toggleChartType" class="action-btn" style="font-size: 12px; padding: 8px 14px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <polyline points="19 12 12 19 5 12"/>
          </svg>
          Switch View
        </button>
      </div>
    </div>
    <div class="chart-container">
      <canvas id="volumeChart"></canvas>
    </div>
  </div>

  <!-- Data Table -->
  <div class="data-table-card">
    <div class="table-header">
      <h3>📋 Detailed Data</h3>
      <button onclick="exportTableToCSV('volume-data.csv')" class="action-btn">
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
            <th>Date</th>
            <th>Total Volume</th>
            <th>Change</th>
            <th>News</th>
            <th>Twitter</th>
            <th>Facebook</th>
            <th>Others</th>
          </tr>
        </thead>
        <tbody>
          @forelse($volumeData as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item['date'] ?? 'N/A' }}</td>
            <td><strong>{{ number_format($item['volume'] ?? 0) }}</strong></td>
            <td>
              @if($loop->index > 0)
                @php
                  $prev = $volumeData[$loop->index - 1]['volume'] ?? 0;
                  $current = $item['volume'] ?? 0;
                  $change = $prev > 0 ? (($current - $prev) / $prev) * 100 : 0;
                @endphp
                <span class="badge badge-{{ $change >= 0 ? 'success' : 'danger' }}">
                  {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 1) }}%
                </span>
              @else
                <span class="badge badge-secondary">-</span>
              @endif
            </td>
            <td>{{ number_format($item['by_media']['doc'] ?? 0) }}</td>
            <td>{{ number_format($item['by_media']['twit'] ?? 0) }}</td>
            <td>{{ number_format($item['by_media']['fb'] ?? 0) }}</td>
            <td>
              @php
                $others = 0;
                foreach (['youtube', 'instagram', 'tiktok'] as $m) {
                  $others += (int)($item['by_media'][$m] ?? 0);
                }
              @endphp
              {{ number_format($others) }}
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center">No data available</td>
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
    <p>Please select a project and date range to view volume analytics.</p>
  </div>
  @endif
</div>
@endsection

@section('styles')
<style>
  :root {
    --primary-blue: #0ea5e9;
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-muted: #94a3b8;
    --border-color: #e2e8f0;
    --card-shadow: 0 2px 8px rgba(0,0,0,0.06);
    --card-shadow-hover: 0 8px 24px rgba(0,0,0,0.12);
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
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
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
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 2px solid var(--primary-blue);
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

  .media-breakdown-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--card-shadow);
  }

  .media-breakdown-card h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 20px 0;
  }

  .media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
  }

  .media-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 10px;
    transition: all 0.2s;
  }

  .media-item:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
  }

  .media-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .media-icon svg {
    width: 24px;
    height: 24px;
  }

  .media-info h4 {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    margin: 0 0 4px 0;
  }

  .media-count {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    line-height: 1;
  }

  .media-percentage {
    font-size: 11px;
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

  .chart-controls {
    display: flex;
    gap: 8px;
  }

  .chart-container {
    position: relative;
    height: 420px;
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
    border-color: var(--primary-blue);
    color: var(--primary-blue);
  }

  .action-btn.primary {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
  }

  .action-btn.primary:hover {
    background: #0284c7;
    border-color: #0284c7;
  }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  let volumeChartInstance;
  let currentChartType = 'line'; // 'line' or 'bar'

  @if(!empty($chartData['labels']))
  
  function createVolumeChart(type = 'line') {
    const ctx = document.getElementById('volumeChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (volumeChartInstance) {
      volumeChartInstance.destroy();
    }

    const config = {
      type: type,
      data: {
        labels: @json($chartData['labels']),
        datasets: [{
          label: 'Total Volume',
          data: @json($chartData['values']),
          borderColor: '#0ea5e9',
          backgroundColor: type === 'line' ? 'rgba(14, 165, 233, 0.1)' : 'rgba(14, 165, 233, 0.8)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointRadius: 5,
          pointHoverRadius: 8,
          pointBackgroundColor: '#ffffff',
          pointBorderColor: '#0ea5e9',
          pointBorderWidth: 2.5,
        }]
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
              font: { family: 'Poppins', size: 13, weight: '600' },
              padding: 15,
              usePointStyle: true,
              pointStyle: 'circle'
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
                return 'Volume: ' + new Intl.NumberFormat().format(context.parsed.y);
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { 
              color: 'rgba(148, 163, 184, 0.1)',
              drawBorder: false
            },
            ticks: {
              font: { family: 'Poppins', size: 11 },
              color: '#64748b',
              padding: 8,
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
        },
        animation: {
          duration: 750,
          easing: 'easeInOutQuart'
        }
      }
    };

    volumeChartInstance = new Chart(ctx, config);
  }

  // Initialize chart
  createVolumeChart('line');

  // Toggle chart type
  document.getElementById('toggleChartType').addEventListener('click', function() {
    currentChartType = currentChartType === 'line' ? 'bar' : 'line';
    createVolumeChart(currentChartType);
    
    // Update button text
    const icon = currentChartType === 'line' ? '📊' : '📈';
    this.innerHTML = `
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19"/>
        <polyline points="19 12 12 19 5 12"/>
      </svg>
      ${currentChartType === 'line' ? 'Bar Chart' : 'Line Chart'}
    `;
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