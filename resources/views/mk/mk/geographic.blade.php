@extends('mk.layouts.app')

@section('title', 'Geographic Data - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Geographic Distribution</h2>
    <div class="page-subtitle">Track geographic locations of social media activity</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
  
  <!-- Main Content -->
  <div>
    
    <!-- Geographic Distribution by Sentiment -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Top Locations by Sentiment</h3>
      </div>
      
      <div class="section-body">
        @if(count($geoRows) === 0)
          <div class="empty-state">
            <div class="empty-icon">🌍</div>
            <div class="empty-text">No geographic data available. Please select a project and filters.</div>
          </div>
        @else
          <div class="data-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Location</th>
                  <th style="text-align: right;">Post Count</th>
                </tr>
              </thead>
              <tbody>
                @foreach($geoRows as $index => $r)
                <tr>
                  <td style="font-weight: 800; color: var(--sage);">{{ $index + 1 }}</td>
                  <td>{{ $r['name'] }}</td>
                  <td class="count-cell">{{ number_format($r['count']) }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Bar Chart -->
          <div class="chart-container large" style="margin-top: 24px;">
            <canvas id="geoChart"></canvas>
          </div>
        @endif
      </div>
    </div>

    <!-- Geographic Distribution - All Users -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Top Locations - All Users</h3>
      </div>
      
      <div class="section-body">
        @if(count($geoUserRows) === 0)
          <div class="empty-state">
            <div class="empty-text">No user geographic data available</div>
          </div>
        @else
          <div class="data-table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Location</th>
                  <th style="text-align: right;">User Count</th>
                </tr>
              </thead>
              <tbody>
                @foreach($geoUserRows as $index => $r)
                <tr>
                  <td style="font-weight: 800; color: var(--sage);">{{ $index + 1 }}</td>
                  <td>{{ $r['name'] }}</td>
                  <td class="count-cell">{{ number_format($r['count']) }}</td>
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
          <summary class="debug-toggle">View Geo Sentiment Raw Data</summary>
          <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($geoRawData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </details>
        
        <details style="margin-top: 12px;">
          <summary class="debug-toggle">View Geo Users Raw Data</summary>
          <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($geoUserRawData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
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
      'showSentiment' => true,
      'helperText' => 'Data from /get_geo_twitter_user_sentiment & /get_geo_twitter_user'
    ])
  </div>

</div>

@endsection

@section('scripts')
<script>
  @if(count($geoRows) > 0)
  // Geographic Bar Chart
  const labels = @json(array_column($geoRows, 'name'));
  const data = @json(array_column($geoRows, 'count'));
  
  const ctx = document.getElementById('geoChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Post Count',
        data: data,
        backgroundColor: colors.sage,
        borderColor: colors.brown,
        borderWidth: 2,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'y',
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Geographic Distribution - Top 10 Locations',
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
  @endif
</script>
@endsection