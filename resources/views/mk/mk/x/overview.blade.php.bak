@extends('mk.layouts.app')

@section('title', 'X Overview - SMADIMENT')

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

  /* Main Layout - FIXED WIDTH */
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

  /* Date Filter Card */
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

  /* Stats Grid - Modern Cards */
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

  /* Charts Section */
  .charts-section {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-bottom: 24px;
  }

  @media (min-width: 1024px) {
    .charts-section {
      grid-template-columns: 1.2fr 0.8fr;
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

  .data-table th:first-child {
    padding-left: 20px;
  }

  .data-table th:last-child {
    padding-right: 20px;
  }

  .data-table td {
    padding: 12px;
    font-size: 12px;
    color: var(--text-primary);
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }

  .data-table td:first-child {
    padding-left: 20px;
  }

  .data-table td:last-child {
    padding-right: 20px;
  }

  .data-table tbody tr {
    transition: all 0.2s;
    background: var(--bg-white);
  }

  .data-table tbody tr:hover {
    background: #fafbfc;
  }

  .data-table tbody tr:last-child td {
    border-bottom: none;
  }

  /* Avatar Container */
  .avatar-container {
    position: relative;
    display: inline-block;
  }

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

  .user-avatar {
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

  /* View All Button */
  .view-all-container {
    display: flex;
    justify-content: center;
    padding: 20px 0;
    border-top: 1px solid var(--border-gray);
    margin-top: 16px;
  }

  .view-all-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 28px;
    background: var(--primary-green);
    border: none;
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all 0.3s;
  }

  .view-all-btn:hover {
    background: var(--primary-green-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 128, 71, 0.3);
  }

  .view-all-btn svg {
    width: 18px;
    height: 18px;
  }

  /* All Users Modal */
  .all-users-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .all-users-modal.show {
    display: flex;
    opacity: 1;
  }

  .all-users-modal .modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
  }

  .all-users-modal .modal-content {
    position: relative;
    background: var(--bg-white);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 95%;
    max-width: 1400px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    animation: modalSlideIn 0.3s ease-out;
    z-index: 10000;
  }

  .all-users-modal .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-gray);
    background: var(--bg-white);
    border-radius: 16px 16px 0 0;
  }

  .all-users-modal .modal-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
  }

  .all-users-modal .modal-body {
    padding: 0;
    overflow-y: auto;
    position: relative;
    background: var(--bg-white);
    border-radius: 0 0 16px 16px;
  }

  .all-users-modal .data-table {
    margin: 0;
    background: var(--bg-white);
  }

  .all-users-modal .data-table tbody tr {
    background: var(--bg-white);
  }

  .all-users-modal .data-table tbody tr:hover {
    background: #f8fafc;
  }

  /* User Detail Modal */
  .user-detail-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .user-detail-modal.show {
    opacity: 1;
  }

  .modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
  }

  .modal-content {
    position: relative;
    background: var(--bg-white);
    border-radius: 16px;
    box-shadow: var(--shadow-lg);
    width: 90%;
    max-width: 1200px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    animation: modalSlideIn 0.3s ease-out;
  }

  @keyframes modalSlideIn {
    from {
      transform: translateY(-20px) scale(0.95);
      opacity: 0;
    }
    to {
      transform: translateY(0) scale(1);
      opacity: 1;
    }
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 28px;
    border-bottom: 2px solid var(--bg-gray-50);
  }

  .modal-header h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
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

  .modal-close:hover {
    background: #ef4444;
    color: white;
  }

  .modal-body {
    padding: 28px;
    overflow-y: auto;
    position: relative;
  }

  /* Mentions Modal Styles */
  .mentions-list {
    max-height: 60vh;
    overflow-y: auto;
  }

  .mention-item {
    background: var(--bg-gray-50);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    border: 1px solid var(--border-gray);
    transition: all 0.2s;
  }

  .mention-item:hover {
    background: var(--bg-white);
    box-shadow: var(--shadow-sm);
  }

  .mention-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }

  .mention-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .mention-info strong {
    color: var(--text-primary);
    font-size: 14px;
  }

  .mention-date {
    color: var(--text-secondary);
    font-size: 12px;
  }

  .sentiment-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }

  .mention-text {
    color: var(--text-primary);
    line-height: 1.6;
    font-size: 14px;
  }

  .mentions-loading {
    padding: 40px 20px;
  }

  /* Loading States */
  .loading-skeleton {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s ease-in-out infinite;
    border-radius: 8px;
  }

  @keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .skeleton-text {
    height: 44px;
    margin-bottom: 8px;
  }

  /* Lazy Loading Badge */
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
    to { transform: rotate(360deg); }
  }

  /* Animations */
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

  /* ========================================
     DATE PICKER STYLES
     ======================================== */
  
  /* Date Picker Trigger Button */
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
      transform: translateY(20px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
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
    align-items: center;
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

  /* ========================================
     RESPONSIVE STYLES
     ======================================== */

  @media (max-width: 1400px) {
    .data-table {
      font-size: 12px;
    }
    
    .data-table th,
    .data-table td {
      padding: 10px 12px;
    }

    .data-table th:first-child,
    .data-table td:first-child {
      padding-left: 16px;
    }

    .data-table th:last-child,
    .data-table td:last-child {
      padding-right: 16px;
    }
  }

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

    .data-table {
      min-width: 900px;
    }
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
  }

  @media (max-width: 640px) {
    .stat-value {
      font-size: 28px;
    }

    .chart-container {
      height: 250px;
    }

    .table-search {
      width: 100%;
    }

    .page-header h1 {
      font-size: 24px;
    }

    .modal-content {
      width: 95%;
      max-height: 90vh;
    }

    .modal-header, .modal-body {
      padding: 20px;
    }

    .data-table {
      font-size: 11px;
    }

    .date-picker-trigger {
      max-width: 100%;
    }

    .date-picker-container {
      width: 100%;
      max-height: 100vh;
      border-radius: 0;
    }

    .date-picker-sidebar {
      border-radius: 0;
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
  
  <!-- Page Header -->
  <div class="page-header">
    <h1>X Overview Dashboard</h1>
    <p>Monitor and analyze your X (Twitter) social media performance metrics</p>
  </div>

  @if(!$projectId)
  <div class="alert alert-warning">
    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: currentColor; fill: none;">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <span>No project selected. Please select a project from the sidebar to view X Overview data.</span>
  </div>
  @else

  <!-- Date Filter Card with Date Picker -->
  <div class="filter-card">
    <form id="filterForm" method="GET" action="{{ route('mk.x.overview') }}">
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
    
    <!-- Total Users Card -->
    <div class="stat-card" data-lazy-load="totalUsers">
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
      
      <div id="totalUsersValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Total Authors Card -->
    <div class="stat-card" data-lazy-load="totalAuthors">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Total Authors</div>
      
      <div id="totalAuthorsValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Volume Total Card -->
    <div class="stat-card" data-lazy-load="volumeTotal">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Volume Total</div>
      
      <div id="volumeTotalValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

    <!-- Sentiment Score Card -->
    <div class="stat-card" data-lazy-load="sentiment">
      <div class="stat-header">
        <div class="stat-icon-wrapper">
          <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
            <line x1="9" y1="9" x2="9.01" y2="9"/>
            <line x1="15" y1="9" x2="15.01" y2="9"/>
          </svg>
        </div>
      </div>
      
      <div class="stat-label">Sentiment Score</div>
      
      <div id="sentimentValue" class="stat-value-wrapper">
        <div class="loading-skeleton skeleton-text" style="width: 140px;"></div>
      </div>
      
      <div class="stat-progress">
        <div class="stat-progress-bar" style="width: 0%"></div>
      </div>
    </div>

  </div>

  <!-- Charts Section -->
  <div class="charts-section">
    
    <!-- Volume Trend Chart -->
    <div class="chart-card" data-lazy-load="volumeTotal">
      <div class="chart-header">
        <div class="chart-title-group">
          <h3>Volume Trend</h3>
          <p class="chart-subtitle">Daily posting volume over time</p>
        </div>
      </div>
      
      <div class="chart-container">
        <div id="volumeTrendLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="volumeTrendChart" style="display: none;"></canvas>
      </div>
    </div>

    <!-- Sentiment Distribution Chart -->
    <div class="chart-card" data-lazy-load="sentiment">
      <div class="chart-header">
        <div class="chart-title-group">
          <h3>Sentiment Distribution</h3>
          <p class="chart-subtitle">Positive, neutral, and negative breakdown</p>
        </div>
      </div>
      
      <div class="chart-container">
        <div id="sentimentLoading" class="loading-skeleton" style="height: 100%;"></div>
        <canvas id="sentimentChart" style="display: none;"></canvas>
      </div>
    </div>

  </div>

  <!-- Most Active Users Table -->
  <div class="table-section" data-lazy-load="activeUsers">
    <div class="table-header">
      <div class="table-title">
        <h3>Most Active Users</h3>
        <p class="table-subtitle">Top 10 users with highest posting frequency</p>
      </div>
      <div class="table-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/>
          <path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="userSearchInput" placeholder="Search users..." onkeyup="filterUsers()">
      </div>
    </div>
    
    <div id="activeUsersLoading" class="loading-skeleton" style="height: 400px;"></div>
    <div id="activeUsersTable" style="display: none; overflow-x: auto;"></div>
    
    <!-- View All Button -->
    <div id="viewAllContainer" class="view-all-container" style="display: none;">
      <button class="view-all-btn" onclick="showAllUsersModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        View All Users (<span id="remainingCount">0</span> more)
      </button>
    </div>
  </div>

  @endif

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    
    const firstDayOfWeek = firstDay.getDay();
    for (let i = firstDayOfWeek - 1; i >= 0; i--) {
      const day = prevLastDay.getDate() - i;
      html += `<button type="button" class="calendar-day other-month" disabled>${day}</button>`;
    }
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
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
      html += `<button type="button" class="calendar-day ${classes}" data-date="${dateStr}" ${disabled}>${day}</button>`;
    }
    
    const lastDayOfWeek = lastDay.getDay();
    for (let i = 1; i < 7 - lastDayOfWeek; i++) {
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
// MAIN DASHBOARD LOGIC
// ========================================
const projectId = '{{ $projectId ?? '' }}';
const startDate = '{{ $startDate ?? '' }}';
const endDate = '{{ $endDate ?? '' }}';

let allUsers = [];
let displayedCount = 10;

if (projectId && startDate && endDate) {
  
  function formatNumber(num) {
    return new Intl.NumberFormat('en-US').format(num);
  }

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
          
          switch(componentId) {
            case 'totalUsers':
              loadTotalUsers();
              break;
            case 'totalAuthors':
              loadTotalAuthors();
              break;
            case 'volumeTotal':
              loadVolumeTotal();
              break;
            case 'sentiment':
              loadSentimentTotal();
              break;
            case 'activeUsers':
              loadMostActiveUsers();
              break;
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

  async function loadTotalUsers() {
    const card = document.querySelector('[data-lazy-load="totalUsers"]');
    addLoadingBadge(card);
    
    try {
      const response = await fetch(`/mk/api/x/total-users?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      if (result.success && result.data) {
        const total = result.data.total || 0;
        const valueEl = document.getElementById('totalUsersValue');
        valueEl.innerHTML = `<div class="stat-value">${formatNumber(total)}</div>`;
        valueEl.classList.add('data-loaded');
        
        animateProgress(card, 75);
      }
    } catch (error) {
      console.error('Error loading total users:', error);
    } finally {
      removeLoadingBadge(card);
      card.classList.add('loaded');
    }
  }

  async function loadTotalAuthors() {
    const card = document.querySelector('[data-lazy-load="totalAuthors"]');
    addLoadingBadge(card);
    
    try {
      const response = await fetch(`/mk/api/x/total-authors?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      if (result.success && result.data) {
        const total = result.data.total || 0;
        const valueEl = document.getElementById('totalAuthorsValue');
        valueEl.innerHTML = `<div class="stat-value">${formatNumber(total)}</div>`;
        valueEl.classList.add('data-loaded');
        
        animateProgress(card, 68);
      }
    } catch (error) {
      console.error('Error loading total authors:', error);
    } finally {
      removeLoadingBadge(card);
      card.classList.add('loaded');
    }
  }

  async function loadVolumeTotal() {
    const cards = document.querySelectorAll('[data-lazy-load="volumeTotal"]');
    cards.forEach(card => addLoadingBadge(card));
    
    try {
      const response = await fetch(`/mk/api/x/volume-total?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      if (result.success && result.data) {
        const total = result.data.total || 0;
        const valueEl = document.getElementById('volumeTotalValue');
        valueEl.innerHTML = `<div class="stat-value">${formatNumber(total)}</div>`;
        valueEl.classList.add('data-loaded');
        
        const chartData = result.data.chart || [];
        renderVolumeTrendChart(chartData);
        
        const statCard = document.querySelector('.stat-card[data-lazy-load="volumeTotal"]');
        animateProgress(statCard, 82);
      }
    } catch (error) {
      console.error('Error loading volume total:', error);
    } finally {
      cards.forEach(card => {
        removeLoadingBadge(card);
        card.classList.add('loaded');
      });
    }
  }

  async function loadSentimentTotal() {
    const cards = document.querySelectorAll('[data-lazy-load="sentiment"]');
    cards.forEach(card => addLoadingBadge(card));
    
    try {
      const response = await fetch(`/mk/api/x/sentiment-total?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      if (result.success && result.data) {
        const positive = result.data.positive || 0;
        const neutral = result.data.neutral || 0;
        const negative = result.data.negative || 0;
        const total = positive + neutral + negative;
        
        const score = total > 0 ? ((positive * 100 + neutral * 50) / total).toFixed(1) : 0;
        
        const valueEl = document.getElementById('sentimentValue');
        valueEl.innerHTML = `<div class="stat-value">${score}%</div>`;
        valueEl.classList.add('data-loaded');
        
        renderSentimentChart({ positive, neutral, negative });
        
        const statCard = document.querySelector('.stat-card[data-lazy-load="sentiment"]');
        animateProgress(statCard, parseFloat(score));
      }
    } catch (error) {
      console.error('Error loading sentiment:', error);
    } finally {
      cards.forEach(card => {
        removeLoadingBadge(card);
        card.classList.add('loaded');
      });
    }
  }

  async function loadMostActiveUsers() {
    const card = document.querySelector('[data-lazy-load="activeUsers"]');
    addLoadingBadge(card);
    
    try {
      const response = await fetch(`/mk/api/x/most-active-users?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}`);
      const result = await response.json();
      
      const container = document.getElementById('activeUsersTable');
      const loading = document.getElementById('activeUsersLoading');
      const viewAllContainer = document.getElementById('viewAllContainer');
      
      if (result.success && result.data && result.data.data) {
        allUsers = result.data.data;
        displayUsersTable(10);
        
        if (allUsers.length > 10) {
          viewAllContainer.style.display = 'flex';
          document.getElementById('remainingCount').textContent = allUsers.length - 10;
        }
        
        loading.style.display = 'none';
        container.style.display = 'block';
      }
    } catch (error) {
      console.error('Error loading active users:', error);
    } finally {
      removeLoadingBadge(card);
      card.classList.add('loaded');
    }
  }

  function displayUsersTable(count) {
    const container = document.getElementById('activeUsersTable');
    const users = allUsers.slice(0, count);
    
    let html = '<table class="data-table"><thead><tr>';
    html += '<th>NO.</th><th>AVATAR</th><th>NAME</th><th>ACCOUNT NAME</th><th>ENGAGEMENT</th><th>POSTS</th><th>RETWEETS</th><th>REPLIES</th><th>FOLLOWERS</th><th>FOLLOWING</th></tr></thead><tbody>';
    
    users.forEach((item, index) => {
      const username = item.username || item.author || item.name || 'Unknown';
      const profileUrl = item.profile_url || item.profile_image_url || '';
      const accountName = item.contentJson?.name || item.name || username;
      const followers = item.followers || item.contentJson?.followers_count || 0;
      const following = item.contentJson?.friends_count || 0;
      const mentions = item.mentions || 0;
      const replies = item.replies || 0;
      const retweets = item.retweets || 0;
      const totalPosts = item.posts || item.y || (mentions + replies + retweets);
      const engagement = totalPosts;
      
      html += `<tr>
        <td><strong>${index + 1}</strong></td>
        <td>
          <div class="avatar-container">
            ${profileUrl ? 
              `<img src="${profileUrl}" alt="${username}" class="user-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
               <div class="user-avatar-fallback" style="display:none;">${username.charAt(0).toUpperCase()}</div>` 
              : 
              `<div class="user-avatar">${username.charAt(0).toUpperCase()}</div>`
            }
          </div>
        </td>
        <td>
          <a href="#" class="username-link" onclick='event.preventDefault(); showMentionsModal("${username.replace(/'/g, "\\'")}"); return false;'>@${username}</a>
        </td>
        <td>
          <a href="https://twitter.com/${username}" target="_blank" class="account-name-link">${accountName}</a>
        </td>
        <td><strong>${formatNumber(engagement)}</strong></td>
        <td>${formatNumber(totalPosts)}</td>
        <td>${formatNumber(retweets)}</td>
        <td>${formatNumber(replies)}</td>
        <td>${formatNumber(followers)}</td>
        <td>${formatNumber(following)}</td>
      </tr>`;
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
    container.classList.add('data-loaded');
    
    displayedCount = count;
  }

  function showAllUsersModal() {
    const modal = document.createElement('div');
    modal.className = 'all-users-modal';
    modal.innerHTML = `
      <div class="modal-overlay" onclick="this.parentElement.remove()"></div>
      <div class="modal-content">
        <div class="modal-header">
          <h3>All Active Users (${allUsers.length} total)</h3>
          <button class="modal-close" onclick="this.closest('.all-users-modal').remove()">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <div id="allUsersTableContent"></div>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
    
    const container = document.getElementById('allUsersTableContent');
    let html = '<table class="data-table"><thead><tr>';
    html += '<th>NO.</th><th>AVATAR</th><th>NAME</th><th>ACCOUNT NAME</th><th>ENGAGEMENT</th><th>POSTS</th><th>RETWEETS</th><th>REPLIES</th><th>FOLLOWERS</th><th>FOLLOWING</th></tr></thead><tbody>';
    
    allUsers.forEach((item, index) => {
      const username = item.username || item.author || item.name || 'Unknown';
      const profileUrl = item.profile_url || item.profile_image_url || '';
      const accountName = item.contentJson?.name || item.name || username;
      const followers = item.followers || item.contentJson?.followers_count || 0;
      const following = item.contentJson?.friends_count || 0;
      const mentions = item.mentions || 0;
      const replies = item.replies || 0;
      const retweets = item.retweets || 0;
      const totalPosts = item.posts || item.y || (mentions + replies + retweets);
      const engagement = totalPosts;
      
      html += `<tr>
        <td><strong>${index + 1}</strong></td>
        <td>
          <div class="avatar-container">
            ${profileUrl ? 
              `<img src="${profileUrl}" alt="${username}" class="user-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
               <div class="user-avatar-fallback" style="display:none;">${username.charAt(0).toUpperCase()}</div>` 
              : 
              `<div class="user-avatar">${username.charAt(0).toUpperCase()}</div>`
            }
          </div>
        </td>
        <td>
          <a href="#" class="username-link" onclick='event.preventDefault(); showMentionsModal("${username.replace(/'/g, "\\'")}"); return false;'>@${username}</a>
        </td>
        <td>
          <a href="https://twitter.com/${username}" target="_blank" class="account-name-link">${accountName}</a>
        </td>
        <td><strong>${formatNumber(engagement)}</strong></td>
        <td>${formatNumber(totalPosts)}</td>
        <td>${formatNumber(retweets)}</td>
        <td>${formatNumber(replies)}</td>
        <td>${formatNumber(followers)}</td>
        <td>${formatNumber(following)}</td>
      </tr>`;
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
  }

  function filterUsers() {
    const searchTerm = document.getElementById('userSearchInput').value.toLowerCase();
    
    if (!searchTerm) {
      displayUsersTable(displayedCount);
      return;
    }
    
    const filteredUsers = allUsers.filter(user => {
      const username = (user.username || user.author || user.name || '').toLowerCase();
      const accountName = (user.contentJson?.name || user.name || '').toLowerCase();
      return username.includes(searchTerm) || accountName.includes(searchTerm);
    });
    
    const container = document.getElementById('activeUsersTable');
    let html = '<table class="data-table"><thead><tr>';
    html += '<th>NO.</th><th>AVATAR</th><th>NAME</th><th>ACCOUNT NAME</th><th>ENGAGEMENT</th><th>POSTS</th><th>RETWEETS</th><th>REPLIES</th><th>FOLLOWERS</th><th>FOLLOWING</th></tr></thead><tbody>';
    
    if (filteredUsers.length === 0) {
      html += '<tr><td colspan="10" style="text-align: center; padding: 40px; color: var(--text-secondary);">No users found matching "' + searchTerm + '"</td></tr>';
    } else {
      filteredUsers.forEach((item, index) => {
        const username = item.username || item.author || item.name || 'Unknown';
        const profileUrl = item.profile_url || item.profile_image_url || '';
        const accountName = item.contentJson?.name || item.name || username;
        const followers = item.followers || item.contentJson?.followers_count || 0;
        const following = item.contentJson?.friends_count || 0;
        const mentions = item.mentions || 0;
        const replies = item.replies || 0;
        const retweets = item.retweets || 0;
        const totalPosts = item.posts || item.y || (mentions + replies + retweets);
        const engagement = totalPosts;
        
        html += `<tr>
          <td><strong>${index + 1}</strong></td>
          <td>
            <div class="avatar-container">
              ${profileUrl ? 
                `<img src="${profileUrl}" alt="${username}" class="user-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                 <div class="user-avatar-fallback" style="display:none;">${username.charAt(0).toUpperCase()}</div>` 
                : 
                `<div class="user-avatar">${username.charAt(0).toUpperCase()}</div>`
              }
            </div>
          </td>
          <td>
            <a href="#" class="username-link" onclick='event.preventDefault(); showMentionsModal("${username.replace(/'/g, "\\'")}"); return false;'>@${username}</a>
          </td>
          <td>
            <a href="https://twitter.com/${username}" target="_blank" class="account-name-link">${accountName}</a>
          </td>
          <td><strong>${formatNumber(engagement)}</strong></td>
          <td>${formatNumber(totalPosts)}</td>
          <td>${formatNumber(retweets)}</td>
          <td>${formatNumber(replies)}</td>
          <td>${formatNumber(followers)}</td>
          <td>${formatNumber(following)}</td>
        </tr>`;
      });
    }
    
    html += '</tbody></table>';
    container.innerHTML = html;
  }

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

  function renderVolumeTrendChart(data) {
    const canvas = document.getElementById('volumeTrendChart');
    const loading = document.getElementById('volumeTrendLoading');
    
    if (!data || data.length === 0) {
      loading.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No data available</p>';
      return;
    }

    const ctx = canvas.getContext('2d');
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map(d => d.date),
        datasets: [{
          label: 'Volume',
          data: data.map(d => d.count || d.value || 0),
          borderColor: '#038047',
          backgroundColor: 'rgba(3, 128, 71, 0.1)',
          borderWidth: 3,
          tension: 0.4,
          fill: true,
          pointRadius: 5,
          pointHoverRadius: 7,
          pointBackgroundColor: '#038047',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointHoverBackgroundColor: '#026738',
          pointHoverBorderColor: '#ffffff',
          pointHoverBorderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
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
            cornerRadius: 8
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f1f5f9', drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 12 },
              padding: 8
            }
          },
          x: {
            grid: { display: false, drawBorder: false },
            ticks: { 
              color: '#64748b', 
              font: { family: 'Poppins', size: 12 },
              padding: 8
            }
          }
        }
      }
    });

    loading.style.display = 'none';
    canvas.style.display = 'block';
  }

  function renderSentimentChart(sentiment) {
    const canvas = document.getElementById('sentimentChart');
    const loading = document.getElementById('sentimentLoading');
    
    const ctx = canvas.getContext('2d');
    
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Positive', 'Neutral', 'Negative'],
        datasets: [{
          data: [sentiment.positive, sentiment.neutral, sentiment.negative],
          backgroundColor: ['#10b981', '#64748b', '#ef4444'],
          borderWidth: 0,
          hoverOffset: 15
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#1a202c',
              font: { family: 'Poppins', size: 13, weight: '600' },
              padding: 20,
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
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
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((context.parsed / total) * 100).toFixed(1);
                return `${context.label}: ${formatNumber(context.parsed)} (${percentage}%)`;
              }
            }
          }
        }
      }
    });

    loading.style.display = 'none';
    canvas.style.display = 'block';
  }
}

// Global function to show mentions modal
function showMentionsModal(username) {
  const modal = document.createElement('div');
  modal.className = 'user-detail-modal';
  modal.innerHTML = `
    <div class="modal-overlay" onclick="this.parentElement.remove()"></div>
    <div class="modal-content">
      <div class="modal-header">
        <h3>Mentions of @${username}</h3>
        <button class="modal-close" onclick="this.closest('.user-detail-modal').remove()">
          <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      <div class="modal-body">
        <div class="mentions-loading">
          <div class="spinner" style="width: 40px; height: 40px; border-width: 4px; margin: 40px auto;"></div>
          <p style="text-align: center; color: var(--text-secondary); margin-top: 20px;">Loading mentions...</p>
        </div>
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
  setTimeout(() => modal.classList.add('show'), 10);

  fetch(`/mk/api/x/user-mentions?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}&username=${username}`)
    .then(response => response.json())
    .then(result => {
      const modalBody = modal.querySelector('.modal-body');
      
      if (result.success && result.data && result.data.mentions) {
        const mentions = result.data.mentions;
        
        let html = '<div class="mentions-list">';
        
        if (mentions.length === 0) {
          html += '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">No mentions found for this user.</p>';
        } else {
          mentions.forEach(mention => {
            const tweetText = mention.text || mention.content || 'No content';
            const tweetDate = mention.created_at || mention.date || '';
            const sentiment = mention.sentiment || 'neutral';
            const sentimentColor = sentiment === 'positive' ? '#10b981' : sentiment === 'negative' ? '#ef4444' : '#64748b';
            const sentimentLabel = sentiment.charAt(0).toUpperCase() + sentiment.slice(1);
            
            html += `
              <div class="mention-item">
                <div class="mention-header">
                  <div class="mention-info">
                    <strong>@${username}</strong>
                    <span class="mention-date">${tweetDate}</span>
                  </div>
                  <span class="sentiment-badge" style="background: ${sentimentColor}20; color: ${sentimentColor};">
                    ${sentimentLabel}
                  </span>
                </div>
                <div class="mention-text">${tweetText}</div>
              </div>
            `;
          });
        }
        
        html += '</div>';
        modalBody.innerHTML = html;
      } else {
        modalBody.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 40px;">Failed to load mentions.</p>';
      }
    })
    .catch(error => {
      console.error('Error loading mentions:', error);
      const modalBody = modal.querySelector('.modal-body');
      modalBody.innerHTML = '<p style="text-align: center; color: #ef4444; padding: 40px;">Error loading mentions. Please try again.</p>';
    });
}
</script>
@endsection