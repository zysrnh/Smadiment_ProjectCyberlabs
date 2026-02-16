@extends('mk.layouts.app')

@section('title', 'Most Viewed Posts - SMADIMENT')

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

    .apply-btn svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        fill: none;
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

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .chart-subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 4px 0 0 0;
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

    .chart-toggle-btn:hover {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
    }

    .chart-toggle-btn svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
        fill: none;
        transition: transform 0.3s;
    }

    .chart-toggle-btn.collapsed svg {
        transform: rotate(180deg);
    }

    .chart-body {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .chart-body.hidden {
        max-height: 0;
        opacity: 0;
        margin-top: 0;
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
    }

    /* Modern Table */
    .status-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-family: 'Poppins', sans-serif;
    }

    .status-table thead {
        background: linear-gradient(135deg, var(--bg-gray-50) 0%, var(--bg-white) 100%);
        border-bottom: 2px solid var(--border-gray);
    }

    .status-table th {
        padding: 16px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }

    .status-table th.text-center { text-align: center; }
    .status-table th.text-right { text-align: right; }

    .status-table tbody tr {
        border-bottom: 1px solid var(--bg-gray-100);
        transition: all 0.2s;
    }

    .status-table tbody tr:hover {
        background: var(--bg-gray-50);
    }

    .status-table tbody tr:last-child {
        border-bottom: none;
    }

    .status-table td {
        padding: 16px 20px;
        font-size: 13px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    /* Rank Column */
    .rank-cell {
        font-weight: 700;
        color: var(--primary-green);
        font-size: 15px;
        text-align: center;
        width: 60px;
    }

    /* Author Column */
    .author-cell {
        min-width: 200px;
    }

    .author-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .author-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--border-gray);
        flex-shrink: 0;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 16px;
        text-transform: uppercase;
    }

    .author-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .author-details {
        flex: 1;
        min-width: 0;
    }

    .author-name {
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 2px 0;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .author-handle {
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Status Column */
    .status-cell {
        max-width: 450px;
    }

    .status-content {
        font-size: 13px;
        line-height: 1.5;
        color: var(--text-primary);
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-wrap: break-word;
    }

    .status-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 11px;
        color: var(--text-muted);
    }

    .status-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .status-meta-item svg {
        width: 13px;
        height: 13px;
        stroke: currentColor;
        fill: none;
    }

    .status-link {
        color: var(--accent-blue);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .status-link:hover {
        color: var(--primary-green);
        text-decoration: underline;
    }

    /* Stats Columns */
    .stat-cell {
        text-align: right;
        font-weight: 700;
        white-space: nowrap;
    }

    .stat-cell .stat-value {
        display: block;
        font-size: 16px;
        color: var(--text-primary);
        margin-bottom: 2px;
    }

    .stat-cell .stat-label {
        display: block;
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .views-cell .stat-value { color: var(--primary-green); }
    .retweets-cell .stat-value { color: var(--accent-blue); }

    /* Sentiment Badge */
    .sentiment-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
        min-width: 80px;
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
        background: linear-gradient(135deg, var(--bg-gray-100) 0%, var(--bg-gray-50) 100%);
        color: var(--text-secondary);
        border: 1px solid var(--border-gray);
    }

    .sentiment-percentage {
        font-size: 10px;
        opacity: 0.8;
        margin-left: 4px;
    }

    /* Export Button */
    .export-btn {
        padding: 10px 20px;
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: var(--shadow-sm);
    }

    .export-btn:hover {
        border-color: var(--primary-green);
        color: var(--primary-green);
        box-shadow: var(--shadow-md);
    }

    .export-btn svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
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

    /* Skeleton Loading */
    .skeleton-line {
        height: 16px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .skeleton-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
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
        stroke: currentColor;
        fill: none;
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

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease-out;
    }

    .modal-overlay.show,
    .modal-overlay.active {
        display: flex;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
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

    .modal-content {
        background: var(--bg-white);
        border-radius: 16px;
        width: 90%;
        max-width: 700px;
        max-height: 85vh;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        padding: 20px 24px;
        border-bottom: 2px solid var(--bg-gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .modal-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .modal-close {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: var(--bg-gray-50);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        color: var(--text-secondary);
    }

    .modal-close:hover {
        background: #ef4444;
        color: white;
    }

    .modal-close svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        fill: none;
        stroke-width: 2.5;
    }

    .modal-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: var(--border-gray);
        border-radius: 3px;
    }

    .modal-author-section {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--bg-gray-100);
        margin-bottom: 20px;
    }

    .modal-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        border: 3px solid var(--border-gray);
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 24px;
        flex-shrink: 0;
    }

    .modal-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .modal-author-info {
        flex: 1;
    }

    .modal-author-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 4px 0;
    }

    .modal-author-handle {
        font-size: 14px;
        color: var(--text-secondary);
        font-weight: 500;
        margin-bottom: 8px;
    }

    .modal-author-stats {
        display: flex;
        gap: 16px;
        font-size: 12px;
        color: var(--text-muted);
    }

    .modal-author-stats span {
        font-weight: 700;
        color: var(--text-primary);
    }

    .modal-post-content {
        font-size: 15px;
        line-height: 1.7;
        color: var(--text-primary);
        margin-bottom: 20px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .modal-post-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 0;
        border-top: 1px solid var(--bg-gray-100);
        border-bottom: 1px solid var(--bg-gray-100);
        margin-bottom: 20px;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .modal-post-meta svg {
        width: 14px;
        height: 14px;
        stroke: currentColor;
    }

    .modal-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .modal-stat-box {
        text-align: center;
        padding: 16px;
        background: var(--bg-gray-50);
        border-radius: 12px;
        border: 1px solid var(--border-gray);
    }

    .modal-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .modal-stat-value.views { color: var(--primary-green); }
    .modal-stat-value.retweets { color: var(--accent-blue); }

    .modal-stat-label {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modal-actions {
        display: flex;
        gap: 12px;
    }

    .modal-btn {
        flex: 1;
        padding: 12px 20px;
        border-radius: 10px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }

    .modal-btn.primary {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        color: white;
        border: none;
    }

    .modal-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
    }

    .modal-btn.secondary {
        background: var(--bg-white);
        color: var(--text-primary);
        border: 1px solid var(--border-gray);
    }

    .modal-btn.secondary:hover {
        background: var(--bg-gray-50);
        border-color: var(--primary-green);
        color: var(--primary-green);
    }

    .modal-btn svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
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

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 16px;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .filter-content {
            flex-direction: column;
            align-items: stretch;
        }
        .date-range-wrapper {
            flex-direction: column;
        }
        .apply-btn {
            width: 100%;
            justify-content: center;
        }
        
        .status-table th,
        .status-table td {
            padding: 12px 10px;
            font-size: 12px;
        }
        
        .author-cell {
            min-width: 150px;
        }
        
        .status-cell {
            max-width: 200px;
        }
        
        .status-content {
            -webkit-line-clamp: 2;
            font-size: 12px;
        }
        
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

        .date-preset {
            white-space: nowrap;
        }

        .date-picker-content {
            padding: 20px 16px;
        }

        .calendars-wrapper {
            flex-direction: column;
            gap: 16px;
        }

        .date-picker-header {
            flex-wrap: wrap;
        }

        .date-picker-trigger {
            max-width: 100%;
        }

        .calendar-day {
            font-size: 12px;
        }

        .weekday {
            font-size: 10px;
        }

        .cancel-btn,
        .apply-date-btn {
            flex: 1;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <div class="page-header">
        <h1>Most Viewed Posts</h1>
        <p>Top performing X (Twitter) posts by view count</p>
    </div>

    @if(!$projectId)
    <div class="alert alert-warning">
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>No project selected. Please select a project from the sidebar to view most viewed posts.</span>
    </div>
    @else

    <!-- Filter Card -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.x.most-status') }}">
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
            <!-- Sidebar with Presets -->
            <div class="date-picker-sidebar">
                <button type="button" class="date-preset" data-preset="today">Today</button>
                <button type="button" class="date-preset" data-preset="yesterday">Yesterday</button>
                <button type="button" class="date-preset" data-preset="last7days">Last 7 Days</button>
                <button type="button" class="date-preset" data-preset="last30days">Last 30 Days</button>
                <button type="button" class="date-preset" data-preset="thismonth">This Month</button>
                <button type="button" class="date-preset" data-preset="lastmonth">Last Month</button>
                <button type="button" class="date-preset active" data-preset="custom">Custom Range</button>
            </div>
            
            <!-- Calendar Content -->
            <div class="date-picker-content">
                <!-- Navigation Header -->
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
                
                <!-- Selected Date Display -->
                <div class="date-picker-display">
                    <span id="selectedRangeText">{{ $startDate }} to {{ $endDate }}</span>
                </div>
                
                <!-- Footer Buttons -->
                <div class="date-picker-footer">
                    <button type="button" class="cancel-btn">Cancel</button>
                    <button type="button" class="apply-date-btn" id="applyDatePicker">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Posts</div>
            <div id="totalPosts" class="stat-value">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Views</div>
            <div id="totalViews" class="stat-value">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg. Views per Post</div>
            <div id="avgViews" class="stat-value">0</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Top Post Views</div>
            <div id="topViews" class="stat-value">0</div>
        </div>
    </div>

    <!-- View Tabs & Export -->
    <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 24px;">
        <button class="export-btn" onclick="MostStatusLoader.exportCSV()">
            <svg viewBox="0 0 24 24">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download CSV
        </button>
    </div>

    <!-- Top Posts Chart -->
    <div class="table-container" style="margin-bottom: 24px;">
        <div style="padding: 20px 24px; border-bottom: 2px solid var(--bg-gray-50);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, rgba(3,128,71,0.1) 0%, rgba(3,128,71,0.05) 100%); display: flex; align-items: center; justify-content: center;">
                        <svg viewBox="0 0 24 24" style="width: 28px; height: 28px; color: var(--primary-green); fill: none; stroke: currentColor; stroke-width: 2;">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0;">Top 10 Posts by Views</h3>
                        <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Highest performing posts ranked by view count</p>
                    </div>
                </div>
                <button class="chart-toggle-btn" id="chartToggleBtn" onclick="MostStatusLoader.toggleChart()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                    <span id="chartToggleText">Hide Chart</span>
                </button>
            </div>
        </div>
        <div class="chart-body" id="chartBody" style="padding: 24px;">
            <canvas id="topPostsChart" style="max-height: 400px;"></canvas>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-wrapper">
            <!-- Loading State -->
            <table class="status-table" id="loadingTable" style="display: table;">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th class="text-right" style="width: 120px;">#Followers</th>
                        <th class="text-right" style="width: 120px;">#Views</th>
                        <th class="text-right" style="width: 100px;">#Retweets</th>
                        <th class="text-center" style="width: 130px;">Sentiment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" style="padding: 40px 20px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div class="skeleton-avatar"></div>
                                <div style="flex: 1;">
                                    <div class="skeleton-line" style="width: 30%;"></div>
                                    <div class="skeleton-line" style="width: 60%;"></div>
                                    <div class="skeleton-line" style="width: 45%;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Actual Table (hidden initially) -->
            <table class="status-table" id="statusTable" style="display: none;">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th class="text-right" style="width: 120px;">#Followers</th>
                        <th class="text-right" style="width: 120px;">#Views</th>
                        <th class="text-right" style="width: 100px;">#Retweets</th>
                        <th class="text-center" style="width: 130px;">Sentiment</th>
                    </tr>
                </thead>
                <tbody id="statusTableBody">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>

            <!-- Empty State -->
            <div id="emptyState" style="display: none;">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <h3>No Posts Found</h3>
                    <p>No most viewed posts available for the selected date range.</p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination" id="pagination" style="display: none;">
            <button class="pagination-btn" id="prevBtn" onclick="MostStatusLoader.changePage(-1)">
                ← Previous
            </button>
            <span class="pagination-info" id="pageInfo">Page 1 of 1</span>
            <button class="pagination-btn" id="nextBtn" onclick="MostStatusLoader.changePage(1)">
                Next →
            </button>
        </div>
    </div>

    <!-- Post Detail Modal -->
    <div class="modal-overlay" id="postModal" onclick="if(event.target === this) MostStatusLoader.closeModal()">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Post Details</h3>
                <button class="modal-close" onclick="MostStatusLoader.closeModal()">
                    <svg viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be injected here -->
            </div>
        </div>
    </div>

    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ========================================
// DATE PICKER JAVASCRIPT
// ========================================
(function() {
  'use strict';
  
  let selectedStartDate = null;
  let selectedEndDate = null;
  let currentMonth1 = new Date();
  let currentMonth2 = new Date();
  let selectingStart = true;

  document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('hiddenStartDate');
    const endDateInput = document.getElementById('hiddenEndDate');
    
    if (startDateInput && startDateInput.value) {
      selectedStartDate = new Date(startDateInput.value);
    } else {
      selectedEndDate = new Date();
      selectedStartDate = new Date();
      selectedStartDate.setDate(selectedStartDate.getDate() - 6);
    }
    
    if (endDateInput && endDateInput.value) {
      selectedEndDate = new Date(endDateInput.value);
    }
    
    currentMonth1 = new Date(selectedStartDate);
    currentMonth2 = new Date(selectedStartDate);
    currentMonth2.setMonth(currentMonth2.getMonth() + 1);
    
    renderCalendars();
    setupEventListeners();
  });

  function setupEventListeners() {
    const trigger = document.getElementById('datePickerTrigger');
    if (trigger) {
      trigger.addEventListener('click', openDatePicker);
    }

    const overlay = document.querySelector('.date-picker-overlay');
    if (overlay) {
      overlay.addEventListener('click', closeDatePicker);
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const modal = document.getElementById('datePickerModal');
        if (modal && modal.classList.contains('show')) {
          closeDatePicker();
        }
      }
    });

    document.querySelectorAll('.date-preset').forEach(btn => {
      btn.addEventListener('click', handlePresetClick);
    });

    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
      prevBtn.addEventListener('click', function() {
        currentMonth1.setMonth(currentMonth1.getMonth() - 1);
        currentMonth2.setMonth(currentMonth2.getMonth() - 1);
        renderCalendars();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function() {
        currentMonth1.setMonth(currentMonth1.getMonth() + 1);
        currentMonth2.setMonth(currentMonth2.getMonth() + 1);
        renderCalendars();
      });
    }

    const applyBtn = document.getElementById('applyDatePicker');
    if (applyBtn) {
      applyBtn.addEventListener('click', applyDateSelection);
    }

    const cancelBtn = document.querySelector('.cancel-btn');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', closeDatePicker);
    }
  }

  function openDatePicker() {
    document.getElementById('datePickerModal').classList.add('show');
    renderCalendars();
  }

  function closeDatePicker() {
    document.getElementById('datePickerModal').classList.remove('show');
  }

  function handlePresetClick(e) {
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');

    const preset = e.target.dataset.preset;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    switch(preset) {
      case 'today':
        selectedStartDate = new Date(today);
        selectedEndDate = new Date(today);
        break;
      case 'yesterday':
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 1);
        selectedEndDate = new Date(selectedStartDate);
        break;
      case 'last7days':
        selectedEndDate = new Date(today);
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 6);
        break;
      case 'last30days':
        selectedEndDate = new Date(today);
        selectedStartDate = new Date(today);
        selectedStartDate.setDate(today.getDate() - 29);
        break;
      case 'thismonth':
        selectedStartDate = new Date(today.getFullYear(), today.getMonth(), 1);
        selectedEndDate = new Date(today);
        break;
      case 'lastmonth':
        selectedStartDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        selectedEndDate = new Date(today.getFullYear(), today.getMonth(), 0);
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
    const end = formatDate(selectedEndDate);
    
    document.getElementById('hiddenStartDate').value = start;
    document.getElementById('hiddenEndDate').value = end;
    
    const displayElement = document.getElementById('dateRangeDisplay');
    if (displayElement) {
      displayElement.textContent = `${start} to ${end}`;
    }
    
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

    const year = month.getFullYear();
    const monthNum = month.getMonth();
    const firstDay = new Date(year, monthNum, 1);
    const lastDay = new Date(year, monthNum + 1, 0);
    const prevLastDay = new Date(year, monthNum, 0);
    
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                       'July', 'August', 'September', 'October', 'November', 'December'];
    const weekdays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
    
    let html = `
      <div class="calendar-month">${monthNames[monthNum]} ${year}</div>
      <div class="calendar-weekdays">
        ${weekdays.map(day => `<div class="weekday">${day}</div>`).join('')}
      </div>
      <div class="calendar-days">
    `;
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Add empty cells for days before the first day of the month
    const firstDayOfWeek = firstDay.getDay();
    for (let i = 0; i < firstDayOfWeek; i++) {
      const prevMonthDay = prevLastDay.getDate() - (firstDayOfWeek - 1 - i);
      html += `<button type="button" class="calendar-day other-month" disabled>${prevMonthDay}</button>`;
    }
    
    // Add all days of the current month
    for (let day = 1; day <= lastDay.getDate(); day++) {
      const date = new Date(year, monthNum, day);
      date.setHours(0, 0, 0, 0);
      
      const dateStr = formatDate(date);
      let classes = 'calendar-day';
      
      if (isSameDay(date, today)) classes += ' today';
      if (date > today) classes += ' disabled';
      
      if (selectedStartDate && selectedEndDate) {
        if (isSameDay(date, selectedStartDate)) {
          classes += ' selected range-start';
        } else if (isSameDay(date, selectedEndDate)) {
          classes += ' selected range-end';
        } else if (date > selectedStartDate && date < selectedEndDate) {
          classes += ' in-range';
        }
      }
      
      const disabled = date > today ? 'disabled' : '';
      html += `<button type="button" class="${classes}" data-date="${dateStr}" ${disabled}>${day}</button>`;
    }
    
    // Add empty cells for days after the last day of the month
    const lastDayOfWeek = lastDay.getDay();
    const remainingCells = lastDayOfWeek === 6 ? 0 : 6 - lastDayOfWeek;
    for (let i = 1; i <= remainingCells; i++) {
      html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    }
    
    html += '</div>';
    calendar.innerHTML = html;
    
    // Add click listeners to enabled date buttons
    calendar.querySelectorAll('.calendar-day:not(.other-month):not(.disabled)').forEach(btn => {
      btn.addEventListener('click', handleDateClick);
    });
  }

  function handleDateClick(e) {
    const dateStr = e.target.dataset.date;
    const date = new Date(dateStr);
    date.setHours(0, 0, 0, 0);
    
    document.querySelectorAll('.date-preset').forEach(b => b.classList.remove('active'));
    const customPreset = document.querySelector('[data-preset="custom"]');
    if (customPreset) customPreset.classList.add('active');
    
    if (selectingStart || date < selectedStartDate) {
      selectedStartDate = date;
      selectedEndDate = date;
      selectingStart = false;
    } else {
      if (date >= selectedStartDate) {
        selectedEndDate = date;
      } else {
        selectedEndDate = selectedStartDate;
        selectedStartDate = date;
      }
      selectingStart = true;
    }
    
    updateDateDisplay();
    renderCalendars();
  }

  function updateDateDisplay() {
    if (!selectedStartDate || !selectedEndDate) return;
    
    const start = formatDate(selectedStartDate);
    const end = formatDate(selectedEndDate);
    
    const displayElement = document.getElementById('selectedRangeText');
    if (displayElement) {
      displayElement.textContent = `${start} to ${end}`;
    }
  }

  function formatDate(date) {
    if (!date) return '';
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  function isSameDay(date1, date2) {
    if (!date1 || !date2) return false;
    
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
  }
})();

// ========================================
// MOST STATUS LOADER
// ========================================
const MostStatusLoader = {
    projectId: '{{ $projectId ?? "" }}',
    startDate: '{{ $startDate ?? "" }}',
    endDate: '{{ $endDate ?? "" }}',
    allPosts: [],
    currentPage: 1,
    postsPerPage: 20,
    currentView: 'table',

    async init() {
        if (!this.projectId) return;
        
        try {
            await this.loadData();
        } catch (error) {
            console.error('❌ Failed to load most status data:', error);
            this.showError();
        }
    },

    async loadData() {
        const url = `/mk/api/x/most-status?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`;
        
        const response = await fetch(url);
        const result = await response.json();

        console.log('📊 Most Status API response:', result);

        if (!result.success) {
            throw new Error(result.error || 'Failed to load data');
        }

        this.allPosts = result.data || [];
        
        this.updateStats(this.allPosts);
        this.renderChart();
        this.renderTable();
    },

    renderChart() {
        const canvas = document.getElementById('topPostsChart');
        if (!canvas) return;

        const top10 = this.allPosts.slice(0, 10);
        
        if (!top10.length) return;

        const ctx = canvas.getContext('2d');
        
        if (this.chart) {
            this.chart.destroy();
        }

        const labels = top10.map((post, idx) => {
            const author = post.author?.scr_name || post.name || 'Unknown';
            return `#${idx + 1} @${author}`;
        });

        const data = top10.map(post => post.view_cnt || 0);

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Views',
                    data: data,
                    backgroundColor: 'rgba(3, 128, 71, 0.8)',
                    borderColor: '#038047',
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1a202c',
                        titleFont: { family: 'Poppins', size: 13, weight: '600' },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.parsed.x.toLocaleString() + ' views';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: '#e2e8f0',
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: 'Poppins', size: 11 },
                            color: '#64748b',
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                return value;
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { family: 'Poppins', size: 11, weight: '600' },
                            color: '#1a202c'
                        }
                    }
                }
            }
        });
    },

    viewPost(index) {
        const startIdx = (this.currentPage - 1) * this.postsPerPage;
        const post = this.allPosts[startIdx + index];
        
        if (!post) return;

        const authorName = this.escapeHtml(post.author?.name || post.name || 'Unknown User');
        const authorHandle = post.author?.scr_name || post.name || 'unknown';
        const initials = this.getInitials(authorName);
        
        let avatarHtml = initials;
        const hasValidAvatar = post.avatar_url && 
                               !post.avatar_url.startsWith('/external') && 
                               post.avatar_url !== '/images/default-avatar.png';
        
        if (hasValidAvatar) {
            avatarHtml = `<img src="${post.avatar_url}" alt="${authorName}" onerror="this.parentElement.innerHTML='${initials}'">`;
        } else {
            const username = authorHandle.replace('@', '');
            avatarHtml = `<img src="https://unavatar.io/twitter/${username}" alt="${authorName}" onerror="this.parentElement.innerHTML='${initials}'">`;
        }

        const content = this.escapeHtml(post.content || '');
        const viewCount = post.view_cnt || 0;
        const rtCount = post.rt || 0;
        const followers = post.author?.flw_cnt || 0;
        const sentimentClass = post.sentiment_str.toLowerCase();
        const sentimentPrec = post.sentiment_prec || 0;
        const date = this.formatDate(post.date_created);
        const twitterUrl = post.sub_id ? `https://twitter.com/i/web/status/${post.sub_id}` : '#';

        const modalBody = document.getElementById('modalBody');
        modalBody.innerHTML = `
            <div class="modal-author-section">
                <div class="modal-avatar">${avatarHtml}</div>
                <div class="modal-author-info">
                    <h4 class="modal-author-name">${authorName}</h4>
                    <div class="modal-author-handle">@${authorHandle}</div>
                    <div class="modal-author-stats">
                        <div><span>${this.formatNumber(followers)}</span> Followers</div>
                    </div>
                </div>
            </div>

            <div class="modal-post-content">${content}</div>

            <div class="modal-post-meta">
                <svg viewBox="0 0 24 24" style="stroke:currentColor;fill:none;">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                ${date}
            </div>

            <div class="modal-stats-grid">
                <div class="modal-stat-box">
                    <div class="modal-stat-value views">${this.formatNumber(viewCount)}</div>
                    <div class="modal-stat-label">Views</div>
                </div>
                <div class="modal-stat-box">
                    <div class="modal-stat-value retweets">${this.formatNumber(rtCount)}</div>
                    <div class="modal-stat-label">Retweets</div>
                </div>
                <div class="modal-stat-box">
                    <span class="sentiment-badge ${sentimentClass}" style="display:block;margin:0;">
                        ${post.sentiment_str}
                        ${sentimentPrec > 0 ? `<span class="sentiment-percentage">${sentimentPrec.toFixed(0)}%</span>` : ''}
                    </span>
                    <div class="modal-stat-label" style="margin-top:8px;">Sentiment</div>
                </div>
            </div>

            ${post.sub_id ? `
            <div class="modal-actions">
                <a href="${twitterUrl}" target="_blank" class="modal-btn primary">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    View on X (Twitter)
                </a>
                <button onclick="MostStatusLoader.closeModal()" class="modal-btn secondary">
                    Close
                </button>
            </div>
            ` : ''}
        `;

        document.getElementById('postModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    },

    openModal(index) {
        this.viewPost(index);
        
        this._escKeyHandler = (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        };
        document.addEventListener('keydown', this._escKeyHandler);
    },

    closeModal() {
        const modal = document.getElementById('postModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
        
        if (this._escKeyHandler) {
            document.removeEventListener('keydown', this._escKeyHandler);
            this._escKeyHandler = null;
        }
    },

    toggleChart() {
        const chartBody = document.getElementById('chartBody');
        const toggleBtn = document.getElementById('chartToggleBtn');
        const toggleText = document.getElementById('chartToggleText');
        
        if (chartBody.classList.contains('hidden')) {
            chartBody.classList.remove('hidden');
            toggleBtn.classList.remove('collapsed');
            toggleText.textContent = 'Hide Chart';
        } else {
            chartBody.classList.add('hidden');
            toggleBtn.classList.add('collapsed');
            toggleText.textContent = 'Show Chart';
        }
    },

    updateStats(posts) {
        const totalPosts = posts.length;
        const totalViews = posts.reduce((sum, p) => sum + p.view_cnt, 0);
        const avgViews = totalPosts > 0 ? Math.round(totalViews / totalPosts) : 0;
        const topViews = posts.length > 0 ? posts[0].view_cnt : 0;

        document.getElementById('totalPosts').textContent = totalPosts.toLocaleString();
        document.getElementById('totalViews').textContent = totalViews.toLocaleString();
        document.getElementById('avgViews').textContent = avgViews.toLocaleString();
        document.getElementById('topViews').textContent = topViews.toLocaleString();
    },

    renderTable() {
        const loadingTable = document.getElementById('loadingTable');
        const statusTable = document.getElementById('statusTable');
        const emptyState = document.getElementById('emptyState');
        const pagination = document.getElementById('pagination');

        if (!this.allPosts.length) {
            loadingTable.style.display = 'none';
            statusTable.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        const startIdx = (this.currentPage - 1) * this.postsPerPage;
        const endIdx = startIdx + this.postsPerPage;
        const currentPosts = this.allPosts.slice(startIdx, endIdx);

        const tbody = document.getElementById('statusTableBody');
        tbody.innerHTML = currentPosts.map((post, idx) => this.createTableRow(post, startIdx + idx + 1)).join('');

        loadingTable.style.display = 'none';
        statusTable.style.display = 'table';
        emptyState.style.display = 'none';

        this.updatePagination();
        pagination.style.display = 'flex';
    },

    createTableRow(post, rank) {
        const sentimentClass = post.sentiment_str.toLowerCase();
        const authorName = this.escapeHtml(post.author?.name || post.name || 'Unknown User');
        const authorHandle = post.author?.scr_name || post.name || 'unknown';
        
        const initials = this.getInitials(authorName);
        
        let avatarHtml = '';
        const hasValidAvatar = post.avatar_url && 
                               !post.avatar_url.startsWith('/external') && 
                               post.avatar_url !== '/images/default-avatar.png';
        
        if (hasValidAvatar) {
            avatarHtml = `<img src="${post.avatar_url}" alt="${authorName}" onerror="this.parentElement.innerHTML='${initials}'">`;
        } else {
            const username = authorHandle.replace('@', '');
            avatarHtml = `<img src="https://unavatar.io/twitter/${username}" alt="${authorName}" onerror="this.parentElement.innerHTML='${initials}'">`;
        }
        
        const content = this.escapeHtml(post.content || '');
        const viewCount = post.view_cnt || 0;
        const rtCount = post.rt || 0;
        const followers = post.author?.flw_cnt || 0;
        const sentimentPrec = post.sentiment_prec || 0;
        const date = this.formatDate(post.date_created);

        return `
            <tr>
                <td class="rank-cell">${rank}</td>
                
                <td class="author-cell">
                    <div class="author-info">
                        <div class="author-avatar">${avatarHtml}</div>
                        <div class="author-details">
                            <div class="author-name" title="${authorName}">${authorName}</div>
                            <div class="author-handle">@${authorHandle}</div>
                        </div>
                    </div>
                </td>
                
                <td class="status-cell">
                    <div class="status-content">${content}</div>
                    <div class="status-meta">
                        <span class="status-meta-item">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            ${date}
                        </span>
                        ${post.sub_id ? `
                        <a href="javascript:void(0)" onclick="MostStatusLoader.viewPost(${rank - 1})" class="status-link">
                            <svg viewBox="0 0 24 24" style="width:13px;height:13px;display:inline;vertical-align:middle;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            View
                        </a>
                        ` : ''}
                    </div>
                </td>
                
                <td class="stat-cell">
                    <span class="stat-value">${this.formatNumber(followers)}</span>
                    <span class="stat-label">Followers</span>
                </td>
                
                <td class="stat-cell views-cell">
                    <span class="stat-value">${this.formatNumber(viewCount)}</span>
                    <span class="stat-label">Views</span>
                </td>
                
                <td class="stat-cell retweets-cell">
                    <span class="stat-value">${this.formatNumber(rtCount)}</span>
                    <span class="stat-label">Retweets</span>
                </td>
                
                <td class="text-center">
                    <span class="sentiment-badge ${sentimentClass}">
                        ${post.sentiment_str}
                        ${sentimentPrec > 0 ? `<span class="sentiment-percentage">${sentimentPrec.toFixed(0)}%</span>` : ''}
                    </span>
                </td>
            </tr>`;
    },

    getInitials(name) {
        if (!name || name === 'Unknown User') return '?';
        const parts = name.trim().split(/\s+/);
        if (parts.length === 1) {
            return parts[0].substring(0, 2).toUpperCase();
        }
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    },

    updatePagination() {
        const totalPages = Math.ceil(this.allPosts.length / this.postsPerPage);
        const pageInfo = document.getElementById('pageInfo');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        pageInfo.textContent = `Page ${this.currentPage} of ${totalPages}`;
        prevBtn.disabled = this.currentPage === 1;
        nextBtn.disabled = this.currentPage === totalPages;
    },

    changePage(direction) {
        const totalPages = Math.ceil(this.allPosts.length / this.postsPerPage);
        const newPage = this.currentPage + direction;

        if (newPage >= 1 && newPage <= totalPages) {
            this.currentPage = newPage;
            this.renderTable();
            
            document.querySelector('.table-container').scrollIntoView({ behavior: 'smooth' });
        }
    },

    exportCSV() {
        if (!this.allPosts.length) {
            alert('No data to export');
            return;
        }

        const headers = ['Rank', 'Author', 'Handle', 'Status', 'Followers', 'Views', 'Retweets', 'Sentiment', 'Sentiment %', 'Date'];
        const rows = this.allPosts.map((post, idx) => [
            idx + 1,
            `"${(post.author?.name || post.name || 'Unknown').replace(/"/g, '""')}"`,
            `"${(post.author?.scr_name || post.name || 'unknown').replace(/"/g, '""')}"`,
            `"${(post.content || '').replace(/"/g, '""')}"`,
            post.author?.flw_cnt || 0,
            post.view_cnt || 0,
            post.rt || 0,
            post.sentiment_str || 'Neutral',
            (post.sentiment_prec || 0).toFixed(2),
            post.date_created || ''
        ]);

        const csv = [
            headers.join(','),
            ...rows.map(row => row.join(','))
        ].join('\n');

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', `most_viewed_posts_${this.startDate}_to_${this.endDate}.csv`);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    },

    formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    formatNumber(num) {
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
        return num.toLocaleString();
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    showError() {
        const loadingTable = document.getElementById('loadingTable');
        const emptyState = document.getElementById('emptyState');
        
        loadingTable.style.display = 'none';
        emptyState.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" style="color:#ef4444;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <h3>Failed to Load Data</h3>
                <p>Unable to fetch most viewed posts. Please try again later.</p>
            </div>`;
        emptyState.style.display = 'block';
        
        document.getElementById('totalPosts').textContent = '0';
        document.getElementById('totalViews').textContent = '0';
        document.getElementById('avgViews').textContent = '0';
        document.getElementById('topViews').textContent = '0';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    MostStatusLoader.init();
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            MostStatusLoader.closeModal();
        }
    });
});
</script>
@endsection