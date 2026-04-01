@extends('mk.layouts.app')

@section('title', 'X Engagement Metrics - SMADIMENT')

@section('styles')
<style>
    :root {
        --primary-green: #038047;
        --primary-green-dark: #026738;
        --text-primary: #1a202c;
        --text-secondary: #64748b;
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
    .page-header h1 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin: 0 0 8px 0; }
    .page-header p  { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* Section Divider */
    .section-divider {
        margin: 48px 0 32px 0;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--border-gray);
    }
    .section-title { font-size: 22px; font-weight: 700; color: var(--text-primary); margin: 0 0 6px 0; }
    .section-subtitle { font-size: 14px; color: var(--text-secondary); margin: 0; }

    /* Filter */
    .filter-card {
        background: var(--bg-white);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 32px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-gray);
    }
    .filter-content { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .filter-label   { font-size: 14px; font-weight: 600; color: var(--text-primary); white-space: nowrap; }
    .date-range-wrapper { display: flex; align-items: center; gap: 12px; flex: 1; }

    .date-input-group {
        display: flex; align-items: center; gap: 8px;
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
    .date-input-group svg { width: 18px; height: 18px; color: var(--text-secondary); stroke: currentColor; fill: none; stroke-width: 2; }
    .date-separator { color: var(--text-secondary); font-weight: 600; font-size: 14px; }
    .date-input {
        border: none; background: transparent;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500;
        color: var(--text-primary); outline: none; min-width: 140px;
    }

    .apply-btn {
        padding: 12px 28px;
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        color: white; border: none; border-radius: 12px;
        font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: all 0.3s;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
    }
    .apply-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(3, 128, 71, 0.3); }
    .apply-btn svg { width: 18px; height: 18px; stroke: currentColor; fill: none; }

    /* Alert */
    .alert {
        padding: 16px 20px; border-radius: 12px; margin-bottom: 24px;
        font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 12px;
    }
    .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

    /* Stats Grid - 2 columns */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--bg-white); border: 1px solid var(--border-gray);
        border-radius: 16px; padding: 24px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; overflow: hidden;
        min-height: 120px; display: flex; flex-direction: column; justify-content: center;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        opacity: 0; transition: opacity 0.3s;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-green); }
    .stat-card:hover::before { opacity: 1; }
    .stat-label { font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
    .stat-value { font-size: 32px; font-weight: 700; color: var(--text-primary); line-height: 1.2; word-break: break-word; }
    .stat-value-small { font-size: 24px; }

    /* do-card */
    .do-card {
        background: var(--bg-white); border: 1px solid var(--border-gray);
        border-radius: 16px; overflow: hidden;
        display: flex; flex-direction: column;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative; margin-bottom: 24px;
    }
    .do-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
        opacity: 0; transition: opacity 0.3s;
    }
    .do-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-green); }
    .do-card:hover::before { opacity: 1; }

    .do-card-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 2px solid var(--bg-gray-50);
        background: var(--bg-white);
        flex-shrink: 0;
    }
    .do-card-head-left { display: flex; align-items: center; gap: 12px; }
    .do-head-icon {
        width: 56px; height: 56px; border-radius: 14px;
        background: linear-gradient(135deg, rgba(3,128,71,0.1) 0%, rgba(3,128,71,0.05) 100%);
        display: flex; align-items: center; justify-content: center;
        position: relative; flex-shrink: 0;
    }
    .do-head-icon::after {
        content: ''; position: absolute; inset: -4px; border-radius: 16px; padding: 4px;
        background: linear-gradient(135deg, var(--primary-green), var(--primary-green-dark));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor; mask-composite: exclude;
        opacity: 0; transition: opacity 0.3s;
    }
    .do-card:hover .do-head-icon::after { opacity: 0.5; }
    .do-head-icon svg { width: 28px; height: 28px; color: var(--primary-green); fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .do-card-title    { font-size: 18px; font-weight: 700; color: var(--text-primary); font-family: 'Poppins', sans-serif; margin: 0 0 4px 0; }
    .do-card-subtitle { font-size: 13px; color: var(--text-secondary); font-weight: 500; margin: 0; }
    .do-badge { font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 20px; background: var(--bg-gray-100); color: var(--text-secondary); text-transform: uppercase; letter-spacing: .4px; font-family: 'Poppins', sans-serif; }

    .do-card-body { padding: 20px 24px 24px; flex: 1; }

    /* Skeleton */
    .skeleton-line {
        height: 28px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%; animation: shimmer 1.5s infinite;
        border-radius: 8px; margin-bottom: 10px;
    }
    .skeleton-number-inline {
        height: 48px; width: 140px;
        background: linear-gradient(90deg, var(--bg-gray-50) 25%, var(--border-gray) 50%, var(--bg-gray-50) 75%);
        background-size: 200% 100%; animation: shimmer 1.5s infinite;
        border-radius: 8px; display: inline-block; margin: 0 auto;
    }
    @keyframes shimmer {
        0%   { background-position:  200% 0; }
        100% { background-position: -200% 0; }
    }
    .do-card[data-loaded="true"] .do-skeleton { display: none; }

    /* Chart Container */
    .chart-container {
        position: relative;
        height: 280px;
    }

    /* User Cards Grid */
    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }

    .user-card {
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s;
        cursor: pointer;
    }
    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-green);
    }

    .user-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--primary-green);
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-username {
        font-size: 12px;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .user-stat {
        text-align: center;
    }

    .user-stat-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-green);
    }

    .user-stat-label {
        font-size: 10px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    /* Reach Ranges */
    .reach-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }

    .reach-card {
        background: var(--bg-white);
        border: 1px solid var(--border-gray);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s;
    }

    .reach-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-green);
    }

    .reach-range {
        font-size: 12px;
        font-weight: 700;
        color: var(--primary-green);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .reach-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .reach-label {
        font-size: 11px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .reach-authors {
        font-size: 13px;
        color: var(--text-secondary);
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid var(--bg-gray-100);
    }

    .reach-authors strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Empty State */
    .do-empty {
        font-size: 14px;
        color: var(--text-secondary);
        text-align: center;
        padding: 60px 20px;
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-container { padding: 16px; }
        .stats-grid { grid-template-columns: 1fr; }
        .filter-content { flex-direction: column; align-items: stretch; }
        .date-range-wrapper { flex-direction: column; }
        .apply-btn { width: 100%; justify-content: center; }
        .users-grid { grid-template-columns: 1fr; }
        .reach-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">

    <div class="page-header">
        <h1>X Engagement Metrics</h1>
        <p>Monitor user engagement, sentiment trends, and estimated reach</p>
    </div>

    @if(!$projectId)
    <div class="alert alert-warning">
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <span>No project selected. Please select a project from the sidebar to view engagement metrics.</span>
    </div>
    @else

    <!-- Date Filter -->
    <div class="filter-card">
        <form id="filterForm" method="GET" action="{{ route('mk.x.engagement') }}">
            <input type="hidden" name="project_id" value="{{ $projectId }}">
            <div class="filter-content">
                <div class="filter-label">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;display:inline;vertical-align:middle;margin-right:6px;stroke:currentColor;fill:none;">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Date Range
                </div>
                <div class="date-range-wrapper">
                    <div class="date-input-group">
                        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <input type="date" name="start_date" class="date-input" value="{{ $startDate }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                    <span class="date-separator">to</span>
                    <div class="date-input-group">
                        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <input type="date" name="end_date" class="date-input" value="{{ $endDate }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <button type="submit" class="apply-btn">
                    <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================
         SECTION 1: MOST ACTIVE USERS
         ======================================== -->
    <div class="section-divider">
        <h2 class="section-title">Most Active Users</h2>
        <p class="section-subtitle">Top contributors and their engagement statistics</p>
    </div>

    <div class="stats-grid" data-lazy="active-users-stats">
        <div class="stat-card">
            <div class="stat-label">Total Active Users</div>
            <div id="totalActiveUsers" class="stat-value">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Top Contributor</div>
            <div id="topContributor" class="stat-value stat-value-small">—</div>
        </div>
    </div>

    <div class="do-card" data-lazy="active-users-list">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <div class="do-head-icon">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <p class="do-card-title">Active Users</p>
                    <p class="do-card-subtitle">Users ranked by activity</p>
                </div>
            </div>
            <span class="do-badge">Top 20</span>
        </div>
        <div class="do-card-body">
            <div class="do-skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
            </div>
            <div id="activeUsersList"></div>
        </div>
    </div>

    <!-- ========================================
         SECTION 2: SENTIMENT ANALYSIS
         ======================================== -->
    <div class="section-divider">
        <h2 class="section-title">Sentiment Analysis</h2>
        <p class="section-subtitle">Overall sentiment distribution and trends</p>
    </div>

    <div class="stats-grid" data-lazy="sentiment-stats">
        <div class="stat-card">
            <div class="stat-label">Positive</div>
            <div id="sentimentPositive" class="stat-value" style="color: #22c55e;">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Negative</div>
            <div id="sentimentNegative" class="stat-value" style="color: #ef4444;">—</div>
        </div>
    </div>

    <div class="do-card" data-lazy="sentiment-chart">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <div class="do-head-icon">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </div>
                <div>
                    <p class="do-card-title">Sentiment Distribution</p>
                    <p class="do-card-subtitle">Overall sentiment breakdown</p>
                </div>
            </div>
            <span class="do-badge">Analysis</span>
        </div>
        <div class="do-card-body">
            <div class="do-skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
            </div>
            <div class="chart-container">
                <canvas id="sentimentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ========================================
         SECTION 3: ESTIMATED REACH
         ======================================== -->
    <div class="section-divider">
        <h2 class="section-title">Estimated Reach</h2>
        <p class="section-subtitle">Potential audience reach by follower ranges</p>
    </div>

    <div class="stats-grid" data-lazy="reach-stats">
        <div class="stat-card">
            <div class="stat-label">Total Estimated Reach</div>
            <div id="totalReach" class="stat-value">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Highest Range</div>
            <div id="highestRange" class="stat-value stat-value-small">—</div>
        </div>
    </div>

    <div class="do-card" data-lazy="reach-breakdown">
        <div class="do-card-head">
            <div class="do-card-head-left">
                <div class="do-head-icon">
                    <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div>
                    <p class="do-card-title">Reach by Follower Range</p>
                    <p class="do-card-subtitle">Breakdown by audience size</p>
                </div>
            </div>
            <span class="do-badge">Metrics</span>
        </div>
        <div class="do-card-body">
            <div class="do-skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
            </div>
            <div id="reachBreakdown"></div>
        </div>
    </div>

    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
const XEngagementLoader = {
    projectId: '{{ $projectId ?? "" }}',
    startDate: '{{ $startDate ?? "" }}',
    endDate:   '{{ $endDate ?? "" }}',
    loadedSections: new Set(),

    init() {
        this.setupIntersectionObserver();
    },

    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const section = el.dataset.lazy;
                if (this.loadedSections.has(section)) return;
                this.loadedSections.add(section);
                observer.unobserve(el);
                this.loadSection(section, el);
            });
        }, { root: null, rootMargin: '100px', threshold: 0.1 });

        document.querySelectorAll('[data-lazy]').forEach(el => observer.observe(el));
    },

    async loadSection(section, el) {
        try {
            switch (section) {
                case 'active-users-stats': await this.loadActiveUsersStats(el); break;
                case 'active-users-list':  await this.loadActiveUsersList(el);  break;
                case 'sentiment-stats':    await this.loadSentimentStats(el);   break;
                case 'sentiment-chart':    await this.loadSentimentChart(el);   break;
                case 'reach-stats':        await this.loadReachStats(el);       break;
                case 'reach-breakdown':    await this.loadReachBreakdown(el);   break;
            }
            el.dataset.loaded = 'true';
        } catch (err) {
            console.error(`❌ Failed to load ${section}:`, err);
        }
    },

    // ========================================
    // MOST ACTIVE USERS
    // ========================================
    async loadActiveUsersStats(el) {
        const res = await fetch(`/mk/api/x/most-active-users?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();
        console.log('📊 mostActiveUsers response:', result);

        if (result.success && result.data && result.data.data) {
            const users = result.data.data;
            document.getElementById('totalActiveUsers').textContent = users.length;
            const topUser = users[0];
            if (topUser) {
                document.getElementById('topContributor').textContent = '@' + topUser.username;
            } else {
                document.getElementById('topContributor').textContent = 'N/A';
            }
        } else {
            document.getElementById('totalActiveUsers').textContent = '0';
            document.getElementById('topContributor').textContent = 'N/A';
        }

        document.querySelectorAll('[data-lazy="active-users-stats"]').forEach(c => {
            c.dataset.loaded = 'true';
        });
    },

    async loadActiveUsersList(card) {
        const res = await fetch(`/mk/api/x/most-active-users?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();

        const listEl = document.getElementById('activeUsersList');

        if (!result.success || !result.data || !result.data.data || result.data.data.length === 0) {
            listEl.innerHTML = '<div class="do-empty">No active users found</div>';
            return;
        }

        const users = result.data.data.slice(0, 20);

        let html = '<div class="users-grid">';
        users.forEach(user => {
            const avatar = user.profile_image_url || user.profile_url || 'https://via.placeholder.com/48';
            const name = user.name || user.username || 'Unknown';
            const username = user.username || '';
            const followers = parseInt(user.followers) || 0;
            const posts = parseInt(user.posts) || parseInt(user.y) || 0;

            html += `
                <div class="user-card">
                    <div class="user-header">
                        <img src="${avatar}" alt="${name}" class="user-avatar" onerror="this.src='https://via.placeholder.com/48'">
                        <div class="user-info">
                            <div class="user-name">${name}</div>
                            <div class="user-username">@${username}</div>
                        </div>
                    </div>
                    <div class="user-stats">
                        <div class="user-stat">
                            <div class="user-stat-value">${posts.toLocaleString()}</div>
                            <div class="user-stat-label">Posts</div>
                        </div>
                        <div class="user-stat">
                            <div class="user-stat-value">${this.formatNumber(followers)}</div>
                            <div class="user-stat-label">Followers</div>
                        </div>
                    </div>
                </div>`;
        });
        html += '</div>';

        listEl.innerHTML = html;
    },

    // ========================================
    // SENTIMENT ANALYSIS
    // ========================================
    async loadSentimentStats(el) {
        const res = await fetch(`/mk/api/x/get-sentiment?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();
        console.log('😊 getSentiment response:', result);

        if (result.success && result.data && result.data.data) {
            const data = result.data.data;
            const pos = parseInt(data.pos) || 0;
            const neg = parseInt(data.neg) || 0;

            document.getElementById('sentimentPositive').textContent = pos.toLocaleString();
            document.getElementById('sentimentNegative').textContent = neg.toLocaleString();
        } else {
            document.getElementById('sentimentPositive').textContent = '0';
            document.getElementById('sentimentNegative').textContent = '0';
        }

        document.querySelectorAll('[data-lazy="sentiment-stats"]').forEach(c => {
            c.dataset.loaded = 'true';
        });
    },

    async loadSentimentChart(card) {
        const res = await fetch(`/mk/api/x/get-sentiment?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();

        const canvasEl = document.getElementById('sentimentChart');
        if (!canvasEl) return;

        if (!result.success || !result.data || !result.data.data) {
            canvasEl.parentElement.innerHTML = '<div class="do-empty">No sentiment data</div>';
            return;
        }

        const data = result.data.data;
        const pos = parseInt(data.pos) || 0;
        const neg = parseInt(data.neg) || 0;
        const net = parseInt(data.net) || 0;

        new Chart(canvasEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [pos, net, neg],
                    backgroundColor: ['#22c55e', '#94a3b8', '#ef4444'],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#1a202c',
                            font: { family: 'Poppins', size: 12, weight: '600' },
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1a202c',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        displayColors: false,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = pos + neg + net;
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.parsed.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 900
                }
            }
        });
    },

    // ========================================
    // ESTIMATED REACH
    // ========================================
    async loadReachStats(el) {
        const res = await fetch(`/mk/api/x/est-reach?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();
        console.log('📈 estReach response:', result);

        if (result.success && result.data) {
            const ranges = result.data;
            let totalReach = 0;
            let highestRange = { name: '', reach: 0 };

            Object.entries(ranges).forEach(([rangeName, rangeData]) => {
                const reach = parseFloat(rangeData.reach) || 0;
                totalReach += reach;

                if (reach > highestRange.reach) {
                    highestRange = { name: this.formatRangeName(rangeName), reach };
                }
            });

            document.getElementById('totalReach').textContent = this.formatNumber(totalReach);
            document.getElementById('highestRange').textContent = highestRange.name || 'N/A';
        } else {
            document.getElementById('totalReach').textContent = '0';
            document.getElementById('highestRange').textContent = 'N/A';
        }

        document.querySelectorAll('[data-lazy="reach-stats"]').forEach(c => {
            c.dataset.loaded = 'true';
        });
    },

    async loadReachBreakdown(card) {
        const res = await fetch(`/mk/api/x/est-reach?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`);
        const result = await res.json();

        const breakdownEl = document.getElementById('reachBreakdown');

        if (!result.success || !result.data || Object.keys(result.data).length === 0) {
            breakdownEl.innerHTML = '<div class="do-empty">No reach data available</div>';
            return;
        }

        const ranges = result.data;

        // Sort ranges by reach (descending)
        const sortedRanges = Object.entries(ranges).sort((a, b) => {
            const reachA = parseFloat(a[1].reach) || 0;
            const reachB = parseFloat(b[1].reach) || 0;
            return reachB - reachA;
        });

        let html = '<div class="reach-grid">';
        sortedRanges.forEach(([rangeName, rangeData]) => {
            const reach = parseFloat(rangeData.reach) || 0;
            const authors = rangeData.authors_count || 0;
            const displayName = this.formatRangeName(rangeName);

            html += `
                <div class="reach-card">
                    <div class="reach-range">${displayName}</div>
                    <div class="reach-value">${this.formatNumber(reach)}</div>
                    <div class="reach-label">Estimated Reach</div>
                    <div class="reach-authors"><strong>${authors.toLocaleString()}</strong> authors</div>
                </div>`;
        });
        html += '</div>';

        breakdownEl.innerHTML = html;
    },

    // ========================================
    // UTILITY FUNCTIONS
    // ========================================
    formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toLocaleString();
    },

    formatRangeName(rangeName) {
        // Convert "1M_up" → "1M+" etc.
        const formatted = rangeName
            .replace('_up', '+')
            .replace('_', '-')
            .replace('K', 'K')
            .replace('M', 'M');
        return formatted;
    }
};

document.addEventListener('DOMContentLoaded', () => XEngagementLoader.init());
</script>
@endsection