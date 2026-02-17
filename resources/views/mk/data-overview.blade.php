@extends('mk.layouts.app')

@section('title', 'Data Overview - SMADIMENT')

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
    padding: 24px;
    background: var(--bg-gray-50);
    min-height: 100vh;
  }

  /* Page Header */
  .page-header { margin-bottom: 28px; }
  .page-header h1 {
    font-size: 28px; font-weight: 700;
    color: var(--text-primary); margin: 0 0 6px 0;
  }
  .page-header p {
    font-size: 14px; color: var(--text-secondary);
    font-weight: 500; margin: 0;
  }

  /* Filter Card */
  .filter-card {
    background: var(--bg-white);
    border-radius: 16px; padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
  }
  .filter-content {
    display: flex; align-items: flex-end;
    gap: 16px; flex-wrap: wrap;
  }
  .filter-group { display: flex; flex-direction: column; gap: 6px; }
  .filter-label {
    font-size: 11px; font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase; letter-spacing: 0.5px;
  }
  .filter-select {
    padding: 10px 14px;
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px; font-weight: 500;
    color: var(--text-primary);
    background: var(--bg-gray-50);
    outline: none; transition: all 0.2s; min-width: 200px;
  }
  .filter-select:focus {
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3,128,71,0.1);
  }
  .date-picker-trigger {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 16px;
    background: var(--bg-gray-50);
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px; font-weight: 500;
    color: var(--text-primary);
    cursor: pointer; transition: all 0.2s; min-width: 300px;
  }
  .date-picker-trigger:hover {
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3,128,71,0.1);
  }
  .date-picker-trigger svg:first-child { width:16px; height:16px; color:var(--text-secondary); flex-shrink:0; }
  .date-picker-trigger span { flex:1; text-align:left; }
  .date-picker-trigger svg:last-child { width:14px; height:14px; color:var(--text-secondary); }

  .apply-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 24px;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: white; border: none; border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px; font-weight: 600;
    cursor: pointer; transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(3,128,71,0.2); white-space: nowrap;
  }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,0.3); }
  .apply-btn svg { width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:2.5; stroke-linecap:round; }

  /* Date Picker Modal */
  .date-picker-modal {
    position: fixed; top:0; left:0; right:0; bottom:0;
    z-index: 10000; display: none;
    align-items: center; justify-content: center;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(8px);
  }
  .date-picker-modal.show { display: flex; }
  .date-picker-overlay { position:absolute; top:0; left:0; right:0; bottom:0; cursor:pointer; }
  .date-picker-container {
    position: relative; background: #ffffff; border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    display: flex; max-width: 900px; width: 90%; max-height: 90vh;
    z-index: 10001; animation: dpUp 0.3s ease-out;
  }
  @keyframes dpUp {
    from { opacity:0; transform:translateY(20px) scale(0.95); }
    to   { opacity:1; transform:translateY(0) scale(1); }
  }
  .date-picker-sidebar {
    width: 180px; background: var(--bg-gray-50);
    border-right: 1px solid var(--border-gray);
    padding: 16px 12px; border-radius: 16px 0 0 16px;
    display: flex; flex-direction: column; gap: 4px; flex-shrink: 0;
  }
  .date-preset {
    padding: 10px 16px; background: transparent; border: none;
    border-radius: 8px; font-family: 'Poppins', sans-serif;
    font-size: 13px; font-weight: 500; color: var(--text-primary);
    text-align: left; cursor: pointer; transition: all 0.2s;
  }
  .date-preset:hover  { background: var(--bg-white); color: var(--primary-green); }
  .date-preset.active { background: var(--primary-green); color: white; }
  .date-picker-content { flex:1; padding:24px; display:flex; flex-direction:column; overflow:hidden; }
  .date-picker-header  { display:flex; align-items:flex-start; gap:20px; margin-bottom:20px; }
  .nav-btn {
    width:36px; height:36px; border-radius:8px;
    background:var(--bg-gray-50); border:1px solid var(--border-gray);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all 0.2s; flex-shrink:0;
  }
  .nav-btn:hover { background:var(--primary-green); border-color:var(--primary-green); color:white; }
  .nav-btn svg { width:20px; height:20px; }
  .calendars-wrapper { display:flex; gap:24px; flex:1; min-height:0; }
  .calendar { flex:1; display:flex; flex-direction:column; min-width:0; }
  .calendar-month { font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:16px; text-align:center; }
  .calendar-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:8px; }
  .weekday { text-align:center; font-size:11px; font-weight:700; color:var(--text-secondary); padding:8px 0; }
  .calendar-days { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
  .calendar-day {
    aspect-ratio:1; display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:500; border-radius:8px; cursor:pointer;
    transition:all 0.2s; color:var(--text-primary); background:transparent; border:none; padding:0;
  }
  .calendar-day:hover:not(.disabled):not(.other-month) { background:var(--bg-gray-100); }
  .calendar-day.other-month { color:#cbd5e1; cursor:default; }
  .calendar-day.disabled    { color:#e2e8f0; cursor:not-allowed; }
  .calendar-day.today       { border:2px solid var(--primary-green); }
  .calendar-day.selected    { background:var(--primary-green); color:white; }
  .calendar-day.in-range    { background:rgba(3,128,71,0.1); color:var(--primary-green); }
  .calendar-day.range-start,
  .calendar-day.range-end   { background:var(--primary-green); color:white; }
  .date-picker-display {
    padding:16px 20px; background:var(--bg-gray-50); border-radius:12px;
    text-align:center; margin-bottom:20px; border:1px solid var(--border-gray);
  }
  .date-picker-display span { font-size:14px; font-weight:600; color:var(--text-primary); }
  .date-picker-footer { display:flex; gap:12px; justify-content:flex-end; }
  .cancel-btn, .apply-date-btn {
    padding:10px 24px; border-radius:10px;
    font-family:'Poppins',sans-serif; font-size:14px; font-weight:600;
    cursor:pointer; transition:all 0.2s; border:none;
  }
  .cancel-btn { background:var(--bg-gray-100); color:var(--text-primary); }
  .cancel-btn:hover { background:var(--border-gray); }
  .apply-date-btn {
    background:linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color:white; box-shadow:0 4px 12px rgba(3,128,71,0.2);
  }
  .apply-date-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(3,128,71,0.3); }

  /* Card */
  .do-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 16px; overflow: hidden;
    display: flex; flex-direction: column;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    position: relative;
  }
  .do-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    opacity:0; transition:opacity 0.3s;
  }
  .do-card:hover { box-shadow:var(--shadow-lg); border-color:rgba(3,128,71,0.25); }
  .do-card:hover::before { opacity:1; }

  .do-card-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:16px 20px; border-bottom:1px solid var(--border-gray);
  }
  .do-card-head-left { display:flex; align-items:center; gap:12px; }

  .do-head-icon {
    width:40px; height:40px; border-radius:12px;
    background:linear-gradient(135deg, rgba(3,128,71,0.1) 0%, rgba(3,128,71,0.05) 100%);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
  }
  .do-head-icon svg {
    width:20px; height:20px; fill:none;
    stroke:var(--primary-green);
    stroke-width:2; stroke-linecap:round; stroke-linejoin:round;
  }
  .do-card-title { font-size:16px; font-weight:700; color:var(--text-primary); }

  .do-badge {
    display:inline-flex; align-items:center;
    padding:4px 12px; border-radius:20px;
    font-size:11px; font-weight:700;
    text-transform:uppercase; letter-spacing:0.4px;
    background:var(--bg-gray-100); color:var(--text-secondary);
  }

  .do-card-body { padding:20px; flex:1; }
  .do-body-scroll { max-height:220px; overflow-y:auto; }
  .do-body-scroll::-webkit-scrollbar { width:4px; }
  .do-body-scroll::-webkit-scrollbar-thumb { background:var(--border-gray); border-radius:2px; }

  /* Grid Layouts */
  .do-row-top {
    display:grid;
    grid-template-columns: 1.1fr 1.1fr 0.8fr;
    gap:20px; margin-bottom:20px;
  }
  .do-row-mid {
    display:grid;
    grid-template-columns: 1fr 1.5fr;
    gap:20px; margin-bottom:20px;
  }

  /* Mini Table */
  .do-tbl { width:100%; border-collapse:separate; border-spacing:0; font-size:13px; }
  .do-tbl th {
    padding:0 0 10px; text-align:left;
    font-size:10px; font-weight:700; color:var(--text-secondary);
    text-transform:uppercase; letter-spacing:0.4px;
    border-bottom:1px solid var(--border-gray);
  }
  .do-tbl td {
    padding:10px 0; color:var(--text-primary);
    border-bottom:1px solid #f1f5f9; vertical-align:middle;
  }
  .do-tbl tbody tr { transition:all 0.2s; }
  .do-tbl tbody tr:hover { background:#fafbfc; }
  .do-tbl tbody tr:last-child td { border-bottom:none; }
  .do-tbl-rank { font-weight:800; color:var(--primary-green); width:24px; font-size:12px; }
  .do-tbl-name { font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .do-tbl-num  { text-align:right; font-weight:700; font-size:12px; color:var(--text-secondary); }

  /* Topic Link */
  .topic-link {
    color: var(--text-primary); text-decoration: none;
    transition: all 0.2s; display: block; cursor: pointer;
  }
  .topic-link:hover { color: var(--primary-green); text-decoration: underline; }

  /* Mention */
  .mention-split { display:grid; grid-template-columns:1fr 1fr; flex:1; }
  .mention-split-item {
    display:flex; flex-direction:column;
    justify-content:center; padding:24px 20px; min-height:160px;
  }
  .mention-split-item:first-child { border-right:1px solid var(--border-gray); }
  .mention-split-label {
    font-size:11px; font-weight:700; color:var(--text-secondary);
    text-transform:uppercase; letter-spacing:0.5px; margin-bottom:10px;
    display:flex; align-items:center; gap:6px;
  }
  .mention-split-label svg { width:14px; height:14px; stroke:var(--primary-green); fill:none; stroke-width:2; stroke-linecap:round; }
  .mention-val { font-size:48px; font-weight:700; line-height:1; letter-spacing:-2px; color:var(--text-primary); }

  /* View All Button */
  .do-view-all-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 14px; background:transparent;
    color:var(--primary-green); border:1.5px solid var(--primary-green);
    border-radius:8px; font-family:'Poppins',sans-serif;
    font-size:11px; font-weight:700; cursor:pointer; transition:all 0.2s;
  }
  .do-view-all-btn:hover { background:var(--primary-green); color:white; transform:translateY(-1px); }
  .do-view-all-btn svg { fill:none; stroke:currentColor; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round; width:13px; height:13px; }

  /* Modal */
  .do-modal {
    display:none; position:fixed; z-index:9999;
    left:0; top:0; width:100%; height:100%;
    background:rgba(0,0,0,0.75); backdrop-filter:blur(10px);
  }
  .do-modal.active { display:flex; align-items:center; justify-content:center; }
  .do-modal-content {
    background:#ffffff !important; border-radius:16px;
    width:90%; max-width:600px; max-height:80vh;
    box-shadow:0 20px 60px rgba(0,0,0,0.4);
    animation:mSlide 0.3s ease-out; overflow:hidden;
  }
  .do-modal-header {
    display:flex; justify-content:space-between; align-items:flex-start;
    padding:24px 28px; border-bottom:2px solid var(--border-gray);
    background:#ffffff !important; border-radius:16px 16px 0 0;
  }
  .do-modal-title    { font-size:20px; font-weight:700; color:#1a202c !important; margin:0 0 4px 0; }
  .do-modal-subtitle { font-size:12px; font-weight:500; color:#64748b !important; margin:0; }
  .do-modal-close {
    width:36px; height:36px; border-radius:10px;
    background:#ffffff !important; border:1px solid var(--border-gray);
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all 0.2s;
  }
  .do-modal-close:hover { background:#ef4444 !important; border-color:#ef4444; color:white; transform:rotate(90deg); }
  .do-modal-close svg { width:16px; height:16px; stroke:currentColor; stroke-width:2.5; stroke-linecap:round; fill:none; }
  .do-modal-body {
    padding:20px 28px 28px; max-height:calc(80vh - 100px);
    overflow-y:auto; background:#ffffff !important;
  }
  .do-modal-body::-webkit-scrollbar { width:6px; }
  .do-modal-body::-webkit-scrollbar-thumb { background:var(--border-gray); border-radius:3px; }
  .do-modal-body .do-tbl { background:#ffffff !important; }
  .do-modal-body .do-tbl thead tr { background:#ffffff !important; }
  .do-modal-body .do-tbl tbody tr { background:#ffffff !important; }
  .do-modal-body .do-tbl tbody tr:hover { background:#f8fafc !important; }

  /* Skeleton */
  .loading-skeleton {
    background:linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size:200% 100%;
    animation:shimmer 1.5s ease-in-out infinite;
    border-radius:8px;
  }
  @keyframes shimmer { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }
  .skeleton-line   { height:32px; margin-bottom:10px; border-radius:6px; }
  .skeleton-number { height:52px; width:120px; border-radius:8px; }
  .skel-overlay    { position:absolute; inset:0; z-index:5; border-radius:8px; }
  .do-card[data-loaded="true"] .do-skeleton,
  .do-card[data-loaded="true"] .skel-overlay { display:none !important; }

  /* Empty state */
  .do-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:40px 20px; gap:8px; }
  .do-empty-icon { width:40px; height:40px; color:var(--border-gray); }
  .do-empty-text { font-size:13px; font-weight:600; color:var(--text-secondary); }

  /* Map with Location Panel */
  .map-with-panel { display: flex; padding: 0; }
  .map-area { flex: 1; min-width: 0; position: relative; }
  .location-panel {
    width: 220px; flex-shrink: 0;
    border-left: 1px solid var(--border-gray);
    display: flex; flex-direction: column;
    background: var(--bg-white);
  }
  .location-panel-title {
    padding: 14px 16px 10px; font-size: 11px; font-weight: 700;
    color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;
    border-bottom: 1px solid var(--bg-gray-100);
  }
  .location-list { overflow-y: auto; flex: 1; max-height: 420px; }
  .location-list::-webkit-scrollbar { width: 4px; }
  .location-list::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 2px; }
  .location-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; cursor: pointer;
    border-bottom: 1px solid var(--bg-gray-50); transition: all 0.15s;
  }
  .location-item:hover { background: rgba(3, 128, 71, 0.06); }
  .location-item.active { background: rgba(3, 128, 71, 0.08); border-left: 3px solid var(--primary-green); padding-left: 11px; }
  .location-item-rank { font-size: 10px; font-weight: 700; color: var(--primary-green); width: 18px; flex-shrink: 0; }
  .location-item-info { flex: 1; min-width: 0; }
  .location-item-name { font-size: 12px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .location-item-count { font-size: 11px; color: var(--text-secondary); font-weight: 500; }
  .location-item-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

  /* Animations */
  @keyframes mSlide { from{transform:translateY(-20px) scale(0.95);opacity:0;} to{transform:translateY(0) scale(1);opacity:1;} }
  @keyframes fadeIn  { from{opacity:0;transform:translateY(8px);} to{opacity:1;transform:translateY(0);} }
  .data-loaded { animation:fadeIn 0.4s ease-out; }

  /* Responsive */
  @media (max-width:1100px) { .do-row-top { grid-template-columns:1fr 1fr; } }
  @media (max-width:1024px) { .do-row-mid { grid-template-columns:1fr; } }
  @media (max-width:900px) {
    .map-with-panel { flex-direction: column; }
    .location-panel { width: 100%; border-left: none; border-top: 1px solid var(--border-gray); }
    .location-list { max-height: 200px; }
  }
  @media (max-width:768px) {
    .dashboard-container { padding:16px; }
    .do-row-top { grid-template-columns:1fr; gap:12px; }
    .filter-content { flex-direction:column; align-items:stretch; }
    .date-picker-trigger { min-width:auto; }
    .apply-btn { width:100%; justify-content:center; }
    .mention-split { grid-template-columns:1fr; }
    .mention-split-item:first-child { border-right:none; border-bottom:1px solid var(--border-gray); }
    .date-picker-container { flex-direction:column; max-height:85vh; overflow-y:auto; width:95%; }
    .date-picker-sidebar { width:100%; border-right:none; border-bottom:1px solid var(--border-gray); border-radius:16px 16px 0 0; flex-direction:row; overflow-x:auto; padding:12px 16px; }
    .date-preset { white-space:nowrap; }
    .calendars-wrapper { flex-direction:column; gap:16px; }
    .cancel-btn, .apply-date-btn { flex:1; }
  }
  @media (max-width:600px) {
    .mention-val { font-size:36px; }
    .page-header h1 { font-size:22px; }
  }
  .circle-label { pointer-events: none !important; }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <div class="page-header">
    <h1>Data Overview</h1>
    <p>Ringkasan analitik sosial media dan berita</p>
  </div>

  <div class="filter-card">
    <form id="filterForm" method="GET">
      <input type="hidden" name="project_id" id="hiddenProjectId" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hiddenEndDate"   value="{{ $endDate }}">
      <div class="filter-content">
        <div class="filter-group">
          <label class="filter-label">Project</label>
          <select class="filter-select" id="doProject">
            @foreach($projects as $p)
            <option value="{{ $p['id'] }}" {{ $p['id'] == $projectId ? 'selected' : '' }}>
              {{ $p['name'] ?? $p['title'] ?? 'Project #' . $p['id'] }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="filter-group">
          <label class="filter-label">Tanggal</label>
          <button type="button" class="date-picker-trigger" id="datePickerTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="dateRangeDisplay">{{ $startDate }} to {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <div class="filter-group">
          <label class="filter-label" style="opacity:0;pointer-events:none;">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Filter
          </button>
        </div>
      </div>
    </form>
  </div>

  <!-- Date Picker Modal -->
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
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="calendar1"></div>
            <div class="calendar" id="calendar2"></div>
          </div>
          <button type="button" class="nav-btn" id="nextMonth">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
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

  <!-- ROW 1 -->
  <div class="do-row-top">

    <!-- Trending Topics -->
    <div class="do-card" data-lazy="trending-topics">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
          </span>
          <span class="do-card-title">Trending Topics</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
          <span class="do-badge">News</span>
        </div>
      </div>
      <div class="do-card-body do-body-scroll">
        <div class="do-skeleton">
          <div class="loading-skeleton skeleton-line"></div>
          <div class="loading-skeleton skeleton-line"></div>
          <div class="loading-skeleton skeleton-line"></div>
          <div class="loading-skeleton skeleton-line" style="width:70%;"></div>
        </div>
      </div>
    </div>

    <!-- Top Hashtag -->
    <div class="do-card" data-lazy="top-hashtags">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24">
              <line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/>
              <line x1="9" y1="4" x2="5" y2="20"/><line x1="15" y1="4" x2="11" y2="20"/>
            </svg>
          </span>
          <span class="do-card-title">Top Hashtag</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
          <span class="do-badge">X</span>
        </div>
      </div>
      <div class="do-card-body do-body-scroll">
        <div class="do-skeleton">
          <div class="loading-skeleton skeleton-line"></div>
          <div class="loading-skeleton skeleton-line"></div>
          <div class="loading-skeleton skeleton-line"></div>
          <div class="loading-skeleton skeleton-line" style="width:70%;"></div>
        </div>
      </div>
    </div>

    <!-- Mention -->
    <div class="do-card" data-lazy="mention-combined">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span class="do-card-title">Mention</span>
        </div>
        <span class="do-badge">All Media</span>
      </div>
      <div class="mention-split">
        <div class="mention-split-item">
          <div class="mention-split-label">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Social Media
          </div>
          <div class="mention-val" id="mentionSocialVal">
            <div class="loading-skeleton skeleton-number"></div>
          </div>
        </div>
        <div class="mention-split-item">
          <div class="mention-split-label">
            <svg viewBox="0 0 24 24">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            Online News
          </div>
          <div class="mention-val" id="mentionNewsVal">
            <div class="loading-skeleton skeleton-number"></div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- ROW 2 -->
  <div class="do-row-mid">

    <div class="do-card" data-lazy="engaged-users">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </span>
          <span class="do-card-title">Most Engaged User</span>
        </div>
        <span class="do-badge">X</span>
      </div>
      <div class="do-card-body" style="padding:16px;min-height:320px;display:flex;align-items:center;justify-content:center;position:relative;">
        <canvas id="chartDonut" style="max-width:100%;max-height:280px;"></canvas>
        <div class="skel-overlay">
          <div class="loading-skeleton" style="height:100%;border-radius:8px;"></div>
        </div>
      </div>
    </div>

    <div class="do-card" data-lazy="sentiment-timeline">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </span>
          <span class="do-card-title">Sentiment Score</span>
        </div>
        <span class="do-badge">All Media</span>
      </div>
      <div class="do-card-body" style="padding:20px;height:280px;position:relative;">
        <canvas id="chartSentiment"></canvas>
        <div class="skel-overlay">
          <div class="loading-skeleton" style="height:100%;border-radius:8px;"></div>
        </div>
      </div>
    </div>

  </div>

  <!-- ROW 3 -->
  <div class="do-card" style="margin-bottom:20px;" data-lazy="sentiment-media">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <span class="do-head-icon">
          <svg viewBox="0 0 24 24">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6"  y1="20" x2="6"  y2="14"/>
          </svg>
        </span>
        <span class="do-card-title">Sentiment by Media</span>
      </div>
      <span class="do-badge">Breakdown</span>
    </div>
    <div class="do-card-body" style="padding:24px;position:relative;">
      <p style="font-size:13px;color:var(--text-secondary);margin:0 0 20px 0;">
        Sentiment distribution across different media platforms
      </p>
      <div style="position:relative;height:300px;">
        <canvas id="chartSentimentMedia"></canvas>
        <div class="skel-overlay" style="position:absolute;inset:0;">
          <div class="loading-skeleton" style="height:100%;border-radius:8px;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ROW 4 -->
  <div class="do-card" data-lazy="buzzer-map">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <span class="do-head-icon">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="10" r="3"/>
            <path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/>
          </svg>
        </span>
        <span class="do-card-title">Buzzer Map</span>
      </div>
      <span class="do-badge">Geographic</span>
    </div>
    <div class="map-with-panel">
      <div class="map-area">
        <div id="buzzMap" style="width:100%; height:420px;"></div>
        <div id="mapSkeleton" style="position:absolute;inset:0;height:420px;border-radius:0 0 0 16px;">
          <div class="loading-skeleton" style="height:100%;border-radius:0 0 0 16px;"></div>
        </div>
      </div>
      <div class="location-panel" id="buzzMapPanel">
        <div class="location-panel-title">📍 Locations</div>
        <div class="location-list" id="buzzMapList">
          <div class="do-skeleton" style="padding:10px 14px;">
            <div class="skeleton-line" style="height:20px;margin-bottom:8px;"></div>
            <div class="skeleton-line" style="height:20px;margin-bottom:8px;"></div>
            <div class="skeleton-line" style="height:20px;margin-bottom:8px;"></div>
            <div class="skeleton-line" style="height:20px;margin-bottom:8px;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div><!-- /dashboard-container -->

<!-- Hashtag Modal -->
<div id="hashtagModal" class="do-modal">
  <div class="do-modal-content">
    <div class="do-modal-header">
      <div>
        <h3 class="do-modal-title">Top Hashtags</h3>
        <p class="do-modal-subtitle">Showing all trending hashtags</p>
      </div>
      <button class="do-modal-close" onclick="closeHashtagModal()">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="do-modal-body" id="hashtagModalBody"></div>
  </div>
</div>

<!-- Trending Topics Modal -->
<div id="trendingModal" class="do-modal">
  <div class="do-modal-content">
    <div class="do-modal-header">
      <div>
        <h3 class="do-modal-title">All Trending Topics</h3>
        <p class="do-modal-subtitle">Complete list of trending topics</p>
      </div>
      <button class="do-modal-close" onclick="closeTrendingModal()">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="do-modal-body" id="trendingModalBody"></div>
  </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ============================================================
// DATE PICKER
// ============================================================
(function () {
  'use strict';
  let ds = null, de = null;
  let m1 = new Date(), m2 = new Date();
  let pickStart = true;

  document.addEventListener('DOMContentLoaded', function () {
    const si = document.getElementById('hiddenStartDate');
    const ei = document.getElementById('hiddenEndDate');
    ds = si && si.value ? new Date(si.value) : (() => { const d=new Date(); d.setDate(d.getDate()-6); return d; })();
    de = ei && ei.value ? new Date(ei.value) : new Date();
    m1 = new Date(ds); m2 = new Date(ds); m2.setMonth(m2.getMonth()+1);
    renderCals(); setup();
  });

  function setup() {
    const t = document.getElementById('datePickerTrigger');
    if (t) t.addEventListener('click', open);
    const ov = document.querySelector('.date-picker-overlay');
    if (ov) ov.addEventListener('click', close);
    document.addEventListener('keydown', e => { if (e.key==='Escape') close(); });
    document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', preset));
    document.getElementById('prevMonth').addEventListener('click', () => { m1.setMonth(m1.getMonth()-1); m2.setMonth(m2.getMonth()-1); renderCals(); });
    document.getElementById('nextMonth').addEventListener('click', () => { m1.setMonth(m1.getMonth()+1); m2.setMonth(m2.getMonth()+1); renderCals(); });
    document.getElementById('applyDatePicker').addEventListener('click', apply);
    document.querySelector('.cancel-btn').addEventListener('click', close);
    const proj = document.getElementById('doProject');
    if (proj) proj.addEventListener('change', function(){ document.getElementById('hiddenProjectId').value=this.value; });
  }

  function open()  { document.getElementById('datePickerModal').classList.add('show'); renderCals(); }
  function close() { document.getElementById('datePickerModal').classList.remove('show'); }

  function preset(e) {
    document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
    e.target.classList.add('active');
    const today = new Date(); today.setHours(0,0,0,0);
    switch(e.target.dataset.preset) {
      case 'today':     ds=new Date(today); de=new Date(today); break;
      case 'yesterday': ds=new Date(today); ds.setDate(today.getDate()-1); de=new Date(ds); break;
      case 'last7days': de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-6); break;
      case 'last30days':de=new Date(today); ds=new Date(today); ds.setDate(today.getDate()-29); break;
      case 'thismonth': ds=new Date(today.getFullYear(),today.getMonth(),1); de=new Date(today); break;
      case 'lastmonth': ds=new Date(today.getFullYear(),today.getMonth()-1,1); de=new Date(today.getFullYear(),today.getMonth(),0); break;
    }
    if (e.target.dataset.preset!=='custom') { m1=new Date(ds); m2=new Date(ds); m2.setMonth(m2.getMonth()+1); updateDisp(); renderCals(); }
  }

  function apply() {
    document.getElementById('hiddenStartDate').value=fmt(ds);
    document.getElementById('hiddenEndDate').value=fmt(de);
    document.getElementById('dateRangeDisplay').textContent=`${fmt(ds)} to ${fmt(de)}`;
    close();
  }

  function renderCals() { renderCal('calendar1',m1); renderCal('calendar2',m2); updateDisp(); }

  function renderCal(id, month) {
    const el=document.getElementById(id); if(!el) return;
    const y=month.getFullYear(), mn=month.getMonth();
    const first=new Date(y,mn,1), last=new Date(y,mn+1,0), prevL=new Date(y,mn,0);
    const mN=['January','February','March','April','May','June','July','August','September','October','November','December'];
    const wD=['Su','Mo','Tu','We','Th','Fr','Sa'];
    const today=new Date(); today.setHours(0,0,0,0);
    let h=`<div class="calendar-month">${mN[mn]} ${y}</div>
      <div class="calendar-weekdays">${wD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div>
      <div class="calendar-days">`;
    for(let i=0;i<first.getDay();i++) h+=`<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for(let d=1;d<=last.getDate();d++){
      const date=new Date(y,mn,d); date.setHours(0,0,0,0);
      let cls='calendar-day';
      if(sD(date,today)) cls+=' today';
      if(date>today) cls+=' disabled';
      if(ds&&de){ if(sD(date,ds)) cls+=' selected range-start'; else if(sD(date,de)) cls+=' selected range-end'; else if(date>ds&&date<de) cls+=' in-range'; }
      h+=`<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem=last.getDay()===6?0:6-last.getDay();
    for(let i=1;i<=rem;i++) h+=`<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h+='</div>'; el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn=>{
      btn.addEventListener('click',function(){
        const d=new Date(this.dataset.date); d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-preset="custom"]').classList.add('active');
        if(pickStart||d<ds){ ds=d; de=d; pickStart=false; }
        else { if(d>=ds) de=d; else { de=ds; ds=d; } pickStart=true; }
        updateDisp(); renderCals();
      });
    });
  }

  function updateDisp() { const el=document.getElementById('selectedRangeText'); if(el&&ds&&de) el.textContent=`${fmt(ds)} to ${fmt(de)}`; }
  function fmt(d) { if(!d) return ''; return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
  function sD(a,b) { return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
})();

// ============================================================
// LAZY LOADER
// ============================================================
const DataOverviewLazyLoader = {
  projectId: {{ $projectId ?? 'null' }},
  startDate: '{{ $startDate }}',
  endDate:   '{{ $endDate }}',
  loaded: new Set(),
  _allTopics: [],
  _allHashtags: [],

  init() {
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const card = entry.target, sec = card.dataset.lazy;
          if (!this.loaded.has(sec)) { this.loaded.add(sec); this.load(sec, card); obs.unobserve(card); }
        }
      });
    }, { rootMargin:'100px', threshold:0.05 });
    document.querySelectorAll('[data-lazy]').forEach(c => obs.observe(c));
  },

  async load(sec, card) {
    try {
      switch(sec) {
        case 'trending-topics':    await this.loadTrending(card); break;
        case 'top-hashtags':       await this.loadHashtags(card); break;
        case 'mention-combined':   await this.loadMentions(card); break;
        case 'engaged-users':      await this.loadEngaged(card);  break;
        case 'sentiment-timeline': await this.loadSentLine(card); break;
        case 'sentiment-media':    await this.loadSentMedia(card);break;
        case 'buzzer-map':         await this.loadMap(card);      break;
      }
      card.dataset.loaded = 'true';
    } catch(err) { console.error(`Error loading ${sec}:`, err); this.errState(card); }
  },

  async loadTrending(card) {
    const r = await fetch(`/mk/api/trending-topics`);
    const d = await r.json();
    const body = card.querySelector('.do-card-body');
    const topics = d.data || [];
    if (!topics.length) { body.innerHTML = this.empty(); return; }
    this._allTopics = topics;
    if (topics.length > 10) {
      const right = card.querySelector('.do-card-head > div:last-child');
      right.innerHTML += `<button class="do-view-all-btn" onclick="DataOverviewLazyLoader.openTrendingModal()">
        <svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>View All</button>`;
    }
    let h = `<table class="do-tbl"><thead><tr><th style="width:24px;">#</th><th>Topic</th></tr></thead><tbody>`;
    topics.slice(0,10).forEach((t,i) => {
      const topicName = t.title || t.name || t.topic || 'Unknown';
      const url = t.reference || t.url || (t.urls && t.urls[0]) || '#';
      const esc = topicName.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      h += `<tr>
        <td class="do-tbl-rank">${i+1}</td>
        <td class="do-tbl-name" style="max-width:400px;">
          ${url !== '#' ? `<a href="${url}" target="_blank" rel="noopener noreferrer" class="topic-link">${esc}</a>` : esc}
        </td>
      </tr>`;
    });
    h += '</tbody></table>';
    body.innerHTML = h; body.classList.add('data-loaded');
  },

  openTrendingModal() {
    let h = `<table class="do-tbl"><thead><tr><th style="width:40px;">#</th><th>Topic</th></tr></thead><tbody>`;
    this._allTopics.forEach((t,i) => {
      const topicName = t.title || t.name || t.topic || 'Unknown';
      const url = t.reference || t.url || (t.urls && t.urls[0]) || '#';
      const esc = topicName.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      h += `<tr>
        <td class="do-tbl-rank">${i+1}</td>
        <td class="do-tbl-name" style="max-width:480px;">
          ${url !== '#' ? `<a href="${url}" target="_blank" rel="noopener noreferrer" class="topic-link">${esc}</a>` : esc}
        </td>
      </tr>`;
    });
    h += '</tbody></table>';
    document.getElementById('trendingModalBody').innerHTML = h;
    document.getElementById('trendingModal').classList.add('active');
    document.body.style.overflow = 'hidden';
  },

  async loadHashtags(card) {
    const r = await fetch(`/mk/api/top-hashtags?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
    const d = await r.json();
    const body = card.querySelector('.do-card-body');

    // ✅ FIX: struktur response adalah d.data.hashtags bukan d.data langsung
    let tags = [];
    if (d.data && Array.isArray(d.data.hashtags)) {
      tags = d.data.hashtags;
    } else if (Array.isArray(d.data)) {
      tags = d.data;
    }

    if (!tags.length) { body.innerHTML = this.empty(); return; }

    // Simpan semua untuk modal
    this._allHashtags = tags;

    if (tags.length > 5) {
      const right = card.querySelector('.do-card-head > div:last-child');
      right.innerHTML += `<button class="do-view-all-btn" onclick="DataOverviewLazyLoader.openHashtagModal()">
        <svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>View All</button>`;
    }

    let h = `<table class="do-tbl"><thead><tr><th style="width:24px;">#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>`;
    tags.slice(0,5).forEach((tag,i) => {
      let name = tag.name || tag.hashtag || tag.tag || 'unknown';
      if (!name.startsWith('#')) name = '#' + name;
      const count = parseInt(tag.size || tag.mention || tag.count || 0);
      h += `<tr>
        <td class="do-tbl-rank">${i+1}</td>
        <td class="do-tbl-name" style="color:var(--primary-green);font-weight:700;">${name}</td>
        <td class="do-tbl-num">${count.toLocaleString()}</td>
      </tr>`;
    });
    h += '</tbody></table>';
    body.innerHTML = h; body.classList.add('data-loaded');
  },

  openHashtagModal() {
    const tags = this._allHashtags || [];
    let h = `<table class="do-tbl"><thead><tr><th style="width:40px;">#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>`;
    tags.forEach((tag,i) => {
      let name = tag.name || tag.hashtag || tag.tag || 'unknown';
      if (!name.startsWith('#')) name = '#' + name;
      const count = parseInt(tag.size || tag.mention || tag.count || 0);
      h += `<tr>
        <td class="do-tbl-rank">${i+1}</td>
        <td class="do-tbl-name" style="color:var(--primary-green);font-weight:700;">${name}</td>
        <td class="do-tbl-num">${count.toLocaleString()}</td>
      </tr>`;
    });
    h += '</tbody></table>';
    document.getElementById('hashtagModalBody').innerHTML = h;
    document.getElementById('hashtagModal').classList.add('active');
    document.body.style.overflow = 'hidden';
  },

  async loadMentions(card) {
    const r = await fetch(`/mk/api/mention-counts?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
    const d = await r.json();
    const sEl = document.getElementById('mentionSocialVal');
    const nEl = document.getElementById('mentionNewsVal');
    if (sEl) { sEl.innerHTML = Number(d.social||0).toLocaleString(); sEl.classList.add('data-loaded'); }
    if (nEl) { nEl.innerHTML = Number(d.news||0).toLocaleString();   nEl.classList.add('data-loaded'); }
  },

  async loadEngaged(card) {
    const r = await fetch(`/mk/api/active-users?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
    const d = await r.json();
    const users = d.data || [];
    const wrap  = card.querySelector('.do-card-body');
    if (!users.length) { wrap.innerHTML = this.empty('No active user data available'); return; }
    const labels = users.map(u=>'@'+u.username);
    const counts = users.map(u=>u.count);
    const colors = ['#038047','#0284c7','#7c3aed','#ea580c','#ef4444','#16a34a','#0369a1'];
    this.renderDonut(labels, counts, colors);
  },

  async loadSentLine(card) {
    const r = await fetch(`/mk/api/sentiment-timeline?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
    const d = await r.json();
    this.renderLine(d);
  },

  async loadSentMedia(card) {
    const r    = await fetch(`/mk/api/sentiment-by-media?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
    const d    = await r.json();
    const data = d.data || [];
    if (!data.length) { const c=card.querySelector('canvas'); if(c) c.closest('[style*="height"]').innerHTML=this.empty('No sentiment data available'); return; }
    this.renderMediaBar(data);
  },

  async loadMap(card) {
    const r  = await fetch(`/mk/api/geo-users?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
    const d  = await r.json();
    const sk = document.getElementById('mapSkeleton');
    if (sk) sk.style.display='none';
    const rows = d.data || [];
    const mapResult = this.renderMap('buzzMap', rows);
    this.buildLocationPanel('buzzMapList', rows, mapResult, '#038047');
  },

  renderMap(elementId, rows) {
    const map = L.map(elementId, { center:[-2.5,118], zoom:5, scrollWheelZoom:false });
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
      attribution: '© OpenStreetMap, © CARTO',
      subdomains: 'abcd', maxZoom: 19
    }).addTo(map);
    if (!rows.length) return { map, markerRefs: [] };
    const max = Math.max(...rows.map(p=>parseInt(p.count||0)));
    const markerRefs = [];
    rows.forEach(p => {
      const lat = parseFloat(p.latitude||0), lng = parseFloat(p.longitude||0);
      if (lat===0 && lng===0) { markerRefs.push(null); return; }
      const name  = p.name || 'Unknown';
      const count = parseInt(p.count || 0);
      if (count >= 10) {
        let r = Math.sqrt(count) * 2500;
        r = Math.max(5000, Math.min(r, 50000));
        L.circle([lat,lng],{ radius:r, fillColor:'#038047', color:'#038047', weight:1, opacity:0.2, fillOpacity:Math.min(0.15+(count/max)*0.4,0.55) }).addTo(map);
      }
      const pin = L.marker([lat,lng],{ icon:L.divIcon({ className:'', html:'<div style="width:13px;height:13px;background:#038047;border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>', iconSize:[13,13], iconAnchor:[6.5,6.5] })}).addTo(map)
        .bindPopup(`<div style="font-family:Poppins;text-align:center;padding:8px;"><div style="font-weight:700;font-size:15px;color:#1a202c;margin-bottom:6px;">${name}</div><div style="font-size:24px;font-weight:700;color:#038047;margin-bottom:2px;">${count.toLocaleString()}</div><div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:0.8px;font-weight:600;">mentions</div></div>`);
      markerRefs.push({ marker: pin, lat, lng });
      const lbl = count>999 ? (count/1000).toFixed(1)+'k' : count;
      L.marker([lat,lng],{ icon:L.divIcon({ className:'circle-label', html:`<div style="font-family:Poppins;font-size:11px;font-weight:800;color:#fff;background:rgba(3,128,71,0.92);padding:3px 8px;border-radius:12px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.4);white-space:nowrap;">${lbl}</div>`, iconSize:[40,20], iconAnchor:[20,25] }), interactive:false }).addTo(map);
    });
    return { map, markerRefs };
  },

  buildLocationPanel(listId, rows, mapResult, color) {
    const listEl = document.getElementById(listId);
    if (!listEl) return;
    const { map, markerRefs } = mapResult;
    const validRows = rows.filter(p => {
      const lat = parseFloat(p.latitude||0), lng = parseFloat(p.longitude||0);
      return !(lat===0 && lng===0);
    });
    if (!validRows.length) {
      listEl.innerHTML = '<div class="do-empty" style="padding:24px 14px;font-size:12px;">No location data</div>';
      return;
    }
    const sorted = [...validRows].sort((a,b) => parseInt(b.count||0) - parseInt(a.count||0));
    let h = '';
    sorted.forEach((p,rank) => {
      const name  = p.name || 'Unknown';
      const count = parseInt(p.count || 0);
      const label = count>999 ? (count/1000).toFixed(1)+'k' : count;
      h += `<div class="location-item" data-name="${name}">
        <span class="location-item-rank">${rank+1}</span>
        <div class="location-item-info">
          <div class="location-item-name" title="${name}">${name}</div>
          <div class="location-item-count">${label} mentions</div>
        </div>
        <div class="location-item-dot" style="background:${color};"></div>
      </div>`;
    });
    listEl.innerHTML = h;
    listEl.querySelectorAll('.location-item').forEach(item => {
      item.addEventListener('click', () => {
        const name = item.dataset.name;
        const targetRow = validRows.find(p => (p.name||'Unknown') === name);
        if (!targetRow) return;
        const lat = parseFloat(targetRow.latitude||0);
        const lng = parseFloat(targetRow.longitude||0);
        if (lat===0 && lng===0) return;
        map.flyTo([lat, lng], 8, { animate:true, duration:1 });
        const ref = markerRefs.find(r => r && Math.abs(r.lat-lat)<0.001 && Math.abs(r.lng-lng)<0.001);
        if (ref) setTimeout(() => ref.marker.openPopup(), 800);
        listEl.querySelectorAll('.location-item').forEach(i => i.classList.remove('active'));
        item.classList.add('active');
      });
    });
  },

  renderDonut(labels, counts, colors) {
    const plugin = { id:'extLabel', afterDatasetsDraw(chart) {
      const ctx=chart.ctx, meta=chart.getDatasetMeta(0);
      const cx=chart.chartArea.left+(chart.chartArea.right-chart.chartArea.left)/2;
      const cy=chart.chartArea.top+(chart.chartArea.bottom-chart.chartArea.top)/2;
      const or=meta.data[0]?meta.data[0].outerRadius:0;
      ctx.save(); ctx.textBaseline='middle';
      let pos=[];
      meta.data.forEach((sl,i)=>{
        if(!sl||sl.circumference===0) return;
        const a=(sl.startAngle+sl.endAngle)/2, isR=Math.cos(a)>=0;
        const lx=cx+(or+32)*Math.cos(a), ly=cy+(or+32)*Math.sin(a);
        pos.push({ ex:cx+or*Math.cos(a), ey:cy+or*Math.sin(a), lx, ly, ex2:isR?lx+38:lx-38,
          label:labels[i]||'', cnt:(counts[i]||0).toLocaleString(), color:colors[i%colors.length], isR });
      });
      pos.sort((a,b)=>a.ly-b.ly);
      for(let i=1;i<pos.length;i++) if(Math.abs(pos[i].ly-pos[i-1].ly)<26) pos[i].ly=pos[i-1].ly+26;
      pos.forEach(p=>{
        ctx.strokeStyle=p.color; ctx.lineWidth=1.5;
        ctx.beginPath(); ctx.moveTo(p.ex,p.ey); ctx.lineTo(p.lx,p.ly); ctx.lineTo(p.ex2,p.ly); ctx.stroke();
        ctx.fillStyle=p.color; ctx.beginPath(); ctx.arc(p.ex2,p.ly,3,0,Math.PI*2); ctx.fill();
        const tx=p.isR?p.ex2+7:p.ex2-7; ctx.textAlign=p.isR?'left':'right';
        ctx.fillStyle='#1a202c'; ctx.font='700 11px Poppins,sans-serif'; ctx.fillText(p.label,tx,p.ly-7);
        ctx.fillStyle='#64748b'; ctx.font='500 10px Poppins,sans-serif'; ctx.fillText('('+p.cnt+' twits)',tx,p.ly+7);
      });
      ctx.restore();
    }};
    new Chart(document.getElementById('chartDonut').getContext('2d'), {
      type:'doughnut', plugins:[plugin],
      data:{ labels, datasets:[{ data:counts, backgroundColor:colors, borderColor:'#fff', borderWidth:3, hoverOffset:8 }] },
      options:{
        responsive:true, maintainAspectRatio:true, aspectRatio:1.3, cutout:'58%',
        layout:{ padding:{ top:28, right:120, bottom:28, left:120 } },
        plugins:{
          legend:{ display:false },
          tooltip:{ backgroundColor:'#1a202c', titleColor:'#fff', bodyColor:'#fff', borderColor:'rgba(226,232,240,0.3)', borderWidth:1, cornerRadius:10, padding:14,
            titleFont:{ size:13, weight:'700', family:'Poppins' }, bodyFont:{ size:12, family:'Poppins' },
            callbacks:{ label: ctx=>' '+ctx.parsed.toLocaleString()+' tweets' } }
        },
        animation:{ animateRotate:true, duration:900 }
      }
    });
  },

  renderLine(data) {
    new Chart(document.getElementById('chartSentiment').getContext('2d'), {
      type:'line',
      data:{ labels:data.dates, datasets:[
        { label:'Total',    data:data.values, borderColor:'#3b82f6', backgroundColor:'rgba(59,130,246,0.1)', borderWidth:2.5, tension:0.4, fill:true, pointRadius:4, pointBackgroundColor:'#3b82f6', pointBorderColor:'#fff', pointBorderWidth:2 },
        { label:'Positive', data:data.sentiment.positive, borderColor:'#22c55e', backgroundColor:'transparent', borderWidth:2, tension:0.4, fill:false, pointRadius:3 },
        { label:'Neutral',  data:data.sentiment.neutral,  borderColor:'#94a3b8', backgroundColor:'transparent', borderWidth:1.5, tension:0.4, fill:false, pointRadius:2 },
        { label:'Negative', data:data.sentiment.negative, borderColor:'#ef4444', backgroundColor:'transparent', borderWidth:1.5, tension:0.4, fill:false, pointRadius:2 }
      ]},
      options:{
        responsive:true, maintainAspectRatio:false,
        interaction:{ intersect:false, mode:'index' },
        plugins:{
          legend:{ display:true, position:'bottom', align:'start', labels:{ usePointStyle:true, pointStyle:'circle', padding:12, boxWidth:8, boxHeight:8, font:{ size:11, weight:'600', family:'Poppins' }, color:'#64748b' }},
          tooltip:{ backgroundColor:'#1a202c', titleColor:'#fff', bodyColor:'#fff', borderColor:'rgba(226,232,240,0.3)', borderWidth:1, padding:14, cornerRadius:10,
            titleFont:{ size:12, weight:'bold', family:'Poppins' }, bodyFont:{ size:11, family:'Poppins' },
            callbacks:{ label: ctx=>' '+ctx.dataset.label+': '+ctx.parsed.y.toLocaleString() } }
        },
        scales:{
          x:{ grid:{ display:false }, ticks:{ font:{ size:10, family:'Poppins' }, color:'#94a3b8', maxTicksLimit:7 }},
          y:{ beginAtZero:true, grid:{ color:'rgba(0,0,0,0.04)' }, ticks:{ font:{ size:10, family:'Poppins' }, color:'#94a3b8', callback:v=>v>=1000?(v/1000)+'k':v, maxTicksLimit:5 }}
        }
      }
    });
  },

  renderMediaBar(data) {
    const ctx = document.getElementById('chartSentimentMedia'); if(!ctx) return;
    new Chart(ctx.getContext('2d'), {
      type:'bar',
      data:{ labels:data.map(d=>d.media), datasets:[
        { label:'Positive', data:data.map(d=>d.positive), backgroundColor:'#22c55e', borderRadius:8, barThickness:28, borderWidth:0 },
        { label:'Negative', data:data.map(d=>d.negative), backgroundColor:'#ef4444', borderRadius:8, barThickness:28, borderWidth:0 }
      ]},
      options:{
        indexAxis:'y', responsive:true, maintainAspectRatio:false,
        interaction:{ intersect:false, mode:'index' },
        plugins:{
          legend:{ display:true, position:'top', align:'end', labels:{ usePointStyle:true, pointStyle:'circle', padding:16, boxWidth:10, boxHeight:10, font:{ size:12, weight:'700', family:'Poppins' }, color:'#1a202c' }},
          tooltip:{ backgroundColor:'#1a202c', titleColor:'#fff', bodyColor:'#fff', borderColor:'rgba(226,232,240,0.3)', borderWidth:1, padding:14, cornerRadius:10,
            titleFont:{ size:13, weight:'700', family:'Poppins' }, bodyFont:{ size:12, family:'Poppins' },
            callbacks:{ label(ctx) { const item=data[ctx.dataIndex]; const pct=ctx.dataset.label==='Positive'?item.positive_percentage:item.negative_percentage; return ` ${ctx.dataset.label}: ${ctx.parsed.x.toLocaleString()} (${pct}%)`; }}}
        },
        scales:{
          x:{ beginAtZero:true, grid:{ color:'rgba(226,232,240,0.5)', drawBorder:false }, ticks:{ font:{ size:11, family:'Poppins' }, color:'#64748b', padding:8, callback:v=>v>=1000?(v/1000)+'k':v }},
          y:{ grid:{ display:false }, ticks:{ font:{ size:12, weight:'700', family:'Poppins' }, color:'#1a202c', padding:10 }}
        },
        animation:{ duration:750, easing:'easeInOutQuart' }
      }
    });
  },

  empty(msg='Tidak ada data') {
    return `<div class="do-empty"><svg class="do-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="do-empty-text">${msg}</span></div>`;
  },
  errState(card) { const b=card.querySelector('.do-card-body'); if(b) b.innerHTML=this.empty('Failed to load data'); }
};

function closeHashtagModal() { document.getElementById('hashtagModal').classList.remove('active'); document.body.style.overflow='auto'; }
function closeTrendingModal() { document.getElementById('trendingModal').classList.remove('active'); document.body.style.overflow='auto'; }

window.addEventListener('click', e => {
  const hm=document.getElementById('hashtagModal');
  if(e.target===hm) closeHashtagModal();
  const tm=document.getElementById('trendingModal');
  if(e.target===tm) closeTrendingModal();
});

document.addEventListener('keydown', e => {
  if(e.key==='Escape'){
    const hm=document.getElementById('hashtagModal');
    if(hm&&hm.classList.contains('active')) closeHashtagModal();
    const tm=document.getElementById('trendingModal');
    if(tm&&tm.classList.contains('active')) closeTrendingModal();
  }
});

document.addEventListener('DOMContentLoaded', () => DataOverviewLazyLoader.init());
</script>
@endsection