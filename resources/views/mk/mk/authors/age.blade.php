@extends('mk.layouts.app')

@section('title', 'Age Distribution - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Authors Age Distribution</h2>
    <div class="page-subtitle">Analyze age demographics of social media authors</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
  
  <!-- Main Content -->
  <div>
    
    <!-- Age Distribution Chart -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Age Groups Analysis</h3>
      </div>
      
      <div class="section-body">
        @php
          $hasData = !empty($chartData['labels']) && !empty($chartData['values']);
        @endphp

        @if(!$hasData)
          <div class="empty-state">
            <div style="width: 80px; height: 80px; background: var(--light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
              <svg style="width: 40px; height: 40px; stroke: var(--dark-blue); fill: none; stroke-width: 2; opacity: 0.3;" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
              </svg>
            </div>
            <div class="empty-text">No age distribution data available. Please select a project.</div>
          </div>
        @else
          <!-- Mode Toggle -->
          <div class="mode-toggle">
            <button class="mode-btn active" onclick="switchMode('line')">Line Chart</button>
            <button class="mode-btn" onclick="switchMode('bar')">Bar Chart</button>
            <button class="mode-btn" onclick="switchMode('donut')">Donut Chart</button>
          </div>

          <!-- Line Chart -->
          <div class="chart-container" id="lineContainer">
            <canvas id="lineChart"></canvas>
          </div>

          <!-- Bar Chart -->
          <div class="chart-container" id="barContainer" style="display:none;">
            <canvas id="barChart"></canvas>
          </div>

          <!-- Donut Chart -->
          <div class="chart-container donut" id="donutContainer" style="display:none;">
            <canvas id="donutChart"></canvas>
          </div>

          <!-- Summary Table -->
          <div class="data-table-wrapper" style="margin-top: 24px;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Age Group</th>
                  <th style="text-align: right;">Post Frequency</th>
                  <th style="text-align: right;">Percentage</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $total = array_sum($chartData['values']);
                @endphp
                @foreach($chartData['labels'] as $index => $label)
                  @php
                    $value = $chartData['values'][$index];
                    $percentage = $total > 0 ? round($value / $total * 100, 1) : 0;
                  @endphp
                  <tr>
                    <td style="font-weight: 700;">{{ $label }}</td>
                    <td class="count-cell">{{ number_format($value) }}</td>
                    <td class="count-cell">{{ $percentage }}%</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
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
      'showMedia' => true,
      'showSentiment' => false,
      'helperText' => 'Data from /authors_age endpoint'
    ])
  </div>

</div>

@endsection

@section('scripts')
<script>
  @if($hasData)
  const labels = @json($chartData['labels']);
  const data = @json($chartData['values']);

  let lineChart, barChart, donutChart;

  // Line Chart - FIXED: fill: false to prevent black area
  const lineCtx = document.getElementById('lineChart').getContext('2d');
  lineChart = new Chart(lineCtx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Post Frequency',
        data: data,
        borderColor: colors.primaryGreen,
        backgroundColor: colors.primaryGreen,
        borderWidth: 3,
        fill: false, // IMPORTANT: Set to false to prevent black fill
        tension: 0.4,
        pointBackgroundColor: colors.primaryGreen,
        pointBorderColor: colors.white,
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
          text: 'Age Distribution - Line Chart',
          font: {
            size: 16,
            weight: 'bold',
            family: 'Poppins'
          },
          color: colors.darkBlue
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: colors.lightGray
          },
          ticks: {
            font: {
              family: 'Poppins',
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
              family: 'Poppins',
              weight: '600'
            }
          }
        }
      }
    }
  });

  // Bar Chart
  const barCtx = document.getElementById('barChart').getContext('2d');
  barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Post Frequency',
        data: data,
        backgroundColor: colors.palette,
        borderWidth: 2,
        borderColor: colors.white,
        borderRadius: 8
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
          text: 'Age Distribution - Bar Chart',
          font: {
            size: 16,
            weight: 'bold',
            family: 'Poppins'
          },
          color: colors.darkBlue
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: colors.lightGray
          },
          ticks: {
            font: {
              family: 'Poppins',
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
              family: 'Poppins',
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
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: colors.palette,
        borderWidth: 3,
        borderColor: colors.white
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
              family: 'Poppins',
              weight: '600',
              size: 12
            },
            padding: 15,
            usePointStyle: true
          }
        },
        title: {
          display: true,
          text: 'Age Distribution - Donut Chart',
          font: {
            size: 16,
            weight: 'bold',
            family: 'Poppins'
          },
          color: colors.darkBlue
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
  @endif
</script>
@endsection