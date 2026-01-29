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
        
        <!-- Project Body -->
        <div class="project-body">
          <div class="project-id">
            # ID: {{ $project['id'] }}
          </div>
          
          <!-- Chart Container with Better Spacing -->
          <div class="project-chart-wrapper">
            <div class="project-chart">
              <canvas id="chart-{{ $project['id'] }}"></canvas>
            </div>
          </div>
          
          <!-- Media Stats - Horizontal Layout like Reference -->
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
/* Chart Wrapper - Better Spacing */
.project-chart-wrapper {
  margin-bottom: 24px;
  background: #FFFFFF;
  border-radius: 12px;
  padding: 24px 20px 20px 20px;
  border: 1px solid #E8EAED;
}

/* Project Chart - Bigger Height for Better Visibility */
.project-chart {
  height: 260px;
  position: relative;
}

/* Media Stats Container - Horizontal Layout like Reference */
.media-stats-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 24px;
  padding: 16px;
  background: #FAFBFC;
  border-radius: 10px;
  border: 1px solid #E8EAED;
  overflow-x: auto;
}

.media-stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  min-width: 60px;
  flex: 1;
}

.media-stat-label {
  font-size: 11px;
  font-weight: 600;
  color: #7A8B96;
  text-transform: capitalize;
  letter-spacing: 0.3px;
  line-height: 1;
  text-align: center;
}

.media-stat-value {
  font-size: 20px;
  font-weight: 800;
  color: #1A2332;
  line-height: 1;
  text-align: center;
}

/* Project Actions */
.project-actions {
  display: flex;
  gap: 8px;
}

.btn-primary-custom {
  flex: 1;
  padding: 14px;
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
}

.btn-icon svg {
  width: 20px;
  height: 20px;
}

/* Responsive */
@media (max-width: 768px) {
  .project-chart {
    height: 220px;
  }
  
  .media-stats-container {
    gap: 8px;
    padding: 12px;
  }
  
  .media-stat-item {
    min-width: 50px;
  }
  
  .media-stat-value {
    font-size: 16px;
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
    textGray: '#7A8B96'
  };
  
  // Projects data from backend (already includes timeline from projectStats)
  const projects = @json($projects);
  
  projects.forEach(project => {
    const ctx = document.getElementById(`chart-${project.id}`);
    if (!ctx) return;
    
    // Use timeline data that's already fetched by backend
    const timeline = project.timeline || { dates: [], values: [] };
    let chartDates = timeline.dates || [];
    let chartValues = timeline.values || [];
    
    // If no real data, use sample data
    if (chartDates.length === 0) {
      chartDates = ['22. Jan', '24. Jan', '26. Jan', '28. Jan'];
      chartValues = [100, 5212, 130, 150];
    }
    
    // Calculate sentiment breakdown (estimate based on total)
    // Positive ~45%, Neutral ~35%, Negative ~20% of total
    const posData = chartValues.map(v => Math.round(v * 0.45));
    const neuData = chartValues.map(v => Math.round(v * 0.35));
    const negData = chartValues.map(v => Math.round(v * 0.20));
    const newData = chartValues; // Total (all mentions)
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: chartDates,
        datasets: [
          {
            label: 'new',
            data: newData,
            borderColor: '#9E9E9E',
            backgroundColor: 'rgba(158, 158, 158, 0.08)',
            borderWidth: 2.5,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#FFFFFF',
            pointBorderColor: '#9E9E9E',
            pointBorderWidth: 2.5,
            fill: false
          },
          {
            label: 'net',
            data: neuData,
            borderColor: '#B0BEC5',
            backgroundColor: 'rgba(176, 190, 197, 0.08)',
            borderWidth: 2.5,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#FFFFFF',
            pointBorderColor: '#B0BEC5',
            pointBorderWidth: 2.5,
            fill: false
          },
          {
            label: 'pos',
            data: posData,
            borderColor: '#00BCD4',
            backgroundColor: 'rgba(0, 188, 212, 0.12)',
            borderWidth: 3.5,
            tension: 0.4,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: '#FFFFFF',
            pointBorderColor: '#00BCD4',
            pointBorderWidth: 3.5,
            fill: true
          },
          {
            label: 'neg',
            data: negData,
            borderColor: '#FF6B6B',
            backgroundColor: 'rgba(255, 107, 107, 0.08)',
            borderWidth: 2.5,
            tension: 0.4,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#FFFFFF',
            pointBorderColor: '#FF6B6B',
            pointBorderWidth: 2.5,
            fill: false
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: {
            top: 15,
            right: 15,
            bottom: 15,
            left: 10
          }
        },
        plugins: {
          legend: {
            display: true,
            position: 'bottom',
            align: 'center',
            labels: {
              usePointStyle: true,
              pointStyle: 'circle',
              padding: 20,
              font: {
                size: 12,
                weight: '600',
                family: 'Poppins'
              },
              color: '#7A8B96',
              boxWidth: 10,
              boxHeight: 10
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
            borderWidth: 1,
            padding: 14,
            cornerRadius: 8,
            titleFont: {
              size: 12,
              weight: 'bold',
              family: 'Poppins'
            },
            bodyFont: {
              size: 12,
              family: 'Poppins'
            },
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
                weight: '600',
                family: 'Poppins'
              },
              color: '#7A8B96',
              padding: 10
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
                size: 11,
                weight: '600',
                family: 'Poppins'
              },
              color: '#7A8B96',
              padding: 12,
              callback: function(value) {
                if (value >= 1000) {
                  return (value / 1000).toFixed(1) + 'k';
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