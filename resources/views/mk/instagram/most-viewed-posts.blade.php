@extends('mk.layouts.app')

@section('title', 'Instagram Most Viewed Posts - SMADIMENT')

@section('styles')
<style>
    :root {
        --primary-green: #038047;
        --primary-green-dark: #026738;
        --accent-blue: #2FC6F6;
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
        /* Instagram gradient */
        --ig-gradient: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    }

    body { background: var(--bg-gray-50); }

    .dashboard-container {
        padding: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }

    .page-header { margin-bottom: 32px; }
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

    .apply-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; }

    /* Sort Tabs */
    .sort-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 14px;
        padding: 6px;
        width: fit-content;
        box-shadow: var(--shadow-sm);
    }

    .sort-tab {
        padding: 9px 20px;
        border-radius: 10px;
        border: none;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.22s;
        color: var(--text-secondary);
        background: transparent;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .sort-tab:hover { color: var(--text-primary); background: var(--bg-gray-50); }

    .sort-tab.active {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        color: white;
        box-shadow: 0 3px 10px rgba(220,39,67,0.22);
    }

    .sort-tab svg { width: 15px; height: 15px; stroke: currentColor; fill: none; }

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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        z-index: 1;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: var(--ig-gradient);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: #dc2743; }
    .stat-card:hover::before { opacity: 1; }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
        word-break: break-word;
    }

    /* Table Container */
    .table-container {
        background: var(--bg-white);
        border-radius: 16px;
        border: 1px solid var(--border-gray);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .table-wrapper {
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--border-gray) transparent;
    }

    .table-wrapper::-webkit-scrollbar { height: 6px; }
    .table-wrapper::-webkit-scrollbar-track { background: transparent; }
    .table-wrapper::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 3px; }

    .status-table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0;
        font-family: 'Poppins', sans-serif;
        table-layout: fixed;
    }

    /* Column widths */
    .status-table .col-rank     { width: 52px; }
    .status-table .col-author   { width: 180px; }
    .status-table .col-post     { width: auto; }
    .status-table .col-likes    { width: 96px; }
    .status-table .col-comments { width: 96px; }
    .status-table .col-views    { width: 96px; }
    .status-table .col-sentiment{ width: 110px; }

    .status-table thead {
        background: linear-gradient(135deg, var(--bg-gray-50) 0%, var(--bg-white) 100%);
        border-bottom: 2px solid var(--border-gray);
    }

    .status-table th {
        padding: 14px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
        overflow: hidden;
    }

    .status-table th.text-center { text-align: center; }
    .status-table th.text-right  { text-align: right; }

    .status-table tbody tr {
        border-bottom: 1px solid var(--bg-gray-100);
        transition: background 0.15s;
    }

    .status-table tbody tr:hover { background: var(--bg-gray-50); }
    .status-table tbody tr:last-child { border-bottom: none; }

    .status-table td {
        padding: 14px 16px;
        font-size: 13px;
        color: var(--text-primary);
        vertical-align: middle;
        overflow: hidden;
    }

    /* Rank */
    .rank-cell {
        font-weight: 700;
        font-size: 15px;
        text-align: center;
        padding: 14px 8px;
        color: #dc2743;
    }

    /* Author */
    .author-cell { padding: 14px 12px; }
    .author-info { display: flex; align-items: center; gap: 10px; }

    .author-avatar {
        width: 40px; height: 40px;
        border-radius: 50%;
        border: 2px solid var(--border-gray);
        flex-shrink: 0;
        background: var(--ig-gradient);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 700; font-size: 14px; text-transform: uppercase;
        overflow: hidden;
    }

    .author-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .author-details { flex: 1; min-width: 0; }

    .author-name {
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 2px 0;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .author-handle {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* Post content */
    .status-cell { padding: 14px 12px; }

    .status-content {
        font-size: 13px;
        line-height: 1.55;
        color: var(--text-primary);
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
    }

    .status-content.empty-content {
        color: var(--text-muted);
        font-style: italic;
    }

    .status-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 11px;
        color: var(--text-muted);
    }

    .status-meta-item { display: flex; align-items: center; gap: 4px; }
    .status-meta-item svg { width: 12px; height: 12px; stroke: currentColor; fill: none; flex-shrink: 0; }

    .status-link {
        color: var(--accent-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        transition: color 0.2s;
    }

    .status-link:hover { color: #dc2743; }
    .status-link svg { width: 11px; height: 11px; stroke: currentColor; fill: none; }

    /* Stat cells */
    .stat-cell {
        text-align: right;
        font-weight: 700;
        white-space: nowrap;
        padding: 14px 12px;
    }

    .stat-cell .stat-num {
        display: block;
        font-size: 15px;
        color: var(--text-primary);
        margin-bottom: 2px;
        font-weight: 700;
    }

    .stat-cell .stat-lbl {
        display: block;
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .likes-cell    .stat-num { color: #ef4444; }
    .comments-cell .stat-num { color: #f59e0b; }
    .views-cell    .stat-num { color: #8b5cf6; }

    /* Sentiment */
    .sentiment-cell { text-align: center; padding: 14px 8px; }

    .sentiment-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
    }

    .sentiment-badge.positive { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border: 1px solid #6ee7b7; }
    .sentiment-badge.negative { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; border: 1px solid #fca5a5; }
    .sentiment-badge.neutral  { background: linear-gradient(135deg, var(--bg-gray-100) 0%, var(--bg-gray-50) 100%); color: var(--text-secondary); border: 1px solid var(--border-gray); }

    /* Chart Container */
    .chart-container {
        background: var(--bg-white);
        border-radius: 16px;
        border: 1px solid var(--border-gray);
        box-shadow: var(--shadow-sm);
        padding: 24px;
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
    }

    .chart-toggle-btn {
        padding: 8px 16px;
        background: var(--bg-gray-50);
        border: 1px solid var(--border-gray);
        border-radius: 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .chart-toggle-btn:hover { background: #dc2743; color: white; border-color: #dc2743; }
    .chart-toggle-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; transition: transform 0.3s; }
    .chart-toggle-btn.collapsed svg { transform: rotate(180deg); }

    .chart-body { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; }
    .chart-body.hidden { max-height: 0; opacity: 0; margin-top: 0; }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 20px;
        background: var(--bg-white);
        border-top: 1px solid var(--border-gray);
    }

    .pagination-info { font-size: 13px; color: var(--text-secondary); font-weight: 500; }

    .page-btn {
        width: 36px; height: 36px;
        border-radius: 10px;
        border: 1px solid var(--border-gray);
        background: var(--bg-white);
        color: var(--text-primary);
        font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Poppins', sans-serif;
    }

    .page-btn:hover:not(:disabled) { border-color: #dc2743; color: #dc2743; background: rgba(220,39,67,0.05); }
    .page-btn.active { background: var(--ig-gradient); color: white; border-color: transparent; }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* Skeleton */
    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .skeleton-line {
        height: 14px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .skeleton-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        flex-shrink: 0;
    }

    /* Empty State */
    .empty-state { text-align: center; padding: 80px 20px; }
    .empty-state svg { width: 56px; height: 56px; color: var(--text-secondary); margin-bottom: 16px; stroke: currentColor; fill: none; }
    .empty-state h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
    .empty-state p { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0, 0, 0, 0.75);
        z-index: 9999;
        align-items: center; justify-content: center;
    }

    .modal-overlay.active { display: flex; }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-content {
        background: #ffffff !important;
        border-radius: 16px;
        width: 90%; max-width: 700px; max-height: 85vh;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex; flex-direction: column;
        position: relative;
        isolation: isolate;
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 2px solid var(--bg-gray-50);
        display: flex; justify-content: space-between; align-items: center;
        flex-shrink: 0;
        background: var(--ig-gradient);
    }

    .modal-header h3 { font-size: 18px; font-weight: 700; color: #fff; margin: 0; }

    .modal-close {
        width: 36px; height: 36px; border-radius: 8px;
        background: rgba(255,255,255,0.2); border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; color: #fff;
    }

    .modal-close:hover { background: rgba(255,255,255,0.35); }
    .modal-close svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2.5; }

    .modal-body { padding: 24px; overflow-y: auto; flex: 1; }
    .modal-body::-webkit-scrollbar { width: 6px; }
    .modal-body::-webkit-scrollbar-thumb { background: var(--border-gray); border-radius: 3px; }

    .modal-author-section {
        display: flex; align-items: center; gap: 16px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--bg-gray-100);
        margin-bottom: 20px;
    }

    .modal-avatar {
        width: 64px; height: 64px; border-radius: 50%;
        border: 3px solid var(--border-gray);
        background: var(--ig-gradient);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 700; font-size: 24px; flex-shrink: 0;
        overflow: hidden;
    }

    .modal-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }

    .modal-author-info { flex: 1; }
    .modal-author-name { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0; }
    .modal-author-handle { font-size: 14px; color: var(--text-secondary); font-weight: 500; margin-bottom: 8px; }

    .modal-post-content {
        font-size: 15px; line-height: 1.7;
        color: var(--text-primary); margin-bottom: 20px;
        white-space: pre-wrap; word-wrap: break-word;
    }

    .modal-post-meta {
        display: flex; align-items: center; gap: 12px;
        padding: 16px 0;
        border-top: 1px solid var(--bg-gray-100);
        border-bottom: 1px solid var(--bg-gray-100);
        margin-bottom: 20px;
        font-size: 13px; color: var(--text-secondary);
    }

    .modal-post-meta svg { width: 14px; height: 14px; stroke: currentColor; fill: none; }

    .modal-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .modal-stat-box {
        text-align: center; padding: 14px 8px;
        background: var(--bg-gray-50);
        border-radius: 12px; border: 1px solid var(--border-gray);
    }

    .modal-stat-value { font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .modal-stat-value.engagement { background: var(--ig-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .modal-stat-value.likes    { color: #ef4444; }
    .modal-stat-value.comments { color: #f59e0b; }
    .modal-stat-value.views    { color: #8b5cf6; }

    .modal-stat-label { font-size: 11px; color: var(--text-secondary); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

    .modal-actions { display: flex; gap: 12px; }

    .modal-btn {
        flex: 1; padding: 12px 20px; border-radius: 10px;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        text-decoration: none;
    }

    .modal-btn.primary { background: var(--ig-gradient); color: white; border: none; }
    .modal-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220,39,67,0.3); }
    .modal-btn.secondary { background: var(--bg-white); color: var(--text-primary); border: 1px solid var(--border-gray); }
    .modal-btn.secondary:hover { background: var(--bg-gray-50); border-color: #dc2743; color: #dc2743; }
    .modal-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; }

    /* Date Picker Trigger */
    .date-picker-trigger {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 20px;
        background: var(--bg-gray-50); border: 1px solid var(--border-gray); border-radius: 12px;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; color: var(--text-primary);
        cursor: pointer; transition: all 0.2s;
        width: 100%; max-width: 400px;
    }

    .date-picker-trigger:hover { border-color: #dc2743; background: var(--bg-white); box-shadow: 0 0 0 3px rgba(220,39,67,0.1); }
    .date-picker-trigger svg { width: 18px; height: 18px; stroke: currentColor; fill: none; flex-shrink: 0; }
    .date-picker-trigger span { flex: 1; text-align: left; }

    /* Date Picker Modal */
    .date-picker-modal {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        z-index: 10000; display: none;
        align-items: center; justify-content: center;
        background: rgba(0,0,0,0.5);
    }

    .date-picker-modal.show { display: flex; }

    .date-picker-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; cursor: pointer; }

    .date-picker-container {
        position: relative; background: #ffffff; border-radius: 16px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        display: flex; max-width: 900px; width: 90%; max-height: 90vh;
        z-index: 10001; animation: slideUp 0.3s ease-out;
    }

    .date-picker-sidebar {
        width: 180px; background: var(--bg-gray-50);
        border-right: 1px solid var(--border-gray);
        padding: 16px 12px; border-radius: 16px 0 0 16px;
        display: flex; flex-direction: column; gap: 4px; flex-shrink: 0;
    }

    .date-preset {
        padding: 10px 16px; background: transparent; border: none; border-radius: 8px;
        font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 500;
        color: var(--text-primary); text-align: left; cursor: pointer; transition: all 0.2s;
    }

    .date-preset:hover { background: var(--bg-white); color: #dc2743; }
    .date-preset.active { background: var(--ig-gradient); color: white; }

    .date-picker-content { flex: 1; padding: 24px; display: flex; flex-direction: column; overflow: hidden; }
    .date-picker-header { display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px; }

    .nav-btn {
        width: 36px; height: 36px; border-radius: 8px;
        background: var(--bg-gray-50); border: 1px solid var(--border-gray);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; flex-shrink: 0;
    }

    .nav-btn:hover { background: var(--ig-gradient); border-color: transparent; color: white; }
    .nav-btn svg { width: 20px; height: 20px; stroke: currentColor; fill: none; }

    .calendars-wrapper { display: flex; gap: 24px; flex: 1; min-height: 0; }
    .calendar { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .calendar-month { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; text-align: center; }
    .calendar-weekdays { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 8px; }
    .weekday { text-align: center; font-size: 11px; font-weight: 700; color: var(--text-secondary); padding: 8px 0; }
    .calendar-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }

    .calendar-day {
        aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 500; border-radius: 8px;
        cursor: pointer; transition: all 0.2s;
        color: var(--text-primary); background: transparent; border: none; padding: 0;
    }

    .calendar-day:hover:not(.disabled):not(.other-month) { background: var(--bg-gray-100); }
    .calendar-day.other-month { color: #cbd5e1; cursor: default; }
    .calendar-day.disabled { color: #e2e8f0; cursor: not-allowed; }
    .calendar-day.today { border: 2px solid #dc2743; }
    .calendar-day.selected { background: var(--ig-gradient); color: white; }
    .calendar-day.in-range { background: rgba(220,39,67,0.1); color: #dc2743; }
    .calendar-day.range-start, .calendar-day.range-end { background: var(--ig-gradient); color: white; }

    .date-picker-display {
        padding: 16px 20px; background: var(--bg-gray-50);
        border-radius: 12px; text-align: center; margin-bottom: 20px;
        border: 1px solid var(--border-gray);
    }

    .date-picker-display span { font-size: 14px; font-weight: 600; color: var(--text-primary); }

    .date-picker-footer { display: flex; gap: 12px; justify-content: flex-end; }

    .cancel-btn, .apply-date-btn {
        padding: 10px 24px; border-radius: 10px;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; border: none;
    }

    .cancel-btn { background: var(--bg-gray-100); color: var(--text-primary); }
    .cancel-btn:hover { background: var(--border-gray); }

    .apply-date-btn { background: var(--ig-gradient); color: white; box-shadow: 0 4px 12px rgba(220,39,67,0.2); }
    .apply-date-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220,39,67,0.3); }

    /* Responsive */
    @media (max-width: 1024px) {
        .status-table .col-author { width: 150px; }
        .status-table .col-likes, .status-table .col-comments, .status-table .col-views { width: 80px; }
        .status-table .col-sentiment { width: 100px; }
    }

    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-range-wrapper { flex-direction: column; }
        .apply-btn { width: 100%; justify-content: center; }
        .modal-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .date-picker-container { flex-direction: column; max-height: 85vh; overflow-y: auto; width: 95%; }
        .date-picker-sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-gray); border-radius: 16px 16px 0 0; flex-direction: row; overflow-x: auto; padding: 12px 16px; }
        .date-preset { white-space: nowrap; }
        .date-picker-content { padding: 20px 16px; }
        .calendars-wrapper { flex-direction: column; gap: 16px; }
        .date-picker-header { flex-wrap: wrap; }
        .date-picker-trigger { max-width: 100%; }
        .cancel-btn, .apply-date-btn { flex: 1; }
        .status-table { min-width: 700px; }
        .status-table .col-author { width: 130px; }
        .status-table .col-sentiment { width: 90px; }
        .sort-tabs { width: 100%; overflow-x: auto; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <div class="page-header">
        <h1>Instagram Most Viewed Posts</h1>
        <p>Top performing Instagram posts ranked by likes, comments, or views</p>
    </div>

    @if(!$projectId)
    <div class="alert alert-warning">
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;flex-shrink:0;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>No project selected. Please select a project from the sidebar to view most viewed posts.</span>
    </div>
    @else

    <!-- Filter Card -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.instagram.most-viewed-posts') }}">
            <input type="hidden" name="project_id" value="{{ $projectId }}">
            <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
            <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">

            <div class="filter-content">
                <div class="filter-label">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
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

                <button type="submit" class="apply-btn">
                    <svg viewBox="0 0 24 24">
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

    <!-- Sort Tabs -->
    <div class="sort-tabs">
        <button class="sort-tab active" id="tabLikes" onclick="IGPostsLoader.switchSort('postbylike', this)">
            <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            By Likes
        </button>
        <button class="sort-tab" id="tabComments" onclick="IGPostsLoader.switchSort('postbycomment', this)">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            By Comments
        </button>
        <button class="sort-tab" id="tabViews" onclick="IGPostsLoader.switchSort('postbyview', this)">
            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            By Views
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Posts</div>
            <div id="totalPosts" class="stat-value">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Likes</div>
            <div id="totalLikes" class="stat-value">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Comments</div>
            <div id="totalComments" class="stat-value">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Views</div>
            <div id="totalViews" class="stat-value">0</div>
        </div>
    </div>

    <!-- Top Posts Chart -->
    <div class="table-container" style="margin-bottom: 24px;">
        <div style="padding: 20px 24px; border-bottom: 2px solid var(--bg-gray-50);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,rgba(220,39,67,0.1) 0%,rgba(188,24,136,0.05) 100%);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg viewBox="0 0 24 24" style="width:24px;height:24px;fill:none;stroke:#dc2743;stroke-width:2;">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0 0 2px 0;">Top 10 Instagram Posts</h3>
                        <p style="font-size:12px;color:var(--text-secondary);margin:0;" id="chartSubtitle">Ranked by likes</p>
                    </div>
                </div>
                <button class="chart-toggle-btn" id="chartToggleBtn" onclick="IGPostsLoader.toggleChart()">
                    <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                    <span id="chartToggleText">Hide Chart</span>
                </button>
            </div>
        </div>
        <div class="chart-body" id="chartBody" style="padding: 24px;">
            <canvas id="topPostsChart" style="max-height: 380px;"></canvas>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-wrapper">

            <!-- Loading State -->
            <table class="status-table" id="loadingTable" style="display: table; table-layout: fixed;">
                <colgroup>
                    <col class="col-rank">
                    <col class="col-author">
                    <col class="col-post">
                    <col class="col-likes">
                    <col class="col-comments">
                    <col class="col-views">
                    <col class="col-sentiment">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Author</th>
                        <th>Caption</th>
                        <th class="text-right">❤️ Likes</th>
                        <th class="text-right">💬 Comments</th>
                        <th class="text-right">👁️ Views</th>
                        <th class="text-center">Sentiment</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td style="text-align:center;"><div class="skeleton-line" style="width:24px;margin:0 auto;"></div></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="skeleton-avatar"></div>
                                <div style="flex:1;min-width:0;">
                                    <div class="skeleton-line" style="width:80%;margin-bottom:6px;"></div>
                                    <div class="skeleton-line" style="width:50%;height:10px;"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="skeleton-line" style="width:90%;margin-bottom:6px;"></div>
                            <div class="skeleton-line" style="width:70%;margin-bottom:6px;"></div>
                            <div class="skeleton-line" style="width:40%;height:10px;"></div>
                        </td>
                        <td><div class="skeleton-line" style="width:60%;margin-left:auto;"></div></td>
                        <td><div class="skeleton-line" style="width:60%;margin-left:auto;"></div></td>
                        <td><div class="skeleton-line" style="width:60%;margin-left:auto;"></div></td>
                        <td><div class="skeleton-line" style="width:70%;margin:0 auto;"></div></td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Actual Table -->
            <table class="status-table" id="statusTable" style="display: none;">
                <colgroup>
                    <col class="col-rank">
                    <col class="col-author">
                    <col class="col-post">
                    <col class="col-likes">
                    <col class="col-comments">
                    <col class="col-views">
                    <col class="col-sentiment">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Author</th>
                        <th>Caption</th>
                        <th class="text-right">❤️ Likes</th>
                        <th class="text-right">💬 Comments</th>
                        <th class="text-right">👁️ Views</th>
                        <th class="text-center">Sentiment</th>
                    </tr>
                </thead>
                <tbody id="statusTableBody"></tbody>
            </table>

            <!-- Empty State -->
            <div id="emptyState" style="display: none;">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <h3>No Instagram Posts Found</h3>
                    <p>No posts available for the selected date range.</p>
                </div>
            </div>
        </div>

        <div id="paginationWrapper" class="pagination" style="display: none;"></div>
    </div>

    <!-- Post Detail Modal -->
    <div class="modal-overlay" id="postModal" onclick="if(event.target === this) IGPostsLoader.closeModal()">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Post Details</h3>
                <button class="modal-close" onclick="IGPostsLoader.closeModal()">
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
// ========================================
// DATE PICKER
// ========================================
(function() {
    'use strict';
    let selectedStartDate = null, selectedEndDate = null;
    let currentMonth1 = new Date(), currentMonth2 = new Date();
    let selectingStart = true;

    document.addEventListener('DOMContentLoaded', function() {
        const s = document.getElementById('hiddenStartDate');
        const e = document.getElementById('hiddenEndDate');
        selectedStartDate = s && s.value ? new Date(s.value) : (() => { const d = new Date(); d.setDate(d.getDate()-6); return d; })();
        selectedEndDate   = e && e.value ? new Date(e.value) : new Date();
        currentMonth1 = new Date(selectedStartDate);
        currentMonth2 = new Date(selectedStartDate);
        currentMonth2.setMonth(currentMonth2.getMonth() + 1);
        renderCalendars();
        setupEventListeners();
    });

    function setupEventListeners() {
        document.getElementById('datePickerTrigger')?.addEventListener('click', () => document.getElementById('datePickerModal').classList.add('show'));
        document.querySelector('.date-picker-overlay')?.addEventListener('click', closeDatePicker);
        document.querySelectorAll('.date-preset').forEach(btn => btn.addEventListener('click', handlePresetClick));
        document.getElementById('prevMonth')?.addEventListener('click', () => { currentMonth1.setMonth(currentMonth1.getMonth()-1); currentMonth2.setMonth(currentMonth2.getMonth()-1); renderCalendars(); });
        document.getElementById('nextMonth')?.addEventListener('click', () => { currentMonth1.setMonth(currentMonth1.getMonth()+1); currentMonth2.setMonth(currentMonth2.getMonth()+1); renderCalendars(); });
        document.getElementById('applyDatePicker')?.addEventListener('click', applyDateSelection);
        document.querySelector('.cancel-btn')?.addEventListener('click', closeDatePicker);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDatePicker(); });
    }

    function closeDatePicker() { document.getElementById('datePickerModal').classList.remove('show'); }

    function handlePresetClick(e) {
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        const today = new Date(); today.setHours(0,0,0,0);
        switch(e.target.dataset.preset) {
            case 'today':      selectedStartDate = new Date(today); selectedEndDate = new Date(today); break;
            case 'yesterday':  selectedStartDate = new Date(today); selectedStartDate.setDate(today.getDate()-1); selectedEndDate = new Date(selectedStartDate); break;
            case 'last7days':  selectedEndDate = new Date(today); selectedStartDate = new Date(today); selectedStartDate.setDate(today.getDate()-6); break;
            case 'last30days': selectedEndDate = new Date(today); selectedStartDate = new Date(today); selectedStartDate.setDate(today.getDate()-29); break;
            case 'thismonth':  selectedStartDate = new Date(today.getFullYear(), today.getMonth(), 1); selectedEndDate = new Date(today); break;
            case 'lastmonth':  selectedStartDate = new Date(today.getFullYear(), today.getMonth()-1, 1); selectedEndDate = new Date(today.getFullYear(), today.getMonth(), 0); break;
        }
        if (e.target.dataset.preset !== 'custom') {
            currentMonth1 = new Date(selectedStartDate);
            currentMonth2 = new Date(selectedStartDate);
            currentMonth2.setMonth(currentMonth2.getMonth() + 1);
            updateDateDisplay(); renderCalendars();
        }
    }

    function applyDateSelection() {
        const start = formatDate(selectedStartDate), end = formatDate(selectedEndDate);
        document.getElementById('hiddenStartDate').value = start;
        document.getElementById('hiddenEndDate').value = end;
        document.getElementById('dateRangeDisplay').textContent = `${start} to ${end}`;
        closeDatePicker();
    }

    function renderCalendars() { renderCalendar('calendar1', currentMonth1); renderCalendar('calendar2', currentMonth2); updateDateDisplay(); }

    function renderCalendar(elementId, month) {
        const calendar = document.getElementById(elementId); if (!calendar) return;
        const year = month.getFullYear(), monthNum = month.getMonth();
        const firstDay = new Date(year, monthNum, 1), lastDay = new Date(year, monthNum+1, 0);
        const prevLastDay = new Date(year, monthNum, 0);
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const weekdays = ['Su','Mo','Tu','We','Th','Fr','Sa'];
        const today = new Date(); today.setHours(0,0,0,0);
        let html = `<div class="calendar-month">${monthNames[monthNum]} ${year}</div><div class="calendar-weekdays">${weekdays.map(d=>`<div class="weekday">${d}</div>`).join('')}</div><div class="calendar-days">`;
        for (let i=0; i<firstDay.getDay(); i++) html += `<button type="button" class="calendar-day other-month" disabled>${prevLastDay.getDate()-(firstDay.getDay()-1-i)}</button>`;
        for (let day=1; day<=lastDay.getDate(); day++) {
            const date = new Date(year, monthNum, day); date.setHours(0,0,0,0);
            let classes = 'calendar-day';
            if (isSameDay(date, today)) classes += ' today';
            if (date > today) classes += ' disabled';
            if (selectedStartDate && selectedEndDate) {
                if (isSameDay(date, selectedStartDate)) classes += ' selected range-start';
                else if (isSameDay(date, selectedEndDate)) classes += ' selected range-end';
                else if (date > selectedStartDate && date < selectedEndDate) classes += ' in-range';
            }
            html += `<button type="button" class="${classes}" data-date="${formatDate(date)}" ${date>today?'disabled':''}>${day}</button>`;
        }
        const rem = lastDay.getDay() === 6 ? 0 : 6-lastDay.getDay();
        for (let i=1; i<=rem; i++) html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
        html += '</div>'; calendar.innerHTML = html;
        calendar.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => btn.addEventListener('click', handleDateClick));
    }

    function handleDateClick(e) {
        const date = new Date(e.target.dataset.date); date.setHours(0,0,0,0);
        document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
        document.querySelector('[data-preset="custom"]')?.classList.add('active');
        if (selectingStart || date < selectedStartDate) { selectedStartDate = date; selectedEndDate = date; selectingStart = false; }
        else { selectedEndDate = date >= selectedStartDate ? date : selectedStartDate; if (date < selectedStartDate) selectedStartDate = date; selectingStart = true; }
        updateDateDisplay(); renderCalendars();
    }

    function updateDateDisplay() {
        const el = document.getElementById('selectedRangeText');
        if (el && selectedStartDate && selectedEndDate) el.textContent = `${formatDate(selectedStartDate)} to ${formatDate(selectedEndDate)}`;
    }

    function formatDate(date) {
        if (!date) return '';
        return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
    }

    function isSameDay(a, b) {
        if (!a || !b) return false;
        return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();
    }
})();

// ========================================
// INSTAGRAM POSTS LOADER
// ========================================
const IGPostsLoader = {
    projectId:   '{{ $projectId ?? "" }}',
    startDate:   '{{ $startDate ?? "" }}',
    endDate:     '{{ $endDate ?? "" }}',
    allPosts:    [],
    currentPage: 1,
    postsPerPage: 20,
    currentSub:  'postbylike',
    chart:       null,

    async init() {
        if (!this.projectId) return;
        try {
            await this.loadData();
        } catch (error) {
            console.error('❌ Failed to load IG posts:', error);
            this.showError();
        }
    },

    async loadData() {
        // Show loading
        document.getElementById('loadingTable').style.display = 'table';
        document.getElementById('statusTable').style.display  = 'none';
        document.getElementById('emptyState').style.display   = 'none';

        const url = `/mk/api/instagram/most-viewed-posts?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}&sub=${this.currentSub}`;
        const response = await fetch(url);
        const result = await response.json();
        console.log('📊 IG Most Viewed Posts response:', result);
        if (!result.success) throw new Error(result.error || 'Failed to load data');
        this.allPosts    = result.data || [];
        this.currentPage = 1;
        this.updateStats(this.allPosts);
        this.renderChart();
        this.renderTable();
    },

    switchSort(sub, btn) {
        this.currentSub = sub;
        document.querySelectorAll('.sort-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        // Update chart subtitle
        const subtitles = { postbylike: 'Ranked by likes', postbycomment: 'Ranked by comments', postbyview: 'Ranked by views' };
        const chartSubtitle = document.getElementById('chartSubtitle');
        if (chartSubtitle) chartSubtitle.textContent = subtitles[sub] || '';
        this.loadData().catch(e => { console.error(e); this.showError(); });
    },

    updateStats(posts) {
        const total    = posts.length;
        const likes    = posts.reduce((s, p) => s + (p.likes    || 0), 0);
        const comments = posts.reduce((s, p) => s + (p.comments || 0), 0);
        const views    = posts.reduce((s, p) => s + (p.view_cnt || 0), 0);
        document.getElementById('totalPosts').textContent    = total.toLocaleString();
        document.getElementById('totalLikes').textContent    = this.formatNumber(likes);
        document.getElementById('totalComments').textContent = this.formatNumber(comments);
        document.getElementById('totalViews').textContent    = this.formatNumber(views);
    },

    renderChart() {
        const canvas = document.getElementById('topPostsChart');
        if (!canvas || !this.allPosts.length) return;
        const top10 = this.allPosts.slice(0, 10);
        const ctx = canvas.getContext('2d');
        if (this.chart) this.chart.destroy();

        // Pick primary metric by current sort
        const metricMap = {
            postbylike:    { key: 'likes',    label: 'Likes',    color: 'rgba(239,68,68,0.85)' },
            postbycomment: { key: 'comments', label: 'Comments', color: 'rgba(245,158,11,0.85)' },
            postbyview:    { key: 'view_cnt', label: 'Views',    color: 'rgba(139,92,246,0.85)' },
        };
        const m = metricMap[this.currentSub] || metricMap['postbylike'];

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: top10.map((p, i) => `#${i+1} ${(p.author?.name || p.name || 'Unknown').substring(0,20)}`),
                datasets: [
                    {
                        label: m.label,
                        data: top10.map(p => p[m.key] || 0),
                        backgroundColor: m.color,
                        borderColor: m.color.replace('0.85','1'),
                        borderWidth: 2,
                        borderRadius: 6,
                    },
                    {
                        label: 'Engagement',
                        data: top10.map(p => (p.likes||0) + (p.comments||0)),
                        backgroundColor: 'rgba(220,39,67,0.25)',
                        borderColor: 'rgba(220,39,67,0.7)',
                        borderWidth: 2,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: true, indexAxis: 'y',
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Poppins', size: 12 }, padding: 16 } },
                    tooltip: {
                        backgroundColor: '#1a202c',
                        titleFont: { family: 'Poppins', size: 13, weight: '600' },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 12, cornerRadius: 8,
                        callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.x.toLocaleString()}` }
                    }
                },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#e2e8f0' }, ticks: { font: { family: 'Poppins', size: 11 }, color: '#64748b', callback: v => v >= 1e6 ? (v/1e6).toFixed(1)+'M' : v >= 1e3 ? (v/1e3).toFixed(0)+'K' : v } },
                    y: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 11, weight: '600' }, color: '#1a202c' } }
                }
            }
        });
    },

    toggleChart() {
        const chartBody  = document.getElementById('chartBody');
        const toggleBtn  = document.getElementById('chartToggleBtn');
        const toggleText = document.getElementById('chartToggleText');
        if (chartBody.classList.contains('hidden')) {
            chartBody.classList.remove('hidden'); toggleBtn.classList.remove('collapsed'); toggleText.textContent = 'Hide Chart';
        } else {
            chartBody.classList.add('hidden'); toggleBtn.classList.add('collapsed'); toggleText.textContent = 'Show Chart';
        }
    },

    renderTable() {
        const loadingTable = document.getElementById('loadingTable');
        const statusTable  = document.getElementById('statusTable');
        const emptyState   = document.getElementById('emptyState');

        if (!this.allPosts.length) {
            loadingTable.style.display = 'none';
            statusTable.style.display  = 'none';
            emptyState.style.display   = 'block';
            return;
        }

        const startIdx     = (this.currentPage - 1) * this.postsPerPage;
        const currentPosts = this.allPosts.slice(startIdx, startIdx + this.postsPerPage);
        document.getElementById('statusTableBody').innerHTML = currentPosts.map((post, idx) => this.createTableRow(post, startIdx + idx + 1, startIdx + idx)).join('');

        loadingTable.style.display = 'none';
        statusTable.style.display  = 'table';
        emptyState.style.display   = 'none';
        this.updatePagination();
    },

    createTableRow(post, rank, globalIdx) {
        const authorName   = post.author?.name || post.name || 'Instagram User';
        const authorHandle = post.author?.scr_name || post.author?.name || 'instagram';
        const initials     = this.getInitials(authorName);
        const avatarUrl    = post.author?.image || post.avatar_url || '';
        const avatarHtml   = avatarUrl && !avatarUrl.includes('ui-avatars.com')
            ? `<img src="${avatarUrl}" alt="${this.escapeHtml(authorName)}" onerror="this.parentElement.innerHTML='<span style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#fff;\'>${initials}</span>'">`
            : `<span style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#fff;">${initials}</span>`;

        const rawContent   = post.content || '';
        const content      = this.escapeHtml(rawContent);
        const likes        = parseInt(post.likes    || 0);
        const comments     = parseInt(post.comments || 0);
        const views        = parseInt(post.view_cnt || 0);
        const sentimentRaw = (post.sentiment_str || 'neutral').toLowerCase();
        const sentimentLbl = post.sentiment_str || 'Neutral';
        const date         = this.formatDate(post.date_created);
        const hasContent   = rawContent.trim().length > 0;

        return `<tr>
            <td class="rank-cell">${rank}</td>
            <td class="author-cell">
                <div class="author-info">
                    <div class="author-avatar">${avatarHtml}</div>
                    <div class="author-details">
                        <div class="author-name" title="${this.escapeHtml(authorName)}">${this.escapeHtml(authorName)}</div>
                        <div class="author-handle">@${this.escapeHtml(authorHandle)}</div>
                    </div>
                </div>
            </td>
            <td class="status-cell">
                <div class="status-content${hasContent ? '' : ' empty-content'}">${hasContent ? content : '(No caption)'}</div>
                <div class="status-meta">
                    <span class="status-meta-item">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        ${date}
                    </span>
                    <a href="javascript:void(0)" onclick="IGPostsLoader.openModal(${globalIdx})" class="status-link">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        View
                    </a>
                </div>
            </td>
            <td class="stat-cell likes-cell">
                <span class="stat-num">${this.formatNumber(likes)}</span>
                <span class="stat-lbl">Likes</span>
            </td>
            <td class="stat-cell comments-cell">
                <span class="stat-num">${this.formatNumber(comments)}</span>
                <span class="stat-lbl">Comments</span>
            </td>
            <td class="stat-cell views-cell">
                <span class="stat-num">${this.formatNumber(views)}</span>
                <span class="stat-lbl">Views</span>
            </td>
            <td class="sentiment-cell">
                <span class="sentiment-badge ${sentimentRaw}">${sentimentLbl}</span>
            </td>
        </tr>`;
    },

    openModal(globalIdx) {
        const post = this.allPosts[globalIdx];
        if (!post) return;

        const authorName   = post.author?.name || post.name || 'Instagram User';
        const authorHandle = post.author?.scr_name || post.author?.name || 'instagram';
        const initials     = this.getInitials(authorName);
        const avatarUrl    = post.author?.image || post.avatar_url || '';
        const avatarHtml   = avatarUrl && !avatarUrl.includes('ui-avatars.com')
            ? `<img src="${avatarUrl}" alt="${this.escapeHtml(authorName)}" onerror="this.parentElement.innerHTML='<span style=\'width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:#fff;\'>${initials}</span>'">`
            : `<span style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:#fff;">${initials}</span>`;

        const rawContent   = post.content || '';
        const content      = this.escapeHtml(rawContent);
        const likes        = parseInt(post.likes    || 0);
        const comments     = parseInt(post.comments || 0);
        const views        = parseInt(post.view_cnt || 0);
        const engagement   = likes + comments;
        const sentimentRaw = (post.sentiment_str || 'neutral').toLowerCase();
        const sentimentLbl = post.sentiment_str || 'Neutral';
        const date         = this.formatDate(post.date_created);
        const igUrl        = post.url || null;
        const hasContent   = rawContent.trim().length > 0;

        document.getElementById('modalBody').innerHTML = `
            <div class="modal-author-section">
                <div class="modal-avatar">${avatarHtml}</div>
                <div class="modal-author-info">
                    <h4 class="modal-author-name">${this.escapeHtml(authorName)}</h4>
                    <div class="modal-author-handle">@${this.escapeHtml(authorHandle)}</div>
                    <span class="sentiment-badge ${sentimentRaw}" style="margin-top:4px;">${sentimentLbl}</span>
                </div>
            </div>
            <div class="modal-post-content">${hasContent ? content : '<span style="color:var(--text-muted);font-style:italic;">(No caption available)</span>'}</div>
            <div class="modal-post-meta">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                ${date}
            </div>
            <div class="modal-stats-grid">
                <div class="modal-stat-box">
                    <div class="modal-stat-value engagement">${this.formatNumber(engagement)}</div>
                    <div class="modal-stat-label">Engagement</div>
                </div>
                <div class="modal-stat-box">
                    <div class="modal-stat-value likes">${this.formatNumber(likes)}</div>
                    <div class="modal-stat-label">Likes</div>
                </div>
                <div class="modal-stat-box">
                    <div class="modal-stat-value comments">${this.formatNumber(comments)}</div>
                    <div class="modal-stat-label">Comments</div>
                </div>
                <div class="modal-stat-box">
                    <div class="modal-stat-value views">${this.formatNumber(views)}</div>
                    <div class="modal-stat-label">Views</div>
                </div>
            </div>
            <div class="modal-actions">
                ${igUrl ? `<a href="${igUrl}" target="_blank" rel="noopener" class="modal-btn primary">
                    <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    View on Instagram
                </a>` : ''}
                <button onclick="IGPostsLoader.closeModal()" class="modal-btn secondary">Close</button>
            </div>`;

        document.getElementById('postModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    },

    closeModal() {
        document.getElementById('postModal').classList.remove('active');
        document.body.style.overflow = '';
    },

    getPageRange(cur, total) {
        if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
        if (cur <= 4)   return [1, 2, 3, 4, 5, '...', total];
        if (cur >= total - 3) return [1, '...', total-4, total-3, total-2, total-1, total];
        return [1, '...', cur-1, cur, cur+1, '...', total];
    },

    updatePagination() {
        const totalPages = Math.ceil(this.allPosts.length / this.postsPerPage);
        const wrapper = document.getElementById('paginationWrapper');
        const from = this.allPosts.length ? (this.currentPage - 1) * this.postsPerPage + 1 : 0;
        const to   = Math.min(this.currentPage * this.postsPerPage, this.allPosts.length);

        let html = `<div class="pagination-info">Showing ${this.formatNumber(from)}–${this.formatNumber(to)} of ${this.formatNumber(this.allPosts.length)} posts</div>`;
        html += `<div style="display:flex;align-items:center;gap:6px;">`;
        html += `<button class="page-btn" onclick="IGPostsLoader.changePage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
        </button>`;
        this.getPageRange(this.currentPage, totalPages).forEach(p => {
            html += p === '...'
                ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
                : `<button class="page-btn ${p === this.currentPage ? 'active' : ''}" onclick="IGPostsLoader.changePage(${p})">${p}</button>`;
        });
        html += `<button class="page-btn" onclick="IGPostsLoader.changePage(${this.currentPage + 1})" ${this.currentPage === totalPages ? 'disabled' : ''}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
        </button></div>`;

        wrapper.innerHTML = html;
        wrapper.style.display = this.allPosts.length > 0 ? 'flex' : 'none';
    },

    changePage(p) {
        const totalPages = Math.ceil(this.allPosts.length / this.postsPerPage);
        if (p < 1 || p > totalPages) return;
        this.currentPage = p;
        this.renderTable();
        document.querySelector('.table-container:last-of-type')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    formatDate(dateString) {
        if (!dateString) return '—';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        } catch(e) { return dateString; }
    },

    formatNumber(num) {
        if (!num) return '0';
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000)    return (num / 1000).toFixed(1) + 'K';
        return num.toLocaleString();
    },

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    },

    getInitials(name) {
        if (!name || name === 'Instagram User') return '📷';
        const parts = name.trim().split(/\s+/);
        return parts.length === 1 ? parts[0].substring(0, 2).toUpperCase() : (parts[0][0] + parts[parts.length-1][0]).toUpperCase();
    },

    showError() {
        document.getElementById('loadingTable').style.display = 'none';
        const emptyState = document.getElementById('emptyState');
        emptyState.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" style="color:#ef4444;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <h3>Failed to Load Data</h3>
                <p>Unable to fetch Instagram posts. Please try again later.</p>
            </div>`;
        emptyState.style.display = 'block';
        ['totalPosts','totalLikes','totalComments','totalViews'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '0';
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    IGPostsLoader.init();
    document.addEventListener('keydown', e => { if (e.key === 'Escape') IGPostsLoader.closeModal(); });
});
</script>
@endsection