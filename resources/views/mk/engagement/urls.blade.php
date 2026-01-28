@extends('mk.layouts.app')

@section('title', 'Shared URLs - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Shared URLs Frequency</h2>
    <div class="page-subtitle">Analyze most frequently shared URLs across social media</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
  
  <!-- Main Content -->
  <div>
    
    <!-- Shared URLs Table -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Top Shared URLs (Top 10)</h3>
      </div>
      
      <div class="section-body">
        @if(count($tableData) === 0)
          <div class="empty-state">
            <div class="empty-icon">🔗</div>
            <div class="empty-text">No shared URL data available. Please select a project.</div>
          </div>
        @else
          <div class="data-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width: 60px; text-align: center;">#</th>
                  <th>URL</th>
                  <th style="text-align: right; width: 150px;">Frequency</th>
                  <th style="text-align: right; width: 120px;">Percentage</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $total = array_sum(array_column($tableData, 'freq'));
                @endphp
                @foreach($tableData as $index => $row)
                  @php
                    $percentage = $total > 0 ? round($row['freq'] / $total * 100, 1) : 0;
                  @endphp
                  <tr>
                    <td style="text-align: center; font-weight: 800; color: var(--sage);">{{ $index + 1 }}</td>
                    <td style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $row['url'] }}">
                      <a href="{{ $row['url'] }}" target="_blank" rel="noopener noreferrer" style="color: var(--brown); text-decoration: none; font-weight: 600;">
                        {{ $row['url'] }}
                      </a>
                    </td>
                    <td class="count-cell">{{ number_format($row['freq']) }}</td>
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
                  Total URLs
                </div>
                <div style="font-size: 24px; font-weight: 900; color: var(--dark-teal);">
                  {{ number_format(count($tableData)) }}
                </div>
              </div>
              <div>
                <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                  Total Shares
                </div>
                <div style="font-size: 24px; font-weight: 900; color: var(--dark-teal);">
                  {{ number_format($total) }}
                </div>
              </div>
              <div>
                <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                  Avg per URL
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
      'helperText' => 'Data from /shared_url_freq endpoint'
    ])
  </div>

</div>

@endsection

@section('scripts')
@if(count($tableData) > 0)
<script>
  // Prepare data - truncate long URLs for chart labels
  const rawUrls = @json(array_column($tableData, 'url'));
  const urlLabels = rawUrls.map(url => {
    if (url.length > 30) {
      return url.substring(0, 30) + '...';
    }
    return url;
  });
  const frequencies = @json(array_column($tableData, 'freq'));

  let lineChart, barChart, donutChart;

  // Line Chart
  const lineCtx = document.getElementById('lineChart').getContext('2d');
  lineChart = new Chart(lineCtx, {
    type: 'line',
    data: {
      labels: urlLabels,
      datasets: [{
        label: 'Share Frequency',
        data: frequencies,
        borderColor: '#6b8bc3',
        backgroundColor: '#6b8bc340',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#6b8bc3',
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
          text: 'URL Share Frequency - Line Chart',
          font: {
            size: 16,
            weight: 'bold',
            family: 'Montserrat'
          },
          color: colors.darkTeal
        },
        tooltip: {
          callbacks: {
            title: function(context) {
              return rawUrls[context[0].dataIndex];
            }
          }
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
      labels: urlLabels,
      datasets: [{
        label: 'Share Frequency',
        data: frequencies,
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
          text: 'URL Share Frequency - Bar Chart',
          font: {
            size: 16,
            weight: 'bold',
            family: 'Montserrat'
          },
          color: colors.darkTeal
        },
        tooltip: {
          callbacks: {
            title: function(context) {
              return rawUrls[context[0].dataIndex];
            }
          }
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
              weight: '600',
              size: 10
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
      labels: urlLabels,
      datasets: [{
        data: frequencies,
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
              size: 10
            },
            padding: 15,
            usePointStyle: true
          }
        },
        title: {
          display: true,
          text: 'URL Share Distribution - Donut Chart',
          font: {
            size: 16,
            weight: 'bold',
            family: 'Montserrat'
          },
          color: colors.darkTeal
        },
        tooltip: {
          callbacks: {
            title: function(context) {
              return rawUrls[context[0].dataIndex];
            }
          }
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