@extends('mk.layouts.app')

@section('title', 'Total Authors - Data Source')

@section('content')
<div class="top-bar">
  <div class="page-title">
    <h2>Total Authors</h2>
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
          
          @if($mediaName === 'fb')
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          @elseif($mediaName === 'twit')
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
            </svg>
          @elseif($mediaName === 'instagram')
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
          @elseif($mediaName === 'youtube')
            <svg viewBox="0 0 24 24" fill="white" stroke="none">
              <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
          @else
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
            </svg>
          @endif
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

  <!-- Chart Card with Controls -->
  <div class="chart-card">
    <div class="chart-header">
      <div>
        <h3>Authors by Media Platform</h3>
        <p>Distribution of authors across different social media platforms</p>
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
        <button class="chart-type-btn" data-type="pie" onclick="changeChartType('pie', this)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
            <path d="M22 12A10 10 0 0 0 12 2v10z"/>
          </svg>
          Pie
        </button>
        <button class="chart-type-btn" data-type="doughnut" onclick="changeChartType('doughnut', this)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <circle cx="12" cy="12" r="6"/>
          </svg>
          Doughnut
        </button>
      </div>
    </div>
    <div class="chart-container">
      <canvas id="authorsChart"></canvas>
    </div>
  </div>

  <!-- Data Table -->
  <div class="data-table-card">
    <div class="table-header">
      <h3>Summary</h3>
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
              <td>
                <div class="media-name-cell">
                  <div class="media-icon-small">
                    @if($mediaName === 'fb')
                      <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                      </svg>
                    @elseif($mediaName === 'twit')
                      <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                      </svg>
                    @elseif($mediaName === 'instagram')
                      <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                      </svg>
                    @elseif($mediaName === 'youtube')
                      <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                      </svg>
                    @endif
                  </div>
                  {{ strtoupper($mediaName) }}
                </div>
              </td>
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

  .total-row {
    background: #f0f9ff !important;
    font-weight: 600;
  }

  .media-name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .media-icon-small {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .media-icon-small svg {
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
  }

  .badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
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
  let authorsChart = null;
  const chartData = {
    labels: @json($chartData['labels'] ?? []),
    values: @json($chartData['values'] ?? []),
    colors: {
      fb: '#1877F2',
      twit: '#1DA1F2',
      instagram: '#E4405F',
      youtube: '#FF0000',
      default: '#6B7280'
    }
  };

  @if(isset($chartData) && !empty($chartData['labels']))
  // Initialize Chart
  function initChart(type = 'bar') {
    const ctx = document.getElementById('authorsChart').getContext('2d');
    
    // Destroy existing chart if exists
    if (authorsChart) {
      authorsChart.destroy();
    }

    // Prepare colors based on labels
    const backgroundColors = chartData.labels.map(label => {
      const lowerLabel = label.toLowerCase();
      if (lowerLabel.includes('fb') || lowerLabel.includes('facebook')) return chartData.colors.fb + 'CC';
      if (lowerLabel.includes('twit') || lowerLabel.includes('twitter') || lowerLabel.includes('x')) return chartData.colors.twit + 'CC';
      if (lowerLabel.includes('instagram') || lowerLabel.includes('ig')) return chartData.colors.instagram + 'CC';
      if (lowerLabel.includes('youtube') || lowerLabel.includes('yt')) return chartData.colors.youtube + 'CC';
      return chartData.colors.default + 'CC';
    });

    const borderColors = chartData.labels.map(label => {
      const lowerLabel = label.toLowerCase();
      if (lowerLabel.includes('fb') || lowerLabel.includes('facebook')) return chartData.colors.fb;
      if (lowerLabel.includes('twit') || lowerLabel.includes('twitter') || lowerLabel.includes('x')) return chartData.colors.twit;
      if (lowerLabel.includes('instagram') || lowerLabel.includes('ig')) return chartData.colors.instagram;
      if (lowerLabel.includes('youtube') || lowerLabel.includes('yt')) return chartData.colors.youtube;
      return chartData.colors.default;
    });

    // Common configuration
    const commonOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: type === 'pie' || type === 'doughnut',
          position: 'right',
          labels: {
            font: { family: 'Poppins', size: 13 },
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
              const label = context.dataset.label || '';
              const value = context.parsed.y !== undefined ? context.parsed.y : context.parsed;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = ((value / total) * 100).toFixed(1);
              
              if (type === 'pie' || type === 'doughnut') {
                return ` ${context.label}: ${value.toLocaleString()} (${percentage}%)`;
              }
              return ` ${label}: ${value.toLocaleString()}`;
            }
          }
        }
      }
    };

    // Chart type specific configurations
    let chartConfig = {
      type: type,
      data: {
        labels: chartData.labels,
        datasets: [{
          label: 'Authors Count',
          data: chartData.values,
          backgroundColor: backgroundColors,
          borderColor: borderColors,
          borderWidth: 2
        }]
      },
      options: commonOptions
    };

    // Specific adjustments per chart type
    if (type === 'bar') {
      chartConfig.data.datasets[0].borderRadius = 8;
      chartConfig.options.scales = {
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
      };
    } else if (type === 'line') {
      chartConfig.data.datasets[0].tension = 0.4;
      chartConfig.data.datasets[0].fill = true;
      chartConfig.data.datasets[0].backgroundColor = 'rgba(3, 128, 71, 0.1)';
      chartConfig.data.datasets[0].borderColor = '#038047';
      chartConfig.data.datasets[0].pointBackgroundColor = '#038047';
      chartConfig.data.datasets[0].pointBorderColor = '#fff';
      chartConfig.data.datasets[0].pointBorderWidth = 2;
      chartConfig.data.datasets[0].pointRadius = 5;
      chartConfig.data.datasets[0].pointHoverRadius = 7;
      chartConfig.options.scales = {
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
      };
    } else if (type === 'doughnut') {
      chartConfig.options.cutout = '65%';
    }

    authorsChart = new Chart(ctx, chartConfig);
  }

  // Change Chart Type
  function changeChartType(type, button) {
    // Update active button
    document.querySelectorAll('.chart-type-btn').forEach(btn => {
      btn.classList.remove('active');
    });
    button.classList.add('active');

    // Reinitialize chart with new type
    initChart(type);
  }

  // Initialize on load
  document.addEventListener('DOMContentLoaded', function() {
    initChart('bar');
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