@extends('mk.layouts.app')

@section('title', 'Media Statistic - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
    --primary-green-light: rgba(3,128,71,0.08);
    --primary-green-border: rgba(3,128,71,0.2);
    --blue:           #0284c7;
    --blue-light:     rgba(2,132,199,.08);
    --fb-color:       #1877f2;
    --ig-color:       #e1306c;
    --yt-color:       #ff0000;
    --tt-color:       #111827;
    --twitter-color:  #1d9bf0;

    --text-primary:   #1a202c;
    --text-secondary: #64748b;
    --text-muted:     #94a3b8;

    --bg-white:       #ffffff;
    --bg-body:        #f0f4f8;
    --bg-gray-50:     #f8fafc;
    --bg-gray-100:    #f1f5f9;

    --border-gray:    #e2e8f0;
    --border-light:   #f1f5f9;

    --shadow-xs:      0 1px 2px rgba(0,0,0,.05);
    --shadow-sm:      0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:      0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg:      0 10px 15px -3px rgba(0,0,0,.1);
    --shadow-xl:      0 20px 40px -8px rgba(0,0,0,.18);

    --radius:         16px;
    --radius-sm:      12px;
    --radius-xs:      8px;
    --transition:     all 0.2s cubic-bezier(0.4,0,0.2,1);
    --font:           'Poppins', -apple-system, sans-serif;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: var(--font); background: var(--bg-body); color: var(--text-primary); }

  .ms-page {
    padding: 24px;
    max-width: 1600px;
    margin: 0 auto;
    min-height: 100vh;
    background: var(--bg-body);
  }

  .page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 12px;
  }

  .page-header-left h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 6px 0;
    letter-spacing: -0.4px;
  }

  .page-header-left p {
    font-size: 14px;
    color: var(--text-secondary);
    font-weight: 500;
    margin: 0;
  }

  .ms-refresh-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-family: var(--font);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 4px 14px rgba(0,0,0,.2);
  }

  .ms-refresh-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,.25);
  }

  .ms-refresh-btn svg {
    width: 15px; height: 15px;
    stroke: currentColor; fill: none;
    stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
  }

  .filter-card {
    background: var(--bg-white);
    border-radius: var(--radius);
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
  }

  .filter-content {
    display: flex;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
  }

  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .filter-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: .5px;
  }

  .filter-select {
    padding: 10px 14px;
    border: 1px solid var(--border-gray);
    border-radius: var(--radius-sm);
    font-family: var(--font);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    background: var(--bg-gray-50);
    outline: none;
    transition: var(--transition);
    min-width: 200px;
    cursor: pointer;
  }

  .filter-select:focus {
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px var(--primary-green-light);
  }

  .date-picker-trigger {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    background: var(--bg-gray-50);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius-sm);
    font-family: var(--font);
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: var(--transition);
    min-width: 300px;
  }

  .date-picker-trigger:hover {
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px var(--primary-green-light);
  }

  .date-picker-trigger svg { width: 16px; height: 16px; color: var(--text-secondary); flex-shrink: 0; }
  .date-picker-trigger span { flex: 1; text-align: left; }

  .apply-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-family: var(--font);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 4px 12px rgba(3,128,71,.2);
    white-space: nowrap;
  }

  .apply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(3,128,71,.3);
  }

  .apply-btn svg {
    width: 16px; height: 16px;
    stroke: currentColor; fill: none;
    stroke-width: 2.5; stroke-linecap: round;
  }

  /* DATE PICKER MODAL */
  .date-picker-modal {
    position: fixed;
    inset: 0;
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,.5);
    backdrop-filter: blur(8px);
  }

  .date-picker-modal.show { display: flex; }

  .date-picker-overlay {
    position: absolute;
    inset: 0;
    cursor: pointer;
  }

  .date-picker-container {
    position: relative;
    z-index: 1;
    background: #fff;
    border-radius: var(--radius);
    box-shadow: 0 25px 50px rgba(0,0,0,.3);
    display: flex;
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    animation: dpUp .3s ease-out;
  }

  @keyframes dpUp {
    from { opacity: 0; transform: translateY(20px) scale(.95); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }

  .date-picker-sidebar {
    width: 180px;
    background: var(--bg-gray-50);
    border-right: 1px solid var(--border-gray);
    padding: 16px 12px;
    border-radius: var(--radius) 0 0 var(--radius);
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex-shrink: 0;
  }

  .date-preset {
    padding: 10px 16px;
    background: transparent;
    border: none;
    border-radius: var(--radius-xs);
    font-family: var(--font);
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    text-align: left;
    cursor: pointer;
    transition: var(--transition);
  }

  .date-preset:hover  { background: var(--bg-white); color: var(--primary-green); }
  .date-preset.active { background: var(--primary-green); color: #fff; }

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
    width: 36px; height: 36px;
    border-radius: var(--radius-xs);
    background: var(--bg-gray-50);
    border: 1px solid var(--border-gray);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    flex-shrink: 0;
  }

  .nav-btn:hover { background: var(--primary-green); border-color: var(--primary-green); color: #fff; }
  .nav-btn svg   { width: 20px; height: 20px; }

  .calendars-wrapper { display: flex; gap: 24px; flex: 1; }
  .calendar          { flex: 1; display: flex; flex-direction: column; }

  .calendar-month {
    font-size: 16px; font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 16px;
    text-align: center;
  }

  .calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7,1fr);
    gap: 4px;
    margin-bottom: 8px;
  }

  .weekday {
    text-align: center;
    font-size: 11px; font-weight: 700;
    color: var(--text-secondary);
    padding: 8px 0;
  }

  .calendar-days {
    display: grid;
    grid-template-columns: repeat(7,1fr);
    gap: 4px;
  }

  .calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px; font-weight: 500;
    border-radius: var(--radius-xs);
    cursor: pointer;
    transition: var(--transition);
    color: var(--text-primary);
    background: transparent;
    border: none;
    padding: 0;
    font-family: var(--font);
  }

  .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
  .calendar-day.other-month { color: #cbd5e1; cursor: default; }
  .calendar-day.disabled    { color: #e2e8f0; cursor: not-allowed; }
  .calendar-day.today       { border: 2px solid var(--primary-green); }
  .calendar-day.selected    { background: var(--primary-green); color: #fff; }
  .calendar-day.in-range    { background: var(--primary-green-light); color: var(--primary-green); }

  .date-picker-display {
    padding: 16px 20px;
    background: var(--bg-gray-50);
    border-radius: var(--radius-sm);
    text-align: center;
    margin-bottom: 20px;
    border: 1px solid var(--border-gray);
  }

  .date-picker-display span {
    font-size: 14px; font-weight: 600;
    color: var(--text-primary);
  }

  .date-picker-footer {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
  }

  .cancel-btn, .apply-date-btn {
    padding: 10px 24px;
    border-radius: 10px;
    font-family: var(--font);
    font-size: 14px; font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    border: none;
  }

  .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
  .cancel-btn:hover { background: var(--border-gray); }

  .apply-date-btn {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(3,128,71,.2);
  }

  .apply-date-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(3,128,71,.3);
  }

  /* SECTION HEADERS */
  .ms-section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    margin-top: 4px;
  }

  .ms-section-icon {
    width: 36px; height: 36px;
    border-radius: var(--radius-sm);
    background: var(--primary-green-light);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .ms-section-icon svg {
    width: 18px; height: 18px;
    stroke: var(--primary-green); fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
  }

  .ms-section-title {
    font-size: 13px; font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: .8px;
  }

  .ms-section-line {
    flex: 1;
    height: 1.5px;
    background: var(--border-gray);
    border-radius: 1px;
  }

  /* DO-CARD */
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
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    opacity: 0;
    transition: opacity .3s;
  }

  .do-card:hover { box-shadow: var(--shadow-lg); border-color: var(--primary-green-border); }
  .do-card:hover::before { opacity: 1; }

  .do-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-gray);
    flex-shrink: 0;
  }

  .do-card-head-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .do-head-icon {
    width: 40px; height: 40px;
    border-radius: var(--radius-sm);
    background: linear-gradient(135deg, var(--primary-green-light) 0%, rgba(3,128,71,.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .do-head-icon svg {
    width: 20px; height: 20px;
    fill: none; stroke: var(--primary-green);
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
  }

  .do-card-title   { font-size: 16px; font-weight: 700; color: var(--text-primary); line-height: 1.3; }
  .do-card-subtitle{ font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 2px; }

  .do-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    background: var(--bg-gray-100);
    color: var(--text-secondary);
    white-space: nowrap;
    flex-shrink: 0;
  }

  .do-card-body { padding: 20px; flex: 1; }

  /* STAT CARDS */
  .ms-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
  }

  .ms-stat-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius);
    padding: 20px 22px;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    cursor: default;
  }

  .ms-stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--stat-bar, linear-gradient(90deg, var(--primary-green), var(--primary-green-dark)));
    opacity: 0;
    transition: opacity .25s;
  }

  .ms-stat-card:hover { box-shadow: var(--shadow-lg); border-color: var(--primary-green-border); transform: translateY(-2px); }
  .ms-stat-card:hover::before { opacity: 1; }

  .ms-stat-card--mass   { --stat-bar: linear-gradient(90deg, #0284c7, #0369a1); }
  .ms-stat-card--social { --stat-bar: linear-gradient(90deg, #038047, #026738); }
  .ms-stat-card--total  { --stat-bar: linear-gradient(90deg, #7c3aed, #5b21b6); }
  .ms-stat-card--period { --stat-bar: linear-gradient(90deg, #d97706, #b45309); }
  .ms-stat-card--clickable { cursor: pointer; }

  .ms-stat-label {
    font-size: 11px; font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .ms-stat-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

  .ms-stat-value {
    font-size: 32px; font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -1px;
    line-height: 1;
    min-height: 40px;
    display: flex;
    align-items: center;
  }

  .ms-stat-sub  { font-size: 11px; color: var(--text-muted); font-weight: 500; margin-top: 7px; }

  .ms-stat-hint {
    font-size: 10px; color: var(--primary-green); font-weight: 600;
    margin-top: 8px;
    display: flex; align-items: center; gap: 4px;
    opacity: .85;
  }

  .ms-stat-hint svg { width: 10px; height: 10px; stroke: currentColor; fill: none; stroke-width: 2.5; }

  /* PLATFORM MINI CARDS */
  .ms-platform-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
    margin-bottom: 20px;
  }

  .ms-plat-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius-sm);
    padding: 14px 16px;
    box-shadow: var(--shadow-xs);
    display: flex;
    align-items: center;
    gap: 12px;
    transition: var(--transition);
    cursor: default;
  }

  .ms-plat-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); border-color: var(--primary-green-border); }
  .ms-plat-card[onclick]:hover .ms-plat-card__name::after {
    content: ' ↗';
    font-size: 9px;
    color: var(--primary-green);
    opacity: .7;
  }

  .ms-plat-card__icon {
    width: 40px; height: 40px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }

  .ms-plat-card__name {
    font-size: 10px; font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .5px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }

  .ms-plat-card__count {
    font-size: 18px; font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -.5px;
    min-height: 26px;
    display: flex; align-items: center;
  }

  /* GRID LAYOUTS */
  .ms-grid-3-2 { display: grid; grid-template-columns: 1.55fr 1fr; gap: 20px; margin-bottom: 20px; }
  .ms-grid-2-3 { display: grid; grid-template-columns: 1fr 1.55fr; gap: 20px; margin-bottom: 20px; }
  .ms-grid-2   { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
  .ms-mb20     { margin-bottom: 20px; }

  /* CHART HEIGHT HELPERS */
  .ms-ch-300 { position: relative; height: 300px; }
  .ms-ch-280 { position: relative; height: 280px; }
  .ms-ch-320 { position: relative; height: 320px; }
  .ms-ch-340 { position: relative; height: 340px; }

  /* SOV LAYOUT */
  .sov-card-body {
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0;
  }

  /* TAB SYSTEM */
  .ms-tabs {
    display: flex;
    gap: 4px;
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius);
    padding: 6px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
  }

  .ms-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 20px;
    border-radius: var(--radius-sm);
    border: none;
    background: transparent;
    font-family: var(--font);
    font-size: 13px; font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
  }

  .ms-tab-btn:hover { background: var(--bg-gray-50); color: var(--text-primary); }

  .ms-tab-btn.active {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(3,128,71,.25);
  }

  .ms-tab-btn svg {
    width: 15px; height: 15px;
    stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    flex-shrink: 0;
  }

  .ms-tab-panel { display: none; }
  .ms-tab-panel.active { display: block; }

  /* TREND TOGGLE GROUP */
  .ms-toggle-group {
    display: flex;
    background: var(--bg-gray-100);
    border-radius: var(--radius-xs);
    padding: 3px;
    gap: 2px;
    border: 1px solid var(--border-gray);
  }

  .ms-toggle-btn {
    display: flex; align-items: center; gap: 5px;
    padding: 6px 14px;
    border-radius: 6px;
    border: none;
    background: transparent;
    font-family: var(--font);
    font-size: 12px; font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    white-space: nowrap;
  }

  .ms-toggle-btn:hover { background: var(--bg-white); color: var(--text-primary); }

  .ms-toggle-btn.active {
    background: var(--bg-white);
    color: var(--primary-green);
    box-shadow: 0 1px 4px rgba(0,0,0,.08);
  }

  /* CSV BUTTON */
  .ms-csv-btn {
    display: flex; align-items: center; gap: 5px;
    padding: 6px 14px;
    background: var(--bg-gray-100);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius-xs);
    font-family: var(--font);
    font-size: 12px; font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    white-space: nowrap;
  }

  .ms-csv-btn:hover {
    background: var(--primary-green);
    border-color: var(--primary-green);
    color: #fff;
  }

  /* CSV MODAL */
  .ms-csv-modal {
    position: fixed; inset: 0; z-index: 99998;
    background: rgba(0,0,0,.5); backdrop-filter: blur(6px);
    display: none; align-items: center; justify-content: center;
  }

  .ms-csv-modal.show { display: flex; }

  .ms-csv-modal__box {
    background: #fff;
    border-radius: var(--radius);
    box-shadow: 0 24px 48px rgba(0,0,0,.25);
    width: 540px; max-width: 92vw;
    overflow: hidden;
    animation: dpUp .25s ease-out;
  }

  .ms-csv-modal__head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    background: var(--bg-gray-50);
    border-bottom: 1px solid var(--border-gray);
  }

  .ms-csv-modal__title {
    font-size: 15px; font-weight: 700; color: var(--text-primary);
  }

  .ms-csv-modal__close {
    width: 28px; height: 28px;
    border-radius: var(--radius-xs); border: none;
    background: transparent; font-size: 20px;
    color: var(--text-secondary); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: var(--transition);
  }

  .ms-csv-modal__close:hover { background: #fee2e2; color: #991b1b; }

  .ms-csv-modal__body {
    padding: 16px 20px;
  }

  .ms-csv-modal__pre {
    background: var(--bg-gray-50);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius-xs);
    padding: 14px 16px;
    font-family: 'Courier New', monospace;
    font-size: 13px; color: var(--text-primary);
    line-height: 1.7;
    max-height: 280px; overflow-y: auto;
    white-space: pre;
  }

  .ms-csv-modal__foot {
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    padding: 14px 20px;
    border-top: 1px solid var(--border-gray);
    background: var(--bg-gray-50);
  }

  .ms-csv-copy-btn {
    display: flex; align-items: center; gap: 6px;
    padding: 9px 20px;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    color: #fff; border: none; border-radius: var(--radius-xs);
    font-family: var(--font); font-size: 13px; font-weight: 700;
    cursor: pointer; transition: var(--transition);
  }

  .ms-csv-copy-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(3,128,71,.3); }
  .ms-csv-copy-btn.copied { background: #059669; }

  /* SKELETON */
  .loading-skeleton {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
    border-radius: var(--radius-xs);
  }

  @keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .skel-overlay {
    position: absolute;
    inset: 0;
    z-index: 3;
    border-radius: inherit;
  }

  /* EMPTY STATE */
  .do-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 44px 20px;
    gap: 10px;
  }

  .do-empty svg { width: 40px; height: 40px; stroke: var(--border-gray); fill: none; stroke-width: 1.5; }
  .do-empty-text { font-size: 13px; font-weight: 600; color: var(--text-secondary); }

  /* MENTION POPUP */
  @keyframes msPopIn {
    from { opacity: 0; transform: translateY(14px) scale(.94); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }

  #msPopup {
    position: fixed;
    z-index: 99999;
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius);
    box-shadow: var(--shadow-xl);
    width: 480px; height: 600px;
    display: none;
    flex-direction: column;
    overflow: hidden;
    font-family: var(--font);
    animation: msPopIn .22s cubic-bezier(.34,1.3,.64,1);
    user-select: none;
  }

  #msPopup.visible { display: flex; }

  .msp-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: var(--bg-gray-50);
    border-bottom: 1px solid var(--border-gray);
    cursor: grab;
    flex-shrink: 0;
  }

  .msp-header:active { cursor: grabbing; }

  .msp-drag-handle { display: flex; flex-direction: column; gap: 3px; margin-right: 4px; flex-shrink: 0; opacity: .4; }
  .msp-drag-handle span { display: block; width: 18px; height: 2px; background: var(--text-secondary); border-radius: 1px; }

  .msp-dot   { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
  .msp-title { font-size: 13px; font-weight: 700; color: var(--text-primary); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  .msp-count {
    background: var(--primary-green);
    color: #fff;
    border-radius: 10px;
    padding: 1px 9px;
    font-size: 11px; font-weight: 800;
    flex-shrink: 0;
  }

  .msp-close {
    width: 28px; height: 28px;
    border-radius: var(--radius-xs);
    border: none;
    background: transparent;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-secondary);
    font-size: 20px; line-height: 1;
    transition: var(--transition);
    flex-shrink: 0;
  }

  .msp-close:hover { background: #fee2e2; color: #991b1b; }

  .msp-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 13px;
    border-bottom: 1px solid var(--border-gray);
    background: #fafbfc;
    flex-shrink: 0;
  }

  .msp-meta {
    flex: 1;
    font-size: 10px; font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .5px;
    display: flex; align-items: center; gap: 8px;
    overflow: hidden;
  }

  .msp-meta__label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  .msp-export-btn {
    display: flex; align-items: center; gap: 5px;
    padding: 5px 11px;
    background: var(--primary-green);
    color: #fff;
    border: none;
    border-radius: var(--radius-xs);
    font-family: var(--font);
    font-size: 10px; font-weight: 700;
    cursor: pointer;
    transition: var(--transition);
    white-space: nowrap;
  }

  .msp-export-btn:hover { background: var(--primary-green-dark); transform: translateY(-1px); }
  .msp-export-btn svg { width: 11px; height: 11px; stroke: #fff; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

  .msp-list { overflow-y: auto; flex: 1; padding: 4px 0; min-height: 0; }
  .msp-list::-webkit-scrollbar { width: 5px; }
  .msp-list::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 4px; }
  .msp-list::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

  .msp-item {
    display: flex; gap: 10px;
    padding: 10px 14px;
    border-bottom: 1px solid var(--border-light);
    transition: background .1s;
    cursor: pointer;
    align-items: flex-start;
  }

  .msp-item:last-child { border-bottom: none; }
  .msp-item:hover { background: #f0fdf4; }

  .msp-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    color: #fff; font-weight: 700; font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    border: 1.5px solid var(--border-gray);
    overflow: hidden;
  }

  .msp-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
  .msp-avatar.av-fallback::after {
    content: attr(data-ini);
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff;
  }
  .msp-avatar.av-fallback { position: relative; }

  .msp-item-body { flex: 1; min-width: 0; }

  .msp-item-author { font-size: 12px; font-weight: 700; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .msp-item-handle { font-size: 10px; color: var(--text-muted); font-weight: 500; margin-bottom: 3px; }

  .msp-item-text {
    font-size: 12px; color: var(--text-secondary); line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 5px;
  }

  .msp-item-footer { display: flex; align-items: center; gap: 6px; font-size: 10px; color: var(--text-muted); flex-wrap: wrap; }

  .msp-sent { padding: 1px 7px; border-radius: 10px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
  .msp-sent--pos { background: #d1fae5; color: #065f46; }
  .msp-sent--neg { background: #fee2e2; color: #991b1b; }
  .msp-sent--neu { background: var(--bg-gray-100); color: var(--text-secondary); }

  .msp-loading {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    height: 100%; gap: 14px;
    color: var(--text-secondary); font-size: 13px; font-weight: 600;
  }

  .msp-spinner {
    width: 32px; height: 32px;
    border: 3px solid var(--border-gray);
    border-top-color: var(--primary-green);
    border-radius: 50%;
    animation: msSpin .7s linear infinite;
  }

  @keyframes msSpin { to { transform: rotate(360deg); } }

  /* DETAIL PANEL */
  @keyframes msDetailIn {
    from { transform: translateX(100%); }
    to   { transform: translateX(0); }
  }

  #msDetailPanel {
    position: absolute; inset: 0;
    background: var(--bg-white);
    z-index: 10;
    display: none;
    flex-direction: column;
    animation: msDetailIn .22s cubic-bezier(.4,0,.2,1);
  }

  #msDetailPanel.visible { display: flex; }

  .msdp-header {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px;
    background: var(--bg-gray-50);
    border-bottom: 1px solid var(--border-gray);
    flex-shrink: 0;
  }

  .msdp-back {
    width: 30px; height: 30px;
    border-radius: var(--radius-xs);
    border: 1px solid var(--border-gray);
    background: var(--bg-white);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-secondary);
    transition: var(--transition);
    flex-shrink: 0;
  }

  .msdp-back:hover { background: var(--primary-green-light); color: var(--primary-green); border-color: var(--primary-green-border); }
  .msdp-back svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

  .msdp-title { font-size: 13px; font-weight: 700; color: var(--text-primary); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

  .msdp-close {
    width: 28px; height: 28px;
    border-radius: var(--radius-xs);
    border: none; background: transparent;
    cursor: pointer; font-size: 20px;
    color: var(--text-secondary);
    display: flex; align-items: center; justify-content: center;
    transition: var(--transition);
  }

  .msdp-close:hover { background: #fee2e2; color: #991b1b; }

  .msdp-body { overflow-y: auto; flex: 1; padding: 16px; }
  .msdp-body::-webkit-scrollbar { width: 5px; }
  .msdp-body::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 4px; }

  .msdp-avatar-row { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }

  .msdp-avatar-lg {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    color: #fff; font-weight: 700; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid var(--border-gray);
    overflow: hidden; flex-shrink: 0;
  }

  .msdp-avatar-lg img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

  .msdp-author-name   { font-size: 15px; font-weight: 700; color: var(--text-primary); }
  .msdp-author-handle { font-size: 11px; color: var(--text-muted); font-weight: 500; }

  .msdp-media-wrap   { border-radius: 10px; overflow: hidden; margin-bottom: 12px; background: #000; }
  .msdp-media-img    { width: 100%; max-height: 240px; object-fit: cover; display: block; }

  .msdp-content-text {
    font-size: 13px; color: var(--text-secondary); line-height: 1.7;
    margin-bottom: 12px;
    background: var(--bg-gray-50);
    border-radius: 10px; padding: 12px 14px;
    border: 1px solid var(--border-gray);
    word-break: break-word;
  }

  .msdp-meta-row {
    display: flex; align-items: center; justify-content: space-between;
    font-size: 11px; color: var(--text-muted); font-weight: 500;
    margin-bottom: 12px;
  }

  .msdp-sent-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 700;
    margin-bottom: 12px;
  }

  .msdp-sent-badge--pos { background: #d1fae5; color: #065f46; }
  .msdp-sent-badge--neg { background: #fee2e2; color: #991b1b; }
  .msdp-sent-badge--neu { background: var(--bg-gray-100); color: var(--text-secondary); }

  .msdp-stats-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-bottom: 12px; }

  .msdp-stat-box {
    background: var(--bg-gray-50); border-radius: 10px;
    padding: 10px 12px; border: 1px solid var(--border-gray); text-align: center;
  }

  .msdp-stat-val { font-size: 16px; font-weight: 700; color: var(--text-primary); }
  .msdp-stat-lbl { font-size: 9px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .4px; margin-top: 2px; }

  .msdp-link-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 14px;
    background: var(--primary-green); color: #fff;
    border-radius: 10px; font-size: 12px; font-weight: 700;
    text-decoration: none;
    transition: var(--transition);
    width: 100%; margin-top: 4px;
  }

  .msdp-link-btn:hover { background: var(--primary-green-dark); }
  .msdp-link-btn svg { width: 13px; height: 13px; stroke: #fff; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

  /* PLATFORM PICKER */
  @keyframes msPlatIn {
    from { opacity: 0; transform: scale(.9) translateY(8px); }
    to   { opacity: 1; transform: none; }
  }

  #msPlatPicker {
    position: fixed;
    z-index: 999999;
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: var(--radius-sm);
    box-shadow: var(--shadow-lg);
    padding: 6px;
    min-width: 180px;
    font-family: var(--font);
    animation: msPlatIn .15s ease-out;
    display: none;
  }

  #msPlatPicker.visible { display: block; }

  .mspp-header {
    padding: 5px 10px 8px;
    font-size: 10px; font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid var(--border-light);
    margin-bottom: 4px;
  }

  .mspp-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 11px;
    border-radius: var(--radius-xs);
    font-size: 12px; font-weight: 600;
    cursor: pointer;
    background: transparent; border: none;
    font-family: var(--font);
    width: 100%; text-align: left;
    color: var(--text-secondary);
    transition: var(--transition);
  }

  .mspp-btn:hover { background: var(--primary-green-light); color: var(--primary-green); }

  .mspp-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; margin-left: auto; }

  /* RESPONSIVE */
  @media (max-width: 1280px) {
    .ms-stat-grid     { grid-template-columns: repeat(2,1fr); }
    .ms-platform-grid { grid-template-columns: repeat(3,1fr); }
    .ms-grid-3-2,
    .ms-grid-2-3      { grid-template-columns: 1fr; }
    .ms-grid-2        { grid-template-columns: 1fr; }
  }

  @media (max-width: 768px) {
    .ms-page            { padding: 16px; }
    .ms-stat-grid       { grid-template-columns: 1fr; }
    .ms-platform-grid   { grid-template-columns: repeat(2,1fr); }
    #msPopup            { width: 93vw; }
    .ms-tab-btn         { font-size: 12px; padding: 10px 12px; }
    .date-picker-container { flex-direction: column; width: 96%; }
    .date-picker-sidebar  { width: 100%; flex-direction: row; overflow-x: auto; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: var(--radius) var(--radius) 0 0; flex-shrink: 0; }
    .date-preset          { white-space: nowrap; }
    .calendars-wrapper    { flex-direction: column; }
  }
</style>
@endsection

@section('content')
@php
  $projectId = request()->get('project_id');
  $startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
  $endDate   = request()->get('end_date', now()->format('Y-m-d'));
  $projects  = $projects ?? [];
@endphp

<div class="ms-page">

  {{-- PAGE HEADER --}}
  <div class="page-header">
    <div class="page-header-left">
      <h1>Media Statistic</h1>
      <p>Distribusi mention, share of voice, trend harian, dan pola waktu posting per platform</p>
    </div>
    <button class="ms-refresh-btn" onclick="MSPage.reload()">
      <svg viewBox="0 0 24 24">
        <polyline points="23 4 23 10 17 10"/>
        <polyline points="1 20 1 14 7 14"/>
        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
      </svg>
      Refresh
    </button>
  </div>

  {{-- FILTER CARD --}}
  <div class="filter-card">
    <form id="msFilterForm" method="GET">
      <input type="hidden" name="project_id" id="hPid" value="{{ $projectId }}">
      <input type="hidden" name="start_date"  id="hSD"  value="{{ $startDate }}">
      <input type="hidden" name="end_date"    id="hED"  value="{{ $endDate }}">

      <div class="filter-content">
        @if(count($projects))
        <div class="filter-group">
          <label class="filter-label">Project</label>
          <select class="filter-select" id="msProjSel"
            onchange="document.getElementById('hPid').value=this.value">
            @foreach($projects as $p)
            <option value="{{ $p['id'] }}" {{ ($p['id'] == $projectId) ? 'selected' : '' }}>
              {{ $p['name'] ?? $p['title'] ?? 'Project #' . $p['id'] }}
            </option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="filter-group">
          <label class="filter-label">Periode</label>
          <button type="button" class="date-picker-trigger" id="msDpTrigger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span id="msDpDisplay">{{ $startDate }} – {{ $endDate }}</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
        </div>

        <div class="filter-group" style="margin-left:auto;">
          <label class="filter-label" style="opacity:0;pointer-events:none;">‎</label>
          <button type="submit" class="apply-btn">
            <svg viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/>
              <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Filter
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- DATE PICKER MODAL --}}
  <div class="date-picker-modal" id="msDpModal" aria-modal="true" role="dialog">
    <div class="date-picker-overlay" onclick="MSDp.close()"></div>
    <div class="date-picker-container">
      <div class="date-picker-sidebar">
        <button class="date-preset" data-p="today">Today</button>
        <button class="date-preset" data-p="yesterday">Yesterday</button>
        <button class="date-preset" data-p="last7">Last 7 Days</button>
        <button class="date-preset" data-p="last30">Last 30 Days</button>
        <button class="date-preset" data-p="thismonth">This Month</button>
        <button class="date-preset" data-p="lastmonth">Last Month</button>
        <button class="date-preset active" data-p="custom">Custom Range</button>
      </div>
      <div class="date-picker-content">
        <div class="date-picker-header">
          <button class="nav-btn" onclick="MSDp.nav(-1)" aria-label="Previous month">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <div class="calendars-wrapper">
            <div class="calendar" id="msDpCal1"></div>
            <div class="calendar" id="msDpCal2"></div>
          </div>
          <button class="nav-btn" onclick="MSDp.nav(1)" aria-label="Next month">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
        <div class="date-picker-display">
          <span id="msDpRangeText">{{ $startDate }} – {{ $endDate }}</span>
        </div>
        <div class="date-picker-footer">
          <button class="cancel-btn" onclick="MSDp.close()">Batal</button>
          <button class="apply-date-btn" onclick="MSDp.apply()">Terapkan</button>
        </div>
      </div>
    </div>
  </div>

  {{-- SECTION 1 — DISTRIBUTION OF MENTIONS --}}
  <div class="ms-section-header">
    <div class="ms-section-icon">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    </div>
    <span class="ms-section-title">Distribution of Mentions by Media</span>
    <div class="ms-section-line"></div>
  </div>

  {{-- Stat Cards --}}
  <div class="ms-stat-grid">
    <div class="ms-stat-card ms-stat-card--mass ms-stat-card--clickable"
         onclick="MSPopup.open('doc', event.clientX, event.clientY)"
         title="Klik untuk lihat Online News mentions">
      <div class="ms-stat-label">
        <span class="ms-stat-dot" style="background:#0284c7;"></span>
        Mass Media
      </div>
      <div class="ms-stat-value" id="valMass">
        <div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div>
      </div>
      <div class="ms-stat-sub">Online News / Article</div>
      <div class="ms-stat-hint">
        <svg viewBox="0 0 24 24"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
        Klik untuk lihat detail
      </div>
    </div>

    <div class="ms-stat-card ms-stat-card--social ms-stat-card--clickable"
         onclick="MSPopup.showPlatPicker(event.clientX, event.clientY)"
         title="Klik untuk lihat Social Media mentions">
      <div class="ms-stat-label">
        <span class="ms-stat-dot" style="background:var(--primary-green);"></span>
        Social Media
      </div>
      <div class="ms-stat-value" id="valSocial">
        <div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div>
      </div>
      <div class="ms-stat-sub">Semua platform sosial</div>
      <div class="ms-stat-hint">
        <svg viewBox="0 0 24 24"><path d="M15 3h6v6"/><path d="M10 14L21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
        Klik untuk lihat detail
      </div>
    </div>

    <div class="ms-stat-card ms-stat-card--total">
      <div class="ms-stat-label">Total Mentions</div>
      <div class="ms-stat-value" id="valTotal">
        <div class="loading-skeleton" style="height:36px;width:120px;border-radius:6px;"></div>
      </div>
      <div class="ms-stat-sub">Mass Media + Social Media</div>
    </div>

    <div class="ms-stat-card ms-stat-card--period">
      <div class="ms-stat-label">Periode Data</div>
      <div class="ms-stat-value" style="font-size:16px;font-weight:700;color:var(--text-secondary);">
        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
      </div>
      <div class="ms-stat-sub">s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
    </div>
  </div>

  {{-- Platform Mini Cards --}}
  <div class="ms-platform-grid">
    <div class="ms-plat-card" style="cursor:pointer;" onclick="MSPopup.open('doc', event.clientX, event.clientY)" title="Klik untuk lihat Online News">
      <div class="ms-plat-card__icon" style="background:rgba(2,132,199,.1);">
        <svg viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
          <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
          <line x1="10" y1="6" x2="16" y2="6"/><line x1="10" y1="10" x2="16" y2="10"/><line x1="10" y1="14" x2="16" y2="14"/>
        </svg>
      </div>
      <div>
        <div class="ms-plat-card__name">Mass Media</div>
        <div class="ms-plat-card__count" id="pcDoc">
          <div class="loading-skeleton" style="height:22px;width:55px;border-radius:5px;"></div>
        </div>
      </div>
    </div>

    <div class="ms-plat-card" style="cursor:pointer;" onclick="MSPopup.open('twit', event.clientX, event.clientY)" title="Klik untuk lihat Twitter">
      <div class="ms-plat-card__icon" style="background:rgba(29,155,240,.1);">
        <svg viewBox="0 0 24 24" fill="#1d9bf0" stroke="none" style="width:20px;height:20px;">
          <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
      </div>
      <div>
        <div class="ms-plat-card__name">X / Twitter</div>
        <div class="ms-plat-card__count" id="pcTwit">
          <div class="loading-skeleton" style="height:22px;width:55px;border-radius:5px;"></div>
        </div>
      </div>
    </div>

    <div class="ms-plat-card" style="cursor:pointer;" onclick="MSPopup.open('fb', event.clientX, event.clientY)" title="Klik untuk lihat Facebook">
      <div class="ms-plat-card__icon" style="background:rgba(24,119,242,.1);">
        <svg viewBox="0 0 24 24" fill="none" stroke="#1877f2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
          <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
        </svg>
      </div>
      <div>
        <div class="ms-plat-card__name">Facebook</div>
        <div class="ms-plat-card__count" id="pcFb">
          <div class="loading-skeleton" style="height:22px;width:55px;border-radius:5px;"></div>
        </div>
      </div>
    </div>

    <div class="ms-plat-card" style="cursor:pointer;" onclick="MSPopup.open('ig', event.clientX, event.clientY)" title="Klik untuk lihat Instagram">
      <div class="ms-plat-card__icon" style="background:rgba(225,48,108,.1);">
        <svg viewBox="0 0 24 24" fill="none" stroke="#e1306c" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
          <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
          <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
          <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
        </svg>
      </div>
      <div>
        <div class="ms-plat-card__name">Instagram</div>
        <div class="ms-plat-card__count" id="pcIg">
          <div class="loading-skeleton" style="height:22px;width:55px;border-radius:5px;"></div>
        </div>
      </div>
    </div>

    <div class="ms-plat-card" style="cursor:pointer;" onclick="MSPopup.open('yt', event.clientX, event.clientY)" title="Klik untuk lihat YouTube">
      <div class="ms-plat-card__icon" style="background:rgba(255,0,0,.08);">
        <svg viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
          <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/>
          <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>
        </svg>
      </div>
      <div>
        <div class="ms-plat-card__name">YouTube</div>
        <div class="ms-plat-card__count" id="pcYt">
          <div class="loading-skeleton" style="height:22px;width:55px;border-radius:5px;"></div>
        </div>
      </div>
    </div>

    <div class="ms-plat-card" style="cursor:pointer;" onclick="MSPopup.open('tiktok', event.clientX, event.clientY)" title="Klik untuk lihat TikTok">
      <div class="ms-plat-card__icon" style="background:rgba(17,24,39,.07);">
        <svg viewBox="0 0 24 24" fill="#111827" stroke="none" style="width:20px;height:20px;">
          <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/>
        </svg>
      </div>
      <div>
        <div class="ms-plat-card__name">TikTok</div>
        <div class="ms-plat-card__count" id="pcTt">
          <div class="loading-skeleton" style="height:22px;width:55px;border-radius:5px;"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Bar Chart + SOV Mass vs Social --}}
  <div class="ms-grid-3-2">
    <div class="do-card">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </span>
          <div>
            <div class="do-card-title">Total Mention by Media Platform</div>
            <div class="do-card-subtitle">Klik bar untuk lihat detail mentions per platform</div>
          </div>
        </div>
        <span class="do-badge">All Platforms</span>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-300" id="chBarWrap">
          <div id="chBar" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="skBar"></div>
        </div>
      </div>
    </div>

    <div class="do-card">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          </span>
          <div>
            <div class="do-card-title">Share of Voice</div>
            <div class="do-card-subtitle">Mass vs Social — klik untuk detail</div>
          </div>
        </div>
        <span class="do-badge">2 Categories</span>
      </div>
      <div class="sov-card-body" id="sovMassBody">
        <div style="position:relative;height:280px;width:100%;">
          <div id="chSovMass" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton" style="position:absolute;inset:0;border-radius:8px;" id="skSovMass"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- SOV by Platform + Bar Race --}}
  <div class="ms-grid-2-3 ms-mb20">
    <div class="do-card">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </span>
          <div>
            <div class="do-card-title">Share of Voice</div>
            <div class="do-card-subtitle">Breakdown per platform</div>
          </div>
        </div>
        <span class="do-badge">By Platform</span>
      </div>
      <div class="sov-card-body" id="sovPlatBody">
        <div style="position:relative;height:340px;width:100%;">
          <div id="chSovPlat" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton" style="position:absolute;inset:0;border-radius:8px;" id="skSovPlat"></div>
        </div>
      </div>
    </div>

    {{-- ★ BAR RACE CARD ★ --}}
    <div class="do-card">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </span>
          <div>
            <div class="do-card-title">Mentions per Platform</div>
            <div class="do-card-subtitle">Volume & share — klik untuk lihat mentions</div>
          </div>
        </div>
        <span class="do-badge" style="background:#d1fae5;color:#065f46;">Bar Race</span>
      </div>
      <div class="do-card-body" style="padding:0;">
        <div style="position:relative;height:320px;">
          <div id="chBarRace" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="skBarRace"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- SECTION 2+3 — TAB: TREND | POLA WAKTU --}}
  <div class="ms-section-header">
    <div class="ms-section-icon">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <span class="ms-section-title">Media Type</span>
    <div class="ms-section-line"></div>
  </div>

  {{-- Tab Buttons --}}
  <div class="ms-tabs">
    <button class="ms-tab-btn active" id="tabBtnTrend" onclick="MSTab.show('trend')">
      <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Trend Mentions
    </button>
    <button class="ms-tab-btn" id="tabBtnPola" onclick="MSTab.show('pola')">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Pola Waktu Posting
    </button>
  </div>

  {{-- TAB PANEL: TREND MENTIONS --}}
  <div class="ms-tab-panel active" id="panelTrend">
    <div class="do-card ms-mb20">
      <div class="do-card-head">
        <div class="do-card-head-left">
          <span class="do-head-icon">
            <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </span>
          <div>
            <div class="do-card-title">The Trends of Total Mentions by Media Types</div>
            <div class="do-card-subtitle" id="trendSubtitle">8 hari terakhir dihitung mundur dari hari ini</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
        <div class="ms-toggle-group" id="trendToggle">
            <button class="ms-toggle-btn active" data-mode="daily" onclick="MSTrendToggle.set('daily')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              Harian
            </button>
            <button class="ms-toggle-btn" data-mode="monthly" onclick="MSTrendToggle.set('monthly')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              Bulanan
            </button>
          </div>
          <div class="ms-toggle-group" id="weekNavGroup" style="display:flex;">
            <button class="ms-toggle-btn" id="weekNavPrev" onclick="MSTrendToggle.navWeek(1)" title="Minggu sebelumnya">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <span id="weekNavLabel" style="padding:6px 10px;font-size:11px;font-weight:700;color:var(--text-secondary);white-space:nowrap;line-height:1;display:flex;align-items:center;">Minggu Ini</span>
            <button class="ms-toggle-btn" id="weekNavNext" onclick="MSTrendToggle.navWeek(-1)" title="Minggu berikutnya" disabled style="opacity:.35;cursor:not-allowed;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
          <button class="ms-csv-btn" onclick="MSTrendToggle.copyCSV()" title="Copy CSV Data">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Copy CSV
          </button>
          <span class="do-badge" id="trendBadge">Loading…</span>
        </div>
      </div>
      <div class="do-card-body">
        <div class="ms-ch-340" id="chTrendWrap">
          <div id="chTrend" style="width:100%;height:100%;"></div>
          <div class="loading-skeleton skel-overlay" id="skTrend"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="do-card ms-mb20">
    <div class="do-card-head">
      <div class="do-card-head-left">
        <span class="do-head-icon">
          <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="10" y1="6" x2="16" y2="6"/><line x1="10" y1="10" x2="16" y2="10"/><line x1="10" y1="14" x2="16" y2="14"/></svg>
        </span>
        <div>
          <div class="do-card-title">The Trends of Total Articles by Media Types</div>
          <div class="do-card-subtitle">Artikel Online News per hari</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
          <button class="ms-csv-btn" onclick="MSCsvModal.showArticleTrend()" title="Copy CSV Data">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Copy CSV
          </button>
          <span class="do-badge" id="articleTrendBadge">Loading…</span>
        </div>
    </div>
    <div class="do-card-body">
      <div class="ms-ch-340" id="chArticleTrendWrap">
        <div id="chArticleTrend" style="width:100%;height:100%;"></div>
        <div class="loading-skeleton skel-overlay" id="skArticleTrend"></div>
      </div>
    </div>
  </div>

  {{-- TAB PANEL: POLA WAKTU --}}
  <div class="ms-tab-panel" id="panelPola">
    <div class="ms-grid-2">
      <div class="do-card">
        <div class="do-card-head">
          <div class="do-card-head-left">
            <span class="do-head-icon">
              <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <div>
              <div class="do-card-title">Mentions by Weekday</div>
              <div class="do-card-subtitle">Total mention per hari dalam seminggu</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            <button class="ms-csv-btn" onclick="MSCsvModal.showWeekday()" title="Copy CSV Data">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
              CSV
            </button>
            <span class="do-badge">7 Hari</span>
          </div>
        </div>
        <div class="do-card-body">
          <div class="ms-ch-280">
            <div id="chWeekday" style="width:100%;height:100%;"></div>
            <div class="loading-skeleton skel-overlay" id="skWeekday"></div>
          </div>
        </div>
      </div>

      <div class="do-card">
        <div class="do-card-head">
          <div class="do-card-head-left">
            <span class="do-head-icon">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <div>
              <div class="do-card-title">Mentions by Hour</div>
              <div class="do-card-subtitle">Distribusi volume mention per jam (00–23)</div>
            </div>
          </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            <button class="ms-csv-btn" onclick="MSCsvModal.showHour()" title="Copy CSV Data">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
              CSV
            </button>
            <span class="do-badge">24 Jam</span>
          </div>
        </div>{{-- closes do-card-head --}}
        <div class="do-card-body">
          <div class="ms-ch-280">
            <div id="chHour" style="width:100%;height:100%;"></div>
            <div class="loading-skeleton skel-overlay" id="skHour"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>{{-- /ms-page --}}

{{-- MENTION POPUP --}}
<div id="msPopup">
  <div class="msp-header" id="msPopHeader">
    <div class="msp-drag-handle"><span></span><span></span><span></span></div>
    <div class="msp-dot" id="msPopDot"></div>
    <span class="msp-title" id="msPopTitle">Mentions</span>
    <span class="msp-count" id="msPopCount">…</span>
    <button class="msp-close" onclick="MSPopup.close()">×</button>
  </div>
  <div class="msp-actions">
    <div class="msp-meta">
      <svg viewBox="0 0 24 24" style="width:11px;height:11px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0;">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
      <span class="msp-meta__label" id="msPopMeta">—</span>
    </div>
    <button class="msp-export-btn" onclick="MSPopup.exportCsv()">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Export CSV
    </button>
  </div>
  <div class="msp-list" id="msPopList"></div>
  <div id="msDetailPanel">
    <div class="msdp-header">
      <button class="msdp-back" onclick="MSDetail.close()">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="msdp-title" id="msDpTitle">Detail</span>
      <button class="msdp-close" onclick="MSPopup.close()">×</button>
    </div>
    <div class="msdp-body" id="msDpBody"></div>
  </div>
</div>

{{-- Platform Picker --}}
<div id="msPlatPicker">
  <div class="mspp-header">Pilih Platform</div>
  <button class="mspp-btn" onclick="MSPopup.openPlatform('twit')">X / Twitter <span class="mspp-dot" style="background:#0ea5e9;"></span></button>
  <button class="mspp-btn" onclick="MSPopup.openPlatform('fb')">Facebook <span class="mspp-dot" style="background:#1877f2;"></span></button>
  <button class="mspp-btn" onclick="MSPopup.openPlatform('ig')">Instagram <span class="mspp-dot" style="background:#e1306c;"></span></button>
  <button class="mspp-btn" onclick="MSPopup.openPlatform('yt')">YouTube <span class="mspp-dot" style="background:#ff0000;"></span></button>
  <button class="mspp-btn" onclick="MSPopup.openPlatform('tiktok')">TikTok <span class="mspp-dot" style="background:#111827;"></span></button>
</div>

{{-- CSV Modal --}}
<div class="ms-csv-modal" id="msCsvModal">
  <div style="position:absolute;inset:0;" onclick="MSCsvModal.close()"></div>
  <div class="ms-csv-modal__box">
    <div class="ms-csv-modal__head">
      <span class="ms-csv-modal__title">CSV Data</span>
      <button class="ms-csv-modal__close" onclick="MSCsvModal.close()">×</button>
    </div>
    <div class="ms-csv-modal__body">
      <pre class="ms-csv-modal__pre" id="msCsvContent"></pre>
    </div>
    <div class="ms-csv-modal__foot">
      <button class="ms-csv-copy-btn" id="msCsvCopyBtn" onclick="MSCsvModal.copy()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        Copy CSV data
      </button>
      <button onclick="MSCsvModal.close()" style="padding:9px 20px;background:var(--bg-gray-100);border:1px solid var(--border-gray);border-radius:var(--radius-xs);font-family:var(--font);font-size:13px;font-weight:600;color:var(--text-secondary);cursor:pointer;">
        ✕ Close
      </button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
const MSCfg = {
  pid:  {{ $projectId ? (int)$projectId : 'null' }},
  sd:   '{{ $startDate }}',
  ed:   '{{ $endDate }}',
  platColors: {
    'Mass Media':  '#0284c7',
    'X (Twitter)': '#1d9bf0',
    'Facebook':    '#1877f2',
    'Instagram':   '#e1306c',
    'YouTube':     '#ff0000',
    'TikTok':      '#111827',
    doc:       '#0284c7',
    twit:      '#1d9bf0',
    twitter:   '#1d9bf0',
    fb:        '#1877f2',
    facebook:  '#1877f2',
    ig:        '#e1306c',
    instagram: '#e1306c',
    yt:        '#ff0000',
    youtube:   '#ff0000',
    tiktok:    '#111827',
    tt:        '#111827',
  },
  platMeta: {
    doc:    { label: 'Online News',  color: '#0284c7' },
    twit:   { label: 'X / Twitter', color: '#0ea5e9' },
    fb:     { label: 'Facebook',    color: '#1877f2' },
    ig:     { label: 'Instagram',   color: '#e1306c' },
    yt:     { label: 'YouTube',     color: '#ff0000' },
    tiktok: { label: 'TikTok',      color: '#111827' },
  }
};

/* ══════════════════════════════════════════════════════
   UTILS
══════════════════════════════════════════════════════ */
const numFmt    = n => parseInt(n || 0).toLocaleString('id-ID');
const numK      = n => { n = parseInt(n || 0); return n >= 1e6 ? (n/1e6).toFixed(1)+'M' : n >= 1000 ? (n/1000).toFixed(1)+'k' : String(n); };

// Dual Y-axis: Twitter → left axis (0), all others → right axis (1)
const Y_AXIS_IDX = { doc: 1, twitter: 0, facebook: 1, instagram: 1, youtube: 1, tiktok: 1 };
const esc       = s => (s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const hideSk    = id => { const e = document.getElementById(id); if (e) e.style.display = 'none'; };
const showSk    = id => { const e = document.getElementById(id); if (e) e.style.display = ''; };
const emptyHtml = msg => `<div class="do-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="do-empty-text">${msg}</span></div>`;
const labelToKey = { 'Mass Media':'doc', 'X (Twitter)':'twit', 'Facebook':'fb', 'Instagram':'ig', 'YouTube':'yt', 'TikTok':'tiktok' };

/* ══════════════════════════════════════════════════════
   ECHARTS INSTANCES REGISTRY
══════════════════════════════════════════════════════ */
const MSCharts = {
  _instances: {},
  make(id, theme) {
    if (this._instances[id]) { try { this._instances[id].dispose(); } catch(e) {} }
    const dom = document.getElementById(id);
    if (!dom) return null;
    const chart = echarts.init(dom, theme || null, { renderer: 'canvas' });
    this._instances[id] = chart;
    return chart;
  },
  disposeAll() {
    Object.values(this._instances).forEach(c => { try { c.dispose(); } catch(e) {} });
    this._instances = {};
  }
};

window.addEventListener('resize', () => {
  Object.values(MSCharts._instances).forEach(c => {
    try { if (!c.isDisposed()) c.resize(); } catch(e) {}
  });
});

const EC_TOOLTIP = {
  backgroundColor: '#1a202c',
  borderColor: '#e2e8f0',
  borderWidth: 1,
  padding: [10, 14],
  textStyle: { color: '#ffffff', fontFamily: "'Poppins', sans-serif", fontSize: 13 },
  extraCssText: 'border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.3);'
};

/* ══════════════════════════════════════════════════════
   TAB SYSTEM
══════════════════════════════════════════════════════ */
const MSTab = {
  _loaded: { trend: false, pola: false },

  show(tab) {
    document.querySelectorAll('.ms-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tabBtn' + tab.charAt(0).toUpperCase() + tab.slice(1))?.classList.add('active');
    document.querySelectorAll('.ms-tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel' + tab.charAt(0).toUpperCase() + tab.slice(1))?.classList.add('active');

    if (tab === 'trend' && !this._loaded.trend) {
      this._loaded.trend = true;
      loadTrend();
      loadArticleTrend();
    }
    if (tab === 'pola') {
      if (!this._loaded.pola) { this._loaded.pola = true; loadWeekHour(); }
      else {
        setTimeout(() => {
          ['chWeekday','chHour'].forEach(id => {
            const c = MSCharts._instances[id];
            try { if (c && !c.isDisposed()) c.resize(); } catch(e) {}
          });
        }, 60);
      }
    }
    if (tab === 'trend') {
      setTimeout(() => {
        ['chTrend','chArticleTrend'].forEach(id => {
          const c = MSCharts._instances[id];
          try { if (c && !c.isDisposed()) c.resize(); } catch(e) {}
        });
      }, 60);
    }
  },

  reset() { this._loaded = { trend: false, pola: false }; }
};

/* ══════════════════════════════════════════════════════
   DATE PICKER
══════════════════════════════════════════════════════ */
const MSDp = (() => {
  let ds = null, de = null, m1 = new Date(), m2 = new Date(), pickStart = true;
  const MN = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const WD = ['Su','Mo','Tu','We','Th','Fr','Sa'];

  function init() {
    const si = document.getElementById('hSD'), ei = document.getElementById('hED');
    ds = si?.value ? new Date(si.value) : (() => { const d = new Date(); d.setDate(d.getDate()-6); return d; })();
    de = ei?.value ? new Date(ei.value) : new Date();
    m1 = new Date(ds); m2 = new Date(ds); m2.setMonth(m2.getMonth()+1);
    render();
    document.getElementById('msDpTrigger').addEventListener('click', open);
    document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', onPreset));
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && document.getElementById('msDpModal').classList.contains('show')) close(); });
  }

  function open()  { document.getElementById('msDpModal').classList.add('show'); render(); }
  function close() { document.getElementById('msDpModal').classList.remove('show'); }

  function apply() {
    document.getElementById('hSD').value = fmt(ds);
    document.getElementById('hED').value = fmt(de);
    document.getElementById('msDpDisplay').textContent = fmt(ds) + ' – ' + fmt(de);
    close();
    document.getElementById('msFilterForm').submit();
  }

  function nav(dir) { m1.setMonth(m1.getMonth()+dir); m2.setMonth(m2.getMonth()+dir); render(); }

function onPreset(e){
    document.querySelectorAll('.do-dp-preset').forEach(b=>b.classList.remove('active'));
    e.target.classList.add('active');
    const today=new Date();today.setHours(0,0,0,0);
    switch(e.target.dataset.p){
        case 'today':    ds=new Date(today);de=new Date(today);break;
        case 'yesterday':ds=new Date(today);ds.setDate(today.getDate()-1);de=new Date(ds);break;
        case 'last7':    de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-6);break;
        case 'last30':   de=new Date(today);ds=new Date(today);ds.setDate(today.getDate()-29);break;
        case 'thismonth':ds=new Date(today.getFullYear(),today.getMonth(),1);de=new Date(today);break;
        case 'lastmonth':ds=new Date(today.getFullYear(),today.getMonth()-1,1);de=new Date(today.getFullYear(),today.getMonth(),0);break;
    }
    if(e.target.dataset.p!=='custom'){
        m1=new Date(ds);m2=new Date(ds);m2.setMonth(m2.getMonth()+1);
        // Auto apply & submit untuk preset (bukan custom)
        apply();
    } else {
        updDisp();render();
    }
}

  function render() { renderCal('msDpCal1', m1); renderCal('msDpCal2', m2); updDisp(); }

  function renderCal(id, month) {
    const el = document.getElementById(id); if (!el) return;
    const y = month.getFullYear(), mn = month.getMonth();
    const first = new Date(y, mn, 1), last = new Date(y, mn+1, 0), prevL = new Date(y, mn, 0);
    const today = new Date(); today.setHours(0,0,0,0);
    let h = `<div class="calendar-month">${MN[mn]} ${y}</div>
      <div class="calendar-weekdays">${WD.map(d=>`<div class="weekday">${d}</div>`).join('')}</div>
      <div class="calendar-days">`;
    for (let i = 0; i < first.getDay(); i++)
      h += `<button type="button" class="calendar-day other-month" disabled>${prevL.getDate()-(first.getDay()-1-i)}</button>`;
    for (let d = 1; d <= last.getDate(); d++) {
      const date = new Date(y, mn, d); date.setHours(0,0,0,0);
      let cls = 'calendar-day';
      if (sameD(date, today)) cls += ' today';
      if (date > today) cls += ' disabled';
      if (ds && de) {
        if (sameD(date, ds) || sameD(date, de)) cls += ' selected';
        else if (date > ds && date < de) cls += ' in-range';
      }
      h += `<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
    }
    const rem = last.getDay() === 6 ? 0 : 6 - last.getDay();
    for (let i = 1; i <= rem; i++) h += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    h += '</div>';
    el.innerHTML = h;
    el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => {
      btn.addEventListener('click', function () {
        const d = new Date(this.dataset.date); d.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        document.querySelector('[data-p="custom"]').classList.add('active');
        if (pickStart || d < ds) { ds = d; de = d; pickStart = false; }
        else { if (d >= ds) de = d; else { de = ds; ds = d; } pickStart = true; }
        updDisp(); render();
      });
    });
  }

  function updDisp() {
    const el = document.getElementById('msDpRangeText');
    if (el && ds && de) el.textContent = fmt(ds) + ' – ' + fmt(de);
  }

  function fmt(d) {
    if (!d) return '';
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  }

  function sameD(a, b) {
    return a && b && a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();
  }

  return { init, open, close, apply, nav };
})();

/* ══════════════════════════════════════════════════════
   ECHARTS — DOUGHNUT
══════════════════════════════════════════════════════ */
// SESUDAH — style mirip engagement share
function makeEDoughnut(domId, labels, values, colors, onClickFns, subtitles) {
  const total = values.reduce((a, b) => a + b, 0);
  const chart = MSCharts.make(domId);
  if (!chart) return null;

  const seriesData = labels.map((label, i) => ({
    name:      label,
    value:     values[i],
    subtitle:  subtitles ? subtitles[i] : '',
    itemStyle: { color: colors[i], borderColor: '#fff', borderWidth: 3 },
  }));

  chart.setOption({
    animation: true, animationDuration: 800, animationEasing: 'cubicOut',
    backgroundColor: 'transparent',
    tooltip: {
      trigger: 'item',
      backgroundColor: '#1e293b', borderColor: '#334155', borderWidth: 1,
      padding: [10, 14],
      textStyle: { color: '#f8fafc', fontFamily: "'Poppins', sans-serif", fontSize: 12 },
      extraCssText: 'border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.2);',
      formatter: params => {
        const pct = total > 0 ? ((params.value / total) * 100).toFixed(1) : '0.0';
        const sub = params.data.subtitle ? `<br><span style="color:#94a3b8;font-size:11px;">${params.data.subtitle}</span>` : '';
        return `<div style="font-weight:700;margin-bottom:5px;font-size:13px;">${params.name}${sub}</div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:4px;">
                  <span style="color:#94a3b8;">Mentions</span>
                  <span style="font-weight:700;">${numFmt(params.value)}</span>
                </div>
                <div style="display:flex;justify-content:space-between;gap:20px;margin-top:3px;">
                  <span style="color:#94a3b8;">Share</span>
                  <span style="font-weight:700;color:#4aab8c;">${pct}%</span>
                </div>`;
      }
    },
    legend: { show: false },
    series: [{
  type: 'pie',
  radius: ['46%', '64%'],
  center: ['50%', '52%'],
  avoidLabelOverlap: true,
  minAngle: 5,
  itemStyle: { borderRadius: 6 },
  label: {
    show: true,
    alignTo: 'edge',      // ← ganti dari 'labelLine' ke 'edge'
    edgeDistance: 10,     // ← jarak dari tepi chart
    lineHeight: 18,
    fontFamily: "'Poppins', sans-serif",
    fontSize: 11,
    color: '#374151',
    formatter: params => {
      // Hide label kalau slice terlalu kecil (< 2%)
      const pct = total > 0 ? (params.value / total * 100) : 0;
      if (pct < 2) return '';

      const name = params.name.length > 11 ? params.name.slice(0, 10) + '…' : params.name;
      const sub  = params.data.subtitle
        ? (params.data.subtitle.length > 11 ? params.data.subtitle.slice(0, 10) + '…' : params.data.subtitle)
        : '';
      return sub
        ? `{name|${name}}\n{sub|${sub}}\n{eng|${numK(params.value)}}`
        : `{name|${name}}\n{eng|${numK(params.value)}}`;
    },
    rich: {
      name: { fontWeight: '700', fontSize: 12,   color: '#1a202c', lineHeight: 20 },
      sub:  { fontWeight: '400', fontSize: 10.5, color: '#64748b', lineHeight: 17 },
      eng:  { fontWeight: '700', fontSize: 11,   color: '#038047', lineHeight: 17,
              backgroundColor: '#edf7f3', borderRadius: 4, padding: [1, 5] },
    },
  },
  labelLine: {
    show: true,
    length: 15,
    length2: 20,
    smooth: 0.4,
    minTurnAngle: 135,    // ← angle lebih besar biar ga bentrok
    lineStyle: { color: '#c4cdd8', width: 1.2, type: 'solid' },
    showAbove: false,
  },
  emphasis: {
    scale: true, scaleSize: 5,
    itemStyle: { shadowBlur: 10, shadowColor: 'rgba(0,0,0,.12)' },
  },
  data: seriesData,
}],
    graphic: [
      {
        type: 'text', left: 'center', top: '47%', z: 100,
        style: {
          text: numK(total),
          fill: '#0f172a',
          font: "800 26px 'Poppins', sans-serif",
          textAlign: 'center',
        },
      },
      {
        type: 'text', left: 'center', top: '55%', z: 100,
        style: {
          text: 'TOTAL',
          fill: '#94a3b8',
          font: "600 10px 'Poppins', sans-serif",
          textAlign: 'center',
          letterSpacing: 2,
        },
      },
    ],
  });

  if (onClickFns) {
    chart.on('click', params => {
      const fn = onClickFns[params.dataIndex];
      if (typeof fn === 'function') {
        const rect = chart.getDom().getBoundingClientRect();
        fn(rect.left + rect.width / 2, rect.top + rect.height / 2);
      }
    });
  }
  chart.on('mouseover', () => { if (onClickFns) chart.getDom().style.cursor = 'pointer'; });
  chart.on('mouseout',  () => { chart.getDom().style.cursor = 'default'; });

  return chart;
}
/* ══════════════════════════════════════════════════════
   LOAD: MENTION BY PLATFORM
══════════════════════════════════════════════════════ */
async function loadMentionByPlatform() {
  if (!MSCfg.pid) {
    ['valMass','valSocial','valTotal'].forEach(id => {
      const e = document.getElementById(id);
      if (e) e.innerHTML = '<span style="font-size:14px;color:#94a3b8;">—</span>';
    });
    ['skBar','skSovMass','skSovPlat','skBarRace'].forEach(hideSk);
    const platTableBody = document.getElementById('platTableBody');
    if (platTableBody) platTableBody.innerHTML = emptyHtml('No project selected');
    return;
  }

  try {
    const res = await fetch(`/mk/api/media-statistic/mention-by-platform?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`);
    const d   = await res.json();
    if (d.error) throw new Error(d.error);

    document.getElementById('valMass').textContent   = numFmt(d.mass_total   || 0);
    document.getElementById('valSocial').textContent = numFmt(d.social_total || 0);
    document.getElementById('valTotal').textContent  = numFmt(d.grand_total  || 0);

    const platforms = d.platforms || [];
    const pcMap = {
      doc:'pcDoc', twit:'pcTwit', twitter:'pcTwit',
      fb:'pcFb', facebook:'pcFb',
      ig:'pcIg', instagram:'pcIg',
      yt:'pcYt', youtube:'pcYt',
      tiktok:'pcTt'
    };

    platforms.forEach(p => {
      const key = labelToKey[p.label] || '';
      const elId = pcMap[key];
      if (elId) { const e = document.getElementById(elId); if (e) e.textContent = numFmt(p.count || 0); }
    });

    /* ── Bar Chart ─────────────────────────── */
    hideSk('skBar');
    if (platforms.length) {
      const bLabels = platforms.map(p => p.label);
      const bValues = platforms.map(p => p.count || 0);
      const bColors = platforms.map(p => MSCfg.platColors[p.label] || '#038047');

      const barChart = MSCharts.make('chBar');
      if (barChart) {
        barChart.setOption({
          animation: true, animationDuration: 800, animationEasing: 'elasticOut',
          tooltip: {
            ...EC_TOOLTIP, trigger: 'axis',
            axisPointer: { type: 'shadow', shadowStyle: { color: 'rgba(3,128,71,.06)' } },
            formatter: params => {
              const p = params[0];
              return `<div style="font-weight:700;font-size:13px;margin-bottom:4px;">${p.name}</div><div style="font-size:13px;">${numFmt(p.value)} mentions</div><div style="margin-top:6px;font-size:10px;color:#94a3b8;text-align:center;">Klik untuk lihat mentions</div>`;
            }
          },
          grid: { top: 16, right: 16, bottom: 36, left: 56, containLabel: false },
          xAxis: {
            type: 'category', data: bLabels,
            axisLine: { show: false }, axisTick: { show: false },
            axisLabel: { fontFamily: "'Poppins', sans-serif", fontSize: 12, color: '#64748b', interval: 0 }
          },
          yAxis: {
            type: 'value',
            axisLine: { show: false }, axisTick: { show: false },
            splitLine: { lineStyle: { color: '#f1f5f9', type: 'dashed' } },
            axisLabel: { fontFamily: "'Poppins', sans-serif", fontSize: 11, color: '#94a3b8', formatter: numK }
          },
          series: [{
            type: 'bar',
            data: bValues.map((v, i) => ({
              value: v,
              itemStyle: {
                color: { type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                  colorStops: [{ offset: 0, color: bColors[i] }, { offset: 1, color: bColors[i] + '55' }]
                },
                borderRadius: [8, 8, 0, 0],
              },
              emphasis: { itemStyle: { color: bColors[i], shadowBlur: 14, shadowColor: bColors[i] + '66' } }
            })),
            barMaxWidth: 56,
            label: {
              show: true, position: 'top',
              fontFamily: "'Poppins', sans-serif", fontWeight: '700', fontSize: 11, color: '#64748b',
              formatter: p => numK(p.value)
            }
          }]
        });

        barChart.on('click', params => {
          const k = labelToKey[bLabels[params.dataIndex]];
          if (k) {
            const rect = barChart.getDom().getBoundingClientRect();
            MSPopup.open(k, rect.left + rect.width / 2, rect.top + 100);
          }
        });
        barChart.on('mouseover', () => { barChart.getDom().style.cursor = 'pointer'; });
        barChart.on('mouseout',  () => { barChart.getDom().style.cursor = 'default'; });
      }
    } else {
      document.getElementById('chBar').parentElement.innerHTML = emptyHtml('Tidak ada data mention');
    }

    /* ── SOV Mass vs Social ───── */
    hideSk('skSovMass');
    makeEDoughnut('chSovMass',
  ['Mass Media', 'Social Media'],
  [d.mass_total || 0, d.social_total || 0],
  ['#0284c7', '#038047'],
  [(x,y) => MSPopup.open('doc', x, y), (x,y) => MSPopup.showPlatPicker(x, y)],
  null  // ← tambah ini
);

    /* ── SOV by Platform ──────── */
    hideSk('skSovPlat');
    const nz = platforms.filter(p => p.count > 0);
    const pList = nz.length ? nz : platforms;
    makeEDoughnut('chSovPlat',
  pList.map(p => p.label),
  pList.map(p => p.count || 0),
  pList.map(p => MSCfg.platColors[p.label] || '#038047'),
  pList.map(p => {
    const k = labelToKey[p.label];
    return k ? (x,y) => MSPopup.open(k, x, y) : null;
  }),
  pList.map(p => {                          // ← tambah subtitles
    const grandTotal = d.grand_total || 1;
    const pct = ((p.count || 0) / grandTotal * 100).toFixed(1);
    return pct + '%';
  })
);
    /* ══════════════════════════════════════════
       ★ BAR RACE CHART ★
    ══════════════════════════════════════════ */
    hideSk('skBarRace');

    if (platforms.length) {
      const grandTotal = d.grand_total || 1;

      // Sort ascending (terkecil di bawah) untuk tampilan awal
      const brData = platforms
        .map(p => ({ label: p.label, value: p.count || 0, color: MSCfg.platColors[p.label] || '#038047' }))
        .sort((a, b) => a.value - b.value);

      const brMax = Math.max(...brData.map(p => p.value), 1);

      const brChart = MSCharts.make('chBarRace');
      if (brChart) {

        const buildSeriesData = items => items.map(item => ({
          value: item.value,
          itemStyle: {
            color: {
              type: 'linear', x: 0, y: 0, x2: 1, y2: 0,
              colorStops: [
                { offset: 0,   color: item.color + '33' },
                { offset: 0.6, color: item.color + 'bb' },
                { offset: 1,   color: item.color },
              ]
            },
            borderRadius: [0, 10, 10, 0],
          },
          emphasis: {
            itemStyle: { shadowBlur: 20, shadowColor: item.color + '55' }
          }
        }));

        brChart.setOption({
          animation:              true,
          animationDuration:      1400,
          animationDurationUpdate: 1100,
          animationEasing:        'elasticOut',
          animationEasingUpdate:  'cubicInOut',
          backgroundColor: '#ffffff',
          tooltip: {
            backgroundColor: '#1a202c',
            borderColor: '#334155',
            borderWidth: 1,
            padding: [12, 16],
            textStyle: { color: '#ffffff', fontFamily: "'Poppins', sans-serif", fontSize: 13 },
            extraCssText: 'border-radius:12px;box-shadow:0 12px 32px rgba(0,0,0,.35);',
            trigger: 'axis',
            axisPointer: { type: 'shadow', shadowStyle: { color: 'rgba(3,128,71,.05)' } },
            formatter: params => {
              const p    = params[0];
              const item = brData.find(x => x.label === p.name) || {};
              const pct  = ((p.value / grandTotal) * 100).toFixed(1);
              const clr  = item.color || '#038047';
              return `<div style="font-weight:800;font-size:14px;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid rgba(255,255,255,.12);">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${clr};margin-right:7px;vertical-align:middle;"></span>${p.name}
                      </div>
                      <div style="display:flex;align-items:center;justify-content:space-between;gap:24px;margin-bottom:6px;">
                        <span style="font-size:12px;color:#94a3b8;">Mentions</span>
                        <span style="font-size:15px;font-weight:700;">${numFmt(p.value)}</span>
                      </div>
                      <div style="display:flex;align-items:center;justify-content:space-between;gap:24px;margin-bottom:6px;">
                        <span style="font-size:12px;color:#94a3b8;">Share of Voice</span>
                        <span style="font-size:13px;font-weight:700;color:#34d399;">${pct}%</span>
                      </div>
                      <div style="display:flex;align-items:center;justify-content:space-between;gap:24px;">
                        <span style="font-size:12px;color:#94a3b8;">Total</span>
                        <span style="font-size:12px;color:#64748b;">${numFmt(grandTotal)}</span>
                      </div>
                      <div style="margin-top:6px;font-size:10px;color:#94a3b8;text-align:center;">Klik untuk lihat mentions</div>`;
            }
          },
          grid: { top: 12, right: 110, bottom: 12, left: 16, containLabel: true },
          xAxis: {
            type: 'value',
            max: brMax * 1.15,
            axisLine: { show: false }, axisTick: { show: false },
            splitLine: { lineStyle: { color: '#f8fafc', type: 'solid' } },
            axisLabel: { show: false }
          },
          yAxis: {
            type: 'category',
            data: brData.map(p => p.label),
            inverse: false,
            animationDuration: 300,
            animationDurationUpdate: 1100,
            axisLine: { show: false }, axisTick: { show: false },
            axisLabel: {
              fontFamily: "'Poppins', sans-serif",
              fontSize: 12, fontWeight: '700', color: '#1a202c',
              margin: 14,
            }
          },
          series: [{
            realtimeSort: true,
            type: 'bar',
            data: buildSeriesData(brData),
            barMaxWidth: 44,
            label: {
              show: true,
              position: 'right',
              fontFamily: "'Poppins', sans-serif",
              fontWeight: '700',
              fontSize: 12,
              color: '#1a202c',
              formatter: p => {
                const pct = ((p.value / grandTotal) * 100).toFixed(1);
                return `{val|${numFmt(p.value)}}  {pct|${pct}%}`;
              },
              rich: {
                val: { fontSize: 12, fontWeight: '700', color: '#1a202c', fontFamily: "'Poppins', sans-serif" },
                pct: { fontSize: 10, fontWeight: '600', color: '#94a3b8', fontFamily: "'Poppins', sans-serif" },
              }
            },
          }]
        });

        // ── Animasi race: ascending → descending setelah 1.6 detik ──
        setTimeout(() => {
          const sorted = [...brData].sort((a, b) => b.value - a.value);
          brChart.setOption({
            yAxis:  { data: sorted.map(p => p.label) },
            series: [{ data: buildSeriesData(sorted) }]
          });
        }, 1600);

        // ── Events ──
        brChart.on('click', params => {
          const k = labelToKey[params.name];
          if (k) {
            const rect = brChart.getDom().getBoundingClientRect();
            MSPopup.open(k, rect.left + rect.width / 2, rect.top + 100);
          }
        });
        brChart.on('mouseover', () => { brChart.getDom().style.cursor = 'pointer'; });
        brChart.on('mouseout',  () => { brChart.getDom().style.cursor = 'default'; });
      }
    } else {
      const brDom = document.getElementById('chBarRace');
      if (brDom) brDom.parentElement.innerHTML = emptyHtml('Tidak ada data mention');
    }

  } catch (err) {
    console.error('loadMentionByPlatform error:', err);
    ['valMass','valSocial','valTotal'].forEach(id => {
      const e = document.getElementById(id);
      if (e) e.innerHTML = '<span style="font-size:13px;color:#dc2626;font-weight:600;">Error</span>';
    });
    ['skBar','skSovMass','skSovPlat','skBarRace'].forEach(hideSk);
    const brDom = document.getElementById('chBarRace');
    if (brDom) brDom.parentElement.innerHTML = emptyHtml('Gagal memuat data');
  }
}

/* ══════════════════════════════════════════════════════
   LOAD TREND
══════════════════════════════════════════════════════ */
async function loadTrend() {
  if (!MSCfg.pid) { hideSk('skTrend'); return; }

 // SESUDAH
const fmtDate = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
let trendSD, trendED;
if (MSTrendToggle._datePickerOverride) {
  trendSD = MSCfg.sd;
  trendED = MSCfg.ed;
} else {
  const now    = new Date();
  const off    = MSTrendToggle._weekOffset;
  const edDate = new Date(now); edDate.setDate(now.getDate() - (7 * off));
  const sdDate = new Date(now); sdDate.setDate(now.getDate() - (7 * (off + 1)));
  trendSD = fmtDate(sdDate);
  trendED = fmtDate(edDate);
}

  const platMeta = {
    doc:       { label: 'Online News (Ind)', color: '#038047' },
    twitter:   { label: 'Twitter',           color: '#00b4d8' }, // cyan biru terang — beda dari FB
    facebook:  { label: 'Facebook',          color: '#4361ee' }, // biru ungu — beda dari Twitter
    instagram: { label: 'Instagram',         color: '#f72585' }, // pink magenta
    youtube:   { label: 'YouTube',           color: '#e63946' }, // merah terang
    tiktok:    { label: 'TikTok',            color: '#7209b7' }, // ungu tua
  };
  const platOrder = ['doc', 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok'];

  try {
    const res  = await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${MSCfg.pid}&start_date=${trendSD}&end_date=${trendED}`);
    const json = await res.json();
    if (json.error) throw new Error(json.error);

    hideSk('skTrend');

    const raw = json.data || [];
    const dSet = new Set();
    raw.forEach(p => (p.data || []).forEach(d => dSet.add(d.date)));
    const allDates = Array.from(dSet).sort();

    // Simpan ke cache MSTrendToggle
    MSTrendToggle.setData(raw);

    // Kalau mode monthly, delegate ke MSTrendToggle
    if (MSTrendToggle._mode === 'monthly') {
      hideSk('skTrend');
      MSTrendToggle._render(raw);
      return;
    }

    const trendRaw = platOrder.map(key => {
      const found = raw.find(p => p.key === key);
      return found || { key, label: platMeta[key]?.label || key, color: platMeta[key]?.color || '#94a3b8', data: [] };
    });

    const fmtB = d => {
      const dt = new Date(d + 'T00:00:00');
      return `${dt.getDate()} ${dt.toLocaleString('id-ID', { month: 'short' })}`;
    };
    document.getElementById('trendBadge').textContent = `${fmtB(trendSD)} – ${fmtB(trendED)}`;

    // Update week nav UI
    // SESUDAH
const weekNavGroup = document.getElementById('weekNavGroup');
const weekNavLabel = document.getElementById('weekNavLabel');
const weekNavNext  = document.getElementById('weekNavNext');
if (weekNavGroup && MSTrendToggle._mode === 'daily' && !MSTrendToggle._datePickerOverride) {
  weekNavGroup.style.display = 'flex';
      if (weekNavLabel) weekNavLabel.textContent = MSTrendToggle._weekLabel();
      if (weekNavNext) {
        const isCurrentWeek = MSTrendToggle._weekOffset === 0;
        weekNavNext.disabled = isCurrentWeek;
        weekNavNext.style.opacity = isCurrentWeek ? '.35' : '1';
        weekNavNext.style.cursor  = isCurrentWeek ? 'not-allowed' : 'pointer';
      }
    } else if (weekNavGroup) {
      weekNavGroup.style.display = 'none'; // hide for monthly mode
    }

    const trendChart = MSCharts.make('chTrend');
    if (!trendChart) return;

    const series = trendRaw.map(p => {
      const vals    = allDates.map(d => { const pt = p.data.find(x => x.date === d); return pt ? pt.count : 0; });
      const hasData = vals.some(v => v > 0);
      return {
       name: p.label, type: 'line', yAxisIndex: Y_AXIS_IDX[p.key] ?? 1, data: vals, smooth: 0.4,
        symbol: 'circle',
        symbolSize: hasData && allDates.length <= 30 ? 6 : 0,
        showSymbol: allDates.length <= 30,
        itemStyle:  { color: p.color, borderColor: '#fff', borderWidth: 2 },
        lineStyle:  { color: p.color, width: hasData ? 2.5 : 1, opacity: hasData ? 1 : 0.15 },
        label: {
          show: hasData && allDates.length <= 14, position: 'top',
          formatter: params => params.value > 0 ? numFmt(params.value) : '',
          fontFamily: "'Poppins', sans-serif", fontWeight: '700', fontSize: 10, color: '#64748b',
        },
        emphasis: {
          focus: 'series', lineStyle: { width: 3.5 },
          itemStyle: { symbolSize: 10, borderColor: '#fff', borderWidth: 2.5, shadowBlur: 10, shadowColor: p.color + '88' }
        },
      };
    });

    const xLabels = allDates.map(d => {
      const dt = new Date(d + 'T00:00:00');
      return `${dt.getDate()}. ${dt.toLocaleString('id-ID', { month: 'short' })}`;
    });

    trendChart.setOption({
      animation: true, animationDuration: 900, animationEasing: 'cubicInOut',
      backgroundColor: '#ffffff',
     // SESUDAH
tooltip: {
  backgroundColor: '#1a202c',
  borderColor: '#334155',
  borderWidth: 1,
  padding: [12, 16],
  textStyle: { color: '#ffffff', fontFamily: "'Poppins', sans-serif", fontSize: 13 },
  extraCssText: 'border-radius:12px;box-shadow:0 12px 32px rgba(0,0,0,.35);',
  trigger: 'item',   // ← per series, bukan axis
  formatter: params => {
    if (params.componentType !== 'series') return '';
    const date    = allDates[params.dataIndex] || '';
    const fullDt  = date
      ? new Date(date + 'T00:00:00').toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' })
      : '';
    const color   = params.color;
    const name    = params.seriesName;
    const value   = params.value || 0;
    return `<div style="font-weight:800;font-size:14px;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid rgba(255,255,255,.12);">
              <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${color};margin-right:7px;vertical-align:middle;"></span>${name}
            </div>
            ${fullDt ? `<div style="font-size:11px;color:#94a3b8;margin-bottom:8px;">${fullDt}</div>` : ''}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:24px;">
              <span style="font-size:12px;color:#94a3b8;">Mentions</span>
              <span style="font-size:15px;font-weight:700;">${numFmt(value)}</span>
            </div>
            <div style="margin-top:6px;font-size:10px;color:#94a3b8;text-align:center;">Klik untuk lihat mentions</div>`;
  }
},
      legend: {
        bottom: 0, type: 'scroll',
        data: trendRaw.map(p => p.label),
        textStyle: { fontFamily:"'Poppins', sans-serif", fontSize:11, fontWeight:'600', color:'#64748b' },
        icon: 'circle', itemWidth:10, itemHeight:10, itemGap:20,
      },
     grid: { top:32, right:72, bottom:50, left:64 },
      xAxis: {
        type: 'category', data: xLabels, boundaryGap: false,
        axisLine: { lineStyle: { color:'#e2e8f0' } }, axisTick: { show:false },
        axisLabel: { fontFamily:"'Poppins', sans-serif", fontSize:11, fontWeight:'600', color:'#64748b' }
      },
      yAxis: [
        {
          // LEFT — Twitter scale
          type: 'value', position: 'left',
          name: 'Twitter', nameGap: 8,
          nameTextStyle: { color:'#1d9bf044', fontSize:10, fontWeight:'700', fontFamily:"'Poppins', sans-serif", align:'right' },
          axisLine: { show:true, lineStyle:{ color:'#1d9bf018', width:1 } },
          axisTick: { show:false },
          splitLine: { show:false },
          axisLabel: { fontFamily:"'Poppins', sans-serif", fontSize:10, color:'#1d9bf0aa', formatter:numK },
        },
        {
          // RIGHT — All others scale
          type: 'value', position: 'right',
          name: 'Others', nameGap: 8,
          nameTextStyle: { color:'#94a3b8', fontSize:10, fontWeight:'700', fontFamily:"'Poppins', sans-serif", align:'left' },
          axisLine: { show:true, lineStyle:{ color:'#e2e8f0', width:1 } },
          axisTick: { show:false },
          splitLine: { lineStyle:{ color:'#f1f5f9', type:'solid', width:1 } },
          axisLabel: { fontFamily:"'Poppins', sans-serif", fontSize:10, color:'#94a3b8', formatter:numK },
        },
      ],
      series,
    }, true);

    // ── Klik titik/dot → buka popup mention platform tsb ──
    trendChart.on('click', params => {
      if (params.componentType !== 'series') return;
      const seriesName = params.seriesName;
      const platEntry  = trendRaw.find(p => p.label === seriesName);
      if (!platEntry) return;
      // Map label → platform key untuk MSPopup
      const keyMap = {
        'Online News (Ind)': 'doc',
        'Twitter':           'twit',
        'Facebook':          'fb',
        'Instagram':         'ig',
        'YouTube':           'yt',
        'TikTok':            'tiktok',
      };
      const k = keyMap[seriesName];
      if (!k) return;
      const rect = trendChart.getDom().getBoundingClientRect();
      MSPopup.open(k, rect.left + params.event.offsetX, rect.top + params.event.offsetY);
    });
    trendChart.on('mouseover', params => {
      if (params.componentType === 'series') trendChart.getDom().style.cursor = 'pointer';
    });
    trendChart.on('mouseout', () => { trendChart.getDom().style.cursor = 'default'; });

  } catch (err) {
    hideSk('skTrend');
    console.warn('loadTrend error:', err);
    document.getElementById('trendBadge').textContent = 'Error';
    document.getElementById('chTrend').parentElement.innerHTML = emptyHtml('Data trend tidak tersedia');
  }
}

/* ══════════════════════════════════════════════════════
   LOAD ARTICLE TREND
══════════════════════════════════════════════════════ */
async function loadArticleTrend() {
  if (!MSCfg.pid) { hideSk('skArticleTrend'); return; }

  const fmtDate = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
const trendSD = MSCfg.sd;
const trendED = MSCfg.ed;

  const fmtB = d => {
    const dt = new Date(d + 'T00:00:00');
    return `${dt.getDate()} ${dt.toLocaleString('id-ID', { month: 'short' })}`;
  };

  try {
    const res  = await fetch(`/mk/api/media-statistic/trend-mentions?project_id=${MSCfg.pid}&start_date=${trendSD}&end_date=${trendED}`);
    const json = await res.json();
    if (json.error) throw new Error(json.error);

    hideSk('skArticleTrend');

    const raw     = json.data || [];
    const docData = raw.find(p => p.key === 'doc');

    if (!docData || !docData.data?.length) {
      document.getElementById('articleTrendBadge').textContent = 'No Data';
      document.getElementById('chArticleTrend').parentElement.innerHTML = emptyHtml('Data artikel tidak tersedia untuk periode ini');
      return;
    }

    document.getElementById('articleTrendBadge').textContent = `${fmtB(trendSD)} – ${fmtB(trendED)}`;

    const dates   = docData.data.map(d => d.date);
    const values  = docData.data.map(d => d.count);

    // Cache untuk CSV export
    MSCsvModal.setArticleData(dates, values);

    const xLabels = dates.map(d => {
      const dt = new Date(d + 'T00:00:00');
      return `${dt.getDate()}. ${dt.toLocaleString('id-ID', { month: 'short' })}`;
    });

    const artChart = MSCharts.make('chArticleTrend');
    if (!artChart) return;

    artChart.setOption({
      animation: true, animationDuration: 900, animationEasing: 'cubicInOut',
      backgroundColor: '#ffffff',
      tooltip: {
        backgroundColor: '#ffffff', borderColor: '#e2e8f0', borderWidth: 1,
        padding: [12, 16],
        textStyle: { color: '#1a202c', fontFamily: "'Poppins', sans-serif", fontSize: 12 },
        extraCssText: 'border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.12);',
        trigger: 'axis',
        axisPointer: { type: 'line', lineStyle: { color: '#e2e8f0', type: 'dashed', width: 1.5 } },
        formatter: params => {
          const p      = params[0];
          const fullDt = new Date(dates[p.dataIndex] + 'T00:00:00')
            .toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
          return `<div style="font-weight:700;font-size:12px;color:#1a202c;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #f1f5f9;">${fullDt}</div>
                  <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;">
                    <div style="display:flex;align-items:center;gap:7px;">
                      <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:#038047;flex-shrink:0;"></span>
                      <span style="font-size:12px;color:#64748b;">Online News (Ind)</span>
                    </div>
                    <span style="font-size:12px;font-weight:700;color:#1a202c;">${numFmt(p.value)}</span>
                  </div>
                  <div style="margin-top:6px;font-size:10px;color:#94a3b8;text-align:center;">Klik untuk lihat mentions</div>`;
        }
      },
      legend: {
        bottom: 0,
        data: ['Online News (Ind)'],
        textStyle: { fontFamily:"'Poppins', sans-serif", fontSize:11, fontWeight:'600', color:'#64748b' },
        icon: 'circle', itemWidth:10, itemHeight:10,
      },
      grid: { top:24, right:20, bottom:50, left:60 },
      xAxis: {
        type: 'category', data: xLabels, boundaryGap: false,
        axisLine: { lineStyle: { color:'#e2e8f0' } }, axisTick: { show:false },
        axisLabel: { fontFamily:"'Poppins', sans-serif", fontSize:11, fontWeight:'600', color:'#64748b' }
      },
      yAxis: {
        type: 'value', axisLine: { show:false }, axisTick: { show:false },
        splitLine: { lineStyle: { color:'#f1f5f9', type:'solid', width:1 } },
        axisLabel: { fontFamily:"'Poppins', sans-serif", fontSize:11, color:'#94a3b8', formatter:numK }
      },
      series: [{
        name: 'Online News (Ind)',
        type: 'line', data: values, smooth: 0.4,
        symbol: 'circle',
        symbolSize: xLabels.length <= 30 ? 7 : 0,
        showSymbol: xLabels.length <= 30,
        itemStyle: { color: '#038047', borderColor: '#fff', borderWidth: 2 },
        lineStyle: { color: '#038047', width: 2.5 },
        areaStyle: {
          color: {
            type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
            colorStops: [
              { offset: 0, color: 'rgba(3,128,71,0.2)' },
              { offset: 1, color: 'rgba(3,128,71,0.02)' },
            ]
          }
        },
        label: {
          show: xLabels.length <= 14, position: 'top',
          formatter: params => params.value > 0 ? numFmt(params.value) : '',
          fontFamily: "'Poppins', sans-serif", fontWeight: '700', fontSize: 10, color: '#64748b',
        },
        emphasis: {
          focus: 'series',
          lineStyle: { width: 3.5 },
          itemStyle: { symbolSize: 12, borderColor: '#fff', borderWidth: 2.5, shadowBlur: 12, shadowColor: '#03804788' }
        },
      }]
    });

    // ── Klik titik → buka popup Online News ──
    artChart.on('click', params => {
      if (params.componentType !== 'series') return;
      const rect = artChart.getDom().getBoundingClientRect();
      MSPopup.open('doc', rect.left + params.event.offsetX, rect.top + params.event.offsetY);
    });
    artChart.on('mouseover', params => {
      if (params.componentType === 'series') artChart.getDom().style.cursor = 'pointer';
    });
    artChart.on('mouseout', () => { artChart.getDom().style.cursor = 'default'; });

  } catch (err) {
    hideSk('skArticleTrend');
    console.warn('loadArticleTrend error:', err);
    document.getElementById('articleTrendBadge').textContent = 'Error';
    document.getElementById('chArticleTrend').parentElement.innerHTML = emptyHtml('Data artikel tidak tersedia');
  }
}

/* ══════════════════════════════════════════════════════
   LOAD WEEKDAY & HOUR
══════════════════════════════════════════════════════ */
async function loadWeekHour() {
  if (!MSCfg.pid) {
    ['skWeekday','skHour'].forEach(hideSk);
    document.getElementById('chWeekday').parentElement.innerHTML = emptyHtml('No project selected');
    document.getElementById('chHour').parentElement.innerHTML    = emptyHtml('No project selected');
    return;
  }

  const [wdRes, hrRes] = await Promise.allSettled([
    fetch(`/mk/api/media-statistic/mentions-by-weekday?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`).then(r => r.json()),
    fetch(`/mk/api/media-statistic/mentions-by-hour?project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}`).then(r => r.json()),
  ]);

  // ── WEEKDAY ──
  try {
    if (wdRes.status === 'rejected') throw wdRes.reason;
    const json      = wdRes.value;
    const wdNames   = json.weekdays  || ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
    const wdTotal   = json.total     || Array(7).fill(0);
    const platItems = json.platforms || [];
    hideSk('skWeekday');

    // Cache untuk CSV export
    MSCsvModal.setWeekdayData(wdNames, platItems);

    if (!wdTotal.some(v => v > 0)) {
      document.getElementById('chWeekday').parentElement.innerHTML = emptyHtml('Data weekday tidak tersedia untuk periode ini');
    } else {
      const wdChart = MSCharts.make('chWeekday');
      if (wdChart) {
        const series = platItems.map((plat, pi) => ({
          name: plat.label, type: 'bar', stack: 'total',
          data: plat.data.map((v, di) => {
            let isTop = v > 0;
            if (isTop) { for (let si = pi+1; si < platItems.length; si++) { if (platItems[si].data[di] > 0) { isTop = false; break; } } }
            return { value: v, itemStyle: { color: plat.color, borderRadius: isTop ? [5,5,0,0] : [0,0,0,0] } };
          }),
          emphasis: { focus: 'series' },
        }));
        if (series.length > 0) {
          series[series.length-1].label = {
            show: true, position: 'top',
            fontFamily: "'Poppins', sans-serif", fontWeight: '700', fontSize: 11, color: '#64748b',
            formatter: p => wdTotal[p.dataIndex] > 0 ? numK(wdTotal[p.dataIndex]) : '',
          };
        }
        wdChart.setOption({
          animation: true, animationDuration: 800, animationEasing: 'elasticOut',
          tooltip: {
            ...EC_TOOLTIP, trigger: 'axis',
            axisPointer: { type: 'shadow', shadowStyle: { color: 'rgba(3,128,71,.06)' } },
            formatter: params => {
              const day   = params[0]?.axisValue || '';
              const total = params.reduce((s, p) => s + (p.value||0), 0);
              const rows  = [...params].sort((a,b) => b.value-a.value).filter(p => p.value > 0).map(p =>
                `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
                  <div style="display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0;"></span>
                    <span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span>
                  </div>
                  <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
                </div>`).join('');
              return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">${day}</div>
                ${rows || '<div style="color:#64748b;font-size:12px;">Tidak ada data</div>'}
                <div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;gap:16px;">
                  <span style="font-size:11px;color:#94a3b8;">Total</span>
                  <span style="font-size:13px;font-weight:700;">${numFmt(total)}</span>
                </div>
                <div style="margin-top:5px;font-size:10px;color:#94a3b8;text-align:center;">Klik untuk lihat mentions</div>`;
            }
          },
          legend: {
            bottom: 0, data: platItems.map(p => p.label),
            textStyle: { fontFamily:"'Poppins', sans-serif", fontSize:11, fontWeight:'600', color:'#64748b' },
            icon: 'circle', itemWidth: 9, itemHeight: 9, itemGap: 14,
          },
          grid: { top:24, right:16, bottom:60, left:56 },
          xAxis: {
            type: 'category', data: wdNames,
            axisLine:{show:false}, axisTick:{show:false},
            axisLabel: { fontFamily:"'Poppins', sans-serif", fontSize:12, fontWeight:'600', color:'#64748b' }
          },
          yAxis: {
            type:'value', axisLine:{show:false}, axisTick:{show:false},
            splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
            axisLabel:{fontFamily:"'Poppins', sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
          },
          series,
        });

        // ── Klik bar weekday → platform picker ──
        wdChart.on('click', params => {
          if (params.componentType !== 'series') return;
          const rect = wdChart.getDom().getBoundingClientRect();
          // kalau klik series tertentu, langsung buka platform itu; kalau klik stack, buka picker
          const seriesName = params.seriesName;
          const platEntry  = platItems.find(p => p.label === seriesName);
          if (platEntry) {
            const labelToKeyLocal = {
              'Online News (Ind)': 'doc', 'Twitter':'twit', 'Facebook':'fb',
              'Instagram':'ig', 'YouTube':'yt', 'TikTok':'tiktok',
              'Online News':'doc',
            };
            const k = labelToKeyLocal[seriesName];
            if (k) MSPopup.open(k, rect.left + params.event.offsetX, rect.top + params.event.offsetY);
            else MSPopup.showPlatPicker(rect.left + params.event.offsetX, rect.top + params.event.offsetY);
          } else {
            MSPopup.showPlatPicker(rect.left + params.event.offsetX, rect.top + params.event.offsetY);
          }
        });
        wdChart.on('mouseover', params => {
          if (params.componentType === 'series') wdChart.getDom().style.cursor = 'pointer';
        });
        wdChart.on('mouseout', () => { wdChart.getDom().style.cursor = 'default'; });
      }
    }
  } catch(err) {
    hideSk('skWeekday');
    document.getElementById('chWeekday').parentElement.innerHTML = emptyHtml('Data tidak tersedia');
  }

  // ── HOUR ──
  try {
    if (hrRes.status === 'rejected') throw hrRes.reason;
    const json      = hrRes.value;
    const hrLabels  = json.hours     || Array.from({length:24}, (_,i) => String(i).padStart(2,'0') + ':00');
    const hrTotal   = json.total     || Array(24).fill(0);
    const platItems = json.platforms || [];
    hideSk('skHour');

    // Cache untuk CSV export
    MSCsvModal.setHourData(hrLabels, platItems);

    if (!hrTotal.some(v => v > 0)) {
      document.getElementById('chHour').parentElement.innerHTML = emptyHtml('Data per jam tidak tersedia untuk periode ini');
    } else {
      const hrChart = MSCharts.make('chHour');
      if (hrChart) {
        const series = platItems.map((plat, pi) => ({
          name: plat.label, type: 'bar', stack: 'total',
          data: plat.data.map((v, di) => {
            let isTop = v > 0;
            if (isTop) { for (let si = pi+1; si < platItems.length; si++) { if (platItems[si].data[di] > 0) { isTop = false; break; } } }
            return { value: v, itemStyle: { color: plat.color, borderRadius: isTop ? [4,4,0,0] : [0,0,0,0] } };
          }),
          emphasis: { focus: 'series' },
        }));
        hrChart.setOption({
          animation: true, animationDuration: 800, animationEasing: 'elasticOut',
          tooltip: {
            ...EC_TOOLTIP, trigger: 'axis',
            axisPointer: { type: 'shadow', shadowStyle: { color: 'rgba(3,128,71,.06)' } },
            formatter: params => {
              const hour  = params[0]?.axisValue || '';
              const total = params.reduce((s, p) => s + (p.value||0), 0);
              const rows  = [...params].sort((a,b) => b.value-a.value).filter(p => p.value > 0).map(p =>
                `<div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:2px 0;">
                  <div style="display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0;"></span>
                    <span style="font-size:12px;color:#94a3b8;">${p.seriesName}</span>
                  </div>
                  <span style="font-size:12px;font-weight:700;">${numFmt(p.value)}</span>
                </div>`).join('');
              return `<div style="font-weight:700;font-size:13px;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid rgba(255,255,255,.1);">Jam ${hour}</div>
                ${rows || '<div style="color:#64748b;font-size:12px;">Tidak ada data</div>'}
                <div style="border-top:1px solid rgba(255,255,255,.1);margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;gap:16px;">
                  <span style="font-size:11px;color:#94a3b8;">Total</span>
                  <span style="font-size:13px;font-weight:700;">${numFmt(total)}</span>
                </div>
                <div style="margin-top:5px;font-size:10px;color:#94a3b8;text-align:center;">Klik untuk lihat mentions</div>`;
            }
          },
          legend: {
            bottom: 0, data: platItems.map(p => p.label),
            textStyle: { fontFamily:"'Poppins', sans-serif", fontSize:11, fontWeight:'600', color:'#64748b' },
            icon: 'circle', itemWidth: 9, itemHeight: 9, itemGap: 14,
          },
          grid: { top:24, right:16, bottom:60, left:56 },
          xAxis: {
            type: 'category', data: hrLabels,
            axisLine:{show:false}, axisTick:{show:false},
            axisLabel: { fontFamily:"'Poppins', sans-serif", fontSize:10, fontWeight:'600', color:'#64748b', interval: 1, rotate: 45 }
          },
          yAxis: {
            type:'value', axisLine:{show:false}, axisTick:{show:false},
            splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},
            axisLabel:{fontFamily:"'Poppins', sans-serif",fontSize:11,color:'#94a3b8',formatter:numK}
          },
          series,
        });

        // ── Klik bar hour → platform picker ──
        hrChart.on('click', params => {
          if (params.componentType !== 'series') return;
          const rect       = hrChart.getDom().getBoundingClientRect();
          const seriesName = params.seriesName;
          const labelToKeyLocal = {
            'Online News (Ind)': 'doc', 'Twitter':'twit', 'Facebook':'fb',
            'Instagram':'ig', 'YouTube':'yt', 'TikTok':'tiktok',
            'Online News':'doc',
          };
          const k = labelToKeyLocal[seriesName];
          if (k) MSPopup.open(k, rect.left + params.event.offsetX, rect.top + params.event.offsetY);
          else MSPopup.showPlatPicker(rect.left + params.event.offsetX, rect.top + params.event.offsetY);
        });
        hrChart.on('mouseover', params => {
          if (params.componentType === 'series') hrChart.getDom().style.cursor = 'pointer';
        });
        hrChart.on('mouseout', () => { hrChart.getDom().style.cursor = 'default'; });
      }
    }
  } catch(err) {
    hideSk('skHour');
    document.getElementById('chHour').parentElement.innerHTML = emptyHtml('Data tidak tersedia');
  }
}

/* ══════════════════════════════════════════════════════
   MENTION POPUP
══════════════════════════════════════════════════════ */
const MSPopup = {
  _drag: false, _ox: 0, _oy: 0,
  _cache: {}, _items: [], _curPlat: null,

  init() {
    const popup  = document.getElementById('msPopup');
    const header = document.getElementById('msPopHeader');
    header.addEventListener('mousedown', e => {
      this._drag = true;
      const r = popup.getBoundingClientRect();
      this._ox = e.clientX - r.left;
      this._oy = e.clientY - r.top;
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
      const pp = document.getElementById('msPlatPicker');
      if (pp?.classList.contains('visible') && !pp.contains(e.target)) pp.classList.remove('visible');
    });
  },

  _pos(popup, x, y) {
    const pw = 480, ph = 600, vw = window.innerWidth, vh = window.innerHeight;
    let left = x + 18, top = y - 40;
    if (left + pw > vw - 12) left = x - pw - 18;
    if (top  + ph > vh - 12) top  = vh - ph - 12;
    if (top < 8) top = 8;
    if (left < 8) left = 8;
    popup.style.left = left + 'px';
    popup.style.top  = top  + 'px';
  },

  showPlatPicker(x, y) {
    const pp = document.getElementById('msPlatPicker'); if (!pp) return;
    const pw = 185, ph = 240, vw = window.innerWidth, vh = window.innerHeight;
    let left = x + 10, top = y - 10;
    if (left + pw > vw - 8) left = x - pw - 10;
    if (top  + ph > vh - 8) top  = vh - ph - 8;
    if (top < 8) top = 8;
    pp.style.left = left + 'px';
    pp.style.top  = top  + 'px';
    pp.classList.add('visible');
  },

  openPlatform(platform) {
    const pp = document.getElementById('msPlatPicker');
    const x  = pp ? parseFloat(pp.style.left) + 90 : window.innerWidth / 2;
    const y  = pp ? parseFloat(pp.style.top) + 20  : window.innerHeight / 2;
    if (pp) pp.classList.remove('visible');
    this.open(platform, x, y);
  },

  async open(platform, x, y) {
    const popup = document.getElementById('msPopup');
    const meta  = MSCfg.platMeta[platform] || { label: platform, color: '#038047' };
    this._curPlat = platform;
    MSDetail.close();
    document.getElementById('msPopDot').style.background  = meta.color;
    document.getElementById('msPopTitle').textContent     = meta.label;
    document.getElementById('msPopMeta').textContent      = MSCfg.sd + ' – ' + MSCfg.ed;
    document.getElementById('msPopCount').textContent     = '…';
    const list = document.getElementById('msPopList');
    list.innerHTML = `<div class="msp-loading"><div class="msp-spinner"></div>Memuat mentions…</div>`;
    popup.classList.add('visible');
    this._pos(popup, x, y);
    const key = `${MSCfg.pid}_${platform}_${MSCfg.sd}_${MSCfg.ed}`;
    try {
      if (!this._cache[key]) this._cache[key] = await this._fetch(platform);
      this._items = this._cache[key];
      document.getElementById('msPopCount').textContent = this._items.length.toLocaleString();
      this._render(list, this._items, platform, meta.color);
    } catch (err) {
      list.innerHTML = `<div class="msp-loading" style="color:#94a3b8;">
        <svg style="width:32px;height:32px;stroke:#e2e8f0;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        Gagal memuat data
      </div>`;
      document.getElementById('msPopCount').textContent = '0';
    }
  },

  close() {
    const popup = document.getElementById('msPopup');
    if (popup) popup.classList.remove('visible');
    MSDetail.close();
  },

  exportCsv() {
    if (!this._items?.length) { alert('Tidak ada data untuk diekspor.'); return; }
    const lbl  = { doc:'Online_News', twit:'Twitter', fb:'Facebook', ig:'Instagram', yt:'YouTube', tiktok:'TikTok' };
    // Format: index;field1;field2;... (semicolon-separated dengan index di depan)
    const rows = this._items.map((item, idx) => {
      const name    = (item.author_name||item.channel_name||item.publisher||item.source_name||item.name||item.author_scr_name||item.screen_name||'').trim();
      const handle  = (item.author_scr_name||item.screen_name||item.username||'').trim();
      const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,500);
      const sentRaw = String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
      const sent    = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif' ? 'Positif' : sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'||sentRaw==='negatif' ? 'Negatif' : 'Netral';
      const url     = item.url||item.link||'';
      const date    = item.date_created||item.created_at||'';
      const likes   = item.num_likes||item.likes||item.favorite_count||item.digg_count||0;
      const shares  = item.num_retweeted||item.shares||item.retweet_count||item.share_count||0;
      const views   = item.num_views||item.views||item.play_count||0;
      // escape semicolons in fields
      const esc2 = s => String(s||'').replace(/;/g,',').replace(/\n/g,' ').replace(/\r/g,'');
      return `${idx};${esc2(name)};${esc2(handle)};${esc2(sent)};${esc2(parseInt(likes)||0)};${esc2(parseInt(shares)||0)};${esc2(parseInt(views)||0)};${esc2(date.split('T')[0])};${esc2(url)};${esc2(content)}`;
    });
    const header = 'index;nama;handle;sentimen;likes;shares;views;tanggal;url;konten';
    const csv    = [header, ...rows].join('\r\n');
    const blob   = new Blob(['\uFEFF'+csv], { type: 'text/csv;charset=utf-8;' });
    const url    = URL.createObjectURL(blob);
    const a      = document.createElement('a');
    a.href = url; a.download = `${lbl[this._curPlat]||this._curPlat}_${MSCfg.sd}_${MSCfg.ed}.csv`;
    a.click(); URL.revokeObjectURL(url);
  },

  async _fetch(platform) {
    const q   = `project_id=${MSCfg.pid}&start_date=${MSCfg.sd}&end_date=${MSCfg.ed}&rows=500&start=0`;

    // Untuk IG: coba beberapa sub sampai dapat data
    if (platform === 'ig') {
      const subs = ['postbylike', 'postbycomment', 'postbydate', ''];
      for (const sub of subs) {
        const url  = `/mk/api/news/ig-top-status?${q}${sub ? '&sub='+sub : ''}`;
        try {
          const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 15000);
          const res  = await fetch(url, { signal: ctrl.signal }); clearTimeout(tid);
          if (!res.ok) continue;
          const data  = await res.json();
          const items = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
          if (items.length > 0) return items;
        } catch(e) { continue; }
      }
      return [];
    }

    const eps = {
      doc:    `/mk/api/news/mentions?${q}`,
      twit:   `/mk/api/x/most-status?${q}&media=all&mention_type=view_all`,
      fb:     `/mk/api/news/fb-top-status?${q}&sub=fblike`,
      yt:     `/mk/api/news/ytb-top-status?${q}`,
      tiktok: `/mk/api/news/tiktok-top-status?${q}&sub=postbylike`,
    };
    const url  = eps[platform]; if (!url) throw new Error('Platform tidak dikenali');
    const ctrl = new AbortController(), tid = setTimeout(() => ctrl.abort(), 30000);
    const res  = await fetch(url, { signal: ctrl.signal }); clearTimeout(tid);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    let items  = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);

    // Debug: log keys dari item pertama untuk FB/IG supaya bisa identify field yang benar
    if ((platform === 'fb' || platform === 'ig') && items.length > 0) {
      console.debug(`[MSPopup] ${platform} item keys:`, Object.keys(items[0]));
      console.debug(`[MSPopup] ${platform} item sample:`, items[0]);
    }

    if (platform === 'doc') {
      items = items.filter(m => {
        const tc = String(m.tcode || '').toLowerCase();
        const mt = String(m.media_type || '').toLowerCase();
        return tc === 'berita' || mt === 'berita' || mt === 'doc' || mt === 'news' || mt === 'online' || mt === 'article';
      });
    }
    return items;
  },

  _render(list, items, platform, color) {
    if (!items.length) {
      list.innerHTML = `<div class="msp-loading" style="color:#94a3b8;">
        <svg style="width:32px;height:32px;stroke:#e2e8f0;fill:none;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
        Tidak ada mention periode ini
      </div>`;
      return;
    }
    const SHOW = 60;
    list.innerHTML = items.slice(0, SHOW).map(item => {
      // ── Nama: platform-specific fields dulu, lalu generic ──
      const _nameRaw = (() => {
        // Facebook: from_name, page_name, from.name
        if (platform === 'fb') {
          return item.from_name || item.page_name || item.account_name ||
                 (item.from && item.from.name) || item.user_name ||
                 item.author_name || item.name || item.publisher ||
                 null;
        }
        // Instagram: username biasanya lebih bermakna daripada id
        if (platform === 'ig') {
          return item.username || item.user_name || item.account_name ||
                 item.author_name || item.from_name || item.name ||
                 item.channel_name || null;
        }
        // TikTok: nickname > unique_id > author
        if (platform === 'tiktok') {
          return item.author_nickname || item.nickname || item.author_name ||
                 item.unique_id || item.author_unique_id || item.user_name ||
                 item.name || null;
        }
        // YouTube: channel_title > channel_name > author_name
        if (platform === 'yt') {
          return item.channel_title || item.channel_name || item.author_name ||
                 item.uploader || item.name || null;
        }
        return null;
      })();

      const name = (
        _nameRaw ||
        item.author_name || item.channel_name || item.publisher || item.source_name ||
        item.name || item.fullname || item.full_name || item.display_name ||
        item.account_name || item.user_name ||
        item.author_scr_name || item.screen_name || item.username ||
        'Tidak diketahui'
      ).trim();

      // Kalau masih berupa angka panjang (FB user ID), gunakan fallback label
      const isNumericId = /^\d{8,}$/.test(name);
      const displayName = isNumericId ? `User FB ${name.slice(-4)}` : name;

      // ── Handle/username: jangan tampilkan kalau sama dengan nama ──
      const rawHandle = (() => {
        if (platform === 'fb')  return (item.from_id ? '' : '') || item.author_scr_name || item.screen_name || item.username || item.handle || '';
        if (platform === 'ig')  return item.username || item.author_scr_name || item.screen_name || item.handle || '';
        return item.author_scr_name || item.screen_name || item.username || item.handle || item.user_handle || item.account_handle || '';
      })().trim();

      const handle = (() => {
        if (!rawHandle) return '';
        const withAt = ['twit','ig','tiktok'].includes(platform) ? (rawHandle.startsWith('@') ? rawHandle : '@'+rawHandle) : rawHandle;
        // jangan tampilkan kalau sama persis dengan displayName (case-insensitive, strip @)
        const bare = withAt.replace(/^@/,'').toLowerCase();
        return bare === displayName.toLowerCase() ? '' : withAt;
      })();

      const text    = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim().slice(0,155);

      // ── Avatar: coba lebih banyak field ──
      const av = (
        item.avatar_url || item.profile_image_url || item.author_image ||
        item.profile_image || item.thumbnail || item.picture ||
        item.user_image || item.avatar || item.photo_url ||
        item.profile_photo || item.user_photo || ''
      ).trim();

      // ── Inisial: ambil dari displayName ──
      const words   = displayName.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
      const ini     = words.length >= 2
        ? (words[0][0] + words[words.length-1][0]).toUpperCase()
        : (words[0]?.[0] || displayName[0] || '?').toUpperCase();
      const safeIni = ini.replace(/['"]/g,'');

      const sentRaw = String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
      const sent    = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif' ? 'pos' : sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'||sentRaw==='negatif' ? 'neg' : 'neu';
      const sentLbl = sent === 'pos' ? 'Pos' : sent === 'neg' ? 'Neg' : 'Neu';
      const dt      = (item.date_created || item.created_at || item.publish_date || '').split('T')[0];
      const avHtml  = (av && (av.startsWith('http://') || av.startsWith('https://')))
        ? `<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.setAttribute('data-ini','${safeIni}');this.parentElement.classList.add('av-fallback');">`
        : ini;
      const eng     = (() => {
        const f = n => parseInt(n)||0 > 0 ? parseInt(n).toLocaleString() : null;
        const parts = [];
        if (platform==='twit')   { const rt=f(item.num_retweeted||item.retweet_count), lk=f(item.num_likes||item.favorite_count); if(rt)parts.push('RT '+rt); if(lk)parts.push('Like '+lk); }
        else if (platform==='yt'){ const v=f(item.num_views||item.views), lk=f(item.num_likes||item.likes); if(v)parts.push('Views '+v); if(lk)parts.push('Like '+lk); }
        else if (platform==='tiktok'){ const v=f(item.views||item.num_views||item.play_count), lk=f(item.likes||item.num_likes||item.digg_count); if(v)parts.push('Views '+v); if(lk)parts.push('Like '+lk); }
        else if (platform==='ig'){ const lk=f(item.num_likes||item.likes||item.like_count), cm=f(item.num_comments||item.comment_count||item.comments_count); if(lk)parts.push('Like '+lk); if(cm)parts.push('Komen '+cm); }
        else if (platform==='fb'){ const lk=f(item.likes||item.num_likes||item.like_count||item.reactions_count), sh=f(item.shares||item.share_count); if(lk)parts.push('Like '+lk); if(sh)parts.push('Share '+sh); }
        return parts.join(' · ');
      })();
      const itemData = esc(JSON.stringify(item));
      return `<div class="msp-item" data-item='${itemData}' data-plat="${platform}" onclick="MSPopup._onItemClick(this)">
        <div class="msp-avatar" style="background:linear-gradient(135deg,${color},${color}99);">${avHtml}</div>
        <div class="msp-item-body">
          <div class="msp-item-author">${esc(displayName)}</div>
          ${handle ? `<div class="msp-item-handle">${esc(handle)}</div>` : ''}
          <div class="msp-item-text">${esc(text || '(tidak ada konten)')}</div>
          <div class="msp-item-footer">
            <span class="msp-sent msp-sent--${sent}">${sentLbl}</span>
            ${eng ? `<span>${esc(eng)}</span>` : ''}
            ${dt  ? `<span style="margin-left:auto;">${dt}</span>` : ''}
          </div>
        </div>
      </div>`;
    }).join('');
    if (items.length > SHOW) {
      list.insertAdjacentHTML('beforeend', `<div style="padding:9px 14px;text-align:center;font-size:11px;font-weight:600;color:#64748b;background:var(--bg-gray-50);border-top:1px dashed var(--border-gray);">+${(items.length-SHOW).toLocaleString()} mentions lainnya</div>`);
    }
  },

  _onItemClick(el) {
    try {
      const raw  = el.getAttribute('data-item');
      const item = JSON.parse(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"'));
      const plat = el.dataset.plat || this._curPlat;
      MSDetail.open(item, plat);
    } catch(e) { console.warn('Detail parse error:', e); }
  }
};

/* ══════════════════════════════════════════════════════
   DETAIL PANEL
══════════════════════════════════════════════════════ */
const MSDetail = {
  open(item, platform) {
    const panel = document.getElementById('msDetailPanel');
    const body  = document.getElementById('msDpBody');
    const title = document.getElementById('msDpTitle');
    const meta  = MSCfg.platMeta[platform] || { label: platform, color: '#038047' };

    const _nameRaw = (() => {
      if (platform === 'fb')     return item.from_name || item.page_name || item.account_name || (item.from && item.from.name) || item.user_name || null;
      if (platform === 'ig')     return item.username || item.user_name || item.account_name || item.author_name || null;
      if (platform === 'tiktok') return item.author_nickname || item.nickname || item.author_name || item.unique_id || null;
      if (platform === 'yt')     return item.channel_title || item.channel_name || item.author_name || item.uploader || null;
      return null;
    })();

    const nameRaw = (
      _nameRaw ||
      item.author_name || item.channel_name || item.publisher || item.source_name ||
      item.name || item.fullname || item.full_name || item.display_name ||
      item.account_name || item.user_name ||
      item.author_scr_name || item.screen_name || item.username ||
      'Tidak diketahui'
    ).trim();

    const isNumericId = /^\d{8,}$/.test(nameRaw);
    const name = isNumericId ? `User ${nameRaw.slice(-4)}` : nameRaw;

    const rawHandle = (() => {
      if (platform === 'ig')  return item.username || item.author_scr_name || item.screen_name || '';
      return item.author_scr_name || item.screen_name || item.username || item.handle || '';
    })().trim();
    const handle    = rawHandle && rawHandle.toLowerCase() !== name.toLowerCase()
      ? (rawHandle.startsWith('@') ? rawHandle : '@'+rawHandle) : '';

    const content = (item.content||item.caption||item.description||item.title||item.text||'').replace(/<[^>]*>/g,'').trim();
    const av      = (
      item.avatar_url || item.profile_image_url || item.author_image ||
      item.profile_image || item.thumbnail || item.picture ||
      item.user_image || item.avatar || item.photo_url ||
      item.profile_photo || item.user_photo || ''
    ).trim();
    const url     = item.url||item.link||'';
    const date    = item.date_created||item.created_at||item.publish_date||'';
    const sentRaw = String(item.class_sentiment||item.sentiment||'0').toLowerCase().trim();
    const sent    = sentRaw==='1'||sentRaw==='positive'||sentRaw==='positif' ? 'pos' : sentRaw==='-1'||sentRaw==='2'||sentRaw==='negative'||sentRaw==='negatif' ? 'neg' : 'neu';
    const sentLbl = sent==='pos' ? 'Positive' : sent==='neg' ? 'Negative' : 'Neutral';

    title.textContent = name;

    const words   = name.replace(/[^a-zA-Z0-9\s]/g,'').trim().split(/\s+/).filter(Boolean);
    const ini     = words.length >= 2
      ? (words[0][0] + words[words.length-1][0]).toUpperCase()
      : (words[0]?.[0] || name[0] || '?').toUpperCase();
    const safeIni = ini.replace(/['"]/g,'');
    const avHtml  = (av && (av.startsWith('http://') || av.startsWith('https://')))
      ? `<img src="${esc(av)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`
      : ini;

    let dtFmt = '';
    if (date) {
      try { dtFmt = new Date(date).toLocaleDateString('id-ID', { weekday:'long', day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit' }); }
      catch(e) { dtFmt = date.split('T')[0]; }
    }

    let mediaHtml = '';
    if (platform === 'yt') {
      const ytId = (url.match(/(?:v=|youtu\.be\/|embed\/)([a-zA-Z0-9_-]{11})/) || [])[1];
      if (ytId) mediaHtml = `<div class="msdp-media-wrap"><iframe style="width:100%;height:210px;border:none;display:block;" src="https://www.youtube.com/embed/${ytId}?rel=0&modestbranding=1" allowfullscreen></iframe></div>`;
    } else {
      const imgUrl = item.image_url||item.thumbnail||item.media_url||item.picture||'';
      if (imgUrl) mediaHtml = `<div class="msdp-media-wrap"><img class="msdp-media-img" src="${esc(imgUrl)}" onerror="this.parentElement.style.display='none'"></div>`;
    }

    const statsMap = {
      twit:  [['Retweet', item.num_retweeted||item.retweet_count||0], ['Like', item.num_likes||item.favorite_count||0], ['Quote', item.num_quote||0]],
      fb:    [['Like', item.likes||item.num_likes||0], ['Share', item.shares||item.share_count||0], ['Comment', item.num_comments||0]],
      ig:    [['Like', item.num_likes||item.likes||0], ['Comment', item.num_comments||item.comment_count||0], ['View', item.num_views||item.views||0]],
      yt:    [['View', item.num_views||item.views||0], ['Like', item.num_likes||item.likes||0], ['Comment', item.num_comments||item.comment_count||0]],
      tiktok:[['Play', item.views||item.play_count||0], ['Like', item.likes||item.digg_count||0], ['Share', item.shares||item.share_count||0]],
      doc:   [['Read', item.num_views||0], ['Share', item.num_share||0], ['Comment', item.num_comments||0]],
    };
    const stats     = statsMap[platform] || [];
    const statsHtml = stats.some(s => parseInt(s[1]) > 0) ?
      `<div class="msdp-stats-grid">${stats.map(([l,v]) => `<div class="msdp-stat-box"><div class="msdp-stat-val">${parseInt(v||0).toLocaleString()}</div><div class="msdp-stat-lbl">${l}</div></div>`).join('')}</div>` : '';

    body.innerHTML = `
      <div class="msdp-avatar-row">
        <div class="msdp-avatar-lg" style="background:linear-gradient(135deg,${meta.color},${meta.color}99);">${avHtml}</div>
        <div>
          <div class="msdp-author-name">${esc(name)}</div>
          ${handle ? `<div class="msdp-author-handle">${esc(handle.startsWith('@') ? handle : '@'+handle)}</div>` : ''}
          <span style="background:${meta.color}22;color:${meta.color};padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;display:inline-block;margin-top:4px;">${meta.label}</span>
        </div>
      </div>
      ${dtFmt ? `<div class="msdp-meta-row"><span>${dtFmt}</span></div>` : ''}
      <span class="msdp-sent-badge msdp-sent-badge--${sent}">${sentLbl}</span>
      ${mediaHtml}
      ${content ? `<div class="msdp-content-text">${esc(content)}</div>` : ''}
      ${statsHtml}
      ${url ? `<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="msdp-link-btn">
        <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Lihat ${meta.label} Asli
      </a>` : ''}`;

    panel.classList.add('visible');
  },

  close() { document.getElementById('msDetailPanel').classList.remove('visible'); }
};

/* ══════════════════════════════════════════════════════
   MSPage
══════════════════════════════════════════════════════ */
/* ══════════════════════════════════════════════════════
   TREND TOGGLE (DAILY ↔ MONTHLY)
══════════════════════════════════════════════════════ */
const MSTrendToggle = {
  _mode: 'daily',
  _trendData: null,
  _weekOffset: 0,
  _datePickerOverride: false,

  set(mode) {
    if (this._mode === mode) return;
    this._mode = mode;
    // Update toggle button UI
    document.querySelectorAll('#trendToggle .ms-toggle-btn').forEach(b => {
      b.classList.toggle('active', b.dataset.mode === mode);
    });
    // Update subtitle
    const sub = document.getElementById('trendSubtitle');
    if (sub) sub.textContent = mode === 'monthly' 
  ? 'Total mentions per bulan' 
  : MSTrendToggle._datePickerOverride 
    ? `${MSCfg.sd} – ${MSCfg.ed}` 
    : '8 hari terakhir dihitung mundur dari hari ini';
    // Show/hide week nav
    const weekNavGroup = document.getElementById('weekNavGroup');
    if (weekNavGroup) weekNavGroup.style.display = mode === 'daily' ? 'flex' : 'none';
    // Reset week offset when switching to daily
    if (mode === 'daily') { this._weekOffset = 0; this._trendData = null; }
    // Re-render chart
    if (this._trendData) this._render(this._trendData);
    else loadTrend();
  },

  setData(rawData) { this._trendData = rawData; },

navWeek(dir) {
  // dir: +1 = go further back, -1 = come forward
  const next = this._weekOffset + dir;
  if (next < 0) return; // can't go beyond current week
  this._weekOffset = next;
  this._trendData = null; // clear cache to force fresh fetch
  loadTrend();
},

_weekLabel() {
  if (this._weekOffset === 0) return 'Minggu Ini';
  return `Week -${this._weekOffset}`;
},

  copyCSV() {
    if (!this._trendData) { alert('Data belum tersedia'); return; }
    const lines = this._buildCSV(this._trendData, this._mode);
    MSCsvModal.show('Trend Mentions — ' + (this._mode === 'monthly' ? 'Bulanan' : 'Harian'), lines);
  },

  _buildCSV(raw, mode) {
    const platOrder = ['doc', 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok'];
    const platMeta  = { doc:'Online News (Ind)', twitter:'Twitter', facebook:'Facebook', instagram:'Instagram', youtube:'YouTube', tiktok:'TikTok' };

    if (mode === 'monthly') {
      // Group data per month
      const monthMap = {};
      raw.forEach(p => {
        (p.data || []).forEach(d => {
          const m = d.date.slice(0, 7); // YYYY-MM
          if (!monthMap[m]) monthMap[m] = {};
          monthMap[m][p.key] = (monthMap[m][p.key] || 0) + d.count;
        });
      });
      const months = Object.keys(monthMap).sort();
      const lines  = [];
      months.forEach((m, mi) => {
        platOrder.forEach((k, ki) => {
          const val = monthMap[m][k] || 0;
          if (val > 0) lines.push(`${lines.length};${platMeta[k]||k};${val};${m}`);
        });
      });
      return lines;
    } else {
      // Daily
      const dSet = new Set();
      raw.forEach(p => (p.data || []).forEach(d => dSet.add(d.date)));
      const allDates = Array.from(dSet).sort();
      const lines = [];
      allDates.forEach(date => {
        raw.forEach(p => {
          const pt = (p.data || []).find(x => x.date === date);
          if (pt && pt.count > 0) lines.push(`${lines.length};${platMeta[p.key]||p.key};${pt.count};${date}`);
        });
      });
      return lines;
    }
  },

  _render(raw) {
    const platMetaFull = {
      doc:       { label: 'Online News (Ind)', color: '#038047' },
      twitter:   { label: 'Twitter',           color: '#00b4d8' },
      facebook:  { label: 'Facebook',          color: '#4361ee' },
      instagram: { label: 'Instagram',         color: '#f72585' },
      youtube:   { label: 'YouTube',           color: '#e63946' },
      tiktok:    { label: 'TikTok',            color: '#7209b7' },
    };
    const platOrder = ['doc', 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok'];

    if (this._mode === 'monthly') {
      // Aggregate per month
      const monthMap = {};
      raw.forEach(p => {
        (p.data || []).forEach(d => {
          const m = d.date.slice(0, 7);
          if (!monthMap[m]) monthMap[m] = {};
          monthMap[m][p.key] = (monthMap[m][p.key] || 0) + d.count;
        });
      });
      const months  = Object.keys(monthMap).sort();
      const xLabels = months.map(m => {
        const dt = new Date(m + '-01T00:00:00');
        return dt.toLocaleString('id-ID', { month: 'short', year: 'numeric' });
      });

      document.getElementById('trendBadge').textContent = xLabels[0] + ' – ' + xLabels[xLabels.length - 1];

      const series = platOrder.map(key => {
        const meta = platMetaFull[key];
        const vals = months.map(m => monthMap[m]?.[key] || 0);
        const hasData = vals.some(v => v > 0);
        return {
          name: meta.label, type: 'bar', stack: 'total',
          data: vals,
          itemStyle: { color: meta.color, borderRadius: [4,4,0,0] },
          label: { show: false },
          emphasis: { focus: 'series', itemStyle: { shadowBlur: 12, shadowColor: meta.color + '66' } },
        };
      }).filter(s => s.data.some(v => v > 0));

      // Add total label on top of stack
      if (series.length > 0) {
        series[series.length - 1].label = {
          show: months.length <= 18, position: 'top',
          fontFamily: "'Poppins', sans-serif", fontWeight: '700', fontSize: 10, color: '#64748b',
          formatter: p => {
            const total = platOrder.reduce((s, k) => s + (monthMap[months[p.dataIndex]]?.[k] || 0), 0);
            return total > 0 ? numK(total) : '';
          }
        };
      }

      const trendChart = MSCharts.make('chTrend');
      if (!trendChart) return;

      trendChart.setOption({
        animation: true, animationDuration: 800, animationEasing: 'elasticOut',
        backgroundColor: '#ffffff',
        tooltip: {
          backgroundColor: '#ffffff', borderColor: '#e2e8f0', borderWidth: 1,
          padding: [12, 16],
          textStyle: { color: '#1a202c', fontFamily: "'Poppins', sans-serif", fontSize: 12 },
          extraCssText: 'border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.12);',
          trigger: 'axis',
          axisPointer: { type: 'shadow', shadowStyle: { color: 'rgba(3,128,71,.05)' } },
          formatter: params => {
            const m      = months[params[0]?.dataIndex ?? 0];
            const sorted = [...params].sort((a, b) => b.value - a.value);
            const rows   = sorted.filter(p => p.value > 0).map(p =>
              `<div style="display:flex;align-items:center;justify-content:space-between;gap:20px;padding:2px 0;">
                 <div style="display:flex;align-items:center;gap:7px;">
                   <span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:${p.color};flex-shrink:0;"></span>
                   <span style="font-size:12px;color:#64748b;">${p.seriesName}</span>
                 </div>
                 <span style="font-size:12px;font-weight:700;color:#1a202c;">${numFmt(p.value)}</span>
               </div>`).join('');
            const total = params.reduce((s, p) => s + (p.value || 0), 0);
            return `<div style="font-weight:700;font-size:12px;color:#1a202c;margin-bottom:8px;padding-bottom:6px;border-bottom:1px solid #f1f5f9;">${xLabels[params[0]?.dataIndex ?? 0]}</div>
                    ${rows || '<div style="color:#94a3b8;font-size:12px;">Tidak ada data</div>'}
                    <div style="border-top:1px solid #f1f5f9;margin-top:6px;padding-top:6px;display:flex;justify-content:space-between;">
                      <span style="font-size:11px;color:#94a3b8;">Total</span>
                      <span style="font-size:12px;font-weight:700;color:#1a202c;">${numFmt(total)}</span>
                    </div>
                    <div style="margin-top:5px;font-size:10px;color:#94a3b8;text-align:center;">Klik untuk lihat mentions</div>`;
          }
        },
        legend: {
          bottom: 0, type: 'scroll',
          data: series.map(s => s.name),
          textStyle: { fontFamily:"'Poppins', sans-serif", fontSize:11, fontWeight:'600', color:'#64748b' },
          icon: 'circle', itemWidth:10, itemHeight:10, itemGap:20,
        },
        grid: { top:24, right:20, bottom:50, left:60 },
        xAxis: {
          type: 'category', data: xLabels,
          axisLine: { lineStyle: { color:'#e2e8f0' } }, axisTick: { show:false },
          axisLabel: { fontFamily:"'Poppins', sans-serif", fontSize:11, fontWeight:'600', color:'#64748b' }
        },
        yAxis: {
          type: 'value', axisLine: { show:false }, axisTick: { show:false },
          splitLine: { lineStyle: { color:'#f1f5f9', type:'solid', width:1 } },
          axisLabel: { fontFamily:"'Poppins', sans-serif", fontSize:11, color:'#94a3b8', formatter:numK }
        },
        series,
      }, true);

      trendChart.on('click', params => {
        if (params.componentType !== 'series') return;
        const keyMap = { 'Online News (Ind)':'doc', 'Twitter':'twit', 'Facebook':'fb', 'Instagram':'ig', 'YouTube':'yt', 'TikTok':'tiktok' };
        const k = keyMap[params.seriesName];
        if (k) {
          const rect = trendChart.getDom().getBoundingClientRect();
          MSPopup.open(k, rect.left + params.event.offsetX, rect.top + params.event.offsetY);
        }
      });
      trendChart.on('mouseover', p => { if (p.componentType==='series') trendChart.getDom().style.cursor='pointer'; });
      trendChart.on('mouseout',  () => { trendChart.getDom().style.cursor='default'; });

    } else {
      // Re-run daily
      loadTrend();
    }
  }
};

/* ══════════════════════════════════════════════════════
   CSV MODAL
══════════════════════════════════════════════════════ */
const MSCsvModal = {
  _content: '',
  _articleData: null,  // { dates, values }
  _weekdayData: null,  // { wdNames, platItems }
  _hourData:    null,  // { hrLabels, platItems }

  show(title, lines) {
    this._content = lines.join('\n');
    document.querySelector('.ms-csv-modal__title').textContent = title || 'CSV Data';
    document.getElementById('msCsvContent').textContent = this._content;
    document.getElementById('msCsvCopyBtn').innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;display:inline;vertical-align:middle;margin-right:5px;"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy CSV data`;
    document.getElementById('msCsvCopyBtn').classList.remove('copied');
    document.getElementById('msCsvModal').classList.add('show');
  },

  close() { document.getElementById('msCsvModal').classList.remove('show'); },

  // ── Article trend ──
  setArticleData(dates, values) { this._articleData = { dates, values }; },

  showArticleTrend() {
    if (!this._articleData) { alert('Data belum tersedia'); return; }
    const { dates, values } = this._articleData;
    const lines = dates.map((d, i) => `${i};Online News (Ind);${values[i]};${d}`).filter((_,i) => values[i] > 0);
    this.show('Trend Articles — Online News', lines);
  },

  // ── Weekday ──
  setWeekdayData(wdNames, platItems) { this._weekdayData = { wdNames, platItems }; },

  showWeekday() {
    if (!this._weekdayData) { alert('Data belum tersedia'); return; }
    const { wdNames, platItems } = this._weekdayData;
    const lines = [];
    wdNames.forEach((day, di) => {
      platItems.forEach(plat => {
        const v = plat.data[di] || 0;
        if (v > 0) lines.push(`${lines.length};${plat.label};${v};${day}`);
      });
    });
    this.show('Mentions by Weekday', lines);
  },

  // ── Hour ──
  setHourData(hrLabels, platItems) { this._hourData = { hrLabels, platItems }; },

  showHour() {
    if (!this._hourData) { alert('Data belum tersedia'); return; }
    const { hrLabels, platItems } = this._hourData;
    const lines = [];
    hrLabels.forEach((hr, hi) => {
      platItems.forEach(plat => {
        const v = plat.data[hi] || 0;
        if (v > 0) lines.push(`${lines.length};${plat.label};${v};${hr}`);
      });
    });
    this.show('Mentions by Hour', lines);
  },

  copy() {
    if (!this._content) return;
    navigator.clipboard.writeText(this._content).then(() => {
      const btn = document.getElementById('msCsvCopyBtn');
      btn.textContent = '✓ Tersalin!';
      btn.classList.add('copied');
      setTimeout(() => {
        btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:13px;height:13px;display:inline;vertical-align:middle;margin-right:5px;"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy CSV data`;
        btn.classList.remove('copied');
      }, 2000);
    }).catch(() => {
      const ta = document.createElement('textarea');
      ta.value = this._content;
      ta.style.cssText = 'position:fixed;opacity:0;top:0;left:0;';
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      const btn = document.getElementById('msCsvCopyBtn');
      btn.textContent = '✓ Tersalin!';
      btn.classList.add('copied');
      setTimeout(() => { btn.textContent = ' Copy CSV data'; btn.classList.remove('copied'); }, 2000);
    });
  }
};

// SESUDAH
const MSPage = {
  _syncDateFilter() {
    const today = new Date(); today.setHours(0,0,0,0);
    const ed    = new Date(MSCfg.ed + 'T00:00:00');
    const sd    = new Date(MSCfg.sd + 'T00:00:00');
    const diff  = Math.round((ed - sd) / 86400000) + 1;
    // Pakai week nav hanya kalau end date = hari ini dan range <= 8 hari
    MSTrendToggle._datePickerOverride = !(ed.getTime() === today.getTime() && diff <= 8);
    MSTrendToggle._weekOffset = 0;
  },

  reload() {
    MSCharts.disposeAll();
    MSTab.reset();
    this._syncDateFilter();

    ['skBar','skTrend','skWeekday','skHour','skBarRace'].forEach(showSk);

    loadMentionByPlatform();

    const activeTab = document.querySelector('.ms-tab-panel.active')?.id;
    if (activeTab === 'panelTrend') {
      MSTab._loaded.trend = true;
      loadTrend();
      loadArticleTrend();
    } else if (activeTab === 'panelPola') {
      MSTab._loaded.pola = true;
      loadWeekHour();
    }
  },

  init() {
    MSDp.init();
    MSPopup.init();
    this._syncDateFilter();
    loadMentionByPlatform();
    MSTab._loaded.trend = true;
    loadTrend();
    loadArticleTrend();
  }
};

document.addEventListener('DOMContentLoaded', () => MSPage.init());
</script>
@endsection