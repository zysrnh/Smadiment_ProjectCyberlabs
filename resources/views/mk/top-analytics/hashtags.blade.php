@extends('mk.layouts.app')

@section('title', 'Top Hashtags - SMADIMENT')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
    }

    .stat-card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-4px);
    }

    .stat-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .stat-value {
        font-size: 36px;
        font-weight: 800;
        color: var(--primary-green);
        line-height: 1;
    }

    .chart-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 28px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
        margin-bottom: 24px;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--border-color);
    }

    .table-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 28px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 14px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 12px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-color);
    }

    .data-table td {
        padding: 16px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    .data-table tr:hover {
        background: var(--sidebar-hover);
    }

    .hashtag-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        color: #ffffff;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    .loading-spinner {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 4px solid var(--border-color);
        border-top-color: var(--primary-green);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 400px;
        flex-direction: column;
        gap: 16px;
    }

    .loading-text {
        color: var(--text-secondary);
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="top-bar">
    <div class="page-title">
        <h2>🏷️ Top Hashtags</h2>
        <p class="page-subtitle">Most popular hashtags across social media platforms</p>
    </div>
    <div class="top-actions">
        <input type="date" id="startDate" value="{{ $startDate }}" class="action-btn">
        <input type="date" id="endDate" value="{{ $endDate }}" class="action-btn">
        <button onclick="refreshData()" class="action-btn primary">🔄 Refresh</button>
    </div>
</div>

<div class="content-wrapper">
    <!-- Stats Cards -->
    <div class="stats-grid" id="statsContainer">
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <div class="loading-text">Loading hashtags data...</div>
        </div>
    </div>

    <!-- Chart -->
    <div class="chart-card" id="chartContainer" style="display: none;">
        <h3 class="chart-title">📊 Top 10 Hashtags</h3>
        <canvas id="hashtagsChart" height="100"></canvas>
    </div>

    <!-- Table -->
    <div class="table-card" id="tableContainer" style="display: none;">
        <h3 class="chart-title">📋 All Hashtags</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hashtag</th>
                    <th>Count</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody id="hashtagsTableBody">
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const projectId = '{{ $projectId ?? '' }}';
    const startDate = '{{ $startDate }}';
    const endDate = '{{ $endDate }}';
    const media = '{{ $media ?? 'all' }}';

    let hashtagsChart = null;

    async function loadHashtagsData() {
        if (!projectId) {
            document.getElementById('statsContainer').innerHTML = `
                <div class="stat-card">
                    <div class="stat-label">⚠️ No Project Selected</div>
                    <p style="color: var(--text-secondary);">Please select a project to view data.</p>
                </div>
            `;
            return;
        }

        try {
            const response = await fetch(
                `/mk/api/top-hashtags?project_id=${projectId}&start_date=${startDate}&end_date=${endDate}&media=${media}`
            );
            
            const result = await response.json();

            if (result.success && result.data) {
                renderStats(result.data);
                renderChart(result.data.chartData);
                renderTable(result.data.hashtags);
            } else {
                throw new Error(result.message || 'Failed to load data');
            }
        } catch (error) {
            console.error('Error loading hashtags data:', error);
            document.getElementById('statsContainer').innerHTML = `
                <div class="stat-card">
                    <div class="stat-label">❌ Error</div>
                    <p style="color: var(--text-secondary);">${error.message}</p>
                </div>
            `;
        }
    }

    function renderStats(data) {
        const total = data.total || 0;
        const topHashtag = data.hashtags && data.hashtags[0] ? data.hashtags[0] : null;

        document.getElementById('statsContainer').innerHTML = `
            <div class="stat-card">
                <div class="stat-label">Total Hashtags</div>
                <div class="stat-value">${total.toLocaleString()}</div>
            </div>
            ${topHashtag ? `
            <div class="stat-card">
                <div class="stat-label">Top Hashtag</div>
                <div class="stat-value" style="font-size: 24px;">${topHashtag.hashtag || topHashtag.tag || topHashtag.name}</div>
                <p style="color: var(--text-secondary); margin-top: 8px;">${(topHashtag.count || 0).toLocaleString()} mentions</p>
            </div>
            ` : ''}
        `;
    }

    function renderChart(chartData) {
        if (!chartData || !chartData.labels || chartData.labels.length === 0) {
            document.getElementById('chartContainer').style.display = 'none';
            return;
        }

        document.getElementById('chartContainer').style.display = 'block';

        const ctx = document.getElementById('hashtagsChart').getContext('2d');

        if (hashtagsChart) {
            hashtagsChart.destroy();
        }

        hashtagsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Mentions',
                    data: chartData.values,
                    backgroundColor: colors.primaryGreen,
                    borderColor: colors.primaryGreenDark,
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: colors.borderColor
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function renderTable(hashtags) {
        if (!hashtags || hashtags.length === 0) {
            document.getElementById('tableContainer').style.display = 'none';
            return;
        }

        document.getElementById('tableContainer').style.display = 'block';

        const tbody = document.getElementById('hashtagsTableBody');
        tbody.innerHTML = hashtags.map((item, index) => `
            <tr>
                <td><strong>#${index + 1}</strong></td>
                <td>
                    <span class="hashtag-tag">
                        #${item.hashtag || item.tag || item.name || 'Unknown'}
                    </span>
                </td>
                <td><strong>${(item.count || item.frequency || 0).toLocaleString()}</strong></td>
                <td>📈 Trending</td>
            </tr>
        `).join('');
    }

    function refreshData() {
        const newStartDate = document.getElementById('startDate').value;
        const newEndDate = document.getElementById('endDate').value;
        
        const url = new URL(window.location.href);
        url.searchParams.set('start_date', newStartDate);
        url.searchParams.set('end_date', newEndDate);
        
        window.location.href = url.toString();
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', loadHashtagsData);
</script>
@endsection