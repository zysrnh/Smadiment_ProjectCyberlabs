<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'SMADIMENT - Analytics Platform')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    :root {
      /* B24-inspired Modern Color Palette */
      --primary-green: #038047;
      --primary-green-dark: #026738;
      --primary-green-light: #04995a;
      --accent-blue: #2FC6F6;
      --dark-bg: #0f172a;
      --sidebar-bg: #ffffff;
      --sidebar-hover: #f8fafc;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --text-muted: #94a3b8;
      --border-color: #e2e8f0;
      --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
      --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: #f8fafc;
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
      font-size: 14px;
    }

    /* ========================================================
       SIDEBAR - B24 Style
       ======================================================== */
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 280px;
      height: 100vh;
      background: var(--sidebar-bg);
      border-right: 1px solid var(--border-color);
      padding: 0;
      z-index: 100;
      overflow-y: auto;
      box-shadow: 2px 0 8px rgba(0, 0, 0, 0.02);
    }

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: transparent;
    }

    .sidebar::-webkit-scrollbar-thumb {
      background: var(--border-color);
      border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
      background: var(--text-muted);
    }

    /* Logo Area */
    .logo {
      padding: 28px 24px 24px;
      border-bottom: 1px solid var(--border-color);
      background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .logo-image {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 8px;
    }

    .logo-image img {
      height: 40px;
      width: auto;
    }

    .logo h1 {
      font-size: 24px;
      font-weight: 800;
      color: var(--primary-green);
      letter-spacing: -0.5px;
      margin: 0;
    }

    .logo p {
      font-size: 12px;
      color: var(--text-secondary);
      font-weight: 500;
      margin: 0;
      padding-left: 2px;
    }

    /* Navigation Container */
    .nav-container {
      padding: 20px 16px;
    }

    .nav-section {
      margin-bottom: 28px;
    }

    .nav-label {
      font-size: 10px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 1.2px;
      margin-bottom: 8px;
      padding: 0 12px;
    }

    /* Navigation Items - B24 Style */
    .nav-item {
      padding: 10px 12px;
      margin-bottom: 2px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 500;
      color: var(--text-secondary);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .nav-item:hover {
      background: var(--sidebar-hover);
      color: var(--text-primary);
      transform: translateX(2px);
    }

    .nav-item.active {
      background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
      color: #ffffff;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(3, 128, 71, 0.15);
    }

    .nav-item.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 60%;
      background: #ffffff;
      border-radius: 0 3px 3px 0;
    }

    /* Icon Styling */
    .nav-icon {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: transparent;
      border-radius: 8px;
      flex-shrink: 0;
      transition: all 0.2s;
    }

    .nav-item:hover .nav-icon {
      background: rgba(3, 128, 71, 0.08);
    }

    .nav-item.active .nav-icon {
      background: rgba(255, 255, 255, 0.15);
    }

    .nav-icon svg {
      width: 20px;
      height: 20px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    /* Sub Navigation */
    .nav-sub {
      margin-left: 48px;
      margin-top: 4px;
      padding-left: 12px;
      border-left: 2px solid var(--border-color);
    }

    .nav-sub .nav-item {
      font-size: 13px;
      padding: 8px 12px;
      gap: 10px;
    }

    .nav-sub .nav-item::before {
      display: none;
    }

    /* ========================================================
       MAIN CONTENT - B24 Style
       ======================================================== */
    .main-content {
      margin-left: 280px;
      padding: 0;
      min-height: 100vh;
      background: #f8fafc;
    }

    /* Top Bar - Modern Header */
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 24px 32px;
      background: #ffffff;
      border-bottom: 1px solid var(--border-color);
      margin-bottom: 24px;
      gap: 24px;
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .page-title {
      flex: 1;
    }

    .page-title h2 {
      font-size: 28px;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 4px;
      letter-spacing: -0.5px;
    }

    .page-subtitle {
      font-size: 14px;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .top-actions {
      display: flex;
      gap: 12px;
    }

    /* Action Buttons - Modern Style */
    .action-btn {
      padding: 10px 20px;
      border-radius: 10px;
      border: 1px solid var(--border-color);
      background: #ffffff;
      color: var(--text-primary);
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: var(--card-shadow);
    }

    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--card-shadow-hover);
      border-color: var(--primary-green);
      color: var(--primary-green);
    }

    .action-btn.primary {
      background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
      color: #ffffff;
      border-color: transparent;
    }

    .action-btn.primary:hover {
      background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
      transform: translateY(-2px);
      box-shadow: 0 12px 24px -6px rgba(3, 128, 71, 0.25);
    }

    /* Content Wrapper */
    .content-wrapper {
      padding: 0 32px 32px;
    }

    /* ========================================================
       CARDS - Modern B24 Style
       ======================================================== */
    .section {
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid var(--border-color);
      overflow: hidden;
      margin-bottom: 24px;
      box-shadow: var(--card-shadow);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .section:hover {
      box-shadow: var(--card-shadow-hover);
      transform: translateY(-2px);
    }

    .section-header {
      padding: 20px 24px;
      background: #ffffff;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .section-title {
      font-size: 18px;
      font-weight: 700;
      color: var(--text-primary);
      letter-spacing: -0.3px;
    }

    .item-count {
      background: var(--primary-green);
      color: #ffffff;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
    }

    .section-body {
      padding: 24px;
    }

    /* ========================================================
       CHARTS
       ======================================================== */
    .chart-container {
      position: relative;
      height: 320px;
      margin-bottom: 20px;
    }

    .chart-container.large {
      height: 420px;
    }

    .chart-container.donut {
      height: 380px;
    }

    /* ========================================================
       MODE TOGGLE
       ======================================================== */
    .mode-toggle {
      display: flex;
      gap: 6px;
      margin-bottom: 16px;
      background: var(--sidebar-hover);
      padding: 4px;
      border-radius: 10px;
      width: fit-content;
    }

    .mode-btn {
      padding: 8px 16px;
      border: none;
      background: transparent;
      color: var(--text-secondary);
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .mode-btn:hover {
      background: #ffffff;
      color: var(--text-primary);
    }

    .mode-btn.active {
      background: var(--primary-green);
      color: #ffffff;
      box-shadow: 0 2px 4px rgba(3, 128, 71, 0.2);
    }

    /* ========================================================
       STATS CARDS - B24 Style
       ======================================================== */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 28px;
    }

    .stat-card {
      background: #ffffff;
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 20px;
      box-shadow: var(--card-shadow);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card:hover {
      box-shadow: var(--card-shadow-hover);
      transform: translateY(-4px);
      border-color: var(--primary-green);
    }

    .stat-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .stat-name {
      font-size: 11px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    .stat-percentage {
      font-size: 16px;
      font-weight: 800;
      color: var(--text-primary);
    }

    .stat-number {
      font-size: 32px;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 12px;
      letter-spacing: -1px;
    }

    .progress-bar {
      height: 8px;
      background: var(--sidebar-hover);
      border-radius: 10px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      border-radius: 10px;
      transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .progress-positive {
      background: linear-gradient(90deg, var(--primary-green), var(--primary-green-light));
    }

    .progress-neutral {
      background: linear-gradient(90deg, #60a5fa, #3b82f6);
    }

    .progress-negative {
      background: linear-gradient(90deg, #f87171, #ef4444);
    }

    /* ========================================================
       TABLE - Modern Style
       ======================================================== */
    .data-table-wrapper {
      border: 1px solid var(--border-color);
      border-radius: 12px;
      overflow: hidden;
      background: #ffffff;
    }

    .data-table {
      width: 100%;
      border-collapse: collapse;
    }

    .data-table thead {
      background: var(--sidebar-hover);
    }

    .data-table th {
      padding: 14px 16px;
      text-align: left;
      font-size: 11px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    .data-table td {
      padding: 14px 16px;
      font-size: 14px;
      font-weight: 500;
      color: var(--text-primary);
      border-bottom: 1px solid var(--border-color);
    }

    .data-table tr:last-child td {
      border-bottom: none;
    }

    .data-table tbody tr {
      transition: all 0.15s;
    }

    .data-table tbody tr:hover {
      background: var(--sidebar-hover);
    }

    .count-cell {
      text-align: right;
      font-weight: 700;
      color: var(--primary-green);
    }

    /* ========================================================
       EMPTY STATE
       ======================================================== */
    .empty-state {
      text-align: center;
      padding: 64px 24px;
    }

    .empty-icon {
      font-size: 48px;
      margin-bottom: 16px;
      opacity: 0.2;
      font-weight: 300;
      color: var(--text-muted);
    }

    .empty-text {
      font-size: 15px;
      font-weight: 500;
      color: var(--text-secondary);
    }

    /* ========================================================
       DEBUG
       ======================================================== */
    .debug-toggle {
      font-size: 13px;
      font-weight: 600;
      color: var(--primary-green);
      cursor: pointer;
      padding: 10px 16px;
      background: var(--sidebar-hover);
      border-radius: 10px;
      display: inline-block;
      margin-bottom: 16px;
      user-select: none;
      border: 1px solid var(--border-color);
      transition: all 0.2s;
    }

    .debug-toggle:hover {
      background: var(--primary-green);
      color: #ffffff;
      border-color: var(--primary-green);
    }

    .debug-content {
      background: var(--dark-bg);
      color: #e2e8f0;
      padding: 20px;
      border-radius: 12px;
      overflow: auto;
      max-height: 300px;
      font-family: 'SF Mono', 'Consolas', 'Monaco', monospace;
      font-size: 12px;
      line-height: 1.6;
      border: 1px solid var(--border-color);
    }

    /* ========================================================
       TOPIC CARD
       ======================================================== */
    .topic-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid var(--border-color);
    }

    .topic-card:hover {
      transform: translateX(4px);
      border-color: var(--primary-green);
      box-shadow: -4px 4px 16px rgba(3, 128, 71, 0.1);
    }

    .topic-card a {
      transition: all 0.2s;
    }

    .topic-card a:hover {
      color: var(--primary-green) !important;
    }

    /* ========================================================
       RESPONSIVE
       ======================================================== */
    @media (max-width: 1200px) {
      .stats-container {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .main-content {
        margin-left: 0;
      }

      .top-bar {
        padding: 20px;
        flex-direction: column;
        align-items: flex-start;
      }

      .content-wrapper {
        padding: 0 20px 20px;
      }

      .top-actions {
        width: 100%;
      }

      .action-btn {
        flex: 1;
      }
    }

    @yield('styles')
  </style>
</head>

<body>

  <!-- ============================================================
       SIDEBAR
       ============================================================ -->
  <div class="sidebar">
    <div class="logo">
      <div class="logo-image">
        <img src="{{ asset('images/SMADIMENT 2025 _ Logo-03.png') }}" alt="SMADIMENT Logo">
      </div>
      <p>Social Media Analytics Platform</p>
    </div>

    <div class="nav-container">
      <!-- MAIN -->
      <div class="nav-section">
        <div class="nav-label">Main</div>

        <a href="{{ route('mk.dashboard') }}" class="nav-item {{ request()->routeIs('mk.dashboard') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          </span>
          <span>Dashboard</span>
        </a>

        <a href="{{ route('mk.data-overview') }}" class="nav-item {{ request()->routeIs('mk.data-overview') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          </span>
          <span>Data Overview</span>
        </a>

        <a href="{{ route('mk.projects') }}" class="nav-item {{ request()->routeIs('mk.projects') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Projects</span>
        </a>
      </div>

      <!-- ANALYTICS -->
      <div class="nav-section">
        <div class="nav-label">Analytics</div>

        <a href="{{ route('mk.sentiment') }}" class="nav-item {{ request()->routeIs('mk.sentiment') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Sentiment Analysis</span>
        </a>

        <a href="{{ route('mk.geographic') }}" class="nav-item {{ request()->routeIs('mk.geographic') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          </span>
          <span>Geographic Data</span>
        </a>

        <!-- Authors Submenu -->
        <div class="nav-item {{ request()->routeIs('mk.authors.*') ? 'active' : '' }}" style="cursor: default;">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </span>
          <span>Author Demographics</span>
        </div>
        <div class="nav-sub">
          <a href="{{ route('mk.authors.age') }}" class="nav-item {{ request()->routeIs('mk.authors.age') ? 'active' : '' }}">
            <span>Age Distribution</span>
          </a>
          <a href="{{ route('mk.authors.gender') }}" class="nav-item {{ request()->routeIs('mk.authors.gender') ? 'active' : '' }}">
            <span>Gender Distribution</span>
          </a>
          <a href="{{ route('mk.authors.type') }}" class="nav-item {{ request()->routeIs('mk.authors.type') ? 'active' : '' }}">
            <span>Organization Type</span>
          </a>
        </div>

        <a href="{{ route('mk.categories') }}" class="nav-item {{ request()->routeIs('mk.categories') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          </span>
          <span>Categories</span>
        </a>

        <!-- Engagement Submenu -->
        <div class="nav-item {{ request()->routeIs('mk.engagement.*') ? 'active' : '' }}" style="cursor: default;">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </span>
          <span>Engagement Metrics</span>
        </div>
        <div class="nav-sub">
          <a href="{{ route('mk.engagement.reach') }}" class="nav-item {{ request()->routeIs('mk.engagement.reach') ? 'active' : '' }}">
            <span>Estimated Reach</span>
          </a>
          <a href="{{ route('mk.engagement.urls') }}" class="nav-item {{ request()->routeIs('mk.engagement.urls') ? 'active' : '' }}">
            <span>Shared URLs</span>
          </a>
          <a href="{{ route('mk.engagement.users') }}" class="nav-item {{ request()->routeIs('mk.engagement.users') ? 'active' : '' }}">
            <span>Active Users</span>
          </a>
          <a href="{{ route('mk.engagement.retweets') }}" class="nav-item {{ request()->routeIs('mk.engagement.retweets') ? 'active' : '' }}">
            <span>Most Retweets</span>
          </a>
        </div>
      </div>

      <!-- CONTENT -->
      <div class="nav-section">
        <div class="nav-label">Content</div>

        <a href="{{ route('mk.publisher') }}" class="nav-item {{ request()->routeIs('mk.publisher') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
          </span>
          <span>Publisher Stats</span>
        </a>

        <a href="{{ route('mk.topics') }}" class="nav-item {{ request()->routeIs('mk.topics') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          </span>
          <span>Recent Topics</span>
        </a>
      </div>

      <!-- TOOLS -->
      <div class="nav-section">
        <div class="nav-label">Tools</div>

        <a href="#" class="nav-item">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
          </span>
          <span>Settings</span>
        </a>

        <a href="#" class="nav-item">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="10" y1="9" x2="16" y2="9"/><line x1="10" y1="13" x2="16" y2="13"/></svg>
          </span>
          <span>Documentation</span>
        </a>
      </div>
    </div>
  </div>

  <!-- ============================================================
       MAIN CONTENT
       ============================================================ -->
  <div class="main-content">
    @yield('content')
  </div>

  <script>
    // Global color palette - Modern B24 Theme
    const colors = {
      primaryGreen: '#038047',
      primaryGreenDark: '#026738',
      primaryGreenLight: '#04995a',
      accentBlue: '#2FC6F6',
      darkBg: '#0f172a',
      textPrimary: '#1e293b',
      textSecondary: '#64748b',
      borderColor: '#e2e8f0',
      palette: [
        '#038047', '#2FC6F6', '#8b5cf6', '#f59e0b', '#ef4444',
        '#10b981', '#3b82f6', '#ec4899', '#6366f1', '#14b8a6'
      ]
    };
  </script>

  @yield('scripts')

</body>

</html>