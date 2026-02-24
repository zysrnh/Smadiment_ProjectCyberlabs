<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SMADIMENT - Analytics Platform')</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    :root {
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
      --card-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px -1px rgba(0,0,0,0.1);
      --card-shadow-hover: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
      --sidebar-width: 260px;
      --transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: #f1f5f9;
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
      font-size: 14px;
    }

    /* ═══════════════════════════════════════════════════════
       SIDEBAR
    ═══════════════════════════════════════════════════════ */
    .sidebar {
      position: fixed;
      left: 0; top: 0;
      width: var(--sidebar-width);
      height: 100vh;
      background: var(--sidebar-bg);
      border-right: 1px solid var(--border-color);
      z-index: 100;
      display: flex;
      flex-direction: column;
      box-shadow: 2px 0 12px rgba(0,0,0,0.04);
      overflow: hidden;
    }

    /* ── Logo ── */
    .sidebar-logo {
      padding: 22px 20px 18px;
      border-bottom: 1px solid var(--border-color);
      background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
      flex-shrink: 0;
    }

    .sidebar-logo .logo-img {
      height: 36px;
      width: auto;
      display: block;
      margin-bottom: 4px;
    }

    .sidebar-logo p {
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 500;
      letter-spacing: 0.2px;
    }

    /* ── Scroll Area ── */
    .sidebar-scroll {
      flex: 1;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 16px 12px 24px;
      scrollbar-width: thin;
      scrollbar-color: #e2e8f0 transparent;
    }

    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }
    .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

    /* ── Section Label ── */
    .nav-label {
      font-size: 10px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 1.2px;
      margin: 16px 0 6px;
      padding: 0 10px;
    }

    .nav-label:first-child { margin-top: 0; }

    /* ── Nav Item ── */
    .nav-item {
      padding: 9px 10px;
      margin-bottom: 1px;
      border-radius: 9px;
      font-size: 13.5px;
      font-weight: 500;
      color: var(--text-secondary);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: var(--transition);
      position: relative;
      cursor: pointer;
      user-select: none;
      white-space: nowrap;
    }

    .nav-item:hover {
      background: var(--sidebar-hover);
      color: var(--text-primary);
    }

    .nav-item.active {
      background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
      color: #fff;
      font-weight: 600;
      box-shadow: 0 3px 10px rgba(3,128,71,0.18);
    }

    .nav-item.active::after {
      content: '';
      position: absolute;
      left: 0; top: 50%;
      transform: translateY(-50%);
      width: 3px; height: 55%;
      background: rgba(255,255,255,0.6);
      border-radius: 0 3px 3px 0;
    }

    /* ── Nav Icon ── */
    .nav-icon {
      width: 32px; height: 32px;
      display: flex; align-items: center; justify-content: center;
      border-radius: 8px;
      flex-shrink: 0;
      transition: var(--transition);
    }

    .nav-icon svg {
      width: 18px; height: 18px;
      stroke: currentColor; fill: none;
      stroke-width: 1.8;
      stroke-linecap: round; stroke-linejoin: round;
    }

    .nav-item:hover .nav-icon { background: rgba(3,128,71,0.07); }
    .nav-item.active .nav-icon { background: rgba(255,255,255,0.15); }

    /* ── Menu Icon (sub items) ── */
    .menu-icon {
      width: 18px; height: 18px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; opacity: 0.65;
      transition: opacity 0.2s;
    }

    .menu-icon svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; }
    .nav-item:hover .menu-icon,
    .nav-item.active .menu-icon { opacity: 1; }

    /* ── Dropdown Trigger ── */
    .dropdown-trigger { justify-content: flex-start; }

    .dropdown-arrow {
      margin-left: auto;
      flex-shrink: 0;
      width: 16px; height: 16px;
      display: flex; align-items: center; justify-content: center;
      transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      opacity: 0.5;
    }

    .dropdown-arrow svg {
      width: 14px; height: 14px;
      stroke: currentColor; fill: none; stroke-width: 2.5;
      stroke-linecap: round; stroke-linejoin: round;
    }

    .nav-item:hover .dropdown-arrow { opacity: 0.8; }
    .nav-item.active .dropdown-arrow { opacity: 1; }
    .nav-item.open .dropdown-arrow { transform: rotate(180deg); opacity: 1; }

    /* Active parent states */
    .dropdown-trigger.has-active-project,
    .dropdown-trigger.has-active-child {
      background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
      color: #fff;
      font-weight: 600;
      box-shadow: 0 3px 10px rgba(3,128,71,0.18);
    }

    .dropdown-trigger.has-active-project .dropdown-arrow,
    .dropdown-trigger.has-active-child .dropdown-arrow { opacity: 1; }

    .dropdown-trigger.has-active-project:hover,
    .dropdown-trigger.has-active-child:hover {
      background: linear-gradient(135deg, var(--primary-green-dark) 0%, var(--primary-green-light) 100%);
    }

    /* ── Project Select ── */
    .project-select-trigger {
      border: 1px solid var(--border-color);
      background: linear-gradient(135deg, #fff 0%, var(--sidebar-hover) 100%);
      font-weight: 600;
    }

    .project-select-trigger:hover {
      border-color: var(--primary-green);
      box-shadow: 0 0 0 3px rgba(3,128,71,0.08);
    }

    .project-select-trigger.has-active-project { border-color: transparent; }

    .current-project-name {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: 13px;
    }

    /* ── SUB NAV — Animated Accordion ── */
    .nav-sub-wrapper {
      overflow: hidden;
      max-height: 0;
      transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                  opacity 0.25s ease,
                  margin 0.25s ease;
      opacity: 0;
      margin-top: 0;
    }

    .nav-sub-wrapper.open {
      max-height: 2000px;
      opacity: 1;
      margin-top: 2px;
      margin-bottom: 4px;
    }

    .nav-sub {
      margin-left: 14px;
      padding-left: 12px;
      border-left: 2px solid #e8f5ef;
    }

    .nav-sub .nav-item {
      font-size: 13px;
      padding: 8px 10px;
      border-radius: 8px;
    }

    .nav-sub .nav-item::after { display: none; }
    .nav-sub .nav-item.active::after { display: none; }

    .nav-sub .nav-item.active {
      background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
      color: #fff;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(3,128,71,0.2);
    }

    /* ═══════════════════════════════════════════════════════
       MAIN CONTENT
    ═══════════════════════════════════════════════════════ */
    .main-content {
      margin-left: var(--sidebar-width);
      min-height: 100vh;
      background: #f1f5f9;
    }

    /* Top Bar */
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 28px;
      background: #fff;
      border-bottom: 1px solid var(--border-color);
      margin-bottom: 24px;
      gap: 20px;
      box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }

    .page-title h2 {
      font-size: 24px;
      font-weight: 700;
      color: var(--text-primary);
      letter-spacing: -0.4px;
    }

    .page-subtitle {
      font-size: 13px;
      color: var(--text-secondary);
      font-weight: 500;
      margin-top: 2px;
    }

    .top-actions { display: flex; gap: 10px; }

    /* Action Buttons */
    .action-btn {
      padding: 9px 18px;
      border-radius: 9px;
      border: 1px solid var(--border-color);
      background: #fff;
      color: var(--text-primary);
      font-family: 'Poppins', sans-serif;
      font-size: 13.5px;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: var(--card-shadow);
    }

    .action-btn:hover {
      transform: translateY(-1px);
      box-shadow: var(--card-shadow-hover);
      border-color: var(--primary-green);
      color: var(--primary-green);
    }

    .action-btn.primary {
      background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
      color: #fff;
      border-color: transparent;
    }

    .action-btn.primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 20px -4px rgba(3,128,71,0.3);
    }

    /* Content Wrapper */
    .content-wrapper { padding: 0 28px 28px; }

    /* ═══════════════════════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════════════════════ */
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); }
      .main-content { margin-left: 0; }
      .top-bar { padding: 16px; flex-direction: column; align-items: flex-start; }
      .content-wrapper { padding: 0 16px 16px; }
      .top-actions { width: 100%; }
      .action-btn { flex: 1; justify-content: center; }
    }

    @yield('styles')
  </style>
</head>

<body>

  <!-- ============================================================
       SIDEBAR
  ============================================================ -->
  <div class="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
      <img src="{{ asset('images/SMADIMENT 2025 _ Logo-03.png') }}" alt="SMADIMENT" class="logo-img">
      <p>Social Media Analytics Platform</p>
    </div>

    <!-- Scrollable Nav -->
    <div class="sidebar-scroll">

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
            $currentProjectName = $currentProject['name']
              ?? $currentProject['project_name']
              ?? $currentProject['title']
              ?? $currentProject['label']
              ?? 'Project #' . $currentProject['id'];
          }
        }
        $hasActiveProject = !empty($currentProjectId);
      @endphp

      <!-- PROJECT -->
      <div class="nav-label">Project</div>

      <div class="nav-item dropdown-trigger project-select-trigger {{ $hasActiveProject ? 'has-active-project' : '' }}"
           id="projectTrigger"
           onclick="toggleNav('projectSub', this)">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
        </span>
        <span class="current-project-name">{{ $currentProjectName }}</span>
        <span class="dropdown-arrow">
          <svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </span>
      </div>

      <div class="nav-sub-wrapper" id="projectSub">
        <div class="nav-sub">
          @if($hasProjects)
            @foreach($projects as $project)
              @php
                $pName = $project['name'] ?? $project['project_name'] ?? $project['title'] ?? $project['label'] ?? 'Project #' . ($project['id'] ?? '');
                $pId   = $project['id'] ?? '';
                $isActive = $currentProjectId == $pId;
              @endphp
              <a href="javascript:void(0)"
                 class="nav-item {{ $isActive ? 'active' : '' }}"
                 onclick="changeProject({{ $pId }}, '{{ addslashes($pName) }}')">
                <span>{{ $pName }}</span>
              </a>
            @endforeach
          @else
            <a href="#" class="nav-item"><span>No Projects Available</span></a>
          @endif
        </div>
      </div>

      <!-- MAIN -->
      <div class="nav-label">Main</div>

      <a href="{{ route('mk.dashboard') }}"
         class="nav-item {{ request()->routeIs('mk.dashboard') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        </span>
        <span>Dashboard</span>
      </a>

      <a href="{{ route('mk.data-overview') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
         class="nav-item {{ request()->routeIs('mk.data-overview') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        </span>
        <span>Data Overview</span>
      </a>

      {{-- ✅ Compare Projects --}}
      <a href="{{ route('mk.compare.index') }}"
         class="nav-item {{ request()->routeIs('mk.compare.index') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="2" y="3" width="6" height="18"/><rect x="10" y="8" width="6" height="13"/><rect x="18" y="5" width="4" height="16"/>
          </svg>
        </span>
        <span>Compare Projects</span>
      </a>

      <a href="{{ route('mk.topic-map') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
         class="nav-item {{ request()->routeIs('mk.topic-map') ? 'active' : '' }}">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="19" r="2"/><circle cx="5" cy="5" r="2"/><line x1="12" y1="9" x2="6.5" y2="6.5"/><line x1="12" y1="15" x2="6.5" y2="17.5"/><line x1="15" y1="12" x2="17" y2="6.5"/><line x1="15" y1="12" x2="17" y2="17.5"/></svg>
        </span>
        <span>World Map</span>
      </a>

      @php
        $dataSourceRoutes  = ['mk.data-source.users','mk.data-source.authors','mk.data-source.volume','mk.data-source.trends'];
        $isDataSourceActive = request()->routeIs($dataSourceRoutes);
      @endphp

      <div class="nav-item dropdown-trigger {{ $isDataSourceActive ? 'has-active-child open' : '' }}"
           onclick="toggleNav('dataSourceSub', this)">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
        </span>
        <span>Data Source</span>
        <span class="dropdown-arrow"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
      </div>

      <div class="nav-sub-wrapper {{ $isDataSourceActive ? 'open' : '' }}" id="dataSourceSub">
        <div class="nav-sub">
          <a href="{{ route('mk.data-source.users') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.data-source.users') ? 'active' : '' }}"><span>Total Users</span></a>
          <a href="{{ route('mk.data-source.authors') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.data-source.authors') ? 'active' : '' }}"><span>Total Authors</span></a>
          <a href="{{ route('mk.data-source.volume') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.data-source.volume') ? 'active' : '' }}"><span>Volume Total</span></a>
          <a href="{{ route('mk.data-source.trends') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.data-source.trends') ? 'active' : '' }}"><span>Trends Total</span></a>
        </div>
      </div>

      <!-- NEWS -->
      <div class="nav-label">News</div>

      @php
        $newsRoutes = ['mk.news.word-cloud','mk.news.top-publishers','mk.news.timeline','mk.news.articles','mk.news.ai-analysis','mk.news.topic-map'];
        $isNewsActive = request()->routeIs($newsRoutes);
      @endphp

      <div class="nav-item dropdown-trigger {{ $isNewsActive ? 'has-active-child open' : '' }}"
           onclick="toggleNav('newsSub', this)">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        </span>
        <span>News</span>
        <span class="dropdown-arrow"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
      </div>

      <div class="nav-sub-wrapper {{ $isNewsActive ? 'open' : '' }}" id="newsSub">
        <div class="nav-sub">

          <a href="{{ route('mk.news.word-cloud') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.news.word-cloud') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
            <span>Word Cloud</span>
          </a>

          <a href="{{ route('mk.news.top-publishers') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.news.top-publishers') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="10" y1="6" x2="16" y2="6"/><line x1="10" y1="10" x2="16" y2="10"/><line x1="10" y1="14" x2="16" y2="14"/></svg></span>
            <span>Top Publishers</span>
          </a>

          <a href="{{ route('mk.news.timeline') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.news.timeline') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
            <span>Mention</span>
          </a>

          <a href="{{ route('mk.news.articles') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.news.articles') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
            <span>Articles</span>
          </a>

          <a href="{{ route('mk.news.topic-map') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.news.topic-map') ? 'active' : '' }}">
            <span class="menu-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="8" height="11"/><rect x="13" y="3" width="8" height="6"/>
                <rect x="13" y="11" width="8" height="10"/><rect x="3" y="16" width="8" height="6"/>
              </svg>
            </span>
            <span>Topic Map</span>
          </a>

        </div>
      </div>

      <!-- SOCIAL MEDIA -->
      <div class="nav-label">Social Media</div>

      @php
        $xRoutes = ['mk.x.overview','mk.x.most-status','mk.x.most-retweets','mk.x.authors.demographics','mk.x.geographic','mk.x.post-with-location','mk.x.trending-topics','mk.x.trending-word-cloud','mk.x.shared-urls','mk.x.most-active-users','mk.x.ai-analysis'];
        $isXActive = request()->routeIs($xRoutes);
      @endphp

      <div class="nav-item dropdown-trigger {{ $isXActive ? 'has-active-child open' : '' }}"
           onclick="toggleNav('xSub', this)">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </span>
        <span>X (Twitter)</span>
        <span class="dropdown-arrow"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
      </div>

      <div class="nav-sub-wrapper {{ $isXActive ? 'open' : '' }}" id="xSub">
        <div class="nav-sub">
          <a href="{{ route('mk.x.overview') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.x.overview') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
            <span>Overview</span>
          </a>
          <a href="{{ route('mk.x.trending-topics') }}"
             class="nav-item {{ request()->routeIs('mk.x.trending-topics') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
            <span>Top Hashtags</span>
          </a>
          <a href="{{ route('mk.x.most-status') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.x.most-status') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
            <span>Most Viewed Posts</span>
          </a>
          <a href="{{ route('mk.x.most-retweets') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.x.most-retweets') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg></span>
            <span>Most Retweets</span>
          </a>
          <a href="{{ route('mk.x.authors.demographics') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.x.authors.demographics') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            <span>Author Profiles</span>
          </a>
          <a href="{{ route('mk.x.geographic') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.x.geographic') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span>
            <span>Location Map</span>
          </a>
          <a href="{{ route('mk.x.post-with-location') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.x.post-with-location') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
            <span>Posts with Location</span>
          </a>
          <a href="{{ route('mk.x.trending-word-cloud') }}"
             class="nav-item {{ request()->routeIs('mk.x.trending-word-cloud') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="19" r="2"/><circle cx="5" cy="5" r="2"/><line x1="12" y1="9" x2="6.5" y2="6.5"/><line x1="12" y1="15" x2="6.5" y2="17.5"/><line x1="15" y1="12" x2="17" y2="6.5"/><line x1="15" y1="12" x2="17" y2="17.5"/></svg></span>
            <span>Word Cloud</span>
          </a>
          <a href="{{ route('mk.x.shared-urls') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.x.shared-urls') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></span>
            <span>Shared URLs</span>
          </a>
          <a href="{{ route('mk.x.most-active-users') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.x.most-active-users') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
            <span>Most Active Users</span>
          </a>
        </div>
      </div>

      @php
        $facebookRoutes   = ['mk.facebook.overview','mk.facebook.trending-topics','mk.facebook.most-viewed-posts','mk.facebook.geographic','mk.facebook.trending-word-cloud'];
        $isFacebookActive = request()->routeIs($facebookRoutes);
      @endphp

      <div class="nav-item dropdown-trigger {{ $isFacebookActive ? 'has-active-child open' : '' }}"
           onclick="toggleNav('facebookSub', this)">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </span>
        <span>Facebook</span>
        <span class="dropdown-arrow"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
      </div>

      <div class="nav-sub-wrapper {{ $isFacebookActive ? 'open' : '' }}" id="facebookSub">
        <div class="nav-sub">
          <a href="{{ route('mk.facebook.overview') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.facebook.overview') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
            <span>Overview</span>
          </a>
          <a href="{{ route('mk.facebook.trending-topics') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.facebook.trending-topics') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
            <span>Top Hashtags</span>
          </a>
          <a href="{{ route('mk.facebook.most-viewed-posts') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.facebook.most-viewed-posts') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
            <span>Most Viewed Posts</span>
          </a>
          <a href="{{ route('mk.facebook.trending-word-cloud') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.facebook.trending-word-cloud') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="19" r="2"/><circle cx="5" cy="5" r="2"/><line x1="12" y1="9" x2="6.5" y2="6.5"/><line x1="12" y1="15" x2="6.5" y2="17.5"/><line x1="15" y1="12" x2="17" y2="6.5"/><line x1="15" y1="12" x2="17" y2="17.5"/></svg></span>
            <span>Word Cloud</span>
          </a>
        </div>
      </div>

      {{-- Instagram --}}
      @php
        $instagramRoutes   = ['mk.instagram.overview','mk.instagram.trending-topics','mk.instagram.most-viewed-posts','mk.instagram.authors.demographics','mk.instagram.geographic','mk.instagram.trending-word-cloud','mk.instagram.ai-analysis'];
        $isInstagramActive = request()->routeIs($instagramRoutes);
      @endphp

      <div class="nav-item dropdown-trigger {{ $isInstagramActive ? 'has-active-child open' : '' }}"
           onclick="toggleNav('instagramSub', this)">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
        </span>
        <span>Instagram</span>
        <span class="dropdown-arrow"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
      </div>

      <div class="nav-sub-wrapper {{ $isInstagramActive ? 'open' : '' }}" id="instagramSub">
        <div class="nav-sub">
          <a href="{{ route('mk.instagram.overview') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.instagram.overview') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
            <span>Overview</span>
          </a>
          <a href="{{ route('mk.instagram.trending-topics') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.instagram.trending-topics') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
            <span>Top Hashtags</span>
          </a>
          <a href="{{ route('mk.instagram.most-viewed-posts') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.instagram.most-viewed-posts') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
            <span>Most Viewed Posts</span>
          </a>
          <a href="{{ route('mk.instagram.trending-word-cloud') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.instagram.trending-word-cloud') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="19" r="2"/><circle cx="5" cy="5" r="2"/><line x1="12" y1="9" x2="6.5" y2="6.5"/><line x1="12" y1="15" x2="6.5" y2="17.5"/><line x1="15" y1="12" x2="17" y2="6.5"/><line x1="15" y1="12" x2="17" y2="17.5"/></svg></span>
            <span>Word Cloud</span>
          </a>
        </div>
      </div>

      @php
        $youtubeRoutes   = ['mk.youtube.overview','mk.youtube.trending-topics','mk.youtube.most-viewed-posts','mk.youtube.authors.demographics','mk.youtube.geographic','mk.youtube.trending-word-cloud','mk.youtube.ai-analysis'];
        $isYoutubeActive = request()->routeIs($youtubeRoutes);
      @endphp

      <div class="nav-item dropdown-trigger {{ $isYoutubeActive ? 'has-active-child open' : '' }}"
           onclick="toggleNav('youtubeSub', this)">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
        </span>
        <span>Youtube</span>
        <span class="dropdown-arrow"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
      </div>

      <div class="nav-sub-wrapper {{ $isYoutubeActive ? 'open' : '' }}" id="youtubeSub">
        <div class="nav-sub">
          <a href="{{ route('mk.youtube.overview') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.youtube.overview') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
            <span>Overview</span>
          </a>
          <a href="{{ route('mk.youtube.trending-topics') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.youtube.trending-topics') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
            <span>Top Hashtags</span>
          </a>
          <a href="{{ route('mk.youtube.most-viewed-posts') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.youtube.most-viewed-posts') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
            <span>Most Viewed Posts</span>
          </a>
          <a href="{{ route('mk.youtube.trending-word-cloud') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.youtube.trending-word-cloud') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="19" r="2"/><circle cx="5" cy="5" r="2"/><line x1="12" y1="9" x2="6.5" y2="6.5"/><line x1="12" y1="15" x2="6.5" y2="17.5"/><line x1="15" y1="12" x2="17" y2="6.5"/><line x1="15" y1="12" x2="17" y2="17.5"/></svg></span>
            <span>Word Cloud</span>
          </a>
        </div>
      </div>

      <!-- TikTok -->
      @php
        $tiktokRoutes   = ['mk.tiktok.overview','mk.tiktok.trending-topics','mk.tiktok.most-viewed-posts','mk.tiktok.trending-word-cloud','mk.tiktok.ai-analysis'];
        $isTiktokActive = request()->routeIs($tiktokRoutes);
      @endphp

      <div class="nav-item dropdown-trigger {{ $isTiktokActive ? 'has-active-child open' : '' }}"
           onclick="toggleNav('tiktokSub', this)">
        <span class="nav-icon">
          <svg viewBox="0 0 24 24" fill="currentColor" stroke="none">
            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/>
          </svg>
        </span>
        <span>TikTok</span>
        <span class="dropdown-arrow"><svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></span>
      </div>

      <div class="nav-sub-wrapper {{ $isTiktokActive ? 'open' : '' }}" id="tiktokSub">
        <div class="nav-sub">
          <a href="{{ route('mk.tiktok.overview') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.tiktok.overview') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
            <span>Overview</span>
          </a>
          <a href="{{ route('mk.tiktok.trending-topics') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.tiktok.trending-topics') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></span>
            <span>Top Hashtags</span>
          </a>
          <a href="{{ route('mk.tiktok.most-viewed-posts') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.tiktok.most-viewed-posts') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
            <span>Most Viewed Posts</span>
          </a>
          <a href="{{ route('mk.tiktok.trending-word-cloud') }}{{ !empty($currentProjectId) ? '?project_id='.$currentProjectId : '' }}"
             class="nav-item {{ request()->routeIs('mk.tiktok.trending-word-cloud') ? 'active' : '' }}">
            <span class="menu-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="19" r="2"/><circle cx="5" cy="5" r="2"/><line x1="12" y1="9" x2="6.5" y2="6.5"/><line x1="12" y1="15" x2="6.5" y2="17.5"/><line x1="15" y1="12" x2="17" y2="6.5"/><line x1="15" y1="12" x2="17" y2="17.5"/></svg></span>
            <span>Word Cloud</span>
          </a>
        </div>
      </div>

    </div>{{-- end sidebar-scroll --}}
  </div>{{-- end sidebar --}}

  <!-- ============================================================
       MAIN CONTENT
  ============================================================ -->
  <div class="main-content">
    @yield('content')
  </div>

  <script>
    const colors = {
      primaryGreen: '#038047', primaryGreenDark: '#026738', primaryGreenLight: '#04995a',
      accentBlue: '#2FC6F6', darkBg: '#0f172a', textPrimary: '#1e293b',
      textSecondary: '#64748b', borderColor: '#e2e8f0',
      palette: ['#038047','#2FC6F6','#8b5cf6','#f59e0b','#ef4444','#10b981','#3b82f6','#ec4899','#6366f1','#14b8a6']
    };

    function toggleNav(subId, trigger) {
      const wrapper = document.getElementById(subId);
      if (!wrapper) return;
      const isOpen = wrapper.classList.contains('open');
      if (isOpen) {
        wrapper.classList.remove('open');
        trigger.classList.remove('open');
      } else {
        wrapper.classList.add('open');
        trigger.classList.add('open');
      }
    }

    function toggleDropdown(dropdownId, trigger) {
      const map = {
        projectDropdown   : 'projectSub',
        dataSourceDropdown: 'dataSourceSub',
        newsDropdown      : 'newsSub',
        xDropdown         : 'xSub',
        facebookDropdown  : 'facebookSub',
        instagramDropdown : 'instagramSub',
        youtubeDropdown   : 'youtubeSub',
        tiktokDropdown    : 'tiktokSub',
      };
      const newId = map[dropdownId] || dropdownId;
      toggleNav(newId, trigger);
    }

    function changeProject(projectId, projectName) {
      const url = new URL(window.location.href);
      url.searchParams.set('project_id', projectId);
      window.location.href = url.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.nav-sub-wrapper.open').forEach(wrapper => {
        const trigger = document.querySelector(`[onclick*="${wrapper.id}"]`);
        if (trigger) trigger.classList.add('open');
      });

      const urlParams = new URLSearchParams(window.location.search);
      const currentProjectId = urlParams.get('project_id');
      const currentPath = window.location.pathname;

      const needsProject = [
        '/mk/data-overview','/mk/data-source/users','/mk/data-source/authors',
        '/mk/data-source/volume','/mk/data-source/trends','/mk/sentiment',
        '/mk/geographic','/mk/authors','/mk/categories','/mk/engagement','/mk/publisher'
      ];

      const requiresProject = needsProject.some(path => currentPath.includes(path));

      if (requiresProject && !currentProjectId) {
        const projectItems = document.querySelectorAll('#projectSub .nav-item');
        if (projectItems.length === 1) {
          const first = projectItems[0];
          if (first.textContent.trim() !== 'No Projects Available') first.click();
        } else if (projectItems.length > 1) {
          const trigger = document.querySelector('[onclick*="projectSub"]');
          if (trigger) setTimeout(() => toggleNav('projectSub', trigger), 300);
        }
      }
    });
  </script>

  @yield('scripts')

</body>
</html>