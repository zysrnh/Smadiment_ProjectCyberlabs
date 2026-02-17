@extends('mk.layouts.app')

@section('title', 'Shared URLs - SMADIMENT')

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
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-label svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        fill: none;
    }

    .date-range-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
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

    .date-picker-overlay-inner {
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
        animation: slideUpModal 0.3s ease-out;
    }

    @keyframes slideUpModal {
        from { 
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to { 
            opacity: 1;
            transform: translateY(0) scale(1);
        }
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

    /* Stats Grid - MATCHING MOST RETWEETS */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
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

    .stat-card:hover::before { opacity: 1; }

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
        position: relative;
    }

    .stat-icon-wrapper::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 16px;
        padding: 4px;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stat-card:hover .stat-icon-wrapper::after { opacity: 0.5; }

    .stat-icon {
        width: 28px;
        height: 28px;
        color: var(--primary-green);
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value-wrapper {
        display: flex;
        align-items: baseline;
        gap: 12px;
        margin-bottom: 16px;
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
        margin-top: 8px;
    }

    .stat-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        border-radius: 10px;
        transition: width 1s ease-out;
        width: 0%;
    }

    /* Chart Card - MATCHING MOST RETWEETS */
    .charts-section {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 28px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-gray);
        transition: all 0.3s;
    }

    .chart-card:hover {
        box-shadow: var(--shadow-md);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--bg-gray-50);
    }

    .chart-title-group h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 6px 0;
    }

    .chart-subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
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

    .chart-container {
        position: relative;
        height: 320px;
        overflow: hidden;
        transition: height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
    }

    .chart-container.hidden {
        height: 0 !important;
        opacity: 0;
    }

    /* Export Button */
    .export-btn-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 24px;
    }

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

    /* Table Section - MATCHING MOST RETWEETS */
    .table-section {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 28px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-gray);
        margin-bottom: 24px;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--bg-gray-50);
        gap: 16px;
        flex-wrap: wrap;
    }

    .table-title h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 6px 0;
    }

    .table-title p {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
    }

    .table-wrapper { overflow-x: auto; }

    /* Data Table - MATCHING MOST RETWEETS */
    .url-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
    }

    .url-table thead tr {
        background: var(--bg-white);
        border-bottom: 1px solid var(--border-gray);
    }

    .url-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        border-bottom: 1px solid var(--border-gray);
    }

    .url-table th:first-child { padding-left: 20px; }
    .url-table th:last-child { padding-right: 20px; }
    .url-table th.text-center { text-align: center; }
    .url-table th.text-right { text-align: right; }

    .url-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s;
        background: var(--bg-white);
    }

    .url-table tbody tr:hover { background: #fafbfc; }
    .url-table tbody tr:last-child { border-bottom: none; }

    .url-table td {
        padding: 14px 16px;
        font-size: 12px;
        color: var(--text-primary);
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .url-table td:first-child { padding-left: 20px; }
    .url-table td:last-child { padding-right: 20px; }
    .url-table tbody tr:last-child td { border-bottom: none; }

    /* Rank Column */
    .rank-cell {
        font-weight: 700;
        color: var(--primary-green);
        font-size: 15px;
        text-align: center;
        width: 60px;
    }

    /* Preview Column */
    .preview-cell { width: 70px; }

    .url-preview {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid var(--border-gray);
        background: var(--bg-gray-100);
        display: block;
        flex-shrink: 0;
    }

    .url-preview-placeholder {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--bg-gray-100) 0%, var(--border-gray) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .url-preview-placeholder svg {
        width: 22px;
        height: 22px;
        stroke: var(--text-muted);
        fill: none;
    }

    /* URL Column */
    .url-cell { min-width: 360px; max-width: 480px; }

    .url-info { display: flex; flex-direction: column; gap: 4px; }

    .url-hostname {
        font-size: 12px;
        font-weight: 700;
        color: var(--primary-green);
        text-transform: lowercase;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .url-hostname svg {
        width: 12px;
        height: 12px;
        stroke: currentColor;
        fill: none;
        flex-shrink: 0;
    }

    .url-text {
        font-size: 11px;
        color: var(--text-secondary);
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 420px;
        font-family: 'Courier New', monospace;
        line-height: 1.4;
    }

    .url-link {
        color: var(--accent-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 11px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 0;
    }

    .url-link:hover {
        color: var(--primary-green);
        text-decoration: underline;
    }

    .url-link svg {
        width: 12px;
        height: 12px;
        stroke: currentColor;
        fill: none;
    }

    /* Type Badge */
    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .type-badge svg {
        width: 12px;
        height: 12px;
        stroke: currentColor;
        fill: none;
    }

    .type-image {
        background: #ede9fe;
        color: #6d28d9;
        border: 1px solid #c4b5fd;
    }

    .type-video {
        background: #fce7f3;
        color: #be185d;
        border: 1px solid #f9a8d4;
    }

    .type-article {
        background: #dbeafe;
        color: #1d4ed8;
        border: 1px solid #93c5fd;
    }

    .type-other {
        background: var(--bg-gray-100);
        color: var(--text-secondary);
        border: 1px solid var(--border-gray);
    }

    /* Frequency Column */
    .freq-cell {
        text-align: right;
        white-space: nowrap;
    }

    .freq-value {
        display: block;
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-green);
        margin-bottom: 0;
        line-height: 1.2;
    }

    .freq-label {
        display: block;
        font-size: 9px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-top: 2px;
    }

    /* Bar indicator */
    .freq-bar-wrapper {
        margin-top: 6px;
    }

    .freq-bar-track {
        width: 100%;
        height: 3px;
        background: var(--bg-gray-100);
        border-radius: 2px;
        overflow: hidden;
    }

    .freq-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--accent-blue) 100%);
        border-radius: 2px;
        transition: width 0.6s ease;
    }

    /* Skeleton Loading - MATCHING MOST RETWEETS */
    .loading-skeleton {
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s ease-in-out infinite;
        border-radius: 8px;
    }

    @keyframes loading {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .skeleton-text {
        height: 44px;
        margin-bottom: 8px;
    }

    .skeleton-line {
        height: 16px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    .skeleton-box {
        width: 56px;
        height: 56px;
        border-radius: 10px;
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
    .page-btn {
  width: 36px; height: 36px; border-radius: 10px;
  border: 1px solid var(--border-gray); background: var(--bg-white);
  color: var(--text-primary); font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all 0.2s;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Poppins', sans-serif;
}
.page-btn:hover:not(:disabled) { border-color: var(--primary-green); color: var(--primary-green); background: rgba(3,128,71,0.05); }
.page-btn.active { background: var(--primary-green); color: white; border-color: var(--primary-green); }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

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

    /* Lazy Load */
    .lazy-loading-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: rgba(3, 128, 71, 0.1);
        color: var(--primary-green);
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 10;
    }

    .spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(3, 128, 71, 0.2);
        border-top-color: var(--primary-green);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    [data-lazy-load] {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    [data-lazy-load].loaded {
        opacity: 1;
        transform: translateY(0);
    }

    .data-loaded {
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to   { opacity: 1; transform: scale(1); }
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 1024px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-range-wrapper { flex-direction: column; }
        .apply-btn { width: 100%; justify-content: center; }
        .url-table { min-width: 900px; }

        .date-picker-trigger {
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .url-cell { min-width: 240px; }
        .url-table th, .url-table td { padding: 12px 14px; }
        .url-table th:first-child, .url-table td:first-child { padding-left: 16px; }
        .url-table th:last-child, .url-table td:last-child { padding-right: 16px; }
        .url-preview, .url-preview-placeholder { width: 48px; height: 48px; }
        .freq-value { font-size: 18px; }

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

    @media (max-width: 640px) {
        .stat-value { font-size: 28px; }
        .chart-container { height: 250px; }
        .page-header h1 { font-size: 24px; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <h1>Shared URLs</h1>
        <p>Most frequently shared URLs and media links from X (Twitter) posts</p>
    </div>

    @if(!$projectId)
    <div class="alert alert-warning">
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;flex-shrink:0;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>No project selected. Please select a project from the sidebar to view shared URLs.</span>
    </div>
    @else

    <!-- Filter Card -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.x.shared-urls') }}">
            <input type="hidden" name="project_id" value="{{ $projectId }}">
            <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
            <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">
            
            <div class="filter-content">
                <div class="filter-label">
                    <svg viewBox="0 0 24 24">
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
        <div class="date-picker-overlay-inner"></div>
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
        <div class="stat-card" data-lazy-load="urlStats">
            <div class="stat-header">
                <div class="stat-icon-wrapper">
                    <svg class="stat-icon" viewBox="0 0 24 24">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Total URLs</div>
            <div id="statTotalUrls" class="stat-value-wrapper">
                <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar"></div>
            </div>
        </div>

        <div class="stat-card" data-lazy-load="urlStats">
            <div class="stat-header">
                <div class="stat-icon-wrapper">
                    <svg class="stat-icon" viewBox="0 0 24 24">
                        <path d="M15.5 9.5v13a1 1 0 0 1 -1 1h-13a1 1 0 0 1 -1 -1v-13a1 1 0 0 1 1 -1h4"/>
                        <path d="m19.5 0.499 4 4 -4 4"/>
                        <path d="M23.5 4.5H14A5.5 5.5 0 0 0 8.5 10v1.5"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Total Shares</div>
            <div id="statTotalShares" class="stat-value-wrapper">
                <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar"></div>
            </div>
        </div>

        <div class="stat-card" data-lazy-load="urlStats">
            <div class="stat-header">
                <div class="stat-icon-wrapper">
                    <svg class="stat-icon" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Unique Hostnames</div>
            <div id="statUniqueHosts" class="stat-value-wrapper">
                <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar"></div>
            </div>
        </div>

        <div class="stat-card" data-lazy-load="urlStats">
            <div class="stat-header">
                <div class="stat-icon-wrapper">
                    <svg class="stat-icon" viewBox="0 0 24 24">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
            </div>
            <div class="stat-label">Most Shared</div>
            <div id="statTopFreq" class="stat-value-wrapper">
                <div class="loading-skeleton skeleton-text" style="width:140px;"></div>
            </div>
            <div class="stat-progress">
                <div class="stat-progress-bar"></div>
            </div>
        </div>
    </div>

    <!-- Export Button -->
    <div class="export-btn-wrapper">
        <button class="export-btn" onclick="SharedUrlsLoader.exportCSV()">
            <svg viewBox="0 0 24 24">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Export to CSV
        </button>
    </div>

    <!-- Top URLs Chart -->
    <div class="charts-section">
        <div class="chart-card" data-lazy-load="urlChart">
            <div class="chart-header">
                <div class="chart-title-group">
                    <h3>Top 10 Most Shared URLs</h3>
                    <p class="chart-subtitle">Ranked by share frequency across X posts</p>
                </div>
                <button class="chart-toggle-btn" id="chartToggleBtn" onclick="SharedUrlsLoader.toggleChart()">
                    <svg viewBox="0 0 24 24">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                    <span id="chartToggleText">Hide Chart</span>
                </button>
            </div>
            <div class="chart-container" id="chartContainer">
                <div id="chartLoading" class="loading-skeleton" style="height: 100%;"></div>
                <canvas id="topUrlsChart" style="display: none;"></canvas>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-section" data-lazy-load="urlTable">
        <div class="table-header">
            <div class="table-title">
                <h3>Shared URLs Ranking</h3>
                <p>Complete list of all shared links sorted by frequency</p>
            </div>
        </div>

        <div class="table-wrapper">
            <!-- Loading State -->
            <table class="url-table" id="loadingTable">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th style="width:80px;">Preview</th>
                        <th>URL</th>
                        <th style="width:120px;">Type</th>
                        <th class="text-right" style="width:140px;">Frequency</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td><div class="skeleton-line" style="width:24px;height:16px;margin:0;"></div></td>
                        <td><div class="skeleton-box"></div></td>
                        <td>
                            <div class="skeleton-line" style="width:30%;margin-bottom:8px;"></div>
                            <div class="skeleton-line" style="width:70%;margin-bottom:8px;"></div>
                            <div class="skeleton-line" style="width:45%;margin:0;"></div>
                        </td>
                        <td><div class="skeleton-line" style="width:70px;height:24px;margin:0;border-radius:20px;"></div></td>
                        <td><div class="skeleton-line" style="width:60px;height:24px;margin:0 0 0 auto;"></div></td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Actual Table -->
            <table class="url-table" id="urlTable" style="display: none;">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th style="width:80px;">Preview</th>
                        <th>URL</th>
                        <th style="width:120px;">Type</th>
                        <th class="text-right" style="width:140px;">Frequency</th>
                    </tr>
                </thead>
                <tbody id="urlTableBody"></tbody>
            </table>

            <!-- Empty State -->
            <div id="emptyState" style="display: none;">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                    <h3>No Shared URLs Found</h3>
                    <p>No shared URL data available for the selected date range.</p>
                </div>
            </div>
        </div>

        <div id="paginationWrapper" class="pagination" style="display: none; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;"></div>
    </div>

    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ========================================
// DATE PICKER JAVASCRIPT (Same as Geographic)
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

    const overlay = document.querySelector('.date-picker-overlay-inner');
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
// SHARED URLS LOADER
// ========================================
const SharedUrlsLoader = {
    projectId: '{{ $projectId ?? "" }}',
    startDate: '{{ $startDate ?? "" }}',
    endDate:   '{{ $endDate ?? "" }}',
    allUrls:   [],
    currentPage: 1,
    rowsPerPage: 20,
    chart: null,
    maxFreq: 1,
    dataLoaded: false,
    chartLoaded: false,

    async init() {
        if (!this.projectId) return;
        this.setupLazyLoad();
    },

    setupLazyLoad() {
        const lazyLoadConfig = { rootMargin: '50px', threshold: 0.01 };
        const loadedComponents = new Set();

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.dataset.lazyLoad;
                    if (!loadedComponents.has(id)) {
                        loadedComponents.add(id);
                        this.addLoadingBadge(entry.target);
                        
                        if (id === 'urlStats' || id === 'urlTable') {
                            this.loadData();
                        }
                        if (id === 'urlChart') {
                            this.loadChart();
                        }
                        
                        observer.unobserve(entry.target);
                    }
                }
            });
        }, lazyLoadConfig);

        document.querySelectorAll('[data-lazy-load]').forEach(el => observer.observe(el));
    },

    addLoadingBadge(card) {
        if (!card || card.querySelector('.lazy-loading-badge')) return;
        const badge = document.createElement('div');
        badge.className = 'lazy-loading-badge';
        badge.innerHTML = '<div class="spinner"></div><span>Loading...</span>';
        card.style.position = 'relative';
        card.appendChild(badge);
    },

    removeLoadingBadge(card) {
        if (!card) return;
        const badge = card.querySelector('.lazy-loading-badge');
        if (badge) {
            badge.style.opacity = '0';
            setTimeout(() => badge.remove(), 300);
        }
    },

    animateProgress(card, pct) {
        const bar = card.querySelector('.stat-progress-bar');
        if (bar) setTimeout(() => bar.style.width = Math.min(pct, 100) + '%', 100);
    },

    async loadData() {
        if (this.dataLoaded) return;
        this.dataLoaded = true;

        const statCards = document.querySelectorAll('[data-lazy-load="urlStats"]');

        try {
            const url = `/mk/api/x/shared-urls?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`;
            const response = await fetch(url);
            const result   = await response.json();

            if (!result.success) throw new Error(result.error || 'API error');

            this.allUrls = result.data || [];
            this.maxFreq = this.allUrls.length > 0 ? (this.allUrls[0].freq || 1) : 1;

            this.updateStats();
            this.renderTable();

            const pcts = [80, 90, 65, 100];
            statCards.forEach((c, i) => {
                this.animateProgress(c, pcts[i] ?? 70);
            });

        } catch (error) {
            console.error('Failed to load shared URLs:', error);
            this.showError();
        } finally {
            statCards.forEach(c => {
                this.removeLoadingBadge(c);
                c.classList.add('loaded');
            });
            document.querySelector('[data-lazy-load="urlTable"]')?.classList.add('loaded');
        }
    },

    async loadChart() {
        if (this.chartLoaded) return;
        this.chartLoaded = true;

        const card = document.querySelector('[data-lazy-load="urlChart"]');

        try {
            const url = `/mk/api/x/shared-urls?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`;
            const response = await fetch(url);
            const result   = await response.json();

            if (result.success && result.data && result.data.length > 0) {
                this.renderChart(result.data);
            }
        } catch (error) {
            console.error('Failed to load chart:', error);
        } finally {
            this.removeLoadingBadge(card);
            card.classList.add('loaded');
        }
    },

    updateStats() {
        const total       = this.allUrls.length;
        const totalShares = this.allUrls.reduce((s, u) => s + parseInt(u.freq || 0), 0);
        const hosts       = new Set(this.allUrls.map(u => this.extractHostname(u.url || ''))).size;
        const topFreq     = this.allUrls.length > 0 ? parseInt(this.allUrls[0].freq || 0) : 0;

        document.getElementById('statTotalUrls').innerHTML   = `<div class="stat-value">${this.formatNumber(total)}</div>`;
        document.getElementById('statTotalShares').innerHTML = `<div class="stat-value">${this.formatNumber(totalShares)}</div>`;
        document.getElementById('statUniqueHosts').innerHTML = `<div class="stat-value">${this.formatNumber(hosts)}</div>`;
        document.getElementById('statTopFreq').innerHTML     = `<div class="stat-value">${this.formatNumber(topFreq)}</div>`;

        document.getElementById('statTotalUrls').classList.add('data-loaded');
        document.getElementById('statTotalShares').classList.add('data-loaded');
        document.getElementById('statUniqueHosts').classList.add('data-loaded');
        document.getElementById('statTopFreq').classList.add('data-loaded');
    },

    renderChart(data) {
        const canvas = document.getElementById('topUrlsChart');
        const loading = document.getElementById('chartLoading');
        if (!canvas) return;

        const top10 = data.slice(0, 10);
        if (!top10.length) return;

        if (this.chart) this.chart.destroy();

        const ctx = canvas.getContext('2d');

        // Build labels: only show domain (hostname) without path
        const labels = top10.map((u, i) => {
            const host = this.extractHostname(u.url || '');
            return `#${i + 1} ${host}`;
        });
        const chartData = top10.map(u => parseInt(u.freq || 0));

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Shares',
                    data: chartData,
                    backgroundColor: 'rgba(3, 128, 71, 0.8)',
                    borderColor: '#038047',
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a202c',
                        titleFont: { family: 'Poppins', size: 13, weight: '600' },
                        bodyFont:  { family: 'Poppins', size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => ' ' + ctx.parsed.x.toLocaleString() + ' shares'
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#e2e8f0', drawBorder: false },
                        ticks: {
                            font: { family: 'Poppins', size: 11 },
                            color: '#64748b',
                            callback: v => v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { family: 'Poppins', size: 11, weight: '600' }, color: '#1a202c' }
                    }
                }
            }
        });

        loading.style.display = 'none';
        canvas.style.display = 'block';
    },

    renderTable() {
        const loadingTable = document.getElementById('loadingTable');
        const urlTable     = document.getElementById('urlTable');
        const emptyState   = document.getElementById('emptyState');
        const pagination   = document.getElementById('pagination');

        if (!this.allUrls.length) {
            loadingTable.style.display = 'none';
            urlTable.style.display     = 'none';
            emptyState.style.display   = 'block';
            return;
        }

        const startIdx   = (this.currentPage - 1) * this.rowsPerPage;
        const pageUrls   = this.allUrls.slice(startIdx, startIdx + this.rowsPerPage);
        const tbody      = document.getElementById('urlTableBody');

        tbody.innerHTML = pageUrls.map((u, i) => this.createRow(u, startIdx + i + 1)).join('');

        loadingTable.style.display = 'none';
        urlTable.style.display     = 'table';
        emptyState.style.display   = 'none';

       this.updatePagination();

    },

    detectType(url, hostname) {
        const lower = (url || '').toLowerCase();
        const host  = (hostname || '').toLowerCase();

        const videoHosts = ['youtube.com', 'youtu.be', 'vimeo.com', 'dailymotion.com', 'tiktok.com'];
        if (videoHosts.some(h => host.includes(h))) return 'video';

        if (/\.(mp4|webm|mov|avi|mkv)(\?|$|&)/.test(lower)) return 'video';
        if (host.includes('pbs.twimg.com') && lower.includes('ext_tw_video_thumb')) return 'video';
        if (/\.(jpg|jpeg|png|gif|webp|svg|bmp|avif)(\?|$|&|%3F)/.test(lower)) return 'image';
        if (host.includes('pbs.twimg.com') || host.includes('twimg.com')) return 'image';
        if (/\.(pdf)(\?|$)/.test(lower)) return 'article';
        if (host.includes('twitter.com') || host.includes('x.com')) return 'article';

        return 'other';
    },

    typeBadgeHtml(type) {
        const map = {
            image: {
                class: 'type-image',
                icon: '<circle cx="12" cy="12" r="3"/><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 14H4L12 9l8 9z"/>',
                label: 'Image'
            },
            video: {
                class: 'type-video',
                icon: '<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>',
                label: 'Video'
            },
            article: {
                class: 'type-article',
                icon: '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>',
                label: 'Article'
            },
            other: {
                class: 'type-other',
                icon: '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>',
                label: 'Link'
            }
        };
        const t = map[type] || map.other;
        return `<span class="type-badge ${t.class}">
            <svg viewBox="0 0 24 24">${t.icon}</svg>
            ${t.label}
        </span>`;
    },

    previewHtml(url, type) {
        if (type === 'image') {
            return `<img class="url-preview" src="${this.escapeHtml(url)}" 
                        alt="preview"
                        onerror="this.parentElement.innerHTML='<div class=\'url-preview-placeholder\'><svg viewBox=\'0 0 24 24\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/><polyline points=\'21 15 16 10 5 21\'/></svg></div>'"
                        loading="lazy">`;
        }
        const icons = {
            video:   '<polygon points="5 3 19 12 5 21 5 3"/>',
            article: '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>',
            other:   '<path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>'
        };
        return `<div class="url-preview-placeholder">
            <svg viewBox="0 0 24 24" stroke="var(--text-muted)" fill="none" stroke-width="2">${icons[type] || icons.other}</svg>
        </div>`;
    },

    createRow(item, rank) {
        const url      = item.url || '';
        const hostname = this.extractHostname(url);
        const freq     = parseInt(item.freq || 0);
        const pct      = Math.round((freq / this.maxFreq) * 100);
        const type     = this.detectType(url, hostname);
        const shortUrl = url.length > 70 ? url.substring(0, 67) + '...' : url;

        return `
            <tr>
                <td class="rank-cell">${rank}</td>
                <td class="preview-cell">${this.previewHtml(url, type)}</td>
                <td class="url-cell">
                    <div class="url-info">
                        <div class="url-hostname">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="2" y1="12" x2="22" y2="12"/>
                                <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                            </svg>
                            ${this.escapeHtml(hostname)}
                        </div>
                        <div class="url-text" title="${this.escapeHtml(url)}">${this.escapeHtml(shortUrl)}</div>
                        <a href="${this.escapeHtml(url)}" target="_blank" rel="noopener noreferrer" class="url-link">
                            <svg viewBox="0 0 24 24">
                                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                                <polyline points="15 3 21 3 21 9"/>
                                <line x1="10" y1="14" x2="21" y2="3"/>
                            </svg>
                            Open URL
                        </a>
                    </div>
                </td>
                <td>${this.typeBadgeHtml(type)}</td>
                <td class="freq-cell">
                    <span class="freq-value">${this.formatNumber(freq)}</span>
                    <span class="freq-label">Shares</span>
                    <div class="freq-bar-wrapper">
                        <div class="freq-bar-track">
                            <div class="freq-bar-fill" style="width:${pct}%;"></div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    },

    toggleChart() {
        const container = document.getElementById('chartContainer');
        const btn = document.getElementById('chartToggleBtn');
        const txt = document.getElementById('chartToggleText');

        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            btn.classList.remove('collapsed');
            txt.textContent = 'Hide Chart';
        } else {
            container.classList.add('hidden');
            btn.classList.add('collapsed');
            txt.textContent = 'Show Chart';
        }
    },

   getPageRange(cur, total) {
    if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
    if (cur <= 4)   return [1, 2, 3, 4, 5, '...', total];
    if (cur >= total - 3) return [1, '...', total-4, total-3, total-2, total-1, total];
    return [1, '...', cur-1, cur, cur+1, '...', total];
},

updatePagination() {
    const totalPages = Math.ceil(this.allUrls.length / this.rowsPerPage);
    const wrapper = document.getElementById('paginationWrapper');
    const from = this.allUrls.length ? (this.currentPage - 1) * this.rowsPerPage + 1 : 0;
    const to   = Math.min(this.currentPage * this.rowsPerPage, this.allUrls.length);

    let html = `<div class="pagination-info">Showing ${this.formatNumber(from)}–${this.formatNumber(to)} of ${this.formatNumber(this.allUrls.length)} URLs</div>`;
    html += `<div style="display:flex;align-items:center;gap:6px;">`;
    html += `<button class="page-btn" onclick="SharedUrlsLoader.changePage(-1)" ${this.currentPage === 1 ? 'disabled' : ''}>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
    </button>`;

    this.getPageRange(this.currentPage, totalPages).forEach(p => {
        html += p === '...'
            ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
            : `<button class="page-btn ${p === this.currentPage ? 'active' : ''}" onclick="SharedUrlsLoader.changePage(${p - this.currentPage})">${p}</button>`;
    });

    html += `<button class="page-btn" onclick="SharedUrlsLoader.changePage(1)" ${this.currentPage === totalPages ? 'disabled' : ''}>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
    </button></div>`;

    wrapper.innerHTML = html;
    wrapper.style.display = this.allUrls.length > 0 ? 'flex' : 'none';
},

    changePage(dir) {
        const total = Math.ceil(this.allUrls.length / this.rowsPerPage);
        const next  = this.currentPage + dir;
        if (next >= 1 && next <= total) {
            this.currentPage = next;
            this.renderTable();
            document.querySelector('.table-section').scrollIntoView({ behavior: 'smooth' });
        }
    },

    exportCSV() {
        if (!this.allUrls.length) { alert('No data to export'); return; }

        const headers = ['Rank', 'URL', 'Hostname', 'Type', 'Frequency'];
        const rows = this.allUrls.map((u, i) => {
            const host = this.extractHostname(u.url || '');
            return [
                i + 1,
                `"${(u.url || '').replace(/"/g, '""')}"`,
                `"${host.replace(/"/g, '""')}"`,
                this.detectType(u.url, host),
                u.freq || 0,
            ];
        });

        const csv  = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.setAttribute('href', URL.createObjectURL(blob));
        link.setAttribute('download', `shared_urls_${this.startDate}_to_${this.endDate}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    },

    extractHostname(url) {
        try {
            const parsed = new URL(url);
            return parsed.hostname.replace(/^www\./, '');
        } catch {
            const m = url.match(/https?:\/\/([^\/\?#]+)/i);
            return m ? m[1].replace(/^www\./, '') : url;
        }
    },

    escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    },

    formatNumber(n) {
        if (!n && n !== 0) return '0';
        return new Intl.NumberFormat('en-US').format(n);
    },

    showError() {
        document.getElementById('loadingTable').style.display = 'none';
        const es = document.getElementById('emptyState');
        es.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" style="color:#ef4444;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <h3>Failed to Load Data</h3>
                <p>Unable to fetch shared URLs. Please try again later.</p>
            </div>`;
        es.style.display = 'block';
        ['statTotalUrls','statTotalShares','statUniqueHosts','statTopFreq']
            .forEach(id => document.getElementById(id).innerHTML = '<div class="stat-value">0</div>');
    }
};

document.addEventListener('DOMContentLoaded', () => SharedUrlsLoader.init());
</script>
@endsection