@extends('mk.layouts.app')

@section('title', 'Active Users - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Most Active Users</h2>
    <div class="page-subtitle">Analyze top contributors and most active social media users</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
  
  <!-- Main Content -->
  <div>
    
    <!-- Active Users Table -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Top Active Users (Top 10)</h3>
      </div>
      
      <div class="section-body">
        @if(count($tableData) === 0)
          <div class="empty-state">
            <div class="empty-icon">👤</div>
            <div class="empty-text">No active users data available. Please select a project.</div>
          </div>
        @else
          <div class="data-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width: 60px; text-align: center;">#</th>
                  <th>Username</th>
                  <th style="text-align: right; width: 150px;">Post Count</th>
                  <th style="text-align: right; width: 120px;">Percentage</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $total = array_sum(array_column($tableData, 'count'));
                  $usernames = array_column($tableData, 'username');
                @endphp
                @foreach($tableData as $index => $row)
                  @php
                    $percentage = $total > 0 ? round($row['count'] / $total * 100, 1) : 0;
                    $username = '@' . $usernames[$index];
                  @endphp
                  <tr>
                    <td style="text-align: center; font-weight: 800; color: var(--sage);">{{ $index + 1 }}</td>
                    <td style="font-weight: 700; color: var(--dark-teal);">
                      {{ $username }}
                    </td>
                    <td class="count-cell">{{ number_format($row['count']) }}</td>
                    <td class="count-cell">{{ $percentage }}%</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Summary Stats -->
          <div style="margin-top: 24px; padding: 20px; background: var(--cream); border-radius: 12px; border: 2px solid var(--beige);">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; text-align: center;">
              <div>
                <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                  Total Users
                </div>
                <div style="font-size: 24px; font-weight: 900; color: var(--dark-teal);">
                  {{ number_format(count($tableData)) }}
                </div>
              </div>
              <div>
                <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                  Total Posts
                </div>
                <div style="font-size: 24px; font-weight: 900; color: var(--dark-teal);">
                  {{ number_format($total) }}
                </div>
              </div>
              <div>
                <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                  Avg per User
                </div>
                <div style="font-size: 24px; font-weight: 900; color: var(--dark-teal);">
                  {{ count($tableData) > 0 ? number_format($total / count($tableData), 1) : 0 }}
                </div>
              </div>
            </div>
          </div>

          <!-- Chart Mode Toggle -->
          <div style="margin-top: 24px;">
            <div class="mode-toggle">
              <button class="mode-btn active" onclick="switchMode('line')">Line Chart</button>
              <button class="mode-btn" onclick="switchMode('bar')">Bar Chart</button>
              <button class="mode-btn" onclick="switchMode('donut')">Donut Chart</button>
            </div>

            <!-- Line Chart -->
            <div class="chart-container" id="lineContainer" style="height: 400px;">
              <canvas id="lineChart"></canvas>
            </div>

            <!-- Bar Chart -->
            <div class="chart-container" id="barContainer" style="height: 400px; display: none;">
              <canvas id="barChart"></canvas>
            </div>

            <!-- Donut Chart -->
            <div class="chart-container donut" id="donutContainer" style="height: 400px; display: none;">
              <canvas id="donutChart"></canvas>
            </div>
          </div>
        @endif
      </div>
    </div>

    <!-- Debug Section -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">API Response</h3>
      </div>
      <div class="section-body">
        <details>
          <summary class="debug-toggle">View Raw Data</summary>
          <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($rawData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </details>
      </div>
    </div>

  </div>

  <!-- Sidebar Filter -->
  <div>
    @include('mk.components.analytics-form', [
      'projects' => $projects,
      'projectId' => $projectId,
      'params' => $params,
      'showMedia' => false,
      'showSentiment' => false,
      'helperText' => 'Data from /most_active_users endpoint'
    ])
  </div>

</div>

@endsection

@section('scripts')
@if(count($tableData) > 0)
<script>
  // Prepare data
  const usernames = @json(array_column($tableData, 'username'));
  const counts = @json(array_column($tableData, 'count'));

  let lineChart, barChart, donutChart;

  // Line Chart
  const lineCtx = document.getElementById('lineChart').getContext('2d');
  lineChart = new Chart(lineCtx, {
    type: 'line',
    data: {
      labels: usernames,
      datasets: [{
        label: 'Post Count',
        data: counts,
        borderColor: colors.brown,
        backgroundColor: colors.sage + '40',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: colors.brown,
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 6,
        pointHoverRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'User Activity Distribution - Line Chart',
          font: {
            size: 16,
            weight: 'bold',
            family: 'Montserrat'
          },
          color: colors.darkTeal
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: colors.beige
          },
          ticks: {
            font: {
              family: 'Montserrat',
              weight: '600'
            }
          }
        },
        x: {
          grid: {
            display: false
          },
          ticks: {
            font: {
              family: 'Montserrat',
              weight: '600'
            },
            maxRotation: 45,
            minRotation: 45
          }
        }
      }
    }
  });

  // Bar Chart (Horizontal)
  const barCtx = document.getElementById('barChart').getContext('2d');
  barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
      labels: usernames,
      datasets: [{
        label: 'Post Count',
        data: counts,
        backgroundColor: colors.palette,
        borderWidth: 2,
        borderColor: '#fff',
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'y', // Horizontal bars
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'User Activity Distribution - Bar Chart',
          font: {
            size: 16,
            weight: 'bold',
            family: 'Montserrat'
          },
          color: colors.darkTeal
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          grid: {
            color: colors.beige
          },
          ticks: {
            font: {
              family: 'Montserrat',
              weight: '600'
            }
          }
        },
        y: {
          grid: {
            display: false
          },
          ticks: {
            font: {
              family: 'Montserrat',
              weight: '600'
            }
          }
        }
      }
    }
  });

  // Donut Chart
  const donutCtx = document.getElementById('donutChart').getContext('2d');
  donutChart = new Chart(donutCtx, {
    type: 'doughnut',
    data: {
      labels: usernames,
      datasets: [{
        data: counts,
        backgroundColor: colors.palette,
        borderWidth: 3,
        borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            font: {
              family: 'Montserrat',
              weight: '600',
              size: 12
            },
            padding: 15,
            usePointStyle: true
          }
        },
        title: {
          display: true,
          text: 'User Activity Distribution - Donut Chart',
          font: {
            size: 16,
            weight: 'bold',
            family: 'Montserrat'
          },
          color: colors.darkTeal
        }
      }
    }
  });

  // Mode Switching
  function switchMode(mode) {
    const lineContainer = document.getElementById('lineContainer');
    const barContainer = document.getElementById('barContainer');
    const donutContainer = document.getElementById('donutContainer');
    const buttons = document.querySelectorAll('.mode-btn');

    // Hide all
    lineContainer.style.display = 'none';
    barContainer.style.display = 'none';
    donutContainer.style.display = 'none';
    buttons.forEach(btn => btn.classList.remove('active'));

    // Show selected
    if (mode === 'line') {
      lineContainer.style.display = 'block';
      buttons[0].classList.add('active');
    } else if (mode === 'bar') {
      barContainer.style.display = 'block';
      buttons[1].classList.add('active');
    } else if (mode === 'donut') {
      donutContainer.style.display = 'block';
      buttons[2].classList.add('active');
    }
  }
</script>
@endif
@endsection