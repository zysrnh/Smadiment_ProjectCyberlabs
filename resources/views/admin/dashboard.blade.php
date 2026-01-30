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

<!-- Projects Section -->
<div class="section">
  <div class="section-header">
    <h3 class="section-title">All Projects</h3>
    <span class="item-count">{{ count($projects) }}</span>
  </div>
  <div class="section-body">
    
    @if(count($projects) > 0)
    <div class="projects-grid">
      @foreach($projects as $project)
      <div class="project-card">
        <!-- Project Header -->
        <div class="project-header">
          <h4 class="project-name">{{ $project['name'] ?? $project['title'] ?? 'Unnamed Project' }}</h4>
          <span class="project-badge">Active</span>
        </div>
        
        <!-- Project Body - Two Column Layout -->
        <div class="project-body">
          <!-- Left Column: ID, Stats, Actions -->
          <div class="project-left-column">
            <div class="project-id">
              # ID: {{ $project['id'] }}
            </div>
            
            <!-- Media Stats - Horizontal Slider -->
            <div class="media-stats-container">
              <div class="media-stat-item">
                <div class="media-stat-label">All</div>
                <div class="media-stat-value">{{ number_format($project['stats']['all'] ?? 0) }}</div>
              </div>
              
              <div class="media-stat-item">
                <div class="media-stat-label">News</div>
                <div class="media-stat-value">{{ number_format($project['stats']['news'] ?? 0) }}</div>
              </div>
              
              <div class="media-stat-item">
                <div class="media-stat-label">Twit</div>
                <div class="media-stat-value">{{ number_format($project['stats']['twit'] ?? 0) }}</div>
              </div>
              
              <div class="media-stat-item">
                <div class="media-stat-label">FB</div>
                <div class="media-stat-value">{{ number_format($project['stats']['fb'] ?? 0) }}</div>
              </div>
              
              <div class="media-stat-item">
                <div class="media-stat-label">IG</div>
                <div class="media-stat-value">{{ number_format($project['stats']['ig'] ?? 0) }}</div>
              </div>
              
              <div class="media-stat-item">
                <div class="media-stat-label">YT</div>
                <div class="media-stat-value">{{ number_format($project['stats']['yt'] ?? 0) }}</div>
              </div>
              
              <div class="media-stat-item">
                <div class="media-stat-label">TikTok</div>
                <div class="media-stat-value">{{ number_format($project['stats']['tiktok'] ?? 0) }}</div>
              </div>
            </div>
            
            <!-- Actions -->
            <div class="project-actions">
              <a href="{{ route('mk.sentiment', ['project_id' => $project['id']]) }}" class="btn-primary-custom">
                <svg viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                View Analytics
              </a>
              <a href="{{ route('mk.geographic', ['project_id' => $project['id']]) }}" class="btn-icon" title="Geographic">
                <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              </a>
              <a href="{{ route('mk.publisher', ['project_id' => $project['id']]) }}" class="btn-icon" title="Publisher">
                <svg viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
              </a>
            </div>
          </div>
          
          <!-- Right Column: Chart -->
          <div class="project-chart-wrapper">
            <div class="project-chart">
              <canvas id="chart-{{ $project['id'] }}"></canvas>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <!-- Empty State -->
    <div class="empty-state">
      <svg viewBox="0 0 24 24" style="width: 72px; height: 72px; stroke: var(--dark-blue); fill: none; stroke-width: 2; opacity: 0.2;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      <h3 class="empty-title">No Projects Found</h3>
      <p class="empty-text">There are no projects available at the moment.</p>
    </div>
    @endif
    
  </div>
</div>

@endsection

@section('styles')
<style>
/* Project Body - Two Column Layout */
.project-body {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 20px;
  align-items: start;
}

/* Left Column */
.project-left-column {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Project ID */
.project-id {
  font-size: 12px;
  font-weight: 600;
  color: #7A8B96;
  padding: 10px 14px;
  background: #FAFBFC;
  border-radius: 8px;
  border: 1px solid #E8EAED;
}

/* Media Stats Container - Horizontal Slider */
.media-stats-container {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 12px;
  background: #FAFBFC;
  border-radius: 10px;
  border: 1px solid #E8EAED;
  overflow-x: auto;
  overflow-y: hidden;
}

/* Hide scrollbar but keep functionality */
.media-stats-container::-webkit-scrollbar {
  height: 4px;
}

.media-stats-container::-webkit-scrollbar-track {
  background: transparent;
}

.media-stats-container::-webkit-scrollbar-thumb {
  background: #E8EAED;
  border-radius: 4px;
}

.media-stats-container::-webkit-scrollbar-thumb:hover {
  background: #D1D5DB;
}

.media-stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  min-width: 60px;
  flex-shrink: 0;
}

.media-stat-label {
  font-size: 10px;
  font-weight: 600;
  color: #7A8B96;
  text-transform: capitalize;
  letter-spacing: 0.3px;
  line-height: 1;
  text-align: center;
  white-space: nowrap;
}

.media-stat-value {
  font-size: 16px;
  font-weight: 700;
  color: #1A2332;
  line-height: 1;
  text-align: center;
}

/* Chart Wrapper - Smaller, Right Side */
.project-chart-wrapper {
  background: #FFFFFF;
  border-radius: 12px;
  padding: 16px 14px 14px 14px;
  border: 1px solid #E8EAED;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}

/* Project Chart - Compact Size */
.project-chart {
  height: 200px;
  position: relative;
}

/* Project Actions */
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
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-primary-custom:hover {
  background: #025a34;
  transform: translateY(-2px);
  color: var(--white);
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
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  flex-shrink: 0;
}

.btn-icon:hover {
  background: var(--primary-green);
  color: var(--white);
}

.btn-icon svg {
  width: 18px;
  height: 18px;
}

/* Responsive */
@media (max-width: 1024px) {
  .project-body {
    grid-template-columns: 1fr;
  }
  
  .project-chart {
    height: 220px;
  }
}

@media (max-width: 768px) {
  .project-chart {
    height: 200px;
  }
  
  .project-chart-wrapper {
    padding: 12px 10px 10px 10px;
  }
  
  .media-stat-item {
    min-width: 55px;
  }
  
  .media-stat-value {
    font-size: 14px;
  }
}

@media (min-width: 1400px) {
  .project-body {
    grid-template-columns: 1fr 2fr;
  }
  
  .project-chart {
    height: 240px;
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
    areaBlue: '#5AB9EA',
    areaYellow: '#F2C94C',
    areaOrange: '#F2994A'
  };
  
  // Projects data from backend
  const projects = @json($projects);
  
  projects.forEach(project => {
    const ctx = document.getElementById(`chart-${project.id}`);
    if (!ctx) return;
    
    const timeline = project.timeline || { dates: [], values: [], sentiment: {} };
    let chartDates = timeline.dates || [];
    let chartValues = timeline.values || [];
    
    let posData, neuData, negData, newData;
    
    if (chartDates.length === 0) {
      chartDates = ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
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
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: chartDates,
        datasets: [
          {
            label: 'new',
            data: newData,
            borderColor: '#5AB9EA',
            backgroundColor: 'transparent',
            borderWidth: 2,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: '#5AB9EA',
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 1.5,
            fill: false
          },
          {
            label: 'pos',
            data: posData,
            borderColor: '#F2994A',
            backgroundColor: 'transparent',
            borderWidth: 2,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: '#F2994A',
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 1.5,
            fill: false
          },
          {
            label: 'net',
            data: neuData,
            borderColor: '#B0BEC5',
            backgroundColor: 'transparent',
            borderWidth: 1.5,
            tension: 0.4,
            pointRadius: 2,
            pointHoverRadius: 4,
            pointBackgroundColor: '#B0BEC5',
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 1.5,
            fill: false
          },
          {
            label: 'neg',
            data: negData,
            borderColor: '#FF6B6B',
            backgroundColor: 'transparent',
            borderWidth: 1.5,
            tension: 0.4,
            pointRadius: 2,
            pointHoverRadius: 4,
            pointBackgroundColor: '#FF6B6B',
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 1.5,
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
              padding: 10,
              font: {
                size: 10,
                weight: '500',
                family: 'Poppins'
              },
              color: '#8B96A5',
              boxWidth: 8,
              boxHeight: 8
            }
          },
          tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255, 255, 255, 0.98)',
            titleColor: '#1A2332',
            bodyColor: '#1A2332',
            borderColor: '#E8EAED',
            borderWidth: 1.5,
            padding: 10,
            cornerRadius: 8,
            titleFont: {
              size: 11,
              weight: 'bold',
              family: 'Poppins'
            },
            bodyFont: {
              size: 10,
              family: 'Poppins'
            },
            displayColors: true,
            boxWidth: 8,
            boxHeight: 8,
            boxPadding: 4,
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
                size: 9,
                weight: '500',
                family: 'Poppins'
              },
              color: '#B4BCC7',
              padding: 6,
              maxRotation: 0,
              autoSkip: true,
              maxTicksLimit: 7
            }
          },
          y: {
            display: true,
            beginAtZero: true,
            grid: {
              display: true,
              color: 'rgba(0, 0, 0, 0.03)',
              drawBorder: false,
              lineWidth: 1
            },
            ticks: {
              font: {
                size: 9,
                weight: '500',
                family: 'Poppins'
              },
              color: '#B4BCC7',
              padding: 8,
              callback: function(value) {
                if (value >= 1000) {
                  return (value / 1000) + 'k';
                }
                return value;
              },
              maxTicksLimit: 4
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