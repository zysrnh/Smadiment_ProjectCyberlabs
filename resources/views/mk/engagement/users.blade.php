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
                @endphp
                @foreach($tableData as $index => $row)
                  @php
                    $percentage = $total > 0 ? round($row['count'] / $total * 100, 1) : 0;
                  @endphp
                  <tr>
                    <td style="text-align: center; font-weight: 800; color: var(--sage);">{{ $index + 1 }}</td>
                    <td style="font-weight: 700; color: var(--dark-teal);">
                      @if(str_starts_with($row['username'], '@'))
                        {{ $row['username'] }}
                      @else
                        @{{ $row['username'] }}
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

          <!-- User Activity Chart -->
          <div style="margin-top: 24px;">
            <canvas id="userActivityChart" style="max-height: 300px;"></canvas>
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
  const userLabels = @json(array_column($tableData, 'username'));
  const userData = @json(array_column($tableData, 'count'));

  const userCtx = document.getElementById('userActivityChart').getContext('2d');
  const userActivityChart = new Chart(userCtx, {
    type: 'bar',
    data: {
      labels: userLabels,
      datasets: [{
        label: 'Post Count',
        data: userData,
        backgroundColor: colors.palette,
        borderWidth: 2,
        borderColor: '#fff',
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'y', // Horizontal bar chart
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'User Activity Distribution',
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
</script>
@endif
@endsection