@extends('mk.layouts.app')

@section('title', 'News Mentions Timeline - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
    --text-primary: #1a202c;
    --text-secondary: #64748b;
    --bg-white: #ffffff;
    --bg-gray-50: #f8fafc;
    --bg-gray-100: #f1f5f9;
    --border-gray: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  }

  /* Main Layout */
  .dashboard-container {
    padding: 24px;
    background: var(--bg-gray-50);
    min-height: 100vh;
    max-width: 1600px;
    margin: 0 auto;
  }

  .page-header {
    margin-bottom: 32px;
  }

  .page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 8px 0;
  }

  .page-header p {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
  }

  /* Filter Card */
  .filter-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
  }

  .filter-content {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
  }

  .filter-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
  }

  .date-range-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
  }

  .apply-btn {
    padding: 12px 28px;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
  }

  .apply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
  }

  .apply-btn svg {
    width: 18px;
    height: 18px;
  }

  /* Date Picker */
  .date-picker-trigger {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    background: var(--bg-gray-50);
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
    max-width: 400px;
  }

  .date-picker-trigger:hover {
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    opacity: 0;
    transition: opacity 0.3s;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-green);
  }

  .stat-card:hover::before {
    opacity: 1;
  }

  .stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
  }

  .stat-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(3, 128, 71, 0.1) 0%, rgba(3, 128, 71, 0.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .stat-icon {
    width: 28px;
    height: 28px;
    color: var(--primary-green);
  }

  .stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
  }

  .stat-value {
    font-size: 36px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
  }

  .stat-progress {
    height: 6px;
    background: var(--bg-gray-100);
    border-radius: 10px;
    overflow: hidden;
    margin-top: 16px;
  }

  .stat-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    border-radius: 10px;
    transition: width 1s ease-out;
  }

  /* Charts Section */
  .charts-section {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-bottom: 24px;
  }

  @media (min-width: 1024px) {
    .charts-section {
      grid-template-columns: 1.5fr 1fr;
    }
  }

  .chart-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
  }

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--bg-gray-50);
  }

  .chart-title-group h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 6px 0;
  }

  .chart-subtitle {
    font-size: 13px;
    color: var(--text-secondary);
    font-weight: 500;
  }

  .chart-container {
    position: relative;
    height: 320px;
  }

  /* Timeline Section */
  .timeline-section {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
    margin-bottom: 24px;
  }

  .timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--bg-gray-50);
  }

  .timeline-title h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 6px 0;
  }

  .timeline-subtitle {
    font-size: 13px;
    color: var(--text-secondary);
  }

  .timeline-search {
    position: relative;
    width: 280px;
  }

  .timeline-search input {
    width: 100%;
    padding: 10px 16px 10px 44px;
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    background: var(--bg-gray-50);
    transition: all 0.2s;
  }

  .timeline-search input:focus {
    outline: none;
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  .timeline-search svg {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
  }

  /* Timeline Item */
  .timeline-list {
    position: relative;
  }

  .timeline-item {
    position: relative;
    padding-left: 32px;
    padding-bottom: 24px;
    margin-bottom: 24px;
    border-left: 2px solid var(--border-gray);
  }

  .timeline-item:last-child {
    border-left-color: transparent;
    margin-bottom: 0;
  }

  .timeline-dot {
    position: absolute;
    left: -7px;
    top: 8px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--primary-green);
    border: 2px solid var(--bg-white);
    box-shadow: 0 0 0 2px var(--primary-green);
  }

  .timeline-content {
    background: var(--bg-gray-50);
    border-radius: 12px;
    padding: 16px;
    border: 1px solid var(--border-gray);
    transition: all 0.2s;
  }

  .timeline-content:hover {
    background: var(--bg-white);
    box-shadow: var(--shadow-sm);
  }

  .timeline-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    flex-wrap: wrap;
  }

  .timeline-date {
    font-size: 12px;
    color: var(--text-secondary);
    font-weight: 600;
  }

  .timeline-source {
    padding: 4px 10px;
    background: var(--primary-green);
    color: white;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
  }

  .timeline-text {
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-primary);
    margin-bottom: 12px;
  }

  .timeline-author {
    font-size: 12px;
    color: var(--text-secondary);
  }

  .timeline-author strong {
    color: var(--text-primary);
    font-weight: 600;
  }

  /* Pagination */
  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 20px;
    background: var(--bg-white);
    border-top: 1px solid var(--border-gray);
    margin-top: 16px;
  }

  .pagination-btn {
    padding: 8px 16px;
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.2s;
  }

  .pagination-btn:hover:not(:disabled) {
    background: var(--primary-green);
    color: white;
    border-color: var(--primary-green);
  }

  .pagination-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
  }

  .pagination-info {
    font-size: 13px;
    color: var(--text-secondary);
    font-weight: 500;
  }

  /* Loading */
  .loading-skeleton {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s ease-in-out infinite;
    border-radius: 8px;
  }

  @keyframes loading {
    0% {
      background-position: 200% 0;
    }
    100% {
      background-position: -200% 0;
    }
  }

  .skeleton-text {
    height: 44px;
    margin-bottom: 8px;
  }

  .lazy-loading-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: rgba(3, 128, 71, 0.1);
    color: var(--primary-green);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 10;
  }

  .spinner {
    width: 14px;
    height: 14px;
    border: 2px solid rgba(3, 128, 71, 0.2);
    border-top-color: var(--primary-green);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  [data-lazy-load] {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
  }

  [data-lazy-load].loaded {
    opacity: 1;
    transform: translateY(0);
  }

  .data-loaded {
    animation: fadeIn 0.4s ease-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  /* Alert */
  .alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .alert-warning {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .dashboard-container {
      padding: 16px;
    }

    .stats-grid {
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
    }

    .filter-content {
      flex-direction: column;
      align-items: stretch;
    }

    .date-range-wrapper {
      flex-direction: column;
    }

    .apply-btn {
      width: 100%;
      justify-content: center;
    }
  }

  @media (max-width: 768px) {
    .stat-value {
      font-size: 28px;
    }

    .chart-container {
      height: 250px;
    }

    .timeline-search {
      width: 100%;
    }

    .page-header h1 {
      font-size: 24px;
    }

    .date-picker-trigger {
      max-width: 100%;
    }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>News Mentions Timeline</h1>
    <p>Track news mentions over time with volume trends and peak hours analysis</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view timeline data.</span>
  </div>
  @else

  <!-- Date Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.news.timeline') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">

      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; display: inline; vertical-align: middle; margin-right: 6px; stroke: currentColor; fill: none;">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          Date Range
        </div>

        <div class="date-range-wrapper">
          <button type="button" class="date-picker-trigger" id="datePickerTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="dateRangeDisplay">{{ $startDate }} to {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
        </div>

        <button type="submit" class="apply-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
          Apply Filter
        </button>
      </div>
    </form>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid">

    <!-- Total Mentions -->
    <div class="stat-card" data-lazy-load="stats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Total Mentions</div>
      
      <div id="statTotalMentions" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Peak Hour -->
    <div class="stat-card" data-lazy-load="stats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Peak Hour</div>
      
      <div id="statPeakHour" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Avg Per Day -->
    <div class="stat-card" data-lazy-load="stats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Avg Per Day</div>
      
      <div id="statAvgPerDay" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

  </div>

  <!-- Charts Section -->
  <div class="charts-section">
    
    <!-- Volume Over Time Chart -->
    <div class="chart-card" data-lazy-load="volumeChart">
      <div class="chart-header">
        <div class="chart-title-group">
          <h3>Volume Over Time</h3>
          <p class="chart-subtitle">Daily news mentions count</p>
        </div>
      </div>
      
      <div class="chart-container">
        <div id="volumeChartLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="volumeOverTimeChart" style="display: none;"></canvas>
      </div>
    </div>

    <!-- Peak Hours Chart -->
    <div class="chart-card" data-lazy-load="peakHoursChart">
      <div class="chart-header">
        <div class="chart-title-group">
          <h3>Peak Hours Analysis</h3>
          <p class="chart-subtitle">Hourly distribution (24h)</p>
        </div>
      </div>
      
      <div class="chart-container">
        <div id="peakHoursChartLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="peakHoursChart" style="display: none;"></canvas>
      </div>
    </div>

  </div>

  <!-- Timeline Section -->
  <div class="timeline-section" data-lazy-load="timeline">
    <div class="timeline-header">
      <div class="timeline-title">
        <h3>News Mentions Timeline</h3>
        <p class="timeline-subtitle">Chronological list of all news mentions</p>
      </div>

      <div class="timeline-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/>
          <path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="timelineSearchInput" placeholder="Search mentions..." onkeyup="filterTimeline()">
      </div>
    </div>

    <div id="timelineLoading" class="loading-skeleton" style="height: 400px;"></div>
    <div id="timelineContent" style="display: none;"></div>

    <!-- Pagination -->
    <div class="pagination" id="pagination" style="display: none;">
      <button class="pagination-btn" id="prevBtn" onclick="changePage(-1)">
        ← Previous
      </button>
      <span class="pagination-info" id="pageInfo">Page 1 of 1</span>
      <button class="pagination-btn" id="nextBtn" onclick="changePage(1)">
        Next →
      </button>
    </div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ========================================
// MAIN LOGIC
// ========================================
const projectId = '{{ $projectId ?? '' }}';
const startDate = '{{ $startDate ?? '' }}';
const endDate = '{{ $endDate ?? '' }}';

let allMentions = [];
let currentPage = 1;
let mentionsPerPage = 10;

function formatNumber(num) {
  return new Intl.NumberFormat('en-US').format(num);
}

function formatDate(dateStr) {
  if (!dateStr) return '—';
  try {
    return new Date(dateStr).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  } catch (e) {
    return dateStr;
  }
}

if (projectId && startDate && endDate) {
  
  // Lazy loading setup
  const lazyLoadConfig = {
    rootMargin: '50px',
    threshold: 0.01
  };

  const loadedComponents = new Set();

  const lazyLoadObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const componentId = entry.target.dataset.lazyLoad;
        
        if (!loadedComponents.has(componentId)) {
          loadedComponents.add(componentId);
          
          if (componentId === 'stats' || componentId === 'timeline') {
            loadMentions();
          }
          if (componentId === 'volumeChart') {
            loadVolumeChart();
          }
          if (componentId === 'peakHoursChart') {
            loadPeakHoursChart();
          }
          
          lazyLoadObserver.unobserve(entry.target);
        }
      }
    });
  }, lazyLoadConfig);

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-lazy-load]').forEach(element => {
      lazyLoadObserver.observe(element);
    });
  });

  function addLoadingBadge(card) {
    if (!card || card.querySelector('.lazy-loading-badge')) return;
    
    const badge = document.createElement('div');
    badge.className = 'lazy-loading-badge';
    badge.innerHTML = '<div class="spinner"></div><span>Loading...</span>';
    card.style.position = 'relative';
    card.appendChild(badge);
  }

  function removeLoadingBadge(card) {
    if (!card) return;
    const badge = card.querySelector('.lazy-loading-badge');
    if (badge) {
      badge.style.opacity = '0';
      setTimeout(() => badge.remove(), 300);
    }
  }

  function animateProgress(card, percentage) {
    const progressBar = card.querySelector('.stat-progress-bar');
    if (progressBar) {
      setTimeout(() => {
        progressBar.style.width = percentage + '%';
      }, 100);
    }
  }

  // ─── Load Mentions Data ───────────────────────────────
  let mentionsLoaded = false;

  async function loadMentions() {
    if (mentionsLoaded) return;
    mentionsLoaded = true;

    const statCards = document.querySelectorAll('[data-lazy-load="stats"]');
    statCards.forEach(c => addLoadingBadge(c));

    try {
      // Fetch news mentions with filter media='news'
      const response = await fetch(`/mk/api/news/mentions?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();

      if (result.success && result.data && result.data.length > 0) {
        allMentions = result.data;

        // Calculate stats
        const totalMentions = allMentions.length;
        
        // Calculate peak hour
        const hourCounts = {};
        allMentions.forEach(m => {
          const date = new Date(m.date_created);
          const hour = date.getHours();
          hourCounts[hour] = (hourCounts[hour] || 0) + 1;
        });
        
        let peakHour = 0;
        let maxCount = 0;
        Object.entries(hourCounts).forEach(([hour, count]) => {
          if (count > maxCount) {
            maxCount = count;
            peakHour = parseInt(hour);
          }
        });

        // Calculate average per day
        const dateRange = new Date(endDate) - new Date(startDate);
        const days = Math.ceil(dateRange / (1000 * 60 * 60 * 24)) + 1;
        const avgPerDay = Math.round(totalMentions / days);

        // Update stats
        document.getElementById('statTotalMentions').innerHTML = `<div class="stat-value">${formatNumber(totalMentions)}</div>`;
        document.getElementById('statPeakHour').innerHTML = `<div class="stat-value">${peakHour}:00</div>`;
        document.getElementById('statAvgPerDay').innerHTML = `<div class="stat-value">${formatNumber(avgPerDay)}</div>`;

        document.getElementById('statTotalMentions').classList.add('data-loaded');
        document.getElementById('statPeakHour').classList.add('data-loaded');
        document.getElementById('statAvgPerDay').classList.add('data-loaded');

        statCards.forEach((c, i) => {
          const pcts = [90, 75, 65];
          animateProgress(c, pcts[i] ?? 70);
        });

        // Render timeline
        renderTimeline();
        updatePagination();
        
        document.getElementById('timelineLoading').style.display = 'none';
        document.getElementById('timelineContent').style.display = 'block';
        document.getElementById('pagination').style.display = 'flex';

      } else {
        ['statTotalMentions', 'statPeakHour', 'statAvgPerDay'].forEach(id => {
          document.getElementById(id).innerHTML = '<div class="stat-value">0</div>';
        });
      }

    } catch (error) {
      console.error('Error loading mentions:', error);
    } finally {
      statCards.forEach(c => {
        removeLoadingBadge(c);
        c.classList.add('loaded');
      });
      document.querySelector('[data-lazy-load="timeline"]')?.classList.add('loaded');
    }
  }

  // ─── Load Volume Chart ────────────────────────────────
  async function loadVolumeChart() {
    const card = document.querySelector('[data-lazy-load="volumeChart"]');
    addLoadingBadge(card);

    try {
      const response = await fetch(`/mk/api/news/mentions?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();

      if (result.success && result.data) {
        // Group by date
        const dateCounts = {};
        result.data.forEach(m => {
          const date = new Date(m.date_created).toISOString().split('T')[0];
          dateCounts[date] = (dateCounts[date] || 0) + 1;
        });

        // Create chart data
        const sortedDates = Object.keys(dateCounts).sort();
        const chartData = sortedDates.map(date => ({
          date,
          count: dateCounts[date]
        }));

        renderVolumeChart(chartData);
      }
    } catch (error) {
      console.error('Error loading volume chart:', error);
    } finally {
      removeLoadingBadge(card);
      card.classList.add('loaded');
    }
  }

  function renderVolumeChart(data) {
    const canvas = document.getElementById('volumeOverTimeChart');
    const loading = document.getElementById('volumeChartLoading');
    
    const ctx = canvas.getContext('2d');
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map(d => d.date),
        datasets: [{
          label: 'Mentions',
          data: data.map(d => d.count),
          borderColor: '#038047',
          backgroundColor: 'rgba(3, 128, 71, 0.1)',
          borderWidth: 3,
          tension: 0.4,
          fill: true,
          pointRadius: 5,
          pointHoverRadius: 7,
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
            backgroundColor: '#1a202c',
            padding: 16,
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            titleFont: { size: 14, weight: '600' },
            bodyFont: { size: 13 },
            borderColor: '#e2e8f0',
            borderWidth: 1,
            displayColors: false,
            cornerRadius: 8
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9', drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 12 },
              padding: 8
            }
          },
          x: {
            grid: { display: false, drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 12 },
              padding: 8
            }
          }
        }
      }
    });

    loading.style.display = 'none';
    canvas.style.display = 'block';
  }

  // ─── Load Peak Hours Chart ────────────────────────────
  async function loadPeakHoursChart() {
    const card = document.querySelector('[data-lazy-load="peakHoursChart"]');
    addLoadingBadge(card);

    try {
      const response = await fetch(`/mk/api/news/mentions?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();

      if (result.success && result.data) {
        // Group by hour
        const hourCounts = new Array(24).fill(0);
        result.data.forEach(m => {
          const date = new Date(m.date_created);
          const hour = date.getHours();
          hourCounts[hour]++;
        });

        renderPeakHoursChart(hourCounts);
      }
    } catch (error) {
      console.error('Error loading peak hours chart:', error);
    } finally {
      removeLoadingBadge(card);
      card.classList.add('loaded');
    }
  }

  function renderPeakHoursChart(hourCounts) {
    const canvas = document.getElementById('peakHoursChart');
    const loading = document.getElementById('peakHoursChartLoading');
    
    const ctx = canvas.getContext('2d');
    
    const labels = Array.from({ length: 24 }, (_, i) => `${i}:00`);
    
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Mentions',
          data: hourCounts,
          backgroundColor: 'rgba(3, 128, 71, 0.8)',
          borderColor: '#038047',
          borderWidth: 1,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1a202c',
            padding: 16,
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            titleFont: { size: 14, weight: '600' },
            bodyFont: { size: 13 },
            displayColors: false,
            cornerRadius: 8
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9', drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 12 },
              padding: 8
            }
          },
          x: {
            grid: { display: false, drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 11 },
              padding: 8,
              maxRotation: 45,
              minRotation: 45
            }
          }
        }
      }
    });

    loading.style.display = 'none';
    canvas.style.display = 'block';
  }

  // ─── Render Timeline ──────────────────────────────────
  function renderTimeline() {
    const startIdx = (currentPage - 1) * mentionsPerPage;
    const endIdx = startIdx + mentionsPerPage;
    const currentData = allMentions.slice(startIdx, endIdx);

    let html = '<div class="timeline-list">';

    currentData.forEach(item => {
      const content = item.content || 'No content';
      const date = formatDate(item.date_created);
      const author = item.author_scr_name || item.author_name || 'Unknown';
      const source = item.hostname || 'News';

      html += `
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <div class="timeline-meta">
              <span class="timeline-date">${date}</span>
              <span class="timeline-source">${source}</span>
            </div>
            <div class="timeline-text">${content}</div>
            <div class="timeline-author">
              By <strong>${author}</strong>
            </div>
          </div>
        </div>
      `;
    });

    html += '</div>';
    document.getElementById('timelineContent').innerHTML = html;
  }

  function updatePagination() {
    const totalPages = Math.ceil(allMentions.length / mentionsPerPage);
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
  }

  function changePage(direction) {
    const totalPages = Math.ceil(allMentions.length / mentionsPerPage);
    const newPage = currentPage + direction;

    if (newPage >= 1 && newPage <= totalPages) {
      currentPage = newPage;
      renderTimeline();
      updatePagination();
      
      document.querySelector('.timeline-section').scrollIntoView({ behavior: 'smooth' });
    }
  }

  function filterTimeline() {
    const term = document.getElementById('timelineSearchInput').value.toLowerCase();
    
    if (!term) {
      currentPage = 1;
      renderTimeline();
      updatePagination();
      document.getElementById('pagination').style.display = 'flex';
      return;
    }
    
    const filtered = allMentions.filter(item => {
      const content = (item.content || '').toLowerCase();
      const author = (item.author_scr_name || item.author_name || '').toLowerCase();
      const source = (item.hostname || '').toLowerCase();
      return content.includes(term) || author.includes(term) || source.includes(term);
    });
    
    let html = '<div class="timeline-list">';

    filtered.forEach(item => {
      const content = item.content || 'No content';
      const date = formatDate(item.date_created);
      const author = item.author_scr_name || item.author_name || 'Unknown';
      const source = item.hostname || 'News';

      html += `
        <div class="timeline-item">
          <div class="timeline-dot"></div>
          <div class="timeline-content">
            <div class="timeline-meta">
              <span class="timeline-date">${date}</span>
              <span class="timeline-source">${source}</span>
            </div>
            <div class="timeline-text">${content}</div>
            <div class="timeline-author">
              By <strong>${author}</strong>
            </div>
          </div>
        </div>
      `;
    });

    html += '</div>';
    document.getElementById('timelineContent').innerHTML = html;
    
    document.getElementById('pagination').style.display = 'none';
  }
}
</script>
@endsection