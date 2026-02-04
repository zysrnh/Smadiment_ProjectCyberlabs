@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Dashboard</h2>
    <p class="page-subtitle">Manage and monitor all projects</p>
  </div>
</div>

<!-- Two Column Layout -->
<div class="dashboard-container">
  
  <!-- Left Column: Project List -->
  <div class="projects-sidebar">
    <div class="sidebar-header">
      <h3 class="sidebar-title">Projects</h3>
      <span class="project-count">{{ count($projects) }}</span>
    </div>
    
    <div class="project-list">
      @if(count($projects) > 0)
        @foreach($projects as $project)
        <div class="project-item" data-project-id="{{ $project['id'] }}">
          <div class="project-item-header">
            <h4 class="project-item-name">{{ $project['name'] ?? $project['title'] ?? 'Unnamed Project' }}</h4>
            <span class="project-status-dot"></span>
          </div>
          <div class="project-item-meta">
            <span class="project-item-id"># {{ $project['id'] }}</span>
            <span class="project-item-count">{{ number_format($project['stats']['all'] ?? 0) }} items</span>
          </div>
        </div>
        @endforeach
      @else
        <div class="empty-state-sidebar">
          <svg viewBox="0 0 24 24" style="width: 48px; height: 48px; stroke: #B4BCC7; fill: none; stroke-width: 2; opacity: 0.3;">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
          </svg>
          <p class="empty-text-sidebar">No projects available</p>
        </div>
      @endif
    </div>
  </div>
  
  <!-- Right Column: Charts Area -->
  <div class="charts-area">
    @if(count($projects) > 0)
      @foreach($projects as $project)
      <div class="chart-card" id="chart-card-{{ $project['id'] }}">
        <div class="chart-card-header">
          <div class="chart-card-info">
            <h3 class="chart-card-title">{{ $project['name'] ?? $project['title'] ?? 'Unnamed Project' }}</h3>
            <span class="chart-card-id">Project #{{ $project['id'] }}</span>
          </div>
          <div class="chart-card-actions">
            <a href="{{ route('mk.sentiment', ['project_id' => $project['id']]) }}" class="btn-chart-action" title="Analytics">
              <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
              </svg>
              Analytics
            </a>
            <a href="{{ route('mk.geographic', ['project_id' => $project['id']]) }}" class="btn-chart-icon" title="Geographic">
              <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="2" y1="12" x2="22" y2="12"/>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
              </svg>
            </a>
            <a href="{{ route('mk.publisher', ['project_id' => $project['id']]) }}" class="btn-chart-icon" title="Publisher">
              <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
              </svg>
            </a>
          </div>
        </div>
        
        <div class="chart-card-stats">
          <div class="stat-item">
            <span class="stat-label">All</span>
            <span class="stat-value">{{ number_format($project['stats']['all'] ?? 0) }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">News</span>
            <span class="stat-value">{{ number_format($project['stats']['news'] ?? 0) }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Twitter</span>
            <span class="stat-value">{{ number_format($project['stats']['twit'] ?? 0) }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Facebook</span>
            <span class="stat-value">{{ number_format($project['stats']['fb'] ?? 0) }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Instagram</span>
            <span class="stat-value">{{ number_format($project['stats']['ig'] ?? 0) }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">YouTube</span>
            <span class="stat-value">{{ number_format($project['stats']['yt'] ?? 0) }}</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">TikTok</span>
            <span class="stat-value">{{ number_format($project['stats']['tiktok'] ?? 0) }}</span>
          </div>
        </div>
        
        <div class="chart-wrapper">
          <canvas id="chart-{{ $project['id'] }}"></canvas>
        </div>
      </div>
      @endforeach
    @else
      <div class="empty-state-main">
        <svg viewBox="0 0 24 24" style="width: 72px; height: 72px; stroke: #B4BCC7; fill: none; stroke-width: 2; opacity: 0.2;">
          <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
        </svg>
        <h3 class="empty-title">No Projects Found</h3>
        <p class="empty-text">There are no projects available at the moment.</p>
      </div>
    @endif
  </div>
  
</div>

@endsection

@section('styles')
<style>
/* Root Variables */
:root {
  --primary-green: #027447;
  --dark-blue: #1A2332;
  --text-gray: #7A8B96;
  --light-gray: #E8EAED;
  --bg-gray: #FAFBFC;
  --white: #FFFFFF;
  --border-color: #E8EAED;
  --sidebar-width: 260px;
}

/* Dashboard Container - Two Column Layout */
.dashboard-container {
  display: grid;
  grid-template-columns: var(--sidebar-width) 1fr;
  gap: 16px;
  height: calc(100vh - 140px);
  min-height: 500px;
}

/* ===== LEFT COLUMN: Projects Sidebar ===== */
.projects-sidebar {
  background: var(--white);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: sticky;
  top: 24px;
  height: fit-content;
  max-height: calc(100vh - 160px);
}

.sidebar-header {
  padding: 14px 16px 12px 16px;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}

.sidebar-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--dark-blue);
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

.project-count {
  background: var(--bg-gray);
  color: var(--text-gray);
  padding: 3px 10px;
  border-radius: 10px;
  font-size: 11px;
  font-weight: 700;
  font-family: 'Poppins', sans-serif;
}

/* Project List - Scrollable */
.project-list {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 8px;
}

.project-list::-webkit-scrollbar {
  width: 6px;
}

.project-list::-webkit-scrollbar-track {
  background: transparent;
}

.project-list::-webkit-scrollbar-thumb {
  background: var(--light-gray);
  border-radius: 3px;
}

.project-list::-webkit-scrollbar-thumb:hover {
  background: #D1D5DB;
}

/* Project Item */
.project-item {
  padding: 10px 12px;
  background: var(--bg-gray);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  margin-bottom: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.project-item:hover {
  background: var(--white);
  border-color: var(--primary-green);
  transform: translateX(4px);
  box-shadow: 0 2px 8px rgba(2, 116, 71, 0.08);
}

.project-item-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
}

.project-item-name {
  font-size: 12px;
  font-weight: 600;
  color: var(--dark-blue);
  margin: 0;
  font-family: 'Poppins', sans-serif;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  flex: 1;
}

.project-status-dot {
  width: 6px;
  height: 6px;
  background: var(--primary-green);
  border-radius: 50%;
  flex-shrink: 0;
  margin-left: 6px;
}

.project-item-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 10px;
  color: var(--text-gray);
  font-family: 'Poppins', sans-serif;
}

.project-item-id {
  font-weight: 600;
}

.project-item-count {
  font-weight: 500;
}

/* Empty State Sidebar */
.empty-state-sidebar {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  text-align: center;
}

.empty-text-sidebar {
  font-size: 13px;
  color: var(--text-gray);
  margin: 12px 0 0 0;
  font-family: 'Poppins', sans-serif;
}

/* ===== RIGHT COLUMN: Charts Area ===== */
.charts-area {
  display: flex;
  flex-direction: column;
  gap: 16px;
  overflow-y: auto;
  overflow-x: hidden;
  padding-right: 4px;
}

.charts-area::-webkit-scrollbar {
  width: 6px;
}

.charts-area::-webkit-scrollbar-track {
  background: transparent;
}

.charts-area::-webkit-scrollbar-thumb {
  background: var(--light-gray);
  border-radius: 3px;
}

.charts-area::-webkit-scrollbar-thumb:hover {
  background: #D1D5DB;
}

/* Chart Card */
.chart-card {
  background: var(--white);
  border-radius: 10px;
  border: 1px solid var(--border-color);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  padding: 16px;
  scroll-margin-top: 16px;
}

.chart-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
  flex-wrap: wrap;
  gap: 10px;
}

.chart-card-info {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.chart-card-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--dark-blue);
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

.chart-card-id {
  font-size: 11px;
  font-weight: 600;
  color: var(--text-gray);
  font-family: 'Poppins', sans-serif;
}

.chart-card-actions {
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-chart-action {
  padding: 8px 12px;
  background: var(--primary-green);
  color: var(--white);
  border: none;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 5px;
}

.btn-chart-action:hover {
  background: #025a34;
  transform: translateY(-2px);
  color: var(--white);
}

.btn-chart-icon {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-gray);
  color: var(--text-gray);
  border: 1px solid var(--border-color);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}

.btn-chart-icon:hover {
  background: var(--primary-green);
  color: var(--white);
  border-color: var(--primary-green);
}

/* Chart Stats */
.chart-card-stats {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px;
  background: var(--bg-gray);
  border-radius: 8px;
  margin-bottom: 14px;
  overflow-x: auto;
  overflow-y: hidden;
}

.chart-card-stats::-webkit-scrollbar {
  height: 3px;
}

.chart-card-stats::-webkit-scrollbar-track {
  background: transparent;
}

.chart-card-stats::-webkit-scrollbar-thumb {
  background: var(--border-color);
  border-radius: 2px;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 3px;
  min-width: 60px;
  flex-shrink: 0;
}

.stat-label {
  font-size: 9px;
  font-weight: 600;
  color: var(--text-gray);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-family: 'Poppins', sans-serif;
}

.stat-value {
  font-size: 14px;
  font-weight: 700;
  color: var(--dark-blue);
  font-family: 'Poppins', sans-serif;
}

/* Chart Wrapper */
.chart-wrapper {
  height: 220px;
  position: relative;
}

/* Empty State Main */
.empty-state-main {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  text-align: center;
  background: var(--white);
  border-radius: 12px;
  border: 1px solid var(--border-color);
}

.empty-title {
  font-size: 18px;
  font-weight: 700;
  color: var(--dark-blue);
  margin: 16px 0 8px 0;
  font-family: 'Poppins', sans-serif;
}

.empty-text {
  font-size: 14px;
  color: var(--text-gray);
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

/* ===== RESPONSIVE ===== */

/* Tablet */
@media (max-width: 1200px) {
  :root {
    --sidebar-width: 240px;
  }
  
  .chart-card-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .chart-card-actions {
    width: 100%;
    justify-content: flex-end;
  }
}

/* Large Tablet / Small Desktop */
@media (max-width: 1024px) {
  .dashboard-container {
    grid-template-columns: 1fr;
    height: auto;
  }
  
  .projects-sidebar {
    position: relative;
    top: 0;
    max-height: 400px;
  }
  
  .charts-area {
    overflow-y: visible;
  }
}

/* Mobile */
@media (max-width: 768px) {
  .dashboard-container {
    gap: 12px;
  }
  
  .projects-sidebar {
    max-height: 280px;
  }
  
  .sidebar-header {
    padding: 12px;
  }
  
  .project-list {
    padding: 6px;
  }
  
  .chart-card {
    padding: 12px;
  }
  
  .chart-card-title {
    font-size: 13px;
  }
  
  .chart-card-actions {
    flex-wrap: wrap;
  }
  
  .btn-chart-action {
    flex: 1;
    justify-content: center;
  }
  
  .chart-wrapper {
    height: 200px;
  }
  
  .stat-item {
    min-width: 55px;
  }
  
  .stat-value {
    font-size: 12px;
  }
}

/* Large Desktop */
@media (min-width: 1400px) {
  :root {
    --sidebar-width: 280px;
  }
  
  .chart-wrapper {
    height: 260px;
  }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  
  // Define colors
  const colors = {
    darkBlue: '#1A2332',
    primaryGreen: '#027447',
    lightGray: '#E8EAED',
    white: '#FFFFFF',
    textGray: '#7A8B96',
    blue: '#5AB9EA',
    orange: '#F2994A',
    gray: '#B0BEC5',
    red: '#FF6B6B'
  };
  
  // Projects data from backend
  const projects = @json($projects);
  
  // Smooth scroll to chart when project item is clicked
  document.querySelectorAll('.project-item').forEach(item => {
    item.addEventListener('click', function() {
      const projectId = this.getAttribute('data-project-id');
      const chartCard = document.getElementById(`chart-card-${projectId}`);
      
      if (chartCard) {
        chartCard.scrollIntoView({ 
          behavior: 'smooth', 
          block: 'start'
        });
        
        // Highlight effect
        chartCard.style.transition = 'all 0.3s ease';
        chartCard.style.boxShadow = '0 4px 20px rgba(2, 116, 71, 0.15)';
        chartCard.style.borderColor = colors.primaryGreen;
        
        setTimeout(() => {
          chartCard.style.boxShadow = '0 1px 3px rgba(0, 0, 0, 0.02)';
          chartCard.style.borderColor = colors.lightGray;
        }, 2000);
      }
    });
  });
  
  // Create charts for each project
  projects.forEach(project => {
    const ctx = document.getElementById(`chart-${project.id}`);
    if (!ctx) return;
    
    const timeline = project.timeline || { dates: [], values: [], sentiment: {} };
    let chartDates = timeline.dates || [];
    let chartValues = timeline.values || [];
    
    let posData, neuData, negData, newData;
    
    // Generate dummy data if no real data exists
    if (chartDates.length === 0) {
      chartDates = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      newData = [520, 520, 490, 660, 640, 740, 300, 420, 540, 600, 750, 870];
      posData = [220, 270, 350, 450, 470, 520, 580, 650, 670, 690, 760, 780];
      neuData = [180, 210, 240, 280, 300, 320, 340, 360, 380, 400, 420, 440];
      negData = [120, 140, 160, 180, 200, 220, 240, 260, 280, 300, 320, 340];
    } else {
      newData = chartValues;
      
      if (timeline.sentiment && timeline.sentiment.positive) {
        posData = timeline.sentiment.positive;
        neuData = timeline.sentiment.neutral;
        negData = timeline.sentiment.negative;
      } else {
        posData = chartValues.map(v => Math.round(v * 0.40));
        neuData = chartValues.map(v => Math.round(v * 0.44));
        negData = chartValues.map(v => Math.round(v * 0.16));
      }
    }
    
    // Create Chart
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: chartDates,
        datasets: [
          {
            label: 'New',
            data: newData,
            borderColor: colors.blue,
            backgroundColor: 'transparent',
            borderWidth: 2.5,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: colors.blue,
            pointBorderColor: colors.white,
            pointBorderWidth: 2,
            fill: false
          },
          {
            label: 'Positive',
            data: posData,
            borderColor: colors.orange,
            backgroundColor: 'transparent',
            borderWidth: 2.5,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: colors.orange,
            pointBorderColor: colors.white,
            pointBorderWidth: 2,
            fill: false
          },
          {
            label: 'Neutral',
            data: neuData,
            borderColor: colors.gray,
            backgroundColor: 'transparent',
            borderWidth: 2,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: colors.gray,
            pointBorderColor: colors.white,
            pointBorderWidth: 2,
            fill: false
          },
          {
            label: 'Negative',
            data: negData,
            borderColor: colors.red,
            backgroundColor: 'transparent',
            borderWidth: 2,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: colors.red,
            pointBorderColor: colors.white,
            pointBorderWidth: 2,
            fill: false
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: 5,
            right: 5,
            bottom: 5,
            left: 5
          }
        },
        plugins: {
          legend: {
            display: true,
            position: 'bottom',
            align: 'start',
            labels: {
              usePointStyle: true,
              pointStyle: 'circle',
              padding: 12,
              font: {
                size: 10,
                weight: '600',
                family: 'Poppins'
              },
              color: colors.textGray,
              boxWidth: 8,
              boxHeight: 8
            }
          },
          tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.98)',
            titleColor: colors.darkBlue,
            bodyColor: colors.darkBlue,
            borderColor: colors.lightGray,
            borderWidth: 1,
            padding: 10,
            cornerRadius: 6,
            titleFont: {
              size: 11,
              weight: '700',
              family: 'Poppins'
            },
            bodyFont: {
              size: 10,
              weight: '500',
              family: 'Poppins'
            },
            displayColors: true,
            boxWidth: 8,
            boxHeight: 8,
            boxPadding: 5,
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                label += context.parsed.y.toLocaleString();
                return label;
              }
            }
          }
        },
        scales: {
          x: {
            display: true,
            grid: {
              display: false,
              drawBorder: false
            },
            ticks: {
              font: {
                size: 10,
                weight: '500',
                family: 'Poppins'
              },
              color: colors.textGray,
              padding: 6,
              maxRotation: 0,
              autoSkip: true,
              maxTicksLimit: 8
            }
          },
          y: {
            display: true,
            beginAtZero: true,
            grid: {
              display: true,
              color: 'rgba(0, 0, 0, 0.04)',
              drawBorder: false,
              lineWidth: 1
            },
            ticks: {
              font: {
                size: 10,
                weight: '500',
                family: 'Poppins'
              },
              color: colors.textGray,
              padding: 8,
              callback: function(value) {
                if (value >= 1000) {
                  return (value / 1000) + 'k';
                }
                return value;
              },
              maxTicksLimit: 5
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }
    });
  });
  
});
</script>
@endsection