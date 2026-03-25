@extends('mk.layouts.app')

@section('title', 'Recent Topics - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Recent News Topics</h2>
    <div class="page-subtitle">Trending topics and latest news from various sources</div>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
  
  <!-- Main Content -->
  <div>
    
    <!-- Topics Cards -->
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Trending Topics</h3>
        <span class="item-count">{{ is_array($topics) ? count($topics) : 0 }}</span>
      </div>
      
      <div class="section-body">
        @if(empty($topics) || !is_array($topics) || count($topics) === 0)
          <div class="empty-state">
            <div class="empty-icon">📰</div>
            <div class="empty-text">No recent topics available for this level.</div>
          </div>
        @else
          <div style="display: flex; flex-direction: column; gap: 20px;">
            @foreach($topics as $index => $topic)
              @php
                // Force $index to integer to prevent type errors
                $indexNum = is_int($index) ? $index : (int) $index;
                
                $title = $topic['title'] ?? $topic['headline'] ?? 'Untitled';
                $summary = $topic['summary'] ?? $topic['description'] ?? $topic['content'] ?? '';
                $source = $topic['source'] ?? $topic['publisher'] ?? 'Unknown Source';
                $date = $topic['date'] ?? $topic['published_at'] ?? $topic['created_at'] ?? null;
                $url = $topic['url'] ?? $topic['link'] ?? null;
                $category = $topic['category'] ?? null;
              @endphp

              <div class="topic-card" style="background: linear-gradient(135deg, var(--cream) 0%, var(--white) 100%); border: 2px solid var(--beige); border-radius: 16px; padding: 24px; transition: all 0.3s; position: relative; overflow: hidden;">
                
                <!-- Accent Bar -->
                <div style="position: absolute; left: 0; top: 0; width: 6px; height: 100%; background: linear-gradient(180deg, var(--brown), var(--tan)); opacity: 0; transition: opacity 0.3s;"></div>

                <!-- Header -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                  <div style="background: var(--brown); color: var(--white); padding: 8px 16px; border-radius: 10px; font-size: 14px; font-weight: 900; letter-spacing: 0.5px;">
                    #{{ $indexNum + 1 }}
                  </div>
                  @if($category)
                    <span style="background: var(--beige); color: var(--brown); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700;">
                      {{ $category }}
                    </span>
                  @endif
                </div>

                <!-- Title -->
                <h3 style="font-size: 20px; font-weight: 800; color: var(--dark-teal); margin-bottom: 12px; line-height: 1.3;">
                  @if($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" style="color: var(--dark-teal); text-decoration: none;">
                      {{ $title }}
                    </a>
                  @else
                    {{ $title }}
                  @endif
                </h3>

                <!-- Summary -->
                @if($summary)
                  <p style="font-size: 14px; color: var(--dark-teal); line-height: 1.6; margin-bottom: 16px; opacity: 0.8;">
                    {{ Str::limit($summary, 200) }}
                  </p>
                @endif

                <!-- Meta Info -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 16px; border-top: 2px solid var(--beige);">
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 12px; font-weight: 700; color: var(--sage);">
                      📰 {{ $source }}
                    </span>
                    @if($date)
                      <span style="font-size: 12px; font-weight: 600; color: var(--sage);">
                        📅 {{ $date }}
                      </span>
                    @endif
                  </div>
                  @if($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" style="padding: 8px 16px; background: var(--brown); border-radius: 8px; color: white; text-decoration: none; font-size: 13px; font-weight: 700; transition: all 0.2s;">
                      Read More →
                    </a>
                  @endif
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
    <div class="section">
      <div class="section-header">
        <h3 class="section-title">Filter Options</h3>
      </div>
      
      <div class="section-body">
        <form method="GET" action="{{ route('mk.topics') }}">
          
          <!-- Level Filter -->
          <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
              News Level
            </label>
            <select name="level" style="width: 100%; padding: 12px 16px; border: 2px solid var(--beige); border-radius: 12px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark-teal); outline: none;">
              <option value="internasional" {{ $level === 'internasional' ? 'selected' : '' }}>International</option>
              <option value="nasional" {{ $level === 'nasional' ? 'selected' : '' }}>National</option>
              <option value="lokal" {{ $level === 'lokal' ? 'selected' : '' }}>Local</option>
            </select>
          </div>

          <!-- Size Filter -->
          <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
              Number of Topics
            </label>
            <input type="number" name="size" value="{{ $size }}" min="1" max="20" style="width: 100%; padding: 12px 16px; border: 2px solid var(--beige); border-radius: 12px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; color: var(--dark-teal); outline: none;" />
          </div>

          <!-- Load Button -->
          <button type="submit" style="width: 100%; padding: 16px; background: var(--brown); color: white; border: none; border-radius: 12px; font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 800; cursor: pointer; transition: all 0.2s;">
            Load Topics
          </button>

          <div style="margin-top: 16px; padding: 12px; background: var(--beige); border-radius: 8px; font-size: 12px; color: var(--brown); font-weight: 600; text-align: center;">
            Data from /recenttopics endpoint
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

@endsection

@section('scripts')
<script>
  // Topic card hover effect
  document.querySelectorAll('.topic-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.transform = 'translateX(8px)';
      this.style.borderColor = 'var(--brown)';
      this.style.boxShadow = '-8px 8px 24px rgba(82, 61, 53, 0.12)';
      const accentBar = this.querySelector('div[style*="opacity: 0"]');
      if (accentBar) {
        accentBar.style.opacity = '1';
      }
    });

    card.addEventListener('mouseleave', function() {
      this.style.transform = '';
      this.style.borderColor = 'var(--beige)';
      this.style.boxShadow = '';
      const accentBar = this.querySelector('div[style*="opacity"]');
      if (accentBar) {
        accentBar.style.opacity = '0';
      }
    });
  });
</script>
@endsection