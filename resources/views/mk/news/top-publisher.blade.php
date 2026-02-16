@extends('mk.layouts.app')

@section('title', 'Top News Publishers - SMADIMENT')

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

  .filter-select {
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
    min-width: 200px;
  }

  .filter-select:focus {
    outline: none;
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
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
    flex-wrap: wrap;
    gap: 16px;
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

  /* Data Table */
  .data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 14px;
  }

  .data-table thead tr {
    background: var(--bg-white);
    border-bottom: 1px solid var(--border-gray);
  }

  .data-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
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
    padding: 16px;
    font-size: 14px;
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
    transform: translateX(2px);
  }

  .data-table tbody tr:last-child td {
    border-bottom: none;
  }

  /* Publisher Link */
  .publisher-link {
    color: var(--primary-green);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .publisher-link:hover {
    color: var(--primary-green-dark);
    text-decoration: underline;
  }

  .publisher-link svg {
    width: 16px;
    height: 16px;
  }

  /* Rank Badge */
  .rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
  }

  .rank-1 {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #854d0e;
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
  }

  .rank-2 {
    background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%);
    color: #52525b;
    box-shadow: 0 4px 12px rgba(192, 192, 192, 0.3);
  }

  .rank-3 {
    background: linear-gradient(135deg, #cd7f32 0%, #e3a869 100%);
    color: #78350f;
    box-shadow: 0 4px 12px rgba(205, 127, 50, 0.3);
  }

  .rank-other {
    background: var(--bg-gray-100);
    color: var(--text-secondary);
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

  /* Lazy Loading Badge */
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

  /* Animations */
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

  /* Date Picker Trigger */
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

  .date-picker-trigger svg:first-child {
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
    flex-shrink: 0;
  }

  .date-picker-trigger span {
    flex: 1;
    text-align: left;
  }

  .date-picker-trigger svg:last-child {
    width: 16px;
    height: 16px;
    margin-left: auto;
    color: var(--text-secondary);
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

    .apply-btn,
    .filter-select {
      width: 100%;
      justify-content: center;
    }
  }

  @media (max-width: 768px) {
    .stat-value {
      font-size: 28px;
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

    .data-table th,
    .data-table td {
      padding: 10px 12px;
    }

    .date-picker-trigger,
    .filter-select {
      max-width: 100%;
    }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">
  
  <!-- Page Header -->
  <div class="page-header">
    <h1>Top News Publishers</h1>
    <p>Track the most active and influential news publishers</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view publisher data.</span>
  </div>
  @else

  <!-- Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.news.top-publishers') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">
      
      <div class="filter-content">
        <!-- Date Range -->
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

        <!-- News Type Filter -->
        <div class="filter-label">
          <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; display: inline; vertical-align: middle; margin-right: 6px; stroke: currentColor; fill: none;">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
          </svg>
          News Type
        </div>
        
        <select name="news_type" id="newsTypeSelect" class="filter-select">
          <option value="article" selected>Article</option>
          <option value="blog">Blog</option>
          <option value="press">Press Release</option>
        </select>
        
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
    
    <!-- Total Publishers Card -->
    <div class="stat-card" data-lazy-load="publishers">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Total Publishers</div>
      
      <div id="totalPublishersValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Total Articles Card -->
    <div class="stat-card" data-lazy-load="publishers">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Total Articles</div>
      
      <div id="totalArticlesValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Top Publisher Card -->
    <div class="stat-card" data-lazy-load="publishers">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Top Publisher</div>
      
      <div id="topPublisherValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 160px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

  </div>

  <!-- Publishers Table -->
  <div class="table-section" data-lazy-load="publishers">
    <div class="table-header">
      <div class="table-title">
        <h3>Publisher Rankings</h3>
        <p class="table-subtitle">Top 100 publishers by article volume</p>
      </div>
      <div class="table-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/>
          <path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="publisherSearchInput" placeholder="Search publishers..." onkeyup="filterPublishers()">
      </div>
    </div>
    
    <div id="publishersLoading" class="loading-skeleton" style="height: 500px;"></div>
    <div id="publishersTable" style="display: none; overflow-x: auto;"></div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script>
// ========================================
// MAIN LOGIC
// ========================================
const projectId = '{{ $projectId ?? '' }}';
const startDate = '{{ $startDate ?? '' }}';
const endDate = '{{ $endDate ?? '' }}';
const newsType = '{{ $newsType ?? 'article' }}';

let allPublishers = [];

if (projectId && startDate && endDate) {
  
  function formatNumber(num) {
    return new Intl.NumberFormat('en-US').format(num);
  }

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
          
          if (componentId === 'publishers') {
            loadPublishers();
          }
          
          lazyLoadObserver.unobserve(entry.target);
        }
      }
    });
  }, lazyLoadConfig);

  document.addEventListener('DOMContentLoaded', function() {
    // Set news type in select
    const newsTypeSelect = document.getElementById('newsTypeSelect');
    if (newsTypeSelect) {
      newsTypeSelect.value = newsType;
    }

    // Observe lazy load elements
    document.querySelectorAll('[data-lazy-load]').forEach(element => {
      lazyLoadObserver.observe(element);
    });
  });

  async function loadPublishers() {
    const cards = document.querySelectorAll('[data-lazy-load="publishers"]');
    cards.forEach(card => addLoadingBadge(card));
    
    try {
      const response = await fetch(`/mk/api/news/top-publisher?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}&news_type=${newsType}`);
      const result = await response.json();
      
      if (result.success && result.data) {
        allPublishers = result.data;
        
        // Update stats
        const totalPublishers = result.meta.total_publishers || 0;
        const totalArticles = result.meta.total_articles || 0;
        const topPublisher = allPublishers.length > 0 ? allPublishers[0].domain : 'N/A';
        
        // Total Publishers
        const totalPubEl = document.getElementById('totalPublishersValue');
        totalPubEl.innerHTML = `<div class="stat-value">${formatNumber(totalPublishers)}</div>`;
        totalPubEl.classList.add('data-loaded');
        
        // Total Articles
        const totalArticlesEl = document.getElementById('totalArticlesValue');
        totalArticlesEl.innerHTML = `<div class="stat-value">${formatNumber(totalArticles)}</div>`;
        totalArticlesEl.classList.add('data-loaded');
        
        // Top Publisher
        const topPubEl = document.getElementById('topPublisherValue');
        topPubEl.innerHTML = `<div class="stat-value" style="font-size: 20px; word-break: break-all;">${topPublisher}</div>`;
        topPubEl.classList.add('data-loaded');
        
        // Animate progress bars
        const statCards = document.querySelectorAll('.stat-card[data-lazy-load="publishers"]');
        animateProgress(statCards[0], 85);
        animateProgress(statCards[1], 92);
        animateProgress(statCards[2], 100);
        
        // Render table
        renderPublishersTable(allPublishers);
        
        const loading = document.getElementById('publishersLoading');
        const table = document.getElementById('publishersTable');
        loading.style.display = 'none';
        table.style.display = 'block';
      }
    } catch (error) {
      console.error('Error loading publishers:', error);
    } finally {
      cards.forEach(card => {
        removeLoadingBadge(card);
        card.classList.add('loaded');
      });
    }
  }

  function renderPublishersTable(publishers) {
    const container = document.getElementById('publishersTable');
    
    if (!publishers || publishers.length === 0) {
      container.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No publisher data available</p>';
      return;
    }
    
    let html = '<table class="data-table"><thead><tr>';
    html += '<th>RANK</th><th>PUBLISHER</th><th>ARTICLES</th><th>PERCENTAGE</th></tr></thead><tbody>';
    
    const totalArticles = publishers.reduce((sum, p) => sum + p.count, 0);
    
    publishers.forEach((item, index) => {
      const rank = item.rank || (index + 1);
      const domain = item.domain || 'Unknown';
      const count = item.count || 0;
      const percentage = totalArticles > 0 ? ((count / totalArticles) * 100).toFixed(2) : 0;
      
      let rankClass = 'rank-other';
      if (rank === 1) rankClass = 'rank-1';
      else if (rank === 2) rankClass = 'rank-2';
      else if (rank === 3) rankClass = 'rank-3';
      
      html += `<tr>
        <td>
          <div class="rank-badge ${rankClass}">${rank}</div>
        </td>
        <td>
          <a href="http://${domain}" target="_blank" class="publisher-link">
            ${domain}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
              <polyline points="15 3 21 3 21 9"/>
              <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
          </a>
        </td>
        <td><strong>${formatNumber(count)}</strong></td>
        <td>${percentage}%</td>
      </tr>`;
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
    container.classList.add('data-loaded');
  }

  function filterPublishers() {
    const searchTerm = document.getElementById('publisherSearchInput').value.toLowerCase();
    
    if (!searchTerm) {
      renderPublishersTable(allPublishers);
      return;
    }
    
    const filtered = allPublishers.filter(pub => {
      const domain = (pub.domain || '').toLowerCase();
      return domain.includes(searchTerm);
    });
    
    renderPublishersTable(filtered);
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

  function animateProgress(card, percentage) {
    const progressBar = card.querySelector('.stat-progress-bar');
    if (progressBar) {
      setTimeout(() => {
        progressBar.style.width = percentage + '%';
      }, 100);
    }
  }
}

// ========================================
// SIMPLE DATE RANGE DISPLAY (No Picker)
// ========================================
document.addEventListener('DOMContentLoaded', function() {
  const trigger = document.getElementById('datePickerTrigger');
  if (trigger) {
    // Disable date picker - just show current range
    trigger.style.cursor = 'default';
    trigger.style.opacity = '0.7';
  }
});
</script>
@endsection