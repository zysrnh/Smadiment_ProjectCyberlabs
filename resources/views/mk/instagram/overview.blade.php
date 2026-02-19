@extends('mk.layouts.app')

@section('title', 'Instagram Overview - SMADIMENT')

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
    --ig-gradient: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
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
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .ig-icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: var(--ig-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .ig-icon-wrapper svg {
    width: 24px;
    height: 24px;
    stroke: white;
    fill: none;
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
    border-color: #dc2743;
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(220, 39, 67, 0.1);
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
    background: var(--ig-gradient);
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
    box-shadow: 0 4px 12px rgba(220, 39, 67, 0.2);
  }

  .apply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 39, 67, 0.35);
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
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--ig-gradient);
    opacity: 0;
    transition: opacity 0.3s;
  }

  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
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
    background: linear-gradient(135deg, rgba(220,39,67,0.1) 0%, rgba(188,24,136,0.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .stat-icon {
    width: 28px;
    height: 28px;
    stroke: #dc2743;
    fill: none;
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
    background: var(--ig-gradient);
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
      grid-template-columns: 1.2fr 0.8fr;
    }
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
    height: 320px;
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
    border-color: #dc2743;
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(220, 39, 67, 0.1);
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

  .actions-dropdown { position: relative; }

  .actions-dropdown-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.2s;
  }

  .actions-dropdown-btn:hover {
    background: var(--bg-gray-50);
    border-color: #dc2743;
  }

  .actions-dropdown-btn svg {
    width: 16px;
    height: 16px;
    color: var(--text-secondary);
  }

  .actions-dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    min-width: 220px;
    padding: 8px;
    z-index: 1000;
    display: none;
  }

  .actions-dropdown-menu.show {
    display: block;
    animation: dropdownSlideIn 0.2s ease-out;
  }

  @keyframes dropdownSlideIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .actions-dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
  }

  .actions-dropdown-item:hover {
    background: var(--bg-gray-50);
    color: #dc2743;
  }

  .actions-dropdown-item svg {
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
  }

  .actions-dropdown-item:hover svg { color: #dc2743; }
  .actions-dropdown-divider { height: 1px; background: var(--border-gray); margin: 6px 0; }

  .data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 12px;
  }

  .data-table thead tr {
    background: var(--bg-white);
    border-bottom: 1px solid var(--border-gray);
  }

  .data-table th {
    padding: 10px 12px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 1px solid var(--border-gray);
    white-space: nowrap;
  }

  .data-table th:first-child { padding-left: 20px; }
  .data-table th:last-child { padding-right: 20px; }

  .data-table td {
    padding: 12px;
    font-size: 12px;
    color: var(--text-primary);
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }

  .data-table td:first-child { padding-left: 20px; }
  .data-table td:last-child { padding-right: 20px; }

  .data-table tbody tr {
    transition: all 0.2s;
    background: var(--bg-white);
  }

  .data-table tbody tr:hover { background: #fafbfc; }
  .data-table tbody tr:last-child td { border-bottom: none; }

  .avatar-container { position: relative; display: inline-block; }

  .user-avatar-img {
    width: 36px; height: 36px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
  }

  .user-avatar-fallback {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: var(--ig-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 13px;
  }

  .user-avatar {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: var(--ig-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 13px;
  }

  .account-name-link {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
  }

  .account-name-link:hover { color: #dc2743; text-decoration: underline; }

  .view-all-container {
    display: flex;
    justify-content: center;
    padding: 20px 0;
    border-top: 1px solid var(--border-gray);
    margin-top: 16px;
  }

  .view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 28px;
    background: var(--ig-gradient);
    border: none;
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all 0.3s;
  }

  .view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 39, 67, 0.3);
  }

  .view-all-btn svg { width: 18px; height: 18px; }

  /* Modal */
  .all-users-modal {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.75);
    backdrop-filter: blur(10px);
  }

  .all-users-modal.show { display: flex; }

  .all-users-modal .modal-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.8);
  }

  .all-users-modal .modal-content {
    position: relative;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    width: 95%;
    max-width: 1400px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    animation: modalSlideIn 0.3s ease-out;
    z-index: 10001;
  }

  .all-users-modal .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 16px 16px 0 0;
  }

  .all-users-modal .modal-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
  }

  .all-users-modal .modal-body {
    padding: 0;
    overflow-y: auto;
    border-radius: 0 0 16px 16px;
  }

  .modal-close {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: var(--bg-gray-50);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    color: var(--text-secondary);
  }

  .modal-close:hover { background: #ef4444; color: white; }

  @keyframes modalSlideIn {
    from { transform: translateY(-20px) scale(0.95); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
  }

  /* Loading */
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

  .skeleton-text { height: 44px; margin-bottom: 8px; }

  .lazy-loading-badge {
    position: absolute;
    top: 16px; right: 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: rgba(220, 39, 67, 0.1);
    color: #dc2743;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 10;
  }

  .spinner {
    width: 14px; height: 14px;
    border: 2px solid rgba(220, 39, 67, 0.2);
    border-top-color: #dc2743;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin { to { transform: rotate(360deg); } }

  [data-lazy-load] {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
  }

  [data-lazy-load].loaded { opacity: 1; transform: translateY(0); }

  .data-loaded {
    animation: fadeIn 0.4s ease-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }

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

  @media (max-width: 1024px) {
    .dashboard-container { padding: 16px; }
    .stats-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
    .filter-content { flex-direction: column; align-items: stretch; }
    .date-range-wrapper { flex-direction: column; }
    .apply-btn { width: 100%; justify-content: center; }
    .data-table { min-width: 900px; }
  }

  @media (max-width: 640px) {
    .stat-value { font-size: 28px; }
    .chart-container { height: 250px; }
    .table-search { width: 100%; }
    .page-header h1 { font-size: 22px; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>
      <div class="ig-icon-wrapper">
        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
          <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
          <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
        </svg>
      </div>
      Instagram Overview Dashboard
    </h1>
    <p>Monitor and analyze your Instagram social media performance metrics</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;flex-shrink:0;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view Instagram Overview data.</span>
  </div>
  @else

  <!-- Date Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.instagram.overview') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">

      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
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
            <input type="date" name="start_date" class="date-input" value="{{ $startDate }}" max="{{ date('Y-m-d') }}" required>
          </div>

          <span class="date-separator">to</span>

          <div class="date-input-group">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <input type="date" name="end_date" class="date-input" value="{{ $endDate }}" max="{{ date('Y-m-d') }}" required>
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
  <div class="stats-grid">

    <!-- Total Users -->
    <div class="stat-card" data-lazy-load="totalUsers">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Total Users</div>
      <div id="totalUsersValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:0%"></div></div>
    </div>

    <!-- Total Authors -->
    <div class="stat-card" data-lazy-load="totalAuthors">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Total Authors</div>
      <div id="totalAuthorsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:0%"></div></div>
    </div>

    <!-- Volume Total -->
    <div class="stat-card" data-lazy-load="volumeTotal">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" stroke-width="2">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Volume Total</div>
      <div id="volumeTotalValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:0%"></div></div>
    </div>

    <!-- Sentiment -->
    <div class="stat-card" data-lazy-load="sentiment">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
            <line x1="9" y1="9" x2="9.01" y2="9"/>
            <line x1="15" y1="9" x2="15.01" y2="9"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Sentiment Score</div>
      <div id="sentimentValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" style="width:0%"></div></div>
    </div>

  </div>

  <!-- Charts Section -->
  <div class="charts-section">

    <!-- Volume Trend Chart -->
    <div class="chart-card" data-lazy-load="volumeTotal">
      <div class="chart-header">
        <div class="chart-title-group">
          <h3>Volume Trend</h3>
          <p class="chart-subtitle">Daily posting volume over time</p>
        </div>
      </div>
      <div class="chart-container">
        <div id="volumeTrendLoading" class="loading-skeleton" style="height:100%;"></div>
        <canvas id="volumeTrendChart" style="display:none;"></canvas>
      </div>
    </div>

    <!-- Sentiment Distribution Chart -->
    <div class="chart-card" data-lazy-load="sentiment">
      <div class="chart-header">
        <div class="chart-title-group">
          <h3>Sentiment Distribution</h3>
          <p class="chart-subtitle">Positive, neutral, and negative breakdown</p>
        </div>
      </div>
      <div class="chart-container">
        <div id="sentimentLoading" class="loading-skeleton" style="height:100%;"></div>
        <canvas id="sentimentChart" style="display:none;"></canvas>
      </div>
    </div>

  </div>

  <!-- Most Active Users Table -->
  <div class="table-section" data-lazy-load="activeUsers">
    <div class="table-header">
      <div class="table-title">
        <h3>Most Active Users</h3>
        <p class="table-subtitle">Top 10 users with highest posting frequency</p>
      </div>

      <div class="table-actions">
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <input type="text" id="userSearchInput" placeholder="Search users..." onkeyup="filterUsers()">
        </div>

        <div class="actions-dropdown">
          <button class="actions-dropdown-btn" onclick="toggleActionsDropdown(event)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="1"/>
              <circle cx="12" cy="5" r="1"/>
              <circle cx="12" cy="19" r="1"/>
            </svg>
            Actions
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>

          <div class="actions-dropdown-menu" id="actionsDropdownMenu">
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault(); exportUsers()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              Export to CSV
            </a>
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault(); refreshUsers()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"/>
                <polyline points="1 20 1 14 7 14"/>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
              </svg>
              Refresh Data
            </a>
            <div class="actions-dropdown-divider"></div>
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault(); printTable()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
              </svg>
              Print Table
            </a>
          </div>
        </div>
      </div>
    </div>

    <div id="activeUsersLoading" class="loading-skeleton" style="height:400px;"></div>
    <div id="activeUsersTable" style="display:none; overflow-x:auto;"></div>

    <div id="viewAllContainer" class="view-all-container" style="display:none;">
      <button class="view-all-btn" onclick="showAllUsersModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
          <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
          <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
        </svg>
        View All Users (<span id="remainingCount">0</span> more)
      </button>
    </div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script>
  const projectId = '{{ $projectId ?? '' }}';
  const startDate = '{{ $startDate ?? '' }}';
  const endDate   = '{{ $endDate ?? '' }}';

  let allUsers      = [];
  let displayedCount = 10;

  if (projectId && startDate && endDate) {

    function formatNumber(num) {
      return new Intl.NumberFormat('en-US').format(num);
    }

    const loadedComponents = new Set();

    const lazyLoadObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const id = entry.target.dataset.lazyLoad;
          if (!loadedComponents.has(id)) {
            loadedComponents.add(id);
            switch (id) {
              case 'totalUsers':   loadTotalUsers();    break;
              case 'totalAuthors': loadTotalAuthors();  break;
              case 'volumeTotal':  loadVolumeTotal();   break;
              case 'sentiment':    loadSentimentTotal(); break;
              case 'activeUsers':  loadMostActiveUsers(); break;
            }
            lazyLoadObserver.unobserve(entry.target);
          }
        }
      });
    }, { rootMargin: '50px', threshold: 0.01 });

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('[data-lazy-load]').forEach(el => lazyLoadObserver.observe(el));
    });

    // ── Loaders ──────────────────────────────────────────

    async function loadTotalUsers() {
      const card = document.querySelector('[data-lazy-load="totalUsers"]');
      addLoadingBadge(card);
      try {
        const res    = await fetch(`/mk/api/instagram/total-users?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        const result = await res.json();
        if (result.success) {
          const total  = result.data.total || 0;
          const el     = document.getElementById('totalUsersValue');
          el.innerHTML = `<div class="stat-value">${formatNumber(total)}</div>`;
          el.classList.add('data-loaded');
          animateProgress(card, 75);
        }
      } catch (e) { console.error(e); }
      finally { removeLoadingBadge(card); card.classList.add('loaded'); }
    }

    async function loadTotalAuthors() {
      const card = document.querySelector('[data-lazy-load="totalAuthors"]');
      addLoadingBadge(card);
      try {
        const res    = await fetch(`/mk/api/instagram/total-authors?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        const result = await res.json();
        if (result.success) {
          const total  = result.data.total || 0;
          const el     = document.getElementById('totalAuthorsValue');
          el.innerHTML = `<div class="stat-value">${formatNumber(total)}</div>`;
          el.classList.add('data-loaded');
          animateProgress(card, 68);
        }
      } catch (e) { console.error(e); }
      finally { removeLoadingBadge(card); card.classList.add('loaded'); }
    }

    async function loadVolumeTotal() {
      const cards = document.querySelectorAll('[data-lazy-load="volumeTotal"]');
      cards.forEach(c => addLoadingBadge(c));
      try {
        const res    = await fetch(`/mk/api/instagram/volume-total?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        const result = await res.json();
        if (result.success) {
          const total  = result.data.total || 0;
          const el     = document.getElementById('volumeTotalValue');
          el.innerHTML = `<div class="stat-value">${formatNumber(total)}</div>`;
          el.classList.add('data-loaded');
          renderVolumeTrendChart(result.data.chart || []);
          animateProgress(document.querySelector('.stat-card[data-lazy-load="volumeTotal"]'), 82);
        }
      } catch (e) { console.error(e); }
      finally { cards.forEach(c => { removeLoadingBadge(c); c.classList.add('loaded'); }); }
    }

    async function loadSentimentTotal() {
      const cards = document.querySelectorAll('[data-lazy-load="sentiment"]');
      cards.forEach(c => addLoadingBadge(c));
      try {
        const res    = await fetch(`/mk/api/instagram/sentiment-total?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        const result = await res.json();
        if (result.success) {
          const { positive = 0, neutral = 0, negative = 0 } = result.data;
          const total = positive + neutral + negative;
          const score = total > 0 ? ((positive * 100 + neutral * 50) / total).toFixed(1) : 0;
          const el    = document.getElementById('sentimentValue');
          el.innerHTML = `<div class="stat-value">${score}%</div>`;
          el.classList.add('data-loaded');
          renderSentimentChart({ positive, neutral, negative });
          animateProgress(document.querySelector('.stat-card[data-lazy-load="sentiment"]'), parseFloat(score));
        }
      } catch (e) { console.error(e); }
      finally { cards.forEach(c => { removeLoadingBadge(c); c.classList.add('loaded'); }); }
    }

    async function loadMostActiveUsers() {
      const card    = document.querySelector('[data-lazy-load="activeUsers"]');
      const loading = document.getElementById('activeUsersLoading');
      const container     = document.getElementById('activeUsersTable');
      const viewAllContainer = document.getElementById('viewAllContainer');
      addLoadingBadge(card);
      try {
        const res    = await fetch(`/mk/api/instagram/most-active-users?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
        const result = await res.json();
        if (result.success && result.data?.data) {
          allUsers = result.data.data;
          displayUsersTable(10);
          if (allUsers.length > 10) {
            viewAllContainer.style.display = 'flex';
            document.getElementById('remainingCount').textContent = allUsers.length - 10;
          }
          loading.style.display   = 'none';
          container.style.display = 'block';
        }
      } catch (e) { console.error(e); }
      finally { removeLoadingBadge(card); card.classList.add('loaded'); }
    }

    // ── Table render ─────────────────────────────────────

    function buildTableHTML(users) {
      let html = `<table class="data-table"><thead><tr>
        <th>NO.</th><th>AVATAR</th><th>NAME</th><th>POSTS</th><th>LIKES</th><th>COMMENTS</th>
      </tr></thead><tbody>`;

      users.forEach((item, i) => {
        const name    = item.username || item.name || 'Unknown';
        const pic     = item.profile_url || item.profile_image_url || '';
        const posts   = item.posts || item.y || 0;
        const likes   = item.likes   || 0;
        const comments = item.comments || 0;

        html += `<tr>
          <td><strong>${i + 1}</strong></td>
          <td>
            <div class="avatar-container">
              ${pic
                ? `<img src="${pic}" alt="${name}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                   <div class="user-avatar-fallback" style="display:none;">${name.charAt(0).toUpperCase()}</div>`
                : `<div class="user-avatar">${name.charAt(0).toUpperCase()}</div>`}
            </div>
          </td>
          <td><a href="https://instagram.com/${name}" target="_blank" class="account-name-link">${name}</a></td>
          <td><strong>${formatNumber(posts)}</strong></td>
          <td>${formatNumber(likes)}</td>
          <td>${formatNumber(comments)}</td>
        </tr>`;
      });

      html += '</tbody></table>';
      return html;
    }

    function displayUsersTable(count) {
      const container  = document.getElementById('activeUsersTable');
      container.innerHTML = buildTableHTML(allUsers.slice(0, count));
      container.classList.add('data-loaded');
      displayedCount = count;
    }

    function filterUsers() {
      const term      = document.getElementById('userSearchInput').value.toLowerCase();
      const container = document.getElementById('activeUsersTable');
      const filtered  = term
        ? allUsers.filter(u => (u.username || u.name || '').toLowerCase().includes(term))
        : allUsers.slice(0, displayedCount);

      if (term && filtered.length === 0) {
        container.innerHTML = `<table class="data-table"><tbody>
          <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-secondary);">No users found matching "${term}"</td></tr>
        </tbody></table>`;
      } else {
        container.innerHTML = buildTableHTML(filtered);
      }
    }

    function showAllUsersModal() {
      const modal = document.createElement('div');
      modal.className = 'all-users-modal show';
      modal.innerHTML = `
        <div class="modal-overlay" onclick="this.parentElement.remove()"></div>
        <div class="modal-content">
          <div class="modal-header">
            <h3>All Active Users (${allUsers.length} total)</h3>
            <button class="modal-close" onclick="this.closest('.all-users-modal').remove()">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
          <div class="modal-body">${buildTableHTML(allUsers)}</div>
        </div>`;
      document.body.appendChild(modal);
    }

    // ── Charts ───────────────────────────────────────────

    function renderVolumeTrendChart(data) {
      const canvas  = document.getElementById('volumeTrendChart');
      const loading = document.getElementById('volumeTrendLoading');

      if (!data || data.length === 0) {
        loading.innerHTML = '<p style="text-align:center;color:var(--text-secondary);padding:40px;">No data available</p>';
        return;
      }

      new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
          labels: data.map(d => d.date),
          datasets: [{
            label: 'Volume',
            data: data.map(d => d.count || d.value || 0),
            borderColor: '#dc2743',
            backgroundColor: 'rgba(220,39,67,0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#dc2743',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
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
              titleColor: '#fff',
              bodyColor: '#fff',
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
              ticks: { color: '#64748b', font: { family: 'Poppins', size: 12 }, padding: 8 }
            },
            x: {
              grid: { display: false, drawBorder: false },
              ticks: { color: '#64748b', font: { family: 'Poppins', size: 12 }, padding: 8 }
            }
          }
        }
      });

      loading.style.display = 'none';
      canvas.style.display  = 'block';
    }

    function renderSentimentChart(sentiment) {
      const canvas  = document.getElementById('sentimentChart');
      const loading = document.getElementById('sentimentLoading');

      new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: ['Positive', 'Neutral', 'Negative'],
          datasets: [{
            data: [sentiment.positive, sentiment.neutral, sentiment.negative],
            backgroundColor: ['#10b981', '#64748b', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 15
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '70%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: { color: '#1a202c', font: { family: 'Poppins', size: 13, weight: '600' }, padding: 20, usePointStyle: true, pointStyle: 'circle' }
            },
            tooltip: {
              backgroundColor: '#1a202c',
              padding: 16,
              titleColor: '#fff',
              bodyColor: '#fff',
              titleFont: { size: 14, weight: '600' },
              bodyFont: { size: 13 },
              displayColors: false,
              cornerRadius: 8,
              callbacks: {
                label: function(ctx) {
                  const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                  const pct   = ((ctx.parsed / total) * 100).toFixed(1);
                  return `${ctx.label}: ${formatNumber(ctx.parsed)} (${pct}%)`;
                }
              }
            }
          }
        }
      });

      loading.style.display = 'none';
      canvas.style.display  = 'block';
    }

    // ── Helpers ──────────────────────────────────────────

    function addLoadingBadge(card) {
      if (!card || card.querySelector('.lazy-loading-badge')) return;
      const badge = document.createElement('div');
      badge.className = 'lazy-loading-badge';
      badge.innerHTML = '<div class="spinner"></div><span>Loading...</span>';
      card.style.position = 'relative';
      card.appendChild(badge);
    }

    function removeLoadingBadge(card) {
      const badge = card?.querySelector('.lazy-loading-badge');
      if (badge) { badge.style.opacity = '0'; setTimeout(() => badge.remove(), 300); }
    }

    function animateProgress(card, pct) {
      const bar = card?.querySelector('.stat-progress-bar');
      if (bar) setTimeout(() => { bar.style.width = pct + '%'; }, 100);
    }
  }

  // ── Dropdown & Actions ───────────────────────────────

  function toggleActionsDropdown(event) {
    event.stopPropagation();
    document.getElementById('actionsDropdownMenu').classList.toggle('show');
  }

  document.addEventListener('click', () => {
    document.getElementById('actionsDropdownMenu')?.classList.remove('show');
  });

  function exportUsers() {
    document.getElementById('actionsDropdownMenu').classList.remove('show');
    let csv = "No.,Name,Posts,Likes,Comments\n";
    allUsers.forEach((u, i) => {
      const name    = (u.username || u.name || '').replace(/,/g, ' ');
      const posts   = u.posts || u.y || 0;
      const likes   = u.likes   || 0;
      const comments = u.comments || 0;
      csv += `${i + 1},"${name}",${posts},${likes},${comments}\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = `instagram_active_users_${startDate}_${endDate}.csv`;
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  function refreshUsers() {
    document.getElementById('actionsDropdownMenu').classList.remove('show');
    window.location.reload();
  }

  function printTable() {
    document.getElementById('actionsDropdownMenu').classList.remove('show');
    const win = window.open('', '_blank');
    win.document.write(`<!DOCTYPE html><html><head>
      <title>Most Active Users - Instagram Overview</title>
      <style>
        body{font-family:Arial,sans-serif;padding:20px;}
        h1{color:#1a202c;margin-bottom:10px;}
        p{color:#64748b;margin-bottom:20px;}
        table{width:100%;border-collapse:collapse;}
        th{background:#f8fafc;padding:12px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0;}
        td{padding:12px;font-size:12px;border-bottom:1px solid #f1f5f9;}
      </style></head><body>
      <h1>Most Active Users - Instagram</h1>
      <p>Date Range: ${startDate} to ${endDate}</p>
      ${document.getElementById('activeUsersTable').innerHTML}
    </body></html>`);
    win.document.close();
    setTimeout(() => { win.print(); win.close(); }, 250);
  }
</script>
@endsection