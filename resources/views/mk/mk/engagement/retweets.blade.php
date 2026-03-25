@extends('mk.layouts.app')

@section('title', 'Most Retweets - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Most Retweeted Content</h2>
    <div class="page-subtitle">Analyze most retweeted posts and viral content distribution</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
  
  <!-- Main Content -->
  <div>
    
    <!-- Retweets Table -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Top Retweeted Posts (Top 10)</h3>
      </div>
      
      <div class="section-body">
        @if(count($tableData) === 0)
          <div class="empty-state">
            <div class="empty-icon">🔄</div>
            <div class="empty-text">No retweet data available. Please select a project.</div>
          </div>
        @else
          <div class="data-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width: 60px; text-align: center;">#</th>
                  <th style="width: 180px;">Author</th>
                  <th>Content Preview</th>
                  <th style="text-align: right; width: 120px;">Retweets</th>
                  <th style="text-align: right; width: 100px;">%</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $total = array_sum(array_column($tableData, 'retweet_count'));
                @endphp
                @foreach($tableData as $index => $row)
                  @php
                    $percentage = $total > 0 ? round($row['retweet_count'] / $total * 100, 1) : 0;
                    
                    // Safety: Handle author as array or string
                    $authorDisplay = is_array($row['author']) ? ($row['author'][0] ?? 'Unknown') : $row['author'];
                    
                    // Safety: Handle content as array or string
                    $contentDisplay = is_array($row['content']) ? ($row['content'][0] ?? 'No content') : $row['content'];
                    $contentTitle = is_array($row['content']) ? implode(' ', $row['content']) : $row['content'];
                  @endphp
                  <tr>
                    <td style="text-align: center; font-weight: 800; color: var(--sage);">{{ $index + 1 }}</td>
                    <td style="font-weight: 700; color: var(--dark-teal);">
                      {{ $authorDisplay }}
                    </td>
                    <td style="font-size: 13px; color: var(--dark-teal); line-height: 1.4;" title="{{ $contentTitle }}">
                      {{ Str::limit($contentDisplay, 150) }}
                    </td>
                    <td class="count-cell">{{ number_format($row['retweet_count']) }}</td>
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
                  Total Posts
                </div>
                <div style="font-size: 24px; font-weight: 900; color: var(--dark-teal);">
                  {{ number_format(count($tableData)) }}
                </div>
              </div>
              <div>
                <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                  Total Retweets
                </div>
                <div style="font-size: 24px; font-weight: 900; color: var(--dark-teal);">
                  {{ number_format($total) }}
                </div>
              </div>
              <div>
                <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                  Avg per Post
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
            <div class="chart-container" id="barContainer" style="height: 400px;">
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
      'helperText' => 'Data from /most_retweets endpoint'
    ])
  </div>

</div>

@endsection

@section('scripts')
@if(count($tableData) > 0)
<script>
  const authors = @json(array_column($tableData, 'author'));
  const retweets = @json(array_column($tableData, 'retweet_count'));

  let barChart, donutChart;

  // Bar Chart (Horizontal)
  const barCtx = document.getElementById('barChart').getContext('2d');
  barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
      labels: authors,
      datasets: [{
        label: 'Retweets',
        data: retweets,
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
          text: 'Most Retweeted Posts - Bar Chart',
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
          ticks: { font: { family: 'Montserrat', weight: '600' } }
        }
      }
    }
  });

  // Donut Chart
  const donutCtx = document.getElementById('donutChart').getContext('2d');
  donutChart = new Chart(donutCtx, {
    type: 'doughnut',
    data: {
      labels: authors,
      datasets: [{
        data: retweets,
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
            font: { family: 'Montserrat', weight: '600', size: 12 },
            padding: 15,
            usePointStyle: true
          }
        },
        title: {
          display: true,
          text: 'Retweet Distribution - Donut Chart',
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