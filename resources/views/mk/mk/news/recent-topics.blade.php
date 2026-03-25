@extends('mk.layouts.app')

@section('title', 'Recent Topics - SMADIMENT')

@section('styles')
<style>
  :root {
    --primary-green: #038047;
    --primary-green-dark: #026738;
    --text-primary: #1a202c;
    --text-secondary: #64748b;
    --bg-white: #ffffff;
    --bg-gray-50: #f8fafc;
    --border-gray: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  }

  .news-container {
    padding: 24px;
    background: var(--bg-gray-50);
    min-height: 100vh;
  }

  /* Page Header */
  .page-header { margin-bottom: 28px; }
  .page-header h1 {
    font-size: 28px; font-weight: 700;
    color: var(--text-primary); margin: 0 0 6px 0;
  }
  .page-header p {
    font-size: 14px; color: var(--text-secondary);
    font-weight: 500; margin: 0;
  }

  /* API Version Badge */
  .api-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(3, 128, 71, 0.1);
    border: 1px solid rgba(3, 128, 71, 0.2);
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    color: var(--primary-green);
    margin-left: 12px;
  }

  /* Tab Navigation */
  .tab-nav {
    display: flex; gap: 8px;
    background: var(--bg-white);
    padding: 8px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-gray);
  }

  .tab-btn {
    flex: 1;
    padding: 12px 24px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .tab-btn:hover {
    background: var(--bg-gray-50);
    color: var(--text-primary);
  }

  .tab-btn.active {
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(3, 128, 71, 0.2);
  }

  .tab-btn svg {
    width: 18px;
    height: 18px;
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  /* Tab Content */
  .tab-content {
    display: none;
  }

  .tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Issue Cards */
  .issues-grid {
    display: grid;
    gap: 20px;
  }

  .issue-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }

  .issue-card::before {
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

  .issue-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    border-color: rgba(3, 128, 71, 0.25);
    transform: translateY(-2px);
  }

  .issue-card:hover::before {
    opacity: 1;
  }

  .issue-header {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
  }

  .issue-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(3, 128, 71, 0.1) 0%, rgba(3, 128, 71, 0.05) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .issue-icon svg {
    width: 22px;
    height: 22px;
    stroke: var(--primary-green);
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .issue-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 8px 0;
    line-height: 1.4;
  }

  .issue-description {
    font-size: 14px;
    color: var(--text-secondary);
    line-height: 1.7;
    margin-bottom: 16px;
  }

  /* URLs List - for v2 */
  .issue-urls {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 16px;
  }

  .issue-url-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: var(--bg-gray-50);
    border-radius: 8px;
    font-size: 12px;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid transparent;
  }

  .issue-url-item:hover {
    background: var(--primary-green);
    border-color: var(--primary-green);
    color: #ffffff;
    transform: translateX(4px);
  }

  .issue-url-item svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2.5;
    stroke-linecap: round;
    flex-shrink: 0;
  }

  .issue-url-text {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 500;
  }

  /* Single Link - for v1 */
  .issue-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: var(--bg-gray-50);
    border: 1px solid var(--border-gray);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--primary-green);
    text-decoration: none;
    transition: all 0.2s;
    margin-top: 12px;
  }

  .issue-link:hover {
    background: var(--primary-green);
    border-color: var(--primary-green);
    color: #ffffff;
    transform: translateX(4px);
  }

  .issue-link svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2.5;
    stroke-linecap: round;
  }

  .issue-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    background: rgba(3, 128, 71, 0.1);
    color: var(--primary-green);
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    margin-top: 12px;
  }

  /* Loading & Empty */
  .loading-skeleton {
    background: linear-gradient(90deg, var(--bg-gray-50) 25%, #e2e8f0 50%, var(--bg-gray-50) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
    border-radius: 8px;
  }

  @keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .skeleton-card {
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
  }

  .skeleton-title {
    height: 24px;
    width: 70%;
    margin-bottom: 12px;
  }

  .skeleton-text {
    height: 16px;
    width: 100%;
    margin-bottom: 8px;
  }

  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    background: var(--bg-white);
    border: 1px solid var(--border-gray);
    border-radius: 16px;
  }

  .empty-icon {
    width: 64px;
    height: 64px;
    color: var(--border-gray);
    margin-bottom: 16px;
  }

  .empty-text {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-secondary);
  }

  @media (max-width: 768px) {
    .news-container {
      padding: 16px;
    }

    .tab-nav {
      flex-direction: column;
    }

    .page-header h1 {
      font-size: 22px;
    }

    .issue-title {
      font-size: 16px;
    }
  }
</style>
@endsection

@section('content')
<div class="news-container">

  <!-- Page Header -->
  <div class="page-header">
    <h1>
      Recent Topics
      @if(isset($apiVersion))
      <span class="api-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
        API {{ strtoupper($apiVersion) }}
      </span>
      @endif
    </h1>
    <p>Trending news topics from various sources</p>
  </div>

  <!-- Tab Navigation -->
  <div class="tab-nav">
    <button class="tab-btn {{ $level === 'internasional' ? 'active' : '' }}" 
            onclick="switchTab('internasional')" 
            id="tab-internasional">
      <svg viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <line x1="2" y1="12" x2="22" y2="12"/>
        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
      </svg>
      Internasional
    </button>
    <button class="tab-btn {{ $level === 'nasional' ? 'active' : '' }}" 
            onclick="switchTab('nasional')" 
            id="tab-nasional">
      <svg viewBox="0 0 24 24">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      Nasional
    </button>
  </div>

  <!-- Tab Content: Internasional -->
  <div class="tab-content {{ $level === 'internasional' ? 'active' : '' }}" id="content-internasional">
    <div class="issues-grid" id="issues-internasional">
      @if($level === 'internasional')
        @if(count($issues) > 0)
          @foreach($issues as $issue)
          <div class="issue-card">
            <div class="issue-header">
              <div class="issue-icon">
                <svg viewBox="0 0 24 24">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
              </div>
              <div style="flex: 1;">
                <h3 class="issue-title">{{ $issue['judul'] ?? 'Untitled Issue' }}</h3>
              </div>
            </div>
            
            {{-- Show description if available (v1) --}}
            @if(!empty($issue['deskripsi']))
            <p class="issue-description">{{ $issue['deskripsi'] }}</p>
            @endif

            {{-- Show multiple URLs if available (v2) --}}
            @if(isset($issue['urls']) && count($issue['urls']) > 1)
            <div class="issue-urls">
              @foreach($issue['urls'] as $url)
              <a href="{{ $url }}" target="_blank" class="issue-url-item">
                <svg viewBox="0 0 24 24">
                  <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                  <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
                <span class="issue-url-text">{{ $url }}</span>
              </a>
              @endforeach
            </div>
            <span class="issue-badge">{{ count($issue['urls']) }} sources</span>
            
            {{-- Show single link if available (v1) --}}
            @elseif(!empty($issue['referensi']))
            <a href="{{ $issue['referensi'] }}" target="_blank" class="issue-link">
              Read More
              <svg viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
              </svg>
            </a>
            @endif
          </div>
          @endforeach
        @else
          <div class="empty-state">
            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span class="empty-text">No international topics available</span>
          </div>
        @endif
      @else
        <div class="skeleton-card">
          <div class="loading-skeleton skeleton-title"></div>
          <div class="loading-skeleton skeleton-text"></div>
          <div class="loading-skeleton skeleton-text"></div>
        </div>
      @endif
    </div>
  </div>

  <!-- Tab Content: Nasional -->
  <div class="tab-content {{ $level === 'nasional' ? 'active' : '' }}" id="content-nasional">
    <div class="issues-grid" id="issues-nasional">
      @if($level === 'nasional')
        @if(count($issues) > 0)
          @foreach($issues as $issue)
          <div class="issue-card">
            <div class="issue-header">
              <div class="issue-icon">
                <svg viewBox="0 0 24 24">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
              </div>
              <div style="flex: 1;">
                <h3 class="issue-title">{{ $issue['judul'] ?? 'Untitled Issue' }}</h3>
              </div>
            </div>
            
            @if(!empty($issue['deskripsi']))
            <p class="issue-description">{{ $issue['deskripsi'] }}</p>
            @endif

            @if(isset($issue['urls']) && count($issue['urls']) > 1)
            <div class="issue-urls">
              @foreach($issue['urls'] as $url)
              <a href="{{ $url }}" target="_blank" class="issue-url-item">
                <svg viewBox="0 0 24 24">
                  <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                  <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
                <span class="issue-url-text">{{ $url }}</span>
              </a>
              @endforeach
            </div>
            <span class="issue-badge">{{ count($issue['urls']) }} sources</span>
            
            @elseif(!empty($issue['referensi']))
            <a href="{{ $issue['referensi'] }}" target="_blank" class="issue-link">
              Read More
              <svg viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
              </svg>
            </a>
            @endif
          </div>
          @endforeach
        @else
          <div class="empty-state">
            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span class="empty-text">No national topics available</span>
          </div>
        @endif
      @else
        <div class="skeleton-card">
          <div class="loading-skeleton skeleton-title"></div>
          <div class="loading-skeleton skeleton-text"></div>
          <div class="loading-skeleton skeleton-text"></div>
        </div>
      @endif
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
  async function switchTab(level) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(`tab-${level}`).classList.add('active');

    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    document.getElementById(`content-${level}`).classList.add('active');

    const container = document.getElementById(`issues-${level}`);
    if (container.querySelector('.skeleton-card')) {
      await loadTopics(level);
    }

    const url = new URL(window.location.href);
    url.searchParams.set('level', level);
    window.history.pushState({}, '', url.toString());
  }

  async function loadTopics(level) {
    const container = document.getElementById(`issues-${level}`);
    
    try {
      const response = await fetch(`/mk/api/news/recent-topics?level=${level}&size=10`);
      const result = await response.json();

      if (!result.success || !result.data || result.data.length === 0) {
        container.innerHTML = `
          <div class="empty-state">
            <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span class="empty-text">No ${level} topics available</span>
          </div>
        `;
        return;
      }

      let html = '';
      result.data.forEach(issue => {
        const urls = issue.urls || [];
        const hasDescription = issue.deskripsi && issue.deskripsi.trim() !== '';
        
        html += `<div class="issue-card">
          <div class="issue-header">
            <div class="issue-icon">
              <svg viewBox="0 0 24 24">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
              </svg>
            </div>
            <div style="flex: 1;">
              <h3 class="issue-title">${issue.judul || 'Untitled Issue'}</h3>
            </div>
          </div>`;

        if (hasDescription) {
          html += `<p class="issue-description">${issue.deskripsi}</p>`;
        }

        if (urls.length > 1) {
          html += `<div class="issue-urls">`;
          urls.forEach(url => {
            html += `<a href="${url}" target="_blank" class="issue-url-item">
              <svg viewBox="0 0 24 24">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
              </svg>
              <span class="issue-url-text">${url}</span>
            </a>`;
          });
          html += `</div><span class="issue-badge">${urls.length} sources</span>`;
        } else if (issue.referensi) {
          html += `<a href="${issue.referensi}" target="_blank" class="issue-link">
            Read More
            <svg viewBox="0 0 24 24">
              <line x1="5" y1="12" x2="19" y2="12"/>
              <polyline points="12 5 19 12 12 19"/>
            </svg>
          </a>`;
        }

        html += `</div>`;
      });

      container.innerHTML = html;

    } catch (error) {
      console.error('Error loading topics:', error);
      container.innerHTML = `
        <div class="empty-state">
          <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <span class="empty-text">Failed to load topics</span>
        </div>
      `;
    }
  }
</script>
@endsection