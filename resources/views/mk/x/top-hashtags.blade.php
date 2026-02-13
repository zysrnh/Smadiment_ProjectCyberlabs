@extends('mk.layouts.app')

@section('title', 'Top Hashtags - X Analytics')

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

  /* Date Filter Card */
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

  .date-input-group {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: var(--bg-gray-50);
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    transition: all 0.2s;
  }

  .date-input-group:focus-within {
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  .date-input-group svg {
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
  }

  .date-input {
    border: none;
    background: transparent;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    outline: none;
    min-width: 140px;
  }

  .date-separator {
    color: var(--text-secondary);
    font-weight: 600;
    font-size: 14px;
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
    position: relative;
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

  .stat-value-wrapper {
    display: flex;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 16px;
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
    margin-top: 8px;
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

  .chart-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
    transition: all 0.3s;
  }

  .chart-card:hover {
    box-shadow: var(--shadow-md);
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
    height: 450px;
  }

  /* Table Section */
  .table-section {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
    margin-bottom: 24px;
  }

  .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--bg-gray-50);
    gap: 16px;
    flex-wrap: wrap;
  }

  .table-title h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 6px 0;
  }

  .table-subtitle {
    font-size: 13px;
    color: var(--text-secondary);
  }

  .table-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .table-search {
    position: relative;
    width: 280px;
  }

  .table-search input {
    width: 100%;
    padding: 10px 16px 10px 44px;
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    background: var(--bg-gray-50);
    transition: all 0.2s;
  }

  .table-search input:focus {
    outline: none;
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  .table-search svg {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
  }

  .data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
  }

  .data-table thead tr {
    background: var(--bg-white);
  }

  .data-table th {
    padding: 12px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 1px solid var(--border-gray);
    white-space: nowrap;
  }

  .data-table th:first-child {
    padding-left: 20px;
  }

  .data-table th:last-child {
    padding-right: 20px;
  }

  .data-table td {
    padding: 14px 12px;
    font-size: 13px;
    color: var(--text-primary);
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }

  .data-table td:first-child {
    padding-left: 20px;
  }

  .data-table td:last-child {
    padding-right: 20px;
  }

  .data-table tbody tr {
    transition: all 0.2s;
    background: var(--bg-white);
  }

  .data-table tbody tr:hover {
    background: #fafbfc;
  }

  .data-table tbody tr:last-child td {
    border-bottom: none;
  }

  .hashtag-name {
    font-weight: 600;
    color: var(--primary-green);
    font-size: 14px;
  }

  .hashtag-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    background: rgba(3, 128, 71, 0.1);
    color: var(--primary-green);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }

  /* Loading States */
  .loading-skeleton {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s ease-in-out infinite;
    border-radius: 8px;
  }

  @keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
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
    to { transform: rotate(360deg); }
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

  @media (max-width: 640px) {
    .stat-value {
      font-size: 28px;
    }

    .chart-container {
      height: 300px;
    }

    .table-search {
      width: 100%;
    }

    .page-header h1 {
      font-size: 24px;
    }

    .data-table {
      font-size: 12px;
    }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">
  
  <!-- Page Header -->
  <div class="page-header">
    <h1>Top Hashtags</h1>
    <p>Discover the most popular hashtags and trending topics on X (Twitter)</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view Top Hashtags.</span>
  </div>
  @else

  <!-- Date Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.top-hashtags') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      
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
          <div class="date-input-group">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <input type="date" 
                   name="start_date" 
                   class="date-input" 
                   value="{{ $startDate }}"
                   max="{{ date('Y-m-d') }}"
                   required>
          </div>
          
          <span class="date-separator">to</span>
          
          <div class="date-input-group">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <input type="date" 
                   name="end_date" 
                   class="date-input" 
                   value="{{ $endDate }}"
                   max="{{ date('Y-m-d') }}"
                   required>
          </div>
        </div>
        
        <button type="submit" class="apply-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
          Apply Filter
        </button>
      </div>
    </form>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid" data-lazy-load="hashtags">
    
    <!-- Total Hashtags -->
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="4" y1="9" x2="20" y2="9"/>
            <line x1="4" y1="15" x2="20" y2="15"/>
            <line x1="10" y1="3" x2="8" y2="21"/>
            <line x1="16" y1="3" x2="14" y2="21"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Total Hashtags</div>
      
      <div id="totalHashtagsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Total Mentions -->
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Total Mentions</div>
      
      <div id="totalMentionsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Top Hashtag -->
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Top Hashtag</div>
      
      <div id="topHashtagValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 160px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

  </div>

  <!-- Top 10 Chart -->
  <div class="charts-section" data-lazy-load="hashtags">
    <div class="chart-card">
      <div class="chart-header">
        <div class="chart-title-group">
          <h3>Top 10 Hashtags</h3>
          <p class="chart-subtitle">Most mentioned hashtags by volume</p>
        </div>
      </div>
      
      <div class="chart-container">
        <div id="hashtagsChartLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="hashtagsChart" style="display: none;"></canvas>
      </div>
    </div>
  </div>

  <!-- All Hashtags Table -->
  <div class="table-section" data-lazy-load="hashtags">
    <div class="table-header">
      <div class="table-title">
        <h3>All Hashtags</h3>
        <p class="table-subtitle">Complete list of trending hashtags</p>
      </div>
      
      <div class="table-actions">
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <input type="text" id="hashtagSearchInput" placeholder="Search hashtags..." onkeyup="filterHashtags()">
        </div>
      </div>
    </div>
    
    <div id="hashtagsTableLoading" class="loading-skeleton" style="height: 400px;"></div>
    <div id="hashtagsTable" style="display: none; overflow-x: auto;"></div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script>
  const projectId = '{{ $projectId ?? '' }}';
  const startDate = '{{ $startDate ?? '' }}';
  const endDate = '{{ $endDate ?? '' }}';

  let allHashtags = [];

  if (projectId && startDate && endDate) {
    
    function formatNumber(num) {
      return new Intl.NumberFormat('en-US').format(num);
    }

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
            loadHashtags();
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

   async function loadHashtags() {
  const cards = document.querySelectorAll('[data-lazy-load="hashtags"]');
  cards.forEach(card => addLoadingBadge(card));
  
  try {
    console.log('🔍 Fetching hashtags...', {
      projectId,
      startDate,
      endDate
    });
    
    const url = `/mk/api/x/top-hashtags-data?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`;
    console.log('📡 API URL:', url);
    
    const response = await fetch(url);
    console.log('📥 Response status:', response.status);
    
    const result = await response.json();
    console.log('📦 API Result:', result);
    
    if (result.success && result.data) {
      allHashtags = result.data.hashtags || [];
      console.log('✅ Hashtags loaded:', allHashtags.length, allHashtags);
      
      // Update stats
      const totalHashtags = result.data.total_hashtags || 0;
      const totalMentions = result.data.total_mentions || 0;
      const topHashtag = result.data.top_hashtag;
      
      console.log('📊 Stats:', { totalHashtags, totalMentions, topHashtag });
      
      // Total Hashtags
      document.getElementById('totalHashtagsValue').innerHTML = 
        `<div class="stat-value">${formatNumber(totalHashtags)}</div>`;
      document.getElementById('totalHashtagsValue').classList.add('data-loaded');
      
      // Total Mentions
      document.getElementById('totalMentionsValue').innerHTML = 
        `<div class="stat-value">${formatNumber(totalMentions)}</div>`;
      document.getElementById('totalMentionsValue').classList.add('data-loaded');
      
      // Top Hashtag
      if (topHashtag) {
        document.getElementById('topHashtagValue').innerHTML = 
          `<div class="stat-value" style="font-size: 28px;">#${topHashtag.name}</div>
           <span class="hashtag-badge">${formatNumber(topHashtag.size)} mentions</span>`;
      } else {
        document.getElementById('topHashtagValue').innerHTML = 
          `<div class="stat-value" style="font-size: 24px;">No data</div>`;
      }
      document.getElementById('topHashtagValue').classList.add('data-loaded');
      
      // Animate progress bars
      const statCards = document.querySelectorAll('.stat-card');
      statCards[0]?.querySelector('.stat-progress-bar')?.style.setProperty('width', '85%');
      statCards[1]?.querySelector('.stat-progress-bar')?.style.setProperty('width', '92%');
      statCards[2]?.querySelector('.stat-progress-bar')?.style.setProperty('width', '100%');
      
      // Render chart
      renderHashtagsChart(allHashtags.slice(0, 10));
      
      // Render table
      displayHashtagsTable(allHashtags);
    } else {
      console.error('❌ API returned error:', result);
    }
  } catch (error) {
    console.error('💥 Error loading hashtags:', error);
    console.error('Error stack:', error.stack);
  } finally {
    cards.forEach(card => {
      removeLoadingBadge(card);
      card.classList.add('loaded');
    });
  }
}
```

## 4. Test API Directly

Buka browser dan akses langsung:
```
http://localhost:8000/mk/api/x/top-hashtags-data?project_id=16978&start_date=2026-02-07&end_date=2026-02-13
    function renderHashtagsChart(data) {
      const canvas = document.getElementById('hashtagsChart');
      const loading = document.getElementById('hashtagsChartLoading');
      
      if (!data || data.length === 0) {
        loading.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No hashtags data available</p>';
        return;
      }

      const ctx = canvas.getContext('2d');
      
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: data.map(h => '#' + h.name),
          datasets: [{
            label: 'Mentions',
            data: data.map(h => h.size),
            backgroundColor: 'rgba(3, 128, 71, 0.8)',
            borderColor: '#038047',
            borderWidth: 2,
            borderRadius: 8,
            hoverBackgroundColor: '#026738',
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'y',
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
              cornerRadius: 8,
              callbacks: {
                label: function(context) {
                  return formatNumber(context.parsed.x) + ' mentions';
                }
              }
            }
          },
          scales: {
            x: {
              beginAtZero: true,
              grid: { color: '#f1f5f9', drawBorder: false },
              ticks: { 
                color: '#64748b', 
                font: { family: 'Poppins', size: 12 },
                callback: function(value) {
                  return formatNumber(value);
                }
              }
            },
            y: {
              grid: { display: false, drawBorder: false },
              ticks: { 
                color: '#64748b', 
                font: { family: 'Poppins', size: 12, weight: '600' },
              }
            }
          }
        }
      });

      loading.style.display = 'none';
      canvas.style.display = 'block';
    }

    function displayHashtagsTable(hashtags) {
      const container = document.getElementById('hashtagsTable');
      const loading = document.getElementById('hashtagsTableLoading');
      
      let html = '<table class="data-table"><thead><tr>';
      html += '<th>RANK</th><th>HASHTAG</th><th>MENTIONS</th><th>PERCENTAGE</th></tr></thead><tbody>';
      
      const totalMentions = hashtags.reduce((sum, h) => sum + h.size, 0);
      
      if (hashtags.length === 0) {
        html += '<tr><td colspan="4" style="text-align: center; padding: 40px; color: var(--text-secondary);">No hashtags found</td></tr>';
      } else {
        hashtags.forEach((hashtag, index) => {
          const percentage = totalMentions > 0 ? ((hashtag.size / totalMentions) * 100).toFixed(2) : 0;
          
          html += `<tr>
            <td><strong>${index + 1}</strong></td>
            <td><span class="hashtag-name">#${hashtag.name}</span></td>
            <td><strong>${formatNumber(hashtag.size)}</strong></td>
            <td>${percentage}%</td>
          </tr>`;
        });
      }
      
      html += '</tbody></table>';
      container.innerHTML = html;
      container.classList.add('data-loaded');
      
      loading.style.display = 'none';
      container.style.display = 'block';
    }

    function filterHashtags() {
      const searchTerm = document.getElementById('hashtagSearchInput').value.toLowerCase();
      
      if (!searchTerm) {
        displayHashtagsTable(allHashtags);
        return;
      }
      
      const filtered = allHashtags.filter(h => 
        h.name.toLowerCase().includes(searchTerm)
      );
      
      displayHashtagsTable(filtered);
    }

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
  }
</script>
@endsection