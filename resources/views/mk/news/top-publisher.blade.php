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

  .data-table th:first-child { padding-left: 20px; }
  .data-table th:last-child { padding-right: 20px; }

  .data-table td {
    padding: 16px;
    font-size: 14px;
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
    from { opacity: 0; transform: scale(0.95); }
    to   { opacity: 1; transform: scale(1); }
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

  /* Date Picker Modal */
  .date-picker-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(8px);
  }

  .date-picker-modal.show {
    display: flex;
  }

  .date-picker-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    cursor: pointer;
  }

  .date-picker-container {
    position: relative;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    display: flex;
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    z-index: 10001;
    animation: slideUp 0.3s ease-out;
  }

  @keyframes slideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }

  /* Sidebar with Presets */
  .date-picker-sidebar {
    width: 180px;
    background: var(--bg-gray-50);
    border-right: 1px solid var(--border-gray);
    padding: 16px 12px;
    border-radius: 16px 0 0 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex-shrink: 0;
  }

  .date-preset {
    padding: 10px 16px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    text-align: left;
    cursor: pointer;
    transition: all 0.2s;
  }

  .date-preset:hover { background: var(--bg-white); color: var(--primary-green); }
  .date-preset.active { background: var(--primary-green); color: white; }

  /* Calendar Content */
  .date-picker-content {
    flex: 1;
    padding: 24px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .date-picker-header {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 20px;
  }

  .nav-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--bg-gray-50);
    border: 1px solid var(--border-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
  }

  .nav-btn:hover {
    background: var(--primary-green);
    border-color: var(--primary-green);
    color: white;
  }

  .nav-btn svg { width: 20px; height: 20px; }

  /* Calendars Wrapper */
  .calendars-wrapper {
    display: flex;
    gap: 24px;
    flex: 1;
    min-height: 0;
  }

  .calendar {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .calendar-month {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 16px;
    text-align: center;
  }

  .calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin-bottom: 8px;
  }

  .weekday {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-secondary);
    padding: 8px 0;
  }

  .calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
  }

  .calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 500;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--text-primary);
    background: transparent;
    border: none;
    padding: 0;
  }

  .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
  .calendar-day.other-month { color: #cbd5e1; cursor: default; }
  .calendar-day.disabled { color: #e2e8f0; cursor: not-allowed; }
  .calendar-day.today { border: 2px solid var(--primary-green); }
  .calendar-day.selected { background: var(--primary-green); color: white; }
  .calendar-day.in-range { background: rgba(3, 128, 71, 0.1); color: var(--primary-green); }
  .calendar-day.range-start, .calendar-day.range-end { background: var(--primary-green); color: white; }

  /* Date Display */
  .date-picker-display {
    padding: 16px 20px;
    background: var(--bg-gray-50);
    border-radius: 12px;
    text-align: center;
    margin-bottom: 20px;
    border: 1px solid var(--border-gray);
  }

  .date-picker-display span {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
  }

  /* Footer Buttons */
  .date-picker-footer {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
  }

  .cancel-btn,
  .apply-date-btn {
    padding: 10px 24px;
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
  }

  .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
  .cancel-btn:hover { background: var(--border-gray); }

  .apply-date-btn {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
  }

  .apply-date-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
  }

  /* Pagination — numbered style */
  .pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 20px 24px;
    background: var(--bg-white);
    border-top: 1px solid var(--border-gray);
    margin-top: 16px;
    flex-wrap: wrap;
  }

  .pagination-info {
    font-size: 13px;
    color: var(--text-secondary);
    font-weight: 500;
  }

  .page-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid var(--border-gray);
    background: var(--bg-white);
    color: var(--text-primary);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
  }

  .page-btn:hover:not(:disabled) {
    border-color: var(--primary-green);
    color: var(--primary-green);
    background: rgba(3, 128, 71, 0.05);
  }

  .page-btn.active {
    background: var(--primary-green);
    color: white;
    border-color: var(--primary-green);
  }

  .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

  /* Responsive */
  @media (max-width: 1024px) {
    .dashboard-container { padding: 16px; }
    .stats-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
    .filter-content { flex-direction: column; align-items: stretch; }
    .date-range-wrapper { flex-direction: column; }
    .apply-btn, .filter-select { width: 100%; justify-content: center; }
  }

  @media (max-width: 768px) {
    .stat-value { font-size: 28px; }
    .table-search { width: 100%; }
    .page-header h1 { font-size: 24px; }
    .data-table { font-size: 12px; }
    .data-table th, .data-table td { padding: 10px 12px; }
    .date-picker-trigger, .filter-select { max-width: 100%; }
    .date-picker-container { flex-direction: column; max-height: 85vh; overflow-y: auto; width: 95%; }
    .date-picker-sidebar {
      width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray);
      border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; padding: 12px 16px;
    }
    .date-preset { white-space: nowrap; }
    .date-picker-content { padding: 20px 16px; }
    .calendars-wrapper { flex-direction: column; gap: 16px; }
    .date-picker-header { flex-wrap: wrap; }
    .calendar-day { font-size: 12px; }
    .weekday { font-size: 10px; }
    .cancel-btn, .apply-date-btn { flex: 1; }
    .pagination { padding: 16px; }
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

  <!-- Date Range Picker Modal -->
  <div class="date-picker-modal" id="datePickerModal">
    <div class="date-picker-overlay"></div>
    <div class="date-picker-container">
      <div class="date-picker-sidebar">
        <button type="button" class="date-preset" data-preset="today">Today</button>
        <button type="button" class="date-preset" data-preset="yesterday">Yesterday</button>
        <button type="button" class="date-preset" data-preset="last7days">Last 7 Days</button>
        <button type="button" class="date-preset" data-preset="last30days">Last 30 Days</button>
        <button type="button" class="date-preset" data-preset="thismonth">This Month</button>
        <button type="button" class="date-preset" data-preset="lastmonth">Last Month</button>
        <button type="button" class="date-preset active" data-preset="custom">Custom Range</button>
      </div>
      <div class="date-picker-content">
        <div class="date-picker-header">
          <button type="button" class="nav-btn" id="prevMonth">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="15 18 9 12 15 6"/>
            </svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="calendar1"></div>
            <div class="calendar" id="calendar2"></div>
          </div>
          <button type="button" class="nav-btn" id="nextMonth">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </button>
        </div>
        <div class="date-picker-display">
          <span id="selectedRangeText">{{ $startDate }} to {{ $endDate }}</span>
        </div>
        <div class="date-picker-footer">
          <button type="button" class="cancel-btn">Cancel</button>
          <button type="button" class="apply-date-btn" id="applyDatePicker">Apply</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid">
    
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
      <div class="stat-progress"><div class="stat-progress-bar" style="width: 0%"></div></div>
    </div>

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
      <div class="stat-progress"><div class="stat-progress-bar" style="width: 0%"></div></div>
    </div>

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
      <div class="stat-progress"><div class="stat-progress-bar" style="width: 0%"></div></div>
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
    
    <!-- Pagination — numbered style -->
    <div class="pagination" id="pagination" style="display: none;"></div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script>
// ========================================
// DATE PICKER JAVASCRIPT
// ========================================
(function() {
  'use strict';
  
  let selectedStartDate = null;
  let selectedEndDate = null;
  let currentMonth1 = new Date();
  let currentMonth2 = new Date();
  let selectingStart = true;

  document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('hiddenStartDate');
    const endDateInput = document.getElementById('hiddenEndDate');
    
    if (startDateInput && startDateInput.value) {
      selectedStartDate = new Date(startDateInput.value);
    } else {
      selectedEndDate = new Date();
      selectedStartDate = new Date();
      selectedStartDate.setDate(selectedStartDate.getDate() - 6);
    }
    
    if (endDateInput && endDateInput.value) {
      selectedEndDate = new Date(endDateInput.value);
    }
    
    currentMonth1 = new Date(selectedStartDate);
    currentMonth2 = new Date(selectedStartDate);
    currentMonth2.setMonth(currentMonth2.getMonth() + 1);
    
    renderCalendars();
    setupEventListeners();
  });

  function setupEventListeners() {
    const trigger = document.getElementById('datePickerTrigger');
    if (trigger) trigger.addEventListener('click', openDatePicker);

    const overlay = document.querySelector('.date-picker-overlay');
    if (overlay) overlay.addEventListener('click', closeDatePicker);

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('datePickerModal');
        if (modal && modal.classList.contains('show')) closeDatePicker();
      }
    });

    document.querySelectorAll('.date-preset').forEach(btn => {
      btn.addEventListener('click', handlePresetClick);
    });

    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
      prevBtn.addEventListener('click', function() {
        currentMonth1.setMonth(currentMonth1.getMonth() - 1);
        currentMonth2.setMonth(currentMonth2.getMonth() - 1);
        renderCalendars();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function() {
        currentMonth1.setMonth(currentMonth1.getMonth() + 1);
        currentMonth2.setMonth(currentMonth2.getMonth() + 1);
        renderCalendars();
      });
    }

    const applyBtn = document.getElementById('applyDatePicker');
    if (applyBtn) applyBtn.addEventListener('click', applyDateSelection);

    const cancelBtn = document.querySelector('.cancel-btn');
    if (cancelBtn) cancelBtn.addEventListener('click', closeDatePicker);
  }

  function openDatePicker() {
    document.getElementById('datePickerModal').classList.add('show');
    renderCalendars();
  }

  function closeDatePicker() {
    document.getElementById('datePickerModal').classList.remove('show');
  }

  function handlePresetClick(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');

    const preset = e.target.dataset.preset;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    switch(preset) {
      case 'today':
        selectedStartDate = new Date(today);
        selectedEndDate = new Date(today);
        break;
      case 'yesterday':
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 1);
        selectedEndDate = new Date(selectedStartDate);
        break;
      case 'last7days':
        selectedEndDate = new Date(today);
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 6);
        break;
      case 'last30days':
        selectedEndDate = new Date(today);
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 29);
        break;
      case 'thismonth':
        selectedStartDate = new Date(today.getFullYear(), today.getMonth(), 1);
        selectedEndDate = new Date(today);
        break;
      case 'lastmonth':
        selectedStartDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        selectedEndDate = new Date(today.getFullYear(), today.getMonth(), 0);
        break;
    }
    
    if (preset !== 'custom') {
      currentMonth1 = new Date(selectedStartDate);
      currentMonth2 = new Date(selectedStartDate);
      currentMonth2.setMonth(currentMonth2.getMonth() + 1);
      updateDateDisplay();
      renderCalendars();
    }
  }

  function applyDateSelection() {
    const start = formatDate(selectedStartDate);
    const end = formatDate(selectedEndDate);
    document.getElementById('hiddenStartDate').value = start;
    document.getElementById('hiddenEndDate').value = end;
    const displayElement = document.getElementById('dateRangeDisplay');
    if (displayElement) displayElement.textContent = `${start} to ${end}`;
    closeDatePicker();
  }

  function renderCalendars() {
    renderCalendar('calendar1', currentMonth1);
    renderCalendar('calendar2', currentMonth2);
    updateDateDisplay();
  }

  function renderCalendar(elementId, month) {
    const calendar = document.getElementById(elementId);
    if (!calendar) return;

    const year = month.getFullYear();
    const monthNum = month.getMonth();
    const firstDay = new Date(year, monthNum, 1);
    const lastDay = new Date(year, monthNum + 1, 0);
    const prevLastDay = new Date(year, monthNum, 0);
    
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const weekdays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
    
    let html = `
      <div class="calendar-month">${monthNames[monthNum]} ${year}</div>
      <div class="calendar-weekdays">
        ${weekdays.map(day => `<div class="weekday">${day}</div>`).join('')}
      </div>
      <div class="calendar-days">
    `;
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const firstDayOfWeek = firstDay.getDay();
    for (let i = 0; i < firstDayOfWeek; i++) {
      const prevMonthDay = prevLastDay.getDate() - (firstDayOfWeek - 1 - i);
      html += `<button type="button" class="calendar-day other-month" disabled>${prevMonthDay}</button>`;
    }
    
    for (let day = 1; day <= lastDay.getDate(); day++) {
      const date = new Date(year, monthNum, day);
      date.setHours(0, 0, 0, 0);
      const dateStr = formatDate(date);
      let classes = 'calendar-day';
      
      if (isSameDay(date, today)) classes += ' today';
      if (date > today) classes += ' disabled';
      
      if (selectedStartDate && selectedEndDate) {
        if (isSameDay(date, selectedStartDate))      classes += ' selected range-start';
        else if (isSameDay(date, selectedEndDate))   classes += ' selected range-end';
        else if (date > selectedStartDate && date < selectedEndDate) classes += ' in-range';
      }
      
      const disabled = date > today ? 'disabled' : '';
      html += `<button type="button" class="${classes}" data-date="${dateStr}" ${disabled}>${day}</button>`;
    }
    
    const lastDayOfWeek = lastDay.getDay();
    const remainingCells = lastDayOfWeek === 6 ? 0 : 6 - lastDayOfWeek;
    for (let i = 1; i <= remainingCells; i++) {
      html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    }
    
    html += '</div>';
    calendar.innerHTML = html;
    
    calendar.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => {
      btn.addEventListener('click', handleDateClick);
    });
  }

  function handleDateClick(e) {
    const dateStr = e.target.dataset.date;
    const date = new Date(dateStr);
    date.setHours(0, 0, 0, 0);
    
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    const customPreset = document.querySelector('[data-preset="custom"]');
    if (customPreset) customPreset.classList.add('active');
    
    if (selectingStart || date < selectedStartDate) {
      selectedStartDate = date;
      selectedEndDate = date;
      selectingStart = false;
    } else {
      if (date >= selectedStartDate) {
        selectedEndDate = date;
      } else {
        selectedEndDate = selectedStartDate;
        selectedStartDate = date;
      }
      selectingStart = true;
    }
    
    updateDateDisplay();
    renderCalendars();
  }

  function updateDateDisplay() {
    if (!selectedStartDate || !selectedEndDate) return;
    const start = formatDate(selectedStartDate);
    const end = formatDate(selectedEndDate);
    const displayElement = document.getElementById('selectedRangeText');
    if (displayElement) displayElement.textContent = `${start} to ${end}`;
  }

  function formatDate(date) {
    if (!date) return '';
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  function isSameDay(date1, date2) {
    if (!date1 || !date2) return false;
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
  }
})();

// ========================================
// MAIN LOGIC
// ========================================
const projectId = '{{ $projectId ?? '' }}';
const startDate = '{{ $startDate ?? '' }}';
const endDate = '{{ $endDate ?? '' }}';
const newsType = '{{ $newsType ?? 'article' }}';

let allPublishers = [];
let currentPage = 1;
let publishersPerPage = 20;

if (projectId && startDate && endDate) {
  
  function formatNumber(num) {
    return new Intl.NumberFormat('en-US').format(num);
  }

  // Lazy loading setup
  const lazyLoadConfig = { rootMargin: '50px', threshold: 0.01 };
  const loadedComponents = new Set();

  const lazyLoadObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const componentId = entry.target.dataset.lazyLoad;
        if (!loadedComponents.has(componentId)) {
          loadedComponents.add(componentId);
          if (componentId === 'publishers') loadPublishers();
          lazyLoadObserver.unobserve(entry.target);
        }
      }
    });
  }, lazyLoadConfig);

  document.addEventListener('DOMContentLoaded', function() {
    const newsTypeSelect = document.getElementById('newsTypeSelect');
    if (newsTypeSelect) newsTypeSelect.value = newsType;
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
        
        const totalPublishers = result.meta.total_publishers || 0;
        const totalArticles   = result.meta.total_articles  || 0;
        const topPublisher    = allPublishers.length > 0 ? allPublishers[0].domain : 'N/A';
        
        document.getElementById('totalPublishersValue').innerHTML = `<div class="stat-value">${formatNumber(totalPublishers)}</div>`;
        document.getElementById('totalPublishersValue').classList.add('data-loaded');

        document.getElementById('totalArticlesValue').innerHTML = `<div class="stat-value">${formatNumber(totalArticles)}</div>`;
        document.getElementById('totalArticlesValue').classList.add('data-loaded');

        document.getElementById('topPublisherValue').innerHTML = `<div class="stat-value" style="font-size: 20px; word-break: break-all;">${topPublisher}</div>`;
        document.getElementById('topPublisherValue').classList.add('data-loaded');
        
        const statCards = document.querySelectorAll('.stat-card[data-lazy-load="publishers"]');
        animateProgress(statCards[0], 85);
        animateProgress(statCards[1], 92);
        animateProgress(statCards[2], 100);
        
        document.getElementById('publishersLoading').style.display = 'none';
        document.getElementById('publishersTable').style.display   = 'block';

        renderPublishersTable();
        updatePagination();
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

  function renderPublishersTable() {
    const container = document.getElementById('publishersTable');
    
    if (!allPublishers || allPublishers.length === 0) {
      container.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No publisher data available</p>';
      return;
    }
    
    const startIdx     = (currentPage - 1) * publishersPerPage;
    const currentData  = allPublishers.slice(startIdx, startIdx + publishersPerPage);
    const totalArticles = allPublishers.reduce((sum, p) => sum + p.count, 0);
    
    let html = '<table class="data-table"><thead><tr>';
    html += '<th>RANK</th><th>PUBLISHER</th><th>ARTICLES</th><th>PERCENTAGE</th></tr></thead><tbody>';
    
    currentData.forEach((item, index) => {
      const rank       = startIdx + index + 1;
      const domain     = item.domain || 'Unknown';
      const count      = item.count  || 0;
      const percentage = totalArticles > 0 ? ((count / totalArticles) * 100).toFixed(2) : 0;
      
      let rankClass = 'rank-other';
      if (rank === 1)      rankClass = 'rank-1';
      else if (rank === 2) rankClass = 'rank-2';
      else if (rank === 3) rankClass = 'rank-3';
      
      html += `<tr>
        <td><div class="rank-badge ${rankClass}">${rank}</div></td>
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

  // ─── Helpers ─────────────────────────────────────────

  function getPageRange(cur, total) {
    if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
    if (cur <= 4)          return [1, 2, 3, 4, 5, '...', total];
    if (cur >= total - 3)  return [1, '...', total-4, total-3, total-2, total-1, total];
    return [1, '...', cur-1, cur, cur+1, '...', total];
  }

  function updatePagination() {
    const totalPages = Math.ceil(allPublishers.length / publishersPerPage);
    const wrapper    = document.getElementById('pagination');
    const from       = allPublishers.length ? (currentPage - 1) * publishersPerPage + 1 : 0;
    const to         = Math.min(currentPage * publishersPerPage, allPublishers.length);

    if (allPublishers.length === 0) {
      wrapper.style.display = 'none';
      return;
    }

    let html = `<div class="pagination-info">Showing ${formatNumber(from)}–${formatNumber(to)} of ${formatNumber(allPublishers.length)} publishers</div>`;
    html += `<div style="display:flex;align-items:center;gap:6px;">`;

    html += `<button class="page-btn" onclick="goPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg>
    </button>`;

    getPageRange(currentPage, totalPages).forEach(p => {
      html += p === '...'
        ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
        : `<button class="page-btn ${p === currentPage ? 'active' : ''}" onclick="goPage(${p})">${p}</button>`;
    });

    html += `<button class="page-btn" onclick="goPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="9 18 15 12 9 6"/></svg>
    </button>`;

    html += `</div>`;
    wrapper.innerHTML     = html;
    wrapper.style.display = 'flex';
  }

  function goPage(p) {
    const totalPages = Math.ceil(allPublishers.length / publishersPerPage);
    if (p < 1 || p > totalPages) return;
    currentPage = p;
    renderPublishersTable();
    updatePagination();
    document.querySelector('.table-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function filterPublishers() {
    const searchTerm = document.getElementById('publisherSearchInput').value.toLowerCase();
    
    if (!searchTerm) {
      currentPage = 1;
      renderPublishersTable();
      updatePagination();
      return;
    }
    
    const filtered      = allPublishers.filter(pub => (pub.domain || '').toLowerCase().includes(searchTerm));
    const container     = document.getElementById('publishersTable');
    const totalArticles = allPublishers.reduce((sum, p) => sum + p.count, 0);
    
    let html = '<table class="data-table"><thead><tr>';
    html += '<th>RANK</th><th>PUBLISHER</th><th>ARTICLES</th><th>PERCENTAGE</th></tr></thead><tbody>';
    
    filtered.forEach((item, index) => {
      const rank       = index + 1;
      const domain     = item.domain || 'Unknown';
      const count      = item.count  || 0;
      const percentage = totalArticles > 0 ? ((count / totalArticles) * 100).toFixed(2) : 0;
      
      let rankClass = 'rank-other';
      if (rank === 1)      rankClass = 'rank-1';
      else if (rank === 2) rankClass = 'rank-2';
      else if (rank === 3) rankClass = 'rank-3';
      
      html += `<tr>
        <td><div class="rank-badge ${rankClass}">${rank}</div></td>
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
    
    // Hide pagination during search
    document.getElementById('pagination').style.display = 'none';
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
      setTimeout(() => { progressBar.style.width = percentage + '%'; }, 100);
    }
  }

  window.goPage            = goPage;
  window.filterPublishers  = filterPublishers;
}
</script>
@endsection