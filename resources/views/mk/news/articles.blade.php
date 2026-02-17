@extends('mk.layouts.app')

@section('title', 'News Articles - SMADIMENT')

@section('styles')
<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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

  /* Flatpickr Custom Theme */
  .flatpickr-calendar {
    border-radius: 16px !important;
    border: 1px solid var(--border-gray) !important;
    box-shadow: var(--shadow-lg) !important;
    font-family: 'Poppins', sans-serif !important;
    overflow: hidden;
  }

  .flatpickr-months {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%) !important;
    border-radius: 16px 16px 0 0 !important;
    padding: 8px 0 !important;
  }

  .flatpickr-month {
    color: white !important;
    fill: white !important;
  }

  .flatpickr-current-month {
    color: white !important;
    font-size: 15px !important;
    font-weight: 600 !important;
  }

  .flatpickr-current-month .numInputWrapper input {
    color: white !important;
  }

  .flatpickr-current-month .flatpickr-monthDropdown-months {
    color: white !important;
    background: transparent !important;
    font-weight: 600 !important;
  }

  .flatpickr-prev-month svg,
  .flatpickr-next-month svg {
    fill: white !important;
  }

  .flatpickr-prev-month:hover,
  .flatpickr-next-month:hover {
    background: rgba(255,255,255,0.2) !important;
    border-radius: 8px !important;
  }

  .flatpickr-weekday {
    color: var(--text-secondary) !important;
    font-weight: 600 !important;
    font-size: 12px !important;
  }

  .flatpickr-day {
    border-radius: 8px !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: var(--text-primary) !important;
  }

  .flatpickr-day:hover {
    background: rgba(3, 128, 71, 0.1) !important;
    border-color: transparent !important;
    color: var(--primary-green) !important;
  }

  .flatpickr-day.selected,
  .flatpickr-day.startRange,
  .flatpickr-day.endRange {
    background: var(--primary-green) !important;
    border-color: var(--primary-green) !important;
    color: white !important;
    font-weight: 700 !important;
  }

  .flatpickr-day.inRange {
    background: rgba(3, 128, 71, 0.12) !important;
    border-color: transparent !important;
    color: var(--primary-green) !important;
    box-shadow: none !important;
  }

  .flatpickr-day.today {
    border-color: var(--primary-green) !important;
    color: var(--primary-green) !important;
    font-weight: 700 !important;
  }

  .flatpickr-day.today:hover {
    background: var(--primary-green) !important;
    color: white !important;
  }

  .numInputWrapper:hover {
    background: rgba(255,255,255,0.1) !important;
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
    padding: 10px 16px;
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    background: var(--bg-gray-50);
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.2s;
    min-width: 150px;
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

  .date-picker-trigger.active {
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
    transition: transform 0.2s;
  }

  .date-picker-trigger.active svg:last-child {
    transform: rotate(180deg);
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

  /* Sentiment Card */
  .sentiment-card {
    grid-column: span 1;
  }

  .sentiment-chart-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
    margin-top: 12px;
  }

  .sentiment-donut {
    width: 180px;
    height: 180px;
    position: relative;
  }

  .sentiment-center-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
  }

  .sentiment-center-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 4px;
  }

  .sentiment-center-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .sentiment-legend {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .sentiment-legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
  }

  .sentiment-legend-color {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    flex-shrink: 0;
  }

  .sentiment-legend-label {
    color: var(--text-secondary);
    flex: 1;
  }

  .sentiment-legend-value {
    font-weight: 700;
    color: var(--text-primary);
  }

  /* Articles Container */
  .articles-container {
    background: var(--bg-white);
    border-radius: 16px;
    border: 1px solid var(--border-gray);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  .articles-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 28px;
    border-bottom: 2px solid var(--bg-gray-50);
    gap: 16px;
    flex-wrap: wrap;
  }

  .articles-title-group h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px 0;
  }

  .articles-subtitle {
    font-size: 13px;
    color: var(--text-secondary);
  }

  .filter-controls {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .search-box {
    position: relative;
    min-width: 240px;
  }

  .search-box input {
    width: 100%;
    padding: 10px 16px 10px 44px;
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    background: var(--bg-gray-50);
    transition: all 0.2s;
  }

  .search-box input:focus {
    outline: none;
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  .search-box svg {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
  }

  /* Article Card */
  .articles-list {
    padding: 20px;
  }

  .article-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 16px;
    transition: all 0.3s;
    cursor: pointer;
  }

  .article-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--primary-green);
    transform: translateY(-2px);
  }

  .article-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
    gap: 16px;
  }

  .article-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 12px 0;
    line-height: 1.4;
    flex: 1;
  }

  .article-title a {
    color: var(--text-primary);
    text-decoration: none;
    transition: color 0.2s;
  }

  .article-title a:hover {
    color: var(--primary-green);
  }

  .sentiment-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
  }

  .sentiment-badge.positive {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #6ee7b7;
  }

  .sentiment-badge.negative {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border: 1px solid #fca5a5;
  }

  .sentiment-badge.neutral {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    color: #374151;
    border: 1px solid #d1d5db;
  }

  .article-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 16px;
  }

  .article-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .article-meta-item svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    fill: none;
  }

  .publisher-name {
    font-weight: 600;
    color: var(--primary-green);
  }

  .article-content {
    font-size: 14px;
    line-height: 1.7;
    color: var(--text-primary);
    margin-bottom: 16px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .article-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 1px solid var(--bg-gray-100);
  }

  .quotes-count {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: var(--bg-gray-50);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
  }

  .quotes-count svg {
    width: 16px;
    height: 16px;
    color: var(--primary-green);
  }

  .view-article-btn {
    padding: 8px 16px;
    background: var(--primary-green);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
  }

  .view-article-btn:hover {
    background: var(--primary-green-dark);
    transform: translateX(2px);
  }

  .view-article-btn svg {
    width: 14px;
    height: 14px;
  }

  /* Quote Card */
  .quotes-section {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 2px solid var(--bg-gray-50);
  }

  .quotes-header {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .quotes-header svg {
    width: 18px;
    height: 18px;
    color: var(--primary-green);
  }

  .quote-card {
    background: var(--bg-gray-50);
    border-left: 4px solid var(--primary-green);
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
  }

  .quote-card:last-child {
    margin-bottom: 0;
  }

  .quote-speaker {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
  }

  .quote-position {
    font-size: 12px;
    color: var(--text-secondary);
    margin-bottom: 12px;
  }

  .quote-text {
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-primary);
    font-style: italic;
    position: relative;
    padding-left: 20px;
  }

  .quote-text::before {
    content: '"';
    position: absolute;
    left: 0;
    top: -5px;
    font-size: 32px;
    color: var(--primary-green);
    opacity: 0.3;
    font-family: Georgia, serif;
  }

  .quote-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 12px;
    font-size: 12px;
    color: var(--text-muted);
  }

  .quote-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .quote-sentiment {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: capitalize;
  }

  .quote-sentiment.positif {
    background: #d1fae5;
    color: #065f46;
  }

  .quote-sentiment.negatif {
    background: #fee2e2;
    color: #991b1b;
  }

  .quote-sentiment.netral {
    background: #f3f4f6;
    color: #374151;
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
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .skeleton-text {
    height: 44px;
    margin-bottom: 8px;
  }

  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 80px 20px;
  }

  .empty-state svg {
    width: 64px;
    height: 64px;
    color: var(--text-secondary);
    margin-bottom: 16px;
  }

  .empty-state h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 8px 0;
  }

  .empty-state p {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
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
    .dashboard-container { padding: 16px; }
    .stats-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
    .filter-content { flex-direction: column; align-items: stretch; }
    .date-range-wrapper { flex-direction: column; }
    .apply-btn { width: 100%; justify-content: center; }
    .date-picker-trigger { max-width: 100%; }
  }

  @media (max-width: 768px) {
    .stat-value { font-size: 28px; }
    .page-header h1 { font-size: 24px; }
    .article-card { padding: 16px; }
    .article-title { font-size: 16px; }
    .article-meta { flex-direction: column; align-items: flex-start; gap: 8px; }
    .sentiment-chart-container { flex-direction: column; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>News Articles</h1>
    <p>Browse and analyze news articles from online media sources</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view articles.</span>
  </div>
  @else

  <!-- Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.news.articles') }}">
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

        <div class="filter-label">Media Type</div>
        <select name="media" id="mediaType" class="filter-select">
          <option value="doc" {{ (request('media', 'doc') === 'doc') ? 'selected' : '' }}>Online News</option>
          <option value="cetak" {{ (request('media') === 'cetak') ? 'selected' : '' }}>Print Media</option>
        </select>

        <div class="filter-label">Sentiment</div>
        <select name="sentiment" id="sentimentFilter" class="filter-select">
          <option value="all" {{ (request('sentiment', 'all') === 'all') ? 'selected' : '' }}>All Sentiment</option>
          <option value="1" {{ (request('sentiment') === '1') ? 'selected' : '' }}>Positive</option>
          <option value="0" {{ (request('sentiment') === '0') ? 'selected' : '' }}>Neutral</option>
          <option value="-1" {{ (request('sentiment') === '-1') ? 'selected' : '' }}>Negative</option>
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

    <!-- Total Articles -->
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Total Articles</div>
      <div id="statTotalArticles" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Total Publishers -->
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Total Publishers</div>
      <div id="statTotalPublishers" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Sentiment Distribution -->
    <div class="stat-card sentiment-card">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
            <line x1="9" y1="9" x2="9.01" y2="9"/>
            <line x1="15" y1="9" x2="15.01" y2="9"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Sentiment Distribution</div>
      <div id="statSentiment" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 100%; height: 200px;"></div>
      </div>
    </div>

  </div>

  <!-- Articles Container -->
  <div class="articles-container">
    <div class="articles-header">
      <div class="articles-title-group">
        <h3>News Articles</h3>
        <p class="articles-subtitle">Latest articles from monitored online news sources</p>
      </div>
      <div class="filter-controls">
        <div class="search-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <input type="text" id="searchInput" placeholder="Search articles..." onkeyup="filterArticles()">
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div id="articlesLoading" class="loading-skeleton" style="height: 400px; margin: 20px;"></div>

    <!-- Articles List -->
    <div id="articlesList" class="articles-list" style="display: none;"></div>

    <!-- Empty State -->
    <div id="emptyState" style="display: none;">
      <div class="empty-state">
        <svg viewBox="0 0 24 24" style="stroke: currentColor; fill: none;">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <h3>No Articles Found</h3>
        <p>No articles available for the selected filters.</p>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination" id="pagination" style="display: none;">
      <button class="pagination-btn" id="prevBtn" onclick="changePage(-1)">← Previous</button>
      <span class="pagination-info" id="pageInfo">Page 1 of 1</span>
      <button class="pagination-btn" id="nextBtn" onclick="changePage(1)">Next →</button>
    </div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script>
// =============================================
// DATE PICKER — Flatpickr Integration
// =============================================
document.addEventListener('DOMContentLoaded', function () {

  const trigger   = document.getElementById('datePickerTrigger');
  const hiddenStart = document.getElementById('hiddenStartDate');
  const hiddenEnd   = document.getElementById('hiddenEndDate');
  const display   = document.getElementById('dateRangeDisplay');

  if (trigger && hiddenStart && hiddenEnd) {

    // Buat hidden input sebagai anchor Flatpickr (tidak terlihat user)
    const fpInput = document.createElement('input');
    fpInput.type = 'text';
    fpInput.style.cssText = 'position:absolute;opacity:0;pointer-events:none;width:0;height:0;';
    trigger.parentNode.appendChild(fpInput);

    const fp = flatpickr(fpInput, {
      mode: 'range',
      dateFormat: 'Y-m-d',
      defaultDate: [hiddenStart.value, hiddenEnd.value],
      maxDate: 'today',
      showMonths: window.innerWidth > 768 ? 2 : 1, // 2 bulan di desktop
      locale: {
        rangeSeparator: ' → '
      },
      onOpen: function () {
        trigger.classList.add('active');
      },
      onClose: function () {
        trigger.classList.remove('active');
      },
      onChange: function (selectedDates) {
        if (selectedDates.length === 2) {
          // Format tanggal ke YYYY-MM-DD
          const fmt = (d) => {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${dd}`;
          };

          const s = fmt(selectedDates[0]);
          const e = fmt(selectedDates[1]);

          // Update hidden inputs yang akan dikirim via form
          hiddenStart.value = s;
          hiddenEnd.value   = e;

          // Update teks display pada tombol
          display.textContent = `${s} to ${e}`;
        }
      }
    });

    // Klik tombol → buka/tutup kalender
    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      if (fp.isOpen) {
        fp.close();
      } else {
        // Posisikan kalender di bawah tombol
        fp.open();
      }
    });

    // Resize handler — update jumlah bulan yang ditampilkan
    window.addEventListener('resize', function () {
      fp.set('showMonths', window.innerWidth > 768 ? 2 : 1);
    });
  }

});

// =============================================
// ARTICLES — Logic utama
// =============================================
const projectId = '{{ $projectId ?? '' }}';
const startDate = '{{ $startDate ?? '' }}';
const endDate   = '{{ $endDate ?? '' }}';

let allArticles      = [];
let filteredArticles = [];
let currentPage      = 1;
const articlesPerPage = 10;
let sentimentChart   = null;

function formatNumber(num) {
  return new Intl.NumberFormat('en-US').format(num);
}

function formatDate(dateStr) {
  if (!dateStr) return { date: '—', time: '' };
  try {
    const date = new Date(dateStr);
    return {
      date: date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }),
      time: date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
    };
  } catch (e) {
    return { date: dateStr, time: '' };
  }
}

function getSentimentClass(sentiment) {
  const s = (sentiment || '').toLowerCase();
  if (s.includes('pos')) return 'positive';
  if (s.includes('neg')) return 'negative';
  return 'neutral';
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function cleanContent(content) {
  if (!content) return '';
  return content.replace(/<[^>]*>/g, '').trim();
}

if (projectId && startDate && endDate) {

  document.addEventListener('DOMContentLoaded', function () {
    loadArticles();
  });

  async function loadArticles() {
    try {
      const mediaType = document.getElementById('mediaType')?.value || 'doc';
      const sentiment = document.getElementById('sentimentFilter')?.value || 'all';

      let apiUrl = `/mk/api/news/articles?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}&media=${mediaType}`;
      if (sentiment !== 'all') apiUrl += `&sentiment=${sentiment}`;

      console.log('🔍 Fetching articles from:', apiUrl);

      const response = await fetch(apiUrl);
      const result   = await response.json();

      console.log('📊 API Response:', result);

      if (result.success && result.data && result.data.length > 0) {
        allArticles      = result.data;
        filteredArticles = [...allArticles];

        // Hitung sentiment distribution
        const sentimentCounts = { positive: 0, neutral: 0, negative: 0 };
        allArticles.forEach(article => {
          sentimentCounts[getSentimentClass(article.sentiment)]++;
        });

        // Hitung unique publishers
        const uniquePublishers = [...new Set(allArticles.map(a => a.publisher))].length;

        // Update stats cards
        document.getElementById('statTotalArticles').innerHTML   = `<div class="stat-value">${formatNumber(allArticles.length)}</div>`;
        document.getElementById('statTotalPublishers').innerHTML = `<div class="stat-value">${formatNumber(uniquePublishers)}</div>`;

        createSentimentChart(sentimentCounts);
        animateProgress(document.querySelectorAll('.stat-card')[0], 90);
        animateProgress(document.querySelectorAll('.stat-card')[1], 75);

        renderArticles();

        document.getElementById('articlesLoading').style.display = 'none';
        document.getElementById('articlesList').style.display    = 'block';
        document.getElementById('pagination').style.display      = 'flex';

      } else {
        console.warn('⚠️ No articles found');
        showEmptyState();
      }

    } catch (error) {
      console.error('❌ Error loading articles:', error);
      showEmptyState();
    }
  }

  function showEmptyState() {
    document.getElementById('statTotalArticles').innerHTML   = '<div class="stat-value">0</div>';
    document.getElementById('statTotalPublishers').innerHTML = '<div class="stat-value">0</div>';
    document.getElementById('statSentiment').innerHTML       = '<p style="text-align:center;color:var(--text-muted);padding:20px;">No data</p>';
    document.getElementById('articlesLoading').style.display = 'none';
    document.getElementById('emptyState').style.display      = 'block';
  }

  function createSentimentChart(sentimentCounts) {
    const container  = document.getElementById('statSentiment');
    const totalArticles = sentimentCounts.positive + sentimentCounts.neutral + sentimentCounts.negative;

    container.innerHTML = `
      <div class="sentiment-chart-container">
        <div class="sentiment-donut">
          <canvas id="sentimentChart"></canvas>
          <div class="sentiment-center-text">
            <div class="sentiment-center-value">${totalArticles}</div>
            <div class="sentiment-center-label">Articles</div>
          </div>
        </div>
        <div class="sentiment-legend">
          <div class="sentiment-legend-item">
            <div class="sentiment-legend-color" style="background:#10b981;"></div>
            <span class="sentiment-legend-label">Positive</span>
            <span class="sentiment-legend-value">${sentimentCounts.positive}</span>
          </div>
          <div class="sentiment-legend-item">
            <div class="sentiment-legend-color" style="background:#6b7280;"></div>
            <span class="sentiment-legend-label">Neutral</span>
            <span class="sentiment-legend-value">${sentimentCounts.neutral}</span>
          </div>
          <div class="sentiment-legend-item">
            <div class="sentiment-legend-color" style="background:#ef4444;"></div>
            <span class="sentiment-legend-label">Negative</span>
            <span class="sentiment-legend-value">${sentimentCounts.negative}</span>
          </div>
        </div>
      </div>
    `;

    const ctx = document.getElementById('sentimentChart').getContext('2d');
    if (sentimentChart) sentimentChart.destroy();

    sentimentChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Positive', 'Neutral', 'Negative'],
        datasets: [{
          data: [sentimentCounts.positive, sentimentCounts.neutral, sentimentCounts.negative],
          backgroundColor: ['#10b981', '#6b7280', '#ef4444'],
          borderWidth: 0,
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function (context) {
                const value = context.parsed || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const pct   = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                return `${context.label}: ${value} (${pct}%)`;
              }
            }
          }
        },
        cutout: '70%'
      }
    });
  }

  function animateProgress(card, percentage) {
    const bar = card.querySelector('.stat-progress-bar');
    if (bar) setTimeout(() => { bar.style.width = percentage + '%'; }, 100);
  }

  function renderArticles() {
    const startIdx   = (currentPage - 1) * articlesPerPage;
    const currentData = filteredArticles.slice(startIdx, startIdx + articlesPerPage);
    const container  = document.getElementById('articlesList');

    if (!currentData.length) {
      container.style.display = 'none';
      document.getElementById('emptyState').style.display   = 'block';
      document.getElementById('pagination').style.display   = 'none';
      return;
    }

    document.getElementById('emptyState').style.display   = 'none';
    document.getElementById('pagination').style.display   = 'flex';

    container.innerHTML = currentData.map((article) => {
      const dateInfo  = formatDate(article.date_created);
      const sentiment = getSentimentClass(article.sentiment);
      const content   = cleanContent(article.content);

      let quotes = [];
      if (Array.isArray(article.quotes)) {
        quotes = article.quotes.filter(q => q && q.Kutipan && q.Kutipan.trim() !== '');
      }

      const hasQuotes = quotes.length > 0;

      return `
        <div class="article-card">
          <div class="article-header">
            <h3 class="article-title">
              <a href="${article.url || '#'}" target="_blank" rel="noopener noreferrer">
                ${escapeHtml(article.title || 'Untitled')}
              </a>
            </h3>
            <span class="sentiment-badge ${sentiment}">${article.sentiment || 'Neutral'}</span>
          </div>

          <div class="article-meta">
            <div class="article-meta-item">
              <svg viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
              <span class="publisher-name">${escapeHtml(article.publisher || 'Unknown')}</span>
            </div>
            <div class="article-meta-item">
              <svg viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
              <span>${dateInfo.date}${dateInfo.time ? ' • ' + dateInfo.time : ''}</span>
            </div>
          </div>

          ${content ? `
            <div class="article-content">
              ${escapeHtml(content.substring(0, 300))}${content.length > 300 ? '...' : ''}
            </div>
          ` : ''}

          ${hasQuotes ? `
            <div class="quotes-section">
              <div class="quotes-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                Extracted Quotes (${quotes.length})
              </div>
              ${quotes.slice(0, 2).map(quote => `
                <div class="quote-card">
                  <div class="quote-speaker">${escapeHtml(quote.Tokoh || 'Unknown Speaker')}</div>
                  ${quote.Jabatan && quote.Jabatan.trim() ? `<div class="quote-position">${escapeHtml(quote.Jabatan)}</div>` : ''}
                  <div class="quote-text">${escapeHtml(quote.Kutipan || '')}</div>
                  <div class="quote-meta">
                    ${quote.Tempat && quote.Tempat.trim() ? `
                      <div class="quote-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
                          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                          <circle cx="12" cy="10" r="3"/>
                        </svg>
                        ${escapeHtml(quote.Tempat)}
                      </div>
                    ` : ''}
                    ${quote.Waktu && quote.Waktu.trim() ? `
                      <div class="quote-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
                          <circle cx="12" cy="12" r="10"/>
                          <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        ${escapeHtml(quote.Waktu)}
                      </div>
                    ` : ''}
                    ${quote.Sentimen && quote.Sentimen.trim() ? `
                      <span class="quote-sentiment ${quote.Sentimen.toLowerCase()}">${escapeHtml(quote.Sentimen)}</span>
                    ` : ''}
                  </div>
                </div>
              `).join('')}
              ${quotes.length > 2 ? `
                <div style="text-align:center;padding:8px;color:var(--text-secondary);font-size:13px;">
                  +${quotes.length - 2} more quote${quotes.length - 2 > 1 ? 's' : ''}
                </div>
              ` : ''}
            </div>
          ` : ''}

          <div class="article-footer">
            ${hasQuotes ? `
              <div class="quotes-count">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <span>${quotes.length} ${quotes.length === 1 ? 'Quote' : 'Quotes'}</span>
              </div>
            ` : `
              <div class="quotes-count" style="opacity:0.6;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span>${content.length > 0 ? Math.round(content.length / 100) + ' min read' : 'News Article'}</span>
              </div>
            `}
            <a href="${article.url || '#'}"
               target="_blank"
               rel="noopener noreferrer"
               class="view-article-btn"
               ${!article.url || article.url === '#' ? 'style="opacity:0.5;pointer-events:none;"' : ''}>
              Read Full Article
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
              </svg>
            </a>
          </div>
        </div>
      `;
    }).join('');

    updatePagination();
  }

  function updatePagination() {
    const totalPages = Math.ceil(filteredArticles.length / articlesPerPage);
    document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
    document.getElementById('prevBtn').disabled = currentPage === 1;
    document.getElementById('nextBtn').disabled = currentPage === totalPages || totalPages === 0;
  }

  function changePage(direction) {
    const totalPages = Math.ceil(filteredArticles.length / articlesPerPage);
    const newPage    = currentPage + direction;
    if (newPage >= 1 && newPage <= totalPages) {
      currentPage = newPage;
      renderArticles();
      document.querySelector('.articles-container').scrollIntoView({ behavior: 'smooth' });
    }
  }

  function filterArticles() {
    const term = document.getElementById('searchInput').value.toLowerCase();
    filteredArticles = allArticles.filter(article =>
      (article.title     || '').toLowerCase().includes(term) ||
      (article.content   || '').toLowerCase().includes(term) ||
      (article.publisher || '').toLowerCase().includes(term)
    );
    currentPage = 1;
    renderArticles();
  }

  // Expose globals
  window.changePage    = changePage;
  window.filterArticles = filterArticles;
}
</script>
@endsection