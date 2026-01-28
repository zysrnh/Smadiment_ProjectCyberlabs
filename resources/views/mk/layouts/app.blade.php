<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'SMADIMENT - Analytics Platform')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    :root {
      --dark-teal: #192338;
      --brown: #31487A;
      --sage: #8FB3E2;
      --beige: #D9E1F1;
      --cream: #F5F7FA;
      --white: #ffffff;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background: var(--cream);
      color: var(--dark-teal);
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
      border-right: 3px solid var(--beige);
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
      color: var(--brown);
      letter-spacing: -1px;
      margin-bottom: 8px;
    }

    .logo p {
      font-size: 13px;
      color: var(--sage);
      font-weight: 600;
    }

    .nav-section {
      margin-bottom: 32px;
    }

    .nav-label {
      font-size: 11px;
      font-weight: 800;
      color: var(--sage);
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
      color: var(--dark-teal);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all 0.2s;
    }

    .nav-item:hover {
      background: var(--beige);
      color: var(--brown);
      transform: translateX(4px);
    }

    .nav-item.active {
      background: var(--brown);
      color: var(--white);
    }

    .nav-icon {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 11px;
      background: var(--beige);
      border-radius: 8px;
      color: var(--brown);
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
      color: var(--dark-teal);
      margin-bottom: 4px;
    }

    .page-subtitle {
      font-size: 14px;
      color: var(--sage);
      font-weight: 600;
    }

    .top-actions {
      display: flex;
      gap: 12px;
    }

    .action-btn {
      padding: 12px 24px;
      border-radius: 12px;
      border: 2px solid var(--beige);
      background: var(--white);
      color: var(--dark-teal);
      font-family: 'Montserrat', sans-serif;
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
      box-shadow: 0 4px 12px rgba(82, 61, 53, 0.15);
    }

    .action-btn.primary {
      background: var(--brown);
      color: var(--white);
      border-color: var(--brown);
    }

    /* Section */
    .section {
      background: var(--white);
      border-radius: 20px;
      border: 2px solid var(--beige);
      overflow: hidden;
      margin-bottom: 24px;
    }

    .section-header {
      padding: 24px 28px;
      background: linear-gradient(135deg, var(--beige) 0%, var(--sage) 100%);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .section-title {
      font-size: 20px;
      font-weight: 800;
      color: var(--brown);
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
      border: 2px solid var(--beige);
      background: var(--white);
      color: var(--dark-teal);
      font-family: 'Montserrat', sans-serif;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .mode-btn:hover {
      background: var(--beige);
    }

    .mode-btn.active {
      background: var(--brown);
      color: var(--white);
      border-color: var(--brown);
    }

    /* Stats Cards */
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 16px;
      margin-bottom: 28px;
    }

    .stat-card {
      background: linear-gradient(135deg, var(--cream) 0%, var(--white) 100%);
      border: 2px solid var(--beige);
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
      color: var(--sage);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .stat-percentage {
      font-size: 16px;
      font-weight: 900;
      color: var(--dark-teal);
    }

    .stat-number {
      font-size: 32px;
      font-weight: 900;
      color: var(--dark-teal);
      margin-bottom: 12px;
    }

    .progress-bar {
      height: 10px;
      background: var(--beige);
      border-radius: 10px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      border-radius: 10px;
      transition: width 0.5s ease;
    }

    .progress-positive {
      background: linear-gradient(90deg, var(--sage), #6b7563);
    }

    .progress-neutral {
      background: linear-gradient(90deg, #A8C5E8, #9a8676);
    }

    .progress-negative {
      background: linear-gradient(90deg, var(--brown), #3d2d27);
    }

    /* Table */
    .data-table-wrapper {
      border: 2px solid var(--beige);
      border-radius: 12px;
      overflow: hidden;
    }

    .data-table {
      width: 100%;
      border-collapse: collapse;
    }

    .data-table thead {
      background: var(--beige);
    }

    .data-table th {
      padding: 14px 16px;
      text-align: left;
      font-size: 11px;
      font-weight: 800;
      color: var(--brown);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .data-table td {
      padding: 14px 16px;
      font-size: 14px;
      font-weight: 600;
      color: var(--dark-teal);
      border-bottom: 1px solid var(--beige);
    }

    .data-table tr:last-child td {
      border-bottom: none;
    }

    .data-table tr:hover {
      background: var(--cream);
    }

    .count-cell {
      text-align: right;
      font-weight: 800;
      color: var(--brown);
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
      color: var(--sage);
    }

    .empty-text {
      font-size: 15px;
      font-weight: 600;
      color: var(--sage);
    }

    /* Debug Section */
    .debug-toggle {
      font-size: 13px;
      font-weight: 700;
      color: var(--brown);
      cursor: pointer;
      padding: 12px 16px;
      background: var(--beige);
      border-radius: 10px;
      display: inline-block;
      margin-bottom: 16px;
      user-select: none;
    }

    .debug-toggle:hover {
      background: var(--sage);
      color: var(--white);
    }

    .debug-content {
      background: var(--dark-teal);
      color: var(--cream);
      padding: 20px;
      border-radius: 12px;
      overflow: auto;
      max-height: 300px;
      font-family: 'Courier New', monospace;
      font-size: 12px;
      line-height: 1.6;
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

  @include('mk.components.sidebar')

  <!-- Main Content -->
  <div class="main-content">
    @yield('content')
  </div>

  <script>
    // Global color palette
    const colors = {
      brown: '#31487A',
      sage: '#8FB3E2',
      beige: '#D9E1F1',
      darkTeal: '#192338',
      cream: '#F5F7FA',
      palette: [
        '#31487A', '#8FB3E2', '#D9E1F1', '#192338', '#5A6F9E',
        '#A8C5E8', '#6B8BC3', '#4A5E8C', '#7A94C7', '#9BB5DD'
      ]
    };
  </script>

  @yield('scripts')

</body>

</html>