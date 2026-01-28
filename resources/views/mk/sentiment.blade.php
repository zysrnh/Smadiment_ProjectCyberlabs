@extends('mk.layouts.app')

@section('title', 'Sentiment Analysis - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Sentiment Analysis</h2>
    <div class="page-subtitle">Analyze sentiment distribution across your projects</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
  
  <!-- Main Content -->
  <div>
    
    <!-- Sentiment Distribution Stats -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Sentiment Distribution</h3>
      </div>
      
      <div class="section-body">
        @php
          $pos = $sentimentData['positive'] ?? 0;
          $neu = $sentimentData['neutral'] ?? 0;
          $neg = $sentimentData['negative'] ?? 0;
          $total = max(1, $pos + $neu + $neg);
          $posP = round($pos/$total*100);
          $neuP = round($neu/$total*100);
          $negP = round($neg/$total*100);
          $hasData = $total > 1;
        @endphp

        @if(!$hasData)
          <div class="empty-state">
            <div class="empty-icon">📊</div>
            <div class="empty-text">No sentiment data available. Please select a project and date range.</div>
          </div>
        @else
          <div class="stats-container">
            <div class="stat-card">
              <div class="stat-header">
                <span class="stat-name">Positive</span>
                <span class="stat-percentage">{{ $posP }}%</span>
              </div>
              <div class="stat-number">{{ number_format($pos) }}</div>
              <div class="progress-bar">
                <div class="progress-fill progress-positive" style="width: {{ $posP }}%"></div>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-header">
                <span class="stat-name">Neutral</span>
                <span class="stat-percentage">{{ $neuP }}%</span>
              </div>
              <div class="stat-number">{{ number_format($neu) }}</div>
              <div class="progress-bar">
                <div class="progress-fill progress-neutral" style="width: {{ $neuP }}%"></div>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-header">
                <span class="stat-name">Negative</span>
                <span class="stat-percentage">{{ $negP }}%</span>
              </div>
              <div class="stat-number">{{ number_format($neg) }}</div>
              <div class="progress-bar">
                <div class="progress-fill progress-negative" style="width: {{ $negP }}%"></div>
              </div>
            </div>
          </div>

          <!-- Donut Chart -->
          <div class="chart-container donut">
            <canvas id="sentimentChart"></canvas>
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
      'helperText' => 'Data from /sentiment_total endpoint'
    ])
  </div>

</div>

@endsection

@section('scripts')
<script>
  @if($hasData)
  // Sentiment Donut Chart
  const ctx = document.getElementById('sentimentChart').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Positive', 'Neutral', 'Negative'],
      datasets: [{
        data: [{{ $pos }}, {{ $neu }}, {{ $neg }}],
        backgroundColor: [
          colors.sage,
          '#A8C5E8',
          colors.brown
        ],
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
              size: 14
            },
            padding: 20,
            usePointStyle: true
          }
        },
        title: {
          display: true,
          text: 'Sentiment Distribution Overview',
          font: {
            size: 18,
            weight: 'bold',
            family: 'Montserrat'
          },
          color: colors.darkTeal,
          padding: 20
        }
      }
    }
  });
  @endif
</script>
@endsection