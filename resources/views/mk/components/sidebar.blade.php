<!-- Sidebar Navigation -->
<div class="sidebar">
  <div class="logo">
    <h1>SMADIMENT</h1>
    <p>Social Media Analytics</p>
  </div>

  <div class="nav-section">
    <div class="nav-label">Main</div>
    <a href="{{ route('mk.dashboard') }}" class="nav-item {{ request()->routeIs('mk.dashboard') ? 'active' : '' }}">
      <span class="nav-icon">📊</span>
      <span>Dashboard</span>
    </a>
    <a href="{{ route('mk.projects') }}" class="nav-item {{ request()->routeIs('mk.projects') ? 'active' : '' }}">
      <span class="nav-icon">📁</span>
      <span>Projects</span>
    </a>
  </div>

  <div class="nav-section">
    <div class="nav-label">Analytics</div>
    
    <a href="{{ route('mk.sentiment') }}" class="nav-item {{ request()->routeIs('mk.sentiment') ? 'active' : '' }}">
      <span class="nav-icon">💬</span>
      <span>Sentiment Analysis</span>
    </a>
    
    <a href="{{ route('mk.geographic') }}" class="nav-item {{ request()->routeIs('mk.geographic') ? 'active' : '' }}">
      <span class="nav-icon">🌍</span>
      <span>Geographic Data</span>
    </a>
    
    <!-- Authors Submenu -->
    <div class="nav-item {{ request()->routeIs('mk.authors.*') ? 'active' : '' }}" style="cursor: default;">
      <span class="nav-icon">👥</span>
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
      <span class="nav-icon">🏷️</span>
      <span>Categories</span>
    </a>
    
    <!-- Engagement Submenu -->
    <div class="nav-item {{ request()->routeIs('mk.engagement.*') ? 'active' : '' }}" style="cursor: default;">
      <span class="nav-icon">📈</span>
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
    </div>
  </div>

  <div class="nav-section">
    <div class="nav-label">Tools</div>
    <a href="#" class="nav-item">
      <span class="nav-icon">⚙️</span>
      <span>Settings</span>
    </a>
    <a href="#" class="nav-item">
      <span class="nav-icon">📖</span>
      <span>Documentation</span>
    </a>
  </div>
</div>