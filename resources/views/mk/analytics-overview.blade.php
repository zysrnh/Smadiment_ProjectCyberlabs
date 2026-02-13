@extends('mk.layouts.app')

@section('title', 'Analytics Overview - SMADIMENT')

@section('content')

<!-- ============================================================
     TOP BAR WITH FILTERS
     ============================================================ -->
<div class="top-bar">
    <div class="page-title">
        <h2>Analytics Overview</h2>
        <div class="page-subtitle">Topic trends, hashtags, locations & top influencers</div>
    </div>

    <div style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap;">
        <!-- Project Selector -->
        <div class="ao-filter-group">
            <label class="ao-filter-label">Project</label>
            <select class="ao-filter-input" id="aoProject">
                @foreach($projects as $p)
                <option value="{{ $p['id'] }}" {{ $p['id'] == $projectId ? 'selected' : '' }}>
                    {{ $p['name'] ?? $p['title'] ?? 'Project #' . $p['id'] }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="ao-filter-group">
            <label class="ao-filter-label">Start Date</label>
            <input type="date" class="ao-filter-input" id="aoStartDate" value="{{ $startDate }}">
        </div>
        
        <div class="ao-filter-group">
            <label class="ao-filter-label">End Date</label>
            <input type="date" class="ao-filter-input" id="aoEndDate" value="{{ $endDate }}">
        </div>

        <div class="ao-filter-group">
            <label class="ao-filter-label">Media</label>
            <select class="ao-filter-input" id="aoMedia">
                <option value="all" {{ $media == 'all' ? 'selected' : '' }}>All Media</option>
                <option value="twitter" {{ $media == 'twitter' ? 'selected' : '' }}>Twitter/X</option>
                <option value="news" {{ $media == 'news' ? 'selected' : '' }}>News</option>
                <option value="instagram" {{ $media == 'instagram' ? 'selected' : '' }}>Instagram</option>
            </select>
        </div>
        
        <button class="ao-btn-apply" id="aoBtnApply">
            <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            Apply Filters
        </button>
    </div>
</div>

<!-- ============================================================
     ROW 1 — TOPIC MAP + TOP HASHTAGS
     ============================================================ -->
<div class="ao-row-dual">

    <!-- Topic Map Card -->
    <div class="ao-card" data-lazy="topic-map">
        <div class="ao-card-head">
            <div class="ao-card-head-left">
                <span class="ao-head-icon" style="background:#F1F5F8; color:#038047;">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" />
                    </svg>
                </span>
                <span class="ao-card-title">Topic Map</span>
            </div>
            <div class="ao-chart-switcher">
                <button class="ao-chart-btn active" data-chart="wordcloud" onclick="switchTopicChart('wordcloud', this)">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                </button>
                <button class="ao-chart-btn" data-chart="bar" onclick="switchTopicChart('bar', this)">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                        <line x1="12" y1="20" x2="12" y2="10"/>
                        <line x1="18" y1="20" x2="18" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="16"/>
                    </svg>
                </button>
                <button class="ao-chart-btn" data-chart="pie" onclick="switchTopicChart('pie', this)">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
                        <path d="M22 12A10 10 0 0 0 12 2v10z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="ao-card-body" style="min-height: 400px; position: relative;">
            <!-- Word Cloud View -->
            <div class="ao-chart-view active" id="topicWordCloud">
                <canvas id="topicCloudCanvas" style="width:100% !important; height:380px !important;"></canvas>
            </div>
            
            <!-- Bar Chart View -->
            <div class="ao-chart-view" id="topicBarChart">
                <div style="height: 380px;">
                    <canvas id="topicBarCanvas"></canvas>
                </div>
            </div>
            
            <!-- Pie Chart View -->
            <div class="ao-chart-view" id="topicPieChart">
                <div style="height: 380px;">
                    <canvas id="topicPieCanvas"></canvas>
                </div>
            </div>
            
            <!-- Loading Skeleton -->
            <div class="ao-skeleton">
                <div class="skeleton-cloud"></div>
            </div>
        </div>
    </div>

    <!-- Top Hashtags Card -->
    <div class="ao-card" data-lazy="top-hashtags">
        <div class="ao-card-head">
            <div class="ao-card-head-left">
                <span class="ao-head-icon" style="background:#F1F5F8; color:#27384A;">
                    <svg viewBox="0 0 24 24">
                        <line x1="4" y1="9" x2="20" y2="9" />
                        <line x1="4" y1="15" x2="20" y2="15" />
                        <line x1="10" y1="3" x2="8" y2="21" />
                        <line x1="16" y1="3" x2="14" y2="21" />
                    </svg>
                </span>
                <span class="ao-card-title">Top Hashtags</span>
            </div>
            <button class="ao-view-all-btn" id="hashtagsViewAllBtn" style="display:none;" onclick="openHashtagsModal()">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
                View All
            </button>
        </div>
        <div class="ao-card-body ao-body-scroll" style="max-height: 400px;">
            <div class="ao-skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================
     ROW 2 — TOP LOCATIONS + TOP INFLUENCERS
     ============================================================ -->
<div class="ao-row-dual" style="margin-top: 20px;">

    <!-- Top Locations Card -->
    <div class="ao-card" data-lazy="top-locations">
        <div class="ao-card-head">
            <div class="ao-card-head-left">
                <span class="ao-head-icon" style="background:#F1F5F8; color:#038047;">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </span>
                <span class="ao-card-title">Top Locations</span>
            </div>
            <button class="ao-view-all-btn" id="locationsViewAllBtn" style="display:none;" onclick="openLocationsModal()">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
                View All
            </button>
        </div>
        <div class="ao-card-body ao-body-scroll" style="max-height: 400px;">
            <div class="ao-skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
            </div>
        </div>
    </div>

    <!-- Top Influencers Card -->
    <div class="ao-card" data-lazy="top-influencers">
        <div class="ao-card-head">
            <div class="ao-card-head-left">
                <span class="ao-head-icon" style="background:#F1F5F8; color:#27384A;">
                    <svg viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>
                <span class="ao-card-title">Top Influencers</span>
            </div>
            <button class="ao-view-all-btn" id="influencersViewAllBtn" style="display:none;" onclick="openInfluencersModal()">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
                View All
            </button>
        </div>
        <div class="ao-card-body ao-body-scroll" style="max-height: 400px;">
            <div class="ao-skeleton">
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
                <div class="skeleton-line"></div>
            </div>
        </div>
    </div>

</div>

<!-- ============================================================
     MODALS
     ============================================================ -->

<!-- Hashtags Modal -->
<div id="hashtagsModal" class="ao-modal">
    <div class="ao-modal-content">
        <div class="ao-modal-header">
            <div>
                <h3 class="ao-modal-title">All Top Hashtags</h3>
                <p class="ao-modal-subtitle">Complete list of trending hashtags</p>
            </div>
            <button class="ao-modal-close" onclick="closeHashtagsModal()">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="ao-modal-body">
            <input type="text" class="ao-modal-search" id="hashtagsSearch" placeholder="Search hashtags...">
            <div id="hashtagsModalBody"></div>
        </div>
    </div>
</div>

<!-- Locations Modal -->
<div id="locationsModal" class="ao-modal">
    <div class="ao-modal-content">
        <div class="ao-modal-header">
            <div>
                <h3 class="ao-modal-title">All Top Locations</h3>
                <p class="ao-modal-subtitle">Complete list of top locations</p>
            </div>
            <button class="ao-modal-close" onclick="closeLocationsModal()">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="ao-modal-body">
            <input type="text" class="ao-modal-search" id="locationsSearch" placeholder="Search locations...">
            <div id="locationsModalBody"></div>
        </div>
    </div>
</div>

<!-- Influencers Modal -->
<div id="influencersModal" class="ao-modal">
    <div class="ao-modal-content">
        <div class="ao-modal-header">
            <div>
                <h3 class="ao-modal-title">All Top Influencers</h3>
                <p class="ao-modal-subtitle">Complete list of top influencers</p>
            </div>
            <button class="ao-modal-close" onclick="closeInfluencersModal()">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="ao-modal-body">
            <input type="text" class="ao-modal-search" id="influencersSearch" placeholder="Search influencers...">
            <div id="influencersModalBody"></div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

    :root {
        --primary-green: #038047;
        --dark-blue: #27384A;
        --white: #FFFFFF;
        --light-gray: #F1F5F8;
        --text-primary: #27384A;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --border-color: #e2e8f0;
        --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    }

    * {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Filters */
    .ao-filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .ao-filter-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-primary);
        opacity: .45;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .ao-filter-input {
        padding: 8px 12px;
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        background: var(--white);
        outline: none;
        transition: border-color .2s;
        min-width: 140px;
    }

    .ao-filter-input:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
    }

    .ao-btn-apply {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 9px 20px;
        background: #038047;
        color: #FFFFFF;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
    }

    .ao-btn-apply:hover {
        background: #026838;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Grid Layouts */
    .ao-row-dual {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 24px;
    }

    /* Card */
    .ao-card {
        background: #FFFFFF;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: var(--card-shadow);
        transition: box-shadow .2s;
    }

    .ao-card:hover {
        box-shadow: var(--card-shadow-hover);
    }

    /* Card Head */
    .ao-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px 12px;
        border-bottom: 1px solid #e2e8f0;
        background: #FFFFFF;
    }

    .ao-card-head-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ao-head-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ao-head-icon svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ao-card-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--text-primary);
    }

    /* Card Body */
    .ao-card-body {
        padding: 18px;
        flex: 1;
    }

    .ao-body-scroll {
        overflow-y: auto;
    }

    .ao-body-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .ao-body-scroll::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 2px;
    }

    /* Chart Switcher */
    .ao-chart-switcher {
        display: flex;
        gap: 4px;
        background: #F1F5F8;
        padding: 3px;
        border-radius: 8px;
    }

    .ao-chart-btn {
        padding: 6px 10px;
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ao-chart-btn svg {
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ao-chart-btn:hover {
        background: #FFFFFF;
        color: #27384A;
    }

    .ao-chart-btn.active {
        background: #038047;
        color: #FFFFFF;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    /* Chart Views */
    .ao-chart-view {
        display: none;
    }

    .ao-chart-view.active {
        display: block;
    }

    /* View All Button */
    .ao-view-all-btn {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: transparent;
        color: #27384A;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
    }

    .ao-view-all-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-1px);
    }

    .ao-view-all-btn svg {
        fill: none;
        stroke: currentColor;
        stroke-width: 2.5;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* Table */
    .ao-tbl {
        width: 100%;
        border-collapse: collapse;
    }

    .ao-tbl th {
        font-size: 10px;
        font-weight: 700;
        color: var(--text-primary);
        opacity: .4;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 0 0 7px;
        border-bottom: 1px solid var(--border-color);
        text-align: left;
    }

    .ao-tbl-left {
        text-align: left;
    }

    .ao-tbl-right {
        text-align: right;
    }

    .ao-tbl td {
        padding: 6.5px 0;
        font-size: 13px;
        color: var(--text-primary);
        border-bottom: 1px solid #f0f2f5;
    }

    .ao-tbl tr:last-child td {
        border-bottom: none;
    }

    .ao-tbl-rank {
        font-weight: 800;
        color: #038047;
        width: 22px;
        font-size: 12px;
    }

    .ao-tbl-name {
        font-weight: 600;
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ao-tbl-num {
        text-align: right;
        font-weight: 700;
        font-size: 12px;
        color: var(--text-primary);
        opacity: .65;
    }

    /* Skeleton Loading */
    .ao-skeleton {
        padding: 10px 0;
    }

    .skeleton-line {
        height: 28px;
        background: linear-gradient(90deg, #f0f2f5 25%, #e8eaed 50%, #f0f2f5 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .skeleton-cloud {
        height: 360px;
        background: linear-gradient(90deg, #f0f2f5 25%, #e8eaed 50%, #f0f2f5 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 12px;
    }

    @keyframes shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .ao-card[data-loaded="true"] .ao-skeleton {
        display: none;
    }

    /* Modal Styles */
    .ao-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        animation: fadeIn 0.2s ease;
    }

    .ao-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ao-modal-content {
        background: #fff;
        border-radius: 16px;
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
        overflow: hidden;
    }

    .ao-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 24px 28px;
        border-bottom: 2px solid #f0f2f5;
        background: linear-gradient(135deg, #eef3f9 0%, #fff 100%);
    }

    .ao-modal-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0 0 4px 0;
    }

    .ao-modal-subtitle {
        font-size: 12px;
        font-weight: 500;
        color: #7A8B96;
        margin: 0;
    }

    .ao-modal-close {
        background: #fff;
        border: 1.5px solid #e8eaed;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .2s;
    }

    .ao-modal-close:hover {
        background: #f5f5f5;
        border-color: #d0d5dd;
        transform: rotate(90deg);
    }

    .ao-modal-close svg {
        width: 16px;
        height: 16px;
        stroke: #475569;
        stroke-width: 2.5;
        stroke-linecap: round;
    }

    .ao-modal-body {
        padding: 20px 28px 28px;
        max-height: calc(80vh - 120px);
        overflow-y: auto;
    }

    .ao-modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .ao-modal-body::-webkit-scrollbar-thumb {
        background: #d0d5dd;
        border-radius: 3px;
    }

    .ao-modal-search {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        margin-bottom: 20px;
        transition: all 0.2s;
    }

    .ao-modal-search:focus {
        outline: none;
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(3, 128, 71, 0.1);
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { 
            transform: translateY(30px);
            opacity: 0;
        }
        to { 
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Empty State */
    .ao-empty {
        font-size: 13px;
        color: var(--text-primary);
        opacity: .35;
        text-align: center;
        padding: 40px 0;
        font-weight: 600;
    }

    /* ─────────────────────────────────────────────────────────────────────────────────
       🔥 INFLUENCERS LIST - IMPROVED STYLES
       ───────────────────────────────────────────────────────────────────────────────── */

    .influencers-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .influencer-item {
        display: flex;
        align-items: center;
        padding: 12px 14px;
        background: #FFFFFF;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        gap: 12px;
        transition: all 0.2s ease;
    }

    .influencer-item:hover {
        border-color: #038047;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.12);
        transform: translateY(-2px);
    }

    .influencer-rank {
        font-size: 14px;
        font-weight: 800;
        color: #038047;
        min-width: 26px;
        text-align: center;
        flex-shrink: 0;
    }

    .influencer-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2.5px solid #e2e8f0;
        flex-shrink: 0;
        transition: border-color 0.2s;
    }

    .influencer-item:hover .influencer-avatar {
        border-color: #038047;
    }

    .influencer-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .influencer-name {
        font-size: 13px;
        font-weight: 700;
        color: #27384A;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .influencer-username {
        font-size: 11px;
        font-weight: 500;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .influencer-location {
        font-size: 10px;
        font-weight: 500;
        color: #94a3b8;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .influencer-bio {
        font-size: 11px;
        font-weight: 400;
        color: #64748b;
        margin-top: 4px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .verified-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        background: #1DA1F2;
        color: #FFFFFF;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .influencer-stats {
        display: flex;
        gap: 20px;
        flex-shrink: 0;
        margin-left: auto;
    }

    .stat-item {
        text-align: right;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .stat-value {
        font-size: 14px;
        font-weight: 800;
        color: #27384A;
        white-space: nowrap;
    }

    .stat-label {
        font-size: 9px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Modal Version */
    .influencers-list-modal {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .influencer-item-modal {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        background: #FFFFFF;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        gap: 20px;
        transition: all 0.2s ease;
    }

    .influencer-item-modal:hover {
        border-color: #038047;
        box-shadow: 0 4px 12px rgba(3, 128, 71, 0.12);
        transform: translateX(4px);
    }

    .influencer-left {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
        min-width: 0;
    }

    /* Responsive */
    @media(max-width:1100px) {
        .ao-row-dual {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 992px) {
        .influencer-item {
            flex-wrap: wrap;
            padding: 14px;
        }
        
        .influencer-stats {
            width: 100%;
            justify-content: flex-end;
            gap: 20px;
            padding-left: 60px;
        }
    }

    @media (max-width: 768px) {
        .influencer-rank {
            min-width: 22px;
            font-size: 13px;
        }
        
        .influencer-avatar {
            width: 42px;
            height: 42px;
        }
        
        .influencer-item-modal {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        
        .influencer-left {
            width: 100%;
        }
        
        .influencer-stats {
            width: 100%;
            justify-content: flex-start;
            padding-left: 0;
        }
    }
</style>
@endsection

@section('scripts')
<!-- WordCloud2.js Library -->
<script src="https://cdn.jsdelivr.net/npm/wordcloud@1.2.2/src/wordcloud2.min.js"></script>

<script>
// ────────────────────────────────────────────
// ANALYTICS OVERVIEW LAZY LOADER
// ────────────────────────────────────────────
const AnalyticsOverviewLoader = {
    projectId: {{ $projectId ?? 'null' }},
    startDate: '{{ $startDate }}',
    endDate: '{{ $endDate }}',
    media: '{{ $media }}',
    loadedSections: new Set(),
    charts: {},
    topicsData: [],
    hashtagsData: [],
    locationsData: [],
    influencersData: [],

    init() {
        console.log('Initializing Analytics Overview Lazy Loader');
        this.setupIntersectionObserver();
        this.setupFilterButton();
        this.setupModalSearches();
    },

    setupIntersectionObserver() {
        const options = {
            root: null,
            rootMargin: '100px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    const section = card.dataset.lazy;
                    
                    if (!this.loadedSections.has(section)) {
                        this.loadedSections.add(section);
                        this.loadSection(section, card);
                        observer.unobserve(card);
                    }
                }
            });
        }, options);

        document.querySelectorAll('[data-lazy]').forEach(card => {
            observer.observe(card);
        });
    },

    async loadSection(section, card) {
        console.log(`Loading section: ${section}`);
        
        try {
            switch(section) {
                case 'topic-map':
                    await this.loadTopicMap(card);
                    break;
                case 'top-hashtags':
                    await this.loadHashtags(card);
                    break;
                case 'top-locations':
                    await this.loadLocations(card);
                    break;
                case 'top-influencers':
                    await this.loadInfluencers(card);
                    break;
            }
            
            card.dataset.loaded = 'true';
        } catch (error) {
            console.error(`Failed to load ${section}:`, error);
            this.showError(card);
        }
    },

    async loadTopicMap(card) {
        const response = await fetch(
            `/mk/api/analytics/topic-map?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}&media=${this.media}`
        );
        const result = await response.json();
        
        console.log('Topic Map API Result:', result);
        
        if (result.success && result.data) {
            this.topicsData = result.data;
            this.renderTopicWordCloud(result.data);
        } else {
            throw new Error(result.message || 'Failed to load topic map');
        }
    },

    async loadHashtags(card) {
        const response = await fetch(
            `/mk/api/analytics/hashtags?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}&media=${this.media}`
        );
        const result = await response.json();
        
        console.log('Hashtags API Result:', result);
        
        if (result.success && result.data) {
            this.hashtagsData = result.data;
            this.renderHashtagsTable(result.data, card);
        } else {
            throw new Error(result.message || 'Failed to load hashtags');
        }
    },

    async loadLocations(card) {
        const response = await fetch(
            `/mk/api/analytics/locations?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}&media=${this.media}`
        );
        const result = await response.json();
        
        console.log('Locations API Result:', result);
        
        if (result.success && result.data) {
            this.locationsData = result.data;
            this.renderLocationsTable(result.data, card);
        } else {
            throw new Error(result.message || 'Failed to load locations');
        }
    },

    async loadInfluencers(card) {
        const response = await fetch(
            `/mk/api/analytics/influencers?project_id=${this.projectId}&start_date=${this.startDate}&end_date=${this.endDate}`
        );
        const result = await response.json();
        
        console.log('Influencers API Result:', result);
        
        if (result.success && result.data) {
            this.influencersData = result.data;
            this.renderInfluencersTable(result.data, card);
        } else {
            throw new Error(result.message || 'Failed to load influencers');
        }
    },

    renderTopicWordCloud(topics) {
        if (!topics || topics.length === 0) {
            document.getElementById('topicWordCloud').innerHTML = '<div class="ao-empty">No topic data available</div>';
            return;
        }

        const canvas = document.getElementById('topicCloudCanvas');
        const topTopics = topics.slice(0, 40);
        
        const maxCount = Math.max(...topTopics.map(t => t.count));
        const minCount = Math.min(...topTopics.map(t => t.count));
        
        const wordList = topTopics.map(topic => {
            const normalizedWeight = ((topic.count - minCount) / (maxCount - minCount)) * 100;
            return [topic.name, Math.max(normalizedWeight, 25)];
        });
        
        const colors = [
            '#038047', '#04995a', '#06bf80', '#059669', '#10b981',
            '#14b8a6', '#0891b2', '#0284c7', '#27384A', '#6366f1'
        ];
        
        WordCloud(canvas, {
            list: wordList,
            gridSize: 10,
            weightFactor: function(size) { return size * 2.5; },
            fontFamily: "'Poppins', 'Arial', sans-serif",
            fontWeight: '700',
            color: function() {
                return colors[Math.floor(Math.random() * colors.length)];
            },
            rotateRatio: 0.4,
            rotationSteps: 2,
            minSize: 18,
            backgroundColor: 'transparent',
            drawOutOfBound: false,
            shrinkToFit: true
        });
    },

    renderHashtagsTable(hashtags, card) {
        const body = card.querySelector('.ao-card-body');
        
        if (hashtags.length === 0) {
            body.innerHTML = '<div class="ao-empty">No hashtags data</div>';
            return;
        }

        if (hashtags.length > 8) {
            document.getElementById('hashtagsViewAllBtn').style.display = 'flex';
        }

        let html = `
            <table class="ao-tbl">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th class="ao-tbl-left">Hashtag</th>
                        <th class="ao-tbl-right">Mentions</th>
                    </tr>
                </thead>
                <tbody>
        `;

        hashtags.slice(0, 8).forEach((tag, i) => {
            let tagName = tag.name || 'unknown';
            const count = tag.size || 0;
            
            if (!tagName.startsWith('#')) tagName = '#' + tagName;
            
            html += `
                <tr>
                    <td class="ao-tbl-rank">${i + 1}</td>
                    <td class="ao-tbl-name" style="color:#27384A;">${tagName}</td>
                    <td class="ao-tbl-num">${count.toLocaleString()}</td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        body.innerHTML = html;
    },

    renderLocationsTable(locations, card) {
        const body = card.querySelector('.ao-card-body');
        
        if (locations.length === 0) {
            body.innerHTML = '<div class="ao-empty">No locations data</div>';
            return;
        }

        if (locations.length > 8) {
            document.getElementById('locationsViewAllBtn').style.display = 'flex';
        }

        let html = `
            <table class="ao-tbl">
                <thead>
                    <tr>
                        <th style="width:28px;">#</th>
                        <th class="ao-tbl-left">Location</th>
                        <th class="ao-tbl-right">Count</th>
                    </tr>
                </thead>
                <tbody>
        `;

        locations.slice(0, 8).forEach((loc, i) => {
            const name = loc.name || loc.location || loc.city || 'Unknown';
            const count = loc.count || loc.frequency || loc.total || 0;
            
            html += `
                <tr>
                    <td class="ao-tbl-rank">${i + 1}</td>
                    <td class="ao-tbl-name">${name}</td>
                    <td class="ao-tbl-num">${count.toLocaleString()}</td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        body.innerHTML = html;
    },

    // 🔥 IMPROVED: Render Influencers with Profile Images
    renderInfluencersTable(influencers, card) {
        const body = card.querySelector('.ao-card-body');
        
        if (influencers.length === 0) {
            body.innerHTML = '<div class="ao-empty">No influencers data available</div>';
            return;
        }

        if (influencers.length > 8) {
            document.getElementById('influencersViewAllBtn').style.display = 'flex';
        }

        let html = '<div class="influencers-list">';

        influencers.slice(0, 8).forEach((inf, i) => {
            const profileImg = inf.profile_image || 'https://abs.twimg.com/sticky/default_profile_images/default_profile_400x400.png';
            const verified = inf.verified ? '<span class="verified-badge">✓</span>' : '';
            const followers = this.formatNumber(inf.followers_count);
            const mentions = this.formatNumber(inf.mentions);
            
            html += `
                <div class="influencer-item">
                    <div class="influencer-rank">${i + 1}</div>
                    <img src="${profileImg}" alt="${inf.username}" class="influencer-avatar" 
                         onerror="this.src='https://abs.twimg.com/sticky/default_profile_images/default_profile_400x400.png'">
                    <div class="influencer-info">
                        <div class="influencer-name">
                            ${inf.name}
                            ${verified}
                        </div>
                        <div class="influencer-username">@${inf.username}</div>
                        ${inf.location ? `<div class="influencer-location">📍 ${inf.location}</div>` : ''}
                    </div>
                    <div class="influencer-stats">
                        <div class="stat-item">
                            <div class="stat-value">${followers}</div>
                            <div class="stat-label">Followers</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">${mentions}</div>
                            <div class="stat-label">Mentions</div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        body.innerHTML = html;
    },

    // 🔥 NEW: Format Number Helper
    formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toLocaleString();
    },

    showError(card) {
        const body = card.querySelector('.ao-card-body');
        if (body) {
            body.innerHTML = '<div class="ao-empty">Failed to load data</div>';
        }
    },

    setupFilterButton() {
        document.getElementById('aoBtnApply').addEventListener('click', () => {
            const pid = document.getElementById('aoProject').value;
            const sd = document.getElementById('aoStartDate').value;
            const ed = document.getElementById('aoEndDate').value;
            const media = document.getElementById('aoMedia').value;
            
            if (!sd || !ed) return;
            
            const params = new URLSearchParams(window.location.search);
            params.set('project_id', pid);
            params.set('start_date', sd);
            params.set('end_date', ed);
            params.set('media', media);
            window.location.search = params.toString();
        });
    },

    setupModalSearches() {
        // Hashtags search
        document.getElementById('hashtagsSearch')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const items = document.querySelectorAll('#hashtagsModalBody .ao-tbl tbody tr');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Locations search
        document.getElementById('locationsSearch')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const items = document.querySelectorAll('#locationsModalBody .ao-tbl tbody tr');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Influencers search
        document.getElementById('influencersSearch')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const items = document.querySelectorAll('#influencersModalBody .influencer-item-modal');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
};

// ────────────────────────────────────────────
// TOPIC CHART SWITCHING
// ────────────────────────────────────────────
function switchTopicChart(type, button) {
    document.querySelectorAll('.ao-chart-btn[data-chart]').forEach(btn => {
        btn.classList.remove('active');
    });
    button.classList.add('active');
    
    document.querySelectorAll('.ao-chart-view').forEach(view => {
        view.classList.remove('active');
    });
    
    if (type === 'wordcloud') {
        document.getElementById('topicWordCloud').classList.add('active');
    } else if (type === 'bar') {
        document.getElementById('topicBarChart').classList.add('active');
        if (!AnalyticsOverviewLoader.charts.topicBar) {
            renderTopicBarChart();
        }
    } else if (type === 'pie') {
        document.getElementById('topicPieChart').classList.add('active');
        if (!AnalyticsOverviewLoader.charts.topicPie) {
            renderTopicPieChart();
        }
    }
}

function renderTopicBarChart() {
    const ctx = document.getElementById('topicBarCanvas').getContext('2d');
    const topTopics = AnalyticsOverviewLoader.topicsData.slice(0, 15);
    
    AnalyticsOverviewLoader.charts.topicBar = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topTopics.map(t => t.name),
            datasets: [{
                label: 'Mentions',
                data: topTopics.map(t => t.count),
                backgroundColor: '#038047',
                borderRadius: 8,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    padding: 12,
                    titleFont: { size: 13, weight: 'bold', family: 'Poppins' },
                    bodyFont: { size: 12, family: 'Poppins' }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });
}

function renderTopicPieChart() {
    const ctx = document.getElementById('topicPieCanvas').getContext('2d');
    const topTopics = AnalyticsOverviewLoader.topicsData.slice(0, 10);
    
    const colors = [
        '#038047', '#04995a', '#27384A', '#8b5cf6', '#f59e0b',
        '#ef4444', '#10b981', '#3b82f6', '#ec4899', '#6366f1'
    ];
    
    AnalyticsOverviewLoader.charts.topicPie = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: topTopics.map(t => t.name),
            datasets: [{
                data: topTopics.map(t => t.count),
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: { size: 11, weight: 600, family: 'Poppins' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    padding: 12
                }
            }
        }
    });
}

// ────────────────────────────────────────────
// MODAL FUNCTIONS
// ────────────────────────────────────────────
function openHashtagsModal() {
    const modal = document.getElementById('hashtagsModal');
    const body = document.getElementById('hashtagsModalBody');
    
    let html = `
        <table class="ao-tbl">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th class="ao-tbl-left">Hashtag</th>
                    <th class="ao-tbl-right">Mentions</th>
                </tr>
            </thead>
            <tbody>
    `;

    AnalyticsOverviewLoader.hashtagsData.forEach((tag, i) => {
        let tagName = tag.name || 'unknown';
        const count = tag.size || 0;
        
        if (!tagName.startsWith('#')) tagName = '#' + tagName;
        
        html += `
            <tr>
                <td class="ao-tbl-rank">${i + 1}</td>
                <td class="ao-tbl-name" style="color:#27384A;">${tagName}</td>
                <td class="ao-tbl-num">${count.toLocaleString()}</td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    body.innerHTML = html;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeHashtagsModal() {
    document.getElementById('hashtagsModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('hashtagsSearch').value = '';
}

function openLocationsModal() {
    const modal = document.getElementById('locationsModal');
    const body = document.getElementById('locationsModalBody');
    
    let html = `
        <table class="ao-tbl">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th class="ao-tbl-left">Location</th>
                    <th class="ao-tbl-right">Count</th>
                </tr>
            </thead>
            <tbody>
    `;

    AnalyticsOverviewLoader.locationsData.forEach((loc, i) => {
        const name = loc.name || loc.location || loc.city || 'Unknown';
        const count = loc.count || loc.frequency || loc.total || 0;
        
        html += `
            <tr>
                <td class="ao-tbl-rank">${i + 1}</td>
                <td class="ao-tbl-name">${name}</td>
                <td class="ao-tbl-num">${count.toLocaleString()}</td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    body.innerHTML = html;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLocationsModal() {
    document.getElementById('locationsModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('locationsSearch').value = '';
}

// 🔥 IMPROVED: Influencers Modal with Better Layout
function openInfluencersModal() {
    const modal = document.getElementById('influencersModal');
    const body = document.getElementById('influencersModalBody');
    
    let html = '<div class="influencers-list-modal">';

    AnalyticsOverviewLoader.influencersData.forEach((inf, i) => {
        const profileImg = inf.profile_image || 'https://abs.twimg.com/sticky/default_profile_images/default_profile_400x400.png';
        const verified = inf.verified ? '<span class="verified-badge">✓</span>' : '';
        const followers = AnalyticsOverviewLoader.formatNumber(inf.followers_count);
        const mentions = AnalyticsOverviewLoader.formatNumber(inf.mentions);
        const bio = inf.description ? `<div class="influencer-bio">${truncateText(inf.description, 80)}</div>` : '';
        
        html += `
            <div class="influencer-item-modal">
                <div class="influencer-left">
                    <div class="influencer-rank">${i + 1}</div>
                    <img src="${profileImg}" alt="${inf.username}" class="influencer-avatar" 
                         onerror="this.src='https://abs.twimg.com/sticky/default_profile_images/default_profile_400x400.png'">
                    <div class="influencer-info">
                        <div class="influencer-name">
                            ${inf.name}
                            ${verified}
                        </div>
                        <div class="influencer-username">@${inf.username}</div>
                        ${bio}
                        ${inf.location ? `<div class="influencer-location">📍 ${inf.location}</div>` : ''}
                    </div>
                </div>
                <div class="influencer-stats">
                    <div class="stat-item">
                        <div class="stat-value">${followers}</div>
                        <div class="stat-label">Followers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">${mentions}</div>
                        <div class="stat-label">Mentions</div>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    body.innerHTML = html;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeInfluencersModal() {
    document.getElementById('influencersModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('influencersSearch').value = '';
}

// 🔥 NEW: Helper function for truncating text
function truncateText(text, maxLength) {
    if (!text || text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// Close modals on overlay click
window.addEventListener('click', event => {
    ['hashtagsModal', 'locationsModal', 'influencersModal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
});

// Close modals on Escape key
document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        ['hashtagsModal', 'locationsModal', 'influencersModal'].forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && modal.classList.contains('active')) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    }
});

// ────────────────────────────────────────────
// INITIALIZE ON PAGE LOAD
// ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    AnalyticsOverviewLoader.init();
});
</script>
@endsection