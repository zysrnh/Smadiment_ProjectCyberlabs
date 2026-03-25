@extends('mk.layouts.app')

@section('title', 'Categories - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Category Analysis</h2>
    <div class="page-subtitle">Analyze content distribution across categories with sentiment breakdown</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
  
  <!-- Main Content -->
  <div>
    
    <!-- Categories Section -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Category Groups</h3>
      </div>
      
      <div class="section-body">
        @php
          // Handle different response structures
          $categoryGroups = [];
          
          if (is_array($rawData)) {
            // Direct array response
            if (isset($rawData[0]) && is_array($rawData[0])) {
              $categoryGroups = $rawData;
            }
            // Wrapped in 'data' key
            elseif (isset($rawData['data']) && is_array($rawData['data'])) {
              $categoryGroups = $rawData['data'];
            }
            // Other wrapped structures
            elseif (isset($rawData['categories']) && is_array($rawData['categories'])) {
              $categoryGroups = $rawData['categories'];
            }
          }
          
          $hasData = !empty($categoryGroups) && is_array($categoryGroups) && count($categoryGroups) > 0;
        @endphp

        @if(!$hasData)
          <div class="empty-state">
            <div class="empty-icon">🏷️</div>
            <div class="empty-text">No category data available. Please select a project and date range.</div>
            @if(!empty($rawData))
              <div style="margin-top: 16px; padding: 12px; background: var(--beige); border-radius: 8px; font-size: 12px; color: var(--brown); font-weight: 600;">
                API returned data but structure is unexpected. Check debug panel below.
              </div>
            @endif
          </div>
        @else
          <!-- Categories Accordion -->
          <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($categoryGroups as $groupIndex => $group)
              @php
                $groupLabel = $group['label'] ?? 'Unnamed Group';
                $groupTotal = $group['total'] ?? 0;
                $groupId = $group['id'] ?? $groupIndex;
                $categories = $group['categories'] ?? [];
              @endphp

              <div class="category-group" style="background: var(--white); border: 2px solid var(--beige); border-radius: 16px; overflow: hidden; transition: all 0.3s;">
                
                <!-- Group Header -->
                <div class="group-header" onclick="toggleGroup({{ $groupIndex }})" style="padding: 20px 24px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, var(--cream) 0%, var(--white) 100%); transition: all 0.2s;">
                  <div style="display: flex; align-items: center; gap: 16px;">
                    <div class="expand-icon" id="icon-{{ $groupIndex }}" style="width: 32px; height: 32px; background: var(--brown); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 18px; transition: transform 0.3s;">
                      +
                    </div>
                    <div>
                      <h4 style="font-size: 16px; font-weight: 800; color: var(--dark-teal); margin-bottom: 4px;">
                        {{ $groupLabel }}
                      </h4>
                      <div style="font-size: 13px; color: var(--sage); font-weight: 600;">
                        {{ count($categories) }} categories • {{ number_format($groupTotal) }} total mentions
                      </div>
                    </div>
                  </div>
                  <div style="background: var(--brown); color: white; padding: 8px 20px; border-radius: 10px; font-size: 18px; font-weight: 900;">
                    {{ number_format($groupTotal) }}
                  </div>
                </div>

                <!-- Group Content (Hidden by default) -->
                <div id="group-{{ $groupIndex }}" class="group-content" style="display: none; padding: 24px; background: var(--white);">
                  
                  <!-- Categories Table -->
                  <div class="data-table-wrapper">
                    <table class="data-table">
                      <thead>
                        <tr>
                          <th style="width: 60px; text-align: center;">#</th>
                          <th>Category</th>
                          <th style="text-align: center; width: 100px;">😊 Pos</th>
                          <th style="text-align: center; width: 100px;">😐 Neu</th>
                          <th style="text-align: center; width: 100px;">😞 Neg</th>
                          <th style="text-align: right; width: 120px;">Total</th>
                          <th style="text-align: right; width: 100px;">%</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($categories as $catIndex => $cat)
                          @php
                            $label = $cat['label'] ?? 'Unknown';
                            $total = $cat['total'] ?? 0;
                            $sentiment = $cat['sentiment'] ?? [];
                            
                            // Handle sentiment sebagai array atau object
                            $positive = 0;
                            $neutral = 0;
                            $negative = 0;
                            
                            if (is_array($sentiment)) {
                              $positive = (int) ($sentiment['1'] ?? 0);
                              $neutral = (int) ($sentiment['0'] ?? 0);
                              $negative = (int) ($sentiment['2'] ?? $sentiment['-1'] ?? 0);
                            }
                            
                            $percentage = $groupTotal > 0 ? round($total / $groupTotal * 100, 1) : 0;
                          @endphp
                          <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--sage);">
                              {{ $catIndex + 1 }}
                            </td>
                            <td style="font-weight: 700; color: var(--dark-teal);">
                              {{ $label }}
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #10b981;">
                              {{ $positive > 0 ? number_format($positive) : '-' }}
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #6b7280;">
                              {{ $neutral > 0 ? number_format($neutral) : '-' }}
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #ef4444;">
                              {{ $negative > 0 ? number_format($negative) : '-' }}
                            </td>
                            <td class="count-cell">{{ number_format($total) }}</td>
                            <td class="count-cell">{{ $percentage }}%</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>

                  <!-- Group Chart -->
                  <div style="margin-top: 24px;">
                    <div class="mode-toggle">
                      <button class="mode-btn active" onclick="switchGroupMode({{ $groupIndex }}, 'bar')">Bar Chart</button>
                      <button class="mode-btn" onclick="switchGroupMode({{ $groupIndex }}, 'donut')">Donut Chart</button>
                    </div>

                    <!-- Bar Chart -->
                    <div class="chart-container" id="barContainer-{{ $groupIndex }}" style="height: 350px;">
                      <canvas id="barChart-{{ $groupIndex }}"></canvas>
                    </div>

                    <!-- Donut Chart -->
                    <div class="chart-container donut" id="donutContainer-{{ $groupIndex }}" style="height: 350px; display: none;">
                      <canvas id="donutChart-{{ $groupIndex }}"></canvas>
                    </div>
                  </div>

                </div>
              </div>
            @endforeach
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
      'helperText' => 'Data from /categories endpoint'
    ])
  </div>

</div>

@endsection

@section('scripts')
@if($hasData)
<script>
  const categoryGroups = @json($categoryGroups);
  const charts = {};

  // Initialize charts for each group
  categoryGroups.forEach((group, groupIndex) => {
    const categories = group.categories || [];
    const labels = categories.map(c => c.label || 'Unknown');
    const values = categories.map(c => c.total || 0);

    // Bar Chart
    const barCtx = document.getElementById(`barChart-${groupIndex}`)?.getContext('2d');
    if (barCtx) {
      charts[`bar-${groupIndex}`] = new Chart(barCtx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Total Mentions',
            data: values,
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
            legend: { display: false },
            title: {
              display: true,
              text: `${group.label} - Distribution`,
              font: { size: 16, weight: 'bold', family: 'Montserrat' },
              color: colors.darkTeal
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: colors.beige },
              ticks: { font: { family: 'Montserrat', weight: '600' } }
            },
            x: {
              grid: { display: false },
              ticks: {
                font: { family: 'Montserrat', weight: '600', size: 10 },
                maxRotation: 45,
                minRotation: 45
              }
            }
          }
        }
      });
    }

    // Donut Chart
    const donutCtx = document.getElementById(`donutChart-${groupIndex}`)?.getContext('2d');
    if (donutCtx) {
      charts[`donut-${groupIndex}`] = new Chart(donutCtx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: values,
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
              text: `${group.label} - Distribution`,
              font: { size: 16, weight: 'bold', family: 'Montserrat' },
              color: colors.darkTeal
            }
          }
        }
      });
    }
  });

  // Toggle group expand/collapse
  function toggleGroup(index) {
    const content = document.getElementById(`group-${index}`);
    const icon = document.getElementById(`icon-${index}`);
    const header = content.closest('.category-group').querySelector('.group-header');
    
    if (content.style.display === 'none') {
      content.style.display = 'block';
      icon.textContent = '−';
      icon.style.transform = 'rotate(180deg)';
      header.style.background = 'var(--beige)';
    } else {
      content.style.display = 'none';
      icon.textContent = '+';
      icon.style.transform = 'rotate(0deg)';
      header.style.background = 'linear-gradient(135deg, var(--cream) 0%, var(--white) 100%)';
    }
  }

  // Switch chart mode for a group
  function switchGroupMode(groupIndex, mode) {
    const barContainer = document.getElementById(`barContainer-${groupIndex}`);
    const donutContainer = document.getElementById(`donutContainer-${groupIndex}`);
    const buttons = document.querySelectorAll(`#group-${groupIndex} .mode-btn`);

    barContainer.style.display = mode === 'bar' ? 'block' : 'none';
    donutContainer.style.display = mode === 'donut' ? 'block' : 'none';

    buttons.forEach(btn => btn.classList.remove('active'));
    buttons[mode === 'bar' ? 0 : 1].classList.add('active');
  }

  // Group header hover effect
  document.querySelectorAll('.group-header').forEach(header => {
    header.addEventListener('mouseenter', function() {
      if (this.nextElementSibling.style.display !== 'block') {
        this.style.background = 'var(--beige)';
      }
    });
    
    header.addEventListener('mouseleave', function() {
      if (this.nextElementSibling.style.display !== 'block') {
        this.style.background = 'linear-gradient(135deg, var(--cream) 0%, var(--white) 100%)';
      }
    });
  });
</script>
@endif
@endsection