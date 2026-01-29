@extends('mk.layouts.app')

@section('title', 'Publisher Stats - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Publisher Statistics</h2>
    <div class="page-subtitle">Analyze content distribution across publishers and news sources</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
  
  <!-- Main Content -->
  <div>
    
   

    <!-- Publisher Table -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Top Publishers</h3>
        <span class="item-count">{{ count($tableData) }}</span>
      </div>
      
      <div class="section-body">
        @if(count($tableData) === 0)
          <div class="empty-state">
            <div class="empty-icon">📰</div>
            <div class="empty-text">No publisher data available. Please select a project and date range.</div>
          </div>
        @else
          <div class="data-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width: 60px; text-align: center;">#</th>
                  <th>Publisher / Source</th>
                  <th style="text-align: center; width: 120px;">PageRank</th>
                  <th style="text-align: right; width: 120px;">Articles</th>
                  <th style="text-align: right; width: 100px;">%</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $total = array_sum(array_column($tableData, 'count'));
                @endphp
                @foreach($tableData as $index => $row)
                  @php
                    $percentage = $total > 0 ? round($row['count'] / $total * 100, 1) : 0;
                    $pagerank = $row['pagerank'] ?? null;
                    
                    // Check if this is a social media aggregated entry
                    $isSocialMedia = str_contains(strtolower($row['publisher']), 'posts') || 
                                    str_contains(strtolower($row['publisher']), 'twitter') ||
                                    str_contains(strtolower($row['publisher']), 'instagram');
                  @endphp
                  <tr>
                    <td style="text-align: center; font-weight: 800; color: var(--sage);">{{ $index + 1 }}</td>
                    <td style="font-weight: 700; color: var(--dark-teal);">
                      {{ $row['publisher'] }}
                      @if($isSocialMedia)
                        <span style="display: inline-block; margin-left: 8px; background: #e3f2fd; color: #1976d2; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600;">
                          AGGREGATED
                        </span>
                      @endif
                    </td>
                    <td style="text-align: center; font-weight: 700;">
                      @if($pagerank !== null)
                        <span style="background: var(--beige); color: var(--brown); padding: 6px 12px; border-radius: 8px; font-size: 12px;">
                          {{ number_format($pagerank, 2) }}
                        </span>
                      @else
                        <span style="color: var(--sage);">-</span>
                      @endif
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
                  Total Publishers
                </div>
                <div style="font-size: 24px; font-weight: 900; color: var(--dark-teal);">
                  {{ number_format(count($tableData)) }}
                </div>
              </div>
              <div>
                <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                  Total Articles
                </div>
                <div style="font-size: 24px; font-weight: 900; color: var(--dark-teal);">
                  {{ number_format($total) }}
                </div>
              </div>
              <div>
                <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                  Avg per Publisher
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
              <button class="mode-btn active" onclick="switchMode('bar')">Bar Chart</button>
              <button class="mode-btn" onclick="switchMode('donut')">Donut Chart</button>
            </div>

            <!-- Bar Chart -->
            <div class="chart-container" id="barContainer" style="height: 450px;">
              <canvas id="barChart"></canvas>
            </div>

            <!-- Donut Chart -->
            <div class="chart-container donut" id="donutContainer" style="height: 450px; display: none;">
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
        
        <!-- Additional Debug Info -->
        <div style="margin-top: 16px; padding: 12px; background: #f5f5f5; border-radius: 8px; font-size: 12px; font-family: monospace;">
          <div><strong>Media Type:</strong> {{ $params['media'] ?? 'N/A' }}</div>
          <div><strong>Date Range:</strong> {{ $params['startDate'] ?? 'N/A' }} to {{ $params['endDate'] ?? 'N/A' }}</div>
          <div><strong>Normalized Count:</strong> {{ count($tableData) }} publishers</div>
        </div>
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
      'helperText' => 'Data from /publisher_stats endpoint. Social media (Twitter, Instagram) shows aggregated posts.'
    ])
  </div>

</div>

@endsection

@section('scripts')
@if(count($tableData) > 0)
<script>
  const publishers = {!! json_encode(array_slice(array_column($tableData, 'publisher'), 0, 15)) !!};
  const counts = {!! json_encode(array_slice(array_column($tableData, 'count'), 0, 15)) !!};

  let barChart, donutChart;

  // Bar Chart (Horizontal)
  const barCtx = document.getElementById('barChart').getContext('2d');
  barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
      labels: publishers,
      datasets: [{
        label: 'Articles',
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
      indexAxis: 'y',
      plugins: {
        legend: { display: false },
        title: {
          display: true,
          text: 'Top Publishers by Article Count',
          font: { size: 16, weight: 'bold', family: 'Montserrat' },
          color: colors.darkTeal
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          grid: { color: colors.beige },
          ticks: { font: { family: 'Montserrat', weight: '600' } }
        },
        y: {
          grid: { display: false },
          ticks: { 
            font: { family: 'Montserrat', weight: '600', size: 10 }
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
      labels: publishers,
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
            font: { family: 'Montserrat', weight: '600', size: 11 },
            padding: 12,
            usePointStyle: true
          }
        },
        title: {
          display: true,
          text: 'Publisher Distribution',
          font: { size: 16, weight: 'bold', family: 'Montserrat' },
          color: colors.darkTeal
        }
      }
    }
  });

  // Mode Switching
  function switchMode(mode) {
    const barContainer = document.getElementById('barContainer');
    const donutContainer = document.getElementById('donutContainer');
    const buttons = document.querySelectorAll('.mode-btn');

    barContainer.style.display = mode === 'bar' ? 'block' : 'none';
    donutContainer.style.display = mode === 'donut' ? 'block' : 'none';

    buttons.forEach(btn => btn.classList.remove('active'));
    buttons[mode === 'bar' ? 0 : 1].classList.add('active');
  }
</script>
@endif
@endsection