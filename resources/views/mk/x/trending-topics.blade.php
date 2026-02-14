@extends('mk.layouts.app')

@section('title', 'X Trending Topics - SMADIMENT')

@section('styles')
<style>
    :root {
        --primary-green: #038047;
        --primary-green-dark: #026738;
        --accent-blue: #2FC6F6;
        --text-primary: #1a202c;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --bg-white: #ffffff;
        --bg-gray-50: #f8fafc;
        --bg-gray-100: #f1f5f9;
        --border-gray: #e2e8f0;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    body { background: var(--bg-gray-50); }

    .dashboard-container {
        padding: 24px;
        max-width: 1600px;
        margin: 0 auto;
    }

    .page-header { margin-bottom: 32px; }
    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 8px 0;
    }
    .page-header p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    /* Filter Card */
    .filter-card {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-gray);
    }

    .filter-content {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
    }

    .date-range-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .date-input-group {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: var(--bg-gray-50);
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        transition: all 0.2s;
    }

    .date-input-group:focus-within {
        border-color: var(--primary-green);
        background: var(--bg-white);
        box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
    }

    .date-input-group svg {
        width: 18px;
        height: 18px;
        color: var(--text-secondary);
        stroke: currentColor;
        fill: none;
        stroke-width: 2;
    }

    .date-separator {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 14px;
    }

    .date-input {
        border: none;
        background: transparent;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        outline: none;
        min-width: 140px;
    }

    .location-select {
        padding: 10px 16px;
        background: var(--bg-gray-50);
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        outline: none;
        cursor: pointer;
        transition: all 0.2s;
        min-width: 180px;
    }

    .location-select:focus {
        border-color: var(--primary-green);
        background: var(--bg-white);
        box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
    }

    .apply-btn {
        padding: 12px 28px;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }

    .apply-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3);
    }

    .apply-btn svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        fill: none;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-green);
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }

    /* Table Container */
    .table-container {
        background: var(--bg-white);
        border-radius: 12px;
        border: 1px solid var(--border-gray);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    /* Ultra Clean Table */
    .status-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Poppins', sans-serif;
    }

    .status-table thead {
        background: var(--bg-white);
    }

    .status-table th {
        padding: 16px 24px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border-bottom: 2px solid var(--bg-gray-100);
        white-space: nowrap;
    }

    .status-table th.text-center { text-align: center; }
    .status-table th.text-right { text-align: right; }

    .status-table tbody tr {
        border-bottom: 1px solid var(--bg-gray-100);
        transition: background 0.15s;
    }

    .status-table tbody tr:hover {
        background: var(--bg-gray-50);
    }

    .status-table tbody tr:last-child {
        border-bottom: none;
    }

    .status-table td {
        padding: 18px 24px;
        font-size: 14px;
        color: var(--text-primary);
        vertical-align: middle;
    }

    /* Rank Column */
    .rank-cell {
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 14px;
        width: 60px;
    }

    /* Topic Name Column */
    .topic-cell {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 14px;
    }

    /* Stats Columns */
    .appearances-cell,
    .rank-avg-cell {
        font-weight: 500;
        color: var(--text-secondary);
        font-size: 14px;
        text-align: center;
    }

    /* Action Column */
    .action-cell {
        text-align: center;
        width: 100px;
    }

    .btn-view {
        padding: 6px 14px;
        background: transparent;
        border: 1px solid var(--border-gray);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-view:hover {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
    }

    .btn-view svg {
        width: 12px;
        height: 12px;
        stroke: currentColor;
        fill: none;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        padding: 20px;
        background: var(--bg-white);
        border-top: 1px solid var(--border-gray);
    }

    .pagination-btn {
        padding: 8px 16px;
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
    }

    .pagination-btn:hover:not(:disabled) {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
    }

    .pagination-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .pagination-info {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .pagination-info strong {
        color: var(--text-primary);
        font-weight: 700;
    }

    /* Skeleton Loading */
    .skeleton-line {
        height: 16px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: var(--bg-white);
        border-radius: 16px;
        border: 1px solid var(--border-gray);
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        color: var(--text-secondary);
        margin-bottom: 16px;
        stroke: currentColor;
        fill: none;
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 8px 0;
    }

    .empty-state p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 16px;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .filter-content {
            flex-direction: column;
            align-items: stretch;
        }
        .date-range-wrapper {
            flex-direction: column;
        }
        .location-select {
            width: 100%;
        }
        .apply-btn {
            width: 100%;
            justify-content: center;
        }
        
        /* Mobile Table Adjustments */
        .status-table th,
        .status-table td {
            padding: 12px 10px;
            font-size: 12px;
        }
        
        .topic-hashtag {
            font-size: 13px;
            padding: 4px 8px;
        }
        
        .stat-value {
            font-size: 14px;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <h1>X Trending Topics</h1>
        <p>Real-time trending topics on X (Twitter) in {{ $location }}</p>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.x.trending-topics') }}">
            <div class="filter-content">
                <div class="filter-label">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Date Range
                </div>
                <div class="date-range-wrapper">
                    <div class="date-input-group">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <input type="date" name="start_date" class="date-input" 
                               value="{{ $startDate }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                    <span class="date-separator">to</span>
                    <div class="date-input-group">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <input type="date" name="end_date" class="date-input" 
                               value="{{ $endDate }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <select name="location" class="location-select">
                    <option value="Indonesia" {{ $location === 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                    <option value="Worldwide" {{ $location === 'Worldwide' ? 'selected' : '' }}>Worldwide</option>
                    <option value="United States" {{ $location === 'United States' ? 'selected' : '' }}>United States</option>
                    <option value="United Kingdom" {{ $location === 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                </select>
                <button type="submit" class="apply-btn">
                    <svg viewBox="0 0 24 24">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Periods</div>
            <div id="totalPeriods" class="stat-value">
                <div class="skeleton-line" style="width:60%;"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Unique Topics</div>
            <div id="uniqueTopics" class="stat-value">
                <div class="skeleton-line" style="width:60%;"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Topics/Period</div>
            <div id="avgTopics" class="stat-value">
                <div class="skeleton-line" style="width:60%;"></div>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-wrapper">
            <!-- Loading State -->
            <table class="status-table" id="loadingTable" style="display: table;">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Topic Name</th>
                        <th class="text-center" style="width: 140px;">Appearances</th>
                        <th class="text-center" style="width: 120px;">Avg Rank</th>
                        <th class="text-center" style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" style="padding: 30px 24px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div class="skeleton-line" style="width: 30px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 280px; height: 16px;"></div>
                                <div style="flex: 1;"></div>
                                <div class="skeleton-line" style="width: 80px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 70px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 60px; height: 28px; border-radius: 6px;"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding: 30px 24px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div class="skeleton-line" style="width: 30px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 240px; height: 16px;"></div>
                                <div style="flex: 1;"></div>
                                <div class="skeleton-line" style="width: 80px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 70px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 60px; height: 28px; border-radius: 6px;"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding: 30px 24px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div class="skeleton-line" style="width: 30px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 260px; height: 16px;"></div>
                                <div style="flex: 1;"></div>
                                <div class="skeleton-line" style="width: 80px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 70px; height: 16px;"></div>
                                <div class="skeleton-line" style="width: 60px; height: 28px; border-radius: 6px;"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Actual Table (hidden initially) -->
            <table class="status-table" id="trendingTable" style="display: none;">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Topic Name</th>
                        <th class="text-center" style="width: 140px;">Appearances</th>
                        <th class="text-center" style="width: 120px;">Avg Rank</th>
                        <th class="text-center" style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="trendingTableBody">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>

            <!-- Empty State -->
            <div id="emptyState" style="display: none;">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <h3>No Trending Topics Found</h3>
                    <p>No trending topics data available for the selected date range.</p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination" id="pagination" style="display: none;">
            <button class="pagination-btn" id="prevBtn" onclick="TrendingLoader.changePage(-1)">
                ← Previous
            </button>
            <span class="pagination-info" id="pageInfo">Page 1 of 1</span>
            <button class="pagination-btn" id="nextBtn" onclick="TrendingLoader.changePage(1)">
                Next →
            </button>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const TrendingLoader = {
    startDate: '{{ $startDate ?? "" }}',
    endDate: '{{ $endDate ?? "" }}',
    location: '{{ $location ?? "Indonesia" }}',
    trendingData: null,
    allTopics: [],
    currentPage: 1,
    topicsPerPage: 20,

    async init() {
        console.log('🚀 TrendingLoader init started');
        console.log('📅 Date Range:', this.startDate, 'to', this.endDate);
        console.log('📍 Location:', this.location);
        
        try {
            await this.loadData();
        } catch (error) {
            console.error('❌ Failed to load trending data:', error);
            this.showError();
        }
    },

    async loadData() {
        const url = `/mk/api/x/trending-topics?start_date=${this.startDate}&end_date=${this.endDate}&location=${this.location}`;
        
        console.log('🌐 Fetching from URL:', url);
        
        try {
            const response = await fetch(url);
            console.log('📡 Response status:', response.status);
            
            const result = await response.json();
            console.log('📊 Full API response:', result);

            if (!result.success) {
                console.error('❌ API returned success=false:', result.error);
                throw new Error(result.error || 'Failed to load data');
            }

            this.trendingData = result.data;
            this.allTopics = this.trendingData.top_topics || [];
            
            console.log('💾 Data stored:', {
                totalTopics: this.allTopics.length,
                totalPeriods: this.trendingData.total_periods,
                uniqueTopics: this.trendingData.total_unique_topics
            });
            
            this.updateStats();
            this.renderTable();
        } catch (error) {
            console.error('💥 Error in loadData:', error);
            throw error;
        }
    },

    updateStats() {
        const data = this.trendingData;
        
        const totalPeriods = data.total_periods || 0;
        const uniqueTopics = data.total_unique_topics || 0;
        const avgTopics = totalPeriods > 0 ? Math.round(uniqueTopics / totalPeriods) : 0;

        console.log('📊 Stats:', { totalPeriods, uniqueTopics, avgTopics });

        document.getElementById('totalPeriods').textContent = totalPeriods;
        document.getElementById('uniqueTopics').textContent = uniqueTopics;
        document.getElementById('avgTopics').textContent = avgTopics;
    },

    renderTable() {
        const loadingTable = document.getElementById('loadingTable');
        const trendingTable = document.getElementById('trendingTable');
        const emptyState = document.getElementById('emptyState');
        const pagination = document.getElementById('pagination');

        if (!this.allTopics || !this.allTopics.length) {
            console.warn('⚠️ No topics to display - showing empty state');
            loadingTable.style.display = 'none';
            trendingTable.style.display = 'none';
            emptyState.style.display = 'block';
            pagination.style.display = 'none';
            return;
        }

        const startIdx = (this.currentPage - 1) * this.topicsPerPage;
        const endIdx = startIdx + this.topicsPerPage;
        const currentTopics = this.allTopics.slice(startIdx, endIdx);

        const tbody = document.getElementById('trendingTableBody');
        tbody.innerHTML = currentTopics.map((topic, idx) => this.createTableRow(topic, startIdx + idx + 1)).join('');

        loadingTable.style.display = 'none';
        trendingTable.style.display = 'table';
        emptyState.style.display = 'none';

        // Update pagination
        this.updatePagination();
        
        // Only show pagination if there are multiple pages
        const totalPages = Math.ceil(this.allTopics.length / this.topicsPerPage);
        pagination.style.display = totalPages > 1 ? 'flex' : 'none';
        
        console.log('✅ Table rendered successfully', {
            totalTopics: this.allTopics.length,
            currentPage: this.currentPage,
            showing: currentTopics.length,
            totalPages: totalPages
        });
    },

    createTableRow(topic, rank) {
        const name = this.escapeHtml(topic.name);
        const appearances = topic.appearances || 0;
        const avgRank = topic.avg_rank || 0;
        const url = topic.url || '#';

        return `
            <tr>
                <td class="rank-cell">#${rank}</td>
                
                <td class="topic-cell">${name}</td>
                
                <td class="appearances-cell">${appearances}</td>
                
                <td class="rank-avg-cell">${avgRank}</td>
                
                <td class="action-cell">
                    ${url !== '#' ? `
                    <a href="${url}" target="_blank" class="btn-view">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        View
                    </a>
                    ` : '<span style="color: var(--text-muted); font-size: 11px; font-weight: 500;">—</span>'}
                </td>
            </tr>`;
    },

    updatePagination() {
        const totalPages = Math.ceil(this.allTopics.length / this.topicsPerPage);
        const pageInfo = document.getElementById('pageInfo');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        const startIdx = (this.currentPage - 1) * this.topicsPerPage + 1;
        const endIdx = Math.min(this.currentPage * this.topicsPerPage, this.allTopics.length);

        pageInfo.innerHTML = `Showing <strong>${startIdx}-${endIdx}</strong> of <strong>${this.allTopics.length}</strong> topics`;
        prevBtn.disabled = this.currentPage === 1;
        nextBtn.disabled = this.currentPage === totalPages;
        
        console.log('📄 Pagination updated', {
            currentPage: this.currentPage,
            totalPages: totalPages,
            showing: `${startIdx}-${endIdx} of ${this.allTopics.length}`
        });
    },

    changePage(direction) {
        const totalPages = Math.ceil(this.allTopics.length / this.topicsPerPage);
        const newPage = this.currentPage + direction;

        if (newPage >= 1 && newPage <= totalPages) {
            this.currentPage = newPage;
            this.renderTable();
            
            // Scroll to top of table
            document.querySelector('.table-container').scrollIntoView({ behavior: 'smooth' });
            
            console.log('🔄 Page changed to:', newPage);
        }
    },

    formatNumber(num) {
        if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
        if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
        return num.toLocaleString();
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    showError() {
        const loadingTable = document.getElementById('loadingTable');
        const emptyState = document.getElementById('emptyState');
        
        loadingTable.style.display = 'none';
        emptyState.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" style="color:#ef4444;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <h3>Failed to Load Data</h3>
                <p>Unable to fetch trending topics. Please try again later.</p>
            </div>`;
        emptyState.style.display = 'block';
        
        document.getElementById('totalPeriods').textContent = '0';
        document.getElementById('uniqueTopics').textContent = '0';
        document.getElementById('avgTopics').textContent = '0';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('🎬 DOM Content Loaded - Starting TrendingLoader');
    TrendingLoader.init();
});
</script>
@endsection