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

  .dashboard-container {
    padding: 24px; background: var(--bg-gray-50);
    min-height: 100vh; max-width: 1600px; margin: 0 auto;
  }
  .page-header { margin-bottom: 32px; }
  .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
  .page-header p  { font-size: 14px; color: var(--text-secondary); margin: 0; }

  .filter-card { background: var(--bg-white); border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); }
  .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
  .filter-label { font-size: 14px; font-weight: 600; color: var(--text-primary); white-space: nowrap; }
  .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }

  .date-picker-trigger {
    display: flex; align-items: center; gap: 12px; padding: 12px 20px;
    background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: 12px;
    font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; color: var(--text-primary);
    cursor: pointer; transition: all 0.2s; width: 100%; max-width: 400px;
  }
  .date-picker-trigger:hover {
    border-color: var(--primary-green); background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }
  .date-picker-trigger svg:first-child { width: 18px; height: 18px; color: var(--text-secondary); flex-shrink: 0; }
  .date-picker-trigger span { flex: 1; text-align: left; }
  .date-picker-trigger svg:last-child { width: 16px; height: 16px; margin-left: auto; color: var(--text-secondary); }

  .apply-btn { padding: 12px 28px; background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); color: white; border: none; border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(3,128,71,0.2); }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,0.3); }
  .apply-btn svg { width: 18px; height: 18px; }

  .date-picker-modal {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000;
    display: none; align-items: center; justify-content: center;
    background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(8px);
  }
  .date-picker-modal.show { display: flex; }
  .date-picker-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); cursor: pointer; }
  .date-picker-container {
    position: relative; background: #ffffff; border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3); display: flex;
    max-width: 900px; width: 90%; max-height: 90vh; z-index: 10001;
    animation: slideUp 0.3s ease-out;
  }
  @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

  .date-picker-sidebar {
    width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray);
    padding: 16px 12px; border-radius: 16px 0 0 16px; display: flex; flex-direction: column;
    gap: 4px; flex-shrink: 0;
  }
  .date-preset { padding: 10px 16px; background: transparent; border: none; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500; color: var(--text-primary); text-align: left; cursor: pointer; transition: all 0.2s; }
  .date-preset:hover { background: var(--bg-white); color: var(--primary-green); }
  .date-preset.active { background: var(--primary-green); color: white; }

  .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; overflow: hidden; }
  .date-picker-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
  .nav-btn { width: 36px; height: 36px; border-radius: 8px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
  .nav-btn:hover { background: var(--primary-green); border-color: var(--primary-green); color: white; }
  .nav-btn svg { width: 20px; height: 20px; }

  .calendars-wrapper { display: flex; gap: 24px; flex: 1; min-height: 0; }
  .calendar { flex: 1; display: flex; flex-direction: column; min-width: 0; }
  .calendar-month { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-align: center; }
  .calendar-weekdays { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 8px; }
  .weekday { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
  .calendar-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
  .calendar-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 500; border-radius: 8px; cursor: pointer; transition: all 0.2s; color: var(--text-primary); background: transparent; border: none; padding: 0; }
  .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
  .calendar-day.other-month { color: #cbd5e1; cursor: default; }
  .calendar-day.disabled { color: #e2e8f0; cursor: not-allowed; }
  .calendar-day.today { border: 2px solid var(--primary-green); }
  .calendar-day.selected { background: var(--primary-green); color: white; }
  .calendar-day.in-range { background: rgba(3, 128, 71, 0.1); color: var(--primary-green); }
  .calendar-day.range-start, .calendar-day.range-end { background: var(--primary-green); color: white; }

  .date-picker-display { padding: 16px 20px; background: var(--bg-gray-50); border-radius: 12px; text-align: center; margin-bottom: 20px; border: 1px solid var(--border-gray); }
  .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }

  .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }
  .cancel-btn, .apply-date-btn { padding: 10px 24px; border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
  .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
  .cancel-btn:hover { background: var(--border-gray); }
  .apply-date-btn { background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); color: white; box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2); }
  .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3); }

  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px; }
  .stat-card { background: var(--bg-white); border-radius: 16px; padding: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden; }
  .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); opacity: 0; transition: opacity 0.3s; }
  .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-green); }
  .stat-card:hover::before { opacity: 1; }
  .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
  .stat-icon-wrapper { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, rgba(3,128,71,0.1) 0%, rgba(3,128,71,0.05) 100%); display: flex; align-items: center; justify-content: center; }
  .stat-icon { width: 28px; height: 28px; color: var(--primary-green); }
  .stat-label { font-size: 13px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
  .stat-value-wrapper { display: flex; align-items: baseline; gap: 12px; margin-bottom: 16px; min-height: 44px; flex-wrap: wrap; }
  .stat-value { font-size: 36px; font-weight: 700; color: var(--text-primary); line-height: 1; }
  .stat-progress { height: 6px; background: var(--bg-gray-100); border-radius: 10px; overflow: hidden; margin-top: 8px; }
  .stat-progress-bar { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); transition: width 1s ease-out; width: 0%; }

  .sentiment-stats { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
  .sentiment-card { flex: 1; min-width: 140px; background: var(--bg-white); border-radius: 12px; padding: 16px 20px; box-shadow: var(--shadow-sm); border: 2px solid transparent; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 12px; user-select: none; }
  .sentiment-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
  .sentiment-card.active-all { border-color: var(--primary-green); background: rgba(3,128,71,0.05); }
  .sentiment-card.active-pos { border-color: #10b981; background: #f0fdf4; }
  .sentiment-card.active-neu { border-color: #6b7280; background: #f9fafb; }
  .sentiment-card.active-neg { border-color: #ef4444; background: #fef2f2; }
  .sentiment-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
  .dot-all { background: var(--primary-green); }
  .dot-pos { background: #10b981; }
  .dot-neu { background: #6b7280; }
  .dot-neg { background: #ef4444; }
  .sentiment-info { flex: 1; }
  .sentiment-label { font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; }
  .sentiment-count { font-size: 22px; font-weight: 700; color: var(--text-primary); line-height: 1.2; }

  .chart-card { background: var(--bg-white); border-radius: 16px; padding: 28px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); margin-bottom: 24px; }
  .chart-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid var(--bg-gray-50); }
  .chart-header h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; }
  .chart-subtitle { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
  .chart-container { position: relative; height: 450px; }
  .chart-toggle { display: flex; gap: 6px; background: var(--bg-gray-50); border-radius: 10px; padding: 4px; border: 1px solid var(--border-gray); }
  .chart-toggle-btn { display: flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; border: none; background: transparent; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; color: var(--text-secondary); cursor: pointer; transition: all 0.2s; white-space: nowrap; }
  .chart-toggle-btn svg { width: 15px; height: 15px; }
  .chart-toggle-btn:hover { color: var(--primary-green); }
  .chart-toggle-btn.active { background: var(--bg-white); color: var(--primary-green); box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); }
  .donut-wrapper { display: none; height: 450px; position: relative; align-items: center; justify-content: center; gap: 32px; }
  .donut-wrapper.visible { display: flex; }
  .donut-canvas-wrap { position: relative; width: 340px; height: 340px; flex-shrink: 0; }
  .donut-legend { display: flex; flex-direction: column; gap: 10px; max-height: 380px; overflow-y: auto; flex: 1; min-width: 0; }
  .donut-legend-item { display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 8px; transition: background 0.15s; cursor: default; }
  .donut-legend-item:hover { background: var(--bg-gray-50); }
  .donut-legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
  .donut-legend-name { font-size: 13px; font-weight: 600; color: var(--text-primary); flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .donut-legend-val { font-size: 12px; font-weight: 700; color: var(--text-secondary); white-space: nowrap; }
  .donut-legend-pct { font-size: 11px; color: var(--text-secondary); min-width: 40px; text-align: right; }
  @media (max-width: 768px) {
    .donut-wrapper { flex-direction: column; height: auto; gap: 16px; }
    .donut-canvas-wrap { width: 260px; height: 260px; }
    .donut-legend { max-height: 200px; width: 100%; }
  }

  .table-section { background: var(--bg-white); border-radius: 16px; padding: 28px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); margin-bottom: 24px; }
  .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid var(--bg-gray-50); gap: 16px; flex-wrap: wrap; }
  .table-header h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0; }
  .table-subtitle { font-size: 13px; color: var(--text-secondary); }
  .table-controls { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
  .table-search { position: relative; width: 260px; }
  .table-search input { width: 100%; padding: 10px 16px 10px 44px; border: 1px solid var(--border-gray); border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; background: var(--bg-gray-50); transition: all 0.2s; box-sizing: border-box; }
  .table-search input:focus { outline: none; border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(3,128,71,0.1); }
  .table-search svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: var(--text-secondary); }
  .per-page-select { padding: 10px 14px; border: 1px solid var(--border-gray); border-radius: 12px; font-family: 'Poppins', sans-serif; font-size: 14px; background: var(--bg-gray-50); color: var(--text-primary); cursor: pointer; outline: none; transition: all 0.2s; }
  .per-page-select:focus { border-color: var(--primary-green); box-shadow: 0 0 0 3px rgba(3,128,71,0.1); }

  .data-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
  .data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid var(--border-gray); white-space: nowrap; }
  .data-table td { padding: 14px 16px; font-size: 13px; color: var(--text-primary); border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
  .data-table tbody tr { transition: all 0.2s; background: var(--bg-white); }
  .data-table tbody tr:hover { background: #fafbfc; }
  .data-table tbody tr:last-child td { border-bottom: none; }
  .hashtag-name { font-weight: 600; color: var(--primary-green); font-size: 14px; }
  .hashtag-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: rgba(3,128,71,0.1); color: var(--primary-green); border-radius: 20px; font-size: 12px; font-weight: 600; }
  .badge-pos { background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
  .badge-neu { background: #f3f4f6; color: #374151; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
  .badge-neg { background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
  .bar-wrap { display: flex; align-items: center; gap: 10px; }
  .mini-bar { flex: 1; height: 6px; background: var(--bg-gray-100); border-radius: 10px; overflow: hidden; max-width: 120px; }
  .mini-bar-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--primary-green), var(--primary-green-dark)); transition: width 0.6s ease; }

  .pagination-wrapper { display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border-gray); flex-wrap: wrap; gap: 12px; }
  .pagination-info { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
  .pagination-controls { display: flex; align-items: center; gap: 6px; }
  .page-btn { width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--border-gray); background: var(--bg-white); color: var(--text-primary); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; }
  .page-btn:hover:not(:disabled) { border-color: var(--primary-green); color: var(--primary-green); background: rgba(3,128,71,0.05); }
  .page-btn.active { background: var(--primary-green); color: white; border-color: var(--primary-green); }
  .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
  .page-btn svg { width: 16px; height: 16px; }

  .loading-skeleton { background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite; border-radius: 8px; }
  @keyframes shimmer { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }
  .data-loaded { animation: fadeIn 0.4s ease-out; }
  @keyframes fadeIn { from{opacity:0;transform:scale(0.95);} to{opacity:1;transform:scale(1);} }
  .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px; }
  .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
  .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; gap: 12px; }
  .empty-state svg { width: 48px; height: 48px; color: var(--border-gray); }
  .empty-state p { font-size: 14px; font-weight: 600; color: var(--text-secondary); margin: 0; }

  @media (max-width: 768px) {
    .date-picker-container { flex-direction: column; max-height: 85vh; overflow-y: auto; width: 95%; }
    .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; padding: 12px 16px; }
    .date-preset { white-space: nowrap; }
    .date-picker-content { padding: 20px 16px; }
    .calendars-wrapper { flex-direction: column; gap: 16px; }
    .date-picker-trigger { max-width: 100%; }
    .cancel-btn, .apply-date-btn { flex: 1; }
  }
  @media (max-width: 1024px) {
    .filter-content { flex-direction: column; align-items: stretch; }
    .date-range-wrapper { flex-direction: column; }
    .apply-btn { width: 100%; justify-content: center; }
    .table-controls { flex-direction: column; align-items: stretch; }
    .table-search { width: 100%; }
  }
  @media (max-width: 640px) {
    .stat-value { font-size: 28px; }
    .chart-container { height: 300px; }
    .dashboard-container { padding: 16px; }
    .sentiment-stats { flex-direction: column; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <div class="page-header">
    <h1>Top Hashtags</h1>
    <p>Discover the most popular hashtags and trending topics on X (Twitter)</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;flex-shrink:0;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view Top Hashtags.</span>
  </div>
  @else

  <!-- Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.top-hashtags') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">

      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
          Date Range
        </div>

        <div class="date-range-wrapper">
          <button type="button" class="date-picker-trigger" id="datePickerTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="dateRangeDisplay">{{ $startDate }} to {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
        </div>

        <button type="submit" class="apply-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
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

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/>
          <line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/>
        </svg>
      </div></div>
      <div class="stat-label">Total Hashtags</div>
      <div id="totalHashtagsValue" class="stat-value-wrapper">
        <div class="loading-skeleton" style="height:44px;width:120px;border-radius:8px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" id="bar1"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
      </div></div>
      <div class="stat-label">Total Mentions</div>
      <div id="totalMentionsValue" class="stat-value-wrapper">
        <div class="loading-skeleton" style="height:44px;width:140px;border-radius:8px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" id="bar2"></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-header"><div class="stat-icon-wrapper">
        <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </svg>
      </div></div>
      <div class="stat-label">Top Hashtag</div>
      <div id="topHashtagValue" class="stat-value-wrapper">
        <div class="loading-skeleton" style="height:44px;width:160px;border-radius:8px;"></div>
      </div>
      <div class="stat-progress"><div class="stat-progress-bar" id="bar3"></div></div>
    </div>
  </div>

  <!-- Sentiment Filter -->
  <div class="sentiment-stats" id="sentimentStats" style="display:none;">
    <div class="sentiment-card active-all" id="filterAll" onclick="setSentimentFilter('all')">
      <div class="sentiment-dot dot-all"></div>
      <div class="sentiment-info">
        <div class="sentiment-label">All</div>
        <div class="sentiment-count" id="countAll">0</div>
      </div>
    </div>
    <div class="sentiment-card" id="filterPos" onclick="setSentimentFilter('positive')">
      <div class="sentiment-dot dot-pos"></div>
      <div class="sentiment-info">
        <div class="sentiment-label">Positive</div>
        <div class="sentiment-count" id="countPos">0</div>
      </div>
    </div>
    <div class="sentiment-card" id="filterNeu" onclick="setSentimentFilter('neutral')">
      <div class="sentiment-dot dot-neu"></div>
      <div class="sentiment-info">
        <div class="sentiment-label">Neutral</div>
        <div class="sentiment-count" id="countNeu">0</div>
      </div>
    </div>
    <div class="sentiment-card" id="filterNeg" onclick="setSentimentFilter('negative')">
      <div class="sentiment-dot dot-neg"></div>
      <div class="sentiment-info">
        <div class="sentiment-label">Negative</div>
        <div class="sentiment-count" id="countNeg">0</div>
      </div>
    </div>
  </div>

  <!-- Chart -->
  <div class="chart-card">
    <div class="chart-header">
      <div>
        <h3>Top 10 Hashtags</h3>
        <p class="chart-subtitle" id="chartSubtitle">Most mentioned hashtags by volume</p>
      </div>
      <div class="chart-toggle">
        <button type="button" class="chart-toggle-btn active" id="btnBar" onclick="switchChart('bar')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="12" width="4" height="9"/><rect x="10" y="7" width="4" height="14"/><rect x="17" y="3" width="4" height="18"/>
          </svg>
          Bar
        </button>
        <button type="button" class="chart-toggle-btn" id="btnDonut" onclick="switchChart('donut')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/>
          </svg>
          Donut
        </button>
      </div>
    </div>
    <div class="chart-container" id="barChartWrap">
      <div id="hashtagsChartLoading" class="loading-skeleton" style="height:100%;"></div>
      <canvas id="hashtagsChart" style="display:none;"></canvas>
    </div>
    <div class="donut-wrapper" id="donutChartWrap">
      <div class="donut-canvas-wrap">
        <canvas id="hashtagsDonut"></canvas>
      </div>
      <div class="donut-legend" id="donutLegend"></div>
    </div>
  </div>

  <!-- Table -->
  <div class="table-section">
    <div class="table-header">
      <div>
        <h3>All Hashtags</h3>
        <p class="table-subtitle" id="tableSubtitle">Complete list of trending hashtags</p>
      </div>
      <div class="table-controls">
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          <input type="text" id="hashtagSearchInput" placeholder="Search hashtags..." oninput="onSearch()">
        </div>
        <select class="per-page-select" id="perPageSelect" onchange="onPerPageChange()">
          <option value="10">10 / page</option>
          <option value="25" selected>25 / page</option>
          <option value="50">50 / page</option>
          <option value="100">100 / page</option>
        </select>
      </div>
    </div>
    <div id="hashtagsTableLoading" class="loading-skeleton" style="height:400px;"></div>
    <div id="hashtagsTable" style="display:none; overflow-x:auto;"></div>
    <div id="paginationWrapper" class="pagination-wrapper" style="display:none;"></div>
  </div>

  @endif
</div>
@endsection

@section('scripts')
<script>
(function () {
  'use strict';

  // ─── Date Picker ─────────────────────────────────────────
  let selectedStartDate = null;
  let selectedEndDate   = null;
  let currentMonth1     = new Date();
  let currentMonth2     = new Date();
  let selectingStart    = true;

  function dpInit() {
    const startVal = document.getElementById('hiddenStartDate').value;
    const endVal   = document.getElementById('hiddenEndDate').value;

    selectedStartDate = startVal ? new Date(startVal) : (() => { const d = new Date(); d.setDate(d.getDate() - 6); return d; })();
    selectedEndDate   = endVal   ? new Date(endVal)   : new Date();

    currentMonth1 = new Date(selectedStartDate);
    currentMonth2 = new Date(selectedStartDate);
    currentMonth2.setMonth(currentMonth2.getMonth() + 1);

    renderCalendars();

    document.getElementById('datePickerTrigger').addEventListener('click', openPicker);
    document.querySelector('.date-picker-overlay').addEventListener('click', closePicker);
    document.querySelector('.cancel-btn').addEventListener('click', closePicker);
    document.getElementById('applyDatePicker').addEventListener('click', applyPicker);
    document.getElementById('prevMonth').addEventListener('click', function () {
      currentMonth1.setMonth(currentMonth1.getMonth() - 1);
      currentMonth2.setMonth(currentMonth2.getMonth() - 1);
      renderCalendars();
    });
    document.getElementById('nextMonth').addEventListener('click', function () {
      currentMonth1.setMonth(currentMonth1.getMonth() + 1);
      currentMonth2.setMonth(currentMonth2.getMonth() + 1);
      renderCalendars();
    });
    document.querySelectorAll('.date-preset').forEach(function (btn) {
      btn.addEventListener('click', handlePreset);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closePicker();
    });
  }

  function openPicker()  { document.getElementById('datePickerModal').classList.add('show'); renderCalendars(); }
  function closePicker() { document.getElementById('datePickerModal').classList.remove('show'); }

  function applyPicker() {
    const start = fmtDate(selectedStartDate);
    const end   = fmtDate(selectedEndDate);
    document.getElementById('hiddenStartDate').value  = start;
    document.getElementById('hiddenEndDate').value    = end;
    document.getElementById('dateRangeDisplay').textContent = start + ' to ' + end;
    closePicker();
  }

  function handlePreset(e) {
    document.querySelectorAll('.date-preset').forEach(function (b) { b.classList.remove('active'); });
    e.target.classList.add('active');

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const preset = e.target.dataset.preset;

    switch (preset) {
      case 'today':
        selectedStartDate = new Date(today);
        selectedEndDate   = new Date(today);
        break;
      case 'yesterday':
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 1);
        selectedEndDate = new Date(selectedStartDate);
        break;
      case 'last7days':
        selectedEndDate   = new Date(today);
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 6);
        break;
      case 'last30days':
        selectedEndDate   = new Date(today);
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 29);
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
      updatePickerDisplay();
      renderCalendars();
    }
  }

  function renderCalendars() {
    buildCalendar('calendar1', currentMonth1);
    buildCalendar('calendar2', currentMonth2);
    updatePickerDisplay();
  }

  function buildCalendar(elId, month) {
    const el = document.getElementById(elId);
    if (!el) return;

    const year     = month.getFullYear();
    const monthNum = month.getMonth();
    const firstDay = new Date(year, monthNum, 1);
    const lastDay  = new Date(year, monthNum + 1, 0);
    const prevLast = new Date(year, monthNum, 0);
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const weekdays   = ['Su','Mo','Tu','We','Th','Fr','Sa'];
    const today      = new Date();
    today.setHours(0, 0, 0, 0);

    let html = '<div class="calendar-month">' + monthNames[monthNum] + ' ' + year + '</div>';
    html += '<div class="calendar-weekdays">' + weekdays.map(function (d) { return '<div class="weekday">' + d + '</div>'; }).join('') + '</div>';
    html += '<div class="calendar-days">';

    for (let i = firstDay.getDay() - 1; i >= 0; i--) {
      html += '<button type="button" class="calendar-day other-month" disabled>' + (prevLast.getDate() - i) + '</button>';
    }

    for (let day = 1; day <= lastDay.getDate(); day++) {
      const date = new Date(year, monthNum, day);
      date.setHours(0, 0, 0, 0);
      const dateStr = fmtDate(date);
      let cls = 'calendar-day';

      if (isSameDay(date, today)) cls += ' today';
      if (date > today) cls += ' disabled';

      if (selectedStartDate && selectedEndDate) {
        if (isSameDay(date, selectedStartDate)) { cls += ' selected range-start'; }
        else if (isSameDay(date, selectedEndDate)) { cls += ' selected range-end'; }
        else if (date > selectedStartDate && date < selectedEndDate) { cls += ' in-range'; }
      }

      const disabled = date > today ? 'disabled' : '';
      html += '<button type="button" class="' + cls + '" data-date="' + dateStr + '" ' + disabled + '>' + day + '</button>';
    }

    const lastDow = lastDay.getDay();
    for (let i = 1; i < 7 - lastDow; i++) {
      html += '<button type="button" class="calendar-day other-month" disabled>' + i + '</button>';
    }

    html += '</div>';
    el.innerHTML = html;

    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(function (btn) {
      btn.addEventListener('click', handleDayClick);
    });
  }

  function handleDayClick(e) {
    const date = new Date(e.target.dataset.date);
    date.setHours(0, 0, 0, 0);

    document.querySelectorAll('.date-preset').forEach(function (b) { b.classList.remove('active'); });
    const customBtn = document.querySelector('[data-preset="custom"]');
    if (customBtn) customBtn.classList.add('active');

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

    updatePickerDisplay();
    renderCalendars();
  }

  function updatePickerDisplay() {
    if (!selectedStartDate || !selectedEndDate) return;
    const text = fmtDate(selectedStartDate) + ' to ' + fmtDate(selectedEndDate);
    const el = document.getElementById('selectedRangeText');
    if (el) el.textContent = text;
  }

  function fmtDate(date) {
    if (!date) return '';
    const y  = date.getFullYear();
    const m  = String(date.getMonth() + 1).padStart(2, '0');
    const d  = String(date.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + d;
  }

  function isSameDay(a, b) {
    if (!a || !b) return false;
    return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  }

  // ─── Hashtags Logic ───────────────────────────────────────
  const projectId = '{{ $projectId ?? "" }}';
  const startDate = '{{ $startDate ?? "" }}';
  const endDate   = '{{ $endDate ?? "" }}';

  if (!projectId || !startDate || !endDate) return;

  let allHashtags     = [];
  let filteredList    = [];
  let currentPage     = 1;
  let perPage         = 25;
  let sentimentFilter = 'all';
  let searchQuery     = '';
  let chartInstance   = null;
  let donutInstance   = null;
  let chartMode       = 'bar';
  let lastChartData   = [];

  function fmt(n) { return new Intl.NumberFormat('en-US').format(n || 0); }

  function sentimentBadge(s) {
    if (s === 'positive') return '<span class="badge-pos">Positive</span>';
    if (s === 'negative') return '<span class="badge-neg">Negative</span>';
    return '<span class="badge-neu">Neutral</span>';
  }

  function emptyState(msg) {
    return '<div class="empty-state"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><p>' + msg + '</p></div>';
  }

  const posWords = ['win','winner','won','best','good','great','love','happy','success','amazing','excellent','perfect','beautiful','wonderful','fantastic','celebrate','victory','achievement','congratulations','bagus','baik','hebat','keren','mantap','juara','bangga','selamat','merdeka'];
  const negWords = ['bad','worst','hate','sad','fail','failed','lose','lost','angry','terrible','awful','wrong','crisis','disaster','death','died','scandal','protest','boycott','corrupt','bohong','buruk','jelek','goblok','tolol','curang','korupsi','tangkap','penjara','penjarakan','copot','mundur','turun'];

  function detectSentiment(name) {
    const lc = name.toLowerCase();
    for (const w of posWords) if (lc.includes(w)) return 'positive';
    for (const w of negWords) if (lc.includes(w)) return 'negative';
    return 'neutral';
  }

  async function loadHashtags() {
    try {
      const url  = '/mk/api/x/top-hashtags-data?project_id=' + projectId + '&start_date=' + startDate + '&end_date=' + endDate;
      const res  = await fetch(url);
      const data = await res.json();

      if (!data.success || !data.data) throw new Error(data.error || 'No data');

      let hashtags = Array.isArray(data.data.hashtags)
        ? data.data.hashtags
        : (Array.isArray(data.data) ? data.data : []);

      hashtags = hashtags.map(function (h) {
        return Object.assign({}, h, { sentiment: h.sentiment || detectSentiment(h.name) });
      });

      allHashtags = hashtags;

      const totalMentions = data.data.total_mentions || hashtags.reduce(function (s, h) { return s + (parseInt(h.size) || 0); }, 0);
      const topHashtag    = data.data.top_hashtag || hashtags[0] || null;

      document.getElementById('totalHashtagsValue').innerHTML = '<div class="stat-value">' + fmt(hashtags.length) + '</div>';
      document.getElementById('totalMentionsValue').innerHTML = '<div class="stat-value">' + fmt(totalMentions) + '</div>';
      document.getElementById('topHashtagValue').innerHTML    = topHashtag
        ? '<div class="stat-value" style="font-size:26px;">#' + topHashtag.name + '</div><span class="hashtag-badge">' + fmt(topHashtag.size) + ' mentions</span>'
        : '<div class="stat-value" style="font-size:24px;">No data</div>';

      ['totalHashtagsValue','totalMentionsValue','topHashtagValue'].forEach(function (id) {
        document.getElementById(id).classList.add('data-loaded');
      });

      setTimeout(function () {
        document.getElementById('bar1').style.width = '85%';
        document.getElementById('bar2').style.width = '92%';
        document.getElementById('bar3').style.width = '100%';
      }, 300);

      updateSentimentCounts();
      document.getElementById('sentimentStats').style.display = 'flex';
      applyFilters();

    } catch (err) {
      document.getElementById('hashtagsChartLoading').innerHTML = emptyState('Failed to load: ' + err.message);
      document.getElementById('hashtagsTableLoading').innerHTML = emptyState('Failed to load hashtags');
    }
  }

  function applyFilters() {
    let list = allHashtags.slice();
    if (sentimentFilter !== 'all') list = list.filter(function (h) { return h.sentiment === sentimentFilter; });
    if (searchQuery) list = list.filter(function (h) { return h.name.toLowerCase().includes(searchQuery); });
    filteredList = list;
    currentPage  = 1;

    const label = sentimentFilter === 'all' ? 'all sentiments' : sentimentFilter.charAt(0).toUpperCase() + sentimentFilter.slice(1);
    document.getElementById('tableSubtitle').textContent = 'Showing ' + fmt(filteredList.length) + ' hashtags — ' + label;
    document.getElementById('chartSubtitle').textContent = 'Top 10 by volume — ' + label;

    renderChart(filteredList.slice(0, 10));
    renderTable();
  }

  function updateSentimentCounts() {
    document.getElementById('countAll').textContent = fmt(allHashtags.length);
    document.getElementById('countPos').textContent = fmt(allHashtags.filter(function (h) { return h.sentiment === 'positive'; }).length);
    document.getElementById('countNeu').textContent = fmt(allHashtags.filter(function (h) { return h.sentiment === 'neutral'; }).length);
    document.getElementById('countNeg').textContent = fmt(allHashtags.filter(function (h) { return h.sentiment === 'negative'; }).length);
  }

  window.setSentimentFilter = function (val) {
    sentimentFilter = val;
    document.getElementById('filterAll').className = 'sentiment-card' + (val === 'all'      ? ' active-all' : '');
    document.getElementById('filterPos').className = 'sentiment-card' + (val === 'positive' ? ' active-pos' : '');
    document.getElementById('filterNeu').className = 'sentiment-card' + (val === 'neutral'  ? ' active-neu' : '');
    document.getElementById('filterNeg').className = 'sentiment-card' + (val === 'negative' ? ' active-neg' : '');
    applyFilters();
  };

  window.onSearch = function () {
    searchQuery = document.getElementById('hashtagSearchInput').value.toLowerCase().trim();
    applyFilters();
  };

  window.onPerPageChange = function () {
    perPage     = parseInt(document.getElementById('perPageSelect').value);
    currentPage = 1;
    renderTable();
  };

  window.switchChart = function (mode) {
    chartMode = mode;
    document.getElementById('btnBar').classList.toggle('active', mode === 'bar');
    document.getElementById('btnDonut').classList.toggle('active', mode === 'donut');
    document.getElementById('barChartWrap').style.display   = mode === 'bar' ? 'block' : 'none';
    const donutWrap = document.getElementById('donutChartWrap');
    donutWrap.classList.toggle('visible', mode === 'donut');
    if (mode === 'donut') {
      renderDonut(lastChartData);
    }
  };

  function renderChart(data) {
    lastChartData = data;
    const canvas  = document.getElementById('hashtagsChart');
    const loading = document.getElementById('hashtagsChartLoading');

    if (chartInstance) { chartInstance.destroy(); chartInstance = null; }

    if (!data.length) {
      loading.innerHTML     = emptyState('No hashtag data for this filter');
      loading.style.display = 'block';
      canvas.style.display  = 'none';
      document.getElementById('donutLegend').innerHTML = '';
      if (donutInstance) { donutInstance.destroy(); donutInstance = null; }
      return;
    }

    const colors  = data.map(function (h) { return h.sentiment === 'positive' ? 'rgba(16,185,129,0.8)' : h.sentiment === 'negative' ? 'rgba(239,68,68,0.8)' : 'rgba(3,128,71,0.8)'; });
    const borders = data.map(function (h) { return h.sentiment === 'positive' ? '#10b981' : h.sentiment === 'negative' ? '#ef4444' : '#038047'; });

    chartInstance = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: data.map(function (h) { return '#' + h.name; }),
        datasets: [{ label: 'Mentions', data: data.map(function (h) { return parseInt(h.size) || 0; }), backgroundColor: colors, borderColor: borders, borderWidth: 2, borderRadius: 8 }]
      },
      options: {
        responsive: true, maintainAspectRatio: false, indexAxis: 'y',
        plugins: {
          legend: { display: false },
          tooltip: { backgroundColor: '#1a202c', padding: 16, titleColor: '#fff', bodyColor: '#fff', titleFont: { size: 14, weight: '600' }, bodyFont: { size: 13 }, displayColors: false, cornerRadius: 8, callbacks: { label: function (ctx) { return fmt(ctx.parsed.x) + ' mentions'; } } }
        },
        scales: {
          x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#64748b', font: { family: 'Poppins', size: 12 }, callback: function (v) { return fmt(v); } } },
          y: { grid: { display: false }, ticks: { color: '#64748b', font: { family: 'Poppins', size: 12, weight: '600' } } }
        }
      }
    });

    loading.style.display = 'none';
    canvas.style.display  = 'block';

    if (chartMode === 'donut') {
      renderDonut(data);
    }
  }

  function renderDonut(data) {
    const canvas = document.getElementById('hashtagsDonut');
    if (donutInstance) { donutInstance.destroy(); donutInstance = null; }

    if (!data.length) return;

    const donutColors = [
      '#038047','#10b981','#3b82f6','#f59e0b','#ef4444',
      '#8b5cf6','#ec4899','#06b6d4','#84cc16','#f97316'
    ];

    const total  = data.reduce(function (s, h) { return s + (parseInt(h.size) || 0); }, 0);
    const values = data.map(function (h) { return parseInt(h.size) || 0; });
    const labels = data.map(function (h) { return '#' + h.name; });
    const bgColors = data.map(function (_, i) { return donutColors[i % donutColors.length]; });

    donutInstance = new Chart(canvas.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: values,
          backgroundColor: bgColors,
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverOffset: 12
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '62%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1a202c', padding: 14, titleColor: '#fff', bodyColor: '#fff',
            titleFont: { size: 13, weight: '600' }, bodyFont: { size: 13 }, cornerRadius: 8,
            callbacks: {
              label: function (ctx) {
                const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : '0';
                return fmt(ctx.parsed) + ' mentions (' + pct + '%)';
              }
            }
          }
        },
        animation: { animateRotate: true, duration: 600 }
      }
    });

    const legend = document.getElementById('donutLegend');
    legend.innerHTML = data.map(function (h, i) {
      const val = parseInt(h.size) || 0;
      const pct = total > 0 ? ((val / total) * 100).toFixed(1) : '0';
      return '<div class="donut-legend-item">'
        + '<div class="donut-legend-dot" style="background:' + bgColors[i] + ';"></div>'
        + '<span class="donut-legend-name">#' + h.name + '</span>'
        + '<span class="donut-legend-val">' + fmt(val) + '</span>'
        + '<span class="donut-legend-pct">' + pct + '%</span>'
        + '</div>';
    }).join('');
  }

  function renderTable() {
    const container  = document.getElementById('hashtagsTable');
    const loading    = document.getElementById('hashtagsTableLoading');
    const pagWrapper = document.getElementById('paginationWrapper');

    const total    = filteredList.reduce(function (s, h) { return s + (parseInt(h.size) || 0); }, 0);
    const maxVal   = filteredList.length ? (parseInt(filteredList[0].size) || 1) : 1;
    const totalPgs = Math.max(1, Math.ceil(filteredList.length / perPage));
    const start    = (currentPage - 1) * perPage;
    const pageData = filteredList.slice(start, start + perPage);

    let html = '<table class="data-table"><thead><tr><th style="width:56px;">RANK</th><th>HASHTAG</th><th>SENTIMENT</th><th>MENTIONS</th><th>SHARE</th><th>VOLUME</th></tr></thead><tbody>';

    if (!pageData.length) {
      html += '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-secondary);">No hashtags found</td></tr>';
    } else {
      pageData.forEach(function (h, i) {
        const rank = start + i + 1;
        const pct  = total > 0 ? ((parseInt(h.size) || 0) / total * 100).toFixed(2) : '0.00';
        const barW = maxVal > 0 ? Math.round(((parseInt(h.size) || 0) / maxVal) * 100) : 0;
        html += '<tr><td><strong>' + rank + '</strong></td><td><span class="hashtag-name">#' + h.name + '</span></td><td>' + sentimentBadge(h.sentiment) + '</td><td><strong>' + fmt(h.size) + '</strong></td><td>' + pct + '%</td><td><div class="bar-wrap"><div class="mini-bar"><div class="mini-bar-fill" style="width:' + barW + '%"></div></div></div></td></tr>';
      });
    }

    html += '</tbody></table>';
    container.innerHTML = html;
    container.classList.add('data-loaded');
    loading.style.display   = 'none';
    container.style.display = 'block';

    const from  = filteredList.length ? start + 1 : 0;
    const to    = Math.min(currentPage * perPage, filteredList.length);
    let pHtml   = '<div class="pagination-info">Showing ' + fmt(from) + '–' + fmt(to) + ' of ' + fmt(filteredList.length) + ' hashtags</div>';
    pHtml += '<div class="pagination-controls">';
    pHtml += '<button class="page-btn" onclick="goPage(' + (currentPage - 1) + ')" ' + (currentPage === 1 ? 'disabled' : '') + '><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>';
    getPageRange(currentPage, totalPgs).forEach(function (p) {
      pHtml += p === '...'
        ? '<button class="page-btn" disabled style="cursor:default;">…</button>'
        : '<button class="page-btn ' + (p === currentPage ? 'active' : '') + '" onclick="goPage(' + p + ')">' + p + '</button>';
    });
    pHtml += '<button class="page-btn" onclick="goPage(' + (currentPage + 1) + ')" ' + (currentPage === totalPgs ? 'disabled' : '') + '><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>';
    pHtml += '</div>';
    pagWrapper.innerHTML     = pHtml;
    pagWrapper.style.display = filteredList.length > 0 ? 'flex' : 'none';
  }

  function getPageRange(cur, total) {
    if (total <= 7) return Array.from({ length: total }, function (_, i) { return i + 1; });
    if (cur <= 4)          return [1, 2, 3, 4, 5, '...', total];
    if (cur >= total - 3)  return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
    return [1, '...', cur - 1, cur, cur + 1, '...', total];
  }

  window.goPage = function (p) {
    const totalPgs = Math.ceil(filteredList.length / perPage);
    if (p < 1 || p > totalPgs) return;
    currentPage = p;
    renderTable();
    document.querySelector('.table-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
  };

  document.addEventListener('DOMContentLoaded', function () {
    dpInit();
    loadHashtags();
  });
})();
</script>
@endsection