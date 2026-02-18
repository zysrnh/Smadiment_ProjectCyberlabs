@extends('mk.layouts.app')

@section('title', 'Facebook Most Viewed Posts - SMADIMENT')

@section('styles')
<style>
    :root {
        --primary-blue: #1877F2;
        --primary-blue-dark: #0d5dbf;
        --accent-green: #42b72a;
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

    body { background: var(--bg-gray-50); }

    .dashboard-container {
        padding: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }

    .page-header { margin-bottom: 32px; display: flex; align-items: center; gap: 16px; }
    .page-header-icon {
        width: 56px; height: 56px; border-radius: 14px;
        background: linear-gradient(135deg, rgba(24,119,242,0.12) 0%, rgba(24,119,242,0.06) 100%);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .page-header-icon svg { width: 30px; height: 30px; fill: var(--primary-blue); }
    .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; }
    .page-header p { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* Filter Card */
    .filter-card {
        background: var(--bg-white); border-radius: 16px;
        padding: 20px 24px; margin-bottom: 24px;
        box-shadow: var(--shadow-sm); border: 1px solid var(--border-gray);
    }
    .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .filter-label { font-size: 14px; font-weight: 600; color: var(--text-primary); white-space: nowrap; display: flex; align-items: center; gap: 6px; }
    .filter-label svg { width: 18px; height: 18px; stroke: currentColor; fill: none; }
    .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }
    .apply-btn {
        padding: 12px 28px;
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
        color: white; border: none; border-radius: 12px;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 12px rgba(24,119,242,0.25);
    }
    .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(24,119,242,0.35); }
    .apply-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; }

    /* Alert */
    .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px; }
    .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
    .alert-warning svg { width: 20px; height: 20px; stroke: currentColor; fill: none; flex-shrink: 0; }

    /* Stats Grid */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card {
        background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 16px;
        padding: 24px; box-shadow: var(--shadow-sm); transition: all 0.3s;
        position: relative; overflow: hidden; min-height: 120px;
        display: flex; flex-direction: column; justify-content: center;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
        opacity: 0; transition: opacity 0.3s;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-blue); }
    .stat-card:hover::before { opacity: 1; }
    .stat-label { font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .stat-value { font-size: 32px; font-weight: 700; color: var(--text-primary); line-height: 1.2; word-break: break-word; }

    /* Table Container */
    .table-container {
        background: var(--bg-white); border-radius: 16px; border: 1px solid var(--border-gray);
        box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .table-wrapper { overflow-x: auto; }

    /* Table */
    .status-table { width: 100%; border-collapse: separate; border-spacing: 0; font-family: 'Poppins', sans-serif; }
    .status-table thead { background: linear-gradient(135deg, var(--bg-gray-50) 0%, var(--bg-white) 100%); border-bottom: 2px solid var(--border-gray); }
    .status-table th { padding: 16px 20px; text-align: left; font-size: 11px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
    .status-table th.text-center { text-align: center; }
    .status-table th.text-right { text-align: right; }
    .status-table tbody tr { border-bottom: 1px solid var(--bg-gray-100); transition: all 0.2s; }
    .status-table tbody tr:hover { background: var(--bg-gray-50); }
    .status-table tbody tr:last-child { border-bottom: none; }
    .status-table td { padding: 16px 20px; font-size: 13px; color: var(--text-primary); vertical-align: middle; }

    /* Rank */
    .rank-cell { font-weight: 700; color: var(--primary-blue); font-size: 15px; text-align: center; width: 60px; }

    /* Author */
    .author-cell { min-width: 200px; }
    .author-info { display: flex; align-items: center; gap: 12px; }
    .author-avatar {
        width: 44px; height: 44px; border-radius: 50%; object-fit: cover;
        border: 2px solid var(--border-gray); flex-shrink: 0;
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 700; font-size: 16px; text-transform: uppercase;
        overflow: hidden;
    }
    .author-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .author-details { flex: 1; min-width: 0; }
    .author-name { font-weight: 700; color: var(--text-primary); margin: 0 0 2px 0; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .author-platform { font-size: 11px; color: var(--primary-blue); font-weight: 600; display: flex; align-items: center; gap: 4px; }
    .author-platform svg { width: 12px; height: 12px; fill: currentColor; }

    /* Post Content */
    .status-cell { max-width: 420px; }
    .status-content {
        font-size: 13px; line-height: 1.5; color: var(--text-primary); margin-bottom: 8px;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; word-wrap: break-word;
    }
    .status-meta { display: flex; align-items: center; gap: 12px; font-size: 11px; color: var(--text-muted); flex-wrap: wrap; }
    .status-meta-item { display: flex; align-items: center; gap: 4px; }
    .status-meta-item svg { width: 13px; height: 13px; stroke: currentColor; fill: none; }
    .status-link { color: var(--primary-blue); text-decoration: none; font-weight: 600; transition: all 0.2s; }
    .status-link:hover { color: var(--primary-blue-dark); text-decoration: underline; }

    /* Engagement Stat Cells */
    .stat-cell { text-align: right; font-weight: 700; white-space: nowrap; }
    .stat-cell .val { display: block; font-size: 16px; color: var(--text-primary); margin-bottom: 2px; }
    .stat-cell .lbl { display: block; font-size: 10px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
    .engagement-cell .val { color: var(--primary-blue); }
    .likes-cell .val { color: #ef4444; }
    .comments-cell .val { color: #f59e0b; }
    .shares-cell .val { color: var(--accent-green); }

    /* Sentiment Badge */
    .sentiment-badge {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap; min-width: 80px;
    }
    .sentiment-badge.positive { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border: 1px solid #6ee7b7; }
    .sentiment-badge.negative { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border: 1px solid #fca5a5; }
    .sentiment-badge.neutral { background: linear-gradient(135deg, var(--bg-gray-100) 0%, var(--bg-gray-50) 100%); color: var(--text-secondary); border: 1px solid var(--border-gray); }

    /* Chart Container */
    .chart-container { background: var(--bg-white); border-radius: 16px; border: 1px solid var(--border-gray); box-shadow: var(--shadow-sm); padding: 24px; margin-bottom: 24px; }
    .chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .chart-title { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .chart-subtitle { font-size: 13px; color: var(--text-secondary); margin: 4px 0 0 0; }
    .chart-toggle-btn {
        padding: 8px 16px; background: var(--bg-gray-50); border: 1px solid var(--border-gray);
        border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 12px; font-weight: 600;
        color: var(--text-primary); cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px;
    }
    .chart-toggle-btn:hover { background: var(--primary-blue); color: white; border-color: var(--primary-blue); }
    .chart-toggle-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; transition: transform 0.3s; }
    .chart-body { transition: all 0.3s; overflow: hidden; }
    .chart-body.hidden { max-height: 0; opacity: 0; }

    /* Export */
    .export-btn {
        padding: 10px 20px; background: var(--bg-white); border: 1px solid var(--border-gray); border-radius: 10px;
        font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; color: var(--text-primary);
        cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; box-shadow: var(--shadow-sm);
    }
    .export-btn:hover { border-color: var(--primary-blue); color: var(--primary-blue); box-shadow: var(--shadow-md); }
    .export-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; }

    /* Pagination */
    .pagination { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 20px; background: var(--bg-white); border-top: 1px solid var(--border-gray); }
    .pagination-info { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
    .page-btn {
        width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--border-gray);
        background: var(--bg-white); color: var(--text-primary); font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;
        font-family: 'Poppins', sans-serif;
    }
    .page-btn:hover:not(:disabled) { border-color: var(--primary-blue); color: var(--primary-blue); background: rgba(24,119,242,0.05); }
    .page-btn.active { background: var(--primary-blue); color: white; border-color: var(--primary-blue); }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* Skeleton */
    .skeleton-line { height: 16px; background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 8px; margin-bottom: 12px; }
    .skeleton-avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    /* Empty State */
    .empty-state { text-align: center; padding: 80px 20px; }
    .empty-state svg { width: 64px; height: 64px; color: var(--text-secondary); margin-bottom: 16px; stroke: currentColor; fill: none; }
    .empty-state h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
    .empty-state p { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* Modal */
    .modal-overlay {
        display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7);
        backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content { background: var(--bg-white); border-radius: 16px; width: 90%; max-width: 680px; max-height: 85vh; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.3); display: flex; flex-direction: column; animation: slideUp 0.3s ease-out; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .modal-header { padding: 20px 24px; border-bottom: 2px solid var(--bg-gray-50); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
    .modal-header h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .modal-close { width: 36px; height: 36px; border-radius: 8px; background: var(--bg-gray-50); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; color: var(--text-secondary); }
    .modal-close:hover { background: #ef4444; color: white; }
    .modal-close svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2.5; }
    .modal-body { padding: 24px; overflow-y: auto; flex: 1; }
    .modal-author-section { display: flex; align-items: center; gap: 16px; padding-bottom: 20px; border-bottom: 1px solid var(--bg-gray-100); margin-bottom: 20px; }
    .modal-avatar { width: 64px; height: 64px; border-radius: 50%; border: 3px solid var(--border-gray); background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 24px; flex-shrink: 0; overflow: hidden; }
    .modal-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .modal-author-name { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0; }
    .modal-platform-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: rgba(24,119,242,0.1); color: var(--primary-blue); border-radius: 20px; font-size: 12px; font-weight: 600; }
    .modal-platform-badge svg { width: 14px; height: 14px; fill: currentColor; }
    .modal-post-content { font-size: 15px; line-height: 1.7; color: var(--text-primary); margin-bottom: 20px; white-space: pre-wrap; word-wrap: break-word; }
    .modal-post-meta { display: flex; align-items: center; gap: 12px; padding: 16px 0; border-top: 1px solid var(--bg-gray-100); border-bottom: 1px solid var(--bg-gray-100); margin-bottom: 20px; font-size: 13px; color: var(--text-secondary); }
    .modal-post-meta svg { width: 14px; height: 14px; stroke: currentColor; fill: none; }
    .modal-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
    .modal-stat-box { text-align: center; padding: 16px 8px; background: var(--bg-gray-50); border-radius: 12px; border: 1px solid var(--border-gray); }
    .modal-stat-value { font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .modal-stat-value.engagement { color: var(--primary-blue); }
    .modal-stat-value.likes { color: #ef4444; }
    .modal-stat-value.comments { color: #f59e0b; }
    .modal-stat-value.shares { color: var(--accent-green); }
    .modal-stat-label { font-size: 11px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .modal-actions { display: flex; gap: 12px; }
    .modal-btn { flex: 1; padding: 12px 20px; border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; }
    .modal-btn.primary { background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%); color: white; border: none; }
    .modal-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(24,119,242,0.3); }
    .modal-btn.secondary { background: var(--bg-white); color: var(--text-primary); border: 1px solid var(--border-gray); }
    .modal-btn.secondary:hover { background: var(--bg-gray-50); border-color: var(--primary-blue); color: var(--primary-blue); }
    .modal-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; }

    /* Date Picker */
    .date-picker-trigger {
        display: flex; align-items: center; gap: 12px; padding: 12px 20px;
        background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: 12px;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; color: var(--text-primary);
        cursor: pointer; transition: all 0.2s; width: 100%; max-width: 400px;
    }
    .date-picker-trigger:hover { border-color: var(--primary-blue); background: var(--bg-white); box-shadow: 0 0 0 3px rgba(24,119,242,0.1); }
    .date-picker-trigger svg { width: 18px; height: 18px; stroke: currentColor; fill: none; }
    .date-picker-trigger span { flex: 1; text-align: left; }

    .date-picker-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.5); backdrop-filter: blur(8px); }
    .date-picker-modal.show { display: flex; }
    .date-picker-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; cursor: pointer; }
    .date-picker-container { position: relative; background: #ffffff; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); display: flex; max-width: 900px; width: 90%; max-height: 90vh; z-index: 10001; animation: slideUp 0.3s ease-out; }
    .date-picker-sidebar { width: 180px; background: var(--bg-gray-50); border-right: 1px solid var(--border-gray); padding: 16px 12px; border-radius: 16px 0 0 16px; display: flex; flex-direction: column; gap: 4px; flex-shrink: 0; }
    .date-preset { padding: 10px 16px; background: transparent; border: none; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500; color: var(--text-primary); text-align: left; cursor: pointer; transition: all 0.2s; }
    .date-preset:hover { background: var(--bg-white); color: var(--primary-blue); }
    .date-preset.active { background: var(--primary-blue); color: white; }
    .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; overflow: hidden; }
    .date-picker-header { display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px; }
    .nav-btn { width: 36px; height: 36px; border-radius: 8px; background: var(--bg-gray-50); border: 1px solid var(--border-gray); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
    .nav-btn:hover { background: var(--primary-blue); border-color: var(--primary-blue); color: white; }
    .nav-btn svg { width: 20px; height: 20px; stroke: currentColor; fill: none; }
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
    .calendar-day.today { border: 2px solid var(--primary-blue); }
    .calendar-day.selected { background: var(--primary-blue); color: white; }
    .calendar-day.in-range { background: rgba(24,119,242,0.1); color: var(--primary-blue); }
    .calendar-day.range-start, .calendar-day.range-end { background: var(--primary-blue); color: white; }
    .date-picker-display { padding: 16px 20px; background: var(--bg-gray-50); border-radius: 12px; text-align: center; margin-bottom: 20px; border: 1px solid var(--border-gray); }
    .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }
    .cancel-btn, .apply-date-btn { padding: 10px 24px; border-radius: 10px; font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
    .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
    .cancel-btn:hover { background: var(--border-gray); }
    .apply-date-btn { background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%); color: white; box-shadow: 0 4px 12px rgba(24,119,242,0.25); }
    .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(24,119,242,0.35); }

    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-picker-trigger { max-width: 100%; }
        .apply-btn { width: 100%; justify-content: center; }
        .modal-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .date-picker-container { flex-direction: column; width: 95%; max-height: 85vh; overflow-y: auto; }
        .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; }
        .calendars-wrapper { flex-direction: column; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </div>
        <div>
            <h1>Facebook Most Viewed Posts</h1>
            <p>Top performing Facebook posts ranked by engagement (likes + comments + shares)</p>
        </div>
    </div>

    @if(!$projectId)
    <div class="alert alert-warning">
        <svg viewBox="0 0 24 24">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>No project selected. Please select a project from the sidebar to view most viewed posts.</span>
    </div>
    @else

    {{-- Filter Card --}}
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.facebook.most-viewed-posts') }}">
            <input type="hidden" name="project_id" value="{{ $projectId }}">
            <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
            <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">

            <div class="filter-content">
                <div class="filter-label">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Date Range
                </div>
                <div class="date-range-wrapper">
                    <button type="button" class="date-picker-trigger" id="datePickerTrigger">
                        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span id="dateRangeDisplay">{{ $startDate }} to {{ $endDate }}</span>
                        <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                </div>
                <button type="submit" class="apply-btn">
                    <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Date Range Picker Modal --}}
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
                        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <div class="calendars-wrapper">
                        <div class="calendar" id="calendar1"></div>
                        <div class="calendar" id="calendar2"></div>
                    </div>
                    <button type="button" class="nav-btn" id="nextMonth">
                        <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
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

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Posts</div>
            <div id="totalPosts" class="stat-value">–</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Likes</div>
            <div id="totalLikes" class="stat-value">–</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Comments</div>
            <div id="totalComments" class="stat-value">–</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Shares</div>
            <div id="totalShares" class="stat-value">–</div>
        </div>
    </div>

    {{-- Export Button --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
        <button class="export-btn" onclick="FBPostsLoader.exportCSV()">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download CSV
        </button>
    </div>

    {{-- Top Posts Chart --}}
    <div class="chart-container">
        <div class="chart-header">
            <div>
                <h3 class="chart-title">Top 10 Posts by Engagement</h3>
                <p class="chart-subtitle">Ranked by total likes + comments + shares</p>
            </div>
            <button class="chart-toggle-btn" id="chartToggleBtn" onclick="FBPostsLoader.toggleChart()">
                <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                <span id="chartToggleText">Hide Chart</span>
            </button>
        </div>
        <div class="chart-body" id="chartBody">
            <canvas id="topPostsChart" style="max-height: 380px;"></canvas>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <div class="table-wrapper">

            {{-- Loading State --}}
            <table class="status-table" id="loadingTable">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Author</th>
                        <th>Post</th>
                        <th class="text-right" style="width:110px;">❤️ Likes</th>
                        <th class="text-right" style="width:110px;">💬 Comments</th>
                        <th class="text-right" style="width:110px;">🔁 Shares</th>
                        <th class="text-center" style="width:130px;">Sentiment</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 5; $i++)
                    <tr>
                        <td colspan="7" style="padding: 20px;">
                            <div style="display:flex;align-items:center;gap:16px;">
                                <div class="skeleton-avatar"></div>
                                <div style="flex:1;">
                                    <div class="skeleton-line" style="width:25%;"></div>
                                    <div class="skeleton-line" style="width:55%;"></div>
                                    <div class="skeleton-line" style="width:40%;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            {{-- Actual Table --}}
            <table class="status-table" id="statusTable" style="display:none;">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Author</th>
                        <th>Post</th>
                        <th class="text-right" style="width:110px;">❤️ Likes</th>
                        <th class="text-right" style="width:110px;">💬 Comments</th>
                        <th class="text-right" style="width:110px;">🔁 Shares</th>
                        <th class="text-center" style="width:130px;">Sentiment</th>
                    </tr>
                </thead>
                <tbody id="statusTableBody"></tbody>
            </table>

            {{-- Empty State --}}
            <div id="emptyState" style="display:none;">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <h3>No Facebook Posts Found</h3>
                    <p>No posts available for the selected date range. Try adjusting the date filter.</p>
                </div>
            </div>
        </div>

        <div id="paginationWrapper" class="pagination" style="display:none;"></div>
    </div>

    {{-- Post Detail Modal --}}
    <div class="modal-overlay" id="postModal" onclick="if(event.target===this)FBPostsLoader.closeModal()">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Post Details</h3>
                <button class="modal-close" onclick="FBPostsLoader.closeModal()">
                    <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ───────────────────────────────────────────
// DATE PICKER
// ───────────────────────────────────────────
(function () {
    'use strict';
    let selectedStartDate = null, selectedEndDate = null;
    let currentMonth1 = new Date(), currentMonth2 = new Date();
    let selectingStart = true;

    document.addEventListener('DOMContentLoaded', function () {
        const s = document.getElementById('hiddenStartDate');
        const e = document.getElementById('hiddenEndDate');
        selectedStartDate = s && s.value ? new Date(s.value) : (() => { const d = new Date(); d.setDate(d.getDate() - 6); return d; })();
        selectedEndDate   = e && e.value ? new Date(e.value) : new Date();
        currentMonth1 = new Date(selectedStartDate);
        currentMonth2 = new Date(selectedStartDate);
        currentMonth2.setMonth(currentMonth2.getMonth() + 1);
        renderCalendars();
        setupListeners();
    });

    function setupListeners() {
        document.getElementById('datePickerTrigger')?.addEventListener('click', () => document.getElementById('datePickerModal').classList.add('show'));
        document.querySelector('.date-picker-overlay')?.addEventListener('click', close);
        document.querySelectorAll('.date-preset').forEach(b => b.addEventListener('click', handlePreset));
        document.getElementById('prevMonth')?.addEventListener('click', () => { currentMonth1.setMonth(currentMonth1.getMonth()-1); currentMonth2.setMonth(currentMonth2.getMonth()-1); renderCalendars(); });
        document.getElementById('nextMonth')?.addEventListener('click', () => { currentMonth1.setMonth(currentMonth1.getMonth()+1); currentMonth2.setMonth(currentMonth2.getMonth()+1); renderCalendars(); });
        document.getElementById('applyDatePicker')?.addEventListener('click', apply);
        document.querySelector('.cancel-btn')?.addEventListener('click', close);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
    }

    function close() { document.getElementById('datePickerModal').classList.remove('show'); }

    function handlePreset(e) {
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        const today = new Date(); today.setHours(0,0,0,0);
        switch (e.target.dataset.preset) {
            case 'today':      selectedStartDate = selectedEndDate = new Date(today); break;
            case 'yesterday':  selectedStartDate = selectedEndDate = new Date(today.setDate(today.getDate()-1)); break;
            case 'last7days':  selectedEndDate = new Date(); selectedStartDate = new Date(); selectedStartDate.setDate(selectedStartDate.getDate()-6); break;
            case 'last30days': selectedEndDate = new Date(); selectedStartDate = new Date(); selectedStartDate.setDate(selectedStartDate.getDate()-29); break;
            case 'thismonth':  selectedStartDate = new Date(today.getFullYear(), today.getMonth(), 1); selectedEndDate = new Date(); break;
            case 'lastmonth':  selectedStartDate = new Date(today.getFullYear(), today.getMonth()-1, 1); selectedEndDate = new Date(today.getFullYear(), today.getMonth(), 0); break;
        }
        if (e.target.dataset.preset !== 'custom') {
            currentMonth1 = new Date(selectedStartDate);
            currentMonth2 = new Date(selectedStartDate);
            currentMonth2.setMonth(currentMonth2.getMonth()+1);
            updateDisplay(); renderCalendars();
        }
    }

    function apply() {
        const s = fmt(selectedStartDate), en = fmt(selectedEndDate);
        document.getElementById('hiddenStartDate').value = s;
        document.getElementById('hiddenEndDate').value = en;
        document.getElementById('dateRangeDisplay').textContent = `${s} to ${en}`;
        close();
    }

    function renderCalendars() { renderCal('calendar1', currentMonth1); renderCal('calendar2', currentMonth2); updateDisplay(); }

    function renderCal(id, month) {
        const el = document.getElementById(id); if (!el) return;
        const y = month.getFullYear(), m = month.getMonth();
        const first = new Date(y, m, 1), last = new Date(y, m+1, 0);
        const prevLast = new Date(y, m, 0);
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const today = new Date(); today.setHours(0,0,0,0);
        let html = `<div class="calendar-month">${months[m]} ${y}</div><div class="calendar-weekdays">${['Su','Mo','Tu','We','Th','Fr','Sa'].map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
        for (let i=0; i<first.getDay(); i++) html += `<button type="button" class="calendar-day other-month" disabled>${prevLast.getDate()-(first.getDay()-1-i)}</button>`;
        for (let d=1; d<=last.getDate(); d++) {
            const date = new Date(y,m,d); date.setHours(0,0,0,0);
            let cls = 'calendar-day';
            if (isSame(date,today)) cls += ' today';
            if (date > today) cls += ' disabled';
            if (selectedStartDate && selectedEndDate) {
                if (isSame(date,selectedStartDate)) cls += ' selected range-start';
                else if (isSame(date,selectedEndDate)) cls += ' selected range-end';
                else if (date>selectedStartDate && date<selectedEndDate) cls += ' in-range';
            }
            html += `<button type="button" class="${cls}" data-date="${fmt(date)}" ${date>today?'disabled':''}>${d}</button>`;
        }
        const rem = last.getDay()===6 ? 0 : 6-last.getDay();
        for (let i=1; i<=rem; i++) html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
        html += '</div>'; el.innerHTML = html;
        el.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(b => b.addEventListener('click', function() {
            const d = new Date(this.dataset.date); d.setHours(0,0,0,0);
            document.querySelectorAll('.date-preset').forEach(x=>x.classList.remove('active'));
            document.querySelector('[data-preset="custom"]')?.classList.add('active');
            if (selectingStart || d < selectedStartDate) { selectedStartDate = d; selectedEndDate = d; selectingStart = false; }
            else { selectedEndDate = d >= selectedStartDate ? d : selectedStartDate; if (d < selectedStartDate) selectedStartDate = d; selectingStart = true; }
            updateDisplay(); renderCalendars();
        }));
    }

    function updateDisplay() {
        const t = document.getElementById('selectedRangeText');
        if (t && selectedStartDate && selectedEndDate) t.textContent = `${fmt(selectedStartDate)} to ${fmt(selectedEndDate)}`;
    }

    function fmt(d) { if (!d) return ''; return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`; }
    function isSame(a,b) { return a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
})();

// ───────────────────────────────────────────
// FACEBOOK POSTS LOADER
// ───────────────────────────────────────────
const FBPostsLoader = {
    projectId: '{{ $projectId ?? "" }}',
    startDate: '{{ $startDate ?? "" }}',
    endDate: '{{ $endDate ?? "" }}',
    allPosts: [],
    currentPage: 1,
    postsPerPage: 20,
    chart: null,

    async init() {
        if (!this.projectId) return;
        try {
            await this.loadData();
        } catch (err) {
            console.error('FB Posts error:', err);
            this.showError();
        }
    },

    async loadData() {
        const url = `/mk/api/facebook/most-viewed-posts?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`;
        const res = await fetch(url);
        const result = await res.json();
        console.log('📊 FB Most Viewed Posts API response:', result);
        if (!result.success) throw new Error(result.error || 'Failed to load data');

        this.allPosts = result.data || [];
        this.updateStats(this.allPosts);
        this.renderChart();
        this.renderTable();
    },

    updateStats(posts) {
        const total    = posts.length;
        const likes    = posts.reduce((s,p) => s + (p.likes    || 0), 0);
        const comments = posts.reduce((s,p) => s + (p.comments || 0), 0);
        const shares   = posts.reduce((s,p) => s + (p.shares   || 0), 0);

        document.getElementById('totalPosts').textContent    = this.fmtNum(total);
        document.getElementById('totalLikes').textContent    = this.fmtNum(likes);
        document.getElementById('totalComments').textContent = this.fmtNum(comments);
        document.getElementById('totalShares').textContent   = this.fmtNum(shares);
    },

    renderChart() {
        const canvas = document.getElementById('topPostsChart');
        if (!canvas || !this.allPosts.length) return;
        const top10 = this.allPosts.slice(0, 10);
        const ctx   = canvas.getContext('2d');
        if (this.chart) this.chart.destroy();

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: top10.map((p, i) => `#${i+1} ${(p.author?.name || p.name || 'Unknown').substring(0,20)}`),
                datasets: [
                    { label: 'Likes',    data: top10.map(p=>p.likes    ||0), backgroundColor: 'rgba(239,68,68,0.8)',   borderColor:'#ef4444', borderWidth:2, borderRadius:6, stack:'a' },
                    { label: 'Comments', data: top10.map(p=>p.comments ||0), backgroundColor: 'rgba(245,158,11,0.8)',  borderColor:'#f59e0b', borderWidth:2, borderRadius:6, stack:'a' },
                    { label: 'Shares',   data: top10.map(p=>p.shares   ||0), backgroundColor: 'rgba(66,183,42,0.8)',   borderColor:'#42b72a', borderWidth:2, borderRadius:6, stack:'a' },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: {
                    legend: { position: 'top', labels: { font: { family:'Poppins', size:12 }, padding:16 } },
                    tooltip: {
                        backgroundColor:'#1a202c', titleFont:{family:'Poppins',size:13,weight:'600'},
                        bodyFont:{family:'Poppins',size:12}, padding:12, cornerRadius:8,
                        callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.x.toLocaleString()}` }
                    }
                },
                scales: {
                    x: { stacked:true, beginAtZero:true, grid:{color:'#e2e8f0'}, ticks:{font:{family:'Poppins',size:11},color:'#64748b',callback:v=>v>=1e6?(v/1e6).toFixed(1)+'M':v>=1e3?(v/1e3).toFixed(0)+'K':v} },
                    y: { stacked:true, grid:{display:false}, ticks:{font:{family:'Poppins',size:11,weight:'600'},color:'#1a202c'} }
                }
            }
        });
    },

    toggleChart() {
        const body = document.getElementById('chartBody');
        const btn  = document.getElementById('chartToggleBtn');
        const txt  = document.getElementById('chartToggleText');
        const hidden = body.classList.toggle('hidden');
        txt.textContent = hidden ? 'Show Chart' : 'Hide Chart';
    },

    renderTable() {
        const loading = document.getElementById('loadingTable');
        const table   = document.getElementById('statusTable');
        const empty   = document.getElementById('emptyState');

        if (!this.allPosts.length) {
            loading.style.display = 'none'; table.style.display = 'none'; empty.style.display = 'block'; return;
        }

        const start = (this.currentPage - 1) * this.postsPerPage;
        const page  = this.allPosts.slice(start, start + this.postsPerPage);
        document.getElementById('statusTableBody').innerHTML = page.map((p, i) => this.createRow(p, start + i + 1)).join('');

        loading.style.display = 'none'; table.style.display = 'table'; empty.style.display = 'none';
        this.updatePagination();
    },

    createRow(post, rank) {
        const name     = this.esc(post.author?.name || post.name || 'Unknown');
        const initials = this.initials(name);
        const avatar   = post.avatar_url || '';
        const avatarHtml = avatar
            ? `<img src="${avatar}" alt="${name}" onerror="this.parentElement.innerHTML='${initials}'">`
            : initials;
        const content  = this.esc(post.content || '');
        const date     = this.fmtDate(post.date_created);
        const sClass   = (post.sentiment_str || 'neutral').toLowerCase();
        const likes    = post.likes    || 0;
        const comments = post.comments || 0;
        const shares   = post.shares   || 0;
        const engage   = post.view_cnt || (likes + comments + shares);

        return `<tr>
            <td class="rank-cell">${rank}</td>
            <td class="author-cell">
                <div class="author-info">
                    <div class="author-avatar">${avatarHtml}</div>
                    <div class="author-details">
                        <div class="author-name" title="${name}">${name}</div>
                        <div class="author-platform">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </div>
                    </div>
                </div>
            </td>
            <td class="status-cell">
                <div class="status-content">${content}</div>
                <div class="status-meta">
                    <span class="status-meta-item">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        ${date}
                    </span>
                    <a href="javascript:void(0)" onclick="FBPostsLoader.openModal(${rank-1})" class="status-link">
                        <svg viewBox="0 0 24 24" style="width:13px;height:13px;display:inline;vertical-align:middle;stroke:currentColor;fill:none;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        View
                    </a>
                </div>
            </td>
            <td class="stat-cell likes-cell">
                <span class="val">${this.fmtNum(likes)}</span>
                <span class="lbl">Likes</span>
            </td>
            <td class="stat-cell comments-cell">
                <span class="val">${this.fmtNum(comments)}</span>
                <span class="lbl">Comments</span>
            </td>
            <td class="stat-cell shares-cell">
                <span class="val">${this.fmtNum(shares)}</span>
                <span class="lbl">Shares</span>
            </td>
            <td class="text-center">
                <span class="sentiment-badge ${sClass}">${post.sentiment_str || 'Neutral'}</span>
            </td>
        </tr>`;
    },

    openModal(globalIdx) {
        const post     = this.allPosts[globalIdx];
        if (!post) return;
        const name     = this.esc(post.author?.name || post.name || 'Unknown');
        const initials = this.initials(name);
        const avatar   = post.avatar_url || '';
        const avatarHtml = avatar
            ? `<img src="${avatar}" alt="${name}" onerror="this.parentElement.innerHTML='${initials}'">`
            : initials;
        const content  = this.esc(post.content || '');
        const date     = this.fmtDate(post.date_created);
        const sClass   = (post.sentiment_str || 'neutral').toLowerCase();
        const likes    = post.likes    || 0;
        const comments = post.comments || 0;
        const shares   = post.shares   || 0;
        const engage   = likes + comments + shares;
        const fbUrl    = post.sub_id ? `https://facebook.com/${post.sub_id}` : null;

        document.getElementById('modalBody').innerHTML = `
            <div class="modal-author-section">
                <div class="modal-avatar">${avatarHtml}</div>
                <div>
                    <h4 class="modal-author-name">${name}</h4>
                    <div class="modal-platform-badge">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        Facebook
                    </div>
                </div>
            </div>
            <div class="modal-post-content">${content}</div>
            <div class="modal-post-meta">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                ${date}
            </div>
            <div class="modal-stats-grid">
                <div class="modal-stat-box">
                    <div class="modal-stat-value engagement">${this.fmtNum(engage)}</div>
                    <div class="modal-stat-label">Engagement</div>
                </div>
                <div class="modal-stat-box">
                    <div class="modal-stat-value likes">${this.fmtNum(likes)}</div>
                    <div class="modal-stat-label">Likes</div>
                </div>
                <div class="modal-stat-box">
                    <div class="modal-stat-value comments">${this.fmtNum(comments)}</div>
                    <div class="modal-stat-label">Comments</div>
                </div>
                <div class="modal-stat-box">
                    <div class="modal-stat-value shares">${this.fmtNum(shares)}</div>
                    <div class="modal-stat-label">Shares</div>
                </div>
            </div>
            <div class="modal-actions">
                ${fbUrl ? `<a href="${fbUrl}" target="_blank" class="modal-btn primary">
                    <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    View on Facebook
                </a>` : ''}
                <button onclick="FBPostsLoader.closeModal()" class="modal-btn secondary">Close</button>
            </div>`;

        document.getElementById('postModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    },

    closeModal() {
        document.getElementById('postModal').classList.remove('active');
        document.body.style.overflow = '';
    },

    getPageRange(cur, total) {
        if (total <= 7) return Array.from({length:total},(_,i)=>i+1);
        if (cur <= 4)   return [1,2,3,4,5,'...',total];
        if (cur >= total-3) return [1,'...',total-4,total-3,total-2,total-1,total];
        return [1,'...',cur-1,cur,cur+1,'...',total];
    },

    updatePagination() {
        const total = Math.ceil(this.allPosts.length / this.postsPerPage);
        const from  = this.allPosts.length ? (this.currentPage-1)*this.postsPerPage+1 : 0;
        const to    = Math.min(this.currentPage*this.postsPerPage, this.allPosts.length);
        const wrap  = document.getElementById('paginationWrapper');

        let html = `<div class="pagination-info">Showing ${this.fmtNum(from)}–${this.fmtNum(to)} of ${this.fmtNum(this.allPosts.length)} posts</div>`;
        html += `<div style="display:flex;align-items:center;gap:6px;">`;
        html += `<button class="page-btn" onclick="FBPostsLoader.changePage(${this.currentPage-1})" ${this.currentPage===1?'disabled':''}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
        </button>`;
        this.getPageRange(this.currentPage, total).forEach(p => {
            html += p==='...'
                ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
                : `<button class="page-btn ${p===this.currentPage?'active':''}" onclick="FBPostsLoader.changePage(${p})">${p}</button>`;
        });
        html += `<button class="page-btn" onclick="FBPostsLoader.changePage(${this.currentPage+1})" ${this.currentPage===total?'disabled':''}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
        </button></div>`;

        wrap.innerHTML = html;
        wrap.style.display = this.allPosts.length > 0 ? 'flex' : 'none';
    },

    changePage(p) {
        const total = Math.ceil(this.allPosts.length / this.postsPerPage);
        if (p<1 || p>total) return;
        this.currentPage = p;
        this.renderTable();
        document.querySelector('.table-container').scrollIntoView({behavior:'smooth',block:'start'});
    },

    exportCSV() {
        if (!this.allPosts.length) { alert('No data to export'); return; }
        const headers = ['Rank','Author','Post','Likes','Comments','Shares','Engagement','Sentiment','Date'];
        const rows = this.allPosts.map((p,i) => [
            i+1,
            `"${(p.author?.name||p.name||'Unknown').replace(/"/g,'""')}"`,
            `"${(p.content||'').replace(/"/g,'""')}"`,
            p.likes||0, p.comments||0, p.shares||0,
            (p.likes||0)+(p.comments||0)+(p.shares||0),
            p.sentiment_str||'Neutral',
            p.date_created||''
        ]);
        const csv   = [headers.join(','), ...rows.map(r=>r.join(','))].join('\n');
        const blob  = new Blob([csv], {type:'text/csv;charset=utf-8;'});
        const link  = document.createElement('a');
        link.href   = URL.createObjectURL(blob);
        link.download = `facebook_most_viewed_posts_${this.startDate}_to_${this.endDate}.csv`;
        document.body.appendChild(link); link.click(); document.body.removeChild(link);
    },

    showError() {
        document.getElementById('loadingTable').style.display = 'none';
        const e = document.getElementById('emptyState');
        e.innerHTML = `<div class="empty-state"><svg viewBox="0 0 24 24" style="color:#ef4444;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><h3>Failed to Load Data</h3><p>Unable to fetch Facebook posts. Please try again later.</p></div>`;
        e.style.display = 'block';
        ['totalPosts','totalLikes','totalComments','totalShares'].forEach(id => document.getElementById(id).textContent = '0');
    },

    fmtNum(n) { if (n>=1e6) return (n/1e6).toFixed(1)+'M'; if (n>=1e3) return (n/1e3).toFixed(1)+'K'; return (n||0).toLocaleString(); },
    fmtDate(s) { if (!s) return ''; const d = new Date(s); return d.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric',hour:'2-digit',minute:'2-digit'}); },
    initials(n) { if(!n||n==='Unknown')return '?'; const p=n.trim().split(/\s+/); return p.length===1?p[0].substring(0,2).toUpperCase():(p[0][0]+p[p.length-1][0]).toUpperCase(); },
    esc(t) { const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }
};

document.addEventListener('DOMContentLoaded', () => {
    FBPostsLoader.init();
    document.addEventListener('keydown', e => { if (e.key==='Escape') FBPostsLoader.closeModal(); });
});
</script>
@endsection