@extends('mk.layouts.app')

@section('title', 'Mentions Timeline - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047; --primary-green-dark: #026738;
    --text-primary: #1a202c; --text-secondary: #64748b;
    --bg-white: #ffffff; --bg-gray-50: #f8fafc; --bg-gray-100: #f1f5f9;
    --border-gray: #e2e8f0; --shadow-sm: 0 1px 2px 0 rgba(0,0,0,.05);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1); --r: 16px; --rs: 12px;
  }
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  .dashboard-container { padding: 24px; background: var(--bg-gray-50); min-height: 100vh; }
  .page-header { margin-bottom: 28px; }
  .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; }
  .page-header p  { font-size: 14px; color: var(--text-secondary); font-weight: 500; margin: 0; }
  .filter-card { background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: var(--r); padding: 20px 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm); }
  .filter-content { display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
  .filter-group { display: flex; flex-direction: column; gap: 6px; }
  .filter-label { font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .5px; }
  .date-trigger { display: flex; align-items: center; gap: 10px; padding: 10px 16px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: var(--rs); font-size: 14px; font-weight: 500; color: var(--text-primary); cursor: pointer; transition: all .2s; min-width: 300px; }
  .date-trigger:hover { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(3,128,71,.1); }
  .date-trigger span { flex: 1; text-align: left; }
  .apply-btn { display: flex; align-items: center; gap: 8px; padding: 10px 24px; background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark)); color: #fff; border: none; border-radius: var(--rs); font-size: 14px; font-weight: 600; cursor: pointer; transition: all .3s; box-shadow: 0 4px 12px rgba(3,128,71,.2); white-space: nowrap; }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.3); }

  /* DATE PICKER */
  .date-picker-modal { position: fixed; top:0; left:0; right:0; bottom:0; z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); }
  .date-picker-modal.show { display: flex; }
  .date-picker-overlay { position:absolute; top:0; left:0; right:0; bottom:0; cursor:pointer; }
  .date-picker-container { position: relative; background: #fff; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); display: flex; max-width: 900px; width: 90%; max-height: 90vh; z-index: 10001; animation: dpUp .3s ease-out; }
  @keyframes dpUp { from{opacity:0;transform:translateY(20px) scale(.95)} to{opacity:1;transform:translateY(0) scale(1)} }
  .date-picker-sidebar { width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray); padding: 16px 12px; border-radius: 16px 0 0 16px; display: flex; flex-direction: column; gap: 4px; flex-shrink: 0; }
  .date-preset { padding: 10px 16px; background: transparent; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; color: var(--text-primary); text-align: left; cursor: pointer; transition: all .2s; }
  .date-preset:hover { background: var(--bg-white); color: var(--primary-green); }
  .date-preset.active { background: var(--primary-green); color: white; }
  .date-picker-content { flex:1; padding:24px; display:flex; flex-direction:column; overflow:hidden; }
  .date-picker-header { display:flex; align-items:flex-start; gap:20px; margin-bottom:20px; }
  .nav-btn { width:36px; height:36px; border-radius:8px; background:var(--bg-gray-50); border:1px solid var(--border-gray); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; flex-shrink:0; }
  .nav-btn:hover { background:var(--primary-green); border-color:var(--primary-green); color:white; }
  .nav-btn svg { width:20px; height:20px; }
  .calendars-wrapper { display:flex; gap:24px; flex:1; min-height:0; }
  .calendar { flex:1; display:flex; flex-direction:column; min-width:0; }
  .calendar-month { font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:16px; text-align:center; }
  .calendar-weekdays { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:8px; }
  .weekday { text-align:center; font-size:11px; font-weight:700; color:var(--text-secondary); padding:8px 0; }
  .calendar-days { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; }
  .calendar-day { aspect-ratio:1; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:500; border-radius:8px; cursor:pointer; transition:all .2s; color:var(--text-primary); background:transparent; border:none; padding:0; }
  .calendar-day:hover:not(.disabled):not(.other-month) { background:var(--bg-gray-100); }
  .calendar-day.other-month { color:#cbd5e1; cursor:default; }
  .calendar-day.disabled { color:#e2e8f0; cursor:not-allowed; }
  .calendar-day.today { border:2px solid var(--primary-green); }
  .calendar-day.selected,.calendar-day.range-start,.calendar-day.range-end { background:var(--primary-green); color:white; }
  .calendar-day.in-range { background:rgba(3,128,71,.1); color:var(--primary-green); }
  .date-picker-display { padding:16px 20px; background:var(--bg-gray-50); border-radius:12px; text-align:center; margin-bottom:20px; border:1px solid var(--border-gray); }
  .date-picker-display span { font-size:14px; font-weight:600; color:var(--text-primary); }
  .date-picker-footer { display:flex; gap:12px; justify-content:flex-end; }
  .cancel-btn,.apply-date-btn { padding:10px 24px; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; transition:all .2s; border:none; }
  .cancel-btn { background:var(--bg-gray-100); color:var(--text-primary); }
  .cancel-btn:hover { background:var(--border-gray); }
  .apply-date-btn { background:linear-gradient(135deg, var(--primary-green), var(--primary-green-dark)); color:white; }
  .apply-date-btn:hover { transform:translateY(-2px); }

  /* DO-CARD */
  .do-card { background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: var(--r); overflow: hidden; box-shadow: var(--shadow-sm); transition: all .3s; position: relative; margin-bottom: 20px; }
  .do-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, var(--primary-green), var(--primary-green-dark)); opacity:0; transition:opacity .3s; }
  .do-card:hover { box-shadow: var(--shadow-lg); border-color: rgba(3,128,71,.25); }
  .do-card:hover::before { opacity: 1; }
  .do-card-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-gray); flex-wrap:wrap; gap:12px; }
  .do-card-head-left { display:flex; align-items:center; gap:12px; }
  .do-head-icon { width:40px; height:40px; border-radius:12px; background:linear-gradient(135deg,rgba(3,128,71,.1),rgba(3,128,71,.05)); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .do-head-icon svg { width:20px; height:20px; fill:none; stroke:var(--primary-green); stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
  .do-card-title { font-size:16px; font-weight:700; color:var(--text-primary); }
  .do-badge { display:inline-flex; align-items:center; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; background:var(--bg-gray-100); color:var(--text-secondary); }

  /* PLATFORM LOADING BAR */
  .platform-loading-bar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; padding:10px 20px; background:var(--bg-gray-50); border-bottom:1px solid var(--border-gray); font-size:12px; }
  .plb-label { font-weight:600; color:var(--text-secondary); margin-right:4px; }
  .plb-item { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; border:1px solid; transition:all .3s; }
  .plb-item.loading { background:var(--bg-gray-100); color:var(--text-secondary); border-color:var(--border-gray); }
  .plb-item.done { background:#d1fae5; color:#065f46; border-color:#6ee7b7; }
  .plb-item.error { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }
  .plb-spinner { display:inline-block; width:8px; height:8px; border:1.5px solid currentColor; border-top-color:transparent; border-radius:50%; animation:spin .7s linear infinite; }

  /* OVERVIEW */
  .overview-body { display:grid; grid-template-columns:1fr 1px 1fr; min-height:240px; }
  .overview-panel { padding:24px 28px; display:flex; flex-direction:column; justify-content:center; }
  .overview-divider { background:var(--border-gray); }
  .overview-title { font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.6px; margin-bottom:18px; display:flex; align-items:center; gap:8px; }
  .overview-title-dot { width:8px; height:8px; border-radius:50%; }
  .donut-row { display:flex; align-items:center; gap:20px; }
  .donut-canvas-wrap { flex-shrink:0; position:relative; width:150px; height:150px; }
  .donut-center-text { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; pointer-events:none; }
  .donut-center-num { font-size:19px; font-weight:800; color:var(--text-primary); line-height:1; }
  .donut-center-lbl { font-size:9px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.5px; margin-top:3px; }
  .donut-legend { display:flex; flex-direction:column; gap:7px; flex:1; }
  .dl-item { display:flex; align-items:center; gap:8px; }
  .dl-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
  .dl-name { font-size:12px; font-weight:600; color:var(--text-secondary); flex:1; }
  .dl-val { font-size:13px; font-weight:800; color:var(--text-primary); }
  .dl-pct { font-size:10px; font-weight:600; color:var(--text-secondary); min-width:34px; text-align:right; }
  .progress-bar-wrap { height:3px; background:var(--bg-gray-100); overflow:hidden; }
  .progress-bar { height:100%; background:linear-gradient(90deg, var(--primary-green), #34d399); width:0%; transition:width .4s ease; }

  /* CHART */
  .chart-controls { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
  .chart-toggle { display:flex; background:var(--bg-gray-100); border-radius:10px; padding:3px; gap:2px; }
  .ct-btn { padding:5px 14px; border:none; border-radius:8px; font-size:11px; font-weight:700; cursor:pointer; transition:all .2s; color:var(--text-secondary); background:transparent; }
  .ct-btn.active { background:var(--bg-white); color:var(--text-primary); box-shadow:0 1px 3px rgba(0,0,0,.1); }
  .legend { display:flex; flex-wrap:wrap; gap:12px; align-items:center; }
  .legend-item { display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:var(--text-secondary); cursor:pointer; transition:opacity .2s; user-select:none; }
  .legend-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }
  .legend-cnt { font-size:10px; font-weight:800; padding:1px 7px; border-radius:10px; }
  .chart-wrap { position:relative; height:300px; }
  .chart-click-hint { display:flex; align-items:center; gap:6px; padding:6px 14px; background:rgba(3,128,71,.06); border:1px solid rgba(3,128,71,.15); border-radius:20px; font-size:11px; font-weight:600; color:var(--primary-green); }
  .chart-click-hint svg { width:12px; height:12px; }

  /* TABLE */
  .table-head { display:flex; justify-content:space-between; align-items:center; padding:18px 22px; border-bottom:1px solid var(--border-gray); flex-wrap:wrap; gap:12px; }
  .table-head-info h3 { font-size:16px; font-weight:700; color:var(--text-primary); }
  .table-head-info p { font-size:12px; color:var(--text-secondary); margin-top:3px; font-weight:500; }
  .search-box { position:relative; }
  .search-box input { width:260px; padding:9px 14px 9px 38px; border:1px solid var(--border-gray); border-radius:20px; font-size:13px; font-weight:500; background:var(--bg-gray-50); color:var(--text-primary); transition:all .2s; }
  .search-box input:focus { outline:none; border-color:var(--primary-green); background:var(--bg-white); box-shadow:0 0 0 3px rgba(3,128,71,.1); }
  .search-box svg { position:absolute; left:12px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--text-secondary); }

  /* TABS */
  .media-tabs { display:flex; gap:6px; flex-wrap:wrap; padding:14px 22px 0; border-bottom:1px solid var(--border-gray); }
  .media-tab { display:flex; align-items:center; gap:6px; padding:8px 16px; background:transparent; border:none; border-bottom:2px solid transparent; font-size:13px; font-weight:600; color:var(--text-secondary); cursor:pointer; transition:all .2s; margin-bottom:-1px; }
  .media-tab:hover { color:var(--primary-green); }
  .media-tab.active { color:var(--primary-green); border-bottom-color:var(--primary-green); }
  .tab-cnt { padding:2px 8px; border-radius:10px; font-size:10px; font-weight:800; background:var(--bg-gray-100); color:var(--text-secondary); }
  .media-tab.active .tab-cnt { background:rgba(3,128,71,.1); color:var(--primary-green); }
  .tab-spinner { display:inline-block; width:10px; height:10px; border:2px solid var(--border-gray); border-top-color:var(--primary-green); border-radius:50%; animation:spin .7s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* MENTIONS TABLE */
  .mentions-table { width:100%; border-collapse:collapse; }
  .mentions-table thead { background:var(--bg-gray-50); border-bottom:1.5px solid var(--border-gray); }
  .mentions-table th { padding:11px 14px; text-align:left; font-size:10px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.7px; white-space:nowrap; }
  .mentions-table th.c { text-align:center; }
  .mentions-table td { padding:13px 14px; border-bottom:1px solid var(--bg-gray-100); font-size:13px; color:var(--text-primary); vertical-align:middle; }
  .mentions-table tr:hover td { background:var(--bg-gray-50); }
  .mentions-table tr:last-child td { border-bottom:none; }
  .no-cell { width:42px; text-align:center; font-weight:700; color:var(--text-secondary); font-size:12px; }
  .media-cell { width:90px; text-align:center; }
  .media-badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:10px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; }
  .mb-doc { background:#dbeafe; color:#1e40af; border:1px solid #93c5fd; }
  .mb-twit { background:#e0f2fe; color:#0369a1; border:1px solid #7dd3fc; }
  .mb-fb { background:#e0e7ff; color:#3730a3; border:1px solid #a5b4fc; }
  .mb-ig { background:#fce7f3; color:#be185d; border:1px solid #f9a8d4; }
  .mb-ytb { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
  .mb-tiktok { background:var(--bg-gray-100); color:#374151; border:1px solid var(--border-gray); }
  .type-badge { display:inline-block; padding:2px 8px; border-radius:6px; font-size:10px; font-weight:700; background:var(--bg-gray-100); color:var(--text-secondary); }
  .content-cell { max-width:440px; }
  .mention-text { font-size:13px; line-height:1.55; color:var(--text-primary); display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:5px; }
  .mention-meta { display:flex; align-items:center; gap:8px; font-size:11px; color:var(--text-secondary); flex-wrap:wrap; }
  .view-link { color:var(--primary-green); font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:3px; font-size:11px; transition:all .15s; cursor:pointer; border:none; background:none; padding:0; font-family:inherit; }
  .view-link:hover { color:var(--primary-green-dark); text-decoration:underline; }
  .eng-cell { text-align:center; min-width:100px; vertical-align:middle; }
  .eng-primary-val { font-weight:800; font-size:15px; color:var(--text-primary); line-height:1; }
  .eng-primary-lbl { font-size:10px; font-weight:600; color:var(--text-secondary); margin-bottom:4px; }
  .eng-secondary { display:flex; flex-direction:column; gap:2px; margin-top:2px; }
  .eng-sec-item { font-size:10px; font-weight:600; color:var(--text-secondary); white-space:nowrap; }
  .eng-empty { color:var(--border-gray); font-size:20px; font-weight:300; }
  .author-cell { min-width:170px; }
  .author-wrap { display:flex; align-items:center; gap:10px; }
  .ava { width:38px; height:38px; border-radius:50%; border:2px solid var(--border-gray); flex-shrink:0; background:linear-gradient(135deg, var(--primary-green), var(--primary-green-dark)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:13px; overflow:hidden; }
  .ava img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
  .aname { font-weight:700; font-size:13px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:130px; }
  .ahandle { font-size:11px; color:var(--text-secondary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:130px; }
  .date-cell { min-width:130px; }
  .date-main { font-weight:600; font-size:12px; color:var(--text-primary); }
  .date-time { font-size:11px; color:var(--text-secondary); margin-top:2px; }
  .sent-cell { text-align:center; min-width:110px; }
  .sent-badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:10px; font-weight:800; letter-spacing:.3px; }
  .sp { background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; }
  .sn { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
  .su { background:var(--bg-gray-100); color:#374151; border:1px solid var(--border-gray); }

  /* ═══ SENTIMENT EDITOR (tabel) ═══ */
  .sent-edit-wrap { position:relative; display:inline-flex; flex-direction:column; align-items:center; gap:5px; }
  .sent-edit-btn { cursor:pointer; display:inline-flex; align-items:center; gap:4px; border:none; background:none; padding:0; font-family:inherit; }
  .sent-edit-pencil { font-size:9px; color:var(--text-secondary); opacity:0; transition:opacity .15s; margin-left:2px; }
  .sent-edit-wrap:hover .sent-edit-pencil { opacity:1; }
  .sent-dropdown { position:absolute; bottom:calc(100% + 8px); left:50%; transform:translateX(-50%); background:#fff; border:1px solid var(--border-gray); border-radius:12px; box-shadow:0 16px 40px rgba(0,0,0,.18),0 4px 12px rgba(0,0,0,.08); z-index:99999; padding:6px; display:none; flex-direction:column; gap:3px; min-width:138px; animation:sentDrop .15s ease-out; }
  @keyframes sentDrop { from{opacity:0;transform:translateX(-50%) translateY(6px)} to{opacity:1;transform:translateX(-50%) translateY(0)} }
  .sent-dropdown.open { display:flex; }
  .sent-dd-arrow { position:absolute; bottom:-6px; left:50%; transform:translateX(-50%); width:12px; height:6px; overflow:hidden; }
  .sent-dd-arrow::after { content:''; position:absolute; top:-5px; left:50%; transform:translateX(-50%) rotate(45deg); width:10px; height:10px; background:#fff; border-right:1px solid var(--border-gray); border-bottom:1px solid var(--border-gray); }
  .sent-opt { display:flex; align-items:center; gap:8px; padding:7px 12px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; transition:background .12s; border:none; background:transparent; font-family:inherit; width:100%; text-align:left; white-space:nowrap; }
  .sent-opt:hover { background:var(--bg-gray-100); }
  .sent-opt .so-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
  .sent-opt.so-pos { color:#065f46; } .sent-opt.so-neg { color:#991b1b; } .sent-opt.so-neu { color:#374151; }
  .sent-opt.so-active { background:var(--bg-gray-100); }
  .sent-flash { position:absolute; top:-22px; left:50%; transform:translateX(-50%); background:#065f46; color:#fff; font-size:9px; font-weight:800; padding:2px 8px; border-radius:20px; white-space:nowrap; animation:flashIn .9s ease-out forwards; pointer-events:none; z-index:100000; }
  @keyframes flashIn { 0%{opacity:0;transform:translateX(-50%) translateY(4px)} 20%{opacity:1;transform:translateX(-50%) translateY(0)} 70%{opacity:1} 100%{opacity:0;transform:translateX(-50%) translateY(-4px)} }
  .sent-saving-dot { display:inline-block; width:6px; height:6px; border:1.5px solid #94a3b8; border-top-color:var(--primary-green); border-radius:50%; animation:spin .6s linear infinite; margin-left:3px; vertical-align:middle; }

  /* PAGINATION */
  .pager { display:flex; justify-content:space-between; align-items:center; padding:14px 20px; border-top:1px solid var(--border-gray); flex-wrap:wrap; gap:12px; }
  .pager-info { font-size:13px; color:var(--text-secondary); font-weight:600; }
  .pager-btns { display:flex; align-items:center; gap:4px; }
  .pbtn { width:32px; height:32px; border-radius:8px; border:1px solid var(--border-gray); background:var(--bg-white); color:var(--text-secondary); font-size:12px; font-weight:700; cursor:pointer; transition:all .15s; display:flex; align-items:center; justify-content:center; }
  .pbtn:hover:not(:disabled) { border-color:var(--primary-green); color:var(--primary-green); }
  .pbtn.active { background:var(--primary-green); border-color:var(--primary-green); color:#fff; }
  .pbtn:disabled { opacity:.35; cursor:not-allowed; }

  /* SKELETON */
  .sk { background:linear-gradient(90deg,var(--bg-gray-50) 25%,#e2e8f0 50%,var(--bg-gray-50) 75%); background-size:200% 100%; animation:shimmer 1.5s ease-in-out infinite; border-radius:8px; }
  @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

  /* EMPTY STATE */
  .empty-state { text-align:center; padding:72px 20px; color:var(--text-secondary); }
  .empty-state svg { width:44px; height:44px; margin-bottom:12px; stroke:currentColor; fill:none; stroke-width:1.5; }
  .empty-state h4 { font-size:16px; font-weight:700; color:var(--text-primary); }
  .empty-state p { font-size:13px; margin-top:5px; }

  /* ALERT */
  .alert-warn { display:flex; align-items:center; gap:12px; padding:14px 18px; background:#fffbeb; border:1px solid #fcd34d; border-radius:var(--r); font-size:13px; font-weight:600; color:#92400e; margin-bottom:24px; }

  /* LAZY LOAD */
  .lazy-load-row { text-align:center; padding:14px; border-top:1px solid var(--border-gray); display:none; }
  .lazy-load-row.show { display:block; }
  .lazy-load-info { font-size:12px; color:var(--text-secondary); font-weight:500; display:flex; align-items:center; justify-content:center; gap:8px; }

  /* CHART POPUP */
  .chart-popup { position:fixed; z-index:99999; background:#fff; border:1px solid var(--border-gray); border-radius:14px; box-shadow:0 24px 64px rgba(0,0,0,.2),0 4px 16px rgba(0,0,0,.08); width:380px; height:520px; display:none; flex-direction:column; overflow:hidden; animation:popupIn .18s cubic-bezier(.34,1.56,.64,1); pointer-events:auto; }
  @keyframes popupIn { from{opacity:0;transform:translateY(10px) scale(.95)} to{opacity:1;transform:translateY(0) scale(1)} }
  .chart-popup.show { display:flex; }
  .chart-popup-header { display:flex; align-items:center; justify-content:space-between; padding:13px 16px 11px; border-bottom:1px solid var(--border-gray); background:var(--bg-gray-50); flex-shrink:0; }
  .chart-popup-title { font-size:13px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
  .chart-popup-platform-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
  .chart-popup-close { width:26px; height:26px; border-radius:6px; background:transparent; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--text-secondary); font-size:18px; line-height:1; transition:background .15s,color .15s; }
  .chart-popup-close:hover { background:#fee2e2; color:#991b1b; }
  .chart-popup-date-bar { padding:8px 16px; background:var(--bg-white); border-bottom:1px solid var(--border-gray); font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.5px; display:flex; align-items:center; gap:8px; flex-shrink:0; }
  .chart-popup-count-badge { background:var(--primary-green); color:#fff; border-radius:10px; padding:2px 9px; font-size:11px; font-weight:800; }
  .chart-popup-list { overflow-y:auto; flex:1; padding:4px 0; min-height:0; }
  .chart-popup-list::-webkit-scrollbar { width:5px; }
  .chart-popup-list::-webkit-scrollbar-thumb { background:var(--border-gray); border-radius:4px; }
  .chart-popup-item { display:flex; gap:10px; padding:10px 16px; border-bottom:1px solid var(--bg-gray-50); transition:background .1s; cursor:pointer; }
  .chart-popup-item:last-child { border-bottom:none; }
  .chart-popup-item:hover { background:var(--bg-gray-50); }
  .chart-popup-item-ava { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark)); color:#fff; font-weight:700; font-size:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; border:1.5px solid var(--border-gray); }
  .chart-popup-item-ava img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
  .chart-popup-item-body { flex:1; min-width:0; }
  .chart-popup-item-author { font-size:12px; font-weight:700; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center; gap:5px; margin-bottom:2px; }
  .chart-popup-item-time { font-size:10px; color:var(--text-secondary); font-weight:600; flex-shrink:0; margin-left:auto; }
  .chart-popup-item-text { font-size:12px; color:var(--text-primary); line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:4px; }
  .chart-popup-item-meta { display:flex; align-items:center; gap:7px; font-size:10px; color:var(--text-secondary); }
  .chart-popup-item-sent { padding:1px 7px; border-radius:10px; font-size:9px; font-weight:800; }
  .chart-popup-footer { padding:10px 16px; border-top:1px solid var(--border-gray); background:var(--bg-gray-50); text-align:center; flex-shrink:0; }
  .chart-popup-footer-btn { font-size:12px; font-weight:700; color:var(--primary-green); background:none; border:1px solid rgba(3,128,71,.2); cursor:pointer; padding:5px 16px; border-radius:8px; transition:all .15s; }
  .chart-popup-footer-btn:hover { background:rgba(3,128,71,.08); border-color:var(--primary-green); }

  /* ═══ MENTION PREVIEW MODAL ═══ */
  .mpv-backdrop { position:fixed; inset:0; z-index:200000; background:rgba(10,15,25,.75); backdrop-filter:blur(10px); display:none; align-items:center; justify-content:center; padding:20px; }
  .mpv-backdrop.open { display:flex; }
  .mpv-shell { position:relative; width:100%; max-width:940px; max-height:calc(100vh - 40px); background:#fff; border-radius:20px; box-shadow:0 32px 80px rgba(0,0,0,.35); display:flex; flex-direction:column; animation:mpvUp .2s cubic-bezier(.34,1.3,.64,1); overflow:hidden; }
  @keyframes mpvUp { from{opacity:0;transform:translateY(20px) scale(.97)} to{opacity:1;transform:none} }
  .mpv-header { display:flex; align-items:center; gap:12px; padding:13px 18px; border-bottom:1px solid #e2e8f0; background:#f8fafc; flex-shrink:0; }
  .mpv-plat-pill { display:inline-flex; align-items:center; padding:3px 11px; border-radius:20px; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.5px; flex-shrink:0; white-space:nowrap; }
  .mpv-title { flex:1; font-size:13px; font-weight:700; color:#1a202c; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; min-width:0; }
  .mpv-ext-btn { display:inline-flex; align-items:center; gap:6px; padding:6px 13px; border-radius:8px; font-size:12px; font-weight:700; color:var(--primary-green); background:rgba(3,128,71,.08); border:1px solid rgba(3,128,71,.2); cursor:pointer; text-decoration:none; flex-shrink:0; transition:all .15s; white-space:nowrap; }
  .mpv-ext-btn:hover { background:rgba(3,128,71,.15); }
  .mpv-ext-btn svg { width:11px; height:11px; }
  .mpv-close { width:30px; height:30px; border-radius:8px; border:none; background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#64748b; font-size:22px; line-height:1; transition:all .15s; flex-shrink:0; }
  .mpv-close:hover { background:#fee2e2; color:#991b1b; }
  .mpv-author-bar { display:flex; align-items:center; gap:12px; padding:10px 18px; border-bottom:1px solid #f1f5f9; background:#fff; flex-shrink:0; }
  .mpv-ava { width:38px; height:38px; border-radius:50%; border:2px solid #e2e8f0; flex-shrink:0; background:linear-gradient(135deg,var(--primary-green),var(--primary-green-dark)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:13px; overflow:hidden; }
  .mpv-ava img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
  .mpv-author-name { font-size:13px; font-weight:700; color:#1a202c; }
  .mpv-author-meta { font-size:11px; color:#64748b; margin-top:1px; }

  /* ═══ SENTIMENT EDITOR DI MODAL ═══ */
  .mpv-sent-row { display:flex; align-items:center; gap:10px; padding:10px 18px; border-bottom:1px solid #f1f5f9; background:#fff; flex-shrink:0; flex-wrap:wrap; }
  .mpv-sent-row-label { font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; flex-shrink:0; }
  .mpv-sent-opts { display:flex; gap:6px; flex-wrap:wrap; }
  .mpv-sent-opt { display:inline-flex; align-items:center; gap:6px; padding:5px 14px; border-radius:20px; font-size:11px; font-weight:800; cursor:pointer; border:2px solid transparent; transition:all .18s; background:var(--bg-gray-100); color:var(--text-secondary); }
  .mpv-sent-opt:hover:not(.mpv-so-active) { transform:translateY(-1px); box-shadow:0 3px 8px rgba(0,0,0,.1); }
  .mpv-sent-opt .mpv-so-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
  .mpv-so-active-pos { background:#d1fae5 !important; color:#065f46 !important; border-color:#6ee7b7 !important; }
  .mpv-so-active-neg { background:#fee2e2 !important; color:#991b1b !important; border-color:#fca5a5 !important; }
  .mpv-so-active-neu { background:var(--bg-gray-100) !important; color:#374151 !important; border-color:var(--border-gray) !important; }
  .mpv-sent-status { font-size:11px; font-weight:600; color:var(--text-secondary); margin-left:auto; display:flex; align-items:center; gap:5px; }

  .mpv-view-tabs { display:flex; gap:0; padding:0 18px; border-bottom:1px solid #e2e8f0; background:#fff; flex-shrink:0; overflow-x:auto; }
  .mpv-view-tab { padding:10px 16px; border:none; border-bottom:2px solid transparent; font-size:12px; font-weight:700; color:#64748b; background:transparent; cursor:pointer; transition:all .15s; white-space:nowrap; flex-shrink:0; }
  .mpv-view-tab.active { color:var(--primary-green); border-bottom-color:var(--primary-green); }
  .mpv-view-tab:hover:not(.active) { color:var(--primary-green); }
  .mpv-body { flex:1; min-height:0; overflow-y:auto; }
  .mpv-body::-webkit-scrollbar { width:5px; }
  .mpv-body::-webkit-scrollbar-thumb { background:#e2e8f0; border-radius:4px; }
  .mpv-panel { display:none; }
  .mpv-panel.active { display:block; }
  .mpv-iframe-wrap { position:relative; width:100%; height:520px; }
  .mpv-iframe-wrap iframe { width:100%; height:100%; border:none; display:block; opacity:0; transition:opacity .3s; }
  .mpv-iframe-wrap iframe.loaded { opacity:1; }
  .mpv-iframe-loader { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; background:#f8fafc; color:#64748b; font-size:13px; font-weight:600; pointer-events:none; }
  .mpv-iframe-loader.hidden { display:none; }
  .mpv-iframe-spinner { width:28px; height:28px; border:3px solid #e2e8f0; border-top-color:var(--primary-green); border-radius:50%; animation:spin .7s linear infinite; }
  .mpv-video-wrap { position:relative; padding-bottom:56.25%; height:0; background:#000; }
  .mpv-video-wrap iframe { position:absolute; inset:0; width:100%; height:100%; border:none; }
  .mpv-tiktok-wrap { display:flex; justify-content:center; background:#f8fafc; padding:16px; min-height:480px; align-items:flex-start; overflow-y:auto; }
  .mpv-fallback { padding:22px; }
  .mpv-content-text { font-size:14px; line-height:1.75; color:#1a202c; margin-bottom:20px; word-break:break-word; }
  .mpv-meta-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(128px,1fr)); gap:10px; margin-bottom:20px; }
  .mpv-meta-card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; }
  .mpv-meta-lbl { font-size:9px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }
  .mpv-meta-val { font-size:16px; font-weight:800; color:#1a202c; }
  .mpv-url-bar { display:flex; align-items:center; gap:10px; padding:11px 15px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; text-decoration:none; transition:all .15s; margin-top:4px; }
  .mpv-url-bar:hover { border-color:var(--primary-green); }
  .mpv-url-favicon { width:18px; height:18px; border-radius:4px; flex-shrink:0; }
  .mpv-url-text { font-size:12px; font-weight:600; color:var(--primary-green); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .mpv-hint-box { display:flex; align-items:flex-start; gap:8px; padding:10px 14px; background:#fffbeb; border:1px solid #fcd34d; border-radius:10px; font-size:12px; font-weight:600; color:#92400e; margin-bottom:16px; line-height:1.5; }
  .mpv-hint-box svg { width:15px; height:15px; flex-shrink:0; margin-top:1px; }

  @media(max-width:768px){
    .dashboard-container{padding:16px;} .content-cell{max-width:200px;} .filter-content{flex-direction:column;align-items:stretch;}
    .date-trigger{min-width:auto;} .apply-btn{width:100%;justify-content:center;}
    .overview-body{grid-template-columns:1fr;} .overview-divider{height:1px;width:auto;} .donut-canvas-wrap{width:120px;height:120px;}
    .date-picker-container{flex-direction:column;max-height:85vh;overflow-y:auto;width:95%;}
    .date-picker-sidebar{width:100%;border-right:none;border-bottom:1px solid var(--border-gray);border-radius:16px 16px 0 0;flex-direction:row;overflow-x:auto;padding:12px 16px;}
    .date-preset{white-space:nowrap;} .calendars-wrapper{flex-direction:column;gap:16px;} .cancel-btn,.apply-date-btn{flex:1;}
    .chart-popup{width:calc(100vw - 32px);max-width:380px;height:480px;}
    .mpv-shell{max-height:96vh;border-radius:16px 16px 0 0;}
    .mpv-backdrop{align-items:flex-end;padding:0;}
    .mpv-iframe-wrap{height:340px;}
    .mpv-sent-row{gap:8px;}
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">
  <div class="page-header">
    <h1>Mentions Timeline</h1>
    <p>Track all media mentions across Online News, Twitter, Facebook, Instagram, YouTube &amp; TikTok</p>
  </div>

  @if(!$projectId)
  <div class="alert-warn">
    <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;flex-shrink:0;stroke-width:2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    No project selected. Please select a project from the sidebar.
  </div>
  @else

  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.news.timeline') }}" style="display:contents">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hStart" value="{{ $startDate }}">
      <input type="hidden" name="end_date"   id="hEnd"   value="{{ $endDate }}">
      <div class="filter-content">
        <div class="filter-group">
          <label class="filter-label">Date Range</label>
          <button type="button" class="date-trigger" id="dpTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0;color:var(--text-secondary)"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="dpDisplay">{{ $startDate }} &rarr; {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:var(--text-secondary)"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <div class="filter-group">
          <label class="filter-label" style="opacity:0;pointer-events:none;">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:15px;height:15px"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Apply Filter
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- DATE PICKER MODAL --}}
  <div class="date-picker-modal" id="datePickerModal">
    <div class="date-picker-overlay" id="dpOverlay"></div>
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
          <button type="button" class="nav-btn" id="dpPrev"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
          <div class="calendars-wrapper"><div class="calendar" id="dpCal1"></div><div class="calendar" id="dpCal2"></div></div>
          <button type="button" class="nav-btn" id="dpNext"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
        </div>
        <div class="date-picker-display"><span id="dpRangeText">{{ $startDate }} to {{ $endDate }}</span></div>
        <div class="date-picker-footer">
          <button type="button" class="cancel-btn" id="dpCancel">Cancel</button>
          <button type="button" class="apply-date-btn" id="dpApply">Apply</button>
        </div>
      </div>
    </div>
  </div>

  {{-- STATS CARD --}}
  <div class="do-card">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <span class="do-head-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
        <span class="do-card-title">Overview</span>
      </div>
      <span class="do-badge">All Media Types</span>
    </div>
    <div class="platform-loading-bar" id="platformLoadingBar">
      <span class="plb-label">Loading:</span>
      <span class="plb-item loading" id="plb-doc"><span class="plb-spinner"></span> Online News</span>
      <span class="plb-item loading" id="plb-twit"><span class="plb-spinner"></span> Twitter</span>
      <span class="plb-item loading" id="plb-fb"><span class="plb-spinner"></span> Facebook</span>
      <span class="plb-item loading" id="plb-ig"><span class="plb-spinner"></span> Instagram</span>
      <span class="plb-item loading" id="plb-ytb"><span class="plb-spinner"></span> YouTube</span>
      <span class="plb-item loading" id="plb-tiktok"><span class="plb-spinner"></span> TikTok</span>
    </div>
    <div class="overview-body">
      <div class="overview-panel">
        <div class="overview-title"><div class="overview-title-dot" style="background:var(--primary-green)"></div>Mentions by Platform</div>
        <div class="donut-row">
          <div class="donut-canvas-wrap">
            <div class="sk" id="skPlatform" style="width:150px;height:150px;border-radius:50%;"></div>
            <canvas id="donutPlatform" width="150" height="150" style="display:none"></canvas>
            <div class="donut-center-text"><div class="donut-center-num" id="donutTotalPlatform">—</div><div class="donut-center-lbl">Total</div></div>
          </div>
          <div class="donut-legend">
            @foreach([['doc','Online News','#3b82f6'],['twit','Twitter','#0ea5e9'],['fb','Facebook','#6366f1'],['ig','Instagram','#ec4899'],['ytb','YouTube','#ef4444'],['tiktok','TikTok','#6b7280']] as $p)
            <div class="dl-item">
              <div class="dl-dot" style="background:{{ $p[2] }}"></div>
              <div class="dl-name">{{ $p[1] }}</div>
              <div class="dl-val" id="dlval-{{ $p[0] }}"><div class="sk" style="height:14px;width:40px;"></div></div>
              <div class="dl-pct" id="dlpct-{{ $p[0] }}"></div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
      <div class="overview-divider"></div>
      <div class="overview-panel">
        <div class="overview-title"><div class="overview-title-dot" style="background:#f59e0b"></div>Sentiment Distribution</div>
        <div class="donut-row">
          <div class="donut-canvas-wrap">
            <div class="sk" id="skSentiment" style="width:150px;height:150px;border-radius:50%;"></div>
            <canvas id="donutSentiment" width="150" height="150" style="display:none"></canvas>
            <div class="donut-center-text"><div class="donut-center-num" id="donutTotalSent">—</div><div class="donut-center-lbl">Total</div></div>
          </div>
          <div class="donut-legend">
            @foreach([['pos','Positive','#16a34a'],['neg','Negative','#ef4444'],['neu','Neutral','#94a3b8']] as $s)
            <div class="dl-item">
              <div class="dl-dot" style="background:{{ $s[2] }}"></div>
              <div class="dl-name">{{ $s[1] }}</div>
              <div class="dl-val" id="dlval-{{ $s[0] }}"><div class="sk" style="height:14px;width:40px;"></div></div>
              <div class="dl-pct" id="dlpct-{{ $s[0] }}"></div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    <div class="progress-bar-wrap"><div class="progress-bar" id="loadProgress"></div></div>
  </div>

  {{-- CHART CARD --}}
  <div class="do-card">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <span class="do-head-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
        <div>
          <div class="do-card-title">Mentions Trend</div>
          <div style="font-size:12px;color:var(--text-secondary);margin-top:2px;font-weight:500;">Daily volume per media platform — click legend to toggle</div>
        </div>
      </div>
      <div class="chart-controls">
        <div class="chart-click-hint">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
          Klik titik/bar untuk lihat detail
        </div>
        <div class="chart-toggle">
          <button id="chartBtnLine" class="ct-btn active" onclick="setChartMode('line')">Line</button>
          <button id="chartBtnArea" class="ct-btn"        onclick="setChartMode('area')">Area</button>
          <button id="chartBtnBar"  class="ct-btn"        onclick="setChartMode('bar')">Bar</button>
          <button id="chartBtnLog"  class="ct-btn"        onclick="setChartMode('log')">Log</button>
        </div>
        <div class="legend" id="chartLegend"><div class="sk" style="height:18px;width:360px;"></div></div>
      </div>
    </div>
    <div style="padding:20px;">
      <div class="chart-wrap">
        <div class="sk" id="chartSk" style="height:100%;"></div>
        <canvas id="volChart" style="display:none"></canvas>
      </div>
    </div>
  </div>

  {{-- TABLE CARD --}}
  <div class="do-card" style="overflow:visible;">
    <div class="table-head">
      <div class="table-head-info">
        <h3>Mentions Timeline</h3>
        <p id="tblSub">Loading data from all platforms…</p>
      </div>
      <div class="search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Search mentions…" oninput="doSearch()">
      </div>
    </div>
    <div class="media-tabs" id="mediaTabs">
      @php $tabs=[['all','All'],['doc','Online News'],['twit','Twitter'],['fb','Facebook'],['ig','Instagram'],['ytb','YouTube'],['tiktok','TikTok']]; @endphp
      @foreach($tabs as $tab)
      <button class="media-tab {{ $tab[0]==='all'?'active':'' }}" data-tab="{{ $tab[0] }}" onclick="switchTab('{{ $tab[0] }}')">
        {{ $tab[1] }}<span class="tab-cnt" id="tc-{{ $tab[0] }}"><span class="tab-spinner"></span></span>
      </button>
      @endforeach
    </div>
    <div style="overflow-x:auto">
      <div id="tblLoading" style="padding:20px"><div class="sk" style="height:380px;"></div></div>
      <table class="mentions-table" id="mainTbl" style="display:none">
        <thead><tr>
          <th class="c">No</th><th>Media</th><th>Type</th><th>Content</th>
          <th class="c">Engagement</th><th>Author</th><th>Date</th><th class="c">Sentiment</th>
        </tr></thead>
        <tbody id="tblBody"></tbody>
      </table>
      <div id="emptyState" style="display:none">
        <div class="empty-state">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <h4>No Mentions Found</h4><p>No data available for the selected filters.</p>
        </div>
      </div>
      <div class="lazy-load-row" id="lazyLoadRow">
        <div class="lazy-load-info"><span class="tab-spinner"></span><span id="lazyLoadText">Loading other platforms…</span></div>
      </div>
    </div>
    <div class="pager" id="pager" style="display:none"></div>
  </div>

  @endif
</div>

{{-- MENTION PREVIEW MODAL --}}
<div class="mpv-backdrop" id="mpvBackdrop">
  <div class="mpv-shell" id="mpvShell">
    <div class="mpv-header">
      <span class="mpv-plat-pill" id="mpvPlatPill"></span>
      <span class="mpv-title" id="mpvTitle"></span>
      <a class="mpv-ext-btn" id="mpvExtBtn" href="#" target="_blank" rel="noopener noreferrer">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Buka sumber
      </a>
      <button class="mpv-close" id="mpvClose">×</button>
    </div>
    <div class="mpv-author-bar">
      <div class="mpv-ava" id="mpvAva"></div>
      <div style="min-width:0;flex:1">
        <div class="mpv-author-name" id="mpvAuthorName"></div>
        <div class="mpv-author-meta" id="mpvAuthorMeta"></div>
      </div>
    </div>
    {{-- ROW SENTIMENT EDIT DI MODAL --}}
    <div class="mpv-sent-row" id="mpvSentRow">
      <span class="mpv-sent-row-label">Sentiment</span>
      <div class="mpv-sent-opts">
        <button class="mpv-sent-opt" id="mpvSentPos" onclick="mpvChangeSent('1')">
          <span class="mpv-so-dot" style="background:#16a34a"></span>Positive
        </button>
        <button class="mpv-sent-opt" id="mpvSentNeg" onclick="mpvChangeSent('-1')">
          <span class="mpv-so-dot" style="background:#ef4444"></span>Negative
        </button>
        <button class="mpv-sent-opt" id="mpvSentNeu" onclick="mpvChangeSent('0')">
          <span class="mpv-so-dot" style="background:#94a3b8"></span>Neutral
        </button>
      </div>
      <span class="mpv-sent-status" id="mpvSentStatus"></span>
    </div>
    <div class="mpv-view-tabs" id="mpvViewTabs"></div>
    <div class="mpv-body" id="mpvBody"></div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ═══════════════════════════════════════════════
// DATE PICKER
// ═══════════════════════════════════════════════
(function(){
  'use strict';
  var ds=null,de=null,m1=new Date(),m2=new Date(),pickStart=true,dpOpen=false;
  document.addEventListener('DOMContentLoaded',function(){
    var si=document.getElementById('hStart'),ei=document.getElementById('hEnd');
    ds=si&&si.value?new Date(si.value):(function(){var d=new Date();d.setDate(d.getDate()-6);return d;})();
    de=ei&&ei.value?new Date(ei.value):new Date();
    m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);renderCals();
    document.getElementById('dpTrigger').addEventListener('click',open);
    document.getElementById('dpOverlay').addEventListener('click',close);
    document.querySelectorAll('.date-preset').forEach(function(b){b.addEventListener('click',preset);});
    document.getElementById('dpPrev').addEventListener('click',function(){m1.setMonth(m1.getMonth()-1);m2.setMonth(m2.getMonth()-1);renderCals();});
    document.getElementById('dpNext').addEventListener('click',function(){m1.setMonth(m1.getMonth()+1);m2.setMonth(m2.getMonth()+1);renderCals();});
    document.getElementById('dpApply').addEventListener('click',apply);
    document.getElementById('dpCancel').addEventListener('click',close);
  });
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&dpOpen){e.stopPropagation();close();}},true);
  function open(){dpOpen=true;document.getElementById('datePickerModal').classList.add('show');renderCals();}
  function close(){dpOpen=false;document.getElementById('datePickerModal').classList.remove('show');}
  function preset(e){
    document.querySelectorAll('.date-preset').forEach(function(b){b.classList.remove('active');});
    e.target.classList.add('active');
    var today=new Date();today.setHours(0,0,0,0);
    switch(e.target.dataset.preset){
      case'today':ds=new Date(today);de=new Date(today);break;
      case'yesterday':ds=new Date(today);ds.setDate(today.getDate()-1);de=new Date(ds);break;
      case'last7days':de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-6);break;
      case'last30days':de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-29);break;
      case'thismonth':ds=new Date(today.getFullYear(),today.getMonth(),1);de=new Date(today);break;
      case'lastmonth':ds=new Date(today.getFullYear(),today.getMonth()-1,1);de=new Date(today.getFullYear(),today.getMonth(),0);break;
    }
    if(e.target.dataset.preset!=='custom'){m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);updateDisp();renderCals();}
  }
  function apply(){document.getElementById('hStart').value=fmt(ds);document.getElementById('hEnd').value=fmt(de);document.getElementById('dpDisplay').textContent=fmt(ds)+' → '+fmt(de);close();}
  function renderCals(){renderCal('dpCal1',m1);renderCal('dpCal2',m2);updateDisp();}
  function renderCal(id,month){
    var el=document.getElementById(id);if(!el)return;
    var y=month.getFullYear(),mn=month.getMonth(),first=new Date(y,mn,1),last=new Date(y,mn+1,0),prevL=new Date(y,mn,0);
    var MN=['January','February','March','April','May','June','July','August','September','October','November','December'];
    var WD=['Su','Mo','Tu','We','Th','Fr','Sa'],today=new Date();today.setHours(0,0,0,0);
    var h='<div class="calendar-month">'+MN[mn]+' '+y+'</div><div class="calendar-weekdays">'+WD.map(function(d){return'<div class="weekday">'+d+'</div>';}).join('')+'</div><div class="calendar-days">';
    for(var i=0;i<first.getDay();i++)h+='<button type="button" class="calendar-day other-month" disabled>'+(prevL.getDate()-(first.getDay()-1-i))+'</button>';
    for(var d=1;d<=last.getDate();d++){
      var date=new Date(y,mn,d);date.setHours(0,0,0,0);
      var cls='calendar-day';
      if(sameDay(date,today))cls+=' today';
      if(date>today)cls+=' disabled';
      if(ds&&de){if(sameDay(date,ds))cls+=' selected range-start';else if(sameDay(date,de))cls+=' selected range-end';else if(date>ds&&date<de)cls+=' in-range';}
      h+='<button type="button" class="'+cls+'" data-date="'+fmt(date)+'"'+(date>today?' disabled':'')+'>'+d+'</button>';
    }
    var rem=last.getDay()===6?0:6-last.getDay();
    for(var j=1;j<=rem;j++)h+='<button type="button" class="calendar-day other-month" disabled>'+j+'</button>';
    h+='</div>';el.innerHTML=h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(function(btn){
      btn.addEventListener('click',function(){
        var d=new Date(this.dataset.date);d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(function(b){b.classList.remove('active');});
        document.querySelector('[data-preset="custom"]').classList.add('active');
        if(pickStart||d<ds){ds=d;de=d;pickStart=false;}else{if(d>=ds)de=d;else{de=ds;ds=d;}pickStart=true;}
        updateDisp();renderCals();
      });
    });
  }
  function updateDisp(){
    var el=document.getElementById('dpRangeText');if(el&&ds&&de)el.textContent=fmt(ds)+' to '+fmt(de);
    var disp=document.getElementById('dpDisplay');if(disp&&ds&&de)disp.textContent=fmt(ds)+' → '+fmt(de);
  }
  function fmt(d){if(!d)return'';return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');}
  function sameDay(a,b){return a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();}
})();

// ═══════════════════════════════════════════════
// CONFIG & STATE
// ═══════════════════════════════════════════════
var PID='{{ $projectId ?? "" }}',SD='{{ $startDate ?? "" }}',ED='{{ $endDate ?? "" }}';
var CSRF='{{ csrf_token() }}';
var PLATFORM_CFG={
  doc:    {label:'Online News',color:'#3b82f6'},
  twit:   {label:'Twitter',    color:'#0ea5e9'},
  fb:     {label:'Facebook',   color:'#6366f1'},
  ig:     {label:'Instagram',  color:'#ec4899'},
  ytb:    {label:'YouTube',    color:'#ef4444'},
  tiktok: {label:'TikTok',     color:'#6b7280'}
};
var PLAT_KEYS=['doc','twit','fb','ig','ytb','tiktok'];
var store={all:[],doc:[],twit:[],fb:[],ig:[],ytb:[],tiktok:[]};
var loadingPlatforms=new Set(PLAT_KEYS),errorPlatforms=new Set();
var firstRenderDone=false,mentionsFetchDone=false;
var activeTab='all',filtered=[],page=1,PER=100,q='';
var chartPlatformInstance=null,chartSentimentInstance=null,chartInstance=null,chartMode='line';
var hiddenPlatforms=new Set();
var PLAT_CFG_DONUT=[
  {key:'doc',label:'Online News',color:'#3b82f6'},{key:'twit',label:'Twitter',color:'#0ea5e9'},
  {key:'fb',label:'Facebook',color:'#6366f1'},{key:'ig',label:'Instagram',color:'#ec4899'},
  {key:'ytb',label:'YouTube',color:'#ef4444'},{key:'tiktok',label:'TikTok',color:'#6b7280'}
];
var chartDates=[];

// ═══════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════
function fmtN(n){return new Intl.NumberFormat('en-US').format(n);}
function esc(t){var d=document.createElement('div');d.textContent=t;return d.innerHTML;}
function strip(h){return h?h.replace(/<[^>]*>/g,'').trim():'';}
function fmtDate(str){
  if(!str)return{d:'—',t:''};
  try{var dt=new Date(str);return{d:dt.toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}),t:dt.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'})};}
  catch(e){return{d:str,t:''};}
}
function initials(n){if(!n||n==='Unknown')return'?';var p=n.trim().split(/\s+/);return p.length===1?p[0].slice(0,2).toUpperCase():(p[0][0]+p[p.length-1][0]).toUpperCase();}

function normSentiment(v){
  if(v===null||v===undefined)return'0';
  var s=String(v).toLowerCase().trim();
  if(s==='1'||s==='positive'||s==='positif')return'1';
  if(s==='-1'||s==='2'||s==='negative'||s==='negatif')return'-1';
  if(s==='0'||s==='3'||s==='neutral'||s==='netral'||s==='neu')return'0';
  var n=parseInt(s,10);
  if(!isNaN(n)){if(n===1)return'1';if(n===-1||n===2)return'-1';if(n===0||n===3)return'0';}
  return'0';
}
function sentBadge(v,absIdx){
  var s=normSentiment(v);
  var cls=s==='1'?'sp':s==='-1'?'sn':'su';
  var lbl=s==='1'?'Positive':s==='-1'?'Negative':'Neutral';
  // Tanpa absIdx = badge biasa (untuk chart popup)
  if(absIdx===undefined){return'<span class="sent-badge '+cls+'">'+lbl+'</span>';}
  // Dengan absIdx = editable (untuk tabel)
  var wid='sew'+absIdx;var did='sed'+absIdx;var bid='seb'+absIdx;
  return'<div class="sent-edit-wrap" id="'+wid+'">'
    +'<button type="button" class="sent-edit-btn" onclick="toggleSentDrop(event,'+absIdx+')">'
    +'<span class="sent-badge '+cls+'" id="'+bid+'">'+lbl+'</span>'
    +'<span class="sent-edit-pencil">✎</span>'
    +'</button>'
    +'<div class="sent-dropdown" id="'+did+'">'
    +'<button type="button" class="sent-opt so-pos'+(s==='1'?' so-active':'')+'" onclick="changeSent(event,'+absIdx+',\'1\')">'
    +'<span class="so-dot" style="background:#16a34a"></span>Positive</button>'
    +'<button type="button" class="sent-opt so-neg'+(s==='-1'?' so-active':'')+'" onclick="changeSent(event,'+absIdx+',\'-1\')">'
    +'<span class="so-dot" style="background:#ef4444"></span>Negative</button>'
    +'<button type="button" class="sent-opt so-neu'+(s==='0'?' so-active':'')+'" onclick="changeSent(event,'+absIdx+',\'0\')">'
    +'<span class="so-dot" style="background:#94a3b8"></span>Neutral</button>'
    +'<div class="sent-dd-arrow"></div>'
    +'</div>'
    +'</div>';
}
function mediaBadge(p){var map={doc:['mb-doc','News'],twit:['mb-twit','Twitter'],fb:['mb-fb','Facebook'],ig:['mb-ig','Instagram'],ytb:['mb-ytb','YouTube'],tiktok:['mb-tiktok','TikTok']};var r=map[p]||['mb-doc','Other'];return'<span class="media-badge '+r[0]+'">'+r[1]+'</span>';}
window._avaFail=function(el,i){el.parentElement.textContent=i;};
function avaImg(src,name,initl){var s=initl.replace(/'/g,'&#39;').replace(/"/g,'&quot;');return'<img src="'+esc(src)+'" alt="'+esc(name)+'" onerror="_avaFail(this,\''+s+'\')">';}

function detectPlatform(item){
  var tcode=String(item.tcode||'').toLowerCase().trim();
  if(tcode==='berita')return'doc';
  if(tcode==='rt'||tcode==='mention'||tcode==='reply'||tcode==='tweet'||tcode==='retweet')return'twit';
  if(tcode==='fb-post'||tcode==='fb-comment'||tcode==='fb-share')return'fb';
  if(tcode==='youtube')return'ytb';
  if(tcode==='ig-post'||tcode==='ig-story'||tcode==='ig-reel')return'ig';
  var mt=String(item.media_type||'').toLowerCase().trim();
  if(mt==='fb'||mt==='facebook')return'fb';if(mt==='ig'||mt==='instagram')return'ig';
  if(mt==='ytb'||mt==='youtube')return'ytb';if(mt==='tiktok'||mt==='tt')return'tiktok';
  if(mt==='twit'||mt==='twitter'||mt==='x')return'twit';
  if(mt==='berita'||mt==='online'||mt==='news'||mt==='article'||mt==='doc')return'doc';
  var id=String(item.id||item.docid||'').toLowerCase();
  if(id.startsWith('tiktok-')||id.startsWith('tt-'))return'tiktok';
  if(id.startsWith('in-')||id.startsWith('ig-'))return'ig';
  if(id.startsWith('fb-'))return'fb';if(id.startsWith('yt-')||id.startsWith('ytb-'))return'ytb';
  if(id.startsWith('tw-')||id.startsWith('twit-'))return'twit';
  var url=String(item.url||'').toLowerCase();
  if(url.includes('tiktok.com'))return'tiktok';if(url.includes('instagram.com'))return'ig';
  if(url.includes('facebook.com')||url.includes('fb.com'))return'fb';
  if(url.includes('youtube.com')||url.includes('youtu.be'))return'ytb';
  if(url.includes('twitter.com')||url.includes('x.com'))return'twit';
  var host=String(item.hostname||'').toLowerCase();
  if(host.includes('tiktok'))return'tiktok';if(host.includes('instagram'))return'ig';
  if(host.includes('facebook'))return'fb';if(host.includes('youtube')||host.includes('youtu'))return'ytb';
  if(host.includes('twitter')||host==='x.com')return'twit';
  var mtid=String(item.media_type_id||'').trim();
  if(mtid==='6')return'tiktok';if(mtid==='3')return'ig';
  return'doc';
}
function _safeStr(v){if(!v)return'';var s=String(v).trim();if(/^\d+$/.test(s))return'';if(s.length<2)return'';return s;}

function norm(item,platform){
  var authorObj={};
  if(item.author&&typeof item.author==='string'){try{authorObj=JSON.parse(item.author);}catch(e){}}
  else if(item.author&&typeof item.author==='object'&&item.author!==null){authorObj=item.author;}
  var authorHandle=_safeStr(item.author_scr_name)||_safeStr(item.author_id)||_safeStr(authorObj.scr_name)||_safeStr(authorObj.username)||_safeStr(item.username)||_safeStr(item.screen_name)||_safeStr(item.user_name)||'';
  var authorName='';
  if(platform==='doc'){
    authorName=_safeStr(authorObj.name)||_safeStr(item.author_name)||_safeStr(item.journalist)||_safeStr(item.reporter)||_safeStr(item.source_name)||_safeStr(item.media_name)||_safeStr(item.site_name)||_safeStr(item.publisher)||_safeStr(item.source)||authorHandle||'';
    if(!authorName){var hn=item.hostname||'';if(!hn&&item.url){try{hn=new URL(item.url.startsWith('http')?item.url:'https://'+item.url).hostname;}catch(e){}}hn=hn.replace(/^www\./,'');var domPart=hn.split('.')[0]||'';if(domPart.length>=2){authorName=domPart.replace(/[-_]/g,' ').replace(/\b\w/g,function(c){return c.toUpperCase();});}}
  }else{authorName=_safeStr(authorObj.name)||_safeStr(item.author_name)||_safeStr(item.display_name)||_safeStr(item.full_name)||authorHandle||_safeStr(item.username)||_safeStr(item.screen_name)||'';}
  function _validUrl(u){return u&&typeof u==='string'&&u.trim().startsWith('http');}
  var rawAvatar=(_validUrl(item.avatar_url)&&item.avatar_url)||(_validUrl(item.profile_image)&&item.profile_image)||(_validUrl(authorObj.image)&&authorObj.image)||(_validUrl(authorObj.avatar)&&authorObj.avatar)||(_validUrl(authorObj.profile_image_url)&&authorObj.profile_image_url)||(_validUrl(item.image)&&item.image)||'';
  if(!rawAvatar&&item.contentJson){try{var cj=typeof item.contentJson==='string'?JSON.parse(item.contentJson):item.contentJson;rawAvatar=(_validUrl(cj.user&&cj.user.image)&&cj.user.image)||(_validUrl(cj.image)&&cj.image)||(_validUrl(cj.avatar)&&cj.avatar)||'';}catch(e){}}
  var hostname='';
  if(item.hostname){hostname=item.hostname;}else if(item.url){try{hostname=new URL(item.url.startsWith('http')?item.url:'https://'+item.url).hostname;}catch(e){}}
  var sent=normSentiment(item.class_sentiment||item.sentiment||item.sentiment_id||item.sentiment_str||'0');
  var nL,nC,nS,nV,nR,nF;
  if(platform==='twit'){nL=parseInt(item.num_likes||item.likes||item.favorite_count||0,10);nR=parseInt(item.num_retweeted||item.rt||item.retweet_count||0,10);nC=parseInt(item.num_comments||item.replies||item.reply_count||0,10);nS=parseInt(item.num_shares||item.shares||0,10);nV=parseInt(item.view_cnt||item.num_views||item.impression_count||item.views||0,10);nF=parseInt(item.num_followers||(authorObj&&authorObj.flw_cnt)||(authorObj&&authorObj.followers_count)||0,10);}
  else if(platform==='fb'){nL=parseInt(item.likes||item.num_likes||item.reactions||item.freq||0,10);nC=parseInt(item.comments||item.num_comments||0,10);nS=parseInt(item.shares||item.num_shares||0,10);nV=parseInt(item.views||item.view_cnt||item.num_views||0,10);nR=0;nF=parseInt(item.num_followers||item.followers||0,10);}
  else if(platform==='ig'){nL=parseInt(item.num_likes||item.likes||item.freq||0,10);nC=parseInt(item.num_comments||item.comments||0,10);nS=parseInt(item.num_shares||item.shares||0,10);nV=parseInt(item.num_views||item.views||item.view_cnt||0,10);nR=0;nF=parseInt(item.num_followers||item.followers||0,10);}
  else if(platform==='ytb'){nV=parseInt(item.view_cnt||item.views||item.num_views||0,10);nL=parseInt(item.num_likes||item.likes||0,10);nC=parseInt(item.num_comments||item.comments||0,10);nS=parseInt(item.num_shares||item.shares||0,10);nR=0;nF=parseInt(item.num_followers||item.subscribers||0,10);}
  else if(platform==='tiktok'){nV=parseInt(item.views||item.num_views||item.view_cnt||item.play_count||0,10);nL=parseInt(item.likes||item.num_likes||item.digg_count||item.freq||0,10);nC=parseInt(item.comments||item.num_comments||item.comment_count||0,10);nS=parseInt(item.shares||item.num_shares||item.share_count||0,10);nR=0;nF=parseInt(item.num_followers||item.followers||0,10);}
  else{nV=parseInt(item.view_cnt||item.views||item.num_views||0,10);nL=parseInt(item.num_likes||item.likes||0,10);nC=parseInt(item.num_comments||item.comments||0,10);nS=parseInt(item.num_shares||item.shares||0,10);nR=0;nF=0;}
  var rawUrl=item.url||item.link||item.permalink||'';var finalUrl='';
  if(rawUrl){rawUrl=String(rawUrl).trim();if(rawUrl.startsWith('http://')||rawUrl.startsWith('https://'))finalUrl=rawUrl;else if(rawUrl.startsWith('//'))finalUrl='https:'+rawUrl;else if(rawUrl.length>5&&rawUrl.includes('.'))finalUrl='https://'+rawUrl;}
  return{
    _platform:platform,
    _rawId:item.id||item.docid||item.doc_id||'',
    content:strip(item.content||item.name||item.title||item.text||''),
    author_name:authorName,author_handle:authorHandle,
    avatar_url:(rawAvatar&&String(rawAvatar).startsWith('http'))?rawAvatar:'',
    hostname:hostname,url:finalUrl,
    date_created:item.date_created||item.created_at||item.published_at||'',
    num_likes:nL,num_comments:nC,num_shares:nS,num_views:nV,num_retweeted:nR,num_followers:nF,
    class_sentiment:sent,
    mention_type:item.mention_type||item.tcode||item.type||'post'
  };
}

// ═══════════════════════════════════════════════
// FETCH
// ═══════════════════════════════════════════════
async function safeGet(url,retries){
  retries=retries===undefined?2:retries;
  for(var i=0;i<=retries;i++){
    try{var ctrl=new AbortController();var tid=setTimeout(function(){ctrl.abort();},30000);var r=await fetch(url,{signal:ctrl.signal});clearTimeout(tid);if(!r.ok)throw new Error('HTTP '+r.status);return await r.json();}
    catch(e){if(i===retries)return null;await new Promise(function(res){setTimeout(res,1000*(i+1));});}
  }
  return null;
}
function extractItems(r){if(!r)return null;if(r.success===true&&Array.isArray(r.data))return r.data;if(Array.isArray(r.data))return r.data;if(Array.isArray(r))return r;return null;}

function setPlatformBadge(platform,state,count){
  var el=document.getElementById('plb-'+platform);if(!el)return;
  var labels={doc:'Online News',twit:'Twitter',fb:'Facebook',ig:'Instagram',ytb:'YouTube',tiktok:'TikTok'};
  if(state==='done'){el.className='plb-item done';el.innerHTML='<span>✓</span> '+labels[platform]+' <strong>('+fmtN(count)+')</strong>';}
  else if(state==='error'){el.className='plb-item error';el.innerHTML='<span>✗</span> '+labels[platform]+' (0)';}
  if(loadingPlatforms.size===0){setTimeout(function(){var bar=document.getElementById('platformLoadingBar');if(bar)bar.style.display='none';},1200);}
}
function updateProgressBar(){var done=PLAT_KEYS.length-loadingPlatforms.size;var pct=Math.round((done/PLAT_KEYS.length)*100);var bar=document.getElementById('loadProgress');if(bar)bar.style.width=pct+'%';if(pct>=100)setTimeout(function(){if(bar){bar.style.transition='opacity .5s';bar.style.opacity='0';}},800);}
function updateLazyLoadRow(){var row=document.getElementById('lazyLoadRow');var txt=document.getElementById('lazyLoadText');if(!row)return;if(loadingPlatforms.size>0){var names=[...loadingPlatforms].map(function(p){return PLATFORM_CFG[p]?PLATFORM_CFG[p].label:p;}).join(', ');if(txt)txt.textContent='Loading '+names+'…';row.classList.add('show');}else{row.classList.remove('show');}}
function updateTabCounts(){['all','doc','twit','fb','ig','ytb','tiktok'].forEach(function(k){var el=document.getElementById('tc-'+k);if(!el)return;var loading=loadingPlatforms.has(k);var count=k==='all'?store.all.length:(store[k]||[]).length;if(loading&&count===0)el.innerHTML='<span class="tab-spinner"></span>';else if(loading&&count>0)el.innerHTML=fmtN(count)+'<span class="tab-spinner" style="margin-left:4px"></span>';else el.innerHTML=fmtN(count);});}

var fallbackStore={twit:[],fb:[],ig:[],ytb:[],tiktok:[]};
function platformReady(platform,items,isError){
  store[platform]=items||[];
  store.all=PLAT_KEYS.reduce(function(acc,k){return acc.concat(store[k]||[]);}, []).sort(function(a,b){return new Date(b.date_created)-new Date(a.date_created);});
  loadingPlatforms.delete(platform);
  if(isError)errorPlatforms.add(platform);
  setPlatformBadge(platform,isError&&items.length===0?'error':'done',items.length);
  updateProgressBar();updateTabCounts();updateLazyLoadRow();
  if(!firstRenderDone&&store.all.length>0){firstRenderDone=true;document.getElementById('tblLoading').style.display='none';buildFiltered();renderTable();}
  else if(firstRenderDone&&(activeTab==='all'||activeTab===platform)){buildFiltered();renderTable();}
  renderStats();renderChart();
}
function waitFallback(key,cb,maxMs){maxMs=maxMs||25000;var t=Date.now();var iv=setInterval(function(){var hasFallback=fallbackStore[key]&&fallbackStore[key].length>0;var timedOut=Date.now()-t>maxMs;if(hasFallback||timedOut||mentionsFetchDone){clearInterval(iv);cb(fallbackStore[key]||[]);}},100);}

if(PID&&SD&&ED){
  var BASE='/mk/api/news';var XB='/mk/api/x';
  var Q='project_id='+PID+'&start_date='+SD+'&end_date='+ED+'&rows=2000&start=0';
  safeGet(BASE+'/mentions?'+Q,2).then(function(r){mentionsFetchDone=true;var all=extractItems(r);if(!all||!Array.isArray(all)){platformReady('doc',[],true);return;}var buckets={doc:[],twit:[],fb:[],ig:[],ytb:[],tiktok:[]};all.forEach(function(item){var p=detectPlatform(item);buckets[p].push(norm(item,p));});platformReady('doc',buckets.doc);['twit','fb','ig','ytb','tiktok'].forEach(function(p){fallbackStore[p]=buckets[p];});}).catch(function(){mentionsFetchDone=true;platformReady('doc',[],true);});
  safeGet(XB+'/most-status?'+Q+'&media=all&mention_type=view_all',2).then(function(r){var items=extractItems(r);if(items&&items.length>0)platformReady('twit',items.map(function(m){return norm(m,'twit');}));else waitFallback('twit',function(fb){platformReady('twit',fb,!items);});}).catch(function(){waitFallback('twit',function(fb){platformReady('twit',fb,true);});});
  safeGet(BASE+'/fb-top-status?'+Q+'&sub=fblike',2).then(function(r){var items=extractItems(r);if(items&&items.length>0)platformReady('fb',items.map(function(m){return m._platform?m:norm(m,'fb');}));else waitFallback('fb',function(fb){platformReady('fb',fb,!items);});}).catch(function(){waitFallback('fb',function(fb){platformReady('fb',fb,true);});});
  safeGet(BASE+'/ig-top-status?'+Q+'&sub=postbylike',2).then(function(r){var items=extractItems(r);if(items&&items.length>0)platformReady('ig',items.map(function(m){return m._platform?m:norm(m,'ig');}));else waitFallback('ig',function(fb){platformReady('ig',fb,!items);});}).catch(function(){waitFallback('ig',function(fb){platformReady('ig',fb,true);});});
  safeGet(BASE+'/tiktok-top-status?'+Q+'&sub=postbylike',2).then(function(r){var items=extractItems(r);if(items&&items.length>0)platformReady('tiktok',items.map(function(m){return m._platform?m:norm(m,'tiktok');}));else waitFallback('tiktok',function(fb){platformReady('tiktok',fb,!items);});}).catch(function(){waitFallback('tiktok',function(fb){platformReady('tiktok',fb,true);});});
  safeGet(BASE+'/ytb-top-status?'+Q,2).then(function(r){var items=extractItems(r);if(items&&items.length>0)platformReady('ytb',items.map(function(m){return m._platform?m:norm(m,'ytb');}));else waitFallback('ytb',function(fb){platformReady('ytb',fb,!items);},30000);}).catch(function(){waitFallback('ytb',function(fb){platformReady('ytb',fb,true);},30000);});
}

// ═══════════════════════════════════════════════
// STATS
// ═══════════════════════════════════════════════
function renderStats(){
  var platVals=PLAT_CFG_DONUT.map(function(p){return(store[p.key]||[]).length;});
  var platTotal=platVals.reduce(function(a,b){return a+b;},0);
  document.getElementById('donutTotalPlatform').textContent=fmtN(platTotal);
  PLAT_CFG_DONUT.forEach(function(p,i){var v=platVals[i];var pct=platTotal>0?((v/platTotal)*100).toFixed(1):'0.0';var vEl=document.getElementById('dlval-'+p.key);var pEl=document.getElementById('dlpct-'+p.key);if(vEl)vEl.textContent=fmtN(v);if(pEl)pEl.textContent=pct+'%';});
  document.getElementById('skPlatform').style.display='none';
  var cvP=document.getElementById('donutPlatform');cvP.style.display='block';
  if(chartPlatformInstance)chartPlatformInstance.destroy();
  chartPlatformInstance=new Chart(cvP.getContext('2d'),{type:'doughnut',data:{labels:PLAT_CFG_DONUT.map(function(p){return p.label;}),datasets:[{data:platVals,backgroundColor:PLAT_CFG_DONUT.map(function(p){return p.color;}),borderColor:'#fff',borderWidth:3,hoverOffset:8}]},options:{responsive:false,cutout:'68%',plugins:{legend:{display:false},tooltip:{backgroundColor:'#1a202c',padding:12,cornerRadius:10,callbacks:{label:function(ctx){var t=platVals.reduce(function(a,b){return a+b;},0);return' '+ctx.label+': '+fmtN(ctx.parsed)+'('+(t>0?((ctx.parsed/t)*100).toFixed(1):0)+'%)';}}}}}}); 
  var pos=store.all.filter(function(m){return m.class_sentiment==='1';}).length;
  var neg=store.all.filter(function(m){return m.class_sentiment==='-1';}).length;
  var neu=store.all.length-pos-neg;var sentTotal=store.all.length;
  document.getElementById('donutTotalSent').textContent=fmtN(sentTotal);
  [['pos',pos],['neg',neg],['neu',neu]].forEach(function(arr){var pct=sentTotal>0?((arr[1]/sentTotal)*100).toFixed(1):'0.0';var vEl=document.getElementById('dlval-'+arr[0]);var pEl=document.getElementById('dlpct-'+arr[0]);if(vEl)vEl.textContent=fmtN(arr[1]);if(pEl)pEl.textContent=pct+'%';});
  document.getElementById('skSentiment').style.display='none';
  var cvS=document.getElementById('donutSentiment');cvS.style.display='block';
  if(chartSentimentInstance)chartSentimentInstance.destroy();
  chartSentimentInstance=new Chart(cvS.getContext('2d'),{type:'doughnut',data:{labels:['Positive','Negative','Neutral'],datasets:[{data:[pos,neg,neu],backgroundColor:['#16a34a','#ef4444','#94a3b8'],borderColor:'#fff',borderWidth:3,hoverOffset:8}]},options:{responsive:false,cutout:'68%',plugins:{legend:{display:false},tooltip:{backgroundColor:'#1a202c',padding:12,cornerRadius:10,callbacks:{label:function(ctx){return' '+ctx.label+': '+fmtN(ctx.parsed)+'('+(sentTotal>0?((ctx.parsed/sentTotal)*100).toFixed(1):0)+'%)';}}}}}});
}

// ═══════════════════════════════════════════════
// CHART
// ═══════════════════════════════════════════════
function setChartMode(mode){chartMode=mode;['line','area','bar','log'].forEach(function(m){var btn=document.getElementById('chartBtn'+m.charAt(0).toUpperCase()+m.slice(1));if(btn)btn.classList.toggle('active',m===mode);});renderChart();}
function renderChart(){
  var canvas=document.getElementById('volChart');var sk=document.getElementById('chartSk');if(!canvas)return;
  var start=new Date(SD),end=new Date(ED);chartDates=[];
  for(var d=new Date(start);d<=end;d.setDate(d.getDate()+1))chartDates.push(d.toISOString().split('T')[0]);
  var isLog=chartMode==='log',isBar=chartMode==='bar',isArea=chartMode==='area';
  var datasets=PLAT_KEYS.map(function(p){var cfg=PLATFORM_CFG[p],dayMap={};(store[p]||[]).forEach(function(m){if(!m.date_created)return;var day=(m.date_created+'').split('T')[0].split(' ')[0];dayMap[day]=(dayMap[day]||0)+1;});var data=chartDates.map(function(d){return dayMap[d]||0;});var rawData=isLog?data.map(function(v){return v>0?v:0.1;}):data;return{label:cfg.label,data:rawData,borderColor:cfg.color,backgroundColor:isBar?cfg.color+'cc':isArea?cfg.color+'33':cfg.color+'15',borderWidth:isBar?0:2,tension:isBar?0:0.4,fill:isArea,pointRadius:isBar?0:(data.length>30?3:5),pointHoverRadius:10,pointBackgroundColor:cfg.color,pointBorderColor:'#fff',pointBorderWidth:2,pointHitRadius:isBar?0:12,hidden:hiddenPlatforms.has(p)};});
  document.getElementById('chartLegend').innerHTML=PLAT_KEYS.map(function(p){var cfg=PLATFORM_CFG[p],total=(store[p]||[]).length;var off=hiddenPlatforms.has(p),loading=loadingPlatforms.has(p);return'<div class="legend-item" onclick="togglePlatform(\''+p+'\')" style="opacity:'+(off?'.35':'1')+'"><div class="legend-dot" style="background:'+cfg.color+'"></div><span>'+cfg.label+'</span><span class="legend-cnt" style="color:'+cfg.color+';background:'+cfg.color+'18">'+(loading&&total===0?'…':fmtN(total))+'</span></div>';}).join('');
  if(chartInstance)chartInstance.destroy();
  var yConfig=isLog?{type:'logarithmic',beginAtZero:false,min:0.1,grid:{color:'rgba(0,0,0,.04)'},ticks:{color:'#94a3b8',font:{size:11},callback:function(v){return v>=1?fmtN(Math.round(v)):'';}}}: {beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'},ticks:{color:'#94a3b8',font:{size:11},precision:0}};
  chartInstance=new Chart(canvas.getContext('2d'),{type:isBar?'bar':'line',data:{labels:chartDates.map(function(d){var dt=new Date(d+'T00:00:00');return dt.toLocaleDateString('en-US',{month:'short',day:'numeric'});}),datasets:datasets},options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'nearest',intersect:true},onHover:function(event,elements){if(event.native&&event.native.target){event.native.target.style.cursor=elements.length?'pointer':'default';}},onClick:function(event,elements){if(!elements||!elements.length){closeChartPopup();return;}var el=elements[0];var dateStr=chartDates[el.index];var platKey=PLAT_KEYS[el.datasetIndex]||null;if(platKey&&hiddenPlatforms.has(platKey)){closeChartPopup();return;}openChartPopup(dateStr,platKey,event.native.clientX,event.native.clientY);},plugins:{legend:{display:false},tooltip:{backgroundColor:'#1e293b',titleColor:'#e2e8f0',bodyColor:'#cbd5e1',footerColor:'#94a3b8',padding:14,cornerRadius:10,displayColors:true,boxWidth:10,boxHeight:10,mode:'nearest',intersect:true,filter:function(item){return Math.round(item.parsed.y)>0;},callbacks:{title:function(items){return items[0]?items[0].label:'';},label:function(ctx){var v=Math.round(ctx.parsed.y);return v>0?'  '+ctx.dataset.label+': '+fmtN(v):null;},footer:function(items){var total=items.reduce(function(s,i){return s+Math.round(i.parsed.y);},0);return total>0?'Total: '+fmtN(total)+'  ·  Klik dot untuk detail':'0';}}}},scales:{y:yConfig,x:{stacked:isBar,grid:{display:false},ticks:{color:'#94a3b8',font:{size:11},maxRotation:45,autoSkip:true,maxTicksLimit:14}}}}});
  sk.style.display='none';canvas.style.display='block';
}
function togglePlatform(p){if(hiddenPlatforms.has(p))hiddenPlatforms.delete(p);else hiddenPlatforms.add(p);renderChart();}

// ═══════════════════════════════════════════════
// CHART POPUP
// ═══════════════════════════════════════════════
var _popupEl=null,_popupPlatformFilter=null,_popupDateFilter=null;
var _cpCache={};
function _buildPopupDom(){
  if(_popupEl)return;
  _popupEl=document.createElement('div');_popupEl.className='chart-popup';_popupEl.id='chartPopup';
  _popupEl.innerHTML='<div class="chart-popup-header"><div class="chart-popup-title"><div class="chart-popup-platform-dot" id="cpDot"></div><span id="cpTitle">Mentions</span></div><button class="chart-popup-close" onclick="closeChartPopup()">×</button></div><div class="chart-popup-date-bar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;flex-shrink:0"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span id="cpDate">—</span><span class="chart-popup-count-badge" id="cpCount">0</span><span>mentions</span></div><div class="chart-popup-list" id="cpList"></div><div class="chart-popup-footer" id="cpFooter"><button class="chart-popup-footer-btn" onclick="popupViewAll()">Lihat semua di tabel →</button></div>';
  document.body.appendChild(_popupEl);
  document.addEventListener('mousedown',function(e){if(_popupEl&&_popupEl.classList.contains('show')&&!_popupEl.contains(e.target)&&!e.target.closest('#volChart')){closeChartPopup();}},false);
}
function _positionPopup(x,y){var pw=380,ph=520,vw=window.innerWidth,vh=window.innerHeight;var left=x+16,top=y-40;if(left+pw>vw-12)left=x-pw-16;if(top+ph>vh-12)top=vh-ph-12;if(top<8)top=8;if(left<8)left=8;_popupEl.style.left=left+'px';_popupEl.style.top=top+'px';}
function _sentCls(s){var n=normSentiment(s);if(n==='1')return'sp';if(n==='-1')return'sn';return'su';}
function _sentLbl(s){var n=normSentiment(s);if(n==='1')return'Pos';if(n==='-1')return'Neg';return'Net';}
function _buildCpAva(item){var nm=item.author_name||item.author_handle||item.hostname||'?';var initl=initials(nm);var safe=initl.replace(/\\/g,'').replace(/'/g,'').replace(/"/g,'');if(item.avatar_url&&item.avatar_url.startsWith('http')){return'<img src="'+esc(item.avatar_url)+'" onerror="this.parentElement.textContent=\''+safe+'\'">';}var hClean=(item.author_handle||'').replace(/^@/,'').trim();var svcMap={twit:'twitter',ig:'instagram',tiktok:'tiktok',ytb:'youtube'};if(svcMap[item._platform]&&hClean){return'<img src="https://unavatar.io/'+svcMap[item._platform]+'/'+encodeURIComponent(hClean)+'" onerror="this.parentElement.textContent=\''+safe+'\'">';}if(item._platform==='doc'&&item.hostname){var ch=item.hostname.replace(/^www\./,'');return'<img src="https://www.google.com/s2/favicons?domain='+encodeURIComponent(ch)+'&sz=64" onerror="this.parentElement.textContent=\''+safe+'\'" style="width:100%;height:100%;object-fit:contain;padding:4px;">';}return esc(initl);}

function openChartPopup(dateStr,platKey,x,y){
  _buildPopupDom();_popupDateFilter=dateStr;_popupPlatformFilter=platKey;
  var cfg=platKey?PLATFORM_CFG[platKey]:null;
  document.getElementById('cpDot').style.background=cfg?cfg.color:'var(--primary-green)';
  document.getElementById('cpTitle').textContent=(cfg?cfg.label:'All Platforms')+' — Mentions';
  var dtObj=new Date(dateStr+'T00:00:00');
  document.getElementById('cpDate').textContent=dtObj.toLocaleDateString('id-ID',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
  var src=platKey?(store[platKey]||[]):store.all;
  var items=src.filter(function(m){if(!m.date_created)return false;var day=(m.date_created+'').split('T')[0].split(' ')[0];return day===dateStr;}).sort(function(a,b){return new Date(b.date_created)-new Date(a.date_created);});
  document.getElementById('cpCount').textContent=fmtN(items.length);
  var list=document.getElementById('cpList');var footer=document.getElementById('cpFooter');list.scrollTop=0;
  var cacheKey=dateStr+'_'+(platKey||'all');
  _cpCache[cacheKey]=items;
  if(!items.length){list.innerHTML='<div style="padding:32px;text-align:center;color:#94a3b8;font-size:13px;">📭 Tidak ada mentions pada tanggal ini.</div>';footer.style.display='none';}
  else{
    footer.style.display='';var SHOW=50;
    var html=items.slice(0,SHOW).map(function(item,i){
      var dt=fmtDate(item.date_created);var dname=item.author_name||item.author_handle||'Unknown';
      var dotColor=(PLATFORM_CFG[item._platform]||{}).color||'#94a3b8';
      var platColor=(PLATFORM_CFG[item._platform]||{}).color||'#94a3b8';
      var platLabel=(PLATFORM_CFG[item._platform]||{}).label||item._platform||'';
      return'<div class="chart-popup-item" onclick="openMentionPreviewFromCache(\''+cacheKey+'\','+i+')">'
        +'<div class="chart-popup-item-ava">'+_buildCpAva(item)+'</div>'
        +'<div class="chart-popup-item-body">'
          +'<div class="chart-popup-item-author"><svg width="7" height="7" viewBox="0 0 8 8" style="flex-shrink:0"><circle cx="4" cy="4" r="4" fill="'+dotColor+'"/></svg>'+esc(dname)+'<span class="chart-popup-item-time">'+dt.t+'</span></div>'
          +'<div class="chart-popup-item-text">'+esc(item.content||'(tidak ada konten)')+'</div>'
          +'<div class="chart-popup-item-meta"><span class="chart-popup-item-sent sent-badge '+_sentCls(item.class_sentiment)+'">'+_sentLbl(item.class_sentiment)+'</span>'
          +'<span style="padding:1px 7px;border-radius:10px;font-size:9px;font-weight:800;background:'+platColor+'20;color:'+platColor+';border:1px solid '+platColor+'40">'+platLabel+'</span></div>'
        +'</div></div>';
    }).join('');
    if(items.length>SHOW)html+='<div style="padding:9px 16px;text-align:center;font-size:11px;font-weight:600;color:var(--text-secondary);background:var(--bg-gray-50);border-top:1px dashed var(--border-gray)">+'+fmtN(items.length-SHOW)+' lainnya</div>';
    list.innerHTML=html;
  }
  _positionPopup(x,y);_popupEl.classList.add('show');
}
function openMentionPreviewFromCache(cacheKey,idx){var items=_cpCache[cacheKey];if(!items||!items[idx])return;closeChartPopup();openMentionPreview(items[idx]);}
function closeChartPopup(){if(_popupEl)_popupEl.classList.remove('show');}
function popupViewAll(){closeChartPopup();var tab=_popupPlatformFilter&&_popupPlatformFilter!=='all'?_popupPlatformFilter:'all';switchTab(tab);setTimeout(function(){var tbl=document.querySelector('.do-card:last-child');if(tbl)tbl.scrollIntoView({behavior:'smooth',block:'start'});},150);}
window.closeChartPopup=closeChartPopup;window.popupViewAll=popupViewAll;
window.openMentionPreviewFromCache=openMentionPreviewFromCache;

// ═══════════════════════════════════════════════
// TABLE
// ═══════════════════════════════════════════════
function switchTab(tab){activeTab=tab;q='';page=1;document.getElementById('searchInput').value='';document.querySelectorAll('.media-tab').forEach(function(b){b.classList.toggle('active',b.dataset.tab===tab);});buildFiltered();renderTable();}
function doSearch(){q=document.getElementById('searchInput').value.toLowerCase();page=1;buildFiltered();renderTable();}
function buildFiltered(){var src=activeTab==='all'?store.all:(store[activeTab]||[]);filtered=q?src.filter(function(m){return(m.content||'').toLowerCase().includes(q)||(m.author_name||'').toLowerCase().includes(q)||(m.author_handle||'').toLowerCase().includes(q)||(m.hostname||'').toLowerCase().includes(q);}):src.slice();}

function _tblAva(item,dname,domain,initl){
  var hClean=(item.author_handle||'').replace(/^@/,'').trim();
  if(item.avatar_url&&item.avatar_url.startsWith('http'))return avaImg(item.avatar_url,dname,initl);
  switch(item._platform){
    case'twit':if(hClean)return avaImg('https://unavatar.io/twitter/'+encodeURIComponent(hClean),dname,initl);break;
    case'ig':if(hClean)return avaImg('https://unavatar.io/instagram/'+encodeURIComponent(hClean),dname,initl);break;
    case'tiktok':if(hClean)return avaImg('https://unavatar.io/tiktok/'+encodeURIComponent(hClean),dname,initl);break;
    case'ytb':if(hClean)return avaImg('https://unavatar.io/youtube/'+encodeURIComponent(hClean),dname,initl);break;
    case'doc':if(domain){var safe2=initl.replace(/'/g,'').replace(/"/g,'');return'<img src="https://www.google.com/s2/favicons?domain='+encodeURIComponent(domain)+'&sz=64" alt="'+esc(dname)+'" onerror="_avaFail(this,\''+safe2+'\')" style="width:100%;height:100%;object-fit:contain;padding:4px">';}break;
  }
  return esc(initl);
}

function renderTable(){
  var loading=document.getElementById('tblLoading');var table=document.getElementById('mainTbl');var empty=document.getElementById('emptyState');var tbody=document.getElementById('tblBody');
  loading.style.display='none';
  if(!filtered.length){
    if(loadingPlatforms.size>0&&store.all.length===0){table.style.display='none';empty.style.display='none';loading.style.display='block';return;}
    table.style.display='none';empty.style.display='block';document.getElementById('pager').style.display='none';document.getElementById('tblSub').textContent='No mentions found';return;
  }
  var from=(page-1)*PER;var slice=filtered.slice(from,from+PER);
  tbody.innerHTML=slice.map(function(item,i){
    var rank=from+i+1;var absIdx=from+i;
    var dt=fmtDate(item.date_created);
    var handle=item.author_handle||(item.hostname?item.hostname.replace('www.','').split('.')[0]:'')||'';
    var dname=item.author_name||handle||'Unknown';
    var domain=item.hostname?item.hostname.replace('www.',''):'';
    var initl=initials(item.author_name||domain||handle||'?');
    var avaInner=_tblAva(item,dname,domain,initl);
    var primaryVal=0,primaryLabel='';
    if(item._platform==='twit'){if(item.num_retweeted>0){primaryVal=item.num_retweeted;primaryLabel='Retweets';}else if(item.num_likes>0){primaryVal=item.num_likes;primaryLabel='Likes';}}
    else if(item._platform==='ytb'){if(item.num_views>0){primaryVal=item.num_views;primaryLabel='Views';}else if(item.num_likes>0){primaryVal=item.num_likes;primaryLabel='Likes';}}
    else if(item._platform==='tiktok'){if(item.num_views>0){primaryVal=item.num_views;primaryLabel='Views';}else if(item.num_likes>0){primaryVal=item.num_likes;primaryLabel='Likes';}}
    else if(item._platform==='ig'){if(item.num_likes>0){primaryVal=item.num_likes;primaryLabel='Likes';}else if(item.num_views>0){primaryVal=item.num_views;primaryLabel='Views';}}
    else if(item._platform==='fb'){if(item.num_likes>0){primaryVal=item.num_likes;primaryLabel='Reactions';}else if(item.num_shares>0){primaryVal=item.num_shares;primaryLabel='Shares';}}
    var primaryHtml=primaryVal>0?'<div class="eng-primary-val">'+fmtN(primaryVal)+'</div><div class="eng-primary-lbl">'+primaryLabel+'</div>':item._platform==='doc'?'<div style="font-size:10px;font-weight:600;color:var(--text-secondary);line-height:1.4">Online<br>Article</div>':'<div class="eng-empty">—</div>';
    var secParts=[];
    if(item._platform==='twit'){if(item.num_likes>0)secParts.push(fmtN(item.num_likes)+'&thinsp;❤');if(item.num_comments>0)secParts.push(fmtN(item.num_comments)+'&thinsp;💬');if(item.num_views>0)secParts.push(fmtN(item.num_views)+'&thinsp;👁');}
    else if(item._platform==='ytb'||item._platform==='tiktok'){if(item.num_likes>0)secParts.push(fmtN(item.num_likes)+'&thinsp;❤');if(item.num_comments>0)secParts.push(fmtN(item.num_comments)+'&thinsp;💬');if(item.num_shares>0)secParts.push(fmtN(item.num_shares)+'&thinsp;↗');}
    else if(item._platform==='ig'||item._platform==='fb'){if(item.num_comments>0)secParts.push(fmtN(item.num_comments)+'&thinsp;💬');if(item.num_shares>0)secParts.push(fmtN(item.num_shares)+'&thinsp;↗');if(item.num_views>0)secParts.push(fmtN(item.num_views)+'&thinsp;👁');}
    var metaHtml='';
    if(domain)metaHtml='<span>'+esc(domain)+'</span>';
    metaHtml+=(metaHtml?'&ensp;·&ensp;':'')
      +'<button type="button" class="view-link" data-idx="'+absIdx+'">'
      +'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>'
      +' Preview</button>';
    return'<tr>'
      +'<td class="no-cell">'+rank+'</td>'
      +'<td class="media-cell">'+mediaBadge(item._platform)+'</td>'
      +'<td><span class="type-badge">'+esc(item.mention_type||'-')+'</span></td>'
      +'<td class="content-cell"><div class="mention-text">'+esc(item.content||'No content')+'</div><div class="mention-meta">'+metaHtml+'</div></td>'
      +'<td class="eng-cell">'+primaryHtml+(secParts.length?'<div class="eng-secondary">'+secParts.map(function(p){return'<div class="eng-sec-item">'+p+'</div>';}).join('')+'</div>':'')+'</td>'
      +'<td class="author-cell"><div class="author-wrap"><div class="ava">'+avaInner+'</div><div><div class="aname" title="'+esc(dname)+'">'+esc(dname)+'</div><div class="ahandle">'+esc(handle)+'</div></div></div></td>'
      +'<td class="date-cell"><div class="date-main">'+dt.d+'</div><div class="date-time">'+dt.t+'</div></td>'
      +'<td class="sent-cell">'+sentBadge(item.class_sentiment,absIdx)+'</td>'
      +'</tr>';
  }).join('');
  table.style.display='table';empty.style.display='none';
  var totalPages=Math.ceil(filtered.length/PER);var toIdx=Math.min(page*PER,filtered.length);
  document.getElementById('tblSub').textContent='Showing '+fmtN(from+1)+'–'+fmtN(toIdx)+' of '+fmtN(filtered.length)+' mentions'+(loadingPlatforms.size>0?' (still loading…)':'');
  renderPager(totalPages);
}

// ═══════════════════════════════════════════════
// PAGINATION
// ═══════════════════════════════════════════════
function renderPager(total){
  var wrap=document.getElementById('pager');if(total<=1){wrap.style.display='none';return;}
  var from=(page-1)*PER+1,to=Math.min(page*PER,filtered.length);
  function range(cur,tot){if(tot<=7)return Array.from({length:tot},function(_,i){return i+1;});if(cur<=4)return[1,2,3,4,5,'…',tot];if(cur>=tot-3)return[1,'…',tot-4,tot-3,tot-2,tot-1,tot];return[1,'…',cur-1,cur,cur+1,'…',tot];}
  var h='<div class="pager-info">Showing '+fmtN(from)+'–'+fmtN(to)+' of '+fmtN(filtered.length)+'</div><div class="pager-btns">';
  h+='<button class="pbtn" onclick="goPage('+(page-1)+')" '+(page===1?'disabled':'')+'><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="15 18 9 12 15 6"/></svg></button>';
  range(page,total).forEach(function(p){h+=p==='…'?'<button class="pbtn" disabled style="cursor:default;font-size:14px">…</button>':'<button class="pbtn '+(p===page?'active':'')+'" onclick="goPage('+p+')">'+p+'</button>';});
  h+='<button class="pbtn" onclick="goPage('+(page+1)+')" '+(page===total?'disabled':'')+'><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="9 18 15 12 9 6"/></svg></button></div>';
  wrap.innerHTML=h;wrap.style.display='flex';
}
function goPage(p){var tot=Math.ceil(filtered.length/PER);if(p<1||p>tot)return;page=p;renderTable();document.querySelector('.do-card:last-child').scrollIntoView({behavior:'smooth',block:'start'});}

// ═══════════════════════════════════════════════
// ╔══════════════════════════════════════════════╗
// ║         SENTIMENT EDITOR — TABEL            ║
// ╚══════════════════════════════════════════════╝
// ═══════════════════════════════════════════════
var _activeSentDrop=null;

function toggleSentDrop(e,absIdx){
  e.stopPropagation();
  var drop=document.getElementById('sed'+absIdx);if(!drop)return;
  // Tutup dropdown lain yang terbuka
  if(_activeSentDrop&&_activeSentDrop!==drop){
    _activeSentDrop.classList.remove('open');
  }
  drop.classList.toggle('open');
  _activeSentDrop=drop.classList.contains('open')?drop:null;
}

// Tutup dropdown kalau klik di luar
document.addEventListener('click',function(e){
  if(_activeSentDrop&&!e.target.closest('.sent-edit-wrap')){
    _activeSentDrop.classList.remove('open');
    _activeSentDrop=null;
  }
},false);

function changeSent(e,absIdx,newSent){
  e.stopPropagation();
  var item=filtered[absIdx];if(!item)return;
  // Tutup dropdown
  var drop=document.getElementById('sed'+absIdx);
  if(drop)drop.classList.remove('open');
  _activeSentDrop=null;
  // Simpan nilai lama untuk rollback
  var oldSent=item.class_sentiment;
  if(oldSent===newSent)return; // tidak ada perubahan
  // Update memory
  _syncSentimentInStore(item,newSent);
  // Update DOM badge langsung (tanpa re-render tabel)
  _updateSentBadgeDOM(absIdx,newSent);
  // Tampilkan flash
  _showSentFlash(absIdx,newSent);
  // Update donut chart
  renderStats();
  // Sync ke modal preview jika item yang sama sedang dibuka
  if(_mpvCurrentItem&&_mpvCurrentItem===item){
    _mpvUpdateSentUI(newSent);
  }
  // Kirim ke API
  _apiUpdateSentiment(item,newSent,oldSent,function(ok){
    if(!ok){
      // Rollback
      _syncSentimentInStore(item,oldSent);
      _updateSentBadgeDOM(absIdx,oldSent);
      renderStats();
      if(_mpvCurrentItem&&_mpvCurrentItem===item)_mpvUpdateSentUI(oldSent);
    }
  });
}

function _syncSentimentInStore(item,newSent){
  item.class_sentiment=newSent;
  // Sync ke semua store array (match by reference — sudah sama karena store.all berisi object yang sama)
  // Tidak perlu iterasi tambahan karena JS pass object by reference
}

function _updateSentBadgeDOM(absIdx,sent){
  var badge=document.getElementById('seb'+absIdx);
  var drop=document.getElementById('sed'+absIdx);
  if(badge){
    var cls=sent==='1'?'sp':sent==='-1'?'sn':'su';
    var lbl=sent==='1'?'Positive':sent==='-1'?'Negative':'Neutral';
    badge.className='sent-badge '+cls;
    badge.textContent=lbl;
  }
  if(drop){
    drop.querySelectorAll('.sent-opt').forEach(function(btn){btn.classList.remove('so-active');});
    var map={'1':'.so-pos','-1':'.so-neg','0':'.so-neu'};
    var target=drop.querySelector(map[sent]);
    if(target)target.classList.add('so-active');
  }
}

function _showSentFlash(absIdx,sent){
  var wrap=document.getElementById('sew'+absIdx);
  if(!wrap)return;
  var lbl=sent==='1'?'✓ Positive':sent==='-1'?'✗ Negative':'– Neutral';
  var fl=document.createElement('div');
  fl.className='sent-flash';fl.textContent=lbl;
  wrap.appendChild(fl);
  setTimeout(function(){if(fl.parentNode)fl.parentNode.removeChild(fl);},950);
}

// ═══════════════════════════════════════════════
// API CALL — Update Sentiment
// Route: POST /mk/api/news/update-sentiment
// ═══════════════════════════════════════════════
function _apiUpdateSentiment(item,newSent,oldSent,callback){
  var payload={
    project_id: PID,
    doc_id:     item._rawId||'',
    platform:   item._platform||'doc',
    sentiment:  newSent,
    url:        item.url||''
  };
  fetch('/mk/api/news/update-sentiment',{
    method:'POST',
    headers:{
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':CSRF,
      'Accept':'application/json'
    },
    body:JSON.stringify(payload)
  })
  .then(function(r){
    if(!r.ok)throw new Error('HTTP '+r.status);
    return r.json();
  })
  .then(function(data){
    console.log('[Sentiment] ✅ Saved:',data);
    if(callback)callback(true);
  })
  .catch(function(err){
    console.warn('[Sentiment] ❌ Failed:',err);
    if(callback)callback(false);
  });
}

// ═══════════════════════════════════════════════
// ╔══════════════════════════════════════════════╗
// ║      SENTIMENT EDITOR — MODAL PREVIEW       ║
// ╚══════════════════════════════════════════════╝
// ═══════════════════════════════════════════════
var _mpvCurrentItem=null;
var _mpvCurrentFilteredIdx=-1;

function mpvChangeSent(newSent){
  if(!_mpvCurrentItem)return;
  var oldSent=_mpvCurrentItem.class_sentiment;
  if(oldSent===newSent)return;
  var statusEl=document.getElementById('mpvSentStatus');
  if(statusEl)statusEl.innerHTML='<span class="sent-saving-dot"></span> Saving…';
  // Update memory & UI segera (optimistic)
  _syncSentimentInStore(_mpvCurrentItem,newSent);
  _mpvUpdateSentUI(newSent);
  renderStats();
  // Sync badge di tabel jika row masih ada di DOM
  if(_mpvCurrentFilteredIdx>=0){
    _updateSentBadgeDOM(_mpvCurrentFilteredIdx,newSent);
  }
  // API
  _apiUpdateSentiment(_mpvCurrentItem,newSent,oldSent,function(ok){
    if(ok){
      if(statusEl)statusEl.innerHTML='<span style="color:#16a34a;font-weight:800">✓ Saved</span>';
      setTimeout(function(){if(statusEl)statusEl.textContent='';},1800);
    }else{
      // Rollback
      _syncSentimentInStore(_mpvCurrentItem,oldSent);
      _mpvUpdateSentUI(oldSent);
      renderStats();
      if(_mpvCurrentFilteredIdx>=0)_updateSentBadgeDOM(_mpvCurrentFilteredIdx,oldSent);
      if(statusEl)statusEl.innerHTML='<span style="color:#ef4444;font-weight:800">✗ Gagal</span>';
      setTimeout(function(){if(statusEl)statusEl.textContent='';},2000);
    }
  });
}

function _mpvUpdateSentUI(sent){
  // Update 3 tombol di modal
  var map={pos:'1',neg:'-1',neu:'0'};
  var clsMap={'1':'mpv-so-active-pos','-1':'mpv-so-active-neg','0':'mpv-so-active-neu'};
  ['pos','neg','neu'].forEach(function(k){
    var btn=document.getElementById('mpvSent'+k.charAt(0).toUpperCase()+k.slice(1));
    if(!btn)return;
    btn.className='mpv-sent-opt';
    if(map[k]===sent)btn.classList.add(clsMap[sent]);
  });
}

// ═══════════════════════════════════════════════
// MENTION PREVIEW MODAL
// ═══════════════════════════════════════════════
var MPV_CFG={
  doc:    {label:'Online News',bg:'#dbeafe',color:'#1e40af'},
  twit:   {label:'Twitter/X',  bg:'#e0f2fe',color:'#0369a1'},
  fb:     {label:'Facebook',   bg:'#e0e7ff',color:'#3730a3'},
  ig:     {label:'Instagram',  bg:'#fce7f3',color:'#be185d'},
  ytb:    {label:'YouTube',    bg:'#fee2e2',color:'#991b1b'},
  tiktok: {label:'TikTok',     bg:'#f1f5f9',color:'#374151'},
};

function _mpvFmt(n){return n?new Intl.NumberFormat('en-US').format(n):'0';}

function _mpvAva(item){
  var nm=item.author_name||item.author_handle||item.hostname||'?';
  var ini=initials(nm);var safe=ini.replace(/'/g,'').replace(/"/g,'');
  if(item.avatar_url&&item.avatar_url.startsWith('http'))return'<img src="'+item.avatar_url+'" onerror="this.parentElement.textContent=\''+safe+'\'" style="width:100%;height:100%;object-fit:cover;border-radius:50%">';
  var h=(item.author_handle||'').replace(/^@/,'').trim();
  var svc={twit:'twitter',ig:'instagram',tiktok:'tiktok',ytb:'youtube'}[item._platform];
  if(svc&&h)return'<img src="https://unavatar.io/'+svc+'/'+encodeURIComponent(h)+'" onerror="this.parentElement.textContent=\''+safe+'\'" style="width:100%;height:100%;object-fit:cover;border-radius:50%">';
  if(item._platform==='doc'&&item.hostname){var ch=item.hostname.replace(/^www\./,'');return'<img src="https://www.google.com/s2/favicons?domain='+encodeURIComponent(ch)+'&sz=64" onerror="this.parentElement.textContent=\''+safe+'\'" style="width:100%;height:100%;object-fit:contain;padding:4px">';}
  return ini;
}

function _mpvStats(item){
  var s=[];
  if(item.num_views>0)s.push({l:'Views',v:_mpvFmt(item.num_views)});
  if(item.num_likes>0)s.push({l:'Likes',v:_mpvFmt(item.num_likes)});
  if(item.num_comments>0)s.push({l:'Comments',v:_mpvFmt(item.num_comments)});
  if(item.num_shares>0)s.push({l:'Shares',v:_mpvFmt(item.num_shares)});
  if(item.num_retweeted>0)s.push({l:'Retweets',v:_mpvFmt(item.num_retweeted)});
  if(item.num_followers>0)s.push({l:'Followers',v:_mpvFmt(item.num_followers)});
  if(!s.length)return'';
  return'<div class="mpv-meta-grid">'+s.map(function(x){return'<div class="mpv-meta-card"><div class="mpv-meta-lbl">'+x.l+'</div><div class="mpv-meta-val">'+x.v+'</div></div>';}).join('')+'</div>';
}

function _mpvYtId(url){if(!url)return null;var m=url.match(/(?:v=|youtu\.be\/|embed\/)([A-Za-z0-9_-]{11})/);return m?m[1]:null;}
function _mpvTtId(url){if(!url)return null;var m=url.match(/video\/(\d+)/);return m?m[1]:null;}

function _mpvAddTab(tabsEl,bodyEl,id,label,isFirst,html){
  var btn=document.createElement('button');btn.className='mpv-view-tab'+(isFirst?' active':'');btn.textContent=label;
  btn.onclick=function(){tabsEl.querySelectorAll('.mpv-view-tab').forEach(function(b){b.classList.remove('active');});btn.classList.add('active');bodyEl.querySelectorAll('.mpv-panel').forEach(function(p){p.classList.remove('active');});var pnl=document.getElementById('mpvPanel-'+id);if(pnl)pnl.classList.add('active');};
  tabsEl.appendChild(btn);
  var pnl=document.createElement('div');pnl.className='mpv-panel'+(isFirst?' active':'');pnl.id='mpvPanel-'+id;pnl.innerHTML=html;bodyEl.appendChild(pnl);
}

function openMentionPreview(item){
  var backdrop=document.getElementById('mpvBackdrop');if(!backdrop)return;
  // Set current item & cari idx di filtered untuk sync badge tabel
  _mpvCurrentItem=item;
  _mpvCurrentFilteredIdx=filtered.indexOf(item);

  var plat=item._platform||'doc';
  var cfg=MPV_CFG[plat]||MPV_CFG.doc;
  var dname=item.author_name||item.author_handle||item.hostname||'Unknown';
  var handle=item.author_handle||(item.hostname?item.hostname.replace(/^www\./,''):'');
  var dtStr=item.date_created?(function(){try{var d=new Date(item.date_created);return d.toLocaleDateString('id-ID',{weekday:'short',year:'numeric',month:'short',day:'numeric'})+' · '+d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'});}catch(e){return item.date_created;}})():'';

  // Header
  var pill=document.getElementById('mpvPlatPill');pill.style.background=cfg.bg;pill.style.color=cfg.color;pill.textContent=cfg.label;
  document.getElementById('mpvTitle').textContent=item.content?item.content.slice(0,90)+(item.content.length>90?'…':''):'Mention Preview';
  var extBtn=document.getElementById('mpvExtBtn');extBtn.href=item.url||'#';extBtn.style.display=item.url?'inline-flex':'none';

  // Author
  document.getElementById('mpvAva').innerHTML=_mpvAva(item);
  document.getElementById('mpvAuthorName').textContent=dname;
  document.getElementById('mpvAuthorMeta').textContent=(handle&&handle!==dname?'@'+handle.replace(/^@/,'')+' · ':'')+dtStr;

  // Sentiment row — set UI sesuai sentiment item
  _mpvUpdateSentUI(item.class_sentiment);
  var statusEl=document.getElementById('mpvSentStatus');
  if(statusEl)statusEl.textContent='';

  // Reset tabs+body
  var tabsEl=document.getElementById('mpvViewTabs');var bodyEl=document.getElementById('mpvBody');
  tabsEl.innerHTML='';bodyEl.innerHTML='';

  var url=item.url||'';var ytId=_mpvYtId(url);var ttId=(plat==='tiktok'&&url)?_mpvTtId(url):null;
  var tabCount=0;

  if(ytId){
    _mpvAddTab(tabsEl,bodyEl,'video','▶ Video',true,
      '<div class="mpv-video-wrap"><iframe src="https://www.youtube.com/embed/'+ytId+'?rel=0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen></iframe></div>'
      +'<div class="mpv-fallback">'+_mpvStats(item)+'</div>');tabCount++;
  } else if(ttId){
    _mpvAddTab(tabsEl,bodyEl,'ttvid','▶ TikTok',true,
      '<div class="mpv-tiktok-wrap"><blockquote class="tiktok-embed" cite="'+url+'" data-video-id="'+ttId+'" style="max-width:605px;min-width:325px;"><section></section></blockquote></div>');
    tabCount++;
    if(!window._ttEmbedLoaded){window._ttEmbedLoaded=true;var ttSc=document.createElement('script');ttSc.src='https://www.tiktok.com/embed.js';ttSc.async=true;document.body.appendChild(ttSc);}
  } else if(plat==='doc'&&url){
    var uid='f'+Date.now();
    _mpvAddTab(tabsEl,bodyEl,'iframe','🌐 Preview Artikel',true,
      '<div class="mpv-iframe-wrap">'
      +'<div class="mpv-iframe-loader" id="mpvL'+uid+'"><div class="mpv-iframe-spinner"></div><span>Memuat artikel…</span><small style="color:#94a3b8;margin-top:3px">Beberapa situs mungkin memblokir preview</small></div>'
      +'<iframe src="'+esc(url)+'" sandbox="allow-scripts allow-same-origin allow-popups allow-forms" loading="lazy"'
      +' onload="this.classList.add(\'loaded\');var l=document.getElementById(\'mpvL'+uid+'\');if(l)l.classList.add(\'hidden\');"'
      +' onerror="var l=document.getElementById(\'mpvL'+uid+'\');if(l)l.innerHTML=\'<div style=\\\'text-align:center;padding:28px\\\'><div style=\\\'font-size:40px;margin-bottom:10px\\\'>🚫</div><b style=\\\'color:#374151\\\'>Situs ini memblokir preview</b><p style=\\\'font-size:12px;color:#64748b;margin-top:6px\\\'>Gunakan tombol Buka sumber di atas</p></div>\';"></iframe>'
      +'</div>');tabCount++;
  }

  // Tab Detail — selalu ada
  var isFirst=(tabCount===0);
  var HINTS={twit:'Twitter/X membatasi embed. Gunakan tombol "Buka sumber" untuk melihat tweet asli.',fb:'Facebook membatasi preview. Gunakan "Buka sumber".',ig:'Instagram membatasi preview. Gunakan "Buka sumber".'};
  var hintHtml=HINTS[plat]?'<div class="mpv-hint-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'+HINTS[plat]+'</div>':'';
  var urlBarHtml='';
  if(url){var dm='';try{dm=new URL(url).hostname.replace(/^www\./,'');}catch(e){}urlBarHtml='<a class="mpv-url-bar" href="'+esc(url)+'" target="_blank" rel="noopener"><img class="mpv-url-favicon" src="https://www.google.com/s2/favicons?domain='+encodeURIComponent(dm)+'&sz=32" onerror="this.style.display=\'none\'"><span class="mpv-url-text">'+esc(url)+'</span><svg viewBox="0 0 24 24" fill="none" stroke="#038047" stroke-width="2.5" style="width:13px;height:13px;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg></a>';}
  _mpvAddTab(tabsEl,bodyEl,'detail','📋 Detail',isFirst,
    '<div class="mpv-fallback">'+hintHtml+'<div class="mpv-content-text">'+esc(item.content||'(tidak ada konten)')+'</div>'+_mpvStats(item)+urlBarHtml+'</div>');

  tabsEl.style.display=tabsEl.children.length<=1?'none':'flex';
  backdrop.classList.add('open');
}

function closeMpv(){
  var bd=document.getElementById('mpvBackdrop');if(!bd)return;
  bd.classList.remove('open');
  _mpvCurrentItem=null;
  _mpvCurrentFilteredIdx=-1;
  setTimeout(function(){
    var b=document.getElementById('mpvBody');if(b)b.innerHTML='';
    var t=document.getElementById('mpvViewTabs');if(t)t.innerHTML='';
  },200);
}

// Close handlers
document.getElementById('mpvClose').onclick=closeMpv;
document.getElementById('mpvBackdrop').addEventListener('mousedown',function(e){if(e.target===this)closeMpv();});
document.addEventListener('keydown',function(e){
  if(e.key==='Escape'){
    var bd=document.getElementById('mpvBackdrop');
    if(bd&&bd.classList.contains('open')){closeMpv();}
  }
});

// ═══════════════════════════════════════════════
// EVENT DELEGATION — klik .view-link di tabel
// ═══════════════════════════════════════════════
document.addEventListener('click',function(e){
  var btn=e.target.closest('.view-link');
  if(!btn)return;
  e.preventDefault();e.stopPropagation();
  var idx=parseInt(btn.getAttribute('data-idx'),10);
  if(isNaN(idx)||idx<0||idx>=filtered.length)return;
  openMentionPreview(filtered[idx]);
},false);

window.switchTab=switchTab;window.doSearch=doSearch;window.goPage=goPage;
window.setChartMode=setChartMode;window.togglePlatform=togglePlatform;
window.openChartPopup=openChartPopup;window.openMentionPreview=openMentionPreview;window.closeMpv=closeMpv;
window.toggleSentDrop=toggleSentDrop;window.changeSent=changeSent;window.mpvChangeSent=mpvChangeSent;
</script>
@endsection