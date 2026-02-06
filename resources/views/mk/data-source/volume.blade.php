@extends('mk.layouts.app')

@section('title', 'Volume Total - Data Source')

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>Volume Total</h2>
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
    <h3>Volume by Media Platform <span class="subtitle-hint">(Click to view trend)</span></h3>
    <div class="media-grid">
      @foreach($byMedia as $mediaName => $count)
      <div class="media-item clickable" onclick="filterChartByMedia('{{ $mediaName }}')" data-media="{{ $mediaName }}">
        <div class="media-icon" style="background: 
          {{ $mediaName === 'doc' ? 'linear-gradient(135deg, #3b82f6, #2563eb)' : '' }}
          {{ $mediaName === 'twit' ? 'linear-gradient(135deg, #1DA1F2, #0C85D0)' : '' }}
          {{ $mediaName === 'fb' ? 'linear-gradient(135deg, #1877F2, #166FE5)' : '' }}
          {{ $mediaName === 'instagram' ? 'linear-gradient(135deg, #E4405F, #C13584)' : '' }}
          {{ $mediaName === 'youtube' ? 'linear-gradient(135deg, #FF0000, #CC0000)' : '' }}
          {{ $mediaName === 'tiktok' ? 'linear-gradient(135deg, #000000, #EE1D52)' : '' }}
          {{ !in_array($mediaName, ['doc', 'twit', 'fb', 'instagram', 'youtube', 'tiktok']) ? 'linear-gradient(135deg, #6B7280, #4B5563)' : '' }}">
          
          @if($mediaName === 'doc')
            {{-- New Newspaper Icon --}}
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
            </svg>
          @elseif($mediaName === 'twit')
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
            </svg>
          @elseif($mediaName === 'fb')
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          @elseif($mediaName === 'instagram')
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
          @elseif($mediaName === 'youtube')
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
          @elseif($mediaName === 'tiktok')
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
            </svg>
          @else
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
            </svg>
          @endif
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
        <h3>Volume Trend <span id="chartMediaLabel" class="chart-filter-label"></span></h3>
        <p>Daily posting activity over selected period</p>
      </div>
      <div class="chart-header-actions">
        <button class="reset-btn" id="resetFilterBtn" onclick="resetChartFilter()" style="display: none;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="1 4 1 10 7 10"/>
            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
          </svg>
          Show All
        </button>
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
    </div>
    <div class="chart-container">
      <canvas id="volumeChart"></canvas>
    </div>
  </div>

  <!-- Data Table -->
  <div class="data-table-card">
    <div class="table-header">
      <h3>Detailed Data</h3>
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
            <th>Instagram</th>
            <th>YouTube</th>
            <th>TikTok</th>
          </tr>
        </thead>
        <tbody>
          @php
            // Fallback: Build table data from available sources
            $tableData = $volumeData ?? [];
            
            // If volumeData is empty but we have chartData, generate from it
            if (empty($tableData) && !empty($chartData['labels'])) {
              $tableData = [];
              foreach ($chartData['labels'] as $index => $label) {
                $tableData[] = [
                  'date' => $label,
                  'volume' => $chartData['values'][$index] ?? 0,
                  'by_media' => $chartData['by_media'][$index] ?? []
                ];
              }
            }
          @endphp
          
          @forelse($tableData as $item)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item['date'] ?? 'N/A' }}</td>
            <td><strong>{{ number_format($item['volume'] ?? 0) }}</strong></td>
            <td>
              @if($loop->index > 0)
                @php
                  $prev = $tableData[$loop->index - 1]['volume'] ?? 0;
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
            <td>{{ number_format($item['by_media']['instagram'] ?? 0) }}</td>
            <td>{{ number_format($item['by_media']['youtube'] ?? 0) }}</td>
            <td>{{ number_format($item['by_media']['tiktok'] ?? 0) }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center">No data available</td>
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
    border-color: #0ea5e9;
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
    border: 2px solid #0ea5e9;
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

  .stat-label {
    font-size: 12px;
    color: var(--text-muted);
    font-family: 'Poppins', sans-serif;
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
    font-family: 'Poppins', sans-serif;
  }

  .subtitle-hint {
    font-size: 12px;
    font-weight: 400;
    color: var(--text-muted);
    font-style: italic;
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
    transition: all 0.3s;
    position: relative;
  }

  .media-item.clickable {
    cursor: pointer;
  }

  .media-item.clickable:hover {
    background: #e0f2fe;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15);
  }

  .media-item.clickable:active {
    transform: translateY(-1px);
  }

  .media-item.active {
    background: #dbeafe;
    border: 2px solid #0ea5e9;
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
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
    font-family: 'Poppins', sans-serif;
  }

  .media-count {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    line-height: 1;
    font-family: 'Poppins', sans-serif;
  }

  .media-percentage {
    font-size: 11px;
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

  .chart-filter-label {
    font-size: 14px;
    font-weight: 500;
    color: #0ea5e9;
    margin-left: 8px;
  }

  .chart-header p {
    font-size: 13px;
    color: var(--text-secondary);
    margin: 4px 0 0 0;
    font-family: 'Poppins', sans-serif;
  }

  .chart-header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .reset-btn {
    padding: 8px 14px;
    border: 1px solid #e2e8f0;
    background: white;
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

  .reset-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--text-primary);
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
    background: #0ea5e9;
    color: white;
    box-shadow: 0 2px 8px rgba(14, 165, 233, 0.2);
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

  .badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
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

  .text-center {
    text-align: center;
  }

  @media (max-width: 768px) {
    .chart-header-actions {
      width: 100%;
      flex-direction: column-reverse;
    }

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
  let volumeChart = null;
  let currentChartType = 'line';
  let currentMediaFilter = null;

  @if(!empty($chartData['labels']))
  // Store full chart data with media breakdown
  const fullChartData = {
    labels: @json($chartData['labels']),
    values: @json($chartData['values']),
    byMedia: @json($chartData['by_media'] ?? [])
  };
  
  // Media color mapping
  const mediaColors = {
    'doc': { border: '#3b82f6', background: 'rgba(59, 130, 246, 0.1)' },
    'twit': { border: '#1DA1F2', background: 'rgba(29, 161, 242, 0.1)' },
    'fb': { border: '#1877F2', background: 'rgba(24, 119, 242, 0.1)' },
    'instagram': { border: '#E4405F', background: 'rgba(228, 64, 95, 0.1)' },
    'youtube': { border: '#FF0000', background: 'rgba(255, 0, 0, 0.1)' },
    'tiktok': { border: '#000000', background: 'rgba(0, 0, 0, 0.1)' }
  };

  function filterChartByMedia(mediaType) {
    currentMediaFilter = mediaType;
    
    // Update UI - highlight selected media
    document.querySelectorAll('.media-item').forEach(item => {
      item.classList.remove('active');
    });
    document.querySelector(`[data-media="${mediaType}"]`)?.classList.add('active');
    
    // Show reset button
    document.getElementById('resetFilterBtn').style.display = 'flex';
    
    // Update chart label
    const mediaLabels = {
      'doc': 'News/Online',
      'twit': 'Twitter',
      'fb': 'Facebook',
      'instagram': 'Instagram',
      'youtube': 'YouTube',
      'tiktok': 'TikTok'
    };
    document.getElementById('chartMediaLabel').textContent = `(${mediaLabels[mediaType] || mediaType})`;
    
    // Rebuild chart with filtered data
    initChart(currentChartType, mediaType);
  }

  function resetChartFilter() {
    currentMediaFilter = null;
    
    // Remove active state
    document.querySelectorAll('.media-item').forEach(item => {
      item.classList.remove('active');
    });
    
    // Hide reset button
    document.getElementById('resetFilterBtn').style.display = 'none';
    
    // Clear label
    document.getElementById('chartMediaLabel').textContent = '';
    
    // Rebuild chart with all data
    initChart(currentChartType);
  }

  function initChart(type = 'line', filterMedia = null) {
    const ctx = document.getElementById('volumeChart').getContext('2d');
    
    if (volumeChart) {
      volumeChart.destroy();
    }

    let chartLabels = fullChartData.labels;
    let chartValues = [];
    
    // Extract data based on filter
    if (filterMedia && fullChartData.byMedia && fullChartData.byMedia.length > 0) {
      // Filter by specific media
      chartValues = fullChartData.byMedia.map(mediaData => {
        return parseInt(mediaData[filterMedia] || 0);
      });
    } else {
      // Show total values
      chartValues = fullChartData.values;
    }

    // Get colors
    const colors = filterMedia && mediaColors[filterMedia] 
      ? mediaColors[filterMedia]
      : { border: '#0ea5e9', background: 'rgba(14, 165, 233, 0.1)' };

    let config = {
      type: type === 'area' ? 'line' : type,
      data: {
        labels: chartLabels,
        datasets: [{
          label: filterMedia ? `${filterMedia.toUpperCase()} Volume` : 'Total Volume',
          data: chartValues,
          borderColor: colors.border,
          backgroundColor: type === 'area' || type === 'line' ? colors.background : colors.border + 'CC',
          borderWidth: type === 'bar' ? 2 : 3,
          fill: type === 'area' || type === 'line' ? true : false,
          tension: 0.4,
          pointRadius: type === 'line' ? 5 : 0,
          pointHoverRadius: type === 'line' ? 8 : 0,
          pointBackgroundColor: '#ffffff',
          pointBorderColor: colors.border,
          pointBorderWidth: 2.5,
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
                return 'Volume: ' + context.parsed.y.toLocaleString();
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

    if (type === 'bar') {
      config.data.datasets[0].borderRadius = 8;
    }

    volumeChart = new Chart(ctx, config);
  }

  function changeChartType(type, button) {
    currentChartType = type;
    document.querySelectorAll('.chart-type-btn').forEach(btn => {
      btn.classList.remove('active');
    });
    button.classList.add('active');
    initChart(type, currentMediaFilter);
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