@extends('mk.layouts.app')

@section('title', 'Projects - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Projects Overview</h2>
    <div class="page-subtitle">Browse and manage all your social media monitoring projects</div>
  </div>
  <div style="display: flex; gap: 12px; align-items: center;">
    <span style="background: var(--beige); padding: 8px 16px; border-radius: 10px; color: var(--brown); font-weight: 800; font-size: 14px;">
      {{ count($projects) }} Projects
    </span>
  </div>
</div>

<!-- Pagination Controls -->
<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
  <div style="display: flex; gap: 12px;">
    <a href="/mk/projects?start={{ max(0, $start - $limit) }}&limit={{ $limit }}" class="action-btn">
      ← Previous
    </a>
    <a href="/mk/projects?start={{ $start + $limit }}&limit={{ $limit }}" class="action-btn">
      Next →
    </a>
  </div>
  
  <div style="color: var(--dark-teal); font-weight: 600;">
    <span>Showing</span>
    <strong style="color: var(--brown);">{{ $start + 1 }}</strong>
    <span>to</span>
    <strong style="color: var(--brown);">{{ min($start + $limit, $start + count($projects)) }}</strong>
  </div>
</div>

<!-- Search Bar -->
<div style="margin-bottom: 32px;">
  <div style="position: relative; max-width: 600px;">
    <span style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); font-size: 20px;">🔍</span>
    <input
      id="searchInput"
      type="text"
      placeholder="Search by title, keywords, group, or media type..."
      style="width: 100%; padding: 16px 20px; padding-left: 56px; border: 2px solid var(--beige); border-radius: 16px; background: var(--white); font-family: 'Montserrat', sans-serif; font-size: 15px; font-weight: 500; color: var(--dark-teal); outline: none; transition: all 0.2s;"
      onfocus="this.style.borderColor='var(--brown)'; this.style.boxShadow='0 0 0 4px rgba(82, 61, 53, 0.1)';"
      onblur="this.style.borderColor='var(--beige)'; this.style.boxShadow='none';"
    />
  </div>
</div>

<!-- Projects Grid -->
@if(count($projects) === 0)
  <div style="padding: 80px 20px; text-align: center;">
    <div style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;">📁</div>
    <div style="font-size: 18px; font-weight: 700; color: var(--dark-teal); margin-bottom: 8px;">
      No projects available
    </div>
    <div style="font-size: 14px; color: var(--sage);">
      Check API connection or try different pagination
    </div>
  </div>
@else
  <div id="projectList" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 24px;">
    @foreach($projects as $p)
      @php
        $id = $p['id'] ?? '-';
        $title = $p['title'] ?? 'Untitled Project';
        $group = $p['project_group_name'] ?? 'No Group';
        $type = $p['project_type'] ?? 'Unknown';
        $keywords = $p['keywords'] ?? 'No keywords';
        $media = $p['media_types'] ?? '';
        $mediaArr = array_filter(array_map('trim', explode(',', $media)));
        $search = strtolower($id.' '.$title.' '.$group.' '.$type.' '.$keywords.' '.$media);
      @endphp

      <div class="project-card" data-search="{{ $search }}" style="background: linear-gradient(135deg, var(--cream) 0%, var(--white) 100%); border: 2px solid var(--beige); border-radius: 16px; padding: 24px; transition: all 0.3s; position: relative; overflow: hidden;">
        
        <!-- Accent Bar -->
        <div style="position: absolute; left: 0; top: 0; width: 6px; height: 100%; background: linear-gradient(180deg, var(--brown), var(--tan)); opacity: 0; transition: opacity 0.3s;"></div>

        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
          <div style="background: var(--brown); color: var(--white); padding: 8px 16px; border-radius: 10px; font-size: 14px; font-weight: 900; letter-spacing: 0.5px;">
            #{{ $id }}
          </div>
          <button class="copy-btn" data-id="{{ $id }}" style="padding: 8px 16px; background: var(--white); border: 2px solid var(--beige); border-radius: 10px; color: var(--dark-teal); font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s;">
            Copy ID
          </button>
        </div>

        <!-- Title -->
        <h3 style="font-size: 18px; font-weight: 800; color: var(--dark-teal); margin-bottom: 16px; line-height: 1.3; min-height: 48px;">
          {{ $title }}
        </h3>

        <!-- Details Grid -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 16px;">
          <div>
            <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
              Group
            </div>
            <div style="font-size: 14px; font-weight: 600; color: var(--dark-teal);">
              {{ $group }}
            </div>
          </div>

          <div>
            <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
              Type
            </div>
            <div style="font-size: 14px; font-weight: 600; color: var(--dark-teal);">
              {{ $type }}
            </div>
          </div>
        </div>

        <!-- Keywords -->
        <div style="margin-bottom: 16px;">
          <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
            Keywords
          </div>
          <div style="font-size: 13px; font-weight: 600; color: var(--dark-teal); line-height: 1.4;">
            {{ Str::limit($keywords, 100) }}
          </div>
        </div>

        <!-- Media Types -->
        <div>
          <div style="font-size: 11px; font-weight: 800; color: var(--sage); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
            Media Types
          </div>
          <div style="display: flex; flex-wrap: wrap; gap: 6px;">
            @if(count($mediaArr) === 0)
              <span style="background: var(--beige); color: var(--brown); padding: 4px 12px; border-radius: 6px; font-size: 11px; font-weight: 700;">
                None
              </span>
            @else
              @foreach($mediaArr as $m)
                <span style="background: var(--beige); color: var(--brown); padding: 4px 12px; border-radius: 6px; font-size: 11px; font-weight: 700;">
                  {{ $m }}
                </span>
              @endforeach
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

<!-- Debug Section -->
<div class="section" style="margin-top: 40px;">
  <div class="section-header">
    <h3 class="section-title">API Response</h3>
  </div>
  <div class="section-body">
    <details>
      <summary class="debug-toggle">View Raw Data</summary>
      <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($raw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}</pre>
    </details>
  </div>
</div>

@endsection

@section('scripts')
<script>
  // Search
  const searchInput = document.getElementById('searchInput');
  const projectCards = document.querySelectorAll('.project-card');

  searchInput?.addEventListener('input', (e) => {
    const query = e.target.value.toLowerCase().trim();
    projectCards.forEach(card => {
      const searchData = card.getAttribute('data-search') || '';
      card.style.display = (!query || searchData.includes(query)) ? '' : 'none';
    });
  });

  // Copy ID
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.copy-btn');
    if (!btn) return;

    const id = btn.getAttribute('data-id');
    try {
      await navigator.clipboard.writeText(id);
      const orig = btn.textContent;
      btn.textContent = 'Copied!';
      btn.style.background = 'var(--sage)';
      btn.style.color = 'white';
      btn.style.borderColor = 'var(--sage)';

      setTimeout(() => {
        btn.textContent = orig;
        btn.style.background = 'var(--white)';
        btn.style.color = 'var(--dark-teal)';
        btn.style.borderColor = 'var(--beige)';
      }, 1500);
    } catch (err) {
      alert('Failed to copy: ' + id);
    }
  });

  // Card hover
  projectCards.forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.style.transform = 'translateX(8px)';
      this.style.borderColor = 'var(--brown)';
      this.style.boxShadow = '-8px 8px 24px rgba(82, 61, 53, 0.12)';
      this.querySelector('div[style*="opacity: 0"]').style.opacity = '1';
    });

    card.addEventListener('mouseleave', function() {
      this.style.transform = '';
      this.style.borderColor = 'var(--beige)';
      this.style.boxShadow = '';
      this.querySelector('div[style*="opacity"]').style.opacity = '0';
    });
  });

  // Copy button hover
  document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
      if (this.textContent !== 'Copied!') {
        this.style.background = 'var(--brown)';
        this.style.color = 'white';
        this.style.borderColor = 'var(--brown)';
      }
    });
    
    btn.addEventListener('mouseleave', function() {
      if (this.textContent !== 'Copied!') {
        this.style.background = 'var(--white)';
        this.style.color = 'var(--dark-teal)';
        this.style.borderColor = 'var(--beige)';
      }
    });
  });
</script>

<style>
  .action-btn {
    padding: 12px 24px;
    border-radius: 12px;
    border: 2px solid var(--beige);
    background: var(--white);
    color: var(--dark-teal);
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
  }

  .action-btn:hover {
    background: var(--brown);
    color: var(--white);
    border-color: var(--brown);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(82, 61, 53, 0.15);
  }

  @media (max-width: 768px) {
    #projectList {
      grid-template-columns: 1fr;
    }
  }
</style>
@endsection