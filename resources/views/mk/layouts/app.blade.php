<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'SMADIMENT - Analytics Platform')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    :root {
      --primary-green: #038047;
      --dark-blue: #273B4A;
      --white: #FFFFFF;
      --light-gray: #F1F5F8;
      --text-dark: #273B4A;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #273B4A;
      color: var(--text-dark);
      line-height: 1.6;
      min-height: 100vh;
    }

    /* Sidebar Navigation */
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 280px;
      height: 100vh;
      background: var(--white);
      border-right: 3px solid var(--light-gray);
      padding: 32px 24px;
      z-index: 100;
      overflow-y: auto;
    }

    .logo {
      margin-bottom: 48px;
    }

    .logo h1 {
      font-size: 32px;
      font-weight: 900;
      color: var(--primary-green);
      letter-spacing: -1px;
      margin-bottom: 8px;
    }

    .logo p {
      font-size: 13px;
      color: var(--dark-blue);
      font-weight: 600;
    }

    .nav-section {
      margin-bottom: 32px;
    }

    .nav-label {
      font-size: 11px;
      font-weight: 800;
      color: var(--dark-blue);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 12px;
      padding-left: 12px;
    }

    .nav-item {
      padding: 14px 16px;
      margin-bottom: 6px;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 600;
      color: var(--text-dark);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all 0.2s;
    }

    .nav-item:hover {
      background: var(--light-gray);
      color: var(--primary-green);
      transform: translateX(4px);
    }

    .nav-item.active {
      background: var(--primary-green);
      color: var(--white);
    }

    .nav-icon {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--light-gray);
      border-radius: 8px;
      color: var(--primary-green);
    }

    .nav-icon svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    .nav-item.active .nav-icon {
      background: rgba(255, 255, 255, 0.2);
      color: var(--white);
    }

    /* Sub Navigation */
    .nav-sub {
      margin-left: 44px;
      margin-top: 6px;
    }

    .nav-sub .nav-item {
      font-size: 13px;
      padding: 10px 12px;
    }

    /* Main Content */
    .main-content {
      margin-left: 280px;
      padding: 32px;
      min-height: 100vh;
    }

    /* Top Bar */
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;
      gap: 24px;
    }

    .page-title {
      flex: 1;
    }

    .page-title h2 {
      font-size: 28px;
      font-weight: 800;
      color: var(--white);
      margin-bottom: 4px;
    }

    .page-subtitle {
      font-size: 14px;
      color: var(--light-gray);
      font-weight: 600;
    }

    .top-actions {
      display: flex;
      gap: 12px;
    }

    .action-btn {
      padding: 12px 24px;
      border-radius: 12px;
      border: 2px solid var(--light-gray);
      background: var(--white);
      color: var(--text-dark);
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 102, 204, 0.15);
    }

    .action-btn.primary {
      background: var(--primary-green);
      color: var(--white);
      border-color: var(--primary-green);
    }

    /* Section */
    .section {
      background: var(--white);
      border-radius: 20px;
      border: 2px solid var(--light-gray);
      overflow: hidden;
      margin-bottom: 24px;
    }

    .section-header {
      padding: 24px 28px;
      background: linear-gradient(135deg, var(--light-gray) 0%, #E0E8EF 100%);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .section-title {
      font-size: 20px;
      font-weight: 800;
      color: var(--dark-blue);
    }

    .item-count {
      background: var(--primary-green);
      color: var(--white);
      padding: 6px 14px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 800;
    }

    .section-body {
      padding: 28px;
    }

    /* Chart Container */
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

    /* Mode Toggle */
    .mode-toggle {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
    }

    .mode-btn {
      padding: 8px 16px;
      border: 2px solid var(--light-gray);
      background: var(--white);
      color: var(--text-dark);
      font-family: 'Poppins', sans-serif;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .mode-btn:hover {
      background: var(--light-gray);
    }

    .mode-btn.active {
      background: var(--primary-green);
      color: var(--white);
      border-color: var(--primary-green);
    }

    /* Stats Cards */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 16px;
      margin-bottom: 28px;
    }

    .stat-card {
      background: linear-gradient(135deg, var(--white) 0%, #FAFBFC 100%);
      border: 2px solid var(--light-gray);
      border-radius: 16px;
      padding: 20px;
    }

    .stat-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .stat-name {
      font-size: 12px;
      font-weight: 800;
      color: var(--dark-blue);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .stat-percentage {
      font-size: 16px;
      font-weight: 900;
      color: var(--text-dark);
    }

    .stat-number {
      font-size: 32px;
      font-weight: 900;
      color: var(--text-dark);
      margin-bottom: 12px;
    }

    .progress-bar {
      height: 10px;
      background: var(--light-gray);
      border-radius: 10px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      border-radius: 10px;
      transition: width 0.5s ease;
    }

    .progress-positive {
      background: linear-gradient(90deg, var(--primary-green), #025a34);
    }

    .progress-neutral {
      background: linear-gradient(90deg, #5A9FD4, #4a7fa2);
    }

    .progress-negative {
      background: linear-gradient(90deg, var(--dark-blue), #1a2935);
    }

    /* Table */
    .data-table-wrapper {
      border: 2px solid var(--light-gray);
      border-radius: 12px;
      overflow: hidden;
    }

    .data-table {
      width: 100%;
      border-collapse: collapse;
    }

    .data-table thead {
      background: var(--light-gray);
    }

    .data-table th {
      padding: 14px 16px;
      text-align: left;
      font-size: 11px;
      font-weight: 800;
      color: var(--dark-blue);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .data-table td {
      padding: 14px 16px;
      font-size: 14px;
      font-weight: 600;
      color: var(--text-dark);
      border-bottom: 1px solid var(--light-gray);
    }

    .data-table tr:last-child td {
      border-bottom: none;
    }

    .data-table tr:hover {
      background: #FAFBFC;
    }

    .count-cell {
      text-align: right;
      font-weight: 800;
      color: var(--primary-green);
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 48px 24px;
    }

    .empty-icon {
      font-size: 56px;
      margin-bottom: 16px;
      opacity: 0.3;
      font-weight: 800;
      color: var(--dark-blue);
    }

    .empty-text {
      font-size: 15px;
      font-weight: 600;
      color: var(--dark-blue);
    }

    /* Debug Section */
    .debug-toggle {
      font-size: 13px;
      font-weight: 700;
      color: var(--primary-green);
      cursor: pointer;
      padding: 12px 16px;
      background: var(--light-gray);
      border-radius: 10px;
      display: inline-block;
      margin-bottom: 16px;
      user-select: none;
    }

    .debug-toggle:hover {
      background: var(--primary-green);
      color: var(--white);
    }

    .debug-content {
      background: var(--dark-blue);
      color: var(--light-gray);
      padding: 20px;
      border-radius: 12px;
      overflow: auto;
      max-height: 300px;
      font-family: 'Courier New', monospace;
      font-size: 12px;
      line-height: 1.6;
    }

    /* Topic Card (for Recent Topics page) */
    .topic-card {
      transition: all 0.3s;
    }

    .topic-card:hover {
      transform: translateX(8px);
      border-color: var(--primary-green);
      box-shadow: -8px 8px 24px rgba(3, 128, 71, 0.12);
    }

    .topic-card a {
      transition: all 0.2s;
    }

    .topic-card a:hover {
      color: var(--primary-green) !important;
    }

    /* Responsive */
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
        padding: 20px;
      }

      .top-bar {
        flex-direction: column;
        align-items: flex-start;
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

  <!-- Sidebar Navigation -->
  <div class="sidebar">
    <div class="logo">
      <h1>SMADIMENT</h1>
      <p>Social Media Analytics</p>
    </div>

    <div class="nav-section">
      <div class="nav-label">Main</div>
      <a href="{{ route('mk.dashboard') }}" class="nav-item {{ request()->routeIs('mk.dashboard') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </span>
        <span>Dashboard</span>
      </a>
      <a href="{{ route('mk.projects') }}" class="nav-item {{ request()->routeIs('mk.projects') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        </span>
        <span>Projects</span>
      </a>
    </div>

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

    <div class="nav-section">
      <div class="nav-label">Tools</div>
      <a href="#" class="nav-item">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v6m0 6v6m-9-9h6m6 0h6"/><path d="M4.2 4.2l4.3 4.3m5 5l4.3 4.3m0-12.8l-4.3 4.3m-5 5l-4.3 4.3"/></svg>
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

  <!-- Main Content -->
  <div class="main-content">
    @yield('content')
  </div>

  <script>
    // Global color palette
    const colors = {
      primaryGreen: '#038047',
      darkBlue: '#273B4A',
      white: '#FFFFFF',
      lightGray: '#F1F5F8',
      palette: [
        '#038047', '#273B4A', '#FFFFFF', '#F1F5F8', '#1a5a3a',
        '#3d5566', '#027037', '#1f4e5f', '#09b868', '#35576a'
      ]
    };
  </script>

  @yield('scripts')

</body>

</html>