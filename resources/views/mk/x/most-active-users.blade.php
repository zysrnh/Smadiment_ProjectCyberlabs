@extends('mk.layouts.app')

@section('title', 'Most Active Users - X Analytics')

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
  }

  /* Table Section */
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

  .table-subtitle {
    font-size: 13px;
    color: var(--text-secondary);
  }

  .table-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .table-search {
    position: relative;
    width: 280px;
  }

  .table-search input {
    width: 100%;
    padding: 10px 16px 10px 44px;
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    background: var(--bg-gray-50);
    transition: all 0.2s;
    color: var(--text-primary);
  }

  .table-search input:focus {
    outline: none;
    border-color: var(--primary-green);
    background: var(--bg-white);
    box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
  }

  .table-search svg {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
  }

  /* Actions Dropdown */
  .actions-dropdown { position: relative; }

  .actions-dropdown-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.2s;
  }

  .actions-dropdown-btn:hover {
    background: var(--bg-gray-50);
    border-color: var(--primary-green);
  }

  .actions-dropdown-btn svg {
    width: 16px;
    height: 16px;
    color: var(--text-secondary);
  }

  .actions-dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    min-width: 220px;
    padding: 8px;
    z-index: 1000;
    display: none;
  }

  .actions-dropdown-menu.show {
    display: block;
    animation: dropdownSlideIn 0.2s ease-out;
  }

  @keyframes dropdownSlideIn {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .actions-dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
  }

  .actions-dropdown-item:hover {
    background: var(--bg-gray-50);
    color: var(--primary-green);
  }

  .actions-dropdown-item svg {
    width: 18px;
    height: 18px;
    color: var(--text-secondary);
  }

  .actions-dropdown-item:hover svg { color: var(--primary-green); }

  .actions-dropdown-divider {
    height: 1px;
    background: var(--border-gray);
    margin: 6px 0;
  }

  /* Data Table */
  .data-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 12px;
  }

  .data-table thead tr {
    background: var(--bg-white);
    border-bottom: 1px solid var(--border-gray);
  }

  .data-table th {
    padding: 10px 12px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 1px solid var(--border-gray);
    white-space: nowrap;
  }

  .data-table th:first-child { padding-left: 20px; }
  .data-table th:last-child  { padding-right: 20px; }

  .data-table td {
    padding: 12px;
    font-size: 12px;
    color: var(--text-primary);
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }

  .data-table td:first-child { padding-left: 20px; }
  .data-table td:last-child  { padding-right: 20px; }

  .data-table tbody tr {
    transition: all 0.2s;
    background: var(--bg-white);
    cursor: pointer;
  }

  .data-table tbody tr:hover { background: #fafbfc; }
  .data-table tbody tr:last-child td { border-bottom: none; }

  /* Avatar */
  .user-avatar-img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
  }

  .user-avatar-fallback {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 13px;
  }

  /* Username/account links */
  .username-link {
    color: #ea580c;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
    cursor: pointer;
  }

  .username-link:hover {
    color: var(--primary-green);
    text-decoration: underline;
  }

  .account-name-link {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
  }

  .account-name-link:hover {
    color: var(--primary-green);
    text-decoration: underline;
  }

  /* Activity Stats */
  .activity-stat {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
  }

  .activity-stat svg {
    width: 14px;
    height: 14px;
    color: var(--primary-green);
  }

  /* View profile btn */
  .view-profile-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    background: #000;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    white-space: nowrap;
  }

  .view-profile-btn:hover {
    background: #1d1d1d;
    transform: translateY(-1px);
  }

  .view-profile-btn svg {
    width: 11px;
    height: 11px;
    fill: white;
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
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
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

  /* User Detail Modal */
  .user-detail-modal {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }

  .user-detail-modal.show { 
    display: flex;
  }

  .modal-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.8);
  }

  .modal-content {
    position: relative;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
    width: 90%;
    max-width: 560px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    animation: modalSlideIn 0.3s ease-out;
    z-index: 10001;
  }

  @keyframes modalSlideIn {
    from { transform: translateY(-20px) scale(0.95); opacity: 0; }
    to   { transform: translateY(0) scale(1); opacity: 1; }
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
    border-radius: 16px 16px 0 0;
  }

  .modal-header h3 {
    font-size: 20px;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .modal-header h3 .x-icon-sm {
    width: 28px;
    height: 28px;
    background: #000;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .modal-header h3 .x-icon-sm svg {
    width: 15px;
    height: 15px;
    fill: #fff;
  }

  .modal-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: var(--bg-gray-50);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    color: var(--text-secondary);
  }

  .modal-close:hover { background: #ef4444; color: white; }

  .modal-body {
    padding: 28px;
    overflow-y: auto;
    background: #ffffff;
    border-radius: 0 0 16px 16px;
  }

  /* Modal inner content */
  .modal-user-row {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 18px;
  }

  .modal-user-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    border: 3px solid #e2e8f0;
  }

  .modal-user-avatar-fallback {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: 700;
    flex-shrink: 0;
    border: 3px solid #e2e8f0;
  }

  .modal-user-name {
    font-size: 17px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.2;
  }

  .modal-user-scr {
    font-size: 14px;
    color: var(--text-secondary);
  }

  .modal-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 20px;
  }

  .modal-stat-card {
    background: var(--bg-gray-50);
    border: 1px solid var(--border-gray);
    border-radius: 12px;
    padding: 16px;
    text-align: center;
  }

  .modal-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
  }

  .modal-stat-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
  }

  .modal-activity-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 16px;
  }

  .modal-activity-card {
    border-radius: 12px;
    padding: 14px 12px;
    text-align: center;
    border: 1px solid transparent;
  }

  .modal-activity-card.mentions  { background: #f0fdf4; border-color: #bbf7d0; }
  .modal-activity-card.replies   { background: #eff6ff; border-color: #bfdbfe; }
  .modal-activity-card.retweets  { background: #fef3c7; border-color: #fde68a; }

  .modal-activity-value {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.4px;
  }

  .modal-activity-card.mentions .modal-activity-value  { color: #15803d; }
  .modal-activity-card.replies .modal-activity-value   { color: #1d4ed8; }
  .modal-activity-card.retweets .modal-activity-value  { color: #d97706; }

  .modal-activity-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
    opacity: 0.7;
  }

  .modal-total-badge {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    border-radius: 12px;
    padding: 16px 20px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-total-label {
    font-size: 13px;
    font-weight: 600;
    opacity: 0.9;
  }

  .modal-total-value {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.5px;
  }

  .modal-footer {
    display: flex;
    justify-content: center;
    padding-top: 16px;
    border-top: 1px solid var(--border-gray);
    margin-top: 16px;
  }

  .modal-open-twitter {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #000;
    color: #fff;
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
  }

  .modal-open-twitter:hover { background: #1d1d1d; transform: translateY(-1px); }

  .modal-open-twitter svg { width: 13px; height: 13px; fill: white; }

  /* Responsive */
  @media (max-width: 1400px) {
    .data-table { font-size: 12px; }
    .data-table th, .data-table td { padding: 10px 12px; }
  }

  @media (max-width: 1024px) {
    .dashboard-container { padding: 16px; }
    .stats-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
    .filter-content { flex-direction: column; align-items: stretch; }
    .date-range-wrapper { flex-direction: column; }
    .apply-btn { width: 100%; justify-content: center; }
    .data-table { min-width: 900px; }
  }

  @media (max-width: 768px) {
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

  @media (max-width: 640px) {
    .stat-value { font-size: 28px; }
    .table-search { width: 100%; }
    .page-header h1 { font-size: 24px; }
    .modal-content { width: 95%; max-height: 90vh; }
    .modal-header, .modal-body { padding: 20px; }
    .modal-stats-grid { grid-template-columns: 1fr; }
  }
</style>
@endsection

@section('content')
<div class="dashboard-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>Most Active Users</h1>
    <p>Top users with the highest activity based on total interactions (mentions, replies, retweets)</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view Most Active Users data.</span>
  </div>
  @else

  <!-- Date Filter Card -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.most-active-users') }}">
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
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
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

    <!-- Total Users -->
    <div class="stat-card" data-lazy-load="userStats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Total Users</div>
      <div id="statTotalUsers" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Total Interactions -->
    <div class="stat-card" data-lazy-load="userStats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Total Interactions</div>
      <div id="statTotalInteractions" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Most Active -->
    <div class="stat-card" data-lazy-load="userStats">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
      </div>
      <div class="stat-label">Most Active</div>
      <div id="statMostActive" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

  </div>

  <!-- Users Table -->
  <div class="table-section" data-lazy-load="usersTable">
    <div class="table-header">
      <div class="table-title">
        <h3>Active Users Ranking</h3>
        <p class="table-subtitle">Sorted by total activity — click user to view detail</p>
      </div>

      <div class="table-actions">
        <div class="table-search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <input type="text" id="searchInput" placeholder="Search users..." onkeyup="filterTable()">
        </div>

        <!-- Actions Dropdown -->
        <div class="actions-dropdown">
          <button class="actions-dropdown-btn" onclick="toggleActionsDropdown(event)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="1"/>
              <circle cx="12" cy="5"  r="1"/>
              <circle cx="12" cy="19" r="1"/>
            </svg>
            Actions
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>

          <div class="actions-dropdown-menu" id="actionsDropdownMenu">
            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault(); exportCSV()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              Export to CSV
            </a>

            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault(); refreshData()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"/>
                <polyline points="1 20 1 14 7 14"/>
                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
              </svg>
              Refresh Data
            </a>

            <div class="actions-dropdown-divider"></div>

            <a href="#" class="actions-dropdown-item" onclick="event.preventDefault(); printTable()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
              </svg>
              Print Table
            </a>
          </div>
        </div>
      </div>
    </div>

    <div id="tableLoading" class="loading-skeleton" style="height: 400px;"></div>
    <div id="tableWrapper" style="display: none; overflow-x: auto;"></div>

    <!-- Pagination -->
    <div class="pagination" id="pagination" style="display: none;">
      <button class="pagination-btn" id="prevBtn" onclick="changePage(-1)">
        ← Previous
      </button>
      <span class="pagination-info" id="pageInfo">Page 1 of 1</span>
      <button class="pagination-btn" id="nextBtn" onclick="changePage(1)">
        Next →
      </button>
    </div>

    <!-- Empty state -->
    <div id="emptyState" style="display: none; text-align: center; padding: 60px 20px; color: var(--text-secondary);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; margin: 0 auto 16px; opacity: 0.4; display: block;">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
      </svg>
      <p style="font-size: 15px; font-weight: 500;">No user data found for the selected date range.</p>
    </div>
  </div>

  @endif
</div>

<!-- User Detail Modal -->
<div class="user-detail-modal" id="userDetailModal">
  <div class="modal-overlay" onclick="closeUserModal()"></div>
  <div class="modal-content">
    <div class="modal-header">
      <h3>
        <span class="x-icon-sm">
          <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </span>
        User Profile
      </h3>
      <button class="modal-close" onclick="closeUserModal()">
        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body" id="userModalBody"></div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const projectId = '{{ $projectId ?? '' }}';
  const startDate = '{{ $startDate ?? '' }}';
  const endDate   = '{{ $endDate ?? '' }}';

  let allData = [];
  let currentPage = 1;
  let usersPerPage = 20;

  function formatNumber(n) {
    if (!n && n !== 0) return '0';
    return new Intl.NumberFormat('en-US').format(n);
  }

  function getInitials(name) {
    if (!name || name === 'Unknown') return '?';
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) {
      return parts[0].substring(0, 2).toUpperCase();
    }
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  }

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

  // ─── Lazy Load ────────────────────────────────────────────────────────────
  const lazyLoadConfig  = { rootMargin: '50px', threshold: 0.01 };
  const loadedComponents = new Set();

  const lazyLoadObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.dataset.lazyLoad;
        if (!loadedComponents.has(id)) {
          loadedComponents.add(id);
          if (id === 'userStats' || id === 'usersTable') {
            loadData();
          }
          lazyLoadObserver.unobserve(entry.target);
        }
      }
    });
  }, lazyLoadConfig);

  document.addEventListener('DOMContentLoaded', () => {
    if (projectId && startDate && endDate) {
      document.querySelectorAll('[data-lazy-load]').forEach(el => lazyLoadObserver.observe(el));
    }
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
    if (badge) { badge.style.opacity = '0'; setTimeout(() => badge.remove(), 300); }
  }

  function animateProgress(card, pct) {
    const bar = card.querySelector('.stat-progress-bar');
    if (bar) setTimeout(() => bar.style.width = Math.min(pct, 100) + '%', 100);
  }

  // ─── Main data load ───────────────────────────────────────────────────────
  let dataLoaded = false;

  async function loadData() {
    if (dataLoaded) return;
    dataLoaded = true;

    const statCards = document.querySelectorAll('[data-lazy-load="userStats"]');
    statCards.forEach(c => addLoadingBadge(c));

    try {
      const res = await fetch(`/mk/api/x/most-active-users?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await res.json();

      if (result.success && result.data?.data && result.data.data.length > 0) {
        allData = result.data.data;

        // Stats
        const totalUsers = allData.length;
        const totalInteractions = allData.reduce((sum, u) => sum + (u.posts || 0), 0);
        const mostActive = Math.max(...allData.map(u => u.posts || 0));
        const topUser = allData.find(u => u.posts === mostActive);

        document.getElementById('statTotalUsers').innerHTML = `<div class="stat-value">${formatNumber(totalUsers)}</div>`;
        document.getElementById('statTotalInteractions').innerHTML = `<div class="stat-value">${formatNumber(totalInteractions)}</div>`;
        document.getElementById('statMostActive').innerHTML = `<div class="stat-value">${formatNumber(mostActive)}</div>`;

        document.getElementById('statTotalUsers').classList.add('data-loaded');
        document.getElementById('statTotalInteractions').classList.add('data-loaded');
        document.getElementById('statMostActive').classList.add('data-loaded');

        statCards.forEach((c, i) => {
          const pcts = [80, 100, 65];
          animateProgress(c, pcts[i] ?? 70);
        });

        // Render table
        renderTable();
        updatePagination();
        
        document.getElementById('tableLoading').style.display  = 'none';
        document.getElementById('tableWrapper').style.display  = 'block';
        document.getElementById('pagination').style.display = 'flex';

      } else {
        document.getElementById('tableLoading').style.display = 'none';
        document.getElementById('emptyState').style.display   = 'block';

        ['statTotalUsers','statTotalInteractions','statMostActive'].forEach(id => {
          document.getElementById(id).innerHTML = '<div class="stat-value">0</div>';
        });
      }

    } catch(err) {
      console.error('Error loading most active users:', err);
      document.getElementById('tableLoading').style.display = 'none';
      document.getElementById('emptyState').style.display   = 'block';
    } finally {
      statCards.forEach(c => { removeLoadingBadge(c); c.classList.add('loaded'); });
      document.querySelector('[data-lazy-load="usersTable"]')?.classList.add('loaded');
    }
  }

  function renderTable() {
    const startIdx = (currentPage - 1) * usersPerPage;
    const endIdx = startIdx + usersPerPage;
    const currentData = allData.slice(startIdx, endIdx);

    let html = `<table class="data-table" id="usersTable">
      <thead><tr>
        <th>RANK</th>
        <th>AVATAR</th>
        <th>USERNAME</th>
        <th>NAME</th>
        <th>FOLLOWERS</th>
        <th>MENTIONS</th>
        <th>REPLIES</th>
        <th>RETWEETS</th>
        <th style="text-align:center;">TOTAL</th>
        <th></th>
      </tr></thead>
      <tbody>`;

    currentData.forEach((user, i) => {
      const actualRank = startIdx + i + 1;
      const name = user.name || user.username || 'Unknown';
      const username = user.username || '';
      const avatarUrl = user.profile_image_url || '';
      const followers = user.followers || 0;
      const mentions = user.mentions || 0;
      const replies = user.replies || 0;
      const retweets = user.retweets || 0;
      const total = user.posts || 0;

      const initials = getInitials(name);

      // Avatar with unavatar fallback
      let avatarHtml = '';
      const hasValidAvatar = avatarUrl && !avatarUrl.startsWith('/external');
      
      if (hasValidAvatar) {
        avatarHtml = `<img src="${avatarUrl}" alt="${name}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                      <div class="user-avatar-fallback" style="display:none;">${initials}</div>`;
      } else {
        avatarHtml = `<img src="https://unavatar.io/twitter/${username}" alt="${name}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                      <div class="user-avatar-fallback" style="display:none;">${initials}</div>`;
      }

      const esc = s => (s || '').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;');
      const userJson = JSON.stringify(user).replace(/'/g, "\\'");

      html += `<tr onclick="openUserModal('${esc(userJson)}')">
        <td><strong>${actualRank}</strong></td>
        <td>${avatarHtml}</td>
        <td>
          <a href="https://twitter.com/${username}" target="_blank" class="username-link" onclick="event.stopPropagation();">@${username}</a>
        </td>
        <td>
          <a href="https://twitter.com/${username}" target="_blank" class="account-name-link" onclick="event.stopPropagation();">${name}</a>
        </td>
        <td>
          <div class="activity-stat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
            </svg>
            ${formatNumber(followers)}
          </div>
        </td>
        <td style="color:var(--text-secondary);font-weight:600">${formatNumber(mentions)}</td>
        <td style="color:var(--text-secondary);font-weight:600">${formatNumber(replies)}</td>
        <td style="color:var(--text-secondary);font-weight:600">${formatNumber(retweets)}</td>
        <td style="text-align:center;">
          <div class="activity-stat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="20" x2="18" y2="10"/>
              <line x1="12" y1="20" x2="12" y2="4"/>
              <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            ${formatNumber(total)}
          </div>
        </td>
        <td>
          <a href="https://twitter.com/${username}" target="_blank" class="view-profile-btn" onclick="event.stopPropagation();">
            <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            View
          </a>
        </td>
      </tr>`;
    });

    html += '</tbody></table>';
    document.getElementById('tableWrapper').innerHTML = html;
  }

  function updatePagination() {
    const totalPages = Math.ceil(allData.length / usersPerPage);
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    prevBtn.disabled = currentPage === 1;
    nextBtn.disabled = currentPage === totalPages;
  }

  function changePage(direction) {
    const totalPages = Math.ceil(allData.length / usersPerPage);
    const newPage = currentPage + direction;

    if (newPage >= 1 && newPage <= totalPages) {
      currentPage = newPage;
      renderTable();
      updatePagination();
      document.querySelector('.table-section').scrollIntoView({ behavior: 'smooth' });
    }
  }

  function filterTable() {
    const term = document.getElementById('searchInput').value.toLowerCase();
    
    if (!term) {
      currentPage = 1;
      renderTable();
      updatePagination();
      document.getElementById('pagination').style.display = 'flex';
      return;
    }
    
    // Filter data
    const filtered = allData.filter(user => {
      const name = user.name || '';
      const username = user.username || '';
      const searchText = (name + ' ' + username).toLowerCase();
      return searchText.includes(term);
    });
    
    // Render filtered results without pagination
    let html = `<table class="data-table" id="usersTable">
      <thead><tr>
        <th>RANK</th>
        <th>AVATAR</th>
        <th>USERNAME</th>
        <th>NAME</th>
        <th>FOLLOWERS</th>
        <th>MENTIONS</th>
        <th>REPLIES</th>
        <th>RETWEETS</th>
        <th style="text-align:center;">TOTAL</th>
        <th></th>
      </tr></thead>
      <tbody>`;

    filtered.forEach((user, i) => {
      const name = user.name || user.username || 'Unknown';
      const username = user.username || '';
      const avatarUrl = user.profile_image_url || '';
      const followers = user.followers || 0;
      const mentions = user.mentions || 0;
      const replies = user.replies || 0;
      const retweets = user.retweets || 0;
      const total = user.posts || 0;

      const initials = getInitials(name);

      let avatarHtml = '';
      const hasValidAvatar = avatarUrl && !avatarUrl.startsWith('/external');
      
      if (hasValidAvatar) {
        avatarHtml = `<img src="${avatarUrl}" alt="${name}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                      <div class="user-avatar-fallback" style="display:none;">${initials}</div>`;
      } else {
        avatarHtml = `<img src="https://unavatar.io/twitter/${username}" alt="${name}" class="user-avatar-img" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                      <div class="user-avatar-fallback" style="display:none;">${initials}</div>`;
      }

      const esc = s => (s || '').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'&quot;');
      const userJson = JSON.stringify(user).replace(/'/g, "\\'");

      html += `<tr onclick="openUserModal('${esc(userJson)}')">
        <td><strong>${i + 1}</strong></td>
        <td>${avatarHtml}</td>
        <td>
          <a href="https://twitter.com/${username}" target="_blank" class="username-link" onclick="event.stopPropagation();">@${username}</a>
        </td>
        <td>
          <a href="https://twitter.com/${username}" target="_blank" class="account-name-link" onclick="event.stopPropagation();">${name}</a>
        </td>
        <td>
          <div class="activity-stat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
            </svg>
            ${formatNumber(followers)}
          </div>
        </td>
        <td style="color:var(--text-secondary);font-weight:600">${formatNumber(mentions)}</td>
        <td style="color:var(--text-secondary);font-weight:600">${formatNumber(replies)}</td>
        <td style="color:var(--text-secondary);font-weight:600">${formatNumber(retweets)}</td>
        <td style="text-align:center;">
          <div class="activity-stat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="20" x2="18" y2="10"/>
              <line x1="12" y1="20" x2="12" y2="4"/>
              <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            ${formatNumber(total)}
          </div>
        </td>
        <td>
          <a href="https://twitter.com/${username}" target="_blank" class="view-profile-btn" onclick="event.stopPropagation();">
            <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            View
          </a>
        </td>
      </tr>`;
    });

    html += '</tbody></table>';
    document.getElementById('tableWrapper').innerHTML = html;
    document.getElementById('pagination').style.display = 'none';
  }

  // ─── User Detail Modal ────────────────────────────────────────────────────
  function openUserModal(userJsonStr) {
    const user = JSON.parse(userJsonStr);
    const name = user.name || user.username || 'Unknown';
    const username = user.username || '';
    const avatarUrl = user.profile_image_url || '';
    const initials = getInitials(name);
    
    let avatarHtml = '';
    const hasValidAvatar = avatarUrl && !avatarUrl.startsWith('/external');
    
    if (hasValidAvatar) {
      avatarHtml = `<img src="${avatarUrl}" class="modal-user-avatar" alt="${name}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="modal-user-avatar-fallback" style="display:none;">${initials}</div>`;
    } else {
      avatarHtml = `<img src="https://unavatar.io/twitter/${username}" class="modal-user-avatar" alt="${name}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="modal-user-avatar-fallback" style="display:none;">${initials}</div>`;
    }

    document.getElementById('userModalBody').innerHTML = `
      <div class="modal-user-row">
        ${avatarHtml}
        <div>
          <div class="modal-user-name">${name}</div>
          <div class="modal-user-scr">@${username}</div>
        </div>
      </div>

      <div class="modal-stats-grid">
        <div class="modal-stat-card">
          <div class="modal-stat-value">${formatNumber(user.followers || 0)}</div>
          <div class="modal-stat-label">Followers</div>
        </div>
        <div class="modal-stat-card">
          <div class="modal-stat-value">${formatNumber(user.following || 0)}</div>
          <div class="modal-stat-label">Following</div>
        </div>
      </div>

      <div class="modal-activity-grid">
        <div class="modal-activity-card mentions">
          <div class="modal-activity-value">${formatNumber(user.mentions || 0)}</div>
          <div class="modal-activity-label">Mentions</div>
        </div>
        <div class="modal-activity-card replies">
          <div class="modal-activity-value">${formatNumber(user.replies || 0)}</div>
          <div class="modal-activity-label">Replies</div>
        </div>
        <div class="modal-activity-card retweets">
          <div class="modal-activity-value">${formatNumber(user.retweets || 0)}</div>
          <div class="modal-activity-label">Retweets</div>
        </div>
      </div>

      <div class="modal-total-badge">
        <span class="modal-total-label">Total Interactions</span>
        <span class="modal-total-value">${formatNumber(user.posts || 0)}</span>
      </div>

      <div class="modal-footer">
        <a href="https://twitter.com/${username}" target="_blank" class="modal-open-twitter">
          <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          View Profile on X
        </a>
      </div>
    `;

    const modal = document.getElementById('userDetailModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
  }

  function closeUserModal() {
    const modal = document.getElementById('userDetailModal');
    modal.classList.remove('show');
    setTimeout(() => modal.style.display = 'none', 300);
  }

  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeUserModal(); });

  // ─── Actions Dropdown ─────────────────────────────────────────────────────
  function toggleActionsDropdown(event) {
    event.stopPropagation();
    document.getElementById('actionsDropdownMenu').classList.toggle('show');
  }

  document.addEventListener('click', () => {
    document.getElementById('actionsDropdownMenu')?.classList.remove('show');
  });

  function exportCSV() {
    document.getElementById('actionsDropdownMenu').classList.remove('show');
    if (!allData.length) return;

    let csv = 'Rank,Username,Name,Followers,Following,Mentions,Replies,Retweets,Total\n';
    allData.forEach((user, i) => {
      const name = (user.name || '').replace(/,/g,' ').replace(/"/g,'""');
      const username = user.username || '';
      csv += `${i+1},"@${username}","${name}",${user.followers||0},${user.following||0},${user.mentions||0},${user.replies||0},${user.retweets||0},${user.posts||0}\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `most_active_users_${startDate}_${endDate}.csv`;
    a.click();
  }

  function refreshData() {
    document.getElementById('actionsDropdownMenu').classList.remove('show');
    window.location.reload();
  }

  function printTable() {
    document.getElementById('actionsDropdownMenu').classList.remove('show');
    const tableContent = document.getElementById('tableWrapper').innerHTML;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
      <!DOCTYPE html><html><head>
        <title>Most Active Users - X</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 20px; }
          h1   { color: #1a202c; margin-bottom: 6px; }
          p    { color: #64748b; margin-bottom: 20px; font-size: 13px; }
          table{ width: 100%; border-collapse: collapse; }
          th   { background: #f8fafc; padding: 10px; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
          td   { padding: 10px; font-size: 12px; border-bottom: 1px solid #f1f5f9; }
          @media print { body { padding: 0; } }
        </style>
      </head><body>
        <h1>Most Active Users — X (Twitter)</h1>
        <p>Date Range: ${startDate} to ${endDate}</p>
        ${tableContent}
      </body></html>
    `);

    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => { printWindow.print(); printWindow.close(); }, 250);
  }
</script>
@endsection