<!doctype html>
<html lang="id">

<head>
    <title>@yield('title', 'SMADIMENT - Analytics Platform')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="icon" href="{{ asset('images/SMADIMENT 2025 _ Logo-03.png') }}" type="image/png" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css" />
    <link rel="stylesheet" href="/assets/css/style.css" id="main-style-link" />
    <link rel="stylesheet" href="/assets/css/style-preset.css" />
    <link rel="stylesheet" href="/assets/css/custom-theme.css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
 
    <style>
        #gdpTriggerLabel { 
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
            max-width: 150px; display: inline-block; vertical-align: middle; font-size: 12px;
        } 
        /* Date picker modal - smooth fade + slide */
        .gdp-modal {
            display: flex !important;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.22s ease;
        }
        .gdp-modal.show {
            opacity: 1;
            pointer-events: auto;
        }
        .gdp-modal .gdp-container {
            transform: translateY(16px) scale(0.97);
            transition: transform 0.22s cubic-bezier(0.34, 1.2, 0.64, 1);
        }
        .gdp-modal.show .gdp-container {
            transform: translateY(0) scale(1);
        }

        /* Breadcrumb - lebih tebal dan keliatan */
        #autoBreadcrumb .breadcrumb-item a {
            font-weight: 600;
            font-size: 13.5px;
            color: #475569;
            text-decoration: none;
            cursor: pointer;
        }
        #autoBreadcrumb .breadcrumb-item a:hover { color: #038047; }
        #autoBreadcrumb .breadcrumb-item.active {
            font-weight: 700;
            font-size: 13.5px;
            color: #1e293b;
        }
        #autoBreadcrumb .breadcrumb-item + .breadcrumb-item::before {
            font-weight: 600;
            color: #94a3b8;
        }

        /* Breadcrumb dropdown - smooth animation */
        .bc-dropdown-menu {
            position: absolute; top: calc(100% + 6px); left: 0;
            min-width: 190px; background: #fff;
            border: 1px solid #e2e8f0; border-radius: 12px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.12);
            z-index: 9999; padding: 6px;
            /* animasi */
            opacity: 0;
            transform: translateY(-6px) scale(0.97);
            pointer-events: none;
            transition: opacity 0.18s ease, transform 0.18s ease;
        }
        .bc-dropdown-menu.show {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        .bc-dropdown-menu a, .bc-dropdown-menu span.bc-dd-item {
            display: block; padding: 7px 12px; border-radius: 7px;
            font-size: 13px; color: #1e293b; text-decoration: none; cursor: pointer;
            transition: background 0.15s;
        }
        .bc-dropdown-menu a:hover, .bc-dropdown-menu span.bc-dd-item:hover { background: #f1f5f9; color: #038047; }
        .bc-dropdown-menu .bc-dd-active { background: #038047 !important; color: #fff !important; border-radius: 7px; }
        .bc-dropdown-menu .bc-dd-label {
            font-size: 10px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 6px 12px 3px; display: block;
        }
        .breadcrumb-item.bc-has-dropdown { position: relative; }
        .breadcrumb-item.bc-has-dropdown > a,
        .breadcrumb-item.bc-has-dropdown > span {
            cursor: pointer;
        }
    </style>

    @yield('styles')
</head>

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">

    <div class="loader-bg"><div class="loader-track"><div class="loader-fill"></div></div></div>

    @include('mk.layouts.partials.sidebar')
    @include('mk.layouts.partials.header')

    <div class="pc-container">
        <div class="pc-content">

            <div class="page-header" style="margin-bottom: 24px;">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb" id="autoBreadcrumb"></ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0" id="autoPageTitle">@yield('page-title', '')</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @yield('content')

        </div>
    </div>

    @include('mk.layouts.partials.footer')

    {{-- -
    @include('mk.layouts.partials.customizer')
 --}}
    <script src="/assets/js/plugins/popper.min.js"></script>
    <script src="/assets/js/plugins/simplebar.min.js"></script>
    <script src="/assets/js/plugins/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>
    <script src="/assets/js/theme.js"></script>

    <script>
        layout_change('light');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');
        layout_theme_sidebar_change('false');
    </script>

    {{-- Re-apply sidebar active state after script.js wipes pc-trigger --}}
    <script>
    (function(){
        var activeItem = document.querySelector('.pc-navbar > li.pc-hasmenu > .pc-submenu .pc-item.active');
        if (activeItem) {
            var parent = activeItem.closest('li.pc-hasmenu');
            if (parent) {
                parent.classList.add('pc-trigger');
                parent.classList.add('active');
                var sub = parent.querySelector('.pc-submenu');
                if (sub) sub.style.display = 'block';
            }
        }
    })();
    </script>

    <script>
    (function () {
        // ── ROUTE MAP: { match, group, section, page }
        // group   = label sidebar section (Project / Main / News / Social Media)
        // section = sub-group jika ada (Statistic / X (Twitter) / dll), null jika tidak ada
        // page    = nama halaman aktif
        const ROUTE_MAP = [
            // Main
            { match: /\/dashboard$/,              group: 'Main', section: null,           page: 'Dashboard' },
            { match: /\/data-overview$/,           group: 'Main', section: null,           page: 'Data Overview' },
            { match: /\/media-statistic$/,         group: 'Main', section: 'Statistic',    page: 'Media' },
            { match: /\/sentiment$/,               group: 'Main', section: 'Statistic',    page: 'Sentiment' },
            { match: /\/net-sentiment-score$/,     group: 'Main', section: 'Statistic',    page: 'Net Sentiment Score' },
            { match: /\/compare/,                  group: 'Main', section: null,           page: 'Compare Projects' },
            { match: /\/topic-map$/,               group: 'Main', section: null,           page: 'World Map' },
            { match: /\/x\/geographic$/,           group: 'Main', section: null,           page: 'Location Map' },
            { match: /\/trending-topic$/,          group: 'Project', section: 'Hot Topic', page: 'Trending Topics' },
            { match: /\/search-topic$/,            group: 'Project', section: 'Hot Topic', page: 'Search Topic' },
            { match: /\/x\/post-with-location$/,   group: 'Main', section: null,           page: 'Posts with Location' },
            // News
            { match: /\/news\/word-cloud$/,        group: 'News', section: null,           page: 'Word Cloud' },
            { match: /\/news\/top-publishers$/,    group: 'News', section: null,           page: 'Top Publishers' },
            { match: /\/news\/timeline$/,          group: 'News', section: null,           page: 'Mention' },
            { match: /\/news\/topic-map$/,         group: 'News', section: null,           page: 'Topic Map' },
            { match: /\/news\/ai-analysis$/,       group: 'News', section: null,           page: 'AI Analysis' },
            // Social Media → X
            { match: /\/x\/overview$/,             group: 'Social Media', section: 'X (Twitter)', page: 'Overview' },
            { match: /\/x\/trending-topics$/,      group: 'Social Media', section: 'X (Twitter)', page: 'Top Hashtags' },
            { match: /\/x\/most-status$/,          group: 'Social Media', section: 'X (Twitter)', page: 'Most Viewed Posts' },
            { match: /\/x\/most-retweets$/,        group: 'Social Media', section: 'X (Twitter)', page: 'Most Retweets' },
            { match: /\/x\/most-engagement$/,      group: 'Social Media', section: 'X (Twitter)', page: 'Most Engagement' },
            { match: /\/x\/authors/,               group: 'Social Media', section: 'X (Twitter)', page: 'Author Profiles' },
            { match: /\/x\/trending-word-cloud$/,  group: 'Social Media', section: 'X (Twitter)', page: 'Word Cloud' },
            { match: /\/x\/shared-urls$/,          group: 'Social Media', section: 'X (Twitter)', page: 'Shared URLs' },
            { match: /\/x\/most-active-users$/,    group: 'Social Media', section: 'X (Twitter)', page: 'Most Active Users' },
            { match: /\/x\/top-influencers$/,      group: 'Social Media', section: 'X (Twitter)', page: 'Top Influencers' },
            { match: /\/x\/emotion-analysis$/,     group: 'Social Media', section: 'X (Twitter)', page: 'Emotion Analysis' },
            { match: /\/x\/ai-analysis$/,          group: 'Social Media', section: 'X (Twitter)', page: 'AI Analysis' },
            // Facebook
            { match: /\/facebook\/overview$/,           group: 'Social Media', section: 'Facebook', page: 'Overview' },
            { match: /\/facebook\/trending-topics$/,    group: 'Social Media', section: 'Facebook', page: 'Top Hashtags' },
            { match: /\/facebook\/trending-word-cloud$/,group: 'Social Media', section: 'Facebook', page: 'Word Cloud' },
            { match: /\/facebook\/most-engagement$/,    group: 'Social Media', section: 'Facebook', page: 'Most Engagement' },
            { match: /\/facebook\/emotion-analysis$/,   group: 'Social Media', section: 'Facebook', page: 'Emotion Analysis' },
            { match: /\/facebook\/ai-analysis$/,        group: 'Social Media', section: 'Facebook', page: 'AI Analysis' },
            // Instagram
            { match: /\/instagram\/overview$/,           group: 'Social Media', section: 'Instagram', page: 'Overview' },
            { match: /\/instagram\/trending-topics$/,    group: 'Social Media', section: 'Instagram', page: 'Top Hashtags' },
            { match: /\/instagram\/trending-word-cloud$/,group: 'Social Media', section: 'Instagram', page: 'Word Cloud' },
            { match: /\/instagram\/most-engagement$/,    group: 'Social Media', section: 'Instagram', page: 'Most Engagement' },
            { match: /\/instagram\/emotion-analysis$/,   group: 'Social Media', section: 'Instagram', page: 'Emotion Analysis' },
            { match: /\/instagram\/ai-analysis$/,        group: 'Social Media', section: 'Instagram', page: 'AI Analysis' },
            // YouTube
            { match: /\/youtube\/overview$/,            group: 'Social Media', section: 'YouTube', page: 'Overview' },
            { match: /\/youtube\/trending-topics$/,     group: 'Social Media', section: 'YouTube', page: 'Top Hashtags' },
            { match: /\/youtube\/trending-word-cloud$/, group: 'Social Media', section: 'YouTube', page: 'Word Cloud' },
            { match: /\/youtube\/most-engagement$/,     group: 'Social Media', section: 'YouTube', page: 'Most Engagement' },
            { match: /\/youtube\/emotion-analysis$/,    group: 'Social Media', section: 'YouTube', page: 'Emotion Analysis' },
            { match: /\/youtube\/ai-analysis$/,         group: 'Social Media', section: 'YouTube', page: 'AI Analysis' },
            // TikTok
            { match: /\/tiktok\/overview$/,            group: 'Social Media', section: 'TikTok', page: 'Overview' },
            { match: /\/tiktok\/trending-topics$/,     group: 'Social Media', section: 'TikTok', page: 'Top Hashtags' },
            { match: /\/tiktok\/trending-word-cloud$/, group: 'Social Media', section: 'TikTok', page: 'Word Cloud' },
            { match: /\/tiktok\/most-engagement$/,     group: 'Social Media', section: 'TikTok', page: 'Most Engagement' },
            { match: /\/tiktok\/emotion-analysis$/,    group: 'Social Media', section: 'TikTok', page: 'Emotion Analysis' },
            { match: /\/tiktok\/ai-analysis$/,         group: 'Social Media', section: 'TikTok', page: 'AI Analysis' },
        ];

        // ── Sub-pages per group/section untuk dropdown
        const GROUP_PAGES = {
            'Main': [
                { label: 'Dashboard',        path: '/mk/dashboard' },
                { label: 'Data Overview',    path: '/mk/data-overview' },
                { label: 'Statistic',        path: '/mk/media-statistic' },
                { label: 'Compare Projects', path: '/mk/compare-projects' },
                { label: 'World Map',        path: '/mk/topic-map' },
                { label: 'Location Map',     path: '/mk/x/geographic' },
                { label: 'Posts with Location', path: '/mk/x/post-with-location' },
            ],
            'Project': [
                { label: 'Trending Topics', path: '/mk/trending-topic' },
                { label: 'Search Topic',    path: '/mk/search-topic' },
            ],
            'News': [
                { label: 'Word Cloud',     path: '/mk/news/word-cloud' },
                { label: 'Top Publishers', path: '/mk/news/top-publishers' },
                { label: 'Mention',        path: '/mk/news/timeline' },
                { label: 'Topic Map',      path: '/mk/news/topic-map' },
                { label: 'AI Analysis',    path: '/mk/news/ai-analysis' },
            ],
            'Social Media': [
                { label: 'X (Twitter)', path: '/mk/x/overview' },
                { label: 'Facebook',    path: '/mk/facebook/overview' },
                { label: 'Instagram',   path: '/mk/instagram/overview' },
                { label: 'YouTube',     path: '/mk/youtube/overview' },
                { label: 'TikTok',      path: '/mk/tiktok/overview' },
            ],
        };

        const SECTION_PAGES = {
            'Statistic': [
                { label: 'Media',              path: '/mk/media-statistic' },
                { label: 'Sentiment',          path: '/mk/sentiment' },
                { label: 'Net Sentiment Score',path: '/mk/net-sentiment-score' },
            ],
            'X (Twitter)': [
                { label: 'Overview',          path: '/mk/x/overview' },
                { label: 'Top Hashtags',      path: '/mk/x/trending-topics' },
                { label: 'Most Viewed Posts', path: '/mk/x/most-status' },
                { label: 'Most Retweets',     path: '/mk/x/most-retweets' },
                { label: 'Most Engagement',   path: '/mk/x/most-engagement' },
                { label: 'Author Profiles',   path: '/mk/x/authors/demographics' },
                { label: 'Word Cloud',        path: '/mk/x/trending-word-cloud' },
                { label: 'Shared URLs',       path: '/mk/x/shared-urls' },
                { label: 'Most Active Users', path: '/mk/x/most-active-users' },
                { label: 'Top Influencers',   path: '/mk/x/top-influencers' },
                { label: 'Emotion Analysis',  path: '/mk/x/emotion-analysis' },
                { label: 'AI Analysis',       path: '/mk/x/ai-analysis' },
            ],
            'Facebook': [
                { label: 'Overview',         path: '/mk/facebook/overview' },
                { label: 'Top Hashtags',     path: '/mk/facebook/trending-topics' },
                { label: 'Word Cloud',       path: '/mk/facebook/trending-word-cloud' },
                { label: 'Most Engagement',  path: '/mk/facebook/most-engagement' },
                { label: 'Emotion Analysis', path: '/mk/facebook/emotion-analysis' },
                { label: 'AI Analysis',      path: '/mk/facebook/ai-analysis' },
            ],
            'Instagram': [
                { label: 'Overview',         path: '/mk/instagram/overview' },
                { label: 'Top Hashtags',     path: '/mk/instagram/trending-topics' },
                { label: 'Word Cloud',       path: '/mk/instagram/trending-word-cloud' },
                { label: 'Most Engagement',  path: '/mk/instagram/most-engagement' },
                { label: 'Emotion Analysis', path: '/mk/instagram/emotion-analysis' },
                { label: 'AI Analysis',      path: '/mk/instagram/ai-analysis' },
            ],
            'YouTube': [
                { label: 'Overview',         path: '/mk/youtube/overview' },
                { label: 'Top Hashtags',     path: '/mk/youtube/trending-topics' },
                { label: 'Word Cloud',       path: '/mk/youtube/trending-word-cloud' },
                { label: 'Most Engagement',  path: '/mk/youtube/most-engagement' },
                { label: 'Emotion Analysis', path: '/mk/youtube/emotion-analysis' },
                { label: 'AI Analysis',      path: '/mk/youtube/ai-analysis' },
            ],
            'TikTok': [
                { label: 'Overview',         path: '/mk/tiktok/overview' },
                { label: 'Top Hashtags',     path: '/mk/tiktok/trending-topics' },
                { label: 'Word Cloud',       path: '/mk/tiktok/trending-word-cloud' },
                { label: 'Most Engagement',  path: '/mk/tiktok/most-engagement' },
                { label: 'Emotion Analysis', path: '/mk/tiktok/emotion-analysis' },
                { label: 'AI Analysis',      path: '/mk/tiktok/ai-analysis' },
            ],
        };

        const path   = window.location.pathname;
        const params = new URLSearchParams(window.location.search);
        const qs     = params.toString() ? '?' + params.toString() : '';
        const route  = ROUTE_MAP.find(r => r.match.test(path));
        if (!route) return;

        function makeDropdown(id, items, currentPath) {
            let html = `<div class="bc-dropdown-menu" id="${id}">`;
            items.forEach(item => {
                const isActive = currentPath.startsWith(item.path);
                html += `<a href="${item.path}${qs}" class="${isActive ? 'bc-dd-active' : ''}">${item.label}</a>`;
            });
            html += `</div>`;
            return html;
        }

        function toggleDropdown(btnId, menuId) {
            // close others
            document.querySelectorAll('.bc-dropdown-menu.show').forEach(m => {
                if (m.id !== menuId) m.classList.remove('show');
            });
            const menu = document.getElementById(menuId);
            if (menu) menu.classList.toggle('show');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.bc-has-dropdown')) {
                document.querySelectorAll('.bc-dropdown-menu.show').forEach(m => m.classList.remove('show'));
            }
        });

        // ── Build breadcrumb HTML
        const bc = document.getElementById('autoBreadcrumb');
        if (!bc) return;

        // Get project name & items dari sidebar (lebih reliable pakai data-* atau text langsung)
        const projectTrigger = document.querySelector('.pc-navbar .pc-hasmenu');
        const projectName = projectTrigger
            ? projectTrigger.querySelector('.pc-mtext').textContent.trim()
            : 'Project';

        // Kumpulkan project list dari submenu sidebar
        const projectItems = [];
        if (projectTrigger) {
            projectTrigger.querySelectorAll('.pc-submenu .pc-item .pc-link').forEach(a => {
                projectItems.push({
                    label: a.textContent.trim(),
                    onclick: a.getAttribute('onclick') || ''
                });
            });
        }

        let projectDropdownHtml = `<div class="bc-dropdown-menu" id="bcDdProject">`;
        if (projectItems.length) {
            projectItems.forEach(item => {
                const isActive = item.label === projectName;
                projectDropdownHtml += `<span class="bc-dd-item ${isActive ? 'bc-dd-active' : ''}" onclick="${item.onclick}; document.querySelectorAll('.bc-dropdown-menu').forEach(m=>m.classList.remove('show'))">${item.label}</span>`;
            });
        } else {
            projectDropdownHtml += `<span style="padding:8px 12px;color:#94a3b8;font-size:12px;display:block">No projects</span>`;
        }
        projectDropdownHtml += `</div>`;

        // Segment: Monitoring (static, link ke dashboard)
        const monitoringHtml = `<li class="breadcrumb-item"><a href="/mk/dashboard${qs}">Monitoring</a></li>`;

        // Segment: Group (Main / News / Social Media) dengan dropdown
        const groupPages = GROUP_PAGES[route.group] || [];
        const groupDropdown = makeDropdown('bcDdGroup', groupPages, path);

        // Segment: Section (Statistic / X (Twitter) / dll) — hanya jika ada
        let sectionHtml = '';
        if (route.section) {
            const sectionPages = SECTION_PAGES[route.section] || [];
            const sectionDropdown = makeDropdown('bcDdSection', sectionPages, path);
            sectionHtml = `
                <li class="breadcrumb-item bc-has-dropdown">
                    <a onclick="toggleDropdown('bcBtnSection','bcDdSection')">${route.section}</a>
                    ${sectionDropdown}
                </li>`;
        }

        bc.innerHTML = `
            <li class="breadcrumb-item bc-has-dropdown">
                <a onclick="toggleDropdown('bcBtnProject','bcDdProject')">${projectName}</a>
                ${projectDropdownHtml}
            </li>
            ${monitoringHtml}
            <li class="breadcrumb-item bc-has-dropdown">
                <a onclick="toggleDropdown('bcBtnGroup','bcDdGroup')">${route.group}</a>
                ${groupDropdown}
            </li>
            ${sectionHtml}
            <li class="breadcrumb-item active">${route.page}</li>
        `;

        // Set page title
        const titleEl = document.getElementById('autoPageTitle');
        if (titleEl && !titleEl.textContent.trim()) {
            titleEl.textContent = route.page;
        }

        // expose toggle globally
        window.toggleDropdown = toggleDropdown;
    })();
    </script>

    @stack('scripts')
    @yield('scripts')

</body>
</html>