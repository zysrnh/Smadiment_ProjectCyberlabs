@extends('mk.layouts.app')

@section('title', 'Dashboard - SMADIMENT')

@section('content')

<!-- Top Bar -->
<div class="top-bar">
  <div class="page-title">
    <h2>Analytics Dashboard</h2>
    <div class="page-subtitle">Welcome to SMADIMENT - Social Media Analytics Platform</div>
  </div>
</div>

<!-- Welcome Section -->
<div class="section">
  <div class="section-header">
    <h3 class="section-title">Quick Start Guide</h3>
  </div>
  
  <div class="section-body">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
      
      <!-- Analytics Card -->
      <div style="background: linear-gradient(135deg, var(--white), #FAFBFC); border: 2px solid var(--light-gray); border-radius: 16px; padding: 24px;">
        <div style="width: 48px; height: 48px; background: var(--light-gray); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
          <svg style="width: 24px; height: 24px; stroke: var(--primary-green); fill: none; stroke-width: 2;" viewBox="0 0 24 24">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
        </div>
        <h4 style="font-size: 18px; font-weight: 800; color: var(--dark-blue); margin-bottom: 8px;">Sentiment Analysis</h4>
        <p style="font-size: 14px; color: var(--dark-blue); opacity: 0.7; font-weight: 600; margin-bottom: 16px;">Track positive, neutral sentiment across your projects</p>
        <a href="{{ route('mk.sentiment') }}" class="action-btn primary" style="width: 100%; justify-content: center;">
          View Sentiment →
        </a>
      </div>

      <!-- Geographic Card -->
      <div style="background: linear-gradient(135deg, var(--white), #FAFBFC); border: 2px solid var(--light-gray); border-radius: 16px; padding: 24px;">
        <div style="width: 48px; height: 48px; background: var(--light-gray); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
          <svg style="width: 24px; height: 24px; stroke: var(--primary-green); fill: none; stroke-width: 2;" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="2" y1="12" x2="22" y2="12"/>
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
          </svg>
        </div>
        <h4 style="font-size: 18px; font-weight: 800; color: var(--dark-blue); margin-bottom: 8px;">Geographic Data</h4>
        <p style="font-size: 14px; color: var(--dark-blue); opacity: 0.7; font-weight: 600; margin-bottom: 16px;">Analyze geographic distribution of social media activity</p>
        <a href="{{ route('mk.geographic') }}" class="action-btn primary" style="width: 100%; justify-content: center;">
          View Geographic →
        </a>
      </div>

      <!-- Demographics Card -->
      <div style="background: linear-gradient(135deg, var(--white), #FAFBFC); border: 2px solid var(--light-gray); border-radius: 16px; padding: 24px;">
        <div style="width: 48px; height: 48px; background: var(--light-gray); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
          <svg style="width: 24px; height: 24px; stroke: var(--primary-green); fill: none; stroke-width: 2;" viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <h4 style="font-size: 18px; font-weight: 800; color: var(--dark-blue); margin-bottom: 8px;">Author Demographics</h4>
        <p style="font-size: 14px; color: var(--dark-blue); opacity: 0.7; font-weight: 600; margin-bottom: 16px;">Explore age, gender, and organization type distribution</p>
        <a href="{{ route('mk.authors.age') }}" class="action-btn primary" style="width: 100%; justify-content: center;">
          View Demographics →
        </a>
      </div>

    </div>
  </div>
</div>

<!-- Available Projects -->
<div class="section">
  <div class="section-header">
    <h3 class="section-title">Available Projects</h3>
    <span style="background: var(--white); padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 800; color: var(--dark-blue);">
      {{ count($projects) }} Projects
    </span>
  </div>
  
  <div class="section-body">
    @if(count($projects) === 0)
      <div class="empty-state">
        <div style="width: 80px; height: 80px; background: var(--light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
          <svg style="width: 40px; height: 40px; stroke: var(--dark-blue); fill: none; stroke-width: 2; opacity: 0.3;" viewBox="0 0 24 24">
            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
          </svg>
        </div>
        <div class="empty-text">No projects available. Check API connection.</div>
      </div>
    @else
      <div class="data-table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Project Title</th>
              <th>Group</th>
              <th>Type</th>
              <th>Media Types</th>
            </tr>
          </thead>
          <tbody>
            @foreach(array_slice($projects, 0, 10) as $p)
              @php
                $id = $p['id'] ?? '-';
                $title = $p['title'] ?? 'Untitled Project';
                $group = $p['project_group_name'] ?? 'No Group';
                $type = $p['project_type'] ?? 'Unknown';
                $media = $p['media_types'] ?? 'None';
              @endphp
              <tr>
                <td style="font-weight: 800; color: var(--primary-green);">#{{ $id }}</td>
                <td style="font-weight: 700;">{{ $title }}</td>
                <td>{{ $group }}</td>
                <td>{{ $type }}</td>
                <td>{{ $media }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if(count($projects) > 10)
        <div style="margin-top: 16px; text-align: center;">
          <a href="{{ route('mk.projects') }}" class="action-btn primary">
            View All Projects ({{ count($projects) }}) →
          </a>
        </div>
      @endif
    @endif
  </div>
</div>

<!-- Features Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-top: 24px;">
  
  <div class="section">
    <div class="section-header">
      <h3 class="section-title">Categories</h3>
    </div>
    <div class="section-body">
      <div style="width: 48px; height: 48px; background: var(--light-gray); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
        <svg style="width: 24px; height: 24px; stroke: var(--primary-green); fill: none; stroke-width: 2;" viewBox="0 0 24 24">
          <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
          <line x1="7" y1="7" x2="7.01" y2="7"/>
        </svg>
      </div>
      <p style="font-size: 14px; color: var(--dark-blue); opacity: 0.7; font-weight: 600; margin-bottom: 16px;">
        Analyze content categories and topics
      </p>
      <a href="{{ route('mk.categories') }}" class="action-btn" style="width: 100%; justify-content: center;">
        View Categories →
      </a>
    </div>
  </div>

  <div class="section">
    <div class="section-header">
      <h3 class="section-title">Engagement</h3>
    </div>
    <div class="section-body">
      <div style="width: 48px; height: 48px; background: var(--light-gray); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
        <svg style="width: 24px; height: 24px; stroke: var(--primary-green); fill: none; stroke-width: 2;" viewBox="0 0 24 24">
          <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
      </div>
      <p style="font-size: 14px; color: var(--dark-blue); opacity: 0.7; font-weight: 600; margin-bottom: 16px;">
        Track reach, URLs, and active users
      </p>
      <a href="{{ route('mk.engagement.reach') }}" class="action-btn" style="width: 100%; justify-content: center;">
        View Metrics →
      </a>
    </div>
  </div>

</div>

@endsection