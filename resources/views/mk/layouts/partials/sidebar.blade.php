<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('mk.dashboard') }}" class="b-brand text-primary">
                <img src="{{ asset('images/SMADIMENT 2025 _ Logo-03.png') }}" class="img-fluid logo-lg" alt="SMADIMENT" style="height: 36px; width: auto;" />
            </a>
        </div>
        <div class="navbar-content">
            @php
                $currentProjectId = request()->get('project_id');
                $hasProjects = isset($projects) && count($projects) > 0;
                if (!$currentProjectId && $hasProjects) {
                    $currentProjectId = $projects[0]['id'] ?? null;
                }
                $currentProjectName = 'Select Project';
                if ($currentProjectId && $hasProjects) {
                    $currentProject = collect($projects)->firstWhere('id', $currentProjectId);
                    if ($currentProject) {
                        $currentProjectName = $currentProject['name']
                            ?? $currentProject['project_name']
                            ?? $currentProject['title']
                            ?? $currentProject['label']
                            ?? 'Project #' . $currentProject['id'];
                    }
                }

                $statisticRoutes   = ['mk.media-statistic', 'mk.media-statistic.trend', 'mk.sentiment', 'mk.net-sentiment-score', 'mk.engagement', 'mk.interaction-sentiment'];
                $newsRoutes        = ['mk.news.word-cloud','mk.news.top-publishers','mk.news.timeline','mk.news.articles','mk.news.ai-analysis','mk.news.topic-map'];
                $xRoutes           = ['mk.x.overview','mk.x.most-status','mk.x.most-retweets','mk.x.most-engagement','mk.x.authors.demographics','mk.x.trending-topics','mk.x.top-hashtags','mk.x.trending-word-cloud','mk.x.shared-urls','mk.x.most-active-users','mk.x.top-influencers','mk.x.emotion-analysis','mk.x.ai-analysis'];
                $facebookRoutes    = ['mk.facebook.overview','mk.facebook.trending-topics','mk.facebook.most-viewed-posts','mk.facebook.most-engagement','mk.facebook.geographic','mk.facebook.trending-word-cloud','mk.facebook.ai-analysis','mk.facebook.emotion-analysis','mk.facebook.top-hashtags','mk.facebook.authors.demographics'];
                $instagramRoutes   = ['mk.instagram.overview','mk.instagram.trending-topics','mk.instagram.most-viewed-posts','mk.instagram.authors.demographics','mk.instagram.geographic','mk.instagram.trending-word-cloud','mk.instagram.ai-analysis','mk.instagram.most-engagement','mk.instagram.emotion-analysis'];
                $youtubeRoutes     = ['mk.youtube.overview','mk.youtube.trending-topics','mk.youtube.most-viewed-posts','mk.youtube.most-engagement','mk.youtube.emotion-analysis','mk.youtube.authors.demographics','mk.youtube.geographic','mk.youtube.trending-word-cloud','mk.youtube.ai-analysis'];
                $tiktokRoutes      = ['mk.tiktok.overview','mk.tiktok.trending-topics','mk.tiktok.most-viewed-posts','mk.tiktok.trending-word-cloud','mk.tiktok.most-engagement','mk.tiktok.emotion-analysis','mk.tiktok.ai-analysis'];

                $isStatisticActive = request()->routeIs($statisticRoutes);
                $isNewsActive      = request()->routeIs($newsRoutes);
                $isXActive         = request()->routeIs($xRoutes);
                $isFacebookActive  = request()->routeIs($facebookRoutes);
                $isInstagramActive = request()->routeIs($instagramRoutes);
                $isYoutubeActive   = request()->routeIs($youtubeRoutes);
                $isTiktokActive    = request()->routeIs($tiktokRoutes);

                $qs = !empty($currentProjectId) ? '?project_id=' . $currentProjectId : '';
            @endphp

            <ul class="pc-navbar">

                {{-- ── PROJECT ── --}}
                <li class="pc-item pc-caption">
                    <label>Project</label>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-folder-open"></i></span>
                        <span class="pc-mtext">{{ $currentProjectName }}</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        @if($hasProjects)
                            @foreach($projects as $project)
                                @php
                                    $pName = $project['name'] ?? $project['project_name'] ?? $project['title'] ?? $project['label'] ?? 'Project #' . ($project['id'] ?? '');
                                    $pId   = $project['id'] ?? '';
                                    $isActive = $currentProjectId == $pId;
                                @endphp
                                <li class="pc-item {{ $isActive ? 'active' : '' }}">
                                    <a class="pc-link" href="javascript:void(0)"
                                       onclick="changeProject({{ $pId }}, '{{ addslashes($pName) }}')">
                                        {{ $pName }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li class="pc-item"><a class="pc-link" href="#">No Projects Available</a></li>
                        @endif
                    </ul>
                </li>

                {{-- ── MAIN ── --}}
                <li class="pc-item pc-caption">
                    <label>Main</label>
                </li>

                <li class="pc-item {{ request()->routeIs('mk.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('mk.dashboard') }}" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-squares-four"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="pc-item {{ request()->routeIs('mk.data-overview') ? 'active' : '' }}">
                    <a href="{{ route('mk.data-overview') }}{{ $qs }}" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-chart-bar"></i></span>
                        <span class="pc-mtext">Data Overview</span>
                    </a>
                </li>

                <li class="pc-item pc-hasmenu {{ $isStatisticActive ? 'pc-trigger active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-table"></i></span>
                        <span class="pc-mtext">Statistic</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('mk.media-statistic') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.media-statistic') }}{{ $qs }}">Media</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.sentiment') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.sentiment') }}{{ $qs }}">Sentiment</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.net-sentiment-score') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.net-sentiment-score') }}{{ $qs }}">Net Sentiment Score</a>
                        </li>
                    </ul>
                </li>

                <li class="pc-item {{ request()->routeIs('mk.compare.index') ? 'active' : '' }}">
                    <a href="{{ route('mk.compare.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-git-diff"></i></span>
                        <span class="pc-mtext">Compare Projects</span>
                    </a>
                </li>

                <li class="pc-item {{ request()->routeIs('mk.topic-map') ? 'active' : '' }}">
                    <a href="{{ route('mk.topic-map') }}{{ $qs }}" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-globe-hemisphere-west"></i></span>
                        <span class="pc-mtext">World Map</span>
                    </a>
                </li>

                <li class="pc-item {{ request()->routeIs('mk.x.geographic') ? 'active' : '' }}">
                    <a href="{{ route('mk.x.geographic') }}{{ $qs }}" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-map-pin"></i></span>
                        <span class="pc-mtext">Location Map</span>
                    </a>
                </li>

                <li class="pc-item {{ request()->routeIs('mk.trending-topic') ? 'active' : '' }}">
                    <a href="{{ route('mk.trending-topic') }}" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-trend-up"></i></span>
                        <span class="pc-mtext">Trending Topics</span>
                    </a>
                </li>

                {{-- Posts with Location hidden from sidebar --}}

                {{-- ── NEWS ── --}}
                <li class="pc-item pc-caption">
                    <label>News</label>
                </li>

                <li class="pc-item pc-hasmenu {{ $isNewsActive ? 'pc-trigger active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-newspaper"></i></span>
                        <span class="pc-mtext">News</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('mk.news.word-cloud') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.news.word-cloud') }}{{ $qs }}">Word Cloud</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.news.top-publishers') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.news.top-publishers') }}{{ $qs }}">Top Publishers</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.news.timeline') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.news.timeline') }}{{ $qs }}">Mention</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.news.topic-map') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.news.topic-map') }}{{ $qs }}">Topic Map</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.news.ai-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.news.ai-analysis') }}{{ $qs }}">AI Analysis</a>
                        </li>
                    </ul>
                </li>

                {{-- ── SOCIAL MEDIA ── --}}
                <li class="pc-item pc-caption">
                    <label>Social Media</label>
                </li>

                {{-- X (Twitter) --}}
                <li class="pc-item pc-hasmenu {{ $isXActive ? 'pc-trigger active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </span>
                        <span class="pc-mtext">X (Twitter)</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('mk.x.overview') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.x.overview') }}{{ $qs }}">Overview</a>
                        </li>
                        {{-- Top Hashtags, Author Profiles, Location Map, Posts with Location, Shared URLs — now in Overview --}}
                        {{-- Most Viewed Posts hidden — covered by Most Engagement --}}
                        <li class="pc-item {{ request()->routeIs('mk.x.most-retweets') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.x.most-retweets') }}{{ $qs }}">Most Retweets</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.x.most-engagement') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.x.most-engagement') }}{{ $qs }}">Most Engagement</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.x.trending-word-cloud') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.x.trending-word-cloud') }}{{ $qs }}">Word Cloud</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.x.most-active-users') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.x.most-active-users') }}{{ $qs }}">Most Active Users</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.x.top-influencers') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.x.top-influencers') }}{{ $qs }}">Top Influencers</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.x.emotion-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.x.emotion-analysis') }}{{ $qs }}">Emotion Analysis</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.x.ai-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.x.ai-analysis') }}{{ $qs }}">AI Analysis</a>
                        </li>
                    </ul>
                </li>

                {{-- Facebook --}}
                <li class="pc-item pc-hasmenu {{ $isFacebookActive ? 'pc-trigger active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-facebook-logo"></i></span>
                        <span class="pc-mtext">Facebook</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('mk.facebook.overview') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.facebook.overview') }}{{ $qs }}">Overview</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.facebook.trending-topics') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.facebook.trending-topics') }}{{ $qs }}">Top Hashtags</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.facebook.trending-word-cloud') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.facebook.trending-word-cloud') }}{{ $qs }}">Word Cloud</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.facebook.most-engagement') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.facebook.most-engagement') }}{{ $qs }}">Most Engagement</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.facebook.emotion-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.facebook.emotion-analysis') }}{{ $qs }}">Emotion Analysis</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.facebook.ai-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.facebook.ai-analysis') }}{{ $qs }}">AI Analysis</a>
                        </li>
                    </ul>
                </li>

                {{-- Instagram --}}
                <li class="pc-item pc-hasmenu {{ $isInstagramActive ? 'pc-trigger active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-instagram-logo"></i></span>
                        <span class="pc-mtext">Instagram</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('mk.instagram.overview') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.instagram.overview') }}{{ $qs }}">Overview</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.instagram.trending-topics') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.instagram.trending-topics') }}{{ $qs }}">Top Hashtags</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.instagram.trending-word-cloud') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.instagram.trending-word-cloud') }}{{ $qs }}">Word Cloud</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.instagram.most-engagement') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.instagram.most-engagement') }}{{ $qs }}">Most Engagement</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.instagram.emotion-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.instagram.emotion-analysis') }}{{ $qs }}">Emotion Analysis</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.instagram.ai-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.instagram.ai-analysis') }}{{ $qs }}">AI Analysis</a>
                        </li>
                    </ul>
                </li>

                {{-- YouTube --}}
                <li class="pc-item pc-hasmenu {{ $isYoutubeActive ? 'pc-trigger active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-youtube-logo"></i></span>
                        <span class="pc-mtext">YouTube</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('mk.youtube.overview') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.youtube.overview') }}{{ $qs }}">Overview</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.youtube.trending-topics') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.youtube.trending-topics') }}{{ $qs }}">Top Hashtags</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.youtube.trending-word-cloud') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.youtube.trending-word-cloud') }}{{ $qs }}">Word Cloud</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.youtube.most-engagement') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.youtube.most-engagement') }}{{ $qs }}">Most Engagement</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.youtube.emotion-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.youtube.emotion-analysis') }}{{ $qs }}">Emotion Analysis</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.youtube.ai-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.youtube.ai-analysis') }}{{ $qs }}">AI Analysis</a>
                        </li>
                    </ul>
                </li>

                {{-- TikTok --}}
                <li class="pc-item pc-hasmenu {{ $isTiktokActive ? 'pc-trigger active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ph ph-tiktok-logo"></i></span>
                        <span class="pc-mtext">TikTok</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('mk.tiktok.overview') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.tiktok.overview') }}{{ $qs }}">Overview</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.tiktok.trending-topics') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.tiktok.trending-topics') }}{{ $qs }}">Top Hashtags</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.tiktok.trending-word-cloud') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.tiktok.trending-word-cloud') }}{{ $qs }}">Word Cloud</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.tiktok.most-engagement') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.tiktok.most-engagement') }}{{ $qs }}">Most Engagement</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.tiktok.emotion-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.tiktok.emotion-analysis') }}{{ $qs }}">Emotion Analysis</a>
                        </li>
                        <li class="pc-item {{ request()->routeIs('mk.tiktok.ai-analysis') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('mk.tiktok.ai-analysis') }}{{ $qs }}">AI Analysis</a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->

<script>
function changeProject(projectId, projectName) {
    localStorage.setItem('selected_project_id', projectId);
    const url = new URL(window.location.href);
    url.searchParams.set('project_id', projectId);
    if (!url.searchParams.get('start_date')) {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        url.searchParams.set('start_date', `${y}-${m}-01`);
    }
    if (!url.searchParams.get('end_date')) {
        const now = new Date();
        const y = now.getFullYear();
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const d = String(now.getDate()).padStart(2, '0');
        url.searchParams.set('end_date', `${y}-${m}-${d}`);
    }
    window.location.href = url.toString();
}
</script>