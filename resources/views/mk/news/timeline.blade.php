@extends('mk.layouts.app')

@section('title', 'News Mentions Timeline - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
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

  /* Main Layout */
  .dashboard-container {
    padding: 24px;
    background: var(--bg-gray-50);
    min-height: 100vh;
    max-width: 1600px;
    margin: 0 auto;
  }

  .page-header {
    margin-bottom: 32px;
  }

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

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: var(--bg-white);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
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

  .stat-card:hover .stat-icon-wrapper::after {
    opacity: 0.5;
  }

  .stat-icon {
    width: 28px;
    height: 28px;
    color: var(--primary-green);
  }

  .stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
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
    margin-top: 16px;
  }

  .stat-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    border-radius: 10px;
    transition: width 1s ease-out;
  }

  /* Charts Section */
  .charts-section {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-bottom: 24px;
  }

  @media (min-width: 1024px) {
    .charts-section {
      grid-template-columns: 1.5fr 1fr;
    }
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

  .chart-container {
    position: relative;
    height: 320px;
  }

  /* Filter Controls */
  .filter-controls {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
  }

  .timeline-search {
    position: relative;
    flex: 1;
    min-width: 240px;
    max-width: 400px;
  }

  .timeline-search input {
    width: 100%;
    padding: 10px 16px 10px 44px;
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    background: var(--bg-gray-50);
    transition: all 0.2s;
  }

  .timeline-search input:focus {
    outline: none;
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  .timeline-search svg {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
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

  .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 28px;
    border-bottom: 2px solid var(--bg-gray-50);
    gap: 16px;
    flex-wrap: wrap;
  }

  .table-title-group h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px 0;
  }

  .table-subtitle {
    font-size: 13px;
    color: var(--text-secondary);
  }

  .table-wrapper {
    overflow-x: auto;
  }

  /* Modern Table */
  .mentions-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-family: 'Poppins', sans-serif;
  }

  .mentions-table thead {
    background: linear-gradient(135deg, var(--bg-gray-50) 0%, var(--bg-white) 100%);
    border-bottom: 2px solid var(--border-gray);
  }

  .mentions-table th {
    padding: 16px 20px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    white-space: nowrap;
  }

  .mentions-table th.text-center { text-align: center; }
  .mentions-table th.text-right { text-align: right; }

  .mentions-table tbody tr {
    border-bottom: 1px solid var(--bg-gray-100);
    transition: all 0.2s;
  }

  .mentions-table tbody tr:hover {
    background: var(--bg-gray-50);
  }

  .mentions-table tbody tr:last-child {
    border-bottom: none;
  }

  .mentions-table td {
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

  /* Date Column */
  .date-cell {
    min-width: 160px;
  }

  .date-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .date-main {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
  }

  .date-time {
    font-size: 11px;
    color: var(--text-secondary);
  }

  /* Content Column */
  .content-cell {
    max-width: 500px;
  }

  .mention-content {
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-primary);
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-wrap: break-word;
  }

  .mention-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 11px;
    color: var(--text-muted);
    flex-wrap: wrap;
  }

  .mention-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .mention-meta-item svg {
    width: 13px;
    height: 13px;
    stroke: currentColor;
    fill: none;
  }

  .mention-link {
    color: var(--primary-green);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .mention-link:hover {
    color: var(--primary-green-dark);
    text-decoration: underline;
  }

  .mention-link svg {
    width: 13px;
    height: 13px;
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

  /* Source Badge */
  .source-badge {
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

  .source-badge.news {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border: 1px solid #93c5fd;
  }

  .source-badge.twitter {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1d4ed8;
    border: 1px solid #60a5fa;
  }

  .source-badge.instagram {
    background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
    color: #be185d;
    border: 1px solid #f9a8d4;
  }

  .source-badge.facebook {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border: 1px solid #93c5fd;
  }

  .source-badge.youtube {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border: 1px solid #fca5a5;
  }

  .source-badge.tiktok {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    color: #1f2937;
    border: 1px solid #d1d5db;
  }

  /* Media Type Badge */
  .media-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: var(--bg-gray-50);
    color: var(--text-secondary);
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid var(--border-gray);
  }

  .media-type-badge svg {
    width: 12px;
    height: 12px;
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
    margin-top: 16px;
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

  /* Loading */
  .loading-skeleton {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s ease-in-out infinite;
    border-radius: 8px;
  }

  @keyframes loading {
    0% {
      background-position: 200% 0;
    }
    100% {
      background-position: -200% 0;
    }
  }

  .skeleton-text {
    height: 44px;
    margin-bottom: 8px;
  }

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

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

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
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
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

  /* Responsive */
  @media (max-width: 1024px) {
    .dashboard-container {
      padding: 16px;
    }

    .stats-grid {
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
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

    .filter-controls {
      width: 100%;
    }

    .timeline-search {
      width: 100%;
      max-width: 100%;
    }

    .mentions-table th,
    .mentions-table td {
      padding: 12px 10px;
      font-size: 12px;
    }

    .author-cell {
      min-width: 150px;
    }

    .content-cell {
      max-width: 300px;
    }
  }

  @media (max-width: 768px) {
    .stat-value {
      font-size: 28px;
    }

    .chart-container {
      height: 250px;
    }

    .page-header h1 {
      font-size: 24px;
    }

    .date-picker-trigger {
      max-width: 100%;
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

    /* Mobile table adjustments */
    .mentions-table th,
    .mentions-table td {
      padding: 10px 8px;
      font-size: 11px;
    }

    .mention-content {
      -webkit-line-clamp: 1;
      font-size: 12px;
    }

    .author-info {
      gap: 8px;
    }

    .author-avatar {
      width: 36px;
      height: 36px;
      font-size: 14px;
    }

    .author-name {
      font-size: 12px;
    }

    .author-handle {
      font-size: 11px;
    }
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
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>News Mentions Timeline</h1>
    <p>Track news mentions over time with volume trends and peak hours analysis</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view timeline data.</span>
  </div>
  @else

  <!-- Date Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.news.timeline') }}">
      <input type="hidden" name="project_id" value="{{ $projectId }}">
      <input type="hidden" name="start_date" id="hiddenStartDate" value="{{ $startDate }}">
      <input type="hidden" name="end_date" id="hiddenEndDate" value="{{ $endDate }}">

      <div class="filter-content">
        <div class="filter-label">
          <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; display: inline; vertical-align: middle; margin-right: 6px; stroke: currentColor; fill: none;">
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
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

    <!-- Total Mentions -->
    <div class="stat-card" data-lazy-load="stats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Total Mentions</div>
      
      <div id="statTotalMentions" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Peak Hour -->
    <div class="stat-card" data-lazy-load="stats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Peak Hour</div>
      
      <div id="statPeakHour" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Avg Per Day -->
    <div class="stat-card" data-lazy-load="stats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Avg Per Day</div>
      
      <div id="statAvgPerDay" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 120px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

  </div>

  <!-- Charts Section -->
  <div class="charts-section">
    
    <!-- Volume Over Time Chart -->
    <div class="chart-card" data-lazy-load="volumeChart">
      <div class="chart-header">
        <div class="chart-title-group">
          <h3>Volume Over Time</h3>
          <p class="chart-subtitle">Daily news mentions count</p>
        </div>
      </div>
      
      <div class="chart-container">
        <div id="volumeChartLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="volumeOverTimeChart" style="display: none;"></canvas>
      </div>
    </div>

    <!-- Peak Hours Chart -->
    <div class="chart-card" data-lazy-load="peakHoursChart">
      <div class="chart-header">
        <div class="chart-title-group">
          <h3>Peak Hours Analysis</h3>
          <p class="chart-subtitle">Hourly distribution (24h)</p>
        </div>
      </div>
      
      <div class="chart-container">
        <div id="peakHoursChartLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="peakHoursChart" style="display: none;"></canvas>
      </div>
    </div>

  </div>

  <!-- Timeline Section -->
  <div class="table-container" data-lazy-load="timeline">
    <div class="table-header">
      <div class="table-title-group">
        <h3>News Mentions Timeline</h3>
        <p class="table-subtitle">All mentions across different media platforms</p>
      </div>

      <div class="filter-controls">
        <div class="timeline-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <input type="text" id="timelineSearchInput" placeholder="Search mentions..." onkeyup="filterMentions()">
        </div>
      </div>
    </div>

    <div class="table-wrapper">
      <!-- Loading State -->
      <div id="timelineLoading" class="loading-skeleton" style="height: 400px; margin: 20px;"></div>

      <!-- Actual Table (hidden initially) -->
      <table class="mentions-table" id="mentionsTable" style="display: none;">
        <thead>
          <tr>
            <th style="width: 60px;">#</th>
            <th>Content</th>
            <th style="width: 220px;">Author / Publisher</th>
            <th style="width: 180px;">Date & Time</th>
          </tr>
        </thead>
        <tbody id="mentionsTableBody">
          <!-- Will be populated by JavaScript -->
        </tbody>
      </table>

      <!-- Empty State -->
      <div id="emptyState" style="display: none;">
        <div class="empty-state">
          <svg viewBox="0 0 24 24" style="width: 64px; height: 64px; color: var(--text-secondary); stroke: currentColor; fill: none;">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <h3>No Mentions Found</h3>
          <p>No mentions available for the selected filters.</p>
        </div>
      </div>
    </div>

    <!-- Pagination -->
   <!-- Pagination -->
<div class="pagination" id="pagination" style="display: none; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;"></div>
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
    
    const firstDayOfWeek = firstDay.getDay();
    for (let i = 0; i < firstDayOfWeek; i++) {
      const prevMonthDay = prevLastDay.getDate() - (firstDayOfWeek - 1 - i);
      html += `<button type="button" class="calendar-day other-month" disabled>${prevMonthDay}</button>`;
    }
    
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
    
    const lastDayOfWeek = lastDay.getDay();
    const remainingCells = lastDayOfWeek === 6 ? 0 : 6 - lastDayOfWeek;
    for (let i = 1; i <= remainingCells; i++) {
      html += `<button type="button" class="calendar-day other-month" disabled>${i}</button>`;
    }
    
    html += '</div>';
    calendar.innerHTML = html;
    
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
// MAIN LOGIC
// ========================================
const projectId = '{{ $projectId ?? '' }}';
const startDate = '{{ $startDate ?? '' }}';
const endDate = '{{ $endDate ?? '' }}';

let allMentions = [];
let filteredMentions = [];
let currentPage = 1;
let mentionsPerPage = 20;
let currentSearchTerm = '';

function formatNumber(num) {
  return new Intl.NumberFormat('en-US').format(num);
}

function formatDate(dateStr) {
  if (!dateStr) return '—';
  try {
    const date = new Date(dateStr);
    return {
      date: date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      }),
      time: date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
      })
    };
  } catch (e) {
    return { date: dateStr, time: '' };
  }
}

function getMediaTypeLabel(mediaTypeId) {
  const types = {
    '1': { label: 'X', class: 'twitter', icon: '𝕏' },
    '2': { label: 'Facebook', class: 'facebook', icon: '📘' },
    '3': { label: 'Instagram', class: 'instagram', icon: '📷' },
    '4': { label: 'YouTube', class: 'youtube', icon: '▶️' },
    '5': { label: 'News', class: 'news', icon: '📰' },
    '6': { label: 'TikTok', class: 'tiktok', icon: '🎵' }
  };
  return types[mediaTypeId] || { label: 'Other', class: 'news', icon: '📌' };
}

function getInitials(name) {
  if (!name || name === 'Unknown' || name === 'Unknown Author') return '?';
  const parts = name.trim().split(/\s+/);
  if (parts.length === 1) {
    return parts[0].substring(0, 2).toUpperCase();
  }
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function cleanContent(content) {
  if (!content) return '';
  // Remove HTML tags
  return content.replace(/<[^>]*>/g, '').trim();
}

if (projectId && startDate && endDate) {
  
  // Lazy loading setup
  const lazyLoadConfig = {
    rootMargin: '50px',
    threshold: 0.01
  };

  const loadedComponents = new Set();

  const lazyLoadObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const componentId = entry.target.dataset.lazyLoad;
        
        if (!loadedComponents.has(componentId)) {
          loadedComponents.add(componentId);
          
          if (componentId === 'stats' || componentId === 'timeline') {
            loadMentions();
          }
          if (componentId === 'volumeChart') {
            loadVolumeChart();
          }
          if (componentId === 'peakHoursChart') {
            loadPeakHoursChart();
          }
          
          lazyLoadObserver.unobserve(entry.target);
        }
      }
    });
  }, lazyLoadConfig);

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-lazy-load]').forEach(element => {
      lazyLoadObserver.observe(element);
    });
  });

  function addLoadingBadge(card) {
    if (!card || card.querySelector('.lazy-loading-badge')) return;
    
    const badge = document.createElement('div');
    badge.className = 'lazy-loading-badge';
    badge.innerHTML = '<div class="spinner"></div><span>Loading...</span>';
    card.style.position = 'relative';
    card.appendChild(badge);
  }

  function removeLoadingBadge(card) {
    if (!card) return;
    const badge = card.querySelector('.lazy-loading-badge');
    if (badge) {
      badge.style.opacity = '0';
      setTimeout(() => badge.remove(), 300);
    }
  }

  function animateProgress(card, percentage) {
    const progressBar = card.querySelector('.stat-progress-bar');
    if (progressBar) {
      setTimeout(() => {
        progressBar.style.width = percentage + '%';
      }, 100);
    }
  }

  // ─── Load Mentions Data ───────────────────────────────
  let mentionsLoaded = false;

  async function loadMentions() {
    if (mentionsLoaded) return;
    mentionsLoaded = true;

    const statCards = document.querySelectorAll('[data-lazy-load="stats"]');
    statCards.forEach(c => addLoadingBadge(c));

    try {
      const response = await fetch(`/mk/api/news/mentions?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();

      if (result.success && result.data && result.data.length > 0) {
        allMentions = result.data;
        filteredMentions = [...allMentions]; // Initialize filtered mentions

        // Calculate stats
        const totalMentions = allMentions.length;
        
        // Calculate peak hour
        const hourCounts = {};
        allMentions.forEach(m => {
          const date = new Date(m.date_created);
          const hour = date.getHours();
          hourCounts[hour] = (hourCounts[hour] || 0) + 1;
        });
        
        let peakHour = 0;
        let maxCount = 0;
        Object.entries(hourCounts).forEach(([hour, count]) => {
          if (count > maxCount) {
            maxCount = count;
            peakHour = parseInt(hour);
          }
        });

        // Calculate average per day
        const dateRange = new Date(endDate) - new Date(startDate);
        const days = Math.ceil(dateRange / (1000 * 60 * 60 * 24)) + 1;
        const avgPerDay = Math.round(totalMentions / days);

        // Update stats
        document.getElementById('statTotalMentions').innerHTML = `<div class="stat-value">${formatNumber(totalMentions)}</div>`;
        document.getElementById('statPeakHour').innerHTML = `<div class="stat-value">${peakHour}:00</div>`;
        document.getElementById('statAvgPerDay').innerHTML = `<div class="stat-value">${formatNumber(avgPerDay)}</div>`;

        document.getElementById('statTotalMentions').classList.add('data-loaded');
        document.getElementById('statPeakHour').classList.add('data-loaded');
        document.getElementById('statAvgPerDay').classList.add('data-loaded');

        statCards.forEach((c, i) => {
          const pcts = [90, 75, 65];
          animateProgress(c, pcts[i] ?? 70);
        });

        // Render timeline
        renderTimeline();
        
        document.getElementById('timelineLoading').style.display = 'none';
        document.getElementById('mentionsTable').style.display = 'table';
        document.getElementById('pagination').style.display = 'flex';

      } else {
        ['statTotalMentions', 'statPeakHour', 'statAvgPerDay'].forEach(id => {
          document.getElementById(id).innerHTML = '<div class="stat-value">0</div>';
        });
      }

    } catch (error) {
      console.error('Error loading mentions:', error);
    } finally {
      statCards.forEach(c => {
        removeLoadingBadge(c);
        c.classList.add('loaded');
      });
      document.querySelector('[data-lazy-load="timeline"]')?.classList.add('loaded');
    }
  }

  // ─── Load Volume Chart ────────────────────────────────
  async function loadVolumeChart() {
    const card = document.querySelector('[data-lazy-load="volumeChart"]');
    addLoadingBadge(card);

    try {
      const response = await fetch(`/mk/api/news/mentions?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();

      if (result.success && result.data) {
        // Group by date
        const dateCounts = {};
        result.data.forEach(m => {
          const date = new Date(m.date_created).toISOString().split('T')[0];
          dateCounts[date] = (dateCounts[date] || 0) + 1;
        });

        // Create complete date range (fill in missing dates with 0)
        const start = new Date(startDate);
        const end = new Date(endDate);
        const chartData = [];
        
        for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
          const dateStr = d.toISOString().split('T')[0];
          chartData.push({
            date: dateStr,
            count: dateCounts[dateStr] || 0
          });
        }

        renderVolumeChart(chartData);
      }
    } catch (error) {
      console.error('Error loading volume chart:', error);
    } finally {
      removeLoadingBadge(card);
      card.classList.add('loaded');
    }
  }

  function renderVolumeChart(data) {
    const canvas = document.getElementById('volumeOverTimeChart');
    const loading = document.getElementById('volumeChartLoading');
    
    if (!canvas || !data || data.length === 0) {
      console.error('Cannot render chart: missing canvas or data');
      if (loading) loading.style.display = 'none';
      return;
    }
    
    // Destroy existing chart if it exists
    const existingChart = Chart.getChart(canvas);
    if (existingChart) {
      existingChart.destroy();
    }
    
    const ctx = canvas.getContext('2d');
    
    // Format dates for display (e.g., "Feb 16" instead of "2026-02-16")
    const labels = data.map(d => {
      const date = new Date(d.date);
      return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Mentions',
          data: data.map(d => d.count),
          borderColor: '#038047',
          backgroundColor: 'rgba(3, 128, 71, 0.1)',
          borderWidth: 3,
          tension: 0.4,
          fill: true,
          pointRadius: 4,
          pointHoverRadius: 7,
          pointBackgroundColor: '#038047',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1a202c',
            padding: 16,
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            titleFont: { size: 14, weight: '600' },
            bodyFont: { size: 13 },
            borderColor: '#e2e8f0',
            borderWidth: 1,
            displayColors: false,
            cornerRadius: 8,
            callbacks: {
              title: function(context) {
                // Show full date in tooltip
                return data[context[0].dataIndex].date;
              },
              label: function(context) {
                return context.parsed.y + ' mentions';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9', drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 12 },
              padding: 8,
              precision: 0
            }
          },
          x: {
            grid: { display: false, drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 11 },
              padding: 8,
              maxRotation: 45,
              minRotation: 0,
              autoSkip: true,
              maxTicksLimit: 10
            }
          }
        }
      }
    });

    loading.style.display = 'none';
    canvas.style.display = 'block';
  }

  // ─── Load Peak Hours Chart ────────────────────────────
  async function loadPeakHoursChart() {
    const card = document.querySelector('[data-lazy-load="peakHoursChart"]');
    addLoadingBadge(card);

    try {
      const response = await fetch(`/mk/api/news/mentions?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();

      if (result.success && result.data) {
        // Group by hour
        const hourCounts = new Array(24).fill(0);
        result.data.forEach(m => {
          const date = new Date(m.date_created);
          const hour = date.getHours();
          hourCounts[hour]++;
        });

        renderPeakHoursChart(hourCounts);
      }
    } catch (error) {
      console.error('Error loading peak hours chart:', error);
    } finally {
      removeLoadingBadge(card);
      card.classList.add('loaded');
    }
  }

  function renderPeakHoursChart(hourCounts) {
    const canvas = document.getElementById('peakHoursChart');
    const loading = document.getElementById('peakHoursChartLoading');
    
    if (!canvas) {
      console.error('Cannot render peak hours chart: canvas not found');
      if (loading) loading.style.display = 'none';
      return;
    }
    
    // Destroy existing chart if it exists
    const existingChart = Chart.getChart(canvas);
    if (existingChart) {
      existingChart.destroy();
    }
    
    const ctx = canvas.getContext('2d');
    
    const labels = Array.from({ length: 24 }, (_, i) => `${String(i).padStart(2, '0')}:00`);
    
    // Find max value for dynamic coloring
    const maxCount = Math.max(...hourCounts);
    
    // Create gradient colors based on value
    const backgroundColors = hourCounts.map(count => {
      const intensity = count / maxCount;
      if (intensity > 0.7) return 'rgba(3, 128, 71, 0.9)'; // Dark green for high
      if (intensity > 0.4) return 'rgba(3, 128, 71, 0.7)'; // Medium green
      return 'rgba(3, 128, 71, 0.5)'; // Light green for low
    });
    
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Mentions',
          data: hourCounts,
          backgroundColor: backgroundColors,
          borderColor: '#038047',
          borderWidth: 1,
          borderRadius: 6,
          hoverBackgroundColor: '#026738'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1a202c',
            padding: 16,
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            titleFont: { size: 14, weight: '600' },
            bodyFont: { size: 13 },
            displayColors: false,
            cornerRadius: 8,
            callbacks: {
              title: function(context) {
                const hour = context[0].dataIndex;
                return `${String(hour).padStart(2, '0')}:00 - ${String(hour).padStart(2, '0')}:59`;
              },
              label: function(context) {
                return context.parsed.y + ' mentions';
              },
              afterLabel: function(context) {
                const total = hourCounts.reduce((a, b) => a + b, 0);
                const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                return percentage + '% of total';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9', drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 12 },
              padding: 8,
              precision: 0
            }
          },
          x: {
            grid: { display: false, drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 10 },
              padding: 8,
              maxRotation: 45,
              minRotation: 45,
              autoSkip: false
            }
          }
        }
      }
    });

    loading.style.display = 'none';
    canvas.style.display = 'block';
  }
function getPageRange(cur, total) {
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
  if (cur <= 4)         return [1, 2, 3, 4, 5, '...', total];
  if (cur >= total - 3) return [1, '...', total-4, total-3, total-2, total-1, total];
  return [1, '...', cur-1, cur, cur+1, '...', total];
}

function goPage(p) {
  const totalPages = Math.ceil(filteredMentions.length / mentionsPerPage);
  if (p < 1 || p > totalPages) return;
  currentPage = p;
  renderTimeline();
  document.querySelector('.table-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
}
  // ─── Render Timeline ──────────────────────────────────
  function renderTimeline() {
    const startIdx = (currentPage - 1) * mentionsPerPage;
    const endIdx = startIdx + mentionsPerPage;
    const currentData = filteredMentions.slice(startIdx, endIdx);

    const tbody = document.getElementById('mentionsTableBody');
    
    if (!currentData.length) {
      document.getElementById('mentionsTable').style.display = 'none';
      document.getElementById('emptyState').style.display = 'block';
      document.getElementById('pagination').style.display = 'none';
      return;
    }

    tbody.innerHTML = currentData.map((item, idx) => {
      const rank = startIdx + idx + 1;
      const content = cleanContent(item.content || 'No content');
      const dateInfo = formatDate(item.date_created);
      
      // ===================================================
      // FIX: Proper author name extraction for news articles
      // ===================================================
      let authorName = 'Unknown Author';
      let authorHandle = 'unknown';
      
      // Priority 1: Use author_name if available and not empty
      if (item.author_name && item.author_name.trim() !== '') {
        authorName = item.author_name.trim();
      } 
      // Priority 2: Use author_scr_name if available
      else if (item.author_scr_name && item.author_scr_name.trim() !== '') {
        authorName = item.author_scr_name.trim();
      } 
      // Priority 3: For news articles, use hostname as publisher name
      else if (item.hostname && item.hostname.trim() !== '') {
        // Convert hostname to readable name (e.g., "kompas.com" -> "Kompas")
        authorName = item.hostname.replace('www.', '').split('.')[0];
        authorName = authorName.charAt(0).toUpperCase() + authorName.slice(1);
      }
      
      // Author handle (for display under name)
      if (item.author_scr_name && item.author_scr_name.trim() !== '') {
        authorHandle = item.author_scr_name.replace('@', '');
      } else if (item.hostname) {
        authorHandle = item.hostname.replace('www.', '');
      }
      
      const source = item.hostname || item.media_name || 'Unknown Source';
      const mediaType = getMediaTypeLabel(item.media_type_id || '5');
      const url = item.url || '#';
      const initials = getInitials(authorName);
      
      // ===================================================
      // FIX: Avatar handling for news articles
      // ===================================================
      let avatarHtml = initials;
      
      // Check if valid avatar URL exists (must be full URL, not relative path)
      const hasValidAvatar = item.avatar_url && 
                             item.avatar_url.trim() !== '' &&
                             !item.avatar_url.startsWith('/external') && 
                             !item.avatar_url.includes('default-avatar') &&
                             item.avatar_url.startsWith('http'); // Must be full URL
      
      if (hasValidAvatar) {
        // Use provided avatar
        avatarHtml = `<img src="${item.avatar_url}" alt="${escapeHtml(authorName)}" onerror="this.parentElement.innerHTML='${initials}'">`;
      } else if (item.media_type_id === '1' && authorHandle !== 'unknown') {
        // Twitter/X - try unavatar
        avatarHtml = `<img src="https://unavatar.io/twitter/${authorHandle}" alt="${escapeHtml(authorName)}" onerror="this.parentElement.innerHTML='${initials}'">`;
      } else if (item.media_type_id === '5' && item.hostname) {
        // News articles - use Clearbit logo API for publisher logo
        const domain = item.hostname.replace('www.', '');
        avatarHtml = `<img src="https://logo.clearbit.com/${domain}" alt="${escapeHtml(authorName)}" onerror="this.parentElement.innerHTML='${initials}'">`;
      }

      return `
        <tr>
          <td class="rank-cell">${rank}</td>
          
          <td class="content-cell">
            <div class="mention-content">${escapeHtml(content)}</div>
            <div class="mention-meta">
              <span class="mention-meta-item">
                <svg viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                  <circle cx="12" cy="10" r="3"/>
                </svg>
                ${escapeHtml(source)}
              </span>
              ${url !== '#' ? `
              <a href="${url}" target="_blank" rel="noopener noreferrer" class="mention-link">
                <svg viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
                  <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                  <polyline points="15 3 21 3 21 9"/>
                  <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                View Article
              </a>
              ` : ''}
            </div>
          </td>
          
          <td class="author-cell">
            <div class="author-info">
              <div class="author-avatar">${avatarHtml}</div>
              <div class="author-details">
                <div class="author-name" title="${escapeHtml(authorName)}">${escapeHtml(authorName)}</div>
                <div class="author-handle">${escapeHtml(authorHandle)}</div>
              </div>
            </div>
          </td>
          
          <td class="date-cell">
            <div class="date-info">
              <div class="date-main">${dateInfo.date}</div>
              <div class="date-time">${dateInfo.time}</div>
            </div>
          </td>
        </tr>
      `;
    }).join('');

    document.getElementById('timelineLoading').style.display = 'none';
    document.getElementById('mentionsTable').style.display = 'table';
    document.getElementById('emptyState').style.display = 'none';
    
    updatePagination();
    document.getElementById('pagination').style.display = 'flex';
  }

  function updatePagination() {
  const totalPages = Math.ceil(filteredMentions.length / mentionsPerPage);
  const wrapper    = document.getElementById('pagination');
  const from       = filteredMentions.length ? (currentPage - 1) * mentionsPerPage + 1 : 0;
  const to         = Math.min(currentPage * mentionsPerPage, filteredMentions.length);

  if (filteredMentions.length === 0) {
    wrapper.style.display = 'none';
    return;
  }

  let html = `<div class="pagination-info">Showing ${formatNumber(from)}–${formatNumber(to)} of ${formatNumber(filteredMentions.length)} mentions</div>`;
  html += `<div style="display:flex;align-items:center;gap:6px;">`;

  html += `<button class="page-btn" onclick="goPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg>
  </button>`;

  getPageRange(currentPage, totalPages).forEach(p => {
    html += p === '...'
      ? `<button class="page-btn" disabled style="cursor:default;">…</button>`
      : `<button class="page-btn ${p === currentPage ? 'active' : ''}" onclick="goPage(${p})">${p}</button>`;
  });

  html += `<button class="page-btn" onclick="goPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="9 18 15 12 9 6"/></svg>
  </button>`;

  html += `</div>`;
  wrapper.innerHTML     = html;
  wrapper.style.display = 'flex';
}

  function filterMentions() {
    const searchTerm = document.getElementById('timelineSearchInput').value.toLowerCase();
    
    currentSearchTerm = searchTerm;
    
    filteredMentions = allMentions.filter(item => {
      // Filter by search term only
      const content = (item.content || '').toLowerCase();
      const author = (item.author_scr_name || item.author_name || '').toLowerCase();
      const source = (item.hostname || '').toLowerCase();
      const matchesSearch = !searchTerm || content.includes(searchTerm) || author.includes(searchTerm) || source.includes(searchTerm);
      
      return matchesSearch;
    });
    
    currentPage = 1;
    renderTimeline();
  }

window.changePage    = changePage;
window.goPage        = goPage;
window.filterMentions = filterMentions;
}
</script>
@endsection