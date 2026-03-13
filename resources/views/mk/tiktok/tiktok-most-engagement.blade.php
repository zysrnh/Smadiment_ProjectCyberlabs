@extends('mk.layouts.app')

@section('title', 'TikTok Most Engagement - SMADIMENT')

@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════
   DESIGN TOKENS — aligned with Dashboard variables
═══════════════════════════════════════════════════════ */
:root {
    /* Dashboard palette */
    --blue         : #4361EE;
    --blue-light   : #EEF2FF;
    --green        : #10B981;
    --green-light  : #ECFDF5;
    --red          : #EF4444;
    --red-light    : #FEF2F2;
    --amber        : #F59E0B;
    --amber-light  : #FFFBEB;
    --cyan         : #06B6D4;
    --cyan-light   : #ECFEFF;

    /* TikTok brand */
    --tiktok       : #EE1D52;
    --tiktok-dark  : #C4163F;
    --tiktok-light : #FFF1F4;

    --slate-50 : #F8FAFC;
    --slate-100: #F1F5F9;
    --slate-200: #E2E8F0;
    --slate-300: #CBD5E1;
    --slate-400: #94A3B8;
    --slate-500: #64748B;
    --slate-600: #475569;
    --slate-700: #334155;
    --slate-800: #1E293B;
    --slate-900: #0F172A;

    --radius   : 8px;
    --radius-sm: 5px;
    --radius-lg: 12px;

    --shadow-sm: 0 1px 3px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04);
    --shadow-md: 0 4px 14px rgba(15,23,42,.08);
    --shadow-lg: 0 10px 30px rgba(15,23,42,.12);
}

/* ═══════════════════════════════════════════════════════
   PAGE WRAPPER
═══════════════════════════════════════════════════════ */
.fme-page { padding: 0; }

/* ═══════════════════════════════════════════════════════
   PAGE HEADER (breadcrumb-style, matches dashboard)
═══════════════════════════════════════════════════════ */
.fme-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}
.fme-page-header-left h1 {
    font-size: 20px;
    font-weight: 700;
    color: var(--slate-900);
    margin: 0 0 3px;
    letter-spacing: -.3px;
}
.fme-page-header-left p {
    font-size: 12px;
    color: var(--slate-400);
    font-weight: 500;
    margin: 0;
}
.fme-refresh-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    background: var(--slate-800);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s, box-shadow .15s;
    box-shadow: var(--shadow-sm);
}
.fme-refresh-btn:hover {
    background: var(--slate-700);
    box-shadow: var(--shadow-md);
}
.fme-refresh-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

/* ═══════════════════════════════════════════════════════
   FILTER CARD
═══════════════════════════════════════════════════════ */
.fme-filter-card {
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius);
    padding: 14px 16px;
    margin-bottom: 18px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
}
.fme-filter-group { display: flex; flex-direction: column; gap: 4px; }
.fme-filter-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: .5px;
}
.fme-filter-select,
.fme-date-trigger {
    padding: 7px 12px;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius-sm);
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--slate-800);
    background: var(--slate-50);
    outline: none;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    min-width: 180px;
}
.fme-filter-select:focus,
.fme-date-trigger:focus,
.fme-date-trigger:hover {
    border-color: var(--blue);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(67,97,238,.08);
}
.fme-date-trigger {
    display: flex;
    align-items: center;
    gap: 7px;
    min-width: 260px;
}
.fme-date-trigger svg { width: 14px; height: 14px; stroke: var(--slate-400); fill: none; flex-shrink: 0; }
.fme-apply-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 18px;
    background: var(--blue);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background .14s, box-shadow .14s;
    white-space: nowrap;
    box-shadow: 0 4px 10px rgba(67,97,238,.18);
}
.fme-apply-btn:hover { background: #2540c4; box-shadow: 0 4px 14px rgba(67,97,238,.3); }
.fme-apply-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; }

/* ═══════════════════════════════════════════════════════
   DATE PICKER MODAL
═══════════════════════════════════════════════════════ */
.dp-modal { position: fixed; inset: 0; z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(15,23,42,.45); backdrop-filter: blur(6px); }
.dp-modal.show { display: flex; }
.dp-overlay { position: absolute; inset: 0; cursor: pointer; }
.dp-container { position: relative; z-index: 1; background: #fff; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); display: flex; max-width: 860px; width: 90%; max-height: 90vh; animation: dpFadeUp .25s ease-out; }
@keyframes dpFadeUp { from{opacity:0;transform:translateY(16px) scale(.97)}to{opacity:1;transform:none} }
.dp-sidebar { width: 170px; background: var(--slate-50); border-right: 1px solid var(--slate-200); padding: 14px 10px; border-radius: var(--radius-lg) 0 0 var(--radius-lg); display: flex; flex-direction: column; gap: 3px; flex-shrink: 0; }
.dp-preset { padding: 8px 14px; background: transparent; border: none; border-radius: var(--radius-sm); font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 600; color: var(--slate-700); text-align: left; cursor: pointer; transition: background .12s, color .12s; }
.dp-preset:hover { background: #fff; color: var(--blue); }
.dp-preset.active { background: var(--blue); color: #fff; }
.dp-content { flex: 1; padding: 20px; display: flex; flex-direction: column; overflow: hidden; }
.dp-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px; }
.dp-nav { width: 32px; height: 32px; border-radius: var(--radius-sm); background: var(--slate-50); border: 1px solid var(--slate-200); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .12s, border-color .12s; flex-shrink: 0; }
.dp-nav:hover { background: var(--blue); border-color: var(--blue); color: #fff; }
.dp-nav svg { width: 18px; height: 18px; }
.dp-calendars { display: flex; gap: 20px; flex: 1; }
.dp-calendar { flex: 1; }
.dp-cal-month { font-size: 14px; font-weight: 700; color: var(--slate-800); margin-bottom: 12px; text-align: center; }
.dp-weekdays { display: grid; grid-template-columns: repeat(7,1fr); gap: 2px; margin-bottom: 6px; }
.dp-weekday { text-align: center; font-size: 10px; font-weight: 700; color: var(--slate-400); padding: 6px 0; }
.dp-days { display: grid; grid-template-columns: repeat(7,1fr); gap: 2px; }
.dp-day { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 500; border-radius: var(--radius-sm); cursor: pointer; transition: background .1s; color: var(--slate-800); background: transparent; border: none; padding: 0; font-family: 'Poppins', sans-serif; }
.dp-day:hover:not(.dp-day--other):not(.dp-day--disabled) { background: var(--slate-100); }
.dp-day--other { color: var(--slate-300); cursor: default; }
.dp-day--disabled { color: var(--slate-200); cursor: not-allowed; }
.dp-day--today { border: 1.5px solid var(--blue); color: var(--blue); font-weight: 700; }
.dp-day--selected { background: var(--blue); color: #fff; }
.dp-day--in-range { background: var(--blue-light); color: var(--blue); }
.dp-display { padding: 10px 14px; background: var(--slate-50); border-radius: var(--radius-sm); border: 1px solid var(--slate-200); text-align: center; font-size: 13px; font-weight: 600; color: var(--slate-800); margin-bottom: 14px; }
.dp-footer { display: flex; gap: 8px; justify-content: flex-end; }
.dp-cancel { padding: 8px 18px; border-radius: var(--radius-sm); font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; border: 1px solid var(--slate-200); background: var(--slate-50); color: var(--slate-700); transition: background .12s; }
.dp-cancel:hover { background: var(--slate-100); }
.dp-apply { padding: 8px 18px; border-radius: var(--radius-sm); font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; border: none; background: var(--blue); color: #fff; box-shadow: 0 3px 10px rgba(67,97,238,.2); transition: background .14s; }
.dp-apply:hover { background: #2540c4; }

/* ═══════════════════════════════════════════════════════
   KPI CARDS — same pattern as Dashboard kpi-grid
═══════════════════════════════════════════════════════ */
.fme-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 18px;
}
@media (max-width: 1100px) { .fme-kpi-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 560px)  { .fme-kpi-grid { grid-template-columns: 1fr; } }

.fme-kpi {
    border-radius: var(--radius);
    padding: 18px 20px;
    position: relative;
    overflow: hidden;
    border: none;
    transition: transform .2s, box-shadow .2s;
}
.fme-kpi:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg) !important; }
.fme-kpi--tiktok { background: linear-gradient(135deg, #EE1D52 0%, #C4163F 100%); }
.fme-kpi--green  { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
.fme-kpi--amber  { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); }
.fme-kpi--cyan   { background: linear-gradient(135deg, #06B6D4 0%, #0891B2 100%); }

.fme-kpi-icon {
    width: 36px; height: 36px;
    border-radius: var(--radius-sm);
    background: rgba(255,255,255,.16);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px;
}
.fme-kpi-icon svg { width: 18px; height: 18px; stroke: #fff; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.fme-kpi-label {
    font-size: 10px; font-weight: 700;
    color: rgba(255,255,255,.68);
    text-transform: uppercase; letter-spacing: .6px; margin-bottom: 3px;
}
.fme-kpi-value {
    font-size: 26px; font-weight: 800; color: #fff;
    line-height: 1; letter-spacing: -1px; margin-bottom: 5px;
    min-height: 30px; display: flex; align-items: center;
}
.fme-kpi-sub { font-size: 11px; color: rgba(255,255,255,.62); font-weight: 600; }
.fme-kpi-wm {
    position: absolute; right: -10px; bottom: -10px;
    font-size: 72px; color: rgba(255,255,255,.07);
    pointer-events: none; line-height: 1;
}

/* ═══════════════════════════════════════════════════════
   SECTION HEADER — same minimal style as dashboard
═══════════════════════════════════════════════════════ */
.fme-section-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    margin-top: 4px;
}
.fme-section-icon {
    width: 28px; height: 28px;
    border-radius: var(--radius-sm);
    background: var(--blue-light);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.fme-section-icon svg { width: 14px; height: 14px; stroke: var(--blue); fill: none; stroke-width: 2; stroke-linecap: round; }
.fme-section-title {
    font-size: 10px; font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase; letter-spacing: .6px;
}
.fme-section-line { flex: 1; height: 1px; background: var(--slate-100); }

/* ═══════════════════════════════════════════════════════
   CARD — matches .proj-card from Dashboard
═══════════════════════════════════════════════════════ */
.fme-card {
    background: #fff;
    border-radius: var(--radius);
    border: 1px solid var(--slate-200);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: border-color .2s, box-shadow .2s;
    margin-bottom: 14px;
}
.fme-card--tiktok:hover { border-color: rgba(238,29,82,.3); box-shadow: 0 0 0 3px rgba(238,29,82,.06), var(--shadow-md); }
.fme-card--green:hover  { border-color: rgba(16,185,129,.3); box-shadow: 0 0 0 3px rgba(16,185,129,.06), var(--shadow-md); }
.fme-card--amber:hover  { border-color: rgba(245,158,11,.3); box-shadow: 0 0 0 3px rgba(245,158,11,.06), var(--shadow-md); }
.fme-card--cyan:hover   { border-color: rgba(6,182,212,.3);  box-shadow: 0 0 0 3px rgba(6,182,212,.06),  var(--shadow-md); }

/* Card header — matches .proj-card-header */
.fme-card-header {
    padding: 12px 16px 10px;
    border-bottom: 1px solid var(--slate-200);
    background: #fff;
}
.fme-card-header-row {
    display: flex; align-items: center;
    justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
    margin-bottom: 6px;
}
.fme-card-header-left { display: flex; align-items: center; gap: 10px; }
.fme-card-icon {
    width: 34px; height: 34px; border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.fme-card-icon svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.fme-card-icon--tiktok { background: rgba(238,29,82,.08); color: #EE1D52; }
.fme-card-icon--green  { background: rgba(16,185,129,.08); color: #10B981; }
.fme-card-icon--amber  { background: rgba(245,158,11,.08); color: #F59E0B; }
.fme-card-icon--cyan   { background: rgba(6,182,212,.08);  color: #06B6D4; }
.fme-card-icon--ink    { background: rgba(30,41,59,.06);   color: var(--slate-700); }
.fme-card-title    { font-size: 13px; font-weight: 700; color: var(--slate-900); margin: 0 0 2px; }
.fme-card-subtitle { font-size: 11px; color: var(--slate-400); font-weight: 500; }
.fme-card-actions  { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.fme-card-body { padding: 14px 16px 16px; }
.fme-card-body--flush { padding: 0; }

/* Badges — flat pill like meta-pill */
.fme-badge {
    display: inline-flex; align-items: center;
    padding: 3px 9px; border-radius: 3px;
    font-size: 10px; font-weight: 700; letter-spacing: .2px;
    white-space: nowrap;
}
.fme-badge--tiktok { background: #ffe4ec; color: #9b1239; }
.fme-badge--green  { background: #d1fae5; color: #065f46; }
.fme-badge--amber  { background: #fef3c7; color: #92400e; }
.fme-badge--cyan   { background: #cffafe; color: #164e63; }
.fme-badge--gray   { background: var(--slate-100); color: var(--slate-500); }

/* Small action buttons — matches btn-primary-sm / btn-icon-sm */
.fme-btn-sm {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 5px 12px;
    background: var(--blue); border: none;
    border-radius: var(--radius-sm);
    color: #fff; font-family: 'Poppins', sans-serif;
    font-size: 11px; font-weight: 600;
    cursor: pointer; transition: background .14s, box-shadow .14s; white-space: nowrap;
}
.fme-btn-sm:hover { background: #2540c4; box-shadow: 0 3px 10px rgba(67,97,238,.3); }
.fme-btn-sm--tiktok { background: var(--tiktok); }
.fme-btn-sm--tiktok:hover { background: var(--tiktok-dark); box-shadow: 0 3px 10px rgba(238,29,82,.3); }

.fme-btn-icon {
    width: 28px; height: 28px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid var(--slate-200); border-radius: var(--radius-sm);
    background: #fff; color: var(--slate-500);
    font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 600;
    cursor: pointer; transition: all .14s; white-space: nowrap;
}
.fme-btn-icon:hover { background: var(--blue); border-color: var(--blue); color: #fff; }
.fme-btn-icon svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 2.2; }

/* Rows select */
.fme-rows-sel {
    padding: 5px 10px;
    border: 1px solid var(--slate-200); border-radius: var(--radius-sm);
    font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 600;
    color: var(--slate-600); background: var(--slate-50);
    outline: none; cursor: pointer; transition: border-color .14s;
}
.fme-rows-sel:focus { border-color: var(--blue); }

/* ═══════════════════════════════════════════════════════
   DONUT CHART SECTION
═══════════════════════════════════════════════════════ */
.fme-donut-legend { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.fme-donut-leg-item {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; color: var(--slate-500);
    padding: 3px 9px; background: var(--slate-50);
    border-radius: 20px; border: 1px solid var(--slate-200);
    cursor: pointer; transition: border-color .12s, background .12s, color .12s;
}
.fme-donut-leg-item:hover { border-color: var(--blue); background: var(--blue-light); color: var(--blue); }
.fme-donut-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* ═══════════════════════════════════════════════════════
   TABS — compact, matches dashboard btn-group style
═══════════════════════════════════════════════════════ */
.fme-tabs {
    display: flex; gap: 4px;
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: var(--radius);
    padding: 5px;
    margin-bottom: 16px;
    box-shadow: var(--shadow-sm);
    flex-wrap: wrap;
}
.fme-tab-btn {
    flex: 1; display: flex; align-items: center;
    justify-content: center; gap: 6px;
    padding: 8px 14px; border-radius: var(--radius-sm);
    border: none; background: transparent;
    font-family: 'Poppins', sans-serif;
    font-size: 12px; font-weight: 600; color: var(--slate-500);
    cursor: pointer; transition: background .14s, color .14s; min-width: 0;
}
.fme-tab-btn:hover { background: var(--slate-50); color: var(--slate-800); }
.fme-tab-btn.active { background: var(--blue); color: #fff; box-shadow: 0 3px 10px rgba(67,97,238,.2); }
.fme-tab-btn.active--tiktok { background: var(--tiktok) !important; box-shadow: 0 3px 10px rgba(238,29,82,.2) !important; }
.fme-tab-btn.active--green  { background: #10B981 !important; box-shadow: 0 3px 10px rgba(16,185,129,.2) !important; }
.fme-tab-btn.active--amber  { background: #F59E0B !important; box-shadow: 0 3px 10px rgba(245,158,11,.2) !important; }
.fme-tab-btn.active--cyan   { background: #06B6D4 !important; box-shadow: 0 3px 10px rgba(6,182,212,.2) !important; }
.fme-tab-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; flex-shrink: 0; }
.fme-tab-chip {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 20px; height: 18px; padding: 0 5px;
    border-radius: 9px; font-size: 9px; font-weight: 800;
    background: rgba(255,255,255,.22); color: inherit;
}
.fme-tab-btn:not(.active) .fme-tab-chip { background: var(--slate-100); color: var(--slate-400); }
.fme-tab-panel { display: none; }
.fme-tab-panel.active { display: block; }

/* ═══════════════════════════════════════════════════════
   TOGGLE GROUP — matches dashboard btn-group
═══════════════════════════════════════════════════════ */
.fme-toggle-group { display: flex; background: var(--slate-50); border-radius: var(--radius-sm); padding: 2px; gap: 2px; border: 1px solid var(--slate-200); }
.fme-toggle-btn { display: flex; align-items: center; gap: 4px; padding: 5px 12px; border-radius: 4px; border: none; background: transparent; font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 600; color: var(--slate-500); cursor: pointer; transition: background .12s, color .12s; white-space: nowrap; }
.fme-toggle-btn:hover { background: #fff; color: var(--slate-800); }
.fme-toggle-btn.active { background: #fff; color: var(--blue); box-shadow: 0 1px 3px rgba(0,0,0,.07); }

/* ═══════════════════════════════════════════════════════
   POST LIST
═══════════════════════════════════════════════════════ */
.fme-post-list { display: flex; flex-direction: column; }
.fme-post {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 13px 16px;
    border-bottom: 1px solid var(--slate-100);
    transition: background .12s; cursor: pointer;
}
.fme-post:last-child { border-bottom: none; }
.fme-post:hover { background: var(--slate-50); }
.fme-post-rank {
    width: 24px; height: 24px; border-radius: 50%;
    background: var(--slate-100); border: 1px solid var(--slate-200);
    display: flex; align-items: center; justify-content: center;
    font-size: 9px; font-weight: 800; color: var(--slate-400);
    flex-shrink: 0; margin-top: 7px;
}
.fme-post-rank--1 { background: linear-gradient(135deg,#ffd700,#F59E0B); color: #7c5900; border-color: #ffd700; }
.fme-post-rank--2 { background: linear-gradient(135deg,#c0c0c0,#9ca3af); color: #3d3d3d; border-color: #c0c0c0; }
.fme-post-rank--3 { background: linear-gradient(135deg,#cd7f32,#b06820); color: #fff; border-color: #cd7f32; }

.fme-post-av {
    width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg,#EE1D52,#c4163f); color: #fff;
    font-weight: 700; font-size: 13px;
    display: flex; align-items: center; justify-content: center;
    border: 1.5px solid var(--slate-200); overflow: hidden;
}
.fme-post-av img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

.fme-post-body { flex: 1; min-width: 0; }
.fme-post-author { font-size: 12.5px; font-weight: 700; color: var(--slate-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fme-post-date   { font-size: 10px; color: var(--slate-400); margin-top: 1px; margin-bottom: 5px; }
.fme-post-text   { font-size: 12px; color: var(--slate-500); line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 8px; word-break: break-word; }
.fme-post-stats  { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }

.fme-metric {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 7px; border-radius: 3px;
    font-size: 10px; font-weight: 700;
    background: var(--slate-100); color: var(--slate-500);
    white-space: nowrap;
}
.fme-metric svg { width: 10px; height: 10px; fill: none; stroke-width: 2; stroke-linecap: round; flex-shrink: 0; }
.fme-metric--tiktok { background: rgba(238,29,82,.08); color: #EE1D52; }
.fme-metric--tiktok svg { stroke: #EE1D52; }
.fme-metric--green  { background: rgba(16,185,129,.08); color: #10B981; }
.fme-metric--green svg  { stroke: #10B981; }
.fme-metric--amber  { background: rgba(245,158,11,.08); color: #F59E0B; }
.fme-metric--amber svg  { stroke: #F59E0B; }
.fme-metric--cyan   { background: rgba(6,182,212,.08);  color: #06B6D4; }
.fme-metric--cyan svg   { stroke: #06B6D4; }

.fme-sent { display: inline-flex; align-items: center; padding: 2px 7px; border-radius: 3px; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; }
.fme-sent--pos { background: #d1fae5; color: #065f46; }
.fme-sent--neg { background: #fee2e2; color: #991b1b; }
.fme-sent--neu { background: var(--slate-100); color: var(--slate-500); }

.fme-view-link {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 9.5px; font-weight: 700; color: var(--tiktok);
    text-decoration: none; padding: 2px 7px; border-radius: 3px;
    background: rgba(238,29,82,.08); border: 1px solid rgba(238,29,82,.2);
    transition: background .12s, color .12s; margin-left: auto;
}
.fme-view-link:hover { background: var(--tiktok); color: #fff; }
.fme-view-link svg { width: 8px; height: 8px; stroke: currentColor; fill: none; stroke-width: 2.5; }

/* Thumbnail */
.fme-post-thumb {
    width: 88px; height: 140px; border-radius: var(--radius-sm);
    flex-shrink: 0; overflow: hidden;
    border: 1.5px solid var(--slate-200);
    background: var(--slate-800);
    position: relative; align-self: center;
    box-shadow: var(--shadow-sm);
}
.fme-post-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .2s; }
.fme-post:hover .fme-post-thumb img { transform: scale(1.06); }
.fme-post-thumb-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 24px; background: linear-gradient(135deg,#1e293b,#374151); }
.fme-post-thumb-play { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,.3); opacity: 0; transition: opacity .2s; border-radius: inherit; }
.fme-post-thumb-play svg { width: 22px; height: 22px; fill: #fff; filter: drop-shadow(0 2px 5px rgba(0,0,0,.6)); }
.fme-post:hover .fme-post-thumb-play { opacity: 1; }

/* ═══════════════════════════════════════════════════════
   PAGINATION
═══════════════════════════════════════════════════════ */
.fme-pagination { display: flex; align-items: center; justify-content: space-between; padding: 11px 16px; border-top: 1px solid var(--slate-100); flex-wrap: wrap; gap: 8px; }
.fme-pag-info { font-size: 11px; color: var(--slate-400); font-weight: 500; }
.fme-pag-controls { display: flex; align-items: center; gap: 3px; }
.fme-pag-btn { min-width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; padding: 0 6px; border-radius: var(--radius-sm); border: 1px solid var(--slate-200); background: #fff; font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 600; color: var(--slate-500); cursor: pointer; transition: all .12s; user-select: none; }
.fme-pag-btn:hover:not(:disabled):not(.is-active) { border-color: var(--blue); color: var(--blue); background: var(--blue-light); }
.fme-pag-btn.is-active { background: var(--blue); border-color: var(--blue); color: #fff; }
.fme-pag-btn:disabled { opacity: .35; cursor: not-allowed; }
.fme-pag-btn svg { width: 11px; height: 11px; stroke: currentColor; fill: none; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; }

/* ═══════════════════════════════════════════════════════
   SKELETON / LOADING
═══════════════════════════════════════════════════════ */
.sk-block {
    border-radius: 4px;
    background: linear-gradient(90deg, var(--slate-100) 25%, var(--slate-200) 50%, var(--slate-100) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}
@keyframes shimmer { 0%{background-position:-200% 0}100%{background-position:200% 0} }

.fme-spinner { width: 28px; height: 28px; border: 2.5px solid var(--slate-100); border-top-color: var(--tiktok); border-radius: 50%; animation: fmeSpin .65s linear infinite; }
@keyframes fmeSpin { to { transform: rotate(360deg); } }
.fme-spinner-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 20px; gap: 12px; color: var(--slate-400); font-size: 12px; font-weight: 600; }

.fme-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 48px 20px; gap: 8px; }
.fme-empty svg { width: 36px; height: 36px; stroke: var(--slate-200); fill: none; stroke-width: 1.5; }
.fme-empty-text { font-size: 12px; font-weight: 600; color: var(--slate-400); }

/* ═══════════════════════════════════════════════════════
   CHART HEIGHTS
═══════════════════════════════════════════════════════ */
.fme-ch-300 { position: relative; height: 300px; }
.fme-ch-320 { position: relative; height: 320px; }
.fme-ch-460 { position: relative; height: 460px; }

/* ═══════════════════════════════════════════════════════
   SLIDE-IN DRAWER (replaces popup modal)
   Slides from the right — consistent with dashboard patterns
═══════════════════════════════════════════════════════ */
.fme-drawer-backdrop {
    position: fixed; inset: 0; z-index: 5000;
    background: rgba(15,23,42,.42);
    backdrop-filter: blur(4px);
    opacity: 0; pointer-events: none;
    transition: opacity .28s ease;
}
.fme-drawer-backdrop.visible { opacity: 1; pointer-events: all; }

.fme-drawer {
    position: fixed; top: 0; right: 0; bottom: 0; z-index: 5001;
    width: 460px; max-width: 96vw;
    background: #fff;
    box-shadow: -8px 0 40px rgba(15,23,42,.16);
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform .3s cubic-bezier(.4,0,.2,1);
}
.fme-drawer.visible { transform: translateX(0); }

.fme-drawer-header {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px;
    background: var(--slate-50);
    border-bottom: 1px solid var(--slate-200);
    flex-shrink: 0;
}
.fme-drawer-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.fme-drawer-title { font-size: 13px; font-weight: 700; color: var(--slate-900); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fme-drawer-count { background: var(--tiktok); color: #fff; border-radius: 3px; padding: 1px 8px; font-size: 10px; font-weight: 800; flex-shrink: 0; }
.fme-drawer-close {
    width: 26px; height: 26px; border-radius: var(--radius-sm);
    border: none; background: transparent; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--slate-400); font-size: 18px; transition: background .12s, color .12s;
}
.fme-drawer-close:hover { background: #fee2e2; color: #991b1b; }

.fme-drawer-toolbar {
    display: flex; align-items: center; gap: 8px; padding: 7px 14px;
    border-bottom: 1px solid var(--slate-100); background: #fafbfc; flex-shrink: 0;
}
.fme-drawer-meta { flex: 1; font-size: 10px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; letter-spacing: .4px; }
.fme-drawer-export {
    display: flex; align-items: center; gap: 4px;
    padding: 4px 10px; background: var(--tiktok); color: #fff;
    border: none; border-radius: var(--radius-sm);
    font-family: 'Poppins', sans-serif; font-size: 10px; font-weight: 700;
    cursor: pointer; transition: background .12s;
}
.fme-drawer-export:hover { background: var(--tiktok-dark); }
.fme-drawer-export svg { width: 10px; height: 10px; stroke: #fff; fill: none; stroke-width: 2.5; stroke-linecap: round; }

.fme-drawer-list { overflow-y: auto; flex: 1; padding: 4px 0; min-height: 0; }
.fme-drawer-list::-webkit-scrollbar { width: 4px; }
.fme-drawer-list::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 3px; }

/* List items inside drawer */
.fme-dl-item { display: flex; gap: 9px; padding: 10px 14px; border-bottom: 1px solid var(--slate-100); transition: background .1s; cursor: pointer; align-items: flex-start; }
.fme-dl-item:last-child { border-bottom: none; }
.fme-dl-item:hover { background: var(--slate-50); }
.fme-dl-avatar { width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0; background: linear-gradient(135deg,#EE1D52,#c4163f); color: #fff; font-weight: 700; font-size: 12px; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--slate-200); overflow: hidden; }
.fme-dl-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.fme-dl-body { flex: 1; min-width: 0; }
.fme-dl-author { font-size: 12px; font-weight: 700; color: var(--slate-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fme-dl-text { font-size: 11.5px; color: var(--slate-500); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 2px 0 4px; }
.fme-dl-footer { display: flex; align-items: center; gap: 5px; font-size: 10px; color: var(--slate-400); flex-wrap: wrap; }

/* ═══════════════════════════════════════════════════════
   DETAIL PANEL (inside drawer)
   Slides in from right, over the list
═══════════════════════════════════════════════════════ */
.fme-detail-panel {
    position: absolute; inset: 0; background: #fff; z-index: 10;
    display: flex; flex-direction: column;
    transform: translateX(100%);
    transition: transform .26s cubic-bezier(.4,0,.2,1);
}
.fme-detail-panel.visible { transform: translateX(0); }

.fme-dp-header {
    display: flex; align-items: center; gap: 8px; padding: 12px 16px;
    background: var(--slate-50); border-bottom: 1px solid var(--slate-200); flex-shrink: 0;
}
.fme-dp-back {
    width: 30px; height: 30px; border-radius: var(--radius-sm);
    border: 1px solid var(--slate-200); background: #fff;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: var(--slate-500); transition: all .14s; flex-shrink: 0;
}
.fme-dp-back:hover { background: var(--tiktok-light); color: var(--tiktok); border-color: rgba(238,29,82,.3); }
.fme-dp-back svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }
.fme-dp-title { font-size: 13px; font-weight: 700; color: var(--slate-900); flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.fme-dp-close { width: 28px; height: 28px; border-radius: var(--radius-sm); border: none; background: transparent; cursor: pointer; font-size: 18px; color: var(--slate-400); display: flex; align-items: center; justify-content: center; transition: background .12s, color .12s; }
.fme-dp-close:hover { background: #fee2e2; color: #991b1b; }

.fme-dp-body { overflow-y: auto; flex: 1; }
.fme-dp-body::-webkit-scrollbar { width: 4px; }
.fme-dp-body::-webkit-scrollbar-thumb { background: var(--slate-200); border-radius: 3px; }
.fme-dp-inner { padding: 18px; display: flex; flex-direction: column; gap: 16px; }

/* Profile row */
.fme-dp-profile {
    display: flex; align-items: center; gap: 12px;
    padding: 14px; background: var(--slate-50);
    border: 1px solid var(--slate-200); border-radius: var(--radius);
}
.fme-dp-avatar {
    width: 50px; height: 50px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 18px; color: #fff;
    border: 2.5px solid rgba(255,255,255,.9);
    box-shadow: 0 3px 12px rgba(0,0,0,.14); overflow: hidden; flex-shrink: 0;
}
.fme-dp-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.fme-dp-profile-info { flex: 1; min-width: 0; }
.fme-dp-name { font-size: 15px; font-weight: 800; color: var(--slate-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -.2px; }
.fme-dp-handle { font-size: 11px; color: var(--slate-400); font-weight: 500; margin-top: 1px; }
.fme-dp-badges { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; margin-top: 6px; }
.fme-dp-plat-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 3px;
    font-size: 10px; font-weight: 700;
}
.fme-dp-date-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 3px;
    background: var(--slate-100); border: 1px solid var(--slate-200);
    font-size: 10px; font-weight: 600; color: var(--slate-500);
}
.fme-dp-date-badge svg { width: 10px; height: 10px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }

/* Sentiment strip */
.fme-dp-sentiment {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid;
}
.fme-dp-sentiment--pos { background: #f0fdf4; border-color: #bbf7d0; }
.fme-dp-sentiment--neg { background: #fff5f5; border-color: #fecaca; }
.fme-dp-sentiment--neu { background: var(--slate-50); border-color: var(--slate-200); }
.fme-dp-sent-icon { width: 32px; height: 32px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px; }
.fme-dp-sent-label { font-size: 12px; font-weight: 700; }
.fme-dp-sent-label--pos { color: #15803d; }
.fme-dp-sent-label--neg { color: #b91c1c; }
.fme-dp-sent-label--neu { color: var(--slate-600); }
.fme-dp-sent-desc { font-size: 10px; font-weight: 500; color: var(--slate-400); margin-top: 1px; }

/* Metrics grid */
.fme-dp-metrics { display: grid; grid-template-columns: repeat(2,1fr); gap: 8px; }
.fme-dp-metric-card {
    background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-sm);
    padding: 12px 10px; display: flex; flex-direction: column;
    align-items: center; text-align: center; gap: 5px;
    transition: all .18s; cursor: default; position: relative; overflow: hidden;
}
.fme-dp-metric-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2.5px; opacity: 0; transition: opacity .2s; }
.fme-dp-metric-card:hover { transform: translateY(-2px); border-color: transparent; background: #fff; }
.fme-dp-metric-card:hover::before { opacity: 1; }
.fme-dp-metric-card--views::before  { background: linear-gradient(90deg,#EE1D52,#ff6b8a); }
.fme-dp-metric-card--likes::before  { background: linear-gradient(90deg,#10B981,#34d399); }
.fme-dp-metric-card--cmts::before   { background: linear-gradient(90deg,#F59E0B,#fbbf24); }
.fme-dp-metric-card--shares::before { background: linear-gradient(90deg,#06B6D4,#22d3ee); }
.fme-dp-metric-card:hover.fme-dp-metric-card--views  { box-shadow: 0 6px 20px rgba(238,29,82,.12); }
.fme-dp-metric-card:hover.fme-dp-metric-card--likes  { box-shadow: 0 6px 20px rgba(16,185,129,.12); }
.fme-dp-metric-card:hover.fme-dp-metric-card--cmts   { box-shadow: 0 6px 20px rgba(245,158,11,.12); }
.fme-dp-metric-card:hover.fme-dp-metric-card--shares { box-shadow: 0 6px 20px rgba(6,182,212,.12); }
.fme-dp-metric-icon { width: 30px; height: 30px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; }
.fme-dp-metric-icon svg { width: 14px; height: 14px; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.fme-dp-metric-icon--views  { background: rgba(238,29,82,.08); } .fme-dp-metric-icon--views svg  { stroke: #EE1D52; }
.fme-dp-metric-icon--likes  { background: rgba(16,185,129,.08); } .fme-dp-metric-icon--likes svg  { stroke: #10B981; }
.fme-dp-metric-icon--cmts   { background: rgba(245,158,11,.08); } .fme-dp-metric-icon--cmts svg   { stroke: #F59E0B; }
.fme-dp-metric-icon--shares { background: rgba(6,182,212,.08);  } .fme-dp-metric-icon--shares svg { stroke: #06B6D4; }
.fme-dp-metric-value { font-size: 18px; font-weight: 800; letter-spacing: -.5px; line-height: 1; }
.fme-dp-metric-value--views  { color: #EE1D52; }
.fme-dp-metric-value--likes  { color: #10B981; }
.fme-dp-metric-value--cmts   { color: #F59E0B; }
.fme-dp-metric-value--shares { color: #06B6D4; }
.fme-dp-metric-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--slate-400); }

/* Section heading inside detail */
.fme-dp-section { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
.fme-dp-section-icon { width: 22px; height: 22px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.fme-dp-section-icon svg { width: 11px; height: 11px; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
.fme-dp-section-title { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .6px; color: var(--slate-500); white-space: nowrap; }
.fme-dp-section-line { flex: 1; height: 1px; background: var(--slate-100); }

/* Description box */
.fme-dp-desc-box { background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-sm); padding: 12px 14px; }
.fme-dp-desc-text { font-size: 13px; color: var(--slate-700); line-height: 1.7; word-break: break-word; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }
.fme-dp-desc-text.expanded { display: block; }
.fme-dp-desc-toggle { margin-top: 8px; display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; color: var(--tiktok); cursor: pointer; border: none; background: transparent; font-family: 'Poppins', sans-serif; padding: 0; transition: opacity .14s; }
.fme-dp-desc-toggle:hover { opacity: .7; }
.fme-dp-desc-toggle svg { width: 10px; height: 10px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; transition: transform .2s; }
.fme-dp-desc-toggle.expanded svg { transform: rotate(180deg); }

/* Media embed */
.fme-dp-media { border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--slate-200); box-shadow: var(--shadow-sm); }
.fme-dp-media iframe { width: 100%; height: 260px; border: none; display: block; }
.fme-dp-media-label { display: flex; align-items: center; gap: 6px; padding: 8px 12px; background: var(--slate-50); border-top: 1px solid var(--slate-200); font-size: 10px; font-weight: 700; color: var(--slate-500); }
.fme-dp-media-label svg { width: 11px; height: 11px; fill: currentColor; flex-shrink: 0; }

/* CTA */
.fme-dp-cta {
    display: flex; align-items: center; justify-content: center; gap: 7px;
    padding: 11px 18px;
    background: linear-gradient(135deg,var(--tiktok),var(--tiktok-dark));
    color: #fff; border-radius: var(--radius-sm);
    font-size: 12px; font-weight: 700;
    text-decoration: none; transition: box-shadow .2s, transform .2s;
    box-shadow: 0 3px 12px rgba(238,29,82,.2); width: 100%;
}
.fme-dp-cta:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(238,29,82,.3); color: #fff; }
.fme-dp-cta svg { width: 12px; height: 12px; stroke: #fff; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

/* ═══════════════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════════════ */
@media (max-width: 768px) {
    .fme-tabs { flex-wrap: wrap; }
    .fme-tab-btn { flex: unset; min-width: calc(50% - 4px); }
    .fme-post-thumb { width: 80px; height: 110px; }
    .fme-dp-metrics { grid-template-columns: repeat(2,1fr); }
}
@media (max-width: 480px) {
    .fme-post-thumb { display: none; }
    .fme-drawer { width: 100vw; }
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

<div class="fme-page">

    {{-- PAGE HEADER --}}
    <div class="fme-page-header">
        <div class="fme-page-header-left">
            <h1>TikTok Most Engagement</h1>
            <p>Top videos ranked by views, likes, comments &amp; shares</p>
        </div>
        <button class="fme-refresh-btn" onclick="FME.reload()">
            <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            Refresh
        </button>
    </div>

    {{-- FILTER CARD + DATE PICKER --}}
    @include('mk.layouts.partials.filter-datepicker')

    {{-- KPI CARDS --}}
    <div class="fme-kpi-grid">
        <div class="fme-kpi fme-kpi--tiktok">
            <div class="fme-kpi-icon"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
            <div class="fme-kpi-label">Total Views</div>
            <div class="fme-kpi-value" id="valTotalViews"><div class="sk-block" style="height:28px;width:100px;border-radius:4px;"></div></div>
            <div class="fme-kpi-sub">Dari semua video</div>
            <i class="ph ph-eye fme-kpi-wm"></i>
        </div>
        <div class="fme-kpi fme-kpi--green">
            <div class="fme-kpi-icon"><svg viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg></div>
            <div class="fme-kpi-label">Total Likes</div>
            <div class="fme-kpi-value" id="valTotalLikes"><div class="sk-block" style="height:28px;width:100px;border-radius:4px;"></div></div>
            <div class="fme-kpi-sub">Apresiasi konten</div>
            <i class="ph ph-thumbs-up fme-kpi-wm"></i>
        </div>
        <div class="fme-kpi fme-kpi--amber">
            <div class="fme-kpi-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
            <div class="fme-kpi-label">Total Comments</div>
            <div class="fme-kpi-value" id="valTotalCmts"><div class="sk-block" style="height:28px;width:100px;border-radius:4px;"></div></div>
            <div class="fme-kpi-sub">Interaksi komentar</div>
            <i class="ph ph-chat-circle fme-kpi-wm"></i>
        </div>
        <div class="fme-kpi fme-kpi--cyan">
            <div class="fme-kpi-icon"><svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></div>
            <div class="fme-kpi-label">Total Shares</div>
            <div class="fme-kpi-value" id="valTotalShares"><div class="sk-block" style="height:28px;width:100px;border-radius:4px;"></div></div>
            <div class="fme-kpi-sub">Video dibagikan</div>
            <i class="ph ph-share-network fme-kpi-wm"></i>
        </div>
    </div>

    {{-- DONUT DISTRIBUTION --}}
    <div class="fme-section-header" id="donutSectionHead">
        <div class="fme-section-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg></div>
        <span class="fme-section-title" id="donutSectionLabel">Distribusi Views — Top 5</span>
        <div class="fme-section-line"></div>
    </div>
    <div class="fme-card fme-card--tiktok" id="donutMasterCard" style="margin-bottom:18px;">
        <div class="fme-card-header">
            <div class="fme-card-header-row">
                <div class="fme-card-header-left">
                    <div class="fme-card-icon fme-card-icon--tiktok" id="donutMasterIco">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
                    </div>
                    <div>
                        <div class="fme-card-title" id="donutMasterTitle">Top 5 Most Viewed — Distribution</div>
                        <div class="fme-card-subtitle">Proporsi engagement per video — hover segmen untuk detail</div>
                    </div>
                </div>
                <div id="donutMasterLegend" class="fme-donut-legend"></div>
            </div>
        </div>
        <div class="fme-card-body">
            <div id="donutViewWrap">
                <div class="sk-block" id="donutViewSkel" style="height:460px;border-radius:6px;"></div>
                <div id="donutViewChart" style="width:100%;height:460px;display:none;"></div>
                <div id="donutViewEmpty" style="display:none;"><div class="fme-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg><span class="fme-empty-text">Tidak ada data</span></div></div>
            </div>
            <div id="donutLikeWrap" style="display:none;"><div class="sk-block" id="donutLikeSkel" style="height:460px;border-radius:6px;"></div><div id="donutLikeChart" style="width:100%;height:460px;display:none;"></div><div id="donutLikeEmpty" style="display:none;"><div class="fme-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg><span class="fme-empty-text">Tidak ada data</span></div></div></div>
            <div id="donutCommentWrap" style="display:none;"><div class="sk-block" id="donutCommentSkel" style="height:460px;border-radius:6px;"></div><div id="donutCommentChart" style="width:100%;height:460px;display:none;"></div><div id="donutCommentEmpty" style="display:none;"><div class="fme-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg><span class="fme-empty-text">Tidak ada data</span></div></div></div>
            <div id="donutShareWrap" style="display:none;"><div class="sk-block" id="donutShareSkel" style="height:460px;border-radius:6px;"></div><div id="donutShareChart" style="width:100%;height:460px;display:none;"></div><div id="donutShareEmpty" style="display:none;"><div class="fme-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg><span class="fme-empty-text">Tidak ada data</span></div></div></div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="fme-tabs">
        <button class="fme-tab-btn active active--tiktok" id="tab-view" onclick="FMETab.show('view')">
            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            Most Viewed <span class="fme-tab-chip" id="chip-view">—</span>
        </button>
        <button class="fme-tab-btn" id="tab-like" onclick="FMETab.show('like')">
            <svg viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
            Most Liked <span class="fme-tab-chip" id="chip-like">—</span>
        </button>
        <button class="fme-tab-btn" id="tab-comment" onclick="FMETab.show('comment')">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Most Comments <span class="fme-tab-chip" id="chip-comment">—</span>
        </button>
        <button class="fme-tab-btn" id="tab-share" onclick="FMETab.show('share')">
            <svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
            Most Shares <span class="fme-tab-chip" id="chip-share">—</span>
        </button>
    </div>

    {{-- ══ VIEW PANEL ══ --}}
    <div class="fme-tab-panel active" id="panel-view">
        <div class="fme-card fme-card--tiktok">
            <div class="fme-card-header">
                <div class="fme-card-header-row">
                    <div class="fme-card-header-left">
                        <div class="fme-card-icon fme-card-icon--tiktok"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
                        <div><div class="fme-card-title">Top Videos by Views</div><div class="fme-card-subtitle">Video TikTok dengan paling banyak views — klik untuk detail</div></div>
                    </div>
                    <div class="fme-card-actions">
                        <select class="fme-rows-sel" id="rows-view" onchange="FMEData.reloadTab('view')"><option value="10">Top 10</option><option value="20">Top 20</option><option value="50">Top 50</option><option value="100" selected>Top 100</option></select>
                        <button class="fme-btn-icon" onclick="FMEData.exportCsv('view')" title="Export CSV"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></button>
                        <span class="fme-badge fme-badge--tiktok" id="badge-view-full">Loading…</span>
                    </div>
                </div>
            </div>
            <div id="list-view" class="fme-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data views…</div></div>
            <div id="pag-view"></div>
        </div>
        <div class="fme-card fme-card--tiktok">
            <div class="fme-card-header">
                <div class="fme-card-header-row">
                    <div class="fme-card-header-left">
                        <div class="fme-card-icon fme-card-icon--tiktok"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
                        <div><div class="fme-card-title">Views Chart</div><div class="fme-card-subtitle">Top 10 video berdasarkan views</div></div>
                    </div>
                    <span class="fme-badge fme-badge--tiktok">Top 10</span>
                </div>
            </div>
            <div class="fme-card-body"><div class="fme-ch-320"><div id="ch-view" style="width:100%;height:100%;"></div><div class="sk-block" id="sk-view" style="position:absolute;inset:0;border-radius:6px;"></div></div></div>
        </div>

        <div class="fme-section-header">
            <div class="fme-section-icon" style="background:rgba(30,41,59,.06);"><svg viewBox="0 0 24 24" style="stroke:var(--slate-700);"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div>
            <span class="fme-section-title">Perbandingan Engagement — Top 10</span>
            <div class="fme-section-line"></div>
        </div>
        <div class="fme-card">
            <div class="fme-card-header">
                <div class="fme-card-header-row">
                    <div class="fme-card-header-left">
                        <div class="fme-card-icon fme-card-icon--ink"><svg viewBox="0 0 24 24"><rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/></svg></div>
                        <div><div class="fme-card-title">View vs Like vs Comment vs Share — Top 10</div><div class="fme-card-subtitle">Perbandingan engagement keseluruhan pada video terpopuler</div></div>
                    </div>
                    <div class="fme-toggle-group" id="engTypeToggle">
                        <button class="fme-toggle-btn active" data-type="stacked" onclick="FMEChart.setEngType('stacked')">Stacked</button>
                        <button class="fme-toggle-btn" data-type="grouped" onclick="FMEChart.setEngType('grouped')">Grouped</button>
                    </div>
                </div>
            </div>
            <div class="fme-card-body"><div class="fme-ch-320"><div id="ch-eng" style="width:100%;height:100%;"></div><div class="sk-block" id="sk-eng" style="position:absolute;inset:0;border-radius:6px;"></div></div></div>
        </div>
    </div>

    {{-- ══ LIKE PANEL ══ --}}
    <div class="fme-tab-panel" id="panel-like">
        <div class="fme-card fme-card--green">
            <div class="fme-card-header">
                <div class="fme-card-header-row">
                    <div class="fme-card-header-left">
                        <div class="fme-card-icon fme-card-icon--green"><svg viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg></div>
                        <div><div class="fme-card-title">Top Videos by Likes</div><div class="fme-card-subtitle">Video TikTok dengan paling banyak likes — klik untuk detail</div></div>
                    </div>
                    <div class="fme-card-actions">
                        <select class="fme-rows-sel" id="rows-like" onchange="FMEData.reloadTab('like')"><option value="10">Top 10</option><option value="20">Top 20</option><option value="50">Top 50</option><option value="100" selected>Top 100</option></select>
                        <button class="fme-btn-icon" onclick="FMEData.exportCsv('like')" title="Export CSV"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></button>
                        <span class="fme-badge fme-badge--green" id="badge-like-full">Loading…</span>
                    </div>
                </div>
            </div>
            <div id="list-like" class="fme-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data likes…</div></div>
            <div id="pag-like"></div>
        </div>
        <div class="fme-card fme-card--green">
            <div class="fme-card-header">
                <div class="fme-card-header-row">
                    <div class="fme-card-header-left">
                        <div class="fme-card-icon fme-card-icon--green"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
                        <div><div class="fme-card-title">Likes Chart</div><div class="fme-card-subtitle">Top 10 video berdasarkan likes</div></div>
                    </div>
                    <span class="fme-badge fme-badge--green">Top 10</span>
                </div>
            </div>
            <div class="fme-card-body"><div class="fme-ch-320"><div id="ch-like" style="width:100%;height:100%;"></div><div class="sk-block" id="sk-like" style="position:absolute;inset:0;border-radius:6px;"></div></div></div>
        </div>
    </div>

    {{-- ══ COMMENT PANEL ══ --}}
    <div class="fme-tab-panel" id="panel-comment">
        <div class="fme-card fme-card--amber">
            <div class="fme-card-header">
                <div class="fme-card-header-row">
                    <div class="fme-card-header-left">
                        <div class="fme-card-icon fme-card-icon--amber"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
                        <div><div class="fme-card-title">Top Videos by Comments</div><div class="fme-card-subtitle">Video TikTok dengan paling banyak komentar — klik untuk detail</div></div>
                    </div>
                    <div class="fme-card-actions">
                        <select class="fme-rows-sel" id="rows-comment" onchange="FMEData.reloadTab('comment')"><option value="10">Top 10</option><option value="20">Top 20</option><option value="50">Top 50</option><option value="100" selected>Top 100</option></select>
                        <button class="fme-btn-icon" onclick="FMEData.exportCsv('comment')" title="Export CSV"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></button>
                        <span class="fme-badge fme-badge--amber" id="badge-comment-full">Loading…</span>
                    </div>
                </div>
            </div>
            <div id="list-comment" class="fme-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data comments…</div></div>
            <div id="pag-comment"></div>
        </div>
        <div class="fme-card fme-card--amber">
            <div class="fme-card-header">
                <div class="fme-card-header-row">
                    <div class="fme-card-header-left">
                        <div class="fme-card-icon fme-card-icon--amber"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
                        <div><div class="fme-card-title">Comments Chart</div><div class="fme-card-subtitle">Top 10 video berdasarkan komentar</div></div>
                    </div>
                    <span class="fme-badge fme-badge--amber">Top 10</span>
                </div>
            </div>
            <div class="fme-card-body"><div class="fme-ch-320"><div id="ch-comment" style="width:100%;height:100%;"></div><div class="sk-block" id="sk-comment" style="position:absolute;inset:0;border-radius:6px;"></div></div></div>
        </div>
    </div>

    {{-- ══ SHARE PANEL ══ --}}
    <div class="fme-tab-panel" id="panel-share">
        <div class="fme-card fme-card--cyan">
            <div class="fme-card-header">
                <div class="fme-card-header-row">
                    <div class="fme-card-header-left">
                        <div class="fme-card-icon fme-card-icon--cyan"><svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></div>
                        <div><div class="fme-card-title">Top Videos by Shares</div><div class="fme-card-subtitle">Video TikTok yang paling banyak dibagikan — klik untuk detail</div></div>
                    </div>
                    <div class="fme-card-actions">
                        <select class="fme-rows-sel" id="rows-share" onchange="FMEData.reloadTab('share')"><option value="10">Top 10</option><option value="20">Top 20</option><option value="50">Top 50</option><option value="100" selected>Top 100</option></select>
                        <button class="fme-btn-icon" onclick="FMEData.exportCsv('share')" title="Export CSV"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></button>
                        <span class="fme-badge fme-badge--cyan" id="badge-share-full">Loading…</span>
                    </div>
                </div>
            </div>
            <div id="list-share" class="fme-card-body--flush"><div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data shares…</div></div>
            <div id="pag-share"></div>
        </div>
        <div class="fme-card fme-card--cyan">
            <div class="fme-card-header">
                <div class="fme-card-header-row">
                    <div class="fme-card-header-left">
                        <div class="fme-card-icon fme-card-icon--cyan"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div>
                        <div><div class="fme-card-title">Shares Chart</div><div class="fme-card-subtitle">Top 10 video berdasarkan shares</div></div>
                    </div>
                    <span class="fme-badge fme-badge--cyan">Top 10</span>
                </div>
            </div>
            <div class="fme-card-body"><div class="fme-ch-320"><div id="ch-share" style="width:100%;height:100%;"></div><div class="sk-block" id="sk-share" style="position:absolute;inset:0;border-radius:6px;"></div></div></div>
        </div>
    </div>

</div>{{-- /fme-page --}}

{{-- ══ SLIDE-IN DRAWER (right side) ══ --}}
<div class="fme-drawer-backdrop" id="fmeBackdrop" onclick="FMEDrawer.close()"></div>
<div class="fme-drawer" id="fmeDrawer">
    <div class="fme-drawer-header">
        <div class="fme-drawer-dot" id="drawerDot"></div>
        <span class="fme-drawer-title" id="drawerTitle">Video Detail</span>
        <span class="fme-drawer-count" id="drawerCount">—</span>
        <button class="fme-drawer-close" onclick="FMEDrawer.close()">×</button>
    </div>
    <div class="fme-drawer-toolbar">
        <span class="fme-drawer-meta" id="drawerMeta">—</span>
        <button class="fme-drawer-export" onclick="FMEDrawer.exportCsv()">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export CSV
        </button>
    </div>
    <div class="fme-drawer-list" id="drawerList"></div>

    {{-- Detail panel slides in over list --}}
    <div class="fme-detail-panel" id="fmeDetailPanel">
        <div class="fme-dp-header">
            <button class="fme-dp-back" onclick="FMEDetail.close()">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <span class="fme-dp-title" id="dpTitle">Detail Video</span>
            <button class="fme-dp-close" onclick="FMEDrawer.close()">×</button>
        </div>
        <div class="fme-dp-body" id="dpBody"></div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
'use strict';

/* ══ CONFIG ══ */
const FMECfg = {
    pid: {{ $projectId ? (int)$projectId : 'null' }},
    sd: '{{ $startDate }}',
    ed: '{{ $endDate }}',
    colors: { view:'#EE1D52', like:'#10b981', comment:'#f59e0b', share:'#06b6d4' },
    perPage: 10
};
const DONUT_COLORS = ['#2FC6F6','#f59e0b','#10b981','#8b5cf6','#f43f5e'];

/* ══ UTILS ══ */
const numFmt = n => parseInt(n||0).toLocaleString('id-ID');
const numK   = n => { n=parseInt(n||0); return n>=1e6?(n/1e6).toFixed(1)+'M':n>=1000?(n/1000).toFixed(1)+'k':String(n); };
const esc    = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const hideSk = id => { const e=document.getElementById(id); if(e) e.style.display='none'; };
const emptyH = msg => `<div class="fme-empty"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span class="fme-empty-text">${msg}</span></div>`;
const decodeStr = s => { if(!s) return ''; try { const f=decodeURIComponent(escape(s)); if(!f.includes('\uFFFD')&&f!==s) return f; } catch(e){} return s; };

/* ══ CHARTS ══ */
const FMECharts = {
    _i:{},
    make(id) { if(this._i[id]) { try{this._i[id].dispose();}catch(e){} } const dom=document.getElementById(id); if(!dom) return null; const c=echarts.init(dom,null,{renderer:'canvas'}); this._i[id]=c; return c; },
    disposeAll() { Object.values(this._i).forEach(c=>{try{c.dispose();}catch(e){}}); this._i={}; }
};
window.addEventListener('resize', ()=>{ Object.values(FMECharts._i).forEach(c=>{try{if(!c.isDisposed())c.resize();}catch(e){}}); });

const EC_TT = { confine:true, backgroundColor:'#1e293b', borderColor:'#334155', borderWidth:1, padding:[10,14], textStyle:{color:'#fff',fontFamily:"'Poppins',sans-serif",fontSize:12}, extraCssText:'border-radius:6px;box-shadow:0 6px 20px rgba(0,0,0,.25);' };

/* ══ STORE & PAGINATION ══ */
const Store = { view:[], like:[], comment:[], share:[] };
const Pag   = { view:1, like:1, comment:1, share:1 };

const tabColorMap = { view:'tiktok', like:'green', comment:'amber', share:'cyan' };
const tabTitleMap = { view:'Top 5 Most Viewed — Distribution', like:'Top 5 Most Liked — Distribution', comment:'Top 5 Most Comments — Distribution', share:'Top 5 Most Shared — Distribution' };
const tabLabelMap = { view:'Distribusi Views — Top 5', like:'Distribusi Likes — Top 5', comment:'Distribusi Comments — Top 5', share:'Distribusi Shares — Top 5' };

/* ══ TABS ══ */
const FMETab = {
    _loaded:{ view:false, like:false, comment:false, share:false },
    show(type) {
        ['view','like','comment','share'].forEach(t => {
            const tb=document.getElementById('tab-'+t), panel=document.getElementById('panel-'+t);
            const cap=t.charAt(0).toUpperCase()+t.slice(1);
            const wrap=document.getElementById('donut'+cap+'Wrap');
            const isThis=t===type;
            if(tb) { tb.classList.toggle('active',isThis); tb.classList.remove('active--tiktok','active--green','active--amber','active--cyan'); if(isThis) tb.classList.add('active--'+tabColorMap[t]); }
            if(panel) panel.classList.toggle('active',isThis);
            if(wrap) wrap.style.display=isThis?'block':'none';
        });
        const col=tabColorMap[type];
        const card=document.getElementById('donutMasterCard'); if(card) card.className='fme-card fme-card--'+col;
        const ico=document.getElementById('donutMasterIco'); if(ico) ico.className='fme-card-icon fme-card-icon--'+col;
        const ttl=document.getElementById('donutMasterTitle'); if(ttl) ttl.textContent=tabTitleMap[type];
        const lbl=document.getElementById('donutSectionLabel'); if(lbl) lbl.textContent=tabLabelMap[type];
        const masterLeg=document.getElementById('donutMasterLegend');
        const srcLeg=document.getElementById('legendSrc-'+type);
        if(masterLeg) masterLeg.innerHTML=srcLeg?srcLeg.innerHTML:'';
        if(!this._loaded[type]) { this._loaded[type]=true; FMEData.loadTab(type); }
        else { requestAnimationFrame(()=>{ ['ch-view','ch-like','ch-comment','ch-share','ch-eng'].forEach(id=>{ const c=FMECharts._i[id]; try{if(c&&!c.isDisposed())c.resize();}catch(e){} }); }); }
    },
    reset() { this._loaded={view:false,like:false,comment:false,share:false}; }
};

/* ══ DATA ══ */
const FMEData = {
    async loadAll() {
        if(!FMECfg.pid) {
            ['valTotalViews','valTotalLikes','valTotalCmts','valTotalShares'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML='<span style="font-size:13px;color:#94a3b8;">—</span>';});
            ['list-view','list-like','list-comment','list-share'].forEach(id=>{const e=document.getElementById(id);if(e)e.innerHTML=emptyH('Pilih project terlebih dahulu');});
            ['sk-view','sk-like','sk-comment','sk-share','sk-eng','donutViewSkel','donutLikeSkel','donutCommentSkel','donutShareSkel'].forEach(hideSk);
            return;
        }
        FMETab._loaded.view=true;
        await this.loadTab('view');
    },

    async loadTab(type) {
        const subMap={ view:'postbyview', like:'postbylike', comment:'postbycomment', share:'postbyshare' };
        const rows=parseInt(document.getElementById('rows-'+type)?.value||'20');
        const listEl=document.getElementById('list-'+type);
        const chipEl=document.getElementById('chip-'+type);
        const badgeEl=document.getElementById('badge-'+type+'-full');
        if(listEl) listEl.innerHTML=`<div class="fme-spinner-state"><div class="fme-spinner"></div>Memuat data ${type}…</div>`;
        try {
            const res=await fetch(`/mk/api/tiktok/most-engagement?project_id=${FMECfg.pid}&start_date=${FMECfg.sd}&end_date=${FMECfg.ed}&sub=${subMap[type]}&rows=${rows}`);
            const json=await res.json();
            let items=json.data||json||[]; if(!Array.isArray(items)) items=[];
            Store[type]=items; Pag[type]=1;
            if(chipEl) chipEl.textContent=items.length;
            if(badgeEl) badgeEl.textContent=`${items.length} videos`;
            if(type==='view') { this._updateStats(items); this._renderEngChart(items); }
            this._renderList(type);
            this._renderBar(type,items.slice(0,10));
            this._renderDonut(type,items);
        } catch(err) {
            console.error(err);
            if(listEl) listEl.innerHTML=emptyH('Gagal memuat data: '+err.message);
            if(chipEl) chipEl.textContent='!';
            if(badgeEl) badgeEl.textContent='Error';
            const cap=type.charAt(0).toUpperCase()+type.slice(1);
            ['sk-'+type,'donut'+cap+'Skel'].forEach(hideSk);
        }
    },

    reloadTab(type) { Store[type]=[]; Pag[type]=1; this.loadTab(type); },

    _metric(item,type) {
        const keys={ view:['view_cnt','views','freq'], like:['likes','num_likes'], comment:['comments','num_comments'], share:['shares','num_shares'] };
        return (keys[type]||['view_cnt']).reduce((v,k)=>v||parseInt(item[k]||0),0);
    },

    _updateStats(items) {
        let tV=0,tL=0,tC=0,tS=0;
        items.forEach(i=>{ tV+=parseInt(i.view_cnt||i.views||i.freq||0); tL+=parseInt(i.likes||i.num_likes||0); tC+=parseInt(i.comments||i.num_comments||0); tS+=parseInt(i.shares||i.num_shares||0); });
        const eV=document.getElementById('valTotalViews'); if(eV) eV.textContent=numFmt(tV);
        const eL=document.getElementById('valTotalLikes'); if(eL) eL.textContent=numFmt(tL);
        const eC=document.getElementById('valTotalCmts'); if(eC) eC.textContent=numFmt(tC);
        const eS=document.getElementById('valTotalShares'); if(eS) eS.textContent=numFmt(tS);
    },

    _renderList(type) {
        const items=Store[type], listEl=document.getElementById('list-'+type), pagEl=document.getElementById('pag-'+type);
        if(!listEl) return;
        if(!items.length) { listEl.innerHTML=emptyH('Tidak ada data untuk periode ini'); if(pagEl) pagEl.innerHTML=''; return; }
        const page=Pag[type]||1, total=items.length, perPage=FMECfg.perPage, pages=Math.ceil(total/perPage), start=(page-1)*perPage;
        listEl.innerHTML=`<div class="fme-post-list">${items.slice(start,start+perPage).map((item,i)=>this._postHTML(item,start+i,type)).join('')}</div>`;
        if(pagEl) pagEl.innerHTML=this._pagHTML(type,page,pages,total,start+1,Math.min(start+perPage,total));
        listEl.querySelectorAll('.fme-post').forEach(el=>{
            el.addEventListener('click',()=>{
                try {
                    const item=JSON.parse(decodeURIComponent(el.dataset.item));
                    const lm={view:'Most Viewed Videos',like:'Most Liked Videos',comment:'Most Comments Videos',share:'Most Shared Videos'};
                    FMEDrawer.open(items,type,lm[type],items.length);
                    FMEDetail.open(item,type);
                } catch(e){ console.warn(e); }
            });
        });
    },

    _pagHTML(type,page,pages,total,from,to) {
        if(pages<=1) return '';
        let btns=''; const r=2;
        btns+=`<button class="fme-pag-btn" ${page<=1?'disabled':''} onclick="FMEData.goPage('${type}',${page-1})"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>`;
        for(let i=1;i<=pages;i++) {
            if(i===1||i===pages||(i>=page-r&&i<=page+r)) btns+=`<button class="fme-pag-btn${i===page?' is-active':''}" onclick="FMEData.goPage('${type}',${i})">${i}</button>`;
            else if(i===page-r-1||i===page+r+1) btns+=`<span class="fme-pag-btn" style="cursor:default;opacity:.4;">…</span>`;
        }
        btns+=`<button class="fme-pag-btn" ${page>=pages?'disabled':''} onclick="FMEData.goPage('${type}',${page+1})"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>`;
        return `<div class="fme-pagination"><span class="fme-pag-info">Menampilkan ${from}–${to} dari ${total} videos</span><div class="fme-pag-controls">${btns}</div></div>`;
    },

    goPage(type,page) { Pag[type]=page; this._renderList(type); const el=document.getElementById('list-'+type); if(el) el.scrollIntoView({behavior:'smooth',block:'nearest'}); },

    _getName(item) { const n=(item.name||item.author_scr_name||item.author_name||'').replace(/<[^>]*>/g,'').trim(); if(n&&n!=='TikTok Creator') return n; return item.author_id?'@'+item.author_id:'TikTok Creator'; },
    _getAvatar(item) { return (item.avatar_url||item.author_avatar||item.profile_image||item.author_image||item.profile_picture||'').trim(); },
    _getThumbnail(item) { return (item.avatar_url||item.profile_url||item.author_avatar||item.profile_image||item.author_image||item.profile_picture||item.thumbnail_url||item.cover_url||item.video_cover||item.thumbnail||item.image||item.media_url||'').trim(); },
    _getInitials(name) { if(!name||name==='TikTok Creator') return 'TT'; const w=name.replace(/[^a-zA-Z0-9\s@]/g,'').replace('@','').split(/\s+/).filter(Boolean); if(w.length>=2) return (w[0][0]+w[1][0]).toUpperCase(); return (w[0]?.[0]||'T').toUpperCase(); },
    _getAvatarColor(item) { const seed=item.author_id||item.id||this._getName(item)||'tt'; const colors=['#EE1D52','#c4163f','#10b981','#f59e0b','#8b5cf6','#06b6d4','#ec4899','#14b8a6','#f97316','#6366f1']; let h=0; for(let i=0;i<seed.length;i++) h=(h*31+seed.charCodeAt(i))&0xffffffff; return colors[Math.abs(h)%colors.length]; },
    _avatarHtml(item) {
        const name=this._getName(item), av=this._getAvatar(item), ini=this._getInitials(name);
        const safeIni=ini.replace(/['"\\]/g,'');
        if(av&&av.startsWith('http')) return `<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`;
        return ini;
    },

    _postHTML(item,globalIdx,type) {
        const rank=globalIdx+1, rkCls=rank===1?'--1':rank===2?'--2':rank===3?'--3':'';
        const name=this._getName(item);
        const avColor=this._getAvatarColor(item);
        const avHtml=this._avatarHtml(item);
        const content=decodeStr((item.content||item.caption||'').replace(/<[^>]*>/g,'').replace(/\s+/g,' ').trim()).slice(0,200);
        const dt=(item.date_created||'').split('T')[0];
        const url=item.url||item.link||'';
        const thumb=this._getThumbnail(item);
        const views=parseInt(item.view_cnt||item.views||item.freq||0);
        const likes=parseInt(item.likes||item.num_likes||0);
        const cmts=parseInt(item.comments||item.num_comments||0);
        const shares=parseInt(item.shares||item.num_shares||0);
        const totalEng=views+likes+cmts+shares;
        const sentRaw=String(item.sentiment_str||item.sentiment||'').toLowerCase();
        const sent=sentRaw.includes('pos')?'pos':sentRaw.includes('neg')?'neg':'neu';
        const sentLbl=sent==='pos'?'Positive':sent==='neg'?'Negative':'Neutral';
        const vV=type==='view'?' fme-metric--tiktok':'';
        const lV=type==='like'?' fme-metric--green':'';
        const cV=type==='comment'?' fme-metric--amber':'';
        const sV=type==='share'?' fme-metric--cyan':'';
        const itemEnc=encodeURIComponent(JSON.stringify(item));

        let thumbHtml='';
        if(thumb&&thumb.startsWith('http')) {
            thumbHtml=`<div class="fme-post-thumb"><img src="${esc(thumb)}" alt="thumb" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'fme-post-thumb-ph\\'>🎵</div>'"><div class="fme-post-thumb-play"><svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg></div></div>`;
        } else {
            thumbHtml=`<div class="fme-post-thumb"><div class="fme-post-thumb-ph">🎵</div></div>`;
        }

        return `<div class="fme-post" data-item="${esc(itemEnc)}">
            <div class="fme-post-rank fme-post-rank${rkCls}">${rank}</div>
            <div class="fme-post-av" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div>
            <div class="fme-post-body">
                <div class="fme-post-author">${esc(name)}</div>
                ${dt?`<div class="fme-post-date">${dt}</div>`:''}
                ${content?`<div class="fme-post-text">${esc(content)}</div>`:''}
                <div class="fme-post-stats">
                    <span class="fme-metric${vV}"><svg viewBox="0 0 24 24" stroke="${type==='view'?'#EE1D52':'currentColor'}"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>${numFmt(views)}</span>
                    <span class="fme-metric${lV}"><svg viewBox="0 0 24 24" stroke="${type==='like'?'#10b981':'currentColor'}"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>${numFmt(likes)}</span>
                    <span class="fme-metric${cV}"><svg viewBox="0 0 24 24" stroke="${type==='comment'?'#f59e0b':'currentColor'}"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>${numFmt(cmts)}</span>
                    <span class="fme-metric${sV}"><svg viewBox="0 0 24 24" stroke="${type==='share'?'#06b6d4':'currentColor'}"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>${numFmt(shares)}</span>
                    <span class="fme-metric" style="font-weight:800;">∑ ${numFmt(totalEng)}</span>
                    <span class="fme-sent fme-sent--${sent}">${sentLbl}</span>
                    ${url?`<a href="${esc(url)}" target="_blank" rel="noopener" class="fme-view-link" onclick="event.stopPropagation()"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Lihat</a>`:''}
                </div>
            </div>
            ${thumbHtml}
        </div>`;
    },

    _renderBar(type,items) {
        hideSk('sk-'+type);
        if(!items.length) return;
        const color=FMECfg.colors[type];
        const metricLbl={view:'Views',like:'Likes',comment:'Comments',share:'Shares'}[type];
        const labels=items.map(it=>{ const n=this._getName(it); return n.length>16?n.slice(0,15)+'…':n; });
        const values=items.map(it=>this._metric(it,type));
        const chart=FMECharts.make('ch-'+type); if(!chart) return;
        chart.setOption({
            animation:true, animationDuration:700, animationEasing:'elasticOut', backgroundColor:'#ffffff',
            tooltip:{...EC_TT, trigger:'axis', axisPointer:{type:'shadow',shadowStyle:{color:'rgba(67,97,238,.03)'}},
                formatter:p=>{ const it=items[p[0]?.dataIndex]; const name=it?this._getName(it):(p[0]?.name||''); return `<div style="min-width:180px;"><div style="font-weight:700;font-size:12px;margin-bottom:5px;padding-bottom:5px;border-bottom:1px solid rgba(255,255,255,.1);">${esc(name)}</div><div style="display:flex;justify-content:space-between;gap:10px;"><span style="color:#94a3b8;font-size:11px;">${metricLbl}</span><span style="font-weight:700;">${numFmt(p[0].value)}</span></div></div>`; }
            },
            grid:{top:12,right:16,bottom:48,left:14,containLabel:true},
            xAxis:{type:'category',data:labels,axisLine:{show:false},axisTick:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'600',color:'#94a3b8',interval:0,rotate:labels.length>6?30:0}},
            yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},
            series:[{type:'bar',data:values.map(v=>({value:v,itemStyle:{color:{type:'linear',x:0,y:0,x2:0,y2:1,colorStops:[{offset:0,color:color},{offset:1,color:color+'44'}]},borderRadius:[5,5,0,0]},emphasis:{itemStyle:{color,shadowBlur:10,shadowColor:color+'44'}}})),barMaxWidth:40,label:{show:true,position:'top',fontFamily:"'Poppins',sans-serif",fontWeight:'700',fontSize:10,color:'#94a3b8',formatter:p=>numK(p.value)}}]
        });
        chart.on('click',params=>{ const item=items[params.dataIndex]; if(!item) return; const lm={view:'Most Viewed Videos',like:'Most Liked Videos',comment:'Most Comments Videos',share:'Most Shared Videos'}; FMEDrawer.open(Store[type].length?Store[type]:items,type,lm[type],Store[type].length||items.length); FMEDetail.open(item,type); });
    },

    _renderDonut(type,items) {
        const cap=type.charAt(0).toUpperCase()+type.slice(1);
        const skel=document.getElementById('donut'+cap+'Skel'), chartEl=document.getElementById('donut'+cap+'Chart'), emptyEl=document.getElementById('donut'+cap+'Empty');
        const metricLbl={view:'Views',like:'Likes',comment:'Comments',share:'Shares'}[type];
        if(!items.length) { if(skel)skel.style.display='none'; if(emptyEl)emptyEl.style.display='block'; return; }
        const top5=items.slice(0,5);
        const total=top5.reduce((s,it)=>s+this._metric(it,type),0);
        const legendHTML=top5.map((it,i)=>{ const n=this._getName(it); const sn=n.length>20?n.slice(0,19)+'…':n; return `<div class="fme-donut-leg-item" onclick="FMEDonut.highlight('${type}',${i})"><span class="fme-donut-dot" style="background:${DONUT_COLORS[i]};"></span>${sn} · ${numFmt(this._metric(it,type))}</div>`; }).join('');
        let srcLeg=document.getElementById('legendSrc-'+type); if(!srcLeg){ srcLeg=document.createElement('div'); srcLeg.id='legendSrc-'+type; srcLeg.style.display='none'; document.body.appendChild(srcLeg); } srcLeg.innerHTML=legendHTML;
        const activeTab=['view','like','comment','share'].find(t=>document.getElementById('tab-'+t)?.classList.contains('active'))||'view';
        const masterLeg=document.getElementById('donutMasterLegend'); if(masterLeg&&activeTab===type) masterLeg.innerHTML=legendHTML;
        if(skel) skel.style.display='none'; if(chartEl) chartEl.style.display='block';
        const chart=FMECharts.make('donut'+cap+'Chart'); if(!chart) return;
        const _self=this;
        const pieData=top5.map((it,i)=>{
            const name=this._getName(it), val=this._metric(it,type);
            const rawContent=(it.content||it.caption||'').replace(/<[^>]*>/g,'').trim();
            const content=decodeStr(rawContent);
            const sentRaw=String(it.sentiment_str||it.sentiment||'').toLowerCase();
            const sentiment=sentRaw.includes('pos')?'Positive':sentRaw.includes('neg')?'Negative':'Neutral';
            return {name,value:val,content,sentiment,color:DONUT_COLORS[i],itemStyle:{color:DONUT_COLORS[i],borderColor:'#fff',borderWidth:3}};
        });
        chart.setOption({
            animation:true, animationDuration:900, animationEasing:'cubicOut', backgroundColor:'#ffffff',
            tooltip:{show:false}, legend:{show:false},
            series:[{
                type:'pie', radius:['36%','54%'], center:['50%','50%'], avoidLabelOverlap:true, minAngle:8, padAngle:1.5,
                itemStyle:{borderColor:'#ffffff',borderWidth:3},
                label:{show:true,position:'outside',alignTo:'edge',edgeDistance:14,lineHeight:16,fontFamily:"'Poppins',sans-serif",fontSize:10.5,color:'#64748b',fontWeight:'500',
                    formatter:function(p){const d=p.data,pct=total>0?((d.value/total)*100).toFixed(1):'0';const nm=d.name.length>28?d.name.slice(0,27)+'…':d.name;const snip=d.content?(d.content.length>38?d.content.slice(0,37)+'…':d.content):'';const line2=snip?`{snip|${snip}}\n`:'';return `{name|${nm}}\n${line2}{pct|${numFmt(d.value)}  ·  ${pct}%}`;},
                    rich:{name:{fontSize:10.5,fontWeight:'700',color:'#1e293b',lineHeight:16},snip:{fontSize:9.5,fontWeight:'400',color:'#94a3b8',lineHeight:14},pct:{fontSize:9.5,fontWeight:'600',color:'#94a3b8',lineHeight:14}}
                },
                labelLine:{show:true,length:10,length2:16,smooth:.3,lineStyle:{width:1,color:'#e2e8f0'}},
                emphasis:{scale:true,scaleSize:7,itemStyle:{shadowBlur:16,shadowColor:'rgba(0,0,0,.18)'},label:{show:true,fontWeight:'800',fontSize:12}},
                data:pieData
            }],
            graphic:[
                {type:'text',left:'center',top:'44%',z:100,style:{text:numFmt(total),fill:'#0f172a',font:"800 26px 'Poppins',sans-serif",textAlign:'center'}},
                {type:'text',left:'center',top:'53%',z:100,style:{text:'TOTAL '+metricLbl.toUpperCase(),fill:'#94a3b8',font:"600 8px 'Poppins',sans-serif",textAlign:'center'}}
            ]
        });
        chart.on('click',params=>{ const item=items[params.dataIndex]; if(!item) return; const lm={view:'Most Viewed',like:'Most Liked',comment:'Most Comments',share:'Most Shared'}; FMEDrawer.open(Store[type].length?Store[type]:items,type,lm[type],Store[type].length||items.length); FMEDetail.open(item,type); });
    },

    _renderEngChart(items) {
        hideSk('sk-eng');
        if(!items.length) return;
        const top10=[...items].map(it=>({...it,_total:parseInt(it.view_cnt||it.views||it.freq||0)+parseInt(it.likes||0)+parseInt(it.comments||0)+parseInt(it.shares||0)})).sort((a,b)=>b._total-a._total).slice(0,10);
        FMEChart._items=top10; FMEChart._render(top10,FMEChart._type);
    },

    exportCsv(type) {
        const items=Store[type]; if(!items?.length){ alert('Tidak ada data.'); return; }
        const header='index;nama;sentiment;views;likes;comments;shares;total_engagement;tanggal;url;konten';
        const rows=items.map((it,i)=>{ const name=this._getName(it); const sentRaw=String(it.sentiment_str||it.sentiment||'').toLowerCase(); const sent=sentRaw.includes('pos')?'Positif':sentRaw.includes('neg')?'Negatif':'Netral'; const views=parseInt(it.view_cnt||it.views||it.freq||0); const likes=parseInt(it.likes||0); const cmts=parseInt(it.comments||0); const shares=parseInt(it.shares||0); const totalEng=views+likes+cmts+shares; const dt=(it.date_created||'').split('T')[0]; const url=it.url||it.link||''; const content=(it.content||it.caption||'').replace(/<[^>]*>/g,'').trim().slice(0,300).replace(/;/g,',').replace(/\n/g,' '); return `${i};${name.replace(/;/g,',')};${sent};${views};${likes};${cmts};${shares};${totalEng};${dt};${url};${content}`; });
        const blob=new Blob(['\uFEFF'+[header,...rows].join('\r\n')],{type:'text/csv;charset=utf-8;'}); const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download=`TikTok_Most${type.charAt(0).toUpperCase()+type.slice(1)}_${FMECfg.sd}_${FMECfg.ed}.csv`; a.click();
    }
};

const FMEDonut = {
    highlight(type,idx) { const cap=type.charAt(0).toUpperCase()+type.slice(1); const chart=FMECharts._i['donut'+cap+'Chart']; if(!chart) return; chart.dispatchAction({type:'highlight',seriesIndex:0,dataIndex:idx}); }
};

const FMEChart = {
    _type:'stacked', _items:[],
    setEngType(t) { this._type=t; document.querySelectorAll('#engTypeToggle .fme-toggle-btn').forEach(b=>b.classList.toggle('active',b.dataset.type===t)); if(this._items.length) this._render(this._items,t); },
    _render(items,stackType) {
        const chart=FMECharts.make('ch-eng'); if(!chart) return;
        const labels=items.map(it=>{ const n=FMEData._getName(it); return n.length>16?n.slice(0,15)+'…':n; });
        const views=items.map(it=>parseInt(it.view_cnt||it.views||it.freq||0));
        const likes=items.map(it=>parseInt(it.likes||0));
        const cmts=items.map(it=>parseInt(it.comments||0));
        const shares=items.map(it=>parseInt(it.shares||0));
        const isStack=stackType==='stacked';
        const lbl={show:true,position:isStack?'inside':'insideBottom',distance:isStack?0:12,align:isStack?'center':'left',verticalAlign:'middle',rotate:isStack?0:90,formatter:p=>p.value>0?numK(p.value):'',fontSize:8.5,fontFamily:"'Poppins',sans-serif",fontWeight:'700',color:'#fff'};
        chart.setOption({
            animation:true, animationDuration:800, animationEasing:'elasticOut', backgroundColor:'#ffffff',
            tooltip:{...EC_TT,trigger:'axis',axisPointer:{type:'shadow'},formatter:params=>{ const idx=params[0]?.dataIndex,it=items[idx]; const name=it?FMEData._getName(it):(params[0]?.name||''); const total=params.reduce((s,p)=>s+(p.value||0),0); const rows=params.map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:2px 0;"><div style="display:flex;align-items:center;gap:6px;"><span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:${p.color};flex-shrink:0;"></span><span style="font-size:11px;color:#94a3b8;">${p.seriesName}</span></div><span style="font-size:11px;font-weight:700;">${numFmt(p.value)}</span></div>`).join(''); return `<div style="min-width:180px;"><div style="font-weight:700;font-size:12px;margin-bottom:6px;padding-bottom:5px;border-bottom:1px solid rgba(255,255,255,.1);">${esc(name)}</div>${rows}<div style="border-top:1px solid rgba(255,255,255,.1);margin-top:5px;padding-top:5px;display:flex;justify-content:space-between;"><span style="font-size:10px;color:#94a3b8;">Total</span><span style="font-size:12px;font-weight:700;">${numFmt(total)}</span></div></div>`; }},
            legend:{bottom:0,data:['Views','Likes','Comments','Shares'],textStyle:{fontFamily:"'Poppins',sans-serif",fontSize:10.5,fontWeight:'600',color:'#94a3b8'},icon:'circle',itemWidth:8,itemHeight:8,itemGap:16},
            grid:{top:12,right:16,bottom:48,left:14,containLabel:true},
            xAxis:{type:'category',data:labels,axisTick:{show:false},axisLine:{show:false},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,fontWeight:'600',color:'#94a3b8',interval:0,rotate:labels.length>7?28:0}},
            yAxis:{type:'value',axisLine:{show:false},axisTick:{show:false},splitLine:{lineStyle:{color:'#f1f5f9',type:'dashed'}},axisLabel:{fontFamily:"'Poppins',sans-serif",fontSize:10,color:'#94a3b8',formatter:numK}},
            series:[
                {name:'Views',   type:'bar',barGap:0,stack:isStack?'eng':undefined,data:views,  barMaxWidth:isStack?52:24,itemStyle:{color:'#EE1D52',borderRadius:isStack?[0,0,0,0]:[4,4,0,0]},emphasis:{focus:'series'},label:lbl},
                {name:'Likes',   type:'bar',barGap:0,stack:isStack?'eng':undefined,data:likes,  barMaxWidth:isStack?52:24,itemStyle:{color:'#10b981',borderRadius:isStack?[0,0,0,0]:[4,4,0,0]},emphasis:{focus:'series'},label:lbl},
                {name:'Comments',type:'bar',barGap:0,stack:isStack?'eng':undefined,data:cmts,   barMaxWidth:isStack?52:24,itemStyle:{color:'#f59e0b',borderRadius:isStack?[0,0,0,0]:[4,4,0,0]},emphasis:{focus:'series'},label:lbl},
                {name:'Shares',  type:'bar',barGap:0,stack:isStack?'eng':undefined,data:shares, barMaxWidth:isStack?52:24,itemStyle:{color:'#06b6d4',borderRadius:isStack?[4,4,0,0]:[4,4,0,0]},emphasis:{focus:'series'},label:lbl}
            ]
        },true);
        chart.on('click',params=>{ const item=items[params.dataIndex]; if(!item) return; const sm={'Views':'view','Likes':'like','Comments':'comment','Shares':'share'}; const ct=sm[params.seriesName]||'view'; const ai=Store[ct].length?Store[ct]:items; const lm={view:'Most Viewed Videos',like:'Most Liked Videos',comment:'Most Comments Videos',share:'Most Shared Videos'}; FMEDrawer.open(ai,ct,lm[ct],ai.length); FMEDetail.open(item,ct); });
    }
};

/* ══ SLIDE-IN DRAWER ══ */
const FMEDrawer = {
    _curType: null, _items: [],

    open(items, type, title, count) {
        this._items = items || [];
        this._curType = type;
        FMEDetail.close();
        const colorMap = { view:'#EE1D52', like:'#10b981', comment:'#f59e0b', share:'#06b6d4' };
        document.getElementById('drawerDot').style.background = colorMap[type] || '#EE1D52';
        document.getElementById('drawerTitle').textContent = title || 'Video Detail';
        document.getElementById('drawerCount').textContent = numFmt(count || items.length);
        document.getElementById('drawerMeta').textContent = FMECfg.sd + ' – ' + FMECfg.ed;
        document.getElementById('fmeBackdrop').classList.add('visible');
        document.getElementById('fmeDrawer').classList.add('visible');
        document.body.style.overflow = 'hidden';
        this._render(items, type);
    },

    close() {
        FMEDetail._killIframe();
        FMEDetail.close();
        document.getElementById('fmeBackdrop').classList.remove('visible');
        document.getElementById('fmeDrawer').classList.remove('visible');
        document.body.style.overflow = '';
    },

    exportCsv() { FMEData.exportCsv(this._curType || 'view'); },

    _render(items, type) {
        const list = document.getElementById('drawerList');
        if (!items?.length) { list.innerHTML = `<div class="fme-spinner-state"><div class="fme-spinner"></div>Tidak ada data</div>`; return; }
        const metricLbl = { view:'Views', like:'Likes', comment:'Komentar', share:'Shares' };
        list.innerHTML = items.slice(0, 100).map(item => {
            const name = FMEData._getName(item);
            const avColor = FMEData._getAvatarColor(item);
            const av = FMEData._getAvatar(item);
            const ini = FMEData._getInitials(name);
            const safeIni = ini.replace(/['"\\]/g, '');
            const avHtml = (av && av.startsWith('http')) ? `<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">` : ini;
            const rawText = (item.content || item.caption || '').replace(/<[^>]*>/g, '').trim();
            const text = rawText ? decodeStr(rawText).slice(0, 130) : '';
            const metVal = FMEData._metric(item, type);
            const dt = (item.date_created || '').split('T')[0];
            const sentRaw = String(item.sentiment_str || item.sentiment || '').toLowerCase();
            const sent = sentRaw.includes('pos') ? 'pos' : sentRaw.includes('neg') ? 'neg' : 'neu';
            const sentLbl = sent === 'pos' ? 'Pos' : sent === 'neg' ? 'Neg' : 'Neu';
            const totalEng = parseInt(item.view_cnt||item.views||item.freq||0)+parseInt(item.likes||item.num_likes||0)+parseInt(item.comments||item.num_comments||0)+parseInt(item.shares||item.num_shares||0);
            const itemEnc = encodeURIComponent(JSON.stringify(item));
            return `<div class="fme-dl-item" data-item="${esc(itemEnc)}" data-type="${type}" onclick="FMEDrawer._onItemClick(this)">
                <div class="fme-dl-avatar" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div>
                <div class="fme-dl-body">
                    <div class="fme-dl-author">${esc(name)}</div>
                    <div class="fme-dl-text">${esc(text || '(tidak ada konten)')}</div>
                    <div class="fme-dl-footer">
                        <span class="fme-sent fme-sent--${sent}">${sentLbl}</span>
                        <span>${metricLbl[type]} ${numFmt(metVal)}</span>
                        <span>∑ ${numFmt(totalEng)}</span>
                        ${dt ? `<span style="margin-left:auto;">${dt}</span>` : ''}
                    </div>
                </div>
            </div>`;
        }).join('');
        if (items.length > 100) list.insertAdjacentHTML('beforeend', `<div style="padding:8px 14px;text-align:center;font-size:10.5px;font-weight:600;color:#94a3b8;background:var(--slate-50);border-top:1px dashed var(--slate-200);">+${(items.length-100).toLocaleString()} videos lainnya</div>`);
    },

    _onItemClick(el) {
        try {
            const raw = el.getAttribute('data-item');
            const item = JSON.parse(decodeURIComponent(raw.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&quot;/g,'"')));
            FMEDetail.open(item, el.dataset.type || this._curType);
        } catch(e) { console.warn(e); }
    }
};

/* ══ DETAIL PANEL (slides in over drawer list) ══ */
const FMEDetail = {
    open(item, type) {
        const panel = document.getElementById('fmeDetailPanel');
        const body  = document.getElementById('dpBody');
        const title = document.getElementById('dpTitle');

        const colorMap = { view:'#EE1D52', like:'#10b981', comment:'#f59e0b', share:'#06b6d4' };
        const labelMap = { view:'Most Viewed', like:'Most Liked', comment:'Most Comments', share:'Most Shared' };
        const color = colorMap[type] || '#EE1D52';
        const label = labelMap[type] || 'TikTok Video';

        const name       = FMEData._getName(item);
        const av         = FMEData._getAvatar(item);
        const avColor    = FMEData._getAvatarColor(item);
        const ini        = FMEData._getInitials(name);
        const safeIni    = ini.replace(/['"\\]/g, '');
        const handle     = item.author_scr_name || item.author_id || '';
        const rawContent = (item.content || item.caption || '').replace(/<[^>]*>/g, '').trim();
        const content    = rawContent ? decodeStr(rawContent) : '';
        const date       = item.date_created || '';
        const url        = item.url || item.link || '';

        const views  = parseInt(item.view_cnt  || item.views   || item.freq || 0);
        const likes  = parseInt(item.likes     || item.num_likes || 0);
        const cmts   = parseInt(item.comments  || item.num_comments || 0);
        const shares = parseInt(item.shares    || item.num_shares || 0);

        const sentRaw  = String(item.sentiment_str || item.sentiment || '').toLowerCase();
        const sent     = sentRaw.includes('pos') ? 'pos' : sentRaw.includes('neg') ? 'neg' : 'neu';
        const sentLbl  = { pos:'Positive', neg:'Negative', neu:'Neutral' }[sent];
        const sentDesc = { pos:'Post menunjukkan sentimen positif', neg:'Post menunjukkan sentimen negatif', neu:'Post bersifat netral' }[sent];
        const sentEmoji= { pos:'😊', neg:'😞', neu:'😐' }[sent];
        const sentBg   = { pos:'rgba(16,185,129,.1)', neg:'rgba(239,68,68,.1)', neu:'rgba(100,116,139,.08)' }[sent];

        const avHtml = (av && av.startsWith('http'))
            ? `<img src="${esc(av)}" alt="${esc(name)}" onerror="this.style.display='none';this.parentElement.textContent='${safeIni}'">`
            : ini;

        let dtFmt = '';
        if (date) { try { dtFmt = new Date(date).toLocaleDateString('id-ID', {weekday:'short',day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}); } catch(e) { dtFmt = date.split('T')[0]; } }

        let videoId = '';
        if (url) { const m = url.match(/\/video\/(\d+)/); if (m) videoId = m[1]; }
        if (!videoId && item.id) { const m = String(item.id).match(/(\d{10,})/); if (m) videoId = m[1]; }

        const mediaHtml = videoId ? `
            <div>
                <div class="fme-dp-section">
                    <div class="fme-dp-section-icon" style="background:rgba(238,29,82,.08);"><svg viewBox="0 0 24 24" style="stroke:#EE1D52;"><polygon points="5 3 19 12 5 21 5 3"/></svg></div>
                    <span class="fme-dp-section-title">Video Preview</span>
                    <div class="fme-dp-section-line"></div>
                </div>
                <div class="fme-dp-media">
                    <iframe src="https://www.tiktok.com/embed/v2/${videoId}" allow="autoplay" allowfullscreen></iframe>
                    <div class="fme-dp-media-label"><svg viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/></svg>TikTok Embed</div>
                </div>
            </div>` : '';

        title.textContent = name;
        body.innerHTML = `
            <div class="fme-dp-inner">
                <!-- Profile -->
                <div class="fme-dp-profile">
                    <div class="fme-dp-avatar" style="background:linear-gradient(135deg,${avColor},${avColor}99);">${avHtml}</div>
                    <div class="fme-dp-profile-info">
                        <div class="fme-dp-name">${esc(name)}</div>
                        ${handle ? `<div class="fme-dp-handle">@${esc(handle)}</div>` : ''}
                        <div class="fme-dp-badges">
                            <span class="fme-dp-plat-badge" style="background:${color}14;color:${color};border:1px solid ${color}28;">
                                <svg viewBox="0 0 24 24" style="width:10px;height:10px;fill:${color};flex-shrink:0;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/></svg>
                                TikTok · ${label}
                            </span>
                            ${dtFmt ? `<span class="fme-dp-date-badge"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>${dtFmt}</span>` : ''}
                        </div>
                    </div>
                </div>

                <!-- Sentiment -->
                <div class="fme-dp-sentiment fme-dp-sentiment--${sent}">
                    <div class="fme-dp-sent-icon" style="background:${sentBg};">${sentEmoji}</div>
                    <div>
                        <div class="fme-dp-sent-label fme-dp-sent-label--${sent}">Sentimen: ${sentLbl}</div>
                        <div class="fme-dp-sent-desc">${sentDesc}</div>
                    </div>
                </div>

                <!-- Metrics -->
                <div>
                    <div class="fme-dp-section">
                        <div class="fme-dp-section-icon" style="background:rgba(67,97,238,.08);"><svg viewBox="0 0 24 24" style="stroke:#4361EE;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
                        <span class="fme-dp-section-title">Engagement Metrics</span>
                        <div class="fme-dp-section-line"></div>
                    </div>
                    <div class="fme-dp-metrics">
                        <div class="fme-dp-metric-card fme-dp-metric-card--views">
                            <div class="fme-dp-metric-icon fme-dp-metric-icon--views"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
                            <div class="fme-dp-metric-value fme-dp-metric-value--views">${numFmt(views)}</div>
                            <div class="fme-dp-metric-label">Views</div>
                        </div>
                        <div class="fme-dp-metric-card fme-dp-metric-card--likes">
                            <div class="fme-dp-metric-icon fme-dp-metric-icon--likes"><svg viewBox="0 0 24 24"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg></div>
                            <div class="fme-dp-metric-value fme-dp-metric-value--likes">${numFmt(likes)}</div>
                            <div class="fme-dp-metric-label">Likes</div>
                        </div>
                        <div class="fme-dp-metric-card fme-dp-metric-card--cmts">
                            <div class="fme-dp-metric-icon fme-dp-metric-icon--cmts"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
                            <div class="fme-dp-metric-value fme-dp-metric-value--cmts">${numFmt(cmts)}</div>
                            <div class="fme-dp-metric-label">Comments</div>
                        </div>
                        <div class="fme-dp-metric-card fme-dp-metric-card--shares">
                            <div class="fme-dp-metric-icon fme-dp-metric-icon--shares"><svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg></div>
                            <div class="fme-dp-metric-value fme-dp-metric-value--shares">${numFmt(shares)}</div>
                            <div class="fme-dp-metric-label">Shares</div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <div class="fme-dp-section">
                        <div class="fme-dp-section-icon" style="background:var(--slate-100);"><svg viewBox="0 0 24 24" style="stroke:var(--slate-500);"><line x1="21" y1="6" x2="3" y2="6"/><line x1="15" y1="12" x2="3" y2="12"/><line x1="17" y1="18" x2="3" y2="18"/></svg></div>
                        <span class="fme-dp-section-title">Post Description</span>
                        <div class="fme-dp-section-line"></div>
                    </div>
                    <div class="fme-dp-desc-box">
                        ${content
                            ? `<div class="fme-dp-desc-text" id="dpDescText">${esc(content)}</div>
                               ${content.length > 200 ? `<button class="fme-dp-desc-toggle" id="dpDescToggle" onclick="FMEDetail.toggleDesc()">Show More<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>` : ''}`
                            : `<span style="font-size:12px;color:var(--slate-400);font-style:italic;">(Tidak ada deskripsi konten)</span>`
                        }
                    </div>
                </div>

                <!-- Media -->
                ${mediaHtml}

                <!-- CTA -->
                ${url ? `<a href="${esc(url)}" target="_blank" rel="noopener noreferrer" class="fme-dp-cta"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Buka Video di TikTok</a>` : ''}
            </div>`;

        panel.classList.add('visible');
    },

    toggleDesc() {
        const text = document.getElementById('dpDescText');
        const btn  = document.getElementById('dpDescToggle');
        if (!text || !btn) return;
        const expanded = text.classList.toggle('expanded');
        btn.classList.toggle('expanded', expanded);
        btn.innerHTML = (expanded ? 'Show Less' : 'Show More') + `<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>`;
    },

    _killIframe() {
        const body = document.getElementById('dpBody');
        if (!body) return;
        body.querySelectorAll('iframe').forEach(f => { f.src = ''; f.remove(); });
    },

    close() {
        this._killIframe();
        document.getElementById('fmeDetailPanel')?.classList.remove('visible');
    }
};

/* ══ MAIN ══ */
const FME = {
    reload() {
        FMECharts.disposeAll();
        FMETab.reset();
        Store.view=[]; Store.like=[]; Store.comment=[]; Store.share=[];
        FMEChart._items=[];
        FMEData.loadAll();
    },
    init() {
        // Close drawer on ESC
        document.addEventListener('keydown', e => { if (e.key === 'Escape') FMEDrawer.close(); });
        FMEData.loadAll();
    }
};

document.addEventListener('DOMContentLoaded', () => FME.init());
</script>
@endsection