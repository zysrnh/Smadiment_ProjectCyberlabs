@extends('mk.layouts.app')

@section('title', 'X Geographic - SMADIMENT')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
    stroke: currentColor;
    fill: none;
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

  /* Stats Grid - IMPROVED */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
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

  .stat-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
    line-height: 1.2;
  }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
    word-break: break-word;
  }

  .stat-value.stat-value-text {
    font-size: 20px;
    font-weight: 700;
  }

  /* Geo Card - IMPROVED */
  .geo-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    margin-bottom: 24px;
  }

  .geo-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: 10;
  }

  .geo-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-green);
  }

  .geo-card:hover::before {
    opacity: 1;
  }

  .geo-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 2px solid var(--bg-gray-50);
    background: var(--bg-white);
  }

  .geo-card-head-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .geo-head-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(3, 128, 71, 0.1) 0%, rgba(3, 128, 71, 0.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    flex-shrink: 0;
  }

  .geo-head-icon::after {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 16px;
    padding: 4px;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s;
  }

  .geo-card:hover .geo-head-icon::after {
    opacity: 0.5;
  }

  .geo-head-icon svg {
    width: 28px;
    height: 28px;
    color: var(--primary-green);
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .geo-card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    font-family: 'Poppins', sans-serif;
    margin: 0 0 4px 0;
  }

  .geo-card-subtitle {
    font-size: 13px;
    color: var(--text-secondary);
    font-weight: 500;
    margin: 0;
  }

  .geo-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
    background: var(--bg-gray-100);
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-family: 'Poppins', sans-serif;
  }

  .geo-card-body {
    padding: 0;
  }

  /* Map Container - IMPROVED */
  .map-container {
    height: 600px;
    position: relative;
    width: 100%;
  }

  #geoMap,
  #geoSentimentMap {
    height: 100%;
    width: 100%;
    z-index: 1;
  }

  /* Leaflet Map Improvements */
  .leaflet-container {
    font-family: 'Poppins', sans-serif;
  }

  .leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  }

  .leaflet-popup-content {
    margin: 0;
    min-width: 200px;
  }

  /* Custom Marker Clustering Styles */
  .marker-cluster {
    background-clip: padding-box;
    border-radius: 50%;
  }

  .marker-cluster div {
    width: 30px;
    height: 30px;
    margin-left: 5px;
    margin-top: 5px;
    text-align: center;
    border-radius: 50%;
    font: 12px "Poppins", sans-serif;
    font-weight: 700;
  }

  .marker-cluster-small {
    background-color: rgba(3, 128, 71, 0.2);
  }

  .marker-cluster-small div {
    background-color: rgba(3, 128, 71, 0.6);
    color: white;
  }

  .marker-cluster-medium {
    background-color: rgba(3, 128, 71, 0.3);
  }

  .marker-cluster-medium div {
    background-color: rgba(3, 128, 71, 0.7);
    color: white;
  }

  .marker-cluster-large {
    background-color: rgba(3, 128, 71, 0.4);
  }

  .marker-cluster-large div {
    background-color: rgba(3, 128, 71, 0.8);
    color: white;
  }

  /* Locations Table */
  .locations-table-container {
    padding: 24px;
  }

  .locations-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .locations-table thead tr {
    background: var(--bg-gray-50);
  }

  .locations-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--border-gray);
  }

  .locations-table td {
    padding: 16px;
    font-size: 14px;
    color: var(--text-primary);
    border-bottom: 1px solid var(--bg-gray-100);
  }

  .locations-table tbody tr {
    transition: all 0.2s;
    background: var(--bg-white);
  }

  .locations-table tbody tr:hover {
    background: var(--bg-gray-50);
    transform: scale(1.01);
  }

  .locations-table tbody tr:last-child td {
    border-bottom: none;
  }

  .location-rank {
    font-weight: 700;
    color: var(--primary-green);
    font-size: 16px;
  }

  .location-name {
    font-weight: 600;
    color: var(--text-primary);
  }

  .location-count {
    font-weight: 700;
    color: var(--text-primary);
    text-align: right;
  }

  /* Skeleton Loading */
  .skeleton-map {
    height: 600px;
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
  }

  .skeleton-text {
    height: 32px;
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 8px;
  }

  @keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  [data-loaded="true"] .skeleton-map,
  [data-loaded="true"] .skeleton-text {
    display: none;
  }

  /* Responsive */
  @media (max-width: 1200px) {
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .dashboard-container {
      padding: 16px;
    }

    .stats-grid {
      grid-template-columns: 1fr;
    }

    .map-container {
      height: 450px;
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
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>X Geographic</h1>
    <p>Monitor geographic distribution and location-based analytics for X (Twitter)</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view geographic data.</span>
  </div>
  @else

  <!-- Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.geographic') }}">
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
            <svg viewBox="0 0 24 24">
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
            <svg viewBox="0 0 24 24">
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
  <div class="stats-grid" data-lazy-load="stats">
    <div class="stat-card">
      <div class="stat-label">Total Countries</div>
      <div id="totalCountries" class="stat-value">
        <div class="skeleton-text"></div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Total Users</div>
      <div id="totalUsers" class="stat-value">
        <div class="skeleton-text"></div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Top Country</div>
      <div id="topCountry" class="stat-value stat-value-text">
        <div class="skeleton-text"></div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Top Province</div>
      <div id="topProvince" class="stat-value stat-value-text">
        <div class="skeleton-text"></div>
      </div>
    </div>
  </div>

  <!-- Map 1: Geo X User -->
  <div class="geo-card" data-lazy-load="geoUser">
    <div class="geo-card-head">
      <div class="geo-card-head-left">
        <div class="geo-head-icon">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="10" r="3"/>
            <path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/>
          </svg>
        </div>
        <div>
          <h3 class="geo-card-title">Geographic User Distribution</h3>
          <p class="geo-card-subtitle">X users by country and province</p>
        </div>
      </div>
      <span class="geo-badge">All Users</span>
    </div>
    <div class="geo-card-body">
      <div class="skeleton-map"></div>
      <div class="map-container" style="display: none;">
        <div id="geoMap"></div>
      </div>
    </div>
  </div>

  <!-- Map 2: Geo X User Sentiment -->
  <div class="geo-card" data-lazy-load="geoSentiment">
    <div class="geo-card-head">
      <div class="geo-card-head-left">
        <div class="geo-head-icon">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
            <line x1="9" y1="9" x2="9.01" y2="9"/>
            <line x1="15" y1="9" x2="15.01" y2="9"/>
          </svg>
        </div>
        <div>
          <h3 class="geo-card-title">Sentiment by Location</h3>
          <p class="geo-card-subtitle">Positive, negative, and neutral sentiment distribution</p>
        </div>
      </div>
      <span class="geo-badge">Sentiment</span>
    </div>
    <div class="geo-card-body">
      <div class="skeleton-map"></div>
      <div class="map-container" style="display: none;">
        <div id="geoSentimentMap"></div>
      </div>
    </div>
  </div>

  <!-- Table: Top Author Locations -->
  <div class="geo-card" data-lazy-load="topLocations">
    <div class="geo-card-head">
      <div class="geo-card-head-left">
        <div class="geo-head-icon">
          <svg viewBox="0 0 24 24">
            <line x1="8" y1="6" x2="21" y2="6"/>
            <line x1="8" y1="12" x2="21" y2="12"/>
            <line x1="8" y1="18" x2="21" y2="18"/>
            <line x1="3" y1="6" x2="3.01" y2="6"/>
            <line x1="3" y1="12" x2="3.01" y2="12"/>
            <line x1="3" y1="18" x2="3.01" y2="18"/>
          </svg>
        </div>
        <div>
          <h3 class="geo-card-title">Top Author Locations</h3>
          <p class="geo-card-subtitle">Ranking of locations by author count</p>
        </div>
      </div>
      <span class="geo-badge">Rankings</span>
    </div>
    <div class="locations-table-container">
      <div class="skeleton-text" style="margin-bottom: 12px;"></div>
      <div class="skeleton-text" style="margin-bottom: 12px;"></div>
      <div class="skeleton-text"></div>
      <div id="topLocationsTable" style="display: none;"></div>
    </div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  const projectId = '{{ $projectId ?? '' }}';
  const startDate = '{{ $startDate ?? '' }}';
  const endDate = '{{ $endDate ?? '' }}';

  if (projectId && startDate && endDate) {
    
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
            
            switch(componentId) {
              case 'stats':
                loadStats();
                break;
              case 'geoUser':
                loadGeoUser();
                break;
              case 'geoSentiment':
                loadGeoSentiment();
                break;
              case 'topLocations':
                loadTopLocations();
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

    // Load Stats
    async function loadStats() {
      const card = document.querySelector('[data-lazy-load="stats"]');
      
      try {
        const response = await fetch(`/mk/api/x/geo-user?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        const result = await response.json();
        
        if (result.success && result.data) {
          const data = result.data.country || {};
          const rows = data.rows || [];
          
          // Total Countries
          document.getElementById('totalCountries').innerHTML = `${rows.length}`;
          
          // Total Users
          document.getElementById('totalUsers').innerHTML = `${(data.total || 0).toLocaleString()}`;
          
          // Top Country
          const topCountry = rows[0];
          if (topCountry) {
            document.getElementById('topCountry').innerHTML = `${topCountry.name}`;
            
            // Top Province (from detail of top country)
            const detail = topCountry.detail || {};
            const provinces = Object.entries(detail).sort((a, b) => b[1] - a[1]);
            if (provinces.length > 0) {
              document.getElementById('topProvince').innerHTML = `${provinces[0][0]}`;
            } else {
              document.getElementById('topProvince').innerHTML = `N/A`;
            }
          } else {
            document.getElementById('topCountry').innerHTML = `N/A`;
            document.getElementById('topProvince').innerHTML = `N/A`;
          }
        }
        
        card.dataset.loaded = 'true';
      } catch (error) {
        console.error('Error loading stats:', error);
        document.getElementById('totalCountries').innerHTML = `0`;
        document.getElementById('totalUsers').innerHTML = `0`;
        document.getElementById('topCountry').innerHTML = `N/A`;
        document.getElementById('topProvince').innerHTML = `N/A`;
      }
    }

    // Load Geo User Map
    async function loadGeoUser() {
      const card = document.querySelector('[data-lazy-load="geoUser"]');
      
      try {
        const response = await fetch(`/mk/api/x/geo-user?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        const result = await response.json();
        
        if (result.success && result.data) {
          const data = result.data.country || {};
          const center = data.center || { lat: -2.5, lng: 118 }; // Indonesia center
          const rows = data.rows || [];
          
          // Initialize map with better view
          const map = L.map('geoMap').setView([center.lat, center.lng], 5);
          
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
          }).addTo(map);
          
          // Calculate bounds for auto-fit
          const bounds = [];
          
          // Add markers
          rows.forEach(location => {
            const lat = parseFloat(location.latitude);
            const lng = parseFloat(location.longitude);
            const count = parseInt(location.count || 0);
            const name = location.name || 'Unknown';
            
            if (lat && lng && count > 0) {
              bounds.push([lat, lng]);
              
              // Calculate radius based on count
              const maxCount = Math.max(...rows.map(r => parseInt(r.count || 0)));
              const minRadius = 10000;
              const maxRadius = 100000;
              const radius = minRadius + ((count / maxCount) * (maxRadius - minRadius));
              
              // Add circle with better visibility
              L.circle([lat, lng], {
                radius: radius,
                fillColor: '#038047',
                color: '#026738',
                weight: 2,
                opacity: 0.6,
                fillOpacity: 0.3
              }).addTo(map);
              
              // Add marker with custom icon
              const marker = L.marker([lat, lng], {
                icon: L.divIcon({
                  className: 'custom-marker',
                  html: `<div style="background: #038047; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                  iconSize: [24, 24],
                  iconAnchor: [12, 12]
                })
              }).addTo(map);
              
              // Enhanced popup content
              let popupContent = `
                <div style="font-family: Poppins, sans-serif; min-width: 220px;">
                  <div style="text-align: center; padding: 16px; border-bottom: 2px solid #f1f5f9;">
                    <div style="font-weight: 700; font-size: 18px; color: #1a202c; margin-bottom: 8px;">${name}</div>
                    <div style="font-size: 32px; font-weight: 800; color: #038047; margin-bottom: 4px;">${count.toLocaleString()}</div>
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">total users</div>
                  </div>
              `;
              
              // Add province details if available
              if (location.detail && Object.keys(location.detail).length > 0) {
                popupContent += '<div style="padding: 16px; max-height: 250px; overflow-y: auto;">';
                popupContent += '<div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px;">Top Provinces</div>';
                
                const provinces = Object.entries(location.detail)
                  .sort((a, b) => b[1] - a[1])
                  .slice(0, 10);
                
                provinces.forEach(([province, pCount], index) => {
                  const percentage = ((pCount / count) * 100).toFixed(1);
                  popupContent += `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding: 8px; background: ${index % 2 === 0 ? '#f8fafc' : 'white'}; border-radius: 6px;">
                      <div style="flex: 1;">
                        <div style="font-size: 13px; font-weight: 600; color: #1a202c; margin-bottom: 2px;">${province}</div>
                        <div style="font-size: 10px; color: #64748b;">${percentage}% of total</div>
                      </div>
                      <div style="font-size: 16px; font-weight: 700; color: #038047;">${pCount.toLocaleString()}</div>
                    </div>
                  `;
                });
                popupContent += '</div>';
              }
              
              popupContent += '</div>';
              marker.bindPopup(popupContent, { maxWidth: 300 });
            }
          });
          
          // Fit map to bounds if we have markers
          if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
          }
          
          card.querySelector('.skeleton-map').style.display = 'none';
          card.querySelector('.map-container').style.display = 'block';
          card.dataset.loaded = 'true';
        }
      } catch (error) {
        console.error('Error loading geo user map:', error);
      }
    }

    // Load Geo Sentiment Map
    async function loadGeoSentiment() {
      const card = document.querySelector('[data-lazy-load="geoSentiment"]');
      
      try {
        const response = await fetch(`/mk/api/x/geo-sentiment?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        const result = await response.json();
        
        if (result.success && result.data) {
          const data = result.data.country || {};
          const center = data.center || { lat: -2.5, lng: 118 };
          const rows = data.rows || [];
          
          // Initialize map
          const map = L.map('geoSentimentMap').setView([center.lat, center.lng], 5);
          
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
          }).addTo(map);
          
          const bounds = [];
          
          // Add markers with sentiment
          rows.forEach(location => {
            const lat = parseFloat(location.latitude);
            const lng = parseFloat(location.longitude);
            const count = parseInt(location.count || 0);
            const name = location.name || 'Unknown';
            const pos = parseInt(location.pos || 0);
            const neg = parseInt(location.neg || 0);
            const net = parseInt(location.net || 0);
            
            if (lat && lng && count > 0) {
              bounds.push([lat, lng]);
              
              // Calculate dominant sentiment
              let color = '#64748b'; // neutral
              let sentiment = 'Neutral';
              if (pos > neg && pos > net) {
                color = '#22c55e'; // positive
                sentiment = 'Positive';
              } else if (neg > pos && neg > net) {
                color = '#ef4444'; // negative
                sentiment = 'Negative';
              }
              
              // Calculate radius
              const maxCount = Math.max(...rows.map(r => parseInt(r.count || 0)));
              const minRadius = 10000;
              const maxRadius = 100000;
              const radius = minRadius + ((count / maxCount) * (maxRadius - minRadius));
              
              // Add circle
              L.circle([lat, lng], {
                radius: radius,
                fillColor: color,
                color: color,
                weight: 2,
                opacity: 0.6,
                fillOpacity: 0.3
              }).addTo(map);
              
              // Add marker
              const marker = L.marker([lat, lng], {
                icon: L.divIcon({
                  className: 'custom-marker',
                  html: `<div style="background: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                  iconSize: [24, 24],
                  iconAnchor: [12, 12]
                })
              }).addTo(map);
              
              // Enhanced popup with sentiment
              let popupContent = `
                <div style="font-family: Poppins, sans-serif; min-width: 240px;">
                  <div style="text-align: center; padding: 16px; border-bottom: 2px solid #f1f5f9;">
                    <div style="font-weight: 700; font-size: 18px; color: #1a202c; margin-bottom: 8px;">${name}</div>
                    <div style="display: inline-block; padding: 6px 16px; background: ${color}20; border-radius: 20px; margin-bottom: 12px;">
                      <span style="font-size: 12px; font-weight: 700; color: ${color}; text-transform: uppercase;">${sentiment} Dominant</span>
                    </div>
                    <div style="font-size: 28px; font-weight: 800; color: ${color}; margin-bottom: 4px;">${count.toLocaleString()}</div>
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">total mentions</div>
                  </div>
                  <div style="padding: 16px;">
                    <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px;">Sentiment Breakdown</div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                      <div style="text-align: center; padding: 12px; background: #f0fdf4; border-radius: 8px;">
                        <div style="font-size: 20px; font-weight: 700; color: #22c55e; margin-bottom: 4px;">${pos}</div>
                        <div style="font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 600;">Positive</div>
                        <div style="font-size: 9px; color: #64748b; margin-top: 2px;">${((pos/count)*100).toFixed(1)}%</div>
                      </div>
                      <div style="text-align: center; padding: 12px; background: #f8fafc; border-radius: 8px;">
                        <div style="font-size: 20px; font-weight: 700; color: #64748b; margin-bottom: 4px;">${net}</div>
                        <div style="font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 600;">Neutral</div>
                        <div style="font-size: 9px; color: #64748b; margin-top: 2px;">${((net/count)*100).toFixed(1)}%</div>
                      </div>
                      <div style="text-align: center; padding: 12px; background: #fef2f2; border-radius: 8px;">
                        <div style="font-size: 20px; font-weight: 700; color: #ef4444; margin-bottom: 4px;">${neg}</div>
                        <div style="font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 600;">Negative</div>
                        <div style="font-size: 9px; color: #64748b; margin-top: 2px;">${((neg/count)*100).toFixed(1)}%</div>
                      </div>
                    </div>
                  </div>
                </div>
              `;
              
              marker.bindPopup(popupContent, { maxWidth: 320 });
            }
          });
          
          if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
          }
          
          card.querySelector('.skeleton-map').style.display = 'none';
          card.querySelector('.map-container').style.display = 'block';
          card.dataset.loaded = 'true';
        }
      } catch (error) {
        console.error('Error loading geo sentiment map:', error);
      }
    }

    // Load Top Locations Table
    async function loadTopLocations() {
      const card = document.querySelector('[data-lazy-load="topLocations"]');
      
      try {
        const response = await fetch(`/mk/api/x/top-locations?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        const result = await response.json();
        
        if (result.success && result.data) {
          const locations = result.data;
          
          let html = `
            <table class="locations-table">
              <thead>
                <tr>
                  <th style="width: 80px;">RANK</th>
                  <th>LOCATION</th>
                  <th style="text-align: right; width: 120px;">AUTHORS</th>
                </tr>
              </thead>
              <tbody>
          `;
          
          locations.forEach((loc, index) => {
            html += `
              <tr>
                <td class="location-rank">#${index + 1}</td>
                <td class="location-name">${loc.name || 'Unknown'}</td>
                <td class="location-count">${(loc.count || 0).toLocaleString()}</td>
              </tr>
            `;
          });
          
          html += '</tbody></table>';
          
          document.getElementById('topLocationsTable').innerHTML = html;
          document.getElementById('topLocationsTable').style.display = 'block';
          
          card.querySelectorAll('.skeleton-text').forEach(el => el.style.display = 'none');
          card.dataset.loaded = 'true';
        }
      } catch (error) {
        console.error('Error loading top locations:', error);
      }
    }
  }
</script>
@endsection