{{--
  Chart Section Component
  
  Usage:
  @include('mk.components.chart-section', [
    'title' => 'Age Groups Analysis',
    'emptyIcon' => '👥',
    'emptyText' => 'No age distribution data available',
    'chartData' => $chartData, // ['labels' => [...], 'values' => [...]]
    'rawData' => $rawData,
    'tableLabel' => 'Age Group', // Label for first column in table
    'chartId' => 'age', // unique ID for this chart instance
    'chartColors' => [ // Optional custom colors
      'line' => '#31487A',
      'fill' => '#8FB3E240'
    ]
  ])
--}}

@php
  $hasData = !empty($chartData['labels']) && !empty($chartData['values']);
  $chartId = $chartId ?? 'chart';
  $tableLabel = $tableLabel ?? 'Category';
  $emptyIcon = $emptyIcon ?? '📊';
  $emptyText = $emptyText ?? 'No data available. Please select a project.';
  
  // Default colors
  $lineColor = $chartColors['line'] ?? 'colors.brown';
  $fillColor = $chartColors['fill'] ?? 'colors.sage + \'40\'';
@endphp

<div class="section">
  <div class="section-header">
    <h3 class="section-title">{{ $title }}</h3>
  </div>
  
  <div class="section-body">
    @if(!$hasData)
      <div class="empty-state">
        <div class="empty-icon">{{ $emptyIcon }}</div>
        <div class="empty-text">{{ $emptyText }}</div>
      </div>
    @else
      <!-- Mode Toggle -->
      <div class="mode-toggle">
        <button class="mode-btn active" onclick="switchMode{{ ucfirst($chartId) }}('line')">Line Chart</button>
        <button class="mode-btn" onclick="switchMode{{ ucfirst($chartId) }}('bar')">Bar Chart</button>
        <button class="mode-btn" onclick="switchMode{{ ucfirst($chartId) }}('donut')">Donut Chart</button>
      </div>

      <!-- Line Chart -->
      <div class="chart-container" id="{{ $chartId }}LineContainer">
        <canvas id="{{ $chartId }}LineChart"></canvas>
      </div>

      <!-- Bar Chart -->
      <div class="chart-container" id="{{ $chartId }}BarContainer" style="display:none;">
        <canvas id="{{ $chartId }}BarChart"></canvas>
      </div>

      <!-- Donut Chart -->
      <div class="chart-container donut" id="{{ $chartId }}DonutContainer" style="display:none;">
        <canvas id="{{ $chartId }}DonutChart"></canvas>
      </div>

      <!-- Summary Table -->
      <div class="data-table-wrapper" style="margin-top: 24px;">
        <table class="data-table">
          <thead>
            <tr>
              <th>{{ $tableLabel }}</th>
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

@if($hasData)
@push('scripts')
<script>
  (function() {
    const labels = @json($chartData['labels']);
    const data = @json($chartData['values']);
    const chartId = '{{ $chartId }}';
    
    let lineChart, barChart, donutChart;

    // Line Chart
    const lineCtx = document.getElementById(chartId + 'LineChart').getContext('2d');
    lineChart = new Chart(lineCtx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Post Frequency',
          data: data,
          borderColor: {!! $lineColor !!},
          backgroundColor: {!! $fillColor !!},
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: {!! $lineColor !!},
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
            text: '{{ $title }} - Line Chart',
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
              }
            }
          }
        }
      }
    });

    // Bar Chart
    const barCtx = document.getElementById(chartId + 'BarChart').getContext('2d');
    barChart = new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Post Frequency',
          data: data,
          backgroundColor: colors.palette,
          borderWidth: 2,
          borderColor: '#fff',
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
            text: '{{ $title }} - Bar Chart',
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
              }
            }
          }
        }
      }
    });

    // Donut Chart
    const donutCtx = document.getElementById(chartId + 'DonutChart').getContext('2d');
    donutChart = new Chart(donutCtx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
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
            text: '{{ $title }} - Donut Chart',
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

    // Mode Switching Function
    window['switchMode{{ ucfirst($chartId) }}'] = function(mode) {
      const lineContainer = document.getElementById(chartId + 'LineContainer');
      const barContainer = document.getElementById(chartId + 'BarContainer');
      const donutContainer = document.getElementById(chartId + 'DonutContainer');
      const section = lineContainer.closest('.section-body');
      const buttons = section.querySelectorAll('.mode-btn');

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
    };
  })();
</script>
@endpush
@endif