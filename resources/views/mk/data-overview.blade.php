@extends('mk.layouts.app')

@section('title', 'Data Overview - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green:       #038047;
    --primary-green-dark:  #026738;
    --primary-green-light: rgba(3,128,71,.08);
    --text-primary:        #1a202c;
    --text-secondary:      #64748b;
    --bg-white:            #ffffff;
    --bg-gray-50:          #f8fafc;
    --bg-gray-100:         #f1f5f9;
    --border-gray:         #e2e8f0;
    --shadow-sm:           0 1px 2px 0 rgba(0,0,0,.05);
    --shadow-md:           0 4px 6px -1px rgba(0,0,0,.1);
    --shadow-lg:           0 10px 15px -3px rgba(0,0,0,.1);
    --shadow-xl:           0 20px 40px -8px rgba(0,0,0,.18);
    --font:                'Poppins', -apple-system, sans-serif;
    --radius:              16px;
    --radius-sm:           12px;
    --radius-xs:           8px;
    --sp-xs:               8px;
    --sp-sm:               12px;
    --sp-md:               16px;
    --sp-lg:               24px;
    --sp-xl:               32px;
    --transition:          all 0.2s cubic-bezier(0.4,0,0.2,1);
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: var(--font); background: #f0f4f8; color: var(--text-primary); }

  .dashboard-container { padding: 24px; min-height: 100vh; max-width: 1600px; margin: 0 auto; }

  /* ── Page Header ── */
  .page-header { margin-bottom: 28px; display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
  .page-header-left h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; letter-spacing: -.4px; }
  .page-header-left p  { font-size: 14px; color: var(--text-secondary); font-weight: 500; margin: 0; }

  /* ── Export Button ── */
  .export-master-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-family: var(--font); font-size: 13px; font-weight: 600;
    cursor: pointer; transition: var(--transition); white-space: nowrap;
    box-shadow: 0 4px 14px rgba(0,0,0,.2);
  }
  .export-master-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.25); }
  .export-master-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

  /* ── Export Dropdown ── */
  .export-dropdown-wrap { position: relative; }
  .export-dropdown {
    position: absolute; top: calc(100% + 8px); right: 0;
    background: #fff; border: 1px solid var(--border-gray);
    border-radius: var(--radius); box-shadow: var(--shadow-xl);
    padding: 6px; min-width: 210px; z-index: 5000;
    display: none; animation: dropDown .18s ease-out;
  }
  .export-dropdown.show { display: block; }
  @keyframes dropDown { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }
  .export-dd-section { padding: 6px 10px 4px; font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; }
  .export-dd-btn {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 12px; border-radius: 9px;
    background: transparent; border: none; width: 100%; text-align: left;
    font-family: var(--font); font-size: 12px; font-weight: 600;
    color: var(--text-primary); cursor: pointer; transition: all .15s;
  }
  .export-dd-btn:hover { background: var(--primary-green-light); color: var(--primary-green); }
  .export-dd-btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
  .export-dd-divider { height: 1px; background: var(--border-gray); margin: 4px 0; }

  /* ── Filter Card ── */
  .filter-card { background: var(--bg-white); border-radius: var(--radius); padding: 20px 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray); }
  .filter-content { display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
  .filter-group { display: flex; flex-direction: column; gap: 6px; }
  .filter-label { font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .5px; }
  .filter-select { padding: 10px 14px; border: 1px solid var(--border-gray); border-radius: var(--radius-sm); font-family: var(--font); font-size: 14px; font-weight: 500; color: var(--text-primary); background: var(--bg-gray-50); outline: none; transition: var(--transition); min-width: 200px; cursor: pointer; }
  .filter-select:focus { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px var(--primary-green-light); }
  .date-picker-trigger { display: flex; align-items: center; gap: 10px; padding: 10px 16px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: var(--radius-sm); font-family: var(--font); font-size: 14px; font-weight: 500; color: var(--text-primary); cursor: pointer; transition: var(--transition); min-width: 300px; }
  .date-picker-trigger:hover { border-color: var(--primary-green); background: var(--bg-white); box-shadow: 0 0 0 3px var(--primary-green-light); }
  .date-picker-trigger svg { width: 16px; height: 16px; color: var(--text-secondary); flex-shrink: 0; }
  .date-picker-trigger span { flex: 1; text-align: left; }
  .apply-btn { display: flex; align-items: center; gap: 8px; padding: 10px 24px; background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); color: #fff; border: none; border-radius: var(--radius-sm); font-family: var(--font); font-size: 14px; font-weight: 600; cursor: pointer; transition: var(--transition); box-shadow: 0 4px 12px rgba(3,128,71,.2); white-space: nowrap; }
  .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.3); }
  .apply-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; }

  /* ── Date Picker Modal ── */
  .date-picker-modal { position: fixed; inset: 0; z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,.5); backdrop-filter: blur(8px); }
  .date-picker-modal.show { display: flex; }
  .date-picker-overlay { position: absolute; inset: 0; cursor: pointer; }
  .date-picker-container { position: relative; background: #fff; border-radius: var(--radius); box-shadow: 0 25px 50px rgba(0,0,0,.3); display: flex; max-width: 900px; width: 90%; max-height: 90vh; z-index: 10001; animation: dpUp .3s ease-out; }
  @keyframes dpUp { from { opacity:0; transform:translateY(20px) scale(.95); } to { opacity:1; transform:translateY(0) scale(1); } }
  .date-picker-sidebar { width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray); padding: 16px 12px; border-radius: var(--radius) 0 0 var(--radius); display: flex; flex-direction: column; gap: 4px; flex-shrink: 0; }
  .date-preset { padding: 10px 16px; background: transparent; border: none; border-radius: var(--radius-xs); font-family: var(--font); font-size: 13px; font-weight: 500; color: var(--text-primary); text-align: left; cursor: pointer; transition: var(--transition); }
  .date-preset:hover  { background: var(--bg-white); color: var(--primary-green); }
  .date-preset.active { background: var(--primary-green); color: #fff; }
  .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; overflow: hidden; }
  .date-picker-header  { display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px; }
  .nav-btn { width: 36px; height: 36px; border-radius: var(--radius-xs); background: var(--bg-gray-50); border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: var(--transition); flex-shrink: 0; }
  .nav-btn:hover { background: var(--primary-green); border-color: var(--primary-green); color: #fff; }
  .nav-btn svg { width: 20px; height: 20px; }
  .calendars-wrapper { display: flex; gap: 24px; flex: 1; min-height: 0; }
  .calendar { flex: 1; display: flex; flex-direction: column; min-width: 0; }
  .calendar-month { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-align: center; }
  .calendar-weekdays { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; margin-bottom: 8px; }
  .weekday { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
  .calendar-days { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; }
  .calendar-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 500; border-radius: var(--radius-xs); cursor: pointer; transition: var(--transition); color: var(--text-primary); background: transparent; border: none; padding: 0; font-family: var(--font); }
  .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
  .calendar-day.other-month { color: #cbd5e1; cursor: default; }
  .calendar-day.disabled    { color: #e2e8f0; cursor: not-allowed; }
  .calendar-day.today       { border: 2px solid var(--primary-green); }
  .calendar-day.selected    { background: var(--primary-green); color: #fff; }
  .calendar-day.in-range    { background: rgba(3,128,71,.1); color: var(--primary-green); }
  .date-picker-display { padding: 16px 20px; background: var(--bg-gray-50); border-radius: var(--radius-sm); text-align: center; margin-bottom: 20px; border: 1px solid var(--border-gray); }
  .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }
  .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }
  .cancel-btn, .apply-date-btn { padding: 10px 24px; border-radius: 10px; font-family: var(--font); font-size: 14px; font-weight: 600; cursor: pointer; transition: var(--transition); border: none; }
  .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
  .cancel-btn:hover { background: var(--border-gray); }
  .apply-date-btn { background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%); color: #fff; box-shadow: 0 4px 12px rgba(3,128,71,.2); }
  .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3,128,71,.3); }

  /* ── Base Card ── */
  .do-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    position: relative;
  }
  .do-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    opacity: 0; transition: opacity .3s;
  }
  .do-card:hover { box-shadow: var(--shadow-lg); border-color: rgba(3,128,71,.25); }
  .do-card:hover::before { opacity: 1; }
  .do-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: var(--sp-md) var(--sp-lg);
    border-bottom: 1px solid var(--border-gray);
    flex-shrink: 0;
  }
  .do-card-head-left { display: flex; align-items: center; gap: var(--sp-sm); }
  .do-head-icon {
    width: 36px; height: 36px;
    border-radius: var(--radius-sm);
    background: var(--primary-green-light);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .do-head-icon svg { width: 18px; height: 18px; fill: none; stroke: var(--primary-green); stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
  .do-card-title    { font-size: 15px; font-weight: 700; color: var(--text-primary); }
  .do-card-subtitle { font-size: 11px; color: #94a3b8; font-weight: 500; margin-top: 2px; }
  .do-badge {
    display: inline-flex; align-items: center;
    padding: 3px 10px; border-radius: 20px;
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    background: var(--bg-gray-100); color: var(--text-secondary);
    white-space: nowrap;
  }
  .do-card-body { padding: 20px; flex: 1; }
  .do-body-scroll { max-height: 220px; overflow-y: auto; }
  .do-body-scroll::-webkit-scrollbar { width: 4px; }
  .do-body-scroll::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 2px; }

  /* ── Grid Rows ── */
  .do-row-top {
    display: grid;
    grid-template-columns: 1fr 1fr 380px;
    gap: 20px;
    margin-bottom: 20px;
    align-items: stretch;
  }
  .do-row-mid {
    display: grid;
    grid-template-columns: 420px 1fr;
    gap: 20px;
    margin-bottom: 20px;
    align-items: stretch;
  }
  .do-mb20 { margin-bottom: 20px; }

  /* ── Tables ── */
  .do-tbl { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; }
  .do-tbl th { padding: 0 0 10px; text-align: left; font-size: 10px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid var(--border-gray); }
  .do-tbl td { padding: 10px 0; color: var(--text-primary); border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
  .do-tbl tbody tr:hover { background: #fafbfc; }
  .do-tbl tbody tr:last-child td { border-bottom: none; }
  .do-tbl-rank { font-weight: 800; color: var(--primary-green); width: 24px; font-size: 12px; }
  .do-tbl-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .do-tbl-num  { text-align: right; font-weight: 700; font-size: 12px; color: var(--text-secondary); }
  .topic-link  { color: var(--text-primary); text-decoration: none; transition: all .2s; display: block; cursor: pointer; }
  .topic-link:hover { color: var(--primary-green); text-decoration: underline; }

  /* ═══════════════════════════════════════════
     MENTION CARD
  ═══════════════════════════════════════════ */
  .mention-revised-body {
    display: flex;
    align-items: stretch;
    flex: 1;
    min-height: 260px;
  }
  .mention-chart-area {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--sp-lg) var(--sp-md);
    min-width: 0;
  }
  #chMentionPie {
    width: 100% !important;
    max-width: 220px;
    height: 220px !important;
  }
  .mention-stats-panel {
    width: 156px;
    flex-shrink: 0;
    border-left: 1px solid var(--border-gray);
    padding: var(--sp-lg) var(--sp-md);
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: var(--sp-md);
  }
  .mention-stats-title { font-size: 10px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px; }
  .mention-stat-row    { display: flex; flex-direction: column; gap: 3px; cursor: pointer; }
  .mention-stat-label  { font-size: 11px; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 5px; }
  .mention-stat-val    { font-size: 20px; font-weight: 700; letter-spacing: -.5px; color: var(--text-primary); line-height: 1.2; }
  .mention-stat-divider      { height: 1px; background: var(--border-gray); }
  .mention-stat-total-label  { font-size: 10px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .4px; }
  .mention-stat-total-val    { font-size: 24px; font-weight: 800; letter-spacing: -1px; color: var(--primary-green); line-height: 1.2; }

  /* ═══════════════════════════════════════════
     SHARE OF VOICE CARD  (PATCHED)
  ═══════════════════════════════════════════ */
  .sov-card-body {
    display: flex;
    align-items: stretch;
    padding: 0;
    flex: 1;
    min-height: 320px;
  }
  .sov-chart-col {
    flex: 0 0 260px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 16px 24px 24px;
    flex-shrink: 0;
  }
  .sov-chart-shadow {
    display: flex;
    align-items: center;
    justify-content: center;
    filter: drop-shadow(0 6px 20px rgba(3,128,71,.14));
  }
  #chSovPie {
    width: 220px !important;
    height: 220px !important;
    flex-shrink: 0;
  }
  .sov-legend-col {
    flex: 1;
    min-width: 0;
    border-left: 1px solid var(--border-gray);
    padding: 20px 20px 20px 18px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
  }
  .sov-legend-title {
    font-size: 10px;
    font-weight: 800;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: .7px;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 2px solid var(--border-gray);
    white-space: nowrap;
  }
  .sov-legend-item {
    display: flex;
    flex-direction: column;
    padding: 6px 6px;
    border-radius: var(--radius-xs);
    transition: all .15s;
    cursor: pointer;
    gap: 4px;
  }
  .sov-legend-item:hover { background: var(--primary-green-light); }
  .sov-legend-item-row {
    display: flex;
    align-items: center;
    gap: 7px;
    width: 100%;
    min-width: 0;
  }
  .sov-legend-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
  .sov-legend-name {
    flex: 1;
    min-width: 0;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-primary);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .sov-legend-pct {
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
    text-align: right;
    letter-spacing: -.3px;
    margin-left: 4px;
  }
  .sov-legend-bar-wrap {
    height: 5px;
    background: var(--bg-gray-100);
    border-radius: 3px;
    width: 100%;
    overflow: hidden;
  }
  .sov-legend-bar { height: 100%; border-radius: 3px; transition: width .9s cubic-bezier(.4,0,.2,1); }

  /* ═══════════════════════════════════════════
     SENTIMENT BY MEDIA CARD  (PATCHED)
  ═══════════════════════════════════════════ */
  .sent-media-body-wrap {
    position: relative;
    padding: 0;
    min-height: 360px;
  }
  #chSentimentMedia { width: 100%; height: 100%; display: block; }

  /* ── View All Button ── */
  .do-view-all-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: transparent; color: var(--primary-green); border: 1.5px solid var(--primary-green); border-radius: 8px; font-family: var(--font); font-size: 11px; font-weight: 700; cursor: pointer; transition: all .2s; }
  .do-view-all-btn:hover { background: var(--primary-green); color: #fff; transform: translateY(-1px); }
  .do-view-all-btn svg { fill: none; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; width: 13px; height: 13px; }

  /* ── Modals ── */
  .do-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,.75); backdrop-filter: blur(10px); }
  .do-modal.active { display: flex; align-items: center; justify-content: center; }
  .do-modal-content { background: #fff; border-radius: var(--radius); width: 90%; max-width: 600px; max-height: 80vh; box-shadow: 0 20px 60px rgba(0,0,0,.4); animation: mSlide .3s ease-out; overflow: hidden; }
  .do-modal-header  { display: flex; justify-content: space-between; align-items: flex-start; padding: 24px 28px; border-bottom: 2px solid var(--border-gray); }
  .do-modal-title   { font-size: 20px; font-weight: 700; color: #1a202c; margin: 0 0 4px 0; }
  .do-modal-subtitle{ font-size: 12px; font-weight: 500; color: #64748b; margin: 0; }
  .do-modal-close   { width: 36px; height: 36px; border-radius: 10px; background: #fff; border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .2s; }
  .do-modal-close:hover { background: #ef4444; border-color: #ef4444; color: #fff; }
  .do-modal-close svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; fill: none; }
  .do-modal-body    { padding: 20px 28px 28px; max-height: calc(80vh - 100px); overflow-y: auto; }
  .do-modal-body::-webkit-scrollbar { width: 6px; }
  .do-modal-body::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 3px; }

  /* ── Skeleton ── */
  .loading-skeleton { background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%); background-size: 200% 100%; animation: shimmer 1.5s ease-in-out infinite; border-radius: 8px; }
  @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
  .skel-overlay { position: absolute; inset: 0; z-index: 5; border-radius: 8px; }

  .do-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; gap: 8px; }
  .do-empty svg { width: 40px; height: 40px; stroke: var(--border-gray); fill: none; stroke-width: 1.5; }
  .do-empty-text { font-size: 13px; font-weight: 600; color: var(--text-secondary); }

  /* ── Map ── */
  .map-with-panel { display: flex; }
  .map-area { flex: 1; min-width: 0; position: relative; }
  .location-panel { width: 220px; flex-shrink: 0; border-left: 1px solid var(--border-gray); display: flex; flex-direction: column; }
  .location-panel-title { padding: 14px 16px 10px; font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid var(--bg-gray-100); }
  .location-list { overflow-y: auto; flex: 1; max-height: 420px; }
  .location-list::-webkit-scrollbar { width: 4px; }
  .location-list::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 2px; }
  .location-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; cursor: pointer; border-bottom: 1px solid var(--bg-gray-50); transition: all .15s; }
  .location-item:hover { background: rgba(3,128,71,.06); }
  .location-item.active { background: rgba(3,128,71,.08); border-left: 3px solid var(--primary-green); padding-left: 11px; }
  .location-item-rank  { font-size: 10px; font-weight: 700; color: var(--primary-green); width: 18px; flex-shrink: 0; }
  .location-item-info  { flex: 1; min-width: 0; }
  .location-item-name  { font-size: 12px; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .location-item-count { font-size: 11px; color: var(--text-secondary); font-weight: 500; }
  .location-item-dot   { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

  /* ── Animations ── */
  @keyframes mSlide { from { transform: translateY(-20px) scale(.95); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
  @keyframes fadeIn  { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
  .data-loaded { animation: fadeIn .4s ease-out; }
  .circle-label { pointer-events: none !important; }

  /* ── Draggable Mention Popup ── */
  @keyframes doPopIn { from { opacity:0; transform:translateY(12px) scale(.95); } to { opacity:1; transform:translateY(0) scale(1); } }
  #doMentionPopup { position: fixed; z-index: 99999; background: #fff; border: 1px solid #e2e8f0; border-radius: var(--radius); box-shadow: 0 24px 64px rgba(0,0,0,.22), 0 4px 16px rgba(0,0,0,.08); width: 480px; height: 600px; display: none; flex-direction: column; overflow: hidden; font-family: var(--font); animation: doPopIn .2s cubic-bezier(.34,1.3,.64,1); user-select: none; }
  #doMentionPopup.visible { display: flex; }
  .do-popup-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; cursor: grab; flex-shrink: 0; }
  .do-popup-header:active { cursor: grabbing; }
  .do-popup-drag-icon { display: flex; flex-direction: column; gap: 3px; margin-right: 8px; flex-shrink: 0; opacity: .4; }
  .do-popup-drag-icon span { display: block; width: 18px; height: 2px; background: #64748b; border-radius: 1px; }
  .do-popup-title-wrap { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0; }
  .do-popup-dot   { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
  .do-popup-title { font-size: 13px; font-weight: 700; color: #1a202c; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .do-popup-close { width: 28px; height: 28px; border-radius: 8px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b; font-size: 20px; line-height: 1; transition: all .15s; flex-shrink: 0; }
  .do-popup-close:hover { background: #fee2e2; color: #991b1b; }
  .do-popup-actions { display: flex; align-items: center; gap: 6px; padding: 7px 12px; border-bottom: 1px solid #e2e8f0; background: #fafbfc; flex-shrink: 0; }
  .do-popup-meta  { flex: 1; font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; display: flex; align-items: center; gap: 8px; }
  .do-popup-count-badge { background: #038047; color: #fff; border-radius: 10px; padding: 1px 9px; font-size: 11px; font-weight: 800; }
  .do-popup-export-btn { display: flex; align-items: center; gap: 5px; padding: 5px 10px; background: #038047; color: #fff; border: none; border-radius: 7px; font-family: var(--font); font-size: 10px; font-weight: 700; cursor: pointer; transition: all .15s; white-space: nowrap; }
  .do-popup-export-btn:hover { background: #026738; transform: translateY(-1px); }
  .do-popup-export-btn svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }
  .do-popup-list { overflow-y: auto; flex: 1; padding: 4px 0; min-height: 0; }
  .do-popup-list::-webkit-scrollbar { width: 5px; }
  .do-popup-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
  .do-popup-item { display: flex; gap: 10px; padding: 10px 16px; border-bottom: 1px solid #f8fafc; transition: background .1s; cursor: pointer; align-items: flex-start; }
  .do-popup-item:last-child { border-bottom: none; }
  .do-popup-item:hover { background: #f0fdf4; }
  .do-popup-avatar { width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0; background: linear-gradient(135deg,#038047,#026738); color: #fff; font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; border: 1.5px solid #e2e8f0; overflow: hidden; }
  .do-popup-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
  .do-popup-item-body { flex: 1; min-width: 0; }
  .do-popup-author  { font-size: 12px; font-weight: 700; color: #1a202c; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .do-popup-handle  { font-size: 10px; color: #94a3b8; font-weight: 500; margin-bottom: 3px; }
  .do-popup-content { font-size: 12px; color: #374151; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 4px; }
  .do-popup-footer  { display: flex; align-items: center; gap: 6px; font-size: 10px; color: #94a3b8; flex-wrap: wrap; }
  .do-popup-sent    { padding: 1px 7px; border-radius: 10px; font-size: 9px; font-weight: 800; }
  .do-popup-sent-p  { background: #d1fae5; color: #065f46; }
  .do-popup-sent-n  { background: #fee2e2; color: #991b1b; }
  .do-popup-sent-u  { background: #f1f5f9; color: #374151; }
  .do-popup-loading { padding: 40px 20px; text-align: center; color: #64748b; font-size: 13px; font-weight: 600; display: flex; flex-direction: column; align-items: center; gap: 12px; }
  .do-popup-spinner { width: 32px; height: 32px; border: 3px solid #e2e8f0; border-top-color: #038047; border-radius: 50%; animation: doSpin .7s linear infinite; }
  @keyframes doSpin { to { transform: rotate(360deg); } }
  .do-popup-empty { padding: 40px 20px; text-align: center; color: #94a3b8; font-size: 13px; font-weight: 600; }

  /* ── Platform Picker ── */
  @keyframes doPlatIn { from { opacity:0; transform:scale(.92) translateY(6px); } to { opacity:1; transform:none; } }
  #doPlatPicker { position: fixed; z-index: 999999; background: #fff; border: 1px solid #e2e8f0; border-radius: var(--radius-sm); box-shadow: 0 16px 40px rgba(0,0,0,.18); padding: 6px; min-width: 175px; font-family: var(--font); animation: doPlatIn .15s ease-out; display: none; }
  #doPlatPicker.visible { display: block; }
  .do-plat-header { padding: 5px 10px 8px; font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #f1f5f9; margin-bottom: 4px; }
  .do-plat-btn { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; background: transparent; border: none; font-family: var(--font); width: 100%; text-align: left; color: #374151; transition: background .12s; }
  .do-plat-btn:hover { background: #f0fdf4; color: #038047; }
  .do-plat-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }

  /* ── Item Detail Panel ── */
  @keyframes detailSlide { from { transform: translateX(100%); } to { transform: translateX(0); } }
  #doDetailPanel { position: absolute; inset: 0; background: #fff; z-index: 10; display: none; flex-direction: column; animation: detailSlide .22s cubic-bezier(.4,0,.2,1); }
  #doDetailPanel.visible { display: flex; }
  .dp-header { display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; }
  .dp-back  { width: 30px; height: 30px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b; transition: all .15s; flex-shrink: 0; }
  .dp-back:hover { background: #f0fdf4; color: #038047; }
  .dp-back svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }
  .dp-title { font-size: 13px; font-weight: 700; color: #1a202c; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .dp-close { width: 28px; height: 28px; border-radius: 8px; border: none; background: transparent; cursor: pointer; font-size: 20px; color: #64748b; display: flex; align-items: center; justify-content: center; transition: all .15s; }
  .dp-close:hover { background: #fee2e2; color: #991b1b; }
  .dp-body { overflow-y: auto; flex: 1; padding: 16px; }
  .dp-body::-webkit-scrollbar { width: 5px; }
  .dp-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
  .dp-avatar-row  { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
  .dp-avatar-lg   { width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg,#038047,#026738); color: #fff; font-weight: 700; font-size: 18px; display: flex; align-items: center; justify-content: center; border: 2px solid #e2e8f0; overflow: hidden; flex-shrink: 0; }
  .dp-avatar-lg img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
  .dp-author-name   { font-size: 15px; font-weight: 700; color: #1a202c; }
  .dp-author-handle { font-size: 12px; color: #94a3b8; font-weight: 500; }
  .dp-media-wrap  { border-radius: 12px; overflow: hidden; margin-bottom: 14px; background: #000; }
  .dp-media-img   { width: 100%; max-height: 260px; object-fit: cover; display: block; }
  .dp-video-wrap  { position: relative; width: 100%; border-radius: 12px; overflow: hidden; margin-bottom: 14px; background: #000; }
  .dp-video-iframe{ width: 100%; height: 220px; border: none; display: block; }
  .dp-content-text{ font-size: 13px; color: #374151; line-height: 1.7; margin-bottom: 14px; background: #f8fafc; border-radius: 10px; padding: 12px 14px; border: 1px solid #e2e8f0; word-break: break-word; }
  .dp-stats-grid  { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-bottom: 14px; }
  .dp-stat-box    { background: #f8fafc; border-radius: 10px; padding: 10px 12px; border: 1px solid #e2e8f0; text-align: center; }
  .dp-stat-val    { font-size: 16px; font-weight: 700; color: #1a202c; }
  .dp-stat-label  { font-size: 9px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-top: 2px; }
  .dp-sent-big    { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; margin-bottom: 14px; }
  .dp-sent-big-p  { background: #d1fae5; color: #065f46; }
  .dp-sent-big-n  { background: #fee2e2; color: #991b1b; }
  .dp-sent-big-u  { background: #f1f5f9; color: #374151; }
  .dp-link-btn    { display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: var(--primary-green); color: #fff; border-radius: 10px; font-size: 12px; font-weight: 700; text-decoration: none; transition: all .2s; width: 100%; justify-content: center; margin-top: 4px; }
  .dp-link-btn:hover { background: var(--primary-green-dark); }
  .dp-link-btn svg{ width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
  .dp-meta-row    { display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #94a3b8; font-weight: 500; margin-bottom: 12px; }

  /* ── Export Progress Overlay ── */
  #exportOverlay { position: fixed; inset: 0; background: rgba(0,0,0,.6); backdrop-filter: blur(8px); z-index: 999999; display: none; align-items: center; justify-content: center; }
  #exportOverlay.show { display: flex; }
  .export-progress-box { background: #fff; border-radius: 20px; padding: 40px 48px; text-align: center; box-shadow: 0 32px 80px rgba(0,0,0,.3); min-width: 320px; }
  .export-prog-icon  { width: 64px; height: 64px; border: 4px solid #e2e8f0; border-top-color: #038047; border-radius: 50%; animation: doSpin .8s linear infinite; margin: 0 auto 20px; }
  .export-prog-title { font-size: 18px; font-weight: 700; color: #1a202c; margin-bottom: 8px; }
  .export-prog-sub   { font-size: 13px; color: #64748b; font-weight: 500; }
  .export-prog-bar-wrap { height: 6px; background: #e2e8f0; border-radius: 3px; margin-top: 20px; overflow: hidden; }
  .export-prog-bar   { height: 100%; background: linear-gradient(90deg,#038047,#22c55e); border-radius: 3px; transition: width .4s ease; }

  /* ── Responsive ── */
  @media (max-width: 1280px) {
    .do-row-top { grid-template-columns: 1fr 1fr; }
    .do-row-top > .do-card:last-child { grid-column: 1 / -1; }
    .mention-revised-body { min-height: 220px; }
    #chMentionPie { max-width: 200px; height: 200px !important; }
    .do-row-mid { grid-template-columns: 360px 1fr; }
  }
  @media (max-width: 1024px) {
    .do-row-mid { grid-template-columns: 1fr; }
    .sov-card-body { flex-direction: column; min-height: auto; }
    .sov-chart-col { flex: none; padding: 20px 16px 12px; }
    .sov-legend-col { flex: none; border-left: none; border-top: 1px solid var(--border-gray); padding: 14px 20px 20px; }
    #chSovPie { width: 200px !important; height: 200px !important; }
  }
  @media (max-width: 900px) {
    .do-row-top { grid-template-columns: 1fr; }
    .mention-revised-body { flex-direction: column; }
    .mention-stats-panel { width: 100%; border-left: none; border-top: 1px solid var(--border-gray); flex-direction: row; flex-wrap: wrap; gap: var(--sp-md); padding: var(--sp-md) var(--sp-lg); }
    .map-with-panel { flex-direction: column; }
    .location-panel { width: 100%; border-left: none; border-top: 1px solid var(--border-gray); }
    .location-list { max-height: 200px; }
    #doMentionPopup { width: 92vw; }
  }
  @media (max-width: 768px) {
    .dashboard-container { padding: 16px; }
    .do-row-top, .do-row-mid { gap: 12px; }
    .do-card-head { padding: var(--sp-sm) var(--sp-md); }
    .filter-content { flex-direction: column; align-items: stretch; }
    .date-picker-trigger { min-width: auto; }
    .apply-btn { width: 100%; justify-content: center; }
    .date-picker-container { flex-direction: column; max-height: 85vh; overflow-y: auto; width: 95%; }
    .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: var(--radius) var(--radius) 0 0; flex-direction: row; overflow-x: auto; padding: 12px 16px; }
    .date-preset { white-space: nowrap; }
    .calendars-wrapper { flex-direction: column; gap: 16px; }
  }
  @media (max-width: 600px) {
    #chSovPie { width: 180px !important; height: 180px !important; }
    .sov-chart-shadow { filter: none; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <div class="page-header">
    <div class="page-header-left">
      <h1>Data Overview</h1>
      <p>Ringkasan analitik sosial media dan berita</p>
    </div>
    <div class="export-dropdown-wrap">
      <button class="export-master-btn" id="exportMasterBtn">
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export Data
        <svg viewBox="0 0 24 24" style="width:12px;height:12px;"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div class="export-dropdown" id="exportDropdown">
        <div class="export-dd-section">Semua Data</div>
        <button class="export-dd-btn" onclick="DataExporter.exportAll('xlsx')">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Export ke Excel (.xlsx)
        </button>
        <button class="export-dd-btn" onclick="DataExporter.exportAll('csv')">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Export ke CSV
        </button>
        <div class="export-dd-divider"></div>
        <div class="export-dd-section">Per Platform</div>
        <button class="export-dd-btn" onclick="DataExporter.exportPlatform('doc')">Online News</button>
        <button class="export-dd-btn" onclick="DataExporter.exportPlatform('twit')">X / Twitter</button>
        <button class="export-dd-btn" onclick="DataExporter.exportPlatform('instagram')">Instagram</button>
      </div>
    </div>
  </div>

  {{-- FILTER --}}
  <div class="filter-card">
    <form id="filterForm" method="GET">
      <input type="hidden" name="project_id" id="hiddenProjectId" value="{{ $projectId }}">
      <input type="hidden" name="start_date"  id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date"     id="hiddenEndDate"   value="{{ $endDate }}">
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
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span id="dateRangeDisplay">{{ $startDate }} – {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <div class="filter-group" style="margin-left:auto;">
          <label class="filter-label" style="opacity:0;pointer-events:none;">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Filter
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- DATE PICKER MODAL --}}
  <div class="date-picker-modal" id="datePickerModal">
    <div class="date-picker-overlay" onclick="DPicker.close()"></div>
    <div class="date-picker-container">
      <div class="date-picker-sidebar">
        <button type="button" class="date-preset" data-p="today">Today</button>
        <button type="button" class="date-preset" data-p="yesterday">Yesterday</button>
        <button type="button" class="date-preset" data-p="last7">Last 7 Days</button>
        <button type="button" class="date-preset" data-p="last30">Last 30 Days</button>
        <button type="button" class="date-preset" data-p="thismonth">This Month</button>
        <button type="button" class="date-preset" data-p="lastmonth">Last Month</button>
        <button type="button" class="date-preset active" data-p="custom">Custom Range</button>
      </div>
      <div class="date-picker-content">
        <div class="date-picker-header">
          <button type="button" class="nav-btn" onclick="DPicker.nav(-1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="doCal1"></div>
            <div class="calendar" id="doCal2"></div>
          </div>
          <button type="button" class="nav-btn" onclick="DPicker.nav(1)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="date-picker-display">
          <span id="doRangeText">{{ $startDate }} – {{ $endDate }}</span>
        </div>
        <div class="date-picker-footer">
          <button type="button" class="cancel-btn" onclick="DPicker.close()">Batal</button>
          <button type="button" class="apply-date-btn" onclick="DPicker.apply()">Terapkan</button>
        </div>
      </div>
    </div>
  </div>

  {{-- ROW 1: Trending | Hashtag | Mention --}}
  <div class="do-row-top">

    {{-- Trending Topics --}}
    <div class="do-card" data-lazy="trending-topics">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
          <span class="do-card-title">Trending Topics</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;" id="trendingHead">
          <span class="do-badge">News</span>
        </div>
      </div>
      <div class="do-card-body do-body-scroll" id="trendingBody">
        <div class="loading-skeleton" style="height:28px;margin-bottom:10px;border-radius:6px;"></div>
        <div class="loading-skeleton" style="height:28px;margin-bottom:10px;border-radius:6px;"></div>
        <div class="loading-skeleton" style="height:28px;margin-bottom:10px;border-radius:6px;"></div>
        <div class="loading-skeleton" style="height:28px;width:70%;border-radius:6px;"></div>
      </div>
    </div>

    {{-- Top Hashtag --}}
    <div class="do-card" data-lazy="top-hashtags">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon"><svg viewBox="0 0 24 24"><line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="9" y1="4" x2="5" y2="20"/><line x1="15" y1="4" x2="11" y2="20"/></svg></span>
          <span class="do-card-title">Top Hashtag</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px;" id="hashtagHead">
          <span class="do-badge">X</span>
        </div>
      </div>
      <div class="do-card-body do-body-scroll" id="hashtagBody">
        <div class="loading-skeleton" style="height:28px;margin-bottom:10px;border-radius:6px;"></div>
        <div class="loading-skeleton" style="height:28px;margin-bottom:10px;border-radius:6px;"></div>
        <div class="loading-skeleton" style="height:28px;margin-bottom:10px;border-radius:6px;"></div>
        <div class="loading-skeleton" style="height:28px;width:70%;border-radius:6px;"></div>
      </div>
    </div>

    {{-- MENTION CARD --}}
    <div class="do-card" data-lazy="mention-combined">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
          <span class="do-card-title">Mention</span>
        </div>
        <span class="do-badge">All Media</span>
      </div>
      <div id="mentionSkeletonWrap" style="padding:20px;flex:1;">
        <div class="loading-skeleton" style="height:220px;border-radius:8px;"></div>
      </div>
      <div class="mention-revised-body" id="mentionRevisedBody" style="display:none;">
        <div class="mention-chart-area">
          <div id="chMentionPie"></div>
        </div>
        <div class="mention-stats-panel">
          <div class="mention-stats-title">Breakdown</div>
          <div class="mention-stat-row" id="statNewsRow">
            <span class="mention-stat-label">
              <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#0284c7;flex-shrink:0;"></span>
              Online News
            </span>
            <span class="mention-stat-val" id="mentionNewsVal">—</span>
          </div>
          <div class="mention-stat-row" id="statSocialRow">
            <span class="mention-stat-label">
              <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#038047;flex-shrink:0;"></span>
              Social Media
            </span>
            <span class="mention-stat-val" id="mentionSocialVal">—</span>
          </div>
          <div class="mention-stat-divider"></div>
          <div class="mention-stat-row">
            <span class="mention-stat-total-label">Total</span>
            <span class="mention-stat-total-val" id="mentionTotalVal">—</span>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /do-row-top --}}

  {{-- ROW 2: SOV | Sentiment Timeline --}}
  <div class="do-row-mid">

    {{-- SHARE OF VOICE --}}
    <div class="do-card" data-lazy="sov">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></span>
          <div>
            <div class="do-card-title">Share of Voice</div>
            <div class="do-card-subtitle">Klik slice atau legend untuk detail</div>
          </div>
        </div>
        <span class="do-badge">By Media</span>
      </div>
      <div id="sovSkeleton" style="position:relative;height:280px;padding:20px;">
        <div class="loading-skeleton" style="height:100%;border-radius:8px;"></div>
      </div>
      <div class="sov-card-body" id="sovCardBody" style="display:none;">
        <div class="sov-chart-col">
          <div class="sov-chart-shadow">
            <div id="chSovPie"></div>
          </div>
        </div>
        <div class="sov-legend-col">
          <div class="sov-legend-title">Media Platforms</div>
          <div id="sovLegendItems"></div>
        </div>
      </div>
    </div>

    {{-- Sentiment Timeline --}}
    <div class="do-card" data-lazy="sentiment-timeline">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
          <span class="do-card-title">Sentiment Score</span>
        </div>
        <span class="do-badge">All Media</span>
      </div>
      <div class="do-card-body" style="position:relative;height:320px;">
        <div id="chSentiment" style="width:100%;height:100%;"></div>
        <div class="loading-skeleton skel-overlay" id="skSentiment"></div>
      </div>
    </div>

  </div>{{-- /do-row-mid --}}


  {{-- ROW 4: Buzzer Map --}}
  <div class="do-card" data-lazy="buzzer-map">
    <div class="do-card-head"> 
      <div class="do-card-head-left">
        <span class="do-head-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="10" r="3"/><path d="M12 2a8 8 0 0 1 8 8c0 5.25-8 12-8 12S4 15.25 4 10a8 8 0 0 1 8-8z"/></svg></span>
        <span class="do-card-title">Buzzer Map</span>
      </div>
      <span class="do-badge">Geographic</span>
    </div>
    <div class="map-with-panel">
      <div class="map-area">
        <div id="buzzMap" style="width:100%;height:420px;"></div>
        <div id="mapSkeleton" style="position:absolute;inset:0;height:420px;">
          <div class="loading-skeleton" style="height:100%;border-radius:0;"></div>
        </div>
      </div>
      <div class="location-panel">
        <div class="location-panel-title">Locations</div>
        <div class="location-list" id="buzzMapList">
          <div style="padding:10px 14px;">
            <div class="loading-skeleton" style="height:20px;margin-bottom:8px;border-radius:5px;"></div>
            <div class="loading-skeleton" style="height:20px;margin-bottom:8px;border-radius:5px;"></div>
            <div class="loading-skeleton" style="height:20px;border-radius:5px;"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>{{-- /dashboard-container --}}

{{-- DRAGGABLE MENTION POPUP --}}
<div id="doMentionPopup">
  <div class="do-popup-header" id="doPopupHeader">
    <div class="do-popup-drag-icon"><span></span><span></span><span></span></div>
    <div class="do-popup-title-wrap">
      <div class="do-popup-dot" id="doPopupDot"></div>
      <span class="do-popup-title" id="doPopupTitle">Mentions</span>
    </div>
    <button class="do-popup-close" onclick="DoMentionPopup.close()">×</button>
  </div>
  <div class="do-popup-actions">
    <div class="do-popup-meta">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;flex-shrink:0"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <span id="doPopupMeta">—</span>
      <span class="do-popup-count-badge" id="doPopupCount">…</span>
      <span>mentions</span>
    </div>
    <button class="do-popup-export-btn" onclick="DoMentionPopup.exportCurrent()">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export
    </button>
  </div>
  <div class="do-popup-list" id="doPopupList"></div>

  <div id="doDetailPanel">
    <div class="dp-header">
      <button class="dp-back" onclick="DoDetailPanel.close()">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="dp-title" id="dpTitle">Detail</span>
      <button class="dp-close" onclick="DoMentionPopup.close()">×</button>
    </div>
    <div class="dp-body" id="dpBody"></div>
  </div>
</div>

{{-- Platform Picker --}}
<div id="doPlatPicker">
  <div class="do-plat-header">Pilih Platform</div>
  <button class="do-plat-btn" onclick="DoMentionPopup.openPlatform('twit')">X / Twitter <span class="do-plat-dot" style="background:#0ea5e9;margin-left:auto;"></span></button>
  <button class="do-plat-btn" onclick="DoMentionPopup.openPlatform('fb')">Facebook <span class="do-plat-dot" style="background:#1877f2;margin-left:auto;"></span></button>
  <button class="do-plat-btn" onclick="DoMentionPopup.openPlatform('instagram')">Instagram <span class="do-plat-dot" style="background:#e1306c;margin-left:auto;"></span></button>
  <button class="do-plat-btn" onclick="DoMentionPopup.openPlatform('youtube')">YouTube <span class="do-plat-dot" style="background:#ff0000;margin-left:auto;"></span></button>
  <button class="do-plat-btn" onclick="DoMentionPopup.openPlatform('tiktok')">TikTok <span class="do-plat-dot" style="background:#333;margin-left:auto;"></span></button>
</div>

{{-- Modals --}}
<div id="hashtagModal" class="do-modal">
  <div class="do-modal-content">
    <div class="do-modal-header">
      <div><h3 class="do-modal-title">Top Hashtags</h3><p class="do-modal-subtitle">Semua trending hashtags</p></div>
      <button class="do-modal-close" onclick="DOModal.closeHashtag()"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="do-modal-body" id="hashtagModalBody"></div>
  </div>
</div>
<div id="trendingModal" class="do-modal">
  <div class="do-modal-content">
    <div class="do-modal-header">
      <div><h3 class="do-modal-title">All Trending Topics</h3><p class="do-modal-subtitle">Daftar lengkap trending topics</p></div>
      <button class="do-modal-close" onclick="DOModal.closeTrending()"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="do-modal-body" id="trendingModalBody"></div>
  </div>
</div>

{{-- Export Overlay --}}
<div id="exportOverlay">
  <div class="export-progress-box">
    <div class="export-prog-icon"></div>
    <div class="export-prog-title" id="exportProgTitle">Mempersiapkan export…</div>
    <div class="export-prog-sub"   id="exportProgSub">Mengambil data dari semua platform</div>
    <div class="export-prog-bar-wrap"><div class="export-prog-bar" id="exportProgBar" style="width:0%"></div></div>
  </div>
</div>

@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
'use strict';

/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
const DOCfg = {
  pid: {{ $projectId ? (int)$projectId : 'null' }},
  sd:  '{{ $startDate }}',
  ed:  '{{ $endDate }}',
  colorMap: {
    'Online News':  '#0284c7',
    'X (Twitter)':  '#1d9bf0',
    'Facebook':     '#1877f2',
    'Instagram':    '#e1306c',
    'YouTube':      '#ff0000',
    'TikTok':       '#111827',
  },
  platMeta: {
    doc:       { label: 'Online News',  color: '#0284c7' },
    twit:      { label: 'X / Twitter',  color: '#0ea5e9' },
    fb:        { label: 'Facebook',     color: '#1877f2' },
    instagram: { label: 'Instagram',    color: '#e1306c' },
    youtube:   { label: 'YouTube',      color: '#ff0000' },
    tiktok:    { label: 'TikTok',       color: '#111827' },
  },
  mediaKeyMap: {
    'Online News':  'doc',
    'X (Twitter)':  'twit',
    'Facebook':     'fb',
    'Instagram':    'instagram',
    'YouTube':      'youtube',
    'TikTok':       'tiktok',
  }
};

/* ══════════════════════════════════════════════════════
   UTILS
══════════════════════════════════════════════════════ */
const numFmt  = n => parseInt(n||0).toLocaleString('id-ID');
const numK    = n => { n = parseInt(n||0); return n >= 1e6 ? (n/1e6).toFixed(1)+'M' : n >= 1000 ? (n/1000).toFixed(1)+'k' : String(n); };
const esc     = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const hideSk  = id => { const e = document.getElementById(id); if (e) e.style.display = 'none'; };
const emptyHtml = msg => `<div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="do-empty-text">${msg||'Tidak ada data'}</span></div>`;

/* ══════════════════════════════════════════════════════
   ECHARTS REGISTRY
══════════════════════════════════════════════════════ */
const DOCharts = {
  _inst: {},
  make(id) {
    if (this._inst[id]) { try { this._inst[id].dispose(); } catch(e) {} }
    const dom = document.getElementById(id);
    if (!dom) return null;
    const c = echarts.init(dom, null, { renderer: 'canvas' });
    this._inst[id] = c;
    return c;
  }
};
window.addEventListener('resize', () => {
  Object.values(DOCharts._inst).forEach(c => { try { if (!c.isDisposed()) c.resize(); } catch(e) {} });
});

const EC_TOOLTIP = {
  backgroundColor: '#1a202c',
  borderColor:     '#334155',
  borderWidth:     1,
  padding:         [10, 14],
  textStyle:       { color: '#ffffff', fontFamily: "'Poppins',sans-serif", fontSize: 13 },
  extraCssText:    'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);'
};

/* ══════════════════════════════════════════════════════
   DATE PICKER
══════════════════════════════════════════════════════ */
const DPicker = (() => {
  let ds=null, de=null, m1=new Date(), m2=new Date(), pickStart=true;
  const MN = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD = ['Su','Mo','Tu','We','Th','Fr','Sa'];

  function init() {
    const si = document.getElementById('hiddenStartDate'), ei = document.getElementById('hiddenEndDate');
    ds = si?.value ? new Date(si.value) : (()=>{ const d=new Date(); d.setDate(d.getDate()-6); return d; })();
    de = ei?.value ? new Date(ei.value) : new Date();
    m1 = new Date(ds); m2 = new Date(ds); m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('datePickerTrigger').addEventListener('click', open);
    document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', onPreset));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
    document.getElementById('doProject').addEventListener('change', function() { document.getElementById('hiddenProjectId').value = this.value; });
  }
  function open()  { document.getElementById('datePickerModal').classList.add('show'); render(); }
  function close() { document.getElementById('datePickerModal').classList.remove('show'); }
  function apply() {
    document.getElementById('hiddenStartDate').value = fmt(ds);
    document.getElementById('hiddenEndDate').value   = fmt(de);
    document.getElementById('dateRangeDisplay').textContent = fmt(ds) + ' – ' + fmt(de);
    close();
  }
  function nav(dir) { m1.setMonth(m1.getMonth()+dir); m2.setMonth(m2.getMonth()+dir); render(); }
  function onPreset(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    const today = new Date(); today.setHours(0,0,0,0);
    switch(e.target.dataset.p) {
      case 'today':     ds = new Date(today); de = new Date(today); break;
      case 'yesterday': ds = new Date(today); ds.setDate(today.getDate()-1); de = new Date(ds); break;
      case 'last7':     de = new Date(today); ds = new Date(today); ds.setDate(today.getDate()-6); break;
      case 'last30':    de = new Date(today); ds = new Date(today); ds.setDate(today.getDate()-29); break;
      case 'thismonth': ds = new Date(today.getFullYear(), today.getMonth(), 1); de = new Date(today); break;
      case 'lastmonth': ds = new Date(today.getFullYear(), today.getMonth()-1, 1); de = new Date(today.getFullYear(), today.getMonth(), 0); break;
    }
    if (e.target.dataset.p !== 'custom') { m1 = new Date(ds); m2 = new Date(ds); m2.setMonth(m2.getMonth()+1); }
    updDisp(); render();
  }
  function render() { renderCal('doCal1', m1); renderCal('doCal2', m2); updDisp(); }
  function renderCal(id, month) {
    const el = document.getElementById(id); if (!el) return;
    const y = month.getFullYear(), mn = month.getMonth();
    const first = new Date(y,mn,1), last = new Date(y,mn+1,0), prevL = new Date(y,mn,0);
    const today = new Date(); today.setHours(0,0,0,0);
    let h = `<div class="calendar-month">${MN[mn]} ${y}</div><div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
    for (let i = 0; i < first.getDay(); i++) h += `<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for (let d = 1; d <= last.getDate(); d++) {
      const date = new Date(y,mn,d); date.setHours(0,0,0,0);
      let cls = 'calendar-day';
      if (sD(date,today)) cls += ' today';
      if (date > today) cls += ' disabled';
      if (ds && de) { if (sD(date,ds) || sD(date,de)) cls += ' selected'; else if (date > ds && date < de) cls += ' in-range'; }
      h += `<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem = last.getDay() === 6 ? 0 : 6 - last.getDay();
    for (let i = 1; i <= rem; i++) h += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h += '</div>'; el.innerHTML = h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => {
      btn.addEventListener('click', function() {
        const d = new Date(this.dataset.date); d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        document.querySelector('[data-p="custom"]').classList.add('active');
        if (pickStart || d < ds) { ds = d; de = d; pickStart = false; }
        else { if (d >= ds) de = d; else { de = ds; ds = d; } pickStart = true; }
        updDisp(); render();
      });
    });
  }
  function updDisp() { const el = document.getElementById('doRangeText'); if (el && ds && de) el.textContent = fmt(ds) + ' – ' + fmt(de); }
  function fmt(d) { if (!d) return ''; return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
  function sD(a,b) { return a && b && a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }
  return { init, open, close, apply, nav };
})();

/* ══════════════════════════════════════════════════════
   MODAL HELPER
══════════════════════════════════════════════════════ */
const DOModal = {
  _allTopics: [], _allHashtags: [],
  openTrending(topics) {
    this._allTopics = topics;
    let h = `<table class="do-tbl"><thead><tr><th style="width:40px;">#</th><th>Topic</th></tr></thead><tbody>`;
    topics.forEach((t,i) => {
      const name = t.title||t.name||t.topic||'Unknown', url = t.reference||t.url||(t.urls&&t.urls[0])||'#';
      h += `<tr><td class="do-tbl-rank">${i+1}</td><td class="do-tbl-name">${url!=='#'?`<a href="${url}" target="_blank" rel="noopener noreferrer" class="topic-link">${esc(name)}</a>`:esc(name)}</td></tr>`;
    });
    h += '</tbody></table>';
    document.getElementById('trendingModalBody').innerHTML = h;
    document.getElementById('trendingModal').classList.add('active');
    document.body.style.overflow = 'hidden';
  },
  openHashtag(tags) {
    this._allHashtags = tags;
    let h = `<table class="do-tbl"><thead><tr><th>#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>`;
    tags.forEach((tag,i) => {
      let name = tag.name||tag.hashtag||tag.tag||'unknown'; if (!name.startsWith('#')) name = '#' + name;
      const count = parseInt(tag.size||tag.mention||tag.count||0);
      h += `<tr><td class="do-tbl-rank">${i+1}</td><td class="do-tbl-name" style="color:var(--primary-green);font-weight:700;">${name}</td><td class="do-tbl-num">${count.toLocaleString()}</td></tr>`;
    });
    h += '</tbody></table>';
    document.getElementById('hashtagModalBody').innerHTML = h;
    document.getElementById('hashtagModal').classList.add('active');
    document.body.style.overflow = 'hidden';
  },
  closeTrending() { document.getElementById('trendingModal').classList.remove('active'); document.body.style.overflow = 'auto'; },
  closeHashtag()  { document.getElementById('hashtagModal').classList.remove('active');  document.body.style.overflow = 'auto'; },
};

window.addEventListener('click', e => {
  if (e.target === document.getElementById('trendingModal')) DOModal.closeTrending();
  if (e.target === document.getElementById('hashtagModal'))  DOModal.closeHashtag();
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { DOModal.closeTrending(); DOModal.closeHashtag(); DoDetailPanel.close(); }
});

/* ══════════════════════════════════════════════════════
   LAZY LOADER
══════════════════════════════════════════════════════ */
const DOLoader = {
  loaded: new Set(),

  init() {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const card = entry.target, sec = card.dataset.lazy;
          if (!this.loaded.has(sec)) { this.loaded.add(sec); this.load(sec, card); obs.unobserve(card); }
        }
      });
    }, { rootMargin: '100px', threshold: .05 });
    document.querySelectorAll('[data-lazy]').forEach(c => obs.observe(c));
  },

  async load(sec, card) {
    try {
      switch(sec) {
        case 'trending-topics':    await this.loadTrending();  break;
        case 'top-hashtags':       await this.loadHashtags();  break;
        case 'mention-combined':   await this.loadMentions();  break;
        case 'sov':                await this.loadSov();       break;
        case 'sentiment-timeline': await this.loadSentLine();  break;
        case 'sentiment-media':    await this.loadSentMedia(); break;
        case 'buzzer-map':         await this.loadMap();       break;
      }
    } catch(err) { console.error(`Error loading ${sec}:`, err); }
  },

  /* ── TRENDING ── */
  async loadTrending() {
    const r = await fetch(`/mk/api/trending-topics`);
    const d = await r.json();
    const body = document.getElementById('trendingBody');
    const topics = d.data || [];
    if (!topics.length) { body.innerHTML = emptyHtml(); return; }
    if (topics.length > 10) {
      document.getElementById('trendingHead').insertAdjacentHTML('beforeend',
        `<button class="do-view-all-btn" onclick="DOModal.openTrending(window._doTopics)">
          <svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>View All
        </button>`);
    }
    window._doTopics = topics;
    let h = `<table class="do-tbl"><thead><tr><th style="width:24px;">#</th><th>Topic</th></tr></thead><tbody>`;
    topics.slice(0,10).forEach((t,i) => {
      const name = t.title||t.name||t.topic||'Unknown', url = t.reference||t.url||(t.urls&&t.urls[0])||'#';
      h += `<tr><td class="do-tbl-rank">${i+1}</td><td class="do-tbl-name" style="max-width:400px;">${url!=='#'?`<a href="${url}" target="_blank" rel="noopener noreferrer" class="topic-link">${esc(name)}</a>`:esc(name)}</td></tr>`;
    });
    h += '</tbody></table>';
    body.innerHTML = h; body.classList.add('data-loaded');
  },

  /* ── HASHTAGS ── */
  async loadHashtags() {
    const r = await fetch(`/mk/api/top-hashtags?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`);
    const d = await r.json();
    const body = document.getElementById('hashtagBody');
    let tags = d.data && Array.isArray(d.data.hashtags) ? d.data.hashtags : (Array.isArray(d.data) ? d.data : []);
    if (!tags.length) { body.innerHTML = emptyHtml(); return; }
    if (tags.length > 5) {
      document.getElementById('hashtagHead').insertAdjacentHTML('beforeend',
        `<button class="do-view-all-btn" onclick="DOModal.openHashtag(window._doHashtags)">
          <svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>View All
        </button>`);
    }
    window._doHashtags = tags;
    let h = `<table class="do-tbl"><thead><tr><th style="width:24px;">#</th><th>Hashtag</th><th style="text-align:right;">Mention</th></tr></thead><tbody>`;
    tags.slice(0,5).forEach((tag,i) => {
      let name = tag.name||tag.hashtag||tag.tag||'unknown'; if (!name.startsWith('#')) name = '#' + name;
      const count = parseInt(tag.size||tag.mention||tag.count||0);
      h += `<tr><td class="do-tbl-rank">${i+1}</td><td class="do-tbl-name" style="color:var(--primary-green);font-weight:700;">${name}</td><td class="do-tbl-num">${count.toLocaleString()}</td></tr>`;
    });
    h += '</tbody></table>';
    body.innerHTML = h; body.classList.add('data-loaded');
  },

  /* ── MENTIONS ── */
  async loadMentions() {
    const r = await fetch(`/mk/api/mention-counts?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`);
    const d = await r.json();
    const social = Number(d.social||0), news = Number(d.news||0), total = social + news;
    document.getElementById('mentionNewsVal').textContent   = numFmt(news);
    document.getElementById('mentionSocialVal').textContent = numFmt(social);
    document.getElementById('mentionTotalVal').textContent  = numFmt(total);
    document.getElementById('mentionSkeletonWrap').style.display = 'none';
    document.getElementById('mentionRevisedBody').style.display  = 'flex';

    if (total > 0) {
      const chart = DOCharts.make('chMentionPie');
      if (chart) {
        chart.setOption({
          animation: true, animationDuration: 900, animationEasing: 'cubicInOut',
          tooltip: {
            ...EC_TOOLTIP, trigger: 'item',
            formatter: p => {
              const pct = total > 0 ? p.value / total * 100 : 0;
              const pctStr = pct < 1 && pct > 0 ? '<1%' : Math.round(pct) + '%';
              return `<div style="font-weight:700;margin-bottom:4px;">${p.name}</div><div>${numFmt(p.value)} mentions (${pctStr})</div>`;
            }
          },
          legend: {
            show: true, bottom: 0, orient: 'horizontal',
            textStyle: { fontFamily: "'Poppins',sans-serif", fontSize: 10, fontWeight: '600', color: '#64748b' },
            icon: 'circle', itemWidth: 8, itemHeight: 8, itemGap: 12,
          },
          series: [{
            type: 'pie',
            radius: ['50%', '74%'],
            center: ['50%', '46%'],
            avoidLabelOverlap: true,
            itemStyle: { borderRadius: 5, borderColor: '#fff', borderWidth: 2 },
            label: { show: false },
            emphasis: {
              label: {
                show: true, fontSize: 12, fontWeight: '700', fontFamily: "'Poppins',sans-serif",
                formatter: p => {
                  const pct = total > 0 ? p.value / total * 100 : 0;
                  return `{n|${p.name}}\n{v|${numK(p.value)}}\n{p|${pct < 1 && pct > 0 ? '<1%' : Math.round(pct) + '%'}}`;
                },
                rich: {
                  n: { fontSize: 10, color: '#64748b', fontWeight: '600', lineHeight: 16 },
                  v: { fontSize: 15, color: '#1a202c', fontWeight: '700', lineHeight: 20 },
                  p: { fontSize: 10, color: '#038047', fontWeight: '700', lineHeight: 16 }
                }
              },
              scale: true, scaleSize: 4,
              itemStyle: { shadowBlur: 12, shadowColor: 'rgba(0,0,0,.15)' }
            },
            data: [
              { name: 'Online News',  value: news,   itemStyle: { color: { type:'linear', x:0, y:0, x2:0, y2:1, colorStops:[{offset:0,color:'#0284c7'},{offset:1,color:'#0369a1'}] } } },
              { name: 'Social Media', value: social, itemStyle: { color: { type:'linear', x:0, y:0, x2:0, y2:1, colorStops:[{offset:0,color:'#038047'},{offset:1,color:'#026738'}] } } },
            ]
          }]
        });
        chart.on('click', p => {
          const rect = chart.getDom().getBoundingClientRect();
          const cx = rect.left + rect.width/2, cy = rect.top + rect.height/2;
          if (p.name === 'Online News') DoMentionPopup.open('doc', cx, cy);
          else DoMentionPopup.showPlatPicker(cx, cy);
        });
        chart.on('mouseover', p => { if (p.componentType === 'series') chart.getDom().style.cursor = 'pointer'; });
        chart.on('mouseout',  () => { chart.getDom().style.cursor = 'default'; });
      }

      const statsRow  = document.getElementById('statNewsRow');
      const socialRow = document.getElementById('statSocialRow');
      if (statsRow)  { statsRow.title  = 'Klik untuk lihat Online News'; statsRow.onclick  = e => DoMentionPopup.open('doc', e.clientX, e.clientY); }
      if (socialRow) { socialRow.title = 'Klik untuk lihat Social Media'; socialRow.onclick = e => DoMentionPopup.showPlatPicker(e.clientX, e.clientY); }
    }
  },

  /* ── SOV (PATCHED) ── */
  async loadSov() {
    const r = await fetch(`/mk/api/sentiment-by-media?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`);
    const d = await r.json();
    const data = d.data || [];
    document.getElementById('sovSkeleton').style.display = 'none';
    const sovBody = document.getElementById('sovCardBody');
    if (sovBody) sovBody.style.display = 'flex';
    if (!data.length) { if (sovBody) sovBody.innerHTML = emptyHtml(); return; }

    const fallback = ['#22c55e','#1d9bf0','#1877f2','#e1306c','#ff0000','#111827','#7c3aed','#f59e0b'];
    const totalAll = data.reduce((s,m) => s + (m.total||0), 0);
    const labels   = data.map(m => m.media);
    const counts   = data.map(m => m.total||0);
    const colors   = data.map((m,i) => DOCfg.colorMap[m.media] || fallback[i % fallback.length]);

    const chart = DOCharts.make('chSovPie');
    if (chart) {
      chart.setOption({
        animation: true, animationDuration: 900, animationEasing: 'cubicInOut',
        tooltip: {
          ...EC_TOOLTIP, trigger: 'item',
          formatter: p => {
            const pct = totalAll > 0 ? p.value / totalAll * 100 : 0;
            const pctStr = pct < 1 && pct > 0 ? '<1%' : pct.toFixed(1) + '%';
            return `<div style="font-weight:700;margin-bottom:4px;">${p.name}</div>
              <div>${numFmt(p.value)} mentions</div>
              <div style="opacity:.75;">${pctStr} dari total</div>`;
          }
        },
        legend: { show: false },
        graphic: [{
          type: 'text', left: 'center', top: 'center',
          style: {
            text: 'Total\nVoice',
            textAlign: 'center',
            fill: '#94a3b8',
            fontSize: 10,
            fontFamily: "'Poppins',sans-serif",
            fontWeight: '700',
            lineHeight: 16,
          }
        }],
        series: [{
          type: 'pie',
          radius: ['50%', '78%'],
          center: ['50%', '50%'],
          avoidLabelOverlap: true,
          itemStyle: { borderRadius: 6, borderColor: '#fff', borderWidth: 3 },
          label: { show: false },
          emphasis: {
            label: {
              show: true, fontSize: 13, fontWeight: '700', fontFamily: "'Poppins',sans-serif",
              formatter: p => {
                const pct = totalAll > 0 ? p.value / totalAll * 100 : 0;
                return `{n|${p.name}}\n{v|${numK(p.value)}}\n{p|${pct < 1 && pct > 0 ? '<1%' : pct.toFixed(1) + '%'}}`;
              },
              rich: {
                n: { fontSize: 10, color: '#64748b', fontWeight: '600', lineHeight: 17 },
                v: { fontSize: 16, color: '#1a202c', fontWeight: '700', lineHeight: 22 },
                p: { fontSize: 11, color: '#038047', fontWeight: '800', lineHeight: 17 }
              }
            },
            scale: true, scaleSize: 5,
            itemStyle: { shadowBlur: 16, shadowColor: 'rgba(0,0,0,.18)' }
          },
          data: labels.map((lb,i) => ({ name: lb, value: counts[i], itemStyle: { color: colors[i] } }))
        }]
      });
      chart.on('click', p => {
        const k = DOCfg.mediaKeyMap[p.name]; if (!k) return;
        const rect = chart.getDom().getBoundingClientRect();
        DoMentionPopup.open(k, rect.left + rect.width/2, rect.top + rect.height/2);
      });
      chart.on('mouseover', p => { if (p.componentType === 'series') chart.getDom().style.cursor = 'pointer'; });
      chart.on('mouseout',  () => { chart.getDom().style.cursor = 'default'; });
    }

    const legendEl = document.getElementById('sovLegendItems');
    if (legendEl) {
      legendEl.innerHTML = data.map((m,i) => {
        const pctFloat   = totalAll > 0 ? m.total / totalAll * 100 : 0;
        const pctDisplay = pctFloat === 0 ? '0%' : pctFloat < 1 ? '<1%' : pctFloat.toFixed(1) + '%';
        const pctBar     = Math.max(pctFloat, pctFloat > 0 ? 2 : 0);
        const k = DOCfg.mediaKeyMap[m.media] || '';
        return `<div class="sov-legend-item" ${k ? `onclick="DoMentionPopup.open('${k}',event.clientX,event.clientY)" title="Lihat mentions ${m.media}"` : ''}>
          <div class="sov-legend-item-row">
            <span class="sov-legend-dot" style="background:${colors[i]};"></span>
            <span class="sov-legend-name">${m.media}</span>
            <span class="sov-legend-pct" style="color:${colors[i]};">${pctDisplay}</span>
          </div>
          <div class="sov-legend-bar-wrap">
            <div class="sov-legend-bar" style="width:${pctBar}%;background:${colors[i]};"></div>
          </div>
        </div>`;
      }).join('');
    }
  },

  /* ── SENTIMENT LINE ── */
  async loadSentLine() {
    const r = await fetch(`/mk/api/sentiment-timeline?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`);
    const d = await r.json();
    hideSk('skSentiment');

    const datasets = [
      { name: 'Total',    data: d.values,              color: '#3b82f6', areaColor: 'rgba(59,130,246,.12)' },
      { name: 'Positive', data: d.sentiment?.positive,  color: '#22c55e', areaColor: 'transparent' },
      { name: 'Neutral',  data: d.sentiment?.neutral,   color: '#94a3b8', areaColor: 'transparent' },
      { name: 'Negative', data: d.sentiment?.negative,  color: '#ef4444', areaColor: 'transparent' },
    ];

    const chart = DOCharts.make('chSentiment');
    if (!chart) return;

    const xLabels = (d.dates||[]).map(dt => {
      try { const o = new Date(dt+'T00:00:00'); return `${o.getDate()}. ${o.toLocaleString('id-ID',{month:'short'})}`; } catch(e) { return dt; }
    });

    chart.setOption({
      animation: true, animationDuration: 900, animationEasing: 'cubicInOut',
      backgroundColor: '#ffffff',
      tooltip: {
        backgroundColor: '#ffffff', borderColor: '#e2e8f0', borderWidth: 1,
        padding: [12,16], textStyle: { color: '#1a202c', fontFamily: "'Poppins',sans-serif", fontSize: 12 },
        extraCssText: 'border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.12);',
        trigger: 'axis', axisPointer: { type: 'line', lineStyle: { color: '#e2e8f0', type: 'dashed', width: 1.5 } },
        formatter: params => {
          const idx = params[0]?.dataIndex ?? 0;
          const fullDt = d.dates?.[idx] ? new Date(d.dates[idx]+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'}) : '';
          const sorted = [...params].sort((a,b) => b.value - a.value);
          const rows = sorted.filter(p => p.value > 0).map(p =>
            `<div style="display:flex;align-items:center;justify-content:space-between;gap:20px;padding:2px 0;">
              <div style="display:flex;align-items:center;gap:7px;"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0;"></span><span style="font-size:12px;color:#64748b;">${p.seriesName}</span></div>
              <span style="font-size:12px;font-weight:700;color:#1a202c;">${numFmt(p.value)}</span>
            </div>`).join('');
          const total = params.reduce((s,p) => s + (p.value||0), 0);
          return `<div style="font-weight:700;font-size:12px;color:#1a202c;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #f1f5f9;">${fullDt}</div>${rows}
            <div style="border-top:1px solid #f1f5f9;margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;">
              <span style="font-size:11px;color:#94a3b8;">Total</span>
              <span style="font-size:12px;font-weight:700;color:#1a202c;">${numFmt(total)}</span>
            </div>`;
        }
      },
      legend: { bottom: 0, type: 'scroll', data: datasets.map(d => d.name), textStyle: { fontFamily: "'Poppins',sans-serif", fontSize: 11, fontWeight: '600', color: '#64748b' }, icon: 'circle', itemWidth: 10, itemHeight: 10, itemGap: 16 },
      grid: { top: 12, right: 20, bottom: 50, left: 60 },
      xAxis: { type: 'category', data: xLabels, boundaryGap: false, axisLine: { lineStyle: { color: '#e2e8f0' } }, axisTick: { show: false }, axisLabel: { fontFamily: "'Poppins',sans-serif", fontSize: 11, fontWeight: '600', color: '#64748b', maxRotate: 0 } },
      yAxis: { type: 'value', axisLine: { show: false }, axisTick: { show: false }, splitLine: { lineStyle: { color: '#f1f5f9', type: 'solid' } }, axisLabel: { fontFamily: "'Poppins',sans-serif", fontSize: 11, color: '#94a3b8', formatter: numK } },
      series: datasets.map(ds => ({
        name: ds.name, type: 'line', data: ds.data||[], smooth: .4,
        symbol: 'circle', symbolSize: xLabels.length <= 30 ? 5 : 0, showSymbol: xLabels.length <= 30,
        itemStyle: { color: ds.color, borderColor: '#fff', borderWidth: 2 },
        lineStyle: { color: ds.color, width: ds.name === 'Total' ? 2.5 : 2 },
        areaStyle: ds.areaColor !== 'transparent' ? { color: { type:'linear', x:0, y:0, x2:0, y2:1, colorStops:[{offset:0,color:'rgba(59,130,246,0.2)'},{offset:1,color:'rgba(59,130,246,0.02)'}] } } : undefined,
        emphasis: { focus: 'series', lineStyle: { width: 3.5 }, itemStyle: { symbolSize: 8, borderColor: '#fff', borderWidth: 2.5, shadowBlur: 10, shadowColor: ds.color + '88' } },
      }))
    });
  },

  /* ── SENTIMENT BY MEDIA (PATCHED) ── */
  async loadSentMedia() {
    const r = await fetch(`/mk/api/sentiment-by-media?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`);
    const d = await r.json();
    const data = d.data || [];
    hideSk('skSentimentMedia');
    if (!data.length) { document.getElementById('chSentimentMedia').parentElement.innerHTML = emptyHtml(); return; }

    /* Sort by total desc for visual hierarchy */
    const sorted  = [...data].sort((a,b) => ((b.positive||0)+(b.negative||0)) - ((a.positive||0)+(a.negative||0)));
    const medias  = sorted.map(m => m.media);
    const posData = sorted.map(m => m.positive||0);
    const negData = sorted.map(m => m.negative||0);

    /* Set height BEFORE initialising chart so canvas gets correct dimensions */
    const dynH = Math.max(360, medias.length * 56 + 80);
    const bodyEl = document.getElementById('sentMediaBody');
    const chartDom = document.getElementById('chSentimentMedia');
    if (bodyEl)   { bodyEl.style.height = dynH + 'px'; bodyEl.style.minHeight = dynH + 'px'; }
    if (chartDom) { chartDom.style.height = dynH + 'px'; }

    const chart = DOCharts.make('chSentimentMedia');
    if (!chart) return;
    chart.resize();

    chart.setOption({
      animation: true,
      animationDuration: 800,
      animationEasing: 'cubicOut',
      animationDelay: idx => idx * 60,
      backgroundColor: '#ffffff',
      tooltip: {
        trigger: 'axis',
        axisPointer: { type: 'shadow', shadowStyle: { color: 'rgba(3,128,71,.05)' } },
        backgroundColor: '#ffffff',
        borderColor: '#e2e8f0',
        borderWidth: 1,
        padding: [12, 16],
        textStyle: { color: '#1a202c', fontFamily: "'Poppins',sans-serif", fontSize: 12 },
        extraCssText: 'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.1);',
        formatter: params => {
          const idx   = params[0]?.dataIndex ?? 0;
          const media = medias[idx];
          const posV  = posData[idx] ?? 0;
          const negV  = negData[idx] ?? 0;
          const total = posV + negV;
          const posPct = total > 0 ? (posV/total*100).toFixed(1) : '0';
          const negPct = total > 0 ? (negV/total*100).toFixed(1) : '0';
          return `<div style="font-weight:700;font-size:13px;color:#1a202c;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid #f1f5f9;">${media}</div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:24px;padding:3px 0;">
              <div style="display:flex;align-items:center;gap:7px;">
                <span style="width:9px;height:9px;border-radius:50%;background:#22c55e;display:inline-block;flex-shrink:0;"></span>
                <span style="font-size:12px;color:#64748b;font-weight:500;">Positive</span>
              </div>
              <span style="font-size:13px;font-weight:700;color:#1a202c;">${numFmt(posV)} <span style="font-size:10px;color:#94a3b8;font-weight:500;">(${posPct}%)</span></span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:24px;padding:3px 0;">
              <div style="display:flex;align-items:center;gap:7px;">
                <span style="width:9px;height:9px;border-radius:50%;background:#ef4444;display:inline-block;flex-shrink:0;"></span>
                <span style="font-size:12px;color:#64748b;font-weight:500;">Negative</span>
              </div>
              <span style="font-size:13px;font-weight:700;color:#1a202c;">${numFmt(negV)} <span style="font-size:10px;color:#94a3b8;font-weight:500;">(${negPct}%)</span></span>
            </div>
            <div style="margin-top:8px;padding-top:8px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;">
              <span style="font-size:11px;color:#94a3b8;font-weight:600;">Total</span>
              <span style="font-size:12px;font-weight:700;color:#038047;">${numFmt(total)}</span>
            </div>`;
        }
      },
      legend: {
        show: true,
        bottom: 8,
        data: [{ name: 'Positive', icon: 'circle' }, { name: 'Negative', icon: 'circle' }],
        textStyle: { fontFamily: "'Poppins',sans-serif", fontSize: 11, fontWeight: '600', color: '#64748b' },
        itemWidth: 10, itemHeight: 10, itemGap: 24,
      },
      grid: { top: 20, right: 48, bottom: 48, left: 16, containLabel: true },
      xAxis: {
        type: 'value',
        axisLine: { show: false }, axisTick: { show: false },
        splitLine: { lineStyle: { color: '#f1f5f9', type: 'dashed', width: 1 } },
        axisLabel: { fontFamily: "'Poppins',sans-serif", fontSize: 10, color: '#94a3b8', formatter: numK, margin: 10 }
      },
      yAxis: {
        type: 'category',
        data: medias,
        boundaryGap: true,
        axisLine: { show: false }, axisTick: { show: false },
        axisLabel: {
          fontFamily: "'Poppins',sans-serif", fontSize: 12, fontWeight: '700',
          color: '#1a202c', margin: 14,
          formatter: v => v.length > 15 ? v.slice(0,14) + '…' : v,
          align: 'right',
        }
      },
      series: [
        {
          name: 'Positive', type: 'bar', data: posData,
          barMaxWidth: 14, barCategoryGap: '20%', barGap: '4%',
          itemStyle: {
            color: { type:'linear', x:0,y:0,x2:1,y2:0, colorStops:[{offset:0,color:'rgba(34,197,94,.18)'},{offset:1,color:'#22c55e'}] },
            borderRadius: [0,5,5,0]
          },
          emphasis: { itemStyle: { shadowBlur:10, shadowColor:'rgba(34,197,94,.4)' } },
          label: {
            show: true, position: 'right',
            fontFamily: "'Poppins',sans-serif", fontSize: 10, fontWeight: '700', color: '#22c55e',
            formatter: p => p.value > 0 ? numK(p.value) : ''
          }
        },
        {
          name: 'Negative', type: 'bar', data: negData,
          barMaxWidth: 14, barGap: '4%',
          itemStyle: {
            color: { type:'linear', x:0,y:0,x2:1,y2:0, colorStops:[{offset:0,color:'rgba(239,68,68,.18)'},{offset:1,color:'#ef4444'}] },
            borderRadius: [0,5,5,0]
          },
          emphasis: { itemStyle: { shadowBlur:10, shadowColor:'rgba(239,68,68,.4)' } },
          label: {
            show: true, position: 'right',
            fontFamily: "'Poppins',sans-serif", fontSize: 10, fontWeight: '700', color: '#ef4444',
            formatter: p => p.value > 0 ? numK(p.value) : ''
          }
        }
      ]
    });
  },

  /* ── BUZZER MAP ── */
  async loadMap() {
    const r = await fetch(`/mk/api/geo-users?project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}`);
    const d = await r.json();
    document.getElementById('mapSkeleton').style.display = 'none';
    const rows = d.data || [];
    const mapResult = this.renderMap('buzzMap', rows);
    this.buildLocationPanel('buzzMapList', rows, mapResult);
  },

  renderMap(elId, rows) {
    const map = L.map(elId, { center: [-2.5, 118], zoom: 5, scrollWheelZoom: false });
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: '© OpenStreetMap, © CARTO', subdomains: 'abcd', maxZoom: 19 }).addTo(map);
    if (!rows.length) return { map, markerRefs: [] };
    const maxCount = Math.max(...rows.map(p => parseInt(p.count||0)));
    const markerRefs = [];
    rows.forEach(p => {
      const lat = parseFloat(p.latitude||0), lng = parseFloat(p.longitude||0);
      if (lat === 0 && lng === 0) { markerRefs.push(null); return; }
      const name = p.name||'Unknown', count = parseInt(p.count||0);
      if (count >= 10) {
        let rad = Math.sqrt(count) * 2500; rad = Math.max(5000, Math.min(rad, 50000));
        L.circle([lat,lng], { radius: rad, fillColor: '#038047', color: '#038047', weight: 1, opacity: .2, fillOpacity: Math.min(.15 + (count/maxCount)*.4, .55) }).addTo(map);
      }
      const pin = L.marker([lat,lng], { icon: L.divIcon({ className: '', html: '<div style="width:13px;height:13px;background:#038047;border:2.5px solid #fff;border-radius:50%;box-shadow:0 2px 5px rgba(0,0,0,.4);"></div>', iconSize: [13,13], iconAnchor: [6.5,6.5] }) }).addTo(map)
        .bindPopup(`<div style="font-family:Poppins;text-align:center;padding:8px;"><div style="font-weight:700;font-size:15px;color:#1a202c;margin-bottom:6px;">${name}</div><div style="font-size:24px;font-weight:700;color:#038047;">${count.toLocaleString()}</div><div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:.8px;font-weight:600;">mentions</div></div>`);
      markerRefs.push({ marker: pin, lat, lng });
      const lbl = count > 999 ? (count/1000).toFixed(1) + 'k' : count;
      L.marker([lat,lng], { icon: L.divIcon({ className: 'circle-label', html: `<div style="font-family:Poppins;font-size:11px;font-weight:800;color:#fff;background:rgba(3,128,71,.92);padding:3px 8px;border-radius:12px;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4);white-space:nowrap;">${lbl}</div>`, iconSize: [40,20], iconAnchor: [20,25] }), interactive: false }).addTo(map);
    });
    return { map, markerRefs };
  },

  buildLocationPanel(listId, rows, mapResult) {
    const listEl = document.getElementById(listId); if (!listEl) return;
    const { map, markerRefs } = mapResult;
    const validRows = rows.filter(p => !(parseFloat(p.latitude||0) === 0 && parseFloat(p.longitude||0) === 0));
    if (!validRows.length) { listEl.innerHTML = '<div class="do-empty" style="padding:24px 14px;font-size:12px;">No location data</div>'; return; }
    const sorted = [...validRows].sort((a,b) => parseInt(b.count||0) - parseInt(a.count||0));
    listEl.innerHTML = sorted.map((p, rank) => {
      const name = p.name||'Unknown', count = parseInt(p.count||0);
      const label = count > 999 ? (count/1000).toFixed(1) + 'k' : count;
      return `<div class="location-item" data-name="${name}">
        <span class="location-item-rank">${rank+1}</span>
        <div class="location-item-info"><div class="location-item-name" title="${name}">${name}</div><div class="location-item-count">${label} mentions</div></div>
        <div class="location-item-dot" style="background:#038047;"></div>
      </div>`;
    }).join('');
    listEl.querySelectorAll('.location-item').forEach(item => {
      item.addEventListener('click', () => {
        const name = item.dataset.name, targetRow = validRows.find(p => (p.name||'Unknown') === name);
        if (!targetRow) return;
        const lat = parseFloat(targetRow.latitude||0), lng = parseFloat(targetRow.longitude||0);
        if (lat === 0 && lng === 0) return;
        map.flyTo([lat,lng], 8, { animate: true, duration: 1 });
        const ref = markerRefs.find(r => r && Math.abs(r.lat-lat) < .001 && Math.abs(r.lng-lng) < .001);
        if (ref) setTimeout(() => ref.marker.openPopup(), 800);
        listEl.querySelectorAll('.location-item').forEach(i => i.classList.remove('active'));
        item.classList.add('active');
      });
    });
  }
};

/* ══════════════════════════════════════════════════════
   DETAIL PANEL
══════════════════════════════════════════════════════ */
const DoDetailPanel = {
  open(item, platform) {
    const panel = document.getElementById('doDetailPanel'), body = document.getElementById('dpBody'), title = document.getElementById('dpTitle');
    const meta = DOCfg.platMeta[platform] || { label: platform, color: '#038047' };
    const _nameRaw = (() => {
      if (platform === 'fb')        return item.from_name||item.page_name||item.account_name||(item.from&&item.from.name)||null;
      if (platform === 'instagram') return item.username||item.user_name||item.account_name||item.author_name||null;
      if (platform === 'tiktok')    return item.author_nickname||item.nickname||item.author_name||item.unique_id||null;
      if (platform === 'youtube')   return item.channel_title||item.channel_name||item.author_name||item.uploader||null;
      return null;
    })();
    const name   = (_nameRaw||item.author_name||item.channel_name||item.publisher||item.source_name||'Unknown').trim();
    const handle = ((platform==='instagram'?item.username:'')||item.author_scr_name||item.screen_name||item.username||item.handle||'').trim();
    const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
    const av  = (item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||'').trim();
    const url  = item.url||item.link||'', date = item.date_created||'';
    const sentRaw = String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
    const sent = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif'?'pos':sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'||sentRaw==='negatif'?'neg':'neu';
    const sentLbl = sent==='pos'?'Positive':sent==='neg'?'Negative':'Neutral';
    title.textContent = name;
    const words = name.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
    const ini = (words.length >= 2 ? (words[0][0] + words[words.length-1][0]) : (words[0]?.[0]||name[0]||'?')).toUpperCase();
    const avHtml = (av && av.startsWith('http')) ? `<img src="${esc(av)}" onerror="this.parentElement.textContent='${ini}'">` : ini;
    let dtFmt = ''; if (date) { try { dtFmt = new Date(date).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}); } catch(e) { dtFmt = date.split('T')[0]; } }

    let mediaHtml = '';
    if (platform === 'youtube') { const ytId = (url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/)||[])[1]; if (ytId) mediaHtml = `<div class="dp-video-wrap"><iframe class="dp-video-iframe" src="https://www.youtube.com/embed/${ytId}?rel=0&modestbranding=1" allowfullscreen></iframe></div>`; }
    else if (platform === 'tiktok') { const tid = (url.match(/\/video\/(\d+)/)||[])[1]; if (tid) mediaHtml = `<div class="dp-video-wrap"><iframe class="dp-video-iframe" src="https://www.tiktok.com/embed/v2/${tid}" allow="autoplay" allowfullscreen></iframe></div>`; }
    else { const imgUrl = item.image_url||item.thumbnail||item.media_url||item.picture||''; if (imgUrl) mediaHtml = `<div class="dp-media-wrap"><img class="dp-media-img" src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'"></div>`; }

    const statsMap = { twit:[['Retweet',item.num_retweeted||item.retweet_count||0],['Like',item.num_likes||item.favorite_count||0],['Quote',item.num_quote||0]], fb:[['Like',item.likes||item.num_likes||0],['Share',item.shares||item.share_count||0],['Comment',item.num_comments||0]], instagram:[['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||item.comment_count||0],['View',item.num_views||item.views||0]], youtube:[['View',item.num_views||item.views||0],['Like',item.num_likes||item.likes||0],['Comment',item.num_comments||0]], tiktok:[['Play',item.views||item.play_count||0],['Like',item.likes||item.digg_count||0],['Share',item.shares||item.share_count||0]], doc:[['Read',item.num_views||0],['Share',item.num_share||0],['Comment',item.num_comments||0]] };
    const stats = statsMap[platform] || [];
    const statsHtml = stats.some(s => parseInt(s[1]) > 0) ? `<div class="dp-stats-grid">${stats.map(([l,v]) => `<div class="dp-stat-box"><div class="dp-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="dp-stat-label">${l}</div></div>`).join('')}</div>` : '';

    body.innerHTML = `
      <div class="dp-avatar-row">
        <div class="dp-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
        <div>
          <div class="dp-author-name">${esc(name)}</div>
          ${handle ? `<div class="dp-author-handle">${esc(handle.startsWith('@') ? handle : '@' + handle)}</div>` : ''}
          <span style="background:${meta.color}22;color:${meta.color};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:800;display:inline-block;margin-top:4px;">${meta.label}</span>
        </div>
      </div>
      ${dtFmt ? `<div class="dp-meta-row"><span>${dtFmt}</span></div>` : ''}
      <span class="dp-sent-big dp-sent-big-${sent}">${sentLbl}</span>
      ${mediaHtml}
      ${content ? `<div class="dp-content-text">${esc(content)}</div>` : ''}
      ${statsHtml}
      ${url ? `<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="dp-link-btn"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Lihat ${meta.label} Asli</a>` : ''}`;
    panel.classList.add('visible');
  },
  close() { document.getElementById('doDetailPanel').classList.remove('visible'); }
};

/* ══════════════════════════════════════════════════════
   MENTION POPUP
══════════════════════════════════════════════════════ */
const DoMentionPopup = {
  _drag: false, _ox: 0, _oy: 0,
  _cache: {}, _items: [], _curPlat: null,

  init() {
    const popup = document.getElementById('doMentionPopup'), header = document.getElementById('doPopupHeader');
    header.addEventListener('mousedown', e => {
      this._drag = true;
      const r = popup.getBoundingClientRect(); this._ox = e.clientX - r.left; this._oy = e.clientY - r.top;
      document.body.style.userSelect = 'none';
    });
    document.addEventListener('mousemove', e => {
      if (!this._drag) return;
      const vw = window.innerWidth, vh = window.innerHeight;
      popup.style.left = Math.max(0, Math.min(e.clientX - this._ox, vw - popup.offsetWidth)) + 'px';
      popup.style.top  = Math.max(0, Math.min(e.clientY - this._oy, vh - popup.offsetHeight)) + 'px';
    });
    document.addEventListener('mouseup', () => { this._drag = false; document.body.style.userSelect = ''; });
    document.addEventListener('mousedown', e => {
      const pp = document.getElementById('doPlatPicker');
      if (pp?.classList.contains('visible') && !pp.contains(e.target)) pp.classList.remove('visible');
    });
  },

  _pos(popup, x, y) {
    const pw = 480, ph = 600, vw = window.innerWidth, vh = window.innerHeight;
    let left = x + 18, top = y - 40;
    if (left + pw > vw - 12) left = x - pw - 18;
    if (top  + ph > vh - 12) top  = vh - ph - 12;
    if (top < 8) top = 8; if (left < 8) left = 8;
    popup.style.left = left + 'px'; popup.style.top = top + 'px';
  },

  showPlatPicker(x, y) {
    const pp = document.getElementById('doPlatPicker'); if (!pp) return;
    const pw = 175, ph = 240, vw = window.innerWidth, vh = window.innerHeight;
    let left = x + 10, top = y - 10;
    if (left + pw > vw - 8) left = x - pw - 10; if (top + ph > vh - 8) top = vh - ph - 8; if (top < 8) top = 8;
    pp.style.left = left + 'px'; pp.style.top = top + 'px';
    pp.classList.add('visible');
  },

  openPlatform(platform) {
    const pp = document.getElementById('doPlatPicker');
    const x = pp ? parseFloat(pp.style.left) + 90 : window.innerWidth / 2;
    const y = pp ? parseFloat(pp.style.top) + 20  : window.innerHeight / 2;
    if (pp) pp.classList.remove('visible');
    this.open(platform, x, y);
  },

  async open(platform, x, y) {
    const popup = document.getElementById('doMentionPopup');
    const meta  = DOCfg.platMeta[platform] || { label: platform, color: '#038047' };
    this._curPlat = platform;
    DoDetailPanel.close();
    document.getElementById('doPopupDot').style.background   = meta.color;
    document.getElementById('doPopupTitle').textContent      = meta.label;
    document.getElementById('doPopupMeta').textContent       = DOCfg.sd + ' – ' + DOCfg.ed;
    document.getElementById('doPopupCount').textContent      = '…';
    const list = document.getElementById('doPopupList');
    list.innerHTML = `<div class="do-popup-loading"><div class="do-popup-spinner"></div>Memuat mentions…</div>`;
    popup.classList.add('visible');
    this._pos(popup, x, y);
    const key = `${DOCfg.pid}_${platform}_${DOCfg.sd}_${DOCfg.ed}`;
    try {
      if (!this._cache[key]) this._cache[key] = await this._fetch(platform);
      this._items = this._cache[key];
      document.getElementById('doPopupCount').textContent = this._items.length.toLocaleString();
      this._render(list, this._items, platform, meta.color);
    } catch(err) {
      list.innerHTML = `<div class="do-popup-empty">Gagal memuat data<br><small style="color:#94a3b8;">${err.message}</small></div>`;
      document.getElementById('doPopupCount').textContent = '0';
    }
  },

  close() {
    const popup = document.getElementById('doMentionPopup');
    if (popup) popup.classList.remove('visible');
    DoDetailPanel.close();
  },

  exportCurrent() {
    if (!this._items?.length) { alert('Tidak ada data.'); return; }
    const labels = { doc:'Online_News', twit:'Twitter', fb:'Facebook', instagram:'Instagram', youtube:'YouTube', tiktok:'TikTok' };
    const rows = this._items.map(item => ({
      author:  item.author_name||item.channel_name||item.publisher||item.source_name||'',
      handle:  item.author_scr_name||item.screen_name||item.username||'',
      content: (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,500),
      sentiment: item.class_sentiment||item.sentiment||'',
      url:   item.url||item.link||'', date: item.date_created||'',
      likes: item.num_likes||item.likes||item.favorite_count||0,
      shares: item.num_retweeted||item.shares||item.retweet_count||0,
      views:  item.num_views||item.views||item.play_count||0,
    }));
    DataExporter.downloadCSV(rows, `${labels[this._curPlat]||this._curPlat}_${DOCfg.sd}_${DOCfg.ed}`);
  },

  async _fetch(platform) {
    const q = `project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}&rows=500&start=0`;
    if (platform === 'instagram') {
      for (const sub of ['postbylike','postbycomment','postbydate','']) {
        try {
          const res = await fetch(`/mk/api/news/ig-top-status?${q}${sub?'&sub='+sub:''}`);
          const data = await res.json();
          const items = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
          if (items.length > 0) return items;
        } catch(e) { continue; }
      }
      return [];
    }
    const eps = { doc: `/mk/api/news/mentions?${q}`, twit: `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`, fb: `/mk/api/news/fb-top-status?${q}&sub=fblike`, youtube: `/mk/api/news/ytb-top-status?${q}`, tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike` };
    const url = eps[platform]; if (!url) throw new Error('Platform tidak dikenali');
    const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 30000);
    const res = await fetch(url, { signal: ctrl.signal }); clearTimeout(tid);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    let items = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
    if (platform === 'doc') items = items.filter(m => { const tc = String(m.tcode||'').toLowerCase(), mt = String(m.media_type||'').toLowerCase(); return tc==='berita'||mt==='berita'||mt==='doc'||mt==='news'||mt==='online'||mt==='article'; });
    return items;
  },

  _render(list, items, platform, color) {
    if (!items.length) { list.innerHTML = `<div class="do-popup-empty">Tidak ada mentions untuk periode ini.</div>`; return; }
    const SHOW = 60;
    list.innerHTML = items.slice(0, SHOW).map(item => {
      const _nameRaw = (() => {
        if (platform === 'fb')        return item.from_name||item.page_name||item.account_name||(item.from&&item.from.name)||null;
        if (platform === 'instagram') return item.username||item.user_name||item.account_name||item.author_name||null;
        if (platform === 'tiktok')    return item.author_nickname||item.nickname||item.author_name||item.unique_id||null;
        if (platform === 'youtube')   return item.channel_title||item.channel_name||item.author_name||null;
        return null;
      })();
      const name = (_nameRaw||item.author_name||item.channel_name||item.publisher||item.source_name||'Unknown').trim();
      const isNumericId = /^\d{8,}$/.test(name); const displayName = isNumericId ? `User ${name.slice(-4)}` : name;
      const rawHandle = ((platform==='instagram'?item.username:'')||item.author_scr_name||item.screen_name||item.username||'').trim();
      const handle = (() => { if (!rawHandle) return ''; const wa = ['twit','instagram','tiktok'].includes(platform) ? (rawHandle.startsWith('@') ? rawHandle : '@' + rawHandle) : rawHandle; return wa.replace(/^@/,'').toLowerCase() === displayName.toLowerCase() ? '' : wa; })();
      const text = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0, 155);
      const av = (item.avatar_url||item.profile_image_url||item.author_image||item.profile_image||item.thumbnail||'').trim();
      const words = displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
      const ini = (words.length >= 2 ? (words[0][0] + words[words.length-1][0]) : (words[0]?.[0]||displayName[0]||'?')).toUpperCase();
      const safeIni = ini.replace(/['"]/g,'');
      const sentRaw = String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
      const sent = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif'?'pos':sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'||sentRaw==='negatif'?'neg':'neu';
      const sentLbl = sent==='pos'?'Pos':sent==='neg'?'Neg':'Neu';
      const dt = (item.date_created||item.created_at||'').split('T')[0];
      const avHtml = (av && av.startsWith('http')) ? `<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.setAttribute('data-ini','${safeIni}');">` : ini;
      const eng = (() => {
        const f = n => parseInt(n)||0 > 0 ? parseInt(n).toLocaleString() : null, parts = [];
        if (platform==='twit')      { const rt=f(item.num_retweeted||item.retweet_count), lk=f(item.num_likes||item.favorite_count); if(rt)parts.push('RT '+rt); if(lk)parts.push('Like '+lk); }
        else if (platform==='youtube')   { const v=f(item.num_views||item.views), lk=f(item.num_likes||item.likes); if(v)parts.push('Views '+v); if(lk)parts.push('Like '+lk); }
        else if (platform==='tiktok')    { const v=f(item.views||item.play_count), lk=f(item.likes||item.digg_count); if(v)parts.push('Views '+v); if(lk)parts.push('Like '+lk); }
        else if (platform==='instagram') { const lk=f(item.num_likes||item.likes), cm=f(item.num_comments||item.comment_count); if(lk)parts.push('Like '+lk); if(cm)parts.push('Komen '+cm); }
        else if (platform==='fb')        { const lk=f(item.likes||item.num_likes), sh=f(item.shares||item.share_count); if(lk)parts.push('Like '+lk); if(sh)parts.push('Share '+sh); }
        return parts.join(' · ');
      })();
      const itemData = esc(JSON.stringify(item));
      return `<div class="do-popup-item" data-item='${itemData}' data-plat="${platform}" onclick="DoMentionPopup._onItemClick(this)">
        <div class="do-popup-avatar" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
        <div class="do-popup-item-body">
          <div class="do-popup-author">${esc(displayName)}</div>
          ${handle ? `<div class="do-popup-handle">${esc(handle)}</div>` : ''}
          <div class="do-popup-content">${esc(text||'(tidak ada konten)')}</div>
          <div class="do-popup-footer">
            <span class="do-popup-sent do-popup-sent-${sent==='pos'?'p':sent==='neg'?'n':'u'}">${sentLbl}</span>
            ${eng ? `<span>${esc(eng)}</span>` : ''}
            ${dt  ? `<span style="margin-left:auto;">${dt}</span>` : ''}
          </div>
        </div>
      </div>`;
    }).join('');
    if (items.length > SHOW) list.insertAdjacentHTML('beforeend', `<div style="padding:9px 16px;text-align:center;font-size:11px;font-weight:600;color:#64748b;background:#f8fafc;border-top:1px dashed #e2e8f0;">+${(items.length-SHOW).toLocaleString()} mentions lainnya</div>`);
  },

  _onItemClick(el) {
    try {
      const raw  = el.getAttribute('data-item');
      const item = JSON.parse(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"'));
      const plat = el.dataset.plat || this._curPlat;
      DoDetailPanel.open(item, plat);
    } catch(e) { console.warn('Detail parse error:', e); }
  }
};

/* ══════════════════════════════════════════════════════
   DATA EXPORTER
══════════════════════════════════════════════════════ */
const DataExporter = {
  showProgress(title, sub) { document.getElementById('exportProgTitle').textContent = title; document.getElementById('exportProgSub').textContent = sub; document.getElementById('exportProgBar').style.width = '0%'; document.getElementById('exportOverlay').classList.add('show'); },
  setProgress(pct, sub)    { document.getElementById('exportProgBar').style.width = pct + '%'; if (sub) document.getElementById('exportProgSub').textContent = sub; },
  hideProgress()           { setTimeout(() => document.getElementById('exportOverlay').classList.remove('show'), 500); },

  async fetchPlatform(platform) {
    const q = `project_id=${DOCfg.pid}&start_date=${DOCfg.sd}&end_date=${DOCfg.ed}&rows=9999&start=0`;
    const eps = { doc: `/mk/api/news/mentions?${q}`, twit: `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`, fb: `/mk/api/news/fb-top-status?${q}&sub=fblike`, instagram: `/mk/api/news/ig-top-status?${q}&sub=postbylike`, youtube: `/mk/api/news/ytb-top-status?${q}`, tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike` };
    const url = eps[platform]; if (!url) return [];
    try { const res = await fetch(url); const data = await res.json(); return Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []); } catch { return []; }
  },

  flatten(item, platform) {
    return { platform, author: item.author_name||item.channel_name||item.publisher||item.source_name||'', handle: item.author_scr_name||item.screen_name||item.username||'', content: (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,500), sentiment: item.class_sentiment||item.sentiment||'', url: item.url||item.link||'', date: item.date_created||'', likes: item.num_likes||item.likes||item.favorite_count||0, shares: item.num_retweeted||item.shares||item.retweet_count||0, views: item.num_views||item.views||item.play_count||0 };
  },

  async exportPlatform(platform) {
    document.getElementById('exportDropdown').classList.remove('show');
    const labels = { doc:'Online News', twit:'Twitter', fb:'Facebook', instagram:'Instagram', youtube:'YouTube', tiktok:'TikTok' };
    this.showProgress(`Mengexport ${labels[platform]||platform}…`, 'Mengambil data…');
    this.setProgress(20);
    const items = await this.fetchPlatform(platform);
    this.setProgress(70, `${items.length} items ditemukan…`);
    this.downloadCSV(items.map(i => this.flatten(i, labels[platform])), `${platform}_${DOCfg.sd}_${DOCfg.ed}`);
    this.setProgress(100, 'Selesai!'); this.hideProgress();
  },

  async exportAll(format) {
    document.getElementById('exportDropdown').classList.remove('show');
    this.showProgress('Mengexport semua data…', 'Mempersiapkan…');
    const platforms = ['doc','twit','fb','instagram','youtube','tiktok'];
    const labels = { doc:'Online News', twit:'Twitter', fb:'Facebook', instagram:'Instagram', youtube:'YouTube', tiktok:'TikTok' };
    let allRows = [];
    for (let i = 0; i < platforms.length; i++) {
      const p = platforms[i]; this.setProgress(Math.round(i / platforms.length * 80), `Mengambil ${labels[p]}…`);
      const items = await this.fetchPlatform(p);
      allRows = allRows.concat(items.map(item => this.flatten(item, labels[p])));
    }
    this.setProgress(90, `${allRows.length} total mentions…`);
    const fn = `data_overview_${DOCfg.sd}_${DOCfg.ed}`;
    if (format === 'xlsx') this.downloadXLSX(allRows, fn);
    else this.downloadCSV(allRows, fn);
    this.setProgress(100, 'Selesai!'); this.hideProgress();
  },

  downloadCSV(rows, filename) {
    if (!rows.length) { alert('Tidak ada data untuk diexport.'); return; }
    const headers = Object.keys(rows[0]);
    const csv = [headers.join(','), ...rows.map(r => headers.map(h => `"${String(r[h]||'').replace(/"/g,'""')}"`).join(','))].join('\r\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob), a = document.createElement('a');
    a.href = url; a.download = filename + '.csv'; a.click(); URL.revokeObjectURL(url);
  },

  downloadXLSX(rows, filename) {
    if (!rows.length) { alert('Tidak ada data untuk diexport.'); return; }
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet(rows);
    ws['!cols'] = [{wch:14},{wch:24},{wch:18},{wch:60},{wch:12},{wch:50},{wch:18},{wch:10},{wch:10},{wch:12}];
    XLSX.utils.book_append_sheet(wb, ws, 'All Data');
    XLSX.writeFile(wb, filename + '.xlsx');
  }
};

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  DPicker.init();
  DoMentionPopup.init();
  DOLoader.init();

  /* Export dropdown toggle */
  const btn = document.getElementById('exportMasterBtn'), dd = document.getElementById('exportDropdown');
  if (btn && dd) {
    btn.addEventListener('click', e => { e.stopPropagation(); dd.classList.toggle('show'); });
    document.addEventListener('click', () => dd.classList.remove('show'));
    dd.addEventListener('click', e => e.stopPropagation());
  }
});
</script>
@endsection