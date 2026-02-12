<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'SMADIMENT - Analytics Platform')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
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
      overflow: hidden;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-sub .nav-item {
      font-size: 13px;
      padding: 8px 12px;
      gap: 10px;
      opacity: 0.9;
    }

    .nav-sub .nav-item:hover {
      opacity: 1;
    }

    .nav-sub .nav-item::before {
      display: none;
    }

    .nav-sub .nav-item.active {
      background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
      color: #ffffff;
      font-weight: 700;
      box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }
    
    .nav-sub .nav-item.active::before {
      content: '';
      position: absolute;
      left: -14px;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 70%;
      background: var(--primary-green);
      border-radius: 0 3px 3px 0;
    }

    /* Dropdown Specific Styles */
    .dropdown-trigger {
      cursor: pointer;
      position: relative;
      justify-content: space-between;
    }

    .dropdown-arrow {
      font-size: 10px;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      color: var(--text-muted);
      margin-left: auto;
    }

    .dropdown-trigger:hover .dropdown-arrow {
      color: var(--text-primary);
    }

    .dropdown-trigger.active .dropdown-arrow {
      color: #ffffff;
    }

    .dropdown-trigger.expanded {
      background: var(--sidebar-hover);
    }

    .dropdown-trigger.expanded .dropdown-arrow {
      transform: rotate(180deg);
      color: var(--primary-green);
    }

    /* 🔥 HIGHLIGHT WHEN PROJECT IS SELECTED */
    .dropdown-trigger.has-active-project {
      background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
      color: #ffffff;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(3, 128, 71, 0.15);
    }

    .dropdown-trigger.has-active-project .dropdown-arrow {
      color: #ffffff;
    }

    .dropdown-trigger.has-active-project:hover {
      background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
    }

    /* 🔥 HIGHLIGHT WHEN ANY CHILD IS ACTIVE */
    .dropdown-trigger.has-active-child {
      background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
      color: #ffffff;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(3, 128, 71, 0.15);
    }

    .dropdown-trigger.has-active-child .dropdown-arrow {
      color: #ffffff;
      transform: rotate(180deg);
    }

    .dropdown-trigger.has-active-child:hover {
      background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green) 100%);
    }

    /* Smooth Dropdown Animation */
    .nav-sub[style*="display: block"] {
      animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        max-height: 0;
        margin-top: 0;
      }
      to {
        opacity: 1;
        max-height: 500px;
        margin-top: 4px;
      }
    }

    /* Project Select Special Styling */
    .project-select-trigger {
      border: 1px solid var(--border-color);
      background: linear-gradient(135deg, #ffffff 0%, var(--sidebar-hover) 100%);
      font-weight: 600;
    }

    .project-select-trigger:hover {
      border-color: var(--primary-green);
      box-shadow: 0 2px 8px rgba(3, 128, 71, 0.1);
    }

    .project-select-trigger.expanded {
      border-color: var(--primary-green);
      background: var(--sidebar-hover);
    }

    /* Current Project Badge */
    .current-project-name {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
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
      font-family: 'Poppins', sans-serif;
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
       RESPONSIVE
       ======================================================== */
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
      <!-- PROJECT SELECT -->
      <div class="nav-section">
        <div class="nav-label">Project</div>
        @php
          $currentProjectId = request()->query('project_id');
          $hasProjects = isset($projects) && count($projects) > 0;
          
          if (!$currentProjectId && $hasProjects) {
              $currentProjectId = $projects[0]['id'] ?? null;
          }
          
          $currentProjectName = 'Select Project';
          if ($currentProjectId && $hasProjects) {
              $currentProject = collect($projects)->firstWhere('id', $currentProjectId);
              if ($currentProject) {
                  $currentProjectName = $currentProject['name'] ?? 
                                       $currentProject['project_name'] ?? 
                                       $currentProject['title'] ?? 
                                       $currentProject['label'] ?? 
                                       'Project #' . $currentProject['id'];
              }
          }
          
          $hasActiveProject = !empty($currentProjectId);
        @endphp
        
        <div class="nav-item dropdown-trigger project-select-trigger {{ $hasActiveProject ? 'has-active-project' : '' }}" 
             onclick="toggleDropdown('projectDropdown', this)"
             id="projectTrigger">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span class="current-project-name">{{ $currentProjectName }}</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        
        <div id="projectDropdown" class="nav-sub" style="display: none;">
          @if($hasProjects)
            @foreach($projects as $project)
              @php
                $pName = $project['name'] ?? 
                         $project['project_name'] ?? 
                         $project['title'] ?? 
                         $project['label'] ?? 
                         'Project #' . ($project['id'] ?? '');
                $pId = $project['id'] ?? '';
                $isActive = $currentProjectId == $pId;
              @endphp
              <a href="javascript:void(0)" 
                 class="nav-item {{ $isActive ? 'active' : '' }}"
                 onclick="changeProject({{ $pId }}, '{{ addslashes($pName) }}')">
                <span>{{ $pName }}</span>
              </a>
            @endforeach
          @else
            <a href="#" class="nav-item">
              <span>No Projects Available</span>
            </a>
          @endif
        </div>
      </div>

      <!-- MAIN NAVIGATION -->
      <div class="nav-section">
        <div class="nav-label">Main</div>

        <a href="{{ route('mk.dashboard') }}" 
           class="nav-item {{ request()->routeIs('mk.dashboard') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <rect x="3" y="3" width="7" height="7"/>
              <rect x="14" y="3" width="7" height="7"/>
              <rect x="14" y="14" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/>
            </svg>
          </span>
          <span>Dashboard</span>
        </a>

        <a href="{{ route('mk.data-overview') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}" 
           class="nav-item {{ request()->routeIs('mk.data-overview') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <line x1="18" y1="20" x2="18" y2="10"/>
              <line x1="12" y1="20" x2="12" y2="4"/>
              <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
          </span>
          <span>Data Overview</span>
        </a>

        <!-- 🔥 ANALYTICS OVERVIEW - menggantikan Topic Map & Top Analytics -->
        <a href="{{ route('mk.analytics-overview') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}" 
           class="nav-item {{ request()->routeIs('mk.analytics-overview') ? 'active' : '' }}">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="3"/>
              <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" />
            </svg>
          </span>
          <span>Analytics Overview</span>
        </a>
      </div>

      <!-- DATA SOURCE DROPDOWN -->
      @php
        $dataSourceRoutes = ['mk.data-source.users', 'mk.data-source.authors', 'mk.data-source.volume', 'mk.data-source.trends'];
        $isDataSourceActive = request()->routeIs($dataSourceRoutes);
      @endphp
      
      <div class="nav-section">
        <div class="nav-label">Data Source</div>
        
        <div class="nav-item dropdown-trigger {{ $isDataSourceActive ? 'has-active-child' : '' }}" 
             onclick="toggleDropdown('dataSourceDropdown', this)"
             id="dataSourceTrigger">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <rect x="2" y="2" width="20" height="8" rx="2" ry="2"/>
              <rect x="2" y="14" width="20" height="8" rx="2" ry="2"/>
              <line x1="6" y1="6" x2="6.01" y2="6"/>
              <line x1="6" y1="18" x2="6.01" y2="18"/>
            </svg>
          </span>
          <span>Data Source</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        
        <div id="dataSourceDropdown" class="nav-sub" style="display: {{ $isDataSourceActive ? 'block' : 'none' }};">
          <a href="{{ route('mk.data-source.users') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}" 
             class="nav-item {{ request()->routeIs('mk.data-source.users') ? 'active' : '' }}">
            <span>Total Users</span>
          </a>
          <a href="{{ route('mk.data-source.authors') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}" 
             class="nav-item {{ request()->routeIs('mk.data-source.authors') ? 'active' : '' }}">
            <span>Total Authors</span>
          </a>
          <a href="{{ route('mk.data-source.volume') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}" 
             class="nav-item {{ request()->routeIs('mk.data-source.volume') ? 'active' : '' }}">
            <span>Volume Total</span>
          </a>
          <a href="{{ route('mk.data-source.trends') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}" 
             class="nav-item {{ request()->routeIs('mk.data-source.trends') ? 'active' : '' }}">
            <span>Trends Total</span>
          </a>
        </div>
      </div>

      <!-- NEWS SECTION -->
      <div class="nav-section">
        <div class="nav-label">News</div>
        <div class="nav-item dropdown-trigger" onclick="toggleDropdown('newsDropdown', this)">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
          </span>
          <span>News</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        <div id="newsDropdown" class="nav-sub" style="display: none;">
          <a href="#" class="nav-item">
            <span>Trending News</span>
          </a>
        </div>
      </div>

      <!-- SOCIAL MEDIA SECTION -->
      <div class="nav-section">
        <div class="nav-label">Social Media</div>
        
        <!-- X (Twitter) -->
        <div class="nav-item dropdown-trigger" onclick="toggleDropdown('xDropdown', this)">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
          </span>
          <span>X</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        <div id="xDropdown" class="nav-sub" style="display: none;">
          <a href="#" class="nav-item">
            <span>X Analytics</span>
          </a>
        </div>

        <!-- Facebook -->
        <div class="nav-item dropdown-trigger" onclick="toggleDropdown('facebookDropdown', this)">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
            </svg>
          </span>
          <span>Facebook</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        <div id="facebookDropdown" class="nav-sub" style="display: none;">
          <a href="#" class="nav-item">
            <span>Facebook Analytics</span>
          </a>
        </div>

        <!-- Instagram -->
        <div class="nav-item dropdown-trigger" onclick="toggleDropdown('instagramDropdown', this)">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
            </svg>
          </span>
          <span>Instagram</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        <div id="instagramDropdown" class="nav-sub" style="display: none;">
          <a href="#" class="nav-item">
            <span>Instagram Analytics</span>
          </a>
        </div>

        <!-- YouTube -->
        <div class="nav-item dropdown-trigger" onclick="toggleDropdown('youtubeDropdown', this)">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/>
              <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/>
            </svg>
          </span>
          <span>Youtube</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        <div id="youtubeDropdown" class="nav-sub" style="display: none;">
          <a href="#" class="nav-item">
            <span>Youtube Analytics</span>
          </a>
        </div>

        <!-- TikTok -->
        <div class="nav-item dropdown-trigger" onclick="toggleDropdown('tiktokDropdown', this)">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
            </svg>
          </span>
          <span>Tiktok</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        <div id="tiktokDropdown" class="nav-sub" style="display: none;">
          <a href="#" class="nav-item">
            <span>Tiktok Analytics</span>
          </a>
        </div>
      </div>

      <!-- REPORT SECTION -->
      <div class="nav-section">
        <div class="nav-label">Report</div>
        <div class="nav-item dropdown-trigger" onclick="toggleDropdown('reportDropdown', this)">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
          </span>
          <span>Report</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        <div id="reportDropdown" class="nav-sub" style="display: none;">
          <a href="#" class="nav-item">
            <span>Generate Report</span>
          </a>
        </div>
      </div>

      <!-- AI INSIGHT SECTION -->
      <div class="nav-section">
        <div class="nav-label">AI Insight</div>
        <div class="nav-item dropdown-trigger" onclick="toggleDropdown('aiDropdown', this)">
          <span class="nav-icon">
            <svg viewBox="0 0 24 24">
              <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
              <polyline points="7.5 4.21 12 6.81 16.5 4.21"/>
              <polyline points="7.5 19.79 7.5 14.6 3 12"/>
              <polyline points="21 12 16.5 14.6 16.5 19.79"/>
              <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
              <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
          </span>
          <span>AI Insight</span>
          <span class="dropdown-arrow">▼</span>
        </div>
        <div id="aiDropdown" class="nav-sub" style="display: none;">
          <a href="#" class="nav-item">
            <span>AI Analysis</span>
          </a>
        </div>
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
    // Global color palette
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

    // Dropdown toggle
    function toggleDropdown(dropdownId, trigger) {
      const dropdown = document.getElementById(dropdownId);
      if (!dropdown) return;

      const arrow = trigger.querySelector('.dropdown-arrow');
      const isOpen = dropdown.style.display === 'block';

      if (isOpen) {
        dropdown.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
        trigger.classList.remove('expanded');
      } else {
        dropdown.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
        trigger.classList.add('expanded');
      }
    }

    // Change project
    function changeProject(projectId, projectName) {
      console.log('Changing project to:', projectId, projectName);
      const url = new URL(window.location.href);
      url.searchParams.set('project_id', projectId);
      window.location.href = url.toString();
    }

    // Auto-select first project if needed
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const currentProjectId = urlParams.get('project_id');
      const currentPath = window.location.pathname;
      
      const needsProject = [
        '/mk/data-overview',
        '/mk/analytics-overview',
        '/mk/data-source'
      ];
      
      const requiresProject = needsProject.some(path => currentPath.includes(path));
      
      if (requiresProject && !currentProjectId) {
        const projectItems = document.querySelectorAll('#projectDropdown .nav-item');
        
        if (projectItems.length === 1) {
          const firstProject = projectItems[0];
          if (firstProject.textContent.trim() !== 'No Projects Available') {
            firstProject.click();
          }
        } else if (projectItems.length > 1) {
          const projectTrigger = document.getElementById('projectTrigger');
          if (projectTrigger) {
            setTimeout(() => {
              toggleDropdown('projectDropdown', projectTrigger);
            }, 300);
          }
        }
      }
    });
  </script>

  @yield('scripts')

</body>

</html>