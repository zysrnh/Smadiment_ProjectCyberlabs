@extends('mk.layouts.app')

@section('title', 'Dashboard - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Welcome back, {{ auth()->user()->name ?? 'User' }}!</h2>
    <div class="page-subtitle">Here are your assigned projects</div>
  </div>
  
  <!-- Logout Button -->
  <form method="POST" action="{{ route('user.logout') }}" style="display: inline;">
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

<div class="content-wrapper">
  <!-- Available Projects -->
  <div class="section">
    <div class="section-header">
      <h3 class="section-title">Your Projects</h3>
      <span class="badge-count">
        {{ count($projects) }} {{ count($projects) === 1 ? 'Project' : 'Projects' }}
      </span>
    </div>
    
    <div class="section-body">
      @if(count($projects) === 0)
        <div class="empty-state">
          <div class="empty-icon">
            <svg viewBox="0 0 24 24">
              <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
            </svg>
          </div>
          <div class="empty-title">No projects assigned</div>
          <div class="empty-text">Contact your administrator to get access to projects</div>
        </div>
      @else
        <!-- Skeleton Loader -->
        <div id="projectsSkeleton" style="display: block;">
          @for($i = 0; $i < min(5, count($projects)); $i++)
          <div class="skeleton-item"></div>
          @endfor
        </div>
        
        <!-- Actual Table -->
        <div id="projectsTable" style="display: none;">
          <div class="projects-grid">
            @foreach($projects as $project)
              @php
                $id = $project['id'] ?? '-';
                $title = $project['title'] ?? $project['name'] ?? $project['project_name'] ?? 'Untitled Project';
                $group = $project['project_group_name'] ?? 'No Group';
                $type = $project['project_type'] ?? 'Unknown';
                $media = $project['media_types'] ?? 'None';
              @endphp
              
              <div class="project-card">
                <div class="project-header">
                  <div class="project-icon">
                    <svg viewBox="0 0 24 24">
                      <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                  </div>
                  <div class="project-id">#{{ $id }}</div>
                </div>
                
                <div class="project-body">
                  <h4 class="project-title">{{ $title }}</h4>
                  
                  <div class="project-meta">
                    <div class="meta-item">
                      <svg viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                      </svg>
                      <span>{{ $group }}</span>
                    </div>
                    
                    <div class="meta-item">
                      <svg viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7"/>
                        <rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/>
                      </svg>
                      <span>{{ $type }}</span>
                    </div>
                    
                    <div class="meta-item full-width">
                      <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                      </svg>
                      <span>{{ $media }}</span>
                    </div>
                  </div>
                </div>
                
                <div class="project-footer">
                  <a href="{{ route('mk.data-overview') }}?project_id={{ $id }}" class="btn-view-project">
                    Data Overview
                    <svg viewBox="0 0 24 24">
                      <line x1="5" y1="12" x2="19" y2="12"/>
                      <polyline points="12 5 19 12 12 19"/>
                    </svg>
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

@endsection

@section('styles')
<style>
/* Override font to Poppins */
body,
.section-title,
.project-title,
.btn-view-project,
.badge-count,
.btn-logout {
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
}

/* ========================================================
   CONTENT WRAPPER
   ======================================================== */
.content-wrapper {
  padding: 0 32px 32px;
}

/* ========================================================
   TOP BAR
   ======================================================== */
.btn-logout {
  padding: 12px 20px;
  background: #DC2626;
  color: #ffffff;
  border: none;
  border-radius: 10px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-logout:hover {
  background: #B91C1C;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

/* ========================================================
   SECTION STYLES
   ======================================================== */
.section {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  padding: 24px;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e2e8f0;
}

.section-title {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.badge-count {
  background: linear-gradient(135deg, #038047 0%, #026738 100%);
  color: #ffffff;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 700;
}

.section-body {
  /* No extra padding needed */
}

/* ========================================================
   EMPTY STATE
   ======================================================== */
.empty-state {
  text-align: center;
  padding: 60px 20px;
}

.empty-icon {
  width: 80px;
  height: 80px;
  background: #f1f5f9;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.empty-icon svg {
  width: 40px;
  height: 40px;
  stroke: #94a3b8;
  fill: none;
  stroke-width: 2;
}

.empty-title {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 8px;
}

.empty-text {
  font-size: 14px;
  color: #64748b;
  font-weight: 500;
}

/* ========================================================
   SKELETON LOADER
   ======================================================== */
.skeleton-item {
  height: 180px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 16px;
  margin-bottom: 16px;
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

/* ========================================================
   PROJECTS GRID
   ======================================================== */
.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
}

/* ========================================================
   PROJECT CARD
   ======================================================== */
.project-card {
  background: #ffffff;
  border: 2px solid #e2e8f0;
  border-radius: 16px;
  padding: 20px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.project-card:hover {
  border-color: #038047;
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -6px rgba(3, 128, 71, 0.15);
}

/* Project Header */
.project-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.project-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.project-icon svg {
  width: 24px;
  height: 24px;
  stroke: #038047;
  fill: none;
  stroke-width: 2;
}

.project-id {
  font-size: 14px;
  font-weight: 800;
  color: #038047;
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  padding: 6px 12px;
  border-radius: 8px;
}

/* Project Body */
.project-body {
  flex: 1;
}

.project-title {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 16px 0;
  line-height: 1.4;
}

.project-meta {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #64748b;
  font-weight: 500;
}

.meta-item svg {
  width: 16px;
  height: 16px;
  stroke: #94a3b8;
  fill: none;
  stroke-width: 2;
  flex-shrink: 0;
}

.meta-item.full-width {
  flex-wrap: wrap;
}

.meta-item.full-width span {
  word-break: break-word;
}

/* Project Footer */
.project-footer {
  padding-top: 16px;
  border-top: 1px solid #e2e8f0;
}

.btn-view-project {
  width: 100%;
  padding: 12px 20px;
  background: linear-gradient(135deg, #038047 0%, #026738 100%);
  color: #ffffff;
  border: none;
  border-radius: 10px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-view-project:hover {
  background: linear-gradient(135deg, #026738 0%, #038047 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 16px -4px rgba(3, 128, 71, 0.4);
}

.btn-view-project svg {
  width: 16px;
  height: 16px;
  stroke: currentColor;
  fill: none;
  stroke-width: 2;
}

/* ========================================================
   FADE-IN ANIMATION
   ======================================================== */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in {
  animation: fadeIn 0.5s ease-out;
}

/* ========================================================
   RESPONSIVE
   ======================================================== */
@media (max-width: 768px) {
  .content-wrapper {
    padding: 0 20px 20px;
  }

  .projects-grid {
    grid-template-columns: 1fr;
  }

  .section {
    padding: 20px;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .badge-count {
    align-self: flex-start;
  }
}

@media (max-width: 480px) {
  .project-card {
    padding: 16px;
  }

  .project-title {
    font-size: 16px;
  }

  .btn-view-project {
    padding: 10px 16px;
    font-size: 13px;
  }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Show projects after 300ms with fade-in
  setTimeout(function() {
    const skeleton = document.getElementById('projectsSkeleton');
    const table = document.getElementById('projectsTable');
    
    if (skeleton && table) {
      skeleton.style.display = 'none';
      table.style.display = 'block';
      table.classList.add('fade-in');
    }
  }, 300);
});
</script>
@endsection