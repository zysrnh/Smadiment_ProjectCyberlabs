<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'SMADIMENT - Admin Dashboard')</title>

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
      display: flex;
      flex-direction: column;
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
      flex: 1;
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

    /* User Profile Section */
    .user-profile {
      padding: 20px;
      background: var(--light-gray);
      border-radius: 12px;
      margin-bottom: 16px;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .user-avatar {
      width: 48px;
      height: 48px;
      background: var(--primary-green);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      font-weight: 800;
      color: var(--white);
    }

    .user-details h4 {
      font-size: 14px;
      font-weight: 700;
      color: var(--dark-blue);
      margin-bottom: 2px;
    }

    .user-details p {
      font-size: 12px;
      color: #7A8B96;
      font-weight: 600;
    }

    /* Logout Button */
    .logout-form {
      width: 100%;
    }

    .btn-logout {
      width: 100%;
      padding: 12px;
      background: transparent;
      color: #FF6B6B;
      border: 2px solid #FF6B6B;
      border-radius: 10px;
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-logout:hover {
      background: #FF6B6B;
      color: var(--white);
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

    /* Project Cards Grid */
    .projects-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
      gap: 24px;
    }

    /* Project Card */
    .project-card {
      background: var(--white);
      border: 2px solid var(--light-gray);
      border-radius: 16px;
      overflow: hidden;
      transition: all 0.3s;
    }

    .project-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(3, 128, 71, 0.12);
      border-color: var(--primary-green);
    }

    .project-header {
      padding: 20px 24px;
      background: linear-gradient(135deg, var(--light-gray) 0%, #E0E8EF 100%);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .project-name {
      font-size: 18px;
      font-weight: 800;
      color: var(--dark-blue);
      margin: 0;
    }

    .project-badge {
      background: var(--primary-green);
      color: var(--white);
      padding: 4px 12px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .project-body {
      padding: 20px 24px;
    }

    .project-id {
      font-size: 12px;
      font-weight: 700;
      color: #7A8B96;
      margin-bottom: 16px;
    }

    /* Mini Chart in Card */
    .project-chart {
      height: 120px;
      margin-bottom: 16px;
      background: #FAFBFC;
      border-radius: 8px;
      padding: 12px;
    }

    .project-stats {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-bottom: 16px;
    }

    .stat-mini {
      text-align: center;
      padding: 12px;
      background: #FAFBFC;
      border-radius: 8px;
      border: 1px solid var(--light-gray);
    }

    .stat-mini-label {
      font-size: 10px;
      font-weight: 700;
      color: #7A8B96;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }

    .stat-mini-value {
      font-size: 18px;
      font-weight: 900;
      color: var(--dark-blue);
    }

    .project-actions {
      display: flex;
      gap: 8px;
    }

    .btn-primary-custom {
      flex: 1;
      padding: 12px;
      background: var(--primary-green);
      color: var(--white);
      border: none;
      border-radius: 10px;
      font-family: 'Poppins', sans-serif;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      text-align: center;
    }

    .btn-primary-custom:hover {
      background: #025a34;
      transform: translateY(-2px);
    }

    .btn-icon {
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--light-gray);
      color: var(--primary-green);
      border: none;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
    }

    .btn-icon:hover {
      background: var(--primary-green);
      color: var(--white);
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 80px 24px;
      background: var(--white);
      border-radius: 20px;
      border: 2px solid var(--light-gray);
    }

    .empty-icon {
      font-size: 72px;
      margin-bottom: 24px;
      opacity: 0.2;
      font-weight: 800;
      color: var(--dark-blue);
    }

    .empty-title {
      font-size: 24px;
      font-weight: 800;
      color: var(--dark-blue);
      margin-bottom: 8px;
    }

    .empty-text {
      font-size: 14px;
      font-weight: 600;
      color: #7A8B96;
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .projects-grid {
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
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

      .projects-grid {
        grid-template-columns: 1fr;
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
      <p>Admin Dashboard</p>
    </div>

    <div class="nav-section">
      <div class="nav-label">Main</div>
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </span>
        <span>Dashboard</span>
      </a>
      <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
  <span class="nav-icon">
    <svg viewBox="0 0 24 24">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
      <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
  </span>
  <span>User Management</span>
</a>
    </div>

    <!-- User Profile & Logout -->
    <div>
      <div class="user-profile">
        <div class="user-info">
          <div class="user-avatar">
            {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
          </div>
          <div class="user-details">
            <h4>{{ Auth::guard('admin')->user()->name }}</h4>
            <p>{{ Auth::guard('admin')->user()->email }}</p>
          </div>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="btn-logout">
          <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Logout
        </button>
      </form>
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