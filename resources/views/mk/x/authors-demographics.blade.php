@extends('mk.layouts.app')

@section('title', 'Authors Demographics (X) - SMADIMENT')

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
    margin-bottom: 32px;
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

  /* Section Divider */
  .section-divider {
    margin: 48px 0 32px 0;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--border-gray);
  }

  .section-title {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 6px 0;
  }

  .section-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
  }

  /* Grid Layout - 2 Columns */
  .metrics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
  }

  /* Metric Card */
  .metric-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }

  .metric-card::before {
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

  .metric-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-green);
  }

  .metric-card:hover::before {
    opacity: 1;
  }

  .metric-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
  }

  .metric-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .metric-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
  }

  .metric-badge.positive {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
  }

  .metric-badge.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
  }

  .metric-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 8px;
  }

  .metric-label {
    font-size: 13px;
    color: var(--text-secondary);
  }

  /* Chart Card */
  .chart-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 24px;
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
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--bg-gray-50);
  }

  .chart-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px 0;
  }

  .chart-subtitle {
    font-size: 12px;
    color: var(--text-secondary);
    font-weight: 500;
  }

  .chart-container {
    position: relative;
    height: 280px;
  }

  .chart-container.large {
    height: 320px;
  }

  /* Full Width Chart */
  .full-width-chart {
    grid-column: 1 / -1;
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
    height: 36px;
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

  /* Legend */
  .chart-legend {
    display: flex;
    gap: 20px;
    margin-top: 16px;
    flex-wrap: wrap;
  }

  .legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
  }

  .legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
  }

  .legend-label {
    color: var(--text-secondary);
    font-weight: 500;
  }

  .legend-value {
    color: var(--text-primary);
    font-weight: 600;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .dashboard-container {
      padding: 16px;
    }

    .metrics-grid {
      grid-template-columns: 1fr;
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
    .metric-value {
      font-size: 24px;
    }

    .chart-container {
      height: 220px;
    }

    .page-header h1 {
      font-size: 24px;
    }

    .section-title {
      font-size: 18px;
    }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">
  
  <!-- Page Header -->
  <div class="page-header">
    <h1>Authors Demographics (X)</h1>
    <p>Comprehensive analysis of author characteristics and engagement patterns</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar.</span>
  </div>
  @else

  <!-- Date Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.authors.demographics') }}">
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

  <!-- ========================================
       AGE DEMOGRAPHICS SECTION
       ======================================== -->
  <div class="section-divider">
    <h2 class="section-title">Age Demographics</h2>
    <p class="section-subtitle">Author distribution and engagement by age groups</p>
  </div>

  <div class="metrics-grid" data-lazy-load="age">
    <!-- Total Authors by Age -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="metric-title">Total Authors</div>
      </div>
      <div id="ageAuthorsValue">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      <div class="metric-label">Across all age groups</div>
    </div>

    <!-- Total Posts by Age -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="metric-title">Total Posts</div>
      </div>
      <div id="agePostsValue">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      <div class="metric-label">Total engagement</div>
    </div>

    <!-- Age Distribution Chart -->
    <div class="chart-card">
      <div class="chart-header">
        <div>
          <h3 class="chart-title">Age Distribution</h3>
          <p class="chart-subtitle">Authors by age group</p>
        </div>
      </div>
      <div class="chart-container">
        <div id="ageDistributionLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="ageDistributionChart" style="display: none;"></canvas>
      </div>
    </div>

    <!-- Age Engagement Chart -->
    <div class="chart-card">
      <div class="chart-header">
        <div>
          <h3 class="chart-title">Engagement by Age</h3>
          <p class="chart-subtitle">Post frequency comparison</p>
        </div>
      </div>
      <div class="chart-container">
        <div id="ageEngagementLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="ageEngagementChart" style="display: none;"></canvas>
      </div>
    </div>
  </div>

  <!-- ========================================
       GENDER DEMOGRAPHICS SECTION
       ======================================== -->
  <div class="section-divider">
    <h2 class="section-title">Gender Demographics</h2>
    <p class="section-subtitle">Author distribution and engagement by gender</p>
  </div>

  <div class="metrics-grid" data-lazy-load="gender">
    <!-- Male Authors -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="metric-title">Male Authors</div>
      </div>
      <div id="maleAuthorsValue">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      <div class="metric-label">Total male contributors</div>
    </div>

    <!-- Female Authors -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="metric-title">Female Authors</div>
      </div>
      <div id="femaleAuthorsValue">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      <div class="metric-label">Total female contributors</div>
    </div>

    <!-- Gender Distribution Chart -->
    <div class="chart-card">
      <div class="chart-header">
        <div>
          <h3 class="chart-title">Gender Distribution</h3>
          <p class="chart-subtitle">Authors by gender</p>
        </div>
      </div>
      <div class="chart-container">
        <div id="genderDistributionLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="genderDistributionChart" style="display: none;"></canvas>
      </div>
    </div>

    <!-- Gender Engagement Chart -->
    <div class="chart-card">
      <div class="chart-header">
        <div>
          <h3 class="chart-title">Engagement by Gender</h3>
          <p class="chart-subtitle">Post frequency comparison</p>
        </div>
      </div>
      <div class="chart-container">
        <div id="genderEngagementLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="genderEngagementChart" style="display: none;"></canvas>
      </div>
    </div>
  </div>

  <!-- ========================================
       AUTHOR TYPE SECTION
       ======================================== -->
  <div class="section-divider">
    <h2 class="section-title">Author Type Analysis</h2>
    <p class="section-subtitle">Organization vs Individual author comparison</p>
  </div>

  <div class="metrics-grid" data-lazy-load="type">
    <!-- Individual Authors -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="metric-title">Individual Authors</div>
      </div>
      <div id="individualAuthorsValue">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      <div class="metric-label">Personal accounts</div>
    </div>

    <!-- Organization Authors -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="metric-title">Organizations</div>
      </div>
      <div id="organizationAuthorsValue">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      <div class="metric-label">Organizational accounts</div>
    </div>

    <!-- Type Distribution Chart -->
    <div class="chart-card">
      <div class="chart-header">
        <div>
          <h3 class="chart-title">Type Distribution</h3>
          <p class="chart-subtitle">Organization vs Individual</p>
        </div>
      </div>
      <div class="chart-container">
        <div id="typeDistributionLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="typeDistributionChart" style="display: none;"></canvas>
      </div>
    </div>

    <!-- Type Engagement Chart -->
    <div class="chart-card">
      <div class="chart-header">
        <div>
          <h3 class="chart-title">Engagement by Type</h3>
          <p class="chart-subtitle">Post frequency comparison</p>
        </div>
      </div>
      <div class="chart-container">
        <div id="typeEngagementLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="typeEngagementChart" style="display: none;"></canvas>
      </div>
    </div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script>
  const projectId = '{{ $projectId ?? '' }}';
  const startDate = '{{ $startDate ?? '' }}';
  const endDate = '{{ $endDate ?? '' }}';

  if (projectId && startDate && endDate) {
    
    function formatNumber(num) {
      return new Intl.NumberFormat('en-US').format(num);
    }

    const lazyLoadConfig = {
      rootMargin: '100px',
      threshold: 0.01
    };

    const loadedComponents = new Set();

    const lazyLoadObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const componentId = entry.target.dataset.lazyLoad;
          
          if (!loadedComponents.has(componentId)) {
            loadedComponents.add(componentId);
            
            switch(componentId) {
              case 'age':
                loadAgeData();
                break;
              case 'gender':
                loadGenderData();
                break;
              case 'type':
                loadTypeData();
                break;
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

    // ========================================
    // AGE DEMOGRAPHICS
    // ========================================
    async function loadAgeData() {
      const section = document.querySelector('[data-lazy-load="age"]');
      addLoadingBadge(section);
      
      try {
        const response = await fetch(`/mk/api/x/authors-age?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log('Age data received:', data);
        
        // API returns direct array, not wrapped object
        if (Array.isArray(data) && data.length > 0) {
          let totalAuthors = 0;
          let totalPosts = 0;
          
          data.forEach(item => {
            totalAuthors += parseInt(item.author_freq) || 0;
            totalPosts += parseInt(item.post_freq) || 0;
          });
          
          document.getElementById('ageAuthorsValue').innerHTML = `<div class="metric-value">${formatNumber(totalAuthors)}</div>`;
          document.getElementById('agePostsValue').innerHTML = `<div class="metric-value">${formatNumber(totalPosts)}</div>`;
          
          renderAgeDistributionChart(data);
          renderAgeEngagementChart(data);
        } else {
          console.warn('No age data available');
          document.getElementById('ageAuthorsValue').innerHTML = `<div class="metric-value">0</div>`;
          document.getElementById('agePostsValue').innerHTML = `<div class="metric-value">0</div>`;
        }
      } catch (error) {
        console.error('Error loading age data:', error);
        document.getElementById('ageAuthorsValue').innerHTML = `<div class="metric-value" style="color: #ef4444;">Error</div>`;
        document.getElementById('agePostsValue').innerHTML = `<div class="metric-value" style="color: #ef4444;">Error</div>`;
      } finally {
        removeLoadingBadge(section);
        section.classList.add('loaded');
      }
    }

    function renderAgeDistributionChart(data) {
      const canvas = document.getElementById('ageDistributionChart');
      const loading = document.getElementById('ageDistributionLoading');
      
      if (!data || data.length === 0) {
        loading.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No data available</p>';
        return;
      }

      const ctx = canvas.getContext('2d');
      
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: data.map(d => d.age_group),
          datasets: [{
            label: 'Authors',
            data: data.map(d => parseInt(d.author_freq) || 0),
            backgroundColor: 'rgba(3, 128, 71, 0.8)',
            borderColor: '#038047',
            borderWidth: 2,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#1a202c',
              padding: 12,
              titleFont: { size: 13, weight: '600' },
              bodyFont: { size: 12 },
              displayColors: false,
              cornerRadius: 8
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: '#f1f5f9', drawBorder: false },
              ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } }
            },
            x: {
              grid: { display: false },
              ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } }
            }
          }
        }
      });

      loading.style.display = 'none';
      canvas.style.display = 'block';
    }

    function renderAgeEngagementChart(data) {
      const canvas = document.getElementById('ageEngagementChart');
      const loading = document.getElementById('ageEngagementLoading');
      
      if (!data || data.length === 0) {
        loading.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No data available</p>';
        return;
      }

      const ctx = canvas.getContext('2d');
      
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: data.map(d => d.age_group),
          datasets: [{
            data: data.map(d => parseInt(d.post_freq) || 0),
            backgroundColor: ['#038047', '#04995a', '#10b981', '#34d399', '#6ee7b7', '#a7f3d0'],
            borderWidth: 0,
            hoverOffset: 10
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '65%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: '#1a202c',
                font: { family: 'Poppins', size: 11, weight: '600' },
                padding: 15,
                usePointStyle: true
              }
            },
            tooltip: {
              backgroundColor: '#1a202c',
              padding: 12,
              titleFont: { size: 13, weight: '600' },
              bodyFont: { size: 12 },
              displayColors: false,
              cornerRadius: 8,
              callbacks: {
                label: function(context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = ((context.parsed / total) * 100).toFixed(1);
                  return `Posts: ${formatNumber(context.parsed)} (${percentage}%)`;
                }
              }
            }
          }
        }
      });

      loading.style.display = 'none';
      canvas.style.display = 'block';
    }

    // ========================================
    // GENDER DEMOGRAPHICS
    // ========================================
    async function loadGenderData() {
      const section = document.querySelector('[data-lazy-load="gender"]');
      addLoadingBadge(section);
      
      try {
        const response = await fetch(`/mk/api/x/authors-gender?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log('Gender data received:', data);
        
        // API returns direct array
        if (Array.isArray(data) && data.length > 0) {
          const maleData = data.find(d => (d.gender || d.name) === 'male');
          const femaleData = data.find(d => (d.gender || d.name) === 'female');
          
          const maleAuthors = maleData ? parseInt(maleData.author_freq) || 0 : 0;
          const femaleAuthors = femaleData ? parseInt(femaleData.author_freq) || 0 : 0;
          
          document.getElementById('maleAuthorsValue').innerHTML = `<div class="metric-value">${formatNumber(maleAuthors)}</div>`;
          document.getElementById('femaleAuthorsValue').innerHTML = `<div class="metric-value">${formatNumber(femaleAuthors)}</div>`;
          
          renderGenderDistributionChart(data);
          renderGenderEngagementChart(data);
        } else {
          console.warn('No gender data available');
          document.getElementById('maleAuthorsValue').innerHTML = `<div class="metric-value">0</div>`;
          document.getElementById('femaleAuthorsValue').innerHTML = `<div class="metric-value">0</div>`;
        }
      } catch (error) {
        console.error('Error loading gender data:', error);
        document.getElementById('maleAuthorsValue').innerHTML = `<div class="metric-value" style="color: #ef4444;">Error</div>`;
        document.getElementById('femaleAuthorsValue').innerHTML = `<div class="metric-value" style="color: #ef4444;">Error</div>`;
      } finally {
        removeLoadingBadge(section);
        section.classList.add('loaded');
      }
    }

    function renderGenderDistributionChart(data) {
      const canvas = document.getElementById('genderDistributionChart');
      const loading = document.getElementById('genderDistributionLoading');
      
      if (!data || data.length === 0) {
        loading.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No data available</p>';
        return;
      }

      const ctx = canvas.getContext('2d');
      
      const labels = data.map(d => {
        const gender = d.gender || d.name;
        return gender.charAt(0).toUpperCase() + gender.slice(1);
      });
      
      const colors = labels.map(label => {
        if (label.toLowerCase() === 'male') return '#3b82f6';
        if (label.toLowerCase() === 'female') return '#ec4899';
        return '#64748b';
      });
      
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data.map(d => parseInt(d.author_freq) || 0),
            backgroundColor: colors,
            borderWidth: 0,
            hoverOffset: 10
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '65%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: '#1a202c',
                font: { family: 'Poppins', size: 11, weight: '600' },
                padding: 15,
                usePointStyle: true
              }
            },
            tooltip: {
              backgroundColor: '#1a202c',
              padding: 12,
              titleFont: { size: 13, weight: '600' },
              bodyFont: { size: 12 },
              displayColors: false,
              cornerRadius: 8,
              callbacks: {
                label: function(context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = ((context.parsed / total) * 100).toFixed(1);
                  return `Authors: ${formatNumber(context.parsed)} (${percentage}%)`;
                }
              }
            }
          }
        }
      });

      loading.style.display = 'none';
      canvas.style.display = 'block';
    }

    function renderGenderEngagementChart(data) {
      const canvas = document.getElementById('genderEngagementChart');
      const loading = document.getElementById('genderEngagementLoading');
      
      if (!data || data.length === 0) {
        loading.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No data available</p>';
        return;
      }

      const ctx = canvas.getContext('2d');
      
      const labels = data.map(d => {
        const gender = d.gender || d.name;
        return gender.charAt(0).toUpperCase() + gender.slice(1);
      });
      
      const colors = labels.map(label => {
        if (label.toLowerCase() === 'male') return 'rgba(59, 130, 246, 0.8)';
        if (label.toLowerCase() === 'female') return 'rgba(236, 72, 153, 0.8)';
        return 'rgba(100, 116, 139, 0.8)';
      });
      
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Posts',
            data: data.map(d => parseInt(d.post_freq) || 0),
            backgroundColor: colors,
            borderColor: colors.map(c => c.replace('0.8', '1')),
            borderWidth: 2,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#1a202c',
              padding: 12,
              titleFont: { size: 13, weight: '600' },
              bodyFont: { size: 12 },
              displayColors: false,
              cornerRadius: 8
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: '#f1f5f9', drawBorder: false },
              ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } }
            },
            x: {
              grid: { display: false },
              ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } }
            }
          }
        }
      });

      loading.style.display = 'none';
      canvas.style.display = 'block';
    }

    // ========================================
    // AUTHOR TYPE
    // ========================================
    async function loadTypeData() {
      const section = document.querySelector('[data-lazy-load="type"]');
      addLoadingBadge(section);
      
      try {
        const response = await fetch(`/mk/api/x/authors-type?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log('Type data received:', data);
        
        // API returns direct array
        if (Array.isArray(data) && data.length > 0) {
          const nonOrgData = data.find(d => (d.is_organization || d.name) === 'non-org');
          const orgData = data.find(d => (d.is_organization || d.name) === 'is-org' || (d.is_organization || d.name) === 'org');
          
          const individualAuthors = nonOrgData ? parseInt(nonOrgData.author_freq) || 0 : 0;
          const organizationAuthors = orgData ? parseInt(orgData.author_freq) || 0 : 0;
          
          document.getElementById('individualAuthorsValue').innerHTML = `<div class="metric-value">${formatNumber(individualAuthors)}</div>`;
          document.getElementById('organizationAuthorsValue').innerHTML = `<div class="metric-value">${formatNumber(organizationAuthors)}</div>`;
          
          renderTypeDistributionChart(data);
          renderTypeEngagementChart(data);
        } else {
          console.warn('No type data available');
          document.getElementById('individualAuthorsValue').innerHTML = `<div class="metric-value">0</div>`;
          document.getElementById('organizationAuthorsValue').innerHTML = `<div class="metric-value">0</div>`;
        }
      } catch (error) {
        console.error('Error loading type data:', error);
        document.getElementById('individualAuthorsValue').innerHTML = `<div class="metric-value" style="color: #ef4444;">Error</div>`;
        document.getElementById('organizationAuthorsValue').innerHTML = `<div class="metric-value" style="color: #ef4444;">Error</div>`;
      } finally {
        removeLoadingBadge(section);
        section.classList.add('loaded');
      }
    }

    function renderTypeDistributionChart(data) {
      const canvas = document.getElementById('typeDistributionChart');
      const loading = document.getElementById('typeDistributionLoading');
      
      if (!data || data.length === 0) {
        loading.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No data available</p>';
        return;
      }

      const ctx = canvas.getContext('2d');
      
      const labels = data.map(d => {
        const type = d.is_organization || d.name;
        return type === 'org' ? 'Organization' : 'Individual';
      });
      
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data.map(d => parseInt(d.author_freq) || 0),
            backgroundColor: ['#3b82f6', '#038047'],
            borderWidth: 0,
            hoverOffset: 10
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '65%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: '#1a202c',
                font: { family: 'Poppins', size: 11, weight: '600' },
                padding: 15,
                usePointStyle: true
              }
            },
            tooltip: {
              backgroundColor: '#1a202c',
              padding: 12,
              titleFont: { size: 13, weight: '600' },
              bodyFont: { size: 12 },
              displayColors: false,
              cornerRadius: 8,
              callbacks: {
                label: function(context) {
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = ((context.parsed / total) * 100).toFixed(1);
                  return `Authors: ${formatNumber(context.parsed)} (${percentage}%)`;
                }
              }
            }
          }
        }
      });

      loading.style.display = 'none';
      canvas.style.display = 'block';
    }

    function renderTypeEngagementChart(data) {
      const canvas = document.getElementById('typeEngagementChart');
      const loading = document.getElementById('typeEngagementLoading');
      
      if (!data || data.length === 0) {
        loading.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No data available</p>';
        return;
      }

      const ctx = canvas.getContext('2d');
      
      const labels = data.map(d => {
        const type = d.is_organization || d.name;
        return type === 'org' ? 'Organization' : 'Individual';
      });
      
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Posts',
            data: data.map(d => parseInt(d.post_freq) || 0),
            backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(3, 128, 71, 0.8)'],
            borderColor: ['#3b82f6', '#038047'],
            borderWidth: 2,
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#1a202c',
              padding: 12,
              titleFont: { size: 13, weight: '600' },
              bodyFont: { size: 12 },
              displayColors: false,
              cornerRadius: 8
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: '#f1f5f9', drawBorder: false },
              ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } }
            },
            x: {
              grid: { display: false },
              ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } }
            }
          }
        }
      });

      loading.style.display = 'none';
      canvas.style.display = 'block';
    }

    // Helper Functions
    function addLoadingBadge(section) {
      if (!section || section.querySelector('.lazy-loading-badge')) return;
      
      const badge = document.createElement('div');
      badge.className = 'lazy-loading-badge';
      badge.innerHTML = '<div class="spinner"></div><span>Loading...</span>';
      badge.style.position = 'fixed';
      badge.style.top = '80px';
      badge.style.right = '32px';
      section.appendChild(badge);
    }

    function removeLoadingBadge(section) {
      if (!section) return;
      const badge = section.querySelector('.lazy-loading-badge');
      if (badge) {
        badge.style.opacity = '0';
        setTimeout(() => badge.remove(), 300);
      }
    }
  }
</script>
@endsection