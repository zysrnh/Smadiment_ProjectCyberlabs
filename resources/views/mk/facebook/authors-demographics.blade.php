@extends('mk.layouts.app')

@section('title', 'Author Profiles (Facebook) - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
    --text-primary: #1a202c;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
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
    from {
      opacity: 0;
      transform: translateY(20px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
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

  .date-preset:hover {
    background: var(--bg-white);
    color: var(--primary-green);
  }

  .date-preset.active {
    background: var(--primary-green);
    color: white;
  }

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

  .nav-btn svg {
    width: 20px;
    height: 20px;
  }

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

  .calendar-day:hover:not(.disabled):not(.other-month) {
    background: var(--bg-gray-100);
  }

  .calendar-day.other-month {
    color: #cbd5e1;
    cursor: default;
  }

  .calendar-day.disabled {
    color: #e2e8f0;
    cursor: not-allowed;
  }

  .calendar-day.today {
    border: 2px solid var(--primary-green);
  }

  .calendar-day.selected {
    background: var(--primary-green);
    color: white;
  }

  .calendar-day.in-range {
    background: rgba(3, 128, 71, 0.1);
    color: var(--primary-green);
  }

  .calendar-day.range-start,
  .calendar-day.range-end {
    background: var(--primary-green);
    color: white;
  }

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

  .cancel-btn {
    background: var(--bg-gray-100);
    color: var(--text-primary);
  }

  .cancel-btn:hover {
    background: var(--border-gray);
  }

  .apply-date-btn {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
  }

  .apply-date-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
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

  /* Icon Wrapper */
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

  .stat-icon-wrapper::after {
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

  .metric-card:hover .stat-icon-wrapper::after {
    opacity: 0.5;
  }

  .stat-icon {
    width: 28px;
    height: 28px;
    color: var(--primary-green);
  }

  .metric-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
  }

  .stat-value-wrapper {
    margin-bottom: 8px;
  }

  .metric-value,
  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 8px;
  }

  .metric-label-sub {
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 12px;
  }

  /* Stat Progress Bar */
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

  /* Loading States */
  .loading-skeleton {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s ease-in-out infinite;
    border-radius: 8px;
  }

  @keyframes loading {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .skeleton-text {
    height: 36px;
    margin-bottom: 8px;
  }

  /* Lazy Loading Badge */
  .lazy-loading-badge {
    position: fixed;
    top: 80px;
    right: 32px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: rgba(3, 128, 71, 0.1);
    color: var(--primary-green);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 200;
    transition: opacity 0.3s;
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

  /* Mobile Responsive */
  @media (max-width: 1024px) {
    .dashboard-container { padding: 16px; }
    .metrics-grid { grid-template-columns: 1fr; gap: 16px; }
    .filter-content { flex-direction: column; align-items: stretch; }
    .date-range-wrapper { flex-direction: column; }
    .apply-btn { width: 100%; justify-content: center; }
  }

  @media (max-width: 768px) {
    .date-picker-container {
      flex-direction: column;
      max-height: 85vh;
      overflow-y: auto;
      width: 95%;
    }
    .date-picker-sidebar {
      width: 100%;
      border-right: none;
      border-bottom: 1px solid var(--border-gray);
      border-radius: 16px 16px 0 0;
      flex-direction: row;
      overflow-x: auto;
      padding: 12px 16px;
    }
    .date-preset { white-space: nowrap; }
    .date-picker-content { padding: 20px 16px; }
    .calendars-wrapper { flex-direction: column; gap: 16px; }
    .date-picker-header { flex-wrap: wrap; }
    .date-picker-trigger { max-width: 100%; }
    .calendar-day { font-size: 12px; }
    .weekday { font-size: 10px; }
    .cancel-btn, .apply-date-btn { flex: 1; }
  }

  @media (max-width: 640px) {
    .metric-value, .stat-value { font-size: 24px; }
    .chart-container { height: 220px; }
    .page-header h1 { font-size: 24px; }
    .section-title { font-size: 18px; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>Author Profiles (Facebook)</h1>
    <p>Comprehensive analysis of Facebook author characteristics and engagement patterns</p>
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
    <form id="filterForm" method="GET" action="{{ route('mk.facebook.authors.demographics') }}">
      <input type="hidden" name="project_id"  value="{{ $projectId }}">
      <input type="hidden" name="start_date"  id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date"    id="hiddenEndDate"   value="{{ $endDate }}">

      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8"  y1="2" x2="8"  y2="6"/>
            <line x1="3"  y1="10" x2="21" y2="10"/>
          </svg>
          Date Range
        </div>

        <div class="date-range-wrapper">
          <button type="button" class="date-picker-trigger" id="datePickerTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8"  y1="2" x2="8"  y2="6"/>
              <line x1="3"  y1="10" x2="21" y2="10"/>
            </svg>
            <span id="dateRangeDisplay">{{ $startDate }} to {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
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

  {{-- ═══════════════════════════════════════════════════════════
       AGE DEMOGRAPHICS SECTION
  ═══════════════════════════════════════════════════════════ --}}
  <div class="section-divider">
    <h2 class="section-title">Age Demographics</h2>
    <p class="section-subtitle">Facebook author distribution and engagement by age groups</p>
  </div>

  <div class="metrics-grid" data-lazy-load="age">
    <!-- Total Authors by Age -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
      </div>
      <div class="metric-label">Total Authors</div>
      <div id="ageAuthorsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:120px;"></div>
      </div>
      <div class="metric-label-sub">Across all age groups</div>
      <div class="stat-progress">
        <div class="stat-progress-bar" id="ageAuthorsProgress" style="width:0%"></div>
      </div>
    </div>

    <!-- Total Posts by Age -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6"  y1="20" x2="6"  y2="14"/>
          </svg>
        </div>
      </div>
      <div class="metric-label">Total Posts</div>
      <div id="agePostsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:120px;"></div>
      </div>
      <div class="metric-label-sub">Total engagement</div>
      <div class="stat-progress">
        <div class="stat-progress-bar" id="agePostsProgress" style="width:0%"></div>
      </div>
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
        <div id="ageDistributionLoading" class="loading-skeleton" style="height:100%;"></div>
        <canvas id="ageDistributionChart" style="display:none;"></canvas>
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
        <div id="ageEngagementLoading" class="loading-skeleton" style="height:100%;"></div>
        <canvas id="ageEngagementChart" style="display:none;"></canvas>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════════
       GENDER DEMOGRAPHICS SECTION
  ═══════════════════════════════════════════════════════════ --}}
  <div class="section-divider">
    <h2 class="section-title">Gender Demographics</h2>
    <p class="section-subtitle">Facebook author distribution and engagement by gender</p>
  </div>

  <div class="metrics-grid" data-lazy-load="gender">
    <!-- Male Authors -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
            <circle cx="12" cy="8" r="5"/>
            <path d="M20 21a8 8 0 1 0-16 0"/>
            <path d="M12 13v-2"/>
          </svg>
        </div>
      </div>
      <div class="metric-label">Male Authors</div>
      <div id="maleAuthorsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:120px;"></div>
      </div>
      <div class="metric-label-sub">Total male contributors</div>
      <div class="stat-progress">
        <div class="stat-progress-bar" id="maleProgress" style="width:0%;background:linear-gradient(90deg,#3b82f6,#2563eb);"></div>
      </div>
    </div>

    <!-- Female Authors -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2">
            <circle cx="12" cy="8" r="5"/>
            <path d="M20 21a8 8 0 1 0-16 0"/>
            <path d="M12 13v8"/>
            <path d="M8 17h8"/>
          </svg>
        </div>
      </div>
      <div class="metric-label">Female Authors</div>
      <div id="femaleAuthorsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:120px;"></div>
      </div>
      <div class="metric-label-sub">Total female contributors</div>
      <div class="stat-progress">
        <div class="stat-progress-bar" id="femaleProgress" style="width:0%;background:linear-gradient(90deg,#ec4899,#db2777);"></div>
      </div>
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
        <div id="genderDistributionLoading" class="loading-skeleton" style="height:100%;"></div>
        <canvas id="genderDistributionChart" style="display:none;"></canvas>
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
        <div id="genderEngagementLoading" class="loading-skeleton" style="height:100%;"></div>
        <canvas id="genderEngagementChart" style="display:none;"></canvas>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════════
       AUTHOR TYPE SECTION
  ═══════════════════════════════════════════════════════════ --}}
  <div class="section-divider">
    <h2 class="section-title">Author Type Analysis</h2>
    <p class="section-subtitle">Organization vs Individual Facebook author comparison</p>
  </div>

  <div class="metrics-grid" data-lazy-load="type">
    <!-- Individual Authors -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
      </div>
      <div class="metric-label">Individual Authors</div>
      <div id="individualAuthorsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:120px;"></div>
      </div>
      <div class="metric-label-sub">Personal accounts</div>
      <div class="stat-progress">
        <div class="stat-progress-bar" id="individualProgress" style="width:0%"></div>
      </div>
    </div>

    <!-- Organization Authors -->
    <div class="metric-card">
      <div class="metric-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
      </div>
      <div class="metric-label">Organizations</div>
      <div id="organizationAuthorsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width:120px;"></div>
      </div>
      <div class="metric-label-sub">Organizational accounts</div>
      <div class="stat-progress">
        <div class="stat-progress-bar" id="organizationProgress" style="width:0%"></div>
      </div>
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
        <div id="typeDistributionLoading" class="loading-skeleton" style="height:100%;"></div>
        <canvas id="typeDistributionChart" style="display:none;"></canvas>
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
        <div id="typeEngagementLoading" class="loading-skeleton" style="height:100%;"></div>
        <canvas id="typeEngagementChart" style="display:none;"></canvas>
      </div>
    </div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ═══════════════════════════════════════════════════════════
// DATE PICKER
// ═══════════════════════════════════════════════════════════
(function () {
  'use strict';

  let selectedStartDate = null;
  let selectedEndDate   = null;
  let currentMonth1     = new Date();
  let currentMonth2     = new Date();
  let selectingStart    = true;

  document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('hiddenStartDate');
    const endInput   = document.getElementById('hiddenEndDate');

    selectedStartDate = startInput && startInput.value ? new Date(startInput.value) : (() => {
      const d = new Date(); d.setDate(d.getDate() - 6); return d;
    })();
    selectedEndDate = endInput && endInput.value ? new Date(endInput.value) : new Date();

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

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('datePickerModal');
        if (modal && modal.classList.contains('show')) closeDatePicker();
      }
    });

    document.querySelectorAll('.date-preset').forEach(btn => btn.addEventListener('click', handlePresetClick));

    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    if (prevBtn) prevBtn.addEventListener('click', function () {
      currentMonth1.setMonth(currentMonth1.getMonth() - 1);
      currentMonth2.setMonth(currentMonth2.getMonth() - 1);
      renderCalendars();
    });
    if (nextBtn) nextBtn.addEventListener('click', function () {
      currentMonth1.setMonth(currentMonth1.getMonth() + 1);
      currentMonth2.setMonth(currentMonth2.getMonth() + 1);
      renderCalendars();
    });

    const applyBtn  = document.getElementById('applyDatePicker');
    const cancelBtn = document.querySelector('.cancel-btn');
    if (applyBtn)  applyBtn.addEventListener('click', applyDateSelection);
    if (cancelBtn) cancelBtn.addEventListener('click', closeDatePicker);
  }

  function openDatePicker()  { document.getElementById('datePickerModal').classList.add('show'); renderCalendars(); }
  function closeDatePicker() { document.getElementById('datePickerModal').classList.remove('show'); }

  function handlePresetClick(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    const today   = new Date(); today.setHours(0, 0, 0, 0);
    const preset  = e.target.dataset.preset;

    switch (preset) {
      case 'today':
        selectedStartDate = new Date(today);
        selectedEndDate   = new Date(today);
        break;
      case 'yesterday':
        selectedStartDate = new Date(today); selectedStartDate.setDate(today.getDate() - 1);
        selectedEndDate   = new Date(selectedStartDate);
        break;
      case 'last7days':
        selectedEndDate   = new Date(today);
        selectedStartDate = new Date(today); selectedStartDate.setDate(today.getDate() - 6);
        break;
      case 'last30days':
        selectedEndDate   = new Date(today);
        selectedStartDate = new Date(today); selectedStartDate.setDate(today.getDate() - 29);
        break;
      case 'thismonth':
        selectedStartDate = new Date(today.getFullYear(), today.getMonth(), 1);
        selectedEndDate   = new Date(today);
        break;
      case 'lastmonth':
        selectedStartDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        selectedEndDate   = new Date(today.getFullYear(), today.getMonth(), 0);
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
    const end   = formatDate(selectedEndDate);
    document.getElementById('hiddenStartDate').value = start;
    document.getElementById('hiddenEndDate').value   = end;
    const el = document.getElementById('dateRangeDisplay');
    if (el) el.textContent = `${start} to ${end}`;
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

    const year     = month.getFullYear();
    const monthNum = month.getMonth();
    const firstDay = new Date(year, monthNum, 1);
    const lastDay  = new Date(year, monthNum + 1, 0);
    const prevLast = new Date(year, monthNum, 0);
    const today    = new Date(); today.setHours(0, 0, 0, 0);

    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const weekdays   = ['Su','Mo','Tu','We','Th','Fr','Sa'];

    let html = `
      <div class="calendar-month">${monthNames[monthNum]} ${year}</div>
      <div class="calendar-weekdays">${weekdays.map(d => `<div class="weekday">${d}</div>`).join('')}</div>
      <div class="calendar-days">`;

    for (let i = 0; i < firstDay.getDay(); i++) {
      html += `<button type="button" class="calendar-day other-month" disabled>${prevLast.getDate() - (firstDay.getDay() - 1 - i)}</button>`;
    }

    for (let day = 1; day <= lastDay.getDate(); day++) {
      const date    = new Date(year, monthNum, day); date.setHours(0, 0, 0, 0);
      const dateStr = formatDate(date);
      let   cls     = 'calendar-day';

      if (isSameDay(date, today))   cls += ' today';
      if (date > today)              cls += ' disabled';

      if (selectedStartDate && selectedEndDate) {
        if (isSameDay(date, selectedStartDate))      cls += ' selected range-start';
        else if (isSameDay(date, selectedEndDate))   cls += ' selected range-end';
        else if (date > selectedStartDate && date < selectedEndDate) cls += ' in-range';
      }

      html += `<button type="button" class="${cls}" data-date="${dateStr}" ${date > today ? 'disabled' : ''}>${day}</button>`;
    }

    const remaining = lastDay.getDay() === 6 ? 0 : 6 - lastDay.getDay();
    for (let i = 1; i <= remaining; i++) {
      html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    }

    html += '</div>';
    calendar.innerHTML = html;

    calendar.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => {
      btn.addEventListener('click', handleDateClick);
    });
  }

  function handleDateClick(e) {
    const date = new Date(e.target.dataset.date); date.setHours(0, 0, 0, 0);
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    const custom = document.querySelector('[data-preset="custom"]');
    if (custom) custom.classList.add('active');

    if (selectingStart || date < selectedStartDate) {
      selectedStartDate = date;
      selectedEndDate   = date;
      selectingStart    = false;
    } else {
      if (date >= selectedStartDate) {
        selectedEndDate = date;
      } else {
        selectedEndDate   = selectedStartDate;
        selectedStartDate = date;
      }
      selectingStart = true;
    }
    updateDateDisplay();
    renderCalendars();
  }

  function updateDateDisplay() {
    if (!selectedStartDate || !selectedEndDate) return;
    const el = document.getElementById('selectedRangeText');
    if (el) el.textContent = `${formatDate(selectedStartDate)} to ${formatDate(selectedEndDate)}`;
  }

  function formatDate(date) {
    if (!date) return '';
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
  }

  function isSameDay(a, b) {
    return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  }
})();

// ═══════════════════════════════════════════════════════════
// FACEBOOK AUTHORS DEMOGRAPHICS — DATA LOADER
// ═══════════════════════════════════════════════════════════
const projectId = '{{ $projectId ?? '' }}';
const startDate = '{{ $startDate ?? '' }}';
const endDate   = '{{ $endDate ?? '' }}';

if (projectId && startDate && endDate) {

  function formatNumber(n) { return new Intl.NumberFormat('en-US').format(n); }

  // ── Lazy Load Observer ──────────────────────────────────────
  const loadedComponents = new Set();
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const id = entry.target.dataset.lazyLoad;
      if (loadedComponents.has(id)) return;
      loadedComponents.add(id);
      if      (id === 'age')    loadAgeData();
      else if (id === 'gender') loadGenderData();
      else if (id === 'type')   loadTypeData();
      observer.unobserve(entry.target);
    });
  }, { rootMargin: '100px', threshold: 0.01 });

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-lazy-load]').forEach(el => observer.observe(el));
  });

  // ── Helpers ─────────────────────────────────────────────────
  function addLoadingBadge(section) {
    if (!section || section.querySelector('.lazy-loading-badge')) return;
    const badge = document.createElement('div');
    badge.className = 'lazy-loading-badge';
    badge.innerHTML = '<div class="spinner"></div><span>Loading...</span>';
    section.appendChild(badge);
  }

  function removeLoadingBadge(section) {
    if (!section) return;
    const badge = section.querySelector('.lazy-loading-badge');
    if (badge) { badge.style.opacity = '0'; setTimeout(() => badge.remove(), 300); }
  }

  const chartDefaults = {
    tooltip: {
      backgroundColor: '#1a202c',
      padding: 12,
      titleFont: { size: 13, weight: '600' },
      bodyFont: { size: 12 },
      displayColors: false,
      cornerRadius: 8,
    },
    scale: {
      y: {
        beginAtZero: true,
        grid: { color: '#f1f5f9', drawBorder: false },
        ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } },
      },
      x: {
        grid: { display: false },
        ticks: { color: '#64748b', font: { family: 'Poppins', size: 11 } },
      },
    },
    legend: {
      labels: {
        color: '#1a202c',
        font: { family: 'Poppins', size: 11, weight: '600' },
        padding: 15,
        usePointStyle: true,
      },
    },
  };

  function showChart(canvasId, loadingId) {
    document.getElementById(loadingId).style.display = 'none';
    document.getElementById(canvasId).style.display  = 'block';
  }

  function showNoData(loadingId) {
    document.getElementById(loadingId).innerHTML =
      '<p style="text-align:center;color:var(--text-secondary);padding:40px;">No data available</p>';
  }

  // ═══════════════════════════════════════════════════════════
  // AGE
  // ═══════════════════════════════════════════════════════════
  async function loadAgeData() {
    const section = document.querySelector('[data-lazy-load="age"]');
    addLoadingBadge(section);
    try {
      const res  = await fetch(`/mk/api/facebook/authors-age?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const data = await res.json();

      if (Array.isArray(data) && data.length > 0) {
        const totalAuthors = data.reduce((s, d) => s + (parseInt(d.author_freq) || 0), 0);
        const totalPosts   = data.reduce((s, d) => s + (parseInt(d.post_freq)   || 0), 0);

        document.getElementById('ageAuthorsValue').innerHTML = `<div class="stat-value">${formatNumber(totalAuthors)}</div>`;
        document.getElementById('agePostsValue').innerHTML   = `<div class="stat-value">${formatNumber(totalPosts)}</div>`;

        setTimeout(() => {
          document.getElementById('ageAuthorsProgress').style.width = '75%';
          document.getElementById('agePostsProgress').style.width   = '85%';
        }, 100);

        renderAgeDistributionChart(data);
        renderAgeEngagementChart(data);
      } else {
        document.getElementById('ageAuthorsValue').innerHTML = `<div class="stat-value">0</div>`;
        document.getElementById('agePostsValue').innerHTML   = `<div class="stat-value">0</div>`;
        showNoData('ageDistributionLoading');
        showNoData('ageEngagementLoading');
      }
    } catch (err) {
      console.error('FB Age error:', err);
      document.getElementById('ageAuthorsValue').innerHTML = `<div class="stat-value" style="color:#ef4444;">Error</div>`;
      document.getElementById('agePostsValue').innerHTML   = `<div class="stat-value" style="color:#ef4444;">Error</div>`;
    } finally {
      removeLoadingBadge(section);
      section.classList.add('loaded');
    }
  }

  function renderAgeDistributionChart(data) {
    if (!data || !data.length) { showNoData('ageDistributionLoading'); return; }
    const ctx = document.getElementById('ageDistributionChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.map(d => d.age_group),
        datasets: [{
          label: 'Authors',
          data: data.map(d => parseInt(d.author_freq) || 0),
          backgroundColor: 'rgba(3,128,71,0.8)',
          borderColor: '#038047',
          borderWidth: 2,
          borderRadius: 8,
        }],
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: chartDefaults.tooltip },
        scales: { y: chartDefaults.scale.y, x: chartDefaults.scale.x },
      },
    });
    showChart('ageDistributionChart', 'ageDistributionLoading');
  }

  function renderAgeEngagementChart(data) {
    if (!data || !data.length) { showNoData('ageEngagementLoading'); return; }
    const ctx = document.getElementById('ageEngagementChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: data.map(d => d.age_group),
        datasets: [{
          data: data.map(d => parseInt(d.post_freq) || 0),
          backgroundColor: ['#038047','#04995a','#10b981','#34d399','#6ee7b7','#a7f3d0'],
          borderWidth: 0, hoverOffset: 10,
        }],
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: {
          legend: { position: 'bottom', labels: chartDefaults.legend.labels },
          tooltip: {
            ...chartDefaults.tooltip,
            callbacks: {
              label: (ctx) => {
                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                return `Posts: ${formatNumber(ctx.parsed)} (${((ctx.parsed / total) * 100).toFixed(1)}%)`;
              },
            },
          },
        },
      },
    });
    showChart('ageEngagementChart', 'ageEngagementLoading');
  }

  // ═══════════════════════════════════════════════════════════
  // GENDER
  // ═══════════════════════════════════════════════════════════
  async function loadGenderData() {
    const section = document.querySelector('[data-lazy-load="gender"]');
    addLoadingBadge(section);
    try {
      const res  = await fetch(`/mk/api/facebook/authors-gender?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const data = await res.json();

      if (Array.isArray(data) && data.length > 0) {
        const maleData   = data.find(d => (d.gender || d.name || '').toLowerCase() === 'male');
        const femaleData = data.find(d => (d.gender || d.name || '').toLowerCase() === 'female');

        const maleAuthors   = maleData   ? parseInt(maleData.author_freq)   || 0 : 0;
        const femaleAuthors = femaleData ? parseInt(femaleData.author_freq) || 0 : 0;
        const total         = maleAuthors + femaleAuthors;

        document.getElementById('maleAuthorsValue').innerHTML   = `<div class="stat-value">${formatNumber(maleAuthors)}</div>`;
        document.getElementById('femaleAuthorsValue').innerHTML = `<div class="stat-value">${formatNumber(femaleAuthors)}</div>`;

        setTimeout(() => {
          document.getElementById('maleProgress').style.width   = (total > 0 ? (maleAuthors   / total) * 100 : 0) + '%';
          document.getElementById('femaleProgress').style.width = (total > 0 ? (femaleAuthors / total) * 100 : 0) + '%';
        }, 100);

        renderGenderDistributionChart(data);
        renderGenderEngagementChart(data);
      } else {
        document.getElementById('maleAuthorsValue').innerHTML   = `<div class="stat-value">0</div>`;
        document.getElementById('femaleAuthorsValue').innerHTML = `<div class="stat-value">0</div>`;
        showNoData('genderDistributionLoading');
        showNoData('genderEngagementLoading');
      }
    } catch (err) {
      console.error('FB Gender error:', err);
      document.getElementById('maleAuthorsValue').innerHTML   = `<div class="stat-value" style="color:#ef4444;">Error</div>`;
      document.getElementById('femaleAuthorsValue').innerHTML = `<div class="stat-value" style="color:#ef4444;">Error</div>`;
    } finally {
      removeLoadingBadge(section);
      section.classList.add('loaded');
    }
  }

  function renderGenderDistributionChart(data) {
    if (!data || !data.length) { showNoData('genderDistributionLoading'); return; }
    const ctx = document.getElementById('genderDistributionChart').getContext('2d');
    const labels = data.map(d => { const g = d.gender || d.name || ''; return g.charAt(0).toUpperCase() + g.slice(1); });
    const colors = labels.map(l => l.toLowerCase() === 'male' ? '#3b82f6' : l.toLowerCase() === 'female' ? '#ec4899' : '#64748b');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{ data: data.map(d => parseInt(d.author_freq) || 0), backgroundColor: colors, borderWidth: 0, hoverOffset: 10 }],
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: {
          legend: { position: 'bottom', labels: chartDefaults.legend.labels },
          tooltip: {
            ...chartDefaults.tooltip,
            callbacks: {
              label: (ctx) => {
                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                return `Authors: ${formatNumber(ctx.parsed)} (${((ctx.parsed / total) * 100).toFixed(1)}%)`;
              },
            },
          },
        },
      },
    });
    showChart('genderDistributionChart', 'genderDistributionLoading');
  }

  function renderGenderEngagementChart(data) {
    if (!data || !data.length) { showNoData('genderEngagementLoading'); return; }
    const ctx    = document.getElementById('genderEngagementChart').getContext('2d');
    const labels = data.map(d => { const g = d.gender || d.name || ''; return g.charAt(0).toUpperCase() + g.slice(1); });
    const bgColors = labels.map(l => l.toLowerCase() === 'male' ? 'rgba(59,130,246,0.8)' : l.toLowerCase() === 'female' ? 'rgba(236,72,153,0.8)' : 'rgba(100,116,139,0.8)');
    const bdColors = labels.map(l => l.toLowerCase() === 'male' ? '#3b82f6' : l.toLowerCase() === 'female' ? '#ec4899' : '#64748b');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{ label: 'Posts', data: data.map(d => parseInt(d.post_freq) || 0), backgroundColor: bgColors, borderColor: bdColors, borderWidth: 2, borderRadius: 8 }],
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: chartDefaults.tooltip },
        scales: { y: chartDefaults.scale.y, x: chartDefaults.scale.x },
      },
    });
    showChart('genderEngagementChart', 'genderEngagementLoading');
  }

  // ═══════════════════════════════════════════════════════════
  // AUTHOR TYPE
  // ═══════════════════════════════════════════════════════════
  async function loadTypeData() {
    const section = document.querySelector('[data-lazy-load="type"]');
    addLoadingBadge(section);
    try {
      const res  = await fetch(`/mk/api/facebook/authors-type?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const data = await res.json();

      if (Array.isArray(data) && data.length > 0) {
        const nonOrgData = data.find(d => { const v = (d.is_organization || d.name || '').toString().toLowerCase(); return v === 'non-org' || v === '0' || v === 'false'; });
        const orgData    = data.find(d => { const v = (d.is_organization || d.name || '').toString().toLowerCase(); return v === 'is-org' || v === 'org' || v === '1' || v === 'true'; });

        const individualAuthors    = nonOrgData ? parseInt(nonOrgData.author_freq) || 0 : 0;
        const organizationAuthors  = orgData    ? parseInt(orgData.author_freq)    || 0 : 0;
        const total                = individualAuthors + organizationAuthors;

        document.getElementById('individualAuthorsValue').innerHTML   = `<div class="stat-value">${formatNumber(individualAuthors)}</div>`;
        document.getElementById('organizationAuthorsValue').innerHTML = `<div class="stat-value">${formatNumber(organizationAuthors)}</div>`;

        setTimeout(() => {
          document.getElementById('individualProgress').style.width   = (total > 0 ? (individualAuthors   / total) * 100 : 0) + '%';
          document.getElementById('organizationProgress').style.width = (total > 0 ? (organizationAuthors / total) * 100 : 0) + '%';
        }, 100);

        renderTypeDistributionChart(data);
        renderTypeEngagementChart(data);
      } else {
        document.getElementById('individualAuthorsValue').innerHTML   = `<div class="stat-value">0</div>`;
        document.getElementById('organizationAuthorsValue').innerHTML = `<div class="stat-value">0</div>`;
        showNoData('typeDistributionLoading');
        showNoData('typeEngagementLoading');
      }
    } catch (err) {
      console.error('FB Type error:', err);
      document.getElementById('individualAuthorsValue').innerHTML   = `<div class="stat-value" style="color:#ef4444;">Error</div>`;
      document.getElementById('organizationAuthorsValue').innerHTML = `<div class="stat-value" style="color:#ef4444;">Error</div>`;
    } finally {
      removeLoadingBadge(section);
      section.classList.add('loaded');
    }
  }

  function renderTypeDistributionChart(data) {
    if (!data || !data.length) { showNoData('typeDistributionLoading'); return; }
    const ctx       = document.getElementById('typeDistributionChart').getContext('2d');
    const chartData = [];
    data.forEach(d => {
      const v    = (d.is_organization || d.name || '').toString().toLowerCase();
      const freq = parseInt(d.author_freq) || 0;
      if      (v === 'non-org' || v === '0' || v === 'false') chartData.push({ label: 'Individual',    value: freq, color: '#3b82f6' });
      else if (v === 'is-org'  || v === 'org' || v === '1' || v === 'true')   chartData.push({ label: 'Organization', value: freq, color: '#038047' });
    });
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: chartData.map(d => d.label),
        datasets: [{ data: chartData.map(d => d.value), backgroundColor: chartData.map(d => d.color), borderWidth: 0, hoverOffset: 10 }],
      },
      options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: {
          legend: { position: 'bottom', labels: chartDefaults.legend.labels },
          tooltip: {
            ...chartDefaults.tooltip,
            callbacks: {
              label: (ctx) => {
                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                return `Authors: ${formatNumber(ctx.parsed)} (${((ctx.parsed / total) * 100).toFixed(1)}%)`;
              },
            },
          },
        },
      },
    });
    showChart('typeDistributionChart', 'typeDistributionLoading');
  }

  function renderTypeEngagementChart(data) {
    if (!data || !data.length) { showNoData('typeEngagementLoading'); return; }
    const ctx       = document.getElementById('typeEngagementChart').getContext('2d');
    const chartData = [];
    data.forEach(d => {
      const v    = (d.is_organization || d.name || '').toString().toLowerCase();
      const freq = parseInt(d.post_freq) || 0;
      if      (v === 'non-org' || v === '0' || v === 'false') chartData.push({ label: 'Individual',    value: freq, bg: 'rgba(59,130,246,0.8)',  bd: '#3b82f6' });
      else if (v === 'is-org'  || v === 'org' || v === '1' || v === 'true')   chartData.push({ label: 'Organization', value: freq, bg: 'rgba(3,128,71,0.8)',   bd: '#038047' });
    });
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: chartData.map(d => d.label),
        datasets: [{ label: 'Posts', data: chartData.map(d => d.value), backgroundColor: chartData.map(d => d.bg), borderColor: chartData.map(d => d.bd), borderWidth: 2, borderRadius: 8 }],
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: chartDefaults.tooltip },
        scales: { y: chartDefaults.scale.y, x: chartDefaults.scale.x },
      },
    });
    showChart('typeEngagementChart', 'typeEngagementLoading');
  }
}
</script>
@endsection