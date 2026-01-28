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
    <h3 class="section-title">📊 Quick Start Guide</h3>
  </div>
  
  <div class="section-body">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
      
      <!-- Analytics Card -->
      <div style="background: linear-gradient(135deg, var(--cream), var(--white)); border: 2px solid var(--beige); border-radius: 16px; padding: 24px;">
        <div style="font-size: 32px; margin-bottom: 12px;">💬</div>
        <h4 style="font-size: 18px; font-weight: 800; color: var(--dark-teal); margin-bottom: 8px;">Sentiment Analysis</h4>
        <p style="font-size: 14px; color: var(--sage); font-weight: 600; margin-bottom: 16px;">Track positive, neutral, and negative sentiment across your projects</p>
        <a href="{{ route('mk.sentiment') }}" class="action-btn primary" style="width: 100%; justify-content: center;">
          View Sentiment →
        </a>
      </div>

      <!-- Geographic Card -->
      <div style="background: linear-gradient(135deg, var(--cream), var(--white)); border: 2px solid var(--beige); border-radius: 16px; padding: 24px;">
        <div style="font-size: 32px; margin-bottom: 12px;">🌍</div>
        <h4 style="font-size: 18px; font-weight: 800; color: var(--dark-teal); margin-bottom: 8px;">Geographic Data</h4>
        <p style="font-size: 14px; color: var(--sage); font-weight: 600; margin-bottom: 16px;">Analyze geographic distribution of social media activity</p>
        <a href="{{ route('mk.geographic') }}" class="action-btn primary" style="width: 100%; justify-content: center;">
          View Geographic →
        </a>
      </div>

      <!-- Demographics Card -->
      <div style="background: linear-gradient(135deg, var(--cream), var(--white)); border: 2px solid var(--beige); border-radius: 16px; padding: 24px;">
        <div style="font-size: 32px; margin-bottom: 12px;">👥</div>
        <h4 style="font-size: 18px; font-weight: 800; color: var(--dark-teal); margin-bottom: 8px;">Author Demographics</h4>
        <p style="font-size: 14px; color: var(--sage); font-weight: 600; margin-bottom: 16px;">Explore age, gender, and organization type distribution</p>
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
    <h3 class="section-title">📁 Available Projects</h3>
    <span style="background: var(--white); padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 800; color: var(--brown);">
      {{ count($projects) }} Projects
    </span>
  </div>
  
  <div class="section-body">
    @if(count($projects) === 0)
      <div class="empty-state">
        <div class="empty-icon">📁</div>
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
                <td style="font-weight: 800; color: var(--brown);">#{{ $id }}</td>
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
      <h3 class="section-title">🏷️ Categories</h3>
    </div>
    <div class="section-body">
      <p style="font-size: 14px; color: var(--sage); font-weight: 600; margin-bottom: 16px;">
        Analyze content categories and topics
      </p>
      <a href="{{ route('mk.categories') }}" class="action-btn" style="width: 100%; justify-content: center;">
        View Categories →
      </a>
    </div>
  </div>

  <div class="section">
    <div class="section-header">
      <h3 class="section-title">📈 Engagement</h3>
    </div>
    <div class="section-body">
      <p style="font-size: 14px; color: var(--sage); font-weight: 600; margin-bottom: 16px;">
        Track reach, URLs, and active users
      </p>
      <a href="{{ route('mk.engagement.reach') }}" class="action-btn" style="width: 100%; justify-content: center;">
        View Metrics →
      </a>
    </div>
  </div>

</div>

@endsection