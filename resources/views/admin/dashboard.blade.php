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
    <!-- Projects List (tanpa grid, full width) -->
    <div class="projects-list">
      @foreach($projects as $project)
      <div class="project-card-full">
        <!-- Project Header -->
        <div class="project-header">
          <div class="project-info">
            <h4 class="project-name">{{ $project['name'] ?? $project['title'] ?? 'Unnamed Project' }}</h4>
            <span class="project-id"># ID: {{ $project['id'] }}</span>
          </div>
          <span class="project-badge">Active</span>
        </div>
        
        <!-- Project Body -->
        <div class="project-body">
          
          <!-- Chart Container - Full Width -->
          <div class="project-chart-wrapper">
            <div class="project-chart">
              <canvas id="chart-{{ $project['id'] }}"></canvas>
            </div>
          </div>
          
          <!-- Bottom Section: Stats + Actions -->
          <div class="project-footer">
            <!-- Media Stats - Horizontal Layout -->
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
/* Projects List - Full Width Layout (bukan grid) */
.projects-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Project Card Full - Full Width */
.project-card-full {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 28px 32px;
  border: 1px solid #E8EAED;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  transition: all 0.3s ease;
}

.project-card-full:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

/* Project Header */
.project-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid #F0F2F5;
}

.project-info {
  display: flex;
  align-items: center;
  gap: 16px;
}

.project-name {
  font-size: 22px;
  font-weight: 700;
  color: #1A2332;
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

.project-id {
  font-size: 13px;
  font-weight: 500;
  color: #7A8B96;
  background: #F0F2F5;
  padding: 6px 14px;
  border-radius: 8px;
}

.project-badge {
  background: #E8F5E9;
  color: #027447;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Project Body */
.project-body {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Chart Wrapper - Full Width dengan tinggi yang lebih besar */
.project-chart-wrapper {
  width: 100%;
  background: #FAFBFC;
  border-radius: 14px;
  padding: 28px 24px 24px 24px;
  border: 1px solid #E8EAED;
}

/* Project Chart - Tinggi lebih besar untuk full width */
.project-chart {
  height: 420px;
  position: relative;
  width: 100%;
}

/* Project Footer - Stats & Actions */
.project-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
  flex-wrap: wrap;
}

/* Media Stats Container */
.media-stats-container {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 18px 24px;
  background: #FAFBFC;
  border-radius: 12px;
  border: 1px solid #E8EAED;
  flex: 1;
  min-width: 0;
}

.media-stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  flex: 1;
  min-width: 70px;
}

.media-stat-label {
  font-size: 12px;
  font-weight: 600;
  color: #7A8B96;
  text-transform: capitalize;
  letter-spacing: 0.3px;
  line-height: 1;
  text-align: center;
}

.media-stat-value {
  font-size: 22px;
  font-weight: 800;
  color: #1A2332;
  line-height: 1;
  text-align: center;
}

/* Project Actions */
.project-actions {
  display: flex;
  gap: 10px;
  flex-shrink: 0;
}

.btn-primary-custom {
  padding: 14px 24px;
  background: var(--primary-green);
  color: var(--white);
  border: none;
  border-radius: 10px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  white-space: nowrap;
}

.btn-primary-custom:hover {
  background: #025a34;
  transform: translateY(-2px);
  color: var(--white);
  box-shadow: 0 4px 12px rgba(2, 116, 71, 0.3);
}

.btn-icon {
  width: 48px;
  height: 48px;
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
}

.btn-icon:hover {
  background: var(--primary-green);
  color: var(--white);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(2, 116, 71, 0.3);
}

.btn-icon svg {
  width: 20px;
  height: 20px;
}

/* Responsive */
@media (max-width: 1024px) {
  .project-footer {
    flex-direction: column;
    align-items: stretch;
  }
  
  .project-actions {
    width: 100%;
  }
  
  .btn-primary-custom {
    flex: 1;
  }
}

@media (max-width: 768px) {
  .project-card-full {
    padding: 20px;
  }
  
  .project-chart {
    height: 320px;
  }
  
  .project-chart-wrapper {
    padding: 20px 16px 16px 16px;
  }
  
  .media-stats-container {
    gap: 12px;
    padding: 14px 16px;
    overflow-x: auto;
  }
  
  .media-stat-item {
    min-width: 60px;
  }
  
  .media-stat-value {
    font-size: 18px;
  }
  
  .project-name {
    font-size: 18px;
  }
  
  .project-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
}

@media (min-width: 1400px) {
  .project-chart {
    height: 480px;
  }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  
  // Define colors - Warna Lebih Soft & Modern
  const colors = {
    darkBlue: '#1A2332',
    primaryGreen: '#027447',
    lightGray: '#E8EAED',
    white: '#FFFFFF',
    textGray: '#7A8B96',
    // Line chart colors
    areaBlue: '#5AB9EA',
    areaYellow: '#F2C94C',
    areaOrange: '#F2994A'
  };
  
  // Projects data from backend
  const projects = @json($projects);
  
  projects.forEach(project => {
    const ctx = document.getElementById(`chart-${project.id}`);
    if (!ctx) return;
    
    // Use timeline data with sentiment breakdown from backend
    const timeline = project.timeline || { dates: [], values: [], sentiment: {} };
    let chartDates = timeline.dates || [];
    let chartValues = timeline.values || [];
    
    // If no real data, use sample data
    let posData, neuData, negData, newData;
    
    if (chartDates.length === 0) {
      // Sample data untuk tampilan line chart
      chartDates = ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      newData = [520, 520, 490, 660, 640, 740, 300, 420, 540, 600, 750, 870];
      posData = [220, 270, 350, 450, 470, 520, 580, 650, 670, 690, 760, 780];
      neuData = [180, 210, 240, 280, 300, 320, 340, 360, 380, 400, 420, 440];
      negData = [120, 140, 160, 180, 200, 220, 240, 260, 280, 300, 320, 340];
    } else {
      // Real data from API with sentiment breakdown
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
            borderWidth: 3,
            tension: 0.4,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: '#5AB9EA',
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 2,
            fill: false
          },
          {
            label: 'pos',
            data: posData,
            borderColor: '#F2994A',
            backgroundColor: 'transparent',
            borderWidth: 3,
            tension: 0.4,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: '#F2994A',
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 2,
            fill: false
          },
          {
            label: 'net',
            data: neuData,
            borderColor: '#B0BEC5',
            backgroundColor: 'transparent',
            borderWidth: 2.5,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#B0BEC5',
            pointBorderColor: '#FFFFFF',
            pointBorderWidth: 2,
            fill: false
          },
          {
            label: 'neg',
            data: negData,
            borderColor: '#FF6B6B',
            backgroundColor: 'transparent',
            borderWidth: 2.5,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#FF6B6B',
            pointBorderColor: '#FFFFFF',
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
            top: 20,
            right: 20,
            bottom: 10,
            left: 10
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
              padding: 18,
              font: {
                size: 13,
                weight: '500',
                family: 'Poppins'
              },
              color: '#8B96A5',
              boxWidth: 12,
              boxHeight: 12
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
            padding: 16,
            cornerRadius: 10,
            titleFont: {
              size: 13,
              weight: 'bold',
              family: 'Poppins'
            },
            bodyFont: {
              size: 12,
              family: 'Poppins'
            },
            displayColors: true,
            boxWidth: 10,
            boxHeight: 10,
            boxPadding: 6,
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                label += context.parsed.y.toLocaleString() + ' mentions';
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
                size: 11,
                weight: '500',
                family: 'Poppins'
              },
              color: '#B4BCC7',
              padding: 12,
              maxRotation: 0,
              autoSkip: true,
              maxTicksLimit: 12
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
                size: 11,
                weight: '500',
                family: 'Poppins'
              },
              color: '#B4BCC7',
              padding: 14,
              callback: function(value) {
                if (value >= 1000) {
                  return (value / 1000) + 'k';
                }
                return value;
              },
              maxTicksLimit: 6
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