<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SMADIMENT - Analytics Platform</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <style>
    :root {
      --dark-teal: #192338;
      --brown: #31487A;
      --sage: #8FB3E2;
      --tan: #8FB3E2;
      --beige: #D9E1F1;
      --cream: #F5F7FA;
      --white: #ffffff;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background: var(--cream);
      color: var(--dark-teal);
      line-height: 1.6;
      min-height: 100vh;
    }

    /* Sidebar Navigation */
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 280px;
      height: 100vh;
      background: var(--white);
      border-right: 3px solid var(--beige);
      padding: 32px 24px;
      z-index: 100;
      overflow-y: auto;
    }

    .logo {
      margin-bottom: 48px;
    }

    .logo h1 {
      font-size: 32px;
      font-weight: 900;
      color: var(--brown);
      letter-spacing: -1px;
      margin-bottom: 8px;
    }

    .logo p {
      font-size: 13px;
      color: var(--sage);
      font-weight: 600;
    }

    .nav-section {
      margin-bottom: 32px;
    }

    .nav-label {
      font-size: 11px;
      font-weight: 800;
      color: var(--sage);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 12px;
      padding-left: 12px;
    }

    .nav-item {
      padding: 14px 16px;
      margin-bottom: 6px;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 600;
      color: var(--dark-teal);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all 0.2s;
    }

    .nav-item:hover {
      background: var(--beige);
      color: var(--brown);
      transform: translateX(4px);
    }

    .nav-item.active {
      background: var(--brown);
      color: var(--white);
    }

    .nav-icon {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 11px;
      background: var(--beige);
      border-radius: 8px;
      color: var(--brown);
    }

    .nav-item.active .nav-icon {
      background: rgba(255, 255, 255, 0.2);
      color: var(--white);
    }

    /* Main Content */
    .main-content {
      margin-left: 280px;
      padding: 32px;
      min-height: 100vh;
    }

    /* Top Bar */
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;
      gap: 24px;
    }

    .page-title {
      flex: 1;
    }

    .page-title h2 {
      font-size: 28px;
      font-weight: 800;
      color: var(--dark-teal);
      margin-bottom: 4px;
    }

    .page-meta {
      display: flex;
      gap: 16px;
      align-items: center;
      font-size: 13px;
      color: var(--sage);
      font-weight: 600;
    }

    .meta-badge {
      background: var(--beige);
      padding: 6px 12px;
      border-radius: 8px;
      color: var(--brown);
      font-weight: 700;
    }

    .top-actions {
      display: flex;
      gap: 12px;
    }

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
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(82, 61, 53, 0.15);
    }

    .action-btn.primary {
      background: var(--brown);
      color: var(--white);
      border-color: var(--brown);
    }

    /* Search Bar */
    .search-container {
      margin-bottom: 32px;
    }

    .search-wrapper {
      position: relative;
      max-width: 600px;
    }

    .search-input {
      width: 100%;
      padding: 16px 20px;
      padding-left: 56px;
      border: 2px solid var(--beige);
      border-radius: 16px;
      background: var(--white);
      font-family: 'Montserrat', sans-serif;
      font-size: 15px;
      font-weight: 500;
      color: var(--dark-teal);
      outline: none;
      transition: all 0.2s;
    }

    .search-input:focus {
      border-color: var(--brown);
      box-shadow: 0 0 0 4px rgba(82, 61, 53, 0.1);
    }

    .search-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--sage);
      font-weight: 800;
      font-size: 14px;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--beige);
      border-radius: 6px;
    }

    /* Content Grid */
    .content-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 32px;
    }

    /* Projects Section */
    .section {
      background: var(--white);
      border-radius: 20px;
      border: 2px solid var(--beige);
      overflow: hidden;
    }

    .section-header {
      padding: 24px 28px;
      background: linear-gradient(135deg, var(--beige) 0%, var(--tan) 100%);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .section-title {
      font-size: 20px;
      font-weight: 800;
      color: var(--brown);
    }

    .item-count {
      background: var(--white);
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 800;
      color: var(--brown);
    }

    .section-body {
      padding: 28px;
    }

    /* Project Card - Horizontal Layout */
    .project-card {
      background: linear-gradient(135deg, var(--cream) 0%, var(--white) 100%);
      border: 2px solid var(--beige);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 16px;
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
    }

    .project-card::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 6px;
      height: 100%;
      background: linear-gradient(180deg, var(--brown), var(--tan));
      opacity: 0;
      transition: opacity 0.3s;
    }

    .project-card:hover {
      transform: translateX(8px);
      border-color: var(--brown);
      box-shadow: -8px 8px 24px rgba(82, 61, 53, 0.12);
    }

    .project-card:hover::before {
      opacity: 1;
    }

    .project-top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 16px;
    }

    .project-id-badge {
      background: var(--brown);
      color: var(--white);
      padding: 10px 18px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 900;
      letter-spacing: 0.5px;
    }

    .copy-id-btn {
      padding: 10px 20px;
      background: var(--white);
      border: 2px solid var(--beige);
      border-radius: 10px;
      color: var(--dark-teal);
      font-family: 'Montserrat', sans-serif;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
    }

    .copy-id-btn:hover {
      background: var(--brown);
      color: var(--white);
      border-color: var(--brown);
    }

    .project-name {
      font-size: 18px;
      font-weight: 800;
      color: var(--dark-teal);
      margin-bottom: 16px;
      line-height: 1.3;
    }

    .project-details {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
      margin-bottom: 16px;
    }

    .detail-item {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .detail-label {
      font-size: 11px;
      font-weight: 800;
      color: var(--sage);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .detail-value {
      font-size: 14px;
      font-weight: 600;
      color: var(--dark-teal);
    }

    .media-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 4px;
    }

    .media-tag {
      background: var(--beige);
      color: var(--brown);
      padding: 6px 14px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
    }

    /* Analytics Panel */
    .analytics-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin-bottom: 28px;
      padding-bottom: 28px;
      border-bottom: 2px solid var(--beige);
    }

    .form-field {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .field-label {
      font-size: 12px;
      font-weight: 800;
      color: var(--dark-teal);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-input,
    .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid var(--beige);
      border-radius: 12px;
      background: var(--white);
      font-family: 'Montserrat', sans-serif;
      font-size: 14px;
      font-weight: 600;
      color: var(--dark-teal);
      outline: none;
      transition: all 0.2s;
    }

    .form-input:focus,
    .form-select:focus {
      border-color: var(--brown);
      box-shadow: 0 0 0 4px rgba(82, 61, 53, 0.1);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .submit-btn {
      width: 100%;
      padding: 16px;
      background: var(--brown);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-family: 'Montserrat', sans-serif;
      font-size: 15px;
      font-weight: 800;
      cursor: pointer;
      transition: all 0.2s;
    }

    .submit-btn:hover {
      background: var(--dark-teal);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(82, 61, 53, 0.25);
    }

    .helper-text {
      font-size: 12px;
      color: var(--sage);
      font-weight: 600;
      margin-top: 8px;
    }

    /* Stats Cards */
    .stats-container {
      display: grid;
      gap: 16px;
      margin-bottom: 28px;
    }

    .stat-card {
      background: linear-gradient(135deg, var(--cream) 0%, var(--white) 100%);
      border: 2px solid var(--beige);
      border-radius: 16px;
      padding: 20px;
    }

    .stat-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .stat-name {
      font-size: 12px;
      font-weight: 800;
      color: var(--sage);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .stat-percentage {
      font-size: 16px;
      font-weight: 900;
      color: var(--dark-teal);
    }

    .stat-number {
      font-size: 32px;
      font-weight: 900;
      color: var(--dark-teal);
      margin-bottom: 12px;
    }

    .progress-bar {
      height: 10px;
      background: var(--beige);
      border-radius: 10px;
      overflow: hidden;
    }

    .progress-fill {
      height: 100%;
      border-radius: 10px;
      transition: width 0.5s ease;
    }

    .progress-positive {
      background: linear-gradient(90deg, var(--sage), #6b7563);
    }

    .progress-neutral {
      background: linear-gradient(90deg, var(--tan), #9a8676);
    }

    .progress-negative {
      background: linear-gradient(90deg, var(--brown), #3d2d27);
    }

    /* Geo Table */
    .geo-section {
      margin-top: 28px;
    }

    .geo-title {
      font-size: 14px;
      font-weight: 800;
      color: var(--dark-teal);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 16px;
    }

    .geo-table-wrapper {
      border: 2px solid var(--beige);
      border-radius: 12px;
      overflow: hidden;
    }

    .geo-table {
      width: 100%;
      border-collapse: collapse;
    }

    .geo-table thead {
      background: var(--beige);
    }

    .geo-table th {
      padding: 14px 16px;
      text-align: left;
      font-size: 11px;
      font-weight: 800;
      color: var(--brown);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .geo-table td {
      padding: 14px 16px;
      font-size: 14px;
      font-weight: 600;
      color: var(--dark-teal);
      border-bottom: 1px solid var(--beige);
    }

    .geo-table tr:last-child td {
      border-bottom: none;
    }

    .geo-table tr:hover {
      background: var(--cream);
    }

    .count-cell {
      text-align: right;
      font-weight: 800;
      color: var(--brown);
    }

    /* Chart Container */
    .chart-container {
      position: relative;
      height: 320px;
      margin-bottom: 20px;
    }

    .chart-container.donut {
      height: 380px;
    }

    /* Mode Toggle */
    .mode-toggle {
      display: flex;
      gap: 8px;
      margin-bottom: 16px;
    }

    .mode-btn {
      padding: 8px 16px;
      border: 2px solid var(--beige);
      background: var(--white);
      color: var(--dark-teal);
      font-family: 'Montserrat', sans-serif;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .mode-btn:hover {
      background: var(--beige);
    }

    .mode-btn.active {
      background: var(--brown);
      color: var(--white);
      border-color: var(--brown);
    }

    /* Debug Section */
    .debug-toggle {
      font-size: 13px;
      font-weight: 700;
      color: var(--brown);
      cursor: pointer;
      padding: 12px 16px;
      background: var(--beige);
      border-radius: 10px;
      display: inline-block;
      margin-bottom: 16px;
      user-select: none;
    }

    .debug-toggle:hover {
      background: var(--tan);
    }

    .debug-content {
      background: var(--dark-teal);
      color: var(--cream);
      padding: 20px;
      border-radius: 12px;
      overflow: auto;
      max-height: 300px;
      font-family: 'Courier New', monospace;
      font-size: 12px;
      line-height: 1.6;
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 48px 24px;
    }

    .empty-icon {
      font-size: 56px;
      margin-bottom: 16px;
      opacity: 0.3;
      font-weight: 800;
      color: var(--sage);
    }

    .empty-text {
      font-size: 15px;
      font-weight: 600;
      color: var(--sage);
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .content-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .main-content {
        margin-left: 0;
        padding: 20px;
      }

      .top-bar {
        flex-direction: column;
        align-items: flex-start;
      }

      .top-actions {
        width: 100%;
      }

      .action-btn {
        flex: 1;
      }

      .project-details {
        grid-template-columns: 1fr;
      }

      .form-row {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>

  <!-- Sidebar Navigation -->
  <div class="sidebar">
    <div class="logo">
      <h1>SMADIMENT</h1>
      <p>Social Media Analytics</p>
    </div>

    <div class="nav-section">
      <div class="nav-label">Main</div>
      <a href="/mk/projects" class="nav-item active">
        <span class="nav-icon">PR</span>
        <span>Projects</span>
      </a>
    </div>

    <div class="nav-section">
      <div class="nav-label">Tools</div>
      <a href="#" class="nav-item">
        <span class="nav-icon">ST</span>
        <span>Settings</span>
      </a>
      <a href="#" class="nav-item">
        <span class="nav-icon">DC</span>
        <span>Documentation</span>
      </a>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">

    <!-- Top Bar -->
    <div class="top-bar">
      <div class="page-title">
        <h2>Projects Overview</h2>
        <div class="page-meta">
          <span>Start: <strong>{{ $start }}</strong></span>
          <span>•</span>
          <span>Limit: <strong>{{ $limit }}</strong></span>
          <span>•</span>
          <span class="meta-badge">{{ count($projects) }} Projects</span>
        </div>
      </div>

      <div class="top-actions">
        <a class="action-btn" href="/mk/projects?start={{ max(0, $start-$limit) }}&limit={{ $limit }}">← Previous</a>
        <a class="action-btn" href="/mk/projects?start={{ $start+$limit }}&limit={{ $limit }}">Next →</a>
        <a class="action-btn primary" href="/mk/projects?start=0&limit=20">Refresh</a>
      </div>
    </div>

    <!-- Search -->
    <div class="search-container">
      <div class="search-wrapper">
        <span class="search-icon">S</span>
        <input
          id="searchInput"
          type="text"
          class="search-input"
          placeholder="Search by title, keywords, group, or media type..." />
      </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">

      <!-- Projects List -->
      <div class="section">
        <div class="section-header">
          <h3 class="section-title">Active Projects</h3>
          <span class="item-count">{{ count($projects) }}</span>
        </div>

        <div class="section-body">
          @if (count($projects) === 0)
          <div class="empty-state">
            <div class="empty-icon">!</div>
            <div class="empty-text">No projects available. Check API connection.</div>
          </div>
          @else
          <div id="projectList">
            @foreach ($projects as $p)
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

            <div class="project-card" data-search="{{ $search }}">
              <div class="project-top">
                <div class="project-id-badge">#{{ $id }}</div>
                <button class="copy-id-btn" data-copy="{{ $id }}">Copy ID</button>
              </div>

              <h4 class="project-name">{{ $title }}</h4>

              <div class="project-details">
                <div class="detail-item">
                  <div class="detail-label">Group</div>
                  <div class="detail-value">{{ $group }}</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">Type</div>
                  <div class="detail-value">{{ $type }}</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">Keywords</div>
                  <div class="detail-value">{{ $keywords }}</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">Media Types</div>
                  <div class="media-tags">
                    @if (count($mediaArr) === 0)
                    <span class="media-tag">None</span>
                    @else
                    @foreach ($mediaArr as $m)
                    <span class="media-tag">{{ $m }}</span>
                    @endforeach
                    @endif
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif
        </div>
      </div>

      <!-- Analytics Panel -->
      <div>
        <div class="section">
          <div class="section-header">
            <h3 class="section-title">Analytics Control</h3>
          </div>

          <div class="section-body">
            <form method="GET" action="/mk/projects" class="analytics-form">
              <input type="hidden" name="start" value="{{ $start }}">
              <input type="hidden" name="limit" value="{{ $limit }}">

              <div class="form-field">
                <label class="field-label">Select Project</label>
                <select class="form-select" name="project_id">
                  @foreach($projects as $p)
                  @php
                  $pid = $p['id'] ?? '';
                  $pt = $p['title'] ?? $pid;
                  @endphp
                  <option value="{{ $pid }}" @selected($pid==$projectId)>
                    #{{ $pid }} - {{ $pt }}
                  </option>
                  @endforeach
                </select>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="field-label">Start Date</label>
                  <input class="form-input" type="date" name="start_date" value="{{ $params['startDate'] }}">
                </div>

                <div class="form-field">
                  <label class="field-label">End Date</label>
                  <input class="form-input" type="date" name="end_date" value="{{ $params['endDate'] }}">
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="field-label">Start Hour</label>
                  <input class="form-input" type="number" min="0" max="23" name="start_time" value="{{ $params['startTime'] }}">
                </div>

                <div class="form-field">
                  <label class="field-label">End Hour</label>
                  <input class="form-input" type="number" min="0" max="23" name="end_time" value="{{ $params['endTime'] }}">
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <label class="field-label">Media Platform</label>
                  <select class="form-select" name="media">
                    <option value="twit" @selected($params['media']=='twit' )>Twitter</option>
                    <option value="fb" @selected($params['media']=='fb' )>Facebook</option>
                    <option value="instagram" @selected($params['media']=='instagram' )>Instagram</option>
                    <option value="youtube" @selected($params['media']=='youtube' )>YouTube</option>
                    <option value="tiktok" @selected($params['media']=='tiktok' )>TikTok</option>
                    <option value="doc" @selected($params['media']=='doc' )>Document</option>
                  </select>
                </div>

                <div class="form-field">
                  <label class="field-label">Sentiment</label>
                  <select class="form-select" name="sentiment">
                    <option value="1" @selected($params['sentiment']==1)>Positive</option>
                    <option value="0" @selected($params['sentiment']==0)>Neutral</option>
                    <option value="-1" @selected($params['sentiment']==-1)>Negative</option>
                  </select>
                </div>
              </div>

              <button type="submit" class="submit-btn">Load Analytics Data</button>
              <div class="helper-text">
                Data from /sentiment_total & /get_geo_twitter_user_sentiment
              </div>
            </form>

            @php
            $pos = $sentimentNorm['positive'] ?? 0;
            $neu = $sentimentNorm['neutral'] ?? 0;
            $neg = $sentimentNorm['negative'] ?? 0;
            $total = max(1, $pos + $neu + $neg);
            $posP = round($pos/$total*100);
            $neuP = round($neu/$total*100);
            $negP = round($neg/$total*100);
            @endphp

            <div class="stats-container">
              <div class="stat-card">
                <div class="stat-header">
                  <span class="stat-name">Positive</span>
                  <span class="stat-percentage">{{ $posP }}%</span>
                </div>
                <div class="stat-number">{{ number_format($pos) }}</div>
                <div class="progress-bar">
                  <div class="progress-fill progress-positive" style="width: {{ $posP }}%"></div>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-header">
                  <span class="stat-name">Neutral</span>
                  <span class="stat-percentage">{{ $neuP }}%</span>
                </div>
                <div class="stat-number">{{ number_format($neu) }}</div>
                <div class="progress-bar">
                  <div class="progress-fill progress-neutral" style="width: {{ $neuP }}%"></div>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-header">
                  <span class="stat-name">Negative</span>
                  <span class="stat-percentage">{{ $negP }}%</span>
                </div>
                <div class="stat-number">{{ number_format($neg) }}</div>
                <div class="progress-bar">
                  <div class="progress-fill progress-negative" style="width: {{ $negP }}%"></div>
                </div>
              </div>
            </div>

            <div class="geo-section">
              <h4 class="geo-title">Geographic Distribution (Top 10)</h4>

              @if (count($geoRows) === 0)
              <div class="empty-state">
                <div class="empty-text">No geographic data available</div>
              </div>
              @else
              <div class="geo-table-wrapper">
                <table class="geo-table">
                  <thead>
                    <tr>
                      <th>Location</th>
                      <th style="text-align: right;">Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($geoRows as $r)
                    <tr>
                      <td>{{ $r['name'] }}</td>
                      <td class="count-cell">{{ number_format($r['count']) }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @endif
            </div>
          </div>
        </div>

        {{-- AUTHORS AGE CHART --}}
        <div class="section" style="margin-top:24px;">
          <div class="section-header">
            <h3 class="section-title">Authors Age Distribution</h3>
          </div>
          <div class="section-body">
            <div class="mode-toggle">
              <button class="mode-btn active" onclick="switchAgeMode('line')">Line Chart</button>
              <button class="mode-btn" onclick="switchAgeMode('donut')">Donut Chart</button>
            </div>

            @php
            $hasAgeData = !empty($ageChart['labels']) && !empty($ageChart['values']);
            @endphp

            @if (!$hasAgeData)
            <div class="empty-state">
              <div class="empty-text">No age data available</div>
            </div>
            @else
            <div class="chart-container" id="ageLineContainer">
              <canvas id="ageLineChart"></canvas>
            </div>
            <div class="chart-container donut" id="ageDonutContainer" style="display:none;">
              <canvas id="ageDonutChart"></canvas>
            </div>
            @endif

            <details style="margin-top: 16px;">
              <summary class="debug-toggle">View Raw Data</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($ageRaw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
          </div>
        </div>

        {{-- AUTHORS GENDER CHART --}}
        <div class="section" style="margin-top:24px;">
          <div class="section-header">
            <h3 class="section-title">Authors Gender Distribution</h3>
          </div>
          <div class="section-body">
            <div class="mode-toggle">
              <button class="mode-btn active" onclick="switchGenderMode('line')">Line Chart</button>
              <button class="mode-btn" onclick="switchGenderMode('donut')">Donut Chart</button>
            </div>

            @php
            $hasGenderData = !empty($genderChart['labels']) && !empty($genderChart['values']);
            @endphp

            @if (!$hasGenderData)
            <div class="empty-state">
              <div class="empty-text">No gender data available</div>
            </div>
            @else
            <div class="chart-container" id="genderLineContainer">
              <canvas id="genderLineChart"></canvas>
            </div>
            <div class="chart-container donut" id="genderDonutContainer" style="display:none;">
              <canvas id="genderDonutChart"></canvas>
            </div>
            @endif

            <details style="margin-top: 16px;">
              <summary class="debug-toggle">View Raw Data</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($genderRaw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
          </div>
        </div>

        {{-- AUTHORS TYPE CHART --}}
        <div class="section" style="margin-top:24px;">
          <div class="section-header">
            <h3 class="section-title">Authors Type Distribution</h3>
          </div>
          <div class="section-body">
            <div class="mode-toggle">
              <button class="mode-btn active" onclick="switchTypeMode('line')">Line Chart</button>
              <button class="mode-btn" onclick="switchTypeMode('donut')">Donut Chart</button>
            </div>

            @php
            $hasTypeData = !empty($authorsTypeChart['labels']) && !empty($authorsTypeChart['values']);
            @endphp

            @if (!$hasTypeData)
            <div class="empty-state">
              <div class="empty-text">No authors type data available</div>
            </div>
            @else
            <div class="chart-container" id="typeLineContainer">
              <canvas id="typeLineChart"></canvas>
            </div>
            <div class="chart-container donut" id="typeDonutContainer" style="display:none;">
              <canvas id="typeDonutChart"></canvas>
            </div>
            @endif

            <details style="margin-top: 16px;">
              <summary class="debug-toggle">View Raw Data</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($authorsTypeRaw ?? ['error' => 'Variable not set'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}</pre>
            </details>
          </div>
        </div>

        {{-- CATEGORIES CHART --}}
        <div class="section" style="margin-top:24px;">
          <div class="section-header">
            <h3 class="section-title">Categories Distribution</h3>
          </div>
          <div class="section-body">
            <div class="mode-toggle">
              <button class="mode-btn active" onclick="switchCategoriesMode('line')">Line Chart</button>
              <button class="mode-btn" onclick="switchCategoriesMode('donut')">Donut Chart</button>
            </div>

            @php
            $hasCategoriesData = !empty($categoriesChart['labels']) && !empty($categoriesChart['values']);
            @endphp

            @if (!$hasCategoriesData)
            <div class="empty-state">
              <div class="empty-text">No categories data available</div>
            </div>
            @else
            <div class="chart-container" id="categoriesLineContainer">
              <canvas id="categoriesLineChart"></canvas>
            </div>
            <div class="chart-container donut" id="categoriesDonutContainer" style="display:none;">
              <canvas id="categoriesDonutChart"></canvas>
            </div>
            @endif

            <details style="margin-top: 16px;">
              <summary class="debug-toggle">View Raw Data</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($categoriesRaw ?? ['error' => 'Variable not set'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}</pre>
            </details>
          </div>
        </div>

        {{-- ESTIMATED REACH CHART --}}
        <div class="section" style="margin-top:24px;">
          <div class="section-header">
            <h3 class="section-title">Estimated Reach Over Time</h3>
          </div>
          <div class="section-body">
            <div class="mode-toggle">
              <button class="mode-btn active" onclick="switchEstReachMode('line')">Line Chart</button>
              <button class="mode-btn" onclick="switchEstReachMode('donut')">Donut Chart</button>
            </div>

            @php
            $hasEstReachData = !empty($estReachChart['labels']) && !empty($estReachChart['values']);
            @endphp

            @if (!$hasEstReachData)
            <div class="empty-state">
              <div class="empty-text">No estimated reach data available</div>
            </div>
            @else
            <div class="chart-container" id="estReachLineContainer">
              <canvas id="estReachLineChart"></canvas>
            </div>
            <div class="chart-container donut" id="estReachDonutContainer" style="display:none;">
              <canvas id="estReachDonutChart"></canvas>
            </div>
            @endif

            <details style="margin-top: 16px;">
              <summary class="debug-toggle">View Raw Data</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($estReachRaw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
          </div>
        </div>

        {{-- GEO TWITTER USER TABLE --}}
        <div class="section" style="margin-top:24px;">
          <div class="section-header">
            <h3 class="section-title">Geographic Twitter Users (Top 10)</h3>
          </div>
          <div class="section-body">
            @if (count($geoUserRows) === 0)
            <div class="empty-state">
              <div class="empty-text">No geographic user data available</div>
            </div>
            @else
            <div class="geo-table-wrapper">
              <table class="geo-table">
                <thead>
                  <tr>
                    <th>Location</th>
                    <th style="text-align: right;">Users</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($geoUserRows as $r)
                  <tr>
                    <td>{{ $r['name'] }}</td>
                    <td class="count-cell">{{ number_format($r['count']) }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif

            <details style="margin-top: 16px;">
              <summary class="debug-toggle">View Raw Data</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($geoUserRaw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
          </div>
        </div>

        {{-- DETAILED SENTIMENT DISTRIBUTION --}}
        <div class="section" style="margin-top:24px;">
          <div class="section-header">
            <h3 class="section-title">Detailed Sentiment Analysis</h3>
          </div>
          <div class="section-body">
            @php
            $detPos = $getSentimentChart['positive'] ?? 0;
            $detNeu = $getSentimentChart['neutral'] ?? 0;
            $detNeg = $getSentimentChart['negative'] ?? 0;
            $detTotal = max(1, $detPos + $detNeu + $detNeg);
            $detPosP = round($detPos/$detTotal*100);
            $detNeuP = round($detNeu/$detTotal*100);
            $detNegP = round($detNeg/$detTotal*100);
            $hasDetailedSentiment = $detTotal > 1;
            @endphp

            @if (!$hasDetailedSentiment)
            <div class="empty-state">
              <div class="empty-text">No detailed sentiment data available</div>
            </div>
            @else
            <div class="stats-container">
              <div class="stat-card">
                <div class="stat-header">
                  <span class="stat-name">Positive (Detailed)</span>
                  <span class="stat-percentage">{{ $detPosP }}%</span>
                </div>
                <div class="stat-number">{{ number_format($detPos) }}</div>
                <div class="progress-bar">
                  <div class="progress-fill progress-positive" style="width: {{ $detPosP }}%"></div>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-header">
                  <span class="stat-name">Neutral (Detailed)</span>
                  <span class="stat-percentage">{{ $detNeuP }}%</span>
                </div>
                <div class="stat-number">{{ number_format($detNeu) }}</div>
                <div class="progress-bar">
                  <div class="progress-fill progress-neutral" style="width: {{ $detNeuP }}%"></div>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-header">
                  <span class="stat-name">Negative (Detailed)</span>
                  <span class="stat-percentage">{{ $detNegP }}%</span>
                </div>
                <div class="stat-number">{{ number_format($detNeg) }}</div>
                <div class="progress-bar">
                  <div class="progress-fill progress-negative" style="width: {{ $detNegP }}%"></div>
                </div>
              </div>
            </div>
            @endif

            <details style="margin-top: 16px;">
              <summary class="debug-toggle">View Raw Data</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($getSentimentRaw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
          </div>
        </div>

        {{-- SHARED URL FREQUENCY TABLE --}}
        <div class="section" style="margin-top:24px;">
          <div class="section-header">
            <h3 class="section-title">Top Shared URLs (Top 10)</h3>
          </div>
          <div class="section-body">
            @if (count($sharedUrlRows) === 0)
            <div class="empty-state">
              <div class="empty-text">No shared URL data available</div>
            </div>
            @else
            <div class="geo-table-wrapper">
              <table class="geo-table">
                <thead>
                  <tr>
                    <th>URL</th>
                    <th style="text-align: right;">Frequency</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($sharedUrlRows as $r)
                  <tr>
                    <td style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $r['url'] }}">
                      {{ $r['url'] }}
                    </td>
                    <td class="count-cell">{{ number_format($r['freq']) }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif

            <details style="margin-top: 16px;">
              <summary class="debug-toggle">View Raw Data</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($sharedUrlRaw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
          </div>
        </div>

        {{-- MOST ACTIVE USERS TABLE --}}
        <div class="section" style="margin-top:24px;">
          <div class="section-header">
            <h3 class="section-title">Most Active Users (Top 10)</h3>
          </div>
          <div class="section-body">
            @if (count($activeUsersRows) === 0)
            <div class="empty-state">
              <div class="empty-text">No active users data available</div>
            </div>
            @else
            <div class="geo-table-wrapper">
              <table class="geo-table">
                <thead>
                  <tr>
                    <th>Username</th>
                    <th style="text-align: right;">Post Count</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($activeUsersRows as $r)
                  <tr>
                    <td>{{ $r['username'] }}</td>
                    <td class="count-cell">{{ number_format($r['count']) }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif

            <details style="margin-top: 16px;">
              <summary class="debug-toggle">View Raw Data</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($activeUsersRaw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
          </div>
        </div>

        <!-- Debug Panel -->
        <div class="section" style="margin-top: 24px;">
          <div class="section-header">
            <h3 class="section-title">API Debug</h3>
          </div>
          <div class="section-body">
            <details>
              <summary class="debug-toggle">View Raw Projects API Response</summary>
              <pre class="debug-content" style="margin-top: 16px;">{{ json_encode($raw, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}</pre>
            </details>
          </div>
        </div>
      </div>

    </div>

  </div>

  <script>
    // Color Palette
    const colors = {
      brown: '#31487A',
      sage: '#8FB3E2',
      tan: '#8FB3E2',
      beige: '#D9E1F1',
      darkTeal: '#192338',
      palette: [
        '#31487A', '#8FB3E2', '#D9E1F1', '#192338', '#5A6F9E',
        '#A8C5E8', '#6B8BC3', '#4A5E8C', '#7A94C7', '#9BB5DD'
      ]
    };

    let ageLineChart, ageDonutChart;
    let genderLineChart, genderDonutChart;
    let typeLineChart, typeDonutChart;
    let categoriesLineChart, categoriesDonutChart;

    // ========== AGE CHARTS ==========
    @if($hasAgeData) {
      const ageLabels = @json($ageChart['labels']);
      const ageValues = @json($ageChart['values']);

      // Line Chart
      const ageLineCtx = document.getElementById('ageLineChart').getContext('2d');
      ageLineChart = new Chart(ageLineCtx, {
        type: 'line',
        data: {
          labels: ageLabels,
          datasets: [{
            label: 'Post Frequency',
            data: ageValues,
            borderColor: colors.brown,
            backgroundColor: colors.sage + '40',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: colors.brown,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            title: {
              display: true,
              text: 'Age Distribution - Line Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: colors.beige
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                }
              }
            }
          }
        }
      });

      // Donut Chart
      const ageDonutCtx = document.getElementById('ageDonutChart').getContext('2d');
      ageDonutChart = new Chart(ageDonutCtx, {
        type: 'doughnut',
        data: {
          labels: ageLabels,
          datasets: [{
            data: ageValues,
            backgroundColor: colors.palette,
            borderWidth: 2,
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
                font: {
                  family: 'Montserrat',
                  weight: '600',
                  size: 11
                },
                padding: 15,
                usePointStyle: true
              }
            },
            title: {
              display: true,
              text: 'Age Distribution - Donut Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          }
        }
      });
    }
    @endif

    // ========== GENDER CHARTS ==========
    @if($hasGenderData) {
      const genderLabels = @json($genderChart['labels']);
      const genderValues = @json($genderChart['values']);

      // Line Chart
      const genderLineCtx = document.getElementById('genderLineChart').getContext('2d');
      genderLineChart = new Chart(genderLineCtx, {
        type: 'line',
        data: {
          labels: genderLabels,
          datasets: [{
            label: 'Post Frequency',
            data: genderValues,
            borderColor: colors.sage,
            backgroundColor: colors.brown + '40',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: colors.sage,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            title: {
              display: true,
              text: 'Gender Distribution - Line Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: colors.beige
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                }
              }
            }
          }
        }
      });

      // Donut Chart
      const genderDonutCtx = document.getElementById('genderDonutChart').getContext('2d');
      genderDonutChart = new Chart(genderDonutCtx, {
        type: 'doughnut',
        data: {
          labels: genderLabels,
          datasets: [{
            data: genderValues,
            backgroundColor: colors.palette.slice(0, genderLabels.length),
            borderWidth: 2,
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
                font: {
                  family: 'Montserrat',
                  weight: '600',
                  size: 11
                },
                padding: 15,
                usePointStyle: true
              }
            },
            title: {
              display: true,
              text: 'Gender Distribution - Donut Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          }
        }
      });
    }
    @endif

    // ========== AUTHORS TYPE CHARTS ==========
    @if($hasTypeData) {
      const typeLabels = @json($authorsTypeChart['labels']);
      const typeValues = @json($authorsTypeChart['values']);

      // Line Chart
      const typeLineCtx = document.getElementById('typeLineChart').getContext('2d');
      typeLineChart = new Chart(typeLineCtx, {
        type: 'line',
        data: {
          labels: typeLabels,
          datasets: [{
            label: 'Post Frequency',
            data: typeValues,
            borderColor: colors.tan,
            backgroundColor: colors.darkTeal + '40',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: colors.tan,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            title: {
              display: true,
              text: 'Authors Type Distribution - Line Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: colors.beige
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                }
              }
            }
          }
        }
      });

      // Donut Chart
      const typeDonutCtx = document.getElementById('typeDonutChart').getContext('2d');
      typeDonutChart = new Chart(typeDonutCtx, {
        type: 'doughnut',
        data: {
          labels: typeLabels,
          datasets: [{
            data: typeValues,
            backgroundColor: colors.palette.slice(0, typeLabels.length),
            borderWidth: 2,
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
                font: {
                  family: 'Montserrat',
                  weight: '600',
                  size: 11
                },
                padding: 15,
                usePointStyle: true
              }
            },
            title: {
              display: true,
              text: 'Authors Type Distribution - Donut Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          }
        }
      });
    }
    @endif

    // ========== CATEGORIES CHARTS ==========
    @if($hasCategoriesData) {
      const categoriesLabels = @json($categoriesChart['labels']);
      const categoriesValues = @json($categoriesChart['values']);

      // Line Chart
      const categoriesLineCtx = document.getElementById('categoriesLineChart').getContext('2d');
      categoriesLineChart = new Chart(categoriesLineCtx, {
        type: 'line',
        data: {
          labels: categoriesLabels,
          datasets: [{
            label: 'Category Count',
            data: categoriesValues,
            borderColor: colors.darkTeal,
            backgroundColor: colors.sage + '40',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: colors.darkTeal,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            title: {
              display: true,
              text: 'Categories Distribution - Line Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: colors.beige
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                },
                maxRotation: 45,
                minRotation: 45
              }
            }
          }
        }
      });

      // Donut Chart
      const categoriesDonutCtx = document.getElementById('categoriesDonutChart').getContext('2d');
      categoriesDonutChart = new Chart(categoriesDonutCtx, {
        type: 'doughnut',
        data: {
          labels: categoriesLabels,
          datasets: [{
            data: categoriesValues,
            backgroundColor: colors.palette,
            borderWidth: 2,
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
                font: {
                  family: 'Montserrat',
                  weight: '600',
                  size: 11
                },
                padding: 15,
                usePointStyle: true
              }
            },
            title: {
              display: true,
              text: 'Categories Distribution - Donut Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          }
        }
      });
    }
    @endif
    // ========== ESTIMATED REACH CHARTS ==========
    @if($hasEstReachData) {
      const estReachLabels = @json($estReachChart['labels']);
      const estReachValues = @json($estReachChart['values']);

      // Line Chart
      const estReachLineCtx = document.getElementById('estReachLineChart').getContext('2d');
      estReachLineChart = new Chart(estReachLineCtx, {
        type: 'line',
        data: {
          labels: estReachLabels,
          datasets: [{
            label: 'Estimated Reach',
            data: estReachValues,
            borderColor: '#6b8bc3',
            backgroundColor: '#6b8bc340',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6b8bc3',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            title: {
              display: true,
              text: 'Estimated Reach Over Time - Line Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: colors.beige
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                },
                callback: function(value) {
                  if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                  if (value >= 1000) return (value / 1000).toFixed(1) + 'K';
                  return value;
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  family: 'Montserrat',
                  weight: '600'
                },
                maxRotation: 45,
                minRotation: 45
              }
            }
          }
        }
      });

      // Donut Chart
      const estReachDonutCtx = document.getElementById('estReachDonutChart').getContext('2d');
      estReachDonutChart = new Chart(estReachDonutCtx, {
        type: 'doughnut',
        data: {
          labels: estReachLabels,
          datasets: [{
            data: estReachValues,
            backgroundColor: colors.palette,
            borderWidth: 2,
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
                font: {
                  family: 'Montserrat',
                  weight: '600',
                  size: 11
                },
                padding: 15,
                usePointStyle: true
              }
            },
            title: {
              display: true,
              text: 'Estimated Reach Distribution - Donut Chart',
              font: {
                size: 16,
                weight: 'bold',
                family: 'Montserrat'
              },
              color: colors.darkTeal
            }
          }
        }
      });
    }
    @endif

    // ========== MODE SWITCHING FUNCTIONS ==========
    function switchAgeMode(mode) {
      const lineContainer = document.getElementById('ageLineContainer');
      const donutContainer = document.getElementById('ageDonutContainer');
      const section = lineContainer.closest('.section-body');
      const buttons = section.querySelectorAll('.mode-btn');

      if (mode === 'line') {
        lineContainer.style.display = 'block';
        donutContainer.style.display = 'none';
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
      } else {
        lineContainer.style.display = 'none';
        donutContainer.style.display = 'block';
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');
      }
    }

    function switchGenderMode(mode) {
      const lineContainer = document.getElementById('genderLineContainer');
      const donutContainer = document.getElementById('genderDonutContainer');
      const section = lineContainer.closest('.section-body');
      const buttons = section.querySelectorAll('.mode-btn');

      if (mode === 'line') {
        lineContainer.style.display = 'block';
        donutContainer.style.display = 'none';
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
      } else {
        lineContainer.style.display = 'none';
        donutContainer.style.display = 'block';
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');
      }
    }

    function switchTypeMode(mode) {
      const lineContainer = document.getElementById('typeLineContainer');
      const donutContainer = document.getElementById('typeDonutContainer');
      const section = lineContainer.closest('.section-body');
      const buttons = section.querySelectorAll('.mode-btn');

      if (mode === 'line') {
        lineContainer.style.display = 'block';
        donutContainer.style.display = 'none';
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
      } else {
        lineContainer.style.display = 'none';
        donutContainer.style.display = 'block';
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');
      }
    }

    function switchCategoriesMode(mode) {
      const lineContainer = document.getElementById('categoriesLineContainer');
      const donutContainer = document.getElementById('categoriesDonutContainer');
      const section = lineContainer.closest('.section-body');
      const buttons = section.querySelectorAll('.mode-btn');

      if (mode === 'line') {
        lineContainer.style.display = 'block';
        donutContainer.style.display = 'none';
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
      } else {
        lineContainer.style.display = 'none';
        donutContainer.style.display = 'block';
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');
      }
    }

    // ========== ESTIMATED REACH MODE SWITCHING ==========
    function switchEstReachMode(mode) {
      const lineContainer = document.getElementById('estReachLineContainer');
      const donutContainer = document.getElementById('estReachDonutContainer');
      const section = lineContainer.closest('.section-body');
      const buttons = section.querySelectorAll('.mode-btn');

      if (mode === 'line') {
        lineContainer.style.display = 'block';
        donutContainer.style.display = 'none';
        buttons[0].classList.add('active');
        buttons[1].classList.remove('active');
      } else {
        lineContainer.style.display = 'none';
        donutContainer.style.display = 'block';
        buttons[0].classList.remove('active');
        buttons[1].classList.add('active');
      }
    }

    // ========== SEARCH FUNCTIONALITY ==========
    const searchInput = document.getElementById('searchInput');
    const projectCards = document.querySelectorAll('.project-card');

    searchInput?.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase().trim();

      projectCards.forEach(card => {
        const searchData = card.getAttribute('data-search') || '';
        card.style.display = (!query || searchData.includes(query)) ? '' : 'none';
      });
    });

    // ========== COPY ID FUNCTIONALITY ==========
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.copy-id-btn');
      if (!btn) return;

      const id = btn.getAttribute('data-copy');

      try {
        await navigator.clipboard.writeText(id);
        const originalText = btn.textContent;
        btn.textContent = 'Copied!';
        btn.style.background = 'var(--sage)';
        btn.style.color = 'white';
        btn.style.borderColor = 'var(--sage)';

        setTimeout(() => {
          btn.textContent = originalText;
          btn.style.background = '';
          btn.style.color = '';
          btn.style.borderColor = '';
        }, 1500);
      } catch (err) {
        alert('Failed to copy ID: ' + id);
      }
    });
  </script>

</body>

</html>